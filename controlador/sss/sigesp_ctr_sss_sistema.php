<?php
/***********************************************************************************
* @Clase para Manejar para la definición de Sistema.
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_crearreporte.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_validaciones.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistema.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_usuariosistema.php');
	
	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	$objSistema = new Sistema();		
	$objSistema->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistema->codsis = $objdata->sistema;
	$objSistema->nomfisico = $objdata->vista;	
	$arrResultado = pasarDatos($objSistema,$objdata,$evento);
	$objSistema = $arrResultado["objDao"];
	$evento = $arrResultado["evento"];

	$objSistema->codemp = $_SESSION['la_empresa']['codemp'];
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr']; 
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico  = $objdata->vista;
	$evento = $objdata->oper;
	// Cargamos los usuarios que se agregaron al sistema
	if ($objdata->datosAdmin)
	{
		$total = count((array)$objdata->datosAdmin);
		for ($j=0; $j<$total; $j++)
		{
			$objSistema->admin[$j] = new Usuariosistema();
			$arrResultado = "";
			$arrResultado = pasarDatos($objSistema->admin[$j],$objdata->datosAdmin[$j]);	
			$objSistema->admin[$j] = $arrResultado["objDao"];
		}
	}
	// Cargamos los usuarios que se eliminaron al sistema
	if ($objdata->datosEliminar)
	{
		$total = count((array)$objdata->datosEliminar);
		for ($j=0; $j<$total; $j++)
		{
			$objSistema->usuarioeliminar[$j] = new UsuarioSistema();
			$arrResultado = "";
			$arrResultado = pasarDatos($objSistema->usuarioeliminar[$j],$objdata->datosEliminar[$j]);	
			$objSistema->usuarioeliminar[$j] = $arrResultado["objDao"];
		}
	}	
	switch ($evento)
	{
		case 'incluir':	
			$objSistemaVentana->campo = 'incluir';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			$correcto=(validaciones($objSistema->codsis,'3','novacio|longexacta') && validaciones($objSistema->nomsis,'60','novacio|nombre'));
			if ($accionvalida)
			{
				if ($correcto)
				{
					$objSistema->verificarCodigo();
					if($objSistema->valido)
					{
						if ($objSistema->existe==false)	
						{				
							$objSistema->incluirLocal();
							if($objSistema->valido)
							{
								$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
							}
							else
							{
								$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
							}
							$arreglo['valido']  = $objSistema->valido;
						}
						else
						{					
							$arreglo['mensaje'] = obtenerMensaje('REGISTRO_EXISTE');
							$arreglo['valido']  = $objSistema->existe;
						}	
					}
					else
					{				
						$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');
						$arreglo['valido']  = $objSistema->existe;
					}
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('DATOS_NO_VALIDO');  
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
		
		case 'actualizar':
			$objSistemaVentana->campo = 'cambiar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			$correcto=validaciones($objSistema->nomsis,'60','novacio|nombre');
			if ($accionvalida)
			{	
				if ($correcto)
				{
					$objSistema->verificarCodigo();
					if($objSistema->valido)
					{
						if ($objSistema->existe==true)	
						{				
							$objSistema->modificarLocal();
							if($objSistema->valido)
							{
								$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
							}
							else
							{
								$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
							}
							$arreglo['valido']  = $objSistema->valido;
						}					
										}
					else
					{				
						$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');
						$arreglo['valido']  = $objSistema->existe;
					}
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('REGISTRO_NO_EXISTE');
					$arreglo['valido']  = $objSistema->existe;
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
			
		case 'catalogo':
			$objSistemaVentana->campo = 'leer';
			$accionvalida = $objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$datos = $objSistema->leer();
				if($objSistema->valido)
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
			}
			else
			{
				$arreglo[0]['mensaje'] = obtenerMensaje('ACCION_NO_VALIDA'); 
				$arreglo[0]['valido']  = false;
				$respuesta  = array('raiz'=>$arreglo);			
				$respuesta  = json_encode($respuesta);
				echo $respuesta;
			}
		break;
		
		case 'catalogodetalle':	
			$datos = $objSistema->obtenerUsuarios();
			if($objSistema->valido)
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
			
		case 'eliminar':
			$objSistemaVentana->campo = 'eliminar';
			$accionvalida = $objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$objSistema->verificarCodigo();
				if($objSistema->valido)
				{
					if ($objSistema->existe===true)
					{
						$objSistema->usuarioeliminar[0] = new UsuarioSistema();
						$objSistema->usuarioeliminar[0]->eliminarLocal();
						if($objSistema->valido)
						{
							$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
						}
						else
						{
							$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
						}
						$arreglo['valido']  = $objSistema->valido;					
					}
					else 
					{
						$arreglo['mensaje'] = obtenerMensaje('REGISTRO_NO_EXISTE');
						$arreglo['valido']  = $objSistema->existe;
					}
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');
					$arreglo['valido']  = $objSistema->existe;
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
		
		case 'reporteficha':
			$objSistemaVentana->campo = 'imprimir';
			$accionvalida = $objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$objReporte = new crearReporte();
				$objReporte->codsis = strtolower($objdata->sistema);
				$objSistema->cadena = $objdata->codsis;
				$datosSis = $objSistema->leer();
				$data = $objSistema->obtenerUsuarios();  
				if (count((array)$data)>0)
				{
					$objReporte->crearXml('datos_sistema',$datosSis);
					$objReporte->crearXml('ficha_sistema',$data);
					$objReporte->nomRep='ficha_sistema';
					echo $objReporte->mostrarReporte();	
				}
				else
				{
					echo '';
				}						
				unset($objReporte);
			}
			else
			{
				echo '';
			}						
	}
	unset($objSistemaVentana);
	unset($objSistema);
}	
?>
