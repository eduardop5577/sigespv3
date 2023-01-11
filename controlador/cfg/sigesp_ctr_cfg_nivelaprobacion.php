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
		$oservicio = new ServicioCfg('sigesp_nivel');
		switch ($ArJson->oper)
		{
			case 'catalogo':
				$data = $oservicio->buscarNivelesAprobacion($_SESSION["la_empresa"]["codemp"]);				
				echo  generarJson($data);
				unset($oservicio);
				unset($data);	
				break;
			
			case 'nuevo' :
				$contador="";
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$contador = $oservicio->buscarCodigoNivel();
				echo $json->encode($contador);
				break;

			case 'incluir':
				$numerror        = 0;
				$numguardado     = 0;
				$numerrorelim    = 0;
				$numguardadoelim = 0;

				$ncuenta = count((array)$ArJson->detallesincluir);
				$mensaje="Inserto lo(s) niveles(s) ";	
				$tipoevento=true;
				$mensajeerror='';
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				for($j=0;$j<=$ncuenta-1;$j++)
				{
					$auxdatos=$ArJson->detallesincluir[$j];
					$contador = $oservicio->buscarCodigoNivel();
					$auxdatos->codniv=$contador;
					$auxdatos->monnivdes=formatoNumericoBd($auxdatos->monnivdes ,1);
					$auxdatos->monnivhas=formatoNumericoBd($auxdatos->monnivhas ,1);
					ServicioCfg::iniTransaccion ();
					$oservicio->incluirDto ($auxdatos);
					if (ServicioCfg::comTransaccion ())
					{
						$numguardado++;
						$mensaje .= ''.$auxdatos->codniv.', ';
					}
					else
					{
						$numerror++;
						$mensajeerror="Error al Insertar el nivel ".$auxdatos->codniv;	
						$tipoevento=false;
					}
				}
				if($ncuenta>0)
				{
					$servicioEvento->evento="INSERTAR";
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_nivelaprobacion.php";
					$servicioEvento->desevetra=$mensaje;	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
				}
				echo $numerror."|".$mensajeerror;
				unset($planCuenta);
				break;

			case 'actualizar':
				$numerror        = 0;
				$numguardado     = 0;
				$numerrorelim    = 0;
				$numguardadoelim = 0;
				$ncuenta = count((array)$ArJson->detallesincluir);
				$mensaje="Insertó lo(s) niveles(s) ";	
				$tipoevento=true;
				$mensajeerror='';
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				for($j=0;$j<=$ncuenta-1;$j++)
				{
					ServicioCfg::iniTransaccion ();
					$auxdatos=$ArJson->detallesincluir[$j];
					if (!$oservicio->VerificarNivelAprobacion($_SESSION["la_empresa"]["codemp"],$auxdatos->codniv))
					{
						$contador = $oservicio->buscarCodigoNivel();
						$auxdatos->codniv=$contador;
						$auxdatos->monnivdes=formatoNumericoBd($auxdatos->monnivdes ,1);
						$auxdatos->monnivhas=formatoNumericoBd($auxdatos->monnivhas ,1);
						$oservicio->incluirDto ($auxdatos);
					}
					if (ServicioCfg::comTransaccion ())
					{
						
						$numguardado++;
						$mensaje .= ''.$auxdatos->codniv.', ';
					}
					else
					{
						$numerror++;
						$mensajeerror="Error al Insertar el nivel ".$auxdatos->codniv;	
						$tipoevento=false;
					}
				}
				if($ncuenta>0)
				{
					$servicioEvento->evento="INSERTAR";
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_nivelaprobacion.php";
					$servicioEvento->desevetra=$mensaje;	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
				}
				$ncuenta = count((array)$ArJson->detalleseliminar);
				$mensaje="eliminó lo(s) niveles(s) ";	
				$tipoevento=true;
				$mensajeerror='';
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				for($j=0;$j<=$ncuenta-1;$j++)
				{
					ServicioCfg::iniTransaccion ();
					$auxdatos=$ArJson->detalleseliminar[$j];
					if ($oservicio->VerificarNivelAprobacion($_SESSION["la_empresa"]["codemp"],$auxdatos->codniv))
					{
						$oservicio->eliminarDto ($auxdatos);
					}
					if (ServicioCfg::comTransaccion ())
					{
						
						$numguardado++;
						$mensaje .= ''.$auxdatos->codniv.', ';
					}
					else
					{
						$numerror++;
						$mensajeerror="Error al eliminar el nivel ".$auxdatos->codniv;	
						$tipoevento=false;
					}
				}
				if($ncuenta>0)
				{
					$servicioEvento->evento="ELIMINO";
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_nivelaprobacion.php";
					$servicioEvento->desevetra=$mensaje;	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
				}

				echo $numerror."|".$mensajeerror;
				unset($planCuenta);
				break;
			
			case 'nuevoasignacion' :
				$oservicio = new ServicioCfg('sigesp_asig_nivel');
				$contador="";
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$contador = $oservicio->buscarCodigoAsignacionNivel();
				echo $json->encode($contador);
				break;

			case 'catalogoasignacion':
				$oservicio = new ServicioCfg('sigesp_asig_nivel');
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$data = $oservicio->buscarAsignacionNivelesAprobacion($_SESSION["la_empresa"]["codemp"]);				
				echo  generarJson($data);
				unset($oservicio);
				unset($data);	
				break;

			case 'incluirasignacion':
				$oservicio = new ServicioCfg('sigesp_asig_nivel');
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ArJson->codemp=$_SESSION["la_empresa"]["codemp"];
				ServicioCfg::iniTransaccion ();
				$oservicio->incluirDto ($ArJson);
				$mensaje="Inserto la asignacion de nivel de aprobación ".$ArJson->codasiniv."";	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al Insertar la asignacion de nivel de aprobación ".$ArJson->codasiniv."";	
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_asignacionnivelaprobacion.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'actualizarasignacion':
				$oservicio = new ServicioCfg('sigesp_asig_nivel');
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$oservicio->modificarDto ( $ArJson );
				$mensaje="Actualizo la asignacion de nivel de aprobación ".$ArJson->codasiniv."";	
				$tipoevento=true;
				if(ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al actualizar la asignacion de nivel de aprobación ".$ArJson->codasiniv."";
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_asignacionnivelaprobacion.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'eliminarasignacion':
				$oservicio = new ServicioCfg('sigesp_asig_nivel');
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oservicio->verificarUltimo('codasiniv','sigesp_asig_nivel'," WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'",$ArJson->codasiniv);
				if ($ultimo)
				{
					ServicioCfg::iniTransaccion ();
					$oservicio->eliminarDto ( $ArJson );
					$mensaje="Elimino la asignacion de nivel de aprobación ".$ArJson->codasiniv."";	
					$tipoevento=true;
					if(ServicioCfg::comTransaccion ())
					{
						echo "|1";
					}
					else
					{
						echo "|0";
						$mensaje="Error al Eliminar la asignacion de nivel de aprobación ".$ArJson->codasiniv."";	
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje="Error al Eliminar la asignacion de nivel de aprobación ".$ArJson->codasiniv."";	
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_asignacionnivelaprobacion.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;	
		}
		unset($oservicio);
	}
}
?>