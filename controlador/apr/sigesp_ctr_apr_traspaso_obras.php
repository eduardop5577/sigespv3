<?php
/***********************************************************************************
* @Clase para manejar el traspaso de las obras
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

	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/apr/sigesp_dao_apr_obras.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_validaciones.php');

	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		

	$objTrasObra = new TraspasoObras();
	$objTrasObra->codemp = $_SESSION['la_empresa']['codemp'];	
	$arrResultado=pasarDatos($objTrasObra,$objdata,$evento);
	$objTrasObra = $arrResultado["objDao"];
	$evento = $arrResultado["evento"];

	if ($objdata->datosObr)
	{
		$total = count((array)$objdata->datosObr);
		for ($j=0; $j<$total; $j++)
		{
			$objTrasObra->obra[$j] = new TraspasoObras();
			$objTrasObra->obra[$j]->codobr = $objdata->datosObr[$j]->codobr;
		}
	}

	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr'];	
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico = $objdata->vista;
	$evento = $objdata->operacion;


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
			$objObras = new TraspasoObras();		
			$objObras->codemp = $_SESSION['la_empresa']['codemp'];	
			
			$objObras->servidor  = $_SESSION['sigesp_servidor_apr'];
			$objObras->usuario   = $_SESSION['sigesp_usuario_apr'];
			$objObras->clave     = $_SESSION['sigesp_clave_apr'];
			$objObras->basedatos = $_SESSION['sigesp_basedatos_apr'];
			$objObras->gestor    = $_SESSION['sigesp_gestor_apr'];
			$objObras->puerto    = $_SESSION['sigesp_puerto_apr'];
			$objObras->tipoconexionbd='ALTERNA';
			
			$i=0;				
			$objObras->estatus = $objdata->estatus;
			$objdata->fecdesde = convertirFechaBd($objdata->fecdesde);
			$objdata->fechasta = convertirFechaBd($objdata->fechasta);
			
			$objObras->criterio[$i]['operador']  = "AND";
			$objObras->criterio[$i]['criterio']  = "feccreobr";
			$objObras->criterio[$i]['condicion'] = ">=";
			$objObras->criterio[$i]['valor']     = "'".$objdata->fecdesde."'";
			$i++;
			
			$objObras->criterio[$i]['operador']  = "AND";
			$objObras->criterio[$i]['criterio']  = "feccreobr";
			$objObras->criterio[$i]['condicion'] = "<=";
			$objObras->criterio[$i]['valor']     = "'".$objdata->fechasta."'";
			$i++;
			
			$datos = $objObras->leer();
			if ($objObras->valido)
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
				$nombrearchivo.=$_SESSION['sigesp_basedatos_apr'].'_traspaso_obras_'.$fecha.'.txt';
				$archivo = @fopen($nombrearchivo,'a+');
				$objTrasObra->archivo = $archivo;	
				

				$objTrasObra->cuenta     = $objdata->cuenta; 
				$objTrasObra->codestpro1 = $objdata->codestpro1; 
				$objTrasObra->codestpro2 = $objdata->codestpro2; 
				$objTrasObra->codestpro3 = $objdata->codestpro3; 
				$objTrasObra->codestpro4 = $objdata->codestpro4; 
				$objTrasObra->codestpro5 = $objdata->codestpro5; 
				$objTrasObra->estcla     = $objdata->estcla;
				$objTrasObra->estconpre  = $objdata->estconpre;
				//print $objdata->fecha = convertirFechaBd($objdata->fecha);
				$objTrasObra->fecha      = $objdata->fecha;
				$objTrasObra->procesarTraspasos();	
				if($objTrasObra->valido)
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
				}
				else
				{
					$arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
				}
				$arreglo['valido']  = $objTrasObra->valido;
				
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
}
?>	
			