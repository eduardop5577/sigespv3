<?php
/***********************************************************************************
* @Clase para Manejar para la definición de Sistema.
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
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_validaciones.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_firmasdinamicas.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
	
	$_SESSION['session_activa']=time();
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);		
	$objFirmas = new FirmasDinamicas();		
	$objFirmas->codemp = $_SESSION['la_empresa']['codemp'];	
	$objFirmas->codsis = $objdata->sistema;
	$objFirmas->nomfisico = $objdata->vista;	
	$arrResultado = pasarDatos($objFirmas,$objdata,$evento);
	$objFirmas = $arrResultado["objDao"];
	$evento = $arrResultado["evento"];

	$objFirmas->codemp = $_SESSION['la_empresa']['codemp'];
	$objSistemaVentana = new SistemaVentana();		
	$objSistemaVentana->codemp = $_SESSION['la_empresa']['codemp'];	
	$objSistemaVentana->codusu = $_SESSION['la_logusr']; 
	$objSistemaVentana->codsis = $objdata->sistema;
	$objSistemaVentana->nomfisico  = $objdata->vista;
	$evento = $objdata->oper;
	// Cargamos los usuarios que se agregaron al sistema
	if ($objdata->datosFirmas)
	{
		$total = count((array)$objdata->datosFirmas);
		for ($j=0; $j<$total; $j++)
		{
                    $objFirmas->firmas[$j]['codemp'] = $_SESSION['la_empresa']['codemp'];
                    $objFirmas->firmas[$j]['codfir'] = $objdata->datosFirmas[$j]->codfir;
                    $objFirmas->firmas[$j]['codcla'] = $objdata->datosFirmas[$j]->codcla;
                    $objFirmas->firmas[$j]['tipclafir'] = $objdata->datosFirmas[$j]->tipclafir;
                    $objFirmas->firmas[$j]['fir1'] = $objdata->datosFirmas[$j]->fir1;
                    $objFirmas->firmas[$j]['fir2'] = $objdata->datosFirmas[$j]->fir2;
                    $objFirmas->firmas[$j]['fir3'] = $objdata->datosFirmas[$j]->fir3;
                    $objFirmas->firmas[$j]['fir4'] = $objdata->datosFirmas[$j]->fir4;
                    $objFirmas->firmas[$j]['fir5'] = $objdata->datosFirmas[$j]->fir5;
		}
	}
	// Cargamos los usuarios que se eliminaron al sistema
	if ($objdata->datosEliminar)
	{
		$total = count((array)$objdata->datosEliminar);
		for ($j=0; $j<$total; $j++)
		{
                    $objFirmas->firmaseliminar[$j]['codemp'] = $_SESSION['la_empresa']['codemp'];
                    $objFirmas->firmaseliminar[$j]['codfir'] = $objdata->datosEliminar[$j]->codfir;
                    $objFirmas->firmaseliminar[$j]['codcla'] = $objdata->datosEliminar[$j]->codcla;
                    $objFirmas->firmaseliminar[$j]['tipclafir'] = $objdata->datosEliminar[$j]->tipclafir;
		}
	}	
	switch ($evento)
	{
                case 'nuevo':
                    $objFirmas->buscarCodigoFirmas();
                    echo json_encode($objFirmas->codfir);                    
                break;
            
                case 'buscarControlNumero':
                    $resultado = $objFirmas->buscarControlNumero($objdata->codsis);
                    $ObjSon    = generarJson($resultado);
                    echo $ObjSon;
                break;
            
                case 'buscarUnidadEjecutora':
                    $resultado = $objFirmas->buscarUnidadEjecutora();
                    $ObjSon    = generarJson($resultado);
                    echo $ObjSon;
                break;
            
                case 'buscarUsuario':
                    $resultado = $objFirmas->buscarUsuario();
                    $ObjSon    = generarJson($resultado);
                    echo $ObjSon;
                break;
            
                case 'buscarTipoSep':
                    $resultado = $objFirmas->buscarTipoSep();
                    $ObjSon    = generarJson($resultado);
                    echo $ObjSon;
                break;
            
                case 'buscarTipoSoc':
                    $resultado[0]['codigo'] = '001';
                    $resultado[0]['nombre'] = 'Bienes';
                    $resultado[1]['codigo'] = '002';
                    $resultado[1]['nombre'] = 'Servicios';
                    $ObjSon    = generarJsonArreglo($resultado);
                    echo $ObjSon;
                break;
            
		case 'catalogo':
                    $datos = $objFirmas->leer();
                    if($objFirmas->valido)
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
                    $datos = $objFirmas->obtenerDetalles();
                    if($objFirmas->valido)
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
                    $objFirmas->codemp = $_SESSION['la_empresa']['codemp'];
                    $objFirmas->guardarFirmas();
                    if($objFirmas->valido)
                    {
                            $arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
                    }
                    else
                    {
                            $arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
                    }
                    $arreglo['valido']  = $objFirmas->valido;
                    $respuesta  = array('raiz'=>$arreglo);
                    $respuesta  = json_encode($respuesta);
                    echo $respuesta;
		break;
		
		case 'actualizar':
                    $objFirmas->codemp = $_SESSION['la_empresa']['codemp'];
                    $objFirmas->actualizarFirmas();
                    if($objFirmas->valido)
                    {
                            $arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
                    }
                    else
                    {
                            $arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
                    }
                    $arreglo['valido']  = $objFirmas->valido;
                    $respuesta  = array('raiz'=>$arreglo);
                    $respuesta  = json_encode($respuesta);
                    echo $respuesta;
		break;
						
		case 'eliminar':
                    $objFirmas->codemp = $_SESSION['la_empresa']['codemp'];
                    $objFirmas->eliminarFirmas();
                    if($objFirmas->valido)
                    {
                            $arreglo['mensaje'] = obtenerMensaje('OPERACION_EXITOSA');	
                    }
                    else
                    {
                            $arreglo['mensaje'] = obtenerMensaje('OPERACION_FALLIDA');	
                    }
                    $arreglo['valido']  = $objFirmas->valido;
                    $respuesta  = array('raiz'=>$arreglo);
                    $respuesta  = json_encode($respuesta);
                    echo $respuesta;
		break;			
	}
	unset($objSistemaVentana);
	unset($objFirmas);
}	
?>
