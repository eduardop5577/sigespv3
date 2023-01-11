<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();   
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 25/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		$ls_descripcion="Generó el Reporte ARC en Excel";
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_arc.php",$ls_descripcion);
		return $lb_valido;
	}
	//------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "cxp_arc.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();				
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="COMPROBANTE DE RETENCIONES VARIAS";
	$ls_titulo2="DEL IMPUESTO SOBRE LA RENTA Bs.";
	$ls_titulo3="(EXCEPTO SUELDOS, SALARIOS Y DEMÁS REMUNERACIONES SIMILARES A PERSONAS NATURALES RESIDENTES)";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_tipo=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_coddes=$io_fun_cxp->uf_obtenervalor_get("codprobendes","");
	$ls_codhas=$io_fun_cxp->uf_obtenervalor_get("codprobenhas","");
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad(); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_arc_cabecera($ls_coddes,$ls_codhas,$ls_tipo); // Cargar el DS con los datos de la cabecera del reporte
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
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('12');
		$lo_encabezado2= &$lo_libro->addformat();
		$lo_encabezado2->set_bold();
		$lo_encabezado2->set_font("Verdana");
		$lo_encabezado2->set_align('center');
		$lo_encabezado2->set_size('10');
		$lo_encabezado3= &$lo_libro->addformat();
		$lo_encabezado3->set_bold();
		$lo_encabezado3->set_font("Verdana");
		$lo_encabezado3->set_align('left');
		$lo_encabezado3->set_size('8');
		$lo_encabezado4= &$lo_libro->addformat();
		$lo_encabezado4->set_bold();
		$lo_encabezado4->set_font("Verdana");
		$lo_encabezado4->set_align('center');
		$lo_encabezado4->set_size('8');
		$lo_normal= &$lo_libro->addformat();
		$lo_normal->set_font("Verdana");
		$lo_normal->set_align('left');
		$lo_normal->set_size('9');
		$lo_normal2= &$lo_libro->addformat();
		$lo_normal2->set_font("Verdana");
		$lo_normal2->set_align('left');
		$lo_normal2->set_size('8');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_text_wrap();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$lo_dataesp= &$lo_libro->addformat();
		$lo_dataesp->set_font("Verdana");
		$lo_dataesp->set_align('left');
		$lo_dataesp->set_size('8');
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$lo_dataright= &$lo_libro->addformat(array("num_format"=> "#,##0.00"));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		$lo_hoja->set_column(0,0,15);
		$lo_hoja->set_column(1,1,4);
		$lo_hoja->set_column(2,2,15);
		$lo_hoja->set_column(3,3,8);
		$lo_hoja->set_column(4,4,15);
		$lo_hoja->set_column(5,5,8);
		$lo_hoja->set_column(6,6,13);
		$lo_hoja->set_column(7,7,8);
		$lo_hoja->set_column(8,8,25);
		$lo_hoja->set_column(9,9,50);
		$lo_hoja->set_column(10,10,15);
		$lo_hoja->set_column(11,11,20);
		$lo_hoja->set_column(12,12,15);
		$lo_hoja->set_column(13,13,15);
		$lo_hoja->set_column(14,14,15);
		$lo_hoja->set_column(15,15,15);
		$lo_hoja->set_column(16,16,15);
		$lo_hoja->set_column(17,17,15);
		$lo_hoja->set_column(18,18,15);
		$lo_hoja->set_column(19,19,15);
		$lo_hoja->set_column(20,20,15);
		$li_totrow=$io_report->DS->getRowCount("tipproben");

		$li_pos=6;
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			if($li_i > 1)
			{
				$lo_hoja->write($li_postit,6,$ls_titulo,$lo_encabezado);
				$lo_hoja->write($li_postit+1,6,$ls_titulo2,$lo_encabezado);
				$lo_hoja->write($li_postit+2,6,$ls_titulo3,$lo_encabezado2);
			}
			else
			{
				$lo_hoja->write(1,6,$ls_titulo,$lo_encabezado);
				$lo_hoja->write(2,6,$ls_titulo2,$lo_encabezado);
				$lo_hoja->write(3,6,$ls_titulo3,$lo_encabezado2);
			}
			$li_total_pagado=0;
			$li_total_retencion=0;
			$li_total_impuesto=0;
			$ls_tipproben=$io_report->DS->data["tipproben"][$li_i];
			$ls_nombre_emp=str_pad($_SESSION["la_empresa"]["nombre"],100," ");
			$ls_rif_emp=$_SESSION["la_empresa"]["rifemp"];
			$ls_nit_emp=$_SESSION["la_empresa"]["nitemp"];
			$ls_direccion_emp=$_SESSION["la_empresa"]["direccion"];
			$ls_telemp=$_SESSION["la_empresa"]["telemp"];
			$ld_fecha="31/12/".substr($_SESSION["la_empresa"]["periodo"],0,4);
			$ld_fechadesde="01/01/".substr($_SESSION["la_empresa"]["periodo"],0,4);
			$ld_fechahasta="31/12/".substr($_SESSION["la_empresa"]["periodo"],0,4);
			$ls_nompro=$io_report->DS->data["nompro"][$li_i];
			$ls_nacpro=$io_report->DS->data["nacpro"][$li_i];
			$ls_rifpro=$io_report->DS->data["rifpro"][$li_i];
			$ls_nitpro=$io_report->DS->data["nitpro"][$li_i];
			$ls_dirpro=$io_report->DS->data["dirpro"][$li_i];
			$ls_telpro=$io_report->DS->data["telpro"][$li_i];
			$ls_nombene=$io_report->DS->data["nombene"][$li_i];
			$ls_apebene=$io_report->DS->data["apebene"][$li_i];
			$ls_nacben=$io_report->DS->data["nacben"][$li_i];
			$ls_cedbene=$io_report->DS->data["ced_bene"][$li_i];
			$ls_numpasben=$io_report->DS->data["numpasben"][$li_i];
			$ls_dirbene=$io_report->DS->data["dirbene"][$li_i];
			$ls_telbene=$io_report->DS->data["telbene"][$li_i];
			if($ls_tipproben=="P")
			{
				$ls_codigo=$io_report->DS->data["cod_pro"][$li_i];
			}
			else
			{
				$ls_codigo=$io_report->DS->data["ced_bene"][$li_i];
			}
			$lo_hoja->write($li_pos,0, "Marque el tipo de agente de retención",$lo_titulo);
			$lo_hoja->write($li_pos,2, "Persona Natural:  ",$lo_datacenter);
			$lo_hoja->write($li_pos,3, "___",$lo_dataleft);
			$lo_hoja->write($li_pos,4, " Persona Juridica:  ",$lo_datacenter);
			$lo_hoja->write($li_pos,5, "___",$lo_dataleft);
			$lo_hoja->write($li_pos,6, "Entidad Pública:  ",$lo_datacenter);
			$lo_hoja->write($li_pos,7, "_X_",$lo_dataleft);
			$lo_hoja->write($li_pos,8, "APELLIDOS Y NOMBRES:  ",$lo_titulo);
			if($ls_tipproben=="P")
			{
				$ls_nombre=$ls_nompro;
				$ls_cedula="";
				$ls_pasaporte="";
				$ls_rif=$ls_rifpro;
				$ls_nit=$ls_nitpro;
				$ls_direccion=$ls_dirpro;
				$ls_telefono=$ls_telpro;
				$ls_nacionalidad=$ls_nacpro;
				
				$li_pos2=$li_pos+2;
				$li_pos3=$li_pos2+2;
				$li_pos4=$li_pos3+1;
				$li_pos5=$li_pos4+1;
				$li_pos6=$li_pos5+1;
				$li_pos7=$li_pos6+2;
				$li_pos8=$li_pos7+1;
				$li_pos9=$li_pos8+1;
				$li_pos10=$li_pos9+1;
				$li_pos11=$li_pos10+1;
				$li_pos12=$li_pos11+2;
				$li_pos13=$li_pos12+2;
				$li_pos14=$li_pos13+1;
				$lo_hoja->write($li_pos,9, " ".$ls_nombre,$lo_datacenter);
				$lo_hoja->write($li_pos,10, "Tipo de Persona Natural:_____  Juridica: __X__",$lo_datacenter);
				$lo_hoja->write($li_pos2,3, "AGENTE DE RETENCIÓN",$lo_encabezado2);
				$lo_hoja->write($li_pos3,0, "NOMBRE DEL ORGANISMO",$lo_encabezado3);
				$lo_hoja->write($li_pos3,3, $ls_nombre_emp,$lo_normal);
				$lo_hoja->write($li_pos4,8, "Nacionalidad",$lo_encabezado4);
				$lo_hoja->write($li_pos6,0, "                  TIPO DE PERSONA JURÍDICA",$lo_encabezado4);
				$lo_hoja->write($li_pos6,3, "RIF:  ".$ls_rif_emp."     NIT: ".$ls_nit_emp,$lo_normal);
				$lo_hoja->write($li_pos7,0, "DIRECCIÓN  ",$lo_encabezado4);
				$lo_hoja->write($li_pos7,1, $ls_direccion_emp,$lo_dataesp);
				$lo_hoja->write($li_pos8,0, "Fecha de Cierre  ",$lo_encabezado4);
				$lo_hoja->write($li_pos8,1, $ld_fecha,$lo_dataesp);
				$lo_hoja->write($li_pos9,0, "TELEFONO(S)  ",$lo_encabezado4);
				$lo_hoja->write($li_pos9,1, $ls_telemp,$lo_dataesp);
				$lo_hoja->write($li_pos7,8, "                                                                                              Cédula                   Número de Pasaporte             Nro. R.I.F             Nro. N.I.T  ",$lo_encabezado4);
				$lo_hoja->write($li_pos8,8, "                                                                                    ".$ls_rif."           ".$ls_nit     ,$lo_dataesp);
				$lo_hoja->write($li_pos9,8, "PERÍODO AL QUE COMPRENDEN LOS PAGOS EFECTUADOS DURANTE ".$ld_fechadesde." AL ".$ld_fechahasta,$lo_dataesp);
				$lo_hoja->write($li_pos10,8, "DIRECCIÓN:     ".$ls_direccion,$lo_dataesp);
				$lo_hoja->write($li_pos11,8, "TELÉFONO:      ".$ls_telefono,$lo_dataesp);
				$lo_hoja->write($li_pos12,6, "INFORMACIÓN DEL IMPUESTO RETENIDO Y ENTERADO  ",$lo_encabezado4);
				$lo_hoja->write($li_pos13,0, "Fecha de Pago                Total Cant. Pagada                Cant. Objeto Retención                %de Ret                Impuesto Retenido                Total Cant. Obj. Ret Acumulada                                Impuesto Ret. Acumulada                ",$lo_dataesp);
				
				if($ls_nacionalidad=="V")
				{
					$lo_hoja->write($li_pos4,9, "V:__X__                           Residente en el pais:",$lo_normal);
					$lo_hoja->write($li_pos5,9, "E:_____",$lo_normal);
					$lo_hoja->write($li_pos4,10, "Si:__X__",$lo_normal);
					$lo_hoja->write($li_pos5,10, "No:_____",$lo_normal);
					$lo_hoja->write($li_pos6,8, "Constituida en el País",$lo_encabezado4);
					$lo_hoja->write($li_pos6,9, "               Si:__X__         No:______",$lo_normal);
				}
				else
				{
					$lo_hoja->write($li_pos4,9, "V:_____                           Residente en el pais:",$lo_normal);
					$lo_hoja->write($li_pos5,9, "E:__X__",$lo_normal);
					$lo_hoja->write($li_pos4,10, "Si:_____",$lo_normal);
					$lo_hoja->write($li_pos5,10, "No:__X__",$lo_normal);
					$lo_hoja->write($li_pos6,8, "Constituida en el País",$lo_encabezado4);
					$lo_hoja->write($li_pos6,9, "               Si:_____         No:__X__",$lo_normal);
				}
				
				$lb_valido=$io_report->uf_arc_detalle($ls_codigo,$ls_tipproben); // Obtenemos el detalle del reporte
				if($lb_valido)
				{
					$li_totrow_det=$io_report->ds_detalle->getRowCount("cod_pro");
					$li_totalacumulado=0;
					$li_retencionacumulada=0;
					for($li_s=1;$li_s<=$li_totrow_det;$li_s++) 
					{
						$ls_fecha=substr($io_report->ds_detalle->data["fecemidoc"][$li_s],8,2);
						$ls_fecha=$ls_fecha."    ".substr($io_report->ds_detalle->data["fecemidoc"][$li_s],5,2);
						$ls_fecha=$ls_fecha."    ".substr($io_report->ds_detalle->data["fecemidoc"][$li_s],0,4);
						$li_montotdoc=number_format($io_report->ds_detalle->data["montotdoc"][$li_s],2,",",".");
						$li_monobjret=number_format($io_report->ds_detalle->data["monobjret"][$li_s],2,",",".");
						$li_porded=number_format($io_report->ds_detalle->data["porded"][$li_s],2,",",".");
						$li_monret=number_format($io_report->ds_detalle->data["monret"][$li_s],2,",",".");
						$li_total_pagado=$li_total_pagado+$io_report->ds_detalle->data["montotdoc"][$li_s];
						$li_total_retencion=$li_total_retencion+$io_report->ds_detalle->data["monobjret"][$li_s];
						$li_total_impuesto=$li_total_impuesto+$io_report->ds_detalle->data["monret"][$li_s];
						$li_totalacumulado=$li_totalacumulado+$io_report->ds_detalle->data["monobjret"][$li_s];
						$li_retencionacumulada=$li_retencionacumulada+$io_report->ds_detalle->data["monret"][$li_s];
						$li_total=number_format($li_totalacumulado,2,",",".");
						$li_retencion=number_format($li_retencionacumulada,2,",",".");
						$lo_hoja->write($li_pos14,0, " ".$ls_fecha,$lo_normal2);
						$lo_hoja->write($li_pos14,1, "          ".$li_montotdoc,$lo_normal2);
						$lo_hoja->write($li_pos14,4, "          ".$li_monobjret,$lo_normal2);
						$lo_hoja->write($li_pos14,6, "          ".$li_porded,$lo_normal2);
						$lo_hoja->write($li_pos14,8, "          ".$li_monret,$lo_normal2);
						$lo_hoja->write($li_pos14,9, "          ".$li_total,$lo_normal2);
						$lo_hoja->write($li_pos14,10,$li_retencion,$lo_normal2);
						$li_pos14++;
					}
					$li_pos15=$li_pos14+1;
					$lo_hoja->write($li_pos15,7, "____________________________________________________________________________________________________________________________________________________________",$lo_encabezado4);
					$li_total_pagado=number_format($li_total_pagado,2,",",".");
					$li_total_retencion=number_format($li_total_retencion,2,",",".");
					$li_total_impuesto=number_format($li_total_impuesto,2,",",".");
					$lo_hoja->write($li_pos15+1,0, " Total",$lo_normal2);
					$lo_hoja->write($li_pos15+1,1, "          ".$li_total_pagado,$lo_normal2);
					$lo_hoja->write($li_pos15+1,4, "          ".$li_total_retencion,$lo_normal2);
					$lo_hoja->write($li_pos15+1,8, "          ".$li_total_impuesto,$lo_normal2);
					$lo_hoja->write($li_pos15+8,0, "                                                        AGENTE DE RETENCIÓN (SELLO Y FIRMA)  ",$lo_encabezado4);
					$lo_hoja->write($li_pos15+13,0, "    FECHA:   /    /       ",$lo_encabezado4);
					$lo_hoja->write($li_pos15+8,9, "PARA USO DE LA ADMINISTRACIÓN PÚBLICA ",$lo_encabezado4);
					$lo_hoja->write($li_pos15+18,7, "____________________________________________________________________________________________________________________________________________________________",$lo_encabezado4);
				}
			
			
			
			}
			else
			{
				$ls_nombre=$ls_apebene.", ".$ls_nombene;
				$ls_cedula=$ls_cedbene;
				$ls_pasaporte=$ls_numpasben;
				$ls_rif="";
				$ls_nit="";
				$ls_direccion=$ls_dirbene;
				$ls_telefono=$ls_telbene;
				$ls_nacionalidad=$ls_nacben;
				
				$li_pos2=$li_pos+2;
				$li_pos3=$li_pos2+2;
				$li_pos4=$li_pos3+1;
				$li_pos5=$li_pos4+1;
				$li_pos6=$li_pos5+1;
				$li_pos7=$li_pos6+2;
				$li_pos8=$li_pos7+1;
				$li_pos9=$li_pos8+1;
				$li_pos10=$li_pos9+1;
				$li_pos11=$li_pos10+1;
				$li_pos12=$li_pos11+2;
				$li_pos13=$li_pos12+2;
				$li_pos14=$li_pos13+1;
				$lo_hoja->write($li_pos,9, " ".$ls_nombre,$lo_datacenter);
				$lo_hoja->write($li_pos,10, "Tipo de Persona Natural:__X__  Juridica: _____",$lo_datacenter);
				$lo_hoja->write($li_pos2,3, "AGENTE DE RETENCIÓN",$lo_encabezado2);
				$lo_hoja->write($li_pos3,0, "NOMBRE DEL ORGANISMO",$lo_encabezado3);
				$lo_hoja->write($li_pos3,3, $ls_nombre_emp,$lo_normal);
				$lo_hoja->write($li_pos3,8, "Nacionalidad",$lo_encabezado4);
				$lo_hoja->write($li_pos6,0, "                  TIPO DE PERSONA JURÍDICA",$lo_encabezado4);
				$lo_hoja->write($li_pos6,3, "RIF:  ".$ls_rif_emp."     NIT: ".$ls_nit_emp,$lo_normal);
				$lo_hoja->write($li_pos7,0, "DIRECCIÓN  ",$lo_encabezado4);
				$lo_hoja->write($li_pos7,1, $ls_direccion_emp,$lo_dataesp);
				$lo_hoja->write($li_pos8,0, "Fecha de Cierre  ",$lo_encabezado4);
				$lo_hoja->write($li_pos8,1, $ld_fecha,$lo_dataesp);
				$lo_hoja->write($li_pos9,0, "TELEFONO(S)  ",$lo_encabezado4);
				$lo_hoja->write($li_pos9,1, $ls_telemp,$lo_dataesp);
				$lo_hoja->write($li_pos7,8, "                                                                                              Cédula                   Número de Pasaporte             Nro. R.I.F             Nro. N.I.T  ",$lo_encabezado4);
				$lo_hoja->write($li_pos8,8, " ".$ls_cedula."                       ".$ls_pasaporte     ,$lo_dataesp);
				$lo_hoja->write($li_pos9,8, "PERÍODO AL QUE COMPRENDEN LOS PAGOS EFECTUADOS DURANTE ".$ld_fechadesde." AL ".$ld_fechahasta,$lo_dataesp);
				$lo_hoja->write($li_pos10,8, "DIRECCIÓN:     ".$ls_direccion,$lo_dataesp);
				$lo_hoja->write($li_pos11,8, "TELÉFONO:      ".$ls_telefono,$lo_dataesp);
				$lo_hoja->write($li_pos12,6, "INFORMACIÓN DEL IMPUESTO RETENIDO Y ENTERADO  ",$lo_encabezado4);
				$lo_hoja->write($li_pos13,0, "Fecha de Pago                Total Cant. Pagada                Cant. Objeto Retención                %de Ret                Impuesto Retenido                Total Cant. Obj. Ret Acumulada                                Impuesto Ret. Acumulada                ",$lo_dataesp);

				
				if($ls_nacionalidad=="V")
				{
					$lo_hoja->write($li_pos4,9, "V:__X__           				   Residente en el pais:",$lo_normal);
					$lo_hoja->write($li_pos5,9, "E:_____",$lo_normal);
					$lo_hoja->write($li_pos4,10, "Si:__X__",$lo_normal);
					$lo_hoja->write($li_pos5,10, "No:_____",$lo_normal);
					$lo_hoja->write($li_pos6,8, "Constituida en el País",$lo_encabezado4);
					$lo_hoja->write($li_pos6,9, "               Si:__X__         No:______",$lo_normal);
				}
				else
				{
					$lo_hoja->write($li_pos4,9, "V:_____                           Residente en el pais:",$lo_normal);
					$lo_hoja->write($li_pos5,9, "E:__X__",$lo_normal);
					$lo_hoja->write($li_pos4,10, "Si:_____",$lo_normal);
					$lo_hoja->write($li_pos5,10, "No:__X__",$lo_normal);
					$lo_hoja->write($li_pos6,8, "Constituida en el País",$lo_encabezado4);
					$lo_hoja->write($li_pos6,9, "               Si:_____         No:__X__",$lo_normal);
				}
				
				$lb_valido=$io_report->uf_arc_detalle($ls_codigo,$ls_tipproben); // Obtenemos el detalle del reporte
				if($lb_valido)
				{
					$li_totrow_det=$io_report->ds_detalle->getRowCount("cod_pro");
					$li_totalacumulado=0;
					$li_retencionacumulada=0;
					for($li_s=1;$li_s<=$li_totrow_det;$li_s++) 
					{
						$ls_fecha=substr($io_report->ds_detalle->data["fecemidoc"][$li_s],8,2);
						$ls_fecha=$ls_fecha."    ".substr($io_report->ds_detalle->data["fecemidoc"][$li_s],5,2);
						$ls_fecha=$ls_fecha."    ".substr($io_report->ds_detalle->data["fecemidoc"][$li_s],0,4);
						$li_montotdoc=number_format($io_report->ds_detalle->data["montotdoc"][$li_s],2,",",".");
						$li_monobjret=number_format($io_report->ds_detalle->data["monobjret"][$li_s],2,",",".");
						$li_porded=number_format($io_report->ds_detalle->data["porded"][$li_s],2,",",".");
						$li_monret=number_format($io_report->ds_detalle->data["monret"][$li_s],2,",",".");
						$li_total_pagado=$li_total_pagado+$io_report->ds_detalle->data["montotdoc"][$li_s];
						$li_total_retencion=$li_total_retencion+$io_report->ds_detalle->data["monobjret"][$li_s];
						$li_total_impuesto=$li_total_impuesto+$io_report->ds_detalle->data["monret"][$li_s];
						$li_totalacumulado=$li_totalacumulado+$io_report->ds_detalle->data["monobjret"][$li_s];
						$li_retencionacumulada=$li_retencionacumulada+$io_report->ds_detalle->data["monret"][$li_s];
						$li_total=number_format($li_totalacumulado,2,",",".");
						$li_retencion=number_format($li_retencionacumulada,2,",",".");
						$lo_hoja->write($li_pos14,0, " ".$ls_fecha,$lo_normal2);
						$lo_hoja->write($li_pos14,1, "          ".$li_montotdoc,$lo_normal2);
						$lo_hoja->write($li_pos14,4, "          ".$li_monobjret,$lo_normal2);
						$lo_hoja->write($li_pos14,6, "          ".$li_porded,$lo_normal2);
						$lo_hoja->write($li_pos14,8, "          ".$li_monret,$lo_normal2);
						$lo_hoja->write($li_pos14,9, "          ".$li_total,$lo_normal2);
						$lo_hoja->write($li_pos14,10,$li_retencion,$lo_normal2);
						$li_pos14++;
					}
					$li_pos15=$li_pos14+1;
					$lo_hoja->write($li_pos15,7, "____________________________________________________________________________________________________________________________________________________________",$lo_encabezado4);
					$li_total_pagado=number_format($li_total_pagado,2,",",".");
					$li_total_retencion=number_format($li_total_retencion,2,",",".");
					$li_total_impuesto=number_format($li_total_impuesto,2,",",".");
					$lo_hoja->write($li_pos15+1,0, " Total",$lo_normal2);
					$lo_hoja->write($li_pos15+1,1, "          ".$li_total_pagado,$lo_normal2);
					$lo_hoja->write($li_pos15+1,4, "          ".$li_total_retencion,$lo_normal2);
					$lo_hoja->write($li_pos15+1,8, "          ".$li_total_impuesto,$lo_normal2);
					$lo_hoja->write($li_pos15+8,0, "                                                        AGENTE DE RETENCIÓN (SELLO Y FIRMA)  ",$lo_encabezado4);
					$lo_hoja->write($li_pos15+13,0, "    FECHA:   /    /       ",$lo_encabezado4);
					$lo_hoja->write($li_pos15+8,9, "PARA USO DE LA ADMINISTRACIÓN PÚBLICA ",$lo_encabezado4);
					$lo_hoja->write($li_pos15+18,7, "____________________________________________________________________________________________________________________________________________________________",$lo_encabezado4);
				}
			
			
			
			
			}
			$li_pos=$li_pos13+40;
			$li_postit=$li_pos-5;	
		}
		
		
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"cxp_arc.xls\"");
		header("Content-Disposition: inline; filename=\"cxp_arc.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_soc);
?> 