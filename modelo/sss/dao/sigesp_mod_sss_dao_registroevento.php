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

require_once('../../base/librerias/php/general/sigesp_lib_daogenerico.php');


class registroEventoDao extends DaoGenerico
{
	function __construct() {
		parent::__construct ( 'sss_registro_eventos' );
	}
	
	public function getip()
	{
		   if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown'))
				   $ip = getenv('HTTP_CLIENT_IP');
		   else if (getenv('HTTP_X_FORWARDED_FOR ') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR '), 'unknown'))
				   $ip = getenv("HTTP_X_FORWARDED_FOR ");
		   else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
				   $ip = getenv('REMOTE_ADDR');
		   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
				   $ip = $_SERVER['REMOTE_ADDR'];
		   else
				   $ip = 'unknown';
		   
		   return($ip);
	}
    
    public function getNumeve(){
    
    	return agregarUno($this->BuscarCodigo('numeve'));
    }
    
	/***********************************/
	/* Metodos Estandar DAO Generico   */
	/***********************************/   
	public function incluirDto($dto){
		
		$this->pasarDatos($dto);		
		return $this->Incluir();
	}
	
	public function modificarDto($dto){
		
		$this->pasarDatos($dto);		
		return $this->Modificar();		
	}
	
	public function eliminarDto($dto){
		
		$this->pasarDatos($dto);		
		return $this->Eliminar();			
	}
	
	public function pasarDatos($ObJson)
	{
		foreach($ObJson as $IndiceJson =>$valorJson)
		{
			if($IndiceJson=='evento'){
				switch ($valorJson) {
					case 'INSERT':
						$valorJson="INSERTAR  ";
						break;
					case 'UPDATE':
						$valorJson="MODIFICAR ";
						break;
					case 'DELETE':
						$valorJson="ELIMINAR  ";
						break;
				}
			}
			
			$this->$IndiceJson = utf8_decode($valorJson);					
		}
		$this->numeve     = 'DEFAULT';
		$this->equevetra  = $this->getip();
		$this->fecevetra  = date('Y-m-d H:i:s');
		$this->ususisoper = 'N/D';
		$this->codintper  = '---------------------------------';
		
	}

	public function buscarTodos(){
		
		return $this->daogenerico->leerTodos();			
	}
	
	public function buscarCampo($campo,$valor)
	{
		return $this->daogenerico->buscarCampo($campo,$valor);
	}
	/***************************************/
	/* Fin Metodos Estandar DAO Generico   */
	/***************************************/
	
}

?>