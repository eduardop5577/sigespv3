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

interface IIntegracionCXPNCND {
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que busca las notas credito/debito para integrar
	 * @param  string    $numsol - número de Solicitud
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $operacion - operacion del documento (credito/debito)
	 * @param  date      $fecope - fecha de registro del documento
	 * @param  date      $fecapr - fecha de aprobacion del documento
	 * @param  string    $estatus - estatus del documento
	 * @return resultset $data - objeto de datos con las notas a integrar 
	 */
	public function buscarNotasIntegrar($numsol, $numrecdoc, $operacion, $fecope, $fecapr, $estatus);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que busca el detalle presupuestario de una nota de credito/debito
	 * @param  string    $numsol - número de Solicitud
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene  - cedula del beneficiario
	 * @param  string    $cod_pro   - codigo del proveedor
	 * @param  string    $codope - operacion del documento (credito/debito)
	 * @param  string    $numdc - numero de la nota de credito/debito
	 * @return resultset $data - objeto de datos con el detalle presupuestario de una nota de credito/debito 
	 */
	public function buscarDetallePresupuesto($numsol, $numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $codope, $numdc);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que construye un arreglo con la data del detalle presupuestario de la NCD
	 * @param  string    $numsol - número de Solicitud
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene  - cedula del beneficiario
	 * @param  string    $cod_pro   - codigo del proveedor
	 * @param  string    $codope - operacion del documento (credito/debito)
	 * @param  string    $numdc - numero de la nota de credito/debito
	 * @param  date      $fecope - fecha de registro de la nota de credito/debito
	 * @return array     $arrDisponible - arreglo con la data del detalle presupuestario de la NCD 
	 */
	public function obtenerDetalleComprobanteNCDSPG($numsol, $numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $codope, $numdc, $fecope);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc  Metodo que obtiene los detalles contables de la NCD
	 * @param  string    $numsol - número de Solicitud
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene  - cedula del beneficiario
	 * @param  string    $cod_pro   - codigo del proveedor
	 * @param  string    $codope - operacion del documento (credito/debito)
	 * @param  string    $numdc - numero de la nota de credito/debito
	 * @return resultset Resulset con la informacion del detalle contable de la NCD 
	 */
	public function obtenerDetalleComprobanteNCDSCG($numsol, $numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $codope, $numdc);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para obtener los detalles contables de una nota credito/debito
	 * @param  array $arrCabecera - Arreglo con los datos de la cabecera del comprobante
	 * @return array $arrDetalleSCG - Arreglo con los detalles contables del comprobante
	 */
	public function obtenerDetalleNotaSCG($arrCabecera);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para obtener los detalles presupuestarios de gastos de una nota credito/debito
	 * @param  array $arrCabecera - Arreglo con los datos de la cabecera del comprobante
	 * @return array $arrDetalleSPG - Arreglo con los detalles de gasto del comprobante
	 */
	public function obtenerDetalleNotaSPG($arrCabecera);

	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para obtener los detalles presupuestarios de ingresos de una nota credito/debito
	 * @param  array $arrCabecera - Arreglo con los datos de la cabecera del comprobante
	 * @return array $arrDetalleSPI - Arreglo con los detalles de ingreso del comprobante
	 */
	public function obtenerDetalleNotaSPI($arrCabecera);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que procesa la contabilizacion de una nota de credito/debito
	 * @param  string    $numsol - número de Solicitud
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene - cedula del beneficiario
	 * @param  string    $cod_pro  - codigo del proveedor
	 * @param  string    $numdc - numero de nota de credito/debito
	 * @param  arreglo $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean boolean $resultado - Variable indicando si se pudo ó no contabilizar la nota credito/debito  
	 */
	public function contabilizarNCD($numsol, $numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $codope, $numdc, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa la contabilizacion de un lote de notas de credito/debito
	 * @param array $arrJson - Arreglo tipo json que contiene la informacion de las notas de credito/debito a procesar  
	 * @return string $resultado - string con los resultados de la operacion. 
	 */
	public function procesoContabilizarNCD($arrJson);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que procesa el reverso de la contabilizacion de una nota de credito/debito
	 * @param  string    $numsol - número de Solicitud
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene - cedula del beneficiario
	 * @param  string    $cod_pro  - codigo del proveedor
	 * @param  string    $numdc - numero de nota de credito/debito
	 * @param  arreglo $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean boolean $resultado - Variable indicando si se pudo ó no reversar la nota credito/debito  
	 */
	public function revContabilizarNCD($numsol, $numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $codope, $numdc, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa el reverso de la contabilizacion de un lote de notas de credito/debito
	 * @param array $arrJson - Arreglo tipo json que contiene la informacion de las notas de credito/debito a procesar  
	 * @return string $resultado - string con los resultados de la operacion.
	 */ 
	public function procesoRevContabilizarNCD($arrJson);
}
?>