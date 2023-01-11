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

$li=0;

$li++; // 001
$arbol['sistema'][$li]='SOC';
$arbol['nivel'][$li]=0;
$arbol['nombre_logico'][$li]='Orden de Compra';
$arbol['nombre_fisico'][$li]=' ';
$arbol['id'][$li]=$li;
$arbol['padre'][$li]='000';
$arbol['numero_hijos'][$li]=1;
$arbol['visible'][$li]=1;
$arbol['enabled'][$li]=1;
$arbol['leer'][$li]=0;
$arbol['incluir'][$li]=0;
$arbol['cambiar'][$li]=0;
$arbol['eliminar'][$li]=0;
$arbol['imprimir'][$li]=0;
$arbol['administrativo'][$li]=0;
$arbol['anular'][$li]=0;
$arbol['ejecutar'][$li]=0;
$arbol['ayuda'][$li]=0;
$arbol['cancelar'][$li]=0;
$arbol['enviarcorreo'][$li]=0;
$arbol['descargar'][$li]=0;

$li++; // 002
$arbol['sistema'][$li]='SOC';
$arbol['nivel'][$li]=0;
$arbol['nombre_logico'][$li]='Reportes';
$arbol['nombre_fisico'][$li]=' ';
$arbol['id'][$li]=$li;
$arbol['padre'][$li]='000';
$arbol['numero_hijos'][$li]=1;
$arbol['visible'][$li]=1;
$arbol['enabled'][$li]=1;
$arbol['leer'][$li]=0;
$arbol['incluir'][$li]=0;
$arbol['cambiar'][$li]=0;
$arbol['eliminar'][$li]=0;
$arbol['imprimir'][$li]=0;
$arbol['administrativo'][$li]=0;
$arbol['anular'][$li]=0;
$arbol['ejecutar'][$li]=0;
$arbol['ayuda'][$li]=0;
$arbol['cancelar'][$li]=0;
$arbol['enviarcorreo'][$li]=0;
$arbol['descargar'][$li]=0;

$i++; //003
$arbol['sistema'][$i] = 'SOC';
$arbol['nivel'][$i] = 1;
$arbol['nombre_logico'][$i] = 'Registro';
$arbol['nombre_fisico'][$i] = 'sigesp_soc_p_registro_orden_compra.php';
$arbol['id'][$li]=$li;
$arbol['padre'][$li]='001';
$arbol['numero_hijos'][$li]=0;
$arbol['visible'][$li]=1;
$arbol['enabled'][$li]=1;
$arbol['leer'][$li]=1;
$arbol['incluir'][$li]=1;
$arbol['cambiar'][$li]=1;
$arbol['eliminar'][$li]=1;
$arbol['imprimir'][$li]=1;
$arbol['administrativo'][$li]=1;
$arbol['anular'][$li]=0;
$arbol['ejecutar'][$li]=0;
$arbol['ayuda'][$li]=0;
$arbol['cancelar'][$li]=1;
$arbol['enviarcorreo'][$li]=0;
$arbol['descargar'][$li]=0;

$li++; // 004
$arbol['sistema'][$li]='SOC';
$arbol['nivel'][$li]=1;
$arbol['nombre_logico'][$li]='Aprobacion / Anulacion';
$arbol['nombre_fisico'][$li]='sigesp_soc_p_aprobacion_anulacion.php';
$arbol['id'][$li]=$li;
$arbol['padre'][$li]='001';
$arbol['numero_hijos'][$li]=0;
$arbol['visible'][$li]=1;
$arbol['enabled'][$li]=1;
$arbol['leer'][$li]=1;
$arbol['incluir'][$li]=0;
$arbol['cambiar'][$li]=0;
$arbol['eliminar'][$li]=0;
$arbol['imprimir'][$li]=0;
$arbol['administrativo'][$li]=0;
$arbol['anular'][$li]=0;
$arbol['ejecutar'][$li]=1;
$arbol['ayuda'][$li]=0;
$arbol['cancelar'][$li]=1;
$arbol['enviarcorreo'][$li]=0;
$arbol['descargar'][$li]=0;

$li++; // 005
$arbol['sistema'][$li]='SOC';
$arbol['nivel'][$li]=1;
$arbol['nombre_logico'][$li]='Listado';
$arbol['nombre_fisico'][$li]='sigesp_soc_r_orden_compra.php';
$arbol['id'][$li]=$li;
$arbol['padre'][$li]='002';
$arbol['numero_hijos'][$li]=0;
$arbol['visible'][$li]=1;
$arbol['enabled'][$li]=1;
$arbol['leer'][$li]=0;
$arbol['incluir'][$li]=0;
$arbol['cambiar'][$li]=0;
$arbol['eliminar'][$li]=0;
$arbol['imprimir'][$li]=1;
$arbol['administrativo'][$li]=0;
$arbol['anular'][$li]=0;
$arbol['ejecutar'][$li]=0;
$arbol['ayuda'][$li]=0;
$arbol['cancelar'][$li]=1;
$arbol['enviarcorreo'][$li]=0;
$arbol['descargar'][$li]=0;

