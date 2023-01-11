<?php
/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
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
	 print "location.href='sigesp_inicio_sesion.php'";
	 print "</script>";		
   }
$ls_logusr = $_SESSION["la_logusr"];
require_once("class_funciones_banco.php");
$io_fun_banco= new class_funciones_banco();
$ls_permisos="";
$la_seguridad=Array();
$la_permisos=Array();
$arrResultado=$io_fun_banco->uf_load_seguridad("SCB","sigesp_scb_r_transferencias_bancarias.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado["as_permisos"];
$la_seguridad=$arrResultado["aa_seguridad"];
$la_permisos=$arrResultado["aa_permisos"];

$ls_reporte   = $io_fun_banco->uf_select_config("SCB","REPORTE","TRANSFERENCIAS","sigesp_scb_rpp_transferencias_bancarias.php","C");
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Listado de Transferencias Bancarias</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="../scb/js/ajax.js"></script>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script type="text/javascript"  src="../shared/js/sigesp_cat_ordenar.js"></script>
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
    <td height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
  <td width="778" height="20" colspan="11" bgcolor="#E7E7E7">
    <table width="778" border="0" align="center" cellpadding="0" cellspacing="0">			
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Caja y Banco</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table>
  </td>
  </tr>
  <tr>
    <td height="20" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_imprimir();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a><a href="javascript:ue_openexcel();"></a><a href="sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" title="Ayuda" width="20" height="20"></td>
  </tr>
</table>
  <?Php
require_once("../shared/class_folder/grid_param.php");
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");

$io_grid    = new grid_param();
$io_conect  = new sigesp_include();
$con        = $io_conect->uf_conectar();
$io_sql     = new class_sql($con);
$io_msg     = new class_mensajes();
$io_funcion = new class_funciones();
$ls_codemp  = $_SESSION["la_empresa"]["codemp"];

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion = $_POST["operacion"];
	$ls_codban    = $_POST["txtcodban"];;
	$ls_denban    = $_POST["txtdenban"];
	$ls_ctaban    = $_POST["txtcuenta"];
	$ls_denctaban = $_POST["txtdenominacion"];
	$ld_fecdes    = $_POST["txtfecdesde"];
	$ld_fechas    = $_POST["txtfechasta"];
    $ls_codope    = $_POST["cmboperacion"];
    $ls_conmov    = $_POST["txtconcepto"];
    $ls_estmov    = $_POST["cmbestmov"];
    $ls_orden     = $_POST["hidorden"];
    $li_totrows   = $_POST["hidtotrows"];
	$ls_numdocmov = $_POST["txtdocumento"];
}
else
{
	$ls_operacion = "";	
	$ld_fecha     = date("d/m/Y");
	$ls_codban    = "";
	$ls_denban    = "";
	$ls_ctaban    = "";
	$ls_denctaban = "";
	$ld_fecdes    = $ld_fecha;
	$ld_fechas    = $ld_fecha;
    $ls_codope    = "T";
    $ls_conmov    = ""; 
    $ls_estmov    = "-";
    $ls_orden     = " ,scb_movbco.numdoc ASC";
    $li_totrows   = 0;
	$ls_numdocmov = "";
}


