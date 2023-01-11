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
		$oserviciocfg = new ServicioCfg('scb_tipocolocacion');
		$servicioEvento = new ServicioEvento();
		$Evento = $ArJson->oper;
		$oserviciocfg->setCodemp ($_SESSION["la_empresa"]["codemp"]);
		switch ($Evento)
		{
			
			case 'incluir':
				$ArJson->codemp=$_SESSION["la_empresa"]["codemp"];
				ServicioCfg::iniTransaccion ();
				$oserviciocfg->incluirDto($ArJson);
				$mensaje="Inserto el Tipo de Colocacion con codigo ".$ArJson->codtipcol." ";
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ()) {
					echo "|1";
				}
				else 
				{
					echo "|0";
					$mensaje="Error al insertar el Tipo de Colocacion con codigo ".$ArJson->codtipcol." ";
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_tipocolocacion.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'catalogo':
				$oserviciocfg->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$datos = $oserviciocfg->buscarTodos("codtipcol");
				$ObjSon = generarJson($datos);
				echo $ObjSon;
				break;
				
			case 'actualizar':
				ServicioCfg::iniTransaccion ();
				$oserviciocfg->modificarDto($ArJson);
				$mensaje="Actualizo el Tipo de Colocacion con codigo ".$ArJson->codtipcol." ";
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else 
				{
					echo "|0";
					$mensaje="Error al actualizar el Tipo de Colocacion con codigo ".$ArJson->codtipcol." ";
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_tipocolocacion.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'eliminar':
				$oserviciocfg->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oserviciocfg->verificarUltimo('codtipcol','scb_tipocolocacion'," WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."'",$ArJson->codtipcol);
				if ($ultimo)
				{
					ServicioCfg::iniTransaccion ();
					$respuesta = $oserviciocfg->eliminarDto($ArJson, 'codtipcol', $ArJson->codtipcol);
					$mensaje="Elimino el Tipo de Colocacion con codigo ".$ArJson->codtipcol." ";
					$tipoevento=true;
					if (ServicioCfg::comTransaccion () && $respuesta=='')
					{
						echo "|1";
					}
					else
					{
						if($respuesta==-1)
						{
							echo '|-9';
						}
						else
						{
							echo "|0";
						}
						$mensaje="Error al eliminar el Tipo de Colocacion con codigo ".$ArJson->codtipcol." ";
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje="Error al eliminar el Tipo de Colocacion con codigo ".$ArJson->codtipcol." ";
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_tipocolocacion.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;	
				
			case 'claveprimaria' :
				$oserviciocfg->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$arregloClave = $oserviciocfg->obtenerPrimaryKey();
				$jsonClave = $json->encode($arregloClave);
				echo $jsonClave;
				unset($oserviciocfg); 
				break;
				
			case 'nuevo' :
				$contador="";
				$oserviciocfg->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$contador = $oserviciocfg->buscarCodigoTipoColocacion($_SESSION["la_empresa"]["codemp"]);
				$ObjSon = $json->encode($contador);
				echo $ObjSon;
				unset($oservicio); 
				break;
		}
	}
}
?>
