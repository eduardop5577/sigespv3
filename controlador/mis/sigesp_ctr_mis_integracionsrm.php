<?php
/***********************************************************************************
* @fecha de modificacion: 28/07/2022, para la version de php 8.1 
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
if (($_POST['ObjSon']) && ($sessionvalida)) {	
	$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
	require_once ($dirsrv.'/base/librerias/php/general/Json.php');
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionsrm.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion) {
    	case 'buscar_por_contabilizar':
    		$servicioIntegracionSRM = new ServicioIntegracionSRM();
    		echo generarJson($servicioIntegracionSRM->buscarCobranzas($objetoJson->numcom, $objetoJson->fecmov, 0));
    		unset($servicioIntegracionSRM);
    		break;

    	case 'buscar_por_reversar':
    		$servicioIntegracionSRM = new ServicioIntegracionSRM();
    		echo generarJson($servicioIntegracionSRM->buscarCobranzas($objetoJson->numcom, $objetoJson->fecmov, 1));
    		unset($servicioIntegracionSRM);
    		break;
		
    	case 'detalle_ingreso':
    		$servicioIntegracionSRM = new ServicioIntegracionSRM();
    		echo generarJson($servicioIntegracionSRM->obtenerDetalleIngreso($objetoJson->numcom,$objetoJson->procede,$objetoJson->fecha,$objetoJson->codban,$objetoJson->ctaban));
    		unset($servicioIntegracionSRM);
    		break;
    		
    	case 'detalle_contable':
    		$servicioIntegracionSRM = new ServicioIntegracionSRM();
    		echo generarJsonArreglo($servicioIntegracionSRM->obtenerDetalleContable($objetoJson->numcom,$objetoJson->procede,$objetoJson->fecha,$objetoJson->codban,$objetoJson->ctaban));
    		unset($servicioIntegracionSRM);
    		break;
    	    	   	   	
    	case 'contabilizar':
    		$servicioIntegracionSRM = new ServicioIntegracionSRM();
    		echo $servicioIntegracionSRM->procesoContabilizarSRM($objetoJson);
    		break;

    	case 'rev_contabilizar':
    		$servicioIntegracionSRM = new ServicioIntegracionSRM();
    		echo $servicioIntegracionSRM->procesoRevContabilizarSRM($objetoJson);
    		break;
    }
}
?>