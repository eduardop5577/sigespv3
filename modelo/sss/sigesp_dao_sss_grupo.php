<?php
/***********************************************************************************
* @Clase para Manejar  para la definición de Grupo
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
require_once('sigesp_dao_sss_permisosinternos.php');
require_once('sigesp_dao_sss_derechosusuario.php');

class Grupo extends DaoGenerico
{
	public $valido=true;
	public $existe=true;
	public $seguridad=true;
	public $mensaje;
	public $cadena;
	public $criterio;
	public $codsis;
	public $nomfisico;
	public $admin = array();
	public $usuarioeliminar = array();
	public $personal = array();
	public $constante = array();
	public $nomina = array();
	public $unidad = array();
	public $estpre = array();
	public $almacen = array();
	public $centrocos = array();

	public $derechos;
	var $grupopersonal = array();
	var $grupoconstante = array();
	var $gruponomina = array();
	var $grupounidad = array();
	var $grupoestpre = array();
	var $grupoalmacen = array();
	var $grupocentrocos = array();	
	var $grupodetalle = array();
	private $conexionbd;

	public function __construct()
    {
		parent::__construct ( 'sss_grupos' );
		$this->conexionbd = $this->obtenerConexionBd(); 
	}

/***********************************************************************************
* @Función para insertar un grupo.
* @parametros: 
* @retorno:
* @fecha de creación: 30/09/2008.
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	public function incluirLocal()
	{
		$this->mensaje='Incluyo el Grupo '.$this->nomgru;
		$this->conexionbd->StartTrans();
		try 
		{ 
			$consulta = " INSERT INTO {$this->_table} ".
						"	(codemp,nomgru,nota,estatus) ".
						" 	values ('{$this->codemp}','{$this->nomgru}','{$this->nota}',1)";
			$result = $this->conexionbd->Execute($consulta);
			
			$total=	count((array)$this->admin);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->admin[$contador]->codemp = $this->codemp;
				$this->admin[$contador]->codsis = $this->codsis;
				$this->admin[$contador]->nomfisico = $this->nomfisico;
				$this->admin[$contador]->nomgru = $this->nomgru;				
				$this->admin[$contador]->incluirLocal();
			}
			$total = count((array)$this->personal);
			for ($i=0; $i < $total; $i++)
			{	
				$this->personal[$i]->codemp = $this->codemp;
				$this->personal[$i]->nomfisico = $this->nomfisico;	
				$this->personal[$i]->incluirPermisosInternos();
					
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
				
			$objPermisos = new PermisosInternos();	
			$objPermisos->codemp = $this->codemp;
			$objPermisos->nomgru = $this->nomgru;
			$objPermisos->nomfisico = $this->nomfisico;
			$objPermisos->incluirPermisosInternosGrupos();
			unset($objPermisos);			
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Incluir el Grupo '.$this->nomgru.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}
	
	
/***********************************************************************************
* @Función que Actualiza un grupo
* @parametros: 
* @retorno:
* @fecha de creación: 07/08/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function modificarLocal()
	{
		$this->mensaje='Modifico el Grupo '.$this->nomgru;
		$this->conexionbd->StartTrans();
		try 
		{ 			
			$consulta = " UPDATE {$this->_table} ".
						"    SET nota = '{$this->nota}', ".
						"        estatus=1 ".
						"  WHERE nomgru='{$this->nomgru}'";
			$result = $this->conexionbd->Execute($consulta);	
			
			$total=	count((array)$this->usuarioeliminar);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->usuarioeliminar[$contador]->codemp = $this->codemp;
				$this->usuarioeliminar[$contador]->nomgru = $this->nomgru;				
				$this->usuarioeliminar[$contador]->codsis = $this->codsis;
				$this->usuarioeliminar[$contador]->nomfisico = $this->nomfisico;
				$this->usuarioeliminar[$contador]->eliminarLocal();
				$objPermisos = new PermisosInternos();	
				$objPermisos->codemp = $this->usuarioeliminar[$contador]->codemp;
				$objPermisos->codusu = $this->usuarioeliminar[$contador]->codusu;
				$objPermisos->nomfisico = $this->nomfisico;
				$objPermisos->nomgru = $this->usuarioeliminar[$contador]->nomgru;
				$objPermisos->eliminarPermisosInternosGrupos();
				unset($objPermisos);
				$objPerfil = new DerechosUsuario();	
				$objPerfil->codemp = $this->usuarioeliminar[$contador]->codemp;
				$objPerfil->codusu = $this->usuarioeliminar[$contador]->codusu;
				$objPerfil->nomfisico = $this->nomfisico;
				$objPerfil->nomgru = $this->usuarioeliminar[$contador]->nomgru;
				$objPerfil->eliminarDerechosGrupos();
				unset($objPerfil);
			}
			$total=	count((array)$this->admin);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->admin[$contador]->codemp = $this->codemp;
				$this->admin[$contador]->codsis = $this->codsis;
				$this->admin[$contador]->nomfisico = $this->nomfisico;
				$this->admin[$contador]->nomgru = $this->nomgru;				
				$this->admin[$contador]->incluirLocal();
			}
			$total=	count((array)$this->grupopersonal);
			for ($i=0; $i < $total; $i++)
			{	
				$this->grupopersonal[$i]->codemp = $this->codemp;
				$this->grupopersonal[$i]->nomfisico = $this->nomfisico;
				
				$this->grupopersonal[$i]->criterio[0]['operador'] = "AND";
				$this->grupopersonal[$i]->criterio[0]['criterio'] = "nomgru";
				$this->grupopersonal[$i]->criterio[0]['condicion'] = "=";
				$this->grupopersonal[$i]->criterio[0]['valor'] = "'".$this->nomgru."'";
							
				$this->grupopersonal[$i]->criterio[1]['operador'] = "AND";
				$this->grupopersonal[$i]->criterio[1]['criterio'] = "codsis";
				$this->grupopersonal[$i]->criterio[1]['condicion'] = "=";
				$this->grupopersonal[$i]->criterio[1]['valor'] = "'".$this->grupopersonal[$i]->codsis."'";
				
				$this->grupopersonal[$i]->criterio[2]['operador'] = "AND";
				$this->grupopersonal[$i]->criterio[2]['criterio'] = "codintper";
				$this->grupopersonal[$i]->criterio[2]['condicion'] = "=";
				$this->grupopersonal[$i]->criterio[2]['valor'] = "'".$this->grupopersonal[$i]->codintper."'";
				
				$this->grupopersonal[$i]->iniciartransaccion=false;
				$this->grupopersonal[$i]->eliminarLocal();
			}
			$total=	count((array)$this->grupoconstante);
			for ($i=0; $i < $total; $i++)
			{	
				$this->grupoconstante[$i]->codemp = $this->codemp;
				$this->grupoconstante[$i]->nomfisico = $this->nomfisico;

				$this->grupoconstante[$i]->criterio[0]['operador'] = "AND";
				$this->grupoconstante[$i]->criterio[0]['criterio'] = "nomgru";
				$this->grupoconstante[$i]->criterio[0]['condicion'] = "=";
				$this->grupoconstante[$i]->criterio[0]['valor'] = "'".$this->nomgru."'";
				
				$this->grupoconstante[$i]->criterio[1]['operador'] = "AND";
				$this->grupoconstante[$i]->criterio[1]['criterio'] = "codsis";
				$this->grupoconstante[$i]->criterio[1]['condicion'] = "=";
				$this->grupoconstante[$i]->criterio[1]['valor'] = "'".$this->grupoconstante[$i]->codsis."'";
				
				$this->grupoconstante[$i]->criterio[2]['operador'] = "AND";
				$this->grupoconstante[$i]->criterio[2]['criterio'] = "codintper";
				$this->grupoconstante[$i]->criterio[2]['condicion'] = "=";
				$this->grupoconstante[$i]->criterio[2]['valor'] = "'".$this->grupoconstante[$i]->codintper."'";
				
				$this->grupoconstante[$i]->iniciartransaccion=false;
				$this->grupoconstante[$i]->eliminarLocal();
			}
			$total=	count((array)$this->gruponomina);
			for ($i=0; $i < $total; $i++)
			{	
				$this->gruponomina[$i]->codemp = $this->codemp;
				$this->gruponomina[$i]->nomfisico = $this->nomfisico;

				$this->gruponomina[$i]->criterio[0]['operador'] = "AND";
				$this->gruponomina[$i]->criterio[0]['criterio'] = "nomgru";
				$this->gruponomina[$i]->criterio[0]['condicion'] = "=";
				$this->gruponomina[$i]->criterio[0]['valor'] = "'".$this->nomgru."'";
				
				$this->gruponomina[$i]->criterio[1]['operador'] = "AND";
				$this->gruponomina[$i]->criterio[1]['criterio'] = "codsis";
				$this->gruponomina[$i]->criterio[1]['condicion'] = "=";
				$this->gruponomina[$i]->criterio[1]['valor'] = "'".$this->gruponomina[$i]->codsis."'";
				
				$this->gruponomina[$i]->criterio[2]['operador'] = "AND";
				$this->gruponomina[$i]->criterio[2]['criterio'] = "codintper";
				$this->gruponomina[$i]->criterio[2]['condicion'] = "=";
				$this->gruponomina[$i]->criterio[2]['valor'] = "'".$this->gruponomina[$i]->codintper."'";
								
				$this->gruponomina[$i]->iniciartransaccion=false;
				$this->gruponomina[$i]->eliminarLocal();
			}
			$total=	count((array)$this->grupounidad);
			for ($i=0; $i < $total; $i++)
			{	
				$this->grupounidad[$i]->codemp = $this->codemp;
				$this->grupounidad[$i]->nomfisico = $this->nomfisico;

				$this->grupounidad[$i]->criterio[0]['operador'] = "AND";
				$this->grupounidad[$i]->criterio[0]['criterio'] = "nomgru";
				$this->grupounidad[$i]->criterio[0]['condicion'] = "=";
				$this->grupounidad[$i]->criterio[0]['valor'] = "'".$this->nomgru."'";
				
				$this->grupounidad[$i]->criterio[1]['operador'] = "AND";
				$this->grupounidad[$i]->criterio[1]['criterio'] = "codsis";
				$this->grupounidad[$i]->criterio[1]['condicion'] = "=";
				$this->grupounidad[$i]->criterio[1]['valor'] = "'".$this->grupounidad[$i]->codsis."'";
				
				$this->grupounidad[$i]->criterio[2]['operador'] = "AND";
				$this->grupounidad[$i]->criterio[2]['criterio'] = "codintper";
				$this->grupounidad[$i]->criterio[2]['condicion'] = "=";
				$this->grupounidad[$i]->criterio[2]['valor'] = "'".$this->grupounidad[$i]->codintper."'";
				
				$this->grupounidad[$i]->iniciartransaccion=false;
				$this->grupounidad[$i]->eliminarLocal();
			}
			$total=	count((array)$this->grupoestpre);
			for ($i=0; $i < $total; $i++)
			{	
				$this->grupoestpre[$i]->codemp = $this->codemp;
				$this->grupoestpre[$i]->nomfisico = $this->nomfisico;

				$this->grupoestpre[$i]->criterio[0]['operador'] = "AND";
				$this->grupoestpre[$i]->criterio[0]['criterio'] = "nomgru";
				$this->grupoestpre[$i]->criterio[0]['condicion'] = "=";
				$this->grupoestpre[$i]->criterio[0]['valor'] = "'".$this->nomgru."'";
				
				$this->grupoestpre[$i]->criterio[1]['operador'] = "AND";
				$this->grupoestpre[$i]->criterio[1]['criterio'] = "codsis";
				$this->grupoestpre[$i]->criterio[1]['condicion'] = "=";
				$this->grupoestpre[$i]->criterio[1]['valor'] = "'".$this->grupoestpre[$i]->codsis."'";
								
				$this->grupoestpre[$i]->criterio[2]['operador'] = "AND";
				$this->grupoestpre[$i]->criterio[2]['criterio'] = "codintper";
				$this->grupoestpre[$i]->criterio[2]['condicion'] = "=";
				$this->grupoestpre[$i]->criterio[2]['valor'] = "'".$this->grupoestpre[$i]->codintper."'";
				
				$this->grupoestpre[$i]->iniciartransaccion=false;
				$this->grupoestpre[$i]->eliminarLocal();
			}
			$total=	count((array)$this->grupoalmacen);
			for ($i=0; $i < $total; $i++)
			{	
				$this->grupoalmacen[$i]->codemp = $this->codemp;
				$this->grupoalmacen[$i]->nomfisico = $this->nomfisico;
								
				$this->grupoalmacen[$i]->criterio[0]['operador'] = "AND";
				$this->grupoalmacen[$i]->criterio[0]['criterio'] = "nomgru";
				$this->grupoalmacen[$i]->criterio[0]['condicion'] = "=";
				$this->grupoalmacen[$i]->criterio[0]['valor'] = "'".$this->nomgru."'";
				
				$this->grupoalmacen[$i]->criterio[1]['operador'] = "AND";
				$this->grupoalmacen[$i]->criterio[1]['criterio'] = "codsis";
				$this->grupoalmacen[$i]->criterio[1]['condicion'] = "=";
				$this->grupoalmacen[$i]->criterio[1]['valor'] = "'".$this->grupoalmacen[$i]->codsis."'";
				
				$this->grupoalmacen[$i]->criterio[2]['operador'] = "AND";
				$this->grupoalmacen[$i]->criterio[2]['criterio'] = "codintper";
				$this->grupoalmacen[$i]->criterio[2]['condicion'] = "=";
				$this->grupoalmacen[$i]->criterio[2]['valor'] = "'".$this->grupoalmacen[$i]->codintper."'";
								
				$this->grupoalmacen[$i]->iniciartransaccion=false;
				$this->grupoalmacen[$i]->eliminarLocal();
			}
			$total=	count((array)$this->grupocentrocos);
			for ($i=0; $i < $total; $i++)
			{	
				$this->grupocentrocos[$i]->codemp = $this->codemp;
				$this->grupocentrocos[$i]->nomfisico = $this->nomfisico;
								
				$this->grupocentrocos[$i]->criterio[0]['operador'] = "AND";
				$this->grupocentrocos[$i]->criterio[0]['criterio'] = "nomgru";
				$this->grupocentrocos[$i]->criterio[0]['condicion'] = "=";
				$this->grupocentrocos[$i]->criterio[0]['valor'] = "'".$this->nomgru."'";
				
				$this->grupocentrocos[$i]->criterio[1]['operador'] = "AND";
				$this->grupocentrocos[$i]->criterio[1]['criterio'] = "codsis";
				$this->grupocentrocos[$i]->criterio[1]['condicion'] = "=";
				$this->grupocentrocos[$i]->criterio[1]['valor'] = "'".$this->grupocentrocos[$i]->codsis."'";
				
				$this->grupocentrocos[$i]->criterio[2]['operador'] = "AND";
				$this->grupocentrocos[$i]->criterio[2]['criterio'] = "codintper";
				$this->grupocentrocos[$i]->criterio[2]['condicion'] = "=";
				$this->grupocentrocos[$i]->criterio[2]['valor'] = "'".$this->grupocentrocos[$i]->codintper."'";
								
				$this->grupocentrocos[$i]->iniciartransaccion=false;
				$this->grupocentrocos[$i]->eliminarLocal();
			}
			$total = count((array)$this->personal);
			for ($i=0; $i<$total; $i++)
			{	
				$this->personal[$i]->codemp = $this->codemp;
				$this->personal[$i]->nomfisico = $this->nomfisico;
				$this->personal[$i]->incluirPermisosInternos();
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
			$objPermisos = new PermisosInternos();	
			$objPermisos->codemp = $this->codemp;
			$objPermisos->nomgru = $this->nomgru;
			$objPermisos->nomfisico = $this->nomfisico;
			$objPermisos->actualizarPermisosInternosGrupos();
			$objPermisos->incluirPermisosInternosGrupos();
			unset($objPermisos);			
			$objPerfil = new DerechosUsuario();	
			$objPerfil->codemp = $this->codemp;
			$objPerfil->nomfisico = $this->nomfisico;
			$objPerfil->nomgru = $this->nomgru;
			$objPerfil->modificarDerechosGrupos();
			unset($objPerfil);
			$objPerfil = new DerechosUsuario();	
			$objPerfil->codemp = $this->codemp;
			$objPerfil->nomgru = $this->nomgru;
			$objPerfil->nomfisico = $this->nomfisico;
			$objPerfil->incluirDerechosGrupos();
			unset($objPerfil);
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Modificar el Grupo '.$this->nomgru.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);
	}
	
	
/***********************************************************************************
* @Función que Elimina un grupo
* @parametros: 
* @retorno:
* @fecha de creación: 07/08/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function eliminarLocal()
	{
		$this->mensaje='Elimino el Grupo '.$this->nomgru;
		$this->conexionbd->StartTrans(); 
		try 
		{ 
			$objPerfil = new DerechosUsuario();	
			$objPerfil->codemp = $this->codemp;
			$objPerfil->nomfisico = $this->nomfisico;
			$objPerfil->nomgru = $this->nomgru;
			$objPerfil->eliminarTodosDerechosGrupos();
			unset($objPerfil);

			$this->usuarioeliminar[0]->codemp = $this->codemp;
			$this->usuarioeliminar[0]->codsis = $this->codsis;
			$this->usuarioeliminar[0]->nomfisico = $this->nomfisico;
			$this->usuarioeliminar[0]->nomgru = $this->nomgru;							
			$this->usuarioeliminar[0]->eliminarTodos();
					

			$objPermisos = new PermisosInternos();	
			$objPermisos->codemp = $this->codemp;
			$objPermisos->codusu = $this->codusu;
			$objPermisos->nomfisico = $this->nomfisico;
			$objPermisos->nomgru = $this->nomgru;
			$objPermisos->eliminarTodosPermisosInternosGrupos();
			unset($objPermisos);

			$this->grupodetalle[0]->codemp = $this->codemp;
			$this->grupodetalle[0]->nomgru = $this->nomgru;
			$this->grupodetalle[0]->nomfisico = $this->nomfisico;
			$this->grupodetalle[0]->fisicamente=true;	
			$this->grupodetalle[0]->criterio[0]['operador'] = "AND";
			$this->grupodetalle[0]->criterio[0]['criterio'] = "nomgru";
			$this->grupodetalle[0]->criterio[0]['condicion'] = "=";
			$this->grupodetalle[0]->criterio[0]['valor'] = "'".$this->nomgru."'";
			$this->grupodetalle[0]->eliminarLocal();	
			
			$objPerfil = new DerechosGrupo();	
			$objPerfil->codemp = $this->codemp;
			$objPerfil->nomgru = $this->nomgru;
			$objPerfil->nomfisico = $this->nomfisico;
			$objPerfil->criterio[0]['operador'] = "AND";
			$objPerfil->criterio[0]['criterio'] = "nomgru";
			$objPerfil->criterio[0]['condicion'] = "=";
			$objPerfil->criterio[0]['valor'] = "'".$this->nomgru."'";
			$objPerfil->eliminarFisicamente();		
			unset($objPerfil);
			
			$consulta = "DELETE FROM {$this->_table} ". 
						" WHERE nomgru='{$this->nomgru}'";
			$result = $this->conexionbd->Execute($consulta);
		} 
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje=' Error al Eliminar el Grupo '.$this->nomgru.' '.$this->conexionbd->ErrorMsg();
	   	} 
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('ELIMINAR',$this->valido);
	}
		
	
/***********************************************************************************
* @Función que Busca uno o todos grupo
* @parametros: 
* @retorno:
* @fecha de creación: 07/08/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
**************************************************************************************/		
	public function leer() 
 	{		
		try 
		{ 
			$consulta = " SELECT codemp,nomgru,nota, 1 as valido ".
						" FROM {$this->_table} WHERE nomgru<>'-----' ".
						" AND codemp='$this->codemp' AND estatus=1";
			$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
            }
            $consulta.= $cadena;
			
		  	$consulta.= " ORDER BY nomgru";
			$result = $this->conexionbd->Execute($consulta);
			if ($result->EOF)
			{		
				$this->existe = false;		
			}			
			return $result;
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Grupo '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
	   	} 
 	}
	
	
