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
$li_periodo=$_SESSION["la_empresa"]["periodo"];
$li_desde = $li_periodo-5;
$li_hasta = $li_periodo+1;
require_once("class_funciones_inventario.php");
$io_fun_activo=new class_funciones_inventario();
$ls_permisos = "";
$la_seguridad = Array();
$la_permisos = Array();
$arrResultado = $io_fun_activo->uf_load_seguridad("SIV","sigesp_siv_r_articuloxalmacen_mensual.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos = $arrResultado['as_permisos'];
$la_seguridad = $arrResultado['aa_seguridad'];
$la_permisos = $arrResultado['aa_permisos'];
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Reporte de Existencias de Articulos Mensual</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
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
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr> 
    <td height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40" class="cd-logo"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			  <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Inventario</td>
			    <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
				<tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td> </tr>
	  	</table>
	 </td>
  </tr>
  <tr>
  <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="11" bgcolor="#E7E7E7" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:uf_mostrar_reporte();"><img src="../shared/imagebank/tools20/imprimir.gif" title="Imprimir" alt="Imprimir" width="20" height="20" border="0"></a><a href="javascript:uf_open_excel();"></a><a href="javascript:ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" title="Ayuda"></td>
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
<table width="650" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="648"></td>
    </tr>
    <tr>
      <td height="13" colspan="3" align="center" class="titulo-ventana">Reporte de Existencias de Articulos Mensual</td>
    </tr>
    <tr>
      <td height="48" colspan="3" align="center"><table width="537" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
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
          <td width="168"><div align="left"><strong>Mes Desde </strong>
            <select name="cmbmesd" id="cmbmesd">
              	<option value="01" selected>Enero</option>
				<option value="02">Febrero</option>
				<option value="03">Marzo</option>
				<option value="04">Abril</option>
				<option value="05">Mayo</option>
				<option value="06">Junio</option>
				<option value="07">Julio</option>
				<option value="08">Agosto</option>
				<option value="09">Septiembre</option>
				<option value="10">Octubre</option>
				<option value="11">Noviembre</option>
				<option value="12">Diciembre</option>
            </select>
          </div></td>
          <td width="122"><div align="left"><strong>A&ntilde;o Desde </strong>
            <select name="cmbaniod" id="cmbaniod">
                <?php 
				$ls_year=date("Y");
				for($i=$li_desde;$i<$li_hasta;$i++)
				{
					if($i!=$ls_year)
					{print"<option value=".$i.">".$i."</option>";}
					else
					{print"<option value=".$i." selected>".$i."</option>";}
				}
			?>
            </select>
          </div></td>
          <td width="245">&nbsp;</td>
        </tr>
        <tr>
          <td height="18">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><div align="left"><strong>Mes Hasta </strong>
                  <select name="cmbmesh" id="cmbmesh">
                    <option value="01" selected>Enero</option>
                    <option value="02">Febrero</option>
                    <option value="03">Marzo</option>
                    <option value="04">Abril</option>
                    <option value="05">Mayo</option>
                    <option value="06">Junio</option>
                    <option value="07">Julio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                  </select>
          </div></td>
          <td><div align="left"><strong>A&ntilde;o Hasta </strong>
                  <select name="cmbanioh" id="cmbanioh">
                    <?php 
				$ls_year=date("Y");
				for($i=$li_desde;$i<$li_hasta;$i++)
				{
					if($i!=$ls_year)
					{print"<option value=".$i.">".$i."</option>";}
					else
					{print"<option value=".$i." selected>".$i."</option>";}
				}
			?>
                  </select>
          </div></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="22">&nbsp;</td>
          <td height="22">&nbsp;</td>
          <td height="22">&nbsp;</td>
        </tr>
        <tr>
          <td height="22" colspan="3"><table width="537" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="105"><div align="right"><span class="style1 style14">Almac&eacute;n</span></div></td>
              <td width="432"><input name="txtcodalm" type="text" id="txtcodprov22" size="12" maxlength="12"  style="text-align:center"  onBlur="javascript:rellenar_cad(this.value,10,document.form1.txtcodprov2.name)">
                <a href="javascript:uf_catalogoalmacen();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
                <input name="txtnomfisalm" type="text" class="sin-borde" id="txtnomfisalm2" size="40" readonly></td>
            </tr>
          </table></td>
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
    <tr>
      <td colspan="3" align="center"><div align="left">
        <table width="546" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td height="22" colspan="2"><span class="style14"><strong>Ordenado Por</strong></span></td>
          </tr>
          <tr>
            <td width="172"><div align="center"><strong>Articulo</strong></div></td>
            <td height="22">&nbsp;</td>
          </tr>
          <tr>
            <td height="22"><div align="right">C&oacute;digo
                    <input name="radioordenact" type="radio" class="sin-borde" value="radiobutton" checked>
              </div></td>
            <td width="372" height="22">&nbsp;</td>
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
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function uf_catalogoalmacen()
{
	window.open("sigesp_catdinamic_almacen.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=120,top=70,location=no,resizable=yes");
}


function uf_mostrar_reporte()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{
		ls_mesd= f.cmbmesd.value;
		ls_aniod= f.cmbaniod.value;
		ls_mesh= f.cmbmesh.value;
		ls_anioh= f.cmbanioh.value;
		tipoformato = f.cmbbsf.value;
		codalm = f.txtcodalm.value;
		nomfisalm = f.txtnomfisalm.value;
		if (ls_anioh>=ls_aniod)
		{
			if (((ls_anioh==ls_aniod)&&(ls_mesh>ls_mesd))||((ls_anioh==ls_aniod)&&(ls_mesh==ls_mesd))||((ls_aniod<ls_anioh)&&(ls_mesd>ls_mesh))||((ls_aniod<ls_anioh)&&(ls_mesd==ls_mesh)))
			{
				if(f.radioordenact[0].checked)
				{
					li_ordenact=0;
				}
				else
				{
					li_ordenact=1;
				}
				window.open("reportes/sigesp_siv_rpp_existencias_mensuales.php?ordenact="+li_ordenact+"&mesd="+ls_mesd+"&aniod="+ls_aniod+"&mesh="+ls_mesh+"&anioh="+ls_anioh+"&codalm="+codalm+"&nomfisalm="+nomfisalm,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
			}
			else
			{
				alert("Seleccione correctamente los meses.")
			}
		}
		else
		{
			alert("Seleccione correctamente los años.");
		}
	}
	else
	{
		alert("No tiene permiso para realizar esta operación");
	}
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