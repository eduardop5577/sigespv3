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
$io_fun_activo=new class_funciones_inventario();
$ls_permisos = "";
$la_seguridad = Array();
$la_permisos = Array();
$arrResultado = $io_fun_activo->uf_load_seguridad("SIV","sigesp_siv_d_configuracion.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos = $arrResultado['as_permisos'];
$la_seguridad = $arrResultado['aa_seguridad'];
$la_permisos = $arrResultado['aa_permisos'];
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_selected,$ls_selectedfifo,$ls_selectedlifo,$ls_selectedcpp,$ls_checksig;
		global $ls_checknum,$ls_checkcont,$ls_checkalfnum,$ls_checkcmp,$ls_checkartpri,$ls_checkcencos,$ls_checktraalmpro, $ls_checkestalmmer;
		
		$ls_selected="";
		$ls_selectedfifo="";
		$ls_selectedlifo="";
		$ls_selectedcpp="";
		$ls_checksig="";
		$ls_checknum="";
		$ls_checkcont="";
		$ls_checkalfnum="";
		$ls_checkcmp="checked";
		$ls_checkartpri="";
		$ls_checkcencos="";
		$ls_checktraalmpro="";
		$ls_checkestalmmer="";
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Configuraci&oacute;n de Inventario</title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #EFEBEF;
}

a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}

-->
</style>
<script type="text/javascript"  src="js/stm31.js"></script>
<link href="css/siv.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<!--<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
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
--></head>

<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu">
	<table width="776" border="0" align="center" cellpadding="0" cellspacing="0">
	
		<td width="423" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Inventario </td>
		<td width="353" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  <tr>
		<td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
		<td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
	</table></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
   <tr>
    <td height="13" colspan="11" bgcolor="#E7E7E7" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" width="20" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="24"><div align="center"></div></td>
    <td class="toolbar" width="24"><div align="center"></div></td>
    <td class="toolbar" width="24"><div align="center"></div></td>
    <td class="toolbar" width="618">&nbsp;</td>
  </tr>
