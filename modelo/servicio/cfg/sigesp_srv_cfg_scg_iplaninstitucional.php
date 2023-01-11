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

interface ISCGPlanInstitucional
{
	
	 /**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que verifica si una cuenta existe en el plan unico de cuentas
	 * @param string $cuenta - codigo de la cuenta a verificar
	 * @return boolean $existe - retorna true si la cuenta existe
	 */
	public function existeCuentaPlanUnico($cuenta);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca una cuenta en el plan unico de cuentas
	 * @param string $cuenta - codigo de la cuenta a ubicar
	 * @return bretorna resultset de Adodb con infromacion de la cuenta.
	 */
	public function buscarCuentaPlanUnico($cuenta);
	
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que valida que la estructura de la cuenta a insertar sea correcta
	 * @param string $cuenta - codigo de la cuenta a verificar
	 * @return boolean $existe - retorna true si la cuenta existe
	 */
	public function validarCuenta($cuenta);
	
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que valida si el registro de la cuenta guarda relacion con algun otro
	 *       en otra tabla
	 * @param string $codemp - codigo de la empresa
	 * @param string $cuenta - codigo de la cuenta a verificar
	 * @return boolean retorna true si existe una relacion con la cuenta
	 */
	public function validarRelacionesCuenta($cuenta);
	
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que valida si la cuenta tiene cuenta de menor nivel asociada
	 * @param string $codemp - codigo de la empresa
	 * @param string $cuenta - codigo de la cuenta a verificar
	 * @param string $formatoCuenta - formato del plan de cuenta institucional
	 * @return boolean retorna true si la cuenta tiene cuentas de nivel menor asociadas
	 */
	public function validarCuentaHijas($cuenta);
	
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca una cuenta en el plan de cuenta institucional
	 * @param string $codemp - codigo de la empresa
	 * @param string $cuenta - codigo de la cuenta a buscar
	 * @return retorna resultset de Adodb con infromacion de la cuenta.
	 */
	public function buscarCuenta($cuenta);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca todas las cuenta del plan de cuenta institucional
	 * @param string $codemp - codigo de la empresa
	 * @return retorna resultset de Adodb con infromacion de las cuentas.
	 */
	public function buscarTodasCuenta();
	
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que registra o modifica los datos de una cuenta del plan de cuenta institucional
	 * @param string $codemp        - codigo de la empresa
	 * @param json   $arrJsonCuenta - objeto json son los datos de la cuenta
	 * @param string $formatoPlan   - formato del plan unico de cuentas
	 * @param string $formatoCuenta - formato del plan de cuenta institucional
	 * @return string retorna cadena con '1' si no hay errores, si no retorna descripcion del error ocurrido.
	 */
	public function grabarCuenta($arrJsonCuenta,$operacion);
	
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que valida que la estructura de la cuenta a insertar sea correcta
	 * @param string $codemp - codigo de la empresa
	 * @param string $cuenta - codigo de la cuenta a eliminar
	 * @param string $formatoCuenta - formato del plan de cuenta institucional
	 * @return string retorna cadena con '1' si no hay errores, si no retorna descripcion del error ocurrido.
	 */
	public function eliminarCuenta($cuenta);
	
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene las cuentas de provisiones acumuladas y reservas tecnicas de depreciacion
	 * @param string $codemp - codigo de empresa
	 * @param string $cueproacu - digitos de cuenta de provicion acumulada configurada en empresa
	 * @param string $cuedepamo - digitos de cuenta de reservas tecnicas de depreciacion acumulada 
	 * 							  configurada en empresa
	 */
	public function buscarCuentaProvAcumResTec($cueproacu, $cuedepamo);
}

?>