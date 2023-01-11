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
	ini_set('memory_limit','512M');
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
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_solicitudesf2.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($lo_libro,$lo_hoja,$li_fila)
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
		

		$lo_hoja->write($li_fila, 0, 'Codigo',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 1, 'Nombre',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 2, 'Solicitud',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 3, 'Fecha Emisión',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 4, 'Concepto',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 5, 'Estatus',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 6, 'Monto',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));

		$li_fila++;

	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------



	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($lo_libro,$lo_hoja,$la_data,$li_totrow,$li_fila)
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
		
		for ($index = 1; $index < ($li_totrow+1); $index++)
		{

			$lo_hoja->write($li_fila, 0, ' '.str_pad($la_data[$index]["codigo"],10,'0',STR_PAD_LEFT),$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'center','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $la_data[$index]["nombre"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 2, $la_data[$index]["numsol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $la_data[$index]["fecemisol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 4, $la_data[$index]["consol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 5, $la_data[$index]["denest"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 6, $la_data[$index]["monsol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'right','size'=>'9')));

			$li_fila++;

		}

	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "solicitudes_f2.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$ls_estmodest=$_SESSION["la_empresa"]["estmodest"];
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="SOLICITUDES DE PAGO";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_solicitudes=$io_fun_cxp->uf_obtenervalor_get("solicitudes","");
    $lr_solicitudes= explode('>>',$ls_solicitudes);
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
    $lr_datos= array_unique($lr_solicitudes);
    $li_total= count((array)$lr_datos);
	sort($lr_datos,SORT_STRING);
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		if($li_total==0) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');");
			print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{

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

			$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);

			$li_fila=2;
			$ls_estretiva=$_SESSION["la_empresa"]["estretiva"];
			uf_print_encabezado_pagina($lo_libro,$lo_hoja,$li_fila);
			for($li_i=0;$li_i<$li_total;$li_i++)
			{
				$li_totmonsol=0;
				$ls_numsol=$lr_datos[$li_i];
				$lb_valido=$io_report->uf_select_solicitudf2($ls_numsol);
				$li_j=1;
				while (!$io_report->rs_data->EOF)
				{
					$ls_numsol=$io_report->rs_data->fields["numsol"];
					$ls_nombre=$io_report->rs_data->fields["nombre"];
					$ld_fecemisol=$io_report->rs_data->fields["fecemisol"];
					$ls_estprosol=$io_report->rs_data->fields["estprosol"];
					$li_monsol=$io_report->rs_data->fields["monsol"];
					$ls_consol=$io_report->rs_data->fields["consol"];
					$ls_tipproben=$io_report->rs_data->fields["tipproben"];
					if ($ls_tipproben=='P')
					{
						$ls_codigo=$io_report->rs_data->fields["cod_pro"];
					}
					else
					{
						$ls_codigo=$io_report->rs_data->fields["ced_bene"];
					}
					switch ($ls_estprosol)
					{
						case 'E':
							$ls_denest='Emitida';
							break;
						case 'C':
							$ls_denest='Contabilizada';
							break;
						case 'A':
							$ls_denest='Anulada';
							break;
						case 'S':
							$ls_denest='Programacion de Pago';
							break;
						case 'P':
							$ls_denest='Pagada';
							break;
						case "N":
							$ls_denest="Anulada sin Afectacion";
							break;
					}
					if($ls_estretiva=="B")
					{
						$li_monretiva=$io_report->uf_select_det_deducciones_solpag($ls_numsol);
						$li_monsol=$li_monsol+$li_monretiva;
					}
					$li_totmonsol=$li_totmonsol+$li_monsol;
					$li_monsol=number_format($li_monsol,2,",",".");
					$ld_fecemisol=$io_funciones->uf_convertirfecmostrar($ld_fecemisol);
					$la_data[$li_j]=array('numsol'=>$ls_numsol,'fecemisol'=>$ld_fecemisol,'consol'=>$ls_consol,
										  'denest'=>$ls_denest,'monsol'=>$li_monsol,'codigo'=>$ls_codigo,'nombre'=>$ls_nombre);
					$li_j++;
					$io_report->rs_data->MoveNext();
				}
				$li_j=$li_j-1;
				$li_totmonsol=number_format($li_totmonsol,2,",",".");

				uf_print_detalle($lo_libro,$lo_hoja,$la_data,$li_j,$li_fila);
			}
		}
		if($lb_valido) // Si no ocurrio ningún error
		{
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"solicitudes_f2.xls\"");
			header("Content-Disposition: inline; filename=\"solicitudes_f2.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
			print("<script language=JavaScript>");
		//	print(" close();");
			print("</script>");		}

	}

?>
