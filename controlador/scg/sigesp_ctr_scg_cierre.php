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
$_SESSION['comprobantecierre']=true;
$datosempresa=$_SESSION["la_empresa"];
$dirctrscg = "";
$dirctrscg = dirname(__FILE__);
$dirctrscg = str_replace("\\","/",$dirctrscg); 
$dirctrscg = str_replace("/controlador/scg","",$dirctrscg);
require_once ($dirctrscg."/base/librerias/php/general/Json.php");
require_once ($dirctrscg."/modelo/servicio/scg/sigesp_srv_scg_cierre.php");
$sessionvalida = validarSession();

if (($_POST['ObjSon']) && ($sessionvalida))
{
	$_SESSION['session_activa'] = time();
    $submit = str_replace("\\", "", $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion)
	{		
    	case "llenar_documento":
    		$numdoc = str_pad($objetoJson->numdoc,15,"0",LEFT); 
    		echo $numdoc;
    	break;
    	
    	case "verificar_estatus_ciesem":
    		$servicioSCG = new ServicioCierreSCG();
    		$resultado = $servicioSCG->verificarEstatusCierreSemestral();
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
			unset($respuesta);
    	break;
    	
    	case "verificar_cierre":
    		$servicioSCG = new ServicioCierreSCG();
    		$resultado = $servicioSCG->verificarCierre();
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
			unset($respuesta);
    	break;
    	
    	case "cargar_detalle_comprobante":
    		$servicioSCG = new ServicioCierreSCG();
    		echo generarJsonArreglo($servicioSCG->cargarDetalleComprobante($_SESSION['la_empresa']['codemp'],$objetoJson->procede,$objetoJson->comprobante,$objetoJson->fecha));
    		unset($servicioSCG);
    	break;
    	
    	case "procesar":
    		$servicioScg = new ServicioCierreSCG();
    		$arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
			$arrevento ['codusu']  = $_SESSION['la_logusr'];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = 'PROCESAR';
			$arrevento ['nomfisico']  = $objetoJson->nomven; 
			$arrevento ['desevetra'] = 'Guardo el comprobante contable con el numero'.$objetoJson->comprobante.', asociado a la empresa '.$_SESSION['la_empresa']['codemp'];
    		$valido = $servicioScg->guardarCierreEjercicio($_SESSION['la_empresa']['codemp'],$objetoJson,$arrevento);
    		$resultado['mensaje'] = $servicioScg->mensaje;  
			$resultado['valido']  = $servicioScg->valido;    		
			$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
			unset($servicioScg);
    		break;	
    		
    	case "eliminar":
    		$servicioScg = new ServicioCierreSCG();
    		$arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
			$arrevento ['codusu']  = $_SESSION['la_logusr'];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = 'DELETE';
			$arrevento ['nomfisico']  = $objetoJson->nomven; 
			$arrevento ['desevetra'] = 'Elimino el comprobante con el número'.$objetoJson->comprobante.', asociado a la empresa '.$_SESSION['la_empresa']['codemp'];
    		$valido = $servicioScg->eliminarCierreEjercicio($_SESSION['la_empresa']['codemp'],$objetoJson,$arrevento);
    		$resultado['mensaje'] = $servicioScg->mensaje;  
			$resultado['valido']  = $valido;    		
			$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
			unset($servicioScg);
    		break;
	}   
}