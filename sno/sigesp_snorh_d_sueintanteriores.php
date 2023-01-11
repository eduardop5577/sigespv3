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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_sueintanteriores.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		global $ls_existe,$ls_operacion,$io_fun_nomina,$li_suelbase,$li_suelint,$li_bonvac,$li_bonfinanio,$li_otrasig,$ld_feccordeu;
		
		$li_suelbase="0,00";
		$li_suelint="0,00";
		$li_bonvac="0,00";
		$li_bonfinanio="0,00";
		$li_otrasig="0,00";
		$ld_feccordeu="dd/mm/aaaa";
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
		// Fecha Creación: 18/03/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codper, $ls_nomper, $ld_fecnacper, $ld_fecingper,$ld_feccordeu,$li_suelbase,$li_suelint,$li_bonvac,$li_bonfinanio,$li_otrasig,$ls_hidmes,$ls_hidano;
		
		$ls_codper=$_POST["txtcodper"];
		$ls_nomper=$_POST["txtnomper"];
		$ld_fecnacper=$_POST["txtfecnacper"];
		$ld_fecingper=$_POST["txtfecingper"];
		$ld_feccordeu=$_POST["txtfeccordeu"];
		$li_suelbase=$_POST["txtsuelbase"];
		$li_suelint=$_POST["txtsuelint"];
		$li_bonvac=$_POST["txtbonvac"];
		$li_bonfinanio=$_POST["txtbonfinanio"];
		$li_otrasig=$_POST["txtotrasig"];
		$ls_hidmes=$_POST["txthidmes"];
		$ls_hidano=$_POST["txthidano"];
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
<title >Sueldos Integrales Anteriores</title>
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
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
.Estilo3 {color: #333333}
-->
</style>
</head>
<body>
<?php 
	require_once("sigesp_snorh_c_sueintanteriores.php");
	$io_sueant=new sigesp_snorh_c_sueintanteriores();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "NUEVO":
		 	$ls_codper=$_GET["codper"];
			$ls_nomper=$_GET["nomper"];
			$ld_fecnacper=$_GET["fecnacper"];
			$ld_fecingper=$_GET["fecingper"];
			break;

		case "GUARDAR":
			uf_load_variables();
			$lb_valido=$io_sueant->uf_guardar($ls_existe,$ls_codper,$ls_hidano,$ls_hidmes,$li_suelbase,$li_suelint,$li_bonvac,$li_bonfinanio,$li_otrasig,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_existe="FALSE";
				$ls_codper=$_POST["txtcodper"];
				$ls_nomper=$_POST["txtnomper"];
			}
			break;

		case "ELIMINAR":
			uf_load_variables();
			$lb_valido=$io_sueant->uf_delete_sueldoanterior($ls_codper,$ls_hidano,$ls_hidmes,$li_suelbase,$li_suelint,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_existe="FALSE";
				$ls_codper=$_POST["txtcodper"];
				$ls_nomper=$_POST["txtnomper"];
			}
			break;
	}
	$io_sueant->uf_destructor();
	unset($io_sueant);
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
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif"  title="Buscar" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" title="Eliminar" alt="Eliminar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_volver();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif"  title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
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
<table width="650" height="138" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="600" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr>
          <td colspan="2" class="sin-borde2"><input name="txtnomper" type="text" class="sin-borde2" id="txtnomper" value="<?php print $ls_nomper;?>" size="60" readonly>
            <input name="txtcodper" type="hidden" id="txtcodper" value="<?php print $ls_codper;?>"></td>
        </tr>
        <tr class="titulo-ventana">
          <td height="20" colspan="2" class="titulo-ventana">Definici&oacute;n de Sueldo Integral Anterior </td>
        </tr>
        <tr>
          <td width="135" height="22">&nbsp;</td>
          <td width="459">&nbsp;</td>
        </tr>
        <tr>
          <td height="33"><div align="right">Fecha</div></td>
          <td><div align="left">
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
            <input name="txtfeccordeu" type="hidden" id="txtfeccordeu" value="<?php print $ld_feccordeu;?>" size="15" maxlength="10" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" readonly>
          </div></td>
        </tr>
        <tr>
          <td height="33"><div align="right">Sueldo Base</div></td>
          <td><div align="left">
            <input name="txtsuelbase" type="text" id="txtsuelbase" value="<?php print $li_suelbase;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Sueldo Integral</div></td>
          <td><div align="left">
              <input name="txtsuelint" type="text" id="txtsuelint" value="<?php print $li_suelint;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))">
          </div></td>
        </tr>
		 <tr>
       	  <td height="20" colspan="2"><div align="center" class="titulo-conect Estilo1">
       	    <div align="left" class="Estilo3">Colocar montos diarios para los siguientes campos: </div>
       	  </div></td>
		</tr>
		<tr>
          <td height="22"><div align="right">BVV (Bono Vacacional)</div></td>
          <td><div align="left">
              <input name="txtbonvac" type="text" id="txtbonvac" value="<?php print $li_bonvac;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))">
          </div></td>
        </tr>
		<tr>
          <td height="22"><div align="right">BFA (Bono Fin de Año)</div></td>
          <td><div align="left">
              <input name="txtbonfinanio" type="text" id="txtbonfinanio" value="<?php print $li_bonfinanio;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))">
          </div></td>
        </tr>
		<tr>
          <td height="22"><div align="right">Otras Asignaciones</div></td>
          <td><div align="left">
              <input name="txtotrasig" type="text" id="txtotrasig" value="<?php print $li_otrasig;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right"></div></td>
          <td><input name="operacion" type="hidden" id="operacion">
            <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
            <input name="txtfecingper" type="hidden" id="txtfecingper" value="<?php print $ld_fecingper;?>">
            <input name="txtfecnacper" type="hidden" id="txtfecnacper" value="<?php print $ld_fecnacper;?>">
		    <input name="txthidmes" type="hidden" id="txthidmes" value="<?php print $ls_hidmes;?>">
			<input name="txthidano" type="hidden" id="txthidano" value="<?php print $ls_hidano;?>">
		  </td>
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
function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.existe.value="FALSE";	
		codper=ue_validarvacio(f.txtcodper.value);
		nomper=ue_validarvacio(f.txtnomper.value);	
		fecnacper=ue_validarvacio(f.txtfecnacper.value);	
		fecingper=ue_validarvacio(f.txtfecingper.value);	
		f.action="sigesp_snorh_d_sueintanteriores.php?codper="+codper+"&nomper="+nomper+"&fecnacper="+fecnacper+"&fecingper="+fecingper+"";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
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
		suebase = ue_validarvacio(f.txtsuelbase.value);
		sueint = ue_validarvacio(f.txtsuelint.value);
		bonvac = ue_validarvacio(f.txtbonvac.value);
		bonfinanio = ue_validarvacio(f.txtbonfinanio.value);
		otrasig = ue_validarvacio(f.txtotrasig.value);
		f.txthidmes.value=f.cmbmes.value;
		f.txthidano.value=f.cmbano.value;	
		if(valido)
		{
			if ((codper!="")&&(suebase!=0)&&(sueint!=0)&&(bonvac!=0)&&(bonfinanio!=0)&&(otrasig!=0))
			{
				f.operacion.value="GUARDAR";
				f.action="sigesp_snorh_d_sueintanteriores.php";
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
			suebase = ue_validarvacio(f.txtsuelbase.value);
     		sueint = ue_validarvacio(f.txtsuelint.value);
			bonvac = ue_validarvacio(f.txtbonvac.value);
			bonfinanio = ue_validarvacio(f.txtbonfinanio.value);
			otrasig = ue_validarvacio(f.txtotrasig.value);
			f.txthidmes.value=f.cmbmes.value;
			f.txthidano.value=f.cmbano.value;	
			if ((codper!="")&&(suebase!=0)&&(sueint!=0)&&(bonvac!=0)&&(bonfinanio!=0)&&(otrasig!=0))
			{
				if(confirm("¿Desea eliminar el Registro actual?"))
				{
					f.operacion.value="ELIMINAR";
					f.action="sigesp_snorh_d_sueintanteriores.php";
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

function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		codper = ue_validarvacio(f.txtcodper.value);
		window.open("sigesp_snorh_cat_sueintanteriores.php?codper="+codper+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_buscardedicacion()
{
	window.open("sigesp_snorh_cat_dedicacion.php?tipo=trabajoant","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function dias(mes, anno) {
      mes = parseInt(mes);
	  anno = parseInt(anno);
      switch (mes) {
	    case 1 : case 3 : case 5 : case 7 : case 8 : case 10 : case 12 : return 31;
		case 2 : return (anno % 4 == 0) ? 29 : 28;
	  }
	  return 30;
   }
   
   function ultimodia(elemento) {
      var arreglo = elemento.split("/");
	  var dia = arreglo[0];
	  var mes = arreglo[1];
	  var anno = arreglo[2];
	  
      dia = dias(mes, anno);
	  
	 return dia;
   }
function ue_cargar_fecha()
{
	f=document.form1;
	f.txtfeccordeu.value="01/"+f.cmbmes.value+"/"+f.cmbano.value;
}

var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
</script> 
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>