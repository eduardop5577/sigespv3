<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
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
	require_once("class_funciones_siv.php");
	$io_funciones_siv=new class_funciones_siv();
	require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
	$io_dscuentas=new class_datastore(); // Datastored de cuentas contables
	// proceso a ejecutar
	$ls_proceso=$io_funciones_siv->uf_obtenervalor("proceso","");
	// total de filas de articulos salientes
	$li_totrowartsal=$io_funciones_siv->uf_obtenervalor("totartsal",1);
	// total de filas de articulos salientes
	$li_totrowartent=$io_funciones_siv->uf_obtenervalor("totartent",1);
	// total 
	$li_total=$io_funciones_siv->uf_obtenervalor("total","0,00");
	// Codigo de tipo de articulo
	$ls_codtipart=$io_funciones_siv->uf_obtenervalor("codtipart","");
	// Codigo de tipo de articulo
	$ls_totartreq=$io_funciones_siv->uf_obtenervalor("totartreq",0);
	// Codigo de tipo de articulo
	$ls_codalm=$io_funciones_siv->uf_obtenervalor("codalm","");
	// Codigo de tipo de articulo
	$ls_totpaqreq=$io_funciones_siv->uf_obtenervalor("totpaqreq",0);
	// 
	$ls_codemppro=$io_funciones_siv->uf_obtenervalor("codemppro","");
	// 
	$ls_denartemp=$io_funciones_siv->uf_obtenervalor("denartemp","");
	switch($ls_proceso)
	{
		case "LIMPIAR":
			uf_print_articulos($li_totrowartsal,$li_totrowartent,$ls_totpaqreq,$li_total);
			break;

		case "DISPONIBLES":
			uf_load_articulosdisponibles($ls_codtipart,$ls_totartreq,$ls_codalm);
			break;

		case "SALIDA":
			uf_print_articulos($li_totrowartsal,$li_totrowartent,$ls_totpaqreq,$li_total);
			break;
		case "LOADARTICULOS":
			uf_load_articulos($ls_codemppro,$ls_denartemp);
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_articulos($ai_totrowsal,$ai_totrowent,$ls_totpaqreq,$ai_total)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_recepciones
		//		   Access: private
		//	    Arguments: ai_totrowrecepciones // Total de filas de recepciones de documentos
		//				   ai_total             // Monto total
		//	  Description: Método que imprime el grid de las cuentas recepciones de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 19/04/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_siv, $io_dscuentas;
		// Titulos el Grid
		$lo_titlesal[1]="Articulo";
		$lo_titlesal[2]="Unidad de Medida";
		$lo_titlesal[3]="Cantidad";
		$lo_titlesal[4]="Costo Unitario";
		$lo_titlesal[5]="Costo Total"; 
		$lo_titlesal[6]=" "; 
		$li_cantotal=0;
		// Recorrido del Grid de Articulos
		$li_montosal=0;
		$ai_total=str_replace(".","",$ai_total);
		$ai_total=str_replace(",",".",$ai_total);
		$ls_totpaqreq=str_replace(".","",$ls_totpaqreq);
		$ls_totpaqreq=str_replace(",",".",$ls_totpaqreq);
		for($li_fila=1;$li_fila<$ai_totrowsal;$li_fila++)
		{
			$ls_codart=trim($io_funciones_siv->uf_obtenervalor("txtcodart".$li_fila,""));
			$ls_denart=trim($io_funciones_siv->uf_obtenervalor("txtdenart".$li_fila,""));
			//$ls_codunimed=trim($io_funciones_cxp->uf_obtenervalor("txtcodunimed".$li_fila,""));
			$ls_denunimed=trim($io_funciones_siv->uf_obtenervalor("txtdenunimed".$li_fila,""));
			$li_canart=trim($io_funciones_siv->uf_obtenervalor("txtcanart".$li_fila,0));
			$li_cosuni=trim($io_funciones_siv->uf_obtenervalor("txtcosuni".$li_fila,0));
			$li_cossubtotsal=$io_funciones_siv->uf_obtenervalor("txtcossubtotsal".$li_fila,0);
			$li_monto=str_replace(".","",$li_cossubtotsal);
			$li_monto=str_replace(",",".",$li_monto);
			$li_montosal=$li_montosal + $li_monto;
			$li_cantidad=str_replace(".","",$li_canart);
			$li_cantidad=str_replace(",",".",$li_cantidad);

			$li_cantotal=$li_cantotal+$li_cantidad;

			$lo_object[$li_fila][1]="<input name=txtdenart".$li_fila." type=text id=txtdenart".$li_fila."   class=sin-borde  style=text-align:center size=40 value='".$ls_denart."' readonly>";
			$lo_object[$li_fila][2]="<input name=txtdenunimed".$li_fila." type=text id=txtdenunimed".$li_fila."   class=sin-borde  style=text-align:center size=15 value='UNIDAD(ES)' readonly>";
			$lo_object[$li_fila][3]="<input name=txtcanart".$li_fila." type=text id=txtcanart".$li_fila."   class=sin-borde  style=text-align:center size=12 value='".$li_canart."' readonly>";
			$lo_object[$li_fila][4]="<input name=txtcosuni".$li_fila." type=text id=txtcosuni".$li_fila."   class=sin-borde  style=text-align:right size=12 value='".$li_cosuni."' readonly>";
			$lo_object[$li_fila][5]="<input name=txtcossubtotsal".$li_fila." type=text id=txtcossubtotsal".$li_fila."   class=sin-borde  style=text-align:right size=12 value='".$li_cossubtotsal."' readonly>";
			$lo_object[$li_fila][6]="<a href=javascript:ue_delete_articulosalida('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>".
									"<input name=txtcodart".$li_fila." type=hidden id=txtcodart".$li_fila." value='".$ls_codart."'>";
		}
			$lo_object[$ai_totrowsal][1]="<input name=txtdenart".$li_fila." type=text id=txtdenart".$li_fila."   class=sin-borde  style=text-align:center size=40 value='' readonly>";
			$lo_object[$ai_totrowsal][2]="<input name=txtdenunimed".$li_fila." type=text id=txtdenunimed".$li_fila."   class=sin-borde  style=text-align:center size=15 value='' readonly>";
			$lo_object[$ai_totrowsal][3]="<input name=txtcanart".$li_fila." type=text id=txtcanart".$li_fila."   class=sin-borde  style=text-align:center size=8 value='' readonly>";
			$lo_object[$ai_totrowsal][4]="<input name=txtcosuni".$li_fila." type=text id=txtcosuni".$li_fila."   class=sin-borde  style=text-align:right size=14 value='' readonly>";
			$lo_object[$ai_totrowsal][5]="<input name=txtcossubtotsal".$li_fila." type=text id=txtcossubtotsal".$li_fila."   class=sin-borde  style=text-align:right size=14 value='' readonly>";
			$lo_object[$ai_totrowsal][6]="<a href=javascript:ue_delete_articulosalida('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>".
									"<input name=txtcodart".$li_fila." type=hidden id=txtcodart".$li_fila." value=''>";
		if($ai_total==0)
		{
			$ai_total=$li_montosal;
		}
		print "  <table width='720' border='0' align='right' cellpadding='0' cellspacing='0' class='celdas-blancas'>";
		print "    <tr>";
		print " 	  <td height='22' align='left'><a href='javascript:ue_catalogoarticulos();'><img src='../shared/imagebank/tools/nuevo.gif' title='Agregar Detalle Articulos' width='20' height='20' border='0'>Agregar Detalle Articulos</a></td>";
		print "    </tr>";
		print "  </table>";
		$io_grid->makegrid($ai_totrowsal,$lo_titlesal,$lo_object,720,"Detalle Articulos Salientes","gridarticulossal");
		print "  <table width='720' border='0' align='right' cellpadding='0' cellspacing='0' class='celdas-blancas'>";
		print "    <tr>";
		print "<td  align='right' width='540'><b>Total&nbsp;&nbsp;</b></td>";
		print "<td  align='left'><input name='txtmontotartsal' type='text' id='txtmontotartsal' size='25' style='text-align:right' value='".number_format($ai_total,2,",",".")."' readonly></td>";
		print "    </tr>";
		print "  </table>";

		
		
		$lo_titleent[1]="Articulo";
		$lo_titleent[2]="Unidad de Medida";
		$lo_titleent[3]="Cantidad";
		$lo_titleent[4]="Costo Unitario";
		$lo_titleent[5]="Costo Total";
		if($ai_total>0)
		{
			$ls_totpaqreq=str_replace(".","",$ls_totpaqreq);
			$ls_totpaqreq=str_replace(",",".",$ls_totpaqreq);
			$li_cosunitario=($ai_total/$ls_totpaqreq);
		}
		else
		{
			$li_cosunitario=0;
		}
		$li_cantotal=number_format($li_cantotal,2,',','.');
		$li_montot=number_format($ai_total,2,",",".");
		$li_cosunitario=number_format($li_cosunitario,2,",",".");
		$ls_totpaqreq=number_format($ls_totpaqreq,2,",",".");
		for($li_fila=1;$li_fila<$ai_totrowent;$li_fila++)
		{
			$ls_codart=trim($io_funciones_siv->uf_obtenervalor("txtcodartent".$li_fila,""));
			$ls_denart=trim($io_funciones_siv->uf_obtenervalor("txtdenartent".$li_fila,""));
			//$ls_codunimed=trim($io_funciones_cxp->uf_obtenervalor("txtcodunimedent".$li_fila,""));
			$ls_denunimed=trim($io_funciones_siv->uf_obtenervalor("txtdenunimedent".$li_fila,""));
			$li_canart=$io_funciones_siv->uf_obtenervalor("txtcanartent".$li_fila,0);
			$li_cosuni=$io_funciones_siv->uf_obtenervalor("txtcosunient".$li_fila,0);
			$li_cossubtotent=$io_funciones_siv->uf_obtenervalor("txtcossubtotent".$li_fila,0);
			$li_monto=str_replace(".","",$li_cossubtotent);
			$li_monto=str_replace(",",".",$li_monto);
			$li_montosal=$li_montosal + $li_monto;

			$lo_object[$li_fila][1]="<input name=txtdenartent".$li_fila." type=text id=txtdenartent".$li_fila."   class=sin-borde  style=text-align:center size=40 value='".$ls_denart."' readonly>";
			$lo_object[$li_fila][2]="<input name=txtdenunimedent".$li_fila." type=text id=txtdenunimedent".$li_fila."   class=sin-borde  style=text-align:center size=15 value='UNIDAD(ES)' readonly>";
			$lo_object[$li_fila][3]="<input name=txtcanartent".$li_fila." type=text id=txtcanartent".$li_fila."   class=sin-borde  style=text-align:center size=12 value='".$ls_totpaqreq."' readonly>";
			$lo_object[$li_fila][4]="<input name=txtcosunient".$li_fila." type=text id=txtcosunient".$li_fila."   class=sin-borde  style=text-align:right size=12 value='".$li_cosunitario."' readonly>";
			$lo_object[$li_fila][5]="<input name=txtcossubtotent".$li_fila." type=text id=txtcossubtotent".$li_fila."   class=sin-borde  style=text-align:right size=12 value='".$li_montot."' readonly>".
									"<input name=txtcodartent".$li_fila." type=hidden id=txtcodartent".$li_fila." value='".$ls_codart."'>";
		}
			$lo_object[$ai_totrowent][1]="<input name=txtdenartent".$li_fila." type=text id=txtdenartent".$li_fila."   class=sin-borde  style=text-align:center size=40 value='' readonly>";
			$lo_object[$ai_totrowent][2]="<input name=txtdenunimed".$li_fila." type=text id=txtdenunimed".$li_fila."   class=sin-borde  style=text-align:center size=15 value='' readonly>";
			$lo_object[$ai_totrowent][3]="<input name=txtcanartent".$li_fila." type=text id=txtcanartent".$li_fila."   class=sin-borde  style=text-align:center size=8 value='' readonly>";
			$lo_object[$ai_totrowent][4]="<input name=txtcosunient".$li_fila." type=text id=txtcosunient".$li_fila."   class=sin-borde  style=text-align:right size=14 value='' readonly>";
			$lo_object[$ai_totrowent][5]="<input name=txtcossubtotent".$li_fila." type=text id=txtcossubtotent".$li_fila."   class=sin-borde  style=text-align:right size=14 value='' readonly>".
									     "<input name=txtcodartent".$li_fila." type=hidden id=txtcodartent".$li_fila." value=''>";
		if($ai_total==0)
		{
			$ai_total=$li_montoent;
		}
		print "<p>&nbsp;</p>";
		print "<p>&nbsp;</p>";
		$io_grid->makegrid($ai_totrowent,$lo_titleent,$lo_object,720,"Detalle Articulos Entrantes","gridarticulosent");
		print "  <table width='720' border='0' align='right' cellpadding='0' cellspacing='0' class='celdas-blancas'>";
		print "    <tr>";
		print "<td  align='right' width='540'><b>Total&nbsp;&nbsp;</b></td>";
		print "<td  align='left'><input name='txtmontotartsal' type='text' id='txtmontotartsal' size='25' style='text-align:right' value='".number_format($ai_total,2,",",".")."' readonly></td>";
		print "    </tr>";
		print "  </table>";

	}// end function uf_print_cuentas_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_articulosdisponibles($as_codtipart,$ai_totartreq,$as_codalm)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_articulosdisponibles
		//		   Access: private
		//	    Arguments: 
		//	  Description: Método que busca las recepciones de documento asociadas y las imprime
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 08/12/2016							Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_cxp;

		// Titulos del Grid
		$lo_title[1]="Articulo";
		$lo_title[2]="Almacen";
		$lo_title[3]="Disponible";
		$lo_title[4]="Costo";
		$lo_title[5]="Total Costo"; 
		$lo_title[6]="Agregar"; 
		$lo_object[0]="";
		require_once("sigesp_siv_c_empaquetado.php");
		$io_siv=new sigesp_siv_c_empaquetado("../../");
		$rs_data = $io_siv->uf_load_articulosdisponibles($as_codtipart,$as_codalm);
		$li_fila=0;
		$ai_totartreq=str_replace(".","",$ai_totartreq);
		$ai_totartreq=str_replace(",",".",$ai_totartreq);							
		$li_montotal=0;
		while($row=$io_siv->io_sql->fetch_row($rs_data))	  
		{
			$ls_codart=trim($row["codart"]);
			$ls_denart=trim($row["denart"]);
			$ls_codalm=rtrim($row["codalm"]);
			$ls_nomfisalm=rtrim($row["nomfisalm"]);
			$li_exiart=$row["existencia"];
			$li_cosart=$row["cosart"];
			if($ai_totartreq<=$li_exiart)
			{
				$li_totcos=$li_cosart*$ai_totartreq;
				$li_totcos=number_format($li_totcos,2,',','.');
				$li_cosart=number_format($li_cosart,2,',','.');
				$li_exiart=number_format($li_exiart,2,',','.');
				$li_fila=$li_fila+1;
				$lo_object[$li_fila][1]="<input name=txtdenart".$li_fila." type=text id=txtdenart".$li_fila."   class=sin-borde  style=text-align:left size=45 value='".$ls_denart."' readonly>";
				$lo_object[$li_fila][2]="<input name=txtnomfisalm".$li_fila." type=text id=txtnomfisalm".$li_fila."   class=sin-borde  style=text-align:left size=20 value='".$ls_nomfisalm."' readonly>";
				$lo_object[$li_fila][3]="<input name=txtexiart".$li_fila." type=text id=txexiart".$li_fila."   class=sin-borde  style=text-align:center size=12 value='".$li_exiart."' readonly>";
				$lo_object[$li_fila][4]="<input name=txtcosart".$li_fila." type=text id=txtcosart".$li_fila."   class=sin-borde  style=text-align:right size=15 value='".$li_cosart."' readonly>";
				$lo_object[$li_fila][5]="<input name=txttotcos".$li_fila." type=text id=txttotcos".$li_fila."   class=sin-borde  style=text-align:right size=15 value='".$li_totcos."' readonly>";
				$lo_object[$li_fila][6]="<a href=javascript:ue_agregar('".$ls_codart."','".$li_fila."','".number_format($ai_totartreq,2,',','.')."');><img src=../shared/imagebank/tools15/aprobado.gif title=Agregar width=15 height=10 border=0></a>".
										"<input name=txtcodart".$li_fila." type=hidden id=txtcodart".$li_fila." value='".$ls_codart."'>".
										"<input name=txtcodalm".$li_fila." type=hidden id=txtcodalm".$li_fila."   class=sin-borde  style=text-align:right size=25 value='".$ls_codalm."' readonly>";
			}
		}
