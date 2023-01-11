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

   //--------------------------------------------------------------
   function uf_print($as_codigo, $as_denominacion, $as_quincena, $as_tipo)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_codigo  // Código de la nómina
		//				   as_denominacion  // Denominación de la nómina
		//				   as_tipo  // Verifica de donde se está llamando el catálogo
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		require_once("sigesp_sno.php");
		$io_sno = new sigesp_sno();
		require_once("sigesp_snorh_c_nominas.php");
		$io_nomina=new sigesp_snorh_c_nominas();
                $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=100>Código</td>";
		print "<td width=400>Denominación</td>";
		print "</tr>";
		$ls_criterio="";
		if (($as_quincena!=3)&&($as_quincena!=4)) // 3 -> nominas Mensuales
		{
			$ls_criterio=" AND (adenom='1' OR divcon='1' OR divcon='0')";
		}
		$ls_sql= "SELECT sno_nomina.codemp, codnom, desnom, tippernom, despernom, anocurnom, fecininom, peractnom, numpernom, tipnom, pasape, ".
			 "		 subnom, racnom, adenom, espnom, ctnom, ctmetnom, diabonvacnom, diareivacnom, diainivacnom, diatopvacnom, ".
			 "		 diaincvacnom, consulnom, descomnom, codpronom, codbennom, conaponom, cueconnom, notdebnom, numvounom, codclanom, codclaapo, codclafid, codclaper, codclacau, ".
			 "		 perresnom, recdocnom, tipdocnom, recdocapo, tipdocapo, conpernom, titrepnom, codorgcestic, informa, racobrnom,cestiksuel,codfuefin, ".
			 "		 (SELECT count(codperi) FROM sno_periodo ".
                         " 		   WHERE codemp='".$ls_codemp."' ".
			 "   		 AND codnom='".$as_codigo."' ".
			 "   		 AND cerper=1) AS total, ".
			 "		 (SELECT denfuefin FROM sigesp_fuentefinanciamiento ".
                         " 		   WHERE codemp=sno_nomina.codemp ".
			 "   		 AND codfuefin=sno_nomina.codfuefin) AS denfuefin, ".
			 "		 (SELECT substr(cast(fecdesper as char(10)),6,2) FROM sno_periodo ".
                         " 		   WHERE codemp=sno_nomina.codemp ".
			 "   		 AND codnom=sno_nomina.codnom ".
			 "   		 AND codperi=sno_nomina.peractnom) AS mesactual ".
			 "  FROM sno_nomina, sss_permisos_internos ".
			 "  WHERE sno_nomina.codemp='".$ls_codemp."' ".
			 "    AND codnom like '".$as_codigo."' ".
			 "    AND desnom like '".$as_denominacion."' ".$ls_criterio.
			 "    AND sss_permisos_internos.codsis='SNO'".
			 "    AND sss_permisos_internos.enabled=1".
			 "    AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'".
			 "    AND sno_nomina.codemp = sss_permisos_internos.codemp ".
			 "    AND sno_nomina.codnom = sss_permisos_internos.codintper ";
		if(($as_tipo=="importar")||($as_tipo=="transferir"))
		{
			$ls_codnom="0000";
			if(array_key_exists("la_nomina",$_SESSION))
			{
				if(array_key_exists("codnom",$_SESSION["la_nomina"]))
				{
					$ls_codnom=$_SESSION["la_nomina"]["codnom"];
				}
			}
			$ls_sql=$ls_sql." AND codnom<>'".$ls_codnom."'";
		}
		if(($as_tipo=="repcesticdes")||($as_tipo=="repcestichas"))
		{
			$ls_sql=$ls_sql." AND espnom='1'".
							" AND ctnom='1' ";
		}
		if(($as_tipo=="repperipsdes")||($as_tipo=="repperipshas"))
		{
			$ls_sql=$ls_sql." AND espnom='0'";
		}
		if($as_tipo=="HISTORICO")
		{
			$ls_codnom=$_SESSION["la_nomina"]["codnom"];
			$ls_sql= "SELECT sno_thnomina. codemp, codnom, desnom, tippernom, despernom, anocurnom, fecininom, peractnom, numpernom, tipnom, ".
				 "		 subnom, racnom, adenom, espnom, ctnom, ctmetnom, diabonvacnom, diareivacnom, diainivacnom, diatopvacnom, ".
				 "		 diaincvacnom, consulnom, descomnom, codpronom, codbennom, conaponom, cueconnom, notdebnom, numvounom,  ".
				 "		 perresnom, recdocnom, tipdocnom, recdocapo, tipdocapo, 0 AS total, conpernom, titrepnom,  racobrnom, ".
				 "       codorgcestic, informa, codclanom, codclaapo, codclafid, codclaper, codclacau ".
				 "  FROM sno_thnomina, sss_permisos_internos   ".
				 "  WHERE sno_thnomina.codemp='".$ls_codemp."' ".
				 "    AND anocurnom='".$_SESSION["la_nomina"]["anocurnom"]."'".
				 "    AND peractnom='".$_SESSION["la_nomina"]["peractnom"]."'".
				 "    AND codnom like '".$ls_codnom."' ".
				 "    AND desnom like '".$as_denominacion."' ";
                                 "    AND sss_permisos_internos.codsis='SNO'".
                                 "    AND sss_permisos_internos.enabled=1".
                                 "    AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'".
                                 "    AND sno_thnomina.codemp = sss_permisos_internos.codemp ".
                                 "    AND sno_thnomina.codnom = sss_permisos_internos.codintper ";
		}
		$ls_sql=$ls_sql." ORDER BY codnom ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{

        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message));
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codnom=$rs_data->fields["codnom"];
				$ls_denominacion=$rs_data->fields["desnom"];
				$li_cmbtipoperiodo=$rs_data->fields["tippernom"];
				$ls_despernom=$rs_data->fields["despernom"];
				$ls_anocurnom=$rs_data->fields["anocurnom"];
				$fecininom=$rs_data->fields["fecininom"];
				$ldt_fecininom=$io_funciones->uf_convertirfecmostrar($fecininom);
				$ls_peractnom=$rs_data->fields["peractnom"];
				$li_cmbtipnom=$rs_data->fields["tipnom"];
				$ls_cmbmet=$rs_data->fields["ctmetnom"];
				$ls_diabonvacnom=$rs_data->fields["diabonvacnom"];
				$ls_diainivacnom=$rs_data->fields["diainivacnom"];
				$ls_diareivacnom=$rs_data->fields["diareivacnom"];
				$ls_diatopvacnom=$rs_data->fields["diatopvacnom"];
				$ls_diaincvacnom=$rs_data->fields["diaincvacnom"];
				$ls_subnom=$rs_data->fields["subnom"];
				$ls_adenom=$rs_data->fields["adenom"];
				$ls_racnom=$rs_data->fields["racnom"];
				$ls_racobrnom=$rs_data->fields["racobrnom"];
				$ls_espnom=$rs_data->fields["espnom"];
				$ls_ctnom=$rs_data->fields["ctnom"];
				$ls_cueconnom=$rs_data->fields["cueconnom"];
				$ls_denom="";
				$ls_notdebnom=$rs_data->fields["notdebnom"];
				$ls_numvounom=$rs_data->fields["numvounom"];
				$ls_consulnom=rtrim($rs_data->fields["consulnom"]);
				$ls_codpronom=$rs_data->fields["codpronom"];
				$ls_nomprov="";
				$ls_codbennom=$rs_data->fields["codbennom"];
				$ls_cmbconaponom=rtrim($rs_data->fields["conaponom"]);
				$ls_recdocnom=$rs_data->fields["recdocnom"];
				$ls_tipdocnom=$rs_data->fields["tipdocnom"];
				$ls_recdocapo=$rs_data->fields["recdocapo"];
				$ls_tipdocapo=$rs_data->fields["tipdocapo"];
				$ls_descomnom=$rs_data->fields["descomnom"];
				$ls_nombene="";
				$ai_total=$rs_data->fields["total"];
				$ls_conpernom=$rs_data->fields["conpernom"];
				$ls_titrepnom=$rs_data->fields["titrepnom"];
				$ls_codorgcestic=$rs_data->fields["codorgcestic"];
				$li_conta_global=$io_sno->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I");
				$ls_informa=$rs_data->fields["informa"];
				$ls_tiksuel=$rs_data->fields["cestiksuel"];
				$ls_codfuefin=$rs_data->fields["codfuefin"];
				$ls_denfuefin=$rs_data->fields["denfuefin"];
				$ls_codclanom=$rs_data->fields["codclanom"]; 
				$ls_codclaapo=$rs_data->fields["codclaapo"]; 
				$ls_codclafid=$rs_data->fields["codclafid"]; 
				$ls_codclaper=$rs_data->fields["codclaper"]; 
				$ls_codclacau=$rs_data->fields["codclacau"];
                                $ls_pasape=$rs_data->fields["pasape"];
				switch ($as_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codnom','$ls_denominacion','$li_cmbtipoperiodo','$ls_despernom',
						'$ls_anocurnom','$ldt_fecininom','$ls_peractnom','$li_cmbtipnom','$ls_cmbmet','$ls_diabonvacnom','$ls_diainivacnom',
						'$ls_diareivacnom','$ls_diatopvacnom','$ls_diaincvacnom','$ls_subnom','$ls_adenom','$ls_racnom','$ls_espnom','$ls_ctnom',
						'$ls_cueconnom','$ls_notdebnom','$ls_numvounom','$ls_consulnom','$ls_codpronom','$ls_codbennom','$ls_denom','$ls_nomprov',
						'$ls_nombene','$ai_total','$ls_cmbconaponom','$li_conta_global','$ls_recdocnom','$ls_tipdocnom','$ls_recdocapo','$ls_tipdocapo',
						'$ls_descomnom','$ls_conpernom','$ls_titrepnom','$ls_codorgcestic','$ls_informa','$ls_racobrnom','$ls_tiksuel','$ls_codfuefin','$ls_denfuefin','$ls_pasape');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
						
					case "importar":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarimportar('$ls_codnom','$ls_denominacion');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "transferirdesde":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptartransferirdesde('$ls_codnom','$ls_denominacion');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "transferirhasta":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptartransferirhasta('$ls_codnom','$ls_denominacion');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "replispreant":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplispreant('$ls_codnom','$ls_denominacion');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "repconconapo":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepconconapo('$ls_codnom','$ls_denominacion');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
						
					case "replispreantdesde":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplispreantdesde('$ls_codnom','$ls_denominacion');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "replispreanthasta":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplispreanthasta('$ls_codnom','$ls_denominacion');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "repconttrab":
						if ($rs_data->fields["peractnom"]=='000')
						{
							$ls_mesactual="13";
						}
						else
						{
							$ls_mesactual=$rs_data->fields["mesactual"];
						}
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepconttrab('$ls_codnom','$ls_denominacion','$ls_racnom','$ls_mesactual','$ls_anocurnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
					
					case "repapopatdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepapopatdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
						
					case "replisperracrecdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisperracrecdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;	
					
					case "replisperracrechas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisperracrechas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;	
						
					case "repgesmindes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepmintdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;	
	
					case "repapopathas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepapopathas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
					
					case "repgesminhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepminthas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
					
					case "repapoipasdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepapoipasdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "repapoipashas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepapoipashas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "repipascobdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepipascobdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "repipascobhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepipascobhas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "repretislrdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepretislrdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "repretislrhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepretislrhas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "repretarcdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepretarcdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "repretarchas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepretarchas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "repcesticdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepcesticdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "repcestichas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepcestichas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "replisfamdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisfamdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "replisfamhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisfamhas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "repconcdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepconcdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "repconchas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepconchas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "replisperdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisperdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "replisperhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisperhas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "repliscumdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepliscumdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "repliscumhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepliscumhas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
						
					case "HISTORICO":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarhistorico('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "repficperdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepficperdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "repficperhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepficperhas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "replisbandes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisbandes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "replisbanhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisbanhas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
					
					case "repdepbandes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepdepbandes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "repdepbanhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepdepbanhas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "replisantdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisantdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "replisanthas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisanthas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "reprecpagcon":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreprecpagcon('$ls_codnom','$ls_denominacion','$li_cmbtipnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
					
					case "relacioncargorac":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarcargorac('$ls_codnom','$ls_denominacion','$li_cmbtipnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
						
					case "repperipsdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepperipsdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "repperipshas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepperipshas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
						
					case "pagounides":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreppagounides('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "pagounihas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreppagounihas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
						
					case "transferir":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptartransferir('$ls_codnom','$ls_denominacion');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "replispreantint":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplispreantint('$ls_codnom','$ls_denominacion');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "prestacionantiguedadant":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarprestacionantiguedadant('$ls_codnom','$ls_denominacion');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "repdedplandes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepdedplandes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;

					case "repdedplanhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepdedplanhas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
						
					case "reptipperdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreptipperdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "reptipperhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepperipshas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
						
					case "resnomdes":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarresnomdes('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
	
					case "resnomhas":
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarresnomhas('$ls_codnom');\">".$ls_codnom."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "</tr>";			
						break;
				}
				$rs_data->MoveNext();
			}
			$io_sql->free_result($rs_data);
		}
		print "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($io_sno);
		unset($io_nomina);
		unset($ls_codemp);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Nomina</title>
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
<style type="text/css">
<!--
.Estilo1 {font-size: 11px}
a:hover {
	color: #006699;
}
-->
</style>
</head>

<body>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
</p>
  	 <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    	<tr>
     	 	<td width="500" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Nomina</td>
    	</tr>
	 </table>
	 <br>
	 <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="111" height="22"><div align="right">Codigo</div></td>
        <td width="451"><div align="left">
          <input name="codigo" type="text" id="codigo" onKeyPress="javascript: ue_mostrar(this,event);">        
          <input name="txtcodigo" type="hidden" id="txtcodigo">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Denominacion</div></td>
        <td><div align="left">
          <input name="denominacion" type="text" id="denominacion" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
	<br>
    <?php
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	$ls_codded=$io_fun_nomina->uf_obtenervalor_get("codded","");	
	if($ls_operacion=="BUSCAR")
	{
		$ls_codigo="%".$_POST["codigo"]."%";
		$ls_denominacion="%".$_POST["denominacion"]."%";
		$ls_quincena=$io_fun_nomina->uf_obtenervalor_get("quincena","1");
		uf_print($ls_codigo, $ls_denominacion, $ls_quincena, $ls_tipo);
	}
	else
	{
		$ls_codigo="%%";
		$ls_denominacion="%%"; 
		$ls_quincena=$io_fun_nomina->uf_obtenervalor_get("quincena","3");// 3 -> nominas Mensuales
		uf_print($ls_codigo, $ls_denominacion, $ls_quincena, $ls_tipo);
	}
	unset($io_fun_nomina);
?>

</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
  function aceptar(codnom,denominacion,cmbtipoperiodo,despernom,anocurnom,fecininom,peractnom,cmbtipnom,cmbmet,diabonvacnom,diainivacnom,
			        diareivacnom,diatopvacnom,diaincvacnom,subnom,adenom,racnom,espnom,ctnom,cueconnom,notdebnom,numvounom,consulnom,
					codpronom,codbennom,ls_denom,ls_nomprov,ls_nombene,ai_total,conaponom,li_conta_global,ls_recdocnom,ls_tipdocnom,ls_recdocapo,ls_tipdocapo,
					descomnom,conpernom,titrepnom,codorgcestic,informa,racobrnom,ls_tiksuel,ls_codfuefin,ls_denfuefin,ls_pasape)
  {
     opener.document.form1.txtcodnom.value=codnom;
	 opener.document.form1.txtcodnom.readOnly=true;
	 opener.document.form1.existe.value="TRUE";
     opener.document.form1.txtdesnom.value=denominacion;
	 opener.document.form1.txtdesnom.disabled=true;
	 opener.document.form1.cmbtippernom.value=cmbtipoperiodo;
	 opener.document.form1.cmbtippernom.disabled=true;
	 opener.document.form1.txtdespernom.value=despernom;
	 opener.document.form1.txtdespernom.disabled=true;
	 opener.document.form1.txtanocurnom.value=anocurnom;
	 opener.document.form1.txtanocurnom.disabled=true;
	 opener.document.form1.txtfecininom.value=fecininom;
	 opener.document.form1.txtfecininom.disabled=true;
	 opener.document.form1.txtperactnom.value=peractnom;
	 opener.document.form1.txtperactnom.disabled=true;
	 opener.document.form1.txttitrepnom.value=titrepnom;
	 opener.document.form1.cmbtipnom.value=cmbtipnom;
	 opener.document.form1.cmbtipnom.disabled=true;
	 opener.document.form1.botsubnom.disabled=true;
	 opener.document.form1.chksubnom.disabled=true;
	 opener.document.form1.chksubnom.checked=false;
     opener.document.form1.chkadenom.checked=false;
	 opener.document.form1.chkadenom.disabled=true;
     opener.document.form1.chkracnom.checked=false;
	 opener.document.form1.chkracnom.disabled=true;
     opener.document.form1.chkracobrnom.checked=false;
	 opener.document.form1.chkracobrnom.disabled=true;
	 opener.document.form1.chkespnom.checked=false;
	 opener.document.form1.chkespnom.disabled=true;
     opener.document.form1.chkctnom.checked=false;
	 opener.document.form1.chkctnom.disabled=true;
     opener.document.form1.chkconpronom.checked=false;
	 opener.document.form1.chkconpronom.disabled=true;
     opener.document.form1.chkconpernom.checked=false;
	 opener.document.form1.txtctmetnom.value="";
	 opener.document.form1.botsubnom.disabled=true;
  	 opener.document.form1.botestper.disabled=true;
	 opener.document.form1.txtcodorgcestic.value=codorgcestic;
	 opener.document.form1.txtinforma.value=informa;
	 opener.document.form1.txtftefinanciamiento.value=ls_codfuefin;
     opener.document.form1.txtdenftefinanciamiento.value=ls_denfuefin;
     opener.document.form1.chkpasape.checked=false;
	 if(ai_total==0)
	 {
	 	opener.document.form1.botestper.disabled=false;
	 }
     if(subnom==1) 
	 {
        opener.document.form1.chksubnom.checked=true;
		opener.document.form1.botsubnom.disabled=false;
	 }
	 if(adenom==1)
	 {
	    opener.document.form1.chkadenom.checked=true;
	 }
	 if(racnom==1)
	 {
	    opener.document.form1.chkracnom.checked=true;
	 }
	 if(racobrnom==1)
	 {
	    opener.document.form1.chkracobrnom.checked=true;
	 }
	 if(espnom==1)
	 {
        opener.document.form1.chkespnom.checked=true;
	 }
	 if(ls_tiksuel==1)
	 {
		opener.document.form1.chkticksuel.checked=true;
	 }
	 if(conpernom==1)
	 {
        opener.document.form1.chkconpernom.checked=true;
	 }
	 if(ctnom==1)
	 {
        opener.document.form1.chkctnom.checked=true;
		opener.document.form1.txtctmetnom.value=cmbmet;
	 }
	 if(ls_pasape==1)
	 {
            opener.document.form1.chkpasape.checked=true;
	 }
	 opener.document.form1.operacion.value="BUSCAR";
	 opener.document.form1.action="sigesp_snorh_d_nominas.php";
	 opener.document.form1.submit();
	 close();
  }

 function aceptarimportar(codnom,desnom)
 {
     opener.document.form1.txtcodnombus.value=codnom;
	 opener.document.form1.txtcodnombus.readOnly=true;
     opener.document.form1.txtdesnombus.value=desnom;
	 opener.document.form1.txtdesnombus.readOnly=true;
	 opener.document.form1.operacion.value="BUSCAR";
	 opener.document.form1.action="sigesp_sno_p_importardefiniciones.php";
	 opener.document.form1.submit();
	 close();
 }

 function aceptartransferirdesde(codnom,desnom)
 {
     opener.document.form1.txtcodnomdes.value=codnom;
	 opener.document.form1.txtcodnomdes.readOnly=true;
     opener.document.form1.txtdesnomdes.value=desnom;
	 opener.document.form1.txtdesnomdes.readOnly=true;
     opener.document.form1.txtcodnomhas.value="";
     opener.document.form1.txtdesnomhas.value="";
	 close();
 }

 function aceptartransferirhasta(codnom,desnom)
 {
 	if(opener.document.form1.txtcodnomdes.value==codnom)
	{
		alert("La nómina hasta no puede ser igual a la nómina desde.");
	}
	else
	{
		 opener.document.form1.txtcodnomhas.value=codnom;
		 opener.document.form1.txtcodnomhas.readOnly=true;
		 opener.document.form1.txtdesnomhas.value=desnom;
		 opener.document.form1.txtdesnomhas.readOnly=true;
		 close();
	}
 }

function aceptarrepapopatdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	opener.document.form1.txtcodsubnomdes.value="";
	opener.document.form1.txtcodsubnomhas.value="";
	opener.document.form1.txtcodconc.value="";
	opener.document.form1.txtnomcon.value="";
	opener.document.form1.txtanocurperdes.value="";
	opener.document.form1.txtmescurperdes.value="";
	opener.document.form1.txtanocurperhas.value="";
	opener.document.form1.txtmescurperhas.value="";
	close();
}

function aceptarreplisperracrecdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	/*opener.document.form1.txtanocurperdes.value="";
	opener.document.form1.txtmescurperdes.value="";
	opener.document.form1.txtanocurperhas.value="";
	opener.document.form1.txtmescurperhas.value="";*/
	close();
}

function aceptarreplisperracrechas(codnom)
{
	opener.document.form1.txtcodnomhas.value=codnom;
	opener.document.form1.txtcodnomhas.readOnly=true;
	/*opener.document.form1.txtanocurperdes.value="";
	opener.document.form1.txtmescurperdes.value="";
	opener.document.form1.txtanocurperhas.value="";
	opener.document.form1.txtmescurperhas.value="";*/
	close();
}


function aceptarrepmintdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	opener.document.form1.txtcodsubnomdes.value="";
	opener.document.form1.txtcodsubnomhas.value="";
	//opener.document.form1.txtcodconc.value="";
	//opener.document.form1.txtnomcon.value="";
	opener.document.form1.txtanocurperdes.value="";
	opener.document.form1.txtmescurperdes.value="";
	opener.document.form1.txtanocurperhas.value="";
	opener.document.form1.txtmescurperhas.value="";
	close();
}
function aceptarrepminthas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}


