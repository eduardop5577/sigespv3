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

interface IIntegracionCXPRD {
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que busca las recepciones de documento para integrar
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  date      $fecemi - fecha de registro del documento
	 * @param  date      $fecapr - fecha de aprobacion del documento
	 * @param  string    $tipo - tipo que indica si el documento pertenece a un proveedor o beneficiario
	 * @param  string    $codigo - codigo de proveedor o beneficiario
	 * @param  string    $estatus - estatus del documento
	 * @param  string    $proceso - cadena que indica el tipo de proceso (reverso/anulacion)
	 * @return resultset $data - objeto de datos con las notas a integrar 
	 */
	public function buscarRecepcionIntegrar($numrecdoc, $fecemi, $fecapr, $tipo, $codigo, $estatus, $proceso = '');
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que busca el detalle presupuestario de una recepcion de documentos
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene  - cedula del beneficiario
	 * @param  string    $cod_pro   - codigo del proveedor
	 * @return resultset $data - objeto de datos con el detalle presupuestario de una recepcion de documentos 
	 */
	public function buscarDetallePresupuesto($numrecdoc, $codtipdoc, $ced_bene, $cod_pro);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que construye un arreglo con la data del detalle presupuestario de la Recepcion
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene  - cedula del beneficiario
	 * @param  string    $cod_pro   - codigo del proveedor
	 * @param  date      $fecregdoc - fecha de registro de la recepcion de documentos
	 * @return array     $arrDisponible - arreglo con la data del detalle presupuestario de la Recepcion 
	 */
	public function obtenerDetalleComprobanteRECSPG($numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $fecregdoc);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc  Metodo que obtiene los detalles contables de la recepcion
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene  - cedula del beneficiario
	 * @param  string    $cod_pro   - codigo del proveedor
	 * @return resultset Resulset con la informacion del detalle contable de la recepcion 
	 */
	public function obtenerDetalleComprobanteRECSCG($numrecdoc, $codtipdoc, $ced_bene, $cod_pro);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para obtener los detalles presupuestarios de gastos de una recepcion de documento
	 * @param  array $arrCabecera - Arreglo con los datos de la cabecera del comprobante
	 * @return array $arrDetalleSPG - Arreglo con los detalles de gasto del comprobante 
	 */
	public function obtenerDetalleRecepcionSPG($arrCabecera);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para obtener los detalles contables de una recepcion de documento
	 * @param  array $arrCabecera - Arreglo con los datos de la cabecera del comprobante
	 * @return array $arrDetalleSCG - Arreglo con los detalles contables del comprobante 
	 */
	public function obtenerDetalleRecepcionSCG($arrCabecera);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para insertar el registro historico de la contabilizacion de una recepcion de documentos
	 * @param  string $estatus - cadena con el valor del estatus de la recepcion
	 * @return boolean $resultado - variable que indica si el historico se inserto exitosamente 
	 */
	public function insertarHistoricoRecepcion($estatus,$fecha);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para eliminar el registro historio de la contabilizacion de una recepcion de documentos
	 * @param  string $estatus - cadena con el valor del estatus de la recepcion
	 * @return boolean $resultado - variable que indica si el historico se elimino exitosamente 
	 */
	public function eliminarHistoricoRecepcion($estatus);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que procesa la contabilizacion de una recepcion de documentos
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene - cedula del beneficiario
	 * @param  string    $cod_pro  - codigo del proveedor
	 * @param  arreglo   $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean boolean $resultado - Variable indicando si se pudo ó no contabilizar la recepcion de documentos  
	 */
	public function contabilizarREC($numrecdoc, $codtipdoc, $cod_pro, $ced_bene, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa la contabilizacion de un lote de recepciones de documentos
	 * @param array $arrJson - Arreglo tipo json que contiene la informacion de las recepciones a procesar  
	 * @return string $resultado - string con los resultados de la operacion.
	 */
	public function procesoContabilizarREC($arrJson);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que procesa el reverso de la contabilizacion de una recepcion de documentos
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene - cedula del beneficiario
	 * @param  string    $cod_pro  - codigo del proveedor
	 * @param  arreglo   $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean boolean $resultado - Variable indicando si se pudo ó no reversar la contabilizacion de la recepcion de documentos  
	 */
	public function revContabilizarREC($numrecdoc, $codtipdoc, $cod_pro, $ced_bene, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa el reverso de la contabilizacion de un lote de recepciones de documentos
	 * @param array $arrJson - Arreglo tipo json que contiene la informacion de las recepciones a procesar  
	 * @return string $resultado - string con los resultados de la operacion.
	 */
	public function procesoRevContabilizarREC($arrJson);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que procesa la anulacion de una recepcion de documentos
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene - cedula del beneficiario
	 * @param  string    $cod_pro  - codigo del proveedor
	 * @param  string    $fecha  - fecha de anulacion del documento
	 * @param  string    $conanurd  - concepto de anulacion del documento
	 * @param  arreglo   $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean boolean $resultado - Variable indicando si se pudo ó no anular la recepcion de documentos  
	 */
	public function anularREC($numrecdoc, $codtipdoc, $cod_pro, $ced_bene, $fecha, $conanurd, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa la anulacion de un lote de recepciones de documentos
	 * @param array $arrJson - Arreglo tipo json que contiene la informacion de las recepciones a procesar  
	 * @return string $resultado - string con los resultados de la operacion.
	 */
	public function procesoAnularREC($arrJson);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que procesa el reverso de la anulacion de una recepcion de documentos
	 * @param  string    $numrecdoc - numero de recepcion de documento
	 * @param  string    $codtipdoc - codigo de tipo de documento
	 * @param  string    $ced_bene - cedula del beneficiario
	 * @param  string    $cod_pro  - codigo del proveedor
	 * @param  arreglo   $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean boolean $resultado - Variable indicando si se pudo ó no reversar la anulacion de la recepcion de documentos  
	 */
	public function revAnularREC($numrecdoc, $codtipdoc, $cod_pro, $ced_bene, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa el reverso de la anulacion de un lote de recepciones de documentos
	 * @param array $arrJson - Arreglo tipo json que contiene la informacion de las recepciones a procesar  
	 * @return string $resultado - string con los resultados de la operacion.
	 */
	public function procesoRevAnularREC($arrJson);
}
?>