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
require_once('../../base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();
if (($_POST['ObjSon']) && ($sessionvalida))
{
	$_SESSION['session_activa']=time();
	require_once('../../base/librerias/php/general/Json.php');
	require_once ('../../modelo/sss/dao/sigesp_mod_sss_dao_registroevento.php');
	require_once ('../../modelo/servicio/spg/sigesp_srv_spg_cuenta.php');
	require_once ('sigesp_ctr_spg_servicio.php');
	$datosempresa=$_SESSION["la_empresa"];
	if ($_POST['ObjSon']) 		
	{
		$submit = str_replace("\\","",$_POST['ObjSon']);
		$json = new Services_JSON;
		$ArJson = $json->decode($submit);
		$ArObjetos = array();
		$oregevent = new registroEventoDao ();
		$cuenta = new Cuenta();
		$evento = $ArJson->operacion;		
		switch ($evento)
		{ 
			case 'catalogootrocredito' : 
				$cuenta->codemp       = $_SESSION["la_empresa"]["codemp"];
				if ($ArJson->codestpro1=="0000000000000000000000000")
				{
					$ArJson->codestpro1="";
				}
				if ($ArJson->codestpro2=="0000000000000000000000000")
				{
					$ArJson->codestpro2="";
				}
				if ($ArJson->codestpro3=="0000000000000000000000000")
				{
					$ArJson->codestpro3="";
				}
				if ($ArJson->codestpro4=="0000000000000000000000000")
				{
					$ArJson->codestpro4="";
				}
				if ($ArJson->codestpro5=="0000000000000000000000000")
				{
					$ArJson->codestpro5="";
				}
				$cuenta->codestpro1   = $ArJson->codestpro1;
				$cuenta->codestpro2   = $ArJson->codestpro2;
				$cuenta->codestpro3   = $ArJson->codestpro3;
				$cuenta->codestpro4   = $ArJson->codestpro4;
				$cuenta->codestpro5   = $ArJson->codestpro5;
				$cuenta->estcla       = $ArJson->estcla;
				$cuenta->spg_cuenta   = $ArJson->spg_cuenta;
				$cuenta->denominacion = $ArJson->denominacion;
				$cuenta->sc_cuenta    = $ArJson->sc_cuenta;
				$datos = $cuenta->obtenerCuentasCatalogoCxp($_SESSION["ls_gestor"],$datosempresa["estmodest"],$datosempresa["loncodestpro1"],$datosempresa["loncodestpro2"],$datosempresa["loncodestpro3"],$datosempresa["loncodestpro4"],$datosempresa["loncodestpro5"]);
				$ObjSon = generarJson($datos);
				echo $ObjSon;
			break;
		}  
	}
}
?>