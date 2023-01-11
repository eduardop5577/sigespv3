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
		$oservicio = new ServicioCfg('scb_concepto');
		$oservicio->setCodemp($datosempresa["codemp"]);
			
		switch ($ArJson->oper)
		{
			case 'incluir':
				ServicioCfg::iniTransaccion ();
				$oservicio->incluirDto ($ArJson);
				$mensaje='Inserto el concepto de movimiento ' . $ArJson->codconmov . ' Asociada a la empresa '.$datosempresa["codemp"];
				$tipoevento=true;
				if (ServicioCfg::comTransaccion())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje='Error al insertar el concepto de movimiento ' . $ArJson->codconmov . ' Asociada a la empresa '.$datosempresa["codemp"];
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_conceptomovimiento.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'buscarcodigo':
				$cad = $oservicio->buscarCodigoConmovimiento();
				echo "|{$cad}";
				break;
				
			case 'catalogo':
				$dataConceptoMov = $oservicio->buscarTodos('codconmov');
				echo generarJson($dataConceptoMov);
				unset($dataConceptoMov);	
				break;
				
			case 'actualizar':
				ServicioCfg::iniTransaccion ();
				$oservicio->modificarDto($ArJson);
				$mensaje='Modifico el concepto de movimiento ' . $ArJson->codconmov . ' Asociada a la empresa '.$datosempresa["codemp"];
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje='Error al Modificar el concepto de movimiento ' . $ArJson->codconmov . ' Asociada a la empresa '.$datosempresa["codemp"];
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_conceptomovimiento.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'eliminar':
				$ultimo=$oservicio->verificarUltimo('codconmov','scb_concepto'," WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."'",$ArJson->codconmov);
				if ($ultimo)
				{
					ServicioCfg::iniTransaccion ();
					$respuesta=$oservicio->eliminarDto($ArJson);
					$mensaje='Elimino el concepto de movimiento ' . $ArJson->codconmov . ' Asociada a la empresa '.$datosempresa["codemp"];
					$tipoevento=true;
					if (ServicioCfg::comTransaccion ())
					{
						echo "|1";
					} 
					else
					{
						if($respuesta='-1')
						{
							echo '|-9';
						}
						else
						{
							echo "|0";
						}
						$mensaje='Error al eliminar el concepto de movimiento ' . $ArJson->codconmov . ' Asociada a la empresa '.$datosempresa["codemp"];
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje='Error al eliminar el concepto de movimiento ' . $ArJson->codconmov . ' Asociada a la empresa '.$datosempresa["codemp"];
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_conceptomovimiento.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
		}
		unset($oservicio);
		unset($oregevent);
	}
}
?>