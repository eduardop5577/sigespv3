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
		echo "<script language=JavaScript>";
		echo "close();";
		echo "opener.document.formulario.submit();";
		echo "</script>";		
	}
	require_once("class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$ls_tipo=$io_fun_cxp->uf_obtenertipo();
	unset($io_fun_cxp);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Unidades Ejecutoras</title>
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
</head>
<script type="text/javascript"  src="js/funcion_cxp.js"></script>
<body>
<form name="formulario" method="post" action="">
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr class="titulo-celda">
        <td height="22" colspan="2"><input name="campoorden" type="hidden" id="campoorden" value="coduniadm">
          <input name="orden" type="hidden" id="orden" value="ASC">
          <input name="tipo" type="hidden" id="tipo" value="<?php echo $ls_tipo; ?>">
        Cat&aacute;logo de Unidades Ejecutoras   
        </td>
      </tr>
      <tr>
        <td height="15">&nbsp;</td>
        <td height="15">&nbsp;</td>
      </tr>
      <tr>
        <td width="76" height="22" style="text-align:right">C&oacute;digo</td>
        <td width="418" height="22" style="text-align:left"><input name="txtcodunieje" type="text" id="txtcodunieje" size="20" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);" style="text-align:center"></td>
      </tr>
      <tr>
        <td height="22" style="text-align:right">Denominaci&oacute;n</td>
        <td height="22" style="text-align:left"><input name="txtdenunieje" type="text" id="txtdenunieje" size="70" maxlength="100" onKeyPress="javascript: ue_mostrar(this,event);" style="text-align:left"></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td height="22" style="text-align:right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></td>
      </tr>
  </table>
<p>
<div id="resultados" align="center"></div>	
</p>
</form>
</body>
<script language="JavaScript">
function aceptar(ls_codunieje,ls_denunieje,ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,
				 ls_estcla,ls_estint,ls_cuentaint)
{
	opener.document.formulario.txtcodunieje.value  = ls_codunieje;
    opener.document.formulario.txtdenunieje.value  = ls_denunieje;
	opener.document.formulario.hidestcla.value     = ls_estcla;
	opener.document.formulario.hidestint.value     = ls_estint;
	opener.document.formulario.hidcuentaint.value  = ls_cuentaint;
    opener.document.formulario.txtcodestpro1.value = ls_codestpro1;
    opener.document.formulario.txtcodestpro2.value = ls_codestpro2;
    opener.document.formulario.txtcodestpro3.value = ls_codestpro3;
    opener.document.formulario.txtcodestpro4.value = ls_codestpro4;
    opener.document.formulario.txtcodestpro5.value = ls_codestpro5;
	close();
}

function aceptar_contable(ls_codunieje,ls_denunieje)
{
	opener.document.formulario.txtcodunieje.value  = ls_codunieje;
    opener.document.formulario.txtdenunieje.value  = ls_denunieje;
	close();
}

function ue_search()
{
	f=document.formulario;
	// Cargamos las variables para pasarlas al AJAX
	ls_codunieje = f.txtcodunieje.value;
	ls_denunieje = f.txtdenunieje.value;
	ls_tipo = f.tipo.value;
	ls_orden     = f.orden.value;
	ls_roworden  = f.campoorden.value;
	// Div donde se van a cargar los resultados
	divgrid = document.getElementById('resultados');
	// Instancia del Objeto AJAX
	ajax=objetoAjax();
	// Pagina donde est?n los m?todos para buscar y pintar los resultados
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
						divgrid.innerHTML = "La p?gina no existe";
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
	ajax.send("catalogo=UNIDADEJECUTORA&codunieje="+ls_codunieje+"&denunieje="+ls_denunieje+"&tipo="+ls_tipo+"&orden="+ls_orden+"&campoorden="+ls_roworden);
}

function aceptar_catalogo(ls_codunieje,ls_denunieje)
{
	opener.document.formulario.txtcodunieje.value = ls_codunieje;
	opener.document.formulario.txtdenunieje.value = ls_denunieje;
	close();
}

function aceptar_reportedesde(ls_codunieje)
{
	opener.document.formulario.txtcoduniejedes.value = ls_codunieje;
	close();
}

function aceptar_reportehasta(ls_codunieje)
{
	opener.document.formulario.txtcoduniejehas.value = ls_codunieje;
	close();
}

function uf_catalogo_estructuras(ls_codunieje)
{
  window.open('sigesp_cxp_cat_estructuras.php?hidcodunieje='+ls_codunieje,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=800,height=450,left=50,top=50,location=no,resizable=yes,dependent=yes");
}
</script>
</html>