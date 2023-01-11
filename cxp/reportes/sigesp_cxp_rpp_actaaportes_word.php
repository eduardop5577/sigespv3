<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
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
		print "opener.document.form1.submit();";		
		print "</script>";		
	}

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		$lb_valido=true;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_actaretencion.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_leer_archivo($as_archivo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_leer_archivo
		//		   Access: private 
		//	    Arguments: as_archivo //  ruta donde se encuentra el archivo
		//    Description: función que lee un archivo de texto y lo mete en una cadena
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 08/06/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_texto = file($as_archivo);
		$li_tamano = sizeof($ls_texto);
		$ls_textocompleto="";
		for($li_i=0;$li_i<$li_tamano;$li_i++)
		{
			$ls_textocompleto=$ls_textocompleto.$ls_texto[$li_i];
		}
		return $ls_textocompleto;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	include("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
	$io_numero_letra= new class_numero_a_letra();
	//imprime numero con los valore por defecto
	//cambia a minusculas
	$io_numero_letra->setMayusculas(1);
	//cambia a femenino
	$io_numero_letra->setGenero(1);
	//cambia moneda
	$io_numero_letra->setMoneda("Bolivares");
	//cambia prefijo
	$io_numero_letra->setPrefijo("");
	//cambia sufijo
	$io_numero_letra->setSufijo("");
	//imprime numero con los cambios
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();
	require_once("../../shared/class_folder/evaluate_formula.php");
	$io_evaluate=new evaluate_formula();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<i>CONSTANCIA</i>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_codigo=$io_fun_cxp->uf_obtenervalor_get("codigo","");
	$ls_tipcont=$io_fun_cxp->uf_obtenervalor_get("tipcont","");
	$ls_codcont=$io_fun_cxp->uf_obtenervalor_get("codcont","");
	$ls_condpag=$io_fun_cxp->uf_obtenervalor_get("condpag","");

	$arr_comprobantes=explode ("@@",$ls_comprobantes); 
	$li_totcomprobantes=count((array)$arr_comprobantes);
	global $ls_tiporeporte;
	
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_existe=false;
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte	
	if($lb_valido)
	{
		$rs_data=$io_report->uf_acta_retencion($ls_codigo); // Obtenemos el detalle del reporte
	}
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

		if ((!$rs_data->EOF))
		{
			$lb_existe=true;
			$ls_archrtf=$rs_data->fields["archrtf"];
		}
		$ls_archivo="../documentos/".$ls_archrtf;
		$ls_copia=substr($ls_archrtf,0,strrpos($ls_archrtf,"."));
		$ls_salida="../documentos/copia/".$ls_copia."-".$_SESSION["la_logusr"].".rtf";
		$ls_contenido="";
		$ls_contenido=uf_leer_archivo($ls_archivo);
		$la_matriz=explode("sectd",$ls_contenido);
		$ls_cabecera=$la_matriz[0]."sectd";
		$li_inicio=strlen($ls_cabecera);
		$li_final=strrpos($ls_contenido,"}");
		$li_longitud=$li_final-$li_inicio;
		$ls_nuevocuerpo=substr($ls_contenido,$li_inicio,$li_longitud);
		$ls_punt=fopen($ls_salida,"w");
		fputs($ls_punt,$ls_cabecera);
		$ls_cuerpo=$ls_nuevocuerpo;
		if($lb_existe)
		{
			for($li_i=0;$li_i<$li_totcomprobantes;$li_i++)
			{
				$arr_documentos=explode ("**",$arr_comprobantes[$li_i]);
				$ls_compromiso=$arr_documentos[0];
				$ls_numrecdoc=$arr_documentos[1];
				$ls_codtipdoc=$arr_documentos[2];
				$ls_cedbene=$arr_documentos[3];
				$ls_codpro=$arr_documentos[4];
				$rs_valores=$io_report->uf_obtener_valores($ls_numrecdoc,$ls_codtipdoc,$ls_codpro,$ls_cedbene); // Obtenemos el detalle del reporte
				
				if ((!$rs_valores->EOF))
				{
					$ls_dencondoc=$rs_valores->fields["dencondoc"];
					$ls_montotdoc=$rs_valores->fields["montotdoc"];
					$ls_moncardoc=$rs_valores->fields["moncardoc"];
					$ls_mondeddoc=$rs_valores->fields["mondeddoc"];
					$ls_monret=$rs_valores->fields["monret"];
					$ls_nompro=$rs_valores->fields["nompro"];
					$ls_cedrep=$rs_valores->fields["cedrep"];
					$ls_nomreppro=$rs_valores->fields["nomreppro"];
					$ls_carrep=$rs_valores->fields["carrep"];
					$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
					$ls_ciuemp=$_SESSION["la_empresa"]["ciuemp"];
					$ls_dia=date("d");
					$ls_mes=$io_fecha->uf_load_nombre_mes(date("m"));
					$ls_ano=date("Y");
					$ls_hora=date("h:i A");
					$ls_baseimp=$ls_montotdoc+$ls_mondeddoc-$ls_moncardoc;
					$ls_totiva=$ls_montotdoc+$ls_mondeddoc;
					$ls_baseimp=number_format($ls_baseimp,2,',','.');
					$ls_moncardoc=number_format($ls_moncardoc,2,',','.');
					$ls_totiva=number_format($ls_totiva,2,',','.');
					$ls_mondeddoc=number_format($ls_mondeddoc,2,',','.');
					$io_numero_letra->setNumero($ls_monret);
					$ls_monret=number_format($ls_monret,2,',','.');
					$ls_monletras= $io_numero_letra->letra();

					$ls_cuerpo=str_replace("@empresa@",$ls_nomemp,$ls_cuerpo);
					$ls_cuerpo=str_replace("@ciudad@",$ls_ciuemp,$ls_cuerpo);
					$ls_cuerpo=str_replace("@dia@",$ls_dia,$ls_cuerpo);
					$ls_cuerpo=str_replace("@mes@",$ls_mes,$ls_cuerpo);
					$ls_cuerpo=str_replace("@ano@",$ls_ano,$ls_cuerpo);
					$ls_cuerpo=str_replace("@hora@",$ls_hora,$ls_cuerpo);
					$ls_cuerpo=str_replace("@representante@",$ls_nomreppro,$ls_cuerpo);
					$ls_cuerpo=str_replace("@cedularep@",$ls_cedrep,$ls_cuerpo);
					$ls_cuerpo=str_replace("@cargorep@",$ls_carrep,$ls_cuerpo);
					$ls_cuerpo=str_replace("@proveedor@",$ls_nompro,$ls_cuerpo);
					$ls_cuerpo=str_replace("@compromiso@",$ls_compromiso,$ls_cuerpo);
					$ls_cuerpo=str_replace("@conceptocomp@",$ls_dencondoc,$ls_cuerpo);
					$ls_cuerpo=str_replace("@tipocontra@",$ls_tipcont,$ls_cuerpo);
					$ls_cuerpo=str_replace("@codigocont@",$ls_codcont,$ls_cuerpo);
					$ls_cuerpo=str_replace("@condpago@",$ls_condpag,$ls_cuerpo);
					$ls_cuerpo=str_replace("@baseimponible@",$ls_baseimp,$ls_cuerpo);
					$ls_cuerpo=str_replace("@montoretenido@",$ls_moncardoc,$ls_cuerpo);
					$ls_cuerpo=str_replace("@montototal@",$ls_totiva,$ls_cuerpo);
					$ls_cuerpo=str_replace("@aporteletras@",$ls_monletras,$ls_cuerpo);
					$ls_cuerpo=str_replace("@aportesocial@",$ls_monret,$ls_cuerpo);
				}
			}
		}
		fputs($ls_punt,$ls_cuerpo);
		fputs($ls_punt,"}");
		fclose($ls_punt);
		@chmod($ls_salida,0755);
		if($lb_valido) // Si no ocurrio ningún error
		{
			header ("Content-Disposition: attachment; filename=".$ls_copia."-".$_SESSION["la_logusr"].".rtf\n\n");
			header ("Content-Type: application/octet-stream");
			header ("Content-Length: ".filesize($ls_salida));
			readfile($ls_salida);
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");		
		}	
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?> 