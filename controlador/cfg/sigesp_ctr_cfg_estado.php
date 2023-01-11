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
		$oservicio = new ServicioCfg('sigesp_estados');
		$servicioEvento = new ServicioEvento();
		$Evento = $ArJson->oper;
			
		switch ($Evento)
		{    			
			case 'incluir':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$resultado=$oservicio->incluirDto ($ArJson);
				$mensaje="Inserto el Estado con codigo ".$ArJson->codest.", asociado al Pais ".$ArJson->codpai;
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al Insertar el Estado con codigo ".$ArJson->codest.", asociado al Pais ".$ArJson->codpai;
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_estado.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'catalogo':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$restriccion[0][0]= 'codpai';
				$restriccion[0][1]= '=';
				$restriccion[0][2]= $ArJson->codpai;
				$restriccion[0][3]= 2;
				$restriccion[1][0]= 'codest';
				$restriccion[1][1]= 'ORDER BY';
				$restriccion[1][2]= 'ASC';
				$restriccion[1][3]= 2;
				$datos = $oservicio->buscarCampoRestriccion($restriccion);
				$ObjSon = generarJson($datos);
				echo $ObjSon;
				break;					
			
			case 'eliminar':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oservicio->verificarUltimo('codest','sigesp_estados'," WHERE codpai='".$ArJson->codpai."'",$ArJson->codest);
				if ($ultimo)
				{
					ServicioCfg::iniTransaccion ();
					$respuesta = $oservicio->eliminarDto($ArJson, 'codest', $ArJson->codest);
					$mensaje="Elimino el Estado con codigo ".$ArJson->codest.", asociado al Pais ".$ArJson->codpai;
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
						$mensaje="Error al Eliminar el Estado con codigo ".$ArJson->codest.", asociado al Pais ".$ArJson->codpai;				
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje="Error al eliminar el estado con codigo ".$ArJson->codest.", asociado al Pais ".$ArJson->codpai;
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_estado.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'actualizar':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				$oservicio->modificarDto ($ArJson);
				$mensaje="Actualizo el Estado con codigo ".$ArJson->codest.", asociado al Pais ".$ArJson->codpai;	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al Actualizar el Estado con codigo ".$ArJson->codest.", asociado al Pais ".$ArJson->codpai;	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_estado.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
					
			case 'buscarcadena':
				$Datos = $rsEstado->LeerPorCadena($GLOBALS["criterio"],$GLOBALS["cadena"]);
				$ObjSon = generarJson($Datos);
				echo $ObjSon;
				break;
				
			case 'catalogocombopais':
				$oservicioPais = new ServicioCfg('sigesp_pais');
				$resultado = $oservicioPais->buscarTodos();
				$ObjSon = generarJson($resultado);
				echo $ObjSon;
				break;
				
			case 'nuevo' :
				$contador="";
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$contador = $oservicio->buscarCodigoEstado($ArJson->codpai);
				$ObjSon = $json->encode($contador);
				echo $ObjSon;
				unset($oservicio); 
				break;
				
			case 'verificarcodigo':
				$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
				$existe = $oservicio->verificarExistenciaRegistro($ArJson);
				$respuesta  = array('existe'=>$existe);
				$respuesta  = json_encode($respuesta);
				echo $respuesta;
				unset($oservicio);
				break;
		}
	}
}
?>