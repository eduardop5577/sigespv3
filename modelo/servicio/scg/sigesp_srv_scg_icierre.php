<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

interface ICierreSCG
{
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que verifica si se ha ejecutado el cierre, en caso afirmativo retorna los datos del comprobante
	 * @return Resulset Adodb con los datos del comprobante.
	 */
	public function verificarCierre();
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que busca los detalles del comprobante de cierre
	 * @param string $codemp - codigo de empresa
	 * @param string $procede - procedencia del comprobante
	 * @param string $comprobante - numero del comprobante
	 * @param string $fecha - fecha del comprobante
	 * @return Resulset Adodb con los datos de los detalles.
	 */
	public function cargarDetalleComprobante($codemp,$procede,$comprobante,$fecha);

	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que guardar el comprobante contable de cierre
	 * @param string $codemp - codigo de empresa
	 * @param Json $objson - json con los datos de la interfaz
	 * @param Array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function guardarCierreEjercicio($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que elimina el comprobante contable de cierre
	 * @param string $codemp - codigo de empresa
	 * @param Json $objson - json con los datos de la interfaz
	 * @param Array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function eliminarCierreEjercicio($codemp,$objson,$arrevento);
	
}