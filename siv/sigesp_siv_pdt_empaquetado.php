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
		$li_canart=0;
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

<body>
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
			$ls_codartemp=$io_fun_inventario->uf_obtenervalor_get("codartemp","");
			$ls_denartemp=$io_fun_inventario->uf_obtenervalor_get("denartemp","");
			$li_canartemp=$io_fun_inventario->uf_obtenervalor_get("canartemp","0,00");
			$ls_codalm=$io_fun_inventario->uf_obtenervalor_get("codalm","");
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
				print "opener.document.formulario.operacion.value=";
				print "obj=eval(opener.document.formulario.operacion);";
				print "obj.value='".$ls_opeopener."';";
				print "opener.document.formulario.submit();";
				print "close();";
				print "</script>";
			}
		break;
	}
?>
<form name="formulario" method="post" action="">
  <table width="750" border="0" align="center" class="formato-blanco">
    <tr>
      <td height="22" colspan="4" class="titulo-celda">Detalle de Art&iacute;culo </td>
    </tr>
    <tr>
      <td width="169"><div align="right">Articulo</div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtcodartemp" type="text" class="sin-borde2" id="txtcodartemp" style="text-align:center " onBlur="javascript: ue_verificar_articulo(); " onKeyPress="javascript: ue_enviar(event);" value="<?php print $ls_codartemp; ?>">
        
          <input name="txtdenartemp" type="text" class="sin-borde2" id="txtdenartemp" value="<?php print $ls_denartemp; ?>" size="40" readonly>
      </div></td>
    </tr>
    <tr>
      <td><div align="right">Cantidad de Empaques </div></td>
      <td height="22"><input name="txtcanartemp" type="text" class="texto-rojo" id="txtcanartemp" style="text-align:right" value="<?php print $li_canartemp; ?>" size="10" readonly>
      <a href="javascript: ue_buscaralmacen();"></a></td>
      <td height="22">&nbsp;</td>
      <td height="22">&nbsp;</td>
    </tr>
    <tr>
      <td><div align="right">Tipo de Articulo </div></td>
      <td height="22" colspan="3"><input name="txtcodtipart" type="text" id="txtcodtipart" value="<?php print $ls_codtipart?>" size="6" maxlength="4" style="text-align:center" readonly>
        <a href="javascript: ue_catatipart();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
        <input name="txtdentipart" type="text" class="sin-borde" id="txtdentipart" value="<?php print $ls_dentipart?>" size="30" readonly>
        <input name="txtobstipart" type="hidden" id="txtobstipart"></td>
    </tr>
    <tr>
      <td><div align="right">Cantidad por Empaque </div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtcanart" type="text" id="txtcanart" style="text-align:right " value="<?php print number_format($li_canart,2,",","."); ?>" size="10"  onKeyPress="return(ue_formatonumero(this,'.',',',event));" onBlur="javascript: calcularTotal();">
      </div></td>
    </tr>
    <tr>
      <td><div align="right">Total de Articulos Requeridos </div></td>
      <td height="22" colspan="3"><input name="txtcantidad" type="text" id="txtcantidad" style="text-align:right "  size="12" readonly></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="22" colspan="3"><input name="btnbuscar" type="button" class="boton" id="btnbuscar" value="Buscar Disponibles" onClick="javascript: buscarDisponibles();">
      <input name="txtcodalm" type="hidden" id="txtcodalm" value="<?php print $ls_codalm; ?>"></td>
    </tr>
    <tr>
      <td height="22" colspan="4">
		<div align="center"><div id="articulos"></div>
		<?php
		//	$in_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
		?>
		</div></td>
    </tr>
    <tr>
      <td><div align="right"></div></td>
      <td width="271" align="center"><input name="operacion" type="hidden" id="operacion">
      <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrowsopenner;?>">
      <input name="totalfilaslocal" type="hidden" id="totalfilaslocal" value="<?php print $li_totrows;?>">
      <input name="filadelete" type="hidden" id="filadelete" value="<?php print $li_rowdelete;?>">
      <input name="hidcosto" type="hidden" id="hidcosto"  value="<?php print number_format($li_cosart,2,",","."); ?>">
      <input name="hidunidad" type="hidden" id="hidunidad" value="<?php print $li_unidad; ?>">
	  <input name="penart" type="hidden" id="penart" value="<?php print $li_penart; ?>">
	  <input name="cansol" type="hidden" id="cansol" value="<?php print $li_cansol; ?>">
	  <input name="ctasep" type="hidden" id="ctasep" value="<?php print $ls_ctasep; ?>">
      <input name="hidalmacen" type="hidden" id="hidalmacen" value="<?php print $ls_codalm; ?>"></td>
	  <td width="124"><a href="javascript: ue_agregar();"></a></td>
      <td width="166"><div align="right"><a href="javascript: ue_cancelar();"><img src="../shared/imagebank/tools15/eliminar.gif" width="15" height="15" class="sin-borde">Cancelar</a> </div></td>
    </tr>
  </table>
