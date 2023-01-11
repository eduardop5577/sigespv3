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
									   $ls_forpagcom,$ld_perentdesde,$ld_perenthasta,$as_estcom,$as_telpro,$as_faxpro,$ls_emailrep,
									   $ls_origen,$ls_resuniadm,$ls_numsep,$ls_fecsep,$ls_denmodcla,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_estcondat  ---> tipo de la orden de compra
		//	    		   as_numordcom ---> numero de la orden de compra
		//	    		   ad_fecordcom ---> fecha de registro de la orden de compra
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 21/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf, $io_encabezado;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
//		$io_pdf->line(15,40,585,40);
//		$io_pdf->line(480,700,480,760);
//		$io_pdf->line(480,730,585,730);
//        $io_pdf->Rectangle(15,700,570,60);
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_miranda.jpg',25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
//		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		if ($as_estcom=='3')
		{
			$io_pdf->addText(480,765,9,"<b>ANULADO</b>"); // Agregar la Fecha
		}
		if($as_estcondat=="B") 
        {
             $ls_titulo="ORDEN DE COMPRA";	
			 $ls_titulo_grid="Bienes";
        }
        else
        {
             $ls_titulo="ORDEN DE SERVICIO";
			 $ls_titulo_grid="Servicios";
        }
		
		$li_tm=$io_pdf->getTextWidth(11,"<b>ORDEN DE COMPRAS, SERVICIOS O EJECUCION DE OBRAS</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,730,11,"<b>ORDEN DE COMPRAS, SERVICIOS O EJECUCION DE OBRAS</b>"); // Agregar el título
		$io_pdf->addText(485,740,8," <b>Nro. </b>".$as_numordcom); // Agregar el título
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////   PRIMER RECUADRO
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$io_pdf->ezSetY(720);
		$la_data[1]=array('columna1'=>'<b>'.$ls_titulo.'</b>  ');
		$la_data[2]=array('columna1'=>' ');
		$la_columna=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>176, // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'center','width'=>310))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$io_pdf->ezSetY(720);
		$la_data[1]=array('columna2'=>'<b>1. No. Orden</b> ',
		                 'columna3'=>'<b>2. Fecha</b> ');
		$la_data[2]=array('columna2'=>$as_numordcom.'','columna3'=>$ad_fecordcom.'');
		$la_columna=array('columna2'=>'','columna3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>461, // Orientación de la tabla
						 'cols'=>array('columna2'=>array('justification'=>'center','width'=>130), // Justificación y ancho de la columna
						 			   'columna3'=>array('justification'=>'center','width'=>130))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////   SEGUNDO RECUADRO
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$io_pdf->ezSetY(693);
		$la_data[1]=array('columna1'=>'<b>MODALIDAD DE SELECCION: </b>  ');
		$la_data[2]=array('columna1'=>$ls_denmodcla);
		$la_columna=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>176, // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'center','width'=>310))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$io_pdf->ezSetY(693);
		$la_data[1]=array('columna2'=>'<b>3. No. S.E.P. </b> ',
		                 'columna3'=>'<b> Fecha S.E.P.</b> ');
		$la_data[2]=array('columna2'=>$ls_numsep.'','columna3'=>$ls_fecsep.'');
		$la_columna=array('columna2'=>'','columna3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>461, // Orientación de la tabla
						 'cols'=>array('columna2'=>array('justification'=>'center','width'=>130), // Justificación y ancho de la columna
						 			   'columna3'=>array('justification'=>'center','width'=>130))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('columna1'=>'<b>INFORMACION DE LA ORDEN DE COMPRA, SERVICIO U OBRA</b>  ');
		$la_columna=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'center','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('columna1'=>'<b>4. Nombre del Proveedor/Contratista/Beneficiario</b>  ',
		                 'columna2'=>'<b>5. No. R.I.F.</b> ',
		                 'columna3'=>'<b>6. Telefono(s):</b> ',
		                 'columna4'=>$as_telpro);
		$la_data[2]=array('columna1'=>$as_nombre,
		                 'columna2'=>$as_rifpro,
		                 'columna3'=>'<b>Fax:</b> ',
		                 'columna4'=>$as_faxpro);
		$la_columna=array('columna1'=>'','columna2'=>'','columna3'=>'','columna4'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>210), // Justificación y ancho de la columna
						 			   'columna2'=>array('justification'=>'left','width'=>100), // Justificación y ancho de la columna
						 			   'columna3'=>array('justification'=>'left','width'=>130), // Justificación y ancho de la columna
						 			   'columna4'=>array('justification'=>'left','width'=>130))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('columna2'=>'<b>7. Direccion</b> ',
		                 'columna3'=>'<b>8. Direccion de Email</b> ');
		$la_data[2]=array('columna2'=>$as_dirpro.'','columna3'=>$ls_emailrep.'');
		$la_columna=array('columna2'=>'','columna3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna2'=>array('justification'=>'left','width'=>310), // Justificación y ancho de la columna
						 			   'columna3'=>array('justification'=>'left','width'=>260))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);







		$ls_uniadm=$as_coduniadm."  -  ".$as_denuniadm;
		$la_data[1]=array('columna1'=>'<b>9. Nombre de la Unidad Solicitante</b>','columna2'=>'<b>10. Nombre del Responsable de la Unidad</b>    ');
		$la_data[2]=array('columna1'=>$as_denuniadm,'columna2'=>$ls_resuniadm);
		$la_columnas=array('columna1'=>'','columna2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>310), // Justificación y ancho de la columna
						 			   'columna2'=>array('justification'=>'left','width'=>260))); // Justificación y ancho de la columna
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
		//	    Arguments: la_data ---> arreglo de información
		//	    		   io_pdf ---> Instancia de objeto pdf
		//    Description: función que imprime el detalle 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 21/06/2007
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
		$la_datatitulo[1]=array('columna1'=>'<b> Detalle de '.$ls_titulo_grid.'</b>');
		$la_columnas=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
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
		$la_columnas=array('i'=>'<b>11. No.</b>',
						   'cantidad'=>'<b>12.Cant.</b>',
						   'unidad'=>'<b>13.Unidad</b>',
						   'codigo'=>'<b>14. Código</b>',
						   'denominacion'=>'<b>15. Denominacion</b>',
						   'cuenta'=>'<b>16. Partida</b>',
						   'cosuni'=>'<b>17. P. Unit.</b>',
						   'baseimp'=>'<b>18. P.Total</b>',);
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('i'=>array('justification'=>'center','width'=>35), // Justificación y ancho de la columna
						 			   'cantidad'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
						 			   'unidad'=>array('justification'=>'center','width'=>54), // Justificación y ancho de la columna
						 			   'codigo'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>185), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'cosuni'=>array('justification'=>'right','width'=>58), // Justificación y ancho de la columna
						 			   'baseimp'=>array('justification'=>'right','width'=>58))); // Justificación y ancho de la columna
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
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 21/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_estmodest, $ls_bolivares;
		global $io_pdf;
		
		if($ls_estmodest==1)
		{
			$ls_titulo="Estructura Presupuestaria";
		}
		else
		{
			$ls_titulo="Estructura Programatica";
		}
		$la_datatit[1]=array('titulo'=>'<b> 27. Imputacion Presupuestaria</b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
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
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codestpro'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'denominacio'=>array('justification'=>'center','width'=>240), // Justificación y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($li_subtot,$li_totcar,$li_montot,$ls_monlet,$as_diaplacom,$ls_forpagcom,$ls_obscom,$ls_concom,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		    Acess: private 
		//	    Arguments: li_subtot ---> Subtotal del articulo
		//	    		   li_totcar -->  Total cargos
		//	    		   li_montot  --> Monto total
		//	    		   ls_monlet   //Monto en letras
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime los totales
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 21/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		global $io_pdf;
		
		unset($la_data2);
		unset($la_columna2);
		unset($la_config2);
		$la_data2[1]=array('columna1'=>'<b>19. Observaciones: </b>  ');
		$la_data2[2]=array('columna1'=>'1 Se anexan clausulas al reverso de esta orden ');
		$la_data2[3]=array('columna1'=>'2 Incluye el 12% de IVA');
		$la_data2[4]=array('columna1'=>'3 (***) Productos exentos del');
		$la_columna2=array('columna1'=>'');
		$la_config2=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>106, // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>170))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data2,$la_columna2,'',$la_config2);
		unset($la_data2);
		unset($la_columna2);
		unset($la_config2);
		$io_pdf->ezSetDy(48.5);
		$la_data2[1]=array('columna1'=>'<b>20. Anticipo </b>  ','columna2'=>'0');
		$la_data2[2]=array('columna1'=>'<b>22. Fiel      </b>  ','columna2'=>'0');
		$la_data2[3]=array('columna1'=>'<b>23. Plazo </b>  ','columna2'=>$as_diaplacom);
		$la_data2[4]=array('columna1'=>'<b>24. Forma de Pago</b>  ','columna2'=>$ls_forpagcom);
		$la_columna2=array('columna1'=>'','columna2'=>'');
		$la_config2=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>291, // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>100),
						               'columna2'=>array('justification'=>'left','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data2,$la_columna2,'',$la_config2);
		$la_data[1]=array('titulo'=>'<b>21. Sub Total </b>','contenido'=>$li_subtot,);
		unset($la_data2);
		unset($la_columna2);
		unset($la_config2);
		$io_pdf->ezSetDy(48.5);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xPos'=>491, // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>Cargos </b>','contenido'=>$li_totcar,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xPos'=>491, // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>Total </b>','contenido'=>$li_montot);
		$la_data[2]=array('titulo'=>'','contenido'=>"");
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xPos'=>491, // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>25. Descripcion Técnico-Funcional del Bien, Servicio o Ejecución de la Obra </b>');
		$la_data[2]=array('titulo'=>$ls_obscom);
		$la_data[3]=array('titulo'=>'<b>26. Observaciones:  </b>'.$ls_concom);
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		/*$la_data[1]=array('columna1'=>'<b>27. Validez de la Oferta</b>  ',
		                 'columna2'=>'<b>28. Periodo de Garantia del Bien/Serv./Ejec</b> ',
		                 'columna3'=>'<b>29. Condiciones del Pago</b> ');
		$la_data[2]=array('columna1'=>$as_nombre,
		                 'columna2'=>$as_rifpro,
		                 'columna3'=>'<b>Fax:</b> ');
		$la_columna=array('columna1'=>'','columna2'=>'','columna3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>190), // Justificación y ancho de la columna
						 			   'columna2'=>array('justification'=>'left','width'=>190), // Justificación y ancho de la columna
						 			   'columna3'=>array('justification'=>'left','width'=>190))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);*/
		
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($ls_nomusureg,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		    Acess: private 
		//	    Arguments: li_montotaux ---> Total de la Orden Bs.F.
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: Función que imprime el total de la Orden de Compra en Bolivares Fuertes.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 25/09/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data[1]=array('titulo'=>'<b>28. COORDINACION DE PLANIFICACION DE PRESUPUESTO </b>');
		$la_data[2]=array('titulo'=>'Conformado Por');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'','titulo2'=>'');
		$la_data[2]=array('titulo'=>'Williams J. Sandoval P.','titulo2'=>'');
		$la_data[3]=array('titulo'=>'JEFE DE OFICINA DE PLANIFICACION Y PRESUPUESTO','titulo2'=>'SELLO');
		$la_columnas=array('titulo'=>'','titulo2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>285),
						 			   'titulo2'=>array('justification'=>'center','width'=>285))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>29. FIRMAS Y SELLOS </b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'','titulo2'=>'','titulo3'=>'');
		$la_data[2]=array('titulo'=>$ls_nomusureg,'titulo2'=>'Mayra Y. Iriarte I.','titulo3'=>'O´nerlys C. Guerra C.');
		$la_data[3]=array('titulo'=>'ANALISTA DE COMPRAS','titulo2'=>'JEFE DE LA DIVISION DE COMPRAS (E)','titulo3'=>'JEFE DE LA OFIC. DE LA GESTION ADM. (E)');
		$la_columnas=array('titulo'=>'','titulo2'=>'','titulo3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>190),
						 			   'titulo2'=>array('justification'=>'center','width'=>190),
						 			   'titulo3'=>array('justification'=>'center','width'=>190))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'Autorizado por','titulo2'=>'Representante Legal','titulo3'=>'Fecha');
		$la_columnas=array('titulo'=>'','titulo2'=>'','titulo3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>190),
						 			   'titulo2'=>array('justification'=>'center','width'=>190),
						 			   'titulo3'=>array('justification'=>'center','width'=>190))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'','titulo2'=>'','titulo3'=>'');
		$la_data[2]=array('titulo'=>'Laura C. Guerra A.','titulo2'=>'','titulo3'=>'');
		$la_data[3]=array('titulo'=>'PRESIDENTA (E)','titulo2'=>'Firma y Cédula de Identidad','titulo3'=>'');
		$la_columnas=array('titulo'=>'','titulo2'=>'','titulo3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>190),
						 			   'titulo2'=>array('justification'=>'center','width'=>190),
						 			   'titulo3'=>array('justification'=>'center','width'=>190))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>NOTA: </b> Para tramitar el pago de una Orden de Compra, es indispensable la presentacion de la Factura en Original (Sin tachadura ni enmendadura)  y de la nota de entrega. Para tramitar el pago de una Orden de Servicio es indispensable la presentacion de la Factura Original y la conformidad del Servicio Recibido por parte de la unidad.');
		$la_data[2]=array('titulo'=>'Al facturar o enviar correspondencia indique el numero y fecha de la orden correspondiente.');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_clausulas($as_numordcom)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_clausulas
		//		    Acess: private 
		//	    Arguments: li_montotaux ---> Total de la Orden Bs.F.
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: Función que imprime el total de la Orden de Compra en Bolivares Fuertes.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 25/09/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetY(705);
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_miranda.jpg',25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addText(485,740,8," <b>Nro. </b>".$as_numordcom); // Agregar el título
		$la_data[1]=array('titulo'=>'<b>ESTIPULACIONES DE LA ORDEN DE COMPRA</b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetY(693);
		$la_data[1]=array('columna1'=>'1. Las partes intervinientes en la presente Orden de Compra son: a) “Fonden” Fondo de Desarrollo Nacional, S.A.; b) “EL PROVEEDOR”; identificado al final del presente documento.  ');
		$la_data[2]=array('columna1'=>'2. PERFECCIONAMIENTO DE LA ORDEN DE COMPRA: Sin perjuicio de lo estipulado en las demás cláusulas de este documento, se entiende que éste se perfecciona por aceptación expresa. Se considera que la aceptación expresa opera cuando: a) “EL PROVEEDOR”, el representante legal de “EL PROVEEDOR”, o uno de sus empleados, facultado para ello, firman una de las copias de esta Orden de Compra; b) “EL PROVEEDOR” así lo comunica por escrito a “Fonden”.');
		$la_data[3]=array('columna1'=>'3. MODIFICACIONES: Las estipulaciones y especificaciones de esta Orden de Compra no podrán ser modificada por el “EL PROVEEDOR”, sin previo acuerdo entre “Las Partes”, en el cual se haga expresa referencia a esta orden y se especifiquen claramente los términos de la modificación. “Fonden”, en cualquier momento podrá, mediante orden escrita, introducir modificaciones a este pedido. Si estos cambios hechos por “Fonden” causan aumento o disminución de la Orden de Compra, o en el tiempo requerido para cumplirla, se efectuará de mutuo acuerdo el ajuste proporcional y equitativo, según sea el caso. A tales efectos “Fonden” remitirá por este concepto, comunicación escrita al Proveedor en donde se expresen las modificaciones y el ajuste. Si dentro de los dos (2) días hábiles siguientes a la recepción de dicha comunicación; “EL PROVEEDOR” no realizara objeciones u observaciones a la misma se entenderá que ha aceptado tácitamente las modificaciones y el reajuste establecido.');
 		$la_data[4]=array('columna1'=>'4. CONDICIÓN RESOLUTORIA: En caso de incumplimiento de alguna de las obligaciones asumidas en esta Orden de Compra por parte de “EL PROVEEDOR”, “Fonden” tendrá derecho a optar por exigir el cumplimiento inmediato o la resolución del mismo, sin necesidad de declaración de notificación judicial alguna, obligándose sólo a comunicarlo a “EL PROVEEDOR” de manera inmediata. En el caso de incumplimiento alegado se deba a causa extraña no imputable a “EL PROVEEDOR”, este deberá probarlo suficientemente, “Fonden” estará en libertad de considerar o no los argumentos presentados por “EL PROVEEDOR” y podrá optar por la contratación de otro proveedor si así lo desea.');
		$la_data[5]=array('columna1'=>'5. PRÓRROGA: Si “EL PROVEEDOR” no cumple con sus obligación de entregar los bienes en la fecha estipulada, libera a “Fonden” de sus obligaciones correlativas, En tal caso “Fonden” estará en libertad de prorrogar o no la presente Orden de Compra y de recibir o devolver los bienes, siendo a cargo de “EL PROVEEDOR” los costos de la devolución. Toda solicitud de prórroga del presente documento deberá constar por escrito con la firma de las partes y la misma deberá realizarse previo al vencimiento de la presente Orden y de conformidad a lo previsto en la Ley de Contrataciones Públicas sobre este particular. Asimismo, si los bienes indicados en esta no son entregados en la fecha prevista y a satisfacción de “Fonden”, este último queda en libertad de contratar con otro proveedor si así lo desea.');
		$la_data[6]=array('columna1'=>'6. GARANTÍA DE PRODUCTOS: si por efecto de la ejecución de esta Orden de Compra a “EL PROVEEDOR” le corresponde suministrar bienes de distinta naturaleza, éste garantiza que dichas mercancías son de buena calidad, nuevos, libres de defectos en cuanto a diseño, confección y se encuentran en prefectas condiciones de uso y que el material y procesos de fabricación se han cumplido las condiciones de idoneidad en consideración a los fines que cumplirán dichos artículos, según las exigencias de calidad y especificaciones establecidas por “Fonden”. Asimismo, la prestación del servicio deberá cumplir según las exigencias de calidad y especificaciones establecidas por “Fonden”.');
		$la_data[7]=array('columna1'=>'Si se comprueba que es defectuoso en cuanto al material empleado, al montaje o a la confección bajo condiciones de uso normal previsto. “EL PROVEEDOR” hará los arreglos necesarios para reemplazar el material defectuoso por uno nuevo, asumiendo “EL PROVEEDOR” los costos de dicho reemplazo. Asimismo, cuando a juicio de “Fonden” cualquier artículo que contenga materiales o confección defectuosa podrá ser rechazado. En tal caso, todos los costos de transporte de ida y regreso, y los riesgos del mismo serán por cuenta y cargo de “EL PROVEEDOR”, para este efecto “Fonden” formulará el reclamo a más tardar dentro de los diez (10) días continuos siguientes a la entrega.');
		$la_data[8]=array('columna1'=>'PARÁGRAFO ÚNICO: La garantía y responsabilidad a que se refiere la presente cláusula será expedida por los fabricantes del material contra defectos de fabricación y mano de obra la cual se hará efectiva a partir de la entrega de los bienes.');
		$la_data[9]=array('columna1'=>'7. TÍTULO DE PROPIEDAD Y POSESIÓN: “EL PROVEEDOR” garantiza plenamente que tiene derecho legitimo de propiedad y posesión sobre los bienes que suministra por conceptos de esta Orden de Compra y por lo tanto tiene pleno derecho a venderlos, garantiza además, que sobre ellos no existe gravamen alguno, embargo o secuestro ni reclamaciones legales y que saldrá el saneamiento conforme a la Ley.');
		$la_data[10]=array('columna1'=>'8. EMBARQUE – EMPAQUES: El embarque y traslado de los bienes a ser suministrados deberá hacerse en los términos convenidos entre “Las Partes”. “EL PROVEEDOR” se compromete a emplear empaques y/o embalajes adecuados y de buena calidad, en consideración a la clase de bien, a fin de garantizar su llegada a destino en perfectas condiciones asumiendo la responsabilidad por cualquier deterioro que tenga por causa de un empaque defectuoso o traslado inadecuado.');
		$la_data[11]=array('columna1'=>'9. PRECIOS, PATENTES, NORMAS DE SEGURIDAD: “EL PROVEEDOR” garantiza: a) que los precios que figuran en esta Orden de Compra son los comerciales suministrados por “EL PROVEEDOR” y que no violan ninguna regulación sobre precios máximos y que estos precios no serán aumentados en ninguna forma ni agregados recargos y otras sumas de ninguna clase; b) que los bienes comprados no violan ningún derecho de propiedad industrial de terceros y que ampara a “Fonden” en relación con cualquier reclamo y perjuicios que pudiera sufrir éste, como resultado de una posible violación en este sentido; c) que los bienes cumplan las normas de seguridad prescritas oficialmente.');
		$la_data[12]=array('columna1'=>'10. CONDICIONES DE PAGO: El monto convenido para la ejecución de la presente Orden será pagado por “Fonden” a “EL PROVEEDOR”, una vez que se haya recibido la totalidad de lo pedido a su entera satisfacción, de conformidad con las condiciones estipuladas en esta Orden de Compra. Sin embargo, con el consentimiento expreso de “Fonden” se podrá estipular anticipos siempre y cuando “EL PROVEEDOR” presente previamente fianza de anticipo a satisfacción de “Fonden”, emitida por una empresa');
		$la_columna=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>176, // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>285))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$io_pdf->ezSetY(693);
		$la_data[1]=array('columna1'=>'10. de seguros debidamente inscrita en la superintendencia de la actividad , por el cien por ciento (100%) de la cantidad dada como anticipo, sin incluir el Impuesto al Valor Agregado (IVA). Dicha Fianza se mantendrá durante el tiempo de amortización de la totalidad del anticipo.');
		$la_data[2]=array('columna1'=>'Para una oportuna tramitación del pago, se deberá entregar a “Fonden”, el original de la factura, la cual deberá cumplir con las condiciones estipuladas por el Ministerio del Poder Popular para las Finanzas, por intermedio del Servicio Nacional Integrado de Administración Aduanera y tributaria (SENIAT).');
		$la_data[3]=array('columna1'=>'11. INDEMNIZACIONES: Cualquier indemnización y/o reembolso que deba “EL PROVEEDOR” a “Fonden” según esta Orden de Compra, podrán ser deducidas del monto que “Fonden” adeuda a “EL PROVEEDOR” por cualquier concepto y sin perjuicio del derecho de ejercer las acciones legales correspondientes a su sola discreción.');
		$la_data[4]=array('columna1'=>'Si para la fecha prevista no se ha dado cumplimiento al objeto de la presente Orden de Compra, “EL PROVEEDOR” pagará como indemnización convencional por los daños y perjuicios que la demora cause del diez por ciento (10%) del monto total de la presente orden, cuando el retraso sea menor a quince (15) días continuos, y si es mayor una penalización equivalente al veinticinco por ciento (25%) del monto total de la presente Orden. Lo adeudado por la indemnización antes descrita, será compensado por “Fonden” adeude a “EL PROVEEDOR” por la presente Orden de Compra.');
		$la_data[5]=array('columna1'=>'12. INCUMPLIMIENTO DE PAGO: En caso de incumplimiento de pago por parte de “Fonden” “EL PROVEEDOR” podrá exigir además de las cantidades adeudas, los intereses moratorios legales que se produzcan hasta la fecha del pago definitivo.');
		$la_data[6]=array('columna1'=>'13. RESCISIÓN UNILATERAL: En caso de rescisión unilateral de la Orden de Compra sin causa justificada por parte de “Fonden” y antes que el proveedor realice la entrega de los bienes, aquella se obliga a pagar treinta por ciento (30%) del precio no facturado hasta el momento de la resolución de la presente Orden, bajo ninguna circunstancia “Fonden” podrá rescindir esta Orden de Compra de forma unilateral una vez que “EL PROVEEDOR” haya completado la entrega de los bienes en las condiciones establecidas.');
		$la_data[7]=array('columna1'=>'14. OBLIGACIONES LABORALES Y ANTE TERCEROS: “EL PROVEEDOR” se obliga, en su condición de patrono, a asumir por su exclusiva cuenta y bajo su sola responsabilidad y propias expensas, las obligaciones derivadas de las leyes laborales. Así como en sus relaciones con sus dependientes y contratantes.');
		$la_data[8]=array('columna1'=>'15. CESIÓN Y SUBCONTRATACIÓN: “EL PROVEEDOR” no podrá ceder bajo ninguna circunstancia, los créditos, derechos y obligaciones derivados de la presente Orden de Compra.');
		$la_data[9]=array('columna1'=>'16. FIANZA DE FIEL CUMPLIMIENTO: En caso de ser exigida por “Fonden” para garantizar el fiel y oportuno cumplimiento de todas las obligaciones que asume “EL PROVEEDOR” por la presente Orden durante su vigencia y hasta su terminación, incluyendo sus prórrogas si las hubiere, éste, deberá presentar una fianza de fiel cumplimiento otorgada por una entidad bancaria o empresa de seguros, debidamente autorizada por la SUDEBAN o la Superintendencia de la Actividad Aseguradora, respectivamente, a plena satisfacción de “Fonden”, por un monto del veinte por ciento (20%), del monto de la Orden de Compra, incluyendo el Impuesto al Valor Agregado (IVA).');
		$la_data[10]=array('columna1'=>'Podrá acordarse con el contratista una garantía constituida por la retención del diez por ciento (10%), sobre los pagos que se realicen, cuyo monto total retenido será reintegrado al momento de la recepción definitiva del bien u obra o terminación del servicio.');
		$la_data[11]=array('columna1'=>'17. COMPROMISO DE RESPONSABILIDAD SOCIAL: Cuando el monto de la contratación supere las Dos Mil Quinientas Unidades Tributarias (2.500 UT), “EL PROVEEDOR” se comprometerá a cumplir con el compromiso de responsabilidad social durante el período de ejecución de la presente Orden de Compra, conforme a lo previsión contenida en la Ley de Contrataciones Públicas y su Reglamento.');
		$la_data[12]=array('columna1'=>'18. RIESGOS: “EL PROVEEDOR” asume todos los riesgos que por cualquier causa pudieran afectar los bienes hasta su entrega a satisfacción de “Fonden”.');
		$la_data[13]=array('columna1'=>'19. CONFIDENCIALIDAD: “EL PROVEEDOR” reconoce expresamente la confidencialidad de la información suministrada por “Fonden” y se obliga a no divulgarla.');
		$la_data[14]=array('columna1'=>'20. EL PROVEEDOR: declara y acepta que conoce y dará estricto cumplimiento a la normativa que regula la ética pública y la moral administrativa la cual regirá el presente Contrato, en todo aquello que le resulte aplicable. Así como se compromete a la consignacióne todos los recaudos que estipule FONDEN, para el pago.');
		$la_data[15]=array('columna1'=>'21. Domicilio: Para todos los efectos derivados de la presente orden, las partes eligen como domicilio especial a la ciudad de Caracas, a la jurisdicción de cuyos tribunales declaran someterse.');
		$la_data[16]=array('columna1'=>'');
		$la_data[17]=array('columna1'=>'');
		$la_data[18]=array('columna1'=>'Firma del Proveedor  _________________________');
		$la_data[19]=array('columna1'=>'');
		$la_data[20]=array('columna1'=>'Nombre y Apellido  _________________________');
		$la_data[21]=array('columna1'=>'');
		$la_data[22]=array('columna1'=>'C.I.  _________________________');
		$la_data[23]=array('columna1'=>'');
		$la_data[24]=array('columna1'=>'Cargo  _________________________');
		$la_data[25]=array('columna1'=>'');
		$la_data[26]=array('columna1'=>'Fecha  _________________________');
		$la_data[27]=array('columna1'=>'');
		$la_columna=array('columna1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>461, // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>285))); // Justificación y ancho de la columna
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
	$ls_bolivares="Bs.";
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_report=new sigesp_soc_class_reportbsf();
		$ls_bolivares="Bs.F.";
		$numalet->setMoneda("Bolivares Fuerte");
	}
		
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_numordcom=$io_fun_soc->uf_obtenervalor_get("numordcom","");
	$ls_estcondat=$io_fun_soc->uf_obtenervalor_get("tipord","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=true;
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
			//
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(8.5,1,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(590,10,8,'','',1); // Insertar el número de página
			$li_numpag = $io_pdf->ezPageCount; // Número de página
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
				$ld_perentdesde=$row["fechentdesde"];
				$ld_perenthasta=$row["fechenthasta"];
				$ls_estcom=$row["estcom"];
				$ls_telpro=$row["telpro"];
				$ls_faxpro=$row["faxpro"];
				$ls_emailrep=$row["emailrep"];
				$ls_concom=$row["concom"];
				$ls_estoricom=$row["estoricom"];
				$ls_resuniadm=$row["resuniadm"];
				$ls_nomusureg=$row["nomusureg"]." ".$row["apeusureg"];
				$ls_denmodcla=$row["denmodcla"];
				if($ls_estoricom=="0")
				{
					$ls_origen="Compra Directa";
				}
				else
				{
					$ls_origen="Proceso de Cotizaciones";
				}
				
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

				$ls_numsep="";
				$ls_fecsep="";

		 		$ls_sep=$io_report->uf_buscar_sep($ls_numordcom,$ls_estcondat);
				$la_sep=explode("@",$ls_sep);
				if($ls_sep!="")
				{
					$ls_numsep=$la_sep[0];
					$ls_fecsep=$la_sep[1];
				}
				uf_print_encabezado_pagina($ls_estcondat,$ls_numordcom,$ld_fecordcom,$ls_coduniadm,$ls_denuniadm,$ls_codfuefin,$ls_denfuefin,
				                            $ls_codpro,$ls_nompro,$ls_obscom,$ls_rifpro,$ls_diaplacom,$ls_dirpro,$ls_forpagcom,$ld_perentdesde,
											$ld_perenthasta,$ls_estcom,$ls_telpro,$ls_faxpro,$ls_emailrep,$ls_origen,$ls_resuniadm,$ls_numsep,$ls_fecsep,$ls_denmodcla,$io_pdf);
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
						$ls_spgcuenta=$row["spg_cuenta"];
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
						$ld_subtotartser=$row["montsubartser"];
						$ld_totartser=$row["monttotartser"];
						$ls_denunimed=$row["denunimed"];
						if(strtoupper($_SESSION['ls_gestor'])=='OCI8PO')
						{
							$arrResultado=$io_report->uf_select_porcentajeimpuesto($ls_numordcom,$ls_estcondat,$ls_codartser,$lb_valido);
							$ld_porimp = $arrResultado['ab_porcar'];
							$lb_valido = $arrResultado['lb_valido'];
						}
						else
						{
							$ld_porimp=$row["porimp"];
						}
						$ld_carartser=$ld_subtotartser*($ld_porimp/100);
						
						
						
						$li_cantartser=number_format($li_cantartser,2,",",".");
						$ld_preartser=number_format($ld_preartser,2,",",".");
						$ld_subtotartser=number_format($ld_subtotartser,2,",",".");
						$ld_totartser=number_format($ld_totartser,2,",",".");
						$ld_carartser=number_format($ld_carartser,2,",",".");
						$la_data[$li_i]=array('codigo'=>$ls_codartser,'denominacion'=>$ls_denartser,'cantidad'=>$li_cantartser,
											  'unidad'=>$ls_denunimed,'cosuni'=>$ld_preartser,'baseimp'=>$ld_subtotartser,
											  'cargo'=>$ld_carartser,'montot'=>$ld_totartser,'i'=>$li_i,'cuenta'=>$ls_spgcuenta);
					}
					uf_print_detalle($la_data,$io_pdf);
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
								$ls_codestpro1 = substr($ls_codestpro1,-$_SESSION["la_empresa"]["loncodestpro1"]);
								$ls_codestpro2 = substr($ls_codestpro2,-$_SESSION["la_empresa"]["loncodestpro2"]);
								$ls_codestpro3 = substr($ls_codestpro3,-$_SESSION["la_empresa"]["loncodestpro3"]);
								$ls_codestpro4 = substr($ls_codestpro4,-$_SESSION["la_empresa"]["loncodestpro4"]);
								$ls_codestpro5 = substr($ls_codestpro5,-$_SESSION["la_empresa"]["loncodestpro5"]);
								$ls_spg_cuenta=$row["spg_cuenta"];
								$ld_monto=$row["monto"];
								$ld_monto=number_format($ld_monto,2,",",".");
								$ls_dencuenta="";
								$arrResultado = $io_report->uf_select_denominacionspg($ls_spg_cuenta,$ls_dencuenta);																																						
								$ls_dencuenta = $arrResultado['as_denominacion'];
								$lb_valido = $arrResultado['lb_valido'];
								if($ls_estmodest==1)
								{
									$ls_codestpro  = $ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3;
								}
								else
								{
									$ls_codestpro = $ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3.'-'.$ls_codestpro4.'-'.$ls_codestpro5;
								}
								$la_data[$li_s]=array('codestpro'=>$ls_codestpro,'denominacion'=>$ls_dencuenta,
													  'cuenta'=>$ls_spg_cuenta,'monto'=>$ld_monto);
							}	
							uf_print_piecabecera($ld_monsubtot,$ld_monimp,$ld_montot,$ls_monto,$as_diaplacom,$ls_forpagcom,$ls_obscom,$ls_concom,$io_pdf);
							uf_print_detalle_cuentas($la_data,$io_pdf);
							unset($la_data);
						}
				     }
			      }
		       }
	     	}
		}
	} 	  	 
//	print $li_numpag=$io_pdf->ezPageCount; // Número de página
		$io_pdf->stopObject($io_encabezado);
	if ($io_pdf->ezPageCount==$li_numpag)
	{// Hacemos el commit de los registros que se desean imprimir
		$io_pdf->transaction('commit');
		uf_print_firmas($ls_nomusureg,$io_pdf);
		$io_pdf->ezNewPage(); // Insertar una nueva página
		$io_pdf->ezSetCmMargins(2,4,4,4); // Configuración de los margenes en centímetros
		uf_print_clausulas($ls_numordcom);
	}
	else
	{// Hacemos un rollback de los registros, agregamos una nueva página y volvemos a imprimir
		$io_pdf->transaction('rewind');
		uf_print_firmas($ls_nomusureg,$io_pdf);
		$io_pdf->ezNewPage(); // Insertar una nueva página
		$io_pdf->ezSetCmMargins(2,4,4,4); // Configuración de los margenes en centímetros
		uf_print_clausulas($ls_numordcom);
	}
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
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_soc);
?>