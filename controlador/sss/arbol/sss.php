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
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=0;
$arbol["nombre_logico"][$li]="Definiciones";
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
$arbol["descargar"][$li]=0;

$li++; // 002
$arbol["sistema"][$li]="SSS";
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
$arbol["descargar"][$li]=0;

$li++; // 003
$arbol["sistema"][$li]="SSS";
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
$arbol["descargar"][$li]=0;

$li++; // 004
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Grupo";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_grupo.html";
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
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;  // 005
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Usuario";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuario.html";
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
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 006
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Sistema";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_sistema.html";
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
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;  // 007
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Aplicar Perfil";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_perfiles.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 008
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Usuarios a Nómina";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuariosnomina.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 009
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Usuarios a Presupuesto";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuariospresupuesto.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 010
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Usuarios a Unidad Ejecutora";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuariosunidad.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 011
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Usuarios a Constantes de Nómina";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuariosconstante.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 012
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Usuarios a Personal";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuariospersonal.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 013 
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Transferir Usuario y Permisología";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_transferirusuario.html";
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
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;  // 014
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Cambio de Password";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_cambiopassword.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=0;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;   // 015
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Actualizar Menu";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_actualizarmenu.html";
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
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=1;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;   // 016
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Configurar Envío de Correo";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_enviocorreo.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 017
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Auditoría";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_auditoria.html";
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
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 018
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Permisos";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_permisos.html";
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
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 019
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Traspasos";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_traspasos.html";
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
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;   // 020
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Usuarios a Almacen";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuariosalmacen.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;   // 021
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Usuarios a Centro de Costos";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuarioscentrocosto.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;   // 022
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Usuarios a niveles de Aprobación";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_nivelesaprobacion.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=1;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++;   // 022
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Usuarios a Cuentas de Banco";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuarioscuentabanco.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 023
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Usuarios/Grupo";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuariosgrupos.html";
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
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=1;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 024
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Usuarios a Prefijo";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuariosprefijo.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 025
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Usuarios a ODI";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_usuariosodi.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
$arbol["numero_hijos"][$li]=0;
$arbol["visible"][$li]=1;
$arbol["enabled"][$li]=1;
$arbol["leer"][$li]=0;
$arbol["incluir"][$li]=0;
$arbol["cambiar"][$li]=1;
$arbol["eliminar"][$li]=1;
$arbol["imprimir"][$li]=0;
$arbol["administrativo"][$li]=1;
$arbol["anular"][$li]=0;
$arbol["ejecutar"][$li]=0;
$arbol["ayuda"][$li]=1;
$arbol["cancelar"][$li]=0;
$arbol["enviarcorreo"][$li]=0;
$arbol["descargar"][$li]=0;

$li++; // 026
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Asignar Permisos en Lote";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_permisosenlote.html";
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

$li++; // 027
$arbol["sistema"][$li]="SSS";
$arbol["nivel"][$li]=1;
$arbol["nombre_logico"][$li]="Firmas Dinamicas";
$arbol["nombre_fisico"][$li]="sigesp_vis_sss_firmasdinamicas.html";
$arbol["id"][$li]=$li;
$arbol["padre"][$li]="002";
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

$gi_total=$li;
?>
