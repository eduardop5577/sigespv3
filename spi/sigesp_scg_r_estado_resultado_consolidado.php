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
	require_once("class_funciones_scg.php");
	$io_fun_scg=new class_funciones_scg();
	$io_fun_scg->uf_load_seguridad("SCG","sigesp_scg_r_estado_resultado_consolidado.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_reporte = $io_fun_scg->uf_select_config("SCG","REPORTE","ESTADO_RESULTADO_CONSOLIDADO","sigesp_scg_rpp_estado_resultado_consolidado.php","C");
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	$li_ano=substr($ldt_periodo,0,4);
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];

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
}?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Estado de Resultado Consolidado</title>
<script type="text/javascript" src="../shared/js/disabled_keys.js"></script>
<script type="text/javascript" src="../spg/js/stm31.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
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
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css"  rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css"   rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.Estilo1 {font-weight: bold}
.Estilo2 {font-size: 14px}
-->
</style>
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
   <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			  <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Contabilidad Patrimonial</td>
			    <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
				<tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td> </tr>
	  	</table>
	 </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript:ue_imprimir('<? print $ls_codemp; ?>','<?php echo $ls_reporte ?>');"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a></td>
    <td class="toolbar" width="25"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" title="Ayuda" width="20" height="20"></td>
    <td class="toolbar" width="25">&nbsp;</td>
    <td class="toolbar" width="678">&nbsp;</td>
  </tr>
</table>
<?php
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../shared/class_folder/ddlb_generic_bd.php");

$io_include = new sigesp_include();
$ls_conect  = $io_include->uf_conectar();
$io_sql		= new class_sql($ls_conect);
$io_msg		= new class_mensajes();
$io_funcion = new class_funciones();
$io_objcmb  = new ddlb_generic_bd($ls_conect);

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
}
else
{
	$ls_operacion="MESES";	
}
if(array_key_exists("hidbot",$_POST))
{
	$lb_bot=false;
}
else
{
	$lb_bot=true;	
}
if (array_key_exists("txtfecdes",$_POST)) 
{
	$ldt_fecdes=$_POST["txtfecdes"];
}
else
{
	$ldt_fecdes="01/01/".$li_ano;
}
if (array_key_exists("txtfechas",$_POST)) 
{
	$ldt_fechas=$_POST["txtfechas"];
}
else
{
	$ldt_fechas=date("d/m/Y");
}
?>
</div> 
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_scg->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_scg);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<table width="530" border="0" align="center" cellpadding="0" cellspacing="1" class="formato-blanco">
    <tr>
      <td width="521"></td>
    </tr>
    <tr class="titulo-ventana">
      <td height="22" colspan="3" align="center"> <input name="operacion"   type="hidden"   id="operacion"   value="<?php print $ls_operacion;?>">
      Estado de Resultado
      <input name="hidbot" type="hidden" id="hidbot" value="<?php print $lb_bot ?>"></td>
    </tr>
    <tr>
      <td height="100" colspan="3" align="center"><div align="left">
        <table width="480" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr class="titulo-celdanew">
            <td height="13" colspan="7"><strong class="titulo-celdanew">Intervalos de Fechas </strong></td>
            </tr>
          <tr>
            <td height="27">&nbsp;</td>
            <td width="65" height="27">&nbsp;</td>
            <td height="27" colspan="2">&nbsp;</td>
            <td height="27"><input name="botmeses" type="button" class="boton" id="botmeses" value="Meses" onClick="uf_cambio_meses()"></td>
            <td height="27"><div align="left">
              <input name="botdias" type="button" class="boton" id="botdias" value="Dias" onClick="uf_cambio_dias()">
            </div></td>
            <td height="27">&nbsp;</td>
          </tr>
		  <?php
		   if($ls_operacion=="MESES") 
		   {
			  $lb_bot=true;	
		  ?>
          <tr>
            <td height="22">&nbsp;</td>
            <td height="22"><div align="right">Desde
            </div></td>
            <td height="22" colspan="2"><div align="left">
              <select name="cmbmesdes" id="cmbmesdes">
                <option value="01">Enero</option>
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
            <td height="22">
			<?php
			  if($_SESSION["ls_gestor"]=='INFORMIX')
			  {
			   $ls_selec="distinct substr(fecsal,1,4) as anuales";
			  }
			  else 
			  {
			   $ls_selec="distinct substring(fecsal,1,4) as anuales";
			  }
			  $ls_coddev="anuales";
			  $ls_tabla="scg_saldos";
	          $ls_codemp=$_SESSION["la_empresa"]["codemp"];
			  $ls_codigo="";
			  $ls_clave="";
			  $ls_nomcmb="cmbagnodes";
			  $li_width="50";			  
			  $io_objcmb->uf_cargar_conceptos($ls_selec,$ls_coddev,$ls_tabla,$ls_codemp,$ls_codigo,$ls_clave,$ls_nomcmb,$li_width,'');
			 ?>			 </td>
            <td height="22"><div align="right">Hasta
            </div></td>
            <td height="22"><div align="left">
				<select name="cmbmeshas" id="cmbmeshas">
				  <option value="12">Diciembre</option>
				  <option value="01">Enero</option>
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
				</select>			  
				<?php
				  if($_SESSION["ls_gestor"]=='INFORMIX')
			       { 
				    $ls_selec="distinct substr(fecsal,1,4) as anuales";
				   }
				   else 
				   { 
				    $ls_selec="distinct substring(fecsal,1,4) as anuales";
				   }
				  $ls_coddev="anuales";
				  $ls_tabla="scg_saldos";
	              $ls_codemp=$_SESSION["la_empresa"]["codemp"];
				  $ls_codigo="";
				  $ls_clave="";
				  $ls_nomcmb="cmbagnohas";
				  $li_width="50";
				  $io_objcmb->uf_cargar_conceptos($ls_selec,$ls_coddev,$ls_tabla,$ls_codemp,$ls_codigo,$ls_clave,$ls_nomcmb,$li_width,'');
			  ?>
            </div></td>
          </tr>
		  <?php 
		    }
			if($ls_operacion=="DIAS")
			{
			  $lb_bot=false;
		  ?>
          <tr>
            <td width="29" height="22"><div align="right"></div></td>
            <td height="22"><div align="right"> Desde
</div></td>
            <td colspan="2"><div align="left">
              <input name="txtfecdes" type="text" id="txtfecdes" onKeyPress="javascript: ue_formatofecha(this,'/',patron,true);" value="<?php print $ldt_fecdes ; ?>" size="15" maxlength="15" datepicker="true">
            </div></td>
            <td width="46">&nbsp;</td>
            <td width="60"><div align="right"> Hasta
</div></td>
            <td width="189"><div align="left">
              <input name="txtfechas" type="text" id="txtfechas"  onKeyPress="javascript: ue_formatofecha(this,'/',patron,true);" value="<?php print $ldt_fechas ; ?>" size="15" maxlength="15" datepicker="true">
            </div></td>
          </tr>
		  <?php 
		    }
		  ?>
        </table>
        </div></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center">&nbsp;</td>
    </tr>
  </table>
