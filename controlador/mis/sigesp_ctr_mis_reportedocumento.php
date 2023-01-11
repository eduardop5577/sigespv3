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
$datosempresa=$_SESSION["la_empresa"];
$dirctrrpc = "";
$dirctrrpc = dirname(__FILE__);
$dirctrrpc = str_replace("\\","/",$dirctrrpc); 
$dirctrrpc = str_replace("/controlador/mis","",$dirctrrpc);
require_once ($dirctrrpc."/base/librerias/php/general/Json.php");
require_once ($dirctrrpc."/modelo/servicio/mis/sigesp_srv_mis_reportedocumento.php");
require_once ($dirctrrpc.'/base/librerias/php/general/sigesp_lib_crearreporte.php');

if ($_POST['ObjSon']) {
    $submit = str_replace("\\", "", $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion) {
		case "catalogo_beneficiario":
    		
    	    break; 
    	    
    	case "buscarBeneReporte":
    	    $servicioReporteBeneficiario = new servicioReporteBeneficiario();
    	    $resultado = $servicioReporteBeneficiario->buscarBeneReporte($datosempresa['codemp'],$objetoJson->ced_benedesde,$objetoJson->ced_benehasta, $objetoJson->orden);
    	    $objReporte = new crearReporte('rpc');
    	    $objReporte->crearXml('listado_beneficiarios',$resultado);
    	    $objReporte->nomRep = 'sigesp_rpc_listado_beneficiarios';
    	    echo $objReporte->mostrarReporte();
    	    unset($servicioReporteBeneficiario);
    	    break;
    	    
    	
    } 
       
}