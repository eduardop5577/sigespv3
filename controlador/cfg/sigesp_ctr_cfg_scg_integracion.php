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
	require_once ('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon'])
	{
		$arre       = $_SESSION["la_empresa"];
		$submit = str_replace("\\","",$_POST['ObjSon']);
		$json = new Services_JSON;	
		$ArJson = $json->decode($submit);
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioCfg ('scg_casa_presu');
		$oservicio->setCodemp ($arre["codemp"]);			
		switch ($ArJson->oper)
		{
			case 'catalogo':
				$cadenasql = "SELECT trim(sig_cuenta) as sig_cuenta,trim(sc_cuenta) as sc_cuenta, cueclaeco, cueoncop, ".
							 "       (SELECT denominacion  FROM sigesp_plan_unico_re ".
							 "     	   WHERE sigesp_plan_unico_re.sig_cuenta=scg_casa_presu.sig_cuenta) as denominacion ".
							 "  FROM scg_casa_presu ".
							 " WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
							 " ORDER BY sig_cuenta ASC";
				$Datos = $oservicio->buscarSql($cadenasql);					
				echo generarJson($Datos);
				unset($oservicio);
				unset($Datos); 	
				break;
				
			
			case 'incluirvarios':
				ServicioCfg::iniTransaccion ();
                                $oservicio->eliminarIntegacionTodos();
				$mensaje = 'Inserto la(s) cuenta(s) presupuestaria(s) ';
				for($j=0;$j<=count((array)$ArJson->datos)-1;$j++)
				{
					$ArJson->datos[$j]->sig_cuenta = trim($ArJson->datos[$j]->sig_cuenta);
					$ArJson->datos[$j]->sc_cuenta = trim($ArJson->datos[$j]->sc_cuenta); 
					if($oservicio->modificarDto ($ArJson->datos[$j],true))
					{
						$mensaje .= ''.$ArJson->datos[$j]->sig_cuenta.' asociada a la cuenta contable '.$ArJson->datos[$j]->sc_cuenta.' de la empresa '.$arre ['codemp'];	
						$tipoevento=true;
					}
					else
					{
						$mensaje='Error al insertar la cuenta presupuestaria '.$ArJson->datos[$j]->sig_cuenta.' asociada a la cuenta contable '.$ArJson->datos[$j]->sc_cuenta.' de la empresa '.$arre['codemp'];	
						$tipoevento=false;
						break;
					}
				}
				if (ServicioCfg::comTransaccion ())
				{
					echo '1';
				}
				else
				{
					echo '0';
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scg_integracion.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$mensaje;
				$servicioEvento->incluirEvento();
				unset($oservicio);
				unset($servicioEvento);
				break;
				
			case 'eliminarvarios':
				ServicioCfg::iniTransaccion ();
				$arrcuentaerro = array();
				$numcuentaeli  = 0;
				$mensaje='Elimino la(s) cuenta(s) presupuestaria(s) ';	
				$tipoevento=true;
				for($j=0;$j<=count((array)$ArJson->datos)-1;$j++)
				{
					$ArJson->datos[$j]->sig_cuenta = trim($ArJson->datos[$j]->sig_cuenta);
					$ArJson->datos[$j]->sc_cuenta = trim($ArJson->datos[$j]->sc_cuenta); 
					if ($oservicio->validarCuentaMovimiento($_SESSION["la_empresa"]["codemp"], $ArJson->datos[$j]->sig_cuenta))
					{
						if($oservicio->eliminarDto($ArJson->datos[$j])=='')
						{
							$numcuentaeli++;
							$mensaje .= ''.$ArJson->datos[$j]->sig_cuenta.' asociada a la cuenta contable '.$ArJson->datos[$j]->sc_cuenta.' de la empresa '.$arre['codemp'];	
						}
						else
						{
							$mensaje = 'Error al eliminar la cuenta '.$ArJson->datos[$j]->sig_cuenta.' asociada a la cuenta contable '.$ArJson->datos[$j]->sc_cuenta.' de la empresa '.$arre['codemp'];	
							$tipoevento=false;
							break;
						}
					}
					else
					{
						$arrcuentaerro[] = $ArJson->datos[$j]->sig_cuenta;
					}
				}
				if (ServicioCfg::comTransaccion ())
				{
					$cuentaerro = implode(",", $arrcuentaerro);
					echo $cuentaerro.'|'.$numcuentaeli;
				}
				else
				{
					echo '|0';
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_scg_integracion.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$mensaje;
				$servicioEvento->incluirEvento();								
				unset($oservicio);
				unset($servicioEvento);
				break;
		}
	}
}
?>