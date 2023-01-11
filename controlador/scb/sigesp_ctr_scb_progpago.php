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
$datosempresa=$_SESSION["la_empresa"];
$dirctrscb = "";
$dirctrscb = dirname(__FILE__);
$dirctrscb = str_replace("\\","/",$dirctrscb); 
$dirctrscb = str_replace("/controlador/scb","",$dirctrscb);
require_once ($dirctrscb."/base/librerias/php/general/Json.php");
require_once ($dirctrscb."/modelo/servicio/scb/sigesp_srv_scb_progpago.php");
//require_once ($dirctrscb."/modelo/servicio/cfg/sigesp_srv_cfg_planctainstitucional.php");


if ($_POST['ObjSon'])
{
    $submit = str_replace("\\", "", $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion) {
		case "catalogo_ctabanco":
    		$servicioBancos = new servicioBanco();
    		$resultado = $servicioBancos->buscarCtasBancarias($objetoJson->codban,$objetoJson->ctaiban,$objetoJson->deniban);

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioBancos);
    		break;
			
		case "catalogo_ctabanco_transf":
    		$servicioBancos = new servicioBanco();
    		$resultado = $servicioBancos->buscarCtasBancariasTransf($objetoJson->codbandes,$objetoJson->ctaiban,$objetoJson->deniban);

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioBancos);
    		break;
		
		case "catalogo_banco":
    		$servicioBancos = new servicioBanco();
    		$resultado = $servicioBancos->buscarBancos();

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioBancos);
    	break;	
		
		case "buscarsaldo":
    		$servicioBancos = new servicioBanco();
    		echo $servicioBancos->buscarSaldoCtaban($objetoJson->codban,$objetoJson->codcta);
			unset($servicioBancos);
    	break;
		
		case 'buscar_solicitudes':
    		$servicioBancos = new servicioBanco();
    		echo generarJsonArreglo($servicioBancos->buscarSolicitudes($objetoJson->tipproben,$objetoJson->tipvia));
			unset($servicioBancos);
    	break;  
		
		case "programar":
    		$servicioBancos = new servicioBanco();
			$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = "SCB";
    		$arrevento ['evento']  = "UPDATE";
    		$arrevento ['nomfisico'] = "sigesp_vis_scb_progpago.html";
    		$arrevento ['desevetra'] = "Realizo la programacion de pago la solicitud ".$objetoJson->numsol.", asociado a la empresa ".$datosempresa["codemp"];
    		$resultado = $servicioBancos->insertarProgramacion($objetoJson->numsol,$objetoJson->fechaprog,$objetoJson->estmov,$objetoJson->codban,$objetoJson->ctaban,$objetoJson->provee_benef,$objetoJson->tipproben,$objetoJson->tipvia,$arrevento);
    		if ($resultado) {
    			echo "1";
    		}
    		else{
    			echo "0";
    		}
    		unset($servicioBancos);
    	break;
		
		case "buscarCuenta":
			$servicioBancos = new servicioBanco();
			echo generarJson($servicioBancos->buscarTipoCuenta($datosempresa['codemp']));
			unset($servicioBancos);
		break;
    	   	
    	   	
	 	/*case "buscarTodasCuenta":
			$ServicioPlanCuentaInstitucional = new ServicioPlanCuentaInstitucional();
			echo generarJson($ServicioPlanCuentaInstitucional->buscarTodasCuenta($datosempresa['codemp']));
			unset($ServicioPlanCuentaInstitucional);
		break;*/
    	    	
    	case "incluir":
    		$servicioBancos = new servicioBanco();
    		$arrevento ['codemp']  = $datosempresa["codemp"];
    		$arrevento ['codusu']  = $_SESSION["la_logusr"];
    		$arrevento ['codsis']  = $objetoJson->codsis;
    		$arrevento ['evento']  = "INSERT";
            $arrevento['nomfisico'] = $objetoJson->nomven;    		
            $arrevento ['desevetra'] = "Inserto el beneficiario con codigo ".$objetoJson->ced_bene.", asociado a la empresa ".$datosempresa["codemp"];
    		$resultado = $servicioBancos->guardarBeneficiario($datosempresa["codemp"],$objetoJson,$arrevento);
    		if ($resultado === 1) {
    			echo "1";
    		}
    		else{
    			echo "0";
    		}
    		unset($servicioBancos);
    	break;
    		
		case "actualizar":
			$servicioBancos = new servicioBanco();
			$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codsis']  = $objetoJson->codsis;
			$arrevento ['evento']  = "UPDATE";
			$arrevento ['nomfisico']  = $objetoJson->nomven; 
			$arrevento ['desevetra'] = "Modifico el beneficiario con codigo ".$objetoJson->ced_bene.", asociado a la empresa ".$datosempresa["codemp"];
			$resultado = $servicioBancos->modificarBeneficiario($datosempresa["codemp"],$objetoJson,$arrevento);
			if ($resultado === 1) {
				echo "1";
			}
			else{
				echo "0";
			}
			unset($servicioBancos);
		break;
    			 
		case "eliminar":
			$servicioBancos = new servicioBanco();
			$arrevento ['codemp']  = $datosempresa["codemp"];
			$arrevento ['codusu']  = $_SESSION["la_logusr"];
			$arrevento ['codsis']  = "RPC";
			$arrevento ['evento']  = "DELETE";
			$arrevento ['nomven']  = "sigesp_vis_rpc_beneficiario.html";
			$arrevento ['desevetra'] = "Modifico el parametro de clasificacion con codigo ".$objetoJson->ced_bene.", asociado a la empresa ".$datosempresa["codemp"];
			$resultado = $servicioBancos->eliminarBeneficiario($datosempresa["codemp"],$objetoJson,$arrevento);
			if ($resultado === 1) {
				echo "1";
			}
			else{
				echo "0";
			}
			unset($servicioBancos);
		break;
     }
       
}