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
	function uf_insert_seguridad($as_titulo,$as_desnom,$as_periodo,$ai_tipo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Arreglo de las variables de seguridad
		//	    		   as_desnom // Arreglo de las variables de seguridad
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Gener� el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_recibopago.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hrecibopago.php",$ls_descripcion,$ls_codnom);
		}
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina1($as_desnom,$as_periodo,$io_pdf)
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
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$as_titulo1="<b>INSTITUCION DE PREVISION SOCIAL</b>";
		$li_tm=$io_pdf->getTextWidth(8,$as_titulo1);		
		$io_pdf->addText(150,750,8,$as_titulo1); // Agregar el t�tulo
		$as_titulo2="<b>DE LAS</b>";
		$li_tm=$io_pdf->getTextWidth(8,$as_titulo2);		
		$io_pdf->addText(200,740,8,$as_titulo2); // Agregar el t�tulo
		$as_titulo3="<b>FUERZAS ARMADAS</b>";
		$li_tm=$io_pdf->getTextWidth(8,$as_titulo3);		
		$io_pdf->addText(170,730,8,$as_titulo3); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(9,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,710,9,$as_periodo); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(9,$as_desnom);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,695,9,"<b>".$as_desnom."</b>"); // Agregar el t�tulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina1
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_cabecera1($io_cabecera,$as_cuenta,$as_banco,$as_forma,
	                            $as_cedben, $as_nomben, $as_apeben,$ls_parentesco,$io_pdf)
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
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf,$io_cabecera;
		$io_pdf->saveState();		
		$io_pdf->ezSetDy(-35);		
		$as_cedben=number_format($as_cedben,0,",",".");		
		$la_data[1]=array('nombre'=>'<b> PENSIONADO: </b>'.$as_apeben." ".$as_nomben,'cedula'=>'<b>CI. </b>'.$as_cedben,
		                  'parentesco'=>'<b>PARENTESCO CON EL CAUSANTE: </b>'.$ls_parentesco);					
		$la_columna=array('nombre'=>'','cedula'=>'','parentesco'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7.5, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'xPos'=>'308',
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>290),
						 			   'cedula'=>array('justification'=>'left','width'=>60),
									   'parentesco'=>array('justification'=>'left','width'=>200))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	   
		///--------------------------------------------------------------------------------------------------------------
		$la_data_banco[1]=array('banco1'=>'<b>FORMA DE PAGO: </b>','banco2'=>$as_forma);	
		$la_data_banco[2]=array('banco1'=>'<b>BANCO: </b>','banco2'=>$as_banco);	
		$la_data_banco[3]=array('banco1'=>'<b>CUENTA NRO.</b>','banco2'=>$as_cuenta);				
		$la_columna=array('banco1'=>'','banco2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7.5, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'xPos'=>'150',
						 'cols'=>array('banco1'=>array('justification'=>'left','width'=>79),
						 			   'banco2'=>array('justification'=>'left','width'=>150))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data_banco,$la_columna,'',$la_config);		
		//----------------------------------------------------------------------------------------------------------------
		
		
		$io_pdf->addText(40,461,'10','______________________________________________________________________________________________');
		$io_pdf->ezSetY(460);
		$la_data=array(array('codcon'=>'<b>CODIGO</b>','denomasig'=>'<b>DESCRIPCION DEL CONCEPTO</b>','cuota'=>'<b>CUOTA / PLAZO</b>', 'valorasig'=>'<b>ASIGNACIONES</b>','valordedu'=>'<b>DEDUCCIONES</b>'));
		$la_columna=array('codcon'=>'<b>CODIGO</b>',
						  'denomasig'=>'<b>DESCRIPCION DEL CONCEPTO</b>',
						  'cuota'=>'<b>CUOTA / PLAZO</b>',
						  'valorasig'=>'<b>ASIGNACI�N</b>',						
						  'valordedu'=>'<b>DEDUCCI�N</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('codcon'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'denomasig'=>array('justification'=>'center','width'=>140), // Justificaci�n y ancho de la columna
									   'cuota'=>array('justification'=>'center','width'=>80), // Justificaci�n y ancho de la columna
						 			   'valorasig'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la columna						 			   
						 			   'valordedu'=>array('justification'=>'right','width'=>90))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->addText(40,445,'10','______________________________________________________________________________________________');
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera

   //--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data_a,$la_data_d,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSety(440);
		$la_columna=array('codcon'=>'',
						  'denomasig'=>'',
						  'cuota'=>'',
						  'valorasig'=>'',						  
						  'valor'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('codcon'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'denomasig'=>array('justification'=>'left','width'=>140), // Justificaci�n y ancho de la columna
									   'cuota'=>array('justification'=>'center','width'=>80), // Justificaci�n y ancho de la columna
						 			   'valorasig'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la columna						 			   
						 			   'valor'=>array('justification'=>'right','width'=>90))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data_a,$la_columna,'',$la_config);		
		$la_columna=array('codcon'=>'',
						  'denomdedu'=>'',
						  'cuota'=>'',
						  'valor'=>'',						  
						  'valordedu'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						'cols'=>array('codcon'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'denomdedu'=>array('justification'=>'left','width'=>140), // Justificaci�n y ancho de la columna
									   'cuota'=>array('justification'=>'center','width'=>80), // Justificaci�n y ancho de la columna
						 			   'valor'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la columna						 			   
						 			   'valordedu'=>array('justification'=>'right','width'=>90))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data_d,$la_columna,'',$la_config);
	}// end function uf_print_detalle

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera1($ai_toting,$ai_totded,$ai_totnet,$as_codcueban,$io_pdf)
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
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares,  $ls_tiporeporte;
		
		$io_piepagina=$io_pdf->openObject(); // Creamos el objeto pie de p�gina
		$io_pdf->saveState();
		
		$io_pdf->ezSety(300);
		$la_data=array(array('valor'=>'<b>TOTALES:</b>    ','valorasig'=>$ai_toting,'valordedu'=>$ai_totded));
		$la_columna=array('valor'=>'<b>Columna</b>',
		                  'valorasig'=>'<b>ASIGNACI�N</b>',						 
						  'valordedu'=>'<b>DEDUCCI�N</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('valor'=>array('justification'=>'right','width'=>290),
						               'valorasig'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la columna
						 			   'valordedu'=>array('justification'=>'right','width'=>90))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		
		$la_data=array(array('cuenta'=>'', 'neto'=>'<b>Neto a Cobrar '.$ls_bolivares.'</b>  '.$ai_totnet));
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
						 'cols'=>array('cuenta'=>array('justification'=>'left','width'=>220), // Justificaci�n y ancho de la columna
						 			   'neto'=>array('justification'=>'right','width'=>250))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->addText(40,300,'10','------------------------------------------------------------------------------------------------------------------------------------------------------------');
		$io_pdf->addText(40,302,'10','------------------------------------------------------------------------------------------------------------------------------------------------------------');	 
		$io_pdf->ezSety(250);
		$ld_informa=$_SESSION["la_nomina"]["informa"];
		$la_informa[1]=array('informa'=>'<b>                                                        	                        EL IPSFA INFORMA </b>');
		$la_informa[2]=array('informa'=>'');
		$la_informa[3]=array('informa'=>strtoupper($ld_informa));
		$la_columna=array('informa'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'xPos'=>315,
						 'cols'=>array('informa'=>array('justification'=>'left','width'=>540))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_informa,$la_columna,'',$la_config);
		  
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_piepagina,'all');
		$io_pdf->stopObject($io_piepagina); // Detener el objeto pie de p�gina
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
 
	function uf_detalle_nomina_oficial($sueldo,$prima1,$prima2,$prima3,$prima4,$prima5,$prima6,$porcentaje,$subtotal,
	                                   $as_porcentajeben,$as_feleypen, $as_nomper,$as_cedper,$as_ano,
									   $as_comp, $as_rango, $as_categoria, $io_pdf)
	{
	    //-------------------------------------------------------------------------------------------------------------------------------------     
		global $io_pdf;
		$io_pdf->ezSety(568);
		$io_pdf->setColor(0,0,0);		
		$la_data_titulo[1]=array('titulo'=>'<b><c:uline>NOMINA DE:</c:uline>  '.strtoupper($as_categoria).' </b>');
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'xPos'=>'285',
						 'cols'=>array('cuenta'=>array('justification'=>'left','width'=>560))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data_titulo,$la_columna,'',$la_config);
