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
if(!array_key_exists("la_logusr",$_SESSION))
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
<title >Sistema de Bancos</title>
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
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
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
    <td height="20" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
</table>
<?php
	require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=new sigesp_include();
	$con=$in->uf_conectar();
	$funciones_db=new class_funciones_db($con);
	$lb_origen=$funciones_db->uf_select_column("scb_cmp_ret","origen");
	//$lb_codintper=$funciones_db->uf_select_column("sss_registro_eventos","codintper");
	$lb_tabla_cartaorden=$funciones_db->uf_select_table("scb_cartaorden");
	if((!$lb_origen) ||  (!$lb_tabla_cartaorden))
	{
		print "<script>";
		print "alert('Debe ejecutar el release');";
		print "location.href='../escritorio.html'";
		print "</script>";
		
	}	
	//***********************************************************************************************************************************
	require_once("../shared/class_folder/sigesp_release.php");
    $io_release= new sigesp_release();
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_dt_movbco','ctabanbene');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		} 
       }
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_cartaorden','archrtf');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Release 3.04 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		} 
     }
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('scb_movbco_fuefinanciamiento');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Release 3.10 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		} 
     }
    if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('sep_solicitud','nombenalt');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_72");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
/*    if($lb_valido)
	{
		switch($_SESSION["ls_gestor"])
	  	{
			case "MYSQLT":
				$lb_valido=$io_release->io_function_db->uf_select_type_columna('scb_movbco_spi','desmov','longtext');	
			 break;

			case "MYSQLI":
				$lb_valido=$io_release->io_function_db->uf_select_type_columna('scb_movbco_spi','desmov','longtext');	
			 break;
				   
			case "POSTGRES":
				$lb_valido=$io_release->io_function_db->uf_select_type_columna('scb_movbco_spi','desmov','text');
				   								
			break;  				  
	    }			
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_84");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
    if($lb_valido)
	{
		switch($_SESSION["ls_gestor"])
	  	{
			case "MYSQLT":
				$lb_valido=$io_release->io_function_db->uf_select_type_columna('scb_movbco_spgop','desmov','longtext');	
			 break;
			 
			case "MYSQLI":
				$lb_valido=$io_release->io_function_db->uf_select_type_columna('scb_movbco_spgop','desmov','longtext');	
			 break;
				   
			case "POSTGRES":
				$lb_valido=$io_release->io_function_db->uf_select_type_columna('scb_movbco_spgop','desmov','text');				   								
			break;  				  
	    }			
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_2_99");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
*/	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('spi_cuentas_estructuras');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_45");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco','estant');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_50");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('scb_movbco_anticipo');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_51");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco_spi','estcla');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_59");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
	 if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco_anticipo','sc_cuenta');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_62");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco','monamo');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2008_3_65");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
	if ($lb_valido)
	   {
		 $lb_valido=$io_release->io_function_db->uf_select_column('sigesp_empresa','confi_ch');	
		 if ($lb_valido==false)
			{
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release SIGESP BD 2008_4_29");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
			}
	   }
	if ($lb_valido)
	   {
		 $lb_valido=$io_release->io_function_db->uf_select_table('scb_tipofondo');
		 if ($lb_valido==false)
			{
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release SIGESP BD 2008_4_68");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
			}
	   }
	if ($lb_valido)
	   {
		 $lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco','numordpagmin');	
		 if ($lb_valido==false)
			{
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release SIGESP BD 2008_4_69");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
			}
	   }
	if ($lb_valido)
	   {
		 $lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco','codtipfon');	
		 if ($lb_valido==false)
			{
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release SIGESP BD 2009_2_02");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
			}
	   }
	if ($lb_valido)
	   {
		 $lb_valido=$io_release->io_function_db->uf_select_table('scb_fondosavance');
		 if ($lb_valido==false)
			{
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release SIGESP BD 2009_2_03");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
			}
	   }
	if ($lb_valido)
	   {
		 $lb_valido=$io_release->io_function_db->uf_select_table('scb_dt_fondosavance');
		 if ($lb_valido==false)
			{
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release SIGESP BD 2009_2_04");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
			}
	   }
	if ($lb_valido)
	   {
		 $lb_valido=$io_release->io_function_db->uf_select_column('scb_ctabanco','ctaserext');	
		 if ($lb_valido==false)
			{
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release SIGESP BD 2009_2_05");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
			}
	   }
	if ($lb_valido)
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
	if ($lb_valido)
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
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('scb_movbco','estserext');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   }
	if($lb_valido)
	{
		$lb_existe = $io_release->uf_select_config('SCB','RELEASE','4_01');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('sigesp_empresa','estvaldisfin');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_05");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   }
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('scb_movbco','estmovcob');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_34");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   }
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('scb_movbco','numconint');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_4_55");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   }      
	if($lb_valido)
	{
		$lb_existe = $io_release->uf_select_config('CFG','RELEASE','2009_10_03');
	    if(!$lb_existe)
	    {
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_10_03");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('scb_movbco','estapribs');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_12_04");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   } 
    if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('scb_movbco','estxmlibs');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2009_12_05");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   }
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_table('sno_tipo_pensionado');
		if($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_04_01");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}		
	}
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('scb_movbco','codper');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_04_02");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   } 
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('scb_movbco','codperi');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_04_03");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   }
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('sno_personal','tipnip');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_04_04");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   }          
	/*if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_constraint('sss_registro_eventos','unk_sss_registro_eventos');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_04_05");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   }*/
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('scb_movbco','tranoreglib');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_04_06");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   }
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('scb_movbco','estcondoc');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_07_03");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   }
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('scb_movbco','fecenvfir');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_08_01");
			  print "<script language=JavaScript>";
			  print "location.href='../escritorio.html'";
			  print "</script>";		
		    }
	   }
	if ($lb_valido)
	   {
	     $lb_valido = $io_release->io_function_db->uf_select_column('scb_movbco_scg','codper');	
		 if ($lb_valido==false)
		    {
			  $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2010_10_01");
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
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco_spg','codfuefin');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2012_10_02 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco','docdestrans');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_02_02 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco','tiptrans');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2013_02_03 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	             
	  if($lb_valido)
	  {
	  	$lb_valido=$io_release->uf_select_config("SCB","RELEASE","2015_10_05");
	  	if($lb_valido==false)
	  	{
	  		$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2015_10_05");
	  		print "<script language=JavaScript>";
	  		print "location.href='../escritorio.html'";
	  		print "</script>";
	  	}
	  }
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movimientoconciliar','codban');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2016_09_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	/*if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_confconciliacion','codconf');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2016_09_02 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}*/
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
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movimientoconciliar','estcon');
		if ($lb_valido)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2017_08_01 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
        $lb_valido=$io_release->io_function_db->uf_select_table('scb_archivoconciliacion');
        if ($lb_valido==false)
        {
                $io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2018_10_01 ");
                print "<script language=JavaScript>";
                print "location.href='../escritorio.html'";
                print "</script>";		
        }
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_column('scb_movbco','codmon');
		if ($lb_valido==false)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2021_09_02 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	if($lb_valido)
	{
		$lb_valido=$io_release->io_function_db->uf_select_type_columna('scb_movbco','tascam','double precision');
		if ($lb_valido)
		{
			$io_release->io_msg->message("Debe Procesar Instala/Procesos/Mantenimiento/Release 2022_01_03 ");
			print "<script language=JavaScript>";
			print "location.href='../escritorio.html'";
			print "</script>";		
		}
	}
	unset($io_release);
?>
</body>
</html>