<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
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
	require_once("class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$arrResultado=$io_fun_cxp->uf_load_seguridad("CXP","sigesp_cxp_r_relacionanticipos.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado["as_permisos"];
	$la_seguridad=$arrResultado["aa_seguridad"];
	$la_permisos=$arrResultado["aa_permisos"];
	unset($arrResultado);
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Reporte de Relacion de Anticipos </title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funcion_cxp.js"></script>

<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<link href="css/cxp.css" rel="stylesheet" type="text/css">
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
<table width="797" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="804" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="793" border="0" align="center" cellpadding="0" cellspacing="0">
			
          <td width="423" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Cuentas por Pagar </td>
			<td width="370" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	  <tr>
	  	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	    <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
    </table>    </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="26" class="toolbar"><div align="center"><a href="javascript: uf_mostrar_reporte();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0" title="Imprimir"></a></div></td>
    <td class="toolbar" width="26"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></div></td>
    <td class="toolbar" width="26"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0" title="Ayuda"></a></div></td>
    <td class="toolbar" width="26"><div align="center"></div></td>
    <td class="toolbar" width="26">&nbsp;</td>
    <td class="toolbar" width="26"><div align="center"></div></td>
    <td class="toolbar" width="26"><div align="center"></div></td>
    <td class="toolbar" width="26"><div align="center"></div></td>
    <td class="toolbar" width="26"><div align="center"></div></td>
    <td class="toolbar" width="26"><div align="center"></div></td>
    <td class="toolbar" width="535">&nbsp;</td>
  </tr>
</table>
</div> 
<p>&nbsp;	</p>
<form name="formulario" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_cxp->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_cxp);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>	

  <table width="578" height="18" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="561" colspan="2" class="titulo-ventana">Reporte de Relacion de Anticipos </td>
    </tr>
  </table>
  <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="573"></td>
    </tr>
    <tr style="visibility:hidden">
      <td height="22" colspan="3" align="center"><div align="left">Reporte en
          <select name="cmbbsf" id="cmbbsf">
            <option value="0" selected>Bs.</option>
            <option value="1">Bs.F.</option>
          </select>
</div></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center"><table width="511" border="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td width="213" height="22"><div align="right">
            <input name="rdproben" type="radio" class="sin-borde" value="radiobutton" onClick="javascript: ue_limpiarproben();" checked>
            Todos</div></td>
          <td width="92" height="22"><div align="center">
                <input name="rdproben" type="radio" class="sin-borde" value="radiobutton" onClick="javascript: ue_limpiarproben();">
            Proveedor</div></td>
          <td width="198" height="22"><div align="left">
            <input name="rdproben" type="radio" class="sin-borde" value="radiobutton" onClick="javascript: ue_limpiarproben();">
          Beneficiario</div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Desde
            <input name="txtcoddes" type="text" id="txtcoddes" size="20" readonly>
              <a href="javascript: ue_catalogo_proben('REPDES');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">Hasta</div></td>
          <td><input name="txtcodhas" type="text" id="txtcodhas" size="20" readonly>
            <a href="javascript: ue_catalogo_proben('REPHAS');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></td>
        </tr>

      </table></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="33" colspan="3" align="center">      <div align="left">
        <table width="511" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td height="22" colspan="5"><strong>Fecha de Registro de la Factura </strong></td>
            </tr>
          <tr>
            <td width="136"><div align="right">Desde</div></td>
            <td width="101"><input name="txtfecregdes" type="text" id="txtfecregdes"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" size="15" maxlength="10"  datepicker="true"></td>
            <td width="42"><div align="right">Hasta</div></td>
            <td width="129"><div align="left">
                <input name="txtfecreghas" type="text" id="txtfecreghas"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" size="15" maxlength="10"  datepicker="true">
            </div></td>
            <td width="101">&nbsp;</td>
          </tr>
        </table>
      </div></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center">&nbsp;</td>
    </tr>
  </table>
    <p align="center">
<input name="total" type="hidden" id="total" value="<?php print $totrow;?>">
</p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
var patron = new Array(2,2,4)
var patron2 = new Array(1,3,3,3,3)
	function ue_limpiarproben()
	{
		f=document.formulario;
		f.txtcoddes.value="";
		f.txtcodhas.value="";
	}

	function ue_catalogo(ls_catalogo)
	{
		// abre el catalogo que se paso por parametros
		window.open(ls_catalogo,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
	}

	function ue_catalogo_proben(ls_tipo)
	{
		f=document.formulario;
		valido=true;		
		if(f.rdproben[0].checked)
		{
			valido=false;
		}
		if(f.rdproben[1].checked)
		{
			ls_catalogo="sigesp_cxp_cat_proveedor.php?tipo="+ls_tipo+"";
		}
		if(f.rdproben[2].checked)
		{
			ls_catalogo="sigesp_cxp_cat_beneficiario.php?tipo="+ls_tipo+"";
		}
		if(valido)
		{		
			window.open(ls_catalogo,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
		}
		else
		{
			alert("Debe indicar si es Proveedor � Beneficiario");
		}
	}
	
	function uf_mostrar_reporte()
	{
			f=document.formulario;
			li_imprimir=f.imprimir.value;
			if(li_imprimir==1)
			{
				if(f.rdproben[0].checked)
				{
					tipproben="";
				}
				else
				{
					if(f.rdproben[1].checked)
					{
						tipproben="P";
					}
					else
					{
						tipproben="B";
					}
				}
				codprobendes=f.txtcoddes.value;
				codprobenhas=f.txtcodhas.value;
				fecregdes=f.txtfecregdes.value;
				fecreghas=f.txtfecreghas.value;
				tiporeporte=f.cmbbsf.value;
				pantalla="reportes/sigesp_cxp_rpp_relacionanticipos.php?tipproben="+tipproben+"&codprobendes="+codprobendes+"&codprobenhas="+codprobenhas+"&fecregdes="+fecregdes+"&fecreghas="+fecreghas+"&tiporeporte="+tiporeporte+"";
				window.open(pantalla,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
			}
			else
			{alert("No tiene permiso para realizar esta operaci�n");}
	}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}
</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
</html>