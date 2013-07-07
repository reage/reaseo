<?php
$directory = 'sohu';
set_time_limit(3600);
$files = scandir($directory);
foreach ($files as $val) {
//echo $directory.'/'.$val.'<br />';
    if (!($val=='.')&&!($val=='..')) {
        $htm = file_get_contents($directory.'/'.$val);
  	$bridge =  get_meta_tags($directory.'/'.$val);
        preg_match("/<title>(.*)<\/title>/", $htm, $matches);
        if (isset($matches[1])) {
            $title = $matches[1];
			$segment = unserialize(file_get_contents("http://smtseo.sinaapp.com/index.php?title=".$title));
            $keywords = $bridge["keywords"];
            $desc = $bridge["description"];
            $keywords_arr = explode(" ", $keywords);
            foreach ($keywords_arr as $value) {
                $pos = @strpos($title, $value);
                if (!($pos===false)) {
                    foreach ($segment as $sub_arr) {
                        if ($sub_arr['word'] == $value) {
                            $type = $sub_arr['word_tag'];
                            $title = str_replace($value, '<span style="color:#ff0000;">'.$value.'</span>(TYPE:'.$type.')', $title);
                        }
                    }
                }
            }
			//echo 'Title:'.$title.'<br />Description:'.$desc.'<br />';
            echo 'Title:'.$title.'<br />Keywords:'.$keywords.'<br />Description:'.$desc.'<br />';
        }
    }
}
?>
