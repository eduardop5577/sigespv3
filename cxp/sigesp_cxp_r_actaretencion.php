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
	$ls_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);
	//$ls_ano=date('Y');
	$ls_mes=date('m');
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$arrResultado=$io_fun_cxp->uf_load_seguridad("CXP","sigesp_cxp_r_actaretencion.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado["as_permisos"];
	$la_seguridad=$arrResultado["aa_seguridad"];
	$la_permisos=$arrResultado["aa_permisos"];
	unset($arrResultado);

	$ls_reporte=$io_fun_cxp->uf_select_config("CXP","REPORTE","ACTA_APORTE","sigesp_cxp_rpp_actaaportes.php","C");
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Acta Compromiso de Responsabilidad Social</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funcion_sep.js"></script>

<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<link href="css/cxp.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script  src="js/funcion_cxp.js"></script>
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
<table width="714" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="807" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			
          <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Cuentas por Pagar </td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
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
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_print();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0" title="Imprimir"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_print_word();"><img src="../shared/imagebank/tools20/word.JPG" alt="Imprimir" width="20" height="20" border="0" title="Imprimir"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0" title="Ayuda"></a></div></td>
    <td class="toolbar" width="25">&nbsp;</td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="462">&nbsp;</td>
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
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="142"></td>
    </tr>
    <tr class="titulo-ventana">
      <td height="22" colspan="4" align="center">Acta Compromiso de Responsabilidad Social</td>
    </tr>
    <tr>
      <td height="22" colspan="4" align="center"><div align="left"></div></td>
    </tr>
    <tr>
      <td height="22" colspan="4" align="center"><table width="511" border="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td height="22" colspan="4" class="titulo-celda">Filtros de Busqueda </td>
          </tr>
        <tr>
          <td width="204" height="22"><div align="right">
              <input name="rdproben" type="radio" class="sin-borde" value="radiobutton" onClick="javascript: ue_limpiarproben();" checked>
            Todos</div></td>
          <td width="84" height="22"><div align="center">
              <input name="rdproben" type="radio" class="sin-borde" value="radiobutton" onClick="javascript: ue_limpiarproben();">
            Proveedor</div></td>
          <td height="22" colspan="2"><div align="left">
              <input name="rdproben" type="radio" class="sin-borde" value="radiobutton" onClick="javascript: ue_limpiarproben();">
            Beneficiario</div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Desde
            <input name="txtcoddes" type="text" id="txtcoddes" size="14" readonly>
              <a href="javascript: ue_catalogo_proben('REPDES');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">Hasta</div></td>
          <td colspan="2"><input name="txtcodhas" type="text" id="txtcodhas" size="14" readonly>
            <a href="javascript: ue_catalogo_proben('REPHAS');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></td>
        </tr>
        <tr>
          <td height="22"><div align="center"><strong>Rango de Fecha </strong></div></td>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Desde
            <input name="txtfecdes" type="text" id="txtfecdes" onBlur="javascript: ue_validar_formatofecha(this);"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" size="12" maxlength="10"  datepicker="true">
          </div></td>
          <td><div align="right">Hasta</div></td>
          <td width="92">
            <div align="left">
              <input name="txtfechas" type="text" id="txtfechas"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" size="12" maxlength="10"  datepicker="true">
            </div></td>
          <td width="121">&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Procedencia
            <select name="cmbprocede" id="cmbprocede">
              <option value="---">Seleccione</option>
              <option value="SEPSCP">SEP</option>
              <option value="SOCCOC">Compras - Bienes</option>
              <option value="SOCCOS">Compras - Servicios</option>
              <option value="CXPRCD">Cuentas por Pagar</option>
              <option value="SOBCON">Obras - Contratos</option>
              </select>
          </div></td>
          <td><div align="right"></div></td>
          <td colspan="2">&nbsp;</td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td height="22" align="center"><div align="right"></div></td>
      <td width="208" height="22" align="center"><div align="left">
        <label></label>
</div></td>
      <td width="66" height="22" align="center"><div align="right"></div></td>
      <td width="182" align="center"><div align="left">
        <label></label>
</div></td>
    </tr>
    <tr>
      <td height="22" colspan="4" align="center"><table width="511" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td height="22" colspan="2" class="titulo-celda">Informacion Adicional </td>
          </tr>
        <tr>
          <td height="22"><div align="right">Acta</div></td>
          <td height="22"><input name="txtcodigo" type="text" id="txtcodigo" size="6">
            <a href="javascript: ue_catalogo_acta();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
            <input name="txtnombre" type="text" class="sin-borde" id="txtnombre" size="18"></td>
        </tr>
        <tr>
          <td width="151" height="22"><div align="right">Tipo de Contratacion </div></td>
          <td width="360" height="22"><input name="txttipcont" type="text" id="txttipcont" size="45"></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Codigo de Contratacion </div></td>
          <td height="22"><input name="txtcodcont" type="text" id="txtcodcont" size="45"></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Condicion de Pago </div></td>
          <td height="22"><input name="txtcondpag" type="text" id="txtcondpag" size="45"></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="22" colspan="4" align="center"><div align="right"><a href="javascript:ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" width="20" height="20" border="0">Buscar Documentos</a></div></td>
    </tr>
    <tr>
      <td colspan="4" align="center">
  		<div id="resultados" align="center"></div>	</td>
    </tr>
  </table>
	<input name="total" type="hidden" id="total" value="<?php print $totrow;?>">
    <input name="formato"    type="hidden" id="formato"    value="<?php print $ls_reporte; ?>">
</form>      
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


function ue_catalogo_acta()
{
	tipo="REPORTE";
	window.open("sigesp_cxp_cat_conf_acta.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=630,height=400,left=50,top=50,location=no,resizable=no");
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
		alert("Debe indicar si es Proveedor ó Beneficiario");
	}
}
	
