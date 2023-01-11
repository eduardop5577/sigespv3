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
require_once ($dirctrrpc."/modelo/servicio/rpc/sigesp_srv_rpc_documentos.php");
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
    	case "buscarcodigo":
    		$servicioDocumentos = new servicioDocumento();
    		$codigo = $servicioDocumentos->buscarCodigoDocumento($datosempresa["codemp"]);
    		echo $codigo;
    		unset($servicioDocumentos);
    	break;
    	
    	case "catalogo":
    		$servicioDocumentos = new servicioDocumento();
    		$resultado = $servicioDocumentos->buscarDocumento($datosempresa["codemp"]);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioDocumentos);
    	break;
    	
    	case "incluir":
    		$servicioDocumentos = new servicioDocumento();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = "INSERT";
			$arrevento ['nomfisico']  = $objetoJson->nomven;
			$arrevento ['desevetra'] = "Inserto el Documento con codigo ".$objetoJson->codclas.", asociado a la empresa ".$datosempresa["codemp"];
			$valido = $servicioDocumentos->guardarDocumento($datosempresa["codemp"],$objetoJson,$arrevento);
    		if($valido)
    		{
    			$mensaje= utf8_encode('El Documento, Fue registrado.');
    		}
    		else
    		{
    			$mensaje= utf8_encode('El Documento, no se pudo registrar.'.$servicioDocumentos->mensaje);
    		}
    		unset($servicioDocumentos);
			$resultado['mensaje'] = $mensaje;  
			$resultado['valido']  = $valido;    		
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
		break;
    	
    	case "actualizar":
    		$servicioDocumentos = new servicioDocumento();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = "UPDATE";
			$arrevento ['nomfisico']  = $objetoJson->nomven;
			$arrevento ['desevetra'] = "Modifico el Documento con codigo ".$objetoJson->codclas.", asociado a la empresa ".$datosempresa["codemp"];
    		$valido = $servicioDocumentos->modificarDocumento($datosempresa["codemp"],$objetoJson,$arrevento);
    		if($valido)
    		{
    			$mensaje= utf8_encode('El Documento, Fue actualizado.');
    		}
    		else
    		{
    			$mensaje= utf8_encode('El Documento, no se pudo actualizar.'.$servicioDocumentos->mensaje);
    		}
    		unset($servicioDocumentos);
			$resultado['mensaje'] = $mensaje;  
			$resultado['valido']  = $valido;    		
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
       	break;
    	
    	case "eliminar":
    		$servicioDocumentos = new servicioDocumento();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = "DELETE";
			$arrevento ['nomfisico']  = $objetoJson->nomven;
			$arrevento ['desevetra'] = "Eliminó el Documento con codigo ".$objetoJson->codclas.", asociado a la empresa ".$datosempresa["codemp"];
    		$valido = $servicioDocumentos->eliminarDocumento($datosempresa["codemp"],$objetoJson,$arrevento);
    		if($valido)
    		{
    			$mensaje= utf8_encode('El Documento, Fue eliminado.');
    		}
    		else
    		{
    			$mensaje= utf8_encode('El Documento, no se pudo eliminar.'.$servicioDocumentos->mensaje);
    		}
    		unset($servicioDocumentos);
			$resultado['mensaje'] = $mensaje;  
			$resultado['valido']  = $valido;    		
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
    	break;
    }
}