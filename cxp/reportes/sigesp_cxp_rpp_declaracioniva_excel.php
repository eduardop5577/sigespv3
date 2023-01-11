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
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time ','0');
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
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_relacionsolicitudes.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
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
		

		$lo_hoja->write($li_fila, 0, 'Solicitud',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 1, 'Proveedor/Beneficiario',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 2, 'Fecha Emisión',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 3, 'Estatus',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 4, 'Monto Bs.',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
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
		//	   Creado Por:  Ing. Luis Lang
		// Fecha Creación: 01/03/2016
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;
		
		for ($index = 0; $index < ($li_totrow+1); $index++)
		{
			//print $index.' Solicitud'.$la_data[$index]["numsol"].'<br>';
			$lo_hoja->write($li_fila, 0, $la_data[$index]["numsol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $la_data[$index]["nombre"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 2, $la_data[$index]["fecemisol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $la_data[$index]["estprosol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 4, $la_data[$index]["monsol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$li_fila++;
		}

	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "solicitudes_f1.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$ls_estmodest=$_SESSION["la_empresa"]["estmodest"];
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="Declaración Informativa de Retenciones IVA";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ld_fecemides=$io_fun_cxp->uf_obtenervalor_get("fecemides","");
	$ld_fecemihas=$io_fun_cxp->uf_obtenervalor_get("fecemihas","");
	$ls_quincena=$io_fun_cxp->uf_obtenervalor_get("quincena","");

			set_time_limit(1800);

			$lo_encabezado= &$lo_libro->addformat();
			$lo_encabezado->set_bold();
			$lo_encabezado->set_font("Verdana");
			$lo_encabezado->set_align('center');
			$lo_encabezado->set_size('11');
			$lo_titulo= &$lo_libro->addformat();
			$lo_titulo->set_bold();
			$lo_titulo->set_font("Verdana");
			$lo_titulo->set_align('center');
			$lo_titulo->set_size('9');
			$lo_datacenter= &$lo_libro->addformat();
			$lo_datacenter->set_font("Verdana");
			$lo_datacenter->set_align('center');
			$lo_datacenter->set_size('9');
			$lo_dataleft= &$lo_libro->addformat();
			$lo_dataleft->set_text_wrap();
			$lo_dataleft->set_font("Verdana");
			$lo_dataleft->set_align('left');
			$lo_dataleft->set_size('9');
			$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
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
			$lo_hoja->set_column(6,7,30);
			$lo_hoja->set_column(6,8,30);
			$lo_hoja->set_column(6,9,30);
			$lo_hoja->set_column(6,10,30);
			$lo_hoja->set_column(6,11,30);
			$lo_hoja->set_column(6,12,30);
			$lo_hoja->set_column(6,13,30);
			$lo_hoja->set_column(6,14,30);
			$lo_hoja->set_column(6,15,30);
			$lo_hoja->set_column(6,16,30);
				
			$ls_subtitulo="";
			if(($ld_fecemides!="")&&($ld_fecemihas!=""))
			{
				$ls_subtitulo="Fecha. Desde: ".$ld_fecemides." Hasta: ".$ld_fecemihas."";
			}

			$lo_hoja->write(0, 2, $ls_titulo,$lo_encabezado);

			$li_fila=3;

	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
			$ld_fecemides=$io_funciones->uf_convertirdatetobd($ld_fecemides);
			$ld_fecemihas=$io_funciones->uf_convertirdatetobd($ld_fecemihas);

		$lb_valido=$io_report->uf_declaracioninformativa_excel($ld_fecemides,$ld_fecemihas,$ls_anio,$la_seguridad); // Cargar el DS con los datos del reporte
		if($lb_valido==false) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			//print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{
			$li_totrow=$io_report->DS->getRowCount("numcom");
			$li_totmonsol="";
			$ls_agenteretencion=$_SESSION["la_empresa"]["nombre"];
			$ls_rifagenteret=str_replace('-','',$_SESSION["la_empresa"]["rifemp"]);
			$ls_direccionagenteret=$_SESSION["la_empresa"]["direccion"];
			
			$lo_hoja->write($li_fila, 1, "RIF AGENTE",$lo_titulo);
			$lo_hoja->write($li_fila, 2, "PERIODO FISCAL",$lo_titulo);
			$lo_hoja->write($li_fila, 3, "FECHA DE FACTURA",$lo_titulo);
			$lo_hoja->write($li_fila, 4, "OPERACION",$lo_titulo);
			$lo_hoja->write($li_fila, 5, "DOCUMENTO",$lo_titulo);
			$lo_hoja->write($li_fila, 6, "RIF",$lo_titulo);
			$lo_hoja->write($li_fila, 7, "FACTURA",$lo_titulo);
			$lo_hoja->write($li_fila, 8, "REFERENCIA",$lo_titulo);
			$lo_hoja->write($li_fila, 9, "TOTAL CON IVA",$lo_titulo);
			$lo_hoja->write($li_fila, 10,"BASE IMPONIBLE",$lo_titulo);
			$lo_hoja->write($li_fila, 11,"IVA RETENIDO",$lo_titulo);
			$lo_hoja->write($li_fila, 12, "FACTURA AFECTADA",$lo_titulo);
			$lo_hoja->write($li_fila, 13, "COMPROBANTE"." ",$lo_titulo);
			$lo_hoja->write($li_fila, 14, "TOTAL SIN IVA",$lo_titulo);
			$lo_hoja->write($li_fila, 15, "PORCENTAJE",$lo_titulo);
			$lo_hoja->write($li_fila, 16, "EXPEDIENTE",$lo_titulo);
			$li_fila++;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numcom=$io_report->DS->data["numcom"][$li_i];
				$ls_perfiscal=$io_report->DS->data["perfiscal"][$li_i];
				$ls_codsujret=$io_report->DS->data["codsujret"][$li_i];
				$ls_nomsujret=$io_report->DS->data["nomsujret"][$li_i];
				$ls_dirsujret=$io_report->DS->data["dirsujret"][$li_i];
				$ls_rif=$io_report->DS->data["rif"][$li_i];
				$lb_valido=$io_report->uf_retencionesiva_detalle($ls_numcom); // Cargar el DS con los datos del reporte
				if($lb_valido)
				{
					if(strlen($ls_numcom)==15)
					{
						$ls_numcom1=substr($ls_numcom,0,6);
						$ls_numcom2=substr($ls_numcom,6,8);
						$ls_numcom =$ls_numcom1.$ls_numcom2;
					}
					$li_total=$io_report->ds_detalle->getRowCount("numfac");
					for($li_j=1;$li_j<=$li_total;$li_j++)
					{
						$ls_numope=$io_report->ds_detalle->data["numope"][$li_j];
						$ls_numfac=trim($io_report->ds_detalle->data["numfac"][$li_j]);
						$ls_numref=trim($io_report->ds_detalle->data["numcon"][$li_j]);
						$ld_fecfac=substr($io_report->ds_detalle->data["fecfac"][$li_j],0,10);
						$li_siniva=number_format($io_report->ds_detalle->data["totcmp_sin_iva"][$li_j],2,".","");
						$li_coniva=number_format($io_report->ds_detalle->data["totcmp_con_iva"][$li_j],2,".","");
						$li_baseimp=number_format($io_report->ds_detalle->data["basimp"][$li_j],2,".","");
						$li_porimp=number_format($io_report->ds_detalle->data["porimp"][$li_j],2,".","");
						$li_totimp=number_format($io_report->ds_detalle->data["totimp"][$li_j],2,".","");
						$li_ivaret=number_format($io_report->ds_detalle->data["iva_ret"][$li_j],2,".","");
						$ls_numdoc=$io_report->ds_detalle->data["numdoc"][$li_j];
						$ls_tiptrans=$io_report->ds_detalle->data["tiptrans"][$li_j];
						$ls_numnotdeb=$io_report->ds_detalle->data["numnd"][$li_j];
						$ls_numnotcre=$io_report->ds_detalle->data["numnc"][$li_j];
						$li_monto=$li_baseimp + $li_totimp;
						$li_totdersiniva= number_format(abs($li_coniva - $li_monto),2,".","");
						$ls_numfacafec="0";
						$ls_tipope="C";
						$ls_tipdoc="01";
						$ls_numexp="0";
						
/*						$la_data[$li_j]=array('rifagenteret'=>$ls_rifagenteret,'perfiscal'=>$ls_perfiscal,'fecfac'=>$ld_fecfac,'tipope'=>$ls_tipope,'tipdoc'=>$ls_tipdoc,
												'rif'=>$ls_rif,'numfac'=>$ls_numfac,'numref'=>$ls_numref,'coniva'=>$li_coniva,'baseimp'=>$li_baseimp,'ivaret'=>$li_ivaret,
					  							'numfacafec'=>$ls_numfacafec,'numcom'=>$ls_numcom,'totdersiniva'=>$li_totdersiniva,'porimp'=>$li_porimp,'numexp'=>$ls_numexp);
*/
						$lo_hoja->write($li_fila, 1, $ls_rifagenteret,$lo_dataleft);
						$lo_hoja->write($li_fila, 2, $ls_perfiscal,$lo_dataleft);
						$lo_hoja->write($li_fila, 3, $ld_fecfac,$lo_dataleft);
						$lo_hoja->write($li_fila, 4, $ls_tipope,$lo_dataleft);
						$lo_hoja->write($li_fila, 5, " ".$ls_tipdoc,$lo_dataleft);
						$lo_hoja->write($li_fila, 6, $ls_rif,$lo_dataleft);
						$lo_hoja->write($li_fila, 7, $ls_numfac,$lo_dataleft);
						$lo_hoja->write($li_fila, 8, $ls_numref,$lo_dataleft);
						$lo_hoja->write($li_fila, 9, $li_coniva,$lo_dataright);
						$lo_hoja->write($li_fila, 10, $li_baseimp,$lo_dataright);
						$lo_hoja->write($li_fila, 11, $li_ivaret,$lo_dataright);
						$lo_hoja->write($li_fila, 12, $ls_numfacafec,$lo_dataleft);
						$lo_hoja->write($li_fila, 13, $ls_numcom." ",$lo_dataleft);
						$lo_hoja->write($li_fila, 14, $li_totdersiniva,$lo_dataright);
						$lo_hoja->write($li_fila, 15, $li_porimp,$lo_dataright);
						$lo_hoja->write($li_fila, 16, $ls_numexp,$lo_dataleft);
						$li_fila++;
					}
				}
			}
		}
		if($lb_valido) // Si no ocurrio ningún error
		{

			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"relacion_solicitudes.xls\"");
			header("Content-Disposition: inline; filename=\"relacion_solicitudes.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
			print("<script language=JavaScript>");
			//print(" close();");
			print("</script>");
		}
		
	}

?>
