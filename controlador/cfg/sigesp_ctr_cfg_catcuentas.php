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
$datosempresa=$_SESSION["la_empresa"];
require_once ('../../base/librerias/php/general/Json.php');
require_once ('sigesp_ctr_cfg_servicio.php');

if ($_POST['ObjSon'])
{
	$submit  = str_replace ( "\\", "", $_POST['ObjSon'] );
	$json = new Services_JSON ( );
	$arrjson = $json->decode ( $submit );
		
	switch ($arrjson->operacion)
	{
		case 'catalogo' :
			$oservicio	= new ServicioCfg ('scg_cuentas');
			$datos		= $oservicio->buscarCuentasContables($datosempresa["codemp"],$arrjson->codcue,$arrjson->dencue,'C');
			echo generarJson($datos);
			unset($datos);
			unset($oservicio);
			break;
		
		case 'catalogoctacob' :
			$oservicio	= new ServicioCfg ('scg_cuentas');
			$datos		= $oservicio->buscarCuentasContables($datosempresa["codemp"],$arrjson->codcuecob,$arrjson->dencuecob,'C');
			echo generarJson($datos);
			unset($datos);
			unset($oservicio);
			break;
		
		case 'catspicuenta' :
			$oservicio	= new ServicioCfg ('spi_cuentas');
			$datos		= $oservicio->buscarSpiCuentas($datosempresa["codemp"],$arrjson->codcuenta,$arrjson->dencuenta,'C');
			echo generarJson($datos);
			unset($datos);
			unset($oservicio);
			break;
	}
}
?>