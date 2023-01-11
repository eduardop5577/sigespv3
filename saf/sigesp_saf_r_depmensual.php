<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
require_once("class_funciones_activos.php");
$io_fun_activo=new class_funciones_activos();
$ls_permisos="";
$la_seguridad = Array();
$la_permisos = Array();
$arrResultado = $io_fun_activo->uf_load_seguridad("SAF","sigesp_saf_r_depmensual.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado['as_permisos'];
$la_seguridad=$arrResultado['aa_seguridad'];
$la_permisos=$arrResultado['aa_permisos'];
require_once("sigesp_saf_c_activo.php");
$ls_codemp = $_SESSION["la_empresa"]["codemp"];
$io_saf_tipcat= new sigesp_saf_c_activo();
$ls_rbtipocat=$io_saf_tipcat->uf_select_valor_config($ls_codemp);
$arrResultado = $io_saf_tipcat->uf_load_config("SAF","DEPRECIACION","MODIFICACION_INCORPORACION",$ls_estsudeban);
$ls_estsudeban = $arrResultado['ls_value'];
$lb_existe=$arrResultado['lb_existe'];
$ls_reporte=$io_fun_activo->uf_select_config("SAF","REPORTE","DEPRECIACION MENSUAL","sigesp_saf_rpp_depmensual.php","C");
$ls_reporte_excel=$io_fun_activo->uf_select_config("SAF","REPORTE","DEPRECIACION MENSUAL EXCEL","sigesp_saf_rpp_depmensual_excel.php","C");
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Reporte de Depreciaci&oacute;n Mensual</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="js/stm31.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../shared/js/disabled_keys.js"></script>
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
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr> 
    <td height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40" class="cd-logo"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			  <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Activos Fijos</td>
			    <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
				<tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td> </tr>
	  	</table>
	 </td>
  </tr>
  <tr>
    <?php 
    if ($ls_rbtipocat == 1) 
    {
   ?>
   <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu_csc.js"></script></td>
  <?php 
    }
	elseif ($ls_rbtipocat == 2)
	{
   ?>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu_cgr.js"></script></td>
  <?php 
	}
	else
	{
   ?>
	<td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu.js"></script></td>
  <?php 
	}
   ?>
  </tr>
  <tr>
    <td height="13" colspan="11" bgcolor="#E7E7E7" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:uf_mostrar_reporte();"><img src="../shared/imagebank/tools20/imprimir.gif" title="Imprimir" alt="Imprimir" width="20" height="20" border="0"></a><a href="javascript:uf_open_excel();"><img src="../shared/imagebank/tools20/excel.jpg" alt="Excel" title="Excel" width="20" height="20" border="0"></a><a href="javascript:ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" title="Ayuda"></td>
  </tr>
</table>
</div> 
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_activo->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_activo);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<table width="542" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="111"></td>
    </tr>
    <tr>
      <td height="13" colspan="3" align="center" class="titulo-ventana">Reporte de Depreciaci&oacute;n Mensual</td>
    </tr>
    <tr>
      <td height="48" colspan="3" align="center"><table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr style="visibility:hidden">
          <td><strong>Reporte en</strong>
            <select name="cmbbsf" id="cmbbsf">
              <option value="0" selected>Bs.</option>
              <option value="1">Bs.F.</option>
            </select></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="201"><div align="left"><strong>Mes</strong>
            <select name="cmbmes" id="cmbmes">
              <option value="Enero" selected>Enero</option>
              <option value="Febrero">Febrero</option>
              <option value="Marzo">Marzo</option>
              <option value="Abril">Abril</option>
              <option value="Mayo">Mayo</option>
              <option value="Junio">Junio</option>
              <option value="Julio">Julio</option>
              <option value="Agosto">Agosto</option>
              <option value="Septiembre">Septiembre</option>
              <option value="Octubre">Octubre</option>
              <option value="Noviembre">Noviembre</option>
              <option value="Diciembre">Diciembre</option>
              </select>
          </div></td>
          <td width="103"><div align="left"><strong>A&ntilde;o</strong>
            <select name="cmbanio" id="cmbanio">
			<?php 
				$ls_year=date("Y");
				for($i=1990;$i<2028;$i++)
				{
					if($i!=$ls_year)
					{print"<option value=".$i.">".$i."</option>";}
					else
					{print"<option value=".$i." selected>".$i."</option>";}
				}
			?>
              </select>
          </div></td>
          <td width="196">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="201"><div align="left"><strong>Cuenta Contable</strong></div></td>
          <td width="500" height="22"><div align="left">
           <input name="txtctacon1" type="text" id="txtctacon1" size="21" maxlength="20"  style="text-align:center ">
           <a href="javascript:uf_catalogo_ctasscg('txtctacon1','txtdenctacon1');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a> 
              <input name="txtdenctacon1" type="text" class="sin-borde" id="txtdenctacon1" size="30" readonly>
       		</div>
           <div align="right"> </div></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center"><div align="right" class="style1 style14"></div>        <div align="right" class="style1 style14"></div>        <div align="left">
          <input name="hidunidad" type="hidden" id="hidunidad">
          <input name="hidstatus" type="hidden" id="hidstatus">
          <input name="estsudeban" type="hidden" id="estsudeban" value="<?php print $ls_estsudeban; ?>">
          <div align="right"></div>
      </div></td>
    </tr>
	<?php 
		if($ls_estsudeban==1)
		{
	?>
    <tr>
      <td height="22" colspan="3" align="center"><table width="500" height="66" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td width="126" height="22"><div align="left"><strong>Otros</strong></div></td>
          <td width="368" height="22">&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Categor&iacute;a SUDEBAN </div></td>
          <td height="22"><input name="txtcodsudeban" type="text" id="txtcodsudeban" onKeyUp="ue_validarcomillas(this);" size="5" readonly>
            <a href="javascript: ue_catalogo_sudeban();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdensudeban" type="text" class="sin-borde" id="txtdensudeban" size="50" readOnly="true"></td>
        </tr>
        <tr>
          <td height="22"><div align="right">
            <input name="chknodep" type="checkbox" class="sin-borde" id="chknodep" value="1">
          </div></td>
          <td height="22">Mostrar Activos Incorporados no Depreciados </td>
        </tr>
      </table></td>
    </tr>
	<?php
		}
	?>
    <tr>
      <td colspan="3" align="center"><div align="left">
        <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td height="22" colspan="2"><span class="style14"><strong>Ordenado Por</strong></span></td>
          </tr>
          <tr>
            <td width="175"><div align="center"><strong>Activo</strong></div></td>
            <td height="22">&nbsp;</td>
          </tr>
          <tr>
            <td height="22"><div align="right">C&oacute;digo
                    <input name="radioordenact" type="radio" class="sin-borde" value="radiobutton" checked>
              </div></td>
            <td width="333" height="22">&nbsp;</td>
          </tr>
          <tr>
            <td height="22"><div align="right">Denominaci&oacute;n
                    <input name="radioordenact" type="radio" class="sin-borde" value="radiobutton">
              </div></td>
            <td height="22">&nbsp;</td>
          </tr>
        </table>
      </div></td>
    </tr>
    
    <tr>
      <td colspan="3" align="center">
        <div align="center">
          <p></p>
      </div></td>
    </tr>
  </table>
  <p align="center">&nbsp;</p>
  <p align="center">
	<input name="total" type="hidden" id="total" value="<?php print $totrow;?>">
	<input name="formato"    type="hidden" id="formato"    value="<?php print $ls_reporte; ?>">
  </p>
  <p align="center">
	<input name="total" type="hidden" id="total" value="<?php print $totrow;?>">
	<input name="formato2"    type="hidden" id="formato2"    value="<?php print $ls_reporte_excel; ?>">
  </p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function uf_mostrar_reporte()
{
	f=document.form1; 
	li_imprimir=f.imprimir.value;
	formato=f.formato.value;
	if(li_imprimir==1)
	{
		ls_mes= f.cmbmes.value;
		ls_anio= f.cmbanio.value;
		tipoformato = f.cmbbsf.value;
		soloincorporados=0;
		codcatsudeban='';
		if(f.radioordenact[0].checked)
		{
			li_ordenact=0;
		}
		else
		{
			li_ordenact=1;
		}
		if(f.estsudeban.value==1)
		{
			codcatsudeban=f.txtcodsudeban.value;
			if(f.chknodep.checked)
			{
				soloincorporados=1;
			}
		}
		ls_cuecon = f.txtctacon1.value;
		window.open("reportes/"+formato+"?ordenact="+li_ordenact+"&mes="+ls_mes+"&anio="+ls_anio+"&tipoformato="+tipoformato+"&codcatsudeban="+codcatsudeban+"&soloincorporados="+soloincorporados+"&cuecon="+ls_cuecon,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");	
	}
	else
	{alert("No tiene permiso para realizar esta operación");}
}

function uf_open_excel()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	formato=f.formato2.value;
	if(li_imprimir==1)
	{
		ls_mes= f.cmbmes.value;
		ls_anio= f.cmbanio.value;
		tipoformato = f.cmbbsf.value;
		soloincorporados=0;
		codcatsudeban='';
		if(f.radioordenact[0].checked)
		{
			li_ordenact=0;
		}
		else
		{
			li_ordenact=1;
		}
		if(f.estsudeban.value==1)
		{
			codcatsudeban=f.txtcodsudeban.value;
			if(f.chknodep.checked)
			{
				soloincorporados=1;
			}
		}
		ls_cuecon = f.txtctacon1.value;
		window.open("reportes/"+formato+"?ordenact="+li_ordenact+"&mes="+ls_mes+"&anio="+ls_anio+"&tipoformato="+tipoformato+"&codcatsudeban="+codcatsudeban+"&soloincorporados="+soloincorporados+"&cuecon="+ls_cuecon,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
	}
	else
	{alert("No tiene permiso para realizar esta operación");}
}

function ue_catalogo_sudeban()
{
	tipo="activos";
    window.open("sigesp_saf_cat_catsudeban.php?tipo="+tipo,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}
	

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

//--------------------------------------------------------
//	Función que obtiene el valor de el radio button
//--------------------------------------------------------
function actualizaValor(oRad)
{ 
	var i 
	f=document.form1;
	for (i=0;i<f.radiostatus.length;i++)
	{ 
	   if (f.radiostatus[i].checked) 
		  break; 
	} 
	valor= i;
	f.hidradio.value=i;
} 
//--------------------------------------------------------
//Función que llama al catalogo de cuenta contable
//--------------------------------------------------------
function uf_catalogo_ctasscg(ls_coddestino,ls_dendestino)
{
window.open("sigesp_cat_ctasscg.php?coddestino="+ ls_coddestino +"&dendestino="+ ls_dendestino +"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=580,height=400,left=120,top=70,location=no,resizable=yes");
}
//--------------------------------------------------------
//	Función que da formato a la fecha colocando los separadores (/).
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

//--------------------------------------------------------
//	Función que valida que un intervalo de tiempo sea valido
//--------------------------------------------------------
function ue_comparar_intervalo()
{ 

	f=document.form1;
	ld_desde="f.txtdesde";
	ld_hasta="f.txthasta";
	var valido = false; 
	var diad = f.txtdesde.value.substr(0, 2); 
	var mesd = f.txtdesde.value.substr(3, 2); 
	var anod = f.txtdesde.value.substr(6, 4); 
	var diah = f.txthasta.value.substr(0, 2); 
	var mesh = f.txthasta.value.substr(3, 2); 
	var anoh = f.txthasta.value.substr(6, 4); 
	
	if (anod < anoh)
	{
		 valido = true; 
	 }
	else 
	{ 
	 if (anod == anoh)
	 { 
	  if (mesd < mesh)
	  {
	   valido = true; 
	  }
	  else 
	  { 
	   if (mesd == mesh)
	   {
		if (diad <= diah)
		{
		 valido = true; 
		}
	   }
	  } 
	 } 
	} 
	if (valido==false)
	{
		alert("El rango de fecha es invalido");
		f.txtdesde.value="";
		f.txthasta.value="";
		
	} 
	return valido;
} 
</script>
</html>