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
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";
	}


	function uf_print_cabecera_detalle($lo_libro,$lo_hoja,$io_encabezado,$ai_estilo,$as_nomper01,$as_nomper02,$as_nomper03,$ad_fecha,$li_fila)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing.Yozelin Barrag�n
	    // Fecha Creaci�n: 12/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $lo_titulo;

		switch($ai_estilo)
		{
		 case 1:
					$lo_hoja->write($li_fila, 1, 'CODIGO',$lo_titulo);
					$lo_hoja->write($li_fila, 2, 'DENOMINACION',$lo_titulo);
					$lo_hoja->write($li_fila, 3, 'ASIGNADO',$lo_titulo);
					$lo_hoja->write($li_fila, 4, $as_nomper01,$lo_titulo);
					$lo_hoja->write($li_fila, 5, 'TOTAL COMPROMETIDO',$lo_titulo);
					$lo_hoja->write($li_fila, 6, 'AJUSTE/COMP',$lo_titulo);
					$lo_hoja->write($li_fila, 7, 'MOD. PRES',$lo_titulo);
					$lo_hoja->write($li_fila, 8, 'PRECOMPROMISOS',$lo_titulo);
					$lo_hoja->write($li_fila, 9, 'LIBER./PRECOMPROMISO',$lo_titulo);
					$lo_hoja->write($li_fila, 10, 'DISPONIBLE AL: '.$ad_fecha,$lo_titulo);
					$li_fila++;
		        break;

		 case 2:
					$lo_hoja->write($li_fila, 1, 'CODIGO',$lo_titulo);
					$lo_hoja->write($li_fila, 2, 'DENOMINACION',$lo_titulo);
					$lo_hoja->write($li_fila, 3, 'ASIGNADO',$lo_titulo);
					$lo_hoja->write($li_fila, 4, $as_nomper01,$lo_titulo);
					$lo_hoja->write($li_fila, 5, $as_nomper02,$lo_titulo);
					$lo_hoja->write($li_fila, 6, 'TOTAL COMPROMETIDO',$lo_titulo);
					$lo_hoja->write($li_fila, 7, 'AJUSTE/COMP',$lo_titulo);
					$lo_hoja->write($li_fila, 8, 'MOD. PRES',$lo_titulo);
					$lo_hoja->write($li_fila, 9, 'PRECOMPROMISOS',$lo_titulo);
					$lo_hoja->write($li_fila, 10, 'LIBER./PRECOMPROMISO',$lo_titulo);
					$lo_hoja->write($li_fila, 11, 'DISPONIBLE AL: '.$ad_fecha,$lo_titulo);
					$li_fila++;

		       	break;

		 case 3:
		       		$lo_hoja->write($li_fila, 1, 'CODIGO',$lo_titulo);
					$lo_hoja->write($li_fila, 2, 'DENOMINACION',$lo_titulo);
					$lo_hoja->write($li_fila, 3, 'ASIGNADO',$lo_titulo);
					$lo_hoja->write($li_fila, 4, $as_nomper01,$lo_titulo);
					$lo_hoja->write($li_fila, 5, $as_nomper02,$lo_titulo);
					$lo_hoja->write($li_fila, 6, $as_nomper03,$lo_titulo);
					$lo_hoja->write($li_fila, 7, 'TOTAL COMPROMETIDO',$lo_titulo);
					$lo_hoja->write($li_fila, 8, 'AJUSTE/COMP',$lo_titulo);
					$lo_hoja->write($li_fila, 9, 'MOD. PRES',$lo_titulo);
					$lo_hoja->write($li_fila, 10, 'PRECOMPROMISOS',$lo_titulo);
					$lo_hoja->write($li_fila, 11, 'LIBER./PRECOMPROMISO',$lo_titulo);
					$lo_hoja->write($li_fila, 12, 'DISPONIBLE AL: '.$ad_fecha,$lo_titulo);
					$li_fila++;
		        break;
		}
		return $li_fila;
	}// end function uf_print_cabecera_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($li_tot,$lo_libro,$lo_hoja,$la_data,$ai_estilo,$as_nomper01,$as_nomper02,$as_nomper03,$li_fila)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle
	//		    Acess: private
	//	    Arguments: la_data // arreglo de informaci�n
	//	   			   io_pdf // Objeto PDF
	//    Description: funci�n que imprime el detalle
	//	   Creado Por: Ing.Yozelin Barrag�n
	// Fecha Creaci�n: 12/09/2006
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $lo_datacenter, $lo_dataleft, $lo_dataright;

		for ($index = 1; $index < ($li_tot+1); $index++)
		{
			if (trim($la_data[$index]["cuenta"])<>'')
			{
				switch($ai_estilo)
				{
					case 1:
						$lo_hoja->write($li_fila, 0, $la_data[$index]["total"],$lo_datacenter);
						$lo_hoja->write($li_fila, 1, trim($la_data[$index]["cuenta"]),$lo_datacenter);
						$lo_hoja->write($li_fila, 2, $la_data[$index]["denominacion"],$lo_dataleft);
						$lo_hoja->write($li_fila, 3, $la_data[$index]["asignado"],$lo_dataright);
						$lo_hoja->write($li_fila, 4, $la_data[$index]["periodo01"],$lo_dataright);
						$lo_hoja->write($li_fila, 5, $la_data[$index]["totcom"],$lo_dataright);
						$lo_hoja->write($li_fila, 6, $la_data[$index]["ajucom"],$lo_dataright);
						$lo_hoja->write($li_fila, 7, $la_data[$index]["modpres"],$lo_dataright);
						$lo_hoja->write($li_fila, 8, $la_data[$index]["precom"],$lo_dataright);
						$lo_hoja->write($li_fila, 9, $la_data[$index]["libprecom"],$lo_dataright);
						$lo_hoja->write($li_fila, 10, $la_data[$index]["disponible"],$lo_dataright);
						$li_fila++;

					break;

					case 2:
						$lo_hoja->write($li_fila, 0, $la_data[$index]["total"],$lo_datacenter);
						$lo_hoja->write($li_fila, 1, trim($la_data[$index]["cuenta"]),$lo_datacenter);
						$lo_hoja->write($li_fila, 2, $la_data[$index]["denominacion"],$lo_dataleft);
						$lo_hoja->write($li_fila, 3, $la_data[$index]["asignado"],$lo_dataright);
						$lo_hoja->write($li_fila, 4, $la_data[$index]["periodo01"],$lo_dataright);
						$lo_hoja->write($li_fila, 5, $la_data[$index]["periodo02"],$lo_dataright);
						$lo_hoja->write($li_fila, 6, $la_data[$index]["totcom"],$lo_dataright);
						$lo_hoja->write($li_fila, 7, $la_data[$index]["ajucom"],$lo_dataright);
						$lo_hoja->write($li_fila, 8, $la_data[$index]["modpres"],$lo_dataright);
						$lo_hoja->write($li_fila, 9, $la_data[$index]["precom"],$lo_dataright);
						$lo_hoja->write($li_fila, 10, $la_data[$index]["libprecom"],$lo_dataright);
						$lo_hoja->write($li_fila, 11, $la_data[$index]["disponible"],$lo_dataright);
						$li_fila++;

					break;

					case 3:
						$lo_hoja->write($li_fila, 0, $la_data[$index]["total"],$lo_datacenter);
						$lo_hoja->write($li_fila, 1, trim($la_data[$index]["cuenta"]),$lo_datacenter);
						$lo_hoja->write($li_fila, 2, $la_data[$index]["denominacion"],$lo_dataleft);
						$lo_hoja->write($li_fila, 3, $la_data[$index]["asignado"],$lo_dataright);
						$lo_hoja->write($li_fila, 4, $la_data[$index]["periodo01"],$lo_dataright);
						$lo_hoja->write($li_fila, 5, $la_data[$index]["periodo02"],$lo_dataright);
						$lo_hoja->write($li_fila, 6, $la_data[$index]["periodo03"],$lo_dataright);
						$lo_hoja->write($li_fila, 7, $la_data[$index]["totcom"],$lo_dataright);
						$lo_hoja->write($li_fila, 8, $la_data[$index]["ajucom"],$lo_dataright);
						$lo_hoja->write($li_fila, 9, $la_data[$index]["modpres"],$lo_dataright);
						$lo_hoja->write($li_fila, 10, $la_data[$index]["precom"],$lo_dataright);
						$lo_hoja->write($li_fila, 11, $la_data[$index]["libprecom"],$lo_dataright);
						$lo_hoja->write($li_fila, 12, $la_data[$index]["disponible"],$lo_dataright);
						$li_fila++;

					break;
				}
			}
		}
		return $li_fila;

	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total_detalle($lo_libro,$lo_hoja,$ai_estilo,$li_fila,$ld_total_asignado,$ld_total_per1,$ld_total_per2,$ld_total_per3,
									$ld_total_comprometido,$ld_total_ajuste,$ld_total_modificaciones,$ld_total_precompromiso,$ld_total_libPrecompromiso,
									$ld_total_disponible)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_total_detalle
	//		    Acess: private
	//	    Arguments: la_data // arreglo de informaci�n
	//	   			   io_pdf // Objeto PDF
	//    Description: funci�n que imprime el detalle
	//	   Creado Por: Ing.Yozelin Barrag�n
	// Fecha Creaci�n: 12/09/2006
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $lo_titulo, $lo_tituloR;

		switch($ai_estilo)
		{
			case 1:
				$lo_hoja->write($li_fila, 0, 'TOTAL BS',$lo_titulo);
				$lo_hoja->write($li_fila, 3, $ld_total_asignado,$lo_tituloR);
				$lo_hoja->write($li_fila, 4, $ld_total_per1,$lo_tituloR);
				$lo_hoja->write($li_fila, 5, $ld_total_comprometido,$lo_tituloR);
				$lo_hoja->write($li_fila, 6, $ld_total_ajuste,$lo_tituloR);
				$lo_hoja->write($li_fila, 7, $ld_total_modificaciones,$lo_tituloR);
				$lo_hoja->write($li_fila, 8, $ld_total_precompromiso,$lo_tituloR);
				$lo_hoja->write($li_fila, 9, $ld_total_precompromiso,$lo_tituloR);
				$lo_hoja->write($li_fila, 10, $ld_total_disponible,$lo_tituloR);
				$li_fila++;

			break;

			case 2:
				$lo_hoja->write($li_fila, 0, 'TOTAL BS',$lo_titulo);
				$lo_hoja->write($li_fila, 3, $ld_total_asignado,$lo_tituloR);
				$lo_hoja->write($li_fila, 4, $ld_total_per1,$lo_tituloR);
				$lo_hoja->write($li_fila, 5, $ld_total_per2,$lo_tituloR);
				$lo_hoja->write($li_fila, 6, $ld_total_comprometido,$lo_tituloR);
				$lo_hoja->write($li_fila, 7, $ld_total_ajuste,$lo_tituloR);
				$lo_hoja->write($li_fila, 8, $ld_total_modificaciones,$lo_tituloR);
				$lo_hoja->write($li_fila, 9, $ld_total_precompromiso,$lo_tituloR);
				$lo_hoja->write($li_fila, 10, $ld_total_precompromiso,$lo_tituloR);
				$lo_hoja->write($li_fila, 11, $ld_total_disponible,$lo_tituloR);
				$li_fila++;

			break;

			case 3:
				$lo_hoja->write($li_fila, 0, 'TOTAL BS',$lo_titulo);
				$lo_hoja->write($li_fila, 3, $ld_total_asignado,$lo_tituloR);
				$lo_hoja->write($li_fila, 4, $ld_total_per1,$lo_tituloR);
				$lo_hoja->write($li_fila, 5, $ld_total_per2,$lo_tituloR);
				$lo_hoja->write($li_fila, 6, $ld_total_per3,$lo_tituloR);
				$lo_hoja->write($li_fila, 7, $ld_total_comprometido,$lo_tituloR);
				$lo_hoja->write($li_fila, 8, $ld_total_ajuste,$lo_tituloR);
				$lo_hoja->write($li_fila, 9, $ld_total_modificaciones,$lo_tituloR);
				$lo_hoja->write($li_fila, 10, $ld_total_precompromiso,$lo_tituloR);
				$lo_hoja->write($li_fila, 11, $ld_total_precompromiso,$lo_tituloR);
				$lo_hoja->write($li_fila, 12, $ld_total_disponible,$lo_tituloR);
				$li_fila++;

			break;
		}
		$li_fila++;
		$li_fila++;
		return $li_fila;

	}// end function uf_print_total_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "ejecucion_trimestral_del_presupuesto.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();

