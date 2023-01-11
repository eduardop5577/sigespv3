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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Cuentas de Ingreso</title>
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
<input name="campoorden" type="hidden" id="campoorden" value="codestpro">
<input name="orden" type="hidden" id="orden" value="ASC">
<table width="620" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="540" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Cuentas de Ingreso</td>
    </tr>
  </table>
  <br>
    <table width="620" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
      <tr>
        <td width="148" height="22"><div align="right">Cuenta</div></td>
        <td width="346" height="22"><div align="left">
          <input name="txtspicuenta" type="text" id="txtspicuenta" onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Denominaci&oacute;n</div></td>
        <td height="22"><input name="txtdencue" type="text" id="nombre" onKeyPress="javascript: ue_mostrar(this,event);">      </td>
      </tr>
	  <tr>
        <td colspan="2"><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a><a href="javascript: ue_close();"> <img src="../shared/imagebank/eliminar.gif" alt="Cerrar" width="15" height="15" class="sin-borde">Cerrar</a></div></td>
	  </tr>
  </table> 
	<p>
  <div id="resultados" align="center"></div>	
	</p>
</form>      
</body>
<script language="JavaScript">
function ue_aceptar(ls_codestpro,ls_estcla,ls_spicuenta,ls_sccuenta,ls_denominacion,ls_programatica,ls_estatus,ls_scgdenominacion,ls_monto)
{
	totrow=ue_calcular_total_fila_opener("txtcodestpro");
	li_i=0;
	parametros="";
	f=opener.document.formulario;
	totalcuentas=1;
	for(j=1;j<=totrow;j++)
	{
		li_i=li_i+1;
		txtcodestpro=eval("f.txtcodestpro"+j+".value");
		txtcodpro=eval("f.txtcodpro"+j+".value");
		txtestclaaux=eval("f.txtestclaaux"+j+".value");
		txtestcla=eval("f.txtestcla"+j+".value");
		txtspicuenta=eval("f.txtspicuenta"+j+".value");
		txtscgcuenta=eval("f.txtscgcuenta"+j+".value");
		txtdenscgcuenta=eval("f.txtdenscgcuenta"+j+".value");
		txtmonto=eval("f.txtmonto"+j+".value");
		txtmontooriginal=eval("f.txtmontooriginal"+j+".value");
		txtdencuenta=eval("f.txtdencuenta"+j+".value");
		parametros=parametros+"&txtcodestpro"+li_i+"="+txtcodestpro+"&txtcodpro"+li_i+"="+txtcodpro+"&txtestclaaux"+li_i+"="+txtestclaaux+""+
				   "&txtestcla"+li_i+"="+txtestcla+"&txtspicuenta"+li_i+"="+txtspicuenta+""+
				   "&txtscgcuenta"+li_i+"="+txtscgcuenta+"&txtdenscgcuenta"+li_i+"="+txtdenscgcuenta+""+ "&txtmonto"+li_i+"="+txtmonto+
				   "&txtmontooriginal"+li_i+"="+txtmontooriginal+ "&txtdencuenta"+li_i+"="+txtdencuenta;
	}
	if(txtcodestpro!="")
	{
		totalcuentas=eval(li_i+"+1");
	}
	parametros=parametros+"&txtcodestpro"+totalcuentas+"="+ls_programatica+"&txtcodpro"+totalcuentas+"="+ls_codestpro+"&txtestclaaux"+totalcuentas+"="+ls_estatus+""+
			   "&txtestcla"+totalcuentas+"="+ls_estcla+"&txtspicuenta"+totalcuentas+"="+ls_spicuenta+""+
			   "&txtscgcuenta"+totalcuentas+"="+ls_sccuenta+"&txtdenscgcuenta"+totalcuentas+"="+ls_scgdenominacion+""+ "&txtmonto"+totalcuentas+"="+ls_monto+
			   "&txtmontooriginal"+totalcuentas+"="+ls_monto+ "&txtdencuenta"+totalcuentas+"="+ls_denominacion;
	parametros=parametros+"&totrow="+totalcuentas+"";
	if(parametros!="")
	{
		// Div donde se van a cargar los resultados
		divgrid = opener.document.getElementById("resultados");
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde están los métodos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_cxp_c_catalogo_ajax.php",true);
		ajax.onreadystatechange=function(){
			if(ajax.readyState==1)
			{
				//divgrid.innerHTML = "";//<-- aqui iria la precarga en AJAX 
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
		ajax.send("catalogo=CATINGRESO"+parametros);
	}
//	close();
}

function ue_search()
{
	f=document.formulario;
	// Cargamos las variables para pasarlas al AJAX
	numrecdoc=opener.document.formulario.numrecdoc.value;
	codtipdoc=opener.document.formulario.codtipdoc.value;
	codproben=opener.document.formulario.codproben.value;
	tipproben=opener.document.formulario.tipproben.value;
	spicuenta=f.txtspicuenta.value;
	dencue=f.txtdencue.value;
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
	ajax.send("catalogo=CUENTASSPI&spicuenta="+spicuenta+"&dencue="+dencue+
			  "&numrecdoc="+numrecdoc+"&codtipdoc="+codtipdoc+"&codproben="+codproben+"&tipproben="+tipproben+
			  "&orden="+orden+"&campoorden="+campoorden);
}

function ue_close()
{
	close();
}
</script>
</html>