</table>
<?php
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=  new sigesp_include();
	$con= $in->uf_conectar();
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_msg= new class_mensajes();
	require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
	$io_fun= new class_funciones_db($con);
	require_once("sigesp_siv_c_configuracion.php");
	$io_siv= new sigesp_siv_c_configuracion();
	require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fec= new class_fecha();
	require_once("class_funciones_inventario.php");
	$io_funciones_inventario= new class_funciones_inventario();
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_operacion=$io_funciones_inventario->uf_obteneroperacion();
	
	switch ($ls_operacion) 
	{
		case "NUEVO":
		break;
		
		case "GUARDAR";
			uf_limpiarvariables();
			$ls_valido= false;
			$li_estcatsig=$io_funciones_inventario->uf_obtenervalor("chksigecof",0);
			$ls_metodo=$io_funciones_inventario->uf_obtenervalor("cmbmetodo","");
			$li_estnum=$io_funciones_inventario->uf_obtenervalor("rdcodigo","");
			$li_estcont=$io_funciones_inventario->uf_obtenervalor("chkcontabilizar",0);
			$li_estcmp=$io_funciones_inventario->uf_obtenervalor("chkestcmp",0);
			$li_artpri=$io_funciones_inventario->uf_obtenervalor("chkartpri",0);
			$li_estcencos=$io_funciones_inventario->uf_obtenervalor("chkestcencos",0);
			$li_traalmpro=$io_funciones_inventario->uf_obtenervalor("chktraalmpro",0);
			$ls_sccuenta=$io_funciones_inventario->uf_obtenervalor("txtsccuenta","");
			$li_estalmmer=$io_funciones_inventario->uf_obtenervalor("chkestalmmer",0);
			$ls_codtipart=$io_funciones_inventario->uf_obtenervalor("txtcodtipart","");
			$li_estdetart=$io_funciones_inventario->uf_obtenervalor("chkestdetart",0);
			switch ($ls_metodo)
			{
				case"":
					$ls_selected="selected";
				break;
				case"FIFO":
					$ls_selectedfifo="selected";
				break;
				case"LIFO":
					$ls_selectedlifo="selected";
				break;
				case"CPP":
					$ls_selectedcpp="selected";
				break;
			}
			if($li_estcatsig==1)
			{$ls_checksig="checked";}
			if($li_estnum==1)
			{$ls_checknum="checked";}
			else
			{$ls_checkalfnum="checked";}
			if($li_estcont==1)
			{$ls_checkcont="checked";}
			$ls_id="1";
			$ls_status=true;
			if($li_estcencos==1)
			{$ls_checkcencos="checked";}
			
			if($ls_metodo=="--")
			{$io_msg->message("Debe seleccionar un Método");}
			else
			{
				if ($ls_status)
				{
					$lb_valido=$io_siv->uf_process_configuracion($ls_codemp,$ls_id,$ls_metodo,$li_estcatsig,$li_estnum,
																  $li_estcmp,$la_seguridad);
				}
				$lb_valido=$io_siv->uf_siv_procesar_configuraciondespacho($ls_codemp,$li_estcont,$la_seguridad);
				if($lb_valido)
				{$io_msg->message("El estaus de contabilización de despacho ha sido actualizado");}
				else
				{$io_msg->message("No se pudo actualizar el estaus de contabilización de despacho");}
				$lb_valido=$io_siv->uf_siv_procesar_articulos_primarios($ls_codemp,$li_artpri,$la_seguridad);
				if($lb_valido)
				{
					$lb_valido=$io_siv->uf_siv_procesar_centro_costos($ls_codemp,$li_estcencos,$la_seguridad);
				}
				if($lb_valido)
				{
					$lb_valido=$io_siv->uf_siv_procesar_almacenes_produccion($ls_codemp,$li_traalmpro,$la_seguridad);
				}
				if($lb_valido)
				{
					$lb_valido=$io_siv->uf_siv_procesar_cuenta_contable($ls_codemp,$ls_sccuenta,$la_seguridad);
				}
				if($lb_valido)
				{
					$lb_valido=$io_siv->uf_siv_procesar_almacenes_mercado($ls_codemp,$li_estalmmer,$la_seguridad);
					if($lb_valido)
					{
						$io_msg->message("Se actualizo el estatus de almacenes de mercado");					
					}
					if($lb_valido)
					{
						$lb_valido=$io_siv->uf_siv_procesar_tipoarticulo($ls_codemp,$ls_codtipart,$la_seguridad);
					}
				}
				if($lb_valido)
				{
					$lb_valido=$io_siv->uf_siv_procesar_detalles_articulos($ls_codemp,$li_estdetart,$la_seguridad);
				}
			}
		break;

	}
	$ls_readonly="readonly";
	uf_limpiarvariables();
	$li_estnum="";
	$arrResultado= $io_siv->uf_siv_load_configuracion($ls_metodo,$li_estcatsig,$li_estnum,$li_estcmp);
	$ls_metodo = $arrResultado['as_metodo'];
	$li_estcatsig = $arrResultado['as_estcatsig'];
	$li_estnum = $arrResultado['as_estnum'];
	$li_estcmp = $arrResultado['as_estcmp'];
	$lb_existe= $arrResultado['lb_valido'];
	if($lb_existe)
	{
		$ls_metodo=trim($ls_metodo);
		switch ($ls_metodo)
		{
			case"":
				$ls_selected="selected";
			break;
			case"FIFO":
				$ls_selectedfifo="selected";
			break;
			case"LIFO":
				$ls_selectedlifo="selected";
			break;
			case"CPP":
				$ls_selectedcpp="selected";
			break;
		}
		if($li_estcatsig==1)
		{$ls_checksig="checked";}
		if($li_estnum==1)
		{$ls_checknum="checked";}
		else
		{$ls_checkalfnum="checked";}
	}
	else
	{$ls_selected="selected";}
	if($li_estcmp!=1)
	{$ls_checkcmp="";}
	
	$arrResultado= $io_siv->uf_siv_load_configuraciondespacho($ls_codemp,$li_estcont);
	$lb_existe=$arrResultado['lb_valido'];
	$li_estcont = $arrResultado['as_value'];
	if($lb_existe)
	{
		if($li_estcont==1)
		{$ls_checkcont="checked";}
	}
	
	$arrResultado= $io_siv->uf_siv_load_articulos_primarios($ls_codemp,$li_estartpri);
	$li_estartpri = $arrResultado['as_value'];
	$lb_existe=$arrResultado['lb_valido'];
	if($lb_existe)
	{
		if($li_estartpri==1)
		{$ls_checkartpri="checked";}
	}
	
	$arrResultado= $io_siv->uf_siv_load_centro_costos($ls_codemp,$li_estcencos);
	$li_estcencos = $arrResultado['as_value'];
	$lb_existe=$arrResultado['lb_valido'];
	if($lb_existe)
	{
		if($li_estcencos==1)
		{$ls_checkcencos="checked";}
	}
	
	$arrResultado= $io_siv->uf_siv_load_almacenes_produccion($ls_codemp,$li_estalmpro);
	$li_estalmpro = $arrResultado['as_value'];
	$lb_existe=$arrResultado['lb_valido'];
	if($lb_existe)
	{
		if($li_estalmpro==1)
		{$ls_checktraalmpro="checked";}
	}
	$arrResultado=$io_siv->uf_siv_load_almacenes_mercado($ls_codemp);
	$li_estalmmer = $arrResultado['as_value'];
	$lb_existe=$arrResultado['lb_valido'];
	if($lb_existe)
	{
		if($li_estalmmer==1)
		{$ls_checkestalmmer="checked";}
	}
	
	$arrResultado=$io_siv->uf_siv_load_cuenta_contable($ls_codemp,$ls_sccuenta,$ls_densccuenta);
	$ls_sccuenta = $arrResultado['as_value'];
	$ls_densccuenta = $arrResultado['as_descripcion'];
	$lb_existe = $arrResultado['lb_valido'];
	
	$arrResultado= $io_siv->uf_siv_load_detalles_articulos($ls_codemp);
	$li_estdetart = $arrResultado['as_value'];
	$lb_existe=$arrResultado['lb_valido'];
	if($lb_existe)
	{
		if($li_estdetart==1)
		{$ls_checkestdetart="checked";}
	}
	$arrResultado=$io_siv->uf_siv_load_tipoarticulo($ls_codemp);
	$ls_codtipart = $arrResultado['as_value'];
	
	
