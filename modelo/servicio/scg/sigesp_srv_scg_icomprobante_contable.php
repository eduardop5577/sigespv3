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

interface IComprobanteContable 
{
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que busca las cuentas contables
	 * @param string $codemp - codigo de empresa
	 * @param string $cuenta - cuenta
	 * @param string $denominacion - denominacion
	 * @return Resulset Adodb con los datos de las cuentas contables.
	 */
	public function buscarCtasCont($codemp,$cuenta,$denominacion,$status);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que busca los comprobantes existentes segun los filtros de busqueda
	 * @param string $codemp - codigo de empresa
	 * @param string $procede - procedencia del comprobante
	 * @param string $comprobante - numero del comprobante
	 * @param string $tipo - tipo (proveedor/beneficiario)
	 * @param string $provben - nombre del (proveedor/beneficiario)
	 * @param string $fecdesde - rango de fecha desde
	 * @param string $fechasta - rango de fecha hasta
	 * @param string $tipcom - indica el tipo de comprobante
	 * @return Resulset Adodb con los datos de los comprobantes.
	 */
	public function buscarComprobantes($codemp,$comprobante,$procede,$tipo,$provben,$fecdesde,$fechasta,$tipcom);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que busca los detalles contables del comprobante
	 * @param string $codemp - codigo de empresa
	 * @param string $procede - procedencia del comprobante
	 * @param string $comprobante - numero del comprobante
	 * @param string $fecha - fecha del comprobante
	 * @param string $codban - codigo del banco del comprobante
	 * @param string $ctaban - cuenta bancaria del comprobante
	 * @return Resulset Adodb con los datos de los detalles contables del comprobante.
	 */
	public function cargarDetalleContable($codemp,$procede,$comprobante,$fecha,$codban,$ctaban);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que guardar el comprobante contable
	 * @param string $codemp - codigo de empresa
	 * @param Json $objson - json con los datos de la interfaz
	 * @param Array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function guardarCmpCon($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que elimina el comprobante contable
	 * @param string $codemp - codigo de empresa
	 * @param Json $objson - json con los datos de la interfaz
	 * @param Array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function eliminarCmpCon($codemp,$objson,$arrevento);
	
}