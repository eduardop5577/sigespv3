<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Reporte Ejecución Presupuestaria Mensual de Gasto</title>
<script type="text/javascript">
		var sistema='SPG';
		var vista='sigesp_vis_spg_reporte_ejecucion_financiera_mensual.php';
		var tbnuevo = false;
		var tbactualizar = false;
		var tbadministrativo = false;
		<?php
                require_once ('../../base/librerias/php/general/sigesp_lib_funciones.php');
                obtenerEmpresaSession();           
         ?>
	</script>
	<script type="text/javascript" src="../../base/librerias/js/general/sigesp_lib_comunes.js"></script>
	<script type="text/javascript" src="../../base/librerias/js/componentes/sigesp_com_fsestructurafuentecuenta.js"></script>
	<script type="text/javascript" src="../../vista/spg/js/sigesp_vis_spg_reporte_ejecucion_financiera_mensual.js"></script>
</head>
<body class="modfondo">
<div id="barra_herramientas"></div>
<div id="formReporteEjePreMen"></div>
</body>
</html>