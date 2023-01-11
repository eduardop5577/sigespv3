<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$ls_file=$_GET["file"];
$ls_enlace=$_GET["enlace"]."/".$ls_file;
header ("Content-Disposition: attachment; filename=".$ls_file."\n\n");
header ("Content-Type: application/octet-stream");
header ("Content-Length: ".filesize($ls_enlace));
readfile($ls_enlace);
?>
