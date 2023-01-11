<?php
/************************************************************************************* 	
* @Controlador para proceso de asignar perfil a usuario o grupo.
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistema.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_grupo.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_usuario.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_derechosusuario.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_derechosgrupo.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_permisosinternos.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_permisosinternos_grupo.php');
	
	$_SESSION['session_activa'] = time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);	
	$objSistema = new Sistema();
	$objSistema->codemp = $_SESSION['la_empresa']['codemp'];	
	$objUsuario = new Usuario();
	$objUsuario->codemp = $_SESSION['la_empresa']['codemp'];
	$objGrupo   = new Grupo();
	$objGrupo->codemp = $_SESSION['la_empresa']['codemp'];
		
	if ($objdata->seleccionado=='usuario')
	{
		$objPerfil   = new DerechosUsuario();
		$objPermisos = new PermisosInternos();	
		if (isUTF8($objdata->codusu))
		{
			$objPermisos->codusu = utf8_to_latin9($objdata->codusu);
		}
		else
		{
			$objPermisos->codusu = $objdata->codusu;
		}
		$blnGrupo = false;
	}
	else
	{
		$objPerfil   = new DerechosGrupo();
		$objPermisos = new PermisosInternosGrupo();
		if (isUTF8($objdata->codusu))
		{
			$objPermisos->nomgru = utf8_to_latin9($objdata->nomgru);
		}
		else
		{
			$objPermisos->nomgru = $objdata->nomgru;
		}
		$blnGrupo = true;
	}	
	$objPermisos->codemp = $_SESSION['la_empresa']['codemp'];
	$objPermisos->codsis = $objdata->codsis;
	$objPermisos->codintper = $objdata->codintper;
	$objPermisos->nomfisico = $objdata->vista;
	$objPermisos->codmenu = $objdata->codmenu;
	$objPerfil->codemp = $_SESSION['la_empresa']['codemp'];	
	$objPerfil->codsis = $objdata->sistema;
	$objPerfil->nomfisico = $objdata->vista;	
	$arrResultado = pasarDatos($objPerfil,$objdata,$evento);
	$objPerfil = $arrResultado["objDao"];
	$evento = $arrResultado["evento"];

	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->operacion;
	switch ($evento)
	{
		case 'obtenerSistema':			
			$datos = $objSistema->leer();
			if ($objSistema->valido)
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
		
		case 'obtenerUsuario':			
			$datos = $objUsuario->leer();
			if ($objUsuario->valido)
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
		
		case 'obtenerGrupo':			
			$datos = $objGrupo->leer();
			if ($objGrupo->valido)
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
		
		case 'obtenerMenu':
			$objSistemaVentana->codsis = $objdata->codsis;
			$datos = $objSistemaVentana->obtenerOpcionesMenu();
			if ($objSistemaVentana->valido)
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

		case 'obtenerPermisos':
			$objSistemaVentana->codsis = $objdata->codsis;
			$objSistemaVentana->codmenu = $objdata->codmenu;
			$datos = $objSistemaVentana->obtenerMenu();
			if ($objSistemaVentana->valido)
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
		
		case 'incluir':
			$objSistemaVentana->campo = 'cambiar';
			$accionvalida = $objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$i=0;
				$objPerfil->criterio[$i]['operador'] = "AND";
				$objPerfil->criterio[$i]['criterio'] = "codsis";
				$objPerfil->criterio[$i]['condicion'] = "=";
				$objPerfil->criterio[$i]['valor'] = "'".$objdata->codsis."'";
				$i++;
				if (!$blnGrupo)
				{
					$objPerfil->criterio[$i]['operador'] = "AND";
					$objPerfil->criterio[$i]['criterio'] = "codusu";
					$objPerfil->criterio[$i]['condicion'] = "=";
					$objPerfil->criterio[$i]['valor'] = "'".$objPermisos->codusu."'";
				}	
				else
				{
					$objPerfil->criterio[$i]['operador'] = "AND";
					$objPerfil->criterio[$i]['criterio'] = "nomgru";
					$objPerfil->criterio[$i]['condicion'] = "=";
					$objPerfil->criterio[$i]['valor'] = "'".$objPermisos->nomgru."'";

				}				
				$i++;
				$objPerfil->criterio[$i]['operador'] = "AND";
				$objPerfil->criterio[$i]['criterio'] = "codmenu";
				$objPerfil->criterio[$i]['condicion'] = "=";
				$objPerfil->criterio[$i]['valor'] = "'".$objdata->codmenu."'";
				$objPerfil->incluirLocal();
				if (($blnGrupo)&&($objPerfil->valido))
				{
					$objPerfilGrupo = new DerechosUsuario();	
					$objPerfilGrupo->codemp = $objPerfil->codemp;
					$objPerfilGrupo->nomgru = $objPerfil->nomgru;
					$objPerfilGrupo->nomfisico = $objPerfil->nomfisico;
					$objPerfilGrupo->codsis = $objdata->codsis;
					$objPerfilGrupo->codmenu = $objdata->codmenu;
					$objPerfilGrupo->visible = 1;
					$objPerfilGrupo->enabled = 1;
					$objPerfilGrupo->incluir = $objPerfil->incluir;
					$objPerfilGrupo->leer = $objPerfil->leer;
					$objPerfilGrupo->cambiar = $objPerfil->cambiar;
					$objPerfilGrupo->eliminar = $objPerfil->eliminar;
					$objPerfilGrupo->imprimir = $objPerfil->imprimir;
					$objPerfilGrupo->ejecutar = $objPerfil->ejecutar;
					$objPerfilGrupo->anular = $objPerfil->anular;
					$objPerfilGrupo->administrativo = $objPerfil->administrativo;
					$objPerfilGrupo->ayuda = $objPerfil->ayuda;
					$objPerfilGrupo->cancelar = $objPerfil->cancelar;
					$objPerfilGrupo->enviarcorreo = $objPerfil->enviarcorreo;
					$objPerfilGrupo->descargar = $objPerfil->descargar;
					$objPerfilGrupo->criterio = $objPerfil->criterio;
					$objPerfilGrupo->incluirDerechosGrupos();
					$objPerfilGrupo->modificarDerechosGrupos();
					$objPerfil->valido = $objPerfilGrupo->valido;
					unset($objPerfilGrupo);
				}
				if ($objPerfil->valido)
				{
					$objPerfil->modificarPerfil();
					if (($blnGrupo)&&($objPerfil->valido))
					{
						$objPerfilGrupo = new DerechosUsuario();	
						$objPerfilGrupo->codemp = $objPerfil->codemp;
						$objPerfilGrupo->nomgru = $objPerfil->nomgru;
						$objPerfilGrupo->nomfisico = $objPerfil->nomfisico;
						$objPerfilGrupo->codsis = $objdata->codsis;
						$objPerfilGrupo->codmenu = $objdata->codmenu;
						$objPerfilGrupo->visible = 1;
						$objPerfilGrupo->enabled = 1;
						$objPerfilGrupo->incluir = $objPerfil->incluir;
						$objPerfilGrupo->leer = $objPerfil->leer;
						$objPerfilGrupo->cambiar = $objPerfil->cambiar;
						$objPerfilGrupo->eliminar = $objPerfil->eliminar;
						$objPerfilGrupo->imprimir = $objPerfil->imprimir;
						$objPerfilGrupo->ejecutar = $objPerfil->ejecutar;
						$objPerfilGrupo->anular = $objPerfil->anular;
						$objPerfilGrupo->administrativo = $objPerfil->administrativo;
						$objPerfilGrupo->ayuda = $objPerfil->ayuda;
						$objPerfilGrupo->cancelar = $objPerfil->cancelar;
						$objPerfilGrupo->enviarcorreo = $objPerfil->enviarcorreo;
						$objPerfilGrupo->descargar = $objPerfil->descargar;
						$objPerfilGrupo->criterio = $objPerfil->criterio;
						$objPerfilGrupo->modificarDerechosGrupos();
						$objPerfil->valido = $objPerfilGrupo->valido;
						unset($objPerfilGrupo);
					}		
				}		
				if ($objPerfil->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objPerfil->valido;
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
				
		case 'actualizarUno':
			$objSistemaVentana->campo = 'cambiar';
			$accionvalida = $objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$i=0;
				$objPerfil->criterio[$i]['operador'] = "AND";
				$objPerfil->criterio[$i]['criterio'] = "codsis";
				$objPerfil->criterio[$i]['condicion'] = "=";
				$objPerfil->criterio[$i]['valor'] = "'".$objdata->codsis."'";
				$i++;
				if (!$blnGrupo)
				{
					$objPerfil->criterio[$i]['operador'] = "AND";
					$objPerfil->criterio[$i]['criterio'] = "codusu";
					$objPerfil->criterio[$i]['condicion'] = "=";
					$objPerfil->criterio[$i]['valor'] = "'".$objPermisos->codusu."'";
				}	
				else
				{
					$objPerfil->criterio[$i]['operador'] = "AND";
					$objPerfil->criterio[$i]['criterio'] = "nomgru";
					$objPerfil->criterio[$i]['condicion'] = "=";
					$objPerfil->criterio[$i]['valor'] = "'".$objPermisos->nomgru."'";

				}				
				$i++;
				$objPerfil->criterio[$i]['operador'] = "AND";
				$objPerfil->criterio[$i]['criterio'] = "codmenu";
				$objPerfil->criterio[$i]['condicion'] = "=";
				$objPerfil->criterio[$i]['valor'] = "'".$objdata->codmenu."'";
				$objPerfil->modificarPerfil();
				if (($blnGrupo)&&($objPerfil->valido))
				{
					$objPerfilGrupo = new DerechosUsuario();	
					$objPerfilGrupo->codemp = $objPerfil->codemp;
					$objPerfilGrupo->nomgru = $objPerfil->nomgru;
					$objPerfilGrupo->nomfisico = $objPerfil->nomfisico;
					$objPerfilGrupo->codsis = $objdata->codsis;
					$objPerfilGrupo->codmenu = $objdata->codmenu;
					$objPerfilGrupo->visible = 1;
					$objPerfilGrupo->enabled = 1;
					$objPerfilGrupo->incluir = $objPerfil->incluir;
					$objPerfilGrupo->leer = $objPerfil->leer;
					$objPerfilGrupo->cambiar = $objPerfil->cambiar;
					$objPerfilGrupo->eliminar = $objPerfil->eliminar;
					$objPerfilGrupo->imprimir = $objPerfil->imprimir;
					$objPerfilGrupo->ejecutar = $objPerfil->ejecutar;
					$objPerfilGrupo->anular = $objPerfil->anular;
					$objPerfilGrupo->administrativo = $objPerfil->administrativo;
					$objPerfilGrupo->ayuda = $objPerfil->ayuda;
					$objPerfilGrupo->cancelar = $objPerfil->cancelar;
					$objPerfilGrupo->enviarcorreo = $objPerfil->enviarcorreo;
					$objPerfilGrupo->descargar = $objPerfil->descargar;
					$objPerfilGrupo->criterio = $objPerfil->criterio;
					$objPerfilGrupo->modificarDerechosGrupos();
					$objPerfil->valido = $objPerfilGrupo->valido;
					unset($objPerfilGrupo);
				}				
				if ($objPerfil->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido'] = $objPerfil->valido;
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
		
		case 'insertarTodas': 
			$objSistemaVentana->campo = 'cambiar';
			$accionvalida  = $objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{				
				$objPerfil->insertarPermisosGlobales();
				if (($blnGrupo)&&($objPerfil->valido))
				{
					$objPerfilGrupo = new DerechosUsuario();	
					$objPerfilGrupo->codemp = $objPerfil->codemp;
					$objPerfilGrupo->nomgru = $objPerfil->nomgru;
					$objPerfilGrupo->nomfisico = $objPerfil->nomfisico;
					$objPerfilGrupo->codintper = $objdata->codintper;
					$objPerfilGrupo->codsis = $objdata->codsis;
					$objPerfilGrupo->insertarPermisosGlobalesGrupo();
					$objPerfil->valido = $objPerfilGrupo->valido;
					unset($objPerfilGrupo);
				}
				if ($objPerfil->valido)
				{
					$objPerfil->modificarDerechos();
					if (($blnGrupo)&&($objPerfil->valido))
					{
						$objPerfilGrupo = new DerechosUsuario();	
						$objPerfilGrupo->codemp = $objPerfil->codemp;
						$objPerfilGrupo->nomgru = $objPerfil->nomgru;
						$objPerfilGrupo->nomfisico = $objPerfil->nomfisico;
						$objPerfilGrupo->codsis = $objdata->codsis;
						$objPerfilGrupo->codmenu = $objdata->codmenu;
						$objPerfilGrupo->visible = 1;
						$objPerfilGrupo->enabled = 1;
						$objPerfilGrupo->incluir = $objPerfil->incluir;
						$objPerfilGrupo->leer = $objPerfil->leer;
						$objPerfilGrupo->cambiar = $objPerfil->cambiar;
						$objPerfilGrupo->eliminar = $objPerfil->eliminar;
						$objPerfilGrupo->imprimir = $objPerfil->imprimir;
						$objPerfilGrupo->ejecutar = $objPerfil->ejecutar;
						$objPerfilGrupo->anular = $objPerfil->anular;
						$objPerfilGrupo->administrativo = $objPerfil->administrativo;
						$objPerfilGrupo->ayuda = $objPerfil->ayuda;
						$objPerfilGrupo->cancelar = $objPerfil->cancelar;
						$objPerfilGrupo->enviarcorreo = $objPerfil->enviarcorreo;
						$objPerfilGrupo->descargar = $objPerfil->descargar;
						$objPerfilGrupo->criterio = $objPerfil->criterio;
						$objPerfilGrupo->modificarDerechosGrupos();
						$objPerfil->valido = $objPerfilGrupo->valido;
						unset($objPerfilGrupo);
					}	
				}			
				if ($objPerfil->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido'] = $objPerfil->valido;
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
		
		case 'buscarUno':
			$i=0;
			$objPerfil->criterio[$i]['operador']  = " AND";
			$objPerfil->criterio[$i]['criterio']  = "codmenu";
			$objPerfil->criterio[$i]['condicion'] = "=";
			$objPerfil->criterio[$i]['valor']     = "'".$objdata->codmenu."'";
		 	$i++;
			$objPerfil->criterio[$i]['operador']  = " AND";
			$objPerfil->criterio[$i]['criterio']  = "codsis";
			$objPerfil->criterio[$i]['condicion'] = "=";
			$objPerfil->criterio[$i]['valor']     = "'".$objdata->codsis."'";
			$datos = $objPerfil->leerUno();
			if ($objPerfil->valido)
			{
				if ((!$datos->EOF)&&($datos!=false))
				{
					$varJson = generarJson($datos);
					echo $varJson;				
				}
				else
				{
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
		
		case 'eliminarTodos':
			$objSistemaVentana->campo = 'eliminar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{				
				$objPerfil->criterio[0]['operador'] = "AND";
				$objPerfil->criterio[0]['criterio'] = "codsis";
				$objPerfil->criterio[0]['condicion'] = "=";
				$objPerfil->criterio[0]['valor'] = "'".$objdata->codsis."'";
				
				if (!$blnGrupo)
				{
					$objPerfil->criterio[1]['operador'] = "AND";
					$objPerfil->criterio[1]['criterio'] = "codusu";
					$objPerfil->criterio[1]['condicion'] = "=";
					$objPerfil->criterio[1]['valor'] = "'".$objPermisos->codusu."'";
				}	
				else
				{
					$objPerfil->criterio[1]['operador'] = "AND";
					$objPerfil->criterio[1]['criterio'] = "nomgru";
					$objPerfil->criterio[1]['condicion'] = "=";
					$objPerfil->criterio[1]['valor'] = "'".$objPermisos->nomgru."'";
				}				
				$objPerfil->eliminarTodos();
				if (($blnGrupo)&&($objPerfil->valido))
				{
					$objPerfilGrupo = new DerechosUsuario();	
					$objPerfilGrupo->codemp = $objPerfil->codemp;
					$objPerfilGrupo->nomgru = $objPerfil->nomgru;
					$objPerfilGrupo->nomfisico = $objPerfil->nomfisico;
					$objPerfilGrupo->codsis = $objdata->codsis;
					$objPerfilGrupo->codmenu = '';
					$objPerfilGrupo->modificarDerechosGrupos();
					$objPerfil->valido = $objPerfilGrupo->valido;
					unset($objPerfilGrupo);
				}
				if ($objPerfil->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objPerfil->valido;		
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
		
		case 'eliminarUno':
			$objSistemaVentana->campo = 'eliminar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{			
				$contador = 0;	
				$objPerfil->criterio[$contador]['operador'] = "AND";
				$objPerfil->criterio[$contador]['criterio'] = "codsis";
				$objPerfil->criterio[$contador]['condicion'] = "=";
				$objPerfil->criterio[$contador]['valor'] = "'".$objdata->codsis."'";
				$contador++;
				
				if (!$blnGrupo)
				{
					$objPerfil->criterio[$contador]['operador'] = "AND";
					$objPerfil->criterio[$contador]['criterio'] = "codusu";
					$objPerfil->criterio[$contador]['condicion'] = "=";
					$objPerfil->criterio[$contador]['valor'] = "'".$objPermisos->codusu."'";
				}	
				else
				{
					$objPerfil->criterio[$contador]['operador'] = "AND";
					$objPerfil->criterio[$contador]['criterio'] = "nomgru";
					$objPerfil->criterio[$contador]['condicion'] = "=";
					$objPerfil->criterio[$contador]['valor'] = "'".$objPermisos->nomgru."'";
				}				
				$contador++;
				
				$objPerfil->criterio[$contador]['operador'] = "AND";
				$objPerfil->criterio[$contador]['criterio'] = "codmenu";
				$objPerfil->criterio[$contador]['condicion'] = "=";
				$objPerfil->criterio[$contador]['valor'] = "'".$objdata->codmenu."'";
				$contador++;

				$objPerfil->eliminarTodos();
				if (($blnGrupo)&&($objPerfil->valido))
				{
					$objPerfilGrupo = new DerechosUsuario();	
					$objPerfilGrupo->codemp = $objPerfil->codemp;
					$objPerfilGrupo->nomgru = $objPerfil->nomgru;
					$objPerfilGrupo->nomfisico = $objPerfil->nomfisico;
					$objPerfilGrupo->codsis = $objdata->codsis;
					$objPerfilGrupo->codmenu = $objdata->codmenu;
					$objPerfilGrupo->modificarDerechosGrupos();
					$objPerfil->valido = $objPerfilGrupo->valido;
					unset($objPerfilGrupo);
				}				
				if ($objPerfil->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{					
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');		
				}
				$arreglo['valido']  = $objPerfil->valido;
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
	unset($objUsuario);
	unset($objGrupo);
	unset($objPerfil);
}	
?>