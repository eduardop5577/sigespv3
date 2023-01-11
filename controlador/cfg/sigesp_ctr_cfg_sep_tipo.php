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
		$oservicio = new ServicioCfg ('sep_tiposolicitud');

		switch ($ArJson->operacion)
		{
			case 'nuevo' :
				$oservicio = new ServicioCfg ('sep_tiposolicitud');
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				echo $oservicio->buscarCodigoTipoSolicitud();
				unset($oservicio); 
				break;
				
			case 'incluir':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
//SE ELIMINO ESTA VALIDACION PARA PODER GUARDAR EL TIPO DE SEP NECESARIA EN EL DIFERENCIAL DE IVA				
//				if(!($oservicio->verificarTipoSolicitud($_SESSION["la_empresa"]["codemp"],$ArJson->modsep,$ArJson->estope)))
//				{
					ServicioCfg::iniTransaccion ();
					$oservicio->incluirDto($ArJson);
					$mensaje="Inserto el Tipo de solicitud con codigo ".$ArJson->codtipsol;	
					$tipoevento=true;
					if (ServicioCfg::comTransaccion ())
					{
						echo "|1";
					}
					else
					{
						echo "|0";
						$mensaje="Error al Insertar el Tipo de solicitud con codigo ".$ArJson->codtipsol;	
						$tipoevento=false;
					}
//				}
//				else
//				{
//					$tiposep='Servicios';
//					if ($ArJson->modsep=='B')
//					{
//						$tiposep='Bienes';
//					}
//					else
//					{
//						if ($ArJson->modsep=='O')
//						{
//							$tipooperacion='Otros';
//						}
//					}
//					$tipooperacion='Precompromiso';
//					if ($ArJson->estope=='O')
//					{
//						$tipooperacion='Compromiso';
//					}
//					else
//					{
//						if ($ArJson->estope=='S')
//						{
//							$tipooperacion='Sin afectacion';
//						}
//					}
//					echo "Solo puede guardar un Tipo de Solicitud de ".$tiposep." de ".$tipooperacion."|-10";
//					$mensaje="Error al Insertar el Tipo de solicitud con codigo ".$ArJson->codtipsol;	
//					$tipoevento=false;
//				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_sep_tipo.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				unset($oservicio);
				unset($servicioEvento);
			break;
				
			case 'actualizar':
					$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
					ServicioCfg::iniTransaccion ();
					$oservicio->modificarDto($ArJson);
					$mensaje="Actualizo el Tipo de solicitud con codigo ".$ArJson->codtipsol;	
					$tipoevento=true;
					if (ServicioCfg::comTransaccion ())
					{
						echo "|1";
					}
					else
					{
						echo "|0";
						$mensaje="Error al actualizar el Tipo de solicitud con codigo ".$ArJson->codtipsol;	
						$tipoevento=false;
					}
					$servicioEvento->evento="MODIFICAR";
					$servicioEvento->codmenu=$ArJson->codmenu;
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_sep_tipo.php";
					$servicioEvento->desevetra=$mensaje;	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
					unset($oservicio);
					unset($servicioEvento);
				break;
			
			case 'eliminar':
				$oservicio = new ServicioCfg ('sep_tiposolicitud');
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oservicio->verificarUltimo('codtipsol','sep_tiposolicitud'," WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'",$ArJson->codtipsol);
				if ($ultimo)
				{
					ServicioCfg::iniTransaccion ();
					$respuesta = $oservicio->eliminarDto($ArJson,'codtipsol',$ArJson->codtipsol);
					$mensaje="Elimino el Tipo de solicitud con codigo ".$ArJson->codtipsol;	
					$tipoevento=true;
					if (ServicioCfg::comTransaccion () && $respuesta=='')
					{
						echo "|1";
					}
					else
					{
						if($respuesta!='')
						{
							if($respuesta=='-1')
							{
								echo '|-9';
							}
						}
						else
						{
							echo "|0";
						}
						$mensaje="Error al eliminar el Tipo de solicitud con codigo ".$ArJson->codtipsol;	
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje="Error al eliminar el Tipo de solicitud con codigo ".$ArJson->codtipsol;	
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_sep_tipo.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				unset($oservicio);
				unset($servicioEvento);
				break;
		
			case 'catalogo':
				$oservicio = new ServicioCfg ('sep_tiposolicitud');
				$cadenasql ="SELECT sep_tiposolicitud.codtipsol, sep_tiposolicitud.dentipsol, sep_tiposolicitud.estope, ".
							"		sep_tiposolicitud.modsep, sep_tiposolicitud.estayueco, sep_tiposolicitud.estdifiva, sep_tipooperacion.desoperacion ".
							"  FROM sep_tiposolicitud,sep_tipooperacion ".
							" WHERE sep_tiposolicitud.estope = sep_tipooperacion.codoperacion ".
							" ORDER BY sep_tiposolicitud.codtipsol";
				echo generarJson($oservicio->buscarSql($cadenasql));
				unset($oservicio);	
				break;
	
			case 'buscarTipoSep':
				$oservicio = new ServicioCfg ('sep_tiposolicitud');
				$cadenasql ="SELECT modsep,estope FROM sep_tiposolicitud";
				echo generarJson($oservicio->buscarSql($cadenasql));
				unset($oservicio);
				break;
		}
	}
}
?>