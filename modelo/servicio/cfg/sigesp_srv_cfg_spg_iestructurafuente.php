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

interface IServicioEstructuraFuente {
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene todas las fuentes de financiamiento asociadas a una empresa.
	 * @param string $codemp - codigo de la empresa
	 * @return retorna resultset de Adodb con las fuentes de finaciamiento de la empresa.
	 */
	public function buscarFuentes($codemp);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene todas las fuentes de financiamiento asociadas a una estructura.
	 * @param string $codemp - codigo de la empresa
	 * @param string $codest1 - codigo de la estuctura nivel 1
	 * @param string $codest2 - codigo de la estuctura nivel 2
	 * @param string $codest3 - codigo de la estuctura nivel 3
	 * @param string $codest4 - codigo de la estuctura nivel 4
	 * @param string $codest5 - codigo de la estuctura nivel 5
	 * @param string $estcla  - estatus de clasificacion de la estructura
	 * @return retorna resultset de Adodb con las fuentes de finaciamiento casadas con una estructura.
	 */
	public function buscarCasamiento($codemp, $codest1, $codest2, $codest3, $codest4, $codest5, $estcla);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene valida si el casamiento fue asociado a una cuenta.
	 * @param string $codemp - codigo de la empresa
	 * @param json   $arrjson   - objeto json con los parametros captados en la vista para realizar la operacion.
	 * @return retorna true si el casamiento no esta asociado a una cuenta.
	 */
	public function validarEliminar($codemp, $arrjson);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que guarda el casamiento de las estructuras y las fuentes de financiamiento.
	 * @param string $codemp    - codigo de la empresa
	 * @param json   $arrjson   - objeto json con los parametros captados en la vista para realizar la operacion.
	 * @param array  $arrEvento - arreglo con los datos para el log de seguridad
	 * @return retorna true si el casamiento fue guardado.
	 */
	public function grabarCasamientoEstructuraFuente($codemp, $arrjson, $arrEvento);
	
		
}

?>