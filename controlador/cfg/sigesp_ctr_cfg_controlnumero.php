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
//	require_once($dirsrv.'/base/librerias/php/general/Json.php');
	require_once($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
	require_once('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon']) 		
	{
		$submit = str_replace ( "\\", "", $_POST ['ObjSon'] );
		$objdata = str_replace("\\","",$_POST['objdata']);	
		$ArJson = json_decode($submit,false);		
		$evento = $ArJson->oper;


		//$json = new Services_JSON();
		//$ArJson = $json->decode($submit);
		$servicioEvento = new ServicioEvento();
                $oservicio      = new ServicioCfg('sigesp_prefijos');
        	$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
     
		switch ($evento)
		{
			case 'nuevo' :
				$contador="";
				$resultado = $oservicio->buscarCodigoControlNumero();
				echo json_encode($resultado);
			break;
			
			case 'catalogo' :
				$cadenaSql = "SELECT sigesp_prefijos.codemp, sigesp_prefijos.codsis, sigesp_prefijos.procede, ".
					     "       sigesp_prefijos.id, sigesp_prefijos.prefijo, sigesp_prefijos.nro_inicial,". 
					     "       sigesp_prefijos.nro_final, sigesp_prefijos.maxlen, sigesp_prefijos.nro_actual,". 
					     "       sigesp_prefijos.estact, sigesp_prefijos.estcompscg ".
					     "  FROM sigesp_prefijos ".
					     " WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."'".
                                             " ORDER BY id ";
				$dataControlNum = $oservicio->buscarSql($cadenaSql);
				echo generarJson($dataControlNum);
				unset($dataControlNum);
			break;
				
			case 'incluir' :
				ServicioCfg::iniTransaccion();
				$total = count((array)$ArJson->usuariosincluir);
				$mensaje="Inserto en CFG un nuevo control numero  ".$ArJson->cabecera[0]->id." para el sistema ".$ArJson->cabecera[0]->codsis.", procedencia ".$ArJson->cabecera[0]->procede.", prefijo ".$ArJson->cabecera[0]->prefijo." y usuarios ";	
				$tipoevento=true;
				$mensajeusuario="";
                                $ArJson->cabecera[0]->codemp = $_SESSION["la_empresa"]["codemp"];
                                $ArJson->cabecera[0]->codsis  = $oservicio->obtenerSistemaProcecencia($ArJson->cabecera[0]->procede);
                                if($ArJson->cabecera[0]->estcompscg)
                                {
                                        $ArJson->cabecera[0]->estcompscg = '1';
                                }
                                else
                                {
                                        $ArJson->cabecera[0]->estcompscg = '0';
                                }
                                if($ArJson->cabecera[0]->estact=='')
                                {
                                        $ArJson->cabecera[0]->estact=0;
                                }
				if($oservicio->incluirDto ($ArJson->cabecera[0]))
				{                                
                                    for($j=0; $j<$total; $j++)
                                    {
                                            $detprefijos    = new ServicioCfg('sigesp_dt_prefijos');
                                            $detprefijos->setCodemp ($_SESSION["la_empresa"]["codemp"]);
                                            $ArJson->usuariosincluir[$j]->codemp = $ArJson->cabecera[0]->codemp;
                                            $ArJson->usuariosincluir[$j]->codsis = $ArJson->cabecera[0]->codsis;
                                            if($detprefijos->incluirDto ($ArJson->usuariosincluir[$j]))
                                            {
                                                    $mensajeusuario.=" ".$ArJson->usuariosincluir[$j]->codusu;	
                                            }
                                            else
                                            {
                                                    if ($detprefijos->getDaogenerico()->errorDuplicate)
                                                    {
                                                            echo '-2';
                                                            $mensaje="Error al insertar en CFG un nuevo control numero  ".$ArJson->usuariosincluir[$j]->id."  para el sistema ".$ArJson->usuariosincluir[$j]->codsis.", procedencia ".$ArJson->usuariosincluir[$j]->procede.", prefijo ".$ArJson->usuariosincluir[$j]->prefijo.", usuario ".$ArJson->usuariosincluir[$j]->codusu;	
                                                            $tipoevento=false;
                                                    }
                                                    break;
                                            }
                                            unset($detprefijos);
                                    }
                                }
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
					$mensaje=$mensaje.$mensajeusuario;	
				}
				else
				{
					echo "|0";
					$mensaje="Error al insertar en CFG un nuevo control numero  ".$ArJson->cabecera[0]->id."  para el sistema ".$ArJson->cabecera[0]->codsis.", procedencia ".$ArJson->cabecera[0]->procede;	
					$tipoevento=false;
				}			
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_controlnumero.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();			
			break;
			
			case 'actualizar' :
				ServicioCfg::iniTransaccion();
				$mensaje="Actualizo en CFG un nuevo control numero  ".$ArJson->cabecera[0]->id." para el sistema ".$ArJson->cabecera[0]->codsis.", procedencia ".$ArJson->cabecera[0]->procede.", prefijo ".$ArJson->cabecera[0]->prefijo." y usuarios ";	
				$tipoevento=true;
				$mensajeusuario="";
				$usuariosinvalidos=" ";
				$ArJson->cabecera[0]->codemp = $_SESSION["la_empresa"]["codemp"];
				$ArJson->cabecera[0]->codsis  = $oservicio->obtenerSistemaProcecencia($ArJson->cabecera[0]->procede);
				if($ArJson->cabecera[0]->estcompscg)
				{
						$ArJson->cabecera[0]->estcompscg = '1';
				}
				else
				{
						$ArJson->cabecera[0]->estcompscg = '0';
				}
				if($ArJson->cabecera[0]->estact=='')
				{
						$ArJson->cabecera[0]->estact=0;
				}
				if ($oservicio->modificarDto ($ArJson->cabecera[0]))
				{
					$totalincluir = count((array)$ArJson->usuariosincluir);
					for($j=0; $j<$totalincluir; $j++)
					{
                                            $detprefijos    = new ServicioCfg('sigesp_dt_prefijos');
                                            $detprefijos->setCodemp ($_SESSION["la_empresa"]["codemp"]);
                                            $ArJson->usuariosincluir[$j]->codemp = $ArJson->cabecera[0]->codemp;
                                            $ArJson->usuariosincluir[$j]->codsis = $ArJson->cabecera[0]->codsis;
                                            $cadena  = " codemp = '".$ArJson->usuariosincluir[$j]->codemp."'";
                                            $cadena .= " AND id = '".$ArJson->usuariosincluir[$j]->id."'";
                                            $cadena .= " AND codsis = '".$ArJson->usuariosincluir[$j]->codsis."'";
                                            $cadena .= " AND procede = '".$ArJson->usuariosincluir[$j]->procede."'";
                                            $cadena .= " AND prefijo = '".$ArJson->usuariosincluir[$j]->prefijo."'";
                                            $cadena .= " AND codusu = '".utf8_decode($ArJson->usuariosincluir[$j]->codusu)."'";
                                            $objdetalle = $detprefijos->getDto($cadena);
                                            if ($objdetalle->codemp =='')
                                            {
                                                if($detprefijos->incluirDto ($ArJson->usuariosincluir[$j]))
                                                {
                                                        $mensajeusuario.=" ".$ArJson->usuariosincluir[$j]->codusu;	
                                                }
                                                else
                                                {
                                                        if ($detprefijos->getDaogenerico()->errorDuplicate)
                                                        {
                                                                echo '-2';
                                                                $mensaje="Error al insertar en CFG un nuevo control numero  ".$ArJson->usuariosincluir[$j]->id."  para el sistema ".$ArJson->usuariosincluir[$j]->codsis.", procedencia ".$ArJson->usuariosincluir[$j]->procede.", prefijo ".$ArJson->usuariosincluir[$j]->prefijo.", usuario ".$ArJson->usuariosincluir[$j]->codusu;	
                                                                $usuariosinvalidos .= " ".$ArJson->usuariosincluir[$j]->codusu;
                                                                $tipoevento=false;
                                                        }
                                                        break;
                                                }
                                            }
                                            else
                                            {
                                                //$mensajeusuario.=" ".$ArJson->usuariosincluir[$j]->codusu;
                                            }
                                            unset($detprefijos);
					}
					$totalincluir = count((array)$ArJson->usuariosincluir1);
					for($j=0; $j<$totalincluir; $j++)
					{
                                            $detprefijos    = new ServicioCfg('sigesp_dt_prefijos');
                                            $detprefijos->setCodemp ($_SESSION["la_empresa"]["codemp"]);
                                            $ArJson->usuariosincluir1[$j]->codemp = $ArJson->cabecera[0]->codemp;
                                            $ArJson->usuariosincluir1[$j]->codsis = $ArJson->cabecera[0]->codsis;
                                            $cadena  = " codemp = '".$ArJson->usuariosincluir1[$j]->codemp."'";
                                            $cadena .= " AND id = '".$ArJson->usuariosincluir1[$j]->id."'";
                                            $cadena .= " AND codsis = '".$ArJson->usuariosincluir1[$j]->codsis."'";
                                            $cadena .= " AND procede = '".$ArJson->usuariosincluir1[$j]->procede."'";
                                            $cadena .= " AND prefijo = '".$ArJson->usuariosincluir1[$j]->prefijo."'";
                                            $cadena .= " AND codusu = '".utf8_decode($ArJson->usuariosincluir1[$j]->codusu)."'";
                                            $objdetalle = $detprefijos->getDto($cadena);
                                            if ($objdetalle->codemp =='')
                                            {
                                                if($detprefijos->incluirDto ($ArJson->usuariosincluir1[$j]))
                                                {
                                                        $mensajeusuario.=" ".$ArJson->usuariosincluir1[$j]->codusu;	
                                                }
                                                else
                                                {
                                                        if ($detprefijos->getDaogenerico()->errorDuplicate)
                                                        {
                                                                echo '-2';
                                                                $mensaje="Error al insertar en CFG un nuevo control numero  ".$ArJson->usuariosincluir[$j]->id."  para el sistema ".$ArJson->usuariosincluir[$j]->codsis.", procedencia ".$ArJson->usuariosincluir[$j]->procede.", prefijo ".$ArJson->usuariosincluir[$j]->prefijo.", usuario ".$ArJson->usuariosincluir[$j]->codusu;	
                                                                $usuariosinvalidos .= " ".$ArJson->usuariosincluir1[$j]->codusu;
                                                                $tipoevento=false;
                                                        }
                                                        break;
                                                }
                                            }
                                            else
                                            {
                                                //$mensajeusuario.=" ".$ArJson->usuariosincluir[$j]->codusu;
                                            }
                                            unset($detprefijos);
					}
					
					$totaleliminar = count((array)$ArJson->usuarioseliminar);
					for($j=0; $j<$totaleliminar; $j++)
					{
                                                $detprefijos    = new ServicioCfg('sigesp_dt_prefijos');
                                                $detprefijos->setCodemp ($_SESSION["la_empresa"]["codemp"]);
                                                $ArJson->usuarioseliminar[$j]->codemp = $ArJson->cabecera[0]->codemp;
                                                $ArJson->usuarioseliminar[$j]->codsis = $ArJson->cabecera[0]->codsis;
						if($detprefijos->eliminarDto ($ArJson->usuarioseliminar[$j])<>'')
						{
							$mensaje="Error al Eliminar el usuario codigo ".$ArJson->usuarioseliminar[$j]->codusu;	
							$tipoevento=false;
                                                        $usuariosinvalidos .= " ".$ArJson->usuarioseliminar[$j]->codusu;
							break;
						}
						unset($detprefijos);
					}

					$totaleliminar = count((array)$ArJson->usuarioseliminar1);
					for($j=0; $j<$totaleliminar; $j++)
					{
                                                $detprefijos    = new ServicioCfg('sigesp_dt_prefijos');
                                                $detprefijos->setCodemp ($_SESSION["la_empresa"]["codemp"]);
                                                $ArJson->usuarioseliminar1[$j]->codemp = $ArJson->cabecera[0]->codemp;
                                                $ArJson->usuarioseliminar1[$j]->codsis = $ArJson->cabecera[0]->codsis;
						if($detprefijos->eliminarDto ($ArJson->usuarioseliminar1[$j])<>'')
						{
							$mensaje="Error al Eliminar el usuario codigo ".$ArJson->usuarioseliminar1[$j]->codusu;	
							$tipoevento=false;
                                                        $usuariosinvalidos .= " ".$ArJson->usuarioseliminar1[$j]->codusu;
							break;
						}
						unset($detprefijos);
					}
				}
                                if (ServicioCfg::comTransaccion ())
                                {
                                    echo "|1|";
                                }
                                else
                                {
                                    echo "|0|";
                                }
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_controlnumero.php";
				$servicioEvento->desevetra=$mensaje.$mensajeusuario;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'eliminar' :
                                $mensaje="Eliminar en CFG un nuevo control numero  ".$ArJson->cabecera[0]->id." para el sistema ".$ArJson->cabecera[0]->codsis.", procedencia ".$ArJson->cabecera[0]->procede.", prefijo ".$ArJson->cabecera[0]->prefijo." y usuarios ";
                                $tipoevento=true;
                                $mensajeusuario=" ";
                                $usuariosinvalidos=" ";
                                $ArJson->cabecera[0]->codemp = $_SESSION["la_empresa"]["codemp"];
                                $ArJson->cabecera[0]->codsis  = $oservicio->obtenerSistemaProcecencia($ArJson->cabecera[0]->procede);
                                ServicioCfg::iniTransaccion();
                                $totaleliminar = count((array)$ArJson->usuarioseliminar);
                                for($j=0; $j<$totaleliminar; $j++)
                                {
                                        $detprefijos    = new ServicioCfg('sigesp_dt_prefijos');
                                        $detprefijos->setCodemp ($_SESSION["la_empresa"]["codemp"]);
                                        $ArJson->usuarioseliminar[$j]->codemp = $ArJson->cabecera[0]->codemp;
                                        $ArJson->usuarioseliminar[$j]->codsis = $ArJson->cabecera[0]->codsis;
                                        if($detprefijos->eliminarDto ($ArJson->usuarioseliminar[$j])<>'')
                                        {
                                                $mensaje="Error al Eliminar el usuario codigo ".$ArJson->usuarioseliminar[$j]->codusu;	
                                                $tipoevento=false;
                                                $usuariosinvalidos .= " ".$ArJson->usuarioseliminar[$j]->codusu;
                                                break;
                                        }
                                        unset($detprefijos);
                                }
				$oservicio->eliminarDto($ArJson->cabecera[0]);
                                if (ServicioCfg::comTransaccion ())
                                {
                                    echo "|1|";
                                }
                                else
                                {
                                    echo "|0|";
                                }
                                $servicioEvento->evento="ELIMINAR";
                                $servicioEvento->codmenu=$ArJson->codmenu;
                                $servicioEvento->codusu=$_SESSION["la_logusr"];
                                $servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
                                $servicioEvento->codsis="CFG";
                                $servicioEvento->nomfisico="sigesp_vis_cfg_controlnumero.php";
                                $servicioEvento->desevetra=$mensaje;	
                                $servicioEvento->tipoevento=$tipoevento;
                                $servicioEvento->incluirEvento();
			break;
				
			case 'claveprimaria' :
				$arregloClave = $oservicio->obtenerPrimaryKey();
				echo json_encode($arregloClave);
			break;
			
			case 'usuarios' :
				$cadenaSql="SELECT sigesp_dt_prefijos.codusu, sss_usuarios.nomusu, sss_usuarios.apeusu ".
				           "  FROM sigesp_dt_prefijos, sss_usuarios ".
				           " WHERE sigesp_dt_prefijos.codemp  = '".$_SESSION["la_empresa"]["codemp"]."' ".
					   "   AND sigesp_dt_prefijos.id      = '".$ArJson->id."' ".
					   "   AND sigesp_dt_prefijos.codsis  = '".$ArJson->codsis."'  ".
					   "   AND sigesp_dt_prefijos.procede = '".$ArJson->procede."'  ".
					   "   AND sigesp_dt_prefijos.prefijo = '".$ArJson->prefijo."' ".
					   "   AND sigesp_dt_prefijos.codemp  = sss_usuarios.codemp ".
				           "   AND sigesp_dt_prefijos.codusu  = sss_usuarios.codusu ";
				$dataUsuario = $oservicio->buscarSql($cadenaSql);
				echo generarJson($dataUsuario);
				unset($dataUsuario); 
			break;
				
			case 'verificarprefijo' :
				$existe=false;
				$codsis="";
				$cadenaSql="";
				$codsis = $oservicio->obtenerSistemaProcecencia($ArJson->procede);
				if ($resultado->fields ['existe']>0) 
				{
					$existe = true;
				} 
				$cadenaSql= "SELECT count(prefijo) as cantidad ".
                                            " FROM sigesp_prefijos ".
                                            " WHERE sigesp_prefijos.codemp  = '".$_SESSION["la_empresa"]["codemp"]."' ".
                                            "   AND sigesp_prefijos.procede = '".$ArJson->procede."' ".
                                            "   AND sigesp_prefijos.codsis  = '".$codsis."' ".
                                            "   AND sigesp_prefijos.prefijo = '".$ArJson->prefijo."'";
				$resultado = $oservicio->buscarSql($cadenaSql);
				if ($resultado->fields ['cantidad'] > 0) 
				{
					$existe=true;
				} 
				$respuesta  = array('existe'=>$existe);
				$respuesta  = json_encode($respuesta);
				echo $respuesta;
				unset($resultado);
			break;
		
			case 'obtenerPrefijo' :
				$cadenaSql="SELECT prefijo ". 
                                           "  FROM sigesp_prefijos ". 
                                           " WHERE codemp  = '".$_SESSION["la_empresa"]["codemp"]."'  ".
					   "   AND codsis  = '".$ArJson->codsis."' ".
					   " GROUP BY prefijo ";
				$dataUsuario = $oservicio->buscarSql($cadenaSql);
				echo generarJson($dataUsuario);
				unset($dataUsuario); 
			break;
		}
		unset($servicioEvento);
                unset($oservicio);	
	}
}
?>