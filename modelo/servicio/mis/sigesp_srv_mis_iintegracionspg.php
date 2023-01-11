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

interface IIntegracionSPG {
	 
	 /**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca los Modificaciones Presupuestarias por Aprobar o Aprobadas.
	 * @param string     $numcom  - Nro de Comprobante de la Modificacion 
	 * @param string     $procede - Codigo de la procedencia del Comprobante
	 * @param date       $fecha   - Fecha del Comprobante de la Modificacion
	 * @param string     $estapro - Estatus que indica si la modificacion esta o no aprobada
	 *                              (0 por aprobar, 1 aprobada)
	 * @return Resulset Adodb con los Modificiaciones Presupuestarias segun los parametros
	 *         indicados.
	 */
	public function buscarModificaciones($numcom, $procede, $fecha, $estapro);
	
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca el detalle presupuestario de la Modificacion.
	 * @param string     $numcom  - Nro de Comprobante de la Modificacion 
	 * @param string     $procede - Codigo de la procedencia del Comprobante
	 * @return Resulset Adodb con los datos del detalle presupuestario.
	 */
	public function buscarDetallePresupuesto($numcom, $procede);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca la disponibilidad para cada una de las cuentas del detalle presupuestario.
	 * @param string     $numcom  - Nro de Comprobante de la Modificacion 
	 * @param string     $procede - Codigo de la procedencia del Comprobante
	 * @return array     $arrDisponible - Arreglo con el detalle presupuestario y la disponibilidad.
	 */
	public function obtenerDetallePresupuestoDisponibilidad($numcom, $procede);
	
	 	
}
?>