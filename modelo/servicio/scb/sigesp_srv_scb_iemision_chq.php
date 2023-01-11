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

interface iemision_chq {
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarConceptosScb($codope);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarContableCta($codban,$ctaban);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarVoucherNuevo($codemp);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarDocumentoExistente($numdoc,$codban,$ctaban,$operacion);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarVoucheExistente($chevau);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarSolicitudesProgProv($numsol,$fecdes,$fechas);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarSolicitudesProgBen($numsol,$fecdes,$fechas);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarCtasBancariasPagmin($codban,$ctaban,$denctaban);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarSolicitudesProgCheques($codban,$ctaban,$codigopb,$numpagmin,$codtipfon,$tipproben,$fechadhoy);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarNumdocCheques($codban,$ctaban);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarChequeraDoc($codban,$ctaban,$codusu);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarChequera($codban,$ctaban,$codusu);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarReporteCfg($sistema,$seccion,$variable,$valor,$tipo,$arrevento);
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca todos los registros de parametro clasificacion
	 * @return resultset $data - arreglo de registros de parametros de clasificacion
	 */
	public function emitirCheque($tipproben,$codproben,$codban,$ctaban,$numdoc,$fecmov,$codope,$estmov,$montomov,
							     $monobjret,$monret,$concepto,$codconmov,$chevau,$nomproben,$numordpagmin,$ls_modageret,
								 $ls_estretmil,$ls_sccuenta,$ls_numchequera,$arremisionch,$arrededucciones,$arrevento);
	
}