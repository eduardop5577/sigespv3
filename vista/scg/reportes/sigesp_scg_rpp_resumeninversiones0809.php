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
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_resumeninversiones0809.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo1,$as_periodo,$io_pdf)
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
		// Fecha Creación: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_titulo1,$io_pdf;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->rectangle(10,480,985,110);
		$io_pdf->addText(15,580,9,"<b>CODIGO PRESUPUESTARIO DEL ENTE: </b>".$_SESSION["la_empresa"]["codasiona"]); // Agregar el título
		$io_pdf->addText(15,565,9,"<b>DENOMINACION DEL ENTE: </b>".$_SESSION["la_empresa"]["nombre"]); // Agregar el título
		$io_pdf->addText(15,550,9,"<b>ORGANO DE ADSCRIPCION: </b>".$_SESSION["la_empresa"]["nomorgads"]); // Agregar el título
		$io_pdf->addText(15,535,9,"<b>PERIODO: </b>".strtoupper($as_periodo)); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo);
		$tm=500-($li_tm/2);
		$io_pdf->addText($tm,500,10,$as_titulo); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo1);
		$tm=500-($li_tm/2);
		$io_pdf->addText($tm,490,10,$as_titulo1); // Agregar el título

		$io_pdf->addText(900,580,7,$_SESSION["ls_database"]); // Agregar la Base de datos
		$io_pdf->addText(900,570,9,"Fecha:  ".date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(900,560,9,"Hora:   ".date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_reporte($ai_ano,$as_meses_trimestre,$as_etiqueta,$io_pdf)
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
		// Fecha Creación: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSetY(520);
		if($as_etiqueta=="Mensual")
		{
		   $ls_etiqueta="Mes";
		}
		if($as_etiqueta=="Bi-Mensual")
		{
		   $ls_etiqueta="Bimestre";
		}
		if($as_etiqueta=="Trimestral")
		{
		   $ls_etiqueta="Trimestre";
		}
		if($as_etiqueta=="Semestral")
		{
		   $ls_etiqueta="Semestre";
		}
		$la_data=array(array('name'=>'<b>Presupuesto   </b> '.'<b>'.$ai_ano.'</b>'),
		               array('name'=>'<b>'.$ls_etiqueta.'   </b>'.'<b>'.$as_meses_trimestre.'</b>'));
		$la_columna=array('name'=>'','name'=>'','name'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 8,       // Tamaño de Letras
						 'titleFontSize' => 8, // Tamaño de Letras de los títulos
						 'showLines'=>0,        // Mostrar Líneas
						 'shaded'=>0,           // Sombra entre líneas
						 'xPos'=>265,//65
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>500),
									   'name'=>array('justification'=>'left','width'=>500)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_venta($io_pdf)
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
		// Fecha Creación: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSetY(480);
		$la_data=array(array('name'=>'<b> VENTA Y/O DESINCORPORACIÓN DE ACTIVOS </b>'));
		$la_columna=array('name'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 8,       // Tamaño de Letras
						 'titleFontSize' => 8, // Tamaño de Letras de los títulos
						 'showLines'=>0,        // Mostrar Líneas
						 'shaded'=>0,           // Sombra entre líneas
						 'xPos'=>265,//65
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>500)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo($as_etiqueta,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_titulo
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
        $io_pdf->ezSetDy(-50);
		if($as_etiqueta=="Mensual")
		{
		   $ls_etiqueta="Mes";
		}
		if($as_etiqueta=="Bi-Mensual")
		{
		   $ls_etiqueta="Bimestre";
		}
		if($as_etiqueta=="Trimestral")
		{
		   $ls_etiqueta="Trimestre";
		}
		if($as_etiqueta=="Semestral")
		{
		   $ls_etiqueta="Semestre";
		}
		$la_data   =array(array('name1'=>'<b></b>',
								'name2'=>'<b>VARIACION '.strtoupper($ls_etiqueta).'</b>',
                                'name3'=>'<b>VARIACION EJECUTADO PROGRAMADO '.strtoupper($ls_etiqueta).'</b>',
                                'name4'=>'<b>TOTAL ACUMULADO AL '.strtoupper($ls_etiqueta).'</b>'));
                                
		$la_columna=array('name1'=>'','name2'=>'','name3'=>'','name4'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 7,       // Tamaño de Letras
						 'titleFontSize' => 7, // Tamaño de Letras de los títulos
						 'showLines'=>1,        // Mostrar Líneas
						 'shaded'=>0,           // Sombra entre líneas
						 'xPos'=>508,
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900,
						 'cols'=>array('name1'=>array('justification'=>'center','width'=>410),// Justificación y ancho de la columna
						               'name2'=>array('justification'=>'center','width'=>190),// Justificación y ancho de la columna
									   'name3'=>array('justification'=>'center','width'=>190),// Justificación y ancho de la columna
									   'name4'=>array('justification'=>'center','width'=>190))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');

	}// end function uf_print_titulo
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_etiqueta,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		if($as_etiqueta=="Mensual")
		{
		   $ls_etiqueta="Mes";
		}
		if($as_etiqueta=="Bi-Mensual")
		{
		   $ls_etiqueta="Bimestre";
		}
		if($as_etiqueta=="Trimestral")
		{
		   $ls_etiqueta="Trimestre";
		}
		if($as_etiqueta=="Semestral")
		{
		   $ls_etiqueta="Semestre";
		}
		$la_data=array(array('cuenta'=>'<b>Cuenta</b>','denominacion'=>'<b>Denominación</b>',
                             'pres_apr'=>'<b>Presupuesto Aprobado</b>',
		                     'pres_mod'=>'<b>Presupuesto Modificado</b>',
                             'monto_prog'=>'<b>Programado</b>',
                             'monto_eject'=>'<b>Ejecutado</b>',
							 'varia_abs'=>'<b>Absoluto</b>',
                             'varia_porc'=>'<b>Porcentual</b>',
                             'total_acum_pro'=>'<b>Programado</b>',
							 'total_acum_eje'=>'<b>Ejecutado</b>'));
		$la_columna=array('cuenta'=>'','denominacion'=>'',
                             'pres_apr'=>'',
                             'pres_mod'=>'',
                             'monto_prog'=>'',
                             'monto_eject'=>'',
		                     'varia_abs'=>'',
                             'varia_porc'=>'',
                             'total_acum_pro'=>'',
                             'total_acum_eje'=>'');
		$la_config=array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 7,       // Tamaño de Letras
						 'titleFontSize' => 7, // Tamaño de Letras de los títulos
						 'showLines'=>2,        // Mostrar Líneas
						 'shaded'=>0,           // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990,
						 'colGap'=>2,
						 'xPos'=>505,
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						 			   'pres_apr'=>array('justification'=>'center','width'=>95), // Justificación y ancho de la columna
						 			   'pres_mod'=>array('justification'=>'center','width'=>95), //f Justificación y ancho de la columna
									   'monto_prog'=>array('justification'=>'center','width'=>95), // Justificación y ancho de la columna
									   'monto_eject'=>array('justification'=>'center','width'=>95), // Justificación y ancho de la columna
									   'varia_abs'=>array('justification'=>'center','width'=>95), // Justificación y ancho de la columna
									   'varia_porc'=>array('justification'=>'center','width'=>95), // Justificación y ancho de la columna
									   'total_acum_pro'=>array('justification'=>'center','width'=>95), // Justificación y ancho de la columna
									   'total_acum_eje'=>array('justification'=>'center','width'=>95))); // Justificación y ancho de la columna
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');

	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>505,        
						 'cols'=>array('cuenta'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>150), // Justificación y ancho de la columna
						 			   'presupuesto_aprobado'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
						 			   'presupuesto_modificado'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'pres_anual'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'monto_eject'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'varia_abs'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'varia_porc'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'prog_acum'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'acum_eject'=>array('justification'=>'right','width'=>95))); // Justificación y ancho de la columna
		
		$la_columnas=array('cuenta'=>'<b></b>',
						   'denominacion'=>'<b></b>',
						   'presupuesto_aprobado'=>'<b>Presupuesto Aprobado</b>',
						   'presupuesto_modificado'=>'<b>Presupuesto Aprobado</b>',
						   'pres_anual'=>'<b>Monto Programado</b>',
						   'monto_eject'=>'<b>Monto Ejecutado</b>',
						   'varia_abs'=>'<b>Absoluto</b>',
						   'varia_porc'=>'<b>Porcentual</b>',
						   'prog_acum'=>'<b>Programado</b>',
						   'acum_eject'=>'<b>Ejecutado</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($la_data_tot,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 07/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
                         'xPos'=>505, 
						 'cols'=>array('total'=>array('justification'=>'right','width'=>220), // Justificación y ancho de la columna
									   'presupuesto_aprobado'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
						 			   'presupuesto_modificado'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'pres_anual'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'monto_eject'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'varia_abs'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'varia_porc'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'prog_acum'=>array('justification'=>'right','width'=>95), // Justificación y ancho de la columna
									   'acum_eject'=>array('justification'=>'right','width'=>95))); // Justificación y ancho de la columna
		
		$la_columnas=array('total'=>'',
		                   'presupuesto_aprobado'=>'',
						   'presupuesto_modificado'=>'',
						   'pres_anual'=>'',
						   'monto_eject'=>'',
						   'varia_abs'=>'',
						   'varia_porc'=>'',
						   'prog_acum'=>'',
						   'acum_eject'=>'');
		$io_pdf->ezTable($la_data_tot,$la_columnas,'',$la_config);
	}// end function uf_print_pie_cabecera
	
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
	
	//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();			
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once("class_funciones_scg.php");
		$io_fun_scg=new class_funciones_scg();
		$ls_tiporeporte="0";
		$ls_bolivares="";
		require_once("sigesp_scg_reporte_comparado_0809.php");
		$io_report  = new sigesp_scg_reporte_comparado_0809();
		$ls_bolivares ="Bs.";
		//--------------------------------------------------  Parámetros para Filtar el Reporte  ---------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$ls_formato_cont = $_SESSION["la_empresa"]["formcont"];
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
		$ls_periodo = "Desde el ".$ldt_fecdes." al ".$ld_fechas;
