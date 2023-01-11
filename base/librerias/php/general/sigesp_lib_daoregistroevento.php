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

require_once ("sigesp_lib_daogenerico.php");
class daoRegistroEvento extends DaoGenerico {
	
	function __construct($tabla) {
		parent::__construct ( $tabla );
	}
	
	public function getip(){
		
		if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')){
			$ip = getenv('HTTP_CLIENT_IP');
		}
		else if (getenv('HTTP_X_FORWARDED_FOR ') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR '), 'unknown')){
			$ip = getenv("HTTP_X_FORWARDED_FOR ");
		}	
		else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')){
			$ip = getenv('REMOTE_ADDR');
		}
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')){
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		else{
			$ip = 'unknown';
		}
			
		return($ip);
	}
	
	public function getNumeve(){
		return agregarUno($this->BuscarCodigo('numeve'));
    }
    
	public function setDataLog($arrseguridad){
		foreach($arrseguridad as $indice =>$valor){
			if($indice == 'evento'){
				switch ($valor) {
					case 'INSERT':
						$valor = "INSERTAR  ";
						break;
					case 'UPDATE':
						$valor = "MODIFICAR ";
						break;
					case 'DELETE':
						$valor = "ELIMINAR  ";
						break;
					case 'PROCESS':
						$valor = "PROCESAR  " ;
						break;
					case 'REPORT':
						$valor = "REPORTAR  " ;
						break;
				}
			}
			
			$this->$indice = utf8_decode($valor);					
		}
		
		$this->numeve     = $this->getNumeve();
		$this->equevetra  = $this->getip();
		$this->fecevetra  = date('Y-m-d H:i:s');
		$this->ususisoper = 'N/D';
		$this->codintper  = '---------------------------------';
	}
	
	public function guardarLog($arrseguridad) {
		$this->setDataLog($arrseguridad);
		return $this->incluir(true,"numeve",true,0);
	}
}

?>