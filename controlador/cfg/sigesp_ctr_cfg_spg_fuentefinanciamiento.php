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
		$oservicio = new ServicioCfg ( 'sigesp_fuentefinanciamiento' );
		$oservicio->setCodemp ( $datosempresa["codemp"] );
		$servicioEvento = new ServicioEvento();
			
		switch ($ArJson->oper)
		{
			case 'incluir' :
				ServicioCfg::iniTransaccion ();
				$resultado=$oservicio->incluirDto ($ArJson,true,"codfuefin",true);
				$arrcadres = explode(",",$resultado);
				if($arrcadres[0]==1||$arrcadres[0]==-1)
				{
					$mensaje='Inserto la Fuente de Financiamiento '. $ArJson->codfuefin . ' Asociada a la empresa '.$datosempresa['codemp'];	
					$tipoevento=true;
					if (ServicioCfg::comTransaccion ())
					{
						if($arrcadres[0]==1)
						{
							echo "|1";
						}
						else
						{
							echo "|".$arrcadres[0]."|".$arrcadres[1];
							$mensaje='Error al insertar la Fuente de Financiamiento '. $ArJson->codfuefin . ' Asociada a la empresa '.$datosempresa['codemp'];	
							$tipoevento=false;
						}
					} 
					else
					{
						echo "|0";
						$mensaje='Error al insertar la Fuente de Financiamiento '. $ArJson->codfuefin . ' Asociada a la empresa '.$datosempresa['codemp'];	
						$tipoevento=false;
					}
					$servicioEvento->evento="INSERTAR";
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_spg_fuentefinanciamiento.php";
					$servicioEvento->desevetra=$mensaje;	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
				}
				break;
			
			case 'buscarcodigo' :
				$cad =  $oservicio->buscarCodigoFuentefinanciamiento();
				echo "|{$cad}";
				break;
			
			case 'catalogo' :
				$dataFuente = $oservicio->buscarTodos ("codfuefin");
				echo generarJson ( $dataFuente );
				unset($dataFuente);
				break;
			
			case 'actualizar' :
				ServicioCfg::iniTransaccion ();
				$oservicio->modificarDto ( $ArJson );
				$mensaje='Actualizo la Fuente de Financiamiento '. $ArJson->codfuefin . ' Asociada a la empresa '.$datosempresa['codemp'];	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje='Error al actualizar la Fuente de Financiamiento '. $ArJson->codfuefin . ' Asociada a la empresa '.$datosempresa['codemp'];	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_spg_fuentefinanciamiento.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'eliminar' :
				ServicioCfg::iniTransaccion ();
				$ultimo=$oservicio->verificarUltimo('codfuefin','sigesp_fuentefinanciamiento'," WHERE codemp='".$datosempresa['codemp']."' ",$ArJson->codfuefin);
				if ($ultimo)
				{
					$respuesta = $oservicio->eliminarDto ( $ArJson, 'codfuefin', $ArJson->codfuefin);
					$mensaje='Elimino la Fuente de Financiamiento '. $ArJson->codfuefin . ' Asociada a la empresa '.$datosempresa['codemp'];	
					$tipoevento=true;
					if (ServicioCfg::comTransaccion () && $respuesta=='')
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
						$mensaje='Error al eliminar la Fuente de Financiamiento '. $ArJson->codfuefin . ' Asociada a la empresa '.$datosempresa['codemp'];	
						$tipoevento=false;
					}
				}
				else
				{
					echo '|-8';
					$mensaje='Error al eliminar la Fuente de Financiamiento '. $ArJson->codfuefin . ' Asociada a la empresa '.$datosempresa['codemp'];	
					$tipoevento=false;
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_spg_fuentefinanciamiento.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'catalogofuefin' :
				$cadenasql = "SELECT spg_dt_fuentefinanciamiento.codfuefin,sigesp_fuentefinanciamiento.denfuefin ".
							 "  FROM sigesp_fuentefinanciamiento,spg_dt_fuentefinanciamiento  ".
							 " WHERE sigesp_fuentefinanciamiento.codemp = '".$datosempresa["codemp"]."'  ".
							 "   AND sigesp_fuentefinanciamiento.codemp = spg_dt_fuentefinanciamiento.codemp ".
							 "   AND sigesp_fuentefinanciamiento.codfuefin = spg_dt_fuentefinanciamiento.codfuefin ".
							 "   AND spg_dt_fuentefinanciamiento.codestpro1 = '".$ArJson->codestpro1."' ".
							 "   AND spg_dt_fuentefinanciamiento.codestpro2 = '".$ArJson->codestpro2."' ". 
							 "   AND spg_dt_fuentefinanciamiento.codestpro3 = '".$ArJson->codestpro3."' ". 
							 "   AND spg_dt_fuentefinanciamiento.codestpro4 = '".$ArJson->codestpro4."' ". 
							 "   AND spg_dt_fuentefinanciamiento.codestpro5 = '".$ArJson->codestpro5."' ". 
							 "   AND spg_dt_fuentefinanciamiento.estcla = '".$ArJson->estcla."' ".
							 " ORDER BY sigesp_fuentefinanciamiento.codfuefin";		
				$dataCatFuente = $oservicio->buscarSql($cadenasql);
				echo generarJson ( $dataCatFuente );
				unset($dataCatFuente);
				break;
		
		}
		unset($oservicio);
		unset($oregevent);
	}
}
?>