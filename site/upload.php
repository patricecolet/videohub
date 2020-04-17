<?php

// -----------------------------------------------------------------------------
/*
	PROJECT		:	video upload
	FILE			:	upload.php
	GOAL			:	upload media on ftp server
	AUTHOR		:	 alien_genius, patko , ajj
	COPYRIGHT	:	Copyright 2020 (c) Alien Genius Software Solutions, Nykto, Cohmbox
*/
// -----------------------------------------------------------------------------


// ----------------------------------------------------------------------------
include_once("settings.php");
include_once("common.php");
require_once('getid3/getid3.php');
set_time_limit(0);

define("SCRIPT_TITLE","Upload Video");
define("MAX_FILE_SIZE","255000000");
define("FTP_SERVER","ftpperso.free.fr");
define("FTP_PATH","/nykto/media/");
define("HTTP_SERVER","http://megalego.free.fr");

define("JSON_FILE", "source/videos.json");
define("FOLDER_VIDEO","/");


// -----------------------------------------------------------------------------
function MakeWelcome()
{
$s	=	"<h1>Welcome</h1>\n";
$s	.=	"<p>Using this admin console you can easily upload videos for HTML5 video players. ";
$s	.=	"On the next pages that are shown, you can choose video to upload.</p>\n";
$s	.=	"<p>There are some important things to keep in mind when you use this admin console.</p>";
$s	.=	"<ul>\n<li class=\"warning\">you may not use the BACK button of your browser.</li>\n";
$s	.=	"<li class=\"warning\">you may not use the REFRESH (or RELOAD) button of your browser.</li>\n";
$s	.=	"<li class=\"warning\">only one person may be logged on at a time</li>\n";
$s	.=	"<li class=\"warning\">you must LOG OF when you are done using the admin console.</li>\n</ul>\n";
$s	.=	"<p>If you do not stick to these rules, there can be unexpected results, including duplicate entries, accidental deletions, etc.</p>\n";
$s	.=	"<p>Enjoy your new site and admin console!</p>\n";
return $s;
}
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
function MakeMenu($pagename)
{
$s	=	"<table border=\"0\" cellspacing=\"2\" cellpadding=\"4\" width=\"100%\">\n";
$s	.=	"<tr>\n";
$s	.=	"\t<td class=\"" . (($pagename	==	"videoUpload")		?	"activemenucell"	:	"menucell")	. "\"><a class=\"menulink\" href=\""	.	$_SERVER['PHP_SELF']	.	"?page=videoUpload\">Video Upload</a></td>\n";
$s	.=	"\t<td class=\"" . (($pagename	==	"logoff")		?	"activemenucell"	:	"menucell")	. "\"><a class=\"menulink\" href=\""	.	$_SERVER['PHP_SELF']	.	"?cmd=logoff\">Log off</a></td>\n";
$s	.=	"</tr>\n";
$s	.=	"</table>\n";
return $s;
}
// -----------------------------------------------------------------------------

