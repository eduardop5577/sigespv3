<?php
/***********************************************************************************
* @Clase para manejar el proceso de apertura del ejercicio contable.
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/apr/sigesp_dao_apr_apertura_ejercicio.php');	
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
		
	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	
	$objEjercicio = new AperturaEjercicio();	
	$objEjercicio->codemp = $_SESSION['la_empresa']['codemp'];	
	$objEjercicio->codsis = $objdata->sistema;
	$objEjercicio->nomfisico = $objdata->vista;
		
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->operacion;
	
	switch ($evento)
	{			
		case 'Procesar':
			$objSistemaVentana->campo = 'ejecutar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$fecha = date('d-m-Y');
				$nombrearchivo = '../../vista/apr/resultados/';
				$nombrearchivo.=$_SESSION['sigesp_basedatos_apr'].'_apertura_ejercicio_contable_'.$fecha.'.txt';
				$archivo = @fopen($nombrearchivo,'a+');
				$objEjercicio->archivo = $archivo;		
						
				$objEjercicio->periodo     = $_SESSION["la_empresa"]["periodo"];
				$objEjercicio->procede     = 'SCGAPR';
				$objEjercicio->comprobante = '0000000APERTURA';
				$objEjercicio->ced_ben     = '----------';	
				$objEjercicio->cod_prov    = '----------';
				$objEjercicio->tipo        = '-';
				$objEjercicio->tipo_cmp    = 1;
				$objEjercicio->descripcion = 'APERTURA DE CUENTAS';			
				$objEjercicio->procesarAperturaEjercicio();		
				if($objEjercicio->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objEjercicio->valido;		
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
	unset($objEjercicio);
}
?>