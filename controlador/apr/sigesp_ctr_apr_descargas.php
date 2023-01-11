<?php
/***********************************************************************************
* @Clase para Manejar la descarga de archivos dada una ruta
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
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->operacion;
	switch ($evento)
	{
		case 'descargar':
			$contador=-1;
			$objSistemaVentana->campo = 'descargar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$lista = array();
				$manejador = opendir($objdata->ruta);
				$contador=0;
				while (false!==$archivo = readdir($manejador))
				{
					 if(($archivo != '.') && ($archivo != '..') && ($archivo != '.svn'))
					 {
					 	$arreglo[$contador]['valido']=true;
					 	$arreglo[$contador]['archivo']="".$archivo."";
					 	$arreglo[$contador]['tope']=$contador*20;
					 	$arreglo[$contador]['ruta']="../../".$objdata->ruta."";
					 	$contador++;
					 }
				}
				if($contador<0)
				{
				 	$arreglo[0]['valido'] = false;
					$arreglo[0]['mensaje'] = obtenerMensaje('ARCHIVO_NO_EXISTE');
				}
			}
			else
			{
				$arreglo[0]['mensaje'] = obtenerMensaje('ACCION_NO_VALIDA');  
				$arreglo[0]['valido']  = false;
			}	
			$respuesta  = array('raiz'=>$arreglo);
			$respuesta  = json_encode($respuesta);
			echo $respuesta;
		break;


	}
}
?>