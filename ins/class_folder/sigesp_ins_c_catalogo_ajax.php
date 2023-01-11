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
	require_once("../../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	require_once("class_funciones_ins.php");
	$io_funciones_ins=new class_funciones_ins("../../");
	// Tipo del catalogo que se requiere pintar
	$ls_catalogo=$io_funciones_ins->uf_obtenervalor("catalogo","");
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
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_proveedor()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de proveedores
		//	   Creado Por:OFIMATICA DE VENEZUELA C.A. - ING. NELSON BARRAEZ
		// Fecha Creación: 01/06/2011							Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_ins;
		
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
		
		$ls_chk=$_POST['chk'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		$ls_cadena="";
        $ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
		if($ls_conrecdoc)
		{
			$ls_estprord='C';
		}
		else
		{
			$ls_estprord='R';
		}
		switch ($ls_tipo)
		{
			case "SOLICITUDPAGO":
				$ls_cadena=" AND cod_pro IN (SELECT cod_pro FROM cxp_rd WHERE estprodoc='".$ls_estprord."' AND estaprord='1' AND tipproben='P')";
			break;
			case "AEROLINEAS":
				$ls_cadena=" AND aerolipro='1'";
			break;
		}
       
	   if($ls_chk==0)
	   {//esta es la parte original
	    $ls_sql="SELECT cod_pro, nompro, trim(rpc_proveedor.sc_cuenta) AS sc_cuenta, rifpro, tipconpro, dirpro, trim(sc_cuentarecdoc) AS sc_cuentarecdoc ".
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
				" ORDER BY ".$ls_campoorden." ".$ls_orden.""; //hasta aqui la consulta original
		}
		else
		{
			$ls_sql="SELECT cod_pro, nompro, trim(rpc_proveedor.sc_cuenta) AS sc_cuenta, rifpro, tipconpro, dirpro, trim(sc_cuentarecdoc) AS sc_cuentarecdoc ".
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
				" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		}		//hasta aqui el codigo nuevo
			//	print $ls_sql;
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->uf_mensajes_ajax("Error al cargar Proveedores","ERROR->".$io_funciones->uf_convertirmsg($io_sql->message),false,""); 
		}
		else
		{
			print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td style='cursor:pointer' title='Ordenar por Codigo' align='center' onClick=ue_orden('cod_pro')>Codigo</td>";
			print "<td style='cursor:pointer' title='Ordenar por Nombre' align='left' onClick=ue_orden('nompro')>Nombre</td>";
			print "<td style='cursor:pointer' title='Ordenar por RIF' align='center' onClick=ue_orden('rifpro')>Rif</td>";
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
				print "<tr class=celdas-blancas>";
				print "<td><a href=\"javascript:aceptar('$ls_codpro','$ls_nompro','$ls_rifpro','$ls_sccuenta','$ls_tipconpro','$ls_ageviapro');\">".$ls_codpro."</a></td>";
				print "<td>".$ls_nompro."</td>";
				print "<td>".$ls_rifpro."</td>";
				print "</tr>";			
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
		//	   Creado Por:OFIMATICA DE VENEZUELA C.A. - ING. NELSON BARRAEZ
		// Fecha Creación: 01/06/2011							Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_ins;
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
        $ls_chk=$_POST['chk'];//se captura el valor de reposicion de caja chica
		
		$ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
		$ls_cadena="";
		if($ls_conrecdoc)
		{
			$ls_estprord='C';
		}
		else
		{
			$ls_estprord='R';
		}
		switch ($ls_tipo)
		{
			case "SOLICITUDPAGO":
				$ls_cadena=" AND ced_bene IN (SELECT ced_bene FROM cxp_rd WHERE estprodoc='".$ls_estprord."' AND estaprord='1' AND tipproben='B')";
			break;
		}
		if($ls_chk==0)
	    {//esta es la parte original
			$ls_sql="SELECT TRIM(ced_bene) as ced_bene, nombene, apebene, rifben, sc_cuenta, tipconben, dirbene, sc_cuentarecdoc ".
					"  FROM rpc_beneficiario ".
					" WHERE codemp='".$ls_codemp."' ".
					"   AND ced_bene <> '----------' ".
					"   AND ced_bene like '".$ls_cedbene."' ".
					"   AND nombene like '".$ls_nombene."' ".
					"   AND apebene like '".$ls_apebene."' ".
					$ls_cadena.
					" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		}
		else
		{
			$ls_sql="SELECT TRIM(ced_bene) as ced_bene, nombene, apebene, rifben, sc_cuenta, tipconben, dirbene, sc_cuentarecdoc ".
					"  FROM rpc_beneficiario ".
					" WHERE codemp='".$ls_codemp."' ".
					"   AND ced_bene <> '----------' ".
					"   AND ced_bene like '".$ls_cedbene."' ".
					"   AND nombene like '".$ls_nombene."' ".
					"   AND apebene like '".$ls_apebene."' ".
					" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		}//hasta aqui el codigo nuevo
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
				print "<tr class=celdas-blancas>";
				print "<td><a href=\"javascript: aceptar('$ls_cedbene','$ls_nombene','$ls_rifben','$ls_sccuenta','$ls_tipconben');\">".$ls_cedbene."</a></td>";
				print "<td>".$ls_nombene."</td>";
				print "</tr>";						
			}
			$io_sql->free_result($rs_data);
			print "</table>";
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_beneficiario
	//-----------------------------------------------------------------------------------------------------------------------------------


?>