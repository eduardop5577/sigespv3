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

interface IIntegracionSAF {
	 /**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca las depreciaciones por contabilizar
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function buscarContabilizarDepSaf($mes,$anio,$estatus);
	 /**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca las depreciaciones por contabilizar
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function buscarRevContabilizacionDepSaf($mes,$anio,$estatus);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca las depreciaciones por contabilizar
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function buscarContabilizarDesSaf($numcmp,$feccmp,$estatus);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca las depreciaciones por contabilizar
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function buscarRevContabilizacionDesSaf($numcmp,$feccmp,$estatus);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca las depreciaciones por contabilizar
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function procesoContabilizarDepSaf($arrjson);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca las depreciaciones por contabilizar
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function procesoRevContabilizarDepSaf($arrjson);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca las depreciaciones por contabilizar
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function procesoContabilizarDesSaf($arrjson);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca las depreciaciones por contabilizar
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function procesoRevContabilizarDesSaf($arrjson);
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los detalles de presupuesto de gasto
	 * @param array     $arrcabecera - Cabecera del documento
	 * @return array $arregloSPG - arreglo con los Movimientos de presupuesto de gasto
	 */
	public function buscarDetalleGasto($arrcabecera,$anio,$mes);

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los detalles de Contabilidad
	 * @param array     $arrcabecera - Cabecera del documento
	 * @return array $arregloSCG - arreglo con los Movimientos de contabilidad
	 */
	public function buscarDetalleContable($arrcabecera,$anio,$mes,$as_depreciacion);	

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los pagos directos por contabilizar
	 * @param string     $as_numdoc - Número de documento
	 * @param string     $as_codban - Código de Banco
	 * @param string     $as_ctaban - Cuenta de Banco
	 * @param string     $as_codope - Código de operación
	 * @return valido 	 $boolean - devuelve válido si se pudo contabilizar el movimiento de banco
	 */
	public function buscarInformacionDetalle($as_anio,$as_mes);	
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los pagos directos por contabilizar
	 * @param string     $as_numdoc - Número de documento
	 * @param string     $as_codban - Código de Banco
	 * @param string     $as_ctaban - Cuenta de Banco
	 * @param string     $as_codope - Código de operación
	 * @return valido 	 $boolean - devuelve válido si se pudo contabilizar el movimiento de banco
	 */
	public function detalleContable($as_anio,$as_mes);
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los pagos directos por contabilizar
	 * @param string     $as_numdoc - Número de documento
	 * @param string     $as_codban - Código de Banco
	 * @param string     $as_ctaban - Cuenta de Banco
	 * @param string     $as_codope - Código de operación
	 * @return valido 	 $boolean - devuelve válido si se pudo contabilizar el movimiento de banco
	 */
	public function detalleContableDes($as_comp,$as_fecha);
	
}
?>