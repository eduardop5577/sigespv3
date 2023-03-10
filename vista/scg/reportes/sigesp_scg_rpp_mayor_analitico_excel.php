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
	set_time_limit(0);
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 22/09/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scg;
		
		$ls_descripcion="Gener? el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_mayor_analitico.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este m?todo realiza una consulta a los formatos de las cuentas
		//               para conocer los niveles de la escalera de las cuentas contables  
		//////////////////////////////////////////////////////////////////////////////////////////////////////
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

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "mayor_analitico.xls");
		$lo_libro = new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once("class_funciones_scg.php");
		$io_fun_scg=new class_funciones_scg();
		$ls_bolivares="";
		if (array_key_exists("tiporeporte",$_GET))
		{
			$ls_tiporeporte=$_GET["tiporeporte"];
		}

		require_once("sigesp_scg_reporte.php");
		$io_report  = new sigesp_scg_reporte();
		$ls_bolivares ="Bs.";

		$ia_niveles_scg[0]="";			
		uf_init_niveles();
		$li_total=count((array)$ia_niveles_scg)-1;
	//---------------------------------------------------------------------------------------------------------------------------
	//Par?metros para Filtar el Reporte
		$ld_fecdesde=$_GET["fecdes"];
		$ld_fechasta=$_GET["fechas"];
		$ls_cuentadesde=$_GET["cuentadesde"];
		$ls_cuentahasta=$_GET["cuentahasta"];
		if(($ls_cuentadesde=="")&&($ls_cuentahasta==""))
		{
			$arrResultado = $io_report->uf_spg_reporte_select_cuenta($ls_cuentadesde,$ls_cuentahasta);
			$ls_cuentadesde = $arrResultado['as_sc_cuenta_min'];
			$ls_cuentahasta = $arrResultado['as_sc_cuenta_max'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido)
			{
				//$ls_cuentadesde=$ls_cuentadesde_min;
				//$ls_cuentahasta=$ls_cuentahasta_max;
			} 
		}
		$ls_parm_orden=$_GET["orden"];
	//---------------------------------------------------------------------------------------------------------------------------
	//Par?metros del encabezado
		$ldt_fecha="Desde   ".$ld_fecdesde."   al   ".$ld_fechasta."";
		$ls_titulo="Mayor  Analitico";       
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data
	$lb_valido=uf_insert_seguridad("<b>Mayor Anal?tico en Excel</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_cargar_mayor_analitico($ld_fecdesde,$ld_fechasta,$ls_cuentadesde,$ls_cuentahasta,$ls_parm_orden);
	}
	//---------------------------------------------------------------------------------------------------------------------------
	// Impresi?n de la informaci?n encontrada en caso de que exista
	if($lb_valido===false) // Existe alg?n error ? no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
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
		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_hoja->set_column(0,0,15);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,70);
		$lo_hoja->set_column(3,3,30);		
		$lo_hoja->set_column(4,4,20);
		$lo_hoja->set_column(5,5,13);
		$lo_hoja->set_column(6,8,30);
		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 3, $ldt_fecha,$lo_encabezado);
		$li_tot=$io_report->rs_analitico->RecordCount();
		$ld_totaldebe=0;
		$ld_totalhaber=0;
		$ld_totalsaldo=0;
        $ld_saldo=0;
		$ldec_mondeb=0;
        $ldec_monhab=0;
		$li_row=2;
		$ls_cuenta_ant="";
		$ld_montototaldebe=0;
		$ld_montototalhaber=0;		
		$siguiente = 0;	
		$count = 0;
		$i=1;
		$ls_anterior="";
		$ls_actual="";	
		while(!$io_report->rs_analitico->EOF)
		{
			$count++;
			$ls_comprobante=$io_report->rs_analitico->fields["comprobante"];
			$ls_cuenta=trim($io_report->rs_analitico->fields["sc_cuenta"]);
			if(!empty($ls_cuenta))
			{
				$ls_cuenta_ant=trim($io_report->rs_analitico->fields["sc_cuenta"]);
			    $ls_actual=trim($io_report->rs_analitico->fields["sc_cuenta"]);
			}
			$ls_denominacion=$io_report->rs_analitico->fields["denominacion"];
			$ls_codpro=$io_report->rs_analitico->fields["cod_pro"];
			$ls_cedbene=$io_report->rs_analitico->fields["ced_bene"];
			$ls_nompro=$io_report->rs_analitico->fields["nompro"];
			$ls_nombene=$io_report->rs_analitico->fields["apebene"].", ".$io_report->rs_analitico->fields["nombene"];
			$ls_nombre="";
			if($ls_codpro!="----------")
			{
				$ls_nombre=$ls_nompro;
			}			
			if($ls_cedbene!="----------")
			{
				$ls_nombre=$ls_nombene;
			}			
			$ls_documento=$io_report->rs_analitico->fields["documento"];
			$ls_procede=$io_report->rs_analitico->fields["procede"];
			$ls_concepto=$io_report->rs_analitico->fields["descripcion"];
			$ldec_monto=$io_report->rs_analitico->fields["monto"];
			$fecmov=$io_report->rs_analitico->fields["fecha"];
			$ld_fecmov=$io_funciones->uf_convertirfecmostrar($fecmov);
			$ls_debhab=$io_report->rs_analitico->fields["debhab"];
			$ld_saldo_ant=$io_report->rs_analitico->fields["saldo_ant"];
			$li_totfil=0;
			$as_cuenta="";
			for($li=$li_total;$li>1;$li--)
			{
				$li_ant=$ia_niveles_scg[$li-1];
				$li_act=$ia_niveles_scg[$li];
				$li_fila=$li_act-$li_ant;
				$li_len=strlen($ls_cuenta);
				$li_totfil=$li_totfil+$li_fila;
				$li_inicio=$li_len-$li_totfil;
				if($li==$li_total)
				{
					$as_cuenta=substr($ls_cuenta,$li_inicio,$li_fila);
				}
				else
				{
					$as_cuenta=substr($ls_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
				}
			}
			$li_fila=$ia_niveles_scg[1]+1;
			$as_cuenta=substr($ls_cuenta,0,$li_fila)."-".$as_cuenta;
			if((!empty($ls_cuenta) and $siguiente and $count<$li_tot) or $li_row==2)
			{
				$ls_cuenta_ant=$ls_cuenta;
				$li_row=$li_row+1;
				$lo_hoja->write($li_row, 0, "Cuenta",$lo_titulo);
				$lo_hoja->write($li_row, 1, $as_cuenta,$lo_datacenter);
				$lo_hoja->write($li_row, 2, $ls_denominacion,$lo_libro->addformat(array('font'=>'Verdana','align'=>'left','size'=>'9')));
				$li_row=$li_row+1;
				$lo_hoja->write($li_row, 0, "Saldo Anterior ".$ls_bolivares,$lo_titulo);
				$lo_hoja->write($li_row, 1, $ld_saldo_ant,$lo_dataright);
				$li_row=$li_row+1;
				$lo_hoja->write($li_row, 0, "Procede",$lo_titulo);
				$lo_hoja->write($li_row, 1, "Comprobante",$lo_titulo);
				$lo_hoja->write($li_row, 2, "Concepto",$lo_titulo);
				$lo_hoja->write($li_row, 3, "Beneficiario",$lo_titulo);
				$lo_hoja->write($li_row, 4, "Documento",$lo_titulo);
				$lo_hoja->write($li_row, 5, "Fecha",$lo_titulo);
				$lo_hoja->write($li_row, 6, "Debe",$lo_titulo);
				$lo_hoja->write($li_row, 7, "Haber",$lo_titulo);
				$lo_hoja->write($li_row, 8, "Saldo Actual",$lo_titulo);
			}
			if($ls_debhab=='D')
			{
				$ldec_mondeb=$ldec_monto;
				$ldec_monhab=0;		
				$ld_totaldebe=$ld_totaldebe+$ldec_mondeb;
			}
			elseif($ls_debhab=='H')
			{
				$ldec_monhab=$ldec_monto;		
				$ldec_mondeb=0;
				$ld_totalhaber=$ld_totalhaber+$ldec_monhab;
			}
			else
			{
			 $ldec_monhab=0;
			 $ldec_mondeb=0;
			}
			if ($ls_anterior!=$ls_actual)
			{
				$ld_saldo=$ld_saldo_ant+$ldec_mondeb-$ldec_monhab;
			    $ls_anterior=$ls_actual;
			}
			else
			{
				if($ls_debhab=='D')
				{
					$ld_saldo=$ld_saldo+$ldec_monto;
				}
				elseif($ls_debhab=='H')
				{
					$ld_saldo=$ld_saldo-$ldec_monto;
				}
			}
			$ldec_mondeb=abs($ldec_mondeb);
			$ldec_monhab=abs($ldec_monhab);
			$ld_saldo_final=$ld_saldo;
			$li_row=$li_row+1;
			if(!empty($ls_comprobante))
			{
				 $lo_hoja->write($li_row, 0, $ls_procede,$lo_datacenter);
				 $lo_hoja->write($li_row, 1, $ls_comprobante." ",$lo_datacenter);
				 $lo_hoja->write($li_row, 2, $ls_concepto,$lo_dataleft);
				 $lo_hoja->write($li_row, 3, $ls_nombre,$lo_dataleft);
				 $lo_hoja->write($li_row, 4, $ls_documento." ",$lo_datacenter);
				 $lo_hoja->write($li_row, 5, $ld_fecmov,$lo_datacenter);
				 $lo_hoja->write($li_row, 6, $ldec_mondeb,$lo_dataright);
				 $lo_hoja->write($li_row, 7, $ldec_monhab,$lo_dataright);
				 $lo_hoja->write($li_row, 8, $ld_saldo_final,$lo_dataright);
			}
			$siguiente = 0;
			$cuenta_anterior=trim($io_report->rs_analitico->fields["sc_cuenta"]);
			$io_report->rs_analitico->MoveNext();
			$cuenta_actual=trim($io_report->rs_analitico->fields["sc_cuenta"]);	
			if($cuenta_anterior!=$cuenta_actual)
			{
				$siguiente = 1;
			}
			elseif($li_tot==$count)
			{	
				$siguiente = 1;
			}
			if ($siguiente==1)
			{
				$ldec_mondeb=abs($ldec_mondeb);
				$ldec_monhab=abs($ldec_monhab);
				$ld_saldo_final=$ld_saldo;
				$ld_saldo_anterior=$ld_saldo_ant;
				$li_row=$li_row+1;
				$ld_montototaldebe=$ld_montototaldebe+$ld_totaldebe;
				$ld_montototalhaber=$ld_montototalhaber+$ld_totalhaber;
				$lo_hoja->write($li_row, 5, "Total ".$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
				$lo_hoja->write($li_row, 6, $ld_totaldebe,$lo_dataright);
				$lo_hoja->write($li_row, 7, $ld_totalhaber,$lo_dataright);
				$lo_hoja->write($li_row, 8, $ld_saldo_final,$lo_dataright);
				$ld_totaldebe=0;
				$ld_totalhaber=0;
				$ls_cuenta_next="";
				$ls_cuenta_ant="";
			}
		}
		$li_row=$li_row+2;
		$lo_hoja->write($li_row, 4, "Total General al ".$ld_fechasta.'  '.$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
		$lo_hoja->write($li_row, 6, $ld_montototaldebe,$lo_dataright);
		$lo_hoja->write($li_row, 7, $ld_montototalhaber,$lo_dataright);
		
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"mayor_analitico.xls\"");
		header("Content-Disposition: inline; filename=\"mayor_analitico.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}
?> 