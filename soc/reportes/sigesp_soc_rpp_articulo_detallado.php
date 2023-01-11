<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
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
	function uf_print_encabezado_pagina($as_titulo,$ls_fecordcomdes,$ls_fecordcomhas,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 16/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		global $io_pdf;
		
		$io_encabezado=$io_pdf->openObject();		
		$io_pdf->saveState();		
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,520,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(12,$as_titulo);
		$tm=380-($li_tm/2);
		$io_pdf->addText($tm,560,12,"<b>".$as_titulo."</b>"); // Agregar el título
		$io_pdf->addText(305,550,9,"<b>Del: ".$ls_fecordcomdes." Al: ".$ls_fecordcomhas."</b>"); // Agregar el título
		$io_pdf->addText(700,527,8,"Fecha: ".date("d/m/Y")); // Agregar la Fecha
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------	
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_listado($la_data,$io_pdf)
	{	 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 16/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
		global $ls_bolivares;
		global $io_pdf;
		
		$la_columna=array('numordcom'=>'<b>Orden de Compra</b>',
						  'fecordcom'=>'<b>Fecha</b>',
						  'nompro'=>'<b>Proveedoor</b>',
						  'codart'=>'<b>Codigo</b>',
						  'denart'=>'<b>Denominacion</b>',
						  'preuniart'=>'<b>Precio Unitario</b>',
						  'canart'=>'<b>Cantidad Solicitada</b>',
						  'totartrec'=>'<b>Cantidad Recibida</b>',
						  'monsubart'=>'<b>Monto </b>');
						  
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas						 
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'cols'=>array('numordcom'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'fecordcom'=>array('justification'=>'center','width'=>55), // Justificación y ancho de la columna
									   'nompro'=>array('justification'=>'left','width'=>150), // Justificación y ancho de la columna
						 			   'codart'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'denart'=>array('justification'=>'left','width'=>160),
						 			   'preuniart'=>array('justification'=>'right','width'=>65),
						 			   'canart'=>array('justification'=>'right','width'=>55),
						 			   'totartrec'=>array('justification'=>'right','width'=>55),
   						 			   'monsubart'=>array('justification'=>'right','width'=>65))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ad_numreg,$ad_totmon,$ai_totrec,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 16/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		global $io_pdf;
		
	    $io_pdf->ezSetDy(-10);
		$la_data[1]= array('numordcom'=>'','fecordcom'=>'','codart'=>'',
								 'denart'=>'','nompro'=>'','preuniart'=>'TOTALES','canart'=>$ad_numreg,'totartrec'=>$ai_totrec,'monsubart'=>$ad_totmon);
		$la_columna=array('numordcom'=>'<b>Orden de Compra</b>',
						  'fecordcom'=>'<b>Fecha</b>',
						  'nompro'=>'<b>Proveedoor</b>',
						  'codart'=>'<b>Codigo</b>',
						  'denart'=>'<b>Denominacion</b>',
						  'preuniart'=>'<b>Precio Unitario</b>',
						  'canart'=>'<b>Cantidad Solicitada</b>',
						  'totartrec'=>'<b>Cantidad Recibida</b>',
						  'monsubart'=>'<b>Monto </b>');
						  
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas						 
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'cols'=>array('numordcom'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'fecordcom'=>array('justification'=>'center','width'=>55), // Justificación y ancho de la columna
									   'nompro'=>array('justification'=>'left','width'=>150), // Justificación y ancho de la columna
						 			   'codart'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'denart'=>array('justification'=>'left','width'=>160),
						 			   'preuniart'=>array('justification'=>'right','width'=>65),
						 			   'canart'=>array('justification'=>'right','width'=>60),
						 			   'totartrec'=>array('justification'=>'right','width'=>60),
   						 			   'monsubart'=>array('justification'=>'right','width'=>70))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");	
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("sigesp_soc_class_report.php");	
	require_once("../class_folder/class_funciones_soc.php");
	$in           = new sigesp_include();
	$con          = $in->uf_conectar();
	$io_sql       = new class_sql($con);	
	$io_funciones = new class_funciones();	
	$io_fun_soc   = new class_funciones_soc();
	$io_report    = new sigesp_soc_class_report($con);
	$ls_tiporeporte=$io_fun_soc->uf_obtenervalor_get("tiporeporte",0);
	$ls_bolivares="Bs.";
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_report=new sigesp_soc_class_reportbsf();
		$ls_bolivares="Bs.F.";
	}
		
	//----------------------------------------------------  Inicializacion de variables  -----------------------------------------------
	$lb_valido=true;
	//----------------------------------------------------  Parámetros del encabezado    -----------------------------------------------
	$ls_titulo ="LISTADO DE LAS ORDENES DE COMPRAS";	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	
	$ls_numordcomdes=$io_fun_soc->uf_obtenervalor_get("txtnumordcomdes","");
	$ls_numordcomhas=$io_fun_soc->uf_obtenervalor_get("txtnumordcomhas","");
	$ls_codprodes=$io_fun_soc->uf_obtenervalor_get("txtcodprodes","");
	$ls_codprohas=$io_fun_soc->uf_obtenervalor_get("txtcodprohas","");
	$ls_fecordcomdes=$io_fun_soc->uf_obtenervalor_get("txtfecordcomdes","");
	$ls_fecordcomhas=$io_fun_soc->uf_obtenervalor_get("txtfecordcomhas","");
	$ls_coduniadmdes=$io_fun_soc->uf_obtenervalor_get("txtcoduniejedes","");
	$ls_coduniadmhas=$io_fun_soc->uf_obtenervalor_get("txtcoduniejehas","");
	$ls_codartdes=$io_fun_soc->uf_obtenervalor_get("txtcodartdes","");
	$ls_codarthas=$io_fun_soc->uf_obtenervalor_get("txtcodarthas","");
	
	//--------------------------------------------------------------------------------------------------------------------------------
	$rs_data = $io_report->uf_select_listado_articulos_detallados($ls_numordcomdes,$ls_numordcomhas,$ls_codprodes,
															$ls_codprohas,$ls_fecordcomdes,$ls_fecordcomhas,$ls_coduniadmdes,
															$ls_coduniadmhas,$ls_codartdes,$ls_codarthas);
	if($rs_data==="") // Existe algún error ó no hay registros
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
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_fecordcomdes,$ls_fecordcomhas,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(725,47,9,'','',1); // Insertar el número de página
		$li_totmonsub=0;
		$li_totcanart=0;
		$li_totrec=0;
		$li_i=0;
		//while(!$rs_data->EOF)
		while($row=$io_sql->fetch_row($rs_data))
		{
			$li_i++;
			$ls_numordcom  = $row["numordcom"]; 
			$ls_codpro  = $row["cod_pro"]; 
			$ls_fecordcom  = $row["fecordcom"]; 
			$ls_codart  = $row["codart"]; 
			$ls_denart  = rtrim($row["denart"]); 
			$ls_nompro  = rtrim($row["nompro"]); 
			$li_preuniart  = $row["preuniart"]; 
			$li_monsubart  = $row["monsubart"]; 
			$li_canart  = $row["canart"]; 
			$li_totartrec = $io_report->uf_select_listado_articulos_recibidos($ls_numordcom,$ls_codpro,$ls_codart);
			$li_totmonsub=$li_totmonsub+$li_monsubart;
			$li_totcanart=$li_totcanart+$li_canart;
			$li_totrec=$li_totrec+$li_totartrec;
			$li_preuniart  = number_format($li_preuniart,2,',','.');
			$li_monsubart  = number_format($li_monsubart,2,',','.');
			$li_totartrec  = number_format($li_totartrec,2,',','.');
			$li_canart  = number_format($li_canart,2,',','.');
			$ls_fecordcom   = $io_funciones->uf_convertirfecmostrar($ls_fecordcom);	
					
			$la_data[$li_i]= array('numordcom'=>$ls_numordcom,'fecordcom'=>$ls_fecordcom,'codart'=>$ls_codart,
								 'denart'=>$ls_denart,'nompro'=>$ls_nompro,'preuniart'=>$li_preuniart,'canart'=>$li_canart,'totartrec'=>$li_totartrec,'monsubart'=>$li_monsubart);
			//$rs_data->moveNext();//break;
		}
		//$li_i=$li_i-1;
		
		if($li_i>0) // Si no ocurrio ningún error
		{
			uf_print_listado($la_data,$io_pdf); // Imprimimos el detalle 		
			$li_totmonsub  = number_format($li_totmonsub,2,',','.');
			$li_totcanart  = number_format($li_totcanart,2,',','.');
			$li_totrec  = number_format($li_totrec,2,',','.');
			uf_print_pie_cabecera($li_totcanart,$li_totmonsub,$li_totrec,$io_pdf);		
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print("close();");
			print("</script>");		
		}
		unset($io_pdf);
		unset($io_report);
		unset($io_funciones);
	}	
?> 