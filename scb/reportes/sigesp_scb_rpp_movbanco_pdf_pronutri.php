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
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");//Estandar SIGESP C.A.
	header("Cache-Control: private",false);
	if (!array_key_exists("la_logusr",$_SESSION))
	   {
		 echo "<script language=JavaScript>";
		 echo "opener.document.form1.submit();"	;	
		 echo "close();";
		 echo "</script>";		
	   }	
	$x_pos		   = 0;//mientras mas grande el numero, mas a la derecha.
	$y_pos		   = -1;//Mientras mas pequeño el numero, mas alto.
	$ls_directorio = "cheque_configurable";
	$ls_archivo	   = "cheque_configurable/medidas.txt";
	$li_medidas    = 16;

	function uf_print_encabezado_pagina($ldec_monto,$ls_nomproben,$ls_monto,$ls_fecha,$ls_numdoc,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		    Acess: private 
		//	    Arguments: ldec_monto : Monto del cheque
		//	    		   ls_nomproben:  Nombre del proveedor o beneficiario
		//	    		   ls_monto : Monto en letras
		//	    		   ls_fecha : Fecha del cheque
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Néstor Falcón
		// Fecha Creación: 25/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $valores;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(15,40,585,40);
		$io_pdf->line(480,720,480,780);
		$io_pdf->line(480,750,585,750);
        $io_pdf->Rectangle(15,720,570,60);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],20,723,55,55); // Agregar Logo
		$io_pdf->addText(240,742,12,'<b>Movimiento de Banco</b>');
		$io_pdf->addText(483,760,9,"<b>Fecha: </b>"); // Agregar el título
		$io_pdf->addText(520,760,9,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(489,733,9,$ls_numdoc); // Agregar el título			
		$io_pdf->Rectangle(15,570,570,120);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezadopagina.

	function uf_print_cabecera($ls_numdoc,$ls_nomban,$ls_ctaban,$ls_chevau,$ls_nomproben,$ls_solicitudes,$ls_conmov,$ld_fecmov,$ls_operacion,$ls_tipodest,$ldec_monto,$io_pdf)
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
		//    Description: función que imprime los datos basicos del cheque
		//	   Creado Por: Ing. Néstor Falcón
		// Fecha Creación: 02/04/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$io_pdf->setStrokeColor(0,0,0);
		$li_pos=245;
		$io_pdf->convertir_valor_mm_px($li_pos);
		$io_pdf->ezSetY($li_pos);
		/*$la_data=array(array('banco'=>'<b>Banco</b>  ','cheque'=>'<b>Cheque Nº</b>  ','cuenta'=>'<b>Cuenta Nº:</b>  ','voucher'=>'<b>Voucher Nº:</b>  '),
						array('banco'=>$ls_nomban,'cheque'=>$ls_numdoc,'cuenta'=>$ls_ctaban,'voucher'=>$ls_chevau));
		$la_columna=array('banco'=>'','cheque'=>'','cuenta'=>'','voucher'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580,
						 'cols'=>array('banco'=>array('justification'=>'left','width'=>160),'cheque'=>array('justification'=>'left','width'=>100),
						 'cuenta'=>array('justification'=>'left','width'=>160),'voucher'=>array('justification'=>'left','width'=>160))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);*/
		if ($ls_tipodest=='P')
		{
			$ls_tit="Proveedor :";
		}
		else
		{
			$ls_tit="Beneficiario :";
		}
		$la_data=array(array('ordenes'=>'<b>Banco :</b>  '.$ls_nomban),
					   array('ordenes'=>'<b>Cuenta Nº :</b> '.$ls_ctaban),
					   array('ordenes'=>'<b>Número del Documento :</b> '.$ls_numdoc),
					   array('ordenes'=>'<b>Fecha :</b> '.$ld_fecmov),
					   array('ordenes'=>'<b>Tipo :</b> '.$ls_operacion),
					   array('ordenes'=>'<b>Concepto :</b> '.$ls_conmov),
					   array('ordenes'=>'<b>'.$ls_tit.'</b> '.$ls_nomproben),
					   array('ordenes'=>'<b>Solicitudes de Pago Asociadas</b> '.$ls_solicitudes),
					   array('ordenes'=>'<b>Monto :</b> '.$ldec_monto),);
		$la_columna=array('ordenes'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580,
						 'cols'=>array('ordenes'=>array('justification'=>'left','width'=>580))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		return $io_pdf;
	}// end function uf_print_cabecera.

	function uf_print_detalle($la_title,$la_data,$li_totrow_spi,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Néstor Falcón
		// Fecha Creación: 02/04/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$io_pdf->ezSetDy(-5);
		$io_pdf->setStrokeColor(0,0,0);
		$la_data_title=array($la_title);
		$io_pdf->set_margenes(90,55,0,0);
		$la_columna=array('title'=>'','title2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580,
						 'cols'=>array('title'=>array('justification'=>'center','width'=>350),'title2'=>array('justification'=>'center','width'=>230))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data_title,$la_columna,'',$la_config);	
		//Imprimo los detalles tanto `de presupuesto como contablwe del movimiento
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('estpro'=>array('justification'=>'center','width'=>195),
			 						   'spg_cuenta'=>array('justification'=>'center','width'=>80),
									   'monto_spg'=>array('justification'=>'right','width'=>75),
						 			   'scg_cuenta'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'debe'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'haber'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la columna
		if($li_totrow_spi<1)
		{
			$la_columnas=array('estpro'=>'<b>Programatica</b>',
							   'spg_cuenta'=>'<b>Cuenta</b>',
							   'monto_spg'=>'<b>Monto</b>',
							   'scg_cuenta'=>'<b>Cuenta</b>',
							   'debe'=>'<b>Debe</b>',
							   'haber'=>'<b>Haber</b>');
		}
		else
		{
			$la_columnas=array('spg_cuenta'=>'<b>Cuenta</b>',
							   'estpro'=>'<b>Denominacion</b>',
							   'monto_spg'=>'<b>Monto</b>',
							   'scg_cuenta'=>'<b>Cuenta</b>',
							   'debe'=>'<b>Debe</b>',
							   'haber'=>'<b>Haber</b>');
		}
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$io_pdf->ezText('                     ',10);//Inserto una linea en blanco
		return $io_pdf;
	}// end function uf_print_detalle.
	
	function uf_print_autorizacion($ls_nomusureg,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_autorizacion
		//		    Acess: private 
		//	    Arguments: io_pdf // Objeto PDF
		//    Description: función el final del voucher 
		//	   Creado Por: Ing. Néstor Falcón
		// Fecha Creación: 25/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);		
		$io_pdf->Rectangle(11,43,580,105);
		$io_pdf->line(11,90,590,90);
		$io_pdf->line(11,74.6,590,74.6);		
		$io_pdf->line(127,90,127,148);
		$io_pdf->line(243,90,243,148);
		$io_pdf->line(359,90,359,148);
		$io_pdf->line(475,90,475,148);		
		$io_pdf->line(191,43,191,75);
		$io_pdf->line(310.5,43,310.5,75);
		$io_pdf->line(411,43,411,75);	
		
		$io_pdf->addText(16,137.6,9,'<b>Elaborado por:</b>');
		$io_pdf->addText(132,137.6,9,'<b>Revisado por:</b>');
		$io_pdf->addText(273,137.6,9,'<b>Presupuesto</b>');		
		$io_pdf->addText(384,137.6,9,'<b>Administración</b>');		
		$io_pdf->addText(508,137.6,9,'<b>Presidencia</b>');
		$io_pdf->addText(258,78.85,10,'<b>Recibí Conforme</b>');
		$io_pdf->addText(16,95,10,$ls_nomusureg);	
		
		$io_pdf->addText(16,63.27,10,'<b>Nombre:</b>');		
		$io_pdf->addText(196,63.27,10,'<b>Cédula de Identidad:</b>');		
		$io_pdf->addText(316,63.27,10,'<b>Fecha:</b>');
		$io_pdf->addText(416,63.27,10,'<b>Firma:</b>');
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
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
$ds_dt_spi	  = new class_datastore();
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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$ls_codemp = $_SESSION["la_empresa"]["codemp"];
$ls_codban = $_GET["codban"];
$ls_ctaban = $_GET["ctaban"];
$ls_numdoc = $_GET["numdoc"];
$ls_chevau = $_GET["chevau"];
$ls_codope = $_GET["codope"];
if(trim($_GET["numcarord"])<>'')
{
	$ls_numdoc = $_GET["numcarord"];
}
$ls_nomusureg = '';				

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
$io_pdf->transaction('start'); // Iniciamos la transacción
$thisPageNum=$io_pdf->ezPageCount;
$io_pdf->ezStartPageNumbers(570,30,10,'','',1); // Insertar el número de página
//uf_print_autorizacion($ls_nomusureg,$io_pdf);	

for($li_i=1;$li_i<=$li_totrow;$li_i++)
{
	unset($la_data);
	$li_totprenom = 0;
	$ldec_mondeb  = 0;
	$ldec_monhab  = 0;
	$li_totant	  = 0;
	$ls_numdoc		= $ds_voucher->data["numdoc"][$li_i];
	$ls_documento		= $ds_voucher->data["numdoc"][$li_i];
	$ls_codban		= $ds_voucher->data["codban"][$li_i];
	$ls_operacion   = $ds_voucher->data["codope"][$li_i];
	$ls_nomusureg = $ds_voucher->data["nomusureg"][$li_i].' '.$ds_voucher->data["apeusureg"][$li_i];
	$ls_solicitudes = $class_report->uf_select_solicitudes($ls_numdoc,$ls_codban,$ls_ctaban);
	
	if ($ls_operacion=='ND')
	{
		$ls_operacion='NOTA DE DEBITO';
		$ls_documento		= $ds_voucher->data["numcarord"][$li_i];
	}
	elseif ($ls_operacion=='NC')
	{
		$ls_operacion='NOTA DE CREDITO';
	}
	elseif ($ls_operacion=='DP')
	{
		$ls_operacion='DEPOSITO';
	}
	if ($ls_operacion=='RE')
	{
		$ls_operacion='RETIRO';
	}
	$ls_nomban		= $class_report->uf_select_data($io_sql,"SELECT nomban FROM scb_banco WHERE codban ='".$ls_codban."' AND codemp='".$ls_codemp."'","nomban");
	$ls_ctaban		= $ds_voucher->data["ctaban"][$li_i];
	$ls_chevau		= $ds_voucher->data["chevau"][$li_i];
	$ld_fecmov	  	= $io_funciones->uf_convertirfecmostrar($ds_voucher->data["fecmov"][$li_i]);
	$ls_nomproben 	= $ds_voucher->data["nomproben"][$li_i];
	$ls_conmov		= $ds_voucher->getValue("conmov",$li_i);
	$ldec_monret	= $ds_voucher->getValue("monret",$li_i);
	$ldec_monto		= $ds_voucher->getValue("monto",$li_i);
	$ls_tipodest	= $ds_voucher->getValue("tipo_destino",$li_i);
	$ldec_total		= $ldec_monto-$ldec_monret;
	//Asigno el monto a la clase numero-letras para la conversion.
	$numalet->setNumero($ldec_total);
	//Obtengo el texto del monto enviado.
	$ls_monto= $numalet->letra();
	$io_pdf=uf_print_encabezado_pagina(number_format($ldec_total,2,",","."),$ls_nomproben,$ls_monto,$_SESSION["la_empresa"]["ciuemp"].", ".$ld_fecmov,$ls_numdoc,$io_pdf); // Imprimimos el encabezado de la página
	$io_pdf=uf_print_cabecera($ls_documento,$ls_nomban,$ls_ctaban,$ls_chevau,$ls_nomproben,$ls_solicitudes,$ls_conmov,$ld_fecmov,$ls_operacion,$ls_tipodest,number_format($ldec_total,2,",","."),$io_pdf); // Imprimimos la cabecera del registro
	
	$ds_dt_scg->data=$class_report->uf_cargar_dt_scg($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope); // Obtenemos el detalle del reporte
	$la_items = array('0'=>'scg_cuenta','1'=>'debhab');
	$la_suma  = array('0'=>'monto');
	$ds_dt_scg->group_by($la_items,$la_suma,'scg_cuenta');
	$li_totrow_det=$ds_dt_scg->getRowCount("scg_cuenta");
	
	$ds_dt_spg->data=$class_report->uf_cargar_dt_spg($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope);
	$la_items = array('0'=>'estpro','1'=>'spg_cuenta');
	$la_suma  = array('0'=>'monto');
	$ds_dt_spg->group_by($la_items,$la_suma,'spg_cuenta');
	$li_totrow_spg=$ds_dt_spg->getRowCount("spg_cuenta");
	
	$ds_dt_spi->data=$class_report->uf_cargar_dt_spi($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope);
	$la_items = array('0'=>'spi_cuenta');
	$la_suma  = array('0'=>'monto');
	$ds_dt_spi->group_by($la_items,$la_suma,'spi_cuenta');
	$li_totrow_spi=$ds_dt_spi->getRowCount("spi_cuenta");
	
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
			   if ((array_key_exists("spi_cuenta",$ds_dt_spi->data))&&($li_totrow_spi > 0)) // Para la parte de los ingresos
				  {
					if (array_key_exists($li_s,$ds_dt_spi->data["spi_cuenta"]))
					   {
						 $ls_cuentaspi   = trim($ds_dt_spi->getValue("spi_cuenta",$li_s));
						 $ls_denominacion= $ds_dt_spi->getValue("denominacion",$li_s);	  
						 $ldec_monto_spi = number_format($ds_dt_spi->getValue("monto",$li_s),2,",",".");
					   }
					else
					   {
						 $ls_cuentaspi   = "";	
						 $ls_denominacion      = "";	  
						 $ldec_monto_spi = "";
					   }
				  }// Para la parte de los ingresos
			   
			   if ($li_totrow_spi > 0)
			   {
			   		$la_data[$li_s]=array('spg_cuenta'=>$ls_cuentaspi,'estpro'=>$ls_denominacion,'monto_spg'=>$ldec_monto_spi,'scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
			   }
			   else
			   {
			   		$la_data[$li_s]=array('spg_cuenta'=>$ls_cuentaspg,'estpro'=>$ls_estpro,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
			   }
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
				  
				if ((array_key_exists("spi_cuenta",$ds_dt_spi->data))&&($li_totrow_spi > 0)) // Para la parte de los ingresos
				  {
					if (array_key_exists($li_s,$ds_dt_spi->data["spi_cuenta"]))
					   {
						 $ls_cuentaspi   = trim($ds_dt_spi->getValue("spi_cuenta",$li_s));
						 $ls_denominacion= $ds_dt_spi->getValue("denominacion",$li_s);	  
						 $ldec_monto_spi = number_format($ds_dt_spi->getValue("monto",$li_s),2,",",".");
					   }
					else
					   {
						 $ls_cuentaspi   = "";	
						 $ls_denominacion      = "";	  
						 $ldec_monto_spi = "";
					   }
				  }// Para la parte de los ingresos
			   
			   if ($li_totrow_spi > 0)
			   {
			   		$la_data[$li_s]=array('spg_cuenta'=>$ls_cuentaspi,'estpro'=>$ls_denominacion,'monto_spg'=>$ldec_monto_spi,'scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
			   }
			   else
			   {
			   		$la_data[$li_s]=array('spg_cuenta'=>$ls_cuentaspg,'estpro'=>$ls_estpro,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
			   }  
		 }
	   }
	if (empty($la_data))
	   {
		 $ls_cuentaspg	 = '';
		 $ls_estpro		 = '';
		 $ldec_monto_spg = '';
		 $ls_scg_cuenta  = '';
		 $ldec_mondeb	 = '';
		 $ldec_monhab	 = '';
		 $la_data[1]=array('spg_cuenta'=>$ls_cuentaspg,'estpro'=>$ls_estpro,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
		 $la_data[2]=array('spg_cuenta'=>$ls_cuentaspg,'estpro'=>$ls_estpro,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
		 $la_data[3]=array('spg_cuenta'=>$ls_cuentaspg,'estpro'=>$ls_estpro,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
	   }
	if ($li_totrow_spi > 0)
	{
		$io_pdf=uf_print_detalle(array('title'=>'Detalle de Ingreso','title2'=>'Detalle Contable Pago'),$la_data,$li_totrow_spi,$io_pdf);
	}
	else
	{
		$io_pdf=uf_print_detalle(array('title'=>'Detalle Presupuestario Pago','title2'=>'Detalle Contable Pago'),$la_data,$li_totrow_spi,$io_pdf);
	}
	
}
uf_print_autorizacion($ls_nomusureg,$io_pdf);
$io_pdf->ezStopPageNumbers(1,1);
$io_pdf->ezStream();
unset($io_pdf);
unset($class_report);
unset($io_funciones);

?> 