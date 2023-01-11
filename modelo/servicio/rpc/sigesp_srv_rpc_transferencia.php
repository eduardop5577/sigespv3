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
require_once ($dirsrvrpc."/modelo/servicio/rpc/sigesp_srv_rpc_itransferencia.php");
require_once ($dirsrvrpc."/modelo/servicio/sss/sigesp_srv_sss_evento.php");

class servicioTransferencia implements itransferencia
{
	private $daoTransferencia;
	private $daoRegistroEvento;
	private $daoRegistroFalla;
	private $conexionbd;
	public  $mensaje; 
	public  $valido; 
	
	public function __construct()
	{
		$this->daoTransferencia = null;
		$this->daoRegistroEvento = null;
		$this->daoRegistroFalla  = null;
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos  = ConexionBaseDatos::getInstanciaConexion();
	}

	public function buscarFiltroPersonal($codemp,$cedperdes,$cedperhas)
	{
		$cadenaSql ="SELECT codemp,cedper,codpai,codest,codmun,codpar,coreleper,nacper,nomper,apeper,dirper,telhabper,telmovper, ".
  			   		"	    (SELECT MAX(codban) ".
				    "          FROM sno_personalnomina ".
				    "         WHERE sno_personalnomina.codemp = sno_personal.codemp ".
				    "		   AND sno_personalnomina.codper = sno_personal.codper ".
				    "		 GROUP BY sno_personalnomina.codper) AS codban, ".
	  			    "	   (SELECT MAX(codcueban) ".
				    "          FROM sno_personalnomina ".
				    "         WHERE sno_personalnomina.codemp = sno_personal.codemp ".
				    "		   AND sno_personalnomina.codper = sno_personal.codper ".
				    "		 GROUP BY sno_personalnomina.codper) AS ctaban ".
	                "  FROM sno_personal ".
				    " WHERE codemp='".$codemp."' ".
					"   AND estper='1' ".
					"   AND CAST(trim(cedper) AS INT) >= ".$cedperdes."".
					"   AND CAST(trim(cedper) AS INT) <= ".$cedperhas."".
					"   AND cedper NOT IN (SELECT ced_bene FROM rpc_beneficiario WHERE codemp = '".$codemp."')".
					" ORDER BY CAST(trim(cedper) AS INT) ASC LIMIT 150";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $dataSet;
	}
		
	public function trasferirPersonalBeneficiario($codemp, $arrjson, $arrevento)
	{
		DaoGenerico::iniciarTrans();
		$personal = $arrjson->arrPersonal;
		$numEstInc = count((array)$personal);
		$servicioEvento = new ServicioEvento();
		for ($i = 0; $i < $numEstInc; $i++)
		{
			$this->daoTransferencia = FabricaDao::CrearDAO('N','rpc_beneficiario');
			$this->daoTransferencia->setData($personal[$i]);
			$this->daoTransferencia->codemp = $codemp;
			if(!$this->daoTransferencia->incluir(false,'',false,0,true))
			{
				$this->mensaje .= '  ->'.$this->daoTransferencia->ErrorMsg();
				$this->valido = false;
				break;
			}
			else
			{
				$servicioEvento->desevetra .= "Inserto el personal {$personal[$i]->ced_bene} - {$personal[$i]->nombene} como beneficiario, ";
			}
			unset($this->daoTransferencia);
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
	
	public function transferirTodos($codemp, $sc_cuenta, $arrevento) {
		$cadenaSQL = "INSERT INTO rpc_beneficiario(codemp, ced_bene, codpai, codest, codmun, codpar, email, 
            									   nacben, nombene, apebene, dirbene, telbene, celbene, sc_cuenta, codbansig, codban, ctaban)
						SELECT codemp,cedper,codpai,codest,codmun,codpar,coreleper,nacper,nomper,apeper,dirper,telhabper,telmovper,
							   '{$sc_cuenta}' AS sc_cuenta, '---' AS codbansig,
								(SELECT MAX(codban) FROM sno_personalnomina  
									WHERE sno_personalnomina.codemp = sno_personal.codemp AND sno_personalnomina.codper = sno_personal.codper 
				    		 		GROUP BY sno_personalnomina.codper) AS codban, 
	  			    	   (SELECT MAX(codcueban) 
				              FROM sno_personalnomina 
				             WHERE sno_personalnomina.codemp = sno_personal.codemp 
				    		   AND sno_personalnomina.codper = sno_personal.codper 
				    		 GROUP BY sno_personalnomina.codper) AS ctaban 
	                  FROM sno_personal 
				     WHERE codemp='{$codemp}'   
					   AND codper IN (SELECT MAX(codper) AS codper
										FROM sno_personal 
										WHERE codemp='{$codemp}' AND estper='1'
										AND cedper NOT IN (SELECT ced_bene FROM rpc_beneficiario WHERE codemp = '0001')
										GROUP BY codemp,cedper)";
		if ($this->conexionBaseDatos->Execute ( $cadenaSQL ) === false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		if ($this->valido)
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