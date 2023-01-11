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
if(!array_key_exists("la_logusr",$_SESSION))
{
	print "<script language=JavaScript>";
	print "close();";
	print "</script>";		
}
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codart,$ls_denart,$ls_codartpri,$ls_denartpri,$ls_codtipart,$ls_dentipart,$ls_codunimed,$ls_denunimed;
   		global $ls_codcatsig,$ls_dencatsig,$ls_spg_cuenta,$li_canart,$li_cosart,$ls_dentipart,$ls_codunimed,$ls_ctasep;
		
		$ls_codart="";
		$ls_denart="";
		$ls_codartpri="";
		$ls_denartpri="";
		$ls_codtipart="";
		$ls_dentipart="";
		$ls_codunimed="";
		$ls_denunimed="";
		$ls_codcatsig="";
		$ls_dencatsig="";
		$ls_spg_cuenta="";
		$li_canart=1;
		$li_cosart=0;
		$ls_ctasep="";
   }
   
   function uf_titulosdespacho()
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_titulosdespacho
		//         Access: private
		//      Argumento:  	
		//	      Returns:
		//    Description: Función que carga las caracteristicas del grid de detalle de despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/02/2006								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_titletable,$li_widthtable,$ls_nametable,$lo_title;
		
		$ls_titletable="Detalle del Articulo";
		$li_widthtable=800;
		$ls_nametable="grid";
		$lo_title[1]="Artículo";
		$lo_title[2]="Almacén";
		$lo_title[3]="Unidad";
		$lo_title[4]="Existencia";
		$lo_title[5]="Cant. a Despachar";
		$lo_title[6]="";
   }

   function uf_agregarlineablanca($aa_object,$ai_totrows)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 		
		//                 $ai_totrows // ultima fila pintada en el grid		
		//	      Returns:
		//    Description: Funcion que agrega una linea en blanco al final del grid del detalle de despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/02/2006								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]="<input  name=txtdenart".$ai_totrows."     type=text   id=txtdenart".$ai_totrows." class=sin-borde size=25 maxlength=50 readonly>".
								   "<input  name=txtcodart".$ai_totrows."     type=hidden id=txtcodart".$ai_totrows." class=sin-borde size=20 maxlength=50 readonly>".
							 	   "<input name=txtcodartpri".$ai_totrows."  type=hidden id=txtcodartpri".$ai_totrows."    class=sin-borde size=15 maxlength=25 readonly>";
		$aa_object[$ai_totrows][2]="<input  name=txtcodalm".$ai_totrows."     type=text   id=txtcodalm".$ai_totrows." class=sin-borde size=13 maxlength=10 readonly>";
		$aa_object[$ai_totrows][3]="<input  name=txtunidad".$ai_totrows."     type=text id=txtunidad".$ai_totrows."    class=sin-borde size=15 maxlength=25 readonly>";
		$aa_object[$ai_totrows][4]="<input  name=txtexistencia".$ai_totrows." type=text   id=txtexistencia".$ai_totrows." class=sin-borde size=12 maxlength=12 readonly>";
		$aa_object[$ai_totrows][5]="<input  name=txtcanart".$ai_totrows."     type=text   id=txtcanart".$ai_totrows." class=sin-borde size=12 maxlength=12 onKeyUp='javascript: ue_validarnumero(this);'  onBlur='javascript: ue_montosfactura(".$ai_totrows.");'>";
		$aa_object[$ai_totrows][6]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";			
		return $aa_object;
   }
   //--------------------------------------------------------------
   function uf_loadgrid($lo_object,$ai_totrows)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 		
		//                 $ai_totrows // ultima fila pintada en el grid		
		//	      Returns:
		//    Description: Funcion que agrega una linea en blanco al final del grid del detalle de despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/02/2006								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_inventario;
		for($li_i=1;$li_i<$ai_totrows;$li_i++)
		{
			$ls_codartgrid=$io_fun_inventario->uf_obtenervalor("txtcodart".$li_i,"");
			$ls_denartgrid=$io_fun_inventario->uf_obtenervalor("txtdenart".$li_i,"");
			$ls_codartprigrid=$io_fun_inventario->uf_obtenervalor("txtcodartpri".$li_i,"");
			$ls_codalmgrid=$io_fun_inventario->uf_obtenervalor("txtcodalm".$li_i,"");
			$ls_unidadgrid=$io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
			$ls_exiartgrid=$io_fun_inventario->uf_obtenervalor("txtexistencia".$li_i,"");
			$ls_canartgrid=$io_fun_inventario->uf_obtenervalor("txtcanart".$li_i,"");
			$ls_ctasepgrid=$io_fun_inventario->uf_obtenervalor("ctasep".$li_i,"");

			$lo_object[$li_i][1]="<input  name=txtdenart".$li_i."     type=text   id=txtdenart".$li_i." class=sin-borde size=25 maxlength=50 value='".$ls_denartgrid."'  readonly>".
								 "<input  name=txtcodart".$li_i."     type=hidden id=txtcodart".$li_i." class=sin-borde size=20 maxlength=50 value='".$ls_codartgrid."'  readonly>".
							     "<input  name=ctasep".$li_i."        type=hidden id=ctasep".$li_i." class=sin-borde size=20 maxlength=50 value='".$ls_ctasepgrid."'  readonly>".
								 "<input  name=txtcodartpri".$li_i."  type=hidden id=txtcodartpri".$li_i."    class=sin-borde size=15 maxlength=25 value='".$ls_codartprigrid."' readonly>";
			$lo_object[$li_i][2]="<input  name=txtcodalm".$li_i."     type=text   id=txtcodalm".$li_i." class=sin-borde size=13 maxlength=10 value='".$ls_codalmgrid."' readonly>";
			$lo_object[$li_i][3]="<input  name=txtunidad".$li_i."     type=text id=txtunidad".$li_i."    class=sin-borde size=15 maxlength=25 value='".$ls_unidadgrid."' readonly>";
			$lo_object[$li_i][4]="<input  name=txtexistencia".$li_i." type=text   id=txtexistencia".$li_i." class=sin-borde size=12 maxlength=12 value='".$ls_exiartgrid."' readonly>";
			$lo_object[$li_i][5]="<input  name=txtcanart".$li_i."     type=text   id=txtcanart".$li_i." class=sin-borde size=12 maxlength=12 onKeyUp='javascript: ue_validarnumero(this);'value='".$ls_canartgrid."' readonly>";
			$lo_object[$li_i][6]="<a href=javascript:uf_delete_dt(".$li_i.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";			
		}
		return $lo_object;
   }
   //--------------------------------------------------------------

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Detalle de Activo </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:hover {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style></head>

<body onLoad="javascript: ue_focus();">
<?php
require_once("sigesp_siv_c_despacho.php");
$io_siv=  new sigesp_siv_c_despacho();
require_once("class_funciones_inventario.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../shared/class_folder/grid_param.php");
$in_grid= new grid_param();
$io_msg= new class_mensajes();
$io_fun_inventario= new class_funciones_inventario();
$ls_permisos = "";
$la_seguridad = Array();
$la_permisos = Array();
$arrResultado = $io_fun_inventario->uf_load_seguridad("SIV","sigesp_siv_p_transferencia.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos = $arrResultado['as_permisos'];
$la_seguridad = $arrResultado['aa_seguridad'];
$la_permisos = $arrResultado['aa_permisos'];
uf_titulosdespacho();
$li_totrows=1;
$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_operacion=$io_fun_inventario->uf_obteneroperacion();
	
	switch($ls_operacion)
	{
		case"NUEVO":
			uf_limpiarvariables();
			$li_totrowsopenner=$io_fun_inventario->uf_obtenervalor_get("linea",1);
			$ls_codalm=$io_fun_inventario->uf_obtenervalor_get("almacen","");
			$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
		break;
		case"BUSCAR":
			uf_limpiarvariables();
			$ls_codart=$io_fun_inventario->uf_obtenervalor("txtcodart",1);
			$li_totrowsopenner=$io_fun_inventario->uf_obtenervalor("totalfilas","");
			$li_totrows=$io_fun_inventario->uf_obtenervalor("totalfilaslocal","");
			$ls_origen=$io_fun_inventario->uf_obtenervalor("origen","");
			$arrResultado=$io_siv->uf_select_articulo($ls_codart,$ls_origen,$ls_codartpri,$ls_denart,$li_unidad,$ls_denartpri);
			$ls_codartpri = $arrResultado['ls_codartpri'];
			$ls_denart = $arrResultado['as_denart'];
			$li_unidad = $arrResultado['ai_unidad'];
			$ls_denartpri = $arrResultado['as_denartpri'];
			$lb_valido=$arrResultado['lb_valido'];

			$lo_object = uf_loadgrid($lo_object,$li_totrows);
			$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
			if(!$lb_valido)
			{
				$io_msg->message("El codigo indicado no esta registrado");
				$ls_codart="";
			}
		break;
		case"AGREGARDETALLE":
			$li_totrowsopenner=$io_fun_inventario->uf_obtenervalor("totalfilas","");
			$li_totrows=$io_fun_inventario->uf_obtenervalor("totalfilaslocal","");
			$ls_codart=$io_fun_inventario->uf_obtenervalor("txtcodart","");
			$ls_denart=$io_fun_inventario->uf_obtenervalor("txtdenart","");
			$ls_denart=$io_fun_inventario->uf_obtenervalor("txtdenartpri","");
			$ls_codalm=$io_fun_inventario->uf_obtenervalor("txtcodalm","");
			$ls_codartpri=$io_fun_inventario->uf_obtenervalor("txtcodartpri","");
			$ls_unidad=$io_fun_inventario->uf_obtenervalor("cmbunidad","D");
			$li_exiart=number_format($io_fun_inventario->uf_obtenervalor("hidexistencia",""),2,',','.');
			$li_canart=$io_fun_inventario->uf_obtenervalor("txtcanart","1");
			$ls_ctasep=$io_fun_inventario->uf_obtenervalor("ctasep","");
			$lo_object = uf_loadgrid($lo_object,$li_totrows);
			if($ls_unidad=="M")
			{
				$ls_unidad="Mayor";
			}
			else
			{
				$ls_unidad="Detal";
			}
			if(($ls_codart!="")&&($ls_codalm!="")&&($li_exiart>0))
			{
				$lo_object[$li_totrows][1]="<input  name=txtdenart".$li_totrows."     type=text   id=txtdenart".$li_totrows." class=sin-borde size=25 maxlength=50 value='".$ls_denart."'  readonly>".
										   "<input  name=txtcodart".$li_totrows."     type=hidden id=txtcodart".$li_totrows." class=sin-borde size=20 maxlength=50 value='".$ls_codart."'  readonly>".
										   "<input  name=ctasep".$li_totrows."        type=hidden id=ctasep".$li_totrows." class=sin-borde size=20 maxlength=50 value='".$ls_ctasep."'  readonly>".
										   "<input  name=txtcodartpri".$li_totrows."  type=hidden id=txtcodartpri".$li_totrows."    class=sin-borde size=15 maxlength=25 value='".$ls_codartpri."' readonly>";
				$lo_object[$li_totrows][2]="<input  name=txtcodalm".$li_totrows."     type=text   id=txtcodalm".$li_totrows." class=sin-borde size=13 maxlength=10 value='".$ls_codalm."' readonly>";
				$lo_object[$li_totrows][3]="<input  name=txtunidad".$li_totrows."     type=text id=txtunidad".$li_totrows."    class=sin-borde size=15 maxlength=25 value='".$ls_unidad."' readonly>";
				$lo_object[$li_totrows][4]="<input  name=txtexistencia".$li_totrows." type=text   id=txtexistencia".$li_totrows." class=sin-borde size=12 maxlength=12 value='".$li_exiart."' readonly>";
				$lo_object[$li_totrows][5]="<input  name=txtcanart".$li_totrows."     type=text   id=txtcanart".$li_totrows." class=sin-borde size=12 maxlength=12 onKeyUp='javascript: ue_validarnumero(this);'value='".$li_canart."' readonly>";
				$lo_object[$li_totrows][6]="<a href=javascript:uf_delete_dt(".$li_totrows.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";			
			}
			$li_totrows++;
			$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
			uf_limpiarvariables();
		break;
		case"ELIMINARDETALLE":
			uf_limpiarvariables();
			$li_totrowsopenner=$io_fun_inventario->uf_obtenervalor("totalfilas","");
			$li_totrows=$io_fun_inventario->uf_obtenervalor("totalfilaslocal","");
			$li_totrows=$li_totrows-1;
			$li_rowdelete= $io_fun_inventario->uf_obtenervalor("filadelete","");
			$li_temp=0;
			for($li_i=1;$li_i<=$li_totrows;$li_i++)
			{
				if($li_i!=$li_rowdelete)
				{
					$li_temp=$li_temp+1;			
					$ls_codartgrid=$io_fun_inventario->uf_obtenervalor("txtcodart".$li_i,"");
					$ls_denartgrid=$io_fun_inventario->uf_obtenervalor("txtdenart".$li_i,"");
					$ls_codartprigrid=$io_fun_inventario->uf_obtenervalor("txtcodartpri".$li_i,"");
					$ls_codalmgrid=$io_fun_inventario->uf_obtenervalor("txtcodalm".$li_i,"");
					$ls_unidadgrid=$io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
					$ls_exiartgrid=$io_fun_inventario->uf_obtenervalor("txtexistencia".$li_i,"");
					$ls_canartgrid=$io_fun_inventario->uf_obtenervalor("txtcanart".$li_i,"");
					$ls_ctasepgrid=$io_fun_inventario->uf_obtenervalor("ctasep".$li_i,"");
		
					$lo_object[$li_temp][1]="<input  name=txtdenart".$li_temp."     type=text   id=txtdenart".$li_temp." class=sin-borde size=25 maxlength=50 value='".$ls_denartgrid."'  readonly>".
											   "<input  name=txtcodart".$li_temp."     type=hidden id=txtcodart".$li_temp." class=sin-borde size=20 maxlength=50 value='".$ls_codartgrid."'  readonly>".
										  	   "<input  name=ctasep".$li_temp."        type=hidden id=ctasep".$li_temp." class=sin-borde size=20 maxlength=50 value='".$ls_ctasepgrid."'  readonly>".
											   "<input  name=txtcodartpri".$li_temp."  type=hidden id=txtcodartpri".$li_temp."    class=sin-borde size=15 maxlength=25 value='".$ls_codartprigrid."' readonly>";
					$lo_object[$li_temp][2]="<input  name=txtcodalm".$li_temp."     type=text   id=txtcodalm".$li_temp." class=sin-borde size=13 maxlength=10 value='".$ls_codalmgrid."' readonly>";
					$lo_object[$li_temp][3]="<input  name=txtunidad".$li_temp."     type=text id=txtunidad".$li_temp."    class=sin-borde size=15 maxlength=25 value='".$ls_unidadgrid."' readonly>";
					$lo_object[$li_temp][4]="<input  name=txtexistencia".$li_temp." type=text   id=txtexistencia".$li_temp." class=sin-borde size=12 maxlength=12 value='".$ls_exiartgrid."' readonly>";
					$lo_object[$li_temp][5]="<input  name=txtcanart".$li_temp."     type=text   id=txtcanart".$li_temp." class=sin-borde size=12 maxlength=12 onKeyUp='javascript: ue_validarnumero(this);'value='".$ls_canartgrid."' readonly>";
					$lo_object[$li_temp][6]="<a href=javascript:uf_delete_dt(".$li_temp.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";			
				}
				else
				{
					$li_rowdelete= 0;
				}
				if ($li_temp==0)
				{
					$li_totrows=1;
					$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
				}
				else
				{				
					$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
				}
			}		
		break;

		case"AGREGAR":
			if(array_key_exists("despacho",$_SESSION))
			{
				unset($_SESSION["despacho"]);
			}
			$li_totrowsopenner=$io_fun_inventario->uf_obtenervalor("totalfilas","");
			$li_totrow=$io_fun_inventario->uf_obtenervalor("totalfilaslocal",1);
			$li_cansol=$io_fun_inventario->uf_obtenervalor("cansol","0");
			$li_pendiente=$li_penart=$io_fun_inventario->uf_obtenervalor("penart","");
			$ls_ctasep=$io_fun_inventario->uf_obtenervalor("ctasep","");
			$li_contador=0;
			$li_total=0;
			for($li_i=1;$li_i<$li_totrow;$li_i++)
			{
				$ls_codart=$io_fun_inventario->uf_obtenervalor("txtcodart".$li_i,"");
				$ls_codartpri=$io_fun_inventario->uf_obtenervalor("txtcodartpri".$li_i,"");
				$ls_unidad=$io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"Detal");
				$ls_codalm=$io_fun_inventario->uf_obtenervalor("txtcodalm".$li_i,"");
				$li_canart=$io_fun_inventario->uf_obtenervalor("txtcanart".$li_i,"");
				$li_exiart=$io_fun_inventario->uf_obtenervalor("txtexiart".$li_i,"");
				$li_canartaux=$io_fun_inventario->uf_formatocalculo($li_canart);
				if($li_canartaux>0)
				{
					$ls_sql="SELECT metodo FROM siv_config";
					$li_exec=$io_siv->io_sql->select($ls_sql);
					if($row=$io_siv->io_sql->fetch_row($li_exec))
					{
						$ls_metodo=$row["metodo"];
					}
					$ls_metodo=trim($ls_metodo);
					switch($ls_metodo)
					{
						case"FIFO";
							$ls_sql="SELECT cosart FROM siv_dt_movimiento".
									" WHERE codemp='". $ls_codemp ."'".
									"   AND codart='". $ls_codart ."'".
									"   AND codalm='". $ls_codalm ."'".
									"   AND opeinv='ENT' AND numdocori NOT IN".
									"       (SELECT numdocori FROM siv_dt_movimiento".
									"         WHERE opeinv ='REV')".
									" ORDER BY nummov";  
						break;
						case"LIFO";
							$ls_sql="SELECT cosart FROM siv_dt_movimiento".
									" WHERE codemp='". $ls_codemp ."'".
									"   AND codart='". $ls_codart ."'".
									"   AND codalm='". $ls_codalm ."'".
									"   AND opeinv='ENT' AND numdocori NOT IN".
									"       (SELECT numdocori FROM siv_dt_movimiento".
									"         WHERE opeinv ='REV')".
									" ORDER BY nummov DESC";
						break;
						case"CPP";
							$ls_sql="SELECT Avg(cosart) as cosart, nummov".
									"  FROM siv_dt_movimiento".
									" WHERE codemp='". $ls_codemp ."'".
									"   AND codart='". $ls_codart ."'".
									"   AND codalm='". $ls_codalm ."'".
									"   AND opeinv='ENT' AND codprodoc<>'REV' AND numdocori NOT IN".
									"       (SELECT numdocori FROM siv_dt_movimiento".
									"         WHERE opeinv ='REV')".
									" GROUP BY nummov".
									" ORDER BY nummov DESC"; 
						break;
					}
					$rs_data=$io_siv->io_sql->select($ls_sql);
					if($row=$io_siv->io_sql->fetch_row($rs_data))
					{
						$li_preuniart=$row["cosart"];
						$li_preuniart=$io_fun_inventario->uf_formatonumerico($li_preuniart);
					}
					$arrResultado=$io_siv->uf_obtener_datos_articulo($ls_codart,$ls_denart,$ls_sccuenta,$li_unidad,$ls_denunimed);
					$ls_denart=$arrResultado['ls_denart'];
					$ls_sccuenta=$arrResultado['ls_sccuenta'];
					$li_unidad=$arrResultado['li_unidad'];
					$ls_denunimed=$arrResultado['ls_denunimed'];
					$lb_valido=$arrResultado['lb_valido'];
					if($lb_valido)
					{
						$li_contador++;
						$li_pendiente=$li_pendiente-$li_canart;
						$_SESSION["despacho"]["codart".$li_contador]=$ls_codart;
						$_SESSION["despacho"]["codalm".$li_contador]=$ls_codalm;
						$_SESSION["despacho"]["denart".$li_contador]=$ls_denart;
						$_SESSION["despacho"]["denunimed".$li_contador]=$ls_denunimed;
						$_SESSION["despacho"]["sc_cuenta".$li_contador]=$ls_sccuenta;
						$_SESSION["despacho"]["preuniart".$li_contador]=$li_preuniart;
						$_SESSION["despacho"]["unidad".$li_contador]=$li_unidad;
						$_SESSION["despacho"]["canart".$li_contador]=$li_canart;
						$_SESSION["despacho"]["exiart".$li_contador]=$li_exiart;
						$_SESSION["despacho"]["penart".$li_contador]=$li_pendiente;
						$li_total=$li_total+$li_canartaux;
					}
				}
			}
			$_SESSION["despacho"]["ctasep"]=$ls_ctasep;
			$_SESSION["despacho"]["contador"]=$li_contador;
			$_SESSION["despacho"]["unidad"]=$ls_unidad;
			$_SESSION["despacho"]["cansol"]=$li_cansol;
			$_SESSION["despacho"]["codartpri"]=$ls_codartpri;
			$_SESSION["despacho"]["penart"]=$li_penart;
			$_SESSION["despacho"]["totart"]=$li_total;
			if($lb_valido)
			{
				$ls_opeopener="AGREGARDETALLES";
				print "<script>";
				print "opener.document.form1.operacion.value=";
				print "obj=eval(opener.document.form1.operacion);";
				print "obj.value='".$ls_opeopener."';";
				print "opener.document.form1.submit();";
				print "close();";
				print "</script>";
			}
		break;
	}
?>
<form name="form1" method="post" action="">
  <table width="559" border="0" align="center" class="formato-blanco">
    <tr>
      <td height="22" colspan="4" class="titulo-celda">Detalle de Art&iacute;culo </td>
    </tr>
    <tr>
      <td width="114"><div align="right">Articulo</div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtcodart" type="text" id="txtcodart" style="text-align:center " value="<?php print $ls_codart; ?>" onKeyPress="javascript: ue_enviar(event);" onBlur="javascript: ue_verificar_articulo(); ">
        
          <input name="txtdenart" type="text" class="sin-borde" id="txtdenart" value="<?php print $ls_denart; ?>" size="40" readonly>
      </div></td>
    </tr>
    <tr>
      <td><div align="right">Articulo Primario
        
</div></td>
      <td height="22" colspan="3"><input name="txtdenartpri" type="text" class="sin-borde2" id="txtdenartpri" value="<?php print $ls_denartpri; ?>" size="30">
      <input name="txtcodartpri" type="hidden" id="txtcodartpri" value="<?php print $ls_codartpri; ?>"></td>
    </tr>
    <tr>
      <td><div align="right">Almacen</div></td>
      <td height="22"><input name="txtcodalm" type="text" id="txtcodalm" readonly>
      <a href="javascript: ue_buscaralmacen();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></td>
      <td height="22"><div align="right">Existencia</div></td>
      <td height="22"><input name="hidexistencia" type="text" class="sin-borde2" id="hidexistencia" size="8" readonly>
      <input name="txtpreuniart" type="hidden" id="txtpreuniart">
      <input name="hidnumdocori" type="hidden" id="hidnumdocori"></td>
    </tr>
    <tr>
      <td><div align="right">Unidad</div></td>
      <td height="22" colspan="3"><div align="left">
        <select name="cmbunidad" id="cmbunidad">
          <option value="D">Detal</option>
          <option value="M">Mayor</option>
        </select>
      </div></td>
    </tr>
    <tr>
      <td><div align="right">Cantidad</div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtcanart" type="text" id="txtcanart" style="text-align:right " value="<?php print number_format($li_canart,2,",","."); ?>" size="10"  onKeyPress="return(ue_formatonumero(this,'.',',',event));">
      </div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="22" colspan="3"><input name="btnagregar" type="button" class="boton" id="btnagregar" value="Aceptar" onClick="javascript: uf_agregar_dt();"></td>
    </tr>
    <tr>
      <td height="22" colspan="4">
		<div align="center">
		<?php
			$in_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
		?>
		</div></td>
    </tr>
    <tr>
      <td><div align="right"></div></td>
      <td width="226" align="center"><input name="operacion" type="hidden" id="operacion">
      <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrowsopenner;?>">
      <input name="totalfilaslocal" type="hidden" id="totalfilaslocal" value="<?php print $li_totrows;?>">
      <input name="filadelete" type="hidden" id="filadelete" value="<?php print $li_rowdelete;?>">
      <input name="hidcosto" type="hidden" id="hidcosto"  value="<?php print number_format($li_cosart,2,",","."); ?>">
      <input name="hidunidad" type="hidden" id="hidunidad" value="<?php print $li_unidad; ?>">
	  <input name="penart" type="hidden" id="penart" value="<?php print $li_penart; ?>">
	  <input name="cansol" type="hidden" id="cansol" value="<?php print $li_cansol; ?>">
	  <input name="ctasep" type="hidden" id="ctasep" value="<?php print $ls_ctasep; ?>">
      <input name="hidalmacen" type="hidden" id="hidalmacen" value="<?php print $ls_codalm; ?>"></td>
	  <td width="103"><a href="javascript: ue_agregar();"><img src="../shared/imagebank/tools15/aprobado.gif" width="15" height="15" class="sin-borde">Agregar Detalle</a></td>
      <td width="96"><a href="javascript: ue_cancelar();"><img src="../shared/imagebank/tools15/eliminar.gif" width="15" height="15" class="sin-borde">Cancelar</a> </td>
    </tr>
  </table>
</form>
</body>
<script >

	function ue_verificar_articulo()
	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		articulo=f.txtcodart.value;
		li_totrows=f.totalfilas.value;
		if(articulo!="")
		{
			for(li_i=1; li_i<=li_totrows;li_i++)
			{
				ls_codartgrid=eval("opener.document.form1.txtcodart"+li_i+".value");
				if(ls_codartgrid==articulo)
				{
					f.codartori.value=ls_codartgrid;
				}
			}
			f.action="sigesp_siv_pdt_despachos.php";
			f.submit();
		}
	}
	
	function ue_agregar()
	{
		f=document.form1;
		li_totrows=f.totalfilas.value;
		li_totrowslocal=f.totalfilaslocal.value;
		despachada=0;
		if(li_totrowslocal>1)
		{
			valido=false;
			for(li_i=1; li_i<=li_totrows;li_i++)
			{
				ls_codartprigrid=eval("f.txtcodartpri1.value");
				ls_codartopnner=eval("opener.document.form1.txtcodart"+li_i+".value");
				codartpriopnner= eval("opener.document.form1.txtcodartpri"+li_i+".value");
				canartopnner= eval("opener.document.form1.txtcanart"+li_i+".value");
				canartopnner=ue_formato_operaciones(canartopnner);
				if(codartpriopnner==ls_codartprigrid)
				{
					despachada=(parseFloat(despachada) + parseFloat(canartopnner));
				}
				if(ls_codartprigrid==ls_codartopnner)
				{
					valido=true;
					row=li_i;
				}
			}
			if(valido==false)
			{
				alert("El Codigo primario del Articulo no se encuentra en la SEP.")
			}
			else
			{
				penart=eval("opener.document.form1.txthidpenart"+row+".value");
				cansol=eval("opener.document.form1.txtcansol"+row+".value");
				ctasep=eval("opener.document.form1.txtctasep"+row+".value");
				cansol=ue_formato_operaciones(cansol);
				total=0;
				for(i=1;i<li_totrowslocal;i++)
				{
					existencia= eval("f.txtexistencia"+i+".value");
					cantidad= eval("f.txtcanart"+i+".value");
					existencia=ue_formato_operaciones(existencia);
					cantidad=ue_formato_operaciones(cantidad);
					if(parseFloat(existencia)<parseFloat(cantidad))
					{
						alert("No existe existencia para despachar la cantidad solicitada.");
						objeto=eval("f.txtcanart"+i);
						objeto.value="0,00";
						total=0;
						break;
					}
					else
					{
						total=(parseFloat(total) + parseFloat(cantidad));
					}
				}//for
				if(total>0)
				{
					if(penart>0)
					{
						penart=(parseFloat(penart) - parseFloat(despachada));
						if(penart>0)
						{
							if(parseFloat(total)<=parseFloat(penart))
							{
								f.operacion.value="AGREGAR";
								f.penart.value=penart;
								f.cansol.value=cansol;
								f.ctasep.value=ctasep;
								f.action="sigesp_siv_pdt_despachos.php";
								f.submit();
								opener.form1.rdtipodespacho[1].checked=true;
							}
							else
							{
								alert("El total de articulos a despachar es mayor que el solicitado/pendiente.");
							}
						}
						else
						{
							alert("El total de articulos a despachar es mayor que el solicitado/pendiente.");
						}
					}
					else
					{
						pendiente=(parseFloat(cansol) - parseFloat(despachada));
						if(pendiente>0)
						{
							if(parseFloat(total)<=parseFloat(cansol))
							{
								f.operacion.value="AGREGAR";
								f.penart.value=penart;
								f.cansol.value=cansol;
								f.action="sigesp_siv_pdt_despachos.php";
								f.submit();
								opener.form1.rdtipodespacho[1].checked=true;
							}
							else
							{
								alert("El total de articulos a despachar es mayor que el solicitado/pendiente.");
							}
						}
						else
						{
							alert("El total de articulos a despachar es mayor que el solicitado/pendiente.");
						}
					}
				}
				else
				{
					alert("Debe indicar las cantides a despachar.");
				}
			}
		}
		else
		{
			alert("Debe indicar los articulos a Despachar");
		}
	}//ue_agregar()
	
	
