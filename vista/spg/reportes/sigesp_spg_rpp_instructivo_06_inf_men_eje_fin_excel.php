<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
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
		print "</script>";		
	}	
		
  // para crear el libro excel
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "INFORMACION_MENSUAL_DE_LA_EJECUCION_FINANCIERA.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_moneda,$as_trimestre,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//                 $as_moneda // Moneda
		//	    		   as_trimestre // Nro. del Trimestre
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Arnaldo Su?rez
		// Fecha Creaci?n: 29/10/2008
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,440,1000,440);  // Linea Proyecto/Accion
		$io_pdf->line(130,420,1000,420); // Linea SUBPARTIDA, COMPROMISO, CAUSADO Y PAGADO
		$io_pdf->line(400,405,480,405);  // Linea horizontal Variacion de Compromiso 
		$io_pdf->line(660,405,740,405);  // Linea horizontal Variacion de Causado 
		$io_pdf->line(920,405,1000,405); // Linea horizontal Variacion de Pagado 
		$io_pdf->line(45,380,45,440);    // Linea entre Codigo y Denominacion
		$io_pdf->line(130,380,130,440);  // Linea entre Denominacion y Partida
		$io_pdf->line(150,380,150,440);  // Partida
		$io_pdf->line(170,380,170,420);  // Generica
		$io_pdf->line(190,380,190,420);  // Espec?fica
		$io_pdf->line(220,380,220,460);  // SubEspec?fica
		$io_pdf->line(265,380,265,420);  // Compromiso Programado Mensual
		$io_pdf->line(310,380,310,420);  // Compromiso Programado Acumulado Mensual
		$io_pdf->line(355,380,355,420);  // Compromiso Ejecutado Mensual
		$io_pdf->line(400,380,400,420);  // Compromiso Ejecutado Acumulado Mensual
		$io_pdf->line(435,380,435,405);  // Compromiso Variacion Absoluta Mensual
		$io_pdf->addText(435,390,5,"%");
		$io_pdf->line(440,380,440,420);  // % Compromiso Variacion Absoluta Mensual
		$io_pdf->line(475,380,475,405);  // Compromiso Variacion Absoluta Acumulada
		$io_pdf->addText(475,390,5,"%");
		$io_pdf->line(480,380,480,440);  // % Compromiso Variacion Absoluta Acumulada
		$io_pdf->line(525,380,525,420);  // Causado Programado Mensual
		$io_pdf->line(570,380,570,420);  // Causado Programado Acumulado Mensual
		$io_pdf->line(615,380,615,420);  // Causado Ejecutado Mensual
		$io_pdf->line(660,380,660,420);  // Causado Ejecutado Acumulado Mensual
		$io_pdf->line(695,380,695,405);  // Causado Variacion Absoluta Mensual
		$io_pdf->addText(695,390,5,"%");
		$io_pdf->line(700,380,700,420);  // % Causado Variacion Absoluta Mensual
		$io_pdf->line(735,380,735,405);  // Causado Variacion Absoluta Acumulada
		$io_pdf->addText(735,390,5,"%");
		$io_pdf->line(740,380,740,440);  // % Causado Variacion Absoluta Acumulada
		$io_pdf->line(785,380,785,420);  // Pagado Programado Mensual
		$io_pdf->line(830,380,830,420);  // Pagado Programado Acumulado Mensual
		$io_pdf->line(875,380,875,420);  // Pagado Ejecutado Mensual
		$io_pdf->line(920,380,920,420);  // Pagado Ejecutado Acumulado Mensual
		$io_pdf->line(955,380,955,405);  // Pagado Variacion Absoluta Mensual
		$io_pdf->addText(955,390,5,"%");
		$io_pdf->line(960,380,960,420);  // % Pagado Variacion Absoluta Mensual
		$io_pdf->line(995,380,995,405);  // Pagado Variacion Absoluta Acumulada
		$io_pdf->addText(995,390,5,"%");
		
		
		$io_pdf->addText(12,400,7,"CODIGO");
		$io_pdf->addText(60,400,7,"DENOMINACION");
		$io_pdf->addText(50,450,7,"<b>PROYECTO O ACCION CENTRALIZADA<b>");
		$io_pdf->addText(600,450,7,"<b>MONTO<b>");
		$io_pdf->addText(165,425,7,"SUB-PART");
		$io_pdf->addText(320,425,7,"COMPROMISO");
		$io_pdf->addText(590,425,7,"CAUSADO");
		$io_pdf->addText(850,425,7,"PAGADO");
		$io_pdf->addText(132,400,6,"PART");
		$io_pdf->addText(152,400,6,"GEN");
		$io_pdf->addText(172,400,6,"ESP");
		$io_pdf->addText(192,400,6,"SUB-ESP");
		// COMPROMISO
		$io_pdf->addText(221,400,6,"PROGRAMADO");
		$io_pdf->addText(228,390,6,"MENSUAL");
		$io_pdf->addText(266,400,6,"PROGRAMADO");
		$io_pdf->addText(268,390,6,"ACUMULADO");
		$io_pdf->addText(315,400,6,"EJECUTADO");
		$io_pdf->addText(318,390,6,"MENSUAL");
		$io_pdf->addText(360,400,6,"EJECUTADO");
		$io_pdf->addText(358,390,6,"ACUMULADO");
		$io_pdf->addText(403,407,6,"VARIACION");
		$io_pdf->addText(403,395,5,"ABSOLUTA");
		$io_pdf->addText(404,388,5,"MENSUAL");
		$io_pdf->addText(442,407,6,"VARIACION");
		$io_pdf->addText(442,395,5,"ABSOLUTA");
		$io_pdf->addText(441,388,5,"ACUMULADA");
		// CAUSADO
		$io_pdf->addText(481,400,6,"PROGRAMADO");
		$io_pdf->addText(488,390,6,"MENSUAL");
		$io_pdf->addText(526,400,6,"PROGRAMADO");
		$io_pdf->addText(528,390,6,"ACUMULADO");
		$io_pdf->addText(575,400,6,"EJECUTADO");
		$io_pdf->addText(578,390,6,"MENSUAL");
		$io_pdf->addText(620,400,6,"EJECUTADO");
		$io_pdf->addText(618,390,6,"ACUMULADO");
		$io_pdf->addText(663,407,6,"VARIACION");
		$io_pdf->addText(663,395,5,"ABSOLUTA");
		$io_pdf->addText(664,388,5,"MENSUAL");
		$io_pdf->addText(702,407,6,"VARIACION");
		$io_pdf->addText(702,395,5,"ABSOLUTA");
		$io_pdf->addText(701,388,5,"ACUMULADA");
		// PAGADO
		$io_pdf->addText(741,400,6,"PROGRAMADO");
		$io_pdf->addText(748,390,6,"MENSUAL");
		$io_pdf->addText(788,400,6,"PROGRAMADO");
		$io_pdf->addText(790,390,6,"ACUMULADO");
		$io_pdf->addText(837,400,6,"EJECUTADO");
		$io_pdf->addText(838,390,6,"MENSUAL");
		$io_pdf->addText(880,400,6,"EJECUTADO");
		$io_pdf->addText(878,390,6,"ACUMULADO");
		$io_pdf->addText(923,407,6,"VARIACION");
		$io_pdf->addText(923,395,5,"ABSOLUTA");
		$io_pdf->addText(924,388,5,"MENSUAL");
		$io_pdf->addText(962,407,6,"VARIACION");
		$io_pdf->addText(962,395,5,"ABSOLUTA");
		$io_pdf->addText(961,388,5,"ACUMULADA");
		
		$io_pdf->rectangle(10,460,990,120);
		$io_pdf->rectangle(10,382,990,78);
		
		$li_tm=$io_pdf->getTextWidth(16,$as_titulo);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,500,16,$as_titulo); // Agregar el t?tulo
		
		$li_tm=$io_pdf->getTextWidth(16,$as_moneda);
		$tm=490-($li_tm/2);
		$io_pdf->addText($tm,485,10,$as_moneda); // Agregar el t?tulo
		
		// Fecha
		$io_pdf->line(900,500,900,520);
		$io_pdf->line(900,500,970,500);
		$io_pdf->line(920,500,920,520);
		$io_pdf->line(940,500,940,520);
		$io_pdf->line(970,500,970,520);
		$io_pdf->addText(915,525,10,"FECHA");
		$io_pdf->addText(905,515,10,$_SESSION["ls_database"]);// Agrerar el nombre de la base de datos actual
		$io_pdf->addText(905,505,10,date("d"));
		$io_pdf->addText(925,505,10,date("m"));
		$io_pdf->addText(945,505,10,date("Y"));
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_reporte($io_encabezado,$as_programatica,$ai_ano,$as_mes,$as_denestpro,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   as_periodo_comp // Descripci?n del periodo del comprobante
		//	    		   as_fecha_comp // Descripci?n del per?odo de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Arnaldo Su?rez
		// Fecha Creaci?n: 14/10/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->saveState();
		$io_pdf->ezSetY(570);
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$ls_nomorgads=$_SESSION["la_empresa"]["nomorgads"];
		$ls_codasiona   = $_SESSION['la_empresa']['codasiona'];
		
		$la_data=array(array('name'=>'<b>CODIGO DEL ORGANO:     </b>'.'<b>'.$ls_codasiona.'</b>'),
		               array('name'=>'<b>DENOMINACION:    </b>'.'<b>'.$ls_nombre.'</b>'),
					   array('name'=>'<b>MES:    </b>'.'<b>'.$as_mes." ".$ai_ano.'</b>'));
		$la_columna=array('name'=>'','name'=>'','name'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 8,       // Tama?o de Letras
						 'titleFontSize' => 8, // Tama?o de Letras de los t?tulos
						 'showLines'=>0,        // Mostrar L?neas
						 'shaded'=>0,           // Sombra entre l?neas
						 'xPos'=>465,//65
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Arnaldo Su?rez
		// Fecha Creaci?n: 28/10/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 5, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array(	 'codigo'=>array('justification'=>'left','width'=>37),
										 'denominacion'=>array('justification'=>'left','width'=>85),
										 'partida'=>array('justification'=>'center','width'=>20),
										 'generica'=>array('justification'=>'center','width'=>20),
										 'especifica'=>array('justification'=>'center','width'=>20),
										 'subespecifica'=>array('justification'=>'center','width'=>30),
										 'comp_prog_mensual'=>array('justification'=>'right','width'=>45),
										 'comp_prog_acum'=>array('justification'=>'right','width'=>45),
										 'comp_eje_mens'=>array('justification'=>'right','width'=>45),
										 'comp_eje_acum'=>array('justification'=>'right','width'=>45),
										 'comp_vari_abs_mens'=>array('justification'=>'right','width'=>35),
										 'comp_porc_vari_abs_mens'=>array('justification'=>'center','width'=>5),
										 'comp_vari_abs_acum'=>array('justification'=>'right','width'=>35),
										 'comp_porc_vari_abs_acum'=>array('justification'=>'center','width'=>5),
										 'caus_prog_mensual'=>array('justification'=>'right','width'=>45),
										 'caus_prog_acum'=>array('justification'=>'right','width'=>45),
										 'caus_eje_mens'=>array('justification'=>'right','width'=>45),
										 'caus_eje_acum'=>array('justification'=>'right','width'=>45),
										 'caus_vari_abs_mens'=>array('justification'=>'right','width'=>35),
										 'caus_porc_vari_abs_mens'=>array('justification'=>'center','width'=>5),
										 'caus_vari_abs_acum'=>array('justification'=>'right','width'=>35),
										 'caus_porc_vari_abs_acum'=>array('justification'=>'center','width'=>5),
										 'paga_prog_mensual'=>array('justification'=>'right','width'=>45),
										 'paga_prog_acum'=>array('justification'=>'right','width'=>45),
										 'paga_eje_mens'=>array('justification'=>'right','width'=>45),
										 'paga_eje_acum'=>array('justification'=>'right','width'=>45),
										 'paga_vari_abs_mens'=>array('justification'=>'right','width'=>35),
										 'paga_porc_vari_abs_mens'=>array('justification'=>'center','width'=>5),
										 'paga_vari_abs_acum'=>array('justification'=>'right','width'=>35),
										 'paga_porc_vari_abs_acum'=>array('justification'=>'center','width'=>5))); // Justificaci?n y ancho de la columna
		$la_columnas=array(  'codigo'=>'',
							 'denominacion'=>'',
							 'partida'=>'',
							 'generica'=>'',
							 'especifica'=>'',
							 'subespecifica'=>'',
							 'comp_prog_mensual'=>'',
							 'comp_prog_acum'=>'',
							 'comp_eje_mens'=>'',
							 'comp_eje_acum'=>'',
							 'comp_vari_abs_mens'=>'',
							 'comp_porc_vari_abs_mens'=>'',
							 'comp_vari_abs_acum'=>'',
							 'comp_porc_vari_abs_acum'=>'',
							 'caus_prog_mensual'=>'',
							 'caus_prog_acum'=>'',
							 'caus_eje_mens'=>'',
							 'caus_eje_acum'=>'',
							 'caus_vari_abs_mens'=>'',
							 'caus_porc_vari_abs_mens'=>'',
							 'caus_vari_abs_acum'=>'',
							 'caus_porc_vari_abs_acum'=>'',
							 'paga_prog_mensual'=>'',
							 'paga_prog_acum'=>'',
							 'paga_eje_mens'=>'',
							 'paga_eje_acum'=>'',
							 'paga_vari_abs_mens'=>'',
							 'paga_porc_vari_abs_mens'=>'',
							 'paga_vari_abs_acum'=>'',
							 'paga_porc_vari_abs_acum'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($la_data_tot,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : funci?n que imprime el fin de la cabecera de cada p?gina
		//	   Creado Por: Ing. Arnaldo US?rez
		// Fecha Creaci?n: 10/06/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 5, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array(  'totales'=>array('justification'=>'center','width'=>212), // Justificaci?n y ancho de la columna
						 			     'comp_prog_mensual'=>array('justification'=>'right','width'=>45),
										 'comp_prog_acum'=>array('justification'=>'right','width'=>45),
										 'comp_eje_mens'=>array('justification'=>'right','width'=>45),
										 'comp_eje_acum'=>array('justification'=>'right','width'=>45),
										 'comp_vari_abs_mens'=>array('justification'=>'right','width'=>35),
										 'comp_porc_vari_abs_mens'=>array('justification'=>'center','width'=>5),
										 'comp_vari_abs_acum'=>array('justification'=>'right','width'=>35),
										 'comp_porc_vari_abs_acum'=>array('justification'=>'center','width'=>5),
										 'caus_prog_mensual'=>array('justification'=>'right','width'=>45),
										 'caus_prog_acum'=>array('justification'=>'right','width'=>45),
										 'caus_eje_mens'=>array('justification'=>'right','width'=>45),
										 'caus_eje_acum'=>array('justification'=>'right','width'=>45),
										 'caus_vari_abs_mens'=>array('justification'=>'right','width'=>35),
										 'caus_porc_vari_abs_mens'=>array('justification'=>'center','width'=>5),
										 'caus_vari_abs_acum'=>array('justification'=>'right','width'=>35),
										 'caus_porc_vari_abs_acum'=>array('justification'=>'center','width'=>5),
										 'paga_prog_mensual'=>array('justification'=>'right','width'=>45),
										 'paga_prog_acum'=>array('justification'=>'right','width'=>45),
										 'paga_eje_mens'=>array('justification'=>'right','width'=>45),
										 'paga_eje_acum'=>array('justification'=>'right','width'=>45),
										 'paga_vari_abs_mens'=>array('justification'=>'right','width'=>35),
										 'paga_porc_vari_abs_mens'=>array('justification'=>'center','width'=>5),
										 'paga_vari_abs_acum'=>array('justification'=>'right','width'=>35),
										 'paga_porc_vari_abs_acum'=>array('justification'=>'center','width'=>5))); // Justificaci?n y ancho de la columna
		$la_columnas=array(  'totales'=>'',
						   	 'comp_prog_mensual'=>'',
							 'comp_prog_acum'=>'',
							 'comp_eje_mens'=>'',
							 'comp_eje_acum'=>'',
							 'comp_vari_abs_mens'=>'',
							 'comp_porc_vari_abs_mens'=>'',
							 'comp_vari_abs_acum'=>'',
							 'comp_porc_vari_abs_acum'=>'',
							 'caus_prog_mensual'=>'',
							 'caus_prog_acum'=>'',
							 'caus_eje_mens'=>'',
							 'caus_eje_acum'=>'',
							 'caus_vari_abs_mens'=>'',
							 'caus_porc_vari_abs_mens'=>'',
							 'caus_vari_abs_acum'=>'',
							 'caus_porc_vari_abs_acum'=>'',
							 'paga_prog_mensual'=>'',
							 'paga_prog_acum'=>'',
							 'paga_eje_mens'=>'',
							 'paga_eje_acum'=>'',
							 'paga_vari_abs_mens'=>'',
							 'paga_porc_vari_abs_mens'=>'',
							 'paga_vari_abs_acum'=>'',
							 'paga_porc_vari_abs_acum'=>'');
		$io_pdf->ezTable($la_data_tot,$la_columnas,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();	
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report=new sigesp_spg_funciones_reportes();	
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
//-----------------------------------------------------------------------------------------------------------------------------
		global $la_data_tot;
		require_once("sigesp_spg_class_reportes_instructivo_06.php");
		$io_report = new sigesp_spg_class_reportes_instructivo_06();
		 
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];

		$ls_cmbmes=$_GET["mes"];
		$ls_tipo=$_GET["tipo"];
		switch($ls_cmbmes)
		{
		 case '01': $ls_mes = "ENERO";
		 break;
		 
		 case '02': $ls_mes = "FEBRERO";
		 break;
		 
		 case '03': $ls_mes = "MARZO";
		 break;
		 
		 case '04': $ls_mes = "ABRIL";
		 break;
		 
		 case '05': $ls_mes = "MAYO";
		 break;
		 
		 case '06': $ls_mes = "JUNIO";
		 break;
		 
		 case '07': $ls_mes = "JULIO";
		 break;
		 
		 case '08': $ls_mes = "AGOSTO";
		 break;
		 
		 case '09': $ls_mes = "SEPTIEMBRE";
		 break;
		 
		 case '10': $ls_mes = "OCTUBRE";
		 break;
		 
		 case '11': $ls_mes = "NOVIEMBRE";
		 break;
		 
		 case '12': $ls_mes = "DICIEMBRE";
		 break;
		
		}
		$li_mesdes=substr($ls_cmbmes,0,2);
		$ldt_fecdes=$li_ano."-".$ls_cmbmes."-01";
		$li_meshas=substr($ls_cmbmes,2,2);
		$ldt_ult_dia=$io_fecha->uf_last_day($ls_cmbmes,$li_ano);
		$fechas=$ldt_ult_dia;
		$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);	
		
