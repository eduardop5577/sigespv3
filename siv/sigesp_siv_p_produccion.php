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
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
if(!array_key_exists("la_logusr",$_SESSION))
{
	print "<script language=JavaScript>";
	print "window.close();";
	print "</script>";		
}
$ls_logusr=$_SESSION["la_logusr"];
require_once("class_funciones_inventario.php");
$io_fun_inventario=new class_funciones_inventario();
$ls_permisos = "";
$la_seguridad = Array();
$la_permisos = Array();
$arrResultado = $io_fun_inventario->uf_load_seguridad("SIV","sigesp_siv_p_produccion.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos = $arrResultado['as_permisos'];
$la_seguridad = $arrResultado['aa_seguridad'];
$la_permisos = $arrResultado['aa_permisos'];
$ls_reporte=$io_fun_inventario->uf_select_config("SIV","REPORTE","PRODUCCION","sigesp_siv_rfs_produccion.php","C");
$ls_estartpri=$io_fun_inventario->uf_select_config("SIV","CONFIG","ARTICULO_PRI","","C");
$ls_produccion=$io_fun_inventario->uf_select_config("SIV","CONFIG","PRODUCCION","","C");
$li_numdecper=$_SESSION["la_empresa"]["numdecper"];
if($ls_produccion==0)
{
	print "<script language=JavaScript>";
	print "location.href='sigespwindow_blank.php'";
	print "</script>";		
}
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   function uf_obtenervalor($as_valor, $as_valordefecto)
   {
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_obtenervalor
	//	Access:    public
	//	Arguments:
    // as_valor         //  nombre de la variable que desamos obtener
    // as_valordefecto  //  contenido de la variable
    // Description: Funci?n que obtiene el valor de una variable si viene de un submit
	//////////////////////////////////////////////////////////////////////////////
		if(array_key_exists($as_valor,$_POST))
		{
			$valor=$_POST[$as_valor];
		}
		else
		{
			$valor=$as_valordefecto;
		}
   		return $valor; 
   }
   //--------------------------------------------------------------
   
   function uf_seleccionarcombo($as_valores,$as_seleccionado,$aa_parametro,$li_total)
   {
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_seleccionarcombo
	//	Access:    public
	//	Arguments:
	//  as_valores // valores que puede tomar el combo
	//  as_seleccionado // item seleccionado
	//  aa_parametro  // arreglo de seleccionados
	//  li_total // total de elementos en el combo
	//	Description:  Esta funcion mantiene la seleccion de un combo despues de hacer un submit
	//              
	//////////////////////////////////////////////////////////////////////////////		
   		$la_valores = explode("-",$as_valores);
		for($li_index=0;$li_index<$li_total;++$li_index)
		{
			if($la_valores[$li_index]==$as_seleccionado)
			{
				$aa_parametro[$li_index]=" selected";
			}
		}
		return $aa_parametro;
   }
   //--------------------------------------------------------------

   function uf_agregarlineablanca($aa_object,$ai_totrows)
   {
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_agregarlineablanca
	//	Access:    public
	//	Arguments:
	//  aa_object // arreglo de titulos 
	//  ai_totrows // ultima fila pintada en el grid
	//	Description:  Funcion que agrega una linea en blanco al final del grid
	//              
	//////////////////////////////////////////////////////////////////////////////		
		global $li_numdecper;
		if($li_numdecper!="3")
		{
			$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
		}
		else
		{
			$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
		}
		$aa_object[$ai_totrows][1]="<input name=txtdenart".$ai_totrows."   type=text   id=txtdenart".$ai_totrows." class=sin-borde size=20 maxlength=50 readonly>".
								   "<input name=txtcodart".$ai_totrows."   type=hidden id=txtcodart".$ai_totrows." class=sin-borde size=21 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);' readonly>".
								   "<a href='javascript: ue_catarticulo(".$ai_totrows.");'><img src='../shared/imagebank/tools15/buscar.gif' alt='Codigo de Articulo' width='18' height='18' border='0'></a>";
		$aa_object[$ai_totrows][2]="<input name=txtdenunimed".$ai_totrows." type=text id=txtdenunimed".$ai_totrows." class=sin-borde size=14 maxlength=12 readonly>";
		$aa_object[$ai_totrows][3]="<div align='center'><select name=cmbunidad".$ai_totrows." style='width:100px ' onChange='javascript:ue_montosfactura(".$ai_totrows.");'><option value=D>Detal</option><option value=M selected>Mayor</option></select></div><input name='hidunidad".$ai_totrows."' type='hidden' id='hidunidad".$ai_totrows."'>";
		$aa_object[$ai_totrows][4]="<input name=txtcantidad".$ai_totrows." type=text id=txtcantidad".$ai_totrows." class=sin-borde size=14 maxlength=12 $ls_funcion onBlur='javascript:ue_montosfactura(".$ai_totrows.");'>".
								   "<input name=hidexistencia".$ai_totrows." type=hidden id=hidexistencia".$ai_totrows.">";
		$aa_object[$ai_totrows][5]="<input name=txtcosuni".$ai_totrows."   type=text id=txtcosuni".$ai_totrows."   class=sin-borde size=14 maxlength=15  readonly>";
		$aa_object[$ai_totrows][6]="<input name=txtcostot".$ai_totrows."   type=text id=txtcostot".$ai_totrows."   class=sin-borde size=14 maxlength=15  readonly>";
		$aa_object[$ai_totrows][7]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif alt=Aceptar width=15 height=15 border=0></a>";
		$aa_object[$ai_totrows][8]="";//
		return $aa_object;			
   }
   	//--------------------------------------------------------------
   function uf_agregarlineablanca_entrada($aa_object,$ai_totrows)
   {
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_agregarlineablanca
	//	Access:    public
	//	Arguments:
	//  aa_object // arreglo de titulos 
	//  ai_totrows // ultima fila pintada en el grid
	//	Description:  Funcion que agrega una linea en blanco al final del grid
	//              
	//////////////////////////////////////////////////////////////////////////////		
		global $li_numdecper;
		if($li_numdecper!="3")
		{
			$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
		}
		else
		{
			$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
		}
		$aa_object[$ai_totrows][1]="<input name=txtdenartent".$ai_totrows."   type=text   id=txtdenartent".$ai_totrows." class=sin-borde size=20 maxlength=50 readonly>".
								   "<input name=txtcodartent".$ai_totrows."   type=hidden id=txtcodartent".$ai_totrows." class=sin-borde size=21 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);' readonly>".
								   "<a href='javascript: ue_catarticuloent(".$ai_totrows.");'><img src='../shared/imagebank/tools15/buscar.gif' alt='Codigo de Articulo' width='18' height='18' border='0'></a>";
		$aa_object[$ai_totrows][2]="<input name=txtdenunimedent".$ai_totrows." type=text id=txtdenunimedent".$ai_totrows." class=sin-borde size=14 maxlength=12 readonly>";
		$aa_object[$ai_totrows][3]="<div align='center'><select name=cmbunidadent".$ai_totrows." style='width:100px ' onChange='javascript:ue_costounitario(".$ai_totrows.");'><option value=D>Detal</option><option value=M selected>Mayor</option></select></div><input name='hidunidadent".$ai_totrows."' type='hidden' id='hidunidadent".$ai_totrows."'>";
		$aa_object[$ai_totrows][4]="<input name=txtcantidadent".$ai_totrows." type=text id=txtcantidadent".$ai_totrows." class=sin-borde size=14 maxlength=12 $ls_funcion onBlur='javascript:ue_costounitario(".$ai_totrows.");'>";
		$aa_object[$ai_totrows][5]="<input name=txtcosunient".$ai_totrows."   type=text id=txtcosunient".$ai_totrows."   class=sin-borde size=14 maxlength=15  readonly>";
		$aa_object[$ai_totrows][6]="<input name=txtcostotent".$ai_totrows."   type=text id=txtcostotent".$ai_totrows."   class=sin-borde size=14 maxlength=15  readonly>";
		return $aa_object;
   }
   	//--------------------------------------------------------------

   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Funci?n que limpia todas las variables necesarias en la p?gina
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_numtra,$ld_fecemi,$ls_codusu,$ls_codalmori,$ls_codalmdes,$ls_nomalmori,$ls_nomalmdes,$ls_obstra,$ls_readonly;
		global $ls_status,$ls_estint;
		$ls_numtra="";
		$ld_fecemi=date("d/m/Y ");
		$ls_codusu=$_SESSION["la_logusr"];
		$ls_codalmori="";
		$ls_codalmdes="";
		$ls_nomalmori="";
		$ls_nomalmdes="";
		$ls_obstra="";
		$ls_readonly="true";
		$ls_status="";
		$ls_estint="";
   }

   function uf_obtenervalorunidad($li_i)
   {
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_obtenervalorunidad
	//	Access:    public
	//	Arguments:
    // 				li_i         //  valor del 
    // 				ls_valor     //  nombre de la variable que desamos obtener
    // Description: Funci?n que obtiene el contenido del combo cmbunidad o 
	//				del campo txtunidad deacuerdo sea el caso 
	//////////////////////////////////////////////////////////////////////////////
		if (array_key_exists("cmbunidad".$li_i,$_POST))
		{
			$ls_valor= $_POST["cmbunidad".$li_i];
		}
		else
		{
			$ls_valoraux= $_POST["txtcoduni".$li_i];
			if($ls_valoraux=="Mayor")
			{
				$ls_valor="M";
			}
			else
			{
				$ls_valor="D";
			}
		}
   		return $ls_valor; 
   }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Conversion de articulos</title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">

