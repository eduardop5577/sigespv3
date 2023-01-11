<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirsrvrpc = "";
$dirsrvrpc = dirname(__FILE__);
$dirsrvrpc = str_replace("\\","/",$dirsrvrpc);
$dirsrvrpc = str_replace("/modelo/servicio/rpc","",$dirsrvrpc);
require_once ($dirsrvrpc."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once ($dirsrvrpc."/modelo/servicio/rpc/sigesp_srv_rpc_iespecialidad.php");
require_once ($dirsrvrpc."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirsrvrpc."/base/librerias/php/general/sigesp_lib_relaciones.php");

class servicioEspecialidad implements iespecialidad
{
	private $daoEspecialidad;
	private $conexionbd;
	
	public function __construct() 
	{
		$this->daoEspecialidad = null;	
		$this->conexionbd  = ConexionBaseDatos::getInstanciaConexion();
		$this->valido=true;
		$this->mensaje='';		
	}

	public function buscarCodigoEspecialidad($codemp)
	{
		$this->daoEspecialidad = FabricaDao::CrearDAO("N", "rpc_especialidad");
		$codigo = $this->daoEspecialidad->buscarCodigoSinPrefijo("codesp",false,3);
		unset($this->daoEspecialidad);
		return $codigo;
	}
	
	public function buscarEspecialidad()
	{
		$this->daoEspecialidad = FabricaDao::CrearDAO("N", "rpc_especialidad");
		$cadena ="SELECT * FROM rpc_especialidad WHERE codesp<>'---'";
		$data = $this->daoEspecialidad->buscarSql($cadena);
		unset($this->daoEspecialidad);
		return $data;
	}
	
	public function guardarEspecialidad($codemp,$objson,$arrevento)
	{
		//obteniendo las instacias de los dao's
		$this->daoEspecialidad = FabricaDao::CrearDAO("N", "rpc_especialidad");		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoEspecialidad->setData($objson);
		$this->daoEspecialidad->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//insertando el registro y escribiendo en el log
		if($this->daoEspecialidad->incluir())
		{
			$this->valido=true;
		}
		else
		{
			$this->valido=false;
			$this->mensaje .= $this->daoEspecialidad->ErrorMsg;
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];
		if (DaoGenerico::completarTrans($this->valido))
		{
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento(); 		
		}
		else
		{
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
		}
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoEspecialidad);
		return $this->valido;
	}
		
	public function modificarEspecialidad($codemp,$objson,$arrevento)
	{
		//obteniendo las instacias de los dao's
		$this->daoEspecialidad = FabricaDao::CrearDAO("N", "rpc_especialidad");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoEspecialidad->setData($objson);
		$this->daoEspecialidad->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//modificando el registro y escribiendo en el log
		if($this->daoEspecialidad->modificar())
		{
			$this->valido=true;
		}
		else
		{
			$this->valido=false;
			$this->mensaje .= $this->daoEspecialidad->ErrorMsg;
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];
		if (DaoGenerico::completarTrans($this->valido))
		{
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento();
		}
		else
		{
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
		}
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoEspecialidad);
		return $this->valido;
	}

	public function eliminarEspecialidad($codemp,$objson,$arrevento)
	{
		DaoGenerico::iniciarTrans();
		
		//obteniendo las instacias de los dao's
		$this->daoEspecialidad = FabricaDao::CrearDAO("N", "rpc_especialidad");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoEspecialidad->setData($objson);
		$this->daoEspecialidad->codemp=$codemp;
		$relaciones = new servicioRelaciones();
		$condicion="AND  column_name='codesp'";
		$tabla= 'rpc_especialidad';
		$valor=$this->daoEspecialidad->codesp;
		$mensaje='';
		if(!$relaciones->verificarRelaciones($condicion,$tabla,$valor,$mensaje))
		{
			if($this->daoEspecialidad->eliminar())
			{
				$this->valido=true;
			}
			else
			{
				$this->valido=false;
				$this->mensaje .= $this->daoEspecialidad->ErrorMsg;
			}		
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrevento['evento'];
			$servicioEvento->codemp=$arrevento['codemp'];
			$servicioEvento->codsis=$arrevento['codsis'];
			$servicioEvento->nomfisico=$arrevento['nomfisico'];
			$servicioEvento->desevetra=$arrevento['desevetra'];
			//completando la transaccion retorna 1 si no hay errores
			if (DaoGenerico::completarTrans($this->valido))
			{
				$resultado = 1;
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
			}
			else
			{
				$servicioEvento->tipoevento=false;
				$servicioEvento->desevetra=$this->mensaje;
				$servicioEvento->incluirEvento();
			}
		}	
		else
		{
			$this->valido=false;
			$this->mensaje .= 'La especialidad está asociada a proveedores, no puede ser Eliminada';
		}
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoEspecialidad);
		unset($relaciones);			
		return $this->valido;
	}
}
?>