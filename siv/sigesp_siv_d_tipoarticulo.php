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
$arrResultado = $io_fun_activo->uf_load_seguridad("SIV","sigesp_siv_d_tipoarticulo.php",$ls_permisos,$la_seguridad,$la_permisos);
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
   		global $ls_codtipart,$ls_dentipart,$ls_obstipart;
		
		$ls_codtipart="";
		$ls_dentipart="";
		$ls_obstipart="";
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Definici&oacute;n de Tipo de Art&iacute;culo </title>
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
<script type="text/javascript"  src="js/funciones.js"></script>
<script type="text/javascript"  src="../shared/js/valida_tecla.js"></script>
<link href="css/siv.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
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
    <td height="13" colspan="11" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" width="20" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="24"><div align="center"></div></td>
    <td class="toolbar" width="618">&nbsp;</td>
  </tr>
</table>
<?php
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=     new sigesp_include();
	$con= $in->uf_conectar();
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_msg= new class_mensajes();
	require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
	$io_fun= new class_funciones_db($con);
	require_once("sigesp_siv_c_tipoarticulo.php");
	$io_siv= new sigesp_siv_c_tipoarticulo();

	$arre=$_SESSION["la_empresa"];
	$ls_empresa=$arre["codemp"];
	if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
	}
	else
	{
		$ls_operacion="";
		uf_limpiarvariables();
		$ls_readonly="readonly";
	}

	switch ($ls_operacion) 
	{
		case "NUEVO":
			uf_limpiarvariables();
			$ls_readonly="";
			
			$ls_emp="";
			$ls_codemp="";
			$ls_tabla="siv_tipoarticulo";
			$ls_columna="codtipart";
		
			$ls_codtipart=$io_fun->uf_generar_codigo($ls_emp,$ls_codemp,$ls_tabla,$ls_columna);
			if($ls_codtipart==false)
			{
				print "<script language=JavaScript>";
				print "location.href='sigespwindow_blank.php'";
				print "</script>";
			}
		break;
		
		case "GUARDAR";
		
		$ls_valido= false;
		$ls_readonly="";
		$ls_codtipart=$_POST["txtcodtipart"];
		$ls_dentipart=$_POST["txtdentipart"];
		$ls_obstipart=$_POST["txtobstipart"];
		$ls_esttipmer=$_POST["chkesttipmer"];
		$ls_codunimed=$_POST["txtcodunimed"];
		$ls_spgcuenta=$_POST["txtspg_cuenta"];
		$ls_sccuenta=$_POST["txtsccuenta"];
		$ls_status=$_POST["hidstatus"];
		$ls_clasif=$_POST["cmbclasificacion"];
		if( ($ls_codtipart=="")||($ls_dentipart=="")||($ls_clasif==""))
			{
				$io_msg->message("Debe compeltar los campos código y denominación");
			}
		else
			{
				if ($ls_status=="C")
				{
					$lb_valido=$io_siv->uf_siv_update_tipoarticulo($ls_codtipart,$ls_dentipart,$ls_obstipart,
					                                               $ls_clasif,$ls_esttipmer,$ls_codunimed,$ls_spgcuenta,$ls_sccuenta, $la_seguridad);
	
					if($lb_valido)
					{
						$io_msg->message("El tipo de artículo fue actualizado");
						uf_limpiarvariables();
						
					}	
					else
					{
						$io_msg->message("El tipo de artículo no pudo ser actualizado");
						uf_limpiarvariables();
					}
				}
				else
				{
					$lb_encontrado=$io_siv->uf_siv_select_tipoarticulo($ls_codtipart);
					if ($lb_encontrado)
					{
						$io_msg->message("El tipo de artículo ya existe"); 
					}
					else
					{
						$lb_valido=$io_siv->uf_siv_insert_tipoarticulo($ls_codtipart,$ls_dentipart,$ls_obstipart,
						                                               $ls_clasif,$ls_esttipmer,$ls_codunimed,$ls_spgcuenta,$ls_sccuenta, $la_seguridad);

						if ($lb_valido)
						{
							$io_msg->message("El tipo de artículo fue registrado.");
							uf_limpiarvariables();
						}
						else
						{
							$io_msg->message("No se pudo registrar el tipo de artículo.");
							uf_limpiarvariables();
						}
					
					}
				}
				
			}
		break;

		case "ELIMINAR":
			$ls_codtipart=$_POST["txtcodtipart"];
			$io_msg=new class_mensajes();
			
			$lb_valido=$io_siv->uf_siv_delete_tipoarticulo($ls_codtipart,$la_seguridad);
	
			if($lb_valido)
			{
				$io_msg->message("El tipo de artículo fue eliminado");
				uf_limpiarvariables();
				$ls_readonly="readonly";
			}	
			else
			{
				$io_msg->message("No se pudo eliminar el tipo de artículo");
				uf_limpiarvariables();
				$ls_readonly="readonly";
			}
		break;
	}
	
	
?>

<p>&nbsp;</p>
<div align="center">
  <table width="596" height="159" border="0" class="formato-blanco">
    <tr>
      <td width="588" height="153"><div align="left">
          <form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_activo->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_activo);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>	
