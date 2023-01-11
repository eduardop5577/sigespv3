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
	require_once('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon']) 		
	{
		$submit = str_replace("\\","",$_POST['ObjSon']);
		$json = new Services_JSON;
		$ArJson = $json->decode($submit);
		$ArObjetos = array();
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioCfg('sigesp_correo');
		$Evento = $ArJson->oper;
		switch ($Evento)
		{    			
			case 'incluir':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$resultado=$oservicio->modificarDto($ArJson);
				$mensaje="Actualizo el correo asociado a la empresa " .$ArJson->codemp;	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|".$resultado;
				}
				else
				{
					echo "|".$resultado;
					$mensaje="Error al Actualizar el correo asociado a la empresa " .$ArJson->codemp;	
					$tipoevento=false;
				}	
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_correo.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;				
			
			case 'actualizar':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$oservicio->modificarDto ( $ArJson );
				$resultado=$oservicio->modificarDto($ArJson);
				$mensaje="Actualizo el correo asociado a la empresa " .$ArJson->codemp;	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al Actualizar el correo asociado a la empresa " .$ArJson->codemp;	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_correo.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
		
			case 'cargarcorreo':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$datos = $oservicio->buscarTodos();
				$ObjSon="";
				if($datos->RecordCount() > 0)
				{
				 $ObjSon = generarJson($datos);
				}
				else
				{
					$correo[0]['codemp'] = $_SESSION["la_empresa"]["codemp"];
					$correo[0]['msjenvio'] = 0; 
					$correo[0]['msjsmtp']  = 0; 
					$correo[0]['msjservidor'] = '';
					$correo[0]['msjpuerto'] = '';
					$correo[0]['msjhtml'] = '';
					$correo[0]['msjremitente'] = '';
					$arreglo = array("raiz"=>$correo);
					$ObjSon = $json->encode($arreglo);
				}
				echo $ObjSon;
				break;
		}
	}
}
?>