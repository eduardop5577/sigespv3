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
	require_once("../../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	require_once("class_funciones_cxp.php");
	$io_funciones_cxp=new class_funciones_cxp();
	// Tipo del catalogo que se requiere pintar
	$ls_catalogo=$io_funciones_cxp->uf_obtenervalor("catalogo","");
	$ruta = '../../';
	require_once("../../base/librerias/php/general/sigesp_lib_conexiones.php");
    $io_conexiones=new conexiones();
	$io_conexiones->decodificar_post();
	switch($ls_catalogo)
	{
		case "PROVEEDOR":
			uf_print_proveedor();
			break;
		case "BENEFICIARIO":
			uf_print_beneficiario();
			break;
		case "ESTRUCTURA1":
			uf_print_estructura1();
			break;
		case "ESTRUCTURA2":
			uf_print_estructura2();
			break;
		case "ESTRUCTURA3":
			uf_print_estructura3();
			break;
		case "ESTRUCTURA4":
			uf_print_estructura4();
			break;
		case "ESTRUCTURA5":
			uf_print_estructura5();
			break;
		case "CUENTASSPG":
			uf_print_cuentasspg();
			break;
		case "CUENTASSPI":
			uf_print_cuentasspi();
			break;
		case "CUENTASSCG":
			uf_print_cuentasscg();
			break;
		case "OTROSCREDITOS":
			uf_print_otroscreditos();
			break;
		case "DEDUCCIONES":
			uf_print_deducciones();
			break;
		case "RECEPCION":
			uf_print_recepcion();
			break;
		case "COMPROMISOS":
			uf_print_compromisos();
			break;
		case "SOLICITUDPAGO":
			uf_print_solicitudespago();
			break;
		case "DTPRESUPUESTO":
			uf_print_dtpresupuestario();
			break;	
		case "DTINGRESO":
			uf_print_dtingresoblanco();
			break;	
		case "CATINGRESO":
			uf_print_dtingreso();
			break;	
		case "DTCARGOS":
			uf_print_dtcargos();
			break;
		case "CALCULARCARGO":
			uf_calcular_cargo();
			break;			
		case "DTCONTABLE":
			uf_print_dtcontable();
			break;
	 	case "NOTAS":
			uf_print_notas();
			break;				
		case "RECEPCIONESNCND":
			uf_print_recepcionesncnd();
			break;
		case "FUENTEFINANCIAMIENTO":
			uf_print_fuentefinanciamiento();
			break;
		case "RETENCIONESISLR":
			uf_print_retencionesislr();
			break;
		case "RELACIONFACTURAS":
			uf_print_solfacturas();
			break;
		case "CATDEDUCCIONES":
			uf_print_catdeducciones();
			break;
		case "RETENCIONESIVA":
			uf_print_retencionesiva();
			break;
		case "RETENCIONESMUNICIPALES":
			uf_print_retencionesmunicipales();
			break;
		case "RETENCIONIVA":
			uf_print_retencioniva();
			break;	
		case "AMORTIZACION":
			uf_print_amortizacion();
			break;
		case "UNIDADEJECUTORA":
			uf_print_unidad_ejecutora();
			break;
		case "RETENCIONESAPORTE":
			uf_print_retencionesaporte();
			break;
		case "ORDENMINISTERIO":
			uf_print_ordenministerio();
			break;
		case "RETENCIONES1x1000":
			uf_print_retenciones1x1000();
			break;
		case "CONFACTA":
			uf_print_confacta();
			break;
		case "ACTAAPORTE":
			uf_print_compromisosacta();
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_proveedor()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de proveedores
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_codpro="%".$_POST['codpro']."%";
		$ls_nompro="%".$_POST['nompro']."%";
		$ls_dirpro="%".$_POST['dirpro']."%";
		$ls_rifpro="%".$_POST['rifpro']."%";
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		$ls_repcajchi=$_POST['repcajchi'];
		$ls_cadena="";
		$ls_cadena2="'' AS anticipo";
        $ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
        if($ls_conrecdoc)
		{
			$ls_estprord='C';
		}
		else
		{
			$ls_estprord='R';
		}
		
		$ls_repcon=$_POST['repcon'];
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1' && $ls_repcon=='0') {
			$ls_estconcat = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                "  							WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		
		//FILTRO RECEPCIONES CONTABLE
		if($ls_repcon=='1') {
			$ls_filtrofrom = ",cxp_documento ";
			$ls_filtroest = 'AND cxp_rd.codtipdoc = cxp_documento.codtipdoc AND cxp_documento.estcon=1 AND cxp_documento.estpre=4';	
		}
		//FILTRO RECEPCIONES CONTABLE
		switch ($ls_tipo)
		{
			case "RECEPCION":
					$ls_cadena2=" ";
			break;
			case "SOLICITUDPAGO":
			   if($ls_repcajchi==0)
			   {
					$ls_cadena=" AND cod_pro IN (SELECT cxp_rd.cod_pro 
													FROM cxp_rd {$ls_filtrofrom}
													WHERE estprodoc='".$ls_estprord."' 
													AND estaprord='1' AND tipproben='P' {$ls_filtroest})";
			   }
			break;
			case "AEROLINEAS":
				$ls_cadena=" AND aerolipro='1'";
			break;
			case "CMPRET":
				$ls_cadena=" AND (cod_pro IN (SELECT cxp_rd_deducciones.cod_pro FROM cxp_rd_deducciones,sigesp_deducciones,cxp_rd $ls_filtrofrom
				                              WHERE cxp_rd_deducciones.estcmp<>'1'
											    AND (sigesp_deducciones.iva=1 OR sigesp_deducciones.estretmun=1 OR 
												     sigesp_deducciones.retaposol=1 OR sigesp_deducciones.estretmil='1' OR 
													 sigesp_deducciones.islr=1) $ls_filtroest
												AND cxp_rd.estprodoc='C'
												AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp
												AND cxp_rd_deducciones.codded=sigesp_deducciones.codded
												AND cxp_rd_deducciones.codemp=cxp_rd.codemp
												AND cxp_rd_deducciones.codtipdoc=cxp_rd.codtipdoc
												AND cxp_rd_deducciones.cod_pro=cxp_rd.cod_pro
												AND cxp_rd_deducciones.ced_bene=cxp_rd.ced_bene
												AND cxp_rd_deducciones.numrecdoc=cxp_rd.numrecdoc) 
								    OR cod_pro IN (SELECT cxp_rd.codproalt FROM cxp_rd_deducciones,sigesp_deducciones,cxp_rd $ls_filtrofrom
				                              WHERE cxp_rd_deducciones.estcmp<>'1'
											    AND (sigesp_deducciones.iva=1 OR sigesp_deducciones.estretmun=1 OR 
												     sigesp_deducciones.retaposol=1 OR sigesp_deducciones.estretmil='1' OR 
													 sigesp_deducciones.islr=1) $ls_filtroest
												AND cxp_rd.estprodoc='C'
												AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp
												AND cxp_rd_deducciones.codded=sigesp_deducciones.codded
												AND cxp_rd_deducciones.codemp=cxp_rd.codemp
												AND cxp_rd_deducciones.codtipdoc=cxp_rd.codtipdoc
												AND cxp_rd_deducciones.cod_pro=cxp_rd.cod_pro
												AND cxp_rd_deducciones.ced_bene=cxp_rd.ced_bene
												AND cxp_rd_deducciones.numrecdoc=cxp_rd.numrecdoc))";
			break;
/*			case "MODCMPRET":
				$ls_cadena=" AND (cod_pro IN (SELECT cxp_rd_deducciones.cod_pro FROM cxp_rd_deducciones,sigesp_deducciones,cxp_rd $ls_filtrofrom
				                              WHERE cxp_rd_deducciones.estcmp<>'1'
											    AND (sigesp_deducciones.iva=1 OR sigesp_deducciones.estretmun=1 OR 
												     sigesp_deducciones.retaposol=1 OR sigesp_deducciones.estretmil='1' OR 
													 sigesp_deducciones.islr=1) $ls_filtroest
												AND cxp_rd.estprodoc='C'
												AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp
												AND cxp_rd_deducciones.codded=sigesp_deducciones.codded
												AND cxp_rd_deducciones.codemp=cxp_rd.codemp
												AND cxp_rd_deducciones.codtipdoc=cxp_rd.codtipdoc
												AND cxp_rd_deducciones.cod_pro=cxp_rd.cod_pro
												AND cxp_rd_deducciones.ced_bene=cxp_rd.ced_bene
												AND cxp_rd_deducciones.numrecdoc=cxp_rd.numrecdoc)
									OR cod_pro IN (SELECT cxp_rd.codproalt FROM cxp_rd_deducciones,sigesp_deducciones,cxp_rd
				                              WHERE cxp_rd_deducciones.estcmp<>'1'
											    AND (sigesp_deducciones.iva=1 OR sigesp_deducciones.estretmun=1 OR 
												     sigesp_deducciones.retaposol=1 OR sigesp_deducciones.estretmil='1' OR 
													 sigesp_deducciones.islr=1)
												AND cxp_rd.estprodoc='C'
												AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp
												AND cxp_rd_deducciones.codded=sigesp_deducciones.codded
												AND cxp_rd_deducciones.codemp=cxp_rd.codemp
												AND cxp_rd_deducciones.codtipdoc=cxp_rd.codtipdoc
												AND cxp_rd_deducciones.cod_pro=cxp_rd.cod_pro
												AND cxp_rd_deducciones.ced_bene=cxp_rd.ced_bene
												AND cxp_rd_deducciones.numrecdoc=cxp_rd.numrecdoc))";
			break;
*/		}
			$ls_sql="SELECT cod_pro, nompro, trim(rpc_proveedor.sc_cuenta) AS sc_cuenta, rifpro, tipconpro, dirpro,".
					"       trim(sc_cuentarecdoc) AS sc_cuentarecdoc, ageviapro,".
					"       (SELECT cxp_rd_amortizacion.cod_pro FROM cxp_rd_amortizacion WHERE cxp_rd_amortizacion.cod_pro=rpc_proveedor.cod_pro GROUP BY cxp_rd_amortizacion.cod_pro) AS anticipo".
					"  FROM rpc_proveedor,scg_cuentas  ".
					" WHERE rpc_proveedor.codemp = '".$ls_codemp."' ".
					"   AND cod_pro <> '----------' ".
					"   AND estprov = 0 ".
					"   AND cod_pro like '".$ls_codpro."' ".
					"   AND nompro like '".$ls_nompro."' ".
					"   AND dirpro like '".$ls_dirpro."' ". 
					"   AND rifpro like '".$ls_rifpro."' ".
					"   AND rpc_proveedor.codemp=scg_cuentas.codemp". 
					"   AND rpc_proveedor.sc_cuenta=scg_cuentas.sc_cuenta". 
					$ls_cadena.
					" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Proveedores","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td style='cursor:pointer' title='Ordenar por Codigo' align='center' onClick=ue_orden('rpc_proveedor.cod_pro')>Codigo</td>";
			print "<td style='cursor:pointer' title='Ordenar por Nombre' align='left' onClick=ue_orden('rpc_proveedor.nompro')>Nombre</td>";
			print "<td style='cursor:pointer' title='Ordenar por RIF' align='center' onClick=ue_orden('rpc_proveedor.rifpro')>Rif</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codpro=$row["cod_pro"];
				$ls_nompro=$row["nompro"];
				$ls_sccuenta=trim($row["sc_cuenta"]);
				$ls_tipconpro=$row["tipconpro"];
				$ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
				$ls_rifpro=$row["rifpro"];
				switch($ls_conrecdoc)
				{
					case "0":
						$ls_sccuenta=$row["sc_cuenta"];
						break;
					
					case "1":
						$ls_sccuenta=$row["sc_cuentarecdoc"];
						break;
				}
				$ls_tipconpro=$row["tipconpro"];
				$ls_dirprov=$row["dirpro"];
				$ls_ageviapro=$row["ageviapro"];
				$ls_anticipo=$row["anticipo"];
				switch ($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar('$ls_codpro','$ls_nompro','$ls_rifpro','$ls_sccuenta','$ls_tipconpro','$ls_ageviapro','$ls_anticipo');\">".$ls_codpro."</a></td>";
						print "<td>".$ls_nompro."</td>";
						print "<td>".$ls_rifpro."</td>";
						print "</tr>";
					break;
					
					case "SOLICITUDPAGO":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar_solicitudpago('$ls_codpro','$ls_nompro','$ls_rifpro');\">".$ls_codpro."</a></td>";
						print "<td>".$ls_nompro."</td>";
						print "<td>".$ls_rifpro."</td>";
						print "</tr>";
					break;
					
					case "REPDES":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar_reportedesde('$ls_codpro');\">".$ls_codpro."</a></td>";
						print "<td>".$ls_nompro."</td>";
						print "<td>".$ls_rifpro."</td>";
						print "</tr>";
					break;
					
					case "REPHAS":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar_reportehasta('$ls_codpro');\">".$ls_codpro."</a></td>";
						print "<td>".$ls_nompro."</td>";
						print "<td>".$ls_rifpro."</td>";
						print "</tr>";
					break;
					
					case "CMPRET":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar_cmpretencion('$ls_codpro');\">".$ls_codpro."</a></td>";
						print "<td>".$ls_nompro."</td>";
						print "<td>".$ls_rifpro."</td>";
						print "</tr>";
					break;

					case "MODCMPRET":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar_modcmpretencion('$ls_codpro','$ls_nompro','$ls_rifpro','$ls_dirprov');\">".$ls_codpro."</a></td>";
						print "<td>".$ls_nompro."</td>";
						print "<td>".$ls_rifpro."</td>";
						print "</tr>";
					break;
					case "CATALOGO":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar_solicitudpago('$ls_codpro','$ls_nompro','$ls_rifpro');\">".$ls_codpro."</a></td>";
						print "<td>".$ls_nompro."</td>";
						print "<td>".$ls_rifpro."</td>";
						print "</tr>";
					break;
					case "AEROLINEAS":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar_aerolineas('$ls_codpro','$ls_nompro');\">".$ls_codpro."</a></td>";
						print "<td>".$ls_nompro."</td>";
						print "<td>".$ls_rifpro."</td>";
						print "</tr>";
					break;
					
				}
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_proveedor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_beneficiario()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_beneficiario
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de beneficiarios
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_cedbene="%".$_POST['cedbene']."%";
		$ls_nombene="%".$_POST['nombene']."%";
		$ls_apebene="%".$_POST['apebene']."%";
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
        $ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
		$ls_repcajchi=$_POST['repcajchi'];
		$ls_repcon=$_POST['repcon']; 
		$ls_cadena="";
		if($ls_conrecdoc)
		{
			$ls_estprord='C';
		}
		else
		{
			$ls_estprord='R';
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1' && $ls_repcon=='0') {
			$ls_estconcat = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                "  							WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		
		//FILTRO RECEPCIONES CONTABLE
		if($ls_repcon=='1') {
			$ls_filtrofrom = ",cxp_documento ";
			$ls_filtroest = 'AND cxp_rd.codtipdoc = cxp_documento.codtipdoc AND cxp_documento.estcon=1 AND cxp_documento.estpre=4';	
		}
		//FILTRO RECEPCIONES CONTABLE
		switch ($ls_tipo)
		{
			case "SOLICITUDPAGO":
				if($ls_repcajchi==0)
				{
					$ls_cadena=" AND ced_bene IN (SELECT cxp_rd.ced_bene 
													FROM cxp_rd {$ls_filtrofrom}
													WHERE estprodoc='".$ls_estprord."' 
													AND estaprord='1' AND tipproben='B' $ls_filtroest)";
				}
			break;
			case "CMPRET":
				$ls_cadena=" AND ced_bene IN (SELECT cxp_rd_deducciones.ced_bene FROM cxp_rd_deducciones,sigesp_deducciones,cxp_rd $ls_filtrofrom
				                              WHERE cxp_rd_deducciones.estcmp<>'1'
											    AND (sigesp_deducciones.iva=1 OR sigesp_deducciones.estretmun=1 OR 
												     sigesp_deducciones.retaposol=1 OR sigesp_deducciones.estretmil='1' OR 
													 sigesp_deducciones.islr=1) $ls_filtroest
												AND cxp_rd.estprodoc='C'
												AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp
												AND cxp_rd_deducciones.codded=sigesp_deducciones.codded
												AND cxp_rd_deducciones.codemp=cxp_rd.codemp
												AND cxp_rd_deducciones.codtipdoc=cxp_rd.codtipdoc
												AND cxp_rd_deducciones.cod_pro=cxp_rd.cod_pro
												AND cxp_rd_deducciones.ced_bene=cxp_rd.ced_bene
												AND cxp_rd_deducciones.numrecdoc=cxp_rd.numrecdoc)";
			break;
