<?php
/***********************************************************************************
* @fecha de modificacion: 07/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	session_start();
	require_once("../../class_folder/dao/sigesp_srh_c_seccion.php");
    require_once("../../class_folder/utilidades/class_funciones_srh.php");
	$io_sec=new sigesp_srh_c_seccion('../../../');
	$io_fun_srh=new class_funciones_srh('../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_d_seccion.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];
	$ls_logusr=$_SESSION["la_logusr"];
	
	
    $ls_salida="";
	
	if (array_key_exists("txtcoddep",$_POST))
	{
		$ls_coddep=$_POST["txtcoddep"];
	}
	else
	{
		$ls_coddep="";
    }
	
	if (array_key_exists("txtcodsec",$_POST))
	{
		$ls_codsec=utf8_decode ($_POST["txtcodsec"]);
	}
	else
	{
		$ls_codsec="";
    }
	if (array_key_exists("txtdensec",$_POST))
	{
		$ls_densec=utf8_decode ($_POST["txtdensec"]);
	}
	else
	{
		$ls_densec="";
    }
	
	
	if (isset($_GET['valor']))
	{
		
		$evento=$_GET['valor'];
		
			if($evento=="guardar")
			{
					$lb_existe= $io_sec->uf_srh_select_seccion($ls_codsec);
					if ($lb_existe)
					{
						
							$lb_update=$io_sec->uf_srh_update_seccion($ls_codsec,$ls_densec,$ls_coddep,$la_seguridad);
							
							if ($lb_update)
							{
								echo utf8_decode("La Secci&oacute;n fue Actulizada");
							}
							
					}
					else
					{
						$lb_guardar= $io_sec->uf_srh_insert_seccion($ls_codsec,$ls_densec,$ls_coddep,$la_seguridad);
						 if ($lb_guardar)
							{
								echo utf8_decode("La Secci&oacute;n fue registrada");
							}

					
					}
					
			}
			elseif($evento=="eliminar")
			{
					list($lb_valido,$lb_existe)=$io_sec->uf_srh_delete_seccion($ls_codsec,$la_seguridad);
			
					if($lb_existe)
					{
							
					}	
					else
					{	
						if($lb_valido)
							{
								echo utf8_decode("La Secci&oacute;n fue Eliminada");
								
							}
							else
							{
							
								
							}
					
					}
					
					
			}
			elseif($evento=="existe")
			{
					$lb_existe= $io_sec->uf_srh_select_seccion($ls_codsec);
					if ($lb_existe)
					{
						
					 echo utf8_decode("La Secci&oacute;n ya existe");
					}
					
					
			}
			
			elseif($evento=="createXML")
			{
                    $ls_codsec="%%";
	                $ls_densec="%%";
					$ls_coddep="%%";
					header('Content-type:text/xml');
					print $io_sec->uf_srh_buscar_seccion($ls_codsec, $ls_densec,$ls_coddep);
					
					
			}
			elseif($evento=="buscar")
			{
					$ls_codsec="%".utf8_encode($_REQUEST['txtcodsec'])."%";
	                $ls_densec="%".utf8_encode($_REQUEST['txtdensec'])."%";
					$ls_coddep="%".utf8_encode($_REQUEST['txtcoddep'])."%";
									
					header('Content-type:text/xml');
					print $io_sec->uf_srh_buscar_seccion($ls_codsec, $ls_densec,$ls_coddep);
					
					
					
					
			}
	}
	
?>