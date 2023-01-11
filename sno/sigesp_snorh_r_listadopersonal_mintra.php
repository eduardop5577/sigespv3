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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_r_listadopersonal_mintra.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	require_once("sigesp_sno.php");
	$io_sno=new sigesp_sno();
	$ls_ruta="txt/general";
	//--------------------------------------------------------------
    function uf_cargarnomina()
    {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cargarnomina
		//		   Access: private
		//	  Description: Función que obtiene todas las nóminas y las carga en un 
		//				   combo para seleccionarlas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];

		$ls_sql="SELECT sno_nomina.codnom, sno_nomina.desnom ".
				"  FROM sno_nomina, sss_permisos_internos ".
				" WHERE sno_nomina.codemp='".$ls_codemp."'".
				"   AND sno_nomina.peractnom<>'000'".
				"   AND sss_permisos_internos.codsis='SNO'".
				"   AND sss_permisos_internos.enabled=1".
				"   AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'".
				"   AND sno_nomina.codemp = sss_permisos_internos.codemp ".
				"   AND sno_nomina.codnom = sss_permisos_internos.codintper ".
				" GROUP BY sno_nomina.codnom, sno_nomina.desnom ".
				" ORDER BY sno_nomina.codnom, sno_nomina.desnom ";
		$rs_data=$io_sql->select($ls_sql);
       	print "<select name='cmbnomina' id='cmbnomina' style='width:210px'>";
        print " <option value='' selected>--Seleccione Una--</option>";
		if($rs_data===false)
		{
        	$io_mensajes->message("Clase->Seleccionar Nómina Método->uf_cargarnomina Error->".$io_funciones->uf_convertirmsg($io_sql->message)); 
			print "<script language=JavaScript>";
			print "	close();";
			print "</script>";		
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codnom=$row["codnom"];
				$ls_desnom=$row["desnom"];
            	print "<option value='".$ls_codnom."'>".$ls_codnom."-".$ls_desnom."</option>";				
			}
			$io_sql->free_result($rs_data);
		}
       	print "</select>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);	
		unset($io_mensajes);		
		unset($io_funciones);		
        unset($ls_codemp);
   }
    //--------------------------------------------------------------
	unset($io_sno);
	
	$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
	require_once("sigesp_sno_c_metodo_banco.php");
	$io_metodobanco=new sigesp_sno_c_metodo_banco();
	switch ($ls_operacion) 
	{
		case "GENDISK":
			$ls_ruta="txt/general";
			@mkdir($ls_ruta,0755);
			$ls_nomina=$io_fun_nomina->uf_obtenervalor("cmbnomina","0");
			$ls_anocurper=$io_fun_nomina->uf_obtenervalor("txtanocurper","0");
			$ls_mescurper=$io_fun_nomina->uf_obtenervalor("txtmescurper","0");
			$ls_perdes=$io_fun_nomina->uf_obtenervalor("txtcodperdes","0");
			$ls_perhas=$io_fun_nomina->uf_obtenervalor("txtcodperhas","0");
			$ls_codubifisdes=$io_fun_nomina->uf_obtenervalor("txtcodubifisdes","");
			$ls_codubifishas=$io_fun_nomina->uf_obtenervalor("txtcodubifishas","");
			$ls_coduniadmdes=$io_fun_nomina->uf_obtenervalor("txtcoduniadmdes","");
			$ls_coduniadmhas=$io_fun_nomina->uf_obtenervalor("txtcoduniadmhas","");
			$ls_metodo="MINTRA";
			$arrResultado=$io_metodobanco->uf_listadobanco_txtmintra($ls_nomina,$ls_perdes,$ls_perhas,$ls_anocurper,$ls_mescurper,$ls_codubifisdes,$ls_codubifishas,$ls_coduniadmdes,$ls_coduniadmhas,$rs_data);
			$rs_data=$arrResultado['rs_data'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				$ds_store=$rs_data;
				$lb_valido=$io_metodobanco->uf_metodo_generar_txtmintra($ls_metodo,$ls_ruta,$ds_store,$la_seguridad);
			}
			break;
			
		default:
			$ls_nomina="";
			$ls_perdes="";
			$ls_perhas="";
			break;
	}
	unset($io_metodobanco);
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
<title >Listado de Personal - Ministerio del Trabajo</title>
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
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</head>