/*			case "MODCMPRET":
				$ls_cadena=" AND ced_bene IN (SELECT cxp_rd_deducciones.ced_bene FROM cxp_rd_deducciones,sigesp_deducciones,cxp_rd $ls_filtrofrom
				                              WHERE cxp_rd_deducciones.estcmp<>'1'
											    AND (sigesp_deducciones.iva=1 OR sigesp_deducciones.estretmun=1 OR 
												     sigesp_deducciones.retaposol=1 OR sigesp_deducciones.estretmil='1' OR 
													 sigesp_deducciones.islr=1) $ls_filtroest
												AND cxp_rd.estprodoc='C'
												AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp
												AND cxp_rd_deducciones.codded=sigesp_deducciones.codded
												AND cxp_rd_deducciones.codemp=cxp_rd.codemp
												AND cxp_rd_deducciones.codtipdoc=cxp_rd.codtipdoc
												AND cxp_rd_deducciones.cod_pro=cxp_rd.cod_pro
												AND cxp_rd_deducciones.ced_bene=cxp_rd.ced_bene
												AND cxp_rd_deducciones.numrecdoc=cxp_rd.numrecdoc)";
			break;
*/		}
		$ls_sql="SELECT TRIM(ced_bene) as ced_bene, nombene, apebene, rifben, sc_cuenta, tipconben, dirbene, sc_cuentarecdoc ".
				"  FROM rpc_beneficiario ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND ced_bene <> '----------' ".
				"   AND ced_bene like '".$ls_cedbene."' ".
				"   AND nombene like '".$ls_nombene."' ".
				"   AND apebene like '".$ls_apebene."' ".
				$ls_cadena.
				" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Beneficiarios","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td style='cursor:pointer' title='Ordenar por Cedula' align='center' onClick=ue_orden('ced_bene')>Cedula </td>";
			print "<td style='cursor:pointer' title='Ordenar por Nombre' align='center' onClick=ue_orden('nombene')>Nombre</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_cedbene=$row["ced_bene"];
				$ls_nombene=$row["nombene"]." ".$row["apebene"];
				$ls_rifben=$row["rifben"];
				$ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
				switch($ls_conrecdoc)
				{
					case "0":
						$ls_sccuenta=trim($row["sc_cuenta"]);
						break;
					
					case "1":
						$ls_sccuenta=trim($row["sc_cuentarecdoc"]);
						break;
				}
				$ls_tipconben=$row["tipconben"];
				$ls_dirbene=$row["dirbene"];
				switch ($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_cedbene','$ls_nombene','$ls_rifben','$ls_sccuenta','$ls_tipconben');\">".$ls_cedbene."</a></td>";
						print "<td>".$ls_nombene."</td>";
						print "</tr>";
					break;
					
					case "SOLICITUDPAGO":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_solicitudpago('$ls_cedbene','$ls_nombene','$ls_rifben');\">".$ls_cedbene."</a></td>";
						print "<td>".$ls_nombene."</td>";
						print "</tr>";
					break;
					
					case "REPDES":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_reportedesde('$ls_cedbene');\">".$ls_cedbene."</a></td>";
						print "<td>".$ls_nombene."</td>";
						print "</tr>";
					break;
					
					case "REPHAS":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_reportehasta('$ls_cedbene');\">".$ls_cedbene."</a></td>";
						print "<td>".$ls_nombene."</td>";
						print "</tr>";
					break;
					
					case "CMPRET":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_cmpretencion('$ls_cedbene');\">".$ls_cedbene."</a></td>";
						print "<td>".$ls_nombene."</td>";
						print "</tr>";
					break;

					case "MODCMPRET":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar_modcmpretencion('$ls_cedbene','$ls_nombene','$ls_rifben','$ls_dirbene');\">".$ls_cedbene."</a></td>";
						print "<td>".$ls_nombene."</td>";
						print "</tr>";
					break;
					case "CATALOGO":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_solicitudpago('$ls_cedbene','$ls_nombene','$ls_rifben');\">".$ls_cedbene."</a></td>";
						print "<td>".$ls_nombene."</td>";
						print "</tr>";
					break;
					
				}					
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_beneficiario
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_estructura1()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_estructura1
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de la estructura presupuestaria 1
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 06/04/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_codestpro1="%".$_POST['codestpro1']."%";
		$ls_denestpro1="%".$_POST['denestpro1']."%";
		$ls_tipo=$_POST['tipo'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$arrResultado=$io_funciones_cxp->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
		$li_len1=$arrResultado["ai_len1"];
		$li_len2=$arrResultado["ai_len2"];
		$li_len3=$arrResultado["ai_len3"];
		$li_len4=$arrResultado["ai_len4"];
		$li_len5=$arrResultado["ai_len5"];
		$ls_titulo=$arrResultado["as_titulo"];
		
		$ls_sql="SELECT codestpro1, denestpro1, estcla, estint, sc_cuenta ".
				"  FROM spg_ep1 ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codestpro1 like '".$ls_codestpro1."' ".
				"   AND denestpro1 like '".$ls_denestpro1."' ".
				" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Estructura ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td style='cursor:pointer' title='Ordenar por Código' align='center' onClick=ue_orden('codestpro1')>Código </td>";
			print "<td style='cursor:pointer' title='Ordenar por Denominación' align='center' onClick=ue_orden('denestpro1')>Denominación</td>";
			print "<td style='cursor:pointer' title='Ordenar por Estatus' align='center' onClick=ue_orden('estcla')>Estatus</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codestpro1=substr($row["codestpro1"],(strlen($row["codestpro1"])-$li_len1),$li_len1);
				$ls_denestpro1=rtrim($row["denestpro1"]);
				$ls_estcla=rtrim($row["estcla"]);
				$ls_estint=rtrim($row["estint"]);
				$ls_cuentaint=rtrim($row["sc_cuenta"]);
				$ls_estatus="";
				switch($ls_estcla)
				{
					case "A":
						$ls_estatus="Acción";
						break;
					case "P":
						$ls_estatus="Proyecto";
						break;
				}
				switch($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codestpro1','$ls_denestpro1','$ls_estcla','$ls_estint','$ls_cuentaint');\">".$ls_codestpro1."</a></td>";
						print "<td>".$ls_denestpro1."</td>";
						print "<td>".$ls_estatus."</td>";
						print "</tr>";			
						break;
				}
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_estructura1
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_estructura2()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_estructura2
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de la estructura presupuestaria 2
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 06/04/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_codestpro1=$_POST['codestpro1'];
		$ls_estcla=$_POST['estcla'];
		$ls_codestpro2="%".$_POST['codestpro2']."%";
		$ls_denestpro2="%".$_POST['denestpro2']."%";
		$ls_tipo=$_POST['tipo'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$arrResultado=$io_funciones_cxp->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
		$li_len1=$arrResultado["ai_len1"];
		$li_len2=$arrResultado["ai_len2"];
		$li_len3=$arrResultado["ai_len3"];
		$li_len4=$arrResultado["ai_len4"];
		$li_len5=$arrResultado["ai_len5"];
		$ls_titulo=$arrResultado["as_titulo"];
		
		$ls_sql="SELECT codestpro1, codestpro2, denestpro2, estcla ".
				"  FROM spg_ep2 ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codestpro1 ='".str_pad($ls_codestpro1,25,"0",0)."' ".
				"	AND estcla = '".$ls_estcla."' ".
				"   AND codestpro2 like '".$ls_codestpro2."' ".
				"   AND denestpro2 like '".$ls_denestpro2."' ".
				" ORDER BY codestpro1, ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Estructura ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width=100 align='center'>".$_SESSION["la_empresa"]["nomestpro1"]." </td>";
			print "<td width=150 style='cursor:pointer' title='Ordenar por Código' align='center' onClick=ue_orden('codestpro2')>Código</td>";
			print "<td width=200 style='cursor:pointer' title='Ordenar por Denominación' align='center' onClick=ue_orden('denestpro2')>Denominación</td>";
			print "<td width=50 style='cursor:pointer' title='Ordenar por Estatus' align='center' onClick=ue_orden('estcla')>Estatus</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codestpro1=substr($row["codestpro1"],(strlen($row["codestpro1"])-$li_len1),$li_len1);
				$ls_codestpro2=substr($row["codestpro2"],(strlen($row["codestpro2"])-$li_len2),$li_len2);
				$ls_denestpro2=rtrim($row["denestpro2"]);
				$ls_estcla=rtrim($row["estcla"]);
				$ls_estatus="";
				switch($ls_estcla)
				{
					case "A":
						$ls_estatus="Acción";
						break;
					case "P":
						$ls_estatus="Proyecto";
						break;
				}
				switch($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td width=30 align=\"center\"><a href=\"javascript: aceptar('$ls_codestpro2','$ls_denestpro2');\">".trim($ls_codestpro1)."</td>";
						print "<td width=30 align=\"center\">".trim($ls_codestpro2)."</td>";
						print "<td width=130 align=\"left\">".trim($ls_denestpro2)."</td>";
						print "<td width=30 align=\"center\">".$ls_estatus."</td>";
						print "</tr>";
						break;
				}
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_estructura2
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_estructura3()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_estructura3
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de la estructura presupuestaria 3
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 06/04/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_codestpro1=$_POST['codestpro1'];
		$ls_estcla=$_POST['estcla'];
		$ls_codestpro2=$_POST['codestpro2'];
		$ls_criterio="";
		if($ls_codestpro1!="")
		{
			$ls_criterio=$ls_criterio."   AND spg_ep3.codestpro1 ='".str_pad($ls_codestpro1,25,"0",0)."' ";
			$ls_criterio=$ls_criterio."   AND spg_ep3.estcla ='".$ls_estcla."' ";
		}
		if($ls_codestpro2!="")
		{
			$ls_criterio=$ls_criterio."   AND spg_ep3.codestpro2 ='".str_pad($ls_codestpro2,25,"0",0)."' ";
		}
		$ls_codestpro3="%".$_POST['codestpro3']."%";
		$ls_denestpro3="%".$_POST['denestpro3']."%";
		$ls_tipo=$_POST['tipo'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$arrResultado=$io_funciones_cxp->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
		$li_len1=$arrResultado["ai_len1"];
		$li_len2=$arrResultado["ai_len2"];
		$li_len3=$arrResultado["ai_len3"];
		$li_len4=$arrResultado["ai_len4"];
		$li_len5=$arrResultado["ai_len5"];
		$ls_titulo=$arrResultado["as_titulo"];
		
		$ls_sql="SELECT spg_ep3.codestpro1, spg_ep3.codestpro2, spg_ep3.codestpro3, spg_ep3.denestpro3,".
				"       spg_ep1.denestpro1,spg_ep2.denestpro2, spg_ep1.estcla, spg_ep1.estint, spg_ep1.sc_cuenta ".
				"  FROM spg_ep3,spg_ep2,spg_ep1 ".
				" WHERE spg_ep3.codemp='".$ls_codemp."' ".
				$ls_criterio.
				"   AND spg_ep3.codestpro3 like '".$ls_codestpro3."' ".
				"   AND spg_ep3.denestpro3 like '".$ls_denestpro3."' ".
				"   AND spg_ep1.codemp=spg_ep3.codemp".
				"   AND spg_ep1.codestpro1=spg_ep3.codestpro1".
				"   AND spg_ep1.estcla=spg_ep3.estcla".
				"   AND spg_ep2.codemp=spg_ep3.codemp".
				"   AND spg_ep2.codestpro1=spg_ep3.codestpro1".
				"   AND spg_ep2.codestpro2=spg_ep3.codestpro2".
				"   AND spg_ep2.estcla=spg_ep3.estcla".
				" ORDER BY spg_ep3.codestpro1, spg_ep3.codestpro2, ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Estructura ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width=100 align='center'>".$_SESSION["la_empresa"]["nomestpro1"]." </td>";
			print "<td width=100 align='center'>".$_SESSION["la_empresa"]["nomestpro2"]." </td>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Código' align='center' onClick=ue_orden('spg_ep3.codestpro3')>Código</td>";
			print "<td width=150 style='cursor:pointer' title='Ordenar por Denominación' align='center' onClick=ue_orden('spg_ep3.denestpro3')>Denominación</td>";
			print "<td width=50 style='cursor:pointer' title='Ordenar por Estatus' align='center' onClick=ue_orden('estcla')>Estatus</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codestpro1=substr($row["codestpro1"],(strlen($row["codestpro1"])-$li_len1),$li_len1);
				$ls_codestpro2=substr($row["codestpro2"],(strlen($row["codestpro2"])-$li_len2),$li_len2);
				$ls_codestpro3=substr($row["codestpro3"],(strlen($row["codestpro3"])-$li_len3),$li_len3);
				$ls_denestpro1=rtrim($row["denestpro1"]);
				$ls_denestpro2=rtrim($row["denestpro2"]);
				$ls_denestpro3=rtrim($row["denestpro3"]);
				$ls_estcla=rtrim($row["estcla"]);
				$ls_estint=rtrim($row["estint"]);
				$ls_cuentaint=rtrim($row["sc_cuenta"]);
				$ls_estatus="";
				switch($ls_estcla)
				{
					case "A":
						$ls_estatus="Acción";
						break;
					case "P":
						$ls_estatus="Proyecto";
						break;
				}
				switch($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td width=30 align=\"center\"><a href=\"javascript: aceptar('$ls_codestpro1','$ls_codestpro2','$ls_codestpro3','$ls_denestpro1','$ls_denestpro2','$ls_denestpro3','$ls_estcla','$ls_estint','$ls_cuentaint');\">".trim($ls_codestpro1)."</td>";
						print "<td width=30 align=\"center\">".trim($ls_codestpro2)."</td>";
						print "<td width=30 align=\"center\">".trim($ls_codestpro3)."</a></td>";
						print "<td width=130 align=\"left\">".$ls_denestpro3."</td>";
						print "<td width=30 align=\"center\">".$ls_estatus."</td>";
						print "</tr>";			
						break;
				}
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_estructura3
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_estructura4()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_estructura4
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de la estructura presupuestaria 4
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/04/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_codestpro1=$_POST['codestpro1'];
		$ls_codestpro2=$_POST['codestpro2'];
		$ls_codestpro3=$_POST['codestpro3'];
		$ls_estcla=$_POST['estcla'];
		$ls_codestpro4="%".$_POST['codestpro4']."%";
		$ls_denestpro4="%".$_POST['denestpro4']."%";
		$ls_tipo=$_POST['tipo'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$arrResultado=$io_funciones_cxp->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
		$li_len1=$arrResultado["ai_len1"];
		$li_len2=$arrResultado["ai_len2"];
		$li_len3=$arrResultado["ai_len3"];
		$li_len4=$arrResultado["ai_len4"];
		$li_len5=$arrResultado["ai_len5"];
		$ls_titulo=$arrResultado["as_titulo"];
		
		$ls_sql="SELECT codestpro1,codestpro2,codestpro3,codestpro4,denestpro4,estcla ".
				"  FROM spg_ep4 ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codestpro1 ='".str_pad($ls_codestpro1,25,"0",0)."' ".
				"	AND estcla = '".$ls_estcla."' ".
				"   AND codestpro2 ='".str_pad($ls_codestpro2,25,"0",0)."' ".
				"   AND codestpro3 ='".str_pad($ls_codestpro3,25,"0",0)."' ".
				"   AND codestpro4 like '".$ls_codestpro4."' ".
				"   AND denestpro4 like '".$ls_denestpro4."' ".
				" ORDER BY codestpro1,codestpro2,codestpro3, ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Estructura ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width=80 align='center'>".$_SESSION["la_empresa"]["nomestpro1"]." </td>";
			print "<td width=80 align='center'>".$_SESSION["la_empresa"]["nomestpro2"]." </td>";
			print "<td width=80 align='center'>".$_SESSION["la_empresa"]["nomestpro3"]." </td>";
			print "<td width=80 style='cursor:pointer' title='Ordenar por Código' align='center' onClick=ue_orden('codestpro4')>Código</td>";
			print "<td width=130 style='cursor:pointer' title='Ordenar por Denominación' align='center' onClick=ue_orden('denestpro4')>Denominación</td>";
			print "<td width=50 style='cursor:pointer' title='Ordenar por Estatus' align='center' onClick=ue_orden('estcla')>Estatus</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codestpro1=substr($row["codestpro1"],(strlen($row["codestpro1"])-$li_len1),$li_len1);
				$ls_codestpro2=substr($row["codestpro2"],(strlen($row["codestpro2"])-$li_len2),$li_len2);
				$ls_codestpro3=substr($row["codestpro3"],(strlen($row["codestpro3"])-$li_len3),$li_len3);
				$ls_codestpro4=substr($row["codestpro4"],(strlen($row["codestpro4"])-$li_len4),$li_len4);
				$ls_denestpro4=rtrim($row["denestpro4"]);
				$ls_estcla=rtrim($row["estcla"]);
				$ls_estatus="";
				switch($ls_estcla)
				{
					case "A":
						$ls_estatus="Acción";
						break;
					case "P":
						$ls_estatus="Proyecto";
						break;
				}
				switch($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td width=30 align=\"center\"><a href=\"javascript: aceptar('$ls_codestpro4','$ls_denestpro4');\">".trim($ls_codestpro1)."</td>";
						print "<td width=30 align=\"center\">".trim($ls_codestpro2)."</td>";
						print "<td width=30 align=\"center\">".trim($ls_codestpro3)."</a></td>";
						print "<td width=30 align=\"center\">".trim($ls_codestpro4)."</a></td>";
						print "<td width=130 align=\"left\">".$ls_denestpro4."</td>";
						print "<td width=30 align=\"center\">".$ls_estatus."</td>";
						print "</tr>";			


						break;
				}
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_estructura4
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_estructura5()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_estructura5
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de la estructura presupuestaria 5
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/04/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_codestpro1=$_POST['codestpro1'];
		$ls_codestpro2=$_POST['codestpro2'];
		$ls_codestpro3=$_POST['codestpro3'];
		$ls_codestpro4=$_POST['codestpro4'];
		$ls_estcla=$_POST['estcla'];
		$ls_criterio="";
		if($ls_codestpro1!="")
		{
			$ls_criterio=$ls_criterio."   AND spg_ep5.codestpro1 ='".str_pad($ls_codestpro1,25,"0",0)."' ";
			$ls_criterio=$ls_criterio."   AND spg_ep5.estcla ='".$ls_estcla."' ";
		}
		if($ls_codestpro2!="")
		{
			$ls_criterio=$ls_criterio."   AND spg_ep5.codestpro2 ='".str_pad($ls_codestpro2,25,"0",0)."' ";
		}
		if($ls_codestpro3!="")
		{
			$ls_criterio=$ls_criterio."   AND spg_ep5.codestpro3 ='".str_pad($ls_codestpro3,25,"0",0)."' ";
		}
		if($ls_codestpro4!="")
		{
			$ls_criterio=$ls_criterio."   AND spg_ep5.codestpro4 ='".str_pad($ls_codestpro4,25,"0",0)."' ";
		}
		$ls_codestpro5="%".$_POST['codestpro5']."%";
		$ls_denestpro5="%".$_POST['denestpro5']."%";
		$ls_tipo=$_POST['tipo'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$arrResultado=$io_funciones_cxp->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
		$li_len1=$arrResultado["ai_len1"];
		$li_len2=$arrResultado["ai_len2"];
		$li_len3=$arrResultado["ai_len3"];
		$li_len4=$arrResultado["ai_len4"];
		$li_len5=$arrResultado["ai_len5"];
		$ls_titulo=$arrResultado["as_titulo"];
		
		$ls_sql="SELECT spg_ep5.codestpro1, spg_ep5.codestpro2, spg_ep5.codestpro3, spg_ep5.codestpro4, spg_ep5.codestpro5, ".
				"		spg_ep1.denestpro1, spg_ep2.denestpro2, spg_ep3.denestpro3, spg_ep4.denestpro4, spg_ep5.denestpro5,".
				"       spg_ep1.estcla,spg_ep1.estint,spg_ep1.sc_cuenta".
				"  FROM spg_ep1, spg_ep2, spg_ep3, spg_ep4, spg_ep5 ".
				" WHERE spg_ep5.codemp='".$ls_codemp."' ".
				$ls_criterio.
				"   AND spg_ep5.codestpro5 like '".$ls_codestpro5."' ".
				"   AND spg_ep5.denestpro5 like '".$ls_denestpro5."' ".
				"   AND spg_ep5.codemp = spg_ep1.codemp ".
				"   AND spg_ep5.codestpro1 = spg_ep1.codestpro1 ".
				"   AND spg_ep5.estcla = spg_ep1.estcla ".
				"   AND spg_ep5.codemp = spg_ep2.codemp ".
				"   AND spg_ep5.codestpro1 = spg_ep2.codestpro1 ".
				"   AND spg_ep5.codestpro2 = spg_ep2.codestpro2 ".
				"   AND spg_ep5.estcla = spg_ep2.estcla ".
				"   AND spg_ep5.codemp = spg_ep3.codemp ".
				"   AND spg_ep5.codestpro1 = spg_ep3.codestpro1 ".
				"   AND spg_ep5.codestpro2 = spg_ep3.codestpro2 ".
				"   AND spg_ep5.codestpro3 = spg_ep3.codestpro3 ".
				"   AND spg_ep5.estcla = spg_ep3.estcla ".
				"   AND spg_ep5.codemp = spg_ep4.codemp ".
				"   AND spg_ep5.codestpro1 = spg_ep4.codestpro1 ".
				"   AND spg_ep5.codestpro2 = spg_ep4.codestpro2 ".
				"   AND spg_ep5.codestpro3 = spg_ep4.codestpro3 ".
				"   AND spg_ep5.codestpro4 = spg_ep4.codestpro4 ".				
				"   AND spg_ep5.estcla = spg_ep4.estcla ".
				" ORDER BY spg_ep5.codestpro1, spg_ep5.codestpro2, spg_ep5.codestpro3, spg_ep5.codestpro4, ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Estructura ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width=70 align='center'>".$_SESSION["la_empresa"]["nomestpro1"]." </td>";
			print "<td width=70 align='center'>".$_SESSION["la_empresa"]["nomestpro2"]." </td>";
			print "<td width=70 align='center'>".$_SESSION["la_empresa"]["nomestpro3"]." </td>";
			print "<td width=70 align='center'>".$_SESSION["la_empresa"]["nomestpro4"]." </td>";
			print "<td width=70 style='cursor:pointer' title='Ordenar por Código' align='center' onClick=ue_orden('spg_ep5.codestpro5')>Código</td>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Denominación' align='center' onClick=ue_orden('spg_ep5.denestpro5')>Denominación</td>";
			print "<td width=50 style='cursor:pointer' title='Ordenar por Estatus' align='center' onClick=ue_orden('estcla')>Estatus</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codestpro1=substr($row["codestpro1"],(strlen($row["codestpro1"])-$li_len1),$li_len1);
				$ls_codestpro2=substr($row["codestpro2"],(strlen($row["codestpro2"])-$li_len2),$li_len2);
				$ls_codestpro3=substr($row["codestpro3"],(strlen($row["codestpro3"])-$li_len3),$li_len3);
				$ls_codestpro4=substr($row["codestpro4"],(strlen($row["codestpro4"])-$li_len4),$li_len4);
				$ls_codestpro5=substr($row["codestpro5"],(strlen($row["codestpro5"])-$li_len5),$li_len5);
				$ls_denestpro1=rtrim($row["denestpro1"]);
				$ls_denestpro2=rtrim($row["denestpro2"]);
				$ls_denestpro3=rtrim($row["denestpro3"]);
				$ls_denestpro4=rtrim($row["denestpro4"]);
				$ls_denestpro5=rtrim($row["denestpro5"]);
				$ls_estcla=rtrim($row["estcla"]);
				$ls_estint=rtrim($row["estint"]);
				$ls_cuentaint=rtrim($row["sc_cuenta"]);
				$ls_estatus="";
				switch($ls_estcla)
				{
					case "A":
						$ls_estatus="Acción";
						break;
					case "P":
						$ls_estatus="Proyecto";
						break;
				}
				switch($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td width=30 align=\"center\"><a href=\"javascript: aceptar('$ls_codestpro1','$ls_codestpro2',";
						print "'$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_denestpro1','$ls_denestpro2','$ls_denestpro3',";
						print "'$ls_denestpro4','$ls_denestpro5','$ls_estcla','$ls_estint','$ls_cuentaint');\">".trim($ls_codestpro1)."</td>";
						print "<td width=30 align=\"center\">".trim($ls_codestpro2)."</td>";
						print "<td width=30 align=\"center\">".trim($ls_codestpro3)."</a></td>";
						print "<td width=30 align=\"center\">".trim($ls_codestpro4)."</a></td>";
						print "<td width=30 align=\"center\">".trim($ls_codestpro5)."</a></td>";
						print "<td width=130 align=\"left\">".$ls_denestpro5."</td>";
						print "<td width=30 align=\"center\">".$ls_estatus."</td>";
						print "</tr>";			
						break;
				}
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_estructura5
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cuentasspg()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_cuentasspg
		//		   Access: private
		//	    Arguments: 
		//	  Description: Método que inprime el resultado de la busqueda de las cuentas presupuestarias
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/04/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		require_once("../../shared/class_folder/class_sigesp_int.php");
		require_once("../../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../../shared/class_folder/class_sigesp_int_spg.php");
		$io_intspg=new class_sigesp_int_spg();		
		$ls_spgcuenta="%".$_POST['spgcuenta']."%";
		$ls_dencue="%".$_POST['dencue']."%";
		$ls_estcla=$_POST['estcla'];
		$ls_codestpro1=str_pad($_POST['codestpro1'],25,0,0);
		$ls_codestpro2=str_pad($_POST['codestpro2'],25,0,0);
		$ls_codestpro3=str_pad($_POST['codestpro3'],25,0,0);
		$ls_codestpro4=str_pad($_POST['codestpro4'],25,0,0);
		$ls_codestpro5=str_pad($_POST['codestpro5'],25,0,0);
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$_SESSION["fechacomprobante"]=date('Y-m-d');
		$ls_capiva=$_SESSION["la_empresa"]["capiva"];
		$ls_parcapiva=trim($_SESSION["la_empresa"]["parcapiva"]);
		$ls_valcapiva="";
   	    $ls_vali_nivel =	$_SESSION["la_empresa"]["vali_nivel"];
		if($ls_capiva=="1")
		{
			$la_spg_cuenta=explode(",",$ls_parcapiva);
			$li_total=count((array)$la_spg_cuenta);
			for($li_i=0;$li_i<$li_total;$li_i++)
			{
				if($li_i==0)
				{
					$ls_valcapiva=$ls_valcapiva."   AND spg_cuentas.spg_cuenta NOT IN (SELECT spg_cuentas.spg_cuenta FROM spg_cuentas WHERE spg_cuentas.spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
				}
				else
				{
					$ls_valcapiva=$ls_valcapiva."    OR spg_cuentas.spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
				}
			
			}
			if($li_total>0)
			{
				$ls_valcapiva=$ls_valcapiva." )";
			}
		}
		if($ls_campoorden=="codpro")
		{
			$ls_campoorden= "codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta";
		}
		$arrResultado=$io_funciones_cxp->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
		$li_len1=$arrResultado["ai_len1"];
		$li_len2=$arrResultado["ai_len2"];
		$li_len3=$arrResultado["ai_len3"];
		$li_len4=$arrResultado["ai_len4"];
		$li_len5=$arrResultado["ai_len5"];
		$ls_titulo=$arrResultado["as_titulo"];
		
		$ls_cuentas="";
		$ls_tipocuenta="";
		$ls_sql="SELECT TRIM(spg_cuenta) AS spg_cuenta , denominacion, codestpro1,codestpro2, codestpro3, codestpro4, codestpro5, status, ".
				"       (asignado-(comprometido+precomprometido)+aumento-disminucion) as disponible, sc_cuenta, estcla, nivel ".
			    "  FROM spg_cuentas ".
				" WHERE codemp = '".$ls_codemp."'  ".
				"	AND codestpro1 = '".$ls_codestpro1."' ".
				"	AND codestpro2 = '".$ls_codestpro2."' ".
				"	AND codestpro3 = '".$ls_codestpro3."' ".
				"	AND codestpro4 = '".$ls_codestpro4."' ".
				"	AND codestpro5 = '".$ls_codestpro5."' ".
				"	AND estcla = '".$ls_estcla."' ".
				"	AND spg_cuenta like '".$ls_spgcuenta."' ".
				"   AND denominacion like '".$ls_dencue."' $ls_valcapiva ".								
				" ORDER BY ".$ls_campoorden." ".$ls_orden." ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Cuentas Presupuestarias ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=620 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Programatica'          align='center' onClick=ue_orden('codpro')>".$ls_titulo."</td>";
			print "<td width=50>Estatus</td>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Cuenta Presupuestaria' align='center' onClick=ue_orden('spg_cuenta')>Presupuestaria</td>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Cuenta Contable'       align='center' onClick=ue_orden('sc_cuenta')>Contable</td>";
			print "<td width=170 style='cursor:pointer' title='Ordenar por Denominacion'          align='center' onClick=ue_orden('denominacion')>Denominacion</td>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Disponible'            align='center' onClick=ue_orden('disponible')>Disponible</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_status="";
				$ld_asignado=0;
				$ld_aumento=0;
				$ld_disminucion=0;
				$ld_precomprometido=0;
				$ld_comprometido=0;
				$ld_causado=0;
				$ld_pagado=0;
				$li_disponible=0;
				$ls_spg_cuenta=trim($row["spg_cuenta"]);
				$ls_sccuenta=trim($row["sc_cuenta"]);
				$ls_status1=trim($row["status"]);
				$ls_denominacion=rtrim($row["denominacion"]);
				$ls_estcla=rtrim($row["estcla"]);
				$ls_codestpro=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
				$estprog[0]=$row["codestpro1"];
				$estprog[1]=$row["codestpro2"];
				$estprog[2]=$row["codestpro3"];
				$estprog[3]=$row["codestpro4"];
				$estprog[4]=$row["codestpro5"];
				$estprog[5]=$row["estcla"];
				$ls_nivel=$row["nivel"];
				$ls_nivelnextcuenta=$ls_spg_cuenta;
				for($i=$ls_nivel;$i>$ls_vali_nivel;$i--)
				{
					$ls_nivelnextcuenta=$io_intspg->uf_spg_next_cuenta_nivel($ls_nivelnextcuenta);
				}
//				$ls_nivelnextcuenta=$io_intspg->uf_spg_next_cuenta_nivel($ls_spg_cuenta);
				$ls_status="";
				$li_asignado=0;
				$li_aumento=0;
				$li_disminucion=0;
				$li_precomprometido=0;
				$li_comprometido=0;
				$li_causado=0;
				$li_pagado=0;
				$arrResultado="";
				$arrResultado=$io_intspg->uf_spg_saldo_select($ls_codemp, $estprog, $ls_nivelnextcuenta, $ls_status, $adec_asignado, 
				                                           $adec_aumento,$adec_disminucion,$adec_precomprometido,
													   	   $adec_comprometido,$adec_causado,$adec_pagado);
				$ls_status = $arrResultado['as_status'];
				$adec_asignado = $arrResultado['adec_asignado'];
				$adec_aumento = $arrResultado['adec_aumento'];
				$adec_disminucion = $arrResultado['adec_disminucion'];
				$adec_precomprometido = $arrResultado['adec_precomprometido'];
				$adec_comprometido = $arrResultado['adec_comprometido'];
				$adec_causado = $arrResultado['adec_causado'];
				$adec_pagado = $arrResultado['adec_pagado'];
				$lb_valido = $arrResultado['lb_valido'];
				$li_disponible=($adec_asignado-($adec_comprometido+$adec_precomprometido)+$adec_aumento-$adec_disminucion);
				$li_disponible=number_format($li_disponible,2,",",".");
				$ls_estatus="";
				switch($ls_estcla)
				{
					case "A":
						$ls_estatus="Acción";
						break;
					case "P":
						$ls_estatus="Proyecto";
						break;
				}
				$ls_programatica="";
				$arrResultado=$io_funciones_cxp->uf_formatoprogramatica($ls_codestpro,$ls_programatica);
				$ls_programatica=$arrResultado["as_programatica"];
				if($ls_status1=="C")
				{
					print "<tr class=celdas-azules>";
					print "<td align='center'><a href=\"javascript: ue_aceptar('".$ls_spg_cuenta."','".$ls_denominacion."','".$ls_sccuenta."','".$li_disponible."');\">".$ls_programatica."</a></td>";
					print "<td align='center'>".$ls_estatus."</td>";
					print "<td align='center'>".$ls_spg_cuenta."</td>";
					print "<td align='center'>".$ls_sccuenta."</td>";
					print "<td align='left'>".$ls_denominacion."</td>";
					print "<td align='right'>".$li_disponible."</td>";
					print "</tr>";			
				}
				else
				{
					print "<tr class=celdas-blancas>";
					print "<td align='center'>".$ls_programatica."</td>";
					print "<td align='center'>".$ls_estatus."</td>";
					print "<td align='center'>".$ls_spg_cuenta."</td>";
					print "<td align='center'>".$ls_sccuenta."</td>";
					print "<td align='left'>".$ls_denominacion."</td>";
					print "<td align='right'>".$li_disponible."</td>";
					print "</tr>";			
				}
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp,$_SESSION["fechacomprobante"]);
	}// end function uf_print_cuentasspg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cuentasscg()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_cuentasscg
		//		   Access: private
		//	    Arguments: 
		//	  Description: Método que inprime el resultado de la busqueda de las cuentas contables
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 16/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_scgcuenta="%".$_POST['scgcuenta']."%";
		$ls_dencue="%".$_POST['dencue']."%";
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_sql="SELECT sc_cuenta, denominacion, status ".
			    "  FROM scg_cuentas ".
				" WHERE codemp = '".$ls_codemp."'  ".
				"	AND sc_cuenta like '".$ls_scgcuenta."' ".
				"   AND denominacion like '".$ls_dencue."' ".								
				" ORDER BY ".$ls_campoorden." ".$ls_orden." ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Cuentas Contables ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Cuenta Contable' align='center' onClick=ue_orden('sc_cuenta')>Cuenta Contable</td>";
			print "<td width=400 style='cursor:pointer' title='Ordenar por Denominacion'    align='center' onClick=ue_orden('denominacion')>Denominacion</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_sccuenta=trim($row["sc_cuenta"]);
				$ls_status=trim($row["status"]);
				$ls_denominacion=rtrim($row["denominacion"]);
				if($ls_status=="C")
				{
					print "<tr class=celdas-azules>";
					print "<td align='center'><a href=\"javascript: ue_aceptar('".$ls_sccuenta."','".$ls_denominacion."');\">".$ls_sccuenta."</a></td>";
					print "<td align='left'>".$ls_denominacion."</td>";
					print "</tr>";			
				}
				else
				{
					print "<tr class=celdas-blancas>";
					print "<td align='center'>".$ls_sccuenta."</td>";
					print "<td align='left'>".$ls_denominacion."</td>";
					print "</tr>";			
				}
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_cuentasscg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_otroscreditos()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_otroscreditos
		//		   Access: private
		//	    Arguments: 
		//	  Description: Método que inprime el resultado de la busqueda de los creditos a aplicar en un compromiso en particular
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 15/04/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp, $io_grid, $io_ds_cargos;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();
		require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
		$io_ds_cargos=new class_datastore(); // Datastored de cuentas contables
		require_once("sigesp_cxp_c_recepcion.php");
		$io_recepcion=new sigesp_cxp_c_recepcion("../../");
		require_once("class_funciones_cxp.php");
		$io_funciones_cxp=new class_funciones_cxp();
				
		$ls_compromiso=$_POST['compromiso'];
		$li_baseimponible=$_POST['baseimponible'];
		$ls_procededoc=$_POST['procededoc'];
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		$ls_parcial=$_POST['parcial'];
		$li_fila=0;
		$ls_confiva=$_SESSION["la_empresa"]["confiva"];
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $io_conexion->Concat('spg_cuentas.codestpro1','spg_cuentas.codestpro2','spg_cuentas.codestpro3','spg_cuentas.codestpro4','spg_cuentas.codestpro5','spg_cuentas.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos 
			                   							WHERE sss_permisos_internos.codemp='{$ls_codemp}' 
			                     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		if ($ls_confiva=="C")
		   {
			 $ls_sql = "SELECT sigesp_cargos.codcar, sigesp_cargos.dencar, sigesp_cargos.spg_cuenta,
					           sigesp_cargos.formula, sigesp_cargos.porcar, '' as codestpro, '' as sc_cuenta, '' as estcla
					      FROM sigesp_cargos, scg_cuentas
					     WHERE sigesp_cargos.codemp='".$ls_codemp."'
					       AND sigesp_cargos.codemp=scg_cuentas.codemp 
					       AND trim(sigesp_cargos.spg_cuenta)=trim(scg_cuentas.sc_cuenta)
					     ORDER BY sigesp_cargos.codcar";
		   }
		else
		   {
		     $ls_sql="SELECT sigesp_cargos.codcar, sigesp_cargos.dencar, sigesp_cargos.codestpro, sigesp_cargos.spg_cuenta,".
				     "       sigesp_cargos.formula, spg_cuentas.sc_cuenta, spg_cuentas.estcla,  sigesp_cargos.porcar ".
				     "  FROM sigesp_cargos, spg_cuentas".
				     " WHERE sigesp_cargos.codemp='".$ls_codemp."'".
				     "   AND sigesp_cargos.codemp=spg_cuentas.codemp ".$ls_filtroest.
				     "   AND substr(sigesp_cargos.codestpro,1,25) = spg_cuentas.codestpro1 ".
				     "   AND substr(sigesp_cargos.codestpro,26,25) = spg_cuentas.codestpro2 ".
				     "   AND substr(sigesp_cargos.codestpro,51,25) = spg_cuentas.codestpro3 ".
				     "   AND substr(sigesp_cargos.codestpro,76,25) = spg_cuentas.codestpro4 ".
				     "   AND substr(sigesp_cargos.codestpro,101,25) = spg_cuentas.codestpro5 ".
				     "   AND trim(sigesp_cargos.spg_cuenta)=trim(spg_cuentas.spg_cuenta) ".
					 "   AND sigesp_cargos.estcla=spg_cuentas.estcla ".
				     " ORDER BY sigesp_cargos.codcar";
		   }
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Otros Créditos ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			$lo_title[1]=" ";
			$lo_title[2]="Código";
			$lo_title[3]="Denominación";
			$lo_object[1][1]="";
			$lo_object[1][2]="";
			$lo_object[1][3]="";
			$lo_object[1][4]="";
			$lo_object[1][5]="";
			if ($ls_tipo=='CMPRET')
			   {
				 $lo_title[4]="Porcentaje"; 
				 $lo_title[5]="Fórmula"; 
			   }
			else
			   {
				 $lo_title[4]="Base Imponible"; 
				 $lo_title[5]="Monto Impuesto"; 
				 $lo_title[6]="Monto Ajuste"; 
			     $lo_object[1][6]="";
			   }
			if(array_key_exists("cargos",$_SESSION))
			{
				$ls_compromisoact=$io_ds_cargos->find("nrocomp",$ls_compromiso);
				if($ls_compromisoact==0)
				{
					$arrResultado=$io_recepcion->uf_load_cargos_compromiso($ls_compromiso,$ls_procededoc,$io_ds_cargos);
					$lb_valido=$arrResultado["valido"];
					$io_ds_cargos=$arrResultado["valor"];
				}
				else
				{
					$io_ds_cargos->data=$_SESSION["cargos"];
				}
			}
			else
			{   
				$arrResultado=$io_recepcion->uf_load_cargos_compromiso($ls_compromiso,$ls_procededoc,$io_ds_cargos);
				$lb_valido=$arrResultado["valido"];
				$io_ds_cargos=$arrResultado["valor"];
			}
			if($ls_parcial=="1")
			{
				if($ls_procededoc=="SPGCMP")
				{
					while($row=$io_sql->fetch_row($rs_data))
					{
						$lb_existe=true;
						$ls_codcar=$row["codcar"];
						$ls_dencar=$row["dencar"];
						$ls_formula=$row["formula"];
						$ls_codestpro=$row["codestpro"];
						$ls_estcla=$row["estcla"];
						$ls_spgcuenta=trim($row["spg_cuenta"]);
						$ls_scgcuenta=trim($row["sc_cuenta"]);
						$li_porcar=$row["porcar"];
						$ls_activo="";
						//$li_basimp=number_format($li_baseimponible,2,",",".");
						$li_basimp=$li_baseimponible;
						$li_monimp="0,00";
						$ls_codfuefin="--";
						$li_fila++;
						$lo_object[$li_fila][1]="<input name=chkcargos".$li_fila."  type=checkbox id=chkcargos".$li_fila." class=sin-borde  value='1' onClick=ue_calcular('".$li_fila."') ".$ls_activo.">";
						$lo_object[$li_fila][2]="<input name=txtcodcar".$li_fila."  type=text id=txtcodcar".$li_fila."     class=sin-borde  style=text-align:center size=8 value='".$ls_codcar."' title='".$ls_dencar."'  readonly>";
						$lo_object[$li_fila][3]="<input name=txtdencar".$li_fila."  type=text id=txtdencar".$li_fila."     class=sin-borde  style=text-align:center size=30 value='".$ls_dencar."' title='".$ls_dencar."' readonly>";
						$lo_object[$li_fila][4]="<input name=txtbaseimp".$li_fila." type=text id=txtbaseimp".$li_fila."    class=sin-borde  style=text-align:right  size=23 onBlur=ue_calcular('".$li_fila."'); onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_basimp."' >";
						$lo_object[$li_fila][5]="<input name=txtmonimp".$li_fila."  type=text id=txtmonimp".$li_fila."     class=sin-borde  style=text-align:right  size=23 value='".$li_monimp."' readonly>";
						$lo_object[$li_fila][6]="<input name=txtmonaju".$li_fila."  type=text id=txtmonaju".$li_fila."     class=sin-borde  style=text-align:right  size=6 onKeyPress=return(ue_formatonumero_negativo(this,'.',',',event)); value='0,00'>".
												"<input name=formula".$li_fila."    type=hidden id=formula".$li_fila."     value='".$ls_formula."'>".
												"<input name=codestpro".$li_fila."  type=hidden id=codestpro".$li_fila."   value='".$ls_codestpro."'>".
												"<input name=spgcuenta".$li_fila."  type=hidden id=spgcuenta".$li_fila."   value='".$ls_spgcuenta."'>".
												"<input name=sccuenta".$li_fila."   type=hidden id=sccuenta".$li_fila."    value='".$ls_scgcuenta."'>".
												"<input name=estcla".$li_fila."  type=hidden id=estcla".$li_fila."   value='".$ls_estcla."'>".
												"<input name=porcar".$li_fila."     type=hidden id=porcar".$li_fila."      value='".$li_porcar."'>".
												"<input name=procededoc".$li_fila." type=hidden id=procededoc".$li_fila."  value='".$ls_procededoc."'>".
												"<input name=codfuefin".$li_fila." type=hidden id=codfuefin".$li_fila."  value='".$ls_codfuefin."'>";
					}				
				}
				else
				{
					$lo_title[1]=" ";
					$lo_title[2]="Código";
					$lo_title[3]="Denominación";
					$lo_title[4]="Estructura"; 
					$lo_title[5]="Base Imponible"; 
					$lo_title[6]="Monto Impuesto"; 
					$lo_title[7]="Monto Ajuste"; 
					$li_totrowsession=1;
					$li_fila=0;
					for($li_filaaux=1;$li_filaaux<=$li_totrowsession;$li_filaaux++)
					{
						$ls_codcar=trim($io_ds_cargos->getValue("codcar",$li_filaaux));
						if($io_ds_cargos->getValue("codcar",$li_filaaux+1)!="")
						{
							$li_totrowsession++;
						}
						if(($ls_scgcuenta==="")&&($ls_confiva<>'C'))
						{			
							$io_mensajes->uf_mensajes_ajax("Error en Cuenta","ERROR->La Cuenta Presupuestaria Asociada al cargo no existe en la Estructura. Favor verifique en Configuracion->Cuentas Por Pagar->Otros Créditos el cargo ".$ls_codcar,false,""); 
						}
						$ls_dencar=$io_ds_cargos->getValue("dencar",$li_filaaux);
						$li_basimp=$li_baseimponible;
						$li_monimp=0;
						$ls_formula=$io_ds_cargos->getValue("formula",$li_filaaux);
						$ls_codestpro=$io_ds_cargos->getValue("codpro",$li_filaaux);
						$arrResultado=$io_funciones_cxp->uf_formatoprogramatica($ls_codestpro,$ls_codpro);
						$ls_codpro=$arrResultado["as_programatica"];
						$ls_spgcuenta=$io_ds_cargos->getValue("cuenta",$li_filaaux);
						$ls_estcla=$io_ds_cargos->getValue("estcla",$li_filaaux);
						$li_porcar=$io_ds_cargos->getValue("porcar",$li_filaaux);
						$ls_procededoc=$io_ds_cargos->getValue("procededoc",$li_filaaux);
						$ls_codfuefin=$io_ds_cargos->getValue("codfuefin",$li_filaaux);
						$ls_scgcuenta=$io_ds_cargos->getValue("sc_cuenta",$li_filaaux);
						$li_basimp=number_format($li_basimp,2,",",".");
						$li_monimp=number_format($li_monimp,2,",",".");
						$ls_activo="";
						if(($ls_codcar!="")||(($ls_scgcuenta==="")&&($ls_confiva<>'C')))
						{
							$li_fila++;
							$lo_object[$li_fila][1]="<input name=chkcargos".$li_fila."  type=checkbox id=chkcargos".$li_fila." class=sin-borde  value='1' onClick=ue_calcular('".$li_fila."') ".$ls_activo.">";
							$lo_object[$li_fila][2]="<input name=txtcodcar".$li_fila."  type=text id=txtcodcar".$li_fila."     class=sin-borde  style=text-align:center size=8 value='".$ls_codcar."' readonly>";
							$lo_object[$li_fila][3]="<input name=txtdencar".$li_fila."  type=text id=txtdencar".$li_fila."     class=sin-borde  style=text-align:center size=30 value='".$ls_dencar."' readonly>";
							$lo_object[$li_fila][4]="<input name=codpro".$li_fila." type=text id=codpro".$li_fila."    class=sin-borde  style=text-align:right  size=23 value='".$ls_codpro."' readonly>";
							$lo_object[$li_fila][5]="<input name=txtbaseimp".$li_fila." type=text id=txtbaseimp".$li_fila."    class=sin-borde  style=text-align:right  size=12 onBlur=ue_calcular('".$li_fila."'); onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_basimp."' >";
							$lo_object[$li_fila][6]="<input name=txtmonimp".$li_fila."  type=text id=txtmonimp".$li_fila."     class=sin-borde  style=text-align:right  size=12 value='".$li_monimp."' readonly>";
							$lo_object[$li_fila][7]="<input name=txtmonaju".$li_fila."  type=text id=txtmonaju".$li_fila."     class=sin-borde  style=text-align:right  size=6 onKeyPress=return(ue_formatonumero_negativo(this,'.',',',event)); value='0,00'>".
													"<input name=formula".$li_fila."    type=hidden id=formula".$li_fila."     value='".$ls_formula."'>".
													"<input name=codestpro".$li_fila."  type=hidden id=codestpro".$li_fila."   value='".$ls_codestpro."'>".
													"<input name=spgcuenta".$li_fila."  type=hidden id=spgcuenta".$li_fila."   value='".$ls_spgcuenta."'>".
													"<input name=sccuenta".$li_fila."   type=hidden id=sccuenta".$li_fila."    value='".$ls_scgcuenta."'>".
													"<input name=estcla".$li_fila."  type=hidden id=estcla".$li_fila."   value='".$ls_estcla."'>".
													"<input name=porcar".$li_fila."     type=hidden id=porcar".$li_fila."      value='".$li_porcar."'>".
													"<input name=procededoc".$li_fila." type=hidden id=procededoc".$li_fila."  value='".$ls_procededoc."'>".
													"<input name=codfuefin".$li_fila." type=hidden id=codfuefin".$li_fila."  value='".$ls_codfuefin."'>";
						}
					}
				}
			}// if parcial==1
			else
			{
				while($row=$io_sql->fetch_row($rs_data))
				{
					$lb_existe=true;
					$ls_codcar=$row["codcar"];
					$ls_dencar=$row["dencar"];
					$ls_formula=$row["formula"];
					$ls_codestpro=$row["codestpro"];
					$ls_estcla=$row["estcla"];
					$ls_spgcuenta=trim($row["spg_cuenta"]);
					$ls_scgcuenta=trim($row["sc_cuenta"]);
					$li_porcar=$row["porcar"];
					$ls_activo="";
					$li_basimp=number_format($li_baseimponible,2,",",".");
					$li_monimp="0,00";
					$ls_codfuefin="--";
					$li_row=$io_ds_cargos->findValues(array('codcar'=>$ls_codcar,'nrocomp'=>$ls_compromiso,'procededoc'=>$ls_procededoc),"codcar");
					if($li_row>0)
					{
						$ls_activo="checked";
						$li_basimp=number_format($io_ds_cargos->getValue("baseimp",$li_row),2,",",".");
						$li_monimp=number_format($io_ds_cargos->getValue("monimp",$li_row),2,",",".");
						$ls_codfuefin=$io_ds_cargos->getValue("codfuefin",$li_row);
					}
					else
					{
						$li_row=$io_ds_cargos->findValues(array('codpro'=>$ls_codestpro,'cuenta'=>$ls_spgcuenta),"codpro");
						if($li_row>0)
						{
							$ls_codfuefin=$io_ds_cargos->getValue("codfuefin",$li_row);
						}
					}
					if($ls_parcial=="1")
					{
					  if ($ls_confiva=="C")
						 {
						   $li_row=$io_ds_cargos->findValues(array('cuenta'=>$ls_spgcuenta),"cuenta");
						 }
					  else
						 {//print_r($io_ds_cargos->data);print "<br><br>";//print $ls_codcar."CODPRO->".$ls_codestpro."<br>";
						   $li_row=$io_ds_cargos->findValues(array('codcar'=>$ls_codcar,'cuenta'=>$ls_spgcuenta),"codcar");
						 }//print "----->".$li_row."<br>";
						if($li_row==-1)
						{
							$lb_existe=false;
						}
						else
						{
							$ls_codfuefin=$io_ds_cargos->getValue("codfuefin",$li_row);
							$ls_codestpro=$io_ds_cargos->getValue("codpro",$li_row);
							$ls_estcla=$io_ds_cargos->getValue("estcla",$li_row);
						}
					}
					if($lb_existe && empty($ls_tipo))
					{
						$li_fila++;
						$lo_object[$li_fila][1]="<input name=chkcargos".$li_fila."  type=checkbox id=chkcargos".$li_fila." class=sin-borde  value='1' onClick=ue_calcular('".$li_fila."') ".$ls_activo.">";
						$lo_object[$li_fila][2]="<input name=txtcodcar".$li_fila."  type=text id=txtcodcar".$li_fila."     class=sin-borde  style=text-align:center size=8 value='".$ls_codcar."' title='".$ls_dencar."'  readonly>";
						$lo_object[$li_fila][3]="<input name=txtdencar".$li_fila."  type=text id=txtdencar".$li_fila."     class=sin-borde  style=text-align:center size=30 value='".$ls_dencar."' title='".$ls_dencar."' readonly>";
						$lo_object[$li_fila][4]="<input name=txtbaseimp".$li_fila." type=text id=txtbaseimp".$li_fila."    class=sin-borde  style=text-align:right  size=23 onBlur=ue_calcular('".$li_fila."'); onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_basimp."' >";
						$lo_object[$li_fila][5]="<input name=txtmonimp".$li_fila."  type=text id=txtmonimp".$li_fila."     class=sin-borde  style=text-align:right  size=23 value='".$li_monimp."' readonly>";
						$lo_object[$li_fila][6]="<input name=txtmonaju".$li_fila."  type=text id=txtmonaju".$li_fila."     class=sin-borde  style=text-align:right  size=6 onKeyPress=return(ue_formatonumero_negativo(this,'.',',',event)); value='0,00'>".
												"<input name=formula".$li_fila."    type=hidden id=formula".$li_fila."     value='".$ls_formula."'>".
												"<input name=codestpro".$li_fila."  type=hidden id=codestpro".$li_fila."   value='".$ls_codestpro."'>".
												"<input name=spgcuenta".$li_fila."  type=hidden id=spgcuenta".$li_fila."   value='".$ls_spgcuenta."'>".
												"<input name=sccuenta".$li_fila."   type=hidden id=sccuenta".$li_fila."    value='".$ls_scgcuenta."'>".
												"<input name=estcla".$li_fila."  type=hidden id=estcla".$li_fila."   value='".$ls_estcla."'>".
												"<input name=porcar".$li_fila."     type=hidden id=porcar".$li_fila."      value='".$li_porcar."'>".
												"<input name=procededoc".$li_fila." type=hidden id=procededoc".$li_fila."  value='".$ls_procededoc."'>".
												"<input name=codfuefin".$li_fila." type=hidden id=codfuefin".$li_fila."  value='".$ls_codfuefin."'>";
					}
					elseif($ls_tipo=='CMPRET')
					{
					  $li_fila++;
					  $lo_object[$li_fila][1]="<input name=radiocargos           type=radio id=radiocargos".$li_fila." class=sin-borde  value='1'>";
					  $lo_object[$li_fila][2]="<input name=txtcodcar".$li_fila." type=text  id=txtcodcar".$li_fila."   class=sin-borde  style=text-align:center size=7  value='".trim($ls_codcar)."' title='".$ls_dencar."' readonly>";
					  $lo_object[$li_fila][3]="<input name=txtdencar".$li_fila." type=text  id=txtdencar".$li_fila."   class=sin-borde  style=text-align:left   size=60 value='".$ls_dencar."'    title='".$ls_dencar."'    readonly>";
					  $lo_object[$li_fila][4]="<input name=porcar".$li_fila."    type=text  id=porcar".$li_fila."      class=sin-borde  style=text-align:right  size=7  value='".number_format($li_porcar,2,',','.')."'       readonly>";
					  $lo_object[$li_fila][5]="<input name=formula".$li_fila."   type=text  id=formula".$li_fila."     class=sin-borde  style=text-align:left   size=20 value='".$ls_formula."'      readonly>";
					} 
				}
			}
			$io_sql->free_result($rs_data);
			if ($ls_tipo=='CMPRET')
			   {
			     echo"<table width=534 border=0 align=center cellpadding=0 cellspacing=0>";
    			 echo "<tr>";
      			 echo "<td width=532 colspan=6 align=center bordercolor=#FFFFFF>";
        		 echo "<div align=center class=Estilo2>";
          		 echo "<p align=right>&nbsp;&nbsp;&nbsp;<a href='javascript: uf_aceptar_creditos($li_fila);'><img src='../shared/imagebank/tools20/aprobado.gif' alt='Aceptar' width=20 height=20 border=0>Agregar Otros Cr&eacute;dito</a></p>";
      			 echo "</div></td>";
    			 echo "</tr>";
  				 echo "</table>";
			   }
			$io_grid->makegrid($li_fila,$lo_title,$lo_object,580,"","gridcargos");
			if ($ls_tipo!='CMPRET')
			   {
				 print "  <table width='580' border='0' align='center' cellpadding='0' cellspacing='0'>";
				 print "    <tr>";
				 print "		<td  align='right'> ";
				 print "		   <a href='javascript:ue_ajustar();'><img src='../shared/imagebank/tools20/actualizar.jpg' width='20' height='20' border='0' title='Ajustar'>Ajustar</a>&nbsp;&nbsp;";
				 print "		   <a href='javascript:ue_aceptar();'><img src='../shared/imagebank/tools20/ejecutar.gif' width='20' height='20' border='0' title='Procesar'>Procesar</a>&nbsp;&nbsp;";
				 print "		   <a href='javascript:ue_cerrar();'><img src='../shared/imagebank/tools/eliminar.gif' width='20' height='20' border='0' title='Canccelar'>Cancelar</a>&nbsp;&nbsp;";
				 print "		</td>";
				 print "    </tr>";
				 print "  </table>";
			   }
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_otroscreditos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_deducciones()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_deducciones
		//		   Access: private
		//	    Arguments: 
		//	  Description: Método que inprime el resultado de la busqueda de las cdeducciones a aplicar en la recepción de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 22/04/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp, $io_grid, $io_ds_deducciones;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();
		require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
		$io_ds_deducciones=new class_datastore(); // Datastored de cuentas contables
				
		$ls_numrecdoc=$_POST['numrecdoc'];
		$li_subtotal=$_POST['subtotal'];
		$li_cargos=$_POST['cargos'];
		$ls_procede=$_POST['procede'];
		$ls_presupuestario=$_POST['presupuestario'];
		$ls_contable=$_POST['contable'];
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_modageret = $_SESSION["la_empresa"]["modageret"];
		$ls_estretmil = $_SESSION["la_empresa"]["estretmil"];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipdes=$_POST['tipdes'];
		$ls_codproben=$_POST['codproben'];
		$ls_tipo=$_POST['tipo'];
		$li_fila=0;
		$ls_deduccion="";
		$ls_disabled="";
		$ls_dedconproben = $_SESSION["la_empresa"]["dedconproben"];
		if ($ls_dedconproben=="1")
		{
			$ls_disabled="true";
			switch ($ls_tipdes)
			{
				case "P";
					$ls_deduccion="  INNER JOIN rpc_deduxprov ".
								  "    ON rpc_deduxprov.cod_pro = '".$ls_codproben."' ".
								  "   AND sigesp_deducciones.codemp = rpc_deduxprov.codemp ".
								  "   AND sigesp_deducciones.codded = rpc_deduxprov.codded ";
				break;
					
				case "B":
					$ls_deduccion="  INNER JOIN rpc_deduxbene ".
								  "    ON rpc_deduxbene.ced_bene = '".$ls_codproben."' ".
								  "   AND sigesp_deducciones.codemp = rpc_deduxbene.codemp ".
								  "   AND sigesp_deducciones.codded = rpc_deduxbene.codded ";
					break;
			}
		}
		if($ls_modageret=='C')
		{
			$ls_aux="";
			$ls_aux2=" OR estretmun=1 OR estretmil='1' "	;		
		}
		else
		{
			$ls_aux=" WHERE estretmun<>1  OR estretmil='1' ";
			$ls_aux2="";		
		}
		if($ls_estretmil=='C')
		{
			$ls_aux="";
			$ls_aux2="  OR estretmil='1' "	;		
		}
		if ($ls_tipo=='CMPRETIVA') 
		   {
		     $ls_aux = " WHERE iva=1 AND estretmun=0 AND islr=0 AND estretmil='0'";
		   }
		elseif($ls_tipo=='CMPRETMUN')
		   {
		     $ls_aux = " WHERE estretmun=1 AND iva=0 AND islr=0  AND estretmil='0'";
		   }
		   elseif($ls_tipo=='CMPRETAPO')
		   {
		     $ls_aux = " WHERE estretmun=0 AND iva=0 AND islr=0 AND retaposol=1  AND estretmil='0'";
		   }
		   elseif($ls_tipo=='CMPRETMIL')
		   {
		     $ls_aux = " WHERE estretmun=0 AND iva=0 AND islr=0 AND retaposol=0 AND estretmil='1'";
		   }
		   elseif($ls_tipo=='ISLR')
		   {
		     $ls_aux = " WHERE estretmun=0 AND iva=0 AND islr=1 AND retaposol=0 AND estretmil='0'";
		   }
		if(($ls_contable=="1")&&(($ls_presupuestario=="3")||($ls_presupuestario=="4")))
		{
			$ls_sql="SELECT sigesp_deducciones.codded,dended,formula,porded,monded,sc_cuenta,islr,iva,estretmun,retaposol,estretmil,sc_ctaasu ".
					"  FROM sigesp_deducciones ".
					$ls_deduccion.
					" WHERE islr=1 OR estretmun=1 OR islr=1 OR estretmil='1' OR otras=1".
					$ls_aux2."  ".
					" ORDER BY codded ASC ";
		}
		else
		{
			$ls_sql="SELECT sigesp_deducciones.codded,dended,formula,porded,monded,sc_cuenta,islr,iva,estretmun,retaposol,estretmil,sc_ctaasu ".
					"  FROM sigesp_deducciones ".
					$ls_deduccion.
					$ls_aux.							   
					" ORDER BY codded ASC ";
		}
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Deducciones ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			$lo_title[1]=" ";
			$lo_title[2]="Código";
			$lo_title[3]="Denominación";
			if ($ls_tipo=='CMPRETIVA' || $ls_tipo=='CMPRETMUN'|| $ls_tipo=='CMPRETAPO')
			   {
			     $lo_title[4]="Porcentaje";
			     $lo_title[5]="Fórmula"; 
			   }
			else
			   {
				 $lo_title[4]="Monto Objeto Retención"; 
				 $lo_title[5]="Monto Retención"; 
			   }
			if(array_key_exists("deducciones",$_SESSION))
			{
				$io_ds_deducciones->data=$_SESSION["deducciones"];
			}	
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li_fila=$li_fila+1;
			    $ls_codded=$row["codded"];
				$ls_dended=$row["dended"];
				$ls_formula=$row["formula"];
				$li_monded=number_format($row["monded"],2,',','.');
				$ld_porded=$row["porded"];
				$ls_cuenta=$row["sc_cuenta"];				
				$li_iva=$row["iva"]; 
				$li_islr=$row["islr"]; 
				$li_estretmun=$row["estretmun"];
				$li_estaposol=$row["retaposol"]; 
				$li_estretmil=$row["estretmil"]; 
				$ls_ctaasu=$row["sc_ctaasu"];				
				$ls_activo=""; 
				$li_monobjret=0;
				$li_monret="0,00";
				if($li_iva=='1')
				{
					$li_monobjret=$li_cargos;
				}
				else
				{
					$li_monobjret=number_format($li_subtotal,2,',','.');
					
				}
				$li_row=$io_ds_deducciones->findValues(array('codded'=>$ls_codded),"codded");
				if($li_row>0)
				{
					$ls_activo="checked";
					$li_monobjret=$io_ds_deducciones->getValue("monobjret",$li_row);
					$li_monret=$io_ds_deducciones->getValue("monret",$li_row);
				}
				else
				{
					if ($ls_dedconproben=="1")
					{
						$ls_activo="checked";
						require_once("../../shared/class_folder/evaluate_formula.php");
						$io_formula = new evaluate_formula();
						$ldec_monto = str_replace('.','',$li_monobjret);
						$ldec_monto = str_replace(',','.',$ldec_monto);
						$li_monret   = $io_formula->uf_evaluar($ls_formula,$ldec_monto,$lb_valido);
						$li_monret=number_format($li_monret,2,',','.');
						unset($io_formula);
					}
				}
				if ($ls_tipo!='CMPRETIVA' && $ls_tipo!='CMPRETMUN' && $ls_tipo!='CMPRETAPO' && $ls_tipo!='CMPRETMIL'&& $ls_tipo!='ISLR')
				{
					$lo_object[$li_fila][1]="<input name=chkdeduccion".$li_fila."  type=checkbox id=chkdeduccion".$li_fila." class=sin-borde  value='1' onClick=javascript:ue_calcular('".$li_fila."','".$ls_disabled."') ".$ls_activo.">";
					$lo_object[$li_fila][2]="<input name=txtcodded".$li_fila."  type=text id=txtcodded".$li_fila."     class=sin-borde  style=text-align:center size=8 value='".$ls_codded."'   title ='".$ls_dended."' readonly><input name=txtmonded".$li_fila." type=hidden id=txtmonded".$li_fila." value='".$li_monded."'>";
					$lo_object[$li_fila][3]="<input name=txtdended".$li_fila."  type=text id=txtdended".$li_fila."     class=sin-borde  style=text-align:left size=35 value='".$ls_dended."'  title ='".$ls_dended."' readonly>";
					if(($li_monobjret=="0,00")&&($li_iva==1)&&($li_islr==0))
					{
						$lo_object[$li_fila][1]="<input name=chkdeduccion".$li_fila."  type=checkbox id=chkdeduccion".$li_fila." class=sin-borde  value='1' onClick=javascript:ue_calcular('".$li_fila."','".$ls_disabled."') ".$ls_activo." disabled>";
						$lo_object[$li_fila][4]="<input name=txtmonobjret".$li_fila." type=text id=txtmonobjret".$li_fila."    class=sin-borde  style=text-align:right  size=23 onBlur=ue_calcular('".$li_fila."','".$ls_disabled."'); onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_monobjret."' readonly>";
					}
					else
					{
						$lo_object[$li_fila][4]="<input name=txtmonobjret".$li_fila." type=text id=txtmonobjret".$li_fila."    class=sin-borde  style=text-align:right  size=23 onBlur=ue_calcular('".$li_fila."','".$ls_disabled."'); onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_monobjret."' >";
					}
					$lo_object[$li_fila][5]="<input name=txtmonret".$li_fila."  type=text id=txtmonret".$li_fila."     class=sin-borde  style=text-align:right  size=23 onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_monret."' >".
											"<input name=formula".$li_fila."    type=hidden id=formula".$li_fila."     value='".$ls_formula."'>".
											"<input name=sccuenta".$li_fila."   type=hidden id=sccuenta".$li_fila."    value='".$ls_cuenta."'><input name=txtctaasu".$li_fila." type=hidden id=txtctaasu".$li_fila." value='".$ls_ctaasu."'>".
											"<input name=porded".$li_fila."     type=hidden id=porded".$li_fila."      value='".$ld_porded."'>".
											"<input name=txtislr".$li_fila."    type=hidden id=txtislr".$li_fila."      value='".$li_islr."'>".
				 						    "<input name=txtiva".$li_fila."     type=hidden  id=txtiva".$li_fila."    	 value='".$li_iva."'>";
			    }
			    else
				{
				  $lo_object[$li_fila][1]="<input name=radiodeduccion        type=radio id=radiodeduccion".$li_fila." class=sin-borde>";
				  $lo_object[$li_fila][2]="<input name=txtcodded".$li_fila." type=text  id=txtcodded".$li_fila."      class=sin-borde  style=text-align:center size=7   value='".$ls_codded."'  title ='".$ls_dended."' readonly>";
				  $lo_object[$li_fila][3]="<input name=txtdended".$li_fila." type=text  id=txtdended".$li_fila."      class=sin-borde  style=text-align:left   size=40  value='".$ls_dended."'  title ='".$ls_dended."' readonly>";
				  $lo_object[$li_fila][4]="<input name=porded".$li_fila."    type=text  id=porded".$li_fila."    	  class=sin-borde  style=text-align:right  size=7   value='".number_format($ld_porded,2,',','.')."'  readonly >";
				  $lo_object[$li_fila][5]="<input name=formula".$li_fila."   type=text  id=formula".$li_fila."        class=sin-borde  style=text-align:left   size=50  value='".$ls_formula."' readonly>";
				}
			}
			$io_sql->free_result($rs_data);
			if ($ls_tipo=='CMPRETIVA' || $ls_tipo=='CMPRETMUN'|| $ls_tipo=='CMPRETAPO'|| $ls_tipo=='CMPRETMIL' || $ls_tipo=='ISLR')
			   {
			     echo"<table width=534 border=0 align=center cellpadding=0 cellspacing=0>";
    			 echo "<tr>";
      			 echo "<td width=532 colspan=6 align=center bordercolor=#FFFFFF>";
        		 echo "<div align=center class=Estilo2>";
          		 echo "<p align=right>&nbsp;&nbsp;&nbsp;<a href='javascript: uf_aceptar_deducciones($li_fila);'><img src='../shared/imagebank/tools20/aprobado.gif' alt='Aceptar' width=20 height=20 border=0>Agregar Deducciones</a></p>";
      			 echo "</div></td>";
    			 echo "</tr>";
  				 echo "</table>";
			   }
			$io_grid->makegrid($li_fila,$lo_title,$lo_object,580,"","griddeduccion");
			if ($ls_tipo!='CMPRETIVA' && $ls_tipo!='CMPRETMUN'&& $ls_tipo!='CMPRETAPO'&& $ls_tipo!='CMPRETMIL'&& $ls_tipo!='ISLR')
			   {
				 print "  <table width='580' border='0' align='center' cellpadding='0' cellspacing='0'>";
				 print "    <tr>";
				 print "		<td  align='right'> ";
				 print "		   <a href='javascript:ue_aceptar();'><img src='../shared/imagebank/tools20/ejecutar.gif' width='20' height='20' border='0' title='Procesar'>Procesar</a>&nbsp;&nbsp;";
				 print "		   <a href='javascript:ue_cerrar();'><img src='../shared/imagebank/tools/eliminar.gif' width='20' height='20' border='0' title='Canccelar'>Cancelar</a>&nbsp;&nbsp;";
				 print "		</td>";
				 print "    </tr>";
				 print "  </table>";
			   }
		}
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_deducciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_recepcion()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_recepcion
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de recepciones de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/05/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
		$ls_clactacon=$_SESSION["la_empresa"]["clactacon"];	
		$ls_numrecdoc="%".$_POST['numrecdoc']."%";
		$ls_estprodoc="%".$_POST['estprodoc']."%";
		$ls_codcla="%".$_POST['codcla']."%";
		$ls_procedencia=$io_funciones_cxp->uf_obtenervalor("procedencia","");
		$ls_repcajchi=$io_funciones_cxp->uf_obtenervalor("repcajchi","");
		if($ls_codcla=="%--%")
		{
			$ls_codcla="%%";
		}
		$ld_fecregdes=$io_funciones->uf_convertirdatetobd($_POST['fecregdes']);
		$ld_fecreghas=$io_funciones->uf_convertirdatetobd($_POST['fecreghas']);
		$ls_tipdes=$_POST['tipdes'];
		$ls_codproben=$_POST['codproben'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		$ls_repcon = $_POST['repcon'];
		if ($ls_repcajchi!=1)
		{
			switch($ls_tipdes)
			{
				case "P":
					$ls_codpro=$ls_codproben;
					$ls_cedben="----------";
					break;
					
				case "B":
					$ls_codpro="----------";
					$ls_cedben=$ls_codproben;
					break;
				
				default:
					$ls_codpro="";
					$ls_cedben="";
					break;
			}
		}
		else
		{
			$ls_codpro="";
			$ls_cedben="";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		$ls_filtrofrom="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1' && $ls_repcon=='0') {
			$ls_estconcat = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                "  							WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		
		//FILTRAR SOLO RECEPCIONES CONTABLES
		if($ls_repcon=='1') {
			$ls_filtroest = " AND cxp_documento.estcon=1 AND cxp_documento.estpre=4";
		}
		//FILTRAR SOLO RECEPCIONES CONTABLES
		$ls_criterio="";
		if($ls_procedencia!="RECEPCION")
		{
			if(($ls_codpro!="")&&($ls_cedben!=""))
			{
				$ls_concat1 = $io_conexion->Concat('cxp_rd.codemp','cxp_rd.numrecdoc','cxp_rd.codtipdoc','cxp_rd.cod_pro','cxp_rd.ced_bene');
				$ls_concat2 = $io_conexion->Concat('cxp_dt_solicitudes.codemp','cxp_dt_solicitudes.numrecdoc','cxp_dt_solicitudes.codtipdoc','cxp_dt_solicitudes.cod_pro','cxp_dt_solicitudes.ced_bene');
				if($ls_conrecdoc)
				{

					$ls_estprord='C';
				}
				else
				{
					$ls_estprord='R';
				}
				$ls_criterio="   AND cxp_rd.cod_pro='".$ls_codpro."'".
							 "   AND cxp_rd.ced_bene='".$ls_cedben."'".
							 "   AND estaprord=1".
							 "   AND estprodoc='".$ls_estprord."' ".
							 "   AND ".$ls_concat1." NOT IN (SELECT ".$ls_concat2."".
							 " 								   FROM cxp_solicitudes,cxp_dt_solicitudes".
							 "                                WHERE cxp_dt_solicitudes.codemp='".$ls_codemp."'".
							 "                                  AND cxp_dt_solicitudes.numrecdoc like '".$ls_numrecdoc."'".
							 "                                  AND cxp_dt_solicitudes.cod_pro='".$ls_codpro."'".
							 "                                  AND cxp_dt_solicitudes.ced_bene='".$ls_cedben."'".
							 "                                  AND cxp_solicitudes.estprosol<>'A' AND cxp_solicitudes.estprosol<>'N'".
							 " 									AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp".
							 "									AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol)";
			}
		}
		else
		{
			if(($ls_codpro!="")&&($ls_cedben!=""))
			{
				$ls_criterio="   AND cxp_rd.cod_pro='".$ls_codpro."'".
							 "   AND cxp_rd.ced_bene='".$ls_cedben."'";
			}
		}
		if($ls_tipo=="SOLICITUDPAGO")
		{
			$ls_numordpagmin=$_POST['numordpagmin'];
			$ls_codtipfon=$_POST['codtipfon'];
			//print "REPCAJCHI--->  ".$ls_repcajchi."<br>";
			if ($ls_repcajchi==1)
			{
				if(($ls_numordpagmin!="")&&($ls_codtipfon!="")&&($ls_numordpagmin!="-")&&($ls_codtipfon!="----"))
				{
					$ls_criterio=$ls_criterio."AND cxp_rd.numordpagmin='".$ls_numordpagmin."' AND cxp_rd.codtipfon='".$ls_codtipfon."' AND cxp_rd.repcajchi='".$ls_repcajchi."'";
				}
				else
				{
					//$ls_criterio=$ls_criterio."AND cxp_rd.repcajchi='".$ls_repcajchi."'";
					$ls_criterio=$ls_criterio."AND cxp_rd.estprodoc<> 'E' AND cxp_rd.estprodoc<> 'C' AND cxp_rd.repcajchi='".$ls_repcajchi."'";
				}
			}
			else
			{
				if(($ls_numordpagmin!="")&&($ls_codtipfon!="")&&($ls_numordpagmin!="-")&&($ls_codtipfon!="----"))
				{
					$ls_criterio=$ls_criterio."AND cxp_rd.numordpagmin='".$ls_numordpagmin."' AND cxp_rd.codtipfon='".$ls_codtipfon."' AND cxp_rd.repcajchi='0' ";
				}
				else
				{
					$ls_criterio=$ls_criterio."AND cxp_rd.repcajchi='".$ls_repcajchi."'";
				}
			}
		}
		
		$ls_cadena = $io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		
        $ls_sql="SELECT DISTINCT trim(cxp_rd.numrecdoc) as numrecdoc, cxp_rd.codtipdoc, trim(cxp_rd.ced_bene) as ced_bene, cxp_rd.cod_pro, codcla, dencondoc, 
		                fecemidoc, fecregdoc, fecvendoc, montotdoc, ".
				"		mondeddoc, moncardoc, tipproben, numref, estprodoc, procede, estlibcom, estaprord, estimpmun, codrecdoc, ".
				"		estcon, estpre,tipdocdon, cxp_documento.dentipdoc, cxp_rd.codfuefin, sigesp_fuentefinanciamiento.denfuefin, ".
				"       coduniadm,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,cxp_rd.estcla,estact,numordpagmin,codtipfon,".
				"		repcajchi,codproalt,cxp_rd.conanurd,cxp_rd.tipdoctesnac,cxp_rd.numexprel,cxp_rd.estretasu,".
				"       (SELECT denuniadm FROM spg_unidadadministrativa".
				"         WHERE spg_unidadadministrativa.codemp=cxp_rd.codemp".
				"           AND spg_unidadadministrativa.coduniadm= cxp_rd.coduniadm) AS denuniadm,".
				"       (SELECT cxp_clasificador_rd.sc_cuenta FROM cxp_clasificador_rd".
				"         WHERE cxp_rd.codcla= cxp_clasificador_rd.codcla) AS ctaclard,".
				"		(CASE tipproben WHEN 'P' THEN (SELECT nompro FROM rpc_proveedor ".
				"									   WHERE rpc_proveedor.codemp = cxp_rd.codemp ".
				"										 AND rpc_proveedor.cod_pro = cxp_rd.cod_pro) ".
				"								 ELSE (SELECT ".$ls_cadena." FROM rpc_beneficiario ".
				"									    WHERE rpc_beneficiario.codemp = cxp_rd.codemp ".
				"									 	  AND rpc_beneficiario.ced_bene = cxp_rd.ced_bene) ".
				"		  END) AS nombre, ".
				"		(CASE tipproben WHEN 'P' THEN (SELECT rifpro FROM rpc_proveedor ".
				"									   WHERE rpc_proveedor.codemp = cxp_rd.codemp ".
				"										 AND rpc_proveedor.cod_pro = cxp_rd.cod_pro) ".
				"								 ELSE (SELECT rifben FROM rpc_beneficiario ".
				"									    WHERE rpc_beneficiario.codemp = cxp_rd.codemp ".
				"									 	  AND rpc_beneficiario.ced_bene = cxp_rd.ced_bene) ".
				"		  END) AS rif, ".
				"		(CASE tipproben WHEN 'P' THEN (SELECT trim(sc_cuenta) FROM rpc_proveedor ".
				"									   WHERE rpc_proveedor.codemp = cxp_rd.codemp ".
				"										 AND rpc_proveedor.cod_pro = cxp_rd.cod_pro) ".
				"								 ELSE (SELECT trim(sc_cuenta) FROM rpc_beneficiario ".
				"									    WHERE rpc_beneficiario.codemp = cxp_rd.codemp ".
				"									 	  AND rpc_beneficiario.ced_bene = cxp_rd.ced_bene) ".
				"		  END) AS sc_cuenta, ".
				"		(CASE tipproben WHEN 'P' THEN (SELECT trim(sc_cuentarecdoc) FROM rpc_proveedor ".
				"									   WHERE rpc_proveedor.codemp = cxp_rd.codemp ".
				"										 AND rpc_proveedor.cod_pro = cxp_rd.cod_pro) ".
				"								 ELSE (SELECT trim(sc_cuentarecdoc) FROM rpc_beneficiario ".
				"									    WHERE rpc_beneficiario.codemp = cxp_rd.codemp ".
				"									 	  AND rpc_beneficiario.ced_bene = cxp_rd.ced_bene) ".
				"		  END) AS sc_cuentarecdoc, ".
				"		(CASE tipproben WHEN 'P' THEN (SELECT tipconpro FROM rpc_proveedor ".
				"									   WHERE rpc_proveedor.codemp = cxp_rd.codemp ".
				"										 AND rpc_proveedor.cod_pro = cxp_rd.cod_pro) ".
				"								 ELSE (SELECT tipconben FROM rpc_beneficiario ".
				"									    WHERE rpc_beneficiario.codemp = cxp_rd.codemp ".
				"									 	  AND rpc_beneficiario.ced_bene = cxp_rd.ced_bene) ".
				"		  END) AS tipocont, ".
				"       (SELECT nompro FROM rpc_proveedor".
				"	      WHERE rpc_proveedor.codemp = cxp_rd.codemp ".
				"			AND rpc_proveedor.cod_pro = cxp_rd.codproalt) as nomproalt ".
				"  FROM cxp_rd, cxp_documento, sigesp_fuentefinanciamiento  ".$ls_filtrofrom.
                " WHERE cxp_rd.codemp = '".$ls_codemp."' ".
				"   AND cxp_rd.numrecdoc LIKE '".$ls_numrecdoc."' ".
				"   AND cxp_rd.estprodoc LIKE '".$ls_estprodoc."' ".
				"   AND cxp_rd.codcla LIKE '".$ls_codcla."' ".
				"   AND cxp_rd.fecregdoc >= '".$ld_fecregdes."' ".
				"   AND cxp_rd.fecregdoc <= '".$ld_fecreghas."' ".
				$ls_criterio.
				"	AND cxp_rd.codtipdoc = cxp_documento.codtipdoc ".
				"	AND cxp_rd.codemp = sigesp_fuentefinanciamiento.codemp ".
				"	AND cxp_rd.codfuefin = sigesp_fuentefinanciamiento.codfuefin ".$ls_filtroest.
				" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Recepciones de Documentos ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=600 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td style='cursor:pointer' title='Ordenar por Recepción' align='center' onClick=ue_orden('numrecdoc')>Nro Recepción</td>";
			print "<td style='cursor:pointer' title='Ordenar por Proveedor/Beneficiario' align='center' onClick=ue_orden('nombre')>Proveedor/Beneficiario</td>";
			print "<td style='cursor:pointer' title='Ordenar por Fecha de Registro' align='center' onClick=ue_orden('fecregdoc')>Fecha Registro</td>";
			print "<td style='cursor:pointer' title='Ordenar por Estatus' align='center' onClick=ue_orden('estprodoc')>Estatus</td>";
			print "<td style='cursor:pointer' title='Ordenar por Monto Total' align='center' onClick=ue_orden('montotdoc')>Monto Total</td>";
			print "</tr>";
			$li_i=0;
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li_i++;
				$ls_numrecdoc=$row["numrecdoc"];
				$ls_codtipdoc=$row["codtipdoc"];
				$ls_cedbene=$row["ced_bene"];
				$ls_codpro=$row["cod_pro"];
				$ls_codcla=$row["codcla"];
				$ls_estact=$row["estact"];
				$ls_dencondoc=$row["dencondoc"];
				$ld_fecemidoc=date("d/m/Y",strtotime($row["fecemidoc"]));
				$ld_fecregdoc=date("d/m/Y",strtotime($row["fecregdoc"]));
				$ld_fecvendoc=date("d/m/Y",strtotime($row["fecvendoc"]));
				$li_montotdoc=$row["montotdoc"];
				$li_mondeddoc=$row["mondeddoc"];
				$li_moncardoc=$row["moncardoc"];
				$ls_tipproben=$row["tipproben"];
				$ls_numref=$row["numref"];
				$ls_estprodoc=$row["estprodoc"];
				$ls_procede=$row["procede"];
				$ls_estlibcom=$row["estlibcom"];
				$ls_estaprord=$row["estaprord"];
				$ls_estimpmun=$row["estimpmun"];
				$ls_nombre=$row["nombre"];
				$ls_rif=$row["rif"];
				$ls_repcajchi=$row["repcajchi"];
				$ls_tipdoctesnac=$row["tipdoctesnac"];
				$ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
				switch($ls_conrecdoc)
				{
					case "0":
						$ls_sccuenta=$row["sc_cuenta"];
						break;
					
					case "1":
						$ls_sccuenta=$row["sc_cuentarecdoc"];
						break;
				}
				if(($ls_clactacon==1)&&($ls_codcla!="--"))
				{
					$ls_sccuenta=$row["ctaclard"];
				}
				$ls_tipocont=$row["tipocont"];
				$ls_estcon=$row["estcon"];
				$ls_estpre=$row["estpre"];
				$ls_tipdocdon=$row["tipdocdon"];
				$ls_dentipdoc=$row["dentipdoc"];
				$ls_codfuefin=$row["codfuefin"];
				$ls_denfuefin=$row["denfuefin"];
				$ls_codrecdoc=$row["codrecdoc"];
				$ls_coduniadm=$row["coduniadm"];
				$ls_denuniadm=$row["denuniadm"];
				$ls_codestpro1=$row["codestpro1"];
				$ls_codestpro2=$row["codestpro2"];
				$ls_codestpro3=$row["codestpro3"];
				$ls_codestpro4=$row["codestpro4"];
				$ls_codestpro5=$row["codestpro5"];
				$ls_estcla=$row["estcla"];
				$ls_numordpagmin=$row["numordpagmin"];
				$ls_codtipfon=$row["codtipfon"];
				$ls_codproalt=$row["codproalt"];
				$ls_nomproalt=$row["nomproalt"];
				$ls_conanurd=$row["conanurd"];
				$ls_numexprel=$row["numexprel"];
				$ls_estretasu=$row["estretasu"];
				switch($ls_estprodoc)
				{
					case "R": 
						$ls_estatus="Recibida";
						break;
					case "E": 
						$ls_estatus="Emitida";
						break;
					case "C": 
						$ls_estatus="Contabilizada";
						break;
					case "A": 
						$ls_estatus="Anulada";
						break;
				}
				switch ($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td with='100'><a href=\"javascript:ue_aceptar('".$ls_numrecdoc."','".$ls_codtipdoc."','".$ls_cedbene."','".$ls_codpro."',";
						print "'".$ls_codcla."','".$ld_fecemidoc."','".$ld_fecregdoc."','".$ld_fecvendoc."','".$li_montotdoc."',";
						print "'".$li_mondeddoc."','".$li_moncardoc."','".$ls_tipproben."','".$ls_numref."','".$ls_estprodoc."','".$ls_procede."',";
						print "'".$ls_estlibcom."','".$ls_estaprord."','".$ls_estimpmun."','".$ls_nombre."','".$ls_rif."','".$ls_sccuenta."','".$ls_tipocont."',";
						print "'".$ls_estcon."','".$ls_estpre."','".$li_i."','".$ls_estatus."','".$ls_codfuefin."','".$ls_denfuefin."','".$ls_codrecdoc."',";
						print "'".$ls_coduniadm."','".$ls_denuniadm."','".$ls_codestpro1."','".$ls_codestpro2."','".$ls_codestpro3."','".$ls_codestpro4."',";
						print "'".$ls_codestpro5."','".$ls_estcla."','".$ls_estact."','".$ls_numordpagmin."','".$ls_codtipfon."','".$ls_repcajchi."',";
						print "'".$ls_codproalt."','".$ls_nomproalt."','".$ls_conanurd."','".$ls_tipdocdon."','".$ls_tipdoctesnac."','".$ls_numexprel."','".$ls_estretasu."');\">".$ls_numrecdoc."</a></td>";
						print "<td with='200'>".$ls_nombre."</td>";
						print "<td with='100' align='center'>".$ld_fecregdoc."</td>";
						print "<td with='100' align='center'>".$ls_estatus."</td>";
						print "<td with='100' align='right'><input name='txtdencondoc".$li_i."' type='hidden' id='txtdencondoc".$li_i."' value='".$ls_dencondoc."'>".number_format($li_montotdoc,2,",",".")."</td>";
						print "</tr>";
					break;
					
					case "SOLICITUDPAGO":
						if($ls_estretasu=="1")
						{
							$li_montotdoc=	$li_montotdoc+$li_mondeddoc;
						}
						$li_montotdoc=number_format($li_montotdoc,2,",",".");
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: ue_aceptar_solicitud('".$ls_numrecdoc."','".$ls_codtipdoc."','".$ls_dentipdoc."','".$li_montotdoc."','".$ls_cedbene."','".$ls_codpro."','".$ls_codproalt."','".$ls_tipdoctesnac."','".$ls_numexprel."','".$li_i."');\">".$ls_numrecdoc."</a></td>";
						print "<td with='200'>".$ls_nombre."</td>";
						print "<td with='100' align='center'>".$ld_fecregdoc."</td>";
						print "<td with='100' align='center'>".$ls_estatus."</td>";
						print "<td with='100' align='right'><input name='txtdencondoc".$li_i."' type='hidden' id='txtdencondoc".$li_i."' value='".$ls_dencondoc."'>".$li_montotdoc."</td>";
						print "</tr>";
						break;
					
					case "REPORTE_UBICACION":
						$li_montotdoc=number_format($li_montotdoc,2,",",".");
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: ue_aceptar_ubicacion('".$ls_numrecdoc."');\">".$ls_numrecdoc."</a></td>";
						print "<td with='200'>".$ls_nombre."</td>";
						print "<td with='100' align='center'>".$ld_fecregdoc."</td>";
						print "<td with='100' align='center'>".$ls_estatus."</td>";
						print "<td with='100' align='right'>".$li_montotdoc."</td>";
						print "</tr>";
						break;
				}
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_compromisos()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_compromisos
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de compromisos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 09/05/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		require_once("sigesp_cxp_c_recepcion.php");
		$io_recepcion=new sigesp_cxp_c_recepcion("../../");
		$ls_numdoc=$_POST['numdoc'];
		$ls_codtipdoc=$_POST["codtipdoc"];
		$ls_codigo=$_POST['codigo'];
		$ls_tipodes=$_POST['tipodes'];
		$ls_confiva=$_SESSION["la_empresa"]["confiva"];
		$ld_fechareg=$io_funciones->uf_convertirdatetobd($_POST['fechareg']);
		$ls_varcomrd=$_SESSION["la_empresa"]["valcomrd"];
		$ls_estafenc  = $_SESSION["la_empresa"]["estafenc"];	
		$ls_estcomobr=$_SESSION["la_empresa"]["estcomobr"];
		$ls_numdoccom=$_POST['numdoccom'];
		switch($ls_tipodes)
		{
			case "P":
				$ls_codprov=$ls_codigo;
				$ls_cedbene="----------";
				break;
			case "B":
				$ls_codprov="----------";
				$ls_cedbene=$ls_codigo;
				break;
		}
		$lb_valido=$io_recepcion->uf_select_solicitudes_pago($ls_numdoc,$ls_codtipdoc,$ls_codprov,$ls_cedbene);
		if($lb_valido==true)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Compromisos ","ERROR->".$io_funciones->uf_convertirmsg($io_recepcion->io_sql->message),false,""); 
		}
		else
		{
			$lb_valido=$io_recepcion->uf_load_comprobantes_positivos($ls_tipodes,$ls_codprov,$ls_cedbene,$ld_fechareg,$ls_numdoccom);
			if ($lb_valido)
			{
				$li_totrow=$io_recepcion->rs_compromisos->RecordCount();
				if($li_totrow>0)
				{
					print "<table width=600 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
					print "<tr class=titulo-celda>";
					print "	<td align='center' >Comprobante</td>";
					print "	<td align='center' >Procede</td>";
					print "	<td align='center' >Fecha</td>";
					print "	<td align='center' >Descripcion</td>";
					print "	<td align='rigth' >Total</td>";
					print "</tr>";
					$li_i=0;
					while((!$io_recepcion->rs_compromisos->EOF)&&($lb_valido))
					{
						$li_i++;
						$ls_valcompromiso=true;
						$ls_procede=$io_recepcion->rs_compromisos->fields["procede"]; 
						$ls_comprobante=$io_recepcion->rs_compromisos->fields["comprobante"];
						$li_total=$io_recepcion->rs_compromisos->fields["total"];				  
						$li_monto=$io_recepcion->rs_compromisos->fields["monto"];				  
						$ls_descripcion=$io_recepcion->rs_compromisos->fields["descripcion"];
						$ls_fecha=$io_recepcion->rs_compromisos->fields["fecha"];
						$li_monto_ajuste=0;
						$li_monto_causado=0;
						$li_monto_anulado=0;
						$li_monto_recepcion=0;
						$li_monto_ordenpago=0;
						$li_monto_cargo=0;
						$li_monto_solicitud=0;
						$li_disponible=0;
						$li_monimpcon=0;
						$ls_numcomanu="";
						$li_monnotas=0;
						if($li_monto>$li_total)
							$li_total=$li_monto;
						
						if($ls_estcomobr!=0)
						{
							if(($ls_varcomrd=="1")&&($ls_procede=="SOCCOC"||$ls_procede=="SOCCOS"||$ls_procede=="SOBCON"))
							{
								$ls_valcompromiso=$io_recepcion->uf_validacion_compromiso($ls_comprobante,$ls_procede,$ls_tipodes,
																						  $ls_codprov,$ls_cedbene);
							}
						}
						else
						{
							if(($ls_varcomrd=="1")&&($ls_procede=="SOCCOC"||$ls_procede=="SOCCOS"))
							{
								$ls_valcompromiso=$io_recepcion->uf_validacion_compromiso($ls_comprobante,$ls_procede,$ls_tipodes,
																						  $ls_codprov,$ls_cedbene);
							}
						}
						if($ls_valcompromiso)
						{
							$li_monto_ajuste="";
							$arrResultado=$io_recepcion->uf_load_monto_ajustes($ls_comprobante,$ls_procede,$ls_tipodes,$ls_codprov,
																			$ls_cedbene,$li_monto_ajuste);
							$lb_valido=$arrResultado["valido"];
							$li_monto_ajuste=$arrResultado["monto"];
							unset($arrResultado);
							if($lb_valido)
							{
								$$li_monto_causado="";
								$arrResultado=$io_recepcion->uf_load_monto_causados($ls_comprobante,$ls_procede,$ls_tipodes,$ls_codprov,
																				 $ls_cedbene,$li_monto_causado);
								$lb_valido=$arrResultado["valido"];
								$li_monto_causado=$arrResultado["monto"];
								unset($arrResultado);
							}
							if($lb_valido)
							{
								$la_numcomanu="";
								$arrResultado=$io_recepcion->uf_load_comprobantes_anulados($ls_comprobante,$ls_tipodes,$ls_codprov,
																						$ls_cedbene,$ld_fechareg,$la_numcomanu);
								$lb_valido=$arrResultado["valido"];
								$la_numcomanu=$arrResultado["valor"];
								unset($arrResultado);
							}
							$li_totrowanu=count((array)$la_numcomanu);
							$li_monto_totanulado=0;
							if(!empty($la_numcomanu))
							{
								for($li_k=1;$li_k<=$li_totrowanu;$li_k++)
								{
									if($lb_valido)
									{
										$ls_numcomanu=$la_numcomanu[$li_k];
										$li_monto_anulado="";
										$arrResultado=$io_recepcion->uf_load_monto_anulados($ls_numcomanu,$ls_procede,$ls_tipodes,$ls_codprov,
																						 $ls_cedbene,$li_monto_anulado);
										$lb_valido=$arrResultado["valido"];
										$li_monto_anulado=$arrResultado["monto"];
										unset($arrResultado);
										$li_monto_totanulado=$li_monto_totanulado+$li_monto_anulado;
									}
								}
							}
							if($lb_valido)
							{
								$li_monto_recepcion="";
								$arrResultado=$io_recepcion->uf_load_monto_recepciones($ls_comprobante,$ls_procede,$li_monto_recepcion);
								$lb_valido=$arrResultado["valido"];
								$li_monto_recepcion=$arrResultado["monto"];
								unset($arrResultado);
							}
							if($lb_valido)
							{
								$li_monto_ordenpago="";
								$arrResultado=$io_recepcion->uf_load_monto_ordenespago_directa($ls_comprobante,$ls_procede,$li_monto_ordenpago);
								$lb_valido=$arrResultado["valido"];
								$li_monto_ordenpago=$arrResultado["monto"];
								unset($arrResultado);
							}
							if(($lb_valido)&&($ls_confiva=="C"))
							{
								$li_monimpcon="";
								$arrResultado=$io_recepcion->uf_load_cargos_compromisocontable($ls_comprobante,$ls_procede,$li_monimpcon);
								$lb_valido=$arrResultado["valido"];
								$li_monimpcon=$arrResultado["valor"];
							}
							if(($lb_valido)&&($ls_estafenc==0))
							{
								$li_monnotas="";
								$arrResultado=$io_recepcion->uf_load_monto_notascredito($ls_comprobante,$ls_procede,"",$li_monnotas);
								$lb_valido=$arrResultado["valido"];
								$li_monnotas=$arrResultado["valor"];
							}
							if($lb_valido)
							{
	//							$li_disponible=($li_total+$li_monto_ajuste)-$li_monto_causado+$li_monto_anulado-$li_monto_recepcion-$li_monto_cargo;
								$li_disponible=($li_total+$li_monto_ajuste)-($li_monto_causado+$li_monto_totanulado)-$li_monto_recepcion+$li_monnotas;
	// 					print"COMPROMISO->".$ls_comprobante." DISPONIBLE->".$li_disponible." TOTAL->".$li_total." AJUSTE->".$li_monto_ajuste." CAUSADO->".$li_monto_causado." Anulado->".$li_monto_anulado." Recepcion->".$li_monto_recepcion."<br><br>";
								if($li_disponible>0)
								{
									$li_monto_solicitud="";
									$arrResultado=$io_recepcion->uf_load_acumulado_solicitudes($ls_numdoc,$ls_codtipdoc,$ls_codprov,
																							$ls_cedbene,$li_monto_solicitud);
									$lb_valido=$arrResultado["valido"];
									$li_monto_solicitud=$arrResultado["monto"];
									unset($arrResultado);
									if($lb_valido)
									{
										if($li_total==$li_monto_solicitud)
										{//Verificar que no existan solicitudes de pago con el monto igual a la RD.
											$lb_valido=false;
										}
									}
									if($lb_valido)
									{
										print "<tr class=celdas-blancas>";
										print "	<td  width=110 align=center><a href=\"javascript: ue_aceptar('$ls_comprobante','$ls_procede',";
										print "  '$ls_fecha','$li_disponible','$li_monto_cargo','$li_i');\">".$ls_comprobante."</a></td>";
										print "	<td  width=80  align=center>".$ls_procede."</td>";
										print "	<td  width=80  align=center>".$io_funciones->uf_convertirfecmostrar($ls_fecha)."</td>";
										print " <td  width=330 align=left><textarea name=txtconcepto".$li_i." cols=50 rows=3 class=sin-borde id=txtconcepto".$li_i." readonly>".$ls_descripcion."</textarea></td>";
										print " <td  width=100 align=right>".number_format($li_disponible,2,',','.')."</td>";
										print "</tr>";
									}
								}
							}
						}
						$io_recepcion->rs_compromisos->MoveNext();
					}
					print "</table>";
				}
				else
				{
        			$io_mensajes->message("ERROR->No hay comprobantes asociados a este Proveedor ó Beneficiario"); 
				}
			}
		}
		unset($io_mensajes);
		unset($io_funciones);
		unset($io_recepcion);
	}// end function uf_print_compromisos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_solicitudespago()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_solicitudespago
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de solicitudes de pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 29/04/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_numsol=$_POST['numsol'];
		$ld_fecdes=$_POST['fecemides'];
		$ld_fechas=$_POST['fecemihas'];
		$ls_tipdes=$_POST['tipdes'];
		$ls_codproben=$_POST['codproben'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		$ls_consol=$_POST['consol'];
		$ls_numrecdoc=$_POST['numrecdoc'];
		$ld_fecdes=$io_funciones->uf_convertirdatetobd($ld_fecdes);
		$ld_fechas=$io_funciones->uf_convertirdatetobd($ld_fechas);
		$ls_codpro="";
		$ls_cedben="";
		if($ls_tipo=='NCND')
		{
			$ls_aux=" AND (cxp_solicitudes.estprosol='C' OR cxp_solicitudes.estprosol='S')";			
		}
		else
		{
			$ls_aux="";
		}
		$ls_criterioproveedor="";
		switch ($ls_tipdes)
		{
			case "P":
				$ls_codpro=$ls_codproben;
				$ls_cedben="----------";
				$ls_criterioproveedor="   AND cxp_solicitudes.cod_pro = '".$ls_codpro."'".
									  "   AND cxp_solicitudes.ced_bene = '".$ls_cedben."'";
			break;

			case "B":
				$ls_codpro="----------";
				$ls_cedben=$ls_codproben;
				$ls_criterioproveedor="   AND cxp_solicitudes.cod_pro = '".$ls_codpro."'".
									  "   AND cxp_solicitudes.ced_bene = '".$ls_cedben."'";
			break;
			
			default:
				$ls_criterioproveedor="   AND cxp_solicitudes.cod_pro LIKE '%".$ls_codpro."%'".
									  "   AND cxp_solicitudes.ced_bene LIKE '%".$ls_cedben."%'";
			break;
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " , cxp_rd_spg";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_cadena = $io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		$ls_sql="SELECT DISTINCT cxp_solicitudes.numsol, cxp_solicitudes.cod_pro, cxp_solicitudes.ced_bene, cxp_solicitudes.codfuefin,".
				"       cxp_solicitudes.tipproben, cxp_solicitudes.fecemisol, cxp_solicitudes.consol, cxp_solicitudes.estprosol,".
				"       cxp_solicitudes.monsol, cxp_solicitudes.obssol, cxp_solicitudes.estaprosol, cxp_solicitudes.numordpagmin,".
				"       cxp_solicitudes.codtipfon,cxp_solicitudes.repcajchi,cxp_solicitudes.conanusol,cxp_solicitudes.nombenaltcre,".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ". 
				"                       ELSE 'NINGUNO' END ) AS nombre, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.sc_cuenta ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT rpc_beneficiario.sc_cuenta ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ". 
				"                       ELSE 'NINGUNO' END ) AS sc_cuenta, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT scg_cuentas.denominacion ".
				"                                        FROM rpc_proveedor, scg_cuentas ".
				"                                       WHERE rpc_proveedor.codemp = scg_cuentas.codemp ".
				"										  AND rpc_proveedor.sc_cuenta = scg_cuentas.sc_cuenta ".
				"										  AND rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT scg_cuentas.denominacion ".
				"                                        FROM rpc_beneficiario, scg_cuentas ".
				"                                       WHERE rpc_beneficiario.codemp = scg_cuentas.codemp ".
				"										  AND rpc_beneficiario.sc_cuenta = scg_cuentas.sc_cuenta ".
				"										  AND rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ". 
				"                       ELSE 'NINGUNO' END ) AS denscg, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.rifpro 
														 FROM rpc_proveedor 
														WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp 
														  AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) 
										WHEN 'B' THEN (SELECT rpc_beneficiario.rifben 
														 FROM rpc_beneficiario 
														WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp 
														  AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) 
										  ELSE 'NINGUNO' END ) AS rifproben,".
				"       (SELECT denfuefin".
				"		   FROM sigesp_fuentefinanciamiento".
				"         WHERE sigesp_fuentefinanciamiento.codemp=cxp_solicitudes.codemp".
				"           AND sigesp_fuentefinanciamiento.codfuefin=cxp_solicitudes.codfuefin) AS denfuefin".
				"  FROM cxp_solicitudes, cxp_dt_solicitudes ".$ls_filtrofrom.
		        "  WHERE cxp_solicitudes.codemp='".$ls_codemp."' ". 
				"   AND cxp_solicitudes.numsol LIKE '%".$ls_numsol."%' ".
				"   AND cxp_dt_solicitudes.numrecdoc LIKE '%".$ls_numrecdoc."%' ".
				"   AND cxp_solicitudes.fecemisol >= '".$ld_fecdes."' ".
				"   AND cxp_solicitudes.fecemisol <= '".$ld_fechas."' ".
				"   AND cxp_solicitudes.consol LIKE '%".$ls_consol."%' ". 
				"   AND cxp_solicitudes.codemp= cxp_dt_solicitudes.codemp".
				"   AND cxp_solicitudes.numsol= cxp_dt_solicitudes.numsol".
				$ls_criterioproveedor.
				$ls_aux.$ls_filtroest.
				" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Solicitudes de Pago ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=520 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width=60  style='cursor:pointer' title='Ordenar por Numero de Solicitud'       align='center' onClick=ue_orden('numsol')>Número de Solicitud</td>";
			print "<td width=300 style='cursor:pointer' title='Ordenar por Proveedor/Beneficiario' align='center' onClick=ue_orden('nombre')>Proveedor/Beneficiario</td>";
			print "<td width=80  style='cursor:pointer' title='Ordenar por Fecha de Emision' align='center' onClick=ue_orden('fecemisol')>Fecha</td>";
			print "<td width=80  style='cursor:pointer' title='Ordenar por Monto' align='center' onClick=ue_orden('monsol')>Monto</td>";
			print "</tr>";
			$li_i=0;
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li_i++;
				$ls_numsol=$row["numsol"];
				$ls_codfuefin=$row["codfuefin"];
				$ls_denfuefin=$row["denfuefin"];
				$ls_tipo_destino=$row["tipproben"];
				$ls_codpro=$row["cod_pro"];
				$ls_cedbene=$row["ced_bene"];
				$ls_sccuenta=$row["sc_cuenta"];
				$ls_denscg=$row["denscg"];
				$ls_numordpagmin=$row["numordpagmin"];
				$ls_codtipfon=$row["codtipfon"];
				$ls_nombenaltcre=$row["nombenaltcre"];
				switch ($ls_tipo_destino)
				{
					case "P":// proveedor
						$ls_codigo=$row["cod_pro"];
						break;	
					case "B":// beneficiario
						$ls_codigo=$row["ced_bene"];
						break;	
					case "-":// Ninguno
						$ls_codigo="----------";
						break;	
				}
				$ls_nombre=$row["nombre"];
				$ls_rifproben = $row["rifproben"];
				$ls_consol=$row["consol"];
				$ls_obssol=$row["obssol"];
				$ls_estprosol=$row["estprosol"];
				$ls_estaprosol=$row["estaprosol"];
				$ld_fecemisol=date("Y-m-d",strtotime($row["fecemisol"]));
				$li_monsol=number_format($row["monsol"],2,',','.');
				$ld_fecemisol=$io_funciones->uf_convertirfecmostrar($ld_fecemisol);
				$ls_estatus="";
				$ls_repcajchi=$row["repcajchi"];
				$ls_conanusol=$row["conanusol"];
				switch ($ls_estprosol)
				{
					case "R":
						$ls_estatus="REGISTRO";
						break;
						
					case "S":
						$ls_estatus="PROGRAMACION DE PAGO";
						break;
						
					case "P":
						$ls_estatus="CANCELADA";
						break;

					case "A":
						$ls_estatus="ANULADA";
						break;
						
					case "C":
						$ls_estatus="CONTABILIZADA";
						break;
						
					case "E":
						$ls_estatus="EMITIDA";
						break;
						
					case "N":
						$ls_estatus="ANULADA SIN AFECTACION";
						break;
				}
				switch ($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: ue_aceptar('$ls_numsol','$ls_codfuefin','$ls_denfuefin',";
						print "'$ls_codigo','$ls_nombre','$li_monsol','$ls_estprosol','$ls_estaprosol','$ld_fecemisol',";
						print "'$ls_estatus','$ls_tipo_destino','$li_i','$ls_rifproben','$ls_numordpagmin','$ls_codtipfon','$ls_repcajchi','$ls_conanusol','$ls_nombenaltcre');\">".$ls_numsol."</a></td>";
						print "<td align='left' width=230>".$ls_nombre."</td>";
						print "<td align='left'>".$ld_fecemisol."</td>";
						print "<td align='left'><input name='txtconsol".$li_i."' type='hidden' id='txtconsol".$li_i."' value='".$ls_consol."'>".
							  "<input name='txtobssol".$li_i."' type='hidden' id='txtobssol".$li_i."' value='".$ls_obssol."'>".$li_monsol."</td>";
						print "</tr>";			
						break;
						
					case "NCND":
						if(!uf_chequear_cancelado($ls_numsol))
						{
							print "<tr class=celdas-blancas>";
							print "<td align='center'><a href=\"javascript: aceptarncnd('$ls_numsol','$ls_tipo_destino','$ls_codpro',";
							print "'$ls_cedbene','$ls_nombre','$ls_sccuenta','$ls_denscg','$ls_rifproben');\">".$ls_numsol."</a></td>";
							print "<td align='left' width=230>".$ls_nombre."</td>";
							print "<td align='left'>".$ld_fecemisol."</td>";
							print "<td align='left'>".$li_monsol."</td>";
							print "</tr>";			
						}
						break;
					case "REPDES":
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: aceptarrepdes('$ls_numsol');\">".$ls_numsol."</a></td>";
						print "<td align='left' width=230>".$ls_nombre."</td>";
						print "<td align='left'>".$ld_fecemisol."</td>";
						print "<td align='left'>".$li_monsol."</td>";
						print "</tr>";	
						break;
					case "REPHAS":
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: aceptarrephas('$ls_numsol');\">".$ls_numsol."</a></td>";
						print "<td align='left' width=230>".$ls_nombre."</td>";
						print "<td align='left'>".$ld_fecemisol."</td>";
						print "<td align='left'>".$li_monsol."</td>";
						print "</tr>";			
						break;
					case "MODCMPRET":
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: aceptarmodcmpret('$ls_numsol');\">".$ls_numsol."</a></td>";
						print "<td align='left' width=230>".$ls_nombre."</td>";
						print "<td align='left'>".$ld_fecemisol."</td>";
						print "<td align='left'>".$li_monsol."</td>";
						print "</tr>";			
						break;
				}
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_solicitudespago
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_fuentefinanciamiento()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_fuentefinanciamiento
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de fuente de financiamiento
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 19/04/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		$ls_estcla = $_POST['estcla'];
		$ls_codestpro1 = $_POST['codestpro1'];
		$ls_codestpro2 = $_POST['codestpro2'];
		$ls_codestpro3 = $_POST['codestpro3'];
		$ls_codestpro4 = $_POST['codestpro4'];
		$ls_codestpro5 = $_POST['codestpro5'];
		$ls_criterio="";
		if($ls_tipo!="SOLPAG")
		{
			$ls_criterio=	"   AND codfuefin IN (SELECT codfuefin FROM spg_dt_fuentefinanciamiento ".
							"					   WHERE codemp='".$ls_codemp."' ".
							"					     AND codestpro1 = '".$_POST['codestpro1']."' ".
							"					     AND codestpro2 = '".$_POST['codestpro2']."' ".
							"					     AND codestpro3 = '".$_POST['codestpro3']."' ".
							"					     AND codestpro4 = '".$_POST['codestpro4']."' ".
							"					     AND codestpro5 = '".$_POST['codestpro5']."' ".
							"					     AND estcla = '".$_POST['estcla']."' )";	
		}
		$ls_sql="SELECT codfuefin, denfuefin ".
				"  FROM sigesp_fuentefinanciamiento ".	
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codfuefin <> '--' ".		
				$ls_criterio.
				" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Fuentes de Financiamiento ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width=60  style='cursor:pointer' title='Ordenar por Codigo'       align='center' onClick=ue_orden('codfuefin')>Codigo</td>";
			print "<td width=440 style='cursor:pointer' title='Ordenar por Denominacion' align='center' onClick=ue_orden('denfuefin')>Denominacion</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codfuefin=$row["codfuefin"];
				$ls_denfuefin=$row["denfuefin"];
				switch ($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: aceptar('$ls_codfuefin','$ls_denfuefin');\">".$ls_codfuefin."</a></td>";
						print "<td align='left'>".$ls_denfuefin."</td>";
						print "</tr>";			
						break;
					case "SOLPAG":
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: aceptar('$ls_codfuefin','$ls_denfuefin');\">".$ls_codfuefin."</a></td>";
						print "<td align='left'>".$ls_denfuefin."</td>";
						print "</tr>";			
						break;
				}
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_fuentefinanciamiento
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_retencionesislr()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_retencionesislr
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de retenciones de ISLR
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/07/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];

		$ls_tipproben=$_POST['tipproben'];
		$ld_fecdes=$_POST['fecdes'];
		$ld_fechas=$_POST['fechas'];
		$ls_codprobendes=$_POST['codprobendes'];
		$ls_codprobenhas=$_POST['codprobenhas'];
		$ls_basdatcmp=$_POST['basdatcmp'];
		$ls_numsol=$_POST['numsol'];
		$ld_fecdes=$io_funciones->uf_convertirdatetobd($ld_fecdes);
		$ld_fechas=$io_funciones->uf_convertirdatetobd($ld_fechas);
		$ls_cedbendes="";
		$ls_cedbenhas="";
		$ls_codprodes="";
		$ls_codprohas="";
		$ls_criterio="";
		$ls_criterio2="";
		$ls_criterio3="";
		switch($ls_tipproben)
		{
			case "P":
				$ls_codprodes=$ls_codprobendes;
				$ls_codprohas=$ls_codprobenhas;
			break;

			case "B":
				$ls_cedbendes=$ls_codprobendes;
				$ls_cedbenhas=$ls_codprobenhas;
			break;
		}
		if($ld_fecdes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.fecemisol >= '".$ld_fecdes."'";
			$ls_criterio2=$ls_criterio2."		AND scb_movbco.fecmov >= '".$ld_fecdes."'";
		}
		if($ld_fechas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.fecemisol <= '".$ld_fechas."'";
			$ls_criterio2=$ls_criterio2."		AND scb_movbco.fecmov <= '".$ld_fechas."'";
		}
		if($ls_codprodes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro >= '".$ls_codprodes."'";
			$ls_criterio2=$ls_criterio2."		AND scb_movbco.cod_pro >= '".$ls_codprodes."'";
		}
		if($ls_codprohas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro <= '".$ls_codprohas."'";
			$ls_criterio2=$ls_criterio2."		AND scb_movbco.cod_pro <= '".$ls_codprohas."'";
		}
		if($ls_cedbendes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene >= '".$ls_cedbendes."'";
			$ls_criterio2=$ls_criterio2."		AND scb_movbco.ced_bene >= '".$ls_cedbendes."'";
		}
		if($ls_cedbenhas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene <= '".$ls_cedbenhas."'";
			$ls_criterio2=$ls_criterio2."		AND scb_movbco.ced_bene <= '".$ls_cedbenhas."'";
		}
		if($ls_numsol!="")
		{
			$ls_criterio=$ls_criterio." AND cxp_solicitudes.numsol='".$ls_numsol."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		if($_SESSION["la_empresa"]["estretislr"]=='1')
		{
			if($ld_fecdes!="")
			{
				$ls_criterio3=$ls_criterio3."		AND scb_cmp_ret.fecrep >= '".$ld_fecdes."'";
			}
			if($ld_fechas!="")
			{
				$ls_criterio3=$ls_criterio3."		AND scb_cmp_ret.fecrep <= '".$ld_fechas."'";
			}
			if($ls_codprobendes!="")
			{
				$ls_criterio3=$ls_criterio3."		AND scb_cmp_ret.codsujret >= '".$ls_codprobendes."'";
			}
			if($ls_codprobenhas!="")
			{
				$ls_criterio3=$ls_criterio3."		AND scb_cmp_ret.codsujret <= '".$ls_codprobenhas."'";
			}
		}
			$ls_sql="SELECT DISTINCT cxp_solicitudes.numsol AS numero, cxp_solicitudes.consol AS concepto, cxp_rd.procede AS procede ".
					"  FROM cxp_solicitudes, cxp_dt_solicitudes, cxp_rd, cxp_rd_deducciones, sigesp_deducciones ".$ls_filtrofrom.
					" WHERE cxp_solicitudes.codemp = '".$ls_codemp."' ".
					"   AND sigesp_deducciones.islr=1 ".
					$ls_criterio.
					"   AND cxp_solicitudes.estprosol<>'A'".
					"   AND cxp_solicitudes.estprosol<>'N'".
					"   AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
					"   AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ".
					"   AND cxp_solicitudes.cod_pro = cxp_dt_solicitudes.cod_pro ".
					"   AND cxp_solicitudes.ced_bene = cxp_dt_solicitudes.ced_bene ".
					"	AND cxp_dt_solicitudes.codemp = cxp_rd.codemp ".
					"	AND cxp_dt_solicitudes.numrecdoc = cxp_rd.numrecdoc ".
					"	AND cxp_dt_solicitudes.codtipdoc = cxp_rd.codtipdoc ".
					"	AND cxp_dt_solicitudes.cod_pro = cxp_rd.cod_pro ".
					"   AND cxp_dt_solicitudes.ced_bene = cxp_rd.ced_bene ".
					"	AND cxp_rd.codemp = cxp_rd_deducciones.codemp ".
					"	AND cxp_rd.numrecdoc = cxp_rd_deducciones.numrecdoc ".
					"	AND cxp_rd.codtipdoc = cxp_rd_deducciones.codtipdoc ".
					"   AND cxp_rd.cod_pro = cxp_rd_deducciones.cod_pro ".
					"	AND cxp_rd.ced_bene = cxp_rd_deducciones.ced_bene ".
					"	AND cxp_rd_deducciones.codemp = sigesp_deducciones.codemp ".
					"	AND cxp_rd_deducciones.codded = sigesp_deducciones.codded ".$ls_filtroest.
					" UNION ".
					"SELECT scb_movbco.numdoc AS numero, MAX(scb_movbco.conmov) AS concepto, MAX(scb_movbco.procede) AS procede ".
					"  FROM scb_movbco, sigesp_deducciones, scb_movbco_scg ".
					" WHERE scb_movbco.codemp = '".$ls_codemp."' ".
					"   AND scb_movbco.codope = 'CH' ".
					"   AND scb_movbco.estmov <> 'A' ".
					"   AND scb_movbco.estmov <> 'O' ".
					"   AND scb_movbco.monret <> 0 ".
					"   AND sigesp_deducciones.islr = 1".
					$ls_criterio2.
					"    AND scb_movbco.codemp = scb_movbco_scg.codemp ".
					"    AND scb_movbco.codban = scb_movbco_scg.codban ".
					"    AND scb_movbco.ctaban = scb_movbco_scg.ctaban ".
					"    AND scb_movbco.numdoc = scb_movbco_scg.numdoc ".
					"    AND scb_movbco.codope = scb_movbco_scg.codope ".
					"    AND scb_movbco.estmov = scb_movbco_scg.estmov ".
					"    AND scb_movbco_scg.codemp = sigesp_deducciones.codemp ".
					"    AND scb_movbco_scg.codded = sigesp_deducciones.codded ".
					"  GROUP BY scb_movbco.numdoc ".
					" UNION ".
					"SELECT cxp_cmp_islr.numsol AS numero, cxp_cmp_islr.consol AS concepto, 'INT' AS procede".
					"  FROM cxp_cmp_islr".
					" WHERE cxp_cmp_islr.codemp = '".$ls_codemp."' ".
					"UNION ".
					"SELECT scb_cmp_ret.numcom AS numero, scb_cmp_ret.nomsujret AS concepto, 'GENCMP' AS procede".
					"  FROM scb_cmp_ret".
					" WHERE scb_cmp_ret.codemp = '".$ls_codemp."'".
					"   AND codret='0000000006' ".$ls_criterio3.
				    "  ORDER BY numero ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Retenciones I.S.L.R. ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			$lo_title[1]="<input type=checkbox name=checkall id=checkall value=1 size=10 style=text-align:left  class=sin-borde onclick='javascript:uf_checkall();'>";
			$lo_title[2]="Nro Documento"; 
			if($_SESSION["la_empresa"]["estretislr"]=='1')
			{
				$lo_title[3]="Sujeto Retenido"; 
			}
			else
			{
				$lo_title[3]="Concepto"; 
			}
			
			$lo_title[4]="Procede"; 
			$li_totrow=0;
			$lo_object[$li_totrow][1]="";
			$lo_object[$li_totrow][2]="";
			$lo_object[$li_totrow][3]="";
			$lo_object[$li_totrow][4]="";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_numero=$row["numero"];
				$ls_concepto=$row["concepto"];
				$ls_procede=$row["procede"];
				if($_SESSION["la_empresa"]["estretislr"]=='1') 
				{
					if($ls_procede=="GENCMP")
					{
						$li_totrow++;
						$lo_object[$li_totrow][1]="<input type=checkbox  name=checkcmp".$li_totrow."    id=checkcmp".$li_totrow."    value=1                  size=10  style=text-align:left    class=sin-borde>"; 
						$lo_object[$li_totrow][2]="<input type=text      name=txtnumero".$li_totrow."   id=txtnumero".$li_totrow."   value='".$ls_numero."'   size=15  style=text-align:center  class=sin-borde readonly>"; 
						$lo_object[$li_totrow][3]="<input type=text      name=txtconcepto".$li_totrow." id=txtconcepto".$li_totrow." value='".$ls_concepto."' size=80 style=text-align:left    class=sin-borde readonly title='".$ls_concepto."' bgColor=#FF5500>";
						$lo_object[$li_totrow][4]="<input type=text      name=txtprocede".$li_totrow."  id=txtprocede".$li_totrow."  value='".$ls_procede."'  size=5   style=text-align:center  class=sin-borde readonly>";
					}
				}
				else
				{
					if($ls_procede=="CXPRCD")
					{
						$li_totrow++;
						$lo_object[$li_totrow][1]="<input type=checkbox  name=checkcmp".$li_totrow."    id=checkcmp".$li_totrow."    value=1                  size=10  style=text-align:left    class=sin-borde>"; 
						$lo_object[$li_totrow][2]="<input type=text      name=txtnumero".$li_totrow."   id=txtnumero".$li_totrow."   value='".$ls_numero."'   size=15  style=text-align:center  class=sin-borde readonly>"; 
						$lo_object[$li_totrow][3]="<input type=text      name=txtconcepto".$li_totrow." id=txtconcepto".$li_totrow." value='".$ls_concepto."' size=80 style=text-align:left    class=sin-borde readonly title='".$ls_concepto."' bgColor=#FF5500>";
						$lo_object[$li_totrow][4]="<input type=text      name=txtprocede".$li_totrow."  id=txtprocede".$li_totrow."  value='".$ls_procede."'  size=5   style=text-align:center  class=sin-borde readonly>";
					}
				}
			}
			$io_sql->free_result($rs_data);
			$io_grid->makegrid($li_totrow,$lo_title,$lo_object,550,'Comprobantes de Retención de I.S.L.R.','grid');
		}	unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_retencionesislr
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_solfacturas()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_solfacturas
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de retenciones de RELACION DE FACTURAS
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/07/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];

		$ls_tipproben=$_POST['tipproben'];
		$ld_fecdes=$_POST['fecdes'];
		$ld_fechas=$_POST['fechas'];
		$ls_codprobendes=$_POST['codprobendes'];
		$ls_codprobenhas=$_POST['codprobenhas'];
		$ld_fecdes=$io_funciones->uf_convertirdatetobd($ld_fecdes);
		$ld_fechas=$io_funciones->uf_convertirdatetobd($ld_fechas);
		$ls_cedbendes="";
		$ls_cedbenhas="";
		$ls_codprodes="";
		$ls_codprohas="";
		$ls_criterio="";
		switch($ls_tipproben)
		{
			case "P":
				$ls_codprodes=$ls_codprobendes;
				$ls_codprohas=$ls_codprobenhas;
				$ls_criterio=$ls_criterio."		AND cxp_solicitudes.tipproben= 'P' ";
			break;

			case "B":
				$ls_cedbendes=$ls_codprobendes;
				$ls_cedbenhas=$ls_codprobenhas;
				$ls_criterio=$ls_criterio."		AND cxp_solicitudes.tipproben= 'B' ";
			break;
		}
		if($ld_fecdes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.fecemisol >= '".$ld_fecdes."'";
		}
		if($ld_fechas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.fecemisol <= '".$ld_fechas."'";
		}
		if($ls_codprodes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro >= '".$ls_codprodes."'";
		}
		if($ls_codprohas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro <= '".$ls_codprohas."'";
		}
		if($ls_cedbendes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene >= '".$ls_cedbendes."'";
		}
		if($ls_cedbenhas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene <= '".$ls_cedbenhas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest1 = '';
		$ls_filtroest2 = '';
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			              	" AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd_spg";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_cadena = $io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		$ls_sql="SELECT DISTINCT cxp_solicitudes.tipproben,cxp_solicitudes.numsol,cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene, ".
				"        cxp_solicitudes.fecemisol,cxp_solicitudes.estprosol,cxp_solicitudes.monsol,cxp_solicitudes.consol,cxp_solicitudes.procede,".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre ".
				"  FROM cxp_solicitudes ".$ls_filtrofrom.
				" WHERE cxp_solicitudes.codemp='".$ls_codemp."' ".
				"   ".$ls_criterio." ".$ls_filtroest.
				" ORDER BY cxp_solicitudes.numsol";	
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Solicitudes ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			$lo_title[1]=" ";
			$lo_title[2]="Nro Documento"; 
			$lo_title[3]="Concepto"; 
			$lo_title[4]="Procede"; 
			$li_totrow=0;
			$lo_object[$li_totrow][1]="";
			$lo_object[$li_totrow][2]="";
			$lo_object[$li_totrow][3]="";
			$lo_object[$li_totrow][4]="";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li_totrow++;
				$ls_numero=$row["numsol"];
				$ls_concepto=$row["consol"];
				$ls_procede=$row["procede"];
				$lo_object[$li_totrow][1]="<input type=checkbox  name=checkcmp".$li_totrow."    id=checkcmp".$li_totrow."    value=1                  size=10  style=text-align:left    class=sin-borde>"; 
				$lo_object[$li_totrow][2]="<input type=text      name=txtnumero".$li_totrow."   id=txtnumero".$li_totrow."   value='".$ls_numero."'   size=15  style=text-align:center  class=sin-borde readonly>"; 
				$lo_object[$li_totrow][3]="<input type=text      name=txtconcepto".$li_totrow." id=txtconcepto".$li_totrow." value='".$ls_concepto."' size=80 style=text-align:left    class=sin-borde readonly title='".$ls_concepto."' bgColor=#FF5500>";
				$lo_object[$li_totrow][4]="<input type=text      name=txtprocede".$li_totrow."  id=txtprocede".$li_totrow."  value='".$ls_procede."'  size=5   style=text-align:center  class=sin-borde readonly>";
			}
			$io_sql->free_result($rs_data);
			$io_grid->makegrid($li_totrow,$lo_title,$lo_object,550,'Ordenes de Pago','grid');
		}	unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_solfacturas
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_catdeducciones()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_catdeducciones
		//		   Access: private
		//	    Arguments: 
		//	  Description: Método que inprime el resultado de la busqueda de las deducciones
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 10/07/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_codded="%".$_POST['codded']."%";
		$ls_dended="%".$_POST['dended']."%";
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		$ls_sql="SELECT codded, dended ".
			    "  FROM sigesp_deducciones ".
				" WHERE codemp = '".$ls_codemp."'  ".
				"	AND codded like '".$ls_codded."' ".
				"   AND dended like '".$ls_dended."' ".								
				" ORDER BY ".$ls_campoorden." ".$ls_orden." ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Deducciones ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Código'       align='center' onClick=ue_orden('codded')>Código</td>";
			print "<td width=400 style='cursor:pointer' title='Ordenar por Denominacion' align='center' onClick=ue_orden('dended')>Denominacion</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codded=trim($row["codded"]);
				$ls_dended=rtrim($row["dended"]);
				if($ls_tipo=="rephas")
				{
					print "<tr class=celdas-blancas>";
					print "<td align='center'><a href=\"javascript: ue_aceptar_rephas('".$ls_codded."','".$ls_dended."');\">".$ls_codded."</a></td>";
					print "<td align='left'>".$ls_dended."</td>";
					print "</tr>";			
				}
				else
				{
					print "<tr class=celdas-blancas>";
					print "<td align='center'><a href=\"javascript: ue_aceptar('".$ls_codded."','".$ls_dended."');\">".$ls_codded."</a></td>";
					print "<td align='left'>".$ls_dended."</td>";
					print "</tr>";			
				}
				
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_catdeducciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_retencionesiva()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_retencionesiva
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de retenciones de iva
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 12/07/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];

		$ls_tipproben=$_POST['tipproben'];
		$ld_fecdes=$_POST['fecdes'];
		$ld_fechas=$_POST['fechas'];
		$ls_mes=$_POST['mes'];
		$ls_anio=$_POST['anio'];
		$ls_codprobendes=$_POST['codprobendes'];
		$ls_codprobenhas=$_POST['codprobenhas'];
		$ls_numsol=$_POST['numsol'];
		$ld_fecdes=$io_funciones->uf_convertirdatetobd($ld_fecdes);
		$ld_fechas=$io_funciones->uf_convertirdatetobd($ld_fechas);
		$ls_cedbendes="";
		$ls_cedbenhas="";
		$ls_codprodes="";
		$ls_codprohas="";
		$ls_criterio="";
		$ls_criterio2="";
		$ls_sqlaux = $ls_straux = "";
		switch($ls_tipproben)
		{
			case "P":
				$ls_codprodes=$ls_codprobendes;
				$ls_codprohas=$ls_codprobenhas;
			break;

			case "B":
				$ls_cedbendes=$ls_codprobendes;
				$ls_cedbenhas=$ls_codprobenhas;
			break;
		}
		if($ld_fecdes!="")
		{
			$ls_criterio=$ls_criterio."		AND scb_cmp_ret.fecrep >= '".$ld_fecdes."'";
		}
		if($ld_fechas!="")
		{
			$ls_criterio=$ls_criterio."		AND scb_cmp_ret.fecrep <= '".$ld_fechas."'";
		}
		if($ls_codprodes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro >= '".$ls_codprodes."'";
		}
		if($ls_codprohas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro <= '".$ls_codprohas."'";
		}
		if($ls_cedbendes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene >= '".$ls_cedbendes."'";
		}
		if($ls_cedbenhas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene <= '".$ls_cedbenhas."'";
		}
		if ($ls_codprodes!="" || $ls_codprohas!="" || $ls_cedbendes!="" || $ls_cedbenhas!="")
		   {
		     $ls_sqlaux = ", cxp_solicitudes";
			 $ls_straux = " AND cxp_solicitudes.numsol = scb_dt_cmp_ret.numsop";
		   }

		$ls_periodofiscal = $ls_anio.$ls_mes;
		$ls_where="";
		if($ls_numsol!="")
		{
			$ls_where=" AND scb_dt_cmp_ret.numsop='".$ls_numsol."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT DISTINCT scb_cmp_ret.numcom, scb_cmp_ret.fecrep, scb_cmp_ret.nomsujret ".
				"  FROM scb_cmp_ret, scb_dt_cmp_ret $ls_sqlaux $ls_filtrofrom".
				" WHERE scb_cmp_ret.codemp = '".$ls_codemp."' ".
				"   AND scb_cmp_ret.codret = '0000000001' ".
				"   AND scb_cmp_ret.perfiscal = '".$ls_periodofiscal."' ".
				$ls_where.
				$ls_criterio.
				"	AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp  ".
				"   AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
				"   AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom $ls_straux ".$ls_filtroest.
				" ORDER BY scb_cmp_ret.numcom ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
			$io_mensajes->uf_mensajes_ajax("Error al cargar Retenciones IVA ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		    echo $io_sql->message;
		}
		else
		{
			$lo_title[1]="<input type=checkbox name=checkall id=checkall value=1 size=10 style=text-align:left  class=sin-borde onclick='javascript:uf_checkall();'>";
			$lo_title[2]="Nro Comprobante"; 
			$lo_title[3]="Fecha"; 
			$lo_title[4]="Proveedor / Beneficiario"; 
			$li_totrow=0;
			$lo_object[$li_totrow][1]="";
			$lo_object[$li_totrow][2]="";
			$lo_object[$li_totrow][3]="";
			$lo_object[$li_totrow][4]="";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li_totrow++;
				$ls_numcom=$row["numcom"];
				$ls_nomsujret=$row["nomsujret"];
				$ld_fecrep=$io_funciones->uf_convertirfecmostrar($row["fecrep"]);
				$lo_object[$li_totrow][1]="<input type=checkbox name=checkcmp".$li_totrow."     id=checkcmp".$li_totrow."     value=1                   size=10 style=text-align:left    class=sin-borde>"; 
				$lo_object[$li_totrow][2]="<input type=text     name=txtnumcom".$li_totrow."    id=txtnumcom".$li_totrow."    value='".$ls_numcom."'    size=15 style=text-align:center  class=sin-borde readonly>"; 
				$lo_object[$li_totrow][3]="<input type=text     name=txtfecrep".$li_totrow."    id=txtfecrep".$li_totrow."    value='".$ld_fecrep."'    size=10 style=text-align:center  class=sin-borde readonly>";
				$lo_object[$li_totrow][4]="<input type=text     name=txtnomsujret".$li_totrow." id=txtnomsujret".$li_totrow." value='".$ls_nomsujret."' size=75 style=text-align:left    class=sin-borde readonly title='".$ls_nomsujret."' bgColor=#FF5500>";
			}
			$io_sql->free_result($rs_data);
			$io_grid->makegrid($li_totrow,$lo_title,$lo_object,550,'Comprobantes de Retenci&oacute;n de IVA','grid');

		}	unset($io_include);
		unset($io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_retencionesiva
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_retenciones1x1000()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_retencionesiva
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de retenciones de iva
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 12/07/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];

		/*$ls_tipproben=$_POST['tipproben'];
		$ld_fecdes=$_POST['fecdes'];
		$ld_fechas=$_POST['fechas'];*/
		$ls_mes=$_POST['mes'];
		$ls_anio=$_POST['anio'];
