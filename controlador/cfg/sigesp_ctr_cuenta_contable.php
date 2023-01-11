<?php
/***********************************************************************************
* @fecha de modificacion: 26/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once('../../modelo/cfg/dao/sigesp_mod_dao_cuenta_contable.php');
require_once('../../base/librerias/php/general/Json.php');

if ($_POST['ObjSon']) 	
{
	$submit = str_replace("\\","",$_POST['ObjSon']);
	$json = new Services_JSON;
	$ArJson = $json->decode($submit);
	$cuentacontable = new cuentacontableDao();

	switch ($evento)
	{
		case 'catalogo':
			$Datos = $cuentacontable->buscar();
			$ObjSon = generarJson($Datos);
			echo $ObjSon;	
			break;
	}
}
?>