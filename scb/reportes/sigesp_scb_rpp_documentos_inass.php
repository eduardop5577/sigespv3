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
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "opener.document.form1.submit();";
		print "close();";
		print "</script>";		
	}
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time ','0');
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$ad_fecdesde,$ad_fechasta,$ls_nomban,$ls_tipcta,$ls_ctaban,$ls_tipolistado,$ls_conceptos,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 06/02/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$li_alto=$_SESSION["ls_height"];
		$io_pdf->convertir_valor_px_mm($li_alto);
		$li_ancho=$_SESSION["ls_width"];
		$io_pdf->convertir_valor_px_mm($li_ancho);
		$li_altura_logo=20;
		$io_pdf->convertir_valor_mm_px($li_altura_logo);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,(792-$li_altura_logo),$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->add_texto(65,15,13,$as_titulo);
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		
		//Fecha y hora
		$io_pdf->add_texto(142,25,9,"<b>Pág. </b>");
		$io_pdf->add_texto(142,30,9,"<b>Fecha de Impresión: ".date("d/m/Y")."</b>");
		$io_pdf->add_texto(142,35,9,"<b>Hora de Impresión: ".date("G:i:s")."</b>");
		$io_pdf->add_texto(10,25,9,"<b><i>".$_SESSION["la_empresa"]["nombre"]."</i></b>");
		//Banco
		$io_pdf->add_texto(10,30,10,"<b>BANCO:                    ".$ls_nomban."</b>");
		//Tipo de Cuenta
		$io_pdf->add_texto(10,35,10,"<b>TIPO DE CUENTA:  ".$ls_tipcta."</b>");
		//Cuenta
		$io_pdf->add_texto(10,40,10,"<b>CUENTA:                  ".$ls_ctaban."</b>");
		//Listado de 
		$io_pdf->add_texto(10,50,10,"<b>LISTADO DE:           ".$ls_tipolistado."</b>");		
		if ($ls_conceptos!="")
		{
			// Conceptos
			$io_pdf->add_texto(10,55,9,"<b>TIPO CONCEPTO:       ".$ls_conceptos."</b>");		
		}
		//Rango de Fechas
		if(($ad_fecdesde!="") && ($ad_fechasta!=""))
			$io_pdf->add_texto(10,60,9,"<b>PERIODO DESDE: ".$ad_fecdesde." HASTA ".$ad_fechasta."</b>");	
		elseif(($ad_fecdesde!="") && ($ad_fechasta==""))
			$io_pdf->add_texto(10,60,9,"<b>PERIODO DESDE: ".$ad_fecdesde." HASTA LA FECHA ACTUAL</b>");	
		elseif(($ad_fecdesde=="") && ($ad_fechasta!=""))
			$io_pdf->add_texto(10,60,9,"<b>PERIODO HASTA: ".$ad_fechasta."</b>");
		else
			$io_pdf->add_texto(10,60,9,"<b>TODAS LAS FECHAS</b>");		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_banco($ls_banco, $ls_cuenta, $io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_banco
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el banco y su respectiva cuenta
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 07/12/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $io_pdf->add_lineas(1);
		 $la_data[0]["1"]="<b>Banco:</b>";
		 $la_data[0]["2"]="<b>$ls_banco</b>";
		 $la_data[1]["1"]="<b>Cuenta:</b>";
		 $la_data[1]["2"]="<b>$ls_cuenta</b>";
		 $la_anchos_col = array(15,170);
		 $la_justificaciones = array("left","left");
		 $la_opciones = array("color_texto" => array(0,0,0),
							   "anchos_col"  => $la_anchos_col,
							   "tamano_texto"=> 10,
							   "lineas"=>0,
							   "color_fondo"=>array(242,242,242),
							   "alineacion_col"=>$la_justificaciones,
							   "margen_horizontal"=>2,
							   "margen_vertical"=>2);
		 $io_pdf->add_tabla(7,$la_data,$la_opciones);
		 $io_pdf->add_lineas(1);
		 
		return $io_pdf;
	}// end function uf_print_detalle	

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_dataimprimir,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: $la_data
		//	    		   io_pdf // 
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. laura Cabre
		// Fecha Creación: 06/02/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->set_margenes(70,40,3,3);
		$la_columnas=array('documento'=>'<b>Documento</b>',
						   'ncontrol'=>'<b>Nº Control Interno</b>',	
						   'fecha'=>'<b>Fecha</b>',	
						   'proveedor'=>'<b>Proveedor/Beneficiario</b>',					
						   'conmov'=>'<b>Concepto</b>',
						   'monto'=>'<b>Monto</b>',
						   'status'=>'<b>Estatus</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('documento'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'ncontrol'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>60),// Justificación y ancho de la columna
						 			   'proveedor'=>array('justification'=>'center','width'=>110), // Justificación y ancho de la columna
									   'conmov'=>array('justification'=>'left','width'=>100), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>80),// Justificación y ancho de la columna
									   'status'=>array('justification'=>'center','width'=>45))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_dataimprimir,$la_columnas,'',$la_config);
		unset($la_dataimprimir);
		unset($la_columnas);
		unset($la_config);
		return $io_pdf;
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($ad_debitos,$ad_creditos,$ad_total,$movimientos,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: as_numdoc // Número del documento
		//	    		   as_conmov // concepto del documento
		//	    		   as_nomproben // nombre del proveedor beneficiario
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Laura Cabre
		// Fecha Creación: 05/02/2007
		/////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezText('                     ',10);//Inserto una linea en blanco
		$la_data[1]=array('name'=>'<b>Total Créditos:</b>', 'monto'=>$ad_creditos);
		$la_data[2]=array('name'=>'<b>Total Débitos:</b>', 'monto'=>$ad_debitos);
		$la_data[3]=array('name'=>'<b>Total Saldo:</b>', 'monto'=>$ad_total);
		$la_data[4]=array('name'=>'<b>Total Movimientos:</b>', 'monto'=>$movimientos);
		$la_columna=array('name'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>200, // Ancho de la tabla
						 'maxWidth'=>200, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Justificación y ancho de la columna
						 'xPos'=>460,
						 'cols'=>array('name'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//Linea de REVISADO POR
		$li_pos=$io_pdf->get_alto_usado();
		$li_pos+=90;
		
		/*/Para que la lnea la imprima negra y no gris
		$la_opciones = array("color_texto" => array(0,0,0),
							   "color_fondo"=>array(0,0,0),
							   "anchos_col"  => $la_anchos_col,
							   "tamano_texto"=> 9,
							   "lineas"=>0,
							   "alineacion_col"=>$la_justificaciones,
							   "margen_horizontal"=>2,
							   "margen_vertical"=>3);
		$io_pdf->add_tabla(300,$la_data,$la_opciones);*/		
		$io_pdf->set_margenes(3,3,3,3);	
		$io_pdf->add_linea(130,$li_pos,180,$li_pos)	;
		$io_pdf->add_texto(143,$li_pos,9,"<b>REVISADO POR</b>");
		return $io_pdf;
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total_anulados($ad_totanu,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_total_anulados
		//		   Access: private 
		//	    Arguments: as_numdoc // Número del documento
		//	    		   as_conmov // concepto del documento
		//	    		   as_nomproben // nombre del proveedor beneficiario
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 05/02/2007
		/////////////////////////////////////////////////////////////////////////////////

		$li_pos    = $io_pdf->y;
		$la_data   = array();
		$ad_totanu = number_format($ad_totanu,2,',','.');
		$la_data[0]["1"]="<b>Total Anulados</b>";
		$la_data[0]["2"]="<b>".$ad_totanu."</b>";
		$la_data[0]["3"]="";
		
		$la_anchos_col = array(154,30,12);
		$la_justificaciones = array("right","right","left");
		$la_opciones = array("color_texto" => array(0,0,0),
							   "color_fondo"=>array(219,219,219),
							   "anchos_col"  => $la_anchos_col,
							   "tamano_texto"=> 9,
							   "lineas"=>1,
							   "alineacion_col"=>$la_justificaciones,
							   "margen_horizontal"=>2,
							   "margen_vertical"=>3);
		$io_pdf->add_tabla(7,$la_data,$la_opciones);
		//Linea de REVISADO POR
		$li_pos=$io_pdf->get_alto_usado();
		$li_pos+=90;
		
		//Para que la lnea la imprima negra y no gris
		$la_opciones = array("color_texto" => array(0,0,0),
							   "color_fondo"=>array(0,0,0),
							   "anchos_col"  => $la_anchos_col,
							   "tamano_texto"=> 9,
							   "lineas"=>0,
							   "alineacion_col"=>$la_justificaciones,
							   "margen_horizontal"=>2,
							   "margen_vertical"=>3);
		$io_pdf->add_tabla(300,$la_data,$la_opciones);		
		$io_pdf->set_margenes(3,3,3,3);	
		return $io_pdf;
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_pie($io_pdf)
	{
		////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el pie del reporte
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 06/02/2007 
		////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->add_texto(10,250,8,"C: DOCUMENTOS CONTABILIZADOS");
		$io_pdf->add_texto(73,250,8,"N: DOCUMENTOS POR CONTABILIZAR");
		$io_pdf->add_texto(150,250,8,"A: DOCUMENTOS ANULADOS");
		$io_pdf->add_texto(10,255,8,"O: DOCUMENTOS ORIGINALES");
		$io_pdf->add_texto(73,255,8,"L: DOCUMENTOS SIN AFECTACION CONTABLE");
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}
	

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("sigesp_scb_class_report.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
	require_once('../../shared/class_folder/class_pdf.php');
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");

	$sig_inc   = new sigesp_include();
	$con       = $sig_inc->uf_conectar();
	$io_report = new sigesp_scb_class_report($con);
	$in        = new sigesp_include();
	$con       = $in->uf_conectar();
	$io_sql    = new class_sql($con);	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp    	= $_SESSION["la_empresa"]["codemp"];
	$ld_fecdesde  	= $_GET["fecdes"];
	$ld_fechasta  	= $_GET["fechas"];
	$ls_codope    	= $_GET["codope"];
	$ls_codban    	= $_GET["codban"];
	$ls_nomban    	= $_GET["nomban"];
	$ls_ctaban    	= $_GET["ctaban"];
	$ls_codconcep   = $_GET["codconcep"];
	$ls_orden     	= $_GET["orden"];
	$ls_estatus     = $_GET["hidestmov"];
	$ls_tipbol      = 'Bs.';
	$ls_tiporeporte = 0;
	$ls_tiporeporte = $_GET["tiporeporte"];
	$ls_numdocmov   = $_GET["documento"];
	$ls_numdocchk   = $_GET["chkados"];
	$ls_conchk 		= $_GET["chked"];
	$ls_conceptos   = $_GET["conceptos"];
	//Opción para los selectivos
	$lr_numdocchk= explode('>>',$ls_numdocchk);
    $lr_datos= array_unique($lr_numdocchk);
    $li_total= count((array)$lr_datos);
	sort($lr_datos,SORT_STRING);
	//Opción para los selectivos
	
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_scb_class_reportbsf.php");
		$io_report = new sigesp_scb_class_reportbsf($con);
		$ls_tipbol = 'Bs.F.';
	}
	$ls_titulo    		= "<b>Listado de Movimientos Bancarios $ls_tipbol</b>";
	$ldec_totaldebitos  = 0;
	$ldec_totalcreditos = 0;
	$ldec_saldo         = 0;
	$lb_valido          = true;
	
	set_time_limit(1800);
	$io_pdf=new class_pdf('LETTER','portrait');
	$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->set_margenes(3,3,3,3);	 // Configuración de los margenes en centímetros
	if ($ls_conchk==1)
	{
		$i=0;
		$li_check=0;
		$ld_totanu = 0;
		$ld_totdeb = 0;
		$ld_totcre = 0;
		for($li_p=0;$li_p<$li_total;$li_p++)
		{
			$li_check++;
			$ls_numdocmov		= $lr_datos[$li_p];
			$lb_valido=true;
			$rs_data            = $io_report->uf_cargar_documentos($ls_codope,$ld_fecdesde,$ld_fechasta,$ls_codban,$ls_ctaban,$ls_codconcep,$ls_estatus,$ls_orden,$ls_numdocmov,$ls_conchk);
			if($lb_valido)
			{
				$ls_nomtipcta = $io_report->uf_select_data($io_sql,"SELECT nomtipcta FROM scb_tipocuenta t, scb_ctabanco c WHERE c.codemp='".$ls_codemp."' AND c.codtipcta=t.codtipcta AND c.ctaban='".$ls_ctaban."'","nomtipcta");
				switch($ls_codope)
				{
					case "ND":
						$ls_tipolistado="NOTA DE DEBITO";
					break;
					case "NC":
						$ls_tipolistado="NOTA DE CREDITO";
					break;
					case "DP":
						$ls_tipolistado="DEPOSITO";
					break;
					case "CH":
						$ls_tipolistado="CHEQUE";
					break;
					case "RE":
						$ls_tipolistado="RETIRO";
					break;
					default:
						$ls_tipolistado="TODO TIPO DE DOCUMENTO";
					break;
				}
				if ($li_check==1)
				{
					$io_pdf=uf_print_encabezado_pagina($ls_titulo,$ld_fecdesde,$ld_fechasta,$ls_nomban,$ls_nomtipcta,$ls_ctaban,$ls_tipolistado,$ls_conceptos,$io_pdf); // Imprimimos el encabezado de la página
					$io_pdf=uf_print_pie($io_pdf);
				}
				$li_x=163;
				$li_y=31;
				$io_pdf->convertir_valor_mm_px($li_x);
				$io_pdf->convertir_valor_mm_px($li_y);
				$io_pdf->ezStartPageNumbers($li_x,792-$li_y,9,'','',1); // Insertar el número de página
				
				while (!$rs_data->EOF)		
					{
						$i++;
						$io_pdf->transaction('start'); // Iniciamos la transacción
						$li_numpag    = $io_pdf->ezPageCount; // Número de página			
						$ls_numdoc    = $rs_data->fields["numdoc"];
						$ldec_monto   = $rs_data->fields["monto"]; 
						$ld_fecmov    = $rs_data->fields["fecmov"];
						$ld_fecmov    = $io_report->fun->uf_convertirfecmostrar($ld_fecmov);
						$ls_nombre    = $rs_data->fields["nomproben"];
						$ls_codopebd  = $rs_data->fields["codope"]; 
						$ls_conmov    = $rs_data->fields["conmov"];
						$ls_estbpd    = $rs_data->fields["estbpd"];
						$ls_estmov    = $rs_data->fields["estmov"];
						$ls_numcarord = $rs_data->fields["numcarord"];
						$ls_ncontrol  = $rs_data->fields["numconint"];
						if ($ls_estbpd=='T')
						   {
							 $ls_numdoc = $ls_numcarord;
						   } 
						if ($ls_estatus=='A')
						   {
							 $ld_totanu = ($ld_totanu+$ldec_monto);
						   }
						if (strlen($ls_conmov)>48)
						   {
							 $ls_conmov=substr($ls_conmov,0,46)."..";
						   }
						switch($ls_codopebd)
						{
							case "ND":
							  if ($ls_estmov!='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
							case "NC":
							  if ($ls_estmov!='A')
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							  else
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							break;
							case "DP":
							  if ($ls_estmov!='A')
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							  else
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							break;
							case "CH":
							  if ($ls_estmov!='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
							case "RE":
							  if ($ls_estmov=='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
						}
						$ld_mon=number_format($ldec_monto,2,",",".");
						
						if(($ls_codban!=$rs_data->fields["codban"]) || (trim($ls_ctaban)!=trim($rs_data->fields["ctaban"])))
						{
							$io_pdf=uf_print_detalle($la_data,$io_pdf);
							$ls_codban = $rs_data->fields["codban"];
							$ls_cuenta = $rs_data->fields["ctaban"];
							$ls_nomban = $rs_data->fields["nomban"];
							$la_data   = array();				
						}
						
						if (empty($ls_ncontrol)) {
							$ls_ncontrol='N/A';
						}
						
						$la_data[$i]=array('documento'=>$ls_numdoc,'ncontrol'=>$ls_ncontrol,'fecha'=>$ld_fecmov,
										   'proveedor'=>$ls_nombre,'conmov'=>$ls_conmov,'monto'=>$ld_mon,'status'=>$ls_estmov);		
						$rs_data->MoveNext();
				  }
				
			}
			else
			{
				print("<script language=JavaScript>");
				print(" alert('No hay nada que Reportar A');"); 
				//print(" close();");
				print("</script>");
			}
		}
		$io_pdf=uf_print_detalle($la_data,$io_pdf);		
		if ($ls_estatus=='A')
		{
			 $io_pdf=uf_print_total_anulados($ld_totanu,$io_pdf);
		}
		else
		{
			 $ld_saldo  = $ld_totcre-$ld_totdeb;//Calculo del saldo total para todas las cuentas
			 $ld_totcre = number_format($ld_totcre,2,",",".");
			 $ld_totdeb = number_format($ld_totdeb,2,",",".");
			 $ld_saldo  = number_format($ld_saldo,2,",",".");
			$io_pdf= uf_print_totales($ld_totdeb,$ld_totcre,$ld_saldo,$i,$io_pdf);   
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
	else
	{
			$lb_valido=true;
			$rs_data            = $io_report->uf_cargar_documentos($ls_codope,$ld_fecdesde,$ld_fechasta,$ls_codban,$ls_ctaban,$ls_codconcep,$ls_estatus,$ls_orden,$ls_numdocmov,0);
			if($lb_valido)
			{
				
				set_time_limit(1800);
				$io_pdf=new class_pdf('LETTER','portrait');
				$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
				$io_pdf->set_margenes(3,3,3,3);	 // Configuración de los margenes en centímetros
				$ls_nomtipcta = $io_report->uf_select_data($io_sql,"SELECT nomtipcta FROM scb_tipocuenta t, scb_ctabanco c WHERE c.codemp='".$ls_codemp."' AND c.codtipcta=t.codtipcta AND c.ctaban='".$ls_ctaban."'","nomtipcta");
				
				switch($ls_codope)
				{
					case "ND":
						$ls_tipolistado="NOTA DE DEBITO";
					break;
					case "NC":
						$ls_tipolistado="NOTA DE CREDITO";
					break;
					case "DP":
						$ls_tipolistado="DEPOSITO";
					break;
					case "CH":
						$ls_tipolistado="CHEQUE";
					break;
					case "RE":
						$ls_tipolistado="RETIRO";
					break;
					default:
						$ls_tipolistado="TODO TIPO DE DOCUMENTO";
					break;
				}
				$io_pdf=uf_print_encabezado_pagina($ls_titulo,$ld_fecdesde,$ld_fechasta,$ls_nomban,$ls_nomtipcta,$ls_ctaban,$ls_tipolistado,$ls_conceptos,$io_pdf); // Imprimimos el encabezado de la página
				$io_pdf=uf_print_pie($io_pdf);
				$li_x=163;
				$li_y=31;
				$io_pdf->convertir_valor_mm_px($li_x);
				$io_pdf->convertir_valor_mm_px($li_y);
				$io_pdf->ezStartPageNumbers($li_x,792-$li_y,9,'','',1); // Insertar el número de página
				$i=0;
				$ld_totanu = 0;
				$ld_totdeb = 0;
				$ld_totcre = 0;
			while (!$rs_data->EOF)		
					{
						$i++;
						$io_pdf->transaction('start'); // Iniciamos la transacción
						$li_numpag    = $io_pdf->ezPageCount; // Número de página			
						$ls_numdoc    = $rs_data->fields["numdoc"];
						$ldec_monto   = $rs_data->fields["monto"]; 
						$ld_fecmov    = $rs_data->fields["fecmov"];
						$ld_fecmov    = $io_report->fun->uf_convertirfecmostrar($ld_fecmov);
						$ls_nombre    = $rs_data->fields["nomproben"];
						$ls_codopebd  = $rs_data->fields["codope"]; 
						$ls_conmov    = $rs_data->fields["conmov"];
						$ls_estbpd    = $rs_data->fields["estbpd"];
						$ls_estmov    = $rs_data->fields["estmov"];
						$ls_numcarord = $rs_data->fields["numcarord"];
						$ls_ncontrol  = $rs_data->fields["numconint"];
						if ($ls_estbpd=='T')
						   {
							 $ls_numdoc = $ls_numcarord;
						   } 
						if ($ls_estatus=='A')
						   {
							 $ld_totanu = ($ld_totanu+$ldec_monto);
						   }
						if (strlen($ls_conmov)>48)
						   {
							 $ls_conmov=substr($ls_conmov,0,46)."..";
						   }
						switch($ls_codopebd)
						{
							case "ND":
							  if ($ls_estmov!='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
							case "NC":
							  if ($ls_estmov!='A')
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							  else
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							break;
							case "DP":
							  if ($ls_estmov!='A')
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							  else
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							break;
							case "CH":
							  if ($ls_estmov!='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
							case "RE":
							  if ($ls_estmov=='A')
								 {
								   $ld_totdeb = ($ld_totdeb+$ldec_monto);
								 }
							  else
								 {
								   $ld_totcre = ($ld_totcre+$ldec_monto);						   
								 }
							break;
						}
						$ld_mon=number_format($ldec_monto,2,",",".");
						
						if(($ls_codban!=$rs_data->fields["codban"]) || (trim($ls_ctaban)!=trim($rs_data->fields["ctaban"])))
						{
							$io_pdf=uf_print_detalle($la_data,$io_pdf);
							$ls_codban = $rs_data->fields["codban"];
							$ls_cuenta = $rs_data->fields["ctaban"];
							$ls_nomban = $rs_data->fields["nomban"];
							$la_data   = array();				
						}
						
						if (empty($ls_ncontrol)) {
							$ls_ncontrol='N/A';
						}
						
						$la_data[$i]=array('documento'=>$ls_numdoc,'ncontrol'=>$ls_ncontrol,'fecha'=>$ld_fecmov,
										   'proveedor'=>$ls_nombre,'conmov'=>$ls_conmov,'monto'=>$ld_mon,'status'=>$ls_estmov);		
						$rs_data->MoveNext();
				}
				$io_pdf=uf_print_detalle($la_data,$io_pdf);		
				if ($ls_estatus=='A')
				   {
					$io_pdf= uf_print_total_anulados($ld_totanu,$io_pdf);
				   }
				else
				   {
					 $ld_saldo  = $ld_totcre-$ld_totdeb;//Calculo del saldo total para todas las cuentas
					 $ld_totcre = number_format($ld_totcre,2,",",".");
					 $ld_totdeb = number_format($ld_totdeb,2,",",".");
					 $ld_saldo  = number_format($ld_saldo,2,",",".");
					 $io_pdf=uf_print_totales($ld_totdeb,$ld_totcre,$ld_saldo,$i,$io_pdf);   
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
					print(" close();");
					print("</script>");		
				}
				unset($io_pdf);
			}
			else
			{
				print("<script language=JavaScript>");
				print(" alert('No hay nada que Reportar B');"); 
				print(" close();");
				print("</script>");
			}

	}
?> 