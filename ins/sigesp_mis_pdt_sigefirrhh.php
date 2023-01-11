<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	session_start();
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";
		print "</script>";		
	}
	
   //----------------------------------------------------------------------------------------------------------------------------
   function uf_imprimirresultados($as_codcom,$as_tipoconcepto,$as_gestor_int,$as_puerto_int,$as_servidor_int,$as_basedatos_int,$as_login_int,$as_password_int)
   {
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_imprimirresultados
		//		   Access: private
		//	    Arguments: as_codcom  // Número de Comprobante
		//	  Description: Función que Imprime los detalles del comprobante
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 31/10/2006 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $in_class_ins;
		
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_include.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_sql.php");
		$in=new sigesp_include();
		$io_sigefirrhh=$in->uf_conectar_otra_bd($as_servidor_int,$as_login_int,$as_password_int,$as_basedatos_int,$as_gestor_int,$as_puerto_int);
		$io_sql=new class_sql($io_sigefirrhh);	
		$io_sql2=new class_sql($io_sigefirrhh);	
		require_once("../shared/class_folder/class_mensajes.php");
		$io_mensajes=new class_mensajes();
		require_once("../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$campo="codcom";
		if($as_tipoconcepto=='P')
		{
			$campo="codcomapo";
		}
		$ls_sql="SELECT ".$campo.", MAX(cod_pro) AS cod_pro, MAX(ced_bene) AS ced_bene, MAX(tipo_destino) AS tipo_destino, Max(descripcion) as descripcion ".
				"  FROM v_sno_dt_spg  ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".
				" GROUP BY ".$campo." ".
				" UNION ".
				"SELECT ".$campo.", MAX(cod_pro) AS cod_pro, MAX(ced_bene) AS ced_bene, MAX(tipo_destino) AS tipo_destino, Max(descripcion) as descripcion ".
				"  FROM v_sno_dt_spg_ne  ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".
				" GROUP BY ".$campo." ".
				" UNION ".
				"SELECT ".$campo.", MAX(cod_pro) AS cod_pro, MAX(ced_bene) AS ced_bene, MAX(tipo_destino) AS tipo_destino, Max(descripcion) as descripcion ".
				"  FROM v_sno_dt_spg_obreros  ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".
				" GROUP BY ".$campo." ".
				" UNION ".
				"SELECT ".$campo.", MAX(cod_pro) AS cod_pro, MAX(ced_bene) AS ced_bene, MAX(tipo_destino) AS tipo_destino, Max(descripcion) as descripcion ".
				"  FROM v_sno_dt_spg_obreros_ne  ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".
				" GROUP BY ".$campo."".
				" ORDER BY ".$campo."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_comprobante=$as_codcom;
				$ls_descripcion=$rs_data->fields["descripcion"];
				$ls_tipo_destino=$rs_data->fields["tipo_destino"];
				$ls_operacion='OCP';
				switch($ls_tipo_destino)
				{
					case "P":
						$ls_destino="Proveedor";
						$ls_nombre_destino=$rs_data->fields["cod_pro"]." - ".$rs_data->fields["nompro"];
						break;
	
					case "B":
						$ls_destino="Beneficiario";
						$ls_nombre_destino=$rs_data->fields["ced_bene"]." - ".$rs_data->fields["apebene"].", ".$rs_data->fields["nombene"];
						break;
						
					case "-":
						$ls_destino="-";
						$ls_nombre_destino="-";
						break;
				}
				switch($ls_operacion)
				{
					case "O":
						$ls_operacion="COMPROMETE";
						break;
	
					case "OC":
						$ls_operacion="COMPROMETE Y CAUSA";
						break;
	
					case "OCP":
						$ls_operacion="COMPROMETE, CAUSA Y PAGA";
						break;
	
					case "CP":
						$ls_operacion="CAUSAR Y PAGAR";
						break;

					case "DC":
						$ls_operacion="DEVENGADO Y COBRADO";
						break;

					case "":
						$ls_operacion="CONTABLE";
						break;
				}
				print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0'>";
				print "	<tr>";
				print "		<td width='450' class='titulo-ventana'>Información del Comprobante</td>";
				print " </tr>";
				print "</table>";
				print "<table width='450' border=0 cellpadding=1 cellspacing=1 align='center' class='formato-blanco'>";
				print "  <tr>";
				print "		<td width='100'><div align='right' class='texto-azul'>Comprobante</div></td>";
				print "		<td width='350'><div align='left'>".$ls_comprobante."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'>Descripci&oacute;n </div></td>";
				print "		<td><div align='justify'>".$ls_descripcion."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'>".$ls_destino."</div></td>";
				print "		<td><div align='left'>".$ls_nombre_destino."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'>Contabilizaci&oacute;n </div></td>";
				print "		<td><div align='left'>".$ls_operacion."</div></td>";
				print "  </tr>";
				print "  <tr>";
				print "		<td><div align='right' class='texto-azul'></div></td>";
				print "		<td><div align='left'></div></td>";
				print "  </tr>";
				print "</table>";
				$ls_sql="SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal, ".
						"		SUM(monto_asigna) as monto_asigna, SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
						"  FROM v_sno_dt_spg  ".
						" WHERE codemp='".$ls_codemp."'".
						"   AND ".$campo."='".$as_codcom."' ".
						"   AND tipo_concepto = '".$as_tipoconcepto."'".
						" GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal ".
						" UNION ".
						"SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal, ".
						"		SUM(monto_asigna) as monto_asigna, SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
						"  FROM v_sno_dt_spg_ne  ".
						" WHERE codemp='".$ls_codemp."'".
						"   AND ".$campo."='".$as_codcom."' ".
						"   AND tipo_concepto = '".$as_tipoconcepto."'".
						" GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal ".
						" UNION ".
						"SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta,spg_cuenta_patronal, ".
						"		SUM(monto_asigna) as monto_asigna, SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
						"  FROM v_sno_dt_spg_obreros  ".
						" WHERE codemp='".$ls_codemp."'".
						"   AND ".$campo."='".$as_codcom."' ".
						"   AND tipo_concepto = '".$as_tipoconcepto."'".
						" GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal ".
						" UNION ".
						"SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal, ".
						"		SUM(monto_asigna) as monto_asigna, SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
						"  FROM v_sno_dt_spg_obreros_ne  ".
						" WHERE codemp='".$ls_codemp."'".
						"   AND ".$campo."='".$as_codcom."' ".
						"   AND tipo_concepto = '".$as_tipoconcepto."'".
						" GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal ".
						" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal ";
				$rs_data2=$io_sql2->select($ls_sql);
				if($rs_data2===false)
				{
					$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql2->message)); 
				} 
				else
				{
					print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0' class='formato-blanco'>";
					print "	<tr>";
					print "		<td colspan='4' class='titulo-celdanew'>Detalle Presupuestario</td>";
					print " </tr>";
					print " <tr class=titulo-celdanew>";
					print "		<td width='240'>Estructura</td>";
					print "		<td width='50'>Estatus</td>";
					print "		<td width='80'>Cuenta</td>";
					print "		<td width='80'>Monto</td>";
					print "	</tr>";
					$li_total=0;
					while(!$rs_data2->EOF)
					{
						/*if((trim($rs_data2->fields["spg_cuenta_patronal"])=='')||(empty($rs_data2->fields["spg_cuenta_patronal"])))
						{
							$ls_cuenta=$rs_data2->fields["spg_cuenta"];
							$li_monto=number_format($rs_data2->fields["monto_asigna"] +$rs_data2->fields["monto_deduce"],2,'.','');
						}
						else
						{
							$ls_cuenta=$rs_data2->fields["spg_cuenta"];
							$li_monto=number_format($rs_data2->fields["monto_deduce"],2,'.','');
						}*/
						$ls_cuenta=$rs_data2->fields["spg_cuenta"];
						if($as_tipoconcepto=='N')
						{
							$li_monto=number_format($rs_data2->fields["monto_asigna"] +$rs_data2->fields["monto_deduce"],2,'.','');
						}
						else
						{
							$li_monto=number_format($rs_data2->fields["monto_aporte"],2,'.','');
						}
						$li_total=$li_total+$li_monto;
						$ls_codestpro=$rs_data2->fields["codestpro1"].$rs_data2->fields["codestpro2"].$rs_data2->fields["codestpro3"].$rs_data2->fields["codestpro4"].$rs_data2->fields["codestpro5"];
						$ls_estcla=$rs_data2->fields["estcla"];
						$ls_programatica="";
						$ls_programatica = $in_class_ins->uf_formatoprogramatica($ls_codestpro,$ls_programatica);
						switch($ls_estcla)
						{
							case "A":
								$ls_estatus="Acción";
								break;
							case "P":
								$ls_estatus="Proyecto";
								break;
						}
						print "<tr class=celdas-blancas>";
						print "<td align=center width='240'>".$ls_programatica."</td>";
						print "<td align=center width='50'>".$ls_estatus."</td>";
						print "<td align=center width='80'>".$ls_cuenta."</td>";
						print "<td align=right width='80'>".number_format($li_monto,2,',','.')."  </td>";
						print "</tr>";	
						/*if(trim($rs_data2->fields["spg_cuenta_patronal"]) <> '')
						{
							$ls_cuenta=$rs_data2->fields["spg_cuenta_patronal"];
							$li_monto=number_format($rs_data2->fields["monto_aporte"],2,'.','');
							$li_total=$li_total+$li_monto;
							print "<tr class=celdas-blancas>";
							print "<td align=center width='240'>".$ls_programatica."</td>";
							print "<td align=center width='50'>".$ls_estatus."</td>";
							print "<td align=center width='80'>".$ls_cuenta."</td>";
							print "<td align=right width='80'>".number_format($li_monto,2,',','.')."  </td>";
							print "</tr>";	
						}		*/
						$rs_data2->MoveNext();	
					}
					$li_total=number_format($li_total,2,',','.');
					print "	<tr class=celdas-blancas>";
					print "		<td colspan='3' align='right' class='texto-azul'>Total</td>";
					print "		<td width='100' align='right' class='texto-azul'>".$li_total."</td>";
					print "		<td align=right width='90'></td>";
					print " </tr>";
					print "</table>";
				}
				$io_sql2->free_result($rs_data2);	
				$ls_sql="SELECT sc_cuenta, sc_cuenta_patronal, debhab, SUM(monto_asigna) as monto_asigna,  ".
						"		SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
						"  FROM v_sno_dt_scg  ".
						" WHERE codemp='".$ls_codemp."'".
						"   AND ".$campo."='".$as_codcom."' ".
						"   AND tipo_concepto = '".$as_tipoconcepto."'".
						" GROUP BY sc_cuenta, sc_cuenta_patronal, debhab ".
						" UNION ".
						"SELECT sc_cuenta, sc_cuenta_patronal, debhab, SUM(monto_asigna) as monto_asigna,  ".
						"		SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
						"  FROM v_sno_dt_scg_ne  ".
						" WHERE codemp='".$ls_codemp."'".
						"   AND ".$campo."='".$as_codcom."' ".
						"   AND tipo_concepto = '".$as_tipoconcepto."'".
						" GROUP BY sc_cuenta, sc_cuenta_patronal, debhab ".
						" UNION ".
						"SELECT sc_cuenta, sc_cuenta_patronal, debhab, SUM(monto_asigna) as monto_asigna,  ".
						"		SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
						"  FROM v_sno_dt_scg_obreros  ".
						" WHERE codemp='".$ls_codemp."'".
						"   AND ".$campo."='".$as_codcom."' ".
						"   AND tipo_concepto = '".$as_tipoconcepto."'".
						" GROUP BY sc_cuenta, sc_cuenta_patronal, debhab ".
						" UNION ".
						"SELECT sc_cuenta, sc_cuenta_patronal, debhab, SUM(monto_asigna) as monto_asigna,  ".
						"		SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
						"  FROM v_sno_dt_scg_obreros_ne  ".
						" WHERE codemp='".$ls_codemp."'".
						"   AND ".$campo."='".$as_codcom."' ".
						"   AND tipo_concepto = '".$as_tipoconcepto."'".
						" GROUP BY sc_cuenta, sc_cuenta_patronal, debhab ".
						" ORDER BY sc_cuenta, sc_cuenta_patronal, debhab";
				$rs_data2=$io_sql2->select($ls_sql);
				if($rs_data2===false)
				{
					$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql2->message)); 
				}
				else
				{
					$li_total_deb=0;
					$li_total_hab=0;
					print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0' class='formato-blanco'>";
					print "	<tr>";
					print "		<td colspan='3' class='titulo-celdanew'>Detalle Contable</td>";
					print " </tr>";
					print " <tr class=titulo-celdanew>";
					print "		<td width='100'>Cuenta</td>";
					print "		<td width='100'>Debe</td>";
					print "		<td width='100'>Haber</td>";
					print "	</tr>";
					while(!$rs_data2->EOF)
					{
						/*if((trim($rs_data2->fields["sc_cuenta_patronal"])=='')||(empty($rs_data2->fields["sc_cuenta_patronal"])))
						{
							$ls_cuenta=$rs_data2->fields["sc_cuenta"];
							$li_monto=number_format($rs_data2->fields["monto_asigna"] +$rs_data2->fields["monto_deduce"],2,'.','');
						}
						else
						{
							$ls_cuenta=$rs_data2->fields["sc_cuenta"];
							$li_monto=number_format($rs_data2->fields["monto_deduce"],2,'.','');
						}*/
						if($as_tipoconcepto=='N')
						{
							$li_monto=number_format($rs_data2->fields["monto_asigna"] +$rs_data2->fields["monto_deduce"],2,'.','');
						}
						else
						{
							$li_monto=number_format($rs_data2->fields["monto_aporte"],2,'.','');
						}
						$ls_cuenta=$rs_data2->fields["sc_cuenta"];						
						$ls_debhab=$rs_data2->fields["debhab"];
						switch($ls_debhab)
						{
							case "D":
								$li_debe=$li_monto;
								$li_haber="0,00";
								$li_total_deb=$li_total_deb+$li_monto;
								break;
							case "H":
								$li_debe="0,00";
								$li_haber=$li_monto;
								$li_total_hab=$li_total_hab+$li_monto;
								break;
						}
						print "<tr class=celdas-blancas>";
						print "<td align=center width='100'>".$ls_cuenta."</td>";
						print "<td align=right width='100'>".number_format($li_debe,2,',','.')."</td>";
						print "<td align=right width='100'>".number_format($li_haber,2,',','.')."</td>";
						print "</tr>";			
						/*if(trim($rs_data2->fields["sc_cuenta_patronal"]) <> '')
						{
							$ls_cuenta=$rs_data2->fields["sc_cuenta_patronal"];
							$li_monto=number_format($rs_data2->fields["monto_aporte"],2,'.','');
							switch($ls_debhab)
							{
								case "D":
									$li_debe=$li_monto;
									$li_haber="0,00";
									$li_total_deb=$li_total_deb+$li_monto;
									break;
								case "H":
									$li_debe="0,00";
									$li_haber=$li_monto;
									$li_total_hab=$li_total_hab+$li_monto;
									break;
							}
							print "<tr class=celdas-blancas>";
							print "<td align=center width='100'>".$ls_cuenta."</td>";
							print "<td align=right width='100'>".number_format($li_debe,2,',','.')."</td>";
							print "<td align=right width='100'>".number_format($li_haber,2,',','.')."</td>";
							print "</tr>";			
						}*/
						$rs_data2->MoveNext();	
					}
					$li_total_deb=number_format($li_total_deb,2,',','.');
					$li_total_hab=number_format($li_total_hab,2,',','.');
					print "	<tr>";
					print "		<td align=right class='texto-azul'>Total</td>";
					print "		<td align=right class='texto-azul'>".$li_total_deb."</td>";
					print "		<td align=right class='texto-azul'>".$li_total_hab."</td>";
					print " </tr>";
					print "</table>";
				}
				$io_sql2->free_result($rs_data2);
				print "<br><br>";	
				$rs_data->MoveNext();	
			}
			$io_sql->free_result($rs_data);	
		}
   }
   //----------------------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<!--<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
--><title>Detalle Comprobante</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style>
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
</p>
<?php
	require_once("class_folder/class_funciones_ins.php");
	$in_class_ins=new class_funciones_ins("../");
	$ls_codcom=$in_class_ins->uf_obtenervalor_get("codcom","");
	$ls_tipoconcepto=$in_class_ins->uf_obtenervalor_get("tipoconcepto","");	
	$ls_gestor_int=$in_class_ins->uf_obtenervalor_get("gestor_int","");
	$ls_puerto_int=$in_class_ins->uf_obtenervalor_get("puerto_int","");
	$ls_servidor_int=$in_class_ins->uf_obtenervalor_get("servidor_int","");
	$ls_basedatos_int=$in_class_ins->uf_obtenervalor_get("basedatos_int","");
	$ls_login_int=$in_class_ins->uf_obtenervalor_get("login_int","");
	$ls_password_int=$in_class_ins->uf_obtenervalor_get("password_int","");

	uf_imprimirresultados($ls_codcom,$ls_tipoconcepto,$ls_gestor_int,$ls_puerto_int,$ls_servidor_int,$ls_basedatos_int,$ls_login_int,$ls_password_int);
?>
</div>
</form>
</body>
</html>