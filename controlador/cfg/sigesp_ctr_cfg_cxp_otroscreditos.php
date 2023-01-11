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
	require_once ('sigesp_ctr_cfg_servicio.php');
	
	if ($_POST['ObjSon'])
	{
		$submit = str_replace ( "\\", "", $_POST ['ObjSon'] );
		$json = new Services_JSON();
		$ArJson = $json->decode($submit);
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioCfg('sigesp_cargos');
		$Evento = $ArJson->oper;
	
		switch ($Evento)
		{
			case 'incluir' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$ArJson->porcar     = formatoNumericoBd($ArJson->porcar,1);
				$oservicio->incluirDto ( $ArJson );
				$mensaje="Inserto el cargo con codigo ".$ArJson->codcar.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al insertar el cargo con codigo ".$ArJson->codcar.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_cxp_otroscreditos.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'catalogo' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$datos = $oservicio->buscarTodos('codcar');
				$ObjSon = generarJson($datos);
				echo $ObjSon;
				break;
			
			case 'actualizar' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$ArJson->codemp = $_SESSION["la_empresa"]["codemp"];
				$ArJson->porcar = formatoNumericoBd($ArJson->porcar,1);
				$oservicio->modificarDto ( $ArJson );
				$mensaje="Actualizó el cargo con codigo ".$ArJson->codcar.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al actualizar el cargo con codigo ".$ArJson->codcar.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_cxp_otroscreditos.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'eliminar' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oservicio->verificarUltimo('codcar','sigesp_cargos'," WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'",$ArJson->codcar);
				if ($ultimo)
				{
					$tiene     = $oservicio->validarEliminar('codcar',$ArJson->codcar);//Verifica los movimientos asociados a la cuenta
					$mensaje="Elimino el cargo con codigo ".$ArJson->codcar.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=true;
					if(!$tiene)
					{  
						ServicioCfg::iniTransaccion ();
						$oservicio->eliminarDto ( $ArJson );
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						}
						else
						{
							echo "|0";
							$mensaje="Error al eliminar el cargo con codigo ".$ArJson->codcar.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
					}
					else
					{
						$arreglo = array ("mensaje" =>array('No se puede eliminar la deduccion '.$ArJson->codcar.', posee otras asociaciones en el sistema, verifique que no se haya utilizado anteriormente'));
						$respuesta = $json->encode ( $arreglo );
						echo $respuesta;
						$mensaje='No se puede eliminar la deduccion '.$ArJson->codcar.', posee otras asociaciones en el sistema, verifique que no se haya utilizado anteriormente';	
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje="Error al eliminar el cargo con codigo ".$ArJson->codcar.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;				
				}				
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_cxp_otroscreditos.php";
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
				$contador = $oservicio->buscarCodigoCargos();
				$ObjSon = $json->encode($contador);
				echo $ObjSon; 
			break;
		
		}
		unset($ArJson);
		unset($oregevent);
		unset($oservicio);
	}
}
?>