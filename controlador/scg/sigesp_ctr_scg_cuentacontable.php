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
require_once ('../../base/librerias/php/general/Json.php');
require_once ('../../modelo/sss/dao/sigesp_mod_sss_dao_registroevento.php');
require_once ('sigesp_ctr_scg_servicio.php');

if ($_POST['ObjSon']) {
	$submit = str_replace ( "\\", "", $_POST ['ObjSon'] );
	$json = new Services_JSON();
	$ArJson = $json->decode($submit);
	$oregevent = new registroEventoDao ();
	$oservicio = new ServicioScg('scg_cuentas');
	
	switch ($ArJson->operacion) {
		
		case 'catalogo' :
			$datos = $oservicio->buscarCuentasSCG($_SESSION["la_empresa"]["codemp"], $ArJson->codcuenta, $ArJson->dencuenta);
			echo generarJson($datos);
			break;
			
		case 'catalogocuentamovimiento' :
			$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
			$datos = $oservicio->buscarCuentasConMovimiento();
			echo generarJson($datos);
			break;
			
		case 'catalogocuentaresultado':
			$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
			$datos = $oservicio->buscarCuentasResultado();
			echo generarJson($datos);
			break;
			
		case 'catalogocuentafinanciera':
			$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
			$datos = $oservicio->buscarCuentasFinancieras();
			echo generarJson($datos);
			break;
			
		case 'catalogocuentafiscal':
			$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
			$datos = $oservicio->buscarCuentasFiscales();
			echo generarJson($datos);
			break;
                    
		case 'catalogoclasificadoreconomico':
			$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
			$datos = $oservicio->buscarCuentasclasificadorEconomico();
			echo generarJson($datos);
			break;
                    
		case 'catalogooncop':
			$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
			$datos = $oservicio->buscarCuentasOncop();
			echo generarJson($datos);
			break;
                    
		case 'catalogocuentamovimientoSPG' :
			$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
			$datos = $oservicio->buscarCuentasConMovimientoSPG($ArJson->cuentas);
			echo generarJson($datos);
			break;
                    
		case 'catalogocuentamovimientoSPI' :
			$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
			$datos = $oservicio->buscarCuentasConMovimientoSPI($ArJson->cuentas);
			echo generarJson($datos);
			break;
                    
		case 'catalogoclasificadoreconomicoSPG':
			$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
			$datos = $oservicio->buscarCuentasclasificadorEconomicoSPG($ArJson->cuentas);
			echo generarJson($datos);
			break;
                    
		case 'catalogoclasificadoreconomicoSPI':
			$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
			$datos = $oservicio->buscarCuentasclasificadorEconomicoSPI($ArJson->cuentas);
			echo generarJson($datos);
			break;
	}

        unset($json);
	unset($ArJson);
	unset($oregevent);
	unset($oservicio);
}
?>