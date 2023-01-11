<?php
/***********************************************************************************
* @Clase para manejar el traspaso de los datos básicos
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/apr/sigesp_dao_apr_datos_basicos.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/cfg/sigesp_srv_cfg_configuracion.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sno/sigesp_dao_sno_nomina.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	
	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	$objDatosBasicos = new DatosBasicos();		
	$objDatosBasicos->codemp = $_SESSION['la_empresa']['codemp'];	
	$objDatosBasicos->codsis = $objdata->sistema;
	$objDatosBasicos->nomfisico = $objdata->vista;
	$objDatosBasicos->conversion = (bool)$objdata->conversion;
	$objDatosBasicos->bsf = (bool)$objdata->bsf;
	$objDatosBasicos->v2 = (bool)$objdata->v2;
	$objDatosBasicos->reiniciar = (bool)$objdata->reiniciar;
	$objDatosBasicos->sistema = strtoupper($objdata->codsis);	
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->operacion;
	switch ($evento)
	{
		case 'verificarapertura':
			$objConfiguracion = new Configuracion();
			$i=0;
			$objConfiguracion->criterio[$i]['operador']= "WHERE";
			$objConfiguracion->criterio[$i]['criterio']= " codemp ";
			$objConfiguracion->criterio[$i]['condicion']= " = ";
			$objConfiguracion->criterio[$i]['valor']= "'".$_SESSION['la_empresa']['codemp']."'";
			$i++;
			$objConfiguracion->criterio[$i]['operador']= "AND";
			$objConfiguracion->criterio[$i]['criterio']= " seccion ";
			$objConfiguracion->criterio[$i]['condicion']= " = ";
			$objConfiguracion->criterio[$i]['valor']= "'APERTURA'";
			$i++;
			$objConfiguracion->criterio[$i]['operador']= "AND";
			$objConfiguracion->criterio[$i]['criterio']= " entry ";
			$objConfiguracion->criterio[$i]['condicion']= " = ";
			$objConfiguracion->criterio[$i]['valor']= "'APERTURA'";
			$datos = $objConfiguracion->leer();
			if($objConfiguracion->valido)
			{
				if (!$datos->EOF)
				{
					$varJson=generarJson($datos);
					echo $varJson;				
				}
				else
				{
					$arreglo[0]['codsis']  = '';
					$arreglo[0]['valido']  = true;
					$respuesta  = array('raiz'=>$arreglo);
					$respuesta  = json_encode($respuesta);
					echo $respuesta;
				}
				$datos->Close();
			}
			else 
			{	
				$arreglo[0]['mensaje'] = obtenerMensaje('OPERACION_FALLIDA'); 
				$arreglo[0]['valido']  = false;
				$respuesta  = array('raiz'=>$arreglo);
				$respuesta  = json_encode($respuesta);
				echo $respuesta;
			}
			unset($objConfiguracion);
		break;
		
		case 'obtenerDatosNomina':
			$objNomina = new Nomina();
			$objNomina->codemp=$_SESSION['la_empresa']['codemp'];
			$objNomina->servidor=$_SESSION['sigesp_servidor_apr'];
			$objNomina->usuario=$_SESSION['sigesp_usuario_apr'];
			$objNomina->clave=$_SESSION['sigesp_clave_apr'];
			$objNomina->basedatos=$_SESSION['sigesp_basedatos_apr'];
			$objNomina->gestor=$_SESSION['sigesp_gestor_apr'];
			$objNomina->puerto=$_SESSION['sigesp_puerto_apr'];
			$objNomina->tipoconexionbd='ALTERNA';
			$datos = $objNomina->leer();
			if($objNomina->valido)
			{
				if (!$datos->EOF)
				{
					$varJson=generarJson($datos);
					echo $varJson;				
				}
				else
				{
					$arreglo[0]['codsis']  = '';
					$arreglo[0]['valido']  = true;
					$respuesta  = array('raiz'=>$arreglo);
					$respuesta  = json_encode($respuesta);
					echo $respuesta;
				}
				$datos->Close();
			}
			else 
			{	
				$arreglo[0]['mensaje'] = obtenerMensaje('OPERACION_FALLIDA'); 
				$arreglo[0]['valido']  = false;
				$respuesta  = array('raiz'=>$arreglo);
				$respuesta  = json_encode($respuesta);
				echo $respuesta;
			}
			unset($objNomina);
		break;
		
		case 'procesar':
			$objSistemaVentana->campo = 'ejecutar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$fecha=date('d-m-Y');
				$nombrearchivo='../../vista/apr/resultados/';
				$nombrearchivo.=$_SESSION['sigesp_basedatos_apr'].'_'.$objDatosBasicos->sistema.'_'.$fecha.'.txt';
				$archivo=@fopen($nombrearchivo,'a+');
				$objDatosBasicos->archivo = $archivo;
				
				$ruta = '../../base/xml/';
				$archivo = 'sigesp_xml_apr_'.strtolower($objDatosBasicos->sistema).'.xml';
				$i=0;
				$documentoxml = abrirArchivoXml($ruta,$archivo);
				if ($documentoxml != null)
				{
					$tablas = $documentoxml->getElementsByTagName('tabla');
					if($tablas)
					{ 
						foreach ($tablas as $tabla)
						{	
							$campo = $tabla->getElementsByTagName('tipo');	
							$valor= rtrim($campo->item(0)->nodeValue);
							if (($valor == 'UPDATE') || ($valor == 'INSERT'))
							{
								$objDatosBasicos->tablas[$i]['tipo'] = $valor;
								$objDatosBasicos->tablas[$i]['valornuevo'] = '';
								
								$campo = $tabla->getElementsByTagName('nombre');	
								$valor= rtrim($campo->item(0)->nodeValue);
								$objDatosBasicos->tablas[$i]['tabla'] = $valor;
								
								$campo = $tabla->getElementsByTagName('criterio');	
								$valor= rtrim($campo->item(0)->nodeValue);
								$valor = str_replace('DISTINTO', '<>', $valor);
								$valor = str_replace("-", "'", $valor);
								$valor = str_replace(":", "-", $valor);								
								$objDatosBasicos->tablas[$i]['criterio'] = $valor;
								$i++;
							}
							if((strtolower($objDatosBasicos->sistema)==='sss')&&($objDatosBasicos->v2))
							{
								$campo = $tabla->getElementsByTagName('nombre');	
								$valor= rtrim($campo->item(0)->nodeValue);
								if(($valor==='sss_derechos_usuarios')||($valor==='sss_derechos_grupos')||($valor==='sss_sistemas_ventanas'))
								{
									$objDatosBasicos->tablas[$i]['tipo'] = 'INSERT';
									$objDatosBasicos->tablas[$i]['valornuevo'] = '';									
									$objDatosBasicos->tablas[$i]['tabla'] = $valor;
									$criterio='';
									if($valor==='sss_derechos_usuarios')
									{
										$criterio="WHERE codusu<>'apertura' AND codsis<>'SFP'";
									}
									if($valor==='sss_sistemas_ventanas')
									{
										$criterio="WHERE codsis<>'APR' AND codsis<>'SFP'";
									}
									$objDatosBasicos->tablas[$i]['criterio'] = $criterio;
									$i++;
								}
							}
						}
					}					
				}
				if ($objDatosBasicos->sistema == 'HIS')
				{
					$objDatosBasicos->sistema = 'SNR';
				}
				$objDatosBasicos->procesarDatosBasicos();
				
				if($objDatosBasicos->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objDatosBasicos->valido;
				
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

		case 'procesarsno':
			$objSistemaVentana->campo = 'ejecutar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$fecha=date('d-m-Y');
				$nombrearchivo='../../vista/apr/resultados/';
				$nombrearchivo.=$_SESSION['sigesp_basedatos_apr'].'_'.$objDatosBasicos->sistema.'_'.$fecha.'.txt';
				$archivo=@fopen($nombrearchivo,'a+');
				$objDatosBasicos->archivo = $archivo;
				
				$ruta = '../../base/xml/';
				$archivo = 'sigesp_xml_apr_'.strtolower($objDatosBasicos->sistema).'.xml';
				$i=0;
				$conthistorico=0;
				$documentoxml = abrirArchivoXml($ruta,$archivo);
				if ($documentoxml != null)
				{
					$tablas = $documentoxml->getElementsByTagName('tabla');
					if($tablas)
					{ 
						foreach ($tablas as $tabla)
						{	
							$campo = $tabla->getElementsByTagName('tipo');	
							$valor= rtrim($campo->item(0)->nodeValue);
							if (($valor == 'UPDATE') || ($valor == 'INSERT') || ($valor == 'HISTORICO'))
							{
								if ($valor == 'HISTORICO')
								{
									$objDatosBasicos->historicos[$conthistorico]['tipo'] = 'INSERT';
									$campo = $tabla->getElementsByTagName('nombre');	
									$valor= rtrim($campo->item(0)->nodeValue);
									$objDatosBasicos->historicos[$conthistorico]['tabla'] = $valor;
									$conthistorico++;
								}
								else
								{
									$objDatosBasicos->tablas[$i]['tipo'] = $valor;
									$objDatosBasicos->tablas[$i]['valornuevo'] = '';
									$campo = $tabla->getElementsByTagName('nombre');	
									$valor= rtrim($campo->item(0)->nodeValue);
									$objDatosBasicos->tablas[$i]['tabla'] = $valor;
									
									$campo = $tabla->getElementsByTagName('criterio');	
									$valor= rtrim($campo->item(0)->nodeValue);
									$valor = str_replace('DISTINTO', '<>', $valor);
									$valor = str_replace("-", "'", $valor);
									$valor = str_replace(":", "-", $valor);								
									$objDatosBasicos->tablas[$i]['criterio'] = $valor;
									$i++;
								}
							}
						}
					}					
				}
				$total=	count((array)$objdata->datosNomina);
				
				for ($contador=0; $contador < $total; $contador++)
				{
					$objDatosBasicos->nominas[$contador] = new Nomina();
					$objDatosBasicos->nominas[$contador]->codnom = $objdata->datosNomina[$contador]->codnom;
					$objDatosBasicos->nominas[$contador]->codnuenom = $objdata->datosNomina[$contador]->codnuenom;
					
					foreach ($tablas as $tabla)
					{	
						$campo = $tabla->getElementsByTagName('tipo');	
						$valor= rtrim($campo->item(0)->nodeValue);
						$nombre = $tabla->getElementsByTagName('nombre');	
						$tablanombre= rtrim($nombre->item(0)->nodeValue);
						if(($objDatosBasicos->conversion)&&($tablanombre=='sno_periodo'))
						{
							$valor = 'NOMINA';
						}
						if (($valor == 'NOMINA'))
						{
							$valor = 'INSERT';
							$objDatosBasicos->tablas[$i]['tipo'] = 'INSERT';
							$campo = $tabla->getElementsByTagName('nombre');	
							$valor= rtrim($campo->item(0)->nodeValue);
							$objDatosBasicos->tablas[$i]['tabla'] = $valor;
							$objDatosBasicos->tablas[$i]['valornuevo'] = str_pad($objdata->datosNomina[$contador]->codnuenom,4,'0',0);
							$campo = $tabla->getElementsByTagName('criterio');	
							$valor= rtrim($campo->item(0)->nodeValue);
							$valor = str_replace('DISTINTO', '<>', $valor);
							$valor = str_replace("-", "'", $valor);
							$valor = str_replace(":", "-", $valor);								
							if ($valor == '')
							{
								$objDatosBasicos->tablas[$i]['criterio'] = " WHERE codnom = '".str_pad($objdata->datosNomina[$contador]->codnom,4,'0',0)."' ";
							}
							else
							{
								$objDatosBasicos->tablas[$i]['criterio'] = $valor." AND codnom = '".str_pad($objdata->datosNomina[$contador]->codnom,4,'0',0)."' ";
							}
							$i++;
						}
					}
				} 
				$objDatosBasicos->fecinisem=$objdata->fecinisem;
				$objDatosBasicos->fecinimen=$objdata->fecinimen;
				$objDatosBasicos->prestamosactivos=$objdata->prestamosactivos;				
				$objDatosBasicos->procesarDatosBasicos();
				if($objDatosBasicos->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objDatosBasicos->valido;
				
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
			$objSistemaVentana->campo = 'ejecutar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$fecha=date('d-m-Y');
				$nombrearchivo='../../vista/apr/resultados/';
				$nombrearchivo.=$_SESSION['sigesp_basedatos_apr'].'_'.$objDatosBasicos->sistema.'_'.$fecha.'.txt';
				$archivo=@fopen($nombrearchivo,'a+');
				$objDatosBasicos->archivo = $archivo;
				
				$ruta = '../../base/xml/';
				$archivo = 'sigesp_xml_apr_'.strtolower($objDatosBasicos->sistema).'.xml';
				$documentoxml = abrirArchivoXml($ruta,$archivo);
				if ($documentoxml != null)
				{
					$tablas = $documentoxml->getElementsByTagName('tabla');
					if($tablas)
					{ 
						foreach ($tablas as $tabla)
						{	
							$campo = $tabla->getElementsByTagName('tipo');	
							$valor= rtrim($campo->item(0)->nodeValue);
							if (($valor == 'DELETE') || ($valor == 'INSERT') || ($valor == 'NOMINA') || ($valor == 'HISTORICO'))
							{								
								$campo = $tabla->getElementsByTagName('id');	
								$i = rtrim($campo->item(0)->nodeValue);
								
								$campo = $tabla->getElementsByTagName('nombre');	
								$valor= rtrim($campo->item(0)->nodeValue);
								$objDatosBasicos->tablas[$i]['tabla'] = $valor;
								if(($valor==='sss_sistemas_ventanas')&&(strtolower($objDatosBasicos->sistema)==='sss')&&($objDatosBasicos->v2))
								{
									$valor="WHERE codsis<>'APR' AND codsis<>'SPS'";
								}
								else
								{
									$campo = $tabla->getElementsByTagName('criterio');	
									$valor= rtrim($campo->item(0)->nodeValue);
									$valor = str_replace('DISTINTO', '<>', $valor);
									$valor = str_replace("-", "'", $valor);
									$valor = str_replace(":", "-", $valor);	
								}							
								$objDatosBasicos->tablas[$i]['criterio'] = $valor;
								
							}
						}
					}
				}																					
				$objDatosBasicos->eliminarDatosBasicos();
				if($objDatosBasicos->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objDatosBasicos->valido;
				
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
	unset($objDatosBasicos);
	unset($objSistemaVentana);
}	
?>
