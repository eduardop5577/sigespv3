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
	$li_totrowart=$io_funciones_siv->uf_obtenervalor("totrowart",1);
	// total de filas de articulos salientes
	$li_totrowartent=$io_funciones_siv->uf_obtenervalor("totartent",1);
	// total 
	$li_total=$io_funciones_siv->uf_obtenervalor("total","0,00");
	// Codigo de tipo de articulo
	$ls_codart=$io_funciones_siv->uf_obtenervalor("codart","");
	// Codigo de tipo de articulo
	$ls_serdes=$io_funciones_siv->uf_obtenervalor("serdes","");
	$ls_serhas=$io_funciones_siv->uf_obtenervalor("serhas","");
	// Codigo de tipo de articulo
	$ls_codalm=$io_funciones_siv->uf_obtenervalor("codalm","");
	// Codigo de tipo de articulo
	$ls_codasi=$io_funciones_siv->uf_obtenervalor("codasi","");
	switch($ls_proceso)
	{
		case "LIMPIAR":
			uf_print_articulos($li_totrowart,$li_totrowartent,$ls_totpaqreq,$li_total);
			break;

		case "DISPONIBLES":
			uf_load_articulosdisponibles($ls_codart,$ls_codalm,$ls_serdes,$ls_serhas);
			break;

		case "SALIDA":
			uf_print_articulos($li_totrowartsal,$li_totrowartent,$ls_totpaqreq,$li_total);
			break;
		case "LOADARTICULOS":
			uf_load_articulos($ls_codasi);
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_articulos($ai_totrow,$ai_totrowent,$ls_totpaqreq,$ai_total)
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
		$lo_titlesal[1]="Codigo";
		$lo_titlesal[2]="Articulo";
		$lo_titlesal[3]="Serial";
		$lo_titlesal[4]="";
		// Recorrido del Grid de Articulos
		for($li_fila=1;$li_fila<$ai_totrow;$li_fila++)
		{
			$ls_codart=trim($io_funciones_siv->uf_obtenervalor("txtcodart".$li_fila,""));
			$ls_denart=trim($io_funciones_siv->uf_obtenervalor("txtdenart".$li_fila,""));
			$ls_coddetart=trim($io_funciones_siv->uf_obtenervalor("txtcoddetart".$li_fila,""));

			$lo_object[$li_fila][1]="<input name=txtcodart".$li_fila." type=text id=txtcodart".$li_fila." class=sin-borde  style=text-align:center size=20  value='".$ls_codart."'>";
			$lo_object[$li_fila][2]="<input name=txtdenart".$li_fila." type=text id=txtdenart".$li_fila."   class=sin-borde  style=text-align:center size=40 value='".$ls_denart."' readonly>";
			$lo_object[$li_fila][3]="<input name=txtcoddetart".$li_fila." type=text id=txtcoddetart".$li_fila." class=sin-borde  style=text-align:center size=15  value='".$ls_coddetart."' readonly>";
			$lo_object[$li_fila][4]="<a href=javascript:ue_delete_articulo('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>";
		}
		$lo_object[$ai_totrow][1]="<input name=txtcodart".$ai_totrow."    type=text id=txtcodart".$ai_totrow."    class=sin-borde  style=text-align:center size=20 value='' readonly>";
		$lo_object[$ai_totrow][2]="<input name=txtdenart".$ai_totrow."    type=text id=txtdenart".$ai_totrow."    class=sin-borde  style=text-align:center size=40 value='' readonly>";
		$lo_object[$ai_totrow][3]="<input name=txtcoddetart".$ai_totrow." type=text id=txtcoddetart".$ai_totrow." class=sin-borde  style=text-align:center size=15 value='' readonly>";
		$lo_object[$ai_totrow][4]="";
		if($ai_total==0)
		{
			$ai_total=$li_montosal;
		}
		print "  <table width='680' border='0' align='right' cellpadding='0' cellspacing='0' class='celdas-blancas'>";
		print "    <tr>";
		print " 	  <td height='22' align='left'><a href='javascript:ue_catalogoarticulos();'><img src='../shared/imagebank/tools/nuevo.gif' title='Agregar Materiales' width='20' height='20' border='0'>Agregar Detalle Articulos</a></td>";
		print "    </tr>";
		print "  </table>";
		$io_grid->makegrid($ai_totrow,$lo_titlesal,$lo_object,680,"Detalle de Materiales","gridarticulossal");
		print "  <table width='680' border='0' align='right' cellpadding='0' cellspacing='0' class='celdas-blancas'>";
		print "  </table>";

		
	}// end function uf_print_cuentas_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_articulosdisponibles($as_codart,$as_codalm,$as_serdes,$as_serhas)
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
		$lo_title[1]="<input name=chkall    type=checkbox id=chkall value='1' onChange=javascript:checkall();>";
		$lo_title[2]="Codigo";
		$lo_title[3]="Articulo";
		$lo_title[4]="Serial";
		require_once("sigesp_siv_c_asignacion.php");
		$io_siv=new sigesp_siv_c_asignacion("../../");
		$rs_data = $io_siv->uf_load_materiales($as_codart,$as_codalm,$as_serdes,$as_serhas);
		$li_fila=0;
		while($row=$io_siv->io_sql->fetch_row($rs_data))	  
		{
			$ls_codart=trim($row["codart"]);
			$ls_coddetart=trim($row["coddetart"]);
			$ls_denart=trim($row["denart"]);
			$li_fila=$li_fila+1;
			$lo_object[$li_fila][1]="<input name=chkselect".$li_fila."    type=checkbox id=chkselect".$li_fila." value='1'>";
			$lo_object[$li_fila][2]="<input name=txtcodart".$li_fila."    type=text id=txtcodart".$li_fila."    class=sin-borde  style=text-align:left size=20 value='".$ls_codart."' readonly>";
			$lo_object[$li_fila][3]="<input name=txtdenart".$li_fila."    type=text id=txtdenart".$li_fila."    class=sin-borde  style=text-align:left size=45 value='".$ls_denart."' readonly>";
			$lo_object[$li_fila][4]="<input name=txtcoddetart".$li_fila." type=text id=txtcoddetart".$li_fila." class=sin-borde  style=text-align:left size=20 value='".$ls_coddetart."' readonly>";
		}
		if($ai_total==0)
		{
			$ai_total=$li_montotal;
		}
		print "<p>&nbsp;</p>";
		$io_grid->makegrid($li_fila,$lo_title,$lo_object,680,"Materiales","gridarticulos");
	}// end function uf_print_creditos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_articulos($ls_codasi)
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
		require_once("sigesp_siv_c_asignacion.php");
		$io_siv=new sigesp_siv_c_asignacion("../../");
		// Titulos el Grid
		$lo_titlesal[1]="Codigo";
		$lo_titlesal[2]="Articulo";
		$lo_titlesal[3]="Serial";
		$lo_titlesal[4]="";

		$li_cantotal=0;
		// Recorrido del Grid de Articulos
		$li_montosal=0;
		$rs_data = $io_siv->uf_load_articulos($ls_codasi);
		$li_fila=0;
		while($row=$io_siv->io_sql->fetch_row($rs_data))	  
		{
			$ls_codart=trim($row["codart"]);
			$ls_denart=rtrim($row["denart"]);
			$ls_coddetart=trim($row["coddetart"]);
			$li_fila=$li_fila+1;
			$lo_object[$li_fila][1]="<input name=txtcodart".$li_fila." type=text id=txtcodart".$li_fila." class=sin-borde  style=text-align:center size=20  value='".$ls_codart."'>";
			$lo_object[$li_fila][2]="<input name=txtdenart".$li_fila." type=text id=txtdenart".$li_fila."   class=sin-borde  style=text-align:center size=40 value='".$ls_denart."' readonly>";
			$lo_object[$li_fila][3]="<input name=txtcoddetart".$li_fila." type=text id=txtcoddetart".$li_fila." class=sin-borde  style=text-align:center size=15  value='".$ls_coddetart."' readonly>";
			$lo_object[$li_fila][4]="<a href=javascript:ue_delete_articulo('".$li_fila."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>";

		}
		$ai_totrowsal=$li_fila+1;
			$lo_object[$ai_totrowsal][1]="<input name=txtcodart".$li_fila." type=text id=txtcodart".$li_fila." class=sin-borde  style=text-align:center size=20  value=''>";
			$lo_object[$ai_totrowsal][2]="<input name=txtdenart".$li_fila." type=text id=txtdenart".$li_fila."   class=sin-borde  style=text-align:center size=40 value='' readonly>";
			$lo_object[$ai_totrowsal][3]="<input name=txtcoddetart".$li_fila." type=text id=txtcoddetart".$li_fila." class=sin-borde  style=text-align:center size=15  value='' readonly>";
			$lo_object[$ai_totrowsal][4]="";
		if($ai_total==0)
		{
			$ai_total=$li_montosal;
		}
		$io_grid->makegrid($ai_totrowsal,$lo_titlesal,$lo_object,720,"Detalle Articulos","gridarticulossal");


	}// end function uf_print_cuentas_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

?>