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
require_once ($dirctrrpc."/modelo/servicio/rpc/sigesp_srv_rpc_tipoempresa.php");
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
    		$servicioTipoempresa = new servicioTipoempresa();
    		$codigo = $servicioTipoempresa->buscarCodigoTipoempresa($datosempresa["codemp"]);
    		echo $codigo;
    		unset($servicioTipoempresa);
    	break;
    	
    	case "catalogo":
    		$servicioTipoempresa = new servicioTipoempresa();
    		$resultado = $servicioTipoempresa->buscarTipoempresa($datosempresa["codemp"]);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioTipoempresa);
    	break;
    	
    	case "incluir":
    		$servicioTipoempresa = new servicioTipoempresa();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = "INSERT";
			$arrevento ['nomfisico']  = $objetoJson->nomven;
			$arrevento ['desevetra'] = "Inserto el Tipo de empresa con codigo ".$objetoJson->codtipoorg. ", asociado a la empresa ".$datosempresa["codemp"];
			$valido = $servicioTipoempresa->guardarTipoempresa($datosempresa["codemp"],$objetoJson,$arrevento);
    		if($valido)
    		{
    			$mensaje= utf8_encode('El Tipo de Empresa, Fue registrada.');
    		}
    		else
    		{
    			$mensaje= utf8_encode('El Tipo de Empresa, no se pudo registrar.'.$servicioTipoempresa->mensaje);
    		}
    		unset($servicioTipoempresa);
			$resultado['mensaje'] = $mensaje;  
			$resultado['valido']  = $valido;    		
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
    	break;
    	
    	case "actualizar":
    		$servicioTipoempresa = new servicioTipoempresa();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = "UPDATE";
			$arrevento ['nomfisico']  = $objetoJson->nomven;
			$arrevento ['desevetra'] = "Modifico el tipo ".$objetoJson->codtipoorg;
    		$valido = $servicioTipoempresa->modificarTipoempresa($datosempresa["codemp"],$objetoJson,$arrevento);
    		if($valido)
    		{
    			$mensaje= utf8_encode('El Tipo de Empresa, Fue actualizada.');
    		}
    		else
    		{
    			$mensaje= utf8_encode('El Tipo de Empresa, no se pudo actualizar.'.$servicioTipoempresa->mensaje);
    		}
    		unset($servicioTipoempresa);
			$resultado['mensaje'] = $mensaje;  
			$resultado['valido']  = $valido;    		
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
    	break;
    		    	
    	case "eliminar":
    		$servicioTipoempresa = new servicioTipoempresa();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = "DELETE";
			$arrevento ['nomfisico']  = $objetoJson->nomven;
			$arrevento ['desevetra'] = "Elimino el tipo ".$objetoJson->codtipoorg;
    		$valido = $servicioTipoempresa->eliminarTipoempresa($datosempresa["codemp"],$objetoJson,$arrevento);
    		if($valido)
    		{
    			$mensaje= utf8_encode('El Tipo de Empresa, Fue eliminada.');
    		}
    		else
    		{
    			$mensaje= utf8_encode('El Tipo de Empresa, no se pudo eliminar.'.$servicioTipoempresa->mensaje);
    		}
    		unset($servicioTipoempresa);
			$resultado['mensaje'] = $mensaje;  
			$resultado['valido']  = $valido;    		
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
    	break;
    }
}