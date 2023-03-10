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
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=0;
$arbol["nombre_logico"][$li_i]="Procesos";
$arbol["nombre_fisico"][$li_i]="";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="000";
$arbol["numero_hijos"][$li_i]=2;

$li_i++; // 002
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=0;
$arbol["nombre_logico"][$li_i]="Reportes";
$arbol["nombre_fisico"][$li_i]="";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="000";
$arbol["numero_hijos"][$li_i]=9;


$li_i++; // 003
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=0;
$arbol["nombre_logico"][$li_i]="Mantenimiento";
$arbol["nombre_fisico"][$li_i]="";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="000";
$arbol["numero_hijos"][$li_i]=1;

$li_i++; // 004
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Cierres de Ejercicios";
$arbol["nombre_fisico"][$li_i]="";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=2;

$li_i++; // 005
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Comprobantes Contables";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_p_comprobante.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="001";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 006
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=2;
$arbol["nombre_logico"][$li_i]="Cierre Mensual";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_p_cierremensual.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="004";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 007
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=2;
$arbol["nombre_logico"][$li_i]="Cierre Anual";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_p_cierreanual.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="004";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 008
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Configuraci?n";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_p_configuracion.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="003";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 009
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reportes - Mayor Anal?tico";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_r_mayor_analitico.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 010
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reportes - Balance Comprobaci?n";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_r_balance_comprobacion.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 011
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reportes - Comprobantes";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_r_comprobantes.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 012
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reportes - Listado Cuentas";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_r_listadocuentas.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 013
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reportes - Plan Unico de Cuentas";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_r_listadoplanunico.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 014
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reportes - Balance General Mensual";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_r_balance_general.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 015
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reportes - Balance General Anual";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_r_balance_general_anual.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 016
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reportes - Hoja de Trabajo";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_r_hoja_trabajo.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;

$li_i++; // 017
$arbol["sistema"][$li_i]="SCF";
$arbol["nivel"][$li_i]=1;
$arbol["nombre_logico"][$li_i]="Reportes - Movimientos del Mes";
$arbol["nombre_fisico"][$li_i]="sigesp_scf_r_movimientos_mes.php";
$arbol["id"][$li_i]=$li_i;
$arbol["padre"][$li_i]="002";
$arbol["numero_hijos"][$li_i]=0;


$gi_total=$li_i;

?>
