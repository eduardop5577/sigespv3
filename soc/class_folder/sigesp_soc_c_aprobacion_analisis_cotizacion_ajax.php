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
	//Tipo de Operacion a realizar Aprobacion/Reverso de Aprobacion.
	$ls_tipope = $io_funciones_soc->uf_obtenervalor("tipope","");
	//Tipo de Analisis de Cotización.
	$ls_tipanacot = $io_funciones_soc->uf_obtenervalor("tipanacot","");
    // proceso a ejecutar
	$ls_proceso=$io_funciones_soc->uf_obtenervalor("proceso","");
	
	switch($ls_proceso)
	{
		case "BUSCAR":
		  uf_load_analisis_cotizacion($ls_numanacot,$ld_fecdes,$ld_fechas,$ls_tipanacot,$ls_tipope);
		break;
		
	}	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_analisis_cotizacion($as_numanacot,$ad_fecdes,$ad_fechas,$as_tipanacot,$as_tipope)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_analisis_cotizacion
		//		   Access: private
		//	  Description: Método que busca los Analisis de cotizacion
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 05/08/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_grid, $io_funcion;
	    require_once("sigesp_soc_c_aprobacion_analisis_cotizacion.php");
		$io_aprobacion = new sigesp_soc_c_aprobacion_analisis_cotizacion("../../");

		$ls_nivapro=$_SESSION["la_empresa"]["nivapro"];
		$ls_codasiniv="";
		$ls_codusu=$_SESSION["la_logusr"];
		$ls_codasiniv=$io_aprobacion->uf_nivel_aprobacion_usu($ls_codusu,'2');
		$li_monnivhas=0;
		if($ls_codasiniv!="")
		{
			$ls_codniv=$io_aprobacion->uf_nivel($ls_codasiniv);
			if($ls_codniv!="")
			{
				$li_monnivhas=$io_aprobacion->uf_nivel_aprobacion_montohasta($ls_codniv);
			}
		}
		
		// Titulos del Grid de Ordenes de compra.
		$lo_title[1] = "<input name=chkall type=checkbox id=chkall value=T style=height:15px;width:15px onClick=javascript:uf_select_all(); >";	
	    $lo_title[2] = "N&uacute;mero"; 
	    $lo_title[3] = "Observaci&oacute;n"; 
        $lo_title[4] = "Fecha"; 
	    $lo_title[5] =" Tipo";
		if ($as_tipope=='R')
		   {
		     $lo_title[6] = "Aprobaci&oacute;n";
		   } 
		$lo_object[0]="";
		$rs_data       = $io_aprobacion->uf_load_analisis_cotizacion($as_numanacot,$ad_fecdes,$ad_fechas,$as_tipanacot,$as_tipope);
		$li_fila=0;
		
		while ($row=$io_aprobacion->io_sql->fetch_row($rs_data))	  
		      {
			    $ls_numanacot = str_pad($row["numanacot"],15,0,0);
				$ls_tipanacot = $row["tipsolcot"];
				if ($ls_tipanacot=='B')
				   {
				     $ls_tipanacot = 'Bienes';
				   }
				elseif($ls_tipanacot=='S')
				   {
				     $ls_tipanacot = 'Servicios';
				   }
				$li_montocotizacion = $io_aprobacion->uf_load_monto_cotizacion_nivel($ls_numanacot,$ls_tipanacot);
				
				$ld_fecanacot = $io_funcion->uf_convertirfecmostrar($row["fecanacot"]);
				$ld_fecapro = $io_funcion->uf_convertirfecmostrar($row["fecapro"]);
				$ls_obsana = $row["obsana"];
			    
					if ($ls_nivapro=='1')
					{
						if(($ls_codniv!="")&&($li_monnivhas!=0)&&($li_montocotizacion <= $li_monnivhas))
						{
							$li_fila      = $li_fila+1;
							$lo_object[$li_fila][1]="<input name=chk".$li_fila."          id=chk".$li_fila."           type=checkbox value=1 style=height:15px;width:15px>";
							$lo_object[$li_fila][2]="<input name=txtnumanacot".$li_fila."    id=txtnumanacot".$li_fila."     type=text class=sin-borde  size=15   style=text-align:center   value='".$ls_numanacot."' readonly>";
							$lo_object[$li_fila][3]="<input name=txtobsanacot".$li_fila." id=txtobsanacot".$li_fila."  type=text class=sin-borde  size=50   style=text-align:left     value='".$ls_obsana."' readonly>";
							$lo_object[$li_fila][4]="<input name=txtfecanacot".$li_fila." id=txtfecanacot".$li_fila."  type=text class=sin-borde  size=10   style=text-align:center   value='".$ld_fecanacot."' readonly>";
							$lo_object[$li_fila][5]="<input name=txttipanacot".$li_fila." id=txttipanacot".$li_fila."  type=text class=sin-borde  size=10   style=text-align:center   value='".$ls_tipanacot."' readonly>";
							if ($as_tipope=='R')
							   {
								 $lo_object[$li_fila][6]="<input name=txtfecapro".$li_fila." id=txtfecapro".$li_fila."  type=text class=sin-borde  size=12   style=text-align:center    value='".$ld_fecapro."' readonly>";
							   }
						}
					}
					else
					{
						$li_fila      = $li_fila+1;
						$lo_object[$li_fila][1]="<input name=chk".$li_fila."          id=chk".$li_fila."           type=checkbox value=1 style=height:15px;width:15px>";
						$lo_object[$li_fila][2]="<input name=txtnumanacot".$li_fila."    id=txtnumanacot".$li_fila."     type=text class=sin-borde  size=15   style=text-align:center   value='".$ls_numanacot."' readonly>";
						$lo_object[$li_fila][3]="<input name=txtobsanacot".$li_fila." id=txtobsanacot".$li_fila."  type=text class=sin-borde  size=50   style=text-align:left     value='".$ls_obsana."' readonly>";
						$lo_object[$li_fila][4]="<input name=txtfecanacot".$li_fila." id=txtfecanacot".$li_fila."  type=text class=sin-borde  size=10   style=text-align:center   value='".$ld_fecanacot."' readonly>";
						$lo_object[$li_fila][5]="<input name=txttipanacot".$li_fila." id=txttipanacot".$li_fila."  type=text class=sin-borde  size=10   style=text-align:center   value='".$ls_tipanacot."' readonly>";
						if ($as_tipope=='R')
						   {
							 $lo_object[$li_fila][6]="<input name=txtfecapro".$li_fila." id=txtfecapro".$li_fila."  type=text class=sin-borde  size=12   style=text-align:center    value='".$ld_fecapro."' readonly>";
						   }
					} 
		      }
		if ($li_fila>=1)
		   {
	   		 print "<p>&nbsp;</p>";
			 $io_grid->make_gridScroll($li_fila,$lo_title,$lo_object,588,"An&aacute;lisis de Cotizaciones","gridcompras",250);
		   }
		unset($io_aprobacion);		
	}// end function uf_load_sep
	//-----------------------------------------------------------------------------------------------------------------------------------
?>