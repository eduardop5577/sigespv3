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
	require_once ($dirsrv.'/modelo/servicio/cfg/sigesp_srv_cfg_spg_validacionestructura.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon']) 		
	{
		$submit = str_replace('\\', '', $_POST['ObjSon']);
		$json = new Services_JSON;
		$objetoJson = $json->decode($submit);
		$datosempresa= $_SESSION['la_empresa'];
		$servicioEvento = new ServicioEvento();
		switch ($objetoJson->operacion)
		{
			case 'obtenerNivel':
				if ($datosempresa['valestpre'] == '1')
				{
					echo $datosempresa['nivvalest'];
				}
				else
				{
					echo '-1';
				}
				break;	
				
			case 'cargarEstructuras':
				$servicioValidacionEstructura = new ServicioValidacionEstructura();
				$dataEstructura = $servicioValidacionEstructura->buscarEstructurasValidacion($datosempresa);
				echo generarJson($dataEstructura);
				unset($dataEstructura);
				unset($servicioValidacionEstructura);
				break;

			case 'grabar':
				$servicioValidacionEstructura = new ServicioValidacionEstructura();
				$resultado = $servicioValidacionEstructura->grabarEstructurasValidar($datosempresa['codemp'],$objetoJson);
				if ($resultado === 1)
				{
					echo "1";
					if ($servicioValidacionEstructura->mensajeinsertar<>'')
					{
						$servicioEvento->evento="INCLUIR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_validacionestructura.php";
						$servicioEvento->desevetra=$servicioValidacionEstructura->mensajeinsertar;
						$servicioEvento->tipoevento=true;
						$servicioEvento->incluirEvento();
					}
					if ($servicioValidacionEstructura->mensajeeliminar<>'')
					{
						$servicioEvento->evento="ELIMINAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_validacionestructura.php";
						$servicioEvento->desevetra=$servicioValidacionEstructura->mensajeeliminar;
						$servicioEvento->tipoevento=true;
						$servicioEvento->incluirEvento();
					}
				}
				else
				{
					echo "0";
					$servicioEvento->evento="INCLUIR";
					$servicioEvento->codmenu=$ArJson->codmenu;
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_spg_validacionestructura.php";
					$servicioEvento->desevetra='Error al cambiar la validación de las estructuras';
					$servicioEvento->tipoevento=false;
					$servicioEvento->incluirEvento();
				}
				unset($servicioValidacionEstructura);
				break;
		}
	}
}
?>