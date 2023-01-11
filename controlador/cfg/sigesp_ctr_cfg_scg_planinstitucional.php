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
	require_once($dirsrv.'/modelo/servicio/cfg/sigesp_srv_cfg_scg_planinstitucional.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon'])
	{
		$submit    = str_replace("\\","",$_POST['ObjSon']);
		$json      = new Services_JSON;	
		$arrJson    = $json->decode($submit);
		$arrEmpresa   = $_SESSION["la_empresa"];
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioPlanInstitucional();
		
		switch ($arrJson->oper)
		{
			case 'catalogo':
				$dataCuentas = $oservicio->buscarTodasCuenta($arrEmpresa['codemp']);				
				echo generarJson($dataCuentas);
				unset($dataCuentas);
				unset($oservicio);
				break;
				
			case 'incluir':
				$oservicio->grabarCuenta($arrJson,'I');
				$mensaje="Inserto la cuenta {$arrJson->sc_cuenta} en el plan de cuentas institucional, asociada a la empresa {$arrEmpresa['codemp']} ";	
				$tipoevento=true;
				if($oservicio->valido)
				{
					echo '|1';
				}
				else
				{
					echo $oservicio->mensaje.'|0';
					$mensaje="Error al Insertar la cuenta {$arrJson->sc_cuenta} en el plan de cuentas institucional, asociada a la empresa {$arrEmpresa['codemp']} ";	
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$arrEmpresa['codemp'];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scg_planinstitucional.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			   
			case 'actualizar':
				$oservicio->grabarCuenta($arrJson,'A');
				$mensaje="Actualizo la denominacion de la cuenta {$arrJson->sc_cuenta} en el plan de cuentas institucional, asociada a la empresa {$arrEmpresa['codemp']} ";	
				$tipoevento=true;
				if($oservicio->valido)
				{
					echo '|1';
				}
				else
				{
					echo $oservicio->mensaje.'|0';
					$mensaje="Error al Actualizar la denominacion de la cuenta {$arrJson->sc_cuenta} en el plan de cuentas institucional, asociada a la empresa {$arrEmpresa['codemp']} ";	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$arrEmpresa['codemp'];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scg_planinstitucional.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'eliminar':
				$respuesta = $oservicio->eliminarCuenta($arrJson->sc_cuenta);
				$mensaje="Elimino la cuenta {$arrJson->sc_cuenta} en el plan de cuentas institucional, asociada a la empresa {$arrEmpresa['codemp']} ";	
				$tipoevento=true;
				if($respuesta=='1')
				{
					echo '|1';
				}
				else
				{
					echo $respuesta.'|0';
					$mensaje="Error al eliminar la cuenta {$arrJson->sc_cuenta} en el plan de cuentas institucional, asociada a la empresa {$arrEmpresa['codemp']} ";	
					$tipoevento=true;
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$arrEmpresa['codemp'];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scg_planinstitucional.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'catalogoCuentaAcum':
				$dataCuentasAcum = $oservicio->buscarCuentaProvAcumResTec($arrEmpresa['cueproacu'], $arrEmpresa['cuedepamo']);				
				echo generarJson($dataCuentasAcum);
				unset($dataCuentasAcum);
				unset($oservicio);
				break;
		}
	}
}
?>