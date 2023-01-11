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
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time ','0');
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
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
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_solicitudesf1.php",$ls_descripcion);
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
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 16/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;
		
		for ($index = 0; $index < ($li_totrow+1); $index++)
		{
			//print $index.' Solicitud'.$la_data[$index]["numsol"].'<br>';
			$lo_hoja->write($li_fila, 0, $la_data[$index]["numsol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $la_data[$index]["nombre"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 2, $la_data[$index]["fecemisol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $la_data[$index]["denest"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 4, $la_data[$index]["monsol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$li_fila++;
		}
		//print $index.':final  <br>';
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
	$ls_titulo="Declaracion de Salarios y Otras Remuneraciones";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$fecemides=$io_fun_cxp->uf_obtenervalor_get("fecemides","");
	$fecemihas=$io_fun_cxp->uf_obtenervalor_get("fecemihas","");
	$year=$io_fun_cxp->uf_obtenervalor_get("year","");
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
	$lo_hoja->set_column(0,0,5);
	$lo_hoja->set_column(1,1,30);
	$lo_hoja->set_column(2,2,20);
	$lo_hoja->set_column(3,3,20);
	$lo_hoja->set_column(4,4,20);
	$lo_hoja->set_column(5,5,30);
	$lo_hoja->set_column(6,6,20);
	$lo_hoja->set_column(7,7,20);
	$lo_hoja->set_column(8,8,30);
		
	$ls_subtitulo="";
	$ls_subtitulo=" Desde: ".$fecemides." Hasta: ".$fecemihas."";

	$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
	$lo_hoja->write(1, 3, $ls_subtitulo,$lo_encabezado);

	$lo_hoja->write(3, 1, 'RIF Proveedor/Beneficiario',$lo_titulo);
	$lo_hoja->write(3, 2, 'Factura',$lo_titulo);
	$lo_hoja->write(3, 3, 'Numero Control',$lo_titulo);
	$lo_hoja->write(3, 4, 'Fecha Factura',$lo_titulo);
	$lo_hoja->write(3, 5, 'Codigo Concepto',$lo_titulo);
	$lo_hoja->write(3, 6, 'Monto',$lo_titulo);
	$lo_hoja->write(3, 7, '% Retencion',$lo_titulo);
	$li_fila=3;

	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{

			$ls_periodo=str_pad($li_i,2,"0",0);
			$ld_fechadesde=$io_funciones->uf_convertirdatetobd($fecemides);
			$ld_fechahasta=$io_funciones->uf_convertirdatetobd($fecemihas);
			$rs_data=$io_report->uf_declaracion_xml_cabecera($ld_fechadesde,$ld_fechahasta,"",$year);
			while(!$rs_data->EOF)
			{
				$ls_rifpro=str_replace("-","",trim($rs_data->fields["rifpro"]));
				$ls_rifben=str_replace("-","",trim($rs_data->fields["rifben"]));
				if($ls_rifpro!="")
				{
					$ls_rif=$ls_rifpro;
				}
				else
				{
					$ls_rif=$ls_rifben;
				}
				$ls_numrecdoc=trim($rs_data->fields["numrecdoc"]);
				$ls_numref=trim($rs_data->fields["numref"]);
				if($ls_numref=="")
				{
					$ls_numref="NA";
				}
				$ls_numref=str_replace("-","",trim($ls_numref));
				$ls_numrecdoc=str_replace("-","",trim($ls_numrecdoc));
				$li_baseimp=number_format($rs_data->fields["baseimp"],2,'.','');
				$ls_codconret=trim($rs_data->fields["codconret"]);
				$ls_codper=trim($rs_data->fields["codper"]);
				$li_porded=number_format($rs_data->fields["porded"],2,'.','');
				$ls_procedencia=trim($rs_data->fields["procedencia"]);
				$ld_fecemidoc=$io_report->io_funciones->uf_convertirfecmostrar(trim($rs_data->fields["fecemidoc"]));
				$correcto=true;
				$li_lenrif=strlen($ls_rif);
				if ($ls_procedencia=='CXP')
				{
					if ((trim($ls_rif)=="")||($li_lenrif<10))
					{
						$ls_cadena=$ls_cadena."La factura ".$ls_numrecdoc." no se pudo agregar ya que el proveedor/beneficiario asociado no posee rif. \r\n";
						$correcto=false;
					}
					if ((trim($ls_codconret)==""))
					{
						$ls_cadena=$ls_cadena."La factura ".$ls_numrecdoc." no se pudo agregar ya que la deducción no posee Concepto de Retención asociado. \r\n";
						$correcto=false;
					}
				}
				if ($ls_procedencia=='SNO')
				{
					if ((trim($ls_rif)=="")||($li_lenrif<10))
					{
						$ls_cadena=$ls_cadena."El personal  ".$ls_codper." no se pudo agregar ya que no posee rif. \r\n";
						$correcto=false;
					}
					if ((trim($ls_codconret)==""))
					{
						$ls_cadena=$ls_cadena."El personal  ".$ls_codper." no se pudo agregar ya que la deducción no posee Concepto de Retención asociado. \r\n";
						$correcto=false;
					}
				}		
				if($correcto)
				{
					$li_fila++;
					$lo_hoja->write($li_fila, 1, $ls_rif,$lo_dataleft);
					$lo_hoja->write($li_fila, 2, $ls_numrecdoc,$lo_dataleft);
					$lo_hoja->write($li_fila, 3, $ls_numref,$lo_dataleft);
					$lo_hoja->write($li_fila, 4, $ld_fecemidoc,$lo_datacenter);
					$lo_hoja->write($li_fila, 5, $ls_codconret,$lo_dataleft);
					$lo_hoja->write($li_fila, 6, $li_baseimp,$lo_dataright);
					$lo_hoja->write($li_fila, 7, $li_porded,$lo_datacenter);
				}
				$rs_data->MoveNext();
			}
		if($lb_valido) // Si no ocurrio ningún error
		{

			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"solicitudes_f1.xls\"");
			header("Content-Disposition: inline; filename=\"solicitudes_f1.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
			print("<script language=JavaScript>");
			//print(" close();");
			print("</script>");
		}
	}

?>
