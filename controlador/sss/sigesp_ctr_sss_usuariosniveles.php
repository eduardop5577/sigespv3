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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_usuariosniveles.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	
	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	$objPermisos = new UsuarioNiveles();
	$objPermisos->codemp = $_SESSION['la_empresa']['codemp'];		
	$objPermisos->codasiniv = $objdata->codasiniv;	
	$objPermisos->codniv = $objdata->codniv;	
	$objPermisos->codtipniv = $objdata->codtipniv;	

	// Cargamos los usuarios que se agregaron al personal
	if ($objdata->datosAdmin)
	{
		$total = count((array)$objdata->datosAdmin);
		for ($j=0; $j<$total; $j++)
		{
			$objPermisos->admin[$j] = new UsuarioNiveles();
			$arrResultado = "";
			$arrResultado = pasarDatos($objPermisos->admin[$j],$objdata->datosAdmin[$j]);	
			$objSistema->usuarioeliminar[$j] = $arrResultado["objDao"];

			$objPermisos->admin[$j]->codemp = $_SESSION['la_empresa']['codemp'];
			$objPermisos->admin[$j]->codusu = $objdata->datosAdmin[$j]->codusu;
			$objPermisos->admin[$j]->codasiniv = $objdata->codasiniv;
			$objPermisos->admin[$j]->codniv = $objdata->codniv;
			$objPermisos->admin[$j]->codtipniv = $objdata->codtipniv;			
		}
	}
	// Cargamos los usuarios que se eliminaron al personal
	if ($objdata->datosEliminar)
	{
		$total = count((array)$objdata->datosEliminar);
		for ($j=0; $j<$total; $j++)
		{
			$objPermisos->usuarioeliminar[$j] = new UsuarioNiveles();
			$arrResultado = "";
			$arrResultado = pasarDatos($objPermisos->usuarioeliminar[$j],$objdata->datosEliminar[$j]);	
			$objSistema->usuarioeliminar[$j] = $arrResultado["objDao"];
			
			$objPermisos->usuarioeliminar[$j]->codemp = $_SESSION['la_empresa']['codemp'];
			$objPermisos->usuarioeliminar[$j]->codusu = $objdata->datosEliminar[$j]->codusu;
			$objPermisos->usuarioeliminar[$j]->codasiniv = $objdata->codasiniv;
			$objPermisos->usuarioeliminar[$j]->codniv = $objdata->codniv;
			$objPermisos->usuarioeliminar[$j]->codtipniv = $objdata->codtipniv;	
		}
	}	

		
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->oper;
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
				$objPermisos->criterio[0]['operador'] = "AND";
				$objPermisos->criterio[0]['criterio'] = "codniv";
				$objPermisos->criterio[0]['condicion'] = "=";
				$objPermisos->criterio[0]['valor'] = "'".$objPermisos->codniv."'";
				
				$objPermisos->criterio[1]['operador'] = "AND";
				$objPermisos->criterio[1]['criterio'] = "codasiniv";
				$objPermisos->criterio[1]['condicion'] = "=";
				$objPermisos->criterio[1]['valor'] = "'".$objPermisos->codasiniv."'";

				$objPermisos->criterio[2]['operador'] = "AND";
				$objPermisos->criterio[2]['criterio'] = "codtipniv";
				$objPermisos->criterio[2]['condicion'] = "=";
				$objPermisos->criterio[2]['valor'] = "'".$objPermisos->codtipniv."'";
				$objPermisos->eliminarTodos();
				if($objPermisos->valido)
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
			$datos = $objPermisos->obtenerUsuarios();	
			if($objPermisos->valido)
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
	unset($objPermisos);
}	
?>
