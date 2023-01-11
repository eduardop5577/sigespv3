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
$dirctrrpc = "";
$dirctrrpc = dirname(__FILE__);
$dirctrrpc = str_replace("\\","/",$dirctrrpc); 
$dirctrrpc = str_replace("/controlador/rpc","",$dirctrrpc);
require_once ($dirctrrpc."/base/librerias/php/general/Json.php");
require_once ($dirctrrpc."/modelo/servicio/rpc/sigesp_srv_rpc_transferencia.php");
require_once ($dirctrrpc.'/base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();

if (($_POST['ObjSon']) && ($sessionvalida))
{
    $_SESSION['session_activa'] = time();
    $submit = str_replace("\\", "", $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
       
    switch ($objetoJson->operacion)
    {
    	case "buscarFiltroPersonal":
    		$servicioTransferencia = new servicioTransferencia();
    		$resultado = $servicioTransferencia->buscarFiltroPersonal($datosempresa['codemp'],$objetoJson->cedperdes,$objetoJson->cedperhas);
    		echo generarJson($resultado);
    		unset($resultado);
    		unset($servicioTransferencia);
    	break;
    		
     	case 'procesar':
    		$servicioTransferencia = new servicioTransferencia();
    		$arrevento ['codemp']  = $datosempresa['codemp'];
    		$arrevento ['codusu']  = $_SESSION['la_logusr'];
    		$arrevento ['codsis']  = $objetoJson->codsis;
    		$arrevento ['evento']  = 'PROCESAR';
    		$arrevento ['nomfisico']  = $objetoJson->nomven;
    		$arrevento ['desevetra'] = '';
    		$valido = $servicioTransferencia->trasferirPersonalBeneficiario($datosempresa['codemp'],$objetoJson,$arrevento);
    		if($valido)
    		{
    			$mensaje= utf8_encode('La Transferencia del Personal se realizó con exito.');
    		}
    		else
    		{
    			$mensaje= utf8_encode('No se pudo procesar la Transferencia del Personal.'.$servicioTransferencia->mensaje);
    		}
    		unset($servicioTransferencia);
			$resultado['mensaje'] = $mensaje;  
			$resultado['valido']  = $valido;    		
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
		break;
		
		case 'transTodo':
			$servicioTransferencia = new servicioTransferencia();
			$arrevento ['codemp']  = $datosempresa['codemp'];
			$arrevento ['codusu']  = $_SESSION['la_logusr'];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = 'PROCESAR';
			$arrevento ['nomfisico']  = $objetoJson->nomven;
			$arrevento ['desevetra'] = '';
			$valido = $servicioTransferencia->transferirTodos($datosempresa['codemp'], $objetoJson->sc_cuenta, $arrevento);
			if($valido)
			{
				$mensaje= utf8_encode('La Transferencia del Personal se realizó con exito.');
			}
			else
			{
				$mensaje= utf8_encode('No se pudo procesar la Transferencia del Personal.'.$servicioTransferencia->mensaje);
			}
			unset($servicioTransferencia);
			$resultado['mensaje'] = $mensaje;
			$resultado['valido']  = $valido;
			$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
			break;
	}
}   
