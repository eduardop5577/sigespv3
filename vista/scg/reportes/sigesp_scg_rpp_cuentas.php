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
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_cuentas.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		    Acess: private 
		//	    Arguments: ldec_monto : Monto del cheque
		//	    		   ls_nomproben:  Nombre del proveedor o beneficiario
		//	    		   ls_monto : Monto en letras
		//	    		   ls_fecha : Fecha del cheque
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 25/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,718,11,$as_periodo); // Agregar el título
		$io_pdf->addText(500,740,7,$_SESSION["ls_database"]); // Agregar la Base de datos
		$io_pdf->addText(500,730,10,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(500,720,10,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 24/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		global $io_pdf;		
		$la_columna=array('cuenta'=>'<b>Cuenta</b>','denominacion'=>'<b>Denominacion</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
							 'showLines'=>1, // Mostrar Líneas
							 'shaded'=>0, // Sombra entre líneas
							 'shadeCol'=>array(0.95,0.95,0.95), // Color de la sombra
							 'shadeCol2'=>array(1.5,1.5,1.5), // Color de la sombra
							 'xOrientation'=>'center', // Orientación de la tabla
							 'width'=>550, // Ancho de la tabla
							 'maxWidth'=>550,
							 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>160),
							               'denominacion'=>array('justification'=>'left','width'=>390))); // Ancho Máximo de la tabla

		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$io_pdf->ezText('                     ',10);//Inserto una linea en blanco
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este método realiza una consulta a los formatos de las cuentas
		//               para conocer los niveles de la escalera de las cuentas contables  
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones,$ia_niveles_scg;
		global $io_pdf;		
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
	

	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
	$in=new sigesp_include();
	$con=$in->uf_conectar();
	require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql=new class_sql($con);	
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../../../base/librerias/php/general/sigesp_lib_datastore.php");
	$ds_cta=new class_datastore();	
	require_once("class_funciones_scg.php");
	$io_fun_scg=new class_funciones_scg();
	$ia_niveles_scg[0]="";			
	uf_init_niveles();
	$li_total=count((array)$ia_niveles_scg)-1;
	
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_cuenta_desde=$_GET["cuentadesde"];
	$ls_cuenta_hasta=$_GET["cuentahasta"];
	$ls_costodesde = $_GET['costodesde'];
	$ls_costohasta = $_GET['costohasta'];
	$ls_aux="";
	$ls_filtrocosto = '';
	$lb_valido=uf_insert_seguridad("<b>Listado de Cuentas en PDF</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		if((!empty($ls_cuenta_desde))&&(!empty($ls_cuenta_hasta)))
		{
			$ls_aux=" AND sc_cuenta between '".$ls_cuenta_desde."' AND '".$ls_cuenta_hasta."'";
		}
		
		if((!empty($ls_costodesde))&&(!empty($ls_costohasta)))
		{
			$loncencos = strlen($ls_costodesde);
			$ls_filtrocosto = " AND SUBSTR(sc_cuenta,".$_SESSION["la_empresa"]["inicencos"].",".$loncencos.") BETWEEN '{$ls_costodesde}' AND '{$ls_costohasta}' ";
		}
		$ls_sql="SELECT distinct(sc_cuenta),denominacion FROM scg_cuentas WHERE codemp='".$ls_codemp."'".$ls_aux.$ls_filtrocosto." ORDER BY sc_cuenta ";
	
		$rs_data=$io_sql->select($ls_sql);	
		if($rs_data===false)
		{
			?>
			<script language=javascript>
			 alert("<?php print $io_funciones->uf_convertirmsg($io_sql->message);?>");
			 close();
			</script>
			<?php
			exit();
		}
		else
		{
			$ds_cta->data=$io_sql->obtener_datos($rs_data);
		}

		$li_totrow=$ds_cta->getRowCount("sc_cuenta");
		if($li_totrow<=0)
		{
			?>
			<script language=javascript>
			 alert('No hay datos a reportar');
			 close();
			</script>
			<?php
			exit();
		}
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3.5,3.5,3.5); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina("<b>Listado de Cuentas Contables</b>","Desde ".$ls_cuenta_desde." hasta ".$ls_cuenta_hasta,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
			//$io_pdf->transaction('start'); // Iniciamos la transacción
			$thisPageNum=$io_pdf->ezPageCount;
			$li_totprenom=0;
			$ldec_mondeb=0;
			$ldec_monhab=0;
			$li_totant=0;
			//unset($la_data);
			$ls_cuenta=rtrim($ds_cta->getValue("sc_cuenta",$li_i));
			$ls_denominacion=rtrim($ds_cta->getValue("denominacion",$li_i));
	
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
			$la_data[$li_i]=array('cuenta'=>$as_cuenta,'denominacion'=>$ls_denominacion);
			
						
		}
	
		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		unset($la_data);
		$io_pdf->transaction('commit');
		$io_pdf->ezStopPageNumbers(1,1);
		$io_pdf->ezStream();
		unset($io_pdf);
	}
	unset($class_report);
	unset($io_funciones);
?> 