function uf_load_documentos($as_codemp,$as_codban,$as_ctaban,$as_fecdes,$as_fechas,$ls_numdocmov,$lb_valido)
{
  global $io_sql;
  global $io_funcion;
  
  $lb_valido = true;
  $ls_straux = "";
  $as_fecdes = $io_funcion->uf_convertirdatetobd($as_fecdes);
  $as_fechas = $io_funcion->uf_convertirdatetobd($as_fechas);
  
  if (!empty($as_fecdes) && !empty($as_fechas))
  {
    $ls_straux = $ls_straux." AND scb_movbco.fecmov BETWEEN '".$as_fecdes."' AND '".$as_fechas."'";
  }
  if (!empty($as_estmov) && $as_estmov!='-')
  {
    $ls_straux = $ls_straux." AND scb_movbco.estmov = '".$as_estmov."'";
  }
  if (!empty($ls_numdocmov))
  {
  	$ls_straux = $ls_straux." AND scb_movbco.numdoc LIKE '%".$ls_numdocmov."%'";
  }
  
  if (empty($as_orden))
     {
	   $as_orden = " scb_movbco.numdoc ASC";
	 }
  $ls_sql  = " SELECT scb_movbco.codemp,scb_movbco.codban,scb_movbco.ctaban,scb_movbco.numdoc,scb_movbco.codope,scb_movbco.estmov, ".
  			 " scb_movbco.cod_pro,scb_movbco.ced_bene,scb_movbco.tipo_destino, scb_movbco.nomproben as nombre,scb_movbco.conmov,   ".
             " (scb_movbco.monto - scb_movbco.monret) as monto,scb_movbco.fecmov,scb_banco.nomban as banco,scb_concepto.denconmov ".
             "  FROM scb_movbco ,scb_banco , scb_ctabanco, scb_concepto  ".
             " WHERE scb_movbco.codemp='".$as_codemp."' ".
			 " AND scb_movbco.codban LIKE '%".$as_codban."%' ".
			 " AND scb_movbco.ctaban LIKE '%".$as_ctaban."%' ".
			 " AND scb_movbco.docdestrans<>'---------------' ".
			 " AND scb_movbco.tiptrans='1' ".
			 " $ls_straux 
			   AND scb_movbco.codban=scb_banco.codban 
			   AND scb_movbco.codemp=scb_banco.codemp 
			   AND scb_movbco.codban=scb_ctabanco.codban 
			   AND scb_movbco.ctaban=scb_ctabanco.ctaban       
			   AND scb_movbco.codconmov=scb_concepto.codconmov ".
			 " ORDER BY $as_orden ";//print $ls_sql;

  $rs_data = $io_sql->select($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
     }
  return $rs_data;
}
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
</div>
<p>&nbsp;</p>
<form id="sigesp_scb_r_transferencias_bancarias.php" name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_banco->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_banco);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <table width="535" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
   
    <tr>
      <td width="62"></td>
    </tr>
    <tr class="titulo-ventana">
      <td height="22" colspan="4" align="center">Listado de Transferencias Bancarias </td>
    </tr>
    <tr>
      <td height="13" colspan="4" align="center">&nbsp;</td>
    </tr>
    <tr style="visibility:hidden">
      <td height="13" colspan="4" style="text-align:left">Reporte en
        <select name="cmbbsf" id="cmbbsf">
          <option value="0" selected>Bs.</option>
          <option value="1">Bs.F.</option>
        </select>
        <input name="formato"    type="hidden" id="formato"    value="<?php print $ls_reporte; ?>"></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Banco</td>
      <td height="22" colspan="3" align="center"><div align="left">
        <input name="txtcodban" type="text" id="txtcodban"  style="text-align:center" value="<?php print $ls_codban ?>" size="10" readonly>
        <a href="javascript:cat_bancos();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Bancos"></a>
        <input name="txtdenban" type="text" class="sin-borde" id="txtdenban" value="<?php print $ls_denban ?>" size="60" readonly>
        <span class="Estilo1">
        <input name="operacion"   type="hidden"   id="operacion"   value="<?php print $ls_operacion;?>">
        </span></div></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Cuenta</td>
      <td height="22" colspan="3" align="center"><div align="left">
        <input name="txtcuenta" type="text" id="txtcuenta" style="text-align:center" value="<?php print $ls_ctaban ?>" size="30" maxlength="25" readonly>
          <a href="javascript:catalogo_cuentabanco();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Cuentas Bancarias"></a>
          <input name="txtdenominacion" type="text" class="sin-borde" id="txtdenominacion" style="text-align:left" value="<?php print $ls_denctaban ?>" size="45" maxlength="254" readonly>
      </div></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">N° de Documento</td>
      <td height="22" colspan="3" align="center"><div align="left">
        <input name="txtdocumento" type="text" id="txtdocumento" style="text-align:center" value="<?php print $ls_numdocmov ?>" size="30" maxlength="25" readonly>
          <a href="javascript:catalogo_numdoc();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de N&uacute;meros de Documentos"></a>
      </div></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Desde</td>
      <td width="146" height="22" align="center"><div align="left">
        <input name="txtfecdesde" type="text" id="txtfecdesde"  style="text-align:center" onKeyPress="currencyDate(this);" value="<?php print $ld_fecdes ?>" size="20" maxlength="10"  datepicker="true">
      </div></td>
      <td width="80" height="22" style="text-align:right">Hasta</td>
      <td width="245" height="22" align="center"><div align="left">
        <input name="txtfechasta" type="text" id="txtfechasta" style="text-align:center" onKeyPress="currencyDate(this);" value="<?php print $ld_fechas ?>" size="20" maxlength="10"  datepicker="true">
      </div></td>
    </tr>
    <tr>
      <td height="22" colspan="4" align="center">
      <p align="right"><a href="javascript:ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0">Buscar</a></p></td>
    </tr>
  </table>
 
