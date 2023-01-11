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
	require_once($dirsrv.'/modelo/servicio/cfg/sigesp_srv_cfg_spg_traspasocuentas.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon']) 		
	{
		$submit     = str_replace("\\","",$_POST['ObjSon']);
		$json       = new Services_JSON;	
		$arrJson    = $json->decode($submit);
		$arrEmpresa = $_SESSION["la_empresa"];
		$servicioEvento = new ServicioEvento();
		
		switch ($arrJson->operacion)
		{
			case 'procesar':
				$servicioTraspasoCuentas = new ServicioTraspasoCuentas();
				$resultado = $servicioTraspasoCuentas->traspasarCuentas($arrEmpresa['codemp'], $arrJson);
				$cadEstOrigen  = $arrjson->estOrigen[0]->codestpro1.'-'.$arrjson->estOrigen[0]->codestpro2.'-'.$arrjson->estOrigen[0]->codestpro3.'-'.$arrjson->estOrigen[0]->codestpro4.'-'.$arrjson->estOrigen[0]->codestpro5;
				$cadEstDestino = $arrjson->estDestino[0]->codestpro1.'-'.$arrjson->estDestino[0]->codestpro2.'-'.$arrjson->estDestino[0]->codestpro3.'-'.$arrjson->estDestino[0]->codestpro4.'-'.$arrjson->estDestino[0]->codestpro5;
				$mensaje = "Copio las cuentas de la estructura {$cadEstOrigen} a la estructura {$cadEstDestino}";
				$tipoevento=true;
				if (!empty($resultado))
				{
					echo $resultado[0].'|'.$resultado[1].'|'.trim($resultado[2]);
				}
				else
				{
					echo '-1|0|0';
					$mensaje = "Error al copiar las cuentas de la estructura {$cadEstOrigen} a la estructura {$cadEstDestino}";
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_spg_traspasocuentas.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				unset($servicioTraspasoCuentas);
				break;
		}
	}
}
?>