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
		$ArObjetos = array();
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioCfg('scv_ciudades');
			
		switch ($ArJson->oper)
		{    			
			case 'incluir':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$oservicio->incluirDto ($ArJson);
				$mensaje="Inserto la Ciudad con codigo ".$ArJson->codciu.", asociado al Estado ".$ArJson->codest." del Pais ".$ArJson->codpai;	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al Insertar la Ciudad con codigo ".$ArJson->codciu.", asociado al Estado ".$ArJson->codest." del Pais ".$ArJson->codpai;	
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_ciudad.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'catalogo':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$restriccion[0][0]= 'codpai';
				$restriccion[0][1]= '=';
				$restriccion[0][2]= $ArJson->codpai;
				$restriccion[0][3]= 0;
				$restriccion[1][0]= 'codest';
				$restriccion[1][1]= '=';
				$restriccion[1][2]= $ArJson->codest;
				$restriccion[1][3]= 2;
				$dataCiudad = $oservicio->buscarCampoRestriccion($restriccion);
				echo generarJson($dataCiudad);
				unset($dataCiudad);
				break;					
			
			case 'actualizar':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$oservicio->modificarDto ( $ArJson );
				$mensaje="Actualizo la Ciudad con codigo ".$ArJson->codciu.", asociado al Estado ".$ArJson->codest." del Pais ".$ArJson->codpai;	
				$tipoevento=true;
				if(ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al actualizar la Ciudad con codigo ".$ArJson->codciu.", asociado al Estado ".$ArJson->codest." del Pais ".$ArJson->codpai;	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_ciudad.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
		
			case 'eliminar':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oservicio->verificarUltimo('codciu','scv_ciudades'," WHERE codpai='".$ArJson->codpai."' AND codest='".$ArJson->codest."'",$ArJson->codciu);
				if ($ultimo)
				{
					ServicioCfg::iniTransaccion ();
					$oservicio->eliminarDto ( $ArJson );
					$mensaje="Elimino la Ciudad con codigo ".$ArJson->codciu.", asociado al Estado ".$ArJson->codest." del Pais ".$ArJson->codpai;	
					$tipoevento=true;
					if(ServicioCfg::comTransaccion ())
					{
						echo "|1";
					}
					else
					{
						if($respuesta='-1')
						{
							echo '|-9';
						}
						else
						{
							echo "|0";
						}
						$mensaje="Error al Eliminar la Ciudad con codigo ".$ArJson->codciu.", asociado al Estado ".$ArJson->codest." del Pais ".$ArJson->codpai;	
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje="Error al Eliminar la Ciudad con codigo ".$ArJson->codciu.", asociado al Estado ".$ArJson->codest." del Pais ".$ArJson->codpai;	
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_ciudad.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;	
			
			case 'catalogocombopais':
				$oservicioPais =  new ServicioCfg('sigesp_pais');
				$oservicioPais->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$dataPais = $oservicioPais->buscarTodos();
				echo generarJson($dataPais);
				unset($oservicioPais);
				unset($dataPais);
				break;
				
			case 'catalogocomboestado':
				$oservicioEstado =  new ServicioCfg('sigesp_estados');
				$oservicioEstado->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$restriccion[0][0]= 'codpai';
				$restriccion[0][1]= '=';
				$restriccion[0][2]= $ArJson->codpai;
				$restriccion[0][3]= 2;
				$dataEstado = $oservicioEstado->buscarCampoRestriccion($restriccion);
				echo generarJson($dataEstado);
				unset($oservicioEstado);
				unset($dataEstado);	
			break;
			
			case 'nuevo' :
				$contador="";
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$contador = $oservicio->buscarCodigoCiudad($ArJson->codpai,$ArJson->codest);
				echo $json->encode($contador);
				break;
				
			case 'verificarcodigo':
				$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
				$existe = $oservicio->verificarExistenciaRegistro($ArJson);
				$respuesta  = array('existe'=>$existe);
				echo json_encode($respuesta);
				break;
		}
		unset($oservicio);
	}
}
?>