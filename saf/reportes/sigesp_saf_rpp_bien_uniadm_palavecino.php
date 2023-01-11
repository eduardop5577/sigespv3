<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
	function uf_print_encabezado_pagina($as_unidadm,$as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   ad_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/04/2006 		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->saveState();
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=400-($li_tm/2);
		$io_pdf->addText($tm,550,11,"<b>".$as_titulo."</b>"); // Agregar el título
		$io_pdf->addText(50,520,9,"ALCALDIA DEL MUNICIPIO PALAVECINO"); // Agregar el título
		$io_pdf->addText(50,510,9,"ESTADO LARA"); // Agregar el título
		$io_pdf->addText(680,535,9,date("d/m/Y")); // Agregar el título
			
		$io_pdf->ezSetY(510);
		$la_data[1]=array('unidad'=>'ESTADO: </b> LARA');
		$la_data[2]=array('unidad'=>'MUNICIPIO: </b> PALAVECINO');
		$la_data[3]=array('unidad'=>'PARROQUIA: </b> CABUDARE');
		$la_columna=array('unidad'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'left', // Orientación de la tabla
						 'cols'=>array('unidad'=>array('justification'=>'left','width'=>360))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_fecha ($io_pdf)
	{
		
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_fecha
		//		   Access: private 
		//    Description: función que imprime la fecha y el numero de pagina en la cabecera del reporte
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 25/06/2008
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;


/*		$la_data[0]=array('fecha'=>'<b>1. FECHA</b>',
						  'pagina'=>'<b>2. PÁGINA</b>');
		$la_columna=array('fecha'=>'',
						  'pagina'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>675,
						 'cols'=>array('fecha'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'pagina'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		
		
		$la_data2[0]=array('fecha2'=>$ad_fecha,
						  'pagina2'=>'');
		$la_columna2=array('fecha2'=>'',
						  'pagina2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>675,
						 'cols'=>array('fecha2'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'pagina2'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
									   
		$io_pdf->ezTable($la_data2,$la_columna2,'',$la_config);*/
	
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_detalle($as_unidadm,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_detalle
		//		   Access: private 
		//	    Arguments: io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera el detalle
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 25/06/2008 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
	
		
		$io_pdf->ezSetDy(-5);
		$la_data[0]=array('unidad'=>'UNIDAD ORGANIZATIVA: </b>'.$as_unidadm);
		$la_columna=array('unidad'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'left', // Orientación de la tabla
						 'cols'=>array('unidad'=>array('justification'=>'left','width'=>360))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
				
		$la_data[0]=array('codigo'=>'<b>Codigo del Bien</b>',
						  'ideact'=>'<b>Numero de Identificacion</b>',
						  'cantidad'=>'<b>Cantidad</b>',
						  'descripcion'=>'<b>Descripcion de los Bienes</b>',
						  'estado'=>'<b>Estado del Bien</b>',
						  'precio'=>'<b>Precio Unitario</b>');
		$la_columna=array('codigo'=>'',
						  'ideact'=>'',
						  'cantidad'=>'',
						  'descripcion'=>'',
						  'estado'=>'',
						  'precio'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'ideact'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'cantidad'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
									   'descripcion'=>array('justification'=>'center','width'=>290), // Justificación y ancho de la columna
						 			   'estado'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'precio'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 25/06/2008 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna=array('codigo'=>'',
						  'ideact'=>'',
						  'cantidad'=>'',
						  'descripcion'=>'',
						  'estado'=>'',
						  'precio'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'ideact'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'cantidad'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
									   'descripcion'=>array('justification'=>'center','width'=>290), // Justificación y ancho de la columna
						 			   'estado'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'precio'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_total($ls_monto,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_total
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 25/06/2008 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data[0]=array('codigo'=>'',
						  'ideact'=>'Monto Total    '.$ls_monto);
		$la_columna=array('codigo'=>'',
						  'ideact'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>520), // Justificación y ancho de la columna
						 			   'ideact'=>array('justification'=>'center','width'=>200))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_total
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera
		//		   Access: private 
		//	    Arguments: ai_montot // Total movimiento
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barrgan
		// Fecha Creación: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-20);
		$la_data=array(array('dato1'=>'11.JEFE DE LA UNIDAD DE TRABAJO',
		                     'dato2'=>'12. RESPONSABLE DE BIENES DE LA UNIDAD DE TRABAJO',
							 'dato3'=>'13.COORDINACIÓN DE BIENES MUNICIPALES'));
		$la_columna=array('dato1'=>'',
		                  'dato2'=>'',
						  'dato3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar Líneas
						 'fontSize' => 8, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('dato1'=>array('justification'=>'center','width'=>250), // Justificación y ancho de la columna
						               'dato2'=>array('justification'=>'center','width'=>250), // Justificación y ancho de la columna
									   'dato3'=>array('justification'=>'center','width'=>250))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		
		$la_data2=array(array('dato1'=>'NOMBRE Y APELLIDO',
		                     'dato2'=>'FIRMA',
							 'dato3'=>'NOMBRE Y APELLIDO',
		                     'dato4'=>'FIRMA',
							 'dato5'=>'NOMBRE Y APELLIDO',
		                     'dato6'=>'FIRMA'));
		$la_columna2=array('dato1'=>'',
		                  'dato2'=>'',
						  'dato3'=>'',
		                  'dato4'=>'',
						  'dato5'=>'',
		                  'dato6'=>'');
		$la_config2=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar Líneas
						 'fontSize' => 8, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('dato1'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						               'dato2'=>array('justification'=>'center','width'=>100),
									   'dato3'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						               'dato4'=>array('justification'=>'center','width'=>100),
									   'dato5'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						               'dato6'=>array('justification'=>'center','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data2,$la_columna2,'',$la_config2);
		
		$la_data3[1]=array('dato1'=>'',
		                  'dato2'=>'',
						  'dato3'=>'',
		                  'dato4'=>'',
						  'dato5'=>'',
		                  'dato6'=>'');
	   	
		$la_data3[2]=array('dato1'=>'',
		                  'dato2'=>'',
						  'dato3'=>'',
		                  'dato4'=>'',
						  'dato5'=>'',
		                  'dato6'=>'');
		$la_columna3=array('dato1'=>'',
		                  'dato2'=>'',
						  'dato3'=>'',
		                  'dato4'=>'',
						  'dato5'=>'',
		                  'dato6'=>'');
		$la_config3=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
						 'fontSize' =>13, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('dato1'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						               'dato2'=>array('justification'=>'center','width'=>100),
									   'dato3'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						               'dato4'=>array('justification'=>'center','width'=>100),
									   'dato5'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						               'dato6'=>array('justification'=>'center','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data3,$la_columna3,'',$la_config3);
		
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_activos.php");
	require_once("sigesp_saf_class_report.php");
	$io_report=new sigesp_saf_class_report();
	$io_fun_activos=new class_funciones_activos();
	$ls_tipoformato=$io_fun_activos->uf_obtenervalor_get("tipoformato",0);
	
	//----------------------------------------------------  Parámetros del encabezado  ----------------------------------------------
	$ls_titulo="INVENTARIO DE BIENES MUNICIPALES";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_nomemp=$arre["nombre"];
	$li_ordenact=$io_fun_activos->uf_obtenervalor_get("ordenact","");
	$ls_coddesde=$io_fun_activos->uf_obtenervalor_get("coddesde","");
	$ls_codhasta=$io_fun_activos->uf_obtenervalor_get("codhasta","");
	$ls_coduniadmdesde=$io_fun_activos->uf_obtenervalor_get("coduniadmdesde","");
	$ls_coduniadmhasta=$io_fun_activos->uf_obtenervalor_get("coduniadmhasta","");	//--------------------------------------------------------------------------------------------------------------------------------
	$ls_grupo=$io_fun_activos->uf_obtenervalor_get("grupo","");
	$ls_subgrupo=$io_fun_activos->uf_obtenervalor_get("subgrupo","");
	$ls_seccion=$io_fun_activos->uf_obtenervalor_get("seccion","");
	$li_incorporado=$io_fun_activos->uf_obtenervalor_get("incorporado","");
	$ls_grupohas=$io_fun_activos->uf_obtenervalor_get("grupohas","");
	$ls_subgrupohas=$io_fun_activos->uf_obtenervalor_get("subgrupohas","");
	$ls_seccionhas=$io_fun_activos->uf_obtenervalor_get("seccionhas","");
	
	$lb_valido=$io_report->uf_saf_load_bienes_uniadm($ls_codemp,$li_ordenact,$ls_coddesde,$ls_codhasta,$ls_coduniadmdesde,
													 $ls_coduniadmhasta,$ls_grupo,$ls_subgrupo,$ls_seccion,$li_incorporado,
													 $ls_grupohas,$ls_subgrupohas,$ls_seccionhas); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////
		$ls_desc_event="Generó un reporte de Inventario por Unidad Organizativa. Desde el Activo   ".$ls_coddesde." hasta   ".$ls_codhasta;
		$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_bien_uniadm.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(5.8,2,3,3);
		$io_pdf->ezStartPageNumbers(725,545,9,'','',1);//Insertar el número de página.
		
		uf_print_encabezado_pagina("",$ls_titulo,$io_pdf);
		$li_totrow=$io_report->ds->getRowCount("codact");
		$i=0;
		$num=1;
		$ls_cantidad=0; 
		$li_totcosto=0;
        //$lb_nuevapagina=true;
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
			if ($li_i==1)
			{
			  $aux_uniad =$io_report->ds->data["coduniadm"][$li_i];
			  $lb_nuevapagina=false;
			}
			else
			{
				if($li_i<$li_totrow)
				{
			   		$aux_uniad = $io_report->ds->data["coduniadm"][$li_i+1];
					$lb_nuevapagina=true;
				}
				if($li_i==$li_totrow)
				{
					$lb_nuevapagina=false;
					$aux_uniad="";
				}
			}
			$ls_codnidadm= $io_report->ds->data["coduniadm"][$li_i];
			if ($aux_uniad == $ls_codnidadm) 
			{

				$io_pdf->transaction('start'); // Iniciamos la transacción
			//	$li_numpag=$io_pdf->ezPageCount; // Número de página
				$ls_numero=$num;
				$ls_denunidadm= $io_report->ds->data["denuniadm"][$li_i];
				$ls_ideact= $io_report->ds->data["ideact"][$li_i];
				$ls_codact=$io_report->ds->data["codact"][$li_i];
				$ls_denconbie=$io_report->ds->data["denconbie"][$li_i];
				$ls_codgru=$io_report->ds->data["codgru"][$li_i];
				$ls_codsubgru=$io_report->ds->data["codsubgru"][$li_i];
				$ls_codsec=$io_report->ds->data["codsec"][$li_i];
				$ls_descripcion=$io_report->ds->data["denact"][$li_i];
				
				if (($li_ordenact==0) && ($li_i<$li_totrow))
				{
				  if ($ls_codact == $io_report->ds->data["codact"][$li_i+1] )
				  {
				    $ls_cantidad=$ls_cantidad+1; 
				  }
				  else
				  {
				   $ls_cantidad=1; 
				  }
				}
				else if (($li_ordenact==1) && ($li_i<$li_totrow))
				{
				  if ($ls_descripcion == $io_report->ds->data["denact"][$li_i+1] )
				  {
				    $ls_cantidad=$ls_cantidad+1; 
				  }
				  else
				  {
				    $ls_cantidad=1; 
				  }
				}
				
				$li_costo=$io_report->ds->data["costo"][$li_i];
				$li_totcosto=$li_totcosto+$li_costo;
				$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
				$la_data[$num]=array('codigo'=>$ls_codgru." ".$ls_codsubgru." ".$ls_codsec,'ideact'=>$ls_codact,'cantidad'=>$ls_cantidad,'descripcion'=>$ls_descripcion,
									  'estado'=>$ls_denconbie,'precio'=>$li_costo);
			   $num=$num+1;
			   $ls_cantidad=0;	
			}
			else
			{
				
				$io_pdf->transaction('start'); // Iniciamos la transacción
				$li_numpag=$io_pdf->ezPageCount; // Número de página
				$ls_numero=$num;
				$ls_denunidadm= $io_report->ds->data["denuniadm"][$li_i];
				$ls_codact=$io_report->ds->data["codact"][$li_i];
				$ls_ideact= $io_report->ds->data["ideact"][$li_i];
				$ls_codgru=$io_report->ds->data["codgru"][$li_i];
				$ls_codsubgru=$io_report->ds->data["codsubgru"][$li_i];
				$ls_codsec=$io_report->ds->data["codsec"][$li_i];
				$ls_descripcion=$io_report->ds->data["denact"][$li_i];
				$ls_denconbie=$io_report->ds->data["denconbie"][$li_i];
				$ls_cantidad=1;
				$li_costo=$io_report->ds->data["costo"][$li_i];
				$li_totcosto=$li_totcosto+$li_costo;
				$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
				$la_data[$num]=array('codigo'=>$ls_codgru." ".$ls_codsubgru." ".$ls_codsec,'ideact'=>$ls_codact,'cantidad'=>$ls_cantidad,'descripcion'=>$ls_descripcion,
									  'estado'=>$ls_denconbie,'precio'=>$li_costo);
				
				uf_print_cabecera_detalle($ls_denunidadm,$io_pdf);
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				if ($lb_nuevapagina)
				{
				  $io_pdf->ezNewPage(); 
				}
				unset($la_data);		
				$num=1;			
					
			}
		}
		$li_totcosto=$io_fun_activos->uf_formatonumerico($li_totcosto);
		uf_print_total($li_totcosto,$io_pdf);
		$io_pdf->transaction('start'); // Iniciamos la transacción
		$li_numpag=$io_pdf->ezPageCount; // Número de página
   		uf_print_pie_cabecera($io_pdf);
		if ($io_pdf->ezPageCount==$li_numpag)
		{// Hacemos el commit de los registros que se desean imprimir
			$io_pdf->transaction('commit');
		}
		else
		{// Hacemos un rollback de los registros, agregamos una nueva página y volvemos a imprimir
			$io_pdf->transaction('rewind');
			$io_pdf->ezNewPage(); // Insertar una nueva página		
   			uf_print_pie_cabecera($io_pdf);
		}
		
		if($lb_valido)
		{
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		}
		else
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}		
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 