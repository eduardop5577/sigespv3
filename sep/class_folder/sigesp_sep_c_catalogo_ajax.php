<?php
/***********************************************************************************
* @Clase donde se cargan todos los cat�logos del sistema SEP con la utilizaci�n del AJAX
* @fecha de modificacion: 15/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();
	require_once("class_funciones_sep.php");
	$io_funciones_sep=new class_funciones_sep();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("../../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	$ruta = '../../';
	require_once("../../base/librerias/php/general/sigesp_lib_conexiones.php");
        $io_conexiones=new conexiones();
	$io_conexiones->decodificar_post();
	
	// Tipo del catalogo que se requiere pintar
	$ls_catalogo=$io_funciones_sep->uf_obtenervalor("catalogo",""); 
	$ls_loncodestpro1=$_SESSION["la_empresa"]["loncodestpro1"];
	$li_longestpro1= (25-$ls_loncodestpro1)+1;
	$ls_loncodestpro2=$_SESSION["la_empresa"]["loncodestpro2"];
	$li_longestpro2= (25-$ls_loncodestpro2)+1;
	$ls_loncodestpro3=$_SESSION["la_empresa"]["loncodestpro3"];
	$li_longestpro3= (25-$ls_loncodestpro3)+1;
	$ls_loncodestpro4=$_SESSION["la_empresa"]["loncodestpro4"];
	$li_longestpro4= (25-$ls_loncodestpro4)+1;
	$ls_loncodestpro5=$_SESSION["la_empresa"]["loncodestpro5"];
	$li_longestpro5= (25-$ls_loncodestpro5)+1;
	
	switch($ls_catalogo)
	{
		case "BIENES":
			uf_print_bienes();
			break;
		case "UNIDADEJECUTORA":
			uf_print_unidad_ejecutora();
			break;
		case "FUENTEFINANCIAMIENTO":
			uf_print_fuentefinanciamiento();
			break;
		case "PROVEEDOR":
			uf_print_proveedor();
			break;
		case "BENEFICIARIO":
			uf_print_beneficiario();
			break;
		case "CUENTASSPG":
			uf_print_cuentasspg();
			break;
		case "CUENTASCARGOS":
			uf_print_cuentas_cargos();
			break;
		case "SOLICITUD":
			uf_print_solicitud();
			break;
		case "SERVICIOS":
			uf_print_servicios();
			break;
		case "CONCEPTOS":
			uf_print_conceptos();
			break;
		case "OTROSCREDITOS":
			uf_print_otroscreditos();
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_bienes()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_bienes
		//		   Access: private
		//	    Arguments: 
		//	  Description: M�todo que obtiene e imprime el resultado de la busqueda de los bienes
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 17/03/2007								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_sep;
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp     = $_SESSION['la_empresa']['codemp'];
		$ls_parsindis  = $_SESSION["la_empresa"]["estparsindis"];
		$ls_codart     = "%".$_POST['codart']."%";
		$ls_denart     = "%".$_POST['denart']."%";
		$ls_codtipart  = "%".$_POST['codtipart']."%";
		$ls_codestpro1 = $_POST['codestpro1'];
		$ls_codestp1   = $io_funciones->uf_cerosizquierda($ls_codestpro1,25);
		$ls_codestpro2 = $_POST['codestpro2'];
		$ls_codestp2   = $io_funciones->uf_cerosizquierda($ls_codestpro2,25);
		$ls_codestpro3 = $_POST['codestpro3'];
		$ls_codestp3   = $io_funciones->uf_cerosizquierda($ls_codestpro3,25);
		$ls_codestpro4 = $_POST['codestpro4'];
		$ls_codestp4   = $io_funciones->uf_cerosizquierda($ls_codestpro4,25);
		$ls_codestpro5 = $_POST['codestpro5'];
		$ls_codestp5   = $io_funciones->uf_cerosizquierda($ls_codestpro5,25);
		$ls_orden      = $_POST['orden'];
		$ls_estcla     = $_POST['estcla'];
		$ls_campoorden = $_POST['campoorden'];
		$ls_estartgen  = $_POST['estartgen'];
		$ls_codartpri  = $_POST['codartpri'];
		$ls_tipsol  = $_POST['tipsol'];
		$ls_tipsepbie  = '-';		
		$ls_sqlaux     = "";
		$ls_tableadd = "";
		$ls_sqladd = "";
		if (array_key_exists("tipsepbie",$_POST)) 
		{
			$ls_tipsepbie = $_POST['tipsepbie'];
			if ($ls_tipsepbie=='M')
			{	
				$ls_tableadd = ", siv_tipoarticulo";
				$ls_sqladd = " AND siv_tipoarticulo.tipart='2' AND siv_articulo.codtipart=siv_tipoarticulo.codtipart";
			}
			elseif($ls_tipsepbie=='A')
			{
				$ls_tableadd = ", siv_tipoarticulo";
				$ls_sqladd = " AND siv_tipoarticulo.tipart='1' AND siv_articulo.codtipart=siv_tipoarticulo.codtipart";
			}
		}		
		$ls_sql  = "SELECT soc_gastos FROM sigesp_empresa WHERE codemp = '".$ls_codemp."'";
		$rs_data = $io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$lb_valido = false;
			$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message));
		}
		else
		{
			$la_spgctas =  explode(",",$rs_data->fields["soc_gastos"]);
			if (!empty($la_spgctas))
			{
				$li_totrows = count((array)$la_spgctas);
				for ($li_i=0;$li_i<$li_totrows;$li_i++)
				{
					if ($li_i==0)
					{
						$ls_sqlaux = $ls_sqlaux." AND (siv_articulo.spg_cuenta like '".$la_spgctas[$li_i]."%'";
					}
					else
					{
						$ls_sqlaux = $ls_sqlaux." OR siv_articulo.spg_cuenta like '".$la_spgctas[$li_i]."%'";
					}
					if ($li_i==$li_totrows-1)
					{
						$ls_sqlaux = $ls_sqlaux.")";
					}						   
				}
				$ls_straux = "";
				if ($ls_parsindis==1)
				{
					$ls_straux = ",(SELECT (spg_cuentas.asignado-(spg_cuentas.comprometido+spg_cuentas.precomprometido)+spg_cuentas.aumento-spg_cuentas.disminucion) ".
								 "	  FROM spg_cuentas ".
								 "	 WHERE spg_cuentas.codestpro1 = '".$ls_codestp1."' ".
								 "	   AND spg_cuentas.codestpro2 = '".$ls_codestp2."' ".
								 "	   AND spg_cuentas.codestpro3 = '".$ls_codestp3."' ".
								 "	   AND spg_cuentas.codestpro4 = '".$ls_codestp4."' ".
								 "	   AND spg_cuentas.codestpro5 = '".$ls_codestp5."' ".
								 "	   AND spg_cuentas.estcla = '".$ls_estcla."' ".
								 "	   AND spg_cuentas.codemp=siv_articulo.codemp ".
								 "	   AND spg_cuentas.spg_cuenta = siv_articulo.spg_cuenta) AS disponibilidad ";
				}
				$ls_sql = "SELECT siv_articulo.codart,siv_articulo.denart,siv_articulo.ultcosart,siv_articulo.codunimed,TRIM(siv_articulo.spg_cuenta) AS spg_cuenta, ".
						  "		  siv_unidadmedida.denunimed, siv_unidadmedida.unidad, siv_articulo.estartgen, ".
						  "		  (SELECT COUNT(spg_cuentas.spg_cuenta)  ".
						  " 		 FROM spg_cuentas ".
						  " 		WHERE spg_cuentas.codestpro1 = '".$ls_codestp1."' ".
						  "			  AND spg_cuentas.codestpro2 = '".$ls_codestp2."' ".
						  "			  AND spg_cuentas.codestpro3 = '".$ls_codestp3."' ".
						  "			  AND spg_cuentas.codestpro4 = '".$ls_codestp4."' ".
						  "			  AND spg_cuentas.codestpro5 = '".$ls_codestp5."' ".
						  "			  AND spg_cuentas.estcla = '".$ls_estcla."' ".
						  "			  AND siv_articulo.codemp = spg_cuentas.codemp ".
						  "			  AND siv_articulo.spg_cuenta = spg_cuentas.spg_cuenta) AS existecuenta, ".
						  "		  (SELECT COUNT(siv_cargosarticulo.codart) ".
						  "		 	 FROM sigesp_cargos, siv_cargosarticulo ".
						  "		    WHERE siv_cargosarticulo.codemp = siv_articulo.codemp ".
						  "		  	  AND siv_cargosarticulo.codart = siv_articulo.codart ".
						  "			  AND sigesp_cargos.codemp = siv_cargosarticulo.codemp ".
						  "			  AND sigesp_cargos.codcar = siv_cargosarticulo.codcar) AS totalcargos ".
						  $ls_straux.
						  "  FROM siv_articulo, siv_unidadmedida ".$ls_tableadd.
						  " WHERE siv_articulo.codemp='".$ls_codemp."' ".
						  "   AND siv_articulo.codart like '".$ls_codart."' ".
						  "   AND siv_articulo.denart like '".$ls_denart."' ".
						  "	  AND siv_articulo.codtipart like '".$ls_codtipart."' ".
						  "	  AND siv_articulo.estartgen = '".$ls_estartgen."' ".
						  "	  AND siv_articulo.codartpri = '".$ls_codartpri."' ".
						  $ls_sqlaux.$ls_sqladd.									 
						  "   AND siv_articulo.codunimed = siv_unidadmedida.codunimed  ".
						  " ORDER BY ".$ls_campoorden." ".$ls_orden; 
				$rs_data=$io_sql->select($ls_sql);
				if ($rs_data===false)
				{
					$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
				}
				else
				{
					echo "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
					echo "<tr class=titulo-celda>";
					echo "<td style='cursor:pointer' title='Ordenar por Codigo'       align='center' onClick=ue_orden('siv_articulo.codart')>Codigo</td>";
					echo "<td style='cursor:pointer' title='Ordenar por Denominacion' align='center' onClick=ue_orden('siv_articulo.denart')>Denominacion</td>";
					echo "<td style='cursor:pointer' title='Ordenar por Unidad'       align='center' onClick=ue_orden('siv_unidadmedida.denunimed')>Unidad</td>";
					echo "<td style='cursor:pointer' title='Ordenar por Cuenta'       align='center' onClick=ue_orden('siv_articulo.spg_cuenta')>Cuenta</td>";
					echo "<td></td>";
					echo "</tr>";
					while(!$rs_data->EOF)
					{
						$ls_codart       = $rs_data->fields["codart"];
						$ls_denart       = $rs_data->fields["denart"];
						$li_ultcosart    = number_format($rs_data->fields["ultcosart"],2,",",".");
						$ls_codunimed    = $rs_data->fields["codunimed"];
						$ls_denunimed    = $rs_data->fields["denunimed"];
						$li_unidad       = $rs_data->fields["unidad"];
						$li_totalcargos  = $rs_data->fields["totalcargos"];
						$ls_spg_cuenta   = $rs_data->fields["spg_cuenta"];
						$li_existecuenta = $rs_data->fields["existecuenta"];
						$ls_estartgen   = $rs_data->fields["estartgen"];
						if ($li_existecuenta==0)
						{
							$ls_estilo = "celdas-blancas";
						}
						else
						{
							$ls_estilo = "celdas-azules";
						}
						echo "<tr class=".$ls_estilo.">";
						echo "<td align='center'>".$ls_codart."</td>";
						echo "<td align='left'>".$ls_denart."</td>";
						echo "<td align='left'>".$ls_denunimed."</td>";
						echo "<td align='center'>".$ls_spg_cuenta."</td>";
						echo "<td style='cursor:pointer'>";
						if ($ls_parsindis==0 || $ls_tipsol=='S')
						{
							echo "<a href=\"javascript: ue_aceptar('".$ls_codart."','".$ls_denart."','".$li_unidad."','".$ls_spg_cuenta."','".$li_ultcosart."','".$li_totalcargos."',
								 '".$li_existecuenta."','".$ls_denunimed."');\"><img src='../shared/imagebank/tools20/aprobado.gif' title='Agregar' width='15' height='15' border='0'></a></td>";
						}
						else
						{
							$li_disponibilidad=$rs_data->fields["disponibilidad"];
							if ($li_disponibilidad >0)
							{
								echo "<a href=\"javascript: ue_aceptar('".$ls_codart."','".$ls_denart."','".$li_unidad."','".$ls_spg_cuenta."',".
								     "'".$li_ultcosart."','".$li_totalcargos."','".$li_existecuenta."','".$ls_denunimed."');\"><img src='../shared/imagebank/tools20/aprobado.gif' title='Agregar' width='15' height='15' border='0'></a></td>";
							}
							else
							{
								echo "<a href=\"javascript: ue_mensaje();\"><img src='../shared/imagebank/tools20/aprobado.gif' title='Agregar' width='15' height='15' border='0'></a></td>";
							}
						} 
						echo  "</tr>";
						$rs_data->MoveNext();
					}
					$io_sql->free_result($rs_data);
					echo "</table>";
				}
			}
		}
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp,$ls_parsindis);
	}// end function uf_print_bienes
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_unidadejecutora()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funci�n que obtiene e imprime los resultados de la busqueda de la unidad ejecutora (Unidad administrativa)
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 17/03/2007 								Fecha �ltima Modificaci�n : 26/08/2008
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
		$ls_parsindis=$_SESSION["la_empresa"]["estparsindis"];
		$ls_coduniadm="%".$_POST["coduniadm"]."%";
		$ls_denuniadm="%".$_POST["denuniadm"]."%";
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60  style='cursor:pointer' title='Ordenar por Codigo'       align='center' onClick=ue_orden('coduniadm')>Codigo</td>";
		print "<td width=440 style='cursor:pointer' title='Ordenar por Denominacion' align='center' onClick=ue_orden('denuniadm')>Denominacion</td>";
		print "</tr>";
		
		$ls_logusr = $_SESSION["la_logusr"];
		$ls_gestor = $_SESSION["ls_gestor"];
		$ls_sql_seguridad = "";
		if ((strtoupper($ls_gestor) == "MYSQLT") || (strtoupper($ls_gestor) == "MYSQLI"))
		{
		 $ls_sql_seguridad = " AND CONCAT('".$ls_codemp."','SEP','".$ls_logusr."',coduniadm) IN (SELECT CONCAT(codemp,codsis,codusu,codintper) 
		                       FROM sss_permisos_internos WHERE codusu = '".$ls_logusr."' AND codsis = 'SEP' AND enabled=1)";
		}
		else
		{
		 $ls_sql_seguridad = " AND '".$ls_codemp."'||'SEP'||'".$ls_logusr."'||coduniadm IN (SELECT codemp||codsis||codusu||codintper
		                       FROM sss_permisos_internos WHERE codusu = '".$ls_logusr."' AND codsis = 'SEP' AND enabled=1)";
		}
		
		$ls_sql="SELECT coduniadm, denuniadm ".
				"  FROM spg_unidadadministrativa ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND coduniadm <>'----------' ".
				"   AND coduniadm like '".$ls_coduniadm."' ".
				"   AND denuniadm like '".$ls_denuniadm."' ".$ls_sql_seguridad." ".
				" ORDER BY ".$ls_campoorden." ".$ls_orden.""; 
	               
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_coduniadm=$row["coduniadm"];
				$ls_denuniadm=$row["denuniadm"];
				
				switch ($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: aceptar('$ls_coduniadm','$ls_denuniadm');\">".$ls_coduniadm."</a></td>";
						print "<td align='left'>".$ls_denuniadm."</td>";
						print "</tr>";			
						break;
					case "APROBACION":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_aprobacion('$ls_coduniadm','$ls_denuniadm');\">".$ls_coduniadm."</a></td>";
						print "<td>".$ls_denuniadm."</td>";
						print "</tr>";			
						break;

					case "REPDES":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_reportedesde('$ls_coduniadm');\">".$ls_coduniadm."</a></td>";
						print "<td>".$ls_denuniadm."</td>";
						print "</tr>";			
						break;

					case "REPHAS":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_reportehasta('$ls_coduniadm');\">".$ls_coduniadm."</a></td>";
						print "<td>".$ls_denuniadm."</td>";
						print "</tr>";			
						break;
						
					case "BUSCAR_CAT_SEP":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_catalogo_sep('$ls_coduniadm','$ls_denuniadm');\">".$ls_coduniadm."</a></td>";
						print "<td>".$ls_denuniadm."</td>";
						print "</tr>";			
					break;
				}
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
	function uf_print_fuentefinanciamiento()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funci�n que obtiene e imprime los resultados de la busqueda de fuente de financiamiento
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 17/03/2007 								Fecha �ltima Modificaci�n : 
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
		
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60  style='cursor:pointer' title='Ordenar por Codigo'       align='center' onClick=ue_orden('codfuefin')>Codigo</td>";
		print "<td width=440 style='cursor:pointer' title='Ordenar por Denominacion' align='center' onClick=ue_orden('denfuefin')>Denominacion</td>";
		print "</tr>";
		$ls_sql="SELECT codfuefin, denfuefin ".
				"  FROM sigesp_fuentefinanciamiento ".	
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codfuefin <> '--' ".	
				"   AND codfuefin IN (SELECT codfuefin FROM spg_dt_fuentefinanciamiento ".
				"					   WHERE codemp='".$ls_codemp."' ".
				"					     AND codestpro1 = '".$_POST['codestpro1']."' ".
				"					     AND codestpro2 = '".$_POST['codestpro2']."' ".
				"					     AND codestpro3 = '".$_POST['codestpro3']."' ".
				"					     AND codestpro4 = '".$_POST['codestpro4']."' ".
				"					     AND codestpro5 = '".$_POST['codestpro5']."' ".
				"					     AND estcla = '".$_POST['estcla']."' )".	
				" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
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
				}
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
	}// end function uf_print_fuentefinanciamiento
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_proveedor()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funci�n que obtiene e imprime los resultados de la busqueda de proveedores
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 17/03/2007 								Fecha �ltima Modificaci�n : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_sep;
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
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td style='cursor:pointer' title='Ordenar por Codigo' align='center' onClick=ue_orden('cod_pro')>Codigo</td>";
		print "<td style='cursor:pointer' title='Ordenar por Nombre' align='center' onClick=ue_orden('nompro')>Nombre</td>";
		print "</tr>";
        $ls_sql="SELECT cod_pro,nompro,sc_cuenta,rifpro,tipconpro".
				"  FROM rpc_proveedor  ".
                " WHERE codemp = '".$ls_codemp."' ".
				"   AND cod_pro <> '----------' ".
				"   AND estprov = 0 ".
				"   AND cod_pro like '".$ls_codpro."' ".
				"   AND nompro like '".$ls_nompro."' ".
				"   AND dirpro like '".$ls_dirpro."' ". 
				" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codpro=$row["cod_pro"];
				$ls_nompro=$row["nompro"];
				$ls_sccuenta=$row["sc_cuenta"];
				$ls_rifpro=$row["rifpro"];
				$ls_tipconpro=$row["tipconpro"];
				switch ($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar('$ls_codpro','$ls_nompro','$ls_rifpro','$ls_tipconpro');\">".$ls_codpro."</a></td>";
						print "<td>".$ls_nompro."</td>";
						print "</tr>";
					break;
					case "REPDES":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar_reportedesde('$ls_codpro');\">".$ls_codpro."</a></td>";
						print "<td>".$ls_nompro."</td>";
						print "</tr>";
					break;
					case "REPHAS":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:aceptar_reportehasta('$ls_codpro');\">".$ls_codpro."</a></td>";
						print "<td>".$ls_nompro."</td>";
						print "</tr>";
					break;
				}
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
	}// end function uf_print_proveedor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_beneficiario()
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_beneficiario
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funci�n que obtiene e imprime los resultados de la busqueda de beneficiarios
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 17/03/2007 								Fecha �ltima Modificaci�n : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_sep;
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
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td style='cursor:pointer' title='Ordenar por Cedula' align='center' onClick=ue_orden('ced_bene')>C&eacute;dula</td>";
		print "<td style='cursor:pointer' title='Ordenar por Nombre' align='center' onClick=ue_orden('nombene')>Nombre</td>";
		print "</tr>";
		$ls_sql="SELECT TRIM(ced_bene) as ced_bene, nombene, apebene, rifben ".
				"  FROM rpc_beneficiario ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND ced_bene <> '----------' ".
				"   AND ced_bene like '".$ls_cedbene."' ".
				"   AND nombene like '".$ls_nombene."' ".
				"   AND apebene like '".$ls_apebene."' ".
				" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_cedbene=$row["ced_bene"];
				$ls_nombene=$row["nombene"]." ".$row["apebene"];
				$ls_rifben=$row["rifben"];
				echo "<tr class=celdas-blancas>";
				switch ($ls_tipo)
				{
					case "":
						echo "<td style=text-align:center><a href=\"javascript: aceptar('$ls_cedbene','$ls_nombene','$ls_rifben');\">".$ls_cedbene."</a></td>";
					break;
					case "REPDES":
						echo "<td style=text-align:center><a href=\"javascript: aceptar_reportedesde('$ls_cedbene');\">".$ls_cedbene."</a></td>";
					break;
					case "REPHAS":
						echo "<td style=text-align:center><a href=\"javascript: aceptar_reportehasta('$ls_cedbene');\">".$ls_cedbene."</a></td>";
					break;
					case "CMPRET":
						echo "<td style=text-align:center><a href=\"javascript: aceptar_cmpretencion('$ls_cedbene');\">".$ls_cedbene."</a></td>";
					break;
				}					
				echo "<td style=text-align:left title='".$ls_nombene."'>".$ls_nombene."</td>";
				echo "</tr>";
			}
			$io_sql->free_result($rs_data);
		}
		echo "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
	}// end function uf_print_beneficiario
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cuentasspg()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_cuentasspg
		//		   Access: private
		//	    Arguments: 
		//	  Description: M�todo que inprime el resultado de la busqueda de las cuentas presupuestarias
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 17/03/2007								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_sep;
		global $ls_loncodestpro1,$li_longestpro1,$ls_loncodestpro2,$li_longestpro2,$ls_loncodestpro3,$li_longestpro3;
		global $ls_loncodestpro4,$li_longestpro4,$ls_loncodestpro5,$li_longestpro5;
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$li_estmodest=$_POST["estmodest"]; 
		$ls_spgcuenta=$_POST['spgcuenta']; 
		$ls_dencue="%".$_POST['dencue']."%";
		$ls_codestpro1=$_POST['codestpro1'];
		$ls_codestpro2=$_POST['codestpro2'];
		$ls_codestpro3=$_POST['codestpro3'];
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_codestpro4=$_POST['codestpro4'];
		$ls_codestpro5=$_POST['codestpro5'];
		$ls_estclap=$_POST['estcla'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		$ls_logusr = $_SESSION["la_logusr"];
		$ls_gestor = $_SESSION["ls_gestor"];
		$ls_criterio="";
		if ($li_estmodest=="1")
		{
		    $codespro1=str_pad($ls_codestpro1,25,"0",0);
		    $codespro2=str_pad($ls_codestpro2,25,"0",0);
		    $codespro3=str_pad($ls_codestpro3,25,"0",0);			
            $estcla=$ls_estclap;
			$ls_scg_cuenta=$_POST['scg_cuenta']; 
		    if ((strtoupper($ls_gestor) == "MYSQLT") || (strtoupper($ls_gestor) == "MYSQLI"))
			{
				
			    $ls_criterio = " AND CONCAT(codestpro1,codestpro2,codestpro3,estcla,spg_cuenta) <> ('$codespro1$codespro2$codespro3$estcla$ls_scg_cuenta')";
			}
			if (strtoupper($ls_gestor) == "POSTGRES")
			{
				$ls_criterio = " AND (codestpro1||codestpro2||codestpro3||estcla||spg_cuenta) <> ('$codespro1$codespro2$codespro3$estcla$ls_scg_cuenta')";
			}
		
		}	
		else
		{
			$codespro1=str_pad($ls_codestpro1,25,"0",0);
		    $codespro2=str_pad($ls_codestpro2,25,"0",0);
		    $codespro3=str_pad($ls_codestpro3,25,"0",0);
			$codespro4=str_pad($ls_codestpro4,25,"0",0);
			$codespro5=str_pad($ls_codestpro5,25,"0",0);
			$ls_scg_cuenta=$_POST['scg_cuenta']; 
            $estcla=$ls_estclap;
		    if ((strtoupper($ls_gestor) == "MYSQLT") || (strtoupper($ls_gestor) == "MYSQLI"))
			{
				
				 $ls_criterio = " AND CONCAT(codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,spg_cuenta) 
				                         <> ('$codespro1$codespro2$codespro3$codespro4$codespro5$estcla$ls_scg_cuenta')";
			}
			if (strtoupper($ls_gestor) == "POSTGRES")
			{
				 $ls_criterio = " AND (codestpro1||codestpro2||codestpro3||codestpro4||codestpro5||estcla||spg_cuenta) 
				                   <> ('$codespro1$codespro2$codespro3$codespro4$codespro5$estcla$ls_scg_cuenta')";
			}
		}
		////-----------se refiere a la seguridad----------------------------------------------------------------
		if ((strtoupper($ls_gestor) == "MYSQLT") || (strtoupper($ls_gestor) == "MYSQLI"))
		{
			 $ls_sql_seguridad = " AND CONCAT('".$ls_codemp."','SPG','".$ls_logusr."',codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla) IN ".
								 "     (SELECT CONCAT(codemp,codsis,codusu,codintper)     ".
								 "        FROM sss_permisos_internos                      ".
								 "       WHERE codusu = '".$ls_logusr."'                  ".
								 "         AND codsis = 'SPG'  AND enabled=1)                            ";
		}
		else
		{
			 $ls_sql_seguridad = " AND '".$ls_codemp."'||'SPG'||'".$ls_logusr."'||codestpro1||codestpro2||codestpro3||codestpro4||codestpro5||estcla IN        ".
			                     "      (SELECT codemp||codsis||codusu||codintper          ".
								 "         FROM sss_permisos_internos                      ".
								 "        WHERE codusu = '".$ls_logusr."'                  ".
								 "          AND codsis = 'SPG'  AND enabled=1)                            ";
		}
		//-------------------------------------------------------------------------------------------------- 
		if($ls_campoorden=="codpro")
		{
			$ls_campoorden= "codestpro1,codestpro2,codestpro3,codestpro4,codestpro5";
		}
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		switch($ls_modalidad)
		{
			case "1": // Modalidad por Proyecto
				$ls_titulo="Estructura Presupuestaria ";
				$li_len1=20;
				$li_len2=6;
				$li_len3=3;
				$li_len4=2;
				$li_len5=2;
				break;
				
			case "2": // Modalidad por Presupuesto
				$ls_titulo="Estructura Program�tica ";
				$li_len1=2;
				$li_len2=2;
				$li_len3=2;
				$li_len4=2;
				$li_len5=2;
				break;
		}
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td style='cursor:pointer' title='Ordenar por Programatica' align='center' onClick=ue_orden('codpro')>".$ls_titulo."</td>";
		print "<td style='cursor:pointer' title='Ordenar por Cuenta'       align='center' onClick=ue_orden('spg_cuenta')>Cuenta</td>";
		print "<td style='cursor:pointer' title='Ordenar por Denominacion' align='center' onClick=ue_orden('denominacion')>Denominacion</td>";
		print "<td style='cursor:pointer' title='Ordenar por Disponible'   align='center' onClick=ue_orden('disponible')>Disponible</td>";
		print "<td></td>";
		print "</tr>";
		$ls_cuentas="";
		$ls_tipocuenta="";
		switch($ls_tipo)
		{
			case "B": // si es de bienes
				$ls_sql="SELECT soc_gastos AS cuenta ".
						"  FROM sigesp_empresa ".
						" WHERE codemp = '".$ls_codemp."' ";
				break;
			case "S": // si es de Servicios
				$ls_sql="SELECT soc_servic AS cuenta ".
						"  FROM sigesp_empresa ".
						" WHERE codemp = '".$ls_codemp."' ";
				break;
		}
	/*	if($ls_tipo!="O")
		{
			$rs_data=$io_sql->select($ls_sql);
			
			if($rs_data===false)
			{
				$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
			}
			else
			{
				if($row=$io_sql->fetch_row($rs_data))
				{
					$ls_cuentas=$row["cuenta"];
				}			
				$la_spg_cuenta=explode(",",$ls_cuentas);
				$li_total=count((array)$la_spg_cuenta);
				for($li_i=0;$li_i<$li_total;$li_i++)
				{
					if($la_spg_cuenta[$li_i]!="")
					{		
						if($li_i==0)
						{
							$ls_tipocuenta=$ls_tipocuenta." SUBSTR(TRIM(spg_cuenta),1,3) = '".trim($la_spg_cuenta[$li_i])."' ";
						}
						else
						{
							$ls_tipocuenta=$ls_tipocuenta."    OR SUBSTR(TRIM(spg_cuenta),1,3) = '".trim($la_spg_cuenta[$li_i])."'";
						}
					}
				}															
			}
		}*/
		if($ls_tipocuenta=="")
		{
			$ls_tipocuenta=" spg_cuenta like '%%' ";
		}
		$ls_sql="SELECT TRIM(spg_cuenta) AS spg_cuenta , denominacion, codestpro1,codestpro2, codestpro3,codestpro4,codestpro5,status,estcla, ".
				"       (asignado-(comprometido+precomprometido)+aumento-disminucion) as disponible ".
			    "  FROM spg_cuentas ".
				" WHERE codemp = '".$ls_codemp."'  ".
				"   AND (".$ls_tipocuenta.")".
				"	AND spg_cuenta like '".$ls_spgcuenta."%' ".
				"   AND denominacion like '".$ls_dencue."' ".								
				"   AND status ='C'  ".$ls_criterio. $ls_sql_seguridad.
				" ORDER BY ".$ls_campoorden." ".$ls_orden." ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_spg_cuenta=$row["spg_cuenta"];
				$ls_denominacion=$row["denominacion"];
				$ls_codpro1=$row["codestpro1"];
				$ls_codest1=substr($ls_codpro1,$li_longestpro1-1,$ls_loncodestpro1);
				$ls_codpro2=$row["codestpro2"];
				$ls_codest2=substr($ls_codpro2,$li_longestpro2-1,$ls_loncodestpro2);
				$ls_codpro3=$row["codestpro3"];
				$ls_codest3=substr($ls_codpro3,$li_longestpro3-1,$ls_loncodestpro3);
				$ls_codpro4=$row["codestpro4"];
				$ls_codest4=substr($ls_codpro4,$li_longestpro4-1,$ls_loncodestpro4);
				$ls_codpro5=$row["codestpro5"];
				$ls_codest5=substr($ls_codpro5,$li_longestpro5-1,$ls_loncodestpro5);
				$ls_codestpro=$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5.'00';
				$li_disponible=number_format($row["disponible"],2,",",".");
				$ls_estcla=$row["estcla"];
				if(($ls_codestpro==$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5) && ($ls_estcla==$ls_estclap))
				{
					$ls_estilo = "celdas-azules";
				}
				else
				{
					$ls_estilo = "celdas-blancas";
				}				
				$ls_programatica=$ls_codpro1.$ls_codpro2.$ls_codpro3.$ls_codpro4.$ls_codpro5;
				print "<tr class=".$ls_estilo.">";
				print "<td align='center'>".$ls_codestpro."</td>";
				print "<td align='center'>".$ls_spg_cuenta."</td>";
				print "<td align='left'>".$ls_denominacion."</td>";
				print "<td align='right'>".$li_disponible."</td>";
				print "<td style='cursor:pointer'>";
				print "<a href=\"javascript: ue_aceptar('".$ls_programatica."','".$ls_spg_cuenta."','".$ls_denominacion."','".$ls_programatica."00','".$ls_estcla."');\"><img src='../shared/imagebank/tools20/aprobado.gif' title='Agregar' width='15' height='15' border='0'></a></td>";
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
	}// end function uf_print_cuentasspg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cuentas_cargos()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_cuentas_cargos
		//		   Access: private
		//	    Arguments: 
		//	  Description: M�todo que inprime el resultado de la busqueda de las cuentas presupuestarias de los cargos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 20/03/2007								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_sep;
		global $ls_loncodestpro1,$li_longestpro1,$ls_loncodestpro2,$li_longestpro2,$ls_loncodestpro3,$li_longestpro3;
		global $ls_loncodestpro4,$li_longestpro4,$ls_loncodestpro5,$li_longestpro5;
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$ls_codestpro1=$_POST['codestpro1'];
		$ls_codestpro2=$_POST['codestpro2'];
		$ls_codestpro3=$_POST['codestpro3'];
		$ls_codestpro4=$_POST['codestpro4'];
		$ls_codestpro5=$_POST['codestpro5'];
		$ls_estclacar=$_POST["estcla"];
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		if($ls_campoorden=="codpro")
		{
			$ls_campoorden= "spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, spg_cuentas.codestpro4, spg_cuentas.codestpro5 ,spg_cuentas.estcla";
		}
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		switch($ls_modalidad)
		{
			case "1": // Modalidad por Proyecto
				$ls_titulo="Estructura Presupuestaria ";
				$li_len1=20;
				$li_len2=6;
				$li_len3=3;
				$li_len4=2;
				$li_len5=2;
				break;
				
			case "2": // Modalidad por Presupuesto
				$ls_titulo="Estructura Program�tica ";
				$li_len1=2;
				$li_len2=2;
				$li_len3=2;
				$li_len4=2;
				$li_len5=2;
				break;
		}
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td style='cursor:pointer' title='Ordenar por Programatica' align='center' onClick=ue_orden('spg_cuentas.codpro')>".$ls_titulo."</td>";
		print "<td style='cursor:pointer' title='Ordenar por Cuenta'       align='center' onClick=ue_orden('spg_cuentas.spg_cuenta')>Cuenta</td>";
		print "<td style='cursor:pointer' title='Ordenar por Denominacion' align='center' onClick=ue_orden('spg_cuentas.denominacion')>Denominacion</td>";
		print "<td style='cursor:pointer' title='Ordenar por Disponible'   align='center' onClick=ue_orden('disponible')>Disponible</td>";
		print "<td></td>";
		print "</tr>";
		$ls_sql="SELECT TRIM(spg_cuentas.spg_cuenta) AS spg_cuenta , MAX(spg_cuentas.denominacion) AS denominacion, spg_cuentas.codestpro1, ".
			    "       spg_cuentas.codestpro2, spg_cuentas.codestpro3, spg_cuentas.codestpro4, spg_cuentas.codestpro5, MAX(status) AS status, ".
				"       (MAX(spg_cuentas.asignado)-(MAX(spg_cuentas.comprometido)+MAX(spg_cuentas.precomprometido))+MAX(spg_cuentas.aumento)-MAX(spg_cuentas.disminucion)) as disponible,spg_cuentas.estcla ".
			    "  FROM spg_cuentas, sigesp_cargos ".
				" WHERE spg_cuentas.codemp = '".$ls_codemp."'  ".
				"   AND spg_cuentas.status ='C'  ".
				"	AND spg_cuentas.codemp = sigesp_cargos.codemp ".
				"   AND spg_cuentas.codestpro1 = substr(sigesp_cargos.codestpro,1,25) ".
				"   AND spg_cuentas.codestpro2 = substr(sigesp_cargos.codestpro,26,25) ".
				"   AND spg_cuentas.codestpro3 = substr(sigesp_cargos.codestpro,51,25) ".
				"   AND spg_cuentas.codestpro4 = substr(sigesp_cargos.codestpro,76,25) ".
				"   AND spg_cuentas.codestpro5 = substr(sigesp_cargos.codestpro,101,25) ".
				"   AND spg_cuentas.estcla=sigesp_cargos.estcla".
				"   AND spg_cuentas.spg_cuenta = sigesp_cargos.spg_cuenta ".
				" GROUP BY spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, spg_cuentas.codestpro4, ".
				"       spg_cuentas.codestpro5, spg_cuentas.spg_cuenta ,spg_cuentas.estcla ".
				" ORDER BY ".$ls_campoorden." ".$ls_orden." ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_spg_cuenta=$row["spg_cuenta"];
				$ls_denominacion=$row["denominacion"];
				$ls_codest1=$row["codestpro1"];
				$ls_codest1=substr($ls_codest1,$li_longestpro1-1,$ls_loncodestpro1);
				$ls_codest2=$row["codestpro2"];
				$ls_codest2=substr($ls_codest2,$li_longestpro2-1,$ls_loncodestpro2);
				$ls_codest3=$row["codestpro3"];
				$ls_codest3=substr($ls_codest3,$li_longestpro3-1,$ls_loncodestpro3);
				$ls_codest4=$row["codestpro4"];
				$ls_codest4=substr($ls_codest4,$li_longestpro4-1,$ls_loncodestpro4);
				$ls_codest5=$row["codestpro5"];
				$ls_estcla=$row["estcla"];
				$ls_codest5=substr($ls_codest5,$li_longestpro5-1,$ls_loncodestpro5);
				$ls_codestpro=$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5;
				$li_disponible=number_format($row["disponible"],2,",",".");
				if(($ls_codestpro==$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5)&& ($ls_estclacar==$ls_estcla))
				{
					$ls_estilo = "celdas-azules";
				}
				else
				{
					$ls_estilo = "celdas-blancas";
				}
				
				$ls_programatica=$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5;
				print "<tr class=".$ls_estilo.">";
				print "<td align='center'>".$ls_programatica."</td>";
				print "<td align='center'>".$ls_spg_cuenta."</td>";
				print "<td align='left'>".$ls_denominacion."</td>";
				print "<td align='right'>".$li_disponible."</td>";
				print "<td style='cursor:pointer'>";
				print "<a href=\"javascript: ue_aceptar('".$ls_programatica."','".$ls_spg_cuenta."','".$ls_denominacion."','".$ls_codestpro."','".$ls_estcla."');\"><img src='../shared/imagebank/tools20/aprobado.gif' title='Agregar' width='15' height='15' border='0'></a></td>";
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
	}// end function uf_print_cuentas_cargos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_solicitud()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funci�n que obtiene e imprime los resultados de la busqueda de la Solicitud de ejecuci�n presupuestaria
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Modificado Por: Ing. Yozelin Barragan 
		// Fecha Creaci�n: 17/03/2007 								Fecha �ltima Modificaci�n : 12/07/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_loncodestpro1,$li_longestpro1,$ls_loncodestpro2,$li_longestpro2,$ls_loncodestpro3,$li_longestpro3;
		global $ls_loncodestpro4,$li_longestpro4,$ls_loncodestpro5,$li_longestpro5;

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
		$ls_estceniva=$_SESSION["la_empresa"]["estceniva"];
		$ls_estvercta=$_SESSION["la_empresa"]["estvercta"];
		$ls_numsol="%".$_POST["numsol"]."%";
		$ls_coduniadm="%".$_POST["coduniadm"]."%";
		$ls_codtipsol=substr($_POST["codtipsol"],0,2);
		if($ls_codtipsol=="-") // no selecciono ninguna
		{
			$ls_codtipsol="";
		}
		$ls_codtipsol="%".$ls_codtipsol."%";
		$ld_fecregdes=$io_funciones->uf_convertirdatetobd($_POST["fecregdes"]);
		$ld_fecreghas=$io_funciones->uf_convertirdatetobd($_POST["fecreghas"]);
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_tipo=$_POST['tipo'];
		$ls_codigo=$_POST['codigo'];
		$ls_tipdes=$_POST['tipdes'];
		$ls_consol=$_POST['consol'];
		$ls_denart=$_POST['denart'];
		$ls_denart=$_POST['denart'];
		$ls_logusr = $_SESSION["la_logusr"];
		$ls_criterio='';
		$ls_filautcan='';
		$ls_tiposolicitud="";
		if(($ls_tipo=="COPIARSEP")||($ls_tipo=="SEPINICIAL"))
		{
			$ls_tiposolicitud=SUBSTR($_POST['cmbcodtipsol'],0,2);
		}		
		if($ls_tipo=="AUTCAN")
                {
			$ls_filautcan = " AND sep_tiposolicitud.estope='R' AND sep_solicitud.estapro=0";
		}
		
		switch ($ls_tipdes)
		{
			case "P":
				$ls_tabla=", rpc_proveedor";
				$ls_cadena_provbene="AND sep_solicitud.cod_pro=rpc_proveedor.cod_pro AND rpc_proveedor.cod_pro='".$ls_codigo."' ";
			break;
			
			case "B":
				$ls_tabla=", rpc_beneficiario";
				$ls_cadena_provbene="AND sep_solicitud.ced_bene=rpc_beneficiario.ced_bene AND rpc_beneficiario.ced_bene='".$ls_codigo."' ";
			break;
			
			case "-":
				$ls_tabla="";
				$ls_cadena_provbene="";
			break;
		}
		if ($_SESSION["ls_gestor"]=='POSTGRES')
                {
			$ilike = 'I';
		}
		else{
			$ilike = '';
		}
		$ls_cadena=$io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		$ls_seguridad=$io_conexion->Concat('sep_solicitud.codestpro1','sep_solicitud.codestpro2','sep_solicitud.codestpro3','sep_solicitud.codestpro4','sep_solicitud.codestpro5','sep_solicitud.estcla');
		
		$ls_join_art = ", ";
		if($ls_denart)
		{
			$ls_criterio = " AND siv_articulo.denart  ".$ilike."LIKE '%".$ls_denart."%' ";
			$ls_join_art = " LEFT JOIN sep_dt_articulos ".
						   "    ON sep_dt_articulos.codemp = '".$ls_codemp."' ".
						   "   AND sep_dt_articulos.numsol like '".$ls_numsol."' ".
						   "   AND sep_solicitud.coduniadm like '".$ls_coduniadm."' ".
						   "   AND sep_solicitud.codtipsol like '".$ls_codtipsol."' ".
						   "   AND sep_dt_articulos.codemp = sep_solicitud.codemp ".
						   "   AND sep_dt_articulos.numsol = sep_solicitud.numsol ".
						   "  LEFT JOIN siv_articulo ".
						   "    ON sep_dt_articulos.codemp = '".$ls_codemp."' ".
						   "   AND sep_dt_articulos.numsol like '".$ls_numsol."' ".
						   "   AND sep_dt_articulos.codemp = siv_articulo.codemp ".
						   "   AND sep_dt_articulos.codart = siv_articulo.codart, ";

		}
		if($ls_tiposolicitud!="")
		{
			$ls_criterio=$ls_criterio." AND sep_solicitud.codtipsol='".$ls_tiposolicitud."'";
		}
		if($ls_estvercta=="1")
		{
			$ls_criterio=	$ls_criterio."AND  substr(numsol,1,6) IN (SELECT codintper FROM sss_permisos_internos WHERE codsis='SEP' AND codusu='".$ls_logusr."' AND enabled=1)";
		}
		if($ls_tipo=="SEPINICIAL")
		{
			$ls_criterio=$ls_criterio." AND sep_solicitud.estsol='C'";
		}		
		$ls_consol=strtoupper($ls_consol);
		$ls_sql="SELECT sep_solicitud.numsol, sep_solicitud.codtipsol, sep_solicitud.coduniadm, sep_solicitud.codfuefin, ".
				"		sep_solicitud.fecregsol, sep_solicitud.estsol, sep_solicitud.consol, sep_solicitud.monto, sep_solicitud.tipsepbie,".
				"		sep_solicitud.monbasinm, sep_solicitud.montotcar, sep_solicitud.tipo_destino, sep_solicitud.cod_pro, sep_solicitud.forpag, ".
				"		sep_solicitud.ced_bene, spg_unidadadministrativa.denuniadm, sigesp_fuentefinanciamiento.denfuefin,numsolini,obssol,".
				"       sep_solicitud.estapro, sep_tiposolicitud.estope, sep_tiposolicitud.modsep,sep_tiposolicitud.estayueco,sep_solicitud.nombenalt,sep_solicitud.numdocori,".
				"       sep_solicitud.codestpro1,sep_solicitud.codestpro2,sep_solicitud.codestpro3,sep_solicitud.codestpro4,sep_solicitud.codestpro5,sep_solicitud.estcla,sep_solicitud.conanusep,".
				"       (CASE tipo_destino WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                          FROM rpc_proveedor ".
				"                                         WHERE rpc_proveedor.codemp=sep_solicitud.codemp ".
				"                                           AND rpc_proveedor.cod_pro=sep_solicitud.cod_pro) ".
				"                         WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                          FROM rpc_beneficiario ".
				"                                         WHERE rpc_beneficiario.codemp=sep_solicitud.codemp ".
				"                                           AND rpc_beneficiario.ced_bene=sep_solicitud.ced_bene) ". 
				"                         ELSE 'NINGUNO' END ) AS nombre, ".
				"       (CASE tipo_destino WHEN 'P' THEN (SELECT rpc_proveedor.rifpro  ".
				"                                          FROM rpc_proveedor          ".
				"                                         WHERE rpc_proveedor.codemp=sep_solicitud.codemp ".
				"                                           AND rpc_proveedor.cod_pro=sep_solicitud.cod_pro) ".
				"                         WHEN 'B' THEN (SELECT rpc_beneficiario.rifben ".
				"                                          FROM rpc_beneficiario ".
				"                                         WHERE rpc_beneficiario.codemp=sep_solicitud.codemp ".
				"                                           AND rpc_beneficiario.ced_bene=sep_solicitud.ced_bene) ". 
				"                         ELSE 'NINGUNO' END ) AS rif ".
				"  FROM sep_solicitud ".$ls_join_art." spg_unidadadministrativa, sigesp_fuentefinanciamiento, sep_tiposolicitud ".$ls_tabla." ".
				" WHERE sep_solicitud.codemp='".$ls_codemp."' ".
				"   AND sep_solicitud.numsol like '".$ls_numsol."' ".
				"   AND sep_solicitud.coduniadm like '".$ls_coduniadm."' ".
				"   AND sep_solicitud.codtipsol like '".$ls_codtipsol."' ".
				"   AND sep_solicitud.fecregsol between '".$ld_fecregdes."' AND '".$ld_fecreghas."' ".
				"   ".$ls_cadena_provbene." ".
				$ls_criterio.
				"   AND upper(sep_solicitud.consol) ".$ilike."LIKE '%".$ls_consol."%' ".
				"   AND sep_solicitud.codemp=spg_unidadadministrativa.codemp ".
				"   AND sep_solicitud.coduniadm=spg_unidadadministrativa.coduniadm ".
				"   AND sep_solicitud.coduniadm=spg_unidadadministrativa.coduniadm ".
				"   AND sep_solicitud.codemp=sigesp_fuentefinanciamiento.codemp ".
				"   AND sep_solicitud.codfuefin=sigesp_fuentefinanciamiento.codfuefin ".
				"   AND sep_solicitud.codtipsol=sep_tiposolicitud.codtipsol ".
				$ls_filautcan.
				/*"   AND ".$ls_seguridad." IN (SELECT codintper FROM sss_permisos_internos".
				"                              WHERE sss_permisos_internos.codemp='".$ls_codemp."'".
				"                                AND codsis='SPG'".
				"                                AND codusu='".$ls_logusr."' AND enabled=1)".
				"   AND sep_solicitud.coduniadm IN (SELECT codintper FROM sss_permisos_internos".
				"                              WHERE sss_permisos_internos.codemp='".$ls_codemp."'".
				"                                AND codsis='SEP'".
				"                                AND codusu='".$ls_logusr."' AND enabled=1)".*/
				" ORDER BY ".$ls_campoorden." ".$ls_orden."";
		$rs_data=$io_sql->select($ls_sql);
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 align=center>";
		print "<tr>";
		print "<td align=center class='texto-azul'>Cantidad de Registros ".$io_sql->num_rows($rs_data)."</td>";
		print "</tr>";
		print "</table>";
		print "<table width=630 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=100  style='cursor:pointer' title='Ordenar por Numero de Solicitud' align='center' onClick=ue_orden('sep_solicitud.numsol')>Numero de Solicitud</td>";
		print "<td width=120 style='cursor:pointer' title='Ordenar por Unidad Ejecutora' align='center' onClick=ue_orden('spg_unidadadministrativa.denuniadm')>Unidad Ejecutora</td>";
		print "<td width=70  style='cursor:pointer' title='Ordenar por Fecha de Registro' align='center' onClick=ue_orden('sep_solicitud.fecregsol')>Fecha de Registro</td>";
		print "<td width=120 style='cursor:pointer' title='Ordenar por Proveedor/Beneficiario' align='center' onClick=ue_orden('nombre')>Proveedor / Beneficiario</td>";
		print "<td width=90  style='cursor:pointer' title='Ordenar por Estatus' align='center' onClick=ue_orden('sep_solicitud.estsol')>Estatus</td>";
		print "<td width=100 style='cursor:pointer' title='Ordenar por Monto' align='center' onClick=ue_orden('monto')>Monto</td>";
		print "</tr>";
		if($rs_data===false)
		{
			$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			$li_i=0;
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_numsol=$row["numsol"];
				$ls_codtipsol=$row["codtipsol"];
				$ls_coduniadm=$row["coduniadm"];
				$ls_codfuefin=$row["codfuefin"];
				$ls_estsol=$row["estsol"];
				$ls_consol=$row["consol"];
				$ls_consol= preg_replace("[\n|\r|\n\r]",' ',$ls_consol);
				$ls_forpag=$row["forpag"];
				$ls_tipo_destino=$row["tipo_destino"];
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
				$ls_rif=$row["rif"];
				$ls_nombre=$row["nombre"];
				$ls_denuniadm=$row["denuniadm"];
				$ls_denfuefin=$row["denfuefin"];
				$ls_estapro=$row["estapro"];
				$ld_fecregsol=$io_funciones->uf_formatovalidofecha($row["fecregsol"]);
				$ld_fecregsol=$io_funciones->uf_convertirfecmostrar($ld_fecregsol);
				$li_monto=number_format($row["monto"],2,",",".");
				$li_monbasinm=number_format($row["monbasinm"],2,",",".");
				$li_montotcar=number_format($row["montotcar"],2,",",".");
				$ls_estope=$row["estope"];
				$ls_modsep=$row["modsep"];
				$ls_codestpro1=$row["codestpro1"];
				$ls_codestpro2=$row["codestpro2"];
				$ls_codestpro3=$row["codestpro3"];
				$ls_codestpro4=$row["codestpro4"];
				$ls_codestpro5=$row["codestpro5"];
				$ls_estcla=$row["estcla"];
				$ls_nombenalt=$row["nombenalt"];
				$ls_estayueco = $row["estayueco"];
				$ls_tipsepbie = $row["tipsepbie"];
				$ls_consolanu=$row["conanusep"];
				$ls_numdocori=$row["numdocori"];
				$ls_numsolini=$row["numsolini"];
				$ls_obssol=$row["obssol"];
				$ls_estatus="";
				switch ($ls_estsol)
				{
					case "R":
						$ls_estatus="REGISTRO";
						break;
						
					case "E":
						if($ls_estapro==0)
						{
							$ls_estatus="EMITIDA";
						}
						else
						{
							$ls_estatus="EMITIDA (APROBADA)";
						}
						break;
						
					case "A":
						$ls_estatus="ANULADA";
						break;
						
					case "C":
						$ls_estatus="CONTABILIZADA";
						break;
						
					case "P":
						$ls_estatus="PROCESADA";
						break;
						
					case "D":
						$ls_estatus="DESPACHADA";
						break;
					
					case "L":
						$ls_estatus="DESPACHADA PARCIALMENTE";
						break;
				
					case "I":
						$ls_estatus="CERRADA POR INVENTARIO";
						break;
				}
				$li_i++;
				switch ($ls_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: ue_aceptar('$ls_numsol','$ls_codtipsol','$ls_coduniadm','$ls_codfuefin',".
											"'$ls_estsol','$ls_tipo_destino','$ls_codigo','$ls_denuniadm',".
											"'$ls_denfuefin','".$ls_nombre."','$ls_estapro','$ld_fecregsol','$li_monto','$li_monbasinm',".
											"'$li_montotcar','$ls_estatus','$ls_estope','$ls_modsep','$ls_codestpro1','$ls_codestpro2',".
											"'$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$ls_consol','$ls_nombenalt',".
											"'$ls_estayueco','$ls_tipsepbie','$ls_rif','$li_i','$ls_consolanu','$ls_numdocori','$ls_forpag','$ls_numsolini','$ls_obssol');\">".$ls_numsol."</a>";
						print "<input type='hidden' id='hidconsol".$li_i."' name='hidconsol".$ls_numsol."' value='".$ls_consol."' ></td>";
						print "<td align='left'>".$ls_denuniadm."</td>";
						print "<td align='center'>".$ld_fecregsol."</td>";
						print "<td align='left'>".$ls_nombre."</td>";
						print "<td align='center'>".$ls_estatus."</td>";
						print "<td align='right'>".$li_monto."</td>";
						print "</tr>";			
						break;

					case "REPDES":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: ue_aceptar_reportedesde('$ls_numsol');\">".$ls_numsol."</a></td>";
						print "<td>".$ls_denuniadm."</td>";
						print "<td align='center'>".$ld_fecregsol."</td>";
						print "<td>".$ls_nombre."</td>";
						print "<td align='center'>".$ls_estatus."</td>";
						print "<td align='right'>".$li_monto."</td>";
						print "</tr>";			
						break;

					case "REPHAS":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: ue_aceptar_reportehasta('$ls_numsol');\">".$ls_numsol."</a></td>";
						print "<td>".$ls_denuniadm."</td>";
						print "<td align='center'>".$ld_fecregsol."</td>";
						print "<td>".$ls_nombre."</td>";
						print "<td align='center'>".$ls_estatus."</td>";
						print "<td align='right'>".$li_monto."</td>";
						print "</tr>";			
						break;
					case "AUTCAN":
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: ue_aceptar_autcan('$ls_numsol','$ls_codtipsol','$ls_coduniadm','$ls_codfuefin',".
											"'$ls_estsol','$ls_tipo_destino','$ls_codigo','$ls_denuniadm',".
											"'$ls_denfuefin','".$ls_nombre."','$ls_estapro','$ld_fecregsol','$li_monto','$li_monbasinm',".
											"'$li_montotcar','$ls_estatus','$ls_estope','$ls_modsep','$ls_codestpro1','$ls_codestpro2',".
											"'$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$ls_consol','$ls_nombenalt',".
											"'$ls_estayueco','$ls_tipsepbie','$ls_rif','$li_i','$ls_numdocori');\">".$ls_numsol."</a>";
						print "<input type='hidden' id='hidconsol".$li_i."' name='hidconsol".$ls_numsol."' value='".$ls_consol."' ></td>";
						print "<td align='left'>".$ls_denuniadm."</td>";
						print "<td align='center'>".$ld_fecregsol."</td>";
						print "<td align='left'>".$ls_nombre."</td>";
						print "<td align='center'>".$ls_estatus."</td>";
						print "<td align='right'>".$li_monto."</td>";
						print "</tr>";			
						break;
					case "COPIARSEP":
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: ue_aceptar_copiar('$ls_numsol','$ls_codtipsol','$ls_coduniadm','$ls_codfuefin',".
											"'$ls_estsol','$ls_tipo_destino','$ls_codigo','$ls_denuniadm',".
											"'$ls_denfuefin','".$ls_nombre."','$ls_estapro','$ld_fecregsol','$li_monto','$li_monbasinm',".
											"'$li_montotcar','$ls_estatus','$ls_estope','$ls_modsep','$ls_codestpro1','$ls_codestpro2',".
											"'$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$ls_consol','$ls_nombenalt',".
											"'$ls_estayueco','$ls_tipsepbie','$ls_rif','$li_i','$ls_numdocori');\">".$ls_numsol."</a>";
						print "<input type='hidden' id='hidconsol".$li_i."' name='hidconsol".$ls_numsol."' value='".$ls_consol."' ></td>";
						print "<td align='left'>".$ls_denuniadm."</td>";
						print "<td align='center'>".$ld_fecregsol."</td>";
						print "<td align='left'>".$ls_nombre."</td>";
						print "<td align='center'>".$ls_estatus."</td>";
						print "<td align='right'>".$li_monto."</td>";
						print "</tr>";			
						break;
					case "SEPINICIAL":
						print "<tr class=celdas-blancas>";
						print "<td align='center'><a href=\"javascript: ue_aceptar_copiarinicial('$ls_numsol','$ls_codtipsol','$ls_coduniadm','$ls_codfuefin',".
											"'$ls_estsol','$ls_tipo_destino','$ls_codigo','$ls_denuniadm',".
											"'$ls_denfuefin','".$ls_nombre."','$ls_estapro','$ld_fecregsol','$li_monto','$li_monbasinm',".
											"'$li_montotcar','$ls_estatus','$ls_estope','$ls_modsep','$ls_codestpro1','$ls_codestpro2',".
											"'$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$ls_consol','$ls_nombenalt',".
											"'$ls_estayueco','$ls_tipsepbie','$ls_rif','$li_i','$ls_numdocori');\">".$ls_numsol."</a>";
						print "<input type='hidden' id='hidconsol".$li_i."' name='hidconsol".$ls_numsol."' value='".$ls_consol."' ></td>";
						print "<td align='left'>".$ls_denuniadm."</td>";
						print "<td align='center'>".$ld_fecregsol."</td>";
						print "<td align='left'>".$ls_nombre."</td>";
						print "<td align='center'>".$ls_estatus."</td>";
						print "<td align='right'>".$li_monto."</td>";
						print "</tr>";			
						break;
				}
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
	}// end function uf_print_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_servicios()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_servicios
		//		   Access: private
		//	    Arguments: 
		//	  Description: M�todo que obtiene e imprime el resultado de la busqueda de los servicios
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 17/03/2007								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_sep;
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
		$ls_codser="%".$_POST['codser']."%";
		$ls_denser="%".$_POST['denser']."%";
		$ls_codestpro1=$_POST['codestpro1'];
		$ls_codestpro2=$_POST['codestpro2'];
		$ls_codestpro3=$_POST['codestpro3'];
		$ls_codestpro4=$_POST['codestpro4'];
		$ls_codestpro5=$_POST['codestpro5'];
		$ls_codestpro1=str_pad($ls_codestpro1,25,0,0);
		$ls_codestpro2=str_pad($ls_codestpro2,25,0,0);
		$ls_codestpro3=str_pad($ls_codestpro3,25,0,0);
		$ls_codestpro4=str_pad($ls_codestpro4,25,0,0);
		$ls_codestpro5=str_pad($ls_codestpro5,25,0,0);
		$ls_estcla=$_POST["estcla"];
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		$ls_parsindis=$_SESSION["la_empresa"]["estparsindis"];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td style='cursor:pointer' title='Ordenar por Codigo'       align='center' onClick=ue_orden('codser')>Codigo</td>";
		print "<td style='cursor:pointer' title='Ordenar por Denominacion' align='center' onClick=ue_orden('denser')>Denominacion</td>";
		print "<td style='cursor:pointer' title='Ordenar por Precio'       align='center' onClick=ue_orden('preser')>Precio Unitario</td>";
		print "<td style='cursor:pointer' title='Ordenar por Cuenta'       align='center' onClick=ue_orden('spg_cuenta')>Cuenta</td>";
		print "<td></td>";
		print "</tr>";
		if($ls_parsindis=='0')
		{
		$ls_sql="SELECT codser, denser, preser,  TRIM(spg_cuenta) as spg_cuenta , ".
				"		(SELECT COUNT(spg_cuenta) FROM spg_cuentas ".
				"		  WHERE spg_cuentas.codestpro1 = '".$ls_codestpro1."' ".
				"		    AND spg_cuentas.codestpro2 = '".$ls_codestpro2."' ".
				"		    AND spg_cuentas.codestpro3 = '".$ls_codestpro3."' ".
				"		    AND spg_cuentas.codestpro4 = '".$ls_codestpro4."' ".
				"		    AND spg_cuentas.codestpro5 = '".$ls_codestpro5."' ".
				"           AND spg_cuentas.estcla = '".$ls_estcla."'".
				"			AND soc_servicios.codemp = spg_cuentas.codemp ".
				"			AND soc_servicios.spg_cuenta = spg_cuentas.spg_cuenta) AS existecuenta ".
				"  FROM soc_servicios ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codser like '".$ls_codser."' ".
				"   AND denser like '".$ls_denser."' ".
				" ORDER BY ".$ls_campoorden." ".$ls_orden." ";
		 }
		 else
		 {
		 	$ls_sql="SELECT codser, denser, preser,  TRIM(spg_cuenta) as spg_cuenta , ".
				"		(SELECT COUNT(spg_cuenta) FROM spg_cuentas ".
				"		  WHERE spg_cuentas.codestpro1 = '".$ls_codestpro1."' ".
				"		    AND spg_cuentas.codestpro2 = '".$ls_codestpro2."' ".
				"		    AND spg_cuentas.codestpro3 = '".$ls_codestpro3."' ".
				"		    AND spg_cuentas.codestpro4 = '".$ls_codestpro4."' ".
				"		    AND spg_cuentas.codestpro5 = '".$ls_codestpro5."' ".
				"           AND spg_cuentas.estcla = '".$ls_estcla."'".
				"			AND soc_servicios.codemp = spg_cuentas.codemp ".
				"			AND soc_servicios.spg_cuenta = spg_cuentas.spg_cuenta) AS existecuenta, ".
				"	    (SELECT (asignado-(comprometido+precomprometido)+aumento-disminucion) ".
				"		   FROM spg_cuentas ".
				"		  WHERE  spg_cuentas.codestpro1 = '".$ls_codestpro1."' ".
				"		    AND spg_cuentas.codestpro2 = '".$ls_codestpro2."'".
				"		    AND spg_cuentas.codestpro3 = '".$ls_codestpro3."' ".
				"		    AND spg_cuentas.codestpro4 = '".$ls_codestpro4."' ".
				"		    AND spg_cuentas.codestpro5 = '".$ls_codestpro5."' ".
				"           AND spg_cuentas.estcla='".$ls_estcla."'".
				"			AND spg_cuentas.spg_cuenta = soc_servicios.spg_cuenta) AS disponibilidad ".
				"  FROM soc_servicios ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codser like '".$ls_codser."' ".
				"   AND denser like '".$ls_denser."' ".
				" ORDER BY ".$ls_campoorden." ".$ls_orden." ";
		 }
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
			  if($ls_parsindis==0)
		      {
					$ls_codser=$row["codser"];
					$ls_denser=$row["denser"];
					$li_preser=number_format($row["preser"],2,",",".");
					$ls_spg_cuenta=$row["spg_cuenta"];
					$li_existecuenta=$row["existecuenta"];
					if($li_existecuenta==0)
					{
						$ls_estilo = "celdas-blancas";
					}
					else
					{
						$ls_estilo = "celdas-azules";
					}
					print "<tr class=".$ls_estilo.">";
					print "<td align='center'>".$ls_codser."</td>";
					print "<td align='left'>".$ls_denser."</td>";
					print "<td align='left'>".$li_preser."</td>";
					print "<td align='center'>".$ls_spg_cuenta."</td>";
					print "<td style='cursor:pointer'>";
					print "<a href=\"javascript: ue_aceptar('".$ls_codser."','".$ls_denser."','".$li_preser."','".$ls_spg_cuenta."','".$li_existecuenta."','".$ls_estcla."');\"><img src='../shared/imagebank/tools20/aprobado.gif' title='Agregar' width='15' height='15' border='0'></a></td>";
					print "</tr>";
			   }
			   else
			   {
			        $ls_codser=$row["codser"];
					$ls_denser=$row["denser"];
					$li_preser=number_format($row["preser"],2,",",".");
					$ls_spg_cuenta=$row["spg_cuenta"];
			   		$li_disponibilidad=$row["disponibilidad"];
					$li_existecuenta=$row["existecuenta"];
				    if($li_existecuenta==0)
					{ 
					  $ls_estilo = "celdas-blancas";
					}
					else
					{
					  $ls_estilo = "celdas-azules"; 
					}
					print "<tr class=".$ls_estilo.">";
					print "<td align='center'>".$ls_codser."</td>";
					print "<td align='left'>".$ls_denser."</td>";
					print "<td align='left'>".$li_preser."</td>";
					print "<td align='center'>".$ls_spg_cuenta."</td>";
					print "<td style='cursor:pointer'>";
					
					 if($li_disponibilidad >0)
					  {
					     $ls_estilo = "celdas-azules";
						  print "<a href=\"javascript: ue_aceptar('".$ls_codser."','".$ls_denser."','".$li_preser."','".$ls_spg_cuenta."','".$li_existecuenta."','".$ls_estcla."');\"><img src='../shared/imagebank/tools20/aprobado.gif' title='Agregar' width='15' height='15' border='0'></a></td>";
					      print "</tr>";	
					  }
					  else
					  {
					    print "<a href=\"javascript: ue_mensaje();\"><img src='../shared/imagebank/tools20/aprobado.gif' title='Agregar' width='15' height='15' border='0'></a></td>";
					    print "</tr>";
					  }
			   }			
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
	}// end function uf_print_servicios
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_conceptos()
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_conceptos
		//		   Access: private
		//	    Arguments: 
		//	  Description: M�todo que obtiene e imprime el resultado de la busqueda de los conceptos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 17/03/2007								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_sep;
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
		$ls_parsindis=$_SESSION["la_empresa"]["estparsindis"];
		$ls_codconsep="%".$_POST['codconsep']."%";
		$ls_denconsep="%".$_POST['denconsep']."%";
		$ls_codestpro1=$_POST['codestpro1'];
		$ls_codestpro2=$_POST['codestpro2'];
		$ls_codestpro3=$_POST['codestpro3'];
		$ls_codestpro4=$_POST['codestpro4'];
		$ls_codestpro5=$_POST['codestpro5'];
		$ls_estcla=$_POST["estcla"];
		$ls_codestpro1 = $io_funciones->uf_cerosizquierda($ls_codestpro1,25);
		$ls_codestpro2 = $io_funciones->uf_cerosizquierda($ls_codestpro2,25);
		$ls_codestpro3 = $io_funciones->uf_cerosizquierda($ls_codestpro3,25);
		$ls_codestpro4 = $io_funciones->uf_cerosizquierda($ls_codestpro4,25);
		$ls_codestpro5 = $io_funciones->uf_cerosizquierda($ls_codestpro5,25);
		$ls_orden=$_POST['orden'];
		$ls_campoorden=$_POST['campoorden'];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td style='cursor:pointer' title='Ordenar por Codigo'       align='center' onClick=ue_orden('codconsep')>Codigo</td>";
		print "<td style='cursor:pointer' title='Ordenar por Denominacion' align='center' onClick=ue_orden('denconsep')>Denominacion</td>";
		print "<td style='cursor:pointer' title='Ordenar por Precio'       align='center' onClick=ue_orden('monconsepe')>Precio Unitario</td>";
		print "<td style='cursor:pointer' title='Ordenar por Cuenta'       align='center' onClick=ue_orden('spg_cuenta')>Cuenta</td>";
		print "<td></td>";
		print "</tr>";
		if($ls_parsindis=='0')
		{
			$ls_sql="SELECT codconsep, denconsep, monconsepe, TRIM(spg_cuenta) as spg_cuenta, ".
					"		(SELECT COUNT(spg_cuenta) FROM spg_cuentas ".
					"		  WHERE spg_cuentas.codestpro1 = '".$ls_codestpro1."' ".
					"		    AND spg_cuentas.codestpro2 = '".$ls_codestpro2."' ".
					"		    AND spg_cuentas.codestpro3 = '".$ls_codestpro3."' ".
					"		    AND spg_cuentas.codestpro4 = '".$ls_codestpro4."' ".
					"		    AND spg_cuentas.codestpro5 = '".$ls_codestpro5."' ".
					"           AND  spg_cuentas.estcla = '".$ls_estcla."'".
					"			AND spg_cuentas.spg_cuenta = sep_conceptos.spg_cuenta) AS existecuenta ".
					"  FROM sep_conceptos ".
					" WHERE codconsep like '".$ls_codconsep."' ".
					"   AND denconsep like '".$ls_denconsep."' ".
					" ORDER BY ".$ls_campoorden." ".$ls_orden." ";
		}
		else
		{
		    $ls_sql="SELECT codconsep, denconsep, monconsepe, TRIM(spg_cuenta) as spg_cuenta, ".
			  		"		(SELECT COUNT(spg_cuenta) FROM spg_cuentas ".
					"		  WHERE spg_cuentas.codestpro1 = '".$ls_codestpro1."' ".
					"		    AND spg_cuentas.codestpro2 = '".$ls_codestpro2."' ".
					"		    AND spg_cuentas.codestpro3 = '".$ls_codestpro3."' ".
					"		    AND spg_cuentas.codestpro4 = '".$ls_codestpro4."' ".
					"		    AND spg_cuentas.codestpro5 = '".$ls_codestpro5."' ".
					"           AND  spg_cuentas.estcla = '".$ls_estcla."' ".
					"			AND spg_cuentas.spg_cuenta = sep_conceptos.spg_cuenta) AS existecuenta, ".
					"	    (SELECT (asignado-(comprometido+precomprometido)+aumento-disminucion) ".
					"		   FROM spg_cuentas ".
					"		  WHERE  spg_cuentas.codestpro1 = '".$ls_codestpro1."' ".
					"		    AND spg_cuentas.codestpro2 = '".$ls_codestpro2."'".
					"		    AND spg_cuentas.codestpro3 = '".$ls_codestpro3."' ".
					"		    AND spg_cuentas.codestpro4 = '".$ls_codestpro4."' ".
					"		    AND spg_cuentas.codestpro5 = '".$ls_codestpro5."' ".
					"           AND spg_cuentas.estcla='".$ls_estcla."'".
					"			AND spg_cuentas.spg_cuenta = sep_conceptos.spg_cuenta) AS disponibilidad ".
					"  FROM sep_conceptos  ".
					"  WHERE codconsep like '".$ls_codconsep."' ".
					"   AND denconsep like '".$ls_denconsep."' ".
					" ORDER BY ".$ls_campoorden." ".$ls_orden." ";
		}			
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
			   if ($ls_parsindis==0)
			   {
					$ls_codconsep=$row["codconsep"];
					$ls_denconsep=$row["denconsep"];
					$li_monconsepe=number_format($row["monconsepe"],2,",",".");
					$ls_spg_cuenta=$row["spg_cuenta"];
					$li_existecuenta=$row["existecuenta"];
					if($li_existecuenta==0)
					{
						$ls_estilo = "celdas-blancas";
					}
					else
					{
						$ls_estilo = "celdas-azules";
					}
					print "<tr class=".$ls_estilo.">";
					print "<td align='center'>".$ls_codconsep."</td>";
					print "<td align='left'>".$ls_denconsep."</td>";
					print "<td align='left'>".$li_monconsepe."</td>";
					print "<td align='center'>".$ls_spg_cuenta."</td>";
					print "<td style='cursor:pointer'>";
					print "<a href=\"javascript: ue_aceptar('".$ls_codconsep."','".$ls_denconsep."','".$li_monconsepe."','".$ls_spg_cuenta."','".$li_existecuenta."');\"><img src='../shared/imagebank/tools20/aprobado.gif' title='Agregar' width='15' height='15' border='0'></a></td>";
					print "</tr>";
				}
				else
				{ 
				    $ls_codconsep=$row["codconsep"];
					$ls_denconsep=$row["denconsep"];
					$li_monconsepe=number_format($row["monconsepe"],2,",",".");
					$ls_spg_cuenta=$row["spg_cuenta"];
					$li_existecuenta=$row["existecuenta"];
				    $li_existecuenta=$row["existecuenta"];
					$li_disponibilidad=$row["disponibilidad"];
					 if($li_existecuenta==0)
					{ 
					  $ls_estilo = "celdas-blancas";
					}
					else
					{
					  $ls_estilo = "celdas-azules"; 
					}
					print "<tr class=".$ls_estilo.">";
					print "<td align='center'>".$ls_codconsep."</td>";
					print "<td align='left'>".$ls_denconsep."</td>";
					print "<td align='left'>".$li_monconsepe."</td>";
					print "<td align='center'>".$ls_spg_cuenta."</td>";
					print "<td style='cursor:pointer'>";
					 if($li_disponibilidad >0)
					  {
					     $ls_estilo = "celdas-azules";
             			  print "<a href=\"javascript: ue_aceptar('".$ls_codconsep."','".$ls_denconsep."','".$li_monconsepe."','".$ls_spg_cuenta."','".$li_existecuenta."');\"><img src='../shared/imagebank/tools20/aprobado.gif' title='Agregar' width='15' height='15' border='0'></a></td>";
					      print "</tr>";	
					  }
					  else
					  {
					    print "<a href=\"javascript: ue_mensaje();\"><img src='../shared/imagebank/tools20/aprobado.gif' title='Agregar' width='15' height='15' border='0'></a></td>";
					    print "</tr>";
					  }				
						
				}
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
	}// end function uf_print_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_unidad_ejecutora()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: private
		//	    Arguments: 
		//	  Description: Funci�n que obtiene e imprime los resultados de la busqueda de la unidad ejecutora (Unidad administrativa)
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Modificado Por: Ing. Yozelin Barragan / Ing. Nestor Falcon 
		// Fecha Creaci�n: 17/03/2007 								Fecha �ltima Modificaci�n : 05/05/2007
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
		$ls_codunieje  = $_POST["coduniadm"];
		$ls_denunieje  = $_POST["denuniadm"];
		$ls_orden      = $_POST['orden'];
		$ls_campoorden = $_POST['campoorden'];
		$ls_tipo       = $_POST['tipo'];		
		$ls_logusr = $_SESSION["la_logusr"];
		$ls_gestor = $_SESSION["ls_gestor"];
		$ls_sql_seguridad = "";
		$ls_concatA = $io_conexion->Concat("'{$ls_codemp}'","'SEP'","'{$ls_logusr}'",'spg_unidadadministrativa.coduniadm');
		$ls_concatB = $io_conexion->Concat('codemp','codsis','codusu','codintper');
		$ls_sql_seguridad = " AND {$ls_concatA} IN (SELECT {$ls_concatB}
		                       FROM sss_permisos_internos WHERE codusu = '".$ls_logusr."' AND codsis = 'SEP' AND enabled=1)";
		$ls_sql="SELECT spg_unidadadministrativa.coduniadm, 
		                count(spg_dt_unidadadministrativa.codestpro1)as items,
                        max(spg_unidadadministrativa.denuniadm) as denuniadm,
						max(spg_dt_unidadadministrativa.codestpro1) as codestpro1, 
						max(spg_dt_unidadadministrativa.codestpro2) as codestpro2,  
						max(spg_dt_unidadadministrativa.codestpro3) as codestpro3,  
						max(spg_dt_unidadadministrativa.codestpro4) as codestpro4,  
						max(spg_dt_unidadadministrativa.codestpro5) as codestpro5, 
						max(spg_dt_unidadadministrativa.estcla) as estcla".
				"  FROM spg_unidadadministrativa, spg_dt_unidadadministrativa, spg_ep5 ".
				" WHERE spg_unidadadministrativa.codemp='".$ls_codemp."' ".
				"   AND spg_unidadadministrativa.coduniadm <>'----------' ".
				"   AND spg_unidadadministrativa.coduniadm like '%".$ls_codunieje."%' ".
				"   AND spg_unidadadministrativa.denuniadm like '%".$ls_denunieje."%' ".$ls_sql_seguridad.
				"   AND spg_unidadadministrativa.codemp=spg_dt_unidadadministrativa.codemp ".
				"   AND spg_unidadadministrativa.coduniadm=spg_dt_unidadadministrativa.coduniadm ".
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
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 align=center>";
		print "<tr>";
		print "<td align=center class='texto-azul'>Cantidad de Registros ".$io_sql->num_rows($rs_data)."</td>";
		print "</tr>";
		print "</table>";
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60  style='cursor:pointer' title='Ordenar por C�digo'       align='center' onClick=ue_orden('coduniadm')>C&oacute;digo</td>";
		if (empty($ls_tipo))
		   {
		     print "<td width=400 style='cursor:pointer' title='Ordenar por Denominaci�n' align='center' onClick=ue_orden('denuniadm')>Denominaci&oacute;n</td>";
			 print "<td width=40  style='cursor:pointer' title='Seleccionar Estructura Presupuestaria'>Detalle</td>";   
		   }
		else
		   {
		     print "<td width=440 style='cursor:pointer' title='Ordenar por Denominaci�n' align='center' onClick=ue_orden('denuniadm')>Denominaci&oacute;n</td>";
		   }
		print "</tr>";
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
							     echo "<td style=text-align:center width=60><a href=\"javascript: aceptar('$ls_codunieje','$ls_denunieje','$ls_codestpro1','$ls_codestpro2','$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla');\">".$ls_codunieje."</a></td>";
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
						
						case "ESTANDAR":
						    echo "<td style=text-align:center width=60><a href=\"javascript: aceptar_unidad('$ls_codunieje','$ls_denunieje');\">".$ls_codunieje."</a></td>";
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
		unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$ls_codemp);
	}// end function uf_print_unidadejecutora
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
		global $io_funciones_sep, $io_grid, $io_ds_cargos;
		
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
		require_once("class_funciones_sep.php");
		$io_funciones_sep=new class_funciones_sep();
				
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
			   }
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
						$lo_object[$li_fila][5]="<input name=txtmonimp".$li_fila."  type=text id=txtmonimp".$li_fila."     class=sin-borde  style=text-align:right  size=23 value='".$li_monimp."' readonly>".
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


?>