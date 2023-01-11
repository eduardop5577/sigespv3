<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
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
	require_once("class_folder/class_funciones_siv.php");
	$io_fun_siv=new class_funciones_siv();
	$arrResultado = $io_fun_siv->uf_load_seguridad("SIV","sigesp_siv_d_causas.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   //--------------------------------------------------------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 19/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_operacion,$ls_codcau,$ls_dencau,$ls_status;
		
		$ls_operacion="ue_nuevo";
		$ls_codcau="";
		$ls_dencau="";
		$ls_status="";
   }
   function uf_cargarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 19/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_operacion,$ls_codcau,$ls_dencau,$ls_status;
		
		$ls_operacion=$_POST["operacion"];
		$ls_codcau=$_POST["txtcodcau"];
		$ls_dencau=$_POST["txtdencau"];
		$ls_status=$_POST["hidstatus"];
   }
   //--------------------------------------------------------------------------------------------------------------

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Definici&oacute;n de Causas de Movimiento</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
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
</head>
<body link="#006699" vlink="#006699" alink="#006699">
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr> 
    <td height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" class="cd-menu">
		<table width="776" border="0" align="center" cellpadding="0" cellspacing="0">
			
                <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Inventario </td>
			      <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </table>
	</td>
  </tr>
  <tr>
    <td height="20" class="cd-menu">
	<script type="text/javascript"  src="js/menu.js"></script>
<script type="text/javascript"  src="../shared/js/valida_tecla.js"></script>
	<script type="text/javascript"  src="js/funcion_siv.js"></script>	</td>
  </tr>
  <tr>
    <td height="20" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0"></a><a href="javascript:ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0"></a><a href="javascript:ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"></a><!--img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20"--><a href="javascript:ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0"></a><a href="sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0"></a></td>
  </tr>
</table>

<?php
	require_once("class_folder/sigesp_siv_c_causas.php");
	$io_siv=new sigesp_siv_c_causas();
	
	if(array_key_exists("operacion",$_POST))
	{
		uf_cargarvariables();
	}
	else
	{
		uf_limpiarvariables();
	}
	
	switch($ls_operacion)
	{
		case "ue_nuevo":
			uf_limpiarvariables();
			require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
			$io_keygen= new sigesp_c_generar_consecutivo();
			$ls_codcau= $io_keygen->uf_generar_numero_nuevo("SIV","siv_causas","codcau","SIV",3,"","","");
		break;
		case "ue_guardar":
			$lb_valido=$io_siv->uf_guardar_causas($ls_codcau,$ls_dencau,$ls_status,$la_seguridad);
			uf_limpiarvariables();
		break;
		case "ue_eliminar":
			$lb_valido=$io_siv->uf_delete_causas($ls_codcau,$la_seguridad);			
			uf_limpiarvariables();
		break;
	}	
	
?>
<p>&nbsp;	</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_siv->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_siv);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>	

    <table width="518" height="197" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td width="516" height="195"><div align="center">
          <table  border="0" cellspacing="0" cellpadding="0" class="formato-blanco" align="center">
            <tr>
              <td colspan="2" class="titulo-ventana">Definici&oacute;n de Causas de Movimiento </td>
            </tr>
            <tr>
              <td >
			  <input name="operacion" type="hidden" id="operacion"  value="<?php print $ls_operacion?>">
			  <input name="hidstatus" type="hidden" id="hidstatus" value="<?php print $ls_status; ?>" >			  </td>
              <td >&nbsp;</td>
            </tr>
            <tr>
              <td width="134" height="22" align="right"><span class="style2">C&oacute;digo</span></td>
              <td width="334" ><input name="txtcodcau" style="text-align:center " type="text" id="txtcodcau" value="<?php print  $ls_codcau;?>" size="3" maxlength="3"  readonly="true">              </td>
            </tr>
            <tr align="left">
              <td height="22" align="right"><span class="style2">Denominacion</span></td>
              <td><input name="txtdencau" id="txtdencau" value="<?php print $ls_dencau; ?>" type="text" size="50" maxlength="254" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn&ntilde;opqrstuvwxyz ()#!%/[]*-+_.,:;');"></td>
            </tr>
            <tr>
              <td height="8">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          </table>
        </div></td>
      </tr>
  </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
</form>
</body>

<script >


/*******************************************************************************************************************************/

function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{		
		f.operacion.value="ue_nuevo";
		f.action="sigesp_siv_d_causas.php";
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
	lb_status=f.hidstatus.value;
	if(((lb_status=="C")&&(li_cambiar==1))||(lb_status!="C")&&(li_incluir==1))
	{
		with(f)
		{
			if (ue_valida_null(txtcodcau,"Código")==false)
			{
				txtcodcau.focus();
			}
			else
			{ 
				if (ue_valida_null(txtdencau,"Denominacion")==false)
				{
					txtdencau.focus();
				}
				else
				{
					f.operacion.value="ue_guardar";
					f.action="sigesp_siv_d_causas.php";
					f.submit();
				}
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
			if (ue_valida_null(txtcodcau,"Código")==false)
			{
				txtcodcau.focus();
			}
			else
			{
				if (confirm("¿ Esta seguro de eliminar este registro ?"))
				{ 		
					f.operacion.value="ue_eliminar";
					f.action="sigesp_siv_d_causas.php";
					f.submit();
				}
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
	if(li_leer==1)
	{
		f.operacion.value="";			
		window.open("sigesp_siv_cat_causas.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function cargarCausas(codcau,dencau)
{
	f=document.form1;
	f.txtcodcau.value=codcau;
	f.txtdencau.value=dencau;
	f.hidstatus.value="C";
}

</script>
</html>