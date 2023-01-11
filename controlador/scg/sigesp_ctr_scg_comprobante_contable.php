<?php
/***********************************************************************************
* @fecha de modificacion: 01/08/2022, para la version de php 8.1 
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
	$dirctrscg = "";
	$dirctrscg = dirname(__FILE__);
	$dirctrscg = str_replace("\\","/",$dirctrscg); 
	$dirctrscg = str_replace("/controlador/scg","",$dirctrscg);
	require_once ($dirctrscg."/base/librerias/php/general/Json.php");
	require_once ($dirctrscg."/modelo/servicio/scg/sigesp_srv_scg_comprobante_contable.php");
	$_SESSION['session_activa'] = time();	
	
	if ($_POST['ObjSon'])
	{
		$submit = str_replace("\\", "", $_POST['ObjSon']);
		$json = new Services_JSON;
		$objetoJson = $json->decode($submit);
		
		switch ($objetoJson->operacion)
		{
			case "llenar_documento":
				echo str_pad($objetoJson->numdoc,15,"0",STR_PAD_LEFT);
			break;

			case "comprobantemasivo":
				echo trim($datosempresa['estconlot']);
			break;
			
			case "comprobantemasivo2":
				echo trim($datosempresa['estcommas']);
			break;
			
			case "cargar_nrodocumento":
				$servicioCmp = new ServicioComprobanteContable();
				$numdoc = $servicioCmp->generarConsecutivo($objetoJson->prefijo); 
				echo str_pad($numdoc,15,"0",STR_PAD_LEFT); 
				unset($servicioCmp);
			break;
			
			case "buscarProcedencia":
				$servicioCmp = new ServicioComprobanteContable();
				echo generarJson($servicioCmp->buscarProcedencias());
				unset($servicioCmp);
			break;
                    
			case "buscarPrefijosUsuarios":
				$servicioCmp = new ServicioComprobanteContable();
				echo generarJson($servicioCmp->buscarPrefijosUsuarios());
				unset($servicioCmp);
			break;
			
			case "buscarComprobantesContables":
				$servicioCmp = new ServicioComprobanteContable();
				echo generarJson($servicioCmp->buscarComprobantes($_SESSION['la_empresa']['codemp'],$objetoJson->comprobante,$objetoJson->procede,$objetoJson->tipo,$objetoJson->provben,$objetoJson->fecdesde,$objetoJson->fechasta,'SCGCMP'));
				unset($servicioCmp);
			break;
			
			case "buscarDetallesContables":
				$servicioCmp = new ServicioComprobanteContable();
				echo generarJson($servicioCmp->cargarDetalleContable($_SESSION['la_empresa']['codemp'],$objetoJson->procede,$objetoJson->comprobante,$objetoJson->fecha,$objetoJson->codban,$objetoJson->ctaban));
				unset($servicioCmp);
			break;
			
			case "guardar":
				$servicioCmp = new ServicioComprobanteContable($objetoJson->prefijo);
				$arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
				$arrevento ['codusu']  = $_SESSION['la_logusr'];
				$arrevento ['codsis']  = $objetoJson->codsis;
				$arrevento ['evento']  = 'PROCESAR';
				$arrevento ['nomfisico']  = $objetoJson->nomven; 
				$arrevento ['desevetra'] = 'Guardo el comprobante contable con el numero'.$objetoJson->comprobante.', asociado a la empresa '.$_SESSION['la_empresa']['codemp'];
				$valido = $servicioCmp->guardarCmpCon($_SESSION['la_empresa']['codemp'],$objetoJson,$arrevento);
				$resultado['mensaje'] = $servicioCmp->mensaje;  
				$resultado['valido']  = $valido;    		
				echo json_encode(array('raiz'=>$resultado));
				unset($servicioCmp);
				break;	
				
			case "eliminar":
				$servicioCmp = new ServicioComprobanteContable();
				$arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
				$arrevento ['codusu']  = $_SESSION['la_logusr'];
				$arrevento ['codsis']  = $objetoJson->codsis;
				$arrevento ['evento']  = 'DELETE';
				$arrevento ['nomfisico']  = $objetoJson->nomven; 
				$arrevento ['desevetra'] = 'Elimino el comprobante contable con el número'.$objetoJson->comprobante.', asociado a la empresa '.$_SESSION['la_empresa']['codemp'];
				$valido = $servicioCmp->eliminarCmpCon($_SESSION['la_empresa']['codemp'],$objetoJson,$arrevento);
				$resultado['mensaje'] = $servicioCmp->mensaje;  
				$resultado['valido']  = $valido;    		
				echo json_encode(array('raiz'=>$resultado));
				unset($servicioCmp);
				break;
				
			case "cargar_archivo":
				$nombrearchivo = $dirctrscg.'/vista/scg/txt/'.$_SESSION['la_logusr'].'.txt';
				if (file_exists("$nombrearchivo"))
				{
					$archivo=@file("$nombrearchivo");
					$total=count((array)$archivo);
					$fecha = substr($objetoJson->fecha,8,2).'/'.substr($objetoJson->fecha,5,2).'/'.substr($objetoJson->fecha,0,4);
					$contador=0;
					for($i=0;($i<$total);$i++)
					{
						$contable=explode("|",$archivo[$i]);
						$debhab='H';
						if (trim($contable[7]) == '40')
						{
							$debhab='D';
						}
						$monto = str_replace(',','.',$contable[9]);
						$monto = doubleval($monto);
						$monto = number_format($monto,2,',','.');
						$arrContabilidad[$contador]['sc_cuenta'] = trim($contable[8]);
						$arrContabilidad[$contador]['debhab'] = $debhab;
						$arrContabilidad[$contador]['monto'] = $monto;
						$contador++;
					}
		    		echo generarJsonArreglo($arrContabilidad);
				}
			break;
				
			case "cargar_archivo2":
				require_once ($dirctrscg."/base/librerias/php/general/sigesp_lib_funciones.php");
				require_once($dirctrscg."/base/librerias/php/readexcel/reader.php");
				$excel = new Spreadsheet_Excel_Reader();
				$nombrearchivo = $dirctrscg.'/vista/scg/txt/'.$_SESSION['la_logusr'].'.xls';
				if (file_exists("$nombrearchivo"))
				{
					$archivo=@file("$nombrearchivo");
					$excel->setOutputEncoding("CP1251");
					$excel->read($nombrearchivo);
					$contador=0;
					for($li_indexfil=$excel->sheets[0]['numRows'];($li_indexfil>=2);$li_indexfil--)
					{
						$contable=$excel->sheets[0]['cells'][$li_indexfil][2];
						$descripcion=$excel->sheets[0]['cells'][$li_indexfil][3];
						$montoDebe=$excel->sheets[0]['cells'][$li_indexfil][4];
						$montoHaber=$excel->sheets[0]['cells'][$li_indexfil][5];
						$documento=$excel->sheets[0]['cells'][$li_indexfil][6];
						$debhab='H';
						$monto=$montoHaber;
						if($montoDebe>0)
						{
							$debhab="D";
							$monto=$montoDebe;
						}
						$monto=uf_formatonumerico($monto);
						$arrContabilidad[$contador]['sc_cuenta'] = trim($contable);
						$arrContabilidad[$contador]['debhab'] = $debhab;
						$arrContabilidad[$contador]['monto'] = $monto;
						$arrContabilidad[$contador]['descripcion'] = $descripcion;
						$arrContabilidad[$contador]['documento'] = $documento;
						$contador++;
				
					}					
		    		echo generarJsonArreglo($arrContabilidad);
				}
			break;
				
			case "cargar_archivo_lote":
				$nombrearchivo = $dirctrscg.'/vista/scg/txt/'.$_SESSION['la_logusr'].'.txt';
				if (file_exists("$nombrearchivo"))
				{
					$archivo=@file("$nombrearchivo");
					$total=count((array)$archivo);
					$descripcion = $objetoJson->descripcion;
					$contador=0;
					$servicioCmp = new ServicioComprobanteContable();
                                        $prefijo = '';
                                        $result = $servicioCmp->buscarPrefijosUsuarios();
                                        if (!$result->EOF)
                                        {
                                            $prefijo = $result->fields['prefijo'];
                                        }
                                        unset($result);
					$numdoc = $servicioCmp->generarConsecutivo($prefijo);
					unset($servicioCmp);
					$arrfechas[$contador]='1900-01-01'; 
					for($i=0;($i<$total);$i++)
					{
						$contable=explode("|",$archivo[$i]);
						$fecha=trim(ereg_replace("[^A-Za-z0-9]", "", trim($contable[0])));
						$fechacmp=trim($contable[0]);
						$monto_debe=0;
						$monto_haber=0;
						if(!in_array($fecha,$arrfechas,true)&&($fecha<>''))
						{
							for($j=0;($j<$total);$j++)
							{
								$contable=explode("|",$archivo[$j]);
								$fecha2=ereg_replace("[^A-Za-z0-9]", "", trim($contable[0]));
								if ($fecha == $fecha2)
								{
									$monto = str_replace(',','.',$contable[9]);
									$monto = doubleval($monto);
									if (trim($contable[7]) == '40')
									{
										$monto_debe=number_format($monto_debe+$monto,2,'.','');
									}
									else
									{
										$monto_haber=number_format($monto_haber+$monto,2,'.','');
									}
								}
								unset($contable);
							}
							$arrContabilidad[$contador]['comprobante']=$numdoc; 
							$arrContabilidad[$contador]['descripcion']=$descripcion;
							$arrContabilidad[$contador]['fecha']=$fechacmp;
							$arrContabilidad[$contador]['monto_debe']=number_format($monto_debe,2,',','.');
							$arrContabilidad[$contador]['monto_haber']=number_format($monto_haber,2,',','.');
							$arrContabilidad[$contador]['valido']=0;
							if (doubleval($monto_debe)==doubleval($monto_haber))
							{
								$arrContabilidad[$contador]['valido']=1;
							}
							$arrfechas[$contador]=$fecha;
                                                        $numero = substr($numdoc,6,10) +1;
                                                        
                                                        $numdoc=substr($numdoc,0,6).str_pad($numero,9,"0",STR_PAD_LEFT); 
							$contador++;
						}
					}
		    		echo generarJsonArreglo($arrContabilidad);
				}
			break;

			case "detalles_archivo":
				$nombrearchivo = $dirctrscg.'/vista/scg/txt/'.$_SESSION['la_logusr'].'.txt';
				if (file_exists("$nombrearchivo"))
				{
					$archivo=@file("$nombrearchivo");
					$total=count((array)$archivo);
					$fecha = $objetoJson->fecha;
					$contador=0;
					for($i=0;($i<$total);$i++)
					{
						$contable=explode("|",$archivo[$i]);
						if ($fecha == $contable[0])
						{
							$debhab='H';
							if (trim($contable[7]) == '40')
							{
								$debhab='D';
							}
							$monto = str_replace(',','.',$contable[9]);
							$monto = doubleval($monto);
							$monto = number_format($monto,2,',','.');
							$arrContabilidad[$contador]['sc_cuenta'] = trim($contable[8]);
							$arrContabilidad[$contador]['debhab'] = $debhab;
							$arrContabilidad[$contador]['monto'] = $monto;
							$contador++;
						}
					}
		    		echo generarJsonArreglo($arrContabilidad);
				}
			break;

			case "guardar_en_lote":
				$servicioCmp = new ServicioComprobanteContable();
				$arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
				$arrevento ['codusu']  = $_SESSION['la_logusr'];
				$arrevento ['codsis']  = $objetoJson->codsis;
				$arrevento ['evento']  = 'PROCESAR';
				$arrevento ['nomfisico']  = $objetoJson->nomven; 
				$arrevento ['desevetra'] = '';			
				$valido = $servicioCmp->guardarCmpConlot($_SESSION['la_empresa']['codemp'],$objetoJson,$arrevento);
				$resultado['mensaje'] = $servicioCmp->mensaje;  
				$resultado['valido']  = $valido;    		
				echo json_encode(array('raiz'=>$resultado));
				unset($servicioCmp);
				break;	
		}   
	}
}