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

interface IIntegracionCXPSOP {
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que busca las Solicitudes de Pago por integrar
	 * @param  string    $numsol - número de Solicitud
	 * @param  date      $fecreg - Fecha de Registro
	 * @param  string    $tipo - si es proveedor ó beneficiario
	 * @param  string    $codigo - código del proveedor ó beneficiario
	 * @param  string    $estatus - estatus de la solicitud a integrar
	 * @return resultset $data - arreglo con las SOP que cumplan con las condiciones dadas
	 */
	public function buscarSolicitudesIntegrar($numsol,$fecreg,$fecapr,$tipo,$codigo, $estatus, $estrepcon);

	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que busca los detalles presupuestarios de la SOP 
	 * @param  string    $numsol - número de Solicitud
	 * @return resultset Resulset con la informacion del detalle presupuestario de la SOP
	 */
	public function buscarDetallePresupuesto($numsol);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que construye un arreglo con la data del detalle presupuestario de la SOP
	 * @param  string    $numsol - número de Solicitud
	 * @param  date      $fecsol - fecha de la solicitud
	 * @return array     $arrDisponible - arreglo con la data del detalle presupuestario de la SOP
	 */
	public function obtenerDetalleComprobanteSOPSPG($numsol, $fecsol);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Metodo que obtiene los detalles contables de la SOP
	 * @param  string    $numsol - número de Solicitud
	 * @return resultset Resulset con la informacion del detalle contable de la SOP
	 */
	public function obtenerDetalleComprobanteSOPSCG($numsol);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para obtener los detalles presupuestarios de gastos de una solicitud de pago
	 * @param  array $arrCabecera - Arreglo con los datos de la cabecera del comprobante
	 * @return array $arrDetalleSPG - Arreglo con los detalles de gasto del comprobante
	 */
	public function obtenerDetalleSolicitudSPG($arrCabecera);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para obtener los detalles contables de una solicitud de pago
	 * @param  array $arrCabecera - Arreglo con los datos de la cabecera del comprobante
	 * @return array $arrDetalleSCG - Arreglo con los detalles contables del comprobante
	 */
	public function obtenerDetalleSolicitudSCG($arrCabecera);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para obtener los detalles contables de una solicitud de pago
	 * @param  array $arrCabecera - Arreglo con los datos de la cabecera del comprobante
	 * @return array $arrDetalleSCG - Arreglo con los detalles contables del comprobante
	 */
	public function obtenerDetalleSolRecepcionSCG($arrCabecera);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para actualizar el estatus del detalle de una solicitud de pago
	 * @param  string $estatus - cadena con el valor del estatus a asignar
	 * @param  boolean $validar - este parametro indica si aplica o no la validacion para el reverso de anulacion
	 * @return boolean $resultado - variable que indica si el estatus se modifico exitosamente 
	 */
	public function actualizarEstatusDetalleSolicitud($estatus, $validar);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para insertar el registro historico de la contabilizacion de una solicitud de pago
	 * @param  string $estatus - cadena con el valor del estatus de la solicitud
	 * @return boolean $resultado - variable que indica si el historico se inserto exitosamente 
	 */
	public function insertarHistoricoSolicitud($estatus,$fecha);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa la solicitud de pago  por contabilizar
	 * @param string $numsol - número de Solicitud
	 * @param  arreglo $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean $resultado - Variable indicando si se pudo ó no contabilizar la SEP
	 */
	public function contabilizarSOP($numsol, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa la solicitud de pago  por contabilizar
	 * @param string $numsol - número de Solicitud
	 * @param  arreglo $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean $resultado - Variable indicando si se pudo ó no contabilizar la SEP
	 */
	public function contabilizarSopRD($numsol, $arrEvento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa la contabilizacion de un lote de solicitudes de pago
	 * @param array $arrJson - Arreglo tipo json que contiene el numero de las solicitudes a procesar
	 * @return string $resultado - string con los resultados de la operacion. 
	 */
	public function procesoContabilizarSOP($arrJson);

	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que valida si existe una OC cerrada 
	 * @param string $numsol - número de Solicitud
	 * @return boolean $resultado - retorna true si existe una OC cerrada
	 */
	public function validarCierreOC($numsol);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para obtener el codigo de un compromiso relacionado si existe 
	 * @param string $numrecdoc - número de la recepcion de documento
	 * @param string $codtipdoc - codigo del tipo de documento
	 * @param string $codpro - codigo del proveedor
	 * @param string $ced_bene - cedula del beneficiario
	 * @return string $numcomp - numero del compromiso
	 */
	public function obtenerNumeroCompromiso($numrecdoc,$codtipdoc,$codpro,$ced_bene);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que valida si existe un asiento de cierre 
	 * @param string $documeto - número de documeto
	 * @return boolean $resultado - retorna true si existe una OC cerrada
	 */
	public function validarAsientoCierre($documeto);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para eliminar el registro historio de la contabilizacion de una solicitud de pago
	 * @param  string $estatus - cadena con el valor del estatus de la solicitud
	 * @return boolean $resultado - variable que indica si el historico se elimino exitosamente 
	 */
	public function eliminarHistoricoSolicitud($estatus);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que reversa la contabilizacion de una solicitud de pago
	 * @param string $numsol - número de Solicitud
	 * @param  arreglo $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean $resultado - Variable indicando si se pudo ó no reversar la contabilizacion de la SEP
	 */
	public function revContabilizaSOP($numsol,$arrEvento);

	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa el reverso de la contabilizacion de un lote de solicitudes de pago
	 * @param array $arrJson - Arreglo tipo json que contiene el numero de las solicitudes a procesar
	 * @return string $resultado - string con los resultados de la operacion. 
	 */
	public function procesoReversoContabilizaSOP($arrJson);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que realiza la anulacion de una solicitud de pago
	 * @param string $numsol - número de Solicitud
	 * @param date $fecha - fecha de anulacion de la solicitud
	 * @param string $conanusop - Concepto de Anulación de la solicitud de pago
	 * @param  arreglo $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean $resultado - Variable indicando si se pudo ó no anular la solicitud de pago 
	 */
	public function anularSOP($numsol, $fecha, $conanusop,$arrEvento);

	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa la anulacion de la contabilizacion de un lote de solicitudes de pago
	 * @param array $arrJson - Arreglo tipo json que contiene el numero de las solicitudes a procesar 
	 * @return string $resultado - string con los resultados de la operacion. 
	 */
	public function procesoAnularSOP($arrJson);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que reversa la anulacion de la contabilizacion de una solicitud de pago
	 * @param string $numsol - número de Solicitud
	 * @param  arreglo $arrEvento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean $resultado - Variable indicando si se pudo ó no reversar la anulación de la solicitud de pago 
	 */
	public function revAnulacionSOP($numsol,$arrEvento);

	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que procesa el reverso de la anulacion de la contabilizacion de un lote de solicitudes de pago
	 * @param array $arrJson - Arreglo tipo json que contiene el numero de las solicitudes a procesar 
	 * @return string $resultado - string con los resultados de la operacion. 
	 */
	public function procesoReversoAnulacionSOP($arrJson);
}
?>