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
if (($_POST['ObjSon']) && ($sessionvalida))
{	
	$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
	require_once ($dirsrv.'/base/librerias/php/general/Json.php');
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionsoc.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion) 
    {
    	case 'buscar_por_contabilizar':
    		$servicioIntegracionSOC = new ServicioIntegracionSOC();
    		$resultado = $servicioIntegracionSOC->buscarContabilizar($objetoJson->estcondat,$objetoJson->numordcom,$objetoJson->cod_pro,$objetoJson->fecaprord,$objetoJson->fecordcom);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSOC);
    	break; 
		
    	case 'buscar_por_rev_contabilizacion':
    		$servicioIntegracionSOC = new ServicioIntegracionSOC();
    		$resultado = $servicioIntegracionSOC->buscarRevContabilizacion($objetoJson->estcondat,$objetoJson->numordcom,$objetoJson->cod_pro,$objetoJson->fecaprord,$objetoJson->fecordcom,$objetoJson->fechaconta);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSOC);
    	break;    	
    	
    	case 'buscar_por_anular':
    		$servicioIntegracionSOC = new ServicioIntegracionSOC();
    		$resultado = $servicioIntegracionSOC->buscarAnular($objetoJson->estcondat,$objetoJson->numordcom,$objetoJson->cod_pro,$objetoJson->fecaprord,$objetoJson->fecordcom);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSOC);
    	break;    	
    	
    	case 'buscar_por_rev_anular':
    		$servicioIntegracionSOC = new ServicioIntegracionSOC();
    		$resultado = $servicioIntegracionSOC->buscarRevAnulacion($objetoJson->estcondat,$objetoJson->numordcom,$objetoJson->cod_pro,$objetoJson->fecaprord,$objetoJson->fecordcom,$objetoJson->fechaanula);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSOC);
    	break;   
		
		case 'buscar_detalles':
    		$servicioIntegracionSOC = new ServicioIntegracionSOC();
    		echo generarJsonArreglo($servicioIntegracionSOC->buscarInformacionDetalle($objetoJson->numordcom, $objetoJson->estcondat));
			unset($ServicioIntegracionSOC);
    	break; 
    	
    	case 'contabilizar':
    		$servicioIntegracionSOC = new ServicioIntegracionSOC();
    		echo $servicioIntegracionSOC->Contabilizar($objetoJson);
    		unset($servicioIntegracionSOC);
       	break;     
    	  
    	case 'rev_contabilizacion':
    		$servicioIntegracionSOC = new ServicioIntegracionSOC();
    		echo $servicioIntegracionSOC->revContabilizacion($objetoJson);
    		unset($servicioIntegracionSOC);
       	break;       	
    	  
    	case 'anular':
    		$servicioIntegracionSOC = new ServicioIntegracionSOC();
    		echo $servicioIntegracionSOC->Anular($objetoJson);
    		unset($servicioIntegracionSOC);
       	break;   
    	    
    	case 'rev_anulacion':
    		$servicioIntegracionSOC = new ServicioIntegracionSOC();
    		echo $servicioIntegracionSOC->revAnulacion($objetoJson);
    		unset($servicioIntegracionSOC);
       	break;
    }
}
?>