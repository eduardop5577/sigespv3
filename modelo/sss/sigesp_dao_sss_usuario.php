<?php
/*******************************************************************************
* @Clase compartida para manejar la definición de Uusario
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
**********************************************************************
* @fecha modificacion  
* @autor  
* @descripcion  
*********************************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once('sigesp_dao_sss_registroeventos.php');
require_once('sigesp_dao_sss_registrofallas.php');

class Usuario extends DaoGenerico
{
	public $mensaje;
	public $evento;
	public $valido = true;
	public $existe = true;
	public $seguridad = true;
	public $cadena;
	public $criterio;
	public $nuevopassword;
	public $codsis;
	public $nomfisico;
	public $admin = array();
	public $usuarioeliminar = array();
	public $constante = array();
	public $nomina = array();
	public $unidad = array();
	public $estpre = array();
	public $almacen = array();
	public $centrocos = array();
	public $cuentabanco = array();
	public $derechos;
	public $iniciosession = 1;	
	var $usuariopersonal = array();
	var $usuarioconstante = array();
	var $usuarionomina = array();
	var $usuariounidad = array();
	var $usuarioestpre = array();
	var $usuarioalmacen = array();
	var $usuariocentrocos = array();
	var $usuariocuentabanco = array();
	var $usuariodetalle = array();
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
		parent::__construct ( 'sss_usuarios' );
		$this->conexionbd = $this->obtenerConexionBd(); //$this->conexionbd->debug=true;
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
		if ($this->tipoconexionbd != 'DEFECTO')
		{
			$this->conexionbd = conectarBD($this->servidor, $this->usuario, $this->clave, $this->basedatos, $this->gestor, $this->puerto);
		}
	}	
	
	//viene del proceso de traspaso de usuarios
	public function iniciarTransaccion()
	{
		$this->conexionbd->StartTrans();
	} 
	
	
	public function completarTransaccion()
	{
		$this->conexionbd->CompleteTrans();		
	}
	
	
/***********************************************************************************
* @Función que inserta los detalles para un usuario.
* @parametros: 
* @retorno: 
* @fecha de creación: 30/09/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function incluirLocal()
	{
		$this->seleccionarConexion();		
		$this->conexionbd->StartTrans();
		$this->mensaje = 'Incluyo el Usuario '.$this->codusu;
		try 
		{ 
			$consulta = "INSERT INTO sss_usuarios(codemp, codusu, cedusu, nomusu, apeusu, pwdusu,telusu, nota,email, estatus, admusu, ".
						" ultingusu, fotousu,estblocon,actusu,blkusu,fecblousu, fecnacusu) VALUES ('".$this->codemp."','".$this->codusu."','".$this->cedusu."',".
						" '".$this->nomusu."','".$this->apeusu."','".$this->pwdusu."','".$this->telusu."','".$this->nota."','".$this->email."',		".
						"".$this->estatus.",".$this->admusu.",'1900-01-01','".$this->fotousu."','".$this->estblocon."',0,0,'1900-01-01','".$this->fecnacusu."')";	
			$result = $this->conexionbd->Execute($consulta);
						
			$total = count((array)$this->admin);
			for ($i=0; $i < $total; $i++)
			{	
				$this->admin[$i]->codemp = $this->codemp;
				$this->admin[$i]->nomfisico = $this->nomfisico;	
				$this->admin[$i]->incluirPermisosInternos();
			}
			$total = count((array)$this->constante);
			for ($i=0; $i < $total; $i++)
			{				
				$this->constante[$i]->codemp = $this->codemp;
				$this->constante[$i]->nomfisico = $this->nomfisico;	
				$this->constante[$i]->incluirPermisosInternos();
			}
			$total = count((array)$this->nomina);
			for ($i=0; $i < $total; $i++)
			{				
				$this->nomina[$i]->codemp = $this->codemp;
				$this->nomina[$i]->nomfisico = $this->nomfisico;	
				$this->nomina[$i]->incluirPermisosInternos();	
			}				
			$total = count((array)$this->unidad);
			for ($i=0; $i < $total; $i++)
			{				
				$this->unidad[$i]->codemp = $this->codemp;
				$this->unidad[$i]->nomfisico = $this->nomfisico;	
				$this->unidad[$i]->incluirPermisosInternos();
			}
			$total = count((array)$this->estpre);
			for ($i=0; $i < $total; $i++)
			{				
				$this->estpre[$i]->codemp = $this->codemp;
				$this->estpre[$i]->nomfisico = $this->nomfisico;	
				$this->estpre[$i]->incluirPermisosInternos();	
			}
			$total = count((array)$this->almacen);
			for ($i=0; $i < $total; $i++)
			{				
				$this->almacen[$i]->codemp = $this->codemp;
				$this->almacen[$i]->nomfisico = $this->nomfisico;	
				$this->almacen[$i]->incluirPermisosInternos();	
			}
			$total = count((array)$this->centrocos);
			for ($i=0; $i < $total; $i++)
			{				
				$this->centrocos[$i]->codemp = $this->codemp;
				$this->centrocos[$i]->nomfisico = $this->nomfisico;	
				$this->centrocos[$i]->incluirPermisosInternos();	
			}
			$total = count((array)$this->cuentabanco);
			for ($i=0; $i < $total; $i++)
			{				
				$this->cuentabanco[$i]->codemp = $this->codemp;
				$this->cuentabanco[$i]->nomfisico = $this->nomfisico;	
				$this->cuentabanco[$i]->incluirPermisosInternos();	
			}
		}
		catch (exception $e) 
		{	
			$this->valido  = false;	
			$this->mensaje='Error al Incluir el Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}	
	
	
/***********************************************************************************
* @Función que actualiza los detalles de un Usuario.
* @parametros: 
* @retorno: 
* @fecha de creación: 30/09/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function modificarLocal()
	{
		$this->mensaje='Modifico el Usuario '.$this->codusu;
		$this->conexionbd->StartTrans();
		try 
		{ 
			$consulta = " UPDATE {$this->_table} ".
						"    SET cedusu='{$this->cedusu}',".
						"        nomusu='{$this->nomusu}', ".
						"        apeusu='{$this->apeusu}',".
						"        fecnacusu='{$this->fecnacusu}',".
						"        telusu='{$this->telusu}',".
						"        email='{$this->email}', ".
						"        estatus={$this->estatus},".
						"        admusu={$this->admusu},".
						"        estblocon={$this->estblocon},".
						"        nota='{$this->nota}' ".
						" WHERE codemp='{$this->codemp}' ".
						"   AND codusu='{$this->codusu}'";
			$result = $this->conexionbd->Execute($consulta);
			
			$total=	count((array)$this->usuariopersonal);
			for ($i=0; $i < $total; $i++)
			{	
				$this->usuariopersonal[$i]->codemp = $this->codemp;
				$this->usuariopersonal[$i]->nomfisico = $this->nomfisico;
			
				$this->usuariopersonal[$i]->criterio[0]['operador'] = "AND";
				$this->usuariopersonal[$i]->criterio[0]['criterio'] = "codusu";
				$this->usuariopersonal[$i]->criterio[0]['condicion'] = "=";
				$this->usuariopersonal[$i]->criterio[0]['valor'] = "'".$this->codusu."'";
				
				$this->usuariopersonal[$i]->criterio[1]['operador'] = "AND";
				$this->usuariopersonal[$i]->criterio[1]['criterio'] = "codsis";
				$this->usuariopersonal[$i]->criterio[1]['condicion'] = "=";
				$this->usuariopersonal[$i]->criterio[1]['valor'] = "'".$this->usuariopersonal[$i]->codsis."'";
				
				$this->usuariopersonal[$i]->criterio[2]['operador'] = "AND";
				$this->usuariopersonal[$i]->criterio[2]['criterio'] = "codintper";
				$this->usuariopersonal[$i]->criterio[2]['condicion'] = "=";
				$this->usuariopersonal[$i]->criterio[2]['valor'] = "'".$this->usuariopersonal[$i]->codintper."'";
				
				$this->usuariopersonal[$i]->eliminarTodos();
			}
			$total=	count((array)$this->usuarioconstante);
			for ($i=0; $i < $total; $i++)
			{	
				$this->usuarioconstante[$i]->codemp = $this->codemp;
				$this->usuarioconstante[$i]->nomfisico = $this->nomfisico;
				
				$this->usuarioconstante[$i]->criterio[0]['operador'] = "AND";
				$this->usuarioconstante[$i]->criterio[0]['criterio'] = "codusu";
				$this->usuarioconstante[$i]->criterio[0]['condicion'] = "=";
				$this->usuarioconstante[$i]->criterio[0]['valor'] = "'".$this->codusu."'";
				
				$this->usuarioconstante[$i]->criterio[1]['operador'] = "AND";
				$this->usuarioconstante[$i]->criterio[1]['criterio'] = "codsis";
				$this->usuarioconstante[$i]->criterio[1]['condicion'] = "=";
				$this->usuarioconstante[$i]->criterio[1]['valor'] = "'".$this->usuarioconstante[$i]->codsis."'";
				
				$this->usuarioconstante[$i]->criterio[2]['operador'] = "AND";
				$this->usuarioconstante[$i]->criterio[2]['criterio'] = "codintper";
				$this->usuarioconstante[$i]->criterio[2]['condicion'] = "=";
				$this->usuarioconstante[$i]->criterio[2]['valor'] = "'".$this->usuarioconstante[$i]->codintper."'";
							
				$this->usuarioconstante[$i]->eliminarTodos();
			}
			$total=	count((array)$this->usuarionomina);
			for ($i=0; $i < $total; $i++)
			{	
				$this->usuarionomina[$i]->codemp = $this->codemp;
				$this->usuarionomina[$i]->nomfisico = $this->nomfisico;
								
				$this->usuarionomina[$i]->criterio[0]['operador'] = "AND";
				$this->usuarionomina[$i]->criterio[0]['criterio'] = "codusu";
				$this->usuarionomina[$i]->criterio[0]['condicion'] = "=";
				$this->usuarionomina[$i]->criterio[0]['valor'] = "'".$this->codusu."'";
				
				$this->usuarionomina[$i]->criterio[1]['operador'] = "AND";
				$this->usuarionomina[$i]->criterio[1]['criterio'] = "codsis";
				$this->usuarionomina[$i]->criterio[1]['condicion'] = "=";
				$this->usuarionomina[$i]->criterio[1]['valor'] = "'".$this->usuarionomina[$i]->codsis."'";
				
				$this->usuarionomina[$i]->criterio[2]['operador'] = "AND";
				$this->usuarionomina[$i]->criterio[2]['criterio'] = "codintper";
				$this->usuarionomina[$i]->criterio[2]['condicion'] = "=";
				$this->usuarionomina[$i]->criterio[2]['valor'] = "'".$this->usuarionomina[$i]->codintper."'";
								
				$this->usuarionomina[$i]->eliminarTodos();
			}
			$total=	count((array)$this->usuariounidad);
			for ($i=0; $i < $total; $i++)
			{	
				$this->usuariounidad[$i]->codemp = $this->codemp;
				$this->usuariounidad[$i]->nomfisico = $this->nomfisico;
							
				$this->usuariounidad[$i]->criterio[0]['operador'] = "AND";
				$this->usuariounidad[$i]->criterio[0]['criterio'] = "codusu";
				$this->usuariounidad[$i]->criterio[0]['condicion'] = "=";
				$this->usuariounidad[$i]->criterio[0]['valor'] = "'".$this->codusu."'";
				
				$this->usuariounidad[$i]->criterio[1]['operador'] = "AND";
				$this->usuariounidad[$i]->criterio[1]['criterio'] = "codsis";
				$this->usuariounidad[$i]->criterio[1]['condicion'] = "=";
				$this->usuariounidad[$i]->criterio[1]['valor'] = "'".$this->usuariounidad[$i]->codsis."'";
				
				$this->usuariounidad[$i]->criterio[2]['operador'] = "AND";
				$this->usuariounidad[$i]->criterio[2]['criterio'] = "codintper";
				$this->usuariounidad[$i]->criterio[2]['condicion'] = "=";
				$this->usuariounidad[$i]->criterio[2]['valor'] = "'".$this->usuariounidad[$i]->codintper."'";
								
				$this->usuariounidad[$i]->eliminarTodos();
			}
			$total=	count((array)$this->usuarioestpre);
			for ($i=0; $i < $total; $i++)
			{	
				$this->usuarioestpre[$i]->codemp = $this->codemp;
				$this->usuarioestpre[$i]->nomfisico = $this->nomfisico;
								
				$this->usuarioestpre[$i]->criterio[0]['operador'] = "AND";
				$this->usuarioestpre[$i]->criterio[0]['criterio'] = "codusu";
				$this->usuarioestpre[$i]->criterio[0]['condicion'] = "=";
				$this->usuarioestpre[$i]->criterio[0]['valor'] = "'".$this->codusu."'";
				
				$this->usuarioestpre[$i]->criterio[1]['operador'] = "AND";
				$this->usuarioestpre[$i]->criterio[1]['criterio'] = "codsis";
				$this->usuarioestpre[$i]->criterio[1]['condicion'] = "=";
				$this->usuarioestpre[$i]->criterio[1]['valor'] = "'".$this->usuarioestpre[$i]->codsis."'";
				
				$this->usuarioestpre[$i]->criterio[2]['operador'] = "AND";
				$this->usuarioestpre[$i]->criterio[2]['criterio'] = "codintper";
				$this->usuarioestpre[$i]->criterio[2]['condicion'] = "=";
				$this->usuarioestpre[$i]->criterio[2]['valor'] = "'".$this->usuarioestpre[$i]->codintper."'";
								
				$this->usuarioestpre[$i]->eliminarTodos();
			}
			$total=	count((array)$this->usuarioalmacen);
			for ($i=0; $i < $total; $i++)
			{	
				$this->usuarioalmacen[$i]->codemp = $this->codemp;
				$this->usuarioalmacen[$i]->nomfisico = $this->nomfisico;
								
				$this->usuarioalmacen[$i]->criterio[0]['operador'] = "AND";
				$this->usuarioalmacen[$i]->criterio[0]['criterio'] = "codusu";
				$this->usuarioalmacen[$i]->criterio[0]['condicion'] = "=";
				$this->usuarioalmacen[$i]->criterio[0]['valor'] = "'".$this->codusu."'";
				
				$this->usuarioalmacen[$i]->criterio[1]['operador'] = "AND";
				$this->usuarioalmacen[$i]->criterio[1]['criterio'] = "codsis";
				$this->usuarioalmacen[$i]->criterio[1]['condicion'] = "=";
				$this->usuarioalmacen[$i]->criterio[1]['valor'] = "'".$this->usuarioalmacen[$i]->codsis."'";
				
				$this->usuarioalmacen[$i]->criterio[2]['operador'] = "AND";
				$this->usuarioalmacen[$i]->criterio[2]['criterio'] = "codintper";
				$this->usuarioalmacen[$i]->criterio[2]['condicion'] = "=";
				$this->usuarioalmacen[$i]->criterio[2]['valor'] = "'".$this->usuarioalmacen[$i]->codintper."'";
								
				$this->usuarioalmacen[$i]->eliminarTodos();
			}
			$total=	count((array)$this->usuariocentrocos);
			for ($i=0; $i < $total; $i++)
			{	
				$this->usuariocentrocos[$i]->codemp = $this->codemp;
				$this->usuariocentrocos[$i]->nomfisico = $this->nomfisico;
								
				$this->usuariocentrocos[$i]->criterio[0]['operador'] = "AND";
				$this->usuariocentrocos[$i]->criterio[0]['criterio'] = "codusu";
				$this->usuariocentrocos[$i]->criterio[0]['condicion'] = "=";
				$this->usuariocentrocos[$i]->criterio[0]['valor'] = "'".$this->codusu."'";
				
				$this->usuariocentrocos[$i]->criterio[1]['operador'] = "AND";
				$this->usuariocentrocos[$i]->criterio[1]['criterio'] = "codsis";
				$this->usuariocentrocos[$i]->criterio[1]['condicion'] = "=";
				$this->usuariocentrocos[$i]->criterio[1]['valor'] = "'".$this->usuariocentrocos[$i]->codsis."'";
				
				$this->usuariocentrocos[$i]->criterio[2]['operador'] = "AND";
				$this->usuariocentrocos[$i]->criterio[2]['criterio'] = "codintper";
				$this->usuariocentrocos[$i]->criterio[2]['condicion'] = "=";
				$this->usuariocentrocos[$i]->criterio[2]['valor'] = "'".$this->usuariocentrocos[$i]->codintper."'";
								
				$this->usuariocentrocos[$i]->eliminarTodos();
			}
			$total=	count((array)$this->usuariocuentabanco);
			for ($i=0; $i < $total; $i++)
			{	
				$this->usuariocuentabanco[$i]->codemp = $this->codemp;
				$this->usuariocuentabanco[$i]->nomfisico = $this->nomfisico;
								
				$this->usuariocuentabanco[$i]->criterio[0]['operador'] = "AND";
				$this->usuariocuentabanco[$i]->criterio[0]['criterio'] = "codusu";
				$this->usuariocuentabanco[$i]->criterio[0]['condicion'] = "=";
				$this->usuariocuentabanco[$i]->criterio[0]['valor'] = "'".$this->codusu."'";
				
				$this->usuariocuentabanco[$i]->criterio[1]['operador'] = "AND";
				$this->usuariocuentabanco[$i]->criterio[1]['criterio'] = "codsis";
				$this->usuariocuentabanco[$i]->criterio[1]['condicion'] = "=";
				$this->usuariocuentabanco[$i]->criterio[1]['valor'] = "'".$this->usuariocuentabanco[$i]->codsis."'";
				
				$this->usuariocuentabanco[$i]->criterio[2]['operador'] = "AND";
				$this->usuariocuentabanco[$i]->criterio[2]['criterio'] = "codintper";
				$this->usuariocuentabanco[$i]->criterio[2]['condicion'] = "=";
				$this->usuariocuentabanco[$i]->criterio[2]['valor'] = "'".$this->usuariocuentabanco[$i]->codintper."'";
								
				$this->usuariocuentabanco[$i]->eliminarTodos();
			}
			$total = count((array)$this->admin);
			for ($i=0; $i<$total; $i++)
			{	
				$this->admin[$i]->codemp = $this->codemp;
				$this->admin[$i]->nomfisico = $this->nomfisico;
				$this->admin[$i]->incluirPermisosInternos();
			}
			$total = count((array)$this->constante);
			for ($i=0; $i<$total; $i++)
			{	
				$this->constante[$i]->codemp = $this->codemp;
				$this->constante[$i]->nomfisico = $this->nomfisico;
				$this->constante[$i]->incluirPermisosInternos();
			}
			$total = count((array)$this->nomina);
			for ($i=0; $i<$total; $i++)
			{	
				$this->nomina[$i]->codemp = $this->codemp;
				$this->nomina[$i]->nomfisico = $this->nomfisico;
				$this->nomina[$i]->incluirPermisosInternos();
			}
			$total = count((array)$this->unidad);
			for ($i=0; $i<$total; $i++)
			{	
				if($i==0)
					$this->unidad[$i]->incluirDefecto();
				$this->unidad[$i]->codemp = $this->codemp;
				$this->unidad[$i]->nomfisico = $this->nomfisico;
				$this->unidad[$i]->incluirPermisosInternos();
			}
			$total = count((array)$this->estpre);
			for ($i=0; $i<$total; $i++)
			{	
				$this->estpre[$i]->codemp = $this->codemp;
				$this->estpre[$i]->nomfisico = $this->nomfisico;
				$this->estpre[$i]->incluirPermisosInternos();
			}
			$total = count((array)$this->almacen);
			for ($i=0; $i<$total; $i++)
			{	
				$this->almacen[$i]->codemp = $this->codemp;
				$this->almacen[$i]->nomfisico = $this->nomfisico;
				$this->almacen[$i]->incluirPermisosInternos();
			}
			$total = count((array)$this->centrocos);
			for ($i=0; $i<$total; $i++)
			{	
				$this->centrocos[$i]->codemp = $this->codemp;
				$this->centrocos[$i]->nomfisico = $this->nomfisico;
				$this->centrocos[$i]->incluirPermisosInternos();
			}
			$total = count((array)$this->cuentabanco);
			for ($i=0; $i<$total; $i++)
			{	
				$this->cuentabanco[$i]->codemp = $this->codemp;
				$this->cuentabanco[$i]->nomfisico = $this->nomfisico;
				$this->cuentabanco[$i]->incluirPermisosInternos();
			}
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Modificar el Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);
	}
	
	
/****************************************************************************
* @Función que elimina un usuario actualizando su estatus a suspendido
* @parametros: 
* @retorno:
* @fecha de creación: 06/08/2008
* @autor: Ing. Gusmary Balza
* ************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*****************************************************************************/		
	function eliminarEvento()
	{
		$operacion='';
		$this->conexionbd->StartTrans();
		try 
		{
			$this->usuariodetalle[0]->codemp = $this->codemp;
			$this->usuariodetalle[0]->codusu = $this->codusu;
			$this->usuariodetalle[0]->nomfisico = $this->nomfisico;
			
			$this->usuariodetalle[0]->criterio[0]['operador']  = " AND";
			$this->usuariodetalle[0]->criterio[0]['criterio']  = "codusu";
			$this->usuariodetalle[0]->criterio[0]['condicion'] = "=";
			$this->usuariodetalle[0]->criterio[0]['valor']     = "'".$this->codusu."'";
			if ($this->verificaRegistroEvento())
			{
				$this->usuariodetalle[0]->eliminarTodos();	
	
				$consulta = " UPDATE {$this->_table} ".
							"    SET estatus=3 ".
							"  WHERE codemp='{$this->codemp}' ".
							"    AND codusu='{$this->codusu}'";
				$result = $this->conexionbd->Execute($consulta);
				$this->mensaje='Suspendió el Usuario '.$this->codusu.', Ya que tiene registros asocidos.';
				$operacion='MODIFICAR';
			}
			else
			{
				$this->usuariodetalle[0]->eliminarFisicamente();	
	
				$consulta = " DELETE FROM {$this->_table} ".
							"  WHERE codemp='{$this->codemp}' ".
							"    AND codusu='{$this->codusu}'";
				$result = $this->conexionbd->Execute($consulta);
				$this->mensaje='Éliminó el Usuario '.$this->codusu.'.';
				$operacion='ELIMINAR';
			}
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje=' Error al Eliminar el Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
	   	} 
	   	$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad($operacion,$this->valido);	
	}	
	
	
