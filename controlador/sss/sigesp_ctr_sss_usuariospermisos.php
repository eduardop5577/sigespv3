<?php
/***********************************************************************************
* @Clase para Manejar  el proceso de asignar usuarios a un tipo de personal.
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sno/sigesp_dao_sno_tipopersonalsss.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sno/sigesp_dao_sno_constante.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sno/sigesp_dao_sno_nomina.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/spg/sigesp_srv_spg_unidadadmin.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/spg/sigesp_srv_spg_estpro5.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_permisosinternos.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_derechosusuario.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/siv/sigesp_dao_siv_almacen.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/cfg/sigesp_srv_cfg_scg_centrocosto.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/scb/sigesp_dao_scb_banco.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/cfg/sigesp_srv_cfg_controlnumero.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/srh/sigesp_srv_srh_odi.php');
	
	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);	

	if ($objdata->seleccionado=='personal')
	{
		$objItem = new TipoPersonalSSS();
		$objItem->codemp = $_SESSION['la_empresa']['codemp'];		
		$objItem->nomfisico = $objdata->vista;	
	}
	elseif ($objdata->seleccionado=='constante')
	{
		$objItem = new Constante();
		$objItem->codemp = $_SESSION['la_empresa']['codemp'];		
		$objItem->nomfisico = $objdata->vista;	
	}	
	elseif ($objdata->seleccionado=='unidad')
	{
		$objItem = new UnidadAdministrativa();
		$objItem->codemp = $_SESSION['la_empresa']['codemp'];		
		$objItem->nomfisico = $objdata->vista;	
	}	
	elseif ($objdata->seleccionado=='nomina')
	{
		$objItem = new Nomina();
		$objItem->codemp = $_SESSION['la_empresa']['codemp'];		
		$objItem->nomfisico = $objdata->vista;	
	}
	elseif ($objdata->seleccionado=='presupuesto')
	{
		$objItem = new EstPro5();
		$objItem->codemp = $_SESSION['la_empresa']['codemp'];		
		$objItem->nomfisico = $objdata->vista;	
	}	
	elseif ($objdata->seleccionado=='almacen')
	{
		$objItem = new Almacen();
		$objItem->codemp = $_SESSION['la_empresa']['codemp'];		
		$objItem->nomfisico = $objdata->vista;	
	}	
	elseif ($objdata->seleccionado=='centrocosto')
	{
		$objItem = new CentroCosto();
		$objItem->codemp = $_SESSION['la_empresa']['codemp'];		
		$objItem->nomfisico = $objdata->vista;	
	}	
	elseif ($objdata->seleccionado=='banco')
	{
		$objItem = new Banco();
		$objItem->codemp = $_SESSION['la_empresa']['codemp'];		
		$objItem->nomfisico = $objdata->vista;	
	}	
	elseif ($objdata->seleccionado=='prefijo')
	{
		$objItem = new ControlNro();
		$objItem->codemp = $_SESSION['la_empresa']['codemp'];		
		$objItem->nomfisico = $objdata->vista;	
	}	
	elseif ($objdata->seleccionado=='odi')
	{
		$objItem = new ODI();
		$objItem->codemp = $_SESSION['la_empresa']['codemp'];		
		$objItem->nomfisico = $objdata->vista;	
	}	

	$objPermisos = new PermisosInternos();
	$objPermisos->codemp = $_SESSION['la_empresa']['codemp'];
	$objPermisos->codusu = $objdata->datosAdmin[0]->codusu;
	$objPermisos->codsis = $objdata->codsis;
	$objPermisos->codintper = trim($objdata->codtippersss);
	$objPermisos->enabled = 1;
	$objPermisos->nomfisico = $objdata->vista;
	if (isUTF8($objPermisos->codusu))
	{
		$objPermisos->codusu = utf8_to_latin9($objPermisos->codusu);
	}		
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	if (isUTF8($objSistemaVentana->codusu))
	{
		$objSistemaVentana->codusu = utf8_to_latin9($objSistemaVentana->codusu);
	}

	$evento = $objdata->oper;
	// Cargamos los usuarios que se agregaron al personal
	if ($objdata->datosAdmin)
	{
		$total = count((array)$objdata->datosAdmin);
		for ($j=0; $j<$total; $j++)
		{
			$objPermisos->admin[$j] = new PermisosInternos();
			$arrResultado = "";
			$arrResultado = pasarDatos($objPermisos->admin[$j],$objdata->datosAdmin[$j]);	
			$objPermisos->admin[$j] = $arrResultado["objDao"];
			
			$objPermisos->admin[$j]->codemp = $_SESSION['la_empresa']['codemp'];
			$objPermisos->admin[$j]->codusu = $objdata->datosAdmin[$j]->codusu;
			$objPermisos->admin[$j]->codsis = $objdata->codsis;
			$objPermisos->admin[$j]->codintper = trim($objdata->codtippersss);
			$objPermisos->admin[$j]->nomfisico = $objdata->vista;			
			if (isUTF8($objPermisos->admin[$j]->codusu))
			{
				$objPermisos->admin[$j]->codusu = utf8_to_latin9($objPermisos->admin[$j]->codusu);
			}		
		}
	}
	// Cargamos los usuarios que se eliminaron al personal
	if ($objdata->datosEliminar)
	{
		$total = count((array)$objdata->datosEliminar);
		for ($j=0; $j<$total; $j++)
		{
			$objPermisos->usuarioeliminar[$j] = new PermisosInternos();
			$arrResultado = "";
			$arrResultado = pasarDatos($objPermisos->usuarioeliminar[$j],$objdata->datosEliminar[$j]);	
			$objSistema->usuarioeliminar[$j] = $arrResultado["objDao"];
			
			$objPermisos->usuarioeliminar[$j]->codemp = $_SESSION['la_empresa']['codemp'];
			$objPermisos->usuarioeliminar[$j]->codusu = $objdata->datosEliminar[$j]->codusu;
			$objPermisos->usuarioeliminar[$j]->codsis = $objdata->codsis;
			$objPermisos->usuarioeliminar[$j]->codintper = trim($objdata->codtippersss);
			$objPermisos->usuarioeliminar[$j]->nomfisico = $objdata->vista;	
			if (isUTF8($objPermisos->usuarioeliminar[$j]->codusu))
			{
				$objPermisos->usuarioeliminar[$j]->codusu = utf8_to_latin9($objPermisos->usuarioeliminar[$j]->codusu);
			}		
		}
	}	
	switch ($evento)
	{
		case 'actualizar':	 
			$objSistemaVentana->campo = 'cambiar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{				
				$objPermisos->actualizar();
				if($objPermisos->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');
					$arreglo['valido']  = $objPermisos->valido;	
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
								
		case 'eliminar':
			$objSistemaVentana->campo = 'eliminar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{				
				$objItemElim = new PermisosInternos();
				$objItemElim->nomfisico = $objdata->vista;
				$objItemElim->codemp = $_SESSION['la_empresa']['codemp'];
						
				$objItemElim->criterio[0]['operador'] = "AND";
				$objItemElim->criterio[0]['criterio'] = "codsis";
				$objItemElim->criterio[0]['condicion'] = "=";
				$objItemElim->criterio[0]['valor'] = "'".$objdata->codsis."'";
				
				$objItemElim->criterio[1]['operador'] = "AND";
				$objItemElim->criterio[1]['criterio'] = "codintper";
				$objItemElim->criterio[1]['condicion'] = "=";
				$objItemElim->criterio[1]['valor'] = "'".trim($objdata->codtippersss)."'";
				$objItemElim->eliminarTodos();
				if($objItemElim->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objItemElim->valido;			
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
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{				
				$datos = $objItem->leer();
				if($objItem->valido)
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
			$objItem = new PermisosInternos();
			$objItem->codemp = $_SESSION['la_empresa']['codemp'];
			$objItem->codusu = $objdata->datosAdmin[0]->codusu;			
			$objItem->codsis = $objdata->codsis;
			$objItem->codintper = trim($objdata->codtippersss);
			$objItem->campo = $objdata->campo;
			$objItem->tabla = $objdata->tabla;
			$datos = $objItem->obtenerUsuarios();	
			if($objItem->valido)
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
	unset($objItem);
}	
?>
