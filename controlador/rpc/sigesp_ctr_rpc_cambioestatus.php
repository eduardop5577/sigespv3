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
require_once ($dirctrrpc."/modelo/servicio/rpc/sigesp_srv_rpc_cambioestatus.php");
require_once ($dirctrrpc."/modelo/servicio/rpc/sigesp_srv_rpc_beneficiario.php");
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
    	case "buscarProveedores": 
    		$servicioCambioEstatus = new servicioCambioEstatus();
    		$resultado = $servicioCambioEstatus->buscarProveedor($objetoJson->cedprov,$objetoJson->nomprov,$objetoJson->dirprov,$objetoJson->rifprov);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioCambioEstatus);
    	break;

    	case "cargarProveedores": 
    		$servicioCambioEstatus = new servicioCambioEstatus();
    		$resultado = $servicioCambioEstatus->cargarProveedores($objetoJson->cod_prodesde,$objetoJson->cod_prohasta,$objetoJson->estprov);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioCambioEstatus);
    	break;   	
    	
    	case "actualizar":
    		$servicioCambioEstatus = new servicioCambioEstatus();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
    		$arrevento ['codusu']  = $_SESSION["la_logusr"];
    		$arrevento ['codsis']  = $objetoJson->codsis;
    		$arrevento ['evento']  = "UPDATE";
    		$arrevento ['nomfisico']  = $objetoJson->nomven;
    		$arrevento ['desevetra'] = "Modifico el proveedor con codigo ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
    		$valido = $servicioCambioEstatus->actualizarEstatus($datosempresa["codemp"],$objetoJson->arrProveedor,$objetoJson->estprovnew,$arrevento);
    		if($valido)
    		{
    			$mensaje= utf8_encode('Los Estatus de los proveedores se cambiaron de forma satisfactoria.');
    		}
    		else
    		{
    			$mensaje= utf8_encode('Ocurrio un error al cambiar los estatus de los proveedores.'.$servicioCambioEstatus->mensaje);
    		}
    		unset($servicioCambioEstatus);
			$resultado['mensaje'] = $mensaje;  
			$resultado['valido']  = $valido;    		
    		$respuesta  =  json_encode(array('raiz'=>$resultado));
			echo $respuesta;
    	break;
	}
}   
