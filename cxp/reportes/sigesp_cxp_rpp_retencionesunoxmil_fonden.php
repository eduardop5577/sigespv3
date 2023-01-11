<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
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
		//	    Arguments: as_titulo // T�tulo del reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 15/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Gener� el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesmunicipales.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 04/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],47,539,100,$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addText(155,585,9,"<b>Republica Bolivariana de Venezuela</b>");
		$io_pdf->addText(155,570,9,"<b>Ministerio del Poder Popular de Economia, Finanzas y Comercio Exterior</b>");
		$io_pdf->addText(155,555,9,"<b>Fondo de Desarrollo Nacional Fonden, S.A.</b>");
		$io_pdf->addText(30,535,9,"<b>COMPROBANTE DE RETENCION DEL IMPUESTO DEL UNO POR MIL (1X1000)</b>"); // Agregar el t�tulo
		$io_pdf->addtext(695,548,10,"Fecha de Entrega");
		$io_pdf->addText(710,538,9,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addtext(695,526,9,"P�gina");
		$io_pdf->ezStartPageNumbers(750,526,9,'','',1); // Insertar el n�mero de p�gina
//		$io_pdf->addText(27,570,9,"<b>REPUBLICA BOLIVARIANA DE VENEZUELA</b>"); // Agregar el t�tulo
//		$io_pdf->addText(27,560,9,"<b>GOBERNACION DEL ESTADO LARA</b>"); // Agregar el t�tulo
//		$io_pdf->addText(27,550,9,"<b>SERVICIO AUTONOMO DE ADMINISTRACION TRIBUTARIA DEL ESTADO LARA (SAATEL)</b>"); // Agregar el t�tulo
		//$io_pdf->addText(712,560,8,date("d/m/Y")); // Agregar la Fecha
		//$io_pdf->addText(718,553,7,date("h:i a")); // Agregar la Hora
		$io_pdf->addText(33,515,9,"En cumplimiento en lo establecido en el Art�culo 18 del Reglamento de la Ley Especial de Timbre Fiscal para el Distrito");
		$io_pdf->addText(33,500,9,"Capital No 84 de fecha 22/08/2011.");


		$io_pdf->addtext(70,85,10,"Glaidymar M. Cabrera A.");
		$io_pdf->addtext(50,75,10,"<b> JEFE DE LA DIVISION DE PAGOS </b>");
		$io_pdf->addtext(40,65,10,"Elaborado en medios electr�nicos por el FONDEN, S.A. ");
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numcon,$ad_fecrep,$as_agenteret,$as_rifagenteret,$as_perfiscal,$as_licagenteret,$as_diragenteret,
							   $as_nomsujret,$as_rif,$as_numlic,$ai_estcmpret,$as_conceptosp,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_numcon // N�mero de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   as_agenteret // agente de Retenci�n
		//	    		   as_rifagenteret // Rif del Agente de Retenci�n
		//	    		   as_perfiscal // Per�odo Fiscal
		//	    		   as_licagenteret // N�mero de licencia de agente de retenci�n
		//	    		   as_diragenteret // Direcci�n del agente de retenci�n
		//	    		   as_nomsujret // Nombre del sujeto retenido
		//	    		   as_rif // Rif del sujeto retenido
		//	    		   as_numlic // N�mero de Licencia del sujeto retenido
		//	    		   ai_estcmpret // Estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 17/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetDy(-4);
	 	if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,495,180,30);		
			$io_pdf->addText(90,505,15,"<b> ANULADO </b>"); 
		}	
		$ls_anio=substr($as_perfiscal,0,4);
		$ls_mes=substr($as_perfiscal,4,2);
		$io_pdf->Rectangle(675,453,94,28);
		$io_pdf->addText(685,471,8,"<b>PERIODO FISCAL</b>"); // Agregar el titulo
		$io_pdf->addText(685,458,8,"A�o: ".$ls_anio." Mes: ".$ls_mes); // Agregar el titulo
		$io_pdf->ezSetY(525);
		$la_data[1]=array('name'=>'<b>Numero Comprobante </b>');
		$la_data[2]=array('name'=>$as_numcon);				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>600, // Orientaci�n de la tabla
						 'width'=>140, // Ancho de la tabla						 
						 'maxWidth'=>140,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>140))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);		
		$io_pdf->ezSetY(525);
		$la_data[1]=array('name'=>'<b>Fecha de Emisi�n</b>');
		$la_data[2]=array('name'=>date("d/m/Y"));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>720, // Orientaci�n de la tabla
						 'width'=>90, // Ancho de la tabla						 
						 'maxWidth'=>90,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>90))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->ezSetY(500);
		$la_data[1]=array('titulo'=>'<b>DATOS DEL AGENTE DE RETENCION</b>');
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'xPos'=>400, // Orientación de la tabla
						 'width'=>500, // Ancho de la tabla
						 'justification'=>'center', // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>500))); // Ancho M�imo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'<b>Nombre: </b>'.$as_agenteret);
		$la_data[2]=array('name'=>'');				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>190, // Orientaci�n de la tabla
						 'width'=>310, // Ancho de la tabla						 
						 'maxWidth'=>310,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>310))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);								 
		$io_pdf->ezSetY(482);
		$la_data[1]=array('name'=>'<b>R.I.F.: </b>'.$as_rifagenteret);
		$la_data[2]=array('name'=>'');				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>515, // Orientaci�n de la tabla
						 'width'=>320, // Ancho de la tabla						 
						 'maxWidth'=>320,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>320))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);	

		$io_pdf->ezSetY(450);
		$la_data[1]=array('name'=>'<b>Direcci�n: </b>'.$as_diragenteret);
		$la_data[2]=array('name'=>'');				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>405, // Orientaci�n de la tabla
						 'width'=>740, // Ancho de la tabla						 
						 'maxWidth'=>740,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>740))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);		
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('titulo'=>'<b>DATOS DEL CONTRIBUYENTE</b> ');
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xPos'=>400, // Orientación de la tabla
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla
						 'justification'=>'center', // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>500))); // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
