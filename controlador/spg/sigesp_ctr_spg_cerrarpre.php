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
require_once('../../base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();
if (($_POST['ObjSon']) && ($sessionvalida))
{
	$_SESSION['session_activa']=time();
	$datosempresa=$_SESSION["la_empresa"];
	$dirctrscg = "";
	$dirctrscg = dirname(__FILE__);
	$dirctrscg = str_replace("\\","/",$dirctrscg); 
	$dirctrscg = str_replace("/controlador/spg","",$dirctrscg);
	require_once ($dirctrscg."/base/librerias/php/general/Json.php");
	require_once ($dirctrscg."/modelo/servicio/spg/sigesp_srv_spg_cerrarpre.php");
	
	
	if ($_POST['ObjSon'])
	{
		$submit = str_replace("\\", "", $_POST['ObjSon']);
		$json = new Services_JSON;
		$objetoJson = $json->decode($submit);
		$_SESSION['session_activa']=time();
		
		switch ($objetoJson->operacion)
		{
			case "cerrarPresupuesto":
				$servicio = new ServicioCierrePresupuestarioGasto();
				$arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
				$arrevento ['codusu']  = $_SESSION['la_logusr'];
				$arrevento ['codsis']  = $objetoJson->codsis;
				$arrevento ['evento']  = 'PROCESAR';
				$arrevento ['nomfisico']  = $objetoJson->nomven;
				$arrevento ['desevetra'] = 'Modificar empresa '.$_SESSION['la_empresa']['codemp'];
				$valido = $servicio->procesarCierrePresupuestario($_SESSION['la_empresa']['codemp'],$objetoJson,$arrevento);
				$resultado['mensaje'] = $servicio->mensaje;  
				$resultado['valido']  = $valido;    		
				$respuesta  =  json_encode(array('raiz'=>$resultado));
				echo $respuesta;
				unset($servicio);
				break;
			
			case "buscarEstatus":
				$servicio = new ServicioCierrePresupuestarioGasto();
				echo $servicio->buscarEstCiePreGas($_SESSION['la_empresa']['codemp']);
				unset($servicio);
				break;
			
		}
	}   
}