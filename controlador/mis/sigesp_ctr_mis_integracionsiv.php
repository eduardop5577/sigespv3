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
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionsiv.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion) {
    	/****************************
    	 * INTEGRACION DE DESPACHOS
    	 ****************************/
    	case 'validarDespacho':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		if($servicioIntegracionSIV->validarConfiguracion('SIV','CONFIG','CONTA DESPACHO'))
			{
    			echo '1';
    		}
    		else
			{
    			echo '0';
    		}
    		unset($servicioIntegracionSIV);
    		break;
    	
    	case 'despacho_por_contabilizar':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarDespachos($objetoJson->numorddes, $objetoJson->fecdes, '0'));
    		unset($servicioIntegracionSIV);
    		break;

    	case 'despachos_por_reversar':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarDespachos($objetoJson->numorddes, $objetoJson->fecdes, '1'));
    		unset($servicioIntegracionSIV);
    		break;
		
    	case 'despacho_detalle_presupuesto':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJsonArreglo($servicioIntegracionSIV->obtenerDetPreDespachoDisponibilidad($objetoJson->numorddes));
    		unset($servicioIntegracionSIV);
    		break;
    	   
    	case 'despacho_detalle_contable':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarDetalleContableDespacho($objetoJson->numorddes));
    		unset($servicioIntegracionSIV);
    		break;
    	   	
    	case 'contabilizar_despacho':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo $servicioIntegracionSIV->procesoContabilizarDespachos($objetoJson);
    		unset($servicioIntegracionSIV);
    		break;

    	case 'reversar_despacho':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo $servicioIntegracionSIV->procesorRevContabilizarDespachos($objetoJson);
    		unset($servicioIntegracionSIV);
    		break;
    	
    	/********************************
    	 * INTEGRACION DE TRANSFERENCIAS
    	 ********************************/
    	case 'validarTransferencia':
			$valor =$_SESSION['la_empresa']['estcencos'];
			if($valor == '0')
			{
				$servicioIntegracionSIV = new ServicioIntegracionSIV();
				$valor =$servicioIntegracionSIV->validarConfiguracion('SIV','CONFIG','PRODUCCION');
				if($valor)
				{
					$valor= '1';
				}
				else
				{
					$valor =$servicioIntegracionSIV->validarConfiguracion('SIV','CONFIG','MERCADO');
					if($valor)
					{
						$valor= '1';
					}
					else
					{
						$valor= '0';
					}
				}
				unset($servicioIntegracionSIV);
			}
    		echo $valor;
    		break;	
    		
    	case 'transferencias_por_contabilizar':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarTransferencias($objetoJson->numtra, $objetoJson->fecemi, '0'));
    		unset($servicioIntegracionSIV);
    		break;

    	case 'transferencias_por_reversar':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarTransferencias($objetoJson->numtra, $objetoJson->fecemi, '1'));
    		unset($servicioIntegracionSIV);
    		break;
		
    	case 'transferencia_detalle_contable':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarDetalleContableTransferencia($objetoJson->numtra, $objetoJson->fecemi));
    		unset($servicioIntegracionSIV);
    		break;
    	   	
    	case 'contabilizar_transferencia':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo $servicioIntegracionSIV->procesoContabilizarTransferencias($objetoJson);
    		unset($servicioIntegracionSIV);
    		break;

    	case 'reversar_transferencia':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo $servicioIntegracionSIV->procesoRevContabilizarTransferencias($objetoJson);
    		unset($servicioIntegracionSIV);
    		break;
    	
    	
    	/********************************
    	 * INTEGRACION DE EMPAQUETADO
    	 ********************************/    		
    	case 'empaquetado_por_contabilizar':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarEmpaquetado($objetoJson->codemppro, $objetoJson->fecemppro, '0'));
    		unset($servicioIntegracionSIV);
    		break;

    	case 'empaquetado_por_reversar':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarEmpaquetado($objetoJson->codemppro, $objetoJson->fecemppro, '1'));
    		unset($servicioIntegracionSIV);
    		break;
		
    	case 'empaquetado_detalle_contable':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarDetalleContableEmpaquetado($objetoJson->codemppro, $objetoJson->fecemppro));
    		unset($servicioIntegracionSIV);
    		break;
    	   	
    	case 'contabilizar_empaquetado':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo $servicioIntegracionSIV->procesoContabilizarEmpaquetado($objetoJson);
    		unset($servicioIntegracionSIV);
    		break;

    	case 'reversar_empaquetado':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo $servicioIntegracionSIV->procesoRevContabilizarEmpaquetado($objetoJson);
    		unset($servicioIntegracionSIV);
    		break;
    	
    	
    	/********************************
    	 * INTEGRACION DE PRODUCCION
    	 ********************************/    		
    	case 'produccion_por_contabilizar':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarProduccion($objetoJson->numpro, $objetoJson->fecemi, '0'));
    		unset($servicioIntegracionSIV);
    		break;

    	case 'produccion_por_reversar':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarProduccion($objetoJson->numpro, $objetoJson->fecemi, '1'));
    		unset($servicioIntegracionSIV);
    		break;
		
    	case 'produccion_detalle_contable':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo generarJson($servicioIntegracionSIV->buscarDetalleContableProduccion($objetoJson->numpro, $objetoJson->fecemi));
    		unset($servicioIntegracionSIV);
    		break;
    	   	
    	case 'contabilizar_produccion':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo $servicioIntegracionSIV->procesoContabilizarProduccion($objetoJson);
    		unset($servicioIntegracionSIV);
    		break;

    	case 'reversar_produccion':
    		$servicioIntegracionSIV = new ServicioIntegracionSIV();
    		echo $servicioIntegracionSIV->procesoRevContabilizarProduccion($objetoJson);
    		unset($servicioIntegracionSIV);
    		break;
    }
}
?>