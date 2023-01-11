<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
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
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/08/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_listadopersonalgenerico.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulos($lo_titulo,$lo_hoja)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_titulos
		//		   Access: private 
		//	    Arguments: lo_hoja // hoja en excel
		//    Description: función que los títulos del reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_total=$_SESSION["li_total"];
		for($li_i=0;($li_i<=$li_total);$li_i++)
		{
			$lo_hoja->set_column($li_i,$li_i,$_SESSION["la_titulos"][$li_i]["ancho"]);
		}
		for($li_i=0;($li_i<=$li_total);$li_i++)
		{
			$lo_hoja->write(3, $li_i, $_SESSION["la_titulos"][$li_i]["titulo"],$lo_titulo);
		}
		return $lo_hoja;
	}// end function uf_print_titulos
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($lo_titulo,$lo_hoja)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: lo_hoja // hoja en excel
		//    Description: función que los títulos del reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_total=$_SESSION["li_total"];
		for($li_i=0;($li_i<=$li_total);$li_i++)
		{
			$lo_hoja->set_column($li_i,$li_i,$_SESSION["la_titulos"][$li_i]["ancho"]);
		}
		for($li_i=0;($li_i<=$li_total);$li_i++)
		{
			$lo_hoja->write(3, $li_i, $_SESSION["la_titulos"][$li_i]["titulo"],$lo_titulo);
		}
		return $lo_hoja;
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_campo($id,$as_campo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_validar_campo
		//		   Access: private 
		//	    Arguments: id // Identificador del campo que se desea validar
		//				   as_campo // Contenido del campo que se desea validar
		//    Description: función que valida los campos 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina, $io_funciones,$io_report;
		
		$ls_tipo=$_SESSION["la_titulos"][$id]["tipo"];
		switch($ls_tipo)
		{
			case "string":
				$as_campo=rtrim($as_campo);
				break;
			case "date":
				$as_campo=$io_funciones->uf_convertirfecmostrar($as_campo);
				break;		
			case "integer":
				$as_campo=number_format($as_campo,0,",",".");
				break;
			case "double":
				$as_campo=number_format($as_campo,2,",",".");
				break;
		}
		$ls_campo=$_SESSION["la_titulos"][$id]["campo"];
		switch($ls_campo)
		{
			case "sno_personal.nacper":
				switch($as_campo)
				{
					case "V":
						$as_campo="Venezolano";
						break;
					case "E":
						$as_campo="Estranjero";
						break;
				}
				break;
			case "sno_personal.sexper":
				switch($as_campo)
				{
					case "M":
						$as_campo="Masculino";
						break;
					case "F":
						$as_campo="Femenino";
						break;
				}
				break;
			case "sno_personal.edocivper":
				switch($as_campo)
				{
					case "S":
						$as_campo="Soltero";
						break;
					case "C":
						$as_campo="Casado";
						break;
					case "D":
						$as_campo="Divorciado";
						break;
					case "V":
						$as_campo="Viudo";
						break;
					case "K":
						$as_campo="En Union Estable";
						break;
				}
				break;
			case "sno_personal.tipvivper":
				switch($as_campo)
				{
					case "0":
						$as_campo="Propia";
						break;
					case "1":
						$as_campo="Alquilada";
						break;
					case "2":
						$as_campo="No tiene";
						break;
					case "3":
						$as_campo="De un familiar";
						break;
				}
				break;
			case "sno_personal.nivacaper":
				switch($as_campo)
				{
					case "0":
						$as_campo="Ninguno";
						break;
					case "1":
						$as_campo="Primaria";
						break;
					case "2":
						$as_campo="Bachiller";
						break;
					case "3":
						$as_campo="Técnico Superior";
						break;
					case "4":
						$as_campo="Universitario";
						break;
					case "5":
						$as_campo="Maestria";
						break;
					case "6":
						$as_campo="PostGrado";
						break;
					case "7":
						$as_campo="Doctorado";
						break;
				}
				break;
			case "sno_personal.estper":
				switch($as_campo)
				{
					case "0":
						$as_campo="Pre-Ingreso";
						break;
					case "1":
						$as_campo="Activo";
						break;
					case "2":
						$as_campo="N/A";
						break;
					case "3":
						$as_campo="Egresado";
						break;
				}
				break;
			case "sno_personalnomina.staper":
				switch($as_campo)
				{
					case "0":
						$as_campo="N/A";
						break;
					case "1":
						$as_campo="Activo";
						break;
					case "2":
						$as_campo="Vacaciones";
						break;
					case "3":
						$as_campo="Egresado";
						break;
					case "4":
						$as_campo="Suspendido";
						break;
				}
				break;
			case "sno_personal.cauegrper":
				switch($as_campo)
				{
					case "N": // Ninguna
						$as_campo="Ninguna";
						break;
					case "D": // Despido
						$as_campo="Despido";
						break;
					case "R": // Renuncia
						$as_campo="Renuncia";
						break;
					case "J": // Jubilado
						$as_campo="Jubilado";
						break;
					case "P": // Pensionado
						$as_campo="Pensionado";
						break;
					case "T": // Traslado
						$as_campo="Traslado";
						break;
					case "F": // Fallecido
						$as_campo="Fallecido";
						break;
				}
				break;
				case "sno_personalnomina.tipcuebanper":
				switch($as_campo)
				{
					case "A": // Ahorro
						$as_campo="Ahorro";
						break;
					case "C": // corriente
						$as_campo="Corriente";
						break;
					case "F": // fondo de activos liquidos
						$as_campo="Fondo de Activos Líquidos";
						break;
				}
				break;
				case "sno_personal.codorg":
					$as_campo=$io_report->uf_buscar_ubicacion_fisica($as_campo);
				break;
		}
		return $as_campo;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "listado_personal_generico.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo=$_SESSION["ls_titulo"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Listado de Personal en Excel</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_listadogenerico(); // Obtenemos el detalle del reporte
	}
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
		$lo_titulo->set_text_wrap();
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
		$lo_hoja->write(0,1,$ls_titulo,$lo_encabezado);
		$lo_hoja=uf_print_titulos($lo_titulo,$lo_hoja);			
		$li_row=3;
		while(!$io_report->rs_data->EOF)
		{
			$ls_campo0=$io_report->rs_data->fields["campo0"];
			$ls_campo1=$io_report->rs_data->fields["campo1"];
			$ls_campo2=$io_report->rs_data->fields["campo2"];
			$ls_campo3=$io_report->rs_data->fields["campo3"];
			$ls_campo4=$io_report->rs_data->fields["campo4"];
			$ls_campo5=$io_report->rs_data->fields["campo5"];
			$ls_campo6=$io_report->rs_data->fields["campo6"];
			$ls_campo7=$io_report->rs_data->fields["campo7"];
			$ls_campo8=$io_report->rs_data->fields["campo8"];
			$ls_campo9=$io_report->rs_data->fields["campo9"];
			$ls_campo10=$io_report->rs_data->fields["campo10"];
			$ls_campo11=$io_report->rs_data->fields["campo11"];
			$ls_campo12=$io_report->rs_data->fields["campo12"];
			$ls_campo13=$io_report->rs_data->fields["campo13"];
			$ls_campo14=$io_report->rs_data->fields["campo14"];
			$ls_campo15=$io_report->rs_data->fields["campo15"];
			$ls_campo16=$io_report->rs_data->fields["campo16"];
			$ls_campo17=$io_report->rs_data->fields["campo17"];
			$ls_campo18=$io_report->rs_data->fields["campo18"];
			$ls_campo19=$io_report->rs_data->fields["campo19"];
			$ls_campo20=$io_report->rs_data->fields["campo20"];
			$ls_campo21=$io_report->rs_data->fields["campo21"];
			$ls_campo22=$io_report->rs_data->fields["campo22"];
			$ls_campo23=$io_report->rs_data->fields["campo23"];
			$ls_campo24=$io_report->rs_data->fields["campo24"];
			$ls_campo25=$io_report->rs_data->fields["campo25"];
			$ls_campo26=$io_report->rs_data->fields["campo26"];
			$ls_campo27=$io_report->rs_data->fields["campo27"];
			$ls_campo28=$io_report->rs_data->fields["campo28"];
			$ls_campo29=$io_report->rs_data->fields["campo29"];
			$ls_campo30=$io_report->rs_data->fields["campo30"];
			$ls_campo31=$io_report->rs_data->fields["campo31"];
			$ls_campo32=$io_report->rs_data->fields["campo32"];
			$ls_campo33=$io_report->rs_data->fields["campo33"];
			$ls_campo34=$io_report->rs_data->fields["campo34"];
			$ls_campo35=$io_report->rs_data->fields["campo35"];
			$ls_campo36=$io_report->rs_data->fields["campo36"];
			$ls_campo37=$io_report->rs_data->fields["campo37"];
			$ls_campo38=$io_report->rs_data->fields["campo38"];
			$ls_campo39=$io_report->rs_data->fields["campo39"];
			$ls_campo40=$io_report->rs_data->fields["campo40"];
			$ls_campo41=$io_report->rs_data->fields["campo41"];
			$ls_campo42=$io_report->rs_data->fields["campo42"];
			$ls_campo43=$io_report->rs_data->fields["campo43"];
			$ls_campo44=$io_report->rs_data->fields["campo44"];
			$ls_campo45=$io_report->rs_data->fields["campo45"];
			$ls_campo46=$io_report->rs_data->fields["campo46"];
			$ls_campo47=$io_report->rs_data->fields["campo47"];
			$ls_campo48=$io_report->rs_data->fields["campo48"];
			$ls_campo49=$io_report->rs_data->fields["campo49"];
			$ls_campo50=$io_report->rs_data->fields["campo50"];
			$ls_campo51=$io_report->rs_data->fields["campo51"];
			$ls_campo52=$io_report->rs_data->fields["campo52"];
			$ls_campo53=$io_report->rs_data->fields["campo53"];
			$ls_campo54=$io_report->rs_data->fields["campo54"];
			$ls_campo55=$io_report->rs_data->fields["campo55"];

			$ls_campo0=uf_validar_campo(0,$ls_campo0);
			$ls_campo1=uf_validar_campo(1,$ls_campo1);
			$ls_campo2=uf_validar_campo(2,$ls_campo2);
			$ls_campo3=uf_validar_campo(3,$ls_campo3);
			$ls_campo4=uf_validar_campo(4,$ls_campo4);
			$ls_campo5=uf_validar_campo(5,$ls_campo5);
			$ls_campo6=uf_validar_campo(6,$ls_campo6);
			$ls_campo7=uf_validar_campo(7,$ls_campo7);
			$ls_campo8=uf_validar_campo(8,$ls_campo8);
			$ls_campo9=uf_validar_campo(9,$ls_campo9);
			$ls_campo10=uf_validar_campo(10,$ls_campo10);
			$ls_campo11=uf_validar_campo(11,$ls_campo11);
			$ls_campo12=uf_validar_campo(12,$ls_campo12);
			$ls_campo13=uf_validar_campo(13,$ls_campo13);
			$ls_campo14=uf_validar_campo(14,$ls_campo14);
			$ls_campo15=uf_validar_campo(15,$ls_campo15);
			$ls_campo16=uf_validar_campo(16,$ls_campo16);
			$ls_campo17=uf_validar_campo(17,$ls_campo17);
			$ls_campo18=uf_validar_campo(18,$ls_campo18);
			$ls_campo19=uf_validar_campo(19,$ls_campo19);
			$ls_campo20=uf_validar_campo(20,$ls_campo20);
			$ls_campo21=uf_validar_campo(21,$ls_campo21);
			$ls_campo22=uf_validar_campo(22,$ls_campo22);
			$ls_campo23=uf_validar_campo(23,$ls_campo23);
			$ls_campo24=uf_validar_campo(24,$ls_campo24);
			$ls_campo25=uf_validar_campo(25,$ls_campo25);
			$ls_campo26=uf_validar_campo(26,$ls_campo26);
			$ls_campo27=uf_validar_campo(27,$ls_campo27);
			$ls_campo28=uf_validar_campo(28,$ls_campo28);
			$ls_campo29=uf_validar_campo(29,$ls_campo29);
			$ls_campo30=uf_validar_campo(30,$ls_campo30);
			$ls_campo31=uf_validar_campo(31,$ls_campo31);
			$ls_campo32=uf_validar_campo(32,$ls_campo32);
			$ls_campo33=uf_validar_campo(33,$ls_campo33);
			$ls_campo34=uf_validar_campo(34,$ls_campo34);
			$ls_campo35=uf_validar_campo(35,$ls_campo35);
			$ls_campo36=uf_validar_campo(36,$ls_campo36);
			$ls_campo37=uf_validar_campo(37,$ls_campo37);
			$ls_campo38=uf_validar_campo(38,$ls_campo38);
			$ls_campo39=uf_validar_campo(39,$ls_campo39);
			$ls_campo40=uf_validar_campo(40,$ls_campo40);
			$ls_campo41=uf_validar_campo(41,$ls_campo41);
			$ls_campo42=uf_validar_campo(42,$ls_campo42);
			$ls_campo43=uf_validar_campo(43,$ls_campo43);
			$ls_campo44=uf_validar_campo(44,$ls_campo44);
			$ls_campo45=uf_validar_campo(45,$ls_campo45);
			$ls_campo46=uf_validar_campo(66,$ls_campo46);
			$ls_campo47=uf_validar_campo(47,$ls_campo47);
			$ls_campo48=uf_validar_campo(48,$ls_campo48);
			$ls_campo49=uf_validar_campo(49,$ls_campo49);
			$ls_campo50=uf_validar_campo(50,$ls_campo50);
			$ls_campo51=uf_validar_campo(51,$ls_campo51);
			$ls_campo52=uf_validar_campo(52,$ls_campo52);
			$ls_campo53=uf_validar_campo(53,$ls_campo53);
			$ls_campo54=uf_validar_campo(54,$ls_campo54);
			$ls_campo55=uf_validar_campo(54,$ls_campo55);

			$la_campos[0]=$ls_campo0;
			$la_campos[1]=$ls_campo1;
			$la_campos[2]=$ls_campo2;
			$la_campos[3]=$ls_campo3;
			$la_campos[4]=$ls_campo4;
			$la_campos[5]=$ls_campo5;
			$la_campos[6]=$ls_campo6;
			$la_campos[7]=$ls_campo7;
			$la_campos[8]=$ls_campo8;
			$la_campos[9]=$ls_campo9;
			$la_campos[10]=$ls_campo10;
			$la_campos[11]=$ls_campo11;
			$la_campos[12]=$ls_campo12;
			$la_campos[13]=$ls_campo13;
			$la_campos[14]=$ls_campo14;
			$la_campos[15]=$ls_campo15;
			$la_campos[16]=$ls_campo16;
			$la_campos[17]=$ls_campo17;
			$la_campos[18]=$ls_campo18;
			$la_campos[19]=$ls_campo19;
			$la_campos[20]=$ls_campo20;
			$la_campos[21]=$ls_campo21;
			$la_campos[22]=$ls_campo22;
			$la_campos[23]=$ls_campo23;
			$la_campos[24]=$ls_campo24;
			$la_campos[25]=$ls_campo25;
			$la_campos[26]=$ls_campo26;
			$la_campos[27]=$ls_campo27;
			$la_campos[28]=$ls_campo28;
			$la_campos[29]=$ls_campo29;
			$la_campos[30]=$ls_campo30;
			$la_campos[31]=$ls_campo31;
			$la_campos[32]=$ls_campo32;
			$la_campos[33]=$ls_campo33;
			$la_campos[34]=$ls_campo34;
			$la_campos[35]=$ls_campo35;
			$la_campos[36]=$ls_campo36;
			$la_campos[37]=$ls_campo37;
			$la_campos[38]=$ls_campo38;
			$la_campos[39]=$ls_campo39;
			$la_campos[40]=$ls_campo40;
			$la_campos[41]=$ls_campo41;
			$la_campos[42]=$ls_campo42;
			$la_campos[43]=$ls_campo43;
			$la_campos[44]=$ls_campo44;
			$la_campos[45]=$ls_campo45;
			$la_campos[46]=$ls_campo46;
			$la_campos[47]=$ls_campo47;
			$la_campos[48]=$ls_campo48;
			$la_campos[49]=$ls_campo49;
			$la_campos[50]=$ls_campo50;
			$la_campos[51]=$ls_campo51;
			$la_campos[52]=$ls_campo52;
			$la_campos[53]=$ls_campo53;
			$la_campos[54]=$ls_campo54;
			$la_campos[55]=$ls_campo55;

			$li_row=$li_row+1;
			for($li_k=0;($li_k<=$_SESSION["li_total"]);$li_k++)
			{
				switch($_SESSION["la_titulos"][$li_k]["alineacion"])
				{
					case "center":
						$lo_hoja->write($li_row, $li_k, $la_campos[$li_k]." ", $lo_datacenter);
						break;
					case "left":
						$lo_hoja->write($li_row, $li_k, $la_campos[$li_k]." ", $lo_dataleft);
						break;
					case "right":
						$lo_hoja->write($li_row, $li_k, $la_campos[$li_k], $lo_dataright);
						break;
				}
			}
			$io_report->rs_data->MoveNext();
		}
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"listado_personal_generico.xls\"");
		header("Content-Disposition: inline; filename=\"listado_personal_generico.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 