<?php
require_once("ogg.class.php");
$video=new Ogg ("../video/blender/01.ogv");
if ($video->LastError) { echo $video->LastError; exit; }
$str = substr(strstr($video->Streams['summary'],"(theora): "),10);
print_r(substr($str,0,strpos($str,"s")));
?>