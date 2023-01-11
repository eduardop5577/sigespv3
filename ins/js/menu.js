/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

stm_bm(["menu08dd",430,"","../shared/imagebank/blank.gif",0,"","",0,0,0,0,1000,1,0,0,"","100%",0],this);
stm_bp("p0",[0,4,0,1,1,3,0,0,100,"",-2,"",-2,90,0,0,"#000000","#e6e6e6","",3,0,0,"#000000"]);
// Menú Principal- Archivo
stm_ai("p0i0",[0," Procesos ","","",-1,-1,0,"","_self","","","","",0,0,0,"","",0,0,0,0,1,"#F7F7F7",0,"#f4f4f4",0,"","",3,3,0,0,"#fffff7","#000000","#909090","#909090","8pt 'Tahoma','Arial'","8pt 'Tahoma','Arial'",0,0]);
stm_bp("p1",[1,4,0,0,2,3,6,1,100,"progid:DXImageTransform.Microsoft.Fade(overlap=.5,enabled=0,Duration=0.10)",-2,"",-2,100,2,3,"#999999","#ffffff","",3,1,1,"#F7F7F7"]);
// Archivo - Opciones de Segundo Nivel
stm_aix("p1i0","p0i0",[0,"Mantenimiento ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p1","p1",[1,2,0,0,2,3,0]);
stm_aix("p1i0","p1i0",[0,"Contabilidad Patrimonial  ","","",-1,-1,0,"sigesp_ins_p_contabilidad.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i1","p1i0",[0,"Consolidación Contable    ","","",-1,-1,0,"sigesp_ins_p_consolidacion_contable.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p1i0",[0,"Presupuesto de Gasto      ","","",-1,-1,0,"sigesp_ins_p_presupuesto_gasto.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i3","p1i0",[0,"Presupuesto de Ingreso    ","","",-1,-1,0,"sigesp_ins_p_presupuesto_ingreso.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i4","p1i0",[0,"Reprocesar Comprobantes ","","",-1,-1,0,"sigesp_ins_p_reprocesar_comprobantes.php","_self"]);
stm_aix("p1i4","p1i0",[0,"Reprocesar Fecha de Comprobantes ","","",-1,-1,0,"sigesp_ins_p_reprocesar_fechacomprobantes.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i4","p1i0",[0,"Reprocesar Fuente de Financimiento ","","",-1,-1,0,"sigesp_ins_p_reprocesar_fuentefinanciamiento.php","_self"]);
stm_aix("p1i5","p1i0",[0,"Reprocesar Existencias Inventario","","",-1,-1,0,"sigesp_ins_p_reprocesar_existencias.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i6","p1i0",[0,"Release						   ","","",-1,-1,0,"sigesp_ins_p_release.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i7","p1i0",[0,"Reporte Comprobantes Descuadrados","","",-1,-1,0,"sigesp_ins_r_compdescuadrado.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i7","p1i0",[0,"Reporte Solicitudes de sin Detalle Asociado","","",-1,-1,0,"sigesp_ins_r_solicitudespago.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i7","p1i0",[0,"Cambio de Alicuota del IVA","","",-1,-1,0,"sigesp_ins_d_cambioiva.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i7","p1i0",[0,"Traspaso de Conceptos y Aportes","","",-1,-1,0,"sigesp_ins_p_traspaso_conceptos_aportes.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i7","p1i0",[0,"Traspasos de Movimientos Bancarios","","",-1,-1,0,"sigesp_ins_p_traspaso_movbancarios.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i7","p1i0",[0,"Traspasos de Historicos de Nómina","","",-1,-1,0,"sigesp_ins_p_traspaso_historicossno.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p1i4","p1i0",[0,"Reprocesar Movimientos ","","",-1,-1,0,"sigesp_ins_p_reprocesar_movimientos.php","_self"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i7","p1i0",[0,"Mantenimiento de Documentos a Libro de Compra","","",-1,-1,0,"sigesp_ins_p_docs_a_libcompra.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i7","p1i0",[0,"Reclasificar Cuentas Contables","","",-1,-1,0,"sigesp_ins_p_reclasificar_scgcuentas.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i0","p0i0",[0,"Integración ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p1","p1",[1,2,0,0,2,3,0]);
stm_aix("p1i0","p1i0",[0,"SIGEFIRRHH","","",-1,-1,0,"sigesp_ins_p_integracionsigefirrhh.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
/*stm_aix("p1i0","p0i0",[0,"SARGUS ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p1","p1",[1,2,0,0,2,3,0]);
stm_aix("p1i0","p1i0",[0,"Configuracion","","",-1,-1,0,"sigesp_ins_d_configuracionsargus.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p1i0",[0,"Integracion","","",-1,-1,0,"sigesp_ins_p_integracionsargus.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();
*/stm_ep();
stm_ep();
stm_aix("p4i0","p1i0",[0," Ir a Módulos  ","","",-1,-1,0,"../escritorio.html","","","","","",6,0,0,"","",0,0,0,0,1,"#F7F7F7"]);
stm_bpx("p10","p1",[]);
stm_ep();
stm_em();