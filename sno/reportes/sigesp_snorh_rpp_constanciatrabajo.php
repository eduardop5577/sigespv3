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
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";		
		print "</script>";		
	}

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$lb_valido=true;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_constanciatrabajo.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,680,13,$as_titulo); // Agregar el título
		if($as_fecha=="1")
		{
			$io_pdf->addText(512,750,8,date("d/m/Y")); // Agregar la Fecha
			$io_pdf->addText(518,743,7,date("h:i a")); // Agregar la Hora
		}
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	include("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
	$io_numero_letra= new class_numero_a_letra();
	//imprime numero con los valore por defecto
	//cambia a minusculas
	$io_numero_letra->setMayusculas(1);
	//cambia a femenino
	$io_numero_letra->setGenero(1);
	//cambia moneda
	$io_numero_letra->setMoneda("Bolivares");
	//cambia prefijo
	$io_numero_letra->setPrefijo("");
	//cambia sufijo
	$io_numero_letra->setSufijo("");
	//imprime numero con los cambios
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();
	require_once("../../shared/class_folder/evaluate_formula.php");
	$io_evaluate=new evaluate_formula();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<i>CONSTANCIA</i>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codcont=$io_fun_nomina->uf_obtenervalor_get("codcont","");
	$ls_codnom=$io_fun_nomina->uf_obtenervalor_get("codnom","");
	$li_rac=$io_fun_nomina->uf_obtenervalor_get("rac","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_fecha=$io_fun_nomina->uf_obtenervalor_get("fecha","");
	$ls_mesactual=$io_fun_nomina->uf_obtenervalor_get("mesactual","");
	$ls_anocurnom=$io_fun_nomina->uf_obtenervalor_get("anocurnom","");
	$ls_parametros=$io_fun_nomina->uf_obtenervalor_get("parametro","");
	$ls_anticipo=$io_fun_nomina->uf_obtenervalor_get("anticipo","");
	$arr_codper=explode ("-",$ls_parametros); 
	$li_totcodper=count((Array)$arr_codper);
	$li_mesanterior=(intval($ls_mesactual)-1);
	if($li_mesanterior==0)
	{
		$li_mesanterior=12;
		$ls_anocurnom=(intval($ls_anocurnom)-1);
	}
	$ls_mesanterior=str_pad($li_mesanterior,2,"0",0);
	global $ls_tiporeporte;
	
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte	
	if ($li_totcodper==1)
	{  
		if($lb_valido)
		{
			$lb_valido=$io_report->uf_constanciatrabajo_constancia($ls_codcont,$ls_codnom,$ls_codperdes,$ls_codperhas); // Obtenemos el detalle del reporte
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
			//
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			while ((!$io_report->rs_data->EOF)&&($lb_valido))
			{
				$ls_concont=$io_report->rs_data->fields["concont"];
				$li_tamletcont=$io_report->rs_data->fields["tamletcont"];
				$li_tamletpiecont=$io_report->rs_data->fields["tamletpiecont"];
				if($li_tamletpiecont=="")
				{
					$li_tamletpiecont=$li_tamletcont;
				}
				$li_intlincont=$io_report->rs_data->fields["intlincont"];
				$li_marinfcont=$io_report->rs_data->fields["marinfcont"];
				$li_marsupcont=$io_report->rs_data->fields["marsupcont"];
				$ls_titcont=$io_report->rs_data->fields["titcont"];
				$ls_piepagcont=$io_report->rs_data->fields["piepagcont"];
				$ls_piepagcont=$io_report->rs_data->fields["piepagcont"];
				$ls_consumcont=trim($io_report->rs_data->fields["consumcont"]);
				
				$ls_ente=$_SESSION["la_empresa"]["nombre"];
				$ld_fecha=date("d/m/Y");
				$ls_dia_act=substr($ld_fecha,0,2);
				$ls_mes_act=$io_fecha->uf_load_nombre_mes(substr($ld_fecha,3,2));
				$ls_ano_act=substr($ld_fecha,6,4);
				$io_pdf->ezSetCmMargins($li_marsupcont,$li_marinfcont,3,3); // Configuración de los margenes en centímetros
				uf_print_encabezado_pagina($ls_titcont,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la página
				$lb_valido=$io_report->uf_constanciatrabajo_personal($ls_codnom,$li_rac,$ls_codperdes,$ls_codperhas); // Obtenemos el detalle del reporte
				if($lb_valido)
				{
					while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
					{
						$ls_contenido="";
						$ls_contenido=$ls_concont;
						$li_confij=0;
						$li_convar=0;
						$ls_codper=$io_report->rs_detalle->fields["codper"];
						$ls_cedper=$io_report->rs_detalle->fields["cedper"];
						$ls_apeper=$io_report->rs_detalle->fields["apeper"];		
						$ls_nomper=$io_report->rs_detalle->fields["nomper"];		
						$ls_descar=$io_report->rs_detalle->fields["descar"];
						$ls_denominacioncargo=$io_report->rs_detalle->fields["descasicar"];		
						$ld_fecingper=$io_report->rs_detalle->fields["fecingper"];
						$ls_mes=$io_fecha->uf_load_nombre_mes(substr($ld_fecingper,5,2));
						$ls_fechaingreso="el ".substr($ld_fecingper,8,2)." de ".$ls_mes." de ".substr($ld_fecingper,0,4);
						$ld_fecegrper=$io_report->rs_detalle->fields["fecegrper"];
						$ls_mes=$io_fecha->uf_load_nombre_mes(substr($ld_fecegrper,5,2));
						$ls_fechaegreso="el ".substr($ld_fecegrper,8,2)." de ".$ls_mes." de ".substr($ld_fecegrper,0,4);
						$ls_dirper=$io_report->rs_detalle->fields["dirper"];		
						$ld_fecnacper=$io_funciones->uf_convertirfecmostrar($io_report->rs_detalle->fields["fecnacper"]);		
						$ld_fechaingreso_ddmmaaaa=$io_funciones->uf_convertirfecmostrar($io_report->rs_detalle->fields["fecingper"]);		
						$ls_edocivper=$io_report->rs_detalle->fields["edocivper"];	
						switch($ls_edocivper)
						{
							case "S": // Soltero
								$ls_edocivper="Soltero";
								break;
							case "C": // Casado
								$ls_edocivper="Casado";
								break;
							case "D": // Divociado
								$ls_edocivper="Divociado";
								break;
							case "V": // Viudo
								$ls_edocivper="Viudo";
								break;
						}
						$ls_nacper=$io_report->rs_detalle->fields["nacper"];
						switch($ls_nacper)
						{
							case "V": // Venezolano
								$ls_nacper="Venezolano";
								break;
							case "E": // Extranjero
								$ls_nacper="Extranjero";
								break;
						}
						$ls_tipnom=$io_report->rs_detalle->fields["tipnom"];	
						switch($ls_tipnom)
						{
							case "1": // Empleado Fijo
								$ls_tipnom="Empleado Fijo";
								break;
							case "2": // Empleado Contratado
								$ls_tipnom="Empleado Contratado";
								break;
							case "3": // Obrero Fijo
								$ls_tipnom="Obrero Fijo";
								break;
							case "4": // Obrero Contratado
								$ls_tipnom="Obrero Contratado";
								break;
							case "5": // Docente Fijo
								$ls_tipnom="Docente Fijo";
								break;
							case "6": // Docente Contratado
								$ls_tipnom="Docente Contratado";
								break;
							case "7": // Jubilado
								$ls_tipnom="Jubilado";
								break;
							case "8": // Comision de Servicios
								$ls_tipnom="Comision de Servicios";
								break;
							case "9": // Libre Nombramiento
								$ls_tipnom="Libre Nombramiento";
								break;
						}
						if($ls_tiporeporte==1)
						{
							$ls_prefijo="Bs.F.";
						}
						else
						{
							$ls_prefijo="Bs.";
						}
						$ls_telhabper=$io_report->rs_detalle->fields["telhabper"];	
						$ls_telmovper=$io_report->rs_detalle->fields["telmovper"];	
						$ls_desuniadm=$io_report->rs_detalle->fields["desuniadm"];	
						$li_horper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["horper"]);	
						$li_sueper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueper"]);		
						
						$ls_sueldo_sin_fomato=$io_report->rs_detalle->fields["sueper"];
						$li_sueldo_semanal=$ls_sueldo_sin_fomato/4;
						$li_sueldo_semanal=($li_sueldo_semanal/7)*30;
						$li_sueldo_semanal_num=$io_fun_nomina->uf_formatonumerico($li_sueldo_semanal);
						$io_numero_letra->setNumero($li_sueldo_semanal);
						$li_sueldo_semanal=$io_numero_letra->letra();
						$li_sueldo_semanal=$li_sueldo_semanal." (".$ls_prefijo." ".$li_sueldo_semanal_num.")";
						
						$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueper"]);
						$ls_sueper=$io_numero_letra->letra();
						$ls_sueper=$ls_sueper." (".$ls_prefijo." ".$li_sueper.")";
						$li_sueintper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueintper"]);	
						$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueintper"]);
						$ls_sueintper=$io_numero_letra->letra();
						$ls_sueintper=$ls_sueintper." (".$ls_prefijo." ".$li_sueintper.")";
						$li_salnorper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["salnorper"]);	
						$io_numero_letra->setNumero($io_report->rs_detalle->fields["salnorper"]);
						$ls_salnorper=$io_numero_letra->letra();
						$ls_salnorper=$ls_salnorper." (".$ls_prefijo." ".$li_salnorper.")";
						$li_sueproper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueproper"]);	
						$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueproper"]);
						$ls_sueproper=$io_numero_letra->letra();
						$ls_sueproper=$ls_sueproper." (".$ls_prefijo." ".$li_sueproper.")";
						$ls_desded=$io_report->rs_detalle->fields["desded"];	
						$ls_destipper=$io_report->rs_detalle->fields["destipper"];	
						$ls_fecjub=$io_report->rs_detalle->fields["fecjubper"];
						$ls_mes2=$io_fecha->uf_load_nombre_mes(substr($ls_fecjub,5,2));
						$ls_fecjub="el ".substr($ls_fecjub,8,2)." de ".$ls_mes2." de ".substr($ls_fecjub,0,4);						
						$li_sueintper_mensual=0;
						$li_sueproper_mensual=0;
						$li_salnorpermensual=0;
						$li_asignacion=0;
						$ls_gerencia=$io_report->rs_detalle->fields["denger"];
						$arrResultado=$io_report->uf_constanciatrabajo_integralpromedio_mensual($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom,$li_sueintper_mensual,
																							 	$li_sueproper_mensual,$li_salnorpermensual); // Obtenemos el detalle del reporte
						$li_sueintper_mensual=$arrResultado['ai_sueintper'];
						$li_sueproper_mensual=$arrResultado['ai_sueproper'];
						$li_salnorpermensual=$arrResultado['ai_salnorper'];
						$lb_valido=$arrResultado['lb_valido'];
						if($lb_valido)
						{
							$arrResultado=$io_report->uf_constanciatrabajo_conceptos_fijos_variable($ls_codper,$ls_mesanterior,$ls_anocurnom,$li_confij,$li_convar); // Obtenemos el detalle del reporte
							$li_confij=$arrResultado['ai_confij'];
							$li_convar=$arrResultado['ai_convar'];
							$lb_valido=$arrResultado['lb_valido'];
						}
						if($lb_valido)
						{
							$arrResultado=$io_report->uf_constanciatrabajo_asignacion_mensual($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom); // Obtenemos el detalle del reporte
							$li_asignacion=$arrResultado['ai_monto'];
							$lb_valido=$arrResultado['lb_valido'];
						}
						$li_sumaconcepto=0;
						if (($ls_consumcont<>'' )&&(($lb_valido)))
						{
							$arrResultado=$io_report->uf_constanciatrabajo_sumaconceptos($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom,$ls_consumcont); // Obtenemos el detalle del reporte
							$li_sumaconcepto=$arrResultado['ai_sumaconcepto'];
							$lb_valido=$arrResultado['lb_valido'];						
						}
						$io_numero_letra->setNumero($li_asignacion);
						$ls_asignacion=$io_numero_letra->letra();
						$li_asignacion=$io_fun_nomina->uf_formatonumerico($li_asignacion);
						$ls_asignacion=$ls_asignacion." (".$ls_prefijo." ".$li_asignacion.")";
						$li_fijo_variable= $li_confij+$li_convar;
						$io_numero_letra->setNumero($li_fijo_variable);
						$ls_fijo_variable=$io_numero_letra->letra();
						$li_fijo_variable=$io_fun_nomina->uf_formatonumerico($li_fijo_variable);
						$ls_fijo_variable=$ls_fijo_variable." (".$ls_prefijo." ".$li_fijo_variable.")";
						$io_numero_letra->setNumero($li_confij);
						$ls_confij=$io_numero_letra->letra();
						$li_confij=$io_fun_nomina->uf_formatonumerico($li_confij);
						$ls_confij=$ls_confij." (".$ls_prefijo." ".$li_confij.")";
						$io_numero_letra->setNumero($li_convar);
						$ls_convar=$io_numero_letra->letra();
						$li_convar=$io_fun_nomina->uf_formatonumerico($li_convar);
						$ls_convar=$ls_convar." (".$ls_prefijo." ".$li_convar.")";
						$li_sueintper_mensual_sinformato=$li_sueintper_mensual;
						$io_numero_letra->setNumero($li_sueintper_mensual);
						$ls_sueintper_mensual=$io_numero_letra->letra();
						$li_sueintper_mensual=$io_fun_nomina->uf_formatonumerico($li_sueintper_mensual);
						$ls_sueintper_mensual=$ls_sueintper_mensual." (".$ls_prefijo." ".$li_sueintper_mensual.")";
						$io_numero_letra->setNumero($li_sueproper_mensual);
						$ls_sueproper_mensual=$io_numero_letra->letra();
						$li_sueproper_mensual=$io_fun_nomina->uf_formatonumerico($li_sueproper_mensual);
						$ls_sueproper_mensual=$ls_sueproper_mensual." (".$ls_prefijo." ".$li_sueproper_mensual.")";
						
						$io_numero_letra->setNumero($li_salnorpermensual);
						$ls_salnorpermensual=$io_numero_letra->letra();
						$li_salnorpermensual=$io_fun_nomina->uf_formatonumerico($li_salnorpermensual);
						$ls_salnorpermensual=$ls_salnorpermensual." (".$ls_prefijo." ".$li_salnorpermensual.")";

						$io_numero_letra->setNumero($li_sumaconcepto);
						$ls_sumaconcepto=$io_numero_letra->letra();
						$li_sumaconcepto=$io_fun_nomina->uf_formatonumerico($li_sumaconcepto);
						$ls_sumaconcepto=$ls_sumaconcepto." (".$ls_prefijo." ".$li_sumaconcepto.")";
						if($li_rac=='1')
						{
							$io_numero_letra->setNumero($io_report->rs_detalle->fields["compen"]);
							$ls_compen=$io_numero_letra->letra();
							$li_compen=$ls_compen.'('.$ls_prefijo.' '.$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["compen"]).')';	
						}
						else
						{
							$li_compen= 0;
						}
						$ld_fecingnom=$io_report->rs_detalle->fields["fecingnom"];
						$ls_mes_nom=$io_fecha->uf_load_nombre_mes(substr($ld_fecingnom,5,2));
						$ls_fecingrnom="el ".substr($ld_fecingnom,8,2)." de ".$ls_mes_nom." de ".substr($ld_fecingnom,0,4);

						$ld_fecculcontr=$io_report->rs_detalle->fields["fecculcontr"];
						$ls_mes_nom=$io_fecha->uf_load_nombre_mes(substr($ld_fecculcontr,5,2));
						$ls_fecculcon="el ".substr($ld_fecculcontr,8,2)." de ".$ls_mes_nom." de ".substr($ld_fecculcontr,0,4);
						
						
						if($ls_anticipo=="0")
						{
							$ls_contenido=str_replace("\$ls_ente",$ls_ente,$ls_contenido);
							$ls_contenido=str_replace("\$ls_dia",$ls_dia_act,$ls_contenido);
							$ls_contenido=str_replace("\$ls_mes",$ls_mes_act,$ls_contenido);
							$ls_contenido=str_replace("\$ls_ano",$ls_ano_act,$ls_contenido);
							$ls_contenido=str_replace("\$ls_nombres",$ls_nomper,$ls_contenido);
							$ls_contenido=str_replace("\$ls_apellidos",$ls_apeper,$ls_contenido);
							$ls_contenido=str_replace("\$ls_cedula",$ls_cedper,$ls_contenido);
							$ls_contenido=str_replace("\$ls_cargo",$ls_descar,$ls_contenido);
							$ls_contenido=str_replace("\$li_sueldo",$ls_sueper,$ls_contenido);
							$ls_contenido=str_replace("\$ld_fecha_ingreso",$ls_fechaingreso,$ls_contenido);
							$ls_contenido=str_replace("\$ld_fecha_egreso",$ls_fechaegreso,$ls_contenido);
							$ls_contenido=str_replace("\$ls_direccion",$ls_dirper,$ls_contenido);
							$ls_contenido=str_replace("\$ld_fecha_nacimiento",$ld_fecnacper,$ls_contenido);
							$ls_contenido=str_replace("\$ld_ddmmaaaa_fecha_ingreso",$ld_fechaingreso_ddmmaaaa,$ls_contenido);
							$ls_contenido=str_replace("\$ls_edo_civil",$ls_edocivper,$ls_contenido);
							$ls_contenido=str_replace("\$ls_nacionalidad",$ls_nacper,$ls_contenido);
							$ls_contenido=str_replace("\$ls_telefono_hab",$ls_telhabper,$ls_contenido);
							$ls_contenido=str_replace("\$ls_telefono_mov",$ls_telmovper,$ls_contenido);
							$ls_contenido=str_replace("\$ls_unidad_administrativa",$ls_desuniadm,$ls_contenido);
							$ls_contenido=str_replace("\$li_horas_lab",$li_horper,$ls_contenido);
							$ls_contenido=str_replace("\$li_inte_sueldo",$ls_sueintper,$ls_contenido);
							$ls_contenido=str_replace("\$li_salario_normal",$ls_salnorper,$ls_contenido);						
							$ls_contenido=str_replace("\$li_prom_sueldo",$ls_sueproper,$ls_contenido);
							$ls_contenido=str_replace("\$ls_dedicacion",$ls_desded,$ls_contenido);
							$ls_contenido=str_replace("\$ls_tipo_personal",$ls_destipper,$ls_contenido);
							$ls_contenido=str_replace("\$ls_nomina_tipo",$ls_tipnom,$ls_contenido);
							$ls_contenido=str_replace("\$li_mensual_inte_sueldo",$ls_sueintper_mensual,$ls_contenido);
							$ls_contenido=str_replace("\$li_mensual_prom_sueldo",$ls_sueproper_mensual,$ls_contenido);
							$ls_contenido=str_replace("\$li_semanal_base_sueldo",$li_sueldo_semanal,$ls_contenido);
							$ls_contenido=str_replace("\$ls_fecjub",$ls_fecjub,$ls_contenido);
							$ls_contenido=str_replace("\$ls_gerencia",$ls_gerencia,$ls_contenido);
							$ls_contenido=str_replace("\$li_numerosueldo",$ls_sueldo_sin_fomato,$ls_contenido);
							$ls_contenido=str_replace("\$li_num_sue_integral",$li_sueintper_mensual_sinformato,$ls_contenido);
							$ls_contenido=str_replace("\$li_normalmensual",$ls_salnorpermensual,$ls_contenido);
							$ls_contenido=str_replace("\$ls_denomin_cargo",$ls_denominacioncargo,$ls_contenido);
							$ls_contenido=str_replace("\$li_compen",$li_compen,$ls_contenido);
							$ls_contenido=str_replace("\$ls_fecingrnom",$ls_fecingrnom,$ls_contenido);
							$ls_contenido=str_replace("\$ls_fecculcon",$ls_fecculcon,$ls_contenido);
							$ls_contenido=str_replace("\$li_ult_concepto_fijo",$ls_confij,$ls_contenido);
							$ls_contenido=str_replace("\$li_pro_concepto_variable_3",$ls_convar,$ls_contenido);
							$ls_contenido=str_replace("\$li_sum_concepto_fijo_variable",$ls_fijo_variable,$ls_contenido);
							$ls_contenido=str_replace("\$li_sum_concepto_asignacion",$ls_asignacion,$ls_contenido);
							$ls_contenido=str_replace("\$li_consum",$ls_sumaconcepto,$ls_contenido);							
							$li_long=strlen($ls_contenido);
							$a=0;
							$ls_pal_bus=array();
							$ls_pal_bus= explode("];",$ls_contenido);
							$ls_cont=count((Array)$ls_pal_bus)-1;
							for($li_x=1;$li_x<=$ls_cont;$li_x++)
							{
								//Creando variable que encierra la formula
								$li_posicion1=strpos($ls_contenido,"FORMULA[",$a);
								if ($li_posicion1!="")
								{
									$li_posicion2=strpos($ls_contenido,"];",$a);
									$li_posicion2=$li_posicion2-$li_posicion1;
									$ls_var_formula=substr($ls_contenido,$li_posicion1,$li_posicion2+2);
									//Creando variable que encierra la formula
									
									$li_pos_for=strpos($ls_contenido,"FORMULA[",$a);
									$li_pos_for=$li_pos_for+8;
									$li_pos_forhas=strpos($ls_contenido,"];",$a);
									$li_pos_forhas=$li_pos_forhas-$li_pos_for;
									$ls_formula_agre=substr($ls_contenido,$li_pos_for,$li_pos_forhas);
									$ls_formula_agre=str_replace(",",".",$ls_formula_agre);
									$arrResultado=$io_evaluate->uf_evaluar_nomina($ls_formula_agre,$li_resultado);
									$li_resultado=$arrResultado['result'];
									$lb_correcto=$arrResultado['lb_valido'];
									$li_resul_numero=$io_fun_nomina->uf_formatonumerico($li_resultado);
									$io_numero_letra->setNumero($li_resultado);
									$ls_resultado=$io_numero_letra->letra();
									$ls_resultado=$ls_resultado." (".$ls_prefijo." ".$li_resul_numero.")";
									$ls_contenido=str_replace("$ls_var_formula",$ls_resultado,$ls_contenido);
								}
								$a++;
							}
							$li_long=strlen($ls_contenido);
							$a=0;
							$ls_pal_bus=array();
							$ls_pal_bus= explode("];",$ls_contenido);
							$ls_cont=count((Array)$ls_pal_bus);
							for($li_x=0;$li_x<$ls_cont;$li_x++)
							{
								$li_posconcepto1=strpos($ls_contenido,"NOMBRE_CONCEPTO[",$a);
								if ($li_posconcepto1!="")
								{
									//Creando variable que encierra la formula
									$li_posconcepto2=strpos($ls_contenido,"];",$a);
									$li_posconcepto2=$li_posconcepto2-$li_posconcepto1;
									$ls_var_concepto=substr($ls_contenido,$li_posconcepto1,$li_posconcepto2+2);
									//Creando variable que encierra la formula
									
									//Creando variable que maneja el codigo del concepto
									$li_posix=strpos($ls_contenido,"NOMBRE_CONCEPTO[",$a);
									$li_posix=$li_posix+16;
									$li_posiy=strpos($ls_contenido,"];",$a);
									$li_posiy=$li_posiy-$li_posix;
									$ls_concepto=substr($ls_contenido,$li_posix,$li_posiy);
									$ls_codconc=$ls_concepto;
									$ls_nombre='';
									$li_monto=0;
									//Creando variable que maneja el codigo del concepto
									$arrResultado=$io_report->uf_constanciatrabajo_concepto_mensual($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom,$ls_codconc,$ls_nombre,$li_monto); // Obtenemos el detalle del reporte
									$ls_nombre=$arrResultado['as_nombre'];
									$li_monto=$arrResultado['ai_monto'];
									$lb_valido=$arrResultado['lb_valido'];
									$li_monto=abs($li_monto);
									$li_monto=$io_fun_nomina->uf_formatonumerico($li_monto);
									if(trim($ls_nombre)!='')
									{
										$ls_concepto_find=$ls_nombre."  --  ".$li_monto;
										$ls_contenido=str_replace("$ls_var_concepto",$ls_concepto_find,$ls_contenido);
									}
									else
									{
										$ls_concepto_find='';
										$ls_contenido=str_replace("$ls_var_concepto",$ls_concepto_find,$ls_contenido);
									}
								}
								$a++;
							}
							$io_pdf->ezText($ls_contenido,$li_tamletcont,array('justification' =>'full','spacing' =>$li_intlincont));
							$li_pos=($li_marinfcont*10)*(72/25.4);
							$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$ls_piepagcont,'center');
							$li_pos=$li_pos-$li_tamletpiecont;
							$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');
							$li_pos=$li_pos-$li_tamletpiecont;
							$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');
						}
						else
						{
							$arrResultado=$io_report->uf_constanciatrabajo_anticipos($ls_codper,$rs_data); // Obtenemos el detalle del reporte
							$rs_data=$arrResultado['rs_data'];
							$lb_valido=$arrResultado['lb_valido'];
							if($lb_valido)
							{
								while(!$rs_data->EOF)
								{
									$ls_codant=$rs_data->fields["codant"];
									$ls_estant=$rs_data->fields["estant"];
									switch($ls_estant)
									{
										case "R": // Registro
											$ls_estant="Registrado";
											break;
										case "A": // Aprobada
											$ls_estant="Aprobado";
											break;
										case "C": // Contabilizada
											$ls_estant="Contabilizado";
											break;
										case "X": // Anulada
											$ls_estant="Anulado";
											break;
									}
									$ld_fecant=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecant"]);
									$li_monpreant=$rs_data->fields["monpreant"];
									$li_monintant=$rs_data->fields["monintant"];
									$li_monantant=$rs_data->fields["monantant"];
									$li_monantint=$rs_data->fields["monantint"];
									$li_monant=$rs_data->fields["monant"];
									$li_monint=$rs_data->fields["monint"];
									$ls_motant=$rs_data->fields["motant"];
									$ls_obsant=$rs_data->fields["obsant"];
									
									$io_numero_letra->setNumero($li_monpreant);
									$ls_monpreant=$io_numero_letra->letra();
									$li_monpreant=$io_fun_nomina->uf_formatonumerico($li_monpreant);
									$ls_monpreant=$ls_monpreant." (".$ls_prefijo." ".$li_monpreant.")";
									$io_numero_letra->setNumero($li_monintant);
									$ls_monintant=$io_numero_letra->letra();
									$li_monintant=$io_fun_nomina->uf_formatonumerico($li_monintant);
									$ls_monintant=$ls_monintant." (".$ls_prefijo." ".$li_monintant.")";
									$io_numero_letra->setNumero($li_monantant);
									$ls_monantant=$io_numero_letra->letra();
									$li_monantant=$io_fun_nomina->uf_formatonumerico($li_monantant);
									$ls_monantant=$ls_monantant." (".$ls_prefijo." ".$li_monantant.")";
									$io_numero_letra->setNumero($li_monantint);
									$ls_monantint=$io_numero_letra->letra();
									$li_monantint=$io_fun_nomina->uf_formatonumerico($li_monantint);
									$ls_monantint=$ls_monantint." (".$ls_prefijo." ".$li_monantint.")";
									$io_numero_letra->setNumero($li_monant);
									$ls_monant=$io_numero_letra->letra();
									$li_monant=$io_fun_nomina->uf_formatonumerico($li_monant);
									$ls_monant=$ls_monant." (".$ls_prefijo." ".$li_monant.")";
									$io_numero_letra->setNumero($li_monint);
									$ls_monint=$io_numero_letra->letra();
									$li_monint=$io_fun_nomina->uf_formatonumerico($li_monint);
									$ls_monint=$ls_monint." (".$ls_prefijo." ".$li_monint.")";

									$ls_anticipo=$ls_contenido;
									$ls_anticipo=str_replace("\$ls_ente",$ls_ente,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_dia",$ls_dia_act,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_mes",$ls_mes_act,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_ano",$ls_ano_act,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_nombres",$ls_nomper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_apellidos",$ls_apeper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_cedula",$ls_cedper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_cargo",$ls_descar,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_sueldo",$ls_sueper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ld_fecha_ingreso",$ls_fechaingreso,$ls_anticipo);
									$ls_anticipo=str_replace("\$ld_fecha_egreso",$ls_fechaegreso,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_direccion",$ls_dirper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ld_fecha_nacimiento",$ld_fecnacper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ld_ddmmaaaa_fecha_ingreso",$ld_fechaingreso_ddmmaaaa,$ls_anticipo);									
									$ls_anticipo=str_replace("\$ls_edo_civil",$ls_edocivper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_nacionalidad",$ls_nacper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_telefono_hab",$ls_telhabper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_telefono_mov",$ls_telmovper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_unidad_administrativa",$ls_desuniadm,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_horas_lab",$li_horper,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_inte_sueldo",$ls_sueintper,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_salario_normal",$ls_salnorper,$ls_anticipo);						
									$ls_anticipo=str_replace("\$li_prom_sueldo",$ls_sueproper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_dedicacion",$ls_desded,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_tipo_personal",$ls_destipper,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_nomina_tipo",$ls_tipnom,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_mensual_inte_sueldo",$ls_sueintper_mensual,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_mensual_prom_sueldo",$ls_sueproper_mensual,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_fecjub",$ls_fecjub,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_gerencia",$ls_gerencia,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_codant",$ls_codant,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_estant",$ls_estant,$ls_anticipo);
									$ls_anticipo=str_replace("\$ld_fecant",$ld_fecant,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_monpreant",$ls_monpreant,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_monintant",$ls_monintant,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_monantant",$ls_monantant,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_monantint",$ls_monantint,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_monant",$ls_monant,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_monint",$ls_monint,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_motant",$ls_motant,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_obsant",$ls_obsant,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_numerosueldo",$ls_sueldo_sin_fomato,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_num_sue_integral",$li_sueintper_mensual_sinformato,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_normalmensual",$ls_salnorpermensual,$ls_anticipo);
									$ls_anticipo=str_replace("\$ls_denomin_cargo",$ls_denominacioncargo,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_ult_concepto_fijo",$ls_confij,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_pro_concepto_variable_3",$ls_convar,$ls_anticipo);
									$ls_anticipo=str_replace("\$li_sum_concepto_fijo_variable",$ls_fijo_variable,$ls_anticipo);
									
									$li_long=strlen($ls_anticipo);
									$a=0;
									$ls_pal_bus=array();
									$ls_pal_bus= explode("];",$ls_anticipo);
									$ls_cont=count((Array)$ls_pal_bus)-1;
									for($li_x=1;$li_x<=$ls_cont;$li_x++)
									{
										//Creando variable que encierra la formula
										$li_posicion1=strpos($ls_anticipo,"FORMULA[",$a);
										$li_posicion2=strpos($ls_anticipo,"];",$a);
										$li_posicion2=$li_posicion2-$li_posicion1;
										$ls_var_formula=substr($ls_anticipo,$li_posicion1,$li_posicion2+2);
										//Creando variable que encierra la formula
										
										$li_pos_for=strpos($ls_anticipo,"FORMULA[",$a);
										$li_pos_for=$li_pos_for+8;
										$li_pos_forhas=strpos($ls_anticipo,"];",$a);
										$li_pos_forhas=$li_pos_forhas-$li_pos_for;
										$ls_formula_agre=substr($ls_anticipo,$li_pos_for,$li_pos_forhas);
										$ls_formula_agre=str_replace(",",".",$ls_formula_agre);
										$arrResultado=$io_evaluate->uf_evaluar_nomina($ls_formula_agre,$li_resultado);
										$li_resultado=$arrResultado['result'];
										$lb_correcto=$arrResultado['lb_valido'];
										$li_resul_numero=$io_fun_nomina->uf_formatonumerico($li_resultado);
										$io_numero_letra->setNumero($li_resultado);
										$ls_resultado=$io_numero_letra->letra();
										$ls_resultado=$ls_resultado." (".$ls_prefijo." ".$li_resul_numero.")";
										$ls_anticipo=str_replace("$ls_var_formula",$ls_resultado,$ls_anticipo);
										$a++;
									}
									$li_long=strlen($ls_anticipo);
									$a=0;
									$ls_pal_bus=array();
									$ls_pal_bus= explode("];",$ls_anticipo);
									$ls_cont=count((Array)$ls_pal_bus);
									for($li_x=0;$li_x<$ls_cont;$li_x++)
									{
										$li_posconcepto1=strpos($ls_contenido,"NOMBRE_CONCEPTO[",$a);
										if ($li_posconcepto1!="")
										{
											//Creando variable que encierra la formula
											$li_posconcepto2=strpos($ls_contenido,"];",$a);
											$li_posconcepto2=$li_posconcepto2-$li_posconcepto1;
											$ls_var_concepto=substr($ls_contenido,$li_posconcepto1,$li_posconcepto2+2);
											//Creando variable que encierra la formula
											
											//Creando variable que maneja el codigo del concepto
											$li_posix=strpos($ls_contenido,"NOMBRE_CONCEPTO[",$a);
											$li_posix=$li_posix+16;
											$li_posiy=strpos($ls_contenido,"];",$a);
											$li_posiy=$li_posiy-$li_posix;
											$ls_concepto=substr($ls_contenido,$li_posix,$li_posiy);
											$ls_codconc=$ls_concepto;
											//Creando variable que maneja el codigo del concepto
											$ls_nombre='';
											$li_monto=0;
											$arrResultado=$io_report->uf_constanciatrabajo_concepto_mensual($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom,$ls_codconc,$ls_nombre,$li_monto); // Obtenemos el detalle del reporte
											$ls_nombre=$arrResultado['as_nombre'];
											$li_monto=$arrResultado['ai_monto'];
											$lb_valido=$arrResultado['lb_valido'];
											$li_monto=abs($li_monto);
											$li_monto=$io_fun_nomina->uf_formatonumerico($li_monto);
											if(trim($ls_nombre)!='')
											{
												$ls_concepto_find=$ls_nombre."  --  ".$li_monto;
												$ls_contenido=str_replace("$ls_var_concepto",$ls_concepto_find,$ls_contenido);
											}
											else
											{
												$ls_concepto_find='';
												$ls_contenido=str_replace("$ls_var_concepto",$ls_concepto_find,$ls_contenido);
											}
										}
										$a++;
									}
									$io_pdf->ezText($ls_anticipo,$li_tamletcont,array('justification' =>'full','spacing' =>$li_intlincont));
									$li_pos=($li_marinfcont*10)*(72/25.4);
														
									$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$ls_piepagcont,'center');
									$li_pos=$li_pos-$li_tamletpiecont;
									$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');
									$li_pos=$li_pos-$li_tamletpiecont;
									$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');
									$rs_data->MoveNext();
									if(!$rs_data->EOF)
									{
										$io_pdf->ezNewPage(); // Insertar una nueva página
									}
								}
							}
						}
						$io_report->rs_detalle->MoveNext();
						if((!$io_report->rs_detalle->EOF)&&($ls_anticipo=="0"))
						{
							$io_pdf->ezNewPage(); // Insertar una nueva página
						}
					}
				}
				$io_report->rs_data->MoveNext();
			}
			if($lb_valido) // Si no ocurrio ningún error
			{
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else  // Si hubo algún error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				print(" close();");
				print("</script>");		
			}
			unset($io_pdf);
		}
		unset($io_report);
		unset($io_funciones);
		unset($io_fun_nomina);
	}
	else
	{
	    $li_total=count((Array)$arr_codper);
		$codperdes1="";			
		$codperhas1="";	
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra				  
		for ($i=1;$i<$li_total;$i++) 
		{   
		    $codperdes1=$arr_codper[$i]; 		
			$codperhas1=$arr_codper[$i];
			$lb_valido=$io_report->uf_constanciatrabajo_constancia($ls_codcont,$ls_codnom,$codperdes1,$codperhas1);
			if($lb_valido==false) // Existe algún error ó no hay registros
			{
				print("<script language=JavaScript>");
				print(" alert('No hay nada que Reportar');"); 
				print(" close();");
				print("</script>");
			}
			else
			{
				while ((!$io_report->rs_data->EOF)&&($lb_valido))
				{
					$ls_concont=$io_report->rs_data->fields["concont"];
					$li_tamletcont=$io_report->rs_data->fields["tamletcont"];
					$li_tamletpiecont=$io_report->rs_data->fields["tamletpiecont"];
					if($li_tamletpiecont=="")
					{
						$li_tamletpiecont=$li_tamletcont;
					}
					$li_intlincont=$io_report->rs_data->fields["intlincont"];
					$li_marinfcont=$io_report->rs_data->fields["marinfcont"];
					$li_marsupcont=$io_report->rs_data->fields["marsupcont"];
					$ls_titcont=$io_report->rs_data->fields["titcont"];
					$ls_piepagcont=$io_report->rs_data->fields["piepagcont"];
					$ls_consumcont=trim($io_report->rs_data->fields["consumcont"]);
					$ls_ente=$_SESSION["la_empresa"]["nombre"];
					$ld_fecha=date("d/m/Y");
					$ls_dia_act=substr($ld_fecha,0,2);
					$ls_mes_act=$io_fecha->uf_load_nombre_mes(substr($ld_fecha,3,2));
					$ls_ano_act=substr($ld_fecha,6,4);
					$io_pdf->ezSetCmMargins($li_marsupcont,$li_marinfcont,3,3); // Configuración de los margenes en centímetros
					uf_print_encabezado_pagina($ls_titcont,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la página
					$lb_valido=$io_report->uf_constanciatrabajo_personal($ls_codnom,$li_rac,$codperdes1,$codperhas1); // Obtenemos el detalle del reporte
					if($lb_valido)
					{
						while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
						{						
							$ls_contenido="";
							$ls_contenido=$ls_concont;
							$ls_codper=$io_report->rs_detalle->fields["codper"];
							$ls_cedper=$io_report->rs_detalle->fields["cedper"];
							$ls_apeper=$io_report->rs_detalle->fields["apeper"];		
							$ls_nomper=$io_report->rs_detalle->fields["nomper"];		
							$ls_descar=$io_report->rs_detalle->fields["descar"];
							$ls_denominacioncargo=$io_report->rs_detalle->fields["descasicar"];			
							$ld_fecingper=$io_report->rs_detalle->fields["fecingper"];
							$ls_mes=$io_fecha->uf_load_nombre_mes(substr($ld_fecingper,5,2));
							$ls_fechaingreso="el ".substr($ld_fecingper,8,2)." de ".$ls_mes." de ".substr($ld_fecingper,0,4);
							$ld_fecegrper=$io_report->rs_detalle->fields["fecegrper"];
							$ls_mes=$io_fecha->uf_load_nombre_mes(substr($ld_fecegrper,5,2));
							$ls_fechaegreso="el ".substr($ld_fecegrper,8,2)." de ".$ls_mes." de ".substr($ld_fecegrper,0,4);
							$ls_dirper=$io_report->rs_detalle->fields["dirper"];		
							$ld_fecnacper=$io_funciones->uf_convertirfecmostrar($io_report->rs_detalle->fields["fecnacper"]);		
							$ld_fechaingreso_ddmmaaaa=$io_funciones->uf_convertirfecmostrar($io_report->rs_detalle->fields["fecingper"]);		
							$ls_edocivper=$io_report->rs_detalle->fields["edocivper"];	
							switch($ls_edocivper)
							{
								case "S": // Soltero
									$ls_edocivper="Soltero";
									break;
								case "C": // Casado
									$ls_edocivper="Casado";
									break;
								case "D": // Divociado
									$ls_edocivper="Divociado";
									break;
								case "V": // Viudo
									$ls_edocivper="Viudo";
									break;
							}
							$ls_nacper=$io_report->rs_detalle->fields["nacper"];
							switch($ls_nacper)
							{
								case "V": // Venezolano
									$ls_nacper="Venezolano";
									break;
								case "E": // Extranjero
									$ls_nacper="Extranjero";
									break;
							}
							$ls_tipnom=$io_report->rs_detalle->fields["tipnom"];	
							switch($ls_tipnom)
							{
								case "1": // Empleado Fijo
									$ls_tipnom="Empleado Fijo";
									break;
								case "2": // Empleado Contratado
									$ls_tipnom="Empleado Contratado";
									break;
								case "3": // Obrero Fijo
									$ls_tipnom="Obrero Fijo";
									break;
								case "4": // Obrero Contratado
									$ls_tipnom="Obrero Contratado";
									break;
								case "5": // Docente Fijo
									$ls_tipnom="Docente Fijo";
									break;
								case "6": // Docente Contratado
									$ls_tipnom="Docente Contratado";
									break;
								case "7": // Jubilado
									$ls_tipnom="Jubilado";
									break;
								case "8": // Comision de Servicios
									$ls_tipnom="Comision de Servicios";
									break;
								case "9": // Libre Nombramiento
									$ls_tipnom="Libre Nombramiento";
									break;
							}
							if($ls_tiporeporte==1)
							{
								$ls_prefijo="Bs.F.";
							}
							else
							{
								$ls_prefijo="Bs.";
							}
							$ls_sueldo_sin_fomato=$io_report->rs_detalle->fields["sueper"];
							$ls_telhabper=$io_report->rs_detalle->fields["telhabper"];	
							$ls_telmovper=$io_report->rs_detalle->fields["telmovper"];	
							$ls_desuniadm=$io_report->rs_detalle->fields["desuniadm"];	
							$li_horper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["horper"]);	
							$li_sueper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueper"]);		
							$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueper"]);
							$ls_sueper=$io_numero_letra->letra();
							$ls_sueper=$ls_sueper." (".$ls_prefijo." ".$li_sueper.")";
							$li_sueintper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueintper"]);	
							$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueintper"]);
							$ls_sueintper=$io_numero_letra->letra();
							$ls_sueintper=$ls_sueintper." (".$ls_prefijo." ".$li_sueintper.")";
							$li_salnorper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["salnorper"]);	
							$io_numero_letra->setNumero($io_report->rs_detalle->fields["salnorper"]);
							$ls_salnorper=$io_numero_letra->letra();
							$ls_salnorper=$ls_salnorper." (".$ls_prefijo." ".$li_salnorper.")";
							$li_sueproper=$io_fun_nomina->uf_formatonumerico($io_report->rs_detalle->fields["sueproper"]);	
							$io_numero_letra->setNumero($io_report->rs_detalle->fields["sueproper"]);
							$ls_sueproper=$io_numero_letra->letra();
							$ls_sueproper=$ls_sueproper." (".$ls_prefijo." ".$li_sueproper.")";
							$ls_desded=$io_report->rs_detalle->fields["desded"];	
							$ls_destipper=$io_report->rs_detalle->fields["destipper"];	
							$ls_fecjub=$io_report->rs_detalle->fields["fecjubper"];
							$ls_mes2=$io_fecha->uf_load_nombre_mes(substr($ls_fecjub,5,2));
							$ls_fecjub="el ".substr($ls_fecjub,8,2)." de ".$ls_mes2." de ".substr($ls_fecjub,0,4);						
							$li_sueintper_mensual=0;
							$li_sueproper_mensual=0;
							$li_salnorpermensual=0;
							$li_asignacion=0;							
							$ls_gerencia=$io_report->rs_detalle->fields["denger"];
							$arrResultado=$io_report->uf_constanciatrabajo_integralpromedio_mensual($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom,$li_sueintper_mensual,
																								 $li_sueproper_mensual,$li_salnorpermensual); // Obtenemos el detalle del reporte
							$li_sueintper_mensual=$arrResultado['ai_sueintper'];
							$li_sueproper_mensual=$arrResultado['ai_sueproper'];
							$li_salnorpermensual=$arrResultado['ai_salnorper'];
							$lb_valido=$arrResultado['lb_valido'];
							$li_confij=0;
							$li_convar=0;
							if($lb_valido)
							{
								$arrResultado=$io_report->uf_constanciatrabajo_conceptos_fijos_variable($ls_codper,$ls_mesanterior,$ls_anocurnom,$li_confij,$li_convar); // Obtenemos el detalle del reporte
								$li_confij=$arrResultado['ai_confij'];
								$li_convar=$arrResultado['ai_convar'];
								$lb_valido=$arrResultado['lb_valido'];
							}
							if($lb_valido)
							{
								$arrResultado=$io_report->uf_constanciatrabajo_asignacion_mensual($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom); // Obtenemos el detalle del reporte
								$li_asignacion=$arrResultado['ai_monto'];
								$lb_valido=$arrResultado['lb_valido'];
							}
							$li_sumaconcepto=0;
							if (($ls_consumcont<>'' )&&(($lb_valido)))
							{
								$arrResultado=$io_report->uf_constanciatrabajo_sumaconceptos($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom,$ls_consumcont); // Obtenemos el detalle del reporte
								$li_sumaconcepto=$arrResultado['ai_sumaconcepto'];
								$lb_valido=$arrResultado['lb_valido'];						
							}
							$io_numero_letra->setNumero($li_asignacion);
							$ls_asignacion=$io_numero_letra->letra();
							$li_asignacion=$io_fun_nomina->uf_formatonumerico($li_asignacion);
							$ls_asignacion=$ls_asignacion." (".$ls_prefijo." ".$li_asignacion.")";
							$li_fijo_variable= $li_confij+$li_convar;
							$io_numero_letra->setNumero($li_fijo_variable);
							$ls_fijo_variable=$io_numero_letra->letra();
							$li_fijo_variable=$io_fun_nomina->uf_formatonumerico($li_fijo_variable);
							$ls_fijo_variable=$ls_fijo_variable." (".$ls_prefijo." ".$li_fijo_variable.")";
							$io_numero_letra->setNumero($li_confij);
							$ls_confij=$io_numero_letra->letra();
							$li_confij=$io_fun_nomina->uf_formatonumerico($li_confij);
							$ls_confij=$ls_confij." (".$ls_prefijo." ".$li_confij.")";
							$io_numero_letra->setNumero($li_convar);
							$ls_convar=$io_numero_letra->letra();
							$li_convar=$io_fun_nomina->uf_formatonumerico($li_convar);
							$ls_convar=$ls_convar." (".$ls_prefijo." ".$li_convar.")";
							$li_sueintper_mensual_sinformato=$li_sueintper_mensual;
							$io_numero_letra->setNumero($li_sueintper_mensual);
							$ls_sueintper_mensual=$io_numero_letra->letra();
							$li_sueintper_mensual=$io_fun_nomina->uf_formatonumerico($li_sueintper_mensual);
							$ls_sueintper_mensual=$ls_sueintper_mensual." (".$ls_prefijo." ".$li_sueintper_mensual.")";
							$io_numero_letra->setNumero($li_sueproper_mensual);
							$ls_sueproper_mensual=$io_numero_letra->letra();
							$li_sueproper_mensual=$io_fun_nomina->uf_formatonumerico($li_sueproper_mensual);
							$ls_sueproper_mensual=$ls_sueproper_mensual." (".$ls_prefijo." ".$li_sueproper_mensual.")";
							$io_numero_letra->setNumero($li_salnorpermensual);
							$ls_salnorpermensual=$io_numero_letra->letra();
							$li_salnorpermensual=$io_fun_nomina->uf_formatonumerico($li_salnorpermensual);
							$ls_salnorpermensual=$ls_salnorpermensual." (".$ls_prefijo." ".$li_salnorpermensual.")";	
							$io_numero_letra->setNumero($li_sumaconcepto);
							$ls_sumaconcepto=$io_numero_letra->letra();
							$li_sumaconcepto=$io_fun_nomina->uf_formatonumerico($li_sumaconcepto);
							$ls_sumaconcepto=$ls_sumaconcepto." (".$ls_prefijo." ".$li_sumaconcepto.")";
							if($ls_anticipo=="0")
							{
								$ls_contenido=str_replace("\$ls_ente",$ls_ente,$ls_contenido);
								$ls_contenido=str_replace("\$ls_dia",$ls_dia_act,$ls_contenido);
								$ls_contenido=str_replace("\$ls_mes",$ls_mes_act,$ls_contenido);
								$ls_contenido=str_replace("\$ls_ano",$ls_ano_act,$ls_contenido);
								$ls_contenido=str_replace("\$ls_nombres",$ls_nomper,$ls_contenido);
								$ls_contenido=str_replace("\$ls_apellidos",$ls_apeper,$ls_contenido);
								$ls_contenido=str_replace("\$ls_cedula",$ls_cedper,$ls_contenido);
								$ls_contenido=str_replace("\$ls_cargo",$ls_descar,$ls_contenido);
								$ls_contenido=str_replace("\$li_sueldo",$ls_sueper,$ls_contenido);
								$ls_contenido=str_replace("\$ld_fecha_ingreso",$ls_fechaingreso,$ls_contenido);
								$ls_contenido=str_replace("\$ld_fecha_egreso",$ls_fechaegreso,$ls_contenido);
								$ls_contenido=str_replace("\$ls_direccion",$ls_dirper,$ls_contenido);
								$ls_contenido=str_replace("\$ld_fecha_nacimiento",$ld_fecnacper,$ls_contenido);
								$ls_contenido=str_replace("\$ld_ddmmaaaa_fecha_ingreso",$ld_fechaingreso_ddmmaaaa,$ls_contenido);								
								$ls_contenido=str_replace("\$ls_edo_civil",$ls_edocivper,$ls_contenido);
								$ls_contenido=str_replace("\$ls_nacionalidad",$ls_nacper,$ls_contenido);
								$ls_contenido=str_replace("\$ls_telefono_hab",$ls_telhabper,$ls_contenido);
								$ls_contenido=str_replace("\$ls_telefono_mov",$ls_telmovper,$ls_contenido);
								$ls_contenido=str_replace("\$ls_unidad_administrativa",$ls_desuniadm,$ls_contenido);
								$ls_contenido=str_replace("\$li_horas_lab",$li_horper,$ls_contenido);
								$ls_contenido=str_replace("\$li_inte_sueldo",$ls_sueintper,$ls_contenido);
								$ls_contenido=str_replace("\$li_salario_normal",$ls_salnorper,$ls_contenido);						
								$ls_contenido=str_replace("\$li_prom_sueldo",$ls_sueproper,$ls_contenido);
								$ls_contenido=str_replace("\$ls_dedicacion",$ls_desded,$ls_contenido);
								$ls_contenido=str_replace("\$ls_tipo_personal",$ls_destipper,$ls_contenido);
								$ls_contenido=str_replace("\$ls_nomina_tipo",$ls_tipnom,$ls_contenido);
								$ls_contenido=str_replace("\$li_mensual_inte_sueldo",$ls_sueintper_mensual,$ls_contenido);
								$ls_contenido=str_replace("\$li_mensual_prom_sueldo",$ls_sueproper_mensual,$ls_contenido);
								$ls_contenido=str_replace("\$ls_fecjub",$ls_fecjub,$ls_contenido);
								$ls_contenido=str_replace("\$ls_gerencia",$ls_gerencia,$ls_contenido);
								$ls_contenido=str_replace("\$li_numerosueldo",$ls_sueldo_sin_fomato,$ls_contenido);
								$ls_contenido=str_replace("\$li_num_sue_integral",$li_sueintper_mensual_sinformato,$ls_contenido);
								$ls_contenido=str_replace("\$li_normalmensual",$ls_salnorpermensual,$ls_contenido);
								$ls_contenido=str_replace("\$ls_denomin_cargo",$ls_denominacioncargo,$ls_contenido);
								$ls_contenido=str_replace("\$li_ult_concepto_fijo",$ls_confij,$ls_contenido);
								$ls_contenido=str_replace("\$li_pro_concepto_variable_3",$ls_convar,$ls_contenido);
								$ls_contenido=str_replace("\$li_sum_concepto_fijo_variable",$ls_fijo_variable,$ls_contenido);
								$ls_contenido=str_replace("\$li_sum_concepto_asignacion",$ls_asignacion,$ls_contenido);
								$ls_contenido=str_replace("\$li_consum",$ls_sumaconcepto,$ls_contenido);							
								
								$li_long=strlen($ls_contenido);
								$a=0;
								$ls_pal_bus=array();
								$ls_pal_bus= explode("];",$ls_contenido);
								$ls_cont=count((Array)$ls_pal_bus)-1;
								for($li_x=1;$li_x<=$ls_cont;$li_x++)
								{
									//Creando variable que encierra la formula
									$li_posicion1=strpos($ls_contenido,"FORMULA[",$a);
									$li_posicion2=strpos($ls_contenido,"];",$a);
									$li_posicion2=$li_posicion2-$li_posicion1;
									$ls_var_formula=substr($ls_contenido,$li_posicion1,$li_posicion2+2);
									//Creando variable que encierra la formula
									
									$li_pos_for=strpos($ls_contenido,"FORMULA[",$a);
									$li_pos_for=$li_pos_for+8;
									$li_pos_forhas=strpos($ls_contenido,"];",$a);
									$li_pos_forhas=$li_pos_forhas-$li_pos_for;
									$ls_formula_agre=substr($ls_contenido,$li_pos_for,$li_pos_forhas);
									$ls_formula_agre=str_replace(",",".",$ls_formula_agre);
									$arrResultado=$io_evaluate->uf_evaluar_nomina($ls_formula_agre,$li_resultado);
									$li_resultado=$arrResultado['result'];
									$lb_correcto=$arrResultado['lb_valido'];
									$li_resul_numero=$io_fun_nomina->uf_formatonumerico($li_resultado);
									$io_numero_letra->setNumero($li_resultado);
									$ls_resultado=$io_numero_letra->letra();
									$ls_resultado=$ls_resultado." (".$ls_prefijo." ".$li_resul_numero.")";
									$ls_contenido=str_replace("$ls_var_formula",$ls_resultado,$ls_contenido);
									$a++;
								}
								$li_long=strlen($ls_contenido);
								$a=0;
								$ls_pal_bus=array();
								$ls_pal_bus= explode("];",$ls_contenido);
								$ls_cont=count((Array)$ls_pal_bus);
								for($li_x=0;$li_x<$ls_cont;$li_x++)
								{
									$li_posconcepto1=strpos($ls_contenido,"NOMBRE_CONCEPTO[",$a);
									if ($li_posconcepto1!="")
									{
										//Creando variable que encierra la formula
										$li_posconcepto2=strpos($ls_contenido,"];",$a);
										$li_posconcepto2=$li_posconcepto2-$li_posconcepto1;
										$ls_var_concepto=substr($ls_contenido,$li_posconcepto1,$li_posconcepto2+2);
										//Creando variable que encierra la formula
										
										//Creando variable que maneja el codigo del concepto
										$li_posix=strpos($ls_contenido,"NOMBRE_CONCEPTO[",$a);
										$li_posix=$li_posix+16;
										$li_posiy=strpos($ls_contenido,"];",$a);
										$li_posiy=$li_posiy-$li_posix;
										$ls_concepto=substr($ls_contenido,$li_posix,$li_posiy);
										$ls_codconc=$ls_concepto;
										//Creando variable que maneja el codigo del concepto
										$ls_nombre='';
										$li_monto=0;
										$arrResultado=$io_report->uf_constanciatrabajo_concepto_mensual($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom,$ls_codconc,$ls_nombre,$li_monto); // Obtenemos el detalle del reporte
										$ls_nombre=$arrResultado['as_nombre'];
										$li_monto=$arrResultado['ai_monto'];
										$lb_valido=$arrResultado['lb_valido'];
										$li_monto=abs($li_monto);
										$li_monto=$io_fun_nomina->uf_formatonumerico($li_monto);
										if(trim($ls_nombre)!='')
										{
											$ls_concepto_find=$ls_nombre."  --  ".$li_monto;
											$ls_contenido=str_replace("$ls_var_concepto",$ls_concepto_find,$ls_contenido);
										}
										else
										{
											$ls_concepto_find='';
											$ls_contenido=str_replace("$ls_var_concepto",$ls_concepto_find,$ls_contenido);
										}
									}
									$a++;
								}
								$io_pdf->ezText($ls_contenido,$li_tamletcont,array('justification' =>'full','spacing' =>$li_intlincont));
								$li_pos=($li_marinfcont*10)*(72/25.4);
													
								$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$ls_piepagcont,'center');
								$li_pos=$li_pos-$li_tamletpiecont;
								$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');
								$li_pos=$li_pos-$li_tamletpiecont;
								$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');
							}
							else
							{
								$arrResultado=$io_report->uf_constanciatrabajo_anticipos($ls_codper,$rs_data); // Obtenemos el detalle del reporte
								$rs_data=$arrResultado['rs_data'];
								$lb_valido=$arrResultado['lb_valido'];
								if($lb_valido)
								{
									while(!$rs_data->EOF)
									{
										$ls_codant=$rs_data->fields["codant"];
										$ls_estant=$rs_data->fields["estant"];
										switch($ls_estant)
										{
											case "R": // Registro
												$ls_estant="Registrado";
												break;
											case "A": // Aprobada
												$ls_estant="Aprobado";
												break;
											case "C": // Contabilizada
												$ls_estant="Contabilizado";
												break;
											case "X": // Anulada
												$ls_estant="Anulado";
												break;
										}
										$ld_fecant=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecant"]);
										$li_monpreant=$rs_data->fields["monpreant"];
										$li_monintant=$rs_data->fields["monintant"];
										$li_monantant=$rs_data->fields["monantant"];
										$li_monantint=$rs_data->fields["monantint"];
										$li_monant=$rs_data->fields["monant"];
										$li_monint=$rs_data->fields["monint"];
										$ls_motant=$rs_data->fields["motant"];
										$ls_obsant=$rs_data->fields["obsant"];
										
										$io_numero_letra->setNumero($li_monpreant);
										$ls_monpreant=$io_numero_letra->letra();
										$li_monpreant=$io_fun_nomina->uf_formatonumerico($li_monpreant);
										$ls_monpreant=$ls_monpreant." (".$ls_prefijo." ".$li_monpreant.")";
										$io_numero_letra->setNumero($li_monintant);
										$ls_monintant=$io_numero_letra->letra();
										$li_monintant=$io_fun_nomina->uf_formatonumerico($li_monintant);
										$ls_monintant=$ls_monintant." (".$ls_prefijo." ".$li_monintant.")";
										$io_numero_letra->setNumero($li_monantant);
										$ls_monantant=$io_numero_letra->letra();
										$li_monantant=$io_fun_nomina->uf_formatonumerico($li_monantant);
										$ls_monantant=$ls_monantant." (".$ls_prefijo." ".$li_monantant.")";
										$io_numero_letra->setNumero($li_monantint);
										$ls_monantint=$io_numero_letra->letra();
										$li_monantint=$io_fun_nomina->uf_formatonumerico($li_monantint);
										$ls_monantint=$ls_monantint." (".$ls_prefijo." ".$li_monantint.")";
										$io_numero_letra->setNumero($li_monant);
										$ls_monant=$io_numero_letra->letra();
										$li_monant=$io_fun_nomina->uf_formatonumerico($li_monant);
										$ls_monant=$ls_monant." (".$ls_prefijo." ".$li_monant.")";
										$io_numero_letra->setNumero($li_monint);
										$ls_monint=$io_numero_letra->letra();
										$li_monint=$io_fun_nomina->uf_formatonumerico($li_monint);
										$ls_monint=$ls_monint." (".$ls_prefijo." ".$li_monint.")";
	
										$ls_anticipo=$ls_contenido;
										$ls_anticipo=str_replace("\$ls_ente",$ls_ente,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_dia",$ls_dia_act,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_mes",$ls_mes_act,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_ano",$ls_ano_act,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_nombres",$ls_nomper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_apellidos",$ls_apeper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_cedula",$ls_cedper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_cargo",$ls_descar,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_sueldo",$ls_sueper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ld_fecha_ingreso",$ls_fechaingreso,$ls_anticipo);
										$ls_anticipo=str_replace("\$ld_fecha_egreso",$ls_fechaegreso,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_direccion",$ls_dirper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ld_fecha_nacimiento",$ld_fecnacper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ld_ddmmaaaa_fecha_ingreso",$ld_fechaingreso_ddmmaaaa,$ls_anticipo);										
										$ls_anticipo=str_replace("\$ls_edo_civil",$ls_edocivper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_nacionalidad",$ls_nacper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_telefono_hab",$ls_telhabper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_telefono_mov",$ls_telmovper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_unidad_administrativa",$ls_desuniadm,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_horas_lab",$li_horper,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_inte_sueldo",$ls_sueintper,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_salario_normal",$ls_salnorper,$ls_anticipo);						
										$ls_anticipo=str_replace("\$li_prom_sueldo",$ls_sueproper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_dedicacion",$ls_desded,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_tipo_personal",$ls_destipper,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_nomina_tipo",$ls_tipnom,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_mensual_inte_sueldo",$ls_sueintper_mensual,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_mensual_prom_sueldo",$ls_sueproper_mensual,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_fecjub",$ls_fecjub,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_gerencia",$ls_gerencia,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_codant",$ls_codant,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_estant",$ls_estant,$ls_anticipo);
										$ls_anticipo=str_replace("\$ld_fecant",$ld_fecant,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_monpreant",$ls_monpreant,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_monintant",$ls_monintant,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_monantant",$ls_monantant,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_monantint",$ls_monantint,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_monant",$ls_monant,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_monint",$ls_monint,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_motant",$ls_motant,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_obsant",$ls_obsant,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_numerosueldo",$ls_sueldo_sin_fomato,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_num_sue_integral",$li_sueintper_mensual_sinformato,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_normalmensual",$ls_salnorpermensual,$ls_anticipo);
										$ls_anticipo=str_replace("\$ls_denomin_cargo",$ls_denominacioncargo,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_ult_concepto_fijo",$ls_confij,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_pro_concepto_variable_3",$ls_convar,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_sum_concepto_fijo_variable",$ls_fijo_variable,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_sum_concepto_asignacion",$ls_asignacion,$ls_anticipo);
										$ls_anticipo=str_replace("\$li_consum",$ls_sumaconcepto,$ls_anticipo);							
										
										$li_long=strlen($ls_anticipo);
										$a=0;
										$ls_pal_bus=array();
										$ls_pal_bus= explode("];",$ls_anticipo);
										$ls_cont=count((Array)$ls_pal_bus)-1;
										for($li_x=1;$li_x<=$ls_cont;$li_x++)
										{
											//Creando variable que encierra la formula
											$li_posicion1=strpos($ls_anticipo,"FORMULA[",$a);
											$li_posicion2=strpos($ls_anticipo,"];",$a);
											$li_posicion2=$li_posicion2-$li_posicion1;
											$ls_var_formula=substr($ls_anticipo,$li_posicion1,$li_posicion2+2);
											//Creando variable que encierra la formula
											
											$li_pos_for=strpos($ls_anticipo,"FORMULA[",$a);
											$li_pos_for=$li_pos_for+8;
											$li_pos_forhas=strpos($ls_anticipo,"];",$a);
											$li_pos_forhas=$li_pos_forhas-$li_pos_for;
											$ls_formula_agre=substr($ls_anticipo,$li_pos_for,$li_pos_forhas);
											$ls_formula_agre=str_replace(",",".",$ls_formula_agre);
											$arrResultado=$io_evaluate->uf_evaluar_nomina($ls_formula_agre,$li_resultado);
											$li_resultado=$arrResultado['result'];
											$lb_correcto=$arrResultado['lb_valido'];
											$li_resul_numero=$io_fun_nomina->uf_formatonumerico($li_resultado);
											$io_numero_letra->setNumero($li_resultado);
											$ls_resultado=$io_numero_letra->letra();
											$ls_resultado=$ls_resultado." (".$ls_prefijo." ".$li_resul_numero.")";
											$ls_anticipo=str_replace("$ls_var_formula",$ls_resultado,$ls_anticipo);
											$a++;
										}
										$li_long=strlen($ls_anticipo);
										$a=0;
										$ls_pal_bus=array();
										$ls_pal_bus= explode("];",$ls_anticipo);
										$ls_cont=count((Array)$ls_pal_bus);
										for($li_x=0;$li_x<$ls_cont;$li_x++)
										{
											$li_posconcepto1=strpos($ls_contenido,"NOMBRE_CONCEPTO[",$a);
											if ($li_posconcepto1!="")
											{
												//Creando variable que encierra la formula
												$li_posconcepto2=strpos($ls_contenido,"];",$a);
												$li_posconcepto2=$li_posconcepto2-$li_posconcepto1;
												$ls_var_concepto=substr($ls_contenido,$li_posconcepto1,$li_posconcepto2+2);
												//Creando variable que encierra la formula
												
												//Creando variable que maneja el codigo del concepto
												$li_posix=strpos($ls_contenido,"NOMBRE_CONCEPTO[",$a);
												$li_posix=$li_posix+16;
												$li_posiy=strpos($ls_contenido,"];",$a);
												$li_posiy=$li_posiy-$li_posix;
												$ls_concepto=substr($ls_contenido,$li_posix,$li_posiy);
												$ls_codconc=$ls_concepto;
												//Creando variable que maneja el codigo del concepto
												$ls_nombre='';
												$li_monto=0;
												$arrResultado=$io_report->uf_constanciatrabajo_concepto_mensual($ls_codnom,$ls_codper,$ls_mesanterior,$ls_anocurnom,$ls_codconc,$ls_nombre,$li_monto); // Obtenemos el detalle del reporte
												$ls_nombre=$arrResultado['as_nombre'];
												$li_monto=$arrResultado['ai_monto'];
												$lb_valido=$arrResultado['lb_valido'];
												$li_monto=abs($li_monto);
												$li_monto=$io_fun_nomina->uf_formatonumerico($li_monto);
												if(trim($ls_nombre)!='')
												{
													$ls_concepto_find=$ls_nombre."  --  ".$li_monto;
													$ls_contenido=str_replace("$ls_var_concepto",$ls_concepto_find,$ls_contenido);
												}
												else
												{
													$ls_concepto_find='';
													$ls_contenido=str_replace("$ls_var_concepto",$ls_concepto_find,$ls_contenido);
												}
											}
											$a++;
										}
										$io_pdf->ezText($ls_anticipo,$li_tamletcont,array('justification' =>'full','spacing' =>$li_intlincont));
										$li_pos=($li_marinfcont*10)*(72/25.4);
															
										$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$ls_piepagcont,'center');
										$li_pos=$li_pos-$li_tamletpiecont;
										$li_texto=$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');
										$li_pos=$li_pos-$li_tamletpiecont;
										$io_pdf->addTextWrap(50,$li_pos,500,$li_tamletpiecont,$li_texto,'center');
										$rs_data->MoveNext();
										if(!$rs_data->EOF)
										{
											$io_pdf->ezNewPage(); // Insertar una nueva página
										}
									}
								}
							}						
							$io_report->rs_detalle->MoveNext();
							if((!$io_report->rs_detalle->EOF)&&($ls_anticipo=="0"))
							{
								$io_pdf->ezNewPage(); // Insertar una nueva página
							}
						}
					}
					$io_report->rs_data->MoveNext();
				}
			}// fin del else
			if ((($i+1)<$li_total)&&($ls_anticipo=="0"))
			{
				$io_pdf->ezNewPage(); // Insertar una nueva página
			}
		}// fin del for($i=1;$i<=$li_total;$i++)
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);				
	}//fin del else
?> 