// ----------------------------------------------------------------------------
function UploadForm($username,$password)
{
$s	=	"<h2>Choose video files to upload</h2>\n";
$s	.=	"<form enctype=\"multipart/form-data\" method=\"post\" action=\"?page=videoUpload&upload=1\"  id=\"my_form\">\n";
$s	.=	"<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"" . MAX_FILE_SIZE . "\" />\n";
$s	.=	"<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" width=\"80%\">\n";
$s	.=	"<tr><td class=\"formlabel\">FTP enable</td><td class=\"formcell\"><input name=\"ftpenable\" type=\"checkbox\" id=\"ftpenable\" /></td></tr>\n";
$s	.=	"<tr><td class=\"formlabel\">FTP server</td><td class=\"formcell\"><input name=\"ftpserver\" type=\"text\" class=\"ftpcell\" value=\"" . FTP_SERVER . "\"/></td></tr>\n";
$s	.=	"<tr><td class=\"formlabel\">FTP path</td><td class=\"formcell\"><input name=\"ftppath\" type=\"text\" class=\"ftpcell\" value=\"" . FTP_PATH . "\"/></td></tr>\n";
$s	.=	"<tr><td class=\"formlabel\">FTP login</td><td class=\"formcell\"><input name=\"ftplogin\" type=\"text\" class=\"ftpcell\" value=\"" . $username . "\"/></td></tr>\n";
$s	.=	"<tr><td class=\"formlabel\">FTP password</td><td class=\"formcell\"><input name=\"ftppassword\" type=\"password\" class=\"ftpcell\" value=\"" . $password . "\"/></td></tr>\n";
$s	.=	"<tr><td class=\"formlabel\">HTTP server</td><td class=\"formcell\"><input name=\"httpserver\" type=\"text\" class=\"ftpcell\" value=\"" . HTTP_SERVER . "\"/></td></tr>\n";
$s	.=	"<tr><td class=\"formlabel\">mp4 upload</td><td class=\"formcell\"><input name=\"mp4file\" type=\"file\" /></td></tr>\n";
$s	.=	"<tr><td class=\"formlabel\">ogv upload</td><td class=\"formcell\"><input name=\"oggfile\" type=\"file\" /></td></tr>\n";
$s	.=	"<tr><td class=\"formlabel\">thumbnail upload</td><td class=\"formcell\"><input name=\"jpegfile\" type=\"file\" /></td></tr>\n";
$s	.=	"<tr><td class=\"formlabel\">Video title</td><td class=\"formcell\" colspan=\"3\">\n";
$s	.=	"\t<input type=\"text\" name=\"videotitle\" size=\"20\" maxlength=\"20\" value=\"\" style=\"width:100%;\" />\n";
$s	.=	"</td>\n</tr>\n";
$s	.=	"<tr><td class=\"formlabel\">Short description</td><td class=\"formcell\" colspan=\"3\">\n";
$s	.=	"\t<input type=\"text\" name=\"content\" size=\"20\" maxlength=\"20\" value=\"\" style=\"width:100%;\" />\n";
$s	.=	"</td>\n</tr>\n";
$s	.=	"</table>\n";
$s	.=	"<hr />\n";
$s	.=	"<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" width=\"80%\">\n";
$s	.=	"<tr><td class=\"formbuttons\" colspan=\"3\"><input class=\"formbutton\" type=\"reset\" value=\"Reset\" />&nbsp;&nbsp;\n";
$s	.=	"<input class=\"formbutton\" type=\"submit\" name=\"submit\" value=\"Upload\" /></td></tr>\n";
$s	.=	"</form>\n";
$s  .=  "<tr><td class=\"formlabel\">Progress</td><td colspan=\"3\" id=\"php-progress\"><div class=\"progress-bar\"></div></td></tr>\n";
$s  .=  "<tr><td class=\"formlabel\">FTP Progress</td><td id=\"mp4-ftp-progress\"><div class=\"progress-bar\"></div></td>\n";
$s  .=  "<td id=\"ogg-ftp-progress\"><div class=\"progress-bar\"></div></td>\n";
$s  .=  "<td id=\"jpeg-ftp-progress\"><div class=\"progress-bar\"></div></td></tr>\n";
$s	.=	"</table>\n";
$s  .=  "<div id=\"server-results\"></div>\n";
$s	.=	"<script type=\"text/javascript\" src=\"ajax.js\"></script>\n";
return $s;
}
// ----------------------------------------------------------------------------

// ----------------------------------------------------------------------------

