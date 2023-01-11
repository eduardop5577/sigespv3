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
	$lo_archivo =  tempnam("/tmp", "EJECUCIÓN_FINANCIERA_DE_LOS_PROYECTOS/ACCIONES_CENTRALIZADAS_DEL_ORGANO.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_moneda,$as_trimestre,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//                 $as_moneda // Moneda
		//	    		   as_trimestre // Nro. del Trimestre
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 29/10/2008
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
		$io_pdf->line(190,380,190,420);  // Específica
		$io_pdf->line(220,380,220,460);  // SubEspecífica
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
		$io_pdf->addText(228,390,4,$as_trimestre);
		$io_pdf->addText(266,400,6,"PROGRAMADO");
		$io_pdf->addText(268,390,6,"ACUMULADO");
		$io_pdf->addText(315,400,6,"EJECUTADO");
		$io_pdf->addText(318,390,4,$as_trimestre);
		$io_pdf->addText(360,400,6,"EJECUTADO");
		$io_pdf->addText(358,390,6,"ACUMULADO");
		$io_pdf->addText(403,407,6,"VARIACION");
		$io_pdf->addText(403,395,5,"ABSOLUTA");
		$io_pdf->addText(404,388,4,$as_trimestre);
		$io_pdf->addText(442,407,6,"VARIACION");
		$io_pdf->addText(442,395,5,"ABSOLUTA");
		$io_pdf->addText(441,388,5,"ACUMULADA");
		// CAUSADO
		$io_pdf->addText(481,400,6,"PROGRAMADO");
		$io_pdf->addText(488,390,4,$as_trimestre);
		$io_pdf->addText(526,400,6,"PROGRAMADO");
		$io_pdf->addText(528,390,6,"ACUMULADO");
		$io_pdf->addText(575,400,6,"EJECUTADO");
		$io_pdf->addText(578,390,4,$as_trimestre);
		$io_pdf->addText(620,400,6,"EJECUTADO");
		$io_pdf->addText(618,390,6,"ACUMULADO");
		$io_pdf->addText(663,407,6,"VARIACION");
		$io_pdf->addText(663,395,5,"ABSOLUTA");
		$io_pdf->addText(664,388,4,$as_trimestre);
		$io_pdf->addText(702,407,6,"VARIACION");
		$io_pdf->addText(702,395,5,"ABSOLUTA");
		$io_pdf->addText(701,388,5,"ACUMULADA");
		// PAGADO
		$io_pdf->addText(741,400,6,"PROGRAMADO");
		$io_pdf->addText(748,390,4,$as_trimestre);
		$io_pdf->addText(788,400,6,"PROGRAMADO");
		$io_pdf->addText(790,390,6,"ACUMULADO");
		$io_pdf->addText(837,400,6,"EJECUTADO");
		$io_pdf->addText(838,390,4,$as_trimestre);
		$io_pdf->addText(880,400,6,"EJECUTADO");
		$io_pdf->addText(878,390,6,"ACUMULADO");
		$io_pdf->addText(923,407,6,"VARIACION");
		$io_pdf->addText(923,395,5,"ABSOLUTA");
		$io_pdf->addText(924,388,4,$as_trimestre);
		$io_pdf->addText(962,407,6,"VARIACION");
		$io_pdf->addText(962,395,5,"ABSOLUTA");
		$io_pdf->addText(961,388,5,"ACUMULADA");
		
		$io_pdf->rectangle(10,460,990,120);
		$io_pdf->rectangle(10,382,990,78);
		
		$li_tm=$io_pdf->getTextWidth(16,$as_titulo);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,500,16,$as_titulo); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(16,$as_moneda);
		$tm=490-($li_tm/2);
		$io_pdf->addText($tm,485,10,$as_moneda); // Agregar el título
		
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
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 14/10/2008 
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
						 'fontSize' => 8,       // Tamaño de Letras
						 'titleFontSize' => 8, // Tamaño de Letras de los títulos
						 'showLines'=>0,        // Mostrar Líneas
						 'shaded'=>0,           // Sombra entre líneas
						 'xPos'=>465,//65
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
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
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 28/10/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 5, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
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
										 'paga_porc_vari_abs_acum'=>array('justification'=>'center','width'=>5))); // Justificación y ancho de la columna
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
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Arnaldo USárez
		// Fecha Creación: 10/06/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 5, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array(  'totales'=>array('justification'=>'center','width'=>212), // Justificación y ancho de la columna
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
										 'paga_porc_vari_abs_acum'=>array('justification'=>'center','width'=>5))); // Justificación y ancho de la columna
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
		 
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];

		$ls_cmbmes=$_GET["mes"];
		$ls_tipo="C";
		$ls_mes="";
		$ls_tmes="";
		$ls_cmbTmes=$_GET["Tmes"];
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
		switch($ls_cmbTmes)
		{
		 case '0103': $ls_tmes = "ENERO - MARZO";
		 break;
		 
		 case '0406': $ls_tmes = "ABRIL - JUNIO";
		 break;
		 
		 case '0709': $ls_tmes = "JULIO - SEPTIEMBRE";
		 break;
		 
		 case '1012': $ls_tmes = "OCTUBRE - DICIEMBRE";
		 break;
		 
		}
		if($ls_mes!="")
		{
			$li_mesdes=substr($ls_cmbmes,0,2);
			$ldt_fecdes=$li_ano."-".$ls_cmbmes."-01";
			$li_meshas=substr($ls_cmbmes,2,2);
			$ldt_ult_dia=$io_fecha->uf_last_day($ls_cmbmes,$li_ano);
			$fechas=$ldt_ult_dia;
			$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
			$ls_mesaux=$ls_mes;
			$ls_mesaux1="MENSUAL";
			$ls_mentri="MES";
		}
		if($ls_tmes!="")
		{
			$li_mesdes=substr($ls_cmbTmes,0,2);
			$ldt_fecdes=$li_ano."-".$li_mesdes."-01";
			$li_meshas=substr($ls_cmbTmes,2,2);
			$ldt_ult_dia=$io_fecha->uf_last_day($li_meshas,$li_ano);
			$fechas=$ldt_ult_dia;
			$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
			$ls_mesaux=$ls_tmes;
			$ls_mesaux1="TRIMESTRAL";
			$ls_mentri="TRIMESTRE";
		}
