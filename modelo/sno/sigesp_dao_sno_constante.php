<?php
/*****************************************************************************
* @Modelo para las funciones de tipo de personal.
* @fecha de modificacion: 26/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/modelo/sss/sigesp_dao_sss_registroeventos.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/modelo/sss/sigesp_dao_sss_registrofallas.php');

class Constante extends DaoGenerico
{
	public $valido = true;
	public $mensaje;
	public $cadena;
	public $criterio;
	public $seguridad = true;
	public $codsis;
	public $nomfisico;
	private $conexionbd;

	public function __construct() {
		parent::__construct ( 'sno_constante' );
		$this->conexionbd = $this->obtenerConexionBd(); 
	}
	
/***********************************************************************************
* @Funci?n para insertar una constante
* @parametros: 
* @retorno:
* @fecha de creaci?n: 09/10/2008.
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificaci?n:
* @descripci?n:
* @autor:
***********************************************************************************/			
	function incluirLocal()
	{
		$this->conexionbd->StartTrans();
		$this->mensaje='Incluyo la Constante de N?mina '.$this->codcons;
		try 
		{
			$this->save();
		}		
		catch (exception $e) 
	   	{
			$this->valido  = false;				
			$this->mensaje='Error al Incluir la Constante '.$this->codcons.' '.$this->conexionbd->ErrorMsg();
		} 
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}
	
	
/***********************************************************************************
* @Funci?n que Busca una,varias todas las constantes
* @parametros: 
* @retorno:
* @fecha de creaci?n: 09/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificaci?n:
* @descripci?n:
* @autor:
***********************************************************************************/		
	public function leer() 
 	{		
		try
		{
			$consulta = " SELECT DISTINCT codnom,codcons,nomcon, 1 as valido ".
						" FROM {$this->_table} ".
						" WHERE codemp='{$this->codemp}'";

		  	$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
            }
            $consulta.= $cadena;
		  	$consulta.= " ORDER BY codcons";
		   	$result = $this->conexionbd->Execute($consulta);	
		 	return $result;	
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al consultar la Constante '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
	   	} 
	}

	
/***********************************************************************************
* @Funci?n que Incluye el registro de la transacci?n exitosa
* @parametros: $evento
* @retorno:
* @fecha de creaci?n: 10/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci?n:
* @descripci?n:
* @autor:
***********************************************************************************/
	function incluirSeguridad($evento,$tipotransaccion)
	{
		if($tipotransaccion) // Transacci?n Exitosa
		{
			$objEvento = new RegistroEventos();
		}
		else // Transacci?n fallida
		{
			$objEvento = new RegistroFallas();
		}
		// Registro del Evento
		$objEvento->codemp = $this->codemp;
		$objEvento->codsis = $this->codsis;
		$objEvento->nomfisico = $this->nomfisico;
		$objEvento->evento = $evento;
		$objEvento->desevetra = $this->mensaje;
		$objEvento->incluirLocal();
		unset($objEvento);
	}	
}	
?>