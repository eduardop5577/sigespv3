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
if (!array_key_exists("la_logusr",$_SESSION))
   {
	 print "<script language=JavaScript>";
	 print "window.close();";
	 print "</script>";		
   }
$ls_logusr = $_SESSION["la_logusr"];
$ls_permisos="";
$la_seguridad=array();
$la_permisos=array();
require_once("class_folder/class_funciones_cxp.php");
$io_fun_cxp=new class_funciones_cxp();
$arrResultado=$io_fun_cxp->uf_load_seguridad("CXP","sigesp_cxp_p_conf_acta.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado["as_permisos"];
$la_seguridad=$arrResultado["aa_seguridad"];
$la_permisos=$arrResultado["aa_permisos"];
unset($arrResultado);
   //--------------------------------------------------------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 19/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $io_fun_cxp,$ls_estatus,$ls_operacion,$ls_codigo,$ls_nombre,$ls_nomrtf,$ls_encabezado,$ls_cuerpo,$ls_pie,$ls_existe;
		
		$ls_operacion=$io_fun_cxp->uf_obteneroperacion();
		$ls_codigo="";
		$ls_nombre="";
		$ls_nomrtf="";
		$ls_encabezado="";
		$ls_cuerpo="";
		$ls_pie="";
		$ls_status="";		
		$ls_existe="FALSE";		

   }
   //--------------------------------------------------------------------------------------------------------------

   //--------------------------------------------------------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 23/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $io_fun_cxp,$ls_estatus,$ls_operacion,$ls_codigo,$ls_nombre,$ls_nomrtf,$ls_encabezado,$ls_cuerpo,$ls_pie,$ls_existe;
		

		$ls_existe=$io_fun_cxp->uf_obtenervalor("existe","");
		$ls_estatus=$io_fun_cxp->uf_obtenervalor("hidstatus","");
		$ls_operacion=$io_fun_cxp->uf_obtenervalor("operacion","");
		$ls_codigo=$io_fun_cxp->uf_obtenervalor("txtcodigo","");
		$ls_nombre=$io_fun_cxp->uf_obtenervalor("txtnombre","");
		$ls_nomrtf=$io_fun_cxp->uf_obtenervalor("txtnomrtf","");
		$ls_encabezado=$io_fun_cxp->uf_obtenervalor("txtencabezado","");
		$ls_cuerpo=$io_fun_cxp->uf_obtenervalor("txtcuerpo","");
		$ls_pie=$io_fun_cxp->uf_obtenervalor("txtpie","");


   }
   //--------------------------------------------------------------------------------------------------------------

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Configuracion Acta de Responsabilidad Social</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="../shared/js/valida_tecla.js"></script>
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

<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
</head>

<body>

<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="807" height="40"></td>
  </tr>
  <tr>
  <td width="778" height="20" colspan="11" bgcolor="#E7E7E7">
    <table width="778" border="0" align="center" cellpadding="0" cellspacing="0">			
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Caja y Banco</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table>
  </td>
  </tr>
  <tr>
    <td height="20" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" class="toolbar"><div align="left"><a href="javascript: ue_nuevo();"><img  src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" title="Nuevo" width="20" height="20" border="0"></a><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Guardar" title="Guardar" width="20" height="20" border="0"></a><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" title="Buscar" width="20" height="20" border="0"></a>
	<a href="javascript:ue_imprimir()"></a>
	<a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" title="Eliminar" width="20" height="20" border="0"></a><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a></div>      
    <div align="center"></div>      <div align="center"></div>      <div align="center"></div>      <div align="center"></div></td>
  </tr>
