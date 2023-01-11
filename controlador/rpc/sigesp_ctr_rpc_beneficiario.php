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
require_once ($dirctrrpc."/modelo/servicio/rpc/sigesp_srv_rpc_beneficiario.php");
require_once ($dirctrrpc.'/base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();

if (($_POST['ObjSon']) && ($sessionvalida))
{
    $_SESSION['session_activa'] = time();
	$submit = str_replace("\\", "", $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    switch ($objetoJson->operacion)
    {			
    	case "buscarBeneficiarios":
    	    $servicioBeneficiarios = new servicioBeneficiario();
    	    $resultado = $servicioBeneficiarios->buscarBeneficiarios($objetoJson->cedulabene,$objetoJson->nombrebene,$objetoJson->apellidobene);
    	    $ObjSon    = generarJson($resultado);
    	    echo $ObjSon;
    	    unset($servicioBeneficiarios);
    	break;
    	     	    
		case "buscarbanco":
    	    $servicioBeneficiarios = new servicioBeneficiario();
    	   	echo generarJson($servicioBeneficiarios->buscarBanco($datosempresa['codemp']));
    	   	unset($servicioBeneficiarios);
    	break;

    	case "buscarCuenta":
    		$servicioBeneficiarios = new servicioBeneficiario();
    	   	echo generarJson($servicioBeneficiarios->buscarTipoCuenta($datosempresa['codemp']));
    	   	unset($servicioBeneficiarios);
    	break;
    	    	
    	case "incluir":
    		$servicioBeneficiarios = new servicioBeneficiario();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
    		$arrevento ['codusu']  = $_SESSION["la_logusr"];
    		$arrevento ['codsis']  = $objetoJson->codsis;
    		$arrevento ['evento']  = "INSERT";
            $arrevento['nomfisico'] = $objetoJson->nomven;    		
            $arrevento ['desevetra'] = "Inserto el beneficiario con codigo ".$objetoJson->ced_bene.", asociado a la empresa ".$datosempresa["codemp"];
    		$resultado = $servicioBeneficiarios->guardarBeneficiario($datosempresa["codemp"],$objetoJson,$arrevento);
    		if ($resultado === 1) {
    			echo "1";
    		}
    		else{
    			echo "0";
    		}
    		unset($servicioBeneficiarios);
    		break;
    		
    		case "actualizar":
    			$servicioBeneficiarios = new servicioBeneficiario();
    			$arrevento ['codemp']  = $datosempresa["codemp"];
    			$arrevento ['codusu']  = $_SESSION["la_logusr"];
    			$arrevento ['codsis']  = $objetoJson->codsis;
    			$arrevento ['evento']  = "UPDATE";
    			$arrevento ['nomfisico']  = $objetoJson->nomven; 
    			$arrevento ['desevetra'] = "Modifico el beneficiario con codigo ".$objetoJson->ced_bene.", asociado a la empresa ".$datosempresa["codemp"];
    			$resultado = $servicioBeneficiarios->modificarBeneficiario($datosempresa["codemp"],$objetoJson,$arrevento);
    			if ($resultado === 1) {
    				echo "1";
    			}
    			else{
    				echo "0";
    			}
    			unset($servicioBeneficiarios);
    			break;
    			 
    		case "eliminar":
    			$servicioBeneficiarios = new servicioBeneficiario();
    			$arrevento ['codemp']  = $datosempresa["codemp"];
    			$arrevento ['codusu']  = $_SESSION["la_logusr"];
    			$arrevento ['codsis']  = "RPC";
    			$arrevento ['evento']  = "DELETE";
    			$arrevento ['nomven']  = "sigesp_vis_rpc_beneficiario.html";
    			$arrevento ['desevetra'] = "Modifico el parametro de clasificacion con codigo ".$objetoJson->ced_bene.", asociado a la empresa ".$datosempresa["codemp"];
    			$resultado = $servicioBeneficiarios->eliminarBeneficiario($datosempresa["codemp"],$objetoJson,$arrevento);
    			if ($resultado === 1) {
    				echo "1";
    			}
	    		else if ($resultado === 2){
	    			echo "2";
	    		}
    			else{
    				echo "0";
    			}
    			unset($servicioParametro);
    			break;
    			
    		case "buscarDeduccionesBeneficiarios":
	    		$servicioBeneficiarios = new servicioBeneficiario();
	    		$resultado = $servicioBeneficiarios->buscarBeneficiarioDeduccionesDisp($objetoJson->ced_bene);
				$ObjSon    = generarJson($resultado);
				echo $ObjSon;
				unset($servicioBeneficiarios);
	    	break;
	    	
			case "buscarDeduccionesDisp":
				$servicioBeneficiarios = new servicioBeneficiario();
				$resultado = $servicioBeneficiarios->buscarBenDeduccionesDisp($objetoJson->ced_bene);
				$ObjSon    = generarJson($resultado);
				echo $ObjSon;
				unset($servicioBeneficiarios);
    		break;
			
			case 'guardar_dedxbene':
	    		$servicioBeneficiarios = new servicioBeneficiario();
	    		$arrevento ['codemp']  = $datosempresa['codemp'];
				$arrevento ['codusu']  = $_SESSION['la_logusr'];
				$arrevento ['codmenu'] = $objetoJson->codmenu;
				$arrevento ['codsis']  = 'RPC';
				$arrevento ['evento']  = 'INSERT';
				$arrevento ['nomven']  = 'sigesp_vis_rpc_beneficiario.html';
				$arrevento ['desevetra'] = '';
				echo $servicioBeneficiarios->guardarBeneficiarioDeducciones($datosempresa['codemp'], $objetoJson, $objetoJson->ced_bene, $arrevento);
	    		unset($servicioBeneficiarios);
	    		break;
				
			case "verificar_rif":
				$servicioBeneficiarios = new servicioBeneficiario();
				echo $servicioBeneficiarios->buscarRifBen($objetoJson->rifben);
				unset($servicioBeneficiarios);
    		break;
			
			case "verificar_cedula":
				$servicioBeneficiarios = new servicioBeneficiario();
				echo $servicioBeneficiarios->buscarCedBen($objetoJson->ced_bene);
				unset($servicioBeneficiarios);
    		break;
    }
       
}