</form>
</body>
<script >

function calcularTotal()
{
	f=document.formulario;
	totpaq=f.txtcanartemp.value;
	totpaq=ue_formato_operaciones(totpaq);
	totart=f.txtcanart.value;
	totart=ue_formato_operaciones(totart);
	total=(parseFloat(totpaq) * parseFloat(totart));
	total=formato_numero(total,2,',','.');
	f.txtcantidad.value=total;
	buscarDisponibles();
	
}
function buscarDisponibles()
{
	f=document.formulario;
	// Cargamos las variables para pasarlas al AJAX
	codtipart=f.txtcodtipart.value;
	totartreq=f.txtcantidad.value;
	codalm=f.txtcodalm.value;
	if((codtipart!="")&&(totartreq!=""))
	{
		// Div donde se van a cargar los resultados
		divgrid = document.getElementById('articulos');
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde están los métodos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_siv_c_empaquetado_ajax.php",true);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				divgrid.innerHTML = ajax.responseText
			}
		}
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		// Enviar todos los campos a la pagina para que haga el procesamiento
		ajax.send("codtipart="+codtipart+"&totartreq="+totartreq+"&codalm="+codalm+"&proceso=DISPONIBLES");
	}
	else
	{
		alert("Debe indicar el tipo de Articulo y la cantidad por empaque");
	}
}