</table>
<?php

	require_once("class_folder/sigesp_cxp_c_conf_acta.php");
	$io_cxp=new sigesp_cxp_c_conf_acta("../");
	uf_limpiarvariables();
	
	switch($ls_operacion)
	{
		case "NUEVO":
			require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
			$io_keygen= new sigesp_c_generar_consecutivo();
			$ls_codigo= $io_keygen->uf_generar_numero_nuevo("CXP","cxp_confacta","codigo","CXPSOP",3,"","","");
			if($ls_codigo===false)
			{
				print "<script language=JavaScript>";
				print "location.href='sigespwindow_blank.php'";
				print "</script>";		
			}
			unset($io_keygen);
			break;
		case "GUARDAR":
			uf_load_variables();

			$ls_archrtf=$_FILES['txtarchrtf']['name'];
			if(strlen($ls_archrtf)>50)
			{
				$in_classconfig->io_msg->message("La Longitud del Nombre del Archivo es mayor a 50 caracteres."); 
				$lb_valido=false;
			} 
			if($ls_archrtf!="")
			{
				$ls_tiparc=$_FILES['txtarchrtf']['type']; 
				$ls_tamarc=$_FILES['txtarchrtf']['size']; 
				$ls_nomtemarc=$_FILES['txtarchrtf']['tmp_name'];
				$ls_archrtf=$io_cxp->uf_upload($ls_archrtf,$ls_tiparc,$ls_tamarc,$ls_nomtemarc);
			}

			$arrResultado=$io_cxp->uf_guardar($ls_existe,$ls_codemp,$ls_codigo,$ls_nombre,$ls_encabezado,$ls_cuerpo,$ls_pie,$ls_status,$ls_archrtf,$la_seguridad);
			$lb_valido=$arrResultado["lb_valido"];
			$ls_codigo=$arrResultado["codigo"];
			break;
		case "ELIMINAR":
			uf_load_variables();
			$lb_valido=$io_cxp->uf_delete_conf_acta($ls_codigo,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
			}
			break;
	}	

?>
<p>&nbsp;</p>
<div align="center">
  <table width="677" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td width="675" height="410" valign="top">
