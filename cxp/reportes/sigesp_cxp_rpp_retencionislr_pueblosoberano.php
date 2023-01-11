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
		// Fecha Creaci�n: 03/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Gener� el Reporte ".$as_titulo;
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
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],20,680,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addText(30,530,12,"<b>Comprobante de Retencion de SERVICIOS VARIOS PERSONAS JURIDICAS</b>"); // Agregar la Fecha
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($as_agente,$as_nombre,$as_rif,$as_nit,$as_telefono,$as_direccion,$as_comprobante,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado
		//		   Access: private 
		//	    Arguments: as_agente // Nombre del agente de retenci�n
		//	    		   as_nombre // Nombre del proveedor � beneficiario
		//	    		   as_rif // Rif del proveedor � beneficiario
		//	    		   as_nit // nit del proveedor � beneficiario
		//	    		   as_telefono // Telefono del proveedor � beneficiario
		//	    		   as_direccion // Direcci�n del proveedor � beneficiario
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle por recepci�n
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
   		$ls_rifagente=$_SESSION["la_empresa"]["rifemp"];
   		$ls_periodo=$_SESSION["la_empresa"]["periodo"];
		
     	$la_data[1]=array('name'=>'<b><i>Nombre o Raz�n Social del Agente de Retenci�n</i></b>','name1'=>'<b><i>RIF del Agente de Retenci�n </i></b>','name2'=>'<b><i>Periodo Fiscal </i></b>','name3'=>'<b><i>Nro Comprobante </i></b>');	
     	$la_data[2]=array('name'=>$as_agente,'name1'=>$ls_rifagente,'name2'=>$ls_periodo,'name3'=>$as_comprobante);	

		$la_columna=array('name'=>'','name1'=>'','name2'=>'','name3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar L�neas
					     'fontSize' => 8,  // Tama�o de Letras
						 'shaded'=>0, // Sombra entre l�neas
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>220),
						 			   'name1'=>array('justification'=>'center','width'=>130),
						 			   'name2'=>array('justification'=>'center','width'=>70),
						 			   'name3'=>array('justification'=>'center','width'=>90))); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'<b><i>Direccion Fiscal del Agente de Retenci�n</i></b>');
		$la_data[2]=array('name'=>$as_direccion);
		$la_data[3]=array('name'=>'');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar L�neas
					     'fontSize' => 8,  // Tama�o de Letras
						 'shaded'=>0, // Sombra entre l�neas
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>510, // Ancho de la tabla
						 'maxWidth'=>510,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>510))); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
     	$la_data[1]=array('name'=>'<b><i>Nombre o Raz�n Social del Sujeto Retenido</i></b>','name1'=>'<b><i>RIF del Sujeto Retenido </i></b>');	
     	$la_data[2]=array('name'=>$as_nombre,'name1'=>$as_rif);	

		$la_columna=array('name'=>'','name1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar L�neas
					     'fontSize' => 8,  // Tama�o de Letras
						 'shaded'=>0, // Sombra entre l�neas
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>380),
						 			   'name1'=>array('justification'=>'center','width'=>130))); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar L�neas
					     'fontSize' => 8,  // Tama�o de Letras
						 'shaded'=>0, // Sombra entre l�neas
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>510, // Ancho de la tabla
						 'maxWidth'=>510,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>510))); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_encabezado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numcon,$ad_fecrep,$as_agenteret,$as_rifagenteret,$as_perfiscal,$as_licagenteret,$as_diragenteret,
							   $as_nomsujret,$as_rif,$as_numlic,$ai_estcmpret,$ls_nit,$io_pdf)
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
		$ls_nitagenteret=$_SESSION["la_empresa"]["nitemp"];
		
		$io_pdf->setStrokeColor(0,0,0);
		if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,495,180,30);		
			$io_pdf->addText(90,505,15,"<b> ANULADO </b>"); 
		}	
		$io_pdf->ezSetY(525);
		$la_data[1]=array('name'=>'<b>NRO COMPROBANTE </b>');
		$la_data[2]=array('name'=>$as_numcon);				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>500, // Orientaci�n de la tabla
						 'width'=>140, // Ancho de la tabla						 
						 'maxWidth'=>140,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>140))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);								 
		$io_pdf->ezSetY(525);
		$la_data[1]=array('name'=>'<b>FECHA</b>');
		$la_data[2]=array('name'=>date("d/m/Y"));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>630, // Orientaci�n de la tabla
						 'width'=>90, // Ancho de la tabla						 
						 'maxWidth'=>90,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>90))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->ezSetY(490);
		$la_data[1]=array('name'=>'<b>NOMBRE � RAZ�N SOCIAL DEL AGENTE DE RETENCI�N</b>');
		$la_data[2]=array('name'=>$as_agenteret);				
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
		$io_pdf->ezSetY(490);
		$la_data[1]=array('name'=>'<b>RIF / NIT AGENTE DE RETENCI�N</b>');
		$la_data[2]=array('name'=>$as_rifagenteret." / ".$ls_nitagenteret);				
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
		$la_data[1]=array('name'=>'<b>DIRECCI�N FISCAL DEL AGENTE DE RETENCI�N</b>');
		$la_data[2]=array('name'=>$as_diragenteret);				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>355, // Orientaci�n de la tabla
						 'width'=>740, // Ancho de la tabla						 
						 'maxWidth'=>740,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>640))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);								 
		$io_pdf->ezSetY(415);
		$la_data[1]=array('name'=>'<b>NOMBRE � RAZ�N SOCIAL DEL SUJETO RETENIDO</b>');
		$la_data[2]=array('name'=>$as_nomsujret);				
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
		$io_pdf->ezSetY(415);
		$la_data[1]=array('name'=>'<b>RIF / NIT SUJETO RETENIDO</b>');
		$la_data[2]=array('name'=>$as_rif." / ".$ls_nit);				
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
	function uf_print_detalle($la_data,$la_data2,$ai_totbasimp,$ai_totmonimp,$as_rifagenteret,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   ai_totbasimp // Total de la base imponible
		//	    		   ai_totmonimp // Total monto imponible
		//	    		   as_rifagenteret // Rif del Agente de Retenci�n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetY(365);
		$la_data1[1]=array('codtipdoc'=>'<b>Tipo</b>',
						  'dentipdoc'=>'<b>Tipo de Documento</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
  						  'dencondoc'=>'<b>Documento</b>',		
						  'fecfac'=>'<b>Fecha</b>',
						  'numref'=>'<b>Referencia</b>',
						  'montotdoc'=>'<b>Monto</b>');
		$la_columna=array('codtipdoc'=>'<b>N�</b>',
						  'dentipdoc'=>'<b>N� Orden de Pago</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
  						  'dencondoc'=>'<b>Num. Ctrol de Factura</b>',		
						  'fecfac'=>'<b>Monto de la Operaci�n</b>',
						  'numref'=>'<b>Al�cuota</b>',
						  'montotdoc'=>'<b>Impuesto Retenido</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>740, // Ancho de la tabla
						 'maxWidth'=>740, // Ancho M�nimo de la tabla
						 'xPos'=>385, // Orientaci�n de la tabla
						 'cols'=>array('codtipdoc'=>array('justification'=>'center','width'=>40), // Justificacion y ancho de la columna
									   'dentipdoc'=>array('justification'=>'center','width'=>160),
						 			   'numfac'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'dencondoc'=>array('justification'=>'center','width'=>180),
						 			   'fecfac'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>80),
   						 			   'montotdoc'=>array('justification'=>'center','width'=>100))); 
		$io_pdf->ezTable($la_data1,$la_columna,'Documentos a Pagar',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_columna=array('codtipdoc'=>'<b>N�</b>',
						  'dentipdoc'=>'<b>N� Orden de Pago</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
  						  'dencondoc'=>'<b>Num. Ctrol de Factura</b>',		
						  'fecfac'=>'<b>Monto de la Operaci�n</b>',
						  'numref'=>'<b>Al�cuota</b>',
						  'montotdoc'=>'<b>Impuesto Retenido</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>2, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>740, // Ancho de la tabla
						 'maxWidth'=>740, // Ancho M�nimo de la tabla
						 'xPos'=>385, // Orientaci�n de la tabla
						 'cols'=>array('codtipdoc'=>array('justification'=>'center','width'=>40), // Justificacion y ancho de la columna
									   'dentipdoc'=>array('justification'=>'center','width'=>160),
						 			   'numfac'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'dencondoc'=>array('justification'=>'center','width'=>180),
						 			   'fecfac'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>80),
   						 			   'montotdoc'=>array('justification'=>'center','width'=>100))); 
		$io_pdf->ezSetDy(-0.5);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);

		$la_columna=array('dended'=>'<b>Retencion</b>',
						  'basimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>%</b>',
						  'iva_ret'=>'<b>Monto Retenido</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>2, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>740, // Ancho de la tabla
						 'maxWidth'=>740, // Ancho M�nimo de la tabla
						 'xPos'=>515, // Orientaci�n de la tabla
						 'cols'=>array('dended'=>array('justification'=>'center','width'=>250), // Justificacion y ancho de la columna
									   'basimp'=>array('justification'=>'center','width'=>80),
						 			   'porimp'=>array('justification'=>'center','width'=>30), // Justificacion y ancho de la columna
   						 			   'iva_ret'=>array('justification'=>'center','width'=>80))); 
		$io_pdf->ezSetDy(-20);
		$io_pdf->ezTable($la_data2,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);




		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_data1[1]=array('firma'=>'_________________________________________________','firma2'=>'_________________________________________________','firma3'=>'_________________________________________________');	
		$la_data1[2]=array('firma'=>'Elaborado Por','firma2'=>'Revisado Por','firma3'=>'Autorizado Por');	
		$la_data1[3]=array('firma'=>'Mary Marquez','firma2'=>'Leyda Herrera','firma3'=>'Miguel Castillo');	
		$la_columna=array('firma'=>'','firma2'=>'','firma3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>200, // Ancho de la tabla
						 'maxWidth'=>200, // Ancho M�nimo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('firma'=>array('justification'=>'center','width'=>250), // Justificacion y ancho de la columna
									   'firma2'=>array('justification'=>'center','width'=>250),
   						 			   'firma3'=>array('justification'=>'center','width'=>250))); 
		$io_pdf->ezSetDy(-50);
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle por recepci�n
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data[0]=array('firma1'=>'','firma2'=>'');
		$la_data[1]=array('firma1'=>'','firma2'=>'');
		$la_data[2]=array('firma1'=>'____________________________','firma2'=>'____________________________');
		$la_data[3]=array('firma1'=>'AGENTE DE RETENCION','firma2'=>'FECHA DE ENTREGA');
		$la_data[4]=array('firma1'=>'','firma2'=>'');
		$la_columna=array('firma1'=>'','firma2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'center','width'=>250), // Justificaci�n y ancho de la columna
						 			   'firma2'=>array('justification'=>'center','width'=>250))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);


		$io_pdf->rectangle(450,60,110,90); 
		$io_pdf->addText(485,66,10,'<b>SELLO</b>');
	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>COMPROBANTE DE RETENCION DE IMPUESTO SOBRE LA RENTA</b>";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
	$ls_licagenteret=$_SESSION["la_empresa"]["numlicemp"];
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
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
			$io_pdf=new Cezpdf('LETTER','landscape');
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(5,4,3,3);
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
					case "GENCMP":
						$lb_valido= $io_report->uf_buscar_comp_islr_gen($ls_numsol);
					break;
					default:
						$lb_valido= $io_report->uf_buscar_comp_islr_gen($ls_numsol);
					break;
				}
				if($lb_valido)
				{
					$li_montotdoc="";
					$ls_dentipdoc="";
					$ls_codtipdoc="";
					$ls_dencondoc="";
					$ls_dended="";
					$arrResultado=$io_report->uf_retencionesunoxmil_detfact($ls_numsol);
					if($arrResultado!="")
					{
						$li_montotdoc=$arrResultado["montotdoc"];
						$ls_dentipdoc=$arrResultado["dentipdoc"];
						$ls_codtipdoc=$arrResultado["codtipdoc"];
						$ls_dencondoc=$arrResultado["dencondoc"];
						$ls_dended=$arrResultado["dended"];
					}
					$li_total=$io_report->DS_ISLR->getRowCount("numcom");
					for($li_i=1;($li_i<=$li_total);$li_i++)
					{
						$ls_codigo=$io_report->DS_ISLR->data["codsujret"][$li_i];
						$ls_nombre=$io_report->DS_ISLR->data["nomsujret"][$li_i];
						$ls_telefono="";
						$ls_direccion=$io_report->DS_ISLR->data["dirsujret"][$li_i];
						$ls_rif=$io_report->DS_ISLR->data["rif"][$li_i];

						$ls_nit=$io_report->DS_ISLR->data["nit"][$li_i];
						$ls_consol="";
						$ls_numdoc=$io_report->DS_ISLR->data["numfac"][$li_i];
						$ls_numref=$io_report->DS_ISLR->data["numcon"][$li_i];
						$ls_numlic=$io_report->DS_ISLR->data["numlic"][$li_i];
						$ld_fecemidoc=$io_funciones->uf_convertirfecmostrar($io_report->DS_ISLR->data["fecfac"][$li_i]);
						$li_montotdoc=number_format($io_report->DS_ISLR->data["totcmp_con_iva"][$li_i],2,',','.');  
						$li_monobjret=number_format($io_report->DS_ISLR->data["basimp"][$li_i],2,',','.');    
						$li_retenido=number_format($io_report->DS_ISLR->data["iva_ret"][$li_i],2,',','.');  
						$li_porcentaje=number_format($io_report->DS_ISLR->data["porimp"][$li_i],2,',','.');
						$ls_perfiscal=substr($ls_numsol,0,4);
						$la_data[$li_i]=array('codtipdoc'=>$ls_codtipdoc,'dentipdoc'=>$ls_dentipdoc, 'numfac'=>$ls_numdoc,'dencondoc'=>$ls_dencondoc,
											  'fecfac'=>$ld_fecemidoc,'numref'=>$ls_numref,'montotdoc'=>$li_montotdoc);
						$la_data2[$li_i]=array('dended'=>$ls_dended,'basimp'=>$li_monobjret, 'porimp'=>$li_porcentaje,'iva_ret'=>$li_retenido);
						if($ls_codigo!=$ls_codigoant)
						{
							if($li_z>=1)
							{
								//uf_print_firmas($io_pdf);
								$io_pdf->ezNewPage();  
							}
							uf_print_cabecera($ls_numsol,$ld_fecemidoc,$ls_agente,$ls_rif,$ls_perfiscal,$ls_licagenteret,
											  $ls_diragenteret,$ls_nombre,$ls_rif,$ls_numlic,"",$ls_nit,$io_pdf);
						//	uf_print_encabezado($ls_agente,$ls_nombre,$ls_rif,$ls_nit,$ls_telefono,$ls_direccion,$ls_numsol,$io_pdf);
							$ls_codigoant=$ls_codigo;
						}
						uf_print_detalle($la_data,$la_data2,"","",$ls_rifagenteret,$io_pdf); 						 						  
						//uf_print_detalle($ls_numdoc,$ls_consol,$ld_fecemidoc,$li_monobjret,$li_retenido,$li_porcentaje,$ls_numref,$li_montotdoc,$io_pdf);
					}
				}	
			}
			//uf_print_firmas($io_pdf);			  
			if($lb_valido) // Si no ocurrio ning�n error
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else  // Si hubo alg�n error
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