/****************************************************************************
* @Función que busca un usuario
* @parametros: 
* @retorno:
* @fecha de creación: 06/08/2008
* @autor: Ing. Gusmary Balza
****************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
******************************************************************************/		
	function leer() 
 	{		
		try
		{
			$consulta = " SELECT codemp,codusu,cedusu,nomusu,apeusu,pwdusu,fecnacusu,telusu, 	".
						" 		 email,estatus,admusu,ultingusu,nota,estblocon,1 as valido 				".
						"   FROM {$this->_table} 												".
						"  WHERE codemp='{$this->codemp}' 										".
						"    AND codusu<>'--------------------'									";
			$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
            }
            $consulta.= $cadena;
            $consulta.= "ORDER BY UPPER(codusu)";
			$result = $this->conexionbd->Execute($consulta);
			if(!$result->EOF)
			{
				$this->existe = true;
			}
			else
			{
				$this->existe = false;
			}
			return $result;
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Usuario '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);			
		}
 		   	
 	} 

	
/***********************************************************************************
* @Función que verifica que los datos del usuario sean correctos.
* @parametros: 
* @retorno: 
* @fecha de creación: 17/07/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	function verificarUsuario()
	{
		if ($this->verificarBloqueo())
		{
			$this->valido = false;
			$this->mensaje = 'Usuario bloqueado: Contacte al administrador del sistema';
		}
		else
		{									
			$consulta = " SELECT codusu, cedusu, nomusu, apeusu, pwdusu, ultingusu ".
					    "   FROM {$this->_table} ".
					    "  WHERE codemp='".$this->codemp."'".
					    "    AND codusu='".$this->codusu."' ".
					    "    AND pwdusu='".$this->pwdusu."' ". 
					    "    AND estatus=1";
			$result = $this->conexionbd->Execute($consulta);
			if($result===false)
			{
				$this->valido = false;
				$this->mensaje = 'Ocurrio un error: '.$this->conexionbd->ErrorMsg();
			}
			else
			{
				if (!$result->EOF)
				{	
					if($result->fields['ultingusu']=='1900-01-01')
					{
						$this->iniciosession = 0;
					}
					else
					{
						$this->iniciosession = 1;
					}
					$this->actualizarAcceso();
					$this->validarNumeroLog();
					$_SESSION['la_cedusu']=$result->fields['cedusu'];
					$_SESSION['la_nomusu']=$result->fields['nomusu'];
					$_SESSION['la_apeusu']=$result->fields['apeusu'];
					$_SESSION['la_codusu']=$result->fields['codusu'];
					$_SESSION['la_pasusu']=$result->fields['pwdusu'];
					$_SESSION['la_logusr']=$result->fields['codusu'];
					unset($_SESSION['sigesp_intentos']);
					$this->valido = true;
				}			
				else
				{	
					if($_SESSION['bloqueo_clave']=='1')
					{
						$intentos = $_SESSION['sigesp_intentos']++;
						if ($intentos > $_SESSION['intentos_bloqueo'])
						{
							$this->bloquearUsuario();
							$this->valido = false;
						}
						else
						{	
							$this->valido = false;
							$this->mensaje = 'Usuario o password incorrectos.';
						}						
					}
					else
					{	
						$this->valido = false;
						$this->mensaje = 'Usuario o password incorrectos.';
					}					
				}
			}
		}
	}
	
	
	
/***********************************************************************************
* @Función que verifica si un usuario está bloqueado.
* @parametros:
* @retorno: 
* @fecha de creación: 01/08/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	function verificarBloqueo()
	{
		$bloqueado = true;
		try
		{
			$consulta = " SELECT codusu ".
				  	    "   FROM {$this->_table} ".
		 		 	    "  WHERE codemp = '".$this->codemp."' ".
				 	    "    AND codusu = '".$this->codusu."' ".
				 	    "    AND estatus = 2 ";	
			$result = $this->conexionbd->Execute($consulta);			
			if ($result->EOF)
			{
				$bloqueado = false;
			}
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al consultar el estatus del Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}
		$result->Close();
		return $bloqueado;
	}
	
	
/***********************************************************************************
* @Función que bloquea un usuario.
* @parametros: 
* @retorno: 
* @fecha de creación: 01/08/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	function bloquearUsuario()
	{
		$this->conexionbd->StartTrans();
		$this->mensaje = 'Usuario o password incorrectos.';
		try
		{
			$consulta = " UPDATE {$this->_table} ".
					    "    SET estatus=2, ".
					    "    	 fecblousu='".date('Y-m-d')."' ".
					    "  WHERE codemp = '".$this->codemp."' ".
					    "    AND estblocon = '0' ".
					    "    AND codusu = '".$this->codusu."' ";
			$result = $this->conexionbd->Execute($consulta);
			if($this->conexionbd->Affected_Rows()>0)
			{
					$this->mensaje = 'Actualizo el estatus a Bloqueado al Usuario '.$this->codusu;
			}
		}	
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al actualizar el estatus a bloqueado al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);
	}
	

/***********************************************************************************
* @Función que actualiza el último acceso de un usuario.
* @parametros: 
* @retorno: 
* @fecha de creación: 17/07/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	function actualizarAcceso()
	{
		$fecha = date('Y/m/d');
		$this->mensaje = 'Actualizo la fecha de ingreso del Usuario '.$this->codusu;
		$this->conexionbd->StartTrans();
		try 
		{
			$consulta = " UPDATE {$this->_table} ".
					    "    SET ultingusu = '".$fecha."' ".
				  	    "  WHERE codemp =  '".$this->codemp."'".
				  	    "    AND codusu = '".$this->codusu."' ";
			$result = $this->conexionbd->Execute($consulta);
			$_SESSION['la_logusr'] = $this->codusu;
		}	
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al actualizar la fecha de ingreso al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		//$this->incluirSeguridad('MODIFICAR',$this->valido);
	}
	
	
/*****************************************************************************************
* @Función que actualiza la contraseña de un usuario
* @parametros: 
* @retorno:
* @fecha de creación: 06/08/2008
* @autor: Ing. Gusmary Balza
*************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*******************************************************************************************/		
	function actualizarPassword()   //para controlador cambio de password
	{
		$this->mensaje = 'Modifico el password al usuario: '.$this->codusu;
		$this->conexionbd->StartTrans();
		try
		{
			if ($_SESSION['la_empresa']['reucon']=='1')
			{
				$li_nro=0;
				$consulta = "SELECT * ".
							"  FROM sss_usuariosdetalle ".
							" WHERE codemp='{$this->codemp}' ".
							"   AND codusu='{$this->codusu}' ".
							"ORDER BY fecreg DESC";
				$result = $this->conexionbd->Execute($consulta);
				while((!$result->EOF)&&($this->valido))
				{
					$li_nro++;
					if($li_nro<=$_SESSION['la_empresa']['nroconreu'])
					{
						$password = $result->fields["pwdusu"];
						if (trim($password)==trim($this->nuevopassword))
						{
							$this->valido=false;
							$this->mensaje = 'No se pudo cambiar el password no debe ser igual a los '.$_SESSION['la_empresa']['nroconreu'].' anteriores';
						}
					}
					else
					{
						$fecreg = $result->fields["fecreg"];
						$password = $result->fields["pwdusu"];
						$consulta = "DELETE  ".
									"  FROM sss_usuariosdetalle ".
									" WHERE codemp='{$this->codemp}' ".
									"   AND codusu='{$this->codusu}' ".
									"   AND fecreg='{$fecreg}' ".
									"   AND pwdusu='{$password}' ";
						$delete = $this->conexionbd->Execute($consulta);
					}
					$result->MoveNext();
				}
			}
			if($this->valido)
			{
				$consulta = " UPDATE {$this->_table} ".
							"    SET pwdusu='{$this->nuevopassword}' ".
							"  WHERE codemp='{$this->codemp}' ".
							"    AND codusu='{$this->codusu}'";
				$result = $this->conexionbd->Execute($consulta);

				$consulta = "INSERT INTO sss_usuariosdetalle (codemp,codusu,fecreg,pwdusu) VALUES ('{$this->codemp}','{$this->codusu}','".date("Y-m-d H:i:s")."','{$this->nuevopassword}')";
				$result = $this->conexionbd->Execute($consulta);
			}
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Modificar el password al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);
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
		$objEvento->objNotificacion->sistema=$this->codsis;
		$objEvento->objNotificacion->tipo=$tiponotificacion;
		$objEvento->objNotificacion->titulo='DEFINICIÓN DE USUARIO';
		$objEvento->objNotificacion->usuario=$_SESSION['la_logusr'];
		$objEvento->objNotificacion->operacion=$this->mensaje;
		$objEvento->objNotificacion->enviarNotificacion();
		unset($objEvento);
	}

