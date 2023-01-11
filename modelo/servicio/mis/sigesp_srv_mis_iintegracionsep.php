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

interface IIntegracionSEP {

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc   Metodo que busca las SEP por contabilizar
	 * @param  string    $numsol - número de Solicitud
	 * @param  date      $fecreg - Fecha de Registro
	 * @param  string    $tipo - si es proveedor ó beneficiario
	 * @param  string    $codigo - código del proveedor ó beneficiario
	 * @return resultset $data - arreglo con las sep que cumplan con las condiciones dadas
	 */
	public function buscarContabilizar($numsol,$fecreg,$fecapr,$tipo,$codigo);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc   Metodo que busca las SEP por contabilizar
	 * @param  string    $numsol - número de Solicitud
	 * @param  date      $fecreg - Fecha de Registro
	 * @param  string    $tipo - si es proveedor ó beneficiario
	 * @param  string    $codigo - código del proveedor ó beneficiario
	 * @return resultset $data - arreglo con las sep que cumplan con las condiciones dadas
	 */
	public function buscarRevContabilizacion($numsol,$fecreg,$fecapr,$tipo,$codigo,$fechaconta);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc   Metodo que busca las SEP por Anular
	 * @param  string    $numsol - número de Solicitud
	 * @param  date      $fecreg - Fecha de Registro
	 * @param  string    $tipo - si es proveedor ó beneficiario
	 * @param  string    $codigo - código del proveedor ó beneficiario
	 * @return resultset $data - arreglo con las sep que cumplan con las condiciones dadas
	 */
	public function buscarAnular($numsol,$fecreg,$fecapr,$tipo,$codigo);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc   Metodo que busca las SEP por Reversar la anulación
	 * @param  string    $numsol - número de Solicitud
	 * @param  date      $fecreg - Fecha de Registro
	 * @param  string    $tipo - si es proveedor ó beneficiario
	 * @param  string    $codigo - código del proveedor ó beneficiario
	 * @return resultset $data - arreglo con las sep que cumplan con las condiciones dadas
	 */
	public function buscarRevAnulacion($numsol,$fecreg,$fecapr,$tipo,$codigo,$fechaanula);

	 /**
	 * @author Ing. Luis Anibal Lang
	 * @desc   Metodo que busca los detalles presupuestarios de la sep 
	 * @param  string    $numsol - número de Solicitud
	 * @return resultset $cabecera||$detalleSpg - Resulset con la informacion de la cabecera y el detalle
	 */
	public function buscarDetallePresupuesto($numsol);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo acumula el monto de las cuentas presupuestarias de la SEP
	 * @param string $numsol - número de Solicitud
	 * @return boolean $montocuenta - Variable indicando el monto acumulado
	 */
	public function validarMontoSEP($numsol);		
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo busca los detalles presupuestarios de la SEP
	 * @param string  $numsol - número de Solicitud
	 * @param array   $arrcabecera - Arreglo con la cabecera del comprobante
	 * @return array $arregloSPG - Arreglo con los detalles presupuestarios de la SEP
	 */
	public function buscarDetallePresupuestario($numsol,$arrcabecera);
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Procesa la Sep por contabilizar
	 * @param Json $objson - json con los datos de la interfaz
	 * @return boolean $resultado - Variable indicando si se pudo ó no contabilizar la SEP
	 */
	public function Contabilizar($objson);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Procesa la Sep por reversar la contabilizacion
	 * @param Json $objson - json con los datos de la interfaz
	 * @return boolean $resultado - Variable indicando si se pudo ó no reversar la contabilizacion de la SEP
	 */
	public function revContabilizacion($objson);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Procesa la Sep por Anular
	 * @param Json $objson - json con los datos de la interfaz
	 * @return boolean $resultado - Variable indicando si se pudo ó no contabilizar la SEP
	 */
	public function Anular($objson);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Procesa la Sep por reversar la anulación
	 * @param Json $objson - json con los datos de la interfaz
	 * @return boolean $resultado - Variable indicando si se pudo ó no reversar la anulación de la SEP
	 */
	public function revAnulacion($objson);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Procesa el reverso de un precompromiso de la SEP 
	 * @param string $numsol - número de Solicitud
	 * @param date   $fecha - Reverso del precompromiso
	 * @param array   $arrevento - Arreglo del evento
	 * @return boolean $resultado - Variable indicando si se pudo ó no reversar el compromiso de la SEP
	 */
	public function revPrecompromiso($numsol,$fecha,$numordcom,$estcondat,$arrevento);
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Elimina el reverso de un precompromiso de la SEP 
	 * @param string $numsol - número de Solicitud
	 * @param array   $arrevento - Arreglo del evento
	 * @return boolean $resultado - Variable indicando si se pudo ó no reversar el compromiso de la SEP
	 */
	public function eliminarRevPrecompromiso($numsol,$numordcom,$estcondat,$arrevento);
	
	 /**
	 * @author Ing. Luis Anibal Lang
	 * @desc Metodo que Busca los detalles del comprobante
	 * @param string $numsol - número de Solicitud
	 * @return boolean $resultado - Variable indicando si se pudo ó no reversar el compromiso de la SEP
	 */
	public function buscarInformacionDetalleC($numsol);
	 
        /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si la se esta relacionada con otra
	 * @param string $numsol - nï¿½mero de Solicitud
	 * @return boolean $existe
	 */
	public function verificarSEPRelacionadas($numsol,$estsol);
}
?>