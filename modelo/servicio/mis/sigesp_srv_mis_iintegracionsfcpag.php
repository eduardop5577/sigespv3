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

interface IIntegracionSFCPAG {
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que busca los pagos  por integrar
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @param  string    $estatus - estatus del documento
	 * @return resultset $data - objeto de datos con las pagos a integrar 
	 */
	public function buscarPagosIntegrar($comprobante, $procede, $fecha, $estatus);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que busca el detalle presupuestario de ingreso de un pago en
	 *         el formato para mostrar en la ventana de informacion del comprobante
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @param  string    $codban - codigo del banco
	 * @param  string    $ctaban - cuenta bancaria
	 * @return resultset $data - objeto de datos con el detalle presupuestario de ingreso de un pago
	 */
	public function buscarDetalleComprobantePAGSPI($comprobante, $procede, $fecha, $codban, $ctaban, $numdoc);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc  Metodo que obtiene los detalles contables de un pago en
	 *         el formato para mostrar en la ventana de informacion del comprobante
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @param  string    $codban - codigo del banco
	 * @param  string    $ctaban - cuenta bancaria 
	 * @return resultset Resulset con la informacion del detalle contable de un pago 
	 */
	public function buscarDetalleComprobantePAGSCG($comprobante, $procede, $fecha, $codban, $ctaban, $numdoc);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que busca el detalle presupuestario de ingreso de un pago
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @param  string    $codban - codigo del banco
	 * @param  string    $ctaban - cuenta bancaria
	 * @return resultset $data - objeto de datos con el detalle presupuestario de ingreso de un pago
	 */
	public function buscarDetallePagoSPI($comprobante, $procede, $fecha, $codban, $ctaban, $numdoc, $arrcabecera);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc  Metodo que obtiene los detalles contables de un pago
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @param  string    $codban - codigo del banco
	 * @param  string    $ctaban - cuenta bancaria 
	 * @return resultset Resulset con la informacion del detalle contable de un pago 
	 */
	public function buscarDetallePagoSCG($comprobante, $procede, $fecha, $codban, $ctaban, $numdoc, $arrcabecera);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para actualizar el estatus de los movimiento de una cuenta por cobrar
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @param  string    $codban - codigo del banco
	 * @param  string    $ctaban - cuenta bancaria
	 * @param  string    $estatus - estatus del documento
	 * @param  string    $fechaConta - fecha de contabilizacion del documento
	 * @param  string    $comprobanteSigesp - fecha de contabilizacion del documento
	 */
	public function actualizarEstatusPAG($comprobante, $procede, $fecha, $codban, $ctaban, $estatus, $fechaConta, $comprobanteSigesp, $numdoc);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que procesa la contabilizacion de una Cuenta por cobrar
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $procede - codigo de procedencia del documento
	 * @param  string    $fecha - fecha del documento
	 * @param  string    $descripcion - descripcion del documento
	 * @param  string    $codban - codigo del banco
	 * @param  string    $ctaban - cuenta bancaria
	 * @param  string    $operacion - codigo de la operacion
	 * @param  string    $numdoc - número de documento
	 * @param  arreglo $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean boolean $resultado - Variable indicando si se pudo ó no contabilizar la cuenta por cobrar  
	 */
	public function contabilizarPAG($comprobante, $fecha, $procede, $descripcion, $codban, $ctaban, $operacion, $numdoc, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa la integracion de un lote de pagos
	 * @param array $arrJson - Arreglo tipo json que contiene la informacion de los pagos por procesar  
	 * @return string $resultado - string con los resultados de la operacion. 
	 */
	public function procesoContabilizarPAG($arrJson);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para verificar si un movimiento de banco se encuentra contabilizado
	 * @param  string    $codban - codigo del banco
	 * @param  string    $ctaban - cuenta bancaria
	 * @param  string    $comprobante - número de comprobante
	 * @return boolean - retorna true si el movimiento no esta contabilizado de lo contario retorna false
	 */
	public function verificarMovimientoBanco($codban, $ctaban, $comprobante, $operacion);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para los detalles de ingreso de un movimiento bancario
	 * @param  string    $codban - codigo del banco
	 * @param  string    $ctaban - cuenta bancaria
	 * @param  string    $comprobante - número de comprobante 
	 */
	public function eliminarDetalleSPI($codban, $ctaban, $comprobante, $operacion);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para los detalles contable de un movimiento bancario
	 * @param  string    $codban - codigo del banco
	 * @param  string    $ctaban - cuenta bancaria
	 * @param  string    $comprobante - número de comprobante 
	 */
	public function eliminarDetalleSCG($codban, $ctaban, $comprobante, $operacion);

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo para los detalles contable de un movimiento bancario
	 * @param  string    $codban - codigo del banco
	 * @param  string    $ctaban - cuenta bancaria
	 * @param  string    $comprobante - número de comprobante 
	 */
	public function eliminarDetalleFuenteFinanciamiento($codban, $ctaban, $comprobante, $operacion);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que procesa el reverso de la integracion de un pago
	 * @param  string    $comprobante - número de comprobante
	 * @param  string    $codban - codigo del banco
	 * @param  string    $ctaban - cuenta bancaria
	 * @param  arreglo $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean boolean $resultado - Variable indicando si se pudo ó no reversar el pago
	 */
	public function revContabilizarPAG($comprobante, $codban, $ctaban, $operacion, $fecha, $procede, $numdoc, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa el reverso de la integracion de un lote de pagos
	 * @param array $arrJson - Arreglo tipo json que contiene la informacion de los pagos a procesar  
	 * @return string $resultado - string con los resultados de la operacion.
	 */ 
	public function procesoRevContabilizarPAG($arrJson);
}
?>