/***********************************************************************************
* @Función que actualiza el último acceso de un usuario.
* @parametros: 
* @retorno: 
* @fecha de creación: 17/07/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	function validarNumeroLog()
	{
		$this->conexionbd->StartTrans();
		try 
		{
			$consulta = "SELECT entry 
							FROM sigesp_config
							WHERE codsis='SSS' AND seccion='RELEASE'
							  AND entry='2014_15_05'";
			$result = $this->conexionbd->Execute($consulta);
			if ($result->EOF) {
				switch($_SESSION["ls_gestor"])
		   		{
					case "MYSQLT":
		 			   $ls_sql= " ALTER TABLE sss_registro_eventos ".
		 			            " DROP PRIMARY KEY,".
		 			            " DROP COLUMN numeve,".
		 			            " ADD COLUMN numeve serial,".
		 			            " ADD CONSTRAINT pk_sss_registro_eventos PRIMARY KEY (codemp, numeve, codusu, evento, codsis, codmenu, fecevetra);";
					   break;

					case "MYSQLI":
		 			   $ls_sql= " ALTER TABLE sss_registro_eventos ".
		 			            " DROP PRIMARY KEY,".
		 			            " DROP COLUMN numeve,".
		 			            " ADD COLUMN numeve serial,".
		 			            " ADD CONSTRAINT pk_sss_registro_eventos PRIMARY KEY (codemp, numeve, codusu, evento, codsis, codmenu, fecevetra);";
					   break;
					   
					case "POSTGRES":
		 			   $ls_sql= " ALTER TABLE sss_registro_eventos DROP CONSTRAINT pk_sss_registro_eventos;
								  ALTER TABLE sss_registro_eventos DROP COLUMN numeve;
                                  ALTER TABLE sss_registro_eventos ADD COLUMN numeve serial;
                                  ALTER TABLE sss_registro_eventos ADD CONSTRAINT id_sss_registro_eventos UNIQUE (numeve);
								  ALTER TABLE sss_registro_eventos ADD CONSTRAINT pk_sss_registro_eventos PRIMARY KEY (codemp, numeve, codusu, evento, codsis, codmenu, fecevetra);";
				        break;	
					
					case "OCI8PO":
		 			   $ls_sql= " ALTER TABLE sss_registro_eventos DROP CONSTRAINT pk_sss_registro_eventos;
								  ALTER TABLE sss_registro_eventos DROP COLUMN numeve;
                                  ALTER TABLE sss_registro_eventos ADD COLUMN numeve serial;
								  ALTER TABLE sss_registro_eventos ADD CONSTRAINT pk_sss_registro_eventos PRIMARY KEY (codemp, numeve, codusu, evento, codsis, codmenu, fecevetra);";
				    break;
				}
				$result = $this->conexionbd->Execute($ls_sql);
				if ($result===false) {
					$this->valido  = false;	
					$this->mensaje='Error al actualizar la fecha de ingreso al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
				}
				else {
					$this->insertarRelease();
				}
			}
		}	
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al actualizar la fecha de ingreso al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
	}
	
/***********************************************************************************
* @Función que actualiza el último acceso de un usuario.
* @parametros: 
* @retorno: 
* @fecha de creación: 17/07/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	function insertarRelease()
	{
		$this->conexionbd->StartTrans();
		try 
		{
			$consulta = "INSERT INTO sigesp_config(codemp, codsis, seccion, entry, type, value)
    						VALUES ('0001', 'SSS', 'RELEASE', '2014_15_05', 'C', 'Cambio del campo numeve a serial')";
			$result = $this->conexionbd->Execute($consulta);
		}	
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al actualizar la fecha de ingreso al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
	}


	
/*****************************************************************************************
* @Función que verifica si el usuario tiene registro eventos
* @parametros: 
* @retorno:
* @fecha de creación: 12/01/2015
* @autor: Ing. Yesenia Moreno
*************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*******************************************************************************************/		
	function verificaRegistroEvento()   //para controlador cambio de password
	{
		$existe = false;
		$consulta = " SELECT codusu ".
					"   FROM sss_registro_eventos ".
					"  WHERE codemp='{$this->codemp}' ".
					"    AND codusu='{$this->codusu}' ".
					" UNION ".
					" SELECT codusu ".
					"   FROM sss_registro_fallas ".
					"  WHERE codemp='{$this->codemp}' ".
					"    AND codusu='{$this->codusu}' ";
		$result = $this->conexionbd->Execute($consulta);
		if ($result===false)
		{
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Registro Evento '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
		}
		else
		{
			if (!$result->EOF)	
			{
				$existe = true;
			}
		}
		return $existe;
	}
}
?>