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

interface IIntegracionSOC {
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca las SOC por contabilizar
	 * @param string $estcondat - Estatus si es orden de Compra ó Servicio
	 * @param string $numordcom - Número de Orden de Compra
	 * @param string $codprov - código del Proveedor
	 * @param date $fecaprord - Fecha de Aprobación
	 * @return resultset $data - arreglo con las SOC que cumplan con las condiciones dadas
	 */
	public function buscarContabilizar($estcondat,$numordcom,$codprov,$fecaprord,$fecordcom);
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca las SOC que se puede reversar la contabilizacion
	 * @param string $estcondat - Estatus si es orden de Compra ó Servicio
	 * @param string $numordcom - Número de Orden de Compra
	 * @param string $codprov - código del Proveedor
	 * @param date $fecaprord - Fecha de Aprobación
	 * @return resultset $data - arreglo con las SOC que cumplan con las condiciones dadas
	 */
	public function buscarRevContabilizacion($estcondat,$numordcom,$codprov,$fecaprord,$fecordcom,$fechaconta);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca las SOC que se pueden anular
	 * @param string $estcondat - Estatus si es orden de Compra ó Servicio
	 * @param string $numordcom - Número de Orden de Compra
	 * @param string $codprov - código del Proveedor
	 * @param date $fecaprord - Fecha de Aprobación
	 * @return resultset $data - arreglo con las SOC que cumplan con las condiciones dadas
	 */
	public function buscarAnular($estcondat,$numordcom,$codprov,$fecaprord,$fecordcom);
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca las SOC que se pueden reversar la anulacion
	 * @param string $estcondat - Estatus si es orden de Compra ó Servicio
	 * @param string $numordcom - Número de Orden de Compra
	 * @param string $codprov - código del Proveedor
	 * @param date $fecaprord - Fecha de Aprobación
	 * @return resultset $data - arreglo con las SOC que cumplan con las condiciones dadas
	 */
	public function buscarRevAnulacion($estcondat,$numordcom,$codprov,$fecaprord,$fecordcom,$fechaanula);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo acumula el monto de las cuentas presupuestarias de la Orden de Compra
	 * @return boolean $montocuenta - Variable indicando el monto acumulado
	 */
	public function validarMontoSOC();		
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si la sep asociada a la orden de compra ya se reverso
	 * @param string $numsol - Número de SEP
	 * @param string $estcom - Estatus de la orden de compra
	 * @return boolean $valido - Devuelve verdadero si ya se reverso la SEP
	 */
	public function verificarReversoSep($numsol,$estcom);		
		
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca si la orden de compra tiene sep asociadas y reversa el prcompromiso
	 * @param array $arrevento - Arreglo de evento
	 * @return boolean $valido - Devuelve verdadero si reverso las sep sin problema
	 */
	public function revSolicitudesAsociadas($arrevento);		
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo busca los detalles presupuestarios de la Orden de Compra
	 * @param array   $arrcabecera - Arreglo con la cabecera del comprobante
	 * @return array $arregloSPG - Arreglo con los detalles presupuestarios de la Orden de Compra
	 */
	public function buscarDetallePresupuestario($arrcabecera);	

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica que la orden de compras no este asociada a una recepción de documentos
	 * @param string   $status - Estatus de la Recepción de documentos
	 * @return boolean $existe - Parametro que me indica si esta o no en una recepción de documentos.
	 */
	public function verificarEnRecepcion($procede);	

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que actualiza los estatus de los detalles de las solicitudes asociadas
	 * @return boolean $valido - Parametro que me indica si se actualizaron los documentos correctamente.
	 */
	public function procesarDetallesSolicitudes();	

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que actualiza los estatus de los detalles de las solicitudes asociadas
	 * @return boolean $valido - Parametro que me indica si se actualizaron los documentos correctamente.
	 */
	public function reversarDetallesSolicitudes();	

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Contabiliza las ordenes de compra
	 * @param Json $objson - json con los datos de la interfaz
	 * @return boolean $valido - Devuelve verdadero si se pudo contabilizar la orden de compra
	 */
	public function Contabilizar($objson);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Procesa la Orden de Compra por reversar la contabilizacion
	 * @param Json $objson - json con los datos de la interfaz
	 * @return boolean $resultado - Variable indicando si se pudo ó no reversar la contabilizacion de la Orden de Compra
	 */
	public function revContabilizacion($objson);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Anula las ordenes de compra
	 * @param Json $objson - json con los datos de la interfaz
	 * @return boolean $valido - Devuelve verdadero si se pudo anular la orden de compra
	 */
	public function Anular($objson);	
	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Procesa la Orden de Compra por reversar la anulación
	 * @param Json $objson - json con los datos de la interfaz
	 * @return boolean $resultado - Variable indicando si se pudo ó no reversar la anulación de la Orden de Compra
	 */
	public function revAnulacion($objson);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Procesa la Orden de Compra por reversar la anulación
	 * @param string $numordcom - Número de Orden de Compra
	 * @param string $estcondat - Estatus si es orden de Compra ó Servicio
	 * @param array  $arrevento - Arreglo del Evento
	 * @return boolean $resultado - Variable indicando si se pudo ó no reversar la anulación de la Orden de Compra
	 */
	public function buscarInformacionDetalle($numordcom,$estcondat);	
	
}
?>