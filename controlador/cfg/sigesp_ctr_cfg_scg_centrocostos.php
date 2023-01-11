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
$datosempresa=$_SESSION["la_empresa"]; 
require_once('../../base/librerias/php/general/sigesp_lib_funciones.php');

$sessionvalida = validarSession();
if (($_POST['ObjSon']) && ($sessionvalida))
{
	$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
	require_once($dirsrv.'/base/librerias/php/general/Json.php');
	require_once($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
	require_once ('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon'])
	{
		$submit = str_replace ( "\\", "", $_POST['ObjSon'] );
		$json = new Services_JSON ( );
		$ArJson = $json->decode ( $submit );
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioCfg ( 'sigesp_cencosto' );
		$oservicio->setCodemp($datosempresa["codemp"]);
			
		switch ($ArJson->oper)
		{
			case 'incluir' :
				ServicioCfg::iniTransaccion ();
				$resultado=$oservicio->incluirDto ($ArJson,true,"codcencos",true);
				$arrcadres = explode(",",$resultado);
				$mensaje='Inserto el centro de costos numero ' . $ArJson->codcencos. ', asociado a la empresa '.$datosempresa['codemp'];
				$tipoevento=true;
				if($arrcadres[0]==1||$arrcadres[0]==-1)
				{
					if (ServicioCfg::comTransaccion ())
					{
						if($arrcadres[0]==1)
						{
							echo "|1";
						}
						else
						{
							echo "|".$arrcadres[0]."|".$arrcadres[1];
						}
					} 
					else
					{
						echo "|0";
						$mensaje='Error al insertar el centro de costos numero ' . $ArJson->codcencos. ', asociado a la empresa '.$datosempresa['codemp'];
						$tipoevento=false;
					}
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scg_centrocostos.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'catalogo' :
				$dataCentroCosto = $oservicio->buscarCentroCosto($datosempresa['codemp'], $ArJson->codcen, $ArJson->dencen);
				echo generarJson ( $dataCentroCosto );
				unset($dataCentroCosto);
				break;
			
			case 'nuevo' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				echo $oservicio->buscarCodigoCentroCosto();
				unset($oservicio); 
				break;
			
			case 'actualizar' :
				ServicioCfg::iniTransaccion ();
				$oservicio->modificarDto ( $ArJson );
				$mensaje='Actualizo el centro de costos numero ' . $ArJson->codcencos. ', asociado a la empresa '.$datosempresa['codemp'];
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje='Error al actualizar el centro de costos numero ' . $ArJson->codcencos. ', asociado a la empresa '.$datosempresa['codemp'];
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scg_centrocostos.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'eliminar' :
				ServicioCfg::iniTransaccion ();
				$resultado = $oservicio->eliminarDto ($ArJson, 'codcencos', $ArJson->codcencos);
				$mensaje='Elimino el centro de costos numero ' . $ArJson->codcencos. ', asociado a la empresa '.$datosempresa['codemp'];
				$tipoevento=true;
				if ($resultado == '')
				{
					if (ServicioCfg::comTransaccion ())
					{
						echo "|1";
					} 
					else
					{
						echo "|0";
						$mensaje='Error al eliminar el centro de costos numero ' . $ArJson->codcencos. ', asociado a la empresa '.$datosempresa['codemp'];
						$tipoevento=false;
					}
				}
				elseif($resultado=='-1')
				{
					echo '|-9';
					$mensaje='Error al eliminar el centro de costos numero ' . $ArJson->codcencos. ', asociado a la empresa '.$datosempresa['codemp'];
					$tipoevento=false;
				}
				else
				{
					echo "|0";
					$mensaje='Error al eliminar el centro de costos numero ' . $ArJson->codcencos. ', asociado a la empresa '.$datosempresa['codemp'];
					$tipoevento=false;
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scg_centrocostos.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
		}
		unset($oservicio);
		unset($servicioEvento);
	}
}
?>