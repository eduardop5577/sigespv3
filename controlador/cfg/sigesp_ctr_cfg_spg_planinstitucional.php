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
	require_once($dirsrv.'/modelo/servicio/cfg/sigesp_srv_cfg_spg_planinstitucional.php');
	require_once('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon'])
	{
		$formatopresupuesto = trim($datosempresa["formpre"]);
		$formatoaux = str_replace( "-", "",$formatopresupuesto);
		$longitudctaconf   = strlen($formatoaux);

		$submit = str_replace("\\", "", $_POST['ObjSon']);
		$json = new Services_JSON;
		$arrjson = $json->decode($submit);
		$planCuenta = new ServicioPlanInstitucional();
		$servicioEvento = new ServicioEvento();
		$cantidad = $arrjson->numniv;
			
		switch ($arrjson->operacion)
		{
			case 'incluir':
				//variables que almacenan el numero de errores y cuentas almacenadas
				$numerror=0;
				$numguardado=0;
				$numerrorelim=0;
				$numguardadoelim=0;
				$arrestpro=array();
				//arreglo de la estructura presupuestaria
				switch($cantidad)
				{
					case "1":
						$arrestpro[0]  = str_pad($arrjson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad('',25,0,0);
						$arrestpro[2]  = str_pad('',25,0,0);
						$arrestpro[3]  = str_pad('',25,0,0);
						$arrestpro[4]  = str_pad('',25,0,0);
						$arrestpro[5]  = $arrjson->datosestructura[0]->estcla;
						$arrestpro[6]  = "";
						break;
					case "2":
						$arrestpro[0]  = str_pad($arrjson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad($arrjson->datosestructura[0]->codest1,25,0,0);
						$arrestpro[2]  = str_pad('',25,0,0);
						$arrestpro[3]  = str_pad('',25,0,0);
						$arrestpro[4]  = str_pad('',25,0,0);
						$arrestpro[5]  = $arrjson->datosestructura[0]->estcla;
						$arrestpro[6]  = "";
						break;
					case "3":
						$arrestpro[0]  = str_pad($arrjson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad($arrjson->datosestructura[0]->codest1,25,0,0);
						$arrestpro[2]  = str_pad($arrjson->datosestructura[0]->codest2,25,0,0);
						$arrestpro[3]  = str_pad('',25,0,0);
						$arrestpro[4]  = str_pad('',25,0,0);
						$arrestpro[5]  = $arrjson->datosestructura[0]->estcla;
						$arrestpro[6]  = "";
						break;
					case "4":
						$arrestpro[0]  = str_pad($arrjson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad($arrjson->datosestructura[0]->codest1,25,0,0);
						$arrestpro[2]  = str_pad($arrjson->datosestructura[0]->codest2,25,0,0);
						$arrestpro[3]  = str_pad($arrjson->datosestructura[0]->codest3,25,0,0);
						$arrestpro[4]  = str_pad('',25,0,0);
						$arrestpro[5]  = $arrjson->datosestructura[0]->estcla;
						$arrestpro[6]  = "";
						break;
					case "5":
						$arrestpro[0]  = str_pad($arrjson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad($arrjson->datosestructura[0]->codest1,25,0,0);
						$arrestpro[2]  = str_pad($arrjson->datosestructura[0]->codest2,25,0,0);
						$arrestpro[3]  = str_pad($arrjson->datosestructura[0]->codest3,25,0,0);
						$arrestpro[4]  = str_pad($arrjson->datosestructura[0]->codest4,25,0,0);
						$arrestpro[5]  = $arrjson->datosestructura[0]->estcla;
						$arrestpro[6]  = "";
						break;
				}
				$mensajeerror='';
				$mensaje="Elimino la(s) cuenta(s) ";	
				$tipoevento=true;
				$ncuentaeli = count((array)$arrjson->datoscuentaseliminar);
				for($j=0;$j<=$ncuentaeli-1;$j++)
				{
					$cuentaspg    = trim($arrjson->datoscuentaseliminar[$j]->sig_cuenta);
					if($cuentaspg!='')
					{
						$planCuenta->eliminarCuenta($cuentaspg,$arrestpro);
						if (!$planCuenta->valido)
						{//No pudo procesar la eliminacion de la cuenta
							$numerrorelim++;
							$mensajeerror .= $planCuenta->mensaje;
						} 
						else
						{//Elimino correctamente la cuenta
							$numguardadoelim++;
							$mensaje .= ''.$cuentaspg.', ';
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
					$servicioEvento->nomfisico="sigesp_vis_cfg_spg_planinstitucional.php";
					$servicioEvento->desevetra=$mensaje.' en el plan de gasto institucional';	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
				}

                                $mensaje="Inserto/Actualizo la(s) cuenta(s) ";	
				$ncuenta = count((array)$arrjson->datoscuentas);
				$tipoevento=true;
				//procesa la insercion de las cuentas de gastos
				for($j=0;$j<=$ncuenta-1;$j++)
				{
					$cuentaspg = trim($arrjson->datoscuentas[$j]->sig_cuenta);
					$denominacion = utf8_decode ($arrjson->datoscuentas[$j]->dencuenta);
					$cuentascg = trim($arrjson->datoscuentas[$j]->sc_cuenta);
                                        $cueclaeco = trim($arrjson->datoscuentas[$j]->cueclaeco);
					if (($cuentaspg!="")&&($denominacion!="")&&($cuentascg!=""))
					{
						$longitudcta=strlen($cuentaspg);
						if ($longitudcta!=$longitudctaconf)
						{
							$cuentaspg = str_pad($codcuentaspg,$longitudctaconf,0,STR_PAD_RIGHT);
						}
						$planCuenta->validarCuenta($cuentaspg,$arrestpro,$cuentascg);
						if($planCuenta->valido)
						{//Si la cuenta es valida me permite insertar la cuenta
							$planCuenta->grabarCuenta($cuentaspg,$denominacion,$arrestpro,$cuentascg,$cueclaeco);
							if (!$planCuenta->valido)
							{//No pudo procesar la cuenta
								$numerror++;
								$mensajeerror .= $planCuenta->mensaje;
							} 
							else
							{//Generó correctamente la cuenta
								$numguardado++;
								$mensaje .= ''.$cuentaspg.', ';
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
					$planCuenta->cuentaFuenteFinanciamiento($arrestpro);
                                        $servicioEvento->evento="INSERTAR";
					$servicioEvento->codusu=$_SESSION["la_logusr"];
					$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
					$servicioEvento->codsis="CFG";
					$servicioEvento->nomfisico="sigesp_vis_cfg_spg_planinstitucional.php";
					$servicioEvento->desevetra=$mensaje.' en el plan de gasto institucional';	
					$servicioEvento->tipoevento=$tipoevento;
					$servicioEvento->incluirEvento();
				}
				echo $numerror."|".$numguardado."|".$numguardadoelim."|".$mensajeerror;
				break;
	
			case 'buscarcuenta':
				$oserviciospg = new ServicioCfg ( 'spg_cuentas' );
				$oserviciospg->setCodemp ( $datosempresa["codemp"] );
				switch($cantidad)
				{
					case "1":
						$arrestpro[0]  = str_pad($arrjson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad('',25,0,0);
						$arrestpro[2]  = str_pad('',25,0,0);
						$arrestpro[3]  = str_pad('',25,0,0);
						$arrestpro[4]  = str_pad('',25,0,0);
						$arrestpro[5]  = $arrjson->datosestructura[0]->estcla;
						break;
					case "2":
						$arrestpro[0]  = str_pad($arrjson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad($arrjson->datosestructura[0]->codest1,25,0,0);
						$arrestpro[2]  = str_pad('',25,0,0);
						$arrestpro[3]  = str_pad('',25,0,0);
						$arrestpro[4]  = str_pad('',25,0,0);
						$arrestpro[5]  = $arrjson->datosestructura[0]->estcla;
						break;
					case "3":
						$arrestpro[0]  = str_pad($arrjson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad($arrjson->datosestructura[0]->codest1,25,0,0);
						$arrestpro[2]  = str_pad($arrjson->datosestructura[0]->codest2,25,0,0);
						$arrestpro[3]  = str_pad('',25,0,0);
						$arrestpro[4]  = str_pad('',25,0,0);
						$arrestpro[5]  = $arrjson->datosestructura[0]->estcla;
						break;
					case "4":
						$arrestpro[0]  = str_pad($arrjson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad($arrjson->datosestructura[0]->codest1,25,0,0);
						$arrestpro[2]  = str_pad($arrjson->datosestructura[0]->codest2,25,0,0);
						$arrestpro[3]  = str_pad($arrjson->datosestructura[0]->codest3,25,0,0);
						$arrestpro[4]  = str_pad('',25,0,0);
						$arrestpro[5]  = $arrjson->datosestructura[0]->estcla;
						break;
					case "5":
						$arrestpro[0]  = str_pad($arrjson->datosestructura[0]->codest0,25,0,0);
						$arrestpro[1]  = str_pad($arrjson->datosestructura[0]->codest1,25,0,0);
						$arrestpro[2]  = str_pad($arrjson->datosestructura[0]->codest2,25,0,0);
						$arrestpro[3]  = str_pad($arrjson->datosestructura[0]->codest3,25,0,0);
						$arrestpro[4]  = str_pad($arrjson->datosestructura[0]->codest4,25,0,0);
						$arrestpro[5]  = $arrjson->datosestructura[0]->estcla;
						break;
				}
				//este metodo busca las cuentas asociada a una estructura dada
				$datoscuentas = $oserviciospg->buscarCuentas($arrestpro[0],$arrestpro[1],$arrestpro[2],$arrestpro[3],$arrestpro[4],$arrestpro[5]);
				echo generarJson($datoscuentas);
				unset($datoscuentas);
				break;
		}
		unset($oserviciospg);
		unset($oservicioemp);
	}
}
?>