<form name="form1" method="post" enctype="multipart/form-data" action="">
<p>
<?php 
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_cxp->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_cxp);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
</p><br>
		<table width="647" height="335" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
              <tr class="titulo-ventana">
                <td colspan="3">Configuracion Acta de Responsabilidad Social</td>
              </tr>
              <tr class="formato-blanco">
                <td width="184" height="23"><div align="right">C&oacute;digo</div></td>
                <td width="461" colspan="2"><label>
                  <div align="left">
                    <input name="txtcodigo" type="text" id="txtcodigo" value="<?php print $ls_codigo?>" size="3" maxlength="3" style="text-align:center" readonly="true">
                  </div>
                </label></td>
              </tr>
              
              <tr class="formato-blanco">
                <td height="22"><div align="right">Nombre</div></td>
                <td colspan="2"><label>
                  <div align="left">
                    <input name="txtnombre" type="text" id="txtnombre" value="<?php print $ls_nombre;?>" size="60" maxlength="50">
                  </div>
                </label></td>
              </tr>
			  
            
              <tr class="formato-blanco">
                <td height="20"><div align="right">Platilla RTF </div></td>
                <td colspan="2">
                  <div align="left">
                    <input name="txtnomrtf" type="text" id="txtnomrtf" size="60" maxlength="50" value="<?php print $ls_nomrtf;?>" readonly>
                  </div></td>
              </tr>
              <tr class="formato-blanco">
                <td height="20"><div align="right">Actualizar Plantilla RTF </div></td>
                <td colspan="2"><div align="left">
                  <input name="txtarchrtf" type="file" id="txtarchrtf" size="60" maxlength="50">
                </div></td>
              </tr>
              <tr class="formato-blanco">
                <td height="20">&nbsp;</td>
                <td colspan="2">El nombre del Archivo no debe contener espacios en Blanco. </td>
              </tr>
              <tr class="formato-blanco">
                <td height="255" colspan="3"><div align="center"></div>
                  <table width="615" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">

                  <tr class="formato-blanco">
                    <td width="142" height="153" rowspan="4"><label>
                      <select name="lista" size="11" >
                        <option value="@empresa@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Nombre de la Empresa</option>
                        <option value="@ciudad@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Ciudad</option>
						<option value="@dia@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Dia</option>
						<option value="@mes@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Mes</option>
						<option value="@ano@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Año</option>
						<option value="@hora@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Hora</option>
                        <option value="@representante@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Representante Legal:</option>
                        <option value="@cedularep@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Cédula Representante Legal</option>
                        <option value="@cargorep@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Cargo Representante Legal</option>
                        <option value="@proveedor@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Proveedor</option>
                        <option value="@compromiso@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Compromiso</option>						
                        <option value="@conceptocomp@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Concepto Compromiso</option>						
                        <option value="@tipocontra@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Tipo de Contratación</option>
                        <option value="@codigocont@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Codigo de Contratación</option>
                        <option value="@condpago@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Condición de Pago</option>
                        <option value="@baseimponible@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Base Imponible</option>
                        <option value="@montoretenido@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Impuesto al valor agregado</option>						
                      	<option value="@montototal@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Monto Total</option>
						<option value="@aporteletras@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Aporte Social (Letras)</option>
					  	<option value="@aportesocial@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Aporte Social</option>
					  	<option value="@dirproveedor@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Direccion del Provedor</option>
					  	<option value="@biletras@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Base Imponible Letras</option>
					  	<option value="@numrecdoc@" onDblClick="javascript:uf_pasar('','selStart','selEnd');">Numero de Factura</option>
					  </select>
                    </label></td>
                    <td width="39"><label></label></td>
                    <td width="80" height="34"><label></label></td>
                    <td width="128"><div align="right"><a href="javascript:uf_formato('b');"><img src="../shared/imagenes/bold.gif" width="23" height="22"  border="0"></a></div></td>
                    <td width="114"><a href="javascript:uf_formato('u')"><img src="../shared/imagenes/underline.gif" width="23" height="22" border="0"></a></td>
                    <td width="110">&nbsp;</td>
                  </tr>
                  <tr class="formato-blanco">
                    <td width="39"><input name="button3" type="button" onClick="javascript:uf_pasar(document.form1.txtencabezado,'selStart','selEnd');" value=">>"></td>
                    <td height="91" colspan="4">
                      <div align="center">
                        <textarea name="txtencabezado" cols="77" rows="5" wrap="physical" id="txtencabezado" onMouseUp="inputKey(this, event,'2850','selStart','selEnd')" onKeyUp="inputKey(this, event,'2850','selStart','selEnd')" onFocus="javascript:uf_setfocus('txtencabezado');"><?php print $ls_encabezado;?></textarea>
                      </div></td></tr>
                  <tr class="formato-blanco">
                    <td width="39"><label>
                      <input name="button" type="button" value=">>" onClick="javascript:uf_pasar(document.form1.txtcuerpo,'selStart2','selEnd2');">
                    </label></td>
                    <td height="100" colspan="4" align="center" valign="top"><label>

                      <div align="center">
                            <p>
                              <textarea name="txtcuerpo" cols="77" rows="5" id="txtcuerpo" onmouseup="inputKey(this, event,'2868','selStart2','selEnd2')" onkeyup="inputKey(this, event,'2868','selStart2','selEnd2')" onFocus="javascript:uf_setfocus('txtcuerpo');"><?php print $ls_cuerpo;?></textarea>
                            </p>
                            <p>Detalle de Carta Orden </p>
                      </div>
                    </label></td></tr>
                  <tr class="formato-blanco">
                    <td width="39"><label>
                      <input name="button2" type="button" value=">>" onClick="javascript:uf_pasar(document.form1.txtpie,'selStart3','selEnd3');">
                    </label></td>
                    <td height="99" colspan="4"><label>

                      <div align="center">
                            <textarea name="txtpie" cols="77" rows="5" id="txtpie" onmouseup="inputKey(this, event,'2886','selStart3','selEnd3')" onkeyup="inputKey(this, event,'2886','selStart3','selEnd3')" onFocus="javascript:uf_setfocus('txtpie');"><?php print $ls_pie;?></textarea>
                      </div>
                        </label></td></tr>
                </table></td>
              </tr>
          </table>
            <p><input name="operacion" type="hidden" id="operacion">
               <input name="hidstatus" type="hidden" id="hidstatus" value="<?php print $ls_status;?>">
               <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
			   <input name="selStart" type="hidden" id="selStart">
			   <input name="selEnd" type="hidden" id="selEnd">
			   <input name="selStart2" type="hidden" id="selStart2">
			   <input name="selEnd2" type="hidden" id="selEnd2">
			   <input name="selStart3" type="hidden" id="selStart3">
			   <input name="selEnd3" type="hidden" id="selEnd3" >
			   <input type="hidden" name="hidfocus" id="hidfocus"></p>
			  
        </form></td>
      </tr>
  </table>
</div>
</body>
<script >

function ue_guardar()
{
	f=document.form1;
    ls_nombre=f.txtnombre.value;
	if (ls_nombre!="")
	{
		f.operacion.value ="GUARDAR";
	}
	else
	{
		alert("Por favor coloque un nombre valido al archivo a guardar!");
	}
    f.submit();
}

function ue_cerrar()
{
	f=document.form1;
	f.action="sigespwindow_blank.php";
	f.submit();
}

function uf_pasar(txt,start,end)
{
	f=document.form1;
	ls_seleccionado=f.lista.value;
	if(txt=="")
	{
		cajita=f.hidfocus.value;
		txt=document.getElementById(cajita);
		if(cajita=="txtencabezado")
			start="selStart";
		else if(cajita=="txtcuerpo")
			start="selStart2";
		else if(cajita=="txtpie")
			start="selStart3";
	}
	//alert(txt.value);		
	if(navigator.appName=="Netscape")
	{
		ls_cadena1=txt.value.slice(0,document.getElementById(start).value);
		ls_cadena2=txt.value.slice(document.getElementById(start).value,txt.value.length);
		ls_cadena=ls_cadena1+ls_seleccionado+ls_cadena2;
		txt.value=ls_cadena;
	}
	else
	{
		txt.value=txt.value+" "+ls_seleccionado;
	}
}

