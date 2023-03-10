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

    session_start();//Oficina de Apoyo Mar?timo de la Armada.(OCAMAR)    
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
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
			chmod("cheque_configurable", 0777);
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
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creaci?n: 25/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha  = new class_fecha();
		$li_nummes = substr($ls_fecha,14,2);
		$ls_nommes = $io_fecha->uf_load_nombre_mes($li_nummes);
		unset($io_fecha);
				
		global $valores;
		//Imprimo el monto
		$io_pdf->add_texto($valores[0],$valores[1],10,"<b>***".$ldec_monto."***</b>");
		//Beneficiario del Cheque
		$io_pdf->add_texto($valores[2],$valores[3],11,"<b>$ls_nomproben</b>");
		$ls_anio=substr($ls_fecha,-4);
		$ls_fecha_corta=substr($ls_fecha,0,(strlen($ls_fecha)-8)).'de '.$ls_nommes;
		//Fecha del Cheque
		$io_pdf->add_texto($valores[8],$valores[9],9,"<b>$ls_fecha_corta</b>");
		$io_pdf->add_texto($valores[10],$valores[11],9,"<b>$ls_anio</b>");	
		$io_pdf->add_texto($valores[12],$valores[13],9,"<b>NO ENDOSABLE</b>");
		$io_pdf->add_texto($valores[14],$valores[15],9,"<b>CADUCA A LOS ".$_SESSION["la_empresa"]["diacadche"]." DIAS</b>");
		return $io_pdf;
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($ls_numdoc,$ls_chevau,$ls_nomban,$ls_ctaban,$ls_nomproben,$ls_solicitudes,$ls_fecmov,$ls_conmov,$ls_monto,$ldec_total,$io_pdf)
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
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creaci?n: 24/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$io_pdf->addText(70,573,11,$ls_fecmov);
		$io_pdf->addText(242,573,11,$ls_numdoc);
		$io_pdf->addText(435,573,11,$ls_chevau);
		/*$li_pos=239;
		$io_pdf->convertir_valor_mm_px($li_pos);
		$io_pdf->ezSetY($li_pos);
		$la_data=array(array('fecha'=>'','cheque'=>'','chevau'=>''),
						array('fecha'=>'          '.$ls_fecmov,'cheque'=>'','chevau'=>'  '.$ls_chevau));
		$la_columna=array('fecha'=>'','cheque'=>'','chevau'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1	, // Mostrar L?neas
						 'fontSize'=>11, // Tama?o de Letras
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientaci?n de la tabla
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580,
						  'cols'=>array('fecha'=>array('justification'=>'left','width'=>100),
						               'cheque'=>array('justification'=>'center','width'=>280),
						               'chevau'=>array('justification'=>'left','width'=>180)));		   
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);*/
	
		$ls_conmov = wordwrap($ls_conmov,100);
		$io_pdf->add_texto(50,180,9,$ls_monto.'                           '.number_format($ldec_total,2,",","."));
		$io_pdf->add_texto(15,190,9,$ls_conmov);
		
		$io_pdf->ezSetY(440);
		$la_data=array(array('banco'=>'','cuenta'=>''),
						array('banco'=>'     '.$ls_nomban,'cuenta'=>$ls_ctaban));
		$la_columna=array('banco'=>'','cuenta'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0	, // Mostrar L?neas
						 'fontSize' =>10, // Tama?o de Letras
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientaci?n de la tabla
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580,
						 'cols'=>array('banco'=>array('justification'=>'left','width'=>250),
						               'cuenta'=>array('justification'=>'center','width'=>250)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		
		$li_pos=40;
		$io_pdf->convertir_valor_mm_px($li_pos);		
		$io_pdf->ezSetY($li_pos);
		$la_data=array(array('nomproben'=>''),
						array('nomproben'=>'<b>Orden(es) de Pago(s):  </b> '.$ls_solicitudes.'       '.'<b>Proveedor:  </b>'.$ls_nomproben));
		$la_columna=array('nomproben'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' =>10, // Tama?o de Letras
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>250, // Orientaci?n de la tabla
						 'xOrientation'=>'left',
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580,
						 'cols'=>array('nomproben'=>array('justification'=>'left','width'=>200)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		return $io_pdf;
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_datapre,$la_datacon,$io_pdf,$x_pos)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creaci?n: 24/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//print_r($la_data);
		//Imprimo los detalles tanto de presupuesto como contablwe del movimiento
		$io_pdf->ezSety(240);//315
		if ($la_datapre<>'')
		 { 
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize'=>10, // Tama?o de Letras
						 'titleFontSize'=>10,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho M?ximo de la tabla
						 'colGap'=>1, //Separacion de los caracteres entre las barras de las tablas.
						 'xPos'=>238, // Orientaci?n de la tabla
						 'cols'=>array('codestpro1'=>array('justification'=>'left','width'=>55),
									   'codestpro2'=>array('justification'=>'left','width'=>90),
									   'codestpro3'=>array('justification'=>'left','width'=>30),
			 						   'spg_cuenta'=>array('justification'=>'center','width'=>110),
									   'denctaspg'=>array('justification'=>'left','width'=>120),
									   'monto_spg'=>array('justification'=>'right','width'=>175))); // Justificaci?n y ancho de la columna
		$la_columnas=array('codestpro1'=>'','codestpro2'=>'','codestpro3'=>'','spg_cuenta'=>'','monto_spg'=>'');
		$io_pdf->ezTable($la_datapre,$la_columnas,'',$la_config);
		}
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize'=>10, // Tama?o de Letras
						 'titleFontSize'=>10,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho M?ximo de la tabla
						 'colGap'=>1, //Separacion de los caracteres entre las barras de las tablas.
						 'xPos'=>332, // Orientaci?n de la tabla
						 'cols'=>array('scg_cuenta'=>array('justification'=>'center','width'=>110),
			 						   'debe'=>array('justification'=>'center','width'=>70),
			 						   'haber'=>array('justification'=>'right','width'=>90))); // Justificaci?n y ancho de la columna
		$la_columnas=array('scg_cuenta'=>'','debe'=>'','haber'=>'');
		$io_pdf->ezTable($la_datacon,$la_columnas,'',$la_config);
		return $io_pdf;
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_firmas($io_sql,$class_report,$io_pdf)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle
	//		   Access: private 
	//	    Arguments: io_sql // Instancia de la clase encargada de ejecutar las sentencias SQL.
	//				   class_report // Instancia de la clase generadora de reportes.
	//	   			   io_pdf // Objeto PDF
	//    Description: Funci?n que imprime las firmas en el formato del voucher preimpreso del instituto.
	//	   Creado Por: Ing. N?stor Falc?n.
	// Fecha Creaci?n: 04/06/2008. 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
	  $io_pdf->add_texto(5,225,10,"<b>CF. EMMY DA COSTA VENEGAS</b>");
	  $io_pdf->add_texto(70,225,10,"<b>CN. JUAN CARLOS FLORES ZAVALA</b>");
	  $ls_nomusu = $class_report->uf_select_data($io_sql,"SELECT nomusu||', '||apeusu as nomusu FROM sss_usuarios WHERE codemp ='".$_SESSION["la_empresa"]["codemp"]."' AND codusu='".trim($_SESSION["la_logusr"])."'","nomusu");
	  $io_pdf->add_texto(5,240,10,"<b>".strtoupper($ls_nomusu)."</b>");
	  $io_pdf->add_texto(70,240,10,"<b>MTP. JOSE SALAZAR P.</b>");
		return $io_pdf;
	}

    uf_inicializar_variables();
    require_once("sigesp_scb_report.php");
	require_once('../../shared/class_folder/class_pdf.php');
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
	$in=new sigesp_include();
	$con=$in->uf_conectar();
	$io_sql=new class_sql($con);	
	
	$class_report = new sigesp_scb_report($con);
	$io_funciones = new class_funciones();				
	$ds_voucher	  = new class_datastore();	
	$ds_dt_scg	  = new class_datastore();				
	$ds_dt_spg	  = new class_datastore();
	//Instancio a la clase de conversi?n de numeros a letras.
	require_once("../../shared/class_folder/cnumero_letra.php");
	$numalet= new cnumero_letra();
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
	$io_pdf=new class_pdf('LEGAL','portrait'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra	
	//$io_pdf->set_margenes(0,0,$x_pos,0);
	$io_pdf->set_margenes(0,0,0,0);	
	$li_totrow=$ds_voucher->getRowCount("numdoc");
	$io_pdf->transaction('start'); // Iniciamos la transacci?n
	$thisPageNum=$io_pdf->ezPageCount;
	for($li_i=1;$li_i<=$li_totrow;$li_i++)
	{
		unset($la_data);
		$li_totprenom=0;
		$ldec_mondeb=0;
		$ldec_monhab=0;
		$li_totant=0;
		$ls_numdoc    = $ds_voucher->data["numdoc"][$li_i];
		$ls_codban    = $ds_voucher->data["codban"][$li_i];
		$ls_nomban    = $class_report->uf_select_data($io_sql,"SELECT nomban FROM scb_banco WHERE codban ='".$ls_codban."' AND codemp='".$ls_codemp."'","nomban");
		$ls_ctaban    = $ds_voucher->data["ctaban"][$li_i];
		$ls_chevau    = $ds_voucher->data["chevau"][$li_i];
		$ld_fecmov    = $io_funciones->uf_convertirfecmostrar($ds_voucher->data["fecmov"][$li_i]);
		$ls_nomproben = $ds_voucher->data["nomproben"][$li_i];
		$ls_solicitudes=$class_report->uf_select_solicitudes($ls_numdoc,$ls_codban,$ls_ctaban);
		$ls_conmov   = $ds_voucher->getValue("conmov",$li_i);
		$ldec_monret = $ds_voucher->getValue("monret",$li_i);
		$ldec_monto  = $ds_voucher->getValue("monto",$li_i);
		$ldec_total  = $ldec_monto-$ldec_monret;
		$ls_monto    = $numalet->uf_convertir_letra($ldec_total,'','');
		$io_pdf=uf_print_encabezado_pagina(number_format($ldec_total,2,",","."),$ls_nomproben,$ls_monto,$_SESSION["la_empresa"]["ciuemp"].", ".$ld_fecmov,$io_pdf); // Imprimimos el encabezado de la p?gina
		$io_pdf=uf_print_cabecera($ls_numdoc,$ls_chevau,$ls_nomban,$ls_ctaban,$ls_nomproben,$ls_solicitudes,$ld_fecmov,$ls_conmov,$ls_monto,$ldec_total,$io_pdf); // Imprimimos la cabecera del registro
		$io_pdf=uf_print_firmas($io_sql,$class_report,$io_pdf);
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
		$li_totrowpre = $li_totrowcon = 0;
		$la_datapre='';
		$la_datacon='';
		if ($li_totrow_det>=$li_totrow_spg)
		   {
		     for ($li_s=1;$li_s<=$li_totrow_det;$li_s++)
			     {
				   $ls_scg_cuenta = trim($ds_dt_scg->data["scg_cuenta"][$li_s]);
				   $ls_debhab     = $ds_dt_scg->data["debhab"][$li_s];
				   $ldec_monto    = $ds_dt_scg->data["monto"][$li_s];
				   if ($ls_debhab=='D')
				      {
					    $ldec_mondeb = "";
					    $ldec_monhab = number_format($ldec_monto,2,",",".");;				
			 	      }
				   else
				      {
					    $ldec_monhab = number_format($ldec_monto,2,",",".");
					    $ldec_mondeb = "";
				      }
				   $li_totrowcon++;
				   $la_datacon[$li_totrowcon] = array('scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ls_debhab,'haber'=>$ldec_monhab);	
				   if (array_key_exists("spg_cuenta",$ds_dt_spg->data))
				      {
					    if (array_key_exists($li_s,$ds_dt_spg->data["spg_cuenta"]))
					       {
							 $ls_cuentaspg   = trim($ds_dt_spg->getValue("spg_cuenta",$li_s));
							 $ls_denctaspg   = $ds_dt_spg->getValue("denominacion",$li_s);	
							 $ls_estpro      = $ds_dt_spg->getValue("estpro",$li_s);	
							 $ls_codestpro1  = substr($ls_estpro,14,6);
							 $ls_codestpro2  = substr($ls_estpro,21,6);
							 $ls_codestpro3  = substr($ls_estpro,28,3);  
							 $ldec_monto_spg = number_format($ds_dt_spg->getValue("monto",$li_s),2,",",".");
					         $li_totrowpre++;
							 $la_datapre[$li_totrowpre] = array('codestpro1'=>$ls_codestpro1,'codestpro2'=>$ls_codestpro2,'codestpro3'=>$ls_codestpro3,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg);
							
						   }
				      }
			     }
		   }
		//$la_datapre='';
		if ($li_totrow_spg>$li_totrow_det)
		   {
		     for ($li_s=1;$li_s<=$li_totrow_spg;$li_s++)
			     {
				   if (array_key_exists("scg_cuenta",$ds_dt_scg->data))
				      {
					    if (array_key_exists($li_s,$ds_dt_scg->data["scg_cuenta"]))
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
					         $li_totrowcon++;
							 $la_datacon[$li_totrowcon] = array('scg_cuenta'=>$ls_scg_cuenta,'debe'=>$ls_debhab,'haber'=>$ldec_monhab);	
						   }
 					  }
					  
				   if (array_key_exists("spg_cuenta",$ds_dt_spg->data))
				      {
					    if (array_key_exists($li_s,$ds_dt_spg->data["spg_cuenta"]))
					       {
						     $ls_cuentaspg   = trim($ds_dt_spg->getValue("spg_cuenta",$li_s));
						     $ls_denctaspg   = $ds_dt_spg->getValue("denominacion",$li_s);	
						     $ls_estpro      = $ds_dt_spg->getValue("estpro",$li_s);	
							 $ls_codestpro1  = substr($ls_estpro,14,6);
							 $ls_codestpro2  = substr($ls_estpro,21,6);
							 $ls_codestpro3  = substr($ls_estpro,28,3);   
							 $ldec_monto_spg = number_format($ds_dt_spg->getValue("monto",$li_s),2,",",".");
							 $li_totrowpre++;
							 $la_datapre[$li_totrowpre] = array('codestpro1'=>$ls_codestpro1,'codestpro2'=>$ls_codestpro2,'codestpro3'=>$ls_codestpro3,'spg_cuenta'=>$ls_cuentaspg,'denctaspg'=>$ls_denctaspg,'monto_spg'=>$ldec_monto_spg);
					       }
					  }
		         }
		   }
		$io_pdf->y=190;
		$io_pdf->y=440;	
		$io_pdf->set_margenes(138,20,$x_pos,0);
		
		$io_pdf=uf_print_detalle($la_datapre,$la_datacon,$io_pdf,$x_pos); // Imprimimos el detalle 
	}
	$io_pdf->ezStopPageNumbers(1,1);
	$io_pdf->ezStream();
	unset($la_datapre,$la_datacon);
	unset($io_pdf);
	unset($class_report);
	unset($io_funciones);
?> 