/***********************************************************************************
* @Función que busca los usuarios de un grupo
* @parametros: 
* @retorno:
* @fecha de creación: 06/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	public function obtenerUsuarios()
	{
		try 
		{ 
			$consulta = " SELECT {$this->_table}.nomgru, sss_usuarios.codusu, sss_usuarios.nomusu,".
						"  		sss_usuarios.apeusu, sss_usuarios.email, 1 as valido ".
						"  FROM {$this->_table} ".
						" INNER JOIN  (sss_usuarios_en_grupos ".
						"      INNER JOIN sss_usuarios  ".
						"   	   ON sss_usuarios.codemp = sss_usuarios_en_grupos.codemp ".
						"         AND sss_usuarios.codusu = sss_usuarios_en_grupos.codusu) ".
						"    ON {$this->_table}.codemp = sss_usuarios_en_grupos.codemp ".
						"   AND {$this->_table}.nomgru = sss_usuarios_en_grupos.nomgru ".
						" WHERE {$this->_table}.nomgru = '{$this->nomgru}' ".
						"   AND {$this->_table}.codemp = '{$this->codemp}' ";
			$result = $this->conexionbd->Execute($consulta);
			return $result;
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al consultar los usuarios del Grupo '.$consulta.' '.$this->conexionbd->ErrorMsg();
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
		$objEvento->objNotificacion->sistema=$this->codsis;
		$objEvento->objNotificacion->tipo=$tiponotificacion;
		$objEvento->objNotificacion->titulo='DEFINICIÓN DE GRUPO';
		$objEvento->objNotificacion->usuario=$_SESSION['la_logusr'];
		$objEvento->objNotificacion->operacion=$this->mensaje;
		$objEvento->objNotificacion->enviarNotificacion();
		unset($objEvento);
	}
}
?>