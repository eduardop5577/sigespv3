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
if (($_POST['ObjSon']) && ($sessionvalida))
{
	$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
	require_once($dirsrv.'/base/librerias/php/general/Json.php');
	require_once($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
	require_once($dirsrv.'/modelo/servicio/cfg/sigesp_srv_cfg_scg_planpatrimonial.php');
	$_SESSION['session_activa'] = time();	

	$submit    = str_replace("\\","",$_POST['ObjSon']);
	$json      = new Services_JSON;	
	$ArJson    = $json->decode($submit);
	$oplan     = new servicioPlanCuentaPatrimonial(); 
	$servicioEvento = new ServicioEvento();
	$ArJson->denominacion = utf8_decode($ArJson->denominacion);
	switch ($ArJson->oper)
	{
		case 'incluir':
			$mensaje="Inserto la cuenta con codigo del plan partimonial ".$ArJson->sc_cuenta;	
			$tipoevento=true;
			if($oplan->guardarCuenta($ArJson))
			{
				echo '|1';
			}
			else 
			{
				if ($oplan->errorDuplicate)
				{
					echo '|-2';
				}
				else
				{
					echo '|0';
				}
				$mensaje="Error al insertar la cuenta con codigo del plan partimonial ".$ArJson->sc_cuenta;	
				$tipoevento=false;
			}
			$servicioEvento->evento="INSERTAR";
			$servicioEvento->codmenu=$ArJson->codmenu;
			$servicioEvento->codusu=$_SESSION["la_logusr"];
			$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
			$servicioEvento->codsis="CFG";
			$servicioEvento->nomfisico="sigesp_vis_cfg_scg_planpatrimonial.php";
			$servicioEvento->desevetra=$mensaje;	
			$servicioEvento->tipoevento=$tipoevento;
			$servicioEvento->incluirEvento();
			unset($oplan);
			break;
		
		case 'catalogo':
			$datos = $oplan->buscarCuenta($ArJson->codcue, $ArJson->dencue);					
			echo generarJson($datos);
			unset($datos);
			unset($oplan);	
			break;   
		
		case 'actualizar':
			$mensaje="Actualizo la cuenta con codigo del plan partimonial ".$ArJson->sc_cuenta;	
			$tipoevento=true;
			if($oplan->modificarCuenta($ArJson, $arreve))
			{
				echo '|1';
			}
			else
			{
				echo '|0';
				$mensaje="Error al actualizar la cuenta con codigo del plan partimonial ".$ArJson->sc_cuenta;	
				$tipoevento=false;
			}
			$servicioEvento->evento="MODIFICAR";
			$servicioEvento->codmenu=$ArJson->codmenu;
			$servicioEvento->codusu=$_SESSION["la_logusr"];
			$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
			$servicioEvento->codsis="CFG";
			$servicioEvento->nomfisico="sigesp_vis_cfg_scg_planpatrimonial.php";
			$servicioEvento->desevetra=$mensaje;	
			$servicioEvento->tipoevento=$tipoevento;
			$servicioEvento->incluirEvento();
			unset($oplan);
			break;
			
		
		case 'eliminar':
			$resultado=$oplan->eliminarCuenta($ArJson, $arreve, $_SESSION["la_empresa"]["codemp"], $_SESSION["la_empresa"]["formplan"]);
			if($resultado!='0')
			{
				$mensaje="Elimino la cuenta con codigo del plan partimonial ".$ArJson->sc_cuenta;	
				$tipoevento=true;
			}
			else
			{
				$mensaje="Error al eliminar la cuenta con codigo del plan partimonial ".$ArJson->sc_cuenta;	
				$tipoevento=false;
			}
			echo '|'.$resultado;
			$servicioEvento->evento="ELIMINAR";
			$servicioEvento->codmenu=$ArJson->codmenu;
			$servicioEvento->codusu=$_SESSION["la_logusr"];
			$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
			$servicioEvento->codsis="CFG";
			$servicioEvento->nomfisico="sigesp_vis_cfg_scg_planpatrimonial.php";
			$servicioEvento->desevetra=$mensaje;	
			$servicioEvento->tipoevento=$tipoevento;
			$servicioEvento->incluirEvento();
			break;
	}
}
?>