/*		$li_fila=$li_fila+1;
		$lo_object[$li_fila][1]="<input name=txtnumrecdoc".$li_fila." type=text id=txtnumrecdoc".$li_fila."   class=sin-borde  style=text-align:center size=20 readonly>";
		$lo_object[$li_fila][2]="<input name=txtnumexprel".$li_fila." type=text id=txtnumexprel".$li_fila."   class=sin-borde  style=text-align:center size=15 readonly>";
		$lo_object[$li_fila][3]="<input name=txtdentipdoc".$li_fila." type=text id=txtdentipdoc".$li_fila."   class=sin-borde  style=text-align:center size=45 readonly>";
		$lo_object[$li_fila][4]="<input name=txtmontotdoc".$li_fila." type=text id=txtmontotdoc".$li_fila."   class=sin-borde  style=text-align:right size=25  readonly>";
		$lo_object[$li_fila][5]="<a><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>".
											 "<input name=txtcodtipdoc".$li_fila." type=hidden id=txtcodtipdoc".$li_fila.">".
											 "<input name=txtauxpro".$li_fila." type=hidden id=txtauxpro".$li_fila."   class=sin-borde  style=text-align:right size=25  readonly>".
											 "<input name=txtcodproalt".$li_fila." type=hidden id=txtcodproalt".$li_fila."   class=sin-borde  style=text-align:right size=25 readonly>".
											 "<input name=txtauxben".$li_fila." type=hidden id=txtauxben".$li_fila."   class=sin-borde  style=text-align:right size=25  readonly>";
*/		if($ai_total==0)
		{
			$ai_total=$li_montotal;
		}
		print "<p>&nbsp;</p>";
		$io_grid->makegrid($li_fila,$lo_title,$lo_object,720,"Existencias de Articulos","gridarticulos");
	}// end function uf_print_creditos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_articulos($ls_codemppro,$ls_denartemp)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_articulos
		//		   Access: private
		//	    Arguments: ai_totrowrecepciones // Total de filas de recepciones de documentos
		//				   ai_total             // Monto total
		//	  Description: Método que imprime el grid de las cuentas recepciones de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 19/04/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_siv, $io_dscuentas;
		require_once("sigesp_siv_c_empaquetado.php");
		$io_siv=new sigesp_siv_c_empaquetado("../../");
		// Titulos el Grid
		$lo_titlesal[1]="Articulo";
		$lo_titlesal[2]="Unidad de Medida";
		$lo_titlesal[3]="Cantidad";
		$lo_titlesal[4]="Costo Unitario";
		$lo_titlesal[5]="Costo Total"; 
		$lo_titlesal[6]=" "; 

		$li_cantotal=0;
		// Recorrido del Grid de Articulos
		$li_montosal=0;
		$rs_data = $io_siv->uf_load_articulos($ls_codemppro,'S');
		$li_fila=0;
		while($row=$io_siv->io_sql->fetch_row($rs_data))	  
		{
			$ls_codart=trim($row["codart"]);
			$ls_denart=rtrim($row["denart"]);
			$li_canart=trim($row["cantidad"]);
			$li_cosuni=trim($row["cosuni"]);
			$li_cossubtotsal=trim($row["costot"]);

			
			$li_montosal=$li_montosal + $li_cossubtotsal;

			$li_cantotal=$li_cantotal+$li_canart;
			$li_canart=number_format($li_canart,2,',','.');
			$li_cosuni=number_format($li_cosuni,2,',','.');
			$li_cossubtotsal=number_format($li_cossubtotsal,2,',','.');
			$li_fila=$li_fila+1;
			$lo_object[$li_fila][1]="<input name=txtdenart".$li_fila." type=text id=txtdenart".$li_fila."   class=sin-borde  style=text-align:center size=40 value='".$ls_denart."' readonly>";
			$lo_object[$li_fila][2]="<input name=txtdenunimed".$li_fila." type=text id=txtdenunimed".$li_fila."   class=sin-borde  style=text-align:center size=15 value='UNIDAD(ES)' readonly>";
			$lo_object[$li_fila][3]="<input name=txtcanart".$li_fila." type=text id=txtcanart".$li_fila."   class=sin-borde  style=text-align:center size=12 value='".$li_canart."' readonly>";
			$lo_object[$li_fila][4]="<input name=txtcosuni".$li_fila." type=text id=txtcosuni".$li_fila."   class=sin-borde  style=text-align:right size=12 value='".$li_cosuni."' readonly>";
			$lo_object[$li_fila][5]="<input name=txtcossubtotsal".$li_fila." type=text id=txtcossubtotsal".$li_fila."   class=sin-borde  style=text-align:right size=12 value='".$li_cossubtotsal."' readonly>";
			$lo_object[$li_fila][6]="<a href=javascript:ue_delete_articulosalida('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>".
									"<input name=txtcodart".$li_fila." type=hidden id=txtcodart".$li_fila." value='".$ls_codart."'>";
		}
		$ai_totrowsal=$li_fila+1;
			$lo_object[$ai_totrowsal][1]="<input name=txtdenart".$li_fila." type=text id=txtdenart".$li_fila."   class=sin-borde  style=text-align:center size=40 value='' readonly>";
			$lo_object[$ai_totrowsal][2]="<input name=txtdenunimed".$li_fila." type=text id=txtdenunimed".$li_fila."   class=sin-borde  style=text-align:center size=15 value='' readonly>";
			$lo_object[$ai_totrowsal][3]="<input name=txtcanart".$li_fila." type=text id=txtcanart".$li_fila."   class=sin-borde  style=text-align:center size=8 value='' readonly>";
			$lo_object[$ai_totrowsal][4]="<input name=txtcosuni".$li_fila." type=text id=txtcosuni".$li_fila."   class=sin-borde  style=text-align:right size=14 value='' readonly>";
			$lo_object[$ai_totrowsal][5]="<input name=txtcossubtotsal".$li_fila." type=text id=txtcossubtotsal".$li_fila."   class=sin-borde  style=text-align:right size=14 value='' readonly>";
			$lo_object[$ai_totrowsal][6]="<a href=javascript:ue_delete_articulosalida('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>".
									"<input name=txtcodart".$li_fila." type=hidden id=txtcodart".$li_fila." value=''>";
		if($ai_total==0)
		{
			$ai_total=$li_montosal;
		}
		print "  <table width='720' border='0' align='right' cellpadding='0' cellspacing='0' class='celdas-blancas'>";
		print "    <tr>";
		print " 	  <td height='22' align='left'><a href='javascript:ue_catalogoarticulos();'><img src='../shared/imagebank/tools/nuevo.gif' title='Agregar Detalle Articulos' width='20' height='20' border='0'>Agregar Detalle Articulos</a></td>";
		print "    </tr>";
		print "  </table>";
		$io_grid->makegrid($ai_totrowsal,$lo_titlesal,$lo_object,720,"Detalle Articulos Salientes","gridarticulossal");
		print "  <table width='720' border='0' align='right' cellpadding='0' cellspacing='0' class='celdas-blancas'>";
		print "    <tr>";
		print "<td  align='right' width='540'><b>Total&nbsp;&nbsp;</b></td>";
		print "<td  align='left'><input name='txtmontotartsal' type='text' id='txtmontotartsal' size='25' style='text-align:right' value='".number_format($ai_total,2,",",".")."' readonly></td>";
		print "    </tr>";
		print "  </table>";

		
		
		$lo_titleent[1]="Articulo";
		$lo_titleent[2]="Unidad de Medida";
		$lo_titleent[3]="Cantidad";
		$lo_titleent[4]="Costo Unitario";
		$lo_titleent[5]="Costo Total";

		$rs_data = $io_siv->uf_load_articulos($ls_codemppro,'E');
		$li_fila=0;
		while($row=$io_siv->io_sql->fetch_row($rs_data))	  
		{
			$ls_codart=trim($row["codart"]);
			$ls_denart=$ls_denartemp;
			$li_canart=trim($row["cantidad"]);
			$li_cosuni=trim($row["cosuni"]);
			$li_cossubtotsal=trim($row["costot"]);

			
			//$li_montosal=$li_montosal + $li_cossubtotsal;

			//$li_cantotal=$li_cantotal+$li_canart;
			$li_canart=number_format($li_canart,2,',','.');
			$li_cosuni=number_format($li_cosuni,2,',','.');
			$li_cossubtotsal=number_format($li_cossubtotsal,2,',','.');

			$li_fila=$li_fila+1;
			$lo_object[$li_fila][1]="<input name=txtdenartent".$li_fila." type=text id=txtdenartent".$li_fila."   class=sin-borde  style=text-align:center size=40 value='".$ls_denart."' readonly>";
			$lo_object[$li_fila][2]="<input name=txtdenunimedent".$li_fila." type=text id=txtdenunimedent".$li_fila."   class=sin-borde  style=text-align:center size=15 value='UNIDAD(ES)' readonly>";
			$lo_object[$li_fila][3]="<input name=txtcanartent".$li_fila." type=text id=txtcanartent".$li_fila."   class=sin-borde  style=text-align:center size=12 value='".$li_canart."' readonly>";
			$lo_object[$li_fila][4]="<input name=txtcosunient".$li_fila." type=text id=txtcosunient".$li_fila."   class=sin-borde  style=text-align:right size=12 value='".$li_cosuni."' readonly>";
			$lo_object[$li_fila][5]="<input name=txtcossubtotent".$li_fila." type=text id=txtcossubtotent".$li_fila."   class=sin-borde  style=text-align:right size=12 value='".$li_cossubtotsal."' readonly>".
									"<input name=txtcodartent".$li_fila." type=hidden id=txtcodartent".$li_fila." value='".$ls_codart."'>";
		}
		$ai_totrowent=$li_fila+1;
			$lo_object[$ai_totrowent][1]="<input name=txtdenartent".$li_fila." type=text id=txtdenartent".$li_fila."   class=sin-borde  style=text-align:center size=40 value='' readonly>";
			$lo_object[$ai_totrowent][2]="<input name=txtdenunimed".$li_fila." type=text id=txtdenunimed".$li_fila."   class=sin-borde  style=text-align:center size=15 value='' readonly>";
			$lo_object[$ai_totrowent][3]="<input name=txtcanartent".$li_fila." type=text id=txtcanartent".$li_fila."   class=sin-borde  style=text-align:center size=8 value='' readonly>";
			$lo_object[$ai_totrowent][4]="<input name=txtcosunient".$li_fila." type=text id=txtcosunient".$li_fila."   class=sin-borde  style=text-align:right size=14 value='' readonly>";
			$lo_object[$ai_totrowent][5]="<input name=txtcossubtotent".$li_fila." type=text id=txtcossubtotent".$li_fila."   class=sin-borde  style=text-align:right size=14 value='' readonly>".
									     "<input name=txtcodartent".$li_fila." type=hidden id=txtcodartent".$li_fila." value=''>";
		if($ai_total==0)
		{
			$ai_total=$li_montoent;
		}
		print "<p>&nbsp;</p>";
		print "<p>&nbsp;</p>";
		$io_grid->makegrid($ai_totrowent,$lo_titleent,$lo_object,720,"Detalle Articulos Entrantes","gridarticulosent");
		print "  <table width='720' border='0' align='right' cellpadding='0' cellspacing='0' class='celdas-blancas'>";
		print "    <tr>";
		print "<td  align='right' width='540'><b>Total&nbsp;&nbsp;</b></td>";
		print "<td  align='left'><input name='txtmontotartsal' type='text' id='txtmontotartsal' size='25' style='text-align:right' value='".number_format($ai_total,2,",",".")."' readonly></td>";
		print "    </tr>";
		print "  </table>";

	}// end function uf_print_cuentas_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

?>