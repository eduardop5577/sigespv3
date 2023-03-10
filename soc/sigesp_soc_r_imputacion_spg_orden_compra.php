<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
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
require_once("class_folder/class_funciones_soc.php");
$io_fun_compra = new class_funciones_soc();
$arrResultado = $io_fun_compra->uf_load_seguridad("SOC","sigesp_soc_r_imputacion_spg_orden_compra.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos = $arrResultado['as_permisos'];
$la_seguridad = $arrResultado['aa_seguridad'];
$la_permisos = $arrResultado['aa_permisos'];

$ls_reporte = $io_fun_compra->uf_select_config("SOC","REPORTE","LISTADO_IMP_SPG_ORDCOM","sigesp_soc_rpp_imputacion_spg_orden_compra.php","C");

$ls_logusr = $_SESSION["la_logusr"];
$ls_codemp = $_SESSION["la_empresa"]["codemp"];
$li_diasem = date('w');
switch ($li_diasem){
  case '0': $ls_diasem='Domingo';
  break; 
  case '1': $ls_diasem='Lunes';
  break;
  case '2': $ls_diasem='Martes';
  break;
  case '3': $ls_diasem='Mi&eacute;rcoles';
  break;
  case '4': $ls_diasem='Jueves';
  break;
  case '5': $ls_diasem='Viernes';
  break;
  case '6': $ls_diasem='S&aacute;bado';
  break;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript"  src="js/funcion_soc.js"></script>
<script type="text/javascript"  src="../soc/js/stm31.js"></script>
<script type="text/javascript"  src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<title>Reporte de Orden de Compra</title>
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/general.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css" />
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
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
</style>
</head>
<body>
<?php
if (array_key_exists("operacion",$_POST))
    {
      $ls_operacion = $_POST["operacion"];
      $ls_numordcomdes = $_POST["txtnumordcomdes"];
      $ls_numordcomhas = $_POST["txtnumordcomhas"];
      $ls_fecordcomdes = $_POST["txtfecordcomdes"];
      $ls_fecordcomhas = $_POST["txtfecordcomhas"];
    }
else
    {
      $ls_operacion = "";
      $ls_numordcomdes = "";
      $ls_numordcomhas = "";
      $ls_codprodes = "";
      $ls_codprohas = "";
      $ls_fecordcomdes = '01/'.date("m/Y");
      $ls_fecordcomhas = date("d/m/Y");
      $ls_coduniejedes = "";
      $ls_coduniadmhas = "";
      $ls_codartdes = "";
      $ls_codarthas = "";
      $ls_codserdes = "";
      $ls_codserhas = "";
    }

?>
<div align="center">
  <table width="800" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
    <tr>
      <td width="800" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" alt="Encabezado" width="800" height="40" /></td>
    </tr>
    <td height="20" colspan="12" bgcolor="#E7E7E7">
    <table width="800" border="0" align="center" cellpadding="0" cellspacing="0">			
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Ordenes de Compra</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table></td><tr>
      <td height="20" bgcolor="#E7E7E7" class="cd-menu" style="text-align:left"><script type="text/javascript"  src="../soc/js/menu.js"></script></td>
    </tr>
    <tr>
      <td height="13" colspan="11" class="toolbar"></td>
    </tr>
    <tr style="text-align:left">
      <td width="800" height="13" colspan="11" class="toolbar" style="text-align:left"><span class="toolbar" style="text-align:left"></span><a href="javascript: ue_imprimir();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0" title="Imprimir" /></a><a href="../soc/sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir" /></a><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0" title="Ayuda" /></a></td>
    </tr>
  </table>
  <p>&nbsp;</p>
  <form id="formulario" name="formulario" method="post" action="">
  <?php
  //////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_compra->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_compra);
  //////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
  ?>
    <table width="543" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td height="22" colspan="6" class="titulo-ventana"><input name="operacion" type="hidden" id="operacion" value="<?php print $ls_operacion ?>" />
          Imputacion Presupuestaria de Ordenes de Compra
          
          <input name="formato"    type="hidden" id="formato"    value="<?php print $ls_reporte; ?>" /></td>
      </tr>
      <tr>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>

      </tr>
      <tr style="visibility:hidden">
        <td height="13" colspan="2" style="text-align:right">Reporte en</td>
        <td height="13"><div align="left">
          <select name="cmbbsf" id="cmbbsf">
            <option value="0" selected="selected">Bs.</option>
            <option value="1">Bs.F.</option>
          </select>
        </div></td>

      </tr>
      <tr>
        <td height="13" colspan="6">&nbsp;</td>
      </tr>
      <tr>
        <td height="13" colspan="6"><table width="490" height="41" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td height="13" style="text-align:right"><div align="left"><strong>Tipo de Orden de Compra </strong></div></td>
          </tr>
          <tr>
            <td height="26" style="text-align:right"><label></label>
              <div align="left"><strong>Bienes</strong>
                <input name="rdtipo" type="radio" class="sin-borde" value="B" checked=true />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Servicios </strong>
		<input name="rdtipo" type="radio" class="sin-borde" value="S" />

	      </div>
	    </td>
        </tr>
        </table></td>
      </tr>
      <tr>
        <td height="13" colspan="6">&nbsp;</td>
      </tr>
      <tr>
        <td height="13" colspan="6"><table width="490" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td colspan="4" style="text-align:left"><strong>Orden de Compra </strong></td>
          </tr>
          <tr>
            <td width="63" style="text-align:right">Desde</td>
            <td width="171" style="text-align:left"><input name="txtnumordcomdes" type="text" id="txtnumordcomdes" value="<?php print $ls_numordcomdes ?>" size="20" maxlength="15"  style="text-align:center "  onblur="javascript:rellenar_cad(this.value,15,this)" onkeypress="return keyRestrict(event,'1234567890');" />
              <a href="javascript: ue_catalogo('sigesp_soc_cat_orden_compra.php?origen=REPORTE-DESDE');"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar hasta..." name="buscar1" width="15" height="15" border="0"  id="buscar1" /></a></td>
            <td width="44" style="text-align:right">Hasta</td>
            <td width="154" style="text-align:left"><input name="txtnumordcomhas" type="text" id="txtnumordcomhas" value="<?php print $ls_numordcomhas ?>" size="20" maxlength="15"  style="text-align:center"  onblur="javascript:rellenar_cad(this.value,15,this)"  onkeypress="return keyRestrict(event,'1234567890');" />
              <a href="javascript: ue_catalogo('sigesp_soc_cat_orden_compra.php?origen=REPORTE-HASTA');"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar desde..." name="buscar2" width="15" height="15" border="0" id="buscar2" /></a></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td height="13" colspan="6">&nbsp;</td>
      </tr>
      
    
      <tr>
        <td height="22" colspan="6" style="text-align:center"><table width="490" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td colspan="4" style="text-align:left"><strong>Fecha</strong></td>
          </tr>
          <tr>
            <td width="63" style="text-align:right">Desde</td>
            <td width="171" style="text-align:left"><input name="txtfecordcomdes" type="text" id="txtfecordcomdes" value="<?php print $ls_fecordcomdes ?>" size="12" maxlength="10"  style="text-align:left"  datepicker="true" onkeypress="currencyDate(this);" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);"/>
              <a href="javascript: ue_catalogo('sigesp_soc_cat_analisis_cotizacion.php?catalogo=txtnumanacotdes');"></a></td>
            <td width="44" style="text-align:right">Hasta</td>
            <td width="154" style="text-align:left"><input name="txtfecordcomhas" type="text" id="txtfecordcomhas" value="<?php print $ls_fecordcomhas ?>" size="12" maxlength="10"  style="text-align:left"  datepicker="true" onkeypress="currencyDate(this);" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);"/>
              <a href="javascript: ue_catalogo('sigesp_soc_cat_analisis_cotizacion.php?catalogo=txtnumanacothas');"></a></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
      </tr>

      <tr>
        <td height="30" colspan="6" style="text-align:center">&nbsp;</td>

    </table>
  </form>
  <p>&nbsp;</p>
