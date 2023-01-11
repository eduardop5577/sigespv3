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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_intereses_prestacionantiguedad.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_mesint, $ls_anoint, $ls_nrogacint, $ld_fecviggacint, $li_montasint;
		global $ls_operacion, $lb_existe, $io_fun_nomina, $ls_disabled;
		
		$ls_mesint="";
		$ls_anoint="";
		$ls_nrogacint="";
		$ld_fecviggacint="";
		$li_montasint="";
		$ls_disabled="";
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$lb_existe=$io_fun_nomina->uf_obtenerexiste();
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
		// Fecha Creación: 18/03/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_mesint, $ls_anoint, $ls_nrogacint, $ld_fecviggacint, $li_montasint;
		
		$ls_mesint=$_POST["cmbmesint"];
		if($ls_mesint=="")
		{
			$ls_mesint=$_POST["txtmesint"];
		}
		$ls_anoint=$_POST["cmbanoint"];
		if($ls_anoint=="")
		{
			$ls_anoint=$_POST["txtanoint"];
		}
		$ls_nrogacint=$_POST["txtnrogacint"];
		$ld_fecviggacint=$_POST["txtfecviggacint"];
		$li_montasint=$_POST["txtmontasint"];
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
<title>Definici&oacute;n de Intereses de Prestaci&oacute;n Antiguedad</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body {
	background-color: #EAEAEA;
	margin-left: 0px;
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
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="js/stm31.js"></script>
</head>
<body>
<?php 
	require_once("sigesp_sno.php");
	$io_sno=new sigesp_sno();
	$li_calint=trim($io_sno->uf_select_config("SNO","NOMINA","CALCULO_INT_FIDEICOISO","0","I"));
	if($li_calint=="0")
	{
		print("<script language=JavaScript>");
		print(" alert('El Sistema no está definido para calcular los intereses de Prestación Antiguedad.');");
		print(" location.href='sigespwindow_blank.php'");
		print("</script>");
	}	
	unset($io_sno);
	require_once("sigesp_snorh_c_intereses_prestacionantiguedad.php");
	$io_intereses = new sigesp_snorh_c_intereses_prestacionantiguedad();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "GUARDAR":
			uf_load_variables();
			$lb_valido=$io_intereses->uf_guardar($lb_existe,$ls_mesint,$ls_anoint,$ls_nrogacint,$ld_fecviggacint,$li_montasint,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$lb_existe="FALSE";
			}
			else
			{
				if($lb_existe=="TRUE")
				{
					$ls_disabled="disabled";
				}
			}
			break;

		case "ELIMINAR":
			uf_load_variables();
			$lb_valido=$io_intereses->uf_delete($ls_mesint,$ls_anoint,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$lb_existe="FALSE";
			}
			else
			{
				$ls_disabled="disabled";
			}
			break;
	}
	$io_intereses->uf_destructor();
	unset($io_intereses);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Nómina</td>
			<td width="346" bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
        </table>
	 </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" title="Buscar" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" title="Eliminar" alt="Eliminar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
<div align="center">
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <table width="600" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td>
		  <p>&nbsp;</p>
		  <table width="550" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
            <tr class="titulo-ventana">
              <td height="20" colspan="2"><div align="center">Definici&oacute;n de Intereses de Prestaci&oacute;n Antiguedad </div></td>
            </tr>
            <tr >
              <td height="22">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td width="122" height="22"><div align="right" >
                <p>Mes</p>
              </div></td>
              <td width="456"><div align="left" >
                <select name="cmbmesint" id="cmbmesint" "<?php print $ls_disabled; ?>">
                  <option value="1" selected>Enero</option>
                  <option value="2">Febrero</option>
                  <option value="3">Marzo</option>
                  <option value="4">Abril</option>
                  <option value="5">Mayo</option>
                  <option value="6">Junio</option>
                  <option value="7">Julio</option>
                  <option value="8">Agosto</option>
                  <option value="9">Septiembre</option>
                  <option value="10">Octubre</option>
                  <option value="11">Noviembre</option>
                  <option value="12">Diciembre</option>
                </select>
                <input name="txtmesint" type="hidden" id="txtmesint" value="<?php print $ls_mesint; ?>">
              </div></td>
            </tr>
            <tr >
              <td height="22"><div align="right">A&ntilde;o</div></td>
              <td><div align="left">
                <select name="cmbanoint" id="cmbanoint" "<?php print $ls_disabled; ?>">
                </select>
				<input name="txtanoint" type="hidden" id="txtanoint" value="<?php print $ls_anoint; ?>">
              </div></td>
            </tr>
            <tr>
              <td height="22"><div align="right">Nro de Gaceta </div></td>
              <td><div align="left">
                <input name="txtnrogacint" type="text" id="txtnrogacint" value="<?php print $ls_nrogacint; ?>" size="12" maxlength="6" onKeyUp="ue_validarcomillas(this);">
              </div></td>
            </tr>
            <tr>
              <td height="22"><div align="right">Fecha Vigencia </div></td>
              <td> <div align="left">
                <input name="txtfecviggacint" type="text" id="txtfecviggacint" value="<?php print $ld_fecviggacint;?>" size="15" maxlength="10" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" datepicker="true">
              </div></td>
            </tr>
            <tr>
              <td height="22"><div align="right">Tasa de Inter&eacute;s </div></td>
              <td>
                <div align="left">
                  <input name="txtmontasint" type="text" id="txtmontasint"  style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))" value="<?php  print $li_montasint; ?>" size="22" maxlength="23">
                    </div></td></tr>
            <tr>
              <td height="22"><div align="right"></div></td>
              <td><input name="operacion" type="hidden" id="operacion">
            <input name="existe" type="hidden" id="existe" value="<?php print $lb_existe;?>">            </tr>
          </table>
		  <p>&nbsp;</p></td>
      </tr>
  </table>
</form>  
</div>
</body>
<script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
f=document.form1;
f.cmbanoint.length=0;
var fecha = new Date();
actual = fecha.getFullYear();
i=0;
for(inicio=1970;inicio<=actual;inicio++)
{
	f.cmbanoint.options[i]= new Option(inicio,inicio);
	i++;
}
function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.existe.value="FALSE"
		f.action="sigesp_snorh_d_intereses_prestacionantiguedad.php";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operación");
   	}
}

function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	lb_existe=f.existe.value;
	if(((lb_existe=="TRUE")&&(li_cambiar==1))||(lb_existe=="FALSE")&&(li_incluir==1))
	{
		mesint = ue_validarvacio(f.cmbmesint.value);
		anoint = ue_validarvacio(f.cmbanoint.value);
		mesint1 = ue_validarvacio(f.txtmesint.value);
		anoint1 = ue_validarvacio(f.txtanoint.value);
		if (((mesint!="")&&(anoint!=""))||((mesint1!="")&&(anoint1!="")))
		{
			f.operacion.value ="GUARDAR";
			f.action="sigesp_snorh_d_intereses_prestacionantiguedad.php";
			f.submit();
		}
		else
		{
			alert("Debe llenar todos los datos.");
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
			if(confirm("¿Desea eliminar el Registro actual?"))
			{
				f.operacion.value ="ELIMINAR";
				f.action="sigesp_snorh_d_intereses_prestacionantiguedad.php";
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
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		window.open("sigesp_snorh_cat_intereses_prestacionantiguedad.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_cerrar()
{
	f=document.form1;
	f.action="sigespwindow_blank.php";
	f.submit();
}

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
	//window.open("../hlp/index.php?sistema=SNO&subsistema=SNR&nomfis=sno/sigesp_hlp_snr_cestaticket.php","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

</script>
</html>