//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
		$ls_titulo=" EJECUCION FINANCIERA ".$ls_mesaux1." DE LOS PROYECTOS/ACCIONES CENTRALIZADAS DEL ORGANO POR PARTIDAS DE EGRESO";       
//--------------------------------------------------------------------------------------------------------------------------------
   
     $lb_valido=$io_report->uf_spg_reporte_informacion_mensual_eje_fin($ldt_fecdes,$ldt_fechas,$ls_tipo);

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
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
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
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$ls_nomorgads=$_SESSION["la_empresa"]["nomorgads"];
		$ls_codasiona   = $_SESSION['la_empresa']['codasiona'];

		$lo_hoja->write(0, 0,"CÓDIGO DE ORGANISMO",$lo_titulo);
		$lo_hoja->write(0, 1,$ls_codasiona,$lo_titulo);
		$lo_hoja->write(1, 0,"DENOMINACIÓN",$lo_titulo);
		$lo_hoja->write(1, 1,$ls_nombre,$lo_titulo);
		$lo_hoja->write(2, 0,$ls_mentri,$lo_titulo);
		$lo_hoja->write(2, 1,"{$ls_mesaux} {$li_ano}",$lo_titulo);
		
	 	$lo_hoja->write(4, 5,$ls_titulo,$lo_titulo);
	 	$lo_hoja->write(4, 9,"".date('d')."/".date('m')."/".date('Y'),$lo_titulo);
		$la_data='';

		$lo_hoja->write(6,0,"PROYECTO O ACCION CENTRALIZADA",$lo_datacenter);
		$lo_hoja->write(9,0,"CODIGO",$lo_datacenter);
		$lo_hoja->write(9,1,"DENOMINACION",$lo_datacenter);
		$lo_hoja->write(9,2,"PART",$lo_datacenter);
		$lo_hoja->write(7,3,"SUB-PART",$lo_datacenter);
		$lo_hoja->write(9,3,"GEN",$lo_datacenter);
		$lo_hoja->write(9,4,"ESP",$lo_datacenter);
		$lo_hoja->write(9,5,"SUB ESP",$lo_datacenter);
		$lo_hoja->write(7,6,"COMPROMISO",$lo_datacenter);
		$lo_hoja->write(9,6,"PROGAMADO ".$ls_mesaux1,$lo_datacenter);
		$lo_hoja->write(9,7,"PROGAMADO ACUMULADO",$lo_datacenter);
		$lo_hoja->write(9,8,"EJECUTADO".$ls_mesaux1,$lo_datacenter);
		$lo_hoja->write(9,9,"EJECUTADO ACUMULADO",$lo_datacenter);
		$lo_hoja->write(8,10,"VARIACION",$lo_datacenter);
		$lo_hoja->write(9,10,"ABSOLUTA".$ls_mesaux1,$lo_datacenter);
		$lo_hoja->write(9,11,"%",$lo_datacenter);
		$lo_hoja->write(8,12,"VARIACION",$lo_datacenter);
		$lo_hoja->write(9,12,"ABSOLUTA ACUMULADO",$lo_datacenter);
		$lo_hoja->write(9,13,"%",$lo_datacenter);
