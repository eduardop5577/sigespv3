<?php 
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Definici&oacute;n de Estructura Presupuestaria</title>
	<script type="text/javascript">
			var sistema='CFG';
			var vista='sigesp_vis_cfg_spg_estructurapresupuestaria.php';
			var codmenu = null;
			var tbnuevo = false;
			var tbactualizar = false;
			var tbadministrativo = false;
			var codmenu='0000';
			<?php
               require_once ('../../base/librerias/php/general/sigesp_lib_funciones.php');
               obtenerEmpresaSession();           
            ?>
	</script>
	<link href="../../base/css/general.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../../base/librerias/js/general/sigesp_lib_comunes.js"></script>
	<script type="text/javascript" src="../../base/librerias/js/componentes/sigesp_com_catalogo.js"></script>
	<script type="text/javascript" src="catalogo/sigesp_vista_cfg_catalogo_plan_cta_patrimonial.js"></script>
	<script type="text/javascript" src="catalogo/sigesp_vista_cfg_catalogo_fuentefinanciamiento.js"></script>
	<script type="text/javascript" src="js/sigesp_vis_cfg_spg_estructurapresupuestaria.js"></script>
</head>
<body class="modfondo" >
	<div id="barra_herramientas"></div>
	<div id="tabs7"></div>
	<table id='formestprog' border="0" style="margin-top:0px">
		 <tr>
			<td id='nivel1' class="NombreNivel"></td>
			<td id='valornivel1' class="ValorNivel"></td>
		</tr>
		<tr>
			<td id='nivel2' class="NombreNivel"></td>
			<td id='valornivel2' class="ValorNivel"></td>
		</tr>
		<tr>
			<td id='nivel3' class="NombreNivel"></td>
			<td id='valornivel3' class="ValorNivel"></td>
		</tr>
		<tr>
			<td id='nivel4' class="NombreNivel"></td>
			<td id='valornivel4' class="ValorNivel"></td>
		</tr> 
	</table>	
	<div id="grid0" class="x-hide-display"></div>
	<div id="grid1" class="x-hide-display"></div>
	<div id="grid2" class="x-hide-display"></div>
	<div id="grid3" class="x-hide-display"></div>
	<div id="grid4" class="x-hide-display"></div>
</body>
</html>