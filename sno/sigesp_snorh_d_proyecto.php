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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_proyecto.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	$ls_loncodestpro1=$_SESSION["la_empresa"]["loncodestpro1"];
	$ls_loncodestpro2=$_SESSION["la_empresa"]["loncodestpro2"];
	$ls_loncodestpro3=$_SESSION["la_empresa"]["loncodestpro3"];
	$ls_loncodestpro4=$_SESSION["la_empresa"]["loncodestpro4"];
	$ls_loncodestpro5=$_SESSION["la_empresa"]["loncodestpro5"];
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
   		global $ls_codproy,$ls_nomproy,$ls_modalidad,$ls_nomestpro1,$ls_nomestpro2,$ls_nomestpro3,$ls_nomestpro4,$ls_nomestpro5;
		global $ls_titulo,$ls_codestpro1,$ls_denestpro1,$ls_codestpro2,$ls_denestpro2,$ls_codestpro3,$ls_denestpro3;
		global $ls_codestpro4,$ls_denestpro4,$ls_codestpro5,$ls_denestpro5,$ls_operacion,$lb_existe,$io_fun_nomina,$li_maxlen;
		global $ls_estcla;
		
		$ls_codproy="";
		$ls_nomproy="";
		$ls_nomestpro1=$_SESSION["la_empresa"]["nomestpro1"];		
		$ls_nomestpro2=$_SESSION["la_empresa"]["nomestpro2"];		
		$ls_nomestpro3=$_SESSION["la_empresa"]["nomestpro3"];		
		$ls_nomestpro4=$_SESSION["la_empresa"]["nomestpro4"];		
		$ls_nomestpro5=$_SESSION["la_empresa"]["nomestpro5"];		
		$ls_codestpro1="";
		$ls_denestpro1="";
		$ls_codestpro2="";
		$ls_denestpro2="";
		$ls_codestpro3="";
		$ls_denestpro3="";
		$ls_codestpro4="";
		$ls_denestpro4="";
		$ls_codestpro5="";
		$ls_denestpro5="";
		$ls_estcla="";
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		switch($ls_modalidad)
		{
			case "1": // Modalidad por Proyecto
				$ls_titulo="Estructura Presupuestaria";
				$ls_codestpro4="0000000000000000000000000";
				$ls_codestpro5="0000000000000000000000000";
				$li_maxlen=25;
				break;
				
			case "2": // Modalidad por Presupuesto
				$ls_titulo="Estructura Programática";
				$li_maxlen=25;
				break;
		}

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
   		global $ls_codproy,$ls_nomproy,$ls_codestpro1,$ls_denestpro1,$ls_codestpro2,$ls_denestpro2,$ls_codestpro3;
		global $ls_denestpro3,$ls_codestpro4,$ls_denestpro4,$ls_codestpro5,$ls_denestpro5;
		global $ls_estcla;
		
		$ls_codproy=$_POST["txtcodproy"];
		$ls_nomproy=$_POST["txtnomproy"];
		$ls_codestpro1=$_POST["txtcodestpro1"];
		$ls_denestpro1=$_POST["txtdenestpro1"];
		$ls_codestpro2=$_POST["txtcodestpro2"];
		$ls_denestpro2=$_POST["txtdenestpro2"];
		$ls_codestpro3=$_POST["txtcodestpro3"];
		$ls_denestpro3=$_POST["txtdenestpro3"];
		$ls_codestpro4=$_POST["txtcodestpro4"];
		$ls_denestpro4=$_POST["txtdenestpro4"];
		$ls_codestpro5=$_POST["txtcodestpro5"];
		$ls_denestpro5=$_POST["txtdenestpro5"];
		$ls_estcla=$_POST["txtestcla"];
		$ls_codestpro1=str_pad($ls_codestpro1,25,"0",0);
		$ls_codestpro2=str_pad($ls_codestpro2,25,"0",0);
		$ls_codestpro3=str_pad($ls_codestpro3,25,"0",0);
		$ls_codestpro4=str_pad($ls_codestpro4,25,"0",0);
		$ls_codestpro5=str_pad($ls_codestpro5,25,"0",0);
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
<title>Definici&oacute;n de Proyecto</title>
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
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_snorh_c_proyecto.php");
	$io_proyecto = new sigesp_snorh_c_proyecto();
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$msg=new class_mensajes();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "NUEVO":		
			$ls_statusg=0;
		    $ls_statusi=0;
		    $arrResultado=$io_proyecto->uf_validarcierre_gastos_ingreso($ls_statusg,$ls_statusi);
			$ls_statusg=$arrResultado['as_statusg'];
			$ls_statusi=$arrResultado['as_statusi'];
			$lb_valido=$arrResultado['lb_valido'];
		    if(($lb_valido)&&($ls_statusg=="1")&&($ls_statusi=="1"))
		    {
				$msg->message("El cierre Presuepuestario de Gasto e Ingreso fue ejecutado con Anterioridad, No se puede crear Proyectos");			
		    }
		break;
		
		case "GUARDAR":
		    $ls_statusg=0;
		    $ls_statusi=0;
		    $arrResultado=$io_proyecto->uf_validarcierre_gastos_ingreso($ls_statusg,$ls_statusi);
			$ls_statusg=$arrResultado['as_statusg'];
			$ls_statusi=$arrResultado['as_statusi'];
			$lb_valido=$arrResultado['lb_valido'];
		    if(($lb_valido)&&($ls_statusg=="0")&&($ls_statusi=="0"))
		    {
				uf_load_variables();			
				$lb_valido=$io_proyecto->uf_guardar($lb_existe,$ls_codproy,$ls_nomproy,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
													$ls_codestpro4,$ls_codestpro5,$ls_estcla,$la_seguridad);
				if($lb_valido)
				{
					uf_limpiarvariables();
					$lb_existe="FALSE";
				}
			}
			else
			{
				$msg->message("El cierre Presuepuestario de Gasto e Ingreso fue ejecutado con Anterioridad, No se puede crear Proyectos");	
			}
			break;

		case "ELIMINAR":
		    $ls_statusg=0;
		    $ls_statusi=0;
		    $arrResultado=$io_proyecto->uf_validarcierre_gastos_ingreso($ls_statusg,$ls_statusi);
			$ls_statusg=$arrResultado['as_statusg'];
			$ls_statusi=$arrResultado['as_statusi'];
			$lb_valido=$arrResultado['lb_valido'];
		    if(($lb_valido)&&($ls_statusg=="0")&&($ls_statusi=="0"))
		    {
				uf_load_variables();
				$lb_valido=$io_proyecto->uf_delete_proyecto($ls_codproy,$la_seguridad);
				if($lb_valido)
				{
					uf_limpiarvariables();
					$lb_existe="FALSE";
				}
			}
			else
			{
				$msg->message("El cierre Presuepuestario de Gasto e Ingreso fue ejecutado con Anterioridad, No se puede Eliminar Proyectos");	
			}
			break;
	}
	$io_proyecto->uf_destructor();
	unset($io_proyecto);
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
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif"  title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif"  title="Buscar" alt="Buscar" width="20" height="20" border="0"></a></div></td>
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
  <table width="700" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td>
		  <p>&nbsp;</p>
		  <table width="650" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
              <tr class="titulo-ventana">
                <td height="20" colspan="2">Definici&oacute;n de Proyecto </td>
              </tr>
              <tr >
                <td width="158" height="22">&nbsp;</td>
                <td width="486">&nbsp;</td>
              </tr>
              <tr>
                <td height="22"><div align="right" >
                    C&oacute;digo
                </div></td>
                <td ><div align="left" >
                  <input name="txtcodproy" type="text" id="txtcodproy" value="<?php print $ls_codproy; ?>" size="14" maxlength="10" onKeyUp="javascript: ue_validarnumero(this);" onBlur="javascript: ue_rellenarcampo(this,10);">
                </div></td>
              </tr>
              <tr >
                <td height="22"><div align="right">Denominaci&oacute;n</div></td>
                <td><div align="left">
                  <input name="txtnomproy" type="text" id="txtnomproy" value="<?php print $ls_nomproy; ?>" onKeyUp="ue_validarcomillas(this);" size="70" maxlength="100">
                </div></td>
              </tr>
              <tr>
                <td height="22">&nbsp;</td>
                <td><div align="left"><strong><?php print $ls_titulo; ?></strong></div></td>
              </tr>
              <tr>
                <td height="22"><div align="right">
                <?php print $ls_nomestpro1;?>				
                </div></td>
                <td>	
				  <div align="left">
                  <input name="txtcodestpro1" type="text" id="txtcodestpro1" value="<?php print $ls_codestpro1;?>" size="<?php print $ls_loncodestpro1+10; ?>" maxlength="<?php print $ls_loncodestpro1+1; ?>" readonly>
                  <a href="javascript:ue_estructura1();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Buscar"></a>
                  <input name="txtdenestpro1" type="text" class="sin-borde" id="txtdenestpro1" value="<?php print $ls_denestpro1;?>" size="53" readonly>			
                  <input name="txtestcla" type="hidden" id="txtestcla" size="2" value="<?php print $ls_estcla;?>">
				  </div>
              </td>
              </tr>
            <tr>
                <td height="22">
				<div align="right">
				<?php print $ls_nomestpro2;?>
			  </div>
			  </td>
                <td>
				 <div align="left" >
                 <input name="txtcodestpro2" type="text" id="txtcodestpro2" value="<?php print $ls_codestpro2 ; ?>" size="<?php print $ls_loncodestpro2+10; ?>" maxlength="<?php print $ls_loncodestpro2+1; ?>" readonly>
                 <a href="javascript:ue_estructura2();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Buscar"></a>
                 <input name="txtdenestpro2" type="text" class="sin-borde" id="txtdenestpro2" value="<?php print $ls_denestpro2 ; ?>" size="53" readonly>
				</td>
            </tr>
            <tr>
              <td height="22">
                <div align="right">
				<?php print $ls_nomestpro3; ?>
			  </div>
			  </td>
              <td>
			    <div align="left">
                <input name="txtcodestpro3" type="text" id="txtcodestpro3" value="<?php print $ls_codestpro3;?>" size="<?php print $ls_loncodestpro3+10; ?>" maxlength="<?php print $ls_loncodestpro3+1; ?>" readonly>
                <a href="javascript:ue_estructura3();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Buscar"></a>
                <input name="txtdenestpro3" type="text" class="sin-borde" id="txtdenestpro3" value="<?php print $ls_denestpro3;?>" size="53" readonly>
            </tr>
