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
$datosempresa=$_SESSION["la_empresa"];
$dirctrrpc = "";
$dirctrrpc = dirname(__FILE__);
$dirctrrpc = str_replace("\\","/",$dirctrrpc); 
$dirctrrpc = str_replace("/controlador/sno","",$dirctrrpc);
require_once ($dirctrrpc."/base/librerias/php/general/Json.php");
require_once ($dirctrrpc."/modelo/servicio/sno/sigesp_srv_sno_personal.php");
require_once ($dirctrrpc.'/base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();

if (($_POST['ObjSon']) && ($sessionvalida))
{
    $submit = str_replace("\\", "", $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion)
	{
    	case "buscarPersonal": 
    		$servicioPersonal = new servicioPersonal();
    		$resultado = $servicioPersonal->buscarPersonal($datosempresa['codemp'],$objetoJson->mcodper,$objetoJson->mcedper,$objetoJson->mnomper,$objetoJson->mapeper,$objetoJson->mesbeneficiario);
			$ObjSon    = generarJson($resultado);
			echo $ObjSon;
			unset($servicioPersonal);
		break;
    }	
}   
