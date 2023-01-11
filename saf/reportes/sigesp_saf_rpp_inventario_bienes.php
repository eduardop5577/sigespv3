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
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
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
		//$io_pdf->addText(50,520,9,"ALCALDIA DEL MUNICIPIO PALAVECINO"); // Agregar el título
		//$io_pdf->addText(50,510,9,"ESTADO LARA"); // Agregar el título
			
		//uf_print_fecha ($io_pdf);
				
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	    $io_pdf->ezSetDy(-20);
	
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
	function uf_print_cabecera_detalle($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_detalle
		//		   Access: private 
		//	    Arguments: io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera el detalle
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 25/06/2008 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;	
		
		$la_data[1]=array('unidad'=>'ESTADO: </b> LARA');
		$la_data[2]=array('unidad'=>'MUNICIPIO: </b> PALAVECINO');
		$la_data[3]=array('unidad'=>'PARROQUIA: </b> CABUDARE                                               FECHA: '.date("d-m-Y"));
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
		$io_pdf->ezSetDy(-20);
				
		$la_data[0]=array('codigo'=>'<b>Ubicacion Geografica</b>',
						  'ideact'=>'<b>Clasificacion Funcional    Grupo  Sub-Grupo</b>',
						  'cantidad'=>'<b>No. de Expediente</b>',
						  'descripcion'=>'<b>Denominación del Inmueble</b>',
						  'estado'=>'<b>Fecha de Incorporación       Mes     Año</b>',
						  'precio'=>'<b>Valor</b>');
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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($li_totcosto,$li_cantidad,$io_pdf)
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
		$la_data[1]=array('dato1'=>'','dato2'=>'Resumen:');
		$la_data[2]=array('dato1'=>'','dato2'=>'No. Total de Inmuebles  '.$li_cantidad);
		$la_data[3]=array('dato1'=>'','dato2'=>'Monto Total    '.$li_totcosto);
		$la_columna=array('dato1'=>'',
		                  'dato2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'fontSize' => 8, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>800, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('dato1'=>array('justification'=>'left','width'=>600), // Justificación y ancho de la columna
						               'dato2'=>array('justification'=>'left','width'=>200))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime las firmas
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 06/12/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
	$io_pdf->addText(10,150,8.5,"                JEFE DE LA UNIDAD DE TRABAJO              ");	
	$io_pdf->addText(10,120,8.5,"Nombre y Apellido:______________________________");
	$io_pdf->addText(10,100,8.5,"Firma:                    ______________________________");
	$io_pdf->addText(10,80,8.5,"C.I.:                        ______________________________");
	
	$io_pdf->addText(230,150,8.5,"            RESPONSABLE DE BIENES EN LA UNIDAD DE TRABAJO              ");	
	$io_pdf->addText(260,120,8.5,"Nombre y Apellido:______________________________");
	$io_pdf->addText(260,100,8.5,"Firma:                    ______________________________");
	$io_pdf->addText(260,80,8.5,"C.I.:                        ______________________________");	
	
	$io_pdf->addText(480,150,8.5,"                             COORDINACION DE BIENES MUNICIPALES              ");	
	$io_pdf->addText(520,120,8.5,"Nombre y Apellido:______________________________");
	$io_pdf->addText(520,100,8.5,"Firma:                    ______________________________");
	$io_pdf->addText(520,80,8.5,"C.I.:                        ______________________________");	
	}// end function uf_print_firmas	//--------------------------------------------------------------------------------------------------------------------------------

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
	$ls_titulo="INVENTARIO DE BIENES INMUEBLES";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_nomemp=$arre["nombre"];
	$li_ordenact=$io_fun_activos->uf_obtenervalor_get("ordenact","");
	$ls_fecdesde=$io_fun_activos->uf_obtenervalor_get("coddesde","");
	$ls_fechasta=$io_fun_activos->uf_obtenervalor_get("codhasta","");
	$ls_coddesde=$io_fun_activos->uf_obtenervalor_get("coddesde","");
	$ls_codhasta=$io_fun_activos->uf_obtenervalor_get("codhasta","");
	$ls_grupo=$io_fun_activos->uf_obtenervalor_get("grupo","");
	$ls_subgrupo=$io_fun_activos->uf_obtenervalor_get("subgrupo","");
	$ls_seccion=$io_fun_activos->uf_obtenervalor_get("seccion","");
	$ls_grupohas=$io_fun_activos->uf_obtenervalor_get("grupohas","");
	$ls_subgrupohas=$io_fun_activos->uf_obtenervalor_get("subgrupohas","");
	$ls_seccionhas=$io_fun_activos->uf_obtenervalor_get("seccionhas","");
	$li_incorporado=$io_fun_activos->uf_obtenervalor_get("incorporado","");
	$ls_unitri=$io_fun_activos->uf_obtenervalor_get("unitri","0");
	
	$lb_valido=$io_report->uf_saf_load_inventario_bienes($ls_codemp,$li_ordenact,$ls_coddesde,$ls_codhasta,$ls_grupo,$ls_subgrupo,
													 $ls_seccion,$ls_grupohas,$ls_subgrupohas,$ls_seccionhas,$ls_fecdes,
													 $ls_fechas,$lb_valido,$ls_unitri); // Cargar el DS con los datos de la cabecera del reporte
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
		$io_pdf->ezSetCmMargins(4,4,3,3);
		$io_pdf->ezStartPageNumbers(725,440,9,'','',1);//Insertar el número de página.
		uf_print_encabezado_pagina($ls_titulo,$io_pdf);
		$io_pdf->transaction('start'); // Iniciamos la transacción
		$li_totrow=$io_report->ds->getRowCount("codact");
		$i=0;
		$num=1;
		$ls_cantidad=0; 
		$li_totcosto=0;
		uf_print_cabecera_detalle($io_pdf);
		while((!$io_report->rs_data->EOF))
		{
			$ls_denuniadm=$io_report->rs_data->fields["denuniadm"];
			$ls_codact=$io_report->rs_data->fields["codact"];
			$ls_ideact=$io_report->rs_data->fields["ideact"];
			$ls_codgru=$io_report->rs_data->fields["codgru"];
			$ls_codsubgru=$io_report->rs_data->fields["codsubgru"];
			$ls_codsec=$io_report->rs_data->fields["codsec"];
			$ls_denact=$io_report->rs_data->fields["denact"];
			$ls_denpai=$io_report->rs_data->fields["denpai"];
			$ls_denest=$io_report->rs_data->fields["denest"];
			$ls_denmun=$io_report->rs_data->fields["denmun"];
			$ls_expediente=$io_report->rs_data->fields["expediente"];
			$ls_fecincact=$io_report->rs_data->fields["fecincact"];
			$li_costo=$io_report->rs_data->fields["costo"];
			$ls_descripcion=$io_report->rs_data->fields["denact"];
			$li_totcosto=$li_totcosto+$li_costo;
			$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
			$ls_mes=substr($ls_fecincact,5,2);
			$ls_anio=substr($ls_fecincact,0,4);

			$la_data[$num]=array('codigo'=>$ls_denpai." - ".$ls_denest." - ".$ls_denmun,'ideact'=>$ls_codgru."       -   ".$ls_codsubgru,'cantidad'=>$ls_expediente,'descripcion'=>$ls_descripcion,'estado'=>$ls_mes."   -   ".$ls_anio,'precio'=>$li_costo);
			$num++;
			$io_report->rs_data->MoveNext();
		}
