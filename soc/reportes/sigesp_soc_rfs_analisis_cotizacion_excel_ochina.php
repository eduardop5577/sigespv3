<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();   
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 25/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_soc;
		$ls_descripcion="Generó el Reporte Análisis de Cotización en Excel";
		$lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_p_analisis_cotizacion.php",$ls_descripcion);
		return $lb_valido;
	}
	//------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "analisiscotizacion.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_soc.php");
	$io_fun_soc=new class_funciones_soc();
	require_once("sigesp_soc_class_report.php");
	$io_class_report=new sigesp_soc_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
	$io_ds=new class_datastore();				
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="ANÁLISIS DE COTIZACIONES";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_tipsolcot=$io_fun_soc->uf_obtenervalor_get("tipsolcot","");
	$ls_numanacot=$io_fun_soc->uf_obtenervalor_get("numanacot","");
	$ld_fecha=$io_fun_soc->uf_obtenervalor_get("fecha","");
	$ls_observacion=$io_fun_soc->uf_obtenervalor_get("observacion","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad(); // Seguridad de Reporte
	if($lb_valido)
	{
                $io_ds_detalle=new class_datastore();
                $io_ds_detallecot=new class_datastore();
                $io_ds_detallepro=new class_datastore();
                $io_ds_detalleprocot=new class_datastore();
                $io_ds_detallepro1=new class_datastore();
		
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('10');
                
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('8');
		/////////////////////////////////////////////////
		$lo_titulocombinado =& $lo_libro->addformat();
                $lo_titulocombinado->set_text_wrap();
		$lo_titulocombinado->set_size('8');		
		$lo_titulocombinado->set_bold();
		$lo_titulocombinado->set_font("Verdana");
		$lo_titulocombinado->set_align('center');
		$lo_titulocombinado->set_merge(); # This is the key feature

		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('8');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('8');
		$lo_dataright= &$lo_libro->addformat(array("num_format"=> "#,##0.00"));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('8');
		$lo_datarighttext= &$lo_libro->addformat();
		$lo_datarighttext->set_font("Verdana");
		$lo_datarighttext->set_align('right');
		$lo_datarighttext->set_size('8');
                
		$lo_hoja->set_column(0,0,10);
		$lo_hoja->set_column(1,1,10);
		$lo_hoja->set_column(2,2,15);
		$lo_hoja->set_column(3,3,45);
		$lo_hoja->set_column(4,4,20);
		$lo_hoja->set_column(5,5,15);
		$lo_hoja->set_column(6,6,20);
		$lo_hoja->set_column(7,7,15);
		$lo_hoja->set_column(8,8,20);
		$lo_hoja->set_column(9,9,15);
		$lo_hoja->set_column(10,10,15);

                $li_line=2;
		$lo_hoja->write($li_line,4,"REPÚBLICA BOLIVARIANA DE VENEZUELA",$lo_encabezado);
                $li_line++;
		$lo_hoja->write($li_line,4,"MINISTERIO DEL PODER POPULAR PARA LA DEFENSA",$lo_encabezado);
                $li_line++;
		$lo_hoja->write($li_line,4,"VICEMINISTERIO DE SERVICIOS",$lo_encabezado);
                $li_line++;
		$lo_hoja->write($li_line,4,"DIRECCIÓN GENERAL DE EMPRESAS Y SERVICIOS",$lo_encabezado);
                $li_line++;
		$lo_hoja->write($li_line,4,"OFICINA COORDINADORA DE HIDROGRAFÍA Y NAVEGACIÓN",$lo_encabezado);
                $li_line++;
                $li_line++;
                $li_line++;
		$lo_hoja->write($li_line,0,"ANÁLISIS Y RECOMENDACION",$lo_titulocombinado);
		$lo_hoja->write_blank($li_line,1,$lo_titulocombinado);
		$lo_hoja->write_blank($li_line,2,$lo_titulocombinado);
		$lo_hoja->write_blank($li_line,3,$lo_titulocombinado);
		$lo_hoja->write($li_line,4,"FECHA",$lo_titulo);
		$lo_hoja->write($li_line,5,"Maiquetía",$lo_titulo);
                $li_line++;
                                
		$la_cotizaciones="";
		$arrResultado=$io_class_report->uf_cargar_cotizaciones_esp($ls_numanacot,$la_cotizaciones,$ls_tipsolcot);
		$la_cotizaciones = $arrResultado['aa_proveedores'];
		$lb_valido=$arrResultado['lb_valido'];
		if($lb_valido)
		{	
			$li_totrow=count((array)$la_cotizaciones);			
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$io_ds_detalle->insertRow("codigo",$la_cotizaciones[$li_i]["codigo"]);
				$io_ds_detallepro->insertRow("cod_pro",$la_cotizaciones[$li_i]["cod_pro"]);
				$io_ds_detallepro->insertRow("nompro",$la_cotizaciones[$li_i]["nompro"]);
			}
			$io_ds_detallepro->group('cod_pro');
			$io_ds_detalle->group('codigo');
                        $ls_countcot=0;
                        $io_ds_detalleprocot->data=$io_ds_detallepro->data;
                        $io_ds_detallepro1->data=$io_ds_detallepro->data;
			$arrResultado=$io_class_report->uf_count_cotizaciones($ls_numanacot,$ls_countcot,$ls_tipsolcot);
			$ls_countcot = $arrResultado['aa_proveedores'];
			$lb_valido = $arrResultado['lb_valido'];
			$ls_countcot=count((array)$ls_countcot);
			if($lb_valido)
			{
                            $li_totalcotizaciones=count((array)$la_cotizaciones);
                            $la_ganadores=$io_class_report->uf_select_cotizacion_analisis($ls_numanacot,$ls_tipsolcot);
                            //Imprimiendo primer titulo

                            $li_cont=0;
                            $colum[1]="";
                            $colum[2]="";
                            $colum[3]="";
                            for($li_s=0;$li_s<$li_totalcotizaciones;$li_s++)
                            {    
                                $ls_codpro=$la_cotizaciones[$li_s+1]["cod_pro"];
                                $li_findrow=$io_ds_detallepro->find("cod_pro",$ls_codpro);
                                if($li_findrow>0)
                                {   $li_cont++;
                                    $colum[$li_cont]=substr($la_cotizaciones[$li_s+1]["nompro"],0,410);
                                    $io_ds_detallepro->deleteRow("cod_pro",$li_findrow);                            
                                }
                            }
                            $lo_hoja->write($li_line,0,"ADQUISICIÓN DE MATERIAL ELÉCTRICO  EL CUAL SERA UTILIZADO EN  LA ESCUELA DE VEHICULOS ANFIBIOS Y ARTILLERIA DE LA INFANTERIA DE MARINA BOLIVARIANA,  SOLICITADO POR EL COMANDANTE DE DIESCASIVA, SEGÚN OFICIO 0119 DE FECHA 28OCT19.",$lo_titulocombinado);
                   	    $lo_hoja->write_blank($li_line,1,$lo_titulocombinado);
                   	    $lo_hoja->write_blank($li_line,2,$lo_titulocombinado);
                   	    $lo_hoja->write_blank($li_line,3,$lo_titulocombinado);
                            $lo_hoja->write($li_line,4,$colum[1],$lo_titulocombinado);
                   	    $lo_hoja->write_blank($li_line,5,$lo_titulocombinado);
                            $lo_hoja->write($li_line,6,$colum[2],$lo_titulocombinado);
                   	    $lo_hoja->write_blank($li_line,7,$lo_titulocombinado);
                            $lo_hoja->write($li_line,8,$colum[3],$lo_titulocombinado);
                   	    $lo_hoja->write_blank($li_line,9,$lo_titulocombinado);
                            $li_line++;
                            $lo_hoja->write($li_line,0,"RENGLON",$lo_titulo);
                            $lo_hoja->write($li_line,1,"CANT.",$lo_titulo);
                            $lo_hoja->write($li_line,2,"UNID.",$lo_titulo);
                            $lo_hoja->write($li_line,3,"ARTICULOS",$lo_titulo);
                            $lo_hoja->write($li_line,4,"PRECIO UNITARIO B.S",$lo_titulo);
                            $lo_hoja->write($li_line,5,"SUBTOTAL B.S",$lo_titulo);
                            $lo_hoja->write($li_line,6,"PRECIO UNITARIO B.S",$lo_titulo);
                            $lo_hoja->write($li_line,7,"SUBTOTAL B.S",$lo_titulo);
                            $lo_hoja->write($li_line,8,"PRECIO UNITARIO B.S",$lo_titulo);
                            $lo_hoja->write($li_line,9,"SUBTOTAL B.S",$lo_titulo);
                            $li_line++;

                            $li_a=0;
                            $li_totalsubtotal1=0;
                            $li_poriva1="";
                            $li_totaliva1=0;
                            $li_totalmonto1=0;
                            $li_totalsubtotal2=0;
                            $li_poriva2="";
                            $li_totaliva2=0;
                            $li_totalmonto2=0;
                            $li_totalsubtotal3=0;
                            $li_poriva3="";
                            $li_totaliva3=0;
                            $li_totalmonto3=0;
                            for($li_i=0;$li_i<$li_totalcotizaciones;$li_i++)
                            { 
                                $ls_codigo=$la_cotizaciones[$li_i+1]["codigo"];
                                $li_findrow=$io_ds_detalle->find("codigo",$ls_codigo);
                                if($li_findrow>0)
                                {   $li_a++;
                                    $la_data[$li_a]["columna1"]=$li_a;
                                    $la_data[$li_a]["columna2"]=number_format($la_cotizaciones[$li_i+1]["canart"],2,",",".");
                                    $la_data[$li_a]["columna3"]=$la_cotizaciones[$li_i+1]["denunimed"];
                                    $la_data[$li_a]["columna4"]=$la_cotizaciones[$li_i+1]["denominacion"];
                                    $io_ds_detalle->deleteRow("codigo",$li_findrow);
                                }
                                else
                                {
                                    $ls_codigo="";
                                }
                                $io_ds_det=new class_datastore();
                                $io_ds_det->data=$io_ds_detallepro1->data;
                                $li=0;
                                for($li_s=0;$li_s<$li_totalcotizaciones;$li_s++)
                                {
                                    if ($ls_codigo == $la_cotizaciones[$li_s+1]["codigo"])
                                    {
                                        $ls_codpro=$la_cotizaciones[$li_s+1]["cod_pro"];
                                        $li_findrow=$io_ds_det->find("cod_pro",$ls_codpro);
                                        if($li_findrow>0)
                                        {
                                            if ($li==0)
                                            {
                                                $la_data[$li_a]["columna5"]=number_format($la_cotizaciones[$li_s+1]["preuniart"],2,",",".");
                                                $la_data[$li_a]["columna6"]=number_format($la_cotizaciones[$li_s+1]["monsubart"],2,",",".");  
                                                $li_totalsubtotal1=number_format($li_totalsubtotal1+$la_cotizaciones[$li_s+1]["monsubart"],2,".","");  
                                                $li_totaliva1=number_format($li_totaliva1+$la_cotizaciones[$li_s+1]["moniva"],2,".","");  
                                                $li_poriva1=$la_cotizaciones[$li_s+1]["poriva"];
                                            }
                                            if ($li==1)
                                            {
                                                $la_data[$li_a]["columna7"]=number_format($la_cotizaciones[$li_s+1]["preuniart"],2,",",".");
                                                $la_data[$li_a]["columna8"]=number_format($la_cotizaciones[$li_s+1]["monsubart"],2,",",".");  
                                                $li_totalsubtotal2=number_format($li_totalsubtotal2+$la_cotizaciones[$li_s+1]["monsubart"],2,".","");  
                                                $li_totaliva2=number_format($li_totaliva2+$la_cotizaciones[$li_s+1]["moniva"],2,".","");  
                                                $li_poriva2=$la_cotizaciones[$li_s+1]["poriva"];
                                            }
                                            if ($li==2)
                                            {
                                                $la_data[$li_a]["columna9"]=number_format($la_cotizaciones[$li_s+1]["preuniart"],2,",",".");
                                                $la_data[$li_a]["columna10"]=number_format($la_cotizaciones[$li_s+1]["monsubart"],2,",",".");  
                                                $li_totalsubtotal3=number_format($li_totalsubtotal3+$la_cotizaciones[$li_s+1]["monsubart"],2,".","");  
                                                $li_totaliva3=number_format($li_totaliva3+$la_cotizaciones[$li_s+1]["moniva"],2,".","");  
                                                $li_poriva3=$la_cotizaciones[$li_s+1]["poriva"];
                                            }                            
                                            $io_ds_det->deleteRow("cod_pro",$li_findrow); 
                                            $li++;
                                        }
                                    }
                                }
                            }
                            for($li_i=1;$li_i<=$li_a;$li_i++)
                            {
                                $lo_hoja->write($li_line,0,$la_data[$li_i]["columna1"],$lo_datacenter);
                                $lo_hoja->write($li_line,1,$la_data[$li_i]["columna2"],$lo_datacenter);
                                $lo_hoja->write($li_line,2,$la_data[$li_i]["columna3"],$lo_datacenter);
                                $lo_hoja->write($li_line,3,$la_data[$li_i]["columna4"],$lo_dataleft);
                                $lo_hoja->write($li_line,4,$la_data[$li_i]["columna5"],$lo_dataright);
                                $lo_hoja->write($li_line,5,$la_data[$li_i]["columna6"],$lo_dataright);
                                $lo_hoja->write($li_line,6,$la_data[$li_i]["columna7"],$lo_dataright);
                                $lo_hoja->write($li_line,7,$la_data[$li_i]["columna8"],$lo_dataright);
                                $lo_hoja->write($li_line,8,$la_data[$li_i]["columna9"],$lo_dataright);
                                $lo_hoja->write($li_line,9,$la_data[$li_i]["columna10"],$lo_dataright);
                                $li_line++;                                
                            }
                            $li_totalmonto1=number_format($li_totalsubtotal1+$li_totaliva1,2,",",".");
                            $li_totalsubtotal1=number_format($li_totalsubtotal1,2,",",".");
                            $li_totaliva1=number_format($li_totaliva1,2,",",".");
                            $li_totalmonto2=number_format($li_totalsubtotal2+$li_totaliva2,2,",",".");
                            $li_totalsubtotal2=number_format($li_totalsubtotal2,2,",",".");
                            $li_totaliva2=number_format($li_totaliva2,2,",",".");
                            $li_totalmonto3=number_format($li_totalsubtotal3+$li_totaliva3,2,",",".");
                            $li_totalsubtotal3=number_format($li_totalsubtotal3,2,",",".");
                            $li_totaliva3=number_format($li_totaliva3,2,",",".");
                            
                            $lo_hoja->write($li_line,4,"BASE IMPONIBLE B.S",$lo_datarighttext);
                            $lo_hoja->write($li_line,5,$li_totalsubtotal1,$lo_dataright);
                            $lo_hoja->write($li_line,6,"BASE IMPONIBLE B.S",$lo_datarighttext);
                            $lo_hoja->write($li_line,7,$li_totalsubtotal2,$lo_dataright);
                            $lo_hoja->write($li_line,8,"BASE IMPONIBLE B.S",$lo_datarighttext);
                            $lo_hoja->write($li_line,9,$li_totalsubtotal3,$lo_dataright);
                            $li_line++;
                                
                            $lo_hoja->write($li_line,4,"IVA ".$li_poriva1." %",$lo_datarighttext);
                            $lo_hoja->write($li_line,5,$li_totaliva1,$lo_dataright);
                            $lo_hoja->write($li_line,6,"IVA ".$li_poriva2." %",$lo_datarighttext);
                            $lo_hoja->write($li_line,7,$li_totaliva2,$lo_dataright);
                            $lo_hoja->write($li_line,8,"IVA ".$li_poriva3." %",$lo_datarighttext);
                            $lo_hoja->write($li_line,9,$li_totaliva3,$lo_dataright);
                            $li_line++;
                                
                            $lo_hoja->write($li_line,4,"TOTAL B.S",$lo_datarighttext);
                            $lo_hoja->write($li_line,5,$li_totalmonto1,$lo_dataright);
                            $lo_hoja->write($li_line,6,"TOTAL B.S",$lo_datarighttext);
                            $lo_hoja->write($li_line,7,$li_totalmonto2,$lo_dataright);
                            $lo_hoja->write($li_line,8,"TOTAL B.S",$lo_datarighttext);
                            $lo_hoja->write($li_line,9,$li_totalmonto3,$lo_dataright);
                            $li_line++;
                            $li_line++;
                                
                            $la_data[0]["columna0"]="REFERENCIA 'A'";
                            $la_data[1]["columna0"]="REFERENCIA 'B'";
                            $la_data[2]["columna0"]="REFERENCIA 'C'";
                            $la_data[3]["columna0"]="REFERENCIA 'D'";
                            $la_data[4]["columna0"]="REFERENCIA 'E'";
                            $la_data[5]["columna0"]="REFERENCIA 'F'";
                            $la_data[6]["columna0"]="REFERENCIA 'G'";
                            $la_data[7]["columna0"]="REFERENCIA 'H'";
                            
                            $la_data[0]["columna1"]="TIEMPO DE ENTREGA";
                            $la_data[1]["columna1"]="VALIDEZ DE LA OFERTA";
                            $la_data[2]["columna1"]="CONDICIONES DE PAGO";
                            $la_data[3]["columna1"]="GARANTÍAS TECNICAS";
                            $la_data[4]["columna1"]="SOLVENCIA LABORAL";
                            $la_data[5]["columna1"]="CUMPLE CON LAS ESPECIFICACONES TÉCNICAS DE LA SOLICITUD";
                            $la_data[6]["columna1"]="SE AJUSTA A LA DISPONIBILIDAD PRESUPUESTARIA";
                            $la_data[7]["columna1"]="PRESENTÓ LAS CARTAS ESTABLECIDAS EN LA LCP";
                            $li_cont=1;
                            for($li_s=0;$li_s<$li_totalcotizaciones;$li_s++)
                            {    
                                $ls_codpro=$la_cotizaciones[$li_s+1]["cod_pro"];
                                $li_findrow=$io_ds_detalleprocot->find("cod_pro",$ls_codpro);
                                if($li_findrow>0)
                                {   $li_cont++;
                                    $estasitec = "NO CUMPLE";
                                    if ($la_cotizaciones[$li_s+1]["estasitec"] == 1)
                                    {
                                       $estasitec = "SI CUMPLE"; 
                                    }
                                    $la_data[0]["columna".$li_cont]=$la_cotizaciones[$li_s+1]["diaentcom"]." Dias";
                                    $la_data[1]["columna".$li_cont]=$la_cotizaciones[$li_s+1]["diavalofe"]." Dias";
                                    $la_data[2]["columna".$li_cont]=$la_cotizaciones[$li_s+1]["forpagcom"];
                                    $la_data[3]["columna".$li_cont]=$la_cotizaciones[$li_s+1]["garanacot"];
                                    $la_data[4]["columna".$li_cont]="SI";
                                    $la_data[5]["columna".$li_cont]=$estasitec;
                                    $la_data[6]["columna".$li_cont]="SÍ SE AJUSTA";
                                    $la_data[7]["columna".$li_cont]="PRESENTÓ";
                                    $io_ds_detalleprocot->deleteRow("cod_pro",$li_findrow);                            
                                }
                            }
                            for($li_i=0;$li_i<=7;$li_i++)
                            {
                                $lo_hoja->write($li_line,0,$la_data[$li_i]["columna0"],$lo_titulocombinado);
                                $lo_hoja->write_blank($li_line,1,$lo_titulocombinado);
                                $lo_hoja->write($li_line,2,$la_data[$li_i]["columna1"],$lo_titulocombinado);
                                $lo_hoja->write_blank($li_line,3,$lo_titulocombinado);
                                $lo_hoja->write($li_line,4,$la_data[$li_i]["columna2"],$lo_titulocombinado);
                                $lo_hoja->write_blank($li_line,5,$lo_titulocombinado);
                                $lo_hoja->write($li_line,6,$la_data[$li_i]["columna3"],$lo_titulocombinado);
                                $lo_hoja->write_blank($li_line,7,$lo_titulocombinado);
                                $lo_hoja->write($li_line,8,$la_data[$li_i]["columna4"],$lo_titulocombinado);
                                $lo_hoja->write_blank($li_line,9,$lo_titulocombinado);
                                $li_line++;                                
                            }
                            $li_line++;
                            $lo_hoja->write($li_line,0,"RECOMENDACIONES: SE RECOMIENDA ASIGNAR LA PRESENTE ORDEN DE COMPRA A LA EMPRESA ".$la_ganadores[0]["nompro"].", MOTIVADO A QUE PRESENTA LA MEJOR OFERTA ENTRE LOS PARTICIPANTES Y EN EL MERCADO, TOMANDO EN CONSIDERACIÓN LOS ASPECTOS ANTERIORMENTE EVALUADOS.",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,1,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,2,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,3,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,4,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,5,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,6,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,7,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,8,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,9,$lo_titulocombinado);
                            $li_line++;                                
                            
                            $li_line++;                                
                            $li_line++;                                
                            $li_line++;       
                            $li_line++;                                
                            $li_line++;                                
                            $li_line++;       
                            $lo_hoja->write($li_line,0,"LUIS MIGUEL PEREZ",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,1,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,2,$lo_titulocombinado);
                            $lo_hoja->write($li_line,3,"AMY CASTRO PAREDES",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,4,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,5,$lo_titulocombinado);
                            $lo_hoja->write($li_line,6,"DOUGLAS EDUARDO JASPE LÓPEZ",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,7,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,8,$lo_titulocombinado);
                            $lo_hoja->write($li_line,9,"OVELIO BARRERA CORRALES",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,10,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,11,$lo_titulocombinado);
                            $li_line++;       
                            $lo_hoja->write($li_line,0,"ECONOMISTA",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,1,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,2,$lo_titulocombinado);
                            $lo_hoja->write($li_line,3,"INGENIERO",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,4,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,5,$lo_titulocombinado);
                            $lo_hoja->write($li_line,6,"TENIENTE DE NAVIO",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,7,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,8,$lo_titulocombinado);
                            $lo_hoja->write($li_line,9,"CONTRALMIRANTE",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,10,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,11,$lo_titulocombinado);
                            $li_line++;       
                            $lo_hoja->write($li_line,0,"Comprador",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,1,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,2,$lo_titulocombinado);
                            $lo_hoja->write($li_line,3,"Jefe del Área Funcional de Compras",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,4,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,5,$lo_titulocombinado);
                            $lo_hoja->write($li_line,6,"Jefe de la Div. de Adquisiciones",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,7,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,8,$lo_titulocombinado);
                            $lo_hoja->write($li_line,9,"Dirección de Administración y Finánzas",$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,10,$lo_titulocombinado);
                            $lo_hoja->write_blank($li_line,11,$lo_titulocombinado);
                            $li_line++;       
                            
                            $lo_libro->close();
                            header("Content-Type: application/x-msexcel; name=\"analisiscotizacion.xls\"");
                            header("Content-Disposition: inline; filename=\"analisiscotizacion.xls\"");
                            $fh=fopen($lo_archivo, "rb");
                            fpassthru($fh);
                            unlink($lo_archivo);
                            print("<script language=JavaScript>");
                            print(" close();");
                            print("</script>");
                            unset($io_pdf);
			}
		}
    }
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_soc);
?> 