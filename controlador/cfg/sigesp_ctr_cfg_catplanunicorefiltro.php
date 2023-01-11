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
require_once ('../../modelo/sss/dao/sigesp_mod_sss_dao_registroevento.php');
require_once ('sigesp_ctr_cfg_servicio.php');
require_once ('../spi/sigesp_ctr_spi_servicio.php');
if ($_POST['ObjSon']) {
	$submit = str_replace ( "\\", "", $_POST['ObjSon'] );
	$json = new Services_JSON ( );
	$ArJson = $json->decode ( $submit );
	$oregevent = new registroEventoDao ( );
	
	switch ($ArJson->oper) {
		case 'catalogounico' :
			$datos = $oservicio->buscarTodos ();
			echo generarJson( $datos );
			unset($oservicio);
			unset($datos);
			break;
			
		
			
		case 'catalogoingresos' :
			$oservicio = new ServicioCfg ( 'spi_cuentas' );
			$cadenaSQL = "SELECT spi_cuenta AS sig_cuenta,
								 denominacion
							FROM spi_cuentas
							WHERE codemp='{$datosempresa["codemp"]}'
							ORDER BY spi_cuenta";
			$datos = $oservicio->buscarSql($cadenaSQL);
			echo generarJson( $datos );
			unset($oservicio);
			unset($datos);
			break;
	}
}

?>