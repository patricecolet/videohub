<html><body>
<center><h1>Ogg.class.php v1.3f DEMO</h1>
Get more information at <a href="http://opensource.grisambre.net/ogg">http://opensource.grisambre.net/ogg</a></center><br><br>
<?php // demonstrator for the oggtag class from the ogg.php librairie. (c) Nicolas Ricquemaque 2008, GPL3
$password="a";  // Please enter a password here to protect your own files !

if (file_exists("ogg.class.php")) require_once("ogg.class.php");
elseif (file_exists("lib/ogg.php")) require_once("lib/ogg.class.php");
else { echo "<br><br> Missing the ogg.class.php library !"; exit; }

function get($val)
	{
	if (isset( $_POST[$val] )) return ($_POST[$val]);
	else return(false);
	}
	
$action = get('a'); 
$file = get('file');
$pass = get('pass'); 

if (strlen($password)==0) { echo "<br><br> Please modify the \$password variable inside your ".$_SERVER['PHP_SELF']." before using this demo !"; exit; }

if (!$action) // first call to choose the file
	{
	echo "<br><form action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
	echo "File to tag:&nbsp;<input type='text' value='x.ogg' name='file' size='20' onclick='this.value=\"\"'><br>\n";
	echo "Password:&nbsp;<input type='password' value='' name='pass' size='20'>\n";
	echo "&nbsp;&nbsp;&nbsp;<input type='submit' name='a' value='Modify Tags'>&nbsp;&nbsp;&nbsp;<input type='submit' name='a' value='Get information'>\n";
	echo "</form>\n";
	exit;
	}
	
if ($action=='Get information') // load file
	{
	if ($pass!=$password) { echo "<br><br> Bad password !"; exit; }
	echo "<form action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
	echo "<input type='hidden' name='file' value='$file'><input type='hidden' name='pass' value='$pass'>\n";
	echo "<input type='submit' name='a' value='Modify Tags'>\n";
	echo "</form>\n";		
	echo "<form action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
	echo "<input type='submit' value='Another file ?'>\n";
	echo "</form>";
	echo "<pre>";
	$info=new Ogg($file);
	if ($info->LastError) { echo $info->LastError; exit; }	
	if ($info->Streams['picturable'])
		{
		if (!isset($info->Streams['theora']['framecount'])) $frame=1;
		else $frame=intval($info->Streams['theora']['framecount']/2);
		$img=$info->GetPicture($frame);
		if ($info->LastError) echo $info->LastError; 
		else echo "Extracted picture : <img src='$img'><br><br>";
		}
	echo "Extracted information :<br>".htmlentities(print_r($info->Streams,true)); 
	echo "</pre>";
	exit;
	}

if ($action=='Modify Tags') // load file
	{
	if ($pass!=$password) { echo "<br><br> Bad password !"; exit; }
	$tag=new Ogg($file,UPDATECACHE);
	if ($tag->LastError) { echo $tag->LastError; exit; }
	echo "<br> File: ".nl2br(htmlentities($tag->Streams['summary']))."<br><br>\n";
	echo "<form action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
	if (isset($tag->Streams['theora'])) 
		{ 
		echo "<b>VIDEO (Theora) :</b> <i>".htmlentities($tag->Streams['theora']['vendor'],ENT_QUOTES)."</i><br>\n";
		echo "<textarea rows='10' cols='80' name='theora'>";
		if (isset($tag->Streams['theora']['comments'])) foreach ($tag->Streams['theora']['comments'] as $comment) echo "$comment\n";
		echo "</textarea><br><br>\n";
		}
	if (isset($tag->Streams['vorbis'])) 
		{ 
		echo "<b>AUDIO (Vorbis) :</b> <i>".htmlentities($tag->Streams['vorbis']['vendor'],ENT_QUOTES)."</i><br>\n";
		echo "<textarea rows='10' cols='80' name='vorbis'>";
		if (isset($tag->Streams['vorbis']['comments'])) foreach ($tag->Streams['vorbis']['comments'] as $comment) echo "$comment\n";
		echo "</textarea><br><br>\n";
		}	
	echo "<input type='submit' value='Modify'>\n";
	echo "&nbsp;&nbsp;&nbsp;(A list of <a href='http://xiph.org/vorbis/doc/v-comment.html'>recommanded field names can be found here</a>.)<br>\n";
	echo "<input type='hidden' name='a' value='p'>\n";
	echo "<input type='hidden' name='file' value='$file'>\n";
	echo "<input type='hidden' name='pass' value='$pass'>\n";
	echo "</form>\n";
	}