function UploadFiles($ftpenable)
{

	$s				=	"";
	$errors			=	array();
	$arrayForJson["type"] = array();
        $arrayForJson["filename"]= array();
        $arrayForJson["name"]= array();
        $arrayForJson["source"]= array();
        $arrayForJson["duration"]= array();
// récupère les données du formulaire

	$videotitle		=	GetScriptParameter("videotitle","");
	$content		=	GetScriptParameter("content","");
// vérifie si le titre de la vidéo a été donné et arrete le script si le champ est vide
	if (!$videotitle) 
	{
		return "<p> Il manque le titre de la vidéo !<p><br />\n";
	}
	if($ftpenable == 1) 
	{
		$ftp_server 	=	GetScriptParameter("ftpserver","");
		$ftp_path 	=	GetScriptParameter("ftppath","");
		$ftp_login		=	GetScriptParameter("ftplogin","");
		$ftp_password	=	GetScriptParameter("ftppassword","");
		$http_server 	=	GetScriptParameter("httpserver","");
		$s .= ftpUpload($videotitle,$ftp_server,$ftp_login,$ftp_password,$http_server);
	}
	else 
	{
		$filedir = "video/" . $videotitle;
// vérifie si le dossier a déjà été créé, et le crée dans le cas échéant
		if(is_dir($filedir))
		{
			$s .= "Le dossier " . $videotitle . " existe déjà<br />\n";
		}
		else
		{
			mkdir($filedir, 0777);
		}
		$filelist = array("mp4", "ogg", "jpeg");
		foreach ($filelist as &$value)
		{
			$file = $value . "file";		
// vérifie avant d'aller plus loin si le fichier a été téléchargé sur le serveur PHP		
			if (is_uploaded_file($_FILES[$file]["tmp_name"]))
			{
				$filename = $_FILES[$file]["name"];
				$type = $_FILES[$file]['type'];	
				$tmp_file = $_FILES[$file]["tmp_name"];
				$downloaded_file = $filedir . "/" . $filename;
// vérifie si le fichier est présent
				if (file_exists($downloaded_file))
				{
					$s .= "Le fichier " . $downloaded_file . " existe déjà<br />\n";
				}
				else
				{
					if ($type == "video/" . $value) 
					{
						$getID3 = new getID3;
						$ThisFileInfo = $getID3->analyze($tmp_file);
						$s .= "<p>" . $filename . " size is: " . $ThisFileInfo['filesize'] . "</p>\n";
						$s .= "<p>" . $filename . " duration is: " . $ThisFileInfo['playtime_string'] . "</p>\n";	
					}
					move_uploaded_file($tmp_file, $downloaded_file);
					$s .= "<p>" . $videotitle . "/" . $filename . " téléchargé</p>\n";	
                    if($filename !== null)
					{
						array_push($arrayForJson["type"],$type);
						array_push($arrayForJson["filename"],$filename);
						array_push($arrayForJson["name"],$videotitle);
						array_push($arrayForJson["source"],$filedir);
						array_push($arrayForJson["duration"],$ThisFileInfo['playtime_string']);
					}				
// ajoute la vidéo dans la playlist
					editJsonFile($arrayForJson);	
				}
			}
			else 
			{
				$s .= "Il manque le fichier " . $value . "<br />\n";
			}
		}
	}
	
	return $s;
}
// ----------------------------------------------------------------------------



function editJsonFile($arrayForJson){
    
    $jsonFile= JSON_FILE;
    $folder = FOLDER_VIDEO;
    $myArray["id"] = array();
    $myArray["img"] = array();
    $myArray["source"] = array();
    $myArray["name"] = array();
    $myArray["duration"] = array();
    //var_dump($arrayForJson);
 
    try{
        // Get the contents of the JSON file 
        $strJsonFileContents = file_get_contents($jsonFile);
        // Convert to array 
        $array = json_decode($strJsonFileContents, true);
		if(isset($array))
		{
			
        $id= count($array);
		}
		else
		{
			$array=[];
			$id = 0;
		}
			

        $myArray["id"]=$id;
        $nbTab = count($arrayForJson["type"]);
        for($i=0;$i<$nbTab;$i++){

            if($arrayForJson["type"][$i]=="image/jpeg" || $arrayForJson["type"][$i]=="image/png" || $arrayForJson["type"][$i]=="image/jpg"){
                $myArray["img"]= $folder."".$arrayForJson["source"][$i]."/".$arrayForJson["filename"][$i]; 
            }elseif($arrayForJson["type"][$i]=="video/mp4" || $arrayForJson["type"][$i]=="video/mpeg4" || $arrayForJson["type"][$i]=="video/ogv" || $arrayForJson["type"][$i]=="video/ogg"){
                $myArray["name"]= $arrayForJson["name"][$i];
                $myArray["source"] = $folder."".$arrayForJson["source"][$i]."/".$arrayForJson["filename"][$i];
                $myArray["duration"]= $arrayForJson["duration"][$i];
            }else{
                var_dump("Autres type rencontré: ".$arrayForJson["type"][$i]);
            }
        }

       // var_dump("===================================================================================================================");
       // var_dump($myArray);
       // var_dump("===================================================================================================================");
       
        // modify the array key/value
        array_push($array, $myArray);
        // push the new content inside the json file
        file_put_contents($jsonFile, stripslashes(json_encode($array)));
        var_dump("Fichier json mis à jour avec succès");
     } catch (Exception $ex) {
        var_dump("Erreur pendant le traitement: . $ex");
    }
}


