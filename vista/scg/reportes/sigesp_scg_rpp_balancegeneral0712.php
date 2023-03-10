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
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";		
	}
	ini_set('memory_limit','512M');
	ini_set('max_execution_time','0');
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 22/09/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scg;
		
		$ls_descripcion="Gener? el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_balancegeneral0712.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	require_once("../../../shared/class_folder/class_pdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();
	require_once("../../../shared/class_folder/class_sigesp_int.php");
	require_once("../../../shared/class_folder/class_sigesp_int_scg.php");
	require_once("../../../shared/class_folder/class_sigesp_int_spi.php");
	require_once("../../../shared/class_folder/class_sigesp_int_spg.php");
	require_once("class_funciones_scg.php");
	$io_fun_scg=new class_funciones_scg();
	$ls_tiporeporte="0";
	$ls_bolivares="";
	if (array_key_exists("tiporeporte",$_GET))
	{
		$ls_tiporeporte=$_GET["tiporeporte"];
	}
	switch($ls_tiporeporte)
	{
		case "0":
			require_once("sigesp_scg_class_comparados.php");
			$io_report  = new sigesp_scg_class_comparados();
			$ls_bolivares ="Bolivares";
			break;

		case "1":
			require_once("sigesp_scg_class_comparadosbsf.php");
			$io_report  = new sigesp_scg_class_comparadosbsf();
			$ls_bolivares ="Bolivares Fuerte";
			break;
	}	
	$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	$li_ano=substr($ldt_periodo,0,4);
	$ls_etiqueta=$_GET["etiqueta"];
	if($ls_etiqueta=="Mensual")
	{
		$ls_combo=$_GET["mesdes"];
		$ls_combomes=$_GET["meshas"];
		$li_mesdes=substr($ls_combo,0,2);
		$li_meshas=substr($ls_combomes,0,2); 
		$li_mesdes=intval($li_mesdes);
		$li_meshas=intval($li_meshas); 
		$li_cant_mes=1;
		if($li_meshas==12)
		{
			$io_report->li_mes_prox=0;
		}
		elseif($li_meshas<=11)
		{
			$io_report->li_mes_prox=1;
		}
		$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
		$ls_combo=$ls_combo.$ls_combomes;
	}
	else
	{
		$ls_combo=$_GET["mesdes"];
		$li_mesdes=substr($ls_combo,0,2);
		$li_meshas=substr($ls_combo,2,2); 
		$li_mesdes=intval($li_mesdes);
		$li_meshas=intval($li_meshas); 
		if($ls_etiqueta=="Bimensual")
		{
			$li_cant_mes=2;
			if($li_meshas==12)
			{
				$io_report->li_mes_prox=0;
			}
			elseif($li_meshas<=10)
			{
				$io_report->li_mes_prox=2;
			}
			$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
		}
		if($ls_etiqueta=="Trimestral")
		{
			$li_cant_mes=3;
			if($li_meshas==12)
			{
				$io_report->li_mes_prox=0;
			}
			elseif($li_meshas<=9)
			{
				$io_report->li_mes_prox=3;
			}
			$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
		}
		if($ls_etiqueta=="Semestral")
		{
			$li_cant_mes=6;
			if($li_meshas==12)
			{
				$io_report->li_mes_prox=0;
			}
			elseif($li_meshas<=6)
			{
				$io_report->li_mes_prox=6;
			}
			$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
		}
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo1,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_etiqueta;
		global $ls_meses;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,720,40);
		$io_pdf->rectangle(40,510,920,80);
		$io_pdf->addText(45,580,9,"OFICINA NACIONAL DE PRESUPUESTO(ONAPRE)"); // Agregar el t?tulo
		$io_pdf->addText(45,570,9,"OFICINA DE PLANIFICACION DEL SECTOR UNIVERSITARIO(OPSU)"); // Agregar el t?tulo

		$li_tm=$io_pdf->getTextWidth(10,$_SESSION["la_empresa"]["nombre"]);
		$tm=500-($li_tm/2);
		$io_pdf->addText($tm,550,10,$_SESSION["la_empresa"]["nombre"]); // Agregar el t?tulo
		
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo1);
		$tm=500-($li_tm/2);
		$io_pdf->addText($tm,540,10,$as_titulo1); // Agregar el t?tulo

		$io_pdf->addText(870,580,7,$_SESSION["ls_database"]); // Agregar la Base de datos
		$io_pdf->addText(870,570,9,"Fecha:  ".date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(870,560,9,"Hora:    ".date("h:i a")); // Agregar la hora
		$io_pdf->addText(45,525,8,"PRESUPUESTO A?O:  ".substr($_SESSION["la_empresa"]["periodo"],0,4)); // Agregar la Fecha
		$io_pdf->addText(45,515,8,strtoupper($ls_etiqueta).": "); // Agregar la hora
		$io_pdf->addText(105,515,8,$ls_meses); // Agregar la hora
		$io_pdf->line(105,514,165,514);
		$io_pdf->line(125,524,165,524);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_2=$io_pdf->openObject();
		$io_pdf->saveState();
		$li_pos=180;
		$io_pdf->convertir_valor_mm_px($li_pos);
		$io_pdf->ezSetY($li_pos);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>450,
						 'cols'=>array('cuentas'=>array('justification'=>'center','width'=>200), // Justificaci?n y ancho de la columna
						 			   'programado'=>array('justification'=>'center','width'=>200), // Justificaci?n y ancho de la columna
						 			   'real'=>array('justification'=>'center','width'=>200), // Justificaci?n y ancho de la columna
						 			   'variacion'=>array('justification'=>'center','width'=>220))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuentas'=>' ',
						   'programado'=>' ',
						   'real'=>' ',
						   'variacion'=>' ');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_2,'all');
	}// end function uf_print_detalle
	//-------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_reprog($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_3=$io_pdf->openObject();
		$io_pdf->saveState();
		$li_pos=180;
		$io_pdf->convertir_valor_mm_px($li_pos);
		$io_pdf->ezSetY(504);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>120, // Ancho de la tabla
						 'maxWidth'=>120, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>910,
						 'cols'=>array('cuentas'=>array('justification'=>'center','width'=>100))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuentas'=>' ');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_3,'all');
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado2($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_4=$io_pdf->openObject();
		$io_pdf->saveState();
		$li_pos=175.3;
		$io_pdf->convertir_valor_mm_px($li_pos);	
		$io_pdf->ezSetY($li_pos+12);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>820, // Ancho de la tabla
						 'maxWidth'=>820, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>275,
						 'cols'=>array('cuentas'=>array('justification'=>'center','width'=>80), // Justificaci?n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'center','width'=>110), // Justificaci?n y ancho de la columna
									   'saldo_real_ant'=>array('justification'=>'center','width'=>70), // Justificaci?n y ancho de la columna
									   'saldo_apro'=>array('justification'=>'center','width'=>70), // Justificaci?n y ancho de la columna
									   'saldo_mod'=>array('justification'=>'center','width'=>70) )); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuentas'=>' ','denominacion'=>' ' ,'saldo_real_ant'=>' ','saldo_apro'=>' ','saldo_mod'=>' ');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_4,'all');
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------
		function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$li_pos=166;
		$io_pdf->convertir_valor_mm_px($li_pos);	//'variacion_absoluta','variacion_porcentual','variacion_saldos');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>820, // Ancho de la tabla
						 'maxWidth'=>820, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>495,
						 'cols'=>array('cuentas_sal'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>110), // Justificaci?n y ancho de la columna
									   'saldo_real_ant'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
									   'saldo_apro'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
									   'saldo_mod'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
						 			   'programado'=>array('justification'=>'right','width'=>90), // Justificaci?n y ancho de la columna
						 			   'saldo_ant'=>array('justification'=>'right','width'=>90), // Justificaci?n y ancho de la columna
						 			   'variacion_absoluta'=>array('justification'=>'right','width'=>90),
									   'variacion_porcentual'=>array('justification'=>'right','width'=>90),
									   'variacion_saldos'=>array('justification'=>'right','width'=>80))); 
		$la_columnas=array('cuentas_sal'=>' ','denominacion'=>' ' ,'saldo_real_ant'=>' ','saldo_apro'=>' ','saldo_mod'=>' ','programado'=>' ',
							'saldo_ant'=>' ','variacion_absoluta'=>' ','variacion_porcentual'=>' ','variacion_saldos'=>' ');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_print_absolutos($ls_etiqueta,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_5=$io_pdf->openObject();
		$io_pdf->saveState();
		$li_pos=175.3;
		$io_pdf->convertir_valor_mm_px($li_pos);
		$io_pdf->ezSetY($li_pos+12);
		$la_data[1]=array('absoluta1'=>'     ','absoluta2'=>'   ');
		$la_data[2]=array('absoluta1'=>strtoupper($ls_etiqueta),'absoluta2'=>'Variaci?n Saldo Ejecutado-Saldo Programado');			
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>360, // Ancho de la tabla
						 'maxWidth'=>360, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>655,
						 'cols'=>array('absoluta1'=>array('justification'=>'center','width'=>180), // Justificaci?n y ancho de la columna
						 			   'absoluta2'=>array('justification'=>'center','width'=>180))); // Justificaci?n y ancho de la columna
		$la_columnas=array('absoluta1'=>'  ','absoluta2'=>'  ' );
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		
		unset($la_data);
		
		$la_data[1]=array('absoluta1'=>'Saldo Programado','porc1'=>'Saldo Ejecutado','absoluta2'=>'  Absoluta ','porc2'=>' Porcentual ' );			
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>360, // Ancho de la tabla
						 'maxWidth'=>360, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>655,
						 'rowGap' => 4.5,
						 'cols'=>array('absoluta1'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
						 			   'porc1'=>array('justification'=>'center','width'=>90),
						 			   'absoluta2'=>array('justification'=>'center','width'=>90),
									   'porc2'=>array('justification'=>'center','width'=>90))); // Justificaci?n y ancho de la columna
		$la_columnas=array('absoluta1'=>'  ','porc1'=>' ' ,'absoluta2'=>'  ','porc2'=>'  ' );
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		
		unset($la_data);
		$io_pdf->ezSetY($li_pos+12);
		$la_data[1]=array('absoluta1'=>'Var. Saldo Ejecutado Per?odo N, menos Saldo Periodo N-1 ' );			
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>300, // Ancho de la tabla
						 'maxWidth'=>300, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>875,
						 'rowGap' => 8.5,
						 'cols'=>array('absoluta1'=>array('justification'=>'center','width'=>80))); // Justificaci?n y ancho de la columna
		$la_columnas=array('absoluta1'=>'  ');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);		
		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_5,'all');
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ld_total,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_totaldebe // Total debe
		//    Description : funci?n que imprime el fin de la cabecera de cada p?gina
		//	   Creado Por : Ing. Yozelin Barragan
		// Fecha Creaci?n : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('total'=>'<b>Total Pasivo + Capital + Resultado del Ejercicio</b>','totalgen'=>$ld_total));
		$la_columna=array('total'=>'','totalgen'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' => 9, // Tama?o de Letras
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>530, // Ancho M?ximo de la tabla
						 'colGap'=>1, // separacion entre tablas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>290), // Justificaci?n y ancho de la columna
						 			   'totalgen'=>array('justification'=>'right','width'=>240))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
    {    ///////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_init_niveles
        //         Access: public
        //        Returns: vacio     
        //    Description: Este m?todo realiza una consulta a los formatos de las cuentas
        //               para conocer los niveles de la escalera de las cuentas contables  
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        global $io_funciones,$ia_niveles_scg;
        
        $ls_formato=""; $li_posicion=0; $li_indice=0;
        $dat_emp=$_SESSION["la_empresa"];
        //contable
        $ls_formato = trim($dat_emp["formcont"])."-";
        //print "ls_formato : $ls_formato <br>";
        $li_posicion = 1 ;
        $li_indice   = 1 ;
        $li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
        do
        {
            $ia_niveles_scg[$li_indice] = $li_posicion;
            $li_indice   = $li_indice+1;
            $li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
            //print "pos: $li_posicion   <br>";
        } while ($li_posicion>=0);
        //var_dump($ia_niveles_scg);
    }// end function uf_init_niveles
	//--------------------------------------------------------------------------------------------------------------------------------     
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
		uf_init_niveles();

		$ls_etiqueta=$_GET["etiqueta"];
		if($ls_etiqueta=="Mensual")
		{
			$ls_combo=$_GET["mesdes"];
			$ls_combomes=$_GET["meshas"];
			$li_mesdes=substr($ls_combo,0,2);
			$li_meshas=substr($ls_combomes,0,2); 
			$li_mesdes=intval($li_mesdes);
			$li_meshas=intval($li_meshas); 
			$ls_cant_mes=1;
			$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			$ls_combo=$ls_combo.$ls_combomes;
		}
		else
		{
			$ls_combo=$_GET["mesdes"];
			$li_mesdes=substr($ls_combo,0,2);
			$li_meshas=substr($ls_combo,2,2); 
			$li_mesdes=intval($li_mesdes);
			$li_meshas=intval($li_meshas); 
			if($ls_etiqueta=="Bimensual")
			{
				$ls_cant_mes=2;
				$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			}
			if($ls_etiqueta=="Trimestral")
			{
				$ls_cant_mes=3;
				$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			}
			if($ls_etiqueta=="Semestral")
			{
				$ls_cant_mes=6;
				$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			}
		}
		$ls_mesdes=substr($ls_combo,0,2);
		$ls_meshas=substr($ls_combo,2,2);
		$ls_diades="01";
		$ls_diahas=$io_fecha->uf_last_day($ls_meshas,$li_ano);
		$ldt_fecdes=$ls_diades."/".$ls_mesdes."/".$li_ano;
		$ldt_fechas=$ls_diahas;
		$ld_fechas=$io_funciones->uf_convertirfecmostrar($ldt_fechas);
		$ls_titulo="<b>COMPARADO BALANCE GENERAL FORMA 0718</b>";
		$ls_titulo1="<b>".$ls_titulo."  (En ".$ls_bolivares.")   al  </b>"."<b>".$ld_fechas."</b>";
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar datastore con los datos del reporte
	$lb_valido=uf_insert_seguridad("<b>Instructivo 07 Comparado Balance General</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_balance_general_comparado($ldt_fecdes,$ldt_fechas,$ls_cant_mes); 
	}
		if($lb_valido==false) // Existe alg?n error ? no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}	
		else// Imprimimos el reporte
		{
			$io_pdf=new class_pdf('LEGAL','landscape'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(5.5,3,3,3); // Configuraci?n de los margenes en cent?metros
			uf_print_encabezado_pagina($ls_titulo,$ls_titulo1,$io_pdf); // Imprimimos el encabezado de la p?gina
			$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n?mero de p?gina
			$li_tot=$io_report->ds_cuentas->getRowCount("sc_cuenta");
            $ld_saldo4="";
		    $ld_saldo3="";  
		    $ld_saldo2="";
			$ld_total=0;
			
			$la_data[1]=array('cuentas'=>'REPROGRAMACION');			
			$la_data[2]=array('cuentas'=>'PROXIMO'       );			
			$la_data[3]=array('cuentas'=>strtoupper($ls_etiqueta));			

			unset($la_data);
			
			$la_data[1]=array('cuentas'=>'           ','denominacion'=>'     ',  'saldo_real_ant'=>'','saldo_apro'=>'','saldo_mod'=>'');	//,'periodo1'=>'     ','saldo_ant'=>'        ','variacion_absoluta'=> '           ','variacion_porcentual'=>'      ','variacion_saldos'=>'      '			
			$la_data[2]=array('cuentas'=>'Codigo','denominacion'=>'Denominacion','saldo_real_ant'=>'Saldo Presupuesto Real A?o Anterior','saldo_apro'=>'Saldo Presupuesto Aprobado','saldo_mod'=>'Saldo Presupuesto Modificado');

			
			uf_print_encabezado2($la_data,$io_pdf);
			uf_print_absolutos($ls_etiqueta,$io_pdf);
			$dec_var_absoluta = 0;
			for($li_i=1;$li_i<=$li_tot;$li_i++)
			{
				$io_pdf->transaction('start'); // Iniciamos la transacci?n
				$thisPageNum=$io_pdf->ezPageCount;
				$ls_cuenta       		= $io_report->ds_cuentas->getValue("sc_cuenta",$li_i);	
				$ls_denominacion 		= htmlentities($io_report->ds_cuentas->getValue("denominacion",$li_i));	
				$ls_tipo         		= $io_report->ds_cuentas->getValue("tipo",$li_i);	
				$ldec_prog_varia 		= $io_report->ds_cuentas->getValue("prg_varia",$li_i);	
				$ldec_prog_acum  		= $io_report->ds_cuentas->getValue("prg_acum",$li_i);	
				$ldec_s_ant      		= $io_report->ds_cuentas->getValue("s_ant",$li_i);	
				$ldec_saldo_ant  		= $io_report->ds_cuentas->getValue("saldo_ant",$li_i);	
				$ldec_repproxmes 		= $io_report->ds_cuentas->getValue("repproxmes",$li_i);
				$ldec_s1         		= $io_report->ds_cuentas->getValue("s_1",$li_i);
				$ldec_s2         		= $io_report->ds_cuentas->getValue("s_2",$li_i);
				$ldec_s3         		= $io_report->ds_cuentas->getValue("s_3",$li_i);
				$ldec_s4         		= $io_report->ds_cuentas->getValue("s_4",$li_i);
				$ldec_s5         		= $io_report->ds_cuentas->getValue("s_5",$li_i);
				$ldec_s6         		= $io_report->ds_cuentas->getValue("s_6",$li_i);																
				$ldec_s7         		= $io_report->ds_cuentas->getValue("s_7",$li_i);
				$ls_nivel        		= $io_report->ds_cuentas->getValue("nivel",$li_i);
				$ldec_var_prog   		= $io_report->ds_cuentas->getValue("var_prog",$li_i);
				$ldec_var_ejec   		= $io_report->ds_cuentas->getValue("var_ejec",$li_i);
				$ldec_porc1      		= $io_report->ds_cuentas->getValue("p1",$li_i);
				$ldec_porc2      		= $io_report->ds_cuentas->getValue("p2",$li_i);
				$ls_cuenta_sal   		= htmlentities($io_report->ds_cuentas->getValue("sc_cuenta_salida",$li_i).'   ');
				$ldec_saldo_real_ant  	= $io_report->ds_cuentas->getValue("saldo_real_ant",$li_i);
				$ldec_saldo_apro      	= $io_report->ds_cuentas->getValue("saldo_apro",$li_i);
				$ldec_saldo_mod       	= $io_report->ds_cuentas->getValue("saldo_mod",$li_i);
				if($ls_tipo=='999')
				{
					$ldec_periodo=$ldec_s1;
					$ldec_acumulado=$ldec_s2;
					$ldec_periodo2=$ldec_s3;
					$ldec_acumulado2=$ldec_s4;
					$ldec_absoluta1=$ldec_s5;
					$ldec_absoluta2=$ldec_s6;
					$ldec_reprog=$ldec_s7;
				}
				else
				{
					$ldec_saldo_periodo_anterior = $ldec_saldo_ant;
					$ldec_periodo=$ldec_prog_varia;
					$ldec_acumulado=$ldec_prog_acum;
					$ldec_periodo2=$ldec_s_ant+$ldec_saldo_ant;
					$ldec_acumulado2=$ldec_s_ant+$ldec_saldo_ant;
					$ldec_absoluta1=$ldec_var_prog;
					$ldec_absoluta2=$ldec_var_ejec;
					$ldec_reprog=$ldec_repproxmes;
					$dec_var_absoluta = $ldec_saldo_ant - $ldec_prog_varia;	
					if ($ldec_saldo_ant==0)
					{
						$dec_var_porc = 0;
					}	
					else
					{	
						$dec_var_porc = ($ldec_prog_varia*100)/$ldec_saldo_ant;
					}					
				}
				if($dec_var_absoluta<0)
				{	
					$dec_var_absoluta="(".number_format(abs($dec_var_absoluta),2,",",".").")";
				}
				else
				{	
					$dec_var_absoluta = doubleval($dec_var_absoluta);
					$dec_var_absoluta=number_format($dec_var_absoluta,2,",",".");
				}
				if($ldec_saldo_real_ant=="")
				{$ldec_saldo_real_ant=0;}				
				if($ldec_saldo_real_ant<0)
				{	
					$ldec_saldo_real_ant="(".number_format(abs($ldec_saldo_real_ant),2,",",".").")";
				}
				else
				{		
					$ldec_saldo_real_ant = doubleval($ldec_saldo_real_ant);
					$ldec_saldo_real_ant=number_format($ldec_saldo_real_ant,2,",",".");
				}
				if($ldec_saldo_apro=="")
				{$ldec_saldo_apro=0;}				
				if($ldec_saldo_apro<0)
				{	
					$ldec_saldo_apro="(".number_format(abs($ldec_saldo_apro),2,",",".").")";
				}
				else
				{	
					$ldec_saldo_apro = doubleval($ldec_saldo_apro);
					$ldec_saldo_apro=number_format($ldec_saldo_apro,2,",",".");
				}
				if($ldec_saldo_mod=="")
				{$ldec_saldo_mod=0;}				
				if($ldec_saldo_mod<0)
				{	
					$ldec_saldo_mod="(".number_format(abs($ldec_saldo_mod),2,",",".").")";	
				}
				else
				{	$ldec_saldo_mod = doubleval($ldec_saldo_mod);
					$ldec_saldo_mod=number_format($ldec_saldo_mod,2,",",".");
				}		
				if($ldec_periodo=="")
				{$ldec_periodo=0;}				
				if($ldec_periodo<0)
				{	
					$ldec_periodo="(".number_format(abs($ldec_periodo),2,",",".").")";
				}
				else
				{	
					$ldec_periodo = doubleval($ldec_periodo);
					$ldec_periodo=number_format($ldec_periodo,2,",",".");
				}		
				if($ldec_acumulado=="")
				{$ldec_acumulado=0;}				
				if($ldec_acumulado<0)
				{	
					$ldec_acumulado="(".number_format(abs($ldec_acumulado),2,",",".").")";
				}
				else
				{  
					$ldec_acumulado = doubleval($ldec_acumulado);
					$ldec_acumulado=number_format($ldec_acumulado,2,",","."); 
				}
				if($ldec_periodo2=="")
				{$ldec_periodo2=0;}				
				if($ldec_periodo2<0)
				{	
					$ldec_periodo2="(".number_format(abs($ldec_periodo2),2,",",".").")";
				}
				else
				{   
					$ldec_periodo2 = doubleval($ldec_periodo2);
					$ldec_periodo2=number_format($ldec_periodo2,2,",",".");	
				}
				if($ldec_acumulado2=="")
				{$ldec_acumulado2=0;}				
				if($ldec_acumulado2<0)
				{	
					$ldec_acumulado2="(".number_format(abs($ldec_acumulado2),2,",",".").")";
				}
				else
				{
					$ldec_acumulado2 = doubleval($ldec_acumulado2); 
				 	$ldec_acumulado2=number_format($ldec_acumulado2,2,",",".");
				}	
							
				$ldec_absoluta1=number_format(abs($ldec_absoluta1),2,",",".");	
				$ldec_absoluta2=number_format(abs($ldec_absoluta2),2,",",".");
				
				$ldec_var_saldos = $ldec_s_ant; 

				if($ldec_var_saldos=="")
				{$ldec_var_saldos=0;}				
				if($ldec_var_saldos<0)
				{	
					$ldec_var_saldos="(".number_format(abs($ldec_var_saldos),2,",",".").")";
				}
				else
				{
					$ldec_var_saldos = doubleval($ldec_var_saldos);  
				 	$ldec_var_saldos=number_format($ldec_var_saldos,2,",","."); 
				}
				if($ldec_prog_varia=="")
				{$ldec_prog_varia=0;}				
				if($ldec_prog_varia<0)
				{	
					$ldec_prog_varia="(".number_format(abs($ldec_prog_varia),2,",",".").")";
				}
				else
				{    
					$ldec_prog_varia = doubleval($ldec_prog_varia);
					$ldec_prog_varia=number_format($ldec_prog_varia,2,",","."); 
				}
				if($ldec_saldo_ant=="")
				{$ldec_saldo_ant=0;}				
				if($ldec_saldo_ant<0)
				{	
					$ldec_saldo_ant="(".number_format(abs($ldec_saldo_ant),2,",",".").")";
				}
				else
				{ 
					$ldec_saldo_ant = doubleval($ldec_saldo_ant);
					$ldec_saldo_ant=number_format($ldec_saldo_ant,2,",","."); 
				}
				if($dec_var_porc=="")
				{$dec_var_porc=0;}				
				if($dec_var_porc<0)
				{	
					$dec_var_porc="(".number_format(abs($dec_var_porc),2,",",".").")";
				}
				else
				{   
					$dec_var_porc = doubleval($dec_var_porc);
					$dec_var_porc=number_format($dec_var_porc,2,",",".");
				}
				$ldec_saldo_real_ant = str_replace('.','',$ldec_saldo_real_ant);
				$ldec_saldo_real_ant = str_replace(',','.',$ldec_saldo_real_ant);
				$la_data[$li_i]=array('cuentas'=>$ls_cuenta,
										'denominacion'=>$ls_denominacion ,
										'periodo1'=>$ldec_periodo,
							  			'acumulado1'=>$ldec_acumulado,
							  			'periodo2'=>$ldec_periodo2,
										'acumulado2'=>$ldec_acumulado2,
										'absoluta1'=>$ldec_absoluta1,
										'porc1'=>number_format($ldec_porc1,2,",","."),
										'absoluta2'=>$ldec_absoluta2,
										'porc2'=>number_format($ldec_porc2,2,",","."),
										'reprox'=>number_format($ldec_reprog,2,",","."),
										'cuentas_sal'=>$ls_cuenta_sal,
										'saldo_real_ant'=>'',//number_format($ldec_saldo_real_ant,2,",","."),
										'saldo_apro'=>'',//$ldec_saldo_apro,
										'saldo_mod'=>'',//$ldec_saldo_mod,
										'saldo_ant'=>'',//$ldec_saldo_ant,
										'programado'=>'',//$ldec_prog_varia,
										'variacion_absoluta'=>'',//$dec_var_absoluta,
										'variacion_porcentual'=>'',//$dec_var_porc,
										'variacion_saldos'=>$ldec_var_saldos);
										//print $ls_denominacion.'<br>';
			
			}//for
			uf_print_detalle($la_data,$io_pdf);
			unset($la_data);		
			$io_pdf->ezStopPageNumbers(1,1);
			if (isset($d) && $d)
			{
				/*$ls_pdfcode = $io_pdf->ezOutput(1);
				$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
				echo '<html><body>';
				echo trim($ls_pdfcode);
				echo '</body></html>';*/
			}
			else
			{
				$io_pdf->ezStream();
			}
			unset($io_pdf);
		 }//else
		unset($io_report);
	    unset($io_funciones);			
?> 