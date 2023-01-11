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
require_once ($dirsrvrpc."/modelo/servicio/rpc/sigesp_srv_rpc_itipoempresa.php");
require_once ($dirsrvrpc."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirsrvrpc."/base/librerias/php/general/sigesp_lib_relaciones.php");

class servicioTipoempresa implements itipoempresa
{
	private $daoTipoOrganizacion;
	private $conexionbd;
	
	public function __construct()
	{
		$this->daoTipoOrganizacion = null;
		$this->conexionbd  = ConexionBaseDatos::getInstanciaConexion();
		$this->valido=true;
		$this->mensaje='';		
	}
	
	public function buscarCodigoTipoempresa($codemp)
	{
		$this->daoTipoOrganizacion = FabricaDao::CrearDAO("N", "rpc_tipo_organizacion");
		$this->daoTipoOrganizacion->codemp = $codemp;
		$codigo = $this->daoTipoOrganizacion->buscarCodigoSinPrefijo("codtipoorg",false,2);
		unset($this->daoTipoOrganizacion);
		return $codigo;
	}
	
	public function buscarTipoempresa($codemp)
	{
		$this->daoTipoOrganizacion = FabricaDao::CrearDAO("N", "rpc_tipo_organizacion");
		$data = $this->daoTipoOrganizacion->leerTodos("codtipoorg",0,false);
		unset($this->daoTipoOrganizacion);
		return $data;
	}
	
	public function guardarTipoempresa($codemp,$objson,$arrevento)
	{
		//obteniendo las instacias de los dao's
		$this->daoTipoOrganizacion = FabricaDao::CrearDAO("N", "rpc_tipo_organizacion");		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoTipoOrganizacion->setData($objson);
		$this->daoTipoOrganizacion->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//insertando el registro y escribiendo en el log
		if($this->daoTipoOrganizacion->incluir())
		{
			$this->valido=true;
		}
		else
		{
			$this->valido=false;
			$this->mensaje .= $this->daoTipoOrganizacion->ErrorMsg;
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
		unset($this->daoTipoOrganizacion);
		return $this->valido;
	}
	
	public function modificarTipoempresa($codemp,$objson,$arrevento)
	{
		//obteniendo las instacias de los dao's
		$this->daoTipoOrganizacion = FabricaDao::CrearDAO("N", "rpc_tipo_organizacion");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoTipoOrganizacion->setData($objson);
		$this->daoTipoOrganizacion->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//modificando el registro y escribiendo en el log
		if($this->daoTipoOrganizacion->modificar())
		{
			$this->valido=true;
		}
		else
		{
			$this->valido=false;
			$this->mensaje .= $this->daoTipoOrganizacion->ErrorMsg;
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
		unset($this->daoTipoOrganizacion);
		return $this->valido;
	}
	
	public function eliminarTipoempresa($codemp,$objson,$arrevento)
	{		
		//obteniendo las instacias de los dao's
		$this->daoTipoOrganizacion = FabricaDao::CrearDAO("N", "rpc_tipo_organizacion");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoTipoOrganizacion->setData($objson);
		$this->daoTipoOrganizacion->codemp=$codemp;
		$relaciones = new servicioRelaciones();
		$condicion="AND  column_name='codtipoorg'";
		$tabla= 'rpc_tipo_organizacion';
		$valor=$this->daoTipoOrganizacion->codtipoorg;
		$mensaje='';
		if(!$relaciones->verificarRelaciones($condicion,$tabla,$valor,$mensaje))
		{
			DaoGenerico::iniciarTrans();
			if($this->daoTipoOrganizacion->eliminar())
			{
				$this->valido=true;
			}
			else
			{
				$this->valido=false;
				$this->mensaje .= $this->daoTipoOrganizacion->ErrorMsg;
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
			$this->mensaje .= 'El tipo de Organizacion está asociado a proveedores, no puede ser Eliminado';
		}
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoTipoOrganizacion);
		unset($relaciones);
		return $this->valido;
	}
}
?>