function aceptarrepapopathas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarreplispreant(codnom,desnom)
{
	opener.document.form1.txtcodnom.value=codnom;
	opener.document.form1.txtcodnom.readOnly=true;
	opener.document.form1.txtdesnom.value=desnom;
	opener.document.form1.txtdesnom.readOnly=true;
	close();
}

function aceptarrepconconapo(codnom,desnom)
{
	opener.document.form1.txtcodnom.value=codnom;
	opener.document.form1.txtcodnom.readOnly=true;
	opener.document.form1.txtdesnom.value=desnom;
	opener.document.form1.txtdesnom.readOnly=true;
	close();
}

function aceptarreplispreantdesde(codnom,desnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtdesnomdes.value=desnom;
	opener.document.form1.txtdesnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value=codnom;
	opener.document.form1.txtcodnomhas.readOnly=true;
	opener.document.form1.txtdesnomhas.value=desnom;
	opener.document.form1.txtdesnomhas.readOnly=true;
	close();
}

function aceptarreplispreanthasta(codnom,desnom)
{
	opener.document.form1.txtcodnomhas.value=codnom;
	opener.document.form1.txtcodnomhas.readOnly=true;
	opener.document.form1.txtdesnomhas.value=desnom;
	opener.document.form1.txtdesnomhas.readOnly=true;
	close();
}

