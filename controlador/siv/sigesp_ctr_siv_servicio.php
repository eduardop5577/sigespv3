<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once ("../../base/librerias/php/general/sigesp_lib_daogenerico.php");

class ServicioSiv
{
	private $daogenerico;
	
	public function __construct($tabla) 
	{
		$this->daogenerico = new DaoGenerico ( $tabla );
	}
	
	public function getCodemp() {
		return $this->daogenerico->codemp;
	}
	
	public function setCodemp($codemp) {
		
		$this->daogenerico->codemp = $codemp;
	}
	
	/***********************************/
	/* Metodos Estandar DAO Generico   */
	/***********************************/
	
	public static function iniTransaccion() {
		DaoGenerico::iniciarTrans ();
	}
	
	public static function comTransaccion() {
		return DaoGenerico::completarTrans ();
	}
	
	public function incluirDto($dto) {
		
		$this->pasarDatos ( $dto );
		$this->daogenerico->incluir ();
	}
	
	public function modificarDto($dto) {
		
		$this->pasarDatos ( $dto );
		return $this->daogenerico->modificar();
	}
	
	public function eliminarDto($dto) {
		
		$this->pasarDatos ( $dto );
		$this->daogenerico->eliminar ();
	}
	
	function pasarDatos($ObJson) {
		foreach ( $this->daogenerico as $IndiceDAO ) {
			foreach ( $ObJson as $IndiceJson => $valorJson ) {
				if ($IndiceJson == $IndiceDAO && $IndiceJson != "codemp") {
					$this->daogenerico->$IndiceJson = utf8_decode ( $valorJson );
				} else {
					$GLOBALS [$IndiceJson] = $valorJson;
				}
			}
		}
	}
	
	public function buscarTodos($campoorden="",$tipoorden=0) {
		
		return $this->daogenerico->leerTodos ($campoorden,$tipoorden);
	}
	
	public function buscarCampo($campo, $valor) {
		
		return $this->daogenerico->buscarCampo ( $campo, $valor );
	}
	
	public function buscarCampoRestriccion($restricciones)  {
		
		return $this->daogenerico->buscarCampoRestriccion($restricciones) ;
	}
	
	public function buscarSql($cadenasql)  {
		
		return $this->daogenerico->buscarSql($cadenasql) ;
	}
	
	public function concatenarSQL($arreglocadena)
	{
		return $this->daogenerico->concatenarCadena($arreglocadena);
	}
	
	public function obtenerConexionBd(){
		return $this->daogenerico->obtenerConexionBd();
	}

}

?>