<?php
/***********************************************************************************
* @Clase para Manejar  el proceso de configurar el envio de correo
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

class EnvioCorreo extends DaoGenerico
{
	public $valido = true;
	public $mensaje;
	public $nomfisico;
	public $admin = array();
	public $usuarioeliminar = array();
	private $conexionbd;
	
	public function __construct() {
		parent::__construct ( 'sss_envio_correo' );
		$this->conexionbd = $this->obtenerConexionBd(); 
	}
	
/***********************************************************************************
* @Función para incluir o actualizar los usuarios de un menu para un sistema
* @parametros: 
* @retorno: 
* @fecha de creación: 11/11/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function modificarLocal()
	{
		$this->mensaje='Modifico los usuarios al menu '.$this->codmenu.' del sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try 
		{ 
			$total=	count((array)$this->usuarioeliminar);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->usuarioeliminar[$contador]->codemp = $this->codemp;
				$this->usuarioeliminar[$contador]->codsis = $this->codsis;
				$this->usuarioeliminar[$contador]->codmenu = $this->codmenu;
				$this->usuarioeliminar[$contador]->delete();
			}
			$total=	count((array)$this->admin);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->admin[$contador]->codemp = $this->codemp;
				$this->admin[$contador]->codsis = $this->codsis;
				$this->admin[$contador]->codmenu = $this->codmenu;
				$this->admin[$contador]->save();
			}
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Modificar los usuarios al menu '.$this->codmenu.' del sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);	
	}
	
	
/***********************************************************************************
* @Función para eliminar los usuarios de un menu para un sistema
* @parametros: 
* @retorno: 
* @fecha de creación: 11/11/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function eliminarLocal()
	{
		$this->conexionbd->debug = 1;
		$this->mensaje='Elimino los usuarios al menu '.$this->codmenu;
		$this->conexionbd->StartTrans();
		try 
		{ 
			//$this->Delete();
			$consulta = " DELETE FROM {$this->_table} ".
						" WHERE codsis='{$this->codsis}' AND codmenu='{$this->codmenu}'";
			$result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Eliminar los usuarios al menu '.$this->codmenu.' del sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('ELIMINAR',$this->valido);			
	}
	
	
/***********************************************************************************
* @Función que busca los usuarios de un MENU
* @parametros: 
* @retorno:
* @fecha de creación: 11/11/2008
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*************************************************************************************/
	public function obtenerUsuarios()
	{
		try 
		{ 
			$consulta = " SELECT {$this->_table}.codsis, {$this->_table}.codmenu, ".
						" 		 sss_usuarios.codusu, sss_usuarios.nomusu, sss_usuarios.apeusu,".
						"  		 sss_usuarios.email, 1 as valido ".
						"  FROM {$this->_table} ".
						" INNER JOIN sss_usuarios ON ".
						" 	sss_usuarios.codemp = {$this->_table}.codemp
							AND sss_usuarios.codusu = {$this->_table}.codusu ".
						" INNER JOIN sss_sistemas ON 
							sss_sistemas.codsis = {$this->_table}.codsis  ".
						" INNER JOIN sss_sistemas_ventanas ON 
							sss_sistemas_ventanas.codmenu= {$this->_table}.codmenu  ".
						" WHERE {$this->_table}.codemp = '{$this->codemp}' ".
						" AND {$this->_table}.codsis = '{$this->codsis}' ".
						" AND {$this->_table}.codmenu = {$this->codmenu}"; 
			$result = $this->conexionbd->Execute($consulta);
			return $result;
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al consultar los usuarios del sistema '.$consulta.' '.$this->conexionbd->ErrorMsg();
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
		if($tipotransaccion)
		{
			$objEvento = new RegistroEventos();
			$tiponotificacion = 'NOTIFICACION';
		}
		else
		{
			$objEvento = new RegistroFallas();
			$tiponotificacion = 'ERROR';
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