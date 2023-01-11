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
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 15/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesunoxmil.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],22,719,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_escudo.jpg',527,719,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->setStrokeColor(0,0,0);
		$li_tm=$io_pdf->getTextWidth(9,"<b>Republica Bolivariana de Venezuela</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,760,9,"<b>Republica Bolivariana de Venezuela</b>"); 			
		$li_tm=$io_pdf->getTextWidth(9,"<b>Barquisimeto - Estado Lara</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,750,9,"<b>Barquisimeto - Estado Lara</b>"); 
		$li_tm=$io_pdf->getTextWidth(9,"<b>Alcaldia del Municipio Iribarren</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,740,9,"<b>Alcaldia del Municipio Iribarren</b>"); 
		$li_tm=$io_pdf->getTextWidth(9,"<b>Direccion de Tesoreria</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,730,9,"<b>Direccion de Tesoreria</b>"); 	


		$li_tm=$io_pdf->getTextWidth(10,"<b>".$as_titulo."</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,700,10,"<b>".$as_titulo."</b>"); 
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina2($as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],22,319,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_escudo.jpg',527,319,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->setStrokeColor(0,0,0);
		$li_tm=$io_pdf->getTextWidth(9,"<b>Republica Bolivariana de Venezuela</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,360,9,"<b>Republica Bolivariana de Venezuela</b>"); 			
		$li_tm=$io_pdf->getTextWidth(9,"<b>Barquisimeto - Estado Lara</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,350,9,"<b>Barquisimeto - Estado Lara</b>"); 
		$li_tm=$io_pdf->getTextWidth(9,"<b>Alcaldia del Municipio Iribarren</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,340,9,"<b>Alcaldia del Municipio Iribarren</b>"); 
		$li_tm=$io_pdf->getTextWidth(9,"<b>Direccion de Tesoreria</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,330,9,"<b>Direccion de Tesoreria</b>"); 	


		$li_tm=$io_pdf->getTextWidth(10,"<b>".$as_titulo."</b>");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,300,10,"<b>".$as_titulo."</b>"); 
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numcon,$ad_fecrep,$as_agenteret,$as_rifagenteret,$as_perfiscal,$as_licagenteret,$as_diragenteret,
							   $as_nomsujret,$as_rif,$as_numlic,$ai_estcmpret,$ls_fecpag,$ls_dirsujret,$ls_denest,$ls_denmun,$ls_telpro,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   as_agenteret // agente de Retención
		//	    		   as_rifagenteret // Rif del Agente de Retención
		//	    		   as_perfiscal // Período Fiscal
		//	    		   as_licagenteret // Número de licencia de agente de retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_nomsujret // Nombre del sujeto retenido
		//	    		   as_rif // Rif del sujeto retenido
		//	    		   as_numlic // Número de Licencia del sujeto retenido
		//	    		   ai_estcmpret // Estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetDy(-4);
	 	if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,495,180,30);		
			$io_pdf->addText(90,505,15,"<b> ANULADO </b>"); 
		}	
		$la_data[1]=array('name'=>'<b>ENTIDAD DE CARACTER PUBLICO</b>');		
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'colGap'=>1,
						 'width'=>690, // Ancho de la tabla						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'LEFT','width'=>590))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'<b>NOMBRE</b>','name1'=>'<b>RIF</b>','name2'=>'<b>DIRECCION</b>','name3'=>'<b>CIUDAD</b>','name4'=>'<b>ESTADO</b>','name5'=>'<b>MUNICIPIO</b>');				
		$la_data[2]=array('name'=>$as_agenteret,'name1'=>$as_rifagenteret,'name2'=>$as_diragenteret,'name3'=>'Barquisimeto','name4'=>'Lara','name5'=>'Iribarren');				
		$la_columna=array('name'=>'',
		                  'name1'=>'',
						  'name2'=>'',
						  'name3'=>'',
						  'name4'=>'',
						  'name5'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla	
						 'colGap'=>1,					 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>130),
						               'name1'=>array('justification'=>'center','width'=>70),
						               'name2'=>array('justification'=>'center','width'=>190),
						               'name3'=>array('justification'=>'center','width'=>70),
						               'name4'=>array('justification'=>'center','width'=>60),
						               'name5'=>array('justification'=>'center','width'=>70)));
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('name'=>'<b>AGENTES DE RETENCION</b>');		
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'colGap'=>1,
						 'width'=>690, // Ancho de la tabla						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'LEFT','width'=>590))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		$la_data[1]=array('name'=>'<b>NOMBRE</b>','name1'=>'<b>RIF</b>','name2'=>'<b>DIRECCION</b>','name3'=>'<b>CIUDAD</b>','name4'=>'<b>ESTADO</b>','name5'=>'<b>MUNICIPIO</b>');				
		$la_data[2]=array('name'=>'Jaime E. Lopez M.','name1'=>'V-09545914-1','name2'=>'Calle 4/Carr. 3 y 4 Casa No3-12 Barrio San Francisco','name3'=>'Barquisimeto','name4'=>'Lara','name5'=>'Iribarren');				
		$la_data[3]=array('name'=>'Gloria N. Marin L.','name1'=>'V-04069398-0','name2'=>'Calle 2 Casa No 2-12 Urb. Roca del Valle 1 Agua Viva','name3'=>'Cabudare','name4'=>'Lara','name5'=>'Iribarren');				
		$la_data[4]=array('name'=>'Luis A. Contreras H.','name1'=>'V-03399776-7','name2'=>'Av. Libertador Calle 1 Casa 1-3 Urb La Mendera','name3'=>'Cabudare','name4'=>'Lara','name5'=>'Iribarren');				
		$la_columna=array('name'=>'',
		                  'name1'=>'',
						  'name2'=>'',
						  'name3'=>'',
						  'name4'=>'',
						  'name5'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla	
						 'colGap'=>1,					 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>130),
						               'name1'=>array('justification'=>'center','width'=>70),
						               'name2'=>array('justification'=>'center','width'=>190),
						               'name3'=>array('justification'=>'center','width'=>70),
						               'name4'=>array('justification'=>'center','width'=>60),
						               'name5'=>array('justification'=>'center','width'=>70)));
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		
		$la_data[1]=array('name'=>'<b>BENEFICIARIO</b>');		
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'colGap'=>1,
						 'width'=>690, // Ancho de la tabla						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'LEFT','width'=>590))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		$la_data[1]=array('name'=>'<b>NOMBRE</b>','name1'=>'<b>RIF</b>','name21'=>'<b>FECHA PAGO</b>','name2'=>'<b>DIRECCION</b>','name3'=>'<b>ESTADO</b>','name4'=>'<b>MUNICIPIO</b>','name5'=>'<b>TELEFONO</b>');				
		$la_data[2]=array('name'=>$as_nomsujret,'name1'=>$as_rif,'name21'=>$ls_fecpag,'name2'=>$ls_dirsujret,'name3'=>$ls_denest,'name4'=>$ls_denmun,'name5'=>$ls_telpro);				
		$la_columna=array('name'=>'',
		                  'name1'=>'',
						  'name21'=>'',
						  'name2'=>'',
						  'name3'=>'',
						  'name4'=>'',
						  'name5'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla	
						 'colGap'=>1,					 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>120),
						               'name1'=>array('justification'=>'center','width'=>60),
						               'name21'=>array('justification'=>'center','width'=>50),
						               'name2'=>array('justification'=>'center','width'=>170),
						               'name3'=>array('justification'=>'center','width'=>70),
						               'name4'=>array('justification'=>'center','width'=>60),
						               'name5'=>array('justification'=>'center','width'=>60)));
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
			
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera2($as_numcon,$ad_fecrep,$as_agenteret,$as_rifagenteret,$as_perfiscal,$as_licagenteret,$as_diragenteret,
							   $as_nomsujret,$as_rif,$as_numlic,$ai_estcmpret,$ls_fecpag,$ls_dirsujret,$ls_denest,$ls_denmun,$ls_telpro,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   as_agenteret // agente de Retención
		//	    		   as_rifagenteret // Rif del Agente de Retención
		//	    		   as_perfiscal // Período Fiscal
		//	    		   as_licagenteret // Número de licencia de agente de retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_nomsujret // Nombre del sujeto retenido
		//	    		   as_rif // Rif del sujeto retenido
		//	    		   as_numlic // Número de Licencia del sujeto retenido
		//	    		   ai_estcmpret // Estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetY(290);
	 	if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,495,180,30);		
			$io_pdf->addText(90,505,15,"<b> ANULADO </b>"); 
		}	
		$la_data[1]=array('name'=>'<b>ENTIDAD DE CARACTER PUBLICO</b>');		
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'colGap'=>1,
						 'width'=>690, // Ancho de la tabla						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'LEFT','width'=>590))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'<b>NOMBRE</b>','name1'=>'<b>RIF</b>','name2'=>'<b>DIRECCION</b>','name3'=>'<b>CIUDAD</b>','name4'=>'<b>ESTADO</b>','name5'=>'<b>MUNICIPIO</b>');				
		$la_data[2]=array('name'=>$as_agenteret,'name1'=>$as_rifagenteret,'name2'=>$as_diragenteret,'name3'=>'Barquisimeto','name4'=>'Lara','name5'=>'Iribarren');				
		$la_columna=array('name'=>'',
		                  'name1'=>'',
						  'name2'=>'',
						  'name3'=>'',
						  'name4'=>'',
						  'name5'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla	
						 'colGap'=>1,					 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>130),
						               'name1'=>array('justification'=>'center','width'=>70),
						               'name2'=>array('justification'=>'center','width'=>190),
						               'name3'=>array('justification'=>'center','width'=>70),
						               'name4'=>array('justification'=>'center','width'=>60),
						               'name5'=>array('justification'=>'center','width'=>70)));
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('name'=>'<b>AGENTES DE RETENCION</b>');		
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'colGap'=>1,
						 'width'=>690, // Ancho de la tabla						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'LEFT','width'=>590))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		$la_data[1]=array('name'=>'<b>NOMBRE</b>','name1'=>'<b>RIF</b>','name2'=>'<b>DIRECCION</b>','name3'=>'<b>CIUDAD</b>','name4'=>'<b>ESTADO</b>','name5'=>'<b>MUNICIPIO</b>');				
		$la_data[2]=array('name'=>'Jaime E. Lopez M.','name1'=>'V-09545914-1','name2'=>'Calle 4/Carr. 3 y 4 Casa No3-12 Barrio San Francisco','name3'=>'Barquisimeto','name4'=>'Lara','name5'=>'Iribarren');				
		$la_data[3]=array('name'=>'Gloria N. Marin L.','name1'=>'V-04069398-0','name2'=>'Calle 2 Casa No 2-12 Urb. Roca del Valle 1 Agua Viva','name3'=>'Cabudare','name4'=>'Lara','name5'=>'Iribarren');				
		$la_data[4]=array('name'=>'Luis A. Contreras H.','name1'=>'V-03399776-7','name2'=>'Av. Libertador Calle 1 Casa 1-3 Urb La Mendera','name3'=>'Cabudare','name4'=>'Lara','name5'=>'Iribarren');				
		$la_columna=array('name'=>'',
		                  'name1'=>'',
						  'name2'=>'',
						  'name3'=>'',
						  'name4'=>'',
						  'name5'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla	
						 'colGap'=>1,					 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>130),
						               'name1'=>array('justification'=>'center','width'=>70),
						               'name2'=>array('justification'=>'center','width'=>190),
						               'name3'=>array('justification'=>'center','width'=>70),
						               'name4'=>array('justification'=>'center','width'=>60),
						               'name5'=>array('justification'=>'center','width'=>70)));
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		
		$la_data[1]=array('name'=>'<b>BENEFICIARIO</b>');		
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'colGap'=>1,
						 'width'=>690, // Ancho de la tabla						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'LEFT','width'=>590))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		$la_data[1]=array('name'=>'<b>NOMBRE</b>','name1'=>'<b>RIF</b>','name21'=>'<b>FECHA PAGO</b>','name2'=>'<b>DIRECCION</b>','name3'=>'<b>ESTADO</b>','name4'=>'<b>MUNICIPIO</b>','name5'=>'<b>TELEFONO</b>');				
		$la_data[2]=array('name'=>$as_nomsujret,'name1'=>$as_rif,'name21'=>$ls_fecpag,'name2'=>$ls_dirsujret,'name3'=>$ls_denest,'name4'=>$ls_denmun,'name5'=>$ls_telpro);				
		$la_columna=array('name'=>'',
		                  'name1'=>'',
						  'name21'=>'',
						  'name2'=>'',
						  'name3'=>'',
						  'name4'=>'',
						  'name5'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla	
						 'colGap'=>1,					 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>120),
						               'name1'=>array('justification'=>'center','width'=>60),
						               'name21'=>array('justification'=>'center','width'=>50),
						               'name2'=>array('justification'=>'center','width'=>170),
						               'name3'=>array('justification'=>'center','width'=>70),
						               'name4'=>array('justification'=>'center','width'=>60),
						               'name5'=>array('justification'=>'center','width'=>60)));
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
			
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$ai_totbasimp,$ai_totmonimp,$ai_totmoniva,$as_rifagenteret,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   ai_totbasimp // Total de la base imponible
		//	    		   ai_totmonimp // Total monto imponible
		//                 ai_totmoniva // Total monto iva
		//	    		   as_rifagenteret // Rif del Agente de Retención
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		//     Modificado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_datat[1]=array('name'=>'<b>INFORMACION DEL IMPUESTO DEL 1X1000 RETENIDO Y ENTERADO</b>');		
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'colGap'=>1,
						 'width'=>690, // Ancho de la tabla						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>590))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_datat,$la_columna,'',$la_config);
		unset($la_datat);
		unset($la_columna);
		unset($la_config);
		$la_data1[1]=array('fecfac'=>'<b>Fecha Fact.</b>',
		                  'numero'=>'<b>Doc Pago</b>',
		                  'numfac'=>'<b>Nº Fact</b>',
  						  'numref'=>'<b>Nº Control</b>',		
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>Porcentaje</b>',  
						  'iva_ret'=>'<b>Imp Retenido</b>',
						  'totimp'=>'<b>Tipo de Actividad de la Retencion</b>');
		$la_columna=array('fecfac'=>'<b>Fecha FacT.</b>',
		                  'numero'=>'<b>Doc Pago</b>',
		                  'numfac'=>'<b>Nº Fact</b>',
  						  'numref'=>'<b>Nº Control</b>',		
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>Porcentaje</b>',  
						  'iva_ret'=>'<b>Imp Retenido</b>',
						  'totimp'=>'<b>Tipo de Actividad de la Retencion</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>690, // Ancho de la tabla
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'colGap'=>1,
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>50),
						 			   'numero'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>50), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>50), // Justificacion y ancho de la columna
									   'baseimp'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'porimp'=>array('justification'=>'center','width'=>60),
						 			   'iva_ret'=>array('justification'=>'center','width'=>50),
   						 			   'totimp'=>array('justification'=>'center','width'=>210))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$la_columna=array('fecfac'=>'<b>Fecha FacT.</b>',
		                  'numero'=>'<b>Doc Pago</b>',
		                  'numfac'=>'<b>Nº Fact</b>',
  						  'numref'=>'<b>Nº Control</b>',		
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>Porcentaje</b>',  
						  'iva_ret'=>'<b>Imp Retenido</b>',
						  'totimp'=>'<b>Tipo de Actividad de la Retencion</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>690, // Ancho de la tabla
						 'colGap'=>1,
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>50),
						 			   'numero'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>50), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>50), // Justificacion y ancho de la columna
									   'baseimp'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'porimp'=>array('justification'=>'center','width'=>60),
						 			   'iva_ret'=>array('justification'=>'center','width'=>50),
   						 			   'totimp'=>array('justification'=>'center','width'=>210))); 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);


		$la_data1[1]=array('total'=>'<b>Total Monto Retenido:</b>',
		                   'monto'=>'<b>'.$ai_totbasimp.'</b>',
						   'ret'=>'',		
		                   'iva'=>'<b>'.$ai_totmoniva.'</b>',
						   'imponible'=>'');
		$la_columna=array('total'=>'',
		                  'monto'=>'',
						  'ret'=>'',		
		                  'iva'=>'',
						  'imponible'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>690, // Ancho de la tabla
						 'colGap'=>1,
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'cols'=>array('total'=>array('justification'=>'center','width'=>210), // Justificacion y ancho de la columna
   						 			   'monto'=>array('justification'=>'right','width'=>60),
									   'ret'=>array('justification'=>'right','width'=>60),
									   'iva'=>array('justification'=>'right','width'=>50),
   						 			   'imponible'=>array('justification'=>'right','width'=>210))); 
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
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Jennifer Rivero
		//     Modificado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 13/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		 $la_data2[1]=array('name1'=>'');	
		 $la_data2[2]=array('name1'=>'');	
		 $la_data2[3]=array('name1'=>'<b>____________________________</b>');	
		 $la_data2[4]=array('name1'=>'<b>TESORERIA MUNICIPAL</b>');	
        $la_columna=array('name1'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 7, // Tamaño de Letras
						  'showLines'=>0, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>440))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data2,$la_columna,'',$la_config); 		
			    
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle2($la_data,$ai_totbasimp,$ai_totmonimp,$ai_totmoniva,$as_rifagenteret,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   ai_totbasimp // Total de la base imponible
		//	    		   ai_totmonimp // Total monto imponible
		//                 ai_totmoniva // Total monto iva
		//	    		   as_rifagenteret // Rif del Agente de Retención
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		//     Modificado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_datat[1]=array('name'=>'<b>INFORMACION DEL IMPUESTO DEL 1X1000 RETENIDO Y ENTERADO</b>');		
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'colGap'=>1,
						 'width'=>690, // Ancho de la tabla						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>590))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_datat,$la_columna,'',$la_config);
		unset($la_datat);
		unset($la_columna);
		unset($la_config);
		$la_data1[1]=array('fecfac'=>'<b>Fecha Fact.</b>',
		                  'numero'=>'<b>Doc Pago</b>',
		                  'numfac'=>'<b>Nº Fact</b>',
  						  'numref'=>'<b>Nº Control</b>',		
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>Porcentaje</b>',  
						  'iva_ret'=>'<b>Imp Retenido</b>',
						  'totimp'=>'<b>Tipo de Actividad de la Retencion</b>');
		$la_columna=array('fecfac'=>'<b>Fecha FacT.</b>',
		                  'numero'=>'<b>Doc Pago</b>',
		                  'numfac'=>'<b>Nº Fact</b>',
  						  'numref'=>'<b>Nº Control</b>',		
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>Porcentaje</b>',  
						  'iva_ret'=>'<b>Imp Retenido</b>',
						  'totimp'=>'<b>Tipo de Actividad de la Retencion</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>690, // Ancho de la tabla
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'colGap'=>1,
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>50),
						 			   'numero'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>50), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>50), // Justificacion y ancho de la columna
									   'baseimp'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'porimp'=>array('justification'=>'center','width'=>60),
						 			   'iva_ret'=>array('justification'=>'center','width'=>50),
   						 			   'totimp'=>array('justification'=>'center','width'=>210))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$la_columna=array('fecfac'=>'<b>Fecha FacT.</b>',
		                  'numero'=>'<b>Doc Pago</b>',
		                  'numfac'=>'<b>Nº Fact</b>',
  						  'numref'=>'<b>Nº Control</b>',		
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>Porcentaje</b>',  
						  'iva_ret'=>'<b>Imp Retenido</b>',
						  'totimp'=>'<b>Tipo de Actividad de la Retencion</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>690, // Ancho de la tabla
						 'colGap'=>1,
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>50),
						 			   'numero'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>50), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>50), // Justificacion y ancho de la columna
									   'baseimp'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'porimp'=>array('justification'=>'center','width'=>60),
						 			   'iva_ret'=>array('justification'=>'center','width'=>50),
   						 			   'totimp'=>array('justification'=>'center','width'=>210))); 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);


		$la_data1[1]=array('total'=>'<b>Total Monto Retenido:</b>',
		                   'monto'=>'<b>'.$ai_totbasimp.'</b>',
						   'ret'=>'',		
		                   'iva'=>'<b>'.$ai_totmoniva.'</b>',
						   'imponible'=>'');
		$la_columna=array('total'=>'',
		                  'monto'=>'',
						  'ret'=>'',		
		                  'iva'=>'',
						  'imponible'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>690, // Ancho de la tabla
						 'colGap'=>1,
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'cols'=>array('total'=>array('justification'=>'center','width'=>210), // Justificacion y ancho de la columna
   						 			   'monto'=>array('justification'=>'right','width'=>60),
									   'ret'=>array('justification'=>'right','width'=>60),
									   'iva'=>array('justification'=>'right','width'=>50),
   						 			   'imponible'=>array('justification'=>'right','width'=>210))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle

	function uf_print_sello2($io_pdf)
	{
	    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_sello
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Jennifer Rivero
		//     Modificado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 13/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		 $la_data2[1]=array('name1'=>'');	
		 $la_data2[2]=array('name1'=>'');	
		 $la_data2[3]=array('name1'=>'<b>____________________________</b>');	
		 $la_data2[4]=array('name1'=>'<b>TESORERIA MUNICIPAL</b>');	
        $la_columna=array('name1'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 7, // Tamaño de Letras
						  'showLines'=>0, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>440))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data2,$la_columna,'',$la_config); 		
			    
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
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo= "COMPROBANTE DE 1 x 1000";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
    $ls_codemp=$_SESSION["la_empresa"]["codemp"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
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
			$io_pdf = new Cezpdf("LETTER","portrait");
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(3.5,1,3,3);
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
						$ls_codest=$io_report->DS->data["codest"][$li_i];									
						$ls_codmun=$io_report->DS->data["codmun"][$li_i];	
						$ls_telpro=$io_report->DS->data["telpro"][$li_i];	
						$ls_denest=$io_report->uf_select_estado($ls_codest);						
						$ls_denmun=$io_report->uf_select_municipio($ls_codest,$ls_codmun);						
						if ($ls_numcom!=$ls_numcomant)
					   {
					    if ($li_z>=1)
						   {
							 $io_pdf->ezNewPage();  
						   }
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
							$li_montotdoc=$io_report->uf_retenciones1x1000_monfact($ls_numcon);
							$ls_numsop=$io_report->ds_detalle->data["numsop"][$li_i];					
							$ld_fecfac=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecfac"][$li_i]);	
							$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];	
							$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];	              
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];
							$li_iva_ret=$io_report->ds_detalle->data["iva_ret"][$li_i];	
							$li_porimp=$io_report->ds_detalle->data["porimp"][$li_i];	
							$li_totimp=$io_report->ds_detalle->data["totimp"][$li_i];	

							$li_totalbaseimp=$li_totalbaseimp + $li_baseimp ;	
							$li_totalmontoimp=$li_totalmontoimp + $li_totimp;
							$li_totmontotdoc=$li_totmontotdoc+$li_montotdoc;
							$li_totmontoiva=$li_totmontoiva+$li_iva_ret;
							$li_iva_ret=number_format($li_iva_ret,2,",",".");	
							$li_baseimp=number_format($li_baseimp,2,",",".");			
							$li_porimp=number_format($li_porimp,4,",",".");			
							$li_totimp=number_format($li_totimp,2,",",".");							
							$li_montotdoc=number_format($li_montotdoc,2,",",".");							
							$arrResultado=$io_report->uf_select_datos_cheque_retencion($ls_numsop,"","","");
							$numdocpag=$arrResultado["as_nummov"];
							$ls_dended=$io_report->uf_select_det_deducciones_1x1000_solpag($ls_numsop);					
							
							$la_data[$li_i]=array('numero'=>$numdocpag,'fecfac'=>$ld_fecfac,'numfac'=>$ls_numfac,
												  'numref'=>$ls_numref,'baseimp'=>$li_baseimp,'iva_ret'=>$li_iva_ret,'porimp'=>'1 x 1000','totimp'=>$ls_dended,'numsop'=>$ls_numsop, );														
												  
						  }																		 																						  
  						  $li_totalbaseimp= number_format($li_totalbaseimp,2,",","."); 
  						  $li_totalmontoimp= number_format($li_totmontotdoc,2,",","."); 
						  $li_totmontoiva= number_format($li_totmontoiva,2,",","."); 
						  $ls_fecpag=$io_report->uf_select_fechapagos($ls_numsop);
						  $ls_fecpag=$io_funciones->uf_convertirfecmostrar( $ls_fecpag);
					     uf_print_cabecera($ls_numcon,$ls_fecrep,$ls_agenteret,$ls_rifagenteret,$ls_perfiscal,$ls_licagenteret,
									  $ls_diragenteret,$ls_nomsujret,$ls_rif,$ls_numlic,$li_estcmpret,$ls_fecpag,$ls_dirsujret,$ls_denest,$ls_denmun,$ls_telpro,$io_pdf);
						  uf_print_detalle($la_data,$li_totalbaseimp,$li_totalmontoimp,$li_totmontoiva,$ls_rifagenteret,$io_pdf);
						  uf_print_sello($io_pdf);
						  uf_print_encabezado_pagina2($ls_titulo,$io_pdf);
					     uf_print_cabecera2($ls_numcon,$ls_fecrep,$ls_agenteret,$ls_rifagenteret,$ls_perfiscal,$ls_licagenteret,
									  $ls_diragenteret,$ls_nomsujret,$ls_rif,$ls_numlic,$li_estcmpret,$ls_fecpag,$ls_dirsujret,$ls_denest,$ls_denmun,$ls_telpro,$io_pdf);
						  uf_print_detalle2($la_data,$li_totalbaseimp,$li_totalmontoimp,$li_totmontoiva,$ls_rifagenteret,$io_pdf);
						  uf_print_sello2($io_pdf);
						  unset($la_data);							 
						  
					}
				}
				$io_report->DS->reset_ds();
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