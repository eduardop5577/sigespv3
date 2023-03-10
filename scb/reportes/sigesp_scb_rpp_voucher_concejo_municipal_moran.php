<?PHP
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
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");//Concejo Municipal del Municipio Mor?n.
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "opener.document.form1.submit();"	;	
		print "close();";
		print "</script>";		
	}	
	$x_pos=0;//mientras mas grande el numero, mas a la derecha.
	$y_pos=-1;//Mientras mas peque?o el numero, mas alto.
	$ls_directorio="cheque_configurable";
	$ls_archivo="cheque_configurable/medidas.txt";
	$li_medidas=16;
	//-------------------------------------------------------------------------------------------------
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
	}
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($ldec_monto,$ls_nomproben,$ls_monto,$ls_fecha,$io_pdf,$x)
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
		// Fecha Creaci?n: 25/06/2007 
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
		$io_pdf->add_texto($valores[14],$valores[15],9,"<b>Caduca a los 90 d?as</b>");	
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
   //--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf,$x_pos)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. N?stor Falc?n
		// Fecha Creaci?n: 24/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		//Imprimo los detalles tanto `de presupuesto como contablwe del movimiento
		$li_pos=154;//160
		$io_pdf->convertir_valor_mm_px($li_pos);		
		$io_pdf->ezSetY($li_pos);
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho M?ximo de la tabla
						 'colGap'=> 1, //Separacion de los caracteres entre las barras de las tablas.
						 'xPos'=>270, // Orientaci?n de la tabla
						 'cols'=>array('codestpro'=>array('justification'=>'center','width'=>60),
						 			   'spg_cuenta'=>array('justification'=>'center','width'=>45),
			 						   'denctaspg'=>array('justification'=>'left','width'=>300),									   
									   'monto_spg'=>array('justification'=>'right','width'=>70))); // Justificaci?n y ancho de la columna
		$la_columnas=array('codestpro'=>'',
						   'spg_cuenta'=>'',
						   'denctaspg'=>'',
						   'monto_spg'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);

		/*$io_pdf->set_margenes(0,0,0,0);		
	   	$li_pos=110;//100
		$io_pdf->convertir_valor_mm_px($li_pos);		
		$io_pdf->ezSetY($li_pos);*/
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho M?ximo de la tabla
						 'colGap'=> 1, //Separacion de los caracteres entre las barras de las tablas.
						 'xPos'=>270, // Orientaci?n de la tabla
						 'cols'=>array('scg_cuenta'=>array('justification'=>'center','width'=>60),
						               'denctascg'=>array('justification'=>'left','width'=>275),  
			 						   'debe'=>array('justification'=>'right','width'=>70),
			 						   'haber'=>array('justification'=>'right','width'=>70))); // Justificaci?n y ancho de la columna
		$la_columnas=array('scg_cuenta'=>'',
		                   'denctascg'=>'',
						   'debe'=>'',
						   'haber'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_pagina($ls_numdoc,$ls_nomban,$ls_ctaban,$ls_nomproben,$ls_fecmov,$ls_conmov,$ls_chevau,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_pagina
		//		   Access: private 
		//	    Arguments: ls_numdoc : Numero de documento
		//	    		   ls_nomban : Nombre del banco
		//				   ls_cbtan  : Cuenta del banco
		//				   ls_chevau : Voucher del cheuqe
		//				   ls_nomproben: Nombre del proveedor o beneficiario
		//				   ls_solicitudes: Solicitudes canceladas con el cheque					  
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funci?n que imprime los datos basicos del cheque
		//	   Creado Por: Ing. N?stor Falc?n.
		// Fecha Creaci?n: 04/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$ls_chevau = substr($ls_chevau,-12);
		$io_pdf->add_texto(155,110,10,$ls_chevau);
		$li_pos=117;//108
		$io_pdf->convertir_valor_mm_px($li_pos);		
		$io_pdf->ezSetY($li_pos);
		$ls_nomban = substr($ls_nomban,0,20);
		$la_data=array(array('cheque'=>'','banco'=>''),
						array('cheque'=>$ls_numdoc,'banco'=>$ls_nomban));
		$la_columna=array('cheque'=>'','banco'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' =>7, // Tama?o de Letras
						 'colGap' =>1, // Tama?o de Letras
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>135, // Orientaci?n de la tabla
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580,
						 'cols'=>array('cheque'=>array('justification'=>'center','width'=>150),
						 'banco'=>array('justification'=>'left','width'=>115))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_pagina
	//--------------------------------------------------------------------------------------------------------------------------------


	function uf_convertir($ls_numero)
	{
		$ls_numero=str_replace(".","",$ls_numero);
		$ls_numero=str_replace(",",".",$ls_numero);
		return $ls_numero;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	//require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	 uf_inicializar_variables();
	require_once('../../shared/class_folder/class_pdf.php');
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
	$in=new sigesp_include();
	$con=$in->uf_conectar();
	$io_sql=new class_sql($con);	
	require_once("sigesp_scb_report.php");
	$class_report=new sigesp_scb_report($con);
	$io_funciones=new class_funciones();				
	$ds_voucher=new class_datastore();	
	$ds_dt_scg=new class_datastore();				
	$ds_dt_spg=new class_datastore();
	//Instancio a la clase de conversi?n de numeros a letras.
	require_once("../../shared/class_folder/cnumero_letra.php");
	$numalet= new cnumero_letra();
	
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
			$io_pdf->set_margenes(0,0,$x_pos,0);	
			
			for ($i=1;$i<=$li_total;$i++)
			{
				$ls_numdoc=$class_report->ds_voucher1->getValue("numdoc",$i);
				
				$ls_chevau=$class_report->ds_voucher1->getValue("chevau",$i);
				$ls_codope='CH';
				
				$data=$class_report->uf_cargar_chq_voucher($ls_numdoc,$ls_chevau,$ls_codban,$ls_ctaban,$ls_codope);
			$class_report->SQL->begin_transaction();
			$lb_valido=$class_report->uf_actualizar_status_impreso($ls_numdoc,$ls_chevau,$ls_codban,$ls_ctaban,$ls_codope);
			if(!$lb_valido)
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
			$io_pdf->set_margenes(0,0,$x_pos,0);	
			$li_totrow=$ds_voucher->getRowCount("numdoc");
			$io_pdf->transaction('start'); // Iniciamos la transacci?n
			$thisPageNum=$io_pdf->ezPageCount;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				unset($la_data);
				$li_totprenom	= 0;
				$ldec_mondeb	= 0;
				$ldec_monhab	= 0;
				$li_totant		= 0;
				$ls_numdoc	    = $ds_voucher->data["numdoc"][$li_i];
				$ls_codban	    = $ds_voucher->data["codban"][$li_i];
				$ls_nomban	    = $class_report->uf_select_data($io_sql,"SELECT * FROM scb_banco WHERE codban ='".$ls_codban."' AND codemp='".$ls_codemp."'","nomban");
				$ls_ctaban	    = $ds_voucher->data["ctaban"][$li_i];
				$ls_chevau	    = $ds_voucher->data["chevau"][$li_i];
				$ld_fecmov	    = $io_funciones->uf_convertirfecmostrar($ds_voucher->data["fecmov"][$li_i]);
				$ls_nomproben   = $ds_voucher->data["nomproben"][$li_i];
				$ls_solicitudes = $class_report->uf_select_solicitudes($ls_numdoc,$ls_codban,$ls_ctaban);
				$ls_conmov		= $ds_voucher->getValue("conmov",$li_i);
				$ldec_monret    = $ds_voucher->getValue("monret",$li_i);
				$ldec_monto	    = $ds_voucher->getValue("monto",$li_i);
				$ldec_total	    = $ldec_monto-$ldec_monret;
				$ls_monto	    = $numalet->uf_convertir_letra($ldec_total,'','');
				$io_encabezado  = $io_pdf->openObject();
				uf_print_encabezado_pagina(number_format($ldec_total,2,",","."),$ls_nomproben,$ls_monto,$_SESSION["la_empresa"]["ciuemp"].", ".$ld_fecmov,$io_pdf,$y_pos); // Imprimimos el encabezado de la p?gina
				uf_print_pie_pagina($ls_numdoc,$ls_nomban,$ls_ctaban,$ls_nomproben,$ld_fecmov,$ls_conmov,$ls_chevau,$io_pdf); // Imprimimos la cabecera del registro
				$io_pdf->closeObject();
				$io_pdf->addObject($io_encabezado,'all');
				$ds_dt_scg->data=$class_report->uf_cargar_dt_scg($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope); // Obtenemos el detalle del reporte
				$ds_dt_spg->data=$class_report->uf_cargar_dt_spg($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope);
				$la_campos=array("scg_cuenta");
				$la_monto=array("monto");
				$ds_dt_scg->group_by($la_campos,$la_monto,"scg_cuenta");
				$la_campos=array("spg_cuenta","estpro");
				$ds_dt_spg->group_by($la_campos,$la_monto,"spg_cuenta");
				$li_totrow_det=$ds_dt_scg->getRowCount("scg_cuenta");
				$li_totrow_spg=$ds_dt_spg->getRowCount("spg_cuenta");
				$la_contable = array();
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// Hago un ciclo para unir en una sola matriz los detalles de presupuesto y los contables para proceder luego a pintarlos
				if($li_totrow_det>=$li_totrow_spg)
				{
					for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
					{
						$ls_scg_cuenta = $ds_dt_scg->data["scg_cuenta"][$li_s];
						$ls_denctascg  = substr($ds_dt_scg->data["denominacion"][$li_s],0,80);
						$ls_debhab     = $ds_dt_scg->data["debhab"][$li_s];
						$ldec_monto    = $ds_dt_scg->data["monto"][$li_s];
						
						if($ls_debhab=='D')
						{
							$ldec_mondeb=number_format($ldec_monto,2,",",".");
							$ldec_monhab=" ";
						}
						else
						{
							$ldec_monhab=number_format($ldec_monto,2,",",".");
							$ldec_mondeb=" ";
						}
						if(array_key_exists("spg_cuenta",$ds_dt_spg->data))
						{
							if(array_key_exists($li_s,$ds_dt_spg->data["spg_cuenta"]))
							{
								$ls_cuentaspg   = $ds_dt_spg->getValue("spg_cuenta",$li_s);	
								$ls_denctaspg   = substr($ds_dt_spg->getValue("denominacion",$li_s),0,80);	
								$ls_codestpro   = $ds_dt_spg->getValue("estpro",$li_s);	  
								$ldec_monto_spg = number_format($ds_dt_spg->getValue("monto",$li_s),2,",",".");
							}
							else
							{
								$ls_cuentaspg=" ";	
								$ls_codestpro=" ";	  
								$ldec_monto_spg=" ";
								$ls_denctaspg = "";
							}
						}
						else
						{
							$ls_cuentaspg=" ";	
							$ls_codestpro=" ";	  
							$ldec_monto_spg=" ";
							$ls_denctaspg = "";
						}
						$la_data[$li_s]=array('codestpro'=>$ls_codestpro,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'denctascg'=>$ls_denctascg,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
					}
				}
				
				if($li_totrow_spg>$li_totrow_det)
				{
					for($li_s=1;$li_s<=$li_totrow_spg;$li_s++)
					{
						if(array_key_exists("scg_cuenta",$ds_dt_scg->data))
						{
							if(array_key_exists($li_s,$ds_dt_scg->data["scg_cuenta"]))
							{
								$ls_scg_cuenta = $ds_dt_scg->data["scg_cuenta"][$li_s];
								$ls_denctascg  = substr($ds_dt_scg->data["denominacion"][$li_s],0,80);
								$ls_debhab     = $ds_dt_scg->data["debhab"][$li_s];
								$ldec_monto    = $ds_dt_scg->data["monto"][$li_s];
								if($ls_debhab=='D')
								{
									$ldec_mondeb=number_format($ldec_monto,2,",",".");
									$ldec_monhab=" ";
								}
								else
								{
									$ldec_monhab=number_format($ldec_monto,2,",",".");
									$ldec_mondeb=" ";
								}
							}
							else
							{
								$ls_scg_cuenta="";
								$ls_denctascg  = "";
								$ls_debhab = "";
								$ldec_monto= "";
								$ldec_mondeb="";
								$ldec_monhab="";					
							}
						}
						else
						{
							$ls_scg_cuenta="";
							$ls_denctascg  = "";
							$ls_debhab = "";
							$ldec_monto= "";
							$ldec_mondeb="";
							$ldec_monhab="";					
						}
						if(array_key_exists("spg_cuenta",$ds_dt_spg->data))
						{
							if(array_key_exists($li_s,$ds_dt_spg->data["spg_cuenta"]))
							{
								$ls_cuentaspg   = $ds_dt_spg->getValue("spg_cuenta",$li_s);	
								$ls_denctaspg   = substr($ds_dt_spg->getValue("denominacion",$li_s),0,80);	
								$ls_codestpro   = $ds_dt_spg->getValue("estpro",$li_s);
								$ldec_monto_spg = number_format($ds_dt_spg->getValue("monto",$li_s),2,",",".");
							}
							else
							{
								$ls_cuentaspg   = "";	
								$ls_denctaspg   = "";
								$ls_codestpro   = "";
								$ldec_monto_spg = "";
							}
						}
						else
						{
							$ls_cuentaspg 	= "";
							$ls_denctaspg 	= "";
							$ls_codestpro	= "";	  
							$ldec_monto_spg = "";
						}
						$la_data[$li_s]=array('codestpro'=>$ls_codestpro,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'denctascg'=>$ls_denctascg,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
					}
					
				}
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				if(empty($la_data))
				{
					$ls_cuentaspg='';
					$ls_codestpro='';
					$ldec_monto_spg='';
					$ls_scg_cuenta='';
					$ldec_mondeb='';
					$ldec_monhab='';
					$la_data[1]=array('codestpro'=>$ls_codestpro,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'denctascg'=>$ls_denctascg,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
					$la_data[2]=array('codestpro'=>$ls_codestpro,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'denctascg'=>$ls_denctascg,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
					$la_data[3]=array('codestpro'=>$ls_codestpro,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'denctascg'=>$ls_denctascg,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
				}
				//codigo para unir las cuentas iguales
				$la_dataaux=array();
				for($li_k=1;$li_k<=count((array)$la_data);$li_k++)
				{
					$lb_existe=false;
					$li_pos=0;
					for($li_l=1;$li_l<=count((array)$la_dataaux);$li_l++)
					{
						if(($la_data[$li_k]["spg_cuenta"]==$la_dataaux[$li_l]["spg_cuenta"])
							&& ($la_data[$li_k]["codestpro"]==$la_dataaux[$li_l]["codestpro"])
							&& ($la_data[$li_k]["scg_cuenta"]==$la_dataaux[$li_l]["scg_cuenta"]))
						{
							$li_pos=$li_i;
							$lb_existe=true;
						}
					}
					if(!$lb_existe)
					{
						$li_index=count((array)$la_dataaux)+1;
						$la_dataaux[$li_index]=$la_data[$li_k];
					}
					else
					{
						
						$ls_monto_spg1   = uf_convertir($la_dataaux[$li_pos]["monto_spg"]);
						$ls_monto_spg2   = uf_convertir($la_data[$li_k]["monto_spg"]);
						$ls_monto_debe1  = uf_convertir($la_dataaux[$li_pos]["debe"]);
						$ls_monto_debe2  = uf_convertir($la_data[$li_k]["debe"]);
						$ls_monto_haber1 = uf_convertir($la_dataaux[$li_pos]["haber"]);
						$ls_monto_haber2 = uf_convertir($la_data[$li_k]["haber"]);
						$la_dataaux[$li_pos]["monto_spg"] = number_format(($ls_monto_spg1 + $ls_monto_spg2),2,",",".");
						if (($ls_monto_debe1 + $ls_monto_debe2) != 0)
							$la_dataaux[$li_pos]["debe"] = number_format(($ls_monto_debe1 + $ls_monto_debe2),2,",",".");
						else
							$la_dataaux[$li_pos]["debe"]="";
						if(($ls_monto_haber1 + $ls_monto_haber2) != 0)
							$la_dataaux[$li_pos]["haber"] = number_format(($ls_monto_haber1 + $ls_monto_haber2),2,",",".");
						else
							$la_dataaux[$li_pos]["haber"]="";
					}
					
				}
				
				$io_pdf->y=190;
				$io_pdf->y=440;	
				$io_pdf->set_margenes(138,70,$x_pos,0);
				uf_print_detalle($la_dataaux,$io_pdf,$x_pos); // Imprimimos el detalle 	
			} // Fin del for 2
			
				if ($i<$li_total)
				{			
					$io_pdf->ezNewPage(); // Insertar una nueva p?gina
					$io_pdf->set_margenes(0,0,$x_pos,0);
				}
		
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
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// SI NO ES POR LOTE ENTONCES
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	else 
	{
		
			$ls_codemp=$_SESSION["la_empresa"]["codemp"];
			$ls_codban=$_GET["codban"];
			$ls_ctaban=$_GET["ctaban"];
			$ls_numdoc=$_GET["numdoc"];
			$ls_chevau=$_GET["chevau"];
			$ls_codope=$_GET["codope"];				
		
			$data=$class_report->uf_cargar_chq_voucher($ls_numdoc,$ls_chevau,$ls_codban,$ls_ctaban,$ls_codope);
			$class_report->SQL->begin_transaction();
			$lb_valido=$class_report->uf_actualizar_status_impreso($ls_numdoc,$ls_chevau,$ls_codban,$ls_ctaban,$ls_codope);
			if(!$lb_valido)
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
			$io_pdf->set_margenes(0,0,$x_pos,0);	
			$li_totrow=$ds_voucher->getRowCount("numdoc");
			$io_pdf->transaction('start'); // Iniciamos la transacci?n
			$thisPageNum=$io_pdf->ezPageCount;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				unset($la_data);
				$li_totprenom	= 0;
				$ldec_mondeb	= 0;
				$ldec_monhab	= 0;
				$li_totant		= 0;
				$ls_numdoc	    = $ds_voucher->data["numdoc"][$li_i];
				$ls_codban	    = $ds_voucher->data["codban"][$li_i];
				$ls_nomban	    = $class_report->uf_select_data($io_sql,"SELECT * FROM scb_banco WHERE codban ='".$ls_codban."' AND codemp='".$ls_codemp."'","nomban");
				$ls_ctaban	    = $ds_voucher->data["ctaban"][$li_i];
				$ls_chevau	    = $ds_voucher->data["chevau"][$li_i];
				$ld_fecmov	    = $io_funciones->uf_convertirfecmostrar($ds_voucher->data["fecmov"][$li_i]);
				$ls_nomproben   = $ds_voucher->data["nomproben"][$li_i];
				$ls_solicitudes = $class_report->uf_select_solicitudes($ls_numdoc,$ls_codban,$ls_ctaban);
				$ls_conmov		= $ds_voucher->getValue("conmov",$li_i);
				$ldec_monret    = $ds_voucher->getValue("monret",$li_i);
				$ldec_monto	    = $ds_voucher->getValue("monto",$li_i);
				$ldec_total	    = $ldec_monto-$ldec_monret;
				$ls_monto	    = $numalet->uf_convertir_letra($ldec_total,'','');
				$io_encabezado  = $io_pdf->openObject();
				uf_print_encabezado_pagina(number_format($ldec_total,2,",","."),$ls_nomproben,$ls_monto,$_SESSION["la_empresa"]["ciuemp"].", ".$ld_fecmov,$io_pdf,$y_pos); // Imprimimos el encabezado de la p?gina
				uf_print_pie_pagina($ls_numdoc,$ls_nomban,$ls_ctaban,$ls_nomproben,$ld_fecmov,$ls_conmov,$ls_chevau,$io_pdf); // Imprimimos la cabecera del registro
				$io_pdf->closeObject();
				$io_pdf->addObject($io_encabezado,'all');
				$ds_dt_scg->data=$class_report->uf_cargar_dt_scg($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope); // Obtenemos el detalle del reporte
				$ds_dt_spg->data=$class_report->uf_cargar_dt_spg($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope);
				$la_campos=array("scg_cuenta");
				$la_monto=array("monto");
				$ds_dt_scg->group_by($la_campos,$la_monto,"scg_cuenta");
				$la_campos=array("spg_cuenta","estpro");
				$ds_dt_spg->group_by($la_campos,$la_monto,"spg_cuenta");
				$li_totrow_det=$ds_dt_scg->getRowCount("scg_cuenta");
				$li_totrow_spg=$ds_dt_spg->getRowCount("spg_cuenta");
				$la_contable = array();
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// Hago un ciclo para unir en una sola matriz los detalles de presupuesto y los contables para proceder luego a pintarlos
				if($li_totrow_det>=$li_totrow_spg)
				{
					for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
					{
						$ls_scg_cuenta = $ds_dt_scg->data["scg_cuenta"][$li_s];
						$ls_denctascg  = substr($ds_dt_scg->data["denominacion"][$li_s],0,80);
						$ls_debhab     = $ds_dt_scg->data["debhab"][$li_s];
						$ldec_monto    = $ds_dt_scg->data["monto"][$li_s];
						
						if($ls_debhab=='D')
						{
							$ldec_mondeb=number_format($ldec_monto,2,",",".");
							$ldec_monhab=" ";
						}
						else
						{
							$ldec_monhab=number_format($ldec_monto,2,",",".");
							$ldec_mondeb=" ";
						}
						if(array_key_exists("spg_cuenta",$ds_dt_spg->data))
						{
							if(array_key_exists($li_s,$ds_dt_spg->data["spg_cuenta"]))
							{
								$ls_cuentaspg   = $ds_dt_spg->getValue("spg_cuenta",$li_s);	
								$ls_denctaspg   = substr($ds_dt_spg->getValue("denominacion",$li_s),0,80);	
								$ls_codestpro   = $ds_dt_spg->getValue("estpro",$li_s);	  
								$ldec_monto_spg = number_format($ds_dt_spg->getValue("monto",$li_s),2,",",".");
							}
							else
							{
								$ls_cuentaspg=" ";	
								$ls_codestpro=" ";	  
								$ldec_monto_spg=" ";
								$ls_denctaspg = "";
							}
						}
						else
						{
							$ls_cuentaspg=" ";	
							$ls_codestpro=" ";	  
							$ldec_monto_spg=" ";
							$ls_denctaspg = "";
						}
						$la_data[$li_s]=array('codestpro'=>$ls_codestpro,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'denctascg'=>$ls_denctascg,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
					}
				}
				
				if($li_totrow_spg>$li_totrow_det)
				{
					for($li_s=1;$li_s<=$li_totrow_spg;$li_s++)
					{
						if(array_key_exists("scg_cuenta",$ds_dt_scg->data))
						{
							if(array_key_exists($li_s,$ds_dt_scg->data["scg_cuenta"]))
							{
								$ls_scg_cuenta = $ds_dt_scg->data["scg_cuenta"][$li_s];
								$ls_denctascg  = substr($ds_dt_scg->data["denominacion"][$li_s],0,80);
								$ls_debhab     = $ds_dt_scg->data["debhab"][$li_s];
								$ldec_monto    = $ds_dt_scg->data["monto"][$li_s];
								if($ls_debhab=='D')
								{
									$ldec_mondeb=number_format($ldec_monto,2,",",".");
									$ldec_monhab=" ";
								}
								else
								{
									$ldec_monhab=number_format($ldec_monto,2,",",".");
									$ldec_mondeb=" ";
								}
							}
							else
							{
								$ls_scg_cuenta="";
								$ls_denctascg  = "";
								$ls_debhab = "";
								$ldec_monto= "";
								$ldec_mondeb="";
								$ldec_monhab="";					
							}
						}
						else
						{
							$ls_scg_cuenta="";
							$ls_denctascg  = "";
							$ls_debhab = "";
							$ldec_monto= "";
							$ldec_mondeb="";
							$ldec_monhab="";					
						}
						if(array_key_exists("spg_cuenta",$ds_dt_spg->data))
						{
							if(array_key_exists($li_s,$ds_dt_spg->data["spg_cuenta"]))
							{
								$ls_cuentaspg   = $ds_dt_spg->getValue("spg_cuenta",$li_s);	
								$ls_denctaspg   = substr($ds_dt_spg->getValue("denominacion",$li_s),0,80);	
								$ls_codestpro   = $ds_dt_spg->getValue("estpro",$li_s);
								$ldec_monto_spg = number_format($ds_dt_spg->getValue("monto",$li_s),2,",",".");
							}
							else
							{
								$ls_cuentaspg   = "";	
								$ls_denctaspg   = "";
								$ls_codestpro   = "";
								$ldec_monto_spg = "";
							}
						}
						else
						{
							$ls_cuentaspg 	= "";
							$ls_denctaspg 	= "";
							$ls_codestpro	= "";	  
							$ldec_monto_spg = "";
						}
						$la_data[$li_s]=array('codestpro'=>$ls_codestpro,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'denctascg'=>$ls_denctascg,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
					}
					
				}
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				if(empty($la_data))
				{
					$ls_cuentaspg='';
					$ls_codestpro='';
					$ldec_monto_spg='';
					$ls_scg_cuenta='';
					$ldec_mondeb='';
					$ldec_monhab='';
					$la_data[1]=array('codestpro'=>$ls_codestpro,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'denctascg'=>$ls_denctascg,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
					$la_data[2]=array('codestpro'=>$ls_codestpro,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'denctascg'=>$ls_denctascg,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
					$la_data[3]=array('codestpro'=>$ls_codestpro,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg,'scg_cuenta'=>$ls_scg_cuenta,'denctascg'=>$ls_denctascg,'debe'=>$ldec_mondeb,'haber'=>$ldec_monhab);
				}
				//codigo para unir las cuentas iguales
				$la_dataaux=array();
				for($li_k=1;$li_k<=count((array)$la_data);$li_k++)
				{
					$lb_existe=false;
					$li_pos=0;
					for($li_l=1;$li_l<=count((array)$la_dataaux);$li_l++)
					{
						if(($la_data[$li_k]["spg_cuenta"]==$la_dataaux[$li_l]["spg_cuenta"])
							&& ($la_data[$li_k]["codestpro"]==$la_dataaux[$li_l]["codestpro"])
							&& ($la_data[$li_k]["scg_cuenta"]==$la_dataaux[$li_l]["scg_cuenta"]))
						{
							$li_pos=$li_i;
							$lb_existe=true;
						}
					}
					if(!$lb_existe)
					{
						$li_index=count((array)$la_dataaux)+1;
						$la_dataaux[$li_index]=$la_data[$li_k];
					}
					else
					{
						
						$ls_monto_spg1   = uf_convertir($la_dataaux[$li_pos]["monto_spg"]);
						$ls_monto_spg2   = uf_convertir($la_data[$li_k]["monto_spg"]);
						$ls_monto_debe1  = uf_convertir($la_dataaux[$li_pos]["debe"]);
						$ls_monto_debe2  = uf_convertir($la_data[$li_k]["debe"]);
						$ls_monto_haber1 = uf_convertir($la_dataaux[$li_pos]["haber"]);
						$ls_monto_haber2 = uf_convertir($la_data[$li_k]["haber"]);
						$la_dataaux[$li_pos]["monto_spg"] = number_format(($ls_monto_spg1 + $ls_monto_spg2),2,",",".");
						if (($ls_monto_debe1 + $ls_monto_debe2) != 0)
							$la_dataaux[$li_pos]["debe"] = number_format(($ls_monto_debe1 + $ls_monto_debe2),2,",",".");
						else
							$la_dataaux[$li_pos]["debe"]="";
						if(($ls_monto_haber1 + $ls_monto_haber2) != 0)
							$la_dataaux[$li_pos]["haber"] = number_format(($ls_monto_haber1 + $ls_monto_haber2),2,",",".");
						else
							$la_dataaux[$li_pos]["haber"]="";
					}
					
				}
				
				$io_pdf->y=190;
				$io_pdf->y=440;	
				$io_pdf->set_margenes(138,70,$x_pos,0);
				uf_print_detalle($la_dataaux,$io_pdf,$x_pos); // Imprimimos el detalle 	
			}
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
			unset($io_pdf);
			unset($class_report);
			unset($io_funciones);
			
	}
?> 