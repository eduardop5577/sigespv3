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
	function uf_print_encabezado_pagina($as_titulo,$ad_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   ad_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 17/12/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->saveState();
		$io_pdf->line(50,40,950,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=504-($li_tm/2);
		$io_pdf->addText($tm,550,11,"<b>".$as_titulo."</b>"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$ad_fecha);
		$tm=504-($li_tm/2);
		$io_pdf->addText(750,535,11,""); // Agregar la fecha
		$io_pdf->addText($tm,535,11,$ad_fecha); // Agregar la fecha
		$io_pdf->addText(750,555,11,""); // Agregar la fecha
		$io_pdf->addText(800,555,11,""); // Agregar la fecha
		$io_pdf->addText(908,570,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(914,563,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_codemp,$as_nomemp,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codemp   // codigo de empresa
		//	    		   as_nomemp   // nombre de empresa
		//	    		   as_codact   // codigo de activo
		//	    		   as_denact   // denominacion de activo
		//	    		   as_maract   // marca del activo
		//	    		   as_modact   // modelo del activo
		//	    		   ai_costo    // costo del activo
		//	    		   io_pdf      // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 17/12/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>'<b>ORGANISMO:</b>  '.$as_codemp." - ".$as_nomemp.''),
					   array ('name'=>'<b>UNIDAD DE BIENES</b>'),
					   array ('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'lineCol'=>array(0.9,0.9,0.9), // Mostrar Líneas
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 17/12/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-5);
		global $ls_tipoformato;
		if($ls_tipoformato==0)
		{
		  $ls_titulo=" Bs.";
		}
		elseif($ls_tipoformato==1)
		{
		  $ls_titulo=" Bs.F.";
		}
		$la_columna=array('codact'=>'<b>Código de Activo</b>',
						  'denact'=>'<b>Denominación</b>',
						  'idchp'=>'<b>Nro. de Bien Nacional </b>',
						  'estact'=>'<b>Estado</b>',
						  'maract'=>'<b>Marca</b>',
						  'coduniadm'=>'<b>Unidad</b>',
						  'modact'=>'<b>Modelo</b>',
						  'fecregart'=>'<b>Fecha de Aquisición</b>',
						  'seract'=>'<b>Serial</b>',
						  'costo'=>'<b>Precio '.$ls_titulo.'</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codact'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'denact'=>array('justification'=>'left','width'=>140), // Justificación y ancho de la columna
									   'idchp'=>array('justification'=>'left','width'=>140), // Justificación y ancho de la columna
									   'estact'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
									   'maract'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'coduniadm'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
									   'modact'=>array('justification'=>'left','width'=>65), // Justificación y ancho de la columna
									   'fecregart'=>array('justification'=>'left','width'=>60), // Justificación y ancho de la columna
									   'seract'=>array('justification'=>'center','width'=>120), // Justificación y ancho de la columna 
						 			   'costo'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ai_montot,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera
		//		   Access: private 
		//	    Arguments: ai_montot // Total movimiento
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing.Arnaldo Suárez
		// Fecha Creación: 17/12/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('total'=>'TOTAL','monto'=>$ai_montot));
		$la_columna=array('total'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar Líneas
						 'fontSize' => 8, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>800), // Justificación y ancho de la columna
						               'monto'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_autorizacion($li_exist_final,$li_exist_ant,$li_tot_inc,$li_tot_desinc,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_autorizacion
		//		    Acess: private 
		//	    Arguments: io_pdf // Objeto PDF
		//    Description: función el final del voucher 
		//	   Creado Por: Ing. Néstor Falcón
		// Fecha Creación: 25/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setColor(0.9,0.9,0.9);
		$io_pdf->filledRectangle(50,149,896,$io_pdf->getFontHeight(11));
		$io_pdf->setColor(0,0,0);		
		$io_pdf->Rectangle(51,43,895,105);
		$io_pdf->line(51,135,945,135);
		//$io_pdf->line(51,90,945,90);
		$io_pdf->line(51,115,945,115);		
		$io_pdf->line(257,115,257,148);
		$io_pdf->line(493,115,493,148);
		$io_pdf->line(720,115,720,148);		
		
		$io_pdf->addText(126,150,9,'<b>Totales</b>');
		$io_pdf->addText(526,150,9,'<b>RESUMEN</b>');
		$io_pdf->addText(800,150,9,'<b>Van...</b>'.'                   '.'<b>'.$li_tot_inc.'</b>');
		$io_pdf->addText(106,137.6,9,'<b>Existencia Anterior</b>');
		$io_pdf->addText(332,137.6,9,'<b>Mas Incorporaciones</b>');
		$io_pdf->addText(550,137.6,9,'<b>Menos Desincorporaciones</b>');		
		$io_pdf->addText(800,137.6,9,'<b>Existencia Final</b>');
		$io_pdf->addText(362,119,9,'<b>'.$li_tot_inc.'</b>');
		$io_pdf->addText(600,119,9,'<b>'.$li_tot_desinc.'</b>');
		$io_pdf->addText(820,119,9,'<b>'.$li_exist_final.'</b>');
		
		
		$io_pdf->addText(55,90,10,'<b>Responsable: ____________________________________</b>');		
		$io_pdf->addText(55,53,10,'<b>Entregado por:___________________________________</b>');		
		$io_pdf->addText(455,90,10,'<b>Realizado por: ____________________________________</b>');		
		$io_pdf->addText(455,53,10,'<b>Revisado por:___________________________________</b>');
		$io_pdf->addText(790,60,8,'<b>R=> Registrado, P=> Procesado,</b>');
		$io_pdf->addText(790,53,8,'<b>I=> Contabilizado, D=> Desincorporado</b>');	
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_autorizacion.	
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_activos.php");
	$io_fun_activos=new class_funciones_activos();
	$ls_tipoformato=$io_fun_activos->uf_obtenervalor_get("tipoformato",0);
	global $ls_tipoformato;
	if($ls_tipoformato==1)
	{
		require_once("sigesp_saf_class_reportbsf.php");
		$io_report=new sigesp_saf_class_reportbsf();
		$ls_titulo_report="Bs.F.";
	}
	else
	{
		require_once("sigesp_saf_class_report.php");
		$io_report=new sigesp_saf_class_report();
		$ls_titulo_report="Bs.";
	}	
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ld_desde=$io_fun_activos->uf_obtenervalor_get("desde","");
	$ld_hasta=$io_fun_activos->uf_obtenervalor_get("hasta","");

	$ls_titulo="INVENTARIO GENERAL DE BIENES MUEBLES EN ".$ls_titulo_report."";
	$ld_fecha="";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_nomemp=$arre["nombre"];
	$li_ordenact=$io_fun_activos->uf_obtenervalor_get("ordenact","");
	$ls_coddesde=$io_fun_activos->uf_obtenervalor_get("coddesde","");
	$ls_codhasta=$io_fun_activos->uf_obtenervalor_get("codhasta","");
	$ls_grupo=$io_fun_activos->uf_obtenervalor_get("grupo","");
	$ls_subgrupo=$io_fun_activos->uf_obtenervalor_get("subgrupo","");
	$ls_seccion=$io_fun_activos->uf_obtenervalor_get("seccion","");
	$ls_unitri=$io_fun_activos->uf_obtenervalor_get("unitri","0");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=$io_report->uf_saf_load_invgenbie($ls_codemp,$li_ordenact,$ld_desde,$ld_hasta,$ls_coddesde,$ls_codhasta,$ls_grupo,$ls_subgrupo,$ls_seccion,$ls_unitri); // Cargar el DS con los datos de la cabecera del reporte
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
		$ls_desc_event="Generó un reporte de Inventario General de Bienes Muebles. Desde el Activo   ".$ls_coddesde." hasta   ".$ls_codhasta;
		$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_defactivo.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,4.8,3,3); // Configuración de los margenes en centímetros
		//$io_pdf->ezStartPageNumbers(900,50,10,'','',1); // Insertar el número de página
		uf_print_encabezado_pagina($ls_titulo,$ld_fecha,$io_pdf);
		$li_totrow=$io_report->ds->getRowCount("codact");
		$i=0;
		$ld_total_costo=0;
		$io_report->uf_saf_cargar_desinc_incorp($ls_codemp,$ld_desde,$ld_hasta,$ls_coddesde,$ls_codhasta,$ls_grupo,$ls_subgrupo,$ls_seccion,$ls_unitri);
		$li_totrow2=$io_report->ds_detalle->getRowCount("codact");
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
			$io_pdf->transaction('start'); // Iniciamos la transacción
			$li_numpag=$io_pdf->ezPageCount; // Número de página
			$ls_codact=$io_report->ds->data["codact"][$li_i];
			$ls_denact=$io_report->ds->data["denact"][$li_i];
			$ls_estact=$io_report->ds->data["estact"][$li_i];
			if ($ls_estact=='INCORPORADO')
			{
				$ls_estact='INC';
			}
			if ($ls_estact=='REGISTRADO')
			{
				$ls_estact='REG';
			}
			if ($ls_estact=='DESINCORPORADO')
			{
				$ls_estact='DES';
			}
			if ($ls_estact=='MODIFICADO')
			{
				$ls_estact='MOD';
			}
			if ($ls_estact=='CONTABILIZADO')
			{
				$ls_estact='CONT';
			}
			$ls_maract=$io_report->ds->data["maract"][$li_i];
			$ls_modact=$io_report->ds->data["modact"][$li_i];
			$ls_seract=$io_report->ds->data["seract"][$li_i];
			$li_costo=$io_report->ds->data["costo"][$li_i];
			$ls_idchapa=$io_report->ds->data["idchapa"][$li_i];
			$ls_coduniadm=$io_report->ds->data["coduniadm"][$li_i];
			$ls_fecregact=$io_funciones->uf_convertirfecmostrar($io_report->ds->data["fecregact"][$li_i]);
			$ld_total_costo=$ld_total_costo+$li_costo;
			$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
			$la_data[$li_i]=array('codact'=>$ls_codact,'denact'=>$ls_denact,'estact'=>$ls_estact,'maract'=>$ls_maract,
			                      'modact'=>$ls_modact,'seract'=>$ls_seract,'costo'=>$li_costo,'idchp'=>$ls_idchapa,
								  'coduniadm'=>$ls_coduniadm,'fecregart'=>$ls_fecregact);
		}
		/////////////////////////////////////////////////////////////////////////////////
		$li_exist_ant=0;
		$li_tot_inc=0;
		$li_tot_desinc=0;
		$li_exist_final=0;
		for($li_i=1;$li_i<=$li_totrow2;$li_i++)
		{
			$li_tot_inc=$li_tot_inc+$io_report->ds_detalle->data["tot_inc"][$li_i];
			$li_tot_desinc=$li_tot_desinc+$io_report->ds_detalle->data["tot_desinc"][$li_i];
		}
		$li_exist_final=$li_exist_final+$li_exist_ant+$li_tot_inc-$li_tot_desinc;
		$li_exist_ant=$io_fun_activos->uf_formatonumerico($li_exist_ant);
		$li_tot_inc=$io_fun_activos->uf_formatonumerico($li_tot_inc);
		$li_tot_desinc=$io_fun_activos->uf_formatonumerico($li_tot_desinc);
		$li_exist_final=$io_fun_activos->uf_formatonumerico($li_exist_final);
		/////////////////////////////////////////////////////////////////////////////////
		uf_print_cabecera($ls_codemp,$ls_nomemp,$io_pdf);
		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		$ld_total_costo=$io_fun_activos->uf_formatonumerico($ld_total_costo);
		uf_print_pie_cabecera($ld_total_costo,$io_pdf);
		uf_print_autorizacion($li_exist_final,$li_exist_ant,$li_tot_inc,$li_tot_desinc,$io_pdf);
		unset($la_data);			
		if($lb_valido)
		{
			//$io_pdf->ezStopPageNumbers(1,1);
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