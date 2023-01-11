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

$li_i=0;
$li_i++; // 001
$arbol["sistema"][$li_i]="SEP";
$arbol["nivel"][$li_i]=0;
$arbol["nombre_logico"][$li_i]="Procesos";
$arbol["nombre_fisico"][$li_i]="";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="000";
$arbol["numero_hijos"][$li_i]=2;

$li_i++; // 002
$arbol["sistema"][$li_i]="SEP";
$arbol["nivel"][$li_i]=0;
$arbol["nombre_logico"][$li_i]="Reportes";
$arbol["nombre_fisico"][$li_i]="";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="000";
$arbol["numero_hijos"][$li_i]=1;

$li_i++; // 003
$arbol["sistema"][$li_i]="SEP";
$arbol["nivel"][$li_i]=0;
$arbol["nombre_logico"][$li_i]="Créditos";
$arbol["nombre_fisico"][$li_i]="";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="000";
$arbol["numero_hijos"][$li_i]=1;

$li_i++; // 003
$arbol["sistema"][$li_i]="SEP";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Registro de Solicitud de Ejecución Presupuestaria";
$arbol["nombre_fisico"][$li_i]="sigesp_sep_p_solicitud.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 004
$arbol["sistema"][$li_i]="SEP";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Aprobación de Solicitud de Ejecución Presupuestaria";
$arbol["nombre_fisico"][$li_i]="sigesp_sep_p_aprobacion.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 005
$arbol["sistema"][$li_i]="SEP";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Anulación de Solicitud de Ejecución Presupuestaria";
$arbol["nombre_fisico"][$li_i]="sigesp_sep_p_anulacion.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 006
$arbol["sistema"][$li_i]="SEP";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reporte de Solicitudes";
$arbol["nombre_fisico"][$li_i]="sigesp_sep_r_solicitudes.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 006
$arbol["sistema"][$li_i]="SEP";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Aprobación de Créditos";
$arbol["nombre_fisico"][$li_i]="sigesp_sep_p_aprobacioncreditos.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 006
$arbol["sistema"][$li_i]="SEP";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Ubicacion de Solicitudes";
$arbol["nombre_fisico"][$li_i]="sigesp_sep_r_ubicacionsolicitudes.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 005
$arbol["sistema"][$li_i]="SEP";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Autorizar Cantidades";
$arbol["nombre_fisico"][$li_i]="sigesp_sep_p_autorizarcantidades.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$gi_total=$li_i;
?>