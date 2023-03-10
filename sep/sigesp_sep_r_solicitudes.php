<?php
/***********************************************************************************
* @fecha de modificacion: 15/08/2022, para la version de php 8.1 
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
	require_once("class_folder/class_funciones_sep.php");
	$io_fun_sep=new class_funciones_sep();
	$arrResultado = $io_fun_sep->uf_load_seguridad("SEP","sigesp_sep_r_solicitudes.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];
	
	$ls_reporte=$io_fun_sep->uf_select_config("SEP","REPORTE","REPORTE_SEP","sigesp_sep_rpp_solicitudes.php","C");
	$ls_reporte_excel=$io_fun_sep->uf_select_config("SEP","REPORTE","REPORTE_SEP_EXCEL","sigesp_sep_rpp_solicitudes_excel.php","C");
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Reporte de Ejecucion Presupuestaria </title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funcion_sep.js"></script>

<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="css/sep.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
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
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			
          <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Solicitud 
            de Ejecuci&oacute;n Presupuestaria</td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
			<tr>
			<td height="20" bgcolor="#E7E7E7">&nbsp;</td>
			<td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
		</table>     </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: uf_mostrar_reporte();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0" title="Imprimir"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript:ue_openexcel();"><img src="../shared/imagebank/tools20/excel.jpg" alt="Excel" title="Excel" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0" title="Ayuda"></a></div></td>
    <td class="toolbar" width="25">&nbsp;</td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
</div> 
<p>&nbsp;</p>
<form name="formulario" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_sep->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_sep);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>	

  <table width="578" height="18" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="561" colspan="2" class="titulo-ventana">Reporte de Solicitudes de Ejecuci&oacute;n Presupuestaria </td>
    </tr>
  </table>
  <table width="575" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="573"></td>
    </tr>
    <tr>
      <td colspan="3" align="center"><table width="511" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr style="display:none">
          <td height="22" colspan="4"><div align="left"><strong>Reporte en</strong>
            <select name="cmbbsf" id="cmbbsf">
              <option value="0" selected>Bs.</option>
              <option value="1">Bs.F.</option>
            </select>
          </div></td>
        </tr>
        <tr>
          <td height="22" colspan="4"><strong>Solicitudes </strong></td>
        </tr>
        <tr>
          <td width="70" height="22"><div align="right">Desde</div></td>
          <td width="163"><div align="left">
            <input name="txtnumsoldes" type="text" id="txtnumsoldes" size="20" readonly>
            <a href="javascript: ue_catalogo('sigesp_sep_cat_solicitud.php?tipo=REPDES');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
          <td width="60"><div align="right">Hasta</div></td>
          <td width="216"><div align="left">
            <input name="txtnumsolhas" type="text" id="txtnumsolhas" size="20" readonly>
            <a href="javascript: ue_catalogo('sigesp_sep_cat_solicitud.php?tipo=REPHAS');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center">
        <div align="left"></div></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center"><table width="511" border="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td width="199" height="22"><div align="right">
            <input name="rdproben" type="radio" class="sin-borde" value="radiobutton" onClick="javascript: ue_limpiarproben();" checked>
            Todas</div></td>
          <td width="89" height="22"><div align="center">
                <input name="rdproben" type="radio" class="sin-borde" value="radiobutton" onClick="javascript: ue_limpiarproben();">
            Proveedor</div></td>
          <td width="215" height="22"><div align="left">
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
            <td height="22" colspan="5"><strong>Fecha de Registro </strong></td>
            </tr>
          <tr>
            <td width="136"><div align="right">Desde</div></td>
            <td width="101"><input name="txtfecregdes" type="text" id="txtfecregdes"  onKeyPress="ue_formatofecha(this,'/',patron,true);" size="15" maxlength="10"  datepicker="true"></td>
            <td width="42"><div align="right">Hasta</div></td>
            <td width="129"><div align="left">
                <input name="txtfecreghas" type="text" id="txtfecreghas"  onKeyPress="ue_formatofecha(this,'/',patron,true);" size="15" maxlength="10"  datepicker="true">
            </div></td>
            <td width="101">&nbsp;</td>
          </tr>
        </table>
      </div></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center"><div align="right" class="style1 style14"></div>        <div align="right" class="style1 style14"></div>        <div align="left"></div></td>
    </tr>
    <tr>
      <td colspan="3" align="center"><table width="511" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td height="22" colspan="4"><strong>Unidad Ejecutora</strong></td>
          </tr>
        <tr>
          <td width="76" height="22"><div align="right">Desde</div></td>
          <td width="158" height="22"><div align="left">
            <label>
            <input name="txtcodunides" type="text" id="txtcodunides" size="20" readonly>
            </label>
            <a href="javascript: ue_catalogo('sigesp_sep_cat_unidad_ejecutora.php?tipo=REPDES')"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
          <td width="62" height="22"><div align="right">Hasta</div></td>
          <td width="215"><div align="left">
            <input name="txtcodunihas" type="text" id="txtcodunihas" size="20" readonly>
            <a href="javascript:  ue_catalogo('sigesp_sep_cat_unidad_ejecutora.php?tipo=REPHAS');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
        </tr>

      </table></td>
    </tr>
    <tr>
      <td colspan="3" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" align="center">	  </td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center"><table width="511" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td height="22" colspan="4"><strong>Usuario</strong></td>
        </tr>
        <tr>
          <td width="76" height="22"><div align="right">Desde</div></td>
          <td width="158" height="22"><div align="left">
              <label>
              <input name="txtcodusudes" type="text" id="txtcodusudes" size="20" readonly>
              </label>
          <a href="javascript: ue_catalogo('sigesp_sep_cat_usuarios.php?tipo=REPDES');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
          <td width="62" height="22"><div align="right">Hasta</div></td>
          <td width="215"><div align="left">
              <input name="txtcodusuhas" type="text" id="txtcodusuhas" size="20" readonly>
          <a href="javascript: ue_catalogo('sigesp_sep_cat_usuarios.php?tipo=REPHAS');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a></div></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center"><div align="left" class="style14"></div></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center"><table width="511" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td height="22" colspan="4"><strong>Tipo </strong></td>
          </tr>
        <tr>
          <td width="118" height="22"><div align="right">
                <input name="rdtipsol" type="radio" class="sin-borde" value="radiobutton" checked>
            Todas</div></td>
          <td width="109" height="22"><div align="center">
                <input name="rdtipsol" type="radio" class="sin-borde" value="radiobutton">
            Bienes</div></td>
          <td width="125" height="22"><div align="center">
                <input name="rdtipsol" type="radio" class="sin-borde" value="radiobutton">
            Servicios</div></td>
          <td width="157" height="22"><input name="rdtipsol" type="radio" class="sin-borde" value="radiobutton">
            Conceptos</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center"><table width="511" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td colspan="4"><strong>Estatus</strong></td>
          </tr>
        <tr>
          <td><input name="chkestsol" type="checkbox" class="sin-borde" id="chkestsol" value="1">
            Registrada</td>
          <td><input name="chkestsol" type="checkbox" class="sin-borde" id="chkestsol" value="1">
            Emitida</td>
          <td><input name="chkestsol" type="checkbox" class="sin-borde" id="chkestsol" value="1">
            Contabilizada</td>
          <td><input name="chkestsol" type="checkbox" class="sin-borde" id="chkestsol" value="1">
            Aprobada</td>
        </tr>
        <tr>
          <td><input name="chkestsol" type="checkbox" class="sin-borde" id="chkestsol" value="1">
            Procesada</td>
          <td><input name="chkestsol" type="checkbox" class="sin-borde" id="chkestsol" value="1">
            Anulada</td>
          <td><input name="chkestsol" type="checkbox" class="sin-borde" id="chkestsol" value="1">
            Despachada</td>
          <td><input name="chkestsol" type="checkbox" class="sin-borde" id="chkestsol" value="1">
            Pagada</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" align="center"><div align="center">
        <table width="511" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td width="88" height="22"><strong>Ordenar Por</strong> </td>
            <td width="160" height="22">&nbsp;</td>
            <td width="263" height="22">&nbsp;</td>
          </tr>
          <tr>
            <td height="22">&nbsp;</td>
            <td height="22"><div align="center">
                  <input name="rdorden" type="radio" class="sin-borde" value="radiobutton" checked>
              Codigo de Solicitud </div></td>
            <td height="22"><div align="left">
                  <input name="rdorden" type="radio" class="sin-borde" value="radiobutton">
                  Unidad Ejecutora </div></td>
          </tr>
        </table>
      </div>
      <div align="center"></div></td>
    </tr>
    <tr>
      <td colspan="3" align="center"><div align="left" class="style14">
      <div align="right"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" class="sin-borde">Buscar</a></div>
    </div></td>
    </tr>
  </table>
    <p align="center">
          <input name="total"    type="hidden"  id="total"    value="<?php print $totrow;?>">
          <input name="formato"  type="hidden"  id="formato"  value="<?php print $ls_reporte; ?>">
          <input name="formato_excel"  type="hidden"  id="formato_excel"  value="<?php print $ls_reporte_excel; ?>">
          <input name="selectivo" type="hidden" id="selectivo">
  <div id="solicitudes" align="center"></div>
</p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
var patron = new Array(2,2,4)
var patron2 = new Array(1,3,3,3,3)
	function ue_buscar()
	{
		f=document.formulario;
		li_leer=f.leer.value;
		if (li_leer==1)
		{
			numsoldes= f.txtnumsoldes.value;
			numsolhas= f.txtnumsolhas.value;
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
			codprobenhas=f.txtcoddes.value;
			fegregdes=f.txtfecregdes.value;
			fegreghas=f.txtfecreghas.value;
			codunides=f.txtcodunides.value;
			codunihas=f.txtcodunihas.value;
			codusudes=f.txtcodusudes.value;
			codusuhas=f.txtcodusuhas.value;
			if(f.rdtipsol[3].checked)
			{
				tipsol="C";
			}
			else
			{
				if(f.rdtipsol[2].checked)
				{
					tipsol="S";
				}
				else
				{
					if(f.rdtipsol[1].checked)
					{
						tipsol="B";
					}
					else
					{
						tipsol="";
					}
				}
			}
			if(f.chkestsol[0].checked)
			{registrada=1;}
			else
			{registrada=0;}
			if(f.chkestsol[1].checked)
			{emitida=1;}
			else
			{emitida=0;}
			if(f.chkestsol[2].checked)
			{contabilizada=1;}
			else
			{contabilizada=0;}
			if(f.chkestsol[3].checked)
			{aprobada=1;}
			else
			{aprobada=0;}
			if(f.chkestsol[4].checked)
			{procesada=1;}
			else
			{procesada=0;}
			if(f.chkestsol[5].checked)
			{anulada=1;}
			else
			{anulada=0;}
			if(f.chkestsol[6].checked)
			{despachada=1;}
			else
			{despachada=0;}
			if(f.chkestsol[7].checked)
			{pagada=1;}
			else
			{pagada=0;}
			if((fegregdes!="")&&(fegreghas!=""))
			{
				f.selectivo.value=1;
				// Div donde se van a cargar los resultados
				divgrid = document.getElementById('solicitudes');
				// Instancia del Objeto AJAX
				ajax=objetoAjax();
				// Pagina donde est?n los m?todos para buscar y pintar los resultados
				ajax.open("POST","class_folder/sigesp_sep_c_reportes_ajax.php",true);
				ajax.onreadystatechange=function() {
					if(ajax.readyState==1)
					{
						divgrid.innerHTML = "<img src='imagenes/loading.gif' width='350' height='200'>";//<-- aqui iria la precarga en AJAX 
					}
					else
					{
						if (ajax.readyState==4) {
							divgrid.innerHTML = ajax.responseText
						}
					}
				}
				ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				// Enviar todos los campos a la pagina para que haga el procesamiento
				ajax.send("numsoldes="+numsoldes+"&numsolhas="+numsolhas+"&tipproben="+tipproben+"&codprobendes="+codprobendes+"&codprobenhas="+codprobenhas+"&fegregdes="+fegregdes+"&fegreghas="+fegreghas+"&codunides="+codunides+"&codunihas="+codunihas+"&tipsol="+tipsol+"&registrada="+registrada+"&emitida="+emitida+"&contabilizada="+contabilizada+"&procesada="+procesada+"&anulada="+anulada+"&despachada="+despachada+"&aprobada="+aprobada+"&pagada="+pagada+"&codusudes="+codusudes+"&codusuhas="+codusuhas+"&proceso=SOLICITUDES");

			}
			else
			{
				alert("Debe Indicar las Fechas para el Intervalo de Busqueda");
			}
		}
		else
		{
			alert("No tiene permiso para realizar esta operacion");
		}
	}

	function ue_limpiarproben()
	{
		f=document.formulario;
		f.txtcoddes.value="";
		f.txtcodhas.value="";
	}

	function ue_catalogo(ls_catalogo)
	{
		// abre el catalogo que se paso por parametros
		window.open(ls_catalogo,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=650,height=400,left=50,top=50,location=no,resizable=yes");
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
			ls_catalogo="sigesp_sep_cat_proveedor.php?tipo="+ls_tipo+"";
		}
		if(f.rdproben[2].checked)
		{
			ls_catalogo="sigesp_sep_cat_beneficiario.php?tipo="+ls_tipo+"";
		}
		if(valido)
		{		
			window.open(ls_catalogo,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
		}
		else
		{
			alert("Debe indicar si es Proveedor ? Beneficiario");
		}
	}
	
	function ue_openexcel()
	{
		f=document.formulario;
		li_imprimir=f.imprimir.value;
		ls_solicitudes="";
		if(li_imprimir==1)
		{
			selectivo= f.selectivo.value;
			if(selectivo==1)
			{
				total=ue_calcular_total_fila_local("txtnumsol");
				for (i=1;i<=total;i++)
				{
					if (eval("f.chkimprimir"+i+".checked==true"))
					{
						ls_numsol=eval("f.txtnumsol"+i+".value");
						if (ls_solicitudes.length>0)
						{
							ls_solicitudes=ls_solicitudes+">>"+ls_numsol;
						}
						else
						{
							ls_solicitudes=ls_numsol;
						}
					}
				}
				if(ls_solicitudes=="")
				{
					alert("Debe seleccionar documentos para imprimir !!!");
				}
				else
				{
					tipoformato = "SELECTIVO";
					ls_formato=f.formato_excel.value;
					pantalla    = "reportes/"+ls_formato+"?solicitudes="+ls_solicitudes+"&tipoformato="+tipoformato+"";
					window.open(pantalla,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				}
			}
			else
			{
				numsoldes= f.txtnumsoldes.value;
				numsolhas= f.txtnumsolhas.value;
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
				codprobenhas=f.txtcoddes.value;
				fegregdes=f.txtfecregdes.value;
				fegreghas=f.txtfecreghas.value;
				codunides=f.txtcodunides.value;
				codunihas=f.txtcodunihas.value;
				codusudes=f.txtcodusudes.value;
				codusuhas=f.txtcodusuhas.value;
				if(f.rdtipsol[3].checked)
				{
					tipsol="C";
				}
				else
				{
					if(f.rdtipsol[2].checked)
					{
						tipsol="S";
					}
					else
					{
						if(f.rdtipsol[1].checked)
						{
							tipsol="B";
						}
						else
						{
							tipsol="";
						}
					}
				}
				if(f.chkestsol[0].checked)
				{registrada=1;}
				else
				{registrada=0;}
				if(f.chkestsol[1].checked)
				{emitida=1;}
				else
				{emitida=0;}
				if(f.chkestsol[2].checked)
				{contabilizada=1;}
				else
				{contabilizada=0;}
				if(f.chkestsol[3].checked)
				{aprobada=1;}
				else
				{aprobada=0;}
				if(f.chkestsol[4].checked)
				{procesada=1;}
				else
				{procesada=0;}
				if(f.chkestsol[5].checked)
				{anulada=1;}
				else
				{anulada=0;}
				if(f.chkestsol[6].checked)
				{despachada=1;}
				else
				{despachada=0;}
				if(f.chkestsol[7].checked)
				{pagada=1;}
				else
				{pagada=0;}
				
				if(f.rdorden[0].checked)
				{orden="numsol";}
				else
				{orden="spg_unidadadministrativa.denuniadm";}
				//formato  = "sigesp_sep_rpp_solicitudes_excel.php";
				tipoformato="NORMAL";
				formato=f.formato_excel.value;
				pantalla="reportes/"+formato+"?numsoldes="+numsoldes+"&numsolhas="+numsolhas+"&tipproben="+tipproben+"&codprobendes="+codprobendes+"&codprobenhas="+codprobenhas+"&fegregdes="+fegregdes+"&fegreghas="+fegreghas+"&codunides="+codunides+"&codunihas="+codunihas+"&tipsol="+tipsol+"&registrada="+registrada+"&emitida="+emitida+"&contabilizada="+contabilizada+"&procesada="+procesada+"&anulada="+anulada+"&despachada="+despachada+"&aprobada="+aprobada+"&pagada="+pagada+"&orden="+orden+"&tipoformato="+tipoformato+"&codusudes="+codusudes+"&codusuhas="+codusuhas+"";
				window.open(pantalla,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
			}
		}
		else
		{alert("No tiene permiso para realizar esta operaci?n");}
	}
	
	function uf_mostrar_reporte()
	{
		f=document.formulario;
		li_imprimir=f.imprimir.value;
		ls_solicitudes="";
		if(li_imprimir==1)
		{
			selectivo= f.selectivo.value;
			if(selectivo==1)
			{
				total=ue_calcular_total_fila_local("txtnumsol");
				for (i=1;i<=total;i++)
				{
					if (eval("f.chkimprimir"+i+".checked==true"))
					{
						ls_numsol=eval("f.txtnumsol"+i+".value");
						if (ls_solicitudes.length>0)
						{
							ls_solicitudes=ls_solicitudes+">>"+ls_numsol;
						}
						else
						{
							ls_solicitudes=ls_numsol;
						}
					}
				}
				if(ls_solicitudes=="")
				{
					alert("Debe seleccionar documentos para imprimir !!!");
				}
				else
				{
					tipoformato = "SELECTIVO";
					ls_formato  = f.formato.value;
					pantalla    = "reportes/"+ls_formato+"?solicitudes="+ls_solicitudes+"&tipoformato="+tipoformato+"";
					window.open(pantalla,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				}
			}
			else
			{
				numsoldes= f.txtnumsoldes.value;
				numsolhas= f.txtnumsolhas.value;
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
				codprobenhas=f.txtcoddes.value;
				fegregdes=f.txtfecregdes.value;
				fegreghas=f.txtfecreghas.value;
				codunides=f.txtcodunides.value;
				codunihas=f.txtcodunihas.value;
				codusudes=f.txtcodusudes.value;
				codusuhas=f.txtcodusuhas.value;
				if(f.rdtipsol[3].checked)
				{
					tipsol="C";
				}
				else
				{
					if(f.rdtipsol[2].checked)
					{
						tipsol="S";
					}
					else
					{
						if(f.rdtipsol[1].checked)
						{
							tipsol="B";
						}
						else
						{
							tipsol="";
						}
					}
				}
				if(f.chkestsol[0].checked)
				{registrada=1;}
				else
				{registrada=0;}
				if(f.chkestsol[1].checked)
				{emitida=1;}
				else
				{emitida=0;}
				if(f.chkestsol[2].checked)
				{contabilizada=1;}
				else
				{contabilizada=0;}
				if(f.chkestsol[3].checked)
				{aprobada=1;}
				else
				{aprobada=0;}
				if(f.chkestsol[4].checked)
				{procesada=1;}
				else
				{procesada=0;}
				if(f.chkestsol[5].checked)
				{anulada=1;}
				else
				{anulada=0;}
				if(f.chkestsol[6].checked)
				{despachada=1;}
				else
				{despachada=0;}
				if(f.chkestsol[7].checked)
				{pagada=1;}
				else
				{pagada=0;}
				
				if(f.rdorden[0].checked)
				{orden="numsol";}
				else
				{orden="spg_unidadadministrativa.denuniadm";}
				formato=f.formato.value;
				tipoformato="NORMAL";
				pantalla="reportes/"+formato+"?numsoldes="+numsoldes+"&numsolhas="+numsolhas+"&tipproben="+tipproben+"&codprobendes="+codprobendes+"&codprobenhas="+codprobenhas+"&fegregdes="+fegregdes+"&fegreghas="+fegreghas+"&codunides="+codunides+"&codunihas="+codunihas+"&tipsol="+tipsol+"&registrada="+registrada+"&emitida="+emitida+"&contabilizada="+contabilizada+"&procesada="+procesada+"&anulada="+anulada+"&despachada="+despachada+"&aprobada="+aprobada+"&pagada="+pagada+"&orden="+orden+"&tipoformato="+tipoformato+"&codusudes="+codusudes+"&codusuhas="+codusuhas+"";
				window.open(pantalla,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
			}
		}
		else
		{alert("No tiene permiso para realizar esta operaci?n");}
	}
	
function ue_catausuario(destino)
	{
		window.open("sigesp_sss_cat_usuarios.php?destino="+ destino +"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=120,top=70,location=no,resizable=yes");
	}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
	window.open("ayudas/sigesp_ayu_sep_reporte_solicitudes.pdf","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>