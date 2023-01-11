<?php
/************************************************************************** 	
* @Controlador para las funciones de estructuras presupuestarias de nivel 2.
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/spg/sigesp_srv_spg_estpro2.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	
	$_SESSION['session_activa']=time();
	$objdata = str_replace("\\","",$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	$objEstPre = new EstPro2();		
	$arrResultado=pasarDatos($objEstPre,$objdata,$evento);
	$objEstPre = $arrResultado["objDao"];
	$evento = $arrResultado["evento"];
	$objEstPre->codemp = $_SESSION['la_empresa']['codemp'];	
	$objEstPre->codsis = $objdata->sistema;
	$objEstPre->nomfisico = $objdata->vista;	
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->oper;
	
	switch ($evento)
	{
		case 'cargarTituloGridCat':		
			$nomestpro1 = $_SESSION['la_empresa']['nomestpro1'];
			$nomestpro2 = $_SESSION['la_empresa']['nomestpro2'];			
			$arreglo = array ('nivel1'=>$nomestpro1,'nivel2'=>$nomestpro2);			
			$respuesta  = array('raiz'=>$arreglo);
			$respuesta  = json_encode($respuesta);
			echo $respuesta;
		break;
		
		case 'catalogo':			
			$objEstPre->codusu = $_SESSION['la_logusr'];	
			$objEstPre->criterio[0]['operador'] = "AND";
			$objEstPre->criterio[0]['criterio'] = "codestpro1";
			$objEstPre->criterio[0]['condicion'] = "=";
			$objEstPre->criterio[0]['valor'] =	"'".$objdata->codestpro1."'";
			$objEstPre->criterio[1]['operador'] = "AND";
			$objEstPre->criterio[1]['criterio'] = "estcla";
			$objEstPre->criterio[1]['condicion'] = "=";
			$objEstPre->criterio[1]['valor'] =	"'".$objdata->estcla."'";
			$datos = $objEstPre->leer();
			if ($objEstPre->valido)
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
				
		case 'catalogo_apertura':			
			$objEstPre->codemp = $_SESSION['la_empresa']['codemp'];	
			$objEstPre->codusu = $_SESSION['la_logusr'];	
			$objEstPre->criterio[0]['operador'] = "AND";
			$objEstPre->criterio[0]['criterio'] = "codestpro1";
			$objEstPre->criterio[0]['condicion'] = "=";
			$objEstPre->criterio[0]['valor'] =	"'".$objdata->codestpro1."'";
			$objEstPre->criterio[1]['operador'] = "AND";
			$objEstPre->criterio[1]['criterio'] = "estcla";
			$objEstPre->criterio[1]['condicion'] = "=";
			$objEstPre->criterio[1]['valor'] =	"'".$objdata->estcla."'";
			$datos = $objEstPre->leer_apertura();
			if ($objEstPre->valido)
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
	unset($objEstPre);
}		
?>