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

$li++; // 001
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=0;
$arbol["nombre_logico"][$li]="Definiciones";
$arbol["nombre_fisico"][$li]=" ";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="000";
$arbol["numero_hijos"][$li]=1;
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
$arbol["ayuda"][$li]=0;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 002
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=0;
$arbol["nombre_logico"][$li]="Procesos";
$arbol["nombre_fisico"][$li]=" ";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="000";
$arbol["numero_hijos"][$li]=1;
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
$arbol["ayuda"][$li]=0;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 003
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=0;
$arbol["nombre_logico"][$li]="Reportes";
$arbol["nombre_fisico"][$li]=" ";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="000";
$arbol["numero_hijos"][$li]=1;
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
$arbol["ayuda"][$li]=0;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 004
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Parametro de Calificación";
$arbol["nombre_fisico"][$li]="sigesp_vis_rpc_parametroclasificacion.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="001";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1; 
$arbol["leer"][$li]=1; 
$arbol["incluir"][$li]=1; 
$arbol["cambiar"][$li]=1; 
$arbol["eliminar"][$li]=1; 
$arbol["imprimir"][$li]=0; 
$arbol["administrativo"][$li]=0; 
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0; 
$arbol["ayuda"][$li]=1; 
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0; 
$arbol["descargar"][$li]=0;

$li++;  // 005
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Recaudos o Documentos";
$arbol["nombre_fisico"][$li]="sigesp_vis_rpc_documentos.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="001";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1; 
$arbol["enabled"][$li]=1; 
$arbol["leer"][$li]=1; 
$arbol["incluir"][$li]=1; 
$arbol["cambiar"][$li]=1; 
$arbol["eliminar"][$li]=1; 
$arbol["imprimir"][$li]=0; 
$arbol["administrativo"][$li]=0; 
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0; 
$arbol["ayuda"][$li]=1; 
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0; 
$arbol["descargar"][$li]=0;

$li++;  // 006
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Especialidad";
$arbol["nombre_fisico"][$li]="sigesp_vis_rpc_especialidad.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="001";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1; 
$arbol["leer"][$li]=1; 
$arbol["incluir"][$li]=1; 
$arbol["cambiar"][$li]=1; 
$arbol["eliminar"][$li]=1; 
$arbol["imprimir"][$li]=0; 
$arbol["administrativo"][$li]=0; 
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0; 
$arbol["ayuda"][$li]=1; 
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0; 
$arbol["descargar"][$li]=0;

$li++;  // 007
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Tipo Empresa";
$arbol["nombre_fisico"][$li]="sigesp_vis_rpc_tipoempresa.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="001";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1; 
$arbol["leer"][$li]=1; 
$arbol["incluir"][$li]=1; 
$arbol["cambiar"][$li]=1; 
$arbol["eliminar"][$li]=1; 
$arbol["imprimir"][$li]=0; 
$arbol["administrativo"][$li]=0; 
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0; 
$arbol["ayuda"][$li]=1; 
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0; 
$arbol["descargar"][$li]=0;

$li++;  // 008
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Proveedor";
$arbol["nombre_fisico"][$li]="sigesp_vis_rpc_proveedor.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="001";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1; 
$arbol["leer"][$li]=1; 
$arbol["incluir"][$li]=1; 
$arbol["cambiar"][$li]=1; 
$arbol["eliminar"][$li]=1; 
$arbol["imprimir"][$li]=0; 
$arbol["administrativo"][$li]=0; 
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0; 
$arbol["ayuda"][$li]=1; 
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0; 
$arbol["descargar"][$li]=0;

$li++;  // 009
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Beneficiario";
$arbol["nombre_fisico"][$li]="sigesp_vis_rpc_beneficiario.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="001";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1; 
$arbol["leer"][$li]=1; 
$arbol["incluir"][$li]=1; 
$arbol["cambiar"][$li]=1; 
$arbol["eliminar"][$li]=1; 
$arbol["imprimir"][$li]=0; 
$arbol["administrativo"][$li]=0; 
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0; 
$arbol["ayuda"][$li]=1; 
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0; 
$arbol["descargar"][$li]=0;

$li++;  // 010
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Listado de Beneficiarios";
$arbol["nombre_fisico"][$li]="sigesp_vis_rpc_reporteBeneficiarios.html";
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
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0; 
$arbol["descargar"][$li]=0;

$li++;  // 011
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Listado de Proveedores";
$arbol["nombre_fisico"][$li]="sigesp_vis_rpc_reporteProveedores.html";
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
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0; 
$arbol["descargar"][$li]=0;

$li++;  // 012
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Transferencia de Personal";
$arbol["nombre_fisico"][$li]="sigesp_vis_rpc_transferencia.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
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
$arbol["descargar"][$li]=0;

$li++;  // 013
$arbol["sistema"][$li]="RPC";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Cambio Estatus de Proveedor";
$arbol["nombre_fisico"][$li]="sigesp_vis_rpc_cambioestatus_proveedor.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
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
$arbol["descargar"][$li]=0;

$gi_total=$li;
?>
