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

session_start(); 
require_once('../../base/librerias/php/general/Json.php');
require_once('sigesp_ctr_cfg_servicio.php');

if ($_POST['ObjSon']) {
	$submit = str_replace("\\","",$_POST['ObjSon']);
	$json = new Services_JSON;	
	$ArJson = $json->decode($submit);
	$oservicio = new ServicioCfg('spg_cuentas');
	$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
	
	switch ($ArJson->oper){
		
		case 'buscar_cuenta':
			$datos  = $oservicio->buscarCuentasSpg($_SESSION["la_empresa"]["codemp"],$ArJson->codcue,$ArJson->dencue);
			echo generarJson($datos);
			unset($datos);
			break;
			
		case 'buscar_cuenta_servicio':
			$datos  = $oservicio->buscarCuentasSpg($_SESSION["la_empresa"]["codemp"],$ArJson->codcue,$ArJson->dencue,$_SESSION["la_empresa"]["soc_servic"]);
			echo generarJson($datos);
			unset($datos);
			break;
	}
	unset($oservicio);
	unset($json);
	unset($ArJson);
}

?>