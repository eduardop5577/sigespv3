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
if (($_POST['objdata']) && ($sessionvalida))
{
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/scb/sigesp_dao_scb_banco.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/scb/sigesp_dao_scb_cuentabanco.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_crearreporte.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_validaciones.php');

	$_SESSION['session_activa']=time();
	$objdata = str_replace("\\","",$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	$objBanco = new Banco();	
	$arrResultado = pasarDatos($objBanco,$objdata,$evento);
	$objBanco = $arrResultado["objDao"];
	$evento = $objdata->operacion;
	
	switch ($evento)
	{	
		case 'obtenerBancos':	
			$objBanco->codemp = $_SESSION['la_empresa']['codemp'];	
			$objBanco->tipoconexionbd='DEFECTO';
			$i=0;
			$objBanco->criterio[$i]['operador'] = "AND";
			$objBanco->criterio[$i]['criterio'] = "codban";
			$objBanco->criterio[$i]['condicion'] = " IN ";
			$objBanco->criterio[$i]['valor'] =	"(SELECT codban FROM scb_ctabanco )";
			
			$datos = $objBanco->leer();
			if ($objBanco->valido)
			{
				if (!$datos->EOF)
				{
					$varJson=generarJson($datos);
					echo $varJson;					
				}
				else
				{
					$arreglo[0]['mensaje'] = obtenerMensaje('DATA_NO_EXISTE'); 
					$arreglo[0]['valido']  = false;
					$respuesta  = array('raiz'=>$arreglo);
					$respuesta  = json_encode($respuesta);
					echo $respuesta;	
				}
			}	
			else 
			{	
				$arreglo[0]['mensaje'] = obtenerMensaje('OPERACION_FALLIDA'); 
				$arreglo[0]['valido']  = false;
				$respuesta  = array('raiz'=>$arreglo);
				$respuesta  = json_encode($respuesta);
				echo $respuesta;
			}				
		break;		
		
		case 'obtenerCuenta':
			$objCuenta = new CuentaBanco();
			$objCuenta->codemp = $_SESSION['la_empresa']['codemp'];
			$objCuenta->tipoconexionbd='DEFECTO';
			$i=0;
			$objCuenta->criterio[$i]['operador'] = "AND";
			$objCuenta->criterio[$i]['criterio'] = "codban";
			$objCuenta->criterio[$i]['condicion'] = "=";
			$objCuenta->criterio[$i]['valor'] =	"'".$objdata->codban."'";
			$datos = $objCuenta->leer();
			if ($objCuenta->valido)
			{
				if (!$datos->EOF)
				{
					$varJson=generarJson($datos);
					echo $varJson;				
				}
				else
				{
					$arreglo[0]['mensaje'] = obtenerMensaje('DATA_NO_EXISTE'); 
					$arreglo[0]['valido']  = false;	
					$respuesta  = array('raiz'=>$arreglo);
					$respuesta  = json_encode($respuesta);
					echo $respuesta;									
				}
			}	
			else 
			{	
				$arreglo[0]['mensaje'] = obtenerMensaje('OPERACION_FALLIDA'); 
				$arreglo[0]['valido']  = false;	
				$respuesta  = array('raiz'=>$arreglo);
				$respuesta  = json_encode($respuesta);
				echo $respuesta;				
			}
			unset($objCuenta);					
		break;	

		case 'obtenerCuentaBanco':
			$objCuenta = new CuentaBanco();
			$objCuenta->codemp = $_SESSION['la_empresa']['codemp'];
			$objCuenta->tipoconexionbd='DEFECTO';
			$datos = $objCuenta->leerCuentaBanco();
			if ($objCuenta->valido)
			{
				if (!$datos->EOF)
				{
					$varJson=generarJson($datos);
					echo $varJson;				
				}
				else
				{
					$arreglo[0]['mensaje'] = obtenerMensaje('DATA_NO_EXISTE'); 
					$arreglo[0]['valido']  = false;	
					$respuesta  = array('raiz'=>$arreglo);
					$respuesta  = json_encode($respuesta);
					echo $respuesta;									
				}
			}	
			else 
			{	
				$arreglo[0]['mensaje'] = obtenerMensaje('OPERACION_FALLIDA'); 
				$arreglo[0]['valido']  = false;	
				$respuesta  = array('raiz'=>$arreglo);
				$respuesta  = json_encode($respuesta);
				echo $respuesta;				
			}
			unset($objCuenta);					
		break;	
	}
	unset($objSistemaVentana);
	unset($objBanco);
}		
?>