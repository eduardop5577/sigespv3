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
		$oservicio = new ServicioCfg('scb_ctabanco');
		$servicioEvento = new ServicioEvento();
		$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
		$Evento = $ArJson->oper;
		
		switch ($Evento)
		{
			case 'incluir':
				ServicioCfg::iniTransaccion ();
				if ($ArJson->feccie == '' )
				{
					$ArJson->feccie = '1900-01-01';
				}
				else
				{
					$ArJson->feccie = convertirFechaBd($ArJson->feccie);
				}
				if ($ArJson->fecapr == '')
				{
					$ArJson->fecapr = '1900-01-01';
				}
				else
				{
					$ArJson->fecapr = convertirFechaBd($ArJson->fecapr);
				}
				$mensaje='Inserto el Tipo de cuenta ' . $ArJson->ctaban . 'perteneciente al banco'. $ArJson->codban . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
				$tipoevento=true;
				$oservicio->incluirDto ( $ArJson );
				if (ServicioCfg::comTransaccion ()) {
					echo "|1";
				}
				else
				{
					$dao=$oservicio->getDaogenerico();
					if($dao->errorDuplicate)
					{
						echo "La cuenta que intenta registrar existe |0";
					}
					else
					{
						echo "|0";
					}
					$mensaje='Error al insertar el Tipo de cuenta ' . $ArJson->ctaban . 'perteneciente al banco'. $ArJson->codban . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_cuentabanco.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
						
			case 'catalogo':
				if ((strtoupper($_SESSION["ls_gestor"]) == "MYSQLT") || (strtoupper($_SESSION["ls_gestor"]) == "MYSQLI"))
				{
					$cadenaSeguridad = " AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'". 
									   " AND scb_ctabanco.codemp=sss_permisos_internos.codemp". 
									   " AND trim(CONCAT(scb_ctabanco.codban,'-',scb_ctabanco.ctaban))= trim(sss_permisos_internos.codintper)";
				}
				else {
					$cadenaSeguridad = " AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'". 
									   " AND scb_ctabanco.codemp=sss_permisos_internos.codemp". 
									   " AND  trim(sss_permisos_internos.codintper)=trim(scb_ctabanco.codban||'-'||scb_ctabanco.ctaban) ";
				}
				
				$cadenasql="SELECT scb_ctabanco.ctaban as ctaban, scb_ctabanco.dencta as dencta, scb_ctabanco.sc_cuenta as sc_cuenta, scg_cuentas.denominacion as denominacion, ".
						   "	   scb_ctabanco.codban as codban, scb_banco.nomban as nomban, scb_ctabanco.codtipcta as codtipcta, scb_tipocuenta.nomtipcta as nomtipcta, ".
						   "	   scb_ctabanco.fecapr as fecapr, scb_ctabanco.feccie as feccie, scb_ctabanco.estact as estact, scb_ctabanco.ctabanext as ctabanext, ".
						   "	   scb_ctabanco.codmon as codmon, sigesp_moneda.denmon as denmon ".
						   "  FROM scb_ctabanco, scb_tipocuenta, scb_banco, scg_cuentas, sss_permisos_internos, sigesp_moneda ".  
						   " WHERE scb_ctabanco.codemp='".$_SESSION["la_empresa"]["codemp"]."' ". 
						   "   AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta ". 
						   "   AND scb_ctabanco.codban=scb_banco.codban ". 
						   "   AND scb_ctabanco.sc_cuenta=scg_cuentas.sc_cuenta ".
						   "   AND scb_ctabanco.codemp=scg_cuentas.codemp ".
						   "   AND scb_ctabanco.codmon=sigesp_moneda.codmon ".
						   "   AND scb_ctabanco.codemp=sigesp_moneda.codemp ".
						   "  {$cadenaSeguridad}"; 
				$dataCuentaBanco = $oservicio->buscarSql($cadenasql);
				echo generarJson($dataCuentaBanco);
				unset($dataCuentaBanco);	
				break;
				
			case 'actualizar':
				ServicioCfg::iniTransaccion ();
				$ArJson->feccie = convertirFechaBd($ArJson->feccie);
				$ArJson->fecapr = convertirFechaBd($ArJson->fecapr);
				$oservicio->modificarDto($ArJson);
				$mensaje='Actualizo el Tipo de cuenta ' . $ArJson->ctaban . 'perteneciente al banco'. $ArJson->codban . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					echo "|0";
					$mensaje='Error al actualizar el Tipo de cuenta ' . $ArJson->ctaban . 'perteneciente al banco'. $ArJson->codban . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;
				}
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_cuentabanco.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'eliminar':
				ServicioCfg::iniTransaccion ();
				$respuesta = $oservicio->eliminarDto($ArJson);
				$mensaje='Elimino el Tipo de cuenta ' . $ArJson->ctaban . 'perteneciente al banco'. $ArJson->codban . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				}
				else
				{
					if($respuesta!='')
					{
						if($respuesta=='-1')
						{
							echo '|-9';
						}
					}
					else
					{
						echo "|0";
					}
					$mensaje='Error al eliminar el Tipo de cuenta ' . $ArJson->ctaban . 'perteneciente al banco'. $ArJson->codban . ' Asociada a la empresa '.$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scb_cuentabanco.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'catalogofiltrobanco':
				$dataCuentaFil = $oservicio->obtenerChequeraBanco($_SESSION["la_empresa"]["codemp"],$ArJson->codban);
				echo generarJson($dataCuentaFil);
				unset($dataCuentaFil);
				break;	

			case 'catalogocol':
				if ((strtoupper($_SESSION["ls_gestor"]) == "MYSQLT") || (strtoupper($_SESSION["ls_gestor"]) == "MYSQLI"))
				{
					$cadenaSeguridad = " AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'". 
									   " AND scb_ctabanco.codemp=sss_permisos_internos.codemp". 
									   " AND trim(CONCAT(scb_ctabanco.codban,'-',scb_ctabanco.ctaban))= trim(sss_permisos_internos.codintper)";
				}
				else {
					$cadenaSeguridad = " AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'". 
									   " AND scb_ctabanco.codemp=sss_permisos_internos.codemp". 
									   " AND  trim(sss_permisos_internos.codintper)=trim(scb_ctabanco.codban||'-'||scb_ctabanco.ctaban) ";
				}
				
				$cadenasql="SELECT scb_ctabanco.ctaban as ctaban, scb_ctabanco.dencta as dencta,scb_banco.nomban as nomban, scb_tipocuenta.nomtipcta as nomtipcta, scb_ctabanco.sc_cuenta as sc_cuenta ".
						   "  FROM scb_ctabanco, scb_tipocuenta, scb_banco, scg_cuentas, sss_permisos_internos ".  
						   " WHERE scb_ctabanco.codemp='".$_SESSION["la_empresa"]["codemp"]."' ". 
						   "   AND scb_ctabanco.codban='".$ArJson->codban."' ". 
						   "   AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta ". 
						   "   AND scb_ctabanco.codban=scb_banco.codban ". 
						   "   AND scb_ctabanco.sc_cuenta=scg_cuentas.sc_cuenta ".
						   "   AND scb_ctabanco.codemp=scg_cuentas.codemp ".
						   "  {$cadenaSeguridad}"; 

				$dataCuentaBanco = $oservicio->buscarSql($cadenasql);
				echo generarJson($dataCuentaBanco);
				unset($dataCuentaBanco);	
				break;
		}
		unset($oregevent);
		unset($oservicio);
	}
}
?>