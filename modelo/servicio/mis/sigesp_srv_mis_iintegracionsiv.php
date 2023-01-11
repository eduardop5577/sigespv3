<?php
/***********************************************************************************
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

interface IIntegracionSIV {
	 
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca los Despachos por Contabilizar o Contabilizados.
	 * @param string     $numorddes  - Nro de despacho 
	 * @param string     $fecdes     - Fecha de despacho
	 * @param string     $estint     - Estatus que indica si el despacho esta o no contabilizado
	 * @return Resulset Adodb con los despachos segun los parametros indicados.
	 */
	public function buscarDespachos($numorddes, $fecdes, $estint);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca el detalle presupuestario de gasto del despacho
	 * @param string     $numorddes  - Nro de Despacho 
	 * @return Resulset Adodb con los datos del detalle presupuestario.
	 */
	public function buscarDetallePresupuestoDespacho($numorddes);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca la disponibilidad para cada una de las cuentas del detalle presupuestario.
	 * @param string     $numorddes  - Nro de Despacho 
	 * @return array     $arrDisponible - Arreglo con el detalle presupuestario y la disponibilidad.
	 */
	public function obtenerDetPreDespachoDisponibilidad($numorddes);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca el detalle contable del despacho
	 * @param string     $numorddes  - Nro de Despacho 
	 * @return Resulset Adodb con los datos del detalle contable del despacho.
	 */
	public function buscarDetalleContableDespacho($numorddes);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que verifica si la bd trabaja con contabilizacion de despachos
	 * @return true si la bd esta configurada para contabilizar despachos.
	 */
	public function validarConfiguracion($codsis,$seccion,$entry);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que contabiliza los despachos 
	 * @param Json     $objson  - json con los datos de la interfaz 
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 sino valor 0
	 */
	public function procesoContabilizarDespachos($objson);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que reversa la contabilizacion de los despachos 
	 * @param Json     $objson  - json con los datos de la interfaz 
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 sino valor 0
	 */
	public function procesorRevContabilizarDespachos($objson);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca las Transferencias por Contabilizar o Contabilizados.
	 * @param string     $numtra  - Nro de Transferencia
	 * @param string     $fecemi  - Fecha de emision de transferencia
	 * @param string     $estint  - Estatus que indica si la transferencia esta o no contabilizada
	 * @return Resulset Adodb con las transferencias segun los parametros indicados.
	 */
	public function buscarTransferencias($numtra, $fecemi, $estint);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca el detalle contable de la transferencia 
	 * @param string     $numorddes  - Nro de Tranferencia
	 * @param string     $fecemi     - Fecha de emision de transferencia 
	 * @return Resulset Adodb con los datos del detalle contable del despacho.
	 */
	public function buscarDetalleContableTransferencia($numtra, $fecemi);	

	public function buscarProduccion($numpro, $fecemi, $estint);
	
	public function buscarDetalleContableProduccion($numpro, $fecemi);
	
	public function procesoContabilizarProduccion($objson);
	
	public function contabilizarProduccion($objson,$arrevento,$j);
	
	public function cargarArregloDetConPro($comprobante,$fecha,$arrcabecera);
	
	public function actualizarFechaEstatuProduccion($comprobante,$estatus,$fecemision,$fechaconta,$fechaanula);
	
	public function procesoRevContabilizarProduccion($objson);
	
	public function reversarProduccion($objson,$arrevento,$j);

	public function buscarEmpaquetado($codemppro, $fecemppro, $estint);

	public function buscarDetalleContableEmpaquetado($codemppro, $fecemppro);

	public function procesoContabilizarEmpaquetado($objson);
	
	public function contabilizarEmpaquetado($objson,$arrevento,$j);
	
	public function cargarArregloDetConEmp($comprobante,$fecha,$arrcabecera);
	
	public function actualizarFechaEstatusEmpaquetado($comprobante,$estatus,$fecemision,$fechaconta,$fechaanula);

	public function procesoRevContabilizarEmpaquetado($objson);
	
	public function reversarEmpaquetado($objson,$arrevento,$j);
}
?>