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

interface IIntegracionSNO {
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca las SOC por contabilizar
	 * @param string $estcondat - Estatus si es orden de Compra ó Servicio
	 * @param string $numordcom - Número de Orden de Compra
	 * @param string $codprov - código del Proveedor
	 * @param date $fecaprord - Fecha de Aprobación
	 * @return resultset $data - arreglo con las SOC que cumplan con las condiciones dadas
	 */
	public function buscarContabilizar($codcomp,$codnom,$codperi,$tipnom,$estatus);
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los periodos dependiendo de la nomina
	 * @return resultset $data - arreglo con las SNO que cumplan con las condiciones dadas
	 */
	public function buscarPeriodos($codnom,$estatus);
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los periodos dependiendo de la nomina
	 * @return resultset $data - arreglo con las SNO que cumplan con las condiciones dadas
	 */
	public function buscarInformacionDetalle($as_codcom,$as_codcomapo,$fecha);
	
	/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que busca los periodos dependiendo de la nomina
	 * @return resultset $data - arreglo con las SNO que cumplan con las condiciones dadas
	 */
	public function detalleContable($as_codcom,$as_codcomapo);
}
?>