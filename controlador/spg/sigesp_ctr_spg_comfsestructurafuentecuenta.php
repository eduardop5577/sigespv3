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
require_once('../../base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();
if (($_POST['ObjSon']) && ($sessionvalida))
{
	$_SESSION['session_activa']=time();
	if ($_POST['ObjSon'])
	{
		require_once ('../../base/librerias/php/general/Json.php');
		require_once ('../../modelo/servicio/spg/sigesp_srv_spg_comfsestructurafuentecuenta.php');
		$submit = str_replace ( "\\", "", $_POST['ObjSon'] );
		$json = new Services_JSON ( );
		$arrJson = $json->decode ( $submit );
		$arrEmpresa = $_SESSION["la_empresa"]; 
		$_SESSION['session_activa']=time();
		
		
		switch ($arrJson->operacion) {
			case 'nivel1' :
				$servicioComEstructuraFuenteCuenta = new ServicioComEstructuraFuenteCuenta();
				$datos = $servicioComEstructuraFuenteCuenta->buscarSpgEp1($arrEmpresa['codemp']);
				echo generarJson ($datos,false,false);
				unset($datos);
				unset($servicioComEstructuraFuenteCuenta);
				break;
			case 'nivel2' :
				$servicioComEstructuraFuenteCuenta = new ServicioComEstructuraFuenteCuenta();
				$datos = $servicioComEstructuraFuenteCuenta->buscarSpgEp2($arrEmpresa['codemp'],$arrJson->codest0);
				echo generarJson ($datos,false,false);
				unset($datos);
				unset($servicioComEstructuraFuenteCuenta);
				break;
			case 'nivel3' :
				$servicioComEstructuraFuenteCuenta = new ServicioComEstructuraFuenteCuenta();
				$datos = $servicioComEstructuraFuenteCuenta->buscarSpgEp3($arrEmpresa['codemp'],$arrJson->codest0,$arrJson->codest1);
				echo generarJson ($datos,false,false);
				unset($datos);
				unset($servicioComEstructuraFuenteCuenta);
				break;
			case 'nivel4' :
				$servicioComEstructuraFuenteCuenta = new ServicioComEstructuraFuenteCuenta();
				$datos = $servicioComEstructuraFuenteCuenta->buscarSpgEp4($arrEmpresa['codemp'],$arrJson->codest0,$arrJson->codest1,$arrJson->codest2);
				echo generarJson ($datos,false,false);
				unset($datos);
				unset($servicioComEstructuraFuenteCuenta);
				break;
			case 'nivel5' :
				$servicioComEstructuraFuenteCuenta = new ServicioComEstructuraFuenteCuenta();
				$datos = $servicioComEstructuraFuenteCuenta->buscarSpgEp5($arrEmpresa['codemp'],$arrJson->codest0,$arrJson->codest1,$arrJson->codest2,$arrJson->codest3);
				echo generarJson ($datos,false,false);
				unset($datos);
				unset($servicioComEstructuraFuenteCuenta);
				break;
			case 'nivelN' :
				$servicioComEstructuraFuenteCuenta = new ServicioComEstructuraFuenteCuenta();
				$datos = $servicioComEstructuraFuenteCuenta->buscarSpgEpN($arrJson->cantnivel, $arrEmpresa['codemp']);
				echo generarJson ($datos,false,false);
				unset($datos);
				unset($servicioComEstructuraFuenteCuenta);
				break;
			case 'fuente' :
				$codest3 = '0000000000000000000000000';
				$codest4 = '0000000000000000000000000';
				if($arrJson->cantnivel=='5'){
					$codest3 = $arrJson->codest3;
					$codest4 = $arrJson->codest4;
				}
				$servicioComEstructuraFuenteCuenta = new ServicioComEstructuraFuenteCuenta();
				$datos = $servicioComEstructuraFuenteCuenta->buscarFuentes($arrEmpresa['codemp'], $arrJson->codest0, $arrJson->codest1, 
																		   $arrJson->codest2, $codest3, $codest4, $arrJson->estcla);
				echo generarJson ($datos,false,false);
				unset($datos);
				unset($servicioComEstructuraFuenteCuenta);			
				break;
			case 'cuenta' :
				$codest3 = '0000000000000000000000000';
				$codest4 = '0000000000000000000000000';
				if($arrJson->cantnivel=='5'){
					$codest3 = $arrJson->codest3;
					$codest4 = $arrJson->codest4;
				}
				$CuentaMovimiento=(bool)$arrJson->CuentaMovimiento;
				$servicioComEstructuraFuenteCuenta = new ServicioComEstructuraFuenteCuenta();
				$datos = $servicioComEstructuraFuenteCuenta->buscarCuentas($arrEmpresa['codemp'], $arrJson->codest0, $arrJson->codest1, 
																		   $arrJson->codest2, $codest3, $codest4, $arrJson->estcla, $arrJson->codfuefin, 
																		   $arrJson->codcuenta, $arrJson->dencuenta, $arrJson->codcuentascg, 
																		   $_SESSION["la_logusr"], $arrJson->grupocuenta, $arrJson->nofiltroest, $CuentaMovimiento);
				
				echo generarJson ($datos,false,false);
				unset($datos);
				unset($servicioComEstructuraFuenteCuenta);			
				break;
		}
	}
}
?>