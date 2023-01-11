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
require_once ($dirsrvrpc."/modelo/servicio/rpc/sigesp_srv_rpc_icambioestatus.php");
require_once ($dirsrvrpc."/modelo/servicio/sss/sigesp_srv_sss_evento.php");

class servicioCambioEstatus implements icambioestatus
{
	private $daoCambioEstatusProveedor;
	private $daoRegistroEvento;
	private $daoRegistroFalla;
	private $conexionbd;
	public  $mensaje; 
	public  $valido; 
	
	public function __construct()
	{
		$this->daoCambioEstatusProveedor = null;
		$this->daoRegistroEvento = null;
		$this->daoRegistroFalla  = null;
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionbd  = ConexionBaseDatos::getInstanciaConexion();
	}

	public function buscarProveedor($cedprov,$nomprov,$dirprov,$rifprov) 
	{
		$limite = ConexionBaseDatos::limitSIGESP();
		$nompro = ConexionBaseDatos::criterioUpperSIGESP('nompro', "'%{$as_nomprov}%'", 'LIKE');
		$dirpro = ConexionBaseDatos::criterioUpperSIGESP('dirpro', "'%{$as_dirprov}%'", 'LIKE');
		$cadenaSql = "SELECT * ".
					 "  FROM rpc_proveedor  ".
					 " WHERE codemp = '{$_SESSION["la_empresa"]["codemp"]}' ".
					 "   AND cod_pro like '%{$cedprov}%' ".
					 "   AND {$nompro} ".
					 "   AND {$dirpro} ".
					 "   AND rifpro like '%{$rifprov}%' ".	
					 "   AND cod_pro <> '----------' ".
					 " ORDER BY cod_pro ASC ";
		 			
		$dataSet  = $this->conexionbd->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $dataSet;
    }
    
	public function cargarProveedores($cod_prodesde,$cod_prohasta,$estprov) 
	{
		$limite = ConexionBaseDatos::limitSIGESP();
		$criterio='';
		if(!empty($cod_prodesde))
		{
			$criterio .= "   AND cod_pro >= '".$cod_prodesde."' ";
		}
		if(!empty($cod_prohasta))
		{
			$criterio .= "   AND cod_pro <= '".$cod_prohasta."' ";
		}
		if(!empty($estprov))
		{
			$criterio .=  "   AND estprov = '".$estprov."' ";
		}
		
		$cadenaSql = "SELECT cod_pro , nompro, estprov ".
					 "  FROM rpc_proveedor  ".
					 " WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
					 "   AND cod_pro <> '----------' ".
					 $criterio.
					 " ORDER BY cod_pro ASC ";
		$dataSet  = $this->conexionbd->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $dataSet;
	}
	
	public function actualizarEstatus($codemp,$arrProveedor,$estprovnew, $arrevento)
	{
		DaoGenerico::iniciarTrans();
		$numEstInc = count((array)$arrProveedor);
		$servicioEvento = new ServicioEvento();
		for ($i = 0; $i < $numEstInc; $i++)
		{
			$strPK = "cod_pro='{$arrProveedor[$i]->cod_pro}'";
			$this->daoCambioEstatusProveedor = FabricaDao::CrearDAO('C','rpc_proveedor',null,$strPK);
			$this->daoCambioEstatusProveedor->estprov=$estprovnew;
			if($this->daoCambioEstatusProveedor->codesp=='')
			{
				$this->daoCambioEstatusProveedor->codesp='---';
			}
			if($this->daoCambioEstatusProveedor->modificar()==0)
			{
				$this->mensaje .= '  ->'.$this->daoTransferencia->ErrorMsg();
				$this->valido = false;
				break;
			}
			else
			{
				$servicioEvento->desevetra .= "Cambio el estatus del Proveedor {$arrProveedor[$i]->cod_pro}, de {$arrProveedor[$i]->estpro} A {$estprovnew}";
			}
			unset($this->daoCambioEstatusProveedor);
		}
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
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
		unset($this->servicioEvento);
		return $this->valido;
	}
}		
?>