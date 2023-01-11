<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
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
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("sigesp_spi_funciones_reportes.php");
	$io_function_report=new sigesp_spi_funciones_reportes();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha = new class_fecha();
	require_once("sigesp_spi_reporte.php");
	$io_spirep = new sigesp_spi_reporte();
	require_once("../../shared/graficos/pChart/pData.class");
	require_once("../../shared/graficos/pChart/pChart.class");
//-----------------------------------------------------------------------------------------------------------------------------
	global $la_data_tot;
	require_once("sigesp_spi_class_reportes_instructivos.php");
	$io_report = new sigesp_spi_class_reportes_instructivos();

	$li_estpreing = $_SESSION["la_empresa"]["estpreing"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	$li_ano=substr($ldt_periodo,0,4);
	$ls_cmbmes=$_GET["cmbmes"];
	if ($li_estpreing==1)
	{
		$ls_codestpro1 = $_GET["codestpro1"];
		$ls_codestpro2 = $_GET["codestpro2"];
		$ls_codestpro3 = $_GET["codestpro3"];
		$ls_codestpro4 = $_GET["codestpro4"];
		$ls_codestpro5 = $_GET["codestpro5"];
		$ls_codestpro1h = $_GET["codestpro1h"];
		$ls_codestpro2h = $_GET["codestpro2h"];
		$ls_codestpro3h = $_GET["codestpro3h"];
		$ls_codestpro4h = $_GET["codestpro4h"];
		$ls_codestpro5h = $_GET["codestpro5h"];
		$ls_estclades   = $_GET["estclades"];
		$ls_estclahas   = $_GET["estclahas"];
	}
	else
	{
		$ls_codestpro1 = "";
		$ls_codestpro2 = "";
		$ls_codestpro3 = "";
		$ls_codestpro4 = "";
		$ls_codestpro5 = "";
		$ls_codestpro1h = "";
		$ls_codestpro2h = "";
		$ls_codestpro3h = "";
		$ls_codestpro4h = "";
		$ls_codestpro5h = "";
		$ls_estclades   = "";
		$ls_estclahas   = "";
	}
	switch($ls_cmbmes)
	{
		case '0103': $ls_trimestre = "01";
		break;

		case '0406': $ls_trimestre = "02";
		break;

		case '0709': $ls_trimestre = "03";
		break;

		case '1012': $ls_trimestre = "04";
		break;
	}
	if ($ls_codestpro1==='' and $ls_codestpro2==='' and $ls_codestpro3==='')
	{
		$arrResultado = $io_spirep->uf_spg_reporte_select_estpro_blanco($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,'TOP',$ls_estclades);	
		$ls_codestpro1 = $arrResultado['as_codestpro1'];
		$ls_codestpro2 = $arrResultado['as_codestpro2'];
		$ls_codestpro3 = $arrResultado['as_codestpro3'];
		$ls_codestpro4 = $arrResultado['as_codestpro4'];
		$ls_codestpro5 = $arrResultado['as_codestpro5'];
		$ls_estclades = $arrResultado['as_estcla'];
	}
	
	if ($ls_codestpro1h==='' and $ls_codestpro2h==='' and $ls_codestpro3h==='')
	{
		$arrResultado = $io_spirep->uf_spg_reporte_select_estpro_blanco($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$ls_codestpro5h,'BOTTOM',$ls_estclahas); 
		$ls_codestpro1h = $arrResultado['as_codestpro1'];
		$ls_codestpro2h = $arrResultado['as_codestpro2'];
		$ls_codestpro3h = $arrResultado['as_codestpro3'];
		$ls_codestpro4h = $arrResultado['as_codestpro4'];
		$ls_codestpro5h = $arrResultado['as_codestpro5'];
		$ls_estclahas = $arrResultado['as_estcla'];
	}
	$li_mesdes=substr($ls_cmbmes,0,2);
	$ldt_fecdes=$li_ano."-".$li_mesdes."-01";
	$li_meshas=substr($ls_cmbmes,2,2);
	$ldt_ult_dia=$io_fecha->uf_last_day($li_meshas,$li_ano);
	$fechas=$ldt_ult_dia;
	$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
	$ls_mesdes=$io_fecha->uf_load_nombre_mes($li_mesdes);
	$ls_meshas=$io_fecha->uf_load_nombre_mes($li_meshas);


//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
		$ls_titulo=" EJECUCION TRIMESTRAL DE INGRESO Y FUENTES FINANCIERAS";
//--------------------------------------------------------------------------------------------------------------------------------

	$lb_valido=$io_report->uf_spi_reportes_ejecucion_trimestral($ldt_fecdes,$ldt_fechas,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
	 															 $ls_codestpro4,$ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
																 $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas);
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
		$li_tot=$io_report->dts_reporte->getRowCount("spi_cuenta");
		$ld_total_previsto = $li_i = 0;
		$ld_total_modificado=0;
		$ld_total_programado=0;
		$ld_total_devengado=0;
		$ld_total_liquidado=0;
		$ld_total_recaudado=0;
		$ld_total_programado_acum=0;
		$ld_total_devengado_acum=0;
		$ld_total_liquidado_acum=0;
		$ld_total_recaudado_acum=0;
		$ld_total_ingresos_recibir=0;

		$ld_montotpre = 0;
		$ld_montotmod = 0;
		$ld_montotpro = 0;
		$ld_montotdev = 0;
		$ld_montotliq = 0;
		$ld_montotrec = 0;
		$ld_montotpac = 0;
		$ld_montotdac = 0;
		$ld_montotlac = 0;
		$ld_montotrac = 0;
		$ld_montotire = 0;

		if ($ls_codestpro1=="")
		{
		     $ls_denestpro1 = " TODAS";
			$arrResultado = $io_spirep->uf_spg_reporte_select_denestpro_global(str_pad($ls_codestpro1,25,0,0),str_pad($ls_codestpro2,25,0,0),str_pad($ls_codestpro3,25,0,0),str_pad($ls_codestpro4,25,0,0),str_pad($ls_codestpro5,25,0,0),
									   $ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5,$ls_estclades);
			$ls_denestpro1 = $arrResultado['as_denestpro1'];
			$ls_denestpro2 = $arrResultado['as_denestpro2'];
			$ls_denestpro3 = $arrResultado['as_denestpro3'];
			$ls_denestpro4 = $arrResultado['as_denestpro4'];
			$ls_denestpro5 = $arrResultado['as_denestpro5'];

			$arrResultado = $io_spirep->uf_spg_reporte_select_denestpro_global(str_pad($ls_codestpro1h,25,0,0),str_pad($ls_codestpro2h,25,0,0),str_pad($ls_codestpro3h,25,0,0),str_pad($ls_codestpro4h,25,0,0),str_pad($ls_codestpro5h,25,0,0),
									   $ls_denestpro1h,$ls_denestpro2h,$ls_denestpro3h,$ls_denestpro4h,$ls_denestpro5h,$ls_estclahas);
			$ls_denestpro1h = $arrResultado['as_denestpro1'];
			$ls_denestpro2h = $arrResultado['as_denestpro2'];
			$ls_denestpro3h = $arrResultado['as_denestpro3'];
			$ls_denestpro4h = $arrResultado['as_denestpro4'];
			$ls_denestpro5h = $arrResultado['as_denestpro5'];
			
			$la_data_cab_ep[1]=array('ep_desde'=>$ls_codestpro1.' - '.$ls_denestpro1,'ep_hasta'=>$ls_codestpro1h.' - '.$ls_denestpro1h);
			$la_data_cab_ep[2]=array('ep_desde'=>$ls_codestpro2.' - '.$ls_denestpro2,'ep_hasta'=>$ls_codestpro2h.' - '.$ls_denestpro2h);
			$la_data_cab_ep[3]=array('ep_desde'=>$ls_codestpro3.' - '.$ls_denestpro3,'ep_hasta'=>$ls_codestpro3h.' - '.$ls_denestpro3h);		     
		}
		else
		{
			$arrResultado = $io_spirep->uf_spg_reporte_select_denestpro1(str_pad($ls_codestpro1,25,0,0),$ls_denestpro1,$ls_estclades);
			$ls_denestpro1 = $arrResultado['as_denestpro'];
			$arrResultado = $io_spirep->uf_spg_reporte_select_denestpro_global(str_pad($ls_codestpro1,25,0,0),str_pad($ls_codestpro2,25,0,0),str_pad($ls_codestpro3,25,0,0),str_pad($ls_codestpro4,25,0,0),str_pad($ls_codestpro5,25,0,0),
									   $ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5,$ls_estclades);
			$ls_denestpro1 = $arrResultado['as_denestpro1'];
			$ls_denestpro2 = $arrResultado['as_denestpro2'];
			$ls_denestpro3 = $arrResultado['as_denestpro3'];
			$ls_denestpro4 = $arrResultado['as_denestpro4'];
			$ls_denestpro5 = $arrResultado['as_denestpro5'];

			$arrResultado = $io_spirep->uf_spg_reporte_select_denestpro_global(str_pad($ls_codestpro1h,25,0,0),str_pad($ls_codestpro2h,25,0,0),str_pad($ls_codestpro3h,25,0,0),str_pad($ls_codestpro4h,25,0,0),str_pad($ls_codestpro5h,25,0,0),
									   $ls_denestpro1h,$ls_denestpro2h,$ls_denestpro3h,$ls_denestpro4h,$ls_denestpro5h,$ls_estclahas);
			$ls_denestpro1h = $arrResultado['as_denestpro1'];
			$ls_denestpro2h = $arrResultado['as_denestpro2'];
			$ls_denestpro3h = $arrResultado['as_denestpro3'];
			$ls_denestpro4h = $arrResultado['as_denestpro4'];
			$ls_denestpro5h = $arrResultado['as_denestpro5'];
			
			$la_data_cab_ep[1]=array('ep_desde'=>$ls_codestpro1.' - '.$ls_denestpro1,'ep_hasta'=>$ls_codestpro1h.' - '.$ls_denestpro1h);
			$la_data_cab_ep[2]=array('ep_desde'=>$ls_codestpro2.' - '.$ls_denestpro2,'ep_hasta'=>$ls_codestpro2h.' - '.$ls_denestpro2h);
			$la_data_cab_ep[3]=array('ep_desde'=>$ls_codestpro3.' - '.$ls_denestpro3,'ep_hasta'=>$ls_codestpro3h.' - '.$ls_denestpro3h);
			 
		}
		$ls_partida_aux="";
		for ($z=1;$z<=$li_tot;$z++)
		    {
			  $ld_previsto=0;
			  $ld_modificado=0;
			  $ld_programado=0;
			  $ld_devengado=0;
			  $ld_liquidado=0;
			  $ld_recaudado=0;
			  $ld_programado_acum=0;
			  $ld_devengado_acum=0;
			  $ld_liquidado_acum=0;
			  $ld_recaudado_acum=0;
			  $ld_ingresos_recibir=0;
			  $ls_ramo="";
			  $ls_subramo="";
			  $ls_especifica="";
			  $ls_subesp="";
			  $ls_status="";

			  $ls_spi_cuenta       = trim($io_report->dts_reporte->data["spi_cuenta"][$z]);
			  $arrResultado = $io_function_report->uf_get_spi_cuenta($ls_spi_cuenta,$ls_ramo,$ls_subramo,$ls_especifica,$ls_subesp);
			  $ls_ramo = $arrResultado['as_spi_ramo'];
			  $ls_subramo = $arrResultado['as_spi_subramo'];
			  $ls_especifica = $arrResultado['as_spi_especifica'];
			  $ls_subesp = $arrResultado['as_spi_subesp'];
			  $ls_denominacion     = trim($io_report->dts_reporte->data["denominacion"][$z]);
			  $ld_previsto         = $io_report->dts_reporte->data["previsto"][$z];
			  $ld_modificado       = $io_report->dts_reporte->data["modificado"][$z];
			  $ld_programado       = $io_report->dts_reporte->data["programado"][$z].'<br>';
			  $ld_devengado        = $io_report->dts_reporte->data["devengado"][$z];
			  $ld_liquidado        = $io_report->dts_reporte->data["liquidado"][$z];
			  $ld_recaudado        = $io_report->dts_reporte->data["recaudado"][$z];
			  $ld_programado_acum  = $io_report->dts_reporte->data["programado_acum"][$z];
			  $ld_devengado_acum   = $io_report->dts_reporte->data["devengado_acum"][$z];
			  $ld_liquidado_acum   = $io_report->dts_reporte->data["liquidado_acum"][$z];
			  $ld_recaudado_acum   = $io_report->dts_reporte->data["recaudado_acum"][$z];
			  $ld_ingresos_recibir = $io_report->dts_reporte->data["ingresos_recibir"][$z];
			  $ls_status           = $io_report->dts_reporte->data["status"][$z];
			  if ($ls_status=="C")
			     {
				   $ld_montotpre += $ld_previsto;
				   $ld_montotmod += $ld_modificado;
				   $ld_montotpro += $ld_programado;
				   $ld_montotdev += $ld_devengado;
				   $ld_montotliq += $ld_liquidado;
				   $ld_montotrec += $ld_recaudado;
				   $ld_montotpac += $ld_programado_acum;
				   $ld_montotdac += $ld_devengado_acum;
				   $ld_montotlac += $ld_liquidado_acum;
				   $ld_montotrac += $ld_recaudado_acum;
				   $ld_montotire += $ld_ingresos_recibir;
				 }
			  if ($ls_partida_aux=="")
				 {
				   $ls_partida_aux=$ls_ramo;
				 }
			  elseif($ls_partida_aux==$ls_ramo)
				 {
				   if ($ls_status=="C")
				      {
					    $ld_total_previsto         += $ld_previsto;
					    $ld_total_modificado       += $ld_modificado;
					    $ld_total_programado       += $ld_programado;
					    $ld_total_devengado        += $ld_devengado;
					    $ld_total_liquidado        += $ld_liquidado;
					    $ld_total_recaudado        += $ld_recaudado;
					    $ld_total_programado_acum  += $ld_programado_acum;
					    $ld_total_devengado_acum   += $ld_devengado_acum;
					    $ld_total_liquidado_acum   += $ld_liquidado_acum;
					    $ld_total_recaudado_acum   += $ld_recaudado_acum;
					    $ld_total_ingresos_recibir += $ld_ingresos_recibir;
				      }
				 }
			  else
			     {
				   $la_data_tot[1]=array('totales'=>"TOTALES ".$ls_partida_aux,
								         'previsto'=>number_format($ld_total_previsto,2,",","."),
								         'modificado'=>number_format($ld_total_modificado,2,",","."),
										 'programado'=>number_format($ld_total_programado,2,",","."),
										 'devengado'=>number_format($ld_total_devengado,2,",","."),
										 'liquidado'=>number_format($ld_total_liquidado,2,",","."),
										 'recaudado'=>number_format($ld_total_recaudado,2,",","."),
										 'programado_acum'=>number_format($ld_total_programado_acum,2,",","."),
										 'devengado_acum'=>number_format($ld_total_devengado_acum,2,",","."),
										 'liquidado_acum'=>number_format($ld_total_liquidado_acum,2,",","."),
										 'recaudado_acum'=>number_format($ld_total_recaudado_acum,2,",","."),
										 'ingresos_recibir'=>number_format($ld_total_ingresos_recibir,2,",","."));

				   unset($la_data,$la_data_tot);
				   $li_i = 0;
                   $ld_total_previsto=$ld_total_modificado=$ld_total_programado=$ld_total_devengado=0;
				   $ld_total_liquidado=$ld_total_recaudado=$ld_total_programado_acum=0;
				   $ld_total_devengado_acum=$ld_total_liquidado_acum=$ld_total_recaudado_acum=$ld_total_ingresos_recibir=0;				   $ls_partida_aux		= $ls_ramo;
				 }
			  $ld_previsto         = number_format($ld_previsto,2,",",".");
			  $ld_modificado       = number_format($ld_modificado,2,",",".");
			  $ld_programado       = number_format($ld_programado,2,",",".");
			  $ld_devengado        = number_format($ld_devengado,2,",",".");
			  $ld_liquidado        = number_format($ld_liquidado,2,",",".");
			  $ld_recaudado        = number_format($ld_recaudado,2,",",".");
			  $ld_programado_acum  = number_format($ld_programado_acum,2,",",".");
			  $ld_devengado_acum   = number_format($ld_devengado_acum,2,",",".");
			  $ld_liquidado_acum   = number_format($ld_liquidado_acum,2,",",".");
			  $ld_recaudado_acum   = number_format($ld_recaudado_acum,2,",",".");
			  $ld_ingresos_recibir = number_format($ld_ingresos_recibir,2,",",".");

			  $li_i++;
			  $la_data[$li_i]=array('ramo'=>$ls_ramo,
			                        'subramo'=>$ls_subramo,
									'especifica'=>$ls_especifica,
				                    'subesp'=>$ls_subesp,
									'denominacion'=>$ls_denominacion,
									'previsto'=>$ld_previsto,
									'modificado'=>$ld_modificado,
									'programado'=>$ld_programado,
									'devengado'=>$ld_devengado,
									'liquidado'=>$ld_liquidado,
									'recaudado'=>$ld_recaudado,
									'programado_acum'=>$ld_programado_acum,
									'devengado_acum'=>$ld_devengado_acum,
									'liquidado_acum'=>$ld_liquidado_acum,
									'recaudado_acum'=>$ld_recaudado_acum,
									'ingresos_recibir'=>$ld_ingresos_recibir);

			  if ($z==$li_tot)
			     {
				   if (isset($la_data_tot))
				      {
					    unset($la_data_tot);
					  }
				   $la_data_tot[1]=array('totales'=>"TOTALES ".$ls_partida_aux,
								         'previsto'=>number_format($ld_total_previsto,2,",","."),
								         'modificado'=>number_format($ld_total_modificado,2,",","."),
										 'programado'=>number_format($ld_total_programado,2,",","."),
										 'devengado'=>number_format($ld_total_devengado,2,",","."),
										 'liquidado'=>number_format($ld_total_liquidado,2,",","."),
										 'recaudado'=>number_format($ld_total_recaudado,2,",","."),
										 'programado_acum'=>number_format($ld_total_programado_acum,2,",","."),
										 'devengado_acum'=>number_format($ld_total_devengado_acum,2,",","."),
										 'liquidado_acum'=>number_format($ld_total_liquidado_acum,2,",","."),
										 'recaudado_acum'=>number_format($ld_total_recaudado_acum,2,",","."),
										 'ingresos_recibir'=>number_format($ld_total_ingresos_recibir,2,",","."));
				   //Impresión del Total General.
				   unset($la_data_tot);
				   $la_data_tot[1]=array('totales'=>"TOTAL GENERAL ",
										 'previsto'=>number_format($ld_montotpre,2,",","."),
										 'modificado'=>number_format($ld_montotmod,2,",","."),
										 'programado'=>number_format($ld_montotpro,2,",","."),
										 'devengado'=>number_format($ld_montotdev,2,",","."),
										 'liquidado'=>number_format($ld_montotliq,2,",","."),
										 'recaudado'=>number_format($ld_montotrec,2,",","."),
										 'programado_acum'=>number_format($ld_montotpac,2,",","."),
										 'devengado_acum'=>number_format($ld_montotdac,2,",","."),
										 'liquidado_acum'=>number_format($ld_montotlac,2,",","."),
										 'recaudado_acum'=>number_format($ld_montotrac,2,",","."),
										 'ingresos_recibir'=>number_format($ld_montotire,2,",","."));
				   unset($la_data);
					 $DataSet = new pData;
					 $DataSet->AddPoint(array($ld_montotpro),"Serie0");
					 $DataSet->AddPoint(array($ld_montotdev),"Serie1");
					 $DataSet->AddPoint(array($ld_montotrec),"Serie2");
					 $DataSet->AddPoint(array($ld_montotire),"Serie3");
					 $DataSet->AddPoint(array(""),"titulos");
					 $DataSet->AddSerie("Serie0");
					 $DataSet->AddSerie("Serie1");
					 $DataSet->AddSerie("Serie2");
					 $DataSet->AddSerie("Serie3");
					 $DataSet->SetSerieName("Programado","Serie0");
					 $DataSet->SetSerieName("Devengado","Serie1");
					 $DataSet->SetSerieName("Recaudado","Serie2");
					 $DataSet->SetSerieName("Por Cobrar","Serie3");
					 $DataSet->SetAbsciseLabelSerie("titulos");
					
					 // Initialise the graph
					 $Test = new pChart(700,230);
					 $Test->setFontProperties("../../shared/graficos/Fonts/tahoma.ttf",8);
					 $Test->setGraphArea(90,30,580,200);
					 $Test->drawFilledRoundedRectangle(7,7,593,223,5,240,240,240);
					 $Test->drawRoundedRectangle(5,5,595,225,5,230,230,230);
					 $Test->drawGraphArea(255,255,255,TRUE);
					 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE);
					 $Test->drawGrid(4,TRUE,230,230,230,50);
					
					 // Draw the 0 line
					 $Test->setFontProperties("../../shared/graficos/Fonts/tahoma.ttf",6);
					 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);
					
					 // Draw the bar graph
					 $Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE,80);
					
					
					 // Finish the graph
					 $Test->setFontProperties("../../shared/graficos/Fonts/tahoma.ttf",8);
					 $Test->drawLegend(596,50,$DataSet->GetDataDescription(),255,255,255);
					 $Test->setFontProperties("../../shared/graficos/Fonts/tahoma.ttf",10);
					 $Test->drawTitle(50,22,$ls_titulo,50,50,50,585);
					
					 $Test->Render("ejecuciontrimestral.png");
				 }
			}//for
			unset($la_data,$la_data_tot);
	}//else
	unset($io_report,$io_funciones);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EJECUCION TRIMESTRAL DE INGRESO Y FUENTES FINANCIERAS</title>
<link href="../../shared/css/cabecera.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/general.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/report.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/tablas.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="498" border="0" align="center">
  <tr>
    <td width="320" class="sin-borde2"><div align="center" class="titulo-celdanew"> EJECUCION TRIMESTRAL DE INGRESO Y FUENTES FINANCIERAS </div></td>
  </tr>
  <tr>
    <td width="320"><img src="ejecuciontrimestral.png" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right">
<a href="javascript:ue_print();"> <img src="../../shared/imagebank/tools20/print.gif" width="35" height="30" border="0"/></a></div>
	</td>
  </tr>
  <tr>
    <td width="320"></td>
  </tr>
</table>


</body>
<script >
function ue_print()
{
	window.print();
}
</script>
</html>

