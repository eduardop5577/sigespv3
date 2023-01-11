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

interface iproveedor
{
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarCodigoProveedor($codemp);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarCodigoOrganizacion($codemp);
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarBancos($codemp);
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarMonedas($codemp);
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que busca todos los registros de parametro clasificacion
	 * @return resultset $data - arreglo de registros de parametros de clasificacion
	 */
	public function buscarProveedor($codemp,$ls_codpro,$ls_nompro,$ls_dirpro,$ls_rifpro,$ls_fecdes,$ls_fechas);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function buscarBancoSigecof();
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function buscarDocumentosProv($cod_pro);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function buscarCalificProv($cod_pro);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function buscarNivelClasif();
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function buscarCtaConPag($codemp,$sc_cta,$d_deno);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */ 
	public function buscarCtaConAnt($codemp,$sc_cta,$d_deno);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */ 
	public function buscarCtaConRec($codemp,$sc_cta,$d_deno);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */ 
	public function buscarPais();
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function buscarEstado($restriccion);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function buscarMunicipio($restriccion);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function buscarParroquia($restriccion);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	 
	public function guardarProveedor($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que modifica un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function modificarProveedor($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que elimina un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function eliminarProveedor($codemp,$objson,$arrevento);
	
		/**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si existe un Provedor
	 * @param string $codemp - codigo de empresa
	 * @param string $codpro - código del proveedor
	 * @return string $existe - si existe o no el mismo
	 */
	public function existeProveedor($codemp,$codpro);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarProveedores($codpro,$nompro,$dirpro);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	 
	public function guardarProveedorSocios($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarSocios($codpro);

	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function eliminarProveedorSocios($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function modificarProveedorSocios($codemp,$objson,$arrevento);
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	 
	public function guardarProveedorDocumentos($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarProveedorDoc($cod_pro);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function eliminarProveedorDocumentos($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function modificarProveedorDocumentos($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que inserta un registro de parametro clasificacion
	 * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	 
	public function guardarProveedorCalificacion($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function modificarProveedorCalificacion($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarProveedorCla($cod_pro);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function eliminarProveedorCalif($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarProveedorEspecialidades($cod_pro);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarProveedorEspecialidadesDisp($cod_pro);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function guardarProveedorEspecialidades($codemp, $arrjson, $cod_pro, $arrEvento);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarProveedorDeducciones($cod_pro);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarProveedorDeduccionesDisp($cod_pro);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function guardarProveedorDeducciones($codemp, $arrjson, $cod_pro, $arrEvento);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarDenomEstado($codpai);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarDenomMunicipio($codpai,$codest);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarDenomParroquia($codpai,$codest,$codmun);
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca los proveedores de las ordenes de compra a contabilizar
	 * @param string $codpro - código del Proveedor
	 * @param string $nompro - nombre del proveedor
	 * @param string $dirpro - direccion del proveedor
	 */
	public function buscarRifProv($rifpro, $seniat);
}