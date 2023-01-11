<?php
/***********************************************************************************
* @fecha de modificacion: 07/09/2022, para la version de php 8.1 
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
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_srh;
		
		$ls_descripcion="Generó el Reporte Resultados evaluacion contratado obrero";
		$lb_valido=$io_fun_srh->uf_load_seguridad_reporte("SRH","sigesp_srh_p_evaluacion_contratado_obrero.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_print_encabezado_pagina($io_pdf)
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
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(15,40,585,40);
		//$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,700,60,60);// Agregar Logo ipsfa
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_psicologia.jpg',525,700,60,60);// Agregar Logo psicologia
		
		$io_pdf->addText(540,770,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(546,764,6,date("h:i a")); // Agregar la Hora

		$li_tm=$io_pdf->getTextWidth(10,'REPÚBLICA BOLIVARIANA DE VENEZUELA');
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,10,'REPÚBLICA BOLIVARIANA DE VENEZUELA'); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,'INSTITUTO DE PREVISION SOCIAL');
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,10,'INSTITUTO DE PREVISION SOCIAL'); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,'DE LA FUERZA ARMADA');
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,710,10,'DE LA FUERZA ARMADA'); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(9,'GERENCIA DE RECURSOS HUMANOS');
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,690,9,'GERENCIA DE RECURSOS HUMANOS'); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(9,'Unidad de Psicología');
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,680,9,'Unidad de Psicología'); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(9,'Resultados de Evaluación');
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,670,9,'Resultados de Evaluación'); // Agregar el título
		$io_pdf->addText(40,650,10,'Datos de Identificación'); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');		
	}
	//-----------------------------------------------------------------------------------------------------------------------------------	
		
	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_print_cabecera($as_codper,$as_nombre,$as_edocivper,$as_nivacaper,$as_carpos,$as_fecnacper,$io_pdf)
	{
		global $io_pdf;
		$la_data[0]=array('nombre'=>'<b>APELLIDOS Y NOMBRES DEL ASPIRANTE</b>','edad'=>'<b>EDAD</b>','estado'=>'<b>ESTADO CIVIL</b>','cedula'=>'<b>CÉDULA DE IDENTIDAD</b>');
		$la_data[1]=array('nombre'=>$as_nombre,'edad'=>$as_fecnacper,'estado'=>$as_edocivper,'cedula'=>$as_codper);
		$la_columna=array('nombre'=>'','edad'=>'','estado'=>'','cedula'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla						 
						 'maxWidth'=>550, // Orientaci? de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>230), // Justificación y ancho de la columna
									   'edad'=>array('justification'=>'center','width'=>100),
									   'estado'=>array('justification'=>'center','width'=>100),
						 			   'cedula'=>array('justification'=>'center','width'=>120))); // Justificación y ancho de la columna
						 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$la_data[0]=array('grado'=>'<b>GRADO DE INSTRUCCIÓN</b>','cargo'=>'<b>CARGO PARA EL CUAL SE EVALUA</b>');
		$la_data[1]=array('grado'=>$as_nivacaper,'cargo'=>$as_carpos);
		$la_columna=array('grado'=>'','cargo'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla						 
						 'maxWidth'=>550, // Orientaci? de la tabla
						 'cols'=>array('grado'=>array('justification'=>'center','width'=>230), // Justificación y ancho de la columna
						 			   'cargo'=>array('justification'=>'center','width'=>320))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}		
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_print_detalle($la_data,$as_obseval,$as_receval,$io_pdf)
	{
		global $io_pdf;
		$io_pdf->ezSetDy(-15);
		$la_data1[0]=array('titulo'=>'<b>PARA EL MOMENTO DE LA EVALUACIÓN Y CON LAS TÉCNICAS APLICADAS SE OBSERVO</b>');
		$la_columna=array('titulo'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla						 
						 'maxWidth'=>550, // Orientaci? de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>550))); // Justificación y ancho de la columna
 		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$la_columna=array('denasp'=>'                           ASPECTOS EVALUADOS','deficiente'=>'DEF.','bajo'=>'BAJ.', 'promedio'=>'PROM.',
						  'optimo'=>'OPTI.','excepcional'=>'EXCE.','no'=>'NO OBSERV.');		
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla						 
						 'maxWidth'=>550, // Orientaci? de la tabla
						 'cols'=>array('denasp'=>array('justification'=>'left','width'=>240), // Justificación y ancho de la columna
						 			   'deficiente'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'bajo'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'promedio'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'optimo'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'excepcional'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'no'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
						 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);

		$io_pdf->ezSetDy(-10);
		$la_data1[0]=array('titulo'=>'<b>OBSERVACIONES</b>');
		$la_columna=array('titulo'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla						 
						 'maxWidth'=>550, // Orientaci? de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>550))); // Justificación y ancho de la columna
 		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		$la_data1[0]=array('titulo'=>str_pad(substr($as_obseval,0,2500),2600," "));
		$la_columna=array('titulo'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla						 
						 'maxWidth'=>550, // Orientaci? de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la columna
 		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);

		$la_puntaje[0]='';
		$la_puntaje[1]='';
		$la_puntaje[2]='';
		$la_puntaje[3]='';
		$la_puntaje[4]='';
		$la_puntaje[5]='';
		$la_puntaje[$as_receval]='X';
		$io_pdf->ezSetDy(-10);
		$la_data1[0]=array('titulo'=>'<b>RECOMENDACIÓN</b>');
		$la_columna=array('titulo'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla						 
						 'maxWidth'=>550, // Orientaci? de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>550))); // Justificación y ancho de la columna
 		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		$la_data1[0]=array('campo1'=>'Ingresar', 'campo2'=>$la_puntaje[0], 'campo3'=>'Archivo de Elegible','campo4'=>$la_puntaje[3]);
		$la_data1[1]=array('campo1'=>'Ingresar bajo supervisión directa', 'campo2'=>$la_puntaje[1], 'campo3'=>'No ingresar','campo4'=>$la_puntaje[4]);
		$la_data1[2]=array('campo1'=>'Ingresar en período de prueba','campo2'=>$la_puntaje[2], 'campo3'=>'Otra','campo4'=>$la_puntaje[5]);
		$la_columna=array('campo1'=>'', 'campo2'=>'', 'campo3'=>'', 'campo4'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla						 
						 'maxWidth'=>550, // Orientaci? de la tabla
						 'cols'=>array('campo1'=>array('justification'=>'left','width'=>225), // Justificación y ancho de la columna
						 			   'campo2'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'campo3'=>array('justification'=>'left','width'=>225), // Justificación y ancho de la columna
						 			   'campo4'=>array('justification'=>'center','width'=>50))); // Justificación y ancho de la columna
 		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$io_pdf->ezSetDy(-80);
		$la_data1[0]=array('titulo'=>'                                                                                                                   <b>FIRMA:_______________________________________</b>');
		$la_columna=array('titulo'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla						 
						 'maxWidth'=>550, // Orientaci? de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>550))); // Justificación y ancho de la columna
 		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
	}		
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//--------------------------------------------------------------------------------------------------------------------
   	function calcular_edad($fecha_nac,$fecha_hasta)
	{  	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: calcular_edad
		//	    Arguments: fecha_nac  // fecha de nacimiento
		//                 fecha_hasta	 fecha hasta 	 
		//	      Returns: anos
		//	  Description: Funcion que obtiene la edad de una persona dada una fecha de nacimiento
		//     Creado Por: Maria Beatriz Unda		
		// Fecha Creación: 29/05/2008							Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;		 
		$c = date("Y",$fecha_nac);	   
		$b = date("m",$fecha_nac);	  
		$a = date("d",$fecha_nac); 	
		$anos = date("Y",$fecha_hasta)-$c; 
		if(date("m",$fecha_hasta)-$b > 0)
		{
		}
		elseif(date("m",$fecha_hasta)-$b == 0)
		{
			if(date("d",$fecha_hasta)-$a <= 0)
			{		  
				$anos = $anos-1;	        
			}
		}
		else
		{		  
			$anos = $anos-1;		          
		}  
		return $anos;	 
	}// fin de function calcular_edad($fecha_nac,$fecha_hasta)
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/utilidades/class_funciones_srh.php");
	$io_fun_srh=new class_funciones_srh('../../');
	require_once("class_folder/sigesp_srh_class_report.php");
	$io_report=new sigesp_srh_class_report();
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------	
	
 	$ls_codper =$io_fun_srh->uf_obtenervalor_get("codper","");
	$ls_feceval =$io_fun_srh->uf_obtenervalor_get("feceval",""); 	
	$ls_feceval=$io_funciones->uf_convertirdatetobd($ls_feceval);

	//----------------------------------------------------------------------------------------------------------------------------------//
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
        $lb_valido=$io_report->uf_select_evaluacion_contratado_obrero($ls_codper,$ls_feceval);
		if ($lb_valido==false)
		{
		    print("<script language=JavaScript>");
			print(" alert('No hay nada que reportar');"); 
			print(" close();");
			print("</script>");
		}
		   
		else  // Imprimimos el reporte
		{     
		    
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(5,3,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(570,47,8,'','',1); // Insertar el número de página			
			uf_print_encabezado_pagina($io_pdf);
			while (!$io_report->rs_data->EOF)
			{
				$ls_codper=$io_report->rs_data->fields["codper"];
				$ls_nombre=$io_report->rs_data->fields["apesol"].", ".$io_report->rs_data->fields["nomsol"];
				$ls_carpos=$io_report->rs_data->fields["carpos"];
				$ls_fecnacper=$io_report->rs_data->fields["fecnac"];
				$ls_edocivper=$io_report->rs_data->fields["estciv"];
				$ls_nivacaper=$io_report->rs_data->fields["nivacasol"];
				$ls_obseval=$io_report->rs_data->fields["obseval"];
				$ls_receval=$io_report->rs_data->fields["receval"];
				$ld_fecact=	date("Y-m-d");
				$ls_edad=calcular_edad(strtotime($ls_fecnacper),strtotime($ld_fecact));
				switch ($ls_edocivper)
				{
					case "S":
						$ls_edocivper = "Soltero";
					break;
					case "C":
						$ls_edocivper = "Casado";
					break;
					case "V":
						$ls_edocivper = "Viudo";
					break;
					case "D":
						$ls_edocivper = "Divorciado";
					break;
					case "C":
						$ls_edocivper = "Concubino";
					break;
				}
				switch ($ls_nivacaper)
				{
					case "1":
						$ls_nivacaper = "Primaria";
					break;
					case "2":
						$ls_nivacaper = "Bachiller";
					break;
					case "3":
						$ls_nivacaper = "Técnico Superior";
					break;
					case "4":
						$ls_nivacaper = "Universitario";
					break;
					case "5":
						$ls_nivacaper = "Maestría";
					break;
					case "6":
						$ls_nivacaper = "Postgrado";
					break;
					case "7":
						$ls_nivacaper = "Doctorado";
					break;
					case "8":
						$ls_nivacaper="Especializacion";
					break;
					case "9":
						$ls_nivacaper="Tecnico Medio";
					break;
				}
				uf_print_cabecera($ls_codper,$ls_nombre,$ls_edocivper,$ls_nivacaper,$ls_carpos,$ls_edad,$io_pdf);
				$lb_valido=$io_report->uf_select_dt_evaluacion_contratado_obrero($ls_codper,$ls_feceval);
				if ($lb_valido)
				{
					$li_i=0;
					while (!$io_report->rs_data_dt->EOF)
					{
						$la_puntaje[0]='';
						$la_puntaje[1]='';
						$la_puntaje[2]='';
						$la_puntaje[3]='';
						$la_puntaje[4]='';
						$la_puntaje[5]='';
						$ls_denasp=$io_report->rs_data_dt->fields["denasp"];
						$ls_puntaje=$io_report->rs_data_dt->fields["puntaje"];
						$la_puntaje[$ls_puntaje]='X';
						$la_data[$li_i]=array('denasp'=>$ls_denasp,'deficiente'=>$la_puntaje[0],'bajo'=>$la_puntaje[1], 'promedio'=>$la_puntaje[2],
											  'optimo'=>$la_puntaje[3],'excepcional'=>$la_puntaje[4],'no'=>$la_puntaje[5]);
						$li_i++;
						$io_report->rs_data_dt->MoveNext();
					}
				}
				uf_print_detalle($la_data,$ls_obseval,$ls_receval,$io_pdf);
				$io_report->rs_data->MoveNext();
			}
		}
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");	
		}
	}
?>	
