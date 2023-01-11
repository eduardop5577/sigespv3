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

interface ITraspasoCuentas {
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene todas las cuentas de gastos pertenecientes a una estructru presupuestaria
	 *       en especifico.
	 * @param string $codemp - codigo de la empresa
	 * @param string $codestpro1 - codigo de la estrutura nivel 1
	 * @param string $codestpro2 - codigo de la estrutura nivel 2
	 * @param string $codestpro3 - codigo de la estrutura nivel 3
	 * @param string $codestpro4 - codigo de la estrutura nivel 4
	 * @param string $codestpro5 - codigo de la estrutura nivel 5
	 * @param string $estcla     - estatus de la estrutura
	 * @return retorna resultset de Adodb con el grupo de cuentas perteneciente a las estructura indicada.
	 */
	public function buscarCuentas($codemp, $codestpro1, $codestpro2, $codestpro3, $codestpro4, $codestpro5, $estcla);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que copia las cuentas de una estructura presupuestaria a otra.
	 * @param string $codemp    - codigo de la empresa
	 * @param json   $arrjson   - objeto json con los parametros captados en la vista para realizar la operacion.
	 * @param array  $arrEvento - arreglo con los datos para el log de seguridad
	 * @return retorna array con el resultado de la operacion.
	 */
	public function traspasarCuentas($codemp, $arrjson);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que valida si una cuenta existe para una estructura presupuestaria.
	 * @param string $codemp - codigo de la empresa
	 * @param string $codestpro1 - codigo de la estrutura nivel 1
	 * @param string $codestpro2 - codigo de la estrutura nivel 2
	 * @param string $codestpro3 - codigo de la estrutura nivel 3
	 * @param string $codestpro4 - codigo de la estrutura nivel 4
	 * @param string $codestpro5 - codigo de la estrutura nivel 5
	 * @param string $estcla     - estatus de la estrutura
	 * @param string $cuenta     - codigo de la cuenta a verificar
	 * @return retorna booleano true si la cuenta existe.
	 */
	public function validarCuentaDestino($codemp, $codestpro1, $codestpro2, $codestpro3, $codestpro4, $codestpro5, $estcla, $cuenta);
	
}

?>