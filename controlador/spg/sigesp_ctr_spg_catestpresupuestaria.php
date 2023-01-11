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
$datosempresa=$_SESSION["la_empresa"]; 
require_once ('../../base/librerias/php/general/Json.php');
require_once ('../../modelo/sss/dao/sigesp_mod_sss_dao_registroevento.php');
require_once ('sigesp_ctr_spg_servicio.php');
if ($_POST['ObjSon']) 
{
	$_SESSION['session_activa']=time();
	$submit = str_replace ( "\\", "", $_POST['ObjSon'] );
	$json = new Services_JSON ( );
	$ArJson = $json->decode ( $submit );
	$oregevent = new registroEventoDao ( );
	$evento = $ArJson->operacion;
	$oservicioest = new ServicioSpg ( 'spg_ep1' );
	$oservicioest->setCodemp ( $datosempresa["codemp"] );
	
	switch ($evento)
	{
		case 'nivel1' :
			$datos = $oservicioest->buscarEstructuraNivel1 ();
			echo generarJson ($datos,false,false);
			unset($datos);
			break;
		case 'nivel2' :
			$datos = $oservicioest->buscarEstructuraNivel2 ($ArJson->codest0);
			echo generarJson ($datos,false,false);
			unset($datos);			
			break;
		case 'nivel3' :
			$datos = $oservicioest->buscarEstructuraNivel3 ($ArJson->codest0,$ArJson->codest1);
			echo generarJson ($datos,false,false);
			unset($datos);			
			break;
		case 'nivel4' :
			$datos = $oservicioest->buscarEstructuraNivel4 ($ArJson->codest0,$ArJson->codest1,$ArJson->codest2);
			echo generarJson ($datos,false,false);
			unset($datos);			
			break;
		case 'nivel5' :
			$datos = $oservicioest->buscarEstructuraNivel5 ($ArJson->codest0,$ArJson->codest1,$ArJson->codest2,$ArJson->codest3);
			echo generarJson ($datos,false,false);
			unset($datos);			
			break;
		case 'nivelN' :
			$datos = $oservicioest->buscarEstructuraNivelN ($ArJson->cantnivel, $_SESSION["la_empresa"]);
			echo generarJson ($datos,false,false);
			unset($datos);			
			break;
	}
}

?>