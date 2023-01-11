<?php
/***********************************************************************************
* @Clase para el inicio del módulo de apertura
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
	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);
	$objdata = json_decode($objdata,false);	
	$ruta = '../../base/xml/';
	$archivoconfig = 'sigesp_xml_configuracion_apr.xml';
	switch ($objdata->operacion)
	{
		case 'obtenerbd':    		
			$documentoxml = abrirArchivoXml($ruta,$archivoconfig);
			if ($documentoxml != null)
			{
				$datos = array();
				$datos  = obtenerConexionbd($documentoxml,$datos);
				$datos  = array('raiz'=>$datos);
				$textJson = json_encode($datos);
				echo $textJson;
			}
		break;

		case 'verificarsession':	
			require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_conexion.php');
			$valido=true;
			$objlibconexion = new ConexionBaseDatos();
			$mensaje=obtenerMensaje('OPERACION_EXITOSA');	
			$documentoxml = abrirArchivoXml($ruta,$archivoconfig);	
			if (!is_null($documentoxml))
			{
				$basededatos = obtenerBdApertura($documentoxml,$objdata->basedatos);
				$conexion = $objlibconexion->conectarBD($_SESSION['sigesp_servidor_apr'], $_SESSION['sigesp_usuario_apr'],
									   $_SESSION['sigesp_clave_apr'], $_SESSION['sigesp_basedatos_apr'], 
									   $_SESSION['sigesp_gestor_apr'],$_SESSION['sigesp_puerto_apr']);
				if($conexion===false)
				{
					$valido=false;
					$mensaje=obtenerMensaje('OPERACION_FALLIDA');
				}
			}	
			$datos['valido'] = $valido;
			$datos['mensaje'] = $mensaje;
			$datos  = array('raiz'=>$datos);
			$respuesta= json_encode($datos);
			echo $respuesta;
			unset($conexion);
		break;	
	}
}	
?>
