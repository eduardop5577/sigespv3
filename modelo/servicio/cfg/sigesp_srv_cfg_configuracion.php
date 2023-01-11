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

class Configuracion extends DaoGenerico
{
	public $mensaje;
	public $valido= true;
	public $nomfisico;
	public $codsis;
	public $criterio = Array();
	private $conexionbd;

	public function __construct()
	{
		parent::__construct ( 'sigesp_config' );
		$this->conexionbd = $this->obtenerConexionBd(); 
	}

/***********************************************************************************
* @Función que busca a que sistemas se les ha hecho la apertura
* @parametros: 
* @retorno: 
* @fecha de creación: 21/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function leer()
	{
		try 
		{ 
			$consulta = " SELECT codemp, codsis, seccion, entry, type, value, 1 as valido ".
						"  FROM {$this->_table}";
			$cadena=" ";
			$total = count((array)$this->criterio);
			for ($contador = 0; $contador < $total; $contador++)
			{
				$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
						  $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
			}
			$consulta.=$cadena;
			$result = $this->conexionbd->Execute($consulta); 
			return $result; 
		}
		catch (exception $e) 
		{
			$this->valido = false;
		}
		$conexion->Close();
	}
	

/***********************************************************************************
* @Función para insertar una configuración
* @parametros: 
* @retorno:
* @fecha de creación: 27/10/2008.
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	public function incluirLocal()
	{
		$this->save();
		if($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}
	}	

	
/***********************************************************************************
* @Función que Elimina una Configuración
* @parametros: 
* @retorno:
* @fecha de creación: 27/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function eliminarLocal()
	{
		$this->delete();	
		if($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}
	}	
}
?>