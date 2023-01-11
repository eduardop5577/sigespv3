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

interface ibeneficiario {
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca todos los registros de parametro clasificacion
	 * @return resultset $data - arreglo de registros de parametros de clasificacion
	 */
	public function buscarBeneficiarios($as_cedbene,$as_nombene,$as_apebene);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca los registros de beneficiarios para cargarlos
	 *       en la configuracion de Empresa/Banco/Carta Orden
	 * @param string $cedula - cedula del beneficiario
	 * @param string $nombre - nombre del beneficiario
	 * @param string $apellido - apellido del beneficiario 
	 * @return resultset $data - arreglo de registros de beneficiarios
	 */
	public function buscarBeneficiariosCatEmpresa($cedula, $nombre, $apellido);
	
	/**
	 * @author Neneskha Salas
	 * @desc Metodo que verifica si existe un Beneficiario
	 * @param string $codemp - codigo de empresa
	 * @param string $cedbene - cedula del beneficiario
	 * @return string $existe - si existe o no el mismo
	 */
	public function existeBeneficiario($codemp,$cedbene);
	
	/**
	 *@author Neneskha Salas
	 * @desc Metodo que verifica si existe un Beneficiario
	 * @param string $codemp - codigo de empresa
	 * @param Json $objson
	 * @param Array $arrevento
	 * @return true si la operacion se ejecuto satisfactoriamente
	 */
	public function guardarBeneficiario($codemp,$objson,$arrevento);
	
	/**
	 *@author Neneskha Salas
	 * @desc Metodo que verifica si existe un Beneficiario
	 * @param string $codemp - codigo de empresa
	 * @param Json $objson
	 * @param array $arrevento
	 * @return true si la operacion se ejecuto satisfactoriamente
	 */
	public function modificarBeneficiario($codemp,$objson,$arrevento);
	
	
	/**
	 *@author Neneskha Salas
	 * @desc Metodo que verifica si existe un Beneficiario
	 * @param string $codemp - codigo de empresa
	 * @param Json $objson
	 * @param Array $arrevento
	 * @return true si la operacion se ejecuto satisfactoriamente
	 */
	public function eliminarBeneficiario($codemp,$objson,$arrevento);
	
	
	/**
	 *@author Neneskha Salas
	 * @desc Metodo que obtiene la lista de bancos registrados
	 * @param string $codemp - codigo de empresa
	 * @return resulset de adodb con los datos de los bancos
	 */
	public function buscarBanco($codemp);
	
	public function buscarBeneficiarioDeduccionesDisp($ced_bene);
	
	public function buscarBenDeduccionesDisp($ced_bene);
	
	public function buscarRifBen($rifben);
	
	public function buscarCedBen($cedben);
	
}