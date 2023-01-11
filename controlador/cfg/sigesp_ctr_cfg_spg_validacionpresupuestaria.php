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
require_once('../../base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();
if (($_POST['ObjSon']) && ($sessionvalida))
{
	$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
	require_once($dirsrv.'/base/librerias/php/general/Json.php');
	require_once($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
	require_once('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon']) 		
	{
		$submit = str_replace("\\","",$_POST['ObjSon']);
		$json = new Services_JSON;	
		$ArJson = $json->decode($submit);
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioCfg('sigesp_empresa');
		$oservicio->setCodemp($datosempresa["codemp"]);
		$evento = $ArJson->oper;
		
		switch ($evento)
		{
			case 'incluir':
				ServicioCfg::iniTransaccion ();
				if ($ArJson->estvalspg==='true')
				{
					$ArJson->estvalspg=1;
				}
				else
				{
					$ArJson->estvalspg=0;
					$ArJson->ctaspgced='';
					$ArJson->ctaspgrec='';
				}
				$oservicio->updateValicacionPresupuestaria($ArJson->estvalspg,$ArJson->ctaspgced,$ArJson->ctaspgrec);
				$mensaje='Actualizo la empresa ' . $datosempresa["codemp"] . ' activo la validacion presupuestaria';
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje='Error al actualizar la empresa ' . $datosempresa["codemp"] . ' activo la validacion presupuestaria';
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_spg_validacionpresupuestaria.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				unset($oservicio);
				unset($objDtoEmpresa);
				unset($oregevent);
				break;
						
			case 'catalogo':
				$objDtoEmpresa = $oservicio->getDto("codemp='".$datosempresa["codemp"]."'");
				echo $objDtoEmpresa->estvalspg."|".$objDtoEmpresa->ctaspgced."|".$objDtoEmpresa->ctaspgrec;
				unset($objDtoEmpresa);
				unset($oservicio);
				break;
		}
	}
}
?>