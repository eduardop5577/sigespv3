<?PHP
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

    session_start();//OCHINA
	ini_set('memory_limit','1024M');
 	ini_set('max_execution_time ','0');  
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "opener.document.form1.submit();"	;	
		print "close();";
		print "</script>";		
	}	
	//---------------------------------------------------------------------------------------------------------------
	
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
		global $io_fun_compra;
		$ls_descripcion="Generó el Reporte Análisis de Cotización";
		$lb_valido=$io_fun_compra->uf_load_seguridad_reporte("SOC","sigesp_soc_p_analisis_cotizacion.php",$ls_descripcion);
		return $lb_valido;
	}
	//------------------------------------------------------------------------------------------------------

        //---------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_numanacot,$ad_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		    Acess: private 
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime el banner del reporte
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 17/06/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],35,530,80,40); // Agregar Logo
		$io_pdf->add_texto(140,10,8,"<b>REPÚBLICA BOLIVARIANA DE VENEZUELA</b>");
		$io_pdf->add_texto(133,13,8,"<b>MINISTERIO DEL PODER POPULAR PARA LA DEFENSA</b>");
		$io_pdf->add_texto(148,16,8,"<b>VICEMINISTERIO DE SERVICIOS</b>");
		$io_pdf->add_texto(134,19,8,"<b>DIRECCIÓN GENERAL DE EMPRESAS Y SERVICIOS</b>");
		$io_pdf->add_texto(128,22,8,"<b>OFICINA COORDINADORA DE HIDROGRAFÍA Y NAVEGACIÓN</b>");
		//$io_pdf->add_texto(325,18,10,"$ad_fecha");	
                
		$la_data[1]=array('columna1'=>'ANÁLISIS Y RECOMENDACION',
		                 'columna2'=>'FECHA',
		                 'columna3'=>'Maiquetía,',
		                 'columna4'=>'');
		$la_columna=array('columna1'=>'','columna2'=>'','columna3'=>'','columna4'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>950, // Ancho de la tabla
						 'maxWidth'=>950, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'center','width'=>350), // Justificación y ancho de la columna
						 	       'columna2'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 	       'columna3'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 	       'columna4'=>array('justification'=>'center','width'=>400))); // Justificación y ancho de la columna
		$io_pdf->ezSetDy(-75);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);                
	}// end function uf_print_encabezado_pagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
        //--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_proveedores($la_cotizaciones,$io_ds_detalle,$io_ds_detallepro,$la_countcot,$io_pdf,$as_numanacot,$as_tipsolcot)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_proveedores
		//		    Acess: private 
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime el el listado de  proveedores participantes
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 18/06/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_class_report;
		global $io_funciones, $ls_bolivares;		
		global $io_pdf;
		
		$io_ds_detallepro1=new class_datastore();
		$li_totalcotizaciones=count((array)$la_cotizaciones);
		$io_ds_detallepro1->data=$io_ds_detallepro->data;
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
		$la_data[1]=array('columna1'=>'<b>ADQUISICIÓN DE MATERIAL ELÉCTRICO  EL CUAL SERA UTILIZADO EN  LA ESCUELA DE VEHICULOS ANFIBIOS Y ARTILLERIA DE LA INFANTERIA DE MARINA BOLIVARIANA,  SOLICITADO POR EL COMANDANTE DE DIESCASIVA, SEGÚN OFICIO 0119 DE FECHA 28OCT19.</b>',
                                  'columna2'=>$colum[1],
                                  'columna3'=>$colum[2],
                                  'columna4'=>$colum[3]);
		$la_columna=array('columna1'=>'','columna2'=>'','columna3'=>'','columna4'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>950, // Ancho de la tabla
						 'maxWidth'=>950, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'left','width'=>350), // Justificación y ancho de la columna
                                                               'columna2'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
                                                               'columna3'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
                                                               'columna4'=>array('justification'=>'center','width'=>200))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
                
                //IMPRIMIENDO EL SEGUNDO TITULO
		$la_data[0]['columna1']="<b>RENGLON</b>";
		$la_data[0]['columna2']="<b>CANT.</b>";
		$la_data[0]['columna3']="<b>UNID.</b>";
		$la_data[0]['columna4']="<b>ARTICULOS</b>";
		$la_data[0]['columna5']="<b>PRECIO UNITARIO B.S</b>";
		$la_data[0]['columna6']="<b>SUBTOTAL B.S</b>";
		$la_data[0]['columna7']="<b>PRECIO UNITARIO B.S</b>";
		$la_data[0]['columna8']="<b>SUBTOTAL B.S</b>";
		$la_data[0]['columna9']="<b>PRECIO UNITARIO B.S</b>";
		$la_data[0]['columna10']="<b>SUBTOTAL B.S</b>";
		$la_columna=array('columna1'=>'','columna2'=>'','columna3'=>'','columna4'=>'','columna5'=>'','columna6'=>'','columna7'=>'','columna8'=>'','columna9'=>'','columna10'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>950, // Ancho de la tabla
						 'maxWidth'=>950, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
                                                               'columna2'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
                                                               'columna3'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
                                                               'columna4'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
                                                               'columna5'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
                                                               'columna6'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
                                                               'columna7'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
                                                               'columna8'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
                                                               'columna9'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
                                                               'columna10'=>array('justification'=>'center','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_config);
                
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
                    {	$li_a++;
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
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>950, // Ancho de la tabla
						 'maxWidth'=>950, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna1'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
                                                               'columna2'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
                                                               'columna3'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
                                                               'columna4'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
                                                               'columna5'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
                                                               'columna6'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
                                                               'columna7'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
                                                               'columna8'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
                                                               'columna9'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
                                                               'columna10'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

                $li_totalmonto1=number_format($li_totalsubtotal1+$li_totaliva1,2,",",".");
                $li_totalsubtotal1=number_format($li_totalsubtotal1,2,",",".");
                $li_totaliva1=number_format($li_totaliva1,2,",",".");
                $li_totalmonto2=number_format($li_totalsubtotal2+$li_totaliva2,2,",",".");
                $li_totalsubtotal2=number_format($li_totalsubtotal2,2,",",".");
                $li_totaliva2=number_format($li_totaliva2,2,",",".");
                $li_totalmonto3=number_format($li_totalsubtotal3+$li_totaliva3,2,",",".");
                $li_totalsubtotal3=number_format($li_totalsubtotal3,2,",",".");
                $li_totaliva3=number_format($li_totaliva3,2,",",".");
                
                $la_data[0]["columna0"]="";
                $la_data[1]["columna0"]="";
                $la_data[2]["columna0"]="";
                $la_data[0]["columna5"]="BASE IMPONIBLE B.S";
                $la_data[0]["columna6"]=$li_totalsubtotal1;
                $la_data[1]["columna5"]="IVA ".$li_poriva1." %";
                $la_data[1]["columna6"]=$li_totaliva1;
                $la_data[2]["columna5"]="TOTAL B.S";
                $la_data[2]["columna6"]=$li_totalmonto1;
                $la_data[0]["columna7"]="BASE IMPONIBLE B.S";
                $la_data[0]["columna8"]=$li_totalsubtotal2;
                $la_data[1]["columna7"]="IVA ".$li_poriva2." %";
                $la_data[1]["columna8"]=$li_totaliva2;
                $la_data[2]["columna7"]="TOTAL B.S";
                $la_data[2]["columna8"]=$li_totalmonto2;
                $la_data[0]["columna9"]="BASE IMPONIBLE B.S";
                $la_data[0]["columna10"]=$li_totalsubtotal3;
                $la_data[1]["columna9"]="IVA ".$li_poriva3." %";
                $la_data[1]["columna10"]=$li_totaliva3;
                $la_data[2]["columna9"]="TOTAL B.S";
                $la_data[2]["columna10"]=$li_totalmonto3;
		$la_columna=array('columna0'=>'','columna5'=>'','columna6'=>'','columna7'=>'','columna8'=>'','columna9'=>'','columna10'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>950, // Ancho de la tabla
						 'maxWidth'=>950, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna0'=>array('justification'=>'center','width'=>350), // Justificación y ancho de la columna
                                                               'columna5'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
                                                               'columna6'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
                                                               'columna7'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
                                                               'columna8'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
                                                               'columna9'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
                                                               'columna10'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}//fin de uf_print_proveedores
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_items($la_cotizaciones,$io_ds_detalleprocot,$la_ganadores,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_items
		//		    Acess: private 
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime los items del analisis de cotizacion y su respectivo proveedor
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 17/06/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_class_report;
		global $io_funciones, $ls_bolivares;		
		global $io_pdf;
                
                $li_totalcotizaciones=count((array)$la_cotizaciones);
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
                
		$la_columna=array('columna0'=>'','columna1'=>'','columna2'=>'','columna3'=>'','columna4'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>950, // Ancho de la tabla
						 'maxWidth'=>950, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna0'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
                                                               'columna1'=>array('justification'=>'center','width'=>250), // Justificación y ancho de la columna
                                                               'columna2'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
                                                               'columna3'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
                                                               'columna4'=>array('justification'=>'center','width'=>200))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

                $la_data[0]["columna0"]="<b>RECOMENDACIONES:</b> SE RECOMIENDA ASIGNAR LA PRESENTE ORDEN DE COMPRA A LA EMPRESA <b>".$la_ganadores[0]["nompro"]."</b>, MOTIVADO A QUE PRESENTA LA MEJOR OFERTA ENTRE LOS PARTICIPANTES Y EN EL MERCADO, TOMANDO EN CONSIDERACIÓN LOS ASPECTOS ANTERIORMENTE EVALUADOS.";
                $la_columna=array('columna0'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>950, // Ancho de la tabla
						 'maxWidth'=>950, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('columna0'=>array('justification'=>'left','width'=>950))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}//fin de uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

        //------------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_pagina($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_pagina
		//		    Acess: private 
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime el pie del reporte
		//	   Creado Por: Ing. Laura Cabré                  Modificado Por: Ing. Gloriely Fréitez
		// Fecha Creación: 17/06/2007                 Fecha de Modificación: 01/04/2008
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
				
		$io_pdf->setStrokeColor(0,0,0);
		
		$io_pdf->line(30,60,135,60);	//VERTICAL
		$io_pdf->addText(40,48,8,"<b>LUIS MIGUEL PEREZ</b>");
		$io_pdf->addText(50,40,8,"<b>ECONOMISTA</b>");
		$io_pdf->addText(55,32,8,"<b>Comprador</b>");

		$io_pdf->line(250,60,400,60);	//VERTICAL
                $io_pdf->addText(280,48,8,"<b>AMY CASTRO PAREDES</b>");
		$io_pdf->addText(300,40,8,"<b>INGENIERO</b>");
		$io_pdf->addText(255,32,8,"<b>Jefe del Área Funcional de Compras</b>");
                
		$io_pdf->line(500,60,650,60);	//VERTICAL
		$io_pdf->addText(505,48,8,"<b>DOUGLAS EDUARDO JASPE LÓPEZ</b>");
		$io_pdf->addText(535,40,8,"<b>TENIENTE DE NAVIO</b>");
		$io_pdf->addText(513,32,8,"<b>Jefe de la Div. de Adquisiciones</b>");

		$io_pdf->line(750,60,900,60);	//VERTICAL                
		$io_pdf->addText(760,48,8,"<b>OVELIO BARRERA CORRALES</b>");
		$io_pdf->addText(790,40,8,"<b>CONTRALMIRANTE</b>");
		$io_pdf->addText(750,32,8,"<b>Dirección de Administración y Finánzas</b>");
	
		
	}// end function uf_print_pie_pagina
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
  	
	require_once("sigesp_soc_class_report.php");
	require_once('../../shared/class_folder/class_pdf.php');
	require_once("../class_folder/class_funciones_soc.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
	$io_class_report = new sigesp_soc_class_report();
	$io_funciones    = new class_funciones();
	$io_fun_compra   = new class_funciones_soc();
	$ls_tiporeporte=$io_fun_compra->uf_obtenervalor_get("tiporeporte",1);
	$ls_bolivares="Bs.";
	$io_ds_detalle=new class_datastore();
	$io_ds_detallecot=new class_datastore();
	$io_ds_detallepro=new class_datastore();
	$io_ds_detalleprocot=new class_datastore();
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_class_report=new sigesp_soc_class_reportbsf();
		$ls_bolivares="Bs.F.";
	}
        
	set_time_limit(3000);	
	$io_pdf=new class_pdf('LEGAL','landscape'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->numerar_paginas(7);	
	$io_pdf->set_margenes(10,30,3,3);	
	$ls_tipsolcot=$_GET["tipsolcot"];
	$ls_numanacot=$_GET["numanacot"];
	$ld_fecha=$_GET["fecha"];
	$ls_observacion=$_GET["observacion"];	
	$lb_valido=uf_insert_seguridad();
	if($lb_valido)
	{
		uf_print_encabezado_pagina($ls_numanacot,$ld_fecha,$io_pdf);
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
			$arrResultado=$io_class_report->uf_count_cotizaciones($ls_numanacot,$ls_countcot,$ls_tipsolcot);
			$ls_countcot = $arrResultado['aa_proveedores'];
			$lb_valido = $arrResultado['lb_valido'];
			$ls_countcot=count((array)$ls_countcot);
			if($lb_valido)
			{
				$la_ganadores=$io_class_report->uf_select_cotizacion_analisis($ls_numanacot,$ls_tipsolcot);
				uf_print_proveedores($la_cotizaciones,$io_ds_detalle,$io_ds_detallepro,$ls_countcot,$io_pdf,$ls_numanacot,$ls_tipsolcot);
				uf_print_items($la_cotizaciones,$io_ds_detalleprocot,$la_ganadores,$io_pdf);
				uf_print_pie_pagina($io_pdf);
				$io_pdf->ezStream();
				unset($io_pdf);
			}
		}
	}
	if(!$lb_valido)
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que reportar');"); 
		print(" close();");
		print("</script>");	
	}
?> 
