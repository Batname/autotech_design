<?php
header('Content-type: text/plain; charset=CP1251');
//
//$text = 'Àðòèêóë';
//$text = iconv("utf-8", "windows-1252", $text);
//echo $text;

$str = "бат";
echo  mb_convert_encoding($str, "CP1251", "UTF-8");

?>