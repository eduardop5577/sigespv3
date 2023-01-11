<?php
$path = $_SERVER['DOCUMENT_ROOT'].'/ap/';

$data=explode("_",$_SESSION["ls_database"]);
$file="".$data[1].".php";

$archivo=$path."/".$data[1].".php";

if (file_exists("$archivo"))
{
	require_once($archivo);
}
else
{
	unset($_SESSION);
}

require_once("classv.php");
?>
