<?php
/***********************************************************************************
* @Clase compartida para registrar los eventos que generan modificaciones a la base 
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
**********************************************************************
* @fecha modificacion  
* @autor  
* @descripcion  
***********************************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once('sigesp_dao_sss_notificacion.php');
require_once('sigesp_dao_sss_sistemaventana.php');

class RegistroEventos extends DaoGenerico
{
	public $valido;
	public $objNotificacion;
	public $objSistemaVentana;
	public $nomfisico;
	private $conexionbd;
	
	public function __construct() {
		parent::__construct ( 'sss_registro_eventos' );
		$this->conexionbd = $this->obtenerConexionBd();
		$this->objNotificacion = new Notificacion();
		$this->objSistemaVentana = new SistemaVentana(); 
	}
	
/***********************************************************************************
* @Función que incluye los eventos según lo realizado por el usuario
* @parametros: 
* @retorno:
* @fecha de creación: 27/08/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function incluirEvento()
	{	
		$valido = true;
		try 
		{ 
			$this->objSistemaVentana->codsis=$this->codsis;
			$this->objSistemaVentana->nomfisico=$this->nomfisico;
			$this->codmenu = $this->objSistemaVentana->obtenerCodigoMenu();
			$this->obtenerEquipo();
			$this->desevetra = str_replace("'","",$this->desevetra);
			$this->desevetra = str_replace('"','',$this->desevetra);	
			if(strtoupper($_SESSION["ls_gestor"])=='OCI8PO')
			{
				$fecha = $this->conexionbd->sysTimeStamp;
			}
			else
			{
				$fecha = "'".date("Y-m-d H:i:s")."'";
			}
			$consulta = "INSERT INTO {$this->_table} (codemp, codusu, codsis, codmenu, evento,  ".
	  					"                             fecevetra, equevetra, desevetra) ".
						"       VALUES('{$this->codemp}', '".$_SESSION['la_logusr']."', '{$this->codsis}', '{$this->codmenu}',".
			            "              '{$this->evento}', ".$fecha.",'{$this->equevetra}',".
			            "              '{$this->desevetra}')";
			$result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
		{
			$valido = false;
			if ($this->conexionbd->ErrorNo() == '23505' || $this->conexionbd->ErrorNo() == '1062' || $this->conexionbd->ErrorNo() == '-239' || $this->conexionbd->ErrorNo() == '-5'|| $this->conexionbd->ErrorNo() == '-1') {
					$updateId = "SELECT pg_catalog.setval(pg_get_serial_sequence('sss_registro_eventos', 'numeve'), (SELECT MAX(numeve) FROM sss_registro_eventos)+1)";
					$this->conexionbd->Execute($updateId);
			}
		}
		
		return $valido;
	}		
	
	
/***********************************************************************************
* @Función que obtiene el valor de la ip del equipo donde se realizó la transaccción 
* @parametros: 
* @retorno:
* @fecha de creación: 27/08/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function obtenerEquipo()
	{
		if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown'))
		{
			$this->equevetra = getenv('HTTP_CLIENT_IP');
		}	
		else if (getenv('HTTP_X_FORWARDED_FOR ') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR '), 'unknown'))
		{
			$this->equevetra = getenv('HTTP_X_FORWARDED_FOR ');
		}
		else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
		{
			$this->equevetra = getenv('REMOTE_ADDR');
		}
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
		{
			$this->equevetra = $_SERVER['REMOTE_ADDR'];
		}	
		else
		{
		   $this->equevetra = 'unknown';
		}
	}
}
?>