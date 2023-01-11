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
		$submit = str_replace("\\", "", $_POST['ObjSon']);
		$json = new Services_JSON;
		$ArJson = $json->decode($submit);
		$servicioEvento = new ServicioEvento();
		$oservicioemp = new ServicioCfg('sigesp_empresa');
		for ($j = 1; $j <= 5; $j++)
		{
			$ArObjetos[$j] = new ServicioCfg('spg_ep'.$j);
			$ArObjetos[$j]->setCodemp($datosempresa["codemp"]);
		}
		switch ($ArJson->oper)
		{
			case 'getSesion':
				$datosnivel = $oservicioemp->bucarNivelesPresupuesto($datosempresa["codemp"]);
				$Cantidad   = $oservicioemp->bucarCantNivelPresu($datosempresa["codemp"]);
				$Texto = GenerarJsonDeObjetos($datosnivel);
				echo $Cantidad."|".$Texto;
				unset($datosnivel);
				unset($ArObjetos);
				unset($oregevent);
				unset($oservicioemp);
				break;
				
			case 'actualizarvarios':
				$numestructura = $ArJson->numest;
				switch ($numestructura)
				{
					case 1:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[1]->modificarDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 2:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[2]->modificarDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro2 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro2 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 3:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[3]->modificarDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro3 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						//aqui se dispara el rellenar de las estructuras restantes...
						for($i=4;$i<=5;$i++)
						{
							if($ArJson->numest < $i-1)
							{
								$ArObjetos[$i]->setEstructura($i-1,'0000000000000000000000000','NINGUNO');
							}
							$ArObjetos[$i]->setEstructura($i,'0000000000000000000000000','NINGUNO');
							$auxdatos=$ArJson->datos[0];
							$auxdatos->codfuefin=$ArJson->datos[0]->codfuefin;
							$ArObjetos[$i]->modificarDto ( $auxdatos );
						}
						$Cantidad   = $oservicioemp->bucarCantNivelPresu($datosempresa["codemp"]);
						if($Cantidad==3)
						{
							//incluir fuente financiamiento por defecto
							$objFuenteFinDefecto = new ServicioCfg('spg_dt_fuentefinanciamiento');
							$objFuenteFinDefecto->setCodemp($datosempresa["codemp"]);
							$objFuenteFinDefecto->setFuenteDefecto($ArJson->datos[0], true);
						}
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro3 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
							
					case 4:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[4]->modificarDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro4 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro4 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 5:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[5]->modificarDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro5 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						//incluir fuente financiamiento por defecto
						$objFuenteFinDefecto = new ServicioCfg('spg_dt_fuentefinanciamiento');
						$objFuenteFinDefecto->setCodemp($datosempresa["codemp"]);
						$objFuenteFinDefecto->setFuenteDefecto($ArJson->datos[0], false);

						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro5 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
				}
				unset($ArObjetos);
				unset($oregevent);
				unset($oservicioemp);
				break;
			
			case 'eliminar':
				$numestructura = $ArJson->numest;
				switch ($numestructura)
				{
					case 1:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[1]->eliminarDto ( $ArJson->datos[0] );
						$mensaje='Elimino la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al eliminar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="ELIMINAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 2:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[2]->eliminarDto ( $ArJson->datos[0] );
						$mensaje='Elimino la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro2 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al eliminar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro2 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="ELIMINAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 3:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[3]->eliminarDto ( $ArJson->datos[0] );
						$mensaje='Elimino la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro3 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al eliminar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro3 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="ELIMINAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
							
					case 4:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[4]->eliminarDto ( $ArJson->datos[0] );
						$mensaje='Elimino la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro4 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al eliminar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro4 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="ELIMINAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 5:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[5]->eliminarDto ( $ArJson->datos[0] );
						$mensaje='Elimino la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al eliminar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="ELIMINAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
				}
				unset($ArObjetos);
				unset($oregevent);
				unset($oservicioemp);
				break;
			
			case 'eliminarUltimo':
			   $numestructura = $ArJson->numest;
				switch ($numestructura)
				{
					case 1:
						ServicioCfg::iniTransaccion ();
						//aqui se dispara el eliminar de las estructuras por defecto...
						for($i=5;$i>=2;$i--)
						{
							if($ArJson->numest < $i-1)
							{
								$ArObjetos[$i]->setEstructura($i-1,'0000000000000000000000000','NINGUNO');
							}
							$ArObjetos[$i]->setEstructura($i,'0000000000000000000000000','NINGUNO');
							$auxdatos=$ArJson->datos[0];
							$auxdatos->codfuefin='--';
							$ArObjetos[$i]->eliminarDto ( $ArJson->datos[0] );
						}
						//incluir fuente financiamiento por defecto
						$ArObjetos[1]->eliminarDto ( $ArJson->datos[0] );
						$mensaje='Elimino la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al eliminar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="ELIMINAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 2:
						ServicioCfg::iniTransaccion ();
						//aqui se dispara el eliminar de las estructuras por defecto...
						for($i=5;$i>=3;$i--)
						{
							if($ArJson->numest < $i-1)
							{
								$ArObjetos[$i]->setEstructura($i-1,'0000000000000000000000000','NINGUNO');
							}
							$ArObjetos[$i]->setEstructura($i,'0000000000000000000000000','NINGUNO');
							$auxdatos=$ArJson->datos[0];
							$auxdatos->codfuefin='--';
							$ArObjetos[$i]->eliminarDto ( $ArJson->datos[0] );
						}
						$ArObjetos[2]->eliminarDto ( $ArJson->datos[0] );
						$mensaje='Elimino la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro2 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al eliminar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro2 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="ELIMINAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 3:
						ServicioCfg::iniTransaccion ();
						//aqui se dispara el eliminar de las estructuras por defecto...
						for($i=5;$i>=4;$i--)
						{
							if($ArJson->numest < $i-1)
							{
								$ArObjetos[$i]->setEstructura($i-1,'0000000000000000000000000','NINGUNO');
							}
							$ArObjetos[$i]->setEstructura($i,'0000000000000000000000000','NINGUNO');
							$objFuenteFinDefecto = new ServicioCfg('spg_dt_fuentefinanciamiento');
							$objFuenteFinDefecto->setCodemp($datosempresa["codemp"]);
							$auxdatos=$ArJson->datos[0];
							$auxdatos->codfuefin='--';
							$auxdatos->codestpro4='0000000000000000000000000';
							$auxdatos->codestpro5='0000000000000000000000000';
							$objFuenteFinDefecto->eliminarDto ($auxdatos);
							unset($objFuenteFinDefecto);
							unset($auxdatos);

							$auxdatos=$ArJson->datos[0];
							$auxdatos->codfuefin='--';
							$ArObjetos[$i]->eliminarDto ( $auxdatos );
						}
						$ArObjetos[3]->eliminarDto ( $ArJson->datos[0] );
						$mensaje='Elimino la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro3 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al eliminar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro3 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="ELIMINAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
							
					case 4:
						ServicioCfg::iniTransaccion ();
						//aqui se dispara el eliminar de las estructuras por defecto...
						for($i=5;$i>=5;$i--)
						{
							if($ArJson->numest < $i-1)
							{
								$ArObjetos[$i]->setEstructura($i-1,'0000000000000000000000000','NINGUNO');
							}
							$ArObjetos[$i]->setEstructura($i,'0000000000000000000000000','NINGUNO');
							$auxdatos=$ArJson->datos[0];
							$auxdatos->codfuefin='--';
							$ArObjetos[$i]->eliminarDto ( $auxdatos );
						}
						$ArObjetos[4]->eliminarDto ( $ArJson->datos[0] );
						$mensaje='Elimino la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro4 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al eliminar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro4 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="ELIMINAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 5:
						ServicioCfg::iniTransaccion ();
						$objFuenteFinDefecto = new ServicioCfg('spg_dt_fuentefinanciamiento');
						$objFuenteFinDefecto->setCodemp($datosempresa["codemp"]);
						$auxdatos=$ArJson->datos[0];
						$auxdatos->codfuefin='--';
						$objFuenteFinDefecto->eliminarDto ($auxdatos);
						unset($objFuenteFinDefecto);
						unset($auxdatos);
						$ArObjetos[5]->eliminarDto ( $ArJson->datos[0] );
						$mensaje='Elimino la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro5 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al eliminar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro5 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="ELIMINAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
				}
				unset($ArObjetos);
				unset($oregevent);
				unset($oservicioemp);
				break;
			
			case 'filtrarEst':
				$numestructura = $ArJson->numest;
				switch ($numestructura)
				{
					case 1:
						$datosestructura = $ArObjetos[1]->buscarEstructuraNiv1($datosempresa["codemp"]);
						break;
						
					case 2:
						$restriccion[0][0] = 'codemp';
						$restriccion[0][1] = '=';
						$restriccion[0][2] = $datosempresa["codemp"];
						$restriccion[0][3] = 0;
						$restriccion[1][0] = 'codestpro1';
						$restriccion[1][1] = '=';
						$restriccion[1][2] = $ArJson->cod1;
						$restriccion[1][3] = 0;
						$restriccion[2][0] = 'estcla';
						$restriccion[2][1] = '=';
						$restriccion[2][2] = $ArJson->estcla;
						$restriccion[2][3] = 2;
						$datosestructura = $ArObjetos[2]->buscarCampoRestriccion($restriccion);
						break;
						
					case 3:
						$restriccion[0][0] = 'codemp';
						$restriccion[0][1] = '=';
						$restriccion[0][2] = $datosempresa["codemp"];
						$restriccion[0][3] = 0;
						$restriccion[1][0] = 'codestpro1';
						$restriccion[1][1] = '=';
						$restriccion[1][2] = $ArJson->cod1;
						$restriccion[1][3] = 0;
						$restriccion[2][0] = 'codestpro2';
						$restriccion[2][1] = '=';
						$restriccion[2][2] = $ArJson->cod2;
						$restriccion[2][3] = 0;
						$restriccion[3][0] = 'estcla';
						$restriccion[3][1] = '=';
						$restriccion[3][2] = $ArJson->estcla;
						$restriccion[3][3] = 2;
						$datosestructura = $ArObjetos[3]->buscarCampoRestriccion($restriccion);
						break;
					  
					case 4:
						$restriccion[0][0] = 'codemp';
						$restriccion[0][1] = '=';
						$restriccion[0][2] = $datosempresa["codemp"];
						$restriccion[0][3] = 0;
						$restriccion[1][0] = 'codestpro1';
						$restriccion[1][1] = '=';
						$restriccion[1][2] = $ArJson->cod1;
						$restriccion[1][3] = 0;
						$restriccion[2][0] = 'codestpro2';
						$restriccion[2][1] = '=';
						$restriccion[2][2] = $ArJson->cod2;
						$restriccion[2][3] = 0;
						$restriccion[3][0] = 'codestpro3';
						$restriccion[3][1] = '=';
						$restriccion[3][2] = $ArJson->cod3;
						$restriccion[3][3] = 0;
						$restriccion[4][0] = 'estcla';
						$restriccion[4][1] = '=';
						$restriccion[4][2] = $ArJson->estcla;
						$restriccion[4][3] = 2;
						$datosestructura = $ArObjetos[4]->buscarCampoRestriccion($restriccion);
						break;
						
					case 5:
						$restriccion[0][0] = 'codemp';
						$restriccion[0][1] = '=';
						$restriccion[0][2] = $datosempresa["codemp"];
						$restriccion[0][3] = 0;
						$restriccion[1][0] = 'codestpro1';
						$restriccion[1][1] = '=';
						$restriccion[1][2] = $ArJson->cod1;
						$restriccion[1][3] = 0;
						$restriccion[2][0] = 'codestpro2';
						$restriccion[2][1] = '=';
						$restriccion[2][2] = $ArJson->cod2;
						$restriccion[2][3] = 0;
						$restriccion[3][0] = 'codestpro3';
						$restriccion[3][1] = '=';
						$restriccion[3][2] = $ArJson->cod3;
						$restriccion[3][3] = 0;
						$restriccion[4][0] = 'codestpro4';
						$restriccion[4][1] = '=';
						$restriccion[4][2] = $ArJson->cod4;
						$restriccion[4][3] = 0;
						$restriccion[5][0] = 'estcla';
						$restriccion[5][1] = '=';
						$restriccion[5][2] = $ArJson->estcla;
						$restriccion[5][3] = 2;
						$datosestructura = $ArObjetos[5]->buscarCampoRestriccion($restriccion);
						break;
				}
				echo generarJson($datosestructura);
				unset($datosestructura);
				unset($ArObjetos);
				unset($oregevent);
				unset($oservicioemp);
				break;
			
			case 'incluirestpro':
				$numestructura = $ArJson->numest;
				switch ($numestructura)
				{
					case 1:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[1]->incluirDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 2:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[2]->incluirDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro2 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro2 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 3:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[3]->incluirDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro3 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro3 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
							
					case 4:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[4]->incluirDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro4 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro4 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 5:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[5]->incluirDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro5 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro5 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
				}
				unset($ArObjetos);
				unset($oregevent);
				unset($oservicioemp);            
				break;
			
			case 'incluirUltimo':
				$numestructura = $ArJson->numest;
				switch ($numestructura)
				{
					case 1:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[1]->incluirDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						//aqui se dispara el rellenar de las estructuras restantes...
						for($i=2;$i<=5;$i++)
						{
							if($ArJson->numest < $i-1)
							{
								$ArObjetos[$i]->setEstructura($i-1,'0000000000000000000000000','NINGUNO');
							}
							$ArObjetos[$i]->setEstructura($i,'0000000000000000000000000','NINGUNO');
							$auxdatos=$ArJson->datos[0];
							$auxdatos->codfuefin=$ArJson->datos[0]->codfuefin;
							$ArObjetos[$i]->incluirDto ( $ArJson->datos[0] );
						}
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro1 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 2:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[2]->incluirDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro2 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						//aqui se dispara el rellenar de las estructuras restantes...
						for($i=3;$i<=5;$i++)
						{
							if($ArJson->numest < $i-1)
							{
								$ArObjetos[$i]->setEstructura($i-1,'0000000000000000000000000','NINGUNO');
							}
							$ArObjetos[$i]->setEstructura($i,'0000000000000000000000000','NINGUNO');
							$auxdatos=$ArJson->datos[0];
							$auxdatos->codfuefin = $ArJson->datos[0]->codfuefin;
							$ArObjetos[$i]->incluirDto ( $ArJson->datos[0] );
						}
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro2 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 3:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[3]->incluirDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro3 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						//aqui se dispara el rellenar de las estructuras restantes...
						for($i=4;$i<=5;$i++)
						{
							if($ArJson->numest < $i-1)
							{
								$ArObjetos[$i]->setEstructura($i-1,'0000000000000000000000000','NINGUNO');
							}
							$ArObjetos[$i]->setEstructura($i,'0000000000000000000000000','NINGUNO');
							$auxdatos=$ArJson->datos[0];
							$auxdatos->codfuefin=$ArJson->datos[0]->codfuefin;
							$ArObjetos[$i]->incluirDto ( $auxdatos );
						}
						
						//incluir fuente financiamiento por defecto
						$objFuenteFinDefecto = new ServicioCfg('spg_dt_fuentefinanciamiento');
						$objFuenteFinDefecto->setCodemp($datosempresa["codemp"]);
						$objFuenteFinDefecto->setFuenteDefecto($ArJson->datos[0], true);
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro3 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
							
					case 4:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[4]->incluirDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro4 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						//aqui se dispara el rellenar de las estructuras restantes...
						for($i=5;$i<=5;$i++)
						{
							if($ArJson->numest < $i-1)
							{
								$ArObjetos[$i]->setEstructura($i-1,'0000000000000000000000000','NINGUNO');
							}
							$ArObjetos[$i]->setEstructura($i,'0000000000000000000000000','NINGUNO');
							$auxdatos=$ArJson->datos[0];
							$auxdatos->codfuefin=$ArJson->datos[0]->codfuefin;
							$ArObjetos[$i]->incluirDto ( $auxdatos );
						}
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro4 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
						
					case 5:
						ServicioCfg::iniTransaccion ();
						$ArObjetos[5]->incluirDto ( $ArJson->datos[0] );
						$mensaje='Inserto la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro5 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=true;
						//incluir fuente financiamiento por defecto
						$objFuenteFinDefecto = new ServicioCfg('spg_dt_fuentefinanciamiento');
						$objFuenteFinDefecto->setCodemp($datosempresa["codemp"]);
						$objFuenteFinDefecto->setFuenteDefecto($ArJson->datos[0], false);
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						} 
						else
						{
							echo "|0";
							$mensaje='Error al insertar la Estructura Presupuestaria ' . $ArJson->datos[0]->codestpro4 . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
							$tipoevento=false;
						}
						$servicioEvento->evento="INSERTAR";
						$servicioEvento->codmenu=$ArJson->codmenu;
						$servicioEvento->codusu=$_SESSION["la_logusr"];
						$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$servicioEvento->codsis="CFG";
						$servicioEvento->nomfisico="sigesp_vis_cfg_spg_estructurapresupuestaria.php";
						$servicioEvento->desevetra=$mensaje;	
						$servicioEvento->tipoevento=$tipoevento;
						$servicioEvento->incluirEvento();
						break;
				}
				unset($ArObjetos);
				unset($oregevent);
				unset($oservicioemp);
				break;
		}
	}
}

function GenerarJsonDeObjetos($datetiqueta)
{
    global $json;
    $i = 0;
    foreach ($datetiqueta as $etiquetas)
	{
        foreach ($etiquetas as $etq=>$valor)
		{
            $arRegistros[$i]["nombre_pest"] = utf8_encode($valor);
            $i++;
        }
    }
    //aqui se pasa el arreglo de arreglos a un objeto json
    $TextJso = array("raiz"=>$arRegistros);
    $TextJson = $json->encode($TextJso);
    return $TextJson;
}
?>