</div>
</body>
<script >
f = document.formulario;

function ue_catalogo_bienes(ls_tipo)
{
    f.tipo.value=ls_tipo
	window.open("sigesp_soc_cat_bienes.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,resizable=yes,location=no,left=50,top=50");          
}

function ue_catalogo_servicios(ls_tipo)
{
    f.tipo.value=ls_tipo
	window.open("sigesp_soc_cat_servicios.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,resizable=yes,location=no,left=50,top=50");          
}

function rellenar_cad(cadena,longitud,objeto)
{//1
	var mystring = new String(cadena);
	cadena_ceros = "";
	lencad       = mystring.length;
    total        = longitud-lencad;
	if (cadena!="")
	   {
	     for (i=1;i<=total;i++)
			 {
			   cadena_ceros=cadena_ceros+"0";
			 }
	     cadena=cadena_ceros+cadena;
		 objeto.value=cadena;
	   }
}

//--------------------------------------------------------
//	Funci?n que valida que un intervalo de tiempo sea valido
//--------------------------------------------------------
   function ue_comparar_intervalo()
   { 
	var valido = false; 
    var diad = f.txtfecanades.value.substr(0, 2); 
    var mesd = f.txtfecanades.value.substr(3, 2); 
    var anod = f.txtfecanades.value.substr(6, 4); 
    var diah = f.txtfecanahas.value.substr(0, 2); 
    var mesh = f.txtfecanahas.value.substr(3, 2); 
    var anoh = f.txtfecanahas.value.substr(6, 4); 
    
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
		alert("El rango de fecha es invalido !!!");
	} 
	return valido;
   } 
   
