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
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("class_funciones_ins.php");
	$io_funciones_ins=new class_funciones_ins("../../");
	require_once("sigesp_ins_c_docs_a_libcompra.php");
	$io_docs_libro=new sigesp_ins_c_docs_a_libcompra('../../');
	// proceso a ejecutar
	$ls_proceso=$io_funciones_ins->uf_obtenervalor("proceso","");
	// numero de sep
	$ls_numrecdoc=$io_funciones_ins->uf_obtenervalor("numrecdoc","");
	// fecha(registro) de inicio de busqueda
	$ld_fecregdes=$io_funciones_ins->uf_obtenervalor("fecregdes","");
	// fecha(registro) de fin de busqueda
	$ld_fecreghas=$io_funciones_ins->uf_obtenervalor("fecreghas","");
	// codigo de proveedor/beneficiario
	$ls_proben=$io_funciones_ins->uf_obtenervalor("proben","");
	// tipo proveedor/beneficiario
	$ls_tipproben=$io_funciones_ins->uf_obtenervalor("tipproben","");
	// tipo de operacion aprobacion/reverso
	$ls_tipooperacion=$io_funciones_ins->uf_obtenervalor("tipooperacion","");
	switch($ls_proceso)
	{
		case "BUSCAR":
			uf_print_recepciones($ls_numrecdoc,$ld_fecregdes,$ld_fecreghas,$ls_tipproben,$ls_proben,$ls_tipooperacion);
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_recepciones($as_recepcion,$ad_fecregdes,$ad_fecreghas,$as_tipproben,$as_proben,$as_tipooperacion)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_solicitudes
		//		   Access: private
		//		 Argument: as_numsol        // Numero de la solicitud de orden de Pago
		//                 ad_fecregdes     // Fecha (Registro) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Registro) de fin de la Busqueda
		//                 as_tipproben     // Tipo proveedor/ beneficiario
		//                 as_proben        // Codigo de proveedor/ beneficiario
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Método que impirme el grid de las recepciones a ser aprobadas o para reversar la aprovaciòn
		//	   Creado Por: OFIMATICA DE VENEZUELA, C.A.  - Ing. Nelson Barraéz 
		// Fecha Creación: 25/05/2011								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("class_funciones_ins.php");
		$io_ins= new class_funciones_ins("../../");
		$io_fecha=new class_fecha();
		global $io_grid, $io_funciones_ins, $io_funciones, $io_docs_libro, $io_mensajes;
		$ls_nivapro=$_SESSION["la_empresa"]["nivapro"];
		$ls_codasiniv="";
		$ls_codusu=$_SESSION["la_logusr"];
		$ls_codasiniv=$io_docs_libro->uf_nivel_aprobacion_usu($ls_codusu,'3');
		$li_monnivhas=0;
		if($ls_codasiniv!="")
		{
			$ls_codniv=$io_docs_libro->uf_nivel($ls_codasiniv);
			if($ls_codniv!="")
			{
				$li_monnivhas=$io_docs_libro->uf_nivel_aprobacion_montohasta($ls_codniv);
			}
		}
		// Titulos del Grid de Solicitudes
		$lo_title[1]="";
		$lo_title[2]="Numero de Recepcion";
		$lo_title[3]="Fecha Registro";
		$lo_title[4]="Proveedor / Beneficiario";
		$lo_title[5]="Libro de Compra";
		$lo_title[6]="Monto";
		$ad_fecregdes=$io_funciones->uf_convertirdatetobd($ad_fecregdes);
		$ad_fecreghas=$io_funciones->uf_convertirdatetobd($ad_fecreghas);
		$as_recepcion="%".$as_recepcion."%";
		$as_proben="%".$as_proben."%";
		$rs_datasol=$io_docs_libro->uf_load_recepciones($as_recepcion,$ad_fecregdes,$ad_fecreghas,$as_tipproben,$as_proben,$as_tipooperacion);
		$li_fila=0;
		if($rs_datasol!=false)
		{
			while($row=$io_docs_libro->io_sql->fetch_row($rs_datasol))
			{
				$lb_imprimir=true;
				$ls_numrecdoc=$row["numrecdoc"];
				$ld_fecregdoc=date("Y-m-d",strtotime($row["fecregdoc"]));
				$ls_estlibcom=$row["estlibcom"];
				$ls_codtipdoc=$row["codtipdoc"];
				$ls_codpro=$row["cod_pro"];
				$ls_cedben=$row["ced_bene"];
				$li_rowspg=$row["rowspg"];
				$li_rowscg=$row["rowscg"];
				$ls_proben=utf8_encode($row["nombre"]);
				$li_montotdoc_comp=$row["montotdoc"];
				$li_montotdoc=number_format($row["montotdoc"],2,',','.');
				if($ls_estlibcom==0)
				{
					$ls_estatus="No incluido";
				}
				else
				{
					$ls_estatus="Incluido";
				}
				if($li_rowspg>=1)
				{
					$arrResultado = $io_ins->uf_verificar_cierre_spg("../../",$ls_estciespg);
					$ls_estciespg = $arrResultado['as_estciespg'];
					$lb_valido = $arrResultado['lb_valido'];
					if($ls_estciespg=="1")
					{
						$lb_imprimir=false;
					}
				}
				if($li_rowscg>=1)
				{
					$arrResultado = $io_ins->uf_verificar_cierre_scg("../../",$ls_estciescg);
					$ls_estciescg = $arrResultado['as_estciespg'];
					$lb_valido = $arrResultado['lb_valido'];
					if($ls_estciescg=="1")
					{
						$lb_imprimir=false;
					}
				}
				$ld_fecregdoc=$io_funciones->uf_convertirfecmostrar($ld_fecregdoc);
				$lb_fecha_valida=$io_fecha->uf_valida_fecha_periodo($ld_fecregdoc,$_SESSION["la_empresa"]["codemp"]);
				if ($ls_nivapro==1)
				{
					if(($ls_codniv!="")&&($li_monnivhas!=0)&&($li_montotdoc_comp <= $li_monnivhas))
				    {
						if($lb_imprimir && $lb_fecha_valida)
						{
							$li_fila=$li_fila + 1;
							$lo_object[$li_fila][1]="<input type=checkbox name=chkaprobacion".$li_fila.">";
							$lo_object[$li_fila][2]="<input type=text name=txtnumrecdoc".$li_fila." id=txtnumrecdoc".$li_fila." class=sin-borde style=text-align:center size=20 value='".$ls_numrecdoc."' readonly>";
							$lo_object[$li_fila][3]="<input type=text name=txtfecregdoc".$li_fila." id=txtfecregdoc".$li_fila." class=sin-borde style=text-align:left   size=15 value='".$ld_fecregdoc."' readonly>"; 
							$lo_object[$li_fila][4]="<input type=text name=txtproben".$li_fila."    id=txtproben".$li_fila."    class=sin-borde style=text-align:left   size=35 value='".$ls_proben."'    readonly>"; 
							$lo_object[$li_fila][5]="<input type=text name=txtestapr".$li_fila."    id=txtestapr".$li_fila."    class=sin-borde style=text-align:left   size=20 value='".$ls_estatus."'   readonly>";
							$lo_object[$li_fila][6]="<input type=text name=txtmontotdoc".$li_fila." id=txtmontotdoc".$li_fila." class=sin-borde style=text-align:right  size=20 value='".$li_montotdoc."' readonly>".
													"<input type=hidden name=txtcodtipdoc".$li_fila." id=txtcodtipdoc".$li_fila." class=sin-borde style=text-align:right  size=20 value='".$ls_codtipdoc."' readonly>".
													"<input type=hidden name=txtcodpro".$li_fila."  id=txtcodpro".$li_fila."    class=sin-borde style=text-align:right  size=20 value='".$ls_codpro."' readonly>".
													"<input type=hidden name=txtcedben".$li_fila."  id=txtcedben".$li_fila."    class=sin-borde style=text-align:right  size=20 value='".$ls_cedben."' readonly>";
						}
					}
				}
				else
				{
					if($lb_imprimir  && $lb_fecha_valida)
					{
						$li_fila=$li_fila + 1;
						$lo_object[$li_fila][1]="<input type=checkbox name=chkaprobacion".$li_fila.">";
						$lo_object[$li_fila][2]="<input type=text name=txtnumrecdoc".$li_fila." id=txtnumrecdoc".$li_fila." class=sin-borde style=text-align:center size=20 value='".$ls_numrecdoc."' readonly>";
						$lo_object[$li_fila][3]="<input type=text name=txtfecregdoc".$li_fila." id=txtfecregdoc".$li_fila." class=sin-borde style=text-align:left   size=15 value='".$ld_fecregdoc."' readonly>"; 
						$lo_object[$li_fila][4]="<input type=text name=txtproben".$li_fila."    id=txtproben".$li_fila."    class=sin-borde style=text-align:left   size=35 value='".$ls_proben."'    readonly>"; 
						$lo_object[$li_fila][5]="<input type=text name=txtestapr".$li_fila."    id=txtestapr".$li_fila."    class=sin-borde style=text-align:left   size=20 value='".$ls_estatus."'   readonly>";
						$lo_object[$li_fila][6]="<input type=text name=txtmontotdoc".$li_fila." id=txtmontotdoc".$li_fila." class=sin-borde style=text-align:right  size=20 value='".$li_montotdoc."' readonly>".
												"<input type=hidden name=txtcodtipdoc".$li_fila." id=txtcodtipdoc".$li_fila." class=sin-borde style=text-align:right  size=20 value='".$ls_codtipdoc."' readonly>".
												"<input type=hidden name=txtcodpro".$li_fila."  id=txtcodpro".$li_fila."    class=sin-borde style=text-align:right  size=20 value='".$ls_codpro."' readonly>".
												"<input type=hidden name=txtcedben".$li_fila."  id=txtcedben".$li_fila."    class=sin-borde style=text-align:right  size=20 value='".$ls_cedben."' readonly>";
					}
				}
			}
		}
/*		if(($li_rowspg>1)&&($ls_estciespg=="1"))
		{
			$io_docs_libro->io_mensajes->message("Esta procesado el cierre presupuestario");
		}
		if(($li_rowscg>1)&&($ls_estciescg=="1"))
		{
			$io_docs_libro->io_mensajes->message("Esta procesado el cierre contable");
		}
		
*/		if($li_fila==0)
		{
			$io_docs_libro->io_mensajes->message("No se encontraron resultados");
			$li_fila=1;
			$lo_object[$li_fila][1]="<input type=checkbox name=chkaprobacion value=1 disabled/>";
			$lo_object[$li_fila][2]="<input type=text name=txtnumrecdoc".$li_fila." class=sin-borde style=text-align:center size=20 readonly>";
			$lo_object[$li_fila][3]="<input type=text name=txtfecregdoc".$li_fila." class=sin-borde style=text-align:left   size=15 readonly>"; 
			$lo_object[$li_fila][4]="<input type=text name=txtproben".$li_fila."    class=sin-borde style=text-align:left   size=35 readonly>"; 
			$lo_object[$li_fila][5]="<input type=text name=txtestapr".$li_fila."    class=sin-borde style=text-align:left   size=20 readonly>";
			$lo_object[$li_fila][6]="<input type=text name=txtmontotdoc".$li_fila." class=sin-borde style=text-align:right  size=20 readonly>";
									"<input type=hidden name=txtcodtipdoc".$li_fila." class=sin-borde style=text-align:right  size=20 readonly>".
									"<input type=hidden name=txtcodpro".$li_fila."  class=sin-borde style=text-align:right  size=20 readonly>".
									"<input type=hidden name=txtcedben".$li_fila."  class=sin-borde style=text-align:right  size=20 readonly>";
		}

		$io_grid->makegrid($li_fila,$lo_title,$lo_object,700,"Recepciones de Documentos","gridsolicitudes");
	}// end function uf_print_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
?>