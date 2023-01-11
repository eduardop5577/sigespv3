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
    require_once ($dirsrv."/base/librerias/php/general/sigesp_lib_relaciones.php");
        
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon']) 		
	{
		$submit = str_replace ( "\\", "", $_POST ['ObjSon'] );
		$json = new Services_JSON();
		$ArJson = $json->decode($submit);
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioCfg('sigesp_moneda');
		$Evento = $ArJson->oper;
	
		switch ($Evento)
		{
			case 'incluir' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion();
				$mensaje="Inserta  Moneda,  codigo ".$ArJson->cabecera[0]->codmon;	
				$tipoevento=true;
				if ($oservicio->verificarMonedaPrincipal ($_SESSION["la_empresa"]["codemp"],$ArJson->cabecera[0]->codmon,$ArJson->cabecera[0]->estatuspri))
				{
					if($oservicio->incluirDto ($ArJson->cabecera[0]))
					{
						$total = count((array)$ArJson->detallesincluir);
						for($j=0; $j<$total; $j++)
						{
                            $detmoneda = new ServicioCfg('sigesp_dt_moneda');
							$detmoneda->setCodemp ($_SESSION["la_empresa"]["codemp"]);
							$ArJson->detallesincluir[$j]->fecha   = convertirFechaBd($ArJson->detallesincluir[$j]->fecha);
							$ArJson->detallesincluir[$j]->tascam1 = formatoNumericoBd($ArJson->detallesincluir[$j]->tascam1,1);
							$ArJson->detallesincluir[$j]->tascam2 = formatoNumericoBd($ArJson->detallesincluir[$j]->tascam2,1);
							if(!$detmoneda->incluirDto ($ArJson->detallesincluir[$j]))
							{
								$mensaje="Error al Insertar una nueva Moneda, codigo ".$ArJson->cabecera[0]->codmon;	
								$tipoevento=false;
								break;
							}
							unset($detmoneda);
						 }				
					}
					if (ServicioCfg::comTransaccion ())
					{
						echo "1";
					}
					else
					{
						echo "0";
						$mensaje="Error al Insertar una nueva Moneda, codigo ".$ArJson->cabecera[0]->codmon;	
						$tipoevento=false;
					}
				}
				else
				{
					echo "2";
					$mensaje="Solo debe existir una moneda principal";	
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_moneda.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento;
			break;
	
			case 'catalogo' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$cadenaSql = "SELECT sigesp_moneda.codmon, sigesp_moneda.denmon, sigesp_moneda.desmon, sigesp_moneda.abrmon,sigesp_moneda.estatuspri,  ".
							 "	     sigesp_moneda.codpai, sigesp_pais.despai AS denpai, sigesp_moneda.estmonpri ".
							 "	FROM sigesp_moneda, sigesp_pais ".
							 "	WHERE sigesp_moneda.codpai = sigesp_pais.codpai ".
							 "  ORDER BY codmon ";

				$datos = $oservicio->buscarSql($cadenaSql);
				$ObjSon = generarJson($datos);
				echo $ObjSon;
				break;
	
			case 'actualizar' :
				$resultado[0] ="";
				$resultado[1] ="";
				$resultado[2] ="";
				$totdetincluido  = 0;
				$totdeteliminado = 0;
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion();
				$mensaje="Actualiza  Moneda, con descripcion  codigo ".$ArJson->cabecera[0]->codmon;	
				$tipoevento=true;
				if ($oservicio->verificarMonedaPrincipal ($_SESSION["la_empresa"]["codemp"],$ArJson->cabecera[0]->codmon,$ArJson->cabecera[0]->estatuspri))
				{
					if ($oservicio->modificarDto ($ArJson->cabecera[0]))
					{
						if ($oservicio->eliminarDetallesMoneda($_SESSION["la_empresa"]["codemp"],$ArJson->cabecera[0]->codmon))
						{
							$totalincluir = count($ArJson->detallesincluir);
							for($j=0; $j<$totalincluir; $j++)
							{
									$detmoneda = new ServicioCfg('sigesp_dt_moneda');
									$detmoneda->setCodemp ($_SESSION["la_empresa"]["codemp"]);
									$ArJson->detallesincluir[$j]->fecha   = convertirFechaBd($ArJson->detallesincluir[$j]->fecha);
									$ArJson->detallesincluir[$j]->tascam1 = formatoNumericoBd($ArJson->detallesincluir[$j]->tascam1 ,1);
									$ArJson->detallesincluir[$j]->tascam2 = formatoNumericoBd($ArJson->detallesincluir[$j]->tascam2 ,1);
									if(!$detmoneda->incluirDto($ArJson->detallesincluir[$j]))
									{
											$mensaje="Error al Actualizar  Moneda, con descripcion  codigo ".$ArJson->cabecera[0]->codmon;	
											$tipoevento=false;
											break;
									}
									unset($detmoneda);
							}
						}
					}
					if (ServicioCfg::comTransaccion ())
					{
						echo "1";
					}
					else
					{
						echo "0";
						$mensaje="Error al Actualizar  Moneda, con descripcion  codigo ".$ArJson->cabecera[0]->codmon;	
						$tipoevento=false;
					}
				}
				else
				{
					echo "2";
					$mensaje="Solo debe existir una moneda principal";	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_moneda.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento;
			break;
	
			case 'eliminar' :
				$resultado[0] ="";
				$resultado[1] ="";
				$totdeteliminado = 0;
				ServicioCfg::iniTransaccion();
                                $relaciones = new servicioRelaciones();
                                $condicion="AND  column_name='codmon' AND TABLE_NAME<>'sigesp_dt_moneda'";
                                $tabla= 'sigesp_moneda';
                                $valor=$ArJson->cabecera[0]->codmon;
                                $mensaje='';
                                if(!$relaciones->verificarRelaciones($condicion,$tabla,$valor,$mensaje))
                                {
                                    if($ArJson->cabecera)
                                    {
                                            $arrtabignorar[0]='sigesp_dt_moneda';
                                            $mensaje="Eliminar Moneda, codigo ".$ArJson->cabecera[0]->codmon;	
                                            $tipoevento=true;
                                            $ultimo=$oservicio->verificarUltimo('codmon','sigesp_moneda'," WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."'",$ArJson->cabecera[0]->codmon);
                                            if ($ultimo)
                                            {
                                                    if(!$oservicio->validarEliminar('codmon', $ArJson->cabecera[0]->codmon,$arrtabignorar))
                                                    {
                                                            if($ArJson->detalleseliminar)
                                                            {
                                                                    $total = count((array)$ArJson->detalleseliminar);
                                                                    for($j=0; $j<$total; $j++)
                                                                    {
                                                                        $detmoneda = new ServicioCfg('sigesp_dt_moneda');
                                                                        $detmoneda->setCodemp($_SESSION["la_empresa"]["codemp"]);
                                                                        $ArJson->detalleseliminar[$j]->fecha   = convertirFechaBd($ArJson->detalleseliminar[$j]->fecha);
                                                                        $ArJson->detalleseliminar[$j]->tascam1 = formatoNumericoBd($ArJson->detalleseliminar[$j]->tascam1,1);
                                                                        $ArJson->detalleseliminar[$j]->tascam2 = formatoNumericoBd($ArJson->detalleseliminar[$j]->tascam2,1);
                                                                        if(!$detmoneda->eliminarDto ($ArJson->detalleseliminar[$j]))
                                                                        {
                                                                                $resultado[0] ='Error al eliminar Moneda, codigo '.$ArJson->cabecera[0]->codmon;	
                                                                                $tipoevento=false;
                                                                                break;
                                                                        }
                                                                    }
                                                                    $oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
                                                                    $oservicio->eliminarDto($ArJson->cabecera[0]);
                                                                    if (ServicioCfg::comTransaccion ())
                                                                    {
                                                                            $resultado[0] = 'Moneda eliminada exitosamente';
                                                                            $resultado[1] = $totdeteliminado.' detalle(s) de la moneda eliminado(s) exitosamente';
                                                                    }
                                                                    else
                                                                    {
                                                                            $resultado[0] = 'Falló eliminación de Moneda';
                                                                            $mensaje='Error al eliminar Moneda, codigo '.$ArJson->cabecera[0]->codmon;	
                                                                            $tipoevento=false;
                                                                    }
                                                            }
                                                            else
                                                            {
                                                                    $resultado[1] = 'Error en eliminación de detalle(s) de la moneda';
                                                                    $mensaje='Error al eliminar Moneda, codigo '.$ArJson->cabecera[0]->codmon;	
                                                                    $tipoevento=false;
                                                            }
                                                    }
                                                    else
                                                    {
                                                            $resultado[0] = 'El registro no puede ser eliminado ya que posee relaciones en otras tablas';
                                                            $mensaje='El registro no puede ser eliminado ya que posee relaciones en otras tablas codigo '.$ArJson->cabecera[0]->codmon;	
                                                            $tipoevento=false;
                                                    }
                                            }
                                            else
                                            {
                                                    $resultado[0] = 'El registro no puede ser eliminado, no puede eliminar registros intermedios';
                                                    $mensaje='Error al eliminar Moneda, codigo '.$ArJson->cabecera[0]->codmon;	
                                                    $tipoevento=false;
                                            }
                                            $servicioEvento->evento="ELIMINAR";
                                            $servicioEvento->codmenu=$ArJson->codmenu;
                                            $servicioEvento->codusu=$_SESSION["la_logusr"];
                                            $servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
                                            $servicioEvento->codsis="CFG";
                                            $servicioEvento->nomfisico="sigesp_vis_cfg_moneda.php";
                                            $servicioEvento->desevetra=$mensaje;	
                                            $servicioEvento->tipoevento=$tipoevento;
                                            $servicioEvento->incluirEvento;
                                    }
                                }
                                else
                                {
                                    $resultado[0] = 'La Moneda esta asociada a algun movimiento, no puede ser Eliminada';
                                }
				echo $jsonClave = $json->encode(array ("resultado" =>$resultado));
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
				$resultado = $oservicio->buscarCodigoMoneda();
				$ObjSon = $json->encode($resultado);
				echo $ObjSon;
				break;
	
			case 'detalles' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$cadenaSql="SELECT codemp,codmon,fecha, REPLACE(CAST(tascam1 AS varchar(25)),'.',',') AS tascam1, REPLACE(CAST(tascam2 AS varchar(25)),'.',',') AS tascam2 ".
                                           "  FROM sigesp_dt_moneda ".
                                           " WHERE sigesp_dt_moneda.codmon  = '".$ArJson->codmon."' order by fecha ";
				$resultado = $oservicio->buscarSql($cadenaSql);
				$ObjSon = generarJson($resultado);
				echo $ObjSon;
				break;
		}
		unset($oservicio);
	}
}
?>