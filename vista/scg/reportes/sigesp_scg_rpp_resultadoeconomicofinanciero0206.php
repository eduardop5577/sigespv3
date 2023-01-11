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
            print "<script language=+JavaScript>";
            print "close();";
            print "</script>";		
    }
    //--------------------------------------------------------------------------------------------------------------------------------
    function uf_print_encabezado_pagina($as_titulo,$as_moneda,$io_pdf)
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
            global $io_pdf;
            $io_encabezado=$io_pdf->openObject();
            $io_pdf->saveState();
            $io_pdf->line(80,380,80,460);
            //$io_pdf->line(279,380,279,460); 
            //$io_pdf->line(369,380,369,460); 
            $io_pdf->line(459,380,459,460); 
            $io_pdf->line(549,380,549,460);
            $io_pdf->line(639,380,639,460);
            $io_pdf->line(729,380,729,430);
            $io_pdf->line(819,380,819,460);
            $io_pdf->line(909,380,909,430);
            $io_pdf->line(639,430,999,430);
            $io_pdf->addText(25,410,8,"CÓDIGO");
            $io_pdf->addText(15,400,8,"CLASIFICADOR");
            $io_pdf->addText(20,390,8,"ECONÓMICO");
            $io_pdf->addText(250,400,8,"DENOMINACIÓN");
            $io_pdf->addText(475,400,8,"PRESUPUESTO");
            $io_pdf->addText(480,390,8,"APROBADO");
            $io_pdf->addText(565,400,8,"PRESUPUESTO");
            $io_pdf->addText(570,390,8,"MODIFICADO");
            $io_pdf->addText(700,440,8,"ACUMULADO");
            $io_pdf->addText(660,400,8,"PROGRAMADO");
            $io_pdf->addText(755,400,8,"EJECUTADO");
            $io_pdf->addText(870,445,8,"VARIACIÓN EJECUCIÓN");
            $io_pdf->addText(890,435,8,"PROGRAMADO");
            $io_pdf->addText(845,400,8,"ABSOLUTO");
            $io_pdf->addText(925,400,8,"PORCENTUAL");
            $io_pdf->rectangle(10,460,990,120);
            $io_pdf->rectangle(10,382,990,78);

            $li_tm=$io_pdf->getTextWidth(16,$as_titulo);
            $tm=505-($li_tm/2);
            $io_pdf->addText($tm,490,16,$as_titulo); // Agregar el título

            $li_tm=$io_pdf->getTextWidth(10,'<b>'.$as_moneda.'</b>');
            $tm=505-($li_tm/2);
            $io_pdf->addText($tm,480,10,'<b>'.$as_moneda.'</b>'); // Agregar el título

            $io_pdf->restoreState();
            $io_pdf->closeObject();
            $io_pdf->addObject($io_encabezado,'all');

    }// end function uf_print_encabezadopagina
    //--------------------------------------------------------------------------------------------------------------------------------
	
    //--------------------------------------------------------------------------------------------------------------------------------
    function uf_print_titulo_reporte($io_encabezado,$ai_ano,$as_meses,$io_pdf)
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
            global $io_pdf;
            $io_pdf->saveState();
            $io_pdf->ezSetY(570);
            $ls_codemp=$_SESSION["la_empresa"]["codemp"];
            $ls_nombre=$_SESSION["la_empresa"]["nombre"];
            $ls_nomorgads=$_SESSION["la_empresa"]["nomorgads"];
            $ls_codasiona   = $_SESSION['la_empresa']['codasiona'];

            $la_data=array(array('name'=>'<b>CODIGO PRESUPUESTARIO DEL ENTE:     </b>'.'<b>'.$ls_codasiona.'</b>'),
                           array('name'=>'<b>DENOMINACION DEL ENTE:    </b>'.'<b>'.$ls_nombre.'</b>'),
                           array('name'=>'<b>ORGANO DE ADSCRIPCION:    </b>'. $ls_nomorgads.'<b>'."".'</b>'),
                           array('name'=>'<b>FECHA:    </b>'.'<b>'.$ai_ano.'</b>'),
                           array('name'=>'<b>MES:    </b>'.'<b>'.$as_meses.'</b>'));
            $la_columna=array('name'=>'','name'=>'','name'=>'','name'=>'','name'=>'','name'=>'');
            $la_config =array('showHeadings'=>0,     // Mostrar encabezados
                                'fontSize' => 8,       // Tamaño de Letras
                                'titleFontSize' => 8, // Tamaño de Letras de los títulos
                                'showLines'=>0,        // Mostrar Líneas
                                'shaded'=>0,           // Sombra entre líneas
                                'xPos'=>465,//65
                                'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
                                'xOrientation'=>'center', // Orientación de la tabla
                                'width'=>900, // Ancho de la tabla
                                'maxWidth'=>900);
            $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
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
            //		    Acess: private 
            //	    Arguments: la_data // arreglo de información
            //	   			   io_pdf // Objeto PDF
            //    Description: función que imprime el detalle
            //	   Creado Por: Ing. Yozelin Barragán
            // Fecha Creación: 26/06/2006 
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            global $io_pdf;
            $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                             'fontSize' => 7, // Tamaño de Letras
                                             'titleFontSize' => 7,  // Tamaño de Letras de los títulos
                                             'showLines'=>2, // Mostrar Líneas
                                             'shaded'=>0, // Sombra entre líneas
                                             'colGap'=>0, // separacion entre tablas
                                             'width'=>990, // Ancho de la tabla
                                             'maxWidth'=>990, // Ancho Máximo de la tabla
                                             'xOrientation'=>'center', // Orientación de la tabla
                                             'cols'=>array('cuenta'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
                                                                       'denominacion'=>array('justification'=>'left','width'=>380), // Justificación y ancho de la columna
                                                                       'asignado'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
                                                                       'modificado'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
                                                                       'programado'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
                                                                       'ejecutado'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
                                                                       'absoluto'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
                                                                       'porcentual'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
            $la_columnas=array(  'cuenta'=>'',
                                                     'denominacion'=>'',
                                                     'asignado'=>'',
                                                     'modificado'=>'',
                                                     'programado'=>'',
                                                     'ejecutado'=>'',
                                                     'absoluto'=>'',
                                                     'porcentual'=>'');
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
            //	   Creado Por: Ing. Arnaldo USárez
            // Fecha Creación: 10/06/2008 
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            global $io_pdf;
            $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                             'fontSize' => 7, // Tamaño de Letras
                                             'titleFontSize' => 7,  // Tamaño de Letras de los títulos
                                             'showLines'=>2, // Mostrar Líneas
                                             'shaded'=>0, // Sombra entre líneas
                                             'colGap'=>0, // separacion entre tablas
                                             'width'=>990, // Ancho de la tabla
                                             'maxWidth'=>990, // Ancho Máximo de la tabla
                                             'xOrientation'=>'center', // Orientación de la tabla
                                             'cols'=>array('cuenta'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
                                                                       'denominacion'=>array('justification'=>'right','width'=>380), // Justificación y ancho de la columna
                                                                       'asignado'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
                                                                       'modificado'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
                                                                       'programado'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
                                                                       'ejecutado'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
                                                                       'absoluto'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
                                                                       'porcentual'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
            $la_columnas=array(  'cuenta'=>'',
                                                 'denominacion'=>'',
                                                     'asignado'=>'',
                                                     'modificado'=>'',
                                                     'programado'=>'',
                                                     'ejecutado'=>'',
                                                     'absoluto'=>'',
                                                     'porcentual'=>'');
            $io_pdf->ezTable($la_data_tot,$la_columnas,'',$la_config);
    }// end function uf_print_pie_cabecera
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

    require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
    require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
    $io_funciones=new class_funciones();	
    require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
    $io_fecha = new class_fecha();
    //-----------------------------------------------------------------------------------------------------------------------------
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
    
    $ls_ceros=$io_funciones->uf_cerosderecha("",$li_len);		
//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
    $ls_titulo=" <b>RESULTADO ECONÓMICO-FINANCIERO</b>";       
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
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		uf_print_encabezado_pagina($ls_titulo,'(En Bolivares Fuertes)',$io_pdf); // Imprimimos el encabezado de la página
                $io_pdf->ezStartPageNumbers(980,40,10,'','',1); // Insertar el número de página
		$io_pdf->transaction('start'); // Iniciamos la transacción
		$thisPageNum=$io_pdf->ezPageCount;
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
                 
		$thisPageNum=$io_pdf->ezPageCount;
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
                $li_pos1=$li_contador;
                $la_data[$li_contador]=array('cuenta'=>"",
                                                 'denominacion'=>"<b>1.1 INGRESOS CORRIENTES</b>",
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
                            $li_pos2=$li_contador;                            
                            $la_data[$li_contador]=array('cuenta'=>"",
                                                             'denominacion'=>"<b>1.2 GASTOS CORRIENTES</b>",
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
		$ld_total_asignado           = "<b>".number_format($ld_11_asignado-$ld_211_asignado,2,",",".")."</b>";
		$ld_total_modificado         = "<b>".number_format($ld_11_modificado-$ld_211_modificado,2,",",".")."</b>";
		$ld_total_programado         = "<b>".number_format($ld_11_programado -$ld_211_programado,2,",",".")."</b>";
		$ld_total_ejecutado          = "<b>".number_format($ld_11_ejecutado-$ld_211_ejecutado,2,",",".")."</b>";
		$ld_total_absoluto           = "<b>".number_format($ld_total_absoluto,2,",",".")."</b>";
		$ld_total_porcentual         = "<b>".number_format($ld_total_porcentual,2,",",".")." % </b>";
		
		if($ld_total_modificado=='<b>0,00</b>')
		{
			$ld_total_modificado='';
		}
                $ld_absoluto           = ($ld_11_ejecutado - $ld_11_programado);
                $ld_porcentual = 0;
		if ($ld_11_programado > 0)
		{
			$ld_porcentual = ($ld_11_ejecutado/$ld_11_programado)*100;
		}
                $ld_asignado               = "<b>".number_format($ld_11_asignado,2,",",".")."</b>";
                $ld_modificado             = "<b>".number_format($ld_11_modificado,2,",",".")."</b>";
                $ld_programado             = "<b>".number_format($ld_11_programado,2,",",".")."</b>";
                $ld_ejecutado              = "<b>".number_format($ld_11_ejecutado,2,",",".")."</b>";
                $ld_absoluto               = "<b>".number_format($ld_absoluto,2,",",".")."</b>";
                $ld_porcentual             = "<b>".number_format($ld_porcentual,2,",",".")." % </b>";

                if($ld_modificado=='<b>0,00</b>')
                {
                        $ld_modificado='';
                }
                $la_data[$li_pos1]=array('cuenta'=>"",
                                                 'denominacion'=>"<b>1.1 INGRESOS CORRIENTES</b>",
                                                 'asignado'=>$ld_asignado,
                                                 'modificado'=>$ld_modificado,
                                                 'programado'=>$ld_programado,
                                                 'ejecutado'=>$ld_ejecutado,
                                                 'absoluto'=>$ld_absoluto,
                                                 'porcentual'=>$ld_porcentual);
                
                $ld_absoluto           = ($ld_211_ejecutado - $ld_211_programado);
                $ld_porcentual = 0;
		if ($ld_211_programado > 0)
		{
			$ld_porcentual = ($ld_211_ejecutado/$ld_211_programado)*100;
		}
                $ld_asignado               = "<b>".number_format($ld_211_asignado,2,",",".")."</b>";
                $ld_modificado             = "<b>".number_format($ld_211_modificado,2,",",".")."</b>";
                $ld_programado             = "<b>".number_format($ld_211_programado,2,",",".")."</b>";
                $ld_ejecutado              = "<b>".number_format($ld_211_ejecutado,2,",",".")."</b>";
                $ld_absoluto               = "<b>".number_format($ld_absoluto,2,",",".")."</b>";
                $ld_porcentual             = "<b>".number_format($ld_porcentual,2,",",".")." % </b>";

                if($ld_modificado=='<b>0,00</b>')
                {
                        $ld_modificado='';
                }
                
                $la_data[$li_pos2]=array('cuenta'=>"",
                                                 'denominacion'=>"<b>1.2 GASTOS CORRIENTES</b>",
                                                 'asignado'=>$ld_asignado,
                                                 'modificado'=>$ld_modificado,
                                                 'programado'=>$ld_programado,
                                                 'ejecutado'=>$ld_ejecutado,
                                                 'absoluto'=>$ld_absoluto,
                                                 'porcentual'=>$ld_porcentual);
                
		$la_data_tot[1]=array('cuenta'=>"",
							  'denominacion'=>"<b>RESULTADO ECONÓMICO EN CUENTA CORRIENTE : AHORRO/(DESAHORRO)        </b>",
							  'asignado'=>$ld_total_asignado,
							  'modificado'=>$ld_total_modificado,
							  'programado'=>$ld_total_programado,
							  'ejecutado'=>$ld_total_ejecutado,
							  'absoluto'=>$ld_total_absoluto,
							  'porcentual'=>$ld_total_porcentual);

		$io_encabezado=$io_pdf->openObject();
		uf_print_titulo_reporte($io_encabezado,$ldt_fechas,$ls_meses,$io_pdf);
		$io_pdf->ezSetCmMargins(8.025,3,3,3);
		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		uf_print_pie_cabecera($la_data_tot,$io_pdf);
		unset($la_data);
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
                    $li_pos1 = 0;
                    $li_pos2 = 0;
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
                    $ld_total_asignado           = "<b>".number_format($ld_11_asignado-$ld_211_asignado,2,",",".")."</b>";
                    $ld_total_modificado         = "<b>".number_format($ld_11_modificado-$ld_211_modificado,2,",",".")."</b>";
                    $ld_total_programado         = "<b>".number_format($ld_11_programado -$ld_211_programado,2,",",".")."</b>";
                    $ld_total_ejecutado          = "<b>".number_format($ld_11_ejecutado-$ld_211_ejecutado,2,",",".")."</b>";
                    $ld_total_absoluto           = "<b>".number_format($ld_total_absoluto,2,",",".")."</b>";
                    $ld_total_porcentual         = "<b>".number_format($ld_total_porcentual,2,",",".")." % </b>";

                    if($ld_total_modificado=='<b>0,00</b>')
                    {
                            $ld_total_modificado='';
                    }
                    $la_data_tot[1]=array('cuenta'=>"",
                                                              'denominacion'=>"<b>RESULTADO FINANCIERO: SUPERÁVIT/(DÉFICIT))        </b>",
                                                              'asignado'=>$ld_total_asignado,
                                                              'modificado'=>$ld_total_modificado,
                                                              'programado'=>$ld_total_programado,
                                                              'ejecutado'=>$ld_total_ejecutado,
                                                              'absoluto'=>$ld_total_absoluto,
                                                              'porcentual'=>$ld_total_porcentual);

                    uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
                    uf_print_pie_cabecera($la_data_tot,$io_pdf);
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
                    $ld_total_asignado           = "<b>".number_format($ld_11_asignado,2,",",".")."</b>";
                    $ld_total_modificado         = "<b>".number_format($ld_11_modificado,2,",",".")."</b>";
                    $ld_total_programado         = "<b>".number_format($ld_11_programado,2,",",".")."</b>";
                    $ld_total_ejecutado          = "<b>".number_format($ld_11_ejecutado,2,",",".")."</b>";
                    $ld_total_absoluto           = "<b>".number_format($ld_total_absoluto,2,",",".")."</b>";
                    $ld_total_porcentual         = "<b>".number_format($ld_total_porcentual,2,",",".")." % </b>";

                    if($ld_total_modificado=='<b>0,00</b>')
                    {
                            $ld_total_modificado='';
                    }
                    $la_data_tot[1]=array('cuenta'=>"",
                                                              'denominacion'=>"<b>SUPERAVIT FINANCIERO        </b>",
                                                              'asignado'=>$ld_total_asignado,
                                                              'modificado'=>$ld_total_modificado,
                                                              'programado'=>$ld_total_programado,
                                                              'ejecutado'=>$ld_total_ejecutado,
                                                              'absoluto'=>$ld_total_absoluto,
                                                              'porcentual'=>$ld_total_porcentual);

                    uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
                    uf_print_pie_cabecera($la_data_tot,$io_pdf);
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
                            $ld_total_porcentual = ($ld_11_ejecutado/$ld_total_programado)*100;
                    }
                    $ld_total_asignado           = "<b>".number_format($ld_11_asignado,2,",",".")."</b>";
                    $ld_total_modificado         = "<b>".number_format($ld_11_modificado,2,",",".")."</b>";
                    $ld_total_programado         = "<b>".number_format($ld_11_programado,2,",",".")."</b>";
                    $ld_total_ejecutado          = "<b>".number_format($ld_11_ejecutado,2,",",".")."</b>";
                    $ld_total_absoluto           = "<b>".number_format($ld_total_absoluto,2,",",".")."</b>";
                    $ld_total_porcentual         = "<b>".number_format($ld_total_porcentual,2,",",".")." % </b>";

                    if($ld_total_modificado=='<b>0,00</b>')
                    {
                            $ld_total_modificado='';
                    }
                    $la_data_tot[1]=array('cuenta'=>"",
                                                              'denominacion'=>"<b>DEFICIT FINANCIERO        </b>",
                                                              'asignado'=>$ld_total_asignado,
                                                              'modificado'=>$ld_total_modificado,
                                                              'programado'=>$ld_total_programado,
                                                              'ejecutado'=>$ld_total_ejecutado,
                                                              'absoluto'=>$ld_total_absoluto,
                                                              'porcentual'=>$ld_total_porcentual);

                    uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
                    uf_print_pie_cabecera($la_data_tot,$io_pdf);
                    unset($la_data);
                    unset($la_data_tot);
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