//		$io_pdf->ezSetY(420);
		$la_data[1]=array('name'=>'<b>Nombre o Raz�n Social: </b>'.$as_nomsujret);
		$la_data[2]=array('name'=>'');				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>190, // Orientaci�n de la tabla
						 'width'=>310, // Ancho de la tabla						 
						 'maxWidth'=>310,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>310))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);								 
		$io_pdf->ezSetY(408);
		$la_data[1]=array('name'=>'<b>R.I.F: </b>'.$as_rif);
		$la_data[2]=array('name'=>'');				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>515, // Orientaci�n de la tabla
						 'width'=>320, // Ancho de la tabla						 
						 'maxWidth'=>320,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>320))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);        
		unset($la_data);
		unset($la_columna);
		unset($la_config);	

	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
			
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$ai_totbasimp,$ai_totmonimp,$ai_totmoniva,$as_rifagenteret,$li_totmontotdoc,$io_pdf)
	{						 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   ai_totbasimp // Total de la base imponible
		//	    		   ai_totmonimp // Total monto imponible
		//                 ai_totmoniva // Total monto iva
		//	    		   as_rifagenteret // Rif del Agente de Retenci�n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		//     Modificado Por: Ing. Arnaldo Su�rez
		// Fecha Creaci�n: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetDy(-15);
		$la_datatit[1]=array('name'=>'<b>Importe Gravado      </b>');
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>2, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>545, // Orientaci�n de la tabla
						 'width'=>320, // Ancho de la tabla						 
						 'maxWidth'=>320,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>80))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_datatit,$la_columna,'',$la_config);        
		unset($la_datatit);
		unset($la_columna);
		unset($la_config);	

		$la_data1[1]=array('numero'=>'<b>Item No.</b>',
						  'fecfac'=>'<b>Fecha Factura</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
						  'numref'=>'<b>Numero de Control</b>',
						  'numcom'=>'<b>Referencia</b>',
						  'montotdoc'=>'<b>Total Facturado</b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>% Alic.</b>',
						  'iva_ret'=>'<b>Impuesto Retenido</b>');
		$la_columna=array('numero'=>'<b>Item No.</b>',
						  'fecfac'=>'<b>Fecha Factura</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
						  'numref'=>'<b>Numero de Control</b>',
						  'montotdoc'=>'<b>Total Facturado</b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>% Alic.</b>',
						  'iva_ret'=>'<b>Impuesto Retenido</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>740, // Ancho de la tabla
						 'maxWidth'=>740, // Ancho M�nimo de la tabla
						 'xPos'=>405, // Orientaci�n de la tabla
						 'cols'=>array('numero'=>array('justification'=>'center','width'=>50), // Justificacion y ancho de la columna
						 			   'fecfac'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>100),
						 			   'numcom'=>array('justification'=>'center','width'=>100),
						 			   'montotdoc'=>array('justification'=>'center','width'=>80),
						 			   'baseimp'=>array('justification'=>'center','width'=>80),
						 			   'porimp'=>array('justification'=>'center','width'=>40),
   						 			   'iva_ret'=>array('justification'=>'right','width'=>80))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_columna=array('numero'=>'<b>Item No.</b>',
						  'fecfac'=>'<b>Fecha Factura</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
						  'numref'=>'<b>Numero de Control</b>',
						  'montotdoc'=>'<b>Total Facturado</b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>% Alic.</b>',
						  'iva_ret'=>'<b>Impuesto Retenido</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>740, // Ancho de la tabla
						 'maxWidth'=>740, // Ancho M�nimo de la tabla
						 'xPos'=>405, // Orientaci�n de la tabla
						 'cols'=>array('numero'=>array('justification'=>'center','width'=>50), // Justificacion y ancho de la columna
						 			   'fecfac'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>100),
						 			   'montotdoc'=>array('justification'=>'center','width'=>80),
						 			   'baseimp'=>array('justification'=>'center','width'=>80),
						 			   'porimp'=>array('justification'=>'center','width'=>40),
   						 			   'iva_ret'=>array('justification'=>'right','width'=>80))); 
		$io_pdf->ezSetDy(-0.5);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);		
		$la_data1[1]=array('total'=>'<b>Total</b>',		
						   'montotot'=>'<b>'.$li_totmontotdoc.'</b>',
						   'monto'=>'<b>'.$ai_totbasimp.'</b>',
						   'alic'=>'',
						   'imponible'=>'<b>'.$ai_totmoniva.'</b>');
		$la_columna=array('total'=>'<b>total</b>',		
						  'montotot'=>'<b>monto</b>',		
						  'monto'=>'<b>monto</b>',		
						   'alic'=>'',
						  'imponible'=>'<b>monto</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>360, // Ancho de la tabla
						 'maxWidth'=>360, // Ancho M�nimo de la tabla
						 'xPos'=>510, // Orientaci�n de la tabla
						 'cols'=>array('total'=>array('justification'=>'center','width'=>110), // Justificacion y ancho de la columna
   						 			   'montotot'=>array('justification'=>'right','width'=>80),
   						 			   'monto'=>array('justification'=>'right','width'=>80),
   						 			   'alic'=>array('justification'=>'right','width'=>40),
   						 			   'imponible'=>array('justification'=>'right','width'=>80))); 
		$io_pdf->ezSetDy(-0.5);
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);		

	}// end function uf_print_detalle

	function uf_print_sello($io_pdf)
	{
	    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_sello
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Jennifer Rivero
		//     Modificado Por: Ing. Arnaldo Su�rez
		// Fecha Creaci�n: 13/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
	    $la_data[1]=array('name1'=>'<b>ELABORADO POR</b>',
	                    'name2'=>'<b>JEFE DE LA UNIDAD</b>',
						'name3'=>'<b>TESORERO </b>');	
        $la_columna=array('name1'=>'','name2'=>'','name3'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 11, // Tama�o de Letras
						  'showLines'=>2, // Mostrar L�neas
						  'shaded'=>0, // Sombra entre l�neas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientaci�n de la tabla
						  'colGap'=>1,
						  'width'=>690,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>240),						                
										'name2'=>array('justification'=>'center','width'=>200),
										'name3'=>array('justification'=>'center','width'=>250))); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config); 		
		 
	    $la_data[1]=array('name1'=>'','name2'=>'','name3'=>'');
		$la_data[2]=array('name1'=>'','name2'=>'','name3'=>'');	
		$la_data[3]=array('name1'=>'','name2'=>'','name3'=>'');	
		$la_data[4]=array('name1'=>'','name2'=>'','name3'=>'');	
		$la_data[5]=array('name1'=>'','name2'=>'','name3'=>'');		
        $la_columna=array('name1'=>'','name2'=>'','name3'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados					  
						  'shaded'=>0, // Sombra entre l�neas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientaci�n de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>240),						                
										'name2'=>array('justification'=>'center','width'=>200),
										'name3'=>array('justification'=>'center','width'=>250))); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config); 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $la_data2[1]=array('name1'=>'<b>RECIBE CONFORME</b>',
	                       'name2'=>'<b>SELLO</b>');	
        $la_columna=array('name1'=>'','name2'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 11, // Tama�o de Letras
						  'showLines'=>2, // Mostrar L�neas
						  'shaded'=>0, // Sombra entre l�neas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientaci�n de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>440),						                
										'name2'=>array('justification'=>'center','width'=>250))); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data2,$la_columna,'',$la_config); 		
			    
		$la_data3[1]=array('name1'=>'','name2'=>'','name3'=>'');
		$la_data3[2]=array('name1'=>'<b> Nombre y Apellido:                               ________________________________</b>','name2'=>'');	
		$la_data3[3]=array('name1'=>'','name2'=>'');	
		$la_data3[4]=array('name1'=>'<b> C�dula de Identidad:                            ________________________________</b>','name2'=>'');	
		$la_data3[5]=array('name1'=>'','name2'=>'');	
		$la_data3[6]=array('name1'=>'<b> Fecha en se que Recibe Comprobante:                       ___________________</b>','name2'=>'');	
		$la_data3[7]=array('name1'=>'','name2'=>'');
		
        $la_columna=array('name1'=>'','name2'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados					  
						  'shaded'=>0, // Sombra entre l�neas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientaci�n de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'left','width'=>440),						                
										'name2'=>array('justification'=>'center','width'=>250))); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data3,$la_columna,'',$la_config); 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo= "COMPROBANTE DE RETENCION DE IMPUESTO DE TIMBRE FISCAL";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_mes=$io_fun_cxp->uf_obtenervalor_get("mes","");
	$ls_anio=$io_fun_cxp->uf_obtenervalor_get("anio","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
	$ls_licagenteret=$_SESSION["la_empresa"]["numlicemp"];
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$la_comprobantes=explode('-',$ls_comprobantes);
		$la_datos=array_unique($la_comprobantes);
		$li_totrow=count((array)$la_datos);
		sort($la_datos,SORT_STRING);
		if($li_totrow<=0)
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else
		{
			set_time_limit(1800);
			$io_pdf = new Cezpdf("LETTER","landscape");
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(3.5,1.5,3,3);
			$lb_valido=true;
			$ls_numcomant = "";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				uf_print_encabezado_pagina($ls_titulo,$io_pdf);
				$ls_numcom=$la_datos[$li_z];
				$lb_valido=$io_report->uf_retencionesunoxmil_proveedor($ls_numcom,$ls_mes,$ls_anio);
				if($lb_valido)
				{
					$li_total=$io_report->DS->getRowCount("numcom");
					for($li_i=1;$li_i<=$li_total;$li_i++)
					{
						$ls_numcon=$io_report->DS->data["numcom"][$li_i];		 								
						$ls_codret=$io_report->DS->data["codret"][$li_i];			   
						$ls_fecrep=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecrep"][$li_i]);
						$ls_perfiscal=$io_report->DS->data["perfiscal"][$li_i];						
						$ls_codsujret=$io_report->DS->data["codsujret"][$li_i];			     
						$ls_nomsujret=$io_report->DS->data["nomsujret"][$li_i];	
						$ls_rif=$io_report->DS->data["rif"][$li_i];	
						$ls_dirsujret=$io_report->DS->data["dirsujret"][$li_i];		
						$li_estcmpret=$io_report->DS->data["estcmpret"][$li_i];	
						$ls_numlic=$io_report->DS->data["numlic"][$li_i];									
						if ($ls_numcom!=$ls_numcomant)
					   	{
						    if ($li_z>=1)
							{
								 $io_pdf->ezNewPage();  
							}
							$lb_valido=$io_report->uf_retencion1x1000_detalle_solpago($ls_numcom);
							if ($lb_valido)
							{
								
								$ls_conceptosp=$io_report->ds_detalle_solpago1x1000->data['descrip'][1];
								
							}
							
						    uf_print_cabecera($ls_numcon,$ls_fecrep,$ls_agenteret,$ls_rifagenteret,$ls_perfiscal,$ls_licagenteret,
										  $ls_diragenteret,$ls_nomsujret,$ls_rif,$ls_numlic,$li_estcmpret,$ls_conceptosp,$io_pdf);
							$ls_numcomant=$ls_numcom;
					   	}
					}											
					$lb_valido=$io_report->uf_retencionesunoxmil_detalles($ls_numcom);
					if($lb_valido)
					{
						$li_totalbaseimp=0;
						$li_totalmontoimp=0;
						$li_totmontoiva=0;
						$li_totmontotdoc=0;
						$li_total=$io_report->ds_detalle->getRowCount("numfac");			   
						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							$li_montotdoc=$io_report->uf_retencionesmunicipales_monfact($ls_numcon);
							$ls_numsop=$io_report->ds_detalle->data["numsop"][$li_i];					
							$ld_fecfac=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecfac"][$li_i]);	
							$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];	
							$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];	              
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];
							$li_iva_ret=$io_report->ds_detalle->data["iva_ret"][$li_i];	
							$li_porimp=$io_report->ds_detalle->data["porimp"][$li_i];	
							$li_totimp=$io_report->ds_detalle->data["totimp"][$li_i];	
							$li_montotdoc=$io_report->ds_detalle->data["totcmp_con_iva"][$li_i];	

							$li_totalbaseimp=$li_totalbaseimp + $li_baseimp ;	
							$li_totalmontoimp=$li_totalmontoimp + $li_totimp;
							$li_totmontotdoc=$li_totmontotdoc+$li_montotdoc;
							$li_totmontoiva=$li_totmontoiva+$li_iva_ret;
							$li_iva_ret=number_format($li_iva_ret,2,",",".");	
							$li_baseimp=number_format($li_baseimp,2,",",".");			
							$li_porimp=number_format($li_porimp,2,",",".");			
							$li_totimp=number_format($li_totimp,2,",",".");							
							$li_montotdoc=number_format($li_montotdoc,2,",",".");							
							$la_data[$li_i]=array('numero'=>$li_i,'fecfac'=>$ld_fecfac,'numfac'=>$ls_numfac,'numcomp'=>$ls_numsop,
												  'numref'=>$ls_numref,'baseimp'=>$li_baseimp,'iva_ret'=>$li_iva_ret,'porimp'=>$li_porimp,'totimp'=>$li_montotdoc,'montotdoc'=>$li_montotdoc );														
						  }																		 																						  
  						  $li_totalbaseimp= number_format($li_totalbaseimp,2,",","."); 
  						  $li_totalmontoimp= number_format($li_totalmontoimp,2,",","."); 
						  $li_totmontoiva= number_format($li_totmontoiva,2,",","."); 
						  $li_totmontotdoc= number_format($li_totmontotdoc,2,",","."); 
						  uf_print_detalle($la_data,$li_totalbaseimp,$li_totalmontoimp,$li_totmontoiva,$ls_rifagenteret,$li_totmontotdoc,$io_pdf);
						  //uf_print_sello($io_pdf);
						  unset($la_data);							 
						  
					}
				}
				$io_report->DS->reset_ds();
			}
			if($lb_valido) // Si no ocurrio ning�n error
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else  // Si hubo alg�n error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				//print(" close();");
				print("</script>");		
			}
			unset($io_pdf);
		}
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_cxp);
?> 
