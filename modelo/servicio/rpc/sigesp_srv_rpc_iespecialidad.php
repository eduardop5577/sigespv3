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

interface iespecialidad
{
	/**
	 * @author Ing. Luis Anibal Lang
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * * @param string $codemp - codigo de empresa
	 */
	public function buscarCodigoEspecialidad($codemp);
	
	/**
	 * @author Neneskha Salas
	 * @desc Metodo que busca todos los registros de tipo de empresa
	 * @return resultset $data - arreglo de registros de especialidad
	 */
	public function buscarEspecialidad();
	
	/**
	 * @author Neneskha Salas
	 * @desc Metodo que inserta un registro de tipo de empresa
	 * * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function guardarEspecialidad($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Luis Anibal Lang
	 * @desc Metodo que modifica un registro de especialidad
	 * * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function modificarEspecialidad($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Luis Anibal Lang
	 * @desc Metodo que elimina un registro de especialidad
	 * * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function eliminarEspecialidad($codemp,$objson,$arrevento);
}