</style>
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<script type="text/javascript"  src="js/funciones.js"></script>
<script type="text/javascript"  src="../shared/js/valida_tecla.js"></script>
<link href="css/siv.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey))
		{
			window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ return false;} 
		} 
	}
</script>
</head>

<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="12" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="12" bgcolor="#E7E7E7" class="cd-menu">
	<table width="776" border="0" align="center" cellpadding="0" cellspacing="0">
	
		<td width="423" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Inventario </td>
		<td width="353" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  <tr>
		<td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
		<td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
	</table></td>
  </tr>
  <tr>
    <td height="20" colspan="12" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="12" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" width="20" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0"></a></td>
    <td class="toolbar" width="24"><div align="center"><a href="javascript: ue_imprimir('<?php print $ls_reporte ?>');"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="24"><div align="center"></div></td>
    <td class="toolbar" width="618">&nbsp;</td>
  </tr>
</table>
<?php
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=     new sigesp_include();
	$con=    $in->uf_conectar();
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql= new class_sql($con);
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_msg= new class_mensajes();
	require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fec= new class_fecha();
	require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
	$io_fun= new class_funciones_db($con);
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_func= new class_funciones();
	require_once("../shared/class_folder/grid_param.php");
	$in_grid=new grid_param();
	require_once("sigesp_siv_c_produccion.php");
	$io_siv= new sigesp_siv_c_produccion();
	require_once("sigesp_siv_c_articuloxalmacen.php");
	$io_art= new sigesp_siv_c_articuloxalmacen();
	require_once("sigesp_siv_c_movimientoinventario.php");
	$io_mov= new sigesp_siv_c_movimientoinventario();
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_codusu=$_SESSION["la_logusr"];
	$li_totrows = uf_obtenervalor("totalfilas",1);
	$li_totrowsc= uf_obtenervalor("totalfilasc",0);
	$ls_estint= uf_obtenervalor("txtestint","");

	$ls_titletable="Articulos Salientes";
	$li_widthtable=600;
	$ls_nametable="grid";
	$lo_title[1]="Art?culo";
	$lo_title[2]="Unidad de Medida";
	$lo_title[3]="Modalidad";
	$lo_title[4]="Cantidad";
	$lo_title[5]="Costo Unitario";
	$lo_title[6]="Costo Total";
	$lo_title[7]="";
	$lo_title[8]="";
	
	$ls_titletableent="Articulo Generado";
	$lo_titleent[1]="Art?culo";
	$lo_titleent[2]="Unidad de Medida";
	$lo_titleent[3]="Modalidad";
	$lo_titleent[4]="Cantidad";
	$lo_titleent[5]="Costo Unitario";
	$lo_titleent[6]="Costo Total";
	
	$ls_titlecontable="Detalle Contable";
	$li_widthcontable=600;
	$ls_namecontable="grid";
	$lo_titlecontable[1]="Art?culo";
	$lo_titlecontable[2]="Cuenta";
	$lo_titlecontable[3]="Debe/Haber";
	$lo_titlecontable[4]="Monto";

	
	if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion= $_POST["operacion"];
		$ls_status=    $_POST["hidestatus"];
		if ($ls_status=="C")
		{
			$ls_readonly=$_POST["hidreadonly"];
			if (array_key_exists("catafilas",$_POST))
			{
				$li_catafilas=$_POST["catafilas"];
			}
			else
			{
			$li_catafilas="";
			}
		}
		else
		{
			$ls_status="";
		}
	}
	else
	{
		$ls_operacion="";
		uf_limpiarvariables();
		$lo_object = uf_agregarlineablanca($lo_object,1);
		$lo_objectent = uf_agregarlineablanca_entrada($lo_objectent,1);
	}
	switch ($ls_operacion) 
	{

		case "NUEVO":
			$lo_object = uf_agregarlineablanca($lo_object,1);
			$lo_objectent = uf_agregarlineablanca_entrada($lo_objectent,1);
			uf_limpiarvariables();
			$li_totrows=1;

		break;
		case "GUARDAR":
			$ls_numtra=     $_POST["txtnumtra"];
			$ld_fecemi=     $_POST["txtfecemi"];
			$ls_codalmori=  $_POST["txtcodalm"];
			$ls_codalmdes=  $_POST["txtcodalmdes"];
			$ls_nomalmori=  $_POST["txtnomfisalm"];
			$ls_nomalmdes=  $_POST["txtnomfisdes"];
			$ls_obstra=     $_POST["txtobstra"];
			$lb_valido=$io_fec->uf_valida_fecha_mes($ls_codemp,$ld_fecemi);
			if($lb_valido)
			{ 
				$ld_fecemi=$io_func->uf_convertirdatetobd($ld_fecemi);
				if ($ls_status=="C")
				{
					$io_msg->message("El proceso de Produccion no puede ser modificado");
					$li_totrows=1;
					$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
					uf_limpiarvariables();
				}
				else
				{ 
					$lb_encontrado=$io_siv->uf_siv_select_produccion($ls_codemp,$ls_numtra,$ld_fecemi);					
					if ($lb_encontrado)
					{
						$io_msg->message("El proceso de Produccion ya existe"); 
					}
					else
					{
						$io_sql->begin_transaction();
						$ls_nummov="";					
						$lb_valido1=false;
						$lb_existencia=false;
						$arrResultado=$io_siv->uf_siv_insert_produccion($ls_codemp,$ls_numtra,$ld_fecemi,$ls_codusu,$ls_codalmori,$ls_codalmdes,$ls_obstra,$la_seguridad);
						$ls_numtra = $arrResultado['as_numtra'];
						$lb_valido = $arrResultado['lb_valido'];
						if ($lb_valido)
						{
							$ls_nummov=0;
							$ls_nomsol="Produccion";
							$arrResultado=$io_mov->uf_siv_insert_movimiento($ls_nummov,$ld_fecemi,$ls_nomsol,$ls_codusu,$la_seguridad);
							$ls_nummov=$arrResultado['as_nummov'];
							$lb_valido=$arrResultado['lb_valido'];
							if ($lb_valido)
							{ 
								for($li_i=1;$li_i<$li_totrows;$li_i++)
								{
								
									$li_unidad=   $_POST["hidunidad".$li_i];
									$ls_unidad=   $_POST["txtcoduni".$li_i];
									$ls_denunimed=   $_POST["txtdenunimed".$li_i];
									$ls_codart=   $_POST["txtcodart".$li_i];
									$ls_denart=   $_POST["txtdenart".$li_i];
									$li_cantidad= $_POST["txtcantidad".$li_i];
									$li_cosuni=   $_POST["txtcosuni".$li_i];
									$li_costot=   $_POST["txtcostot".$li_i];
									$li_existencia=$_POST["hidexistencia".$li_i];
									$li_cantidad=  str_replace(".","",$li_cantidad);
									$li_cantidad=  str_replace(",",".",$li_cantidad);
									$li_cosuni=    str_replace(".","",$li_cosuni);
									$li_cosuni=    str_replace(",",".",$li_cosuni);
									$li_costot=    str_replace(".","",$li_costot);
									$li_costot=    str_replace(",",".",$li_costot);


									switch ($ls_unidad) 
									{
										case "Mayor":
											$ls_coduni="M";
											$li_cantidad=($li_cantidad * $li_unidad);
											break;
										case "Detal":
											$ls_coduni="D";
											break;
									}
	
									$lb_valido=$io_art->uf_siv_chequear_articuloxalmacen($ls_codemp,$ls_codart,$ls_codalmori,$li_cantidad);
									if ($lb_valido)
									{
										$lb_valido=$io_siv->uf_siv_guardar_dt_produccion($ls_codemp,$ls_numtra,$ld_fecemi,$ls_codart,
																							$ls_coduni,$li_cantidad,$li_cosuni,$li_costot,
																							$la_seguridad);
										if ($lb_valido)
										{
											$lb_valido=$io_art->uf_siv_disminuir_articuloxalmacen($ls_codemp,$ls_codart,$ls_codalmori,$li_cantidad,$la_seguridad);
											if ($lb_valido)
											{
												if($lb_valido)
												{
													$lb_valido=$io_siv->uf_siv_procesar_dt_movimientotransferencia($ls_codemp,$ls_nummov,$ls_codart,
																												   $ls_codalmori,$ls_unidad,$li_cantidad,
																												   $li_cosuni,$ld_fecemi,$ls_numtra,
																												   $la_seguridad);
												}
											}
										}
									}
//
									if($li_numdecper!="3")
									{
										$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
										$li_cantidad=number_format($li_cantidad,2,",",".");
									}
									else
									{
										$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
										$li_cantidad=number_format($li_cantidad,3,",",".");
									}
									$lo_object[$li_i][1]="<input name=txtdenart".$li_i."   type=text   id=txtdenart".$li_i."   class=sin-borde size=20 maxlength=50 value='".$ls_denart."' readonly>".
														 "<input name=txtcodart".$li_i."   type=hidden id=txtcodart".$li_i."   class=sin-borde size=21 maxlength=20 value='".$ls_codart."' onKeyUp='javascript: ue_validarcomillas(this);' readonly>";
									$lo_object[$li_i][2]="<input name=txtdenunimed".$li_i." type=text  id=txtdenunimed".$li_i." class=sin-borde size=14 maxlength=12 value='".$ls_denunimed."'readonly>";
									$lo_object[$li_i][3]="<input name=txtcoduni".$li_i."   type=text   id=txtcoduni".$li_i."   class=sin-borde size=14 maxlength=12 value='".$ls_unidad."' onKeyUp='javascript: ue_validarcomillas(this);' readonly>".
													     "<input name='hidunidad".$li_i."' type=hidden id=hidunidad".$li_i."   value='". $li_unidad ."'>";
									$lo_object[$li_i][4]="<input name=txtcantidad".$li_i." type=text   id=txtcantidad".$li_i." class=sin-borde size=14 maxlength=12 value='".$li_cantidad."' onKeyUp='javascript: ue_validarnumero(this);' readonly>".
								  						 "<input name=hidexistencia".$li_i." type=hidden id=hidexistencia".$li_i."  value='". $li_existencia ."'>";
									$lo_object[$li_i][5]="<input name=txtcosuni".$li_i."   type=text   id=txtcosuni".$li_i."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_cosuni,2,",",".")."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";
									$lo_object[$li_i][6]="<input name=txtcostot".$li_i."   type=text   id=txtcostot".$li_i."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_costot,2,",",".")."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";
									$lo_object[$li_i][7]="";
									$lo_object[$li_i][8]="";			

								}// for
								if($lb_valido)//ENTRADA
								{
									$li_unidadent=   $_POST["hidunidadent1"];
									$ls_unidadent=   $_POST["txtcodunient1"];
									$ls_denunimedent=   $_POST["txtdenunimedent1"];
									$ls_codartent=   $_POST["txtcodartent1"];
									$ls_denartent=   $_POST["txtdenartent1"];
									$li_cantidadent= $_POST["txtcantidadent1"];
									$li_cosunient=   $_POST["txtcosunient1"];
									$li_costotent=   $_POST["txtcostotent1"];
									$li_existenciaent=$_POST["hidexistenciaent1".$li_i];
									$li_cantidadent=  str_replace(".","",$li_cantidadent);
									$li_cantidadent=  str_replace(",",".",$li_cantidadent);
									$li_cosunient=    str_replace(".","",$li_cosunient);
									$li_cosunient=    str_replace(",",".",$li_cosunient);
									$li_costotent=    str_replace(".","",$li_costotent);
									$li_costotent=    str_replace(",",".",$li_costotent);

									switch ($ls_unidad) 
									{
										case "Mayor":
											$ls_coduni="M";
											$li_cantidad=($li_cantidad * $li_unidad);
											break;
										case "Detal":
											$ls_coduni="D";
											break;
									}
									$lb_valido=$io_siv->uf_siv_insert_dt_produccion($ls_codemp,$ls_numtra,$ld_fecemi,$ls_codartent,$ls_coduni,
			                                                 $li_cantidadent,$li_cosunient,$li_costotent,"E",$aa_seguridad);

									if($lb_valido)
									{
										$lb_valido=$io_art->uf_siv_aumentar_articuloxalmacen($ls_codemp,$ls_codartent,$ls_codalmdes,$li_cantidadent,$la_seguridad);
										if($lb_valido)
										{
											$ls_opeinv="ENT";
											$ls_codprodoc="ALM";
											$ls_promov="PRO";
											$lb_valido=$io_mov->uf_siv_insert_dt_movimiento($ls_codemp,$ls_nummov,$ld_fecemi,
																							$ls_codartent,$ls_codalmdes,$ls_opeinv,
																							$ls_codprodoc,$ls_numtra,$li_cantidadent,
																							$li_cosunient,$ls_promov,$ls_numtra,
																							$li_cantidadent,$ld_fecemi,$la_seguridad);
										}
									}
//
									if($li_numdecper!="3")
									{
										$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
										$li_cantidadent=number_format($li_cantidadent,2,",",".");
									}
									else
									{
										$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
										$li_cantidadent=number_format($li_cantidadent,3,",",".");
									}
									$lo_objectent[1][1]="<input name=txtdenartent".$li_i."   type=text   id=txtdenartent".$li_i."   class=sin-borde size=20 maxlength=50 value='".$ls_denartent."' readonly>".
														 "<input name=txtcodartent".$li_i."   type=hidden id=txtcodartent".$li_i."   class=sin-borde size=21 maxlength=20 value='".$ls_codartent."' onKeyUp='javascript: ue_validarcomillas(this);' readonly>";
									$lo_objectent[1][2]="<input name=txtdenunimedent".$li_i." type=text  id=txtdenunimedent".$li_i." class=sin-borde size=14 maxlength=12 value='".$ls_denunimedent."'readonly>";
									$lo_objectent[1][3]="<input name=txtcodunient".$li_i."   type=text   id=txtcodunient".$li_i."   class=sin-borde size=14 maxlength=12 value='".$ls_unidadent."' onKeyUp='javascript: ue_validarcomillas(this);' readonly>".
													     "<input name='hidunidadent".$li_i."' type=hidden id=hidunidadent".$li_i."   value='". $li_unidadent ."'>";
									$lo_objectent[1][4]="<input name=txtcantidadent".$li_i." type=text   id=txtcantidadent".$li_i." class=sin-borde size=14 maxlength=12 value='".$li_cantidadent."' onKeyUp='javascript: ue_validarnumero(this);'>".
								  						 "<input name=hidexistenciaent".$li_i." type=hidden id=hidexistenciaent".$li_i."  value='". $li_existenciaent ."'>";
									$lo_objectent[1][5]="<input name=txtcosunient".$li_i."   type=text   id=txtcosunient".$li_i."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_cosunient,2,",",".")."' onKeyUp='javascript: ue_validarnumero(this);'>";
									$lo_objectent[1][6]="<input name=txtcostotent".$li_i."   type=text   id=txtcostotent".$li_i."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_costotent,2,",",".")."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";

									
								}//ENTRADA
								//CONTABILIDAD
								for($li_i=1;$li_i<=$li_totrowsc;$li_i++)
								{
									$ls_codart=   $io_fun_inventario->uf_obtenervalor("txtcodartc".$li_i,"");
									$ls_denart=   $io_fun_inventario->uf_obtenervalor("txtdenartc".$li_i,"");
									$ls_sccuenta=   $io_fun_inventario->uf_obtenervalor("txtsccuenta".$li_i,"");
									$ls_debhab=   $io_fun_inventario->uf_obtenervalor("txtdebhab".$li_i,"");
									$li_monto=   $io_fun_inventario->uf_obtenervalor("txtmonto".$li_i,"");
									$li_monto=  str_replace(".","",$li_monto);
									$li_monto=  str_replace(",",".",$li_monto);
									switch ($ls_debhab) 
									{
										case "DEBE":
											$ls_debhab="D";
											break;
										case "HABER":
											$ls_debhab="H";
											break;
									}
									$lb_valido=$io_siv->uf_siv_insert_contable($ls_numtra,$ld_fecemi,$ls_codart,$ls_sccuenta,$ls_debhab,$li_monto,
																				 $la_seguridad);
									if($lb_valido)
									{
										$lo_objectc[$li_i][1]="<input  name=txtdenartc".$li_i."  type=text   id=txtdenartc".$li_i."  class=sin-borde size=40 maxlength=50 value='".$ls_denart."' readonly  style=text-align:left>".
																	 "<input  name=txtcodartc".$li_i."  type=hidden id=txtcodartc".$li_i."  class=sin-borde size=30 maxlength=50 value='".$ls_codart."' readonly  style=text-align:center>";
										$lo_objectc[$li_i][2]="<input  name=txtsccuenta".$li_i." type=text   id=txtsccuenta".$li_i." class=sin-borde size=20              value='".$ls_sccuenta."' readonly  style=text-align:center>";
										$lo_objectc[$li_i][3]="<input  name=txtdebhab".$li_i."   type=text   id=txtdebhab".$li_i."   class=sin-borde size=10              value='".$ls_debhab."' readonly style='text-align:center'>";
										$lo_objectc[$li_i][4]="<input  name=txtmonto".$li_i."    type=text   id=txtcansolc".$li_i."  class=sin-borde size=20              value='".number_format ($li_monto,2,",",".")."' style='text-align:right' readonly>";
									}
								
								}
								
								//CONTABILIDAD
							}
						}
						$ld_fecemi=$io_func->uf_convertirfecmostrar($ld_fecemi);
						if($lb_valido)
						{
							$io_sql->commit();
							$io_msg->message("El proceso de Produccion ha sido procesada");
							$ls_status="C";
							$li_totrows=$li_totrows-1;
							$li_totrowsc=0;
						}
						else
						{
							$io_sql->rollback();
							$li_totrows=1;
							$lo_object = uf_agregarlineablanca($lo_object,1);
							$io_msg->message("No se pudo realizar el proceso de Produccion");
							$li_totrowsc=0;
						}
					}
				}
			}
			else
			{
				$io_msg->message("El mes no esta abierto");
				$li_totrows=1;
				$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
				uf_limpiarvariables();
			}
			break;

		case "AGREGARDETALLE":
			$li_totrows=$li_totrows+1;
			$ls_numtra=     $_POST["txtnumtra"];
			$ld_fecemi=     $_POST["txtfecemi"];
			$ls_codalmori=  $_POST["txtcodalm"];
			$ls_codalmdes=  $_POST["txtcodalmdes"];
			$ls_nomalmori=  $_POST["txtnomfisalm"];
			$ls_nomalmdes=  $_POST["txtnomfisdes"];
			$ls_obstra=     $_POST["txtobstra"];
			$li_totentsum= $io_fun_inventario->uf_obtenervalor("txttotentsum","");
			for($li_i=1;$li_i<$li_totrows;$li_i++)
			{	
				$ls_codart=     $_POST["txtcodart".$li_i];
				$ls_denunimed=  $_POST["txtdenunimed".$li_i];
				$ls_denart=     $_POST["txtdenart".$li_i];
				$ls_unidad= uf_obtenervalorunidad($li_i);
				$li_unidad=     $_POST["hidunidad".$li_i];
				$li_cantidad=   $_POST["txtcantidad".$li_i];
				$li_existencia=$_POST["hidexistencia".$li_i];
				$li_cosuni=     $_POST["txtcosuni".$li_i];
				$li_costot=     $_POST["txtcostot".$li_i];
				$li_existencia=     $_POST["hidexistencia".$li_i];
				switch ($ls_unidad) 
				{
					case "M":
						$ls_unidadaux="Mayor";
						break;
					case "D":
						$ls_unidadaux="Detal";
						break;
				}
				$lo_object[$li_i][1]="<input name=txtdenart".$li_i."   type=text id=txtdenart".$li_i."   class=sin-borde size=15 maxlength=50 value='".$ls_denart."' readonly>".
									 "<input name=txtcodart".$li_i." type=hidden id=txtcodart".$li_i." class=sin-borde size=21 maxlength=20 value='".$ls_codart."' onKeyUp='javascript: ue_validarcomillas(this);' readonly><a href='javascript: ue_catarticulo(".$li_i.");'><img src='../shared/imagebank/tools15/buscar.gif' alt='Codigo de Articulo' width='18' height='18' border='0'></a>";
				$lo_object[$li_i][2]="<input name=txtdenunimed".$li_i."   type=text id=txtdenunimed".$li_i."   class=sin-borde size=14 maxlength=12 value='".$ls_denunimed."' readonly>";
				$lo_object[$li_i][3]="<input name=txtcoduni".$li_i."   type=text id=txtcoduni".$li_i."   class=sin-borde size=14 maxlength=12 value='".$ls_unidadaux."' onKeyUp='javascript: ue_validarcomillas(this);' readonly><input name='hidunidad".$li_i."' type='hidden' id='hidunidad".$li_i."' value='". $li_unidad ."'>";
				$lo_object[$li_i][4]="<input name=txtcantidad".$li_i." type=text id=txtcantidad".$li_i." class=sin-borde size=14 maxlength=12 value='".$li_cantidad."' onKeyUp='javascript: ue_validarnumero(this);' readonly>".
								  	 "<input name=hidexistencia".$li_i." type=hidden id=hidexistencia".$li_i."  value='". $li_existencia ."'>";
				$lo_object[$li_i][5]="<input name=txtcosuni".$li_i."   type=text id=txtcosuni".$li_i."   class=sin-borde size=14 maxlength=15 value='".$li_cosuni."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";
				$lo_object[$li_i][6]="<input name=txtcostot".$li_i."   type=text id=txtcostot".$li_i."   class=sin-borde size=14 maxlength=15 value='".$li_costot."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";
				$lo_object[$li_i][7]="";
				$lo_object[$li_i][8]="<a href=javascript:uf_delete_dt(".$li_i.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";			
			}
			$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
			$lo_objectent = uf_agregarlineablanca_entrada($lo_objectent,1);
			$li_totrowsc=1;
			break;

		case "ELIMINARDETALLE":
			$ls_numtra=     $_POST["txtnumtra"];
			$ld_fecemi=     $_POST["txtfecemi"];
			$ls_codalmori=  $_POST["txtcodalm"];
			$ls_codalmdes=  $_POST["txtcodalmdes"];
			$ls_nomalmori=  $_POST["txtnomfisalm"];
			$ls_nomalmdes=  $_POST["txtnomfisdes"];
			$ls_obstra=     $_POST["txtobstra"];
			
			$li_totrows=$li_totrows-1;
			$li_rowdelete=$_POST["filadelete"];
			$li_temp=0;
			$li_totentsum=0;
			for($li_i=1;$li_i<=$li_totrows;$li_i++)
			{
				if($li_i!=$li_rowdelete)
				{		
					$li_temp=$li_temp+1;			
					$ls_codart=     $_POST["txtcodart".$li_i];
					$ls_denart=     $_POST["txtdenart".$li_i];
					$ls_denunimed=  $_POST["txtdenunimed".$li_i];
					$ls_unidad=     $_POST["txtcoduni".$li_i];
					$li_unidad=     $_POST["hidunidad".$li_i];
					$li_cantidad=   $_POST["txtcantidad".$li_i];
					$li_existencia=$_POST["hidexistencia".$li_i];
					$li_cosuni=     $_POST["txtcosuni".$li_i];
					$li_costot=     $_POST["txtcostot".$li_i];
					$li_existencia=     $_POST["hidexistencia".$li_i];
					$li_totentsum=$li_totentsum+$li_costot;
					$lo_object[$li_i][1]="<input name=txtdenart".$li_temp."   type=text id=txtdenart".$li_temp."   class=sin-borde size=15 maxlength=50 value='".$ls_denart."' readonly><input name=txtcodart".$li_temp." type=hidden id=txtcodart".$li_temp." class=sin-borde size=21 maxlength=20 value='".$ls_codart."' onKeyUp='javascript: ue_validarcomillas(this);' readonly><a href='javascript: ue_catarticulo(".$li_temp.");'><img src='../shared/imagebank/tools15/buscar.gif' alt='Codigo de Articulo' width='18' height='18' border='0'></a>";
					$lo_object[$li_i][2]="<input name=txtdenunimed".$li_temp."   type=text id=txtdenunimed".$li_temp."   class=sin-borde size=14 maxlength=12 value='".$ls_denunimed."' readonly>";
					$lo_object[$li_i][3]="<input name=txtcoduni".$li_temp."   type=text id=txtcoduni".$li_temp."   class=sin-borde size=14 maxlength=12 value='".$ls_unidad."' onKeyUp='javascript: ue_validarcomillas(this);' readonly><input name='hidunidad".$li_temp."' type='hidden' id='hidunidad".$li_temp."' value='". $li_unidad ."'>";
					$lo_object[$li_i][4]="<input name=txtcantidad".$li_temp." type=text id=txtcantidad".$li_temp." class=sin-borde size=14 maxlength=12 value='".$li_cantidad."' onKeyUp='javascript: ue_validarnumero(this);' readonly>".
								  		 "<input name=hidexistencia".$li_temp." type=hidden id=hidexistencia".$li_temp."  value='". $li_existencia ."'>";
					$lo_object[$li_i][5]="<input name=txtcosuni".$li_temp."   type=text id=txtcosuni".$li_temp."   class=sin-borde size=14 maxlength=15 value='".$li_cosuni."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";
					$lo_object[$li_i][6]="<input name=txtcostot".$li_temp."   type=text id=txtcostot".$li_temp."   class=sin-borde size=14 maxlength=15 value='".$li_costot."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";
					$lo_object[$li_i][7]="";
					$lo_object[$li_i][8]="<a href=javascript:uf_delete_dt(".$li_temp.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";			
				}
				else
				{
					$li_rowdelete= 0;
				}					
			}
			$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
			$lo_objectent = uf_agregarlineablanca_entrada($lo_objectent,1);
			$li_totentsum=number_format($li_totentsum,2,',','.');
			$li_totrowsc=0;
			break;
	
		case "BUSCARDETALLE":
			$ls_numtra=     $_POST["txtnumtra"];
			$ld_fecemi=     $_POST["txtfecemi"];
			$ls_codalmori=  $_POST["txtcodalm"];
			$ls_codalmdes=  $_POST["txtcodalmdes"];
			$ls_nomalmori=  $_POST["txtnomfisalm"];
			$ls_nomalmdes=  $_POST["txtnomfisdes"];
			$ls_obstra=     $_POST["txtobstra"];
			$ld_fecemiaux=$io_func->uf_convertirdatetobd($ld_fecemi);
			$arrResultado=$io_siv->uf_siv_obtener_dt_produccion($ls_codemp,$ls_numtra,$ld_fecemiaux,$li_totrows,$lo_object,$li_totentsum);
			$li_totrows = $arrResultado['ai_totrows'];
			$lo_object = $arrResultado['ao_object'];
			$li_totentsum = $arrResultado['ai_totentsum'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido)
			{
				$arrResultado=$io_siv->uf_siv_obtener_dt_produccion2($ls_codemp,$ls_numtra,$ld_fecemiaux,$li_totrowsent,$lo_objectent);
				$li_totrowsent = $arrResultado['ai_totrows'];
				$lo_objectent = $arrResultado['ao_object'];
				$lb_valido = $arrResultado['lb_valido'];
			}
			if($lb_valido)
			{
				$arrResultado=$io_siv->uf_siv_obtener_dt_contable($ls_codemp,$ls_numtra,$ld_fecemiaux,$li_totrowsc,$lo_objectc);
				$li_totrowsc = $arrResultado['ai_totrows'];
				$lo_objectc = $arrResultado['ao_object'];
				$lb_valido = $arrResultado['lb_valido'];
			}
			$li_totentsum=number_format($li_totentsum,2,',','.');
			break;
		case "ELIMINAR":
			$ls_numtra=     $_POST["txtnumtra"];
			$ld_fecemi=     $_POST["txtfecemi"];
			$ls_codalmori=  $_POST["txtcodalm"];
			$ls_codalmdes=  $_POST["txtcodalmdes"];
			$ls_nomalmori=  $_POST["txtnomfisalm"];
			$ls_nomalmdes=  $_POST["txtnomfisdes"];
			$ls_obstra=     $_POST["txtobstra"];
			$lb_valido=$io_fec->uf_valida_fecha_mes($ls_codemp,$ld_fecemi);
			if($lb_valido)
			{ 
				$ld_fecemi=$io_func->uf_convertirdatetobd($ld_fecemi);
				if ($ls_status!="C")
				{
					$io_msg->message("El proceso de Produccion no puede ser modificado");
					$li_totrows=1;
					$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
					uf_limpiarvariables();
				}
				else
				{ 
					$lb_encontrado=$io_siv->uf_siv_select_produccion($ls_codemp,$ls_numtra,$ld_fecemi);					
					if ($lb_encontrado)
					{
						$io_sql->begin_transaction();
						$ls_nummov="";					
						$lb_valido1=false;
						$lb_existencia=false;
						if ($lb_valido)
						{
							$ls_nummov=0;
							$ls_nomsol="Produccion";
							$arrResultado=$io_mov->uf_siv_insert_movimiento($ls_nummov,$ld_fecemi,$ls_nomsol,$ls_codusu,$la_seguridad);
							$ls_nummov=$arrResultado['as_nummov'];
							$lb_valido=$arrResultado['lb_valido'];
							if ($lb_valido)
							{ 
								for($li_i=1;$li_i<=$li_totrows;$li_i++)
								{
								
									$li_unidad=   $_POST["hidunidad".$li_i];
									$ls_unidad=   $_POST["txtcoduni".$li_i];
									$ls_denunimed=   $_POST["txtdenunimed".$li_i];
									$ls_codart=   $_POST["txtcodart".$li_i];
									$ls_denart=   $_POST["txtdenart".$li_i];
									$li_cantidad= $_POST["txtcantidad".$li_i];
									$li_cosuni=   $_POST["txtcosuni".$li_i];
									$li_costot=   $_POST["txtcostot".$li_i];
									$li_existencia=$_POST["hidexistencia".$li_i];
									$li_cantidad=  str_replace(".","",$li_cantidad);
									$li_cantidad=  str_replace(",",".",$li_cantidad);
									$li_cosuni=    str_replace(".","",$li_cosuni);
									$li_cosuni=    str_replace(",",".",$li_cosuni);
									$li_costot=    str_replace(".","",$li_costot);
									$li_costot=    str_replace(",",".",$li_costot);

									switch ($ls_unidad) 
									{
										case "Mayor":
											$ls_coduni="M";
											$li_cantidad=($li_cantidad * $li_unidad);
											break;
										case "Detal":
											$ls_coduni="D";
											break;
									}
	
									if ($lb_valido)
									{
										$lb_valido=$io_art->uf_siv_aumentar_articuloxalmacen($ls_codemp,$ls_codart,$ls_codalmori,$li_cantidad,$la_seguridad);
										if ($lb_valido)
										{
											if($lb_valido)
											{
											$ls_opeinv="ENT";
											$ls_codprodoc="ALM";
											$ls_promov="PRO";
											$lb_valido=$io_mov->uf_siv_insert_dt_movimiento($ls_codemp,$ls_nummov,$ld_fecemi,
																							$ls_codart,$ls_codalmori,$ls_opeinv,
																							$ls_codprodoc,$ls_numtra,$li_cantidad,
																							$li_cosuni,$ls_promov,$ls_numtra,
																							$li_cantidad,$ld_fecemi,$la_seguridad);
											}
										}
									}
								}// for
								if($lb_valido)//ENTRADA
								{
									$li_unidadent=   $_POST["hidunidadent1"];
									$ls_unidadent=   $_POST["txtcodunient1"];
									$ls_denunimedent=   $_POST["txtdenunimedent1"];
									$ls_codartent=   $_POST["txtcodartent1"];
									$ls_denartent=   $_POST["txtdenartent1"];
									$li_cantidadent= $_POST["txtcantidadent1"];
									$li_cosunient=   $_POST["txtcosunient1"];
									$li_costotent=   $_POST["txtcostotent1"];
									$li_existenciaent=$_POST["hidexistenciaent1".$li_i];
									$li_cantidadent=  str_replace(".","",$li_cantidadent);
									$li_cantidadent=  str_replace(",",".",$li_cantidadent);
									$li_cosunient=    str_replace(".","",$li_cosunient);
									$li_cosunient=    str_replace(",",".",$li_cosunient);
									$li_costotent=    str_replace(".","",$li_costotent);
									$li_costotent=    str_replace(",",".",$li_costotent);

									switch ($ls_unidad) 
									{
										case "Mayor":
											$ls_coduni="M";
											$li_cantidad=($li_cantidad * $li_unidad);
											break;
										case "Detal":
											$ls_coduni="D";
											break;
									}
									if(($lb_valido)&&($ls_codartent!=""))
									{
										$lb_valido=$io_art->uf_siv_disminuir_articuloxalmacen($ls_codemp,$ls_codartent,$ls_codalmdes,$li_cantidadent,$la_seguridad);
										if($lb_valido)
										{
											$ls_opeinv="SAL";
											$ls_promov="PRO";
											$ls_codprodoc="ALM";
											$li_candesart="0.00";
											$lb_valido=$io_mov->uf_siv_insert_dt_movimiento($ls_codemp,$ls_nummov,$ld_fecemi,
																							$ls_codartent,$ls_codalmdes,$ls_opeinv,
																							$ls_codprodoc,$ls_numtra,$li_cantidadent,
																							$li_cosunient,$ls_promov,$ls_numtra,
																							$li_cantidadent,$ld_fecemi,$la_seguridad);
										}
									}
									
								}//ENTRADA
							}
						}
						$ld_fecemi=$io_func->uf_convertirfecmostrar($ld_fecemi);
						if($lb_valido)
						{
							$lb_valido=$io_siv->uf_siv_delete_produccion($ls_codemp,$ls_numtra,$la_seguridad);
						}
						if($lb_valido)
						{
							$io_sql->commit();
							$io_msg->message("El proceso de Produccion se elimino satisfactoriamente");
							$li_totrows=1;
							$lo_objectent = uf_agregarlineablanca_entrada($lo_objectent,1);
							$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
							uf_limpiarvariables();
						}
						else
						{
							$io_sql->commit();
							$io_msg->message("Se produjo un error al eliminar");
							$li_totrows=1;
							$lo_objectent = uf_agregarlineablanca_entrada($lo_objectent,1);
							$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
							uf_limpiarvariables();
						}
					}
				}
			}
			else
			{
				$io_msg->message("El mes no esta abierto");
				$li_totrows=1;
				$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
				uf_limpiarvariables();
				$li_totrowsc=0;
			}
			break;
		case "ELIMINAR1":
			$ls_numtra=     $_POST["txtnumtra"];
			$ld_fecemi=     $_POST["txtfecemi"];
			$ls_codalmori=  $_POST["txtcodalm"];
			$ls_codalmdes=  $_POST["txtcodalmdes"];
			$ls_nomalmori=  $_POST["txtnomfisalm"];
			$ls_nomalmdes=  $_POST["txtnomfisdes"];
			$ls_obstra=     $_POST["txtobstra"];
			$ld_fecemiaux=$io_func->uf_convertirdatetobd($ld_fecemi);
			$io_sql->begin_transaction();
			$lb_valido=$io_siv->uf_siv_delete_movimiento($ls_codemp,$ls_numtra,$ld_fecemiaux,$la_seguridad);
			if($lb_valido)
			{
				$lb_valido=$io_siv->uf_siv_delete_produccion($ls_codemp,$ls_numtra,$la_seguridad);
			}
			if($lb_valido)
			{
				$io_sql->commit();
				$io_msg->message("El proceso de Produccion se elimino satisfactoriamente");
				$li_totrows=1;
				$lo_objectent =  uf_agregarlineablanca_entrada($lo_objectent,1);
				$lo_object =  uf_agregarlineablanca($lo_object,$li_totrows);
				uf_limpiarvariables();
			}
			else
			{
				$io_sql->commit();
				$io_msg->message("Se produjo un error al eliminar");
				$li_totrows=1;
				$lo_objectent = uf_agregarlineablanca_entrada($lo_objectent,1);
				$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
				uf_limpiarvariables();
			}
			break;
		case "CALCULARCONTABLE":
			$ls_numtra=     $_POST["txtnumtra"];
			$ld_fecemi=     $_POST["txtfecemi"];
			$ls_codalmori=  $_POST["txtcodalm"];
			$ls_codalmdes=  $_POST["txtcodalmdes"];
			$ls_nomalmori=  $_POST["txtnomfisalm"];
			$ls_nomalmdes=  $_POST["txtnomfisdes"];
			$ls_obstra=     $_POST["txtobstra"];
			$li_totentsum= $io_fun_inventario->uf_obtenervalor("txttotentsum","");
			
			//Detalle de Entrada
			$li_unidadent=   $_POST["hidunidadent1"];
			$ls_unidadent=   $_POST["cmbunidadent1"];
			$ls_denunimedent=   $_POST["txtdenunimedent1"];
			$ls_codartent=   $_POST["txtcodartent1"];
			$ls_denartent=   $_POST["txtdenartent1"];
			$li_cantidadent= $_POST["txtcantidadent1"];
			$li_cosunient=   $_POST["txtcosunient1"];
			$li_costotent=   $_POST["txtcostotent1"];
			$li_existenciaent=$_POST["hidexistenciaent1".$li_i];
			$li_cantidadent=  str_replace(".","",$li_cantidadent);
			$li_cantidadent=  str_replace(",",".",$li_cantidadent);
			$li_cosunient=    str_replace(".","",$li_cosunient);
			$li_cosunient=    str_replace(",",".",$li_cosunient);
			$li_costotent=    str_replace(".","",$li_costotent);
			$li_costotent=    str_replace(",",".",$li_costotent);
			switch ($ls_unidadent) 
			{
				case "M":
					$ls_unidadent="Mayor";
					break;
				case "D":
					$ls_unidadent="Detal";
					break;
			}
			$li_i=1;
			if($li_numdecper!="3")
			{
				$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
				$li_cantidadent=number_format($li_cantidadent,2,",",".");
			}
			else
			{
				$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
				$li_cantidadent=number_format($li_cantidadent,3,",",".");
			}
			$lo_objectent[1][1]="<input name=txtdenartent".$li_i."   type=text   id=txtdenartent".$li_i."   class=sin-borde size=20 maxlength=50 value='".$ls_denartent."' readonly>".
								 "<input name=txtcodartent".$li_i."   type=hidden id=txtcodartent".$li_i."   class=sin-borde size=21 maxlength=20 value='".$ls_codartent."' onKeyUp='javascript: ue_validarcomillas(this);' readonly>";
			$lo_objectent[1][2]="<input name=txtdenunimedent".$li_i." type=text  id=txtdenunimedent".$li_i." class=sin-borde size=14 maxlength=12 value='".$ls_denunimedent."'readonly>";
			$lo_objectent[1][3]="<input name=txtcodunient".$li_i."   type=text   id=txtcodunient".$li_i."   class=sin-borde size=14 maxlength=12 value='".$ls_unidadent."' onKeyUp='javascript: ue_validarcomillas(this);' readonly>".
								 "<input name='hidunidadent".$li_i."' type=hidden id=hidunidadent".$li_i."   value='". $li_unidadent ."'>";
			$lo_objectent[1][4]="<input name=txtcantidadent".$li_i." type=text   id=txtcantidadent".$li_i." class=sin-borde size=14 maxlength=12 value='".$li_cantidadent."' readonly>".
								 "<input name=hidexistenciaent".$li_i." type=hidden id=hidexistenciaent".$li_i."  value='". $li_existenciaent ."'>";
			$lo_objectent[1][5]="<input name=txtcosunient".$li_i."   type=text   id=txtcosunient".$li_i."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_cosunient,2,",",".")."' readonly>";
			$lo_objectent[1][6]="<input name=txtcostotent".$li_i."   type=text   id=txtcostotent".$li_i."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_costotent,2,",",".")."' readonly>";
			$ls_sccuenta=$io_siv->uf_siv_buscar_cuenta($ls_codartent);
			if($ls_sccuenta=="")
			{
				$io_msg->message("Falta la cuenta contable para el articulo ".$ls_denartent);
				$li_totrowsc=0;
			}
			else
			{
				$li_totrowsc=1;
				$lo_objectc[1][1]="<input  name=txtdenartc".$li_totrowsc."  type=text   id=txtdenartc".$li_totrowsc."  class=sin-borde size=40  value='".$ls_denartent."'   readonly style='text-align:left'>".
								  "<input  name=txtcodartc".$li_totrowsc."  type=hidden id=txtcodartc".$li_totrowsc."  class=sin-borde size=30  value='".$ls_codartent."'   readonly>";
				$lo_objectc[1][2]="<input  name=txtsccuenta".$li_totrowsc." type=text   id=txtsccuenta".$li_totrowsc." class=sin-borde size=20  value='".$ls_sccuenta."' readonly style='text-align:center'>";
				$lo_objectc[1][3]="<input  name=txtdebhab".$li_totrowsc."   type=text   id=txtdebhab".$li_totrowsc."   class=sin-borde size=10  value='DEBE'   readonly style='text-align:center'>";
				$lo_objectc[1][4]="<input  name=txtmonto".$li_totrowsc."    type=text   id=txtcansolc".$li_totrowsc."  class=sin-borde size=20  value='".number_format ($li_costotent,2,",",".")."' style='text-align:right' readonly>";
			}
			for($li_i=1;$li_i<$li_totrows;$li_i++)
			{
			
				$li_unidad=   $_POST["hidunidad".$li_i];
				$ls_unidad=   $_POST["txtcoduni".$li_i];
				$ls_denunimed=   $_POST["txtdenunimed".$li_i];
				$ls_codart=   $_POST["txtcodart".$li_i];
				$ls_denart=   $_POST["txtdenart".$li_i];
				$li_cantidad= $_POST["txtcantidad".$li_i];
				$li_cosuni=   $_POST["txtcosuni".$li_i];
				$li_costot=   $_POST["txtcostot".$li_i];
				$li_existencia=$_POST["hidexistencia".$li_i];
				$li_cantidad=  str_replace(".","",$li_cantidad);
				$li_cantidad=  str_replace(",",".",$li_cantidad);
				$li_cosuni=    str_replace(".","",$li_cosuni);
				$li_cosuni=    str_replace(",",".",$li_cosuni);
				$li_costot=    str_replace(".","",$li_costot);
				$li_costot=    str_replace(",",".",$li_costot);

				if($li_numdecper!="3")
				{
					$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
					$li_cantidad=number_format($li_cantidad,2,",",".");
				}
				else
				{
					$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
					$li_cantidad=number_format($li_cantidad,3,",",".");
				}
				$lo_object[$li_i][1]="<input name=txtdenart".$li_i."   type=text   id=txtdenart".$li_i."   class=sin-borde size=20 maxlength=50 value='".$ls_denart."' readonly>".
									 "<input name=txtcodart".$li_i."   type=hidden id=txtcodart".$li_i."   class=sin-borde size=21 maxlength=20 value='".$ls_codart."' onKeyUp='javascript: ue_validarcomillas(this);' readonly>";
				$lo_object[$li_i][2]="<input name=txtdenunimed".$li_i." type=text  id=txtdenunimed".$li_i." class=sin-borde size=14 maxlength=12 value='".$ls_denunimed."'readonly>";
				$lo_object[$li_i][3]="<input name=txtcoduni".$li_i."   type=text   id=txtcoduni".$li_i."   class=sin-borde size=14 maxlength=12 value='".$ls_unidad."' onKeyUp='javascript: ue_validarcomillas(this);' readonly>".
									 "<input name='hidunidad".$li_i."' type=hidden id=hidunidad".$li_i."   value='". $li_unidad ."'>";
				$lo_object[$li_i][4]="<input name=txtcantidad".$li_i." type=text   id=txtcantidad".$li_i." class=sin-borde size=14 maxlength=12 value='".$li_cantidad."'  readonly>".
									 "<input name=hidexistencia".$li_i." type=hidden id=hidexistencia".$li_i."  value='". $li_existencia ."'>";
				$lo_object[$li_i][5]="<input name=txtcosuni".$li_i."   type=text   id=txtcosuni".$li_i."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_cosuni,2,",",".")."' readonly>";
				$lo_object[$li_i][6]="<input name=txtcostot".$li_i."   type=text   id=txtcostot".$li_i."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_costot,2,",",".")."' readonly>";
				$lo_object[$li_i][7]="";
				$lo_object[$li_i][8]="<a href=javascript:uf_delete_dt(".$li_i.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";			
				$ls_sccuenta=$io_siv->uf_siv_buscar_cuenta($ls_codart);
				if($ls_sccuenta=="")
				{
					$io_msg->message("Falta la cuenta contable para el articulo ".$ls_denart);
					$li_totrowsc=0;
				}
				else
				{
					$li_totrowsc=$li_totrowsc+1;
					$lo_objectc[$li_totrowsc][1]="<input  name=txtdenartc".$li_totrowsc."  type=text   id=txtdenartc".$li_totrowsc."  class=sin-borde size=40  value='".$ls_denart."'   readonly style='text-align:left'>".
									  "<input  name=txtcodartc".$li_totrowsc."  type=hidden id=txtcodartc".$li_totrowsc."  class=sin-borde size=30  value='".$ls_codart."'   readonly>";
					$lo_objectc[$li_totrowsc][2]="<input  name=txtsccuenta".$li_totrowsc." type=text   id=txtsccuenta".$li_totrowsc." class=sin-borde size=20  value='".$ls_sccuenta."' readonly style='text-align:center'>";
					$lo_objectc[$li_totrowsc][3]="<input  name=txtdebhab".$li_totrowsc."   type=text   id=txtdebhab".$li_totrowsc."   class=sin-borde size=10  value='HABER'   readonly style='text-align:center'>";
					$lo_objectc[$li_totrowsc][4]="<input  name=txtmonto".$li_totrowsc."    type=text   id=txtcansolc".$li_totrowsc."  class=sin-borde size=20  value='".number_format ($li_costot,2,",",".")."' style='text-align:right' readonly>";
				}
			}
			$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
			break;
	}