/*		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
			$li_numpag=$io_pdf->ezPageCount; // Número de página
			$ls_numero=$num;
			$ls_denunidadm= $io_report->ds->data["denuniadm"][$li_i];
			$ls_codact=$io_report->ds->data["codact"][$li_i];
			$ls_ideact= $io_report->ds->data["ideact"][$li_i];
			$ls_codgru=$io_report->ds->data["codgru"][$li_i];
			$ls_codsubgru=$io_report->ds->data["codsubgru"][$li_i];
			$ls_codsec=$io_report->ds->data["codsec"][$li_i];
			$ls_descripcion=$io_report->ds->data["denact"][$li_i];
			$ls_denpai=$io_report->ds->data["denpai"][$li_i];
			$ls_denest=$io_report->ds->data["denest"][$li_i];
			$ls_denmun=$io_report->ds->data["denmun"][$li_i];
			$ls_expediente=$io_report->ds->data["expediente"][$li_i];
			$ls_fecincact=$io_report->ds->data["fecincact"][$li_i];
			$ls_cantidad=1;
			$li_costo=$io_report->ds->data["costo"][$li_i];
			$li_totcosto=$li_totcosto+$li_costo;
			$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
			$ls_mes=substr($ls_fecincact,5,2);
			$ls_anio=substr($ls_fecincact,0,4);

			$la_data[$num]=array('codigo'=>$ls_denpai." - ".$ls_denest." - ".$ls_denmun,'ideact'=>$ls_codgru."       -   ".$ls_codsubgru,'cantidad'=>$ls_expediente,'descripcion'=>$ls_descripcion,'estado'=>$ls_mes."   -   ".$ls_anio,'precio'=>$li_costo);
			$num++;
				
		}
*/			
		if($num>1)
		{
			uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
  uf_print_firmas($io_pdf);
			$li_totcosto=$io_fun_activos->uf_formatonumerico($li_totcosto);
			$num=$num-1;
			uf_print_pie_cabecera($li_totcosto,$num,$io_pdf);
			unset($la_data);	
		}
            
		else
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
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