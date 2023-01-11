<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
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
	$ls_codnom=$_SESSION["la_nomina"]["codnom"];
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_permisos="";
	$la_seguridad = Array();
	$la_permisos = Array();	
	$arrResultado=$io_fun_nomina->uf_load_seguridad_nomina("SNO","sigesp_sno_r_hlistadohojatiempo.php",$ls_codnom,$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	require_once("sigesp_sno.php");
	$io_sno=new sigesp_sno();
	$lb_valido=$io_sno->uf_crear_sessionnomina();		
	$ls_codnom=$_SESSION["la_nomina"]["codnom"];
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$ls_desper=$_SESSION["la_nomina"]["descripcionperiodo"];
	$ls_reporte=$io_sno->uf_select_config("SNO","REPORTE","LISTADO_HOJATIEMPO","sigesp_sno_rpp_listadohojatiempo.php","C");
	unset($io_sno);
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();		
	if($_SESSION["la_nomina"]["hojtienom"]=="0")
	{
		print("<script language=JavaScript>");
		print(" alert('Esta definición esta desactiva para nóminas que no utilizan Hoja de Tiempo.');");
		print(" location.href='sigespwindow_blank_hnomina.php'");
		print("</script>");
	}	
	$ld_fechas=$_SESSION["la_nomina"]["fecdesper"];
	if(substr($ld_fechas,5,2)=='01')
	{
		$li_anio=substr($ld_fechas,0,4)-1;
		$ld_fecdes=$li_anio."-12-01";
	}
	else
	{
		$li_mes=str_pad(substr($ld_fechas,5,2)-1,2,"0",0);
		$ld_fecdes=substr($ld_fechas,0,4)."-".$li_mes."-01";
	}
	$ld_fecdes=$io_funciones->uf_convertirfecmostrar($ld_fecdes);
	$ld_fechas=$io_funciones->uf_convertirfecmostrar($ld_fechas);
	unset($io_funciones);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title >Reporte Listado de Hoja de Tiempo</title>
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
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema"><?php print $ls_desnom;?></td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><?php print $ls_desper;?></span></div></td>
			 <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
	  </table>
	</td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu_hnomina.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript:ue_print();"><img src="../shared/imagebank/tools20/imprimir.gif" title='Imprimir' alt="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title='Salir' alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title='Ayuda' alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>

<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank_nomina.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="550" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
            <td height="20" colspan="4" class="titulo-ventana">Reporte de Listado de Hoja de Tiempo </td>
        </tr>
        <tr>
          <td height="20" colspan="4" class="titulo-celdanew">Intervalo de Horario </td>
        </tr>
        <tr>
          <td width="143" height="22"><div align="right"> Desde </div></td>
          <td width="127"><div align="left">
            <input name="txtcodhordes" type="text" id="txtcodhordes" size="13" maxlength="10" value="<?php print $ls_codhordes;?>" readonly>
            <a href="javascript: ue_buscarhorariodesde();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td width="96"><div align="right">Hasta </div></td>
          <td width="124"><div align="left">
            <input name="txtcodhorhas" type="text" id="txtcodhorhas" value="<?php print $ls_codhorhas;?>" size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarhorariohasta();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="20" colspan="4" class="titulo-celdanew">Intervalo de Personal </td>
          </tr>
        <tr>
          <td width="143" height="22"><div align="right"> Desde </div></td>
          <td width="127"><div align="left">
            <input name="txtcodperdes" type="text" id="txtcodperdes" size="13" maxlength="10" value="<?php print $ls_codperdes;?>" readonly>
            <a href="javascript: ue_buscarpersonaldesde();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td width="96"><div align="right">Hasta </div></td>
          <td width="124"><div align="left">
            <input name="txtcodperhas" type="text" id="txtcodperhas" value="<?php print $ls_codperhas;?>" size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarpersonalhasta();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="20" colspan="4" class="titulo-celdanew">&nbsp;</td>
          </tr>
        <tr>
          <td height="22"><div align="right">Fecha Desde</div></td>
          <td><div align="left">
            <input name="txtfecdes" type="text" id="txtfecdes" value="<?php print $ld_fecdes;?>" size="12" maxlength="10" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="ue_validar_formatofecha(this);" datepicker=true>
          </div></td>
          <td><div align="right">Fecha Hasta</div></td>
          <td><div align="left">
            <input name="txtfechas" type="text" id="txtfechas" value="<?php print $ld_fechas;?>" size="12" maxlength="10" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="ue_validar_formatofecha(this);" datepicker=true>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Estatus</div></td>
          <td colspan="3"><div align="left">
            <label>
                  <select name="cmbestatus" id="cmbestatus">
                    <option value="" selected >--Seleccione--</option>
                    <option value="0" >No Aprobado</option>
                    <option value="1" >Aprobado</option>
                  </select>
            </label>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Unidad Administrativa </div></td>
          <td colspan="3"><div align="left">
            <input name="txtcoduniadm" type="text" id="txtcoduniadm" size="19" maxlength="16" readonly>
            <a href="javascript: ue_buscaruniadm();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
			<input name="txtdenuniadm" type="text" class="sin-borde" id="txtdenuniadm" size="40" maxlength="30" readonly></div></td>
          </tr>
        <tr class="titulo-celdanew">
          <td height="20" colspan="4"><div align="right" class="titulo-celdanew">Ordenado por </div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo del Personal </div></td>
          <td colspan="3"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="1" checked>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Apellido del Personal </div></td>
          <td colspan="3"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="2">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Nombre del Personal </div></td>
          <td colspan="3"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="3">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">C&eacute;dula del Personal </div></td>
          <td colspan="3"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="4">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right"></div></td>
          <td colspan="3"><div align="right">
            <input name="operacion" type="hidden" id="operacion" value="">
            <input name="reporte" type="hidden" id="reporte" value="<?php print $ls_reporte;?>">
		  </div></td>
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
<p>&nbsp;</p>
</body>
<script >
function ue_cerrar()
{
	location.href = "sigespwindow_blank_hnomina.php";
}

function ue_print()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		reporte=f.reporte.value;
		codhordes=f.txtcodhordes.value;
		codhorhas=f.txtcodhorhas.value;
		tiporeporte="0";
		if(codhordes<=codhorhas)
		{
			codperdes=f.txtcodperdes.value;
			codperhas=f.txtcodperhas.value;
			if(codperdes<=codperhas)
			{		
				coduniadm=f.txtcoduniadm.value;
				denuniadm=f.txtdenuniadm.value;
				fechas=f.txtfechas.value;
				fecdes=f.txtfecdes.value;
				esthojtie=f.cmbestatus.value;
				if(f.rdborden[0].checked)
				{
					orden="1";
				}
				if(f.rdborden[1].checked)
				{
					orden="2";
				}
				if(f.rdborden[2].checked)
				{
					orden="3";
				}
				if(f.rdborden[3].checked)
				{
					orden="4";
				}
				pagina="reportes/"+reporte+"?codhordes="+codhordes+"&codhorhas="+codhorhas+"&codperdes="+codperdes;
				pagina=pagina+"&codperhas="+codperhas+"&fecdes="+fecdes+"&fechas="+fechas+"&esthojtie="+esthojtie+"&coduniadm="+coduniadm+"&orden="+orden;
				pagina=pagina+"&denuniadm="+denuniadm+"&tiporeporte="+tiporeporte;
				window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
			}
			else
			{
				alert("El rango del personal está erroneo");
			}
		}
		else
		{
			alert("El rango del Turno está erroneo");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}		
}

function ue_buscarhorariodesde()
{
	window.open('sigesp_snorh_cat_horario.php?tipo=lishojtiedes','catalogo','menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no');
}

function ue_buscarhorariohasta()
{
	window.open('sigesp_snorh_cat_horario.php?tipo=lishojtiehas','catalogo','menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no');
}

function ue_buscarpersonaldesde()
{
	window.open("sigesp_sno_cat_hpersonalnomina.php?tipo=reppagnomdes","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarpersonalhasta()
{
	window.open("sigesp_sno_cat_hpersonalnomina.php?tipo=reppagnomhas","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscaruniadm()
{
	window.open("sigesp_snorh_cat_uni_ad.php?tipo=replisconc","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}
</script> 
</html>