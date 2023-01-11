<?php
/***********************************************************************************
* @Modelo para proceso de asignación de usuarios a grupo.
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
require_once('sigesp_dao_sss_registroeventos.php');
require_once('sigesp_dao_sss_registrofallas.php');

class UsuarioGrupo extends DaoGenerico
{
	public $mensaje;
	public $valido = true;
	public $existe = true;
	public $codsis;
	public $nomfisico;
	private $conexionbd;

	public function __construct() {
		parent::__construct ( 'sss_usuarios_en_grupos' );
		$this->conexionbd = $this->obtenerConexionBd(); 
	}
	
	
/***********************************************************************************
* @Función que incluye un usuario en un grupo
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
		$this->mensaje='Incluyo el Usuario '.$this->codusu.' en el Grupo '.$this->nomgru;
		$this->save();
		if($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}

	
/***********************************************************************************
* @Función que Elimina el usuario de un Grupo
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
		$this->mensaje='Elimino el Usuario '.$this->codusu.' del Grupo '.$this->nomgru;
		$this->delete();
		if($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}
		$this->incluirSeguridad('ELIMINAR',$this->valido);
	}	
	

/***********************************************************************************
* @Función que Elimina todos los usuarios de un Grupo
* @parametros: 
* @retorno:
* @fecha de creación: 10/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	public function eliminarTodos()
	{
		$this->mensaje='Elimino los Usuarios del Grupo '.$this->nomgru;
		$consulta = "DELETE FROM $this->_table ".
					" WHERE codemp = '$this->codemp' ".
					"   AND nomgru = '$this->nomgru' ";
		$result = $this->conexionbd->Execute($consulta);
		if($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg;
		}
		$this->incluirSeguridad('ELIMINAR',$this->valido);
	}

	
/***************************************************************************
* @Función que Busca si un usuario esta asignado a un grupo
* @parametros: 
* @retorno:
* @fecha de creación: 03/09/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
******************************************************************************/			
	function buscarUsuarioGrupo()
	{
		try
		{
			$consulta = " SELECT nomgru,codusu ".
						"   FROM $this->_table ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}' ";	
			$result = $this->conexionbd->Execute($consulta); 
			if ($result->EOF)
			{
				$this->existe = false;	
			}
			$result->Close();			 
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Usuario'.$this->codusu.' en los Grupos '.' '.$this->conexionbd->ErrorMsg();
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
		$objEvento->codsis = $this->codsis;
		$objEvento->nomfisico = $this->nomfisico;
		$objEvento->evento = $evento;
		$objEvento->desevetra = $this->mensaje;
		$objEvento->incluirEvento();
		unset($objEvento);
	}
}
?>
