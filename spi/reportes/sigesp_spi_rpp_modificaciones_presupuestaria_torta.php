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
	//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../shared/graficos/pChart/pData.class");
		require_once("../../shared/graficos/pChart/pChart.class");
		require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("sigesp_spi_reporte.php");
		$io_report = new sigesp_spi_reporte();
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();			
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once("sigesp_spi_funciones_reportes.php");
		$io_function_report = new sigesp_spi_funciones_reportes();
		require_once("../class_funciones_ingreso.php");
		$io_fun_ingreso=new class_funciones_ingreso();			
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
        $ls_ckbaum=$_GET["ckbaum"];
        $ls_ckbdis=$_GET["ckbdis"];
		$ls_comprobante  = $_GET["txtcomprobante"];
		$ls_procede  = $_GET["txtprocede"];
		$ldt_fecha  = $_GET["txtfecha"];

		$fecdes=$_GET["txtfecdes"];
		$ldt_fecdes=$io_funciones->uf_convertirdatetobd($fecdes);

		$fechas=$_GET["txtfechas"];
		$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
		$ldt_fecha=$io_funciones->uf_convertirdatetobd($ldt_fecha);
		
		/////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_desc_event="Solicitud de Reporte Modificaciones Presupuestarias Aprobadas desde la fecha ".$fecdes." hasta ".$fechas.", Comprobante  ".$ls_comprobante." ,Procede  ".$ls_procede." ,Fecha del Comprobante  ".$ldt_fecha;
		$io_fun_ingreso->uf_load_seguridad_reporte("SPI","sigesp_spi_r_modificaciones_presupuestarias_aprobadas.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//----------------------------------------------------  Parámetros del encabezado  ----------------------------------------------------------------------------------------------------------------------------------------------
		$ls_titulo=" <b>MODIFICACIONES PRESUPUESTARIAS APROBADAS</b> ";
		$ls_titulo1="<b> DESDE LA FECHA  ".$fecdes."   HASTA  ".$fechas." </b>";
		$ls_tiporeporte=$_GET["tiporeporte"];
		global $ls_tiporeporte;
		require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
		
		if($ls_tiporeporte==1)
		{
			require_once("sigesp_spi_reportebsf.php");
			$io_report=new sigesp_spi_reportebsf();
		}      
		$li_totnoapr=0;
		$li_totapr=0;
		
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
     $lb_valido=$io_report->uf_spi_reporte_modificaciones_presupuestarias_aprobadas($ls_ckbaum,$ls_ckbdis,$ldt_fecdes,$ldt_fechas,
	                                                                                $ls_comprobante,$ls_procede,$ldt_fecha);
 
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
		$io_report->dts_reporte->group("comprobante");
		$li_totapr=$io_report->dts_reporte->getRowCount("spi_cuenta");
		$lb_valido=$io_report->uf_spi_reporte_modificaciones_presupuestarias_no_aprobadas($ls_ckbaum,$ls_ckbdis,$ldt_fecdes,$ldt_fechas,
																						   $ls_comprobante,$ls_procede,$ldt_fecha);
		 if($lb_valido==false) // Existe algún error ó no hay registros
		 {
		 }
		 else // Imprimimos el reporte
		 {
			$li_totnoapr=$io_report->dts_reporte->getRowCount("spi_cuenta");
		 }
	  }
		$DataSet = new pData;
		$DataSet->AddPoint(array($li_totapr,$li_totnoapr),"Serie1");
		$DataSet->AddPoint(array("Aprobadas","No Aprobadas"),"titulos");
		$DataSet->AddAllSeries();
		$DataSet->SetAbsciseLabelSerie("titulos");
		
		
		// Initialise the graph
		$Test = new pChart(500,400);
		$Test->drawFilledRoundedRectangle(7,7,493,393,5,240,240,240);
		$Test->drawRoundedRectangle(5,5,495,395,5,230,230,230);
		
		// This will draw a shadow under the pie chart
		$Test->drawFilledCircle(225,205,170,200,200,200);
		
		// Draw the pie chart
		$Test->setFontProperties("../../shared/graficos/Fonts/tahoma.ttf",8);
		$Test->AntialiasQuality = 0;
		$Test->drawBasicPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),220,200,170,PIE_PERCENTAGE,255,255,218);
		$Test->drawPieLegend(380,15,$DataSet->GetData(),$DataSet->GetDataDescription(),150,150,150);
		
		$Test->Render("modificaciones.png");
		unset($io_report);
		unset($io_funciones);
		unset($io_function_report);
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Modificaciones Presupuestarias</title>
<link href="../../shared/css/cabecera.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/general.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/report.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/tablas.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="498" border="0" align="center">
  <tr>
    <td width="320" class="sin-borde2"><div align="center" class="titulo-celdanew"> Modificaciones Presupuestarias </div></td>
  </tr>
  <tr>
    <td width="320"><img src="modificaciones.png" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right">
	<a href="javascript:ue_print();"> <img src="../../shared/imagebank/tools20/print.gif" width="35" height="30" border="0"/> </a></div>
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


