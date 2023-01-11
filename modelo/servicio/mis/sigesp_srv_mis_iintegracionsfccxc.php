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

interface IIntegracionSFCCXC {
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que busca las cuentas por cobrar para integrar
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @param  string    $estatus - estatus del documento
	 * @return resultset $data - objeto de datos con las cuentas por cobrar a integrar 
	 */
	public function buscarCuentasCobrarIntegrar($comprobante, $procede, $fecha, $estatus);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que busca el detalle presupuestario de ingreso de una cuenta a cobrar
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @return resultset $data - objeto de datos con el detalle presupuestario de ingreso de una cuenta a cobrar 
	 */
	public function buscarDetalleComprobanteCXCSPI($comprobante, $procede, $fecha);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc  Metodo que obtiene los detalles contables de una cuenta a cobrar
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento 
	 * @return resultset Resulset con la informacion del detalle contable de la cuenta a cobrar 
	 */
	public function buscarDetalleComprobanteCXCSCG($comprobante, $procede, $fecha);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para obtener los detalles contables de una cuenta por cobrar
	 * @param  array $arrCabecera - Arreglo con los datos de la cabecera del comprobante
	 * @return array $arrDetalleSCG - Arreglo con los detalles contables del comprobante
	 */
	public function obtenerDetalleCuentaCobrarSCG($comprobante,$arrCabecera);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para obtener los detalles presupuestarios de ingresos de una cuenta por cobrar
	 * @param  array $arrCabecera - Arreglo con los datos de la cabecera del comprobante
	 * @return array $arrDetalleSPI - Arreglo con los detalles de ingreso del comprobante
	 */
	public function obtenerDetalleCuentaCobrarSPI($comprobante,$arrCabecera);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para actualizar el estatus de los movimiento de una cuenta por cobrar
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @param  string    $estatus - estatus del documento
	 * @param  string    $fechaConta - fecha de contabilizacion del documento
	 */
	public function actualizarEstatusCXC($comprobante, $procede, $fecha, $estatus, $fechaConta);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que procesa la contabilizacion de una Cuenta por cobrar
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @param  string    $descripcion - descripcion del documento
	 * @param  arreglo $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean boolean $resultado - Variable indicando si se pudo ó no contabilizar la cuenta por cobrar  
	 */
	public function contabilizarCXC($comprobante, $fecha, $procede, $descripcion, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa la contabilizacion de un lote de cuentas a cobrar
	 * @param array $arrJson - Arreglo tipo json que contiene la informacion de las cuentas a cobrar por procesar  
	 * @return string $resultado - string con los resultados de la operacion. 
	 */
	public function procesoContabilizarCXC($arrJson);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que procesa el reverso de la contabilizacion de una cuenta a cobrar
	 * @param  @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @param  string    $descripcion - descripcion del documento
	 * @param  arreglo $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean boolean $resultado - Variable indicando si se pudo ó no reversar la cuenta a cobrar  
	 */
	public function revContabilizarCXC($comprobante, $procede, $fecha, $descripcion, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa el reverso de la contabilizacion de un lote de cuentas a cobrar
	 * @param array $arrJson - Arreglo tipo json que contiene la informacion de las de cuentas a cobrar a procesar  
	 * @return string $resultado - string con los resultados de la operacion.
	 */ 
	public function procesoRevContabilizarCXC($arrJson);
}
?>