function ue_enviar(e)
{
    var whichCode = (window.Event) ? e.which : e.keyCode; 
	if (whichCode == 13) // Enter 
	{
		ue_verificar_articulo();
	}
}

function ue_buscaralmacen()
{
	f=document.form1;
	li_linea="";
	ls_articulo=f.txtcodart.value;
	cansol=0;
	penart="";
	unidad="";
	window.open("sigesp_catdinamic_almacendespacho.php?linea="+li_linea+"&codart="+ls_articulo+"&cansol="+cansol+"&penart="+penart+"&unidad="+unidad+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=425,height=400,left=180,top=160,location=no,resizable=yes");
}

function ue_focus()
{
	f=document.form1;
	if(f.txtcodart.value=="")
	{
		f.txtcodart.focus();
	}
}

function uf_agregar_dt()
{
	f=document.form1;
	f.operacion.value="BUSCAR";
	articulo=f.txtcodart.value;
	li_totrows=f.totalfilas.value;
	li_totrowslocal=f.totalfilaslocal.value;
	ls_codart=f.txtcodart.value;
	ls_codartpri=f.txtcodartpri.value;
	ls_almacen=f.txtcodalm.value;
	li_existencia=f.hidexistencia.value;
	li_canart=f.txtcanart.value;
	li_canart=ue_formato_operaciones(li_canart);
	lb_valido=false;
	
	for(li_i=1;li_i<li_totrowslocal&&lb_valido!=true;li_i++)
	{
		ls_codartgrid=    eval("f.txtcodart"+li_i+".value");
		ls_codartprigrid=    eval("f.txtcodartpri"+li_i+".value");
		ls_almacengrid=    eval("f.txtunidad"+li_i+".value");
		if((ls_codart==ls_codartgrid)&&(ls_almacen==ls_almacengrid))
		{
			alert("El detalle ya esta registrado");
			lb_valido=true;
			break;
		}
		if(ls_codartpri!=ls_codartprigrid)
		{
			alert("Solo se pueden registrar articulos relacionados al articulo "+ls_codartprigrid);
			lb_valido=true;
			break;
		}
	}
	if(parseFloat(li_existencia)<parseFloat(li_canart))
	{
		alert("La cantidad a despachar es mayor que la existencia.");
		lb_valido=true;
	}
	
	if((ls_codart=="")||(ls_almacen=="")||(li_canart==""))
	{
		alert("Debe llenar todos los campos");
		lb_valido=true;
	}

	for(li_i=1; li_i<=li_totrows;li_i++)
	{
		ls_codartopnner=eval("opener.document.form1.txtcodart"+li_i+".value");
		if(ls_codartopnner==ls_codart)
		{
			lb_valido=true;
			alert("El articulo ya esta registrado en el detalle del despacho");
			break;
		}
	}
	if(!lb_valido)
	{
		f.operacion.value="AGREGARDETALLE";
		f.action="sigesp_siv_pdt_despachos.php";
		f.submit();
	}
}
function uf_delete_dt(li_row)
{
	f=document.form1;
	li_totrows=f.totalfilaslocal.value
	if(li_totrows!=li_row)
	{
		if(confirm("¿Desea eliminar el Registro actual?"))
		{	
			f.filadelete.value=li_row;
			f.operacion.value="ELIMINARDETALLE"
			f.action="sigesp_siv_pdt_despachos.php";
			f.submit();
		}
	}
	else
	{
		alert("Esta fila no puede ser eliminada");
	}
}

function ue_cancelar()
{
	close();
}
</script> 
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script  src="js/funciones.js"></script>
</html>