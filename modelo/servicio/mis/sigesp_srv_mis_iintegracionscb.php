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

interface IIntegracionSCB {
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los Movimientos de Banco por contabilizar
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function buscarMovBcoContabilizar($numdoc,$fecdoc,$codope,$numcarord);
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los Movimientos de Banco por Reversar la contabilización
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function buscarMovBcoRevContabilizacion($numdoc,$fecdoc,$codope,$numcarord);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los Movimientos de Banco por Anular la contabilización
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function buscarMovBcoAnular($numdoc,$fecdoc,$codope,$numcarord);
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los Movimientos de Banco por Reversar la Anulación de la contabilización
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @param string     $codope - código de la operacion
	 * @param string     $numcarord - Fecha de Aprobación
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function buscarMovBcoRevAnulacion($numdoc,$fecdoc,$codope,$numcarord);
		 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los pagos directos por contabilizar
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function buscarOpdContabilizar($numdoc,$fecdoc);
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los pagos directos por contabilizar
	 * @param string     $numdoc - Nro de Documento 
	 * @param date       $fecdoc - Fecha del Documento
	 * @return resultset $data - arreglo con los Movimientos de Banco que cumplan con las condiciones dadas
	 */
	public function buscarRevOpdContabilizar($numdoc,$fecdoc);	

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los detalles de presupuesto de gasto
	 * @param array     $arrcabecera - Cabecera del documento
	 * @return array $arregloSPG - arreglo con los Movimientos de presupuesto de gasto
	 */
	public function buscarDetalleGasto($arrcabecera,$tabla);

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los detalles de Contabilidad
	 * @param array     $arrcabecera - Cabecera del documento
	 * @return array $arregloSCG - arreglo con los Movimientos de contabilidad
	 */
	public function buscarDetalleContable($arrcabecera);	

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los detalles de presupuesto de ingreso
	 * @param array     $arrcabecera - Cabecera del documento
	 * @return array $arregloSPI - arreglo con los Movimientos de presupuesto de ingreso
	 */
	public function buscarDetalleIngreso($arrcabecera);

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que elimina los historicos de pagado de las solicitudes de pago asociadas 
	 * @param string   $numsol - Numero de solicitud de pago
	 * @return boolean $valido - Valido si elimino sin ningún problema
	 */
	public function eliminarHistoricoPagado($numsol);

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que coloca en estatus de pagado ó pagado parcial las solicitudes de pagos asociadas 
	 * @param  
	 * @return boolean $valido - Valido si proceso las solicitudes sin ningún problema
	 */
	public function procesarProgramacionPagos();
		
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si un movimiento de banco existe 
	 * @param string $estmov - Estatus del movimiento
	 * @return boolean $existe - si existe el movimiento de banco
	 */	
	public function existeMovimientoBanco($estmov);
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que cre un movimiento de banco con nuevos datos
	 * @param string     $procede - Procede del movimiento
	 * @param string     $comprobante - Numero del comprobante
	 * @param string     $estmov - Estatus del Movimiento 
	 * @param string     $fecmov - FEcha del Movimiento
	 * @param string     $fechaconta - Fecha de Contabilización
	 * @param string     $fechaanula - Fecha de Anulación
	 * @param string     $conanu - Concepto de Anulación
	 * @return Boolean   $valido - si se creo el movmiento sin problema
	 */	
	public function crearMovimientoBanco($procede,$comprobante,$estmov,$fecmov,$fechaconta,$fechaanula,$conanu);
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Elimina un movimiento de banco
	 * @param Boolean     $revanu - Procede del movimiento
	 * @return Boolean   $valido - si se elimino el movimiento sin problema
	 */	
	public function eliminarMovimientoBanco($revanu=false);
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Busca si el documento es un anticipo y tiene amortizaciones
	 * @param 
	 * @return Integer   $valor - Cantidad de amortizaciones que tiene
	 */	
	public function buscarAmortizaciones();

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Busca las amortizaciones y las resta 
	 * @param 
	 * @return Integer   $valor - Cantidad de amortizaciones que tiene
	 */	
 	public function eliminarAmortizacion();
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los pagos directos por contabilizar
	 * @param string     $codban - Código de Banco
	 * @param string     $ctaban - Cuenta de Banco
	 * @param string     $numdoc - Nro de Documento
	 * @param string     $codope - Código de operación
	 * @param string     $estmov - Estatus del Movimiento
	 * @param array      $arrevento - Arreglo de eventos
	 * @return valido 	 $boolean - devuelve válido si se pudo contabilizar el movimiento de banco
	 */
//	public function revContabilizacionMovBco($codban,$ctaban,$numdoc,$codope,$estmov,$arrevento);	
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los pagos directos por contabilizar
	 * @param string     $codban - Código de Banco
	 * @param string     $ctaban - Cuenta de Banco
	 * @param string     $numdoc - Nro de Documento
	 * @param string     $codope - Código de operación
	 * @param string     $estmov - Estatus del Movimiento
	 * @param date       $fechaanula - Fecha de Anulación
	 * @param string     $conanu - Concepto de Anulación 
	 * @param array      $arrevento - Arreglo de eventos
	 * @return valido 	 $boolean - devuelve válido si se pudo contabilizar el movimiento de banco
	 */
//	public function anularMovBco($codban,$ctaban,$numdoc,$codope,$estmov,$fechaanula,$conanu,$arrevento);
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los pagos directos por contabilizar
	 * @param string     $as_numdoc - Número de documento
	 * @param string     $as_codban - Código de Banco
	 * @param string     $as_ctaban - Cuenta de Banco
	 * @param string     $as_codope - Código de operación
	 * @return valido 	 $boolean - devuelve válido si se pudo contabilizar el movimiento de banco
	 */
	public function buscarInformacionDetalle($as_numdoc,$as_codban,$as_ctaban,$as_codope);	
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los pagos directos por contabilizar
	 * @param string     $as_numdoc - Número de documento
	 * @param string     $as_codban - Código de Banco
	 * @param string     $as_ctaban - Cuenta de Banco
	 * @param string     $as_codope - Código de operación
	 * @return valido 	 $boolean - devuelve válido si se pudo contabilizar el movimiento de banco
	 */
	public function detalleContable($as_numdoc,$as_codban,$as_ctaban,$as_codope);
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los pagos directos por contabilizar
	 * @param string     $as_numdoc - Número de documento
	 * @param string     $as_codban - Código de Banco
	 * @param string     $as_ctaban - Cuenta de Banco
	 * @param string     $as_codope - Código de operación
	 * @return valido 	 $boolean - devuelve válido si se pudo contabilizar el movimiento de banco
	 */
	public function detalleContableMovcol($as_numdoc,$as_codban,$as_ctaban,$as_codope);	
}
?>