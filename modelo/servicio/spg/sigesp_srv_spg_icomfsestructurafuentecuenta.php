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

interface IComEstructuraFuenteCuenta {
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene las estructuras presupuestarias nivel 1.
	 * @param string $codemp - codigo de la empresa
	 * @return retorna resultset de Adodb con las estructuras presupuestarias nivel 1.
	 */
	public function buscarSpgEp1($codemp);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene las estructuras presupuestarias nivel 2.
	 * @param string $codemp - codigo de la empresa
	 * @param string $codest1 - codigo de la estuctura nivel 1
	 * @return retorna resultset de Adodb con las estructuras presupuestarias nivel 2.
	 */
	public function buscarSpgEp2($codemp, $codest1);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene las estructuras presupuestarias nivel 3.
	 * @param string $codemp - codigo de la empresa
	 * @param string $codest1 - codigo de la estuctura nivel 1
	 * @param string $codest2 - codigo de la estuctura nivel 2
	 * @return retorna resultset de Adodb con las estructuras presupuestarias nivel 3.
	 */
	public function buscarSpgEp3($codemp, $codest1, $codest2);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene las estructuras presupuestarias nivel 4.
	 * @param string $codemp - codigo de la empresa
	 * @param string $codest1 - codigo de la estuctura nivel 1
	 * @param string $codest2 - codigo de la estuctura nivel 2
	 * @param string $codest3 - codigo de la estuctura nivel 3
	 * @return retorna resultset de Adodb con las estructuras presupuestarias nivel 4.
	 */
	public function buscarSpgEp4($codemp, $codest1, $codest2, $codest3);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene las estructuras presupuestarias nivel 5.
	 * @param string $codemp - codigo de la empresa
	 * @param string $codest1 - codigo de la estuctura nivel 1
	 * @param string $codest2 - codigo de la estuctura nivel 2
	 * @param string $codest3 - codigo de la estuctura nivel 3
	 * @param string $codest4 - codigo de la estuctura nivel 4
	 * @return retorna resultset de Adodb con las estructuras presupuestarias nivel 5.
	 */
	public function buscarSpgEp5($codemp, $codest1, $codest2, $codest3, $codest4);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene las estructuras presupuestarias nivel 1.
	 * @param string $codemp - codigo de la empresa
	 * @return retorna resultset de Adodb con las estructuras presupuestarias nivel 1.
	 */
	public function buscarSpgEpN($cantnivel, $codemp);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene todas las fuentes de financiamiento asociadas a una estructura.
	 * @param string $codemp - codigo de la empresa
	 * @param string $codest1 - codigo de la estuctura nivel 1
	 * @param string $codest2 - codigo de la estuctura nivel 2
	 * @param string $codest3 - codigo de la estuctura nivel 3
	 * @param string $codest4 - codigo de la estuctura nivel 4
	 * @param string $codest5 - codigo de la estuctura nivel 5
	 * @param string $estcla  - estatus de clasificacion de la estructura
	 * @return retorna resultset de Adodb con las fuentes de finaciamiento casadas con una estructura.
	 */
	public function buscarFuentes($codemp, $codest1, $codest2, $codest3, $codest4, $codest5, $estcla);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene las cuentas presupuestarias de gasto asociadas a una estructura.
	 * @param string $codemp - codigo de la empresa
	 * @param string $codest1 - codigo de la estuctura nivel 1
	 * @param string $codest2 - codigo de la estuctura nivel 2
	 * @param string $codest3 - codigo de la estuctura nivel 3
	 * @param string $codest4 - codigo de la estuctura nivel 4
	 * @param string $codest5 - codigo de la estuctura nivel 5
	 * @param string $estcla  - estatus de clasificacion de la estructura
	 * @param string $codigo  - codigo de la cuenta presupuestaria de gasto
	 * @param string $denominacion - denominacion de la cuenta presupuestaria de gasto
	 * @param string $codcontable  - codigo de la cuenta contable
	 * @param string $logusr  - login del usuario
	 * @param string $grupo  - para filtar las cuentas por un grupo especifico primero 3 digitos
	 * @param string $rangoest - indica si no se desea aplicar el filtro por estructura 
	 * @return retorna resultset de Adodb con las cuentas presupuestarias de gasto asociadas a una estructura.
	 */
	public function buscarCuentas($codemp, $codest1, $codest2, $codest3, $codest4, $codest5, $estcla, $codfuefin, $codigo, $denominacion, $codcontable, $logusr, $grupo, $nofiltroest, $CuentaMovimiento);
	
}

?>