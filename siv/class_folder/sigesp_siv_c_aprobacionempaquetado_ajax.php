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
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("../class_funciones_inventario.php");
	$io_funciones_siv=new class_funciones_inventario();
	require_once("sigesp_siv_c_aprobacionempaquetado.php");
	$io_aprobacion=new sigesp_siv_c_aprobacionempaquetado('../../');
	// proceso a ejecutar
	$ls_proceso=$io_funciones_siv->uf_obtenervalor("proceso","");
	// numero de recepcion 
	$ls_codemppro=$io_funciones_siv->uf_obtenervalor("codemppro","");
	// fecha(registro) de inicio de busqueda
	$ld_fecregdes=$io_funciones_siv->uf_obtenervalor("fecregdes","");
	// fecha(registro) de fin de busqueda
	$ld_fecreghas=$io_funciones_siv->uf_obtenervalor("fecreghas","");
	// tipo de operacion aprobacion/reverso
	$ls_tipooperacion=$io_funciones_siv->uf_obtenervalor("tipooperacion","");
	// nunmero 
	$ls_codartemp=$io_funciones_siv->uf_obtenervalor("codartemp","");
	switch($ls_proceso)
	{
		case "BUSCAR":
			uf_print_empaquetado($ls_codemppro,$ld_fecregdes,$ld_fecreghas,$ls_codartemp,$ls_tipooperacion);
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_empaquetado($ls_codemppro,$ad_fecregdes,$ad_fecreghas,$ls_codartemp,$as_tipooperacion)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_recepciones
		//		   Access: private
		//		 Argument: as_numsol        // Numero de la solicitud de orden de Pago
		//                 ad_fecregdes     // Fecha (Registro) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Registro) de fin de la Busqueda
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Método que impirme el grid de las entradas de suministros de almacen
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/05/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funciones_siv, $io_funciones, $io_aprobacion, $io_mensajes;
		// Titulos del Grid de Solicitudes
		$lo_title[1]="";
		$lo_title[2]="Codigo de Empaquetado";
		$lo_title[3]="Articulo Generado";
		$lo_title[4]="Fecha";
		$ad_fecregdes=$io_funciones->uf_convertirdatetobd($ad_fecregdes);
		$ad_fecreghas=$io_funciones->uf_convertirdatetobd($ad_fecreghas);
		$ls_codemppro="%".$ls_codemppro."%";
		$ls_codartemp="%".$ls_codartemp."%";
		$rs_datasol=$io_aprobacion->uf_load_empaquetado($ls_codemppro,$ad_fecregdes,$ad_fecreghas,$ls_codartemp,$as_tipooperacion);
		$li_fila=0;
		while($row=$io_aprobacion->io_sql->fetch_row($rs_datasol))
		{
			$li_fila=$li_fila + 1;
			$ls_codemppro=$row["codemppro"];
			$ls_codartemp=$row["codartemp"];
			$ld_fecemppro=$row["fecemppro"];
			$ls_nomartemp=$row["denartemp"];
			$ld_fecemppro=$io_funciones->uf_convertirfecmostrar($ld_fecemppro);
			$lo_object[$li_fila][1]="<input type=checkbox name=chkaprobacion".$li_fila.">";
			$lo_object[$li_fila][2]="<input type=text name=txtcodemppro".$li_fila." id=txtcodemppro".$li_fila." class=sin-borde style=text-align:center size=20 value='".$ls_codemppro."' readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtnomartemp".$li_fila." id=txtnomartemp".$li_fila." class=sin-borde style=text-align:center   size=20 value='".trim($ls_codartemp)."-".$ls_nomartemp."' readonly>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtfecemppro".$li_fila."    id=txtfecemppro".$li_fila."    class=sin-borde style=text-align:left   size=27 value='".$ld_fecemppro."'    readonly>". 
									"<input type=hidden name=txtcodartemp".$li_fila."    id=txtcodartemp".$li_fila."  value='".$ls_codartemp."'>";
		}
		if($li_fila==0)
		{
			$io_aprobacion->io_mensajes->message("No se encontraron resultados");
			$li_fila=1;
			$lo_object[$li_fila][1]="<input type=checkbox name=chkaprobacion".$li_fila.">";
			$lo_object[$li_fila][2]="<input type=text name=txtcodemppro".$li_fila." id=txtcodemppro".$li_fila." class=sin-borde style=text-align:center size=20 value='' readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtnomartemp".$li_fila." id=txtnomartemp".$li_fila." class=sin-borde style=text-align:center   size=20 value='' readonly>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtfecemppro".$li_fila."    id=txtfecemppro".$li_fila."    class=sin-borde style=text-align:left   size=27 value=''    readonly>". 
									"<input type=hidden name=txtcodartemp".$li_fila."    id=txtcodartemp".$li_fila."  value=''>";
		}

		$io_grid->makegrid($li_fila,$lo_title,$lo_object,700,"Empaquetado de Productos","gridsolicitudes");
	}// end function uf_print_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
?>