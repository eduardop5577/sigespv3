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
	function uf_insert_seguridad($as_titulo,$as_titulo2,$as_desnom,$as_periodo,$ai_tipo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del reporte
		//	    		   as_desnom // Descripci�n de la n�mina
		//	    		   as_periodo // Descripci�n del per�odo
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Gener� el Reporte ".$as_titulo." ".$as_titulo2.". Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_contableconceptos.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hcontableconceptos.php",$ls_descripcion,$ls_codnom);
		}
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo2,$as_desnom,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_titulo2 // T�tulo del Reporte
		//	    		   as_desnom // Descripci�n de la n�mina
		//	    		   as_periodo // Descripci�n del per�odo
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,940,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,950,11,$as_titulo); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo2);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,940,11,$as_titulo2); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,930,11,$as_periodo); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(10,$as_desnom);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,920,10,$as_desnom); // Agregar el t�tulo
		$io_pdf->addText(500,970,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,963,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_presupuesto($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_presupuesto
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime la cabecera para el detalle presupuestario
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>'<b>Afectaci�n Presupuestaria</b>'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera_presupuesto
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_contable($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_contable
		//		   Access: private 
		//	    Arguments: io_pdf //Instancia de objeto pdf
		//    Description: funci�n que imprime la cabecera para el detalle contable
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>'<b>Afectaci�n Contable</b>'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera_contable
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_presupuesto($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_presupuesto
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle presupuestario
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-3);
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		$ls_titulo="";
		switch($ls_modalidad)
		{
			case "1": // Modalidad por Proyecto
				$ls_titulo="Estructura Presupuestaria";
				break;
				
			case "2": // Modalidad por Presupuesto
				$ls_titulo="Estructura Program�tica  ";
				break;
		}
		$la_columna=array('programatica'=>'<b>'.$ls_titulo.'</b>',
						  'estadisticos'=>'<b>Partida Presupuestaria</b>',
						  'denominacion'=>'<b>                             Descripci�n</b>',
						  'total'=>'<b>Total                 </b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('programatica'=>array('justification'=>'center','width'=>100), // Justificaci�n y ancho de la columna
						 			   'estadisticos'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>260), // Justificaci�n y ancho de la columna
						 			   'total'=>array('justification'=>'right','width'=>80))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle_presupuesto
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_contable($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_contable
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle contable
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-3);
		$la_columna=array('cuenta'=>'<b>Cuenta</b>',
						  'denominacion'=>'<b>                                Descripci�n</b>',
						  'debe'=>'<b>Debe               </b>',
						  'haber'=>'<b>Haber               </b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>80), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>260), // Justificaci�n y ancho de la columna
						 			   'debe'=>array('justification'=>'right','width'=>80), // Justificaci�n y ancho de la columna
						 			   'haber'=>array('justification'=>'right','width'=>80))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle_contable
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_contable_cestatik($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_contable
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle contable
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		//$io_pdf->ezSetDy(-3);
		$la_columna=array('cuenta'=>'',
						  'denominacion'=>'',
						  'debe'=>'',
						  'haber'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>80), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>260), // Justificaci�n y ancho de la columna
						 			   'debe'=>array('justification'=>'right','width'=>80), // Justificaci�n y ancho de la columna
						 			   'haber'=>array('justification'=>'right','width'=>80))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle_contable

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera_presupuesto($ai_total,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera_presupuesto
		//		   Access: private 
		//	    Arguments: ai_total // Total del presupuesto
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el fin de la cabecera para el detalle presupuestario
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 11/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares;
		
		$la_data=array(array('name'=>'<b>Totales '.$ls_bolivares.'</b>','total'=>$ai_total));
		$la_columna=array('name'=>'','total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				 		 'cols'=>array('name'=>array('justification'=>'right','width'=>420), // Justificaci�n y ancho de la columna
						 			   'total'=>array('justification'=>'right','width'=>80))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center'); // Orientaci�n de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera_presupuesto
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera_contable($ai_debe,$ai_haber,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera_contable
		//		   Access: private 
		//	    Arguments: ai_debe // Total por el Debe
		//	               ai_haber // Total por el Haber
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el fin de la cabecera para los detalles contables
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares;
		
		$la_data=array(array('name'=>'<b>Totales '.$ls_bolivares.'</b>','debe'=>$ai_debe,'haber'=>$ai_haber));
		$la_columna=array('name'=>'','debe'=>'','haber'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				 		 'cols'=>array('name'=>array('justification'=>'right','width'=>340), // Justificaci�n y ancho de la columna
						 			   'debe'=>array('justification'=>'right','width'=>80),
						 			   'haber'=>array('justification'=>'right','width'=>80))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera_contable
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private 
		//    Description: funci�n que imprime las firmas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data[0]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[1]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[2]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[3]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[4]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[5]=array('firma1'=>'_____________________________________','firma2'=>'','firma3'=>'_____________________________________');
		$la_data[6]=array('firma1'=>'LCDO. FERMIN MARIN','firma2'=>'','firma3'=>'T.S.U.ARGELIS FREITEZ');
		$la_data[7]=array('firma1'=>'ALCALDE DEL MUNICIPIO SIMON PLANAS','firma2'=>'','firma3'=>'DIRECTORA DE ADMINISTRACION Y FINANZAS (E)');
		$la_columna=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>600, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'center','width'=>275), // Justificaci�n y ancho de la columna
						 			   'firma2'=>array('justification'=>'center','width'=>50),
						 			   'firma3'=>array('justification'=>'center','width'=>275))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[0]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[2]=array('firma1'=>'','firma2'=>'_____________________________________','firma3'=>'');
		$la_data[3]=array('firma1'=>'','firma2'=>'ABG.FATIMA COLMENAREZ','firma3'=>'');
		$la_data[4]=array('firma1'=>'','firma2'=>'COORDINADORA DE RECURSOS HUMANOS','firma3'=>'');
		$la_columna=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>600, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'center','width'=>100), // Justificaci�n y ancho de la columna
						 			   'firma2'=>array('justification'=>'center','width'=>275),
						 			   'firma3'=>array('justification'=>'center','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);

	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------
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
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$ls_conpronom=$_SESSION["la_nomina"]["conpronom"];
	$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
	$ld_fechasper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
	$ls_titulo="<b>Resumen Contable</b>";
	$ls_titulo2="<b>Presupuestario de Conceptos</b>";
	$ls_periodo="<b>Per�odo Nro ".$ls_peractnom.", ".$ld_fecdesper." - ".$ld_fechasper."</b>";
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_titulo2,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido) // Buscamos la informaci�n que afecta el presupuesto
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
	if($lb_valido) // Buscamos la informaci�n que afecta contabilidad por el debe
	{
		switch($ls_conpronom)
		{
			case "1":
				$lb_valido=$io_report->uf_contableconceptos_contable_proyecto();
				break;
				
			default:
				$lb_valido=$io_report->uf_contableconceptos_contable();
				break;
		}
	}
	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('legal','portrait'); // Instancia de la clase PDF portrait
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.3,2.5,3,3); // Configuraci�n de los margenes en cent�metros 3.3,2.5,3,3
		uf_print_encabezado_pagina($ls_titulo,$ls_titulo2,$ls_desnom,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la p�gina
		//$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n�mero de p�gina
		
  	    //--------------------------------------------- Imprimir el detalle Presupuestario------------------------------------------------	
		$li_totrow=$io_report->DS->getRowCount("cueprecon");
		$li_totalpresupuesto=0;
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		$ls_espnom=$_SESSION["la_nomina"]["espnom"];
		$ls_ctnom=$_SESSION["la_nomina"]["ctnom"];
		$ls_codpronom=$_SESSION["la_nomina"]["codpronom"];
		$ls_codbennom=$_SESSION["la_nomina"]["codbennom"];
		$la_data=array();
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_programatica=$io_report->DS->data["codestpro1"][$li_i].$io_report->DS->data["codestpro2"][$li_i].
							 $io_report->DS->data["codestpro3"][$li_i].$io_report->DS->data["codestpro4"][$li_i].
							 $io_report->DS->data["codestpro5"][$li_i];
			$ls_codest1="";
			$ls_codest2="";
			$ls_codest3="";
			$ls_codest4="";
			$ls_codest5="";
			$arrResultado=$io_fun_nomina->uf_formato_estructura($ls_programatica,$ls_codest1,$ls_codest2,$ls_codest3,$ls_codest4,$ls_codest5);
			$ls_codest1=$arrResultado['as_codestpro1'];
			$ls_codest2=$arrResultado['as_codestpro2'];
			$ls_codest3=$arrResultado['as_codestpro3'];
			$ls_codest4=$arrResultado['as_codestpro4'];
			$ls_codest5=$arrResultado['as_codestpro5'];
			$ls_programatica=$ls_codest1.'-'.$ls_codest2.'-'.$ls_codest3;
			switch($ls_modalidad)
			{
				case "2": // Modalidad por Programa
					
					$ls_programatica=$ls_codest1.'-'.$ls_codest2.'-'.$ls_codest3.'-'.$ls_codest4.'-'.$ls_codest5;
					break;
			}
			$ls_cueprecon=$io_report->DS->data["cueprecon"][$li_i];
			$ls_denominacion=$io_report->DS->data["denominacion"][$li_i];
			$li_total=$io_report->DS->data["total"][$li_i];
			$li_totalpresupuesto=$li_totalpresupuesto+$li_total;
			$li_total=$io_fun_nomina->uf_formatonumerico($li_total);
			$la_data[$li_i]=array('programatica'=>$ls_programatica,'estadisticos'=>$ls_cueprecon,
								  'denominacion'=>$ls_denominacion,'total'=>$li_total);
		}
		$io_report->DS->resetds("cueprecon");
		if($li_totrow>0)
		{
			uf_print_cabecera_presupuesto($io_pdf); // Imprimimos la cabecera de presupuesto
			uf_print_detalle_presupuesto($la_data,$io_pdf); // Imprimimos el detalle presupuestario
			$li_totalpresupuesto=$io_fun_nomina->uf_formatonumerico($li_totalpresupuesto);
			uf_print_pie_cabecera_presupuesto($li_totalpresupuesto,$io_pdf); // imprimimos los totales presupuestario
			unset($la_data);			
		}
		//-------------------------------------------------------------------------------------------------------------------------------	
		
		//--------------------------------------------- Imprimir el detalle Contable------------------------------------------------	
		$li_i=0;
		$li_totrow=$io_report->DS_detalle->getRowCount("cuenta");
		$li_totalcontadebe=0;
		$li_totalcontahaber=0;
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_cueconpatcon=trim($io_report->DS_detalle->data["cuenta"][$li_i]);
			$ls_denominacion=$io_report->DS_detalle->data["denominacion"][$li_i];
			$ls_operacion=$io_report->DS_detalle->data["operacion"][$li_i];
			if($ls_operacion=="D")
			{
				$li_debe=abs($io_report->DS_detalle->data["total"][$li_i]);
				$li_haber=0;
				$li_totalcontadebe=$li_totalcontadebe+$li_debe;
				$li_totalcontahaber=$li_totalcontahaber+$li_haber;
				$li_debe=$io_fun_nomina->uf_formatonumerico($li_debe);
				$li_haber=$io_fun_nomina->uf_formatonumerico($li_haber);
				$la_data[$li_i]=array('cuenta'=>$ls_cueconpatcon,'denominacion'=>$ls_denominacion,'debe'=>$li_debe,'haber'=>$li_haber);
			}
		}
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_cueconpatcon=trim($io_report->DS_detalle->data["cuenta"][$li_i]);
			$ls_denominacion=$io_report->DS_detalle->data["denominacion"][$li_i];
			$ls_operacion=$io_report->DS_detalle->data["operacion"][$li_i];
			if($ls_operacion=="H")
			{
				$li_debe=0;
				$li_haber=abs($io_report->DS_detalle->data["total"][$li_i]);
				$li_totalcontadebe=$li_totalcontadebe+$li_debe;
				$li_totalcontahaber=$li_totalcontahaber+$li_haber;
				$li_debe=$io_fun_nomina->uf_formatonumerico($li_debe);
				$li_haber=$io_fun_nomina->uf_formatonumerico($li_haber);
				$la_data[$li_i]=array('cuenta'=>$ls_cueconpatcon,'denominacion'=>$ls_denominacion,'debe'=>$li_debe,'haber'=>$li_haber);
			}
		}
		$io_report->DS_detalle->resetds("cuenta");
		if($li_totrow>0)
		{
			uf_print_cabecera_contable($io_pdf);// Imprimimos la cabecera contable
			uf_print_detalle_contable($la_data,$io_pdf); // Imprimimos el detalle contable
			$li_totalcontadebe=$io_fun_nomina->uf_formatonumerico($li_totalcontadebe);
			$li_totalcontahaber=$io_fun_nomina->uf_formatonumerico($li_totalcontahaber);
			uf_print_pie_cabecera_contable($li_totalcontadebe,$li_totalcontahaber,$io_pdf); // imprimimos los totales contable			
			unset($la_data);
		}		
		uf_print_firmas($io_pdf);
		//-------------------------------------------------------------------------------------------------------------------------------	
		if($lb_valido) // Si no ocurrio ning�n error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo alg�n error
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