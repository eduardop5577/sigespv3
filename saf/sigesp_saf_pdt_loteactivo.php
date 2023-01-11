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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Detalle de Lote de Activo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../../shared/css/tablas.css"  rel="stylesheet" type="text/css">
<link href="../../shared/css/ventanas.css" rel="stylesheet" type="text/css">
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
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
</head>
<body>
<br>
<?php
require_once("../base/librerias/php/general/sigesp_lib_include.php");
$io_in=new sigesp_include();
$con=$io_in->uf_conectar();
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$io_sql=new class_sql($con);
require_once("../shared/class_folder/grid_param.php");
$in_grid=new grid_param();
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
$io_msg=new class_mensajes();
require_once("sigesp_saf_c_movimiento.php");
$io_saf=new sigesp_saf_c_movimiento();
require_once("class_funciones_activos.php");
$io_funciones=new class_funciones_activos();


$la_emp=$_SESSION["la_empresa"];
if (array_key_exists("operacion",$_POST))
   {
	 $ls_operacion=$_POST["operacion"];
   }
else
   {
	 $ls_operacion="";	
   }
if (array_key_exists("totrow",$_GET))
   {
     $li_gridtotrows = $_GET["totrow"];
   }
else
   {
     $li_gridtotrows = "";
   }
?>
<form name="form1" method="post" action="">
  <table width="576" height="134" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td height="13" colspan="3" class="titulo-ventana"><span>Detalle de Lote de Activo </span></td>
    </tr>
    <tr class="formato-blanco">
      <td height="13" colspan="3">&nbsp;</td>
    </tr>
    <tr class="formato-blanco">
      <td height="22" colspan="3"><div align="center">
        <table width="480" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="120" height="22"><div align="right">Activo</div></td>
            <td width="120" height="22"><div align="left">
              <input name="txtcodact" type="text" id="txtcodact" size="20">
            </div></td>
            <td width="120" height="22"><div align="right">Serial</div></td>
            <td width="120" height="22"><div align="left">
              <input name="txtseract" type="text" id="txtseract" size="20">
            </div></td>
          </tr>
          <tr>
            <td width="120" height="22"><div align="right">Descripci&oacute;n</div></td>
            <td width="120" height="22"><div align="left">
              <input name="txtdenact" type="text" id="txtdenact" size="20">
            </div></td>
            <td width="120" height="22"><div align="right">Identificador</div></td>
            <td width="120" height="22"><div align="left">
              <input name="txtideact" type="text" id="txtideact" size="20">
            </div></td>
          </tr>
          <tr>
            <td width="120" height="22"><div align="right">Chapa</div></td>
            <td width="120" height="22"><div align="left">
              <input name="txtidchapa" type="text" id="txtidchapa" size="20">
            </div></td>
            <td width="120" height="22"><div align="right"></div></td>
            <td width="120" height="22"><div align="left"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0">Buscar</a></div></td>
          </tr>
        </table>
      </div></td>
    </tr>
    <tr class="formato-blanco">
      <td height="13" colspan="3">&nbsp;</td>
    </tr>
    <tr class="formato-blanco">
      <td height="22" colspan="3" align="center">
<table width="575" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="489">
<?php
	$ls_opener = "";
	if (array_key_exists("opener",$_GET))
	{
		$ls_opener = $_GET["opener"];
	}	
	if (array_key_exists("ls_coduniadmcede",$_GET))
	{
		$ls_coduniadm = $_GET["ls_coduniadmcede"];
	}	
	$la_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_logusr=$_SESSION["la_logusr"];
	$ls_sistema="SAF";
	$ls_ventanas="sigesp_saf_d_incorporacioneslote.php";

	$la_seguridad["empresa"]=$la_codemp;
	$la_seguridad["logusr"]=$ls_logusr;
	$la_seguridad["sistema"]=$ls_sistema;
	$la_seguridad["ventanas"]=$ls_ventanas;


	$ls_titletable="Catálogo de Activos";
	$li_widthtable=520;
	$ls_nametable="grid";
	$lo_title[1]="Agregar";
	$lo_title[2]="Código";
	$lo_title[3]="Serial";
	$lo_title[4]="Denominación";
	$lo_title[5]="Id Activo"; 
	$lo_title[6]="Monto"; 
	$grid1="grid";	
	$li_totrows="";
	$lo_object=array();
	$ls_codemp=$la_emp["codemp"];
	
	switch ($ls_operacion) 
	{
		case "":
			if ($ls_opener=='acta' || $ls_opener=='autorizacion')
			{
				$ls_codact=$io_funciones->uf_obtenervalor('txtcodact','');
				$ls_denact=$io_funciones->uf_obtenervalor('txtdenact','');
				$ls_seract=$io_funciones->uf_obtenervalor('txtseract','');
				$ls_ideact=$io_funciones->uf_obtenervalor('txtideact','');
				$ls_idchapa=$io_funciones->uf_obtenervalor('txtidchapa','');
				$arrResultado=$io_saf->uf_saf_load_activos_cedente($ls_codemp,$ls_coduniadm,$ls_codact,$ls_denact,$ls_seract,$ls_ideact,$ls_idchapa,$li_totrows,$lo_object);
				$li_totrows=$arrResultado['ai_totrows'];
				$lo_object=$arrResultado['ao_object'];
				$lb_valido=$arrResultado['lb_valido'];
			}
			else
			{
				$arrResultado=$io_saf->uf_saf_load_activos($ls_codemp,$li_totrows,$lo_object);
				$li_totrows=$arrResultado['ai_totrows'];
				$lo_object=$arrResultado['ao_object'];
				$lb_valido=$arrResultado['lb_valido'];
			}
			if (!$lb_valido)
			{
				$io_msg->message("No hay registros");
			}
		break;
	}// fin switch

	$in_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