//----------------------------------------------------  Parámetros del encabezado  --------------------------------------------
		$ls_titulo=" <b>RESUMEN DE  INVERSIONES</b>";       
		$ls_titulo1=" (En Bolívares) ";
//------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
	$lb_valido=uf_insert_seguridad("<b>Instructivo 08 Comparado Resumen de Inversiones</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
	    $lb_valido=$io_report->uf_scg_reportes_comparados_resumen_inversiones_ins08($ldt_fecdes,$ldt_fechas,$li_mesdes,$li_meshas,$ls_cant_mes);
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
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetDy(0.5);
		$io_pdf->ezSetCmMargins(7.08,3,3,3);            // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_titulo1,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(980,40,10,'','',1); // Insertar el número de página
		$ld_total_monto_programado=0;
		$ld_total_monto_programado_acumulado=0;
		$ld_total_monto_ejecutado=0;
		$ld_total_monto_ejecutado_acumulado=0;
		$ld_total_variacion_absoluta=0;
		$ld_total_porcentaje_variacion=0;
		$ld_total_variacion_absoluta_acumulada=0;
		$ld_total_porcentaje_variacion_acumulada=0;
		$ld_total_reprog_proxima=0;
        $ld_total_presupuesto_aprobado = 0;
        $ld_total_presupuesto_modificado = 0;
		$li_total=$io_report->dts_reporte->getRowCount("cuenta");
		$la_cuentas_saldos = array();
		$ls_mascara = str_replace("-","",$ls_formato_cont);
		$li_len_mas = strlen($ls_mascara);
		$ls_ceros = str_pad("",$li_len_mas - 9,"0");  
		$la_data_tot = NULL;
        $la_cuentas_saldos[1]  = '121010100'.$ls_ceros;
        $la_cuentas_saldos[2]  = '121010200'.$ls_ceros;
        $la_cuentas_saldos[3]  = '121020100'.$ls_ceros;
        $la_cuentas_saldos[4]  = '121020200'.$ls_ceros;
        $la_cuentas_saldos[5]  = '121030100'.$ls_ceros;
        $la_cuentas_saldos[6]  = '121030200'.$ls_ceros;
        $la_cuentas_saldos[7]  = '123010100'.$ls_ceros;
        $la_cuentas_saldos[8]  = '123010200'.$ls_ceros;
        $la_cuentas_saldos[9]  = '123010300'.$ls_ceros;
        $la_cuentas_saldos[10] = '123010400'.$ls_ceros;
        $la_cuentas_saldos[11] = '123010500'.$ls_ceros;
        $la_cuentas_saldos[12] = '123010600'.$ls_ceros;
        $la_cuentas_saldos[13] = '123010700'.$ls_ceros;
        $la_cuentas_saldos[14] = '123010800'.$ls_ceros;
        $la_cuentas_saldos[15] = '123011900'.$ls_ceros;
        $la_cuentas_saldos[16] = '123020000'.$ls_ceros;
        $la_cuentas_saldos[17] = '123030000'.$ls_ceros;
        $la_cuentas_saldos[18] = '123040000'.$ls_ceros;
        $la_cuentas_saldos[19] = '123050100'.$ls_ceros;
        $la_cuentas_saldos[20] = '123050200'.$ls_ceros;
        $la_cuentas_saldos[21] = '124010000'.$ls_ceros;
        $la_cuentas_saldos[22] = '124020000'.$ls_ceros;
        $la_cuentas_saldos[23] = '124030000'.$ls_ceros;
        $la_cuentas_saldos[24] = '124040000'.$ls_ceros;
        $la_cuentas_saldos[25] = '124050000'.$ls_ceros;
        $la_cuentas_saldos[26] = '124190000'.$ls_ceros;
		
		for($z=1;$z<=$li_total;$z++)
		{
			
			$thisPageNum=$io_pdf->ezPageCount;
			$ls_sc_cuenta=$io_report->dts_reporte->data["cuenta"][$z];
			$ls_denominacion=$io_report->dts_reporte->data["denominacion"][$z];
			$li_nivel=$io_report->dts_reporte->data["nivel"][$z];
			$ld_monto_programado=$io_report->dts_reporte->data["monto_programado"][$z];
			$ld_monto_programado_acumulado=$io_report->dts_reporte->data["programado_acumulado"][$z];
			$ld_monto_ejecutado=$io_report->dts_reporte->data["monto_ejecutado"][$z];
			$ld_monto_ejecutado_acumulado=$io_report->dts_reporte->data["ejecutado_acumulado"][$z];
			$ld_variacion_absoluta=$io_report->dts_reporte->data["variacion_absoluta"][$z];
			$ld_porcentaje_variacion=$io_report->dts_reporte->data["porcentaje_variacion"][$z];
            $ld_presupuesto_aprobado=$io_report->dts_reporte->data["presupuesto_aprobado"][$z];
            $ld_presupuesto_modificado=$io_report->dts_reporte->data["presupuesto_modificado"][$z];
			
			if(($li_nivel==1)&&(substr($ls_sc_cuenta,0,1) == "4"))
			{
				$ld_total_monto_programado=$ld_total_monto_programado+$ld_monto_programado;
				$ld_total_monto_programado_acumulado=$ld_total_monto_programado_acumulado+$ld_monto_programado_acumulado;
				$ld_total_monto_ejecutado=$ld_total_monto_ejecutado+$ld_monto_ejecutado;
				$ld_total_monto_ejecutado_acumulado=$ld_total_monto_ejecutado_acumulado+$ld_monto_ejecutado_acumulado;
                $ld_total_presupuesto_aprobado = $ld_total_presupuesto_aprobado +  $ld_presupuesto_aprobado;
                $ld_total_presupuesto_modificado = $ld_total_presupuesto_modificado + $ld_presupuesto_modificado;  
			}
			
			if(is_int(array_search($ls_sc_cuenta,$la_cuentas_saldos)))
			{
			    $ld_total_monto_programado=$ld_total_monto_programado+$ld_monto_programado;
				$ld_total_monto_programado_acumulado=$ld_total_monto_programado_acumulado+$ld_monto_programado_acumulado;
				$ld_total_monto_ejecutado=$ld_total_monto_ejecutado+$ld_monto_ejecutado;
				$ld_total_monto_ejecutado_acumulado=$ld_total_monto_ejecutado_acumulado+$ld_monto_ejecutado_acumulado;
                $ld_total_presupuesto_aprobado = $ld_total_presupuesto_aprobado +  $ld_presupuesto_aprobado;
                $ld_total_presupuesto_modificado = $ld_total_presupuesto_modificado + $ld_presupuesto_modificado;
			
			}
			
			
			if(substr($ls_sc_cuenta,0,1) != 4)
			{
             $ls_sc_cuenta = uf_formato_salida($ls_sc_cuenta,$li_nivel,$ls_formato_cont,".");
			}
			else
			{
			 $li_nivel_spg = count((array)explode('-',$_SESSION["la_empresa"]["formpre"]));
			 $ls_sc_cuenta = uf_formato_salida($ls_sc_cuenta,$li_nivel_spg,$_SESSION["la_empresa"]["formpre"],".");
			}	
			$ld_monto_programado=number_format($ld_monto_programado,2,",",".");
			$ld_monto_programado_acumulado=number_format($ld_monto_programado_acumulado,2,",",".");
			$ld_monto_ejecutado=number_format($ld_monto_ejecutado,2,",",".");
			$ld_monto_ejecutado_acumulado=number_format($ld_monto_ejecutado_acumulado,2,",",".");
			$ld_variacion_absoluta=number_format(abs($ld_variacion_absoluta),2,",",".");
			$ld_porcentaje_variacion=number_format(abs($ld_porcentaje_variacion),2,",",".");
            $ld_presupuesto_aprobado    =number_format($ld_presupuesto_aprobado,2,",",".");
			$ld_presupuesto_modificado  =number_format($ld_presupuesto_modificado,2,",",".");
            
			$la_data[]=array('cuenta'=>$ls_sc_cuenta,
			                   'denominacion'=>$ls_denominacion,
							   'pres_anual'=>$ld_monto_programado,
							   'prog_acum'=>$ld_monto_programado_acumulado,
							   'monto_eject'=>$ld_monto_ejecutado,
							   'acum_eject'=>$ld_monto_ejecutado_acumulado,
							   'varia_abs'=>$ld_variacion_absoluta,
							   'varia_porc'=>$ld_porcentaje_variacion,
                               'presupuesto_aprobado'=>$ld_presupuesto_aprobado,
							   'presupuesto_modificado'=>$ld_presupuesto_modificado);
								                                              
			$ld_monto_programado=str_replace('.','',$ld_monto_programado);
			$ld_monto_programado=str_replace(',','.',$ld_monto_programado);		
			$ld_monto_programado_acumulado=str_replace('.','',$ld_monto_programado_acumulado);
			$ld_monto_programado_acumulado=str_replace(',','.',$ld_monto_programado_acumulado);		
			$ld_monto_ejecutado=str_replace('.','',$ld_monto_ejecutado);
			$ld_monto_ejecutado=str_replace(',','.',$ld_monto_ejecutado);		
			$ld_monto_ejecutado_acumulado=str_replace('.','',$ld_monto_ejecutado_acumulado);
			$ld_monto_ejecutado_acumulado=str_replace(',','.',$ld_monto_ejecutado_acumulado);
			$ld_variacion_absoluta=str_replace('.','',$ld_variacion_absoluta);
			$ld_variacion_absoluta=str_replace(',','.',$ld_variacion_absoluta);
			$ld_porcentaje_variacion=str_replace('.','',$ld_porcentaje_variacion);
			$ld_porcentaje_variacion=str_replace(',','.',$ld_porcentaje_variacion);	
		}//for

				$ld_total_variacion_absoluta = abs($ld_total_monto_programado - $ld_total_monto_ejecutado);
				
				if($ld_total_monto_programado > 0)
				{
				 $ld_total_porcentaje_variacion =  ($ld_total_monto_ejecutado/$ld_total_monto_programado)*100;
				}
				else
				{
				 $ld_total_porcentaje_variacion = 0;
				}
				
				if($ld_total_monto_programado_acumulado > 0)
				{
				 $ld_total_porcentaje_variacion_acumulada= ($ld_total_monto_ejecutado_acumulado/$ld_total_monto_programado_acumulado)*100; 
				}
				else
				{
				 $ld_total_porcentaje_variacion_acumulada = 0;
				}
				
				
				
				$ld_total_monto_programado=number_format($ld_total_monto_programado,2,",",".");
				$ld_total_monto_programado_acumulado=number_format($ld_total_monto_programado_acumulado,2,",",".");
				$ld_total_monto_ejecutado=number_format($ld_total_monto_ejecutado,2,",",".");
				$ld_total_monto_ejecutado_acumulado=number_format($ld_total_monto_ejecutado_acumulado,2,",",".");
				$ld_total_variacion_absoluta=number_format($ld_total_variacion_absoluta,2,",",".");
				$ld_total_porcentaje_variacion=number_format($ld_total_porcentaje_variacion,2,",",".");
                $ld_total_presupuesto_aprobado = number_format($ld_total_presupuesto_aprobado,2,",",".");
                $ld_total_presupuesto_modificado = number_format($ld_total_presupuesto_modificado,2,",",".");
				
				 $la_data_tot[]=array('total'=>'<b>TOTALES</b>',
				                        'pres_anual'=>$ld_total_monto_programado,
										'prog_acum'=>$ld_total_monto_programado_acumulado,
										'monto_eject'=>$ld_total_monto_ejecutado,
										'acum_eject'=>$ld_total_monto_ejecutado_acumulado,
										'varia_abs'=>$ld_total_variacion_absoluta,
										'varia_porc'=>$ld_total_porcentaje_variacion,
                                        'presupuesto_aprobado'=>$ld_total_presupuesto_aprobado,
										'presupuesto_modificado'=>$ld_total_presupuesto_modificado);
		//print_r($la_data);
		uf_print_titulo_reporte($li_ano,$ls_meses,$ls_etiqueta,$io_pdf);
		uf_print_titulo($ls_etiqueta,$io_pdf);
		uf_print_cabecera($ls_etiqueta,$io_pdf);
		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		uf_print_pie_cabecera($la_data_tot,$io_pdf);
		unset($la_data);
		unset($la_data_tot);
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if($z<$li_total)
		{
		 $io_pdf->ezNewPage(); // Insertar una nueva página
		} 
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