?>

<p>&nbsp;</p>
<div align="center">
  <table width="512" height="134" border="0" class="formato-blanco">
    <tr>
      <td width="538" height="130"><div align="left">
          <form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_activo->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_activo);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>	
<table width="484" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td colspan="2" class="titulo-ventana">Configuraci&oacute;n de Inventario</td>
  </tr>
  <tr class="formato-blanco">
    <td width="135" height="13">&nbsp;</td>
    <td width="347">
      <div align="left"></div></td>
  </tr>
  <tr class="formato-blanco">
    <td height="22"><div align="right">M&eacute;todo de Inventario</div></td>
    <td><div align="left">
      <select name="cmbmetodo" id="cmbmetodo">
        
        <option value="--" <?php print $ls_selected; ?>>-- Seleccione Uno --</option>
        <option value="FIFO" <?php print $ls_selectedfifo; ?>>FIFO - PEPS</option>
        <option value="LIFO" <?php print $ls_selectedlifo; ?>>LIFO - UEPS</option>
        <option value="CPP" <?php print $ls_selectedcpp; ?>>Costo Promedio Ponderado</option>
      </select>
    </div></td>
  </tr>
  <tr class="formato-blanco">
    <td height="22"><div align="right">Codificaci&oacute;n de Art&iacute;culos</div></td>
    <td><div align="left">
      <input name="rdcodigo" type="radio" class="sin-borde" value="0" <?php print $ls_checkalfnum ?>>
      Alfanum&eacute;rico
      <input name="rdcodigo" type="radio" class="sin-borde" value="1" <?php print $ls_checknum; ?>>
      Num&eacute;rico</div></td>
  </tr>
  <tr class="formato-blanco">
    <td height="22">&nbsp;</td>
    <td>
      <p align="left">
        <input name="chksigecof" type="checkbox" class="sin-borde" id="chksigecof" value="1" <?php print $ls_checksig; ?>>
