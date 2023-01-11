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
if (($_POST['ObjSon']) && ($sessionvalida)) {
	$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
	require_once ($dirsrv.'/base/librerias/php/general/Json.php');
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionsfccxc.php');
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionsfcpag.php');
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion){
    	/*************************************
    	 * INTEGRACION CUENTAS POR PAGAR
    	 *************************************/
    	case 'buscar_contabilizar_cxc':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarCuentasCobrarIntegrar($objetoJson->comprobante, 'SFCFAC', $objetoJson->fecha, '0');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;
    	
    	case 'comprobante_detalle_spicxc':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarDetalleComprobanteCXCSPI($objetoJson->comprobante, $objetoJson->procede, $objetoJson->fecha);
    		echo generarJson($resultado);
    		unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;

    	case 'comprobante_detalle_scgcxc':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarDetalleComprobanteCXCSCG($objetoJson->comprobante, $objetoJson->procede, $objetoJson->fecha);
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;
    	 	
    	case 'contabilizar_cxc':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		echo $servicioIntegracionSFCCXC->procesoContabilizarCXC($objetoJson);
    		unset($servicioIntegracionSFCCXC);
    		break;

    	case 'buscar_reversar_cxc':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarCuentasCobrarIntegrar($objetoJson->comprobante, $objetoJson->procede, $objetoJson->fecha, '1');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;
    	
    	case 'rev_contabilizacion_cxc':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		echo $servicioIntegracionSFCCXC->procesoRevContabilizarCXC($objetoJson);
    		unset($servicioIntegracionSFCCXC);
       		break;    	
    	/*************************************
    	 * INTEGRACION PAGOS
    	 *************************************/
       	case 'buscar_contabilizar_pag':
    		$servicioIntegracionSFCPAG = new ServicioIntegracionSFCPAG();
    		$resultado = $servicioIntegracionSFCPAG->buscarPagosIntegrar($objetoJson->comprobante, 'SFCREC', $objetoJson->fecha, '0');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCPAG);
    		break;
    		
    	case 'comprobante_detalle_spipag':
    		$servicioIntegracionSFCPAG = new ServicioIntegracionSFCPAG();
    		$resultado = $servicioIntegracionSFCPAG->buscarDetalleComprobantePAGSPI($objetoJson->comprobante, $objetoJson->procede, 
    																				$objetoJson->fecha,$objetoJson->codban,$objetoJson->ctaban,$objetoJson->numdoc);
			echo generarJson($resultado);
    		unset($resultado);
			unset($servicioIntegracionSFCPAG);
    		break;

    	case 'comprobante_detalle_scgpag':
    		$servicioIntegracionSFCPAG = new ServicioIntegracionSFCPAG();
    		$resultado = $servicioIntegracionSFCPAG->buscarDetalleComprobantePAGSCG($objetoJson->comprobante, $objetoJson->procede, 
    																				$objetoJson->fecha,$objetoJson->codban,$objetoJson->ctaban,$objetoJson->numdoc);
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCPAG);
    		break;
    	
    	case 'contabilizar_pag':
    		$servicioIntegracionSFCPAG = new ServicioIntegracionSFCPAG();
    		echo $servicioIntegracionSFCPAG->procesoContabilizarPAG($objetoJson);
    		unset($servicioIntegracionSFCPAG);
    		break;
    		
    	case 'buscar_por_reversar_pag':
    		$servicioIntegracionSFCPAG = new ServicioIntegracionSFCPAG();
    		$resultado = $servicioIntegracionSFCPAG->buscarPagosIntegrar($objetoJson->comprobante, 'SFCREC', $objetoJson->fecha, '1');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCPAG);
    		break;
    	
    	case 'rev_contabilizar_pag':
    		$servicioIntegracionSFCPAG = new ServicioIntegracionSFCPAG();
    		echo $servicioIntegracionSFCPAG->procesoRevContabilizarPAG($objetoJson);
    		unset($servicioIntegracionSFCPAG);
    		break;
    		
    	/*************************************
    	 * INTEGRACION DEDUCCIONES
    	 *************************************/
    	case 'buscar_contabilizar_ded':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarCuentasCobrarIntegrar($objetoJson->comprobante, 'SFCDED', $objetoJson->fecha, '0');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;
    	
    	case 'comprobante_detalle_spided':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarDetalleComprobanteCXCSPI($objetoJson->comprobante, $objetoJson->procede, $objetoJson->fecha);
    		echo generarJson($resultado);
    		unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;

    	case 'comprobante_detalle_scgded':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarDetalleComprobanteCXCSCG($objetoJson->comprobante, $objetoJson->procede, $objetoJson->fecha);
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;
    	 	
    	case 'contabilizar_ded':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		echo $servicioIntegracionSFCCXC->procesoContabilizarCXC($objetoJson);
    		unset($servicioIntegracionSFCCXC);
    		break;

    	case 'buscar_reversar_ded':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarCuentasCobrarIntegrar($objetoJson->comprobante, 'SFCDED', $objetoJson->fecha, '1');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;
    	
    	case 'rev_contabilizacion_ded':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		echo $servicioIntegracionSFCCXC->procesoRevContabilizarCXC($objetoJson);
    		unset($servicioIntegracionSFCCXC);
       		break;
       		
		/*************************************
    	 * INTEGRACION NOTAS CREDITO/DEBITO
    	 *************************************/
    	case 'buscar_contabilizar_ncd':
    		$procede = '';
    		if ($objetoJson->documento=='C') 
			{
    			$procede='SFCNCR';
    		}
    		elseif ($objetoJson->documento=='D')
			{
    			$procede='SFCNDR';
    		}
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarCuentasCobrarIntegrar($objetoJson->comprobante, $procede, $objetoJson->fecha, '0');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;
    	
    	case 'comprobante_detalle_spincd':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarDetalleComprobanteCXCSPI($objetoJson->comprobante, $objetoJson->procede, $objetoJson->fecha);
    		echo generarJson($resultado);
    		unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;

    	case 'comprobante_detalle_scgncd':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarDetalleComprobanteCXCSCG($objetoJson->comprobante, $objetoJson->procede, $objetoJson->fecha);
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;
    	 	
    	case 'contabilizar_ncd':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		echo $servicioIntegracionSFCCXC->procesoContabilizarCXC($objetoJson);
    		unset($servicioIntegracionSFCCXC);
    		break;

    	case 'buscar_reversar_ncd':
    		$procede = '';
    		if ($objetoJson->documento=='C') {
    			$procede='SFCNCR';
    		}
    		elseif ($objetoJson->documento=='D'){
    			$procede='SFCNDR';
    		}
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		$resultado = $servicioIntegracionSFCCXC->buscarCuentasCobrarIntegrar($objetoJson->comprobante, $procede, $objetoJson->fecha, '1');
    		echo generarJson($resultado);
			unset($resultado);
			unset($servicioIntegracionSFCCXC);
    		break;
    	
    	case 'rev_contabilizacion_ded':
    		$servicioIntegracionSFCCXC = new ServicioIntegracionSFCCXC();
    		echo $servicioIntegracionSFCCXC->procesoRevContabilizarCXC($objetoJson);
    		unset($servicioIntegracionSFCCXC);
       		break;       
	}
}
?>