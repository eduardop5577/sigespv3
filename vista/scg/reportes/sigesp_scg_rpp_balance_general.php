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
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 22/09/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scg;
		
		$ls_descripcion="Gener� el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_balance_general.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo1,$as_titulo2,$as_titulo3,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,715,11,$as_titulo); // Agregar el t�tulo		
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo1);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,700,11,$as_titulo1); // Agregar el t�tulo
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo2);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,685,11,$as_titulo2); // Agregar el t�tulo
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo3);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,670,11,$as_titulo3); // Agregar el t�tulo

		$io_pdf->addText(510,740,7,$_SESSION["ls_database"]); // Agregar la Base de datos
		$io_pdf->addText(510,730,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(510,720,8,date("h:i a")); // Agregar la hora
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
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;		
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'left','width'=>115), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>305), // Justificaci�n y ancho de la columna
									   'saldo'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la columna
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
						   'denominacion'=>'<b>Denominaci�n</b>',
						   'saldo'=>'<b>Saldo</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_cueproacu($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_cueproacu
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci�n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'colGap'=>1, // separacion entre tablas
						 //'width'=>520, // Ancho de la tabla
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'left','width'=>105), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>295), // Justificaci�n y ancho de la columna
									   'saldo_cueproacu'=>array('justification'=>'right','width'=>75), // Justificaci�n y ancho de la columna
									   'saldo'=>array('justification'=>'right','width'=>75))); // Justificaci�n y ancho de la columna
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
						   'denominacion'=>'<b>Denominaci�n</b>',
						   'saldo_cueproacu'=>'',
						   'saldo'=>'<b>Saldo</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_cuentas_acreedoras($la_data_acreedoras,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cuentas_acreedoras
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle de las Cuentas acreedoras
		//	   Creado Por: Ing. Arnaldo Su�rez
		// Fecha Creaci�n: 14/05/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;		
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'left','width'=>105), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>295), // Justificaci�n y ancho de la columna
									   'saldo_cueproacu'=>array('justification'=>'right','width'=>75), // Justificaci�n y ancho de la columna
									   'saldo'=>array('justification'=>'right','width'=>75))); // Justificaci�n y ancho de la columna
		$la_columnas=array('cuenta'=>'',
						   'denominacion'=>'',
						   'saldo_cueproacu'=>'',
						   'saldo'=>'');
		$io_pdf->ezTable($la_data_acreedoras,$la_columnas,'',$la_config);
	}// end function uf_print_cuentas_acreedoras

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ld_total,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_totaldebe // Total debe
		//    Description : funci�n que imprime el fin de la cabecera de cada p�gina
		//	   Creado Por : Ing. Yozelin Barragan
		// Fecha Creaci�n : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_abrmon;
		
		$la_data=array(array('total'=>'<b>Total Pasivo + Capital + Resultado '.$ls_abrmon.'</b>','totalgen'=>$ld_total));
		$la_columna=array('total'=>'','totalgen'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L�neas
						 'fontSize' => 9, // Tama�o de Letras
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>520, // Ancho M�ximo de la tabla
						 'colGap'=>1, // separacion entre tablas
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>420), // Justificaci�n y ancho de la columna
						 			   'totalgen'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este m�todo realiza una consulta a los formatos de las cuentas
		//               para conocer los niveles de la escalera de las cuentas contables  
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
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
		} while ($li_posicion>=0);
	}// end function uf_init_niveles
	//-----------------------------------------------------------------------------------------------------------------------------------

	 require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	 require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	 $io_funciones=new class_funciones();
	 require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	 require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
	 require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
	 require_once("../../../shared/class_folder/class_sigesp_int.php");
	 require_once("../../../shared/class_folder/class_sigesp_int_scg.php");
	 $ls_tiporeporte="0";
	 $ls_bolivares="";
	 require_once("sigesp_scg_class_bal_general.php");
	 $io_report  = new sigesp_scg_class_bal_general();
	 $ls_bolivares ="Bs.";
		 
	 require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	 $io_fecha=new class_fecha();
	 require_once("class_funciones_scg.php");
	 $io_fun_scg=new class_funciones_scg();
	 $ia_niveles_scg[0]="";			
	 uf_init_niveles();
	 $li_total=count((array)$ia_niveles_scg)-1;
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	   $ls_cmbmes=$_GET["cmbmes"];
	   $ls_cmbagno=$_GET["cmbagno"];
	   $ls_last_day=$io_fecha->uf_last_day($ls_cmbmes,$ls_cmbagno);
	   $fechas=$ls_last_day;
	   $ldt_fechas=$io_funciones->uf_convertirdatetobd($ls_last_day);
	   $li_nivel=$_GET["cmbnivel"];
	   $ls_fecdesde = $_GET["fecdesde"];
	   $ls_fechasta = $_GET["fechasta"];
	   $ldt_fecdesde=$io_funciones->uf_convertirdatetobd($ls_fecdesde);
	   $ldt_fechasta=$io_funciones->uf_convertirdatetobd($ls_fechasta);
		$ls_codmon=$_GET["codmon"];
	   $ls_rango = $_GET["rango"];
	   //----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$li_ano=substr($ldt_periodo,0,4);
		$ls_pasivo=$_SESSION["la_empresa"]["pasivo"];
		$ls_resultado=$_SESSION["la_empresa"]["resultado"];
		$ls_capital=$_SESSION["la_empresa"]["capital"];
		$ls_acreedora=trim($_SESSION["la_empresa"]["orden_h"]);
		$arrResultado=$io_report->uf_buscar_tasacambio($ls_codmon);
		$ls_tascam1=$arrResultado["tascam1"];
		$ls_denmon=$arrResultado["denmon"];
		$ls_abrmon=$arrResultado["abrmon"];

		$ld_fechas=$io_funciones->uf_convertirfecmostrar($fechas);
		$ls_titulo="<b>BALANCE GENERAL</b>";
		$ls_titulo1="<b> ".$ls_nombre." </b>";
		if ($ls_rango == '1')
		{
			$ls_titulo2="<b> al ".$ld_fechas."</b>";
		}
		else
		{
			$ls_titulo2="<b> del ".$ls_fecdesde." al ".$ls_fechasta." </b>";
		} 
		
		$ls_titulo3="<b>(Expresado en ".$ls_denmon.")</b>";  
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar datastore con los datos del reporte
	$lb_valido=uf_insert_seguridad("<b>Balance General en PDF</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_balance_general($ldt_fechas,$li_nivel,false,$ls_rango,$ldt_fecdesde,$ldt_fechasta); 
	}
		if($lb_valido==false) // Existe alg�n error � no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}	
		else// Imprimimos el reporte
		{
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(4.8,3,3,3); // Configuraci�n de los margenes en cent�metros
			
			uf_print_encabezado_pagina($ls_titulo,$ls_titulo1,$ls_titulo2,$ls_titulo3,$io_pdf); // Imprimimos el encabezado de la p�gina
			$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n�mero de p�gina
			$li_tot=$io_report->ds_reporte->getRowCount("sc_cuenta");
           	$cont=0;	
			$ld_total=0;
			for($li_i=1;$li_i<=$li_tot;$li_i++)
			{
				$io_pdf->transaction('start'); // Iniciamos la transacci�n
				$thisPageNum=$io_pdf->ezPageCount;
				$ls_orden=$io_report->ds_reporte->data["orden"][$li_i];
				$li_nro_reg=$io_report->ds_reporte->data["num_reg"][$li_i];
				$ls_sc_cuenta=trim($io_report->ds_reporte->data["sc_cuenta"][$li_i]);

					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);
						$li_totfil=$li_totfil+$li_fila;
						$li_inicio=$li_len-$li_totfil;
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1;
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
				
				$ls_denominacion=$io_report->ds_reporte->data["denominacion"][$li_i];
				$ls_nivel=$io_report->ds_reporte->data["nivel"][$li_i];
				$ls_nivel=abs($ls_nivel);
				$ld_saldo=Number_format($io_report->ds_reporte->data["saldo"][$li_i],2,".","");
				
				$ld_saldo=($ld_saldo/$ls_tascam1);
				
				$ld_saldo_neto = 0;
				$li_cueproacu=$io_report->ds_reporte->data["cueproacu"][$li_i];
				$ls_estatus  =$io_report->ds_reporte->data["estatus"][$li_i];
				if($li_i>1)
				{
					$li_cueproacu_last=$io_report->ds_reporte->data["cueproacu"][$li_i-1];
					if(($li_cueproacu==1)&&($li_cueproacu_last == 1))
					{
					 $ld_saldo_last =Number_format($io_report->ds_reporte->data["saldo"][$li_i-1],2,".","");				 
					 $ld_saldo_last=($ld_saldo_last/$ls_tascam1);
					 $ld_saldo_neto = Number_format($ld_saldo_last + $ld_saldo,2,".","");
					 if($ld_saldo_neto<0)
					 { 
						$ld_saldo_neto="(".number_format(abs($ld_saldo_neto),2,",",".").")";
					 }
					 else
					 {
						$ld_saldo_neto=number_format(abs($ld_saldo_neto),2,",",".");	
					 }
					}
					else
					{
					 $ld_saldo_neto = "";
					}
				}
				
				$ls_rnivel=$io_report->ds_reporte->data["rnivel"][$li_i];
				if($ls_pasivo."000"==substr($ls_sc_cuenta,0,4))
				{
					$ld_total=Number_format($ld_total+$ld_saldo,2,".","");
				}
				if($ls_capital."000"==substr($ls_sc_cuenta,0,4))
				{
					$ld_total=Number_format($ld_total+$ld_saldo,2,".","");
				}
				
				if($ls_resultado."000"==substr($ls_sc_cuenta,0,4))
				{
					if(trim($ls_capital) != trim($ls_resultado))
				    {
					 $ld_total=Number_format($ld_total+$ld_saldo,2,".","");
					}
				}
				
				if($ld_saldo<0)
				 { 
					$ld_saldo="(".number_format(abs($ld_saldo),2,",",".").")";
				 }
				 else
				 {
					$ld_saldo=number_format(abs($ld_saldo),2,",",".");	
				 }
				 
				$cont=$cont+1;
				switch($ls_nivel)
				{
				 case 9:
						if($ls_estatus == 'C')
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');					  
						}
						else
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);
						}
				 break;
				 case 8:
						if($ls_estatus == 'C')
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');					  
						}
						else
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);
						}
				 break;
				 case 7:
						if($ls_estatus == 'C')
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');					  
						}
						else
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);
						}
				 break;
				 case 6:
					    if($ls_estatus == 'C')
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');					  
						}
						else
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);
						}	
				 break;
				 case 5:
					    if($ls_estatus == 'C')
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');					  
						}
						else
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);
						}
				 break;
				 case 4:
				        if($ls_estatus == 'C')
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');					  
						}
						else
						{
						 $la_data[$cont]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);
						}
				 break;
				 case 3:
					    if($ls_estatus == 'C')
						{
						 $la_data[$cont]=array('cuenta'=>'<b>'.$as_cuenta.'</b>','denominacion'=>'<b>        '.$ls_denominacion.'</b>','saldo_cueproacu'=>$ld_saldo,'saldo'=>'');					  
						}
						else
						{
						 $la_data[$cont]=array('cuenta'=>'<b>'.$as_cuenta.'</b>','denominacion'=>'<b>        '.$ls_denominacion.'</b>','saldo_cueproacu'=>'','saldo'=>$ld_saldo);
						}
				 break;
				 case 2:
				        $la_data[$cont]=array('cuenta'=>'','denominacion'=>'','saldo_cueproacu'=>'','saldo'=>'');
						$cont=$cont+1;
						if($ls_estatus == 'C')
						{
						   $la_data[$cont]=array('cuenta'=>'<b>'.$as_cuenta.'</b>','denominacion'=>'<b>        '.$ls_denominacion.'</b>','saldo_cueproacu'=>$ld_saldo,'saldo'=>'');					  
						}
						else
						{
						   $la_data[$cont]=array('cuenta'=>'<b>'.$as_cuenta.'</b>','denominacion'=>'    '.'<b>'.$ls_denominacion.'</b>','saldo_cueproacu'=>'','saldo'=>'<b>'.$ld_saldo.'</b>');
						}
				 break;
				 case 1:
						 if ($cont>1)
							 {
								$la_data[$cont]=array('cuenta'=>'','denominacion'=>'','saldo_cueproacu'=>'','saldo'=>'');
								$cont=$cont+1;
								if($ls_estatus == 'C')
								{
								 $la_data[$cont]=array('cuenta'=>'<b><i>'.$as_cuenta.'</b></i>','denominacion'=>'<b><i>'.$ls_denominacion.'</b></i>','saldo_cueproacu'=>'<b><i>'.$ld_saldo.'</b></i>','saldo'=>'<b><i></b></i>');					  
								}
								else
								{
								 $la_data[$cont]=array('cuenta'=>'<b><i>'.$as_cuenta.'</b></i>','denominacion'=>'<b><i>'.$ls_denominacion.'</b></i>','saldo_cueproacu'=>'','saldo'=>'<b><i>'.$ld_saldo.'</b></i>');
								}
							 }
							 else
							 {
								if($ls_estatus == 'C')
								{
								 $la_data[$cont]=array('cuenta'=>'<b><i>'.$as_cuenta.'</b></i>','denominacion'=>'<b><i>'.$ls_denominacion.'</b></i>','saldo_cueproacu'=>'<b><i>'.$ld_saldo.'</b></i>','saldo'=>'<b><i></b></i>');					  
								}
								else
								{
								 $la_data[$cont]=array('cuenta'=>'<b><i>'.$as_cuenta.'</b></i>','denominacion'=>'<b><i>'.$ls_denominacion.'</b></i>','saldo_cueproacu'=>'','saldo'=>'<b><i>'.$ld_saldo.'</b></i>');
								}
							 }
				}
				
			}//for
			uf_print_detalle_cueproacu($la_data,$io_pdf);
			$ld_total=$ld_total;
			if($ld_total<0)
			{
			   $ld_total="(".number_format(abs($ld_total),2,",",".").")";
			}
			else
			{
				$ld_total=number_format($ld_total,2,",",".");
			}
			uf_print_pie_cabecera($ld_total,$io_pdf); // Imprimimos pie de la cabecera
			$lb_ctas_acreedoras = $io_report->uf_obtener_cuentas_acreedoras($ldt_fechas,$li_nivel,$ls_rango,$ldt_fecdesde,$ldt_fechasta);
			$la_data_acreedoras = NULL; 
			if($lb_ctas_acreedoras)
			{
				$li_tot_acree=$io_report->ds_cuentas_acreedoras->getRowCount("sc_cuenta");
				$pos=0;	
				$ld_total=0;
				for($li_i=1;$li_i<=$li_tot_acree;$li_i++)
				{
				 $ls_sc_cuenta=trim($io_report->ds_cuentas_acreedoras->data["sc_cuenta"][$li_i]);
				 $li_totfil=0;
				 $as_cuenta="";
				 for($li=$li_total;$li>1;$li--)
				 {
					$li_ant=$ia_niveles_scg[$li-1];
					$li_act=$ia_niveles_scg[$li];
					$li_fila=$li_act-$li_ant;
					$li_len=strlen($ls_sc_cuenta);
					$li_totfil=$li_totfil+$li_fila;
					$li_inicio=$li_len-$li_totfil;
					if($li==$li_total)
					{
						$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
					}
					else
					{
						$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
					}
				 }
				 $li_fila=$ia_niveles_scg[1]+1;
				 $as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
				 $ls_denominacion=$io_report->ds_cuentas_acreedoras->data["denominacion"][$li_i];
				 $ls_nivel=$io_report->ds_cuentas_acreedoras->data["nivel"][$li_i];
				 $ls_nivel=abs($ls_nivel);
				 $ld_saldo=$io_report->ds_cuentas_acreedoras->data["saldo"][$li_i];
				 $ls_rnivel=$io_report->ds_cuentas_acreedoras->data["rnivel"][$li_i];
				 $ls_estatus  =$io_report->ds_cuentas_acreedoras->data["estatus"][$li_i];
				 if($ld_saldo<0)
				 { 
					$ld_saldo="(".number_format(abs($ld_saldo),2,",",".").")";
				 }
				 else
				 {
					$ld_saldo=number_format(abs($ld_saldo),2,",",".");	
				 }
				 	$pos++;
					switch($ls_nivel)
				    {
					 case 9:
					        if($ls_estatus == 'C')
						    {
							 $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');
							}
							else
							{
							 $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);
							}
					 break;
					 case 8:
					        if($ls_estatus == 'C')
						    {
							 $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');
							}
							else
							{
							 $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);
							}
					 break;
					 case 7:
					        if($ls_estatus == 'C')
						    {
							 $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');
							}
							else
							{
							 $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);
							}
					 break;
					 case 6:
					        if($ls_estatus == 'C')
						    {
							 $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');					  
							}
							else
							{
							 $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);					  
							}
					 break;
					 case 5:
					        if($ls_estatus == 'C')
						    {
							 $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');					  
							}
							else
							{
							 $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);					  
							}
					 break;
					 case 4:
					        if($ls_estatus == 'C')
						    {
					         $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>$ld_saldo,'saldo'=>'');					  
							}
							{
							 $la_data_acreedoras[$pos]=array('cuenta'=>$as_cuenta,'denominacion'=>'            '.$ls_denominacion,'saldo_cueproacu'=>'','saldo'=>$ld_saldo);					  
							}
					 break;
					 case 3:
					        if($ls_estatus == 'C')
							{
							 $la_data_acreedoras[$pos]=array('cuenta'=>'<b>'.$as_cuenta.'</b>','denominacion'=>'<b>        '.$ls_denominacion.'</b>','saldo_cueproacu'=>$ld_saldo,'saldo'=>'');
							}
							else
							{
							 $la_data_acreedoras[$pos]=array('cuenta'=>'<b>'.$as_cuenta.'</b>','denominacion'=>'<b>        '.$ls_denominacion.'</b>','saldo_cueproacu'=>'','saldo'=>$ld_saldo);
							}
					 break;
					 case 2:
					        if($ls_estatus == 'C')
							{
							 $la_data_acreedoras[$pos]=array('cuenta'=>'','denominacion'=>'','saldo_cueproacu'=>'','saldo'=>'');
						     $pos++;
						     $la_data_acreedoras[$pos]=array('cuenta'=>'<b>'.$as_cuenta.'</b>','denominacion'=>'    '.'<b>'.$ls_denominacion.'</b>','saldo_cueproacu'=>'<b>'.$ld_saldo.'</b>','saldo'=>'');
							}
							else
							{
							 $la_data_acreedoras[$pos]=array('cuenta'=>'','denominacion'=>'','saldo_cueproacu'=>'','saldo'=>'');
						     $pos++;
						     $la_data_acreedoras[$pos]=array('cuenta'=>'<b>'.$as_cuenta.'</b>','denominacion'=>'    '.'<b>'.$ls_denominacion.'</b>','saldo_cueproacu'=>'','saldo'=>'<b>'.$ld_saldo.'</b>');
							}
					 break;
					 case 1:
					        if($ls_estatus == 'C')
							{
							 if ($pos>1)
							 {
								$la_data_acreedoras[$pos]=array('cuenta'=>'','denominacion'=>'','saldo_cueproacu'=>'','saldo'=>'');
								$pos++;
								$la_data_acreedoras[$pos]=array('cuenta'=>'<b><i>'.$as_cuenta.'</b></i>','denominacion'=>'<b><i>'.$ls_denominacion.'</b></i>','saldo_cueproacu'=>'<b><i>'.$ld_saldo.'</i></b>','saldo'=>'');
							 }
							 else
							 {
								$la_data_acreedoras[$pos]=array('cuenta'=>'<b><i>'.$as_cuenta.'</b></i>','denominacion'=>'<b><i>'.$ls_denominacion.'</b></i>','saldo_cueproacu'=>'<b><i>'.$ld_saldo.'</i></b>','saldo'=>'');
							 }
							}
							else
							{
							 if ($pos>1)
							 {
								$la_data_acreedoras[$pos]=array('cuenta'=>'','denominacion'=>'','saldo_cueproacu'=>'','saldo'=>'');
								$pos++;
								$la_data_acreedoras[$pos]=array('cuenta'=>'<b><i>'.$as_cuenta.'</b></i>','denominacion'=>'<b><i>'.$ls_denominacion.'</b></i>','saldo_cueproacu'=>'','saldo'=>'<b><i>'.$ld_saldo.'</i></b>');
							 }
							 else
							 {
								$la_data_acreedoras[$pos]=array('cuenta'=>'<b><i>'.$as_cuenta.'</b></i>','denominacion'=>'<b><i>'.$ls_denominacion.'</b></i>','saldo_cueproacu'=>'','saldo'=>'<b><i>'.$ld_saldo.'</i></b>');
							 }
							 
							}
					 break;
					}
				}
			}
			uf_print_cuentas_acreedoras($la_data_acreedoras,$io_pdf);
			unset($la_data_acreedoras);
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