?>
<p>&nbsp;</p>
<div align="center">
          <form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_inventario->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_inventario);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>	
  <table width="635" height="286" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="755" height="19"><div align="left">
<input name="operacion" type="hidden" id="operacion">
                    <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
                    <input name="filadelete" type="hidden" id="filadelete">
                    <input name="catafilas" type="hidden" id="catafilas" value="<?php print $li_catafilas;?>">
          			<input name="totalfilasc" type="hidden" id="totalfilasc" value="<?php print $li_totrowsc;?>">
      </div></td>
    </tr>
    <tr>
      <td height="251"><table width="596" height="274" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr>
          <td height="17" colspan="4" class="titulo-ventana">Conversion de Productos</td>
        </tr>
        <tr class="formato-blanco">
          <td width="120" height="20"><div align="right"></div></td>
          <td width="269" height="22"><div align="left">
              <input name="txtnumtra" type="hidden" id="txtnumtra" value="<?php print $ls_numtra?>" size="15" maxlength="15" readonly>
              <input name="hidestatus" type="hidden" id="hidestatus" value="<?php print $ls_status?>">
              <input name="hidreadonly" type="hidden" id="hidreadonly">
              <input name="txtcodusu" type="hidden" id="txtcodusu" onKeyPress="javascript: ue_validarcomillas(this);" value="<?php print $ls_codusu?>" size="20" maxlength="60" readonly>
              <input name="txtcodcencos" type="hidden" id="txtcodcencos">
              <input name="txtdenominacion" type="hidden" id="txtdenominacion">
          </div></td>
          <td width="70" align="right">Fecha</td>
          <td width="127"><input name="txtfecemi" type="text" id="txtfecemi" style="text-align:center " onKeyPress="ue_separadores(this,'/',patron,true);" value="<?php print $ld_fecemi?>" size="17" maxlength="10" datepicker="true"></td>
        </tr>
        <tr class="formato-blanco">
          <td height="20"><div align="right">Almac&eacute;n Salida </div></td>
          <td height="22" colspan="3"><div align="left">
              <input name="txtcodalm" type="text" id="txtcodalm" value="<?php print $ls_codalmori?>" size="15" style="text-align:center " readonly>
              <a href="javascript: ue_buscarorigen();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
              <input name="txtnomfisalm" type="text" class="sin-borde" id="txtnomfisalm3" value="<?php print $ls_nomalmori?>" size="60" readonly>
          </div></td>
        </tr>
        <tr class="formato-blanco">
          <td height="20"><div align="right">Almac&eacute;n Entrada </div></td>
          <td height="22" colspan="3"><div align="left">
              <input name="txtcodalmdes" type="text" id="txtcodalmdes" value="<?php print $ls_codalmdes?>" size="15" style="text-align:center " readonly>
              <a href="javascript: ue_buscardestino();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
              <input name="txtnomfisdes" type="text" class="sin-borde" id="txtnomfisdes3" value="<?php print $ls_nomalmdes?>" size="60" readonly>
          </div></td>
        </tr>
        <tr class="formato-blanco">
          <td height="16"><div align="right">Observaciones</div></td>
          <td colspan="3" rowspan="2"><div align="left">
            <textarea name="txtobstra" cols="78" rows="3" id="txtobstra" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn?opqrstuvwxyz?????., ()@#!%/[]*-+_');"><?php print $ls_obstra?></textarea>
          </div></td>
        </tr>
        <tr class="formato-blanco">
          <td height="20">&nbsp;</td>
        </tr>
        <tr class="formato-blanco">
          <td height="13">&nbsp;</td>
          <td colspan="3">
            <input name="txtdesalm" type="hidden" id="txtdesalm4">
            <input name="txttelalm" type="hidden" id="txttelalm4">
            <input name="txtubialm" type="hidden" id="txtubialm4">
            <input name="txtnomresalm" type="hidden" id="txtnomresalm3">
            <input name="txttelresalm" type="hidden" id="txttelresalm4">
            <input name="hidstatus" type="hidden" id="hidstatus4">
            <input name="txtdenunimed" type="hidden" id="txtdenunimed">
            <input name="txtunidad" type="hidden" id="txtunidad">
            <input name="txtestint" type="hidden" id="txtestint"  value="<?php print $ls_estint; ?>">
            <input name="txtobsunimed" type="hidden" id="txtobsunimed"></td>
        </tr>
        <tr class="formato-blanco">
          <td height="20" colspan="4">
		 	 </td>
       </tr>
        <tr class="formato-blanco">
          <td height="20" colspan="4"><p align="center">
              <?php
					$in_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
					?>
          </p>              </td>
        </tr>
        <tr class="formato-blanco">
          <td height="22">&nbsp;</td>
          <td height="22">&nbsp;</td>
          <td height="22"><div align="right">Total</div></td>
          <td height="22"><div align="left">
            <input name="txttotentsum" type="text" id="txttotentsum" value="<?php print $li_totentsum; ?>" size="17" style="text-align:right" readonly>
          </div></td>
        </tr>
        <tr class="formato-blanco">
          <td height="20" colspan="4">
		                
                <div align="center">
                  <?php
					$in_grid->makegrid(1,$lo_titleent,$lo_objectent,$li_widthtable,$ls_titletableent,$ls_nametable);
					?>
                </div></td>
        </tr>
        <tr class="formato-blanco">
          <td height="20" colspan="4"><div align="center">
            <input name="btngenerar" type="button" class="boton" id="btngenerar" value="Generar Detalle Contables" onClick="javascript: ue_contable();" <?php print $ls_disable ?>>
          </div></td>
        </tr>
        <tr class="formato-blanco">
          <td height="20" colspan="4"><div align="center">
            <?php
					$in_grid->makegrid($li_totrowsc,$lo_titlecontable,$lo_objectc,$li_widthcontable,$ls_titlecontable,$ls_namecontable);
				?>
          </div></td>
        </tr>
        <tr class="formato-blanco">
          <td height="20" colspan="4">&nbsp;</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="13">&nbsp;</td>
    </tr>
  </table>
          </form>
