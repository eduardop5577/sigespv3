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
$arrResultado = $io_fun_activo->uf_load_seguridad("SIV","sigesp_siv_r_movimientos.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos = $arrResultado['as_permisos'];
$la_seguridad = $arrResultado['aa_seguridad'];
$la_permisos = $arrResultado['aa_permisos'];
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Reporte de Movimientos de Inventario </title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="css/siv.css" rel="stylesheet" type="text/css">
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
.Estilo1 {font-weight: bold}
-->
</style></head>
<body>
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr> 
    <td height="30" colspan="4" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="4" class="cd-menu">
	<table width="776" border="0" align="center" cellpadding="0" cellspacing="0">
	
		<td width="423" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Inventario </td>
		<td width="353" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  <tr>
		<td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
		<td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
	</table></td>
  </tr>
  <tr>
    <td height="20" colspan="4" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="7" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td width="20" height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:uf_mostrar_reporte();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="../siv/sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20"></td>
    <td width="718" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
</table>
<?php
require_once("../base/librerias/php/general/sigesp_lib_include.php");
$io_in=new sigesp_include();
$con=$io_in->uf_conectar();

require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
$io_ds=new class_datastore();

require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$io_sql=new class_sql($con);

require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
$io_msg=new class_mensajes();

require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_funcion=new class_funciones(); 

require_once("../shared/class_folder/grid_param.php");
$grid=new grid_param();

$la_emp=$_SESSION["la_empresa"];
if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
}
else
{
	$ls_operacion="";	
}

?>
</div> 
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_activo->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_activo);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>	

  <table width="442" height="18" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="438" colspan="2" class="titulo-ventana">Reporte de Movimientos de Inventario </td>
    </tr>
  </table>
  <table width="437" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td colspan="3"></td>
    </tr>
    <tr>
      <td colspan="5" align="center">
        <div align="left"></div></td>
    </tr>
    <tr>
      <td height="53" colspan="5" align="center">      <div align="left">
        <table width="415" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr style="display:none">
            <td height="19" colspan="2"><strong>Reporte en</strong>
                <select name="cmbbsf" id="cmbbsf">
                  <option value="0" selected>Bs.</option>
                  <option value="1">Bs.F.</option>
              </select></td>
          </tr>
          <tr>
            <td height="13" colspan="2"><strong>Tipo de Busqueda </strong></td>
            </tr>
          <tr>
            <td width="55"><div align="right">Art&iacute;culo</div></td>
            <td height="22"><div align="left">
              <input name="txtcodart" type="text" id="txtcodart" size="21" maxlength="20"  style="text-align:center ">
              <a href="javascript:uf_catalogoarticulo();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
              <input name="txtdenart" type="text" class="sin-borde" id="txtdenart2" size="30">
            </div>             </td>
            </tr>
          <tr>
            <td height="19"><div align="right"><span class="style1 style14">Almac&eacute;n</span></div></td>
            <td height="22"><div align="left">
              <input name="txtcodalm" type="text" id="txtcodalm" size="12" maxlength="12"  style="text-align:center" >
              <a href="javascript:uf_catalogoalmacen();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
              <input name="txtnomfisalm" type="text" class="sin-borde" id="txtnomfisalm" size="40">
            </div>             </td>
            </tr>
        </table>
      </div></td>
    </tr>
    <tr>
      <td colspan="5" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="5" align="center"><table width="415" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td colspan="2"><strong>Intervalo de Fechas </strong></td>
          <td width="65">&nbsp;</td>
          <td width="172">&nbsp;</td>
          <td width="30">&nbsp;</td>
        </tr>
        <tr>
          <td width="81"><div align="right">Desde</div></td>
          <td width="65"><input name="txtdesde" type="text" id="txtdesde" size="15"  onKeyPress="ue_separadores(this,'/',patron,true);"  datepicker="true"></td>
          <td><div align="right">Hasta</div></td>
          <td><div align="left">
            <input name="txthasta" type="text" id="txthasta" size="15"  onKeyPress="ue_separadores(this,'/',patron,true);"  datepicker="true">
