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
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_titulo2,$as_desnom,$as_periodo,$ai_tipo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Generó el Reporte ".$as_titulo." ".$as_titulo2.". Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_disponibilidad.php",$ls_descripcion,$ls_codnom);
		}
		if($ai_tipo==2)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hdisponibilidad.php",$ls_descripcion,$ls_codnom);
		}
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina_general($as_titulo,$as_titulo2,$as_desnom,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_titulo2 // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo2);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,11,$as_titulo2); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,710,11,$as_periodo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,$as_desnom);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,700,10,$as_desnom); // Agregar el título
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_presupuesto($titulo, $io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_presupuesto
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime la cabecera para el detalle presupuestario
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-10);
		$la_data=array(array('name'=>'<b>'.$titulo.'</b>'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera_presupuesto
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_presupuesto($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_presupuesto
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle presupuestario
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-3);
		$la_columna=array('estructura'=>'<b>Estrutura</b>','cuenta'=>'<b>Cuenta</b>','denominacion'=>'<b>Denominación</b>',
						  'gastonomina'=>'<b>Gasto Nómina</b>','disponible'=>'<b>Disponibilidad</b>','saldo'=>'<b>Saldo</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('estructura'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>175), // Justificación y ancho de la columna
									   'gastonomina'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
									   'disponible'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
									   'saldo'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle_presupuesto
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_detalle_presupuesto_general($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_presupuesto
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle presupuestario
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-3);
		$la_columna=array('estructura'=>'<b>Estrutura</b>','cuenta'=>'<b>Cuenta</b>','denominacion'=>'<b>Denominación</b>',
						  'gastonomina'=>'<b>Gasto Nómina</b>','disponible'=>'<b>Disponibilidad</b>','saldo'=>'<b>Saldo</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('estructura'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>175), // Justificación y ancho de la columna
									   'gastonomina'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
									   'disponible'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
									   'saldo'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle_presupuesto
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera_presupuesto($ai_total,$ai_totalpersonal,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera_presupuesto
		//		   Access: private 
		//	    Arguments: ai_total // Total del presupuesto
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera para el detalle presupuestario
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares;
		
		$la_data=array(array('name'=>'<b>Total </b>','total'=>$ai_total));
		$la_columna=array('name'=>'','total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('name'=>array('justification'=>'right','width'=>475), // Justificación y ancho de la columna
						 			   'total'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera_presupuesto
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	$li_tipo=0;
	$ls_bolivares="";
	if($_SESSION["la_nomina"]["tiponomina"]=="NORMAL")
	{
		require_once("sigesp_sno_class_report_contables.php");
		$io_report=new sigesp_sno_class_report_contables();
		$li_tipo=1;
	}
	if($_SESSION["la_nomina"]["tiponomina"]=="HISTORICA")
	{
		require_once("sigesp_sno_class_report_historico_contables.php");
		$io_report=new sigesp_sno_class_report_historico_contables();
		$li_tipo=2;
	}	
	$ls_bolivares ="Bs.";
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$ls_conpronom=$_SESSION["la_nomina"]["conpronom"];
	$ls_tipo=$io_fun_nomina->uf_obtenervalor_get("tipo","");
	$ls_codestpro1=str_pad(trim($io_fun_nomina->uf_obtenervalor_get("codestpro1","")),25,"0",0);
	$ls_codestpro2=str_pad(trim($io_fun_nomina->uf_obtenervalor_get("codestpro2","")),25,"0",0);
	$ls_codestpro3=str_pad(trim($io_fun_nomina->uf_obtenervalor_get("codestpro3","")),25,"0",0);
	$ls_codestpro4=str_pad(trim($io_fun_nomina->uf_obtenervalor_get("codestpro4","")),25,"0",0);
	$ls_codestpro5=str_pad(trim($io_fun_nomina->uf_obtenervalor_get("codestpro5","")),25,"0",0);	
	$ls_estcla=trim($io_fun_nomina->uf_obtenervalor_get("estcla",""));
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("subnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("subnomhas","");
	$ld_fecpro=$io_fun_nomina->uf_obtenervalor_get("fecpro","");
	$ls_programatica="";
	$li_len1=0;
	$li_len2=0;
	$li_len3=0;
	$li_len4=0;
	$li_len5=0;
	$ls_tituloprog="";
	$arrResultado=$io_fun_nomina->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_tituloprog);
	$li_len1=$arrResultado['ai_len1'];
	$li_len2=$arrResultado['ai_len2'];
	$li_len3=$arrResultado['ai_len3'];
	$li_len4=$arrResultado['ai_len4'];
	$li_len5=$arrResultado['ai_len5'];
	$ls_tituloprog=$arrResultado['as_titulo'];
	$ls_programatica=$io_fun_nomina->uf_formatoprogramatica($ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5,$ls_programatica);
	$ls_titulo="<b>Resumen Contable</b>";
	$ls_titulo2="<b>Disponibilidad Presupuestaria</b>";
	$ls_titulo3="<b>".$ls_tituloprog."</b>";
	$ls_titulo4="<b>".$ls_programatica."</b>";
	$ls_periodo="<b>A la Fecha".$ld_fecpro."</b>";
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_titulo2,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido) // Buscamos la información que afecta el presupuesto
	{
		switch($ls_conpronom)
		{
			case "1":
				$lb_valido=$io_report->uf_contableconceptos_presupuesto_proyecto();
				break;
				
			default:
				$lb_valido=$io_report->uf_contableconceptos_presupuesto();
				break;
		}
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
		$io_pdf->ezSetCmMargins(3.5,2.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina_general($ls_titulo,$ls_titulo2,$ls_desnom,$ls_periodo,$io_pdf);
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		
  	    //--------------------------------------------- Imprimir el detalle Presupuestario------------------------------------------------	
		$li_totrow=$io_report->DS->getRowCount("cueprecon");
		if($li_totrow==0) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		$li_totalpresupuesto=0;
		$li_totalpersonal=0;
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		require_once("../../shared/class_folder/class_sigesp_int.php");
		require_once("../../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../../shared/class_folder/class_sigesp_int_spg.php");
		$io_intspg=new class_sigesp_int_spg();		
		$_SESSION["fechacomprobante"]=$io_funciones->uf_convertirdatetobd($ld_fecpro);
		$li_j=1;
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_programatica=$io_report->DS->data["codestpro1"][$li_i].$io_report->DS->data["codestpro2"][$li_i].
							 $io_report->DS->data["codestpro3"][$li_i].$io_report->DS->data["codestpro4"][$li_i].
							 $io_report->DS->data["codestpro5"][$li_i];
			$ls_cueprecon=$io_report->DS->data["cueprecon"][$li_i];
			$ls_denominacion=$io_report->DS->data["denominacion"][$li_i];
			$li_total_concepto=$io_report->DS->data["total"][$li_i];
			$ls_estclaconcepto=$io_report->DS->data["estcla"][$li_i];
			$ls_codestpro1=substr($ls_programatica,0,25);
			$ls_codestpro2=substr($ls_programatica,25,25);
			$ls_codestpro3=substr($ls_programatica,50,25);
			$ls_codestpro4=substr($ls_programatica,75,25);
			$ls_codestpro5=substr($ls_programatica,100,25);
			$ls_estructura=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
			$estprog[0]=$ls_codestpro1;
			$estprog[1]=$ls_codestpro2;
			$estprog[2]=$ls_codestpro3;
			$estprog[3]=$ls_codestpro4;
			$estprog[4]=$ls_codestpro5;
			$estprog[5]=$ls_estclaconcepto;
			$ls_status = "";
			$adec_asignado = 0;
			$adec_aumento = 0;
			$adec_disminucion = 0;
			$adec_precomprometido = 0;
			$adec_comprometido = 0;
			$adec_causado = 0;
			$adec_pagado = 0;
			$arrResultado=$io_intspg->uf_spg_saldo_select($ls_codemp, $estprog, $ls_cueprecon, $ls_status, $adec_asignado, 
													   $adec_aumento,$adec_disminucion,$adec_precomprometido,
													   $adec_comprometido,$adec_causado,$adec_pagado);
			$ls_status = $arrResultado['as_status'];
			$adec_asignado = $arrResultado['adec_asignado'];
			$adec_aumento = $arrResultado['adec_aumento'];
			$adec_disminucion = $arrResultado['adec_disminucion'];
			$adec_precomprometido = $arrResultado['adec_precomprometido'];
			$adec_comprometido = $arrResultado['adec_comprometido'];
			$adec_causado = $arrResultado['adec_causado'];
			$adec_pagado = $arrResultado['adec_pagado'];
			$lb_valido = $arrResultado['lb_valido'];

			$li_disponibilidad=($adec_asignado-($adec_comprometido+$adec_precomprometido)+$adec_aumento-$adec_disminucion);
			$li_resto=$li_disponibilidad-$li_total_concepto;
			$ls_estmodest = $_SESSION["la_empresa"]["estmodest"];
			if($ls_estmodest==1)
			{
				$ls_codestpro1 = substr($ls_codestpro1,-$_SESSION["la_empresa"]["loncodestpro1"]);
				$ls_codestpro2 = substr($ls_codestpro2,-$_SESSION["la_empresa"]["loncodestpro2"]);
				$ls_codestpro3 = substr($ls_codestpro3,-$_SESSION["la_empresa"]["loncodestpro3"]);
				$ls_codestpro  = $ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3;
			}
			else
			{
				$ls_codestpro=substr($ls_codestpro1,-2)."-".substr($ls_codestpro2,-2)."-".substr($ls_codestpro3,-2)."-".substr($ls_codestpro4,-2)."-".substr($ls_codestpro5,-2);
			}
			$li_total_concepto=number_format($li_total_concepto,2,",",".");
			$li_disponibilidad=number_format($li_disponibilidad,2,",",".");
			$li_resto=number_format($li_resto,2,",",".");
			$la_datag[$li_j]=array('estructura'=>$ls_codestpro,'cuenta'=>$ls_cueprecon,'denominacion'=>$ls_denominacion,'gastonomina'=>$li_total_concepto,'disponible'=>$li_disponibilidad,'saldo'=>$li_resto);
			$li_j++;
		}
		$io_report->DS->resetds("cueprecon");
		if($li_j>1)
		{
			uf_print_cabecera_presupuesto("Afectación de Nómina",$io_pdf); // Imprimimos la cabecera de presupuesto
			uf_print_detalle_presupuesto_general($la_datag,$io_pdf); // Imprimimos el detalle presupuestario
			$li_totalpresupuesto=$io_fun_nomina->uf_formatonumerico($li_totalpresupuesto);
			$li_totalpersonal=number_format($li_totalpersonal,0,"","");
			unset($la_datag);			
		}
		switch($ls_conpronom)
		{
			case "1":
				$lb_valido=$io_report->uf_contableaportes_presupuesto_proyecto();
				break;
				
			default:
				$lb_valido=$io_report->uf_contableaportes_presupuesto();
				break;
		}
		if($lb_valido==false) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error en el reporte');"); 
			print(" close();");
			print("</script>");
		}
		else // Imprimimos el reporte
		{
			$li_totrow=$io_report->DS->getRowCount("cueprepatcon");
			$li_j=1;
			for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
			{
				$ls_programatica=$io_report->DS->data["codestpro1"][$li_i].$io_report->DS->data["codestpro2"][$li_i].
								 $io_report->DS->data["codestpro3"][$li_i].$io_report->DS->data["codestpro4"][$li_i].
								 $io_report->DS->data["codestpro5"][$li_i];
				$ls_cueprecon=$io_report->DS->data["cueprepatcon"][$li_i];
				$ls_denominacion=$io_report->DS->data["denominacion"][$li_i];
				$li_total_concepto=abs($io_report->DS->data["total"][$li_i]);
				$ls_estclaconcepto=$io_report->DS->data["estcla"][$li_i];
				$ls_codestpro1=substr($ls_programatica,0,25);
				$ls_codestpro2=substr($ls_programatica,25,25);
				$ls_codestpro3=substr($ls_programatica,50,25);
				$ls_codestpro4=substr($ls_programatica,75,25);
				$ls_codestpro5=substr($ls_programatica,100,25);
				$ls_estructura=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
				$estprog[0]=$ls_codestpro1;
				$estprog[1]=$ls_codestpro2;
				$estprog[2]=$ls_codestpro3;
				$estprog[3]=$ls_codestpro4;
				$estprog[4]=$ls_codestpro5;
				$estprog[5]=$ls_estclaconcepto;
				$ls_status = "";
				$adec_asignado = 0;
				$adec_aumento = 0;
				$adec_disminucion = 0;
				$adec_precomprometido = 0;
				$adec_comprometido = 0;
				$adec_causado = 0;
				$adec_pagado = 0;
				$arrResultado=$io_intspg->uf_spg_saldo_select($ls_codemp, $estprog, $ls_cueprecon, $ls_status,$adec_asignado, 
														   $adec_aumento,$adec_disminucion,$adec_precomprometido,
														   $adec_comprometido,$adec_causado,$adec_pagado);
				$ls_status = $arrResultado['as_status'];
				$adec_asignado = $arrResultado['adec_asignado'];
				$adec_aumento = $arrResultado['adec_aumento'];
				$adec_disminucion = $arrResultado['adec_disminucion'];
				$adec_precomprometido = $arrResultado['adec_precomprometido'];
				$adec_comprometido = $arrResultado['adec_comprometido'];
				$adec_causado = $arrResultado['adec_causado'];
				$adec_pagado = $arrResultado['adec_pagado'];
				$lb_valido = $arrResultado['lb_valido'];
				$li_disponibilidad=($adec_asignado-($adec_comprometido+$adec_precomprometido)+$adec_aumento-$adec_disminucion);
				$li_resto=$li_disponibilidad-$li_total_concepto;
				$ls_estmodest = $_SESSION["la_empresa"]["estmodest"];
				if($ls_estmodest==1)
				{
					$ls_codestpro1 = substr($ls_codestpro1,-$_SESSION["la_empresa"]["loncodestpro1"]);
					$ls_codestpro2 = substr($ls_codestpro2,-$_SESSION["la_empresa"]["loncodestpro2"]);
					$ls_codestpro3 = substr($ls_codestpro3,-$_SESSION["la_empresa"]["loncodestpro3"]);
					$ls_codestpro  = $ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3;
				}
				else
				{
					$ls_codestpro=substr($ls_codestpro1,-2)."-".substr($ls_codestpro2,-2)."-".substr($ls_codestpro3,-2)."-".substr($ls_codestpro4,-2)."-".substr($ls_codestpro5,-2);
				}
				$li_total_concepto=number_format($li_total_concepto,2,",",".");
				$li_disponibilidad=number_format($li_disponibilidad,2,",",".");
				$li_resto=number_format($li_resto,2,",",".");
				$la_datag[$li_j]=array('estructura'=>$ls_codestpro,'cuenta'=>$ls_cueprecon,'denominacion'=>$ls_denominacion,'gastonomina'=>$li_total_concepto,'disponible'=>$li_disponibilidad,'saldo'=>$li_resto);
				$li_j++;
			}
			$io_report->DS->resetds("cueprecon");
		}
		unset($_SESSION["fechacomprobante"]);
		if($li_j>1)
		{
			uf_print_cabecera_presupuesto("Afectación Aportes",$io_pdf); // Imprimimos la cabecera de presupuesto
			uf_print_detalle_presupuesto_general($la_datag,$io_pdf); // Imprimimos el detalle presupuestario
			$li_totalpresupuesto=$io_fun_nomina->uf_formatonumerico($li_totalpresupuesto);
			$li_totalpersonal=number_format($li_totalpersonal,0,"","");
			unset($la_datag);			
		}
		//-------------------------------------------------------------------------------------------------------------------------------	
		
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
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
?> 