//----------------------------------------------------  Par?metros del encabezado  ---------------------------------------------
		$ls_titulo="INFORMACION MENSUAL DE LA EJECUCION FINANCIERA";       
//--------------------------------------------------------------------------------------------------------------------------------
   
     $lb_valido=$io_report->uf_spg_reporte_informacion_mensual_eje_fin($ldt_fecdes,$ldt_fechas,$ls_tipo);

	 if($lb_valido==false) // Existe alg?n error ? no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
	 	
	 	
	 	/*
	    
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		uf_print_encabezado_pagina($ls_titulo,"(En Bolivar Fuerte)",$ls_mes,$io_pdf); // Imprimimos el encabezado de la p?gina
 	    $io_pdf->ezStartPageNumbers(980,40,10,'','',1); // Insertar el n?mero de p?gina
		$io_pdf->transaction('start'); // Iniciamos la transacci?n
		$thisPageNum=$io_pdf->ezPageCount;
	 */

	 		 	
	 	$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		$lo_titulo2= &$lo_libro->addformat();
		$lo_titulo2->set_bold();
		$lo_titulo2->set_font("Verdana");
		$lo_titulo2->set_align('center');
		$lo_titulo2->set_size('9');
		$lo_titulo2->set_merge();
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_dataright2= &$lo_libro->addformat();
		$lo_dataright2->set_font("Verdana");
		$lo_dataright2->set_bold();
		$lo_dataright2->set_align('right');
		$lo_dataright2->set_size('9');
		$lo_hoja->set_column(0,0,15);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,30);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,13);
		$lo_hoja->set_column(5,7,30);
	    $ls_spg_cuenta_ant="";
		$ld_total_asignado=0;
		$ld_total_aumento=0;
		$ld_total_disminucion=0;
		$ld_total_monto_actualizado=0;
		$ld_total_compromiso=0;
		$ld_total_precompromiso=0;
		$ld_total_compromiso=0;
		$ld_total_saldo_comprometer=0;
		$ld_total_causado=0;
		$ld_total_pagado=0;
		$ld_total_por_paga=0;
		$li_row=2;
		$z=0;
		$contlineas=0;
		
		$li_tot=$io_report->dts_reporte->getRowCount("codigo");
	    $ld_total_programado_mensual   = 0;
		$ld_total_programado_acumulado = 0;
		$ld_total_ejecutado_mens_comp  = 0;
		$ld_total_ejecutado_mens_caus  = 0;
		$ld_total_ejecutado_mens_paga  = 0;
		$ld_total_ejecutado_acum_comp  = 0;
		$ld_total_ejecutado_acum_caus  = 0;
		$ld_total_ejecutado_acum_paga  = 0;
		$ld_total_variacion_mens_comp  = 0;
	    $ld_total_variacion_acum_comp  = 0;
	    $ld_total_variacion_mens_caus  = 0;
	    $ld_total_variacion_acum_caus  = 0;
	    $ld_total_variacion_mens_paga  = 0;
	    $ld_total_variacion_acum_paga  = 0;
		$ls_mesdes = "";	
		$thisPageNum=$io_pdf->ezPageCount;
		
		
	
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$ls_nomorgads=$_SESSION["la_empresa"]["nomorgads"];
		$ls_codasiona   = $_SESSION['la_empresa']['codasiona'];
		$lo_hoja->write(0, 0,"C?DIGO DEL ?RGANO",$lo_titulo);
		$lo_hoja->write(0, 1,$ls_codasiona,$lo_titulo);
		$lo_hoja->write(1, 0,"DENOMINACI?N",$lo_titulo);
		$lo_hoja->write(1, 1,$ls_nombre,$lo_titulo);
		$lo_hoja->write(2, 0,"MES",$lo_titulo);
		$lo_hoja->write(2, 1,"{$ls_mes} {$li_ano}",$lo_titulo);	
	 	$lo_hoja->write(4, 5,$ls_titulo,$lo_titulo);
	 	$lo_hoja->write(4, 9,"".date('d')."/".date('m')."/".date('Y'),$lo_titulo);
		
	 	
	 			
		$lo_hoja->write(6,0,"PROYECTO O ACCION CENTRALIZADA",$lo_titulo2);
		$lo_hoja->write(6,1,"",$lo_titulo2);
		$lo_hoja->write(6,2,"",$lo_titulo2);
		$lo_hoja->write(6,3,"",$lo_titulo2);
		$lo_hoja->write(6,4,"",$lo_titulo2);	
		
		$lo_hoja->write(6,5,"MONTO",$lo_titulo2);
		$lo_hoja->write(6,6,"",$lo_titulo2);
		$lo_hoja->write(6,7,"",$lo_titulo2);
		
		
		/*
		$lo_hoja->write(6,8,"",$lo_titulo2);
		$lo_hoja->write(6,9,"",$lo_titulo2);	
		$lo_hoja->write(6,10,"",$lo_titulo2);	
		$lo_hoja->write(6,11,"",$lo_titulo2);	
		$lo_hoja->write(6,12,"",$lo_titulo2);	
		$lo_hoja->write(6,13,"",$lo_titulo2);
		$lo_hoja->write(6,14,"",$lo_titulo2);	
		$lo_hoja->write(6,15,"",$lo_titulo2);	
		$lo_hoja->write(6,16,"",$lo_titulo2);
		$lo_hoja->write(6,17,"",$lo_titulo2);
		*/
		
		$lo_hoja->write(7,0,"C?DIGO",$lo_titulo);
		$lo_hoja->write(7,1,"DENOMINACION",$lo_titulo);
		$lo_hoja->write(7,2,"",$lo_titulo);
		$lo_hoja->write(7,3,"SUB-PARTIDA",$lo_titulo2);
		$lo_hoja->write(7,4,"",$lo_titulo2);
		$lo_hoja->write(7,5,"",$lo_titulo2);
		
		$lo_hoja->write(7,6,"COMPROMISO",$lo_titulo2);
		$lo_hoja->write(7,7,"",$lo_titulo2);
		$lo_hoja->write(7,8,"",$lo_titulo2);
		$lo_hoja->write(7,9,"",$lo_titulo2);
		$lo_hoja->write(7,10,"",$lo_titulo2);
		$lo_hoja->write(7,11,"",$lo_titulo2);
			
		$lo_hoja->write(7,12,"CAUSADO",$lo_titulo2);
		$lo_hoja->write(7,13,"",$lo_titulo2);
		$lo_hoja->write(7,14,"",$lo_titulo2);
		$lo_hoja->write(7,15,"",$lo_titulo2);
		$lo_hoja->write(7,16,"",$lo_titulo2);
		$lo_hoja->write(7,17,"",$lo_titulo2);

		$lo_hoja->write(7,18,"PAGADO",$lo_titulo2);
		$lo_hoja->write(7,19,"",$lo_titulo2);
		$lo_hoja->write(7,20,"",$lo_titulo2);
		$lo_hoja->write(7,21,"",$lo_titulo2);
		$lo_hoja->write(7,22,"",$lo_titulo2);
		$lo_hoja->write(7,23,"",$lo_titulo2);
		
				
		$lo_hoja->write(8,2,"PART",$lo_titulo);
		$lo_hoja->write(8,3,"GEN",$lo_titulo2);
		$lo_hoja->write(8,4,"ESP",$lo_titulo2);
		$lo_hoja->write(8,5,"SUB-ESP",$lo_titulo2);
		
			
		$lo_hoja->write(8,7,"PROGRAMADO MENSUAL",$lo_titulo);
		$lo_hoja->write(8,8,"PROGRAMADO ACUMULADO",$lo_titulo);
		$lo_hoja->write(8,9,"EJECUTADO MENSUAL",$lo_titulo);	
		$lo_hoja->write(8,10,"EJECUTADO EJECUTADO",$lo_titulo);
		$lo_hoja->write(8,11,"VARIACION",$lo_titulo2);
		$lo_hoja->write(8,12,"",$lo_titulo2);	
		$lo_hoja->write(8,13,"VARIACION",$lo_titulo2);		
		$lo_hoja->write(8,14,"",$lo_titulo2);
		
		$lo_hoja->write(8,15,"PROGRAMADO MENSUAL",$lo_titulo);
		$lo_hoja->write(8,16,"PROGRAMADO ACUMULADO",$lo_titulo);
		$lo_hoja->write(8,17,"EJECUTADO MENSUAL",$lo_titulo);	
		$lo_hoja->write(8,18,"EJECUTADO EJECUTADO",$lo_titulo);
		$lo_hoja->write(8,19,"VARIACION",$lo_titulo2);
		$lo_hoja->write(8,20,"",$lo_titulo2);	
		$lo_hoja->write(8,21,"VARIACION",$lo_titulo2);		
		$lo_hoja->write(8,22,"",$lo_titulo2);
		
		
		$lo_hoja->write(8,23,"PROGRAMADO MENSUAL",$lo_titulo);
		$lo_hoja->write(8,24,"PROGRAMADO ACUMULADO",$lo_titulo);
		$lo_hoja->write(8,25,"EJECUTADO MENSUAL",$lo_titulo);	
		$lo_hoja->write(8,26,"EJECUTADO EJECUTADO",$lo_titulo);
		$lo_hoja->write(8,27,"VARIACION",$lo_titulo2);
		$lo_hoja->write(8,28,"",$lo_titulo2);	
		$lo_hoja->write(8,29,"VARIACION",$lo_titulo2);		
		$lo_hoja->write(8,30,"",$lo_titulo2);
		
	

		$lo_hoja->write(9,10,"ABSOLUTA MENSUAL",$lo_titulo);
		$lo_hoja->write(9,11,"%",$lo_titulo);	
		$lo_hoja->write(9,12,"ABSOLUTA ACUMULADO",$lo_titulo);		
		$lo_hoja->write(9,13,"%",$lo_titulo);
		
		$lo_hoja->write(9,18,"ABSOLUTA MENSUAL",$lo_titulo);
		$lo_hoja->write(9,19,"%",$lo_titulo);	
		$lo_hoja->write(9,20,"ABSOLUTA ACUMULADO",$lo_titulo);		
		$lo_hoja->write(9,21,"%",$lo_titulo);
		
		$lo_hoja->write(9,26,"ABSOLUTA MENSUAL",$lo_titulo);
		$lo_hoja->write(9,27,"%",$lo_titulo);	
		$lo_hoja->write(9,28,"ABSOLUTA ACUMULADO",$lo_titulo);		
		$lo_hoja->write(9,29,"%",$lo_titulo);
		$contlineas=10;
			
		for($z=1;$z<=$li_tot;$z++)
		{		
			$contlineas++;
			$ls_codigo="";
			$ls_denominacion="";
			$ls_spg_cuenta = "";
	        $ls_partida="";
			$ls_generica="";
			$ls_especifica="";
			$ls_subesp="";
			$ls_status="";
			$ld_programado_mensual   = 0;
		    $ld_programado_acumulado = 0;
		    $ld_ejecutado_mens_comp  = 0;
		    $ld_ejecutado_mens_caus  = 0;
			$ld_ejecutado_mens_paga  = 0;
			$ld_ejecutado_acum_comp  = 0;
		    $ld_ejecutado_acum_caus  = 0;
			$ld_ejecutado_acum_paga  = 0;
			$ld_variacion_mens_comp  = 0;
			$ld_variacion_acum_comp  = 0;
			$ld_variacion_mens_caus  = 0;
			$ld_variacion_acum_caus  = 0;
			$ld_variacion_mens_paga  = 0;
			$ld_variacion_acum_paga  = 0;
			

				  $ls_codigo                 = trim($io_report->dts_reporte->data["codigo"][$z]);
				  $ls_denominacion           = trim($io_report->dts_reporte->data["denominacion"][$z]);
				  $ls_spg_cuenta             = trim($io_report->dts_reporte->data["spg_cuenta"][$z]);
				  $arrResultado=$io_function_report->uf_get_spg_cuenta($ls_spg_cuenta,$ls_partida,$ls_generica,$ls_especifica,$ls_subesp,$as_spg_int);
				  $ls_partida=$arrResultado['as_spg_partida'];
				  $ls_generica=$arrResultado['as_spg_generica'];
				  $ls_especifica=$arrResultado['as_spg_especifica'];
				  $ls_subesp=$arrResultado['as_spg_subesp'];
				  $as_spg_int=$arrResultado['as_spg_int'];
				  $ls_status                 = trim($io_report->dts_reporte->data["status"][$z]);
				  $ld_programado_mensual     = $io_report->dts_reporte->data["programado_mensual"][$z];
				  $ld_programado_acumulado   = $io_report->dts_reporte->data["programado_acumulado"][$z];
				  $ld_ejecutado_mens_comp    = $io_report->dts_reporte->data["ejecutado_mens_comp"][$z];
				  $ld_ejecutado_mens_caus    = $io_report->dts_reporte->data["ejecutado_mens_caus"][$z];
				  $ld_ejecutado_mens_paga    = $io_report->dts_reporte->data["ejecutado_mens_paga"][$z];
				  $ld_ejecutado_acum_comp    = $io_report->dts_reporte->data["ejecutado_acum_comp"][$z];
				  $ld_ejecutado_acum_caus    = $io_report->dts_reporte->data["ejecutado_acum_caus"][$z];
				  $ld_ejecutado_acum_paga    = $io_report->dts_reporte->data["ejecutado_acum_paga"][$z];
				  $ld_variacion_mens_comp    = abs($ld_programado_mensual - $ld_ejecutado_mens_comp);
				  $ld_variacion_acum_comp    = abs($ld_programado_acumulado - $ld_ejecutado_acum_comp);
				  $ld_variacion_mens_caus    = abs($ld_programado_mensual - $ld_ejecutado_mens_caus);
				  $ld_variacion_acum_caus    = abs($ld_programado_acumulado - $ld_ejecutado_acum_caus);
				  $ld_variacion_mens_paga    = abs($ld_programado_mensual - $ld_ejecutado_mens_paga);
				  $ld_variacion_acum_paga    = abs($ld_programado_acumulado - $ld_ejecutado_acum_caus);
				  
				  if (($ls_status == "C")&&($ls_tipo == "D"))
				  {
				   $ld_total_programado_mensual   = $ld_total_programado_mensual + $ld_programado_mensual;
				   $ld_total_programado_acumulado = $ld_total_programado_acumulado + $ld_programado_acumulado;
				   $ld_total_ejecutado_mens_comp  = $ld_total_ejecutado_mens_comp + $ld_ejecutado_mens_comp;
				   $ld_total_ejecutado_mens_caus  = $ld_total_ejecutado_mens_caus + $ld_ejecutado_mens_caus;
				   $ld_total_ejecutado_mens_paga  = $ld_total_ejecutado_mens_paga + $ld_ejecutado_mens_paga;
				   $ld_total_ejecutado_acum_comp  = $ld_total_ejecutado_acum_comp + $ld_ejecutado_acum_comp;
			       $ld_total_ejecutado_acum_caus  = $ld_total_ejecutado_acum_caus + $ld_ejecutado_acum_caus;
				   $ld_total_ejecutado_acum_paga  = $ld_total_ejecutado_acum_paga + $ld_ejecutado_acum_paga;
				   $ld_total_variacion_mens_comp  = $ld_total_variacion_mens_comp + $ld_variacion_mens_comp;
				   $ld_total_variacion_acum_comp  = $ld_total_variacion_acum_comp + $ld_variacion_acum_comp;
				   $ld_total_variacion_mens_caus  = $ld_total_variacion_mens_caus + $ld_variacion_mens_caus;
				   $ld_total_variacion_acum_caus  = $ld_total_variacion_acum_caus + $ld_variacion_acum_caus;
				   $ld_total_variacion_mens_paga  = $ld_total_variacion_mens_paga + $ld_variacion_mens_paga;
				   $ld_total_variacion_acum_paga  = $ld_total_variacion_acum_paga + $ld_variacion_acum_paga;
				  }
				  elseif(($ls_status == "S")&&($ls_tipo == "C"))
				  {
				   $ld_total_programado_mensual   = $ld_total_programado_mensual + $ld_programado_mensual;
				   $ld_total_programado_acumulado = $ld_total_programado_acumulado + $ld_programado_acumulado;
				   $ld_total_ejecutado_mens_comp  = $ld_total_ejecutado_mens_comp + $ld_ejecutado_mens_comp;
				   $ld_total_ejecutado_mens_caus  = $ld_total_ejecutado_mens_caus + $ld_ejecutado_mens_caus;
				   $ld_total_ejecutado_mens_paga  = $ld_total_ejecutado_mens_paga + $ld_ejecutado_mens_paga;
				   $ld_total_ejecutado_acum_comp  = $ld_total_ejecutado_acum_comp + $ld_ejecutado_acum_comp;
			       $ld_total_ejecutado_acum_caus  = $ld_total_ejecutado_acum_caus + $ld_ejecutado_acum_caus;
				   $ld_total_ejecutado_acum_paga  = $ld_total_ejecutado_acum_paga + $ld_ejecutado_acum_paga;
				   $ld_total_variacion_mens_comp  = $ld_total_variacion_mens_comp + $ld_variacion_mens_comp;
				   $ld_total_variacion_acum_comp  = $ld_total_variacion_acum_comp + $ld_variacion_acum_comp;
				   $ld_total_variacion_mens_caus  = $ld_total_variacion_mens_caus + $ld_variacion_mens_caus;
				   $ld_total_variacion_acum_caus  = $ld_total_variacion_acum_caus + $ld_variacion_acum_caus;
				   $ld_total_variacion_mens_paga  = $ld_total_variacion_mens_paga + $ld_variacion_mens_paga;
				   $ld_total_variacion_acum_paga  = $ld_total_variacion_acum_paga + $ld_variacion_acum_paga;
				  } 
				  
			      

				  $ld_programado_mensual   = number_format($ld_programado_mensual,2,",",".");
				  $ld_programado_acumulado = number_format($ld_programado_acumulado,2,",",".");
				  $ld_ejecutado_mens_comp  = number_format($ld_ejecutado_mens_comp,2,",",".");
				  $ld_ejecutado_mens_caus  = number_format($ld_ejecutado_mens_caus,2,",",".");
				  $ld_ejecutado_mens_paga  = number_format($ld_ejecutado_mens_paga,2,",",".");
				  $ld_ejecutado_acum_comp  = number_format($ld_ejecutado_acum_comp,2,",",".");
				  $ld_ejecutado_acum_caus  = number_format($ld_ejecutado_acum_caus,2,",",".");
				  $ld_ejecutado_acum_paga  = number_format($ld_ejecutado_acum_paga,2,",",".");
				  $ld_variacion_mens_comp  = number_format($ld_variacion_mens_comp,2,",",".");
				  $ld_variacion_acum_comp  = number_format($ld_variacion_acum_comp,2,",",".");
				  $ld_variacion_mens_caus  = number_format($ld_variacion_mens_caus,2,",",".");
				  $ld_variacion_acum_caus  = number_format($ld_variacion_acum_caus,2,",",".");
				  $ld_variacion_mens_paga  = number_format($ld_variacion_mens_paga,2,",",".");
				  $ld_variacion_acum_paga  = number_format($ld_variacion_acum_paga,2,",",".");
				
				  $la_data[$z]=array('codigo'=>$ls_codigo,
									 'denominacion'=>$ls_denominacion,
									 'partida'=>$ls_partida,
									 'generica'=>$ls_generica,
									 'especifica'=>$ls_especifica,
									 'subespecifica'=>$ls_subesp,
									 'comp_prog_mensual'=>$ld_programado_mensual,
									 'comp_prog_acum'=>$ld_programado_acumulado,
									 'comp_eje_mens'=>$ld_ejecutado_mens_comp,
									 'comp_eje_acum'=>$ld_ejecutado_acum_comp,
									 'comp_vari_abs_mens'=>$ld_variacion_mens_comp,
									 'comp_porc_vari_abs_mens'=>'',
									 'comp_vari_abs_acum'=>$ld_variacion_acum_comp,
									 'comp_porc_vari_abs_acum'=>'',
									 'caus_prog_mensual'=>$ld_programado_mensual,
									 'caus_prog_acum'=>$ld_programado_acumulado,
									 'caus_eje_mens'=>$ld_ejecutado_mens_caus,
									 'caus_eje_acum'=>$ld_ejecutado_acum_caus,
									 'caus_vari_abs_mens'=>$ld_variacion_mens_caus,
									 'caus_porc_vari_abs_mens'=>'',
									 'caus_vari_abs_acum'=>$ld_variacion_acum_caus,
									 'caus_porc_vari_abs_acum'=>'',
									 'paga_prog_mensual'=>$ld_programado_mensual,
									 'paga_prog_acum'=>$ld_programado_acumulado,
									 'paga_eje_mens'=>$ld_ejecutado_mens_paga,
									 'paga_eje_acum'=>$ld_ejecutado_acum_paga,
									 'paga_vari_abs_mens'=>$ld_variacion_mens_paga,
									 'paga_porc_vari_abs_mens'=>'',
									 'paga_vari_abs_acum'=>$ld_variacion_acum_paga,
									 'paga_porc_vari_abs_acum'=>'');
				  
				  
			$lo_hoja->write($contlineas,0," ".$ls_codigo,$lo_dataleft);
			$lo_hoja->write($contlineas,1,$ls_denominacion,$lo_dataleft);
			$lo_hoja->write($contlineas,2,$ls_partida,$lo_datacenter);
			$lo_hoja->write($contlineas,3,$ls_generica,$lo_datacenter);
			$lo_hoja->write($contlineas,4,$ls_especifica,$lo_datacenter);
			$lo_hoja->write($contlineas,5,$ls_subesp,$lo_datacenter);
			if($as_spg_int!='')
			{
				$lo_hoja->write($contlineas,6,$as_spg_int,$lo_datacenter);
				$lo_hoja->write(8,6,"INT",$lo_titulo);
			}
			$lo_hoja->write($contlineas,7,$ld_programado_mensual,$lo_dataright);
			$lo_hoja->write($contlineas,8,$ld_programado_acumulado,$lo_dataright);
			$lo_hoja->write($contlineas,9,$ld_ejecutado_mens_comp,$lo_dataright);
			$lo_hoja->write($contlineas,10,$ld_ejecutado_acum_comp,$lo_dataright);
			$lo_hoja->write($contlineas,11,$ld_variacion_mens_comp,$lo_dataright);
			$lo_hoja->write($contlineas,12,"",$lo_dataright);
			$lo_hoja->write($contlineas,13,$ld_variacion_acum_comp,$lo_dataright);
			$lo_hoja->write($contlineas,14,"",$lo_dataright);
			$lo_hoja->write($contlineas,15,$ld_programado_mensual,$lo_dataright);
			$lo_hoja->write($contlineas,16,$ld_programado_acumulado,$lo_dataright);
			$lo_hoja->write($contlineas,17,$ld_ejecutado_mens_caus,$lo_dataright);
			$lo_hoja->write($contlineas,18,$ld_ejecutado_acum_caus,$lo_dataright);
			$lo_hoja->write($contlineas,19,$ld_variacion_mens_caus,$lo_dataright);
			$lo_hoja->write($contlineas,20,"",$lo_dataright);
			$lo_hoja->write($contlineas,21,$ld_variacion_acum_caus,$lo_dataright);
			$lo_hoja->write($contlineas,22,"",$lo_dataright);
			$lo_hoja->write($contlineas,23,$ld_programado_mensual,$lo_dataright);
			$lo_hoja->write($contlineas,24,$ld_programado_acumulado,$lo_dataright);
			$lo_hoja->write($contlineas,25,$ld_ejecutado_mens_paga,$lo_dataright);
			$lo_hoja->write($contlineas,26,$ld_ejecutado_acum_paga,$lo_dataright);
			$lo_hoja->write($contlineas,27,$ld_variacion_mens_paga,$lo_dataright);
			$lo_hoja->write($contlineas,28,'',$lo_dataright);
			$lo_hoja->write($contlineas,29,$ld_variacion_acum_paga,$lo_dataright);
			$lo_hoja->write($contlineas,30,'',$lo_dataright);
			$contlineas++;	  
					  							 						   
			}//for
			  $ld_total_programado_mensual   = number_format($ld_total_programado_mensual,2,",",".");
			  $ld_total_programado_acumulado = number_format($ld_total_programado_acumulado,2,",",".");
			  $ld_total_ejecutado_mens_comp  = number_format($ld_total_ejecutado_mens_comp,2,",",".");
			  $ld_total_ejecutado_mens_caus  = number_format($ld_total_ejecutado_mens_caus,2,",",".");
			  $ld_total_ejecutado_mens_paga  = number_format($ld_total_ejecutado_mens_paga,2,",",".");
			  $ld_total_ejecutado_acum_comp  = number_format($ld_total_ejecutado_acum_comp,2,",",".");
			  $ld_total_ejecutado_acum_caus  = number_format($ld_total_ejecutado_acum_caus,2,",",".");
			  $ld_total_ejecutado_acum_paga  = number_format($ld_total_ejecutado_acum_paga,2,",",".");
			  $ld_total_variacion_mens_comp  = number_format($ld_total_variacion_mens_comp,2,",",".");
			  $ld_total_variacion_acum_comp  = number_format($ld_total_variacion_acum_comp,2,",",".");
			  $ld_total_variacion_mens_caus  = number_format($ld_total_variacion_mens_caus,2,",",".");
			  $ld_total_variacion_acum_caus  = number_format($ld_total_variacion_acum_caus,2,",",".");
			  $ld_total_variacion_mens_paga  = number_format($ld_total_variacion_mens_paga,2,",",".");
			  $ld_total_variacion_acum_paga  = number_format($ld_total_variacion_acum_paga,2,",",".");
			
			$la_data_tot[1]=array(	 'totales'=>"TOTALES",
								  	 'comp_prog_mensual'=>$ld_total_programado_mensual,
									 'comp_prog_acum'=>$ld_total_programado_acumulado,
									 'comp_eje_mens'=>$ld_total_ejecutado_mens_comp,
									 'comp_eje_acum'=>$ld_total_ejecutado_acum_comp,
									 'comp_vari_abs_mens'=>$ld_total_variacion_mens_comp,
									 'comp_porc_vari_abs_mens'=>'',
									 'comp_vari_abs_acum'=>$ld_total_variacion_acum_comp,
									 'comp_porc_vari_abs_acum'=>'',
									 'caus_prog_mensual'=>$ld_total_programado_mensual,
									 'caus_prog_acum'=>$ld_total_programado_acumulado,
									 'caus_eje_mens'=>$ld_total_ejecutado_mens_caus,
									 'caus_eje_acum'=>$ld_total_ejecutado_acum_caus,
									 'caus_vari_abs_mens'=>$ld_total_variacion_mens_caus,
									 'caus_porc_vari_abs_mens'=>'',
									 'caus_vari_abs_acum'=>$ld_total_variacion_acum_caus,
									 'caus_porc_vari_abs_acum'=>'',
									 'paga_prog_mensual'=>$ld_total_programado_mensual,
									 'paga_prog_acum'=>$ld_total_programado_acumulado,
									 'paga_eje_mens'=>$ld_total_ejecutado_mens_paga,
									 'paga_eje_acum'=>$ld_total_ejecutado_acum_paga,
									 'paga_vari_abs_mens'=>$ld_total_variacion_mens_paga,
									 'paga_porc_vari_abs_mens'=>'',
									 'paga_vari_abs_acum'=>$ld_total_variacion_acum_paga,
									 'paga_porc_vari_abs_acum'=>'');

 			
			$lo_hoja->write($contlineas,0,"",$lo_dataleft);
			$lo_hoja->write($contlineas,1,"",$lo_dataleft);
			$lo_hoja->write($contlineas,2,"",$lo_datacenter);
			$lo_hoja->write($contlineas,3,"",$lo_datacenter);
			$lo_hoja->write($contlineas,4,"",$lo_datacenter);
			$lo_hoja->write($contlineas,5,"TOTAL",$lo_datacenter);
			if($as_spg_int!='')
			{
				$lo_hoja->write($contlineas,6,$as_spg_int,$lo_datacenter);
			}
			$lo_hoja->write($contlineas,7,$ld_total_programado_mensual,$lo_dataright);
			$lo_hoja->write($contlineas,8,$ld_total_programado_acumulado,$lo_dataright);
			$lo_hoja->write($contlineas,9,$ld_total_ejecutado_mens_comp,$lo_dataright);
			$lo_hoja->write($contlineas,10,$ld_total_ejecutado_acum_comp,$lo_dataright);
			$lo_hoja->write($contlineas,11,$ld_total_variacion_mens_comp,$lo_dataright);
			$lo_hoja->write($contlineas,12,"",$lo_dataright);
			$lo_hoja->write($contlineas,13,$ld_total_variacion_acum_comp,$lo_dataright);
			$lo_hoja->write($contlineas,14,"",$lo_dataright);
			$lo_hoja->write($contlineas,15,$ld_total_programado_mensual,$lo_dataright);
			$lo_hoja->write($contlineas,16,$ld_total_programado_acumulado,$lo_dataright);
			$lo_hoja->write($contlineas,17,$ld_total_ejecutado_mens_caus,$lo_dataright);
			$lo_hoja->write($contlineas,18,$ld_total_ejecutado_acum_caus,$lo_dataright);
			$lo_hoja->write($contlineas,19,$ld_total_variacion_mens_caus,$lo_dataright);
			$lo_hoja->write($contlineas,20,"",$lo_dataright);
			$lo_hoja->write($contlineas,21,$ld_total_variacion_acum_caus,$lo_dataright);
			$lo_hoja->write($contlineas,22,"",$lo_dataright);
			$lo_hoja->write($contlineas,23,$ld_total_programado_mensual,$lo_dataright);
			$lo_hoja->write($contlineas,24,$ld_total_programado_acumulado,$lo_dataright);
			$lo_hoja->write($contlineas,25,$ld_total_ejecutado_mens_paga,$lo_dataright);
			$lo_hoja->write($contlineas,26,$ld_total_ejecutado_acum_paga,$lo_dataright);
			$lo_hoja->write($contlineas,27,$ld_total_variacion_mens_paga,$lo_dataright);
			$lo_hoja->write($contlineas,28,'',$lo_dataright);
			$lo_hoja->write($contlineas,29,$ld_total_variacion_acum_paga,$lo_dataright);
			$lo_hoja->write($contlineas,30,'',$lo_dataright);
			$contlineas++;	  
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"INFORMACION_MENSUAL_DE_LA_EJECUCION_FINANCIERA.xls\"");
			header("Content-Disposition: inline; filename=\"INFORMACION_MENSUAL_DE_LA_EJECUCION_FINANCIERA.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
			print("<script language=JavaScript>");
			print(" close();");
			print("</script>");
			unset($class_report);
			unset($io_funciones);
			unset($la_data);
			unset($la_data_tot);					
			unset($la_data);
			unset($la_data_tot);							
			
	}//else
	unset($io_report);
	unset($io_funciones);
?> 