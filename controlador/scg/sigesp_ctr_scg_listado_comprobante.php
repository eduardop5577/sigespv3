<?php
/***********************************************************************************
* @fecha de modificacion: 01/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();
$datosempresa=$_SESSION["la_empresa"];
$dirctrscg = "";
$dirctrscg = dirname(__FILE__);
$dirctrscg = str_replace("\\","/",$dirctrscg); 
$dirctrscg = str_replace("/controlador/scg","",$dirctrscg);
require_once ($dirctrscg."/base/librerias/php/general/Json.php");
require_once ($dirctrscg."/modelo/servicio/scg/sigesp_srv_scg_listado_comprobante.php");
require_once ($dirctrscg.'/base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();

if (($_POST['ObjSon']) && ($sessionvalida))
{
	$_SESSION['session_activa'] = time();
    $submit = str_replace("\\", "", $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);

    switch ($objetoJson->operacion)
	{
		case 'buscarFormato':
   			echo selectConfig($objetoJson->sistema,$objetoJson->seccion,$objetoJson->variable,$objetoJson->valor,$objetoJson->tipo);
    	break;
		
		case 'buscarFormato1':
   			echo selectConfig($objetoJson->sistema,$objetoJson->seccion,$objetoJson->variable,$objetoJson->valor,$objetoJson->tipo);
    	break;
		
		case "verificar_estatus_estcencos":
    		$servicioListadoC = new servicioListadoComp();
    		$estcencos = $datosempresa["estcencos"];
    		echo $estcencos;
    		unset($servicioListadoC);
    	break;
		
		case "catalogo_comprobante":
    		$servicioListadoC = new servicioListadoComp();
    		$resultado = $servicioListadoC->buscarComprobantes($objetoJson->id_comprobante,$objetoJson->id_procede);

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioListadoC);
    	break;
		
		case "catalogo_comprobante_hasta":
    		$servicioListadoC = new servicioListadoComp();
    		$resultado = $servicioListadoC->buscarComprobantes($objetoJson->icomprobante,$objetoJson->iprocede);

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioListadoC);
    	break;
		
		case "catalogo_procede":
    		$servicioListadoC = new servicioListadoComp();
    		$resultado = $servicioListadoC->buscarComprobantes($objetoJson->id_comprobante1,$objetoJson->id_procede1);

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioListadoC);
    	break;		  
		
		case "catalogo_procede_hasta":
    		$servicioListadoC = new servicioListadoComp();
    		$resultado = $servicioListadoC->buscarComprobantes($objetoJson->icomprobante1,$objetoJson->iprocede1);

			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioListadoC);
    	break;		  
	}
}