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
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionscb.php');
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionsob.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion) 
    {
    	case 'buscar_por_contabilizar_movbco':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		$resultado = $servicioIntegracionSCB->buscarMovBcoContabilizar($objetoJson->numdoc,$objetoJson->fecmov,$objetoJson->codope,$objetoJson->numcarord);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSCB);
    	break; 
    	
    	case 'verificar_config':
    		$Serviciosobasi = new ServicioIntegracionSOB();
    		echo $Serviciosobasi->verificarConfig($objetoJson->sistema,$objetoJson->seccion,$objetoJson->variable);
    		unset($Serviciosobasi);
    		break;
    	
    	case 'buscar_por_rev_contabilizacion_movbco':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		$resultado = $servicioIntegracionSCB->buscarMovBcoRevContabilizacion($objetoJson->numdoc,$objetoJson->fecmov,$objetoJson->codope,$objetoJson->numcarord);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSCB);
    	break;       
    	
    	case 'buscar_por_anular_movbco':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		$resultado = $servicioIntegracionSCB->buscarMovBcoAnular($objetoJson->numdoc,$objetoJson->fecmov,$objetoJson->codope,$objetoJson->numcarord);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSCB);
		break;
    	
    	case 'buscar_por_rev_anular_movbco':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		$resultado = $servicioIntegracionSCB->buscarMovBcoRevAnulacion($objetoJson->numdoc,$objetoJson->fecmov,$objetoJson->codope,$objetoJson->numcarord);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSCB);
		break;
		
		case 'buscar_contabilizacion_opd':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		$resultado = $servicioIntegracionSCB->buscarOpdContabilizar($objetoJson->numdoc,$objetoJson->fecmovcol);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSCB);
    	break;
		
		case 'buscar_rev_contabilizacion_opd':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		$resultado = $servicioIntegracionSCB->buscarRevOpdContabilizar($objetoJson->numdoc,$objetoJson->fecmovcol);
    		$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioIntegracionSCB);
    	break;
   
    	case 'contabilizar_movbco':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		echo $servicioIntegracionSCB->Contabilizar($objetoJson);
    	break; 

    	case 'rev_contabilizacion_movbco':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		echo $servicioIntegracionSCB->RevContabilizar($objetoJson);
       	break;    
       		
    	case 'anular_movbco':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		echo $servicioIntegracionSCB->AnularSCBMOV($objetoJson);
    	break; 
    	
    	case 'rev_anular_movbco':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		echo $servicioIntegracionSCB->RevAnularSCBMOV($objetoJson);
       	break;
       	
        case 'contabilizar_opd':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		echo $servicioIntegracionSCB->procesoConScbOpd($objetoJson);
    	break; 
    	
    	case 'rev_contabilizar_opd':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		echo $servicioIntegracionSCB->procesoRevConScbOpd($objetoJson);
       	break; 
		
		case 'buscar_detalles_gasto_ing':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		echo generarJsonArreglo($servicioIntegracionSCB->buscarInformacionDetalle($objetoJson->numdoc, $objetoJson->codban, $objetoJson->ctaban, $objetoJson->codope));
			unset($servicioIntegracionSCB);
    	break; 
		
		case 'buscar_detalles_contable':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		echo generarJson($servicioIntegracionSCB->detalleContable($objetoJson->numdoc, $objetoJson->codban, $objetoJson->ctaban, $objetoJson->codope));
			unset($servicioIntegracionSCB);
    	break; 
		
		case 'buscar_detalles_contable_movcol':
    		$servicioIntegracionSCB = new ServicioIntegracionSCB();
    		echo generarJson($servicioIntegracionSCB->detalleContableMovcol($objetoJson->numdoc, $objetoJson->codban, $objetoJson->ctaban, $objetoJson->codope));
			unset($servicioIntegracionSCB);
    	break; 
    }
}

?>