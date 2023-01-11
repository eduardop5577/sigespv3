<?php
/***********************************************************************************
* @Clase para Manejar  para la definición de Grupo.
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_grupo.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_usuariogrupo.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_derechosgrupo.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_permisosinternos_grupo.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	
	$_SESSION['session_activa'] = time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);
	$objGrupo = new Grupo();
	$arrResultado = pasarDatos($objGrupo,$objdata,$evento);
	$objGrupo = $arrResultado["objDao"];
	$evento = $arrResultado["evento"];
	$objGrupo->codemp = $_SESSION['la_empresa']['codemp'];	
	$objGrupo->codsis = $objdata->sistema;	
	$objGrupo->nomfisico = $objdata->vista;	
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->oper;
	// Cargamos los usuarios que se agregaron al grupo
	if ($objdata->datosAdmin)
	{
		$total = count((array)$objdata->datosAdmin);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->admin[$j] = new UsuarioGrupo();
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->admin[$j],$objdata->datosAdmin[$j]);	
			$objGrupo->admin[$j] = $arrResultado["objDao"];
		}
	}
	// Cargamos los usuarios que se eliminaron al grupo
	if ($objdata->datosEliminar)
	{
		$total = count((array)$objdata->datosEliminar);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->usuarioeliminar[$j] = new UsuarioGrupo();
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->usuarioeliminar[$j],$objdata->datosEliminar[$j]);	
			$objGrupo->usuarioeliminar[$j] = $arrResultado["objDao"];
		}
	}
	//personal	
	if ($objdata->datosPer)
	{
		$total = count((array)$objdata->datosPer);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->personal[$j] = new PermisosInternosGrupo();
			$objGrupo->personal[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$objGrupo->personal[$j]->codsis = $objdata->datosPer[$j]->codsis;
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->personal[$j],$objdata->datosPer[$j]);	
			$objGrupo->personal[$j] = $arrResultado["objDao"];
		}
	}	
	//constantes
	if ($objdata->datosCons)
	{
		$total = count((array)$objdata->datosCons);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->constante[$j] = new PermisosInternosGrupo();
			$objGrupo->constante[$j]->codemp = $_SESSION['la_empresa']['codemp'];
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->constante[$j],$objdata->datosCons[$j]);	
			$objGrupo->constante[$j] = $arrResultado["objDao"];
		}
	}
	//nomina
	if ($objdata->datosNom)
	{
		$total = count((array)$objdata->datosNom);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->nomina[$j] = new PermisosInternosGrupo();
			$objGrupo->nomina[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->nomina[$j],$objdata->datosNom[$j]);	
			$objGrupo->nomina[$j] = $arrResultado["objDao"];
		}
	}
	//unidades ejecutoras
	if ($objdata->datosUni)
	{
		$total = count((array)$objdata->datosUni);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->unidad[$j] = new PermisosInternosGrupo();
			$objGrupo->unidad[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->unidad[$j],$objdata->datosUni[$j]);	
			$objGrupo->unidad[$j] = $arrResultado["objDao"];
		}
	}
	//estructuras presupuestarias
	if ($objdata->datosEstPre)
	{
		$total = count((array)$objdata->datosEstPre);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->estpre[$j] = new PermisosInternosGrupo();
			$objGrupo->estpre[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->estpre[$j],$objdata->datosEstPre[$j]);	
			$objGrupo->estpre[$j] = $arrResultado["objDao"];
		}
	}
	//almacen
	if ($objdata->datosAlmacen)
	{
		$total = count((array)$objdata->datosAlmacen);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->almacen[$j] = new PermisosInternosGrupo();
			$objGrupo->almacen[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->almacen[$j],$objdata->datosAlmacen[$j]);	
			$objGrupo->almacen[$j] = $arrResultado["objDao"];
		}
	}
	//Centro Costos
	if ($objdata->datosCenCos)
	{
		$total = count((array)$objdata->datosCenCos);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->centrocos[$j] = new PermisosInternosGrupo();
			$objGrupo->centrocos[$j]->codemp = $_SESSION['la_empresa']['codemp'];	
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->centrocos[$j],$objdata->datosCenCos[$j]);	
			$objGrupo->centrocos[$j] = $arrResultado["objDao"];
		}
	}

	if ($objdata->datosEliminarPer)
	{
		$total = count((array)$objdata->datosEliminarPer);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->grupopersonal[$j] = new PermisosInternosGrupo();
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->grupopersonal[$j],$objdata->datosEliminarPer[$j]);	
			$objGrupo->grupopersonal[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarCons)
	{
		$total = count((array)$objdata->datosEliminarCons);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->grupoconstante[$j] = new PermisosInternosGrupo();
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->grupoconstante[$j],$objdata->datosEliminarCons[$j]);	
			$objGrupo->grupoconstante[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarNom)
	{
		$total = count((array)$objdata->datosEliminarNom);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->gruponomina[$j] = new PermisosInternosGrupo();
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->gruponomina[$j],$objdata->datosEliminarNom[$j]);	
			$objGrupo->gruponomina[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarUni)
	{
		$total = count((array)$objdata->datosEliminarUni);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->grupounidad[$j] = new PermisosInternosGrupo();
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->grupounidad[$j],$objdata->datosEliminarUni[$j]);	
			$objGrupo->grupounidad[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarPre)
	{
		$total = count((array)$objdata->datosEliminarPre);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->grupoestpre[$j] = new PermisosInternosGrupo();
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->grupoestpre[$j],$objdata->datosEliminarPre[$j]);	
			$objGrupo->grupoestpre[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarAlmacen)
	{
		$total = count((array)$objdata->datosEliminarAlmacen);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->grupoalmacen[$j] = new PermisosInternosGrupo();
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->grupoalmacen[$j],$objdata->datosEliminarAlmacen[$j]);	
			$objGrupo->grupoalmacen[$j] = $arrResultado["objDao"];
		}
	}	
	if ($objdata->datosEliminarCenCos)
	{
		$total = count((array)$objdata->datosEliminarCenCos);
		for ($j=0; $j<$total; $j++)
		{
			$objGrupo->grupocentrocos[$j] = new PermisosInternosGrupo();
			$arrResultado = "";
			$arrResultado = pasarDatos($objGrupo->grupocentrocos[$j],$objdata->datosEliminarCenCos[$j]);	
			$objGrupo->grupocentrocos[$j] = $arrResultado["objDao"];
		}
	}	

	switch ($evento)
	{
		case 'incluir':	 
			$objSistemaVentana->campo = 'incluir';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			$correcto = (validaciones($objGrupo->nomgru,'60','alfanumerico'));
			if ($accionvalida)
			{
				if ($correcto)
				{
					if ($_SESSION["ls_gestor"]=='POSTGRES')
					{
						$objGrupo->criterio[0]['operador']  = "AND";
						$objGrupo->criterio[0]['criterio']  = "nomgru";
						$objGrupo->criterio[0]['condicion'] = " ILIKE ";
						$objGrupo->criterio[0]['valor']     = "'%".$objdata->nomgru."%'";
					}
					else
					{
						$objGrupo->criterio[0]['operador']  = "AND";
						$objGrupo->criterio[0]['criterio']  = "nomgru";
						$objGrupo->criterio[0]['condicion'] = " LIKE ";
						$objGrupo->criterio[0]['valor']     = "'%".$objdata->nomgru."%'";
					}
					$objGrupo->leer();
					if($objGrupo->valido)
					{
						if ($objGrupo->existe===false)
						{
							$objGrupo->incluirLocal();
							if($objGrupo->valido)
							{
								$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
							}
							else
							{
								$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
							}
							$arreglo['valido']  = $objGrupo->valido;
						}
						else
						{
							$arreglo['valido']  = false;
							$arreglo['mensaje'] = obtenerMensaje('REGISTRO_EXISTE');	
						}
					}
					else
					{				
						$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');
						$arreglo['valido']  = $objGrupo->existe;
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
			if ($accionvalida)
			{	
				$objGrupo->criterio[0]['operador']  = "AND";
				$objGrupo->criterio[0]['criterio']  = "nomgru";
				$objGrupo->criterio[0]['condicion'] = "=";
				$objGrupo->criterio[0]['valor']     = "'".$objdata->nomgru."'";
				$objGrupo->leer();
				if($objGrupo->valido)
				{
					if ($objGrupo->existe===true)
					{
						$objGrupo->modificarLocal();
						if($objGrupo->valido)
						{
							$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
						}
						else
						{
							$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
						}
						$arreglo['valido']  = $objGrupo->valido;
					}
					else
					{
						$arreglo['mensaje'] = obtenerMensaje('REGISTRO_NO_EXISTE'); 
						$arreglo['valido']  = $objGrupo->valido;
					}					
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');
					$arreglo['valido']  = $objGrupo->valido;
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
				$objGrupo->criterio[0]['operador']  = "AND";
				$objGrupo->criterio[0]['criterio']  = "nomgru";
				$objGrupo->criterio[0]['condicion'] = "=";
				$objGrupo->criterio[0]['valor']     = "'".$objdata->nomgru."'";
				$objGrupo->leer();
				if($objGrupo->valido)
				{
					if ($objGrupo->existe===true)
					{
						
						$objGrupo->usuarioeliminar[0] = new UsuarioGrupo();						
						$objGrupo->grupodetalle[0] = new PermisosInternosGrupo();
											
						
						$objGrupo->grupodetalle[0]->criterio[0]['operador']  = "AND";
						$objGrupo->grupodetalle[0]->criterio[0]['criterio']  = "nomgru";
						$objGrupo->grupodetalle[0]->criterio[0]['condicion'] = "=";
						$objGrupo->grupodetalle[0]->criterio[0]['valor']     = "'".$objdata->nomgru."'";
						
						$objGrupo->eliminarLocal();
						if($objGrupo->valido)
						{
							$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
						}
						else
						{
							$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
						}
						$arreglo['valido']  = $objGrupo->valido;					
					}
					else 
					{
						$arreglo['mensaje'] = obtenerMensaje('REGISTRO_NO_EXISTE');
						$arreglo['existe']  = $objGrupo->existe;
					}
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');
					$arreglo['valido']  = $objGrupo->existe;
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
					$objGrupo->criterio[0]['operador']  = "AND";
					$objGrupo->criterio[0]['criterio']  = "UPPER({$objdata->campo})";
					$objGrupo->criterio[0]['condicion'] = "like";
					$objGrupo->criterio[0]['valor']     = "UPPER('".$objdata->cadena."%"."')";
				}
				$datos = $objGrupo->leer();
				if($objGrupo->valido)
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
			
		case 'catalogousuarios':
			$datos = $objGrupo->obtenerUsuarios();
			if($objGrupo->valido)
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
			$objGrupoDetalle = new PermisosInternosGrupo();
			$objGrupoDetalle->codemp = $_SESSION['la_empresa']['codemp'];
			$objGrupoDetalle->nomgru = $objdata->nomgru;
			$objGrupoDetalle->tabla = 'sno_tipopersonalsss';
			$objGrupoDetalle->campo = 'codtippersss';
			$objGrupoDetalle->campo2 = 'dentippersss';
			$objGrupoDetalle->sistema = 'SNO';
			$objSonPersonal = generarJson($objGrupoDetalle->obtenerPermisos());
			
			$objGrupoDetalle->tabla = 'sno_constante';
			$objGrupoDetalle->campo = 'codcons';
			$objGrupoDetalle->campo2 = 'nomcon';
			$objGrupoDetalle->nomgru = $objdata->nomgru;
			$objGrupoDetalle->sistema = 'SNO';
			$objSonConstante = generarJson($objGrupoDetalle->obtenerPermisos());
			
			$objGrupoDetalle->tabla = 'sno_nomina';
			$objGrupoDetalle->campo = 'codnom';
			$objGrupoDetalle->campo2 = 'desnom';
			$objGrupoDetalle->nomgru = $objdata->nomgru;
			$objGrupoDetalle->sistema = 'SNO';
			$objSonNomina = generarJson($objGrupoDetalle->obtenerPermisos());
			
			$objGrupoDetalle->tabla = 'spg_unidadadministrativa';
			$objGrupoDetalle->campo = 'coduniadm';
			$objGrupoDetalle->nomgru = $objdata->nomgru;
			$objGrupoDetalle->campo2 = 'denuniadm';
			$objSonUnidad = generarJson($objGrupoDetalle->obtenerPermisos());
						
			$objGrupoDetalle->nomgru = $objdata->nomgru;
			$objSonEstPre = generarJson($objGrupoDetalle->obtenerEstPre());


			$objGrupoDetalle->tabla = 'siv_almacen';
			$objGrupoDetalle->campo = 'codalm';
			$objGrupoDetalle->campo2 = 'nomfisalm';
			$objGrupoDetalle->codusu = $objdata->nomgru;
			$objGrupoDetalle->sistema = 'SIV';
			$objSonAlmacen = generarJson($objGrupoDetalle->obtenerPermisos());

			$objGrupoDetalle->tabla = 'sigesp_cencosto';
			$objGrupoDetalle->campo = 'codcencos';
			$objGrupoDetalle->campo2 = 'denominacion';
			$objGrupoDetalle->codusu = $objdata->nomgru;
			$objGrupoDetalle->sistema = 'CFG';
			$objSonCenCos = generarJson($objGrupoDetalle->obtenerPermisos());


			echo "{$objSonPersonal}|{$objSonConstante}|{$objSonNomina}|{$objSonUnidad}|{$objSonEstPre}|{$objSonAlmacen}|{$objSonCenCos}";
		break;
		
		case 'reporteficha':
			$objSistemaVentana->campo = 'imprimir';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$objReporte = new crearReporte();
				$objReporte->codsis = strtolower($objdata->codsis);
				$data = $objGrupo->obtenerUsuarios();  
				
				$objPermisos = new PermisosInternosGrupo();
				$objPermisos->codemp = $_SESSION['la_empresa']['codemp'];
				$objPermisos->nomgru = $objdata->nomgru;
				$objPermisos->tabla = 'sno_tipopersonalsss';
				$objPermisos->campo = 'codtippersss';
				$objPermisos->campo2 = 'dentippersss';
				$objPermisos->sistema = 'SNO';
				$datosPer = $objPermisos->obtenerPermisos();
				
				$objPermisos->tabla = 'sno_constante';
				$objPermisos->campo = 'codcons';
				$objPermisos->campo2 = 'nomcon';
				$objPermisos->nomgru = $objdata->nomgru;
				$objPermisos->sistema = 'SNO';
				$datosCons = $objPermisos->obtenerPermisos();
				
				$objPermisos->tabla = 'sno_nomina';
				$objPermisos->campo = 'codnom';
				$objPermisos->campo2 = 'desnom';
				$objPermisos->nomgru = $objdata->nomgru;
				$objPermisos->sistema = 'SNO';
				$datosNom = $objPermisos->obtenerPermisos();
				
				$objPermisos->tabla = 'spg_unidadadministrativa';
				$objPermisos->campo = 'coduniadm';
				$objPermisos->nomgru = $objdata->nomgru;
				$objPermisos->campo2 = 'denuniadm';
				$objPermisos->sistema = 'SPG';
				$datosUni = $objPermisos->obtenerPermisos();
				
				$objPermisos->nomgru = $objdata->nomgru;
				$datosPre = $objPermisos->obtenerEstPre();
				
				$objReporte->crearXml('usuarios',$data);
				$objReporte->crearXml('personal',$datosPer);
				$objReporte->crearXml('constantes',$datosCons);
				$objReporte->crearXml('nominas',$datosNom);
				$objReporte->crearXml('unidades',$datosUni);
				$objReporte->crearXml('presupuestos',$datosPre);
				
				$objReporte->nomRep='ficha_grupo';
				echo $objReporte->mostrarReporte();	
				unset($objReporte);
			}
			else
			{
				$arreglo[0]['mensaje'] = obtenerMensaje('ACCION_NO_VALIDA'); 
				$arreglo[0]['valido']  = false;
				$respuesta  = array('raiz'=>$arreglo);			
				$respuesta  = json_encode($respuesta);
				echo $respuesta;
			}						
	}
	unset($objSistemaVentana);
	unset($objGrupo);
}	
?>