/*		$ls_codprobendes=$_POST['codprobendes'];
		$ls_codprobenhas=$_POST['codprobenhas'];
		$ls_numsol=$_POST['numsol'];
		$ld_fecdes=$io_funciones->uf_convertirdatetobd($ld_fecdes);
		$ld_fechas=$io_funciones->uf_convertirdatetobd($ld_fechas);
		$ls_cedbendes="";
		$ls_cedbenhas="";
		$ls_codprodes="";
		$ls_codprohas="";
		$ls_criterio="";
		$ls_criterio2="";
		$ls_sqlaux = $ls_straux = "";
		switch($ls_tipproben)
		{
			case "P":
				$ls_codprodes=$ls_codprobendes;
				$ls_codprohas=$ls_codprobenhas;
			break;

			case "B":
				$ls_cedbendes=$ls_codprobendes;
				$ls_cedbenhas=$ls_codprobenhas;
			break;
		}
		if($ld_fecdes!="")
		{
			$ls_criterio=$ls_criterio."		AND scb_cmp_ret.fecrep >= '".$ld_fecdes."'";
		}
		if($ld_fechas!="")
		{
			$ls_criterio=$ls_criterio."		AND scb_cmp_ret.fecrep <= '".$ld_fechas."'";
		}
		if($ls_codprodes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro >= '".$ls_codprodes."'";
		}
		if($ls_codprohas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro <= '".$ls_codprohas."'";
		}
		if($ls_cedbendes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene >= '".$ls_cedbendes."'";
		}
		if($ls_cedbenhas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene <= '".$ls_cedbenhas."'";
		}
		if ($ls_codprodes!="" || $ls_codprohas!="" || $ls_cedbendes!="" || $ls_cedbenhas!="")
		   {
		     $ls_sqlaux = ", cxp_solicitudes";
			 $ls_straux = " AND cxp_solicitudes.numsol = scb_dt_cmp_ret.numsop";
		   }*/

		$ls_periodofiscal = $ls_anio.$ls_mes;
		$ls_where="";
		/*if($ls_numsol!="")
		{
			$ls_where=" AND scb_dt_cmp_ret.numsop='".$ls_numsol."'";
		}*/
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc "; 
			$ls_filtrofrom = " , cxp_dt_solicitudes, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT DISTINCT scb_cmp_ret.numcom, scb_cmp_ret.fecrep, scb_cmp_ret.nomsujret ".
				"  FROM scb_cmp_ret, scb_dt_cmp_ret ".$ls_filtrofrom.
				" WHERE scb_cmp_ret.codemp = '".$ls_codemp."' ".
				"   AND scb_cmp_ret.codret = '0000000005' ".
				"   AND scb_cmp_ret.perfiscal = '".$ls_periodofiscal."' ".
				$ls_where.
				"	AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp  ".
				"   AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
				"   AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ".$ls_filtroest.
				" ORDER BY scb_cmp_ret.numcom ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
			$io_mensajes->uf_mensajes_ajax("Error al cargar Retenciones 1x1000 ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		    echo $io_sql->message;
		}
		else
		{
			$lo_title[1]="<input type=checkbox name=checkall id=checkall value=1 size=10 style=text-align:left  class=sin-borde onclick='javascript:uf_checkall();'>";
			$lo_title[2]="Nro Comprobante"; 
			$lo_title[3]="Fecha"; 
			$lo_title[4]="Proveedor / Beneficiario"; 
			$li_totrow=0;
			$lo_object[$li_totrow][1]="";
			$lo_object[$li_totrow][2]="";
			$lo_object[$li_totrow][3]="";
			$lo_object[$li_totrow][4]="";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li_totrow++;
				$ls_numcom=$row["numcom"];
				$ls_nomsujret=$row["nomsujret"];
				$ld_fecrep=$io_funciones->uf_convertirfecmostrar($row["fecrep"]);
				$lo_object[$li_totrow][1]="<input type=checkbox name=checkcmp".$li_totrow."     id=checkcmp".$li_totrow."     value=1                   size=10 style=text-align:left    class=sin-borde>"; 
				$lo_object[$li_totrow][2]="<input type=text     name=txtnumcom".$li_totrow."    id=txtnumcom".$li_totrow."    value='".$ls_numcom."'    size=15 style=text-align:center  class=sin-borde readonly>"; 
				$lo_object[$li_totrow][3]="<input type=text     name=txtfecrep".$li_totrow."    id=txtfecrep".$li_totrow."    value='".$ld_fecrep."'    size=10 style=text-align:center  class=sin-borde readonly>";
				$lo_object[$li_totrow][4]="<input type=text     name=txtnomsujret".$li_totrow." id=txtnomsujret".$li_totrow." value='".$ls_nomsujret."' size=75 style=text-align:left    class=sin-borde readonly title='".$ls_nomsujret."' bgColor=#FF5500>";
			}
			$io_sql->free_result($rs_data);
			$io_grid->makegrid($li_totrow,$lo_title,$lo_object,550,'Comprobantes de Retenci&oacute;n 1x1000','grid');
		}	unset($io_include);
		unset($io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_retencionesiva
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_retencionesaporte()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_retencionesaporte
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de retenciones de aporte social
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 12/07/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];

		$ls_tipproben=$_POST['tipproben'];
		$ld_fecdes=$_POST['fecdes'];
		$ld_fechas=$_POST['fechas'];
		$ls_mes=$_POST['mes'];
		$ls_anio=$_POST['anio'];
		$ls_codprobendes=$_POST['codprobendes'];
		$ls_codprobenhas=$_POST['codprobenhas'];
		$ls_numsol=$_POST['numsol'];
		$ld_fecdes=$io_funciones->uf_convertirdatetobd($ld_fecdes);
		$ld_fechas=$io_funciones->uf_convertirdatetobd($ld_fechas);
		$ls_cedbendes="";
		$ls_cedbenhas="";
		$ls_codprodes="";
		$ls_codprohas="";
		$ls_criterio="";
		$ls_criterio2="";
		$ls_tabla="";
		switch($ls_tipproben)
		{
			case "P":
				$ls_codprodes=$ls_codprobendes;
				$ls_codprohas=$ls_codprobenhas;
			break;

			case "B":
				$ls_cedbendes=$ls_codprobendes;
				$ls_cedbenhas=$ls_codprobenhas;
			break;
		}
		if(($ls_codprodes!="")||($ls_codprohas!="")||($ls_cedbendes!="")||($ls_cedbenhas!=""))
		{
			$ls_tabla=",cxp_solicitudes";
			$ls_criterio=$ls_criterio."   AND scb_dt_cmp_ret.numsop = cxp_solicitudes.numsol ";
		}
		if($ld_fecdes!="")
		{
			$ls_criterio=$ls_criterio."		AND scb_cmp_ret.fecrep >= '".$ld_fecdes."'";
		}
		if($ld_fechas!="")
		{
			$ls_criterio=$ls_criterio."		AND scb_cmp_ret.fecrep <= '".$ld_fechas."'";
		}
		if($ls_codprodes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro >= '".$ls_codprodes."'";
		}
		if($ls_codprohas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro <= '".$ls_codprohas."'";
		}
		if($ls_cedbendes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene >= '".$ls_cedbendes."'";
		}
		if($ls_cedbenhas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene <= '".$ls_cedbenhas."'";
		}
		$ls_periodofiscal = $ls_anio.$ls_mes;
		$ls_where="";
		if($ls_numsol!="")
		{
			$ls_where=" AND scb_dt_cmp_ret.numsop='".$ls_numsol."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc "; 
			$ls_filtrofrom = " , cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT DISTINCT scb_cmp_ret.numcom, scb_cmp_ret.fecrep, scb_cmp_ret.nomsujret ".
				"  FROM scb_cmp_ret, scb_dt_cmp_ret ".$ls_tabla.$ls_filtrofrom.
				" WHERE scb_cmp_ret.codemp = '".$ls_codemp."' ".
				"   AND scb_cmp_ret.codret = '0000000004' ".
				"   AND scb_cmp_ret.perfiscal = '".$ls_periodofiscal."' ".
				$ls_where.
				$ls_criterio.
				"	AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp  ".
				"   AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
				"   AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ".$ls_filtroest.
			//	"   AND scb_dt_cmp_ret.numsop = cxp_solicitudes.numsol ".
				" ORDER BY scb_cmp_ret.numcom ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Retenciones Aporte Social ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			$lo_title[1]="<input type=checkbox name=checkall id=checkall value=1 size=10 style=text-align:left  class=sin-borde onclick='javascript:uf_checkall();'>";
			$lo_title[2]="Nro Comprobante"; 
			$lo_title[3]="Fecha"; 
			$lo_title[4]="Proveedor / Beneficiario"; 
			$li_totrow=0;
			$lo_object[$li_totrow][1]="";
			$lo_object[$li_totrow][2]="";
			$lo_object[$li_totrow][3]="";
			$lo_object[$li_totrow][4]="";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li_totrow++;
				$ls_numcom=$row["numcom"];
				$ls_nomsujret=$row["nomsujret"];
				$ld_fecrep=$io_funciones->uf_convertirfecmostrar($row["fecrep"]);
				$lo_object[$li_totrow][1]="<input type=checkbox name=checkcmp".$li_totrow."     id=checkcmp".$li_totrow."     value=1                   size=10 style=text-align:left    class=sin-borde>"; 
				$lo_object[$li_totrow][2]="<input type=text     name=txtnumcom".$li_totrow."    id=txtnumcom".$li_totrow."    value='".$ls_numcom."'    size=15 style=text-align:center  class=sin-borde readonly>"; 
				$lo_object[$li_totrow][3]="<input type=text     name=txtfecrep".$li_totrow."    id=txtfecrep".$li_totrow."    value='".$ld_fecrep."'    size=10 style=text-align:center  class=sin-borde readonly>";
				$lo_object[$li_totrow][4]="<input type=text     name=txtnomsujret".$li_totrow." id=txtnomsujret".$li_totrow." value='".$ls_nomsujret."' size=75 style=text-align:left    class=sin-borde readonly title='".$ls_nomsujret."' bgColor=#FF5500>";
			}
			$io_sql->free_result($rs_data);
			$io_grid->makegrid($li_totrow,$lo_title,$lo_object,550,'Comprobantes de Retención de IVA','grid');
		}	unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_retencionesaporte
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_retencionesmunicipales()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_retencionesmunicipales
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de retenciones de iva
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 15/07/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha=new class_fecha();
		$ls_criterio='';
		$ls_join    ='';		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];

		$ls_mes=$_POST['mes'];
		$ls_anio=$_POST['anio'];
		$ld_fecdesde=$ls_anio."-".$ls_mes."-01";
		$ld_fechasta=$ls_anio."-".$ls_mes."-".substr($io_fecha->uf_last_day($ls_mes,$ls_anio),0,2);
		$ls_codprobendes = $_POST['codprobendes'];
		$ls_codprobenhas = $_POST['codprobenhas'];
		$ls_numsol       = $_POST['numsol'];
		if ($_POST['fecdes']!='' && $_POST['fechas']!='') {
			$ld_fecdesde       = $io_funciones->uf_convertirdatetobd($_POST['fecdes']);
			$ld_fechasta       = $io_funciones->uf_convertirdatetobd($_POST['fechas']);
		}
		
		if($ls_codprobendes!=""){ 
			$ls_criterio .= " AND scb_cmp_ret.codsujret >= '".$ls_codprobendes."' ";
		}
		
		if($ls_codprobenhas!=""){
			$ls_criterio .= " AND scb_cmp_ret.codsujret <= '".$ls_codprobenhas."' ";
		}
		
		if($ls_numsol!=""){
			$ls_criterio .= " AND scb_dt_cmp_ret.numsop='".$ls_numsol."' ";
		}
		$ls_periodofiscal = $ls_anio.$ls_mes;
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT DISTINCT scb_cmp_ret.numcom, scb_cmp_ret.codsujret, scb_cmp_ret.nomsujret, scb_cmp_ret.dirsujret, scb_cmp_ret.rif ".
				"  FROM scb_cmp_ret,scb_dt_cmp_ret ".$ls_filtrofrom.
				" WHERE scb_cmp_ret.codemp = '".$ls_codemp."' ".
				"   AND scb_cmp_ret.perfiscal = '".$ls_periodofiscal."' ".
				"   AND scb_cmp_ret.fecrep>='".$ld_fecdesde."' ".
				"   AND scb_cmp_ret.fecrep<='".$ld_fechasta."' ".
				"   AND scb_cmp_ret.codret='0000000003' ".
		        "   AND scb_cmp_ret.codemp=scb_dt_cmp_ret.codemp ".
		        "   AND scb_cmp_ret.codret=scb_dt_cmp_ret.codret ".
		        "   AND scb_cmp_ret.numcom=scb_dt_cmp_ret.numcom {$ls_criterio} {$ls_filtroest}".
				" ORDER BY scb_cmp_ret.numcom";	
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Retenciones Muncipales ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			$lo_title[1]=" ";
			$lo_title[2]="Comprobante";     
			$lo_title[3]="Codigo Proveedor/Beneficiario";   
			$lo_title[4]="Nombre Proveedor/Beneficiario";   
			$lo_title[5]="Dirección";  
			$lo_title[6]="R.I.F.";   
			$li_totrow=0;
			$lo_object[$li_totrow][1]="";
			$lo_object[$li_totrow][2]="";
			$lo_object[$li_totrow][3]="";
			$lo_object[$li_totrow][4]="";
			$lo_object[$li_totrow][5]="";
			$lo_object[$li_totrow][6]="";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li_totrow++;
				$ls_numcom=$row["numcom"];
				$ls_codsujret=$row["codsujret"];
				$ls_nomsujret=$row["nomsujret"];
				$ls_dirsujret=$row["dirsujret"];
				$ls_rif=$row["rif"];
				$lo_object[$li_totrow][1]="<input type=checkbox name=checkcmp".$li_totrow."     id=checkcmp".$li_totrow."     value=1                   size=10 style=text-align:left    class=sin-borde>"; 
				$lo_object[$li_totrow][2]="<div align=center><input type=text name=txtnumcom".$li_totrow."    id=txtnumcom".$li_totrow."    value='".$ls_numcom."'    class=sin-borde readonly style=text-align:center size=15 maxlength=15></div>";
				$lo_object[$li_totrow][3]="<div align=center><input type=text name=txtcodsujret".$li_totrow." id=txtcodsujret".$li_totrow." value='".$ls_codsujret."' class=sin-borde readonly style=text-align:center size=10 maxlength=10></div>";
				$lo_object[$li_totrow][4]="<div align=left><input   type=text name=txtnomsujret".$li_totrow." id=txtnomsujret".$li_totrow." value='".$ls_nomsujret."' class=sin-borde readonly style=text-align:left size=25 maxlength=80></div>";
				$lo_object[$li_totrow][5]="<div align=left><input   type=text name=txtdirsujret".$li_totrow." id=txtdirsujret".$li_totrow." value='".$ls_dirsujret."' class=sin-borde readonly style=text-align:left size=35 maxlength=200></div>";
				$lo_object[$li_totrow][6]="<div align=center><input type=text name=txtrif".$li_totrow."       id=txtrif".$li_totrow."       value='".$ls_rif."'       class=sin-borde readonly style=text-align:center size=15 maxlength=15></div>";
			}
			$io_sql->free_result($rs_data);
			$io_grid->makegrid($li_totrow,$lo_title,$lo_object,750,'Comprobantes de Retención Municipal','grid');
		}
		unset($io_include);
		unset($io_conexion);

		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_retencionesmunicipales
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_recepcionesncnd()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funcion que obtiene e imprime los resultados de la busqueda de las recepciones de documento asociadas
		//				   a la solicitud de pago seleccionada
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin:  08/04/2007 								Fecha ltima Modificacin : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_numord=$_POST["numord"];
		$ls_tipo=$_POST["tipo"];
		$ls_codproben=$_POST["codproben"];
		$ls_orden    = $io_funciones_cxp->uf_obtenervalor("orden","");
		$ls_campoorden=$io_funciones_cxp->uf_obtenervalor("campoorden","");
		$li=0;
		$ls_aux="";
		$ls_codpro="";
		$ls_cedbene="";
		if($ls_tipo=='P')
		{
			$ls_destino="Proveedor";
			$ls_cedbene="";
			$ls_codpro=$ls_codproben;
			$ls_aux=" AND sol.cod_pro='".$ls_codproben."' ";
		}
		elseif($ls_tipo=='B')
		{
			$ls_destino="Beneficiario";
			$ls_codpro="";
			$ls_cedbene=$ls_codproben;
			$ls_aux=" AND trim(sol.ced_bene) = '".trim($ls_codproben)."' ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat = $io_conexion->Concat('rd.codestpro','rd.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND rd.codemp = dt.codemp ".
							" AND rd.numrecdoc = dt.numrecdoc ".
							" AND rd.codtipdoc = dt.codtipdoc ".
							" AND rd.ced_bene = dt.ced_bene ".
							" AND rd.cod_pro = dt.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg rd ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql=" SELECT sol.numsol,trim(dt.numrecdoc) as numrecdoc,dt.codtipdoc,dt.monto,sol.tipproben,sol.fecemisol,dt.codtipdoc,doc.dentipdoc,doc.estcon,doc.estpre".
				" FROM	 cxp_dt_solicitudes dt,cxp_solicitudes sol,cxp_documento doc".$ls_filtrofrom.
				" WHERE  sol.codemp='".$ls_codemp."' AND sol.codemp=dt.codemp AND dt.codtipdoc=doc.codtipdoc".
				" AND    sol.numsol ='".$ls_numord."' ".$ls_aux." AND sol.numsol=dt.numsol ".$ls_filtroest.
				" ORDER BY ".$ls_campoorden." ".$ls_orden." ";

		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error en catalogo de Recepciones de Documento","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)."  SQL: ".$ls_sql,true,"javascript: ue_close();"); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td style='cursor:pointer' title='Ordenar por Numero de Orden'     align='center' onClick=ue_orden('sol.numsol')>Numero de Orden</td>";
			print "<td style='cursor:pointer' title='Ordenar por Numero de Recepcion' align='center' onClick=ue_orden('dt.numrecdoc')>Numero de Recepcion</td>";
			print "<td style='cursor:pointer' title='Ordenar por Fecha'               align='center' onClick=ue_orden('sol.fecemisol')>Fecha de Emision</td>";
			print "<td style='cursor:pointer' title='Ordenar por Monto'               align='center' onClick=ue_orden('dt.monto')>Monto Recepcion</td>";
			print "<td style='cursor:pointer' title='Ordenar por Tipo Documento'      align='center' onClick=ue_orden('dt.codtipdoc')>Tipo Documento</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li++;
				$ls_numord=$row["numsol"];
				$ls_numrecdoc=$row["numrecdoc"];
				$ld_fecha =$io_funciones->uf_convertirfecmostrar($row["fecemisol"]);
				$ls_tipproben=$row["tipproben"];
				$ldec_monto=$row["monto"];
				$ls_codtipdoc=$row["codtipdoc"];
				$ls_dentipdoc=$row["dentipdoc"];
				$li_estcon   =$row["estcon"];
				$li_estpre   =$row["estpre"];
				print "<tr class=celdas-blancas>";
				print "<td align='center'><a href=\"javascript: aceptar('$ls_codemp','$ls_numrecdoc','$ls_codtipdoc','$ls_dentipdoc','$ls_tipo','$ls_codpro','$ls_cedbene','$li_estcon','$li_estpre');\">".$ls_numord."</a></td>";
				print "<td align='center'>".$ls_numrecdoc."</td>";
				print "<td align='center'>".$ld_fecha."</td>";
				print "<td align='right'>".number_format($ldec_monto,2,",",".")."</td>";	
				print "<td align='center'>".$ls_dentipdoc."</td>";	
				print "</tr>";			
			}
			if($li==0)
			{
				$io_mensajes->uf_mensajes_ajax("Informacion","No hay datos para mostrar",true,"javascript: ue_close();"); 				
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql);
		unset($io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_reecepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_dtpresupuestario()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_dtpresupuestario
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funcin que obtiene e imprime los resultados de la busqueda del detalle presupestario de la recepcion
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin:  08/04/2007 								Fecha ltima Modificacin : 03/06/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();	
		require_once("../../shared/class_folder/grid_param.php");
		$io_grid=new grid_param();		
		require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
		$io_ds_cuentasspg=new class_datastore();
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_modalidad=$_SESSION['la_empresa']['estmodest'];
		$ls_numrecdoc=$io_funciones_cxp->uf_obtenervalor("numrecdoc",""); 
		$ls_codtipdoc=$io_funciones_cxp->uf_obtenervalor("codtipdoc",""); 
		$ls_tipproben=$io_funciones_cxp->uf_obtenervalor("tipproben",""); 
		$ls_codproben=$io_funciones_cxp->uf_obtenervalor("codproben",""); 
		$ls_tiponota=$io_funciones_cxp->uf_obtenervalor("tiponota",""); 
		$li=0;
		$ls_aux="";
		$ls_codpro="";
		$ls_cedbene="";
		if($ls_tipproben=='P')
		{
			$ls_destino="Proveedor";
			$ls_cedbene="";
			$ls_codpro=$ls_codproben;
			$ls_aux=" AND cxp_rd_spg.cod_pro='".$ls_codproben."' ";
			$ls_aux2=" AND cxp_rd_cargos.cod_pro='".$ls_codproben."' ";
		}
		elseif($ls_tipproben=='B')
		{
			$ls_destino="Beneficiario";
			$ls_codpro="";
			$ls_cedbene=$ls_codproben;
			$ls_aux=" AND cxp_rd_spg.ced_bene='".$ls_codproben."' ";
			$ls_aux2=" AND cxp_rd_cargos.ced_bene='".$ls_codproben."' ";
		}
//		$ls_cadena1=$io_conexion->Concat('spg.codestpro1','spg.codestpro2','spg.codestpro3','spg.codestpro4','spg.codestpro5');
//		$ls_aux_estpro=" AND rd.codestpro={$ls_cadena1} ";
//		$ls_cadena2=$io_conexion->Concat('rd.codestpro','rd.spg_cuenta','rd.ced_bene','rd.cod_pro','rd.codtipdoc','rd.numrecdoc','rd.numdoccom');
//		$ls_cadena3=$io_conexion->Concat('x.codestpro1','x.codestpro2','x.codestpro3','x.codestpro4','x.codestpro5','x.spg_cuenta','x.ced_bene','x.cod_pro','x.codtipdoc','x.numrecdoc','x.numdoccom');
//		$ls_aux_where =" AND {$ls_cadena2} NOT IN (SELECT {$ls_cadena3} FROM cxp_rd_cargos x) ";
////		switch ($_SESSION["ls_gestor"])
////		{
////			case "MYSQLT":
////				$ls_aux_estpro=" AND rd.codestpro=CONCAT(spg.codestpro1,spg.codestpro2,spg.codestpro3,spg.codestpro4,spg.codestpro5) ";
////				$ls_aux_where =" AND CONCAT(rd.codestpro,rd.spg_cuenta,rd.ced_bene,rd.cod_pro,rd.codtipdoc,rd.numrecdoc,rd.numdoccom)
////								 NOT IN (SELECT CONCAT(x.codestpro1,x.codestpro2,x.codestpro3,x.codestpro4,x.codestpro5,x.spg_cuenta,
////													   x.ced_bene,x.cod_pro,x.codtipdoc,x.numrecdoc,x.numdoccom) FROM cxp_rd_cargos x) ";
////				break;
////			case "MYSQLI":
////				$ls_aux_estpro=" AND rd.codestpro=CONCAT(spg.codestpro1,spg.codestpro2,spg.codestpro3,spg.codestpro4,spg.codestpro5) ";
////				$ls_aux_where =" AND CONCAT(rd.codestpro,rd.spg_cuenta,rd.ced_bene,rd.cod_pro,rd.codtipdoc,rd.numrecdoc,rd.numdoccom)
////								 NOT IN (SELECT CONCAT(x.codestpro1,x.codestpro2,x.codestpro3,x.codestpro4,x.codestpro5,x.spg_cuenta,
////													   x.ced_bene,x.cod_pro,x.codtipdoc,x.numrecdoc,x.numdoccom) FROM cxp_rd_cargos x) ";
////				break;
////			case "POSTGRES":
////				$ls_aux_estpro=" AND rd.codestpro=spg.codestpro1||spg.codestpro2||spg.codestpro3||spg.codestpro4||spg.codestpro5 ";
////				$ls_aux_where =" AND rd.codestpro||rd.spg_cuenta||rd.ced_bene||rd.cod_pro||rd.codtipdoc||rd.numrecdoc||rd.numdoccom
////								 NOT IN (SELECT (x.codestpro1||x.codestpro2||x.codestpro3||x.codestpro4||x.codestpro5||x.spg_cuenta||
////													   x.ced_bene||x.cod_pro||x.codtipdoc||x.numrecdoc||x.numdoccom) FROM cxp_rd_cargos x) ";
////				break;
////			case "INFORMIX":
////				$ls_aux_estpro=" AND rd.codestpro=spg.codestpro1||spg.codestpro2||spg.codestpro3||spg.codestpro4||spg.codestpro5 ";
////				$ls_aux_where =" AND rd.codestpro||rd.spg_cuenta||rd.ced_bene||rd.cod_pro||rd.codtipdoc||rd.numrecdoc||rd.numdoccom
////								 NOT IN (SELECT (x.codestpro1||x.codestpro2||x.codestpro3||x.codestpro4||x.codestpro5||x.spg_cuenta||
////													   x.ced_bene||x.cod_pro||x.codtipdoc||x.numrecdoc||x.numdoccom) FROM cxp_rd_cargos x) ";
////				break;
////		}
//		//FILTRO POR ESTRUCTURA CASO BAER 
//		$ls_filtroest = '';
//		$ls_filtrofrom = '';
//		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
//		{
//			$ls_estconcat1 = $io_conexion->Concat('rd.codestpro','rd.estcla');
//			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
//			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
//			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ";
//		}
//		//FILTRO POR ESTRUCTURA CASO BAER 
//		$ls_sql=" SELECT rd.codemp, rd.numrecdoc, rd.codtipdoc, rd.ced_bene, rd.cod_pro, rd.procede_doc, rd.numdoccom, 
//		                 rd.codestpro,trim(rd.spg_cuenta) as spg_cuenta, rd.monto,rd.estcla,spg.denominacion,
//						 trim(spg.sc_cuenta) as sc_cuenta,scg.denominacion as denscg, rd.procede_doc,rd.codfuefin
//				    FROM cxp_rd_spg rd,spg_cuentas spg,scg_cuentas scg $ls_filtrofrom
//				   WHERE rd.codemp='".$ls_codemp."'
//				     AND rd.numrecdoc='".$ls_numrecdoc."' 
//				     AND rd.codtipdoc='".$ls_codtipdoc."' $ls_aux $ls_filtroest
//				     AND rd.codemp=spg.codemp AND rd.spg_cuenta=spg.spg_cuenta AND rd.codemp=scg.codemp
//				     AND spg.sc_cuenta=scg.sc_cuenta $ls_aux_estpro $ls_aux_where
//			       ORDER BY rd.spg_cuenta ASC" ;

		switch ($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_concat="CONCAT(cxp_rd_cargos.codestpro1,cxp_rd_cargos.codestpro2,cxp_rd_cargos.codestpro3,cxp_rd_cargos.codestpro4,cxp_rd_cargos.codestpro5) ";
			break;
			
			case "MYSQLI":
				$ls_concat="CONCAT(cxp_rd_cargos.codestpro1,cxp_rd_cargos.codestpro2,cxp_rd_cargos.codestpro3,cxp_rd_cargos.codestpro4,cxp_rd_cargos.codestpro5) ";
			break;
			
			default: //  POSTGRES
				$ls_concat="(cxp_rd_cargos.codestpro1||cxp_rd_cargos.codestpro2||cxp_rd_cargos.codestpro3||cxp_rd_cargos.codestpro4||cxp_rd_cargos.codestpro5)";
			break;
		}
		$ls_sql="SELECT cxp_rd_spg.numdoccom, cxp_rd_spg.codestpro, cxp_rd_spg.spg_cuenta, cxp_rd_spg.monto AS monto, spg_cuentas.sc_cuenta, ".
				"		cxp_rd_spg.procede_doc, cxp_rd_spg.codfuefin, cxp_rd_spg.estcla, '' AS cargo, spg_cuentas.denominacion AS denominacion,".
				"       (SELECT denominacion FROM scg_cuentas WHERE trim(scg_cuentas.codemp) = trim(spg_cuentas.codemp) AND trim(scg_cuentas.sc_cuenta) = trim(spg_cuentas.sc_cuenta)) AS denscg".
				"  FROM cxp_rd_spg, spg_cuentas ".
				" WHERE cxp_rd_spg.codemp = '".$ls_codemp."'".
				"	AND trim(cxp_rd_spg.numrecdoc) = '".trim($ls_numrecdoc)."'".
				"	AND cxp_rd_spg.codtipdoc = '".$ls_codtipdoc."'".$ls_aux.
				"   AND cxp_rd_spg.codemp = spg_cuentas.codemp ".
				"   AND substr(cxp_rd_spg.codestpro,1,25) = spg_cuentas.codestpro1 ".
				"   AND substr(cxp_rd_spg.codestpro,26,25) = spg_cuentas.codestpro2 ".
				"   AND substr(cxp_rd_spg.codestpro,51,25) = spg_cuentas.codestpro3 ".
				"   AND substr(cxp_rd_spg.codestpro,76,25) = spg_cuentas.codestpro4 ".
				"   AND substr(cxp_rd_spg.codestpro,101,25) = spg_cuentas.codestpro5 ".
				"   AND cxp_rd_spg.estcla = spg_cuentas.estcla ".
				"   AND cxp_rd_spg.spg_cuenta = spg_cuentas.spg_cuenta ".
				" UNION ".
				"SELECT cxp_rd_cargos.numdoccom,  ".$ls_concat." AS codestpro,".
				"       cxp_rd_cargos.spg_cuenta, -cxp_rd_cargos.monret AS monto, spg_cuentas.sc_cuenta, ".
				"		cxp_rd_cargos.procede_doc,".
				"      (SELECT cxp_rd_spg.codfuefin FROM cxp_rd_spg".
				" 		 WHERE cxp_rd_spg.codemp=cxp_rd_cargos.codemp AND cxp_rd_spg.numrecdoc=cxp_rd_cargos.numrecdoc".
				"          AND cxp_rd_spg.codtipdoc=cxp_rd_cargos.codtipdoc AND cxp_rd_spg.cod_pro=cxp_rd_cargos.cod_pro".
				"          AND cxp_rd_spg.ced_bene=cxp_rd_cargos.ced_bene AND cxp_rd_spg.spg_cuenta=cxp_rd_cargos.spg_cuenta".
				"		   AND substr(cxp_rd_spg.codestpro,1,25) = cxp_rd_cargos.codestpro1  AND substr(cxp_rd_spg.codestpro,26,25) = cxp_rd_cargos.codestpro2 ".
				" 		   AND substr(cxp_rd_spg.codestpro,51,25) = cxp_rd_cargos.codestpro3  AND substr(cxp_rd_spg.codestpro,76,25) = cxp_rd_cargos.codestpro4 ".
				"   AND substr(cxp_rd_spg.codestpro,101,25) = cxp_rd_cargos.codestpro5 AND cxp_rd_spg.estcla = cxp_rd_cargos.estcla) AS codfuefin,".
				"       cxp_rd_cargos.estcla,  '' AS cargo, spg_cuentas.denominacion AS denominacion,".
				"       (SELECT denominacion FROM scg_cuentas WHERE trim(scg_cuentas.codemp) = trim(spg_cuentas.codemp) AND trim(scg_cuentas.sc_cuenta) = trim(spg_cuentas.sc_cuenta)) AS denscg".
				"  FROM cxp_rd_cargos, spg_cuentas ".
				" WHERE cxp_rd_cargos.codemp = '".$ls_codemp."'".
				"	AND trim(cxp_rd_cargos.numrecdoc) = '".trim($ls_numrecdoc)."'".
				"	AND cxp_rd_cargos.codtipdoc = '".$ls_codtipdoc."'".$ls_aux2.
				"   AND cxp_rd_cargos.codemp = spg_cuentas.codemp ".
				"   AND cxp_rd_cargos.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND cxp_rd_cargos.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND cxp_rd_cargos.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND cxp_rd_cargos.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND cxp_rd_cargos.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND cxp_rd_cargos.estcla = spg_cuentas.estcla ".
				"   AND trim(cxp_rd_cargos.spg_cuenta) = trim(spg_cuentas.spg_cuenta) ".
				" ORDER BY numdoccom ";
		$rs_data=$io_sql->select($ls_sql);
		
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar detalles presupuestarios","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)."  SQL: ".$ls_sql,true,"javascript: ue_close();"); 
		}
		else
		{
			if($row=$io_sql->fetch_row($rs_data))
			{
				$io_ds_cuentasspg->data=$io_sql->obtener_datos($rs_data);
				$io_ds_cuentasspg->group_by(array('0'=>'numdoccom','1'=>'codestpro','2'=>'spg_cuenta','3'=>'estcla'),array('0'=>'monto'),'numdoccom');
				$rs_datacuentas=$io_ds_cuentasspg->data; 
			}

			if($rs_datacuentas!="")
				$li_totrowrs=count((array)$rs_datacuentas["numdoccom"]);
	
			for($li_i=1;$li_i<=$li_totrowrs;$li_i++)
			{
				$ls_numdoccom=trim($rs_datacuentas["numdoccom"][$li_i]);
				$ls_codestpro=trim($rs_datacuentas["codestpro"][$li_i]);
				$arrResultado=$io_funciones_cxp->uf_formatoprogramatica($ls_codestpro,$ls_programatica);
				$ls_programatica=$arrResultado["as_programatica"];
				$ls_estcla=trim($rs_datacuentas["estcla"][$li_i]);
				$ls_spgcuenta=trim($rs_datacuentas["spg_cuenta"][$li_i]);
				$ls_scgcuenta=trim($rs_datacuentas["sc_cuenta"][$li_i]);
				$ls_cargo=trim($rs_datacuentas["cargo"][$li_i]);
				$ls_procededoc=trim($rs_datacuentas["procede_doc"][$li_i]);
				$ldec_monto=$rs_datacuentas["monto"][$li_i];
				$ls_codfuefin=trim($rs_datacuentas["codfuefin"][$li_i]);
				$ls_dencuenta=utf8_encode($rs_datacuentas["denominacion"][$li_i]);
				$ls_denscg=utf8_encode($rs_datacuentas["denscg"][$li_i]);
				switch($ls_estcla)
				{
					case "A":
						$ls_estatus=utf8_encode("Acción");
						break;
					case "P":
						$ls_estatus=utf8_encode("Proyecto");
						break;
				}
				$ldec_montoant=uf_verificar_anterior($ls_numrecdoc,$ls_codtipdoc,$ls_tipproben,$ls_codproben,$ls_codestproaux,$ls_estcla,$ls_spgcuenta);
				if($ls_tiponota=='NC')
				{
					$ldec_disponible=$ldec_monto-abs($ldec_montoant);
				}
				else
				{
					$ldec_disponible=$ldec_monto+abs($ldec_montoant);
				}
		
				$ls_estatus="";
				switch($ls_estcla)
				{
					case "A":
						$ls_estatus="Acción";
						break;
					case "P":
						$ls_estatus="Proyecto";
						break;
				}
				if($ldec_disponible>0)
				{
					$li++;			
					//$ls_dencuenta=$row["denominacion"];
					//$ls_scgcuenta=$row["sc_cuenta"];
					//$ls_denscg=$row["denscg"];
					//$ls_procededoc=$row["procede_doc"];
					//$ls_numdoccom=$row["numdoccom"];
					$lo_object[$li][1]="<input type=checkbox name=chk".$li."      id=chk".$li." class=sin-borde >";
					$lo_object[$li][2]="<input type=text name=txtnumdoccom".$li."    class=sin-borde style=text-align:center size=15 value='".$ls_numdoccom."'    readonly><input name=txtprocede".$li." type=hidden id=txtprocede".$li." value='".$ls_procededoc."'>";
					$lo_object[$li][3]="<input type=text name=txtcodestpro".$li."    class=sin-borde style=text-align:center size=20 value='".$ls_programatica."'    readonly><input name=txtcodpro".$li." type=hidden id=txtcodpro".$li." value='".$ls_codestpro."'>";
					$lo_object[$li][4]="<input type=text name=txtestclaaux".$li."    class=sin-borde style=text-align:center size=10 value='".$ls_estatus."'    readonly><input name=txtestcla".$li." type=hidden id=txtestcla".$li." value='".$ls_estcla."'>";
					$lo_object[$li][5]="<input type=text name=txtspgcuenta".$li."    class=sin-borde style=text-align:center size=15  value='".$ls_spgcuenta."'     readonly><input type=hidden name=txtscgcuenta".$li."  value='".$ls_scgcuenta."'><input type=hidden name=txtdenscgcuenta".$li."  value='".$ls_denscg."'>"; 
					$lo_object[$li][6]="<input type=text name=txtmonto".$li."        class=sin-borde style=text-align:right  size=10 value='".number_format($ldec_disponible,2,",",".")."' onBlur='javascript:uf_format(this);uf_valida_monto($li);'  onKeyPress=return(ue_formatonumero(this,'.',',',event));><input type=hidden name=txtmontooriginal".$li." value='".$ldec_disponible."'>";
					$lo_object[$li][7]="<input type=text name=txtdencuenta".$li."    class=sin-borde style=text-align:left  size=40 value='".$ls_dencuenta."' readonly>";
					$lo_object[$li][8]="<input type=text name=txtcodfuefin".$li."    class=sin-borde style=text-align:center  size=10 value='".$ls_codfuefin."' readonly>";
				}
				else
				{
					if($ls_tiponota=='ND')
					{
						if($ldec_disponible>0){
						$li++;			
						$ls_dencuenta=$row["denominacion"];
						$ls_scgcuenta=$row["sc_cuenta"];
						$ls_denscg=$row["denscg"];
						$ls_denscg=$row["procede_doc"];
						$lo_object[$li][1]="<input type=checkbox name=chk".$li."      id=chk".$li." class=sin-borde >";
						$lo_object[$li][2]="<input type=text name=txtnumdoccom".$li."    class=sin-borde style=text-align:center size=15 value=''    readonly><input name=txtprocede".$li." type=hidden id=txtprocede".$li." value=''>";
						$lo_object[$li][3]="<input type=text name=txtcodestpro".$li."    class=sin-borde style=text-align:center size=20 value='".$ls_codestpro."'    readonly>";
						$lo_object[$li][4]="<input type=text name=txtestclaaux".$li."    class=sin-borde style=text-align:center size=10 value='".$ls_estatus."'    readonly><input name=txtestcla".$li." type=hidden id=txtestcla".$li." value='".$ls_estcla."'>";
						$lo_object[$li][5]="<input type=text name=txtspgcuenta".$li."    class=sin-borde style=text-align:center size=15  value='".$ls_spgcuenta."'     readonly><input type=hidden name=txtscgcuenta".$li."  value='".$ls_scgcuenta."'><input type=hidden name=txtdenscgcuenta".$li."  value='".$ls_denscg."'>"; 
						$lo_object[$li][6]="<input type=text name=txtmonto".$li."        class=sin-borde style=text-align:right  size=10 value='".number_format($ldec_monto,2,",",".")."' onBlur='javascript:uf_format(this);uf_valida_monto($li);'  onKeyPress=return(ue_formatonumero(this,'.',',',event));><input type=hidden name=txtmontooriginal".$li." value='".$ldec_monto."'>";
						$lo_object[$li][7]="<input type=text name=txtdencuenta".$li."    class=sin-borde style=text-align:left  size=40 value='".$ls_dencuenta."' readonly>";
						$lo_object[$li][8]="<input type=text name=txtcodfuefin".$li."    class=sin-borde style=text-align:center  size=10 value='".$ls_codfuefin."' readonly>";
						}
					}
				}
			}
			
			if($li==0)
			{
				$io_mensajes->uf_mensajes_ajax("Informacion","No hay datos para mostrar",true,"javascript: ue_close();");
				$lo_object=array();				
			}
			// Titulos del Grid de Bienes
			$lo_title[1]=" ";
			$lo_title[2]="Compromiso";
			$lo_title[3]="Codigo Programatico";
			$lo_title[4]="Estatus";
			$lo_title[5]="Codigo Estadistico";
			$lo_title[6]="Monto";
			$lo_title[7]="Denominaci&oacute;n";
			$lo_title[8]="Fuente de Financiamiento";
			print "<input name=totalrows type=hidden id=totalrows value=$li>";
			$io_grid->makegrid($li,$lo_title,$lo_object,758,"Registrar Detalle Presupuestario","grid");
			$io_sql->free_result($rs_data);	
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_dtpresupuestario
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_dtingresoblanco()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_dtingresoblanco
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funcin que obtiene e imprime los resultados de la busqueda del detalle presupestario de la recepcion
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin:  08/04/2007 								Fecha ltima Modificacin : 03/06/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();	
		require_once("../../shared/class_folder/grid_param.php");
		$io_grid=new grid_param();		
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_modalidad=$_SESSION['la_empresa']['estmodest'];
		$ls_numrecdoc=$io_funciones_cxp->uf_obtenervalor("numrecdoc",""); 
		$ls_codtipdoc=$io_funciones_cxp->uf_obtenervalor("codtipdoc",""); 
		$ls_tipproben=$io_funciones_cxp->uf_obtenervalor("tipproben",""); 
		$ls_codproben=$io_funciones_cxp->uf_obtenervalor("codproben",""); 
		$ls_tiponota=$io_funciones_cxp->uf_obtenervalor("tiponota",""); 
		$li=1;
		$lo_object[$li][1]="";
		$lo_object[$li][2]="<input type=text name=txtcodestpro".$li."  id=txtcodestpro".$li."    class=sin-borde style=text-align:center size=37 value=''    readonly><input name=txtcodpro".$li." type=hidden id=txtcodpro".$li." value=''>";
		$lo_object[$li][3]="<input type=text name=txtestclaaux".$li."    class=sin-borde style=text-align:center size=20 value=''    readonly><input name=txtestcla".$li." type=hidden id=txtestcla".$li." value=''>";
		$lo_object[$li][4]="<input type=text name=txtspicuenta".$li."    class=sin-borde style=text-align:center size=16  value=''     readonly><input type=hidden name=txtscgcuenta".$li."  value=''><input type=hidden name=txtdenscgcuenta".$li."  value=''>"; 
		$lo_object[$li][5]="<input type=text name=txtmonto".$li."        class=sin-borde style=text-align:right  size=20 value='' onBlur='javascript:uf_format(this);uf_valida_monto($li);'  onKeyPress=return(ue_formatonumero(this,'.',',',event));><input type=hidden name=txtmontooriginal".$li." value=''>";
		$lo_object[$li][6]="<input type=text name=txtdencuenta".$li."    class=sin-borde style=text-align:left  size=50 value='' readonly>";
		// Titulos del Grid de Bienes
		$lo_title[1]=" ";
		$lo_title[2]="Codigo Programatico";
		$lo_title[3]="Estatus";
		$lo_title[4]="Codigo Estadistico";
		$lo_title[5]="Monto";
		$lo_title[6]="Denominaci&oacute;n";
		print "<input name=totalrows type=hidden id=totalrows value=$li>";
		print "<input name=numrecdoc type=hidden id=totalrows value=$ls_numrecdoc>";
		print "<input name=codtipdoc type=hidden id=totalrows value=$ls_codtipdoc>";
		print "<input name=codproben type=hidden id=totalrows value=$ls_codproben>";
		print "<input name=tipproben type=hidden id=totalrows value=$ls_tipproben>";
		print "<input name=tiponota type=hidden id=totalrows value=$ls_tiponota>";
		$io_grid->makegrid($li,$lo_title,$lo_object,758,"Registrar Detalle de Ingreso","grid");
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_dtingreso
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_dtingreso()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_dtingreso
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funcin que obtiene e imprime los resultados de la busqueda del detalle presupestario de la recepcion
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin:  08/04/2007 								Fecha ltima Modificacin : 03/06/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();	
		require_once("../../shared/class_folder/grid_param.php");
		$io_grid=new grid_param();		
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_modalidad=$_SESSION['la_empresa']['estmodest'];
		$li_totrow=$io_funciones_cxp->uf_obtenervalor("totrow",1);
		for($li=1;$li<=$li_totrow;$li++)
		{
			$ls_codestpro=$io_funciones_cxp->uf_obtenervalor("txtcodestpro".$li,""); 
			$ls_codpro=$io_funciones_cxp->uf_obtenervalor("txtcodpro".$li,""); 
			$ls_estclaaux=$io_funciones_cxp->uf_obtenervalor("txtestclaaux".$li,""); 
			$ls_estcla=$io_funciones_cxp->uf_obtenervalor("txtestcla".$li,""); 
			$ls_spicuenta=$io_funciones_cxp->uf_obtenervalor("txtspicuenta".$li,""); 
			$ls_scgcuenta=$io_funciones_cxp->uf_obtenervalor("txtscgcuenta".$li,""); 
			$ls_denscgcuenta=$io_funciones_cxp->uf_obtenervalor("txtdenscgcuenta".$li,""); 
			$ls_monto=number_format($io_funciones_cxp->uf_obtenervalor("txtmonto".$li,""),2,',','.'); 
			$ls_montooriginal=number_format($io_funciones_cxp->uf_obtenervalor("txtmontooriginal".$li,""),2,',','.'); 
			$ls_dencuenta=$io_funciones_cxp->uf_obtenervalor("txtdencuenta".$li,""); 

			$lo_object[$li][1]="";
			$lo_object[$li][2]="<input type=text name=txtcodestpro".$li." id=txtcodestpro".$li."    class=sin-borde style=text-align:center size=37 value='".$ls_codestpro."'    readonly><input name=txtcodpro".$li." type=hidden id=txtcodpro".$li." value='".$ls_codpro."'>";
			$lo_object[$li][3]="<input type=text name=txtestclaaux".$li."    class=sin-borde style=text-align:center size=20 value='".$ls_estclaaux."'    readonly><input name=txtestcla".$li." type=hidden id=txtestcla".$li." value='".$ls_estcla."'>";
			$lo_object[$li][4]="<input type=text name=txtspicuenta".$li."    class=sin-borde style=text-align:center size=16  value='".$ls_spicuenta."'     readonly><input type=hidden name=txtscgcuenta".$li."  value='".$ls_scgcuenta."'><input type=hidden name=txtdenscgcuenta".$li."  value='".$ls_denscgcuenta."'>"; 
			$lo_object[$li][5]="<input type=text name=txtmonto".$li."        class=sin-borde style=text-align:right  size=20 value='".$ls_monto."' onBlur='javascript:uf_format(this);uf_valida_monto($li);'  onKeyPress=return(ue_formatonumero(this,'.',',',event));><input type=hidden name=txtmontooriginal".$li." value='".$ls_montooriginal."'>";
			$lo_object[$li][6]="<input type=text name=txtdencuenta".$li."    class=sin-borde style=text-align:left  size=50 value='".$ls_dencuenta."' readonly>";
		} 
		// Titulos del Grid de Bienes
		$lo_title[1]=" ";
		$lo_title[2]="Codigo Programatico";
		$lo_title[3]="Estatus";
		$lo_title[4]="Codigo Estadistico";
		$lo_title[5]="Monto";
		$lo_title[6]="Denominaci&oacute;n";
		print "<input name=totalrows type=hidden id=totalrows value=$li>";
		print "<input name=numrecdoc type=hidden id=totalrows value=$ls_numrecdoc>";
		print "<input name=codtipdoc type=hidden id=totalrows value=$ls_codtipdoc>";
		print "<input name=codproben type=hidden id=totalrows value=$ls_codproben>";
		print "<input name=tipproben type=hidden id=totalrows value=$ls_tipproben>";
		print "<input name=tiponota type=hidden id=totalrows value=$ls_tiponota>";
		$io_grid->makegrid($li-1,$lo_title,$lo_object,758,"Registrar Detalle de Ingreso","grid");
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_dtingreso
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cuentasspi()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_cuentasspi
		//		   Access: private
		//	    Arguments: 
		//	  Description: Método que inprime el resultado de la busqueda de las cuentas presupuestarias
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/04/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		require_once("../../shared/class_folder/class_sigesp_int.php");
		require_once("../../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../../shared/class_folder/class_sigesp_int_spg.php");
		$io_intspg=new class_sigesp_int_spg();		
		$ls_numrecdoc=$io_funciones_cxp->uf_obtenervalor("numrecdoc",""); 
		$ls_codtipdoc=$io_funciones_cxp->uf_obtenervalor("codtipdoc",""); 
		$ls_tipproben=$io_funciones_cxp->uf_obtenervalor("tipproben",""); 
		$ls_codproben=$io_funciones_cxp->uf_obtenervalor("codproben",""); 
		$ls_tiponota=$io_funciones_cxp->uf_obtenervalor("tiponota",""); 
		$ls_orden     = $io_funciones_cxp->uf_obtenervalor("orden","");
		$ls_campoorden=$io_funciones_cxp->uf_obtenervalor("campoorden","");
		$li=0;
		$ls_aux="";
		$ls_codpro="";
		$ls_cedbene="";
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		if($ls_tipproben=='P')
		{
			$ls_codpro=$ls_codproben;
			$ls_aux=" AND rd.cod_pro='".$ls_codproben."' ";
		}
		elseif($ls_tipproben=='B')
		{
			$ls_cedbene=$ls_codproben;
			$ls_aux=" AND rd.ced_bene='".$ls_codproben."' ";
		}
		$cadena_A = $io_conexion->Concat('spi.codestpro1','spi.codestpro2','spi.codestpro3','spi.codestpro4','spi.codestpro5');
		$ls_aux_estpro=" AND rd.codestpro='{$cadena_A}' ";
		if($ls_estpreing==1)
		{
			$ls_sql=" SELECT rd.codestpro,trim(spi.spi_cuenta) as cuenta, rd.estcla, MAX(spi_cuentas.denominacion) AS denominacion,
							 MAX(spi_cuentas.sc_cuenta) as sc_cuenta,MAX(scg_cuentas.denominacion) AS scgdenominacion
						FROM cxp_rd_spg rd,spi_cuentas_estructuras spi, spi_cuentas, scg_cuentas 
					   WHERE rd.codemp='".$ls_codemp."'
						 AND rd.numrecdoc='".$ls_numrecdoc."' 
						 AND rd.codtipdoc='".$ls_codtipdoc."' $ls_aux 
						 AND rd.codemp=spi_cuentas.codemp
						 AND spi.spi_cuenta=spi_cuentas.spi_cuenta $ls_aux_estpro
						 AND spi_cuentas.codemp=scg_cuentas.codemp
						 AND spi_cuentas.sc_cuenta=scg_cuentas.sc_cuenta
					   GROUP BY rd.codestpro,rd.estcla,spi.spi_cuenta 
					   ORDER BY $ls_campoorden $ls_orden" ;
		}
		else
		{
			$ls_sql=" SELECT '-----------------------------------------------------------------------------------------------------------------------------'AS codestpro,".
					"        trim(spi.spi_cuenta) as cuenta, '-' AS estcla, MAX(spi_cuentas.denominacion) AS denominacion,
							 MAX(spi_cuentas.sc_cuenta) as sc_cuenta,MAX(scg_cuentas.denominacion) AS scgdenominacion
						FROM cxp_rd_spg rd,spi_cuentas spi, spi_cuentas, scg_cuentas 
					   WHERE rd.codemp='".$ls_codemp."'
						 AND rd.numrecdoc='".$ls_numrecdoc."' 
						 AND rd.codtipdoc='".$ls_codtipdoc."' $ls_aux 
						 AND rd.codemp=spi_cuentas.codemp
						 AND spi.spi_cuenta=spi_cuentas.spi_cuenta
						 AND spi_cuentas.codemp=scg_cuentas.codemp
						 AND spi_cuentas.sc_cuenta=scg_cuentas.sc_cuenta
					   GROUP BY rd.codestpro,rd.estcla,spi.spi_cuenta 
					   ORDER BY $ls_campoorden $ls_orden" ;
		
		}
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar detalles presupuestarios","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)."  SQL: ".$ls_sql,true,"javascript: ue_close();"); 
		}
		else
		{
			print "<table width=620 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Programatica'          align='center' onClick=ue_orden('codestpro')>Estructura</td>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Clasificador'          align='center' onClick=ue_orden('estcla')>Estatus</td>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Cuenta de Ingreso' align='center' onClick=ue_orden('cuenta')>Ingreso</td>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Cuenta Contable'       align='center' onClick=ue_orden('sc_cuenta')>Contable</td>";
			print "<td width=170 style='cursor:pointer' title='Ordenar por Denominacion'          align='center' onClick=ue_orden('denominacion')>Denominacion</td>";
			print "</tr>";
			while(!$rs_data->EOF)
			{
				$ls_codestpro=$rs_data->fields["codestpro"];
				$ls_spicuenta=$rs_data->fields["cuenta"];
				$ls_denominacion=$rs_data->fields["denominacion"];
				$ls_sccuenta=$rs_data->fields["sc_cuenta"];
				$ls_estcla=$rs_data->fields["estcla"];
				$ls_scgdenominacion=$rs_data->fields["scgdenominacion"];
				$ls_monto="";
				$arrResultado=$io_funciones_cxp->uf_formatoprogramatica($ls_codestpro,$ls_programatica);
				$ls_programatica=$arrResultado["as_programatica"];
				switch($ls_estcla)
				{
					case "A":
						$ls_estatus="Accion";
						break;
					case "P":
						$ls_estatus="Proyecto";
						break;
					case "-":
						$ls_estatus="-";
						break;
				}

				print "<tr class=celdas-azules>";
				print "<td align='center'><a href=\"javascript: ue_aceptar('".$ls_codestpro."','".$ls_estcla."','".$ls_spicuenta."','".$ls_sccuenta."','".$ls_denominacion."','".$ls_programatica."','".$ls_estatus."','".$ls_scgdenominacion."','".$ls_monto."');\">".$ls_programatica."</a></td>";
				print "<td align='center'>".$ls_estatus."</td>";
				print "<td align='center'>".$ls_spicuenta."</td>";
				print "<td align='center'>".$ls_sccuenta."</td>";
				print "<td align='left'>".$ls_denominacion."</td>";
				print "</tr>";			

				$rs_data->MoveNext();
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp,$_SESSION["fechacomprobante"]);
	}// end function uf_print_cuentasspg
	//-----------------------------------------------------------------------------------------------------------------------------------




	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_anterior($ls_numrecdoc,$ls_codtipdoc,$ls_tipproben,$ls_codproben,$ls_codestproaux,$ls_estcla,$ls_spgcuenta)
	{
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();
		$io_function=new class_funciones();
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ldec_monto=0;
		if($ls_tipproben=='P')
		{
			$ls_aux=" AND cod_pro='".$ls_codproben."' ";
		}
		elseif($ls_tipproben=='B')
		{
			$ls_aux=" AND ced_bene='".$ls_codproben."' ";
		}
		$ls_sql=" SELECT SUM(monto) as monto  ".
				"   FROM  cxp_dc_spg ".
				"  WHERE  codemp='".$ls_codemp."' ".
				"    AND  numrecdoc='".$ls_numrecdoc."'".
				"    AND  codtipdoc='".$ls_codtipdoc."' ".$ls_aux. 
				"    AND  codestpro ='".$ls_codestproaux."'".
				"    AND  estcla ='".$ls_estcla."'".
				"    AND  spg_cuenta='".$ls_spgcuenta."' ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al calcular disponible presupuestario","ERROR->".$io_function->uf_convertirmsg($io_sql->message)."  SQL: ".$ls_sql,true,"javascript: ue_close();"); 
		}
		else
		{
			if($row=$io_sql->fetch_row($rs_data))
			{
				$ldec_monto=$row["monto"];
			}
			$io_sql->free_result($rs_data);
		}		
		return $ldec_monto;	
	}
	
	function uf_verificar_contable($ls_numrecdoc,$ls_codtipdoc,$ls_tipproben,$ls_codproben,$ls_scgcuenta,$ls_debhab)
	{
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ldec_monto=0;
		if($ls_tipproben=='P')
		{
			$ls_aux=" AND cod_pro='".$ls_codproben."' ";
		}
		elseif($ls_tipproben=='B')
		{
			$ls_aux=" AND ced_bene='".$ls_codproben."' ";
		}
		$ls_sql=" SELECT SUM(monto) as monto ".
				"   FROM  cxp_dc_scg  ".
				"  WHERE  codemp='".$ls_codemp."' ".
				"    AND  numrecdoc='".$ls_numrecdoc."' AND codtipdoc='".$ls_codtipdoc."' ".$ls_aux. 
				"    AND sc_cuenta='".$ls_scgcuenta."'  AND debhab='".$ls_debhab."' ";

		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al calcular disponible contable","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)."  SQL: ".$ls_sql,true,"javascript: ue_close();"); 
		}
		else
		{
			if($row=$io_sql->fetch_row($rs_data))
			{
				$ldec_monto=$row["monto"];
			}
			$io_sql->free_result($rs_data);
		}
		return $ldec_monto;	
	}
	
	
