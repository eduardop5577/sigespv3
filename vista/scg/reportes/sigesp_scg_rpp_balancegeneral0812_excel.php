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
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_balancegeneral0812.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	require_once("../../../shared/class_folder/class_sigesp_int.php");
	require_once("../../../shared/class_folder/class_sigesp_int_scg.php");
	require_once("../../../shared/class_folder/class_sigesp_int_spi.php");
	require_once("../../../shared/class_folder/class_sigesp_int_spg.php");
	require_once("class_funciones_scg.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
    require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "balancegeneral0812.xls");
	$io_funciones=new class_funciones();
	$io_fecha=new class_fecha();
	$io_fun_scg = new class_funciones_scg();
	$lo_libro   = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja    =  &$lo_libro->addworksheet();
	$ls_tiporeporte="0";
	$ls_bolivares="";
	require_once("sigesp_scg_class_comparados.php");
	$io_report  = new sigesp_scg_class_comparados();
	$ls_bolivares ="Bolivares";
	$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	$li_ano=substr($ldt_periodo,0,4);
	$ls_etiqueta=$_GET["etiqueta"];
	if($ls_etiqueta=="Mensual")
	{
		$ls_combo=$_GET["mesdes"];
		$ls_combomes=$_GET["meshas"];
		$li_mesdes=substr($ls_combo,0,2);
		$li_meshas=substr($ls_combomes,0,2); 
		$li_mesdes=intval($li_mesdes);
		$li_meshas=intval($li_meshas); 
		$li_cant_mes=1;
		if($li_meshas==12)
		{
			$io_report->li_mes_prox=0;
		}
		elseif($li_meshas<=11)
		{
			$io_report->li_mes_prox=1;
		}
		$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
		$ls_combo=$ls_combo.$ls_combomes;
		$ls_etiqueta = "Mes : ".$ls_meses;
	}
	else
	{
		$ls_combo=$_GET["mesdes"];
		$li_mesdes=substr($ls_combo,0,2);
		$li_meshas=substr($ls_combo,2,2); 
		$li_mesdes=intval($li_mesdes);
		$li_meshas=intval($li_meshas); 
		if($ls_etiqueta=="Bimestral")
		{
			$li_cant_mes=2;
			if($li_meshas==12)
			{
				$io_report->li_mes_prox=0;
			}
			elseif($li_meshas<=10)
			{
				$io_report->li_mes_prox=2;
			}
			$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			$ls_etiqueta = "Bimestre : ".$ls_meses;
		}
		if($ls_etiqueta=="Trimestral")
		{
			$li_cant_mes=3;
			if($li_meshas==12)
			{
				$io_report->li_mes_prox=0;
			}
			elseif($li_meshas<=9)
			{
				$io_report->li_mes_prox=3;
			}
			$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			$ls_etiqueta = "Trimestre : ".$ls_meses;
		}
		if($ls_etiqueta=="Semestral")
		{
			$li_cant_mes=6;
			if($li_meshas==12)
			{
				$io_report->li_mes_prox=0;
			}
			elseif($li_meshas<=6)
			{
				$io_report->li_mes_prox=6;
			}
			$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			$ls_etiqueta = "Semestre : ".$ls_meses;
		}
	}
	//------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
    {    ///////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_init_niveles
        //         Access: public
        //        Returns: vacio     
        //    Description: Este método realiza una consulta a los formatos de las cuentas
        //               para conocer los niveles de la escalera de las cuentas contables  
        //////////////////////////////////////////////////////////////////////////////////////////////////////
        global $io_funciones,$ia_niveles_scg;
        
        $ls_formato=""; 
		$li_posicion=0; 
		$li_indice=0;
        $dat_emp=$_SESSION["la_empresa"];
        //contable
        $ls_formato = trim($dat_emp["formcont"])."-";
        //print "ls_formato : $ls_formato <br>";
        $li_posicion = 1 ;
        $li_indice   = 1 ;
        $li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
        do
        {
            $ia_niveles_scg[$li_indice] = $li_posicion;
            $li_indice   = $li_indice+1;
            $li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
            //print "pos: $li_posicion   <br>";
        } while ($li_posicion>=0);
        //var_dump($ia_niveles_scg);
    }// end function uf_init_niveles
	//--------------------------------------------------------------------------------------------------------------------------------     
	
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
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
		uf_init_niveles();
    	$ls_mesdes=substr($ls_combo,0,2);
		$ls_meshas=substr($ls_combo,2,2);
		$ls_diades="01";
		$ls_diahas=$io_fecha->uf_last_day($ls_meshas,$li_ano);
		$ldt_fecdes=$ls_diades."/".$ls_mesdes."/".$li_ano;
		$ldt_fechas=$ls_diahas;
		$ld_fechas=$io_funciones->uf_convertirfecmostrar($ldt_fechas);
		$ls_titulo="BALANCE GENERAL";
		$ls_titulo1="(En ".$ls_bolivares.")";
		$ls_periodo = "Desde el ".$ldt_fecdes." al ".$ld_fechas;
		$ls_formcont =  $_SESSION["la_empresa"]["formcont"];
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar datastore con los datos del reporte
	$lb_valido=uf_insert_seguridad("<b>Instructivo 08 Comparado Balance General</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_balance_general_comparado_ins08($ldt_fecdes,$ldt_fechas,$li_cant_mes); 
	}
		if($lb_valido==false) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}	
		else// Imprimimos el reporte
		{
			//
			set_time_limit(1800);
			$lo_encabezado= &$lo_libro->addformat();
			$lo_encabezado->set_bold();
			$lo_encabezado->set_font("Verdana");
			$lo_encabezado->set_align('center');
			$lo_encabezado->set_size('11');
			$lo_titulo= &$lo_libro->addformat();
			$lo_titulo->set_bold();
			$lo_titulo->set_font("Verdana");
			$lo_titulo->set_align('center');
			$lo_titulo->set_size('9');
			$lo_datacenter= &$lo_libro->addformat();
			$lo_datacenter->set_font("Verdana");
			$lo_datacenter->set_align('center');
			$lo_datacenter->set_size('9');
			$lo_dataleft= &$lo_libro->addformat();
			$lo_dataleft->set_text_wrap();
			$lo_dataleft->set_font("Verdana");
			$lo_dataleft->set_align('left');
			$lo_dataleft->set_size('9');
			$lo_dataleftbold= &$lo_libro->addformat();
			$lo_dataleftbold->set_text_wrap();
			$lo_dataleftbold->set_font("Verdana");
			$lo_dataleftbold->set_align('left');
			$lo_dataleftbold->set_size('9');
			$lo_dataleftbold->set_bold();
			$lo_dataright= &$lo_libro->addformat(array('num_format' =>'#,##0.00;[Red](#,##0.00)'));
			$lo_dataright->set_font("Verdana");
			$lo_dataright->set_align('right');
			$lo_dataright->set_size('9');
			$lo_datarightbold= &$lo_libro->addformat(array('num_format' =>'#,##0.00;[Red](#,##0.00)'));
			$lo_datarightbold->set_font("Verdana");
			$lo_datarightbold->set_align('right');
			$lo_datarightbold->set_size('9');
			$lo_datarightbold->set_bold();
			$lo_datacuenta= &$lo_libro->addformat();
			$lo_datacuenta->set_font("Verdana");
			$lo_datacuenta->set_align('right');
			$lo_datacuenta->set_size('9');

			$lo_hoja->set_column(0,0,40);
		    $lo_hoja->set_column(1,1,60);
			$lo_hoja->write(0, 0, "CODIGO PRESUPUESTARIO DEL ENTE: ",$lo_dataleftbold); // Agregar el título
		    $lo_hoja->write(1, 0, "DENOMINACION DEL ENTE: ",$lo_dataleftbold); // Agregar el título
		    $lo_hoja->write(2, 0, "ORGANO DE ADSCRIPCION: ",$lo_dataleftbold); // Agregar el título
		    $lo_hoja->write(3, 0, "PERIODO: ",$lo_dataleftbold); // Agregar el título
			$lo_hoja->write(0, 1,$_SESSION["la_empresa"]["codasiona"],$lo_dataleft); // Agregar el título
		    $lo_hoja->write(1, 1,$_SESSION["la_empresa"]["nombre"],$lo_dataleft); // Agregar el título
		    $lo_hoja->write(2, 1,$_SESSION["la_empresa"]["nomorgads"],$lo_dataleft); // Agregar el título
		    $lo_hoja->write(3, 1,strtoupper($ls_periodo),$lo_dataleft); // Agregar el título
		    $lo_hoja->write(5, 3, $ls_titulo,$lo_encabezado);
		    $lo_hoja->write(6, 3, $ls_titulo1,$lo_encabezado);
            $li_row = 8;
			$lo_hoja->set_column(2,7,40);
			$lo_hoja->set_column(8,9,50);
			$lo_hoja->write($li_row, 5,strtoupper($ls_etiqueta),$lo_titulo); // Agregar el título
		    $lo_hoja->write($li_row, 7,"Variacion Saldo Ejecutado - Saldo Programado",$lo_titulo); // Agregar el título
			$li_row++;
			$lo_hoja->write($li_row, 0,"Codigo",$lo_titulo); // Agregar el título
			$lo_hoja->write($li_row, 1,"Denominacion",$lo_titulo); // Agregar el título
			$lo_hoja->write($li_row, 2,"Saldo Presupuesto Real Año Anterior",$lo_titulo); // Agregar el título
			$lo_hoja->write($li_row, 3,"Saldo Presupuesto Aprobado",$lo_titulo); // Agregar el título
			$lo_hoja->write($li_row, 4,"Saldo Presupuesto Modificado",$lo_titulo); // Agregar el título
			$lo_hoja->write($li_row, 5,"Saldo Programado",$lo_titulo); // Agregar el título
			$lo_hoja->write($li_row, 6,"Saldo Ejecutado",$lo_titulo); // Agregar el título
			$lo_hoja->write($li_row, 7,"Absoluta",$lo_titulo); // Agregar el título
			$lo_hoja->write($li_row, 8,"Porcentual",$lo_titulo); // Agregar el título
			$lo_hoja->write($li_row, 9,"Var. Saldo Ejecutado Periodo N, menos Periodo N-1",$lo_titulo); // Agregar el título
			$li_tot=$io_report->ds_cuentas->getRowCount("sc_cuenta");
            $ld_saldo4="";
		    $ld_saldo3="";  
		    $ld_saldo2="";
			$ld_total=0;
			
			// TOTALES PASIVO + PATRIMONIO
 			$ld_total_saldo_real_ant      = 0;
			$ld_total_saldo_apro          = 0;
			$ld_total_saldo_modi          = 0;
			$ld_total_saldo_prog          = 0;
			$ld_total_saldo_ejec          = 0;
			$ld_total_var_saldo_ejec_ant  = 0;
			
			// TOTALES CUENTAS A COBRAR  - CUENTAS NETAS
 			$ld_ctacobnetas_saldo_real_ant      = 0;
			$ld_ctacobnetas_saldo_apro          = 0;
			$ld_ctacobnetas_saldo_modi          = 0;
			$ld_ctacobnetas_saldo_prog          = 0;
			$ld_ctacobnetas_saldo_ejec          = 0;
			$ld_ctacobnetas_var_saldo_ejec_ant  = 0;
			
			// TOTALES CUENTAS ACTIVO FIJO NETO
 			$ld_ctaactfijneto_saldo_real_ant      = 0;
			$ld_ctaactfijneto_saldo_apro          = 0;
			$ld_ctaactfijneto_saldo_modi          = 0;
			$ld_ctaactfijneto_saldo_prog          = 0;
			$ld_ctaactfijneto_saldo_ejec          = 0;
			$ld_ctaactfijneto_var_saldo_ejec_ant  = 0;
			
			// TOTALES CUENTAS ACTIVO INTANGIBLE
 			$ld_ctaactintneto_saldo_real_ant      = 0;
			$ld_ctaactintneto_saldo_apro          = 0;
			$ld_ctaactintneto_saldo_modi          = 0;
			$ld_ctaactintneto_saldo_prog          = 0;
			$ld_ctaactintneto_saldo_ejec          = 0;
			$ld_ctaactintneto_var_saldo_ejec_ant  = 0;
			
			for($li_i=1;$li_i<=$li_tot;$li_i++)
			{
				$ls_cuenta       		= $io_report->ds_cuentas->getValue("sc_cuenta",$li_i);	
				$ls_denominacion 		= $io_report->ds_cuentas->getValue("denominacion",$li_i);
				$ld_saldo_real_ant       = $io_report->ds_cuentas->getValue("saldo_real_ant",$li_i);
				$ld_saldo_apro           = $io_report->ds_cuentas->getValue("saldo_apro",$li_i);
				$ld_saldo_modi           = $io_report->ds_cuentas->getValue("saldo_modi",$li_i);
				$ld_saldo_prog           = $io_report->ds_cuentas->getValue("saldo_prog",$li_i);
				$ld_saldo_ejec           = $io_report->ds_cuentas->getValue("saldo_ejec",$li_i);
				$ld_var_saldo_ejec_ant   = $io_report->ds_cuentas->getValue("var_saldo_ejec_ant",$li_i);
				$li_nivel                = $io_report->ds_cuentas->getValue("nivel",$li_i);
				$li_row++;
				if(empty($ld_saldo_real_ant))
				{
					$ld_saldo_real_ant = 0;
				}
				
				if(empty($ld_saldo_apro))
				{
					$ld_saldo_apro = 0;
				}
				
				if(empty($ld_saldo_modi))
				{
					$ld_saldo_modi = 0;
				}
				
				if(empty($ld_saldo_prog))
				{
					$ld_saldo_prog = 0;
				}
				
				if(empty($ld_saldo_ejec))
				{
					$ld_saldo_ejec = 0;
				}
			   
				if(empty($ld_var_saldo_ejec_ant))
				{
					$ld_var_saldo_ejec_ant = 0;
				}
			   
				$ld_variacion_absoluta = 0;
				$ld_variacion_porcentual = 0;
				
				if($ld_saldo_prog>0)
				{
					$ld_variacion_porcentual = ($ld_saldo_ejec/$ld_saldo_prog)*100;
				}
				
				$ld_variacion_absoluta = abs($ld_saldo_prog - $ld_saldo_ejec);
				$ls_cuenta_nva = uf_formato_salida( $ls_cuenta,$li_nivel,$ls_formcont,".");
				
				if((trim($ls_cuenta_nva) == '2')||(trim($ls_cuenta_nva) == '3')&&($li_nivel == 1))
				{
					$ld_total_saldo_real_ant      += $ld_saldo_real_ant;
					$ld_total_saldo_apro          += $ld_saldo_apro;
					$ld_total_saldo_modi          += $ld_saldo_modi;
					$ld_total_saldo_prog          += $ld_saldo_prog;
					$ld_total_saldo_ejec          += $ld_saldo_ejec;
					$ld_total_var_saldo_ejec_ant  += $ld_var_saldo_ejec_ant;				
				}
			   
				if((trim($ls_cuenta_nva) == '1.1.2.03')||(trim($ls_cuenta_nva) == '2.2.4.01.01'))
				{
					switch($ls_cuenta_nva)
					{
						case '1.1.2.03' :
							$ld_ctacobnetas_saldo_real_ant      += $ld_saldo_real_ant;
							$ld_ctacobnetas_saldo_apro          += $ld_saldo_apro;
							$ld_ctacobnetas_saldo_modi          += $ld_saldo_modi;
							$ld_ctacobnetas_saldo_prog          += $ld_saldo_prog;
							$ld_ctacobnetas_saldo_ejec          += $ld_saldo_ejec;
							$ld_ctacobnetas_var_saldo_ejec_ant  += $ld_var_saldo_ejec_ant;
						break;
						
						case '2.2.4.01.01' :
							$ld_ctacobnetas_saldo_real_ant      -= $ld_saldo_real_ant;
							$ld_ctacobnetas_saldo_apro          -= $ld_saldo_apro;
							$ld_ctacobnetas_saldo_modi          -= $ld_saldo_modi;
							$ld_ctacobnetas_saldo_prog          -= $ld_saldo_prog;
							$ld_ctacobnetas_saldo_ejec          -= $ld_saldo_ejec;
							$ld_ctacobnetas_var_saldo_ejec_ant  -= $ld_var_saldo_ejec_ant;
							$ls_denominacion = "Menos ".$ls_denominacion;
						break;
					}
				}
			   
				if((trim($ls_cuenta_nva) == '1.1.3')||(trim($ls_cuenta_nva) == '2.2.5.01'))
				{
					switch($ls_cuenta_nva)
					{
						case '1.1.3' :
							$ld_ctaactfijneto_saldo_real_ant      += $ld_saldo_real_ant;
							$ld_ctaactfijneto_saldo_apro          += $ld_saldo_apro;
							$ld_ctaactfijneto_saldo_modi          += $ld_saldo_modi;
							$ld_ctaactfijneto_saldo_prog          += $ld_saldo_prog;
							$ld_ctaactfijneto_saldo_ejec          += $ld_saldo_ejec;
							$ld_ctaactfijneto_var_saldo_ejec_ant  += $ld_var_saldo_ejec_ant;
						break;
						
						case '2.2.5.01' :
							$ld_ctaactfijneto_saldo_real_ant      -= $ld_saldo_real_ant;
							$ld_ctaactfijneto_saldo_apro          -= $ld_saldo_apro;
							$ld_ctaactfijneto_saldo_modi          -= $ld_saldo_modi;
							$ld_ctaactfijneto_saldo_prog          -= $ld_saldo_prog;
							$ld_ctaactfijneto_saldo_ejec          -= $ld_saldo_ejec;
							$ld_ctaactfijneto_var_saldo_ejec_ant  -= $ld_var_saldo_ejec_ant;
							$ls_denominacion = "Menos ".$ls_denominacion;
						break;
					}
				}
			   
				if((trim($ls_cuenta_nva) == '1.2.4')||(trim($ls_cuenta_nva) == '2.2.5.02'))
				{
					switch($ls_cuenta_nva)
					{
						case '1.2.4' :
							$ld_ctaactintneto_saldo_real_ant      += $ld_saldo_real_ant;
							$ld_ctaactintneto_saldo_apro          += $ld_saldo_apro;
							$ld_ctaactintneto_saldo_modi          += $ld_saldo_modi;
							$ld_ctaactintneto_saldo_prog          += $ld_saldo_prog;
							$ld_ctaactintneto_saldo_ejec          += $ld_saldo_ejec;
							$ld_ctaactintneto_var_saldo_ejec_ant  += $ld_var_saldo_ejec_ant;
						break;
						
						case '2.2.5.02' :
							$ld_ctaactintneto_saldo_real_ant      -= $ld_saldo_real_ant;
							$ld_ctaactintneto_saldo_apro          -= $ld_saldo_apro;
							$ld_ctaactintneto_saldo_modi          -= $ld_saldo_modi;
							$ld_ctaactintneto_saldo_prog          -= $ld_saldo_prog;
							$ld_ctaactintneto_saldo_ejec          -= $ld_saldo_ejec;
							$ld_ctaactintneto_var_saldo_ejec_ant  -= $ld_var_saldo_ejec_ant;
							$ls_denominacion = "Menos ".$ls_denominacion;
						break;
					}
				}
			   
				$lo_hoja->write($li_row, 0,$ls_cuenta_nva,$lo_datacuenta); // Agregar el título
				$lo_hoja->write($li_row, 1,$ls_denominacion,$lo_dataleft); // Agregar el título
				$lo_hoja->write($li_row, 2,$ld_saldo_real_ant,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 3,$ld_saldo_apro,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 4,$ld_saldo_modi,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 5,$ld_saldo_ejec,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 6,$ld_saldo_prog,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 7,$ld_variacion_absoluta,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 8,$ld_variacion_porcentual,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 9,$ld_var_saldo_ejec_ant,$lo_dataright); // Agregar el título
				
				if(trim($ls_cuenta_nva) == '2.2.4.01.01')
				{
					$li_row++;
					$ld_ctacobnetas_variacion_porcentual = 0;
					$ld_ctacobnetas_variacion_absoluta = 0;
						
					if($ld_ctacobnetas_saldo_prog>0)
					{
						$ld_ctacobnetas_variacion_porcentual = ($ld_ctacobnetas_saldo_ejec/$ld_ctacobnetas_saldo_prog)*100;
					}
					$ld_ctacobnetas_variacion_absoluta = abs($ld_ctacobnetas_saldo_prog - $ld_ctacobnetas_saldo_ejec);
					
					$lo_hoja->write($li_row, 0,'',$lo_datacuenta); // Agregar el título
					$lo_hoja->write($li_row, 1,'CUENTAS A COBRAR - COMERCIALES NETAS',$lo_dataleft); // Agregar el título
					$lo_hoja->write($li_row, 2,$ld_ctacobnetas_saldo_real_ant,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 3,$ld_ctacobnetas_saldo_apro,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 4,$ld_ctacobnetas_saldo_modi,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 5,$ld_ctacobnetas_saldo_ejec,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 6,$ld_ctacobnetas_saldo_prog,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 7,$ld_ctacobnetas_variacion_absoluta,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 8,$ld_ctacobnetas_variacion_porcentual,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 9,$ld_ctacobnetas_var_saldo_ejec_ant,$lo_dataright); // Agregar el título
				}
				 
				if(trim($ls_cuenta_nva) == '2.2.5.01')
				{
					$li_row++;
					$ld_ctaactfijneto_variacion_porcentual = 0;
					$ld_ctaactfijneto_variacion_absoluta = 0;
					
					if($ld_ctaactfijneto_saldo_prog>0)
					{
						$ld_ctaactfijneto_variacion_porcentual = ($ld_ctaactfijneto_saldo_ejec/$ld_ctaactfijneto_saldo_prog)*100;
					}
					
					$ld_ctaactfijneto_variacion_absoluta = abs($ld_ctaactfijneto_saldo_prog - $ld_ctaactfijneto_saldo_ejec);
					
					$lo_hoja->write($li_row, 0,'',$lo_datacuenta); // Agregar el título
					$lo_hoja->write($li_row, 1,'ACTIVO FIJO NETO',$lo_dataleft); // Agregar el título
					$lo_hoja->write($li_row, 2,$ld_ctaactfijneto_saldo_real_ant,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 3,$ld_ctaactfijneto_saldo_apro,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 4,$ld_ctaactfijneto_saldo_modi,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 5,$ld_ctaactfijneto_saldo_ejec,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 6,$ld_ctaactfijneto_saldo_prog,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 7,$ld_ctaactfijneto_variacion_absoluta,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 8,$ld_ctaactfijneto_variacion_porcentual,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 9,$ld_ctaactfijneto_var_saldo_ejec_ant,$lo_dataright); // Agregar el título
				
				}
				 
				if(trim($ls_cuenta_nva) == '2.2.5.02')
				{
					$li_row++;
					$ld_ctaactintneto_variacion_porcentual = 0;
					$ld_ctaactintneto_variacion_absoluta = 0;
					
					if($ld_ctaactintneto_saldo_prog>0)
					{
						$ld_ctaactintneto_variacion_porcentual = ($ld_ctaactintneto_saldo_ejec/$ld_ctaactintneto_saldo_prog)*100;
					}
					
					$ld_ctaactintneto_variacion_absoluta = abs($ld_ctaactintneto_saldo_prog - $ld_ctaactintneto_saldo_ejec);
					
					$lo_hoja->write($li_row, 0,'',$lo_datacuenta); // Agregar el título
					$lo_hoja->write($li_row, 1,'ACTIVO INTANGIBLE NETO',$lo_dataleft); // Agregar el título
					$lo_hoja->write($li_row, 2,$ld_ctaactintneto_saldo_real_ant,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 3,$ld_ctaactintneto_saldo_apro,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 4,$ld_ctaactintneto_saldo_modi,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 5,$ld_ctaactintneto_saldo_ejec,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 6,$ld_ctaactintneto_saldo_prog,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 7,$ld_ctaactintneto_variacion_absoluta,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 8,$ld_ctaactintneto_variacion_porcentual,$lo_dataright); // Agregar el título
					$lo_hoja->write($li_row, 9,$ld_ctaactintneto_var_saldo_ejec_ant,$lo_dataright); // Agregar el título
				}
			
		} // for
		$li_row++;
		$ld_total_variacion_absoluta =  0;
		$ld_total_variacion_porcentual = 0;
		if($ld_total_saldo_prog>0)
		{
			$ld_total_variacion_porcentual = ($ld_total_saldo_ejec/$ld_total_saldo_prog)*100;
		}
		$lo_hoja->write($li_row, 0,'',$lo_datacuenta); // Agregar el título
		$lo_hoja->write($li_row, 1,'PASIVO + PATRIMONIO',$lo_dataleftbold); // Agregar el título
		$lo_hoja->write($li_row, 2,$ld_total_saldo_real_ant,$lo_datarightbold); // Agregar el título
		$lo_hoja->write($li_row, 3,$ld_total_saldo_apro,$lo_datarightbold); // Agregar el título
		$lo_hoja->write($li_row, 4,$ld_total_saldo_modi,$lo_datarightbold); // Agregar el título
		$lo_hoja->write($li_row, 5,$ld_total_saldo_ejec,$lo_datarightbold); // Agregar el título
		$lo_hoja->write($li_row, 6,$ld_total_saldo_prog,$lo_datarightbold); // Agregar el título
		$lo_hoja->write($li_row, 7,$ld_total_variacion_absoluta,$lo_datarightbold); // Agregar el título
		$lo_hoja->write($li_row, 8,$ld_total_variacion_porcentual,$lo_datarightbold); // Agregar el título
		$lo_hoja->write($li_row, 9,$ld_total_var_saldo_ejec_ant,$lo_datarightbold); // Agregar el título
	    $lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"balancegeneral0812.xls\"");
		header("Content-Disposition: inline; filename=\"balancegeneral0812.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}//else
	unset($io_report);
	unset($io_funciones);			
?> 