Usar Cat&aacute;logo SIGECOF </p>    </td>
    </tr>
  <tr class="formato-blanco">
    <td height="22"><div align="right"></div></td>
    <td><div align="left">
      <input name="chkcontabilizar" type="checkbox" class="sin-borde" id="chkcontabilizar" value="1" <?php print $ls_checkcont; ?>>
     Contabilizar Despachos </div></td>
  </tr>
  <tr>
    <td height="22">&nbsp;</td>
    <td><input name="chkestcmp" type="checkbox" class="sin-borde" id="chkestcmp" value="1" <?php print $ls_checkcmp; ?>>
      Completar C&oacute;digo de Articulos con Ceros </td>
  </tr>
  <tr class="formato-blanco">
    <td height="22">&nbsp;</td>
    <td><input name="chkartpri" type="checkbox" class="sin-borde" id="chkartpri" value="1" <?php print $ls_checkartpri; ?>> 
      Trabajar con Articulos Dependientes </td>
  </tr>
  <tr class="formato-blanco">
    <td height="22">&nbsp;</td>
    <td><input name="chktraalmpro" type="checkbox" class="sin-borde" id="chktraalmpro" value="1"  <?php print $ls_checktraalmpro; ?> onClick="javascript: ue_validar();">
      Trabajar con Almacenes de producci&oacute;n</td>
  </tr>
  <tr class="formato-blanco">
    <td height="22">&nbsp;</td>
    <td><input name="chkestcencos" type="checkbox" class="sin-borde" id="chkestcencos" value="1"  <?php print $ls_checkcencos; ?> onClick="javascript: ue_validar();">
      Despachos con Centro de Costos </td>
  </tr>
  <tr class="formato-blanco">
    <td height="22">&nbsp;</td>
    <td><table width="400" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td colspan="2"><input name="chkestalmmer" type="checkbox" class="sin-borde" id="chkestalmmer" value="1"   <?php print $ls_checkestalmmer; ?> onClick="javascript: ue_validar();">
Trabajar con Almacenes de Empaquetado </td>
          </tr>
          <tr>
            <td width="134"><div align="right">Tipo de Articulo </div></td>
            <td width="264"><input name="txtcodtipart" type="text" id="txtcodtipart" value="<?php print $ls_codtipart; ?>" size="6">
              <a href="javascript: ue_tipoarticulo();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></td>
          </tr>
        </table></td>
  </tr>
  <tr class="formato-blanco">
    <td height="22">&nbsp;</td>
    <td><input name="chkestdetart" type="checkbox" class="sin-borde" id="chkestdetart" value="1"   <?php print $ls_checkestdetart; ?> onClick="javascript: ue_validar();"> 
      Trabajar con Detalles de Articulos </td>
  </tr>
  <tr class="formato-blanco">
    <td height="22">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr class="formato-blanco">
    <td height="22"><div align="right">Cuenta Costo de Venta </div></td>
    <td><input name="txtsccuenta" type="text" id="txtsccuenta" style="text-align:center" value="<?php print $ls_sccuenta?>" size="22" readonly>
      <a href="javascript: ue_catascg();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
      <input name="txtdensccuenta" type="text" class="sin-borde" id="txtdensccuenta" value="<?php print $ls_densccuenta?>" size="30" readonly></td>
  </tr>
  <tr class="formato-blanco">
    <td height="22">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<input name="operacion" type="hidden" id="operacion">
          </form>
      </div></td>
    </tr>
  </table>
</div>
<p align="center">&nbsp;</p>
</body>
<script >
function ue_mostrar(ls_id,ls_accion)
{
	f=document.form1;
	if(ls_accion==1)
	{
		document.getElementById(ls_id).style.visibility="visible";
	}
	else
	{
		document.getElementById(ls_id).style.visibility="hidden";	
	}
}

//Funciones de operaciones sobre el comprobante
function ue_nuevo()
{
	f=document.form1;
	f.operacion.value="NUEVO";
	f.action="sigesp_siv_d_configuracion.php";
	f.submit();
}
function ue_validar()
{
	f=document.form1;
	if(f.chktraalmpro.checked==true)
	{
		if(f.chkestcencos.checked==true)
		{
			alert("No se puede trabajar con centro de costos y con almacenes de produccion");
			f.chkestcencos.checked=false;
		}
	}
	if(f.chkestalmmer.checked==true)
	{
		if(f.chkestcencos.checked==true)
		{
			alert("No se puede trabajar con centro de costos y con almacenes de mercado");
			f.chkestcencos.checked=false;
		}
	}
}

function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		if((f.rdcodigo[0].checked==true)||(f.rdcodigo[1].checked==true))
		{
			f.operacion.value="GUARDAR";
			f.action="sigesp_siv_d_configuracion.php";
			f.submit();
		}
		else
		{
		    alert("Debe tildar una opcion Alfanumerico ó Numerico");
		}
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

function ue_catascg()
{
	window.open("sigesp_siv_cat_ctasscg.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_tipoarticulo()
{
	tipo="CONFIGURACION";
	window.open("sigesp_catdinamic_tipoarticulo.php?tipo="+tipo,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}

</script> 
</html>