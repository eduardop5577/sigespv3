<?php
/***********************************************************************************
* @fecha de modificacion: 26/08/2022, para la version de php 8.1 
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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el título
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_cabecera($codigo,$descripcion,$io_pdf){
		
		$la_data=array(array('titulo'=>'<b> Codigo</b>','contenido'=>$codigo),
					   array('titulo'=>'<b> Descripción</b>','contenido'=>$descripcion));
					   
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'cols'=>array('titulo'=>array('justification'=>'left','width'=>120), // Justificación y ancho de la columna
						 			   'contenido'=>array('justification'=>'left','width'=>450))); // Justificación y ancho de la columna
		
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		return $io_pdf;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 13/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-15);
		$la_columnas=array('numdoc'=>'<b>Documento</b>',
						   'fecha'=>'<b>Fecha Emision</b>',
						   'proben'=>'<b>Proveedor/Beneficiario</b>',					
						   'tipodoc'=>'<b>Tipo</b>',
						   'monto'=>'<b>Monto</b>',
						   'estado'=>'<b>Estatus</b>',
						   'fecenv'=>'<b>Fecha Envio</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numdoc'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'proben'=>array('justification'=>'center','width'=>150),// Justificación y ancho de la columna
						 			   'tipodoc'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'estado'=>array('justification'=>'center','width'=>80),// Justificación y ancho de la columna
									   'fecenv'=>array('justification'=>'center','width'=>70))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		return $io_pdf;
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>LISTADO DE DOCUMENTOS </b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codigo         = $_GET["codigo"];
	$ls_descripcion    = $_GET["descripcion"];
	$ls_tipo    	   = $_GET["tipo"];
	$ld_fecdes    	   = $_GET["fecdes"];
	$ld_fechas    	   = $_GET["fechas"];
	$ls_estado    	   = $_GET["estatus"];
	$ls_bansol    	   = $_GET["bansol"];
	$ls_codban    	   = $_GET["codban"];
	$ls_cuenta    	   = $_GET["cuenta"];
	$ls_orden    	   = $_GET["orden"];
	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../class_folder/sigesp_scb_c_controldocumentos.php");
	$io_control = new sigesp_scb_c_controldocumentos('../../');
	//$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	/*if($lb_valido)
	{*/
		$rs_data   = $io_control->uf_buscar_documentos($ls_codigo,$ls_tipo,$ld_fecdes,$ld_fechas,$ls_estado,$ls_bansol,$ls_codban,$ls_cuenta,$ls_orden);
		//var_dump($rs_data);
		if($rs_data->EOF) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{
			
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuración de los margenes en centímetros
			$io_pdf=uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la página
			$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
			
			
			while(!$rs_data->EOF){
			$ls_numero  = $rs_data->fields["numero"];
				$ls_fecha   = $io_funciones->uf_convertirfecmostrar($rs_data->fields["fecha"]);
				$ls_tipodoc = $rs_data->fields["tipodoc"];
				$ld_monto   = number_format($rs_data->fields["monto"],2,",",".");
				$ls_estatus = $rs_data->fields["estado"];
				$ls_proben = $rs_data->fields["nombre"];
				$ls_detest  = "";
				$ls_fecenv  = "N/A";
				
				if ($ls_tipodoc!='SP') {
					switch ($ls_estatus) {
						case 'S':
							$ls_detest = 'Emitido';
							$la_data[]= array('numdoc'=>$ls_numero,'fecha'=>$ls_fecha,'tipodoc'=>$ls_tipodoc,'monto'=>$ld_monto,'estado'=>$ls_detest,'fecenv'=>$ls_fecenv,'proben'=>$ls_proben);
							break;
						
						case 'F':
							$ls_detest = 'Enviado a la Firma';
							$ls_fecenv = $io_funciones->uf_convertirfecmostrar($rs_data->fields['fecenvfir']);
							$la_data[]= array('numdoc'=>$ls_numero,'fecha'=>$ls_fecha,'tipodoc'=>$ls_tipodoc,'monto'=>$ld_monto,'estado'=>$ls_detest,'fecenv'=>$ls_fecenv,'proben'=>$ls_proben);
							break;
					
						case 'C':
							$ls_detest = 'Enviado a Caja';
							$ls_fecenv = $io_funciones->uf_convertirfecmostrar($rs_data->fields['fecenvcaj']);
							$la_data[]= array('numdoc'=>$ls_numero,'fecha'=>$ls_fecha,'tipodoc'=>$ls_tipodoc,'monto'=>$ld_monto,'estado'=>$ls_detest,'fecenv'=>$ls_fecenv,'proben'=>$ls_proben);
							break;
						
						case 'E':
							$ls_detest = 'Entregado';
							$ls_fecenv = $io_funciones->uf_convertirfecmostrar($rs_data->fields['fecenvcaj']);
							$la_data[]= array('numdoc'=>$ls_numero,'fecha'=>$ls_fecha,'tipodoc'=>$ls_tipodoc,'monto'=>$ld_monto,'estado'=>$ls_detest,'fecenv'=>$ls_fecenv,'proben'=>$ls_proben);
							break;
					}
				}
				else{
					if($ls_estatus=='X'){
						$ls_detest = 'Por programar pago';
						$la_data[]= array('numdoc'=>$ls_numero,'fecha'=>$ls_fecha,'tipodoc'=>$ls_tipodoc,'monto'=>$ld_monto,'estado'=>$ls_detest,'fecenv'=>$ls_fecenv,'proben'=>$ls_proben);
					}
				}
				$rs_data->MoveNext();
			}		
			
			/*if ($ls_codigo!='' && $ls_descripcion!='') {
				uf_print_cabecera($ls_codigo,$ls_descripcion,$io_pdf);;
			}*/
			$io_pdf=uf_print_detalle($la_data,$io_pdf);
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
//}		
?>