</form>      
</body>
<script >
function ue_imprimir(codemp,as_reporte)
{
  f = document.form1;
  li_imprimir = f.imprimir.value;
  if (li_imprimir==1)
	 {	
	   hidbot = f.hidbot.value;
	   if (hidbot==true)
		  {
		    cmbmesdes  = f.cmbmesdes.value;
			cmbmeshas = f.cmbmeshas.value;
			cmbagnodes = f.cmbagnodes.value;
			cmbagnohas = f.cmbagnohas.value;
			if ((cmbagnodes=="s1")&&(cmbagnohas=="s1"))
			   {
				 alert ("Debe seleccionar los Parametros de Busqueda");
			   }
			else
			   {
				 pagina = "reportes/"+as_reporte+"?hidcodemp="+codemp+"&cmbmesdes="+cmbmesdes+"&cmbmeshas="+cmbmeshas+
				          "&cmbagnodes="+cmbagnodes+"&cmbagnohas="+cmbagnohas+"&hidbot="+hidbot;
				 window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			   }
		  }
	   if (hidbot==false)
		  {
		    txtfecdes = f.txtfecdes.value;
			txtfechas = f.txtfechas.value;
			if ((txtfecdes=="")&&(txtfechas==""))
			   {
				 alert ("Debe seleccionar los Parametros de Busqueda");
			   }
			else
			   {
				 pagina = "reportes/"+as_reporte+"?hidcodemp="+codemp+"&txtfecdes="+txtfecdes+
				          "&txtfechas="+txtfechas+"&hidbot="+hidbot;
				 window.open(pagina,"catalogo","menubar=yes,toolbar=yes,scrollbars=yes,width=800,height=600,resizable=yes,location=yes");
			   }
		  }	
   	 }
  else
   	 {
 	   alert("No tiene permiso para realizar esta operaci?n");
   	 }
}

function uf_cambio_meses()
{
  f=document.form1;
  f.operacion.value = "MESES";
  f.action		    = "sigesp_scg_r_estado_resultado_consolidado.php";
  f.submit();
}

function uf_cambio_dias()
{
  f=document.form1;
  f.operacion.value = "DIAS";
  f.action          = "sigesp_scg_r_estado_resultado_consolidado.php";
  f.submit();	
}
   
function ue_cerrar()
{
  location.href = "sigespwindow_blank.php";
}

//--------------------------------------------------------
//	Funci?n que le da formato a la fecha
//--------------------------------------------------------
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);

function ue_formatofecha(d,sep,pat,nums)
{
	if(d.valant != d.value)
	{
		val = d.value
		largo = val.length
		val = val.split(sep)
		val2 = ''
		for(r=0;r<val.length;r++)
		{
			val2 += val[r]	
		}
		if(nums)
		{
			for(z=0;z<val2.length;z++)
			{
				if(isNaN(val2.charAt(z)))
				{
					letra = new RegExp(val2.charAt(z),"g")
					val2 = val2.replace(letra,"")
				}
			}
		}
		val = ''
		val3 = new Array()
		for(s=0; s<pat.length; s++)
		{
			val3[s] = val2.substring(0,pat[s])
			val2 = val2.substr(pat[s])
		}
		for(q=0;q<val3.length; q++)
		{
			if(q ==0)
			{
				val = val3[q]
			}
			else
			{
				if(val3[q] != "")
				{
					val += sep + val3[q]
				}
			}
		}
		d.value = val
		d.valant = val
	}
}
</script>
<script  src="../shared/js/js_intra/datepickercontrol.js" ></script>
</html>