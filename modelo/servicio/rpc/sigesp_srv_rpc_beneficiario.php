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
require_once ($dirsrvrpc."/modelo/servicio/rpc/sigesp_srv_rpc_ibeneficiario.php");
require_once ($dirsrvrpc."/modelo/servicio/sss/sigesp_srv_sss_evento.php");

class servicioBeneficiario implements ibeneficiario
{
	private $daoBeneficiario;
	private $conexionbd;
	
	public function __construct()
	{
		$this->daoBeneficiario = null;
		$this->conexionbd  = ConexionBaseDatos::getInstanciaConexion();
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/ibenefiacio::buscarCodigoBeneficiario()
	 */
	public function buscarCodigoBeneficiario($codemp) {
		$this->daoBeneficiario = FabricaDao::CrearDAO("N", "rpc_beneficiario");
		$this->daoBeneficiario->codemp = $codemp;
		$dataEmp = $this->daoBeneficiario->leerTodos('ced_bene',1,$codemp);
		
		unset($this->daoBeneficiario);
		return $dataEmp;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/ibenefiacio::buscarCodigoBeneficiario()
	 */
	public function buscarCodigoBeneficiarios($codemp) {
		$this->daoBeneficiario = FabricaDao::CrearDAO("N", "rpc_beneficiario");
		$this->daoBeneficiario->codemp = $codemp;
		$codigo = $this->daoBeneficiario->buscarCodigo("ced_bene",true,10);
		unset($this->daoBeneficiario);
		return $codigo;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/ibeneficiario::buscarBeneficiario()
	 */
	public function buscarBeneficiarios($as_cedbene,$as_nombene,$as_apebene) 
	{
		$cadenasql = "SELECT rpc_beneficiario.*,scg_cuentas.denominacion, ".
					 " 	(SELECT dencta FROM scb_ctabanco WHERE rpc_beneficiario.ctaban=scb_ctabanco.ctaban) as dencta, ".
					 " 	(SELECT denominacion FROM scg_cuentas WHERE rpc_beneficiario.codemp=scg_cuentas.codemp AND rpc_beneficiario.sc_cuentarecdoc=scg_cuentas.sc_cuenta) as denominacion_rec ".
					 "  FROM rpc_beneficiario  ".
					 "  LEFT JOIN  scg_cuentas ON rpc_beneficiario.codemp=scg_cuentas.codemp AND rpc_beneficiario.sc_cuenta=scg_cuentas.sc_cuenta ".
					 " 	WHERE rpc_beneficiario.codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
					 "  AND rpc_beneficiario.ced_bene like '%$as_cedbene%' ".
					 "  AND rpc_beneficiario.nombene like '%$as_nombene%' ".
		        	 "  AND rpc_beneficiario.apebene like '%$as_apebene%' ".
					 "  AND rpc_beneficiario.ced_bene <> '----------' ";
				//	echo $cadenasql;
				//	break;  
		switch (strtoupper($_SESSION["ls_gestor"])){
	   		case "MYSQLT":
				$limite="LIMIT 0,100 ";
				$cadenasql = $cadenasql."ORDER BY ced_bene ASC {$limite}";
			break;
	   		case "MYSQLI":
				$limite="LIMIT 0,100 ";
				$cadenasql = $cadenasql."ORDER BY ced_bene ASC {$limite}";
			break;
			case "POSTGRES": // POSTGRES
				$limite="LIMIT 100";
				$cadenasql = $cadenasql."ORDER BY ced_bene ASC {$limite}";
			break;
			case "OCI8PO":
				$limite="AND rownum<=100";
				$cadenasql = $cadenasql."{$limite} ORDER BY ced_bene ASC";
	   }		
	  	return $this->conexionbd->Execute ( $cadenasql );		
    }
    
	public function buscarBeneficiariosCatEmpresa($cedula, $nombre, $apellido) {
    	$concatNomApe = $this->conexionbd->Concat("nombene","' '","apebene");
        $cadenasql = "SELECT codemp, ced_bene, nombene, apebene, 
                             sc_cuenta AS scctaben, {$concatNomApe} as nomapebene ".
                     "  FROM rpc_beneficiario  ".
                     " WHERE codemp='{$_SESSION["la_empresa"]["codemp"]}' ".
                     "   AND ced_bene like '%{$cedula}%' ".
                     "   AND nombene like '%{$nombre}%' ".
                     "   AND apebene like '%{$apellido}%' ".
                     "   AND ced_bene <> '----------' ";
        
		switch (strtoupper($_SESSION["ls_gestor"])){
			case "MYSQLT":
				$cadenasql .= ' ORDER BY ced_bene ASC LIMIT 0,100';
				break;

			case "MYSQLI":
				$cadenasql .= ' ORDER BY ced_bene ASC LIMIT 0,100';
				break;
				
			case "POSTGRES":
				$cadenasql .= ' ORDER BY ced_bene ASC LIMIT 100';
				break;
				
			case "OCI8PO":
				$cadenasql .= ' AND rownum<=100 ORDER BY ced_bene ASC';
				break;
	   	}
		return $this->conexionbd->Execute ( $cadenasql );  
    }
	
	public function existeBeneficiario($codemp, $cedbene)
	{
		$existe = false;
		$conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSql         = "SELECT ced_bene ".
  							 "	FROM rpc_beneficiario ".
  							 " WHERE codemp ='{$codemp}' ".
							 "   AND ced_bene LIKE '%{$cedbene}%'";		
		$dataSet  = $conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= "  ->".$conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->_numOfRows > 0) {
				$existe = true;
			}
		}
		return $existe;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/ibeneficiario::guardarParametro()
	*/
	public function guardarBeneficiario($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
	
		//obteniendo las instacias de los dao's
		$this->daoBeneficiario = FabricaDao::CrearDAO("N", "rpc_beneficiario");
	
		//seteando la data e iniciando transaccion de base de datos
		$this->daoBeneficiario->codbansig = '---';
		$this->daoBeneficiario->setData($objson);
		$this->daoBeneficiario->codemp=$codemp;
		$this->daoBeneficiario->fecregben = convertirFechaBd($objson->fecregben);
		DaoGenerico::iniciarTrans();
	
		//insertando el registro y escribiendo en el log
		$this->daoBeneficiario->incluir();
	
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];

		//completando la transaccion retorna 1 si no hay errores
		if (DaoGenerico::completarTrans()) {
			$resultado = 1;
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento(); 		
		}
		 else{
			$arrevento ['desevetra'] = $this->daoBeneficiario->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
	
		//liberando variables y retornando el resultado de la operacion
		
		unset($this->daoBeneficiario);	
    	return $resultado;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/ibeneficiario::modificarParametro()
	*/
	public function modificarBeneficiario($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
	
		//obteniendo las instacias de los dao's
		$this->daoBeneficiario = FabricaDao::CrearDAO("N", "rpc_beneficiario");
	
		//seteando la data e iniciando transaccion de base de datos
		$this->daoBeneficiario->codbansig = '---';
		$this->daoBeneficiario->setData($objson);
		$this->daoBeneficiario->codemp=$codemp;
		$this->daoBeneficiario->fecregben = convertirFechaBd($objson->fecregben);
		DaoGenerico::iniciarTrans();
	
		//modificando el registro y escribiendo en el log
		$respuesta = $this->daoBeneficiario->modificar();
		
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];
		
	
		//completando la transaccion retorna 1 si no hay errores
	if (DaoGenerico::completarTrans()) {
			$resultado = 1;
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento(); 		
		}
		 else{
			$arrevento ['desevetra'] = $this->daoBeneficiario->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
	
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoBeneficiario);
		return $resultado;
	}
	
	public function eliminarDto($dto,$codemp) {
		$this->daogenerico = FabricaDao::CrearDAO("N", "rpc_beneficiario");
		$this->daogenerico->codemp = $codemp;
		$this->daogenerico->setData($dto);
		$errorNo = '';
		$this->pasarDatos ( $dto );
		try {
			if(!$this->daogenerico->eliminar ('ced_bene',$dto->ced_bene)){
				if($this->daogenerico->errorValidacion){
					$errorNo = '-1';
				}
			}
		} catch (Exception $e) {
			return false;
		}
					
		return $errorNo;		
	}
	
	public function pasarDatos($ObJson) {
		$arratributos = $this->daogenerico->getAttributeNames();
		foreach ( $arratributos as $IndiceDAO ) {
			foreach ( $ObJson as $IndiceJson => $valorJson ) {
				if ($IndiceJson == $IndiceDAO && $IndiceJson != "codemp") {
					$this->daogenerico->$IndiceJson = utf8_decode ( $valorJson );
				} 
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/ibeneficiario::eliminarParametro()
	*/
	public function eliminarBeneficiario($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
	
		$existe = $this->eliminarDto($objson, $codemp);
			if($existe==''){
			//obteniendo las instacias de los dao's
			$this->daoBeneficiario = FabricaDao::CrearDAO("N", "rpc_beneficiario");
		
			//seteando la data e iniciando transaccion de base de datos
			$this->daoBeneficiario->codbansig = '---';
			$this->daoBeneficiario->setData($objson);
			$this->daoBeneficiario->codemp=$codemp;
			DaoGenerico::iniciarTrans();
			$this->daoBeneficiario->eliminar();
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrevento['evento'];
			$servicioEvento->codemp=$arrevento['codemp'];
			$servicioEvento->codsis=$arrevento['codsis'];
			$servicioEvento->nomfisico=$arrevento['nomfisico'];
			$servicioEvento->desevetra=$arrevento['desevetra'];
		
			//completando la transaccion retorna 1 si no hay errores
			if (DaoGenerico::completarTrans()) {
				$resultado = 1;
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento(); 		
			}
			else{
				$arrevento ['desevetra'] = $this->daoBeneficiario->ErrorMsg();
				$servicioEvento->tipoevento=false;
				$servicioEvento->desevetra=$arrevento['desevetra'];
				$servicioEvento->incluirEvento();
			}
		}
		else{
			if($respuesta='-1'){
				$resultado = 2;
			}
		}
	
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoBeneficiario);
		return $resultado;
	}
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/ibeneficiario::buscarBeneficiario()
	*/
	public function buscarBanco($codemp) {
		$cadenasql = "SELECT * 
						FROM scb_banco 
						WHERE codemp= '{$codemp}'";	
		return $this->conexionbd->Execute($cadenasql);	
	}
	public function buscarTipoCuenta($codemp) {
		$cadenasql = "SELECT *
		           		FROM scb_tipocuenta
						WHERE codemp= '{$codemp}'";
						return $this->conexionbd->Execute($cadenasql);
	}
	
	public function buscarBeneficiarioDeduccionesDisp($ced_bene)
	{
		$cadenasql="SELECT rpc_deduxbene.codded, sigesp_deducciones.dended  ".
				" FROM sigesp_deducciones, rpc_deduxbene ".
				" WHERE rpc_deduxbene.codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
				" AND sigesp_deducciones.codded<>'-----'   ".
				" AND rpc_deduxbene.ced_bene = '".$ced_bene."' ".
				" AND rpc_deduxbene.codded = sigesp_deducciones.codded ".
				" ORDER BY rpc_deduxbene.codded ASC";  
			return $this->conexionbd->Execute($cadenasql);
	}
	
	public function buscarBenDeduccionesDisp($ced_bene)
	{
		$cadenasql=" SELECT codded, dended ".
				" FROM sigesp_deducciones ".
				" WHERE codded<>'---' ".
				" AND codded NOT IN (SELECT codded ".
								"	FROM rpc_deduxbene ".
								"	WHERE rpc_deduxbene.codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
								"	AND rpc_deduxbene.ced_bene = '".$ced_bene."' ".
								"	AND rpc_deduxbene.codded = sigesp_deducciones.codded) ".
				" ORDER BY codded ASC"; 

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}

public function eliminarDedxbenef($ced_bene)
	{
		$cadenasql=" DELETE ".
				   " FROM rpc_deduxbene ".
				   " WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
				   " AND ced_bene='".$ced_bene."'";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}

public function guardarBeneficiarioDeducciones($codemp, $arrjson, $ced_bene, $arrEvento){
		$resultado = '0';
		DaoGenerico::iniciarTrans();
		$this->eliminarDedxbenef($ced_bene);
		$dedInsertar = $arrjson->arrDedIncluir;
		$numFueInc = count((array)$dedInsertar);
		for ($i = 0; $i < $numFueInc; $i++) {
			$this->daoBeneficiario = FabricaDao::CrearDAO('N','rpc_deduxbene');
			$this->daoBeneficiario->setData($dedInsertar[$i]);
			$this->daoBeneficiario->codemp = $codemp;
			$this->daoBeneficiario->ced_bene = $ced_bene;
			if(!$this->daoBeneficiario->incluir(false,'',false,0,true)){
				break;
			}
			else {
				$servicioEvento = new ServicioEvento();
				$servicioEvento->evento=$arrevento['evento'];
				$servicioEvento->codemp=$arrevento['codemp'];
				$servicioEvento->codsis=$arrevento['codsis'];
				$servicioEvento->nomfisico=$arrevento['nomfisico'];
				$servicioEvento->desevetra=$arrevento['desevetra'];
			}
			unset($this->daoBeneficiario);
		}
		
		if (DaoGenerico::completarTrans()) {
			$resultado = '1';
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento(); 		
		}
		 else{
			$arrevento ['desevetra'] = $this->daoBeneficiario->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }

		return $resultado;
	}
	
	public function buscarRifBen($rifben)
	{
		$cadenasql=" SELECT rifben ".
				" FROM rpc_beneficiario ".
				" WHERE rifben='".$rifben."' ";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		$rif=$resultado->fields['rifben'];
		unset($conexionbd);
		unset($resultado);
		return trim($rif);
	}
	
	public function buscarCedBen($cedben)
	{
		$cadenasql=" SELECT ced_bene ".
				" FROM rpc_beneficiario ".
				" WHERE ced_bene='".$cedben."' ";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		$ced=$resultado->fields['ced_bene'];
		unset($conexionbd);
		unset($resultado);
		return trim($ced);
	}	
}
		
	
?>