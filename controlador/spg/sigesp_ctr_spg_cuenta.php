<?php
/************************************************************************** 	
* @Controlador para las funciones de cuentas spg.
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
if (($_POST['objdata']) && ($sessionvalida))
{
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/spg/sigesp_srv_spg_cuenta.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	
	$_SESSION['session_activa']=time();
	$objdata = str_replace("\\","",$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	$objCuenta = new Cuenta();		
	$arrResultado=pasarDatos($objCuenta,$objdata,$evento);
	$objCuenta = $arrResultado["objDao"];
	$evento = $arrResultado["evento"];
	$objCuenta->codemp = $_SESSION['la_empresa']['codemp'];	
	$objCuenta->codsis = $objdata->sistema;
	$objCuenta->nomfisico = $objdata->vista;	
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->oper;
	
	switch ($evento)
	{
		case 'catalogo':			
			$i=0;
			if ($objdata->codestpro1!='' && $objdata->codestpro2!='' && $objdata->codestpro3!='')
			{					
				$objCuenta->criterio[$i]['operador'] = "AND";
				$objCuenta->criterio[$i]['criterio'] = "codestpro1";
				$objCuenta->criterio[$i]['condicion'] = "=";
				$objCuenta->criterio[$i]['valor'] =	"'".$objdata->codestpro1."'";
				$i++;
				$objCuenta->criterio[$i]['operador'] = "AND";
				$objCuenta->criterio[$i]['criterio'] = "estcla";
				$objCuenta->criterio[$i]['condicion'] = "=";
				$objCuenta->criterio[$i]['valor'] =	"'".$objdata->estcla."'";
				$i++;
				$objCuenta->criterio[$i]['operador'] = "AND";
				$objCuenta->criterio[$i]['criterio'] = "codestpro2";
				$objCuenta->criterio[$i]['condicion'] = "=";
				$objCuenta->criterio[$i]['valor'] =	"'".$objdata->codestpro2."'";
				$i++;
				$objCuenta->criterio[$i]['operador'] = "AND";
				$objCuenta->criterio[$i]['criterio'] = "codestpro3";
				$objCuenta->criterio[$i]['condicion'] = "=";
				$objCuenta->criterio[$i]['valor'] =	"'".$objdata->codestpro3."'";
				$i++;
				if ($objdata->codestpro4!='')
				{
					$objCuenta->criterio[$i]['operador'] = "AND";
					$objCuenta->criterio[$i]['criterio'] = "codestpro4";
					$objCuenta->criterio[$i]['condicion'] = "=";
					$objCuenta->criterio[$i]['valor'] =	"'".$objdata->codestpro4."'";
					$i++;
				}	
				if ($objdata->codestpro5!='')
				{
					$objCuenta->criterio[$i]['operador'] = "AND";
					$objCuenta->criterio[$i]['criterio'] = "codestpro5";
					$objCuenta->criterio[$i]['condicion'] = "=";
					$objCuenta->criterio[$i]['valor'] =	"'".$objdata->codestpro5."'";
					$i++;
				}
				$objCuenta->criterio[$i]['operador'] = "AND";
				$objCuenta->criterio[$i]['criterio'] = "spg_cuenta";
				$objCuenta->criterio[$i]['condicion'] = "like";
				$objCuenta->criterio[$i]['valor'] =	"'411%'";													
				$i++;
			}							
			else
			{
				$objCuenta->consulta = 'todos';
			}
			$objCuenta->codemp = $_SESSION['la_empresa']['codemp'];	
			$datos = $objCuenta->leer();
			if ($objCuenta->valido)
			{
				if (!$datos->EOF)
				{
					$varJson=generarJson($datos);
					echo $varJson;				
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
	}
	unset($objSistemaVentana);
	unset($objCuenta);
}		
?>