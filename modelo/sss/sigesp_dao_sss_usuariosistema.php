<?php
/***********************************************************************************
* @Modelo para proceso de asignación de usuarios a sistema.
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

class UsuarioSistema extends DaoGenerico
{
	public $mensaje;
	public $evento;
	public $valido=true;
	public $existe = true;
	public $nomfisico;
	private $conexionbd;

	public function __construct() {
		parent::__construct ( 'sss_usuario_sistema' );
		$this->conexionbd = $this->obtenerConexionBd(); 
	}
	
	
/***********************************************************************************
* @Función que incluye un usuario en un sistema
* @parametros: 
* @retorno:
* @fecha de creación: 08/08/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 10/10/2008
* @descripción: Se agrego la seguridad
* @autor: Ing. Yesenia Moreno de Lang
***********************************************************************************/	
	public function incluirLocal()
	{
		$this->mensaje='Incluyo el Usuario '.$this->codusu.' en el Sistema '.$this->codsis;
		$this->save();
		if($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}

	
/***********************************************************************************
* @Función que Elimina el usuario de un sistema
* @parametros: 
* @retorno:
* @fecha de creación: 10/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	public function eliminarLocal()
	{
		$this->mensaje='Elimino el Usuario '.$this->codusu.' del Sistema '.$this->codsis;
		$this->delete();
		if($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}
		$this->incluirSeguridad('ELIMINAR',$this->valido);
	}

	
/***********************************************************************************
* @Función que Elimina todos los usuarios de un Sistema
* @parametros: 
* @retorno:
* @fecha de creación: 14/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	public function eliminarTodos()
	{
		$this->mensaje='Elimino los Usuarios del Sistema '.$this->codsis;
		$consulta = "DELETE FROM $this->_table ".
					" WHERE codemp = '$this->codemp' ".
					"   AND codsis = '$this->codsis' ";
		$result = $this->conexionbd->Execute($consulta);
		if($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg;
		}
		$this->incluirSeguridad('ELIMINAR',$this->valido);
	}	
	
	
/***************************************************************************
* @Función que verifica si un usuario está en un sistema
* @parametros: 
* @retorno:
* @fecha de creación: 06/08/2008
* @autor: Ing. Gusmary Balza
***************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
****************************************************************************/		
	function buscarUsuarioSistema()
	{
		try
		{	
			$consulta = " SELECT codsis,codusu ". 
						" FROM $this->_table ".
						" WHERE codemp='{$this->codemp}' ".
						" AND codusu='{$this->codusu}' ";
			$result = $this->conexionbd->Execute($consulta);
			if ($result->EOF)
			{
				$this->existe = false;
			}			
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Usuario '.$this->codusu.' en los Sistemas '.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}
	}
	
/***********************************************************************************
* @Función que Incluye el registro de la transacción exitosa
* @parametros: $evento
* @retorno:
* @fecha de creación: 10/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function incluirSeguridad($evento,$tipotransaccion)
	{
		if($tipotransaccion) // Transacción Exitosa
		{
			$objEvento = new RegistroEventos();
		}
		else // Transacción fallida
		{
			$objEvento = new RegistroFallas();
		}
		// Registro del Evento
		$objEvento->codemp = $this->codemp;
		$objEvento->codsis = 'SSS';
		$objEvento->nomfisico = $this->nomfisico;
		$objEvento->evento = $evento;
		$objEvento->desevetra = $this->mensaje;
		$objEvento->incluirEvento();
		unset($objEvento);
	}	
}
?>
