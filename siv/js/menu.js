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

stm_bm(["menu08dd",430,"","../shared/imagebank/blank.gif",0,"","",0,0,0,0,1000,1,0,0,"","100%",0],this);
stm_bp("p0",[0,4,0,0,1,3,0,0,100,"",-2,"",-2,90,0,0,"#000000","#e6e6e6","",3,0,0,"#000000"]);

// Menú Principal- Archivo
stm_ai("p0i0",[0," Definiciones ","","",-1,-1,0,"","_self","","","","",0,0,0,"","",0,0,0,0,1,"#F7F7F7",0,"#f4f4f4",0,"","",3,3,0,0,"#fffff7","#000000","#909090","#909090","8pt 'Tahoma','Arial'","8pt 'Tahoma','Arial'",0,0]);
stm_bp("p1",[1,4,0,0,2,3,6,0,100,"progid:DXImageTransform.Microsoft.Fade(overlap=.5,enabled=0,Duration=0.10)",-2,"",-2,100,2,3,"#999999","#ffffff","",3,1,1,"#F7F7F7"]);

// Archivo - Opciones de Segundo Nivel
stm_aix("p1i0","p0i0",[0,"Configuración de Inventario        ","","",-1,-1,0,"sigesp_siv_d_configuracion.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"Tipo de Artículo        ","","",-1,-1,0,"sigesp_siv_d_tipoarticulo.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p0i0",[0,"Unidad de Medida    ","","",-1,-1,0,"sigesp_siv_d_unidadmedida.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i6","p0i0",[0,"Almacén                 ","","",-1,-1,0,"sigesp_siv_d_almacen.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i8","p0i0",[0,"Definición de Segmento  ","","",-1,-1,0,"sigesp_siv_d_segmento.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i8","p0i0",[0,"Artículo            ","","",-1,-1,0,"sigesp_siv_d_articulo.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i8","p0i0",[0,"Causas de Movimiento            ","","",-1,-1,0,"sigesp_siv_d_causas.php","","","","","",0,0,0,"","",0,0,0,0,1,"#ffffff"]);

stm_ep();


//stm_aix("p1i0","p0i0",[0,"Nuevo    ","","",-1,-1,0,"javascript:ue_nuevo()","","","","imagebank/tools20/nuevo.gif","imagebank/tools20/nuevo-off.gif",20,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p1i2","p1i0",[0,"Guardar    ","","",-1,-1,0,"javascript:ue_guardar()","","","","imagebank/tools20/grabar.gif","imagebank/tools20/grabar-off.gif",20,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p1i4","p1i0",[0,"Eliminar   ","","",-1,-1,0,"javascript:ue_eliminar()","","","","imagebank/tools20/eliminar.gif","imagebank/tools20/eliminar-off.gif",20,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p1i4","p1i0",[0,"Buscar   ","","",-1,-1,0,"javascript:ue_buscar()","","","","imagebank/tools20/buscar.gif","imagebank/tools20/buscar-off.gif",20,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p1i4","p1i0",[0,"Cerrar   ","","",-1,-1,0,"sigespwindow_blank.php","","","","imagebank/tools20/salir.gif","imagebank/tools20/salir-off.gif",20,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p1i4","p1i0",[0,"Salir   ","","",-1,-1,0,"javascript:close();","","","","imagebank/tools20/salir.png","imagebank/tools20/salir.png",20,0,0,"","",0,0,0,0,1,"#ffffff"]);

// Menú Principal - Edición
//stm_aix("p0i1","p0i0",[0," Edición "]);
//stm_bpx("p2","p1",[1,4,0,0,2,3,6,7]);
// Edición - Opciones de Segundo Nivel
//stm_aix("p2i0","p1i0",[0,"Edición 1    ","","",-1,-1,0,"","_self","","","","",6,0,0,"imagebank/arrow.gif","imagebank/arrow.gif",7,7]);
//stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edición - Opciones de Tercer Nivel
//stm_aix("p3i0","p1i0",[0,"  Menu Item 1  ","","",-1,-1,0,"","_self","","","","",0]);
//stm_aix("p3i1","p3i0",[0,"  Menu Item 2  "]);
//stm_aix("p3i2","p3i0",[0,"  Menu Item 3  "]);
//stm_ep();
// Edición - Opciones de Segundo Nivel (continuación)
//stm_aix("p2i2","p1i0",[0,"Edición 2    ","","",-1,-1,0,"http://www.google.com/"]);
//stm_aix("p2i4","p1i0",[0,"Edición 3    ","","",-1,-1,0,"http://www.google.com"]);
//stm_ep();

// Menú Principal - Definiciones
//stm_aix("p0i2","p0i0",[0," Definiciones "]);
//stm_bpx("p4","p1",[1,4,0,0,2,3,6,7]);
//stm_aix("p4i0","p1i0",[0,"Eventos   ","","",-1,-1,0,"sigespwindow_sss_eventos.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p4i0","p1i0",[0,"Tipo de Artículo         ","","",-1,-1,0,"sigesp_siv_d_tipoarticulo.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p4i0","p1i0",[0,"Unidad de Medida         ","","",-1,-1,0,"sigesp_siv_d_unidadmedida.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
//stm_aix("p4i0","p1i0",[0,"Almacén         ","","",-1,-1,0,"sigesp_siv_d_almacen.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p4i0","p1i0",[0,"Artículo         ","","",-1,-1,0,"sigesp_siv_d_articulo.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p4i0","p1i0",[0,"Catálogos de Grupos, Subgrupos y Secciones ","","",-1,-1,0,"sigesp_saf_d_grupo.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p4i0","p1i0",[0,"Catálogo SIGECOF        ","","",-1,-1,0,"sigesp_saf_d_catalogo.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p4i0","p1i0",[0,"Ventanas         ","","",-1,-1,0,"abrir.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p4i0","p1i0",[0,"Cambio de Password         ","","",-1,-1,0,"javascript:ue_abrir();","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_ep();

// Menú Principal - Procesos
stm_aix("p0i3","p0i0",[0," Procesos "]);
stm_bpx("p5","p1",[1,4,0,0,2,3,6,7]);
stm_aix("p1i0","p0i0",[0,"Entrada de Suministros a Almacén ","","",-1,-1,0,"sigesp_siv_p_recepcion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"Entrada de Materiales por Lote ","","",-1,-1,0,"sigesp_siv_p_recepcion_lote.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p0i0",[0,"Aprobación de Entrada de Suministros        ","","",-1,-1,0,"sigesp_siv_p_aprobacionrecepcion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i4","p1i0",[0,"Transferencia entre Almacenes    ","","",-1,-1,0,"sigesp_siv_p_transferencia.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i4","p1i0",[0,"Transferencia por Lote    ","","",-1,-1,0,"sigesp_siv_p_transferencia_lote.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i6","p1i0",[0,"Despacho                         ","","",-1,-1,0,"sigesp_siv_p_despacho.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i6","p1i0",[0,"Conversion de Articulos             ","","",-1,-1,0,"sigesp_siv_p_produccion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i6","p1i0",[0,"Empaquetado de Productos             ","","",-1,-1,0,"sigesp_siv_p_empaquetado.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i6","p1i0",[0,"Aprobacion de Empaq. de Productos             ","","",-1,-1,0,"sigesp_siv_p_aprobacionempaquetado.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i6","p1i0",[0,"Movimientos de Materiales             ","","",-1,-1,0,"sigesp_siv_p_asignacion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i0","p0i0",[0,"Cierre de Órdenes de Compra ","","",-1,-1,0,"sigesp_siv_p_cerraroc.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"Cierre de Solicitudes de Ejecución Presupuestaria ","","",-1,-1,0,"sigesp_siv_p_cerrarsep.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p0i0",[0,"Toma de Inventario ","","",-1,-1,0,"sigesp_siv_p_toma.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i8","p1i0",[0,"Reverso de Entrada de Suministros a Almacén","","",-1,-1,0,"sigesp_siv_p_revrecepcion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i8","p1i0",[0,"Reverso de Despacho","","",-1,-1,0,"sigesp_siv_p_revdespacho.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i8","p1i0",[0,"Reverso de Transferencia","","",-1,-1,0,"sigesp_siv_p_revtransferencia.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p1i4","p1i0",[0,"Programación de reportes OAF ","","",-1,-1,0,"sigesp_scg_wproc_progrep.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
//stm_aix("p1i5","p1i0",[0,"Programación de reportes  ","","",-1,-1,0,"sigesp_scg_wproc_progoaf.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_ep();

// Menú Principal - Reportes
stm_aix("p0i4","p0i0",[0," Reportes "]);
stm_bpx("p6","p1",[1,4,0,0,2,3,6,7]);
stm_aix("p1i0","p1i0",[0,"Existencia de Artículos            ","","",-1,-1,0,"sigesp_siv_r_articuloxalmacen.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p1i0",[0,"Existencia de Artículos Mensuales  ","","",-1,-1,0,"sigesp_siv_r_articuloxalmacen_mensual.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i0","p1i0",[0,"Listado de Artículos Detallados    ","","",-1,-1,0,"sigesp_siv_r_articulosespecificos.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p1i0",[0,"Movimientos                        ","","",-1,-1,0,"sigesp_siv_r_movimientos.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i2","p1i0",[0,"Movimientos de Artículos Detallados  ","","",-1,-1,0,"sigesp_siv_r_movimientos_articulos_det.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i4","p1i0",[0,"Artículos por Solicitar            ","","",-1,-1,0,"sigesp_siv_r_articulosxsolicitar.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i4","p1i0",[0,"Artículos por Fecha de Vencimiento ","","",-1,-1,0,"sigesp_siv_r_articulosxvencer.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i4","p1i0",[0,"Listado de Artículos Por Almacen   ","","",-1,-1,0,"sigesp_siv_r_listadoarticulos.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i6","p1i0",[0,"Ordenes de Despacho                ","","",-1,-1,0,"sigesp_siv_r_despachos.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i8","p1i0",[0,"Entradas de Suministros a Almacén  ","","",-1,-1,0,"sigesp_siv_r_recepcion.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i10","p1i0",[0,"Transferencias entre Almacenes    ","","",-1,-1,0,"sigesp_siv_r_transferencia.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i12","p1i0",[0,"Resumen de Inventario             ","","",-1,-1,0,"sigesp_siv_r_inventario.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i14","p1i0",[0,"Listado de Almacenes              ","","",-1,-1,0,"sigesp_siv_r_almacenes.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i16","p1i0",[0,"Valoración de Inventario          ","","",-1,-1,0,"sigesp_siv_r_valinventario.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i16","p1i0",[0,"Cierre de Ordenes de Compra       ","","",-1,-1,0,"sigesp_siv_r_cierre.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i16","p1i0",[0,"Valoración de Toma de Inventario  ","","",-1,-1,0,"sigesp_siv_r_valtoma.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i16","p1i0",[0,"Articulos Despachados ","","",-1,-1,0,"sigesp_siv_r_articulos_despachados.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i16","p1i0",[0,"Valoración de Ajuste de Inventario  ","","",-1,-1,0,"sigesp_siv_r_valajustes.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i16","p1i0",[0,"Acta de Recepcion de Bienes ","","",-1,-1,0,"sigesp_siv_r_acta_recepcion_bienes.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i16","p1i0",[0,"Cierre de Solicitudes de Ejecucion Presupuestaria ","","",-1,-1,0,"sigesp_siv_r_cierresep.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
<!--stm_aix("p1i16","p1i0",[0,"Acta de Recepcion de Bienes ","","",-1,-1,0,"sigesp_siv_r_acta_recepcion_bienes.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
-->
stm_aix("p1i16","p1i0",[0,"Listado Imputación Presupuestaria del Inventario","","",-1,-1,0,"sigesp_siv_r_imputacionpresupuestaria.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
stm_aix("p1i16","p1i0",[0,"Despacho de Articulos ","","",-1,-1,0,"sigesp_siv_r_despachoarticulos.php","","","","","",6,0,0,"","",0,0,0,0,1,"#ffffff"]);
/*stm_aix("p6i1","p1i0",[0,"Reportes 2    "]);
stm_aix("p6i2","p1i0",[0,"Reportes 3    ","","",-1,-1,0,"","_self","","","","",6,0,0,"imagebank/arrow.gif","imagebank/arrow.gif",7,7]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edición - Opciones de Tercer Nivel
stm_aix("p3i0","p1i0",[0,"  Menu Item 1  ","","",-1,-1,0,"","_self","","","","",0]);
stm_aix("p3i1","p3i0",[0,"  Menu Item 2  ","","",-1,-1,0,"http://www.google.com/"]);
stm_aix("p3i2","p3i0",[0,"  Menu Item 3  "]);
stm_aix("p3i3","p3i0",[0,"  Menu Item 4  "]);
stm_aix("p3i4","p3i0",[0,"  Menu Item 5  "]);
stm_ep();
stm_aix("p6i3","p1i0",[0,"Reportes 4    "]);
stm_aix("p6i4","p1i0",[0,"Reportes 5    "]);*/
stm_ep();


// Menú Principal - Ir a Módulo
stm_aix("p4i0","p1i0",[0," Ir a Módulos  ","","",-1,-1,0,"../escritorio.html","","","","","",6,0,0,"","",0,0,0,0,1,"#F7F7F7"]);
stm_bpx("p10","p1",[]);
stm_ep();

/*stm_aix("p8i2","p1i0",[0,"Ventana 3    "]);
stm_aix("p8i3","p1i0",[0,"Ventana 4    "]);
stm_aix("p8i4","p1i0",[0,"Ventana 5    "]);*/
//stm_ep();

// Menú Principal - Exploración
//stm_aix("p0i7","p0i0",[0," Exploración "]);
//stm_bpx("p9","p1",[]);
/*stm_aix("p9i0","p1i0",[0,"Exploración 1    "]);
stm_aix("p9i1","p1i0",[0,"Exploración 1    "]);
stm_aix("p9i2","p1i0",[0,"Exploración 1    "]);
stm_aix("p9i3","p1i0",[0,"Exploración 1    "]);
stm_aix("p9i4","p1i0",[0,"Exploración 1    "]);*/
//stm_ep();

// Menú Principal - Ayuda
//stm_aix("p0i8","p0i0",[0," Ayuda "]);
//stm_bpx("p10","p1",[]);
/*stm_aix("p10i0","p1i0",[0,"Ayuda 1    "]);
stm_aix("p10i1","p1i0",[0,"Ayuda 2    "]);
stm_aix("p10i2","p1i0",[0,"Ayuda 3    "]);
stm_aix("p10i3","p1i0",[0,"Ayuda 4    "]);
stm_aix("p10i4","p1i0",[0,"Ayuda 5    "]);
stm_ep();*/
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
