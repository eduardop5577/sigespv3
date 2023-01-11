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
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioCfg('scb_banco');
		switch ($ArJson->oper)
		{    			
			case 'incluir':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$oservicio->incluirDto($ArJson);
				$mensaje="Inserto el Banco con codigo ".$ArJson->codban;	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al insertar el Banco con codigo ".$ArJson->codban;	
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_banco.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
		
			case 'catalogo':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$dataBanco = $oservicio->buscarTodos("codban");
				echo generarJson($dataBanco);
				unset($dataBanco);
				break;					
			
			case 'actualizar':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$oservicio->modificarDto ( $ArJson );
				$mensaje="Actualizo el Banco con codigo ".$ArJson->codban;	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al actualizar el Banco con codigo ".$ArJson->codban;	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_banco.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
		
			case 'eliminar':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oservicio->verificarUltimo('codban','scb_banco'," WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."'",$ArJson->codban);
				if ($ultimo)
				{
					ServicioCfg::iniTransaccion ();
					$respuesta = $oservicio->eliminarDto ($ArJson, 'codban', $ArJson->codban);
					$mensaje="Elimino el Banco con codigo ".$ArJson->codban;	
					$tipoevento=true;
					if($respuesta=='')
					{
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje="Erro al eliminar el Banco con codigo ".$ArJson->codban;	
							$tipoevento=false;
						}
					}
					else if($respuesta=='-1')
					{
						echo '|-9';
						$mensaje="Erro al eliminar el Banco con codigo ".$ArJson->codban;	
						$tipoevento=false;
					}
					else
					{
						echo "|0";
						$mensaje="Erro al eliminar el Banco con codigo ".$ArJson->codban;	
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje="Erro al eliminar el Banco con codigo ".$ArJson->codban;
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_banco.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'nuevo' :
				$contador="";
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$contador = $oservicio->buscarCodigoBanco();
				echo $json->encode($contador);
				break;
		}
		unset($json);
		unset($ArJson);
		unset($registroEventoDao);
		unset($oservicio);
	}
}
?>