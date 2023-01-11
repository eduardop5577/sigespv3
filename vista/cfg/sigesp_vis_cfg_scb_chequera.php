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
	    <title >SIGESP - Registro de Chequera</title> 
    	<link href="../../base/css/general.css" rel="stylesheet" type="text/css">
        <script type="text/javascript">
            var sistema='CFG';
            var vista='sigesp_vis_cfg_scb_chequera.php';
            var tbnuevo = false;
            var tbactualizar = false;
            var tbadministrativo = false;
			var codmenu='0000';
            <?php
            	require_once ('../../base/librerias/php/general/sigesp_lib_funciones.php');
    			obtenerEmpresaSession();           
    		?>
        </script>
		<script type="text/javascript" src="../../base/librerias/js/general/sigesp_lib_comunes.js"></script>
        <script type="text/javascript" src="../cfg/catalogo/sigesp_vis_cfg_catalogo_chequera.js"></script>
        <script type="text/javascript" src="../cfg/catalogo/sigesp_vis_cfg_catalogo_banco.js"></script>
        <script type="text/javascript" src="../cfg/catalogo/sigesp_vis_cfg_catalogo_scb_cuentabanco.js"></script>
        <script type="text/javascript" src="../sss/catalogo/sigesp_vis_sss_catalogo_usuarios.js"></script>
		<script type="text/javascript" src="js/sigesp_vis_cfg_scb_chequera.js"></script>
	</head>
<body class="modfondo">
	<div id="barra_herramientas"></div>
	<div id="formulario_Chequera"></div>
	<div id="grid_panelcheques"></div>
	<div id="grid_panelusuarios"></div>
</body> 
</html>