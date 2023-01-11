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

interface IIntegracionSRM {
	 
	 /**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca los Movimiento generados por el modulo de rentas municipales.
	 * @param string     $numcom  - Nro de Comprobante de rentas 
	 * @param date       $fecha   - Fecha del Comprobante de rentas
	 * @param string     $estatus - Estatus que indica si el documento de rentas fue o no procesado
	 *                              (0 por procesar, 1 procesada)
	 * @return Resulset Adodb con los documentos de rentas segun los parametros
	 *         indicados.
	 */
	public function buscarCobranzas($numcom, $fecha, $estatus );
	
}
?>