</table>
</p>
<p align="center">
  <?php
if ($ls_operacion=="BUSCAR")
   {
	 $lb_valido = true;
	 $li_fila   = 0;
	 $rs_data   = uf_load_documentos($ls_codemp,$ls_codban,$ls_ctaban,$ld_fecdes,$ld_fechas,$ls_numdocmov,$lb_valido);
	 if ($lb_valido)
	    {
	      $title[1] = ""; 
		  $title[2] = "<a href=javascript:ue_ordenar('scb_movbco.numdoc');><font color=#FFFFFF>Documento</font></a>"; 
		  $title[3] = "<a href=javascript:ue_ordenar('scb_movbco.codope');><font color=#FFFFFF>Operacion</font>"; 
		  $title[4] = "<a href=javascript:ue_ordenar('nombre');><font color=#FFFFFF>Beneficiario</font></a>"; 
		  $title[5] = "<a href=javascript:ue_ordenar('scb_movbco.fecmov');><font color=#FFFFFF>Fecha</font></a>"; 
		  $title[6] = "<font color=#FFFFFF>Monto</font>"; 
		  $ls_grid  = "grid_documentos";
		  if (!$rs_data->EOF){
		  		
		       while (!$rs_data->EOF){
				       $li_fila++;
					   $ls_codemp = trim($rs_data->fields["codemp"]);
			           $ls_numdoc = trim($rs_data->fields["numdoc"]);
				   	   $ls_codope = $rs_data->fields["codope"];
				       $ls_estdoc = $rs_data->fields["estmov"];
				       $ls_codpro = $rs_data->fields["cod_pro"];
				       $ls_cedben = $rs_data->fields["ced_bene"];
				       $ls_tipdes = $rs_data->fields["tipo_destino"];
				       $ls_nombre = $rs_data->fields["nombre"];
				       $ls_conmov = $rs_data->fields["conmov"];
				       $ld_monmov = $rs_data->fields["monto"];
				       $ls_fecmov = $io_funcion->uf_formatovalidofecha($rs_data->fields["fecmov"]);
					   $ls_fecmov = $io_funcion->uf_convertirfecmostrar($ls_fecmov);
		               $object[$li_fila][1]="<input type=checkbox name=chkimprimir".$li_fila.">";
					   $object[$li_fila][2]="<input type=text      id=txtnumdoc".$li_fila."  name=txtnumdoc".$li_fila."  value='".$ls_numdoc."'  class=sin-borde  size=15  style=text-align:center readonly>";
					   $object[$li_fila][3]="<input type=text      id=txttipope".$li_fila."  name=txttipope".$li_fila."  value='".$ls_codope."'  class=sin-borde  size=5   style=text-align:center readonly>";
					   $object[$li_fila][4]="<input type=text      id=txtnombre".$li_fila."  name=txtnombre".$li_fila."  value='".$ls_nombre."'  class=sin-borde  size=40  style=text-align:left   readonly>"; 
					   $object[$li_fila][5]="<input type=text      id=txtfecha".$li_fila."   name=txtfecha".$li_fila."   value='".$ls_fecmov."'  class=sin-borde  size=8   style=text-align:center readonly>";
		               $object[$li_fila][6]="<input type=text      id=txtmonto".$li_fila."   name=txtmonto".$li_fila."   value='".number_format($ld_monmov,2,',','.')."'  class=sin-borde  size=20  style=text-align:right   readonly>";
		               $rs_data->MoveNext();
			   }
		       $io_grid->make_gridScroll($li_fila,$title,$object,570,'Listado de Transferencias',$ls_grid,200);
		  }
          else
	      {
	        $io_msg->message("No se han encontrado Documentos para este Criterio de Búsqueda, recuerde que el documento debe ser una transferencia !!!");
		  }
        }
   }    