function aceptarrepconttrab(codnom,desnom,racnom,mesactual,anocurnom)
{
	opener.document.form1.txtcodnom.value=codnom;
	opener.document.form1.txtcodnom.readOnly=true;
	opener.document.form1.txtdesnom.value=desnom;
	opener.document.form1.txtdesnom.readOnly=true;
	opener.document.form1.txtrac.value=racnom;
	opener.document.form1.txtmesactual.value=mesactual;
	opener.document.form1.txtanocurnom.value=anocurnom;
	close();
}

function aceptarrepapoipasdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	opener.document.form1.txtanocurper.value="";
	opener.document.form1.txtmescurper.value="";
	opener.document.form1.txtdesmesper.value="";
	close();
}

function aceptarrepapoipashas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarrepipascobdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	opener.document.form1.txtanocurper.value="";
	opener.document.form1.txtmescurper.value="";
	opener.document.form1.txtdesmesper.value="";
	close();
}

function aceptarrepipascobhas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarrepretislrdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	opener.document.form1.txtanocurper.value="";
	opener.document.form1.txtmescurper.value="";
	opener.document.form1.txtdesmesper.value="";
	close();
}

function aceptarrepretislrhas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarrepretarcdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	opener.document.form1.txtanocurper.value="";
	close();
}

