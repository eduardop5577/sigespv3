<?php
/***********************************************************************************
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
	global $li_estmodest,$li_loncodestpro1,$li_loncodestpro2,$li_loncodestpro3,$li_loncodestpro4,$li_loncodestpro5;
	global $ls_disabled,$ls_coduniadm,$li_numdecper, $arr_cargosprocesados, $ld_totalgasto, $ld_totalcargo, $ld_totalgeneral;

	$li_loncodestpro1=$_SESSION["la_empresa"]["loncodestpro1"];
	$li_longestpro1= (25-$li_loncodestpro1)+1;
	$li_loncodestpro2=$_SESSION["la_empresa"]["loncodestpro2"];
	$li_longestpro2= (25-$li_loncodestpro2)+1;
	$li_loncodestpro3=$_SESSION["la_empresa"]["loncodestpro3"];
	$li_longestpro3= (25-$li_loncodestpro3)+1;
	$li_loncodestpro4=$_SESSION["la_empresa"]["loncodestpro4"];
	$li_longestpro4= (25-$li_loncodestpro4)+1;
	$li_loncodestpro5=$_SESSION["la_empresa"]["loncodestpro5"];
	$li_longestpro5= (25-$li_loncodestpro5)+1;
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	$li_numdecper=$_SESSION["la_empresa"]["numdecper"];
	
	$ruta = '../../';
	require_once("../../base/librerias/php/general/sigesp_lib_conexiones.php");
    $io_conexiones=new conexiones();
	$io_conexiones->decodificar_post();
	require_once("../../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	require_once("class_funciones_sep.php");
	$io_funciones_sep=new class_funciones_sep();
	require_once("sigesp_sep_c_solicitud.php");
	$io_solicitud=new sigesp_sep_c_solicitud("../../");
	// tipo de SEP si es de BIENES ó de SERVICIOS
	$ls_tipo=$io_funciones_sep->uf_obtenervalor("tipo","-");
	// proceso a ejecutar
	$ls_proceso=$io_funciones_sep->uf_obtenervalor("proceso","");
	// total de filas de Bienes
	$li_totalbienes=$io_funciones_sep->uf_obtenervalor("totalbienes","1");
	// total de filas de Servicios
	$li_totalservicios=$io_funciones_sep->uf_obtenervalor("totalservicios","1");
	// total de filas de Servicios
	$li_totalconceptos=$io_funciones_sep->uf_obtenervalor("totalconceptos","1");
	// total de filas de Cargos
	$li_totalcargos=$io_funciones_sep->uf_obtenervalor("totalcargos","1");
	// total de filas de Cuentas
	$li_totalcuentas=$io_funciones_sep->uf_obtenervalor("totalcuentas","1");
	// total de filas de Cuentas cargos
	$li_totalcuentascargo=$io_funciones_sep->uf_obtenervalor("totalcuentascargo","1");
	// Indica si se deben cargar los cargos de un bien ó servicios ó si solo se deben pintar
	$ls_cargarcargos=$io_funciones_sep->uf_obtenervalor("cargarcargos","1");
	// Valor del Subtotal de la SEP
	$li_subtotal=$io_funciones_sep->uf_obtenervalor("subtotal","0,00");
	// Valor del Cargo de la SEP
	$li_cargos=$io_funciones_sep->uf_obtenervalor("cargos","0,00");
	// Valor del Total de la SEP
	$li_total=$io_funciones_sep->uf_obtenervalor("total","0,00");
	// Número de solicitud si se va a cargar
	$ls_numsol=$io_funciones_sep->uf_obtenervalor("numsol","");
	$ld_totalgasto=0;
	$ld_totalcargo=0;	
	$ld_totalgeneral=0;
	$ls_tipconpro = $io_funciones_sep->uf_obtenervalor("tipconpro","");
	$ls_forpag = $io_funciones_sep->uf_obtenervalor("forpag","");
	$ls_coduniadm = $io_funciones_sep->uf_obtenervalor("coduniadm","");
	$ls_codestpre = $io_funciones_sep->uf_obtenervalor("codestpre","");
	$ls_estclauni = $io_funciones_sep->uf_obtenervalor("estclauni","");
	$ls_tipsep = $io_funciones_sep->uf_obtenervalor("tipsep","");
	$ls_cambioest = $io_funciones_sep->uf_obtenervalor("cambioest","");
	$ls_titulo="";
	$la_cuentacargo[0]['cargo']="";
	$la_cuentacargo[0]['cuenta']="";
	$la_cuentacargo[0]['monto']="";
	$li_cuenta=1;
		$ls_tipafeiva=$_SESSION["la_empresa"]["confiva"];
	$ls_codtipsep=substr($ls_tipo,0,2);
	switch($ls_proceso)
	{
		case "LIMPIAR":
		 
			switch(substr($ls_tipo,3,1))
			{
				case "B": // Bienes
					$ls_titulo="Bien o Material";
					uf_print_bienes($li_totalbienes, false, $ls_tipsep,$ls_cargarcargos);
					uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"B");
					break;
					
				case "S": // Servicios
					$ls_titulo="Servicios";
					uf_print_servicios($li_totalservicios, false, $ls_tipsep,$ls_cargarcargos);
					uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"S");
					break;
					
				case "O": // Conceptos
					$ls_titulo="Conceptos";
					uf_print_conceptos($li_totalconceptos,$ls_cargarcargos,substr($ls_tipo,0,2));
					uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"O");
					break;
			}
			break;
			
		case "AGREGARBIENES":
			$ls_titulo="Bien o Material";
			uf_print_bienes($li_totalbienes, false, $ls_tipsep, $ls_cargarcargos);
			uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"B");
			break;
			
		case "LOADBIENES":
			$ls_titulo="Bien o Material";
			uf_load_bienes($ls_numsol,false,$ls_tipsep);
			uf_load_creditos($ls_titulo,$ls_numsol,"B");
			uf_load_cuentas($ls_numsol,"B");
			if ($ls_tipafeiva=='P')
			{
				uf_load_cuentas_cargo($ls_numsol,"B");
			}
			uf_load_total($li_subtotal,$li_cargos,$li_total);
			break;
			
		case "AGREGARSERVICIOS":
			$ls_titulo="Servicios";
			uf_print_servicios($li_totalservicios, false, $ls_tipsep, $ls_cargarcargos);
			uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"S");
			break;

		case "LOADSERVICIOS":
			$ls_titulo="Servicios";
			uf_load_servicios($ls_numsol,false,$ls_tipsep);
			uf_load_creditos($ls_titulo,$ls_numsol,"S");
			uf_load_cuentas($ls_numsol,"S");
			if ($ls_tipafeiva=='P')
			{
				uf_load_cuentas_cargo($ls_numsol,"S");
			}
			uf_load_total($li_subtotal,$li_cargos,$li_total);
			break;
			
		case "AGREGARCONCEPTOS":
			$ls_titulo="Conceptos";
			uf_print_conceptos($li_totalconceptos,$ls_cargarcargos,substr($ls_tipo,0,2));
			uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"O");
			break;

		case "LOADCONCEPTOS":
			$ls_titulo="Conceptos";
			uf_load_conceptos($ls_numsol);
			uf_load_creditos($ls_titulo,$ls_numsol,"O");
			uf_load_cuentas($ls_numsol,"O");
			if ($ls_tipafeiva=='P')
			{
				uf_load_cuentas_cargo($ls_numsol,"O");
			}
			uf_load_total($li_subtotal,$li_cargos,$li_total);
			break;
			
		case "AGREGARCUENTAS":
			switch(substr($ls_tipo,3,1))
			{
				case "B": // Bienes
					$ls_titulo="Bien o Material";
					uf_procesar_cargos($li_totalbienes,$ls_forpag,$ls_tipconpro,"B");
					uf_print_bienes($li_totalbienes, false, $ls_tipsep, $ls_cargarcargos);
					uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"B");
					uf_print_cierrecuentas_gasto($li_totalbienes,"B");
					if ($ls_tipafeiva=='P')
					{
						uf_print_cierrecuentas_cargo($li_totalcargos,$ls_cargarcargos,"B");
					}
					uf_print_total($li_totalbienes,"B");
					break;
						
				case "S": // Servicios
					$ls_titulo="Servicios";
					uf_procesar_cargos($li_totalservicios,$ls_forpag,$ls_tipconpro,"S");
					uf_print_servicios($li_totalservicios, false, $ls_tipsep,$ls_cargarcargos);
					uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"S");
					uf_print_cierrecuentas_gasto($li_totalservicios,"S");
					if ($ls_tipafeiva=='P')
					{
						uf_print_cierrecuentas_cargo($li_totalcargos,$ls_cargarcargos,"S");
					}
					uf_print_total($li_totalservicios,"S");
					break;
						
				case "O": // Conceptos
					$ls_titulo="Conceptos";
					uf_procesar_cargos($li_totalconceptos,$ls_forpag,$ls_tipconpro,"O");
					uf_print_conceptos($li_totalconceptos,$ls_cargarcargos,substr($ls_tipo,0,2));
					uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"O");
					uf_print_cierrecuentas_gasto($li_totalconceptos,"O");
					if ($ls_tipafeiva=='P')
					{
						uf_print_cierrecuentas_cargo($li_totalcargos,$ls_cargarcargos,"O");
					}
					uf_print_total($li_totalconceptos,"O");
					break;
			}
			break;
		
		case "LOADBIENES_AUTCAN":
			$ls_titulo="Bien o Material";
			uf_load_bienes($ls_numsol,true, $ls_tipsep);
			uf_load_creditos($ls_titulo,$ls_numsol,"B");
			uf_load_cuentas($ls_numsol,"B");
			if ($ls_tipafeiva=='P')
			{
				uf_load_cuentas_cargo($ls_numsol,"B");
			}
			uf_load_total($li_subtotal,$li_cargos,$li_total);
			break;
		
		case "AGREGARCUENTAS_AUTCAN":
			switch(substr($ls_tipo,3,1))
			{
				case "B": // Bienes
					$ls_titulo="Bien o Material";
					uf_print_bienes($li_totalbienes, true, $ls_tipsep, $ls_cargarcargos);
					uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"B",true);
					uf_print_cierrecuentas_gasto($li_totalbienes,"B",true);
					if ($ls_tipafeiva=='P')
					{
						uf_print_cierrecuentas_cargo($li_totalcargos,$ls_cargarcargos,"B");
					}
					uf_print_total($li_totalbienes,"B");
					break;
						
				case "S": // Servicios
					$ls_titulo="Servicios";
					uf_print_servicios($li_totalservicios,true,$ls_tipsep,$ls_cargarcargos);
					uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"S",true);
					uf_print_cierrecuentas_gasto($li_totalservicios,"S",true);
					if ($ls_tipafeiva=='P')
					{
						uf_print_cierrecuentas_cargo($li_totalcargos,$ls_cargarcargos,"S");
					}
					uf_print_total($li_totalservicios,"S");
					break;
			}
			break;
		
		case "AGREGARBIENES_AUTCAN":
			$ls_titulo="Bien o Material";
			uf_print_bienes($li_totalbienes, true, $ls_tipsep, $ls_cargarcargos);
			uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"B");
			break;
		
		case "LOADSERVICIOS_AUTCAN":
			$ls_titulo="Servicios";
			uf_load_servicios($ls_numsol,true, $ls_tipsep);
			uf_load_creditos($ls_titulo,$ls_numsol,"S");
			uf_load_cuentas($ls_numsol,"S");
			if ($ls_tipafeiva=='P')
			{
				uf_load_cuentas_cargo($ls_numsol,"S");
			}
			uf_load_total($li_subtotal,$li_cargos,$li_total);
			break;
		
		case "AGREGARSERVICIOS_AUTCAN":
			$ls_titulo="Servicios";
			uf_print_servicios($li_totalservicios,true,$ls_tipsep,$ls_cargarcargos);
			uf_print_creditos($ls_titulo,$li_totalcargos,$ls_cargarcargos,$ls_tipconpro,"S");
			break;
			
		case "COPIARBIENES":
			$ls_titulo="Bien o Material";
			uf_copiar_bienes($ls_numsol,$ls_codestpre,$ls_estclauni);
			uf_copiar_creditos($ls_titulo,$ls_numsol,$ls_codestpre,$ls_estclauni,"B");
			break;
			
		case "COPIARSERVICIOS":
			$ls_titulo="Servicios";
			uf_copiar_servicios($ls_numsol,$ls_codestpre,$ls_estclauni);
			uf_copiar_creditos($ls_titulo,$ls_numsol,$ls_codestpre,$ls_estclauni,"S");
			break;
			
		case "COPIARCONCEPTOS":
			$ls_titulo="Conceptos";
			uf_copiar_conceptos($ls_numsol,$ls_codestpre,$ls_estclauni);
			uf_copiar_creditos($ls_titulo,$ls_numsol,$ls_codestpre,$ls_estclauni,"O");
			break;
			
		case "BUSCARUSUARIOS":
			uf_buscar_usuarios($ls_numsol);
			break;			
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_bienes($ai_total,$autocan=false,$tipsep='',$as_cargarcargos)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_bienes
		//		   Access: private
		//	    Arguments: ai_total  // Total de filas a imprimir
		//	  Description: Método que imprime el grid de los Bienes
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $io_solicitud, $li_numdecper, $arr_cargosprocesados, $ld_totalgasto, $ld_totalcargo,  $ld_totalgeneral;
		$i=0;
		$ls_ronlyprecio = "";
		if ($tipsep=='R' && $_SESSION['la_empresa']['blopresep']=='1')
		{
			$ls_ronlyprecio = "readonly";
		} 
		
		// Titulos del Grid de Bienes
		$lo_title[1]="C&oacute;digo";
		$lo_title[2]="Denominaci&oacute;n";
		$lo_title[3]="Cantidad";
		if($autocan)
		{
			$lo_title[4]="Cantidad Autorizada";
			$i=1;
		}
		$lo_title[4+$i]="Modalidad";
		$lo_title[5+$i]="U/M";
		$lo_title[6+$i]="Precio/Unid.";
		$lo_title[7+$i]="Sub-Total";
		$lo_title[8+$i]="Cargos"; 
		$lo_title[9+$i]="Total";
		$lo_title[10+$i]="";		
		// Recorrido de todos los Bienes del Grid
		$ls_estmodpart=$io_solicitud->uf_validar_cambio_imputacion();//verifica si tiene permiso para modificar las partidas
		if ($ls_estmodpart==1)
		{
			$lo_title[11]="";
		}			
		for($li_fila=1;$li_fila<=$ai_total;$li_fila++)
		{
			$ls_codart    = trim($io_funciones_sep->uf_obtenervalor("txtcodart".$li_fila,""));
			$ls_denart    = $io_funciones_sep->uf_obtenervalor("txtdenart".$li_fila,"");
			$li_canart    = $io_funciones_sep->uf_obtenervalor("txtcanart".$li_fila,"0,00");
			if ($autocan)
			{
				$li_canartauto  = $io_funciones_sep->uf_obtenervalor("txtcanartauto".$li_fila,"0,00");
			}
			$ls_unidad    = $io_funciones_sep->uf_obtenervalor("cmbunidad".$li_fila,"M");
			$ls_medida    = $io_funciones_sep->uf_obtenervalor("txtmedida".$li_fila,"");
			$li_preart    = $io_funciones_sep->uf_obtenervalor("txtpreart".$li_fila,"0,00");
			$li_subtotart = $io_funciones_sep->uf_obtenervalor("txtsubtotart".$li_fila,"0,00");
			if (($as_cargarcargos=='1')&&($ls_codart<>''))
			{
				$lb_encontro = false;
				$li_carart	  = number_format(0,'2',',','.');
				$li_totart	  = $li_subtotart;
				foreach($arr_cargosprocesados as $key=>$cargos)
				{
					if(trim($cargos['codigo']) == trim($ls_codart))
					{
						$li_carart	  = number_format($cargos['cargo'],'2',',','.');
						$li_totart	  = number_format($cargos['total'],'2',',','.');
						$lb_encontro = true;
					}
					else
					{
						if (!$lb_encontro)
						{
							$li_carart	  = number_format(0,'2',',','.');
							$li_totart	  = $li_subtotart;
						}
					}
				}
			}
			else
			{
				$li_carart	  = $io_funciones_sep->uf_obtenervalor("txtcarart".$li_fila,"0,00");
				$li_totart	  = $io_funciones_sep->uf_obtenervalor("txttotart".$li_fila,"0,00");
			}
			$li_monto = str_replace('.','',$li_subtotart);
			$li_monto = str_replace(',','.',$li_monto);
			$ld_totalgasto = $ld_totalgasto + $li_monto;
			$li_monto = str_replace('.','',$li_carart);
			$li_monto = str_replace(',','.',$li_monto);
			$ld_totalcargo = $ld_totalcargo + $li_monto;
			$li_monto = str_replace('.','',$li_totart);
			$li_monto = str_replace(',','.',$li_monto);
			$ld_totalgeneral = $ld_totalgeneral + $li_monto;
			$ls_spgcuenta = $io_funciones_sep->uf_obtenervalor("txtspgcuenta".$li_fila,"");
			$li_unidad	  = $io_funciones_sep->uf_obtenervalor("txtunidad".$li_fila,"");	
			$ls_codpro	  = trim($io_funciones_sep->uf_obtenervalor("txtcodgas".$li_fila,""));
			$ls_cuenta	  = trim($io_funciones_sep->uf_obtenervalor("txtcodspg".$li_fila,""));
			$ls_estcla	  = trim($io_funciones_sep->uf_obtenervalor("txtstatus".$li_fila,""));
			if($ls_unidad=="M") // Si es al Mayor
			{
				$ls_maysel="selected";
				$ls_detsel="";
			}
			else // Si es al Detal
			{
				$ls_maysel="";
				$ls_detsel="selected";
			}
			if($li_numdecper!="3")
			{
				$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
			}
			else
			{
				$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
			}
				
			$lo_object[$li_fila][1]="<input type=text name=txtcodart".$li_fila."    id=txtcodart".$li_fila." class=sin-borde style=text-align:center size=22 value='".$ls_codart."'    readonly>
									 <input type=hidden name=txtcodgas".$li_fila." id=txtcodgas".$li_fila."  value='".$ls_codpro."' readonly>
									 <input type=hidden name=txtcodspg".$li_fila." id=txtcodspg".$li_fila."  value='".$ls_cuenta."' readonly>
									 <input type=hidden name=txtstatus".$li_fila." id=txtstatus".$li_fila."  value='".$ls_estcla."' readonly>";
			$lo_object[$li_fila][2]="<input type=text name=txtdenart".$li_fila."    class=sin-borde style=text-align:left   size=20 value='".$ls_denart."'    readonly>";
			if($autocan)
			{
				$lo_object[$li_fila][3]="<input type=text name=txtcanart".$li_fila."    class=sin-borde style=text-align:right size=8  value='".$li_canart."'     readonly>";
				$lo_object[$li_fila][4]="<input type=text name=txtcanartauto".$li_fila."    class=sin-borde style=text-align:right size=8  value='".$li_canartauto."'     $ls_funcion onBlur=ue_procesar_monto('B','".$li_fila."');>";
			}
			else
			{
				$lo_object[$li_fila][3]="<input type=text name=txtcanart".$li_fila."    class=sin-borde style=text-align:right size=8  value='".$li_canart."' $ls_funcion onBlur=ue_procesar_monto('B','".$li_fila."');>";
			} 
			$lo_object[$li_fila][4+$i]="<select name=cmbunidad".$li_fila." style='width:60px' onChange=ue_procesar_monto('B','".$li_fila."');><option value=D ".$ls_detsel.">Detal</option><option value=M ".$ls_maysel.">Mayor</option></select>";
			$lo_object[$li_fila][5+$i]="<input type=text name=txtmedida".$li_fila." class=sin-borde style=text-align:center  size=14 value='".$ls_medida."' readonly>";
			$lo_object[$li_fila][6+$i]="<input type=text name=txtpreart".$li_fila."    class=sin-borde style=text-align:right  size=10 value='".$li_preart."' 	  onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('B','".$li_fila."'); ".$ls_ronlyprecio.">";
			$lo_object[$li_fila][7+$i]="<input type=text name=txtsubtotart".$li_fila." class=sin-borde style=text-align:right  size=14 value='".$li_subtotart."' readonly>";
			$lo_object[$li_fila][8+$i]="<input type=text name=txtcarart".$li_fila."    class=sin-borde style=text-align:right  size=10 value='".$li_carart."'    readonly>";
			$lo_object[$li_fila][9+$i]="<input type=text name=txttotart".$li_fila."    class=sin-borde style=text-align:right  size=14 value='".$li_totart."'    readonly>".
									" <input type=hidden name=txtspgcuenta".$li_fila."  value='".$ls_spgcuenta."'> ".
									" <input type=hidden name=txtunidad".$li_fila."     value='".$li_unidad."'>";
			if($li_fila==$ai_total)// si el la última fila no pinto el eliminar
			{
				$lo_object[$li_fila][10+$i]="";
				if ($ls_estmodpart==1)
				{
					$lo_object[$li_fila][11+$i]="";
				}
			}
			else
			{
				$lo_object[$li_fila][10+$i]="<a href=javascript:ue_delete_bienes('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>";
				if ($ls_estmodpart==1)
				{
					$lo_object[$li_fila][11+$i]="<a href=javascript:ue_cambiar_partida_bien('".$li_fila."','".$ls_codpro."','".$ls_cuenta."','".$ls_estcla."','1');><img src=../shared/imagebank/mas.gif title=Cambiar width=14 height=14 border=0></a>";
				}
			}
			
		}
	    if ($autocan)
		{
	    	print "<p>&nbsp;</p>";
		}
		else
		{
			print "<p>&nbsp;</p>";
			print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
			print "    <tr>";
			print " 	  <td height='22' align='left'><a href='javascript:ue_catalogobienes();'><img src='../shared/imagebank/tools/nuevo.gif' title='Agregar Detalle Bienes' width='20' height='20' border='0'>Agregar Detalle Bienes</a></td>";
			print "    </tr>";
			print "  </table>";
	    }
		$io_grid->makegrid($ai_total,$lo_title,$lo_object,840,"Detalle de Bienes","gridbienes");
	}// end function uf_print_bienes
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_servicios($ai_total,$autcan=false,$tipsep='',$as_cargarcargos)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_servicios
		//		   Access: private
		//	    Arguments: ai_total  // Total de filas a imprimir
		//	  Description: Método que imprime el grid de los servicios
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $io_solicitud, $arr_cargosprocesados, $ld_totalgasto, $ld_totalcargo, $ld_totalgeneral;
		$i=0;
		
		$ls_ronlyprecio = "";
		if ($tipsep=='R' && $_SESSION['la_empresa']['blopresep']=='1')
		{
			$ls_ronlyprecio = "readonly";
		}

		$lo_title[1]="C&oacute;digo";
		$lo_title[2]="Denominaci&oacute;n";
		$lo_title[3]="Cantidad";
		if ($autcan)
		{
			$lo_title[4]="Cantidad Autorizada";
			$i=1;
		}
		$lo_title[4+$i]="Precio";
		$lo_title[5+$i]="Sub-Total";
		$lo_title[6+$i]="Cargos";
		$lo_title[7+$i]="Total";
		$lo_title[8+$i]="";
		$ls_estmodpart=$io_solicitud->uf_validar_cambio_imputacion();//verifica si tiene permiso para modificar las partidas
		if ($ls_estmodpart==1)
		{
			$lo_title[9+$i]="";
		}		
		for($li_fila=1;$li_fila<=$ai_total;$li_fila++)
		{
			$ls_codser=$io_funciones_sep->uf_obtenervalor("txtcodser".$li_fila,"");
			$ls_denser=$io_funciones_sep->uf_obtenervalor("txtdenser".$li_fila,"");
			$li_canser=$io_funciones_sep->uf_obtenervalor("txtcanser".$li_fila,"0,00");
			if ($autcan)
			{
				$li_canserauto=$io_funciones_sep->uf_obtenervalor("txtcanserauto".$li_fila,"0,00");
			}
			$li_preser=$io_funciones_sep->uf_obtenervalor("txtpreser".$li_fila,"0,00");
			$li_subtotser=$io_funciones_sep->uf_obtenervalor("txtsubtotser".$li_fila,"0,00");
			if (($as_cargarcargos=='1')&&($ls_codser<>''))
			{
				$lb_encontro = false;
				$li_carser	  = number_format(0,'2',',','.');
				$li_totser	  = $li_subtotser;
				foreach($arr_cargosprocesados as $key=>$cargos)
				{
					if(trim($cargos['codigo']) == trim($ls_codser))
					{
						$li_carser	  = number_format($cargos['cargo'],'2',',','.');
						$li_totser	  = number_format($cargos['total'],'2',',','.');
						$lb_encontro = true;
					}
					else
					{
						if (!$lb_encontro)
						{
							$li_carser	  = number_format(0,'2',',','.');
							$li_totser	  = $li_subtotser;
						}
					}
				}
			}
			else
			{
				$li_carser=$io_funciones_sep->uf_obtenervalor("txtcarser".$li_fila,"0,00");
				$li_totser=$io_funciones_sep->uf_obtenervalor("txttotser".$li_fila,"0,00");
			}
			$ls_spgcuenta=$io_funciones_sep->uf_obtenervalor("txtspgcuenta".$li_fila,"");
			$li_monto = str_replace('.','',$li_subtotser);
			$li_monto = str_replace(',','.',$li_monto);
			$ld_totalgasto = $ld_totalgasto + $li_monto;
			$li_monto = str_replace('.','',$li_carser);
			$li_monto = str_replace(',','.',$li_monto);
			$ld_totalcargo = $ld_totalcargo + $li_monto;
			$li_monto = str_replace('.','',$li_totser);
			$li_monto = str_replace(',','.',$li_monto);
			$ld_totalgeneral = $ld_totalgeneral + $li_monto;			
			///---------campos relacionados al gasto----------------------------------------
			$ls_codproser=trim($io_funciones_sep->uf_obtenervalor("txtcodgas".$li_fila,""));
			$ls_cuentaser=trim($io_funciones_sep->uf_obtenervalor("txtcodspg".$li_fila,""));
			$ls_estclaser=trim($io_funciones_sep->uf_obtenervalor("txtstatus".$li_fila,""));
			//-------------------------------------------------------------------------------	
			$lo_object[$li_fila][1]="<input type=text name=txtcodser".$li_fila."    id=txtcodser".$li_fila." class=sin-borde  style=text-align:center  size=15 value='".$ls_codser."' readonly>
									 <input type=hidden name=txtcodgas".$li_fila." id=txtcodgas".$li_fila."  value='".$ls_codproser."' readonly>
									 <input type=hidden name=txtcodspg".$li_fila." id=txtcodspg".$li_fila."  value='".$ls_cuentaser."' readonly>
									 <input type=hidden name=txtstatus".$li_fila." id=txtstatus".$li_fila."  value='".$ls_estclaser."' readonly>";
			$lo_object[$li_fila][2]="<input type=text name=txtdenser".$li_fila."    class=sin-borde  style=text-align:left    size=30 value='".$ls_denser."' readonly>";
			if ($autcan)
			{
				$lo_object[$li_fila][3]="<input type=text name=txtcanser".$li_fila."    class=sin-borde  style=text-align:right  size=9  value='".$li_canser."' readonly>";
				$lo_object[$li_fila][4]="<input type=text name=txtcanserauto".$li_fila."    class=sin-borde  style=text-align:right  size=9  value='".$li_canserauto."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('S','".$li_fila."');>";
			}
			else
			{
				$lo_object[$li_fila][3]="<input type=text name=txtcanser".$li_fila."    class=sin-borde  style=text-align:right  size=9  value='".$li_canser."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('S','".$li_fila."');>";
			}
			$lo_object[$li_fila][4+$i]="<input type=text name=txtpreser".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='".$li_preser."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('S','".$li_fila."'); $ls_ronlyprecio>";
			$lo_object[$li_fila][5+$i]="<input type=text name=txtsubtotser".$li_fila." class=sin-borde  style=text-align:right   size=15 value='".$li_subtotser."' readonly>";
			$lo_object[$li_fila][6+$i]="<input type=text name=txtcarser".$li_fila."    class=sin-borde  style=text-align:right   size=10 value='".$li_carser."' readonly>";
			$lo_object[$li_fila][7+$i]="<input type=text name=txttotser".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='".$li_totser."' readonly>".
									"<input type=hidden name=txtspgcuenta".$li_fila."  value='".$ls_spgcuenta."'> ";
			if($li_fila==$ai_total)// si el la última fila no pinto el eliminar
			{
				$lo_object[$li_fila][8+$i]="";
				if ($ls_estmodpart==1)
				{
					$lo_object[$li_fila][9+$i]="";
				}
			}
			else
			{
				$lo_object[$li_fila][8+$i] ="<a href=javascript:ue_delete_servicios('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0><input type=hidden name=hidspgcuentas".$li_fila."  value=''></a>";
				if ($ls_estmodpart==1)
				{
					$lo_object[$li_fila][9+$i]="<a href=javascript:ue_cambiar_partida_servicio('".$li_fila."','".$ls_codproser."','".$ls_cuentaser."','".$ls_estclaser."','3');><img src=../shared/imagebank/mas.gif title=Cambiar width=14 height=14 border=0></a>";
				}
			}
		}
		if ($autcan)
		{
			print "<p>&nbsp;</p>";
		}
		else
		{
			print "<p>&nbsp;</p>";
			print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
			print "    <tr>";
			print "		<td height='22' colspan='3' align='left'><a href='javascript:ue_catalogoservicios();'><img src='../shared/imagebank/tools/nuevo.gif' width='20' height='20' border='0' title='Agregar Detalle Servicios'>Agregar Detalle Servicios</a></td>";
			print "    </tr>";
			print "  </table>";
		}
		
		$io_grid->makegrid($ai_total,$lo_title,$lo_object,840,"Detalle de Servicios","gridservicios");
	}// end function uf_print_servicios
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_conceptos($ai_total,$as_cargarcargos,$as_codtipsep)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_conceptos
		//		   Access: private
		//	    Arguments: ai_total  // Total de filas a imprimir
		//	  Description: Método que imprime el grid de los conceptos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $io_solicitud, $arr_cargosprocesados, $ld_totalgasto, $ld_totalcargo, $ld_totalgeneral;

		$lo_title[1]="C&oacute;digo";
		$lo_title[2]="Denominaci&oacute;n";
		$lo_title[3]="Cantidad";
		$lo_title[4]="Precio";
		$lo_title[5]="Sub-Total";
		$lo_title[6]="Cargos";
		$lo_title[7]="Total";
		$lo_title[8]="";
        $ls_estdifiva=$io_solicitud->uf_validar_diferencial_iva($as_codtipsep);//verifica si tiene permiso para modificar las partidas
        $ls_estmodpart=$io_solicitud->uf_validar_cambio_imputacion();//verifica si tiene permiso para modificar las partidas
		if ($ls_estmodpart==1)
		{
			$lo_title[9]="";
		}	
		for($li_fila=1;$li_fila<=$ai_total;$li_fila++)
		{
			$ls_codcon	  = $io_funciones_sep->uf_obtenervalor("txtcodcon".$li_fila,"");
			$ls_dencon	  = $io_funciones_sep->uf_obtenervalor("txtdencon".$li_fila,"");
			$ld_cancon	  = $io_funciones_sep->uf_obtenervalor("txtcancon".$li_fila,"0,00");   
			$ld_precon	  = $io_funciones_sep->uf_obtenervalor("txtprecon".$li_fila,"0,00");    
			$ld_subtotcon = $io_funciones_sep->uf_obtenervalor("txtsubtotcon".$li_fila,"0,00");
			if (($as_cargarcargos=='1')&&($ls_codcon<>''))
			{
				$lb_encontro = false;
				$ld_carcon = number_format(0,'2',',','.');
				$ld_totcon = $ld_subtotcon;
				foreach($arr_cargosprocesados as $key=>$cargos)
				{
					if(trim($cargos['codigo']) == trim($ls_codcon))
					{
						$ld_carcon = number_format($cargos['cargo'],'2',',','.');
						$ld_totcon = number_format($cargos['total'],'2',',','.');
						$lb_encontro = true;
					}
					else
					{
						if (!$lb_encontro)
						{
							$ld_carcon = number_format(0,'2',',','.');
							$ld_totcon = $ld_subtotcon;
						}
					}
				}
			}
			else
			{
				$ld_carcon = $io_funciones_sep->uf_obtenervalor("txtcarcon".$li_fila,"0,00");			
				$ld_totcon = $io_funciones_sep->uf_obtenervalor("txttotcon".$li_fila,"0,00");    
			}
			$li_monto = str_replace('.','',$ld_subtotcon);
			$li_monto = str_replace(',','.',$li_monto);
			$ld_totalgasto = $ld_totalgasto + $li_monto;
			$li_monto = str_replace('.','',$ld_carcon);
			$li_monto = str_replace(',','.',$li_monto);
			$ld_totalcargo = $ld_totalcargo + $li_monto;
			$li_monto = str_replace('.','',$ld_totcon);
			$li_monto = str_replace(',','.',$li_monto);
			$ld_totalgeneral = $ld_totalgeneral + $li_monto;			
			$ls_spgcuenta = $io_funciones_sep->uf_obtenervalor("txtspgcuenta".$li_fila,"");		
			///---------campos relacionados al gasto----------------------------------------
			$ls_codprocon=trim($io_funciones_sep->uf_obtenervalor("txtcodgas".$li_fila,""));
			$ls_cuentacon=trim($io_funciones_sep->uf_obtenervalor("txtcodspg".$li_fila,"")); 
			$ls_estclacon=trim($io_funciones_sep->uf_obtenervalor("txtstatus".$li_fila,""));
			//-------------------------------------------------------------------------------	
			$lo_object[$li_fila][1]="<input name=txtcodcon".$li_fila."     type=text id=txtcodcon".$li_fila."     class=sin-borde   size=15 value='".$ls_codcon."'     style=text-align:center readonly>
									 <input type=hidden name=txtcodgas".$li_fila." id=txtcodgas".$li_fila."  value='".$ls_codprocon."' readonly>
									 <input type=hidden name=txtcodspg".$li_fila." id=txtcodspg".$li_fila."  value='".$ls_cuentacon."' readonly>
									 <input type=hidden name=txtstatus".$li_fila." id=txtstatus".$li_fila."  value='".$ls_estclacon."' readonly>";
			$lo_object[$li_fila][2]="<input name=txtdencon".$li_fila."     type=text id=txtdencon".$li_fila."     class=sin-borde   size=30 value='".$ls_dencon."'     style=text-align:left   readonly>";
			$lo_object[$li_fila][3]="<input name=txtcancon".$li_fila."     type=text id=txtcancon".$li_fila."     class=sin-borde   size=9  value='".$ld_cancon."'     style=text-align:right onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('O','".$li_fila."');>";
			$lo_object[$li_fila][4]="<input name=txtprecon".$li_fila."     type=text id=txtprecon".$li_fila."     class=sin-borde   size=15 value='".$ld_precon."'     style=text-align:right  onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('O','".$li_fila."');>";
			$lo_object[$li_fila][5]="<input name=txtsubtotcon".$li_fila."  type=text id=txtsubtotcon".$li_fila."  class=sin-borde   size=15 value='".$ld_subtotcon."'  style=text-align:right  readonly>";
			$lo_object[$li_fila][6]="<input name=txtcarcon".$li_fila."     type=text id=txtcarcon".$li_fila."     class=sin-borde   size=10 value='".$ld_carcon."'     style=text-align:right  readonly>";
			$lo_object[$li_fila][7]="<input name=txttotcon".$li_fila."     type=text id=txttotcon".$li_fila."     class=sin-borde   size=15 value='".$ld_totcon."'     style=text-align:right  readonly>".
									"<input type=hidden name=txtspgcuenta".$li_fila." id=txtspgcuenta value='".$ls_spgcuenta."'>";
			if($li_fila==$ai_total)// si el la última fila no pinto el eliminar
			{
				$lo_object[$li_fila][8]="";
				if ($ls_estmodpart==1)
				{
					$lo_object[$li_fila][9]="";
				}
			}
			else
			{
				$lo_object[$li_fila][8]="<a href=javascript:ue_delete_conceptos('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=10 border=0></a>";
				if ($ls_estmodpart==1)
				{
					$lo_object[$li_fila][9]="<a href=javascript:ue_cambiar_partida_conceptos('".$li_fila."','".$ls_codprocon."','".$ls_cuentacon."','".$ls_estclacon."','4');><img src=../shared/imagebank/mas.gif title=Cambiar width=14 height=14 border=0></a>";
				}
			}
		}
		if($ls_estdifiva!="1")
		{
			print "<p>&nbsp;</p>";
			print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
			print "    <tr>";
			print "		<td height='22' colspan='3' align='left'><a href='javascript:ue_catalogoconceptos();'><img src='../shared/imagebank/tools/nuevo.gif' width='20' height='20' border='0' title='Agregar Detalle Conceptos'>Agregar Detalle Conceptos</a></td>";
			print "    </tr>";
			print "  </table>";
		}
		$io_grid->makegrid($ai_total,$lo_title,$lo_object,840,"Detalle de Conceptos","gridconceptos");
		if($ls_estdifiva=="1")
		{
			print "<p>&nbsp;</p>";
			print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
			print "    <tr>";
			print "		<td height='22' colspan='3' align='left'><a href='javascript:ue_catalogocargos();'><img src='../shared/imagebank/tools/nuevo.gif' width='20' height='20' border='0' title='Agregar Diferencial IVA'>Agregar Diferencial Cargo</a></td>";
			print "    </tr>";
			print "  </table>";
		}
	}// end function uf_print_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_creditos($as_titulo,$ai_total,$as_cargarcargos,$as_tipconpro,$as_tipo,$autcan=false)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_creditos
		//		   Access: private
		//	    Arguments: ai_total  // Total de filas a imprimir
		//                 as_titulo // Titulo de bienes o servicios
		//                 as_cargarcargos // Si cargamos los cargos ó solo pintamos
		//                 as_tipo // Tipo de SEP si es de bienes ó de servicios
		//	  Description: Método que imprime el grid de créditos y busca los creditos de un Bien, un Servicio ò un concepto
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $la_cuentacargo, $li_cuenta, $io_solicitud,$ls_coduniadm, $arr_cargosprocesados, $ls_codestpre, $ls_estclauni,$ls_codtipsep,$ls_cambioest;
        $li_f=0;
		// Titulos del Grid
		$lo_title[1]=$as_titulo;
		$lo_title[2]="C&oacute;digo";
		$lo_title[3]="Denominaci&oacute;n";
		$lo_title[4]="Base Imponible";
		$lo_title[5]="Monto del Cargo";
		$lo_title[6]="Sub-Total";
        $ls_estdifiva=$io_solicitud->uf_validar_diferencial_iva($ls_codtipsep);//verifica si tiene permiso para modificar las partidas
		$ls_estmodpart=$io_solicitud->uf_validar_cambio_imputacion();//verifica si tiene permiso para modificar las partidas
		if ($ls_estmodpart==1)
		{
			$lo_title[7]="";
		}	
		$lo_object[0]="";		
		// Recorrido de el grid de Cargos
		if($as_cargarcargos=="0")
		{	// Si se deben cargar los cargos Buscamos el Código del último Bien cargado 

			for($li_fila=1;$li_fila<=$ai_total;$li_fila++)
			{
				$ls_codservic  = $io_funciones_sep->uf_obtenervalor("txtcodservic".$li_fila,"");
				$ls_codcar	   = $io_funciones_sep->uf_obtenervalor("txtcodcar".$li_fila,"");
				$ls_dencar	   = $io_funciones_sep->uf_obtenervalor("txtdencar".$li_fila,"");
				$li_bascar	   = $io_funciones_sep->uf_obtenervalor("txtbascar".$li_fila,"");
				$li_moncar	   = $io_funciones_sep->uf_obtenervalor("txtmoncar".$li_fila,"");
				$li_subcargo   = $io_funciones_sep->uf_obtenervalor("txtsubcargo".$li_fila,"");
				$ls_spg_cuenta = $io_funciones_sep->uf_obtenervalor("cuentacargo".$li_fila,"");
				$ls_formula    = $io_funciones_sep->uf_obtenervalor("formulacargo".$li_fila,"");
				$ls_codpro	   = trim($io_funciones_sep->uf_obtenervalor("txtcodgascre".$li_fila,"")); 
				$ls_cuenta	   = trim($io_funciones_sep->uf_obtenervalor("txtcodspgcre".$li_fila,""));
				$ls_estcla	   = trim($io_funciones_sep->uf_obtenervalor("txtstatuscre".$li_fila,""));
				if ($autcan)
				{
					if ($li_moncar!="0,00") 
					{
						$li_f++;
						$lo_object[$li_f][1]="<input name=txtcodservic".$li_f." type=text id=txtcodservic".$li_f." class=sin-borde  size=22   style=text-align:center value='".$ls_codservic."' readonly>
											 <input type=hidden name=txtcodgascre".$li_f." id=txtcodgascre".$li_f."  value='".$ls_codpro."' readonly>
											 <input type=hidden name=txtcodspgcre".$li_f." id=txtcodspgcre".$li_f."  value='".$ls_cuenta."' readonly>
											 <input type=hidden name=txtstatuscre".$li_f." id=txtstatuscre".$li_f."  value='".$ls_estcla."' readonly>";
						$lo_object[$li_f][2]="<input name=txtcodcar".$li_f."    type=text id=txtcodcar".$li_f."    class=sin-borde  size=10   style=text-align:center value='".$ls_codcar."' readonly>";
						$lo_object[$li_f][3]="<input name=txtdencar".$li_f."    type=text id=txtdencar".$li_f."    class=sin-borde  size=36   style=text-align:left   value='".$ls_dencar."' readonly>";
						$lo_object[$li_f][4]="<input name=txtbascar".$li_f."    type=text id=txtbascar".$li_f."    class=sin-borde  size=17   style=text-align:right  value='".$li_bascar."' readonly>";
						$lo_object[$li_f][5]="<input name=txtmoncar".$li_f."    type=text id=txtmoncar".$li_f."    class=sin-borde  size=13   style=text-align:right  value='".$li_moncar."' readonly>";
						$lo_object[$li_f][6]="<input name=txtsubcargo".$li_f."  type=text id=txtsubcargo".$li_f."  class=sin-borde  size=17   style=text-align:right  value='".$li_subcargo."' readonly>".
											"<input name=cuentacargo".$li_f."  type=hidden id=cuentacargo".$li_f."  value='".$ls_spg_cuenta."'>".
											"<input name=formulacargo".$li_f." type=hidden id=formulacargo".$li_f." value='".$ls_formula."'>
											 <input name=codcargo".$li_f." type=hidden id=codcargo".$li_f." value='".$ls_codcar."'>";
						if ($ls_estmodpart==1)
						{
							$lo_object[$li_f][7]="<a href=javascript:ue_cambiar_creditos('".$li_f."','".$ls_codpro."','".$ls_cuenta."','".$ls_estcla."','2');><img src=../shared/imagebank/mas.gif title=Cambiar width=14 height=14 border=0></a>";
						}
						
					}
				}
				else
				{
					$lo_object[$li_fila][1]="<input name=txtcodservic".$li_fila." type=text id=txtcodservic".$li_fila." class=sin-borde  size=22   style=text-align:center value='".$ls_codservic."' readonly>
											 <input type=hidden name=txtcodgascre".$li_fila." id=txtcodgascre".$li_fila."  value='".$ls_codpro."' readonly>
											 <input type=hidden name=txtcodspgcre".$li_fila." id=txtcodspgcre".$li_fila."  value='".$ls_cuenta."' readonly>
											 <input type=hidden name=txtstatuscre".$li_fila." id=txtstatuscre".$li_fila."  value='".$ls_estcla."' readonly>";
					$lo_object[$li_fila][2]="<input name=txtcodcar".$li_fila."    type=text id=txtcodcar".$li_fila."    class=sin-borde  size=10   style=text-align:center value='".$ls_codcar."' readonly>";
					$lo_object[$li_fila][3]="<input name=txtdencar".$li_fila."    type=text id=txtdencar".$li_fila."    class=sin-borde  size=36   style=text-align:left   value='".$ls_dencar."' readonly>";
					$lo_object[$li_fila][4]="<input name=txtbascar".$li_fila."    type=text id=txtbascar".$li_fila."    class=sin-borde  size=17   style=text-align:right  value='".$li_bascar."' readonly>";
					$lo_object[$li_fila][5]="<input name=txtmoncar".$li_fila."    type=text id=txtmoncar".$li_fila."    class=sin-borde  size=13   style=text-align:right  value='".$li_moncar."' readonly>";
					$lo_object[$li_fila][6]="<input name=txtsubcargo".$li_fila."  type=text id=txtsubcargo".$li_fila."  class=sin-borde  size=17   style=text-align:right  value='".$li_subcargo."' readonly>".
											"<input name=cuentacargo".$li_fila."  type=hidden id=cuentacargo".$li_fila."  value='".$ls_spg_cuenta."'>".
											"<input name=formulacargo".$li_fila." type=hidden id=formulacargo".$li_fila." value='".$ls_formula."'>
											 <input name=codcargo".$li_fila." type=hidden id=codcargo".$li_fila." value='".$ls_codcar."'>";
					if ($ls_estmodpart==1)
					{
						$lo_object[$li_fila][7]="<a href=javascript:ue_cambiar_creditos('".$li_fila."','".$ls_codpro."','".$ls_cuenta."','".$ls_estcla."','2');><img src=../shared/imagebank/mas.gif title=Cambiar width=14 height=14 border=0></a>";
					}
				}
			}
		}
		else
		{	// Si se deben cargar los cargos Buscamos el Código del último Bien cargado 
			// y obtenemos los cargos de dicho Bien
		  if($as_tipconpro!="F")
		  {  
				$ai_total = count((array)$arr_cargosprocesados);
				for($li_fila=0;$li_fila<=$ai_total;$li_fila++)	  
				{
					$lb_existecargo  = true;
					$ls_codservic    = $arr_cargosprocesados[$li_fila]['codigo'];
					$ls_codcar       = $arr_cargosprocesados[$li_fila]['codcar'];
					$ls_dencar       = $arr_cargosprocesados[$li_fila]['dencar'];
					$ls_spg_cuenta   = trim($arr_cargosprocesados[$li_fila]['spg_cuenta']);
					$ls_formula      = $arr_cargosprocesados[$li_fila]['formula'];
					$li_bascar       = number_format($arr_cargosprocesados[$li_fila]['subtotal'],'2',',','.');
					$li_moncar       = number_format($arr_cargosprocesados[$li_fila]['cargo'],'2',',','.');
					$li_subcargo     = number_format($arr_cargosprocesados[$li_fila]['total'],'2',',','.');
					$ls_existecuenta = $arr_cargosprocesados[$li_fila]['existecuenta'];
					if($ls_spg_cuenta!="")
					{// Si la cuenta presupuestaria es diferente de blanco llenamos un arreglo de cuentas
						$la_cuentacargo[$li_cuenta]["cargo"]=$ls_codcar;
						$la_cuentacargo[$li_cuenta]["cuenta"]=$ls_spg_cuenta;
						$ls_estceniva=$_SESSION["la_empresa"]["estceniva"];
						if($ls_estceniva=="1")
						{
							require_once("sigesp_sep_c_solicitud.php");
							$io_solicitud=new sigesp_sep_c_solicitud("../../");
							$arrResultado= $io_solicitud->uf_load_estructura_central($ls_coduniadm,$ls_codestprocen1,$ls_codestprocen2,$ls_codestprocen3,$ls_codestprocen4,$ls_codestprocen5,$ls_esclacen);
							$lb_valido=$arrResultado["lb_valido"];
							$ls_codestprocen1=$arrResultado["as_codestprocen1"];
							$ls_codestprocen2=$arrResultado["as_codestprocen2"];
							$ls_codestprocen3=$arrResultado["as_codestprocen3"];
							$ls_codestprocen4=$arrResultado["as_codestprocen4"];
							$ls_codestprocen5=$arrResultado["as_codestprocen5"];
							$ls_esclacen=$arrResultado["as_esclacen"];
							
							$ls_codestprocen=$ls_codestprocen1.$ls_codestprocen2.$ls_codestprocen3.$ls_codestprocen4.$ls_codestprocen5;
							$la_cuentacargo[$li_cuenta]["programatica"]=$ls_codestprocen;
							$la_cuentacargo[$li_cuenta]["estcla"]=$ls_esclacen;	
							$la_cuentacargo[$li_cuenta]["monto"]=$arr_cargosprocesados[$li_fila]['cargo'];	
						}
						else
						{
							if($ls_cambioest=="1")
							{
								if($ls_existecuenta==0)
								{
									$la_cuentacargo[$li_cuenta]["programatica"]="";
									$la_cuentacargo[$li_cuenta]["estcla"]=$ls_estcla;	
									$la_cuentacargo[$li_cuenta]["monto"]=$arr_cargosprocesados[$li_fila]['cargo'];	
								}
								else
								{
									$la_cuentacargo[$li_cuenta]["programatica"]=$arr_cargosprocesados[$li_fila]['programatica'];	
									$la_cuentacargo[$li_cuenta]["estcla"]=$arr_cargosprocesados[$li_fila]['estcla'];						
									$la_cuentacargo[$li_cuenta]["monto"]=$arr_cargosprocesados[$li_fila]['cargo'];	
								}
							}
							else
							{
								if($ls_existecuenta==0)
								{
									$la_cuentacargo[$li_cuenta]["programatica"]="";
									$la_cuentacargo[$li_cuenta]["estcla"]=$ls_estcla;	
									$la_cuentacargo[$li_cuenta]["monto"]=$arr_cargosprocesados[$li_fila]['cargo'];	
								}
								else
								{
									$la_cuentacargo[$li_cuenta]["programatica"]=$ls_codestpre;
									$la_cuentacargo[$li_cuenta]["estcla"]=$ls_estclauni;						
									$la_cuentacargo[$li_cuenta]["monto"]=$arr_cargosprocesados[$li_fila]['cargo'];	
								}
							}
						}
						$li_cuenta++;
					}
					$ls_estcla ="";
					$li_cuenta=count((array)$la_cuentacargo);
					for ($li_fila2=1;($li_fila2<$li_cuenta);$li_fila2++)
					{
						$ls_cuenta       = trim($la_cuentacargo[$li_fila2]["cuenta"]);
						$ls_programatica = trim($la_cuentacargo[$li_fila2]["programatica"]);
						$ls_estcla       = $la_cuentacargo[$li_fila2]["estcla"];						
					}
					$ls_codpro=$ls_programatica; 
					$ls_cuenta=$ls_cuenta;
					$ls_estcla=$ls_estcla;
					$li_i = $li_fila+1;
					$lo_object[$li_i][1]="<input name=txtcodservic".$li_i." type=text id=txtcodservic".$li_i." class=sin-borde  size=22   style=text-align:center value='".$ls_codservic."' readonly>
											  <input type=hidden name=txtcodgascre".$li_i." id=txtcodgascre".$li_i."  value='".$ls_codpro."' readonly>
											  <input type=hidden name=txtcodspgcre".$li_i." id=txtcodspgcre".$li_i."  value='".$ls_cuenta."' readonly>
											  <input type=hidden name=txtstatuscre".$li_i." id=txtstatuscre".$li_i."  value='".$ls_estcla."' readonly>";
					$lo_object[$li_i][2]="<input name=txtcodcar".$li_i."    type=text id=txtcodcar".$li_i."    class=sin-borde  size=10   style=text-align:center value='".$ls_codcar."' readonly>";
					$lo_object[$li_i][3]="<input name=txtdencar".$li_i."    type=text id=txtdencar".$li_i."    class=sin-borde  size=36   style=text-align:left   value='".$ls_dencar."' readonly>";
					$lo_object[$li_i][4]="<input name=txtbascar".$li_i."    type=text id=txtbascar".$li_i."    class=sin-borde  size=17   style=text-align:right  value='".$li_bascar."' readonly>";
					$lo_object[$li_i][5]="<input name=txtmoncar".$li_i."    type=text id=txtmoncar".$li_i."    class=sin-borde  size=13   style=text-align:right  value='".$li_moncar."' readonly>";
					$lo_object[$li_i][6]="<input name=txtsubcargo".$li_i."  type=text id=txtsubcargo".$li_i."  class=sin-borde  size=17   style=text-align:right  value='".$li_subcargo."' readonly>".
											 "<input name=cuentacargo".$li_i."  type=hidden id=cuentacargo".$li_i."  value='".$ls_spg_cuenta."'>".
											 "<input name=formulacargo".$li_i." type=hidden id=formulacargo".$li_i." value='".$ls_formula."'>
											  <input name=codcargo".$li_i." type=hidden id=codcargo".$li_i." value='".$ls_codcar."'>";
					if ($ls_estmodpart==1)
					{
						$lo_object[$li_i][7]="<a href=javascript:ue_cambiar_creditos('".$li_i."','".$ls_codpro."','".$ls_cuenta."','".$ls_estcla."','2');><img src=../shared/imagebank/mas.gif title=Cambiar width=14 height=14 border=0></a>";
					}
				}
			}
		}		
		print "<p>&nbsp;</p>";
		if ($autcan)
		{
			$ai_total=$li_f;
		}
		$io_grid->makegrid($ai_total,$lo_title,$lo_object,840,"Cr&eacute;ditos","gridcreditos");
		unset($io_solicitud);		
		print "<table width='840' height='22' border='0' align='center' cellpadding='0' cellspacing='0'>";
		print "        <tr>";
		print "          <td width='175' height='22' align='right'><div align='left'><input name='btncrear' type='button' class='boton' id='btncerrar' value='Crear Asiento' onClick='javascript: ue_crear_asiento();'></div></td>";
		print "        </tr>";
		print "</table>";
	}// end function uf_print_creditos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cuentas_gasto($ai_total,$as_tipo,$li_loncodestpro1,$li_loncodestpro2,$li_loncodestpro3,$li_loncodestpro4,$li_loncodestpro5)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_cuentas_gasto
		//		   Access: private
		//	    Arguments: ai_total  // Total de filas a imprimir
		//                 as_tipo // Tipo de SEP si es de bienes ó de servicios
		//	  Description: Método que imprime el grid de las cuentas presupuestarias del Gasto
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $la_cuentacargo;
		require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
		$io_dscuentas=new class_datastore();		
		// Titulos el Grid
		$lo_title[1]="Estructura Programatica";
		$lo_title[2]="Cuenta";
		$lo_title[3]="Monto";
		$ls_codpro="";
		// Recorrido del Grid de Cuentas Presupuestarias
		for($li_fila=1;$li_fila<=$ai_total;$li_fila++)
		{
			$ls_codpro=trim($io_funciones_sep->uf_obtenervalor("txtcodprogas".$li_fila,""));
			$ls_cuenta=trim($io_funciones_sep->uf_obtenervalor("txtcuentagas".$li_fila,""));
			$ls_estcla=trim($io_funciones_sep->uf_obtenervalor("txtestclagas".$li_fila,""));
			
			$li_moncue=trim($io_funciones_sep->uf_obtenervalor("txtmoncuegas".$li_fila,"0,00"));
			$li_moncue=str_replace(".","",$li_moncue);
			$li_moncue=str_replace(",",".",$li_moncue);							
			if($ls_cuenta!="")
			{
				$io_dscuentas->insertRow("codprogas",$ls_codpro);	
				$io_dscuentas->insertRow("estclagas",$ls_estcla);
				$io_dscuentas->insertRow("cuentagas",$ls_cuenta);			
				$io_dscuentas->insertRow("moncuegas",$li_moncue);			
			}
		}
		// Agrupamos las cuentas por programatica y cuenta
		$io_dscuentas->group_by(array('0'=>'codprogas','1'=>'cuentagas'),array('0'=>'moncuegas'),'moncuegas');
		$li_total=$io_dscuentas->getRowCount('codprogas');
		// Recorremos el data stored de cuentas que se lleno y se agrupo anteriormente
		for($li_fila=1;$li_fila<=$li_total;$li_fila++)
		{
			$ls_codpro=$io_dscuentas->getValue('codprogas',$li_fila);
			$ls_cuenta=$io_dscuentas->getValue('cuentagas',$li_fila);
			$ls_estcla=$io_dscuentas->getValue('estclagas',$li_fila);
			$li_moncue=number_format($io_dscuentas->getValue('moncuegas',$li_fila),2,",",".");
			$ls_codest1=substr($ls_codpro,0,$li_loncodestpro1);
			$ls_codest2=substr($ls_codpro,$li_loncodestpro1,$li_loncodestpro2);
			$ls_codest3=substr($ls_codpro,$li_loncodestpro1+$li_loncodestpro2,$li_loncodestpro3);
			$ls_codest4=substr($ls_codpro,$li_loncodestpro1+$li_loncodestpro2+$li_loncodestpro3,$li_loncodestpro4);
			$ls_codest5=substr($ls_codpro,$li_loncodestpro1+$li_loncodestpro2+$li_loncodestpro3+$li_loncodestpro4,$li_loncodestpro5);
			$ls_programatica=$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5;
			
			if($ls_cuenta!="")
			{
				$lo_object[$li_fila][1]="<input name=txtprogramaticagas".$li_fila." type=text id=txtprogramaticagas".$li_fila." class=sin-borde  style=text-align:center size=45 value='".$ls_programatica."' readonly>"."<input name=txtestclagas".$li_fila."  type=hidden size='2' id=txtestclagas".$li_fila."  value='".$ls_estcla."'>";
				$lo_object[$li_fila][2]="<input name=txtcuentagas".$li_fila." type=text id=txtcuentagas".$li_fila." class=sin-borde  style=text-align:center size=25 value='".$ls_cuenta."' readonly>";
				$lo_object[$li_fila][3]="<input name=txtmoncuegas".$li_fila." type=text id=txtmoncuegas".$li_fila." class=sin-borde  style=text-align:right  size=25 onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_moncue."' ><input name=txtcodprogas".$li_fila."  type=hidden id=txtcodprogas".$li_fila."  value='".$ls_programatica."'>";
				/*$lo_object[$li_fila][4]="<a href=javascript:ue_delete_cuenta_gasto('".$li_fila."','".$as_tipo."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>".
										"<input name=txtcodprogas".$li_fila."  type=hidden id=txtcodprogas".$li_fila."  value='".$ls_programatica."'>";*/
			}
		}
		$ai_total=$li_total+1;
		$lo_object[$ai_total][1]="<input name=txtprogramaticagas".$ai_total." type=text id=txtprogramaticagas".$ai_total." class=sin-borde  style=text-align:center size=45 value='' readonly>"."<input name=txtestclagas".$li_fila."  type=hidden size='2' id=txtestclagas".$li_fila."  value=''>";;
		$lo_object[$ai_total][2]="<input name=txtcuentagas".$ai_total."       type=text id=txtcuentagas".$ai_total."       class=sin-borde  style=text-align:center size=25 value='' readonly>";
		$lo_object[$ai_total][3]="<input name=txtmoncuegas".$ai_total."       type=text id=txtmoncuegas".$ai_total."       class=sin-borde  style=text-align:right  size=25 value='' readonly>";
		$lo_object[$ai_total][4]="<input name=txtcodprogas".$ai_total."       type=hidden id=txtcodprogas".$ai_total."  value=''>";        

		print "<p>&nbsp;</p>";
		print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0' style='display:none'> ";
		print "    <tr>";
		print "      <td  align='left'><a href='javascript:ue_catalogo_cuentas_spg();'><img src='../shared/imagebank/tools/nuevo.gif' width='20' height='20' border='0' title='Agregar Cuenta'>Agregar Cuenta</a>&nbsp;&nbsp;</td>";
		print "    </tr>";
		print "  </table>";
		$io_grid->makegrid($ai_total,$lo_title,$lo_object,840,"Cuentas","gridcuentas");
		unset($io_dscuentas);
	}// end function uf_print_cuentas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cuentas_cargo($ai_total,$as_cargarcargos,$as_tipo,$li_loncodestpro1,$li_loncodestpro2,$li_loncodestpro3,$li_loncodestpro4,$li_loncodestpro5)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_cuentas
		//		   Access: private
		//	    Arguments: ai_total  // Total de filas a imprimir
		//                 as_cargarcargos // Si cargamos los cargos ó solo pintamos
		//                 as_tipo // Tipo de SEP si es de bienes ó de servicios
		//	  Description: Método que imprime el grid de las cuentas presupuestarias de los cargos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $la_cuentacargo;
		require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
		$io_dscuentas=new class_datastore();		
		// Titulos el Grid
		$lo_title[1]="Cargo";
		$lo_title[2]="Estructura Programatica";
		$lo_title[3]="Cuenta";
		$lo_title[4]="Monto";
		$ls_codpro="";
		// Recorrido del Grid de Cuentas Presupuestarias del Cargo
		for($li_fila=1;$li_fila<=$ai_total;$li_fila++)
		{
			$ls_cargo  = trim($io_funciones_sep->uf_obtenervalor("txtcodcargo".$li_fila,""));
			$ls_estcla = trim($io_funciones_sep->uf_obtenervalor("txtestclacar".$li_fila,"")); 
			$ls_codpro = trim($io_funciones_sep->uf_obtenervalor("txtcodprocar".$li_fila,""));
			$ls_cuenta = trim($io_funciones_sep->uf_obtenervalor("txtcuentacar".$li_fila,""));
			$li_moncue = trim($io_funciones_sep->uf_obtenervalor("txtmoncuecar".$li_fila,"0,00"));
			$li_moncue = str_replace(".","",$li_moncue);
			$li_moncue = str_replace(",",".",$li_moncue);							
			if($ls_cuenta!="")
			{
				$io_dscuentas->insertRow("codcargo",$ls_cargo);			
				$io_dscuentas->insertRow("codprocar",$ls_codpro);			
				$io_dscuentas->insertRow("cuentacar",$ls_cuenta);			
				$io_dscuentas->insertRow("moncuecar",$li_moncue);
				$io_dscuentas->insertRow("estclacar",$ls_estcla);			
			}			
		}
		if($as_cargarcargos=="1")
		{	// si los cargos se deben cargar recorremos el arreglo de cuentas
			// que se lleno con los cargos 
			$li_cuenta=count((array)$la_cuentacargo)-1;
			for($li_fila=1;($li_fila<=$li_cuenta);$li_fila++)
			{
				$ls_cargo        = trim($la_cuentacargo[$li_fila]["cargo"]); 
				$ls_cuenta       = trim($la_cuentacargo[$li_fila]["cuenta"]);
				$ls_programatica = trim($la_cuentacargo[$li_fila]["programatica"]);
				$ls_estcla       = $la_cuentacargo[$li_fila]["estcla"];
				$li_moncue       = $la_cuentacargo[$li_fila]["monto"];
				if($ls_cuenta!="")
				{
					$io_dscuentas->insertRow("codcargo",$ls_cargo);			
					$io_dscuentas->insertRow("codprocar",$ls_programatica);	
					$io_dscuentas->insertRow("estclacar",$ls_estcla);		
					$io_dscuentas->insertRow("cuentacar",$ls_cuenta);			
					$io_dscuentas->insertRow("moncuecar",$li_moncue);
				}			
			}
		}
		// Agrupamos las cuentas por programatica y cuenta
		$io_dscuentas->group_by(array('0'=>'codcargo','1'=>'codprocar','2'=>'cuentacar'),array('0'=>'moncuecar'),'moncuecar');
		$li_total=$io_dscuentas->getRowCount('codcargo');	
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		switch($ls_modalidad)
		{
			case "1": // Modalidad por Proyecto
				$li_len1=20;
				$li_len2=6;
				$li_len3=3;
				$li_len4=2;
				$li_len5=2;
				break;
				
			case "2": // Modalidad por Presupuesto
				$li_len1=2;
				$li_len2=2;
				$li_len3=2;
				$li_len4=2;
				$li_len5=2;
				break;
		}
		// Recorremos el data stored de cuentas que se lleno y se agrupo anteriormente
		for($li_fila=1;$li_fila<=$li_total;$li_fila++)
		{
			$ls_cargo     = $io_dscuentas->getValue('codcargo',$li_fila);
			$ls_codpro    = $io_dscuentas->getValue('codprocar',$li_fila);
			$ls_cuenta    = $io_dscuentas->getValue('cuentacar',$li_fila);
			$ls_estclacar = $io_dscuentas->getValue('estclacar',$li_fila);
			$li_moncue    = number_format($io_dscuentas->getValue('moncuecar',$li_fila),2,",",".");
			
			$ls_codest1=substr($ls_codpro,0,$li_loncodestpro1); 
			$ls_codest2=substr($ls_codpro,$li_loncodestpro1,$li_loncodestpro2);
			$ls_codest3=substr($ls_codpro,$li_loncodestpro1+$li_loncodestpro2,$li_loncodestpro3);
			$ls_codest4=substr($ls_codpro,$li_loncodestpro1+$li_loncodestpro2+$li_loncodestpro3,$li_loncodestpro4);
			$ls_codest5=substr($ls_codpro,$li_loncodestpro1+$li_loncodestpro2+$li_loncodestpro3+$li_loncodestpro4,$li_loncodestpro5);
			$ls_programatica=$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5;
			if($ls_cuenta!="")
			{
				$lo_object[$li_fila][1]="<input name=txtcodcargo".$li_fila." type=text id=txtcodcargo".$li_fila." class=sin-borde  style=text-align:center size=10 value='".$ls_cargo."' readonly>";
				$lo_object[$li_fila][2]="<input name=txtprogramaticacar".$li_fila." type=text id=txtprogramaticacar".$li_fila." class=sin-borde  style=text-align:center size=45 value='".$ls_programatica."' readonly>".
										"<input name=txtestclacar".$li_fila."       type=hidden size='2' id=txtestclacar".$li_fila."  value='".$ls_estclacar."'>";
				$lo_object[$li_fila][3]="<input name=txtcuentacar".$li_fila." type=text id=txtcuentacar".$li_fila." class=sin-borde  style=text-align:center size=25 value='".$ls_cuenta."' readonly>";
				$lo_object[$li_fila][4]="<input name=txtmoncuecar".$li_fila." type=text id=txtmoncuecar".$li_fila." class=sin-borde  style=text-align:right  size=25 onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_moncue."' >".
										"<input name=txtcodprocar".$li_fila."  type=hidden id=txtcodprocar".$li_fila."  value='".$ls_codpro."'>";
			}
		}
		$ai_total=$li_total+1;
		$lo_object[$ai_total][1]="<input name=txtcodcargo".$ai_total." type=text id=txtcodcargo".$ai_total." class=sin-borde  style=text-align:center size=10 value='' readonly>";
		$lo_object[$ai_total][2]="<input name=txtprogramaticacar".$ai_total." type=text id=txtprogramaticacar".$ai_total." class=sin-borde  style=text-align:center size=45 value='' readonly>"."<input name=txtestclacar".$li_fila."  type=hidden size='2' id=txtestclacar".$li_fila."  value=''>";
		$lo_object[$ai_total][3]="<input name=txtcuentacar".$ai_total."       type=text id=txtcuentacar".$ai_total."       class=sin-borde  style=text-align:center size=25 value='' readonly>";
		$lo_object[$ai_total][4]="<input name=txtmoncuecar".$ai_total."       type=text id=txtmoncuecar".$ai_total."       class=sin-borde  style=text-align:right  size=25 value='' readonly>";
		$lo_object[$ai_total][5]="<input name=txtcodprocar".$ai_total."       type=hidden id=txtcodprocar".$ai_total."  value=''>";        

		print "<p>&nbsp;</p>";
		print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0' style='display:none' >";
		print "    <tr>";
		print "      <td  align='left'><a href='javascript:ue_catalogo_cuentas_cargos();'><img src='../shared/imagebank/tools/nuevo.gif' width='20' height='20' border='0' title='Agregar Cuenta'>Agregar Cuenta Cargos</a>&nbsp;&nbsp;</td>";
		print "    </tr>";
		print "  </table>";
		$io_grid->makegrid($ai_total,$lo_title,$lo_object,840,"Cuentas Cargos","gridcuentascargos");
		unset($io_dscuentas);
	}// end function uf_print_cuentas_cargo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total($ai_totrowitem,$as_tipsol)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_total
		//		   Access: private
		//	    Arguments: ai_subtotal // Valor del subtotal
		//				   ai_cargos // Valor total de los cargos
		//				   ai_total // Total de la solicitu de pago
		//	  Description: Método que imprime los totales de la SEP
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_sep, $ld_totalgasto, $ld_totalcargo, $ld_totalgeneral;
		$ld_totalgasto=number_format($ld_totalgasto,2,',','.');
		$ld_totalcargo=number_format($ld_totalcargo,2,',','.');
		$ld_totalgeneral=number_format($ld_totalgeneral,2,',','.');
		print "<table width='840' height='116' border='0' align='center' cellpadding='0' cellspacing='0' class='formato-blanco'>";
		print "        <tr class='titulo-celdanew'>";
		print "          <td height='22' colspan='4'><div align='center'>Totales</div></td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td width='128' height='13'>&nbsp;</td>";
		print "          <td width='113' height='13' align='left'></td>";
		print "          <td width='368' height='13' align='right'><div align='right'></div></td>";
		print "          <td width='239' height='13' align='left'>&nbsp;</td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td height='22'>&nbsp;</td>";
		print "          <td height='22' align='left'></td>";
		print "          <td height='22' align='right'><strong>Subtotal&nbsp;&nbsp;</strong></td>";
		print "          <td height='22'><input name='txtsubtotal'  type='text' class='titulo-conect' id='txtsubtotal' style='text-align:right' value='".$ld_totalgasto."' size='30' maxlength='25' readonly align='right'></td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td height='22'>&nbsp;</td>";
		print "          <td height='22' align='left'></td>";
		print "          <td height='22' align='right'><div align='right'><strong>Otros Cr&eacute;ditos&nbsp;&nbsp;</strong></div></td>";
		print "          <td height='22'><input name='txtcargos' type='text' class='titulo-conect' id='txtcargos' style='text-align:right' value='".$ld_totalcargo."' size='30' maxlength='25' readonly align='right'></td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td height='22'>&nbsp;</td>";
		print "          <td height='22'>&nbsp;</td>";
		print "          <td height='22' align='right'><div align='right'><strong>Total General&nbsp;&nbsp;</strong></div></td>";
		print "          <td height='22'><input name='txttotal' type='text' class='titulo-conect' id='txttotal' style='text-align:right' value='".$ld_totalgeneral."' size='30' maxlength='25' readonly align='right'></td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td height='13' colspan='4'>&nbsp;</td>";
		print "			</tr>";
		print "</table>";
	}// end function uf_print_total
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_total($ai_subtotal,$ai_cargos,$ai_total)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_total
		//		   Access: private
		//	    Arguments: ai_subtotal // Valor del subtotal
		//				   ai_cargos // Valor total de los cargos
		//				   ai_total // Total de la solicitu de pago
		//	  Description: Método que imprime los totales de la SEP
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		print "<table width='840' height='116' border='0' align='center' cellpadding='0' cellspacing='0' class='formato-blanco'>";
		print "        <tr class='titulo-celdanew'>";
		print "          <td height='22' colspan='4'><div align='center'>Totales</div></td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td width='128' height='13'>&nbsp;</td>";
		print "          <td width='113' height='13' align='left'></td>";
		print "          <td width='368' height='13' align='right'><div align='right'></div></td>";
		print "          <td width='239' height='13' align='left'>&nbsp;</td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td height='22'>&nbsp;</td>";
		print "          <td height='22' align='left'></td>";
		print "          <td height='22' align='right'><strong>Subtotal&nbsp;&nbsp;</strong></td>";
		print "          <td height='22'><input name='txtsubtotal'  type='text' class='titulo-conect' id='txtsubtotal' style='text-align:right' value='".$ai_subtotal."' size='30' maxlength='25' readonly align='right'></td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td height='22'>&nbsp;</td>";
		print "          <td height='22' align='left'></td>";
		print "          <td height='22' align='right'><div align='right'><strong>Otros Cr&eacute;ditos&nbsp;&nbsp;</strong></div></td>";
		print "          <td height='22'><input name='txtcargos' type='text' class='titulo-conect' id='txtcargos' style='text-align:right' value='".$ai_cargos."' size='30' maxlength='25' readonly align='right'></td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td height='22'>&nbsp;</td>";
		print "          <td height='22'>&nbsp;</td>";
		print "          <td height='22' align='right'><div align='right'><strong>Total General&nbsp;&nbsp;</strong></div></td>";
		print "          <td height='22'><input name='txttotal' type='text' class='titulo-conect' id='txttotal' style='text-align:right' value='".$ai_total."' size='30' maxlength='25' readonly align='right'></td>";
		print "        </tr>";
		print "        <tr>";
		print "          <td height='13' colspan='4'>&nbsp;</td>";
		print "			</tr>";
		print "</table>";
	}// end function uf_print_total
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_bienes($as_numsol, $autcan=false, $tipsep='')
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_bienes
		//		   Access: private
		//	    Arguments: as_numsol  // Numero de solicitud 
		//	  Description: Método que busca los bienes de la solicitud y los imprime
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$i=0;
		global $io_grid, $io_funciones_sep,$li_numdecper;
		$ls_ronlyprecio = '';
		if ($tipsep=='R' && $_SESSION['la_empresa']['blopresep']=='1')
		{
			$ls_ronlyprecio = "readonly";
		}
		
		// Titulos del Grid de Bienes
		$lo_title[1]="C&oacute;digo";
		$lo_title[2]="Denominaci&oacute;n";
		$lo_title[3]="Cantidad";
		if ($autcan) {
			$lo_title[4]="Cantidad Autorizada";
			$i++;
		}
		$lo_title[4+$i]="Modalidad";
		$lo_title[5+$i]="U/M";
		$lo_title[6+$i]="Precio/Unid.";
		$lo_title[7+$i]="Sub-Total";
		$lo_title[8+$i]="Cargos"; 
		$lo_title[9+$i]="Total";
		$lo_title[10+$i]="";
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		$rs_data = $io_solicitud->uf_load_bienes($as_numsol);
		$li_fila=0;
		while($row=$io_solicitud->io_sql->fetch_row($rs_data))	  
		{
			$li_fila=$li_fila+1;
			$ls_codart=$row["codart"];
			$ls_denart=utf8_encode($row["denart"]);
			$ls_unidad=$row["unidad"];
			$ls_medida=$row["denunimed"];
			$li_canart=$row["canart"];
			$li_preart=$row["monpre"];
			$li_totart=$row["monart"];
			$ls_spgcuenta=$row["spg_cuenta"];
			$li_unimed=$row["unimed"];
			$ls_codpro=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
			$ls_estcla=$row["estcla"];
			if($ls_unidad=="M") // Si es al Mayor
			{
				$ls_maysel="selected";
				$ls_detsel="";
				//$li_subtotart=$li_preart*($li_canart*$li_unimed);
				$li_subtotart=$li_preart*$li_canart;
			}
			else // Si es al Detal
			{
				$ls_maysel="";
				$ls_detsel="selected";
				$li_subtotart=$li_preart*$li_canart;
			}
			
			$li_totart=number_format($li_totart,2,".","");
			$li_subtotart=number_format($li_subtotart,2,".","");
			$li_carart=$li_totart-$li_subtotart;
			$li_subtotart=number_format($li_subtotart,2,",",".");
			$li_totart=number_format($li_totart,2,",",".");
			$li_preart=number_format($li_preart,2,",",".");
			$li_carart=number_format($li_carart,2,",",".");
 			if($li_numdecper!="3")
			{
				$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
				$li_canart=number_format($li_canart,2,",",".");
			}
			else
			{
				$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
				$li_canart=number_format($li_canart,3,",",".");
			}
			$lo_object[$li_fila][1]="<input type=text name=txtcodart".$li_fila."    id=txtcodart".$li_fila." class=sin-borde style=text-align:center size=22 value='".$ls_codart."'    readonly>
									 <input type=hidden name=txtcodgas".$li_fila." id=txtcodgas".$li_fila."  value='".$ls_codpro."' readonly>
									 <input type=hidden name=txtcodspg".$li_fila." id=txtcodspg".$li_fila."  value='".$ls_spgcuenta."' readonly>
									 <input type=hidden name=txtstatus".$li_fila." id=txtstatus".$li_fila."  value='".$ls_estcla."' readonly>";
			$lo_object[$li_fila][2]="<input type=text name=txtdenart".$li_fila."    class=sin-borde style=text-align:left   size=20 value='".$ls_denart."'    readonly>";
			if($autcan)
			{
				$lo_object[$li_fila][3]="<input type=text name=txtcanart".$li_fila."    class=sin-borde style=text-align:right size=8  value='".$li_canart."'     readonly>";
				$lo_object[$li_fila][4]="<input type=text name=txtcanartauto".$li_fila."    class=sin-borde style=text-align:right size=8  value='".$li_canart."'     onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('B','".$li_fila."');>";
			}
			else
			{
				$lo_object[$li_fila][3]="<input type=text name=txtcanart".$li_fila."    class=sin-borde style=text-align:right size=8  value='".$li_canart."'     $ls_funcion onBlur=ue_procesar_monto('B','".$li_fila."');>";
			} 
			$lo_object[$li_fila][4+$i]="<select name=cmbunidad".$li_fila." style='width:60px' onChange=ue_procesar_monto('B','".$li_fila."');><option value=D ".$ls_detsel.">Detal</option><option value=M ".$ls_maysel.">Mayor</option></select>";
			$lo_object[$li_fila][5+$i]="<input type=text name=txtmedida".$li_fila." class=sin-borde style=text-align:center  size=14 value='".$ls_medida."' readonly>";
			$lo_object[$li_fila][6+$i]="<input type=text name=txtpreart".$li_fila."    class=sin-borde style=text-align:right  size=10 value='".$li_preart."' 	  onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('B','".$li_fila."'); ".$ls_ronlyprecio.">";
			$lo_object[$li_fila][7+$i]="<input type=text name=txtsubtotart".$li_fila." class=sin-borde style=text-align:right  size=14 value='".$li_subtotart."' readonly>";
			$lo_object[$li_fila][8+$i]="<input type=text name=txtcarart".$li_fila."    class=sin-borde style=text-align:right  size=10 value='".$li_carart."'    readonly>";
			$lo_object[$li_fila][9+$i]="<input type=text name=txttotart".$li_fila."    class=sin-borde style=text-align:right  size=14 value='".$li_totart."'    readonly>".
									" <input type=hidden name=txtspgcuenta".$li_fila."  value='".$ls_spgcuenta."'> ".
									" <input type=hidden name=txtunidad".$li_fila."     value='".$li_unimed."'>";
			$lo_object[$li_fila][10+$i]="<a href=javascript:ue_delete_bienes('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>";
		}
		$li_fila=$li_fila+1;
		$lo_object[$li_fila][1]="<input type=text name=txtcodart".$li_fila."    id=txtcodart".$li_fila." class=sin-borde style=text-align:center size=22 value=''  readonly>";
		$lo_object[$li_fila][2]="<input type=text name=txtdenart".$li_fila."    class=sin-borde style=text-align:left   size=20 value=''  readonly>";
		if($autcan)
		{
			$lo_object[$li_fila][3]="<input type=text name=txtcanart".$li_fila."    class=sin-borde style=text-align:right size=8  readonly>";
			$lo_object[$li_fila][4]="<input type=text name=txtcanartauto".$li_fila."    class=sin-borde style=text-align:right size=8  readonly>";
		}
		else
		{
			$lo_object[$li_fila][3]="<input type=text name=txtcanart".$li_fila."    class=sin-borde style=text-align:right size=8  readonly>";
		} 
		$lo_object[$li_fila][4+$i]="<select name=cmbunidad".$li_fila." style='width:60px' onChange=ue_procesar_monto('B','".$li_fila."');><option value=D ".$ls_detsel.">Detal</option><option value=M ".$ls_maysel.">Mayor</option></select>";
		$lo_object[$li_fila][5+$i]="<input type=text name=txtmedida".$li_fila." class=sin-borde style=text-align:center  size=14 value='' readonly>";
		$lo_object[$li_fila][6+$i]="<input type=text name=txtpreart".$li_fila."    class=sin-borde style=text-align:right  size=10 value=''  onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('B','".$li_fila."');>";
		$lo_object[$li_fila][7+$i]="<input type=text name=txtsubtotart".$li_fila." class=sin-borde style=text-align:right  size=14 value=''  readonly>";
		$lo_object[$li_fila][8+$i]="<input type=text name=txtcarart".$li_fila."    class=sin-borde style=text-align:right  size=10 value=''  readonly>";
		$lo_object[$li_fila][9+$i]="<input type=text name=txttotart".$li_fila."    class=sin-borde style=text-align:right  size=14 value=''  readonly>".
								" <input type=hidden name=txtspgcuenta".$li_fila."  value=''> ".
								" <input type=hidden name=txtunidad".$li_fila."     value=''>";
		$lo_object[$li_fila][10+$i]="";
		if ($autcan)
		{
	    	print "<p>&nbsp;</p>";
		}
		else
		{
			print "<p>&nbsp;</p>";
			print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
			print "    <tr>";
			print " 	  <td height='22' align='left'><a href='javascript:ue_catalogobienes();'><img src='../shared/imagebank/tools/nuevo.gif' title='Agregar Detalle Bienes' width='20' height='20' border='0'>Agregar Detalle Bienes</a></td>";
			print "    </tr>";
			print "  </table>";
		}
		unset($io_solicitud);
		$io_grid->makegrid($li_fila,$lo_title,$lo_object,840,"Detalle de Bienes","gridbienes");
	}// end function uf_load_bienes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_copiar_bienes($as_numsol,$as_codestpre,$as_estclauni)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_copiar_bienes
		//		   Access: private
		//	    Arguments: as_numsol  // Numero de solicitud 
		//	  Description: Método que busca los bienes de la solicitud y los imprime
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$i=0;
		global $io_grid, $io_funciones_sep;
		
		// Titulos del Grid de Bienes
		$lo_title[1]="C&oacute;digo";
		$lo_title[2]="Denominaci&oacute;n";
		$lo_title[3]="Cantidad";
		if ($autcan)
		{
			$lo_title[4]="Cantidad Autorizada";
			$i++;
		}
		$lo_title[4+$i]="Modalidad";
		$lo_title[5+$i]="U/M";
		$lo_title[6+$i]="Precio/Unid.";
		$lo_title[7+$i]="Sub-Total";
		$lo_title[8+$i]="Cargos"; 
		$lo_title[9+$i]="Total";
		$lo_title[10+$i]="";
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		$rs_data = $io_solicitud->uf_load_bienes($as_numsol);
		$li_fila=0;
		while($row=$io_solicitud->io_sql->fetch_row($rs_data))	  
		{
			$li_fila=$li_fila+1;
			$ls_codart=$row["codart"];
			$ls_denart=utf8_encode($row["denart"]);
			$ls_unidad=$row["unidad"];
			$ls_medida=$row["denunimed"];
			$li_canart=$row["canart"];
			$li_preart=$row["monpre"];
			$li_totart=$row["monart"];
			$ls_spgcuenta=$row["spg_cuenta"];
			$li_unimed=$row["unimed"];
			$ls_codpro=$as_codestpre;
			$ls_estcla=$as_estclauni;
			if($ls_unidad=="M") // Si es al Mayor
			{
				$ls_maysel="selected";
				$ls_detsel="";
				//$li_subtotart=$li_preart*($li_canart*$li_unimed);
				$li_subtotart=$li_preart*$li_canart;
			}
			else // Si es al Detal
			{
				$ls_maysel="";
				$ls_detsel="selected";
				$li_subtotart=$li_preart*$li_canart;
			}
			
			$li_totart=number_format($li_totart,2,".","");
			$li_subtotart=number_format($li_subtotart,2,".","");
			$li_carart=$li_totart-$li_subtotart;
			$li_subtotart=number_format($li_subtotart,2,",",".");
			$li_totart=number_format($li_totart,2,",",".");
			$li_canart=number_format($li_canart,2,",",".");
			$li_preart=number_format($li_preart,2,",",".");
			$li_carart=number_format($li_carart,2,",",".");
			$lo_object[$li_fila][1]="<input type=text name=txtcodart".$li_fila."    id=txtcodart".$li_fila." class=sin-borde style=text-align:center size=22 value='".$ls_codart."'    readonly>".
									 "<input type=hidden name=txtcodgas".$li_fila." id=txtcodgas".$li_fila."  value='".$ls_codpro."' readonly>".
									 "<input type=hidden name=txtcodspg".$li_fila." id=txtcodspg".$li_fila."  value='".$ls_spgcuenta."' readonly>".
									 "<input type=hidden name=txtstatus".$li_fila." id=txtstatus".$li_fila."  value='".$ls_estcla."' readonly>";
			$lo_object[$li_fila][2]="<input type=text name=txtdenart".$li_fila."    class=sin-borde style=text-align:left   size=20 value='".$ls_denart."'    readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtcanart".$li_fila."    class=sin-borde style=text-align:right size=8  value='".$li_canart."'     onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('B','".$li_fila."');>";
			$lo_object[$li_fila][4+$i]="<select name=cmbunidad".$li_fila." style='width:60px' onChange=ue_procesar_monto('B','".$li_fila."');><option value=D ".$ls_detsel.">Detal</option><option value=M ".$ls_maysel.">Mayor</option></select>";
			$lo_object[$li_fila][5+$i]="<input type=text name=txtmedida".$li_fila." class=sin-borde style=text-align:center  size=14 value='".$ls_medida."' readonly>";
			$lo_object[$li_fila][6+$i]="<input type=text name=txtpreart".$li_fila."    class=sin-borde style=text-align:right  size=10 value='".$li_preart."' 	  onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('B','".$li_fila."');>";
			$lo_object[$li_fila][7+$i]="<input type=text name=txtsubtotart".$li_fila." class=sin-borde style=text-align:right  size=14 value='".$li_subtotart."' readonly>";
			$lo_object[$li_fila][8+$i]="<input type=text name=txtcarart".$li_fila."    class=sin-borde style=text-align:right  size=10 value='".$li_carart."'    readonly>";
			$lo_object[$li_fila][9+$i]="<input type=text name=txttotart".$li_fila."    class=sin-borde style=text-align:right  size=14 value='".$li_totart."'    readonly>".
									" <input type=hidden name=txtspgcuenta".$li_fila."  value='".$ls_spgcuenta."'> ".
									" <input type=hidden name=txtunidad".$li_fila."     value='".$li_unimed."'>";
			$lo_object[$li_fila][10+$i]="<a href=javascript:ue_delete_bienes('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>";
		}
		$li_fila=$li_fila+1;
		$lo_object[$li_fila][1]="<input type=text name=txtcodart".$li_fila."    id=txtcodart".$li_fila." class=sin-borde style=text-align:center size=22 value=''  readonly>";
		$lo_object[$li_fila][2]="<input type=text name=txtdenart".$li_fila."    class=sin-borde style=text-align:left   size=20 value=''  readonly>";
		$lo_object[$li_fila][3]="<input type=text name=txtcanart".$li_fila."    class=sin-borde style=text-align:right size=8  readonly>";
		$lo_object[$li_fila][4+$i]="<select name=cmbunidad".$li_fila." style='width:60px' onChange=ue_procesar_monto('B','".$li_fila."');><option value=D ".$ls_detsel.">Detal</option><option value=M ".$ls_maysel.">Mayor</option></select>";
		$lo_object[$li_fila][5+$i]="<input type=text name=txtmedida".$li_fila." class=sin-borde style=text-align:center  size=14 value='' readonly>";
		$lo_object[$li_fila][6+$i]="<input type=text name=txtpreart".$li_fila."    class=sin-borde style=text-align:right  size=10 value=''  onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('B','".$li_fila."');>";
		$lo_object[$li_fila][7+$i]="<input type=text name=txtsubtotart".$li_fila." class=sin-borde style=text-align:right  size=14 value=''  readonly>";
		$lo_object[$li_fila][8+$i]="<input type=text name=txtcarart".$li_fila."    class=sin-borde style=text-align:right  size=10 value=''  readonly>";
		$lo_object[$li_fila][9+$i]="<input type=text name=txttotart".$li_fila."    class=sin-borde style=text-align:right  size=14 value=''  readonly>".
								" <input type=hidden name=txtspgcuenta".$li_fila."  value=''> ".
								" <input type=hidden name=txtunidad".$li_fila."     value=''>";
		$lo_object[$li_fila][10+$i]="";
		
		print "<p>&nbsp;</p>";
		print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
		print "    <tr>";
		print " 	  <td height='22' align='left'><a href='javascript:ue_catalogobienes();'><img src='../shared/imagebank/tools/nuevo.gif' title='Agregar Detalle Bienes' width='20' height='20' border='0'>Agregar Detalle Bienes</a></td>";
		print "    </tr>";
		print "  </table>";
		unset($io_solicitud);
		$io_grid->makegrid($li_fila,$lo_title,$lo_object,840,"Detalle de Bienes","gridbienes");
	}// end function uf_copiar_bienes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_servicios($as_numsol, $autcan=false, $tipsep)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_servicios
		//		   Access: private
		//	    Arguments: as_numsol  // Numero de solicitud 
		//	  Description: Método que busca los servicios de la solicitud y los imprime
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep;
		$i=0;
		
		$ls_ronlyprecio = '';
		if ($tipsep=='R' && $_SESSION['la_empresa']['blopresep']=='1'){
			$ls_ronlyprecio = "readonly";
		}
		
		// Titulos del Grid de Servicios
		$lo_title[1]="C&oacute;digo";
		$lo_title[2]="Denominaci&oacute;n";
		$lo_title[3]="Cantidad";
		if ($autcan) {
			$lo_title[4]="Cantidad Autorizada";
			$i=1;
		}
		$lo_title[4+$i]="Precio";
		$lo_title[5+$i]="Sub-Total";
		$lo_title[6+$i]="Cargos";
		$lo_title[7+$i]="Total";
		$lo_title[8+$i]="";
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		$rs_data = $io_solicitud->uf_load_servicios($as_numsol);
		$li_fila=0;
		while($row=$io_solicitud->io_sql->fetch_row($rs_data))	  
		{
			$li_fila=$li_fila+1;
			$ls_codser=$row["codser"];
			$ls_denser=utf8_encode($row["denser"]);
			$li_canser=$row["canser"];
			$li_preser=$row["monpre"];
			$li_subtotser=$li_preser*$li_canser;
			$li_totser=$row["monser"];
			$li_totser=number_format($li_totser,2,".","");
			$li_subtotser=number_format($li_subtotser,2,".","");
			$li_carser=$li_totser-$li_subtotser;
			$ls_spgcuenta=$row["spg_cuenta"];
			$ls_codproser=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
			$ls_estclaser=$row["estcla"];
			$li_canser=number_format($li_canser,2,",",".");
			$li_preser=number_format($li_preser,2,",",".");
			$li_subtotser=number_format($li_subtotser,2,",",".");
			$li_carser=number_format($li_carser,2,",",".");
			$li_totser=number_format($li_totser,2,",",".");
			$lo_object[$li_fila][1]="<input type=text name=txtcodser".$li_fila."    id=txtcodser".$li_fila." class=sin-borde  style=text-align:center  size=15 value='".$ls_codser."' readonly>
									 <input type=hidden name=txtcodgas".$li_fila." id=txtcodgas".$li_fila."  value='".$ls_codproser."' readonly>
									 <input type=hidden name=txtcodspg".$li_fila." id=txtcodspg".$li_fila."  value='".$ls_spgcuenta."' readonly>
									 <input type=hidden name=txtstatus".$li_fila." id=txtstatus".$li_fila."  value='".$ls_estclaser."' readonly>";
			$lo_object[$li_fila][2]="<input type=text name=txtdenser".$li_fila."    class=sin-borde  style=text-align:left    size=30 value='".$ls_denser."' readonly>";
			if ($autcan)
			{
				$lo_object[$li_fila][3]="<input type=text name=txtcanser".$li_fila."    class=sin-borde  style=text-align:right  size=9  value='".$li_canser."' readonly>";
				$lo_object[$li_fila][4]="<input type=text name=txtcanserauto".$li_fila."    class=sin-borde  style=text-align:right  size=9  value='".$li_canser."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('S','".$li_fila."');>";
			}
			else
			{
				$lo_object[$li_fila][3]="<input type=text name=txtcanser".$li_fila."    class=sin-borde  style=text-align:right  size=9  value='".$li_canser."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('S','".$li_fila."');>";
			}
			$lo_object[$li_fila][4+$i]="<input type=text name=txtpreser".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='".$li_preser."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('S','".$li_fila."'); ".$ls_ronlyprecio.">";
			$lo_object[$li_fila][5+$i]="<input type=text name=txtsubtotser".$li_fila." class=sin-borde  style=text-align:right   size=15 value='".$li_subtotser."' readonly>";
			$lo_object[$li_fila][6+$i]="<input type=text name=txtcarser".$li_fila."    class=sin-borde  style=text-align:right   size=10 value='".$li_carser."' readonly>";
			$lo_object[$li_fila][7+$i]="<input type=text name=txttotser".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='".$li_totser."' readonly>".
									"<input type=hidden name=txtspgcuenta".$li_fila."  value='".$ls_spgcuenta."'> ";
			$lo_object[$li_fila][8+$i] ="<a href=javascript:ue_delete_servicios('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0><input type=hidden name=hidspgcuentas".$li_fila."  value=''></a>";
		}
		$li_fila=$li_fila+1;
		$lo_object[$li_fila][1]="<input type=text name=txtcodser".$li_fila."    id=txtcodser".$li_fila." class=sin-borde  style=text-align:center  size=15 value='' readonly>";
		$lo_object[$li_fila][2]="<input type=text name=txtdenser".$li_fila."    class=sin-borde  style=text-align:left    size=30 value='' readonly>";
		if ($autcan)
		{
			$lo_object[$li_fila][3]="<input type=text name=txtcanser".$li_fila."    class=sin-borde  style=text-align:right  size=9  readonly>";
			$lo_object[$li_fila][4]="<input type=text name=txtcanserauto".$li_fila."    class=sin-borde  style=text-align:right  size=9  readonly>";
		}
		else
		{
			$lo_object[$li_fila][3]="<input type=text name=txtcanser".$li_fila."    class=sin-borde  style=text-align:right  size=9  readonly>";
		} 
		$lo_object[$li_fila][4+$i]="<input type=text name=txtpreser".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('S','".$li_fila."');>";
		$lo_object[$li_fila][5+$i]="<input type=text name=txtsubtotser".$li_fila." class=sin-borde  style=text-align:right   size=15 value='' readonly>";
		$lo_object[$li_fila][6+$i]="<input type=text name=txtcarser".$li_fila."    class=sin-borde  style=text-align:right   size=10 value='' readonly>";
		$lo_object[$li_fila][7+$i]="<input type=text name=txttotser".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='' readonly>".
								"<input type=hidden name=txtspgcuenta".$li_fila."  value=''> ";
		$lo_object[$li_fila][8+$i] ="";
		if ($autcan)
		{
			print "<p>&nbsp;</p>";
		}
		else 
		{
			print "<p>&nbsp;</p>";
			print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
			print "    <tr>";
			print "		<td height='22' colspan='3' align='left'><a href='javascript:ue_catalogoservicios();'><img src='../shared/imagebank/tools/nuevo.gif' width='20' height='20' border='0' title='Agregar Detalle Servicios'>Agregar Detalle Servicios</a></td>";
			print "    </tr>";
			print "  </table>";
		}
		
		unset($io_solicitud);
		$io_grid->makegrid($li_fila,$lo_title,$lo_object,840,"Detalle de Servicios","gridservicios");
	}// end function uf_load_servicios
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_copiar_servicios($as_numsol,$as_codestpre,$as_estclauni)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_copiar_servicios
		//		   Access: private
		//	    Arguments: as_numsol  // Numero de solicitud 
		//	  Description: Método que busca los servicios de la solicitud y los imprime
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep;
		$i=0;
		
		// Titulos del Grid de Servicios
		$lo_title[1]="C&oacute;digo";
		$lo_title[2]="Denominaci&oacute;n";
		$lo_title[3]="Cantidad";
		if ($autcan)
		{
			$lo_title[4]="Cantidad Autorizada";
			$i=1;
		}
		$lo_title[4+$i]="Precio";
		$lo_title[5+$i]="Sub-Total";
		$lo_title[6+$i]="Cargos";
		$lo_title[7+$i]="Total";
		$lo_title[8+$i]="";
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		$rs_data = $io_solicitud->uf_load_servicios($as_numsol);
		$li_fila=0;
		while($row=$io_solicitud->io_sql->fetch_row($rs_data))	  
		{
			$li_fila=$li_fila+1;
			$ls_codser=$row["codser"];
			$ls_denser=utf8_encode($row["denser"]);
			$li_canser=$row["canser"];
			$li_preser=$row["monpre"];
			$li_subtotser=$li_preser*$li_canser;
			$li_totser=$row["monser"];
			$li_totser=number_format($li_totser,2,".","");
			$li_subtotser=number_format($li_subtotser,2,".","");
			$li_carser=$li_totser-$li_subtotser;
			$ls_spgcuenta=$row["spg_cuenta"];
			$ls_codproser=$as_codestpre;
			$ls_estclaser=$as_estclauni;
			$li_canser=number_format($li_canser,2,",",".");
			$li_preser=number_format($li_preser,2,",",".");
			$li_subtotser=number_format($li_subtotser,2,",",".");
			$li_carser=number_format($li_carser,2,",",".");
			$li_totser=number_format($li_totser,2,",",".");
			$lo_object[$li_fila][1]="<input type=text name=txtcodser".$li_fila."    id=txtcodser".$li_fila." class=sin-borde  style=text-align:center  size=15 value='".$ls_codser."' readonly>
									 <input type=hidden name=txtcodgas".$li_fila." id=txtcodgas".$li_fila."  value='".$ls_codproser."' readonly>
									 <input type=hidden name=txtcodspg".$li_fila." id=txtcodspg".$li_fila."  value='".$ls_spgcuenta."' readonly>
									 <input type=hidden name=txtstatus".$li_fila." id=txtstatus".$li_fila."  value='".$ls_estclaser."' readonly>";
			$lo_object[$li_fila][2]="<input type=text name=txtdenser".$li_fila."    class=sin-borde  style=text-align:left    size=30 value='".$ls_denser."' readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtcanser".$li_fila."    class=sin-borde  style=text-align:right  size=9  value='".$li_canser."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('S','".$li_fila."');>";
			$lo_object[$li_fila][4+$i]="<input type=text name=txtpreser".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='".$li_preser."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('S','".$li_fila."');>";
			$lo_object[$li_fila][5+$i]="<input type=text name=txtsubtotser".$li_fila." class=sin-borde  style=text-align:right   size=15 value='".$li_subtotser."' readonly>";
			$lo_object[$li_fila][6+$i]="<input type=text name=txtcarser".$li_fila."    class=sin-borde  style=text-align:right   size=10 value='".$li_carser."' readonly>";
			$lo_object[$li_fila][7+$i]="<input type=text name=txttotser".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='".$li_totser."' readonly>".
									"<input type=hidden name=txtspgcuenta".$li_fila."  value='".$ls_spgcuenta."'> ";
			$lo_object[$li_fila][8+$i] ="<a href=javascript:ue_delete_servicios('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0><input type=hidden name=hidspgcuentas".$li_fila."  value=''></a>";
		}
		$li_fila=$li_fila+1;
		$lo_object[$li_fila][1]="<input type=text name=txtcodser".$li_fila."    id=txtcodser".$li_fila." class=sin-borde  style=text-align:center  size=15 value='' readonly>";
		$lo_object[$li_fila][2]="<input type=text name=txtdenser".$li_fila."    class=sin-borde  style=text-align:left    size=30 value='' readonly>";
		$lo_object[$li_fila][3]="<input type=text name=txtcanser".$li_fila."    class=sin-borde  style=text-align:right  size=9  readonly>";
		$lo_object[$li_fila][4+$i]="<input type=text name=txtpreser".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('S','".$li_fila."');>";
		$lo_object[$li_fila][5+$i]="<input type=text name=txtsubtotser".$li_fila." class=sin-borde  style=text-align:right   size=15 value='' readonly>";
		$lo_object[$li_fila][6+$i]="<input type=text name=txtcarser".$li_fila."    class=sin-borde  style=text-align:right   size=10 value='' readonly>";
		$lo_object[$li_fila][7+$i]="<input type=text name=txttotser".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='' readonly>".
								"<input type=hidden name=txtspgcuenta".$li_fila."  value=''> ";
		$lo_object[$li_fila][8+$i] ="";
		if ($autcan)
		{
			print "<p>&nbsp;</p>";
		}
		else
		{
			print "<p>&nbsp;</p>";
			print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
			print "    <tr>";
			print "		<td height='22' colspan='3' align='left'><a href='javascript:ue_catalogoservicios();'><img src='../shared/imagebank/tools/nuevo.gif' width='20' height='20' border='0' title='Agregar Detalle Servicios'>Agregar Detalle Servicios</a></td>";
			print "    </tr>";
			print "  </table>";
		}
		
		unset($io_solicitud);
		$io_grid->makegrid($li_fila,$lo_title,$lo_object,840,"Detalle de Servicios","gridservicios");
	}// end function uf_copiar_servicios
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_conceptos($as_numsol)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_conceptos
		//		   Access: private
		//	    Arguments: as_numsol  // Numero de solicitud 
		//	  Description: Método que busca los conceptos de la solicitud y los imprime
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep;
		
		// Titulos del Grid de Servicios
		$lo_title[1]="C&oacute;digo";
		$lo_title[2]="Denominaci&oacute;n";
		$lo_title[3]="Cantidad";
		$lo_title[4]="Precio";
		$lo_title[5]="Sub-Total";
		$lo_title[6]="Cargos";
		$lo_title[7]="Total";
		$lo_title[8]="";
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		$rs_data = $io_solicitud->uf_load_conceptos($as_numsol);
		$li_fila=0;
		while($row=$io_solicitud->io_sql->fetch_row($rs_data))	  
		{
			$li_fila=$li_fila+1;
			$ls_codcon=$row["codconsep"];
			$ls_dencon=utf8_encode($row["denconsep"]);
			$li_cancon=$row["cancon"];
			$li_precon=$row["monpre"];
			$li_subtotcon=$li_precon*$li_cancon;
			$li_totcon=$row["moncon"];
			$li_totcon=number_format($li_totcon,2,".","");
			$li_subtotcon=number_format($li_subtotcon,2,".","");
			$li_carcon=$li_totcon-$li_subtotcon;
			$ls_spgcuenta=$row["spg_cuenta"];
			$ls_codproser=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
			$ls_estclaser=$row["estcla"];
			$li_cancon=number_format($li_cancon,2,",",".");
			$li_precon=number_format($li_precon,2,",",".");
			$li_subtotcon=number_format($li_subtotcon,2,",",".");
			$li_carcon=number_format($li_carcon,2,",",".");
			$li_totcon=number_format($li_totcon,2,",",".");
			$lo_object[$li_fila][1]="<input type=text name=txtcodcon".$li_fila."    id=txtcodcon".$li_fila." class=sin-borde  style=text-align:center  size=15 value='".$ls_codcon."' readonly>".
									"<input type=hidden name=txtcodgas".$li_fila." id=txtcodgas".$li_fila."  value='".$ls_codproser."' readonly>".
									"<input type=hidden name=txtcodspg".$li_fila." id=txtcodspg".$li_fila."  value='".$ls_spgcuenta."' readonly>".
									"<input type=hidden name=txtstatus".$li_fila." id=txtstatus".$li_fila."  value='".$ls_estclaser."' readonly>";
			$lo_object[$li_fila][2]="<input type=text name=txtdencon".$li_fila."    class=sin-borde  style=text-align:left    size=30 value='".$ls_dencon."' readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtcancon".$li_fila."    class=sin-borde  style=text-align:right  size=9  value='".$li_cancon."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('O','".$li_fila."');>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtprecon".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='".$li_precon."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('O','".$li_fila."');>";
			$lo_object[$li_fila][5]="<input type=text name=txtsubtotcon".$li_fila." class=sin-borde  style=text-align:right   size=15 value='".$li_subtotcon."' readonly>";
			$lo_object[$li_fila][6]="<input type=text name=txtcarcon".$li_fila."    class=sin-borde  style=text-align:right   size=10 value='".$li_carcon."' readonly>";
			$lo_object[$li_fila][7]="<input type=text name=txttotcon".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='".$li_totcon."' readonly>".
									"<input type=hidden name=txtspgcuenta".$li_fila."  value='".$ls_spgcuenta."'> ";
			$lo_object[$li_fila][8] ="<a href=javascript:ue_delete_conceptos('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0><input type=hidden name=hidspgcuentas".$li_fila."  value=''></a>";
		}
		$li_fila=$li_fila+1;
		$lo_object[$li_fila][1]="<input type=text name=txtcodcon".$li_fila."    id=txtcodcon".$li_fila." class=sin-borde  style=text-align:center  size=15 value='' readonly>";
		$lo_object[$li_fila][2]="<input type=text name=txtdencon".$li_fila."    class=sin-borde  style=text-align:left    size=30 value='' readonly>";
		$lo_object[$li_fila][3]="<input type=text name=txtcancon".$li_fila."    class=sin-borde  style=text-align:right  size=9  value='' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('O','".$li_fila."');>"; 
		$lo_object[$li_fila][4]="<input type=text name=txtprecon".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('O','".$li_fila."');>";
		$lo_object[$li_fila][5]="<input type=text name=txtsubtotcon".$li_fila." class=sin-borde  style=text-align:right   size=15 value='' readonly>";
		$lo_object[$li_fila][6]="<input type=text name=txtcarcon".$li_fila."    class=sin-borde  style=text-align:right   size=10 value='' readonly>";
		$lo_object[$li_fila][7]="<input type=text name=txttotcon".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='' readonly>".
								"<input type=hidden name=txtspgcuenta".$li_fila."  value=''> ";
		$lo_object[$li_fila][8] ="";
		print "<p>&nbsp;</p>";
		print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
		print "    <tr>";
		print "		<td height='22' colspan='3' align='left'><a href='javascript:ue_catalogoconceptos();'><img src='../shared/imagebank/tools/nuevo.gif' width='20' height='20' border='0' title='Agregar Detalle Conceptos'>Agregar Detalle Conceptos</a></td>";
		print "    </tr>";
		print "  </table>";
		$io_grid->makegrid($li_fila,$lo_title,$lo_object,840,"Detalle de Conceptos","gridconceptos");
	}// end function uf_load_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_copiar_conceptos($as_numsol,$as_codestpre,$as_estclauni)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_copiar_conceptos
		//		   Access: private
		//	    Arguments: as_numsol  // Numero de solicitud 
		//	  Description: Método que busca los conceptos de la solicitud y los imprime
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep;
		
		// Titulos del Grid de Servicios
		$lo_title[1]="C&oacute;digo";
		$lo_title[2]="Denominaci&oacute;n";
		$lo_title[3]="Cantidad";
		$lo_title[4]="Precio";
		$lo_title[5]="Sub-Total";
		$lo_title[6]="Cargos";
		$lo_title[7]="Total";
		$lo_title[8]="";
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		$rs_data = $io_solicitud->uf_load_conceptos($as_numsol);
		$li_fila=0;
		while($row=$io_solicitud->io_sql->fetch_row($rs_data))	  
		{
			$li_fila=$li_fila+1;
			$ls_codcon=$row["codconsep"];
			$ls_dencon=utf8_encode($row["denconsep"]);
			$li_cancon=$row["cancon"];
			$li_precon=$row["monpre"];
			$li_subtotcon=$li_precon*$li_cancon;
			$li_totcon=$row["moncon"];
			$li_totcon=number_format($li_totcon,2,".","");
			$li_subtotcon=number_format($li_subtotcon,2,".","");
			$li_carcon=$li_totcon-$li_subtotcon;
			$ls_spgcuenta=$row["spg_cuenta"];
			$ls_codproser=$as_codestpre;
			$ls_estclaser=$as_estclauni;
			$li_cancon=number_format($li_cancon,2,",",".");
			$li_precon=number_format($li_precon,2,",",".");
			$li_subtotcon=number_format($li_subtotcon,2,",",".");
			$li_carcon=number_format($li_carcon,2,",",".");
			$li_totcon=number_format($li_totcon,2,",",".");
			$lo_object[$li_fila][1]="<input type=text name=txtcodcon".$li_fila."    id=txtcodcon".$li_fila." class=sin-borde  style=text-align:center  size=15 value='".$ls_codcon."' readonly>".
									"<input type=hidden name=txtcodgas".$li_fila." id=txtcodgas".$li_fila."  value='".$ls_codproser."' readonly>".
									"<input type=hidden name=txtcodspg".$li_fila." id=txtcodspg".$li_fila."  value='".$ls_spgcuenta."' readonly>".
									"<input type=hidden name=txtstatus".$li_fila." id=txtstatus".$li_fila."  value='".$ls_estclaser."' readonly>";
			$lo_object[$li_fila][2]="<input type=text name=txtdencon".$li_fila."    class=sin-borde  style=text-align:left    size=30 value='".$ls_dencon."' readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtcancon".$li_fila."    class=sin-borde  style=text-align:right  size=9  value='".$li_cancon."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('O','".$li_fila."');>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtprecon".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='".$li_precon."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('O','".$li_fila."');>";
			$lo_object[$li_fila][5]="<input type=text name=txtsubtotcon".$li_fila." class=sin-borde  style=text-align:right   size=15 value='".$li_subtotcon."' readonly>";
			$lo_object[$li_fila][6]="<input type=text name=txtcarcon".$li_fila."    class=sin-borde  style=text-align:right   size=10 value='".$li_carcon."' readonly>";
			$lo_object[$li_fila][7]="<input type=text name=txttotcon".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='".$li_totcon."' readonly>".
									"<input type=hidden name=txtspgcuenta".$li_fila."  value='".$ls_spgcuenta."'> ";
			$lo_object[$li_fila][8] ="<a href=javascript:ue_delete_conceptos('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0><input type=hidden name=hidspgcuentas".$li_fila."  value=''></a>";
		}
		$li_fila=$li_fila+1;
		$lo_object[$li_fila][1]="<input type=text name=txtcodcon".$li_fila."    id=txtcodcon".$li_fila." class=sin-borde  style=text-align:center  size=15 value='' readonly>";
		$lo_object[$li_fila][2]="<input type=text name=txtdencon".$li_fila."    class=sin-borde  style=text-align:left    size=30 value='' readonly>";
		$lo_object[$li_fila][3]="<input type=text name=txtcancon".$li_fila."    class=sin-borde  style=text-align:right  size=9  value='' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('O','".$li_fila."');>"; 
		$lo_object[$li_fila][4]="<input type=text name=txtprecon".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_procesar_monto('O','".$li_fila."');>";
		$lo_object[$li_fila][5]="<input type=text name=txtsubtotcon".$li_fila." class=sin-borde  style=text-align:right   size=15 value='' readonly>";
		$lo_object[$li_fila][6]="<input type=text name=txtcarcon".$li_fila."    class=sin-borde  style=text-align:right   size=10 value='' readonly>";
		$lo_object[$li_fila][7]="<input type=text name=txttotcon".$li_fila."    class=sin-borde  style=text-align:right   size=15 value='' readonly>".
								"<input type=hidden name=txtspgcuenta".$li_fila."  value=''> ";
		$lo_object[$li_fila][8] ="";
		print "<p>&nbsp;</p>";
		print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
		print "    <tr>";
		print "		<td height='22' colspan='3' align='left'><a href='javascript:ue_catalogoconceptos();'><img src='../shared/imagebank/tools/nuevo.gif' width='20' height='20' border='0' title='Agregar Detalle Conceptos'>Agregar Detalle Conceptos</a></td>";
		print "    </tr>";
		print "  </table>";
		$io_grid->makegrid($li_fila,$lo_title,$lo_object,840,"Detalle de Conceptos","gridconceptos");
	}// end function uf_copiar_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_creditos($as_titulo,$as_numsol,$as_tipo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_creditos
		//		   Access: private
		//	    Arguments: as_numsol  // Número de Solicitud
		//                 as_titulo // Titulo de bienes o servicios
		//                 as_tipo // Tipo de SEP si es de bienes ó de servicios
		//	  Description: Método que busca los creditos de una solicitud y las imprime
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $la_cuentacargo, $li_cuenta;

		// Titulos del Grid
		$lo_title[1]=$as_titulo;
		$lo_title[2]="C&oacute;digo";
		$lo_title[3]="Denominaci&oacute;n";
		$lo_title[4]="Base Imponible";
		$lo_title[5]="Monto del Cargo";
		$lo_title[6]="Sub-Total";
		$lo_object[0]="";
		switch($as_tipo)
		{
			case "B": // Si es de Bienes
				$ls_tabla = "sep_dta_cargos";
				$ls_campo = "codart";
				break;
			case "S": // Si es de Servicios
				$ls_tabla = "sep_dts_cargos";
				$ls_campo = "codser";
				break;
			case "O": // Si es de Conceptos
				$ls_tabla = "sep_dtc_cargos";
				$ls_campo = "codconsep";
				break;
		}
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		$rs_data = $io_solicitud->uf_load_cargos($as_numsol,$ls_tabla,$ls_campo);
		$li_fila=0;
		while($row=$io_solicitud->io_sql->fetch_row($rs_data))	  
		{
			$li_fila=$li_fila+1;
			$ls_codservic=$row["codigo"];
			$ls_codcar=$row["codcar"];
			$ls_dencar=utf8_encode($row["dencar"]);
			$li_bascar=number_format($row["monbasimp"],2,",",".");
			$li_moncar=number_format($row["monimp"],2,",",".");
			$li_subcargo=number_format($row["monto"],2,",",".");
			$ls_spg_cuenta=$row["spg_cuenta"];
			$ls_formula=$row["formula"];
			$ls_codestpro1=$row["codestpro1"];
			$ls_codestpro2=$row["codestpro2"];
			$ls_codestpro3=$row["codestpro3"];
			$ls_codestpro4=$row["codestpro4"];
			$ls_codestpro5=$row["codestpro5"];
			$ls_estcla=$row["estcla"];
			$ls_codestpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
			$lo_object[$li_fila][1]="<input name=txtcodservic".$li_fila." type=text id=txtcodservic".$li_fila." class=sin-borde  size=22   style=text-align:center value='".$ls_codservic."' readonly>".
									"<input type=TEXT name=txtcodgascre".$li_fila." id=txtcodgascre".$li_fila."  value='".$ls_codestpro."' readonly>".
									"<input type=hidden name=txtcodspgcre".$li_fila." id=txtcodspgcre".$li_fila."  value='".$ls_spg_cuenta."' readonly>".
									"<input type=hidden name=txtstatuscre".$li_fila." id=txtstatuscre".$li_fila."  value='".$ls_estcla."' readonly>";
			$lo_object[$li_fila][2]="<input name=txtcodcar".$li_fila."    type=text id=txtcodcar".$li_fila."    class=sin-borde  size=10   style=text-align:center value='".$ls_codcar."' readonly>";
			$lo_object[$li_fila][3]="<input name=txtdencar".$li_fila."    type=text id=txtdencar".$li_fila."    class=sin-borde  size=36   style=text-align:left   value='".$ls_dencar."' readonly>";
			$lo_object[$li_fila][4]="<input name=txtbascar".$li_fila."    type=text id=txtbascar".$li_fila."    class=sin-borde  size=17   style=text-align:right  value='".$li_bascar."' readonly>";
			$lo_object[$li_fila][5]="<input name=txtmoncar".$li_fila."    type=text id=txtmoncar".$li_fila."    class=sin-borde  size=13   style=text-align:right  value='".$li_moncar."' readonly>";
			$lo_object[$li_fila][6]="<input name=txtsubcargo".$li_fila."  type=text id=txtsubcargo".$li_fila."  class=sin-borde  size=17   style=text-align:right  value='".$li_subcargo."' readonly>".
									"<input name=cuentacargo".$li_fila."  type=hidden id=cuentacargo".$li_fila."  value='".$ls_spg_cuenta."'>".
									"<input name=formulacargo".$li_fila." type=hidden id=formulacargo".$li_fila." value='".$ls_formula."'>";
		}
		print "<p>&nbsp;</p>";
		$io_grid->makegrid($li_fila,$lo_title,$lo_object,840,"Cr&eacute;ditos","gridcreditos");
		unset($io_solicitud);		
		print "<table width='840' height='22' border='0' align='center' cellpadding='0' cellspacing='0'>";
		print "        <tr>";
		print "          <td width='175' height='22' align='right'><div align='left'><input name='btncrear' type='button' class='boton' id='btncerrar' value='Crear Asiento' onClick='javascript: ue_crear_asiento();'></div></td>";
		print "        </tr>";
		print "</table>";
	}// end function uf_print_creditos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_copiar_creditos($as_titulo,$as_numsol,$as_codestpre,$as_estclauni,$as_tipo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_copiar_creditos
		//		   Access: private
		//	    Arguments: as_numsol  // Número de Solicitud
		//                 as_titulo // Titulo de bienes o servicios
		//                 as_tipo // Tipo de SEP si es de bienes ó de servicios
		//	  Description: Método que busca los creditos de una solicitud y las imprime
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $la_cuentacargo, $li_cuenta;

		// Titulos del Grid
		$lo_title[1]=$as_titulo;
		$lo_title[2]="C&oacute;digo";
		$lo_title[3]="Denominaci&oacute;n";
		$lo_title[4]="Base Imponible";
		$lo_title[5]="Monto del Cargo";
		$lo_title[6]="Sub-Total";
		$lo_object[0]="";
		switch($as_tipo)
		{
			case "B": // Si es de Bienes
				$ls_tabla = "sep_dta_cargos";
				$ls_campo = "codart";
				break;
			case "S": // Si es de Servicios
				$ls_tabla = "sep_dts_cargos";
				$ls_campo = "codser";
				break;
			case "O": // Si es de Conceptos
				$ls_tabla = "sep_dtc_cargos";
				$ls_campo = "codconsep";
				break;
		}
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		$rs_data = $io_solicitud->uf_load_cargos($as_numsol,$ls_tabla,$ls_campo);
		$li_fila=0;
		while($row=$io_solicitud->io_sql->fetch_row($rs_data))	  
		{
			$li_fila=$li_fila+1;
			$ls_codservic=$row["codigo"];
			$ls_codcar=$row["codcar"];
			$ls_dencar=utf8_encode($row["dencar"]);
			$li_bascar=number_format($row["monbasimp"],2,",",".");
			$li_moncar=number_format($row["monimp"],2,",",".");
			$li_subcargo=number_format($row["monto"],2,",",".");
			$ls_spg_cuenta=$row["spg_cuenta"];
			$ls_formula=$row["formula"];
			$ls_codestpro1=$row["codestpro1"];
			$ls_codestpro2=$row["codestpro2"];
			$ls_codestpro3=$row["codestpro3"];
			$ls_codestpro4=$row["codestpro4"];
			$ls_codestpro5=$row["codestpro5"];
			$ls_estcla=$as_estclauni;
			$ls_codestpro=$as_codestpre;
			$lo_object[$li_fila][1]="<input name=txtcodservic".$li_fila." type=text id=txtcodservic".$li_fila." class=sin-borde  size=22   style=text-align:center value='".$ls_codservic."' readonly>".
									"<input type=TEXT name=txtcodgascre".$li_fila." id=txtcodgascre".$li_fila."  value='".$ls_codestpro."' readonly>".
									"<input type=hidden name=txtcodspgcre".$li_fila." id=txtcodspgcre".$li_fila."  value='".$ls_spg_cuenta."' readonly>".
									"<input type=hidden name=txtstatuscre".$li_fila." id=txtstatuscre".$li_fila."  value='".$ls_estcla."' readonly>";
			$lo_object[$li_fila][2]="<input name=txtcodcar".$li_fila."    type=text id=txtcodcar".$li_fila."    class=sin-borde  size=10   style=text-align:center value='".$ls_codcar."' readonly>";
			$lo_object[$li_fila][3]="<input name=txtdencar".$li_fila."    type=text id=txtdencar".$li_fila."    class=sin-borde  size=36   style=text-align:left   value='".$ls_dencar."' readonly>";
			$lo_object[$li_fila][4]="<input name=txtbascar".$li_fila."    type=text id=txtbascar".$li_fila."    class=sin-borde  size=17   style=text-align:right  value='".$li_bascar."' readonly>";
			$lo_object[$li_fila][5]="<input name=txtmoncar".$li_fila."    type=text id=txtmoncar".$li_fila."    class=sin-borde  size=13   style=text-align:right  value='".$li_moncar."' readonly>";
			$lo_object[$li_fila][6]="<input name=txtsubcargo".$li_fila."  type=text id=txtsubcargo".$li_fila."  class=sin-borde  size=17   style=text-align:right  value='".$li_subcargo."' readonly>".
									"<input name=cuentacargo".$li_fila."  type=hidden id=cuentacargo".$li_fila."  value='".$ls_spg_cuenta."'>".
									"<input name=formulacargo".$li_fila." type=hidden id=formulacargo".$li_fila." value='".$ls_formula."'>";
		}
		print "<p>&nbsp;</p>";
		$io_grid->makegrid($li_fila,$lo_title,$lo_object,840,"Cr&eacute;ditos","gridcreditos");
		unset($io_solicitud);		
		print "<table width='840' height='22' border='0' align='center' cellpadding='0' cellspacing='0'>";
		print "        <tr>";
		print "          <td width='175' height='22' align='right'><div align='left'><input name='btncrear' type='button' class='boton' id='btncerrar' value='Crear Asiento' onClick='javascript: ue_crear_asiento();'></div></td>";
		print "        </tr>";
		print "</table>";
	}// end function uf_copiar_creditos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_cuentas($as_numsol,$as_tipo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_cuentas
		//		   Access: private
		//	    Arguments: as_numsol  // Número de Solicitud
		//                 as_tipo // Tipo de SEP si es de bienes ó de servicios
		//	  Description: Método que busca las cuentas presupuestarias asociadas a una solicitud
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $la_cuentacargo;
		global $li_loncodestpro1,$li_loncodestpro2,$li_loncodestpro3,$li_loncodestpro4,$li_loncodestpro5;
		global $li_longestpro1,$li_longestpro2,$li_longestpro3,$li_longestpro4,$li_longestpro5;
		require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
		$io_dscuentas=new class_datastore();
		
		// Titulos el Grid
		$lo_title[1]="Estructura Programatica";
		$lo_title[2]="Cuenta";
		$lo_title[3]="Monto";
		$lo_title[4]=""; 
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		switch($ls_modalidad)
		{
			case "1": // Modalidad por Proyecto
				$li_len1=20;
				$li_len2=6;
				$li_len3=3;
				$li_len4=2;
				$li_len5=2;
				break;
				
			case "2": // Modalidad por Presupuesto
				$li_len1=2;
				$li_len2=2;
				$li_len3=2;
				$li_len4=2;
				$li_len5=2;
				break;
		}
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		$io_dscuentas = $io_solicitud->uf_load_cuentas($as_numsol);
		$li_fila=0;
		if($io_dscuentas!=false)
		{
			$li_totrow=$io_dscuentas->getRowCount("spg_cuenta");
			for($li_i=1;($li_i<=$li_totrow);$li_i++)
			{
				$li_monto=$io_dscuentas->data["total"][$li_i];
				if($li_monto>0)
				{
					$li_fila=$li_fila+1;
					$ls_codpro=$io_dscuentas->data["codestpro1"][$li_i].$io_dscuentas->data["codestpro2"][$li_i].
							   $io_dscuentas->data["codestpro3"][$li_i].$io_dscuentas->data["codestpro4"][$li_i].
							   $io_dscuentas->data["codestpro5"][$li_i];
					$ls_cuenta=$io_dscuentas->data["spg_cuenta"][$li_i];
					$ls_estcla=$io_dscuentas->data["estcla"][$li_i];
					$li_moncue=number_format($io_dscuentas->data["total"][$li_i],2,",",".");
					$ls_codest1=substr($ls_codpro,0,25);
					$ls_codest1=substr($ls_codest1,$li_longestpro1-1,$li_loncodestpro1);
					$ls_codest2=substr($ls_codpro,25,25);
					$ls_codest2=substr($ls_codest2,$li_longestpro2-1,$li_loncodestpro2);
					$ls_codest3=substr($ls_codpro,50,25);
					$ls_codest3=substr($ls_codest3,$li_longestpro3-1,$li_loncodestpro3);
					$ls_codest4=substr($ls_codpro,75,25);
					$ls_codest4=substr($ls_codest4,$li_longestpro4-1,$li_loncodestpro4);
					$ls_codest5=substr($ls_codpro,100,25);
					$ls_codest5=substr($ls_codest5,$li_longestpro5-1,$li_loncodestpro5);
					$ls_programatica=$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5;
					$lo_object[$li_fila][1]="<input name=txtprogramaticagas".$li_fila." type=text id=txtprogramaticagas".$li_fila." class=sin-borde  style=text-align:center size=45 value='".$ls_programatica."' readonly>"."<input name=txtestclagas".$li_fila."       type=hidden size='2' id=txtestclagas".$li_fila."  value='".$ls_estcla."'>";
					$lo_object[$li_fila][2]="<input name=txtcuentagas".$li_fila." type=text id=txtcuentagas".$li_fila." class=sin-borde  style=text-align:center size=25 value='".$ls_cuenta."' readonly>";
					$lo_object[$li_fila][3]="<input name=txtmoncuegas".$li_fila." type=text id=txtmoncuegas".$li_fila." class=sin-borde  style=text-align:right  size=25 onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_moncue."' >";
					$lo_object[$li_fila][4]="<a href=javascript:ue_delete_cuenta_gasto('".$li_fila."','".$as_tipo."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>".
											"<input name=txtcodprogas".$li_fila."  type=hidden id=txtcodprogas".$li_fila."  value='".$ls_programatica."'>";
				}
			}
		}
		$li_fila=$li_fila+1;
		$lo_object[$li_fila][1]="<input name=txtprogramaticagas".$li_fila." type=text id=txtprogramaticagas".$li_fila." class=sin-borde  style=text-align:center size=45 value='' readonly>"."<input name=txtestclagas".$li_fila."       type=hidden size='2' id=txtestclagas".$li_fila."  value=''>";
		$lo_object[$li_fila][2]="<input name=txtcuentagas".$li_fila." type=text id=txtcuentagas".$li_fila." class=sin-borde  style=text-align:center size=25 value='' readonly>";
		$lo_object[$li_fila][3]="<input name=txtmoncuegas".$li_fila." type=text id=txtmoncuegas".$li_fila." class=sin-borde  style=text-align:right  size=25 value='' readonly>";
		$lo_object[$li_fila][4]="<input name=txtcodprogas".$li_fila."  type=hidden id=txtcodprogas".$li_fila."  value=''>";        

		$io_grid->makegrid($li_fila,$lo_title,$lo_object,840,"Cuentas","gridcuentas");
		unset($io_solicitud);
	}// end function uf_load_cuentas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_cuentas_cargo($as_numsol,$as_tipo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_cuentas_cargo
		//		   Access: private
		//	    Arguments: as_numsol  // Número de Solicitud
		//                 as_tipo // Tipo de SEP si es de bienes ó de servicios
		//	  Description: Método que busca las cuentas asociadas a los cargos de una solicitud
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $la_cuentacargo;
		global $li_longestpro1,$li_longestpro2,$li_longestpro3,$li_longestpro4,$li_longestpro5;
		global $li_loncodestpro1,$li_loncodestpro2,$li_loncodestpro3,$li_loncodestpro4,$li_loncodestpro5;
		// Titulos el Grid
		$lo_title[1]="Cargo";
		$lo_title[2]="Estructura Programatica";
		$lo_title[3]="Cuenta";
		$lo_title[4]="Monto";
		$lo_title[5]=""; 
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		switch($ls_modalidad)
		{
			case "1": // Modalidad por Proyecto
				$li_len1=20;
				$li_len2=6;
				$li_len3=3;
				$li_len4=2;
				$li_len5=2;
				break;
				
			case "2": // Modalidad por Presupuesto
				$li_len1=2;
				$li_len2=2;
				$li_len3=2;
				$li_len4=2;
				$li_len5=2;
				break;
		}
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		$rs_data = $io_solicitud->uf_load_cuentas_cargo($as_numsol);
		$li_fila=0;
		while($row=$io_solicitud->io_sql->fetch_row($rs_data))	  
		{
			$li_fila=$li_fila+1;
			$ls_codcargo=$row["codcar"];
			$ls_codpro=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
			$ls_cuenta=$row["spg_cuenta"];
			$ls_estcla=$row["estcla"];
			$li_moncue=number_format($row["total"],2,",",".");
			$ls_codest1=substr($ls_codpro,0,25);
			$ls_codest1=substr($ls_codest1,$li_longestpro1-1,$li_loncodestpro1);
			$ls_codest2=substr($ls_codpro,25,25);
			$ls_codest2=substr($ls_codest2,$li_longestpro2-1,$li_loncodestpro2);
			$ls_codest3=substr($ls_codpro,50,25);
			$ls_codest3=substr($ls_codest3,$li_longestpro3-1,$li_loncodestpro3);
			$ls_codest4=substr($ls_codpro,75,25);
			$ls_codest4=substr($ls_codest4,$li_longestpro4-1,$li_loncodestpro4);
			$ls_codest5=substr($ls_codpro,100,25);
			$ls_codest5=substr($ls_codest5,$li_longestpro5-1,$li_loncodestpro5);
			$ls_programatica=$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5;
			$lo_object[$li_fila][1]="<input name=txtcodcargo".$li_fila." type=text id=txtcodcargo".$li_fila." class=sin-borde  style=text-align:center size=10 value='".$ls_codcargo."' readonly>";
			$lo_object[$li_fila][2]="<input name=txtprogramaticacar".$li_fila." type=text id=txtprogramaticacar".$li_fila." class=sin-borde  style=text-align:center size=45 value='".$ls_programatica."' readonly>".
									"<input name=txtestclacar".$li_fila."       type=hidden size='2' id=txtestclacar".$li_fila."  value='".$ls_estcla."'>";
			$lo_object[$li_fila][3]="<input name=txtcuentacar".$li_fila." type=text id=txtcuentacar".$li_fila." class=sin-borde  style=text-align:center size=25 value='".$ls_cuenta."' readonly>";
			$lo_object[$li_fila][4]="<input name=txtmoncuecar".$li_fila." type=text id=txtmoncuecar".$li_fila." class=sin-borde  style=text-align:right  size=25 onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_moncue."' >";
			$lo_object[$li_fila][5]="<a href=javascript:ue_delete_cuenta_cargo('".$li_fila."','".$as_tipo."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>".
									"<input name=estclacar".$li_fila."  type=hidden id=estclacar".$li_fila."  value='".$ls_estcla."'>".
									"<input name=txtcodprocar".$li_fila."  type=hidden id=txtcodprocar".$li_fila."  value='".$ls_programatica."'>";
		}
		$li_fila=$li_fila+1;
		$lo_object[$li_fila][1]="<input name=txtcodcargo".$li_fila." type=text id=txtcodcargo".$li_fila." class=sin-borde  style=text-align:center size=10 value='' readonly>";
		$lo_object[$li_fila][2]="<input name=txtprogramaticacar".$li_fila." type=text id=txtprogramaticacar".$li_fila." class=sin-borde  style=text-align:center size=45 value='' readonly>"."<input name=txtestclacar".$li_fila."       type=hidden size='2' id=txtestclacar".$li_fila."  value=''>";
		$lo_object[$li_fila][3]="<input name=txtcuentacar".$li_fila." type=text id=txtcuentacar".$li_fila." class=sin-borde  style=text-align:center size=25 value='' readonly>";
		$lo_object[$li_fila][4]="<input name=txtmoncuecar".$li_fila." type=text id=txtmoncuecar".$li_fila." class=sin-borde  style=text-align:right  size=25 value='' readonly>";
		$lo_object[$li_fila][5]="<input name=txtcodprocar".$li_fila."  type=hidden id=txtcodprocar".$li_fila."  value=''>";        

		$io_grid->makegrid($li_fila,$lo_title,$lo_object,840,"Cuentas Cargos","gridcuentascargos");
		unset($io_solicitud);
	}// end function uf_load_cuentas_cargo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cierrecuentas_gasto($ai_total,$as_tipo,$autcan=false)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_cierrecuentas_gasto
		//		   Access: private
		//	    Arguments: ai_total  // Total de filas a imprimir
		//                 as_tipo // Tipo de SEP si es de bienes ó de servicios
		//	  Description: Método que imprime el grid de las cuentas presupuestarias del Gasto
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $la_cuentacargo,$li_estmodest,$li_loncodestpro1,$li_loncodestpro2,$li_loncodestpro3,
			   $li_loncodestpro4,$li_loncodestpro5;
		require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
		$io_dscuentas=new class_datastore();	
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
			
		// Titulos el Grid
		$lo_title[1]="Estructura Programatica";
		$lo_title[2]="Cuenta";
		$lo_title[3]="Monto";
		//$lo_title[4]=""; 
		$ls_codpro="";
		// Recorrido del Grid de Cuentas Presupuestarias
		switch ($as_tipo)
		{
			case "B":
				for ($li_fila=1;$li_fila<=$ai_total;$li_fila++)
				{ 
					$li_moncue= $io_funciones_sep->uf_obtenervalor("txtsubtotart".$li_fila,"0,00");
					$ls_cuenta= trim($io_funciones_sep->uf_obtenervalor("txtcodspg".$li_fila,""));
					$ls_codprogas= trim($io_funciones_sep->uf_obtenervalor("txtcodgas".$li_fila,""));
					$ls_estclapre= trim($io_funciones_sep->uf_obtenervalor("txtstatus".$li_fila,""));
					$li_moncue= str_replace(".","",$li_moncue);
					$li_moncue= str_replace(",",".",$li_moncue);	
					$ls_codestpro1= substr($ls_codprogas,0,25);
					$ls_codestpro2= substr($ls_codprogas,25,25);
					$ls_codestpro3= substr($ls_codprogas,50,25); 
					$ls_codestpro4= substr($ls_codprogas,75,25);
					$ls_codestpro5= substr($ls_codprogas,100,25); 					
					if (!empty($ls_cuenta))
					{
						if ($autcan){
							if ($li_moncue!="0.00") {
								$io_dscuentas->insertRow("estclagas",$ls_estclapre);
								$io_dscuentas->insertRow("codprogas",$ls_codprogas);
								$io_dscuentas->insertRow("cuentagas",$ls_cuenta);			
								$io_dscuentas->insertRow("moncuegas",$li_moncue);
								$io_dscuentas->insertRow("codestpro1",$ls_codestpro1);
								$io_dscuentas->insertRow("codestpro2",$ls_codestpro2);
								$io_dscuentas->insertRow("codestpro3",$ls_codestpro3);
								$io_dscuentas->insertRow("codestpro4",$ls_codestpro4);
								$io_dscuentas->insertRow("codestpro5",$ls_codestpro5);
							}
						}
						else{
							$io_dscuentas->insertRow("estclagas",$ls_estclapre);
							$io_dscuentas->insertRow("codprogas",$ls_codprogas);
							$io_dscuentas->insertRow("cuentagas",$ls_cuenta);			
							$io_dscuentas->insertRow("moncuegas",$li_moncue);
							$io_dscuentas->insertRow("codestpro1",$ls_codestpro1);
							$io_dscuentas->insertRow("codestpro2",$ls_codestpro2);
							$io_dscuentas->insertRow("codestpro3",$ls_codestpro3);
							$io_dscuentas->insertRow("codestpro4",$ls_codestpro4);
							$io_dscuentas->insertRow("codestpro5",$ls_codestpro5);
						}
					}
				}
			break;
			
			case "S":
				for ($li_fila=1;$li_fila<=$ai_total;$li_fila++)
				{ 
					$li_moncue= $io_funciones_sep->uf_obtenervalor("txtsubtotser".$li_fila,"0,00");
					$ls_cuenta= trim($io_funciones_sep->uf_obtenervalor("txtcodspg".$li_fila,""));
					$ls_codprogas= trim($io_funciones_sep->uf_obtenervalor("txtcodgas".$li_fila,""));
					$ls_estclapre= trim($io_funciones_sep->uf_obtenervalor("txtstatus".$li_fila,""));
					$li_moncue= str_replace(".","",$li_moncue);
					$li_moncue= str_replace(",",".",$li_moncue);	
					$ls_codestpro1= substr($ls_codprogas,0,25);
					$ls_codestpro2= substr($ls_codprogas,25,25);
					$ls_codestpro3= substr($ls_codprogas,50,25); 
					$ls_codestpro4= substr($ls_codprogas,75,25);
					$ls_codestpro5= substr($ls_codprogas,100,25); 					
					if (!empty($ls_cuenta))
					{
						if ($autcan){
							if ($li_moncue!="0.00") {
								$io_dscuentas->insertRow("estclagas",$ls_estclapre);
								$io_dscuentas->insertRow("codprogas",$ls_codprogas);
								$io_dscuentas->insertRow("cuentagas",$ls_cuenta);			
								$io_dscuentas->insertRow("moncuegas",$li_moncue);
								$io_dscuentas->insertRow("codestpro1",$ls_codestpro1);
								$io_dscuentas->insertRow("codestpro2",$ls_codestpro2);
								$io_dscuentas->insertRow("codestpro3",$ls_codestpro3);
								$io_dscuentas->insertRow("codestpro4",$ls_codestpro4);
								$io_dscuentas->insertRow("codestpro5",$ls_codestpro5);
							}
						}
						else{
							$io_dscuentas->insertRow("estclagas",$ls_estclapre);
							$io_dscuentas->insertRow("codprogas",$ls_codprogas);
							$io_dscuentas->insertRow("cuentagas",$ls_cuenta);			
							$io_dscuentas->insertRow("moncuegas",$li_moncue);
							$io_dscuentas->insertRow("codestpro1",$ls_codestpro1);
							$io_dscuentas->insertRow("codestpro2",$ls_codestpro2);
							$io_dscuentas->insertRow("codestpro3",$ls_codestpro3);
							$io_dscuentas->insertRow("codestpro4",$ls_codestpro4);
							$io_dscuentas->insertRow("codestpro5",$ls_codestpro5);
						}
					}
				}
			
			break;
			case "O":
				for ($li_fila=1;$li_fila<=$ai_total;$li_fila++)
				{ 
					$li_moncue= $io_funciones_sep->uf_obtenervalor("txtsubtotcon".$li_fila,"0,00");
					$ls_cuenta= trim($io_funciones_sep->uf_obtenervalor("txtcodspg".$li_fila,""));
					$ls_codprogas= trim($io_funciones_sep->uf_obtenervalor("txtcodgas".$li_fila,""));
					$ls_estclapre= trim($io_funciones_sep->uf_obtenervalor("txtstatus".$li_fila,""));
					$li_moncue= str_replace(".","",$li_moncue);
					$li_moncue= str_replace(",",".",$li_moncue);	
					$ls_codestpro1= substr($ls_codprogas,0,25);
					$ls_codestpro2= substr($ls_codprogas,25,25);
					$ls_codestpro3= substr($ls_codprogas,50,25); 
					$ls_codestpro4= substr($ls_codprogas,75,25);
					$ls_codestpro5= substr($ls_codprogas,100,25); 					
					if (!empty($ls_cuenta))
					{
						$io_dscuentas->insertRow("estclagas",$ls_estclapre);
						$io_dscuentas->insertRow("codprogas",$ls_codprogas);
						$io_dscuentas->insertRow("cuentagas",$ls_cuenta);			
						$io_dscuentas->insertRow("moncuegas",$li_moncue);
						$io_dscuentas->insertRow("codestpro1",$ls_codestpro1);
						$io_dscuentas->insertRow("codestpro2",$ls_codestpro2);
						$io_dscuentas->insertRow("codestpro3",$ls_codestpro3);
						$io_dscuentas->insertRow("codestpro4",$ls_codestpro4);
						$io_dscuentas->insertRow("codestpro5",$ls_codestpro5);			
					}
				}
			
			break;
		}
		// Agrupamos las cuentas por programatica y cuenta
		$io_dscuentas->group_by(array('0'=>'codestpro1','1'=>'codestpro2','2'=>'codestpro3','3'=>'codestpro4','4'=>'codestpro5','5'=>'estclagas','6'=>'cuentagas'),array('0'=>'moncuegas'),'moncuegas');
		$li_total=$io_dscuentas->getRowCount('codprogas');
		// Recorremos el data stored de cuentas que se lleno y se agrupo anteriormente
		for($li_fila=1;$li_fila<=$li_total;$li_fila++)
		{
			$ls_codprogas=$io_dscuentas->getValue('codprogas',$li_fila);
			$ls_cuenta=$io_dscuentas->getValue('cuentagas',$li_fila);
			$ls_estcla=$io_dscuentas->getValue('estclagas',$li_fila);
			$li_moncue=number_format($io_dscuentas->getValue('moncuegas',$li_fila),2,",",".");
			$ls_codestpro1 = substr($ls_codprogas,0,25);
			$ls_codestpro1 = substr($ls_codestpro1,-$li_loncodestpro1);
			$ls_codestpro2 = substr($ls_codprogas,25,25);
			$ls_codestpro2 = substr($ls_codestpro2,-$li_loncodestpro2);
			$ls_codestpro3 = substr($ls_codprogas,50,25);
			$ls_codestpro3 = substr($ls_codestpro3,-$li_loncodestpro3);
			$ls_codestpro  = "";
			if (!empty($ls_codprogas))
			{
				$ls_codestpro  = $ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3;
			} 
			if ($li_estmodest==2)
			{
				if (!empty($ls_codprogas))
				{
					$ls_denestcla  = $_SESSION["la_empresa"]["nomestpro1"]; 
					$ls_codestpro4 = substr($ls_codprogas,75,25);
					$ls_codestpro4 = substr($ls_codestpro4,-$li_loncodestpro4);
					$ls_codestpro5 = substr($ls_codprogas,100,25);
					$ls_codestpro5 = substr($ls_codestpro5,-$li_loncodestpro5);
					$ls_codestpro  = $ls_codestpro.'-'.$ls_codestpro4.'-'.$ls_codestpro5;
				}							   
			}
			elseif($li_estmodest==1) 
			{
				if ($ls_estcla=='P')
				{
					$ls_denestcla = 'Proyecto';
				}
				elseif($ls_estcla=='A')
				{
					$ls_denestcla  = 'Actividad';
				} 
			} 
			if($ls_cuenta!="")
			{
				$ls_tipsep = $io_funciones_sep->uf_obtenervalor("tipsep","");
				$ls_sccuenta='';
				if($ls_tipsep!="S")
				{
					$arrResultado= $io_solicitud->uf_select_cuentacontable(str_pad($ls_codestpro1,25,'0',0),str_pad($ls_codestpro2,25,'0',0),str_pad($ls_codestpro3,25,'0',0),str_pad($ls_codestpro4,25,'0',0),str_pad($ls_codestpro5,25,'0',0),$ls_cuenta,$ls_estcla,$ls_sccuenta);
					$lb_valido = $arrResultado['lb_valido'];
					$ls_sccuenta = $arrResultado['as_sccuenta'];
					if(!$lb_valido)
					{
						$io_mensajes->uf_mensajes_ajax("Informacion","No existe la cuenta contable asociada en la estructura seleccionada",true,""); 				
					}
				}
				else
				{
					$lb_valido=true;
				}
				if($lb_valido)
				{
					$lo_object[$li_fila][1]="<input name=txtprogramaticagas".$li_fila." type=text id=txtprogramaticagas".$li_fila." class=sin-borde  style=text-align:center size=45 value='".$ls_codestpro."' readonly>".
											"<input name=txtestclagas".$li_fila."  type=hidden size='2' id=txtestclagas".$li_fila."  value='".$ls_estcla."'>";
					$lo_object[$li_fila][2]="<input name=txtcuentagas".$li_fila." type=text id=txtcuentagas".$li_fila." class=sin-borde  style=text-align:center size=25 value='".$ls_cuenta."' readonly>";
					$lo_object[$li_fila][3]="<input name=txtmoncuegas".$li_fila." type=text id=txtmoncuegas".$li_fila." class=sin-borde  style=text-align:right  size=25 onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_moncue."' >
											<input name=txtcodprogas".$li_fila."  type=hidden id=txtcodprogas".$li_fila."  value='".$ls_codprogas."'>";
					/*$lo_object[$li_fila][4]="<a href=javascript:ue_delete_cuenta_gasto('".$li_fila."','".$as_tipo."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>".
											"<input name=txtcodprogas".$li_fila."  type=hidden id=txtcodprogas".$li_fila."  value='".$ls_programatica."'>";*/
				}
			}
		}
		$ai_total=$li_total+1;
		$lo_object[$ai_total][1]="<input name=txtprogramaticagas".$ai_total." type=text id=txtprogramaticagas".$ai_total." class=sin-borde  style=text-align:center size=45 value='' readonly>"."<input name=txtestclagas".$li_fila."  type=hidden size='2' id=txtestclagas".$li_fila."  value=''>";;
		$lo_object[$ai_total][2]="<input name=txtcuentagas".$ai_total."       type=text id=txtcuentagas".$ai_total."       class=sin-borde  style=text-align:center size=25 value='' readonly>";
		$lo_object[$ai_total][3]="<input name=txtmoncuegas".$ai_total."       type=text id=txtmoncuegas".$ai_total."       class=sin-borde  style=text-align:right  size=25 value='' readonly>";
		$lo_object[$ai_total][4]="<input name=txtcodprogas".$ai_total."       type=hidden id=txtcodprogas".$ai_total."  value=''>";        

		print "<p>&nbsp;</p>";
		print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0' style='display:none'> ";
		print "    <tr>";
		print "      <td  align='left'><a href='javascript:ue_catalogo_cuentas_spg();'><img src='../shared/imagebank/tools/nuevo.gif' width='20' height='20' border='0' title='Agregar Cuenta'>Agregar Cuenta</a>&nbsp;&nbsp;</td>";
		print "    </tr>";
		print "  </table>";
		$io_grid->makegrid($ai_total,$lo_title,$lo_object,840,"Cuentas","gridcuentas");
		unset($io_dscuentas);
		unset($io_solicitud);
	}// end function uf_print_cuentas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cierrecuentas_cargo($ai_total,$as_cargarcargos,$as_tipo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_cierrecuentas_cargo
		//		   Access: private
		//	    Arguments: ai_total  // Total de filas a imprimir
		//                 as_cargarcargos // Si cargamos los cargos ó solo pintamos
		//                 as_tipo // Tipo de SEP si es de bienes ó de servicios
		//	  Description: Método que imprime el grid de las cuentas presupuestarias de los cargos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Modificado Por: Ing. Yozelin Barragan
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 12/05/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep,$la_cuentacargo,$li_estmodest,$li_loncodestpro1,$li_loncodestpro2,$li_loncodestpro3,
	 	       $li_loncodestpro4,$li_loncodestpro5,$ls_coduniadm;
		require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
		require_once("sigesp_sep_c_solicitud.php");
		$io_solicitud=new sigesp_sep_c_solicitud("../../");
		$io_dscuentas=new class_datastore();
		$ls_estceniva=$_SESSION["la_empresa"]["estceniva"];
		if($ls_estceniva=="1")
		{
			$arrResultado= $io_solicitud->uf_load_estructura_central($ls_coduniadm,$ls_codestprocen1,$ls_codestprocen2,$ls_codestprocen3,$ls_codestprocen4,$ls_codestprocen5,$ls_esclacen);
			$lb_valido=$arrResultado["lb_valido"];
			$ls_codestprocen1=$arrResultado["as_codestprocen1"];
			$ls_codestprocen2=$arrResultado["as_codestprocen2"];
			$ls_codestprocen3=$arrResultado["as_codestprocen3"];
			$ls_codestprocen4=$arrResultado["as_codestprocen4"];
			$ls_codestprocen5=$arrResultado["as_codestprocen5"];
			$ls_esclacen=$arrResultado["as_esclacen"];
			$ls_codestprocen=$ls_codestprocen1.$ls_codestprocen2.$ls_codestprocen3.$ls_codestprocen4.$ls_codestprocen5;
		}
		// Titulos el Grid
		$lo_title[1]="Cr&eacute;dito";
		$lo_title[2]="Estructura Presupuestaria";
		$lo_title[3]="Cuenta";
		$lo_title[4]="Monto";
		//$lo_title[5]=""; 
		$ls_codpro="";
		if($as_cargarcargos=="0")
		{
			// Recorrido del Grid de Cuentas Presupuestarias del Cargo
			for($li_fila=1;$li_fila<=$ai_total;$li_fila++)
			{ 
				$ls_cargo= trim($io_funciones_sep->uf_obtenervalor("txtcodcar".$li_fila,""));
				$li_moncue= $io_funciones_sep->uf_obtenervalor("txtmoncar".$li_fila,""); 
				$ls_cuenta= trim($io_funciones_sep->uf_obtenervalor("txtcodspgcre".$li_fila,""));
				$li_moncue = str_replace(".","",$li_moncue);
				$li_moncue = str_replace(",",".",$li_moncue);
				
				if(($ls_estceniva=="1")&&($ls_codestprocen!=""))
				{
					$ls_codpro= $ls_codestprocen;
					$ls_codestpro1 = $ls_codestprocen1; 
					$ls_codestpro2 = $ls_codestprocen2; 
					$ls_codestpro3 = $ls_codestprocen3; 
					$ls_codestpro4 = $ls_codestprocen4; 
					$ls_codestpro5 = $ls_codestprocen5;	
					$ls_estcla=$ls_esclacen;
				}
				else
				{
					$ls_codpro= $io_funciones_sep->uf_obtenervalor("txtcodgascre".$li_fila,"");
					$ls_estcla= $io_funciones_sep->uf_obtenervalor("txtstatuscre".$li_fila,"");
					$ls_codestpro1 = substr($ls_codpro,0,25); 
					$ls_codestpro2 = substr($ls_codpro,25,25); 
					$ls_codestpro3 = substr($ls_codpro,50,25); 
					$ls_codestpro4 = substr($ls_codpro,75,25); 
					$ls_codestpro5 = substr($ls_codpro,100,25);	
				}
				if($ls_cuenta!="")
				{
					$valores["codcargo"]=$ls_cargo;
					$valores["cuentacar"]=$ls_cuenta;
					$valores["estcla"]=$ls_estcla;
					$valores["codestpro1"]=$ls_codestpro1;
					$valores["codestpro2"]=$ls_codestpro2;
					$valores["codestpro3"]=$ls_codestpro3;
					$valores["codestpro4"]=$ls_codestpro4;
					$valores["codestpro5"]=$ls_codestpro5;
					$ll_row_found=$io_dscuentas->findValues($valores,"codcargo") ;
					if($ll_row_found>0)
					{  
						$ldec_monto=0;
						$ldec_monto=$io_dscuentas->getValue("moncuecar",$ll_row_found);
						$ldec_monto=$ldec_monto + $li_moncue;
						$io_dscuentas->updateRow("moncuecar",$ldec_monto,$ll_row_found);	
					}
					else
					{
						$io_dscuentas->insertRow("codcargo",$ls_cargo);			
						$io_dscuentas->insertRow("codprocar",$ls_codpro);			
						$io_dscuentas->insertRow("cuentacar",$ls_cuenta);			
						$io_dscuentas->insertRow("moncuecar",$li_moncue);
						$io_dscuentas->insertRow("estcla",$ls_estcla);
						$io_dscuentas->insertRow("codestpro1",$ls_codestpro1);
						$io_dscuentas->insertRow("codestpro2",$ls_codestpro2);
						$io_dscuentas->insertRow("codestpro3",$ls_codestpro3);
						$io_dscuentas->insertRow("codestpro4",$ls_codestpro4);
						$io_dscuentas->insertRow("codestpro5",$ls_codestpro5);	
					}		
				}
			}
		}
		else
		{	// si los cargos se deben cargar recorremos el arreglo de cuentas
			// que se lleno con los cargos 
			$li_cuenta=count((array)$la_cuentacargo)-1;
			for($li_fila=1;($li_fila<=$li_cuenta);$li_fila++)
			{
				$ls_cargo = trim($la_cuentacargo[$li_fila]["cargo"]);
				$ls_cuenta = trim($la_cuentacargo[$li_fila]["cuenta"]);
				$li_moncue = $la_cuentacargo[$li_fila]["monto"];
				if($ls_estceniva=="1")
				{
					$ls_programatica = $ls_codestprocen;
					$ls_codestpro1 = $ls_codestprocen1; 
					$ls_codestpro2 = $ls_codestprocen2; 
					$ls_codestpro3 = $ls_codestprocen3; 
					$ls_codestpro4 = $ls_codestprocen4; 
					$ls_codestpro5 = $ls_codestprocen5;	
					$ls_estcla=$ls_esclacen;
				}
				else
				{
					$ls_programatica = trim($la_cuentacargo[$li_fila]["programatica"]);
					$ls_codestpro1 = substr($ls_programatica,0,25);
					$ls_codestpro2 = substr($ls_programatica,25,25);
					$ls_codestpro3 = substr($ls_programatica,50,25);
					$ls_codestpro4 = substr($ls_programatica,75,25);
					$ls_codestpro5 = substr($ls_programatica,100,25); 
					$ls_estcla       = trim($la_cuentacargo[$li_fila]["estcla"]);
				}
				if($ls_cuenta!="")
				{
					$valores["codcargo"]=$ls_cargo;
					$valores["cuentacar"]=$ls_cuenta;
					$valores["estcla"]=$ls_estcla;
					$valores["codestpro1"]=$ls_codestpro1;
					$valores["codestpro2"]=$ls_codestpro2;
					$valores["codestpro3"]=$ls_codestpro3;
					$valores["codestpro4"]=$ls_codestpro4;
					$valores["codestpro5"]=$ls_codestpro5;
					$ll_row_found=$io_dscuentas->findValues($valores,"codcargo") ;
					if($ll_row_found>0)
					{  
						$ldec_monto=0;
						$ldec_monto=$io_dscuentas->getValue("moncuecar",$ll_row_found);
						$ldec_monto=$ldec_monto + $li_moncue;
						$io_dscuentas->updateRow("moncuecar",$ldec_monto,$ll_row_found);	
					}
					else
					{
						$io_dscuentas->insertRow("codcargo",$ls_cargo);			
						$io_dscuentas->insertRow("codprocar",$ls_programatica);			
						$io_dscuentas->insertRow("cuentacar",$ls_cuenta);			
						$io_dscuentas->insertRow("moncuecar",$li_moncue);
						$io_dscuentas->insertRow("estcla",$ls_estcla);
						$io_dscuentas->insertRow("codestpro1",$ls_codestpro1);
						$io_dscuentas->insertRow("codestpro2",$ls_codestpro2);
						$io_dscuentas->insertRow("codestpro3",$ls_codestpro3);
						$io_dscuentas->insertRow("codestpro4",$ls_codestpro4);
						$io_dscuentas->insertRow("codestpro5",$ls_codestpro5);
					}
				}			
			}
		} 
		// Agrupamos las cuentas por programatica y cuenta
		$io_dscuentas->group_by(array('0'=>'codcargo','1'=>'codestpro1','2'=>'codestpro2','3'=>'codestpro3','4'=>'codestpro4','5'=>'codestpro5',
		                              '6'=>'estcla','7'=>'cuentacar'),array('0'=>'moncuecar'),'moncuecar');
		$li_total=$io_dscuentas->getRowCount('codcargo');	
		// Recorremos el data stored de cuentas que se lleno y se agrupo anteriormente
		for($li_fila=1;$li_fila<=$li_total;$li_fila++)
		{ 
			$ls_cargo     = $io_dscuentas->getValue('codcargo',$li_fila);
			$ls_codpro    = $io_dscuentas->getValue('codprocar',$li_fila);
			$ls_cuenta    = $io_dscuentas->getValue('cuentacar',$li_fila);
			$li_moncue    = number_format($io_dscuentas->getValue('moncuecar',$li_fila),2,",",".");

			$ls_codestpro = "";
			if (!empty($ls_codpro))
			{
				 $ls_codestpro1 = substr($ls_codpro,0,25);
				 $ls_codestpro2 = substr($ls_codpro,25,25);
				 $ls_codestpro3 = substr($ls_codpro,50,25);
				 $ls_codestpro4 = substr($ls_codpro,75,25);
				 $ls_codestpro5 = substr($ls_codpro,100,25);
				 $ls_codestpro1 = substr($ls_codestpro1,-$li_loncodestpro1);
			 	 $ls_codestpro2 = substr($ls_codestpro2,-$li_loncodestpro2);
				 $ls_codestpro3 = substr($ls_codestpro3,-$li_loncodestpro3);
				 $ls_codestpro  = $ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3;
			} 
			if ($li_estmodest==2)
			{
				if (!empty($ls_codpro))
				{
					$ls_codestpro4 = substr($ls_codestpro4,-$li_loncodestpro4);
					$ls_codestpro5 = substr($ls_codestpro5,-$li_loncodestpro5);
					$ls_codestpro  = $ls_codestpro.'-'.$ls_codestpro4.'-'.$ls_codestpro5;
				}
			}
			$ls_estcla = $io_dscuentas->getValue('estcla',$li_fila);
			if($ls_cuenta!="")
			{
				$ls_tipsep = $io_funciones_sep->uf_obtenervalor("tipsep","");
				$ls_sccuenta='';
				if($ls_tipsep!="S")
				{
					$arrResultado= $io_solicitud->uf_select_cuentacontable(str_pad($ls_codestpro1,25,'0',0),str_pad($ls_codestpro2,25,'0',0),str_pad($ls_codestpro3,25,'0',0),str_pad($ls_codestpro4,25,'0',0),str_pad($ls_codestpro5,25,'0',0),$ls_cuenta,$ls_estcla,$ls_sccuenta);
					$lb_valido = $arrResultado['lb_valido'];
					$ls_sccuenta = $arrResultado['as_sccuenta'];					
				}
				else
				{
					$lb_valido=true;
				}
				if($lb_valido)
				{
					$lo_object[$li_fila][1]="<input name=txtcodcargo".$li_fila." type=text id=txtcodcargo".$li_fila." class=sin-borde  style=text-align:center size=12 value='".$ls_cargo."' readonly>";
					$lo_object[$li_fila][2]="<input name=txtprogramaticacar".$li_fila." type=text id=txtprogramaticacar".$li_fila." class=sin-borde  style=text-align:center size=75 value='".$ls_codestpro."' readonly>";
					$lo_object[$li_fila][3]="<input name=txtcuentacar".$li_fila." type=text id=txtcuentacar".$li_fila." class=sin-borde  style=text-align:center size=25 value='".$ls_cuenta."' readonly>";
					$lo_object[$li_fila][4]="<input name=txtmoncuecar".$li_fila." type=text id=txtmoncuecar".$li_fila." class=sin-borde  style=text-align:right  size=25 onKeyPress=return(ue_formatonumero(this,'.',',',event)); value='".$li_moncue."' readonly>".
											"<input name=txtcodprocar".$li_fila."  type=hidden id=txtcodprocar".$li_fila."  value='".$ls_codpro."'>".
											"<input name=estclacar".$li_fila."  type=hidden id=estclacar".$li_fila."  value='".$ls_estcla."'>";
				}
										
			}
		}
		$ai_total=$li_total+1;
		$lo_object[$ai_total][1]="<input name=txtcodcargo".$ai_total." type=text id=txtcodcargo".$ai_total." class=sin-borde  style=text-align:center size=12 value='' readonly>";
		$lo_object[$ai_total][2]="<input name=txtprogramaticacar".$ai_total." type=text id=txtprogramaticacar".$ai_total." class=sin-borde  style=text-align:center size=75 value='' readonly>";
		$lo_object[$ai_total][3]="<input name=txtcuentacar".$ai_total."       type=text id=txtcuentacar".$ai_total."       class=sin-borde  style=text-align:center size=25 value='' readonly>";
		$lo_object[$ai_total][4]="<input name=txtmoncuecar".$ai_total."       type=text id=txtmoncuecar".$ai_total."       class=sin-borde  style=text-align:right  size=25 value='' readonly>";
		$lo_object[$ai_total][5]="<input name=txtcodprocar".$ai_total."       type=hidden id=txtcodprocar".$ai_total."  value=''><input name=estclacar".$li_fila."  type=hidden id=estclacar".$li_fila."  value=''>";        

		print "<p>&nbsp;</p>";
		print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
		print "    <tr>";
		print "    </tr>";
		print "  </table>";
		$io_grid->makegrid($ai_total,$lo_title,$lo_object,840,"Cuentas Otros Cr&eacute;ditos","gridcuentascargos");
		unset($io_dscuentas);
		unset($io_solicitud);
	}// end function uf_print_cuentas_cargo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_usuarios($as_numsol)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_bienes
		//		   Access: private
		//	    Arguments: ai_total  // Total de filas a imprimir
		//	  Description: Método que imprime el grid de los Bienes
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_sep, $io_solicitud,$li_numdecper;
		$i=0;

		$arrResultado=$io_solicitud->uf_load_usuarios($as_numsol);
		$as_codusu=$arrResultado["as_codusu"];
		$as_codaprusu=$arrResultado["as_codaprusu"];
		print "<p>&nbsp;</p>";
		print "  <table width='840' border='0' align='center' cellpadding='0' cellspacing='0'";
		print "    <tr>";
		print " 	  <td height='22' align='left' class=texto-azul>Elaborado Por:  $as_codusu </td>";
		print "    </tr>";
		print "    <tr>";
		print " 	  <td height='22' align='left' class=texto-rojo>Aprobado Por:  $as_codaprusu </td>";
		print "    </tr>";
		print "  </table>";
	}// end function uf_print_bienes
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_cargos($ai_total,$as_formapago,$as_tipconpro,$as_tipo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_cargos
		//		   Access: private
		//	    Arguments: as_subtotal  // sub total de la sep
		//                 as_formapago // forma de Pago
		//                 as_tipo // Tipo de SEP si es de bienes ó de servicios
		//	  Description: Método que busca los cargos segun su forma de pago y el subtotal
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 12/10/2017								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones_sep, $ls_codestpre, $ls_estclauni, $arr_cargosprocesados,$ls_codtipsep,$io_solicitud,$li_totalcargos,$ls_cargarcargos,$ls_cambioest;

        $ls_estdifiva=$io_solicitud->uf_validar_diferencial_iva($ls_codtipsep);//verifica si tiene permiso para modificar las partidas

		if($as_tipconpro!="F")
		{  
			$li_subtotalsep = 0;
			$li_nrocargos=0;
			switch ($as_tipo)
			{
				case "B":
					for($li_fila=1;$li_fila<=$ai_total;$li_fila++)
					{
						$ls_codart    = trim($io_funciones_sep->uf_obtenervalor("txtcodart".$li_fila,""));
						$li_subtotart = $io_funciones_sep->uf_obtenervalor("txtsubtotart".$li_fila,"0,00");
						$li_subtotart = str_replace('.','',$li_subtotart);
						$li_subtotart = str_replace(',','.',$li_subtotart);
						if (trim($ls_codart)<>'')
						{
							$arr_cargostemp[$li_nrocargos]['codigo'] = $ls_codart;
							$arr_cargostemp[$li_nrocargos]['subtotal'] = $li_subtotart;
							$arr_cargostemp[$li_nrocargos]['cargo'] = 0;
							$arr_cargostemp[$li_nrocargos]['total'] = $li_subtotart;
							$arr_cargostemp[$li_nrocargos]['codcar'] = '';
							$arr_cargostemp[$li_nrocargos]['dencar'] = '';
							$arr_cargostemp[$li_nrocargos]['spg_cuenta'] = '';
							$arr_cargostemp[$li_nrocargos]['formula'] = '';
							$arr_cargostemp[$li_nrocargos]['existecuenta'] = '';
							$li_nrocargos++;
						}				
						$li_subtotalsep = $li_subtotalsep + $li_subtotart;
					}
					break;
				case "S":
					for($li_fila=1;$li_fila<=$ai_total;$li_fila++)
					{
						$ls_codser=$io_funciones_sep->uf_obtenervalor("txtcodser".$li_fila,"");
						$li_subtotser=$io_funciones_sep->uf_obtenervalor("txtsubtotser".$li_fila,"0,00");
						$li_subtotser = str_replace('.','',$li_subtotser);
						$li_subtotser = str_replace(',','.',$li_subtotser);
						if (trim($ls_codser)<>'')
						{
							$arr_cargostemp[$li_nrocargos]['codigo'] = $ls_codser;
							$arr_cargostemp[$li_nrocargos]['subtotal'] = $li_subtotser;
							$arr_cargostemp[$li_nrocargos]['cargo'] = 0;
							$arr_cargostemp[$li_nrocargos]['total'] = $li_subtotser;
							$arr_cargostemp[$li_nrocargos]['codcar'] = '';
							$arr_cargostemp[$li_nrocargos]['dencar'] = '';
							$arr_cargostemp[$li_nrocargos]['spg_cuenta'] = '';
							$arr_cargostemp[$li_nrocargos]['formula'] = '';
							$arr_cargostemp[$li_nrocargos]['existecuenta'] = '';
							$li_nrocargos++;
						}				
						$li_subtotalsep = $li_subtotalsep + $li_subtotser;
					}
					break;
				case "O":
					for($li_fila=1;$li_fila<=$ai_total;$li_fila++)
					{
						$ls_codcon	  = $io_funciones_sep->uf_obtenervalor("txtcodcon".$li_fila,"");
						$li_subtotcon = $io_funciones_sep->uf_obtenervalor("txtsubtotcon".$li_fila,"0,00");
						$li_subtotcon = str_replace('.','',$li_subtotcon);
						$li_subtotcon = str_replace(',','.',$li_subtotcon);
						if (trim($ls_codcon)<>'')
						{
							$arr_cargostemp[$li_nrocargos]['codigo'] = $ls_codcon;
							$arr_cargostemp[$li_nrocargos]['subtotal'] = $li_subtotcon;
							$arr_cargostemp[$li_nrocargos]['cargo'] = 0;
							$arr_cargostemp[$li_nrocargos]['total'] = $li_subtotcon;
							$arr_cargostemp[$li_nrocargos]['codcar'] = '';
							$arr_cargostemp[$li_nrocargos]['dencar'] = '';
							$arr_cargostemp[$li_nrocargos]['spg_cuenta'] = '';
							$arr_cargostemp[$li_nrocargos]['formula'] = '';
							$arr_cargostemp[$li_nrocargos]['existecuenta'] = '';
							$li_nrocargos++;
						}				
						$li_subtotalsep = $li_subtotalsep + $li_subtotcon;
					}
					break;
			}
		//	$ls_estdifiva="1";
			if($ls_estdifiva!="1")
			{
				if($ls_cambioest!="1")
				{
					require_once("sigesp_sep_c_solicitud.php");
					$io_solicitud=new sigesp_sep_c_solicitud("../../");
					require_once("../../shared/class_folder/evaluate_formula.php");
					$io_eval=new evaluate_formula();
					$li_i=0;
					$arr_cargosprocesados=array();
					for($li_fila=0;$li_fila<$li_nrocargos;$li_fila++)
					{
						$ls_codigo= $arr_cargostemp[$li_fila]['codigo'];
						$li_tipcargo = 0;
						if ($as_formapago == 'E')
						{
							if ($li_subtotalsep<=2000000)
							{
								$li_tipcargo = 2;
							}
							else
							{
								$li_tipcargo = 3;
							}
						}
						else
						{
							$li_tipcargo = 1;
						}
						switch ($as_tipo)
						{
							case "B":
								$rs_data = $io_solicitud->uf_procesar_cargosbienes($ls_codigo,$ls_codestpre,$ls_estclauni,$li_tipcargo);
								break;
							case "S":
								$rs_data = $io_solicitud->uf_procesar_cargosservicios($ls_codigo,$ls_codestpre,$ls_estclauni,$li_tipcargo);
								break;
							case "O":
								$rs_data = $io_solicitud->uf_procesar_cargosconceptos($ls_codigo,$ls_codestpre,$ls_estclauni,$li_tipcargo);
								break;
						}
						while(!$rs_data->EOF)	  
						{
							$lb_existecargo  = true;
							$ls_codservic    = $rs_data->fields["codigo"];
							$ls_codcar       = $rs_data->fields["codcar"];
							$ls_dencar       = $rs_data->fields["dencar"];
							$ls_spg_cuenta   = trim($rs_data->fields["spg_cuenta"]);
							$ls_formula      = $rs_data->fields["formula"];
							$li_bascar       = $arr_cargostemp[$li_fila]['subtotal'];
							$li_moncar       = 0;
							$li_subcargo     = 0;
							$ls_existecuenta = $rs_data->fields["existecuenta"];
		
							$arr_cargosprocesados[$li_i]['codigo'] = $ls_codservic;
							$arr_cargosprocesados[$li_i]['codcar'] = $ls_codcar;
							$arr_cargosprocesados[$li_i]['dencar'] = $ls_dencar;
							$arr_cargosprocesados[$li_i]['spg_cuenta'] = $ls_spg_cuenta;
							$arr_cargosprocesados[$li_i]['formula'] = $ls_formula;
							$arr_cargosprocesados[$li_i]['existecuenta'] = $ls_existecuenta;
							$arrResultado=$io_eval->uf_evaluar($ls_formula,$li_bascar,$lb_valido);
							$li_moncar=number_format($arrResultado['result'],2,'.','');
							$arr_cargosprocesados[$li_i]['subtotal'] = $li_bascar;
							$arr_cargosprocesados[$li_i]['cargo'] = $li_moncar;
							$arr_cargosprocesados[$li_i]['total'] = $li_bascar+$li_moncar;
							$li_i++;
							$rs_data->MoveNext();
						}
					}
				}//if($ls_cambioest!="1")
				else
				{
					$li_i=0;
					for($li_filacargos=1;$li_filacargos<=$li_totalcargos;$li_filacargos++)
					{
						$ls_codservic  = $io_funciones_sep->uf_obtenervalor("txtcodservic".$li_filacargos,"");
						$ls_codcar	   = $io_funciones_sep->uf_obtenervalor("txtcodcar".$li_filacargos,"");
						$ls_dencar	   = $io_funciones_sep->uf_obtenervalor("txtdencar".$li_filacargos,"");
						$li_bascar	   = $io_funciones_sep->uf_obtenervalor("txtbascar".$li_filacargos,"");
						$li_moncar	   = $io_funciones_sep->uf_obtenervalor("txtmoncar".$li_filacargos,"");
						$li_subcargo   = $io_funciones_sep->uf_obtenervalor("txtsubcargo".$li_filacargos,"");
						$ls_spg_cuenta = $io_funciones_sep->uf_obtenervalor("cuentacargo".$li_filacargos,"");
						$ls_formula    = $io_funciones_sep->uf_obtenervalor("formulacargo".$li_filacargos,"");
						$ls_codpro	   = trim($io_funciones_sep->uf_obtenervalor("txtcodgascre".$li_filacargos,"")); 
						$ls_cuenta	   = trim($io_funciones_sep->uf_obtenervalor("txtcodspgcre".$li_filacargos,""));
						$ls_estcla	   = trim($io_funciones_sep->uf_obtenervalor("txtstatuscre".$li_filacargos,""));
						$ls_existecuenta = $io_solicitud->uf_load_existecuenta($ls_codpro,$ls_spg_cuenta,$ls_estclauni);
						$li_bascar = str_replace('.','',$li_bascar);
						$li_bascar = str_replace(',','.',$li_bascar);
						$li_moncar = str_replace('.','',$li_moncar);
						$li_moncar = str_replace(',','.',$li_moncar);
									
						$arr_cargosprocesados[$li_i]['codigo'] = $ls_codservic;
						$arr_cargosprocesados[$li_i]['codcar'] = $ls_codcar;
						$arr_cargosprocesados[$li_i]['dencar'] = $ls_dencar;
						$arr_cargosprocesados[$li_i]['spg_cuenta'] = $ls_spg_cuenta;
						$arr_cargosprocesados[$li_i]['formula'] = $ls_formula;
						$arr_cargosprocesados[$li_i]['existecuenta'] = $ls_existecuenta;
						$arr_cargosprocesados[$li_i]['subtotal'] = $li_bascar;
						$arr_cargosprocesados[$li_i]['cargo'] = $li_moncar;
						$arr_cargosprocesados[$li_i]['total'] = $li_bascar+$li_moncar;
						$arr_cargosprocesados[$li_i]['programatica'] = $ls_codpro;
						$arr_cargosprocesados[$li_i]['estcla'] = $ls_estcla;
						$li_i++;
					}
				}
			}
			else
			{
				$li_i=0;
				for($li_filacargos=1;$li_filacargos<=$li_totalcargos;$li_filacargos++)
				{
					$ls_codservic  = $io_funciones_sep->uf_obtenervalor("txtcodservic".$li_filacargos,"");
					$ls_codcar	   = $io_funciones_sep->uf_obtenervalor("txtcodcar".$li_filacargos,"");
					$ls_dencar	   = $io_funciones_sep->uf_obtenervalor("txtdencar".$li_filacargos,"");
					$li_bascar	   = $io_funciones_sep->uf_obtenervalor("txtbascar".$li_filacargos,"");
					$li_moncar	   = $io_funciones_sep->uf_obtenervalor("txtmoncar".$li_filacargos,"");
					$li_subcargo   = $io_funciones_sep->uf_obtenervalor("txtsubcargo".$li_filacargos,"");
					$ls_spg_cuenta = $io_funciones_sep->uf_obtenervalor("cuentacargo".$li_filacargos,"");
					$ls_formula    = $io_funciones_sep->uf_obtenervalor("formulacargo".$li_filacargos,"");
					$ls_codpro	   = trim($io_funciones_sep->uf_obtenervalor("txtcodgascre".$li_filacargos,"")); 
					$ls_cuenta	   = trim($io_funciones_sep->uf_obtenervalor("txtcodspgcre".$li_filacargos,""));
					$ls_estcla	   = trim($io_funciones_sep->uf_obtenervalor("txtstatuscre".$li_filacargos,""));
					$ls_existecuenta = $io_solicitud->uf_load_existecuenta($ls_codestpre,$ls_spg_cuenta,$ls_estclauni);
					$li_bascar = str_replace('.','',$li_bascar);
					$li_bascar = str_replace(',','.',$li_bascar);
					$li_moncar = str_replace('.','',$li_moncar);
					$li_moncar = str_replace(',','.',$li_moncar);
					
					$arr_cargosprocesados[$li_i]['codigo'] = $ls_codservic;
					$arr_cargosprocesados[$li_i]['codcar'] = $ls_codcar;
					$arr_cargosprocesados[$li_i]['dencar'] = $ls_dencar;
					$arr_cargosprocesados[$li_i]['spg_cuenta'] = $ls_spg_cuenta;
					$arr_cargosprocesados[$li_i]['formula'] = $ls_formula;
					$arr_cargosprocesados[$li_i]['existecuenta'] = $ls_existecuenta;
					$arr_cargosprocesados[$li_i]['subtotal'] = $li_bascar;
					$arr_cargosprocesados[$li_i]['cargo'] = $li_moncar;
					$arr_cargosprocesados[$li_i]['total'] = $li_moncar;
					$li_i++;
				}
				
			}
		}//$as_tipconpro!="F"
		
	}// end function uf_procesar_cargos
	//-----------------------------------------------------------------------------------------------------------------------------------

?>