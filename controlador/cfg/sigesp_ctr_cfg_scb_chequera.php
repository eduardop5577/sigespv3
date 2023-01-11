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
		$submit = str_replace ( "\\", "", $_POST ['ObjSon'] );
		$json = new Services_JSON();
		$ArJson = $json->decode($submit);
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioCfg('scb_cheques');
		
		switch ($ArJson->operacion)
		{
			case 'incluir' :
				$duplicado = '0';
				if($ArJson->chequesincluir)
				{
					ServicioCfg::iniTransaccion();
					$total = count((array)$ArJson->chequesincluir);
					for($j=0; $j<$total; $j++)
					{
						$mensaje="Inserto en CFG nuevo(s) cheque(s) ";	
						$tipoevento=true;
						$oservicio1 = new ServicioCfg('scb_cheques');
						$oservicio1->setCodemp ($_SESSION["la_empresa"]["codemp"]);
						$ArJson->chequesincluir[$j]->codemp  = $_SESSION["la_empresa"]["codemp"];
						if($oservicio1->verificarExistencia($ArJson->chequesincluir[$j]->codban,$ArJson->chequesincluir[$j]->ctaban,$ArJson->chequesincluir[$j]->numche))
						{
							$duplicado = '1';
						}
						else
						{
							$oservicio1->incluirDto ($ArJson->chequesincluir[$j]);
							$mensaje .= " -".$ArJson->chequesincluir[$j]->numche."";	
						}
						unset($oservicio1);
					}
					if (ServicioCfg::comTransaccion ())
					{
						echo "|1";
					}
					else
					{
						if($duplicado='1')
						{
							echo "|2";
						}
						else
						{
							echo "|0";
						}
						$mensaje="Error al insertar en CFG nuevo(s) cheque(s) ";	
						$tipoevento=false;
					}			
					$servicioEvento->evento="INSERTAR";
					$servicioEvento->codmenu=$ArJson->codmenu;
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_scb_chequera.php";
					$servicioEvento->desevetra=$mensaje;	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
				}
				break;
			
			case 'catalogo' :
				$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
				$dataChequera = $oservicio->obtenerChequera();
				echo generarJson($dataChequera);
				unset($dataChequera);
				break;
				
			case 'detalleschequera' :
				$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
				$dataCheque = $oservicio->obtenerChequesChequera($ArJson->codban,$ArJson->ctaban,$ArJson->numchequera);
				echo generarJson($dataCheque);
				unset($dataCheque);
				break;
				
			case 'detallesusuario' :
				$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
				$dataUsuario = $oservicio->obtenerUsuariosChequera($ArJson->codban,$ArJson->ctaban,$ArJson->numchequera);
				echo generarJson($dataUsuario);
				unset($dataUsuario);
				break;
			
			case 'actualizar' :
				if($ArJson->chequesincluir)
				{
					$mensaje="Inserto en CFG nuevo(s) cheque(s)";	
					$tipoevento=true;
					$total = count((array)$ArJson->chequesincluir);
					ServicioCfg::iniTransaccion();
					for($j=0; $j<$total; $j++)
					{
						$oservicio1 = new ServicioCfg('scb_cheques');
						$oservicio1->setCodemp ($_SESSION["la_empresa"]["codemp"]);
						$ArJson->chequesincluir[$j]->codemp  = $_SESSION["la_empresa"]["codemp"];
						$oservicio1->modificarDto($ArJson->chequesincluir[$j]);
						unset($oservicio1);
					}			
					if (ServicioCfg::comTransaccion ()) 
					{
						echo "|1";
					}
					else 
					{
						echo "|0";
						$mensaje="Error al insertar en CFG nuevo(s) cheque(s) ";	
						$tipoevento=false;
					}
					$servicioEvento->evento="MODIFICAR";
					$servicioEvento->codmenu=$ArJson->codmenu;
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_scb_chequera.php";
					$servicioEvento->desevetra=$mensaje;	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
				}
				break;
			
			case 'eliminar' :
				if($ArJson->chequeseliminar)
				{
					$mensaje="Elimino en CFG nuevo(s) cheque(s)";	
					$tipoevento=true;
					$total = count((array)$ArJson->chequeseliminar); 
					ServicioCfg::iniTransaccion();
					for($j=0; $j<$total; $j++)
					{
						$oservicio1 = new ServicioCfg('scb_cheques');
						$oservicio1->setCodemp ($_SESSION["la_empresa"]["codemp"]);
						$oservicio1->eliminarDto($ArJson->chequeseliminar[$j]);
						unset($oservicio1);
					}
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
						$mensaje="Error al eliminar en CFG nuevo(s) cheque(s)";	
						$tipoevento=false;
					}
					$servicioEvento->evento="MODIFICAR";
					$servicioEvento->codmenu=$ArJson->codmenu;
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_scb_chequera.php";
					$servicioEvento->desevetra=$mensaje;	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
				}			
				break;
	
				
			case 'verificarchequera' :
				$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
				$existe=false;
				$existe = $oservicio->verificarExistenciaChequera($ArJson->codban,$ArJson->ctaban,$ArJson->numchequera);
				$respuesta  = array('existe'=>$existe);
				$respuesta  = json_encode($respuesta);
				echo $respuesta; 
				break;
		
		}
		unset($oservicio);
	}
}
?>