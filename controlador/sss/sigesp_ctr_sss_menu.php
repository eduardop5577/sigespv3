<?php
/***********************************************************************************
* @Clase para Manejar el menu del sistema según la permisología del usuario
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
	
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);	
	$objSistemaVentana    = new SistemaVentana();
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];
	$objSistemaVentana->codusu = $_SESSION['la_logusr']; 
	$objSistemaVentana->codsis = $objdata->codsis;
	$evento = $objdata->operacion;
	switch ($evento)
	{
		case 'menu':
			$datos = $objSistemaVentana->obtenerMenuUsuario();
			//var_dump($datos);
			if (count((array)$datos)>0)
			{
				$varJson=generarJson($datos,true,false);
				echo $varJson;				
			}
			else 
			{	
				echo '';
			}
		break;

		case 'barraherramienta':
			$objSistemaVentana->nomfisico = $objdata->nomfisico;
			$datos = $objSistemaVentana->obtenerBarraHerramientaUsuario();
			if (count((array)$datos)>0)
			{
				$varJson=generarJson($datos,true,false);
				echo $varJson;				
			}
			else 
			{	
				echo '';
			}
		break;
	}
}
?>