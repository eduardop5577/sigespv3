<?php
/********************************************************************************* 	
* @Modelo para la definición de Empresa.
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
require_once('../../base/librerias/php/general/sigesp_lib_fabricadao.php');

class Empresa extends DaoGenerico
{
	public $valido = true;
	public $seguridad = true;
	public $mensaje;
	public $codsis;
	public $codemp;
	public $nomfisico;
	public $servidor;
	public $usuario;
	public $clave;
	public $basedatos;
	public $gestor;
	public $puerto;
	public $tipoconexionbd = 'DEFECTO';
	private $conexionbd;
	
	public function __construct()
	{
		parent::__construct ( 'sigesp_empresa' );
		$this->conexionbd = $this->obtenerConexionBd(); 
	}
	
	
/***********************************************************************************
* @Función para seleccionar con que conexion a Base de Datos se va a trabajar
* @parametros: 
* @retorno:
* @fecha de creación: 06/11/2008.
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	public function seleccionarConexion ()
	{
		if ($this->tipoconexionbd != 'DEFECTO') {
			$this->conexionbd = conectarBD($this->servidor, $this->usuario, $this->clave, $this->basedatos, $this->gestor, $this->puerto);
		}
		
		return $this->conexionbd;
	}
	
	
/***********************************************************************************
* @Función para buscar las empresas.
* @parametros: 
* @retorno:
* @fecha de creación: 30/07/2008.
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function filtrarEmpresas()
	{
		$this->seleccionarConexion();
		
		$this->valido = true;
		$cadena = "SELECT * ".
				  "  FROM {$this->_table} ".
				  " ORDER BY codemp ";
		$result = $this->conexionbd->Execute($cadena); 
		if ($result->EOF)
		{
			$this->valido = false;
			$this->mensaje = 'No se ha encontrado la empresa';
		}
		return $result;
	}

/***********************************************************************************
* @Función para buscar las empresas.
* @parametros: 
* @retorno:
* @fecha de creación: 30/07/2008.
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function filtrarEmpresa()
	{
		$this->seleccionarConexion();
		
		$this->valido = true;
		$cadena = "SELECT * ".
				  "  FROM {$this->_table} ".
				  " WHERE codemp = '{$this->codemp} ' ";
		$result = $this->conexionbd->Execute($cadena); 
		if ($result->EOF)
		{
			$this->valido = false;
			$this->mensaje = 'No se ha encontrado la empresa';
		}
		return $result;
	}


/***********************************************************************************
*  @Función para insertar una empresa por defecto.
* @parametros: 
* @retorno:
* @fecha de creación: 04/08/2008.
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function insertarEmpresa()
	{
		$this->mensaje = 'Incluyo la Empresa '.$this->nombre;
		$this->conexionbd->StartTrans();
		try 
		{ 
			$this->save();
		}	
		catch (exception $e) 
	   	{
			$this->valido  = false;				
			$this->mensaje = 'Error al Incluir la Empresa '.$this->nombre.' '.$this->conexionbd->ErrorMsg();
		} 
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}
	
	public function actualizarCentroCostos($arrjson)
	{
		$repuesta = false;
		$cadenaPk = "codemp='{$arrjson->codemp}'";
		$this->daoEmpresa = FabricaDao::CrearDAO('C','sigesp_empresa',array(),$cadenaPk);
		$this->daoEmpresa->cencosact = $arrjson->cencosact;
		$this->daoEmpresa->cencospas = $arrjson->cencospas;
		$this->daoEmpresa->cencosing = $arrjson->cencosing;
		$this->daoEmpresa->cencosgas = $arrjson->cencosgas;
		$this->daoEmpresa->cencosres = $arrjson->cencosres;
		$this->daoEmpresa->cencoscap = $arrjson->cencoscap;
		if($this->daoEmpresa->modificar()==1)
		{
			$repuesta = true;
		}
		return $repuesta;
	}
}
?>