//---------------------------------------------------------------------------------------------------------------------------------------
		
				
		$ls_subtotal2=0;
		$ls_subtotal3=0;
		$ls_totalbene=0;
		$ls_subtotal2= $sueldo + $prima1 + $prima2 + $prima3 + $prima4 + $prima5 + $prima6;
		$ls_subtotal3= $ls_subtotal2*($porcentaje/100);
		$ls_totalbene= $ls_subtotal3*($as_porcentajeben/100);
	    $la_data1[1]=array('sueldo'=>'<b>S. BASICO: </b>'.number_format($sueldo,2,",","."),
		                   'prima1'=>'<b>P. CHOF/T: </b>'.number_format($prima1,2,",","."));
		$la_columna=array('sueldo'=>'','prima1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array(  'sueldo'=>array('justification'=>'left','width'=>300.3),
										 'prima1'=>array('justification'=>'left','width'=>180.3))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		
		$la_data2[1]=array('prima2'=>'<b>    P. A�OS SVC </b>:'.number_format($prima2,2,",","."),
		                   'prima3'=>'<b>    P. DESCEND.</b>: '.number_format($prima3,2,",","."));
		$la_columna=array('prima2'=>'','prima3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'xPos'=>'281',
						 'cols'=>array(  'prima2'=>array('justification'=>'left','width'=>300.3),
										 'prima3'=>array('justification'=>'left','width'=>180.3))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data2,$la_columna,'',$la_config);
			
		$la_data3[1]=array('prima4'=>'<b>P. NO ASCENSO: </b>'.number_format($prima4,2,",","."),
						   'prima5'=>'<b>P. ESPECIAL :</b>'.number_format($prima5,2,",","."));
		$la_columna=array('prima4'=>'','prima5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array(  'prima4'=>array('justification'=>'left','width'=>300.3),
						 				 'prima5'=>array('justification'=>'left','width'=>180.3))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data3,$la_columna,'',$la_config);
			
		
		$la_data6[1]=array('prima6'=>'<b>P. PROFESION.:</b> '.number_format($prima6,2,",","."),
		                   'prima7'=>'<b>PENS. RET: </b>'.number_format($ls_subtotal2,2,",",".").'<b> X (</b>'.number_format($porcentaje,2,",",".").' % + 0%)');
		$la_columna=array('prima6'=>'','prima7'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('prima6'=>array('justification'=>'left','width'=>300.3),
									   'prima7'=>array('justification'=>'left','width'=>180.3))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data6,$la_columna,'',$la_config);	
		
		$la_data5[1]=array('subtotal'=>'<b>PENS. SOB: </b>'.number_format($ls_subtotal3,2,",",".").'<b> X (</b>'.$as_porcentajeben.' %)',
		                   'total'=>'<b>TOTAL: </b>'.number_format($ls_subtotal3,2,",","."));
		$la_columna=array('subtotal'=>'','total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'xPos'=>'300',
						 'cols'=>array('subtotal'=>array('justification'=>'left','width'=>300.3)),
						 			   'total'=>array('justification'=>'left','width'=>180.3)); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data5,$la_columna,'',$la_config);
		$ls_porbene=$subtotal*(($as_porcentajeben)/100);
		$ls_porbene=number_format($ls_porbene,2,",",".");	
		$la_data6[1]=array('bene'=>'','totalbene'=>'<b>TOTAL PENSIONADO. </b>'.number_format($ls_totalbene,2,",","."));
		$la_columna=array('bene'=>'', 'totalbene'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'xPos'=>'290',
						 'cols'=>array('bene'=>array('justification'=>'left','width'=>300.3),
						               'totalbene'=>array('justification'=>'left','width'=>180.3))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data6,$la_columna,'',$la_config);	
		
		$io_pdf->ezSety(620);
		$as_cedper=number_format($as_cedper,0,",",".");		
		$la_data[1]=array('nombre'=>'<b>CAUSANTE: </b>'.$as_nomper,'cedula'=>'<b>CI. </b>'.$as_cedper);				
		$la_columna=array('nombre'=>'','cedula'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7.5, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'xPos'=>'230',
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>290),
						 			   'cedula'=>array('justification'=>'left','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		
		 $la_data_c[1]=array('servicio'=>'<b>TIEMPO DE SERVICIO: </b>'.$as_ano.'<b> A�OS</b>',
		                     'comp'=>'<b>COMPONENTE: </b>'.$as_comp,
						     'rango'=>'<b>RANGO: </b>'.$as_rango);		
		 $la_columna=array('servicio'=>'',
		                   'comp'=>'',
						   'rango'=>'');
		 $la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7.7, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('servicio'=>array('justification'=>'left','width'=>160),
						                'comp'=>array('justification'=>'left','width'=>200),
									   'rango'=>array('justification'=>'left','width'=>200))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data_c,$la_columna,'',$la_config);		
		$la_data4[1]=array('porcentaje'=>'<b>PORCEN. PENS </b>:'.$porcentaje.' %');
		$la_columna=array('porcentaje'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('porcentaje'=>array('justification'=>'left','width'=>514))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data4,$la_columna,'',$la_config);	
		
		$la_data5[1]=array('ley'=>'<b>PENSION. LOSSFAN ('.$as_feleypen.')</b>');
		$la_columna=array('ley'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7.5, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('ley'=>array('justification'=>'left','width'=>514))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data5,$la_columna,'',$la_config);		
	}
	