// ----------------------------------------------------------------------------

function ftpUpload($videotitle,$ftp_server,$ftp_path,$ftp_login,$ftp_password,$http_server)
{
// initialise les messages envoyé par le script PHP
	$error 			=	"";
	$s				=	"";
	$json				= array("end" => 0,"mp4" => 0,"ogg" => 0,"jpeg" => 0, );
	writeJson($json);
// attribution du nom de dossier où sont stockés les media à partir du titre de la vidéo
	$folder_name	= preg_replace('/[^a-z]/', "", strtolower($videotitle));
// connection au serveur FTP en mode passif
	$conn_id 		= ftp_connect($ftp_server);
	$ftp_login 		= ftp_login($conn_id, $ftp_username, $ftp_password);
	$ftp_passive 	= ftp_pasv($conn_id, true);
	$ftp_path		= $ftp_path . $folder_name;
	
// le script s'arrête si la connexion n'est pas établie	
	if (!$conn_id || !$ftp_login || !$ftp_passive)
	{
		return "could not connect to ftp";
	}
//	récupère la liste des dossiers déjà créés et affiche la liste
	$folder_list = str_replace (FTP_PATH . "/", "" , ftp_nlist($conn_id, FTP_PATH));
	$s .= " Dossiers existants:<br>";
	foreach($folder_list as $file) { 
            $s .= $file . "<br>"; 
       } 
// crée un nouveau dossier si il n'existe pas
	if (in_array($folder_name, $folder_list))
	{
		$s .= "<p>le dossier $ftp_path existe déjà, mais on continue...</p><br />\n";
	}
	else
	{
		if (ftp_mkdir($conn_id, $ftp_path)) 
		{
		$s .= "<p>Le dossier $ftp_path a été créé avec succès</p><br />\n";
		}
// le script s'arrête si le dossier ne peut pas être créé
		else 
		{
			return "<p>Erreur durant la création du dossier $ftp_path</p><br />\n";
		}
	}
// récupère et affiche la liste des fichiers qui pourraient se trouver dans le dossier
	$folder_filelist	= str_replace ($ftp_path . "/" , "" , ftp_nlist($conn_id, $ftp_path));
	if($folder_filelist)
	{
		$s .= " Fichiers déjà présents:<br>";
		foreach($folder_filelist as $file) { 
            $s .= $file . "<br>"; 
       } 	
	}
// traitement du mp4, de l'ogv, et du jpg
	$filelist = array("mp4", "ogg", "jpeg");
	foreach ($filelist as &$value)
	{
		$file = $value . "file";
// vérifie avant d'aller plus loin si le fichier a été téléchargé sur le serveur PHP		
		if (is_uploaded_file($_FILES[$file]["tmp_name"]))
		{
// récupère le nom, le type et la taille du fichier à uploader,
// son chemin sur le serveur PHP, attribue le chemin sur les serveur FTP et HTTP
			$filename = $_FILES[$file]["name"];
			$type = $_FILES[$file]['type'];	
			$local_file = $_FILES[$file]["tmp_name"];
			$local_file_size  = filesize($local_file);
			$remote_file = $ftp_path . "/" . $filename;
			$http_file = HTTP_SERVER . $ftp_path . "/" . rawurlencode($filename);
// vérifie si le fichier à télécharger existe déjà		
			if (in_array($filename, $folder_filelist))
			{
				$s .= "<p> $filename existe déjà, il est remplacé au cas où...</p><br />\n";
			}
 // vérifie si le type de fichier correspond et commence l'upload asynchrone		
			if (($type == "video/" . $value) || ($type == "image/jpeg" )) 
			{ 				
				$ret = ftp_nb_put( $conn_id, $remote_file, $local_file, FTP_BINARY ); 
 
// initialise cURL pour calculer la progression du téléchargement					
					
// boucle d'execution pour détecter la progression					
				while ($ret == FTP_MOREDATA) 
				{
					$remote_file_size = getHttpFileSize($http_file);
					
// Calcule le progrès et écrit le résultat dans le fichier json
					if (isset($remote_file_size) && $remote_file_size > 0 )
					{
						$i = round(($remote_file_size/$local_file_size)*100);
						$json["$value"] = $i;
						writeJSON($json);					
					}
					else
					{
						break;
					}
					$ret = ftp_nb_continue($conn_id);
				}				 
			$json["$value"] = 100;
			writeJSON($json);
			}	 		
			else 
			{
				$error .= $value . " n'est pas le bon fichier, essaie encore!< br/>\n";
			}
		}
		else
		{
			$error .= $value . " n'a pas été téléchargé sur le serveur PHP!<br !>\n";
		}
	}
// fermeture des fichiers traités
	$json["end"] = 1;
	writeJSON($json);	
	ftp_close($conn_id);
	if ($error)
	{
		$s .= MakeErrorMessage($error);
	}	
	
}
function GenerateLogin()
{
$s	=	"<center>\n";
$s	.=	"<form name=\"loginform\" action=\"\" method=\"post\">\n";
$s	.=	"<table border=\"0\" cellspacing=\"4\" cellpadding=\"4\">\n";
$s	.=	"<tr>\n";
$s	.=	"\t<td class=\"formlabel\">Username</td>\n";
$s	.=	"\t<td><input type=\"text\" name=\"username\" value=\"\" size=\"40\" /></td>\n";
$s	.=	"</tr>\n";
$s	.=	"<tr>\n";
$s	.=	"\t<td class=\"formlabel\">Password</td>\n";
$s	.=	"\t<td><input type=\"password\" name=\"password\" value=\"\" size=\"40\" /></td>\n";
$s	.=	"</tr>\n";
$s	.=	"<tr>\n";
$s	.=	"\t<td>&nbsp;</td>\n";
$s	.=	"\t<td align=\"right\">\n";
$s	.=	"\t\t<input class=\"formbutton\" type=\"reset\" value=\"Reset\" />\n";
$s	.=	"\t\t<input class=\"formbutton\" type=\"submit\" value=\"Login\" />\n";
$s	.=	"\t</td>\n";
$s	.=	"</table>\n";
$s	.=	"</form>\n";
$s	.=	"</center>\n";
$s	.=	"<script type=\"text/javascript\">\ndocument.loginform.username.focus();\n</script>\n";
return $s;
}
// ----------------------------------------------------------------------------

