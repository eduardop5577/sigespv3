<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$archivo=$_GET['archivo'];
$enlace=$_GET['enlace'].$archivo;
$tipo=$_GET['tipo'];
switch ($tipo)
{
	case 'abrir':
		header ('Content-Disposition: attachment; filename='.$archivo.'');
		header ('Content-Type: application/octet-stream');
		header ('Content-Length: '.filesize($enlace));
		readfile($enlace);
	break;
	
	case 'eliminar':
		header('Pragma: public');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private',false);
		@unlink($enlace);
	break;
}
?>
