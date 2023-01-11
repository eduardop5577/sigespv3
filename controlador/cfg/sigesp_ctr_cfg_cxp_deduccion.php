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

	$datosempresa=$_SESSION["la_empresa"];
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
		$oservicio = new ServicioCfg('sigesp_deducciones');
		$Evento = $ArJson->operacion;
	
		switch ($Evento)
		{
			case 'incluir' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$ArJson->codemp = $_SESSION["la_empresa"]["codemp"];
				$oservicio->guardarDeduccion($ArJson );
				$mensaje="Inserto la deduccion con codigo ".$ArJson->codded.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al Insertar la deduccion con codigo ".$ArJson->codded.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_cxp_deduccion.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'catalogo' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$datos = $oservicio->buscarDeducciones($_SESSION["la_empresa"]["codemp"]);
				$ObjSon = generarJson($datos);
				echo $ObjSon;
				break;
			
			case 'actualizar' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$ArJson->codemp = $_SESSION["la_empresa"]["codemp"];			   
				$oservicio->modificarDeduccion ( $ArJson );
				$mensaje="Actualizo la deduccion con codigo ".$ArJson->codded.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al actualizar la deduccion con codigo ".$ArJson->codded.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_cxp_deduccion.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'eliminar' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oservicio->verificarUltimo('codded','sigesp_deducciones'," WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'",$ArJson->codded);
				if ($ultimo)
				{
					$tiene     = $oservicio->validarEliminar('codded',$ArJson->codded);//Verifica los movimientos asociados a la cuenta
					$mensaje="Elimino la deduccion con codigo ".$ArJson->codded.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=true;
					if(!$tiene)
					{  
						ServicioCfg::iniTransaccion ();
						$oservicio->eliminarDto($ArJson);
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						}
						else
						{
							echo "|0";
							$mensaje="Error al eliminar la deduccion con codigo ".$ArJson->codded.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
					}
					else
					{
						$arreglo = array ("mensaje" =>'No se puede eliminar la deducci&#243;n '.$ArJson->codded.', posee otras asociaciones en el sistema');
						$mensaje='No se puede eliminar la deducci&#243;n '.$ArJson->codded.', posee otras asociaciones en el sistema';	
						$tipoevento=false;
						echo $json->encode ( $arreglo );
					}
				}
				else
				{
					echo "|-8";
					$mensaje="Error al eliminar la deduccion con codigo ".$ArJson->codded.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_cxp_deduccion.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'claveprimaria' :
				$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
				$arregloClave = $oservicio->obtenerPrimaryKey();
				$jsonClave = $json->encode($arregloClave);
				echo $jsonClave; 
				break;
				
			case 'nuevo' :
				$contador="";
				$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
				$contador = $oservicio->buscarCodigoDeduccion($_SESSION["la_empresa"]["codemp"]);
				$ObjSon = $json->encode($contador);
				echo $ObjSon; 
				break;
			
			case 'valmoddeduccion' :
				$valido = false;
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$valido     = $oservicio->validarEliminar('codded',$ArJson->codded);//Verifica los movimientos asociados a la cuenta
				$arregloJson = array ("valido" => $valido );
				$cadenaJson = $json->encode ($arregloJson);
				echo $cadenaJson;
				break;
		}
	}
}
?>