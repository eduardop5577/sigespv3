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

	function uf_inicializar_variables()
	{
		global $valores;
		global $ls_directorio;
		global $ls_archivo;	
		global $li_medidas;	
		if(!file_exists ($ls_directorio))
		{
			$lb_exito=mkdir($ls_directorio,0777);
			if(!$lb_exito)
			{
				print "<script>";
				print "alert('Error al crear directorio cheque_configurable');";
				print "close();";
				print "</script>";
			}
		}
		
		if((!file_exists ($ls_archivo)) || (filesize($ls_archivo)==0))
		{	
			if(file_exists ($ls_directorio))
			{
				$archivo = fopen($ls_archivo, "w");			
				$ls_contenido="138.00-6.00-32.00-24.00-32.00-28.00-32.00-34.00-32.00-43.00-77.00-43.00-137.00-65.00-131.00-70.00";
				fwrite($archivo,$ls_contenido);
				fclose($archivo);
			}
		}
			
		if((file_exists($ls_archivo)) && (filesize($ls_archivo)>0))
		{
			$archivo = fopen($ls_archivo, "r");
			$contenido = fread($archivo, filesize($ls_archivo));			
			fclose($archivo);			
			$valores = explode("-",$contenido);	
			if(count((array)$valores)<>$li_medidas)
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
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Néstor Falcón
		// Fecha Creación: 25/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
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
	}// end function uf_print_encabezadopagina.

	
	
	function uf_print_data_cheque($as_ordenpago, $as_concepto, $as_nomban, $as_ctaban, $as_numdoc, $io_pdf) {
		global $io_pdf;
		$io_pdf->addText(150,460,9,'<b>'.$as_ordenpago.'</b>');
		$as_concepto = $io_pdf->addTextWrap(60,415,550,9,'<b>'.$as_concepto.'</b>');
		$as_concepto = $io_pdf->addTextWrap(60,405,550,9,'<b>'.$as_concepto.'</b>');
		$as_concepto = $io_pdf->addTextWrap(60,395,550,9,'<b>'.$as_concepto.'</b>');
		$as_concepto = $io_pdf->addTextWrap(60,385,550,9,'<b>'.$as_concepto.'</b>');
		
		
		$io_pdf->addText(75,145,10,'<b>'.substr($as_numdoc, -6).'</b>');
		$io_pdf->addText(230,145,10,'<b>'.$as_nomban.'</b>');		
		$io_pdf->addText(450,145,10,'<b>'.$as_ctaban.'</b>');
	}

uf_inicializar_variables();
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
			
		//Descompongo la cadena de documentos en un arreglo tomando como separación el ','
		$arr_documentos = explode(",",$ls_documentos);
		$li_totdoc		= count((array)$arr_documentos);
		//Descompongo la cadena de fechas en un arreglo tomando como separación el '-'
		$arr_fecmov = explode("-",$ls_fechas);
		$li_totfec  = count((array)$arr_fecmov);
	   //Descompongo la cadena de operaciones en un arreglo tomando como separación el '-'
		$arr_operaciones = explode("-",$ls_operaciones);
		$li_totdoc	= count((array)$arr_operaciones);		
		$class_report->uf_buscar_cheques_vouchers($arr_documentos,$arr_fecmov,$arr_operaciones,$ls_codban,$ls_ctaban);
		
		$li_total   = $class_report->ds_voucher1->getRowCount("numdoc");

		if ($li_total>0)
		{	
			
			set_time_limit(1800);
			$io_pdf=new class_pdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->set_margenes(0,5,0,0);
		    $io_pdf->ezStartPageNumbers(570,30,10,'','',1); // Insertar el número de página
			if ((trim($ls_codban)=="001"))//Banco provincial
			   {
			   $ls_archivo   = "cheque_configurable/medidas_provincial.txt";
			   $ls_contenido = "144.00-0.00-24.00-20.00-26.00-30.00-27.00-33.00-22.00-38.00-79.00-38.00-120.00-60.00-130.00-65.00-10.00-91.00-8.00-100.00-12.00-117.00";
			   $li_medidas   = 22;
			   }
			elseif(trim($ls_codban)=="002")//Banco bicentenario
			   {
			   $ls_archivo   = "cheque_configurable/medidas_bicentenario.txt";
			   $ls_contenido = "144.00-0.00-24.00-20.00-26.00-30.00-27.00-33.00-22.00-38.00-79.00-38.00-120.00-60.00-130.00-65.00";
			   $li_medidas   = 16;
			   }
			elseif(trim($ls_codban)=="003")//Banco del tesoro
			   {
			     $ls_archivo   = "cheque_configurable/medidas_tesoro.txt";
				 $ls_contenido="167.00-132.00-65.00-148.00-65.00-154.00-65.00-157.00-65.00-168.00-80.00-168.00-1000.00-1000.00-1000.00-1000.00";
				 $li_medidas   = 16;
			   }
			elseif(trim($ls_codban)=="004")//Banco Venezuela
			   {
				 $ls_archivo   = "cheque_configurable/medidas_venezuela.txt";
				 $ls_contenido = "144.00-0.00-24.00-20.00-26.00-30.00-27.00-33.00-22.00-38.00-79.00-38.00-120.00-60.00-130.00-65.00-10.00-91.00-8.00-100.00-12.00-117.00";
				 $li_medidas   = 22;
			   }
			else
			   {
			     $ls_archivo   = "cheque_configurable/medidas.txt";
			     $ls_contenido = "144.00-0.00-24.00-20.00-26.00-30.00-27.00-33.00-22.00-38.00-79.00-38.00-120.00-60.00-130.00-65.00-10.00-91.00-8.00-100.00-12.00-117.00";
			     $li_medidas   = 22;
			   }
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
				$io_pdf->transaction('start'); // Iniciamos la transacción
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
					$ls_rifpro		= $ds_voucher->getValue("rifpro",$li_i);
					$ls_cedben		= $ds_voucher->getValue("cedben",$li_i);
					$ls_cedrif = '';
					if ($ls_rifpro != '') {
						$ls_cedrif = $ls_rifpro;
					}
					else {
						$ls_cedrif = $ls_cedben;
					}
					//Asigno el monto a la clase numero-letras para la conversion.
					$numalet->setNumero($ldec_total);
					//Obtengo el texto del monto enviado.
					$ls_monto= $numalet->letra();
					uf_print_encabezado_pagina(number_format($ldec_total,2,",","."),$ls_nomproben,$ls_monto,$_SESSION["la_empresa"]["ciuemp"].", ".$ld_fecmov,$io_pdf); // Imprimimos el encabezado de la página
					
							
				} // Fin del for 2
			   // uf_print_autorizacion($io_pdf);	
				if ($i<$li_total)
				{			
					$io_pdf->ezNewPage(); // Insertar una nueva página
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
		$io_pdf->set_margenes(0,5,0,0);
		$li_totrow=$ds_voucher->getRowCount("numdoc");
		$io_pdf->transaction('start'); // Iniciamos la transacción
		$thisPageNum=$io_pdf->ezPageCount;
		$io_pdf->ezStartPageNumbers(570,30,10,'','',1); // Insertar el número de página
		//uf_print_autorizacion($io_pdf);	
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
			$ls_rifpro		= $ds_voucher->getValue("rifpro",$li_i);
			$ls_cedben		= $ds_voucher->getValue("cedben",$li_i);
			$ls_cedrif = '';
			if ($ls_rifpro != '') {
				$ls_cedrif = $ls_rifpro;
			}
			else {
				$ls_cedrif = $ls_cedben;
			}
			//Asigno el monto a la clase numero-letras para la conversion.
			$numalet->setNumero($ldec_total);
			//Obtengo el texto del monto enviado.
			$ls_monto= $numalet->letra();
			uf_print_encabezado_pagina(number_format($ldec_total,2,",","."),$ls_nomproben,$ls_monto,$_SESSION["la_empresa"]["ciuemp"].", ".$ld_fecmov,$io_pdf); // Imprimimos el encabezado de la página
			uf_print_data_cheque($ls_solicitudes, $ls_conmov, $ls_nomban, $ls_ctaban, $ls_numdoc, $io_pdf);
			
		}
		$io_pdf->ezStopPageNumbers(1,1);
		$io_pdf->ezStream();
		unset($io_pdf);
		unset($class_report);
		unset($io_funciones);
	}
?> 