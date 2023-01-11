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
		print "close();";
		print "opener.document.formulario.submit();";
		print "</script>";		
	}
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 19/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codprovben,$ls_nomprovben,$ls_tipodestino,$ls_operacion,$io_fun_cxp;
		require_once("../shared/class_folder/class_generar_id_process_sol.php");
		$io_id_process= new class_generar_id_process_sol();
		
		$ls_codprovben="";
		$ls_nomprovben="";
		$ls_tipodestino="";
		$ls_operacion=$io_fun_cxp->uf_obteneroperacion();
   }
	require_once("class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	uf_limpiarvariables();
	$ls_tipo=$io_fun_cxp->uf_obtenertipo();
	$ls_repcajchi=$io_fun_cxp->uf_obtenervalor_get("repcajchi","");
	unset($io_fun_cxp);
	$ld_fecdes="01/".date("m")."/".date("Y");
	$ld_fechas=date("d/m/Y");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Configuracion de Acta</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
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
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<script type="text/javascript"  src="js/funcion_cxp.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<body>
<form name="formulario" method="post" action="">
<input name="campoorden" type="hidden" id="campoorden" value="codigo">
<input name="orden" type="hidden" id="orden" value="ASC">
<input name="tipo" type="hidden" id="tipo" value="<?php print $ls_tipo; ?>">
<input name="repcajchi" type="hidden" id="repcajchi" value="<?php print $ls_repcajchi; ?>">
<table width="530" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="526" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Configuracion de Acta</td>
    </tr>
  </table>
  <br>
    <table width="520" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
      <tr>
        <td width="135" height="21"><div align="right">Codigo </div></td>
        <td width="217" height="21"><input name="txtcodigo" type="text" id="txtcodigo" onKeyPress="javascript: ue_mostrar(this,event);"></td>
        <td width="160">&nbsp;</td>
      </tr>
      <tr>
        <td height="22"><div align="right">Nombre</div></td>
        <td height="22"><input name="txtnombre" type="text" id="txtnombre" size="35"></td>
        <td width="160">&nbsp;</td>
      </tr>
	  <tr>
        <td colspan="3"><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
	  </tr>
	</table> 
	<p>
  <div id="resultados" align="center"></div>	
	</p>
</form>      
</body>
<script language="JavaScript">
function ue_search()
{
	f=document.formulario;
	// Cargamos las variables para pasarlas al AJAX
	codigo=f.txtcodigo.value;
	nombre=f.txtnombre.value;
	tipo=f.tipo.value;
	orden=f.orden.value;
	campoorden=f.campoorden.value;
	// Div donde se van a cargar los resultados
	divgrid = document.getElementById('resultados');
	// Instancia del Objeto AJAX
	ajax=objetoAjax();
	// Pagina donde están los métodos para buscar y pintar los resultados
	ajax.open("POST","class_folder/sigesp_cxp_c_catalogo_ajax.php",true);
	ajax.onreadystatechange=function(){
		if(ajax.readyState==1)
		{
			divgrid.innerHTML = "<img src='imagenes/loading.gif' width='350' height='200'>";//<-- aqui iria la precarga en AJAX 
		}
		else
		{
			if(ajax.readyState==4)
			{
				if(ajax.status==200)
				{//mostramos los datos dentro del contenedor
					divgrid.innerHTML = ajax.responseText
				}
				else
				{
					if(ajax.status==404)
					{
						divgrid.innerHTML = "La página no existe";
					}
					else
					{//mostramos el posible error     
						divgrid.innerHTML = "Error:".ajax.status;
					}
				}
				
			}
		}
	}	
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	// Enviar todos los campos a la pagina para que haga el procesamiento
	ajax.send("catalogo=CONFACTA&codigo="+codigo+"&nombre="+nombre+"&orden="+orden+"&campoorden="+campoorden+"&tipo="+tipo);
}

function ue_aceptar(ls_codigo,ls_nombre,ls_archrtf,li_i)
{
	opener.document.form1.txtcodigo.value=ls_codigo;
	opener.document.form1.txtnombre.value=ls_nombre;

	f=document.formulario;
	opener.document.form1.txtencabezado.value=eval("f.txtencabezado"+li_i+".value;");
	opener.document.form1.txtcuerpo.value=eval("f.txtcuerpo"+li_i+".value;");
	opener.document.form1.txtpie.value=eval("f.txtpie"+li_i+".value;");
	opener.document.form1.txtnomrtf.value=ls_archrtf;
	opener.document.form1.existe.value="TRUE";
}
function ue_aceptarReporte(ls_codigo,ls_nombre)
{
	opener.document.formulario.txtcodigo.value=ls_codigo;
	opener.document.formulario.txtnombre.value=ls_nombre;
	close();
}
</script>
</html>