function ue_imprimir()
{
	
	if (f.rdtipo[0].checked)
	{ 
		ls_tipord ="B";
	}
	if (f.rdtipo[1].checked)
	{ 
		ls_tipord ="S";
	}		
	ls_numordcomdes = f.txtnumordcomdes.value;
	ls_numordcomhas = f.txtnumordcomhas.value;
	ls_fecordcomdes = f.txtfecordcomdes.value;
	ls_fecordcomhas = f.txtfecordcomhas.value;

	//{	    
	    ls_reporte  = f.formato.value; 
	    tiporeporte = f.cmbbsf.value;
	    pagina="reportes/"+ls_reporte+"?txtnumordcomdes="+ls_numordcomdes+"&rdtipo="+ls_tipord
				+"&txtnumordcomhas="+ls_numordcomhas
				+"&txtfecordcomdes="+ls_fecordcomdes+"&txtfecordcomhas="+ls_fecordcomhas
				+"&tiporeporte="+tiporeporte;
	    window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no,left=50,top=50");
	//}	
}  

function currencyDate(date)
{ 
	ls_date=date.value;
	li_long=ls_date.length;
	if (li_long==2)
	   {
	     ls_date   = ls_date+"/";
	 	 ls_string = ls_date.substr(0,2);
		 li_string = parseInt(ls_string);
		 if ((li_string>=1)&&(li_string<=31))
			{
			  date.value=ls_date;
			}
		 else
			{
			  date.value="";
			}
			
	   }
	if (li_long==5)
	   {
	     ls_date   = ls_date+"/";
		 ls_string = ls_date.substr(3,2);
		 li_string = parseInt(ls_string);
		 if ((li_string>=1)&&(li_string<=12))
			{
			  date.value=ls_date;
			}
		 else
			{
			  date.value=ls_date.substr(0,3);
			}
	   }
	if (li_long==10)
	   {
	     ls_string = ls_date.substr(6,4);
		 li_string = parseInt(ls_string);
		 if ((li_string>=1900)&&(li_string<=2090))
			{
			  date.value=ls_date;
			}
		 else
			{
			  date.value=ls_date.substr(0,6);
			}
	   }
} 

function ue_catalogo(ls_catalogo)
{
	// abre el catalogo que se paso por parametros
	window.open(ls_catalogo,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=750,height=400,left=50,top=50,location=no,resizable=yes");
}

function uf_deshabilitar()
{
   if (document.formulario.esttip[0].checked)
   {
	  document.formulario.txtcodartdes.value="";          
	  document.formulario.txcodtarthas.value="";
	  document.formulario.txtcodserdes.value="";
	  document.formulario.txtcodserhas.value="";
	  document.formulario.txtcodartdes.disabled=true;	   
	  document.formulario.txcodtarthas.disabled=true;	   
	  document.formulario.txtcodserdes.disabled=true;	   
	  document.formulario.txtcodserhas.disabled=true;	   
	  eval("document.images['busartdes'].style.visibility='visible'");
	  eval("document.images['busarthas'].style.visibility='visible'");
	  eval("document.images['busserdes'].style.visibility='visible'");
	  eval("document.images['busserhas'].style.visibility='visible'");
   }
   
   if (document.formulario.esttip[1].checked)
   {
	  document.formulario.txtcodartdes.value="";
	  document.formulario.txcodtarthas.value="";
	  document.formulario.txtcodserdes.value="";
	  document.formulario.txtcodserhas.value="";
	  
	  document.formulario.txtcodartdes.disabled=true;	   
	  document.formulario.txcodtarthas.disabled=true;	   
	  eval("document.images['busartdes'].style.visibility='visible'");
	  eval("document.images['busarthas'].style.visibility='visible'");
	  document.formulario.txtcodserdes.disabled=false;	   
	  document.formulario.txtcodserhas.disabled=false;	   
	  eval("document.images['busserdes'].style.visibility='hidden'");
	  eval("document.images['busserhas'].style.visibility='hidden'");
   }
   
   if (document.formulario.esttip[2].checked)
   {
	  document.formulario.txtcodartdes.value="";
	  document.formulario.txcodtarthas.value="";
	  document.formulario.txtcodserdes.value="";
	  document.formulario.txtcodserhas.value="";
	  
	  document.formulario.txtcodartdes.disabled=false;	   
	  document.formulario.txcodtarthas.disabled=false;	   
	  eval("document.images['busartdes'].style.visibility='hidden'");
	  eval("document.images['busarthas'].style.visibility='hidden'");		  
	  document.formulario.txtcodserdes.disabled=true;	   
	  document.formulario.txtcodserhas.disabled=true;	   
	  eval("document.images['busserdes'].style.visibility='visible'");
	  eval("document.images['busserhas'].style.visibility='visible'");
   }
}
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
	window.open("ayudas/sigesp_ayu_soc_reporte_imputacionpreordcom.pdf","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>