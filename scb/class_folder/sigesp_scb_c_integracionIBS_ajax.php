<?php
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

	session_start();
	require_once ('sigesp_scb_c_integracionIBS.php');
	$io_integracionIBS = new sigesp_scb_c_integracionIBS();
	$ls_proceso = $_POST['proceso']; 
	switch($ls_proceso) {
		case "BUSGER":
			$ls_numche = $_POST['numche'];
			$coderr = $io_integracionIBS->validarNumeroChequeIBS($ls_numche);
			echo $io_integracionIBS->mensajeError($coderr);
			unset($io_integracionIBS);
			break;
	}
?>