<?php if($ls_modalidad=="1") // Por Proyecto
	  {?>
 				<input name="txtcodestpro4" type="hidden" id="txtcodestpro4" value="<?php print $ls_codestpro4;?>">
 				<input name="txtdenestpro4" type="hidden" id="txtdenestpro4" value="<?php print $ls_denestpro4;?>">
 				<input name="txtcodestpro5" type="hidden" id="txtcodestpro5" value="<?php print $ls_codestpro5;?>">
 				<input name="txtdenestpro5" type="hidden" id="txtdenestpro5" value="<?php print $ls_denestpro5;?>">
<?php }
	  else
	  {?>
            <tr>
              <td height="22">
                <div align="right">
				<?php print $ls_nomestpro4; ?>
			  </div>
			  </td>
              <td>
			    <div align="left">
                <input name="txtcodestpro4" type="text" id="txtcodestpro4" value="<?php print $ls_codestpro4;?>" size="<?php print $ls_loncodestpro4+10; ?>" maxlength="<?php print $ls_loncodestpro4+1;?>" readonly>
                <a href="javascript:ue_estructura4();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Buscar"></a>
                <input name="txtdenestpro4" type="text" class="sin-borde" id="txtdenestpro4" value="<?php print $ls_denestpro4;?>" size="53" readonly>
            </tr>
            <tr>
              <td height="22">
                <div align="right">
				<?php print $ls_nomestpro5; ?>
			  </div>
			  </td>
              <td>
			    <div align="left">
                <input name="txtcodestpro5" type="text" id="txtcodestpro5" value="<?php print $ls_codestpro5;?>" size="<?php print $ls_loncodestpro5+10; ?>" maxlength="<?php print $ls_loncodestpro5+1;?>" readonly>
                <a href="javascript:ue_estructura5();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Buscar"></a>
                <input name="txtdenestpro5" type="text" class="sin-borde" id="txtdenestpro5" value="<?php print $ls_denestpro5;?>" size="53" readonly>
            </tr>
<?php } ?>
            <tr>
              <td height="18"><div align="right"></div></td>
              <td><input name="operacion" type="hidden" id="operacion">
              <input name="existe" type="hidden" id="existe" value="<?php print $lb_existe;?>">
              <input name="modalidad" type="hidden" id="modalidad" value="<?php print $ls_modalidad;?>">
			  </td>
            </tr>
          </table>
          <p>&nbsp;</p>
          </td>
      </tr>
  </table>
