<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
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
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_permisos="";
	$la_seguridad = Array();
	$la_permisos = Array();	
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_deudaanterior.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   
   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/11/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ld_feccordeu,$li_monpreant,$li_monint,$li_monant,$io_fun_nomina,$ls_operacion,$ls_existe,$li_monantint;
		
		$ld_feccordeu="dd/mm/aaaa";
		$li_monpreant="0,00";
		$li_monint="0,00";
		$li_monant="0,00";
		$li_monantint="0,00";
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$ls_existe=$io_fun_nomina->uf_obtenerexiste();
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/11/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codper, $ls_nomper, $ld_feccordeu,$li_monpreant,$li_monint,$li_monant,$li_monantint;
		global $ld_fecnacper,$ld_fecingper, $ld_fecingadmpubper; 
		
		$ls_codper=$_POST["txtcodper"];
		$ls_nomper=$_POST["txtnomper"];
		$ld_fecnacper=$_POST["txtfecnacper"];
		$ld_fecingper=$_POST["txtfecingper"];
		$ld_fecingadmpubper=$_POST["txtfecingadmpubper"];
		$ld_feccordeu=$_POST["txtfeccordeu"];
		$li_monpreant=$_POST["txtmonpreant"];
		$li_monint=$_POST["txtmonint"];
		$li_monant=$_POST["txtmonant"];
		$li_monantint=$_POST["txtmonantint"];
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title >Definici&oacute;n de Deuda Anterior</title>
<meta http-equiv="imagetoolbar" content="no"> 
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
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_snorh_c_deudaanterior.php");
	$io_deudaanterior=new sigesp_snorh_c_deudaanterior();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "NUEVO":
		 	$ls_codper=$_GET["codper"];
			$ls_nomper=$_GET["nomper"];
			$ld_fecnacper=$_GET["fecnacper"];
			$ld_fecingper=$_GET["fecingper"];
			$ld_fecingadmpubper=$_GET["fecingadmpubper"];
			$arrResultado=$io_deudaanterior->uf_load_deudaanterior($ls_codper,$ld_feccordeu,$li_monpreant,$li_monint,$li_monant,$li_monantint);
			$ld_feccordeu=$arrResultado['ad_feccordeu'];
			$li_monpreant=$arrResultado['ai_monpreant'];
			$li_monint=$arrResultado['ai_monint'];
			$li_monant=$arrResultado['ai_monant'];
			$li_monantint=$arrResultado['ai_monantint'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				$ls_existe="TRUE";
			}
			break;

		case "GUARDAR":
			uf_load_variables();
			$lb_valido=$io_deudaanterior->uf_guardar($ls_codper,$ld_feccordeu,$li_monpreant,$li_monint,$li_monant,$li_monantint,$la_seguridad);
			if($lb_valido)
			{
				$arrResultado=$io_deudaanterior->uf_load_deudaanterior($ls_codper,$ld_feccordeu,$li_monpreant,$li_monint,$li_monant,$li_monantint);
				$ld_feccordeu=$arrResultado['ad_feccordeu'];
				$li_monpreant=$arrResultado['ai_monpreant'];
				$li_monint=$arrResultado['ai_monint'];
				$li_monant=$arrResultado['ai_monant'];
				$li_monantint=$arrResultado['ai_monantint'];
				$lb_valido=$arrResultado['lb_valido'];
				$ls_codper=$_POST["txtcodper"];
				$ls_nomper=$_POST["txtnomper"];
				$ls_existe="TRUE";
			}
			break;

		case "ELIMINAR":
			uf_load_variables();
			$lb_valido=$io_deudaanterior->uf_delete_deudaanterior($ls_codper,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_codper=$_POST["txtcodper"];
				$ls_nomper=$_POST["txtnomper"];
				$ls_existe="FALSE";
			}
			break;
	}
	$io_deudaanterior->uf_destructor();
	unset($io_deudaanterior);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_nomina">Sistema de Nómina</td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequeñas"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	</table>	 </td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td width="25" height="20" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"></a><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" title="Eliminar" alt="Eliminar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"></a><a href="javascript: ue_volver();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>

