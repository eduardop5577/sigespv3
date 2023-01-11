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
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_confintsigges.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion) {
    	case 'BUS_NUM':
    		$servicioConfiguracion = new ServicioConfIntSigGes();
    		echo $servicioConfiguracion->buscarNumero();
    		unset($servicioConfiguracion);
    		break;

    	case 'OBT_FON':
    		$servicioConfiguracion = new ServicioConfIntSigGes();
    		$dataFondo = $servicioConfiguracion->buscarFondo($objetoJson->codfondo, $objetoJson->denfondo);
    		echo generarJson($dataFondo);
    		unset($dataFondo);
    		unset($servicioConfiguracion);
    		break;
    	
    	case 'BUS_CUE':
    		$servicioConfiguracion = new ServicioConfIntSigGes();
    		$dataCuenta = $servicioConfiguracion->buscarCuenta($objetoJson);
    		echo generarJson($dataCuenta);
    		unset($dataCuenta);
    		unset($servicioConfiguracion);
    		break;
    		
    	case 'INS_CON':
    		$servicioConfiguracion = new ServicioConfIntSigGes();
    		$respuesta = $servicioConfiguracion->insertarConfiguracion($objetoJson);
    		$resultado['mensaje'] = $servicioConfiguracion->mensaje;
    		$resultado['valido']  = $respuesta;
    		echo  json_encode(array('raiz'=>$resultado));
    		unset($servicioConfiguracion);
    		break;

    	case 'MOD_CON':
    		$servicioConfiguracion = new ServicioConfIntSigGes();
    		$respuesta = $servicioConfiguracion->modificarConfiguracion($objetoJson);
    		$resultado['mensaje'] = $servicioConfiguracion->mensaje;
    		$resultado['valido']  = $respuesta;
    		echo  json_encode(array('raiz'=>$resultado));
    		unset($servicioConfiguracion);
    		break;
    		
    	case 'BUS_CON':
    		$servicioConfiguracion = new ServicioConfIntSigGes();
    		$dataConf = $servicioConfiguracion->buscarConfiguracion($objetoJson->numconf, $objetoJson->desconf);
    		echo generarJson($dataConf);
    		unset($dataConf);
    		unset($servicioConfiguracion);
    		break;
    		
    	case 'OBT_CUE':
    		$servicioConfiguracion = new ServicioConfIntSigGes();
    		$dataCueConf = $servicioConfiguracion->obtenerCuentas($objetoJson->numcon);
    		echo generarJson($dataCueConf);
    		unset($dataCueConf);
    		unset($servicioConfiguracion);
    		break;
    	
    	case 'ELI_CUE':
    		$servicioConfiguracion = new ServicioConfIntSigGes();
    		if($servicioConfiguracion->eliminarCuenta($objetoJson)) {
    			echo '1';
    		}
    		else {
    			echo '0';
    		}
    		unset($servicioConfiguracion);
    		break;
    		
    	case 'ELI_CON':
    		$servicioConfiguracion = new ServicioConfIntSigGes();
    		if($servicioConfiguracion->eliminarConfiguracion($objetoJson->numcon)) {
    			echo '1';
    		}
    		else {
    			echo '0';
    		}
    		unset($servicioRegistroActividad);
    		break;
    		
    	/*case 'MOD_ACT':
    		$servicioRegistroActividad = new ServicioRegistroActividad();
    		$respuesta = $servicioRegistroActividad->modificarActividad($objetoData);
    		$resultado['mensaje'] = $servicioRegistroActividad->mensaje;
    		$resultado['valido']  = $respuesta;
    		echo  json_encode(array('raiz'=>$resultado));
    		unset($servicioRegistroActividad);
    		break;*/
	}
}
?>