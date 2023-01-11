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
if(!array_key_exists("la_logusr",$_SESSION))
{
	print "<script language=JavaScript>";
	print "window.close();";
	print "</script>";		
}

require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_funcion=new class_funciones();
$io_funcion->uf_limpiar_sesion();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" src="../shared/js/disabled_keys.js"></script>
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
<title >Sistema de Viaticos</title>
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
<script type="text/javascript" src="js/stm31.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
	
	/// validación de los release necesarios 
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido1=true;
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','estcla');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_31");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_constraint('scv_tarifas','fk_scv_tari_scv_regio_scv_regi');
		if($lb_valido==true)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_44");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_constraint('scv_rutas','ak_key_2_scv_ruta');
		if($lb_valido==true)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_52");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_dt_personal','codnom');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_70");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('spg_dt_fuentefinanciamiento');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_80");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		if ($_SESSION["ls_gestor"]=="oci8po") {
	   	$ls_tabla = 'spg_cuenta_fuentefinan';			
		}
		else {
			$ls_tabla = 'spg_cuenta_fuentefinanciamiento';
		}		
		$lb_valido=$io_release->io_function_db->uf_select_table($ls_tabla);
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_81");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','codfuefin');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_82");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_dt_spg','codfuefin');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_83");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','codtipdoc');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_62");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sss_permisos_internos','enabled');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_10_11 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','repcajchi');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2011_06_02");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->uf_select_config('SNO','RELEASE','2012_01_02');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_01_02");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_misiones','codpai');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_03_03");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('scv_regiones_int');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_03_04");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('scv_dt_regiones_int');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_03_05");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('scv_tarifacargos');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_03_06");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('scv_dt_tarifacargos');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_03_07");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('scv_incremento');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_04_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('scv_cargafamiliar');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_06_02");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','codcar');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_07_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_otrasasignaciones','codmis');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_07_02");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('scv_dt_misiones');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_07_03");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_tarifacargos','tipvia');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_07_04");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_tarifas','codmis');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_07_05");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_tarifacargos','exterior');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_08_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_tarifacargos','codmon');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_08_02");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_tarifas','codmon');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_08_03");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','tipodoc');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_08_04");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('scv_catcargos');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_09_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('scv_dt_catcargos');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_09_02");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_dt_misiones','monaut');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_09_04");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','mondolsol');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_10_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','tascamsol');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_10_02");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','numaut');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_11_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','fecaut');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_11_02");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_misiones','estdesviaper');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_12_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','estsolfam');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2014_01_02");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sss_derechos_usuarios','codintper');
		if($lb_valido)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2017_03_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','codcla');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2017_04_07");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_solicitudviatico','estopediv');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2020_02_03");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		} 
	}
	if($lb_valido1)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scv_otrasasignaciones','codmon');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2022_08_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		} 
	}
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#E7E7E7" class="cd-menu">
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
    <td height="20" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu.js"></script></td>
  </tr>
</table>

</body>
<script >
</script> 
</html>