?>
</p>
<div align="center">
  <input name="hidtotrows" type="hidden" id="hidtotrows" value="<?php print $li_totrows ?>">
</div>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
f = document.form1;

function uf_catalogoprov()
{
    f.operacion.value="BUSCAR";
    pagina="sigesp_catdin_prove.php";
    window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=520,height=400,resizable=yes,location=no");
}

function rellenar_cad(cadena,longitud,objeto)
{
	var mystring=new String(cadena);
	cadena_ceros="";
	lencad=mystring.length;

	total=longitud-lencad;
	if (cadena!="")
	{
		for (i=1;i<=total;i++)
		{
		   cadena_ceros=cadena_ceros+"0";
		}
		cadena=cadena_ceros+cadena;
		if (objeto=="txtcodprov1")
		{
		    document.form1.txtcodprov1.value=cadena;
		}
		else
		{
			document.form1.txtcodprov2.value=cadena;
		}  
     }
}

 function currencyDate(date)
  { 
	ls_date=date.value;
	li_long=ls_date.length;
			 
		if(li_long==2)
		{
			ls_date=ls_date+"/";
			ls_string=ls_date.substr(0,2);
			li_string=parseInt(ls_string,10);

			if((li_string>=1)&&(li_string<=31))
			{
				date.value=ls_date;
			}
			else
			{
				date.value="";
			}
			
		}
		if(li_long==5)
		{
			ls_date=ls_date+"/";
			ls_string=ls_date.substr(3,2);
			li_string=parseInt(ls_string,10);
			if((li_string>=1)&&(li_string<=12))
			{
				date.value=ls_date;
			}
			else
			{
				date.value=ls_date.substr(0,3);
			}
		}
		if(li_long==10)
		{
			ls_string=ls_date.substr(6,4);
			li_string=parseInt(ls_string,10);
			if((li_string>=1900)&&(li_string<=2090))
			{
				date.value=ls_date;
			}
			else
			{
				date.value=ls_date.substr(0,6);
			}
		}
   }

	function catalogo_cuentabanco()
	 {
	   f=document.form1;
	   ls_codban=f.txtcodban.value;
	   ls_nomban=f.txtdenban.value;
	  	   if((ls_codban!=""))
		   {
			   pagina="sigesp_cat_ctabanco.php?codigo="+ls_codban+"&hidnomban="+ls_nomban;
			   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=730,height=400,resizable=yes,location=no");
		   }
		   else
		   {
				alert("Debe Seleccionar un Banco !!!");   
		   }
	  
	 }
	 
	 function catalogo_numdoc()
	 {
	   f=document.form1;
	   ls_ctaban=f.txtcuenta.value;
	   ls_codban=f.txtcodban.value;
	   	   if((ls_ctaban!=""))
		   {
			   pagina="sigesp_cat_numdoc.php?codigo="+ls_ctaban+"&banco="+ls_codban;
			   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=730,height=400,resizable=yes,location=no");
		   }
		   else
		   {
				alert("Debe Seleccionar una Cuenta !!!");   
		   }
	  
	 }	
	 	 
	 function cat_bancos()
	 {
	   f=document.form1;
	   pagina="sigesp_cat_bancos.php";
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
	 }

	function cat_conceptos()
	{
	   f=document.form1;
	   ls_codope=f.cmboperacion.value;
	   pagina="sigesp_cat_conceptos.php?codope="+ls_codope;
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
	}

function ue_search()
{
  li_leer = f.leer.value;
  if (li_leer==1)
  {
    f.operacion.value = "BUSCAR";
    f.action          = "sigesp_scb_r_transferencias_bancarias.php";
	f.submit();
  }
  else
  {
	alert("No tiene permiso para realizar esta operación !!!");
  }
}

function ue_calcular_total_fila_local(campo)
{
	existe=true;
	li_i=1;
	while(existe)
	{
		existe=document.getElementById(campo+li_i);
		if(existe!=null)
		{
			li_i=li_i+1;
		}
		else
		{
			existe=false;
			li_i=li_i-1;
		}
	}
	return li_i;
}