function aceptarrepretarchas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

 function aceptartransferirhasta(codnom,desnom)
 {
 	if(opener.document.form1.txtcodnomdes.value==codnom)
	{
		alert("La nómina hasta no puede ser igual a la nómina desde.");
	}
	else
	{
		 opener.document.form1.txtcodnomhas.value=codnom;
		 opener.document.form1.txtcodnomhas.readOnly=true;
		 opener.document.form1.txtdesnomhas.value=desnom;
		 opener.document.form1.txtdesnomhas.readOnly=true;
		 close();
	}
 }
 
function aceptarrepcesticdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	opener.document.form1.txtcodconcdes.value="";
	opener.document.form1.txtcodconchas.value="";
	opener.document.form1.txtanocurper.value="";
	opener.document.form1.txtmescurper.value="";
	opener.document.form1.txtdesmesper.value="";
	opener.document.form1.txtcodsubnomdes.value="";
	opener.document.form1.txtcodsubnomhas.value="";
	close();
}

function aceptarrepcestichas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarreplisfamdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	close();
}

function aceptarreplisfamhas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarrepconcdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	opener.document.form1.txtcodsubnomdes.value="";
	opener.document.form1.txtcodsubnomhas.value="";
	close();
}

function aceptarrepconchas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarreplisperdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	close();
}

