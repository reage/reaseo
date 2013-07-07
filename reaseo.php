<?php
/*
Plugin Name: ReaSEO
Plugin URI: http://www.daruisoft.com
Description: 自动化的SEO信息添加工具，减轻您SEO优化的压力。
Version: 1.1
Author: Reage
Author URI: http://www.reastudio.com
*/

add_action('wp_head', 'auto_seo', 1);

/**
 * 入口函数
 */
function auto_seo() {
	$title = get_the_title();
	$content = Get_Article();
    $seo = main($title, $content);
	echo $seo;
}

/**
 * 主函数
 * @param string $title 文章标题
 * @param string $content 文章正文
 * @param string $api 分词API地址
 * @return string $meta META标签
 */
function main($title, $content) {
    $meta = '<!--ReaSEO START-->'.Build_KeyWords_Meta($title, $content).Build_Description_Meta($title, $content).'<!--ReaSEO End-->';
    return $meta;
}

/**
 * 制造KeyWords头
 * @param array $segment 分词结果数组
 * @return string $standard_meta_keywords 组装好的KeyWords头
 */
function Build_KeyWords_Meta($seg_content, $art_content) {
    //echo $seg_content;
    $keywords = trim(segment($seg_content, $art_content));
	$standard_meta_keywords = '<meta name="Keywords" content="%keywords%" />';
    $standard_meta_keywords = str_replace('%keywords%', $keywords, $standard_meta_keywords);
    
    return $standard_meta_keywords;

}
    
/**
 * 制造Description头
 * @param string $title 标题
 * @param string $content 正文
 * @return string $standard_meta_description 组装好的Description头
 */
function Build_Description_Meta($title, $content) {
	$standard_meta_description = '<meta name="Description" content="%description%" />';
	$first_paragraph = '';
	$tmp = preg_match('/<p>(.*)<\/p>/', $content, $matches);
	
	if (isset($matches[1])) {
		$first_paragraph = $matches[1];
		$len = mb_strlen($first_paragraph,'utf8');
		
		if ($len <= 150) {
			$description = $first_paragraph;
		}else{
			$description = $title;
		}
		
	}else{
		echo $content;
		$description = $title;
	}
	
	$standard_meta_description = str_replace('%description%', $description, $standard_meta_description);
	return $standard_meta_description;
}

/**
 * 获取文章
 * @return string $content
 * 这个函数来自wordpress的the_content()函数，我只是改echo为return
 */
function Get_Article() {
    $content = get_the_content($more_link_text, $stripteaser);
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    return $content;
}

/**
 * 对照分词
 * @return string $keywords
 */
function segment($seg_content, $art_content) {
    $keywords = '';
    $i=1;
    $legal = array('，','。','、','？','》','《','；','：','“','”','‘',
        '’','、','|','}','{','【','】','——','）','（','%','·','~',
        '`','!','@','#','$','%','^','&','*','(',')','-','_',
        '+','=','[',']',';',':','?','/','>','<',',','.');
    
    foreach ($legal as $value) {
        $seg_content = str_replace($value, '', $seg_content);
    }

    $length = 0;
    $entry = mb_substr($seg_content, 0, 1, 'utf8');
    
    while (strlen($seg_content)) {
        if (!(strpos($art_content, $entry)===false)) {
            if (mb_strlen($entry,'utf8') == mb_strlen($seg_content, 'utf8')) {
                $keywords = $keywords.$entry.' ';
                break;
            }else{
                $word = $entry;
                $length++;
                $entry = mb_substr($seg_content, 0, $length, 'utf8');
            }
        }else{
            if (mb_strlen($entry, 'utf8') == 1) {
                $seg_content = str_replace($entry, '', $seg_content);
            }else{
					$keywords = $keywords.$word.' ';
					$seg_content = str_replace($word, '', $seg_content);
					if (mb_strlen($word,'utf8')==1) {
						$keywords = str_replace($word, '', $keywords);
					}
            }
            $length = 0;
            $entry = mb_substr($seg_content, 0, 1, 'utf8');
        }
    }
    return $keywords;
}
?>