<body>
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
        </table>	 </td>
  </tr>
  <?php

	if (isset($_GET["valor"]))
	{ $ls_valor=$_GET["valor"];	}
	else
	{ $ls_valor="";}
	
	if ($ls_valor!='srh')
	{
	   print ('<tr>');
	   print ('<td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>' );
	   print ('</tr>');
	}
	
	
  ?>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_print();"><img src="../shared/imagebank/tools20/gendisk.jpg" title="Generar" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_descargar('<?php print $ls_ruta;?>');"><img src="../shared/imagebank/tools20/download.gif" title="Descargar" alt="Salir" width="20" height="20" border="0"></a></div></td>
	<?php

	if (isset($_GET["valor"]))
	{ $ls_valor=$_GET["valor"];	}
	else
	{ $ls_valor="";}
	
	if ($ls_valor!='srh')
	{
	    print ('<td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title=Salir alt="Salir" width="20" height="20" border="0"></a></div></td>' );	   
	}
	else
	{
	 print ('<td class="toolbar" width="25"><div align="center"><a href="javascript: close();"><img src="../shared/imagebank/tools20/salir.gif" title=Salir alt="Salir" width="20" height="20" border="0"></a></div></td>' );	
	}
	
  ?>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
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
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="620" height="138" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="588" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
          <td height="20" colspan="5" class="titulo-ventana">Listado de Personal - Ministerio del Trabajo </td>
        </tr>
        <tr>
          <td height="22"><div align="right">N&oacute;mina</div></td>
          <td><div align="left">
            <?php uf_cargarnomina(); ?>
          </div></td>
          <td width="36">&nbsp;</td>
          <td width="273" colspan="3">&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Mes</div></td>
          <td colspan="5"><input name="txtanocurper" type="text" id="txtanocurper" size="7" maxlength="4" readonly>
            <input name="txtmescurper" type="text" id="txtmescurper" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarmeses();"><img id="meses" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0">
            <input name="txtdesmesper" type="text" class="sin-borde" id="txtdesmesper" value="" size="30" maxlength="20" readonly>
            </a></td>
        </tr>
        <tr>
          <td height="20" colspan="5" class="titulo-celdanew">Intervalo de Personal </td>
          </tr>
        <tr>
          <td width="146" height="22"><div align="right"> Desde </div></td>
          <td width="123"><div align="left">
            <input name="txtcodperdes" type="text" id="txtcodperdes" size="13" maxlength="10" value="" readonly>
            <a href="javascript: ue_buscarpersonaldesde();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td width="36"><div align="right">Hasta </div></td>
          <td colspan="2"><div align="left">
            <input name="txtcodperhas" type="text" id="txtcodperhas" value="" size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarpersonalhasta();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="20" colspan="5" class="titulo-celdanew">Intervalo de Ubicación Física </td>
          </tr>
        <tr>
          <td width="146" height="22"><div align="right"> Desde </div></td>
          <td width="123"><div align="left">
            <input name="txtcodubifisdes" type="text" id="txtcodubifisdes" size="13" maxlength="10" value="" readonly>
            <a href="javascript: ue_buscarubicacionfisicadesde();"><img id="Ubicacion" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td width="36"><div align="right">Hasta </div></td>
          <td colspan="2"><div align="left">
            <input name="txtcodubifishas" type="text" id="txtcodubifishas" value="" size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarubicacionfisicahasta();"><img id="Ubicacion" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="22" colspan="5" bgcolor="#3982C6"><div align="center"><span class="titulo-celdanew">Intervalo de Unidades Administrativas </span></div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">Desde</div></td>
          <td><input name="txtcoduniadmdes" type="text" id="txtcoduniadmdes" size="18" maxlength="16">
            <a href="javascript: ue_buscarunidaddesde();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></td>
          <td><div align="right">Hasta</div></td>
          <td colspan="2"><input name="txtcoduniadmhas" type="text" id="txtcoduniadmhas" size="18" maxlength="16">
            <a href="javascript: ue_buscarunidadhasta();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" name="personal" width="15" height="15" border="0" id="personal"></a></td>
        </tr>
        <tr>
          <td height="20" colspan="5" class="titulo-celdanew"><div align="right" class="titulo-celdanew">Ordenado por </div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo del Personal </div></td>
          <td colspan="4"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="1" checked>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Apellido del Personal</div></td>
          <td colspan="4"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="2">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Nombre del Personal</div></td>
          <td colspan="4"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="3">
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
<input name="operacion" type="hidden" id="operacion">
</form>      
</body>
<script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function ue_print()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{
		codnom=f.cmbnomina.value;
		if((f.txtanocurper.value!="")&&(f.txtmescurper.value!=""))
		{
			f.operacion.value="GENDISK";
			f.action="sigesp_snorh_r_listadopersonal_mintra.php";
			f.submit();
		}
		else
		{
			alert("Debe colocar la Nómina y mes.");
		}
	
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operación");
   	}		
}

function ue_buscarpersonaldesde()
{
	f=document.form1;
	codnom=f.cmbnomina.value;
	window.open("sigesp_snorh_cat_personal.php?tipo=replispermintrades&codnom="+codnom,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarpersonalhasta()
{
	f=document.form1;
	codnom=f.cmbnomina.value;
	if(f.txtcodperdes.value!="")
	{
		window.open("sigesp_snorh_cat_personal.php?tipo=replispermintrahas&codnom="+codnom,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un personal desde.");
	}
}

function ue_buscarubicacionfisicadesde()
{
	f=document.form1;
	window.open("sigesp_snorh_cat_ubicacionfisica.php?tipo=mintrasdesde","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarubicacionfisicahasta()
{
	f=document.form1;
	codubifisdes=f.txtcodubifisdes.value;
	if(codubifisdes!="")
	{
		window.open("sigesp_snorh_cat_ubicacionfisica.php?tipo=mintrashasta","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar una Ubicación Física desde.");
	}
}

function ue_descargar(ruta)
{
	window.open("sigesp_sno_cat_directorio.php?ruta="+ruta+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarmeses()
{
	f=document.form1;
	codnom=f.cmbnomina.value;
	if(codnom!="")
	{
		window.open("sigesp_sno_cat_hmes.php?tipo=reppermintra&codnom="+codnom+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=200,top=200,location=no,resizable=no");
   	}
	else
   	{
		alert("Debe seleccionar una nómina desde.");
   	}
}

function limpiar_tipo()
{
	f=document.form1;
	f.txtcoduniadm.value = '';
	f.txtdesuniadm.value = '';
}

function ue_buscarunidaddesde()
{
	window.open("sigesp_snorh_cat_uni_ad.php?tipo=replisperdes","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarunidadhasta()
{
	f=document.form1;
	if(f.txtcoduniadmdes.value!="")
	{
		window.open("sigesp_snorh_cat_uni_ad.php?tipo=replisperhas","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar una Unidad Administrativa desde.");
	}
}
</script> 
</html>