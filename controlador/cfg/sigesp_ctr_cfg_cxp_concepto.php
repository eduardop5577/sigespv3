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
		$oservicio = new ServicioCfg('cxp_clasificador_rd');
		
		switch ($ArJson->oper)
		{
			case 'incluir' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$oservicio->incluirDto ( $ArJson );
				$mensaje="Inserto el concepto con codigo ".$ArJson->codcla.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al insertar el concepto con codigo ".$ArJson->codcla.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_cxp_concepto.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$mensaje;
				$servicioEvento->incluirEvento();
				break;
			
			case 'catalogo' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$cadenasql = "SELECT cxp_clasificador_rd.codemp, cxp_clasificador_rd.codcla, cxp_clasificador_rd.dencla, cxp_clasificador_rd.sc_cuenta, ".
				             "       (SELECT COUNT(cxp_rd.codcla) ".
				             "          FROM cxp_rd ".
				             "         WHERE cxp_rd.codemp = cxp_clasificador_rd.codemp ".
				             "           AND cxp_rd.codcla = cxp_clasificador_rd.codcla) AS usado".
							 "  FROM cxp_clasificador_rd  ".
							 " WHERE cxp_clasificador_rd.codemp = '".$datosempresa["codemp"]."'  ".
							 " ORDER BY cxp_clasificador_rd.codcla";
				$dataConcepto = $oservicio->buscarSql($cadenasql);
				echo generarJson($dataConcepto);
				unset($dataConcepto);
				break;
			
			case 'actualizar' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$oservicio->modificarDto ( $ArJson );
				$mensaje="Actualizó el concepto con codigo ".$ArJson->codcla.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al actualizar el concepto con codigo ".$ArJson->codcla.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_cxp_concepto.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$mensaje;
				$servicioEvento->incluirEvento();
				break;
			
			case 'eliminar' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oservicio->verificarUltimo('codcla','cxp_clasificador_rd'," WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'",$ArJson->codcla);
				if ($ultimo)
				{
					ServicioCfg::iniTransaccion ();
					$oservicio->eliminarDto ( $ArJson );
					$mensaje="Eliminó el concepto con codigo ".$ArJson->codcla.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=true;
					if (ServicioCfg::comTransaccion ())
					{
						echo "|1";
					}
					else
					{
						echo "|0";
						$mensaje="Error al eliminar el concepto con codigo ".$ArJson->codcla.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje="Error al eliminar el concepto con codigo ".$ArJson->codcla.", asociado a la empresa ".$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_cxp_concepto.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$mensaje;
				$servicioEvento->incluirEvento();
				break;
				
			case 'claveprimaria' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$arregloClave = $oservicio->obtenerPrimaryKey();
				echo $json->encode($arregloClave);
				break;
				
			case 'nuevo' :
				$contador="";
				$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
				$contador = $oservicio->buscarCodigoConcepto($_SESSION["la_empresa"]["codemp"]);
				echo $json->encode($contador);
				break;
		}
		unset($oservicio);
	}
}
?>