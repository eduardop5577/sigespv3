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
	require_once('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon']) 		
	{
		$submit = str_replace("\\","",$_POST['ObjSon']);
		$json = new Services_JSON;	
		$ArJson = $json->decode($submit);
		$oserviciocfg = new ServicioCfg('sigesp_cargos');
			
		switch ($ArJson->oper)
		{
			case 'catalogo':
				$dataCargo = $oserviciocfg->buscarTodos();					
				echo generarJson($dataCargo);
				unset($dataCargo);
				break;
			
			case 'catalogo_general':
				$dataCargoGen = $oserviciocfg->buscarCargos($datosempresa["codemp"], 'G');					
				echo generarJson($dataCargoGen);
				unset($dataCargoGen);	
				break;
			
			case 'catalogo_adicional':
				$dataCargoAdi = $oserviciocfg->buscarCargos($datosempresa["codemp"], 'A');					
				$ObjSon= generarJson($dataCargoAdi);
				echo $ObjSon;	
				break;
			
			case 'buscarcargobienes':
				$cadenasql = "SELECT siv_cargosarticulo.codart AS coditem, sigesp_cargos.codcar, sigesp_cargos.dencar, sigesp_cargos.spg_cuenta, sigesp_cargos.formula, ".
							 "	     (SELECT COUNT(spg_cuenta) FROM spg_cuentas ".
							 "	       WHERE spg_cuentas.codestpro1 = '".$ArJson->codestpro1."' ".
							 "			 AND spg_cuentas.codestpro2 = '".$ArJson->codestpro2."' ".
							 "      	 AND spg_cuentas.codestpro3 = '".$ArJson->codestpro3."' ".
							 "   	     AND spg_cuentas.codestpro4 = '".$ArJson->codestpro4."' ".
							 "			 AND spg_cuentas.codestpro5 = '".$ArJson->codestpro5."' ".
							 "			 AND spg_cuentas.estcla = '".$ArJson->estcla."' ". 
							 "			 AND sigesp_cargos.codemp = spg_cuentas.codemp  ".
							 "		 	 AND sigesp_cargos.spg_cuenta = spg_cuentas.spg_cuenta) AS existecuenta  ".
							 "	FROM siv_cargosarticulo, sigesp_cargos ".
							 " WHERE siv_cargosarticulo.codemp ='".$datosempresa["codemp"]."' ".
							 "   AND siv_cargosarticulo.codart ='".$ArJson->codart."' ".
							 "   AND siv_cargosarticulo.codemp = sigesp_cargos.codemp ".
							 "   AND siv_cargosarticulo.codcar = sigesp_cargos.codcar";
				$dataCargoBienes = $oserviciocfg->buscarSql($cadenasql);					
				echo generarJson($dataCargoBienes);
				unset($dataCargoBienes);	
				break;
			
			case 'buscarcargoservicios':
				$cadenasql = "SELECT soc_serviciocargo.codser AS coditem, sigesp_cargos.codcar, sigesp_cargos.dencar, sigesp_cargos.spg_cuenta, sigesp_cargos.formula, ".
							 "		 (SELECT COUNT(spg_cuenta) FROM spg_cuentas ".
							 "	 	   WHERE spg_cuentas.codestpro1 = '".$ArJson->codestpro1."' ".
							 "		 	 AND spg_cuentas.codestpro2 = '".$ArJson->codestpro2."' ".
							 "		 	 AND spg_cuentas.codestpro3 = '".$ArJson->codestpro3."' ".
							 "		 	 AND spg_cuentas.codestpro4 = '".$ArJson->codestpro4."' ".
							 "		 	 AND spg_cuentas.codestpro5 = '".$ArJson->codestpro5."' ".
							 "		 	 AND spg_cuentas.estcla = '".$ArJson->estcla."'  ".
							 "		 	 AND sigesp_cargos.codemp = spg_cuentas.codemp  ".
							 "		 	 AND sigesp_cargos.spg_cuenta = spg_cuentas.spg_cuenta) AS existecuenta ".
							 "  FROM soc_serviciocargo, sigesp_cargos  ".
							 " WHERE soc_serviciocargo.codemp ='".$datosempresa["codemp"]."'  ".
							 "   AND soc_serviciocargo.codser ='".$ArJson->codser."' ". 
							 "   AND soc_serviciocargo.codemp = sigesp_cargos.codemp  ".
							 "   AND soc_serviciocargo.codcar = sigesp_cargos.codcar";
				$dataCargoServicio = $oserviciocfg->buscarSql($cadenasql);					
				echo generarJson($dataCargoServicio);
				unset($dataCargoServicio);	
				break;
			
			case 'buscarcargoconcepto':
				$cadenasql = "SELECT sep_conceptocargos.codconsep AS coditem, sigesp_cargos.codcar, sigesp_cargos.dencar, sigesp_cargos.spg_cuenta, sigesp_cargos.formula, ".
							 "		 (SELECT COUNT(spg_cuenta) FROM spg_cuentas  ".
							 "		   WHERE spg_cuentas.codestpro1 = '".$ArJson->codestpro1."' ".
							 "			 AND spg_cuentas.codestpro2 = '".$ArJson->codestpro2."' ".
							 "			 AND spg_cuentas.codestpro3 = '".$ArJson->codestpro3."' ".
							 "			 AND spg_cuentas.codestpro4 = '".$ArJson->codestpro4."' ".
							 "			 AND spg_cuentas.codestpro5 = '".$ArJson->codestpro5."' ".
							 "			 AND spg_cuentas.estcla = '".$ArJson->estcla."'  ".
							 "			 AND sigesp_cargos.codemp = spg_cuentas.codemp  ".
							 "			 AND sigesp_cargos.spg_cuenta = spg_cuentas.spg_cuenta) AS existecuenta ".
							 "  FROM sep_conceptocargos, sigesp_cargos  ".
							 " WHERE sep_conceptocargos.codemp ='".$datosempresa["codemp"]."' ".
							 "	 AND sep_conceptocargos.codconsep ='".$ArJson->codcon."' ".
							 "	 AND sep_conceptocargos.codemp = sigesp_cargos.codemp ".
							 "	 AND sep_conceptocargos.codcar = sigesp_cargos.codcar";
				$datos = $oserviciocfg->buscarSql($cadenasql);					
				echo generarJson($datos);
				unset($datos);	
				break;
		}
		unset($oserviciocfg);
	}
}
?>