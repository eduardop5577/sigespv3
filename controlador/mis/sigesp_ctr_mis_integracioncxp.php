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
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracioncxpsop.php');
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracioncxpncnd.php');
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracioncxprd.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion){
    	/*************************************
    	 * INTEGRACION SOLICITUDES DE PAGO
    	 *************************************/
    	case 'buscar_por_contabilizar_sop':
    		$servicioIntegracionCXPSOP = new ServicioIntegracionCXPSOP();
    		$resultado = $servicioIntegracionCXPSOP->buscarSolicitudesIntegrar($objetoJson->numsol,$objetoJson->fecreg,$objetoJson->fecapr,
    																		   $objetoJson->tipo,$objetoJson->codigo,'E',$objetoJson->estrepcon);
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPSOP);
    		break;
    	
    	case 'buscar_por_rev_contabilizacion_sop':
   			$servicioIntegracionCXPSOP = new ServicioIntegracionCXPSOP();
    		$resultado = $servicioIntegracionCXPSOP->buscarSolicitudesIntegrar($objetoJson->numsol,$objetoJson->fecreg,$objetoJson->fecapr,
    																		   $objetoJson->tipo,$objetoJson->codigo,'C',$objetoJson->estrepcon);
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPSOP);
    		break;    	
    	
    	case 'buscar_por_anular_sop':
   			$servicioIntegracionCXPSOP = new ServicioIntegracionCXPSOP();
    		$resultado = $servicioIntegracionCXPSOP->buscarSolicitudesIntegrar($objetoJson->numsol,$objetoJson->fecreg,$objetoJson->fecapr,
    																	       $objetoJson->tipo,$objetoJson->codigo,'C',$objetoJson->estrepcon);
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPSOP);
       		break;

       	case 'buscar_por_rev_anulacion_sop':
    		$servicioIntegracionCXPSOP = new ServicioIntegracionCXPSOP();
    		$resultado = $servicioIntegracionCXPSOP->buscarSolicitudesIntegrar($objetoJson->numsol,$objetoJson->fecreg,$objetoJson->fecapr,
    																	  	$objetoJson->tipo,$objetoJson->codigo,'A',$objetoJson->estrepcon);
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPSOP);
    		break;
    	
    	case 'comprobante_detalle_spgsop':
    		$servicioIntegracionCXPSOP = new ServicioIntegracionCXPSOP();
    		echo generarJsonArreglo($servicioIntegracionCXPSOP->obtenerDetalleComprobanteSOPSPG($objetoJson->numsol,$objetoJson->fecreg));
			unset($servicioIntegracionCXPSOP);
    		break;

    	case 'comprobante_detalle_scgsop':
    		$servicioIntegracionCXPSOP = new ServicioIntegracionCXPSOP();
    		$resultado = $servicioIntegracionCXPSOP->obtenerDetalleComprobanteSOPSCG($objetoJson->numsol);
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPSOP);
    		break;
    	 	
    	case 'contabilizar_sop':
    		$servicioIntegracionCXPSOP = new ServicioIntegracionCXPSOP();
    		echo $servicioIntegracionCXPSOP->procesoContabilizarSOP($objetoJson);
    		unset($servicioIntegracionCXPSOP);
    		break; 
    	
    	case 'rev_contabilizacion_sop':
    		$servicioIntegracionCXPSOP = new ServicioIntegracionCXPSOP();
    		echo $servicioIntegracionCXPSOP->procesoReversoContabilizaSOP($objetoJson);
    		unset($servicioIntegracionCXPSOP);
       		break;    	
    	    	
    	case 'anular_sop':
    		$servicioIntegracionCXPSOP = new ServicioIntegracionCXPSOP();
    		echo $servicioIntegracionCXPSOP->procesoAnularSOP($objetoJson);
    		unset($servicioIntegracionCXPSOP);
    		break;    	
    	    	
    	case 'rev_anulacion_sop':
    		$servicioIntegracionCXPSOP = new ServicioIntegracionCXPSOP();
    		echo $servicioIntegracionCXPSOP->procesoReversoAnulacionSOP($objetoJson);
    		unset($servicioIntegracionCXPSOP);
       		break;
       		
		/*************************************
    	 * INTEGRACION NOTAS CREDITO|DEBITO
    	 *************************************/
       	case 'buscar_por_contabilizar_ncd':
    		$servicioIntegracionCXPNCND = new ServicioIntegracionCXPNCND();
    		$resultado = $servicioIntegracionCXPNCND->buscarNotasIntegrar($objetoJson->numsol, $objetoJson->numrecdoc, $objetoJson->codope, 
    																      $objetoJson->fecope, $objetoJson->fecaprnc, 'E');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPNCND);
    		break;
    		
    	case 'comprobante_detalle_spgncd':
    		$servicioIntegracionCXPNCND = new ServicioIntegracionCXPNCND();
    		echo generarJsonArreglo($servicioIntegracionCXPNCND->obtenerDetalleComprobanteNCDSPG($objetoJson->numsol, $objetoJson->numrecdoc, 
    																							 $objetoJson->codtipdoc, $objetoJson->ced_bene, 
    																							 $objetoJson->cod_pro, $objetoJson->codope, 
    																							 $objetoJson->numdc, $objetoJson->fecope));
			unset($servicioIntegracionCXPNCND);
    		break;

    	case 'comprobante_detalle_scgncd':
    		$servicioIntegracionCXPNCND = new ServicioIntegracionCXPNCND();
    		$resultado = $servicioIntegracionCXPNCND->obtenerDetalleComprobanteNCDSCG($objetoJson->numsol, $objetoJson->numrecdoc, 
    																				  $objetoJson->codtipdoc, $objetoJson->ced_bene, 
    																				  $objetoJson->cod_pro, $objetoJson->codope, 
    																				  $objetoJson->numdc);
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPNCND);
    		break;
    	
    	case 'contabilizar_ncd':
    		$servicioIntegracionCXPNCND = new ServicioIntegracionCXPNCND();
    		echo $servicioIntegracionCXPNCND->procesoContabilizarNCD($objetoJson);
    		unset($servicioIntegracionCXPNCND);
    		break;
    		
    	case 'buscar_por_reversar_ncd':
    		$servicioIntegracionCXPNCND = new ServicioIntegracionCXPNCND();
    		$resultado = $servicioIntegracionCXPNCND->buscarNotasIntegrar($objetoJson->numsol, $objetoJson->numrecdoc, $objetoJson->codope, 
    																  $objetoJson->fecope, $objetoJson->fecaprnc, 'C');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPNCND);
    		break;
    	
    	case 'rev_contabilizar_ncd':
    		$servicioIntegracionCXPNCND = new ServicioIntegracionCXPNCND();
    		echo $servicioIntegracionCXPNCND->procesoRevContabilizarNCD($objetoJson);
    		unset($servicioIntegracionCXPNCND);
    		break;

    	/***************************************
    	 * INTEGRACION RECEPCION DE DOCUMENTOS
    	 ***************************************/
    	case 'validar_recepciones':
    		echo $_SESSION["la_empresa"]["conrecdoc"];
    		break;
    		
    	case 'comprobante_detalle_spgrec':
    		$servicioIntegracionCXPRD = new ServicioIntegracionCXPRD();
    		echo generarJsonArreglo($servicioIntegracionCXPRD->obtenerDetalleComprobanteRECSPG($objetoJson->numrecdoc, $objetoJson->codtipdoc, 
    																						   $objetoJson->ced_bene, $objetoJson->cod_pro,
    																						   $objetoJson->fecregdoc));
			unset($servicioIntegracionCXPRD);
    		break;

    	case 'comprobante_detalle_scgrec':
    		$servicioIntegracionCXPRD = new ServicioIntegracionCXPRD();
    		$resultado = $servicioIntegracionCXPRD->obtenerDetalleComprobanteRECSCG($objetoJson->numrecdoc, $objetoJson->codtipdoc, 
    																				$objetoJson->ced_bene, $objetoJson->cod_pro);
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPRD);
    		break;
    		
    	case 'buscar_por_contabilizar_rec':
    		$servicioIntegracionCXPRD = new ServicioIntegracionCXPRD();
    		$resultado = $servicioIntegracionCXPRD->buscarRecepcionIntegrar($objetoJson->numrecdoc, $objetoJson->fecreg, $objetoJson->fecapr,
    																	  $objetoJson->tipo, $objetoJson->codigo, 'E');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPRD);
    		break;
    		
    	case 'contabilizar_rec':
    		$servicioIntegracionCXPRD = new ServicioIntegracionCXPRD();
    		echo $servicioIntegracionCXPRD->procesoContabilizarREC($objetoJson);
    		unset($servicioIntegracionCXPRD);
    		break;
    		
    	case 'buscar_por_reversar_rec':
    		$servicioIntegracionCXPRD = new ServicioIntegracionCXPRD();
    		$resultado = $servicioIntegracionCXPRD->buscarRecepcionIntegrar($objetoJson->numrecdoc, $objetoJson->fecreg, $objetoJson->fecapr,
    																	  $objetoJson->tipo, $objetoJson->codigo, 'C', 'REVERSAR');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPRD);
    		break;
    	
    	case 'rev_contabilizar_rec':
    		$servicioIntegracionCXPRD = new ServicioIntegracionCXPRD();
    		echo $servicioIntegracionCXPRD->procesoRevContabilizarREC($objetoJson);
    		unset($servicioIntegracionCXPRD);
    		break;
    	
    	case 'buscar_por_anular_rec':
    		$servicioIntegracionCXPRD = new ServicioIntegracionCXPRD();
    		$resultado = $servicioIntegracionCXPRD->buscarRecepcionIntegrar($objetoJson->numrecdoc, $objetoJson->fecreg, $objetoJson->fecapr,
    																	  $objetoJson->tipo, $objetoJson->codigo, 'C', 'ANULAR');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPRD);
    		break;
    		
    	case 'anular_rec':
    		$servicioIntegracionCXPRD = new ServicioIntegracionCXPRD();
    		echo $servicioIntegracionCXPRD->procesoAnularREC($objetoJson);
    		unset($servicioIntegracionCXPRD);
    		break;
		
    	case 'buscar_por_rev_anulacion_rec':
    		$servicioIntegracionCXPRD = new ServicioIntegracionCXPRD();
    		$resultado = $servicioIntegracionCXPRD->buscarRecepcionIntegrar($objetoJson->numrecdoc, $objetoJson->fecreg, $objetoJson->fecapr,
    																	  $objetoJson->tipo, $objetoJson->codigo, 'A');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionCXPRD);
    		break;
    		
    	case 'rev_anular_rec':
    		$servicioIntegracionCXPRD = new ServicioIntegracionCXPRD();
    		echo $servicioIntegracionCXPRD->procesoRevAnularREC($objetoJson);
    		unset($servicioIntegracionCXPRD);
    		break;
    }
}
?>