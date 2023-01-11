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
require_once ($dirctrrpc."/modelo/servicio/rpc/sigesp_srv_rpc_especialidad.php");
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
  		case "buscarEspecialidad":
    		$servicioEspecialidad = new servicioEspecialidad();
    	    echo generarJson($servicioEspecialidad->buscarEspecialidad());
    	    unset($servicioEspecialidad);
    	break;
    	
    	case "buscarcodigo":
    		$servicioEspecialidad = new servicioEspecialidad();
    		$codigo = $servicioEspecialidad->buscarCodigoEspecialidad($datosempresa["codemp"]);
    		echo $codigo;
    		unset($servicioEspecialidad);
    	break;
    	
    	case "catalogo":
    		$servicioEspecialidad = new servicioEspecialidad();
    		$resultado = $servicioEspecialidad->buscarEspecialidad($datosempresa["codemp"]);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioEspecialidad);
    	break;
    	
    	case "incluir":
    		$servicioEspecialidad = new servicioEspecialidad();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = "INSERT";
			$arrevento ['nomfisico']  = $objetoJson->nomven;
			$arrevento ['desevetra'] = "Inserto la Especialidad ".$objetoJson->codesp.", asociado a la empresa ".$datosempresa["codemp"];
			$valido = $servicioEspecialidad->guardarEspecialidad($datosempresa["codemp"],$objetoJson,$arrevento);
    		if($valido)
    		{
    			$mensaje= utf8_encode('La Especialidad, Fue registrada.');
    		}
    		else
    		{
    			$mensaje= utf8_encode('La Especialidad, no se pudo registrar.'.$servicioEspecialidad->mensaje);
    		}
    		unset($servicioEspecialidad);
			$resultado['mensaje'] = $mensaje;  
			$resultado['valido']  = $valido;    		
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
    	break;
    	
    	case "actualizar":
    		$servicioEspecialidad = new servicioEspecialidad();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = "UPDATE";
			$arrevento ['nomfisico']  = $objetoJson->nomven;
			$arrevento ['desevetra'] = "Modifico la Especialidad ".$objetoJson->codesp;
    		$valido = $servicioEspecialidad->modificarEspecialidad($datosempresa["codemp"],$objetoJson,$arrevento);
    		if($valido)
    		{
    			$mensaje= utf8_encode('La Especialidad, Fue actualizada.');
    		}
    		else
    		{
    			$mensaje= utf8_encode('La Especialidad, no se pudo actualizar.'.$servicioEspecialidad->mensaje);
    		}
    		unset($servicioEspecialidad);
			$resultado['mensaje'] = $mensaje;  
			$resultado['valido']  = $valido;    		
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
    	break;
    		    	
    	case "eliminar":
    		$servicioEspecialidad = new servicioEspecialidad();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codsis']  =  $objetoJson->codsis;
			$arrevento ['evento']  = "DELETE";
			$arrevento ['nomfisico']  = $objetoJson->nomven;
			$arrevento ['desevetra'] = "Elimino la Especialidad ".$objetoJson->codesp;
    		$valido = $servicioEspecialidad->eliminarEspecialidad($datosempresa["codemp"],$objetoJson,$arrevento);
    		if($valido)
    		{
    			$mensaje= utf8_encode('La Especialidad, Fue eliminada.');
    		}
    		else
    		{
    			$mensaje= utf8_encode('La Especialidad, no se pudo eliminar.'.$servicioEspecialidad->mensaje);
    		}
    		unset($servicioEspecialidad);
			$resultado['mensaje'] = $mensaje;  
			$resultado['valido']  = $valido;    		
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
    	break;
    }
}