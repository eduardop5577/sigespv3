<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
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
     print "window.close();";
     print "</script>";		
   }
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

require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_funcion=new class_funciones();
$io_funcion->uf_limpiar_sesion();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Ordenes de Compra</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<link href="css/soc.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css"  rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
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
</head>
<body onLoad="writetostatus('<?php print "Base de Datos: ".$_SESSION["ls_database"].". Usuario: ".$_SESSION["la_logusr"];?>')">
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="800" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="800" height="40"></td>
  </tr>
  <td width="800" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">			
            <td width="450" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Ordenes de Compra</td>
			  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </table>
    </td>
  <tr>
    <td height="20" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
</table>
<?php
	
	//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_ordencompra','estcondat');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
    //***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sigesp_empresa','numordcom');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
    //***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_ordencompra','uniejeaso');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 3.27");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
    //***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_enlace_sep','coduniadm');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 3.28");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
    //***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_dt_bienes','coduniadm');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 3.29");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
    //***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_dt_servicio','coduniadm');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 3.30");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
	//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_ordencompra','fechentdesde');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2.04");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
	//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sep_solicitud','tipsepbie');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_76");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
	//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_sol_cotizacion','tipsolbie');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_77");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
//***********************************************************************************************************************************
/*	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$valor=$io_release->uf_buscar_unidad();
		if ($valor==0)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 4_23");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
*///***********************************************************************************************************************************
/*	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$valor=$io_release->uf_buscar_sep_solicitud();
		if ($valor==0)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 4_24");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}	
*///***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
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
		unset($io_release);
	}
//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_existe = $io_release->io_function_db->uf_select_column('soc_dt_servicio','codfuefin');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_4_63");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_existe = $io_release->io_function_db->uf_select_column('soc_servicios','codunimed');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_4_72");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_existe = $io_release->io_function_db->uf_select_column('soc_dta_cargos','codestpro1');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_3_43");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_existe = $io_release->io_function_db->uf_select_column('soc_dts_cargos','codestpro1');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_3_44");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_existe = $io_release->uf_select_config('SOC','RELEASE','3_45_1');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_3_45");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
	$io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_existe = $io_release->uf_select_config('SOC','RELEASE','3_46_1');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_3_46");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
	//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_existe = $io_release->uf_select_config('SEP','RELEASE','4_06');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_06");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
	//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_existe = $io_release->uf_select_config('SEP','RELEASE','4_07');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_07");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
	//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_existe = $io_release->uf_select_config('SEP','RELEASE','4_08');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_08");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
	$io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_existe = $io_release->io_function_db->uf_select_column('soc_cotizacion','diavalofe');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_10_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
		unset($io_release);
	}
//***********************************************************************************************************************************
/*    require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
    $lb_valido=true;
    if($lb_valido)
    {
        $lb_existe = $io_release->io_function_db->uf_select_column('soc_analisicotizacion','estasitec');
        if(!$lb_existe)
        {
            $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_10_02");
            print "<script language=JavaScript>";
            print "location.href='../escritorio.html'";
            print "</script>";        
        }
        unset($io_release);
    }
*/
    //***********************************************************************************************************************************
    require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
    $lb_valido=true;
    if($lb_valido)
    {
        $lb_existe = $io_release->io_function_db->uf_select_column('soc_ordencompra','codusureg');
        if(!$lb_existe)
        {
            $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_12_03");
            print "<script language=JavaScript>";
            print "location.href='../escritorio.html'";
            print "</script>";        
        }
        unset($io_release);
    }
    //***********************************************************************************************************************************
    require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
    $lb_valido=true;
    if($lb_valido)
    {
        $lb_existe = $io_release->io_function_db->uf_select_column('soc_cotizacion','estasitec');
        if(!$lb_existe)
        {
            $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_12_08");
            print "<script language=JavaScript>";
            print "location.href='../escritorio.html'";
            print "</script>";        
        }
        unset($io_release);
    }	
    //***********************************************************************************************************************************
    
    //***********************************************************************************************************************************
    require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
    $lb_valido=true;
    if($lb_valido)
    {
        $lb_existe = $io_release->io_function_db->uf_select_constraint('soc_dt_bienes','fk_soc_dt_bienes__spg_cuenta_fuentefinancimiento');
        if($lb_existe)
        {
            $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_01_03");
            print "<script language=JavaScript>";
            print "location.href='../escritorio.html'";
            print "</script>";        
        }
        unset($io_release);
    }	
    //***********************************************************************************************************************************
    
    //***********************************************************************************************************************************
    require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
    $lb_valido=true;
    if($lb_valido)
    {
        $lb_existe = $io_release->io_function_db->uf_select_constraint('soc_dt_servicio','fk_soc_dt_servicio__spg_cuenta_fuentefinancimiento');
        if($lb_existe)
        {
            $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_01_04");
            print "<script language=JavaScript>";
            print "location.href='../escritorio.html'";
            print "</script>";        
        }
    }	
    //***********************************************************************************************************************************
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('siv_articulo','estartgen');	
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_01_27");
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
	 $lb_valido=$io_release->io_function_db->uf_select_column('soc_analisicotizacion','recanacot');	
	 if ($lb_valido==false)
		{
		  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_08_02");
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
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_ordencompra','conanusoc');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2011_07_02 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('rpc_proveedor','estrnc');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_08_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_cuentagasto','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_08 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_solicitudcargos','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_09 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_dt_bienes','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_10 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_dt_servicio','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_11 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_dta_cargos','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_12 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_dts_cargos','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_13 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_sol_cotizacion','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_14 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_ordencompra','numdiacre');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2015_07_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_ordencompra','tipsiscam');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2015_09_07 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_ordencompra','codconobr');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2016_08_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('soc_ordencompra','ressoc');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2016_08_02 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_existe=$io_release->io_function_db->uf_select_column('sss_derechos_usuarios','codintper');
		if ($lb_existe)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2017_03_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
			$lb_valido=false;		
		}
	}
	if($lb_valido)
	{
		$lb_existe=$io_release->io_function_db->uf_select_column('soc_ordencompra','forpag');
	    if(!$lb_existe)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2017_10_03 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
			$lb_valido=false;		
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
		$lb_existe=$io_release->io_function_db->uf_select_column('soc_dtcot_bienes','desart');
                if(!$lb_existe)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2020_02_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
			$lb_valido=false;		
		}
	}
	if($lb_valido)
	{
		$lb_existe=$io_release->io_function_db->uf_select_column('soc_dtcot_servicio','desser');
                if(!$lb_existe)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2020_02_02 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
			$lb_valido=false;		
		}
	}
	if($lb_valido)
	{
		$lb_existe=$io_release->io_function_db->uf_select_column('soc_ordencompra','estoricom');
                if(!$lb_existe)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2021_11_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
			$lb_valido=false;		
		}
	}
	if($lb_valido)
	{
		$lb_existe=$io_release->io_function_db->uf_select_column('spg_unidadadministrativa','resuniadm');
                if(!$lb_existe)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2022_05_02 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
			$lb_valido=false;		
		}
	}
	unset($io_release);
?>  
</body>
<script >
function writetostatus(input){
    window.status=input
    return true
}
</script>
</html>