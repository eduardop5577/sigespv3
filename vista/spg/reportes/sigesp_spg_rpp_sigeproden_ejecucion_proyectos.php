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
	

//------------------------------------------------------------------------------------------------------------------------------
    require_once ("sigesp_spg_class_tcpdf.php");
    require_once("sigesp_spg_reporte.php");
    $io_report = new sigesp_spg_reporte();
    require_once("sigesp_spg_funciones_reportes.php");
    $io_function_report = new sigesp_spg_funciones_reportes();
    require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
    $io_funciones=new class_funciones();			
    require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
    $io_fecha = new class_fecha();
			
//--------------------------------------------------  Parámetros para Filtar el Reporte  ---------------------------------------
		
    $ldt_periodo  = $_SESSION["la_empresa"]["periodo"];
    $li_ano	  = substr($ldt_periodo,0,4);
    $li_estmodest = $_SESSION["la_empresa"]["estmodest"];
    $ls_codestpro1 = $_GET["codestpro1"];
    $ls_codestpro2 = $_GET["codestpro2"];
    $ls_codestpro3 = $_GET["codestpro3"];
    $ls_codestpro4 = $_GET["codestpro4"];
    $ls_codestpro5 = $_GET["codestpro5"];
    $ls_estcla     = $_GET["estcla"];
    $ls_tipoformato=1;
    $fecdes=$_GET["txtfecdes"];
    $ldt_fecdes=$io_funciones->uf_convertirfecmostrar($fecdes);
    $fechas=$_GET["txtfechas"];
    $ldt_fechas=$io_funciones->uf_convertirfecmostrar($fechas);
    $ls_orden=$_GET["rborden"];
    $ls_codfuefin=$_GET["codfuefin"];
    $ls_programatica=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
    $ls_despro="PROYECTO: ".$_GET["despro"];
    /////////////////////////////////         SEGURIDAD               ///////////////////////////////////
    $ls_desc_event="Solicitud de Reporte Ejecucion de proyecto SIGEPRODEN desde la fecha ".$fecdes." hasta ".$fechas." , programatica ".$ls_programatica;
    $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_mayor_analitico.php",$ls_desc_event);
    ////////////////////////////////         SEGURIDAD               ///////////////////////////////////
    
    //----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
    $ls_titulo=" EJECUCION DE PROYECTO SIGEPRODEN  DESDE  ".$ldt_fecdes."  AL  ".$ldt_fechas; 
    //--------------------------------------------------------------------------------------------------------------------------------
  
	 
    $arrResultado=$io_report->uf_spg_reporte_sigeproden_ejecucion_proyectos($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
                                                                            $ls_codestpro5,$ldt_fecdes,$ldt_fechas,$ls_estcla,$ls_codfuefin,
                                                                            $ls_orden);
    $rs_data=$arrResultado['rs_data'];
    $lb_valido=$arrResultado['lb_valido'];
    if(($lb_valido==false)||($rs_data->RecordCount()==0)) // Existe algún error ó no hay registros
    {
        print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');"); 
	print(" close();");
	print("</script>");
    }
    else // Imprimimos el reporte
    {
        $io_tcpdf= new sigesp_spg_class_tcpdf ("L", PDF_UNIT, "legal", true);
        $io_tcpdf->AliasNbPages();
        $io_tcpdf->SetFont("helvetica","B",9);
        $io_tcpdf->SetHeaderData($_SESSION["ls_logo"], 22, date("d/m/Y"), date("h:i a").'-'.$_SESSION["ls_database"].str_repeat(' ',80).$ls_titulo,$_SESSION["ls_height"]);
        $io_tcpdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, 'B', PDF_FONT_SIZE_MAIN));
        $io_tcpdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $io_tcpdf->SetMargins(2, 40, 2);
        $io_tcpdf->SetHeaderMargin(6);
        $io_tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $io_tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $io_tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
        $io_tcpdf->AliasNbPages();
        $io_tcpdf->AddPage();
		
        $ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
        $ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
        $ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
        $ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
        $ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
		
	while(!$rs_data->EOF)
	{
            $ld_total_compromiso=0;
            $ld_total_causado=0;
            $ld_total_pagado=0;
            $ld_total_por_paga=0;

            $li_tmp=0;
            $ld_totspg_compromiso=0;
            $ld_totspg_causado=0;
            $ld_totspg_pagado=0;
            $ld_totspg_por_pagar=0;
            $ls_programatica=$rs_data->fields["programatica"];
            $ls_estcla=substr($ls_programatica,-1);
	    $ls_codestpro1=substr($ls_programatica,0,25);
            $ls_denestpro1="";
	    $arrResultado=$io_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);
            $ls_denestpro1=$arrResultado['as_denestpro1'];
            $lb_valido=$arrResultado['lb_valido'];
	    $ls_codestpro2=substr($ls_programatica,25,25);
            if($lb_valido)
	    {
                $ls_denestpro2="";
		$arrResultado=$io_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
		$ls_denestpro2=$arrResultado['as_denestpro2'];
		$lb_valido=$arrResultado['lb_valido'];
	    }
	    $ls_codestpro3=substr($ls_programatica,50,25);
            if($lb_valido)
	    {
                $ls_denestpro3="";
		$arrResultado=$io_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
		$ls_denestpro3=$arrResultado['as_denestpro3'];
		$lb_valido=$arrResultado['lb_valido'];
	    }
            if($li_estmodest==2)
            {
                $ls_codestpro4=substr($ls_programatica,75,25);
		if($lb_valido)
		{
                    $ls_denestpro4="";
                    $arrResultado=$io_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estcla);
                    $ls_denestpro4=$arrResultado['as_denestpro4'];
                    $lb_valido=$arrResultado['lb_valido'];
		}
		$ls_codestpro5=substr($ls_programatica,100,25);
		if($lb_valido)
		{
                    $ls_denestpro5="";
                    $arrResultado=$io_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
                    $ls_denestpro5=$arrResultado['as_denestpro5'];
                    $lb_valido=$arrResultado['lb_valido'];
		}
                $ls_denestpro=utf8_encode($ls_denestpro1." , ".$ls_denestpro2." , ".$ls_denestpro3." , ".$ls_denestpro4." , ".$ls_denestpro5);
		$ls_programatica=substr($ls_codestpro1,-$ls_loncodestpro1).substr($ls_codestpro2,-$ls_loncodestpro2).substr($ls_codestpro3,-$ls_loncodestpro3).substr($ls_codestpro4,-$ls_loncodestpro4).substr($ls_codestpro5,-$ls_loncodestpro5);
            }
            else
            {
                $ls_programatica=substr($ls_codestpro1,-$ls_loncodestpro1).substr($ls_codestpro2,-$ls_loncodestpro2).substr($ls_codestpro3,-$ls_loncodestpro3);
		$ls_denestpro = array();
		$ls_denestpro[0]=$ls_denestpro1;
		$ls_denestpro[1]=$ls_denestpro2;
		$ls_denestpro[2]=$ls_denestpro3;
            }
            $arrResultado=$io_report->uf_spg_reporte_sigeproden_ejecucion_proyectos2($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
                                                                                     $ls_codestpro5,$ldt_fecdes,$ldt_fechas,$ls_estcla,$ls_codfuefin,$ls_orden); 
            $rs_data2=$arrResultado['rs_data'];
            $lb_valido=$arrResultado['lb_valido'];
            if($lb_valido)
            {  
                $li_totrow_det=$rs_data2->RecordCount();
		if($li_totrow_det>=1)
		{
                    $io_tcpdf->uf_print_cabecera_proyecto($ls_despro,$ls_programatica,$ls_denestpro); // Imprimimos la cabecera del registro
		}				
                $entro=false;
                $ls_antcom=0;
                $ls_antcau=0;
                $ls_antpagado=0;
                $ls_antpagar=0;	
		while(!$rs_data2->EOF)
        	{					
                    $ls_programatica=$rs_data2->fields["codestpro1"].$rs_data2->fields["codestpro2"].$rs_data2->fields["codestpro3"].$rs_data2->fields["codestpro4"].$rs_data2->fields["codestpro5"];   
                    $ls_codestpro1=$rs_data2->fields["codestpro1"];
                    $ls_codestpro2=$rs_data2->fields["codestpro2"];
                    $ls_codestpro3=$rs_data2->fields["codestpro3"];
                    $ls_codestpro4=$rs_data2->fields["codestpro4"];
                    $ls_codestpro5=$rs_data2->fields["codestpro5"];
                    $ls_estcla=$rs_data2->fields["estcla"];

                    $ls_nombre_prog=utf8_encode($rs_data2->fields["nombre_prog"]);

                    $ls_spg_cuenta=$rs_data2->fields["spg_cuenta"];
				  
                    if (($li_tmp)<$li_totrow_det)
                    {
                        $rs_data2->MoveNext();
                        $ls_spg_cuenta_next=$rs_data2->fields["spg_cuenta"];  
                        $rs_data2->Move($li_tmp);
                    }
                    elseif(($li_tmp)==$li_totrow_det)
                    {
                        $ls_spg_cuenta_next=$rs_data2->fields["spg_cuenta"]; 
                    }
                    //PARA BUSCAR LOS SALDOS ANTERIORES
                    if  (!$entro)
                    {
                        $entro=true;
			$rs_data3="";
			$arrResultado=$io_report->uf_spg_calcular_saldo_anterior ($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
					   					  $ls_codestpro4, $ls_codestpro5,$ldt_fecdes,
										  $ls_spg_cuenta,$ls_estcla,$rs_data3); 				 
                        $rs_data3=$arrResultado['rs_data'];
			$lb_valido=$arrResultado['lb_valido'];
			$li_num=$rs_data3->RecordCount();
                        if (($li_num>=1) && ($lb_valido))
			{  
                            $ld_compromiso2=0;
                            $ld_causado2=0;
                            $ld_pagado2=0;
                            $ld_por_paga2=0;
                            while (!$rs_data3->EOF)
                            {
                                $ld_compromiso2=$ld_compromiso2+$rs_data3->fields["compromiso"];
                                $ld_causado2=$ld_causado2+$rs_data3->fields["causado"];
                                $ld_pagado2=$ld_pagado2+$rs_data3->fields["pago"];
									
                                $ls_antcom=$ld_compromiso2;
                                $ls_antcau=$ld_causado2;
                                $ls_antpagado=$ld_pagado2;
                                $ls_antpagar=$ld_por_paga2;
                                $rs_data3->MoveNext();
                            }
                            $ld_por_paga2 = $ld_causado2-$ld_pagado2;

                            $ld_total_compromiso=$ld_total_compromiso+$ld_compromiso2;
                            $ld_total_causado=$ld_total_causado+$ld_causado2;
                            $ld_total_pagado=$ld_total_pagado+$ld_pagado2;
                            $ld_total_por_paga=$ld_total_por_paga+$ld_por_paga2;

                            $ld_totspg_compromiso        = $ld_totspg_compromiso + $ld_compromiso2;
                            $ld_totspg_causado           = $ld_totspg_causado + $ld_causado2;
                            $ld_totspg_pagado            = $ld_totspg_pagado + $ld_pagado2;
                            $ld_totspg_por_pagar         = $ld_totspg_por_pagar + $ld_por_paga2;
														    
                            $la_auxdata[0]=array('','','','SALDOS ANTERIORES',
						 number_format($ld_compromiso2,2,",","."),
						 number_format($ld_causado2,2,",","."),
						 number_format($ld_pagado2,2,",","."),
                                                 number_format($ld_por_paga2,2,",","."));
			}
			else if ($lb_valido)
			{
						 	
                            $la_auxdata[0]=array('','','','SALDOS ANTERIORES','0.00','0.00','0.00','0.00');
                            $ld_compromiso2=0;
                            $ld_causado2=0;
                            $ld_pagado2=0;
                            $ld_por_paga2=0;
			}
                    }
				  	  
                    $ls_denominacion=utf8_encode($rs_data2->fields["denominacion"]); 
                    $fecha=$rs_data2->fields["fecha"];
                    $ls_fecha=$io_funciones->uf_convertirfecmostrar($fecha);
                    $ls_procede=$rs_data2->fields["procede"]; 
                    $ls_procede_doc=$rs_data2->fields["procede_doc"];
                    $ls_comprobante=$rs_data2->fields["comprobante"];
                    $ls_documento=$rs_data2->fields["documento"];
                    $ls_descripcion=substr(utf8_encode($rs_data2->fields["nombre_prog"]), 0,50);
                    $ls_tipodest=$rs_data2->fields["tipo_destino"];
                    $ls_nombene=utf8_encode($rs_data2->fields["nombene"]);
                    $ls_apebene=utf8_encode($rs_data2->fields["apebene"]);
                    $ls_nompro=utf8_encode($rs_data2->fields["nompro"]);
                    if ($ls_tipodest=='B')
                    {
                          $ls_nomproben=$ls_apebene." ".$ls_nombene;
                    }
                    else
                    {
                          $ls_nomproben=$ls_nompro;
                    }				  
                    $ld_compromiso=$rs_data2->fields["compromiso"];
                    $ld_causado=$rs_data2->fields["causado"];
                    $ld_pagado=$rs_data2->fields["pago"];

                    $ld_por_paga=$ld_causado-$ld_pagado;
                    $ld_total_compromiso=$ld_total_compromiso+$ld_compromiso;
                    $ld_total_causado=$ld_total_causado+$ld_causado;
                    $ld_total_pagado=$ld_total_pagado+$ld_pagado;
                    $ld_total_por_paga=$ld_total_por_paga+$ld_por_paga;
								  
                    $ld_totspg_compromiso        = $ld_totspg_compromiso + $ld_compromiso;
                    $ld_totspg_causado           = $ld_totspg_causado + $ld_causado;
                    $ld_totspg_pagado            = $ld_totspg_pagado + $ld_pagado;
                    $ld_totspg_por_pagar         = $ld_totspg_por_pagar + $ld_por_paga;

				  
                    if(($ls_spg_cuenta!=$ls_spg_cuenta_next)&& ($li_tmp!=($li_totrow_det-1)))
                    {
                        $la_data[$li_tmp]=array($ls_fecha,$ls_comprobante,$ls_documento,$ls_descripcion,
                                                number_format($ld_compromiso,2,",","."),
                                                number_format($ld_causado,2,",","."),
                                                number_format($ld_pagado,2,",","."),
                                                number_format($ld_por_paga,2,",","."));
                        $entro=false;
                        $y=0;
                        $io_tcpdf->uf_print_cabecera_detalle($ls_spg_cuenta,$ls_denominacion);
                        $io_tcpdf->uf_print_titulos_campoext_proyectos ();
                        $io_tcpdf->uf_print_detalle_campoext_proyecto($la_auxdata,$y,0); // Imprimimos los saldos anteriores							 
                        $io_tcpdf->uf_print_detalle_campoext_proyecto($la_data,$y,1); // Imprimimos el detalle

                        $ls_subcompromiso         =0;
                        $ls_subcausado            =0;
                        $ls_subpagar              =0;
                        $ls_subpagado             =0;
                               //------------------------------------------------------------------------------------------------------- 

                        $la_data_subtot[1]=array("TOTAL CUENTA ".$ls_spg_cuenta,
                                                 number_format($ld_totspg_compromiso,2,",","."),
                                                 number_format($ld_totspg_causado,2,",","."),
                                                 number_format($ld_totspg_pagado,2,",","."),
                                                 number_format($ld_totspg_por_pagar,2,",",".")); 


                        $io_tcpdf->uf_print_total_proyecto($la_data_subtot,'3',0);
                        $ld_totspg_compromiso=0;
                        $ld_totspg_causado=0;
                        $ld_totspg_pagado=0;
                        $ld_totspg_por_pagar=0;
                        unset($la_data_subtot);
                        unset($la_data_subtot2); 
                        unset($la_data);
                        unset($la_auxdata);
                    }//if	 
                    else
                    {
                        $la_data[$li_tmp]=array($ls_fecha,$ls_comprobante,$ls_documento,$ls_descripcion,
                                                number_format($ld_compromiso,2,",","."),
                                                number_format($ld_causado,2,",","."),
                                                number_format($ld_pagado,2,",","."),
                                                number_format($ld_por_paga,2,",","."));
                    }//else
                    
                    if($li_tmp==($li_totrow_det-1))
                    {
                        $la_data[$li_tmp]=array($ls_fecha,$ls_comprobante,$ls_documento,$ls_descripcion,
                                                number_format($ld_compromiso,2,",","."),
                                                number_format($ld_causado,2,",","."),
                                                number_format($ld_pagado,2,",","."),
                                                number_format($ld_por_paga,2,",","."));

                        //---------- SUB TOTAL POR PERIODO DE LA CUENTA-----------------------------------
                        $ls_subcompromiso         =0;
                        $ls_subcausado            =0;
                        $ls_subpagar              =0;
                        $ls_subpagado             =0;
                        //------------------------------------------------------------------------------------------
										  
                        $y=0;			
                        $io_tcpdf->uf_print_cabecera_detalle($ls_spg_cuenta,$ls_denominacion);
                        $io_tcpdf->uf_print_titulos_campoext_proyectos ();
                        $io_tcpdf->uf_print_detalle_campoext_proyecto($la_auxdata,$y,0,$ls_mostrar); // Imprimimos los saldos anteriores	
                        $io_tcpdf->uf_print_detalle_campoext_proyecto($la_data,$y,1,$ls_mostrar); // Imprimimos el detalle 
                        $io_tcpdf->uf_print_total_proyecto($la_data_subtot2,'3',$y);
                        $la_data_subtot[1]=array("TOTAL CUENTA ".$ls_spg_cuenta,
                                                 number_format($ld_totspg_compromiso,2,",","."),
                                                 number_format($ld_totspg_causado,2,",","."),
                                                 number_format($ld_totspg_pagado,2,",","."),
                                                 number_format(abs($ld_totspg_por_pagar),2,",",".")); 

                        $io_tcpdf->uf_print_total_proyecto($la_data_subtot,'3',0);

			$ld_totspg_compromiso=0;
			$ld_totspg_causado=0;
			$ld_totspg_pagado=0;
			$ld_totspg_por_pagar=0;
			unset($la_data_subtot); 
			unset($la_data_subtot2);
					 
                        $ld_total_compromiso=number_format($ld_total_compromiso,2,",",".");
                        $ld_total_causado=number_format($ld_total_causado,2,",",".");
                        $ld_total_pagado=number_format($ld_total_pagado,2,",",".");
                        $ld_total_por_paga=number_format(abs($ld_total_por_paga),2,",",".");

                        $la_data_tot[$li_tmp]=array('',$ld_total_compromiso,$ld_total_causado,$ld_total_pagado,$ld_total_por_paga);
                        $io_tcpdf->uf_print_total_proyecto($la_data_tot,'3',0);								
			
                        unset($la_data);	
			unset($la_data_tot);
			unset($la_auxdata);
					 
                    }//if
                    $rs_data2->MoveNext();
                    $li_tmp=$li_tmp+1;
		}//while			
            }//if
            unset($la_data);
            $rs_data->MoveNext();			
        }//while
        $io_tcpdf->Output("sigesp_spg_rpp_sigeproden_ejecucion_proyectos.pdf", "I");		
        unset($io_tcpdf);
    } //else
    unset($io_report);
    unset($io_funciones);	
    unset($io_function_report);		
?> 