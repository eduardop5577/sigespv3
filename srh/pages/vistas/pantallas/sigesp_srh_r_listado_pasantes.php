<?php
/***********************************************************************************
* @fecha de modificacion: 07/09/2022, para la version de php 8.1 
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
require_once("../../../class_folder/utilidades/class_funciones_srh.php");
$io_fun_srh=new class_funciones_srh('../../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_r_listado_pasantes.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Reporte Listado de Pasantes</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
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
<script type="text/javascript"  src="../../../public/js/librerias_comunes.js"></script>



</head>

<body >
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../../../public/imagenes/header.jpg" width="778" height="40"></td>
  </tr>
   <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			  <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Recursos Humanos</td>
			    <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
				<tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td> </tr>
	  	</table>
	 </td>
  </tr>
 <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="../../js/menu/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  
  <tr>
   <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_print();"><img src="../../../../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
   <td class="toolbar" width="24"><div align="center"><a href="javascript: ue_cerrar();"><img src="../../../public/imagenes/salir.gif" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><img src="../../../public/imagenes/ayuda.gif" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="24"><div align="center"></div></td>
    <td class="toolbar" width="618">&nbsp;</td>
  </tr>
</table>
<?php

	

	$arre=$_SESSION["la_empresa"];
	$ls_empresa=$arre["codemp"];
	if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
	}
	else
	{
		$ls_operacion="";
		}


?>

<p>&nbsp;</p>
<div align="center">
  
          <form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_srh->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_srh);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<table width="600" height="138" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="550" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="5" class="titulo-ventana">Reporte Listado de Pasantes </td>
        </tr>
		
        <tr>
          <td height="20" colspan="6" class="titulo-celdanew">Intervalo de Fecha Inicio de Pasant&iacute;a </td>
          </tr>
        <tr>
          <td width="136" height="22"><div align="right"> Desde </div></td>
          <td width="160"><div align="left"><input name="txtfecini1" type="text" id="txtfecini1"   size="16"   style="text-align:center" readonly > <input name="reset" type="reset" onclick="return showCalendar('txtfecini1', '%d/%m/%Y');" value=" ... " /> </div></td>
          <td width="210"><div align="left">Hasta 
            <input name="txtfecfin1" type="text" id="txtfecfin1"   size="16"   style="text-align:center" readonly >
            <input name="reset2" type="reset" onClick="return showCalendar('txtfecfin1', '%d/%m/%Y');" value=" ... " />
          </div></td>
          <td width="36" colspan="3"><div align="left"></div></td>
        </tr>
		 <tr>
          <td height="20" colspan="6" class="titulo-celdanew">Intervalo de Fecha Culminaci&oacute;n de  Pasant&iacute;a </td>
          </tr>
        <tr>
          <td width="136" height="22"><div align="right"> Desde </div></td>
          <td width="160"><div align="left"><input name="txtfecini2" type="text" id="txtfecini2"   size="16"   style="text-align:center" readonly > <input name="reset" type="reset" onclick="return showCalendar('txtfecini2', '%d/%m/%Y');" value=" ... " /> </div></td>
          <td width="210"><div align="left">Hasta 
            <input name="txtfecfin2" type="text" id="txtfecfin2"   size="16"   style="text-align:center" readonly >
            <input name="reset2" type="reset" onClick="return showCalendar('txtfecfin2', '%d/%m/%Y');" value=" ... " />
          </div></td>
          <td width="36" colspan="3"><div align="left"></div></td>
        </tr>


       
        
        <tr>
          <td height="22" colspan="5" class="titulo-celdanew">Estado de la Pasant&iacute;a </td>
          </tr>
        <tr>
          <td height="22"><div align="right">Activa
            
          </div></td>
          <td><div align="left">
            <input name="chkactiva" type="checkbox" class="sin-borde" id="chkactiva" value="1" checked></div></td>
            <td><div align="left">Concluida
            
                <input name="chkconcluida" type="checkbox" class="sin-borde" id="chkconcluida" value="1" checked>
            </div></td>
          
          <td width="36">&nbsp;</td>
        </tr>
       
      
        <tr>
          <td height="20" colspan="5" class="titulo-celdanew"><div align="right" class="titulo-celdanew">Ordenado por </div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">N&uacute;mero de Pasant&iacute;a </div></td>
          <td colspan="4"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="1" checked>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">C&eacute;dula del Pasante</div></td>
          <td colspan="4"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="2">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Nombre del Pasante</div></td>
          <td colspan="4"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="3">
          </div></td>
        </tr>
		<tr>
          <td height="22"><div align="right">Apellido del Pasante</div></td>
          <td colspan="4"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="4">
          </div></td>
        </tr>
        <tr>
          <td height="22">&nbsp;</td>
          <td colspan="4"> <div align="right"></div></td>
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
</html>


<script >


function ue_print()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	fecini1=f.txtfecini1.value;
	fecfin1=f.txtfecfin1.value;
	fecini2=f.txtfecini2.value;
	fecfin2=f.txtfecfin2.value;		
	if(li_imprimir==1)
	{	
		if (f.chkactiva.checked)
		 {
			 estatus='Abierto';
		 }
		if (f.chkconcluida.checked) 
		{
			estatus='Cerrado';
		}
		if ((f.chkconcluida.checked)&&(f.chkactiva.checked)) 
		{
		  estatus='';
		}
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
					
			pagina="../../../reporte/sigesp_srh_rpp_listado_pasantes.php?fecini1="+fecini1+"&fecfin1="+fecfin1
					+"&fecini2="+fecini2+"fecfin2="+fecfin2+"estatus="+estatus+"orden="+orden;	
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
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


//FUNCIONES PARA EL CALENDARIO

// Esta es la funcion que detecta cuando el usuario hace click en el calendario, necesaria
function selected(cal, date) {
  cal.sel.value = date; // just update the date in the input field.
                           
  if (cal.dateClicked )
      cal.callCloseHandler();
}


function closeHandler(cal) {
  cal.hide();                        // hide the calendar

  _dynarch_popupCalendar = null;
}


function showCalendar(id, format, showsTime, showsOtherMonths) {
  var el = document.getElementById(id);
  if (_dynarch_popupCalendar != null) {
    // we already have some calendar created
    _dynarch_popupCalendar.hide();                 // so we hide it first.
  } else {
    // first-time call, create the calendar.

    var cal = new Calendar(1, null, selected, closeHandler);
    if (typeof showsTime == "string") {
      cal.showsTime = true;
      cal.time24 = (showsTime == "24");
    }
    if (showsOtherMonths) {
      cal.showsOtherMonths = true;
    }
    _dynarch_popupCalendar = cal;                  // remember it in the global var
    cal.setRange(1900, 2070);        // min/max year allowed.
    cal.create();
  }
  _dynarch_popupCalendar.setDateFormat(format);    // set the specified date format
  _dynarch_popupCalendar.parseDate(el.value);      // try to parse the text in field
  _dynarch_popupCalendar.sel = el;                 // inform it what input field we use
 _dynarch_popupCalendar.showAtElement(el, "T");        // show the calendar

  return false;
}


</script> 