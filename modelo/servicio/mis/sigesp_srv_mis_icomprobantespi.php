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

Interface IComprobanteSPI {
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que verifica si se hizo el cierre presupuestario de ingreso.
	 * @return boolean $valido - Si se hizo el cierre devuelve true;
	 */
	public function existeCierreSPI();
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que obtiene el mensaje partiendo de la operacion
	 * @param  string $operacion - Información con la operacion de lo que se quiere contabilizar
	 * @return string $mensaje - valor del mensaje
	 */
	public function buscarMensaje($operacion);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que obtiene la operación partiendo del mensaje
	 * @param  string $mensaje - Información con el mensaje de lo que se quiere contabilizar
	 * @return string $operaion - valor de la operacion
	 */
	public function buscarOperacion($mensaje);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que verifica si existe el movimiento presupuestario de ingreso
	 * @param  array  $arrDetalleSPI - arreglo que contiene los datos del movimiento 
	 * @return boolean $existe - Si existe el movimiento devuelve true;
	 */
	public function existeMovimiento($arrDetalleSPI);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que obtiene el saldo de cada una de las operaciones
	 * @param  string $cuenta  - Codigo de la cuenta 
	 * @param  array  $arrDetalleSPI - Arreglo que contiene los datos del movimiento
	 * @return array $arrSaldo - Arreglo que contiene los saldos de la cuenta
	 */
	public function saldoSelect($cuenta, $arrDetalleSPI);

	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que obtiene el saldo según la operacion
	 * @param  string $cuenta  - Codigo de la cuenta 
	 * @param  array  $arrDetalleSPI - arreglo que contiene los datos del movimiento
	 * @return double $monto - monto del saldo segun la operacion 
	 */
	public function calcularSaldoRango($cuenta, $arrDetalleSPI, $operacion);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que verifica que los saldos no sobregiren el presupuesto
	 * @param  string $mensaje - Tipo de operacion del movimiento
	 * @param  array $arrSaldo - Arreglo que contiene los saldos de la cuenta
	 * @param  double $montoAnterior - Monto anterior del movimiento(en caso de reverso)
	 * @param  double $montoActual - Monto actual del movimiento 
	 * @return array $arrSaldoAjustado - Arreglo que contiene los saldos de la cuenta verificados
	 */								 
	public function saldosAjusta($mensaje, $arrSaldo, $montoAnterior, $montoActual);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que Actualiza el saldo de las operaciones de la  cuenta
	 * @param  string $cuenta  - Codigo de la cuenta 
	 * @param  array $arrSaldo - Arreglo que contiene los saldos de la cuenta
	 * @return boolean $valido - si no ocurrio ningún error al actualizar los saldos devuelve true
	 */
	public function saldosUpdate($cuenta, $arrSaldo);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que Actualiza el saldo de las operaciones de la  cuenta 
	 * @param  double $montoAnterior - Monto del saldo anterior
	 * @param  double $montoActual - Monto del saldo actual
	 * @param  array  $arrDetalleSPI - arreglo que contiene los datos del movimiento
	 * @return boolean $valido - si no ocurrio ningún error al actualizar los saldos devuelve true
	 */
	public function actualizarSaldo($arrDetalleSPI, $montoActual, $montoAnterior = 0);
		
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que guarda los detalles presupuestarios de un comprobante 
	 * @param  array  $arrDetalleSPI - arreglo que contiene los datos del movimiento
	 * @param  array  $arreEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean $valido - true si los detalles presupuestarios de un  comprobante se guardaron de manera exitosa
	 */
	public function guardarDetalleSPI($daoComprobante, $arrDetalleSPI, $arreEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que asigna los valores del detalle de comprobante que estan almacenados en un arreglo al 
	 *         objeto DAO del detalle del comprobante. 
	 * @param  array  $arrDetalleSPI - arreglo que contiene los datos del movimiento
	 * @return void vacio este metodo no retorna ningun valor
	 */
	public function setDaoDetalleSPI($arrDetalleSPI);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que valida si el movimiento esta asociado con otro. 
	 * @param  object  $daoComprobante - Objeto Active Record que contiene la informacion del comprobante
	 * @param  array  $arrDetalleSPI - arreglo que contiene los datos del movimiento
	 * @return void vacio este metodo no retorna ningun valor
	 */
	public function validaIntegridadComprobanteAjuste($daoComprobante, $arrDetalleSPI);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que valida si el movimiento esta asociado con otro. 
	 * @param  object  $daoComprobante - Objeto Active Record que contiene la informacion del comprobante
	 * @param  array  $arrDetalleSPI - arreglo que contiene los datos del movimiento
	 * @return void vacio este metodo no retorna ningun valor
	 */
	public function validaIntegridadComprobanteOtros($daoComprobante, $arrDetalleSPI);
	

	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que elimina los detalles presupuestarios de un comprobante. 
	 * @param  object  $daoComprobante - Objeto Active Record que contiene la informacion del comprobante
	 * @param  array   $arrDetalleSPI - arreglo que contiene los datos del movimiento
	 * @param  array   $arreEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean $valido - Si los detalles presupuestarios de un  comprobante se eliminaron de manera exitosa
	 */
	public function eliminarDetalleSPI($daoComprobante, $arrDetalleSPI, $arreEvento);
	
}
?>