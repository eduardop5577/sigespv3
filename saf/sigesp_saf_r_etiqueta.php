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
$arrResultado = $io_fun_activo->uf_load_seguridad("SAF","sigesp_saf_r_etiqueta.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado['as_permisos'];
$la_seguridad=$arrResultado['aa_seguridad'];
$la_permisos=$arrResultado['aa_permisos'];
require_once("sigesp_saf_c_activo.php");
$ls_codemp = $_SESSION["la_empresa"]["codemp"];
$io_saf_tipcat= new sigesp_saf_c_activo();
$ls_rbtipocat=$io_saf_tipcat->uf_select_valor_config($ls_codemp);
$ls_reporte=$io_fun_activo->uf_select_config("SAF","REPORTE","ETIQUETAS","sigesp_saf_rpp_etiquetas.php","C");
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Reporte de Etiquetas de Activos </title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="js/stm31.js"></script>

<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
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
.Estilo1 {font-weight: bold}
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
    <td height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:uf_mostrar_reporte();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a><a href="javascript:ue_openexcel();"></a><a href="javascript:ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" title="Ayuda" height="20"></td>
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
/*if ($ls_operacion=="REPORT")
{
	$ld_fecdesde=$_POST["txtdesde"];
	$ld_fechasta=$_POST["txthasta"];
	$ls_coddesde=$_POST["txtcoddesde"];
	$ls_codhasta=$_POST["txtcodhasta"];
	if(($ls_coddesde!="")&&($ls_codhasta!=""))
	{
		$ls_desccod=" y desde el articulo ".$ls_coddesde." hasta el ".$ls_codhasta;
	}
	else
	{
		$ls_desccod="";
	}
	$ls_evento="REPORT";
	$ls_descripcion="Gener� un reporte de resumen de inventario. Desde el  ". $ld_fecdesde ." hasta el ".$ld_fechasta.$ls_desccod;
	$lb_variable= $io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
							$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
							$la_seguridad["ventanas"],$ls_descripcion);
}
*/
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
<table width="575" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="573"></td>
    </tr>
    <tr>
      <td colspan="3" align="center" class="titulo-ventana">Reporte de Etiquetas </td>
    </tr>
    <tr>
      <td colspan="3" align="center"><table width="511" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr style="visibility:hidden">
          <td height="19" colspan="2"><strong>Reporte en</strong>
            <select name="cmbbsf" id="cmbbsf">
              <option value="0" selected>Bs.</option>
              <option value="1">Bs.F.</option>
            </select></td>
          </tr>
        <tr>
          <td colspan="2"><strong> Activos </strong></td>
        </tr>
        <tr>
          <td width="49"><div align="right">Desde</div></td>
          <td width="446" height="22"><div align="left">
              <input name="txtcoddesde" type="text" id="txtcoddesde" size="21" maxlength="20"  style="text-align:center ">
              <a href="javascript:uf_catalogo_activo('txtcoddesde','txtdendesde');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a> 
              <input name="txtdendesde" type="text" class="sin-borde" id="txtdendesde" size="40" readonly>
              <input name="txtseract" type="hidden" id="txtseract">
              <input name="txtideact" type="hidden" id="txtideact">
          </div>
            <div align="left"> </div></td>
        </tr>
        <tr>
          <td height="10"><div align="right"><span class="style1 style14">Hasta</span></div></td>
          <td height="22"><div align="left">
              <input name="txtcodhasta" type="text" id="txtcodhasta" size="21" maxlength="20"  style="text-align:center">
              <a href="javascript:uf_catalogo_activo('txtcodhasta','txtdenhasta');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a> 
              <input name="txtdenhasta" type="text" class="sin-borde" id="txtdenhasta" size="40" readonly>
</div>
            <div align="left"> </div></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="24" colspan="3" align="center"><div align="right">
      <input name="operacion"   type="hidden"   id="operacion2"   value="<?php print $ls_operacion;?>">
      </div></td>
    </tr>
    <tr>
      <td colspan="3" align="center">
        <div align="center">
          <p></p>
      </div></td>
    </tr>
  </table>
    <p align="center">
<input name="total" type="hidden" id="total" value="<?php print $totrow;?>">
	   <input name="formato"    type="hidden" id="formato"    value="<?php print $ls_reporte; ?>">
</p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">

	function uf_resprimario(ls_destino)
	{
		window.open("sigesp_saf_cat_personal.php?destino="+ ls_destino +"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=580,height=400,left=120,top=70,location=no,resizable=yes");
	}
	
	function uf_unidad()
	{
		ls_destino="activo";
		window.open("sigesp_saf_cat_unidadfisica.php?destino="+ ls_destino +"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=580,height=400,left=120,top=70,location=no,resizable=yes");
	}

	function uf_catalogo_activo(ls_coddestino,ls_dendestino)
	{
		window.open("sigesp_saf_cat_codactivoss.php?coddestino="+ ls_coddestino +"&dendestino="+ ls_dendestino +"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=600,height=400,left=120,top=70,location=no,resizable=yes");
	}
	
	function uf_mostrar_reporte()
	{
		valido=true;
		if(valido)
		{
			f=document.form1;
			li_imprimir=f.imprimir.value;
			if(li_imprimir==1)
			{
				ls_coddesde= f.txtcoddesde.value;
				ls_codhasta= f.txtcodhasta.value;
				formato=f.formato.value;
				pantalla="reportes/"+formato+"?coddesde="+ls_coddesde+"&codhasta="+ls_codhasta;
				window.open(pantalla,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				//f.operacion.value="REPORT";
				f.action="sigesp_siv_r_activo.php";
				//f.submit();
			}
			else
			{alert("No tiene permiso para realizar esta operaci�n");}
		}
	}
	
	function ue_openexcel()
	{
		valido=ue_comparar_intervalo();
		if(valido)
		{
			f=document.form1;
			li_imprimir=f.imprimir.value;
			if(li_imprimir==1)
			{
				ld_desde=    f.txtdesde.value;
				ld_hasta=    f.txthasta.value;
				ls_coddesde= f.txtcoddesde.value;
				ls_codhasta= f.txtcodhasta.value;
				ls_estatus=  f.hidradio.value;
				ls_codrespri=f.txtcodrespri.value;
				ls_codresuso=f.txtcodresuso.value;
				ls_coduni=   f.txtcoduni.value;
				if(ls_estatus=="")
				{
					ls_estatus=0;
				}
				if(f.radioordenact[0].checked)
				{
					li_ordenact=0;
				}
				else
				{
					li_ordenact=1;
				}
				tipoformato=f.cmbbsf.value;
				ls_grupo=    f.txtcodgru.value;
				ls_subgrupo= f.txtcodsubgru.value;
				ls_seccion=  f.txtcodsec.value;
				ls_grupohas=    f.txtcodgruhas.value;
				ls_subgrupohas= f.txtcodsubgruhas.value;
				ls_seccionhas=  f.txtcodsechas.value;
				pantalla="reportes/sigesp_saf_rpp_activo_excel.php?ordenact="+li_ordenact+"&desde="+ld_desde+
				         "&hasta="+ld_hasta+"&coddesde="+ls_coddesde+"&codhasta="+ls_codhasta+"&status="+ls_estatus+
						 "&codrespri="+ls_codrespri+"&codresuso="+ls_codresuso+"&coduni="+ls_coduni+"&tipoformato="+tipoformato+
						 "&grupo="+ls_grupo+"&subgrupo="+ls_subgrupo+"&seccion="+ls_seccion+"&grupohas="+ls_grupohas+"&subgrupohas="+ls_subgrupohas+"&seccionhas="+ls_seccionhas;
				window.open(pantalla,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				//f.operacion.value="REPORT";
				f.action="sigesp_siv_r_activo.php";
				//f.submit();
			}
			else
			{alert("No tiene permiso para realizar esta operaci�n");}
		}
	}

function ue_grupo(origen)
{
	f=document.form1;
	
    window.open("sigesp_saf_cat_grupo.php?tipo="+origen+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	if(origen=="desde")
	{
		f.txtcodsubgru.value="";
		f.txtdensubgru.value="";
		f.txtcodsec.value="";
		f.txtdensec.value="";
	}
	else
	{
		f.txtcodsubgruhas.value="";
		f.txtdensubgruhas.value="";
		f.txtcodsechas.value="";
		f.txtdensechas.value="";
	}

}
function ue_subgrupo(origen)
{
	f=document.form1;
	if(origen=="desde")
		codgru=ue_validarvacio(f.txtcodgru.value);
	else
		codgru=ue_validarvacio(f.txtcodgruhas.value);
	
	if(codgru!="---")
	{
	    dengru = f.txtdengru.value;
		window.open("sigesp_saf_cat_subgrupo.php?txtcodgru="+codgru+"&txtdengru="+dengru+"&tipo="+origen+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
		if(origen=="desde")
		{
			f.txtcodsec.value="";
			f.txtdensec.value="";
		}
		else
		{
			f.txtcodsechas.value="";
			f.txtdensechas.value="";
		}
	}
	else
	{
		alert("Debe seleccionar un grupo.");
	}
}

function ue_seccion(origen)
{
	f=document.form1;
	if(origen=="desde")
	{
		codgru=ue_validarvacio(f.txtcodgru.value);
		codsubgru=ue_validarvacio(f.txtcodsubgru.value);
	}
	else
	{
		codgru=ue_validarvacio(f.txtcodgruhas.value);
		codsubgru=ue_validarvacio(f.txtcodsubgruhas.value);
	}

	dengru = f.txtdengru.value;
	densubgru = f.txtdensubgru.value;
	if((codgru!="---")||(codsubgru!="---"))
	{
		window.open("sigesp_saf_cat_seccion.php?txtcodgru="+codgru+"&txtcodsubgru="+codsubgru+"&txtdengru="+dengru+"&txtdensubgru="+densubgru+"&tipo="+origen+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("Debe seleccionar un grupo y un subgrupo.");
	}
}

	function ue_validarvacio(valor)
    {
		var texto;
		while(''+valor.charAt(0)==' ')
		{
			valor=valor.substring(1,valor.length)
		}
		texto = valor;
		return texto;
    }
function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

//--------------------------------------------------------
//	Funci�n que obtiene el valor de el radio button
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