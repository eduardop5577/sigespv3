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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_tipoescuela.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_codtipesc,$ls_dentipesc,$ls_escbol,$li_tophor,$ls_difacc,$ls_medacc,$ls_rural,$ls_colnoc,$ls_colesp,$ls_colpen;
		global $ls_existe, $ls_operacion, $io_fun_nomina;
                
		$ls_codtipesc="";			
		$ls_dentipesc="";
		$ls_escbol="";
		$li_tophor="";
		$ls_difacc="";
                $ls_medaccc="";
                $ls_ruralc="";
                $ls_colnocc="";
                $ls_colespc="";
                $ls_colpenc="";
		$ls_existe=$io_fun_nomina->uf_obtenerexiste();
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
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
   		global $ls_codtipesc,$ls_dentipesc,$ls_escbol,$li_tophor,$ls_difacc,$ls_medacc,$ls_rural,$ls_colnoc,$ls_colesp,$ls_colpen;
		global $io_fun_nomina;
                
		$ls_codtipesc=$_POST["txtcodtipesc"];
		$ls_dentipesc=$_POST["txtdentipesc"];
		$ls_escbol=$io_fun_nomina->uf_obtenervalor("chkescbol","0");                
		$li_tophor=$io_fun_nomina->uf_obtenervalor("txttophor","0");
            	$ls_difacc=$io_fun_nomina->uf_obtenervalor("chkdifacc","0");
            	$ls_medacc=$io_fun_nomina->uf_obtenervalor("chkmedacc","0");
            	$ls_rural=$io_fun_nomina->uf_obtenervalor("chkrural","0");
            	$ls_colnoc=$io_fun_nomina->uf_obtenervalor("chkcolnoc","0");
            	$ls_colesp=$io_fun_nomina->uf_obtenervalor("chkcolesp","0");
            	$ls_colpen=$io_fun_nomina->uf_obtenervalor("chkcolpen","0");
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
<title >Definici&oacute;n de M&eacute;todo a Banco</title>
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
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">

<script  type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>

<body>
<?php 
	require_once("sigesp_snorh_c_tipoescuela.php");
	$io_tipoescuela=new sigesp_snorh_c_tipoescuela();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "GUARDAR":
			uf_load_variables();
			$lb_valido=$io_tipoescuela->uf_guardar($ls_existe,$ls_codtipesc,$ls_dentipesc,$ls_escbol,$li_tophor,$ls_difacc,$ls_medacc,
                                                               $ls_rural,$ls_colnoc,$ls_colesp,$ls_colpen,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_existe="FALSE";
			}
			break;

		case "ELIMINAR":
			uf_load_variables();
			$lb_valido=$io_tipoescuela->uf_delete_tipoescuela($ls_codtipesc,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_existe="FALSE";
			}
			break;
	}
	$io_tipoescuela->uf_destructor();
	unset($io_tipoescuela);
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
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="550" height="138" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="510" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="5" class="titulo-ventana">Definici&oacute;n de Tipo Escuela </td>
        </tr>
        <tr>
          <td width="159" height="22"><div align="right"></div></td>
          <td width="345">&nbsp;</td>
        </tr>
        <tr>
          <td height="22" width="25%"><div align="right">C&oacute;digo</div></td>
          <td colspan="3">            <div align="left">
            <input name="txtcodtipesc" type="text" id="txtcodtipesc" value="<?php print $ls_codtipesc;?>" size="11" maxlength="10" onKeyUp="javascript: ue_validarnumero(this);" onBlur="javascript: ue_rellenarcampo(this,10);">          
          </div></td>
        </tr>
        <tr>
          <td height="22" width="25%"><div align="right">Descripci&oacute;n</div></td>
          <td colspan="3">            <div align="left">
            <input name="txtdentipesc" type="text" id="txtdentipesc" value="<?php print $ls_dentipesc;?>" size="60" maxlength="250" onKeyUp="javascript: ue_validarcomillas(this);">          
          </div></td>
        </tr>
        <tr>
          <td height="22" width="35%"><div align="right">Unidad Educativa Bolivariana</div></td>
          <td width="15%"><div align="left"><input name="chkescbol" type="checkbox" class="sin-borde" id="chkescbol" value="1" <?php if($ls_escbol=="1"){print "checked"; }?>></div></td>
          <td height="22" width="25%"><div align="right">Tope de Horas</div></td>
          <td  width="25%">            <div align="left">
            <input name="txttophor" type="text" id="txttophor" value="<?php print $li_tophor;?>" size="5" maxlength="3" onKeyUp="javascript: ue_validarnumero(this);">          
          </div></td>
        </tr>
        <tr>
          <td height="22" width="25%"><div align="right">Difícil Acceso </div></td>
          <td colspan="3">            <div align="left">
            <input name="chkdifacc" type="checkbox" class="sin-borde" id="chkdifacc" value="1">          
          </div></td>
        </tr>
        <tr>
          <td height="22" width="25%"><div align="right">Mediano  Acceso </div></td>
          <td colspan="3">            <div align="left">
            <input name="chkmedacc" type="checkbox" class="sin-borde" id="chkdmedacc" value="1">          
          </div></td>
        </tr>
        <tr>
          <td height="22" width="25%"><div align="right">Rural</div></td>
          <td colspan="3">            <div align="left">
            <input name="chkrural" type="checkbox" class="sin-borde" id="chkrural" value="1">          
          </div></td>
        </tr>
        <tr>
          <td height="22" width="25%"><div align="right">Colegio Nocturno </div></td>
          <td colspan="3">            <div align="left">
            <input name="chkcolnoc" type="checkbox" class="sin-borde" id="chkcolnoc" value="1">          
          </div></td>
        </tr>
        <tr>
          <td height="22" width="25%"><div align="right">Colegio Especial</div></td>
          <td colspan="3">            <div align="left">
            <input name="chkcolesp" type="checkbox" class="sin-borde" id="chkcolesp" value="1">          
          </div></td>
        </tr>
        <tr>
          <td height="22" width="25%"><div align="right">Penitenciario</div></td>
          <td colspan="3">            <div align="left">
            <input name="chkcolpen" type="checkbox" class="sin-borde" id="chkcolpen" value="1">          
          </div></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input name="operacion" type="hidden" id="operacion">
            <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>"></td>
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
<p>&nbsp;</p>
</body>
<script >
function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.existe.value="FALSE";	
		f.action="sigesp_snorh_d_tipoescuela.php";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
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
		codtipesc = ue_validarvacio(f.txtcodtipesc.value);
		dentipesc = ue_validarvacio(f.txtdentipesc.value);
		if ((codtipesc!="")&&(dentipesc!=""))
		{
			f.operacion.value="GUARDAR";
			f.action="sigesp_snorh_d_tipoescuela.php";
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
		codtipesc = ue_validarvacio(f.txtcodtipesc.value);
		if ((codtipesc!=""))
		{
			if(confirm("¿Desea eliminar el Registro actual?"))
			{
				f.operacion.value="ELIMINAR";
				f.action="sigesp_snorh_d_tipoescuela.php";
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
		window.open("sigesp_snorh_cat_tipoescuela.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
	//window.open("../hlp/index.php?sistema=SNO&subsistema=SNR&nomfis=sno/sigesp_hlp_snr_metodobanco.php","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}
</script> 
</html>