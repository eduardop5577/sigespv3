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
	$datosempresa=$_SESSION["la_empresa"];
	if ($_POST['ObjSon']) 		
	{
		$submit = str_replace("\\", "", $_POST['ObjSon']);
		$json = new Services_JSON;
		$arrjson = $json->decode($submit);
		
		
		switch ($arrjson->operacion)
		{
			case 'buscarcodigo':
				$serviciocfg = new ServicioCfg();
				echo $serviciocfg->buscarCodigoModclausula($datosempresa["codemp"]);
				break;
			
			case 'catalogo_clausuala':
				$serviciocfg = new ServicioCfg();
				$resultado   = $serviciocfg->buscarClausulas($datosempresa["codemp"]);
				$ObjSon      = generarJson($resultado);
				echo $ObjSon;
				unset($serviciocfg);
				break;
				
			case 'incluir':
				$serviciocfg = new ServicioCfg();
				$resultado   = $serviciocfg->guardarModclausula($arrjson,$datosempresa["codemp"]);
				foreach ($resultado as $valresul => $valor)
				{
					echo"|".$valor;
				}
				unset($serviciocfg);
				break;
				
			case 'eliminar':
				$oservicio = new ServicioCfg('soc_modalidadclausulas');
				$ultimo=$oservicio->verificarUltimo('codtipmod','soc_modalidadclausulas'," WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'",$arrjson->datoscabecera[0]->codtipmod);
				if ($ultimo)
				{
					$serviciocfg = new ServicioCfg();
					$respuesta=$serviciocfg->eliminarModclausula($arrjson, $datosempresa["codemp"]);
					unset($serviciocfg);
				}
				else
				{
					echo "|-8";
					$mensaje='Error al eliminar la Clausula(SOC) '. $arrjson->datoscabecera[0]->codtipmod . ' Asociada a la empresa '.$datosempresa["codemp"];	
					$tipoevento=false;				
				}				
				unset($oservicio);
				echo "|".$respuesta;	
				break;
				
			case 'catalogo':
				$serviciocfg = new ServicioCfg();
				$datos       = $serviciocfg->buscarModclausula($datosempresa["codemp"]);
				$ObjSon      = generarJson($datos);
				echo $ObjSon;
				break;
				
			case 'buscardetalle':
				$serviciocfg = new ServicioCfg();
				$datos       = $serviciocfg->buscarDetModclausula($datosempresa["codemp"], $arrjson->codtipmod);
				$ObjSon      = generarJson($datos);
				echo $ObjSon;
				break;
		}
	}
}
?>