function ue_nuevo()
{
	location.href="sigesp_cxp_p_conf_acta.php";
}

function ue_buscar()
{
	window.open("sigesp_cxp_cat_conf_acta.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=600,height=300,left=30,top=30,location=no,resizable=yes");
}

function ue_eliminar()
{
	f=document.form1;
	if(confirm("Esta seguro de eliminar el registro? \n Esta operación no puede ser reversada"))
	{	
		f.operacion.value="ELIMINAR";
		f.submit();
	}
}
function uf_setfocus(cajita)
{
	document.form1.hidfocus.value=cajita;
}

function uf_formato(tipo)
{
	f=document.form1;
	if(tipo=='b')
	{
		ls_cadena_inicio="<b>";
		ls_cadena_fin="</b>";
	}
	else
	{
		ls_cadena_inicio="<u>";
		ls_cadena_fin="</u>";
	}	
	if(navigator.appName=="Netscape")
	{
			
		cajita=f.hidfocus.value;
		txt=document.getElementById(cajita);
		if(cajita=="txtencabezado")
		{
			start="selStart";
			end="selEnd";
		}
		else if(cajita=="txtcuerpo")
		{
			start="selStart2";
			end="selEnd2";
		}
		else if(cajita=="txtpie")
		{
			start="selStart3";
			end="selEnd3";
		}	
		
		ls_cadena1=txt.value.slice(0,document.getElementById(start).value);
		ls_cadena2=txt.value.slice(document.getElementById(start).value,document.getElementById(end).value);
		ls_cadena3=txt.value.slice(document.getElementById(end).value,txt.value.length);
		ls_cadena=ls_cadena1+ls_cadena_inicio+ls_cadena2+ls_cadena_fin+ls_cadena3;
		txt.value=ls_cadena;
	}
	else
	{
		txt.value=txt.value+ls_cadena_inicio+ls_cadena_fin;
	}
}

function ue_imprimir()
{
	f=document.form1;
	ls_codigo=f.txtcodigo.value;
	ls_opener="conf";
	window.open("reportes/sigesp_scb_rpp_cartaorden_pdf.php?codigo="+ls_codigo+"&opener="+ls_opener,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=700,height=600,left=0,top=0,location=no,resizable=yes");
}

//----------------------------------Funciones para obtener la posicion del cursor--------------------//
var is_gecko = /gecko/i.test(navigator.userAgent);
var is_ie    = /MSIE/.test(navigator.userAgent);

function setSelectionRange(input, start, end) {
	input.focus();
	if (is_gecko) {
		input.setSelectionRange(start, end);
	} else {
		// assumed IE
		var range = input.createTextRange();
		range.collapse(true);
		range.moveStart("character", start);
		range.moveEnd("character", end - start);
		range.select();
	}
};

function getSelectionStart(input,valor) {
	input.focus();
	if (is_gecko)
		return input.selectionStart;
	var range = document.selection.createRange();
	//alert(range.text);
	var isCollapsed = range.compareEndPoints("StartToEnd", range) == 0;
	if (!isCollapsed)
		range.collapse(true);
	var b = range.getBookmark();
	//alert((b.charCodeAt(2) - parseInt(valor)));
	return b.charCodeAt(2) - parseInt(valor);
};

function getSelectionEnd(input,valor) {
	input.focus();
	if (is_gecko)
		return input.selectionEnd;
	var range = document.selection.createRange();
	var isCollapsed = range.compareEndPoints("StartToEnd", range) == 0;
	if (!isCollapsed)
		range.collapse(false);
	var b = range.getBookmark();
	return b.charCodeAt(2) - parseInt(valor);
};

function inputKey(input,ev,valor,start,end) {
//setTimeout(function() {
if(navigator.appName=="Netscape")
{
  document.getElementById(start).value = getSelectionStart(input,valor);
  document.getElementById(end).value = getSelectionEnd(input,valor);
}
else
{
  document.getElementById(start).value = 0;
  document.getElementById(end).value = 0;
}

//}, 20);
}
function doSelect() {
var start = document.getElementById("selStart").value;
var end = document.getElementById("selEnd").value;
var input = document.getElementById("testfield");
input.focus();
setSelectionRange(input, start, end);
}

</script>
</html>