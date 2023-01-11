<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
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
<title >Cuentas por Pagar</title>
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
<link href="css/cxp.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="825" height="51"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			
            <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Cuentas por Pagar </td>
			  <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </table>
    </td>
  </tr>
  <tr>
    <td height="20" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
</table>
<?php
	
	// validación de los release necesarios poara que funcione el sistema de nómina
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd_scg','estasicon');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd_spg','codfuefin');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd','codrecdoc');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sigesp_empresa','estvaldis');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_1_39");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_clasificador_rd','sc_cuenta');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_29");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('cxp_cmp_islr');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_65");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_cmp_ret','basdatori');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_69");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_dt_cmp_islr','codded');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_71");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_ordencompra','tipbieordcom');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_78");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd','coduniadm');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_85");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd','estact');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('cxp_dc_cargos');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd','numordpagmin');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco','numordpagmin');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco','codtipfon');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_solicitudes','numordpagmin');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_solicitudes','codtipfon');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_existe = $io_release->uf_select_config('CXP','RELEASE','4_02');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_02");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sigesp_empresa','dedconproben');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_09");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sigesp_deducciones','codconret');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_26");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sno_personalisr','codconret');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_28");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if ($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sigesp_empresa','estaprsoc');	
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_01_28");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if ($lb_valido)
	{
	 $lb_valido=$io_release->io_function_db->uf_select_column('sigesp_cargos','tipo_iva');	
	 if ($lb_valido==false)
		{
		  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_05_03");
		  print "<script language=JavaScript>";
		  print "location.href='../escritorio.html'";
		  print "</script>";		
		}
	} 
	if ($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd','repcajchi');	
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_05_04");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if ($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_solicitudes','repcajchi');	
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_05_05");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_existe = $io_release->uf_select_config('CFG','RELEASE','2010_07_04');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_7_04");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}	
	if ($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_dc_spi','codemp');	
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_08_05");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}	
	if ($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_dc_spg','procede_doc');	
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_10_04");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}	
	if($lb_valido)
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
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd','codproalt');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2011_01_07 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{	
		if ($_SESSION["ls_gestor"]=="POSTGRES")
		{
			$tamano=$io_release->io_function_db->uf_tamano_type_columna('scb_cmp_ret','nomsujret');
		}
		else
		{
			$as_valor1=0;
			$as_valor2=0;
			$as_valor3=0;
			$as_valor4=0;
			$io_release->io_function_db->uf_tamano_type_columna_Mysql('scb_cmp_ret','nomsujret',$as_valor1,$as_valor2,$as_valor3,$as_valor4);
			$tamano=$as_valor1;
		}
		
		if ($tamano=="80")
		{
           $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2011_03_03 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";  
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd','conanurd');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2011_07_03 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_solicitudes','conanusol');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2011_07_04 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_solicitudes','nombenaltcre');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2011_07_08 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_documento','tipdocdon');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2011_08_07 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd','codusureg');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_07_07 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_solicitudes','codusureg');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_07_08 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd_cargos','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_15 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd_spg','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_16 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_dc_cargos','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_17 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_dc_spg','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_18 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_dc_spi','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_19 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd','tipdoctesnac');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_11_05 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_dt_cmp_ret','tipdoctesnac');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_11_06 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd','numexprel');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_02_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{	
		if ($_SESSION["ls_gestor"]=="POSTGRES")
		{
			$tamano=$io_release->io_function_db->uf_tamano_type_columna('cxp_rd','numref');
		}
		else
		{
			$as_valor1=0;
			$as_valor2=0;
			$as_valor3=0;
			$as_valor4=0;
			$io_release->io_function_db->uf_tamano_type_columna_Mysql('cxp_rd','numref',$as_valor1,$as_valor2,$as_valor3,$as_valor4);
			$tamano=$as_valor1;
		}
		
		if ($tamano=="15")
		{
           $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2015_03_02 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";  
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_rd','estretasu');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2015_07_04 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_sol_dc','fecemi');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2015_07_08 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_sol_dc','estlibcom');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2015_09_06 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sigesp_empresa','estcanret');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2016_01_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('cxp_confacta','codemp');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2016_11_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_existe=$io_release->io_function_db->uf_select_table('sigesp_prefijos');
	    if(!$lb_existe)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2018_09_03 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
			$lb_valido=false;		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sss_derechos_usuarios','codintper');
		if ($lb_valido)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2017_03_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	unset($io_release);
?>
</body>
</html>