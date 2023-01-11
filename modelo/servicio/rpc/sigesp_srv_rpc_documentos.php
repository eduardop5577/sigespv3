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
require_once ($dirsrvrpc."/modelo/servicio/rpc/sigesp_srv_rpc_idocumentos.php");
require_once ($dirsrvrpc."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirsrvrpc."/base/librerias/php/general/sigesp_lib_relaciones.php");

class servicioDocumento implements idocumento
{
	private $daoDocumento;
	
	public function __construct() 
	{
		$this->daoDocumento = null;
		$this->valido=true;
		$this->mensaje='';		
	}
	
	public function buscarCodigoDocumento($codemp)
	{
		$this->daoDocumento = FabricaDao::CrearDAO("N", "rpc_documentos");
		$this->daoDocumento->codemp = $codemp;
		$codigo = $this->daoDocumento->buscarCodigoSinPrefijo("coddoc",true,3);
		unset($this->daoDocumento);
		return $codigo;
	}
	
	public function buscarDocumento($codemp)
	{
		$this->daoDocumento = FabricaDao::CrearDAO("N", "rpc_documentos");
		$data = $this->daoDocumento->leerTodos("coddoc",0,$codemp);
		unset($this->daoDocumento);
		return $data;
	}

	public function guardarDocumento($codemp,$objson,$arrevento)
	{
		//obteniendo las instacias de los dao's
		$this->daoDocumento = FabricaDao::CrearDAO("N", "rpc_documentos");		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoDocumento->setData($objson);
		$this->daoDocumento->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//insertando el registro y escribiendo en el log
		if($this->daoDocumento->incluir())
		{
			$this->valido=true;
		}
		else
		{
			$this->valido=false;
			$this->mensaje .= $this->daoDocumento->ErrorMsg;
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
		unset($this->daoDocumento);
		return $this->valido;
	}

	public function modificarDocumento($codemp,$objson,$arrevento)
	{
		//obteniendo las instacias de los dao's
		$this->daoDocumento = FabricaDao::CrearDAO("N", "rpc_documentos");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoDocumento->setData($objson);
		$this->daoDocumento->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//modificando el registro y escribiendo en el log
		if($this->daoDocumento->modificar())
		{
			$this->valido=true;
		}
		else
		{
			$this->valido=false;
			$this->mensaje .= $this->daoDocumento->ErrorMsg;
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
		unset($this->daoDocumento);
		return $this->valido;
	}

	public function eliminarDocumento($codemp,$objson,$arrevento)
	{
		//obteniendo las instacias de los dao's
		$this->daoDocumento = FabricaDao::CrearDAO("N", "rpc_documentos");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoDocumento->setData($objson);
		$this->daoDocumento->codemp=$codemp;
		$relaciones = new servicioRelaciones();
		$condicion="AND  column_name='coddoc'";
		$tabla= 'rpc_documentos';
		$valor=$this->daoDocumento->coddoc;
		$mensaje='';
		if(!$relaciones->verificarRelaciones($condicion,$tabla,$valor,$mensaje))
		{
			DaoGenerico::iniciarTrans();
		
			if($this->daoDocumento->eliminar())
			{
				$this->valido=true;
			}
			else
			{
				$this->valido=false;
				$this->mensaje .= $this->daoDocumento->ErrorMsg;
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
			$this->mensaje .= 'El Documento está asociado a proveedores, no puede ser Eliminado';
		}
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoDocumento);
		unset($relaciones);		
		return $this->valido;
	}
}
?>