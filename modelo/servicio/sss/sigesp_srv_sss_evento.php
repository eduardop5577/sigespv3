<?php
/***********************************************************************************
* @fecha de modificacion: 01/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_fabricadao.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_ievento.php');
require_once ($dirsrv.'/modelo/sss/sigesp_dao_sss_registroeventos.php');
require_once ($dirsrv.'/modelo/sss/sigesp_dao_sss_registrofallas.php');

class ServicioEvento implements IEvento 
{
	public $mensaje; 
	public $valido; 
	public $evento;
	public $tipoevento; 
	public $codemp;
	public $codsis;
	public $nomfisico;
	public $desevetra;
	
		
	public function __construct() {
		$this->mensaje = '';
		$this->valido = true;
	}

	public function incluirEvento()
	{
		if($this->tipoevento)
		{
			$objEvento = new RegistroEventos();
		}
		else
		{
			$objEvento = new RegistroFallas();
		}
		// Registro del Evento
		$objEvento->codemp = $this->codemp;
		$objEvento->codsis = $this->codsis;
		$objEvento->nomfisico = $this->nomfisico;
		$objEvento->evento = $this->evento;
		$objEvento->desevetra = $this->desevetra;
		$objEvento->incluirEvento();
		unset($objEvento);
	}	
}
?>