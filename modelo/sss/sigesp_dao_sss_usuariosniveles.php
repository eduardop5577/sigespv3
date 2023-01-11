<?php
/***********************************************************************************
* @Modelo para proceso de asignación de los permisos internos a los usuarios
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

class UsuarioNiveles extends DaoGenerico
{
	public $mensaje;
	public $evento;
	public $valido = true;
	public $existe = true;
	public $seguridad = true;
	public $codsis;
	public $codintper;
	public $nomfisico;
	public $admin = array();
	public $usuarioeliminar = array();
	public $criterio = array();
	public $objDerechos;
	
	public $servidor;
	public $usuario;
	public $clave;
	public $basedatos;
	public $gestor;
	public $puerto;
	public $tipoconexionbd = 'DEFECTO';
	private $conexionbd;

	public function __construct() {
		parent::__construct ( 'sss_niv_usuarios' );
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
	public function selecionarConexion ()
	{
		if ($this->tipoconexionbd != 'DEFECTO')
		{
			$this->conexionbd = conectarBD($this->servidor, $this->usuario, $this->clave, $this->basedatos, $this->gestor, $this->puerto);
		}
	}

/***********************************************************************************
* @Función que inserta los permisos de un usuario para: una constante, una nomina, 
* un personal
* @parametros: 
* @retorno:
* @fecha de creación: 09/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 
* @descripción: 
* @autor: Ing. 
***********************************************************************************/
	function incluirNivelesUsuarios()
	{
	
		$consulta = " INSERT INTO {$this->_table} (codemp, codasiniv, codniv, codusu, codtipniv) ".
			        " VALUES ('{$this->codemp}','{$this->codasiniv}','{$this->codniv}','{$this->codusu}','{$this->codtipniv}')	";
		$result = $this->conexionbd->Execute($consulta);
		if ($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}	
		$this->incluirSeguridad('INSERTAR',$this->valido);		
	}

		
/***********************************************************************************
* @Función que actualiza los permisos asignados a usuarios  
* @parametros: 
* @retorno:
* @fecha de creación: 27/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 22/12/2008
* @descripción: se agrego el criterio
* @autor: Ing. Gusmary Balza
***********************************************************************************/		
	function actualizar()
	{
		try 
		{ 
			$total=	count((array)$this->usuarioeliminar);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->usuarioeliminar[$contador]->criterio[0]['operador'] = "AND";
				$this->usuarioeliminar[$contador]->criterio[0]['criterio'] = "codniv";
				$this->usuarioeliminar[$contador]->criterio[0]['condicion'] = "=";
				$this->usuarioeliminar[$contador]->criterio[0]['valor'] = "'".$this->codniv."'";
				
				$this->usuarioeliminar[$contador]->criterio[1]['operador'] = "AND";
				$this->usuarioeliminar[$contador]->criterio[1]['criterio'] = "codasiniv";
				$this->usuarioeliminar[$contador]->criterio[1]['condicion'] = "=";
				$this->usuarioeliminar[$contador]->criterio[1]['valor'] = "'".$this->codasiniv."'";
				
				$this->usuarioeliminar[$contador]->criterio[2]['operador'] = "AND";
				$this->usuarioeliminar[$contador]->criterio[2]['criterio'] = "codusu";
				$this->usuarioeliminar[$contador]->criterio[2]['condicion'] = "=";
				$this->usuarioeliminar[$contador]->criterio[2]['valor'] = "'".$this->usuarioeliminar[$contador]->codusu."'";

				$this->usuarioeliminar[$contador]->criterio[1]['operador'] = "AND";
				$this->usuarioeliminar[$contador]->criterio[1]['criterio'] = "codtipniv";
				$this->usuarioeliminar[$contador]->criterio[1]['condicion'] = "=";
				$this->usuarioeliminar[$contador]->criterio[1]['valor'] = "'".$this->codtipniv."'";
				
				$this->usuarioeliminar[$contador]->eliminarTodos();				
			}
			$total=	count((array)$this->admin);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->admin[$contador]->incluirNivelesUsuarios();
			}
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Modificar el permiso del nivel de aprobacion '.$this->codasiniv.' para el usuario '.$this->codusu.' en el Nivel '.$this->codniv.' '.$this->conexionbd->ErrorMsg();
		}
		$this->incluirSeguridad('MODIFICAR',$this->valido);	
	}
	
	
/***********************************************************************************
* @Función que elimina los permisos asignados a usuarios  
* @parametros: 
* @retorno:
* @fecha de creación: 27/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 22/12/2008
* @descripción: se agrego el criterio
* @autor: Ing. Gusmary Balza
***********************************************************************************/			
	function eliminarTodos()
	{
		try
		{
			$consulta = "DELETE FROM {$this->_table} ".
						" WHERE codemp='{$this->codemp}'";
			$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
            }
            $consulta.= $cadena;            
            $result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al eliminar el Nivel de Aprobacion '.$this->codintper.' al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
		}
		$this->incluirSeguridad('ELIMINAR',$this->valido);		
	}
		
	
/***********************************************************************************
* @Función que busca los usuarios de un personal
* @parametros: 
* @retorno:
* @fecha de creación: 24/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
************************************************************************************/
	public function obtenerUsuarios()
	{
		try 
		{
			$this->valido  = true;	
			$consulta = " SELECT {$this->_table}.codusu, sss_usuarios.nomusu, sss_usuarios.apeusu, 1 as valido ".
						"   FROM {$this->_table} ".
						"  INNER JOIN  sss_usuarios  ".
						"	  ON {$this->_table}.codusu = sss_usuarios.codusu ".
						"  WHERE {$this->_table}.codasiniv = '{$this->codasiniv}' ".
						"	 AND {$this->_table}.codniv='{$this->codniv}' ".
						"	 AND {$this->_table}.codtipniv = '{$this->codtipniv}' ".
						"  GROUP BY {$this->_table}.codusu,sss_usuarios.nomusu,sss_usuarios.apeusu ".
						"  ORDER BY {$this->_table}.codusu,sss_usuarios.nomusu,sss_usuarios.apeusu ";
			
			$result = $this->conexionbd->Execute($consulta);
			return $result;
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al consultar los usuarios del Personal '.$consulta.' '.$this->conexionbd->ErrorMsg();
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
****************************************************************************************/
	function incluirSeguridad($evento,$tipotransaccion)
	{
		if ($this->seguridad==true)
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
}	
?>