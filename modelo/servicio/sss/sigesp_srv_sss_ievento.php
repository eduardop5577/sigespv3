<?php
/***********************************************************************************
* @fecha de modificacion: 01/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

interface IEvento
{
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc   Metodo que inserta un evento en los registros de transacciones
	 * @param  string  $evento - Evento que se va a incluir
	 * @param  string  $tipoevento - Tipo de transaccion si es de Exito ó error
	 * @return boolean $valido - si se registro el evento de manera exitosa
	 */
	public function incluirEvento();	
}
?>