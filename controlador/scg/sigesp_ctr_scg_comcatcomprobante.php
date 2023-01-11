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
	$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
	require_once ($dirsrv.'/base/librerias/php/general/Json.php');
	require_once ($dirsrv.'/modelo/servicio/scg/sigesp_srv_scg_comprobante_contable.php');
	require_once ($dirsrv.'/modelo/servicio/spg/sigesp_srv_spg_comprobante.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion) 
    {
    	case "buscarComprobantes": 
    		$servicioCueCon = new ServicioComprobanteContable();
    		$resultado = $servicioCueCon->buscarComprobantes($_SESSION["la_empresa"]["codemp"],$objetoJson->comprobante,$objetoJson->procede,'','',
    				                                         $objetoJson->fecdesde,$objetoJson->fechasta,$objetoJson->tipcom);
			echo generarJson($resultado);
			unset($resultado);
			unset($servicioCueCon);
    		break;
			
    	case "buscarComprobantesPresupuestarios":
    		$servicioCmp = new ServicioComprobantePresupuestarioGasto();
    		echo generarJson($servicioCmp->buscarComprobantes($_SESSION['la_empresa']['codemp'],$objetoJson->comprobante,$objetoJson->procede,$objetoJson->tipo,$objetoJson->provben,$objetoJson->fecdesde,$objetoJson->fechasta,'',$objetoJson->numconcom));
    		unset($servicioCmp);
    	break;
    }
}
?>
