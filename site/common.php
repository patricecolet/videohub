<?php
// ----------------------------------------------------------------------------
/*
	PROJECT			:	video upload
	FILE			:	common.php
	GOAL			:	Contains the code that is reused by other scripts
	AUTHOR			:	alien_genius & patco
	COPYRIGHT		:	Copyright 2020 (c) Alien Genius Software Solutions & Nykto
*/
// ----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
function GetScriptParameter($name,$defvalue)
{
$res	=	$defvalue;
if (isset($_POST[$name]))
	$res	=	$_POST[$name];
else if (isset($_GET[$name]))
	$res	=	$_GET[$name];
return $res;
}

// -----------------------------------------------------------------------------
function MakeHeader()
{
$s	=	"<!DOCTYPE html> \n";
$s	.=	"<html lang=\"fr\">\n";
$s	.=	"<head>\n";
$s	.=	"<title>" . SCRIPT_TITLE . "</title>\n";
$s	.=	"<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" />\n";
$s  .=  "<script src=\"jquery-3.4.1.min.js\"></script>\n";
$s  .=  "<script src=\"script.js\"></script>\n";
$s	.=	"</head>\n";
$s	.=	"<body>\n";
/* $s	.=	"<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">\n";
$s	.=	"<tr>\n";
$s	.=	"\t<td class=\"subtitle\" colspan=\"3\">Admin console</td>\n";
$s	.=	"</tr>\n";
$s	.=	"</table>\n"; */
return $s;
}
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
function MakeFooter()
{
$s	=	"</body>\n";
$s	.=	"</html>\n";
return $s;
}
// -----------------------------------------------------------------------------

// ----------------------------------------------------------------------------
function MakeErrorMessage($err)
{
$res 	=	"<p class=\"error\">" . $err . "</p>\n";
$res	.=	"<p><a href=\"javascript:history.go(-1);\">Go back</a></p><br />";
return $res;
}
// ----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
function ProcessUploadedFile($uploadvarname,$targdir)
{
$res	=	false;
// First check if there is an incoming file
if (is_uploaded_file($_FILES[$uploadvarname]["tmp_name"]))
	{
	// Yes there seems to be an incoming file. Process the upload.
	$clientname		=	$_FILES[$uploadvarname]["name"];
	$targdir			=	(substr($targdir,strlen($targdir)-1,1)	!=	"/")	?	$targdir	:	substr($targdir,0,strlen($targdir)-1);
	$targfile		=	$targdir . "/" . basename($_FILES[$uploadvarname]["name"]);
	move_uploaded_file($_FILES[$uploadvarname]["tmp_name"],$targfile);
	$res	=	$targfile;
	}
return	$res;
}
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------


function writeJSON($arr)
{
	$fp = fopen("status.json", "w");
	fwrite($fp, json_encode($arr));
	fclose($fp);
}
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
function getHttpFileSize($url)
{
    $curl = curl_init($url);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($curl, CURLOPT_HEADER, true); 
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_exec($curl);	
	$result = 	curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);	
	curl_close($curl);
    return $result;
}
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
function GetFolderList($startfolder,$err)
{
$result	=	array();
$err	=	"";
if (!isset($startfolder)	||	$startfolder	==	"")
	{
	$err	=	"Pas de dossier selectionné.";
	}
else
	{
	$ptr	=	opendir($startfolder);
	if (!$ptr)
		{
		$err	=	"Impossible d'ouvrir le dossier " . $startfolder;
		}
	else
		{
		while ($entry	=	readdir($ptr))
			{
			if ($entry	!=	"."	&&	$entry	!=	".."	&&	is_dir($startfolder . $entry))
				{
				$result[]	=	"zob";
					echo $result;
				}
			}
		closedir($ptr);
		}
	}
//return $res;
}
// -----------------------------------------------------------------------------
function GetFilesList($startfolder, $err)
{
global $scriptfolder; global $soundextensions;

$res	=	array();
$err	=	"";
if (!isset($startfolder)	||	$startfolder	==	"")
	{
	$err	=	"Pas de dossier selectionné.";
	}
else
	{
	$ptr	=	opendir($startfolder);
	if (!$ptr)
		{
		$err	=	"Impossible d'ouvrir le dossier " . $startfolder;
		}
	else
		{
		while ($entry	=	readdir($ptr))
			{
			if ($entry	!=	"."	&&	$entry	!=	".."	&&	is_file($startfolder . "/" . $entry))
			{
				// get extension
				$soundextensions	=	array("mp3","wav");
				$p	=	strrchr($entry,".");
				$ext		=	substr($p,1);
				if (in_array($ext,$soundextensions))
					{
					$res[]	=	$entry;
					}
			}
		}
		closedir($ptr);
	}
}
return $res;
}
//-----------------------------------------------------------------------------
// MAIN ROUTINE
// NO OUTPUT ON PURPOSE
?>