//--------------------------------------------------------------------------------------------------------------------------

      function calcular_anos_servicioas($fecha_ingreso,$fecha_egreso)
	  {  
		  $c = date("Y",$fecha_ingreso);	   
		  $b = date("m",$fecha_ingreso);	  
		  $a = date("d",$fecha_ingreso); 	  
		  $anos = date("Y",$fecha_egreso)-$c; 
	   
			  if(date("m",$fecha_egreso)-$b > 0){
		  
			  }elseif(date("m",$fecha_egreso)-$b == 0){
		 
			  if(date("d",$fecha_egreso)-$a <= 0)
			  {		  
			     $anos = $anos-1;	  
			  }
		  
			  }else{		  
			         $anos = $anos-1;		  
			       }  
		  return $anos;	 
      }
//--------------------------------------------------------------------------------------------------------------------------------------
    function uf_print_autorizado($la_cedaut,$la_nomaut,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_autorizado
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSety(270);
		$la_data[1]=array('cedaut'=>'<b>CEDULA DEL AUTORIZADO: </b>'.$la_cedaut,
		                  'nomaut'=>'<b>NOMBRE DEL AUTORIZADO: </b>'.$la_nomaut);
		$la_columna=array('cedaut'=>'',
						  'nomaut'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('cedaut'=>array('justification'=>'left','width'=>200), // Justificaci�n y ancho de la columna
						               'nomaut'=>array('justification'=>'left','width'=>250))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);			
	}// end uf_print_autorizado
	
