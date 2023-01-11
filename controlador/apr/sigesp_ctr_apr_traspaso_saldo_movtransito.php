<?php
/***********************************************************************************
* @Clase para manejar el traspaso de saldos y movimientos en tránsito
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/scb/sigesp_dao_scb_banco.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/scb/sigesp_dao_scb_cuentabanco.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/apr/sigesp_dao_apr_banco.php');	
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_validaciones.php');
	
	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	
	$objTrasSaldos = new TraspasoSaldos();		
	$objTrasSaldos->codemp = $_SESSION['la_empresa']['codemp'];	
	$objTrasSaldos->codsis = $objdata->sistema;
	$objTrasSaldos->nomfisico = $objdata->vista;
	$evento='';
	$arrResultado=pasarDatos($objTrasSaldos,$objdata,$evento);
	$objTrasSaldos = $arrResultado["objDao"];
	$evento = $arrResultado["evento"];
	
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->operacion;
	
	switch ($evento)
	{
		case 'obtenerBancos':	
			$objBanco = new Banco();
			$objBanco->codemp = $_SESSION['la_empresa']['codemp'];	
			$objBanco->servidor  = $_SESSION['sigesp_servidor_apr'];
			$objBanco->usuario   = $_SESSION['sigesp_usuario_apr'];
			$objBanco->clave     = $_SESSION['sigesp_clave_apr'];
			$objBanco->basedatos = $_SESSION['sigesp_basedatos_apr'];
			$objBanco->gestor    = $_SESSION['sigesp_gestor_apr'];
			$objBanco->puerto    = $_SESSION['sigesp_puerto_apr'];
			$objBanco->tipoconexionbd='ALTERNA';

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
			$objCuenta->servidor  = $_SESSION['sigesp_servidor_apr'];
			$objCuenta->usuario   = $_SESSION['sigesp_usuario_apr'];
			$objCuenta->clave     = $_SESSION['sigesp_clave_apr'];
			$objCuenta->basedatos = $_SESSION['sigesp_basedatos_apr'];
			$objCuenta->gestor    = $_SESSION['sigesp_gestor_apr'];
			$objCuenta->puerto    = $_SESSION['sigesp_puerto_apr'];
			$objCuenta->tipoconexionbd='ALTERNA';
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
		break;

		case 'Procesar':
			$objSistemaVentana->campo = 'ejecutar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$fecha=date('d-m-Y');
				$nombrearchivo='../../vista/apr/resultados/';
				$nombrearchivo.=$_SESSION['sigesp_basedatos_apr'].'_traspaso_saldos_movtransito_'.$fecha.'.txt';
				$archivo=@fopen($nombrearchivo,'a+');
				$objTrasSaldos->archivo = $archivo;
				$objTrasSaldos->movtransito = $objdata->movtransito;				
				$objTrasSaldos->fecfin = convertirFechaBd($objdata->fecfin);	
				$objTrasSaldos->fecini = convertirFechaBd($objdata->fecini);			
				$objTrasSaldos->procesarSaldos();
				if($objTrasSaldos->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objTrasSaldos->valido;				
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
	unset($objBanco);
	unset($objCuenta);
	unset($objTrasSaldos);
}
?>	