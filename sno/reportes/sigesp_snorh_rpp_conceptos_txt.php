<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();   
	ini_set('memory_limit','512M');
	ini_set('max_execution_time','0');

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_nomina,$as_periodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_nomina // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo.". ".$as_nomina.". ".$as_periodo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_conceptos.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codconcdes=$io_fun_nomina->uf_obtenervalor_get("codconcdes","");
	$ls_codconchas=$io_fun_nomina->uf_obtenervalor_get("codconchas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_tipconc=$io_fun_nomina->uf_obtenervalor_get("tipconc","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","1");
	$ls_personaldes=$io_fun_nomina->uf_obtenervalor_get("personaldes","");
	$ls_personalhas=$io_fun_nomina->uf_obtenervalor_get("personalhas","");
	$ls_tiporeporte=$io_fun_nomina->uf_obtenervalor_get("tiporeporte",0);
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("codsubnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("codsubnomhas","");
	$ls_anocurper=$io_fun_nomina->uf_obtenervalor_get("year","");
	
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$ls_titulo="CONSOLIDADO DE CONCEPTOS";
	$ls_nomina="Nómina:  Desde ".$ls_codnomdes."  Hasta ".$ls_codnomhas."";
	$ls_periodo="Período:  Desde ".$ls_codperdes." Hasta ".$ls_codperhas."";
	$lb_valido=uf_insert_seguridad($ls_titulo." TXT",$ls_nomina,$ls_periodo); // Seguridad de Reporte
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_consolidadoconcepto_conceptos($ls_codnomdes,$ls_codnomhas,$ls_codconcdes,$ls_codconchas,
																$ls_codperdes,$ls_codperhas,$ls_tipconc,$ls_conceptocero,
																$ls_personaldes,$ls_personalhas,$ls_subnomdes,$ls_subnomhas); // Cargar el DS con los datos de la cabecera del reporte
	}
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		//-------formato para el reporte----------------------------------------------------------
		$li_totrow=$io_report->DS->getRowCount("codconc");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			if($li_totrow>0)
			{	
				$ls_nombrearchivo="../txt/general/listado_conceptos.txt";
				//Chequea si existe el archivo.
				if (file_exists("$ls_nombrearchivo"))
				{
					if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
					{
						$lb_valido = false;
					}
					else
					{
						$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
					}
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
				}
				$ls_encabezado="CONCEPTO;CEDULA;APELLIDOS;NOMBRE;MONTO"."\r\n";
				@fwrite($ls_creararchivo,$ls_encabezado);
				$li_totrow=$io_report->DS->getRowCount("codconc");
				for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
				{
					$ls_codconc=$io_report->DS->data["codconc"][$li_i];
					$lb_valido=$io_report->uf_consolidadoconcepto_personal($ls_codnomdes,$ls_codnomhas,$ls_codconc,$ls_codperdes,$ls_codperhas,$ls_conceptocero,$ls_personaldes,$ls_personalhas,
																		   $ls_subnomdes,$ls_subnomhas,$ls_orden,$ls_anocurper); // Obtenemos el detalle del reporte
					if($lb_valido)
					{
						$li_montot=0;
						$li_totrow_det=$io_report->DS_detalle->getRowCount("cedper");
						for($li_s=1;(($li_s<=$li_totrow_det)&&($lb_valido));$li_s++)
						{
							$ls_cedper=$io_report->DS_detalle->data["cedper"][$li_s];
							$ls_apeper=$io_report->DS_detalle->data["apeper"][$li_s];
							$ls_nomper=$io_report->DS_detalle->data["nomper"][$li_s];
							$li_valsal=$io_report->DS_detalle->data["total"][$li_s];
							$ls_cadena=$ls_codconc.";".$ls_cedper.";".$ls_apeper.";".$ls_nomper.";".$li_valsal."\r\n";
							if ($ls_creararchivo)  //Chequea que el archivo este abierto				
							{
								if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
								{
									$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
									$lb_valido = false;
								}
							}
							else
							{
								$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
								$lb_valido = false;
							}
						}
					}
				}
			}
		}
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}/// fin de else // Imprimimos el reporte
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 
