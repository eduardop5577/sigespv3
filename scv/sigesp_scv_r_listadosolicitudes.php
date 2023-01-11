<?php
/***********************************************************************************
* @fecha de modificacion: 14/11/2022, para la version de php 8.1 
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
require_once("class_folder/class_funciones_viaticos.php");
$io_fun_viaticos=new class_funciones_viaticos();
$ls_permisos="";
$la_seguridad = Array();
$la_permisos = Array();	
$arrResultado=$io_fun_viaticos->uf_load_seguridad("SCV","sigesp_scv_r_listadosolicitudes.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado['as_permisos'];
$la_seguridad=$arrResultado['aa_seguridad'];
$la_permisos=$arrResultado['aa_permisos'];
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
$ls_formato=$io_fun_viaticos->uf_select_config("SCV","REPORTE","LISTADO_SOLICITUDES","sigesp_scv_rpp_listadosolicitudes.php","C");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Listado de Solicitudes de Viaticos</title>
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
<script type="text/javascript" src="../shared/js/validaciones.js"></script>
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
			<table width="778" border="0" align="center" cellpadding="0" cellspacing="0">
			
            <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Control de Viaticos </td>
			  <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </table>
</td>
  </tr>
  <tr>
    <td height="20" colspan="4" class="cd-menu"><script type="text/javascript" src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="7" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td width="20" height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:uf_mostrar_reporte();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0" title="Imprimir"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" title="Ayuda"></td>
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
require_once("class_folder/sigesp_scv_c_regiones.php");
$io_region= new sigesp_scv_c_regiones($con);

$la_emp=$_SESSION["la_empresa"];
if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
}
else
{
	$ls_operacion="";	
}
	if ($ls_operacion=="REPORT")
	{
		$ld_fecdesde=$_POST["txtdesde"];
		$ld_fechasta=$_POST["txthasta"];
		$ls_evento="REPORT";
		$ls_descripcion="Generó un reporte de ordenes de despacho. Desde el  ". $ld_fecdesde ." hasta el ".$ld_fechasta;
		$lb_variable= $io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
								$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
								$la_seguridad["ventanas"],$ls_descripcion);
	}

?>
</div> 
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_viaticos->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_viaticos);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>	

  <table width="544" height="18" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="532" colspan="2" class="titulo-ventana">Listado de Solicitudes de Viaticos</td>
    </tr>
  </table>
  <table width="481" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="479"></td>
    </tr>
    <tr style="display:none">
      <td colspan="3" align="center"><div align="left">Reporte en
          <select name="cmbbsf" id="cmbbsf">
            <option value="0" selected>Bs.</option>
            <option value="1">Bs.F.</option>
          </select>
      </div></td>
    </tr>
    <tr>
      <td colspan="3" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="33" colspan="3" align="center">      <div align="left">
        <table width="532" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td colspan="2"><strong>Fecha de Registro </strong></td>
            <td width="75">&nbsp;</td>
            <td width="201">&nbsp;</td>
            <td width="63">&nbsp;</td>
          </tr>
          <tr>
            <td width="99"><div align="right">Desde</div></td>
            <td width="92"><input name="txtdesde" type="text" id="txtdesde" size="15"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);"  datepicker="true"></td>
            <td><div align="right">Hasta</div></td>
            <td><div align="left">
                <input name="txthasta" type="text" id="txthasta" size="15"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" datepicker="true">
            </div></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="5"><strong>Intervalo de Solicitudes</strong></td>
            </tr>
          <tr>
            <td><div align="right">Desde</div></td>
            <td><input name="txtcodsoldes" type="text" id="txtcodsoldes" size="13" maxlength="10" value="" readonly>
              <a href="javascript: ue_buscarsolicituddesde();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" name="personal" width="15" height="15" border="0" id="personal"></a></td>
            <td><div align="right">Hasta</div></td>
            <td><input name="txtcodsolhas" type="text" id="txtcodsolhas" size="13" maxlength="10" value="" readonly>
              <a href="javascript: ue_buscarsolicitudhasta();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="22"><div align="right">Unidad</div></td>
            <td height="22"><input name="txtcoduniadm" type="text" id="txtcoduniadm" size="17" style="text-align:center" readonly></td>
            <td height="22" colspan="3"><a href="javascript: ue_buscarunidad();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
                <input name="txtdenuniadm" type="text" class="sin-borde" id="txtdenuniadm" size="35" readonly></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Tipo de Viatico </div></td>
            <td height="22" colspan="4"><select name="cmbtipvia" id="cmbtipvia">
              <option value="0" selected>--Ninguna--</option>
              <option value="1">Viaticos de Instalacion</option>
              <option value="2">Orden de Transporte</option>
              <option value="3">Permanencia</option>
              <option value="4" >Internacionales</option>
              <option value="5">Nacionales</option>
            </select></td>
            </tr>
          <tr>
            <td height="22"><div align="right">Mision Origen </div></td>
            <td height="22" colspan="4"><input name="txtcodmis" type="text" id="txtcodmis" onKeyUp="ue_validarcomillas(this);" value="<?php print $ls_codmis; ?>" size="10" maxlength="5" readonly style="text-align:center ">
              <a href="javascript: ue_buscarmision();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
              <input name="txtdenmis" type="text" class="sin-borde" id="txtdenmis"  value="<?php print $ls_denmis; ?>" size="40" readonly>
              <input name="txtcodpaiori" type="hidden" id="txtcodpaiori" value="<?php print $ls_codpaiori; ?>">
              <input name="txtcodestori" type="hidden" id="txtcodestori" value="<?php print $ls_codestori; ?>">
              <input name="txtcodciuori" type="hidden" id="txtcodciuori" value="<?php print $ls_codciuori; ?>">
              <input name="txtobssolvia" type="hidden" id="txtobssolvia"></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Mision Destino </div></td>
            <td height="22" colspan="4"><input name="txtcodmisdes" type="text" id="txtcodmisdes" onKeyUp="ue_validarcomillas(this);" value="<?php print $ls_codmis; ?>" size="10" maxlength="5" readonly style="text-align:center ">
                <a href="javascript: ue_buscarmisiondestino();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
                <input name="txtdenmisdes" type="text" class="sin-borde" id="txtdenmisdes"  value="<?php print $ls_denmis; ?>" size="40" readonly>
                <input name="txtcodpaiori" type="hidden" id="txtcodpaiori" value="<?php print $ls_codpaiori; ?>">
                <input name="txtcodestori" type="hidden" id="txtcodestori" value="<?php print $ls_codestori; ?>">
                <input name="txtcodciuori" type="hidden" id="txtcodciuori" value="<?php print $ls_codciuori; ?>">
                <input name="txtobssolvia" type="hidden" id="txtobssolvia"></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Tipo de Documento </div></td>
            <td height="22" colspan="4"><input name="txtcodtipdoc" type="text" id="txtcodtipdoc" value="<?php print $ls_codtipdoc; ?>" size="10" readonly>
              <a href="javascript: ue_buscartipodocumento();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0">
              <input name="txtdentipdoc" type="text" class="sin-borde" id="txtdentipdoc" value="<?php print $ls_dentipdoc; ?>" size="40" readonly>
              </a></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Continente Destino </div></td>
            <td height="22" colspan="4"><select name="cmbcontinente" id="cmbcontinente" style="width:150px " onChange="document.form1.hidpais.value=this.value; javascript: ue_nuevo()">
              <?php
            $rs_data=$io_region->uf_load_continentes();
		 	$ls_selected="";
			if($ls_continente=="---")
			{
				$ls_selected="";
			}
		 ?>
              <option value='---' selected>--Seleccione--</option>
              <?php
			while ($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codcont= $row["codcont"];
				$ls_descont= $row["dencont"];
				print "<option value='$ls_codcont'>$ls_descont</option>";
			} 
	     ?>
            </select></td>
            </tr>
          <tr>
            <td height="22"><div align="right">Beneficiario</div></td>
            <td height="22"><input name="txtcodben" type="text" id="txtcodben" size="17" style="text-align:center" readonly></td>
            <td height="22" colspan="3"><a href="javascript: ue_buscarbeneficiario();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
                <input name="txtnomben" type="text" class="sin-borde" id="txtnomben" size="35" readonly>
                <input name="txtcedben" type="hidden" id="txtcedben">
              <input name="txtcedben" type="hidden" id="txtcedben">
              <input name="txtcarper" type="hidden" id="txtcarper">
              <input name="txtcodclavia" type="hidden" id="txtcodclavia">
              <input name="txtcodnom" type="hidden" id="txtcodnom"></td>
          </tr>
          <tr>
            <td height="22">&nbsp;</td>
            <td height="22" colspan="4">&nbsp;</td>
          </tr>
        </table>
      </div></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center"><div align="right" class="style1 style14"></div>        <div align="right" class="style1 style14"></div>        <div align="left"></div></td>
    </tr>
    <tr>
      <td height="24" colspan="3" align="center"><table width="415" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td colspan="2"><strong>Ordenado por </strong></td>
          <td width="98">&nbsp;</td>
          <td width="89">&nbsp;</td>
        </tr>
        <tr>
          <td width="134"><div align="right">
            <label >Numero de Solicitud
            <input name="radioorden" type="radio" class="sin-borde" value="scv_solicitudviatico.codsolvia ASC" checked>
            </label>
          </div></td>
          <td width="92"><div align="right">
            <label>Nombre
            <input name="radioorden" type="radio" class="sin-borde" value="nombre">
            </label>
          </div></td>
          <td><div align="right">
            <label>Cédula
            <input name="radioorden" type="radio" class="sin-borde" value="cedula">
            </label>
          </div></td>
          <td><div align="right">
            <label>Ruta
            <input name="radioorden" type="radio" class="sin-borde" value="scv_rutas.desrut">
            </label>
          </div></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="24" colspan="3" align="center"><table width="440" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td colspan="2"><strong>Estatus</strong></td>
          <td width="82">&nbsp;</td>
          <td width="79">&nbsp;</td>
          <td width="74">&nbsp;</td>
        </tr>
        <tr>
          <td width="106"><div align="right">
              <label >Todos
                <input name="radiostatus" type="radio" class="sin-borde" value="-" checked>
              </label>
          </div></td>
          <td width="79"><div align="right">
              <label>Registro
                <input name="radiostatus" type="radio" class="sin-borde" value="R">
              </label>
          </div></td>
          <td><div align="right">
              <label>Calculo
                <input name="radiostatus" type="radio" class="sin-borde" value="C">
              </label>
          </div></td>
          <td><div align="right">
              <label>Aprobado
                <input name="radiostatus" type="radio" class="sin-borde" value="P">
              </label>
          </div></td>
          <td>Anulado
            <input name="radiostatus" type="radio" class="sin-borde" value="A"></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="24" colspan="3" align="center"><input name="chkresumen" type="checkbox" class="sin-borde" id="chkresumen" value="1">
      Relacion Mensual Acumulada </td>
    </tr>
    <tr>
      <td height="24" colspan="3" align="center"><div align="right">
        <input name="formato" type="hidden" id="formato" value="<?php print $ls_formato;?>">
        <input name="operacion"   type="hidden"   id="operacion"   value="<?php print $ls_operacion;?>">
		<input name="txtcodestpro1" type="hidden" id="txtcodestpro1" readonly>
	    <input name="txtcodestpro2" type="hidden" id="txtcodestpro2" readonly>
	    <input name="txtcodestpro3" type="hidden" id="txtcodestpro3" readonly>
	    <input name="txtcodestpro4" type="hidden" id="txtcodestpro4" readonly>
	    <input name="txtcodestpro5" type="hidden" id="txtcodestpro5" readonly>
	    <input name="hidestcla"     type="hidden" id="hidestcla" readonly>
      </div></td>
    </tr>
  </table>
  <div align="left"></div>
  <p align="center">&nbsp;</p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function ue_buscarsolicituddesde()
{
	f=document.form1;
	li_leer=f.leer.value;
	ls_destino="REPORTESOLICITUDPAGODESDE";
	if (li_leer==1)
   	{
		window.open("sigesp_scv_cat_sol_via.php?destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=600,height=400,left=50,top=50,location=no,resizable=yes");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_buscarsolicitudhasta()
{
	f=document.form1;
	li_leer=f.leer.value;
	ls_destino="REPORTESOLICITUDPAGOHASTA";
	if (li_leer==1)
   	{
		window.open("sigesp_scv_cat_sol_via.php?destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=600,height=400,left=50,top=50,location=no,resizable=yes");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}
function ue_buscartipodocumento()
{
	ls_destino="CALCULOINT";
	window.open("sigesp_scv_cat_tipodocumentos.php?destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
}
function ue_buscarbeneficiario()
{
	f=document.form1;
	window.open("sigesp_scv_cat_personal.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=600,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_buscarmision()
{
	ls_destino="INTERNACIONAL";
	window.open("sigesp_scv_cat_misiones.php?destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
}
function ue_buscarmisiondestino()
{
	ls_destino="DESTINO";
	window.open("sigesp_scv_cat_misiones.php?destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
}
	function ue_buscarunidad()
	{
		f=document.form1;
		li_leer=f.leer.value;
		if (li_leer==1)
		{
			window.open("sigesp_scv_cat_unidadadm.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=600,height=400,left=50,top=50,location=no,resizable=yes");
		}
		else
		{
			alert("No tiene permiso para realizar esta operacion");
		}
	}

	function uf_mostrar_reporte()
	{
		valido=ue_comparar_intervalo();
		if(valido)
		{
			f=document.form1;
			li_imprimir=f.imprimir.value;
			formato=f.formato.value;
			if(li_imprimir==1)
			{
				ld_desde= f.txtdesde.value;
				ld_hasta= f.txthasta.value;
				ls_coduniadm= f.txtcoduniadm.value;
				ls_tiporeporte=f.cmbbsf.value;
				tipvia=f.cmbtipvia.value;
				codmisori=f.txtcodmis.value;
				codmisdes=f.txtcodmisdes.value;
				codsoldes=f.txtcodsoldes.value;
				codsolhas=f.txtcodsolhas.value;
				codtipdoc=f.txtcodtipdoc.value;
				continente=f.cmbcontinente.value;
				codben=f.txtcodben.value;
				for(i=0;i<f.radioorden.length;i++)
					if(f.radioorden[i].checked) ls_orden=f.radioorden[i].value;
				for(i=0;i<f.radiostatus.length;i++)
					if(f.radiostatus[i].checked) estatus=f.radiostatus[i].value;
				if(f.chkresumen.checked==true)
					resumen=1;
				else
					resumen=0;
				if ((ld_desde!="")&&(ld_hasta!=""))
				{
					if(resumen==1)
					{
						window.open("reportes/sigesp_scv_rpp_listadosolicitudesresumen.php?orden="+ls_orden+"&desde="+ld_desde+"&hasta="+ld_hasta+"&coduniadm="+ls_coduniadm+"&tiporeporte="+ls_tiporeporte+"&tipvia="+tipvia+"&codmisori="+codmisori+"&codmisdes="+codmisdes+"&codsoldes="+codsoldes+"&codsolhas="+codsolhas+"&codtipdoc="+codtipdoc+"&continente="+continente+"&codben="+codben+"&estatus="+estatus+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
					}
					else
					{					
						window.open("reportes/"+formato+"?orden="+ls_orden+"&desde="+ld_desde+"&hasta="+ld_hasta+"&coduniadm="+ls_coduniadm+"&tiporeporte="+ls_tiporeporte+"&tipvia="+tipvia+"&codmisori="+codmisori+"&codmisdes="+codmisdes+"&codsoldes="+codsoldes+"&codsolhas="+codsolhas+"&codtipdoc="+codtipdoc+"&continente="+continente+"&codben="+codben+"&estatus="+estatus+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
					}
				}
				else
				{
					alert("Debe indicar un rango de fechas");
				}
			}
			else
			{
				alert("No tiene permiso para realizar esta operación");
			}
		}
	}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
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
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>

</html>
