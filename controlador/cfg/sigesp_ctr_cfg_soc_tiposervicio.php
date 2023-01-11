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
		$oservicio = new ServicioCfg('soc_tiposervicio');
		$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
		$Evento = $ArJson->oper;
		switch ($Evento)
		{
			case 'nuevo' :
				$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
				$contador  = $oservicio->buscarCodigoTipoServicio();
				$ObjSon    = $json->encode($contador);
				echo $ObjSon; 
				break;
			
			case 'incluir':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion();
				$oservicio->incluirDto($ArJson);
				$mensaje="Inserto en CFG un nuevo tipo de servicio";	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ()) 
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al insertar en CFG un nuevo tipo de servicio";	
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_soc_tiposervicio.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'catalogo':
				$cadenasql="SELECT codtipser, dentipser, codmil, ".
						   "   		(SELECT denmil FROM sigesp_catalogo_milco  ".
						   "          WHERE soc_tiposervicio.codmil=sigesp_catalogo_milco.codmil) as denmil ".
						   "  FROM soc_tiposervicio ".
						   " ORDER BY codtipser ASC";
				$Datos = $oservicio->buscarSql($cadenasql);					
				$ObjSon = generarJson($Datos);
				echo $ObjSon;	
				break;
				
			case 'actualizar':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion();
				$oservicio->modificarDto($ArJson);
				$mensaje="Actualizo en CFG un nuevo tipo de servicio";	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje="Error al actualizar en CFG un nuevo tipo de servicio";	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_soc_tiposervicio.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;

			case 'eliminar':
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oservicio->verificarUltimo('codtipser','soc_tiposervicio'," WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."' ",$ArJson->codtipser);
				if ($ultimo)
				{
					$tiene  =$oservicio->validarEliminar('codtipser', $ArJson->codtipser);
					$mensaje="Eliminó en CFG un nuevo tipo de servicio";	
					$tipoevento=true;
					if(!$tiene)
					{ 	
						ServicioCfg::iniTransaccion ();
						$oservicio->eliminarDto ( $ArJson );
						if (ServicioCfg::comTransaccion ())
						{
							echo "|1";
						}
						else
						{
							echo "|0";
							$mensaje="Error al eliminar en CFG un nuevo tipo de servicio ".$ArJson->codtipser;	
							$tipoevento=false;
						}
					}
					else
					{
						echo '|-9';
						$mensaje="Error al eliminar en CFG un nuevo tipo de servicio ".$ArJson->codtipser.". Esta relacionado con otras tablas";	
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje="Error al eliminar en CFG un nuevo tipo de servicio".$ArJson->codtipser;
					$tipoevento=false;				
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_soc_tiposervicio.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();				
				break;	
		}
	}
}
?>