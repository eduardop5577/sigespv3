<?php

$path = dirname(__FILE__, 6)."\ap";
$path = str_replace("\\","/",$path);

ini_set("include_path", $path); 
$data=explode("_",$_SESSION["ls_database"]);
$file="".$data[1].".php";
$archivo=$path."/".$data[1].".php";

if (file_exists("$archivo"))
{
	require_once($file);
}
else
{
	unset($_SESSION);
}

require_once("validate.php");
?>