</div>
<p align="center">&nbsp;</p>
</body>
<script >
//Funciones de operaciones 
function ue_agregar_bienes()
{
	f=document.form1;
	totalfilas=f.totalfilas.value;
	ls_codalm=f.txtcodalm.value;
	if(ls_codalm!="")
	{
		window.open("sigesp_siv_pdt_transferencia.php?linea="+totalfilas+"&almacen="+ls_codalm+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("Debe indicar el almac?n origen de la transferencia");
	}
}

function ue_catarticulo(li_linea)
{
	f=document.form1;
	estint=f.txtestint.value;
	if(estint!="1")
	{
		ls_codalm=f.txtcodalm.value;
		window.open("sigesp_catdinamic_articulot.php?linea="+li_linea+"&almacen="+ls_codalm+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("El proceso esta contabilizado y no puede ser modificado");
	}
}

function ue_catarticuloent(li_linea)
{
	f=document.form1;
	li_fila=f.totalfilas.value;
	estint=f.txtestint.value;
	if(estint!="1")
	{
		if(li_fila>1)
		{
			tipo="produccion";
			window.open("sigesp_catdinamic_articulom.php?linea="+li_linea+"&tipo="+tipo+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
		}
		else
		{
			alert("Debe existir al menos 1 articulo saliente");
		}
	}
	else
	{
		alert("El proceso esta contabilizado y no puede ser modificado");
	}
}

function ue_catunidad(li_linea)
{
	window.open("sigesp_catdinamic_unidadmedida.php?linea="+li_linea+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_buscarorigen()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	tipo="origen";
	if(li_incluir==1)
	{	
		window.open("sigesp_catdinamic_almacen.php?tipo="+tipo+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function ue_buscardestino()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	tipo="destino";
	if(li_incluir==1)
	{	
		window.open("sigesp_catdinamic_almacen.php?tipo="+tipo+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if(li_leer==1)
	{
		window.open("sigesp_catdinamic_produccion.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.action="sigesp_siv_p_produccion.php";
		f.submit();
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}
function uf_agregar_dt(li_row)
{
	f=document.form1;
	ls_codnewart=  eval("f.txtcodart"+li_row+".value");
	ls_codnewcan=  eval("f.txtcantidad"+li_row+".value");
	ls_codnewuni=  eval("f.txtcosuni"+li_row+".value");
	ls_codnewuni=  eval("f.cmbunidad"+li_row+".value");
	li_total=f.totalfilas.value;
	lb_valido=false;
	li_unidad=eval("f.hidunidad"+li_row+".value");
	li_unidad=ue_validarvacio(li_unidad);
	li_existencia=eval("f.hidexistencia"+li_row+".value");
	li_existencia=ue_validarvacio(li_existencia);
	li_existencia=   ue_formato_operaciones(li_existencia);
	if (ls_codnewuni=="M")
	{
		ls_codnewuni="Mayor";
	}
	else
	{
		ls_codnewuni="Detal";
	}
	
	for(li_i=1;li_i<li_total&&lb_valido!=true;li_i++)
	{
		ls_codart=   eval("f.txtcodart"+li_i+".value");
		ls_unidad=   eval("f.txtcoduni"+li_i+".value");
		ls_cantidad= eval("f.txtcantidad"+li_i+".value");
		ls_cosuni=   eval("f.txtcosuni"+li_i+".value");
		if((ls_codart==ls_codnewart)&&(ls_unidad==ls_codnewuni)&&(li_row!=li_i))
		{
			alert("El movimiento ya esta registrado");
			lb_valido=true;
		}
	}
	ls_codart=eval("f.txtcodart"+li_row+".value");
	ls_codart=ue_validarvacio(ls_codart);
	ls_cantidad=eval("f.txtcantidad"+li_row+".value");
	ls_cantidad=ue_validarvacio(ls_cantidad);
	ls_cosuni=eval("f.txtcosuni"+li_row+".value");
	ls_cosuni=ue_validarvacio(ls_cosuni);

	if((ls_codart=="")||(ls_cantidad=="")||(ls_cosuni==""))
	{
		alert("Debe llenar todos los campos");
		lb_valido=true;
	}
	li_canartaux=parseFloat(ls_cantidad);
	if(ls_codnewuni=="Mayor")
	{
		li_canartaux=parseFloat(ls_cantidad) * parseFloat(li_unidad);
	}
	if(parseFloat(li_existencia)<parseFloat(li_canartaux))
	{
		alert("No hay suficientes, el maximo es de "+li_existencia+" articulos al detal");
		lb_valido=true;
	}

	ls_fecemi=eval("f.txtfecemi.value");
	ls_fecemi=ue_validarvacio(ls_fecemi);
	ls_almori=eval("f.txtcodalm.value");
	ls_almori=ue_validarvacio(ls_almori);
	ls_almdes=eval("f.txtcodalmdes.value");
	ls_almdes=ue_validarvacio(ls_almdes);
	
	if((ls_fecemi=="")||(ls_almori=="")||(ls_almdes==""))
	{
		alert("Debe llenar los campos principales");
		lb_valido=true;
	}
	if(!lb_valido)
	{
		f.operacion.value="AGREGARDETALLE";
		f.action="sigesp_siv_p_produccion.php";
		f.submit();
	}
}

function uf_delete_dt(li_row)
{
	f=document.form1;
	li_fila=f.totalfilas.value;
	estint=f.txtestint.value;
	if(estint!="1")
	{
		if(li_fila!=li_row)
		{
			if(confirm("?Desea eliminar el Registro actual?"))
			{	
				f.filadelete.value=li_row;
				f.operacion.value="ELIMINARDETALLE"
				f.action="sigesp_siv_p_produccion.php";
				f.submit();
			}
		}
	}
	else
	{
		alert("El proceso esta contabilizado y no puede ser modificado");
	}
}

function ue_guardar()
{
	f=document.form1;
	li_fila=f.totalfilas.value;
	ls_fecemi=eval("f.txtfecemi.value");
	ls_fecemi=ue_validarvacio(ls_fecemi);
	ls_almori=eval("f.txtcodalm.value");
	ls_almori=ue_validarvacio(ls_almori);
	ls_almdes=eval("f.txtcodalmdes.value");
	ls_almdes=ue_validarvacio(ls_almdes);
	ls_estatus=f.hidestatus.value;
	ls_codadrent=eval("f.txtcodartent1.value");
	
	estint=f.txtestint.value;
	if(estint!="1")
	{
		if(ls_estatus=="")
		{
			if(li_fila<1)
			{
				alert("El proceso de Produccion debe tener al menos 1 art?culo");
			}
			else
			{
				if(ls_codadrent!="")
				{
					if ((ls_fecemi=="")||(ls_almori=="")||(ls_almdes==""))
					{
						alert("Debe llenar los campos principales");
					}
					else
					{
						f.operacion.value="GUARDAR";
						f.action="sigesp_siv_p_produccion.php";
						f.submit();
					}
				}
				else
				{
					alert("El proceso de Produccion debe tener un articulo a generar.");
				}
			}
		}
		else
		{
			alert("El proceso de Produccion no puede ser modificado.");
		}
	}
	else
	{
		alert("El proceso esta contabilizado y no puede ser modificado");
	}
}
function ue_contable()
{
//--------------------------------------------------------
// Funcion que genera los asientos contables del despacho
//--------------------------------------------------------

	f=document.form1;
	li_fila=f.totalfilas.value;
	ls_fecemi=eval("f.txtfecemi.value");
	ls_fecemi=ue_validarvacio(ls_fecemi);
	ls_almori=eval("f.txtcodalm.value");
	ls_almori=ue_validarvacio(ls_almori);
	ls_almdes=eval("f.txtcodalmdes.value");
	ls_almdes=ue_validarvacio(ls_almdes);
	ls_estatus=f.hidestatus.value;
	ls_codadrent=eval("f.txtcodartent1.value");
	valido=true;
	if(li_fila<=1)
	{
		alert("El proceso de Produccion debe tener al menos 1 art?culo");
	}
	else
	{
		f.operacion.value="CALCULARCONTABLE";
		f.action="sigesp_siv_p_produccion.php";
		f.submit();
	}		
		
		for(li_i=1;li_i<li_totfilas;li_i++)
		{
			ls_denart=    eval("f.txtdenart"+li_i+".value");
			ls_denart=ue_validarvacio(ls_denart);
			ls_codart=    eval("f.txtcodart"+li_i+".value");
			ls_codart=ue_validarvacio(ls_codart);
		
			if((ls_codart==""))
			{
				lb_blancos=false;
				li_blancos=li_blancos + 1;
			}
		}
		ls_codartent=    eval("f.txtcodartent1.value");
		if(ls_codartent=="")
		{
			valido=false;
		}
		if((!lb_blancos))
		{
			if(li_blancos!=li_totfilas)
			{lb_blancos=true;}
		}
		if((lb_blancos)&&(valido))
		{
			f.operacion.value="CALCULARCONTABLE";
			f.action="sigesp_siv_p_transferencia_ccostos.php";
			f.submit();
		}
}

function ue_imprimir(ls_reporte)
{
	f=document.form1;
	numtra= f.txtnumtra.value;
	if(numtra!="")
	{
		codalmori= f.txtcodalm.value;
		codalmdes= f.txtcodalmdes.value;
		nomfisori= f.txtnomfisalm.value;
		nomfisdes= f.txtnomfisdes.value;
		obstra=    f.txtobstra.value;
		fecemi=    f.txtfecemi.value;
		window.open("reportes/"+ls_reporte+"?numtra="+numtra+"&fecemi="+fecemi+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
	}
	else
	{
	alert("Debe existir un documento a imprimir");
	}
}


function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

function ue_montosfactura(li_row)
{
//--------------------------------------------------------
//	Funci?n que calcula el monto total por articulo 
//	multiplicando la cantidad de articulos a despachar por el costo
//  unitario de cada uno de ellos.
//--------------------------------------------------------
	f=document.form1;
	ls_unidad=eval("f.cmbunidad"+li_row+".value");
	ls_unidad=ue_validarvacio(ls_unidad);
	li_unidad=eval("f.hidunidad"+li_row+".value");
	li_unidad=ue_validarvacio(li_unidad);
	li_canart=eval("f.txtcantidad"+li_row+".value");
	li_canart=ue_validarvacio(li_canart);
	li_canart=eval("f.txtcantidad"+li_row+".value");
	li_canart=ue_validarvacio(li_canart);
	li_preuniart=eval("f.txtcosuni"+li_row+".value");
	li_preuniart=ue_validarvacio(li_preuniart);
	if((li_canart!="")&&(li_preuniart!=""))
	{
		li_preuniart=ue_formato_operaciones(li_preuniart);
		li_canart=   ue_formato_operaciones(li_canart);
		if(ls_unidad=="M")
		{
			li_canart=parseFloat(li_canart)*parseFloat(li_unidad);
			li_canart=String(li_canart);
		}

		li_montot=parseFloat(li_canart) * parseFloat(li_preuniart);
		li_montot=ue_redondear(li_montot,4);
		li_montot=uf_convertir(li_montot);
		obj=eval("f.txtcostot"+li_row+"");
		obj.value=li_montot;
		ue_calculartotal();

	}
}

function ue_costounitario(li_row)
{
//--------------------------------------------------------
//	Funci?n que calcula el monto total por articulo 
//	multiplicando la cantidad de articulos a despachar por el costo
//  unitario de cada uno de ellos.
//--------------------------------------------------------
	f=document.form1;
	ls_unidad=eval("f.cmbunidadent"+li_row+".value");
	ls_unidad=ue_validarvacio(ls_unidad);
	li_unidad=eval("f.hidunidadent"+li_row+".value");
	li_unidad=ue_validarvacio(li_unidad);
	li_canart=eval("f.txtcantidadent"+li_row+".value");
	li_canart=ue_validarvacio(li_canart);
	li_preunitot=eval("f.txtcostotent"+li_row+".value");
	li_preunitot=ue_validarvacio(li_preunitot);
	if((li_canart!="")&&(li_preunitot!=""))
	{
		li_preunitot=ue_formato_operaciones(li_preunitot);
		li_canart=   ue_formato_operaciones(li_canart);
		if(ls_unidad=="M")
		{
			li_canart=parseFloat(li_canart)*parseFloat(li_unidad);
			li_canart=String(li_canart);
		}

		li_montouni=parseFloat(li_preunitot) / parseFloat(li_canart)  ;
		li_montouni=ue_redondear(li_montouni,4);
		li_montouni=uf_convertir(li_montouni);
		obj=eval("f.txtcosunient"+li_row+"");
		obj.value=li_montouni;

	}
}

//--------------------------------------------------------
//	Funci?n que coloca los separadores (/) de las fechas
//--------------------------------------------------------
var patron = new Array(2,2,4)
var patron2 = new Array(1,3,3,3,3)
function ue_separadores(d,sep,pat,nums)
{
	if(d.valant != d.value)
	{
		val = d.value
		largo = val.length
		val = val.split(sep)
		val2 = ''
		for(r=0;r<val.length;r++){
			val2 += val[r]	
		}
		if(nums){
			for(z=0;z<val2.length;z++){
				if(isNaN(val2.charAt(z))){
					letra = new RegExp(val2.charAt(z),"g")
					val2 = val2.replace(letra,"")
				}
			}
		}
		val = ''
		val3 = new Array()
		for(s=0; s<pat.length; s++){
			val3[s] = val2.substring(0,pat[s])
			val2 = val2.substr(pat[s])
		}
		for(q=0;q<val3.length; q++){
			if(q ==0){
				val = val3[q]
			}
			else{
				if(val3[q] != ""){
					val += sep + val3[q]
					}
			}
		}
	d.value = val
	d.valant = val
	}
}
//--------------------------------------------------------------
//	Funci?n que calcula el total de la recepcion de suministros
//--------------------------------------------------------------
function ue_calculartotal()
{
	f=document.form1;
	li_totalrow=f.totalfilas.value;
	li_total=0;
	for(li_i=1;li_i<=li_totalrow;li_i++)
	{
		li_subtotal=eval("f.txtcostot"+li_i+".value");
		if(li_subtotal!="")
		{
			li_subtotal= ue_formato_operaciones(li_subtotal);
			li_total=li_total + parseFloat(li_subtotal);
		}
	}
	li_total=uf_convertir(li_total);
	f.txttotentsum.value=li_total;
}
function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	estint=f.txtestint.value;
	if(estint!="1")
	{
		if(li_eliminar==1)
		{	
			if(confirm("?Seguro desea eliminar el Registro?"))
			{
				f.operacion.value="ELIMINAR";
				f.action="sigesp_siv_p_produccion.php";
				f.submit();
			}
		}
		else
		{
			alert("No tiene permiso para realizar esta operacion");
		}
	}
	else
	{
		alert("El proceso esta contabilizado y no puede ser modificado");
	}
}

</script> 
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>