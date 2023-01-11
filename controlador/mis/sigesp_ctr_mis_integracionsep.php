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
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionsep.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion) 
    {
    	case 'buscar_por_contabilizar':
    		$servicioIntegracionSEP = new ServicioIntegracionSEP();
    		$resultado = $servicioIntegracionSEP->buscarContabilizar($objetoJson->numsol,$objetoJson->fecreg,$objetoJson->fecapr,
    																 $objetoJson->tipo,$objetoJson->codigo);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSEP);
    	break;
    	
    	case 'buscar_por_rev_contabilizacion':
   			$servicioIntegracionSEP = new ServicioIntegracionSEP();
    		$resultado = $servicioIntegracionSEP->buscarRevContabilizacion($objetoJson->numsol,$objetoJson->fecreg,$objetoJson->fecapr,
    																	   $objetoJson->tipo,$objetoJson->codigo,$objetoJson->fechaconta);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSEP);
    	break;    	
    	
    	case 'buscar_por_anular':
   			$servicioIntegracionSEP = new ServicioIntegracionSEP();
    		$resultado = $servicioIntegracionSEP->buscarAnular($objetoJson->numsol,$objetoJson->fecreg,$objetoJson->fecapr,
    														   $objetoJson->tipo,$objetoJson->codigo);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSEP);
       	break;    	
    	
    	case 'buscar_por_rev_anulacion':
    		$servicioIntegracionSEP = new ServicioIntegracionSEP();
    		$resultado = $servicioIntegracionSEP->buscarRevAnulacion($objetoJson->numsol,$objetoJson->fecreg,$objetoJson->fecapr,
    																 $objetoJson->tipo,$objetoJson->codigo,$objetoJson->fechaanula);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSEP);
    	break;
    	    	
    	case 'buscar_detalles':
    		$servicioIntegracionSEP = new ServicioIntegracionSEP();
    		echo generarJsonArreglo($servicioIntegracionSEP->buscarInformacionDetalleC($objetoJson->numsol));
			unset($servicioIntegracionSEP);
    	break;   
    	 	
    	case 'contabilizar':
    		$servicioIntegracionSEP = new ServicioIntegracionSEP();
    		echo $servicioIntegracionSEP->Contabilizar($objetoJson);
    		unset($servicioIntegracionSEP);
    	break; 
    	
    	case 'rev_contabilizacion':
    		$servicioIntegracionSEP = new ServicioIntegracionSEP();
    		echo $servicioIntegracionSEP->revContabilizacion($objetoJson);
    		unset($servicioIntegracionSEP);
       	break;    	
    	    	
    	case 'anular':
    		$servicioIntegracionSEP = new ServicioIntegracionSEP();
    		echo $servicioIntegracionSEP->Anular($objetoJson);
    		unset($servicioIntegracionSEP);
    	break;    	
    	    	
    	case 'rev_anulacion':
    		$servicioIntegracionSEP = new ServicioIntegracionSEP();
    		echo $servicioIntegracionSEP->revAnulacion($objetoJson);
    		unset($servicioIntegracionSEP);
       	break;    	
    }
}
?>