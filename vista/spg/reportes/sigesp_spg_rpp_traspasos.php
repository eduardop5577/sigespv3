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
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   as_desnom // Descripci?n de la n?mina
		//	    		   as_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Arnaldo Su?rez
		// Fecha Creaci?n: 07/08/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],35,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,550,11,"<b>".$as_titulo."</b>"); // Agregar el t?tulo
		$li_tm=$io_pdf->getTextWidth(11,$as_fecha);
		$io_pdf->addText(700,560,9,$_SESSION["ls_database"]);// Agrerar el nombre de la base de datos actual
		$io_pdf->addText(700,550,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(706,543,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_nomemp,$as_bdorigen,$ad_fecdesde,$ad_fechasta,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_nomemp    // nombre de la empresa
		//	    		   ad_bdorigen  // Base de Datos Origen
		//	    		   ad_fecdesde  // Fecha de Inicio del Reporte
		//	    		   ad_fechasta  // Fecha Tope del Reporte
		//	    		   io_pdf       // total de registros que va a tener el reporte
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing. Arnaldo Su?rez
		// Fecha Creaci?n: 10/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>'<b>Empresa: </b>  '.$as_nomemp.''),
					   array('name'=>'<b>Base de Datos Origen: </b>  '.$as_bdorigen.''),
					   array ('name'=>'<b>Fecha de Traspaso Desde:</b>   '.$ad_fecdesde." Hasta: ".$ad_fechasta.''),
					   );
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tama?o de Letras
						 'lineCol'=>array(0.9,0.9,0.9), // Mostrar L?neas
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2	, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>730, // Ancho de la tabla
						 'maxWidth'=>730); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-5);
		$la_datatit=array(array('nro'=>'<b>Nro.</b>',
						  		'descripcion'=>'<b>Descripcion</b>',
								'fecha'=>'<b>Fecha</b>',
								'codsis'=>'<b>Sistema</b>',
								'bdorigen'=>'<b>Base de Datos Origen</b>',
								'bddestino'=>'<b>Base de Datos Destino</b>'));
	 
	    $la_columnatit=array('nro'=>'',
						  	 'descripcion'=>'',
							 'fecha'=>'',
							 'codsis'=>'',
							 'bdorigen'=>'',
							 'bddestino'=>'');
	 
	    $la_configtit=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>730, // Ancho de la tabla
						 'maxWidth'=>730, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('nro'=>array('justification'=>'center','width'=>50), // Justificaci?n y ancho de la columna
						 			   'descripcion'=>array('justification'=>'center','width'=>300), // Justificaci?n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>80), // Justificaci?n y ancho de la columna
						 			   'codsis'=>array('justification'=>'center','width'=>50), // Justificaci?n y ancho de la columna
						 			   'bdorigen'=>array('justification'=>'center','width'=>125), // Justificaci?n y ancho de la columna
						 			   'bddestino'=>array('justification'=>'center','width'=>125)));// Ancho M?ximo de la tabla
	 
	    $io_pdf->ezTable($la_datatit,$la_columnatit,'',$la_configtit);	
		$la_columna=array('nro'=>'<b>Nro.</b>',
						  'descripcion'=>'<b>Descripcion</b>',
						  'fecha'=>'<b>Fecha</b>',
						  'codsis'=>'<b>Sistema</b>',
						  'bdorigen'=>'<b>Base de Datos Origen</b>',
						  'bddestino'=>'<b>Base de Datos Destino</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>730, // Ancho de la tabla
						 'maxWidth'=>730, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('nro'=>array('justification'=>'center','width'=>50), // Justificaci?n y ancho de la columna
						 			   'descripcion'=>array('justification'=>'left','width'=>300), // Justificaci?n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>80), // Justificaci?n y ancho de la columna
						 			   'codsis'=>array('justification'=>'center','width'=>50), // Justificaci?n y ancho de la columna
						 			   'bdorigen'=>array('justification'=>'center','width'=>125), // Justificaci?n y ancho de la columna
						 			   'bddestino'=>array('justification'=>'center','width'=>125))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------



	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_spg_class_report_traspaso.php");
	$io_report=new sigesp_spg_class_report_traspaso();
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
	$ls_titulo="REPORTE DE TRASPASO DE MODIFICACIONES PRESUPUESTARIAS";
	$ls_fecha="";
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	$ls_codemp  =$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp  =$_SESSION["la_empresa"]["nombre"];
	$ls_bdorigen=$_SESSION["ls_database"];
	$ld_fecdesde=$_GET["fecdesde"];
	$ld_fechasta=$_GET["fechasta"];
	
	if(array_key_exists("bddestino",$_GET))
	{
	 $ls_bddestino=$_GET["bddestino"];
	}
	else
	{
	 $ls_bddestino="";
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=$io_report->uf_spg_select_traspasos($ld_fecdesde,$ld_fechasta,$ls_bddestino); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe alg?n error ? no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuraci?n de los margenes en cent?metros
		uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la p?gina
		$io_pdf->ezStartPageNumbers(700,50,10,'','',1); // Insertar el n?mero de p?gina
		$li_totrow=$io_report->ds->getRowCount("codres");
		uf_print_cabecera($ls_nomemp,$ls_bdorigen,$ld_fecdesde,$ld_fechasta,$io_pdf); // Imprimimos la cabecera del registro
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
		    $io_pdf->transaction('start'); // Iniciamos la transacci?n
			$li_numpag=$io_pdf->ezPageCount; // N?mero de p?gina
			$ls_descripcion =  $io_report->ds->data["descripcion"][$li_i];
			$fecha          =  $io_report->ds->data["fecha"][$li_i];
			$ld_fecha       =  $io_funciones->uf_convertirfecmostrar($fecha);
			$ls_codsis      =  $io_report->ds->data["codsis"][$li_i];
			$ls_bdorigen    =  $io_report->ds->data["bdorigen"][$li_i];
			$ls_bddestino   =  $io_report->ds->data["bddestino"][$li_i];
			
            $la_data[$li_i]=array('nro'=>$li_i,'descripcion'=>$ls_descripcion,'fecha'=>$ld_fecha,'codsis'=>$ls_codsis,'bdorigen'=>	
								 $ls_bdorigen,'bddestino'=>$ls_bddestino);
        }
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				
		 if($lb_valido)
		 {
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		 }	
		  unset($la_data);		
		}

	unset($io_pdf);
	unset($io_report);
	unset($io_funciones);
?> 