function ue_imprimir()
{
  li_imprimir 	 = f.imprimir.value;
  ld_fecdesde 	 = f.txtfecdesde.value;
  ld_fechasta 	 = f.txtfechasta.value;
  ls_codban   	 = f.txtcodban.value;
  ls_nomban   	 = f.txtdenban.value;
  ls_ctaban   	 = f.txtcuenta.value;
  ls_tiporeporte = f.cmbbsf.value;
  ls_reporte     = f.formato.value;
  ls_numdoc      = f.txtdocumento.value
  ls_docs		 = "";
  ls_chkmarc=0;
  if (li_imprimir=='1')
     {
  	   	total=ue_calcular_total_fila_local("txtnumdoc");
		for (i=1;i<=total;i++)
		{
			if (eval("f.chkimprimir"+i+".checked==true"))
			{
				ls_chkmarc=1;
				ls_numdoc=eval("f.txtnumdoc"+i+".value");
				if (ls_docs.length>0)
				{
					ls_docs=ls_docs+">>"+ls_numdoc;
				}
				else
				{
					ls_docs=ls_numdoc;
				}
			}
		}
	   if (ls_chkmarc==0)
	   {
	   	 alert("Debe tildar por lo menos una transferencia bancaria!");
	   }
	   else
	   {
		   ls_codban = f.txtcodban.value;
		   ls_ctaban = f.txtcuenta.value;
		   pagina="reportes/"+ls_reporte+"?fecdes="+ld_fecdesde+"&fechas="+ld_fechasta+"&codban="+ls_codban+"&ctaban="+ls_ctaban+"&nomban="+ls_nomban+"&tiporeporte="+ls_tiporeporte+"&documento="+ls_numdoc+"&chked="+ls_chkmarc+"&chkados="+ls_docs;
		   window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	   }
	 }
  else
	 {
	   alert("No tiene permiso para realizar esta operación !!!");
	 }
}

function ue_openexcel()
{
  li_imprimir 	 = f.imprimir.value;
  ld_fecdesde 	 = f.txtfecdesde.value;
  ld_fechasta 	 = f.txtfechasta.value;
  ls_codope      = f.cmboperacion.value;
  ls_codban      = f.txtcodban.value;
  ls_nomban      = f.txtdenban.value;
  ls_ctaban      = f.txtcuenta.value;
  ls_concepto    = f.txtcodconcep.value;
  ls_orden       = f.hidorden.value;
  ls_estmov      = f.cmbestmov.value;
  ls_tiporeporte = f.cmbbsf.value;
  ls_numdoc      = f.txtdocumento.value
  ls_docs		 = "";
  ls_chkmarc=0;
  if (li_imprimir=='1')
     {
   	   	total=ue_calcular_total_fila_local("txtnumdoc");
		for (i=1;i<=total;i++)
		{
			if (eval("f.chkimprimir"+i+".checked==true"))
			{
				ls_chkmarc=1;
				ls_numdoc=eval("f.txtnumdoc"+i+".value");
				if (ls_docs.length>0)
				{
					ls_docs=ls_docs+">>"+ls_numdoc;
				}
				else
				{
					ls_docs=ls_numdoc;
				}
			}
		}
	   ls_codban = f.txtcodban.value;
       ls_ctaban = f.txtcuenta.value;
	   if (ls_codban=="" || ls_ctaban=="" || ls_nomban=="")
	      {
		    alert("Debe establecer el Código del Banco y Número de Cuenta Bancaria para realizar la Búsqueda !!!");  
		  }
       else
	      {
 	        pagina="reportes/sigesp_scb_rpp_documentos_excel.php?fecdes="+ld_fecdesde+"&fechas="+ld_fechasta+"&codope="+ls_codope+"&codban="+ls_codban+"&ctaban="+ls_ctaban+"&codconcep="+ls_concepto+"&orden="+ls_orden+"&hidestmov="+ls_estmov+"&nomban="+ls_nomban+"&tiporeporte="+ls_tiporeporte+"&documento="+ls_numdoc+"&chked="+ls_chkmarc+"&chkados="+ls_docs;
	        window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		  }  
	 }
  else
	 {
	   alert("No tiene permiso para realizar esta operación !!!");
	 }
}	
</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>