<?php
/*
Plugin Name: ReaSEO
Plugin URI: http://www.daruisoft.com
Description: 自动化的SEO信息添加工具，减轻您SEO优化的压力。
Version: 1.0
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
    $seo = main($title, $content, 'http://smtseo.sinaapp.com/1/segment.php?title=');
	echo $seo;
}

/**
 * 主函数
 * @param string $title 文章标题
 * @param string $content 文章正文
 * @param string $api 分词API地址
 * @return string $meta META标签
 */
function main($title, $content, $api) {
    $segment = unserialize(file_get_contents($api.$title));
    $meta = '<!--ReaSEO START-->'.Build_KeyWords_Meta($segment).Build_Description_Meta($title, $content).'<!--ReaSEO End-->';
    return $meta;
}

/**
 * 制造KeyWords头
 * @param array $segment 分词结果数组
 * @return string $standard_meta_keywords 组装好的KeyWords头
 */
function Build_KeyWords_Meta($segment) {
	$title_state = '95,96,99,102,130,131,170,171,180';
	$keywords = '';
	
	foreach ($segment as $sub_seg) {
	    
		$type = $sub_seg['word_tag'];
		$pos = strpos($title_state, $type);
		
		if (!($pos===false)) {
		    $isRepetition = strpos($keywords, $sub_seg['word']);
		    
		    if ($isRepetition===false) {
		        $keywords = $keywords.$sub_seg['word'].' ';
		    }
		    
		}
	}
	
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
?>
