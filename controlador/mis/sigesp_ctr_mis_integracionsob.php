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

@session_start();
require_once('../../base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();
if (($_POST['ObjSon']) && ($sessionvalida))
{	
	$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
	require_once ($dirsrv.'/base/librerias/php/general/Json.php');
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionsob.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion)
	{
    	case 'buscar_sobasi':
    		$Serviciosobasi = new ServicioIntegracionSOB();
    		echo generarJson($Serviciosobasi->buscarSobAsignacion($objetoJson->codasi,$objetoJson->codobr,$objetoJson->fecasi,$objetoJson->cod_pro,$objetoJson->estatus));
    		unset($Serviciosobasi);
    		break;  
    		
    	case 'buscar_sobcon':
    		$Serviciosobcon = new ServicioIntegracionSOB();
    		echo generarJson($Serviciosobcon->buscarSobContrato($objetoJson->codasi,$objetoJson->codcon,$objetoJson->feccon,$objetoJson->fecinicon,$objetoJson->estatus));
    		unset($Serviciosobcon);
    		break;    
    		
    	case 'buscar_detalles_gasto':
    		$Serviciosobasi = new ServicioIntegracionSOB();
    		echo generarJsonArreglo($Serviciosobasi->buscarInformacionDetalle($objetoJson->codasi,$objetoJson->codcon));
    		unset($Serviciosobasi);
    		break; 
    		
    	case 'buscar_detalles_contable_ant':
    		$Serviciosobant = new ServicioIntegracionSOB();
    		echo generarJsonArreglo($Serviciosobant->buscarDetalleContable($objetoJson->codant,$objetoJson->codcon,'sob_cuentaanticipo','codant','sob_cargoanticipo','sob_retencionanticipo',$objetoJson->codpro));
    		unset($Serviciosobant);
    		break; 
    		
    	case 'buscar_detalles_contable_val':
    		$Serviciosobant = new ServicioIntegracionSOB();
    		echo generarJsonArreglo($Serviciosobant->buscarDetalleContable($objetoJson->codant,$objetoJson->codcon,'sob_cuentavaluacion','codval','sob_cargovaluacion','sob_retencionvaluacioncontrato',$objetoJson->codpro));
    		unset($Serviciosobant);
    		break; 
    		
    	case 'buscar_detalles_gasto_ant':
    		$Serviciosobant = new ServicioIntegracionSOB();
    		echo generarJsonArreglo($Serviciosobant->buscarInformacionDetalleAntVal($objetoJson->codigo,$objetoJson->codcon,'sob_cuentaanticipo','codant')); 
    		unset($Serviciosobant);
    		break; 
    		
    	case 'buscar_detalles_gasto_val':
    		$Serviciosobval = new ServicioIntegracionSOB();
    		echo generarJsonArreglo($Serviciosobval->buscarInformacionDetalleValuacion($objetoJson->codigo,$objetoJson->codcon)); 
    		unset($Serviciosobval);
    		break; 
    		
    	case 'verificar_config':
    		$Serviciosobasi = new ServicioIntegracionSOB();
    		echo $Serviciosobasi->verificarConfig($objetoJson->sistema,$objetoJson->seccion,$objetoJson->variable);
    		unset($Serviciosobasi);
    		break;
    	
    	case 'buscar_tipodocumento':
    		$Serviciosobcon = new ServicioIntegracionSOB();
    		echo generarJson($Serviciosobcon->buscarTipoDocumento('ANTICIPO'));
    		unset($Serviciosobcon);
    		break; 
    	
    	case 'buscar_tipodocumento_valuacion':
    		$Serviciosobcon = new ServicioIntegracionSOB();
    		echo generarJson($Serviciosobcon->buscarTipoDocumento('VALUACION'));
    		unset($Serviciosobcon);
    		break; 

    	case 'buscar_sobant':
    		$Serviciosobcon = new ServicioIntegracionSOB();
			echo generarJson($Serviciosobcon->buscarSobAnticipo($objetoJson->codcon,$objetoJson->codant,$objetoJson->feccon,$objetoJson->fecant,$objetoJson->estatus));
    		unset($Serviciosobcon);
    		break;
    		
    	case 'buscar_sobval':
    		$Serviciosobval = new ServicioIntegracionSOB();
			echo generarJson($Serviciosobval->buscarSobValuacion($objetoJson->codval,$objetoJson->codcon,$objetoJson->feccon,$objetoJson->fecval,$objetoJson->estatus));
    		unset($Serviciosobval);
    		break;
    		
        case 'buscar_sobvar':
    		$Serviciosobvar = new ServicioIntegracionSOB();
			echo generarJson($Serviciosobvar->buscarSobVariacion($objetoJson->codvar,$objetoJson->codcon,$objetoJson->feccon,$objetoJson->fecvar,$objetoJson->estatus));
    		unset($Serviciosobvar);
    		break;
    			
    	case 'contabilizar_sobasi':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoContabilizarSOBASI($objetoJson);
    		break; 

    	case 'rev_contabilizar_sobasi':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoRevContabilizarSOBASI($objetoJson);
    		break; 
    		
    	case 'anular_sobasi':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoAnularSOBASI($objetoJson);
    		break; 
    		
    	case 'rev_anular_sobasi':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoAnularSOBASI($objetoJson);
    		break; 
    		
    	case 'contabilizar_sobcon':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoContabilizarSobCon($objetoJson);
    		break; 
    		
    	case 'rev_contabilizar_sobcon':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoRevContabilizarSobCon($objetoJson);
    		break; 
    		
    	case 'anular_sobcon':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoAnularSobCon($objetoJson);
    		break; 
    		
    	case 'rev_anular_sobcon':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoRevAnularSobCon($objetoJson);
    		break;
    		
    	case 'contabilizar_sobant':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoContabilizarSobAnt($objetoJson);
    		break;
    		
    	case 'rev_contabilizar_sobant':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoRevContabilizarSobAnt($objetoJson);
    		break;
    		
    	case 'contabilizar_sobval':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoContabilizarSobVal($objetoJson);
    		break;
    		
    	case 'rev_contabilizar_sobval':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoRevContabilizarSobVal($objetoJson);
    		break;
    		
    	case 'contabilizar_sobvar':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoContabilizarSobVar($objetoJson);
    		break; 

    	case 'rev_contabilizar_sobvar':
    		$servicioIntegracionSob = new ServicioIntegracionSOB();
    		echo $servicioIntegracionSob->procesoRevContabilizarSobVar($objetoJson);
    		break; 
    }
}
?>