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
require_once('../../base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();
if (($_GET['formula']) && ($sessionvalida))
{
	$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
	require_once ($dirsrv.'/base/librerias/php/general/Json.php');
	require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_formula.php');
	
	if (($_GET['formula'])&&($_GET['monto'])) 		
	{
		$formula = str_replace("\\","",$_GET['formula']);
		$monto  =  str_replace("\\","",$_GET['monto']);
		$json = new Services_JSON;
		$monto = str_replace('.','',$monto);
		$monto = str_replace(',','.',$monto);
		$valido  = false;
		$io_formula = new evaluarFormula();
		$arrResultado = $io_formula->evaluar($formula,$monto);
	  	$monto=$arrResultado['result'];
	  	$valido=$arrResultado['valido'];
		if ($valido)
		{
		  $mensaje = "F&#243;rmula V&#225;lida. Total Monto de Prueba =".number_format($monto,2,',','.'); 	
		}
		else
		{
		  $mensaje = "F&#243;rmula Inv&#225;lida";
		}
		$monto   = number_format($monto,2,',','.');
		
		$texto = array("mensaje"=>$mensaje,"valido"=>$valido);
		$texto = $json->encode($texto);
		echo $texto;	 
	}
}
?>