/////////////////////////////////////////////////////////////////////////////
		$lo_hoja->write(6,6,"MONTO",$lo_datacenter);
		$lo_hoja->write(7,14,"CAUSADO",$lo_datacenter);
		$lo_hoja->write(9,14,"PROGAMADO ".$ls_mesaux1,$lo_datacenter);
		$lo_hoja->write(9,15,"PROGAMADO ACUMULADO",$lo_datacenter);
		$lo_hoja->write(9,16,"EJECUTADO".$ls_mesaux1,$lo_datacenter);
		$lo_hoja->write(9,17,"EJECUTADO ACUMULADO",$lo_datacenter);
		$lo_hoja->write(8,18,"VARIACION",$lo_datacenter);
		$lo_hoja->write(9,18,"ABSOLUTA".$ls_mesaux1,$lo_datacenter);
		$lo_hoja->write(9,19,"%",$lo_datacenter);
		$lo_hoja->write(8,20,"VARIACION",$lo_datacenter);
		$lo_hoja->write(9,20,"ABSOLUTA ACUMULADO",$lo_datacenter);
		$lo_hoja->write(9,21,"%",$lo_datacenter);
	
/////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////
		$lo_hoja->write(7,22,"PAGADO",$lo_datacenter);
		$lo_hoja->write(9,22,"PROGAMADO ".$ls_mesaux1,$lo_datacenter);
		$lo_hoja->write(9,23,"PROGAMADO ACUMULADO",$lo_datacenter);
		$lo_hoja->write(9,24,"EJECUTADO".$ls_mesaux1,$lo_datacenter);
		$lo_hoja->write(9,25,"EJECUTADO ACUMULADO",$lo_datacenter);
		$lo_hoja->write(8,26,"VARIACION",$lo_datacenter);
		$lo_hoja->write(9,26,"ABSOLUTA".$ls_mesaux1,$lo_datacenter);
		$lo_hoja->write(9,27,"%",$lo_datacenter);
		$lo_hoja->write(8,28,"VARIACION",$lo_datacenter);
		$lo_hoja->write(9,28,"ABSOLUTA ACUMULADO",$lo_datacenter);
		$lo_hoja->write(9,29,"%",$lo_datacenter);
	
