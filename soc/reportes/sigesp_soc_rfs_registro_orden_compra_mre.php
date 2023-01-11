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
	function uf_print_encabezado_pagina($as_estcondat,$as_numordcom,$ad_fecordcom,$as_coduniadm,$as_denuniadm, $as_codfuefin,
	                                   $as_denfuefin,$as_codigo,$as_nombre,$as_conordcom,$as_rifpro,$as_diaplacom,$as_dirpro,
									   $ls_forpagcom,$ld_perentdesde,$ld_perenthasta,$ls_valorunitrib,$ld_montot,$as_estcom,
									   $as_nomusu,$as_telusu,$as_corele,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_estcondat  ---> tipo de la orden de compra
		//	    		   as_numordcom ---> numero de la orden de compra
		//	    		   ad_fecordcom ---> fecha de registro de la orden de compra
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_encabezado;
		global $io_pdf;
		
		$io_encabezado = $io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(15,40,585,40);
		$io_pdf->line(460,700,460,760);
		$io_pdf->line(460,730,585,730);
        $io_pdf->Rectangle(15,700,570,60);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],30,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		if ($as_estcom=='3')
		{
			$io_pdf->addText(480,765,9,"<b>ANULADO</b>"); // Agregar la Fecha
		}
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
		$io_pdf->addText($tm,715,14,$ls_titulo); // Agregar el t�tulo
		$io_pdf->addText(465,740,9," <b>No. </b>".$as_numordcom); // Agregar el t�tulo
		$io_pdf->addText(465,710,9,"<b>Fecha </b>".$ad_fecordcom); // Agregar el t�tulo
		$io_pdf->addText(540,770,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(546,764,6,date("h:i a")); // Agregar la Hora
		//***********************************************************************************************************//
		//													     //
		//   Modificado: Ing Norma Parada									     //
		//   Descripcion: Se agrega el siguiente codigo para saber si es una orden de servicio o una orden de compra //
		//		  y definir el pie de pagina								     //
		//   Fecha: 08-11-2012											     //
		//													     //		
		//***********************************************************************************************************//
		if($as_estcondat=="B") 
	        {
        	     $jefe="Coordinador(a)";	
	      	     $area="�rea de Compras";
	        }
        	else
        	{
                     $jefe="Director(a)";	
	      	     $area="Ing. y Servicios Grales";
	        }
	// cuadro inferior//

		if ($ld_montot>$ls_valorunitrib)
		{
                  if($as_estcondat=="B"){
		  	$coordinador_area_compras="LIC. JENNY MADRIZ";
			$coordinador_area_compras2="";
		  }
		  else{
			$coordinador_area_compras="    ING. TRUDIMAR";
			$coordinador_area_compras2="      SULBARAN";
		  }
		  $director_de_administracion="LIC. NELSON LEPAJE";
		  $director_osa="ING. ILICH LAREZ";
		  $secretario="LIC. ERIC MALPICA"; 	
			
			
		  $io_pdf->Rectangle(15,60,570,100);
		  $io_pdf->addText(270,152,7,"FIRMAS AUTORIZADAS"); // Agregar el t�tulo
		  $io_pdf->line(15,148,585,148);	//HORIZONTAL
		  $io_pdf->line(15,130,585,130);	//HORIZONTAL
		  $io_pdf->addText(30,138,8,"<b>Elaborado Por</b>"); // Agregar el t�tulo
	      $io_pdf->line(15,105,585,105);	//HORIZONTAL
		  $io_pdf->addText(20,122,7,"<b>Nombre y Apellido</b>"); // Agregar el t�tulo
		  $io_pdf->addText(20,96,7,"<b>Firma</b>"); // Agregar el t�tulo
		  $io_pdf->line(15,80,585,80);	//HORIZONTAL
		  $io_pdf->addText(20,72,7,"<b>Fecha</b>"); // Agregar el t�tulo
		  
		  $io_pdf->line(100,60,100,148);	//VERTICAL
		  $io_pdf->addText(115,140,8,"<b>$jefe</b>"); // Agregar el t�tulo
		  $io_pdf->addText(100,132,8,"<b>$area</b>"); // Agregar el t�tulo
		  $io_pdf->addText(105,122,7,"<b>$coordinador_area_compras</b>"); // Agregar el t�tulo
		  $io_pdf->addText(105,115,7,"<b>$coordinador_area_compras2</b>"); // Agregar el t�tulo
		  $io_pdf->addText(105,96,7,"<b>Firma</b>"); // Agregar el t�tulo
		  $io_pdf->addText(105,72,7,"<b>Fecha</b>"); // Agregar el t�tulo
		  
		  $io_pdf->line(185,60,185,148);	//VERTICAL
		  $io_pdf->addText(202,140,8,"<b>Director(a) de</b>"); // Agregar el t�tulo
		  $io_pdf->addText(197,132,8,"<b>Administraci�n</b>"); // Agregar el t�tulo
		  $io_pdf->addText(190,122,7,"<b>$director_de_administracion</b>"); // Agregar el t�tulo
		  $io_pdf->addText(190,96,7,"<b>Firma</b>"); // Agregar el t�tulo
		  $io_pdf->addText(190,72,7,"<b>Fecha</b>"); // Agregar el t�tulo

		  $io_pdf->line(270,60,270,148);	//VERTICAL
		  $io_pdf->addText(285,140,8,"<b>Director Gral. Oficina de</b>"); // Agregar el t�tulo
		  $io_pdf->addText(280,132,8,"<b>Servicios Administrativos</b>"); // Agregar el t�tulo
		  $io_pdf->addText(275,122,7,"<b>$director_osa</b>"); // Agregar el t�tulo
		  $io_pdf->addText(275,96,7,"<b>Firma</b>"); // Agregar el t�tulo
		  $io_pdf->addText(275,72,7,"<b>Fecha</b>"); // Agregar el t�tulo
		  
		  $io_pdf->line(385,60,385,148);	//VERTICAL
		  $io_pdf->addText(400,140,8,"<b>Secretario General</b>"); // Agregar el t�tulo
		  $io_pdf->addText(420,132,8,"<b>Ejecutivo</b>"); // Agregar el t�tulo
		  $io_pdf->addText(390,122,7,"<b>$secretario</b>"); // Agregar el t�tulo
		  $io_pdf->addText(390,96,7,"<b>Firma</b>"); // Agregar el t�tulo
		  $io_pdf->addText(390,72,7,"<b>Fecha</b>"); // Agregar el t�tulo
		  
		  $io_pdf->line(490,60,490,148);	//VERTICAL
		  $io_pdf->addText(520,138,8,"<b>Proveedor</b>"); // Agregar el t�tulo
		  $io_pdf->addText(495,122,7,"<b>Nombre y Apellido</b>"); // Agregar el t�tulo
		  $io_pdf->addText(495,96,7,"<b>Firma</b>"); // Agregar el t�tulo
		  $io_pdf->addText(495,72,7,"<b>Fecha</b>"); // Agregar el t�tulo
		}
		else
		{ 
		   $io_pdf->Rectangle(15,60,570,100);
		  $io_pdf->addText(270,152,7,"FIRMAS AUTORIZADAS"); // Agregar el t�tulo
		  $io_pdf->line(15,148,585,148);	//HORIZONTAL
		  $io_pdf->line(15,130,585,130);	//HORIZONTAL
		  $io_pdf->addText(35,138,8,"<b>Elaborado Por</b>"); // Agregar el t�tulo
	      $io_pdf->line(15,105,585,105);	//HORIZONTAL
		  $io_pdf->addText(20,122,7,"<b>Nombre y Apellido</b>"); // Agregar el t�tulo
		  $io_pdf->addText(20,96,7,"<b>Firma</b>"); // Agregar el t�tulo
		  $io_pdf->line(135,80,585,80);	//HORIZONTAL
		  $io_pdf->line(135,60,135,148);	//VERTICAL
		  $io_pdf->addText(155,138,8,"<b>Coordinador del Area</b>"); // Agregar el t�tulo
		  $io_pdf->addText(137,122,7,"<b>Nombre y Apellido</b>"); // Agregar el t�tulo
		  $io_pdf->addText(137,96,7,"<b>Firma</b>"); // Agregar el t�tulo
		  $io_pdf->addText(137,72,7,"<b>Fecha</b>"); // Agregar el t�tulo
		  $io_pdf->line(270,60,270,148);	//VERTICAL
		  $io_pdf->addText(272,122,7,"<b>Nombre y Apellido</b>"); // Agregar el t�tulo
		  $io_pdf->addText(272,96,7,"<b>Firma</b>"); // Agregar el t�tulo
		  $io_pdf->addText(272,72,7,"<b>Fecha</b>"); // Agregar el t�tulo
		  $io_pdf->addText(290,138,8,"<b>Director de Administraci�n</b>"); // Agregar el t�tulo
		  $io_pdf->line(430,60,430,148);	//VERTICAL
		  $io_pdf->addText(432,122,7,"<b>Nombre y Apellido</b>"); // Agregar el t�tulo
		  $io_pdf->addText(432,96,7,"<b>Firma</b>"); // Agregar el t�tulo
		  $io_pdf->addText(432,72,7,"<b>Fecha</b>"); // Agregar el t�tulo
		  $io_pdf->addText(485,138,8,"<b>Proveedor</b>"); // Agregar el t�tulo
		}
			
		$io_pdf->ezSetY(695);
		$la_data[1]=array('columna1'=>'<b>Proveedor</b>  '.$as_nombre.'<b>             Rif</b> '.$as_rifpro,
		                 'columna2'=>'<b>Direccion</b> '.$as_dirpro.'');
		$la_columna=array('columna1'=>'','columna2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>250), // Justificaci�n y ancho de la columna
						 			   'columna2'=>array('justification'=>'left','width'=>320))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$ls_uniadm=$as_coduniadm."  -  ".$as_denuniadm;
		$la_data[1]=array('columna1'=>'<b>Unidad Ejecutora</b>    '.$ls_uniadm,'columna2'=>'<b>Forma de Pago</b>    '.$ls_forpagcom);
		$la_columnas=array('columna1'=>'','columna2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>300), // Justificaci�n y ancho de la columna
						 			   'columna2'=>array('justification'=>'left','width'=>270))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$ls_fuefin=$as_codfuefin."  -  ".$as_denfuefin;
		$la_data[1]=array('columna1'=>'<b>Fuente Financiamiento</b>   '.$ls_fuefin,'columna2'=>'<b> Plazo de Entrega</b>    '.$as_diaplacom);
		$la_columnas=array('columna1'=>'','columna2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>300), // Justificaci�n y ancho de la columna
						 			   'columna2'=>array('justification'=>'left','width'=>270))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		$la_data[1]=array('columna1'=>'<b>Per�odo de Entrega    Desde:</b> '.$ld_perentdesde.'    <b>Hasta:</b> '.$ld_perenthasta.'');
		$la_columnas=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>570))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$la_data[1]=array('columna1'=>'<b>Concepto</b>         '.$as_conordcom);
		$la_columnas=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>570))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data ---> arreglo de informaci�n
		//	    		   io_pdf ---> Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle 
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_estmodest, $ls_bolivares;
		global $io_pdf;
		
		if($ls_estmodest==1)
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
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'center','width'=>570))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_datatitulo,$la_columnas,'',$la_config);
		unset($la_datatitulo);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-2);
		$la_columnas=array('codigo'=>'<b>C�digo</b>',
						   'denominacion'=>'<b>Denominacion</b>',
						   'cantidad'=>'<b>Cant.</b>',
						   'unidad'=>'<b>Unidad</b>',
						   'cosuni'=>'<b>Costo '.$ls_bolivares.'</b>',
						   'baseimp'=>'<b>Sub-Total '.$ls_bolivares.'</b>',
						   'cargo'=>'<b>Cargo '.$ls_bolivares.'</b>',
						   'montot'=>'<b>Total '.$ls_bolivares.'</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>115), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>115), // Justificaci�n y ancho de la columna
						 			   'cantidad'=>array('justification'=>'right','width'=>40), // Justificaci�n y ancho de la columna
						 			   'unidad'=>array('justification'=>'center','width'=>45), // Justificaci�n y ancho de la columna
						 			   'cosuni'=>array('justification'=>'right','width'=>60), // Justificaci�n y ancho de la columna
						 			   'baseimp'=>array('justification'=>'right','width'=>65), // Justificaci�n y ancho de la columna
						 			   'cargo'=>array('justification'=>'right','width'=>60), // Justificaci�n y ancho de la columna
						 			   'montot'=>array('justification'=>'right','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_cuentas($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_cuentas
		//		   Access: private 
		//	    Arguments: la_data ---> arreglo de informaci�n
		//	    		   io_pdf ---> Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007
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
			$ls_titulo="Estructura Programatica";
		}
		$la_datatit[1]=array('titulo'=>'<b> Detalle de Presupuesto </b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificaci�n y ancho de la columna
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
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('codestpro'=>array('justification'=>'center','width'=>170), // Justificaci�n y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>100), // Justificaci�n y ancho de la columna
						 			   'denominacio'=>array('justification'=>'center','width'=>200), // Justificaci�n y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($li_subtot,$li_totcar,$li_montot,$ls_monlet,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		    Acess: private 
		//	    Arguments: li_subtot ---> Subtotal del articulo
		//	    		   li_totcar -->  Total cargos
		//	    		   li_montot  --> Monto total
		//	    		   ls_monlet   //Monto en letras
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: funci�n que imprime los totales
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		global $io_pdf;
		
		$la_data[1]=array('titulo'=>'<b>Sub Total '.$ls_bolivares.'</b>','contenido'=>$li_subtot,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci�n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>Cargos '.$ls_bolivares.'</b>','contenido'=>$li_totcar,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci�n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>Total '.$ls_bolivares.'</b>','contenido'=>$li_montot,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci�n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-5);
		$la_data[1]=array('titulo'=>'<b> Son: '.$ls_monlet.'</b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>1, // Sombra entre l�neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificaci�n y ancho de la columna
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
		//    Description: Funci�n que imprime el total de la Orden de Compra en Bolivares Fuertes.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 25/09/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data[1]=array('titulo'=>'<b>Monto Bs.F.</b>','contenido'=>$li_montotaux,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci�n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_clausulas($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_cuentas
		//		   Access: private 
		//	    Arguments: la_data ---> arreglo de informaci�n
		//	    		   io_pdf ---> Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 21/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_encabezado;
		global $io_pdf;
		
		$io_pdf->stopObject($io_encabezado);
		$io_pdf->ezNewPage();
		$la_dataclausulas[1]=array('dencla'=>"<b>CONDICIONES</b>");
		$la_columnas=array('dencla'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 12, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('dencla'=>array('justification'=>'center','width'=>570))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_dataclausulas,$la_columnas,'',$la_config);
		unset($la_dataclausulas);
		unset($la_columnas);
		unset($la_config);

		$la_columnas=array('dencla'=>'');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('dencla'=>array('justification'=>'justify','width'=>570))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_datatit);
		unset($la_columnas);
		unset($la_config);
		
		$la_dataclausulas[1]=array('dencla'=>"<b>CONFORME</b>");
		$la_columnas=array('dencla'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 12, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('dencla'=>array('justification'=>'center','width'=>570))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_dataclausulas,$la_columnas,'',$la_config);
		unset($la_dataclausulas);
		unset($la_columnas);
		unset($la_config);
		
		$la_dataclausulas[1]=array('dencla'=>"");
		$la_dataclausulas[2]=array('dencla'=>"<b>NOMBRE ________________________________</b>");
		$la_dataclausulas[3]=array('dencla'=>"");
		$la_dataclausulas[4]=array('dencla'=>"<b>FECHA  ________________________________</b>");
		$la_dataclausulas[5]=array('dencla'=>"");
		$la_dataclausulas[6]=array('dencla'=>"<b>C.I    ________________________________</b>");
		$la_columnas=array('dencla'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('dencla'=>array('justification'=>'left','width'=>570))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_dataclausulas,$la_columnas,'',$la_config);
		unset($la_dataclausulas);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_detalle
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

	//Instancio a la clase de conversi�n de numeros a letras.
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
	$ls_bolivares="Bs.";
	$ls_valorunitrib = 0;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_report=new sigesp_soc_class_reportbsf();
		$ls_bolivares="Bs.F.";
		$numalet->setMoneda("Bolivares Fuerte");
	}
		
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_numordcom=$io_fun_soc->uf_obtenervalor_get("numordcom","");
	$ls_estcondat=$io_fun_soc->uf_obtenervalor_get("tipord","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=true;
	$arrResultado= $io_report->uf_select_orden_imprimir($ls_numordcom,$ls_estcondat,$lb_valido); // Cargar los datos del reporte
	$rs_data= $arrResultado['rs_data'];
	$lb_valido = $arrResultado['lb_valido'];
	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		$ls_descripcion="Gener� el Reporte de Orden de Compra";
		$lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_p_registro_orden_compra.php",$ls_descripcion);
		if($lb_valido)	
		{
			
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(6.5,6,3,3); // Configuraci�n de los margenes en cent�metros
			$setNum = $io_pdf->ezStartPageNumbers(570,47,8,'','',1); // Insertar el n�mero de p�gina
			if ($row=$io_sql->fetch_row($rs_data))
			{
				$ls_numordcom=$row["numordcom"];
				$ls_estcondat=$row["estcondat"];
				$ls_coduniadm=$row["coduniadm"];
				$ls_denuniadm=$row["denuniadm"];
				$ls_codfuefin=$row["codfuefin"];
				$ls_denfuefin=$row["denfuefin"];
				$ls_diaplacom=$row["diaplacom"];
				$ls_forpagcom=$row["forpagcom"];
				$ls_codpro=$row["cod_pro"];
				$ls_nompro=$row["nompro"];
				$ls_rifpro=$row["rifpro"];
				$ls_dirpro=$row["dirpro"];
				$ld_fecordcom=$row["fecordcom"];
				$ls_obscom=$row["obscom"];
				$ld_monsubtot=$row["monsubtot"];
				$ld_monimp=$row["monimp"];
				$ld_montot=$row["montot"];
				$ld_montotalaux=$ld_montot;
				$ld_perentdesde=$row["fechentdesde"];
				$ld_perenthasta=$row["fechenthasta"];
				$ls_estcom=$row["estcom"];
				$ls_nomusu=$row["nomusureg"]." ".$row["apeusureg"];
				$ls_telusu=$row["telusureg"];
				$ls_corele=$row["corelereg"];
				$ls_codtipmod=$row["codtipmod"];
				$ld_perentdesde=$io_funciones->uf_convertirfecmostrar($ld_perentdesde);
				$ld_perenthasta=$io_funciones->uf_convertirfecmostrar($ld_perenthasta);
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
                /////////////////		      
			    // $rs_datos_uni = $io_report->uf_select_unidadtributaria($ls_valorunitri);
				 $ls_valoruni = 0;
			     $ls_valoruni= $io_report->uf_select_unidadtributaria($ls_valoruni); 
				 if($lb_valido)
				 { 
					/* $li_totrows = $io_sql->num_rows($rs_datos_uni); print $li_totrows;
					 if ($li_totrows>0)
					 {
						$li_i = 0;*/
					/*	if($row=$io_sql->fetch_row($rs_datos_uni))
						{
							$li_i=$li_i+1;
							$ls_codartser=$row["fecentvig"];
							$ls_valoruni=$row["valunitri"]; 
						}*/
					  
					//}
					  $ls_valorunitrib=(500*$ls_valoruni); 
				}
				else
				{
				  $ls_valorunitrib=0;
				} 
     		    /////////////////
				uf_print_encabezado_pagina($ls_estcondat,$ls_numordcom,$ld_fecordcom,$ls_coduniadm,$ls_denuniadm,
				                           $ls_codfuefin,$ls_denfuefin,$ls_codpro,$ls_nompro,$ls_obscom,$ls_rifpro,
										   $ls_diaplacom,$ls_dirpro,$ls_forpagcom,$ld_perentdesde,$ld_perenthasta,
										   $ls_valorunitrib,$ld_montotalaux,$ls_estcom,$ls_nomusu,$ls_telusu,$ls_corele,$io_pdf);
				/////DETALLE  DE  LA ORDEN DE COMPRA
			   $arrResultado = $io_report->uf_select_detalle_orden_imprimir($ls_numordcom,$ls_estcondat,$lb_valido);
			   $rs_datos = $arrResultado['rs_data'];
			   $lb_valido = $arrResultado['lb_valido'];
			   if ($lb_valido)
			   {
		     	 $li_totrows = $io_sql->num_rows($rs_datos);
				 if ($li_totrows>0)
				 {
				    $li_i = 0;  $ld_total_parcial = 0; $la_data_total= ""; $la_data_totalg= ""; $li_total_van="";
					$li = 0; $li_p = 0; $pagactual=0;
				    while($row=$io_sql->fetch_row($rs_datos))
					{
						$li_i=$li_i+1;
						$li=$li+1;
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
						$li_cantartser=$row["cantartser"];
						$ld_preartser=$row["preartser"];
						$ld_subtotartser=$ld_preartser*$li_cantartser;
						$ld_totartser=$row["monttotartser"];
						$ld_carartser=abs($ld_totartser-$ld_subtotartser);
						$ld_subtotartser = round($ld_subtotartser,2);						
						$ld_total_parcial=$ld_total_parcial+$ld_totartser;  
						if($li==14)
						{ 
						  $li_p=$li; 
						  $la_data_totalg = $ld_total_parcial;
						}
						
						$ld_preartser=number_format($ld_preartser,2,",",".");
						$ld_subtotartser=number_format($ld_subtotartser,2,",",".");
						$ld_totartser=number_format($ld_totartser,2,",",".");
						$ld_carartser=number_format($ld_carartser,2,",",".");
						$la_data[$li_i]=array('codigo'=>$ls_codartser,'denominacion'=>$ls_denartser,'cantidad'=>$li_cantartser,
											  'unidad'=>$ls_unidad,'cosuni'=>$ld_preartser,'baseimp'=>$ld_subtotartser,
											  'cargo'=>$ld_carartser,'montot'=>$ld_totartser);
					}
					uf_print_detalle($la_data,$io_pdf);  
					$numpag=$io_pdf->ezPageCount;
	 
					for ($li_row=1;$li_row<$numpag;$li_row++)
					{ 
					  if($li_p<14)
						 {
							$la_data_totalg=number_format($la_data_totalg,2,",",".");
							$io_pdf->addText(495,120,9,'VAN         '.$la_data_totalg);
						 }
					   elseif($li>=15)
						 {
							$la_data_totalg=number_format($la_data_totalg,2,",",".");
							$io_pdf->addText(495,585,9,'VIENEN         '.$la_data_totalg);
						 }
					}
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
								$ls_codestpro1 = substr($ls_codestpro1,-$_SESSION["la_empresa"]["loncodestpro1"]);
								$ls_codestpro2 = substr($ls_codestpro2,-$_SESSION["la_empresa"]["loncodestpro2"]);
								$ls_codestpro3 = substr($ls_codestpro3,-$_SESSION["la_empresa"]["loncodestpro3"]);
								$ls_codestpro4 = substr($ls_codestpro4,-$_SESSION["la_empresa"]["loncodestpro1"]);
								$ls_codestpro5 = substr($ls_codestpro5,-$_SESSION["la_empresa"]["loncodestpro2"]);
								$ls_spg_cuenta=$row["spg_cuenta"];
								$ld_monto=$row["monto"];
								$ld_monto=number_format($ld_monto,2,",",".");
								$ls_dencuenta="";
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
								$la_data2[$li_s]=array('codestpro'=>$ls_codestpro,'denominacion'=>$ls_dencuenta,
													  'cuenta'=>$ls_spg_cuenta,'monto'=>$ld_monto);
							}	
							uf_print_detalle_cuentas($la_data2,$io_pdf);
							unset($la_data);
						}
				     }
			      }
		       }
	     	}
	    }
		uf_print_piecabecera($ld_monsubtot,$ld_monimp,$ld_montot,$ls_monto,$io_pdf);
		
	 	$rs_clausulas=$io_report->uf_select_detalle_modalidad($ls_codtipmod,$lb_valido);
	   	if($lb_valido) {
	   		if(!$rs_clausulas->EOF){
		   		while(!$rs_clausulas->EOF) {
		   			$la_dataclausulas[]=array('dencla'=>rtrim($rs_clausulas->fields['dencla']));
		   			$la_dataclausulas[]=array('dencla'=>'');
					$rs_clausulas->MoveNext();
				}
	   		}
			else {
				$la_dataclausulas[]=array('dencla'=>'');
			}
			uf_print_detalle_clausulas($la_dataclausulas,$io_pdf);
		}
	} 	  	 
	if($lb_valido) // Si no ocurrio ning�n error
	{
		$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
		$io_pdf->ezStream(); // Mostramos el reporte
	}
	else // Si hubo alg�n error
	{
		print("<script language=JavaScript>");
		print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
		//print(" close();");
		print("</script>");		
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_soc);
	
 //}
?>