//-----------------------------------------------------------------------------------------------------------------------------

	require_once("sigesp_spg_funciones_reportes.php");
	$io_function_report = new sigesp_spg_funciones_reportes();
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha = new class_fecha();
//-----------------------------------------------------------------------------------------------------------------------------

	require_once("sigesp_spg_reportes_class.php");
	$io_report = new sigesp_spg_reportes_class();
	$li_candeccon=$_SESSION["la_empresa"]["candeccon"];
	$li_tipconmon=$_SESSION["la_empresa"]["tipconmon"];
	$li_redconmon=$_SESSION["la_empresa"]["redconmon"];
//------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------  Par�metros para Filtar el Reporte  ------------------------------------
	$li_estmodest    = $_SESSION["la_empresa"]["estmodest"];
	$ldt_periodo     = $_SESSION["la_empresa"]["periodo"];
	$li_ano          = substr($ldt_periodo,0,4);
	$ls_codestpro1   = $_GET["codestpro1"];
	$ls_codestpro2   = $_GET["codestpro2"];
	$ls_codestpro3   = $_GET["codestpro3"];
	$ls_codestpro1h  = $_GET["codestpro1h"];
	$ls_codestpro2h  = $_GET["codestpro2h"];
	$ls_codestpro3h  = $_GET["codestpro3h"];
    $ls_estclades    = $_GET["estclades"];
	$ls_estclahas    = $_GET["estclahas"];
	$ld_tipper       = $_GET["tipper"];
    $ld_periodo      = $_GET["periodo"];
	$ls_cuentades    = $_GET["txtcuentades"];
	$ls_cuentahas    = $_GET["txtcuentahas"];


	switch($ld_tipper)
	{
	 case 1:
	       $ld_per01 = intval($ld_periodo);
		   $ld_per02 = "";
		   $ld_per03 = "";
		   $ls_desper = "MENSUAL";
	       $ld_fecfinrep=$io_fecha->uf_last_day($ld_periodo,$li_ano);
	       break;

	 case 2:
	      $ld_per01 = intval(substr($ld_periodo,0,2));
		  $ld_per02 = intval(substr($ld_periodo,2,2));
		  $ls_desper = "BIMENSUAL";
		  $ld_fecfinrep=$io_fecha->uf_last_day(substr($ld_periodo,2,2),$li_ano);
		  $ld_per03 = "";
	      break;

	 case 3:
	      $ld_per01 = intval(substr($ld_periodo,0,2));
		  $ld_per02 = intval(substr($ld_periodo,2,2));
		  $ld_per03 = intval(substr($ld_periodo,4,2));
		  $ls_desper = "TRIMESTRAL";
		  $ld_fecfinrep=$io_fecha->uf_last_day(substr($ld_periodo,4,2),$li_ano);
	      break;
	}
	if($li_estmodest==1)
	{
		$ls_codestpro4  =  "0000000000000000000000000";
		$ls_codestpro5  =  "0000000000000000000000000";
		$ls_codestpro4h =  "0000000000000000000000000";
		$ls_codestpro5h =  "0000000000000000000000000";
	}
	elseif($li_estmodest==2)
	{
		$ls_codestpro4  = $_GET["codestpro4"];
		$ls_codestpro5  = $_GET["codestpro5"];
		$ls_codestpro4h = $_GET["codestpro4h"];
		$ls_codestpro5h = $_GET["codestpro5h"];
    }

	 /////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_desc_event="Solicitud de Reporte Ejecucion Financiera del Presupuesto";
	 $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_ejecucion_financiera.php",$ls_desc_event);
	////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//----------------------------------------------------  Par�metros del encabezado  ----------------------------------------------
		$ls_titulo="EJECUCION FINANCIERA DEL PRESUPUESTO DE GASTO ".$ls_desper." AL ".$ld_fecfinrep." ";