$li++; // 006
$arbol['sistema'][$li]='SOC';
$arbol['nivel'][$li]=1;
$arbol['nombre_logico'][$li]='Aceptacion de Servicios';
$arbol['nombre_fisico'][$li]='sigesp_soc_r_aceptacion_servicios.php';
$arbol['id'][$li]=$li;
$arbol['padre'][$li]='002';
$arbol['numero_hijos'][$li]=0;
$arbol['visible'][$li]=1;
$arbol['enabled'][$li]=1;
$arbol['leer'][$li]=0;
$arbol['incluir'][$li]=0;
$arbol['cambiar'][$li]=0;
$arbol['eliminar'][$li]=0;
$arbol['imprimir'][$li]=1;
$arbol['administrativo'][$li]=0;
$arbol['anular'][$li]=0;
$arbol['ejecutar'][$li]=0;
$arbol['ayuda'][$li]=0;
$arbol['cancelar'][$li]=1;
$arbol['enviarcorreo'][$li]=0;
$arbol['descargar'][$li]=0;

$li++; // 007
$arbol['sistema'][$li]='SOC';
$arbol['nivel'][$li]=1;
$arbol['nombre_logico'][$li]='Ubicacion';
$arbol['nombre_fisico'][$li]='sigesp_soc_r_orden_ubicacioncompra.php';
$arbol['id'][$li]=$li;
$arbol['padre'][$li]='002';
$arbol['numero_hijos'][$li]=0;
$arbol['visible'][$li]=1;
$arbol['enabled'][$li]=1;
$arbol['leer'][$li]=0;
$arbol['incluir'][$li]=0;
$arbol['cambiar'][$li]=0;
$arbol['eliminar'][$li]=0;
$arbol['imprimir'][$li]=1;
$arbol['administrativo'][$li]=0;
$arbol['anular'][$li]=0;
$arbol['ejecutar'][$li]=0;
$arbol['ayuda'][$li]=0;
$arbol['cancelar'][$li]=1;
$arbol['enviarcorreo'][$li]=0;
$arbol['descargar'][$li]=0;

$li++; // 008
$arbol['sistema'][$li]='SOC';
$arbol['nivel'][$li]=1;
$arbol['nombre_logico'][$li]='Imputacion Presupuestaria';
$arbol['nombre_fisico'][$li]='sigesp_soc_r_imputacion_spg_orden_compra.php';
$arbol['id'][$li]=$li;
$arbol['padre'][$li]='002';
$arbol['numero_hijos'][$li]=0;
$arbol['visible'][$li]=1;
$arbol['enabled'][$li]=1;
$arbol['leer'][$li]=0;
$arbol['incluir'][$li]=0;
$arbol['cambiar'][$li]=0;
$arbol['eliminar'][$li]=0;
$arbol['imprimir'][$li]=1;
$arbol['administrativo'][$li]=0;
$arbol['anular'][$li]=0;
$arbol['ejecutar'][$li]=0;
$arbol['ayuda'][$li]=0;
$arbol['cancelar'][$li]=1;
$arbol['enviarcorreo'][$li]=0;
$arbol['descargar'][$li]=0;

$li++; // 009
$arbol['sistema'][$li]='SOC';
$arbol['nivel'][$li]=1;
$arbol['nombre_logico'][$li]='Relacion Mensual';
$arbol['nombre_fisico'][$li]='sigesp_soc_r_orden_relacionmensual.php';
$arbol['id'][$li]=$li;
$arbol['padre'][$li]='002';
$arbol['numero_hijos'][$li]=0;
$arbol['visible'][$li]=1;
$arbol['enabled'][$li]=1;
$arbol['leer'][$li]=0;
$arbol['incluir'][$li]=0;
$arbol['cambiar'][$li]=0;
$arbol['eliminar'][$li]=0;
$arbol['imprimir'][$li]=1;
$arbol['administrativo'][$li]=0;
$arbol['anular'][$li]=0;
$arbol['ejecutar'][$li]=0;
$arbol['ayuda'][$li]=0;
$arbol['cancelar'][$li]=1;
$arbol['enviarcorreo'][$li]=0;
$arbol['descargar'][$li]=0;

$li++; // 009
$arbol['sistema'][$li]='SOC';
$arbol['nivel'][$li]=1;
$arbol['nombre_logico'][$li]='Articulos Detallado';
$arbol['nombre_fisico'][$li]='sigesp_soc_r_articulos.php';
$arbol['id'][$li]=$li;
$arbol['padre'][$li]='002';
$arbol['numero_hijos'][$li]=0;
$arbol['visible'][$li]=1;
$arbol['enabled'][$li]=1;
$arbol['leer'][$li]=0;
$arbol['incluir'][$li]=0;
$arbol['cambiar'][$li]=0;
$arbol['eliminar'][$li]=0;
$arbol['imprimir'][$li]=1;
$arbol['administrativo'][$li]=0;
$arbol['anular'][$li]=0;
$arbol['ejecutar'][$li]=0;
$arbol['ayuda'][$li]=0;
$arbol['cancelar'][$li]=1;
$arbol['enviarcorreo'][$li]=0;
$arbol['descargar'][$li]=0;

$gi_total=$li;
?>