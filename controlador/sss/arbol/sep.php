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
$arbol['sistema'][$li]='SEP';
$arbol['nivel'][$li]=0;
$arbol['nombre_logico'][$li]='Solicitud de Ejecucion Presupuestaria';
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
$arbol['sistema'][$li]='SEP';
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

$li++; // 003
$arbol['sistema'][$li]='SEP';
$arbol['nivel'][$li]=1;
$arbol['nombre_logico'][$li]='Registro';
$arbol['nombre_fisico'][$li]='sigesp_sep_p_solicitud.php';
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
$arbol['sistema'][$li]='SEP';
$arbol['nivel'][$li]=1;
$arbol['nombre_logico'][$li]='Aprobacion / Anulacion';
$arbol['nombre_fisico'][$li]='sigesp_sep_p_aprobacion_anulacion.php';
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
$arbol['sistema'][$li]='SEP';
$arbol['nivel'][$li]=1;
$arbol['nombre_logico'][$li]='Listado';
$arbol['nombre_fisico'][$li]='sigesp_sep_r_solicitudes.php';
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
$arbol['sistema'][$li]='SEP';
$arbol['nivel'][$li]=1;
$arbol['nombre_logico'][$li]='Ubicacion';
$arbol['nombre_fisico'][$li]='sigesp_sep_r_ubicacionsolicitudes.php';
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