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
    require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionspg.php');

    $_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    switch ($objetoJson->operacion)
    {
    	case 'buscar_por_aprobar':
    		$servicioIntegracionSPG = new ServicioIntegracionSPG();
    		echo generarJson($servicioIntegracionSPG->buscarModificaciones($objetoJson->numcom, $objetoJson->procede, $objetoJson->fecmov, '0'));
    		unset($servicioIntegracionSPG);
    	break;

    	case 'buscar_por_reversar':
    		$servicioIntegracionSPG = new ServicioIntegracionSPG();
    		echo generarJson($servicioIntegracionSPG->buscarModificaciones($objetoJson->numcom, $objetoJson->procede, $objetoJson->fecmov, '1'));
    		unset($servicioIntegracionSPG);
    	break;
		
    	case 'detalle_presupuesto':
    		$servicioIntegracionSPG = new ServicioIntegracionSPG();
    		echo generarJsonArreglo($servicioIntegracionSPG->obtenerDetallePresupuestoDisponibilidad($objetoJson->numcom, $objetoJson->procede));
    		unset($servicioIntegracionSPG);
    	break;
    	   
    	case 'detalle_contable':
    		$servicioIntegracionSPG = new ServicioIntegracionSPG();
    		echo generarJson($servicioIntegracionSPG->detalleContable($objetoJson->numcom,  $objetoJson->procede));
    		unset($servicioIntegracionSPG);
    	break;
    	   	
    	case 'contabilizar_spg':
    		$servicioIntegracionSpg = new ServicioIntegracionSPG();
    		echo $servicioIntegracionSpg->ContabilizarSPG($objetoJson);
    	break; 

    	case 'rev_contabilizar_spg':
    		$servicioIntegracionSpg = new ServicioIntegracionSPG();
    		echo $servicioIntegracionSpg->RevContabilizarSPG($objetoJson);
    	break; 

    	case 'catalogo_compromisos':
    		$servicioIntegracionSPG = new ServicioIntegracionSPG();
    		echo generarJsonArreglo($servicioIntegracionSPG->buscarCompromisos($objetoJson->sistema,$objetoJson->mcomprobante,$objetoJson->mfecdes,$objetoJson->mfechas,$objetoJson->mcod_pro));
    		unset($servicioIntegracionSPG);
    	break;

        case 'buscar_cierre_compromiso':
    		$servicioIntegracionSPG = new ServicioIntegracionSPG();
    		echo generarJsonArreglo($servicioIntegracionSPG->buscarCompromisosCausadosParciales($objetoJson->numcom, $objetoJson->sistema));
    		unset($servicioIntegracionSPG);
    	break;
		    	   	
    	case 'contabilizar_cierre_compromisos':
    		$servicioIntegracionSpg = new ServicioIntegracionSPG();
    		echo $servicioIntegracionSpg->ContabilizarCierreCompromisos($objetoJson);
    	break; 

    	case 'catalogo_cierredisminuciones':
    		$servicioIntegracionSPG = new ServicioIntegracionSPG();
    		echo generarJsonArreglo($servicioIntegracionSPG->buscarCierreDisminuciones($objetoJson->sistema,$objetoJson->mcomprobante,$objetoJson->mfecdes,$objetoJson->mfechas,$objetoJson->mcod_pro));
    		unset($servicioIntegracionSPG);
    	break;

        case 'buscar_rev_cierre_compromiso':
    		$servicioIntegracionSPG = new ServicioIntegracionSPG();
    		echo generarJsonArreglo($servicioIntegracionSPG->buscarReversoCierreCompromiso($objetoJson->numcom, $objetoJson->sistema));
    		unset($servicioIntegracionSPG);
    	break;

    	case 'rev_contabilizar_cierre_compromisos':
    		$servicioIntegracionSpg = new ServicioIntegracionSPG();
    		echo $servicioIntegracionSpg->RevContabilizarCierre($objetoJson);
    	break; 
    }
}
?>