function ue_print()
{
	f=document.formulario;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{
		ls_comprobantes="";
		totrow=ue_calcular_total_fila_local("txtnumcom");
		f.total.value=totrow;
		for(li_i=1;li_i<=totrow;li_i++)
		{
			if(eval("f.checkcmp"+li_i+".checked==true"))
			{
				ls_compromiso=eval("f.txtnumcom"+li_i+".value");
				ls_numrecdoc=eval("f.numrecdoc"+li_i+".value");
				ls_codtipdoc=eval("f.codtipdoc"+li_i+".value");
				ls_cedbene=eval("f.ced_bene"+li_i+".value");
				ls_codpro=eval("f.cod_pro"+li_i+".value");
				ls_documento=ls_compromiso+"**"+ls_numrecdoc+"**"+ls_codtipdoc+"**"+ls_cedbene+"**"+ls_codpro;
				if(ls_comprobantes.length>0)
				{
					ls_comprobantes = ls_comprobantes+"@@"+ls_documento;
				}
				else
				{
					ls_comprobantes = ls_documento;
				}
			}
		}
		if(ls_comprobantes!="")
		{
			codigo=f.txtcodigo.value;
			tipcont=f.txttipcont.value;
			codcont=f.txtcodcont.value;
			condpag=f.txtcondpag.value;
			formato=f.formato.value;
			if(codigo!="")
			{
				pagina="reportes/"+formato+"?comprobantes="+ls_comprobantes+"&codigo="+codigo+"&tipcont="+tipcont+"&codcont="+codcont+"&condpag="+condpag;
				window.open(pagina,"reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no,left=0,top=0");
			}
			else
			{
				alert("Debe seleccionar un modelo de Acta a imprimir");
			}
		}
		else
		{
			alert("Debe seleccionar al menos un Número de Documento.");	   
		}	  
	}
	else
	{
		alert("No tiene permiso para realizar esta operación");
	}
}

function ue_print_word()
{
	f=document.formulario;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{
		ls_comprobantes="";
		totrow=ue_calcular_total_fila_local("txtnumcom");
		f.total.value=totrow;
		for(li_i=1;li_i<=totrow;li_i++)
		{
			if(eval("f.checkcmp"+li_i+".checked==true"))
			{
				ls_compromiso=eval("f.txtnumcom"+li_i+".value");
				ls_numrecdoc=eval("f.numrecdoc"+li_i+".value");
				ls_codtipdoc=eval("f.codtipdoc"+li_i+".value");
				ls_cedbene=eval("f.ced_bene"+li_i+".value");
				ls_codpro=eval("f.cod_pro"+li_i+".value");
				ls_documento=ls_compromiso+"**"+ls_numrecdoc+"**"+ls_codtipdoc+"**"+ls_cedbene+"**"+ls_codpro;
				if(ls_comprobantes.length>0)
				{
					ls_comprobantes = ls_comprobantes+"@@"+ls_documento;
				}
				else
				{
					ls_comprobantes = ls_documento;
				}
			}
		}
		if(ls_comprobantes!="")
		{
			codigo=f.txtcodigo.value;
			tipcont=f.txttipcont.value;
			codcont=f.txtcodcont.value;
			condpag=f.txtcondpag.value;
			formato=f.formato.value;
			if(codigo!="")
			{
				pagina="reportes/sigesp_cxp_rpp_actaaportes_word.php?comprobantes="+ls_comprobantes+"&codigo="+codigo+"&tipcont="+tipcont+"&codcont="+codcont+"&condpag="+condpag;
				window.open(pagina,"reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no,left=0,top=0");
			}
			else
			{
				alert("Debe seleccionar un modelo de Acta a imprimir");
			}
		}
		else
		{
			alert("Debe seleccionar al menos un Número de Documento.");	   
		}	  
	}
	else
	{
		alert("No tiene permiso para realizar esta operación");
	}
}

function ue_search()
{
	f=document.formulario;
	// Cargamos las variables para pasarlas al AJAX
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
	fecdes=f.txtfecdes.value;
	fechas=f.txtfechas.value;
	procede=f.cmbprocede.value;
	mes="";
	anio="";
	// Div donde se van a cargar los resultados
	divgrid = document.getElementById('resultados');
	// Instancia del Objeto AJAX
	ajax=objetoAjax();
	// Pagina donde están los métodos para buscar y pintar los resultados
	ajax.open("POST","class_folder/sigesp_cxp_c_catalogo_ajax.php",true);
	ajax.onreadystatechange=function(){
		if(ajax.readyState==1)
		{
			divgrid.innerHTML = "<img src='imagenes/loading.gif' width='350' height='200'>";//<-- aqui iria la precarga en AJAX 
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
	ajax.send("catalogo=ACTAAPORTE&tipproben="+tipproben+"&codprobendes="+codprobendes+"&codprobenhas="+codprobenhas+
			  "&fecdes="+fecdes+"&fechas="+fechas+"&mes="+mes+"&anio="+anio+"&procede="+procede);
}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

function uf_checkall()
{
	f=document.formulario;
	totrow=ue_calcular_total_fila_local("txtnumcom");
	f.total.value=totrow;
	if(f.checkall.checked==true)
	{
		for(li_i=1;li_i<=totrow;li_i++)
		{
			eval("f.checkcmp"+li_i+".checked=true");
		}
	}
	else
	{
		for(li_i=1;li_i<=totrow;li_i++)
		{
			eval("f.checkcmp"+li_i+".checked=false");
		}
	}
}

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
	window.open("ayudas/sigesp_ayu_cxp_rep_retencionesaporte.pdf","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}
</script>
</html>