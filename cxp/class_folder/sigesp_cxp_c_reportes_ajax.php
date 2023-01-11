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
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("class_funciones_cxp.php");
	$io_funciones_cxp=new class_funciones_cxp();
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	$io_include=new sigesp_include();
	$io_conexion=$io_include->uf_conectar();
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql=new class_sql($io_conexion);	
	require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_mensajes=new class_mensajes();		
    $ls_codemp=$_SESSION["la_empresa"]["codemp"];
	// proceso a ejecutar
	$ls_proceso=$io_funciones_cxp->uf_obtenervalor("proceso","");
	// fecha(emision) de inicio de busqueda
	$ld_fecemides=$io_funciones_cxp->uf_obtenervalor("fecemides","");
	// fecha(emision) de fin de busqueda
	$ld_fecemihas=$io_funciones_cxp->uf_obtenervalor("fecemihas","");
	switch($ls_proceso)
	{
		case "FORMATO2":
			uf_print_solicitudes($ld_fecemides,$ld_fecemihas);
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_solicitudes($ld_fecemides,$ld_fecemihas)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_solicitudes
		//		   Access: private
		//		 Argument: ad_fecregdes     // Fecha (Registro) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Registro) de fin de la Busqueda
		//	  Description: Método que impirme el grid de las solicitudes de pago a imprimir en el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 16/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_cxp, $io_funciones, $io_sql, $io_mensajes;
		// Titulos del Grid de Solicitudes
		$lo_title[1]="";
		$lo_title[2]="Solicitud";
		$lo_title[3]="Proveedor / Beneficiario";
		$lo_title[4]="Concepto";
		$lo_title[5]="Fecha";
		$lo_title[6]="Monto";
		$ld_fecemides=$io_funciones->uf_convertirdatetobd($ld_fecemides);
		$ld_fecemihas=$io_funciones->uf_convertirdatetobd($ld_fecemihas);
		$rs_datasol=uf_load_solicitudes($ld_fecemides,$ld_fecemihas);
		$li_fila=0;
		while($row=$io_sql->fetch_row($rs_datasol))
		{
			$li_fila=$li_fila + 1;
			$ls_numsol=$row["numsol"];
			$ld_fecemisol=$row["fecemisol"];
			$ld_fecemisol=$io_funciones->uf_formatovalidofecha($ld_fecemisol);
			$ls_proben=utf8_encode($row["nombre"]);
			$ls_consol=utf8_encode($row["consol"]);
			$li_monsol=number_format($row["monsol"],2,',','.');
			$ld_fecemisol=$io_funciones->uf_convertirfecmostrar($ld_fecemisol);
			$lo_object[$li_fila][1]="<input type=checkbox name=chkimprimir".$li_fila.">";
			$lo_object[$li_fila][2]="<input type=text name=txtnumsol".$li_fila." id=txtnumsol".$li_fila." class=sin-borde style=text-align:center size=20 value='".$ls_numsol."' readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtproben".$li_fila."    id=txtproben".$li_fila."    class=sin-borde style=text-align:left   size=35 value='".$ls_proben."'    readonly>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtconsol".$li_fila."    id=txtconsol".$li_fila."    class=sin-borde style=text-align:left   size=27 value='".$ls_consol."'   readonly>";
			$lo_object[$li_fila][5]="<input type=text name=txtfecemisol".$li_fila." id=txtfecemisol".$li_fila." class=sin-borde style=text-align:left   size=13 value='".$ld_fecemisol."' readonly>"; 
			$lo_object[$li_fila][6]="<input type=text name=txtmonsol".$li_fila." id=txtmonsol".$li_fila." class=sin-borde style=text-align:right  size=15 value='".$li_monsol."' readonly>";
		}
		if($li_fila==0)
		{
			$io_mensajes->message("No se encontraron resultados");
			$li_fila=1;
			$lo_object[$li_fila][1]="<input type=checkbox name=chkimprimir value=1 disabled/>";
			$lo_object[$li_fila][2]="<input type=text name=txtnumsol".$li_fila." class=sin-borde style=text-align:center size=20 readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtproben".$li_fila."    class=sin-borde style=text-align:left   size=35 readonly>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtconsol".$li_fila."    class=sin-borde style=text-align:left   size=27 readonly>";
			$lo_object[$li_fila][5]="<input type=text name=txtfecemisol".$li_fila." class=sin-borde style=text-align:left   size=13 readonly>"; 
			$lo_object[$li_fila][6]="<input type=text name=txtmonsol".$li_fila." class=sin-borde style=text-align:right  size=15 readonly>";
		}

		$io_grid->makegrid($li_fila,$lo_title,$lo_object,700,"Solicitudes de Pago","gridsolicitudes");
	}// end function uf_print_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_solicitudes($ad_fecemides,$ad_fecemihas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_solicitudes
		//         Access: public  
		//	    Arguments: ad_fecemides     // Fecha de Emision Desde
		//                 ad_fecemihas     // Fecha de Emision Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las solicitudes de pago en el intervalo indicado
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 16/06/2007									Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_codemp, $io_sql, $io_funciones, $io_conexion;
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($ad_fecemides))
		{
			$ad_fecemides=$io_funciones->uf_convertirdatetobd($ad_fecemides);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol>='".$ad_fecemides."'";
		}
		if(!empty($ad_fecemihas))
		{
			$ad_fecemihas=$io_funciones->uf_convertirdatetobd($ad_fecemihas);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol<='".$ad_fecemihas."'";
		}
		switch ($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="CONCAT(rpc_beneficiario.nombene,' ',rpc_beneficiario.apebene)";
				break;
			case "MYSQLI":
				$ls_cadena="CONCAT(rpc_beneficiario.nombene,' ',rpc_beneficiario.apebene)";
				break;
			case "POSTGRES":
				$ls_cadena="rpc_beneficiario.nombene||' '||rpc_beneficiario.apebene";
				break;
			case "INFORMIX":
				$ls_cadena="rpc_beneficiario.nombene||' '||rpc_beneficiario.apebene";
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
							" AND cxp_rd_spg.codtipdoc =cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT DISTINCT cxp_solicitudes.tipproben,cxp_solicitudes.numsol,cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene, ".
				"        cxp_solicitudes.fecemisol,cxp_solicitudes.estprosol,cxp_solicitudes.monsol,cxp_solicitudes.consol,".
				"       (CASE tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
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
			$this->io_mensajes->message("CLASE->reportes_ajax MÉTODO->uf_load_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			return $rs_data;
		}		
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
	
?>