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
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";		
		print "</script>";		
	}

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_listadopersonalregistrado.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo2,$as_codusu,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,900,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],20,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(12,$as_titulo);
		$tm=295-($li_tm/2);
		$io_pdf->addText($tm,740,12,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(12,$as_titulo2);
		$tm=295-($li_tm/2);
		$io_pdf->addText($tm,728,10,$as_titulo2); // Agregar el título
		if($as_codusu!="")
			$io_pdf->addText(20,695,9,"USUARIO: ".$as_codusu); // Agregar la Fecha
		$io_pdf->addText(560,740,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(560,730,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		unset($la_config);
		$la_columnas=array('codigo'=>'<b>Código</b>','nombre'=>'<b>       Apellidos y Nombres</b>',
								 'fechaing'=>'<b>Fecha de Ingreso a la Institución</b>',
								 'profesion'=>'<b>       Profesion</b>','dentippersss'=>'<b>  Tipo Personal</b>',
								 'estper'=>'<b>   Estatus</b>','fechareg'=>'<b>Fecha Registro</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla				         
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>120), // Justificación y ancho de la columna
									   'fechaing'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'profesion'=>array('justification'=>'left','width'=>80), // Justificación y ancho de la columna
									   'dentippersss'=>array('justification'=>'left','width'=>70), // Justificación y ancho de la columna
									   'estper'=>array('justification'=>'left','width'=>50), // Justificación y ancho de la columna
									   'fechareg'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalleusuario($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columnas=array('codigo'=>'<b>Código</b>','nombre'=>'<b>          Apellidos y Nombres</b>',
								 'fechaing'=>'<b>Fecha de Ingreso a la Institución</b>',
								 'profesion'=>'<b>         Profesion</b>','dentippersss'=>'<b>    Tipo Personal</b>',
								 'estper'=>'<b>   Estatus</b>','codusu'=>'<b>      Usuario</b>','fechareg'=>'<b>Fecha de Registro</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla				         
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>120), // Justificación y ancho de la columna
									   'fechaing'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'profesion'=>array('justification'=>'left','width'=>80), // Justificación y ancho de la columna
									   'dentippersss'=>array('justification'=>'left','width'=>70), // Justificación y ancho de la columna
									   'estper'=>array('justification'=>'left','width'=>50), // Justificación y ancho de la columna
									   'codusu'=>array('justification'=>'left','width'=>60), // Justificación y ancho de la columna
									   'fechareg'=>array('justification'=>'center','width'=>50))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------------	
	function calcular_anos($fecha_1,$fecha_2)
	{  
		$c = date("Y",$fecha_1);	   
		$b = date("m",$fecha_1);	  
		$a = date("d",$fecha_1); 	
		$anos = date("Y",$fecha_2)-$c; 
		if(date("m",$fecha_2)-$b > 0)
		{
		}
		elseif(date("m",$fecha_2)-$b == 0)
		{
			if(date("d",$fecha_2)-$a <= 0)
			{		  
				$anos = $anos-1;	        
			}
		}
		else
		{		  
			$anos = $anos-1;		          
		}  
		return $anos;	 
	} //FIN DE calcular_anos
	//---------------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();	
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");		
	$ls_codusu=$io_fun_nomina->uf_obtenervalor_get("codusu","");
	$ls_fecdes=$io_fun_nomina->uf_obtenervalor_get("fecdes","");  
	$ls_fechas=$io_fun_nomina->uf_obtenervalor_get("fechas","");
	$ls_titulo="<b>Listado de Personal por Fecha de Registro</b>";
	$ls_titulo2="<b>Desde </b>".$ls_fecdes."<b> Hasta </b>".$ls_fechas;
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{  
		$ls_fecdes=$io_fecha->uf_convert_date_to_db($ls_fecdes);  
		$ls_fechas=$io_fecha->uf_convert_date_to_db($ls_fechas);  
		$lb_valido=$io_report->uf_listado_personalregistrado($ls_codperdes,$ls_codperhas,$ls_codusu,$ls_fecdes,$ls_fechas,$ls_orden);
	}
	if(($lb_valido==false)||($io_report->rs_data->RecordCount()==0)) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		
		set_time_limit(1800);		
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(4,3,3,3); // Configuración de los margenes en centímetros		
		uf_print_encabezado_pagina($ls_titulo,$ls_titulo2,$ls_codusu,$io_pdf); // Imprimimos el encabezado de la página	  
		$io_pdf->ezStartPageNumbers(900,50,10,'','',1); // Insertar el número de página
		$li_totrow=$io_report->rs_data->RecordCount();
		$ls_data="";	
		$li_i=0;
		while ((!$io_report->rs_data->EOF)&&($lb_valido))	
		{
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_nomber=$io_report->rs_data->fields["nomper"];
			$ls_apellido=$io_report->rs_data->fields["apeper"];
			$ls_fechaIng=$io_report->rs_data->fields["fecingper"];	
			$ls_fechaReg=$io_report->rs_data->fields["fecreg"];	
			$ls_codusureg=$io_report->rs_data->fields["codusu"];	
			$ls_despro=$io_report->rs_data->fields["despro"];

			$ls_estper=$io_report->rs_data->fields["estper"];
			$ls_dentippersss=$io_report->rs_data->fields["dentippersss"];	
			switch ($ls_estper)
			{
			 case "0":
			  $ls_estper="Pre-Ingreso";
			  $ls_estatus=0;
			  break;
			 
			 case "1":
			  $ls_estper="Activo";
			  $ls_estatus=1;
			  break;
			 
			 case "2":
			  $ls_estper="N/A";
			  $ls_estatus=2;
			  break;
			 
			 case "3":
			  $ls_estper="Egresado";
			  $ls_estatus=3;
			  break;
			  
			  case "4":
			  $ls_estper="Remoción";
			  $ls_estatus=4;
			  break;
			 
			 case "5":
			  $ls_estper="Retiro";
			  $ls_estatus=5;
			  break;
			 
			 case "6":
			  $ls_estper="Destitución";
			  $ls_estatus=6;
			  break;
			 
			 case "7":
			  $ls_estper="Liquidado";
			  $ls_estatus=7;
			  break;
			}			
			$li_i++;
			if($ls_codusu!="")
				$ls_data[$li_i]=array('codigo'=>$ls_codper,'nombre'=>$ls_apellido.", ".$ls_nomber,
									 'fechaing'=>$io_funciones->uf_convertirfecmostrar($ls_fechaIng),
									 'profesion'=>$ls_despro,
									 'dentippersss'=>$ls_dentippersss,'estper'=>$ls_estper,
									 'fechareg'=>$io_funciones->uf_convertirfecmostrar($ls_fechaReg));	
			else
				$ls_data[$li_i]=array('codigo'=>$ls_codper,'nombre'=>$ls_apellido.", ".$ls_nomber,
									 'fechaing'=>$io_funciones->uf_convertirfecmostrar($ls_fechaIng),
									 'profesion'=>$ls_despro,
									 'dentippersss'=>$ls_dentippersss,'estper'=>$ls_estper,'codusu'=>$ls_codusureg,
									 'fechareg'=>$io_funciones->uf_convertirfecmostrar($ls_fechaReg));	
			$io_report->rs_data->MoveNext();
		}
		if ($ls_data!="")
		{
			if($ls_codusu!="")
				uf_print_detalle($ls_data,$io_pdf);
			else
				uf_print_detalleusuario($ls_data,$io_pdf);
			unset($la_data);			
		}
		$io_report->DS->resetds("codper");
		if(($lb_valido)&&($ls_data!="")) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que reportar');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 