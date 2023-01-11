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
require_once ($dirctrscg."/modelo/servicio/scg/sigesp_srv_scg_estado_resultado.php");
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
		
            case "verificar_estatus_estcencos":
                $servicioListadoC = new servicioEstadoResul();
                $estcencos = $datosempresa["estcencos"];
                echo $estcencos;
                unset($servicioListadoC);
            break;

            case "comboanio":
                $ServResul = new servicioEstadoResul();
                $resultado = $ServResul->buscarAnios();
                $ObjSon    = generarJson($resultado);
                echo $ObjSon;
                unset($ServResul);
            break;
        
            case "buscarEstructuras":
                $ServResul = new servicioEstadoResul();
                $resultado = $ServResul->buscarEstructuras($datosempresa["codemp"]);
                $ObjSon    = generarJson($resultado);
                echo $ObjSon;
                unset($ServResul);
            break;		  
	}
}