</div></td>
          <td>&nbsp;</td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td width="77" align="center"><div align="right" class="style1 style14"></div></td>
      <td width="146" colspan="2" align="left">&nbsp;        </td>
      <td width="49" align="center"><div align="right" class="style1 style14"></div></td>
      <td width="165" align="center"><div align="left">
        <input name="txtdesalm" type="hidden" id="txtdesalm">
        <input name="txttelalm" type="hidden" id="txttelalm">
        <input name="txtubialm" type="hidden" id="txtubialm">
        <input name="txtnomresalm" type="hidden" id="txtnomresalm">
        <input name="txttelresalm" type="hidden" id="txttelresalm">
        <input name="hidstatus" type="hidden" id="hidstatus">
        <input name="hidunidad" type="hidden" id="hidunidad">
      </div></td>
    </tr>
    <tr>
      <td colspan="5" align="center"><div align="left">
        <table width="415" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td colspan="5"><span class="style14"><strong>Ordenado Por</strong></span></td>
            </tr>
          <tr>
            <td colspan="2"><div align="center"><span class="style1"><strong>Fecha</strong></span></div></td>
            <td colspan="2"><div align="center"><strong>Art&iacute;culo</strong></div></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td width="49"><div align="right"></div></td>
            <td width="134"><div align="right"><span class="style1">Ascendente
                  <input name="radioordenfec" type="radio" class="sin-borde" value="0" checked  c�digo>
            </span></div></td>
            <td width="11">&nbsp;</td>
            <td width="128"><div align="right">C&oacute;digo
                  <input name="radioordenart" type="radio" class="sin-borde" value="radiobutton" checked>
            </div></td>
            <td width="91">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right"></div></td>
            <td><div align="right">Descendente
                <input name="radioordenfec" type="radio" class="sin-borde" value="1"  Nombre>
            </div></td>
            <td>&nbsp;</td>
            <td><div align="right">Denominaci&oacute;n
                  <input name="radioordenart" type="radio" class="sin-borde" value="radiobutton">
            </div></td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </div></td>
    </tr>
    <tr>
      <td height="24" colspan="5" align="center"><div align="right">
      <input name="operacion"   type="hidden"   id="operacion2"   value="<?php print $ls_operacion;?>">
      </div></td>
    </tr>
    <tr>
      <td colspan="5" align="center">
        <div align="center">
          <p><span class="Estilo1">
          </span></p>
      </div></td>
    </tr>
  </table>
  <div align="left"></div>
  <p align="center">
<input name="total" type="hidden" id="total" value="<?php print $totrow;?>">
</p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
	function ue_search()
	{
	  f=document.form1;
	  f.operacion.value="BUSCAR";
	  f.action="sigesp_rpc_r_provxespecia.php";
	  f.submit();
	}

	function uf_catalogoarticulo()
	{
		window.open("sigesp_catdinamic_articulom.php?tipo=tipo","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=120,top=70,location=no,resizable=yes");
	}
	
	function uf_catalogoalmacen()
	{
		window.open("sigesp_catdinamic_almacen.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=120,top=70,location=no,resizable=yes");
	}
	
	function uf_mostrar_reporte()
	{
		valido=ue_comparar_intervalo();
		if(valido)
		{
			f=document.form1;
			li_imprimir=f.imprimir.value;
			if(li_imprimir)
			{
				ls_codart= f.txtcodart.value;
				ls_codalm= f.txtcodalm.value;
				ld_desde=  f.txtdesde.value;
				ld_hasta=  f.txthasta.value;
				if(f.radioordenfec[0].checked)
				{
					li_ordenfec=0;
				}
				else
				{
					li_ordenfec=1;
				}
		
				if(f.radioordenart[0].checked)
				{
					li_ordenart=0;
				}
				else
				{
					li_ordenart=1;
				}
				tipoformato=f.cmbbsf.value;
				window.open("reportes/sigesp_siv_rpp_movimientos.php?codart="+ls_codart+"&codalm="+ls_codalm+"&ordenfec="+li_ordenfec+"&ordenart="+li_ordenart+"&desde="+ld_desde+"&hasta="+ld_hasta+"&tipoformato="+tipoformato,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				f.action="sigesp_siv_r_movimientos.php";
			}
		}
		else
		{
			alert("No tiene permiso para realizar esta operaci�n");
		}
	}
//--------------------------------------------------------
//	Funci�n que da formato a la fecha colocando los separadores (/).
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
//	Funci�n que valida que un intervalo de tiempo sea valido
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
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>
