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

$i=0;
$i++;
$arbol["sistema"][$i]	    = "SOC";
$arbol["nivel"][$i]         = 0;
$arbol["nombre_logico"][$i] = "Cotizaciones";
$arbol["nombre_fisico"][$i] = "";
$arbol["id"][$i]			= "001";
$arbol["padre"][$i]		    = "000";
$arbol["numero_hijos"][$i]  = 5;

$i++;
$arbol["sistema"][$i]       = "SOC";
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "Solicitud de Cotizaci&oacute;n";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_solicitud_cotizacion.php";
$arbol["id"][$i]            = "002";
$arbol["padre"][$i]         = "001";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]		= "SOC";
$arbol["nivel"][$i]			= 1;
$arbol["nombre_logico"][$i] = "Registro de Cotizaci&oacute;n";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_registro_cotizacion.php";
$arbol["id"][$i]			= "003";
$arbol["padre"][$i]			= "001";
$arbol["numero_hijos"][$i]	= 0;

$i++;
$arbol["sistema"][$i]		= "SOC";
$arbol["nivel"][$i]			= 1;
$arbol["nombre_logico"][$i] = "An&aacute;lisis de Cotizaciones";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_analisis_cotizacion.php";
$arbol["id"][$i]			= "004";
$arbol["padre"][$i]			= "001";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]		= "SOC";
$arbol["nivel"][$i]         = 0;
$arbol["nombre_logico"][$i] = "Orden de Compra";
$arbol["nombre_fisico"][$i] = "";
$arbol["id"][$i]			= "005";
$arbol["padre"][$i]			= "000";
$arbol["numero_hijos"][$i]	= 5;


$i++;
$arbol["sistema"][$i]       = "SOC";
$arbol["nivel"][$i]			= 1;
$arbol["nombre_logico"][$i] = "Registro de Ordenes de Compra";
$arbol["nombre_fisico"][$i]	= "sigesp_soc_p_registro_orden_compra.php";
$arbol["id"][$i]			= "006";
$arbol["padre"][$i]			= "005";
$arbol["numero_hijos"][$i]  = 0;


$i++;
$arbol["sistema"][$i] 		= "SOC";
$arbol["nivel"][$i]			= 1;
$arbol["nombre_logico"][$i] = "Aprobaci&oacute;n de Ordenes de Compra";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_aprobacion_orden_compra.php";
$arbol["id"][$i]			= "007";
$arbol["padre"][$i]			= "005";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]       = "SOC";
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "Anulaci&oacute;n de Ordenes de Compra";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_anulacion_orden_compra.php";
$arbol["id"][$i]            = "008";
$arbol["padre"][$i]         = "005";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i] 		= "SOC";
$arbol["nivel"][$i]			= 1;
$arbol["nombre_logico"][$i] = "Aceptaci&oacute;n de Servicios";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_aceptacion_servicios.php";
$arbol["id"][$i]			= "009";
$arbol["padre"][$i]			= "005";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]       = "SOC";
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "Reverso de Aceptaci&oacute;n de Servicios";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_reverso_aceptacion_servicio.php";
$arbol["id"][$i]            = "010";
$arbol["padre"][$i]         = "005";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]		= "SOC";
$arbol["nivel"][$i]			= 0;
$arbol["nombre_logico"][$i] = "Reportes";
$arbol["nombre_fisico"][$i] = "";
$arbol["id"][$i]			= "011";
$arbol["padre"][$i]			= "000";
$arbol["numero_hijos"][$i]	= 5;

$i++;
$arbol["sistema"][$i]		= "SOC";
$arbol["nivel"][$i]			= 1;
$arbol["nombre_logico"][$i] = "Ordenes de Compra";
$arbol["nombre_fisico"][$i] = "sigesp_soc_r_orden_compra.php";
$arbol["id"][$i]			= "012";
$arbol["padre"][$i]			= "011";
$arbol["numero_hijos"][$i]	= 0;

