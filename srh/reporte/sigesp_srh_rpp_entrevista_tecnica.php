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
		//	    Arguments: as_titulo // T?tulo del reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 28/02/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_srh;
		
		$ls_descripcion="Gener? el Reporte ".$as_titulo;
		$lb_valido=$io_fun_srh->uf_load_seguridad_reporte("SRH","sigesp_srh_r_listado_entrevista_tecnica.php",$ls_descripcion);
		return $lb_valido;
	}
	
//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	    {
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 28/02/2008
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
		
		$io_pdf->ezSetY(677);
		$la_data=array(array('titulo1'=>'<b>'.$as_titulo.'</b>'));
					
		$la_columnas=array('titulo1'=>'');
					
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 12, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				      	 'cols'=>array('titulo1'=>array('justification'=>'center','width'=>570))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
        unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		
		
		$io_pdf->addText(15,645,9,"<b>DATOS DEL ASPIRANTE Y SU EVALUACION:</b>");
	    $io_pdf->ezSetY(550);
		$la_datatit[1]=array('codite'=>'<b>C?digo del Requisito</b>',
							 'denite'=>'<b>Denominaci?n</b>',
							 'valormax'=>'<b>Puntaje Req</b>',
							 'puntos'=>'<b>Puntaje Obt</b>');
		$la_columnas=array('codite'=>'',
						   'denite'=>'',
						   'valormax'=>'',
						   'puntos'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codite'=>array('justification'=>'center','width'=>120), // Justificaci?n y ancho de la columna
									   'denite'=>array('justification'=>'left','width'=>310),
									   'valormax'=>array('justification'=>'center','width'=>70),
						 			   'puntos'=>array('justification'=>'center','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
	
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	
			
		$io_pdf->restoreState();
	    $io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina	
	
//-------------------------------------------------------------------------------------------------------------------------------//

 function uf_print_encabezado_detalle($la_dataper,$io_pdf)
	 {
		global $io_pdf;		
		$io_pdf->ezSetY(642);
		$la_datap[1]=array('tipo_eval'=>'<b>Tipo de Evaluaci?n</b>',
		                     'codper'=>'<b>C?digo</b>',
		                     'nombre'=>'<b>Nombre</b>',
							 'descon'=>'<b>Concurso</b>',
							 'fecha'=>'<b>Fecha Evaluaci?n</b>',
							 'punenttec'=>'<b>Resultados</b>');
		$la_columnas=array('tipo_eval'=>'',
		                   'codper'=>'',
						   'nombre'=>'',
						   'descon'=>'',
						   'fecha'=>'',
						   'punenttec'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tipo_eval'=>array('justification'=>'center','width'=>90),
						               'codper'=>array('justification'=>'center','width'=>80),
						               'nombre'=>array('justification'=>'center','width'=>160),
									   'descon'=>array('justification'=>'left','width'=>120),
									   'fecha'=>array('justification'=>'center','width'=>60),
									   'punenttec'=>array('justification'=>'center','width'=>60))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_datap,$la_columnas,'',$la_config);
	
		
	    $io_pdf->ezSetY(618);
		$la_columnas=array('tipo_eval'=>'','codper'=>'',
						   'nombre'=>'',
						   'descon'=>'',
						   'fecha'=>'',
						   'punenttec'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						'cols'=>array('tipo_eval'=>array('justification'=>'center','width'=>90),
						              'codper'=>array('justification'=>'center','width'=>80),
						              'nombre'=>array('justification'=>'left','width'=>160),
									  'descon'=>array('justification'=>'left','width'=>120),
									  'fecha'=>array('justification'=>'center','width'=>60),
									  'punenttec'=>array('justification'=>'center','width'=>60))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_dataper,$la_columnas,'',$la_config);	
		
	    unset($la_dataper);
		unset($la_columnas);
		unset($la_config);	
				
		}

//---------------------------------------------------------------------------------------------------------------------------------//

 function uf_print_detalle($la_data,$ai_i,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//				   as_titcuentas // titulo de estructura presupuestaria
		//				   ai_i // total de registros
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Funci?n que imprime el detalle del reporte
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 10/06/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;		
		$io_pdf->ezSetY(535);
		$la_columnas=array('codite'=>'',
						   'denite'=>'',
						   'valormax'=>'',
						   'puntos'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codite'=>array('justification'=>'center','width'=>120), // Justificaci?n y ancho de la columna
									   'denite'=>array('justification'=>'left','width'=>310),
									   'valormax'=>array('justification'=>'right','width'=>70),
						 			   'puntos'=>array('justification'=>'right','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
					
	}// end function uf_print_detalle		

//-----------------------------------------------------------------------------------------------------------------------------------

   require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
  
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/utilidades/class_funciones_srh.php");
	$io_fun_srh=new class_funciones_srh('../../');

	require_once("class_folder/sigesp_srh_class_report.php");
	$io_report=new sigesp_srh_class_report();
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>Listado de Evaluaci?n de Entrevista T?cnica</b>";
	
//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------	
	$ls_orden=$io_fun_srh->uf_obtenervalor_get("orden","");
	$ls_tiporeporte=$io_fun_srh->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	
	$ld_concurdes=$io_fun_srh->uf_obtenervalor_get("curdes","");
	$ld_concurhas=$io_fun_srh->uf_obtenervalor_get("curhas","");
//----------------------------------------------------------------------------------------------------------------------------------//

 $lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
        $lb_valido=$io_report->uf_select_aspirante_entrevista($ld_concurdes,$ld_concurhas,$ls_orden);
		if ($lb_valido==false)
		{
		    print("<script language=JavaScript>");
			print(" alert('No hay nada que reportar');"); 
			print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{       
		    
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(3.6,5,3,3); // Configuraci?n de los margenes en cent?metros
			$io_pdf->ezStartPageNumbers(570,47,8,'','',1); // Insertar el n?mero de p?gina			
			uf_print_encabezado_pagina($ls_titulo,$io_pdf);
		    
			$lp_totrow=$io_report->ds_detalle->getRowCount("codper");
			$li_aux=0;
			for($lp_p=1;$lp_p<=$lp_totrow;$lp_p++)
			{
			 $li_aux++;
			 $li_totrow=0;
			 $ls_tipo_eval=$io_report->ds_detalle->data["tipo_eval"][$lp_p];
			 $ls_codper=$io_report->ds_detalle->data["codper"][$lp_p];
			 $ls_nombre=$io_report->ds_detalle->data["nombre1"][$lp_p];
					 
			 $ls_descon=$io_report->ds_detalle->data["descon"][$lp_p];
			 $ls_codcon=$io_report->ds_detalle->data["codcon"][$lp_p];
			 $ld_fecha=$io_report->ds_detalle->data["fecha"][$lp_p];
			 $ls_punenttec=$io_report->ds_detalle->data["punenttec"][$lp_p];
			 $la_dataper[$lp_p]=array('tipo_eval'=>$ls_tipo_eval,'codper'=>$ls_codper,'nombre'=>$ls_nombre,
			                          'descon'=>$ls_descon,'fecha'=>$ld_fecha,
									  'punenttec'=>$ls_punenttec); 
		
		     uf_print_encabezado_detalle($la_dataper,$io_pdf); //print $lp_totrow;
			 unset($la_dataper);
			 
		     
			 $io_report->uf_select_entrevista_tecnica($ls_tipo_eval,$ls_codcon,$ls_codper,$ld_fecha);
			 $li_totrow=$io_report->DS->getRowCount("codite"); //print $li_totrow." ".$lp_p."<br>";	 
			 for($li_i=1;$li_i<=$li_totrow;$li_i++)
			  {      
				$ls_codigo=$io_report->DS->data["codite"][$li_i];
				$ls_denite=trim ($io_report->DS->data["denite"][$li_i]);
				$ls_valormax=$io_report->DS->data["valormax"][$li_i];
				$ls_puntos=$io_report->DS->data["puntos"][$li_i];
				$la_data[$li_i]=array('codite'=>$ls_codigo,'denite'=>$ls_denite,
				                      'valormax'=>$ls_valormax,'puntos'=>$ls_puntos);
			  			  
			  }
			  if($li_i>1)
			  {
		   		uf_print_detalle($la_data,$li_totrow,$io_pdf);
		   		unset($la_data);
		   	  }
		   if($li_aux<$lp_totrow)		
			 {
			 	$io_pdf->ezNewPage(); // Insertar una nueva p?gina
			 }
			}
	    }	
		if($lb_valido) // Si no ocurrio ning?n error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi?n de los n?meros de p?gina
			$io_pdf->ezStream(); // Mostramos el reporte
		}
        else // Si hubo alg?n error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");	
		}
	}	
?>	