function ue_agregar(codartsal,li_i,totartreq)
{
	f=document.formulario;
	valido=true;
	parametros="";
	totrowent=2;
	totrowsal=ue_calcular_total_fila_opener("txtcodart");
	opener.document.formulario.totrowartent.value=totrowent;
	opener.document.formulario.totrowartsal.value=totrowsal;
	totpaqreq=f.txtcanartemp.value;
	//---------------------------------------------------------------------------------
	// Verificamos que el compromiso no exista
	//---------------------------------------------------------------------------------
	for(j=1;(j<totrowsal)&&(valido);j++)
	{
		codartgrid=eval("opener.document.formulario.txtcodart"+j+".value");
		if(codartgrid==codartsal)
		{
			alert("El Articulo ya está en la incluido");
			valido=false;
		}
	}
	//---------------------------------------------------------------------------------
	// recorremos grid de las cuentas presupuestarias
	//---------------------------------------------------------------------------------
	for(j=1;(j<totrowsal)&&(valido);j++)
	{
		codart=eval("opener.document.formulario.txtcodart"+j+".value");
		denart=eval("opener.document.formulario.txtdenart"+j+".value");
		denunimed="UNIDAD(ES)";
		canart=eval("opener.document.formulario.txtcanart"+j+".value");
		cosuni=eval("opener.document.formulario.txtcosuni"+j+".value");
		cossubtotsal=eval("opener.document.formulario.txtcossubtotsal"+j+".value");
		parametros=parametros+"&txtcodart"+j+"="+codart+"&txtdenart"+j+"="+denart+""+
				   "&txtdenunimed"+j+"="+denunimed+"&txtcanart"+j+"="+canart+""+
				   "&txtcosuni"+j+"="+cosuni+"&txtcossubtotsal"+j+"="+cossubtotsal;
	}
		codart=eval("document.formulario.txtcodart"+li_i+".value");
		denart=eval("document.formulario.txtdenart"+li_i+".value");
		denunimed="UNIDAD(ES)";
		canart=totartreq;
		cosuni=eval("document.formulario.txtcosart"+li_i+".value");
		cossubtotsal=eval("document.formulario.txttotcos"+li_i+".value");
		parametros=parametros+"&txtcodart"+j+"="+codart+"&txtdenart"+j+"="+denart+""+
				   "&txtdenunimed"+j+"="+denunimed+"&txtcanart"+j+"="+canart+""+
				   "&txtcosuni"+j+"="+cosuni+"&txtcossubtotsal"+j+"="+cossubtotsal;
	totrowsal=totrowsal+1;
	parametros=parametros+"&totartsal="+totrowsal+"";

	for(j=1;(j<totrowent)&&(valido);j++)
	{
		codart=eval("opener.document.formulario.txtcodartent"+j+".value");
		denart=eval("opener.document.formulario.txtdenartent"+j+".value");
		denunimed="UNIDAD(ES)";
		canart=eval("opener.document.formulario.txtcanartent"+j+".value");
		cosunient=eval("opener.document.formulario.txtcosuni"+j+".value");
		cosunientaux=ue_formato_operaciones(cosunient);
		totpaqaux=ue_formato_operaciones(totpaq);
		txtcosunient=(parseFloat(cosuni) + parseFloat(cosunientaux));

		cossubtotent=eval("opener.document.formulario.txtcossubtotsal"+j+".value");
		cossubtotsalaux=ue_formato_operaciones(cossubtotsal);
		cossubtotentaux=ue_formato_operaciones(cossubtotent);
		txtcosunient=(parseFloat(cossubtotsalaux) + parseFloat(cossubtotent));

		parametros=parametros+"&txtcodartent"+j+"="+codart+"&txtdenartent"+j+"="+denart+""+
				   "&txtdenunimedent"+j+"="+denunimed+"&txtcanartent"+j+"="+canart+""+
				   "&txtcosunient"+j+"="+txtcosunient+"&txtcosunient"+j+"="+txtcosunient;
	}
	parametros=parametros+"&totartent="+totrowent+"&totpaqreq="+totpaqreq;

	if((parametros!="")&&(valido))
	{
		// Div donde se van a cargar los resultados
		divgrid = opener.document.getElementById("articulos");
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde están los métodos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_siv_c_empaquetado_ajax.php",true);
		ajax.onreadystatechange=function(){
			if(ajax.readyState==1)
			{
				//divgrid.innerHTML = "";//<-- aqui iria la precarga en AJAX 
			}
			else
			{
				if(ajax.readyState==4)
				{
					if(ajax.status==200)
					{//mostramos los datos dentro del contenedor
						divgrid.innerHTML = ajax.responseText
					}
					else
					{
						if(ajax.status==404)
						{
							divgrid.innerHTML = "La página no existe";
						}
						else
						{//mostramos el posible error     
							divgrid.innerHTML = "Error:".ajax.status;
						}
					}
					
				}
			}
		}	
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		// Enviar todos los campos a la pagina para que haga el procesamiento
		ajax.send("proceso=LIMPIAR"+parametros);
	}
}

	
function ue_catatipart()
{

	window.open("sigesp_catdinamic_tipoarticulo.php?tipo=EMPAQUETADO","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}


function ue_cancelar()
{
	close();
}
</script> 
<script type="text/javascript"  src="js/funcion_siv.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script  src="js/funciones.js"></script>
</html>