<?php
/***********************************************************************************
* @Clase para Manejar el Escritorio del sistema según la permisología del usuario
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
if (($_POST['objdata']) && ($sessionvalida))
{

	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_derechosusuario.php');
        require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_include.php");        
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_funciones_db.php');
	
	$objdata = str_replace('\\','',$_POST['objdata']);	
	$objdata = json_decode($objdata,false);	
	$objDerechosUsuario    = new DerechosUsuario();
	$objDerechosUsuario->codemp=$_SESSION['la_empresa']['codemp'];
	$objDerechosUsuario->codusu=$_SESSION['la_codusu'];
	$evento = $objdata->operacion;
	switch ($evento)
	{
		case 'escritorio':
			$datos = $objDerechosUsuario->obtenerEscritorioUsuario();
			if (!$datos->EOF)
			{
				$varJson=generarJson($datos);
				echo $varJson;	
						
			}
			else 
			{	
				$arreglo[0]['mensaje'] = obtenerMensaje('SESION_EXPIRADA'); 
				$arreglo[0]['valido']  = false;
				$respuesta  = array('raiz'=>$arreglo);
				$respuesta  = json_encode($respuesta);
				echo $respuesta;
			}
			$datos->close();
		break;
		
		case 'cabecera':
			$continuar=true;
			$mensaje='';
                        $io_include=new sigesp_include();
                        $io_connect=$io_include->uf_conectar();
                        //$io_connect->debug=true;
                        $io_function_db=new class_funciones_db($io_connect);
                        
			switch ($objdata->codsis)
			{
				case 'SSS':
					$existe = $io_function_db->uf_select_column('sss_usuarios','estblocon');
					break;
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2013_05_03'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sigesp_empresa','reucon');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_21'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sigesp_empresa','nroconreu');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_22'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_table('sss_usuariosdetalle');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_23'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sss_derechos_usuarios','codintper');
					if (($existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2017_03_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_table('sss_firmantesdinamicos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_01_01'; 
						$continuar=false;  
					}
				break;
				
				case 'SCB':
					$existe = $io_function_db->uf_select_table('scb_medidas');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2013_06_03'; 
						$continuar=false;  
					}
				break;
				
				case 'MIS':
					$existe = $io_function_db->uf_select_column('sigesp_cmp','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_03'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('spg_dt_cmp','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_04'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('scg_dt_cmp','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_05'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('spi_dt_cmp','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_06'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('cxp_rd','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_07'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('cxp_rd_spg','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_08'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('cxp_rd_scg','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_09'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('cxp_rd_cargos','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_10'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('cxp_rd_deducciones','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_11'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('scb_movbco','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_16'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('scb_movbco_scg','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_17'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('scb_movbco_spg','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_18'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('scb_movbco_spi','codcencos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2015_11_19'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sno_nomina','recdoccaunom');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2016_02_04'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sno_hnomina','recdoccaunom');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2016_02_05'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sno_thnomina','recdoccaunom');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2016_02_06'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sno_nomina','tipdoccaunom');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2016_02_07'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sno_hnomina','tipdoccaunom');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2016_02_08'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sno_thnomina','tipdoccaunom');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2016_02_09'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sss_derechos_usuarios','codintper');
					if (($existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2017_03_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sno_dt_spg','codcla');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2017_04_05'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sno_rd','codcla');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2017_04_06'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sno_rd','codcla');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2017_04_06'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sep_solicitud','numsolini');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2018_06_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_table('mis_sigesp_cxc');
					if (($existe)&&($continuar))
					{
						$existe = $io_function_db->uf_select_column('mis_sigesp_cxc','codestpro1');
						if ((!$existe)&&($continuar))
						{
								$mensaje .= '->Debe Ejecutar el Release 2018_06_02'; 
								$continuar=false;  
						}
					}
					$existe = $io_function_db->uf_select_column('sigesp_cmp','numconcom');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_06_01'; 
						$continuar=false;  
					}
				break;
				
				case 'SPG':
					$existe = $io_function_db->uf_select_column('sigesp_cmp_md','codusu');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2016_12_04'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sss_derechos_usuarios','codintper');
					if (($existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2017_03_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sigesp_empresa','filindspg');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2018_04_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_table('sigesp_prefijos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2018_09_03'; 
						$continuar=false;  
					}                        
                                        $existe = selectConfig2('SPG','RELEASE','01_1');		
					if ((!$existe)&&($continuar))
					{
                                            $mensaje .= '->Debe Ejecutar el Release 2020_SPG_01 ';	
                                            $continuar=false;   
                                        }
					$existe = $io_function_db->uf_select_table('spg_sigeproden_proyecto');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2022_01_01'; 
						$continuar=false;  
					}                                        
					$existe = $io_function_db->uf_select_table('spg_dt_sigeproden_proyecto');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2022_01_02'; 
						$continuar=false;  
					}                                        
					$existe = $io_function_db->uf_select_type_columna('spg_dt_sigeproden_proyecto','tascam','numeric');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2022_01_04'; 
						$continuar=false;  
					}
				break;
				
				case 'SCG':
					$existe = $io_function_db->uf_select_column('sss_derechos_usuarios','codintper');
					if (($existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2017_03_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sigesp_empresa','costo');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2018_02_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_table('sigesp_prefijos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2018_09_03'; 
						$continuar=false;  
					}                                        
					$existe = $io_function_db->uf_select_column('sigesp_empresa','estcommas');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_07_02'; 
						$continuar=false;  
					}
				break;
				
				case 'RPC':
					$existe = $io_function_db->uf_select_column('sss_derechos_usuarios','codintper');
					if (($existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2017_03_01'; 
						$continuar=false;  
					}
					switch($_SESSION["ls_gestor"])
					{
						case "MYSQLT":
							$existe =$io_function_db->uf_select_type_columna('rpc_beneficiario','dirbene','longtext');		
						 break;
			
						case "MYSQLI":
							$existe =$io_function_db->uf_select_type_columna('rpc_beneficiario','dirbene','longtext');		
						 break;
							   
						case "POSTGRES":
							$existe =$io_function_db->uf_select_type_columna('rpc_beneficiario','dirbene','text');
															
						break;  				  
					}	
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2017_05_01'; 
						$continuar=false;  
					}
				break;
				
				case 'CFG':
					$existe = $io_function_db->uf_select_column('sigesp_empresa','costo');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2018_02_01'; 
						$continuar=false;  
					}
					$existe = selectConfig2('CFG','RELEASE','2018_05_01');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2018_05_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_table('sigesp_prefijos');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2018_09_03'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sigesp_empresa','estconcom');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_06_02'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sigesp_empresa','nroinicom');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_06_03'; 
						$continuar=false;  
					}
					$existe =$io_function_db->uf_select_type_columna('soc_servicios','denser','text');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_07_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sigesp_empresa','estcommas');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_07_02'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_table('sigesp_clasificador_economico');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_11_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('scg_casa_presu','cueclaeco');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_11_02'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('scg_casa_presu','cueoncop');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_11_03'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('spg_cuentas','cueclaeco');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_11_05'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('spi_cuentas','cueclaeco');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_11_06'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('sigesp_moneda','desmon');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2019_12_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('scb_ctabanco','codmon');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2021_09_01'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_type_columna('sigesp_dt_moneda','tascam1','numeric');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2022_01_03'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_column('spg_unidadadministrativa','resuniadm');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2022_05_02'; 
						$continuar=false;  
					}
					$existe = $io_function_db->uf_select_type_columna('sigesp_unidad_tributaria','decnro','character varying');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2022_08_02'; 
						$continuar=false;  
					}
					break;
                                        
				case 'SEP':
					$existe = $io_function_db->uf_select_column('sep_solicitud','codmon');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2022_12_01'; 
						$continuar=false;  
					}
				break;
                                        
				case 'SOC':
					$existe = $io_function_db->uf_select_column('soc_ordencompra','monsubtotdiv');
					if ((!$existe)&&($continuar))
					{
						$mensaje .= '->Debe Ejecutar el Release 2022_12_02'; 
						$continuar=false;  
					}
				break;
				
			}
			unset($io_include);
			unset($io_function_db);
			if($continuar)
			{
				$objDerechosUsuario->codsis=$objdata->codsis;
				$datos = $objDerechosUsuario->obtenerSistemaUsuario();				
				if (!$datos->EOF)
				{
					$arreglo[0]['nomsis'] = $datos->fields['nomsis']; 
					$arreglo[0]['nomusu']  = $datos->fields['nomusu']; 
					$arreglo[0]['apeusu'] = $datos->fields['apeusu']; 
					$arreglo[0]['fecha']  =  date("d/m/Y")." ".date("h:i a "); 
					$arreglo[0]['inactivo']  = $datos->fields['inactivo']; 
					$arreglo[0]['valido']  = $datos->fields['valido']; 
					//$varJson  = array('raiz'=>$arreglo);
					//$varJson  = json_encode($varJson);
					
					$varJson=generarJson($datos,true,false);
					echo $varJson;				
				}
				else 
				{	
					$arreglo[0]['mensaje'] = obtenerMensaje('SESION_EXPIRADA'); 
					$arreglo[0]['valido']  = false;
					$respuesta  = array('raiz'=>$arreglo);
					$respuesta  = json_encode($respuesta);
					echo $respuesta;
				}
				$datos->close();
			}
			else
			{
				$arreglo[0]['mensaje'] = $mensaje; 
				$arreglo[0]['valido']  = false;
				$respuesta  = array('raiz'=>$arreglo);
				$respuesta  = json_encode($respuesta);
				echo $respuesta;
			}
		break;
	}
	unset($objDerechosUsuario);
}
?>