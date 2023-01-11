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
	
	  // para crear el libro excel
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo =  tempnam("/tmp", "Listado_de_Cuentas_Presupuestarias.xls");
		$lo_libro = new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//--------------------------------------------------------------------------------------------------------------------------


	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("sigesp_spg_funciones_reportes.php");
	$io_function_report = new sigesp_spg_funciones_reportes();
	require_once("sigesp_spg_reporte.php");
	$io_spg_report=new sigesp_spg_reporte();

	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	$ls_codestpro1_desde=$_GET["codestpro1"];
	$ls_codestpro2_desde=$_GET["codestpro2"];
	$ls_codestpro3_desde=$_GET["codestpro3"];
	$ls_codestpro1_hasta=$_GET["codestpro1h"];
	$ls_codestpro2_hasta=$_GET["codestpro2h"];
	$ls_codestpro3_hasta=$_GET["codestpro3h"];
	$ls_cuenta_desde=$_GET["txtcuentades"];
	$ls_cuenta_hasta=$_GET["txtcuentahas"];
	$ls_estclades = $_GET["estclades"];
	$ls_estclahas = $_GET["estclahas"];
	$ls_ctascg_desde=$_GET["cuentascg_desde"];
	$ls_ctascg_hasta=$_GET["cuentascg_hasta"];
    $ls_codfuefindes=$_GET["txtcodfuefindes"];
    $ls_codfuefinhas=$_GET["txtcodfuefinhas"];
        
    if (($ls_codfuefindes=='')&&($ls_codfuefindes==''))
    {
			$arrResultado=$io_function_report->uf_spg_select_fuentefinanciamiento($ls_minfuefin,$ls_maxfuefin);
			$ls_minfuefin=$arrResultado['as_minfuefin'];
			$ls_maxfuefin=$arrResultado['as_maxfuefin'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				 $ls_codfuefindes=$ls_minfuefin;
				 $ls_codfuefinhas=$ls_maxfuefin;
			  }
    }
   /////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 if(!empty($$ls_codestpro1_desde))
	 {
	  $ls_codestpro1_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro1_desde,25);
	 }
	 if(!empty($ls_codestpro2_desde))
	 {
	  $ls_codestpro2_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro2_desde,25);
	 }
	 if(!empty($ls_codestpro3_desde))
	 {
	  $ls_codestpro3_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro3_desde,25);
	 }
	 
	 if(!empty($$ls_codestpro1_hasta))
	 {
	  $ls_codestpro1_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro1_hasta,25);
	 }
	 if(!empty($ls_codestpro2_hasta))
	 {
	  $ls_codestpro2_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro2_hasta,25);
	 }
	 if(!empty($ls_codestpro3_hasta))
	 {
	  $ls_codestpro3_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro3_hasta,25);
	 }
	 if($li_estmodest==2)
	 {
		 if(!empty($_GET["codestpro4"]))
		 {
		  $ls_codestpro4_desde  = $io_funciones->uf_cerosizquierda($_GET["codestpro4"],25);
		 }
		 if(!empty($_GET["codestpro5"]))
		 {
		  $ls_codestpro5_desde  = $io_funciones->uf_cerosizquierda($_GET["codestpro5"],25);
		 }
		 
		 if(!empty($_GET["codestpro4h"]))
		 {
		  $ls_codestpro4_hasta  = $io_funciones->uf_cerosizquierda($_GET["codestpro4h"],25);
		 }
		 if(!empty($_GET["codestpro5h"]))
		 {
		  $ls_codestpro5_hasta  = $io_funciones->uf_cerosizquierda($_GET["codestpro5h"],25);
		 }
	 }
	 else
	 {
		 $ls_codestpro4_desde=$io_funciones->uf_cerosizquierda(0,25);
		 $ls_codestpro5_desde=$io_funciones->uf_cerosizquierda(0,25);
		 $ls_codestpro4_hasta=$io_funciones->uf_cerosizquierda(0,25);
		 $ls_codestpro5_hasta=$io_funciones->uf_cerosizquierda(0,25);
	 }
	 
	 if(!empty($ls_codestpro1_desde))
	 {
		$ls_codestpro1_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro1_desde,25);
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro1($ls_codestpro1_desde,$ls_estclades);
		$ls_codestpro1_desde=$arrResultado['as_codestpro1'];
		$ls_estclades=$arrResultado['as_estclades'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if(!empty($ls_codestpro2_desde))
	 {
		$ls_codestpro2_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro2_desde,25);	
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro2($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_estclades);
		$ls_codestpro2_desde=$arrResultado['as_codestpro2'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if(!empty($ls_codestpro3_desde))
	 {
		$ls_codestpro3_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro3_desde,25);
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro3($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_estclades);
		$ls_codestpro3_desde=$arrResultado['as_codestpro3'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 
	 if(!empty($ls_codestpro1_hasta))
	 {
		$ls_codestpro1_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro1_hasta,25);
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_max_codestpro1($ls_codestpro1_hasta,$ls_estclahas);
		$ls_codestpro1_hasta=$arrResultado['as_codestpro1'];
		$ls_estclahas=$arrResultado['as_estclahas'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if(!empty($ls_codestpro2_hasta))
	 {
		$ls_codestpro2_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro2_hasta,25);	
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_max_codestpro2($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_estclahas);
		$ls_codestpro2_hasta=$arrResultado['as_codestpro2'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if(!empty($ls_codestpro3_hasta))
	 {
		$ls_codestpro3_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro3_hasta,25);
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_max_codestpro3($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_estclahas);
		$ls_codestpro3_hasta=$arrResultado['as_codestpro3'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if($li_estmodest==2)
	 {
	    if(!empty($ls_codestpro4_desde))
		{	
		 $ls_codestpro4_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro4_desde,25);	
		}
		else
		{
			$arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro4($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_codestpro4_desde,$ls_estclades);
			$ls_codestpro4_desde=$arrResultado['as_codestpro4'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		if(!empty($ls_codestpro5_desde))
		{	
		  $ls_codestpro5_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro5_desde,25);
		}
		else
		{
			$arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro5($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_codestpro4_desde,$ls_codestpro5_desde,$ls_estclades);
			$ls_codestpro5_desde=$arrResultado['as_codestpro5'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		
		if(!empty($ls_codestpro4_hasta))
		{	
		 $ls_codestpro4_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro4_hasta,25);	
		}
		else
		{
			$arrResultado=$io_function_report->uf_spg_reporte_select_max_codestpro4($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_codestpro4_hasta,$ls_estclahas);
			$ls_codestpro4_hasta=$arrResultado['as_codestpro4'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		if(!empty($ls_codestpro5_hasta))
		{	
		  $ls_codestpro5_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro5_hasta,25);
		}
		else
		{
			$arrResultado=$io_function_report->uf_spg_reporte_select_max_codestpro5($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_codestpro4_hasta,$ls_codestpro5_hasta,$ls_estclahas);
			$ls_codestpro5_hasta=$arrResultado['as_codestpro5'];
			$lb_valido=$arrResultado['lb_valido'];
		}
	 
	 }
	$rs_estructuras=NULL;
	$arrResultado = $io_spg_report->uf_reporte_listado_cuenta_estructura($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_codestpro4_desde,$ls_codestpro5_desde,
                                                         $ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_codestpro4_hasta,$ls_codestpro5_hasta,
											             $ls_estclades,$ls_estclahas,$ls_codfuefindes,$ls_codfuefinhas,$ls_cuenta_desde,$ls_cuenta_hasta,
											             $ls_ctascg_desde,$ls_ctascg_hasta,$rs_estructuras);
	$rs_estructuras=$arrResultado['rs_estructuras'];
	$lb_valido =$arrResultado['lb_valido'];
	if($lb_valido)
	{
		$li_totrow=$rs_estructuras->RecordCount();
		if($li_totrow<=0)
		{
			?>
			<script language=javascript>
				 alert('No hay datos a reportar!!!');
				 close();
			</script>
			<?php
		}
		else
		{
			$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
			$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
			$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
			$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
			$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
			if($li_estmodest==1)
			{
			 $ls_estructura_desde = substr($ls_codestpro1_desde,-$ls_loncodestpro1)."-".substr($ls_codestpro2_desde,-$ls_loncodestpro2)."-".substr($ls_codestpro3_desde,-$ls_loncodestpro3)."-".$ls_estclades;
			 $ls_estructura_hasta = substr($ls_codestpro1_hasta,-$ls_loncodestpro1)."-".substr($ls_codestpro2_hasta,-$ls_loncodestpro2)."-".substr($ls_codestpro3_hasta,-$ls_loncodestpro3)."-".$ls_estclahas;
			}
			else
			{
			 $ls_estructura_desde = substr($ls_codestpro1_desde,-$ls_loncodestpro1)."-".substr($ls_codestpro2_desde,-$ls_loncodestpro2)."-".substr($ls_codestpro3_desde,-$ls_loncodestpro3)."-".substr($ls_codestpro4_desde,-$ls_loncodestpro4)."-".substr($ls_codestpro5_desde,-$ls_loncodestpro5)."-".$ls_estclades;
			 $ls_estructura_hasta = substr($ls_codestpro1_hasta,-$ls_loncodestpro1)."-".substr($ls_codestpro2_hasta,-$ls_loncodestpro2)."-".substr($ls_codestpro3_hasta,-$ls_loncodestpro3)."-".substr($ls_codestpro4_hasta,-$ls_loncodestpro4)."-".substr($ls_codestpro5_hasta,-$ls_loncodestpro5)."-".$ls_estclahas;
			}
			
			$ls_desc_event="Solicitud de Reporte Listado de Cuentas Presupuestarias en Excel desde la Estructura  ".$ls_estructura_desde." hasta ".$ls_estructura_hasta;
	        $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_spg_r_cuentas.php",$ls_desc_event);
			
			$ls_nomestpro1 = $_SESSION["la_empresa"]["nomestpro1"];
			$ls_nomestpro2 = $_SESSION["la_empresa"]["nomestpro2"];
			$ls_nomestpro3 = $_SESSION["la_empresa"]["nomestpro3"];
			$ls_nomestpro4 = $_SESSION["la_empresa"]["nomestpro4"];
			$ls_nomestpro5 = $_SESSION["la_empresa"]["nomestpro5"];
	
			$ls_encabezado="Listado de Cuentas Presupuestarias(Vinculación Cuenta Articulos)"; // Imprimimos el encabezado de la página
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
			
			$lo_datacenter_bold= &$lo_libro->addformat();
			$lo_datacenter_bold->set_font("Verdana");
			$lo_datacenter_bold->set_align('center');
			$lo_datacenter_bold->set_size('9');
			$lo_datacenter_bold->set_bold();

			$lo_dataleft= &$lo_libro->addformat();
			$lo_dataleft->set_text_wrap();
			$lo_dataleft->set_font("Verdana");
			$lo_dataleft->set_align('left');
			$lo_dataleft->set_size('9');
			
			$lo_dataleft_bold= &$lo_libro->addformat();
			$lo_dataleft_bold->set_text_wrap();
			$lo_dataleft_bold->set_font("Verdana");
			$lo_dataleft_bold->set_align('left');
			$lo_dataleft_bold->set_size('9');
			$lo_dataleft_bold->set_bold();
			
			$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
			$lo_dataright->set_font("Verdana");
			$lo_dataright->set_align('right');
			$lo_dataright->set_size('9');
			$lo_hoja->set_column(0,0,15);
			$lo_hoja->set_column(1,1,20);
			$lo_hoja->set_column(2,2,30);
			$lo_hoja->set_column(3,3,20);
			$lo_hoja->set_column(4,4,13);
			$lo_hoja->set_column(5,7,30);
			$lo_hoja->write(0, 3,$ls_encabezado,$lo_titulo);
			$contlineas=0;
			while(!$rs_estructuras->EOF)
			{
				
				$ls_codestpro1=$rs_estructuras->fields["codestpro1"];
				$ls_codestpro2=$rs_estructuras->fields["codestpro2"];
				$ls_codestpro3=$rs_estructuras->fields["codestpro3"];
				$ls_estcla = $rs_estructuras->fields["estcla"];
				$ls_codestpro4="";
				$ls_codestpro5="";
				$ls_denestpro1   = "";
				$ls_denestpro2   = "";
				$ls_denestpro3   = "";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);
				$ls_denestpro1=$arrResultado['as_denestpro1'];
				$lb_valido=$arrResultado['lb_valido'];
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
			    $ls_denestpro2=$arrResultado['as_denestpro2'];
			    $lb_valido=$arrResultado['lb_valido'];
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
			     $ls_denestpro3=$arrResultado['as_denestpro3'];
			     $lb_valido=$arrResultado['lb_valido'];
				if($li_estmodest==1)
				{
					if ($ls_estcla=="P")
					{
						$ls_tipoE="PROYECTO";
					}
					else
					{
						$ls_tipoE="ACCIÓN";
					}
					$ls_codestpro4="0000000000000000000000000";
					$ls_codestpro5="0000000000000000000000000";
					$ls_tit1="ESTRUCTURA PRESUPUESTARIA  (TIPO: ".$ls_tipoE.")";
					$ls_tit2a= substr($ls_codestpro1,-$ls_loncodestpro1);
					$ls_tit2b= $ls_denestpro1;
					$ls_tit3a= substr($ls_codestpro2,-$ls_loncodestpro2);
					$ls_tit3b= $ls_denestpro2;
					$ls_tit4a= substr($ls_codestpro3,-$ls_loncodestpro3);
					$ls_tit4b= $ls_denestpro3;
					$contlineas++;
					$lo_hoja->write($contlineas, 0,$ls_tit1,$lo_titulo);
					$contlineas++;
					$lo_hoja->write($contlineas, 0,$ls_nomestpro1.":", $lo_titulo);
					$lo_hoja->write($contlineas, 1," ".$ls_tit2a, $lo_datacenter);
					$lo_hoja->write($contlineas, 2, $ls_tit2b,$lo_dataleft);
					$contlineas++;
					$lo_hoja->write($contlineas, 0,$ls_nomestpro2.":", $lo_titulo);
					$lo_hoja->write($contlineas, 1," ".$ls_tit3a, $lo_datacenter);
					$lo_hoja->write($contlineas, 2, $ls_tit3b,$lo_dataleft);
					$contlineas++;
					$lo_hoja->write($contlineas, 0,$ls_nomestpro3.":", $lo_titulo);
					$lo_hoja->write($contlineas, 1," ".$ls_tit4a, $lo_datacenter);
					$lo_hoja->write($contlineas, 2, $ls_tit4b,$lo_dataleft);
					$contlineas++;
				}
				elseif($li_estmodest==2)
				{
					$ls_codestpro4=$rs_estructuras->fields["codestpro4"];
					$ls_codestpro5=$rs_estructuras->fields["codestpro5"];
					$ls_denestpro4   = "";
					$ls_denestpro5   = "";
					$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estcla);
					$ls_denestpro4=$arrResultado['as_denestpro4'];
					$lb_valido=$arrResultado['lb_valido'];
					$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
					$ls_denestpro5=$arrResultado['as_denestpro5'];
					$lb_valido=$arrResultado['lb_valido'];
					$ls_tit1="PROGRAMATICA";
					$ls_tit2a= substr($ls_codestpro1,-$ls_loncodestpro1);
					$ls_tit2b= $ls_denestpro1;
					$ls_tit3a= substr($ls_codestpro2,-$ls_loncodestpro2);
					$ls_tit3b= $ls_denestpro2;
					$ls_tit4a= substr($ls_codestpro3,-$ls_loncodestpro3);
					$ls_tit4b= $ls_denestpro3;
					$ls_tit5a= substr($ls_codestpro4,-$ls_loncodestpro4);
					$ls_tit5b= $ls_denestpro4;
					$ls_tit6a= substr($ls_codestpro5,-$ls_loncodestpro5);
					$ls_tit6b= $ls_denestpro5;
					$contlineas++;
					$lo_hoja->write($contlineas, 0,$ls_tit1,$lo_titulo);
					$contlineas++;
					$lo_hoja->write($contlineas, 0,$ls_nomestpro1.":", $lo_titulo);
					$lo_hoja->write($contlineas, 1," ".$ls_tit2a, $lo_datacenter);
					$lo_hoja->write($contlineas, 2, $ls_tit2b,$lo_dataleft);
					$contlineas++;
					$lo_hoja->write($contlineas, 0,$ls_nomestpro2.":", $lo_titulo);
					$lo_hoja->write($contlineas, 1," ".$ls_tit3a, $lo_datacenter);
					$lo_hoja->write($contlineas, 2, $ls_tit3b,$lo_dataleft);
					$contlineas++;
					$lo_hoja->write($contlineas, 0,$ls_nomestpro3.":", $lo_titulo);
					$lo_hoja->write($contlineas, 1," ".$ls_tit4a, $lo_datacenter);
					$lo_hoja->write($contlineas, 2, $ls_tit4b,$lo_dataleft);
					$contlineas++;
					$lo_hoja->write($contlineas, 0,$ls_nomestpro4.":", $lo_titulo);
					$lo_hoja->write($contlineas, 1," ".$ls_tit5a, $lo_datacenter);
					$lo_hoja->write($contlineas, 2, $ls_tit5b,$lo_dataleft);
					$contlineas++;
					$lo_hoja->write($contlineas, 0,$ls_nomestpro5.":", $lo_titulo);
					$lo_hoja->write($contlineas, 1," ".$ls_tit6a, $lo_datacenter);
					$lo_hoja->write($contlineas, 2, $ls_tit6b,$lo_dataleft);
					$contlineas++;
				 
				}
				
				$lo_hoja->write($contlineas, 0,"Cuenta",$lo_titulo);
				$lo_hoja->write($contlineas, 1,"Denominación Cta Presupuestaria", $lo_titulo);
				$lo_hoja->write($contlineas, 2, "Cuenta Contable(Articulo)",$lo_titulo);
				$lo_hoja->write($contlineas, 3,"Denominación Cta Contable(Articulo)", $lo_titulo);
				$contlineas++;
				
				$rs_cuentas = NULL;
				
				$arrResultado=$io_spg_report->uf_reporte_listado_cuenta($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
														  $ls_estcla,$ls_cuenta_desde,$ls_cuenta_hasta,$ls_ctascg_desde,$ls_ctascg_hasta,
														  $rs_cuentas, true);
				$rs_cuentas=$arrResultado['rs_cuentas'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
				 while(!$rs_cuentas->EOF)
				 {
				  if($rs_cuentas->fields["status"] == 'C')
				  {
				   $lo_hoja->write($contlineas, 0," ".$rs_cuentas->fields["spg_cuenta"],$lo_datacenter_bold);
				   $lo_hoja->write($contlineas, 1,$rs_cuentas->fields["denominacion"], $lo_dataleft_bold);
				   $lo_hoja->write($contlineas, 2," ".$rs_cuentas->fields["sc_cuenta"],$lo_datacenter_bold);
				   $lo_hoja->write($contlineas, 3,$rs_cuentas->fields["denominacion_scg"], $lo_dataleft_bold);
				  }
				  else
				  {
				   $lo_hoja->write($contlineas, 0," ".$rs_cuentas->fields["spg_cuenta"],$lo_datacenter);
				   $lo_hoja->write($contlineas, 1,$rs_cuentas->fields["denominacion"], $lo_dataleft);
				   $lo_hoja->write($contlineas, 2," ".$rs_cuentas->fields["sc_cuenta"],$lo_datacenter);
				   $lo_hoja->write($contlineas, 3,$rs_cuentas->fields["denominacion_scg"], $lo_dataleft);
				  }
				  $contlineas++;
				  $rs_cuentas->MoveNext();
				 }
				
				}
			 $rs_estructuras->MoveNext();
			}
			
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"Listado_de_Cuentas_Presupuestarias.xls\"");
			header("Content-Disposition: inline; filename=\"Listado_de_Cuentas_Presupuestarias.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
			print("<script language=JavaScript>");
			print(" close();");
			print("</script>");
			unset($class_report);
			unset($io_funciones);
		}
	}
	else
	{
	 ?>
		<script language=javascript>
			 alert('No hay nada que reportar!!!');
			 close();
		</script>
	 <?php
	}
?>