// MAIN ROUTINE
// -----------------------------------------------------------------------------
session_start();
// Let us see if there is a session in progress
if(!isset($_SESSION["sessioname"]))
	{
	// No session yet, see if username and password were supplied
	$uname	=	GetScriptParameter("username","");
	$pwd		=	GetScriptParameter("password","");
	
	// If username and password do not match, show login
	if ($uname	!=	ADMINUSERNAME	||	$pwd	!=	ADMINPASSWORD)
		{
		print(MakeHeader() . GenerateLogin($uname) . MakeFooter());
		exit;
		}
	// Otherwise, start session
	else
		{
		// Once we get here, the user is authenticated and gets access
		$_SESSION["sessioname"] = $uname; 
		}
	}	

$content		=	"";	
$pagename	=	GetScriptParameter("page","welcome");
$cmd			=	strtolower(GetScriptParameter("cmd",""));
$upload			=	strtolower(GetScriptParameter("upload",""));

if ($cmd	==	"logoff")
{
//	$uname	=	$_SESSION["sessionname"];
//	unset($_SESSION["sessionname"]);
	session_destroy();
	print(MakeHeader() . GenerateLogin() . MakeFooter());
	exit;	
}
else
{
	switch($pagename)
		{
		case	"welcome"	:	$content	.=	MakeWelcome();
									break;
		case	"videoUpload"	:	$content	=	"<h1>Video Upload</h1>\n";
									$adminusername		=	GetScriptParameter("adminusername",ADMINUSERNAME);
									$adminpassword		=	GetScriptParameter("adminpassword",ADMINPASSWORD);
									if ($upload)
									{	
										$ftpenable		=	GetScriptParameter("ftpenable",0);
										print (UploadFiles($ftpenable));
										exit;
									}
									$content	.=	UploadForm($adminusername,$adminpassword);
									break;
		}
}	
// Show the final output of anything other than the login page	
print(MakeHeader() . MakeMenu($pagename) . $content . MakeFooter());
// -----------------------------------------------------------------------------
?>
