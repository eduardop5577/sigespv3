<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

ini_set('memory_limit','1024M');
ini_set('max_execution_time ','0');
print("<script language=JavaScript>");
print "window.open('sigesp_spg_rpp_sol_mod_pre_forma0301_abae01.php?comprobante={$_GET['comprobante']}&procede={$_GET['procede']}&fecha={$_GET['fecha']}','catalogo2','menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes');";
print "window.open('sigesp_spg_rpp_sol_mod_pre_forma03012.php?comprobante={$_GET['comprobante']}&procede={$_GET['procede']}&fecha={$_GET['fecha']}','catalogo3','menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes');";
print "window.open('sigesp_spg_rpp_sol_mod_pre_forma03013.php?comprobante={$_GET['comprobante']}&procede={$_GET['procede']}&fecha={$_GET['fecha']}','catalogo4','menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes');";
print "window.close()";
print("</script>");
?>