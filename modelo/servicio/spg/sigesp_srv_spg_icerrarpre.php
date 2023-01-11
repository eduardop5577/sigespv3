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

interface ICierrePresupuestarioGasto 
{
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Método que genera el cierre/reverso de presupuesto de gasto.
	 * @param string $codemp - codigo de empresa.
	 * @param Json $objson - json con los datos de la interfaz
	 * @param Array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function procesarCierrePresupuestario($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Método que retorna el valor del estatus de cierre presupuestario.
	 * @param string $codemp - codigo de la empresa.
	 * @return integer $resultado - numero que indica el valor del estatus de cierre presupuestario 1 y 0
	 */
	public function buscarEstCiePreGas($codemp);

}