<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
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
   function uf_imprimirresultados($as_codcom,$as_disponibilidad)
   {
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_imprimirresultados
		//		   Access: private
		//	    Arguments: as_codcom  // Número de Comprobante
		//	  Description: Función que Imprime los detalles del comprobante
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 31/10/2006 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$in=new sigesp_include();
		$con=$in->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($con);
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql2=new class_sql($con);
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		require_once("../shared/class_folder/class_sigesp_int.php");
		require_once("../shared/class_folder/class_sigesp_int_int.php");
		require_once("../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../shared/class_folder/class_sigesp_int_spg.php");
		$io_int_spg=new class_sigesp_int_spg();

		$ls_group="";
		$ls_criterio="";
		switch(substr($as_codcom,14,1))
		{
			case "A": // Aportes
				$ls_group = "GROUP BY codemp, codcom, codnom, codperi, codcomapo, descripcion, cod_pro, ced_bene, tipo_destino, ".
				            "         codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla       ";
				break;

			case "N": // Nómina
				$ls_group = "GROUP BY codemp, codcom, codnom, codperi, codcomapo, descripcion, cod_pro, ced_bene, tipo_destino, ".
				             " codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla              ";
				break;

			case "I": // Ingresos
				$ls_group = "GROUP BY codemp, codcom, codnom, codperi, codcomapo, descripcion, cod_pro, ced_bene, tipo_destino, ".
				            "  codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla              ";
				break;

			case "P": // Prestación
				$ls_group = "GROUP BY codemp, codcom, codnom, codperi, codcomapo, descripcion, cod_pro, ced_bene, tipo_destino, ".
				            " codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla               ";
				break;

			case "K": // Intereses Prestación
				$ls_group = "GROUP BY codemp, codcom, codnom, codperi, codcomapo, descripcion, cod_pro, ced_bene, tipo_destino, ".
				            " codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla               ";
				break;
				
			case "L": // Liquidacion
				$ls_group = "GROUP BY codemp, codcom, codnom, codperi, codcomapo, descripcion, cod_pro, ced_bene, tipo_destino, ".
				             " codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla              ";
				break;
				
			case "X": // Anticipos
				$ls_group = "GROUP BY codemp, codcom, codnom, codperi, codcomapo, descripcion, cod_pro, ced_bene, tipo_destino, ".
				             " codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla              ";
				break;
		}
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="CONVERT('   ' USING utf8) as operacion";
				break;
			case "MYSQLI":
				$ls_cadena="CONVERT('   ' USING utf8) as operacion";
				break;
			case "POSTGRES":
				$ls_cadena="CAST('   ' AS char(3)) as operacion";
				break;					
			case "INFORMIX":
				$ls_cadena="CAST('   ' AS char(3)) as operacion";
				break;					
		}
		$ls_sql="SELECT descripcion, cod_pro, ced_bene, tipo_destino, MAX(operacion) AS operacion, codcomapo, ".
				"		(SELECT nompro FROM rpc_proveedor ".
				"		  WHERE rpc_proveedor.codemp = sno_dt_spg.codemp ".
				"           AND rpc_proveedor.cod_pro = sno_dt_spg.cod_pro ) as nompro, ".
				"		(SELECT nombene FROM rpc_beneficiario ".
				"		  WHERE rpc_beneficiario.codemp = sno_dt_spg.codemp ".
				"           AND rpc_beneficiario.ced_bene = sno_dt_spg.ced_bene ) as nombene, ".
				"		(SELECT apebene FROM rpc_beneficiario ".
				"		  WHERE rpc_beneficiario.codemp = sno_dt_spg.codemp ".
				"           AND rpc_beneficiario.ced_bene = sno_dt_spg.ced_bene ) as apebene, ".
				"		(SELECT MAX(fechasper) FROM sno_periodo ".
				"		  WHERE sno_periodo.codemp = sno_dt_spg.codemp ".
				"           AND sno_periodo.codnom = sno_dt_spg.codnom ".
				"           AND sno_periodo.codperi = sno_dt_spg.codperi ) as fechasper, ".
				"       '-------------------------' as codestpro1, '-------------------------' as codestpro2, ".
				"       '-------------------------' as codestpro3, '-------------------------' as codestpro4, ".
				"       '-------------------------' as codestpro5, '-' as estcla      ".
				"  FROM sno_dt_spg ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND codcom='".$as_codcom."' ".
				$ls_group.
				" UNION ".
				"SELECT descripcion, cod_pro, ced_bene, tipo_destino, MAX(operacion) AS operacion, codcomapo, ".
				"		(SELECT nompro FROM rpc_proveedor ".
				"		  WHERE rpc_proveedor.codemp = sno_dt_spi.codemp ".
				"           AND rpc_proveedor.cod_pro = sno_dt_spi.cod_pro ) as nompro, ".
				"		(SELECT nombene FROM rpc_beneficiario ".
				"		  WHERE rpc_beneficiario.codemp = sno_dt_spi.codemp ".
				"           AND rpc_beneficiario.ced_bene = sno_dt_spi.ced_bene ) as nombene, ".
				"		(SELECT apebene FROM rpc_beneficiario ".
				"		  WHERE rpc_beneficiario.codemp = sno_dt_spi.codemp ".
				"           AND rpc_beneficiario.ced_bene = sno_dt_spi.ced_bene ) as apebene, ".
				"		(SELECT MAX(fechasper) FROM sno_periodo ".
				"		  WHERE sno_periodo.codemp = sno_dt_spi.codemp ".
				"           AND sno_periodo.codnom = sno_dt_spi.codnom ".
				"           AND sno_periodo.codperi = sno_dt_spi.codperi ) as fechasper, ".
				"       codestpro1,  codestpro2, codestpro3,  codestpro4, codestpro5, estcla   ".
				"  FROM sno_dt_spi ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codcom='".$as_codcom."' ".
				$ls_group.
				" UNION ".
				"SELECT descripcion, cod_pro, ced_bene, tipo_destino, ".$ls_cadena.", codcomapo, ".
				"		(SELECT nompro FROM rpc_proveedor ".
				"		  WHERE rpc_proveedor.codemp = sno_dt_scg.codemp ".
				"           AND rpc_proveedor.cod_pro = sno_dt_scg.cod_pro ) as nompro, ".
				"		(SELECT nombene FROM rpc_beneficiario ".
				"		  WHERE rpc_beneficiario.codemp = sno_dt_scg.codemp ".
				"           AND rpc_beneficiario.ced_bene = sno_dt_scg.ced_bene ) as nombene, ".
				"		(SELECT apebene FROM rpc_beneficiario ".
				"		  WHERE rpc_beneficiario.codemp = sno_dt_scg.codemp ".
				"           AND rpc_beneficiario.ced_bene = sno_dt_scg.ced_bene ) as apebene, ".
				"		(SELECT MAX(fechasper) FROM sno_periodo ".
				"		  WHERE sno_periodo.codemp = sno_dt_scg.codemp ".
				"           AND sno_periodo.codnom = sno_dt_scg.codnom ".
				"           AND sno_periodo.codperi = sno_dt_scg.codperi ) as fechasper, ".
				"       '-------------------------' as codestpro1, '-------------------------' as codestpro2, ".
				"       '-------------------------' as codestpro3, '-------------------------' as codestpro4, ".
				"       '-------------------------' as codestpro5, '-' as estcla      ".
				"  FROM sno_dt_scg ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codcom='".$as_codcom."' ".
				"   AND codcom NOT IN (SELECT codcom FROM sno_dt_spg WHERE codemp = '".$ls_codemp."' )  ".
				"   AND codcom NOT IN (SELECT codcom FROM sno_dt_spi WHERE codemp = '".$ls_codemp."' )  ".
				$ls_group;
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_criterio="";
				$ls_codcomapo=$rs_data->fields["codcomapo"];
				$ls_comprobante=$as_codcom;
				switch(substr($as_codcom,14,1))
				{
					case "A": // Aportes
						$ls_criterio = " AND codcomapo = '".$ls_codcomapo."'";
						$ls_comprobante=$ls_codcomapo;
						break;
					case "L": // Liquidacion
						$ls_criterio = " AND codcomapo = '".$ls_codcomapo."'";
						$ls_comprobante=$ls_codcomapo;
						break;
				}
				$ls_descripcion=$rs_data->fields["descripcion"];
				$ls_tipo_destino=$rs_data->fields["tipo_destino"];
				$ls_operacion=rtrim($rs_data->fields["operacion"]);
				$ld_fechasper=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fechasper"]);
				$_SESSION["fechacomprobante"]=$ld_fechasper;
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
				print "		<td><div align='right' class='texto-azul'>Fecha</div></td>";
				print "		<td><div align='justify'>".$ld_fechasper."</div></td>";
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
				//---N= Nomina, P=prestaciones, A= Aporte, K= Intereses Prestaciones, L= Liquidaciones-----////
				if((substr($as_codcom,14,1)=="N")||(substr($as_codcom,14,1)=="P")||(substr($as_codcom,14,1)=="K")||(substr($as_codcom,14,1)=="A")||(substr($as_codcom,14,1)=="L")||(substr($as_codcom,14,1)=="X"))
				{
					$ls_sql="SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, monto ".
							"  FROM sno_dt_spg ".
							" WHERE codemp='".$ls_codemp."'".
							"   AND codcom='".$as_codcom."' ".
							$ls_criterio;							
					$rs_data2=$io_sql2->select($ls_sql);
					if($rs_data2===false)
					{
						$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql2->message)); 
					}
					else
					{
						$li_len1=0;
						$li_len2=0;
						$li_len3=0;
						$li_len4=0;
						$li_len5=0;
						$ls_titulo="";
						$arrResultado=$io_fun_nomina->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
						$li_len1=$arrResultado['ai_len1'];
						$li_len2=$arrResultado['ai_len2'];
						$li_len3=$arrResultado['ai_len3'];
						$li_len4=$arrResultado['ai_len4'];
						$li_len5=$arrResultado['ai_len5'];
						$ls_titulo=$arrResultado['as_titulo'];
						print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0' class='formato-blanco'>";
						print "	<tr>";
						print "		<td colspan='5' class='titulo-celdanew'>Detalle Presupuestario</td>";
						print " </tr>";
						print " <tr class=titulo-celdanew>";
						print "		<td width='150'>".$ls_titulo."</td>";
						print "		<td width='50'>Estatus</td>";
						print "		<td width='80'>Cuenta</td>";
						print "		<td width='80'>Monto</td>";
						print "		<td width='90'>Disponibilidad</td>";
						print "	</tr>";
						$li_total=0;
						while(!$rs_data2->EOF)
						{
							$ls_cuenta=$rs_data2->fields["spg_cuenta"];
							$li_total=$li_total+$rs_data2->fields["monto"];
							$li_monto=number_format($rs_data2->fields["monto"],2,',','.');
							$ls_codestpro=$rs_data2->fields["codestpro1"].$rs_data2->fields["codestpro2"].$rs_data2->fields["codestpro3"].$rs_data2->fields["codestpro4"].$rs_data2->fields["codestpro5"];
							$ls_estcla=$rs_data2->fields["estcla"];
							$ls_programatica="";
							$ls_estatus="";
							$ls_imagen='blank.gif';
							if($as_disponibilidad==1)
							{
								$ls_imagen='ok.png';
								$li_disponibilidad=0;
								$arrResultado=uf_disponibilidad($ls_codemp,$rs_data2,$io_int_spg,$ls_imagen,$li_disponibilidad);
								$ls_imagen=$arrResultado['ls_imagen'];
								$li_disponibilidad=$arrResultado['li_disponibilidad'];
								$lb_valido=$arrResultado['lb_valido'];
								$li_disponibilidad=$io_fun_nomina->uf_formatonumerico($li_disponibilidad);
							}
							$ls_programatica=$io_fun_nomina->uf_formatoprogramatica($ls_codestpro,$ls_programatica);
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
							print "<td align=center width='150'>".$ls_programatica."</td>";
							print "<td align=center width='50'>".$ls_estatus."</td>";
							print "<td align=center width='80'>".$ls_cuenta."</td>";
							print "<td align=right width='80'>".$li_monto."  </td>";
							print "<td align=center width='90'><img src='../shared/imagebank/".$ls_imagen."'></td>";
							print "</tr>";			
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
				}
				else
				{
					$ls_sql="SELECT spi_cuenta, monto, codestpro1,  codestpro2, codestpro3,  codestpro4, codestpro5,estcla ".
							"  FROM sno_dt_spi ".
							" WHERE codemp='".$ls_codemp."'".
							"   AND codcom='".$as_codcom."' ".
							$ls_criterio;
					$rs_data2=$io_sql2->select($ls_sql);
					if($rs_data2===false)
					{
						$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql2->message)); 
					}
					else
					{
						
						$li_total=0;
						//-------------------------------------------------
						$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
						$ls_estmodest     = $_SESSION["la_empresa"]["estmodest"];
						$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
						$li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
						$li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
						$li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
						$li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
						//--------------------------------------------------
						if ($ls_estpreing==0)
						{
							print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0' class='formato-blanco'>";
							print "	<tr>";
							print "		<td colspan='4' class='titulo-celdanew'>Detalle Presupuestario de Ingreso</td>";
							print " </tr>";
							print " <tr class=titulo-celdanew>";
							print "		<td colspan='3'>Cuenta</td>";
							print "		<td width='100'>Monto</td>";
							print "	</tr>";
						 }
						 else
						 {
						 	print "<table width='450' height='20' border='0' align='center' cellpadding='0' cellspacing='0' class='formato-blanco'>";
							print "	<tr>";
							print "		<td colspan='12' class='titulo-celdanew'>Detalle Presupuestario de Ingreso</td>";
							print " </tr>";
							print " <tr class=titulo-celdanew>";
							print "		<td colspan='3'>Estructura Presupuestaria</td>";
							print "		<td colspan='3'>Estatus</td>";
							print "		<td colspan='3'>Cuenta</td>";
							print "		<td width='100'>Monto</td>";
							print "	</tr>";
						 }
						while(!$rs_data2->EOF)
						{
							$ls_cuenta=$rs_data2->fields["spi_cuenta"];
							$li_total=$li_total+$rs_data2->fields["monto"];
							$li_monto=$io_fun_nomina->uf_formatonumerico($rs_data2->fields["monto"]);
							//--------------------------------------------------------
							$ls_estcla=$rs_data2->fields["estcla"];
							switch($ls_estcla)
							{
								case "A":
									$ls_estatus="Acción";
									break;
								case "P":
									$ls_estatus="Proyecto";
									break;
							}
							$ls_codestpro1    = trim(substr(substr($rs_data2->fields["codestpro1"],0,25),-$li_loncodestpro1));
							$ls_codestpro2    = trim(substr(substr($rs_data2->fields["codestpro2"],0,25),-$li_loncodestpro2));
							$ls_codestpro3    = trim(substr(substr($rs_data2->fields["codestpro3"],0,25),-$li_loncodestpro3));
							if ($ls_estmodest==2)
							{
							  $ls_denestcla="";
							  $ls_codestpro4   = trim(substr(substr($rs_data2->fields["codestpro4"],0,25),-$li_loncodestpro4));
							  $ls_codestpro5   = trim(substr(substr($rs_data2->fields["codestpro5"],0,25),-$li_loncodestpro5));
							  $ls_programatica = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3."-".$ls_codestpro4."-".$ls_codestpro5;			
							  }
							  else
							  {
								$ls_programatica = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3;			
							  }
							if ($ls_estpreing==0)
							{
								print "<tr class=celdas-blancas>";
								print "<td align=center colspan='3'>".$ls_cuenta."</td>";
								print "<td align=right width='100'>".$li_monto."  </td>";
								print "</tr>";
							}
							else
							{
								print "<tr class=celdas-blancas>";
								print "<td align=center colspan='3'>".$ls_programatica."</td>";
								print "<td align=center colspan='3'>".$ls_estatus."</td>";
								print "<td align=center colspan='3'>".$ls_cuenta."</td>";
								print "<td align=right width='100'>".$li_monto."  </td>";
								print "</tr>";
							}			
							$rs_data2->MoveNext();	
						}
						$li_total=$io_fun_nomina->uf_formatonumerico($li_total);
						if ($ls_estpreing==0)
						{
							print "	<tr class=celdas-blancas>";
							print "		<td colspan='3' align='right' class='texto-azul'>Total</td>";
							print "		<td width='100' align='right' class='texto-azul'>".$li_total."</td>";
							print " </tr>";
							print "</table>";
						}
						else
						{
							print "	<tr class=celdas-blancas>";
							print "		<td colspan='9' align='right' class='texto-azul'>Total</td>";
							print "		<td width='100' align='right' class='texto-azul'>".$li_total."</td>";
							print " </tr>";
							print "</table>";						
						}
					}
				}
				$io_sql2->free_result($rs_data2);	
				$ls_sql="SELECT sc_cuenta, debhab, monto, codnom ".
						"  FROM sno_dt_scg ".
						" WHERE codemp='".$ls_codemp."'".
						"   AND codcom='".$as_codcom."' ".
						$ls_criterio.
						" ORDER BY  debhab ";
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
/////////////////////////////////////////////////////////////////////////////////////////////////////////BUSQUEDA DE CTA BENEF./ PROV CESTA TIKET
					$ls_nomina=$rs_data2->fields["codnom"];
					$ls_sql_cesta=  "SELECT espnom, ctnom, codpronom, codbennom ".
									"  FROM sno_nomina ".
									" WHERE codemp='".$ls_codemp."'".
									"   AND codnom='".$ls_nomina."' ";
					
					$rs_data_cesta=$io_sql2->select($ls_sql_cesta);
					if($rs_data_cesta===false)
					{
						$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql2->message)); 
					}
					else
					{
						while(!$rs_data_cesta->EOF)
						{
							$ls_espnom=$rs_data_cesta->fields["espnom"];
							$ls_ctnom=$rs_data_cesta->fields["ctnom"];
							$ls_codpronom=$rs_data_cesta->fields["codpronom"];
							$ls_codbennom=$rs_data_cesta->fields["codbennom"];
							if (($ls_espnom==1)&&($ls_ctnom==1))
							{
								if($ls_codpronom!='----------')
								{
									$as_benprov='P';
									$as_codprovben_cest=$ls_codpronom;
								}
								else
								{
									$as_benprov='B';
									$as_codprovben_cest=$ls_codbennom;
								}
								$ls_cta_cestatikt=$io_fun_nomina->uf_select_provbene_ctacestatik($as_codprovben_cest,$as_benprov);
							}
														
							$rs_data_cesta->MoveNext();	
						}
					}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					if (($ls_espnom==1)&&($ls_ctnom==1))
					{
						while(!$rs_data2->EOF)
						{
							$ls_cuenta=$rs_data2->fields["sc_cuenta"];
							$li_monto=$rs_data2->fields["monto"];
							$ls_debhab=$rs_data2->fields["debhab"];
							switch($ls_debhab)
							{
								case "D":
									$li_debe=$li_monto;
									$li_debe=$io_fun_nomina->uf_formatonumerico($li_debe);
									$li_haber="0,00";
									$li_total_deb=$li_total_deb+$li_monto;
									break;
								case "H":
									$li_debe="0,00";
									$li_haber=$li_monto;
									$li_haber=$io_fun_nomina->uf_formatonumerico($li_haber);
									$li_total_hab=$li_total_hab+$li_monto;
									break;
							}
							if ($ls_debhab=="D")
							{
								print "<tr class=celdas-blancas>";
								print "<td align=center width='100'>".$ls_cuenta."</td>";
								print "<td align=right width='100'>".$li_debe."</td>";
								print "<td align=right width='100'>".$li_haber."</td>";
								print "</tr>";
							}			
							$rs_data2->MoveNext();	
						}
						$li_debe="0,00";
						$li_haber=$li_monto;
						$li_haber=$io_fun_nomina->uf_formatonumerico($li_haber);
						$li_total_deb_ct=$io_fun_nomina->uf_formatonumerico($li_total_hab);
						print "<tr class=celdas-blancas>";
						print "<td align=center width='100'>".$ls_cta_cestatikt."</td>";
						print "<td align=right width='100'>".$li_debe."</td>";
						print "<td align=right width='100'>".$li_total_deb_ct."</td>";
						print "</tr>";	
					}
					else
					{
						while(!$rs_data2->EOF)
						{
							$ls_cuenta=$rs_data2->fields["sc_cuenta"];
							$li_monto=$rs_data2->fields["monto"];
							$ls_debhab=$rs_data2->fields["debhab"];
							
							switch($ls_debhab)
							{
								case "D":
									$li_debe=$li_monto;
									$li_debe=$io_fun_nomina->uf_formatonumerico($li_debe);
									$li_haber="0,00";
									$li_total_deb=$li_total_deb+$li_monto;
									break;
								case "H":
									$li_debe="0,00";
									$li_haber=$li_monto;
									$li_haber=$io_fun_nomina->uf_formatonumerico($li_haber);
									$li_total_hab=$li_total_hab+$li_monto;
									break;
							}
							print "<tr class=celdas-blancas>";
							print "<td align=center width='100'>".$ls_cuenta."</td>";
							print "<td align=right width='100'>".$li_debe."</td>";
							print "<td align=right width='100'>".$li_haber."</td>";
							print "</tr>";			
							$rs_data2->MoveNext();	
						}
					}
					$li_total_deb=$io_fun_nomina->uf_formatonumerico($li_total_deb);
					$li_total_hab=$io_fun_nomina->uf_formatonumerico($li_total_hab);
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

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_disponibilidad($ls_codemp,$rs_data2,$io_int_spg,$ls_imagen,$li_disponibilidad)
    { 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_disponibilidad
		//		   Access: public 
		//       Argument: uf_disponibilidad //  data
		//	  Description: busca la disponibilidad presupuiestaria 
		//	      Returns: mensaje
		//	   Creado Por: Ing. Yesenia Moreno
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_cuenta=$rs_data2->fields["spg_cuenta"];
		$ls_estprog[0]=$rs_data2->fields["codestpro1"];
		$ls_estprog[1]=$rs_data2->fields["codestpro2"];
		$ls_estprog[2]=$rs_data2->fields["codestpro3"];
		$ls_estprog[3]=$rs_data2->fields["codestpro4"];
		$ls_estprog[4]=$rs_data2->fields["codestpro5"];
		$ls_estprog[5]=$rs_data2->fields["estcla"];
		$la_empresa=$_SESSION["la_empresa"];
		$ls_vali_nivel=$la_empresa["vali_nivel"];
		if($ls_vali_nivel==5)
		{
			$ls_formpre=str_replace("-","",$la_empresa["formpre"]);
			$ls_vali_nivel=$io_int_spg->uf_spg_obtener_nivel($ls_formpre);
		}
		if($_SESSION["la_empresa"]["estvaldis"]==0)
		{
			$ls_vali_nivel=0;
		}
		$lb_valido=true;
		$li_nivel=$io_int_spg->uf_spg_obtener_nivel($ls_cuenta);
		if ($li_nivel <= $ls_vali_nivel)
		{
			$ls_status="";
			$li_asignado=0;
			$li_aumento=0;
			$li_disminucion=0;
			$li_precomprometido=0;
			$li_comprometido=0;
			$li_causado=0;
			$li_pagado=0;
			$arrResultado=$io_int_spg->uf_spg_saldo_select($ls_codemp,$ls_estprog,$ls_cuenta,$ls_status,$li_asignado,$li_aumento,$li_disminucion,
											 $li_precomprometido,$li_comprometido,$li_causado,$li_pagado,'ACTUAL');
			$ls_status = $arrResultado['as_status'];
			$li_asignado = $arrResultado['adec_asignado'];
			$li_aumento = $arrResultado['adec_aumento'];
			$li_disminucion = $arrResultado['adec_disminucion'];
			$li_precomprometido = $arrResultado['adec_precomprometido'];
			$li_comprometido = $arrResultado['adec_comprometido'];
			$li_causado = $arrResultado['adec_causado'];
			$li_pagado = $arrResultado['adec_pagado'];
			$li_disponibilidad=(($li_asignado + $li_aumento) - ( $li_disminucion + $li_comprometido + $li_precomprometido));
			if(round($rs_data2->fields["monto"],2) >= round($li_disponibilidad,2))
			{
				$ls_imagen='failed.png';
				$lb_valido=false;
			}
			if($lb_valido)
			{
				$ls_status="";
				$li_asignado=0;
				$li_aumento=0;
				$li_disminucion=0;
				$li_precomprometido=0;
				$li_comprometido=0;
				$li_causado=0;
				$li_pagado=0;
				$arrResultado=$io_int_spg->uf_spg_saldo_select($ls_codemp,$ls_estprog,$ls_cuenta,$ls_status,$li_asignado,$li_aumento,$li_disminucion,
												 				$li_precomprometido,$li_comprometido,$li_causado,$li_pagado,'COMPROBANTE');
				$ls_status = $arrResultado['as_status'];
				$li_asignado = $arrResultado['adec_asignado'];
				$li_aumento = $arrResultado['adec_aumento'];
				$li_disminucion = $arrResultado['adec_disminucion'];
				$li_precomprometido = $arrResultado['adec_precomprometido'];
				$li_comprometido = $arrResultado['adec_comprometido'];
				$li_causado = $arrResultado['adec_causado'];
				$li_pagado = $arrResultado['adec_pagado'];
				$li_disponibilidad=(($li_asignado + $li_aumento) - ( $li_disminucion + $li_comprometido + $li_precomprometido));
				if(round($rs_data2->fields["monto"],2) >= round($li_disponibilidad,2))
				{
					$ls_imagen='failed.png';
					$lb_valido=false;
				}
			}				
		}
		$arrResultado['ls_imagen']=$ls_imagen;
		$arrResultado['li_disponibilidad']=$li_disponibilidad;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	} // end function uf_show_error_disponible
	//-----------------------------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
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
<title>Detalle Comprobante</title>
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
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_codcom=$io_fun_nomina->uf_obtenervalor_get("codcom","");
	$ls_disponibilidad=$io_fun_nomina->uf_obtenervalor_get("disponibilidad","0");
	uf_imprimirresultados($ls_codcom,$ls_disponibilidad);
?>
</div>
</form>
</body>
</html>