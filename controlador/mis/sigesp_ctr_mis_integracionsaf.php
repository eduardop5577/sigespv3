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
	require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_integracionsaf.php');
	
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion) 
    {
    	case 'buscar_por_contabilizar_depsaf':
			$servicioIntegracionSAF = new servicioIntegracionSAF();
			echo generarJsonArreglo($servicioIntegracionSAF->buscarContabilizarDepSaf($objetoJson->mes,$objetoJson->anio,$objetoJson->estatus));
			unset($servicioIntegracionSAF);
		break; 
    	
    	case 'buscar_por_rev_contabilizar_depsaf':
    		$servicioIntegracionSAF = new servicioIntegracionSAF();
			echo generarJsonArreglo($servicioIntegracionSAF->buscarRevContabilizacionDepSaf($objetoJson->mes,$objetoJson->anio,$objetoJson->estatus));
			unset($servicioIntegracionSCB);
    	break;       

    	case 'buscar_por_contabilizar_dessaf':
			$servicioIntegracionSAF = new servicioIntegracionSAF();
			echo generarJsonArreglo($servicioIntegracionSAF->buscarContabilizarDesSaf($objetoJson->numcmp,$objetoJson->feccmp,$objetoJson->estatus));
			unset($servicioIntegracionSAF);
		break; 
    	
    	case 'buscar_por_rev_contabilizar_dessaf':
    		$servicioIntegracionSAF = new servicioIntegracionSAF();
			echo generarJsonArreglo($servicioIntegracionSAF->buscarRevContabilizacionDesSaf($objetoJson->numcmp,$objetoJson->feccmp,$objetoJson->estatus));
			unset($servicioIntegracionSCB);
    	break; 
   
		case 'contabilizar_safdep':
    		$servicioIntegracionSAF = new servicioIntegracionSAF();
    		echo $servicioIntegracionSAF->procesoContabilizarDepSaf($objetoJson);
    	break;
		
		case 'rev_contabilizar_safdep':
    		$servicioIntegracionSAF = new servicioIntegracionSAF();
    		echo $servicioIntegracionSAF->procesoRevContabilizarDepSaf($objetoJson);
    	break;
		
		case 'contabilizar_safdes':
    		$servicioIntegracionSAF = new servicioIntegracionSAF();
    		echo $servicioIntegracionSAF->procesoContabilizarDesSaf($objetoJson);
    	break;  

		case 'rev_contabilizar_safdes':
    		$servicioIntegracionSAF = new servicioIntegracionSAF();
    		echo $servicioIntegracionSAF->procesoRevContabilizarDesSaf($objetoJson);
    	break;
		
		case 'buscar_detalles_gasto_ing':
    		$servicioIntegracionSAF = new servicioIntegracionSAF();
    		echo generarJsonArreglo($servicioIntegracionSAF->buscarInformacionDetalle($objetoJson->anio, $objetoJson->mes));
			unset($servicioIntegracionSAF);
    	break; 
		
		case 'buscar_detalles_contable':
    		$servicioIntegracionSAF = new servicioIntegracionSAF();
    		echo generarJson($servicioIntegracionSAF->detalleContable($objetoJson->anio, $objetoJson->mes));
			unset($servicioIntegracionSAF);
    	break;
		
		case 'buscar_detalles_contable_des':
    		$servicioIntegracionSAF = new servicioIntegracionSAF();
    		echo generarJson($servicioIntegracionSAF->detalleContableDes($objetoJson->comp,$objetoJson->fecha));
			unset($servicioIntegracionSAF);
    	break; 
		
		}
    }
?>