<?php
/***********************************************************************************
* @fecha de modificacion: 07/09/2022, para la version de php 8.1 
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
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Gloriely Fréitez
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_srh;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_srh->uf_load_seguridad_reporte("SRH","sigesp_srh_r_listado_evaluacion_desempeno.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf,$ls_fechaperidesde,$ls_fechaperihasta)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo1,as_titulo2,as_titulo3,as_titulo4 // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Gloriely Fréitez
		// Fecha Creación: 11/02/2008
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(15,40,585,40);
        
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		
		$io_pdf->addText(540,770,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(546,764,6,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');

	    $io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSetY(710);
		
		 
		$la_data=array(array('titulo1'=>'<b>'.$as_titulo.'</b>'));
					
		$la_columnas=array('titulo1'=>'');
					
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 12, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				      	 'cols'=>array('titulo1'=>array('justification'=>'center','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		$io_pdf->ezSetY(680);
		$la_data=array(array('desde'=>'<b>PERIODO EVALUADO:     DESDE  </b>'.$ls_fechaperidesde,
		               'hasta'=>'<b>HASTA   </b>'.$ls_fechaperihasta));
					
		$la_columnas=array('desde'=>'',
						   'hasta'=>'');
					
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				      	 'cols'=>array('desde'=>array('justification'=>'left','width'=>270), // Justificación y ancho de la columna
						 			   'hasta'=>array('justification'=>'lef','width'=>300))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
		unset($la_columnas);
		unset($la_config);
	
	   
		
	    $io_pdf->ezSetY(660);
		$la_data[1]=array('nro'=>'<b>Nro Evaluación</b>',
							 'codigo'=>'<b>Código del Personal</b>',
		                     'nombre'=>'<b>Nombre y Apellido</b>',
							 'tipoeva'=>'<b>Unidad Administrativa</b>',
							 'puntaje'=>'<b>Rangos de Evaluación</b>',
							 'fechaeva'=>'<b>Fecha de Evaluación</b>');
		$la_columnas=array('nro'=>'',
						   'codigo'=>'',
						   'nombre'=>'',
						   'tipoeva'=>'',
						   'puntaje'=>'',
						   'fechaeva'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('nro'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'codigo'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>180), // Justificación y ancho de la columna
						 			   'tipoeva'=>array('justification'=>'center','width'=>120), // Justificación y ancho de la columna
						 			   'puntaje'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'fechaeva'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	
		$io_pdf->restoreState();
	    $io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	
	
     }// end function uf_print_encabezado_pagina
	 //-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
 	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//				 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime el detalle del reporte.
		//	   Creado Por: Ing. Gloriely Fréitez
		// Fecha Creación: 11/02/2008 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		 //$io_pdf->ezSetY(615);
		$la_columnas=array('nro'=>'',
						   'codigo'=>'',
						   'nombre'=>'',
						   'tipoeva'=>'',
						   'puntaje'=>'',
						   'fechaeva'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('nro'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'codigo'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>180), // Justificación y ancho de la columna
						 			   'tipoeva'=>array('justification'=>'center','width'=>120), // Justificación y ancho de la columna
						 			   'puntaje'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'fechaeva'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
    require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");	
	require_once("class_folder/sigesp_srh_class_report.php");
	$io_report=new sigesp_srh_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/utilidades/class_funciones_srh.php");
	$io_fun_srh=new class_funciones_srh('../../');
	$ls_estmodest=$_SESSION["la_empresa"]["estmodest"];
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
       $ls_titulo="<b>LISTADO DE EVALUACIONES DE DESEMPEÑO</b>"; 
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ld_fechades=$io_fun_srh->uf_obtenervalor_get("fechades","");
	$ld_fechahas=$io_fun_srh->uf_obtenervalor_get("fechahas","");
	$ls_codperdes=$io_fun_srh->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_srh->uf_obtenervalor_get("codperhas","");
	$ls_orden=$io_fun_srh->uf_obtenervalor_get("orden","");

	
	//-----------------------------------------------------------------------------------------------------------------------------------
	global $la_data;
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{

		$lb_valido=$io_report->uf_select_evaluacion_desemp($ld_fechades,$ld_fechahas,$ls_codperdes,$ls_codperhas,$ls_orden); // Cargar el DS con los datos del reporte
		if($lb_valido==false) // Existe algún error ó no hay registros
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
			 $io_pdf->ezSetCmMargins(5.53,3,3,3); // Configuración de los margenes en centímetros
			 $io_pdf->ezStartPageNumbers(570,47,8,'','',1); // Insertar el número de página
			 $li_i=0;
			 while(!$io_report->rs_data->EOF)
			 {
				$ls_codigo=$io_report->rs_data->fields["codper"];
				$ls_nroeval=$io_report->rs_data->fields["nroeval"];
				$ls_fecha=$io_report->rs_data->fields["fecha"];
				$ls_fechaperidesde=$io_report->rs_data->fields["fecinie"];
				$ls_fechaperihasta=$io_report->rs_data->fields["fecfine"];
				$ls_uniadm=$io_report->rs_data->fields["desuniadm"];
				$ls_totalodi=$io_report->rs_data->fields["totalodi"];
				$ls_totalcompe=$io_report->rs_data->fields["totalcompe"];
				$ls_nombreper=$io_report->rs_data->fields["nomper"];
				$ls_apellidoper=$io_report->rs_data->fields["apeper"];
				$ls_actuacion=$io_report->rs_data->fields["actuacion"];
				$ls_puntaje=$ls_totalodi+$ls_totalcompe;
			   	$ls_fechaperidesde=$io_funciones->uf_formatovalidofecha($ls_fechaperidesde);
				$ls_fechaperidesde=$io_funciones->uf_convertirfecmostrar($ls_fechaperidesde);
				$ls_fechaperihasta=$io_funciones->uf_formatovalidofecha($ls_fechaperihasta);
				$ls_fechaperihasta=$io_funciones->uf_convertirfecmostrar($ls_fechaperihasta);
				$ls_fecha=$io_funciones->uf_formatovalidofecha($ls_fecha);
				$ls_fecha=$io_funciones->uf_convertirfecmostrar($ls_fecha);
				$ls_cadena=$ls_nombreper."  ".$ls_apellidoper;
				$li_i++;
				$la_data[$li_i]=array('nro'=>$ls_nroeval,'codigo'=>$ls_codigo,'nombre'=>$ls_cadena,'tipoeva'=>$ls_uniadm,
									  'puntaje'=>$ls_actuacion,'fechaeva'=>$ls_fecha);
				$io_report->rs_data->MoveNext();
			}
			$ld_fechades=$io_funciones->uf_convertirfecmostrar($ld_fechades);
			$ld_fechahas=$io_funciones->uf_convertirfecmostrar($ld_fechahas);
			uf_print_encabezado_pagina($ls_titulo,$io_pdf,$ld_fechades,$ld_fechahas);
			uf_print_detalle($la_data,$io_pdf);
			if($lb_valido) // Si no ocurrio ningún error
				{
					$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
					$io_pdf->ezStream(); // Mostramos el reporte
				}
				else // Si hubo algún error
				{
					print("<script language=JavaScript>");
					print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
					print(" close();");
					print("</script>");		
				}
        }
	}
?>

