<?php 
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	session_start();
	ini_set('memory_limit','256M');
	ini_set('max_execution_time ','0');
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";		
	}

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($li_row,$lo_titulo,$lo_hoja,$as_titulo,$as_moneda,$as_trimestre)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//                 $as_moneda // Moneda
		//	    		   as_trimestre // Nro. del Trimestre
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_row, $lo_titulo, $lo_hoja;
		$lo_hoja->write($li_row, 1, "Denominacion",$lo_titulo);
		$lo_hoja->write($li_row, 2, "Presupuesto",$lo_titulo);
		$lo_hoja->write($li_row+1, 2, "Aprobado",$lo_titulo);
		$lo_hoja->write($li_row, 3, "Presupuesto",$lo_titulo);
		$lo_hoja->write($li_row+1, 3, "Modificado",$lo_titulo);
		$lo_hoja->write($li_row-1, 4, "ACUMULADOO",$lo_titulo);
		$lo_hoja->write($li_row, 4, "Programado",$lo_titulo);
		$lo_hoja->write($li_row, 5, "Ejecutado",$lo_titulo);
		$lo_hoja->write($li_row-2, 6, "VARIACION EJECUCIÓN-",$lo_titulo);
		$lo_hoja->write($li_row-1, 6, "PROGRAMADO",$lo_titulo);
		$lo_hoja->write($li_row, 6, "Absoluto",$lo_titulo);
		$lo_hoja->write($li_row, 7, "Porcentual",$lo_titulo);
		++$li_row;
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_reporte($li_row,$lo_titulo,$lo_hoja,$ai_ano,$as_meses,$ls_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_row, $lo_titulo, $lo_hoja;
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$ls_nomorgads=$_SESSION["la_empresa"]["nomorgads"];
		$ls_codasiona   = $_SESSION['la_empresa']['codasiona'];
		$lo_hoja->write($li_row, 3, "$ls_titulo ",$lo_titulo);
		$li_row++;
		$li_row++;
		$lo_hoja->write($li_row, 1, "CODIGO PRESUPUESTARIO DEL ENTE: $ls_codasiona ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 1, "DENOMINACION DEL ENTE:  $ls_nombre ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 1, "ORGANO DE ADSCRIPCION:  $ls_nomorgads ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 1, "FECHA: $ai_ano  ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 1, "MES: $as_meses  ",$lo_titulo);
		$li_row++;

	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
		
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($li_row,$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$lo_dataleftb,$lo_datarightb,$la_data)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_row, $lo_titulo, $lo_hoja;
		$li_row++;
		$ls_html = array('<b>','</b>');
		for( $i = 1; $i <= count((array)$la_data); $i ++)
		{
			$formato 	= (strstr($la_data[$i]['denominacion'],'<b>')) ? $lo_datarightb : $lo_dataright;
			$formatotx  = (strstr($la_data[$i]['denominacion'],'<b>')) ? $lo_dataleftb : $lo_dataleft;
			if($la_data[$i]['asignado'] == $la_data[$i]['modificado'])
			{
			 $la_data[$i]['modificado'] = " ";
			}
			
			$lo_hoja->write($li_row, 0, $la_data[$i]['cuenta'],$lo_datacenter);
			$lo_hoja->write($li_row, 1, str_replace($ls_html,'',$la_data[$i]['denominacion']),$formatotx);
			$lo_hoja->write($li_row, 2, $la_data[$i]['asignado'],$formato);
			$lo_hoja->write($li_row, 3, $la_data[$i]['modificado'],$formato);
			$lo_hoja->write($li_row, 4, $la_data[$i]['programado'],$formato);
			$lo_hoja->write($li_row, 5, $la_data[$i]['ejecutado'],$formato);
			$lo_hoja->write($li_row, 6, $la_data[$i]['absoluto'],$formato);
			$lo_hoja->write($li_row, 7, $la_data[$i]['porcentual'],$formato);

			$li_row++;
		}		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($li_row,$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_tot)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 10/06/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_row, $lo_titulo, $lo_hoja;

		if($la_data_tot[1]['asignado'] == $la_data_tot[1]['modificado'])
		{
			$la_data_tot[1]['modificado'] = " ";
		}
		
		$lo_hoja->write($li_row, 0, $la_data_tot[1]['cuenta'],$lo_datacenter);
		$lo_hoja->write($li_row, 1, $la_data_tot[1]['denominacion'],$lo_dataleft);
		$lo_hoja->write($li_row, 2, $la_data_tot[1]['asignado'],$lo_dataright);
		$lo_hoja->write($li_row, 3, $la_data_tot[1]['modificado'],$lo_dataright);
		$lo_hoja->write($li_row, 4, $la_data_tot[1]['programado'],$lo_dataright);
		$lo_hoja->write($li_row, 5, $la_data_tot[1]['ejecutado'],$lo_dataright);
		$lo_hoja->write($li_row, 6, $la_data_tot[1]['absoluto'],$lo_dataright);
		$lo_hoja->write($li_row, 7, $la_data_tot[1]['porcentual'],$lo_dataright);

		$li_row++;
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_linea($li_row,$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_tot)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 10/06/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $lo_titulo, $lo_hoja, $lo_dataleftb;

		if($la_data_tot[1]['asignado'] == $la_data_tot[1]['modificado'])
		{
			$la_data_tot[1]['modificado'] = " ";
		}
		
		$lo_hoja->write($li_row, 0, $la_data_tot[1]['cuenta'],$lo_datacenter);
		$lo_hoja->write($li_row, 1, $la_data_tot[1]['denominacion'],$lo_dataleftb);
		$lo_hoja->write($li_row, 2, $la_data_tot[1]['asignado'],$lo_dataright);
		$lo_hoja->write($li_row, 3, $la_data_tot[1]['modificado'],$lo_dataright);
		$lo_hoja->write($li_row, 4, $la_data_tot[1]['programado'],$lo_dataright);
		$lo_hoja->write($li_row, 5, $la_data_tot[1]['ejecutado'],$lo_dataright);
		$lo_hoja->write($li_row, 6, $la_data_tot[1]['absoluto'],$lo_dataright);
		$lo_hoja->write($li_row, 7, $la_data_tot[1]['porcentual'],$lo_dataright);

	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

        //--------------------------------------------------------------------------------------------------------------------------------
	function uf_formato_cuenta_instructivo($as_cuenta)
	{
		 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 //	      Function :	uf_formato_cuenta_instructivo
		 //         Access :	private
		 //     Argumentos :    $as_cuenta // cuenta de ingreso
		 //	       Returns :	Retorna cuenta con el formato para el instructivo
		 //	   Description :	devuelve la cuenta de ingreso con el formato mostrado en los instructivos
		 //     Creado por :    Ing. Arnaldo Suárez
		 // Fecha Creación :    25/09/2009         Fecha última Modificacion :      Hora :
		 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $ls_cuenta="";
            if(!empty($as_cuenta))
            {
                $total = 11;

                for($i=0;$i<$total;$i++)
                {
                    if ($i<=5)
                    {
                        $ls_cuenta .=substr($as_cuenta,$i,1).".";   
                    }
                    if (($i==6)||($i==8))
                    {
                        $ls_cuenta .=substr($as_cuenta,$i,2).".";     
                    }
                    if ($i==10)
                    {
                        $ls_cuenta .=substr($as_cuenta,$i,1);     
                    }
                }
            }
            return $ls_cuenta;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");	
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();	
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha = new class_fecha();
	//-----------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	//para crear el libro excel
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "presupuestodecaja0713.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	$li_row = 1;
	//---------------------------------------------------------------------------------------------------------------------------

	global $la_data_tot;
        require_once("sigesp_scg_reporte_comparado_0206.php");
        $io_report = new sigesp_scg_reporte_comparado_0206();
	$li_candeccon=$_SESSION["la_empresa"]["candeccon"];
	$li_tipconmon=$_SESSION["la_empresa"]["tipconmon"];
	$li_redconmon=$_SESSION["la_empresa"]["redconmon"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	$li_ano=substr($ldt_periodo,0,4);
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
        $li_mesdes=$_GET["mesdes"];
        $li_meshas=$_GET["meshas"];
        $ldt_ult_dia=$io_fecha->uf_last_day($li_meshas,$li_ano);
        $fechas=$ldt_ult_dia;
        $ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
        $ls_meses = "";
        for ($i = $li_mesdes;$i<=$li_meshas;$i++)
        {
            $ls_mes=$io_fecha->uf_load_nombre_mes($i);
            $ls_meses = $ls_meses." ".$ls_mes;
        }
        $ls_cant_mes=$i-1;
        $ls_meshas=$io_fecha->uf_load_nombre_mes($li_meshas);

        $ls_formpre=$_SESSION["la_empresa"]["formpre"];
        $ls_formpre=str_replace('-','',$ls_formpre);
        $li_len=strlen($ls_formpre);
        $li_len=$li_len-9;
        $ls_diades="01";
        $ls_diahas=$io_fecha->uf_last_day($li_meshas,$li_ano);
        $ldt_fecdes=$ls_diades."/".$li_mesdes."/".$li_ano;
        $ldt_fechas=$ls_diahas;
		$ls_trimestre=$li_mesdes." - ".$li_meshas;
        $ls_ceros=$io_funciones->uf_cerosderecha("",$li_len);		
//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
    $ls_titulo="RESULTADO ECONÓMICO-FINANCIERO";       
//--------------------------------------------------------------------------------------------------------------------------------
    $lb_valido=$io_report->uf_scg_reportes_comparados_0206($ldt_fecdes,$ldt_fechas,$li_mesdes,$li_meshas,$ls_cant_mes,1);
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
		$lo_titulo->set_align('left');
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
		//---> formatos bold
		$lo_datacenterb= &$lo_libro->addformat();
		$lo_datacenterb->set_font("Verdana");
		$lo_datacenterb->set_align('center');
		$lo_datacenterb->set_size('9');
		$lo_datacenterb->set_bold();
		$lo_dataleftb= &$lo_libro->addformat();
		$lo_dataleftb->set_text_wrap();
		$lo_dataleftb->set_font("Verdana");
		$lo_dataleftb->set_align('left');
		$lo_dataleftb->set_size('9');
		$lo_dataleftb->set_bold();
		$lo_datarightb= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_datarightb->set_font("Verdana");
		$lo_datarightb->set_align('right');
		$lo_datarightb->set_size('9');
		$lo_datarightb->set_bold();		

		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,70);
		$lo_hoja->set_column(2,2,25);
		$lo_hoja->set_column(3,3,25);
		$lo_hoja->set_column(4,4,25);
		$lo_hoja->set_column(5,5,25);
		$lo_hoja->set_column(6,6,25);
		$lo_hoja->set_column(7,7,25);
		$lo_hoja->set_column(8,8,25);
		$lo_hoja->set_column(9,9,25);
		$lo_hoja->set_column(10,10,25);
		$lo_hoja->set_column(11,11,25);
		$lo_hoja->set_column(12,12,25);

		$li_row = 2;
		//--------------------------------------------------------------------------------------------------		
		$li_row++;
	 	uf_print_titulo_reporte($li_row,$lo_titulo,$lo_hoja,$ldt_fechas,$ls_meses,$ls_titulo);
		
		$li_row = 9;
		uf_print_encabezado_pagina($li_row,$lo_titulo,$lo_hoja,$ls_titulo,'(En Bolivares Fuertes)',$ls_trimestre); // Imprimimos el encabezado de la página
		
		$li_tot=$io_report->dts_reporte->getRowCount("sc_cuenta");
                $ld_total_asignado=0;
		$ld_total_modificado=0;
		$ld_total_programado=0;
		$ld_total_ejecutado=0;
		$ld_total_absoluto=0;
		$ld_total_porcentual=0;
                $ld_11_asignado         = 0;
                $ld_11_modificado       = 0;
                $ld_11_programado       = 0;
                $ld_11_ejecutado        = 0;
                $ld_211_asignado         = 0;
                $ld_211_modificado       = 0;
                $ld_211_programado       = 0;
                $ld_211_ejecutado        = 0;
                $li_pos1=0;
                $li_pos2=0;                
                $espacio=true;

                $li_contador=1;
                $la_data[$li_contador]=array('cuenta'=>"",
                                                 'denominacion'=>"1. CUENTA CORRIENTE",
                                                 'asignado'=>"",
                                                 'modificado'=>"",
                                                 'programado'=>"",
                                                 'ejecutado'=>"",
                                                 'absoluto'=>"",
                                                 'porcentual'=>"");
                $li_contador++;
                $li_pos1=$li_row+2;
                $la_data[$li_contador]=array('cuenta'=>"",
                                                 'denominacion'=>"1.1 INGRESOS CORRIENTES",
                                                 'asignado'=>"",
                                                 'modificado'=>"",
                                                 'programado'=>"",
                                                 'ejecutado'=>"",
                                                 'absoluto'=>"",
                                                 'porcentual'=>"");
                
		for($z=1;$z<=$li_tot;$z++)
		{		
			$ls_cuenta          =  $io_report->dts_reporte->data["sc_cuenta"][$z];
			$ls_denominacion    =  $io_report->dts_reporte->data["denominacion"][$z];
			$ls_status          =  $io_report->dts_reporte->data["status"][$z];
			$ld_asignado        =  $io_report->dts_reporte->data["monto_asignado"][$z];
			$ld_modificado      =  $io_report->dts_reporte->data["monto_modificado"][$z];
			$ld_programado      =  $io_report->dts_reporte->data["programado_acumulado"][$z];
			$ld_ejecutado       =  $io_report->dts_reporte->data["ejecutado_acumulado"][$z];
			$ld_absoluto        =  $ld_ejecutado  - $ld_programado;
                        $ld_porcentual      = 0;
                        if ($ld_programado > 0)
                        {
                            $ld_porcentual      =  ($ld_ejecutado/$ld_programado)*100;  
                        }
			
			if(($ls_status == "C"))
			{
                                if ((substr($ls_cuenta,0,3)=="111") || (substr($ls_cuenta,0,3)=="112"))
                                {
                                    $ld_11_asignado         = $ld_11_asignado + $ld_asignado;
                                    $ld_11_modificado       = $ld_11_modificado + $ld_modificado;
                                    $ld_11_programado       = $ld_11_programado + $ld_programado;
                                    $ld_11_ejecutado        = $ld_11_ejecutado + $ld_ejecutado;
                                }
                                if ((substr($ls_cuenta,0,3)=="211"))
                                {
                                    $ld_211_asignado         = $ld_211_asignado + $ld_asignado;
                                    $ld_211_modificado       = $ld_211_modificado + $ld_modificado;
                                    $ld_211_programado       = $ld_211_programado + $ld_programado;
                                    $ld_211_ejecutado        = $ld_211_ejecutado + $ld_ejecutado;
                                }
			}
                        if ((substr($ls_cuenta,0,3)=="211")&&($espacio))
                        {
                            $li_contador++;
                            $espacio=false;
                            $la_data[$li_contador]=array('cuenta'=>"",
                                                             'denominacion'=>"",
                                                             'asignado'=>"",
                                                             'modificado'=>"",
                                                             'programado'=>"",
                                                             'ejecutado'=>"",
                                                             'absoluto'=>"",
                                                             'porcentual'=>"");
                            
                            $li_contador++;
                            $li_pos2=$li_row+$li_contador;
                            $la_data[$li_contador]=array('cuenta'=>"",
                                                             'denominacion'=>"1.2 GASTOS CORRIENTES",
                                                             'asignado'=>"",
                                                             'modificado'=>"",
                                                             'programado'=>"",
                                                             'ejecutado'=>"",
                                                             'absoluto'=>"",
                                                             'porcentual'=>"");
                            
                        
                        }
			$ld_asignado               = number_format($ld_asignado,2,",",".");
			$ld_modificado             = number_format($ld_modificado,2,",",".");
			$ld_programado             = number_format($ld_programado,2,",",".");
			$ld_ejecutado              = number_format($ld_ejecutado,2,",",".");
			$ld_absoluto               = number_format($ld_absoluto,2,",",".");
			$ld_porcentual             = number_format($ld_porcentual,2,",",".")." % ";
			  
			if($ld_modificado=='0,00')
			{
				$ld_modificado='';
			}
                        $li_contador++;
			$la_data[$li_contador]=array('cuenta'=>uf_formato_cuenta_instructivo(trim($ls_cuenta)),
							 'denominacion'=>$ls_denominacion,
							 'asignado'=>$ld_asignado,
							 'modificado'=>$ld_modificado,
							 'programado'=>$ld_programado,
							 'ejecutado'=>$ld_ejecutado,
							 'absoluto'=>$ld_absoluto,
							 'porcentual'=>$ld_porcentual);
                        
					  							 						   
		}//for
		$ld_total_absoluto           = (($ld_11_ejecutado-$ld_211_ejecutado) - ($ld_11_programado - $ld_211_programado));
                $ld_total_programado = $ld_11_programado - $ld_211_programado;
                $ld_total_porcentual = 0;
		if ($ld_total_programado > 0)
		{
			$ld_total_porcentual = (($ld_11_ejecutado-$ld_211_ejecutado)/$ld_total_programado)*100;
		}
		$ld_total_asignado           = number_format($ld_11_asignado-$ld_211_asignado,2,",",".");
		$ld_total_modificado         = number_format($ld_11_modificado-$ld_211_modificado,2,",",".");
		$ld_total_programado         = number_format($ld_11_programado -$ld_211_programado,2,",",".");
		$ld_total_ejecutado          = number_format($ld_11_ejecutado-$ld_211_ejecutado,2,",",".");
		$ld_total_absoluto           = number_format($ld_total_absoluto,2,",",".");
		$ld_total_porcentual         = number_format($ld_total_porcentual,2,",",".")." % ";
		
		if($ld_total_modificado=='0,00')
		{
			$ld_total_modificado='';
		}
		$la_data_tot[1]=array('cuenta'=>"",
							  'denominacion'=>"RESULTADO ECONÓMICO EN CUENTA CORRIENTE : AHORRO/(DESAHORRO)        ",
							  'asignado'=>$ld_total_asignado,
							  'modificado'=>$ld_total_modificado,
							  'programado'=>$ld_total_programado,
							  'ejecutado'=>$ld_total_ejecutado,
							  'absoluto'=>$ld_total_absoluto,
							  'porcentual'=>$ld_total_porcentual);
		uf_print_detalle($li_row,$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$lo_dataleftb,$lo_datarightb,$la_data); // Imprimimos el detalle 
                uf_print_pie_cabecera($li_row,$lo_titulo,$lo_hoja,$lo_datacenterb,$lo_dataleftb,$lo_datarightb,$la_data_tot);
		unset($la_data);
		unset($la_data_tot);

                $ld_absoluto           = ($ld_11_ejecutado - $ld_11_programado);
                $ld_porcentual = 0;
		if ($ld_11_programado > 0)
		{
			$ld_porcentual = ($ld_11_ejecutado/$ld_11_programado)*100;
		}
                $ld_asignado               = number_format($ld_11_asignado,2,",",".");
                $ld_modificado             = number_format($ld_11_modificado,2,",",".");
                $ld_programado             = number_format($ld_11_programado,2,",",".");
                $ld_ejecutado              = number_format($ld_11_ejecutado,2,",",".");
                $ld_absoluto               = number_format($ld_absoluto,2,",",".");
                $ld_porcentual             = number_format($ld_porcentual,2,",",".")." % ";

                if($ld_modificado=='0,0')
                {
                        $ld_modificado='';
                }
                $la_data_tot[1]=array('cuenta'=>"",
                                                 'denominacion'=>"1.1 INGRESOS CORRIENTES",
                                                 'asignado'=>$ld_asignado,
                                                 'modificado'=>$ld_modificado,
                                                 'programado'=>$ld_programado,
                                                 'ejecutado'=>$ld_ejecutado,
                                                 'absoluto'=>$ld_absoluto,
                                                 'porcentual'=>$ld_porcentual);
                
                uf_print_linea($li_pos1,$lo_titulo,$lo_hoja,$lo_datacenterb,$lo_dataleftb,$lo_datarightb,$la_data_tot);
		unset($la_data_tot);
                
                $ld_absoluto           = ($ld_211_ejecutado - $ld_211_programado);
                $ld_porcentual = 0;
		if ($ld_211_programado > 0)
		{
			$ld_porcentual = ($ld_211_ejecutado/$ld_211_programado)*100;
		}
                $ld_asignado               = number_format($ld_211_asignado,2,",",".");
                $ld_modificado             = number_format($ld_211_modificado,2,",",".");
                $ld_programado             = number_format($ld_211_programado,2,",",".");
                $ld_ejecutado              = number_format($ld_211_ejecutado,2,",",".");
                $ld_absoluto               = number_format($ld_absoluto,2,",",".");
                $ld_porcentual             = number_format($ld_porcentual,2,",",".")." % ";

                if($ld_modificado=='0,00')
                {
                        $ld_modificado='';
                }
                
                $la_data_tot[1]=array('cuenta'=>"",
                                                 'denominacion'=>"1.2 GASTOS CORRIENTES",
                                                 'asignado'=>$ld_asignado,
                                                 'modificado'=>$ld_modificado,
                                                 'programado'=>$ld_programado,
                                                 'ejecutado'=>$ld_ejecutado,
                                                 'absoluto'=>$ld_absoluto,
                                                 'porcentual'=>$ld_porcentual);
                uf_print_linea($li_pos2,$lo_titulo,$lo_hoja,$lo_datacenterb,$lo_dataleftb,$lo_datarightb,$la_data_tot);
		unset($la_data_tot);
                
                $lb_valido=$io_report->uf_scg_reportes_comparados_0206($ldt_fecdes,$ldt_fechas,$li_mesdes,$li_meshas,$ls_cant_mes,2);
                if($lb_valido==false) // Existe algún error ó no hay registros
                {
                       print("<script language=JavaScript>");
                       print(" alert('No hay nada que Reportar');"); 
                       print(" close();");
                       print("</script>");
                }
                else // Imprimimos el reporte
                {
                    $li_tot=$io_report->dts_reporte->getRowCount("sc_cuenta");
                    $ld_total_asignado=0;
                    $ld_total_modificado=0;
                    $ld_total_programado=0;
                    $ld_total_ejecutado=0;
                    $ld_total_absoluto=0;
                    $ld_total_porcentual=0;
                    $ld_11_asignado         = 0;
                    $ld_11_modificado       = 0;
                    $ld_11_programado       = 0;
                    $ld_11_ejecutado        = 0;
                    $ld_211_asignado         = 0;
                    $ld_211_modificado       = 0;
                    $ld_211_programado       = 0;
                    $ld_211_ejecutado        = 0;
                    $espacio=true;
                    $li_contador=1;
                    $la_data[$li_contador]=array('cuenta'=>"",
                                                     'denominacion'=>"",
                                                     'asignado'=>"",
                                                     'modificado'=>"",
                                                     'programado'=>"",
                                                     'ejecutado'=>"",
                                                     'absoluto'=>"",
                                                     'porcentual'=>"");
                    $li_contador++;
                    $la_data[$li_contador]=array('cuenta'=>"",
                                                     'denominacion'=>"",
                                                     'asignado'=>"",
                                                     'modificado'=>"",
                                                     'programado'=>"",
                                                     'ejecutado'=>"",
                                                     'absoluto'=>"",
                                                     'porcentual'=>"");
                    $li_contador++;
                    $la_data[$li_contador]=array('cuenta'=>"",
                                                     'denominacion'=>"2. CUENTA CAPITAL",
                                                     'asignado'=>"",
                                                     'modificado'=>"",
                                                     'programado'=>"",
                                                     'ejecutado'=>"",
                                                     'absoluto'=>"",
                                                     'porcentual'=>"");
                    $li_contador++;
                    $la_data[$li_contador]=array('cuenta'=>"",
                                                     'denominacion'=>"2.1 INGRESOS CAPITAL",
                                                     'asignado'=>"",
                                                     'modificado'=>"",
                                                     'programado'=>"",
                                                     'ejecutado'=>"",
                                                     'absoluto'=>"",
                                                     'porcentual'=>"");
                
                    for($z=1;$z<=$li_tot;$z++)
                    {		
                            $ls_cuenta          =  $io_report->dts_reporte->data["sc_cuenta"][$z];
                            $ls_denominacion    =  $io_report->dts_reporte->data["denominacion"][$z];
                            $ls_status          =  $io_report->dts_reporte->data["status"][$z];
                            $ld_asignado        =  $io_report->dts_reporte->data["monto_asignado"][$z];
                            $ld_modificado      =  $io_report->dts_reporte->data["monto_modificado"][$z];
                            $ld_programado      =  $io_report->dts_reporte->data["programado_acumulado"][$z];
                            $ld_ejecutado       =  $io_report->dts_reporte->data["ejecutado_acumulado"][$z];
                            $ld_absoluto        =  $ld_ejecutado  - $ld_programado;
                            $ld_porcentual      = 0;
                            if ($ld_programado > 0)
                            {
                                $ld_porcentual      =  ($ld_ejecutado/$ld_programado)*100;  
                            }

                            if(($ls_status == "C"))
                            {
                                    if (substr($ls_cuenta,0,3)=="113")
                                    {
                                        $ld_11_asignado         = $ld_11_asignado + $ld_asignado;
                                        $ld_11_modificado       = $ld_11_modificado + $ld_modificado;
                                        $ld_11_programado       = $ld_11_programado + $ld_programado;
                                        $ld_11_ejecutado        = $ld_11_ejecutado + $ld_ejecutado;
                                    }
                                    if ((substr($ls_cuenta,0,3)=="212"))
                                    {
                                        $ld_211_asignado         = $ld_211_asignado + $ld_asignado;
                                        $ld_211_modificado       = $ld_211_modificado + $ld_modificado;
                                        $ld_211_programado       = $ld_211_programado + $ld_programado;
                                        $ld_211_ejecutado        = $ld_211_ejecutado + $ld_ejecutado;
                                    }
                            }
                            if ((substr($ls_cuenta,0,3)=="212")&&($espacio))
                            {
                                $li_contador++;
                                $espacio=false;
                                $la_data[$li_contador]=array('cuenta'=>"",
                                                                 'denominacion'=>"",
                                                                 'asignado'=>"",
                                                                 'modificado'=>"",
                                                                 'programado'=>"",
                                                                 'ejecutado'=>"",
                                                                 'absoluto'=>"",
                                                                 'porcentual'=>"");

                                $li_contador++;
                                $la_data[$li_contador]=array('cuenta'=>"",
                                                                 'denominacion'=>"2.2 GASTOS DE CAPITAL",
                                                                 'asignado'=>"",
                                                                 'modificado'=>"",
                                                                 'programado'=>"",
                                                                 'ejecutado'=>"",
                                                                 'absoluto'=>"",
                                                                 'porcentual'=>"");


                            }
                            $ld_asignado               = number_format($ld_asignado,2,",",".");
                            $ld_modificado             = number_format($ld_modificado,2,",",".");
                            $ld_programado             = number_format($ld_programado,2,",",".");
                            $ld_ejecutado              = number_format($ld_ejecutado,2,",",".");
                            $ld_absoluto               = number_format($ld_absoluto,2,",",".");
                            $ld_porcentual             = number_format($ld_porcentual,2,",",".")." % ";

                            if($ld_modificado=='0,00')
                            {
                                    $ld_modificado='';
                            }
                            $li_contador++;
                            $la_data[$li_contador]=array('cuenta'=>uf_formato_cuenta_instructivo(trim($ls_cuenta)),
                                                             'denominacion'=>$ls_denominacion,
                                                             'asignado'=>$ld_asignado,
                                                             'modificado'=>$ld_modificado,
                                                             'programado'=>$ld_programado,
                                                             'ejecutado'=>$ld_ejecutado,
                                                             'absoluto'=>$ld_absoluto,
                                                             'porcentual'=>$ld_porcentual);


                    }//for
                    $ld_total_absoluto           = (($ld_11_ejecutado-$ld_211_ejecutado) - ($ld_11_programado - $ld_211_programado));
                    $ld_total_programado = $ld_11_programado - $ld_211_programado;
                    $ld_total_porcentual = 0;
                    if ($ld_total_programado > 0)
                    {
                            $ld_total_porcentual = (($ld_11_ejecutado-$ld_211_ejecutado)/$ld_total_programado)*100;
                    }
                    $ld_total_asignado           = number_format($ld_11_asignado-$ld_211_asignado,2,",",".");
                    $ld_total_modificado         = number_format($ld_11_modificado-$ld_211_modificado,2,",",".");
                    $ld_total_programado         = number_format($ld_11_programado -$ld_211_programado,2,",",".");
                    $ld_total_ejecutado          = number_format($ld_11_ejecutado-$ld_211_ejecutado,2,",",".");
                    $ld_total_absoluto           = number_format($ld_total_absoluto,2,",",".");
                    $ld_total_porcentual         = number_format($ld_total_porcentual,2,",",".")." % ";

                    if($ld_total_modificado=='0,00')
                    {
                            $ld_total_modificado='';
                    }
                    $la_data_tot[1]=array('cuenta'=>"",
                                                              'denominacion'=>"RESULTADO FINANCIERO: SUPERÁVIT/(DÉFICIT))        ",
                                                              'asignado'=>$ld_total_asignado,
                                                              'modificado'=>$ld_total_modificado,
                                                              'programado'=>$ld_total_programado,
                                                              'ejecutado'=>$ld_total_ejecutado,
                                                              'absoluto'=>$ld_total_absoluto,
                                                              'porcentual'=>$ld_total_porcentual);
                    uf_print_detalle($li_row,$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$lo_dataleftb,$lo_datarightb,$la_data); // Imprimimos el detalle 
                    uf_print_pie_cabecera($li_row,$lo_titulo,$lo_hoja,$lo_datacenterb,$lo_dataleftb,$lo_datarightb,$la_data_tot);
                    unset($la_data);
                    unset($la_data_tot);
                }
                
                
                $lb_valido=$io_report->uf_scg_reportes_comparados_0206($ldt_fecdes,$ldt_fechas,$li_mesdes,$li_meshas,$ls_cant_mes,3);
                if($lb_valido==false) // Existe algún error ó no hay registros
                {
                       print("<script language=JavaScript>");
                       print(" alert('No hay nada que Reportar');"); 
                       print(" close();");
                       print("</script>");
                }
                else // Imprimimos el reporte
                {
                    $li_tot=$io_report->dts_reporte->getRowCount("sc_cuenta");
                    $ld_total_asignado=0;
                    $ld_total_modificado=0;
                    $ld_total_programado=0;
                    $ld_total_ejecutado=0;
                    $ld_total_absoluto=0;
                    $ld_total_porcentual=0;
                    $ld_11_asignado         = 0;
                    $ld_11_modificado       = 0;
                    $ld_11_programado       = 0;
                    $ld_11_ejecutado        = 0;
                    $ld_211_asignado         = 0;
                    $ld_211_modificado       = 0;
                    $ld_211_programado       = 0;
                    $ld_211_ejecutado        = 0;
                    $espacio=true;
                    $li_contador=1;
                    $la_data[$li_contador]=array('cuenta'=>"",
                                                     'denominacion'=>"",
                                                     'asignado'=>"",
                                                     'modificado'=>"",
                                                     'programado'=>"",
                                                     'ejecutado'=>"",
                                                     'absoluto'=>"",
                                                     'porcentual'=>"");
                    $li_contador++;
                    $la_data[$li_contador]=array('cuenta'=>"",
                                                     'denominacion'=>"",
                                                     'asignado'=>"",
                                                     'modificado'=>"",
                                                     'programado'=>"",
                                                     'ejecutado'=>"",
                                                     'absoluto'=>"",
                                                     'porcentual'=>"");
                    $li_contador++;
                    $la_data[$li_contador]=array('cuenta'=>"",
                                                     'denominacion'=>"3. CUENTAS FINANCIERAS",
                                                     'asignado'=>"",
                                                     'modificado'=>"",
                                                     'programado'=>"",
                                                     'ejecutado'=>"",
                                                     'absoluto'=>"",
                                                     'porcentual'=>"");
                    $li_contador++;
                    $la_data[$li_contador]=array('cuenta'=>"",
                                                     'denominacion'=>"3.1 FUENTES FINANCIERAS",
                                                     'asignado'=>"",
                                                     'modificado'=>"",
                                                     'programado'=>"",
                                                     'ejecutado'=>"",
                                                     'absoluto'=>"",
                                                     'porcentual'=>"");
                
                    for($z=1;$z<=$li_tot;$z++)
                    {		
                            $ls_cuenta          =  $io_report->dts_reporte->data["sc_cuenta"][$z];
                            $ls_denominacion    =  $io_report->dts_reporte->data["denominacion"][$z];
                            $ls_status          =  $io_report->dts_reporte->data["status"][$z];
                            $ld_asignado        =  $io_report->dts_reporte->data["monto_asignado"][$z];
                            $ld_modificado      =  $io_report->dts_reporte->data["monto_modificado"][$z];
                            $ld_programado      =  $io_report->dts_reporte->data["programado_acumulado"][$z];
                            $ld_ejecutado       =  $io_report->dts_reporte->data["ejecutado_acumulado"][$z];
                            $ld_absoluto        =  $ld_ejecutado  - $ld_programado;
                            $ld_porcentual      = 0;
                            if ($ld_programado > 0)
                            {
                                $ld_porcentual      =  ($ld_ejecutado/$ld_programado)*100;  
                            }

                            if(($ls_status == "C"))
                            {
                                    if ((substr($ls_cuenta,0,3)=="121") || (substr($ls_cuenta,0,3)=="122") || (substr($ls_cuenta,0,3)=="124"))
                                    {
                                        $ld_11_asignado         = $ld_11_asignado + $ld_asignado;
                                        $ld_11_modificado       = $ld_11_modificado + $ld_modificado;
                                        $ld_11_programado       = $ld_11_programado + $ld_programado;
                                        $ld_11_ejecutado        = $ld_11_ejecutado + $ld_ejecutado;
                                    }
                            }
                            $ld_asignado               = number_format($ld_asignado,2,",",".");
                            $ld_modificado             = number_format($ld_modificado,2,",",".");
                            $ld_programado             = number_format($ld_programado,2,",",".");
                            $ld_ejecutado              = number_format($ld_ejecutado,2,",",".");
                            $ld_absoluto               = number_format($ld_absoluto,2,",",".");
                            $ld_porcentual             = number_format($ld_porcentual,2,",",".")." % ";

                            if($ld_modificado=='0,00')
                            {
                                    $ld_modificado='';
                            }
                            $li_contador++;
                            $la_data[$li_contador]=array('cuenta'=>uf_formato_cuenta_instructivo(trim($ls_cuenta)),
                                                             'denominacion'=>$ls_denominacion,
                                                             'asignado'=>$ld_asignado,
                                                             'modificado'=>$ld_modificado,
                                                             'programado'=>$ld_programado,
                                                             'ejecutado'=>$ld_ejecutado,
                                                             'absoluto'=>$ld_absoluto,
                                                             'porcentual'=>$ld_porcentual);


                    }//for
                    $ld_total_absoluto           = ($ld_11_ejecutado - $ld_11_programado);
                    $ld_total_programado = $ld_11_programado;
                    $ld_total_porcentual = 0;
                    if ($ld_total_programado > 0)
                    {
                            $ld_total_porcentual = (($ld_11_ejecutado)/$ld_total_programado)*100;
                    }
                    $ld_total_asignado           = number_format($ld_11_asignado,2,",",".");
                    $ld_total_modificado         = number_format($ld_11_modificado,2,",",".");
                    $ld_total_programado         = number_format($ld_11_programado,2,",",".");
                    $ld_total_ejecutado          = number_format($ld_11_ejecutado,2,",",".");
                    $ld_total_absoluto           = number_format($ld_total_absoluto,2,",",".");
                    $ld_total_porcentual         = number_format($ld_total_porcentual,2,",",".")." % ";

                    if($ld_total_modificado=='0,00')
                    {
                            $ld_total_modificado='';
                    }
                    $la_data_tot[1]=array('cuenta'=>"",
                                                              'denominacion'=>"SUPERAVIT FINANCIERO        ",
                                                              'asignado'=>$ld_total_asignado,
                                                              'modificado'=>$ld_total_modificado,
                                                              'programado'=>$ld_total_programado,
                                                              'ejecutado'=>$ld_total_ejecutado,
                                                              'absoluto'=>$ld_total_absoluto,
                                                              'porcentual'=>$ld_total_porcentual);
                
                    uf_print_detalle($li_row,$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$lo_dataleftb,$lo_datarightb,$la_data); // Imprimimos el detalle 
                    uf_print_pie_cabecera($li_row,$lo_titulo,$lo_hoja,$lo_datacenterb,$lo_dataleftb,$lo_datarightb,$la_data_tot);
                    unset($la_data);
                    unset($la_data_tot);
                }

                $lb_valido=$io_report->uf_scg_reportes_comparados_0206($ldt_fecdes,$ldt_fechas,$li_mesdes,$li_meshas,$ls_cant_mes,4);
                if($lb_valido==false) // Existe algún error ó no hay registros
                {
                       print("<script language=JavaScript>");
                       print(" alert('No hay nada que Reportar');"); 
                       print(" close();");
                       print("</script>");
                }
                else // Imprimimos el reporte
                {
                    $li_tot=$io_report->dts_reporte->getRowCount("sc_cuenta");
                    $ld_total_asignado=0;
                    $ld_total_modificado=0;
                    $ld_total_programado=0;
                    $ld_total_ejecutado=0;
                    $ld_total_absoluto=0;
                    $ld_total_porcentual=0;
                    $ld_11_asignado         = 0;
                    $ld_11_modificado       = 0;
                    $ld_11_programado       = 0;
                    $ld_11_ejecutado        = 0;
                    $ld_211_asignado         = 0;
                    $ld_211_modificado       = 0;
                    $ld_211_programado       = 0;
                    $ld_211_ejecutado        = 0;
                    $espacio=true;
                    $li_contador=1;
                    $la_data[$li_contador]=array('cuenta'=>"",
                                                     'denominacion'=>"",
                                                     'asignado'=>"",
                                                     'modificado'=>"",
                                                     'programado'=>"",
                                                     'ejecutado'=>"",
                                                     'absoluto'=>"",
                                                     'porcentual'=>"");
                    $li_contador++;
                    $la_data[$li_contador]=array('cuenta'=>"",
                                                     'denominacion'=>"3.2 APLICACIONES FINANCIERAS",
                                                     'asignado'=>"",
                                                     'modificado'=>"",
                                                     'programado'=>"",
                                                     'ejecutado'=>"",
                                                     'absoluto'=>"",
                                                     'porcentual'=>"");
                
                    for($z=1;$z<=$li_tot;$z++)
                    {		
                            $ls_cuenta          =  $io_report->dts_reporte->data["sc_cuenta"][$z];
                            $ls_denominacion    =  $io_report->dts_reporte->data["denominacion"][$z];
                            $ls_status          =  $io_report->dts_reporte->data["status"][$z];
                            $ld_asignado        =  $io_report->dts_reporte->data["monto_asignado"][$z];
                            $ld_modificado      =  $io_report->dts_reporte->data["monto_modificado"][$z];
                            $ld_programado      =  $io_report->dts_reporte->data["programado_acumulado"][$z];
                            $ld_ejecutado       =  $io_report->dts_reporte->data["ejecutado_acumulado"][$z];
                            $ld_absoluto        =  $ld_ejecutado  - $ld_programado;
                            $ld_porcentual      = 0;
                            if ($ld_programado > 0)
                            {
                                $ld_porcentual      =  ($ld_ejecutado/$ld_programado)*100;  
                            }

                            if(($ls_status == "C"))
                            {
                                    if ((substr($ls_cuenta,0,3)=="221") || (substr($ls_cuenta,0,3)=="222") || (substr($ls_cuenta,0,3)=="223") || (substr($ls_cuenta,0,3)=="224"))
                                    {
                                        $ld_11_asignado         = $ld_11_asignado + $ld_asignado;
                                        $ld_11_modificado       = $ld_11_modificado + $ld_modificado;
                                        $ld_11_programado       = $ld_11_programado + $ld_programado;
                                        $ld_11_ejecutado        = $ld_11_ejecutado + $ld_ejecutado;
                                    }
                            }
                            $ld_asignado               = number_format($ld_asignado,2,",",".");
                            $ld_modificado             = number_format($ld_modificado,2,",",".");
                            $ld_programado             = number_format($ld_programado,2,",",".");
                            $ld_ejecutado              = number_format($ld_ejecutado,2,",",".");
                            $ld_absoluto               = number_format($ld_absoluto,2,",",".");
                            $ld_porcentual             = number_format($ld_porcentual,2,",",".")." % ";

                            if($ld_modificado=='0,00')
                            {
                                    $ld_modificado='';
                            }
                            $li_contador++;
                            $la_data[$li_contador]=array('cuenta'=>uf_formato_cuenta_instructivo(trim($ls_cuenta)),
                                                             'denominacion'=>$ls_denominacion,
                                                             'asignado'=>$ld_asignado,
                                                             'modificado'=>$ld_modificado,
                                                             'programado'=>$ld_programado,
                                                             'ejecutado'=>$ld_ejecutado,
                                                             'absoluto'=>$ld_absoluto,
                                                             'porcentual'=>$ld_porcentual);


                    }//for
                    $ld_total_absoluto           = ($ld_11_ejecutado - $ld_11_programado);
                    $ld_total_programado = $ld_11_programado;
                    $ld_total_porcentual = 0;
                    if ($ld_total_programado > 0)
                    {
                            $ld_total_porcentual = (($ld_11_ejecutado)/$ld_total_programado)*100;
                    }
                    $ld_total_asignado           = number_format($ld_11_asignado,2,",",".");
                    $ld_total_modificado         = number_format($ld_11_modificado,2,",",".");
                    $ld_total_programado         = number_format($ld_11_programado,2,",",".");
                    $ld_total_ejecutado          = number_format($ld_11_ejecutado,2,",",".");
                    $ld_total_absoluto           = number_format($ld_total_absoluto,2,",",".");
                    $ld_total_porcentual         = number_format($ld_total_porcentual,2,",",".")." % ";

                    if($ld_total_modificado=='0,00')
                    {
                            $ld_total_modificado='';
                    }
                    $la_data_tot[1]=array('cuenta'=>"",
                                                              'denominacion'=>"DEFICIT FINANCIERO        ",
                                                              'asignado'=>$ld_total_asignado,
                                                              'modificado'=>$ld_total_modificado,
                                                              'programado'=>$ld_total_programado,
                                                              'ejecutado'=>$ld_total_ejecutado,
                                                              'absoluto'=>$ld_total_absoluto,
                                                              'porcentual'=>$ld_total_porcentual);

                    uf_print_detalle($li_row,$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$lo_dataleftb,$lo_datarightb,$la_data); // Imprimimos el detalle 
                    uf_print_pie_cabecera($li_row,$lo_titulo,$lo_hoja,$lo_datacenterb,$lo_dataleftb,$lo_datarightb,$la_data_tot);
                    unset($la_data);
                    unset($la_data_tot);
                }
                
                $lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"resultadoeconomicofinanciero0206.xls\"");
		header("Content-Disposition: inline; filename=\"resultadoeconomicofinanciero0206.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		//unlink($lo_archivo);
	}
	unset($io_report);
	unset($io_funciones);	
?> 	