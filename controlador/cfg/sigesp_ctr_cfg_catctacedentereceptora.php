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

if ($_POST['ObjSon']) {
	$submit = str_replace ( "\\", "", $_POST['ObjSon'] );
	$json = new Services_JSON ( );
	$ArJson = $json->decode ( $submit );
			
	switch ($ArJson->operacion) {
		case 'catalogo' :
			$oservicio = new ServicioCfg ( 'sigesp_plan_unico_re' );
			$formpre=$datosempresa["formpre"];
			$li_len_formato_spg = strlen(str_replace('-','',trim($formpre)));
			$ls_cuenta401 = str_pad("401",$li_len_formato_spg,0);
			$ls_cuenta402 = str_pad("402",$li_len_formato_spg,0);
			$ls_cuenta403 = str_pad("403",$li_len_formato_spg,0);
			$ls_cuenta404 = str_pad("404",$li_len_formato_spg,0);
			$ls_cuenta405 = str_pad("405",$li_len_formato_spg,0);
			$ls_cuenta406 = str_pad("406",$li_len_formato_spg,0);
			$ls_cuenta407 = str_pad("407",$li_len_formato_spg,0);
			$ls_cuenta408 = str_pad("408",$li_len_formato_spg,0);
			$ls_cuenta409 = str_pad("409",$li_len_formato_spg,0);
			$ls_cuenta410 = str_pad("410",$li_len_formato_spg,0);
			$ls_cuenta411 = str_pad("411",$li_len_formato_spg,0);
			$ls_cuenta412 = str_pad("412",$li_len_formato_spg,0);
			$ls_cuenta498 = str_pad("498",$li_len_formato_spg,0);
			$cadenasql    = "SELECT substr(sig_cuenta,1,3) as codcuenta, denominacion 
			                FROM sigesp_plan_unico_re 
							WHERE sig_cuenta in ('".$ls_cuenta401."','".$ls_cuenta402."','".$ls_cuenta403."','".$ls_cuenta404."', '".$ls_cuenta405."','".$ls_cuenta406."','".$ls_cuenta407."','".$ls_cuenta408."', '".$ls_cuenta409."','".$ls_cuenta410."','".$ls_cuenta411."','".$ls_cuenta412."','".$ls_cuenta498."') 
							GROUP BY sig_cuenta,denominacion ORDER BY sig_cuenta";
			$dataCuenta   = $oservicio->buscarSql($cadenasql);
			echo generarJson($dataCuenta);
			unset($dataCuenta);
			unset($oservicio);
			break;
	}
}
?>