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
	$ruta = '../';
	
	if (isset($_GET["tipo"]))
	{ 
		$ls_tipo=$_GET["tipo"];	
	}
	else
	{ 
		$ls_tipo="";
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Catálogo de Entes</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/catalogos.css" rel="stylesheet" type="text/css">
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

</head>

<script  type="text/JavaScript" src="../shared/js/js_ajax.js"></script>
<script  type="text/JavaScript" src="js/sigesp_sno_js_ente.js"></script>
<body>
<form name="form1" method="post" action="">
<table width="457" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="453" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Entes</td>
    </tr>
  </table>
<br>
<table width="454" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
<tr>
      <td width="162" height="22"><div align="right">Codigo</div></td>
        <td width="286" height="22"><div align="left">
          <input name="txt_codigo" type="text" id="txt_codigo">        
        </div>          
      <div align="right"></div></td>
    </tr>
      <tr>
        <td height="22"><div align="right">Ente</div></td>
        <td height="22"><div align="left">
          <input name="txt_ente" type="text" id="txt_ente">
</div></td>
      <tr>
        
    <tr>
      <td height="22"><input name="tipo" type="hidden" id="tipo" value="<?php print $ls_tipo;?>"></td>
      <td><div align="right"><a href="javascript: buscar();"><img src="../shared/imagebank/tools20/buscar.gif" width="20" height="20" border="0"></a><a href="javascript: buscar();">Buscar</a> </div></td>
	  </tr>
	  
  </table>
  <br> 
  <p>
  <div id="resultados" align="center"></div>	
	</p>
</form>      
</body>
</html>