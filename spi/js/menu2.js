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

// Menú Principal - Procesos
stm_aix("p0i3","p0i0",[0," Procesos "]);
stm_bpx("p5","p1",[1,4,0,0,2,3,6,7]);
stm_aix("p1i0","p0i0",[0,"Apertura ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edición - Opciones de Tercer Nivel
stm_aix("p3i0","p1i0",[0," Mensual ","","",-1,-1,0,"sigesp_spg_p_apertura.php","_self"]);
stm_aix("p3i0","p1i0",[0," Trimestral ","","",-1,-1,0,"sigesp_spg_p_apertura_trim.php","_self"]);
stm_ep();
stm_aix("p1i2","p1i0",[0,"Comprobantes ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edición - Opciones de Tercer Nivel
stm_aix("p3i0","p1i0",[0,"  Ejecución Financiera  ","","",-1,-1,0,"sigesp_spg_p_comprobante.php","_self"]);
stm_ep();
stm_aix("p1i4","p1i0",[0,"Modificaciones Presupuestarias ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edición - Opciones de Tercer Nivel
stm_aix("p3i0","p1i0",[0,"  Rectificaciones  ","","",-1,-1,0,"sigesp_spg_p_rectificaciones.php","_self"]);
stm_aix("p3i0","p1i0",[0,"  Insubsistencias ","","",-1,-1,0,"sigesp_spg_p_insubsistencias.php","_self"]);
stm_aix("p3i0","p1i0",[0,"  Traspasos  ","","",-1,-1,0,"sigesp_spg_p_traspaso.php","_self"]);
stm_aix("p3i0","p1i0",[0,"  Credito/Ingreso Adicional  ","","",-1,-1,0,"sigesp_spg_p_adicional.php","_self"]);
stm_ep();
stm_ai("p1i1",[6,1,"#e6e6e6","",0,0,0]);
stm_aix("p1i5","p1i0",[0,"Programación de reportes  ","","",-1,-1,0,"","","","","","",6,0,0,"../shared/imagebank/arrow.gif","../shared/imagebank/arrow.gif",0,0,0,0,1,"#ffffff"]);
stm_bpx("p3","p1",[1,2,0,0,2,3,0]);
// Edición - Opciones de Tercer Nivel
stm_aix("p3i0","p1i0",[0," Mensual ","","",-1,-1,0,"sigesp_spg_p_progrep.php","_self"]);
stm_aix("p3i0","p1i0",[0," Trimestral ","","",-1,-1,0,"sigesp_spg_p_progrep_trim.php","_self"]);
stm_ep();
stm_ep();

stm_aix("p4i0","p1i0",[0," Ir a Módulos  ","","",-1,-1,0,"../escritorio.html","","","","","",6,0,0,"","",0,0,0,0,1,"#F7F7F7"]);
stm_bpx("p10","p1",[]);
stm_ep();
stm_em();