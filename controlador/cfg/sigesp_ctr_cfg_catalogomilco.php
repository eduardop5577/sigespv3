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
if (($_POST['ObjSon']) && ($sessionvalida))
{
	$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
	require_once($dirsrv.'/base/librerias/php/general/Json.php');
	require_once($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
	require_once('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	
	if ($_POST['ObjSon']) 	
	{
		$submit = str_replace("\\","",$_POST['ObjSon']);
		$json = new Services_JSON;	
		$ArJson = $json->decode($submit);
		$oservicio = new ServicioCfg ('sigesp_catalogo_milco'); 
		
		switch ($ArJson->oper)
		{
			case 'catalogoMilco':
				$dataMilco = $oservicio->buscarTodos();					
				echo generarJson($dataMilco);
				unset($dataMilco);	
				break;
		}
		
		unset($oservicio);
	}
}
?>