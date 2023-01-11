/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
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

// Men� Principal- Archivo
stm_ai("p0i0",[0," Procesos ","","",-1,-1,0,"","_self","","","","",0,0,0,"","",0,0,0,0,1,"#F7F7F7",0,"#f4f4f4",0,"","",3,3,0,0,"#fffff7","#000000","#909090","#909090","8pt 'Tahoma','Arial'","8pt 'Tahoma','Arial'",0,0]);
stm_bp("p1",[1,4,0,0,2,3,6,1,100,"progid:DXImageTransform.Microsoft.Fade(overlap=.5,enabled=0,Duration=0.10)",-2,"",-2,100,2,3,"#999999","#ffffff","",3,1,1,"#F7F7F7"]);
// Archivo - Opciones de Segundo Nivel
stm_aix("p1i0","p0i0",[0,"Movimiento de Banco    ","","",-1,-1,0,"sigesp_scb_p_movbanco.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p0i0",[0,"Cancelaciones ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p1","p1",[1,2,0,0,2,3,0]);
stm_aix("p1i4","p1i0",[0,"Programaci�n de Pagos ","","",-1,-1,0,"sigesp_scb_p_progpago.php","_self"]);
stm_aix("p1i4","p1i0",[0,"Desprogramaci�n de Pagos ","","",-1,-1,0,"sigesp_scb_p_desprogpago.php","_self"]);
stm_aix("p1i4","p1i0",[0,"Emisi�n de Cheques  ","","",-1,-1,0,"sigesp_scb_p_emision_chq.php","_self"]);
stm_aix("p1i4","p1i0",[0,"Eliminaci�n de Cheques no Contabilizados ","","",-1,-1,0,"sigesp_scb_p_elimin_chq.php","_self"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i4","p1i0",[0,"Pago Directo ","","",-1,-1,0,"sigesp_scb_p_pago_directo.php","_self"]);
stm_aix("p1i4","p1i0",[0,"Orden de Pago Directa ","","",-1,-1,0,"sigesp_scb_p_orden_pago_directo.php","_self"]);
stm_aix("p1i4","p1i0",[0,"Orden de Pago Directa con Compromiso Previo","","",-1,-1,0,"sigesp_scb_p_opd_causapaga.php","_self"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i2","p0i0",[0,"Carta Orden ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p1","p1",[1,2,0,0,2,3,0]);
stm_aix("p1i4","p1i0",[0,"&Uacute;nica Nota de D&eacute;bito  ","","",-1,-1,0,"sigesp_scb_p_carta_orden.php","_self"]);
stm_aix("p1i4","p1i0",[0,"M&uacute;ltiples Notas de D&eacute;bito  ","","",-1,-1,0,"sigesp_scb_p_carta_orden_mnd.php","_self"]);
stm_ep();
stm_aix("p1i4","p1i0",[0,"Eliminaci�n de Carta Orden no Contabilizada ","","",-1,-1,0,"sigesp_scb_p_elimin_carta_orden.php","_self"]);
stm_ep();
stm_aix("p1i4","p1i0",[0,"Conciliaci�n Bancaria    ","","",-1,-1,0,"sigesp_scb_p_conciliacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i4","p1i0",[0,"Colocaciones    ","","",-1,-1,0,"sigesp_scb_p_movcol.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p1i0",[0,"Retenciones Municipales ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edici�n - Opciones de Tercer Nivel
stm_aix("p1i4","p1i0",[0,"Crear Comprobante ","","",-1,-1,0,"sigesp_scb_p_cmp_ret_mcp.php","_self"]);
stm_aix("p1i4","p1i0",[0,"Modificar Comprobante ","","",-1,-1,0,"sigesp_scb_p_cmp_ret_mod.php","_self"]);
stm_aix("p1i4","p1i0",[0,"Otros Comprobantes ","","",-1,-1,0,"sigesp_scb_p_cmp_ret_mun_otros.php","_self"]);
stm_ep();
stm_aix("p1i4","p1i0",[0,"Transferencia Bancaria    ","","",-1,-1,0,"sigesp_scb_p_transferencias.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i4","p1i0",[0,"Entrega de Cheques    ","","",-1,-1,0,"sigesp_scb_p_entregach.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i4","p1i0",[0,"Reverso de Entrega de Cheques    ","","",-1,-1,0,"sigesp_scb_p_reverso_entregach.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i0","p0i0",[0,"Eliminacion Anulados Monto 0   ","","",-1,-1,0,"sigesp_scb_p_elimin_anulado.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();

// Men� Principal - Procesos
stm_aix("p0i3","p0i0",[0," Reportes "]);
stm_bpx("p5","p1",[1,4,0,0,2,3,6,7]);
stm_aix("p4i0","p1i0",[0," Disponibilidad Financiera    ","","",-1,-1,0,"sigesp_scb_r_disponibilidad.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p0i0",[0," Listado de Documentos ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edici�n - Opciones de Tercer Nivel
stm_aix("p1i4","p1i0",[0," Documentos ","","",-1,-1,0,"sigesp_scb_r_documentos.php","_self"]);
stm_aix("p1i4","p1i0",[0," Conciliados ","","",-1,-1,0,"sigesp_scb_r_list_doc_conciliados.php","_self"]);
stm_aix("p4i0","p1i0",[0," Documentos en Transito ","","",-1,-1,0,"sigesp_scb_r_list_doc_transito.php","_self"]);
stm_ep();
stm_aix("p4i0","p1i0",[0," Listado de Ordenes de Pago Directa  ","","",-1,-1,0,"sigesp_scb_r_ordenpago.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Pagos    ","","",-1,-1,0,"sigesp_scb_r_pagos.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Registros Contables   ","","",-1,-1,0,"sigesp_scb_r_reg_contables.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Conciliacion Bancaria   ","","",-1,-1,0,"sigesp_scb_r_conciliacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p0i0",[0," Estado de Cuenta      ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edici�n - Opciones de Tercer Nivel
stm_aix("p1i4","p1i0",[0," Formato 1 ","","",-1,-1,0,"sigesp_scb_r_estado_cta.php","_self"]);
stm_aix("p1i4","p1i0",[0," Resumido ","","",-1,-1,0,"sigesp_scb_r_estado_ctares.php","_self"]);
//stm_aix("p1i4","p1i0",[0," Colocaciones ","","",-1,-1,0,"sigesp_scb_r_colocaciones.php","_self"]);
stm_ep();
stm_aix("p1i2","p0i0",[0," Otros  ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edici�n - Opciones de Tercer Nivel
stm_aix("p1i4","p1i0",[0," Movimientos Presupuestarios por Banco ","","",-1,-1,0,"sigesp_scb_r_spg_x_banco.php","_self"]);
stm_aix("p1i4","p1i0",[0," Listado de Chequeras ","","",-1,-1,0,"sigesp_scb_r_listadochequeras.php","_self"]);
stm_aix("p1i4","p1i0",[0," Relaci�n Selectiva de Cheques ","","",-1,-1,0,"sigesp_scb_r_relacion_sel_chq.php","_self"]);
stm_aix("p1i4","p1i0",[0," Relaci�n Selectiva de Documentos(No incluye Cheques) ","","",-1,-1,0,"sigesp_scb_r_relacion_sel_docs.php","_self"]);
stm_ep();
stm_aix("p4i0","p1i0",[0," Cheques en Custodia  ","","",-1,-1,0,"sigesp_scb_r_chq_custodia_entregados.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Libro de Banco   ","","",-1,-1,0,"sigesp_scb_r_libro_banco.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p4i0","p1i0",[0," Comprobante Retenci�n Municipal   ","","",-1,-1,0,"sigesp_scb_r_comp_ret_mun.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//agregado el 25/08/08
stm_aix("p4i0","p1i0",[0," Listado Cheques Caducados   ","","",-1,-1,0,"sigesp_scb_r_chq_caducados.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);

stm_ep();

/*stm_aix("p4i0","p1i0",[0," Configuraci�n  ","","",-1,-1,0,"sigesp_scb_config.php","","","","","",6,0,0,"","",0,0,0,0,1,"#F7F7F7"]);
stm_bpx("p10","p1",[]);
stm_ep();*/

stm_aix("p0i3","p0i0",[0," Configuraci�n "]);
stm_bpx("p5","p1",[1,4,0,0,2,3,6,7]);
stm_aix("p4i0","p1i0",[0," Formato N� Orden de Pago    ","","",-1,-1,0,"sigesp_scb_config.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p0i0",[0," Medidas del Cheque Voucher ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edici�n - Opciones de Tercer Nivel
stm_aix("p1i4","p1i0",[0," Banco Provincial ","","",-1,-1,0,"sigesp_scb_p_conf_voucher_provincial.php","_self"]);
stm_aix("p1i4","p1i0",[0," Banco Industrial ","","",-1,-1,0,"sigesp_scb_p_conf_voucher_industrial.php","_self"]);
stm_aix("p1i4","p1i0",[0," Banco de Venezuela ","","",-1,-1,0,"sigesp_scb_p_conf_voucher_venezuela.php","_self"]);
stm_aix("p1i4","p1i0",[0," Banco Mercantil ","","",-1,-1,0,"sigesp_scb_p_conf_voucher_mercantil.php","_self"]);
stm_ep();
stm_aix("p1i2","p0i0",[0," Selecci�n Formato Carta Orden ","","",-1,-1,0,"sigesp_scb_p_conf_select_cartaorden.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p0i0",[0," Configuraci�n Formato Carta Orden ","","",-1,-1,0,"sigesp_scb_p_conf_cartaorden.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p1i2","p0i0",[0," Graficos ","","",-1,-1,0,"reporte.php","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_ep();
stm_ep();

stm_aix("p0i3","p0i0",[0," Mantenimiento "]);
stm_bpx("p5","p1",[1,4,0,0,2,3,6,7]);
stm_aix("p4i0","p1i0",[0," Movimientos Descuadrados    ","","",-1,-1,0,"sigesp_scb_p_mant_descuadrados.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p1i2","p0i0",[0," Graficos ","","",-1,-1,0,"reporte.php","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_ep();
stm_ep();

stm_aix("p4i0","p1i0",[0," Ir a M�dulos  ","","",-1,-1,0,"../escritorio.html","","","","","",6,0,0,"","",0,0,0,0,1,"#F7F7F7"]);
stm_bpx("p10","p1",[]);
stm_ep();

stm_em();
