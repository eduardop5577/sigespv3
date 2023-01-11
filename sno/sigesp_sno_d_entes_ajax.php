<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/
 
session_start();
$ruta = '../';
include("sigesp_sno_c_entes.php");
$io_ente = new sigesp_sno_c_entes();
$criterio = $_POST['criterio'];
$io_ente->io_conexiones->codificacion_navegador();

switch($criterio){
						  
					  case "guardar":
									$mensajex = $io_ente->uf_insertar_ente($_POST['txtcod'],$_POST['txtente'],$_POST['txtporc']);
									//$mensajex = 'El ente ha sido insertado con exito';
									echo '<input type="hidden" name="txt_msjajax_sigesp" id="txt_msjajax_sigesp" value="'.$mensajex['mensaje'].'">';
									if($_POST['txtcod']){
										echo '<input type="hidden" name="id_insertado" id="id_insertado" value="'.$_POST['txtcod'].'">';
										echo '<input type="hidden" name="txt_ejecutar_funcion" id="txt_ejecutar_funcion" value="cargar_id">';
									}
							break;
									 
					   case "modificar":
									$resultado = $io_ente->uf_modifica_ente($_POST['txtcod'],$_POST['txtente'],$_POST['txtporc']);
									//$mensajex = 'El ente ha sido modificado con exito';
									echo '<input type="hidden" name="txt_msjajax_sigesp" id="txt_msjajax_sigesp" value="'.$resultado['mensaje'].'">';
							break;
					  
					  case "eliminar":
									$resultado = $io_ente->uf_eliminar_ente($_POST['txtcod']);
									echo '<input type="hidden" name="txt_msjajax_sigesp" id="txt_msjajax_sigesp" value="'.$resultado['mensaje'].'">';
									if($_POST['txtcod']){
										echo '<input type="hidden" name="id_insertado" id="id_insertado" value="'.$_POST['txtcod'].'">';
										echo '<input type="hidden" name="txt_ejecutar_funcion" id="txt_ejecutar_funcion" value="nuevo">';
									}
							break;
						
				}


?>