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
$arbol["sistema"][$li]="SFD";
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
$arbol["sistema"][$li]="SFD";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Firmar";
$arbol["nombre_fisico"][$li]="sigesp_vis_sfd_firmar.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="001";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
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
$arbol["descargar"][$li]=1;

$li++;//003
$arbol["sistema"][$li]="SFD";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Verificar";
$arbol["nombre_fisico"][$li]="sigesp_vis_sfd_verificar.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="001";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=0;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=0;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$gi_total=$li;

?>
