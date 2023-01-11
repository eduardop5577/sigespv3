<?php
/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

interface iprogpago
{
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarBancos();	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarCtasBancarias($codban,$ctaban,$denctaban);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarCtasBancariasTransf($codbandes,$ctaban,$denctaban);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarSaldoCtaban($codban,$ctaban);
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca todos los registros de parametro clasificacion
	 * @return resultset $data - arreglo de registros de parametros de clasificacion
	 */
	public function buscarSolicitudes($tipproben,$tipvia);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca todos los registros de parametro clasificacion
	 * @return resultset $data - arreglo de registros de parametros de clasificacion
	 */
	public function insertarProgramacion($numsol,$fechaprog,$estmov,$codban,$ctaban,$provee_benef,$tipproben,$tipvia,$arrevento);
	
}