<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
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
		print "close();";
		print "</script>";
	}

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($lo_libro,$lo_hoja,$as_programatica,$as_denestpro)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: privates
		//	    Arguments: as_programatica // programatica del comprobante
		//	    		   as_denestpro5 // denominacion de la programatica del comprobante
		//	    		   io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 21/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;
		if ($_SESSION["la_empresa"]["estmodest"] == 2)
		{
			$lo_hoja->write($li_fila, 0, "Programatica: ".$as_programatica,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$li_fila++;
			$lo_hoja->write($li_fila, 0, $as_denestpro,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$li_fila++;
		}
		else
		{
		 	$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	 		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	 		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];


			$lo_hoja->write($li_fila, 0, "ESTRUCTURA PRESUPUESTARIA: ",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$li_fila++;

			$lo_hoja->write($li_fila, 0, " ".substr($as_programatica,0,$ls_loncodestpro1),$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, " ".$as_denestpro[0],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$li_fila++;
			$lo_hoja->write($li_fila, 0, " ".substr($as_programatica,$ls_loncodestpro1,$ls_loncodestpro2),$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, " ".$as_denestpro[1],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$li_fila++;
			$lo_hoja->write($li_fila, 0, " ".substr($as_programatica,$ls_loncodestpro1+$ls_loncodestpro2,$ls_loncodestpro3),$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, " ".$as_denestpro[2],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$li_fila++;
		}
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_detalle($lo_libro,$lo_hoja)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 21/04/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;
		
			$li_fila++;
			$lo_hoja->write($li_fila, 0, "Cuenta ".$as_programatica,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, "Denominación ".$as_programatica,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 2, "Asignado ".$as_programatica,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 3, "Actualizado ".$as_programatica,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 4, "Disponibilidad ".$as_programatica,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$li_fila++;
		
	}// end function uf_print_cabecera_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($lo_libro,$lo_hoja,$la_data,$li_inicio,$li_total)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 21/04/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;
		
		for($li_j=$li_inicio;$li_j<=$li_total+$li_inicio;$li_j++)
		{
			$lo_hoja->write($li_fila, 0, $la_data[$li_j]['cuenta'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $la_data[$li_j]['denominacion'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 2, $la_data[$li_j]['asignado'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $la_data[$li_j]['actualizado'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 4, $la_data[$li_j]['disponibilidad'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$li_fila++;
		}
//		$li_fila++;
		
		
		
		
		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($lo_libro,$lo_hoja,$ad_totalasignado,$ad_totalactualizado,$ad_totaldisponible)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación : 18/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;
		
			$lo_hoja->write($li_fila, 2, $ad_totalasignado,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $ad_totalactualizado,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 4, $ad_totaldisponible,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$li_fila++;
			$li_fila++;
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
		require_once("sigesp_spg_reporte.php");
		$io_report = new sigesp_spg_reporte();
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report = new sigesp_spg_funciones_reportes();
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
	//--------------------------------------------  Llamada a clases de gneracion de excel  ------------------------------------------
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "cuentas_por_pagar.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//----------------------------------------------------------------------------------------------------------------------------
		 require_once("sigesp_spg_reporte.php");
		 $io_report = new sigesp_spg_reporte();
		 
    //--------------------------------------------------  Parámetros para Filtar el Reporte  --------------------------------------
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$ls_codestpro1_min  = $_GET["codestpro1"];
		$ls_codestpro2_min  = $_GET["codestpro2"];
		$ls_codestpro3_min  = $_GET["codestpro3"];
		$ls_codestpro1h_max = $_GET["codestpro1h"];
		$ls_codestpro2h_max = $_GET["codestpro2h"];
		$ls_codestpro3h_max = $_GET["codestpro3h"];
	    $ls_estclades       = $_GET["estclades"];
	    $ls_estclahas       = $_GET["estclahas"];
		if($li_estmodest==1)
		{
			$ls_codestpro4_min  = "0000000000000000000000000";
			$ls_codestpro5_min  = "0000000000000000000000000";
			$ls_codestpro4h_max = "0000000000000000000000000";
			$ls_codestpro5h_max = "0000000000000000000000000";
			if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min==""))
			{
			  if($io_function_report->uf_spg_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,                                                                             $ls_codestpro3_min,$ls_codestpro4_min,
			                                                                 $ls_codestpro5_min,$ls_estclades))
			  {
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			  }
			}
			else
			{
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			}
			if(($ls_codestpro1h_max=="")&&($ls_codestpro2h_max=="")&&($ls_codestpro3h_max==""))
			{
			  if($io_function_report->uf_spg_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
																			 $ls_codestpro3h_max,$ls_codestpro4h_max,
																			 $ls_codestpro4h_max,$ls_estclahas))
			  {
					$ls_codestpro1h  = $ls_codestpro1h_max;
					$ls_codestpro2h  = $ls_codestpro2h_max;
					$ls_codestpro3h  = $ls_codestpro3h_max;
					$ls_codestpro4h  = $ls_codestpro4h_max;
					$ls_codestpro5h  = $ls_codestpro5h_max;
			  }
			}
			else
			{
					$ls_codestpro1h  = $ls_codestpro1h_max;
					$ls_codestpro2h  = $ls_codestpro2h_max;
					$ls_codestpro3h  = $ls_codestpro3h_max;
					$ls_codestpro4h  = $ls_codestpro4h_max;
					$ls_codestpro5h  = $ls_codestpro5h_max;
			}
		}
		elseif($li_estmodest==2)
		{
			$ls_codestpro4_min = $_GET["codestpro4"];
			$ls_codestpro5_min = $_GET["codestpro5"];
			$ls_codestpro4h_max = $_GET["codestpro4h"];
			$ls_codestpro5h_max = $_GET["codestpro5h"];
			if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min=="")&&($ls_codestpro4_min=="")&&
			   ($ls_codestpro5_min==""))
			{
			  if($io_function_report->uf_spg_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,
			                                                                 $ls_codestpro3_min,$ls_codestpro4_min,
			                                                                 $ls_codestpro5_min,$ls_estclades))
			  {
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			  }
			}
			else
			{
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			}
			if(($ls_codestpro1h_max=="")&&($ls_codestpro2h_max=="")&&($ls_codestpro3h_max=="")&&($ls_codestpro4h_max=="")&&
			   ($ls_codestpro5h_max==""))
			{
			  if($io_function_report->uf_spg_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
																			 $ls_codestpro3h_max,$ls_codestpro4h_max,
																			 $ls_codestpro5h_max,$ls_estclahas))
			  {
				$ls_codestpro1h  = $ls_codestpro1h_max;
				$ls_codestpro2h  = $ls_codestpro2h_max;
				$ls_codestpro3h  = $ls_codestpro3h_max;
				$ls_codestpro4h  = $ls_codestpro4h_max;
				$ls_codestpro5h  = $ls_codestpro5h_max;
			  }
			}
			else
			{
				$ls_codestpro1h  = $ls_codestpro1h_max;
				$ls_codestpro2h  = $ls_codestpro2h_max;
				$ls_codestpro3h  = $ls_codestpro3h_max;
				$ls_codestpro4h  = $ls_codestpro4h_max;
				$ls_codestpro5h  = $ls_codestpro5h_max;
			}
		}


	    $ls_cuentades_min=$_GET["txtcuentades"];
	    $ls_cuentahas_max=$_GET["txtcuentahas"];
		if($ls_cuentades_min=="")
		{
		   if($io_function_report->uf_spg_reporte_select_min_cuenta($ls_cuentades_min))
		   {
		     $ls_cuentades=$ls_cuentades_min;
		   }
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestraias');");
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentades=$ls_cuentades_min;
		}
		if($ls_cuentahas_max=="")
		{
		   if($io_function_report->uf_spg_reporte_select_max_cuenta($ls_cuentahas_max))
		   {
		     $ls_cuentahas=$ls_cuentahas_max;
		   }
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestraias');");
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentahas=$ls_cuentahas_max;
		}
	   $fechas=$_GET["txtfechas"];
	   if (!empty($fechas))
	   {
			$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
	   }	else {  $ldt_fechas=""; }

       $li_ckbhasfec=$_GET["ckbhasfec"];
       $li_ckbctasinmov=$_GET["ckbctasinmov"];

	   if($li_ckbhasfec==1)
	   {
		  $ldt_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);
		  $ldt_fecdes=$ldt_ano."-01"."-01";

	   }
	   else
	   {
		  $ldt_fecdes="00-00-0000";
	   }
	   
	   $ls_fecha_titulo=$io_funciones->uf_convertirfecmostrar($ldt_fechas);

	   $ls_codfuefindes=$_GET["txtcodfuefindes"];
	   $ls_codfuefinhas=$_GET["txtcodfuefinhas"];
	   
	 /////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_programatica_desde=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estclades;
	 $ls_programatica_hasta=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h.$ls_estclahas;
	 $ls_desc_event="Solicitud de Reporte Disponibilidad Presupuestaria Desde la Cuenta ".$ls_cuentades." hasta ".$ls_cuentahas." ,  Desde la Programatica  ".$ls_programatica_desde." hasta ".$ls_programatica_hasta;
	 $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_spg_r_disponibilidad.php",$ls_desc_event);
	////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   //----------------------------------------------------  Parámetros del encabezado  ----------------------------------------------------------------------------------------------------------------------------------------
		$ls_titulo="DISPONIBILIDAD PRESUPUESTARIA ";
		$ls_fecha="HASTA LA FECHA  ".$ls_fecha_titulo;
  //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )
	$ls_codestpro1  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
	$ls_codestpro2  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
	$ls_codestpro3  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
	$ls_codestpro4  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
	$ls_codestpro5  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
	//print "PROGRAMATICA: ".$ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3."-".$ls_codestpro4."-".$ls_codestpro5;
	$ls_codestpro1h  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
	$ls_codestpro2h  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
	$ls_codestpro3h  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
	$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
	$ls_codestpro5h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
	$lb_valido=$io_report->uf_spg_reporte_disponibilidad_cuenta($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
	                                                            $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
																$ls_codestpro4h,$ls_codestpro5h,$ldt_fecdes,$ldt_fechas,
								                                $ls_cuentades,$ls_cuentahas,$li_ckbctasinmov,$li_ckbhasfec,
																$ls_codfuefindes,$ls_codfuefinhas,$ls_estclades,$ls_estclahas);
	 if($lb_valido==false) // Existe algún error ó no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');");
		print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
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
		$lo_hoja->set_column(1,1,60);
		$lo_hoja->set_column(2,2,20);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,20);
		$lo_hoja->set_column(5,5,20);
		$lo_hoja->set_column(6,6,20);
		$lo_hoja->set_column(6,7,20);
		$lo_hoja->set_column(6,8,20);
		$lo_hoja->set_column(6,9,20);
		$lo_hoja->set_column(6,10,20);

		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 3, $ls_fecha,$lo_encabezado);

		$li_fila=3;



		$io_report->dts_reporte->group_noorder("programatica");
		$li_tot=$io_report->dts_reporte->getRowCount("spg_cuenta");
		$ld_totalasignado=0;
		$ld_totalactualizado=0;
		$ld_totaldisponible=0;
		$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
		$ld_progaux = "";
		if ($li_tot > 0)
		{
		$li_total=0;
		$li_inicio=1;
		for($z=1;$z<=$li_tot;$z++)
		{
		    $li_tmp=($z+1);
			$ls_progaux=$io_report->dts_reporte->data["programatica"][$z];
			if ($ls_progaux !="")
			{
			 $ls_programatica = $ls_progaux;
			}
			//$ls_programatica=$io_report->dts_reporte->data["programatica"][$z];
			$ls_estcla=substr($ls_programatica,-1);
			$ls_codestpro1=substr($ls_programatica,0,25);
		    $ls_denestpro1="";
		    $lb_valido=$io_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);
		    if($lb_valido)
		    {
			  $ls_denestpro1=trim($ls_denestpro1);
		    }
		    $ls_codestpro2=substr($ls_programatica,25,25);
		    if($lb_valido)
		    {
			  $ls_denestpro2="";
			  $lb_valido=$io_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
			  $ls_denestpro2=trim($ls_denestpro2);
		    }
		    $ls_codestpro3=substr($ls_programatica,50,25);
		    if($lb_valido)
		    {
			  $ls_denestpro3="";
			  $lb_valido=$io_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
			  $ls_denestpro3=trim($ls_denestpro3);
		    }
			$ls_spg_cuenta=$io_report->dts_reporte->data["spg_cuenta"][$z];
		    if ($z<$li_tot)
		    {
				$ls_programatica_next=$io_report->dts_reporte->data["programatica"][$li_tmp];
		    }
		    elseif($z=$li_tot)
		    {
				$ls_programatica_next='no_next';
		    }
			//if(empty($ls_programatica_next)&&(!empty($ls_programatica)))
			if((empty($ls_programatica_next))||(!empty($ls_programatica)))
			{
			    $ls_programatica_ant=$io_report->dts_reporte->data["programatica"][$z];
				if($li_estmodest==2)
				{
				    $ls_codestpro4=substr($ls_programatica,75,25);
					if($lb_valido)
					{
					  $ls_denestpro4="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estcla);
					  $ls_denestpro4=trim($ls_denestpro4);
					}
				    $ls_codestpro5=substr($ls_programatica,100,25);
					if($lb_valido)
					{
					  $ls_denestpro5="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
					  $ls_denestpro5=trim($ls_denestpro5);
					}
					$ls_denestpro_ant=trim($ls_denestpro1)." , ".trim($ls_denestpro2)." , ".trim($ls_denestpro3)." , ".trim($ls_denestpro4)." , ".trim($ls_denestpro5);
					$ls_programatica_ant=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
				}
				else
				{
			        //$ls_denestpro_ant=$ls_denestpro1." , ".$ls_denestpro2." , ".$ls_denestpro3;
					$ls_denestpro_ant = array();
					$ls_denestpro_ant[0]=$ls_denestpro1;
					$ls_denestpro_ant[1]=$ls_denestpro2;
					$ls_denestpro_ant[2]=$ls_denestpro3;
					$ls_programatica_ant=substr($ls_codestpro1,-$ls_loncodestpro1).substr($ls_codestpro2,-$ls_loncodestpro2).substr($ls_codestpro3,-$ls_loncodestpro3);
				}
			}
			if($li_tot==1)
			{
			    $ls_programatica_ant=$io_report->dts_reporte->data["programatica"][$z];
				if($li_estmodest==2)
				{
				    $ls_codestpro4=substr($ls_programatica,75,25);
					if($lb_valido)
					{
					  $ls_denestpro4="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_denestpro4,$ls_estcla);
					  $ls_denestpro4=trim($ls_denestpro4);
					}
				    $ls_codestpro5=substr($ls_programatica,100,25);
					if($lb_valido)
					{
					  $ls_denestpro5="";
					  $lb_valido=$io_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
					  $ls_denestpro5=trim($ls_denestpro5);
					}
					$ls_denestpro_ant=trim($ls_denestpro1)." , ".trim($ls_denestpro2)." , ".trim($ls_denestpro3)." , ".trim($ls_denestpro4)." , ".trim($ls_denestpro5);
					$ls_programatica_ant=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
				}
				else
				{
			       //$ls_denestpro_ant=$ls_denestpro1." , ".$ls_denestpro2." , ".$ls_denestpro3;
					$ls_denestpro_ant = array();
					$ls_denestpro_ant[0]=$ls_denestpro1;
					$ls_denestpro_ant[1]=$ls_denestpro2;
					$ls_denestpro_ant[2]=$ls_denestpro3;
					$ls_programatica_ant=substr($ls_codestpro1,-$ls_loncodestpro1).substr($ls_codestpro2,-$ls_loncodestpro2).substr($ls_codestpro3,-$ls_loncodestpro3);
				}
			}
			$ls_denominacion=trim($io_report->dts_reporte->data["denominacion"][$z]);
			$ld_asignado=$io_report->dts_reporte->data["asignado"][$z];
			$ld_actualizado=$io_report->dts_reporte->data["actualizado"][$z];
			$ld_disponible=$io_report->dts_reporte->data["disponible"][$z];
			$ls_status=$io_report->dts_reporte->data["status"][$z];

			if($ls_status=="C")
			{
				$ld_totalasignado=$ld_totalasignado+$ld_asignado;
				$ld_totalactualizado=$ld_totalactualizado+$ld_actualizado;
				$ld_totaldisponible=$ld_totaldisponible+$ld_disponible;
			}
            if ($ld_totaldisponible<0.00009)
            {
                if(intval($ld_totaldisponible)==0)
                {
                    $ld_totaldisponible = abs($ld_totaldisponible);
                }
                
            }

            if ($ld_totalactualizado<0.00009)
            {
            	if(intval($ld_totalactualizado)==0)
            	{
            		$ld_totalactualizado = abs($ld_totalactualizado);
            	}
            
            }
            
            
            if ($ld_disponible<0.00009)
            {
                if(intval($ld_disponible)==0)
                {
                    $ld_disponible = abs($ld_disponible);
                }
                
            }
            
            //print " ls_spg_cuenta : $ls_spg_cuenta      ,  ld_disponible : $ld_disponible    <br>";
			if (!empty($ls_programatica))
		    {
				$ld_asignado=number_format($ld_asignado,2,",",".");
				$ld_actualizado=number_format($ld_actualizado,2,",",".");
				$ld_disponible=number_format($ld_disponible,2,",",".");

				$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,'denominacion'=>$ls_denominacion, 'asignado'=>$ld_asignado,
								   'actualizado'=>$ld_actualizado,'disponibilidad'=>$ld_disponible);

				$ld_asignado=str_replace('.','',$ld_asignado);
				$ld_asignado=str_replace(',','.',$ld_asignado);
				$ld_actualizado=str_replace('.','',$ld_actualizado);
				$ld_actualizado=str_replace(',','.',$ld_actualizado);
				$ld_disponible=str_replace('.','',$ld_disponible);
				$ld_disponible=str_replace(',','.',$ld_disponible);
			}
			else
			{
				$ld_asignado=number_format($ld_asignado,2,",",".");
				$ld_actualizado=number_format($ld_actualizado,2,",",".");
				$ld_disponible=number_format($ld_disponible,2,",",".");

				$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,'denominacion'=>$ls_denominacion,'asignado'=>$ld_asignado,
								   'actualizado'=>$ld_actualizado,'disponibilidad'=>$ld_disponible);

				$ld_asignado=str_replace('.','',$ld_asignado);
				$ld_asignado=str_replace(',','.',$ld_asignado);
				$ld_actualizado=str_replace('.','',$ld_actualizado);
				$ld_actualizado=str_replace(',','.',$ld_actualizado);
				$ld_disponible=str_replace('.','',$ld_disponible);
				$ld_disponible=str_replace(',','.',$ld_disponible);
			}
			if (!empty($ls_programatica_next))
			{
				$ld_asignado=number_format($ld_asignado,2,",",".");
				$ld_actualizado=number_format($ld_actualizado,2,",",".");
				$ld_disponible=number_format($ld_disponible,2,",",".");

				$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,'denominacion'=>$ls_denominacion,'asignado'=>$ld_asignado,
								   'actualizado'=>$ld_actualizado,'disponibilidad'=>$ld_disponible);

				uf_print_cabecera($lo_libro,$lo_hoja,$ls_programatica_ant,$ls_denestpro_ant);
				uf_print_cabecera_detalle($lo_libro,$lo_hoja);
				$li_totaldata= count($la_data);
				//print "1".$li_inicio."    -----    ".$li_totaldata."<br />";
				uf_print_detalle($lo_libro,$lo_hoja,$la_data,$li_inicio,$li_totaldata); // Imprimimos el detalle
				$li_total=$li_total+$li_totaldata;
				$li_inicio=$li_total+1;
				
				
				$ld_totalasignado=number_format($ld_totalasignado,2,",",".");
				$ld_totalactualizado=number_format($ld_totalactualizado,2,",",".");
				$ld_totaldisponible=number_format($ld_totaldisponible,2,",",".");
				uf_print_pie_cabecera($lo_libro,$lo_hoja,$ld_totalasignado,$ld_totalactualizado,$ld_totaldisponible);

				$ld_totalasignado=0;
				$ld_totalactualizado=0;
				$ld_totaldisponible=0;
				if ((!empty($ls_programatica_next))&&($z<$li_tot))
				{
				}
				unset($la_data);
			}//if
	    }//for
		}//if
		else
		{
		  print("<script language=JavaScript>");
		  print(" alert('No hay nada que Reportar');");
		  print(" close();");
		  print("</script>");
		}
			if($lb_valido) // Si no ocurrio ningún error
			{
				unset($io_report);
				$lo_libro->close();
				header("Content-Type: application/x-msexcel; name=\"disponibilidad.xls\"");
				header("Content-Disposition: inline; filename=\"disponibilidad.xls\"");
				$fh=fopen($lo_archivo, "rb");
				fpassthru($fh);
				unlink($lo_archivo);
				print("<script language=JavaScript>");
				//print(" close();");
				print("</script>");
			}
			else // Si hubo algún error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				//print(" close();");
				print("</script>");		
			}
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_function_report);
?>