<?php
/***********************************************************************************
* @fecha de modificacion: 01/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();
$_SESSION['session_activa'] = time();	
if (isset($_FILES['archivo']))
{
	if ($_FILES['archivo']['error'] == UPLOAD_ERR_OK)
	{
		$dirctrscg = "";
		$dirctrscg = dirname(__FILE__);
		$dirctrscg = str_replace("\\","/",$dirctrscg); 
		$dirctrscg = str_replace("/controlador/scg","",$dirctrscg);

		$archivo = $_FILES['archivo']['name']; 
		$tipo=$_FILES["archivo"]["type"]; 
        move_uploaded_file($_FILES['archivo']['tmp_name'], $dirctrscg.'/vista/scg/txt/'.$_SESSION['la_logusr'].'.txt');
    }
}
?>