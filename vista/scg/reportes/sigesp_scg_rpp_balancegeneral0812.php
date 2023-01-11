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
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/09/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scg;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_balancegeneral0812.html",$ls_descripcion);
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
	require_once("sigesp_scg_class_comparados.php");
	$io_report  = new sigesp_scg_class_comparados();
	$ls_bolivares ="Bolivares";
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
		$ls_etiqueta = "Mes : ".$ls_meses;
	}
	else
	{
		$ls_combo=$_GET["mesdes"];
		$li_mesdes=substr($ls_combo,0,2);
		$li_meshas=substr($ls_combo,2,2); 
		$li_mesdes=intval($li_mesdes);
		$li_meshas=intval($li_meshas); 
		if($ls_etiqueta=="Bimestral")
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
			$ls_etiqueta = "Bimestre : ".$ls_meses;
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
			$ls_etiqueta = "Trimestre : ".$ls_meses;
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
			$ls_etiqueta = "Semestre : ".$ls_meses;
		}
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo1,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_etiqueta;
		global $ls_meses;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->rectangle(74,510,838,80);
		$io_pdf->addText(79,580,9,"<b>CODIGO PRESUPUESTARIO DEL ENTE: </b>".$_SESSION["la_empresa"]["codasiona"]); // Agregar el título
		$io_pdf->addText(79,565,9,"<b>DENOMINACION DEL ENTE: </b>".$_SESSION["la_empresa"]["nombre"]); // Agregar el título
		$io_pdf->addText(79,550,9,"<b>ORGANO DE ADSCRIPCION: </b>".$_SESSION["la_empresa"]["nomorgads"]); // Agregar el título
		$io_pdf->addText(79,535,9,"<b>PERIODO: </b>".strtoupper($as_periodo)); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo);
		$tm=500-($li_tm/2);
		$io_pdf->addText($tm,530,10,$as_titulo); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo1);
		$tm=500-($li_tm/2);
		$io_pdf->addText($tm,520,10,$as_titulo1); // Agregar el título

		$io_pdf->addText(800,580,7,$_SESSION["ls_database"]); // Agregar la Base de datos
		$io_pdf->addText(800,570,9,"Fecha:  ".date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(800,560,9,"Hora:    ".date("h:i a")); // Agregar la hora
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
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_2=$io_pdf->openObject();
		$io_pdf->saveState();
		$li_pos=180;
		$io_pdf->convertir_valor_mm_px($li_pos);
		$io_pdf->ezSetY($li_pos);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>450,
						 'cols'=>array('cuentas'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
						 			   'programado'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
						 			   'real'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
						 			   'variacion'=>array('justification'=>'center','width'=>220))); // Justificación y ancho de la columna
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
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_3=$io_pdf->openObject();
		$io_pdf->saveState();
		$li_pos=180;
		$io_pdf->convertir_valor_mm_px($li_pos);
		$io_pdf->ezSetY(504);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>120, // Ancho de la tabla
						 'maxWidth'=>120, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>910,
						 'cols'=>array('cuentas'=>array('justification'=>'center','width'=>100))); // Justificación y ancho de la columna
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
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_4=$io_pdf->openObject();
		$io_pdf->saveState();
		$li_pos=175.3;
		$io_pdf->convertir_valor_mm_px($li_pos);	
		$io_pdf->ezSetY($li_pos+12);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>820, // Ancho de la tabla
						 'maxWidth'=>820, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>275,
						 'cols'=>array('cuentas'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'center','width'=>110), // Justificación y ancho de la columna
									   'saldo_real_ant'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'saldo_apro'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'saldo_mod'=>array('justification'=>'center','width'=>70))); // Justificación y ancho de la columna
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
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$li_pos=166;
		$io_pdf->convertir_valor_mm_px($li_pos);	//'variacion_absoluta','variacion_porcentual','variacion_saldos');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>820, // Ancho de la tabla
						 'maxWidth'=>820, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>495,
						 'cols'=>array('cuentas_sal'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>110), // Justificación y ancho de la columna
									   'saldo_real_ant'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'saldo_apro'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'saldo_mod'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
						 			   'programado'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
						 			   'saldo_ant'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
						 			   'variacion_absoluta'=>array('justification'=>'right','width'=>90),
									   'variacion_porcentual'=>array('justification'=>'right','width'=>90),
									   'variacion_saldos'=>array('justification'=>'right','width'=>80))); 
		$la_columnas=array('cuentas_sal'=>' ',
		                   'denominacion'=>' ' ,
						   'saldo_real_ant'=>' ',
						   'saldo_apro'=>' ',
						   'saldo_mod'=>' ',
						   'programado'=>' ',
						   'saldo_ant'=>' ',
						   'variacion_absoluta'=>' ',
						   'variacion_porcentual'=>' ',
						   'variacion_saldos'=>' ');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_print_absolutos($ls_etiqueta,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_5=$io_pdf->openObject();
		$io_pdf->saveState();
		$li_pos=175.3;
		$io_pdf->convertir_valor_mm_px($li_pos);
		$io_pdf->ezSetY($li_pos+12);
		$la_data[1]=array('absoluta1'=>'     ','absoluta2'=>'   ');
		$la_data[2]=array('absoluta1'=>strtoupper($ls_etiqueta),'absoluta2'=>'Variación Saldo Ejecutado-Saldo Programado');			
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>360, // Ancho de la tabla
						 'maxWidth'=>360, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>655,
						 'cols'=>array('absoluta1'=>array('justification'=>'center','width'=>180), // Justificación y ancho de la columna
						 			   'absoluta2'=>array('justification'=>'center','width'=>180))); // Justificación y ancho de la columna
		$la_columnas=array('absoluta1'=>'  ','absoluta2'=>'  ' );
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		
		unset($la_data);
		
		$la_data[1]=array('absoluta1'=>'Saldo Programado','porc1'=>'Saldo Ejecutado','absoluta2'=>'  Absoluta ','porc2'=>' Porcentual ' );			
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>360, // Ancho de la tabla
						 'maxWidth'=>360, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>655,
						 'rowGap' => 4.5,
						 'cols'=>array('absoluta1'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'porc1'=>array('justification'=>'center','width'=>90),
						 			   'absoluta2'=>array('justification'=>'center','width'=>90),
									   'porc2'=>array('justification'=>'center','width'=>90))); // Justificación y ancho de la columna
		$la_columnas=array('absoluta1'=>'  ','porc1'=>' ' ,'absoluta2'=>'  ','porc2'=>'  ' );
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		
		unset($la_data);
		$io_pdf->ezSetY($li_pos+12);
		$la_data[1]=array('absoluta1'=>'Var. Saldo Ejecutado Período N, menos Saldo Periodo N-1 ' );			
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>300, // Ancho de la tabla
						 'maxWidth'=>300, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>875,
						 'rowGap' => 8.5,
						 'cols'=>array('absoluta1'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
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
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por : Ing. Yozelin Barragan
		// Fecha Creación : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('total'=>'<b>Total Pasivo + Capital + Resultado del Ejercicio</b>','totalgen'=>$ld_total));
		$la_columna=array('total'=>'','totalgen'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'fontSize' => 9, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>530, // Ancho Máximo de la tabla
						 'colGap'=>1, // separacion entre tablas
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>290), // Justificación y ancho de la columna
						 			   'totalgen'=>array('justification'=>'right','width'=>240))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
    {    ///////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_init_niveles
        //         Access: public
        //        Returns: vacio     
        //    Description: Este método realiza una consulta a los formatos de las cuentas
        //               para conocer los niveles de la escalera de las cuentas contables  
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        global $io_funciones,$ia_niveles_scg;
        
        $ls_formato=""; $li_posicion=0; $li_indice=0;
        $dat_emp=$_SESSION["la_empresa"];
        //contable
        $ls_formato = trim($dat_emp["formcont"])."-";
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
	
	function uf_formato_salida($as_cuenta,$ai_nivel,$as_formato,$as_separador)
	{
	    ///////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_formato_salida
        //         Access: public
        //        Returns: vacio     
        //    Description: Este método da formato según lo que estable los instrucivos de la ONAPRE para la cuentas
        //////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_arreglo = explode('-',$as_formato);
        $ls_cuenta = "";
		$j=0;
		$ls_nvoformato = "";
		do
		{
		 if($j<$ai_nivel-1)
		 {
		  $ls_nvoformato .= $la_arreglo[$j].'-';
		 }
		 else
		 {
		  $ls_nvoformato .= $la_arreglo[$j];
		 }
		 $j++;
		}while($j<$ai_nivel);
		
		$la_arreglo_nvo = explode('-',trim($ls_nvoformato));
		$li_total = count((array)$la_arreglo_nvo);
		$ini = 0;
		foreach($la_arreglo_nvo as $key => $valor)
		{
		  if($key <> 0)
		  {
		   $ini += strlen(trim($la_arreglo_nvo[$key-1]));
		  }
		  $len = strlen(trim($valor));
		  if($key<$li_total-1)
		  {
		   $ls_cuenta .= substr(trim($as_cuenta),$ini,$len).$as_separador;
		  }
		  else
		  {
		   $ls_cuenta .= substr(trim($as_cuenta),$ini,$len);
		  }
		}
		
		return $ls_cuenta;
	
	}
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
		uf_init_niveles();
    	$ls_mesdes=substr($ls_combo,0,2);
		$ls_meshas=substr($ls_combo,2,2);
		$ls_diades="01";
		$ls_diahas=$io_fecha->uf_last_day($ls_meshas,$li_ano);
		$ldt_fecdes=$ls_diades."/".$ls_mesdes."/".$li_ano;
		$ldt_fechas=$ls_diahas;
		$ld_fechas=$io_funciones->uf_convertirfecmostrar($ldt_fechas);
		$ls_titulo="<b>BALANCE GENERAL</b>";
		$ls_titulo1="<b> (En ".$ls_bolivares.")</b>";
		$ls_periodo = "Desde el ".$ldt_fecdes." al ".$ld_fechas;
		$ls_formcont =  $_SESSION["la_empresa"]["formcont"];
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar datastore con los datos del reporte
	$lb_valido=uf_insert_seguridad("<b>Instructivo 08 Comparado Balance General</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_balance_general_comparado_ins08($ldt_fecdes,$ldt_fechas,$li_cant_mes); 
	}
		if($lb_valido==false) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}	
		else// Imprimimos el reporte
		{
			set_time_limit(1800);
			$io_pdf=new class_pdf('LEGAL','landscape'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(5.2,3,3,3); // Configuración de los margenes en centímetros
			uf_print_encabezado_pagina($ls_titulo,$ls_titulo1,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la página
			$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
			$li_tot=$io_report->ds_cuentas->getRowCount("sc_cuenta");
            $ld_saldo4="";
		    $ld_saldo3="";  
		    $ld_saldo2="";
			$ld_total=0;
			
			$la_data[1]=array('cuentas'=>'REPROGRAMACION');			
			$la_data[2]=array('cuentas'=>'PROXIMO'       );			
			$la_data[3]=array('cuentas'=>strtoupper($ls_etiqueta));			
			
			unset($la_data);
			
		
			$la_data[1]=array('cuentas'=>'           ','denominacion'=>'     ',  'saldo_real_ant'=>'','saldo_apro'=>'','saldo_mod'=>'');
			$la_data[2]=array('cuentas'=>'Codigo','denominacion'=>'Denominacion','saldo_real_ant'=>'Saldo Presupuesto Real Año Anterior','saldo_apro'=>'Saldo Presupuesto Aprobado','saldo_mod'=>'Saldo Presupuesto Modificado');

			
			uf_print_encabezado2($la_data,$io_pdf);
			uf_print_absolutos($ls_etiqueta,$io_pdf);
			
			// TOTALES PASIVO + PATRIMONIO
 			$ld_total_saldo_real_ant      = 0;
			$ld_total_saldo_apro          = 0;
			$ld_total_saldo_modi          = 0;
			$ld_total_saldo_prog          = 0;
			$ld_total_saldo_ejec          = 0;
			$ld_total_var_saldo_ejec_ant  = 0;
			
			// TOTALES CUENTAS A COBRAR  - CUENTAS NETAS
 			$ld_ctacobnetas_saldo_real_ant      = 0;
			$ld_ctacobnetas_saldo_apro          = 0;
			$ld_ctacobnetas_saldo_modi          = 0;
			$ld_ctacobnetas_saldo_prog          = 0;
			$ld_ctacobnetas_saldo_ejec          = 0;
			$ld_ctacobnetas_var_saldo_ejec_ant  = 0;
			
			// TOTALES CUENTAS ACTIVO FIJO NETO
 			$ld_ctaactfijneto_saldo_real_ant      = 0;
			$ld_ctaactfijneto_saldo_apro          = 0;
			$ld_ctaactfijneto_saldo_modi          = 0;
			$ld_ctaactfijneto_saldo_prog          = 0;
			$ld_ctaactfijneto_saldo_ejec          = 0;
			$ld_ctaactfijneto_var_saldo_ejec_ant  = 0;
			
			// TOTALES CUENTAS ACTIVO INTANGIBLE
 			$ld_ctaactintneto_saldo_real_ant      = 0;
			$ld_ctaactintneto_saldo_apro          = 0;
			$ld_ctaactintneto_saldo_modi          = 0;
			$ld_ctaactintneto_saldo_prog          = 0;
			$ld_ctaactintneto_saldo_ejec          = 0;
			$ld_ctaactintneto_var_saldo_ejec_ant  = 0;
			
			$io_pdf->transaction('start');
			unset($la_data);
            for($li_i=1;$li_i<=$li_tot;$li_i++)
			{
			   $thisPageNum=$io_pdf->ezPageCount;
			   $ls_cuenta       		= $io_report->ds_cuentas->getValue("sc_cuenta",$li_i);	
			   $ls_denominacion 		= $io_report->ds_cuentas->getValue("denominacion",$li_i);
			   $ld_saldo_real_ant       = $io_report->ds_cuentas->getValue("saldo_real_ant",$li_i);
			   $ld_saldo_apro           = $io_report->ds_cuentas->getValue("saldo_apro",$li_i);
			   $ld_saldo_modi           = $io_report->ds_cuentas->getValue("saldo_modi",$li_i);
			   $ld_saldo_prog           = $io_report->ds_cuentas->getValue("saldo_prog",$li_i);
			   $ld_saldo_ejec           = $io_report->ds_cuentas->getValue("saldo_ejec",$li_i);
			   $ld_var_saldo_ejec_ant   = $io_report->ds_cuentas->getValue("var_saldo_ejec_ant",$li_i);
			   $li_nivel                = $io_report->ds_cuentas->getValue("nivel",$li_i);
			   
			   if(empty($ld_saldo_real_ant))
			   {
			    $ld_saldo_real_ant = 0;
			   }
			   
			   if(empty($ld_saldo_apro))
			   {
			    $ld_saldo_apro = 0;
			   }
			   
			   if(empty($ld_saldo_modi))
			   {
			    $ld_saldo_modi = 0;
			   }
			   
			   if(empty($ld_saldo_prog))
			   {
			    $ld_saldo_prog = 0;
			   }
			   
			   if(empty($ld_saldo_ejec))
			   {
			    $ld_saldo_ejec = 0;
			   }
			   
			   if(empty($ld_var_saldo_ejec_ant))
			   {
			    $ld_var_saldo_ejec_ant = 0;
			   }
			   
			   $ld_variacion_absoluta = 0;
			   $ld_variacion_porcentual = 0;
			   
			   if($ld_saldo_prog>0)
			   {
			    $ld_variacion_porcentual = ($ld_saldo_ejec/$ld_saldo_prog)*100;
			   }
			   
			   $ld_variacion_absoluta = abs($ld_saldo_prog - $ld_saldo_ejec);
			   $ls_cuenta_nva = uf_formato_salida( $ls_cuenta,$li_nivel,$ls_formcont,".");
			   
			   if((trim($ls_cuenta_nva) == '2')||(trim($ls_cuenta_nva) == '3')&&($li_nivel == 1))
			   {
			    $ld_total_saldo_real_ant      += $ld_saldo_real_ant;
				$ld_total_saldo_apro          += $ld_saldo_apro;
				$ld_total_saldo_modi          += $ld_saldo_modi;
				$ld_total_saldo_prog          += $ld_saldo_prog;
				$ld_total_saldo_ejec          += $ld_saldo_ejec;
				$ld_total_var_saldo_ejec_ant  += $ld_var_saldo_ejec_ant;
			   
			   }
			   
			   if((trim($ls_cuenta_nva) == '1.1.2.03')||(trim($ls_cuenta_nva) == '2.2.4.01.01'))
			   {
			     switch($ls_cuenta_nva)
				 {
				  case '1.1.2.03' :
				  	$ld_ctacobnetas_saldo_real_ant      += $ld_saldo_real_ant;
					$ld_ctacobnetas_saldo_apro          += $ld_saldo_apro;
					$ld_ctacobnetas_saldo_modi          += $ld_saldo_modi;
					$ld_ctacobnetas_saldo_prog          += $ld_saldo_prog;
					$ld_ctacobnetas_saldo_ejec          += $ld_saldo_ejec;
					$ld_ctacobnetas_var_saldo_ejec_ant  += $ld_var_saldo_ejec_ant;
				  break;
				  
				  case '2.2.4.01.01' :
				    $ld_ctacobnetas_saldo_real_ant      -= $ld_saldo_real_ant;
					$ld_ctacobnetas_saldo_apro          -= $ld_saldo_apro;
					$ld_ctacobnetas_saldo_modi          -= $ld_saldo_modi;
					$ld_ctacobnetas_saldo_prog          -= $ld_saldo_prog;
					$ld_ctacobnetas_saldo_ejec          -= $ld_saldo_ejec;
					$ld_ctacobnetas_var_saldo_ejec_ant  -= $ld_var_saldo_ejec_ant;
					$ls_denominacion = "Menos ".$ls_denominacion;
				  break;
				 }
			   }
			   
			   if((trim($ls_cuenta_nva) == '1.1.3')||(trim($ls_cuenta_nva) == '2.2.5.01'))
			   {
			     switch($ls_cuenta_nva)
				 {
				  case '1.1.3' :
				  	$ld_ctaactfijneto_saldo_real_ant      += $ld_saldo_real_ant;
					$ld_ctaactfijneto_saldo_apro          += $ld_saldo_apro;
					$ld_ctaactfijneto_saldo_modi          += $ld_saldo_modi;
					$ld_ctaactfijneto_saldo_prog          += $ld_saldo_prog;
					$ld_ctaactfijneto_saldo_ejec          += $ld_saldo_ejec;
					$ld_ctaactfijneto_var_saldo_ejec_ant  += $ld_var_saldo_ejec_ant;
				  break;
				  
				  case '2.2.5.01' :
				    $ld_ctaactfijneto_saldo_real_ant      -= $ld_saldo_real_ant;
					$ld_ctaactfijneto_saldo_apro          -= $ld_saldo_apro;
					$ld_ctaactfijneto_saldo_modi          -= $ld_saldo_modi;
					$ld_ctaactfijneto_saldo_prog          -= $ld_saldo_prog;
					$ld_ctaactfijneto_saldo_ejec          -= $ld_saldo_ejec;
					$ld_ctaactfijneto_var_saldo_ejec_ant  -= $ld_var_saldo_ejec_ant;
					$ls_denominacion = "Menos ".$ls_denominacion;
				  break;
				 }
			   }
			   
			   if((trim($ls_cuenta_nva) == '1.2.4')||(trim($ls_cuenta_nva) == '2.2.5.02'))
			   {
			     switch($ls_cuenta_nva)
				 {
				  case '1.2.4' :
				  	$ld_ctaactintneto_saldo_real_ant      += $ld_saldo_real_ant;
					$ld_ctaactintneto_saldo_apro          += $ld_saldo_apro;
					$ld_ctaactintneto_saldo_modi          += $ld_saldo_modi;
					$ld_ctaactintneto_saldo_prog          += $ld_saldo_prog;
					$ld_ctaactintneto_saldo_ejec          += $ld_saldo_ejec;
					$ld_ctaactintneto_var_saldo_ejec_ant  += $ld_var_saldo_ejec_ant;
				  break;
				  
				  case '2.2.5.02' :
				    $ld_ctaactintneto_saldo_real_ant      -= $ld_saldo_real_ant;
					$ld_ctaactintneto_saldo_apro          -= $ld_saldo_apro;
					$ld_ctaactintneto_saldo_modi          -= $ld_saldo_modi;
					$ld_ctaactintneto_saldo_prog          -= $ld_saldo_prog;
					$ld_ctaactintneto_saldo_ejec          -= $ld_saldo_ejec;
					$ld_ctaactintneto_var_saldo_ejec_ant  -= $ld_var_saldo_ejec_ant;
					$ls_denominacion = "Menos ".$ls_denominacion;
				  break;
				 }
			   }
			   
			   if($ld_saldo_real_ant<0)
			   {
			    $ld_saldo_real_ant =  "(".number_format(abs($ld_saldo_real_ant),2,',','.').")";
			   }
			   else
			   {
			    $ld_saldo_real_ant =  number_format($ld_saldo_real_ant,2,',','.');
			   }
			   
			   if($ld_saldo_apro<0)
			   {
			    $ld_saldo_apro =  "(".number_format(abs($ld_saldo_apro),2,',','.').")";
			   }
			   else
			   {
			    $ld_saldo_apro =  number_format($ld_saldo_apro,2,',','.');
			   }
			   
			   if($ld_saldo_modi<0)
			   {
			    $ld_saldo_modi =  "(".number_format(abs($ld_saldo_modi),2,',','.').")";
			   }
			   else
			   {
			    $ld_saldo_modi =  number_format($ld_saldo_modi,2,',','.');
			   }
			   
			   if($ld_saldo_prog<0)
			   {
			    $ld_saldo_prog =  "(".number_format(abs($ld_saldo_prog),2,',','.').")";
			   }
			   else
			   {
			    $ld_saldo_prog =  number_format($ld_saldo_prog,2,',','.');
			   }
			   
			   if($ld_saldo_ejec<0)
			   {
			    $ld_saldo_ejec =  "(".number_format(abs($ld_saldo_ejec),2,',','.').")";
			   }
			   else
			   {
			    $ld_saldo_ejec =  number_format($ld_saldo_ejec,2,',','.');
			   }
			   
			   if($ld_var_saldo_ejec_ant<0)
			   {
			    $ld_var_saldo_ejec_ant =  "(".number_format(abs($ld_var_saldo_ejec_ant),2,',','.').")";
			   }
			   else
			   {
			    $ld_var_saldo_ejec_ant =  number_format($ld_var_saldo_ejec_ant,2,',','.');
			   }
			   
			   $ld_variacion_absoluta =  number_format($ld_variacion_absoluta,2,',','.');
			   $ld_variacion_porcentual = number_format($ld_variacion_porcentual,2,',','.')." %";
			  			
			   $la_data[]=array('cuentas'=>$ls_cuenta,'denominacion'=>$ls_denominacion ,
									 'cuentas_sal'=>$ls_cuenta_nva,
									 'saldo_real_ant'=>$ld_saldo_real_ant,
									 'saldo_apro'=>$ld_saldo_apro,
									 'saldo_mod'=>$ld_saldo_modi,
									 'saldo_ant'=>$ld_saldo_ejec,
									 'programado'=>$ld_saldo_prog,
									 'variacion_absoluta'=>$ld_variacion_absoluta,
									 'variacion_porcentual'=>$ld_variacion_porcentual,
									 'variacion_saldos'=>$ld_var_saldo_ejec_ant);
									 
			 if(trim($ls_cuenta_nva) == '2.2.4.01.01')
			 {
			   $ld_ctacobnetas_variacion_porcentual = 0;
			   $ld_ctacobnetas_variacion_absoluta = 0;
					
			   if($ld_ctacobnetas_saldo_prog>0)
			   {
			    $ld_ctacobnetas_variacion_porcentual = ($ld_ctacobnetas_saldo_ejec/$ld_ctacobnetas_saldo_prog)*100;
			   }
			   
			   $ld_ctacobnetas_variacion_absoluta = abs($ld_ctacobnetas_saldo_prog - $ld_ctacobnetas_saldo_ejec);
			   
			   $ld_ctacobnetas_variacion_absoluta =  number_format($ld_ctacobnetas_variacion_absoluta,2,',','.');
			   $ld_ctacobnetas_variacion_porcentual = number_format($ld_ctacobnetas_variacion_porcentual,2,',','.')." %";
			   
			   if($ld_ctacobnetas_saldo_real_ant<0)
			   {
			    $ld_ctacobnetas_saldo_real_ant =  "(".number_format(abs($ld_ctacobnetas_saldo_real_ant),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctacobnetas_saldo_real_ant =  number_format($ld_ctacobnetas_saldo_real_ant,2,',','.');
			   }
			   
			   if($ld_ctacobnetas_saldo_apro<0)
			   {
			    $ld_ctacobnetas_saldo_apro =  "(".number_format(abs($ld_ctacobnetas_saldo_apro),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctacobnetas_saldo_apro =  number_format($ld_ctacobnetas_saldo_apro,2,',','.');
			   }
			   
			   if($ld_ctacobnetas_saldo_modi<0)
			   {
			    $ld_ctacobnetas_saldo_modi =  "(".number_format(abs($ld_ctacobnetas_saldo_modi),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctacobnetas_saldo_modi =  number_format($ld_ctacobnetas_saldo_modi,2,',','.');
			   }
			   
			   if($ld_ctacobnetas_saldo_prog<0)
			   {
			    $ld_ctacobnetas_saldo_prog =  "(".number_format(abs($ld_ctacobnetas_saldo_prog),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctacobnetas_saldo_prog =  number_format($ld_ctacobnetas_saldo_prog,2,',','.');
			   }
			   
			   if($ld_ctacobnetas_saldo_ejec<0)
			   {
			    $ld_ctacobnetas_saldo_ejec =  "(".number_format(abs($ld_ctacobnetas_saldo_ejec),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctacobnetas_saldo_ejec =  number_format($ld_ctacobnetas_saldo_ejec,2,',','.');
			   }
			   
			   if($ld_ctacobnetas_var_saldo_ejec_ant<0)
			   {
			    $ld_ctacobnetas_var_saldo_ejec_ant =  "(".number_format(abs($ld_ctacobnetas_var_saldo_ejec_ant),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctacobnetas_var_saldo_ejec_ant =  number_format($ld_ctacobnetas_var_saldo_ejec_ant,2,',','.');
			   }

			   $la_data[]=array('cuentas'=>'','denominacion'=>'CUENTAS A COBRAR - COMERCIALES NETAS' ,
									 'cuentas_sal'=>'',
									 'saldo_real_ant'=>$ld_ctacobnetas_saldo_real_ant,
									 'saldo_apro'=>$ld_ctacobnetas_saldo_apro,
									 'saldo_mod'=>$ld_ctacobnetas_saldo_modi,
									 'saldo_ant'=>$ld_ctacobnetas_saldo_ejec,
									 'programado'=>$ld_ctacobnetas_saldo_prog,
									 'variacion_absoluta'=>$ld_ctacobnetas_variacion_absoluta,
									 'variacion_porcentual'=>$ld_ctacobnetas_variacion_porcentual,
									 'variacion_saldos'=>$ld_ctacobnetas_var_saldo_ejec_ant); 
			 }
			 
			 if(trim($ls_cuenta_nva) == '2.2.5.01')
			 {
			   $ld_ctaactfijneto_variacion_porcentual = 0;
			   $ld_ctaactfijneto_variacion_absoluta = 0;
					
			   if($ld_ctaactfijneto_saldo_prog>0)
			   {
			    $ld_ctaactfijneto_variacion_porcentual = ($ld_ctaactfijneto_saldo_ejec/$ld_ctaactfijneto_saldo_prog)*100;
			   }
			   
			   $ld_ctaactfijneto_variacion_absoluta = abs($ld_ctaactfijneto_saldo_prog - $ld_ctaactfijneto_saldo_ejec);
			   
			   $ld_ctaactfijneto_variacion_absoluta =  number_format($ld_ctaactfijneto_variacion_absoluta,2,',','.');
			   $ld_ctaactfijneto_variacion_porcentual = number_format($ld_ctaactfijneto_variacion_porcentual,2,',','.')." %";
			   
			   if($ld_ctaactfijneto_saldo_real_ant<0)
			   {
			    $ld_ctaactfijneto_saldo_real_ant =  "(".number_format(abs($ld_ctaactfijneto_saldo_real_ant),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactfijneto_saldo_real_ant =  number_format($ld_ctaactfijneto_saldo_real_ant,2,',','.');
			   }
			   
			   if($ld_ctaactfijneto_saldo_apro<0)
			   {
			    $ld_ctaactfijneto_saldo_apro =  "(".number_format(abs($ld_ctaactfijneto_saldo_apro),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactfijneto_saldo_apro =  number_format($ld_ctaactfijneto_saldo_apro,2,',','.');
			   }
			   
			   if($ld_ctaactfijneto_saldo_modi<0)
			   {
			    $ld_ctaactfijneto_saldo_modi =  "(".number_format(abs($ld_ctaactfijneto_saldo_modi),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactfijneto_saldo_modi =  number_format($ld_ctaactfijneto_saldo_modi,2,',','.');
			   }
			   
			   if($ld_ctaactfijneto_saldo_prog<0)
			   {
			    $ld_ctaactfijneto_saldo_prog =  "(".number_format(abs($ld_ctaactfijneto_saldo_prog),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactfijneto_saldo_prog =  number_format($ld_ctaactfijneto_saldo_prog,2,',','.');
			   }
			   
			   if($ld_ctaactfijneto_saldo_ejec<0)
			   {
			    $ld_ctaactfijneto_saldo_ejec =  "(".number_format(abs($ld_ctaactfijneto_saldo_ejec),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactfijneto_saldo_ejec =  number_format($ld_ctaactfijneto_saldo_ejec,2,',','.');
			   }
			   
			   if($ld_ctaactfijneto_var_saldo_ejec_ant<0)
			   {
			    $ld_ctaactfijneto_var_saldo_ejec_ant =  "(".number_format(abs($ld_ctaactfijneto_var_saldo_ejec_ant),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactfijneto_var_saldo_ejec_ant =  number_format($ld_ctaactfijneto_var_saldo_ejec_ant,2,',','.');
			   }

			   $la_data[]=array('cuentas'=>'','denominacion'=>'ACTIVO FIJO NETO' ,
									 'cuentas_sal'=>'',
									 'saldo_real_ant'=>$ld_ctaactfijneto_saldo_real_ant,
									 'saldo_apro'=>$ld_ctaactfijneto_saldo_apro,
									 'saldo_mod'=>$ld_ctaactfijneto_saldo_modi,
									 'saldo_ant'=>$ld_ctaactfijneto_saldo_ejec,
									 'programado'=>$ld_ctaactfijneto_saldo_prog,
									 'variacion_absoluta'=>$ld_ctaactfijneto_variacion_absoluta,
									 'variacion_porcentual'=>$ld_ctaactfijneto_variacion_porcentual,
									 'variacion_saldos'=>$ld_ctaactfijneto_var_saldo_ejec_ant); 
			 }
			 
			 if(trim($ls_cuenta_nva) == '2.2.5.02')
			 {
			   $ld_ctaactintneto_variacion_porcentual = 0;
			   $ld_ctaactintneto_variacion_absoluta = 0;
					
			   if($ld_ctaactintneto_saldo_prog>0)
			   {
			    $ld_ctaactintneto_variacion_porcentual = ($ld_ctaactintneto_saldo_ejec/$ld_ctaactintneto_saldo_prog)*100;
			   }
			   
			   $ld_ctaactintneto_variacion_absoluta = abs($ld_ctaactintneto_saldo_prog - $ld_ctaactintneto_saldo_ejec);
			   
			   $ld_ctaactintneto_variacion_absoluta =  number_format($ld_ctaactintneto_variacion_absoluta,2,',','.');
			   $ld_ctaactintneto_variacion_porcentual = number_format($ld_ctaactintneto_variacion_porcentual,2,',','.')." %";
			   
			   if($ld_ctaactintneto_saldo_real_ant<0)
			   {
			    $ld_ctaactintneto_saldo_real_ant =  "(".number_format(abs($ld_ctaactintneto_saldo_real_ant),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactintneto_saldo_real_ant =  number_format($ld_ctaactintneto_saldo_real_ant,2,',','.');
			   }
			   
			   if($ld_ctaactintneto_saldo_apro<0)
			   {
			    $ld_ctaactintneto_saldo_apro =  "(".number_format(abs($ld_ctaactintneto_saldo_apro),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactintneto_saldo_apro =  number_format($ld_ctaactintneto_saldo_apro,2,',','.');
			   }
			   
			   if($ld_ctaactintneto_saldo_modi<0)
			   {
			    $ld_ctaactintneto_saldo_modi =  "(".number_format(abs($ld_ctaactintneto_saldo_modi),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactintneto_saldo_modi =  number_format($ld_ctaactintneto_saldo_modi,2,',','.');
			   }
			   
			   if($ld_ctaactintneto_saldo_prog<0)
			   {
			    $ld_ctaactintneto_saldo_prog =  "(".number_format(abs($ld_ctaactintneto_saldo_prog),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactintneto_saldo_prog =  number_format($ld_ctaactintneto_saldo_prog,2,',','.');
			   }
			   
			   if($ld_ctaactintneto_saldo_ejec<0)
			   {
			    $ld_ctaactintneto_saldo_ejec =  "(".number_format(abs($ld_ctaactintneto_saldo_ejec),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactintneto_saldo_ejec =  number_format($ld_ctaactintneto_saldo_ejec,2,',','.');
			   }
			   
			   if($ld_ctaactintneto_var_saldo_ejec_ant<0)
			   {
			    $ld_ctaactintneto_var_saldo_ejec_ant =  "(".number_format(abs($ld_ctaactintneto_var_saldo_ejec_ant),2,',','.').")";
			   }
			   else
			   {
			    $ld_ctaactintneto_var_saldo_ejec_ant =  number_format($ld_ctaactintneto_var_saldo_ejec_ant,2,',','.');
			   }

			   $la_data[]=array('cuentas'=>'','denominacion'=>'ACTIVO INTANGIBLE NETO' ,
									 'cuentas_sal'=>'',
									 'saldo_real_ant'=>$ld_ctaactintneto_saldo_real_ant,
									 'saldo_apro'=>$ld_ctaactintneto_saldo_apro,
									 'saldo_mod'=>$ld_ctaactintneto_saldo_modi,
									 'saldo_ant'=>$ld_ctaactintneto_saldo_ejec,
									 'programado'=>$ld_ctaactintneto_saldo_prog,
									 'variacion_absoluta'=>$ld_ctaactintneto_variacion_absoluta,
									 'variacion_porcentual'=>$ld_ctaactintneto_variacion_porcentual,
									 'variacion_saldos'=>$ld_ctaactintneto_var_saldo_ejec_ant); 
			 }
			}
			
			   $ld_total_variacion_absoluta =  0;
			   $ld_total_variacion_porcentual = 0;
			   
			   if($ld_total_saldo_real_ant<0)
			   {
			    $ld_total_saldo_real_ant =  "(".number_format(abs($ld_total_saldo_real_ant),2,',','.').")";
			   }
			   else
			   {
			    $ld_total_saldo_real_ant =  number_format($ld_total_saldo_real_ant,2,',','.');
			   }
			   
			   if($ld_total_saldo_apro<0)
			   {
			    $ld_total_saldo_apro =  "(".number_format(abs($ld_total_saldo_apro),2,',','.').")";
			   }
			   else
			   {
			    $ld_total_saldo_apro =  number_format($ld_total_saldo_apro,2,',','.');
			   }
			   
			   if($ld_total_saldo_modi<0)
			   {
			    $ld_total_saldo_modi =  "(".number_format(abs($ld_total_saldo_modi),2,',','.').")";
			   }
			   else
			   {
			    $ld_total_saldo_modi =  number_format($ld_total_saldo_modi,2,',','.');
			   }
			   
			   if($ld_total_saldo_ejec<0)
			   {
			    $ld_total_saldo_ejec =  "(".number_format(abs($ld_total_saldo_ejec),2,',','.').")";
			   }
			   else
			   {
			    $ld_total_saldo_ejec =  number_format($ld_total_saldo_ejec,2,',','.');
			   }
			   
			   if($ld_total_var_saldo_ejec_ant<0)
			   {
			    $ld_total_var_saldo_ejec_ant =  "(".number_format(abs($ld_total_var_saldo_ejec_ant),2,',','.').")";
			   }
			   else
			   {
			    $ld_total_var_saldo_ejec_ant =  number_format($ld_total_var_saldo_ejec_ant,2,',','.');
			   }
			   
			   $ld_total_variacion_absoluta = abs($ld_total_saldo_prog - $ld_total_saldo_ejec);
			   
			   if($ld_total_saldo_prog>0)
			   {
			    $ld_total_variacion_porcentual = ($ld_total_saldo_ejec/$ld_total_saldo_prog)*100;
			   }
			   if($ld_total_saldo_prog<0)
			   {
			    $ld_total_saldo_prog =  "(".number_format(abs($ld_total_saldo_prog),2,',','.').")";
			   }
			   else
			   {
			    $ld_total_saldo_prog =  number_format($ld_total_saldo_prog,2,',','.');
			   }
			   
			   
			   $ld_total_variacion_absoluta =  number_format($ld_total_variacion_absoluta,2,',','.');
			   $ld_total_variacion_porcentual = number_format($ld_total_variacion_porcentual,2,',','.')." %";
			
			
			$la_data[]=array('cuentas'=>'','denominacion'=>'PASIVO + PATRIMONIO' ,
									 'cuentas_sal'=>'',
									 'saldo_real_ant'=>$ld_total_saldo_real_ant,
									 'saldo_apro'=>$ld_total_saldo_apro,
									 'saldo_mod'=>$ld_total_saldo_modi,
									 'saldo_ant'=>$ld_total_saldo_ejec,
									 'programado'=>$ld_total_saldo_prog,
									 'variacion_absoluta'=>$ld_total_variacion_absoluta,
									 'variacion_porcentual'=>$ld_total_variacion_porcentual,
									 'variacion_saldos'=>$ld_total_var_saldo_ejec_ant);
			uf_print_detalle($la_data,$io_pdf);
			unset($la_data);		
			$io_pdf->ezStopPageNumbers(1,1);
			if (isset($d) && $d)
			{
				$ls_pdfcode = $io_pdf->ezOutput(1);
				$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
				echo '<html><body>';
				echo trim($ls_pdfcode);
				echo '</body></html>';
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