<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigesp_snorh_d_personal.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="550" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td>	<p>&nbsp;</p>      <table width="500" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
      <tr>
        <td colspan="3"><div align="center">
            <input name="txtnomper" type="text" class="sin-borde2" id="txtnomper" value="<?php print $ls_nomper;?>" size="60" readonly>
            <input name="txtcodper" type="hidden" id="txtcodper" value="<?php print $ls_codper;?>">
        </div></td>
      </tr>
      <tr class="titulo-ventana">
        <td height="20" colspan="3" class="titulo-ventana">Definici&oacute;n de Deuda Anterior </td>
      </tr>
      <tr>
        <td width="168" height="22">&nbsp;</td>
        <td width="326" colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td height="22"><div align="right">Fecha de Corte  </div></td>
        <td colspan="2"><div align="left">
          <select name="cmbmes" id="cmbmes">
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
          <select name="cmbano" id="cmbano" onChange="javascript: ue_cargar_fecha();">
          </select>
          <input name="txtfeccordeu" type="text" id="txtfeccordeu" value="<?php print $ld_feccordeu;?>" size="15" maxlength="10" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" readonly>
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Antiguedad </div></td>
        <td colspan="2"><div align="left">
          <input name="txtmonpreant" type="text" id="txtmonpreant" value="<?php print $li_monpreant;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))">
        </div></td>
        </tr>
      <tr>
        <td height="22"><div align="right">Intereses </div></td>
        <td colspan="2"><div align="left">
          <input name="txtmonint" type="text" id="txtmonint" value="<?php print $li_monint;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Anticipos Otorgados  </div></td>
        <td colspan="2"><div align="left">
          <input name="txtmonant" type="text" id="txtmonant" value="<?php print $li_monant;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Intereses Pagados  </div></td>
        <td colspan="2"><input name="txtmonantint" type="text" id="txtmonantint" value="<?php print $li_monantint;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))"></td>
      </tr>
      <tr>
        <td><div align="right"></div></td>
        <td colspan="2"><input name="operacion" type="hidden" id="operacion">
            <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
            <input name="txtfecingper" type="hidden" id="txtfecingper" value="<?php print $ld_fecingper;?>">			
			<input name="txtfecingadmpubper" type="hidden" id="txtfecingadmpubper" value="<?php print $ld_fecingadmpubper;?>">			
            <input name="txtfecnacper" type="hidden" id="txtfecnacper" value="<?php print $ld_fecnacper;?>"></td>
      </tr>
	  
	  
    </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>
<p>&nbsp;</p>
</body>
<script >
f=document.form1;
f.cmbano.length=0;
var fecha = new Date();
actual = fecha.getFullYear();
i=0;
for(inicio=1970;inicio<=actual;inicio++)
{
	f.cmbano.options[i]= new Option(inicio,inicio);
	i++;
}

function ue_volver()
{
	f=document.form1;
	f.operacion.value="BUSCAR";
	f.existe.value="TRUE";	
	codper=ue_validarvacio(f.txtcodper.value);
	f.action="sigesp_snorh_d_personal.php?codper="+codper;
	f.submit();
}

function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	lb_existe=f.existe.value;
	if(((lb_existe=="TRUE")&&(li_cambiar==1))||(lb_existe=="FALSE")&&(li_incluir==1))
	{
		valido=true;
		codper = ue_validarvacio(f.txtcodper.value);
		f.txtfeccordeu.value=ue_validarfecha(f.txtfeccordeu.value);	
		feccordeu = ue_validarvacio(f.txtfeccordeu.value);
		monpreant = ue_validarvacio(f.txtmonpreant.value);
		monint = ue_validarvacio(f.txtmonint.value);
		monant = ue_validarvacio(f.txtmonant.value);
		fecnacper=ue_validarvacio(f.txtfecnacper.value);	
		fecingper=ue_validarvacio(f.txtfecingper.value);
		fecingadmpubper=ue_validarvacio(f.txtfecingadmpubper.value);
		if(!ue_comparar_fechas(fecnacper,feccordeu))
		{
			alert("La fecha de la Deuda Anterior es menor que la de Nacimiento del personal.");
			valido=false;
		}
		if(valido)
		{
			if ((codper!="")&&(feccordeu!=""))
			{
				f.operacion.value="GUARDAR";
				f.action="sigesp_snorh_d_deudaanterior.php";
				f.submit();
			}
			else
			{
				alert("Debe llenar todos los datos.");
			}
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1)
	{	
		if(f.existe.value=="TRUE")
		{
			codper = ue_validarvacio(f.txtcodper.value);
			if (codper!="")
			{
				if(confirm("¿Desea eliminar el Registro actual?"))
				{
					f.operacion.value="ELIMINAR";
					f.action="sigesp_snorh_d_deudaanterior.php";
					f.submit();
				}
			}
			else
			{
				alert("Debe buscar el registro a eliminar.");
			}
		}
		else
		{
			alert("Debe buscar el registro a eliminar.");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_cargar_fecha()
{
	f=document.form1;
	f.txtfeccordeu.value="01/"+f.cmbmes.value+"/"+f.cmbano.value;
}

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
//	window.open("../hlp/index.php?sistema=SNO&subsistema=SNR&nomfis=sno/sigesp_hlp_snr_personal.php","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
</script>
</html>