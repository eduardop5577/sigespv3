<?php
/***********************************************************************************
* @Modelo para la definición de Sistema. 
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

class Sistema extends DaoGenerico
{
	public $valido=true;
	public $existe=true;
	public $codemp;
	public $mensaje;
	public $cadena;
	public $criterio;	
	public $codsis;
	public $nomfisico;
	var $admin = array();
	var $usuarioeliminar = array();
	private $conexionbd;

	public function __construct()
	{
		parent::__construct ( 'sss_sistemas' );
		$this->conexionbd = $this->obtenerConexionBd(); 
	}
		
/***********************************************************************************
* @Función que  valida si un sistema ya existe
* @parametros: 
* @retorno:
* @fecha de creación: 08/08/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	public function verificarCodigo()
	{
		try 
		{ 
			$consulta="SELECT codsis ".
					  "  FROM {$this->_table} ".
					  " WHERE codsis = '{$this->codsis}' ";
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
			$this->mensaje='Error al consultar el Sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}
	}

	
/***********************************************************************************
* @Función para insertar un sistema.
* @parametros: 
* @retorno:
* @fecha de creación: 30/09/2008.
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	function incluirLocal()
	{
		$this->mensaje='Incluyo el Sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try 
		{ 
			$consulta = " INSERT INTO {$this->_table} ".
						"	(codsis,nomsis,estsis,imgsis,tipsis,ordsis) ".
						" 	values ('{$this->codsis}','{$this->nomsis}','1','','',0)";
			$result = $this->conexionbd->Execute($consulta);
			$total=	count((array)$this->admin);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->admin[$contador]->codemp = $this->codemp;
				$this->admin[$contador]->codsis = $this->codsis;
				$this->admin[$contador]->nomfisico = $this->nomfisico;
				$this->admin[$contador]->incluirLocal();			
			}
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Incluir el Sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}	

	
/***********************************************************************************
*  @Función que  actualiza el sistema y su detalle
* @parametros: 
* @retorno:
* @fecha de creación: 08/08/2008
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	public function modificarLocal()
	{
		$this->mensaje='Modifico el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try 
		{ 
			$consulta = "UPDATE {$this->_table} ".
						"  SET nomsis = '{$this->nomsis}'".
						" WHERE codsis = '{$this->codsis}' ";
			$result = $this->conexionbd->Execute($consulta);
			$total=	count((array)$this->usuarioeliminar);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->usuarioeliminar[$contador]->codemp = $this->codemp;
				$this->usuarioeliminar[$contador]->codsis = $this->codsis;
				$this->usuarioeliminar[$contador]->nomfisico = $this->nomfisico;
				$this->usuarioeliminar[$contador]->eliminarLocal();
			}
			$total=	count((array)$this->admin);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->admin[$contador]->codemp = $this->codemp;
				$this->admin[$contador]->codsis = $this->codsis;
				$this->admin[$contador]->nomfisico = $this->nomfisico;
				$this->admin[$contador]->incluirLocal();
			}
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Modificar el Sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);	
	}	

	
/***********************************************************************************
*  @Función que  elimina el sistema y su detalle
* @parametros: 
* @retorno:
* @fecha de creación: 08/08/2008
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function eliminarEvento()
	{
		$this->mensaje='Modifico el sistema a Inactivo '.$this->codsis;
		$this->conexionbd->StartTrans(); 
		try 
		{ 
			$this->usuarioeliminar[0]->codemp = $this->codemp;
			$this->usuarioeliminar[0]->codsis = $this->codsis;
			$this->usuarioeliminar[0]->nomfisico = $this->nomfisico;
			$this->usuarioeliminar[0]->eliminarTodos();		
			$consulta = "UPDATE {$this->_table} ".
						"  SET estsis = '0'".
						" WHERE codsis = '{$this->codsis}' ";
			$result = $this->conexionbd->Execute($consulta);
		} 
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje=' Error al Eliminar el Sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
	   	} 
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('ELIMINAR',$this->valido);
		
	}

	
/***********************************************************************************
* @Función que busca los usuarios de un sistema
* @parametros: 
* @retorno:
* @fecha de creación: 08/08/2008
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	public function obtenerUsuarios()
	{
		//$this->conexionbd->debug = 1;
		try 
		{ 
			$consulta = " SELECT {$this->_table}.codsis, {$this->_table}.nomsis as nomsistema, ".
						" 		 sss_usuarios.codusu, sss_usuarios.nomusu, sss_usuarios.apeusu,".
						"  		 sss_usuarios.email, 1 as valido ".
						"  FROM {$this->_table} ".
						" INNER JOIN (sss_usuario_sistema ".
						" 			  INNER JOIN sss_usuarios ".
						"	 			 ON sss_usuarios.codemp = sss_usuario_sistema.codemp ".
						"				AND sss_usuarios.codusu = sss_usuario_sistema.codusu) ".
						"    ON sss_usuario_sistema.codemp = '{$this->codemp}' ".
						"   AND sss_usuario_sistema.codsis = {$this->_table}.codsis ".
						" WHERE {$this->_table}.codsis = '{$this->codsis}' ";
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
* @Función que busca uno todos los sistemas
* @parametros: 
* @retorno:
* @fecha de creación: 08/08/2008
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	public function leer() 
 	{		
		try 
		{ 
			$consulta = "SELECT codsis,nomsis, 1 as valido ".
						"  FROM {$this->_table} ".
						" WHERE estsis = '1' ";
			if (($this->criterio=='')&&(($this->cadena!='')))
			{
				$consulta .= " AND codsis ='{$this->cadena}'";
			}
			elseif ($this->criterio!='')
			{
				$consulta .= " AND {$this->criterio} like '{$this->cadena}%'";
		  	}
		  	$consulta.= "ORDER BY codsis";
		  	$result = $this->conexionbd->Execute($consulta);
			return $result; 
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Sistema '.$consulta.' '.$this->conexionbd->ErrorMsg();
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
		// Envío de Notificación
		$objEvento->objNotificacion->codemp=$this->codemp;
		$objEvento->objNotificacion->sistema='SSS';
		$objEvento->objNotificacion->tipo=$tiponotificacion;
		$objEvento->objNotificacion->titulo='DEFINICIÓN DE SISTEMA';
		$objEvento->objNotificacion->usuario=$_SESSION['la_logusr'];
		$objEvento->objNotificacion->operacion=$this->mensaje;
		$objEvento->objNotificacion->enviarNotificacion();
		unset($objEvento);
	}	
}	
?>