//-------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	$ls_tiporeporte="0";
	$ls_bolivares ="Bs.";
	if($_SESSION["la_nomina"]["tiponomina"]=="NORMAL")
	{
		require_once("sigesp_sno_class_report.php");
		$io_report=new sigesp_sno_class_report();
		$li_tipo=1;
	}
	if($_SESSION["la_nomina"]["tiponomina"]=="HISTORICA")
	{
		require_once("sigesp_sno_class_report_historico.php");
		$io_report=new sigesp_sno_class_report_historico();
		$li_tipo=2;
	}				
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();	
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$li_adelanto=$_SESSION["la_nomina"]["adenom"];
	$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
	$ld_fechasper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);	
	$ls_titulo="<b>RECIBO DE PAGO</b>";
	$ls_periodo="<b>MES: ".$io_fecha->uf_load_nombre_mes(substr($ld_fecdesper,3,2))." / ".substr($ld_fecdesper,6,4)."</b>";	
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_codbendes=$io_fun_nomina->uf_obtenervalor_get("codbendes","");
	$ls_codbenhas=$io_fun_nomina->uf_obtenervalor_get("codbenhas","");
	$ls_coduniadmdes=$io_fun_nomina->uf_obtenervalor_get("coduniadmdes","");
	$ls_coduniadmhas=$io_fun_nomina->uf_obtenervalor_get("coduniadmhas","");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_conceptop2=$io_fun_nomina->uf_obtenervalor_get("conceptop2","");
	$ls_conceptoreporte=$io_fun_nomina->uf_obtenervalor_get("conceptoreporte","");
	$ls_tituloconcepto=$io_fun_nomina->uf_obtenervalor_get("tituloconcepto","");
	$ls_quincena=$io_fun_nomina->uf_obtenervalor_get("quincena","-");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_codubifis=$io_fun_nomina->uf_obtenervalor_get("codubifis","");
	$ls_codpai=$io_fun_nomina->uf_obtenervalor_get("codpai","");
	$ls_codest=$io_fun_nomina->uf_obtenervalor_get("codest","");
	$ls_codmun=$io_fun_nomina->uf_obtenervalor_get("codmun","");
	$ls_codpar=$io_fun_nomina->uf_obtenervalor_get("codpar","");
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("subnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("subnomhas","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_buscar_beneficiarios($ls_codbendes, $ls_codbenhas, $ls_codperdes, $ls_codperhas);
	}
	if(($lb_valido==false) || ($io_report->rs_data_detalle2->RecordCount()==0)) // Existe alg�n error � no hay registros
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
		uf_print_encabezado_pagina1($ls_desnom,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la p�gina
		$li_bene=$io_report->rs_data_detalle2->RecordCount();
		while((!$io_report->rs_data_detalle2->EOF)&&($lb_valido))
		{
		 	$ls_codperdes2=str_pad($io_report->rs_data_detalle2->fields["cedben"],10,"0",0);
			$ls_codperhas2=str_pad($io_report->rs_data_detalle2->fields["cedben"],10,"0",0);			
			$ls_codpe_cau=$io_report->rs_data_detalle2->fields["codper"];
			$ls_cedben=$io_report->rs_data_detalle2->fields["cedben"];
			$ls_cedcaut=$io_report->rs_data_detalle2->fields["cedaut"];		
			$ls_nomaut=$io_report->rs_data_detalle2->fields["nomcheben"];
						 
			$ls_nexben=$io_report->rs_data_detalle2->fields["nexben"];
			$ls_parentesco="";
			switch ($ls_nexben)
			{
				 case "-":
					$ls_parentesco="Sin parentesco";
				break;
				case "C":
					$ls_parentesco="Conyuge";
				break;
				case "H":
					$ls_parentesco="Hijo";
				break;
				case "P":
					$ls_parentesco="Progenitor";
				break;
				case "E":
					$ls_parentesco="Hermano";
				break;
			} 
			$lb_valido=$io_report->uf_recibopago_personal($ls_codperdes2,$ls_codperhas2,$ls_coduniadmdes,$ls_coduniadmhas,$ls_conceptocero,
			                                              $ls_conceptop2,$ls_conceptoreporte,$ls_codubifis,$ls_codpai,$ls_codest,
														  $ls_codmun,$ls_codpar,$ls_subnomdes,$ls_subnomhas,$ls_orden); // Cargar el DS con los datos de la cabecera del reporte
			$ls_cedben=$io_report->rs_data_detalle2->fields["cedben"];
			$ls_nomben=$io_report->rs_data_detalle2->fields["nomben"];	
			$ls_apeben=$io_report->rs_data_detalle2->fields["apeben"];	
			$ls_porcentajeben=$io_report->rs_data_detalle2->fields["porpagben"];										   
			$li_totrow=$io_report->rs_data->RecordCount();
			$li_totrow2=$io_report->rs_data->RecordCount();
			while((!$io_report->rs_data->EOF)&&($lb_valido))
			{
				$li_toting=0;
				$li_totded=0;
				$ls_codper=$io_report->rs_data->fields["codper"];
				$ls_cedper=$io_report->rs_data->fields["cedper"];
				$ls_nomper=$io_report->rs_data->fields["apeper"].", ".$io_report->rs_data->fields["nomper"];
				$ls_descar=$io_report->rs_data->fields["descar"];
				$ls_codcueban=$io_report->rs_data->fields["codcueban"];
				$li_total=$io_report->rs_data->fields["total"];
				$ls_unidad=$io_report->rs_data->fields["desuniadm"];
				$ls_banco=$io_report->rs_data->fields["banco"];
				$ls_fecha_ingreso=$io_report->rs_data->fields["fecingper"];	
				$ls_fecha_egreso=$io_report->rs_data->fields["fecegrper"];				
				$ls_categoria=$io_report->rs_data->fields["descat"];
				$ls_tipopago=$io_report->rs_data->fields["tipcuebanper"];					
				$ls_fecleypen=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecleypen"]);	
				$ls_forma="";
				switch($ls_tipopago)
				{
					case "A":
						$ls_forma="CUENTA DE AHORRO";
					break;
					case "C":
						$ls_forma="CUENTA CORRIENTE";
					break;
					case " ":
					break;
					
				}	 					
				$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
				uf_print_cabecera1($io_cabecera,$ls_codcueban,$ls_banco,$ls_forma,$ls_cedben, $ls_nomben, $ls_apeben,$ls_parentesco,$io_pdf); // Imprimimos la cabecera del registro
				if ($ls_cedben!=$ls_cedcaut)
				{
					uf_print_autorizado($ls_cedcaut,$ls_nomaut,$io_pdf);	
				}			
				$lb_valido=$io_report->uf_recibopago_conceptopersonal($ls_codper,$ls_conceptocero,$ls_conceptop2,$ls_conceptoreporte,$ls_tituloconcepto,$ls_quincena); // Obtenemos el detalle del reporte
				if($lb_valido)
				{
					$li_totrow_det=$io_report->rs_data_detalle->RecordCount();
					$li_totrow_det2=$io_report->rs_data_detalle->RecordCount();
					$li_asig=0;
					$li_dedu=0;
					$ls_cuota="";
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
								$ls_repconsunicon=$io_report->rs_data_detalle->fields["repconsunicon"];
								$ls_consunicon=$io_report->rs_data_detalle->fields["consunicon"];
								$ls_cuota="";
								if (($ls_repconsunicon=='1')&&($ls_consunicon!=""))
								{
									$arrResultado=$io_report->uf_buscar_cuotas($ls_consunicon,$ls_codper,$ls_cuota);
									$ls_cuota=$arrResultado['as_cuota'];
									$lb_valido=$arrResultado['lb_valido'];
								}
								$la_data_a[$li_asig]=array('codcon'=>$ls_codconc,'denominacion'=>$ls_nomcon,
								                           'valor'=>$li_valsal,'cuota'=>$ls_cuota);
								break;
								
							case "2": // segunda quincena;
								while(!$io_report->rs_data_detalle->EOF)
								{
									$ls_tipsal=rtrim($io_report->rs_data_detalle->fields["tipsal"]);
									if(($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2") || ($ls_tipsal=="R")) // Buscamos las asignaciones
									{
										$li_asig=$li_asig+1;
										$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
										$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
							            $li_toting=$li_toting+abs($io_report->rs_data_detalle->fields["valsal"]);
		                                $li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
										$ls_repconsunicon=$io_report->rs_data_detalle->fields["repconsunicon"];
										$ls_consunicon=$io_report->rs_data_detalle->fields["consunicon"];
										$ls_cuota="";
										if (($ls_repconsunicon=='1')&&($ls_consunicon!=""))
										{
											$arrResultado=$io_report->uf_buscar_cuotas($ls_consunicon,$ls_codper,$ls_cuota);
											$ls_cuota=$arrResultado['as_cuota'];
											$lb_valido=$arrResultado['lb_valido'];
										}
										$la_data_a[$li_asig]=array('denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'cuota'=>$ls_cuota);
									}
									else // Buscamos las deducciones y aportes
									{
										$li_dedu=$li_dedu+1;
										$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
										$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
										$li_totded=$li_totded+abs($io_report->rs_data_detalle->fields["valsal"]);
										$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
										$ls_repconsunicon=$io_report->rs_data_detalle->fields["repconsunicon"];
										$ls_consunicon=$io_report->rs_data_detalle->fields["consunicon"];
										$ls_cuota="";
										if (($ls_repconsunicon=='1')&&($ls_consunicon!=""))
										{
											$arrResultado=$io_report->uf_buscar_cuotas($ls_consunicon,$ls_codper,$ls_cuota);
											$ls_cuota=$arrResultado['as_cuota'];
											$lb_valido=$arrResultado['lb_valido'];
										}
										$la_data_d[$li_dedu]=array('codcon'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'cuota'=>$ls_cuota);
									}
									$io_report->rs_data_detalle->MoveNext();
								}
								$li_dedu=$li_dedu+1;
								$ls_codconc="----------";
								$ls_nomcon="ADELANTO 1ra QUINCENA";
								$li_valsal=round($li_total/2,2);
								$li_totded=$li_totded+$li_valsal;
								$li_valsal=$io_fun_nomina->uf_formatonumerico($li_valsal);
								$la_data_d[$li_dedu]=array('codcon'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'cuota'=>$ls_cuota);
								break;
								
							case "3": // Mes Completo;
								while(!$io_report->rs_data_detalle->EOF)
								{
									$ls_tipsal=rtrim($io_report->rs_data_detalle->fields["tipsal"]);
									if(($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2") || ($ls_tipsal=="R")) // Buscamos las asignaciones
									{
										$li_asig=$li_asig+1;
										$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
										$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
										$li_toting=$li_toting+abs($io_report->rs_data_detalle->fields["valsal"]);
										$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
										$ls_repconsunicon=$io_report->rs_data_detalle->fields["repconsunicon"];
										$ls_consunicon=$io_report->rs_data_detalle->fields["consunicon"];
										$ls_cuota="";
										if (($ls_repconsunicon=='1')&&($ls_consunicon!=""))
										{
											$arrResultado=$io_report->uf_buscar_cuotas($ls_consunicon,$ls_codper,$ls_cuota);
											$ls_cuota=$arrResultado['as_cuota'];
											$lb_valido=$arrResultado['lb_valido'];
										}
										$la_data_a[$li_asig]=array('codcon'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'cuota'=>$ls_cuota);
									}
									else // Buscamos las deducciones y aportes
									{
										$li_dedu=$li_dedu+1;
										$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
										$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
										$li_totded=$li_totded+abs($io_report->rs_data_detalle->fields["valsal"]);
										$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
										$ls_repconsunicon=$io_report->rs_data_detalle->fields["repconsunicon"];
										$ls_consunicon=$io_report->rs_data_detalle->fields["consunicon"];
										$ls_cuota="";
										if (($ls_repconsunicon=='1')&&($ls_consunicon!=""))
										{
											$arrResultado=$io_report->uf_buscar_cuotas($ls_consunicon,$ls_codper,$ls_cuota);
											$ls_cuota=$arrResultado['as_cuota'];
											$lb_valido=$arrResultado['lb_valido'];
										}
										$la_data_d[$li_dedu]=array('codcon'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'cuota'=>$ls_cuota);
									}
									$io_report->rs_data_detalle->MoveNext();
								}
								break;
						}
					}
					else// No utiliza adelanto de quincena
					{
						while(!$io_report->rs_data_detalle->EOF)
						{
							$ls_tipsal=rtrim($io_report->rs_data_detalle->fields["tipsal"]);
							if(($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2") || ($ls_tipsal=="R")) // Buscamos las asignaciones
							{
								$li_asig=$li_asig+1;
								$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
								$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
								$li_toting=$li_toting+abs($io_report->rs_data_detalle->fields["valsal"]);
								$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
								$ls_repconsunicon=$io_report->rs_data_detalle->fields["repconsunicon"];
								$ls_consunicon=$io_report->rs_data_detalle->fields["consunicon"];
								$ls_cuota="";
								if (($ls_repconsunicon=='1')&&($ls_consunicon!=""))
								{
									$arrResultado=$io_report->uf_buscar_cuotas($ls_consunicon,$ls_codper,$ls_cuota);
									$ls_cuota=$arrResultado['as_cuota'];
									$lb_valido=$arrResultado['lb_valido'];
								}
								$la_data_a[$li_asig]=array('codcon'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'cuota'=>$ls_cuota);
							}
							else // Buscamos las deducciones y aportes
							{
								$li_dedu=$li_dedu+1;
								$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
								$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
								$li_totded=$li_totded+abs($io_report->rs_data_detalle->fields["valsal"]);
								$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
								$ls_repconsunicon=$io_report->rs_data_detalle->fields["repconsunicon"];
								$ls_consunicon=$io_report->rs_data_detalle->fields["consunicon"];
								$ls_cuota="";
								if (($ls_repconsunicon=='1')&&($ls_consunicon!=""))
								{
									$arrResultado=$io_report->uf_buscar_cuotas($ls_consunicon,$ls_codper,$ls_cuota);
									$ls_cuota=$arrResultado['as_cuota'];
									$lb_valido=$arrResultado['lb_valido'];
								}
								$la_data_d[$li_dedu]=array('codcon'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal,'cuota'=>$ls_cuota);
							}
							$io_report->rs_data_detalle->MoveNext();
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
					for($li_s=1;$li_s<=$li_total;$li_s++) 
					{
						$la_valores_a["codcon"]="";
						$la_valores_a["denomasig"]="";
						$la_valores_a["valorasig"]="";
						$la_valores_a["valor"]="";
						$la_valores_a["cuota"]="";
						$la_valores_d["codcon"]="";
						$la_valores_d["denomdedu"]="";
						$la_valores_d["valordedu"]="";
						$la_valores_d["valor"]="";
						$la_valores_d["cuota"]="";					
						
						if($li_s<=$li_asig)
						{  
							$la_valores_a["codcon"]=$la_data_a[$li_s]["codcon"];
							$la_valores_a["denomasig"]=trim($la_data_a[$li_s]["denominacion"]);
							$la_valores_a["valorasig"]=$la_data_a[$li_s]["valor"];
							$la_valores_a["valor"]="";
							$la_valores_a["cuota"]=$la_data_a[$li_s]["cuota"];
							$la_data_a[$li_s]=$la_valores_a;
						}
						if($li_s<=$li_dedu)
						{
							$la_valores_d["codcon"]=$la_data_d[$li_s]["codcon"];
							$la_valores_d["denomdedu"]=trim($la_data_d[$li_s]["denominacion"]);
							$la_valores_d["valordedu"]=$la_data_d[$li_s]["valor"];
							$la_valores_d["valor"]="";
							$la_valores_d["cuota"]=$la_data_d[$li_s]["cuota"];
							$la_data_d[$li_s]=$la_valores_d;
						}
						else
						{
							 $la_valores_a["codcon"]="";
							 $la_valores_a["denomasig"]="";
							 $la_valores_a["valorasig"]="";
							 $la_valores_a["valor"]="";
							 $la_valores_a["cuota"]="";
							 $la_valores_d["codcon"]="";
							 $la_valores_d["denomdedu"]="";
							 $la_valores_d["valordedu"]="";
							 $la_valores_d["valor"]="";	
							 $la_valores_d["cuota"]="";	
							 $la_data_d[$li_s]=$la_valores_d;
						}
					}
					$lb_valido1=$io_report->uf_recibo_nomina_oficiales_2($ls_codpe_cau);	
					if ($lb_valido1)
					{
					  $li_nom=$io_report->rs_data_detalle->RecordCount();
					  while(!$io_report->rs_data_detalle->EOF)
					  {
							$ls_sueldob=$io_report->rs_data_detalle->fields["suebasper"];
							$ls_prichof=$io_report->rs_data_detalle->fields["pritraper"];
							$ls_prianoserv=$io_report->rs_data_detalle->fields["prianoserper"];
							$ls_prides=$io_report->rs_data_detalle->fields["pridesper"];
							$ls_noasc=$io_report->rs_data_detalle->fields["prinoascper"];
							$ls_priesp=$io_report->rs_data_detalle->fields["priespper"];
							$ls_priprof=$io_report->rs_data_detalle->fields["priproper"];
							$ls_subtotal=$io_report->rs_data_detalle->fields["subtotper"];					
							$ls_porcentaje=$io_report->rs_data_detalle->fields["porpenper"];
							$ls_nomper=$io_report->rs_data_detalle->fields["nomper"];
							$ls_aperper=$io_report->rs_data_detalle->fields["apeper"];
							$ls_nompercau=$ls_aperper.", ".$ls_aperper;
							$ls_cedpercau=$io_report->rs_data_detalle->fields["cedper"];
							$ls_fecingcau=$io_report->rs_data_detalle->fields["fecingper"];
							$ls_fecingnom=$io_funciones->uf_convertirfecmostrar($io_report->rs_data_detalle->fields["fecingnom"]);
							$ls_com=$io_report->rs_data_detalle->fields["descom"];
							$ls_rango=$io_report->rs_data_detalle->fields["desran"];
							$ls_categoria=$io_report->rs_data_detalle->fields["descat"];
							$ls_fecleypen=$io_funciones->uf_convertirfecmostrar($io_report->rs_data_detalle->fields["fecleypen"]);
							$ls_ano=calcular_anos_servicioas(strtotime($ls_fecingcau),strtotime($ls_fecingnom));							
							if ($ls_ano<0)
							{
								$ls_ano=0;
							}	
							$io_report->rs_data_detalle->MoveNext();		
					  }
					  if ($li_nom>0)
					  {	
						uf_detalle_nomina_oficial($ls_sueldob,$ls_prichof,$ls_prianoserv,$ls_prides,$ls_noasc,$ls_priesp,$ls_priprof,$ls_porcentaje,$ls_subtotal,$ls_porcentajeben,	
												  $ls_fecleypen,$ls_nompercau,$ls_cedpercau,$ls_ano,$ls_com,$ls_rango,$ls_categoria,$io_pdf);
					  }
					  else
					  {
					  	uf_detalle_nomina_oficial(0,0,0,0,0,0,0,0,0,0,0,'','',0,'','','',$io_pdf);
					  }
					}
					else
					{
					  uf_detalle_nomina_oficial(0,0,0,0,0,0,0,0,0,0,0,'','',0,'','','',$io_pdf);
					}
							
					uf_print_detalle($la_data_a,$la_data_d,$io_pdf); // Imprimimos el detalle 
					$li_totnet=$li_toting-$li_totded;
					$li_toting=$io_fun_nomina->uf_formatonumerico($li_toting);
					$li_totded=$io_fun_nomina->uf_formatonumerico($li_totded);
					$li_totnet=$io_fun_nomina->uf_formatonumerico($li_totnet);						
					uf_print_pie_cabecera1($li_toting,$li_totded,$li_totnet,$ls_codcueban,$io_pdf); // Imprimimos pie de la cabecera									
					$io_report->DS_detalle->resetds("codconc");
					unset($la_data_a);
					unset($la_data_d);
					unset($la_data);
					$io_pdf->stopObject($io_cabecera); // Detener el objeto cabecera					
				}
				$io_report->rs_data->MoveNext();
				if(!$io_report->rs_data->EOF)
				{
					$io_pdf->ezNewPage(); // Insertar una nueva p�gina						
				}
			}// fin del for (pensinado)
			$io_report->rs_data_detalle2->MoveNext();
		}// fin del For (beneficiario)
		$io_report->DS->resetds("codper");
		$io_report->DS_pension->resetds("codben");
		if(($li_totrow2>0)&&($li_totrow_det2>0)) // Si no ocurrio ning�n error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo alg�n error
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 
