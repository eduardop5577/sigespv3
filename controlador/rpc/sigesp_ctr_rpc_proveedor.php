<?php
/***********************************************************************************
* @fecha de modificacion: 01/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();
$datosempresa=$_SESSION["la_empresa"];
$dirctrrpc = "";
$dirctrrpc = dirname(__FILE__);
$dirctrrpc = str_replace("\\","/",$dirctrrpc); 
$dirctrrpc = str_replace("/controlador/rpc","",$dirctrrpc);
require_once ($dirctrrpc."/base/librerias/php/general/Json.php");
require_once ($dirctrrpc."/modelo/servicio/rpc/sigesp_srv_rpc_proveedor.php");

if ($_POST['ObjSon'])
{
    $submit = str_replace("\\", "", $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    switch ($objetoJson->operacion)
	{
    	case "buscarcodigo":
    		$servicioProveedores = new servicioProveedor();
    		$codigo = $servicioProveedores->buscarCodigoProveedor($datosempresa["codemp"]);
    		echo $codigo;
    		unset($servicioProveedores);
    		break;
    	
    	case "catalogo":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarProveedor($datosempresa["codemp"],$objetoJson->codi_pro,$objetoJson->nombpro,$objetoJson->direcpro,$objetoJson->rifprov,$objetoJson->fecdes,$objetoJson->fechas);
    		$ObjSon    = generarJson($resultado,true,false);
			echo $ObjSon;
			unset($servicioProveedores);
    		break;
    	
    	case "incluir":
    		$servicioProveedores = new servicioProveedor();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
			$arrevento ['evento']  = "INSERT";
			$arrevento ['nomven']  = "sigesp_vis_rpc_proveedor.html";
			$arrevento ['desevetra'] = "Inserto el Proveedor con codigo ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
			$objetoJson->nompro=str_replace('__**__','&',$objetoJson->nompro);
			$valido = $servicioProveedores->guardarProveedor($datosempresa["codemp"],$objetoJson,$arrevento);
			$resultado['mensaje'] = $servicioProveedores->mensaje;
			$resultado['valido']  = $valido;
			echo json_encode(array('raiz'=>$resultado));
    		unset($servicioProveedores);
    		break;
    	
    	case "actualizar":
    		$servicioProveedores = new servicioProveedor();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
			$arrevento ['evento']  = "UPDATE";
			$arrevento ['nomven']  = "sigesp_vis_rpc_proveedor.html";
			$arrevento ['desevetra'] = "Modifico el Proveedor con codigo ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
			$objetoJson->nompro=str_replace('__**__','&',$objetoJson->nompro);
    		$valido = $servicioProveedores->modificarProveedor($datosempresa["codemp"],$objetoJson,$arrevento);
    		$resultado['mensaje'] = $servicioProveedores->mensaje;
			$resultado['valido']  = $valido;
			echo json_encode(array('raiz'=>$resultado));
    		unset($servicioProveedores);
    		break;
    	
    	case "eliminar":
    		$servicioProveedores = new servicioProveedor();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
			$arrevento ['evento']  = "DELETE";
			$arrevento ['nomven']  = "sigesp_vis_rpc_proveedor.html";
			$arrevento ['desevetra'] = "Eliminó el proveedor ".$objetoJson->codpro.", asociado a la empresa ".$datosempresa["codemp"];
    		$resultado = $servicioProveedores->eliminarProveedor($datosempresa["codemp"],$objetoJson,$arrevento);
    		if ($resultado === 1) {
    			echo "1";
    		}
    		else if ($resultado === 2){
    			echo "2";
    		}
    		else{
    			echo "0";
    		}
    		unset($servicioDocumentos);
    		break;
			
		case "catalogocombotipoorg":
    		$servicioProveedores =  new servicioProveedor();
    		$dataEmp = $servicioProveedores->buscarCodigoOrganizacion($_SESSION["codemp"]);

    		unset($servicioProveedores);
    		echo generarJson($dataEmp);
    		break;
			
		case "catalogocombobanco":
    		$servicioProveedores =  new servicioProveedor();
    		$dataBan = $servicioProveedores->buscarBancos($_SESSION["codemp"]);

    		unset($servicioProveedores);
    		echo generarJson($dataBan);
    		break;
		
		case "catalogocombomoneda":
    		$servicioProveedores =  new servicioProveedor();
    		$dataMon = $servicioProveedores->buscarMonedas($_SESSION["codemp"]);

    		unset($servicioProveedores);
    		echo generarJson($dataMon);
    		break;
			
		case "catalogo_bansig":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarBancoSigecof();

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    		break;
		
		case "catalogo_documentos":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarDocumentosProv($objetoJson->cod_pro);

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    		break;
		
		case "catalogo_calificacion":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarCalificProv($objetoJson->cod_pro);

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    		break;
			
		case "catalogo_niv_clasif":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarNivelClasif();

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    		break;
			
		case "catalogo_ctacontpag":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarCtaConPag($datosempresa["codemp"],$objetoJson->sc_ccuenta,$objetoJson->d_denominacion);

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    		break;
		
		case "catalogo_ctacontant":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarCtaConAnt($datosempresa["codemp"],$objetoJson->sc_ctaant,$objetoJson->d_deno);

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    		break;
		
		case "catalogo_ctacontrec":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarCtaConRec($datosempresa["codemp"],$objetoJson->sc_ccuentarecdoc,$objetoJson->d_denominacion_rec);

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    		break;

		case "catalogocombopais":
    		$servicioProveedores =  new servicioProveedor();
    		$dataPais = $servicioProveedores->buscarPais();

    		unset($servicioProveedores);
    		echo generarJson($dataPais);
    		break;
		
		case 'catalogocomboestado':
			$servicioProveedores =  new servicioProveedor();
			$restriccion[0][0]= 'codpai';
			$restriccion[0][1]= '=';
			$restriccion[0][2]= $objetoJson->codpai;
			$restriccion[0][3]= 2;
			
			$dataEstado = $servicioProveedores->buscarEstado($restriccion);
			unset($servicioProveedores);
    		echo generarJson($dataEstado);
    		break;
			
		case 'catalogocombomuni':
			$servicioProveedores =  new servicioProveedor();
			$restriccion[0][0]= 'codpai';
			$restriccion[0][1]= '=';
			$restriccion[0][2]= $objetoJson->codpai;
			$restriccion[0][3]= 0;
			$restriccion[1][0]= 'codest';
			$restriccion[1][1]= '=';
			$restriccion[1][2]= $objetoJson->codest;
			$restriccion[1][3]= 2;
			
			$dataMunicipio = $servicioProveedores->buscarMunicipio($restriccion);
			unset($servicioProveedores);
    		echo generarJson($dataMunicipio);
    		break;
			
		case 'catalogocomboparroquia':
		
			$servicioProveedores =  new servicioProveedor();
			$restriccion[0][0]= 'codpai';
			$restriccion[0][1]= '=';
			$restriccion[0][2]= $objetoJson->codpai;
			$restriccion[0][3]= 0;
			$restriccion[1][0]= 'codest';
			$restriccion[1][1]= '=';
			$restriccion[1][2]= $objetoJson->codest;
			$restriccion[1][3]= 0;
			$restriccion[2][0]= 'codmun';
			$restriccion[2][1]= '=';
			$restriccion[2][2]= $objetoJson->codmun;
			$restriccion[2][3]= 2;
			
			$dataParroquia = $servicioProveedores->buscarParroquia($restriccion);
			unset($servicioProveedores);
    		echo generarJson($dataParroquia);
    		break;
			
		case "catalogo_proveedor":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarProveedores($objetoJson->mcod_pro,$objetoJson->mnompro,$objetoJson->mdirpro);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    	break;
		
		case "incluir_socio":
			$servicioProveedores = new servicioProveedor();
			$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
			$arrevento ['evento']  = "INSERT";
			$arrevento ['nomven']  = "sigesp_vis_rpc_proveedor.html";
			$arrevento ['desevetra'] = "Inserto el Socio de cedula ".$objetoJson->cedsocio.", del proveedor ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
			$resultado = $servicioProveedores->guardarProveedorSocios($datosempresa["codemp"],$objetoJson,$arrevento);
			if ($resultado === 1) {
				echo "1";
			}
			else{
				echo "0";
			}
			unset($servicioProveedores);
		break;
		
		case "actualizar_socio":
    		$servicioProveedores = new servicioProveedor();
			$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
    		$arrevento ['evento']  = "UPDATE";
    		$arrevento ['nomfisico'] = "sigesp_vis_rpc_proveedor.html";
    		$arrevento ['desevetra'] = "Modificó el Socio de cedula ".$objetoJson->cedsocio.", del proveedor ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
    		$resultado = $servicioProveedores->modificarProveedorSocios($datosempresa["codemp"],$objetoJson,$arrevento);
    		if ($resultado === 1) {
    			echo "2";
    		}
    		else{
    			echo "0";
    		}
    		unset($servicioProveedores);
    	break;
		
		case "buscarSocios":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarSocios($objetoJson->cod_pro);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    		break;
			
		case "eliminar_socio":
    		$servicioProveedores = new servicioProveedor();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
			$arrevento ['evento']  = "DELETE";
			$arrevento ['nomven']  = "sigesp_vis_rpc_proveedor.html";
			$arrevento ['desevetra'] = "Eliminó el Socio de cedula ".$objetoJson->cedsocio.", del proveedor ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
    		$resultado = $servicioProveedores->eliminarProveedorSocios($datosempresa["codemp"],$objetoJson,$arrevento);
    		if ($resultado === 1) {
    			echo "1";
    		}
    		else{
    			echo "0";
    		}
    		unset($servicioProveedores);
    		break;
			
		case "incluir_documento_pro":
			$servicioProveedores = new servicioProveedor();
			$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
			$arrevento ['evento']  = "INSERT";
			$arrevento ['nomven']  = "sigesp_vis_rpc_proveedor.html";
			$arrevento ['desevetra'] = "Inserto el Documento ".$objetoJson->coddoc.", del proveedor ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
			$resultado = $servicioProveedores->guardarProveedorDocumentos($datosempresa["codemp"],$objetoJson,$arrevento);
			if ($resultado === 1) {
				echo "1";
			}
			else{
				echo "0";
			}
			unset($servicioProveedores);
		break;
		
		case "actualizar_documento_pro":
    		$servicioProveedores = new servicioProveedor();
			$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
    		$arrevento ['evento']  = "UPDATE";
    		$arrevento ['nomfisico'] = "sigesp_vis_rpc_proveedor.html";
    		$arrevento ['desevetra'] = "Modificó el documento ".$objetoJson->coddoc.", del proveedor ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
    		$resultado = $servicioProveedores->modificarProveedorDocumentos($datosempresa["codemp"],$objetoJson,$arrevento);
    		if ($resultado === 1) {
    			echo "2";
    		}
    		else{
    			echo "0";
    		}
    		unset($servicioProveedores);
    	break;
		
		case "buscarDocumentos":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarProveedorDoc($objetoJson->cod_pro);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    	break;
		
		case "eliminar_documento":
    		$servicioProveedores = new servicioProveedor();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
			$arrevento ['evento']  = "DELETE";
			$arrevento ['nomven']  = "sigesp_vis_rpc_proveedor.html";
			$arrevento ['desevetra'] = "Eliminó el documento ".$objetoJson->coddoc.", del proveedor ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
    		$resultado = $servicioProveedores->eliminarProveedorDocumentos($datosempresa["codemp"],$objetoJson,$arrevento);
    		if ($resultado === 1) {
    			echo "1";
    		}
    		else{
    			echo "0";
    		}
    		unset($servicioProveedores);
    	break;
		
		case "incluir_calif_pro":
			$servicioProveedores = new servicioProveedor();
			$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
			$arrevento ['evento']  = "INSERT";
			$arrevento ['nomven']  = "sigesp_vis_rpc_proveedor.html";
			$arrevento ['desevetra'] = "Inserto la calificación ".$objetoJson->codclas.", del proveedor ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
			$resultado = $servicioProveedores->guardarProveedorCalificacion($datosempresa["codemp"],$objetoJson,$arrevento);
			if ($resultado === 1) {
				echo "1";
			}
			else{
				echo "0";
			}
			unset($servicioProveedores);
		break;
		
		case "actualizar_calif_pro":
    		$servicioProveedores = new servicioProveedor();
			$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
    		$arrevento ['evento']  = "UPDATE";
    		$arrevento ['nomfisico'] = "sigesp_vis_rpc_proveedor.html";
    		$arrevento ['desevetra'] = "Modificó la calificación ".$objetoJson->codclas.", del proveedor ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
    		$resultado = $servicioProveedores->modificarProveedorCalificacion($datosempresa["codemp"],$objetoJson,$arrevento);
    		if ($resultado === 1) {
    			echo "2";
    		}
    		else{
    			echo "0";
    		}
    		unset($servicioProveedores);
    	break;
		
		case "buscarCalificacion":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarProveedorCla($objetoJson->cod_pro);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    	break;
		
		case "eliminar_calificacion":
    		$servicioProveedores = new servicioProveedor();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "RPC";
			$arrevento ['evento']  = "DELETE";
			$arrevento ['nomven']  = "sigesp_vis_rpc_proveedor.html";
			$arrevento ['desevetra'] = "Eliminó la calificación ".$objetoJson->codclas.", del proveedor ".$objetoJson->cod_pro.", asociado a la empresa ".$datosempresa["codemp"];
    		$resultado = $servicioProveedores->eliminarProveedorCalif($datosempresa["codemp"],$objetoJson,$arrevento);
    		if ($resultado === 1) {
    			echo "1";
    		}
    		else{
    			echo "0";
    		}
    		unset($servicioProveedores);
    	break;
		
		case "espc_prov":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarProveedorEspecialidades($objetoJson->cod_pro);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    	break;
		
		case "buscarEspecialidadesDisp":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarProveedorEspecialidadesDisp($objetoJson->cod_pro);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    	break;
		
		case 'guardar_espxprov':
    		$servicioProveedores = new servicioProveedor();
    		$arrevento ['codemp']  = $datosempresa['codemp'];
			$arrevento ['codusu']  = $_SESSION['la_logusr'];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = 'RPC';
			$arrevento ['evento']  = 'INSERT';
			$arrevento ['nomven']  = 'sigesp_vis_rpc_proveedor.html';
			$arrevento ['desevetra'] = '';
			echo $servicioProveedores->guardarProveedorEspecialidades($datosempresa['codemp'], $objetoJson, $objetoJson->cod_pro, $arrevento);
    		unset($servicioProveedores);
    		break;
			
		case "deduc_prov":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarProveedorDeducciones($objetoJson->cod_pro);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    	break;
		
		case "buscarDeduccionesDisp":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarProveedorDeduccionesDisp($objetoJson->cod_pro);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    	break;
		
		case 'guardar_dedxprov':
    		$servicioProveedores = new servicioProveedor();
    		$arrevento ['codemp']  = $datosempresa['codemp'];
			$arrevento ['codusu']  = $_SESSION['la_logusr'];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = 'RPC';
			$arrevento ['evento']  = 'INSERT';
			$arrevento ['nomven']  = 'sigesp_vis_rpc_proveedor.html';
			$arrevento ['desevetra'] = '';
			echo $servicioProveedores->guardarProveedorDeducciones($datosempresa['codemp'], $objetoJson, $objetoJson->cod_pro, $arrevento);
    		unset($servicioProveedores);
    		break;
			
		case "denom_estado":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarDenomEstado($objetoJson->codpai);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    	break;
		
		case "denom_municipio":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarDenomMunicipio($objetoJson->codpai,$objetoJson->codest);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    	break;
		
		case "denom_parroquia":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarDenomParroquia($objetoJson->codpai,$objetoJson->codest,$objetoJson->codmun);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioProveedores);
    	break;
		
		case "verificar_rif":
    		$servicioProveedores = new servicioProveedor();
    		$resultado = $servicioProveedores->buscarRifProv($objetoJson->rifpro,$objetoJson->seniat);
			echo json_encode(array('raiz'=>$resultado));
			unset($servicioProveedores);
    	break;
 
    }
}