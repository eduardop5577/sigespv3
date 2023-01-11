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
	require_once ($dirsrv.'/base/librerias/php/general/Json.php');
	require_once ($dirsrv.'/modelo/servicio/cfg/sigesp_srv_cfg_spg_estructurafuente.php');
	$datosempresa = $_SESSION['la_empresa'];
	$_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    
    switch ($objetoJson->operacion)
	{
    	case 'catalogo_fuente':
    		$servicioCasamientoEstructura = new ServicioEstructuraFuente();
    		$dataFuente = $servicioCasamientoEstructura->buscarFuentes($datosempresa['codemp']);
    		echo generarJson($dataFuente);
    		unset($servicioCasamientoEstructura);
    		unset($dataFuente);
    		break;	
		
    	case 'grabar':
    		$servicioCasamientoEstructura = new ServicioEstructuraFuente();
    		$arrevento ['codemp']  = $datosempresa['codemp'];
			$arrevento ['codusu']  = $_SESSION['la_logusr'];
			$arrevento ['codmenu'] = $objetoJson->codmenu;
			$arrevento ['codsis']  = 'CFG';
			$arrevento ['evento']  = '';
			$arrevento ['nomven']  = 'sigesp_vis_cfg_spg_estructurafuente.php';
			$arrevento ['desevetra'] = '';
			echo $servicioCasamientoEstructura->grabarCasamientoEstructuraFuente($datosempresa['codemp'], $objetoJson, $arrevento);
    		unset($servicioCasamientoEstructura);
    		break;
    		
    	case 'buscar_fuentes':
    		$servicioCasamientoEstructura = new ServicioEstructuraFuente();
    		$dataFuente = $servicioCasamientoEstructura->buscarCasamiento($datosempresa['codemp'], $objetoJson->codestpro1, $objetoJson->codestpro2, $objetoJson->codestpro3, $objetoJson->codestpro4, $objetoJson->codestpro5, $objetoJson->estcla);
    		echo generarJson($dataFuente, false, false);
    		unset($servicioCasamientoEstructura);
    		unset($dataFuente);
    		break;
    }
}
?>