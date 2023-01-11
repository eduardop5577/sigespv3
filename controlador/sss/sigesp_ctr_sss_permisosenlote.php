<?php
/***********************************************************************************
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_derechosusuario.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_permisosinternos.php');
	
	$_SESSION['session_activa'] = time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);	
	
	$objPerfil   = new DerechosUsuario();
	$objPermisos = new PermisosInternos();	
	if (isUTF8($objdata->codusu))
	{
		$objPermisos->codusu = utf8_to_latin9($objdata->codusu);
		$objPerfil->codusu = utf8_to_latin9($objdata->codusu);
	}
	else
	{
		$objPermisos->codusu = $objdata->codusu;
		$objPerfil->codusu = $objdata->codusu;
	}
	$objPermisos->codemp = $_SESSION['la_empresa']['codemp'];
	$objPermisos->codsis = $objdata->sistema;
	$objPermisos->nomfisico = $objdata->vista;
	
	$objPerfil->codemp = $_SESSION['la_empresa']['codemp'];	
	$objPerfil->codsis = $objdata->sistema;
	$objPerfil->nomfisico = $objdata->vista;	
	$evento = $objdata->operacion;
	switch ($evento)
	{
		case 'blanquear_copiar_permisos':			
			$objPerfil->criterio[0]['operador'] = "AND";
			$objPerfil->criterio[0]['criterio'] = "codusu";
			$objPerfil->criterio[0]['condicion'] = "=";
			$objPerfil->criterio[0]['valor'] = "'".$objPerfil->codusu."'";
			$objPerfil->eliminarTodos();
			if ($objPerfil->valido)
			{
				$objPermisos->criterio[0]['operador'] = "AND";
				$objPermisos->criterio[0]['criterio'] = "codusu";
				$objPermisos->criterio[0]['condicion'] = "=";
				$objPermisos->criterio[0]['valor'] = "'".$objPermisos->codusu."'";
				$objPermisos->eliminarTodos();
				if ($objPermisos->valido)
				{					
					if (isUTF8($objdata->codusuorigen))
					{
						$objPermisos->codusuori = utf8_to_latin9($objdata->codusuorigen);
						$objPerfil->codusuori = utf8_to_latin9($objdata->codusuorigen);
					}
					else
					{
						$objPermisos->codusuori = $objdata->codusuorigen;
						$objPerfil->codusuori = $objdata->codusuorigen;
					}
					$objPermisos->copiarPermisosInternos();
					if ($objPermisos->valido)
					{					
						$objPerfil->copiarDerechos();
						if ($objPerfil->valido)
						{					
							$arreglo['mensaje'] = 'Se Copio el perfil de usuario';
							$arreglo['valido']  = $objPerfil->valido;		
						}
						else
						{
							$arreglo['mensaje'] = 'Ocurrio un error al agregar los derechos usuarios';	
							$arreglo['valido']  = $objPerfil->valido;		
						}
					}
					else
					{
						$arreglo['mensaje'] = 'Ocurrio un error al agregar los permisos internos';	
						$arreglo['valido']  = $objPermisos->valido;		
					}
				}
				else
				{
					$arreglo['mensaje'] = 'Ocurrio un error al eliminar los permisos internos';	
					$arreglo['valido']  = $objPermisos->valido;		
				}
			}
			else
			{
				$arreglo['mensaje'] = 'Ocurrio un error al eliminar el perfil';	
				$arreglo['valido']  = $objPerfil->valido;		
			}
			$respuesta  = array('raiz'=>$arreglo);
			$respuesta  = json_encode($respuesta);
			echo $respuesta;	
		break;

		case 'copiar_permisos':			
			if (isUTF8($objdata->codusuorigen))
			{
				$objPermisos->codusuori = utf8_to_latin9($objdata->codusuorigen);
				$objPerfil->codusuori = utf8_to_latin9($objdata->codusuorigen);
			}
			else
			{
				$objPermisos->codusuori = $objdata->codusuorigen;
				$objPerfil->codusuori = $objdata->codusuorigen;
			}
			$objPermisos->copiarPermisosInternos();
			if ($objPermisos->valido)
			{					
				$objPerfil->copiarDerechos();
				if ($objPerfil->valido)
				{					
					$arreglo['mensaje'] = 'Se Copio el perfil de usuario';
					$arreglo['valido']  = $objPerfil->valido;		
				}
				else
				{
					$arreglo['mensaje'] = 'Ocurrio un error al agregar los derechos usuarios';	
					$arreglo['valido']  = $objPerfil->valido;		
				}
			}
			else
			{
				$arreglo['mensaje'] = 'Ocurrio un error al agregar los permisos internos';	
				$arreglo['valido']  = $objPermisos->valido;		
			}
			$respuesta  = array('raiz'=>$arreglo);
			$respuesta  = json_encode($respuesta);
			echo $respuesta;	
		break;
		
		case 'blanquear_permisos':			
			$objPerfil->criterio[0]['operador'] = "AND";
			$objPerfil->criterio[0]['criterio'] = "codusu";
			$objPerfil->criterio[0]['condicion'] = "=";
			$objPerfil->criterio[0]['valor'] = "'".$objPerfil->codusu."'";
			$objPerfil->eliminarTodos();
			if ($objPerfil->valido)
			{
				$objPermisos->criterio[0]['operador'] = "AND";
				$objPermisos->criterio[0]['criterio'] = "codusu";
				$objPermisos->criterio[0]['condicion'] = "=";
				$objPermisos->criterio[0]['valor'] = "'".$objPermisos->codusu."'";
				$objPermisos->eliminarTodos();
				if ($objPermisos->valido)
				{					
					$arreglo['mensaje'] = 'Se Elimino el perfil de usuario';
					$arreglo['valido']  = $objPermisos->valido;		
				}
				else
				{
					$arreglo['mensaje'] = 'Ocurrio un error al eliminar los permisos internos';	
					$arreglo['valido']  = $objPerfil->valido;		
				}
			}
			else
			{
				$arreglo['mensaje'] = 'Ocurrio un error al eliminar el perfil';	
				$arreglo['valido']  = $objPerfil->valido;		
			}
			$respuesta  = array('raiz'=>$arreglo);
			$respuesta  = json_encode($respuesta);
			echo $respuesta;	
		break;
		
		case 'agregar_permisos':			
			$objPerfil->criterio[0]['operador'] = "AND";
			$objPerfil->criterio[0]['criterio'] = "codusu";
			$objPerfil->criterio[0]['condicion'] = "=";
			$objPerfil->criterio[0]['valor'] = "'".$objPerfil->codusu."'";
			$objPerfil->agregarTodos();
			if ($objPerfil->valido)
			{
				$objPermisos->criterio[0]['operador'] = "AND";
				$objPermisos->criterio[0]['criterio'] = "codusu";
				$objPermisos->criterio[0]['condicion'] = "=";
				$objPermisos->criterio[0]['valor'] = "'".$objPermisos->codusu."'";
				$objPermisos->agregarTodos();
				if ($objPermisos->valido)
				{					
					$arreglo['mensaje'] = 'Se Agrego perfil de usuario';
					$arreglo['valido']  = $objPermisos->valido;		
				}
				else
				{
					$arreglo['mensaje'] = 'Ocurrio un error al agregar los permisos internos';	
					$arreglo['valido']  = $objPerfil->valido;		
				}
			}
			else
			{
				$arreglo['mensaje'] = 'Ocurrio un error al agregar el perfil';	
				$arreglo['valido']  = $objPerfil->valido;		
			}
			$respuesta  = array('raiz'=>$arreglo);
			$respuesta  = json_encode($respuesta);
			echo $respuesta;	
		break;
	}
	unset($objPerfil);
	unset($objPermisos);
}	
?>