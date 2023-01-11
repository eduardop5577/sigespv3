<?php
/***********************************************************************************
* @fecha de modificacion: 26/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$li=000;

$li++;//001
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=0;
$arbol["nombre_logico"][$li]="Procesos";
$arbol["nombre_fisico"][$li]=" ";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="000";
$arbol["numero_hijos"][$li]=1;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=1;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=1;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=1;

$li++;//002
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=0;
$arbol["nombre_logico"][$li]="Reportes";
$arbol["nombre_fisico"][$li]=" ";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="000";
$arbol["numero_hijos"][$li]=1;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=1;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=1;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=1;

$li++;//003
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Reportes - Comprobantes";
$arbol["nombre_fisico"][$li]=" ";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=1;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=1;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=1;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=1;

$li++;//004
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Comprobantes Contable";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_comprobante.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="001";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=1;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//005
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Cierre de Ejercicio";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_cierre.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="001";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//006
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Mayor Analítico";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_mayor_analitico.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//007
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Balance de Comprobación";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_balance_comprobacion.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//008
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=2;
$arbol["nombre_logico"][$li]="Comprobantes";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_listado_comprobante.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="003";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//009
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Estado de Resultado";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_estado_resultado.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//010
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Balance General";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_balance_general.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//011
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Listado de Cuentas";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_cuentas.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//012
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Movimientos del Mes";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_movimientos_mes.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//013
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Instructivo NTC";
$arbol["nombre_fisico"][$li]=" ";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=1;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=1;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=1;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=1;

$li++;//014
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=2;
$arbol["nombre_logico"][$li]="Situación Financiera";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_situacion_financiera.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="013";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//015
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=2;
$arbol["nombre_logico"][$li]="Rendimiento Financiero";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_rendimiento_financiero.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="013";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//016
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=2;
$arbol["nombre_logico"][$li]="Movimiento Cuentas de Patrimonio";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_movimiento_cuentas.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="013";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//017
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=2;
$arbol["nombre_logico"][$li]="Estado de Flujo de Efectivo";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_flujo_efectivo.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="013";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//018
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Estados Financieros Comparados SUDEBAN";
$arbol["nombre_fisico"][$li]=" ";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=1;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=1;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=1;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=1;

$li++;//019
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=2;
$arbol["nombre_logico"][$li]="Balance General (Forma A)";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_balance_general_formaa.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="018";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//020
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=2;
$arbol["nombre_logico"][$li]="Estado de Resultado (Forma B)";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_estado_resultado_formab.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="018";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//021
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Instructivos Onapre";
$arbol["nombre_fisico"][$li]=" ";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=1;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=1;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=1;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=1;

$li++;//022
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=2;
$arbol["nombre_logico"][$li]="Instructivo 07";
$arbol["nombre_fisico"][$li]="";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="021";
$arbol["numero_hijos"][$li]=1;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=1;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=1;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=1;

$li++;//023
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=2;
$arbol["nombre_logico"][$li]="Instructivo 08";
$arbol["nombre_fisico"][$li]="";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="021";
$arbol["numero_hijos"][$li]=1;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=1;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=1;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=1;

$li++;//024
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=3;
$arbol["nombre_logico"][$li]="0709  Resumen de inversiones";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_resumeninversiones0709.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="022";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//025
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=3;
$arbol["nombre_logico"][$li]="0711  Estado de resultado";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_estadoresultado0711.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="022";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//026
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=3;
$arbol["nombre_logico"][$li]="0712  Balance General";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_balancegeneral0712.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="022";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//027
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=3;
$arbol["nombre_logico"][$li]="0713  Presupuesto caja";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_presupuestocaja0713.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="022";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//028
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=3;
$arbol["nombre_logico"][$li]="0809  Resumen de inversiones";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_resumeninversiones0809.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="023";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//029
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=3;
$arbol["nombre_logico"][$li]="0811  Estado de resultado";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_estadoresultado0811.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="023";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//030
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=3;
$arbol["nombre_logico"][$li]="0812  Balance General";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_balancegeneral0812.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="023";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//031
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=3;
$arbol["nombre_logico"][$li]="0813  Presupuesto caja";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_presupuestocaja0813.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="023";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;
/*
$li++;//032
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Importar Comprobantes Contables en Lote";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_importarcomprobanteslote.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="001";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;*/

$li++;//033
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Estado de Resultado por Estructura";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_estado_resultado_estructura.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;//034
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=2;
$arbol["nombre_logico"][$li]="Instructivo 02";
$arbol["nombre_fisico"][$li]="";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="021";
$arbol["numero_hijos"][$li]=1;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=1;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=1;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=1;

$li++;//035
$arbol["sistema"][$li]="SCG";
$arbol["nivel"][$li]=3;
$arbol["nombre_logico"][$li]="0206 Resultado Economico Financiero";
$arbol["nombre_fisico"][$li]="sigesp_vis_scg_r_resultadofinanciero0206.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="033";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=1;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$gi_total=$li;
?>
