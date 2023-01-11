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
	require_once($dirsrv.'/modelo/servicio/cfg/sigesp_srv_cfg_spi_planinstitucional.php');
	require_once('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon'])
	{
		$submit = str_replace("\\","",$_POST['ObjSon']);
		$json = new Services_JSON;
		$ArJson = $json->decode($submit);
		$servicioEvento = new ServicioEvento();
		switch ($ArJson->oper)
		{
			case 'catalogo':
				$oservicio = new ServicioCfg('spi_cuentas'); 
				if($_SESSION['la_empresa']['estpreing']==1)
				{
					$cantidad = $ArJson->numniv;
					if($cantidad==3)
					{
						$arrestpro[0]  = str_pad($ArJson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad($ArJson->datosestructura[0]->codest1,25,0,0);
						$arrestpro[2]  = str_pad($ArJson->datosestructura[0]->codest2,25,0,0);
						$arrestpro[3]  = str_pad('',25,0,0);
						$arrestpro[4]  = str_pad('',25,0,0);
						$arrestpro[5]  = $ArJson->datosestructura[0]->estcla;
					}
					else
					{
						$arrestpro[0]  = str_pad($ArJson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad($ArJson->datosestructura[0]->codest1,25,0,0);
						$arrestpro[2]  = str_pad($ArJson->datosestructura[0]->codest2,25,0,0);
						$arrestpro[3]  = str_pad($ArJson->datosestructura[0]->codest3,25,0,0);
						$arrestpro[4]  = str_pad($ArJson->datosestructura[0]->codest4,25,0,0);
						$arrestpro[5]  = $ArJson->datosestructura[0]->estcla;
					}
					$data = $oservicio->buscarPlanCuentaSpiEstructura($_SESSION["la_empresa"]["codemp"],$arrestpro[0],$arrestpro[1],$arrestpro[2],$arrestpro[3],$arrestpro[4],$arrestpro[5]);	
				}
				else
				{
					$data = $oservicio->buscarPlanCuentaSpi($_SESSION["la_empresa"]["codemp"]);	
				}			
				echo  generarJson($data);
				unset($oservicio);
				unset($data);	
				break;
				
			case 'incluirvarios':
				$numerror        = 0;
				$numguardado     = 0;
				$numerrorelim    = 0;
				$numguardadoelim = 0;
				
				$planCuenta = new ServicioPlanInstitucional();
				$ncuenta = count((array)$ArJson->datoscuenta);
				$mensaje="Inserto la(s) cuenta(s) ";	
				$tipoevento=true;
				$mensajeerror='';
				for($j=0;$j<=$ncuenta-1;$j++)
				{
					$auxdatos=$ArJson->datoscuenta[$j];
					$estructura="";
					if($_SESSION['la_empresa']['estpreing']==1)
					{
						$estructura=$ArJson->datosestructura[0];
					}
					$cuentaspi    = trim($auxdatos->spi_cuenta); 			
					$cuentascg    = trim($auxdatos->sc_cuenta);
					$denominacion = utf8_decode ($auxdatos->denominacion); 					
					if(($cuentaspi!="")&&($denominacion!="")&&($cuentascg!=""))
					{
						$planCuenta->validarCuenta($cuentaspi,$cuentascg);
						if($planCuenta->valido)
						{//Si la cuenta es valida me permite insertar la cuenta
							$planCuenta->grabarCuenta($auxdatos,$estructura);
							if (!$planCuenta->valido)
							{//No pudo procesar la cuenta
								$numerror++;
								$mensajeerror .= $planCuenta->mensaje;
							} 
							else
							{//Generó correctamente la cuenta
								$numguardado++;
								$mensaje .= ''.$cuentaspi.', ';
							}
						}
						else
						{
							$numerror++;
							$mensajeerror .= $planCuenta->mensaje;
						}
					}
				}
				if($ncuenta>0)
				{
					$servicioEvento->evento="INSERTAR";
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_spi_planinstitucional.php";
					$servicioEvento->desevetra=$mensaje.' en el plan de ingreso institucional';	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
				}
				$mensaje="Elimino la(s) cuenta(s) ";	
				$tipoevento=true;
				$ncuentaeli = count((array)$ArJson->datoscuentaseliminar);
				for($j=0;$j<=$ncuentaeli-1;$j++)
				{
					$cuentaspi    = trim($ArJson->datoscuentaseliminar[$j]->spi_cuenta);
					$estructura="";
					if($_SESSION['la_empresa']['estpreing']==1)
					{
						$estructura=$ArJson->datosestructura[0];
					}
					
					if($cuentaspi!='')
					{
						$planCuenta->eliminarCuenta($cuentaspi,$estructura);
						if (!$planCuenta->valido)
						{//No pudo procesar la eliminacion de la cuenta
							$numerrorelim++;
							$mensajeerror .= $planCuenta->mensaje;
						} 
						else
						{//Elimino correctamente la cuenta
							$numguardadoelim++;
							$mensaje .= ''.$cuentaspi.', ';
						}						
					}
					else
					{
						$numerrorelim++;
						$mensajeerror .= $planCuenta->mensaje;
					}
				}
				$numerror=$numerror+$numerrorelim;
				if($ncuentaeli>0)
				{
					$servicioEvento->evento="ELIMINAR";
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_spi_planinstitucional.php";
					$servicioEvento->desevetra=$mensaje.' en el plan de ingreso institucional';	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
				}
				echo $numerror."|".$numguardado."|".$numguardadoelim."|".$mensajeerror;
				unset($planCuenta);
				break;
		}
	}
}
?>