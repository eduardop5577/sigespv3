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
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionspi.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion) 
    {
    	case 'buscar_cmpspi':
    		$servicioIntegracionSpi = new servicioIntegracionSPI();
    		echo generarJson($servicioIntegracionSpi->buscarCmpSpi($objetoJson->codcmp,$objetoJson->procede,$objetoJson->fecha,$objetoJson->estatus));
			unset($servicioIntegracionSpi);
    	break; 

    	case 'buscar_detspi':
    		$servicioIntegracionSpi = new servicioIntegracionSPI();
    		echo generarJsonArreglo($servicioIntegracionSpi->buscarDetalleSpi($objetoJson->codcom,$objetoJson->procede));
			unset($servicioIntegracionSpi);
    	break;
    	
    	case 'buscar_detscg':
    		$servicioIntegracionSpi = new servicioIntegracionSPI();
    		echo generarJsonArreglo($servicioIntegracionSpi->buscarDetalleScg($objetoJson->codcom,$objetoJson->procede));
    		unset($servicioIntegracionSpi);
    	break;
    	
    	case 'contabilizar_spi':
    		$servicioIntegracionSpi = new servicioIntegracionSPI();
    		echo $servicioIntegracionSpi->ContabilizarSPI($objetoJson);
    	break; 
    	
    	case 'rev_contabilizar_spi':
    		$servicioIntegracionSpi = new servicioIntegracionSPI();
    		echo $servicioIntegracionSpi->RevContabilizarSPI($objetoJson);
    	break;
    }
}

?>