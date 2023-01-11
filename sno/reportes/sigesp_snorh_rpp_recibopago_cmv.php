<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
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
	ini_set('memory_limit','256M');
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_desnom,$as_periodo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Arreglo de las variables de seguridad
		//	    		   as_desnom // Arreglo de las variables de seguridad
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_descripcion="Gener� el Reporte Consolidado ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_recibopago.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina1($as_titulo,$as_desnom,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina1
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_desnom // Descripci�n de la n�mina
		//	    		   as_periodo // Descripci�n del per�odo
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
        $io_pdf->addJpegFromFile('../../shared/imagebank/cmv1.jpg',40,720,65,65); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/cmv2.jpg',510,720,65,65); // Agregar Logo
		$ls_encabezado="<b>REP�BLICA BOLIVARIANA DE VENEZUELA</b>";
		$li_tm=$io_pdf->getTextWidth(8,$ls_encabezado);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,775,8,$ls_encabezado); // Agregar el t�tulo
		$ls_encabezado="<b>ESTADO CARABOBO - MUNICIPIO DE VALENCIA</b>";
		$li_tm=$io_pdf->getTextWidth(8,$ls_encabezado);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,766,8,$ls_encabezado); // Agregar el t�tulo
		$ls_encabezado="<b>CONTRALOR�A MUNICIPAL DE VALENCIA</b>";
		$li_tm=$io_pdf->getTextWidth(8,$ls_encabezado);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,757,8,$ls_encabezado); // Agregar el t�tulo
		$ls_encabezado="<b>RIF:G-20004957-0</b>";
		$li_tm=$io_pdf->getTextWidth(8,$ls_encabezado);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,748,8,$ls_encabezado); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,10,$as_titulo); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(8,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,8,$as_periodo); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(8,$as_desnom);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,705,8,$as_desnom); // Agregar el t�tulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina1
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina2($as_titulo,$as_desnom,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_desnom // Descripci�n de la n�mina
		//	    		   as_periodo // Descripci�n del per�odo
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->addJpegFromFile('../../shared/imagebank/cmv1.jpg',40,297,65,65); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/cmv2.jpg',510,297,65,65); // Agregar Logo
		$ls_encabezado="<b>REP�BLICA BOLIVARIANA DE VENEZUELA</b>";
		$li_tm=$io_pdf->getTextWidth(8,$ls_encabezado);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,352,8,$ls_encabezado); // Agregar el t�tulo
		$ls_encabezado="<b>ESTADO CARABOBO - MUNICIPIO DE VALENCIA</b>";
		$li_tm=$io_pdf->getTextWidth(8,$ls_encabezado);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,343,8,$ls_encabezado); // Agregar el t�tulo
		$ls_encabezado="<b>CONTRALOR�A MUNICIPAL DE VALENCIA</b>";
		$li_tm=$io_pdf->getTextWidth(8,$ls_encabezado);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,334,8,$ls_encabezado); // Agregar el t�tulo
		$ls_encabezado="<b>RIF:G-20004957-0</b>";
		$li_tm=$io_pdf->getTextWidth(8,$ls_encabezado);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,325,8,$ls_encabezado); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,310,10,$as_titulo); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(8,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,300,8,$as_periodo); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(8,$as_desnom);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,285,8,$as_desnom); // Agregar el t�tulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera1($as_cedper,$as_nomper,$as_descar,$io_cabecera,$as_fecingper,$as_sueper,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera1
		//		   Access: private 
		//	    Arguments: as_cedper // C�dula del personal
		//	    		   as_nomper // Nombre del personal
		//	    		   as_descar // Decripci�n del cargo
		//	    		   io_cabecera // objeto cabecera
		//	    		   io_pdf // Objeto PDF
		//    Description: funci�n que imprime la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf,$io_cabecera;
		$io_pdf->saveState();
		$io_pdf->addText(40,700,'10','______________________________________________________________________________________________');
		$io_pdf->ezSetDy(-6);
		$la_data=array(array('nombre'=>'Apellidos y Nombres:', 'cedula'=>'C�dula:', 'cargo'=>'Cargo:' ));
		$la_columna=array('nombre'=>'','cedula'=>'','cargo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>220), // Justificaci�n y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificaci�n y ancho de la columna
						 			   'cargo'=>array('justification'=>'left','width'=>260))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$io_pdf->addText(40,689,'10','______________________________________________________________________________________________');
		$la_data=array(array('nombre'=>$as_nomper, 'cedula'=>$as_cedper, 'cargo'=>substr($as_descar,0,100)));
		$la_columna=array('nombre'=>'','cedula'=>'','cargo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'rowGap'=>0.5,
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>220), // Justificaci�n y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificaci�n y ancho de la columna
						 			   'cargo'=>array('justification'=>'left','width'=>260))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$la_data=array(array('nombre'=>'Fecha Ing:  '.$as_fecingper, 'cedula'=>'Sueldo:  '.$as_sueper, 'cargo'=>'Compensaci�n: ' ));
		$la_columna=array('nombre'=>'','cedula'=>'','cargo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>180), // Justificaci�n y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>170), // Justificaci�n y ancho de la columna
						 			   'cargo'=>array('justification'=>'left','width'=>180))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$io_pdf->addText(40,666,'10','______________________________________________________________________________________________');
		$io_pdf->ezSetY(668);
		$la_data=array(array('denomasig'=>'<b>DENOMINACI�N</b>', 'valorasig'=>'<b>ASIGNACI�N</b>', 'denomdedu'=>'<b>DENOMINACI�N</b>','valordedu'=>'<b>DEDUCCI�N</b>','saldo'=>'<b>SALDO</b>'));
		$la_columna=array('denomasig'=>'<b>DENOMINACI�N</b>',
						  'valorasig'=>'<b>ASIGNACI�N</b>',
						  'denomdedu'=>'<b>DENOMINACI�N</b>',
						  'valordedu'=>'<b>DEDUCCI�N</b>',
						  'saldo'=>'<b>SALDO</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('denomasig'=>array('justification'=>'center','width'=>120), // Justificaci�n y ancho de la columna
						 			   'valorasig'=>array('justification'=>'right','width'=>100), // Justificaci�n y ancho de la columna
						 			   'denomdedu'=>array('justification'=>'center','width'=>120), // Justificaci�n y ancho de la columna
						 			   'valordedu'=>array('justification'=>'right','width'=>100),
									   'saldo'=>array('justification'=>'right','width'=>60))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->addText(40,656,'10','______________________________________________________________________________________________');
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera2($as_cedper,$as_nomper,$as_descar,$io_cabecera,$as_fecingper,$as_sueper,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_cedper // C�dula del personal
		//	    		   as_nomper // Nombre del personal
		//	    		   as_descar // Decripci�n del cargo
		//	    		   io_cabecera // objeto cabecera
		//	    		   io_pdf // Objeto PDF
		//    Description: funci�n que imprime la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf,$io_cabecera;
		$io_pdf->saveState();
		$io_pdf->addText(40,280,'10','______________________________________________________________________________________________');
		$io_pdf->ezSetDy(-170);
		$la_data=array(array('nombre'=>'Apellidos y Nombres:', 'cedula'=>'C�dula:', 'cargo'=>'Cargo:' ));
		$la_columna=array('nombre'=>'','cedula'=>'','cargo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>220), // Justificaci�n y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificaci�n y ancho de la columna
						 			   'cargo'=>array('justification'=>'left','width'=>260))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$io_pdf->addText(40,269,'10','______________________________________________________________________________________________');
		$la_data=array(array('nombre'=>$as_nomper, 'cedula'=>$as_cedper, 'cargo'=>substr($as_descar,0,100)));
		$la_columna=array('nombre'=>'','cedula'=>'','cargo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'rowGap'=>0.5,
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>220), // Justificaci�n y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificaci�n y ancho de la columna
						 			   'cargo'=>array('justification'=>'left','width'=>260))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$la_data=array(array('nombre'=>'Fecha Ing:  '.$as_fecingper, 'cedula'=>'Sueldo:  '.$as_sueper, 'cargo'=>'Compensaci�n: ' ));
		$la_columna=array('nombre'=>'','cedula'=>'','cargo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 8, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>180), // Justificaci�n y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>170), // Justificaci�n y ancho de la columna
						 			   'cargo'=>array('justification'=>'left','width'=>180))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$io_pdf->addText(40,246,'10','______________________________________________________________________________________________');
		$io_pdf->ezSetY(248);
		$la_data=array(array('denomasig'=>'<b>DENOMINACI�N</b>', 'valorasig'=>'<b>ASIGNACI�N</b>', 'denomdedu'=>'<b>DENOMINACI�N</b>','valordedu'=>'<b>DEDUCCI�N</b>','saldo'=>'<b>SALDO</b>'));
		$la_columna=array('denomasig'=>'<b>DENOMINACI�N</b>',
						  'valorasig'=>'<b>ASIGNACI�N</b>',
						  'denomdedu'=>'<b>DENOMINACI�N</b>',
						  'valordedu'=>'<b>DEDUCCI�N</b>',
						  'saldo'=>'<b>SALDO</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('denomasig'=>array('justification'=>'center','width'=>120), // Justificaci�n y ancho de la columna
						 			   'valorasig'=>array('justification'=>'right','width'=>100), // Justificaci�n y ancho de la columna
						 			   'denomdedu'=>array('justification'=>'center','width'=>120), // Justificaci�n y ancho de la columna
						 			   'valordedu'=>array('justification'=>'right','width'=>100),
									   'saldo'=>array('justification'=>'right','width'=>60))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->addText(40,236,'10','______________________________________________________________________________________________');
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna=array('denomasig'=>'',
						  'valorasig'=>'',
						  'denomdedu'=>'',
						  'valordedu'=>'',
						  'saldo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('denomasig'=>array('justification'=>'left','width'=>120), // Justificaci�n y ancho de la columna
						 			   'valorasig'=>array('justification'=>'right','width'=>100), // Justificaci�n y ancho de la columna
						 			   'denomdedu'=>array('justification'=>'left','width'=>120), // Justificaci�n y ancho de la columna
						 			   'valordedu'=>array('justification'=>'right','width'=>100),
									   'saldo'=>array('justification'=>'right','width'=>60))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera1($ai_toting,$ai_totded,$ai_totnet,$as_codcueban,$as_moncajaho,$as_moncom,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera1
		//		   Access: private 
		//	    Arguments: ai_toting // Total Ingresos
		//	   			   ai_totded // Total Deducciones
		//	   			   ai_totnet // Total Neto
		//	   			   as_codcueban // Codigo cuenta bancaria
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el fin de la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares,  $ls_tiporeporte;
		
		$io_piepagina=$io_pdf->openObject(); // Creamos el objeto pie de p�gina
		$io_pdf->saveState();
		$io_pdf->addText(200,475,'10','------------------------------------------------------------------------------------------------------------');
		$io_pdf->ezSety(480);
		$la_data=array(array('denomasig'=>'<b>Total Ingresos '.$ls_bolivares.'</b>', 'valorasig'=>$ai_toting, 'denomdedu'=>'<b>Total Deducciones '.$ls_bolivares.'</b>','valordedu'=>$ai_totded));
		$la_columna=array('denomasig'=>'<b>DENOMINACI�N</b>',
						  'valorasig'=>'<b>ASIGNACI�N</b>',
						  'denomdedu'=>'<b>DENOMINACI�N</b>',
						  'valordedu'=>'<b>DEDUCCI�N</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('denomasig'=>array('justification'=>'center','width'=>150), // Justificaci�n y ancho de la columna
						 			   'valorasig'=>array('justification'=>'right','width'=>100), // Justificaci�n y ancho de la columna
						 			   'denomdedu'=>array('justification'=>'center','width'=>150), // Justificaci�n y ancho de la columna
						 			   'valordedu'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->addText(40,462,'10','------------------------------------------------------------------------------------------------------------------------------------------------------------');
		$la_data=array(array('cuenta'=>'<b>Cuenta Bancaria:</b>  '.$as_codcueban, 'neto'=>'<b>Neto a Cobrar '.$ls_bolivares.':</b>  '.$ai_totnet));
		$la_columna=array('cuenta'=>'',
						  'neto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'left','width'=>250), // Justificaci�n y ancho de la columna
						 			   'neto'=>array('justification'=>'right','width'=>250))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->addText(40,448,'10','------------------------------------------------------------------------------------------------------------------------------------------------------------');
		$io_pdf->addText(40,442,'9','Aporte Patronal Caja de Ahorro:  '.$as_moncajaho);
		$io_pdf->addText(465,667,'8',$as_moncom);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_piepagina,'all');
		$io_pdf->stopObject($io_piepagina); // Detener el objeto pie de p�gina
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera2($ai_toting,$ai_totded,$ai_totnet,$as_codcueban,$as_moncajaho,$as_moncom,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera
		//		   Access: private 
		//	    Arguments: ai_toting // Total Ingresos
		//	   			   ai_totded // Total Deducciones
		//	   			   ai_totnet // Total Neto
		//	   			   as_codcueban // Codigo cuenta bancaria
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el fin de la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares,  $ls_tiporeporte;
		
		$io_piepagina=$io_pdf->openObject(); // Creamos el objeto pie de p�gina
		$io_pdf->saveState();
		$io_pdf->addText(200,55,'10','------------------------------------------------------------------------------------------------------------');
		$io_pdf->ezSety(60);
		$la_data=array(array('denomasig'=>'<b>Total Ingresos '.$ls_bolivares.'</b>', 'valorasig'=>$ai_toting, 'denomdedu'=>'<b>Total Deducciones '.$ls_bolivares.'</b>','valordedu'=>$ai_totded));
		$la_columna=array('denomasig'=>'<b>DENOMINACI�N</b>',
						  'valorasig'=>'<b>ASIGNACI�N</b>',
						  'denomdedu'=>'<b>DENOMINACI�N</b>',
						  'valordedu'=>'<b>DEDUCCI�N</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('denomasig'=>array('justification'=>'center','width'=>150), // Justificaci�n y ancho de la columna
						 			   'valorasig'=>array('justification'=>'right','width'=>100), // Justificaci�n y ancho de la columna
						 			   'denomdedu'=>array('justification'=>'center','width'=>150), // Justificaci�n y ancho de la columna
						 			   'valordedu'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->addText(40,42,'10','------------------------------------------------------------------------------------------------------------------------------------------------------------');
		$la_data=array(array('cuenta'=>'<b>Cuenta Bancaria:</b>  '.$as_codcueban, 'neto'=>'<b>Neto a Cobrar '.$ls_bolivares.':</b>  '.$ai_totnet));
		$la_columna=array('cuenta'=>'',
						  'neto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'left','width'=>250), // Justificaci�n y ancho de la columna
						 			   'neto'=>array('justification'=>'right','width'=>250))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->addText(40,28,'10','------------------------------------------------------------------------------------------------------------------------------------------------------------');
		$io_pdf->addText(40,22,'9','Aporte Patronal Caja de Ahorro:  '.$as_moncajaho);
		$io_pdf->addText(465,247,'8',$as_moncom);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_piepagina,'all');
		$io_pdf->stopObject($io_piepagina); // Detener el objeto pie de p�gina
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	$ls_tiporeporte="0";
	$ls_bolivares="";
	if (array_key_exists("tiporeporte",$_GET))
	{
		$ls_tiporeporte=$_GET["tiporeporte"];
	}
	switch($ls_tiporeporte)
	{
		case "0":
			require_once("sigesp_snorh_class_report.php");
			$io_report=new sigesp_snorh_class_report();
			$ls_bolivares ="Bs.";
			break;

		case "1":
			require_once("sigesp_snorh_class_reportbsf.php");
			$io_report=new sigesp_snorh_class_reportbsf();
			$ls_bolivares ="Bs.F.";
			break;
	}			
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codnom=$io_fun_nomina->uf_obtenervalor_get("codnom","");
	$ls_desnom="<b>".$io_fun_nomina->uf_obtenervalor_get("desnom","")."</b>";
	$ls_codperides=$io_fun_nomina->uf_obtenervalor_get("codperides","");
	$ls_codperihas=$io_fun_nomina->uf_obtenervalor_get("codperihas","");
	$ld_fecdesper=$io_fun_nomina->uf_obtenervalor_get("fecdesper","");
	$ld_fechasper=$io_fun_nomina->uf_obtenervalor_get("fechasper","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_coduniadmdes=$io_fun_nomina->uf_obtenervalor_get("coduniadmdes","");
	$ls_coduniadmhas=$io_fun_nomina->uf_obtenervalor_get("coduniadmhas","");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_conceptop2=$io_fun_nomina->uf_obtenervalor_get("conceptop2","");
	$ls_conceptoreporte=$io_fun_nomina->uf_obtenervalor_get("conceptoreporte","");
	$ls_tituloconcepto=$io_fun_nomina->uf_obtenervalor_get("tituloconcepto","");
	$ls_quincena=$io_fun_nomina->uf_obtenervalor_get("quincena","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("codsubnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("codsubnomhas","");
	$ls_consolidar=$io_fun_nomina->uf_obtenervalor_get("consolidar","0");
	$ls_codubifisdes=$io_fun_nomina->uf_obtenervalor_get("codubifisdes","");
	$ls_codubifishas=$io_fun_nomina->uf_obtenervalor_get("codubifishas","");	
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>COMPROBANTE DE PAGO</b>";
	$ls_periodo="";
	if($ls_consolidar=="1")
	{
		$ls_periodo="Periodos: <b>".$ls_codperides." - ".$ls_codperihas."</b> del <b>".$ld_fecdesper."</b> al <b>".$ld_fechasper."</b>";
	}
	$ls_quincena=3;
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_recibopago_personal($ls_codnom,$ld_fecdesper,$ld_fechasper,$ls_codperdes,$ls_codperhas,$ls_coduniadmdes,$ls_coduniadmhas,
													  $ls_conceptocero,$ls_conceptop2,$ls_conceptoreporte,$ls_subnomdes,$ls_subnomhas,
													  $ls_consolidar,$ls_orden,$ls_codubifisdes,$ls_codubifishas); // Cargar el DS con los datos de la cabecera del reporte*/
	}
	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3,1,1,2); // Configuraci�n de los margenes en cent�metros
		$li_totrow=$io_report->rs_data->RecordCount();
		uf_print_encabezado_pagina1($ls_titulo,$ls_desnom,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la p�gina
		uf_print_encabezado_pagina2($ls_titulo,$ls_desnom,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la p�gina
		$li_reg=1;
		$li_i=1;
		while((!$io_report->rs_data->EOF)&&($lb_valido))
		{
			$li_toting=0;
			$li_totded=0;
			$li_totacumemp=0;
			$li_moncajaho=0;
			$li_moncompensacion=0;			
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_cedper=$io_report->rs_data->fields["cedper"];
			$ls_nomper=$io_report->rs_data->fields["apeper"].", ".$io_report->rs_data->fields["nomper"];
			$ls_descar=$io_report->rs_data->fields["descar"];
			$ls_fecha=$io_report->rs_data->fields["fecingper"];
			$ls_dia=substr($ls_fecha,8,2);
			$ls_mes=substr($ls_fecha,5,2);
			$ls_ano=substr($ls_fecha,0,4);
			$ls_fecingper=$ls_dia.'/'.$ls_mes.'/'.$ls_ano;
			$ls_sueper=$io_report->rs_data->fields["sueper"];
			$ls_sueper=$io_fun_nomina->uf_formatonumerico(abs($ls_sueper));
			$ls_codcueban=$io_report->rs_data->fields["codcueban"];
			$li_total=$io_report->rs_data->fields["total"];
			$li_adelanto=$io_report->rs_data->fields["adenom"];
			$li_racnom=$io_report->rs_data->fields["racnom"];
			if($li_racnom==1)
			{
				$ls_descar=$io_report->rs_data->fields["denasicar"];
			}
			$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
			if($li_reg==1)
			{
				uf_print_cabecera1($ls_cedper,$ls_nomper,$ls_descar,$io_cabecera,$ls_fecingper,$ls_sueper,$io_pdf); // Imprimimos la cabecera del registro
			}
			else
			{
				uf_print_cabecera2($ls_cedper,$ls_nomper,$ls_descar,$io_cabecera,$ls_fecingper,$ls_sueper,$io_pdf); // Imprimimos la cabecera del registro
			}
			$ls_codperi="";
			if($ls_consolidar=="0")
			{
				$ls_codperi=$io_report->rs_data->fields["codperi"];
				$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecdesper"]);
				$ld_fechasper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fechasper"]);
				$ls_periodo="Periodo: <b>".$ls_codperi."</b> del <b>".$ld_fecdesper."</b> al <b>".$ld_fechasper."</b>";
				if($li_reg==1)
				{
					$li_tm=$io_pdf->getTextWidth(9,$ls_periodo);
					$tm=306-($li_tm/2);
					$io_pdf->addText($tm,720,9,$ls_periodo); // Agregar el t�tulo
				}
				else
				{
					$li_tm=$io_pdf->getTextWidth(9,$ls_periodo);
					$tm=306-($li_tm/2);
					$io_pdf->addText($tm,300,9,$ls_periodo); // Agregar el t�tulo
				}
			}
			$lb_valido=$io_report->uf_recibopago_conceptopersonal($ls_codnom,$ld_fecdesper,$ld_fechasper,$ls_codper,
																  $ls_conceptocero,$ls_conceptop2,$ls_conceptoreporte,
																  $ls_tituloconcepto,$ls_codperi); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_totrow_det=$io_report->DS_detalle->getRowCount("codconc");
				$li_asig=0;
				$li_dedu=0;
				if($li_adelanto==1)// Utiliza el adelanto de quincena
				{
					switch($ls_quincena)
					{
						case "1": // primera quincena;
							$li_asig=$li_asig+1;
							$ls_codconc="----------";
							$ls_nomcon="ADELANTO 1ra QUINCENA";
							$li_valsal=round($li_total/2,2);
							$li_toting=$li_toting+$li_valsal;
							$li_valsal=$io_fun_nomina->uf_formatonumerico($li_valsal);
							$la_data_a[$li_asig]=array('denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
							break;
							
						case "2": // segunda quincena;
							for($li_s=1;$li_s<=$li_totrow_det;$li_s++) 
							{
								$ls_tipsal=rtrim($io_report->DS_detalle->data["tipsal"][$li_s]);
								if(($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2") || ($ls_tipsal=="R")) // Buscamos las asignaciones
								{
									$li_asig=$li_asig+1;
									$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
									$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
									if ($ls_tipsal!="R")
									{
										$li_toting=$li_toting+abs($io_report->DS_detalle->data["valsal"][$li_s]);
									}
									if(trim($ls_nomcon)==trim("COMPENSACION"))
									{
										$li_moncompensacion=$li_valsal*2;
									}
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
									$la_data_a[$li_asig]=array('denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'saldo'=>'');
								}
								else // Buscamos las deducciones y aportes
								{
									$li_dedu=$li_dedu+1;
									$ls_acuemp='';
									$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
									$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
									$li_totded=$li_totded+abs($io_report->DS_detalle->data["valsal"][$li_s]);
									$ls_monto=$io_report->DS_detalle->data["valsal"][$li_s];
									$ls_prestamo=$io_report->uf_prestamo_personal($ls_codper,$ls_codnom,$ls_codconc,"D");
									if($ls_codconc=='0000000301' || $ls_codconc=='0000000302' || $ls_codconc=='0000000303' || $ls_codconc=='0000000304' || $ls_codconc=='0000000305' || $ls_codconc=='0000000306'){
										if($ls_prestamo!=0){
											$ls_acuemp=$io_fun_nomina->uf_formatonumerico(abs($ls_prestamo));
										}
									}
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($ls_monto));
									if((trim($ls_nomcon)==trim("CAJA DE AHORRO")) OR (trim($ls_nomcon)==trim("CAJA DE AHORRO PENSIONADO")))
									{
										$li_moncajaho=$li_valsal;
									}
									$la_data_d[$li_dedu]=array('denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'saldo'=>$ls_acuemp);
								}
							}
							$li_dedu=$li_dedu+1;
							$ls_codconc="----------";
							$ls_nomcon="ADELANTO 1ra QUINCENA";
							$li_valsal=round($li_total/2,2);
							$li_totded=$li_totded+$li_valsal;
							$li_valsal=$io_fun_nomina->uf_formatonumerico($li_valsal);
							$la_data_d[$li_dedu]=array('denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
							break;
							
						case "3": // Mes Completo;
							for($li_s=1;$li_s<=$li_totrow_det;$li_s++) 
							{
								$ls_tipsal=rtrim($io_report->DS_detalle->data["tipsal"][$li_s]);
								if(($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2") || ($ls_tipsal=="R")) // Buscamos las asignaciones
								{
									$li_asig=$li_asig+1;
									$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
									$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
									if ($ls_tipsal!="R")
									{
										$li_toting=$li_toting+abs($io_report->DS_detalle->data["valsal"][$li_s]);
									}
									if(trim($ls_nomcon)==trim("COMPENSACION"))
									{
										$li_moncompensacion=$li_valsal*2;
									}
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
									$la_data_a[$li_asig]=array('denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'saldo'=>'');
								}
								else // Buscamos las deducciones y aportes
								{
									$li_dedu=$li_dedu+1;
									$ls_acuemp='';
									$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
									$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
									$li_totded=$li_totded+abs($io_report->DS_detalle->data["valsal"][$li_s]);
									$ls_monto=$io_report->DS_detalle->data["valsal"][$li_s];
									$ls_prestamo=$io_report->uf_prestamo_personal($ls_codper,$ls_codnom,$ls_codconc,"D");
									if($ls_codconc=='0000000301' || $ls_codconc=='0000000302' || $ls_codconc=='0000000303' || $ls_codconc=='0000000304' || $ls_codconc=='0000000305' || $ls_codconc=='0000000306'){
										if($ls_prestamo!=0){
											$ls_acuemp=$io_fun_nomina->uf_formatonumerico(abs($ls_prestamo));
										}
									}
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
									if((trim($ls_nomcon)==trim("CAJA DE AHORRO")) OR (trim($ls_nomcon)==trim("CAJA DE AHORRO PENSIONADO")))
									{
										$li_moncajaho=$li_valsal;
									}
									$la_data_d[$li_dedu]=array('denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'saldo'=>$ls_acuemp);
								}
							}
							break;
					}
				}
				else// No utiliza adelanto de quincena
				{
					for($li_s=1;$li_s<=$li_totrow_det;$li_s++) 
					{
						$ls_tipsal=rtrim($io_report->DS_detalle->data["tipsal"][$li_s]);
						if(($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2") || ($ls_tipsal=="R")) // Buscamos las asignaciones
						{
							$li_asig=$li_asig+1;
							$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
							$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
							if ($ls_tipsal!="R")
							{
								$li_toting=$li_toting+abs($io_report->DS_detalle->data["valsal"][$li_s]);
							}	
							if(trim($ls_nomcon)==trim("COMPENSACION"))
							{
								$li_moncompensacion=$li_valsal*2;
							}
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							$la_data_a[$li_asig]=array('denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'saldo'=>'');
						}
						else // Buscamos las deducciones y aportes
						{
							$li_dedu=$li_dedu+1;
							$ls_acuemp='';
							$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
							$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
							$li_totded=$li_totded+abs($io_report->DS_detalle->data["valsal"][$li_s]);
							$ls_monto=$io_report->DS_detalle->data["valsal"][$li_s];
							$ls_prestamo=$io_report->uf_prestamo_personal($ls_codper,$ls_codnom,$ls_codconc,"D");
							if($ls_codconc=='0000000301' || $ls_codconc=='0000000302' || $ls_codconc=='0000000303' || $ls_codconc=='0000000304' || $ls_codconc=='0000000305' || $ls_codconc=='0000000306'){
								if($ls_prestamo!=0){
									$ls_acuemp=$io_fun_nomina->uf_formatonumerico(abs($ls_prestamo));
								}
							}
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							if((trim($ls_nomcon)==trim("CAJA DE AHORRO")) OR (trim($ls_nomcon)==trim("CAJA DE AHORRO PENSIONADO")))
							{
								$li_moncajaho=$li_valsal;
							}
							$la_data_d[$li_dedu]=array('denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'saldo'=>$ls_acuemp);
						}
					}
				}
				if($li_asig<=$li_dedu)
				{
					$li_total=$li_dedu;
				}
				else
				{
					$li_total=$li_asig;
				}
				$ls_num=0;
				for($li_s=1;$li_s<=$li_total;$li_s++) 
				{
					$la_valores["denomasig"]="";
					$la_valores["valorasig"]="";
					$la_valores["denomdedu"]="";
					$la_valores["valordedu"]="";
					if($li_s<=$li_asig)
					{
						$la_valores["denomasig"]=$la_data_a[$li_s]["denominacion"];
						$la_valores["valorasig"]=$la_data_a[$li_s]["valor"];
						$la_valores["saldo"]=$la_data_a[$li_s]["saldo"];
					}
					if($li_s<=$li_dedu)
					{
						$la_valores["denomdedu"]=$la_data_d[$li_s]["denominacion"];
						$la_valores["valordedu"]=$la_data_d[$li_s]["valor"];
						$la_valores["saldo"]=$la_data_d[$li_s]["saldo"];
					}
					$la_data[$li_s]=$la_valores;
				}
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				$li_totnet=$li_toting-$li_totded;
				$li_toting=$io_fun_nomina->uf_formatonumerico($li_toting);
				$li_totded=$io_fun_nomina->uf_formatonumerico($li_totded);
				$li_totnet=$io_fun_nomina->uf_formatonumerico($li_totnet);
				$li_moncompensacion=$io_fun_nomina->uf_formatonumerico($li_moncompensacion);
				if($li_reg==1)
				{
					uf_print_pie_cabecera1($li_toting,$li_totded,$li_totnet,$ls_codcueban,$li_moncajaho,$li_moncompensacion,$io_pdf); // Imprimimos pie de la cabecera
				}
				else
				{
					uf_print_pie_cabecera2($li_toting,$li_totded,$li_totnet,$ls_codcueban,$li_moncajaho,$li_moncompensacion,$io_pdf); // Imprimimos pie de la cabecera
				}
				$io_report->DS_detalle->resetds("codconc");
				unset($la_data_a);
				unset($la_data_d);
				unset($la_data);
				$io_pdf->stopObject($io_cabecera); // Detener el objeto cabecera
				if(($li_i<$li_totrow)&&($li_reg==2))
				{
					$io_pdf->ezNewPage(); // Insertar una nueva p�gina
					$li_reg=1;
				}
				else
				{
					$li_reg=2;
				}
			}
			$li_i++;
			$io_report->rs_data->MoveNext();
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
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 