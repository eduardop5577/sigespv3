<?php
/***********************************************************************************
* @Clase para manejar el actualizar las cuentas contables.
* @fecha de modificacion: 26/07/2022, para la version de php 8.1 
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/apr/sigesp_dao_apr_contables.php');	
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
		
	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	
	$objActCuentas = new ActCuentasContables();	
	$objActCuentas->codemp = $_SESSION['la_empresa']['codemp'];	
	$objActCuentas->codsis = $objdata->sistema;
	$objActCuentas->nomfisico = $objdata->vista;
	
	if ($objdata->datosCuentas)
	{
		$total = count((array)$objdata->datosCuentas);
		for ($j=0; $j<$total; $j++)
		{
			$objActCuentas->cuenta[$j] = new ActCuentasContables();	
			$objActCuentas->cuenta[$j]->sccuentaant = $objdata->datosCuentas[$j]->ctaanterior;
			$objActCuentas->cuenta[$j]->sccuentaact = $objdata->datosCuentas[$j]->ctaactual;		
		}
	}	
	
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->operacion;
	
	switch ($evento)
	{
		case 'catalogocuentas':	
			$objCuenta = new ActCuentasContables();
			$datos = $objCuenta->cargarCuentas();
			if ($objCuenta->valido)
			{
				if (!$datos->EOF)
				{
					$respuesta  = array('raiz'=>$datos);
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
				
		
		case 'procesar':
			$objSistemaVentana->campo = 'ejecutar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$fecha = date('d-m-Y');
				$nombrearchivo = '../../vista/apr/resultados/';
				$nombrearchivo.=$_SESSION['sigesp_basedatos_apr'].'_actualizar_cuentas_result_'.$fecha.'.txt';
				$archivo = @fopen($nombrearchivo,'a+');
				$objActCuentas->archivo = $archivo;
				$objActCuentas->incluirCuentas();
				if($objActCuentas->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objActCuentas->valido;		
			}
			else
			{
				$arreglo['mensaje'] = obtenerMensaje('ACCION_NO_VALIDA');  
				$arreglo['valido']  = false;
			}	
			$respuesta  = array('raiz'=>$arreglo);
			$respuesta  = json_encode($respuesta);
			echo $respuesta;
		break;	
	}
	unset($objSistemaVentana);	
	unset($objCuenta);	
	unset($objActCuentas);
}
?>