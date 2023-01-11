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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/apr/sigesp_dao_apr_movimientos.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/cfg/sigesp_srv_cfg_configuracion.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	
	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	$objMovimientos = new Movimientos();		
	$objMovimientos->codemp = $_SESSION['la_empresa']['codemp'];	
	$objMovimientos->codsis = $objdata->sistema;
	$objMovimientos->nomfisico = $objdata->vista;
	$objMovimientos->sistema = strtoupper($objdata->codsis);	
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->operacion;
	switch ($evento)
	{
		case 'verificarMovimientos':
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
			$objConfiguracion->criterio[$i]['valor']= "'MOVIMIENTO'";
			$i++;
			$objConfiguracion->criterio[$i]['operador']= "AND";
			$objConfiguracion->criterio[$i]['criterio']= " entry ";
			$objConfiguracion->criterio[$i]['condicion']= " = ";
			$objConfiguracion->criterio[$i]['valor']= "'MOVIMIENTO'";
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
		
		case 'procesar':
			$objSistemaVentana->campo = 'ejecutar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{
				$fecha=date('d-m-Y');
				$nombrearchivo='../../vista/apr/resultados/';
				$nombrearchivo.=''.$_SESSION['sigesp_basedatos_apr'].'_MOV_'.$objMovimientos->sistema.'_'.$fecha.'.txt';
				$archivo=@fopen($nombrearchivo,'a+');
				$objMovimientos->archivo = $archivo;
				
				$ruta = '../../base/xml/';
				$archivo = 'sigesp_xml_mov_'.strtolower($objMovimientos->sistema).'.xml';
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
								$objMovimientos->tablas[$i]['tipo'] = $valor;
								$objMovimientos->tablas[$i]['valornuevo'] = '';
								
								$campo = $tabla->getElementsByTagName('nombre');	
								$valor= rtrim($campo->item(0)->nodeValue);
								$objMovimientos->tablas[$i]['tabla'] = $valor;
								
								$campo = $tabla->getElementsByTagName('criterio');	
								$valor= rtrim($campo->item(0)->nodeValue);
								$valor = str_replace('DISTINTO', '<>', $valor);
								$valor = str_replace("-", "'", $valor);
								$valor = str_replace(":", "-", $valor);								
								$objMovimientos->tablas[$i]['criterio'] = $valor;
								$i++;
							}
						}
					}					
				}
				$objMovimientos->procesarMovimientos();
				
				if($objMovimientos->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objMovimientos->valido;
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
				$nombrearchivo.='eliminar_movimientos_'.$_SESSION['sigesp_basedatos_apr'].'_'.$objMovimientos->sistema.'_'.$fecha.'.txt';
				$archivo=@fopen($nombrearchivo,'a+');
				$objMovimientos->archivo = $archivo;
				
				$ruta = '../../base/xml/';
				$archivo = 'sigesp_xml_mov_'.strtolower($objMovimientos->sistema).'.xml';
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
							if (($valor == 'DELETE') || ($valor == 'INSERT'))
							{								
								$campo = $tabla->getElementsByTagName('id');	
								$i = rtrim($campo->item(0)->nodeValue);
								
								$campo = $tabla->getElementsByTagName('nombre');	
								$valor= rtrim($campo->item(0)->nodeValue);
								$objMovimientos->tablas[$i]['tabla'] = $valor;
								
								$campo = $tabla->getElementsByTagName('criterio');	
								$valor= rtrim($campo->item(0)->nodeValue);
								$valor = str_replace('DISTINTO', '<>', $valor);
								$valor = str_replace("-", "'", $valor);
								$valor = str_replace(":", "-", $valor);								
								$objMovimientos->tablas[$i]['criterio'] = $valor;
							}
						}
					}
				}																					
				$objMovimientos->eliminarMovimientos();
				if($objMovimientos->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objMovimientos->valido;
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
	unset($objMovimientos);
	unset($objSistemaVentana);
}	
?>
