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
	require_once('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon']) 	
	{
		$submit = str_replace("\\","",$_POST['ObjSon']);
		$json = new Services_JSON;	
		$ArJson = $json->decode($submit);
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioCfg('spg_ministerio_ua');
		$oservicio->setCodemp($datosempresa["codemp"]);
		$Evento = $ArJson->oper;
		switch ($Evento)
		{
			case 'incluir':
				ServicioCfg::iniTransaccion ();
				$oservicio->incluirDto ( $ArJson );
				$mensaje='Inserto la Unidad Administradora ' . $ArJson->coduac . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje='Errar al insertar la Unidad Administradora ' . $ArJson->coduac . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_spg_unidadadministradora.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'buscarcodigo':
				$cad = $oservicio->buscarCodUnidadAdm();
				echo "|{$cad}";
				break;
				
			case 'catalogo':
				$Datos = $oservicio->buscarTodos();					
				$ObjSon = generarJson($Datos);
				echo $ObjSon;	
				break;
				
			case 'actualizar':
				ServicioCfg::iniTransaccion ();
				$oservicio->modificarDto($ArJson);
				$mensaje='Actualizo la Unidad Administradora ' . $ArJson->coduac . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje='Error al actualizar la Unidad Administradora ' . $ArJson->coduac . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_spg_unidadadministradora.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'eliminar':
				ServicioCfg::iniTransaccion ();
				$ultimo=$oservicio->verificarUltimo('coduac','spg_ministerio_ua'," WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'",$ArJson->coduac);
				if ($ultimo)
				{
					$respuesta = $oservicio->eliminarDto($ArJson, 'coduac', $ArJson->coduac);
					$mensaje='Elimino la Unidad Administradora ' . $ArJson->coduac . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];
					$tipoevento=true;
					if(ServicioCfg::comTransaccion () && $respuesta=='')
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
						$mensaje='Error al eliminar la Unidad Administradora ' . $ArJson->coduac . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje='Error al eliminar la Unidad Administradora ' . $ArJson->coduac . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_spg_unidadadministradora.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;			
		}
	}
}
?>