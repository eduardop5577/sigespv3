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
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    if(!array_key_exists("la_logusr",$_SESSION))
    {
            print "<script language=JavaScript>";
            print "close();";
            print "</script>";		
    }
	
    //--------------------------------------------------------------------------------------------------------------------------------
    function uf_print_encabezado_pagina($as_titulo,$io_pdf)
    {
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //       Function: uf_print_encabezadopagina
            //		    Acess: private 
            //	    Arguments: as_titulo // T�tulo del Reporte
            //	    		   as_periodo_comp // Descripci�n del periodo del comprobante
            //	    		   as_fecha_comp // Descripci�n del per�odo de la fecha del comprobante 
            //	    		   io_pdf // Instancia de objeto pdf
            //    Description: funci�n que imprime los encabezados por p�gina
            //	   Creado Por: Ing.Yozelin Barrag�n
            // Fecha Creaci�n: 20/09/2006 
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            global $io_pdf;
            $io_encabezado=$io_pdf->openObject();
            $io_pdf->saveState();
            $io_pdf->line(10,40,578,40);
            $io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],40,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo

            $io_pdf->line(40,710,550,710);
            $li_tm=$io_pdf->getTextWidth(11,"SALDO DEL COMPROMISO DEL IVA 2018");
            $tm=306-($li_tm/2);
            $io_pdf->addText($tm,694,11,"SALDO DEL COMPROMISO DEL IVA 2018"); // Agregar el t�tulo
            $io_pdf->line(40,685,550,685);
            $io_pdf->addText(40,750,9,"REPUBLICA BOLIVARIANA DE VENEZUELA"); // Agregar el t�tulo
            $io_pdf->addText(40,740,9,"HIDROCAPITAL"); // Agregar el t�tulo
            $io_pdf->addText(40,730,9,"PRESUPUESTO"); // Agregar el t�tulo
            $io_pdf->addText(500,750,9,"Fecha: ".date("d/m/Y"));// Agrerar el nombre de la base de datos actual
            $io_pdf->addText(500,740,9,"Hora: ".date("h:i a")); // Agregar la Fecha
            $io_pdf->addText(500,730,9,"Pagina: "); // Agregar la hora
       	    $io_pdf->ezStartPageNumbers(560,730,9,'','',1); // Numero de Pagina
            $io_pdf->restoreState();
            $io_pdf->closeObject();
            $io_pdf->addObject($io_encabezado,'all');
    }// end function uf_print_encabezadopagina
    //--------------------------------------------------------------------------------------------------------------------------------
	
    //--------------------------------------------------------------------------------------------------------------------------------
    function uf_print_cabecera($as_procede,$as_comprobante,$as_nomprobene,$as_tipo_destino,$ls_fecha,$ls_rifprobene,$ls_descripcion,$ls_total,$ls_denfuefin,$io_pdf)
    {
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //       Function: uf_print_cabecera
            //		   Access: private 
            //	    Arguments: as_procede // procede
            //	    		   as_comprobante // comprobante
            //                 as_nomprobene   // nombre del proveedor
            //	    		   io_pdf // Objeto PDF
            //    Description: funci�n que imprime la cabecera de cada p�gina
            //	   Creado Por: Ing.Yozelin Barrag�n
            // Fecha Creaci�n: 20/09/2006 
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            global $io_pdf;
            if($as_tipo_destino=="P")
            {
                    $ls_titulo="Proveedor";
            }
            if($as_tipo_destino=="B")
            {
                    $ls_titulo="Beneficiario";
            }
            if($as_tipo_destino=="-")
            {
                    $ls_titulo="Ninguno";
            }
            $la_data=array(array('name'=>'<b>Dependencia: </b>  C.A. Hidrologica de la Region Capital, HIDROCAPITAL'),
						   array('name'=>'<b>Compromiso:</b>  '.$as_comprobante.'         Fondo: '.$ls_denfuefin),
						   array('name'=>'<b>Fecha:</b>  '.$ls_fecha.'      <b>Status:</b>  Aprobado'),
                           array('name'=>'<b>'.$ls_titulo.'</b>  '.$ls_rifprobene.' - '. $as_nomprobene.''),
                           array('name'=>'<b>Descripcion</b>  '.$ls_descripcion.''),
                           array('name'=>'  '),
                           array('name'=>'<b>Monto Inicial</b>  '.$ls_total.''));
            $la_columna=array('name'=>'');
            $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                             'showLines'=>0, // Mostrar L�neas
                                             'fontSize' => 8, // Tama�o de Letras
                                             'shaded'=>0, // Sombra entre l�neas
                                             'shadeCol'=>array(0.8,0.8,0.8),
                                             'shadeCol2'=>array(0.8,0.8,0.8), // Color de la sombra
                                             'xOrientation'=>'center', // Orientaci�n de la tabla
                                             'width'=>520, // Ancho de la tabla
                                             'maxWidth'=>520, // Ancho M�ximo de la tabla
                                             'xPos'=>299); // Orientaci�n de la tabla 
            $io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
            $la_data=array(array('name'=>'__________________________________________________________________________________________________________________'));
            $la_columna=array('name'=>'');
            $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                             'showLines'=>0, // Mostrar L�neas
                                             'fontSize' => 8, // Tama�o de Letras
                                             'shaded'=>0, // Sombra entre l�neas
                                             'shadeCol'=>array(0.8,0.8,0.8),
                                             'shadeCol2'=>array(0.8,0.8,0.8), // Color de la sombra
                                             'xOrientation'=>'center', // Orientaci�n de la tabla
                                             'width'=>520, // Ancho de la tabla
                                             'maxWidth'=>520, // Ancho M�ximo de la tabla
                                             'xPos'=>299); // Orientaci�n de la tabla 
            $io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
    }// end function uf_print_cabecera
    //--------------------------------------------------------------------------------------------------------------------------------

    //--------------------------------------------------------------------------------------------------------------------------------
    function uf_print_cabecera_programatica($as_programatica,$as_denestpro,$io_pdf)
    {
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //       Function: uf_print_cabecera
            //		   Access: private 
            //	    Arguments: as_programatica // programatica del comprobante
            //	    		   as_denestpro5 // denominacion de la programatica del comprobante
            //	    		   io_pdf // Objeto PDF
            //    Description: funci�n que imprime la cabecera de cada p�gina
            //	   Creado Por: Ing.Yozelin Barrag�n
            // Fecha Creaci�n: 20/09/2006 
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
            global $io_pdf;
            if ($_SESSION["la_empresa"]["estmodest"] == 2)
            {
             $la_data=array(array('name'=>'<b>Programatica    </b>'.$as_programatica.'' ),
                            array('name'=>'<b></b>'.$as_denestpro.''));
            }
            else
            {
             $ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
             $ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
             $ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
             $la_data=array(array('name'=>'<b>Estructura Presupuestaria    </b>'),
                            array('name'=>'                                                  '.substr($as_programatica,0,$ls_loncodestpro1).'    '.$as_denestpro[0]),
                                            array('name'=>'                                                  '.substr($as_programatica,$ls_loncodestpro1,$ls_loncodestpro2).'     '.$as_denestpro[1]),
                                            array('name'=>'                                                  '.substr($as_programatica,$ls_loncodestpro1+$ls_loncodestpro2,$ls_loncodestpro3).'     '.$as_denestpro[2]));
            }				
            $la_columna=array('name'=>'');
            $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                             'showLines'=>0, // Mostrar L�neas
                                             'fontSize' => 9, // Tama�o de Letras
                                             'shaded'=>2, // Sombra entre l�neas
                                             'shadeCol'=>array(0.9,0.9,0.9),
                                             'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
                                             'xOrientation'=>'center', // Orientaci�n de la tabla
                                             'width'=>520, // Ancho de la tabla
                                             'maxWidth'=>520, // Ancho M�ximo de la tabla
                                             'xPos'=>299); // Orientaci�n de la tabla 
            $io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
    }// end function uf_print_cabecera
    //--------------------------------------------------------------------------------------------------------------------------------

    //--------------------------------------------------------------------------------------------------------------------------------
    function uf_print_detalle($la_data,$io_pdf)
    {
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //       Function: uf_print_detalle
            //		    Acess: private 
            //	    Arguments: la_data // arreglo de informaci�n
            //	   			   io_pdf // Objeto PDF
            //    Description: funci�n que imprime el detalle
            //	   Creado Por: Ing. Yesenia Moreno
            // Fecha Creaci�n: 21/04/2006 
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            global $io_pdf;
            $la_config=array('showHeadings'=>1, // Mostrar encabezados
                             'fontSize' => 6, // Tama�o de Letras
                             'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
                             'showLines'=>0, // Mostrar L�neas
                             'shaded'=>0, // Sombra entre l�neas
                             'colGap'=>2, // separacion entre tablas
                             'width'=>520, // Ancho de la tabla
                             'maxWidth'=>520, // Ancho M�ximo de la tabla
                             'xOrientation'=>'center', // Orientaci�n de la tabla
                             'xPos'=>299, // Orientaci�n de la tabla
                             'cols'=>array('cuenta'=>array('justification'=>'center','width'=>40), // Justificaci�n y ancho de la 
                                           'fecha'=>array('justification'=>'center','width'=>40), // Justificaci�n y ancho de la 
                                           'comprobante'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la 
                                           'descripcion'=>array('justification'=>'left','width'=>180), // Justificaci�n 
                                           'monto'=>array('justification'=>'right','width'=>50), // Justificaci�n y ancho de la 
                                           'saldo'=>array('justification'=>'right','width'=>50), // Justificaci�n y ancho de la 
                                           'numsol'=>array('justification'=>'right','width'=>60), // Justificaci�n y ancho de la 
                                           'numdoc'=>array('justification'=>'right','width'=>50))); // Justificaci�n y ancho de la 
            $la_columnas=array('cuenta'=>'<b></b>',
                               'fecha'=>'<b>Fecha</b>',
                               'comprobante'=>'<b>Codigo</b>',
                               'descripcion'=>'<b>Concepto</b>',
                               'monto'=>'<b>Monto</b>',
                               'saldo'=>'<b>Saldo</b>',
                               'numsol'=>'<b>Orden de Pago</b>',
                               'numdoc'=>'<b>Cheque</b>');
            $io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
    }// end function uf_print_detalle
    //--------------------------------------------------------------------------------------------------------------------------------

    //--------------------------------------------------------------------------------------------------------------------------------
    function uf_print_total_programatica($ad_totalcomprometer,$ad_totalcausado,$ad_totalpagado,$io_pdf)
    {
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //       Function : uf_print_total_programatica
            //		    Acess : private
            //	    Arguments : ad_totalprogramatica // Total Programatica
            //    Description : funci�n que imprime el fin de la cabecera de cada p�gina
            //	   Creado Por : Ing. Yozelin Barragan
            // Fecha Creaci�n : 20/09/2006
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            global $io_pdf;
            $la_datat=array(array('name'=>'____________________________________________________________________________________________________________________'));
            $la_columna=array('name'=>'');
            $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                             'fontSize' => 8, // Tama�o de Letras
                                             'showLines'=>0, // Mostrar L�neas
                                             'shaded'=>0, // Sombra entre l�neas
                                             'xOrientation'=>'center', // Orientaci�n de la tabla
                                             'xPos'=>300, // Orientaci�n de la tabla
                                             'width'=>530); // Ancho M�ximo de la tabla
            $io_pdf->ezTable($la_datat,$la_columna,'',$la_config);

            $la_data[]=array('comprobante'=>' ','fecha'=>'<b>SubTotal</b> ','comprometido'=>$ad_totalcomprometer,
                              'causado'=>$ad_totalcausado,'pagado'=>$ad_totalpagado);
            $la_columnas=array('comprobante'=>'','fecha'=>'','comprometido'=>'','causado'=>'','pagado'=>'');
            $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                             'fontSize' => 8, // Tama�o de Letras
                                             'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
                                             'showLines'=>0, // Mostrar L�neas
                                             'shaded'=>0, // Sombra entre l�neas
                                             'colGap'=>2, // separacion entre tablas
                                             'width'=>520, // Ancho de la tabla
                                             'maxWidth'=>520, // Ancho M�ximo de la tabla
                                             'xOrientation'=>'center', // Orientaci�n de la tabla
                                             'xPos'=>299, // Orientaci�n de la tabla
                                             'cols'=>array('comprobante'=>array('justification'=>'center','width'=>140), // Justificaci�n y ancho de la 
                                                                       'fecha'=>array('justification'=>'center','width'=>80), // Justificaci�n y ancho de la 
                                                                       'comprometido'=>array('justification'=>'right','width'=>100), // Justificaci�n 
                                                                       'causado'=>array('justification'=>'right','width'=>100), // Justificaci�n y ancho de la 
                                                                       'pagado'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la 
            $io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
            $la_data=array(array('name'=>''));
            $la_columna=array('name'=>'');
            $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                             'showLines'=>0, // Mostrar L�neas
                                             'shaded'=>0, // Sombra entre l�neas
                                             'width'=>550, // Ancho M�ximo de la tabla
                                             'xOrientation'=>'center'); // Orientaci�n de la tabla
            $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
    }// end function uf_print_total_programatica
    //--------------------------------------------------------------------------------------------------------------------------------

    //--------------------------------------------------------------------------------------------------------------------------------
    function uf_print_pie_cabecera($ad_total_comprometer,$ad_total_causado,$ad_total_pagado,$io_pdf,$ls_titulo)
    {
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //       Function : uf_print_pie_cabecera
            //		    Acess : private
            //	    Arguments : ad_total // Total General
            //    Description : funci�n que imprime el fin de la cabecera de cada p�gina
            //	   Creado Por : Ing. Yozelin Barragan
            // Fecha Creaci�n : 20/09/2006
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            global $io_pdf;
            $la_datat=array(array('name'=>'____________________________________________________________________________________________________________________'));
            $la_columna=array('name'=>'');
            $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                             'fontSize' => 8, // Tama�o de Letras
                                             'showLines'=>0, // Mostrar L�neas
                                             'shaded'=>0, // Sombra entre l�neas
                                             'xOrientation'=>'center', // Orientaci�n de la tabla
                                             'xPos'=>300, // Orientaci�n de la tabla
                                             'width'=>530); // Ancho M�ximo de la tabla
            $io_pdf->ezTable($la_datat,$la_columna,'',$la_config);

            $la_data[1]=array('comprobante'=>' ','fecha'=>'<b>'.$ls_titulo.'</b> ','comprometido'=>$ad_total_comprometer,
                              'causado'=>$ad_total_causado,'pagado'=>$ad_total_pagado);
            $la_columnas=array('comprobante'=>'','fecha'=>'','comprometido'=>'','causado'=>'','pagado'=>'');
            $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                             'fontSize' => 8, // Tama�o de Letras
                                             'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
                                             'showLines'=>0, // Mostrar L�neas
                                             'shaded'=>0, // Sombra entre l�neas
                                             'colGap'=>2, // separacion entre tablas
                                             'width'=>520, // Ancho de la tabla
                                             'maxWidth'=>520, // Ancho M�ximo de la tabla
                                             'xOrientation'=>'center', // Orientaci�n de la tabla
                                             'xPos'=>299, // Orientaci�n de la tabla
                                             'cols'=>array('comprobante'=>array('justification'=>'center','width'=>140), // Justificaci�n y ancho de la 
                                                                       'fecha'=>array('justification'=>'center','width'=>80), // Justificaci�n y ancho de la 
                                                                       'comprometido'=>array('justification'=>'right','width'=>100), // Justificaci�n 
                                                                       'causado'=>array('justification'=>'right','width'=>100), // Justificaci�n y ancho de la 
                                                                       'pagado'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la 
            $io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
            $la_data=array(array('name'=>''));
            $la_columna=array('name'=>'');
            $la_config=array('showHeadings'=>0, // Mostrar encabezados
                                             'showLines'=>0, // Mostrar L�neas
                                             'shaded'=>0, // Sombra entre l�neas
                                             'width'=>550, // Ancho M�ximo de la tabla
                                             'xOrientation'=>'center'); // Orientaci�n de la tabla
            $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
    }// end function uf_print_pie_cabecera	                                                                    
    //-----------------------------------------------------------------------------------------------------------------------------
    require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
    require_once("sigesp_spg_funciones_reportes.php");
    $io_function_report = new sigesp_spg_funciones_reportes();
    require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
    $io_function=new class_funciones() ;
    require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
    $io_fecha = new class_fecha();
    require_once("sigesp_spg_reportes_class.php");
    $io_report = new sigesp_spg_reportes_class();
		
    //------------------------------------------------------------------------------------------------------------------------------		

    //--------------------------------------------------  Par�metros para Filtar el Reporte  --------------------------------------
    $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
    $ls_comprobante  = $_GET["txtcomprobante"];
    $ls_procede  = $_GET["txtprocede"];
    $ldt_fecha  = $_GET["txtfecha"];
    $ldt_fecdes = $_GET["txtfecdes"];
    $ldt_fechas = $_GET["txtfechas"];	
		
    /////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////////////////////////////////////////////////
    $ls_desc_event="Solicitud de Reporte Ejecucion de Compromisos  Comprobante  ".$ls_comprobante." ,Procede  ".$ls_procede." , Fecha ".$ldt_fecha;
    $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_ejecucion_compromisos.html",$ls_desc_event);
   ////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////////////////////////////////////////////////////
   //----------------------------------------------------  Par�metros del encabezado  --------------------------------------------
    $ldt_fecdes_cab=$io_function->uf_convertirfecmostrar($ldt_fecdes);
    $ldt_fechas_cab=$io_function->uf_convertirfecmostrar($ldt_fechas);

    $ls_titulo=" <b>EJECUCION DE COMPROMISOS</b> ";       
    //--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
    $lb_valido=$io_report->uf_spg_select_reportes_ejecucion_compromiso($ls_procede,$ls_comprobante,$ldt_fecha,$ldt_fecdes,$ldt_fechas);
    if($lb_valido==false) // Existe alg�n error � no hay registros
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
        $io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
        $io_pdf->ezSetCmMargins(4.5,3,3,3); // Configuraci�n de los margenes en cent�metros
        uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la p�gina
        $li_tot=$io_report->dts_cab->getRowCount("programatica");
        $ld_total_comprometer=0;
        $ld_total_causado=0;
        $ld_total_pagado=0;
        $ls_procede_next="";
        $ls_comprobante_next="";
        $ls_nomprobene_next="";
        $ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
        $ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
        $ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
        $ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
        $ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
        $li_contcuenta=1;
        for($li_i=1;$li_i<=$li_tot;$li_i++)
        {
            $thisPageNum=$io_pdf->ezPageCount;
            $ls_programatica=$io_report->dts_cab->data["programatica"][$li_i];
            $ls_spg_cuenta=$io_report->dts_cab->data["spg_cuenta"][$li_i];
            $ls_comprobante=$io_report->dts_cab->data["comprobante"][$li_i];
            $ls_procede=$io_report->dts_cab->data["procede"][$li_i];
            $ldt_fecha=$io_report->dts_cab->data["fecha"][$li_i];
            $ls_fecha=$io_function->uf_convertirfecmostrar($ldt_fecha);
            $ls_nombene=$io_report->dts_cab->data["nombene"][$li_i];
            $ls_nompro=$io_report->dts_cab->data["nompro"][$li_i];
            $ls_tipo_destino=$io_report->dts_cab->data["tipo_destino"][$li_i];
            $ls_codban=$io_report->dts_cab->data["codban"][$li_i];
            $ls_ctaban=$io_report->dts_cab->data["ctaban"][$li_i];
            $ls_documento=$io_report->dts_cab->data["documento"][$li_i];
            $ls_procede_doc=$io_report->dts_cab->data["procede_doc"][$li_i];
            $ls_rifpro=$io_report->dts_cab->data["rifpro"][$li_i];
            $ls_rifbene=$io_report->dts_cab->data["rifbene"][$li_i];
            $ls_descripcion=$io_report->dts_cab->data["descripcion"][$li_i];
            $ls_codfuefin=$io_report->dts_cab->data["codfuefin"][$li_i];
            $ls_denfuefin=$io_report->dts_cab->data["denfuefin"][$li_i];
            $ls_total=$io_report->dts_cab->data["total"][$li_i];
            $ls_total=number_format($ls_total,2,",",".");
			if($ls_codfuefin=="--")
				$ls_denfuefin="--";
            if($ls_tipo_destino=="P")
            {
                $ls_nomprobene=$ls_nompro;
                $ls_rifprobene=$ls_rifpro;
            }
            if($ls_tipo_destino=="B")
            {
                    $ls_nomprobene=$ls_nombene;
                	$ls_rifprobene=$ls_rifbene;
           }
            if($ls_tipo_destino=="-")
            {
                    $ls_nomprobene="";
                	$ls_rifprobene="";
            }
            if(($ls_procede_next!=$ls_procede)&&($ls_comprobante_next!=$ls_comprobante)&&($ls_nomprobene_next!=$ls_nomprobene))
            {
                uf_print_cabecera($ls_procede,$ls_comprobante,$ls_nomprobene,$ls_tipo_destino,$ls_fecha,$ls_rifprobene,$ls_descripcion,$ls_total,$ls_denfuefin,$io_pdf); // Imprimimos la cabecera 
                $ls_estcla=substr($ls_programatica,-1);
                $ls_codestpro1=substr($ls_programatica,0,25);
                $ls_denestpro1="";
                $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);
                $ls_denestpro1=$arrResultado['as_denestpro1'];
                $lb_valido=$arrResultado['lb_valido'];
                if($lb_valido)
                {
                  $ls_denestpro1=$ls_denestpro1;
                }
                $ls_codestpro2=substr($ls_programatica,25,25);
                if($lb_valido)
                {
                  $ls_denestpro2="";
                  $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
                  $ls_denestpro2=$arrResultado['as_denestpro2'];
                  $lb_valido=$arrResultado['lb_valido'];
                  $ls_denestpro2=$ls_denestpro2;
                }
                $ls_codestpro3=substr($ls_programatica,50,25);
                if($lb_valido)
                {
                  $ls_denestpro3="";
                  $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
                  $ls_denestpro3=$arrResultado['as_denestpro3'];
                  $lb_valido=$arrResultado['lb_valido'];
                  $ls_denestpro3=$ls_denestpro3;
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
                          $ls_denestpro4=$ls_denestpro4;
                        }
                        $ls_codestpro5=substr($ls_programatica,100,25);
                        if($lb_valido)
                        {
                          $ls_denestpro5="";
                          $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
                          $ls_denestpro5=$arrResultado['as_denestpro5'];
                          $lb_valido=$arrResultado['lb_valido'];
                          $ls_denestpro5=$ls_denestpro5;
                        }
                        $ls_denestpro=trim($ls_denestpro1)." , ".trim($ls_denestpro2)." , ".trim($ls_denestpro3)." , ".trim($ls_denestpro4)." , ".trim($ls_denestpro5);
                        $ls_programatica=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
                }
                else
                {
                        $ls_denestpro = array();
                        $ls_denestpro[0]=$ls_denestpro1;
                        $ls_denestpro[1]=$ls_denestpro2;
                        $ls_denestpro[2]=$ls_denestpro3;
                        $ls_programatica=substr($ls_codestpro1,-$ls_loncodestpro1).substr($ls_codestpro2,-$ls_loncodestpro2).substr($ls_codestpro3,-$ls_loncodestpro3);
                }
                
               // uf_print_cabecera_programatica($ls_programatica,$ls_denestpro,$io_pdf); 
            }	
            $ls_programatica=$io_report->dts_cab->data["programatica"][$li_i];
            $ls_procede_next=$ls_procede;
            $ls_comprobante_next=$ls_comprobante;
            $ls_nomprobene_next=$ls_nomprobene;
            $lb_valido=$io_report->uf_spg_reportes_ejecucion_compromiso_hidrocapital($ls_procede,$ls_comprobante,$ldt_fecha,
                                                                                     $ldt_fecdes,$ldt_fechas,$ls_spg_cuenta,
                                                                                     $ls_codban,$ls_ctaban,$ls_programatica,
                                                                                     $ls_procede_doc,$ls_documento);
            if($lb_valido)
            {
                $ld_sub_total_comprometer=0;
                $ld_sub_total_causado=0;
                $ld_sub_total_pagado=0;
                $li_totrow_det=$io_report->dts_reporte->getRowCount("spg_cuenta");
				$ld_saldo=0;
                for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
                {
                    $ls_spg_cuenta=trim($io_report->dts_reporte->data["spg_cuenta"][$li_s]);
                    $ls_procede=$io_report->dts_reporte->data["procede"][$li_s];
                    $ls_comprobante=$io_report->dts_reporte->data["comprobante"][$li_s];
                    $ldt_fecha=$io_report->dts_reporte->data["fecha"][$li_s]; 
                    $ldt_fecha=$io_function->uf_convertirfecmostrar($ldt_fecha);
                    $ld_comprometer=$io_report->dts_reporte->data["compromiso"][$li_s];  
                    $ld_causado=$io_report->dts_reporte->data["causado"][$li_s];  
                    $ld_pagado=$io_report->dts_reporte->data["pagado"][$li_s];  	  
                    $ls_descripcion=$io_report->dts_reporte->data["descripcion"][$li_s];  	  
                    $ls_proc_comp=$ls_procede."---".$ls_comprobante;

                    $ld_total_comprometer=$ld_total_comprometer+$ld_comprometer;
                    $ld_total_causado=$ld_total_causado+$ld_causado;
                    $ld_total_pagado=$ld_total_pagado+$ld_pagado;

                    $ld_sub_total_comprometer=$ld_sub_total_comprometer+$ld_comprometer;
                    $ld_sub_total_causado=$ld_sub_total_causado+$ld_causado;
                    $ld_sub_total_pagado=$ld_sub_total_pagado+$ld_pagado;
					if($li_contcuenta==1)
					{
						$ld_saldo=$ld_comprometer;
						$ld_comprometer=number_format($ld_comprometer,2,",",".");
						$ld_causado=number_format($ld_causado,2,",",".");
						$ld_pagado=number_format($ld_pagado,2,",",".");
						$ld_saldoaux=number_format($ld_saldo,2,",",".");
						$la_data[$li_contcuenta]=array('cuenta'=>'','fecha'=>"",'comprobante'=>"",'descripcion'=>"",'monto'=>$ld_comprometer,'saldo'=>$ld_saldoaux,'numsol'=>"",'numdoc'=>"");
					}
					else
					{
						if($ld_comprometer!=0)
						{
							$ld_saldo=$ld_saldo+$ld_comprometer;
							$ld_comprometer=number_format($ld_comprometer,2,",",".");
							$ld_causado=number_format($ld_causado,2,",",".");
							$ld_pagado=number_format($ld_pagado,2,",",".");
							$ld_saldoaux=number_format($ld_saldo,2,",",".");
							$la_data[$li_contcuenta]=array('cuenta'=>'','fecha'=>$ldt_fecha,'comprobante'=>$ls_comprobante,'descripcion'=>$ls_descripcion,'monto'=>$ld_comprometer,'saldo'=>$ld_saldoaux,'numsol'=>"",'numdoc'=>"");
						}
						if($ld_causado>0)
						{
							$ld_saldo=$ld_saldo-$ld_causado;
							$ld_comprometer=number_format($ld_comprometer,2,",",".");
							$ld_causado=number_format($ld_causado,2,",",".");
							$ld_pagado=number_format($ld_pagado,2,",",".");
							$ld_saldoaux=number_format($ld_saldo,2,",",".");
							$la_data[$li_contcuenta]=array('cuenta'=>'Ajuste','fecha'=>$ldt_fecha,'comprobante'=>$ls_comprobante,'descripcion'=>$ls_descripcion,'monto'=>'('.$ld_causado.')','saldo'=>$ld_saldoaux,'numsol'=>$ls_comprobante,'numdoc'=>"");
						}
					}

                    $li_contcuenta++;
                    $ld_comprometer=str_replace('.','',$ld_comprometer);
                    $ld_comprometer=str_replace(',','.',$ld_comprometer);	
                    $ld_causado=str_replace('.','',$ld_causado);
                    $ld_causado=str_replace(',','.',$ld_causado);	
                    $ld_pagado=str_replace('.','',$ld_pagado);
                    $ld_pagado=str_replace(',','.',$ld_pagado);	
                }
                $ld_subtotal_comprometer=$ld_sub_total_comprometer;
                $ld_subtotal_causado=$ld_sub_total_causado;
                $ld_subtotal_pagado=$ld_sub_total_pagado;
                $ld_sub_total_comprometer=number_format($ld_sub_total_comprometer,2,",",".");
                $ld_sub_total_causado=number_format($ld_sub_total_causado,2,",",".");
                $ld_sub_total_pagado=number_format($ld_sub_total_pagado,2,",",".");
                //uf_print_total_programatica($ld_sub_total_comprometer,$ld_sub_total_causado,$ld_sub_total_pagado,$io_pdf);
            }
            if($li_i==$li_tot)
            {
                $ld_total_comprometer=number_format($ld_total_comprometer,2,",",".");
                $ld_total_causado=number_format($ld_total_causado,2,",",".");
                $ld_total_pagado=number_format($ld_total_pagado,2,",",".");

                uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle
              // uf_print_pie_cabecera($ld_total_comprometer,$ld_total_causado,$ld_total_pagado,$io_pdf,"Total Bs.");
            }
            			
        }//for
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
   }
   unset($io_report);
   unset($io_funciones);
   unset($io_function_report);
   unset($io_fecha);
?> 