//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )
	$ls_codestpro1  = $io_funciones->uf_cerosizquierda($ls_codestpro1,25);
	$ls_codestpro2  = $io_funciones->uf_cerosizquierda($ls_codestpro2,25);
	$ls_codestpro3  = $io_funciones->uf_cerosizquierda($ls_codestpro3,25);
	$ls_codestpro4  = $io_funciones->uf_cerosizquierda($ls_codestpro4,25);
	$ls_codestpro5  = $io_funciones->uf_cerosizquierda($ls_codestpro5,25);

	$ls_codestpro1h  = $io_funciones->uf_cerosizquierda($ls_codestpro1h,25);
	$ls_codestpro2h  = $io_funciones->uf_cerosizquierda($ls_codestpro2h,25);
	$ls_codestpro3h  = $io_funciones->uf_cerosizquierda($ls_codestpro3h,25);
	$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro4h,25);
	$ls_codestpro5h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h,25);

    $lb_valido=$io_report->uf_spg_reportes_ejecucion_financiera_presupuesto($ls_codestpro1,$ls_codestpro2,
                                                                            $ls_codestpro3,$ls_codestpro4,
                                                                            $ls_codestpro5,$ls_estclades,
																			$ls_codestpro1h,$ls_codestpro2h,
                                                                            $ls_codestpro3h,$ls_codestpro4h,
                                                                            $ls_codestpro5h,$ls_estclahas,
																			$ld_per01,$ld_per02,$ld_per03,
																			$ls_cuentades, $ls_cuentahas);

	 if($lb_valido==false) // Existe alg�n error � no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');");
		print(" close();");
		print("</script>");
	 }
	 else // Se Transfiere la data a otro arreglo para incluir subtotales por partida
	 {
	 	$io_report->dts_reporte->group_noorder("programatica");
		$li_tot=$io_report->dts_reporte->getRowCount("spg_cuenta");
		$ld_totalp_disponible		= 0;
		$ld_totalp_comprometido 	= 0;
		$ld_totalp_ajuste 			= 0;
		$ld_totalp_modificaciones 	= 0;
		$ld_totalp_precompromiso	= 0;
		$ld_totalp_libPrecompromiso	= 0;
		$ld_totalp_per1				= 0;
		$ld_totalp_per2				= 0;
		$ld_totalp_per3				= 0;

		$suma = 0;

	 	$i=1;

	 		for($z=1;$z<=$li_tot;$z++)
	 		{
	 			$li_tmp						= ($z+1);
				$ls_programatica			= $io_report->dts_reporte->data["programatica"][$z];
				$ls_spg_cuenta				= trim($io_report->dts_reporte->data["spg_cuenta"][$z]);
				$arrResultado=$io_function_report->uf_get_spg_cuenta($ls_spg_cuenta,$ls_partida,$ls_generica,$ls_especifica,$ls_subesp);
				$ls_partida=$arrResultado['as_spg_partida'];
				$ls_generica=$arrResultado['as_spg_generica'];
				$ls_especifica=$arrResultado['as_spg_especifica'];
				$ls_subesp=$arrResultado['as_spg_subesp'];
				$ls_status                 	= $io_report->dts_reporte->data["status"][$z];
				$ls_denominacion        	= trim($io_report->dts_reporte->data["denominacion"][$z]);
				$ld_dispact              	= $io_report->dts_reporte->data["dispact"][$z];
				$ld_asignado              	= $io_report->dts_reporte->data["asignado"][$z];
				$ld_disant              	= $io_report->dts_reporte->data["dispant"][$z];
				$ld_periodo01         		= $io_report->dts_reporte->data["periodo01"][$z];
				$ld_periodo02        		= $io_report->dts_reporte->data["periodo02"][$z];
				$ld_periodo03       		= $io_report->dts_reporte->data["periodo03"][$z];
				$ld_modpres              	= $io_report->dts_reporte->data["modpres"][$z];
				$ld_precomprometido     	= $io_report->dts_reporte->data["precomprometido"][$z];
				$ld_libprecomprometido  	= $io_report->dts_reporte->data["libprecomprometido"][$z];
				$ld_libcomprometido     	= $io_report->dts_reporte->data["libcomprometido"][$z];
				$ld_comprometido        	= $ld_periodo01 + $ld_periodo02 + $ld_periodo03;
				$ld_disponible          	= $ld_disant - $ld_comprometido + $ld_modpres - $ld_precomprometido;
				$ls_estcla					= $io_report->dts_reporte->data["estcla"][$z];

			    if ($z<$li_tot)
			    {
					$ls_programatica_next=$io_report->dts_reporte->data["programatica"][$li_tmp];
					$ls_partida_next=substr($io_report->dts_reporte->data["spg_cuenta"][$li_tmp],0,3);
			    }
			    elseif($z=$li_tot)
			    {
					$ls_programatica_next='no_next';
					$ls_partida_next='no_next';
			    }

			    if ($ls_partida!=$ls_partida_next)
			    {
			    	$lb_partida = true;
			    }
			    else
			    {
			    	$lb_partida = false;
			    }

				switch($ld_tipper)
		 		{
		  			case 1:
				         $la_data_temp[$i]=array('programatica'=>$ls_programatica,
											'asignado'=>$ld_asignado,
											'dispact'=>$ld_dispact,
											'dispant'=>$ld_disant,
											'precomprometido'=>$ld_precomprometido,
											'libprecomprometido'=>$ld_libprecomprometido,
											'libcomprometido'=>$ld_libcomprometido,
											'status'=>$ls_status,
											'estcla'=>$ls_estcla,
											'cuenta'=>$ls_spg_cuenta,
										    'denominacion'=>$ls_denominacion,
										    'disponact'=>$ld_dispact,
										    'periodo01'=>$ld_periodo01,
										    'periodo02'=>$ld_periodo02,
										    'periodo03'=>$ld_periodo03,
										    'totcom'=>$ld_comprometido,
										    'ajucom'=>$ld_libcomprometido,
										    'modpres'=>$ld_modpres,
										    'precom'=>$ld_precomprometido,
										    'libprecom'=>$ld_libprecomprometido,
										    'disponible'=>$ld_disponible,
										    'lprintsub'=>false,
										    'total'=>'');
		         	break;

		  			case 2:
		         		$la_data_temp[$i]=array('programatica'=>$ls_programatica,
											'asignado'=>$ld_asignado,
		         							'dispact'=>$ld_dispact,
											'dispant'=>$ld_disant,
											'precomprometido'=>$ld_precomprometido,
											'libprecomprometido'=>$ld_libprecomprometido,
											'libcomprometido'=>$ld_libcomprometido,
											'status'=>$ls_status,
											'estcla'=>$ls_estcla,
											'cuenta'=>$ls_spg_cuenta,
										    'denominacion'=>$ls_denominacion,
										    'disponact'=>$ld_dispact,
										    'periodo01'=>$ld_periodo01,
										    'periodo02'=>$ld_periodo02,
										    'periodo03'=>$ld_periodo03,
										    'totcom'=>$ld_comprometido,
										    'ajucom'=>$ld_libcomprometido,
										    'modpres'=>$ld_modpres,
										    'precom'=>$ld_precomprometido,
										    'libprecom'=>$ld_libprecomprometido,
										    'disponible'=>$ld_disponible,
										    'lprintsub'=>false,
										    'total'=>'');
		         	break;

		  			case 3:
		         		$la_data_temp[$i]=array('programatica'=>$ls_programatica,
											'asignado'=>$ld_asignado,
											'dispact'=>$ld_dispact,
											'dispant'=>$ld_disant,
											'precomprometido'=>$ld_precomprometido,
											'libprecomprometido'=>$ld_libprecomprometido,
											'libcomprometido'=>$ld_libcomprometido,
											'status'=>$ls_status,
											'estcla'=>$ls_estcla,
											'cuenta'=>$ls_spg_cuenta,
										    'denominacion'=>$ls_denominacion,
										    'disponact'=>$ld_dispact,
										    'periodo01'=>$ld_periodo01,
										    'periodo02'=>$ld_periodo02,
										    'periodo03'=>$ld_periodo03,
										    'totcom'=>$ld_comprometido,
										    'ajucom'=>$ld_libcomprometido,
										    'modpres'=>$ld_modpres,
										    'precom'=>$ld_precomprometido,
										    'libprecom'=>$ld_libprecomprometido,
										    'disponible'=>$ld_disponible,
										    'lprintsub'=>false,
										    'total'=>'');
		         	break;
				}// switch
				if($ls_status=="C")
				{
					$ld_totalp_asignado		= $ld_totalp_asignado + $ld_asignado;
					$ld_totalp_disponible		= $ld_totalp_disponible + $ld_disponible;
					$ld_totalp_comprometido 	= $ld_totalp_comprometido + $ld_comprometido;
					$ld_totalp_ajuste 			= $ld_totalp_ajuste + $ld_libcomprometido;
					$ld_totalp_modificaciones 	= $ld_totalp_modificaciones + $ld_modpres;
					$ld_totalp_precompromiso	= $ld_totalp_precompromiso + $ld_precomprometido;
					$ld_totalp_libPrecompromiso	= $ld_totalp_libPrecompromiso + $ld_libprecomprometido;
					$ld_totalp_per1				= $ld_totalp_per1 + $ld_periodo01;
					$ld_totalp_per2				= $ld_totalp_per2 + $ld_periodo02;
					$ld_totalp_per3				= $ld_totalp_per3 + $ld_periodo03;

				}



				if ($ls_partida!=$ls_partida_next)
				{

					$i++;
					switch($ld_tipper)
			 		{
			  			case 1:
					         $la_data_temp[$i]=array('programatica'=>$ls_programatica,
												'asignado'=>$ld_totalp_asignado,
												'dispact'=>$ld_dispact,
												'dispant'=>$ld_disant,
												'precomprometido'=>$ld_precomprometido,
												'libprecomprometido'=>$ld_libprecomprometido,
												'libcomprometido'=>$ld_libcomprometido,
												'status'=>$ls_status,
												'estcla'=>$ls_estcla,
												'cuenta'=>'.',
											    'denominacion'=>'',
											    'disponact'=>$ld_totalp_disponible,
											    'periodo01'=>$ld_totalp_per1,
											    'periodo02'=>$ld_totalp_per2,
											    'periodo03'=>$ld_totalp_per3,
											    'totcom'=>$ld_totalp_comprometido,
											    'ajucom'=>$ld_totalp_ajuste,
											    'modpres'=>$ld_totalp_modificaciones,
											    'precom'=>$ld_totalp_precompromiso,
											    'libprecom'=>$ld_totalp_libPrecompromiso,
											    'disponible'=>$ld_totalp_disponible,
											    'lprintsub'=>true,
												'total'=>'Total Partida');
			         	break;

			  			case 2:
			         		$la_data_temp[$i]=array('programatica'=>$ls_programatica,
												 'asignado'=>$ld_totalp_asignado,
												 'dispact'=>$ld_dispact,
												'dispant'=>$ld_disant,
												'precomprometido'=>$ld_precomprometido,
												'libprecomprometido'=>$ld_libprecomprometido,
												'libcomprometido'=>$ld_libcomprometido,
												'status'=>$ls_status,
												'estcla'=>$ls_estcla,
												'cuenta'=>'.',
											    'denominacion'=>'',
											    'disponact'=>$ld_totalp_disponible,
											    'periodo01'=>$ld_totalp_per1,
											    'periodo02'=>$ld_totalp_per2,
											    'periodo03'=>$ld_totalp_per3,
											    'totcom'=>$ld_totalp_comprometido,
											    'ajucom'=>$ld_totalp_ajuste,
											    'modpres'=>$ld_totalp_modificaciones,
											    'precom'=>$ld_totalp_precompromiso,
											    'libprecom'=>$ld_totalp_libPrecompromiso,
											    'disponible'=>$ld_totalp_disponible,
											    'lprintsub'=>true,
												'total'=>'Total Partida');
			         	break;

			  			case 3:
			         		$la_data_temp[$i]=array('programatica'=>$ls_programatica,
												'asignado'=>$ld_totalp_asignado,
												'dispact'=>0,
												'dispant'=>$ld_disant,
												'precomprometido'=>$ld_precomprometido,
												'libprecomprometido'=>$ld_libprecomprometido,
												'libcomprometido'=>$ld_libcomprometido,
												'status'=>$ls_status,
												'estcla'=>$ls_estcla,
												'cuenta'=>'.',
											    'denominacion'=>'',
											    'disponact'=>$ld_totalp_disponible,
											    'periodo01'=>$ld_totalp_per1,
											    'periodo02'=>$ld_totalp_per2,
											    'periodo03'=>$ld_totalp_per3,
											    'totcom'=>$ld_totalp_comprometido,
											    'ajucom'=>$ld_totalp_ajuste,
											    'modpres'=>$ld_totalp_modificaciones,
											    'precom'=>$ld_totalp_precompromiso,
											    'libprecom'=>$ld_totalp_libPrecompromiso,
											    'disponible'=>$ld_totalp_disponible,
											    'lprintsub'=>true,
												'total'=>'Total Partida');

			         	break;
					}// switch

					$ld_totalp_asignado		= 0;
					$ld_totalp_disponible		= 0;
					$ld_totalp_comprometido 	= 0;
					$ld_totalp_ajuste 			= 0;
					$ld_totalp_modificaciones 	= 0;
					$ld_totalp_precompromiso	= 0;
					$ld_totalp_libPrecompromiso	= 0;
					$ld_totalp_per1				= 0;
					$ld_totalp_per2				= 0;
					$ld_totalp_per3				= 0;
				}
				else
				{
				}
				$i++;
	 		}//for
			$i++;

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
		$lo_tituloR=  &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_tituloR->set_bold();
		$lo_tituloR->set_font("Verdana");
		$lo_tituloR->set_align('right');
		$lo_tituloR->set_size('9');
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
		$lo_hoja->set_column(2,2,50);
		$lo_hoja->set_column(3,14,30);
		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
		$li_fila=2;

		$io_report->dts_reporte->group_noorder("programatica");
		$li_tot=$io_report->dts_reporte->getRowCount("spg_cuenta");
		$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
		$ls_denestpro1="";
		$ls_denestpro2="";
		$ls_denestpro3="";
		$ls_denestpro4="";
		$ls_denestpro5="";
		$ls_partida="";
		$ls_partida_next="";

		$ld_total_disponible		= 0;
		$ld_total_asignado		= 0;
		$ld_total_comprometido 		= 0;
		$ld_total_ajuste 			= 0;
		$ld_total_modificaciones 	= 0;
		$ld_total_precompromiso		= 0;
		$ld_total_libPrecompromiso	= 0;
		$ld_total_per1				= 0;
		$ld_total_per2				= 0;
		$ld_total_per3				= 0;

		$ld_totalp_asignado		= 0;
		$ld_totalp_disponible		= 0;
		$ld_totalp_comprometido 	= 0;
		$ld_totalp_ajuste 			= 0;
		$ld_totalp_modificaciones 	= 0;
		$ld_totalp_precompromiso	= 0;
		$ld_totalp_libPrecompromiso	= 0;
		$ld_totalp_per1				= 0;
		$ld_totalp_per2				= 0;
		$ld_totalp_per3				= 0;
		$ls_partida_aux="";

		for($z=1;$z<=($i-2);$z++)
		{
		    $li_tmp						= ($z+1);
			$ls_programatica			= $la_data_temp[$z]["programatica"];
			$ls_spg_cuenta				= trim($la_data_temp[$z]["cuenta"]);
			$ls_status                 	= $la_data_temp[$z]["status"];
			$lb_lprintsub				= $la_data_temp[$z]["lprintsub"];

			$arrResultado=$io_function_report->uf_get_spg_cuenta($ls_spg_cuenta,$ls_partida,$ls_generica,$ls_especifica,$ls_subesp);
			$ls_partida=$arrResultado['as_spg_partida'];
			$ls_generica=$arrResultado['as_spg_generica'];
			$ls_especifica=$arrResultado['as_spg_especifica'];
			$ls_subesp=$arrResultado['as_spg_subesp'];
		    if ($z<($i-2))
		    {
				$ls_programatica_next=$la_data_temp[$li_tmp]["programatica"];
				$ls_partida_next=substr($la_data_temp[$li_tmp]["cuenta"],0,3);
		    }
		    elseif($z=($i-2))
		    {
				$ls_programatica_next='no_next';
				$ls_partida_next='no_next';
		    }

			if(!empty($ls_programatica))
			{
				$ls_estcla=$la_data_temp[$z]["estcla"];
				$ls_codestpro1=substr($ls_programatica,0,25);
				$ls_denestpro1="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);
				$ls_denestpro1=$arrResultado['as_denestpro1'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
				  $ls_denestpro1=trim($ls_denestpro1);
				}
				$ls_codestpro2=substr($ls_programatica,25,25);
				if($lb_valido)
				{
				  $ls_denestpro2="";
				  $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
				  $ls_denestpro2=$arrResultado['as_denestpro2'];
				  $lb_valido=$arrResultado['lb_valido'];
				  $ls_denestpro2=trim($ls_denestpro2);
				}
				$ls_codestpro3=substr($ls_programatica,50,25);
				if($lb_valido)
				{
				  $ls_denestpro3="";
				  $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
				  $ls_denestpro3=$arrResultado['as_denestpro3'];
				  $lb_valido=$arrResultado['lb_valido'];
				  $ls_denestpro3=trim($ls_denestpro3);
				}
				if($li_estmodest==2)
				{
					$ls_codestpro4=substr($ls_programatica,75,25);
					if($lb_valido)
					{
					  $ls_denestpro4="";
					  $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estcla);
					  $ls_denestpro4=$arrResultado['as_denestpro4'];
					  $lb_valido=$arrResultado['lb_valido'];
					  $ls_denestpro4=trim($ls_denestpro4);
					}
					$ls_codestpro5=substr($ls_programatica,100,25);
					if($lb_valido)
					{
					  $ls_denestpro5="";
					  $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
					  $ls_denestpro5=$arrResultado['as_denestpro5'];
					  $lb_valido=$arrResultado['lb_valido'];
					  $ls_denestpro5=trim($ls_denestpro5);
					}
					$ls_denestpro_ant=trim($ls_denestpro1)." , ".trim($ls_denestpro2)." , ".trim($ls_denestpro3)." , ".trim($ls_denestpro4)." , ".trim($ls_denestpro5);
					$ls_programatica_ant=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
				}
				else
				{
					$ls_denestpro_ant=$ls_denestpro1." , ".$ls_denestpro2." , ".$ls_denestpro3;
					$ls_programatica_ant=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3);
				}
			}

			$ls_subtotal        	=trim($la_data_temp[$z]["total"]);
			$ls_denominacion        	=trim($la_data_temp[$z]["denominacion"]);
			$ld_asignado              	=$la_data_temp[$z]["asignado"];
			$ld_dispact              	=$la_data_temp[$z]["dispact"];
			$ld_disant              	=$la_data_temp[$z]["dispant"];
			$ld_periodo01         		=$la_data_temp[$z]["periodo01"];
			$ld_periodo02        		=$la_data_temp[$z]["periodo02"];
			$ld_periodo03       		=$la_data_temp[$z]["periodo03"];
			$ld_modpres              	=$la_data_temp[$z]["modpres"];
			$ld_precomprometido     	=$la_data_temp[$z]["precomprometido"];
			$ld_libprecomprometido  	=$la_data_temp[$z]["libprecomprometido"];
			$ld_libcomprometido     	=$la_data_temp[$z]["libcomprometido"];
			$ld_disponact     			=$la_data_temp[$z]["disponact"];
			$ld_comprometido        	=$ld_periodo01 + $ld_periodo02 + $ld_periodo03;
			$ld_disponible          	=$ld_disant - $ld_comprometido + $ld_modpres - $ld_precomprometido;
			if(($ls_status == "C")&&(!$lb_lprintsub))
			{

				$ld_comprometido        	=$ld_periodo01 + $ld_periodo02 + $ld_periodo03;
				$ld_disponible          	=$ld_disant - $ld_comprometido + $ld_modpres - $ld_precomprometido;

				$ld_total_asignado		= $ld_total_asignado + $ld_asignado;
				$ld_total_disponible		= $ld_total_disponible + $ld_disponible;
				$ld_total_comprometido 		= $ld_total_comprometido + $ld_comprometido;
				$ld_total_ajuste 			= $ld_total_ajuste + $ld_libcomprometido;
				$ld_total_modificaciones 	= $ld_total_modificaciones + $ld_modpres;
				$ld_total_precompromiso		= $ld_total_precompromiso + $ld_precomprometido;
				$ld_total_libPrecompromiso	= $ld_total_libPrecompromiso + $ld_libprecomprometido;
				$ld_total_per1				= $ld_total_per1 + $ld_periodo01;
				$ld_total_per2				= $ld_total_per2 + $ld_periodo02;
				$ld_total_per3				= $ld_total_per3 + $ld_periodo03;

			}
			$ld_asignado             =number_format($ld_asignado,2,",",".");
			$ld_disant             =number_format($ld_disant,2,",",".");
			$ld_dispact            =number_format($ld_dispact,2,",",".");
			$ld_periodo01          =number_format($ld_periodo01,2,",",".");
			$ld_periodo02          =number_format($ld_periodo02,2,",",".");
			$ld_periodo03          =number_format($ld_periodo03,2,",",".");
			$ld_modpres            =number_format($ld_modpres,2,",",".");
			$ld_comprometido       =number_format($ld_comprometido,2,",",".");
			$ld_precomprometido    =number_format($ld_precomprometido,2,",",".");
			$ld_disponible         =number_format($ld_disponible,2,",",".");
			$ld_libprecomprometido =number_format($ld_libprecomprometido,2,",",".");
			$ld_libcomprometido    =number_format($ld_libcomprometido,2,",",".");
			$ld_disponact		   =number_format($ld_disponact,2,",",".");

			if (!empty($ls_programatica))
		    {
				switch($ld_tipper)
		 		{
		  			case 1:
		  				if ($lb_lprintsub)
		  				{
				         $la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
										    'denominacion'=>$ls_denominacion,
										    'asignado'=>$ld_asignado,
										    'periodo01'=>$ld_periodo01,
										    'totcom'=>$ld_comprometido,
										    'ajucom'=>$ld_libcomprometido,
										    'modpres'=>$ld_modpres,
										    'precom'=>$ld_precomprometido,
										    'libprecom'=>$ld_libprecomprometido,
										    'disponible'=>$ld_disponact,
										    'total'=>$ls_subtotal );

		  				}
		  				else
		  				{
				         $la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
										    'denominacion'=>$ls_denominacion,
										    'asignado'=>$ld_asignado,
										    'periodo01'=>$ld_periodo01,
										    'totcom'=>$ld_comprometido,
										    'ajucom'=>$ld_libcomprometido,
										    'modpres'=>$ld_modpres,
										    'precom'=>$ld_precomprometido,
										    'libprecom'=>$ld_libprecomprometido,
										    'disponible'=>$ld_disponible,
										    'total'=>$ls_subtotal );

		  				}

		         	break;

		  			case 2:
		  				if ($lb_lprintsub)
		  				{
		         		$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
										    'denominacion'=>$ls_denominacion,
										    'asignado'=>$ld_asignado,
										    'periodo01'=>$ld_periodo01,
										    'periodo02'=>$ld_periodo02,
										    'totcom'=>$ld_comprometido,
										    'ajucom'=>$ld_libcomprometido,
										    'modpres'=>$ld_modpres,
										    'precom'=>$ld_precomprometido,
										    'libprecom'=>$ld_libprecomprometido,
										    'disponible'=>$ld_disponact,
										    'total'=>$ls_subtotal );
		  				}
		  				else
		  				{
		         		$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
										    'denominacion'=>$ls_denominacion,
										    'asignado'=>$ld_asignado,
										    'periodo01'=>$ld_periodo01,
										    'periodo02'=>$ld_periodo02,
										    'totcom'=>$ld_comprometido,
										    'ajucom'=>$ld_libcomprometido,
										    'modpres'=>$ld_modpres,
										    'precom'=>$ld_precomprometido,
										    'libprecom'=>$ld_libprecomprometido,
										    'disponible'=>$ld_disponible,
										    'total'=>$ls_subtotal );
		  				}

		         	break;

		  			case 3:
		  				if ($lb_lprintsub)
		  				{
		         		$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
										    'denominacion'=>$ls_denominacion,
										    'asignado'=>$ld_asignado,
										    'periodo01'=>$ld_periodo01,
										    'periodo02'=>$ld_periodo02,
										    'periodo03'=>$ld_periodo03,
										    'totcom'=>$ld_comprometido,
										    'ajucom'=>$ld_libcomprometido,
										    'modpres'=>$ld_modpres,
										    'precom'=>$ld_precomprometido,
										    'libprecom'=>$ld_libprecomprometido,
										    'disponible'=>$ld_disponact,
										    'total'=>$ls_subtotal );
		  				}
		  				else
		  				{
		         		$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
										    'denominacion'=>$ls_denominacion,
										    'asignado'=>$ld_asignado,
										    'periodo01'=>$ld_periodo01,
										    'periodo02'=>$ld_periodo02,
										    'periodo03'=>$ld_periodo03,
										    'totcom'=>$ld_comprometido,
										    'ajucom'=>$ld_libcomprometido,
										    'modpres'=>$ld_modpres,
										    'precom'=>$ld_precomprometido,
										    'libprecom'=>$ld_libprecomprometido,
										    'disponible'=>$ld_disponible,
										    'total'=>$ls_subtotal );
		  				}

		         	break;
				}// switch
			}
			else
			{
				switch($ld_tipper)
				{
					case 1:
						if ($lb_lprintsub)
						{
							$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
												'denominacion'=>$ls_denominacion,
												'asignado'=>$ld_asignado,
												'periodo01'=>$ld_periodo01,
												'totcom'=>$ld_comprometido,
												'ajucom'=>$ld_libcomprometido,
												'modpres'=>$ld_modpres,
												'precom'=>$ld_precomprometido,
												'libprecom'=>$ld_libprecomprometido,
												'disponible'=>$ld_disponact,
												'total'=>$ls_subtotal );
						}
						else
						{
							$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
												'denominacion'=>$ls_denominacion,
												'asignado'=>$ld_asignado,
												'periodo01'=>$ld_periodo01,
												'totcom'=>$ld_comprometido,
												'ajucom'=>$ld_libcomprometido,
												'modpres'=>$ld_modpres,
												'precom'=>$ld_precomprometido,
												'libprecom'=>$ld_libprecomprometido,
												'disponible'=>$ld_disponible,
												'total'=>$ls_subtotal );
						}

					break;

				  	case 2:
				  		if ($lb_lprintsub)
				  		{
							$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
											'denominacion'=>$ls_denominacion,
											'asignado'=>$ld_asignado,
											'periodo01'=>$ld_periodo01,
											'periodo02'=>$ld_periodo02,
											'totcom'=>$ld_comprometido,
											'ajucom'=>$ld_libcomprometido,
											'modpres'=>$ld_modpres,
											'precom'=>$ld_precomprometido,
											'libprecom'=>$ld_libprecomprometido,
											'disponible'=>$ld_disponact,
										    'total'=>$ls_subtotal );
				  		}
				  		else
				  		{
							$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
											'denominacion'=>$ls_denominacion,
											'asignado'=>$ld_asignado,
											'periodo01'=>$ld_periodo01,
											'periodo02'=>$ld_periodo02,
											'totcom'=>$ld_comprometido,
											'ajucom'=>$ld_libcomprometido,
											'modpres'=>$ld_modpres,
											'precom'=>$ld_precomprometido,
											'libprecom'=>$ld_libprecomprometido,
											'disponible'=>$ld_disponible,
										    'total'=>$ls_subtotal );
				  		}

					break;

				  	case 3:
				  		if ($lb_lprintsub)
				  		{
						$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
											'denominacion'=>$ls_denominacion,
											'asignado'=>$ld_asignado,
											'periodo01'=>$ld_periodo01,
											'periodo02'=>$ld_periodo02,
											'periodo03'=>$ld_periodo03,
											'totcom'=>$ld_comprometido,
											'ajucom'=>$ld_libcomprometido,
											'modpres'=>$ld_modpres,
											'precom'=>$ld_precomprometido,
											'libprecom'=>$ld_libprecomprometido,
											'disponible'=>$ld_disponact,
										    'total'=>$ls_subtotal );
				  		}
				  		else
				  		{
						$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
											'denominacion'=>$ls_denominacion,
											'asignado'=>$ld_asignado,
											'periodo01'=>$ld_periodo01,
											'periodo02'=>$ld_periodo02,
											'periodo03'=>$ld_periodo03,
											'totcom'=>$ld_comprometido,
											'ajucom'=>$ld_libcomprometido,
											'modpres'=>$ld_modpres,
											'precom'=>$ld_precomprometido,
											'libprecom'=>$ld_libprecomprometido,
											'disponible'=>$ld_disponible,
										    'total'=>$ls_subtotal );
				  		}

					break;
				}	// switch
		     }	//if
			if (!empty($ls_programatica_next))
			{
				switch($ld_tipper)
				{
					case 1:
						if ($lb_lprintsub)
						{
					    	$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
											    'denominacion'=>$ls_denominacion,
											    'asignado'=>$ld_asignado,
											    'periodo01'=>$ld_periodo01,
											    'totcom'=>$ld_comprometido,
											    'ajucom'=>$ld_libcomprometido,
											    'modpres'=>$ld_modpres,
											    'precom'=>$ld_precomprometido,
											    'libprecom'=>$ld_libprecomprometido,
											    'disponible'=>$ld_disponible,
												'total'=>$ls_subtotal );
						}
						else
						{
					    	$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
											    'denominacion'=>$ls_denominacion,
											    'asignado'=>$ld_asignado,
											    'periodo01'=>$ld_periodo01,
											    'totcom'=>$ld_comprometido,
											    'ajucom'=>$ld_libcomprometido,
											    'modpres'=>$ld_modpres,
											    'precom'=>$ld_precomprometido,
											    'libprecom'=>$ld_libprecomprometido,
											    'disponible'=>$ld_disponible,
												'total'=>$ls_subtotal );
						}

				    break;

				  	case 2:
				  		if ($lb_lprintsub)
				  		{
					    	$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
											    'denominacion'=>$ls_denominacion,
											    'asignado'=>$ld_asignado,
											    'periodo01'=>$ld_periodo01,
											    'periodo02'=>$ld_periodo02,
											    'totcom'=>$ld_comprometido,
											    'ajucom'=>$ld_libcomprometido,
											    'modpres'=>$ld_modpres,
											    'precom'=>$ld_precomprometido,
											    'libprecom'=>$ld_libprecomprometido,
											    'disponible'=>$ld_disponible,
												'total'=>$ls_subtotal );
				  		}
				  		else
				  		{
					    	$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
											    'denominacion'=>$ls_denominacion,
											    'asignado'=>$ld_asignado,
											    'periodo01'=>$ld_periodo01,
											    'periodo02'=>$ld_periodo02,
											    'totcom'=>$ld_comprometido,
											    'ajucom'=>$ld_libcomprometido,
											    'modpres'=>$ld_modpres,
											    'precom'=>$ld_precomprometido,
											    'libprecom'=>$ld_libprecomprometido,
											    'disponible'=>$ld_disponible,
												'total'=>$ls_subtotal );
				  		}

				  	break;

				  	case 3:
				  		if ($lb_lprintsub)
						{
					    	$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
											    'denominacion'=>$ls_denominacion,
											    'asignado'=>$ld_asignado,
											    'periodo01'=>$ld_periodo01,
											    'periodo02'=>$ld_periodo02,
											    'periodo03'=>$ld_periodo03,
											    'totcom'=>$ld_comprometido,
											    'ajucom'=>$ld_libcomprometido,
											    'modpres'=>$ld_modpres,
											    'precom'=>$ld_precomprometido,
											    'libprecom'=>$ld_libprecomprometido,
											    'disponible'=>$ld_disponact,
												'total'=>$ls_subtotal );
						}
						else
						{
					    	$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
											    'denominacion'=>$ls_denominacion,
											    'asignado'=>$ld_asignado,
											    'periodo01'=>$ld_periodo01,
											    'periodo02'=>$ld_periodo02,
											    'periodo03'=>$ld_periodo03,
											    'totcom'=>$ld_comprometido,
											    'ajucom'=>$ld_libcomprometido,
											    'modpres'=>$ld_modpres,
											    'precom'=>$ld_precomprometido,
											    'libprecom'=>$ld_libprecomprometido,
											    'disponible'=>$ld_disponible,
												'total'=>$ls_subtotal );
						}

				     break;
				}	// switch
				//-------------------------------
				//---> Impresion de cabecera
				//-------------------------------
						$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
					    $ls_incio1=25-$ls_loncodestpro1;
					    $ls_codestpro1=substr($ls_codestpro1,$ls_incio1,$ls_loncodestpro1);

						$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
					    $ls_incio2=25-$ls_loncodestpro2;
					    $ls_codestpro2=substr($ls_codestpro2,$ls_incio2,$ls_loncodestpro2);

						$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
					    $ls_incio3=25-$ls_loncodestpro3;
					    $ls_codestpro3=substr($ls_codestpro3,$ls_incio3,$ls_loncodestpro3);

						if($ls_codestpro2!="")
						{
							$ls_tituto_2=" ACCION ESPECIFICA: ";
						}
						else
						{
							$ls_tituto_2="";
						}
						if($ls_codestpro3!="")
						{
							$ls_tituto_3=" UNIDAD: ";
						}
						else
						{
							$ls_tituto_3="";
						}

						$lo_hoja->write($li_fila, 0, 'PROYECTO/ACCION CENTRALIZADA: '.$ls_codestpro1.'  '.$ls_denestpro1,$lo_encabezado);
						$li_fila++;
						$lo_hoja->write($li_fila, 0, $ls_tituto_2.' '.$ls_codestpro2.'  '.$ls_denestpro2,$lo_encabezado);
						$li_fila++;
						$lo_hoja->write($li_fila, 0, $ls_tituto_3.' '.$ls_codestpro3.'  '.$ls_denestpro3,$lo_encabezado);
						$li_fila++;

				//-------------------------------
				//---> Fin Impresion de cabecera
				//-------------------------------
				$as_nomper01="";
				$as_nomper02="";
				$as_nomper03="";
				$as_nomper01=$io_function_report->uf_get_nom_mes($ld_per01,$as_nomper01);
				$as_nomper02=$io_function_report->uf_get_nom_mes($ld_per02,$as_nomper02);
				$as_nomper03=$io_function_report->uf_get_nom_mes($ld_per03,$as_nomper03);

				$li_fila=uf_print_cabecera_detalle($lo_libro,$lo_hoja,$io_encabezado,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,$ld_fecfinrep,$li_fila);
				
				$li_fila=uf_print_detalle($li_tot,$lo_libro,$lo_hoja,$la_data,$ld_tipper,$as_nomper01,$as_nomper02,$as_nomper03,$li_fila); // Imprimimos el detalle
				$ld_total_asignado             		=number_format($ld_total_asignado,2,",",".");
				$ld_total_disponible             		=number_format($ld_total_disponible,2,",",".");
				$ld_total_comprometido             		=number_format($ld_total_comprometido,2,",",".");
				$ld_total_ajuste             			=number_format($ld_total_ajuste,2,",",".");
				$ld_total_modificaciones             	=number_format($ld_total_modificaciones,2,",",".");
				$ld_total_precompromiso             	=number_format($ld_total_precompromiso,2,",",".");
				$ld_total_libPrecompromiso             	=number_format($ld_total_libPrecompromiso,2,",",".");
				$ld_total_per1             				=number_format($ld_total_per1,2,",",".");
				$ld_total_per2             				=number_format($ld_total_per2,2,",",".");
				$ld_total_per3             				=number_format($ld_total_per3,2,",",".");
				$li_fila=uf_print_total_detalle($lo_libro,$lo_hoja,$ld_tipper,$li_fila,$ld_total_asignado,$ld_total_per1,$ld_total_per2,
				$ld_total_per3,$ld_total_comprometido,$ld_total_ajuste,$ld_total_modificaciones,$ld_total_precompromiso,$ld_total_libPrecompromiso,$ld_total_disponible); // Imprimimos el detalle

				//Reinicializa valores
				$ld_total_disponible		= 0;
				$ld_total_comprometido 		= 0;
				$ld_total_ajuste 			= 0;
				$ld_total_modificaciones 	= 0;
				$ld_total_precompromiso		= 0;
				$ld_total_libPrecompromiso	= 0;
				$ld_total_per1				= 0;
				$ld_total_per2				= 0;
				$ld_total_per3				= 0;

				if ((!empty($ls_programatica_next))&&($z<$li_tot))
				{
				}
				$ld_total_general_cuenta=0;
				unset($la_data);
				unset($la_data_tot);
			}//if
		}//for
		if (isset($d) && $d)
		{
		}
		elseif ($li_tot>0)
		{
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"ejecucion_trimestral_del_presupuesto.xls\"");
			header("Content-Disposition: inline; filename=\"ejecucion_trimestral_del_presupuesto.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
			print("<script language=JavaScript>");
			print(" close();");
			print("</script>");

		}
		else
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');");
			print(" close();");
			print("</script>");
	    }

	}
	unset($io_report);
	unset($io_funciones);
	unset($io_function_report);
	unset($io_fecha);
?>