$done=false;

if ($action=='p') // process file
	{
	if ($pass!=$password) { echo "<br><br> Bad password !"; exit; }
	$tag=new Ogg($file,UPDATECACHE); 
	if ($tag->LastError) { echo $tag->LastError; exit; }
	if ($v=get('vorbis')) $tag->Streams['vorbis']['comments']=explode("\n",rtrim(str_replace("\r","",$v),"\n"));
	if ($t=get('theora')) $tag->Streams['theora']['comments']=explode("\n",rtrim(str_replace("\r","",$t),"\n")); 
	echo "<br>Processing file: <b>$file</b>... <span id='status'> Writting headers...</span><br><br>\n";
	@ob_flush(); @flush();
	$write=$tag->WriteNewComments(); //refresh every 5s by default
	if ($tag->LastError) { echo $tag->LastError; exit; }
	if ($write==$tag->Streams['size']) $done=true; 
	else { //hiddhen form to post data to a new php session to continue writting
		echo "<form name='phptag' action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
		echo "<input type='hidden' name='a' value='c'><input type='hidden' name='file' value='$file'><input type='hidden' name='pass' value='$pass'>\n";
		echo "</form>\n";
		echo "<script language='javascript'>document.phptag.submit();</script>\n";
		}
	}
	
if ($action=='c') // continue processing file
	{
	if ($pass!=$password) { echo "<br><br> Bad password !"; exit; }
	$tag=new Ogg($file);
	if ($tag->LastError) { echo $tag->LastError; exit; }
	if (!isset($tag->Streams['tmpfileptr'])) { echo "Error: No file to continue writting ?"; exit;}
	echo "<br>Processing file: <b>$file</b>... <span id='status'>".round($tag->Streams['tmpfileptr']/$tag->Streams['size']*100)."% done</span><br><br>\n";
	@ob_flush(); @flush();
	$write=$tag->ContinueWrite();	
	if ($tag->LastError) { echo $tag->LastError; exit; }
	if ($write==$tag->Streams['size']) $done=true; 
	else { //hiddhen form to post data to a new php session to continue writting
		echo "<form name='phptag' action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
		echo "<input type='hidden' name='a' value='c'><input type='hidden' name='file' value='$file'><input type='hidden' name='pass' value='$pass'>\n";
		echo "</form>\n";
		echo "<script language='javascript'>document.phptag.submit();</script>\n";
		}
	}

if ($done) //file has been completed
	{
	echo "<script language='javascript'>document.all.status.innerHTML='Completed !';</script>\n";
	echo "<br><form action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
	echo "<input type='hidden' name='a' value='Modify Tags'><input type='hidden' name='file' value='$file'><input type='hidden' name='pass' value='$pass'>\n";
	echo "<input type='submit' value='Same file again ?'>\n";
	echo "</form>\n";	
	echo "<form action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
	echo "<input type='hidden' name='file' value='$file'><input type='hidden' name='pass' value='$pass'>\n";
	echo "<input type='submit' name='a' value='Get information'>\n";
	echo "</form>\n";	
	echo "<form action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
	echo "<input type='submit' value='Another one ?'>\n";
	echo "</form><br><br>\n";	
	}
	
?>
</body></html>