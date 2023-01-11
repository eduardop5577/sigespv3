<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirbaselibsergen = "";
$dirbaselibsergen = dirname(__FILE__);
$dirbaselibsergen = str_replace("\\","/",$dirbaselibsergen);
$dirbaselibsergen = str_replace("/base/librerias/php/general","",$dirbaselibsergen);
require_once ($dirbaselibsergen."/modelo/servicio/cfg/sigesp_srv_cfg_sigespconfig.php");
require_once ("sigesp_lib_daogenerico.php");


class ServicioGenerico
{
	private $daogenerico;
	private $daosigespconfig;
	
	function __construct($tabla='')
	{
		if($tabla!=''){
			$this->daogenerico = new DaoGenerico ( $tabla );
		}
	}
		
	/***********************************************************************************
	* @Seccion: Metodos Estandar(INICIO)  
	* @Descripcion: Grupos de metodos implementados en el daogenerico que facilitan la 
	* gestion de los datos
	* @Autor: Ing. Gerardo Cordero
	************************************************************************************/
	
	public static function iniTransaccion() {
		DaoGenerico::iniciarTrans ();
	}

	public static function comTransaccion() {
		return DaoGenerico::completarTrans ();
	}
	
	public function getDaogenerico($cadenapk){
		$resultado=$this->daogenerico->load($cadenapk);
		if($resultado){
			return $this->daogenerico;
		}else{
			return $resultado;
		}
	}
	
	public function setDaogenerico($objdata){
		$this->daogenerico=$objdata;
	}
	
	public function getCodemp() {
		return $this->daogenerico->codemp;
	}
	
	public function setCodemp($codemp) {
		$this->daogenerico->codemp = $codemp;
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
	
	public function pasarDatos($ObJson) {
		$arratributos = $this->daogenerico->getAttributeNames();
		foreach ( $arratributos as $IndiceDAO ) {
			foreach ( $ObJson as $IndiceJson => $valorJson ) {
				if ($IndiceJson == $IndiceDAO && $IndiceJson != "codemp") {
					$this->daogenerico->$IndiceJson = utf8_decode ( $valorJson );
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
	/***********************************************************************************
	* @Seccion: Metodos Estandar(FIN)  
	* @Descripcion: Grupos de metodos implementados en el daogenerico que facilitan la 
	* gestion de los datos
	* @Autor: Ing. Gerardo Cordero
	************************************************************************************/
	
	/***********************************************************************************
	* @Seccion: Metodos de apoyo para la emision de reportes  
	* @Descripcion: Grupos de metodos implementados para manejar 
	* @Autor: Ing. Gerardo Cordero
	************************************************************************************/
	public function obtenerFormato($codemp,$codsis,$seccion,$entry,$type,$value) {
		$this->daosigespconfig = new SigespConfigDao();
		$formato = $this->daosigespconfig->getFormato($codemp,$codsis,$seccion,$entry,$type);
		if ($formato==""){
			$resultado=$this->daosigespconfig->insertarConfigReporte($codemp,$codsis,$seccion,$entry,$type,$value,$formato);
			if($resultado){
				$formato=$value;
			}
			else{
				$formato="Ruta ventana de fallo!!";
			}
		}
		
		return $formato;
	}
	
	/***********************************************************************************
	* @Seccion: Metodos de apoyo para la emision de reportes(INICIO)  
	* @Descripcion: Grupos de metodos implementados para gestionar el registro,modificacion
	* y eliminacion de la SEP
	* @Autor: Ing. Gerardo Cordero
	************************************************************************************/
	
}

?>