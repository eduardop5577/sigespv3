<?php
/***********************************************************************************
* @Modelo para la definición del servidor de Correo
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_daogenerico.php');

class Correo extends DaoGenerico
{
	public $mensaje;
	public $valido= true;
	private $conexionbd;
	
	public function __construct()
	{
		parent::__construct ( 'sigesp_correo' );
		$this->conexionbd = $this->obtenerConexionBd();
	}	

/***********************************************************************************
* @Función que busca la configuración de la empresa
* @parametros: 
* @retorno: 
* @fecha de creación: 25/08/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function obtenerConfiguracion()
	{
		try 
		{ 
			$consulta = "SELECT msjenvio, msjsmtp, msjservidor, msjpuerto, msjhtml ".
					    "  FROM {$this->_table} ".
					    " WHERE codemp = '".$this->codemp."'";
			$result = $this->conexionbd->Execute($consulta); 
			while (!$result->EOF)
			{
				$this->msjenvio =$result->fields["msjenvio"];
				$this->msjsmtp =$result->fields["msjsmtp"];
				$this->msjservidor =$result->fields["msjservidor"];
				$this->msjpuerto =$result->fields["msjpuerto"];
				$this->msjhtml =$result->fields["msjhtml"];
				$result->MoveNext();
			}
			$result->Close();
		}
		catch (exception $e) 
		{
			$this->valido = false;
			$this->mensaje='Error al obtener la Configuración del Correo '.$this->conexionbd->ErrorMsg();
		}
	}
}
?>