//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_dtcontable()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_dtcontable
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funcin que obtiene e imprime los resultados de la busqueda del detalle contable de la recepcion de documento
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin:  22/05/2007 								Fecha ltima Modificacin : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();	
		require_once("../../shared/class_folder/grid_param.php");
		$io_grid=new grid_param();		
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_numrecdoc=trim($io_funciones_cxp->uf_obtenervalor("numrecdoc","")); 
		$ls_codtipdoc=trim($io_funciones_cxp->uf_obtenervalor("codtipdoc","")); 
		$ls_tipproben=$io_funciones_cxp->uf_obtenervalor("tipproben",""); 
		$ls_codproben=trim($io_funciones_cxp->uf_obtenervalor("codproben","")); 
		$ls_tiponota=$io_funciones_cxp->uf_obtenervalor("tiponota",""); 
		$ls_ctaprov=trim($io_funciones_cxp->uf_obtenervalor("ctaprov","")); 
		$li=0;
		$ls_aux="";
		$ls_codpro="";
		$ls_cedbene="";
		if($ls_tipproben=='P')
		{
			$ls_destino="Proveedor";
			$ls_cedbene="";
			$ls_codpro=$ls_codproben;
			$ls_aux=" AND rd.cod_pro='".$ls_codproben."' ";
		}
		elseif($ls_tipproben=='B')
		{
			$ls_destino="Beneficiario";
			$ls_codpro="";
			$ls_cedbene=$ls_codproben;
			$ls_aux=" AND rd.ced_bene='".$ls_codproben."' ";
		}
				
		$ls_concat1 = $io_conexion->Concat('rd.codtipdoc','rd.numrecdoc','rd.ced_bene','rd.cod_pro','rd.numdoccom','rd.sc_cuenta');
		$ls_concat2 = $io_conexion->Concat('x.codtipdoc','x.numrecdoc','x.ced_bene','x.cod_pro','x.numdoccom','x.sc_cuenta');
		$ls_sql=" SELECT rd.codemp, rd.numrecdoc, rd.codtipdoc, rd.ced_bene, rd.cod_pro, rd.procede_doc, rd.numdoccom,
						 rd.sc_cuenta, rd.monto,scg.denominacion,rd.debhab ".
				" FROM cxp_rd_scg rd,scg_cuentas scg ".
				" WHERE  rd.codemp='".$ls_codemp."' ".
				" AND    rd.numrecdoc='".$ls_numrecdoc."' AND rd.codtipdoc='".$ls_codtipdoc."'  ".$ls_aux. 
				" AND rd.codemp=scg.codemp AND rd.sc_cuenta=scg.sc_cuenta ".
				" AND ".$ls_concat1." NOT IN (SELECT ".$ls_concat2." FROM cxp_rd_deducciones x)".
				" ORDER BY rd.sc_cuenta ASC,rd.debhab ASC" ;
