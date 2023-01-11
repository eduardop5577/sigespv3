<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	session_start(); 
	require_once("../../shared/class_folder/sigesp_c_generar_consecutivo.php");
	$io_keygen= new sigesp_c_generar_consecutivo();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funcion = new class_funciones();
	require_once("../../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	require_once("class_funciones_soc.php");
	$io_funciones_soc=new class_funciones_soc();
	//Número del Análisis de Cotizacion.
	$ls_numanacot = $io_funciones_soc->uf_obtenervalor("numanacot","");
	//Fecha a partir del cual realizaremos la busqueda.
	$ld_fecdes = $io_funciones_soc->uf_obtenervalor("fecdes","");
	//Fecha hasta el cual realizaremos la busqueda.
	$ld_fechas = $io_funciones_soc->uf_obtenervalor("fechas","");
	//Tipo de Analisis de Cotización.
	$ls_tipanacot = $io_funciones_soc->uf_obtenervalor("tipanacot","");
    // proceso a ejecutar
	$ls_proceso=$io_funciones_soc->uf_obtenervalor("proceso","");
	
	switch($ls_proceso)
	{
		case "BUSCAR":
		  uf_load_analisis_cotizacion($ls_numanacot,$ld_fecdes,$ld_fechas,$ls_tipanacot);
		break;
		
	}	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_analisis_cotizacion($as_numanacot,$ad_fecdes,$ad_fechas,$as_tipanacot)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_analisis_cotizacion
		//		   Access: private
		//	  Description: Método que busca los Analisis de cotizacion
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 05/08/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funcion, $io_keygen,$io_funciones_soc;
		$arrResultado = $io_funciones_soc->uf_load_seguridad2("SOC","sigesp_soc_p_generar_orden_analisis.php",$ls_permisos,$la_seguridad,$la_permisos);
		$ls_administrador = $arrResultado['aa_permisos']['administrador'];

		// Titulos del Grid de Ordenes de compra.
		$lo_title[1] = "<input name=chkall type=checkbox id=chkall value=T style=height:15px;width:15px onClick=javascript:uf_select_all(); >";	
	    $lo_title[2] = "N&uacute;mero"; 
	    $lo_title[3] = "Observaci&oacute;n"; 
        $lo_title[4] = "Fecha"; 
	    $lo_title[5] =" Tipo";
		$lo_title[6] = "Aprobaci&oacute;n";
		$lo_title[7] = "Prefijo";
		$lo_title[8] = "P. Elec.";
		if($ls_administrador=="1")
		{
			$lo_title[9] = "Admin.";
		}
		 
		$lo_object[0]="";
		require_once("sigesp_soc_c_generar_orden_analisis.php");
		$io_aprobacion = new sigesp_soc_c_generar_orden_analisis("../../");
		$rs_data       = $io_aprobacion->uf_load_analisis_cotizacion($as_numanacot,$ad_fecdes,$ad_fechas,$as_tipanacot);
		$li_fila=0;
		
		while ($row=$io_aprobacion->io_sql->fetch_row($rs_data))	  
		      {
			    $li_fila      = $li_fila+1;
			    $ls_numanacot = str_pad($row["numanacot"],15,0,0);
				$ls_tipanacot = $row["tipsolcot"];
				if ($ls_tipanacot=='B')
				   {
				     $ls_tipanacot = 'Bienes';
					 $ls_procede="SOCCOC";
				   }
				elseif($ls_tipanacot=='S')
				   {
				     $ls_tipanacot = 'Servicios';
					 $ls_procede="SOCCOS";
				   }
				$ld_fecanacot = $io_funcion->uf_convertirfecmostrar($row["fecanacot"]);
				$ld_fecapro = $io_funcion->uf_convertirfecmostrar($row["fecapro"]);
				$ls_obsana = $row["obsana"];

				$lo_object[$li_fila][1]="<input name=chk".$li_fila."          id=chk".$li_fila."           type=checkbox value=1 style=height:15px;width:15px>";
			    $lo_object[$li_fila][2]="<input name=txtnumanacot".$li_fila."    id=txtnumanacot".$li_fila."     type=text class=sin-borde  size=15   style=text-align:center   value='".$ls_numanacot."' readonly>";
		 	    $lo_object[$li_fila][3]="<textarea name=txtobsanacot".$li_fila." id=txtobsanacot".$li_fila." class=sin-borde size=10  style=text-align:left readonly>".$ls_obsana."</textarea>";
//		 	    $lo_object[$li_fila][3]="<input name=txtobsanacot".$li_fila." id=txtobsanacot".$li_fila."  type=text class=sin-borde  size=40   style=text-align:left     value='".$ls_obsana."' readonly>";
				$lo_object[$li_fila][4]="<input name=txtfecanacot".$li_fila." id=txtfecanacot".$li_fila."  type=text class=sin-borde  size=10   style=text-align:center   value='".$ld_fecanacot."' readonly>";
				$lo_object[$li_fila][5]="<input name=txttipanacot".$li_fila." id=txttipanacot".$li_fila."  type=text class=sin-borde  size=10   style=text-align:center   value='".$ls_tipanacot."' readonly>";
   	         	$lo_object[$li_fila][6]="<input name=txtfecapro".$li_fila." id=txtfecapro".$li_fila."  type=text class=sin-borde  size=12   style=text-align:center    value='".$ld_fecapro."' readonly>";
   	         	$lo_object[$li_fila][7]=$io_keygen->uf_prefijogrid("","SOC",$ls_procede,$_SESSION["la_logusr"],$li_fila);
   	         	$lo_object[$li_fila][8]="<input name=chkpagele".$li_fila."          id=chkpagele".$li_fila."           type=checkbox value=1 style=height:15px;width:15px>";
				if($ls_administrador=="1")
				{
						$lo_object[$li_fila][9]="<input name=txtnumordcom".$li_fila." id=txtnumordcom".$li_fila."  type=text  size=12   style=text-align:left    value=''   onBlur= ue_rellenarcampo(this,15);>";
				}
				else
				{
						$lo_object[$li_fila][9]="<input type=hidden name=txtnumordcom".$li_fila." id=txtnumordcom".$li_fila." value='' />";
				}
				 
		      }

		if ($li_fila>=1)
		   {
	   		 print "<p>&nbsp;</p>";
			 $io_grid->make_gridScroll($li_fila,$lo_title,$lo_object,880,"An&aacute;lisis de Cotizaciones","gridcompras",210);
		   }
		unset($io_aprobacion);		
	}// end function uf_load_sep
	//-----------------------------------------------------------------------------------------------------------------------------------
?>