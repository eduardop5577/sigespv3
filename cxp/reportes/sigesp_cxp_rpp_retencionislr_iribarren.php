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
		// Fecha Creación: 03/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesislr.php",$ls_descripcion);
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
	function uf_print_encabezado($as_agente,$as_nombre,$as_rif,$as_nit,$as_telefono,$as_direccion,$ls_denest,$ls_denmun,$ls_fecpag,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado
		//		   Access: private 
		//	    Arguments: as_agente // Nombre del agente de retención
		//	    		   as_nombre // Nombre del proveedor ó beneficiario
		//	    		   as_rif // Rif del proveedor ó beneficiario
		//	    		   as_nit // nit del proveedor ó beneficiario
		//	    		   as_telefono // Telefono del proveedor ó beneficiario
		//	    		   as_direccion // Dirección del proveedor ó beneficiario
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
		$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
		$ls_licagenteret=$_SESSION["la_empresa"]["numlicemp"];
		
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
		$la_data[2]=array('name'=>$as_agente,'name1'=>$ls_rifagenteret,'name2'=>$ls_diragenteret,'name3'=>'Barquisimeto','name4'=>'Lara','name5'=>'Iribarren');				
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
		$la_data[2]=array('name'=>$as_nombre,'name1'=>$as_rif,'name21'=>$ls_fecpag,'name2'=>$as_direccion,'name3'=>$ls_denest,'name4'=>$ls_denmun,'name5'=>$as_telefono);				
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
	}// end function uf_print_encabezado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado2($as_agente,$as_nombre,$as_rif,$as_nit,$as_telefono,$as_direccion,$ls_denest,$ls_denmun,$ls_fecpag,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado
		//		   Access: private 
		//	    Arguments: as_agente // Nombre del agente de retención
		//	    		   as_nombre // Nombre del proveedor ó beneficiario
		//	    		   as_rif // Rif del proveedor ó beneficiario
		//	    		   as_nit // nit del proveedor ó beneficiario
		//	    		   as_telefono // Telefono del proveedor ó beneficiario
		//	    		   as_direccion // Dirección del proveedor ó beneficiario
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetY(290);
		global $io_pdf;
		$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
		$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
		$ls_licagenteret=$_SESSION["la_empresa"]["numlicemp"];
		
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
		$la_data[2]=array('name'=>$as_agente,'name1'=>$ls_rifagenteret,'name2'=>$ls_diragenteret,'name3'=>'Barquisimeto','name4'=>'Lara','name5'=>'Iribarren');				
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
		$la_data[2]=array('name'=>$as_nombre,'name1'=>$as_rif,'name21'=>$ls_fecpag,'name2'=>$as_direccion,'name3'=>$ls_denest,'name4'=>$ls_denmun,'name5'=>$as_telefono);				
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
	}// end function uf_print_encabezado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($as_numsol,$as_concepto,$as_fechapago,$ad_monto,$ad_monret,$ad_porcentaje,$as_numcon,$ls_cheque,$ls_dended,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: as_numsol // Número de recepción
		//	    		   as_concepto // Concepto de la solicitud
		//	    		   as_fechapago // Fecha de la recepción
		//	    		   ad_monto // monto de la recepción
		//	    		   ad_monret // monto retenido
		//	    		   ad_porcentaje // porcentaje de retención
		//	    		   as_numcon // numero de referencia
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data[1]=array('name'=>'<b>INFORMACION DEL IMPUESTO SOBRE LA RENTA RETENIDO Y ENTERADO</b>');		
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
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		$la_data[1]=array('fecha'=>'<b>Fecha de Pago</b>','numdoc'=>'<b>No Factura</b>','numref'=>'<b>No Control</b>','monto'=>'<b>Base Imponible</b>',
						  'porcentaje'=>'<b>porcentaje</b>','retenido'=>'<b>Imp Retenido</b>','dended'=>'<b>Tipo de Actividad de la Retencion</b>');	
		$la_columna=array('fecha'=>'<b>Fecha de Pago</b>','numdoc'=>'<b>No Factura</b>','numref'=>'<b>No Control</b>','monto'=>'<b>Base Imponible</b>',
						  'porcentaje'=>'<b>porcentaje</b>','retenido'=>'<b>Imp Retenido</b>','dended'=>'<b>Tipo de Actividad de la Retencion</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize' => 7, // Tamaño de Letras
					     'showLines'=>2, // Mostrar Líneas
					     'shaded'=>2, // Sombra entre líneas
					     'shadeCol'=>array(0.9,0.9,0.9),
					     'shadeCol2'=>array(0.9,0.9,0.9),
					     'xOrientation'=>'center', // Orientación de la tabla
					     'colGap'=>1,
					     'width'=>500,
					     'cols'=>array('fecha'=>array('justification'=>'center','width'=>60),
									   'numdoc'=>array('justification'=>'center','width'=>60),
									   'numref'=>array('justification'=>'center','width'=>60),
									   'monto'=>array('justification'=>'center','width'=>90),
									   'porcentaje'=>array('justification'=>'center','width'=>60),
									   'retenido'=>array('justification'=>'center','width'=>80),
									   'dended'=>array('justification'=>'center','width'=>180)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('fecha'=>$as_fechapago,'numdoc'=>$as_numsol,'numref'=>$as_numcon,'monto'=>$ad_monto,
						  'porcentaje'=>$ad_porcentaje,'retenido'=>$ad_monret,'dended'=>$ls_dended);	
		$la_columna=array('fecha'=>'','numdoc'=>'','numref'=>'','monto'=>'',
						  'porcentaje'=>'','retenido'=>'','dended'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					      'fontSize' => 7, // Tamaño de Letras
					      'showLines'=>2, // Mostrar Líneas
					      'shaded'=>0, // Sombra entre líneas
					      'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
					      'colGap'=>1,
						  'width'=>500,
					     'cols'=>array('fecha'=>array('justification'=>'center','width'=>60),
									   'numdoc'=>array('justification'=>'center','width'=>60),
									   'numref'=>array('justification'=>'center','width'=>60),
									   'monto'=>array('justification'=>'center','width'=>90),
									   'porcentaje'=>array('justification'=>'center','width'=>60),
									   'retenido'=>array('justification'=>'center','width'=>80),
									   'dended'=>array('justification'=>'left','width'=>180)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data[0]=array('firma1'=>'');
		$la_data[1]=array('firma1'=>'');
		$la_data[2]=array('firma1'=>'____________________________');
		$la_data[3]=array('firma1'=>'TESORERIA MUNICIPAL');
		$la_columna=array('firma1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'center','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);


	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle2($as_numsol,$as_concepto,$as_fechapago,$ad_monto,$ad_monret,$ad_porcentaje,$as_numcon,$ls_cheque,$ls_dended,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: as_numsol // Número de recepción
		//	    		   as_concepto // Concepto de la solicitud
		//	    		   as_fechapago // Fecha de la recepción
		//	    		   ad_monto // monto de la recepción
		//	    		   ad_monret // monto retenido
		//	    		   ad_porcentaje // porcentaje de retención
		//	    		   as_numcon // numero de referencia
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data[1]=array('name'=>'<b>INFORMACION DEL IMPUESTO SOBRE LA RENTA RETENIDO Y ENTERADO</b>');		
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
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		$la_data[1]=array('fecha'=>'<b>Fecha de Pago</b>','numdoc'=>'<b>No Factura</b>','numref'=>'<b>No Control</b>','monto'=>'<b>Base Imponible</b>',
						  'porcentaje'=>'<b>porcentaje</b>','retenido'=>'<b>Imp Retenido</b>','dended'=>'<b>Tipo de Actividad de la Retencion</b>');	
		$la_columna=array('fecha'=>'<b>Fecha de Pago</b>','numdoc'=>'<b>No Factura</b>','numref'=>'<b>No Control</b>','monto'=>'<b>Base Imponible</b>',
						  'porcentaje'=>'<b>porcentaje</b>','retenido'=>'<b>Imp Retenido</b>','dended'=>'<b>Tipo de Actividad de la Retencion</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize' => 7, // Tamaño de Letras
					     'showLines'=>2, // Mostrar Líneas
					     'shaded'=>2, // Sombra entre líneas
					     'shadeCol'=>array(0.9,0.9,0.9),
					     'shadeCol2'=>array(0.9,0.9,0.9),
					     'xOrientation'=>'center', // Orientación de la tabla
					     'colGap'=>1,
					     'width'=>500,
					     'cols'=>array('fecha'=>array('justification'=>'center','width'=>60),
									   'numdoc'=>array('justification'=>'center','width'=>60),
									   'numref'=>array('justification'=>'center','width'=>60),
									   'monto'=>array('justification'=>'center','width'=>90),
									   'porcentaje'=>array('justification'=>'center','width'=>60),
									   'retenido'=>array('justification'=>'center','width'=>80),
									   'dended'=>array('justification'=>'center','width'=>180)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('fecha'=>$as_fechapago,'numdoc'=>$as_numsol,'numref'=>$as_numcon,'monto'=>$ad_monto,
						  'porcentaje'=>$ad_porcentaje,'retenido'=>$ad_monret,'dended'=>$ls_dended);	
		$la_columna=array('fecha'=>'','numdoc'=>'','numref'=>'','monto'=>'',
						  'porcentaje'=>'','retenido'=>'','dended'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					      'fontSize' => 7, // Tamaño de Letras
					      'showLines'=>2, // Mostrar Líneas
					      'shaded'=>0, // Sombra entre líneas
					      'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
					      'colGap'=>1,
						  'width'=>500,
					     'cols'=>array('fecha'=>array('justification'=>'center','width'=>60),
									   'numdoc'=>array('justification'=>'center','width'=>60),
									   'numref'=>array('justification'=>'center','width'=>60),
									   'monto'=>array('justification'=>'center','width'=>90),
									   'porcentaje'=>array('justification'=>'center','width'=>60),
									   'retenido'=>array('justification'=>'center','width'=>80),
									   'dended'=>array('justification'=>'left','width'=>180)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas2($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data[0]=array('firma1'=>'');
		$la_data[1]=array('firma1'=>'');
		$la_data[2]=array('firma1'=>'____________________________');
		$la_data[3]=array('firma1'=>'TESORERIA MUNICIPAL');
		$la_columna=array('firma1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'center','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);


	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------


	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>COMPROBANTE DE RETENCION DE IMPUESTO SOBRE LA RENTA</b>";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_procedencias=$io_fun_cxp->uf_obtenervalor_get("procedencias","");
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$la_procedencias=explode('<<<',$ls_procedencias);
		$la_comprobantes=explode('<<<',$ls_comprobantes);
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
			$ls_codigoant="";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				uf_print_encabezado_pagina($ls_titulo,$io_pdf);
				$ls_numsol=$la_datos[$li_z];
				$ls_procede=$la_procedencias[$li_z];  
				switch ($ls_procede)
				{
					case "SCBBCH":
						$lb_valido= $io_report->uf_retencionesislr_scb($ls_numsol);  
					break;
					case "INT":
						$lb_valido= $io_report->uf_retencionesislr_int($ls_numsol);
					break;
					default:
						$lb_valido= $io_report->uf_retencionesislr_cxp($ls_numsol);
					break;
				}
				if($lb_valido)
				{
					$li_total=$io_report->DS->getRowCount("numdoc");
					for($li_i=1;($li_i<=$li_total);$li_i++)
					{
						$ls_codpro=$io_report->DS->data["cod_pro"][$li_i];
						$ls_cedbene=$io_report->DS->data["ced_bene"][$li_i];
						if($ls_codpro!="----------")
						{
							$ls_tipproben="P";
						}
						else
						{
							$ls_tipproben="B";
						}
						if($ls_tipproben=="P")
						{
							$ls_codigo=$io_report->DS->data["cod_pro"][$li_i];
							$ls_nombre=$io_report->DS->data["proveedor"][$li_i];
							$ls_telefono=$io_report->DS->data["telpro"][$li_i];
							$ls_direccion=$io_report->DS->data["dirpro"][$li_i];
							$ls_rif=$io_report->DS->data["rifpro"][$li_i];
							$ls_codest=$io_report->DS->data["codestpro"][$li_i];									
							$ls_codmun=$io_report->DS->data["codmunpro"][$li_i];	
						}
						else
						{
							$ls_codigo=$io_report->DS->data["ced_bene"][$li_i];
							$ls_nombre=$io_report->DS->data["beneficiario"][$li_i];
							$ls_telefono=$io_report->DS->data["telbene"][$li_i];
							$ls_direccion=$io_report->DS->data["dirbene"][$li_i];
							$ls_rif=$io_report->DS->data["rifben"][$li_i];
							$ls_codest=$io_report->DS->data["codestben"][$li_i];									
							$ls_codmun=$io_report->DS->data["codmunben"][$li_i];	
						}						 
						$ls_nit=$io_report->DS->data["nit"][$li_i];
						$ls_consol=$io_report->DS->data["consol"][$li_i];
						$ls_numdoc=$io_report->DS->data["numdoc"][$li_i];
						$ls_numref=$io_report->DS->data["numref"][$li_i];
						$ls_numsol=$io_report->DS->data["numsol"][$li_i];
						$ls_cheque=$io_report->DS->data["cheque"][$li_i];
						$ls_dended=$io_report->DS->data["dended"][$li_i];
						$ls_denest=$io_report->uf_select_estado($ls_codest);						
						$ls_denmun=$io_report->uf_select_municipio($ls_codest,$ls_codmun);						
						$ld_fecemidoc=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecemidoc"][$li_i]);
						$li_montotdoc=number_format($io_report->DS->data["montotdoc"][$li_i],2,',','.');  
						$li_monobjret=number_format($io_report->DS->data["monobjret"][$li_i],2,',','.');    
						$li_retenido=number_format($io_report->DS->data["retenido"][$li_i],2,',','.');  
						$li_porcentaje=number_format($io_report->DS->data["porcentaje"][$li_i],2,',','.');
						$ls_fecpag=$io_report->uf_select_fechapagos($ls_numsol);
						$ls_fecpag=$io_funciones->uf_convertirfecmostrar( $ls_fecpag);
						if($ls_codigo!=$ls_codigoant)
						{
							if($li_z>=1)
							{
								uf_print_firmas($io_pdf);
								$io_pdf->ezNewPage();  
							}
							uf_print_encabezado($ls_agente,$ls_nombre,$ls_rif,$ls_nit,$ls_telefono,$ls_direccion,$ls_denest,$ls_denmun,$ls_fecpag,$io_pdf);
							$ls_codigoant=$ls_codigo;
						}
						uf_print_detalle($ls_numdoc,$ls_consol,$ld_fecemidoc,$li_monobjret,$li_retenido,$li_porcentaje,$ls_numref,$ls_cheque,$ls_dended,$io_pdf);
						uf_print_firmas($io_pdf);			  
						uf_print_encabezado_pagina2($ls_titulo,$io_pdf);
						uf_print_encabezado2($ls_agente,$ls_nombre,$ls_rif,$ls_nit,$ls_telefono,$ls_direccion,$ls_denest,$ls_denmun,$ls_fecpag,$io_pdf);
						uf_print_detalle2($ls_numdoc,$ls_consol,$ld_fecemidoc,$li_monobjret,$li_retenido,$li_porcentaje,$ls_numref,$ls_cheque,$ls_dended,$io_pdf);
						uf_print_firmas2($io_pdf);			  
					}
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
	//			print(" close();");
				print("</script>");		
			}
			unset($io_pdf);
		}
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_cxp);
?> 