//print $ls_sql;
		$rs_data=$io_sql->select($ls_sql);

		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar detalles contables ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)."  SQL: ".$ls_sql,true,"javascript: ue_close();"); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_scgcuenta=$row["sc_cuenta"];
				$ldec_monto=$row["monto"];
				$ls_dencuenta=$row["denominacion"];
				$ls_debhab=$row["debhab"];
				$ldec_montoant=uf_verificar_contable($ls_numrecdoc,$ls_codtipdoc,$ls_tipproben,$ls_codproben,$ls_scgcuenta,$ls_debhab);
				if($ls_tiponota=='NC')
				{
					$ldec_disponible=$ldec_monto-$ldec_montoant;
				}
				else
				{
					$ldec_disponible=$ldec_monto+$ldec_montoant;
				}
				if($ldec_disponible>0)
				{
					$li++;		
					if($ls_debhab=='D')
					{
						$ldec_mondeb=number_format($ldec_disponible,2,",",".");
						$ldec_monhab="0,00";
						$lb_enabledeb="";
						$lb_enablehab="readonly";
					}
					else
					{
						$ldec_monhab=number_format($ldec_disponible,2,",",".");
						$ldec_mondeb="0,00";
						$lb_enabledeb="readonly";
						$lb_enablehab="";
					}
					$lo_object[$li][1]="<input type=checkbox name=chkcont".$li."     id=chkcont".$li." class=sin-borde ><input type=hidden name=txtdebhab".$li." value='".$ls_debhab."'>";
					$lo_object[$li][2]="<input type=text name=txtscgcuenta".$li."    class=sin-borde style=text-align:center size=22 value='".$ls_scgcuenta."'    readonly>";
					$lo_object[$li][3]="<input type=text name=txtdencuenta".$li."    class=sin-borde style=text-align:left   size=54  value='".$ls_dencuenta."'     readonly>"; 
					$lo_object[$li][4]="<input type=text name=txtmondeb".$li."       class=sin-borde style=text-align:right  size=20 value='".$ldec_mondeb."' onBlur='javascript:uf_format(this);uf_valida_monto($li,'D')'  onKeyPress=return(ue_formatonumero(this,'.',',',event)); ".$lb_enabledeb."><input type=hidden name=txtmontooriginaldeb".$li." value='".$ldec_disponible."'>";
					$lo_object[$li][5]="<input type=text name=txtmonhab".$li."       class=sin-borde style=text-align:right  size=20 value='".$ldec_monhab."' onBlur='javascript:uf_format(this);uf_valida_monto($li,'H')'  onKeyPress=return(ue_formatonumero(this,'.',',',event)); ".$lb_enablehab."><input type=hidden name=txtmontooriginalhab".$li." value='".$ldec_disponible."'>";
				}
			}
			if($li==0)
			{
				$io_mensajes->uf_mensajes_ajax("Informacion","No hay datos para mostrar",true,"javascript: ue_close();"); 	
				$lo_object=array();			
			}
			// Titulos del Grid de Bienes
			$lo_title[1]=" ";
			$lo_title[2]="Cuenta Contable";
			$lo_title[3]="Denominaci&oacute;n";
			$lo_title[4]="Debe";
			$lo_title[5]="Haber";
			print "<input name=totalrows type=hidden id=totalrows value=$li>";
			$io_grid->makegrid($li,$lo_title,$lo_object,758,"Registro de Detalle Contable","grid");
			$io_sql->free_result($rs_data);	
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_dtcontable
	//--------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_notas()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funcion que obtiene e imprime los resultados de la busqueda de las notas de debito o credito
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin:  28/05/2007 								Fecha ltima Modificacin : 03/06/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_numncnd  = "%".$io_funciones_cxp->uf_obtenervalor("numncnd","")."%";
		$ls_tipo     = $io_funciones_cxp->uf_obtenervalor("tipo","");
		$ls_codproben= $io_funciones_cxp->uf_obtenervalor("codproben","");
		$ls_dennota  = "%".$io_funciones_cxp->uf_obtenervalor("dennota","")."%";
		$ld_fecdesde = $io_funciones->uf_convertirdatetobd($io_funciones_cxp->uf_obtenervalor("fecdesde",""));
		$ld_fechasta = $io_funciones->uf_convertirdatetobd($io_funciones_cxp->uf_obtenervalor("fechasta",""));
		$ls_orden    = $io_funciones_cxp->uf_obtenervalor("orden","");
		$ls_campoorden=$io_funciones_cxp->uf_obtenervalor("campoorden","");
		$li=0;
		$ls_aux="";
		$ls_codpro="";
		$ls_cedbene="";
		if($ls_tipo=='P')
		{
			$ls_destino="Proveedor";
			$ls_cedbene="";
			$ls_codpro=$ls_codproben;
			$ls_aux=" AND cxp.cod_pro='".$ls_codproben."' ";			
			$ls_aux_nomben=" prov.nompro as nomproben,prov.sc_cuenta as sc_cuenta";
		}
		elseif($ls_tipo=='B')
		{
			$ls_destino="Beneficiario";
			$ls_codpro="";
			$ls_cedbene=$ls_codproben;
			$ls_aux=" AND cxp.ced_bene='".$ls_codproben."' ";
			$ls_cadena1 = $io_conexion->Concat('ben.nombene',"'  '",'ben.nombene');
			$ls_aux_nomben = " $ls_cadena1  as nomproben,ben.sc_cuenta as sc_cuenta ";
		}
		else
		{
			$ls_cadena1 = $io_conexion->Concat('ben.nombene',"'  '",'ben.nombene');
			$ls_aux_nomben=" (CASE cxp.cod_pro WHEN '----------' THEN ".$ls_cadena1." ELSE prov.nompro END) as nomproben, (CASE cxp.cod_pro WHEN '----------' THEN ben.sc_cuenta ELSE prov.sc_cuenta END) as sc_cuenta ";
		}

		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest1 = '';
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('cxp_dc_spg.codestpro','cxp_dc_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_dc_spg.codemp = cxp_rd.codemp ".
							" AND cxp_dc_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_dc_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_dc_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_dc_spg.cod_pro = cxp_rd.cod_pro ".
							" AND cxp_rd.codemp = cxp.codemp ".
							" AND cxp_rd.numrecdoc = cxp.numrecdoc ".
							" AND cxp_rd.codtipdoc = cxp.codtipdoc ".
							" AND cxp_rd.ced_bene = cxp.ced_bene ".
							" AND cxp_rd.cod_pro = cxp.cod_pro ";
			$ls_filtrofrom = " ,cxp_dc_spg, cxp_rd ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER

		$ls_sql=" SELECT trim(cxp.numdc) as numdc, cxp.numsol,trim(cxp.numrecdoc) as numrecdoc, cxp.fecope, cxp.cod_pro, cxp.estlibcom, 
		                 trim(cxp.ced_bene) as ced_bene, cxp.desope,cxp.fecemi, 
		                 cxp.codope, cxp.monto, cxp.codtipdoc, doc.dentipdoc, doc.estcon, doc.estpre, cxp.estapr, cxp.estnotadc,
					     (CASE cxp.cod_pro WHEN '----------' THEN ben.rifben ELSE prov.rifpro END) as rifproben, 
						 $ls_aux_nomben ,scg.denominacion as den_scg
				    FROM cxp_sol_dc cxp,cxp_documento doc,rpc_proveedor prov,rpc_beneficiario ben,scg_cuentas scg {$ls_filtrofrom}
				   WHERE cxp.codemp='".$ls_codemp."' 
				     AND cxp.numdc like '".$ls_numncnd."' 
					 AND cxp.desope like '".$ls_dennota."' 
				     AND cxp.fecope BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' ".$ls_aux." {$ls_filtroest}  
				     AND cxp.codemp=prov.codemp 
					 AND cxp.codemp=ben.codemp 
					 AND cxp.codtipdoc=doc.codtipdoc 
					 AND cxp.cod_pro=prov.cod_pro 
					 AND cxp.ced_bene=ben.ced_bene
					 AND cxp.codemp=scg.codemp 
				     AND (CASE cxp.cod_pro WHEN '----------' THEN ben.sc_cuenta ELSE prov.sc_cuenta END) = scg.sc_cuenta
				   ORDER BY ".$ls_campoorden." ".$ls_orden." ";
		
		$rs_data=$io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error en catalogo de Notas de Debito o Credito","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)."  SQL: ".$ls_sql,true,"javascript: ue_close();"); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td style='cursor:pointer' title='Ordenar por Numero de Nota'      align='center' onClick=ue_orden('cxp.numdc')>Numero de Nota</td>";
			print "<td style='cursor:pointer' title='Ordenar por Numero de Orden'     align='center' onClick=ue_orden('cxp.numsol')>Numero de Orden de Pago</td>";
			print "<td style='cursor:pointer' title='Ordenar por Numero de Recepcion' align='center' onClick=ue_orden('cxp.numrecdoc')>Numero de Recepcion</td>";
			print "<td style='cursor:pointer' title='Ordenar por Fecha'               align='center' onClick=ue_orden('cxp.fecope')>Fecha</td>";
			print "<td style='cursor:pointer' title='Ordenar por Proveedor'           align='center' onClick=ue_orden('cxp.cod_pro')>Proveedor</td>";
			print "<td style='cursor:pointer' title='Ordenar por Beneficiario'        align='center' onClick=ue_orden('cxp.ced_bene')>Beneficario</td>";
			print "<td style='cursor:pointer' title='Ordenar por Nombre Proveedor/Beneficiario'        align='center' onClick=ue_orden('nomproben')>Nombre Proveedor / Beneficiario</td>";
			print "<td style='cursor:pointer' title='Tipo de Nota'                    align='center' onClick=ue_orden('cxp.codope')>Tipo de Nota</td>";
			print "<td style='cursor:pointer' title='Ordenar por Monto'               align='center' onClick=ue_orden('cxp.monto')>Monto</td>";
			print "<td style='cursor:pointer' title='Ordenar por Descripcion'         align='center' onClick=ue_orden('cxp.desope')>Descripcion</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li++;
				$ls_numncnd		  = $row["numdc"];
				$ls_numord		  = $row["numsol"];
				$ls_numrecdoc	  = $row["numrecdoc"];
				$ld_fecha 		  = $io_funciones->uf_convertirfecmostrar($row["fecope"]);
				$ld_fecemi 		  = $io_funciones->uf_convertirfecmostrar($row["fecemi"]);
				$ls_codpro		  = $row["cod_pro"];
				$ls_cedbene		  = $row["ced_bene"];
				$ls_desope		  = $row["desope"];
				$ls_nomproben	  = $row["nomproben"];
				$ls_cuentaprov 	  = $row["sc_cuenta"];
				$ls_dencuentaprov = $row["den_scg"];
				$ls_codope		  = $row["codope"];
				if($ls_codope=='NC')
				{
					$ls_operacion="Nota de Credito"	;
				}
				else
				{
					$ls_operacion="Nota de Debito"	;				
				}
				if($ls_codpro=='----------')
				{
					$ls_tipproben='B';
				}
				else
				{
					$ls_tipproben='P';
				}
				$ldec_monto=$row["monto"];
				$ls_codtipdoc=$row["codtipdoc"];
				$ls_dentipdoc=$row["dentipdoc"];
				$li_estcon   =$row["estcon"];
				$li_estpre   =$row["estpre"];
				$li_estapro  =$row["estapr"];
				$ls_estnota  =$row["estnotadc"];
				$ls_rifproben = $row["rifproben"];
				$ls_estlibcom = $row["estlibcom"];
				echo "<tr class=celdas-blancas>";
				switch($ls_tipo)
				{
					case "":
						echo "<td align='center'><a href=\"javascript: aceptar('$ls_codemp','$ls_numncnd','$ld_fecha','$ls_numord','$ls_numrecdoc','$ls_codtipdoc',".
							 "'$ls_dentipdoc','$ls_tipproben','$ls_codpro','$ls_cedbene','$ls_nomproben','$li_estcon','$li_estpre','$ls_codope','$ls_cuentaprov',".
							 "'$ls_dencuentaprov','$ls_desope','$ls_estnota','$li_estapro','$ls_rifproben','$ld_fecemi','$ls_estlibcom');\">".$ls_numncnd."</a></td>";
					break;						
					case "REPDES":
						echo "<td align='center'><a href=\"javascript: aceptarrepdes('$ls_numncnd');\">".$ls_numncnd."</a></td>";
					break;						
					case "REPHAS":
						echo "<td align='center'><a href=\"javascript: aceptarrephas('$ls_numncnd');\">".$ls_numncnd."</a></td>";
					break;
				}
				echo "<td align='center'>".$ls_numord."</td>";
				echo "<td align='center'>".$ls_numrecdoc."</td>";
				echo "<td align='center'>".$ld_fecha."</td>";
				echo "<td align='center'>".$ls_codpro."</td>";
				echo "<td align='center'>".$ls_cedbene."</td>";
				echo "<td align='center'>".$ls_nomproben."</td>";
				echo "<td align='center'>".$ls_operacion."</td>";
				echo "<td align='right'>".number_format($ldec_monto,2,",",".")."</td>";	
				echo "<td align='center'>".$ls_desope."</td>";	
				echo "</tr>";
			}
			if($li==0)
			{
				$io_mensajes->uf_mensajes_ajax("Informacion","No hay datos para mostrar",true,"javascript: ue_close();"); 				
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_notas
	
	function uf_print_dtcargos()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_dtcargos
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funcin que obtiene e imprime los resultados de la busqueda del detalle de los cargos de la recepcion
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin:  02/06/2007 								Fecha ltima Modificacin : 03/06/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();	
		require_once("../../shared/class_folder/grid_param.php");
		$io_grid=new grid_param();		
		require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
		$io_ds_cargos=new class_datastore(); // Datastored de cuentas contables
		require_once("sigesp_cxp_c_ncnd.php");
		$io_ncnd = new sigesp_cxp_c_ncnd('../../');
		$io_ds_cargos->data="";
        $ls_codemp     = $_SESSION['la_empresa']['codemp'];
		$ls_modalidad  = $_SESSION['la_empresa']['estmodest'];
		$ls_numncnd    = $io_funciones_cxp->uf_obtenervalor("numnot","");
		$ls_numrecdoc  = $io_funciones_cxp->uf_obtenervalor("numrecdoc","");
		$ls_numrecdoc  = $io_funciones_cxp->uf_obtenervalor("numrecdoc",""); 
		$ls_codtipdoc  = $io_funciones_cxp->uf_obtenervalor("codtipdoc",""); 
		$ls_tipproben  = $io_funciones_cxp->uf_obtenervalor("tipproben",""); 
		$ls_codproben  = $io_funciones_cxp->uf_obtenervalor("codproben",""); 
		$ls_tiponota   = $io_funciones_cxp->uf_obtenervalor("tiponota",""); 
		$ls_numordpag  = $io_funciones_cxp->uf_obtenervalor("numord","");
		$ldec_montodoc = $io_funciones_cxp->uf_obtenervalor("montodoc","0,00");
		$li=0;
		$ls_aux="";
		$ls_codpro="";
		$ls_cedbene="";
		if($ls_tipproben=="P")
		{
			$ls_destino="Proveedor";
			$ls_cedbene="";
			$ls_codpro=$ls_codproben;
			$ls_aux=" AND rd.cod_pro='".$ls_codproben."' ";			
		}
		elseif($ls_tipproben=="B")

		{
			$ls_destino="Beneficiario";
			$ls_codpro="";
			$ls_cedbene=$ls_codproben;
			$ls_aux=" AND rd.ced_bene='".$ls_codproben."' ";			
		}
		$ls_aux_estpro=" rd.codestpro1,rd.codestpro2,rd.codestpro3,rd.codestpro4,rd.codestpro5,rd.estcla";			
		$ls_confiva=$_SESSION["la_empresa"]["confiva"];
		$ls_estafenc=$_SESSION["la_empresa"]["estafenc"];
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		if(($ls_estafenc!="1")||($ls_tiponota=="ND"))
		{
			if ($ls_confiva=="C")
			   {
				 $ls_sql="SELECT rd.codemp, rd.numrecdoc, rd.codtipdoc, rd.ced_bene, rd.cod_pro, rd.procede_doc, rd.numdoccom,
								 rd.spg_cuenta, 
								 '-------------------------' as codestpro1, 
								 '-------------------------' as codestpro2, 
								 '-------------------------' as codestpro3,
								 '-------------------------' as codestpro4,
								 '-------------------------' as codestpro5,
								 '-' as estcla,
								 '--' as codfuefin,
								 scg.denominacion as denscg,rd.formula ,rd.monret,CAR.dencar,CAR.codcar,CAR.porcar
							FROM cxp_rd_cargos rd,scg_cuentas scg ,sigesp_cargos CAR
						   WHERE rd.codemp='".$ls_codemp."'
							 AND rd.numrecdoc='".$ls_numrecdoc."' 
							 AND rd.codtipdoc='".$ls_codtipdoc."' $ls_aux
							 AND rd.codemp=scg.codemp
							 AND rd.spg_cuenta=scg.sc_cuenta
							 AND rd.codemp=scg.codemp
							 AND rd.codcar=CAR.codcar
							 AND rd.codemp=CAR.codemp";
			   }
			elseif($ls_confiva=="P")
			   {
				 $ls_sql=" SELECT rd.codemp, rd.numrecdoc, rd.codtipdoc, rd.ced_bene, rd.cod_pro, rd.procede_doc, rd.numdoccom, $ls_aux_estpro,
								  rd.spg_cuenta, spg.denominacion ,spg.sc_cuenta,scg.denominacion as denscg,rd.formula ,
								  rd.monret,CAR.dencar,CAR.codcar,CAR.porcar,rd.codfuefin
							 FROM cxp_rd_cargos rd,spg_cuentas spg,scg_cuentas scg ,sigesp_cargos CAR
							WHERE rd.codemp='".$ls_codemp."'
							  AND rd.numrecdoc='".$ls_numrecdoc."' 
							  AND rd.codtipdoc='".$ls_codtipdoc."' $ls_aux 
							  AND rd.codemp=spg.codemp 
							  AND rd.spg_cuenta=spg.spg_cuenta 
							  AND rd.codemp=scg.codemp
							  AND spg.sc_cuenta=scg.sc_cuenta 
							  AND rd.codestpro1=spg.codestpro1 
							  AND rd.codestpro2=spg.codestpro2
							  AND rd.codestpro3=spg.codestpro3 
							  AND rd.codestpro4=spg.codestpro4 
							  AND rd.codestpro5=spg.codestpro5
							  AND rd.estcla=spg.estcla
							  AND rd.codcar=CAR.codcar 
							  AND rd.codemp=CAR.codemp";		   
			   }
		}
		else
		{
			if($ls_estpreing==1)
			{
				$ls_sql="SELECT rd.codemp, rd.numrecdoc, rd.codtipdoc, rd.ced_bene, rd.cod_pro, rd.procede_doc, rd.numdoccom, rd.codestpro1,rd.codestpro2,
								rd.codestpro3,rd.codestpro4,rd.codestpro5,rd.estcla, CAR.spi_cuenta as spg_cuenta, spi.denominacion ,spi.sc_cuenta,scg.denominacion as denscg,CAR.formula,
								rd.monret,CAR.dencar, CAR.codcar,CAR.porcar ,rd.codfuefin
						   FROM cxp_rd_cargos rd,spi_cuentas spi,scg_cuentas scg ,sigesp_cargos CAR, spi_cuentas_estructuras 
						  WHERE rd.codemp='".$ls_codemp."'
						   AND rd.numrecdoc='".$ls_numrecdoc."' 
						   AND rd.codtipdoc='".$ls_codtipdoc."' $ls_aux 
						   AND rd.codemp=spi.codemp
						   AND rd.codestpro1=spi_cuentas_estructuras.codestpro1 
						   AND rd.codestpro2=spi_cuentas_estructuras.codestpro2
						   AND rd.codestpro3=spi_cuentas_estructuras.codestpro3 
						   AND rd.codestpro4=spi_cuentas_estructuras.codestpro4 
						   AND rd.codestpro5=spi_cuentas_estructuras.codestpro5
						   AND rd.estcla=CAR.estclaspi 
						   AND spi.codemp=scg.codemp 
						   AND spi.sc_cuenta=scg.sc_cuenta
						   AND spi.codemp=spi_cuentas_estructuras.codemp 
						   AND spi.spi_cuenta=spi_cuentas_estructuras.spi_cuenta
						   AND spi.codemp=CAR.codemp
						   AND spi.spi_cuenta=CAR.spi_cuenta";		   
			}
			else
			{
				$ls_sql="SELECT CAR.spi_cuenta as spg_cuenta, spi.denominacion ,spi.sc_cuenta,scg.denominacion as denscg,CAR.formula,
								CAR.dencar, CAR.codcar,CAR.porcar, 
								 '-------------------------' as codestpro1, 
								 '-------------------------' as codestpro2, 
								 '-------------------------' as codestpro3,
								 '-------------------------' as codestpro4,
								 '-------------------------' as codestpro5,
								 '-' as estcla,
								 '--' as codfuefin
						   FROM sigesp_cargos CAR , spi_cuentas spi, scg_cuentas scg 
						  WHERE spi.codemp=CAR.codemp
						   AND spi.spi_cuenta=CAR.spi_cuenta
						   AND spi.sc_cuenta=scg.sc_cuenta
						   AND spi.codemp=scg.codemp ";		   
			}
		}
		$rs_data=$io_sql->select($ls_sql);
		if ($rs_data===false)
		   {
		     $io_mensajes->uf_mensajes_ajax("Error al cargar detalles presupuestarios","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)."  SQL: ".$ls_sql,true,"javascript: ue_close();"); 
		   }
		else
		   {   
			$arrResultado=$io_funciones_cxp->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
			$li_len1=$arrResultado["ai_len1"];
			$li_len2=$arrResultado["ai_len2"];
			$li_len3=$arrResultado["ai_len3"];
			$li_len4=$arrResultado["ai_len4"];
			$li_len5=$arrResultado["ai_len5"];
			$ls_titulo=$arrResultado["as_titulo"];
		
			 if (array_key_exists("la_crenotas",$_SESSION))
			    {  
				  if (!empty($_SESSION["la_crenotas"]))
				     {
					   $io_ds_cargos->data = $_SESSION["la_crenotas"];
				     }
		  	    }
			 else
			    { 
 			      $io_ncnd->uf_load_creditos_nota($ls_codemp,$ls_numncnd,$ls_numrecdoc,$ls_codtipdoc,$ls_numordpag,$ls_tiponota,$ls_tipproben,$ls_codproben);
			      if (array_key_exists("la_crenotas",$_SESSION) && !empty($_SESSION["la_crenotas"]))
				     { 
					   $io_ds_cargos->data = $_SESSION["la_crenotas"];
					 }				  
			    }
				// Modificado por Ofimatica de Venezuela el 25-07-2011, para inicializar el array, ya que cuando se trabaja con el firefox 5.0, emite 
				// warnnig sobre los parametros que se envian al class_datastore.
				if(isset($io_ds_cargos->data))
				{
					$io_ds_cargos->data=array();
				}
				// Fin de lo modificado por Ofimatica de Venezuela
			 while($row=$io_sql->fetch_row($rs_data))
			      {
				    $ls_activo="";
					$ls_codestpro1=$row["codestpro1"];
					$ls_codestpro2=$row["codestpro2"];
					$ls_codestpro3=$row["codestpro3"];
					$ls_codestpro4=$row["codestpro4"];
					$ls_codestpro5=$row["codestpro5"];
					$ls_codestpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
				$arrResultado=$io_funciones_cxp->uf_formatoprogramatica($ls_codestpro,$ls_programatica);
				$ls_programatica=$arrResultado["as_programatica"];
					$ls_spgcuenta=$row["spg_cuenta"];
					$ls_codfuefin=$row["codfuefin"];
					$ldec_baseimp=$ldec_montodoc;
					$ldec_montodoc=str_replace(".","",$ldec_montodoc);
					$ldec_montodoc=str_replace(",",".",$ldec_montodoc);
				    if ($ldec_montodoc>0)
					   {
					     $li++;		
						 $ldec_monto="0,00";
						 $ls_codcar=$row["codcar"];
					     $li_porcar=$row["porcar"];
					     $ls_dencuenta=$row["denominacion"];
						 if ($ls_confiva=="C")
						    {
							  $ls_scgcuenta = trim($row["spg_cuenta"]);
							}
						 else
						    {
							  $ls_scgcuenta = trim($row["sc_cuenta"]);
							}
						 $ls_denscg=$row["denscg"];
						 $ls_formula=$row["formula"];
						 $ls_estcla=$row["estcla"];
						 $li_totrowcar = $io_ds_cargos->getRowCount("codcar");
						 if ($li_totrowcar>0)
						    { 
							  $li_row=$io_ds_cargos->findValues(array('codcar'=>$ls_codcar,'spg_cuenta'=>$ls_spgcuenta,'estcla'=>$ls_estcla,'codestpro'=>$ls_codestpro),"codcar");
							  if ($li_row>0)
								 {
								   $ls_activo    = "checked";
								   $ldec_baseimp = $io_ds_cargos->getValue("monobjret",$li_row);
								   $ldec_monto   = $io_ds_cargos->getValue("monret",$li_row);
								 }
						    }
						 $ls_estatus = '-';
						 switch($ls_estcla)
						 {
							case "A":
								$ls_estatus="Acción";
								break;
							case "P":
								$ls_estatus="Proyecto";
								break;
						 } 
						 $lo_object[$li][1]="<input type=checkbox name=chk".$li."      id=chk".$li." class=sin-borde onClick='javascript:ue_calcular($li);' $ls_activo>";
						 $lo_object[$li][2]="<input type=text name=txtcodestpro".$li."  class=sin-borde style=text-align:center size=37 value='".$ls_programatica."'    readonly><input type=hidden name=txtformula".$li."  value='".$ls_formula."'><input type=hidden name=codpro".$li."  size=37 value='".$ls_codestpro."'    readonly>";
						 $lo_object[$li][3]="<input type=text name=txtestclaaux".$li."  class=sin-borde style=text-align:center size=10 value='$ls_estatus'    readonly><input name=txtestcla".$li." type=hidden id=txtestcla".$li." value='".$ls_estcla."'>";
						 $lo_object[$li][4]="<input type=text name=txtspgcuenta".$li."  class=sin-borde style=text-align:center size=16 value='".$ls_spgcuenta."'     readonly><input type=hidden name=txtscgcuenta".$li."  value='".$ls_scgcuenta."'><input type=hidden name=txtdenscgcuenta".$li."  value='".$ls_denscg."'>"; 
						 $lo_object[$li][5]="<input type=text name=txtbaseimp".$li."    class=sin-borde style=text-align:right  size=20 value='".$ldec_baseimp."' onBlur='javascript:uf_format(this,true,$li);uf_valida_monto($li);'  onKeyPress=return(ue_formatonumero(this,'.',',',event));>";
						 $lo_object[$li][6]="<input type=text name=txtmonto".$li."      class=sin-borde style=text-align:right  size=20 value='".$ldec_monto."' readonly>";
						 $lo_object[$li][7]="<input type=text name=txtdencuenta".$li."  class=sin-borde style=text-align:left   size=50 value='".$ls_dencuenta."' readonly><input name=txtcodcar".$li." type=hidden id=txtcodcar".$li." value='".$ls_codcar."' readonly><input name=txtporcar".$li." type=hidden id=txtporcar".$li." class=sin-borde style=text-align:left  size=50 value='".$li_porcar."' readonly>";
						 $lo_object[$li][8]="<input type=text name=txtcodfuefin".$li."  class=sin-borde style=text-align:center   size=15 value='".$ls_codfuefin."' readonly>";
				       }
			      }
			 if ($li==0)
			    {
				  $io_mensajes->uf_mensajes_ajax("Informacion","No hay datos para mostrar",true,"javascript: ue_close();"); 				
				  $lo_object=array();
			    }			
			 //Titulos del Grid de Bienes
			 $lo_title[1]=" ";
			 $lo_title[2]="Codigo Programatico";
			 $lo_title[3]="Estatus";
			 $lo_title[4]="Codigo Estadistico";
			 $lo_title[5]="Base Imponible";
			 $lo_title[6]="Monto";
			 $lo_title[7]="Denominaci&oacute;n";
			 $lo_title[8]="Fuente de Financiamiento";
			 print "<input name=totalrows type=hidden id=totalrows value=$li>";
			 $io_grid->makegrid($li,$lo_title,$lo_object,758,"Catalogo de Cargos","grid");
			 $io_sql->free_result($rs_data);	
		   }
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_dtcargos
	//--------------------------------------

	function uf_calcular_cargo()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_calcular_cargo
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funcin que obtiene e imprime los resultados de la busqueda del detalle de los cargos de la recepcion y calcula en base a los nuevos montos
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacin:  02/06/2007 								Fecha ltima Modificacin : 03/06/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();	
		require_once("../../shared/class_folder/grid_param.php");
		$io_grid=new grid_param();		
        require_once("../../shared/class_folder/evaluate_formula.php");
		$io_formula       = new evaluate_formula();
		$li_total=$io_funciones_cxp->uf_obtenervalor("total",0);
		for($li=1;$li<=$li_total;$li++)
		{
			$lb_chk=$io_funciones_cxp->uf_obtenervalor("chk".$li,0); 
			$ls_codestpro=$io_funciones_cxp->uf_obtenervalor("txtcodestpro".$li,""); 
			$ls_codpro=$io_funciones_cxp->uf_obtenervalor("txtcodpro".$li,""); 
			$ls_estcla=$io_funciones_cxp->uf_obtenervalor("txtestcla".$li,""); 
			$ls_estclaaux=$io_funciones_cxp->uf_obtenervalor("txtestclaaux".$li,""); 
			$ls_formula=$io_funciones_cxp->uf_obtenervalor("txtformula".$li,""); 
			$ls_spgcuenta=$io_funciones_cxp->uf_obtenervalor("txtspgcuenta".$li,""); 
			$ls_scgcuenta=$io_funciones_cxp->uf_obtenervalor("txtscgcuenta".$li,""); 
			$ls_denscg=$io_funciones_cxp->uf_obtenervalor("txtdenscgcuenta".$li,""); 
			$ldec_baseimp=$io_funciones_cxp->uf_obtenervalor("txtbaseimp".$li,""); 
			$ls_dencuenta=$io_funciones_cxp->uf_obtenervalor("txtdencuenta".$li,"");
			$ls_codcar=$io_funciones_cxp->uf_obtenervalor("txtcodcar".$li,"");
			$li_porcar=$io_funciones_cxp->uf_obtenervalor("txtporcar".$li,"");
			$ls_codfuefin=$io_funciones_cxp->uf_obtenervalor("txtcodfuefin".$li,"");
			$ldec_baseaux=str_replace(".","",$ldec_baseimp);
			$ldec_baseaux=str_replace(",",".",$ldec_baseaux); 			
			if($lb_chk==1)
			{				
				if ($ldec_baseaux>0)
				{					
				  $ldec_monto = $io_formula->uf_evaluar_formula($ls_formula,$ldec_baseaux);
				}
				else
				{
				  $ldec_monto = 0;
				}
				$ldec_monto=round($ldec_monto,2);
				$lo_object[$li][1]="<input type=checkbox name=chk".$li."      id=chk".$li." class=sin-borde onClick='javascript:ue_calcular($li);' checked>";
			}
			else
			{
				$lo_object[$li][1]="<input type=checkbox name=chk".$li."      id=chk".$li." class=sin-borde onClick='javascript:ue_calcular($li);' >";
				$ldec_monto = 0;
			}
			$lo_object[$li][2]="<input type=text name=txtcodestpro".$li."    class=sin-borde style=text-align:center size=37 value='".$ls_codestpro."'    readonly><input type=hidden name=txtformula".$li."  value='".$ls_formula."'><input type=hidden name=codpro".$li."  size=37 value='".$ls_codpro."'    readonly>";
			$lo_object[$li][3]="<input type=text name=txtestclaaux".$li."        class=sin-borde style=text-align:center   size=10 value='$ls_estclaaux'    readonly><input name=txtestcla".$li." type=hidden id=txtestcla".$li." value='".$ls_estcla."'>";
			$lo_object[$li][4]="<input type=text name=txtspgcuenta".$li."    class=sin-borde style=text-align:center size=16  value='".$ls_spgcuenta."'     readonly><input type=hidden name=txtscgcuenta".$li."  value='".$ls_scgcuenta."'><input type=hidden name=txtdenscgcuenta".$li."  value='".$ls_denscg."'>"; 
			$lo_object[$li][5]="<input type=text name=txtbaseimp".$li."        class=sin-borde style=text-align:right  size=20 value='".number_format($ldec_baseaux,2,",",".")."' onBlur='javascript:uf_format(this,true,$li);'  onKeyPress=return(ue_formatonumero(this,'.',',',event));>";
			$lo_object[$li][6]="<input type=text name=txtmonto".$li."        class=sin-borde style=text-align:right  size=20 value='".number_format($ldec_monto,2,",",".")."' readonly>";
			$lo_object[$li][7]="<input type=text name=txtdencuenta".$li."    class=sin-borde style=text-align:left  size=50 value='".$ls_dencuenta."' readonly><input name=txtcodcar".$li." type=hidden id=txtcodcar".$li." value='".$ls_codcar."' readonly><input name=txtporcar".$li." type=hidden id=txtporcar".$li." class=sin-borde style=text-align:left  size=50 value='".$li_porcar."' readonly>";
			$lo_object[$li][8]="<input type=text name=txtcodfuefin".$li."    class=sin-borde style=text-align:left  size=15 value='".$ls_codfuefin."' readonly>";
		}
		if($li==0)
		{
			$io_mensajes->uf_mensajes_ajax("Informacion","No hay datos para mostrar",true,"javascript: ue_close();"); 				
		}
			// Titulos del Grid de Bienes
		$lo_title[1]=" ";
		$lo_title[2]="Codigo Programatico";
		$lo_title[3]="Estatus";
		$lo_title[4]="Codigo Estadistico";
		$lo_title[5]="Base Imponible";
		$lo_title[6]="Monto";
		$lo_title[7]="Denominaci&oacute;n";
		$lo_title[8]="Fuente de Financiamiento";
		print "<input name=totalrows type=hidden id=totalrows value=".($li-1).">";
		print "<input name=selected type=hidden id=selected value=0>";
		$io_grid->makegrid(($li-1),$lo_title,$lo_object,758,"Catalogo de Cargos","grid");
		unset($io_mensajes,$io_funciones,$ls_codemp);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_retencioniva()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_retencionesiva
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de retenciones de iva
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 12/07/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];

		$ls_tipproben=$_POST['tipproben'];
		$ld_fecdes=$_POST['fecdes'];
		$ld_fechas=$_POST['fechas'];
		$ls_mes=$_POST['mes'];
		$ls_anio=$_POST['anio'];
		$ls_tipo=$_POST['tipo'];
		$ls_numsol=$_POST['numsol'];
		$ls_codprobendes=$_POST['codprobendes'];
		$ls_codprobenhas=$_POST['codprobenhas'];
		$ld_fecdes=$io_funciones->uf_convertirdatetobd($ld_fecdes);
		$ld_fechas=$io_funciones->uf_convertirdatetobd($ld_fechas);
		$ls_cedbendes="";
		$ls_cedbenhas="";
		$ls_codprodes="";
		$ls_codprohas="";
		$ls_criterio="";
		$ls_criterio2="";
		switch($ls_tipproben)
		{
			case "P":
				$ls_codprodes=$ls_codprobendes;
				$ls_codprohas=$ls_codprobenhas;
			break;

			case "B":
				$ls_cedbendes=$ls_codprobendes;
				$ls_cedbenhas=$ls_codprobenhas;
			break;
		}
		if($ld_fecdes!="")
		{
			$ls_criterio=$ls_criterio."		AND scb_cmp_ret.fecrep >= '".$ld_fecdes."'";
		}
		if($ld_fechas!="")
		{
			$ls_criterio=$ls_criterio."		AND scb_cmp_ret.fecrep <= '".$ld_fechas."'";
		}
		if($ls_codprobendes!="")
		{
			$ls_criterio=$ls_criterio."		AND scb_cmp_ret.codsujret >= '".$ls_codprobendes."'";
		}
		if($ls_codprobenhas!="")
		{
			$ls_criterio=$ls_criterio."		AND scb_cmp_ret.codsujret <= '".$ls_codprobenhas."'";
		}
		$ls_periodofiscal = $ls_anio.$ls_mes;
		$ls_where="";
		if($ls_numsol!="")
		{
			$ls_where=" AND scb_dt_cmp_ret.numsop='".$ls_numsol."'";
		}				
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT DISTINCT scb_cmp_ret.numcom, scb_cmp_ret.fecrep, scb_cmp_ret.perfiscal,scb_cmp_ret.codsujret,".
				"       scb_cmp_ret.nomsujret, scb_cmp_ret.dirsujret, scb_cmp_ret.rif,scb_dt_cmp_ret.codret,scb_cmp_ret.estcmpret ".
				"  FROM scb_cmp_ret, scb_dt_cmp_ret ".$ls_filtrofrom.
				" WHERE scb_cmp_ret.codemp = '".$ls_codemp."' ".
				"   AND scb_cmp_ret.codret = '".$ls_tipo."' ".
				"   AND scb_cmp_ret.perfiscal = '".$ls_periodofiscal."' ".
				$ls_where.$ls_filtroest.
				"	AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp  ".
				"   AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
				"   AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ".
			//	"   AND scb_dt_cmp_ret.numsop = cxp_solicitudes.numsol ".
				$ls_criterio.
				" ORDER BY scb_cmp_ret.numcom ";
		$rs_data=$io_sql->select($ls_sql);
		
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Retenciones IVA ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=600 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td style=text-align:center width=100>Codigo</td>";
			print "<td style=text-align:center width=50>Fecha</td>";
			print "<td style=text-align:center width=450>Nombre</td>";
			print "</tr>";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_numcom=$row["numcom"];
				$ls_perfiscal=$row["perfiscal"];
				$ls_anofiscal=substr($ls_perfiscal,0,4);
				$ls_mesfiscal=substr($ls_perfiscal,4,6);
				$ls_codsujret=$row["codsujret"];
				$ls_nomsujret=$row["nomsujret"];
				$ls_dirsujret=$row["dirsujret"];
				$ls_rifsujret=$row["rif"];
				$ls_codret=$row["codret"];
				$ld_fecrep=$io_funciones->uf_convertirfecmostrar($row["fecrep"]);
				$ls_estcmpret=$row["estcmpret"];
				print "<tr class=celdas-blancas>";
				print "<td style=text-align:center width=100><a href=\"javascript:ue_aceptar('$ls_numcom','$ls_anofiscal','$ls_mesfiscal','$ls_codsujret','$ls_nomsujret','$ls_dirsujret','$ls_rifsujret','$ls_codret','$ls_estcmpret');\">".$ls_numcom."</a></td>";
				print "<td style=text-align:center width=50>".$ld_fecrep."</td>";
				print "<td style=text-align:left   width=450>".$ls_nomsujret."</td>";
				print "</tr>";
				
			}
			$io_sql->free_result($rs_data);
		}
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_retencionesiva
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_chequear_cancelado($as_numsol)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_chequear_cancelado
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que verifica si una solicitud esta cancelada.
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 28/08/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$lb_pagado=false;
        $ls_sql="SELECT * ".
				"  FROM scb_prog_pago  ".
                " WHERE codemp = '".$ls_codemp."' ".
				"   AND numsol = '".$as_numsol."' ";
				
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Proveedores","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			if($row=$io_sql->fetch_row($rs_data))
			{
				$ls_status=$row["estmov"];
				if($ls_status=='N'||$ls_status=='C')
				{
					$lb_pagado=true;
				}
			}
		}
		return $lb_pagado;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_amortizacion()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_amortizacion
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de compromisos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 09/05/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		require_once("sigesp_cxp_c_recepcion.php");
		$io_recepcion=new sigesp_cxp_c_recepcion("../../");		
		require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
		$io_ds_amortizacion=new class_datastore(); // Datastored de cuentas contables
		$ls_codigo=$_POST['codigo'];
		$ls_tipodes=$_POST['tipodes'];
		switch($ls_tipodes)
		{
			case "P":
				$ls_codprov=$ls_codigo;
				$ls_cedbene="----------";
				break;
			case "B":
				$ls_codprov="----------";
				$ls_cedbene=$ls_codigo;
				break;
		}
		if(array_key_exists("amortizacion",$_SESSION))
		{
			$io_ds_amortizacion->data=$_SESSION["amortizacion"];
		}
		$lb_valido=$io_recepcion->uf_select_amortizaciones($ls_codprov,$ls_cedbene);
		if($lb_valido==true)
		{
				$li_totrow=$io_recepcion->io_ds_anticipos->getRowCount('numrecdoc');
				if($li_totrow>0)
				{//print_r($io_ds_amortizacion->data);
					print "<table width=600 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
					print "<tr class=titulo-celda>";
					print "	<td align='center' >Recepcion</td>";
					print "	<td align='rigth' >Concepto</td>";
					print "	<td align='rigth' >Total Anticipo</td>";
					print "	<td align='rigth' >Saldo</td>";
					print "	<td align='rigth' >Amortizacion</td>";
					print "</tr>";
					for($li_i=1;($li_i<=$li_totrow)&&($lb_valido);$li_i++)
					{
						$ls_numrecdoc=trim($io_recepcion->io_ds_anticipos->data["numrecdoc"][$li_i]);
						$ls_codtipdoc=trim($io_recepcion->io_ds_anticipos->data["codtipdoc"][$li_i]);
						$ls_dencondoc=$io_recepcion->io_ds_anticipos->data["dencondoc"][$li_i];
						$li_monsal=$io_recepcion->io_ds_anticipos->data["monsal"][$li_i];
						$li_montotamo=$io_recepcion->io_ds_anticipos->data["montotamo"][$li_i];				  
						$ls_cuenta=$io_recepcion->io_ds_anticipos->data["cuenta"][$li_i];				  
						$ls_codamo=$io_recepcion->io_ds_anticipos->data["codamo"][$li_i];				  
						$li_monamo=0;
						if($lb_valido)
						{
							$li_row=$io_ds_amortizacion->findValues(array('recdocant'=>$ls_numrecdoc,'codtipdoc'=>$ls_codtipdoc),"recdocant");
							if($li_row>0)
							{
								$li_monamo=$io_ds_amortizacion->getValue("monto",$li_row);
							}
							print "<tr class=celdas-blancas>";
							print "	<td  width=140 align=center><input name=txtnumrecdoc".$li_i." type=text class=sin-borde style=text-align:center id=txtnumrecdoc".$li_i."' value=".$ls_numrecdoc." readonly/>".
								  "	<input name=txtcuenta".$li_i." type=hidden id=txtcuenta".$li_i."' value=".$ls_cuenta."  /><input name=txtcodtipdoc".$li_i." type=hidden id=txtcodtipdoc".$li_i."' value=".$ls_codtipdoc."  />".
								  " <input name=txtcodamo".$li_i." type=hidden id=txtcodamo".$li_i."' value=".$ls_codamo."  /></td>";
							print " <td  width=155 align=right><textarea name=txtdencondoc".$li_i." rows=3 class=sin-borde style=text-align:left id=txtdencondoc".$li_i."' readonly>".$ls_dencondoc."</textarea></td>";
							print "	<td  width=100  align=right>".number_format($li_montotamo,2,',','.')."</td>";
							print " <td  width=100 align=right><input name=txtsaldo".$li_i." type=text class=sin-borde style=text-align:right id=txtsaldo".$li_i."' value=".number_format($li_monsal,2,',','.')." size='10' readonly/></td>";
							print " <td  width=100 align=right><input name=txtmonto".$li_i." type=text class=sin-borde style=text-align:right id=txtmonto".$li_i."' value=".number_format($li_monamo,2,',','.')." size='10' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur= ue_validarmonto(); /> </td>";
							print "</tr>";
						}
					}
					print "<tr><td>";
					print "<input name=txttotrow type=hidden class=sin-borde  id=txttotrow value=".$li_totrow." onKeyPress=return(ue_formatonumero(this,'.',',',event)); />";
					print "</td></tr>";
					print "</table>";
				}
				else
				{
        			$io_mensajes->message("ERROR->No hay Anticipos asociados a este Proveedor ó Beneficiario"); 
				}
			}
		unset($io_mensajes);
		unset($io_funciones);
		unset($io_recepcion);
	}// end function uf_print_compromisos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_unidad_ejecutora()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de la unidad ejecutora (Unidad administrativa)
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Modificado Por: Ing. Yozelin Barragan / Ing. Nestor Falcon 
		// Fecha Creación: 17/03/2007 								Fecha Última Modificación : 05/05/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones  = new class_funciones();
						
		$ls_codemp     = $_SESSION["la_empresa"]["codemp"];
		$ls_codunieje  = $_POST["codunieje"];
		$ls_denunieje  = $_POST["denunieje"];
		$ls_orden      = $_POST['orden'];
		$ls_campoorden = $_POST['campoorden'];
		$ls_tipo       = $_POST['tipo'];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60  style='cursor:pointer' title='Ordenar por Código'       align='center' onClick=ue_orden('coduniadm')>C&oacute;digo</td>";
		if (empty($ls_tipo))
		   {
		     print "<td width=400 style='cursor:pointer' title='Ordenar por Denominación' align='center' onClick=ue_orden('denuniadm')>Denominaci&oacute;n</td>";
			 print "<td width=40  style='cursor:pointer' title='Seleccionar Estructura Presupuestaria'>Detalle</td>";   
		   }
		else
		   {
		     print "<td width=440 style='cursor:pointer' title='Ordenar por Denominación' align='center' onClick=ue_orden('denuniadm')>Denominaci&oacute;n</td>";
		   }
		print "</tr>";
		
		$ls_logusr = $_SESSION["la_logusr"];
		$ls_gestor = $_SESSION["ls_gestor"];
		$ls_sql_seguridad = "";
		
		$ls_concatA = $io_conexion->Concat("'{$ls_codemp}'","'CXP'","'{$ls_logusr}'",'spg_unidadadministrativa.coduniadm');
		$ls_concatB = $io_conexion->Concat('codemp','codsis','codusu','codintper');
		$ls_sql_seguridad = " AND {$ls_concatA} IN (SELECT {$ls_concatB}
		                       FROM sss_permisos_internos WHERE codusu = '".$ls_logusr."' AND codsis = 'CXP' AND enabled=1) ";

		$ls_sql="SELECT spg_unidadadministrativa.coduniadm, 
		                count(spg_dt_unidadadministrativa.codestpro1)as items,
                        max(spg_unidadadministrativa.denuniadm) as denuniadm,
						max(spg_dt_unidadadministrativa.codestpro1) as codestpro1, 
						max(spg_dt_unidadadministrativa.codestpro2) as codestpro2,  
						max(spg_dt_unidadadministrativa.codestpro3) as codestpro3,  
						max(spg_dt_unidadadministrativa.codestpro4) as codestpro4,  
						max(spg_dt_unidadadministrativa.codestpro5) as codestpro5, 
						max(spg_dt_unidadadministrativa.estcla) as estcla,max(spg_ep1.estint) as estint,max(spg_ep1.sc_cuenta) as sc_cuenta".
				"  FROM spg_unidadadministrativa, spg_dt_unidadadministrativa, spg_ep1, spg_ep5 ".
				" WHERE spg_unidadadministrativa.codemp='".$ls_codemp."' ".
				"   AND spg_unidadadministrativa.coduniadm <>'----------' ".
				"   AND spg_unidadadministrativa.coduniadm like '%".$ls_codunieje."%' ".
				"   AND spg_unidadadministrativa.denuniadm like '%".$ls_denunieje."%' ".$ls_sql_seguridad.
				"   AND spg_unidadadministrativa.codemp=spg_dt_unidadadministrativa.codemp ".
				"   AND spg_unidadadministrativa.coduniadm=spg_dt_unidadadministrativa.coduniadm ".
				"   AND spg_dt_unidadadministrativa.codemp=spg_ep1.codemp ".
				"   AND spg_dt_unidadadministrativa.estcla=spg_ep1.estcla ".
				"   AND spg_dt_unidadadministrativa.codestpro1=spg_ep1.codestpro1 ".
				"   AND spg_dt_unidadadministrativa.codemp=spg_ep5.codemp ".
				"   AND spg_dt_unidadadministrativa.estcla=spg_ep5.estcla ".
				"   AND spg_dt_unidadadministrativa.codestpro1=spg_ep5.codestpro1 ".
				"   AND spg_dt_unidadadministrativa.codestpro2=spg_ep5.codestpro2 ".
				"   AND spg_dt_unidadadministrativa.codestpro3=spg_ep5.codestpro3 ".
				"   AND spg_dt_unidadadministrativa.codestpro4=spg_ep5.codestpro4 ".
				"   AND spg_dt_unidadadministrativa.codestpro5=spg_ep5.codestpro5 ".
				" GROUP BY spg_unidadadministrativa.codemp, spg_unidadadministrativa.coduniadm".
				" ORDER BY ".$ls_campoorden." ".$ls_orden;
				$rs_data=$io_sql->select($ls_sql);
		if ($rs_data===false)
		   {
		     $io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		   }
		else
		   {
			 $li_fila = 0;
			 while($row=$io_sql->fetch_row($rs_data))
			      {
				    $li_fila++;  
					$li_numitedet  = $row["items"];//Numero de Detalles asociados a la Unidad Ejecutora.
					$ls_codunieje  = str_pad(trim($row["coduniadm"]),10,0,0);
				    $ls_denunieje  = $row["denuniadm"];
				    $ls_estcla     = $row["estcla"];
				    $ls_estint     = $row["estint"];
				    $ls_cuentaint  = trim($row["sc_cuenta"]);
					$ls_codestpro1 = str_pad(trim($row["codestpro1"]),25,0,0);
					$ls_codestpro2 = str_pad(trim($row["codestpro2"]),25,0,0);
				    $ls_codestpro3 = str_pad(trim($row["codestpro3"]),25,0,0);
				    $ls_codestpro4 = str_pad(trim($row["codestpro4"]),25,0,0);
				    $ls_codestpro5 = str_pad(trim($row["codestpro5"]),25,0,0);
					echo "<tr class=celdas-blancas>";
					switch ($ls_tipo)
					{
						case "":
							if ($li_numitedet==1)
							   {
							     echo "<td style=text-align:center width=60><a href=\"javascript: aceptar('$ls_codunieje','$ls_denunieje','$ls_codestpro1','$ls_codestpro2','$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$ls_estint','$ls_cuentaint');\">".$ls_codunieje."</a></td>";
							   }
							elseif($li_numitedet>1)
							   {
							     echo "<td style=text-align:center width=60>".$ls_codunieje."</td>";
							   }
							echo "<td style=text-align:left width=400>".$ls_denunieje."</td>";
							if ($li_numitedet>1)
							   {
							     echo "<td style=text-align:center width=40><a href=javascript:uf_catalogo_estructuras('$ls_codunieje');><img src=../shared/imagebank/mas.gif alt=Detalle width=12 height=24 border=0></td></a>";
							   }
							elseif($li_numitedet<=1)
							   {
							     echo "<td style=text-align:center width=40></td>";
							   }
							break;
						
						case "CONTABLE":
						    echo "<td style=text-align:center width=60><a href=\"javascript: aceptar_contable('$ls_codunieje','$ls_denunieje');\">".$ls_codunieje."</a></td>";
                            echo "<td style=text-align:left width=440>".$ls_denunieje."</td>";
						break;
						
						case "REPDES":
							print "<td style=text-align:center width=60><a href=\"javascript:aceptar_reportedesde('$ls_codunieje');\">".$ls_codunieje."</a></td>";
							print "<td style=text-align:left width=440>".$ls_denunieje."</td>";
						break;
						
						case "REPHAS":
							print "<td style=text-align:center width=60><a href=\"javascript:aceptar_reportehasta('$ls_codunieje');\">".$ls_codunieje."</a></td>";
							print "<td style=text-align:left width=440>".$ls_denunieje."</td>";
						break;
					}
			        print "</tr>";
			}
			$io_sql->free_result($rs_data);
		}
		print "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_unidadejecutora
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_ordenministerio()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_ordenministerio
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de proveedores
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once("../../scb/sigesp_c_cuentas_banco.php");
		$io_ctaban    = new sigesp_c_cuentas_banco();
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_numordpagmin="%".$_POST['numordpagmin']."%";
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		$li_fila=0;
		$ls_sql="SELECT scb_movbco.numordpagmin, scb_movbco.codban, scb_movbco.ctaban, scb_banco.nomban, scb_ctabanco.dencta, 
		                scb_tipofondo.porrepfon, scb_movbco.fecmov, scb_tipocuenta.codtipcta, scb_tipocuenta.nomtipcta, 
						trim(scb_ctabanco.sc_cuenta) as sc_cuenta, scb_movbco.monto, scb_movbco.codtipfon, scb_tipofondo.dentipfon
				   FROM scb_movbco, scb_banco, scb_ctabanco, scb_tipocuenta, scb_tipofondo
				  WHERE scb_movbco.codemp = '".$_SESSION["la_empresa"]["codemp"]."'
				    AND trim(scb_movbco.numordpagmin) <>''
					AND trim(scb_movbco.numordpagmin) <>'-'
					AND (scb_movbco.codope = 'DP' OR scb_movbco.codope = 'NC')		
					AND scb_movbco.codtipfon<>'----'
				    AND scb_movbco.numordpagmin like '%".$ls_numordpagmin."%'
					AND scb_movbco.codemp = scb_banco.codemp
					AND scb_movbco.codban = scb_banco.codban					
					AND scb_movbco.codemp = scb_ctabanco.codemp
					AND scb_movbco.codban = scb_ctabanco.codban
					AND scb_movbco.ctaban = scb_ctabanco.ctaban
					AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta
					AND scb_movbco.codemp=scb_tipofondo.codemp
					AND scb_movbco.codtipfon=scb_tipofondo.codtipfon
				  ORDER BY scb_movbco.numordpagmin, scb_movbco.fecmov ASC";//echo $ls_sql.'<br><br>';
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Ordenes","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			 echo "<table width=760 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			 echo "<tr class=titulo-celda>";
			 echo "<td style='cursor:pointer' title='Ordenar por No. Orden Pago'  style=text-align:center width=100 onClick=ue_orden('scb_movbco.numordpagsig')>No. Orden Pago</td>";
			 echo "<td style='cursor:pointer' title='Ordenar por Banco'           style=text-align:center width=150 onClick=ue_orden('scb_movbco.codban')>Banco</td>";
			 echo "<td style='cursor:pointer' title='Ordenar por Cuenta Bancaria' style=text-align:center width=250 onClick=ue_orden('scb_movbco.ctaban')>Cuenta</td>";
			 echo "<td style='cursor:pointer' style=text-align:center width=50>Monto</td>";
			 echo "<td style='cursor:pointer' style=text-align:center width=50>% Reposici&oacute;n</td>";
			 echo "<td style='cursor:pointer' style=text-align:center width=50>% Consumido</td>";
			 echo "<td style='cursor:pointer' style=text-align:center width=80>Disponible</td>";
			 while (!$rs_data->EOF)
			{
				$li_fila++;
				$ls_codban 	  = $rs_data->fields["codban"];
				$ls_ctaban 	  = $rs_data->fields["ctaban"];
				$ls_scgcta 	  = $rs_data->fields["sc_cuenta"];					
				$ls_nomban   	  = $rs_data->fields["nomban"];
				$ls_fecmov   	  = $rs_data->fields["fecmov"];
				$ls_denctaban 	  = $rs_data->fields["dencta"];
				$ls_codtipcta 	  = $rs_data->fields["codtipcta"];
				$ls_dentipcta	  = $rs_data->fields["nomtipcta"];					 
				$ls_numordpagmin = $rs_data->fields["numordpagmin"];
				$ld_monordpagmin = $rs_data->fields["monto"];//Monto Total de la Orden de Pago Ministerio.
				$ls_codtipfon    = $rs_data->fields["codtipfon"];
				$ls_dentipfon    = $rs_data->fields["dentipfon"];
				$ld_porrepfon    = $rs_data->fields["porrepfon"];//Porcentaje de Reposición.
				$ld_totmoncon = uf_load_monto_consumido($ls_numordpagmin,$ls_codtipfon);//Monto Consumido del Monto Original.
				$ld_totporcon = (($ld_totmoncon*100)/$ld_monordpagmin);//Porcentaje Consumido.
				$ld_totmondis= (($ld_monordpagmin*($ld_porrepfon/100))-$ld_totmoncon);
				$ld_totporcon = number_format($ld_totporcon,2,'.','');
				$ld_monmaxmov = 0;
				$ld_monmaxmov = ($ld_monordpagmin-$ld_totmoncon);//Monto Máximo de la Operación.(Original - Consumido).
				if (($ld_totporcon<$ld_porrepfon)||($ls_tipo=="SOLICITUDDEPAGO"))
				{
					if ($ld_totmondis>0)
					{
						echo "<tr class=celdas-azules>";						   
					}
					else
					{
						echo "<tr class=celdas-blancas>"; 
					}
					echo "<td style=text-align:center width=100><a href=\"javascript:aceptar('$ls_numordpagmin','$ls_codtipfon');\">".$ls_numordpagmin."</a></td>";
					echo "<td style=text-align:left   width=100 title='".$ls_nomban."'>".$ls_codban.' - '.$ls_nomban."</td>";
					echo "<td style=text-align:left   width=300 title='".$ls_denctaban."'>".$ls_ctaban.' - '.$ls_denctaban."</td>";
					echo "<td style=text-align:right  width=80>".number_format($ld_monordpagmin,2,',','.')."</td>";
					echo "<td style=text-align:right  width=50>".number_format($ld_porrepfon,2,',','.')."</td>";
					echo "<td style=text-align:right  width=50>".number_format($ld_totporcon,2,',','.')."</td>";
					echo "<td style=text-align:right  width=80>".number_format($ld_totmondis,2,',','.')."</td>";
					echo "</tr>";						
				}
				$rs_data->MoveNext();
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_proveedor
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_monto_consumido($as_numordpagmin,$as_codtipfon)
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_ordenespago
		//		   Access: private
		//	    Arguments: 
		//	  Description: Método que imprime el resultado de la busqueda de las Ordenes de Pago Ministerio.
		//	   Creado Por: Ing. Néstor Falcón
		// Fecha Creación: 11/02/2009.								Fecha Última Modificación : 11/02/2009.
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		
		$io_include   = new sigesp_include();
		$io_mensajes  = new class_mensajes();		
		$io_funciones = new class_funciones();		
		$io_conexion  = $io_include->uf_conectar();
		$io_sql		  = new class_sql($io_conexion);			
		
		$ld_totmoncon = 0;//Sumatoria de los Consumos de Movimientos asociados a la Orden de Pago Ministerio.
		$ld_moncon = 0;

		switch ($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_aux_where=" AND CONCAT(cxp_rd.codemp,cxp_rd.numrecdoc,cxp_rd.codtipdoc,cxp_rd.cod_pro,cxp_rd.ced_bene) ".
							   "	 NOT IN (SELECT CONCAT(cxp_rd.codemp,cxp_rd.numrecdoc,cxp_rd.codtipdoc,cxp_rd.cod_pro,cxp_rd.ced_bene)".
							   "			   FROM cxp_rd,cxp_dt_solicitudes,cxp_sol_banco".
							   "			  WHERE cxp_rd.codemp=cxp_dt_solicitudes.codemp".
							   "				AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
							   "				AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc".
							   "				AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
							   "				AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene".
							   "				AND cxp_dt_solicitudes.codemp=cxp_sol_banco.codemp".
							   "				AND cxp_dt_solicitudes.numsol=cxp_sol_banco.numsol) ";
				break;
				
			case "MYSQLI":
				$ls_aux_where=" AND CONCAT(cxp_rd.codemp,cxp_rd.numrecdoc,cxp_rd.codtipdoc,cxp_rd.cod_pro,cxp_rd.ced_bene) ".
							   "	 NOT IN (SELECT CONCAT(cxp_rd.codemp,cxp_rd.numrecdoc,cxp_rd.codtipdoc,cxp_rd.cod_pro,cxp_rd.ced_bene)".
							   "			   FROM cxp_rd,cxp_dt_solicitudes,cxp_sol_banco".
							   "			  WHERE cxp_rd.codemp=cxp_dt_solicitudes.codemp".
							   "				AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
							   "				AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc".
							   "				AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
							   "				AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene".
							   "				AND cxp_dt_solicitudes.codemp=cxp_sol_banco.codemp".
							   "				AND cxp_dt_solicitudes.numsol=cxp_sol_banco.numsol) ";
				break;

			case "POSTGRES":
				$ls_aux_where =" AND cxp_rd.codemp||cxp_rd.numrecdoc||cxp_rd.codtipdoc||cxp_rd.cod_pro||cxp_rd.ced_bene".
							   "	 NOT IN (SELECT (cxp_rd.codemp||cxp_rd.numrecdoc||cxp_rd.codtipdoc||cxp_rd.cod_pro||cxp_rd.ced_bene)".
							   "			   FROM cxp_rd,cxp_dt_solicitudes,cxp_sol_banco".
							   "			  WHERE cxp_rd.codemp=cxp_dt_solicitudes.codemp".
							   "				AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
							   "				AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc".
							   "				AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
							   "				AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene".
							   "				AND cxp_dt_solicitudes.codemp=cxp_sol_banco.codemp".
							   "				AND cxp_dt_solicitudes.numsol=cxp_sol_banco.numsol) ";
				break;
		}
		$ls_sql="SELECT SUM(monto) as moncon".
				"	 FROM scb_movbco ".
				"	WHERE numordpagmin<>'-' ".
				"	  AND numordpagmin<>''".
				"	  AND numordpagmin = '".$as_numordpagmin."'".
				"	  AND codtipfon = '".$as_codtipfon."'".
				"	  AND (codope='CH' OR codope='ND')".
				"	GROUP BY numordpagmin,codtipfon".
				"	UNION".
				"  SELECT SUM(montotdoc) as moncon".
				"     FROM cxp_rd".
				"	WHERE numordpagmin = '".$as_numordpagmin."'".
				"	  AND codtipfon='".$as_codtipfon."'".
				"	  $ls_aux_where ".
				"	GROUP BY numordpagmin";
		$rs_data=$io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		if ($rs_data===false)
		   {
		     $io_mensajes->uf_mensajes_ajax("Class->sigesp_scb_c_catalogo_ajax.php;Metodo->uf_load_monto_consumido","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		     print $io_sql->message;
		   }
		else
		   {
			 while(!$rs_data->EOF)
			      {
				    $ld_moncon = $rs_data->fields["moncon"];
				    $ld_totmoncon += $ld_moncon;
				    $rs_data->MoveNext();
				  }
			 $io_sql->free_result($rs_data);
		   }
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	  return $ld_totmoncon;
	}// end function uf_load_monto_consumido
    //-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_confacta()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_confacta
		//		   Access: private
		//	    Arguments: 
		//	  Description: Método que inprime el resultado de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 10/07/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_cxp;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_codigo="%".$_POST['codigo']."%";
		$ls_nombre="%".$_POST['nombre']."%";
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_orden=$_POST['orden'];
		$ls_tipo=$_POST['tipo'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_sql="SELECT codigo, nombre, encabezado, cuerpo, pie, archrtf ".
			    "  FROM cxp_confacta ".
				" WHERE codemp = '".$ls_codemp."'  ".
				"	AND codigo like '".$ls_codigo."' ".
				"   AND nombre like '".$ls_nombre."' ".								
				" ORDER BY ".$ls_campoorden." ".$ls_orden." ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Configuraciones ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width=100 style='cursor:pointer' title='Ordenar por Código'       align='center' onClick=ue_orden('codigo')>Código</td>";
			print "<td width=400 style='cursor:pointer' title='Ordenar por Nombre' align='center' onClick=ue_orden('nombre')>Denominacion</td>";
			print "</tr>";
			$li_i=0;
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li_i++;
				$ls_codigo=trim($row["codigo"]);
				$ls_nombre=$row["nombre"];
				$ls_encabezado=$row["encabezado"];
				$ls_cuerpo=$row["cuerpo"];
				$ls_pie=$row["pie"];
				$ls_archrtf=$row["archrtf"];
				if($ls_tipo=="REPORTE")
				{
					print "<tr class=celdas-blancas>";
					print "<td align='center'><a href=\"javascript: ue_aceptarReporte('".$ls_codigo."','".$ls_nombre."');\">".$ls_codigo."</a></td>";
					print "<td align='left'>".$ls_nombre."</td>";
					print "</tr>";			
				}
				else
				{
					print "<tr class=celdas-blancas>";
					print "<td align='center'><a href=\"javascript: ue_aceptar('".$ls_codigo."','".$ls_nombre."','".$ls_archrtf."','".$li_i."');\">".$ls_codigo."</a></td>";
					print "<td align='left'>".$ls_nombre."<input name='txtencabezado".$li_i."' type='hidden' id='txtencabezado".$li_i."' value='".$ls_encabezado."'><input name='txtcuerpo".$li_i."' type='hidden' id='txtcuerpo".$li_i."' value='".$ls_cuerpo."'><input name='txtpie".$li_i."' type='hidden' id='txtpie".$li_i."' value='".$ls_pie."'></td>";
					print "</tr>";			
				}
				
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_catdeducciones
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_compromisosacta()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_compromisosacta
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de retenciones de aporte social
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 12/07/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid;
		
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];

		$ls_tipproben=$_POST['tipproben'];
		$ld_fecdes=$_POST['fecdes'];
		$ld_fechas=$_POST['fechas'];
		$ls_mes=$_POST['mes'];
		$ls_anio=$_POST['anio'];
		$ls_codprobendes=$_POST['codprobendes'];
		$ls_codprobenhas=$_POST['codprobenhas'];
		$ls_procede=$_POST['procede'];
		$ld_fecdes=$io_funciones->uf_convertirdatetobd($ld_fecdes);
		$ld_fechas=$io_funciones->uf_convertirdatetobd($ld_fechas);
		$ls_cedbendes="";
		$ls_cedbenhas="";
		$ls_codprodes="";
		$ls_codprohas="";
		$ls_criterio="";
		$ls_criterio2="";
		$ls_tabla="";
		switch($ls_tipproben)
		{
			case "P":
				$ls_codprodes=$ls_codprobendes;
				$ls_codprohas=$ls_codprobenhas;
			break;

			case "B":
				$ls_cedbendes=$ls_codprobendes;
				$ls_cedbenhas=$ls_codprobenhas;
			break;
		}
		if($ld_fecdes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.fecemisol >= '".$ld_fecdes."'";
		}
		if($ld_fechas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.fecemisol <= '".$ld_fechas."'";
		}
		if($ls_codprodes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro >= '".$ls_codprodes."'";
		}
		if($ls_codprohas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.cod_pro <= '".$ls_codprohas."'";
		}
		if($ls_cedbendes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene >= '".$ls_cedbendes."'";
		}
		if($ls_cedbenhas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.ced_bene <= '".$ls_cedbenhas."'";
		}
		$ls_periodofiscal = $ls_anio.$ls_mes;
		$ls_where="";
		if($ls_procede!="---")
		{
			$ls_criterio=$ls_criterio." AND cxp_rd.procede='".$ls_procede."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc "; 
			$ls_filtrofrom = " , cxp_dt_solicitudes,cxp_rd_spg ";
		}
		$ls_cadena = $io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');

		$ls_sql="SELECT DISTINCT cxp_rd_spg.numdoccom AS numero, cxp_rd.procede AS procede,  cxp_rd.dencondoc AS concepto, cxp_rd.numrecdoc,".
				"                cxp_rd.cod_pro,cxp_rd.ced_bene,cxp_rd.codtipdoc, ".
				"		(CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT nompro FROM rpc_proveedor ".
				"									   WHERE rpc_proveedor.codemp = cxp_rd.codemp ".
				"										 AND rpc_proveedor.cod_pro = cxp_rd.cod_pro) ".
				"								 ELSE (SELECT ".$ls_cadena." FROM rpc_beneficiario ".
				"									    WHERE rpc_beneficiario.codemp = cxp_rd.codemp ".
				"									 	  AND rpc_beneficiario.ced_bene = cxp_rd.ced_bene) ".
				"		  END) AS nombre ".
				"  FROM cxp_solicitudes, cxp_dt_solicitudes, cxp_rd, cxp_rd_deducciones, sigesp_deducciones,cxp_rd_spg ".
			    " WHERE cxp_solicitudes.codemp = '".$ls_codemp."' ".
				"   AND sigesp_deducciones.retaposol=1 ".
				$ls_criterio.
				"   AND cxp_solicitudes.estprosol<>'A'".
				"   AND cxp_solicitudes.estprosol<>'N'".
				"   AND cxp_solicitudes.estprosol<>'R'".
				"   AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
				"   AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
				"   AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
				"   AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
				"   AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
			    "   AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
			    "   AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ".
				"   AND cxp_solicitudes.cod_pro = cxp_dt_solicitudes.cod_pro ".
				"   AND cxp_solicitudes.ced_bene = cxp_dt_solicitudes.ced_bene ".
				"	AND cxp_dt_solicitudes.codemp = cxp_rd.codemp ".
				"	AND cxp_dt_solicitudes.numrecdoc = cxp_rd.numrecdoc ".
				"	AND cxp_dt_solicitudes.codtipdoc = cxp_rd.codtipdoc ".
				"	AND cxp_dt_solicitudes.cod_pro = cxp_rd.cod_pro ".
				"   AND cxp_dt_solicitudes.ced_bene = cxp_rd.ced_bene ".
				"	AND cxp_rd.codemp = cxp_rd_deducciones.codemp ".
				"	AND cxp_rd.numrecdoc = cxp_rd_deducciones.numrecdoc ".
				"	AND cxp_rd.codtipdoc = cxp_rd_deducciones.codtipdoc ".
				"   AND cxp_rd.cod_pro = cxp_rd_deducciones.cod_pro ".
				"	AND cxp_rd.ced_bene = cxp_rd_deducciones.ced_bene ".
				"	AND cxp_rd_deducciones.codemp = sigesp_deducciones.codemp ".
				"	AND cxp_rd_deducciones.codded = sigesp_deducciones.codded ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Retenciones Aporte Social ","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			$lo_title[1]="<input type=checkbox name=checkall id=checkall value=1 size=10 style=text-align:left  class=sin-borde onclick='javascript:uf_checkall();'>";
			$lo_title[2]="Compromiso"; 
			$lo_title[3]="Concepto"; 
			$lo_title[4]="Proveedor / Beneficiario"; 
			$lo_title[5]="Procedencia"; 
			$li_totrow=0;
			$lo_object[$li_totrow][1]="";
			$lo_object[$li_totrow][2]="";
			$lo_object[$li_totrow][3]="";
			$lo_object[$li_totrow][4]="";
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li_totrow++;
				$ls_numero=$row["numero"];
				$ls_procede=$row["procede"];
				$ls_concepto=$row["concepto"];
				$ls_nombre=$row["nombre"];
				$ls_numrecdoc=$row["numrecdoc"];
				$ls_codtipdoc=$row["codtipdoc"];
				$ls_cod_pro=$row["cod_pro"];
				$ls_ced_bene=$row["ced_bene"];
				$ld_fecrep=$io_funciones->uf_convertirfecmostrar($row["fecrep"]);
				$lo_object[$li_totrow][1]="<input type=checkbox name=checkcmp".$li_totrow."     id=checkcmp".$li_totrow."     value=1                   size=10 style=text-align:left    class=sin-borde>"; 
				$lo_object[$li_totrow][2]="<input type=text     name=txtnumcom".$li_totrow."    id=txtnumcom".$li_totrow."    value='".$ls_numero."'    size=15 style=text-align:center  class=sin-borde readonly>".
										   "<input type=hidden  name=numrecdoc".$li_totrow."    id=numrecdoc".$li_totrow."    value='".$ls_numrecdoc."'    >". 
										   "<input type=hidden  name=codtipdoc".$li_totrow."    id=codtipdoc".$li_totrow."    value='".$ls_codtipdoc."'    >". 
										   "<input type=hidden  name=cod_pro".$li_totrow."      id=cod_pro".$li_totrow."    value='".$ls_cod_pro."'    >". 
										   "<input type=hidden  name=ced_bene".$li_totrow."     id=ced_bene".$li_totrow."    value='".$ls_ced_bene."'    >"; 
				$lo_object[$li_totrow][3]="<input type=text     name=txtfecrep".$li_totrow."    id=txtfecrep".$li_totrow."    value='".$ls_concepto."'    size=65 style=text-align:center  class=sin-borde readonly  title='".$ls_concepto."' bgColor=#FF5500>";
				$lo_object[$li_totrow][4]="<input type=text     name=txtnomsujret".$li_totrow." id=txtnomsujret".$li_totrow." value='".$ls_nombre."' size=25 style=text-align:left    class=sin-borde readonly title='".$ls_nombre."' bgColor=#FF5500>";
				$lo_object[$li_totrow][5]="<input type=text     name=txtprocede".$li_totrow."   id=txtprocede".$li_totrow."   value='".$ls_procede."' size=10 style=text-align:left    class=sin-borde readonly>";
			}
			$io_sql->free_result($rs_data);
			$io_grid->makegrid($li_totrow,$lo_title,$lo_object,550,'Retenciones de Aporte Social','grid');
		}	unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_retencionesaporte
	//-----------------------------------------------------------------------------------------------------------------------------------



?>