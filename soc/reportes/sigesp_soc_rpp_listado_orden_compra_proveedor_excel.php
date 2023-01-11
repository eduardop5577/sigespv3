<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();   
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time ','0');
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($lo_libro,$lo_hoja,$as_titulo,$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;
		
		$lo_hoja->write($li_fila, 0, 'Orden de Compra',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 1, 'Proveedor/Beneficiario',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 2, 'R.I.F.',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 3, 'Telefono',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 4, 'Monto Bs.',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 5, 'Cantidad',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$li_fila++;



	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($lo_libro,$lo_hoja,$la_data,$li_totrow,$li_totmonsol,$li_fila)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private
		//	    Arguments: la_data      // arreglo de información
		//				   ai_i         // total de registros
		//				   li_totmonsol // total de solicitudes (Montos)
		//	    		   io_pdf       // Instancia de objeto pdf
		//    Description: Función que imprime el detalle del reporte
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 16/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//print_r($la_data);
		//print $li_totrow.':aqui';
		global $li_fila;
		
		for ($index = 1; $index < ($li_totrow+1); $index++)
		{
			//print $index.' Solicitud'.$la_data[$index]["codigo"].'<br>';
			$lo_hoja->write($li_fila, 0, " ".$la_data[$index]["codigo"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $la_data[$index]["nombre"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 2, $la_data[$index]["rifpro"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $la_data[$index]["telpro"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 4, $la_data[$index]["monto"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 5, $la_data[$index]["cantidad"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$li_fila++;
		}
		$li_fila++;
		$lo_hoja->write($li_fila, 0, "N° de Registros: ".$li_totrow,$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 4, "Total: ".$li_totmonsol,$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
		//print $index.':final  <br>';
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ad_numreg,$ad_totmon,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 16/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		global $li_fila;
		
	    $io_pdf->ezSetDy(-10);
		$la_data=array(array('name'=>'<b>N° de Registros:</b>'.$ad_numreg,
		                     'name1'=>'<b>Total '.$ls_bolivares.':</b> '.$ad_totmon));				
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>310, // Orientación de la tabla
						 'width'=>750, // Ancho de la tabla						 
						 'maxWidth'=>750, // Orientaci? de la tabla
						 'cols'=>array('name'=>array('justification'=>'left','width'=>250),      // Justificaci? y ancho de la columna
						 			   'name1'=>array('justification'=>'right','width'=>335))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");	
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("sigesp_soc_class_report.php");	
	require_once("../class_folder/class_funciones_soc.php");
	$in           = new sigesp_include();
	$con          = $in->uf_conectar();
	$io_sql       = new class_sql($con);	
	$io_funciones = new class_funciones();	
	$io_fun_soc   = new class_funciones_soc();
	$io_report    = new sigesp_soc_class_report($con);
	$ls_tiporeporte=$io_fun_soc->uf_obtenervalor_get("tiporeporte",0);
	$ls_bolivares="Bs.";
		
	//----------------------------------------------------  Inicializacion de variables  -----------------------------------------------
	$lb_valido=true;
	//----------------------------------------------------  Parámetros del encabezado    -----------------------------------------------
	$ls_titulo ="LISTADO DE LAS ORDENES DE COMPRAS";	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	
	$ls_codprodes=$io_fun_soc->uf_obtenervalor_get("codprodes","");
	$ls_codprohas=$io_fun_soc->uf_obtenervalor_get("codprohas","");
	$ls_fecordcomdes=$io_fun_soc->uf_obtenervalor_get("fecordcomdes","");
	$ls_fecordcomhas=$io_fun_soc->uf_obtenervalor_get("fecordcomhas","");
	$ls_montot=$io_fun_soc->uf_obtenervalor_get("montot",0);

	$ls_codesp=$io_fun_soc->uf_obtenervalor_get("hidcodesp","");
	$ls_unitri=$io_fun_soc->uf_obtenervalor_get("unitri","");
	$ls_orden=$io_fun_soc->uf_obtenervalor_get("orden","");

	//--------------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "listado.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = $lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	$lo_encabezado= $lo_libro->addformat();
	$lo_encabezado->set_bold();
	$lo_encabezado->set_font("Verdana");
	$lo_encabezado->set_align('center');
	$lo_encabezado->set_size('11');
	$lo_titulo= $lo_libro->addformat();
	$lo_titulo->set_bold();
	$lo_titulo->set_font("Verdana");
	$lo_titulo->set_align('center');
	$lo_titulo->set_size('9');
	$lo_datacenter= $lo_libro->addformat();
	$lo_datacenter->set_font("Verdana");
	$lo_datacenter->set_align('center');
	$lo_datacenter->set_size('9');
	$lo_dataleft= $lo_libro->addformat();
	$lo_dataleft->set_text_wrap();
	$lo_dataleft->set_font("Verdana");
	$lo_dataleft->set_align('left');
	$lo_dataleft->set_size('9');
	$lo_dataright= $lo_libro->addformat(array('num_format' => '#,##0.00'));
	$lo_dataright->set_font("Verdana");
	$lo_dataright->set_align('right');
	$lo_dataright->set_size('9');
	$lo_hoja->set_column(0,0,15);
	$lo_hoja->set_column(1,1,20);
	$lo_hoja->set_column(2,2,30);
	$lo_hoja->set_column(3,3,20);
	$lo_hoja->set_column(4,4,30);
	$lo_hoja->set_column(5,5,30);
	$lo_hoja->set_column(6,6,30);

	$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);

	$li_fila=2;

	$arrResultado = $io_report->uf_select_listado_orden_compra_proveedor($ls_codprodes,$ls_codprohas,$ls_fecordcomdes,$ls_fecordcomhas,
															$ls_montot,$ls_codesp,$ls_unitri,$ls_orden,$lb_valido);
	$rs_data = $arrResultado['rs_data'];
	$lb_valido = $arrResultado['lb_valido'];
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		$ls_descripcion="Generó el Reporte de Listado de Orden de Compra";
		$lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_r_orden_compra.php",$ls_descripcion);
		if($lb_valido)
		{
			$li_valoruni = 0;
			$li_valoruni = $io_report->uf_select_unidadtributaria($li_valoruni);
		}
		if($lb_valido)	
		{
			set_time_limit(1800);
			uf_print_encabezado_pagina($lo_libro,$lo_hoja,$ls_titulo,$li_fila);
			$ldec_monto=0;
			$li_i=0;
			$li_valoruni=($li_valoruni*2500);
			$ls_montot = str_replace(".","",$ls_montot);
			$ls_montot = str_replace(",",".",$ls_montot);	
			$la_data=Array();
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codpro  = $row["cod_pro"];
				$ls_nompro  = $row["nompro"];
				$ls_rifpro  = $row["rifpro"];
				$ls_telpro  = $row["telpro"];
				$ls_montotal = $row["montot"];
				$ls_cantidad = $row["cantidad"];
				if($ls_montotal>=$ls_montot)
				{
					if($ls_unitri==1)
					{
						if($ls_montotal>=$li_valoruni)
						{
							$ldec_monto=$ldec_monto+$ls_montotal;
							$ls_montotal   = number_format($ls_montotal,2,",",".");	
							$ls_cantidad   = number_format($ls_cantidad,2,",",".");	
							$li_i=$li_i+1;
							$la_data[$li_i]= array('codigo'=>$ls_codpro,'nombre'=>$ls_nompro,'rifpro'=>$ls_rifpro,
													'telpro'=>$ls_telpro,'monto'=>$ls_montotal,'cantidad'=>$ls_cantidad);
						}
					}
					else
					{
						$ldec_monto=$ldec_monto+$ls_montotal;
						$ls_montotal   = number_format($ls_montotal,2,",",".");	
						$ls_cantidad   = number_format($ls_cantidad,2,",",".");	
						$li_i=$li_i+1;
						$la_data[$li_i]= array('codigo'=>$ls_codpro,'nombre'=>$ls_nompro,'rifpro'=>$ls_rifpro,
												'telpro'=>$ls_telpro,'monto'=>$ls_montotal,'cantidad'=>$ls_cantidad);
					}
				}
			
			}
			if(count((array)$la_data)>0)
			{
				$ldec_monto  = number_format($ldec_monto,2,",",".");	
				uf_print_detalle($lo_libro,$lo_hoja,$la_data,$li_i,$ldec_monto,$li_fila);
			}
			else
			{
				print("<script language=JavaScript>");
				print("alert('No hay nada que reportar');"); 
				print("close();");
				print("</script>");		
			}
			if($lb_valido) // Si no ocurrio ningún error
			{
	
				$lo_libro->close();
				header("Content-Type: application/x-msexcel; name=\"listado.xls\"");
				header("Content-Disposition: inline; filename=\"listado.xls\"");
				$fh=fopen($lo_archivo, "rb");
				fpassthru($fh);
				unlink($lo_archivo);
				print("<script language=JavaScript>");
				print(" close();");
				print("</script>");
			}
		}
		else
		{
			print("<script language=JavaScript>");
			print("alert('No hay nada que reportar');"); 
			print("close();");
			print("</script>");		
		}				
	}	
	unset($io_report);
	unset($io_funciones);
?> 