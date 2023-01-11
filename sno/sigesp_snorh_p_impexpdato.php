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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_p_impexpdato.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$ls_ruta="txt/importar";
	@mkdir($ls_ruta,0755);

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
   		global $ls_arctxt, $ls_operacion, $ls_accion;
		global $ls_codarch,$ls_denarch,$ls_acumon,$li_totrow;
		
		$ls_arctxt="";
		$ls_codarch="";
		$ls_denarch="";
		$ls_acumon="";
		$li_totrow=0;
		$ls_operacion=$_POST["operacion"];		
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
<title >Importar/Exportar Datos</title>
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
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php 
	require_once("sigesp_snorh_c_impexpdato.php");
	$io_impexpdato=new sigesp_snorh_c_impexpdato();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "PROCESAR":
			$ls_arctxt=$_FILES["txtarctxt"]["tmp_name"]; 
			$ls_tiparctxt=$_FILES["txtarctxt"]["type"]; 
			$ls_codarch=$_POST["txtcodarch"];
			$ls_denarch=$_POST["txtdenarch"];
			$ls_acumon=$_POST["hidacumon"];
			if($ls_tiparctxt=="text/plain")
			{
				$li_totrow=0;
				$arrResultado=$io_impexpdato->uf_importardatos($ls_arctxt,$ls_codarch);
				$li_totrow=$arrResultado['ai_nrofilas'];
				$lb_valido=$arrResultado['lb_valido'];
				if ($lb_valido)
				{
					if (isset($_FILES['txtarctxt']))
					{
						if ($_FILES['txtarctxt']['error'] == UPLOAD_ERR_OK)
						{
							move_uploaded_file($_FILES['txtarctxt']['tmp_name'], $ls_ruta.'/procesar/PROCESAR_'.$_SESSION['la_logusr'].'.txt');
							@mkdir($ls_ruta.'/procesar',0755);
						}
					}
				}
			}
			else
			{
				$io_impexpdato->io_mensajes->message("Tipo de archivo inválido. Solo se permiten archivos TXT.");
			}
			break;

		case "GUARDAR":
			$ls_codarch=$_POST["txtcodarch"];
			$ls_denarch=$_POST["txtdenarch"];
			$ls_acumon=$_POST["hidacumon"];
			$li_totrow=$_POST["totrow"];
			$lb_valido=$io_impexpdato->uf_procesarimportardatos($ls_codarch,$ls_acumon,$la_seguridad);
			break;
	}
	$io_impexpdato->uf_destructor();
	unset($io_impexpdato);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="762" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
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
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title='Guardar 'alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" title='Ejecutar' alt="Ejecutar" width="20" height="20" border="0"></a></div></td>	
	<td class="toolbar" width="25"><div align="center"><a href="javascript: ue_descargar('<?php print $ls_ruta.'/resultado';?>');"><img src="../shared/imagebank/tools20/download.gif" title="Descargar" alt="Salir" width="20" height="20" border="0"></a></div></td>	
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title='Salir' alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title='Ayuda' alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="301"><div align="center"></div></td>
    <td class="toolbar" width="71"><div align="center"></div></td>
    <td class="toolbar" width="71"><div align="center"></div></td>
    <td class="toolbar" width="71"><div align="center"></div></td>
    <td class="toolbar" width="71"><div align="center"></div></td>
    <td class="toolbar" width="68"><div align="center"></div></td>
    <td class="toolbar" width="3">&nbsp;</td>
  </tr>
</table>

<p>&nbsp;</p>
<form name="form1" method="post" enctype="multipart/form-data" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="750" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="6" class="titulo-ventana">Importar/Exportar Datos</td>
        </tr>
        <tr>
          <td height="22" colspan="6" class="titulo-celdanew">Archivo a Importar</td>
        </tr>
        <tr>
          <td width="118" height="22">&nbsp;</td>
          <td width="618" colspan="5">&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Archivo TXT </div></td>
          <td colspan="5"><div align="left">
            <input name="txtarctxt" type="file" id="txtarctxt" size="50" maxlength="200" value="<?php print $ls_arctxt;?>">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Tipo de Archivo </div></td>
          <td colspan="5"><div align="left">
            <input name="txtcodarch" type="text" size="6" maxlength="4" value="<?php print $ls_codarch; ?>" readonly>
            <a href="javascript: ue_buscararchivo();"><img id="archivo" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdenarch" type="text" class="sin-borde" id="txtdenarch" size="60" maxlength="120" value="<?php print $ls_denarch; ?>" readonly>
			<input name="hidacumon" type="hidden" class="sin-borde" id="hidacumon" size="60" maxlength="120" value="<?php print $ls_acumon; ?>" readonly>
          </div></td>
        </tr>
        <tr>
          <td height="22">&nbsp;</td>
          <td colspan="5">&nbsp;</td>
        </tr>
          </table>		  </td>
          </tr>
        <tr>
          <td height="22"><input name="operacion" type="hidden" id="operacion">
		  				  <input name="totrow" type="hidden" id="totrow" value="<?php print $li_totrow; ?>"> </td>
        </tr>
      </table>    
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
<p>&nbsp;</p>
</body>
<script >
function ue_cargardatos()
{
	f=document.form1;
	f.operacion.value="CARGARDATOS";
	f.action="sigesp_snorh_p_impexpdato.php";
	f.submit();
}

function ue_procesar()
{
	f=document.form1;
	li_ejecutar=f.ejecutar.value;
	if (li_ejecutar==1)
   	{
		arctxt=ue_validarvacio(f.txtarctxt.value);
		codarch=ue_validarvacio(f.txtcodarch.value);
		if((arctxt!="")&&(codarch!=""))
		{
			f.operacion.value="PROCESAR";
			f.action="sigesp_snorh_p_impexpdato.php";
			f.submit();			
		}
		else
		{
			alert("Debe seleccionar el archivo a Importar y un tipo de archivo.");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}		
}

function ue_guardar()
{
	f=document.form1;
	li_ejecutar=f.ejecutar.value;
	if (li_ejecutar==1)
   	{
		totrow=ue_validarvacio(f.totrow.value);
		if(totrow>0)
		{
			f.operacion.value="GUARDAR";
			f.action="sigesp_snorh_p_impexpdato.php";
			f.submit();			
		}
		else
		{
			alert("el archivo esta vacio.");
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

function ue_buscararchivo()
{
	window.open("sigesp_snorh_cat_archivotxt.php?tipo=EXTERNOS","Archivos","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_descargar(ruta)
{
	window.open("sigesp_sno_cat_directorio.php?ruta="+ruta+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}
</script> 
</html>