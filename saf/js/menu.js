/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

stm_bm(["menu08dd",430,"","../shared/imagebank/blank.gif",0,"","",0,0,0,0,1000,1,0,0,"","100%",0],this);
stm_bp("p0",[0,4,0,0,1,3,0,0,100,"",-2,"",-2,90,0,0,"#000000","#e6e6e6","",3,0,0,"#000000"]);

stm_ai("p0i0",[0," Definiciones ","","",-1,-1,0,"","_self","","","","",0,0,0,"","",0,0,0,0,1,"#F7F7F7",0,"#f4f4f4",0,"","",3,3,0,0,"#fffff7","#000000","#909090","#909090","8pt 'Tahoma','Arial'","8pt 'Tahoma','Arial'",0,0]);
stm_bp("p1",[1,4,0,0,2,3,6,0,100,"progid:DXImageTransform.Microsoft.Fade(overlap=.5,enabled=0,Duration=0.10)",-2,"",-2,100,2,3,"#999999","#ffffff","",3,1,1,"#F7F7F7"]);
stm_aix("p1i0","p0i0",[0,"Método de Rotulación    ","","",-1,-1,0,"sigesp_saf_d_rotulacion.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p0i0",[0,"Condición del Activo    ","","",-1,-1,0,"sigesp_saf_d_condicion.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i4","p0i0",[0,"Causas de Movimiento    ","","",-1,-1,0,"sigesp_saf_d_movimientos.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i4","p0i0",[0,"Sedes    ","","",-1,-1,0,"sigesp_saf_d_sede.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i6","p0i0",[0,"Activos                 ","","",-1,-1,0,"sigesp_saf_d_activossigecof.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i8","p0i0",[0,"Catálogo SIGECOF        ","","",-1,-1,0,"sigesp_saf_d_catalogo.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i8","p0i0",[0,"Categoria CGR        ","","",-1,-1,0,"sigesp_saf_d_grupo.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i8","p0i0",[0,"Configuracion de Activos        ","","",-1,-1,0,"sigesp_saf_d_configuracion.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i8","p0i0",[0,"Unidad Física        ","","",-1,-1,0,"sigesp_saf_d_unidadfisica.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i8","p0i0",[0,"Estructuras Predominantes de los Inmuebles   ","","",-1,-1,0,"sigesp_saf_d_materiales.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();

// Menú Principal - Procesos
stm_aix("p0i3","p0i0",[0," Procesos "]);
stm_bpx("p5","p1",[1,4,0,0,2,3,6,7]);
stm_aix("p1i0","p1i0",[0,"Movimientos ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_aix("p1i0","p1i0",[0," Incorporaciones    		","","",-1,-1,0,"sigesp_saf_p_incorporaciones.php","_self"]);
stm_aix("p1i0","p1i0",[0," Desincorporaciones 		","","",-1,-1,0,"sigesp_saf_p_desincorporaciones.php","_self"]);
stm_aix("p1i0","p1i0",[0," Reasignaciones     		","","",-1,-1,0,"sigesp_saf_p_reasignaciones.php","_self"]);
stm_aix("p1i0","p1i0",[0," Modificaciones     	    ","","",-1,-1,0,"sigesp_saf_p_modificaciones.php","_self"]);
stm_aix("p1i0","p1i0",[0," Incorporaciones por Lote ","","",-1,-1,0,"sigesp_saf_p_incorporacioneslote.php","_self"]);
stm_aix("p1i0","p1i0",[0," Incorporaciones General  ","","",-1,-1,0,"sigesp_saf_p_incorporacioneslotegeneral.php","_self"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i0","p1i0",[0," Reverso de Incorporación ","","",-1,-1,0,"sigesp_saf_p_reverso_incorporacion.php","_self"]);
stm_ep();
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i4","p0i0",[0,"Cambio de Responsable","","",-1,-1,0,"sigesp_saf_p_cambioresponsable.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i4","p0i0",[0,"Entrega de Unidad","","",-1,-1,0,"sigesp_saf_p_entregaunidad.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i6","p1i0",[0,"Depreciación de Activos ","","",-1,-1,0,"sigesp_saf_p_depreciacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i6","p1i0",[0,"Acta de Préstamos       ","","",-1,-1,0,"sigesp_saf_p_actaprestamo.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i6","p1i0",[0,"Autorización de Salida  ","","",-1,-1,0,"sigesp_saf_p_autorizacionsalida.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i6","p1i0",[0,"Entrega de Activos      ","","",-1,-1,0,"sigesp_saf_p_entregas.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i6","p1i0",[0,"Retorno de Activos      ","","",-1,-1,0,"sigesp_saf_p_retorno_activos.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();

// Menú Principal - Reportes
stm_aix("p0i4","p0i0",[0," Reportes "]);
stm_bpx("p6","p1",[1,4,0,0,2,3,6,7]);
stm_aix("p1i0","p1i0",[0,"SIGECOF ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_aix("p1i0","p1i0",[0,"Activos                      ","","",-1,-1,0,"sigesp_saf_r_activo.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p1i0",[0,"Incorporación                ","","",-1,-1,0,"sigesp_saf_r_incorporacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p1i0",[0,"Desincorporación             ","","",-1,-1,0,"sigesp_saf_r_desincorporacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p1i0",[0,"Reasignación                 ","","",-1,-1,0,"sigesp_saf_r_reasignacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p1i0",[0,"Modificación                 ","","",-1,-1,0,"sigesp_saf_r_modificacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i0","p1i0",[0,"Depreciación                 ","","",-1,-1,0,"sigesp_saf_r_depreciacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p1i0",[0,"Depreciación Mensual         ","","",-1,-1,0,"sigesp_saf_r_depmensual.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i0","p1i0",[0,"Listado Catálogo SIGECOF     ","","",-1,-1,0,"sigesp_saf_r_sigecof.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i0","p1i0",[0,"Comprobante de Incorporación ","","",-1,-1,0,"sigesp_saf_r_compincorporacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p1i0",[0,"Comprobante de Desincorporación ","","",-1,-1,0,"sigesp_saf_r_compdesincorporacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p1i0",[0,"Comprobante de Reasignación  ","","",-1,-1,0,"sigesp_saf_r_compreasignacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();
stm_aix("p1i0","p1i0",[0,"CGR ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
stm_aix("p1i0","p1i0",[0," Inventario de Bienes Muebles BM-1    ","","",-1,-1,0,"sigesp_saf_r_activo_bien.php","_self"]);
stm_aix("p1i0","p1i0",[0," Relación del Movimiento de Bienes Muebles BM-2    ","","",-1,-1,0,"sigesp_saf_r_relmovbm2.php","_self"]);
stm_aix("p1i0","p1i0",[0," Relación de Bienes Muebles Faltantes BM-3    ","","",-1,-1,0,"sigesp_saf_r_relbmf3.php","_self"]);
stm_aix("p1i0","p1i0",[0," Resumen de la Cuenta de Bienes Muebles BM-4    ","","",-1,-1,0,"sigesp_saf_r_resctabm4.php","_self"]);
stm_aix("p1i0","p1i0",[0," Inventario General de Bienes    ","","",-1,-1,0,"sigesp_saf_r_invgenbie.php","_self"]);
stm_aix("p1i0","p1i0",[0," Resumen de Bienes Muebles por Grupo    ","","",-1,-1,0,"sigesp_saf_r_resbiegru.php","_self"]);
stm_aix("p1i0","p1i0",[0," Incorporaciones y Desincorporación por Departamento    ","","",-1,-1,0,"sigesp_saf_r_incdesinc.php","_self"]);
stm_aix("p1i0","p1i0",[0," Bienes por Cuenta Contable   ","","",-1,-1,0,"sigesp_saf_r_biemuectacont.php","_self"]);
stm_aix("p1i0","p1i0",[0," Rendición Mensual de Cuenta    ","","",-1,-1,0,"sigesp_saf_r_rendmen.php","_self"]);
stm_aix("p1i0","p1i0",[0," Tipos de Adquisición de Bienes    ","","",-1,-1,0,"sigesp_saf_r_tipos_bien.php","_self"]);
stm_aix("p1i0","p1i0",[0," Adquisición de Bienes General    ","","",-1,-1,0,"sigesp_saf_r_bien_general.php","_self"]);
stm_aix("p1i0","p1i0",[0," Inventario de Bienes por Unidad Organizativa    ","","",-1,-1,0,"sigesp_saf_r_bien_uniadm.php","_self"]);
stm_aix("p1i0","p1i0",[0," Inventario de Bienes Inmuebles    ","","",-1,-1,0,"sigesp_saf_r_inventario_bienes.php","_self"]);
stm_aix("p1i0","p1i0",[0," Etiqueta    ","","",-1,-1,0,"sigesp_saf_r_etiqueta.php","_self"]);
stm_ep();
stm_ep();

// Menú Principal - Ir a Módulo
stm_aix("p0i5","p0i0",[0," Ir a Módulos  ","","",-1,-1,0,"../escritorio.html","","","","","",6,0,0,"","",0,0,0,0,1,"#F7F7F7"]);
stm_ep();
stm_ep();

stm_ep();
stm_em();
function ue_abrir(ventana)
{
	window.open(ventana,"catalogo","menubar=no,toolbar=no,scrollbars=no,resizable=no,width=400,height=230,left=150,top=150,location=no,resizable=yes");
}

function ue_abrir_usuario(sistema)
{
	window.open("sigesp_c_seleccionar_usuario.php?sist="+sistema,"catalogo","menubar=no,toolbar=no,scrollbars=no,resizable=no,width=400,height=230,left=150,top=150,location=no,resizable=yes");
}

function ue_actulizar_ventana()
{
	window.open("sigesp_c_Actualizar_ventanas.php","catalogo","menubar=no,toolbar=no,scrollbars=no,resizable=no,width=400,height=230,left=150,top=150,location=no,resizable=yes");
}