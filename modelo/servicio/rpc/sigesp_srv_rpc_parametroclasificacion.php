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
require_once ($dirsrvrpc."/modelo/servicio/rpc/sigesp_srv_rpc_iparametroclasificacion.php");
require_once ($dirsrvrpc."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirsrvrpc."/base/librerias/php/general/sigesp_lib_relaciones.php");

class servicioParametroClasificacion implements iparametroclasificacion
{
	private $daoParametroClasificacion;
	
	public function __construct() 
	{
		$this->daoParametroClasificacion = null;
		$this->mensaje='';
		$this->valido=true;	
	}
	
	public function buscarCodigoParametro($codemp)
	{
		$this->daoParametroClasificacion = FabricaDao::CrearDAO("N", "rpc_clasificacion");
		$this->daoParametroClasificacion->codemp = $codemp;
		$codigo = $this->daoParametroClasificacion->buscarCodigoSinPrefijo("codclas",true,2);
		unset($this->daoParametroClasificacion);
		return $codigo;
	}
	
	public function buscarParametros($codemp)
	{
		$this->daoParametroClasificacion = FabricaDao::CrearDAO("N", "rpc_clasificacion");
		$data = $this->daoParametroClasificacion->leerTodos("codclas",0,$codemp);
		unset($this->daoParametroClasificacion);
		return $data;
	}

	public function guardarParametro($codemp,$objson,$arrevento)
	{		
		//obteniendo las instacias de los dao's
		$this->daoParametroClasificacion = FabricaDao::CrearDAO("N", "rpc_clasificacion");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoParametroClasificacion->setData($objson);
		$this->daoParametroClasificacion->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//insertando el registro y escribiendo en el log
		if($this->daoParametroClasificacion->incluir())
		{
			$this->valido=true;
		}
		else
		{
			$this->valido=false;
			$this->mensaje .= $this->daoParametroClasificacion->ErrorMsg;
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
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoParametroClasificacion);
		return $this->valido;
	}
		
	public function modificarParametro($codemp,$objson,$arrevento)
	{
		//obteniendo las instacias de los dao's
		$this->daoParametroClasificacion = FabricaDao::CrearDAO("N", "rpc_clasificacion");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoParametroClasificacion->setData($objson);
		$this->daoParametroClasificacion->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//modificando el registro y escribiendo en el log
		if($this->daoParametroClasificacion->modificar())
		{
			$this->valido=true;
		}
		else
		{
			$this->valido=false;
			$this->mensaje .= $this->daoParametroClasificacion->ErrorMsg;
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
		unset($this->daoParametroClasificacion);
		return $this->valido;
	}

	public function eliminarParametro($codemp,$objson,$arrevento)
	{
		DaoGenerico::iniciarTrans();
		//obteniendo las instacias de los dao's
		$this->daoParametroClasificacion = FabricaDao::CrearDAO("N", "rpc_clasificacion");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoParametroClasificacion->setData($objson);
		$this->daoParametroClasificacion->codemp=$codemp;
		$relaciones = new servicioRelaciones();
		$condicion="AND  column_name='codclas'";
		$tabla= 'rpc_clasificacion';
		$valor=$this->daoParametroClasificacion->codclas;
		$mensaje='';
		if(!$relaciones->verificarRelaciones($condicion,$tabla,$valor,$mensaje))
		{
			if($this->daoParametroClasificacion->eliminar())
			{
				$this->valido=true;
			}
			else
			{
				$this->valido=false;
				$this->mensaje .= $this->daoParametroClasificacion->ErrorMsg;
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
			$this->mensaje .= 'El Parámetro de Clasificacion está asociado a proveedores, no puede ser Eliminado';
		}
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoParametroClasificacion);
		unset($relaciones);			
		return $this->valido;
	}
}
?>