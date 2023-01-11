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

$i=1;
//001
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=0;
$arbol["nombre_logico"][$i]="Procesos";
$arbol["nombre_fisico"][$i]="";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="000";
$arbol["numero_hijos"][$i]=1;

$i++;
//002
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=1;
$arbol["nombre_logico"][$i]="Mantenimiento";
$arbol["nombre_fisico"][$i]="";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="001";
$arbol["numero_hijos"][$i]=4;

$i++;
//003
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=1;
$arbol["nombre_logico"][$i]="Integracion";
$arbol["nombre_fisico"][$i]="";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="001";
$arbol["numero_hijos"][$i]=1;

$i++;
//004
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Contabilidad";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_contabilidad.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//005
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Presupuesto de Gasto";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_presupuesto_gasto.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//006
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Release";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_release.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//007
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Reprocesar Comprobantes Descuadrados";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_reprocesar_comprobantes.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//008
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Presupuesto de Ingreso";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_presupuesto_ingreso.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//009
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Inventario";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_reprocesar_existencias.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//010
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Reprocesar Fecha de comprobantes";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_reprocesar_fechacomprobantes.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//011
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Solicitudes de Pago sin Detalle";
$arbol["nombre_fisico"][$i]="sigesp_ins_r_solicitudpago.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//012
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Cambios de Alicuota del IVA ";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_cambioiva.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//013
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Consolidación Contable";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_consolidacion_contable.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//014
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Traspaso de Conceptos y Aportes";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_traspaso_conceptos_aportes.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//015
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Traspaso de Movimientos Bancarios";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_traspaso_movbancarios.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//016
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Mantenimiento de Documentos a Libro de Compra";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_docs_a_libcompra.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//017
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="sigefirrhh";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_integracionsigefirrhh.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="003";
$arbol["numero_hijos"][$i]=0;

$i++;
//018
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Traspaso Históricos Nómina";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_traspaso_historicossno.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//019
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Reprocesar Fuente de Financimiento";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_reprocesar_fuentefinanciamiento.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//020
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Comprobantes Descuadrados";
$arbol["nombre_fisico"][$i]="sigesp_ins_r_compdescuadrado.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//021
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Reclasificar Cuentas Contables";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_reclasificar_scgcuentas.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="002";
$arbol["numero_hijos"][$i]=0;

$i++;
//022
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Sargus";
$arbol["nombre_fisico"][$i]="";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="003";
$arbol["numero_hijos"][$i]=1;

$i++;
//023
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Configuracion";
$arbol["nombre_fisico"][$i]="sigesp_ins_d_configuracionsargus.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="022";
$arbol["numero_hijos"][$i]=0;

$i++;
//024
$arbol["sistema"][$i]="INS";
$arbol["nivel"][$i]=2;
$arbol["nombre_logico"][$i]="Integracion";
$arbol["nombre_fisico"][$i]="sigesp_ins_p_integracionsargus.php";
$arbol["id"][$i]=$i;
$arbol["padre"][$i]="022";
$arbol["numero_hijos"][$i]=0;

$gi_total=$i;

?>