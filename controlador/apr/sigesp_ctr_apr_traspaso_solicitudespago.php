<?php
/***********************************************************************************
* @Clase para manejar el traspaso de las solicitudes
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/cxp/sigesp_dao_cxp_solicitud.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/cxp/sigesp_dao_cxp_documento.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/apr/sigesp_dao_apr_solicitudes.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_validaciones.php');
	
	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	
	$objTrasSolicitud = new TraspasoSolicitud();
	$objTrasSolicitud->codemp = $_SESSION['la_empresa']['codemp'];	
	$objTrasSolicitud->codsis = $objdata->sistema;
	$objTrasSolicitud->nomfisico = $objdata->vista;
	$arrResultado=pasarDatos($objTrasSolicitud,$objdata,$evento);
	$objTrasSolicitud = $arrResultado["objDao"];
	$evento = $arrResultado["evento"];
		
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->operacion;
	
	if ($objdata->datosSol)
	{
		$total = count((array)$objdata->datosSol);
		for ($j=0; $j<$total; $j++)
		{
			$objTrasSolicitud->solicitud[$j] = new TraspasoSolicitud();
			$objTrasSolicitud->solicitud[$j]->numsol = $objdata->datosSol[$j]->numsol;
			$objTrasSolicitud->solicitud[$j]->fecemisol = $objdata->datosSol[$j]->fecemisol;
			$objTrasSolicitud->solicitud[$j]->consol = $objdata->datosSol[$j]->consol;
			$objTrasSolicitud->solicitud[$j]->monsol = $objdata->datosSol[$j]->monsol;
			$objTrasSolicitud->solicitud[$j]->pagado = $objdata->datosSol[$j]->pagado;
		}
	}
	
	switch ($evento)
	{
		case 'verificarEstructuras':		
			$nomestpro1 = $_SESSION['la_empresa']['nomestpro1'];
			$nomestpro2 = $_SESSION['la_empresa']['nomestpro2'];
			$nomestpro3 = $_SESSION['la_empresa']['nomestpro3'];
			$nomestpro4 = $_SESSION['la_empresa']['nomestpro4'];
			$nomestpro5 = $_SESSION['la_empresa']['nomestpro5'];
			
			$arreglo = array ('nivel1'=>$nomestpro1,'nivel2'=>$nomestpro2,'nivel3'=>$nomestpro3,'nivel4'=>$nomestpro4,'nivel5'=>$nomestpro5);			
			$respuesta  = array('raiz'=>$arreglo);
			$respuesta  = json_encode($respuesta);
			echo $respuesta;
		break;
		
		case 'buscar':
			$objSolicitud = new Solicitud();		
			$objSolicitud->codemp = $_SESSION['la_empresa']['codemp'];	
			
			$objSolicitud->servidor  = $_SESSION['sigesp_servidor_apr'];
			$objSolicitud->usuario   = $_SESSION['sigesp_usuario_apr'];
			$objSolicitud->clave     = $_SESSION['sigesp_clave_apr'];
			$objSolicitud->basedatos = $_SESSION['sigesp_basedatos_apr'];
			$objSolicitud->gestor    = $_SESSION['sigesp_gestor_apr'];
			$objSolicitud->puerto    = $_SESSION['sigesp_puerto_apr'];
			$objSolicitud->tipoconexionbd='ALTERNA';
			
			$i=0;				
			$objSolicitud->estatus = $objdata->estatus;
			$objdata->fecdesde = convertirFechaBd($objdata->fecdesde);
			$objdata->fechasta = convertirFechaBd($objdata->fechasta);
			
			$objSolicitud->criterio[$i]['operador']  = "AND";
			$objSolicitud->criterio[$i]['criterio']  = "fecemisol";
			$objSolicitud->criterio[$i]['condicion'] = ">=";
			$objSolicitud->criterio[$i]['valor']     = "'".$objdata->fecdesde."'";
			$i++;
			
			$objSolicitud->criterio[$i]['operador']  = "AND";
			$objSolicitud->criterio[$i]['criterio']  = "fecemisol";
			$objSolicitud->criterio[$i]['condicion'] = "<=";
			$objSolicitud->criterio[$i]['valor']     = "'".$objdata->fechasta."'";
			$i++;
			
			$objSolicitud->criterio[$i]['operador']  = "AND";
			$objSolicitud->criterio[$i]['criterio']  = "estprosol";
			$objSolicitud->criterio[$i]['condicion'] = "=";
			$objSolicitud->criterio[$i]['valor']     = "'".$objdata->estatus."'";
			$i++;
			
			$objSolicitud->criterio[$i]['operador']  = "AND";
			$objSolicitud->criterio[$i]['criterio']  = "estapesolpag";
			$objSolicitud->criterio[$i]['condicion'] = "=";
			$objSolicitud->criterio[$i]['valor']     = "'0'";			
	
			$datos = $objSolicitud->leer();
			if ($objSolicitud->valido)
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
		
		
		case 'obtenerTipoDocumento':
			$objTipoDoc = new Documento();
			$objTipoDoc->servidor  = $_SESSION['sigesp_servidor_apr'];
			$objTipoDoc->usuario   = $_SESSION['sigesp_usuario_apr'];
			$objTipoDoc->clave     = $_SESSION['sigesp_clave_apr'];
			$objTipoDoc->basedatos = $_SESSION['sigesp_basedatos_apr'];
			$objTipoDoc->gestor    = $_SESSION['sigesp_gestor_apr'];
			$objTipoDoc->puerto    = $_SESSION['sigesp_puerto_apr'];
			$objTipoDoc->tipoconexionbd='ALTERNA';
			$i=0;	
			$objTipoDoc->criterio[$i]['operador']  = "WHERE";
			$objTipoDoc->criterio[$i]['criterio']  = "estcon";
			$objTipoDoc->criterio[$i]['condicion'] = "=";
			$objTipoDoc->criterio[$i]['valor']     = "1";
			$i++;
			if ($objdata->tipodoc==1)
			{
				$objTipoDoc->criterio[$i]['operador']  = "AND";
				$objTipoDoc->criterio[$i]['criterio']  = "(estpre";
				$objTipoDoc->criterio[$i]['condicion'] = "=";
				$objTipoDoc->criterio[$i]['valor']     = "3";
				$i++;
				
				$objTipoDoc->criterio[$i]['operador']  = "OR";
				$objTipoDoc->criterio[$i]['criterio']  = "estpre";
				$objTipoDoc->criterio[$i]['condicion'] = "=";
				$objTipoDoc->criterio[$i]['valor']     = "4)";
				$i++;
			}
			elseif ($objdata->tipodoc==2)
			{
				$objTipoDoc->criterio[$i]['operador']  = "AND";
				$objTipoDoc->criterio[$i]['criterio']  = "estpre";
				$objTipoDoc->criterio[$i]['condicion'] = "=";
				$objTipoDoc->criterio[$i]['valor']     = "2";
				$i++;
			}	
			$datos = $objTipoDoc->leer();
			if ($objTipoDoc->valido)
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

		
		case 'procesar':
			$objSistemaVentana->campo = 'ejecutar';
			$accionvalida=$objSistemaVentana->verificarUsuario();
			if ($accionvalida)
			{		
				$fecha = date('d-m-Y');
				$nombrearchivo = '../../vista/apr/resultados/';
				$nombrearchivo.=$_SESSION['sigesp_basedatos_apr'].'_traspaso_solicitudespago_'.$fecha.'.txt';
				$archivo = @fopen($nombrearchivo,'a+');
				$objTrasSolicitud->archivo = $archivo;	
				
				$objTrasSolicitud->fecemisol  = convertirFechaBd($objdata->fecope);
				$objTrasSolicitud->prefijo    = $objdata->prefijo;
				$objTrasSolicitud->codtipodoc = $objdata->tipodoc; 
				$objTrasSolicitud->cuenta     = $objdata->cuenta; 
				$objTrasSolicitud->estcla     = $objdata->estcla;
				$objTrasSolicitud->estconpre  = $objdata->estconpre;
				$objTrasSolicitud->consol     = $objdata->consol;
				$objTrasSolicitud->procesarSolicitudes();	
				if($objTrasSolicitud->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objTrasSolicitud->valido;
				
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
	unset($objTrasSolicitud);
	unset($objTipoDoc);
	unset($objSolicitud);
	unset($objSistemaVentana);
	
}
?>	
			