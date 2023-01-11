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
		$oservicio = new ServicioCfg('sigesp_unidad_tributaria');
		$Evento = $ArJson->oper;

		switch ($Evento)
		{
			case 'incluir' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$ArJson->codemp 	= $_SESSION["la_empresa"]["codemp"];			   
				$ArJson->fecentvig  = convertirFechaBd($ArJson->fecentvig);
				$ArJson->fecpubgac  = convertirFechaBd($ArJson->fecpubgac);
				$ArJson->fecdec     = convertirFechaBd($ArJson->fecdec);
				$ArJson->valunitri  = formatoNumericoBd($ArJson->valunitri,1);
				$oservicio->incluirDto ( $ArJson );
				$mensaje="Inserto la Unidad Tributaria con codigo ".$ArJson->codunitri;
				$tipoevento=true;
				if(ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al insertar la Unidad Tributaria con codigo ".$ArJson->codunitri;
					$tipoevento=true;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_unidadtributaria.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'catalogo' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$datos = $oservicio->buscarTodos();
				$ObjSon = generarJson($datos);
				echo $ObjSon;
				break;
			
			case 'actualizar' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ArJson->codemp 	= $_SESSION["la_empresa"]["codemp"];			   
				$ArJson->fecentvig  = convertirFechaBd($ArJson->fecentvig);
				$ArJson->fecpubgac  = convertirFechaBd($ArJson->fecpubgac);
				$ArJson->fecdec     = convertirFechaBd($ArJson->fecdec);
				$ArJson->valunitri  = formatoNumericoBd($ArJson->valunitri,1);
				ServicioCfg::iniTransaccion ();
				$oservicio->modificarDto ( $ArJson );
				$mensaje="Actualizo la Unidad Tributaria con codigo ".$ArJson->codunitri;
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al actualizar la Unidad Tributaria con codigo ".$ArJson->codunitri;
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_unidadtributaria.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'eliminar' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oservicio->verificarUltimo('codunitri','sigesp_unidad_tributaria'," WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."'",$ArJson->codunitri);
				if ($ultimo)
				{
					$ArJson->codemp 	= $_SESSION["la_empresa"]["codemp"];			   
					$ArJson->fecentvig  = convertirFechaBd($ArJson->fecentvig);
					$ArJson->fecpubgac  = convertirFechaBd($ArJson->fecpubgac);
					$ArJson->fecdec     = convertirFechaBd($ArJson->fecdec);
					$ArJson->valunitri  = formatoNumericoBd($ArJson->valunitri,1);
					ServicioCfg::iniTransaccion ();
					$oservicio->eliminarDto ( $ArJson );
					$mensaje="Elimino la Unidad Tributaria con codigo ".$ArJson->codunitri;
					$tipoevento=true;
					if (ServicioCfg::comTransaccion ())
					{
						$usado=$oservicio->verificarunidadtributaria($_SESSION["la_empresa"]["codemp"]);
						if ($usado>0)
						{
							echo "|-2";
						}
						else
						{
							echo "|1";
						}
					}
					else
					{
						echo "|0";
						$mensaje="Error al eliminar la Unidad Tributaria con codigo ".$ArJson->codunitri;
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje="Error al eliminar la Unidad Tributaria con codigo ".$ArJson->codunitri;	
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_unidadtributaria.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'claveprimaria' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$arregloClave = $oservicio->obtenerPrimaryKey();
				$jsonClave = $json->encode($arregloClave);
				echo $jsonClave; 
				break;
			
			case 'nuevo' :
				$contador="";
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$resultado = $oservicio->buscarCodigoUnidadTributaria($_SESSION["la_empresa"]["codemp"]);
				$ObjSon = $json->encode($resultado);
				echo $ObjSon; 
				break;
		}
	}
}
?>