/////////////////////////////////////////////////////////////////////////////

		$li_cont=10;
		for($z=1;$z<=$li_tot;$z++)
		{		
			
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
				  $arrResultado=$io_function_report->uf_get_spg_cuenta($ls_spg_cuenta,$ls_partida,$ls_generica,$ls_especifica,$ls_subesp);
				  $ls_partida=$arrResultado['as_spg_partida'];
				  $ls_generica=$arrResultado['as_spg_generica'];
				  $ls_especifica=$arrResultado['as_spg_especifica'];
				  $ls_subesp=$arrResultado['as_spg_subesp'];
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
				  
				  if($ld_programado_mensual>0)
				  {
				 	 $ld_porcommen=($ld_ejecutado_mens_comp*100)/$ld_programado_mensual;
				  }
				  else
				  {$ld_porcommen=0;}
				  if($ld_programado_acumulado>0)
				  {
					  $ld_porcomacu=($ld_ejecutado_acum_comp*100)/$ld_programado_acumulado;
				  }
				  else
				  {$ld_porcomacu=0;}
				  if($ld_programado_mensual>0)
				  {
				  	$ld_porcaumen=($ld_ejecutado_mens_caus*100)/$ld_programado_mensual;
				  }
				  else
				  {$ld_porcaumen=0;}
				  if($ld_programado_acumulado>0)
				  {
				  	$ld_porcauacu=($ld_ejecutado_acum_caus*100)/$ld_programado_acumulado;
				  }
				  else
				  {$ld_porcauacu=0;}
				  if($ld_programado_mensual>0)
				  {
				  	$ld_porpagmen=($ld_ejecutado_mens_paga*100)/$ld_programado_mensual;
				  }
				  else
				  {$ld_porpagmen=0;}
				  if($ld_programado_acumulado>0)
				  {
				  	$ld_porpagacu=($ld_ejecutado_acum_caus*100)/$ld_programado_acumulado;
				  }
				  else
				  {$ld_porpagacu=0;}

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
				
				  $ld_porcommen  = number_format($ld_porcommen,2,",",".");
				  $ld_porcomacu  = number_format($ld_porcomacu,2,",",".");
				  $ld_porcaumen  = number_format($ld_porcaumen,2,",",".");
				  $ld_porcauacu  = number_format($ld_porcauacu,2,",",".");
				  $ld_porpagmen  = number_format($ld_porpagmen,2,",",".");
				  $ld_porpagacu  = number_format($ld_porpagacu,2,",",".");

					$lo_hoja->write($li_cont,0,$ls_codigo,$lo_datacenter);
					$lo_hoja->write($li_cont,1,$ls_denominacion,$lo_dataleft);
					$lo_hoja->write($li_cont,2," ".$ls_partida,$lo_dataleft);
					$lo_hoja->write($li_cont,3," ".$ls_generica,$lo_dataleft);
					$lo_hoja->write($li_cont,4," ".$ls_especifica,$lo_dataleft);
					$lo_hoja->write($li_cont,5," ".$ls_subesp,$lo_dataleft);
					$lo_hoja->write($li_cont,6,$ld_programado_mensual,$lo_dataright);
					$lo_hoja->write($li_cont,7,$ld_programado_acumulado,$lo_dataright);
					$lo_hoja->write($li_cont,8,$ld_ejecutado_mens_comp,$lo_dataright);
					$lo_hoja->write($li_cont,9,$ld_ejecutado_acum_comp,$lo_dataright);
					$lo_hoja->write($li_cont,10,$ld_variacion_mens_comp.$ls_mesaux1,$lo_dataright);
					$lo_hoja->write($li_cont,11,$ld_porcommen,$lo_datacenter);
					$lo_hoja->write($li_cont,12,$ld_variacion_acum_comp,$lo_dataright);
					$lo_hoja->write($li_cont,13,$ld_porcomacu,$lo_datacenter);
					
					$lo_hoja->write($li_cont,14,$ld_programado_mensual,$lo_dataright);
					$lo_hoja->write($li_cont,15,$ld_programado_acumulado,$lo_dataright);
					$lo_hoja->write($li_cont,16,$ld_ejecutado_mens_caus,$lo_dataright);
					$lo_hoja->write($li_cont,17,$ld_ejecutado_acum_caus,$lo_dataright);
					$lo_hoja->write($li_cont,18,$ld_variacion_mens_caus,$lo_dataright);
					$lo_hoja->write($li_cont,19,$ld_porcaumen,$lo_datacenter);
					$lo_hoja->write($li_cont,20,$ld_variacion_acum_caus,$lo_dataright);
					$lo_hoja->write($li_cont,21,$ld_porcauacu,$lo_datacenter);
					
					$lo_hoja->write($li_cont,22,$ld_programado_mensual,$lo_dataright);
					$lo_hoja->write($li_cont,23,$ld_programado_acumulado,$lo_dataright);
					$lo_hoja->write($li_cont,24,$ld_ejecutado_mens_paga,$lo_dataright);
					$lo_hoja->write($li_cont,25,$ld_ejecutado_acum_paga,$lo_dataright);
					$lo_hoja->write($li_cont,26,$ld_variacion_mens_paga,$lo_dataright);
					$lo_hoja->write($li_cont,27,$ld_porpagmen,$lo_datacenter);
					$lo_hoja->write($li_cont,28,$ld_variacion_acum_paga,$lo_dataright);
					$lo_hoja->write($li_cont,29,$ld_porpagacu,$lo_datacenter);
	$li_cont++;
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
			
					$lo_hoja->write($li_cont,0,"",$lo_datacenter);
					$lo_hoja->write($li_cont,1,"TOTALES",$lo_dataleft);
					$lo_hoja->write($li_cont,2," ",$lo_dataleft);
					$lo_hoja->write($li_cont,3," ",$lo_dataleft);
					$lo_hoja->write($li_cont,4," ",$lo_dataleft);
					$lo_hoja->write($li_cont,5," ",$lo_dataleft);
					$lo_hoja->write($li_cont,6,$ld_total_programado_mensual,$lo_dataright);
					$lo_hoja->write($li_cont,7,$ld_total_programado_acumulado,$lo_dataright);
					$lo_hoja->write($li_cont,8,$ld_total_ejecutado_mens_comp,$lo_dataright);
					$lo_hoja->write($li_cont,9,$ld_total_ejecutado_acum_comp,$lo_dataright);
					$lo_hoja->write($li_cont,10,$ld_total_variacion_mens_comp,$lo_dataright);
					$lo_hoja->write($li_cont,11,"",$lo_dataright);
					$lo_hoja->write($li_cont,12,$ld_total_variacion_acum_comp,$lo_dataright);
					$lo_hoja->write($li_cont,13,"",$lo_dataright);
					$lo_hoja->write($li_cont,14,$ld_total_programado_mensual,$lo_dataright);
					$lo_hoja->write($li_cont,15,$ld_total_programado_acumulado,$lo_dataright);
					$lo_hoja->write($li_cont,16,$ld_total_ejecutado_mens_caus.$ls_mesaux1,$lo_dataright);
					$lo_hoja->write($li_cont,17,$ld_total_ejecutado_acum_caus,$lo_dataright);
					$lo_hoja->write($li_cont,18,$ld_total_variacion_mens_caus,$lo_dataright);
					$lo_hoja->write($li_cont,19,"",$lo_datacenter);
					$lo_hoja->write($li_cont,20,$ld_total_variacion_acum_caus,$lo_dataright);
					$lo_hoja->write($li_cont,21,"",$lo_datacenter);
					$lo_hoja->write($li_cont,22,$ld_total_programado_mensual,$lo_dataright);
					$lo_hoja->write($li_cont,23,$ld_total_programado_acumulado,$lo_dataright);
					$lo_hoja->write($li_cont,24,$ld_total_ejecutado_mens_paga,$lo_dataright);
					$lo_hoja->write($li_cont,25,$ld_total_ejecutado_acum_paga,$lo_dataright);
					$lo_hoja->write($li_cont,26,$ld_total_variacion_mens_paga,$lo_dataright);
					$lo_hoja->write($li_cont,27,"",$lo_datacenter);
					$lo_hoja->write($li_cont,28,$ld_total_variacion_acum_paga,$lo_dataright);
					$lo_hoja->write($li_cont,29,"",$lo_datacenter);
					
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

//			uf_print_titulo_reporte($io_encabezado,"",$li_ano,$ls_mesaux1,"",$io_pdf);
			if(count($la_data)>0)
			{
//				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
//				uf_print_pie_cabecera($la_data_tot,$io_pdf);
			}
			unset($la_data);
			unset($la_data_tot);		
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"EJECUCIÓN_FINANCIERA_DE_LOS_PROYECTOS/ACCIONES_CENTRALIZADAS_DEL_ORGANO.xls\"");
			header("Content-Disposition: inline; filename=\"EJECUCIÓN_FINANCIERA_DE_LOS_PROYECTOS/ACCIONES_CENTRALIZADAS_DEL_ORGANO.xls\"");
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
	}//else
	unset($io_report);
	unset($io_funciones);
?> 