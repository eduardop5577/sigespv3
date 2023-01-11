<?php
/***************************************************************************** 	
* @Controlador para la definición de Usuario.
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_usuario.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_usuariosistema.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_usuariogrupo.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_derechosusuario.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_permisosinternos.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_crearreporte.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_validaciones.php');
	
	$_SESSION['session_activa'] = time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);
	$objUsuario = new Usuario();
	$arrResultado = pasarDatos($objUsuario,$objdata,$evento);
	$objUsuario = $arrResultado["objDao"];
	$evento = $arrResultado["evento"];

	$objUsuario->codemp = $_SESSION['la_empresa']['codemp'];
	$objUsuario->codsis = $objdata->sistema;
	$objUsuario->nomfisico = $objdata->vista;		
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp    = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu    = $_SESSION['la_logusr']; 	
	$objSistemaVentana->codsis    = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->oper;
	//personal
	if ($objdata->datosAdmin)
	{
		$total = count((array)$objdata->datosAdmin);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->admin[$j] = new PermisosInternos();
			$objUsuario->admin[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$objUsuario->admin[$j]->codsis = $objdata->datosAdmin[$j]->codsis;
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->admin[$j],$objdata->datosAdmin[$j]);	
			$objUsuario->admin[$j] = $arrResultado["objDao"];
		}
	}	
	//constantes
	if ($objdata->datosCons)
	{
		$total = count((array)$objdata->datosCons);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->constante[$j] = new PermisosInternos();
			$objUsuario->constante[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->constante[$j],$objdata->datosCons[$j]);	
			$objUsuario->constante[$j] = $arrResultado["objDao"];
		}
	}
	//nomina
	if ($objdata->datosNom)
	{
		$total = count((array)$objdata->datosNom);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->nomina[$j] = new PermisosInternos();
			$objUsuario->nomina[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->nomina[$j],$objdata->datosNom[$j]);	
			$objUsuario->nomina[$j] = $arrResultado["objDao"];
		}
	}
	//unidades ejecutoras
	if ($objdata->datosUni)
	{
		$total = count((array)$objdata->datosUni);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->unidad[$j] = new PermisosInternos();
			$objUsuario->unidad[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->unidad[$j],$objdata->datosUni[$j]);	
			$objUsuario->unidad[$j] = $arrResultado["objDao"];
		}
	}
	//estructuras presupuestarias
	if ($objdata->datosEstPre)
	{
		$total = count((array)$objdata->datosEstPre);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->estpre[$j] = new PermisosInternos();
			$objUsuario->estpre[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->estpre[$j],$objdata->datosEstPre[$j]);	
			$objUsuario->estpre[$j] = $arrResultado["objDao"];
		}
	}
	//almacen
	if ($objdata->datosAlmacen)
	{
		$total = count((array)$objdata->datosAlmacen);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->almacen[$j] = new PermisosInternos();
			$objUsuario->almacen[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->almacen[$j],$objdata->datosAlmacen[$j]);	
			$objUsuario->almacen[$j] = $arrResultado["objDao"];
		}
	}
	//Centro Costos
	if ($objdata->datosCenCos)
	{
		$total = count((array)$objdata->datosCenCos);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->centrocos[$j] = new PermisosInternos();
			$objUsuario->centrocos[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->centrocos[$j],$objdata->datosCenCos[$j]);	
			$objUsuario->centrocos[$j] = $arrResultado["objDao"];
		}
	}
	//Cuenta Banco
	if ($objdata->datosCtaBan)
	{
		$total = count((array)$objdata->datosCtaBan);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->cuentabanco[$j] = new PermisosInternos();
			$objUsuario->cuentabanco[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->cuentabanco[$j],$objdata->datosCtaBan[$j]);	
			$objUsuario->cuentabanco[$j] = $arrResultado["objDao"];
		}
	}
	if ($objdata->datosEliminar)
	{
		$total = count((array)$objdata->datosEliminar);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->usuariopersonal[$j] = new PermisosInternos();
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->usuariopersonal[$j],$objdata->datosEliminar[$j]);	
			$objUsuario->usuariopersonal[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarCons)
	{
		$total = count((array)$objdata->datosEliminarCons);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->usuarioconstante[$j] = new PermisosInternos();
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->usuarioconstante[$j],$objdata->datosEliminarCons[$j]);	
			$objUsuario->usuarioconstante[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarNom)
	{
		$total = count((array)$objdata->datosEliminarNom);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->usuarionomina[$j] = new PermisosInternos();
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->usuarionomina[$j],$objdata->datosEliminarNom[$j]);	
			$objUsuario->usuarionomina[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarUni)
	{
		$total = count((array)$objdata->datosEliminarUni);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->usuariounidad[$j] = new PermisosInternos();
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->usuariounidad[$j],$objdata->datosEliminarUni[$j]);	
			$objUsuario->usuariounidad[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarPre)
	{
		$total = count((array)$objdata->datosEliminarPre);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->usuarioestpre[$j] = new PermisosInternos();
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->usuarioestpre[$j],$objdata->datosEliminarPre[$j]);	
			$objUsuario->usuarioestpre[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarAlmacen)
	{
		$total = count((array)$objdata->datosEliminarAlmacen);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->usuarioalmacen[$j] = new PermisosInternos();
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->usuarioalmacen[$j],$objdata->datosEliminarAlmacen[$j]);	
			$objUsuario->usuarioalmacen[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarCenCos)
	{
		$total = count((array)$objdata->datosEliminarCenCos);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->usuariocentrocos[$j] = new PermisosInternos();
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->usuariocentrocos[$j],$objdata->datosEliminarCenCos[$j]);	
			$objUsuario->usuariocentrocos[$j] = $arrResultado["objDao"];
		}
	}	
    if ($objdata->datosEliminarCtaBan)
	{
		$total = count((array)$objdata->datosEliminarCtaBan);
		for ($j=0; $j<$total; $j++)
		{
			$objUsuario->usuariocuentabanco[$j] = new PermisosInternos();
			$arrResultado = "";
			$arrResultado = pasarDatos($objUsuario->usuariocuentabanco[$j],$objdata->datosEliminarCtaBan[$j]);	
			$objUsuario->usuariocuentabanco[$j] = $arrResultado["objDao"];
		}
	}	
	
	switch ($evento)
	{
		case 'incluir':	 
			$objSistemaVentana->campo = 'incluir';
			$objUsuario->fecnacusu = convertirFechaBd($objUsuario->fecnacusu);
			$accionvalida   = $objSistemaVentana->verificarUsuario();
			$correcto       = true;//(validaciones($objUsuario->codusu,'30','novacio|caracteres')) && (validaciones($objUsuario->cedusu,'8','novacio|numero')) && (validaciones($objUsuario->nomusu,'100','nombre')) && (validaciones($objUsuario->apeusu,'50','nombre')) && (validaciones($objUsuario->telusu,'20','telefono')) && (validaciones($objUsuario->email,'100','vacioemail')) && (validaciones($objUsuario->nota,'2000','vaciocaracteres'));
			if ($accionvalida)
			{
				if ($correcto)
				{
					$contador=0;
					if ($_SESSION["ls_gestor"]=='POSTGRES')
					{
						$objUsuario->criterio[$contador]['operador'] = "AND";
						$objUsuario->criterio[$contador]['criterio'] = "codusu";
						$objUsuario->criterio[$contador]['condicion'] = " ILIKE ";
						$objUsuario->criterio[$contador]['valor'] = "'%".$objUsuario->codusu."%'";
						$contador++;
					}
					else
					{
						$objUsuario->criterio[$contador]['operador'] = "AND";
						$objUsuario->criterio[$contador]['criterio'] = "codusu";
						$objUsuario->criterio[$contador]['condicion'] = " LIKE ";
						$objUsuario->criterio[$contador]['valor'] = "'%".$objUsuario->codusu."%'";
						$contador++;
					}
					$objUsuario->criterio[$contador]['operador'] = "AND";
					$objUsuario->criterio[$contador]['criterio'] = "cedusu";
					$objUsuario->criterio[$contador]['condicion'] = " = ";
					$objUsuario->criterio[$contador]['valor'] = "'".$objUsuario->cedusu."'";
					$contador++;
					$objUsuario->leer();
					if ($objUsuario->valido)
					{
						if ($objUsuario->existe===false)	
						{								
							$objUsuario->incluir(); 
							if ($objUsuario->valido)
							{
								$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');
							}
							else
							{
								$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
							}
							$arreglo['valido']  = $objUsuario->valido;
						}
						else
						{					
							$arreglo['mensaje'] = obtenerMensaje('REGISTRO_EXISTE');
							$arreglo['existe']  = $objUsuario->existe;
						}
					}
					else
					{
						$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');
						$arreglo['valido']  = $objUsuario->existe;
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
			$objUsuario->fecnacusu = convertirFechaBd($objUsuario->fecnacusu);
			$accionvalida = $objSistemaVentana->verificarUsuario();
			$correcto = true;//(validaciones($objUsuario->cedusu,'8','numero')) && (validaciones($objUsuario->nomusu,'100','nombre')) && (validaciones($objUsuario->apeusu,'50','vaciocaracteres')) && (validaciones($objUsuario->telusu,'20','telefono')) && (validaciones($objUsuario->email,'100','vacioemail')) && (validaciones($objUsuario->nota,'2000','vaciocaracteres'));
			if ($accionvalida)
			{	
				if ($correcto)
				{
					$contador=0;
					$objUsuario->criterio[$contador]['operador'] = "AND";
					$objUsuario->criterio[$contador]['criterio'] = "trim(codusu)";
					$objUsuario->criterio[$contador]['condicion'] = "=";
					$objUsuario->criterio[$contador]['valor'] = "'".trim($objUsuario->codusu)."'";
					$contador++;
					$objUsuario->leer();
					if ($objUsuario->valido)
					{
						if ($objUsuario->existe===true)	
						{						
							$objUsuario->modificarLocal();
							if ($objUsuario->valido)
							{						
								$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');
							}
							else
							{
								$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
							}
							$arreglo['valido']  = $objUsuario->valido;			
						}							
						else
						{
							$arreglo['mensaje'] = obtenerMensaje('REGISTRO_NO_EXISTE');
							$arreglo['valido']  = $objUsuario->existe;
						}						
					}
					else
					{
						$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');
						$arreglo['valido']  = $objUsuario->existe;
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
				
		case 'catalogo':
			$objSistemaVentana->campo = 'leer';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				if ($objdata->campo!='')
				{
					$objUsuario->criterio[0]['operador']  = "AND";
					$objUsuario->criterio[0]['criterio']  = "UPPER({$objdata->campo})";
					$objUsuario->criterio[0]['condicion'] = "like";
					$objUsuario->criterio[0]['valor']     = "UPPER('".$objdata->cadena."%"."')";
				}
				$datos = $objUsuario->leer();
				if ($objUsuario->valido)
				{
					if (!$datos->EOF)
					{
						$varJson = generarJson($datos);
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
		
		case 'catalogoActivos':
			$contador = 0;
			$objUsuario->criterio[$contador]['operador'] = "AND";
			$objUsuario->criterio[$contador]['criterio'] = "estatus";
			$objUsuario->criterio[$contador]['condicion'] = "=";
			$objUsuario->criterio[$contador]['valor'] = "1";
			
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

		case 'catalogodetalle':
			$objUsuarioP = new PermisosInternos();
			$objUsuarioP->codemp = $_SESSION['la_empresa']['codemp'];
			$objUsuarioP->codusu = $objUsuario->codusu;
			$objUsuarioP->tabla = 'sno_tipopersonalsss';
			$objUsuarioP->campo = 'codtippersss';
			$objUsuarioP->campo2 = 'dentippersss';
			$objUsuarioP->sistema = 'SNO';
			$objSonPersonal = generarJson($objUsuarioP->obtenerPermisos());
			
			$objUsuarioP->tabla = 'sno_constante';
			$objUsuarioP->campo = 'codcons';
			$objUsuarioP->campo2 = 'nomcon';
			$objUsuarioP->codusu = $objUsuario->codusu;
			$objUsuarioP->sistema = 'SNO';
			$objSonConstante = generarJson($objUsuarioP->obtenerPermisos());
			
			$objUsuarioP->tabla = 'sno_nomina';
			$objUsuarioP->campo = 'codnom';
			$objUsuarioP->campo2 = 'desnom';
			$objUsuarioP->codusu = $objUsuario->codusu;
			$objUsuarioP->sistema = 'SNO';
			$objSonNomina = generarJson($objUsuarioP->obtenerPermisos());
			
			$objUsuarioP->tabla = 'spg_unidadadministrativa';
			$objUsuarioP->campo = 'coduniadm';
			$objUsuarioP->campo2 = 'denuniadm';
			$objUsuarioP->codusu = $objUsuario->codusu;
			$objUsuarioP->sistema = 'SPG';
			$objSonUnidad = generarJson($objUsuarioP->obtenerPermisos());
			
			
			$objUsuarioP->codusu = $objUsuario->codusu;
			$objSonEstPre = generarJson($objUsuarioP->obtenerEstPre());

			$objUsuarioP->tabla = 'siv_almacen';
			$objUsuarioP->campo = 'codalm';
			$objUsuarioP->campo2 = 'nomfisalm';
			$objUsuarioP->codusu = $objUsuario->codusu;
			$objUsuarioP->sistema = 'SIV';
			$objSonAlmacen = generarJson($objUsuarioP->obtenerPermisos());

			$objUsuarioP->tabla = 'sigesp_cencosto';
			$objUsuarioP->campo = 'codcencos';
			$objUsuarioP->campo2 = 'denominacion';
			$objUsuarioP->codusu = $objUsuario->codusu;
			$objUsuarioP->sistema = 'CFG';
			$objSonCenCos = generarJson($objUsuarioP->obtenerPermisos());

			$objUsuarioP->tabla = 'scb_ctabanco';
			$objUsuarioP->campo = 'codban';
			$objUsuarioP->campo2 = 'ctaban';
			$objUsuarioP->codusu = $objUsuario->codusu;
			$objUsuarioP->sistema = 'SCB';
			$objSonCtaBan = generarJson($objUsuarioP->obtenerPermisos());


			echo "{$objSonPersonal}|{$objSonConstante}|{$objSonNomina}|{$objSonUnidad}|{$objSonEstPre}|{$objSonAlmacen}|{$objSonCenCos}|{$objSonCtaBan}";
		break;
		
		case 'eliminar':
			$objSistemaVentana->campo = 'eliminar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$objUsuarioSistema = new UsuarioSistema();
				$objUsuarioSistema->codemp = $_SESSION['la_empresa']['codemp'];
				$objUsuarioSistema->codusu = $objUsuario->codusu;
				$objUsuarioSistema->buscarUsuarioSistema();
				if ($objUsuarioSistema->existe===false)
				{					
					$objUsuarioGrupo = new UsuarioGrupo();
					$objUsuarioGrupo->codemp = $_SESSION['la_empresa']['codemp'];
					$objUsuarioGrupo->codusu = $objUsuario->codusu;
					$objUsuarioGrupo->buscarUsuarioGrupo();
					if ($objUsuarioGrupo->existe===false)
					{				
						$objUsuario->usuariodetalle[0] = new PermisosInternos();
						$objUsuario->usuariodetalle[0]->codemp = $_SESSION['la_empresa']['codemp'];
						$objUsuario->usuariodetalle[0]->nomfisico = $objdata->vista;
												
						$objUsuario->eliminarLocal();
						if ($objUsuario->valido)
						{
						$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
						}
						else
						{
							$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
						}
						$arreglo['valido']  = $objUsuario->valido;	
										
					}
					else
					{
						$arreglo['mensaje'] = obtenerMensaje('RELACION_OTRAS_TABLAS','Grupos');
						$arreglo['existe']  = $objUsuario->existe;
					}	
				}	
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('RELACION_OTRAS_TABLAS','Sistema');
					$arreglo['existe']  = $objUsuario->existe;
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
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$objReporte = new crearReporte();
				$objReporte->codsis = strtolower($objdata->codsis);
				$objUsuario->cadena = $objdata->codusu;
				$datos = $objUsuario->leer(); 
				if (count((array)$datos)>0)
				{
					$objReporte->crearXml('datos_usuario',$datos);
					
					$objUsuarioP = new PermisosInternos();
					$objUsuarioP->codemp = $_SESSION['la_empresa']['codemp'];
					$objUsuarioP->codusu = $objdata->codusu;
					$objUsuarioP->tabla = 'sno_tipopersonalsss';
					$objUsuarioP->campo = 'codtippersss';
					$objUsuarioP->campo2 = 'dentippersss';
					$objUsuarioP->sistema = 'SNO';
					$datosPer = $objUsuarioP->obtenerPermisos();
									
					$objUsuarioP->tabla = 'sno_constante';
					$objUsuarioP->campo = 'codcons';
					$objUsuarioP->campo2 = 'nomcon';
					$objUsuarioP->codusu = $objdata->codusu;
					$objUsuarioP->sistema = 'SNO';
					$datosCons = $objUsuarioP->obtenerPermisos();
					
					$objUsuarioP->tabla = 'sno_nomina';
					$objUsuarioP->campo = 'codnom';
					$objUsuarioP->campo2 = 'desnom';
					$objUsuarioP->codusu = $objdata->codusu;
					$objUsuarioP->sistema = 'SNO';
					$datosNom = $objUsuarioP->obtenerPermisos();
					
					$objUsuarioP->tabla = 'spg_unidadadministrativa';
					$objUsuarioP->campo = 'coduniadm';
					$objUsuarioP->campo2 = 'denuniadm';
					$objUsuarioP->codusu = $objdata->codusu;
					//$objUsuarioP->sistema = 'SPG';
					$datosUni = $objUsuarioP->obtenerPermisos();
					
					$objUsuarioP->codusu = $objdata->codusu;
					$datosPre = $objUsuarioP->obtenerEstPre();
					
					$objReporte->crearXml('personal_usuario',$datosPer);
					$objReporte->crearXml('constantes_usuario',$datosCons);
					$objReporte->crearXml('nominas_usuario',$datosNom);
					$objReporte->crearXml('unidades_usuario',$datosUni);
					$objReporte->crearXml('presupuestos_usuario',$datosPre);
					
					$objReporte->nomRep='ficha_usuario';				
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
				$arreglo['mensaje'] = obtenerMensaje('ACCION_NO_VALIDA'); 
				$arreglo['valido']  = false;
				$respuesta  = array('raiz'=>$arreglo);			
				$respuesta  = json_encode($respuesta);
				echo $respuesta;
			}							
	}
	unset($objSistemaVentana);
	unset($objUsuario);
	//unset($objPerfil);
	unset($objUsuarioGrupo);
	unset($objUsuarioSistema);
	unset($objUsuarioP);
}

?>

