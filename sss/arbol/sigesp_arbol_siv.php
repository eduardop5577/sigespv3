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

$li_i=000;

$li_i++; // 001
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=0;
$arbol["nombre_logico"][$li_i]="Definiciones";
$arbol["nombre_fisico"][$li_i]="";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="000";
$arbol["numero_hijos"][$li_i]=1;

$li_i++; // 002
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=0;
$arbol["nombre_logico"][$li_i]="Procesos";
$arbol["nombre_fisico"][$li_i]="";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="000";
$arbol["numero_hijos"][$li_i]=1;

$li_i++; // 003
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=0;
$arbol["nombre_logico"][$li_i]="Reportes";
$arbol["nombre_fisico"][$li_i]="";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="000";
$arbol["numero_hijos"][$li_i]=1;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Configuración de Inventario";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_d_configuracion.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Tipo de Articulo";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_d_tipoarticulo.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Unidad de Medida";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_d_unidadmedida.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Definición de Almacén";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_d_almacen.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Definición de Segmento";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_d_segmento.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Definición de Familia";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_d_familia.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Definición de CLase";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_d_clase.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Definición de Producto";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_d_producto.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Definición de Artículo";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_d_articulo.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Entrada de Suministros a Almacén";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_recepcion.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Aprobación de Entrada de Suministros a Almacén";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_aprobacionrecepcion.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Transferencia entre Almacenes";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_transferencia.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Despacho de Suministros";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_despacho.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Cierre de O/C";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_cerraroc.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Toma de Inventario";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_toma.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reverso de Entrada de Suministros a Almacén";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_revrecepcion.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reverso de Transferencia";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_revtransferencia.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reverso de Despachos";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_revdespacho.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Existencias de Artículos";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_articuloxalmacen.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Existencias de Artículos Mensuales";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_articuloxalmacen_mensual.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Listado de Artículos Detallados";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_articulosespecificos.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Movimientos de Artículos";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_movimientos.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Movimientos Detallado de Artículos";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_movimientos_articulos_det.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;


$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reporte de Articulos por Tipo";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_articuloxtipo.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Articulos por Solicitar";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_articuloxsolicitar.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Articulos por Vencer";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_articulosxvencer.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Listado de Artículos";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_listadoarticulos.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reporte de Ordenes de Despacho";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_despachos.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reporte de Entradas de Suministros a Almacen";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_recepcion.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reporte de Transferencia entre Almacenes";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_transferencia.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Resumen de Inventario";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_inventario.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Listado de Almacenes";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_almacenes.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Valoración de Inventario";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_valinventario.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Valoración de Toma de Inventario";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_valtoma.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Valoración de Ajustes de Inventario";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_valajustes.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Cierre de Ordenes de Compra";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_cierre.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Acta de Recepcion de Bienes";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_acta_recepcion_bienes.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Listado Imputación Presupuestaria del Inventario";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_imputacionpresupuestaria.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reporte de Articulos Despachados";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_articulos_despachados.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Cierre de SEP";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_cerrarsep.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reporte de Cierre de SEP";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_cierresep.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Conversion de articulos";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_produccion.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Empaquetado  de articulos";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_empaquetado.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Aprobacion de Empaquetado";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_aprobacionempaquetado.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Causas de Movimiento";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_d_causas.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;


$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Movimientos de Materiales";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_p_asignacion.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; 
$arbol["sistema"][$li_i]="SIV";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Despacho de Articulos";
$arbol["nombre_fisico"][$li_i]="sigesp_siv_r_despachoarticulos.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;


$gi_total=$li_i;

?>
