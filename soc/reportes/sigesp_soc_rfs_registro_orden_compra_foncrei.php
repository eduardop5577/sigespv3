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
//  ORGANISMO: FONCREI
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
	
	function uf_print_encabezado_pagina($as_estcondat,$as_numordcom,$ad_fecordcom,$as_nompro,$as_rifpro,$as_nitpro,$as_obscom,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_estcondat  ---> tipo de la orden de compra
		//	    		   as_numordcom ---> numero de la orden de compra
		//	    		   ad_fecordcom ---> fecha de registro de la orden de compra
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 21/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetY(690);
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		if (($as_estcondat=="B") || ($as_estcondat=="-") || ($as_estcondat=="")) 
           {
             $ls_titulo	   = "ORDEN DE COMPRA";
			 $ls_operacion = "ADQUISICIONES";
		     $la_data	   = array(array('name'=>'<b>Proveedor: </b>'.$as_nompro),
 		               			   array('name'=>'<b>R.I.F.: </b>'.$as_rifpro.'<b>     -     N.I.T.:</b> '.$as_nitpro),
					   			   array('name'=>'<b>Concepto: </b> '.$as_obscom));				
           }
        else
           {
             $ls_titulo	   = "ORDEN DE SERVICIO";
			 $ls_operacion = "SERVICIOS";
			 $la_data	   = array(array('name'=>'<b>Proveedor: </b>'.$as_nompro),
					   		       array('name'=>'<b>Observaci?n: </b> '.$as_obscom));	
           }
		$li_tm = $io_pdf->getTextWidth(12,$ls_titulo);
		$tm    = 230;
		
		$la_columna = array('name'=>'');		
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
						    'fontSize' =>9, // Tama?o de Letras
						    'showLines'=>1, // Mostrar L?neas
						    'shaded'=>0, // Sombra entre l?neas
						    'xOrientation'=>'center', // Orientaci?n de la tabla
						    'width'=>570, // Ancho de la tabla						 						 					 
						    'maxWidth'=>570); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addText(165,715,14,"<b>".$ls_titulo."</b>"); // Agregar el t?tulo
		$io_pdf->addText(360,725,10," <b>No. </b>".$as_numordcom); // Agregar el t?tulo
		$io_pdf->addText(355,705,10,"<b>Fecha </b>".$ad_fecordcom); // Agregar el t?tulo
		// cuadro inferior
		$io_pdf->Rectangle(15,75,570,70); 
		$io_pdf->line(15,132,585,132);	//HORIZONTAL
		$io_pdf->line(15,117,585,117);	//HORIZONTAL	
		$io_pdf->addText(40,122,7,"ELABORADO POR"); // Agregar el t?tulo
		$io_pdf->addText(40,136,7,"OBSERVACIONES:"); // Agregar el t?tulo
		$io_pdf->addText(50,63,7,"COMPRAS"); // Agregar el t?tulo
		$io_pdf->line(130,60,130,132);	//VERTICAL	
		$io_pdf->addText(167,122,7,"APROBADO POR"); // Agregar el t?tulo
		$io_pdf->addText(160,63,7,"ADMINISTRACION"); // Agregar el t?tulo
		$io_pdf->line(260,60,260,132);	//VERTICAL		
		$io_pdf->addText(295,122,7,"AUTORIZADO POR"); // Agregar el t?tulo
		$io_pdf->addText(300,63,7,"PRESIDENCIA"); // Agregar el t?tulo
		$io_pdf->line(410,60,410,132);	//VERTICAL
		$io_pdf->line(15,60,15,132);	//VERTICAL
		$io_pdf->line(585,60,585,132);	//VERTICAL	
		$io_pdf->addText(470,122,7,"PROVEEDOR"); // Agregar el t?tulo
		$io_pdf->addText(435,63,7,"FIRMA AUTOGRAFA,SELLO,FECHA"); // Agregar el t?tulo
		$io_pdf->line(15,60,585,60); //HORIZONTAL		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_condicion,$as_lugcom,$as_fecentordcom,$as_formapago,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: $as_condicion : Condici?n de la entrega.
		//                 $as_lugcom    : Lugar donde se realiza la Compra.
		//                 $ad_fecent    : Fecha de Entrega de la Compra.
		//                 $as_formapago : Forma de cancelaci?n de la Compra.
		//	    		   $io_pdf       : Objeto que instancia de la clase PDF.
		//    Description: Funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing. Yesenia Moreno                            Modificado Por: Ing. N?stor Falc?n.
		// Fecha Creaci?n: 17/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		//------------------------------------------------  Datos de la Entrega  ----------------------------------------------------					
		global $io_pdf;
		
		
		//------------------------------------------------  Datos del Pago  ----------------------------------------------------------		
		$la_data[1]=array('condicion'=>'<b>Condiciones de Entrega</b>','forma'=>'<b>Condiciones de Pago</b>');
		$la_data[2]=array('condicion'=>"<b>Condicion:</b> ".$as_condicion." <b>       Lugar: </b>".$as_lugcom."          <b>Fecha: ".$as_fecentordcom."</b>" ,'forma'=>$as_formapago);				
		$la_columna=array('condicion'=>'','forma'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>570, // Ancho de la tabla						 									 
						 'maxWidth'=>570,
                         'cols'=>array('condicion'=>array('justification'=>'left','width'=>331), // Justificaci?n y ancho de la columna
									   'forma'=>array('justification'=>'center','width'=>239))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);					
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($as_tipordcom,$la_data,$ld_subtot,$ld_totcar,$ld_montot,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data ---> arreglo de informaci?n
		//	    		   io_pdf ---> Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle 
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 21/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		global $io_pdf;
		
		if ($as_tipordcom=='B')
		   {
		     $ls_titulo_grid="Bienes";
		   }
		elseif($as_tipordcom=='S')
	 	   {
		     $ls_titulo_grid="Servicios";
		   }
		
		$io_pdf->ezSetDy(-10);
		$la_datatitulo[1]=array('columna1'=>'<b> Detalle de los '.$ls_titulo_grid.'</b>');
		$la_columnas=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'center','width'=>570))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_datatitulo,$la_columnas,'',$la_config);
		unset($la_datatitulo);
		unset($la_columnas);
		unset($la_config);
		
		$io_pdf->ezSetDy(-2);
		if ($as_tipordcom=='B')
		   {
		     $la_columnas=array('codigo'=>'<b>C?digo</b>',
						        'denominacion'=>'<b>Denominaci?n</b>',
						        'cantidad'=>'<b>Cant</b>',
						    	'precio'=>'<b>Precio ('.$ls_bolivares.')</b>',
						   		'unidad'=>'<b>Unid</b>',
						   		'subtotal'=>'<b>Sub-Total ('.$ls_bolivares.')</b>',
						   		'cargos'=>'<b>Cargos ('.$ls_bolivares.')</b>',
						   		'total'=>'<b>Total ('.$ls_bolivares.')</b>');
		
			 $la_config=array('showHeadings'=>1, // Mostrar encabezados
						 	  'fontSize'=>8, // Tama?o de Letras
						 	  'titleFontSize'=>8,  // Tama?o de Letras de los t?tulos
						 	  'showLines'=>1, // Mostrar L?neas
						 	  'shaded'=>0, // Sombra entre l?neas
						 	  'width'=>570, // Ancho de la tabla
						 	  'maxWidth'=>570, // Ancho M?ximo de la tabla
						 	  'xOrientation'=>'center', // Orientaci?n de la tabla
						      'cols'=>array('codigo'=>array('justification'=>'center','width'=>99), // Justificaci?n y ancho de la columna
						 			   		'denominacion'=>array('justification'=>'left','width'=>138), // Justificaci?n y ancho de la columna
						 			   		'cantidad'=>array('justification'=>'right','width'=>40), // Justificaci?n y ancho de la columna
						 			   		'precio'=>array('justification'=>'right','width'=>65), // Justificaci?n y ancho de la columna
						 			   		'unidad'=>array('justification'=>'center','width'=>33), // Justificaci?n y ancho de la columna
						 			   		'subtotal'=>array('justification'=>'right','width'=>65), // Justificaci?n y ancho de la columna
						 			   		'cargos'=>array('justification'=>'right','width'=>65), // Justificaci?n y ancho de la columna
						 			   		'total'=>array('justification'=>'right','width'=>65))); // Justificaci?n y ancho de la columna
		   }
		elseif($as_tipordcom=='S')
		   {
		     $la_columnas=array('codigo'=>'<b>C?digo</b>',
						        'denominacion'=>'<b>Denominaci?n</b>',
						        'cantidad'=>'<b>Cant</b>',
						    	'precio'=>'<b>Precio ('.$ls_bolivares.')</b>',
						   		'subtotal'=>'<b>Sub-Total ('.$ls_bolivares.')</b>',
						   		'cargos'=>'<b>Cargos ('.$ls_bolivares.')</b>',
						   		'total'=>'<b>Total ('.$ls_bolivares.')</b>');
		
			 $la_config=array('showHeadings'=>1, // Mostrar encabezados
						 	  'fontSize'=>8, // Tama?o de Letras
						 	  'titleFontSize'=>8,  // Tama?o de Letras de los t?tulos
						 	  'showLines'=>1, // Mostrar L?neas
						 	  'shaded'=>0, // Sombra entre l?neas
						 	  'width'=>570, // Ancho de la tabla
						 	  'maxWidth'=>570, // Ancho M?ximo de la tabla
						 	  'xOrientation'=>'center', // Orientaci?n de la tabla
						      'cols'=>array('codigo'=>array('justification'=>'center','width'=>99), // Justificaci?n y ancho de la columna
						 			   		'denominacion'=>array('justification'=>'left','width'=>171), // Justificaci?n y ancho de la columna
						 			   		'cantidad'=>array('justification'=>'right','width'=>40), // Justificaci?n y ancho de la columna
						 			   		'precio'=>array('justification'=>'right','width'=>65), // Justificaci?n y ancho de la columna
						 			   		'subtotal'=>array('justification'=>'right','width'=>65), // Justificaci?n y ancho de la columna
						 			   		'cargos'=>array('justification'=>'right','width'=>65), // Justificaci?n y ancho de la columna
						 			   		'total'=>array('justification'=>'right','width'=>65))); // Justificaci?n y ancho de la columna
		   
		   }
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	    unset($la_data);
		unset($la_columnas);
		unset($la_config);		

	    $la_data[1]  = array('titulo'=>'<b>Sub Total '.$ls_bolivares.'</b>','contenido'=>$ld_subtot);
		$la_columnas = array('titulo'=>'','contenido'=>'');
		$la_config   = array('showHeadings'=>0, // Mostrar encabezados
						     'fontSize' => 9, // Tama?o de Letras
						 	 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 	 'showLines'=>0, // Mostrar L?neas
						 	 'shaded'=>0, // Sombra entre l?neas
						 	 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 	 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 	 'width'=>540, // Ancho de la tabla
						 	 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 	 'xOrientation'=>'center', // Orientaci?n de la tabla
						 	 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci?n y ancho de la columna
						 			       'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		$la_data[1]=array('titulo'=>'<b>Cargos '.$ls_bolivares.'</b>','contenido'=>$ld_totcar);
		$la_columnas=array('titulo'=>'','contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		$la_data[1]  = array('titulo'=>'<b>Total '.$ls_bolivares.'</b>','contenido'=>$ld_montot);
		$la_columnas = array('titulo'=>'','contenido'=>'');
		$la_config   = array('showHeadings'=>0, // Mostrar encabezados
						     'fontSize' => 9, // Tama?o de Letras
						 	 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 	 'showLines'=>0, // Mostrar L?neas
						 	 'shaded'=>0, // Sombra entre l?neas
						 	 'width'=>540, // Ancho de la tabla
						 	 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 	 'xOrientation'=>'center', // Orientaci?n de la tabla
						 	 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci?n y ancho de la columna
						 			       'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_cuentas($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_cuentas
		//		   Access: private 
		//	    Arguments: la_data ---> arreglo de informaci?n
		//	    		   io_pdf ---> Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 21/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_estmodest, $ls_bolivares;
		global $io_pdf;
		
		$io_pdf->ezSetDy(-5);
		if($ls_estmodest==1)
		{
			$ls_titulo="Estructura Presupuestaria";
		}
		else
		{
			$ls_titulo="Estructura Program?tica";
		}
		
		$io_pdf->ezSetDy(-2);
		$la_data2 = array(array('codestpro'=>"<b>Estructura Presupuestaria</b>",
		                        'cuenta'=>"<b>Cuenta Presupuestaria</b>",
							    'denominacion'=>"<b>Denominaci?n</b>",
							    'monto'=>'<b>Monto ('.$ls_bolivares.'</b>)'));
        $la_columna = array('codestpro'=>'','cuenta'=>'','denominacion'=>'','monto'=>'');
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>1, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Sombra entre l?neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('codestpro'=>array('justification'=>'center','width'=>170), // Justificaci?n y ancho de la columna
									   'cuenta'=>array('justification'=>'center','width'=>100),
									   'denominacion'=>array('justification'=>'center','width'=>200),
									   'monto'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data2,$la_columna,'',$la_config);
		unset($la_data2);
		unset($la_columna);
		unset($la_config);
		
		$la_columnas=array('codestpro'=>'','cuenta'=>'','denominacion'=>'','monto'=>'');
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('codestpro'=>array('justification'=>'center','width'=>170), // Justificaci?n y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>200), // Justificaci?n y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($ld_subtot,$ld_totcar,$ld_montot,$ls_monlet,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		    Acess: private 
		//	    Arguments: li_subtot ---> Subtotal del articulo
		//	    		   li_totcar -->  Total cargos
		//	    		   li_montot  --> Monto total
		//	    		   ls_monlet   //Monto en letras
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: funci?n que imprime los totales
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 21/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		global $io_pdf;
		
		$io_pdf->ezSetDy(-5);
		$la_data[1]  = array('monlet'=>'<b>MONTO TOTAL EN LETRAS ('.$ls_bolivares.')</b>',
		                     'monnum'=>'<b>MONTO TOTAL ('.$ls_bolivares.')<b>');
		$la_data[2]  = array('monlet'=>$ls_monlet,'monnum'=>$ld_montot);
		$la_columnas = array('monlet'=>'','monnum'=>'');
		$la_config   = array('showHeadings'=>0, // Mostrar encabezados
						     'fontSize'=> 7, // Tama?o de Letras
						     'showLines'=>0, // Mostrar L?neas
						     'colGap'=>1, // Sombra entre l?neas
							 'shaded'=>0, // Sombra entre l?neas
						     'width'=>570, // Ancho de la tabla
						     'maxWidth'=>570, // Ancho M?ximo de la tabla
						     'xOrientation'=>'center', // Orientaci?n de la tabla
						     'cols'=>array('monlet'=>array('justification'=>'left','width'=>400),
						 			       'monnum'=>array('justification'=>'right','width'=>170))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabeceramonto_bsf($li_montotaux,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		    Acess: private 
		//	    Arguments: li_montotaux ---> Total de la Orden Bs.F.
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: Funci?n que imprime el total de la Orden de Compra en Bolivares Fuertes.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 25/09/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data[1]=array('titulo'=>'<b>Monto Bs.F.</b>','contenido'=>$li_montotaux,);
		$la_columnas=array('titulo'=>'','contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_sep($la_datasep,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_sep
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Selena Lucena
		// Fecha Creaci?n: 17/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
		global $io_pdf;
		
		$io_pdf->ezSetDy(-5);

		$la_columna=array('codigo'=>'N? Ejecucion Presupuestaria','denuniadm'=>'Gerencia /Oficina Solicitante');
		$la_config =array('showHeadings'=>1, // Mostrar encabezados
						  'fontSize' => 8, // Tama?o de Letras
						  'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						  'showLines'=>1, // Mostrar L?neas
						  'shaded'=>0, // Sombra entre l?neas
						  'xOrientation'=>'center', // Orientaci?n de la tabla
						  'width'=>570, // Ancho de la tabla						 										 
						  'maxWidth'=>570,
						  'cols'=>array('codigo'=>array('justification'=>'center','width'=>150),
						  				'denuniadm'=>array('justification'=>'left','width'=>420))
						); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_datasep,$la_columna,'',$la_config);
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("sigesp_soc_class_report.php");
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
	require_once("../class_folder/class_funciones_soc.php");
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	
	$in           = new sigesp_include();
	$con          = $in->uf_conectar();
	$io_sql       = new class_sql($con);	
	$io_funciones = new class_funciones();	
	$io_fun_soc   = new class_funciones_soc();
	$io_report    = new sigesp_soc_class_report($con);
	$ls_estmodest = $_SESSION["la_empresa"]["estmodest"];
	$ls_codemp    = $_SESSION["la_empresa"]["codemp"];

	//Instancio a la clase de conversi?n de numeros a letras.
	require_once("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
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
	$ls_bolivares="Bs.";
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_report=new sigesp_soc_class_reportbsf();
		$ls_bolivares="Bs.F.";
		$numalet->setMoneda("Bolivares Fuerte");
	}
		
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	$ls_numordcom = $io_fun_soc->uf_obtenervalor_get("numordcom","");
	$ls_estcondat = $io_fun_soc->uf_obtenervalor_get("tipord","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=true;
	$arrResultado= $io_report->uf_select_orden_imprimir($ls_numordcom,$ls_estcondat,$lb_valido); // Cargar los datos del reporte
	$rs_data= $arrResultado['rs_data'];
	$lb_valido = $arrResultado['lb_valido'];
	if($lb_valido==false) // Existe alg?n error ? no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		$ls_descripcion="Gener? el Reporte de Orden de Compra";
		$lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_p_registro_orden_compra.php",$ls_descripcion);
		if($lb_valido)	
		{
			
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(5.6,6,3,3); // Configuraci?n de los margenes en cent?metros
			$io_pdf->ezStartPageNumbers(588,710,7,'','',1); 
			if ($row=$io_sql->fetch_row($rs_data))
			{
				$ls_numordcom = $row["numordcom"];
				$ls_estcondat = $row["estcondat"];
				$ls_coduniadm = $row["coduniadm"];
				$ls_denuniadm = $row["denuniadm"];
				$ls_codfuefin = $row["codfuefin"];
				$ls_denfuefin = $row["denfuefin"];
				$ls_diaplacom = $row["diaplacom"];
				$ls_forpagcom = $row["forpagcom"];
				$ls_codpro	  = $row["cod_pro"];
				$ls_nompro	  = $row["nompro"];
				$ls_rifpro	  = $row["rifpro"];
				$ls_nitpro	  = $row["nitpro"];
				$ls_dirpro	  = $row["dirpro"];
				$ls_obscom	  = $row["obscom"];
				$ld_monsubtot = $row["monsubtot"];
				$ld_monimp    = $row["monimp"];
				$ld_montot	  = $row["montot"];
				$ls_condicion = $row["concom"];	
				$ls_lugcom    = $row["estlugcom"];
				$ls_codmoneda = $row["codmon"];
				$ls_pais      = $row["codpai"];
				$ls_estado    = $row["codest"];
				$ls_municipio = $row["codmun"];
				$ls_parroquia = $row["codpar"];
				if ($ls_codfuefin!="--")
				   {
				     $ls_denfuefin = $io_report->uf_select_denominacion('sigesp_fuentefinanciamiento','codfuefin',"WHERE codfuefin='".$ls_codfuefin."'");   
				   }  
				else
				   {
				     $ls_denfuefin="";
				     $ls_codfuefin="";
				   }
				if ($ls_codmoneda!="---")
				   {
				     $ls_moneda = $io_report->uf_select_denmoneda($ls_codmoneda);  
				   }  
				else
				   { 
				     $ls_moneda    = "";
				     $ls_codmoneda = "";
				   }
				if ($ls_lugcom==0)
				   {
				     $ls_lugcom="Nacional";				
				   }
				else
				   {
					 $ls_lugcom="Extranjero";				
				   }
				if (($ls_pais=="---") || ($ls_pais=="s1") || ($ls_pais=="---seleccione---"))
				   {
				     $ls_pais="";
				   } 
				if (($ls_estado=="---") || ($ls_estado=="s1") || ($ls_estado=="---seleccione---"))
                   {
				     $ls_estado="";
				   }				
				if (($ls_municipio=="---") || ($ls_municipio=="s1") || ($ls_municipio=="---seleccione---"))
                   {
				     $ls_municipio="";
			 	   }				
				if (($ls_parroquia=="---") || ($ls_parroquia=="s1") || ($ls_parroquia=="---seleccione---")) 
				   {
				     $ls_parroquia="";
				   }
				if ($ls_tiporeporte==0)
				   {
				     $ld_montotaux = $row["montotaux"];
					 $ld_montotaux = number_format($ld_montotaux,2,",",".");
				   }
				$numalet->setNumero($ld_montot);
				$ls_monto	  = $numalet->letra();
				$ld_montot	  = number_format($ld_montot,2,",",".");
				$ld_monsubtot = number_format($ld_monsubtot,2,",",".");
				$ld_monimp	  =	number_format($ld_monimp,2,",",".");
				$ld_fecordcom    = $io_funciones->uf_convertirfecmostrar($row["fecordcom"]);
		        $ls_fecentordcom = $io_funciones->uf_convertirfecmostrar($row["fechentdesde"]);
				uf_print_encabezado_pagina($ls_estcondat,$ls_numordcom,$ld_fecordcom,$ls_nompro,$ls_rifpro,$ls_nitpro,$ls_obscom,$io_pdf);
			    uf_print_cabecera($ls_condicion,$ls_lugcom,$ls_fecentordcom,$ls_forpagcom,$io_pdf);
			    //-----------------------------SEP-----------------------------------				
				$lb_validosep = true;
				$li_totrow    = 0;
				$lb_validosep = $io_report->uf_select_soc_sep($ls_codemp,$ls_numordcom,$ls_estcondat);	
				if ($lb_validosep)
				   {										
				     $li_totrow = $io_report->ds_soc_sep->getRowCount("numordcom");							
					 for ($li_row=1;$li_row<=$li_totrow;$li_row++)
						 {
						   $ls_numsep   		= $io_report->ds_soc_sep->data["numsol"][$li_row];
						   $ls_denunadm 		= $io_report->ds_soc_sep->data["denuniadm"][$li_row];  											  
						   $la_datasep[$li_row] = array('codigo'=>$ls_numsep,'denuniadm'=>$ls_denunadm);
						 }														
					 uf_print_detalle_sep($la_datasep,$io_pdf); 
				   }
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
						$ls_codartser=$row["codartser"];
						$ls_denartser=$row["denartser"];
						if($ls_estcondat=="B")
						{
							$ls_unidad=$row["unidad"];
						}
						else
						{
							$ls_unidad="";
						}
						if($ls_unidad=="D")
						{
						   $ls_unidad="Detal";
						}
						elseif($ls_unidad=="M")
						{
						   $ls_unidad="Mayor";
						}
						$li_cantartser	 = $row["cantartser"];
						$ld_preartser	 = $row["preartser"];
						$ld_subtotartser = $ld_preartser*$li_cantartser;
						$ld_totartser	 = $row["monttotartser"];
						$ld_carartser	 = ($ld_totartser-$ld_subtotartser);
						$ld_preartser	 = number_format($ld_preartser,2,",",".");
						$ld_subtotartser = number_format($ld_subtotartser,2,",",".");
						$ld_totartser	 = number_format($ld_totartser,2,",",".");
						$ld_carartser	 = number_format($ld_carartser,2,",",".");
						
						$la_data[$li_i] = array('codigo'=>$ls_codartser,
						                        'denominacion'=>$ls_denartser,
											    'cantidad'=>$li_cantartser,
											    'precio'=>$ld_preartser,
											    'unidad'=>$ls_unidad,
											    'subtotal'=>$ld_subtotartser,
											    'cargos'=>$ld_carartser,
											    'total'=>$ld_totartser);
					}
					uf_print_detalle($ls_estcondat,$la_data,$ld_monsubtot,$ld_monimp,$ld_montot,$io_pdf);
					unset($la_data);
				    /////DETALLE  DE  LAS  CUENTAS DE GASTOS DE LA ORDEN DE COMPRA
					$arrResultado=$io_report->uf_select_cuenta_gasto($ls_numordcom,$ls_estcondat,$lb_valido); 
					$rs_datos_cuenta = $arrResultado['rs_data'];
					$lb_valido = $arrResultado['lb_valido'];
					if($lb_valido)
					{
						 $li_totrows = $io_sql->num_rows($rs_datos);
						 if ($li_totrows>0)
						 {
							$li_s = 0;
							while($row=$io_sql->fetch_row($rs_datos_cuenta))
							{
								$li_s++;
								$ls_codestpro1 = trim($row["codestpro1"]);
								$ls_codestpro2 = trim($row["codestpro2"]);
								$ls_codestpro3 = trim($row["codestpro3"]);
								$ls_codestpro4 = trim($row["codestpro4"]);
								$ls_codestpro5 = trim($row["codestpro5"]);
								$ls_spg_cuenta = $row["spg_cuenta"];
								$ld_monto      = $row["monto"];
								$ld_monto      = number_format($ld_monto,2,",",".");
								$ls_dencuenta  = "";
								$arrResultado = $io_report->uf_select_denominacionspg($ls_spg_cuenta,$ls_dencuenta);																																						
								$ls_dencuenta = $arrResultado['as_denominacion'];
								$lb_valido = $arrResultado['lb_valido'];
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
		uf_print_piecabecera($ld_monsubtot,$ld_monimp,$ld_montot,$ls_monto,$io_pdf);
		if ($ls_tiporeporte==0)
		   {
			 uf_print_piecabeceramonto_bsf($ld_montotaux,$io_pdf);
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
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_soc);
?>