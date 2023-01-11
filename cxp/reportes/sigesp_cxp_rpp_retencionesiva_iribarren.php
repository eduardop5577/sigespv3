<?PHP
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
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 14/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesiva.php",$ls_descripcion);
		return $lb_valido;
	}// end function uf_insert_seguridad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezadopagina($as_titulo,$as_numcon,$ad_fecrep,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
//		$io_encabezado=$io_pdf->openObject();
//		$io_pdf->saveState();
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],22,719,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_escudo.jpg',527,719,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->setStrokeColor(0,0,0);
       // $io_pdf->Rectangle(48,520,902,60);	
		$li_tm=$io_pdf->getTextWidth(9,"<b>Republica Bolivariana de Venezuela</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,360,9,"<b>Republica Bolivariana de Venezuela</b>"); // Agregar el t�ulo				
		$li_tm=$io_pdf->getTextWidth(9,"<b>Barquisimeto - Estado Lara</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,350,9,"<b>Barquisimeto - Estado Lara</b>"); // Agregar el t�ulo				
		$li_tm=$io_pdf->getTextWidth(9,"<b>Alcaldia del Municipio Iribarren</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,340,9,"<b>Alcaldia del Municipio Iribarren</b>"); // Agregar el t�ulo				
		$li_tm=$io_pdf->getTextWidth(9,"<b>Direccion de Tesoreria</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,330,9,"<b>Direccion de Tesoreria</b>"); // Agregar el t�ulo				


		$li_tm=$io_pdf->getTextWidth(10,"<b>".$as_titulo."</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,320,10,"<b>".$as_titulo."</b>"); // Agregar el t�ulo				

		$li_tm=$io_pdf->getTextWidth(7,"(Ley IVA - Art. 11 La administracion Tributaria podra designar como responsables del pago del impuesto, en calidad de agentes");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,310,7,"(Ley IVA - Art. 11 La administracion Tributaria podra designar como responsables del pago del impuesto, en calidad de agentes"); // Agregar el t�ulo				

		$li_tm=$io_pdf->getTextWidth(7,"de retencion, a los compradores o adquirentes de determinados bienes muebles y los receptores de ciertos servicios).");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,300,7,"de retencion, a los compradores o adquirentes de determinados bienes muebles y los receptores de ciertos servicios)."); // Agregar el t�ulo				


//		$io_pdf->restoreState();
//		$io_pdf->closeObject();
//		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------	

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezadopagina2($as_titulo,$as_numcon,$ad_fecrep,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
//		$io_encabezado=$io_pdf->openObject();
//		$io_pdf->saveState();
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],22,319,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_escudo.jpg',527,319,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->setStrokeColor(0,0,0);
		$li_tm=$io_pdf->getTextWidth(9,"<b>Republica Bolivariana de Venezuela</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,760,9,"<b>Republica Bolivariana de Venezuela</b>"); // Agregar el t�ulo				
		$li_tm=$io_pdf->getTextWidth(9,"<b>Barquisimeto - Estado Lara</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,750,9,"<b>Barquisimeto - Estado Lara</b>"); // Agregar el t�ulo				
		$li_tm=$io_pdf->getTextWidth(9,"<b>Alcaldia del Municipio Iribarren</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,740,9,"<b>Alcaldia del Municipio Iribarren</b>"); // Agregar el t�ulo				
		$li_tm=$io_pdf->getTextWidth(9,"<b>Direccion de Tesoreria</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,730,9,"<b>Direccion de Tesoreria</b>"); // Agregar el t�ulo				


		$li_tm=$io_pdf->getTextWidth(10,"<b>".$as_titulo."</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,720,10,"<b>".$as_titulo."</b>"); // Agregar el t�ulo				

		$li_tm=$io_pdf->getTextWidth(7,"(Ley IVA - Art. 11 La administracion Tributaria podra designar como responsables del pago del impuesto, en calidad de agentes");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,710,7,"(Ley IVA - Art. 11 La administracion Tributaria podra designar como responsables del pago del impuesto, en calidad de agentes"); // Agregar el t�ulo				

		$li_tm=$io_pdf->getTextWidth(7,"de retencion, a los compradores o adquirentes de determinados bienes muebles y los receptores de ciertos servicios).");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,700,7,"de retencion, a los compradores o adquirentes de determinados bienes muebles y los receptores de ciertos servicios)."); // Agregar el t�ulo				


//		$io_pdf->restoreState();
//		$io_pdf->closeObject();
//		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------	

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_agenteret,$as_rifagenteret,$as_perfiscal,$as_codsujret,$as_nomsujret,$as_rif,$as_diragenteret,
					           $as_numcon,$ad_fecrep,$ai_estcmpret,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_agenteret // Nombre del Agente de retención
		//	    		   as_rifagenteret // Rif del Agente de retención
		//	    		   as_perfiscal // Período fiscal
		//	    		   as_codsujret // Código del Sujeto a retención
		//	    		   as_nomsujret // Nombre del Sujeto a retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   ai_estcmpret // estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->setStrokeColor(0,0,0);
		if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,480,180,30);		
			$io_pdf->addText(90,490,15,"<b> ANULADO </b>"); 
		}	
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'0. COMPROBANTE','name1'=>'1. FECHA','name2'=>'2. NOMBRE O RAZON SOCIAL DE AGENTE DE RETENCION');
		$la_columna=array('name'=>'','name1'=>'','name2'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>130), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>130), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>300))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>$as_numcon,'name1'=>$ad_fecrep,'name2'=>$as_agenteret);
		$la_columna=array('name'=>'','name1'=>'','name2'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>130), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>130), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>300))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------------
			$la_data[1]=array('name'=>'3. R.I.F. DEL AGENTE DE RETENCION','name1'=>'4. PERIODO FISCAL','name2'=>'5. DIRECCION DEL AGENTE DE RETENCION');
		$la_columna=array('name'=>'','name1'=>'','name2'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>160), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>160), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>240))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>$as_rifagenteret,'name1'=>$as_perfiscal,'name2'=>$as_diragenteret);
		$la_columna=array('name'=>'','name1'=>'','name2'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>160), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>160), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>240))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------

		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'6.NOMBRE O RAZON SOCIAL DE SUJETO RETENIDO','name1'=>'7. R.I.F. DE SUJETO RETENIDO');
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>200), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>360))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>$as_nomsujret,'name1'=>$as_rif);
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>200), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>360))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
			
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera2($as_agenteret,$as_rifagenteret,$as_perfiscal,$as_codsujret,$as_nomsujret,$as_rif,$as_diragenteret,
					           $as_numcon,$ad_fecrep,$ai_estcmpret,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_agenteret // Nombre del Agente de retención
		//	    		   as_rifagenteret // Rif del Agente de retención
		//	    		   as_perfiscal // Período fiscal
		//	    		   as_codsujret // Código del Sujeto a retención
		//	    		   as_nomsujret // Nombre del Sujeto a retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   ai_estcmpret // estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->ezSetY(290);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'0. COMPROBANTE','name1'=>'1. FECHA','name2'=>'2. NOMBRE O RAZON SOCIAL DE AGENTE DE RETENCION');
		$la_columna=array('name'=>'','name1'=>'','name2'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>130), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>130), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>300))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>$as_numcon,'name1'=>$ad_fecrep,'name2'=>$as_agenteret);
		$la_columna=array('name'=>'','name1'=>'','name2'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>130), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>130), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>300))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------------
			$la_data[1]=array('name'=>'3. R.I.F. DEL AGENTE DE RETENCION','name1'=>'4. PERIODO FISCAL','name2'=>'5. DIRECCION DEL AGENTE DE RETENCION');
		$la_columna=array('name'=>'','name1'=>'','name2'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>160), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>160), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>240))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>$as_rifagenteret,'name1'=>$as_perfiscal,'name2'=>$as_diragenteret);
		$la_columna=array('name'=>'','name1'=>'','name2'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>160), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>160), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>240))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------

		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'6.NOMBRE O RAZON SOCIAL DE SUJETO RETENIDO','name1'=>'7. R.I.F. DE SUJETO RETENIDO');
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>200), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>360))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>$as_nomsujret,'name1'=>$as_rif);
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>200), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>360))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
			
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$ai_totconiva,$ai_totsiniva,$ai_totbasimp,$ai_totmonimp,$ai_totivaret,$li_totcuenta,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   ai_totconiva // Total con iva
		//	    		   ai_totsiniva // Total sin iva
		//	    		   ai_totbasimp // Total de la base imponible
		//	    		   ai_totmonimp // Total monto imponible
		//	    		   ai_totivaret // Total iva retenido
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data1[1]=array('titulo'=>'');
		$la_columna=array('titulo'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>900,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>900))); // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$ls_titulo="Compras Internas o Importaciones";
		$la_data1[1]=array('name'=>$ls_titulo);
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>6,    // Tamaño de Letras
						 'showLines'=>1,    // Mostrar Lineas
						 'shaded'=>0,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>502, 						 					
						 'width'=>114,      // Ancho de la tabla						 
						 'maxWidth'=>114,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>114)));  // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$ls_titulo1="Total Compras Incluyendo el IVA";
		$ls_titulo2="Compras sin Derecho a Credito IVA";
		$la_columna=array('numope'=>'<b>Oper Nro.</b>',
						  'fecfac'=>'<b>Fecha de la Factura</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
  						  'numref'=>'<b>Num. Ctrol de Factura</b>',		
						  'numnotdeb'=>'<b>Numero Nota Debit.</b>',
						  'numnotcre'=>'<b>Numero Nota Crdt.</b>',				  
  						  'tiptrans'=>'<b>Tipo de Transacc.</b>',
						  'numfacafec'=>'<b>Numero de Factura  Afectada</b>',
						  'totalconiva'=>'<b>'.$ls_titulo1.'</b>',
						  'compsinderiva'=>'<b>'.$ls_titulo2.'</b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>%     Alicuota</b>',
						  'totimp'=>'<b>Impuesto IVA</b>',
						  'ivaret'=>'<b>IVA Retenido</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 6,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						// 'xPos'=>500, // Orientación de la tabla
						 'cols'=>array('numope'=>array('justification'=>'center','width'=>45), // Justificacion y ancho de la columna
						 			   'fecfac'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
									   'numref'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
									   'numnotdeb'=>array('justification'=>'center','width'=>42),
  						 			   'numnotcre'=>array('justification'=>'center','width'=>42),
   						 			   'tiptrans'=>array('justification'=>'center','width'=>42),		
									   'numfacafec'=>array('justification'=>'center','width'=>42),		   									   
   						 			   'totalconiva'=>array('justification'=>'center','width'=>52),
									   'compsinderiva'=>array('justification'=>'center','width'=>42),
						 			   'baseimp'=>array('justification'=>'center','width'=>42),
						 			   'porimp'=>array('justification'=>'center','width'=>30),
   						 			   'totimp'=>array('justification'=>'center','width'=>42),
  						 			   'ivaret'=>array('justification'=>'center','width'=>42))); 
		$io_pdf->ezSetDy(-2);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'TOTAL','name1'=>$ai_totconiva,'name2'=>$ai_totsiniva,'name3'=>$ai_totbasimp,
		                  'name4'=>'','name5'=>$ai_totmonimp,'name6'=>$ai_totivaret);						                      
		$la_columna=array('name'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'','name5'=>'','name6'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>6,    // Tamaño de Letras
						 'showLines'=>2,    // Mostrar Lineas
						 'shaded'=>0,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>309, 
						 'yPos'=>734,       // Orientacion de la tabla						
						 'width'=>200,
						 'xOrientation'=>'right',      // Ancho de la tabla						 
						 'maxWidth'=>200,
						 'cols'=>array('name'=>array('justification'=>'right','width'=>41), // Justificacion y ancho de la columna
						               'name1'=>array('justification'=>'center','width'=>52), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
						 			   'name3'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
									   'name4'=>array('justification'=>'center','width'=>30), // Justificacion y ancho de la columna
									   'name5'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
   						 			   'name6'=>array('justification'=>'center','width'=>42)));  
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'Total Factura','name1'=>$ai_totconiva);
		$la_data[2]=array('name'=>'Total en Cuenta','name1'=>$li_totcuenta);
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>300, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>68), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>68))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'','name1'=>'');
		$la_data[2]=array('name'=>'_______________________','name1'=>'');
		$la_data[3]=array('name'=>'TESORERIA MUNICIPAL','name1'=>'');
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>300, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>98), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>78))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
	}// end function uf_print_detalle

	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle2($la_data,$ai_totconiva,$ai_totsiniva,$ai_totbasimp,$ai_totmonimp,$ai_totivaret,$li_totcuenta,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   ai_totconiva // Total con iva
		//	    		   ai_totsiniva // Total sin iva
		//	    		   ai_totbasimp // Total de la base imponible
		//	    		   ai_totmonimp // Total monto imponible
		//	    		   ai_totivaret // Total iva retenido
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data1[1]=array('titulo'=>'');
		$la_columna=array('titulo'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>900,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>900))); // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$ls_titulo="Compras Internas o Importaciones";
		$la_data1[1]=array('name'=>$ls_titulo);
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>6,    // Tamaño de Letras
						 'showLines'=>1,    // Mostrar Lineas
						 'shaded'=>0,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>502, 						 					
						 'width'=>114,      // Ancho de la tabla						 
						 'maxWidth'=>114,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>114)));  // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$ls_titulo1="Total Compras Incluyendo el IVA";
		$ls_titulo2="Compras sin Derecho a Credito IVA";
		$la_columna=array('numope'=>'<b>Oper Nro.</b>',
						  'fecfac'=>'<b>Fecha de la Factura</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
  						  'numref'=>'<b>Num. Ctrol de Factura</b>',		
						  'numnotdeb'=>'<b>Numero Nota Debit.</b>',
						  'numnotcre'=>'<b>Numero Nota Crdt.</b>',				  
  						  'tiptrans'=>'<b>Tipo de Transacc.</b>',
						  'numfacafec'=>'<b>Numero de Factura  Afectada</b>',
						  'totalconiva'=>'<b>'.$ls_titulo1.'</b>',
						  'compsinderiva'=>'<b>'.$ls_titulo2.'</b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>%     Alicuota</b>',
						  'totimp'=>'<b>Impuesto IVA</b>',
						  'ivaret'=>'<b>IVA Retenido</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 6,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						// 'xPos'=>500, // Orientación de la tabla
						 'cols'=>array('numope'=>array('justification'=>'center','width'=>45), // Justificacion y ancho de la columna
						 			   'fecfac'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
									   'numref'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
									   'numnotdeb'=>array('justification'=>'center','width'=>42),
  						 			   'numnotcre'=>array('justification'=>'center','width'=>42),
   						 			   'tiptrans'=>array('justification'=>'center','width'=>42),		
									   'numfacafec'=>array('justification'=>'center','width'=>42),		   									   
   						 			   'totalconiva'=>array('justification'=>'center','width'=>52),
									   'compsinderiva'=>array('justification'=>'center','width'=>42),
						 			   'baseimp'=>array('justification'=>'center','width'=>42),
						 			   'porimp'=>array('justification'=>'center','width'=>30),
   						 			   'totimp'=>array('justification'=>'center','width'=>42),
  						 			   'ivaret'=>array('justification'=>'center','width'=>42))); 
		$io_pdf->ezSetDy(-2);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'TOTAL','name1'=>$ai_totconiva,'name2'=>$ai_totsiniva,'name3'=>$ai_totbasimp,
		                  'name4'=>'','name5'=>$ai_totmonimp,'name6'=>$ai_totivaret);						                      
		$la_columna=array('name'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'','name5'=>'','name6'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>6,    // Tamaño de Letras
						 'showLines'=>2,    // Mostrar Lineas
						 'shaded'=>0,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>309, 
						 'yPos'=>734,       // Orientacion de la tabla						
						 'width'=>200,
						 'xOrientation'=>'right',      // Ancho de la tabla						 
						 'maxWidth'=>200,
						 'cols'=>array('name'=>array('justification'=>'right','width'=>41), // Justificacion y ancho de la columna
						               'name1'=>array('justification'=>'center','width'=>52), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
						 			   'name3'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
									   'name4'=>array('justification'=>'center','width'=>30), // Justificacion y ancho de la columna
									   'name5'=>array('justification'=>'center','width'=>42), // Justificacion y ancho de la columna
   						 			   'name6'=>array('justification'=>'center','width'=>42)));  
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'Total Factura','name1'=>$ai_totconiva);
		$la_data[2]=array('name'=>'Total en Cuenta','name1'=>$li_totcuenta);
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>300, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>68), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>68))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'','name1'=>'');
		$la_data[2]=array('name'=>'_______________________','name1'=>'');
		$la_data[3]=array('name'=>'TESORERIA MUNICIPAL','name1'=>'');
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>300, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>98), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>78))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
	}// end function uf_print_detalle

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piepagina($ls_numsol,$ls_fecsol,$ls_numche,$ls_fecche,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piepagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf,$io_funciones;
		
		$io_pdf->setStrokeColor(0,0,0);
		if($ls_fecsol!="1900-01-01")
		{
			$ls_fecsol=$io_funciones->uf_convertirfecmostrar($ls_fecsol);	
		}
		else
		{
			$ls_fecsol="";
		}
		
		if($ls_fecche!="1900-01-01")
		{
			$ls_fecche=$io_funciones->uf_convertirfecmostrar($ls_fecche);	
		}
		else
		{
			$ls_fecche="";
		}

	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

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
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	if($ls_tiporeporte==1)
	{
		$ls_titulo="COMPROBANTE DE RETENCION DEL IMPUESTO AL VALOR AGREGADO EN Bs.F.";	
	}
	else
	{
		$ls_titulo="COMPROBANTE DE RETENCION DEL IMPUESTO AL VALOR AGREGADO EN Bs.";	
	}
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
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
			$io_pdf=new Cezpdf('LETTER','portrait');
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(3.5,1,3,3);
			$lb_valido=true;
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numcom=$la_datos[$li_z];
				$lb_valido=$io_report->uf_retencionesiva_proveedor($ls_numcom);
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
					}											
					uf_print_encabezadopagina($ls_titulo,$ls_numcon,$ls_fecrep,$io_pdf); 
					uf_print_encabezadopagina2($ls_titulo,$ls_numcon,$ls_fecrep,$io_pdf);
					uf_print_cabecera($ls_agenteret,$ls_rifagenteret,$ls_perfiscal,$ls_codsujret,$ls_nomsujret,$ls_rif,
					                  $ls_diragenteret,$ls_numcon,$ls_fecrep,$li_estcmpret,$io_pdf);
					$lb_valido=$io_report->uf_retencionesiva_detalle($ls_numcom);
					if($lb_valido)
					{
						$li_totalconiva = 0;
						$li_totalsiniva = 0;
						$li_totalbaseimp = 0;
						$li_totalmontoimp = 0;
						$li_totalivaret = 0;
						$li_total=$io_report->ds_detalle->getRowCount("numfac");			   
						$ls_numsol="";									
						$ls_fecsol="";									
						$ls_numche="";									
						$ls_fecche="";									
						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							$ls_numope=$io_report->ds_detalle->data["numope"][$li_i];					
							$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];	
							$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];	              
							$ld_fecfac=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecfac"][$li_i]);	
							$li_siniva=$io_report->ds_detalle->data["totcmp_sin_iva"][$li_i];	
							$li_coniva=$io_report->ds_detalle->data["totcmp_con_iva"][$li_i];	
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];	
							$li_porimp=$io_report->ds_detalle->data["porimp"][$li_i];	
							$li_totimp=$io_report->ds_detalle->data["totimp"][$li_i];	
							$li_ivaret=$io_report->ds_detalle->data["iva_ret"][$li_i];	
							$ls_numdoc=$io_report->ds_detalle->data["numdoc"][$li_i];	
							$ls_tiptrans=$io_report->ds_detalle->data["tiptrans"][$li_i];	
							$ls_numnotdeb=$io_report->ds_detalle->data["numnd"][$li_i];	
							if(trim($ls_numnotdeb)!="")
								$ls_tiptrans="02-Reg";
							$ls_numnotcre=$io_report->ds_detalle->data["numnc"][$li_i];									
							if(trim($ls_numnotcre)!="")
								$ls_tiptrans="03-Reg";
							$li_monto=$li_baseimp + $li_totimp;  
							$li_totdersiniva= abs($li_coniva - $li_monto);
							$ls_numfacafec="";
							$li_totalconiva=$li_totalconiva + $li_coniva;	
							$li_totalsiniva=$li_totalsiniva + $li_totdersiniva;
							$li_totalbaseimp=$li_totalbaseimp + $li_baseimp ;	
							$li_totalmontoimp=$li_totalmontoimp + $li_totimp;	
							$li_totalivaret=$li_totalivaret + $li_ivaret;								
							$li_totdersiniva=number_format($li_totdersiniva,2,",","."); 
							$li_siniva=number_format($li_siniva,2,",","."); 
							$li_coniva=number_format($li_coniva,2,",",".");			
							$li_baseimp=number_format($li_baseimp,2,",",".");			
							$li_porimp=number_format($li_porimp,2,",",".");			
							$li_totimp=number_format($li_totimp,2,",",".");							
							$li_ivaret=number_format($li_ivaret,2,",",".");															
							$ls_numsol=$io_report->ds_detalle->data["numsop"][$li_i];									
							$ls_fecsol=$io_report->ds_detalle->data["fecemisol"][$li_i];									
							$ls_numche=$io_report->ds_detalle->data["numdocpag"][$li_i];									
							$ls_fecche=$io_report->ds_detalle->data["fecmov"][$li_i];									
							$la_data[$li_i]=array('numope'=>$ls_numope,'fecfac'=>$ld_fecfac,'numfac'=>$ls_numfac,'numref'=>$ls_numref,
												  'numnotdeb'=>$ls_numnotdeb,'numnotcre'=>$ls_numnotcre,'tiptrans'=>$ls_tiptrans,
												  'numfacafec'=>$ls_numfacafec,'totalconiva'=>$li_coniva,'compsinderiva'=>$li_totdersiniva,
												  'baseimp'=>$li_baseimp,'porimp'=>$li_porimp,'totimp'=>$li_totimp,
												  'ivaret'=>$li_ivaret,'numdoc'=>$ls_numdoc,'totalsiniva'=>$li_siniva);														
						  }			
						  $li_totcuenta=($li_totalconiva-$li_totalivaret);												 																						  
						  $li_totalconiva= number_format($li_totalconiva,2,",","."); 
						  $li_totalsiniva= number_format($li_totalsiniva,2,",","."); 
  						  $li_totalbaseimp= number_format($li_totalbaseimp,2,",","."); 
  						  $li_totalmontoimp= number_format($li_totalmontoimp,2,",","."); 
						  $li_totalivaret= number_format($li_totalivaret,2,",","."); 
						  $li_totcuenta= number_format($li_totcuenta,2,",","."); 
						  uf_print_detalle($la_data,$li_totalconiva,$li_totalsiniva,$li_totalbaseimp,$li_totalmontoimp,
						  				   $li_totalivaret,$li_totcuenta,$io_pdf); 						 						  
							uf_print_cabecera2($ls_agenteret,$ls_rifagenteret,$ls_perfiscal,$ls_codsujret,$ls_nomsujret,$ls_rif,
											  $ls_diragenteret,$ls_numcon,$ls_fecrep,$li_estcmpret,$io_pdf);
						  uf_print_detalle2($la_data,$li_totalconiva,$li_totalsiniva,$li_totalbaseimp,$li_totalmontoimp,
						  				   $li_totalivaret,$li_totcuenta,$io_pdf); 						 						  
						  unset($la_data);							 
					}
					uf_print_piepagina($ls_numsol,$ls_fecsol,$ls_numche,$ls_fecche,$io_pdf); 
				}
				if($li_z<($li_totrow-1))
				{
					$io_pdf->ezNewPage(); 					  
				}		
			}
			if($lb_valido) // Si no ocurrio ningún error
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else  // Si hubo algún error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				print(" close();");
				print("</script>");		
			}
			unset($io_pdf);
		}
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_cxp);
?> 