?></td>
  </tr>
</table>
        <div align="center"></div></td>
    </tr>
    <tr class="formato-blanco">
      <td width="332" height="28"><div align="right">
          <input name="opener" type="hidden" id="opener">
          <input name="totalfilasgrid" type="hidden" id="totalfilasgrid" value="<?php print $li_gridtotrows;?>">
          <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
          <input name="operacion" type="hidden" id="operacion">
      </div></td>
      <td width="242" height="22" colspan="2" align="right"><div align="right"> <a href="javascript: ue_cancelar();">
        <input name="txtcoduniadm" type="hidden" id="txtcoduniadm" value="<?php print $ls_coduniadm; ?>">
        <img src="../shared/imagebank/tools15/eliminar.gif" alt="Cancelar" width="15" height="15" border="0"></a><a href="javascript: ue_cancelar();">Cerrar</a> </div></td>
    </tr>
  </table>
  <p align="center">&nbsp;</p>
  <p align="center">    <span class="Estilo1"></span>  </p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">

function ue_agregar(li_row)
{
	f=document.form1;
	lb_valido=true;
	li_totrows=f.totalfilas.value;
	li_gridtotrows=eval("opener.document.form1.totalfilas.value");

	ls_codact=eval("f.txtcodact"+li_row+".value");
	ls_ideact=eval("f.txtideact"+li_row+".value");
	for(li_j=1; (li_j<=li_gridtotrows)&& lb_valido; li_j++)
	{
		ls_codactgrid=eval("opener.document.form1.txtcodact"+li_j+".value");
		ls_ideactgrid=eval("opener.document.form1.txtidact"+li_j+".value");
		if((ls_codactgrid==ls_codact)&&(ls_ideactgrid==ls_ideact))
		{
			alert("El activo ya esta en el movimiento");
			lb_valido=false;
			
		}
	}
	if (lb_valido)
	{
//		li_gridtotrows=eval("opener.document.form1.totalfilas.value");
		ls_codact=eval("f.txtcodact"+li_row+".value");
		ls_denact=eval("f.txtdenact"+li_row+".value");
		ls_ideact=eval("f.txtideact"+li_row+".value");
		li_monact=eval("f.txtmonact"+li_row+".value");
		obj=eval("opener.document.form1.txtcodact"+li_gridtotrows+"");
		obj.value=ls_codact;
		obj=eval("opener.document.form1.txtdenact"+li_gridtotrows+"");
		obj.value=ls_denact;
		obj=eval("opener.document.form1.txtidact"+li_gridtotrows+"");
		obj.value=ls_ideact;
		obj=eval("opener.document.form1.txtmonact"+li_gridtotrows+"");
		obj.value=li_monact;
		opener.document.form1.operacion.value="AGREGARDETALLE";
		opener.document.form1.submit();
//		close();
	}
}
	
function ue_buscar()
{
	f=document.form1;
	totrow=opener.document.form1.totalfilas.value;
  	f.action="sigesp_saf_pdt_loteactivo.php?opener=<?PHP print $ls_opener;?>&operacion=<?PHP print $ls_operacion;?>&ls_coduniadmcede=<?php print $ls_coduniadm; ?>&totrow="+totrow+"";
  	f.submit();
}
	function ue_cancelar()
	{
		window.close();
	}

</script>
</html>