</form>  
</div>
</body>
<script >
function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f=document.form1;
		f.operacion.value ="NUEVO";
		f.existe.value="FALSE";		
		f.action="sigesp_snorh_d_proyecto.php";
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
		codproy = ue_validarvacio(f.txtcodproy.value);
		nomproy = ue_validarvacio(f.txtnomproy.value);
		codestpro1 = ue_validarvacio(f.txtcodestpro1.value);
		codestpro2 = ue_validarvacio(f.txtcodestpro2.value);
		codestpro3 = ue_validarvacio(f.txtcodestpro3.value);
		if(f.modalidad.value=="1")
		{
			codestpro4 = "0000000000000000000000000";
			codestpro5 = "0000000000000000000000000";
		}
		else
		{
			codestpro4 = ue_validarvacio(f.txtcodestpro4.value);
			codestpro5 = ue_validarvacio(f.txtcodestpro5.value);
		}
		if ((codproy!="")&&(nomproy!="")&&(codestpro1!="")&&(codestpro2!="")&&(codestpro3!="")&&(codestpro4!="")&&(codestpro5!=""))
		{
			f=document.form1;
			f.operacion.value ="GUARDAR";
			f.action="sigesp_snorh_d_proyecto.php";
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
			codproy = ue_validarvacio(f.txtcodproy.value);
			if (codproy!="")
			{
				if(confirm("¿Desea eliminar el Registro actual?"))
				{
					f.operacion.value ="ELIMINAR";
					f.action="sigesp_snorh_d_proyecto.php";
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

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		window.open("sigesp_snorh_cat_proyecto.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=yes");
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
	//window.open("../hlp/index.php?sistema=SNO&subsistema=SNR&nomfis=sno/sigesp_hlp_snr_unidadadministrativa.php","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

function ue_estructura1()
{
	   window.open("sigesp_snorh_cat_estpre1.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,resizable=yes,location=no");
}

function ue_estructura2()
{
	f=document.form1;
	codestpro1=f.txtcodestpro1.value;
	denestpro1=f.txtdenestpro1.value;
	estcla=f.txtestcla.value;
	if((codestpro1!="")&&(denestpro1!=""))
	{
		pagina="sigesp_snorh_cat_estpre2.php?codestpro1="+codestpro1+"&denestpro1="+denestpro1+"&estcla1="+estcla;
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura nivel 1");
	}
}

function ue_estructura3()
{
	f=document.form1;
	codestpro1=f.txtcodestpro1.value;
	denestpro1=f.txtdenestpro1.value;
	codestpro2=f.txtcodestpro2.value;
	denestpro2=f.txtdenestpro2.value;
	estcla=f.txtestcla.value;
	if((codestpro1!="")&&(denestpro1!="")&&(codestpro2!="")&&(denestpro2!=""))
	{
    	pagina="sigesp_snorh_cat_estpre3.php?codestpro1="+codestpro1+"&denestpro1="+denestpro1+"&codestpro2="+codestpro2+"&denestpro2="+denestpro2+"&estcla2="+estcla;
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura de nivel Anterior");
	}
}

function ue_estructura4()
{
	f=document.form1;
	codestpro1=f.txtcodestpro1.value;
	denestpro1=f.txtdenestpro1.value;
	codestpro2=f.txtcodestpro2.value;
	denestpro2=f.txtdenestpro2.value;
	codestpro3=f.txtcodestpro3.value;
	denestpro3=f.txtdenestpro3.value;
	estcla=f.txtestcla.value;
	if((codestpro1!="")&&(denestpro1!="")&&(codestpro2!="")&&(denestpro2!="")&&(codestpro3!="")&&(denestpro3!=""))
	{
    	pagina="sigesp_snorh_cat_estpre4.php?codestpro1="+codestpro1+"&denestpro1="+denestpro1+"&codestpro2="+codestpro2+"&denestpro2="+denestpro2+"&codestpro3="+codestpro3+"&denestpro3="+denestpro3+"&estcla3="+estcla;
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura de nivel Anterior");
	}
}

function ue_estructura5()
{
	f=document.form1;
	codestpro1=f.txtcodestpro1.value;
	denestpro1=f.txtdenestpro1.value;
	codestpro2=f.txtcodestpro2.value;
	denestpro2=f.txtdenestpro2.value;
	codestpro3=f.txtcodestpro3.value;
	denestpro3=f.txtdenestpro3.value;
	codestpro4=f.txtcodestpro4.value;
	denestpro4=f.txtdenestpro4.value;
	estcla=f.txtestcla.value;
	if((codestpro1!="")&&(denestpro1!="")&&(codestpro2!="")&&(denestpro2!="")&&(codestpro3!="")&&(denestpro3!="")&&(codestpro4!="")&&(denestpro4!=""))
	{
    	pagina="sigesp_snorh_cat_estpre5.php?codestpro1="+codestpro1+"&denestpro1="+denestpro1+"&codestpro2="+codestpro2+"&denestpro2="+denestpro2+"&codestpro3="+codestpro3+"&denestpro3="+denestpro3+"&codestpro4="+codestpro4+"&denestpro4="+denestpro4+"&estcla4="+estcla;
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura de nivel Anterior");
	}
}
</script>
</html>