function aceptarreplisperhas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarrepliscumdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	close();
}

function aceptarrepliscumhas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarhistorico(codnom)
{
	opener.document.form1.txtcodnom.value=codnom;
	opener.document.form1.txtcodnom.readOnly=true;
	opener.document.form1.operacion.value="BUSCAR";
	opener.document.form1.action="sigesp_sno_p_hmodificarnomina.php";
	opener.document.form1.submit();
	close();
}

function aceptarrepficperdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	close();
}

function aceptarrepficperhas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarreplisbandes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value=codnom;
	opener.document.form1.txtcodsubnomdes.value="";
	opener.document.form1.txtcodsubnomhas.value="";
	close();
}

function aceptarreplisbanhas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarrepdepbandes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value=codnom;
	close();
}

function aceptarrepdepbanhas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarreplisantdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	close();
}

function aceptarreplisanthas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarreprecpagcon(codnom,denominacion,tipnom)
{
	opener.document.form1.txtcodnom.value=codnom;
	opener.document.form1.txtcodnom.readOnly=true;
	opener.document.form1.txtdesnom.value=denominacion;
	opener.document.form1.txtdesnom.readOnly=true;
	opener.document.form1.txttipnom.value=tipnom;
	opener.document.form1.txtcodsubnomdes.value="";
	opener.document.form1.txtcodsubnomhas.value="";
	close();
}


function aceptarrepperipsdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	opener.document.form1.txtanocurper.value="";
	opener.document.form1.txtmescurper.value="";
	opener.document.form1.txtdesmesper.value="";	
	close();
}

function aceptarrepperipshas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

 function aceptartransferir(codnom,desnom)
 {
     opener.document.form1.txtcodnombus.value=codnom;
	 opener.document.form1.txtcodnombus.readOnly=true;
     opener.document.form1.txtdesnombus.value=desnom;
	 opener.document.form1.txtdesnombus.readOnly=true;	 
	 close();
 }

function aceptarreppagounides(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	close();
}

function aceptarreppagounihas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarreplispreantint(codnom,desnom)
{
	opener.document.form1.txtcodnom.value=codnom;
	opener.document.form1.txtcodnom.readOnly=true;
	opener.document.form1.txtdesnom.value=desnom;
	opener.document.form1.txtdesnom.readOnly=true;
	close();
}

function aceptarprestacionantiguedadant(codnom,desnom)
{
	opener.document.form1.txtcodnom.value=codnom;
	opener.document.form1.txtcodnom.readOnly=true;
	opener.document.form1.txtdesnom.value=desnom;
	opener.document.form1.txtdesnom.readOnly=true;
	close();
}

function aceptarcargorac(codnom,denominacion,tipnom)
{
	opener.document.form1.txtcodnom.value=codnom;
	opener.document.form1.txtcodnom.readOnly=true;
	opener.document.form1.txtdesnom.value=denominacion;
	opener.document.form1.txtdesnom.readOnly=true;
	opener.document.form1.txttipnom.value=tipnom;
	close();
}

function aceptarrepdedplandes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value=codnom;
	opener.document.form1.txtcodnomhas.readOnly=true;
	opener.document.form1.txtcodconc.value="";
	opener.document.form1.txtanocur.value="";
	opener.document.form1.txtmescur.value="";
	close();
}

function aceptarrepdedplanhas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarreptipperdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	close();
}

function aceptarreptipperhas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function aceptarresnomdes(codnom)
{
	opener.document.form1.txtcodnomdes.value=codnom;
	opener.document.form1.txtcodnomdes.readOnly=true;
	opener.document.form1.txtcodnomhas.value="";
	close();
}

function aceptarresnomhas(codnom)
{
	if(opener.document.form1.txtcodnomdes.value<=codnom)
	{
		opener.document.form1.txtcodnomhas.value=codnom;
		opener.document.form1.txtcodnomhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de la nómina inválido");
	}
}

function ue_mostrar(myfield,e)
{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	if (keycode == 13)
	{
		ue_search();
		return false;
	}
	else
		return true
}

function ue_search()
{
	f=document.form1;
	f.operacion.value="BUSCAR";
	f.action="sigesp_snorh_cat_nomina.php?tipo=<?php print $ls_tipo;?>";
	f.submit();
}
</script>
</html>
