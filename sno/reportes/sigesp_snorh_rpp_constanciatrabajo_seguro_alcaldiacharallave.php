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
	ini_set('memory_limit','2048M');
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci?n: 07/06/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$lb_valido=true;
		$ls_descripcion="Gener? el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_constanciatrabajosegurosocial.php",$ls_descripcion);
		return $lb_valido;
	}
	//-------------------------------------------------------------------------------------------------------------------------------

	//-------------------------------------------------------------------------------------------------------------------------------
   	function uf_print_encabezado_pagina($as_titulo,$as_formato,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci?n: 07/05/2008
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();		
		$io_pdf->addJpegFromFile('../../shared/imagebank/ivss.jpg',50,700,50,50); // Agregar Logo
		$tit1="REPUBLICA BOLIVARIANA DE VENEZUELA";
		$li_tm=$io_pdf->getTextWidth(11,$tit1);		
		$io_pdf->addText(155,733,6,$tit1); // Agregar el t?tulo	
		$tit2="MINISTERIO DEL PODER POPULAR PARA EL TRABAJO Y SEGURIDAD SOCIAL";
		$li_tm=$io_pdf->getTextWidth(11,$tit2);		
		$io_pdf->addText(110,727,6,$tit2); // Agregar el t?tulo	
		$tit3="INSTITUTO VENEZOLANO DE LOS SEGUROS SOCIALES";
		$li_tm=$io_pdf->getTextWidth(11,$tit3);		
		$io_pdf->addText(140,720,6,$tit3); // Agregar el t?tulo	
		$tit1="DIRECCI?N GENERAL DE AFILIACION Y PRESTACIONES EN DINERO";
		$li_tm=$io_pdf->getTextWidth(11,$tit1);		
		$io_pdf->addText(120,713,6,$tit1); // Agregar el t?tulo		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,690,11,$as_titulo); // Agregar el t?tulo		
		$io_pdf->addText(500,730,6,$as_formato); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//--------------------------------------------------------------------------------------------------------------------------------
   
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_datos_empresa($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_datos_empresa
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los datos de la empresa
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci?n: 07/05/2008
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data[1]=array('name'=>'<b>DATOS DE LA EMPRESA</b>');
		$la_columna=array('name'=>'<b>DATOS DE LA EMPRESA</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas				         
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>500))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		//---------------TABLA NRO2-----------------------------------------------------------------------------------------
		$la_data2[1]=array('name1'=>'RAZ?N SOCIAL DE LA EMPRESA O NOMBRE DEL PATRONO','name2'=>'N?MERO DE EMPRESA');
		$la_columna=array('name1'=>'','name2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('name1'=>array('justification'=>'center','width'=>380),
						               'name2'=>array('justification'=>'center','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data2,$la_columna,'',$la_config);
		//---------------TABLA NRO03-------------------------------------------------------------------------------------------
		$io_pdf->ezSetY(660);	
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$ls_nombre=strtoupper($ls_nombre);
		$la_data3[1]=array('name3'=>$ls_nombre);
		$la_columna=array('name3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 10,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'xPos'=>'436', // Orientaci?n de la tabla
						 'cols'=>array('name3'=>array('justification'=>'center','width'=>380))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data3,$la_columna,'',$la_config);
		//------------------------Tabla nro. 04--------------------------------------------------------------------------
		//$io_pdf->ezSetY(660);
		$letra= "";
		$numero1= "";
		$numero2= "";
		$numero3= "";
		$numero4= "";
		$numero5= "";
		$numero6= "";
		$numero7= "";
		$numero8="";
		$tamano=0;
		$ls_numero=trim($_SESSION["la_empresa"]["nroivss"]);
		if ($ls_numero=="")
		{
		 $ls_numero="";
		}			
		else
		{
			$ls_numero=str_replace("-","",$ls_numero);
			$tamano=strlen($ls_numero);////tama?o de la cadena
		}		
		for ($j=1;$j<=$tamano;$j++)
		{
		   if ($j==1)
		   {
		   	 $letra= substr($ls_numero,0,1);
		   }
		   if ($j==2)
		   {
		     $numero1= substr($ls_numero,1,1);
		   }
		   if ($j==3)
		   {
		   	 $numero2= substr($ls_numero,2,1);
		   }
		   if ($j==4)
		   {
		     $numero3= substr($ls_numero,3,1);
		   }
		   if ($j==5)
		   {
		   	 $numero4= substr($ls_numero,4,1);
		   }
		   if ($j==6)
		   {
		   	$numero5= substr($ls_numero,5,1);
		   }
		   if ($j==7)
		   {
		   	$numero6= substr($ls_numero,6,1);
		   }
		   if ($j==8)
		   {
		   	$numero7= substr($ls_numero,7,1);
		   }
		   if ($j==9)
		   {
		    $numero8= substr($ls_numero,8,1);
		   }		
		}///fin del for					
        $io_pdf->Rectangle(431,645,120,13.8);
		$io_pdf->line(444.3,659,444.3,645);//linea vertical
		$io_pdf->line(457.6,659,457.6,645);//linea vertical
		$io_pdf->line(470.9,659,470.9,645);//linea vertical
		$io_pdf->line(482.2,659,482.2,645);//linea vertical
		$io_pdf->line(497.5,659,497.5,645);//linea vertical
		$io_pdf->line(510.8,659,510.8,645);//linea vertical
		$io_pdf->line(524.1,659,524.1,645);//linea vertical
		$io_pdf->line(537.4,659,537.4,645);//linea vertical
		
		$io_pdf->addText(434,648,8,$letra); // Agregar el t?tulo
		$io_pdf->addText(449,648,8,$numero1); // Agregar el t?tulo
		$io_pdf->addText(462,648,8,$numero2); // Agregar el t?tulo
		$io_pdf->addText(475,648,8,$numero3); // Agregar el t?tulo
		$io_pdf->addText(488,648,8,$numero4); // Agregar el t?tulo
		$io_pdf->addText(500,648,8,$numero5); // Agregar el t?tulo
		$io_pdf->addText(515,648,8,$numero6); // Agregar el t?tulo
		$io_pdf->addText(528,648,8,$numero7); // Agregar el t?tulo
		$io_pdf->addText(540,648,8,$numero8); // Agregar el t?tulo*/	
     //-------------------------------Tabla Nro. 5---------------------------------------------------------------------
	    $ls_direccion=$_SESSION["la_empresa"]["direccion"];
	    $la_data5[1]=array('name5'=>'DIRECCI?N DE LA EMPRESA');		
		$la_columna=array('name5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('name5'=>array('justification'=>'center','width'=>500))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data5,$la_columna,'',$la_config); 
		//-------------------------------tabla nro6------------------------------------------------------
		$la_data6[1]=array('name6'=>$ls_direccion);
		$la_columna=array('name6'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('name6'=>array('justification'=>'center','width'=>500))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data6,$la_columna,'',$la_config);  
    //---------------------------------tabla nr07---------------------------------------------------------------------------------
	    $ls_nomrep=$_SESSION["la_empresa"]["nomrep"];
		$ls_cedula=$_SESSION["la_empresa"]["cedrep"];
		$ls_telfrep=$_SESSION["la_empresa"]["telfrep"];
		if ($ls_nomrep=="")
		{
		  $ls_nomrep="";
		}
		if ($ls_cedula=="")
		{
		  $ls_cedula="";
		}
		if ($ls_telfrep=="")
		{
		  $ls_telfrep="";
		}
		$la_data7[1]=array('nombre'=>'APELLIDOS Y NOMBRES DEL PATRONO O REPRESENTANTE LEGAL','cedula'=>'CEDULA DE IDENTIDAD Nro.','telefono'=>'TELEFONO');		
		$la_columna=array('nombre'=>'','cedula'=>'','telefono'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>280),
						               'cedula'=>array('justification'=>'center','width'=>100),
						               'telefono'=>array('justification'=>'center','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data7,$la_columna,'',$la_config); 
	//--------------------------TBALA NRO8-------------------------------------------------------------------------------------
	    $la_data8[1]=array('nombre'=>$ls_nomrep,'cedula'=>$ls_cedula,'telefono'=>$ls_telfrep);
		$la_columna=array('nombre'=>'','cedula'=>'','telefono'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>280),
						               'cedula'=>array('justification'=>'center','width'=>100),
						               'telefono'=>array('justification'=>'center','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data8,$la_columna,'',$la_config); 
		unset($la_data); 
		unset($la_data2); 
		unset($la_data3); 		
		unset($la_data5); 
		unset($la_data6); 
		unset($la_data7); 
		unset($la_data8);    
		
	}// end function uf_print_datos_empresa
	//---------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------
	function datos_empleados($as_nomper,$as_apeper,$as_cedper,$as_nacper,$as_fechaI,$as_fechaE,$io_pdf)
	{
		global $io_pdf;
	    $io_pdf->ezSetY(590);
	    $la_data_datos[1]=array('datos'=>'<b>DATOS DEL TRABAJADOR</b>');
		$la_columna=array('datos'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('datos'=>array('justification'=>'center','width'=>500))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_datos,$la_columna,'',$la_config); 
		
		$as_nombre=$as_apeper.", ".$as_nomper;
		
	    $la_data_tit[1]=array('nombre'=>'APELLIDOS Y NOMBRES','extranjero'=>'E','venezolano'=>'V','cedula'=>'CEDULA DE IDENTIDAD Nro.');		
		$la_columna=array('nombre'=>'','extranjero'=>'','venezolano'=>'','cedula'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>330),
						               'extranjero'=>array('justification'=>'center','width'=>25),
						               'venezolano'=>array('justification'=>'center','width'=>25),
						               'cedula'=>array('justification'=>'center','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_tit,$la_columna,'',$la_config); 
		
		if ($as_nacper=="V")
		 {
		   $ls_nacper1="X";
		   $ls_nacper2="";
		 }
		 else
		 {
		   $ls_nacper1="";
		   $ls_nacper2="X";
		 }			
		$la_data_emp[1]=array('nombre'=>$as_nombre,'extranjero'=>$ls_nacper2,'venezolano'=>$ls_nacper1,'cedula'=>$as_cedper);
		$la_columna=array('nombre'=>'','extranjero'=>'','venezolano'=>'','cedula'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>330),
						               'extranjero'=>array('justification'=>'center','width'=>25),
						               'venezolano'=>array('justification'=>'center','width'=>25),
						               'cedula'=>array('justification'=>'center','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_emp,$la_columna,'',$la_config); 
		
		$io_pdf->ezSetY(548);	
		$la_data_titfecI[1]=array('fecha1'=>'FECHA DE INGRESO');
		$la_columna=array('fecha1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'xPos'=>250,
						 'cols'=>array('fecha1'=>array('justification'=>'center','width'=>105))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_titfecI,$la_columna,'',$la_config); 
		
		$ls_ano= substr($as_fechaI,0,4);
		$ls_mes= substr($as_fechaI,5,2);
		$ls_dia= substr($as_fechaI,8,2);
		$la_data_fechaI[1]=array('dia'=>'DIA','mes'=>'MES','ano'=>'A?O');
		$la_data_fechaI[2]=array('dia'=>$ls_dia,'mes'=>$ls_mes,'ano'=>$ls_ano);
		$la_columna=array('dia'=>'','mes'=>'','ano'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'xPos'=>250,
						 'cols'=>array('dia'=>array('justification'=>'center','width'=>35),
						 			   'mes'=>array('justification'=>'center','width'=>35),
						 			   'ano'=>array('justification'=>'center','width'=>35))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_fechaI,$la_columna,'',$la_config);		
	 
	   $io_pdf->ezSetY(548);	
		$la_data_titfecE[1]=array('fechaE'=>'FECHA DE EGRESO');
		$la_columna=array('fechaE'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'xPos'=>350,
						 'cols'=>array('fechaE'=>array('justification'=>'center','width'=>105))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_titfecE,$la_columna,'',$la_config);
		$as_fechaE=substr($as_fechaE,0,10);  
		if ($as_fechaE=="1900-01-01")
		{
		  $ls_anoE= "";
		  $ls_mesE= "";
		  $ls_diaE= "";
		}
		else
		{
		  $ls_anoE= substr($as_fechaE,0,4);
		  $ls_mesE= substr($as_fechaE,5,2);
		  $ls_diaE= substr($as_fechaE,8,2);
		}
		$la_data_fechaE[1]=array('dia'=>'DIA','mes'=>'MES','ano'=>'A?O');
		$la_data_fechaE[2]=array('dia'=>$ls_diaE,'mes'=>$ls_mesE,'ano'=>$ls_anoE);
		$la_columna=array('dia'=>'','mes'=>'','ano'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'xPos'=>'350',
						 'cols'=>array('dia'=>array('justification'=>'center','width'=>35),
						 			   'mes'=>array('justification'=>'center','width'=>35),
						 			   'ano'=>array('justification'=>'center','width'=>35))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_fechaE,$la_columna,'',$la_config); 
		unset($la_data_fechaE);
		unset($la_data_titfecE);
		unset($la_data_fechaI);
		unset($la_data_emp);
		unset($la_data_tit);
		unset($la_data_datos);
	
	}
	///------------------------------------------------------------------------------------------------------------------------------

	///------------------------------------------------------------------------------------------------------------------------------
    function uf_print_encabezado_salario($as_ano1,$io_pdf)
	{
		global $io_pdf;
	   $io_pdf->ezSetY(515);
	   $la_data_t[1]=array('titulo'=>'<b>SALARIOS DEVENGADOS</b>');
	   $la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla						 
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>500))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_t,$la_columna,'',$la_config); 
		$la_data_ano[1]=array('mesano'=>'<b>MESES / A?OS</b>','ano1'=>$as_ano1-5,
												'ano2'=>$as_ano1-4,
												'ano3'=>$as_ano1-3,
												'ano4'=>$as_ano1-2,
												'ano5'=>$as_ano1-1,
												'ano6'=>$as_ano1);
		$la_columna=array('mesano'=>'','ano1'=>'',
		                               'ano2'=>'',
									   'ano3'=>'',
									   'ano4'=>'',
									   'ano5'=>'',
									   'ano6'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla						 
						 'cols'=>array('mesano'=>array('justification'=>'center','width'=>80),
						 			   'ano1'=>array('justification'=>'center','width'=>70),
						 			   'ano2'=>array('justification'=>'center','width'=>70),
									   'ano3'=>array('justification'=>'center','width'=>70),
									   'ano4'=>array('justification'=>'center','width'=>70),
									   'ano5'=>array('justification'=>'center','width'=>70),
									   'ano6'=>array('justification'=>'center','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_ano,$la_columna,'',$la_config); 
		unset($la_data_t);
		unset($la_data_ano);
	}	

	//-------------------------------------------------------------------------------------------------------------------------------
	function uf_detalle_salario($as_data,$as_data2,$as_data3,$as_data4,$as_data5,$as_data6,$io_pdf)
	{
		global $io_pdf;
	   $io_pdf->ezSetY(490);
       $la_columna=array('periodo'=>'','sueldo1'=>'');
       $la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xPos'=>131, // Orientaci?n de la tabla						 
						 'cols'=>array('periodo'=>array('justification'=>'left','width'=>80),
						 			   'sueldo1'=>array('justification'=>'center','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($as_data,$la_columna,'',$la_config);
		unset($as_data);
		
		$io_pdf->ezSetY(490);
	   	$la_columna=array('sueldo2'=>'');
       	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xPos'=>241, // Orientaci?n de la tabla						 
						 'cols'=>array('sueldo2'=>array('justification'=>'center','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($as_data2,$la_columna,'',$la_config);
		unset($as_data2);
		
		$io_pdf->ezSetY(490);
	   	$la_columna=array('sueldo3'=>'');
       	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xPos'=>311, // Orientaci?n de la tabla						 						 
						 'cols'=>array('sueldo3'=>array('justification'=>'center','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($as_data3,$la_columna,'',$la_config);
		unset($as_data3);
		
		$io_pdf->ezSetY(490);
	   	$la_columna=array('sueldo4'=>'');
       	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xPos'=>381, // Orientaci?n de la tabla						 					 
						 'cols'=>array('sueldo4'=>array('justification'=>'center','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($as_data4,$la_columna,'',$la_config);
		unset($as_data4);
		
		$io_pdf->ezSetY(490);
		$la_columna=array('sueldo5'=>'');
       	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xPos'=>451, // Orientaci?n de la tabla						 				 
						 'cols'=>array('sueldo5'=>array('justification'=>'center','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($as_data5,$la_columna,'',$la_config);
		unset($as_data5);
		
		$io_pdf->ezSetY(490);
		$la_columna=array('sueldo6'=>'');
       	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xPos'=>521, // Orientaci?n de la tabla						 					 
						 'cols'=>array('sueldo6'=>array('justification'=>'center','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($as_data6,$la_columna,'',$la_config);
		unset($as_data6);		
  
  }
	//-------------------------------------------------------------------------------------------------------------------------------

	//-------------------------------------------------------------------------------------------------------------------------------
	function declaracion($as_dirtalhum,$as_ceddirtalhum,$io_pdf)
	{
		global $io_pdf;
        $io_pdf->ezSetY(210);
   		$la_data_obs[1]=array('obs'=>'OBSERVACIONES:');
		$la_data_obs[2]=array('obs'=>'');
		$la_data_obs[3]=array('obs'=>'');		
		$la_columna=array('obs'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('obs'=>array('justification'=>'left','width'=>500))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_obs,$la_columna,'',$la_config);
		///-------------------------------------tabla de observaci?n---------------------------------------------------------------
		
   		$la_data_dec[1]=array('declaracion'=>'<b>DECLARACION JURADA</b>');
		$la_data_dec[2]=array('declaracion'=>'CERTIFICO BAJO FE DE JURAMENTO QUE LA INFORMACION QUE ANTECEDE ES CIERTA EN TODAS SUS PARTES');			
		$la_columna=array('declaracion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('declaracion'=>array('justification'=>'center','width'=>500))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_dec,$la_columna,'',$la_config);
   ///-----------------------------------------tabla de declaraci?n---------------------------------------------------------------
        $la_data_frima[1]=array('nombre'=>'APELLIDOS Y NOMBRE DEL FIRMANTE');				
		$la_columna=array('nombre'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'xPos'=>'405',
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>350))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_frima,$la_columna,'',$la_config);
      //--------------------------------------tabla titulo del firmante------------------------------------------------------------
		if((trim($as_dirtalhum)=="")||(trim($as_dirtalhum)=="-"))
		{
			$ls_nomrep=$_SESSION["la_empresa"]["nomrep"];
			$ls_cedula=$_SESSION["la_empresa"]["cedrep"];
//			$ls_cedula=number_format($ls_cedula,0,",",".");		
			if ($ls_nomrep=="")
			{
			  $ls_nomrep="";
			}
			if ($ls_cedula=="")
			{
			  $ls_cedula="";
			}
			$ls_cargo=$_SESSION["la_empresa"]["cargorep"];
		}
		else
		{
			$ls_nomrep=$as_dirtalhum;
   		    $ls_cargo="Director de Talento Humano";
			$ls_cedula=$as_ceddirtalhum;
		}		
	    $la_data_frima2[1]=array('firma'=>'');	
		$la_data_frima2[2]=array('firma'=>$ls_nomrep);			
		$la_columna=array('firma'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'xPos'=>'405',
						 'cols'=>array('firma'=>array('justification'=>'center','width'=>350))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_frima2,$la_columna,'',$la_config);
      //--------------------------------------tabla del firante------------------------------------------------------------
	    $io_pdf->ezSetY(100);
        $la_data_titulo2[1]=array('cargo'=>'CARGO QUE OCUPA','lugar'=>'LUGAR','fecha'=>'FECHA');				
		$la_columna=array('cargo'=>'','lugar'=>'','fecha'=>'');	
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'xPos'=>'405',
						 'cols'=>array('cargo'=>array('justification'=>'center','width'=>119),
						 			   'lugar'=>array('justification'=>'center','width'=>116),
						 			   'fecha'=>array('justification'=>'center','width'=>116))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_titulo2,$la_columna,'',$la_config);
		//----------------------------------tabla titulo Cargo, lugar y fecha-------------------------------------------------
		 $ls_ciuemp=$_SESSION["la_empresa"]["ciuemp"];	
		 $la_data_cont[1]=array('cargo1'=>'','lugar1'=>'');	
		 $la_data_cont[2]=array('cargo1'=>$ls_cargo,'lugar1'=>$ls_ciuemp);				
		 $la_columna=array('cargo1'=>'','lugar1'=>'');	
		 $la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'xPos'=>'289',
						 'cols'=>array('cargo1'=>array('justification'=>'center','width'=>119),
						 			   'lugar1'=>array('justification'=>'center','width'=>116))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_cont,$la_columna,'',$la_config);
		//----------------------------------tabla que contiene el cargo y lugar ---------------------------------------------
		 $io_pdf->ezSetY(88);
		 $fecha=date("d/m/Y");
	     $ano1= substr($fecha,8,4);		
		 $mes1= substr($fecha,3,2);		
		 $dia1= substr($fecha,0,2);	
		 $la_data_dia[1]=array('dia'=>'DIA','mes'=>'MES','ano'=>'A?O');
		 $la_data_dia[2]=array('dia'=>$dia1,'mes'=>$mes1,'ano'=>$ano1);	 		
		 $la_columna=array('dia'=>'','mes'=>'','ano'=>'');
		 $la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'xPos'=>'289',
						 'cols'=>array('dia'=>array('justification'=>'center','width'=>39),
						 			   'mes'=>array('justification'=>'center','width'=>38),
									   'ano'=>array('justification'=>'center','width'=>39))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_dia,$la_columna,'',$la_config);
		//-------------------------tabla titulo del dia, mes y a?o------------------------------------------------------------
			 
		
		
		//-----------------------------------tabla que contiene la fecha--------------------------------------------------------
		 $io_pdf->ezSetY(149);
		 $la_data_sello[1]=array('sello'=>'FIRMA Y SELLO');
		 $la_data_sello[2]=array('sello'=>'');	
		 $la_data_sello[3]=array('sello'=>'');	
		 $la_data_sello[4]=array('sello'=>'');
		 $la_data_sello[5]=array('sello'=>'');	
		 $la_data_sello[6]=array('sello'=>'');
		 			 		
		 $la_columna=array('sello'=>'');
		 $la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'xPos'=>'410',
						 'cols'=>array('sello'=>array('justification'=>'center','width'=>146))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_sello,$la_columna,'',$la_config);
		//---------------------------------------tabla del sello----------------------------------------------------------------
		 $la_data_cedula[1]=array('ced'=>'C.I Nro.   '.$ls_cedula);				 		
		 $la_columna=array('ced'=>'');
		 $la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'xPos'=>'410',
						 'cols'=>array('ced'=>array('justification'=>'left','width'=>146))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_cedula,$la_columna,'',$la_config);
 //--------------------------------tabla que contiene la cedula del firmante-----------------------------------------------

		unset($la_data_cedula);
		unset($la_data_sello);
		unset($la_data_fecha);
		unset($la_data_dia);
		unset($la_data_cont);
		unset($la_data_titulo2);
		unset($la_data_frima2);
		unset($la_data_frima);
		unset($la_data_dec);
		unset($la_data_obs);	   
	   //----------------------------rectangulo-pie de pag.------------------------------------------------------------------
	     $pie_1="<b>Este Formulario est? autorizado por el IVSS y v?lido ?nicamente para ser consignado en las oficinas administrativas</b>";
		 $io_pdf->addText(75,55,8,$pie_1); // Agregar el t?tulo
		 $pie_2="<b>EL FORMULARIO Y SU TRAMITACI?N SON COMPLETAMENTE GRATUITOS</b>";
		 $io_pdf->addText(150,45,8,$pie_2); // Agregar el t?tulo
		 $pie_3="www.ivss.gov.ve";
		 $io_pdf->addText(265,35,8,$pie_3); // Agregar el t?tulo
		 	
	   
	   //--------------------------------------------------------------------------------------------------------------------
   }
	//------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------
	function uf_totales($as_total1,$as_total2,$as_total3,$as_total4,$as_total5,$as_total6,$io_pdf)
	{
		global $io_pdf;
      $io_pdf->ezSetY(345);
	   $as_data_total[1]=array('total'=>'<b>TOTALES</b>',
								'total1'=>$as_total1,
								'total2'=>$as_total2,
								'total3'=>$as_total3,
								'total4'=>$as_total4,
								'total5'=>$as_total5,
								'total6'=>$as_total6);	 
	  $la_columna=array('total'=>'',
	                    'total1'=>'',
		                'total2'=>'',
					    'total3'=>'',
					    'total4'=>'',
					    'total5'=>'',
					    'total6'=>'');
       $la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas						 
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla						 
						 'cols'=>array('total'=>array('justification'=>'left','width'=>80),
						 			   'total1'=>array('justification'=>'center','width'=>70),
						 			   'total2'=>array('justification'=>'center','width'=>70),
									   'total3'=>array('justification'=>'center','width'=>70),
									   'total4'=>array('justification'=>'center','width'=>70),
									   'total5'=>array('justification'=>'center','width'=>70),
									   'total6'=>array('justification'=>'center','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($as_data_total,$la_columna,'',$la_config); 
		unset($as_data_total);
   
   }  
	//-------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	include("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
	$io_numero_letra= new class_numero_a_letra();
	//imprime numero con los valore por defecto
	//cambia a minusculas
	$io_numero_letra->setMayusculas(1);
	//cambia a femenino
	$io_numero_letra->setGenero(1);
	//cambia moneda
	$io_numero_letra->setMoneda("Bolivares");
	//cambia prefijo
	$io_numero_letra->setPrefijo("");
	//cambia sufijo
	$io_numero_letra->setSufijo("");
	//imprime numero con los cambios
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();	
	$ls_dirtalhum=$io_report->uf_select_config("SNO","NOMINA","DIR_TALENTO_HUMANO","","C");
	$ls_ceddirtalhum=$io_report->uf_select_config("SNO","NOMINA","CED_DIR_TALENTO_HUMANO","","C");
	    	
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	$ls_codper=$io_fun_nomina->uf_obtenervalor_get("codper",""); 
	$ls_year1=$io_fun_nomina->uf_obtenervalor_get("ano1","");
	$ls_year2=$io_fun_nomina->uf_obtenervalor_get("ano2","");
	$rs_data="";
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_snorh_class_reportbsf.php");
		$io_report=new sigesp_snorh_class_reportbsf();
		$io_numero_letra->setMoneda("Bolivares Fuertes");
	}
	$ls_titulo="<b>CONSTANCIA DE TRABAJO PARA EL IVSS</b>";
	$ls_formato="FORMA: 14-100";
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad("CONSTANCIA de I.V.S.S."); // Seguridad de Reporte
	if($lb_valido)
	{
		$arrResultado=$io_report->uf_salarios_ivss($ls_codper,$ls_year1,$ls_year2,$rs_data); // Obtenemos el detalle del reporte
		$rs_data=$arrResultado['rs_data'];
		$lb_valido=$arrResultado['lb_valido'];
	}
	$li_numrowtot=$io_report->io_sql->num_rows($rs_data);	
	if(($lb_valido==false)||($li_numrowtot==0)) // Existe alg?n error ? no hay registros
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
		$io_pdf->ezSetCmMargins(3.8,1.5,3,3); // Configuraci?n de los margenes en cent?metros
		uf_print_encabezado_pagina($ls_titulo,$ls_formato,$io_pdf);
		uf_print_datos_empresa($io_pdf);			
		$ls_mesaux="01";
		$ls_ano1=$ls_year2;
		$ls_ano2=$ls_year2+1;
		$ls_ano3=$ls_year2+2;
		$ls_ano4=$ls_year2+3;
		$ls_ano5=$ls_year2+4;
		$ls_ano6=$ls_year1;
		$i=1;	
		$totalsue1=0;
		$totalsue2=0;
		$totalsue3=0;
		$totalsue4=0;
		$totalsue5=0;
		$totalsue6=0;
		$sueldoper=0;	
		$ls_valor="SUELDO NORMAL";
		$ls_metodo=$io_report->uf_select_config('SNO','CONFIG','METODO IVSS',$ls_valor,'C');	
		$sueldo1=0;
		$sueldo2=0;
		$sueldo3=0;
		$sueldo4=0;
		$sueldo5=0;
		$sueldo6=0;
		$lb_ck1=0;
		$lb_ck2=0;
		$lb_ck3=0;
		$lb_ck4=0;
		$lb_ck5=0;
		$lb_ck6=0;
		for ($x=1;$x<=12;$x++)
		{
			$nombre_mes=$io_fecha->uf_load_nombre_mes($x);
			$ls_data[$x]["periodo"]=$nombre_mes;
			$ls_data[$x]["sueldo1"]=number_format(0,2,",",".");
			$ls_data2[$x]["sueldo2"]=number_format(0,2,",",".");
			$ls_data3[$x]["sueldo3"]=number_format(0,2,",",".");
			$ls_data4[$x]["sueldo4"]=number_format(0,2,",",".");
			$ls_data5[$x]["sueldo5"]=number_format(0,2,",",".");
			$ls_data6[$x]["sueldo6"]=number_format(0,2,",",".");
		}
		while(!$rs_data->EOF)
		{ 
			$ls_cedper=$rs_data->fields["cedper"];
			$ls_cedper=number_format($ls_cedper,0,",",".");
			$ls_nomper=$rs_data->fields["nomper"];
			$ls_apeper=$rs_data->fields["apeper"];
			$ls_nacper=$rs_data->fields["nacper"];
			$ls_fechaI=$rs_data->fields["fecingper"];
			$ls_fechaE=$rs_data->fields["fecegrper"];         
			$ls_anocur=$rs_data->fields["anocur"];
			$ls_dias=substr($rs_data->fields["dias"],0,2); 
			$ls_mes=$rs_data->fields["mes"];
			$sueper=$rs_data->fields["suebas"];
			$sueintper=$rs_data->fields["sueintper"];
			if ($ls_anocur==$ls_ano1)
			{
				$sueldo1=$sueintper;
				if($ls_valor==$ls_metodo)
				{
					$sueldo1=$sueper;					
				}
				$nombre_mes=$io_fecha->uf_load_nombre_mes($ls_mes);
				$i=intval($ls_mes);
				$ls_data[$i]=array('periodo'=>$nombre_mes,'sueldo1'=>number_format($sueldo1,2,",","."));	
				$totalsue1=$totalsue1+$sueldo1;
				if ($i==12)
				{
					$i=0;
				}
			}
			if ($ls_anocur==$ls_ano2)
			{
				$sueldo2=$sueintper;
				if($ls_valor==$ls_metodo)
				{
					$sueldo2=$sueper;					
				}
				$nombre_mes=$io_fecha->uf_load_nombre_mes($ls_mes);
				$i=intval($ls_mes);
				$ls_data2[$i]=array('sueldo2'=>number_format($sueldo2,2,",","."));	
				$totalsue2=$totalsue2+$sueldo2;
				if ($i==12)
				{
					$i=0;
				}
			}
			if ($ls_anocur==$ls_ano3)
			{
				$sueldo3=$sueintper;
				if($ls_valor==$ls_metodo)
				{
					$sueldo3=$sueper;					
				}
				$nombre_mes=$io_fecha->uf_load_nombre_mes($ls_mes);
				$i=intval($ls_mes);
				$ls_data3[$i]=array('sueldo3'=>number_format($sueldo3,2,",","."));	
				$totalsue3=$totalsue3+$sueldo3;
				if ($i==12)
				{
					$i=0;
				}
			}
			if ($ls_anocur==$ls_ano4)
			{
				$sueldo4=$sueintper;
				if($ls_valor==$ls_metodo)
				{
					$sueldo4=$sueper;					
				}
				$nombre_mes=$io_fecha->uf_load_nombre_mes($ls_mes);
				$i=intval($ls_mes);
				$ls_data4[$i]=array('sueldo4'=>number_format($sueldo4,2,",","."));	
				$totalsue4=$totalsue4+$sueldo4;
				if ($i==12)
				{
					$i=0;
				}
			}
			if ($ls_anocur==$ls_ano5)
			{
				$sueldo5=$sueintper;
				if($ls_valor==$ls_metodo)
				{
					$sueldo5=$sueper;					
				}
				$nombre_mes=$io_fecha->uf_load_nombre_mes($ls_mes);
				$i=intval($ls_mes);
				$ls_data5[$i]=array('sueldo5'=>number_format($sueldo5,2,",","."));
				$totalsue5=$totalsue5+$sueldo5;
				if ($i==12)
				{
					$i=0;
				}
			}
			if ($ls_anocur==$ls_ano6)
			{
				$sueldo6=$sueintper;
				if($ls_valor==$ls_metodo)
				{
					$sueldo6=$sueper;					
				}
				$nombre_mes=$io_fecha->uf_load_nombre_mes($ls_mes);
				$i=intval($ls_mes);
				$ls_data6[$i]=array('sueldo6'=>number_format($sueldo6,2,",","."));
				$totalsue6=$totalsue6+$sueldo6;
				if ($i==12)
				{
					$i=0;
				}
			}
			$i++;
			$rs_data->MoveNext();
		}//fin del while
		datos_empleados($ls_nomper,$ls_apeper,$ls_cedper,$ls_nacper,$ls_fechaI,$ls_fechaE,$io_pdf);
		uf_print_encabezado_salario($ls_year1,$io_pdf);	
		uf_detalle_salario($ls_data,$ls_data2,$ls_data3,$ls_data4,$ls_data5,$ls_data6,$io_pdf);
		$totalsue1=number_format($totalsue1,2,",",".");
		$totalsue2=number_format($totalsue2,2,",",".");
		$totalsue3=number_format($totalsue3,2,",",".");
		$totalsue4=number_format($totalsue4,2,",",".");
		$totalsue5=number_format($totalsue5,2,",",".");
		$totalsue6=number_format($totalsue6,2,",",".");	
		uf_totales($totalsue1,$totalsue2,$totalsue3,$totalsue4,$totalsue5,$totalsue6,$io_pdf);	
		declaracion($ls_dirtalhum,$ls_ceddirtalhum,$io_pdf);
	    if($lb_valido==false) // Si hubo alg?n error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");		
		}
		else
		{		  
		  $io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi?n de los n?meros de p?gina
		  $io_pdf->ezStream(); // Mostramos el reporte		
		}
		unset($io_pdf);
		
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);	
?> 