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

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    REPORTE: Formato de salida  de la Orden de Compra
//  ORGANISMO: Ninguno en particular
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
	function uf_print_encabezado_pagina($as_estcondat, $as_numordcom,$ad_fecordcom,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: 
		//	    		   as_numordcom // numero de la orden de compra
		//	    		   ad_fecordcom // fecha de la orden de compra
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 21/06/2007
		// Modificado por: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)  Fecha Ultima MOdificacion: 02-03-2012		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                global $io_pdf;
                
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(15,40,585,40);
		$io_pdf->line(480,700,480,760);
		$io_pdf->line(480,730,585,730);
                $io_pdf->Rectangle(15,700,570,60);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		if($as_estcondat=="B") 
                {
                    $ls_titulo="Orden de Compra";	
			 $ls_titulo_grid="Bienes";
                }
                else
                {
                     $ls_titulo="Orden de Servicio";
                                 $ls_titulo_grid="Servicios";
                }
		
		$li_tm=$io_pdf->getTextWidth(14,$ls_titulo);
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,730,14,$ls_titulo); // Agregar el título
		$io_pdf->addText(485,740,9," <b>No. </b>".$as_numordcom); // Agregar el título
		$io_pdf->addText(485,710,9,"<b>Fecha </b>".$ad_fecordcom); // Agregar el título
		$io_pdf->addText(540,770,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(546,764,6,date("h:i a")); // Agregar la Hora

		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
				
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_coduniadm,$as_denuniadm, $as_codfuefin, $as_denfuefin,$as_codigo,$as_nombre,$as_conordcom,$as_rifpro,$as_diaplacom,$as_dirpro,
							   $as_forpagcom,$ld_perentdesde,$ld_perenthasta,$as_formaentrega,$as_nrofiscalexterior,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime la cabecera por concepto
		//	   Creado Por: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)
		// Fecha Creación: 02/03/2012
		// MODIFICADO POR: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)   FECHA ULTIMA MODIFICACION: 08/01/2015
        // MODIFICADO POR: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)   FECHA ULTIMA MODIFICACION: 02/07/2015		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                global $io_pdf;
                $io_pdf->ezSetY(695);
		// AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA EL 02-07-2015
		if($as_nrofiscalexterior!='')
		{
			$la_data[1]=array('columna1'=>'<b>Proveedor:</b> '.$as_nombre.',  '.$as_nrofiscalexterior.'',
							 'columna2'=>'<b>Direccion:</b> '.$as_dirpro.'');		
		}
		else
		{
			$la_data[1]=array('columna1'=>'<b>Proveedor:</b> '.$as_nombre.', <b>Rif:</b> '.$as_rifpro.'',
							 'columna2'=>'<b>Direccion:</b> '.$as_dirpro.'');
		}
		// FIN AGREGADO Y MODIFICADO
		$la_columna=array('columna1'=>'','columna2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>250), // Justificación y ancho de la columna
						               'columna2'=>array('justification'=>'left','width'=>320))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$ls_uniadm=$as_coduniadm."  -  ".$as_denuniadm;
		$la_data[1]=array('columna1'=>'<b>Unidad Ejecutora</b>    '.$ls_uniadm,'columna2'=>'<b>Forma de Pago</b>    '.$as_forpagcom);
		$la_columnas=array('columna1'=>'','columna2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>300), // Justificación y ancho de la columna
						 			   'columna2'=>array('justification'=>'left','width'=>270))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$ls_fuefin=$as_codfuefin."  -  ".$as_denfuefin;
		$la_data[1]=array('columna1'=>'<b>Fuente Financiamiento</b>   '.$ls_fuefin,'columna2'=>'<b> Plazo en Días</b>    '.$as_diaplacom);
		$la_columnas=array('columna1'=>'','columna2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>300), // Justificación y ancho de la columna
						 			   'columna2'=>array('justification'=>'left','width'=>270))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		$la_data[1]=array('columna1'=>'<b>Período de Entrega    Desde:</b> '.$ld_perentdesde.'    <b>Hasta:</b> '.$ld_perenthasta.'','columna2'=>'<b>Forma de Entrega: </b> '.$as_formaentrega.'');
		$la_columnas=array('columna1'=>'','columna2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>300),   // Justificación y ancho de la columna
						               'columna2'=>array('justification'=>'left','width'=>270))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);		
		

		$la_data[1]=array('columna1'=>'<b>Concepto</b> '.$as_conordcom);
		$la_columnas=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'full','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
			
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$ls_estcondat,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data ---> arreglo de información
		//	    		   io_pdf ---> Instancia de objeto pdf
		//    Description: función que imprime el detalle 
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 21/06/2007
		// Modificado por: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)  Fecha Ultima MOdificacion: 02-03-2012		
		// Modificado por: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)  Fecha Ultima MOdificacion: 04-12-2012				
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                global $io_pdf;
                global $ls_bolivares;
                
		if($ls_estcondat=='B')
		{
			$ls_titulo_grid="Bienes";
		}
		else
		{
			$ls_titulo_grid="Servicios";
		}
		$io_pdf->ezSetDy(-10);
		$la_datatitulo[1]=array('columna1'=>'<b> Detalle de '.$ls_titulo_grid.'</b>');
		$la_columnas=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'center','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatitulo,$la_columnas,'',$la_config);
		unset($la_datatitulo);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-2);
		$la_columnas=array('codigo'=>'<b>Código</b>',
						   'denominacion'=>'<b>Denominacion</b>',
						   'cantidad'=>'<b>Cant.</b>',
						   // MODIFICADO POR OFIMATICA DE VENEZUELA EL 27-01-2012
						   'medida'=>'<b>U/M</b>',
						   // FIN DE LO MODIFICADO POR OFIMATICA DE VENEZUELA
						   'cosuni'=>'<b>Costo '.$ls_bolivares.'</b>',
						   'baseimp'=>'<b>Sub-Total '.$ls_bolivares.'</b>',
						   'cargo'=>'<b>Cargo '.$ls_bolivares.'</b>',
						   'montot'=>'<b>Total '.$ls_bolivares.'</b>');
		// AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA EL 04/12/2012
		if($ls_estcondat=='B')
		{						   
			$la_config=array('showHeadings'=>1, // Mostrar encabezados
							 'fontSize' => 8, // Tamaño de Letras
							 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
							 'showLines'=>1, // Mostrar Líneas
							 'shaded'=>0, // Sombra entre líneas
							 'width'=>570, // Ancho de la tabla
							 'maxWidth'=>570, // Ancho Máximo de la tabla
							 'xOrientation'=>'center', // Orientación de la tabla
							 // MODIFICADO POR OFIMATICA DE VENEZUELA EL 04/12/2012
							 'cols'=>array('codigo'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
							 // FIN DE LO MODIFICADO POR OFIMATICA DE VENEZUELA
										   'denominacion'=>array('justification'=>'left','width'=>200), // Justificación y ancho de la columna
										   'cantidad'=>array('justification'=>'left','width'=>30), // Justificación y ancho de la columna
										   // MODIFICADO POR OFIMATICA DE VENEZUELA EL 27-01-2012
										   'medida'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
										   // FIN DE LO MODIFICADO POR OFIMATICA DE VENEZUELA
										   'cosuni'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
										   'baseimp'=>array('justification'=>'right','width'=>65), // Justificación y ancho de la columna
										   'cargo'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
										   'montot'=>array('justification'=>'right','width'=>70))); // Justificación y ancho de la columna
		}
		else
		{
			$la_config=array('showHeadings'=>1, // Mostrar encabezados
							 'fontSize' => 8, // Tamaño de Letras
							 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
							 'showLines'=>1, // Mostrar Líneas
							 'shaded'=>0, // Sombra entre líneas
							 'width'=>570, // Ancho de la tabla
							 'maxWidth'=>570, // Ancho Máximo de la tabla
							 'xOrientation'=>'center', // Orientación de la tabla
							 // MODIFICADO POR OFIMATICA DE VENEZUELA EL 04/12/2012
							 'cols'=>array('codigo'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
							 // FIN DE LO MODIFICADO POR OFIMATICA DE VENEZUELA
										   'denominacion'=>array('justification'=>'left','width'=>180), // Justificación y ancho de la columna
										   'cantidad'=>array('justification'=>'left','width'=>30), // Justificación y ancho de la columna
										   // MODIFICADO POR OFIMATICA DE VENEZUELA EL 27-01-2012
										   'medida'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
										   // FIN DE LO MODIFICADO POR OFIMATICA DE VENEZUELA
										   'cosuni'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
										   'baseimp'=>array('justification'=>'right','width'=>65), // Justificación y ancho de la columna
										   'cargo'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
										   'montot'=>array('justification'=>'right','width'=>70))); // Justificación y ancho de la columna
		}
		// FIN DE LO AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA 
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_cuentas($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_cuentas
		//		   Access: private 
		//	    Arguments: la_data ---> arreglo de información
		//	    		   io_pdf ---> Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 21/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                global $io_pdf;
                $io_pdf->ezSetDy(-5);
		global $ls_estmodest, $ls_bolivares;
		if($ls_estmodest==1)
		{
			$ls_titulo="Estructura Presupuestaria";
		}
		else
		{
			$ls_titulo="Estructura Programatica";
		}
		$la_datatit[1]=array('titulo'=>'<b> Detalle de Presupuesto </b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
		unset($la_datatit);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-2);
		$la_columnas=array('codestpro'=>'<b>'.$ls_titulo.'</b>',
						   'cuenta'=>'<b>Cuenta</b>',
						   'denominacion'=>'<b>Denominacion</b>',
						   'monto'=>'<b>Total '.$ls_bolivares.'</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codestpro'=>array('justification'=>'center','width'=>170), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'denominacio'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($li_subtot,$li_totcar,$li_montot,$ls_monlet, $as_observacion,$as_mensaje,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		    Acess: private 
		//	    Arguments: li_subtot ---> Subtotal del articulo
		//	    		   li_totcar -->  Total cargos
		//	    		   li_montot  --> Monto total
		//	    		   ls_monlet   //Monto en letras
		//                 as_observacion   // Observacion de la orden de compra
		//                 as_mensaje  // Mensaje sobre la sujecion a la responsabilidad social.
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime los totales
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 21/06/2007
		// Modificado por: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)  Fecha Ultima MOdificacion: 02-03-2012
		// Modificado por: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)  Fecha Ultima MOdificacion: 29-05-2012		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                global $io_pdf;
                global $ls_bolivares;
		
		$la_data[1]=array('titulo'=>'<b>Sub Total '.$ls_bolivares.'</b>','contenido'=>$li_subtot,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificación y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>Cargos '.$ls_bolivares.'</b>','contenido'=>$li_totcar,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificación y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>Total '.$ls_bolivares.'</b>','contenido'=>$li_montot,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificación y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-5);
		$la_data[1]=array('titulo'=>'<b> Son: '.$ls_monlet.'</b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		// AGREGADO POR OFIMATICA DE VENEZUELA EL 02-03-2012
		$la_data[1]=array('columna1'=>'<b>Observacion:</b>         '.$as_observacion);
		$la_columnas=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);		
		// AGREGADO POR OFIMATICA DE VENEZUELA EL 29-05-2012
		if($as_mensaje!='')
		{
			$la_data[1]=array('columna1'=>'<b>Obligatoriedad del cumplimiento del compromiso de responsabilidad social</b>');		
			$la_data[2]=array('columna1'=>$as_mensaje);
			$la_columnas=array('columna1'=>'');
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' => 9, // Tamaño de Letras
							 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
							 'showLines'=>1, // Mostrar Líneas
							 'shaded'=>0, // Sombra entre líneas
							 'width'=>570, // Ancho de la tabla
							 'maxWidth'=>570, // Ancho Máximo de la tabla
							 'xOrientation'=>'center', // Orientación de la tabla
							 'cols'=>array('columna1'=>array('justification'=>'full','width'=>570))); // Justificación y ancho de la columna
			$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);		
		}
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_firmas($io_pdf)
	{
            global $io_pdf;
                // METODO AGREGADO POR OFIMATICA DE VENEZUELA EL 10-04-2012 PARA EL MANEJO MAS EFICIENTE DE LAS FIRMAS YA QUE EL PINTAR EL ESQUEMA DE FIRMAS ERA MAS COMPLEJO POR NO CONOCER LA UBICACION 
		// DEL PUNTERO, AHORA CON EL MANEJO DE TABLAS LAS FUNCIONES PROPIAS DE LA MISMA DETERMINAN EL PUNTERO ACTUAL Y UBICAN LA TABLA EN LA UBICACION ACTUAL DEL PUNTERO Y DE SER NECESARIO 
		// LA IMPRIMEN EN UNA NUEVA PAGINA PARA NO SALIRSE DEL MARGEN NI DEJAR INCOMPLETA LA TABLA
                global $io_pdf;
                $io_pdf->ezText(" ",12);
		$la_data[1]=array('columna1'=>' ','columna2'=>' ','columna3'=>' ','columna4'=>' ','columna5'=>' ');
		$la_data[2]=array('columna1'=>' ','columna2'=>' ','columna3'=>' ','columna4'=>' ','columna5'=>' ');
		$la_data[3]=array('columna1'=>' ','columna2'=>' ','columna3'=>' ','columna4'=>' ','columna5'=>' ');
		$la_data[4]=array('columna1'=>' ','columna2'=>' ','columna3'=>' ','columna4'=>' ','columna5'=>' ');
		$la_data[5]=array('columna1'=>'GCIA. DE COMPRAS Y CONTRATOS','columna2'=>'OFIC. DE GESTION ADMINISTRATIVA','columna3'=>'EJECUCIÓN PRESUPUESTARIA','columna4'=>'PRESIDENTE','columna5'=>'FIRMA / SELLO');
		$la_columna=array('columna1'=>'ELABORADO POR','columna2'=>'APROBADO POR','columna3'=>'CONTABILIZADO POR','columna4'=>'AUTORIZADO POR','columna5'=>'PROVEEDOR');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 6.5, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'center','width'=>130), // Justificación y ancho de la columna
						 			   'columna2'=>array('justification'=>'center','width'=>130),
									   'columna3'=>array('justification'=>'center','width'=>115),
									   'columna4'=>array('justification'=>'center','width'=>100),
									   'columna5'=>array('justification'=>'center','width'=>95))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
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
	$ls_estmodest = $_SESSION["la_empresa"]["estmodest"];

	//Instancio a la clase de conversión de numeros a letras.
	include("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
	$numalet= new class_numero_a_letra();
	//imprime numero con los valore por defecto
	//cambia a minusculas
	$numalet->setMayusculas(1);
	//cambia a femenino
	$numalet->setGenero(1);
	//cambia moneda
	$numalet->setMoneda("Bolivares");
	//cambia prefijo
	$numalet->setPrefijo("***");
	//cambia sufijo
	$numalet->setSufijo("***");
	$ls_tiporeporte=$io_fun_soc->uf_obtenervalor_get("tiporeporte",1);
	//AGREGADO POR OFIMATICA DE VENEZUELA EL 09/07/2018
	$ls_bolivares=trim($_SESSION["la_empresa"]["expresionmonet"]);
	// FIN AGREGADO
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_report=new sigesp_soc_class_reportbsf();
	}
		
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_numordcom=$io_fun_soc->uf_obtenervalor_get("numordcom","");
	$ls_estcondat=$io_fun_soc->uf_obtenervalor_get("tipord","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$arrResultado= $io_report->uf_select_orden_imprimir($ls_numordcom,$ls_estcondat,$lb_valido); // Cargar los datos del reporte
	$rs_data= $arrResultado['rs_data'];
	$lb_valido = $arrResultado['lb_valido'];
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		$ls_descripcion="Generó el Reporte de Orden de Compra";
		$lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_p_registro_orden_compra.php",$ls_descripcion);
		if($lb_valido)	
		{
			
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(3.5,2,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(570,47,8,'','',1); // Insertar el número de página
			if ($row=$io_sql->fetch_row($rs_data))
			{
				$ls_numordcom=$row["numordcom"];
				$ls_estcondat=$row["estcondat"];
				$ls_coduniadm=$row["coduniadm"];
				$ls_denuniadm=trim($row["denuniadm"]);
				$ls_codfuefin=$row["codfuefin"];
				$ls_denfuefin=trim($row["denfuefin"]);
				$ls_diaplacom=$row["diaplacom"];
				$ls_forpagcom=$row["forpagcom"];
				$ls_codpro=$row["cod_pro"];
				$ls_nompro=trim($row["nompro"]);
				$ls_rifpro=$row["rifpro"];
				// AGREGADO POR OFIMATICA DE VENEZUELA EL 02/07/2015
				$ls_nrofiscalexterior=trim($row["nrofiscalexterior"]);	
				// FIN AGREGADO			
				$ls_dirpro=trim($row["dirpro"]);
				$ld_fecordcom=$row["fecordcom"];
				$ls_obscom=trim($row["obscom"]);
				$ld_monsubtot=$row["monsubtot"];
				// AGREGADO POR OFIMATICA DE VENEZUELA EL 29/05/2012
				$li_monsubtot=$row["monsubtot"];
				// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
				$ld_monimp=$row["monimp"];
				$ld_montot=$row["montot"];
				$ld_perentdesde=$row["fechentdesde"];
				$ld_perenthasta=$row["fechenthasta"];
				// AGREGADO POR OFIMATICA DE VENEZUELA EL 02-03-2012
				$ls_observacion=trim($row["obsordcom"]);
				// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
				$ld_perentdesde=$io_funciones->uf_convertirfecmostrar($ld_perentdesde);
				$ld_perenthasta=$io_funciones->uf_convertirfecmostrar($ld_perenthasta);
				// AGREGADO POR OFIMATICA DE VENEZUELA EL 08/01/2015
				$ls_formaentrega=$row["formaentrega"];
				// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA				
				if($ls_tiporeporte==0)
				{
					$ld_montotaux=$row["montotaux"];
					$ld_montotaux=number_format($ld_montotaux,2,",",".");
				}
				$numalet->setNumero($ld_montot);
				$ls_monto= $numalet->letra();
				$ld_montot=number_format($ld_montot,2,",",".");
				$ld_monsubtot=number_format($ld_monsubtot,2,",",".");
				$ld_monimp=number_format($ld_monimp,2,",",".");
				$ld_fecordcom=$io_funciones->uf_convertirfecmostrar($ld_fecordcom);
		 		uf_print_encabezado_pagina($ls_estcondat,$ls_numordcom,$ld_fecordcom,$io_pdf);
				// MODIFICADO POR OFIMATICA DE VENEZUELA EL 02/07/2015
		   	    uf_print_cabecera($ls_coduniadm,$ls_denuniadm,$ls_codfuefin,$ls_denfuefin,$ls_codpro,$ls_nompro,$ls_obscom,$ls_rifpro,
						  	      $ls_diaplacom,$ls_dirpro,$ls_forpagcom,$ld_perentdesde,$ld_perenthasta,$ls_formaentrega,$ls_nrofiscalexterior,$io_pdf);
				 // FIN MODIFICADO
				/////DETALLE  DE  LA ORDEN DE COMPRA
			   $arrResultado = $io_report->uf_select_detalle_orden_imprimir($ls_numordcom,$ls_estcondat,$lb_valido);
			   $rs_datos = $arrResultado['rs_data'];
			   $lb_valido = $arrResultado['lb_valido'];
			   if ($lb_valido)
			   {
		     	 $li_totrows = $io_sql->num_rows($rs_datos);
				 if ($li_totrows>0)
				 {
				    $li_i = 0;
				    while($row=$io_sql->fetch_row($rs_datos))
					{
						$li_i=$li_i+1;
						$ls_denartser=$row["denartser"];
						// MODIFICADO POR OFIMATICA DE VENEZUELA EL 04/12/2012
						if($ls_estcondat=="B")
						{
     						$ls_codartser=substr($row["codartser"],15,5);
							$ls_unidad=$row["unidad"];
						}
						else
						{
   						    $ls_codartser=$row["codartser"];
						    $ls_unidad="";
						}
						// FIN DE LO MODIFICADO POR OFIMATICA DE VENEZUELA
						if($ls_unidad=="D")
						{
						   $ls_unidad="Detal";
						}
						elseif($ls_unidad=="M")
						{
						   $ls_unidad="Mayor";
						}
						// MODIFICADO POR OFIMATICA DE VENEZUELA EL 27-01-2012
						$ls_medida=$row["denunimed"];
						// FIN DE LO MODIFICADO POR OFIMATICA DE VENEZUELA
						$li_cantartser=$row["cantartser"];
						$ld_preartser=$row["preartser"];
						$ld_subtotartser=$ld_preartser*$li_cantartser;
						$ld_totartser=$row["monttotartser"];
						$ld_carartser=$ld_totartser-$ld_subtotartser;
							
						$ld_preartser=number_format($ld_preartser,2,",",".");
						$ld_subtotartser=number_format($ld_subtotartser,2,",",".");
						$ld_totartser=number_format($ld_totartser,2,",",".");
						$ld_carartser=number_format($ld_carartser,2,",",".");
						// MODIFICADO POR OFIMATICA DE VENEZUELA EL 27-01-2012
						$la_data[$li_i]=array('codigo'=>$ls_codartser,'denominacion'=>$ls_denartser,'cantidad'=>$li_cantartser,
											  'medida'=>$ls_medida,'cosuni'=>$ld_preartser,'baseimp'=>$ld_subtotartser,
											  'cargo'=>$ld_carartser,'montot'=>$ld_totartser);
						// FIN DE LO MODIFICADO POR OFIMATICA DE VENEZUELA
					}
					uf_print_detalle($la_data,$ls_estcondat,$io_pdf);
					unset($la_data);
				    /////DETALLE  DE  LAS  CUENTAS DE GASTOS DE LA ORDEN DE COMPRA
					$arrResultado=$io_report->uf_select_cuenta_gasto($ls_numordcom,$ls_estcondat,$lb_valido); 
					$rs_datos_cuenta = $arrResultado['rs_data'];
					$lb_valido = $arrResultado['lb_valido'];
					if($lb_valido)
					{
						 $li_totrows = $io_sql->num_rows($rs_datos_cuenta);
						 if ($li_totrows>0)
						 {
							$li_s = 0;
							while($row=$io_sql->fetch_row($rs_datos_cuenta))
							{
								$li_s=$li_s+1;
								$ls_codestpro1=trim($row["codestpro1"]);
								$ls_codestpro2=trim($row["codestpro2"]);
								$ls_codestpro3=trim($row["codestpro3"]);
								$ls_codestpro4=trim($row["codestpro4"]);
								$ls_codestpro5=trim($row["codestpro5"]);
								// AGREGADO POR OFIMATICA DE VENEZUELA EL 21/11/2012
								$ls_codestpro1 = substr($ls_codestpro1,-$_SESSION["la_empresa"]["loncodestpro1"]);
								$ls_codestpro2 = substr($ls_codestpro2,-$_SESSION["la_empresa"]["loncodestpro2"]);
								$ls_codestpro3 = substr($ls_codestpro3,-$_SESSION["la_empresa"]["loncodestpro3"]);
								$ls_codestpro4 = substr($ls_codestpro4,-$_SESSION["la_empresa"]["loncodestpro4"]);
								$ls_codestpro5 = substr($ls_codestpro5,-$_SESSION["la_empresa"]["loncodestpro5"]);	
								// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA 							
								$ls_spg_cuenta=$row["spg_cuenta"];
								$ld_monto=$row["monto"];
								$ld_monto=number_format($ld_monto,2,",",".");
								$ls_dencuenta="";
								$lb_valido = $io_report->uf_select_denominacionspg($ls_spg_cuenta,$ls_dencuenta);																																						
								if($ls_estmodest==1)
								{
									$ls_codestpro=$ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3;
								}
								else
								{
									$ls_codestpro=substr($ls_codestpro1,-2)."-".substr($ls_codestpro2,-2)."-".substr($ls_codestpro3,-2)."-".substr($ls_codestpro4,-2)."-".substr($ls_codestpro5,-2);
								}
								$la_data[$li_s]=array('codestpro'=>$ls_codestpro,'denominacion'=>$ls_dencuenta,
													  'cuenta'=>$ls_spg_cuenta,'monto'=>$ld_monto);
							}	
							uf_print_detalle_cuentas($la_data,$io_pdf);
							unset($la_data);
						}
				     }
			      }
		       }
	     	}
		}
           //
		   // AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA EL 08/10/2018, POR AJUSTE SOLICITADO POR EL INAC EL 04/10/2018
           if($ls_nrofiscalexterior=="")
		       $ls_mensaje='De acuerdo con lo establecido en el Artículo 31 del Decreto con Rango, Valor y Fuerza de Ley de Contrataciones Públicas, de fecha 13/11/2014, publicado en la Gaceta Oficial de la Republica Bolivariana de Venezuela, N° 6.154 Extraordinario de fecha 19/11/2014, esta empresa está obligada a cumplir con el pago del  tres por ciento (3%) sobre el monto total de la contratación.';
		   else
		       $ls_mensaje=''; 
		   // FIN AJUSTE
		   //
		uf_print_piecabecera($ld_monsubtot,$ld_monimp,$ld_montot,$ls_monto,$ls_observacion,$ls_mensaje,$io_pdf);
		// FIN DE LO MODIFICADO Y AGREGADO POR OFIMATICA DE VENEZUELA
	} 	  	 
	if($lb_valido) // Si no ocurrio ningún error
	{
		uf_print_firmas($io_pdf);
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
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_soc);
?>
