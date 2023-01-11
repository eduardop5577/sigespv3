<?php
/***************************************************************************** 	
* @Controlador para la Proceso de Actualizar Menu.
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	
	$_SESSION['session_activa'] = time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);
	$objSistema = new SistemaVentana();
	$evento=Array();
	$arrResultado = pasarDatos($objSistema,$objdata,$evento);
	$objSistema = $arrResultado["objDao"];
	$evento = $arrResultado["evento"];
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp    = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu    = $_SESSION['la_logusr']; 	
	$objSistemaVentana->codsis    = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->oper;
	if ($objdata->datosSistema)
	{
		$total = count((array)$objdata->datosSistema);
		for ($j=0; $j<$total; $j++)
		{
			$objSistema->arrsistema[$j] = $objdata->datosSistema[$j]->codsis;
		}
	}	
	switch ($evento)
	{
		case 'procesar':	
			$objSistemaVentana->campo = 'ejecutar';
			$accionvalida   = $objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$objSistema->ActualizarMenu();
				if ($objSistema->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');
					$arreglo['valido']  = true;
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');
					$arreglo['valido']  = false;
				}		
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
	unset($objSistema);
}
?>