$i++;
$arbol["sistema"][$i]       = "SOC";
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "Solicitud de Cotizaciones";
$arbol["nombre_fisico"][$i] = "sigesp_soc_r_solicitud_cotizacion.php";
$arbol["id"][$i]            = "013";
$arbol["padre"][$i]			= "011";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]		= "SOC"; 
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "Registro de Cotizaciones";
$arbol["nombre_fisico"][$i] = "sigesp_soc_r_registro_cotizacion.php";
$arbol["id"][$i]            = "014";
$arbol["padre"][$i]         = "011";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]       = "SOC";
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "An&aacute;lisis de Cotizaciones";
$arbol["nombre_fisico"][$i] = "sigesp_soc_r_analisis_cotizacion.php";
$arbol["id"][$i]            = "015";
$arbol["padre"][$i]         = "011";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]       = "SOC";
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "Acta de Aceptaci&oacute;n de Servicios";
$arbol["nombre_fisico"][$i] = "sigesp_soc_r_aceptacion_servicios.php";
$arbol["id"][$i]            = "016";
$arbol["padre"][$i]         = "011";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]	= "SOC";
$arbol["nivel"][$i]		= 1;
$arbol["nombre_logico"][$i] = "Aprobaci&oacute;n An&aacute;lisis de Cotizaciones";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_aprobacion_analisis_cotizacion.php";
$arbol["id"][$i]		= "017";
$arbol["padre"][$i]		= "001";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]	 = "SOC";
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "Generaci&oacute;n de Ordenes de Compra";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_generar_orden_analisis.php";
$arbol["id"][$i]		= "018";
$arbol["padre"][$i]		= "001";
$arbol["numero_hijos"][$i]	= 0;

$i++;
$arbol["sistema"][$i]       = "SOC";
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "Ubicacion de Orden de Compra";
$arbol["nombre_fisico"][$i] = "sigesp_soc_r_orden_ubicacioncompra.php";
$arbol["id"][$i]            = "019";
$arbol["padre"][$i]         = "011";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]       = "SOC";
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "Imputaci?n Presupuestaria de Ordenes de Compra";
$arbol["nombre_fisico"][$i] = "sigesp_soc_r_imputacion_spg_orden_compra.php";
$arbol["id"][$i]            = "020";
$arbol["padre"][$i]         = "011";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]		= "SOC";
$arbol["nivel"][$i]			= 1;
$arbol["nombre_logico"][$i] = "Anulacion Analisis de Cotizaci&oacute;n";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_anulacion_analisis_cotizacion.php";
$arbol["id"][$i]			= "021";
$arbol["padre"][$i]			= "001";
$arbol["numero_hijos"][$i]	= 0;

$i++;
$arbol["sistema"][$i]		= "SOC";
$arbol["nivel"][$i]			= 1;
$arbol["nombre_logico"][$i] = "Anulacion Registro de Cotizaci&oacute;n";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_anulacion_registro_cotizacion.php";
$arbol["id"][$i]			= "022";
$arbol["padre"][$i]			= "001";
$arbol["numero_hijos"][$i]	= 0;

$i++;
$arbol["sistema"][$i]		= "SOC";
$arbol["nivel"][$i]			= 1;
$arbol["nombre_logico"][$i] = "Anulacion Solicitud de Cotizaci&oacute;n";
$arbol["nombre_fisico"][$i] = "sigesp_soc_p_anulacion_solicitud_cotizacion.php";
$arbol["id"][$i]			= "023";
$arbol["padre"][$i]			= "001";
$arbol["numero_hijos"][$i]	= 0;

$i++;
$arbol["sistema"][$i]       = "SOC";
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "Relaci?n Mensual de Ordenes de Compra ";
$arbol["nombre_fisico"][$i] = "sigesp_soc_r_orden_relacionmensual.php";
$arbol["id"][$i]            = "019";
$arbol["padre"][$i]         = "011";
$arbol["numero_hijos"][$i]  = 0;

$i++;
$arbol["sistema"][$i]       = "SOC";
$arbol["nivel"][$i]         = 1;
$arbol["nombre_logico"][$i] = "Reporte de Compras de Articulos Detallado";
$arbol["nombre_fisico"][$i] = "sigesp_soc_r_articulos.php";
$arbol["id"][$i]            = "024";
$arbol["padre"][$i]         = "011";
$arbol["numero_hijos"][$i]  = 0;


$gi_total=$i;
?>