<table width="566" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td colspan="2" class="titulo-ventana">Definici&oacute;n de Tipo de Art&iacute;culo </td>
  </tr>
  <tr class="formato-blanco">
    <td width="111" height="19">&nbsp;</td>
    <td width="408">&nbsp;</td>
  </tr>
  <tr class="formato-blanco">
    <td height="22"><div align="right">C&oacute;digo</div></td>
    <td height="22"><input name="txtcodtipart" type="text" id="txtcodtipart" value="<?php print $ls_codtipart?>" size="10" maxlength="4" <?php print $ls_readonly?> onKeyUp="javascript:ue_validarnumero(this)"  onBlur="javascript: ue_rellenarcampo(this,4);" style="text-align:center ">
        <input name="hidstatus" type="hidden" id="hidstatus"></td>
  </tr>
  <tr>
    <td height="24"><div align="right">Denominaci&oacute;n</div></td>
    <td height="24"><input name="txtdentipart" type="text" id="txtdentipart" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn&ntilde;opqrstuvwxyz ()#!%/[]*-+_.,:;');" value="<?php print $ls_dentipart?>" size="50" maxlength="254"></td>
  </tr>
  <tr class="formato-blanco">
    <td height="22"><div align="right">Clasificaci&oacute;n </div></td>
    <td height="22"><label>
      <select name="cmbclasificacion" id="cmbclasificacion">
	  <option value="" selected>--Seleccione una opción--</option>
	  <option value="1">Bienes</option>
	  <option value="2">Materiales y Suministros</option>
      </select>
    </label></td>
  </tr>
  <tr class="formato-blanco">
    <td height="24"><div align="right">Observaciones</div></td>
    <td rowspan="2"><textarea name="txtobstipart" cols="50" id="txtobstipart"  onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmnñopqrstuvwxyz ()#!%/[]*-+_.,:;');"><?php print $ls_obstipart ?></textarea></td>
  </tr>
  <tr class="formato-blanco">
    <td height="28">&nbsp;</td>
    </tr>
  <tr class="formato-blanco">
    <td height="22"><div align="right">
      <input name="chkesttipmer" type="checkbox" class="sin-borde" id="chkesttipmer" value="1">
    </div></td>
    <td height="22"><div align="left">Tipo de Articulo Empaquetado </div></td>
  </tr>
  <tr class="formato-blanco">
    <td height="22"><div align="right">Unidad de Medida </div></td>
    <td height="22"><div align="left">
      <input name="txtcodunimed" type="text" id="txtcodunimed" value="<?php print $ls_codunimed?>" size="6" maxlength="4" readonly>
      <a href="javascript: ue_cataunimed();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
      <input name="txtdenunimed" type="text" class="sin-borde" id="txtdenunimed" value="<?php print $ls_denunimed?>" size="30" readonly>
      <input name="txtunidad" type="hidden" id="txtunidad">
      <input name="txtobsunimed" type="hidden" id="txtobsunimed">
    </div></td>
  </tr>
  <tr class="formato-blanco">
    <td height="22"><div align="right">Cuenta Presupuestaria </div></td>
    <td height="22"><div align="left">
      <input name="txtspg_cuenta" type="text" id="txtspg_cuenta" onKeyUp="javascript: ue_validarcomillas(this);" value="<?php print $ls_spg_cuenta?>" size="25" maxlength="25" readonly style="text-align:center ">
      <a href="javascript: ue_cataspg();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
  </tr>
  <tr class="formato-blanco">
    <td height="22"><div align="right">Cuenta Contable </div></td>
    <td height="22"><div align="left">
      <input name="txtsccuenta" type="text" id="txtsccuenta" value="<?php print $ls_sccuenta?>" size="25" style="text-align:center" readonly>
      <a href="javascript: ue_catascg();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
      <input name="txtdensccuenta" type="text" class="sin-borde" id="txtdensccuenta"  value="<?php print $ls_densccuenta?>" size="35" readonly>
    </div></td>
  </tr>
  <tr class="formato-blanco">
    <td height="22"><div align="right"></div></td>
    <td height="22"><div align="left"></div></td>
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
//Funciones de operaciones 
function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if(li_leer==1)
	{
		tipo="DEFINICION";
		window.open("sigesp_catdinamic_tipoarticulo.php?tipo="+tipo,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
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
		f.action="sigesp_siv_d_tipoarticulo.php";
		f.submit();
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}
function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	lb_status=f.hidstatus.value;
	if(((lb_status=="C")&&(li_cambiar==1))||(lb_status!="C")&&(li_incluir==1))
	{
		f.operacion.value="GUARDAR";
		f.action="sigesp_siv_d_tipoarticulo.php";
		f.submit();
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}
function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1)
	{	
		if(confirm("¿Seguro desea eliminar el Registro?"))
		{
			f.operacion.value="ELIMINAR";
			f.action="sigesp_siv_d_tipoarticulo.php";
			f.submit();
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
function ue_cataunimed()
{
	tipo="tipoarticulo";
	window.open("sigesp_catdinamic_unidadmedida.php?tipo="+tipo,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_cataspg()
{
	window.open("sigesp_siv_cat_ctasspg.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_catascg()
{
	window.open("sigesp_siv_cat_ctasscg.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_catascginv()
{
	f=document.form1;
	tipo="centrocostos";
	window.open("sigesp_siv_cat_ctasscg.php?tipo="+tipo,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}


</script> 
</html>