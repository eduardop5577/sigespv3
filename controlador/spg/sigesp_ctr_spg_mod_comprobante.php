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
	require_once ($dirsrv.'/modelo/servicio/spg/sigesp_srv_spg_mod_comprobante.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace("\\", "", $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion)
	{
    	case "buscarComprobantes":
    		$servicioCmp = new ServicioModComprobante();
    		echo generarJson($servicioCmp->buscarComprobantes($_SESSION['la_empresa']['codemp'],$objetoJson->comprobante,$objetoJson->procede,$objetoJson->fecdesde,$objetoJson->fechasta));
    		unset($servicioCmp);
    	break;
    	
    	case "buscarUnidadAdm":
    		$servicioCmp = new ServicioModComprobante();
    		echo generarJson($servicioCmp->buscarUnidadAdministrativa($_SESSION['la_empresa']['codemp'],$objetoJson->codicuentad,$objetoJson->descuentad));
    		unset($servicioCmp);
    	break;
    	
    	case "buscarUnidadesEjecutoras":
    		$servicioCmp = new ServicioModComprobante();
    		echo generarJson($servicioCmp->buscarUnidadesEjecutoras($_SESSION['la_empresa']['codemp'],$objetoJson->codicuentad,$objetoJson->descuentad));
    		unset($servicioCmp);
    	break;
    	
    	case "buscarFormato":
    		echo selectConfig($objetoJson->sistema,$objetoJson->seccion,$objetoJson->variable,$objetoJson->valor,$objetoJson->tipo);
    	break;
    	  
    	case "buscarDetallesPresupuestario":
    		$servicioCmp = new ServicioModComprobante();
    		echo generarJson($servicioCmp->cargarDetallePresupuestario($_SESSION['la_empresa']['codemp'],$objetoJson->procede,$objetoJson->comprobante,$objetoJson->fecha));
    		unset($servicioCmp);
    	break;
    	
    	case "guardar":
    		$servicioCmp = new ServicioModComprobante($objetoJson->prefijo);
    		$arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
			$arrevento ['codusu']  = $_SESSION['la_logusr'];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = 'PROCESAR';
			$arrevento ['nomfisico']  = $objetoJson->nomven; 
			$arrevento ['desevetra'] = 'Guardo el comprobante de modificacion presupuestaria con el numero'.$objetoJson->comprobante.', asociado a la empresa '.$_SESSION['la_empresa']['codemp'];
    		$valido = $servicioCmp->guardar($_SESSION['la_empresa']['codemp'],$objetoJson,$arrevento);
    		$resultado['mensaje'] = $servicioCmp->mensaje;  
			$resultado['valido']  = $valido;    		
			$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
			unset($servicioCmp);
    		break;	
    		
    	case "eliminar":
    		$servicioCmp = new ServicioModComprobante();
    		$arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
			$arrevento ['codusu']  = $_SESSION['la_logusr'];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = 'DELETE';
			$arrevento ['nomfisico']  = $objetoJson->nomven; 
			$arrevento ['desevetra'] = 'Elimino el comprobante de modificacion presupuestaria con el número'.$objetoJson->comprobante.', asociado a la empresa '.$_SESSION['la_empresa']['codemp'];
    		$valido = $servicioCmp->eliminarLocal($_SESSION['la_empresa']['codemp'],$objetoJson,$arrevento);
    		$resultado['mensaje'] = $servicioCmp->mensaje;  
			$resultado['valido']  = $valido;    		
			$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
			unset($servicioCmp);
    		break;
    	
    	case "cargar_nrodocumento":
    		$servicioCmp = new ServicioModComprobante();
    		echo $servicioCmp->generarConsecutivo($_SESSION['la_empresa']['codemp'], $_SESSION['la_logusr'], $objetoJson->procede, $objetoJson->prefijo);
    		unset($servicioCmp);
    		break;

                case "buscarPrefijosUsuarios":
                        $servicioCmp = new ServicioModComprobante();
                        echo generarJson($servicioCmp->buscarPrefijosUsuarios($objetoJson->procede));
                        unset($servicioCmp);
                break;

                case "verificar_prefijo":
                        $servicioCmp = new ServicioModComprobante();
                        echo $servicioCmp->verificarPrefijo($_SESSION['la_empresa']['codemp'],$objetoJson->procede);
                        unset($servicioCmp);
                break;

                case "validar_nrodocumento":
                        $numdoc = str_pad($objetoJson->numdoc,15,"0",STR_PAD_LEFT);
                        $servicioCmp = new ServicioModComprobante();
                        $valido = $servicioCmp->existeNumeroComprobante($_SESSION['la_empresa']['codemp'], $objetoJson->procede, $numdoc);
                        $resultado['mensaje'] = $servicioCmp->mensaje;  
                        $resultado['numdoc']  = $numdoc;
                        $resultado['valido']  = $valido;
                        echo json_encode(array('raiz'=>$resultado));
                break;
	}   
}