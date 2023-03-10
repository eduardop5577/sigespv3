<?php
/***********************************************************************************
* @fecha de modificacion: 26/08/2022, para la version de php 8.1 
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
	if (!array_key_exists("la_logusr",$_SESSION))
	   {
		 echo "<script language=JavaScript>";
		 echo "opener.document.form1.submit();"	;	
		 echo "close();";
		 echo "</script>";		
	   }	
	$x_pos = 0;//mientras mas grande el numero, mas a la derecha.
	$y_pos = -1;//Mientras mas peque?o el numero, mas alto.
    $ls_directorio="cheque_configurable";


	function uf_inicializar_variables($as_archivo,$as_contenido,$ai_medidas)
	{
	///////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_inicializar_variables
	//		    Acess: private 
	//	    Arguments: $as_archivo   = Nombre del Archivo desde donde se cargar?n las medidas.
	//                 $as_contenido = Par?metros de Ubicaci?n de los elemento del Cheque Voucher.
	//                 $ai_medidas   = N?mero de Par?metros de Ubicaci?n del Formato del Cheque Voucher.
	//    Description: Funci?n indica las medidas de ubicaci?n iniciales de la Parte Superior del Cheque Voucher.
	//	   Creado Por: Ing. N?stor Falc?n
	// Fecha Creaci?n: 07/01/2009. 
	///////////////////////////////////////////////////////////////////////////////////////////////

	  global $valores;
	  global $ls_directorio;
	  
	  if (!file_exists ($ls_directorio))
		 {
		   $lb_exito = mkdir($ls_directorio,0777);
		   if (!$lb_exito)
			  {
			    print "<script>";
				print "alert('Error al crear directorio cheque_configurable');";
				print "close();";
				print "</script>";
			  }
		 }
		
	  if ((!file_exists ($as_archivo)) || (filesize($as_archivo)==0))
		 {	
		   if (file_exists ($ls_directorio))
			  {
			    $archivo = fopen($as_archivo, "w");			
				fwrite($archivo,$as_contenido);
				fclose($archivo);
			  }
		 }
			
	  if ((file_exists($as_archivo)) && (filesize($as_archivo)>0))
		 {
			$archivo   = fopen($as_archivo, "r");
			$contenido = fread($archivo, filesize($as_archivo));			
			fclose($archivo);			
			$valores = explode("-",$contenido);	
			if (count((array)$valores)<>$ai_medidas)
			   {
				 $archivo = fopen($ls_archivo, "w");
				 fclose($archivo);			
				 print "<script>";
				 print "alert('Ocurrio un error, por favor cargue de nuevo el cheque (Las medidas seran inicializadas por fallo de lectura y escritura)');";
				 print "close();";
				 print "</script>";
			   }
		 }
	  else
		 {
		   print "<script>";
		   print "alert('Ocurrio un error, por favor cargue de nuevo el cheque (Las medidas seran inicializadas por fallo de lectura y escritura)');";
		   print "close();";
		   print "</script>";
		 }
	}// end function uf_inicializar_variables.
	
	function uf_print_encabezado_pagina($ldec_monto,$ls_nomproben,$ls_monto,$ls_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		    Acess: private 
		//	    Arguments: ldec_monto : Monto del cheque
		//	    		   ls_nomproben:  Nombre del proveedor o beneficiario
		//	    		   ls_monto : Monto en letras
		//	    		   ls_fecha : Fecha del cheque
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. N?stor Falc?n
		// Fecha Creaci?n: 25/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $valores;
		//Imprimo el monto
		$io_pdf->add_texto($valores[0],$valores[1],10,"<b>***".$ldec_monto."***</b>");
		//Beneficiario del Cheque
		$io_pdf->add_texto($valores[2],$valores[3],11,"<b>$ls_nomproben</b>");
		//Monto en letras del Cheque
		//Cortando el monto en caso de que sea muy largo		
		$ls_monto_cortado=wordwrap($ls_monto,70,"?");
		$la_arreglo=array();
		$la_arreglo=explode("?",$ls_monto_cortado);
		if(array_key_exists(0,$la_arreglo))
			$io_pdf->add_texto($valores[4],$valores[5],9,"<b>$la_arreglo[0]</b>");
		if(array_key_exists(1,$la_arreglo))
			$io_pdf->add_texto($valores[6],$valores[7],9,"<b>$la_arreglo[1]</b>");
		$ls_anio=substr($ls_fecha,-4);
		$ls_fecha_corta=substr($ls_fecha,0,(strlen($ls_fecha)-5));	
		//Fecha del Cheque
		$io_pdf->add_texto($valores[8],$valores[9],9,"<b>$ls_fecha_corta</b>");
		$io_pdf->add_texto($valores[10],$valores[11],9,"<b>$ls_anio</b>");	
		$io_pdf->add_texto($valores[12],$valores[13],9,"<b>NO ENDOSABLE</b>");
		$io_pdf->add_texto($valores[14],$valores[15],9,"<b>CADUCA A LOS ".$_SESSION["la_empresa"]["diacadche"]." DIAS</b>");	
		return $io_pdf;	
	}// end function uf_print_encabezadopagina.

	function uf_print_cabecera($ls_numdoc,$ls_nomban,$ls_ctaban,$ls_chevau,$ls_nomproben,$ls_solicitudes,$ls_conmov,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: ls_numdoc : Numero de documento
		//	    		   ls_nomban : Nombre del banco
		//				   ls_cbtan  : Cuenta del banco
		//				   ls_chevau : Voucher del cheuqe
		//				   ls_nomproben: Nombre del proveedor o beneficiario
		//				   ls_solicitudes: Solicitudes canceladas con el cheque					  
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funci?n que imprime los datos basicos del cheque
		//	   Creado Por: Ing. N?stor Falc?n
		// Fecha Creaci?n: 02/04/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$io_pdf->setStrokeColor(0,0,0);
		$li_pos=195;
		$io_pdf->convertir_valor_mm_px($li_pos);
		$io_pdf->ezSetY($li_pos);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],35,490,70,60); // Agregar Logo
		$la_data=array(array('title'=>'<b>                                                                          COMPROBANTE DE EGRESO</b>'));
		$la_columna=array('title'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580,
						 'rowGap'=>25,
						 'cols'=>array('title'=>array('justification'=>'left','width'=>580))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('banco'=>'<b>Banco</b>  ','cheque'=>'<b>Cheque N?</b>  ','cuenta'=>'<b>Cuenta N?:</b>  ','voucher'=>'<b>Voucher N?:</b>  '),
						array('banco'=>$ls_nomban,'cheque'=>$ls_numdoc,'cuenta'=>$ls_ctaban,'voucher'=>$ls_chevau));
		$la_columna=array('banco'=>'','cheque'=>'','cuenta'=>'','voucher'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580,
						 'cols'=>array('banco'=>array('justification'=>'left','width'=>160),'cheque'=>array('justification'=>'left','width'=>100),
						 'cuenta'=>array('justification'=>'left','width'=>160),'voucher'=>array('justification'=>'left','width'=>160))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('ordenes'=>'<b>Orden(es) de Pago(s):</b> '.$ls_solicitudes),
					   array('ordenes'=>'<b>Beneficiario:</b> '.$ls_nomproben),
					   array('ordenes'=>'<b>Concepto:</b> '.$ls_conmov));
		$la_columna=array('ordenes'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580,
						 'cols'=>array('ordenes'=>array('justification'=>'left','width'=>580))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		return $io_pdf;	
	}// end function uf_print_cabecera.

	function uf_print_detalle($la_title,$la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. N?stor Falc?n
		// Fecha Creaci?n: 02/04/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$io_pdf->ezSetDy(-5);
		$io_pdf->setStrokeColor(0,0,0);
		$la_data_title=array($la_title);
		$io_pdf->set_margenes(90,55,0,0);
		$la_columna=array('title'=>'','title2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580,
						 'cols'=>array('title'=>array('justification'=>'center','width'=>350),'title2'=>array('justification'=>'center','width'=>230))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data_title,$la_columna,'',$la_config);	
		//Imprimo los detalles tanto `de presupuesto como contablwe del movimiento
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('estpro'=>array('justification'=>'center','width'=>195),
			 						   'spg_cuenta'=>array('justification'=>'center','width'=>80),
									   'monto_spg'=>array('justification'=>'right','width'=>75),
						 			   'scg_cuenta'=>array('justification'=>'center','width'=>80), // Justificaci?n y ancho de la columna
						 			   'debe'=>array('justification'=>'right','width'=>75), // Justificaci?n y ancho de la columna
						 			   'haber'=>array('justification'=>'right','width'=>75))); // Justificaci?n y ancho de la columna
		$la_columnas=array('estpro'=>'<b>Programatica</b>',
						   'spg_cuenta'=>'<b>Cuenta</b>',
						   'monto_spg'=>'<b>Monto</b>',
						   'scg_cuenta'=>'<b>Cuenta</b>',
						   'debe'=>'<b>Debe</b>',
						   'haber'=>'<b>Haber</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$io_pdf->ezText('                     ',10);//Inserto una linea en blanco
		return $io_pdf;	
	}// end function uf_print_detalle.
	
	function uf_print_autorizacion($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_autorizacion
		//		    Acess: private 
		//	    Arguments: io_pdf // Objeto PDF
		//    Description: funci?n el final del voucher 
		//	   Creado Por: Ing. N?stor Falc?n
		// Fecha Creaci?n: 25/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		//$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);		
		$io_pdf->Rectangle(11,43,580,105);
		$io_pdf->line(11,90,590,90);
		$io_pdf->line(11,74.6,590,74.6);
		$io_pdf->line(11,103.5,590,103.5);
		$io_pdf->line(11,135,590,135);		
		
		$io_pdf->line(107,90,107,148);//Vertical
		$io_pdf->line(203,90,203,148);//Vertical
		$io_pdf->line(309,90,309,148);//Vertical
		$io_pdf->line(400,90,400,148);//Vertical
		$io_pdf->line(500,90,500,148);//Vertical		
		$io_pdf->line(191,43,191,75);
		$io_pdf->line(310.5,43,310.5,75);
		$io_pdf->line(411,43,411,75);	
		
		$io_pdf->addText(16,137.6,8,'<b>ELABORADO POR</b>');
		$io_pdf->addText(22,98,6,'TSU ARQUIRIA VARELA');
		$io_pdf->addText(30,92,6,'FIRMA/FECHA');
		$io_pdf->addText(122,137.6,8,'<b>REVISADO POR</b>');
		$io_pdf->addText(122,98,6,'TSU NESTOR ESCOBAR');
		$io_pdf->addText(134,92,6,'FIRMA/FECHA');		
		$io_pdf->addText(223,137.6,8,'<b>PRESUPUESTO</b>');		
		$io_pdf->addText(224,98,6,'TSU LEIDIS LOZADA');
		$io_pdf->addText(224,92,6,'FIRMA/SELLO/FECHA');		
		$io_pdf->addText(324,137.6,8,'<b>CONTABILIDAD</b>');		
		$io_pdf->addText(320,98,6,'LCDA. MARINEL FAJARDO');
		$io_pdf->addText(324,92,6,'FIRMA/SELLO/FECHA');			
		$io_pdf->addText(414,137.6,8,'<b>ADMINISTRACION</b>');
		$io_pdf->addText(410,98,6,'LCDO. DANNY APARICIO');
		$io_pdf->addText(414,92,6,'FIRMA/SELLO/FECHA');				
		$io_pdf->addText(513,137.6,8,'<b>PRESIDENCIA</b>');
		$io_pdf->addText(504,98,6,'ARQ. MARIA E. VILLAQUIRAN');
		$io_pdf->addText(513,92,6,'FIRMA/SELLO/FECHA');		
		$io_pdf->addText(258,78.85,10,'<b>Recib? Conforme</b>');
		
		$io_pdf->addText(16,63.27,10,'<b>Nombre:</b>');		
		$io_pdf->addText(196,63.27,10,'<b>C?dula de Identidad:</b>');		
		$io_pdf->addText(316,63.27,10,'<b>Fecha:</b>');
		$io_pdf->addText(416,63.27,10,'<b>Firma:</b>');
		return $io_pdf;	
	}// end function uf_print_autorizacion.	

require_once("sigesp_scb_report.php");
require_once('../../shared/class_folder/class_pdf.php');
require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../../base/librerias/php/general/sigesp_lib_include.php");
require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");

$io_include   = new sigesp_include();
$ls_conect    = $io_include->uf_conectar();
$io_sql		  = new class_sql($ls_conect);	
$class_report = new sigesp_scb_report($ls_conect);
$io_funciones = new class_funciones();				
$ds_voucher	  = new class_datastore();	
$ds_dt_scg	  = new class_datastore();				
$ds_dt_spg	  = new class_datastore();
$numalet	  = new class_numero_a_letra();

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


$ls_tipimp = "";
	if (array_key_exists("tipimp",$_GET))
	{
		$ls_tipimp = $_GET["tipimp"];
	}	

	if ($ls_tipimp=='lote')
	{
		$ls_codemp		=$_SESSION["la_empresa"]["codemp"];
		$ls_codban      = $_GET["codban"];
		$ls_ctaban      = $_GET["ctaban"];
		$ls_documentos  = $_GET["documentos"];
		$ls_fechas      = $_GET["fechas"];
		$ld_fecdes      = $_GET["fecdesde"];
		$ld_fechas      = $_GET["fechasta"];
		$ls_operaciones = $_GET["operaciones"];
			
		//Descompongo la cadena de documentos en un arreglo tomando como separaci?n el ','
		$arr_documentos = explode(",",$ls_documentos);
		$li_totdoc		= count((array)$arr_documentos);
		//Descompongo la cadena de fechas en un arreglo tomando como separaci?n el '-'
		$arr_fecmov = explode("-",$ls_fechas);
		$li_totfec  = count((array)$arr_fecmov);
	   //Descompongo la cadena de operaciones en un arreglo tomando como separaci?n el '-'
		$arr_operaciones = explode("-",$ls_operaciones);
		$li_totdoc	= count((array)$arr_operaciones);		
		$class_report->uf_buscar_cheques_vouchers($arr_documentos,$arr_fecmov,$arr_operaciones,$ls_codban,$ls_ctaban);
		
		$li_total   = $class_report->ds_voucher1->getRowCount("numdoc");

		if ($li_total>0)
		{	
			
			set_time_limit(1800);
			$io_pdf=new class_pdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->set_margenes(0,55,0,0);
		    $io_pdf->ezStartPageNumbers(570,30,10,'','',1); // Insertar el n?mero de p?gina
			if (trim($ls_codban)=="001")//Banco Industrial de Vzla.
			   {
				 $ls_archivo="cheque_configurable/medidas_industrial.txt";
				 $ls_contenido="144.00-0.00-24.00-20.00-26.00-30.00-27.00-33.00-22.00-38.00-79.00-38.00-120.00-60.00-130.00-65.00-10.00-91.00-8.00-100.00-12.00-117.00";
				 $li_medidas=22;
			   }
		    elseif(trim($ls_codban)=="002")//Banco de Vzla.
			   {
			     $ls_archivo   = "cheque_configurable/medidas_venezuela.txt";
			     $ls_contenido = "144.00-0.00-24.00-20.00-26.00-30.00-27.00-33.00-22.00-38.00-79.00-38.00-120.00-60.00-130.00-65.00-10.00-91.00-8.00-100.00-12.00-117.00";
			     $li_medidas   = 22;
			   }
			else
			   {
			     $ls_archivo   = "cheque_configurable/medidas_venezuela.txt";
			     $ls_contenido = "144.00-0.00-24.00-20.00-26.00-30.00-27.00-33.00-22.00-38.00-79.00-38.00-120.00-60.00-130.00-65.00-10.00-91.00-8.00-100.00-12.00-117.00";
			     $li_medidas   = 22;
			   }
			uf_inicializar_variables($ls_archivo,$ls_contenido,$li_medidas);
			for ($i=1;$i<=$li_total;$i++)
			{
				$ls_numdoc=$class_report->ds_voucher1->getValue("numdoc",$i);
				
				$ls_chevau=$class_report->ds_voucher1->getValue("chevau",$i);
				$ls_codope='CH';
				
				$data 	   = $class_report->uf_cargar_chq_voucher($ls_numdoc,$ls_chevau,$ls_codban,$ls_ctaban,$ls_codope);
				$lb_valido = $class_report->uf_actualizar_status_impreso($ls_numdoc,$ls_chevau,$ls_codban,$ls_ctaban,$ls_codope);
				$class_report->SQL->begin_transaction();
				if (!$lb_valido)
				   {
					 print "Error al actualizar";
					 $class_report->is_msg_error;	
					 $class_report->SQL->rollback();
				   }
				else
				   {
					 $class_report->SQL->commit();
				   }
				$ds_voucher->data=$data;
				
				$li_totrow=$ds_voucher->getRowCount("numdoc");
				$io_pdf->transaction('start'); // Iniciamos la transacci?n
				$thisPageNum=$io_pdf->ezPageCount;
				
				for($li_i=1;$li_i<=$li_totrow;$li_i++)
				{
					
					$li_totprenom = 0;
					$ldec_mondeb  = 0;
					$ldec_monhab  = 0;
					$li_totant	  = 0;
					$ls_numdoc		= $ds_voucher->data["numdoc"][$li_i];
					$ls_codban		= $ds_voucher->data["codban"][$li_i];
					$ls_nomban		= $class_report->uf_select_data($io_sql,"SELECT nomban FROM scb_banco WHERE codban ='".$ls_codban."' AND codemp='".$ls_codemp."'","nomban");
					$ls_ctaban		= $ds_voucher->data["ctaban"][$li_i];
					$ls_chevau		= $ds_voucher->data["chevau"][$li_i];
					$ld_fecmov	  	= $io_funciones->uf_convertirfecmostrar($ds_voucher->data["fecmov"][$li_i]);
					$ls_nomproben 	= $ds_voucher->data["nomproben"][$li_i];
					$ls_solicitudes = $class_report->uf_select_solicitudes($ls_numdoc,$ls_codban,$ls_ctaban);
					$ls_conmov		= $ds_voucher->getValue("conmov",$li_i);
					$ldec_monret	= $ds_voucher->getValue("monret",$li_i);
					$ldec_monto		= $ds_voucher->getValue("monto",$li_i);
					$ldec_total		= $ldec_monto-$ldec_monret;
					//Asigno el monto a la clase numero-letras para la conversion.
					$numalet->setNumero($ldec_total);
					//Obtengo el texto del monto enviado.
					$ls_monto= $numalet->letra();
					$io_pdf=uf_print_encabezado_pagina(number_format($ldec_total,2,",","."),$ls_nomproben,$ls_monto,$_SESSION["la_empresa"]["ciuemp"].", ".$ld_fecmov,$io_pdf); // Imprimimos el encabezado de la p?gina
					
					$io_pdf=uf_print_cabecera($ls_numdoc,$ls_nomban,$ls_ctaban,$ls_chevau,$ls_nomproben,$ls_solicitudes,$ls_conmov,$io_pdf); // Imprimimos la cabecera del registro
					
					$ds_dt_scg->data=$class_report->uf_cargar_dt_scg($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope); // Obtenemos el detalle del reporte
					$la_items = array('0'=>'scg_cuenta');
					$la_suma  = array('0'=>'monto');
					$ds_dt_scg->group_by($la_items,$la_suma,'scg_cuenta');
					$li_totrow_det=$ds_dt_scg->getRowCount("scg_cuenta");
					
					$ds_dt_spg->data=$class_report->uf_cargar_dt_spg($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope);
					$la_items = array('0'=>'estpro','1'=>'spg_cuenta');
					$la_suma  = array('0'=>'monto');
					$ds_dt_spg->group_by($la_items,$la_suma,'spg_cuenta');
					$li_totrow_spg=$ds_dt_spg->getRowCount("spg_cuenta");
					///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					// Ciclo para unir en una sola matriz los detalles de presupuesto y los contables para proceder luego a imprimirlos.
					///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					if ($li_totrow_det>=$li_totrow_spg)
					   {
						 for ($li_s=1;$li_s<=$li_totrow_det;$li_s++)
							 {
							   $ls_scg_cuenta = trim($ds_dt_scg->data["scg_cuenta"][$li_s]);
							   $ls_debhab     = $ds_dt_scg->data["debhab"][$li_s];
							   $ldec_monto    = $ds_dt_scg->data["monto"][$li_s];
							   if ($ls_debhab=='D')
								  {
									$ldec_mondeb = number_format($ldec_monto,2,",",".");
									$ldec_monhab = "";
								  }
							   else
								  {
									$ldec_monhab = number_format($ldec_monto,2,",",".");
									$ldec_mondeb = "";
								  }
							   if (array_key_exists("spg_cuenta",$ds_dt_spg->data))
								  {
									if (array_key_exists($li_s,$ds_dt_spg->data["spg_cuenta"]))
									   {
										 $ls_cuentaspg   = trim($ds_dt_spg->getValue("spg_cuenta",$li_s));
										 $ls_estpro      = $ds_dt_spg->getValue("estpro",$li_s);	  
										 $ldec_monto_spg = number_format($ds_dt_spg->getValue("monto",$li_s),2,",",".");
									   }
									else
									   {
										 $ls_cuentaspg   = "";	
										 $ls_estpro      = "";	  
										 $ldec_monto_spg = "";
									   }
								  }
							   else
								  {
									$ls_cuentaspg   = "";	
									$ls_estpro      = "";	  
									$ldec_monto_spg = "";
								  }
							   $la_data[$li_s]=array('spg_cuenta'=>$ls_cuentaspg,'estpro'=>$ls_estpro,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
							 }
					   }
					if ($li_totrow_spg>$li_totrow_det)
					   {
						 for ($li_s=1;$li_s<=$li_totrow_spg;$li_s++)
							 {
							   if (array_key_exists("scg_cuenta",$ds_dt_scg->data))
								  {
									if (array_key_exists($li_s,$ds_dt_scg->data["scg_cuenta"]))
									   {
										 $ls_scg_cuenta = trim($ds_dt_scg->data["scg_cuenta"][$li_s]);
										 $ls_debhab 	= $ds_dt_scg->data["debhab"][$li_s];
										 $ldec_monto	= $ds_dt_scg->data["monto"][$li_s];
										 if ($ls_debhab=='D')
											{
											  $ldec_mondeb = number_format($ldec_monto,2,",",".");
											  $ldec_monhab = "";
											}
										 else
											{
											  $ldec_monhab = number_format($ldec_monto,2,",",".");
											  $ldec_mondeb = "";
											}
									   }
									else
									   {
										 $ls_scg_cuenta = "";
										 $ls_debhab 	= "";
										 $ldec_monto	= "";
										 $ldec_mondeb	= "";
										 $ldec_monhab   = "";					
									   }
								  }
							   else
								  {
									$ls_scg_cuenta = "";
									$ls_debhab 	   = "";
									$ldec_monto	   = "";
									$ldec_mondeb   = "";
									$ldec_monhab   = "";					
								  }
							   if (array_key_exists("spg_cuenta",$ds_dt_spg->data))
								  {
									if (array_key_exists($li_s,$ds_dt_spg->data["spg_cuenta"]))
									   {
										 $ls_cuentaspg   = trim($ds_dt_spg->getValue("spg_cuenta",$li_s));
										 $ls_estpro      = $ds_dt_spg->getValue("estpro",$li_s);	  
										 $ldec_monto_spg = number_format($ds_dt_spg->getValue("monto",$li_s),2,",",".");
									   }
									else
									   {
										 $ls_cuentaspg   = "";	
										 $ls_estpro      = "";	  
										 $ldec_monto_spg = "";
									   }
								  }
							   else
								  {
									$ls_cuentaspg   = "";	
									$ls_estpro      = "";	  
									$ldec_monto_spg = "";
								  }
							   $la_data[$li_s]=array('spg_cuenta'=>$ls_cuentaspg,'estpro'=>$ls_estpro,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
							 }
					   }
					$io_pdf=uf_print_detalle(array('title'=>'Detalle Presupuestario Pago','title2'=>'Detalle Contable Pago'),$la_data,$io_pdf);
				} // Fin del for 2
			    $io_pdf=uf_print_autorizacion($io_pdf);	
				if ($i<$li_total)
				{			
					$io_pdf->ezNewPage(); // Insertar una nueva p?gina
					$io_pdf->set_margenes(0,55,0,0);
				}
				unset($la_data);
			}// Fin del for 1
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
			unset($io_pdf,$class_report,$io_funciones,$ds_dt_spg,$ds_dt_scg,$ds_voucher,$la_data);
		}//Fin del if ($li_total>0)
		else
		{
			 print("<script language=JavaScript>");
			 print(" alert('No hay nada que Reportar');"); 
			 print(" close();");
			 print("</script>");	  
		} 
		
	} // Fin de si es lote
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// SI NO ES POR LOTE ENTONCES
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	else 
	{
		$ls_codemp = $_SESSION["la_empresa"]["codemp"];
		$ls_codban = $_GET["codban"];
		$ls_ctaban = $_GET["ctaban"];
		$ls_numdoc = $_GET["numdoc"];
		$ls_chevau = $_GET["chevau"];
		$ls_codope = $_GET["codope"];				
	
		$data 	   = $class_report->uf_cargar_chq_voucher($ls_numdoc,$ls_chevau,$ls_codban,$ls_ctaban,$ls_codope);
		$lb_valido = $class_report->uf_actualizar_status_impreso($ls_numdoc,$ls_chevau,$ls_codban,$ls_ctaban,$ls_codope);
		$class_report->SQL->begin_transaction();
		if (!$lb_valido)
		   {
			 print "Error al actualizar";
			 $class_report->is_msg_error;	
			 $class_report->SQL->rollback();
		   }
		else
		   {
			 $class_report->SQL->commit();
		   }
		$ds_voucher->data=$data;
		
		set_time_limit(1800);
		$io_pdf=new class_pdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->set_margenes(0,55,0,0);
		$li_totrow=$ds_voucher->getRowCount("numdoc");
		$io_pdf->transaction('start'); // Iniciamos la transacci?n
		$thisPageNum=$io_pdf->ezPageCount;
		$io_pdf->ezStartPageNumbers(570,30,10,'','',1); // Insertar el n?mero de p?gina
		if (trim($ls_codban)=="001")//Banco Industrial de Vzla.
		   {
			 $ls_archivo="cheque_configurable/medidas_industrial.txt";
			 $ls_contenido="144.00-0.00-24.00-20.00-26.00-30.00-27.00-33.00-22.00-38.00-79.00-38.00-120.00-60.00-130.00-65.00-10.00-91.00-8.00-100.00-12.00-117.00";
			 $li_medidas=22;
		   }
		elseif(trim($ls_codban)=="002")//Banco de Vzla.
		   {
			 $ls_archivo   = "cheque_configurable/medidas_venezuela.txt";
			 $ls_contenido = "144.00-0.00-24.00-20.00-26.00-30.00-27.00-33.00-22.00-38.00-79.00-38.00-120.00-60.00-130.00-65.00-10.00-91.00-8.00-100.00-12.00-117.00";
			 $li_medidas   = 22;
		   }
		else
		   {
			 $ls_archivo   = "cheque_configurable/medidas_venezuela.txt";
			 $ls_contenido = "144.00-0.00-24.00-20.00-26.00-30.00-27.00-33.00-22.00-38.00-79.00-38.00-120.00-60.00-130.00-65.00-10.00-91.00-8.00-100.00-12.00-117.00";
			 $li_medidas   = 22;
		   }
		uf_inicializar_variables($ls_archivo,$ls_contenido,$li_medidas);
		$io_pdf=uf_print_autorizacion($io_pdf);	
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
			unset($la_data);
			$li_totprenom = 0;
			$ldec_mondeb  = 0;
			$ldec_monhab  = 0;
			$li_totant	  = 0;
			$ls_numdoc		= $ds_voucher->data["numdoc"][$li_i];
			$ls_codban		= $ds_voucher->data["codban"][$li_i];
			$ls_nomban		= $class_report->uf_select_data($io_sql,"SELECT nomban FROM scb_banco WHERE codban ='".$ls_codban."' AND codemp='".$ls_codemp."'","nomban");
			$ls_ctaban		= $ds_voucher->data["ctaban"][$li_i];
			$ls_chevau		= $ds_voucher->data["chevau"][$li_i];
			$ld_fecmov	  	= $io_funciones->uf_convertirfecmostrar($ds_voucher->data["fecmov"][$li_i]);
			$ls_nomproben 	= $ds_voucher->data["nomproben"][$li_i];
			$ls_solicitudes = $class_report->uf_select_solicitudes($ls_numdoc,$ls_codban,$ls_ctaban);
			$ls_conmov		= $ds_voucher->getValue("conmov",$li_i);
			$ldec_monret	= $ds_voucher->getValue("monret",$li_i);
			$ldec_monto		= $ds_voucher->getValue("monto",$li_i);
			$ldec_total		= $ldec_monto-$ldec_monret;
			//Asigno el monto a la clase numero-letras para la conversion.
			$numalet->setNumero($ldec_total);
			//Obtengo el texto del monto enviado.
			$ls_monto= $numalet->letra();
			$io_pdf=uf_print_encabezado_pagina(number_format($ldec_total,2,",","."),$ls_nomproben,$ls_monto,$_SESSION["la_empresa"]["ciuemp"].", ".$ld_fecmov,$io_pdf); // Imprimimos el encabezado de la p?gina
			$io_pdf=uf_print_cabecera($ls_numdoc,$ls_nomban,$ls_ctaban,$ls_chevau,$ls_nomproben,$ls_solicitudes,$ls_conmov,$io_pdf); // Imprimimos la cabecera del registro
			
			$ds_dt_scg->data=$class_report->uf_cargar_dt_scg($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope); // Obtenemos el detalle del reporte
			$la_items = array('0'=>'scg_cuenta');
			$la_suma  = array('0'=>'monto');
			$ds_dt_scg->group_by($la_items,$la_suma,'scg_cuenta');
			$li_totrow_det=$ds_dt_scg->getRowCount("scg_cuenta");
			
			$ds_dt_spg->data=$class_report->uf_cargar_dt_spg($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope);
			$la_items = array('0'=>'estpro','1'=>'spg_cuenta');
			$la_suma  = array('0'=>'monto');
			$ds_dt_spg->group_by($la_items,$la_suma,'spg_cuenta');
			$li_totrow_spg=$ds_dt_spg->getRowCount("spg_cuenta");
			if (($li_totrow_det==0)||($li_totrow_spg==0))
			{
				$la_data=array();
			}
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// Ciclo para unir en una sola matriz los detalles de presupuesto y los contables para proceder luego a imprimirlos.
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			if ($li_totrow_det>=$li_totrow_spg)
			   {
				 for ($li_s=1;$li_s<=$li_totrow_det;$li_s++)
					 {
					   $ls_scg_cuenta = trim($ds_dt_scg->data["scg_cuenta"][$li_s]);
					   $ls_debhab     = $ds_dt_scg->data["debhab"][$li_s];
					   $ldec_monto    = $ds_dt_scg->data["monto"][$li_s];
					   if ($ls_debhab=='D')
						  {
							$ldec_mondeb = number_format($ldec_monto,2,",",".");
							$ldec_monhab = "";
						  }
					   else
						  {
							$ldec_monhab = number_format($ldec_monto,2,",",".");
							$ldec_mondeb = "";
						  }
					   if (array_key_exists("spg_cuenta",$ds_dt_spg->data))
						  {
							if (array_key_exists($li_s,$ds_dt_spg->data["spg_cuenta"]))
							   {
								 $ls_cuentaspg   = trim($ds_dt_spg->getValue("spg_cuenta",$li_s));
								 $ls_estpro      = $ds_dt_spg->getValue("estpro",$li_s);	  
								 $ldec_monto_spg = number_format($ds_dt_spg->getValue("monto",$li_s),2,",",".");
							   }
							else
							   {
								 $ls_cuentaspg   = "";	
								 $ls_estpro      = "";	  
								 $ldec_monto_spg = "";
							   }
						  }
					   else
						  {
							$ls_cuentaspg   = "";	
							$ls_estpro      = "";	  
							$ldec_monto_spg = "";
						  }
					   $la_data[$li_s]=array('spg_cuenta'=>$ls_cuentaspg,'estpro'=>$ls_estpro,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
					 }
			   }
			if ($li_totrow_spg>$li_totrow_det)
			   {
				 for ($li_s=1;$li_s<=$li_totrow_spg;$li_s++)
					 {
					   if (array_key_exists("scg_cuenta",$ds_dt_scg->data))
						  {
							if (array_key_exists($li_s,$ds_dt_scg->data["scg_cuenta"]))
							   {
								 $ls_scg_cuenta = trim($ds_dt_scg->data["scg_cuenta"][$li_s]);
								 $ls_debhab 	= $ds_dt_scg->data["debhab"][$li_s];
								 $ldec_monto	= $ds_dt_scg->data["monto"][$li_s];
								 if ($ls_debhab=='D')
									{
									  $ldec_mondeb = number_format($ldec_monto,2,",",".");
									  $ldec_monhab = "";
									}
								 else
									{
									  $ldec_monhab = number_format($ldec_monto,2,",",".");
									  $ldec_mondeb = "";
									}
							   }
							else
							   {
								 $ls_scg_cuenta = "";
								 $ls_debhab 	= "";
								 $ldec_monto	= "";
								 $ldec_mondeb	= "";
								 $ldec_monhab   = "";					
							   }
						  }
					   else
						  {
							$ls_scg_cuenta = "";
							$ls_debhab 	   = "";
							$ldec_monto	   = "";
							$ldec_mondeb   = "";
							$ldec_monhab   = "";					
						  }
					   if (array_key_exists("spg_cuenta",$ds_dt_spg->data))
						  {
							if (array_key_exists($li_s,$ds_dt_spg->data["spg_cuenta"]))
							   {
								 $ls_cuentaspg   = trim($ds_dt_spg->getValue("spg_cuenta",$li_s));
								 $ls_estpro      = $ds_dt_spg->getValue("estpro",$li_s);	  
								 $ldec_monto_spg = number_format($ds_dt_spg->getValue("monto",$li_s),2,",",".");
							   }
							else
							   {
								 $ls_cuentaspg   = "";	
								 $ls_estpro      = "";	  
								 $ldec_monto_spg = "";
							   }
						  }
					   else
						  {
							$ls_cuentaspg   = "";	
							$ls_estpro      = "";	  
							$ldec_monto_spg = "";
						  }
					   $la_data[$li_s]=array('spg_cuenta'=>$ls_cuentaspg,'estpro'=>$ls_estpro,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
					 }
			   }
			$io_pdf=uf_print_detalle(array('title'=>'Detalle Presupuestario Pago','title2'=>'Detalle Contable Pago'),$la_data,$io_pdf);
		}
		$io_pdf->ezStopPageNumbers(1,1);
		$io_pdf->ezStream();
		unset($io_pdf,$class_report,$io_funciones);
	}
?> 