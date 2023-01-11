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
require_once ($dirsrvrpc."/modelo/servicio/rpc/sigesp_srv_rpc_iproveedor.php");
require_once ($dirsrvrpc."/modelo/servicio/sss/sigesp_srv_sss_evento.php");

class servicioProveedor implements iproveedor {
	private $daoProveedor;
	private $daoRegistroEvento;
	private $daoRegistroFalla;
	public  $mensaje; 
	public  $valido; 
	
	public function __construct() 
	{
		$this->daoProveedor = null;
		$this->daoRegistroEvento = null;
		$this->daoRegistroFalla  = null;
		$this->mensaje = '';
		$this->valido = true;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarCodigoOrganizacion($codemp) {
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_tipo_organizacion");
		$this->daoProveedor->codemp = $codemp;
		$dataEmp = $this->daoProveedor->leerTodos('codtipoorg',1,$codemp);
		
		unset($this->daoProveedor);
		return $dataEmp;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarCodigoProveedor($codemp)
	{
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_proveedor");
		$this->daoProveedor->codemp = $codemp;
		$codigo = $this->daoProveedor->buscarCodigo("cod_pro",true,10);
		unset($this->daoProveedor);
		return $codigo;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
		public function buscarBancos($codemp) {
		$this->daoProveedor = FabricaDao::CrearDAO("N", "scb_banco");
		$this->daoProveedor->codemp = $codemp;
		$dataBan = $this->daoProveedor->leerTodos('codban',1,$codemp);
		
		unset($this->daoProveedor);
		return $dataBan;
	}

	 /* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
		public function buscarMonedas($codemp) {
		$this->daoProveedor = FabricaDao::CrearDAO("N", "sigesp_moneda");
		$this->daoProveedor->codemp = $codemp;
		$dataMon = $this->daoProveedor->leerTodos('codmon',1,$codemp);
		
		unset($this->daoProveedor);
		return $dataMon;
	}
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarProveedor($codemp,$ls_codpro,$ls_nompro,$ls_dirpro,$ls_rifpro,$ls_fecdes,$ls_fechas) 
	{
		if (($ls_fecdes == '') && ($ls_fechas == ''))
		{
			$criterio = '';
		}
		else
		{
			$criterio = " AND a.fecvenrnc BETWEEN '%$ls_fecdes%' AND '%$ls_fechas%' ";
		}
		$cadenasql = " SELECT a.*, b.denominacion, (SELECT nomban FROM scb_banco WHERE a.codban=scb_banco.codban) as nomban, ".
					 "	(SELECT dencta FROM scb_ctabanco WHERE a.ctaban=scb_ctabanco.ctaban) as dencta, ". 
					 "	(SELECT denominacion FROM scg_cuentas WHERE a.sc_ctaant=scg_cuentas.sc_cuenta) AS denominacion_2, ".
					 "	(SELECT denominacion FROM scg_cuentas WHERE a.sc_cuentarecdoc=scg_cuentas.sc_cuenta) AS denominacion_rec, ".
					 " 	(SELECT denbansig FROM sigesp_banco_sigecof WHERE a.codbansig=sigesp_banco_sigecof.codbansig) AS denbansig, ".
					 " 	(SELECT COUNT(codsujret) FROM scb_cmp_ret WHERE (a.ageviapro='1' OR a.aerolipro='1') ".
					 " 	AND a.cod_pro=scb_cmp_ret.codsujret AND a.rifpro=scb_cmp_ret.rif) AS comprobante ".
					 " FROM rpc_proveedor a ".
					 " LEFT JOIN scg_cuentas b ON (a.sc_cuenta=b.sc_cuenta ) ".
					 " LEFT JOIN sigesp_pais c ON (a.codpai=c.codpai)" .
					 " LEFT JOIN sigesp_estados d ON (a.codest=d.codest and c.codpai=d.codpai) ".
					 " LEFT JOIN sigesp_municipio e ON (a.codmun=e.codmun and d.codest=e.codest and c.codpai=e.codpai) ".
					 " LEFT JOIN sigesp_parroquia f ON (a.codpar=f.codpar and e.codmun=f.codmun and d.codest=f.codest and c.codpai=f.codpai) ".
					 " WHERE a.cod_pro like '%$ls_codpro%' ".
					 " AND a.nompro like '%$ls_nompro%' ".
					 " AND a.dirpro like '%$ls_dirpro%' ".
					 " AND a.rifpro like '%$ls_rifpro%' ".
					 " ".$criterio." ".
					 " AND a.cod_pro<>'----------' ";
		switch (strtoupper($_SESSION["ls_gestor"])){
	   		case "MYSQLT":
				$cadenasql = $cadenasql."ORDER BY a.cod_pro ASC ";
			break;
	   		case "MYSQLI":
				$cadenasql = $cadenasql."ORDER BY a.cod_pro ASC ";
			break;
			case "POSTGRES": //  POSTGRES
				$cadenasql = $cadenasql."ORDER BY a.cod_pro ASC ";
			break;
			case "OCI8PO":
				$cadenasql = $cadenasql." ORDER BY a.cod_pro ASC";
	   }
		$conbd = ConexionBaseDatos::getInstanciaConexion();
		$dataprov = $conbd->Execute ( $cadenasql );
		unset($this->conbd);
		return $dataprov;
	
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarBancoSigecof() {
		$this->daoProveedor = FabricaDao::CrearDAO("N", "sigesp_banco_sigecof");
		$databansig = $this->daoProveedor->leerTodos();
		unset($this->daoProveedor);
		return $databansig;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarDocumentosProv($cod_pro)
	{
		$cadenasql=" SELECT coddoc, dendoc, ".$cod_pro." AS cod_pro  ".
				" FROM rpc_documentos ".
				" WHERE coddoc<>'---' ".
				" AND coddoc NOT IN (SELECT coddoc ".
								"	FROM rpc_docxprov ".
								"	WHERE rpc_docxprov.codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
								"	AND rpc_docxprov.cod_pro = '".$cod_pro."' ".
								"	AND rpc_docxprov.coddoc = rpc_documentos.coddoc) ".
				" ORDER BY coddoc ASC"; 
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarCalificProv($cod_pro) {
		$cadenasql=" SELECT codclas, denclas ".
				" FROM rpc_clasificacion ".
				" WHERE codclas<>'---' ".
				" AND codclas NOT IN (SELECT codclas ".
								"	FROM rpc_clasifxprov ".
								"	WHERE rpc_clasifxprov.codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
								"	AND rpc_clasifxprov.cod_pro = '".$cod_pro."' ".
								"	AND rpc_clasifxprov.codclas = rpc_clasificacion.codclas) ".
				" ORDER BY codclas ASC"; 

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarNivelClasif() {
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_niveles");
		$dataclapro = $this->daoProveedor->leerTodos();
		unset($this->daoProveedor);
		return $dataclapro;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarCtaConPag($codemp,$sc_cta,$d_deno) {
		$cadenasql = "SELECT trim(sc_cuenta) as sc_cuenta, denominacion ".
					 "FROM scg_cuentas ".
					 "WHERE codemp='{$codemp}' ".
					 "AND sc_cuenta like '{$sc_cta}%' ".
					 "AND denominacion like '%{$d_deno}%' ".
					 "AND status='C'".
					 "ORDER BY sc_cuenta";
		$conbd = ConexionBaseDatos::getInstanciaConexion();
		$datactaconpag = $conbd->Execute ( $cadenasql );
		unset($this->conbd);
		return $datactaconpag;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	
	public function buscarCtaConAnt($codemp,$sc_cta,$d_deno) {
		$cadenasql = "SELECT trim(sc_cuenta) as sc_ctaant, denominacion as denominacion_2 ".
					 "FROM scg_cuentas ".
					 "WHERE codemp='{$codemp}' ".
					 "AND sc_cuenta like '{$sc_cta}%' ".
					 "AND denominacion like '%{$d_deno}%' ".
					 "AND status='C'".
					 "ORDER BY sc_cuenta ASC";
		$conbd = ConexionBaseDatos::getInstanciaConexion();
		$datactaconant = $conbd->Execute ( $cadenasql );
		unset($conbd);
		return $datactaconant;
	}
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarCtaConRec($codemp,$sc_cta,$d_deno) {
		$cadenasql = "SELECT trim(sc_cuenta) as sc_cuentarecdoc, denominacion AS denominacion_rec ".
					 "FROM scg_cuentas ".
					 "WHERE codemp='{$codemp}' ".
					 "AND sc_cuenta like '{$sc_cta}%' ".
					 "AND denominacion like '%{$d_deno}%' ".
					 "AND status='C'".
					 "ORDER BY sc_cuenta";
		$conbd = ConexionBaseDatos::getInstanciaConexion();
		$datactaconpag = $conbd->Execute ( $cadenasql );
		unset($this->conbd);
		return $datactaconpag;
	}

	public function buscarPais() {
		$this->daoPais = FabricaDao::CrearDAO("N", "sigesp_pais");
		$dataPais = $this->daoPais->leerTodos();
		unset($this->daoProveedor);
		return $dataPais;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarEstado($restriccion) {
		$this->daoEstado = FabricaDao::CrearDAO("N", "sigesp_estados");
		$dataEstado = $this->daoEstado->buscarCampoRestriccion($restriccion);
		unset($this->daoProveedor);
		return $dataEstado;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarMunicipio($restriccion) {
		$this->daoMunicipio = FabricaDao::CrearDAO("N", "sigesp_municipio");
		$dataMunicipio = $this->daoMunicipio->buscarCampoRestriccion($restriccion);
		unset($this->daoProveedor);
		return $dataMunicipio;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarParroquia($restriccion) {
		$this->daoParroquia = FabricaDao::CrearDAO("N", "sigesp_parroquia");
		$dataParroquia = $this->daoParroquia->buscarCampoRestriccion($restriccion);
		unset($this->daoProveedor);
		return $dataParroquia;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::guardarParametro()
	 */
	public function guardarProveedor($codemp,$objson,$arrevento)
	{
		$resultado = 0;//variable que indica el resultado de la operacion
		$fecha = date("Y-m-d");
		
		//obteniendo las instacias de los dao's
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_proveedor");
		//seteando la data e iniciando transaccion de base de datos
		$this->daoProveedor->codbansig = '---';
		$this->daoProveedor->setData($objson);
		$this->daoProveedor->codemp=$codemp;
		$this->daoProveedor->cod_pro=str_pad(trim($this->daoProveedor->cod_pro),10,'0',0);
		$this->daoProveedor->codesp='---';
		$this->daoProveedor->fecreg=$fecha;
		//DaoGenerico::iniciarTrans();
		
		//insertando el registro y escribiendo en el log
		$respuesta = $this->daoProveedor->incluir(true, 'cod_pro', true, 10);				
		if($respuesta === true)
		{
			$this->valido = true;
		}
		else
		{
			if ($respuesta !== false)
			{
				$arrRespuesta = explode(",",$respuesta);
				if ($arrRespuesta[0] == "-1")
				{
					$this->valido = true;
					$this->mensaje .= " Le fue asignado el codigo ".$arrRespuesta[1].". ";
				}
				elseif ($arrRespuesta[0] == '0')
				{
					$this->valido = false;
				}
			}
			else
			{
				$this->valido = false;
			}
		}
		
		if($this->valido)
		{
			$this->daoEspecialidad = FabricaDao::CrearDAO('N','rpc_espexprov');
			$this->daoEspecialidad->codesp='---';
			$this->daoEspecialidad->codemp = $this->daoProveedor->codemp;
			$this->daoEspecialidad->cod_pro = $this->daoProveedor->cod_pro;
			if(!$this->daoEspecialidad->incluir())
			{
				$this->valido=false;
			}
		}		
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];
		
		//completando la transaccion retorna 1 si no hay errores
		if ($this->valido)
		{
			$resultado = 1;
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento(); 	
		}
		else
		{
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();	
		}
		
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoProveedor);
		return $this->valido;
	}
		
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::modificarParametro()
	 */
	public function modificarProveedor($codemp,$objson,$arrevento) {
		$this->valido = false;//variable que indica el resultado de la operacion
		
		//obteniendo las instacias de los dao's
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_proveedor");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoProveedor->codbansig = '---';
		$this->daoProveedor->setData($objson);
		$this->daoProveedor->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//modificando el registro y escribiendo en el log
		$respuesta = $this->daoProveedor->modificar();
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];
		
		//completando la transaccion retorna 1 si no hay errores
		if (DaoGenerico::completarTrans()) {
			$this->valido = true;
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento(); 	
		}
		else{
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
		
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoProveedor);
		unset($this->daoRegistroEvento);
		return $this->valido;
	}
	
	public function eliminarDto($dto,$codemp) {
		$this->daogenerico = FabricaDao::CrearDAO("N", "rpc_proveedor");
		$this->daogenerico->codemp = $codemp;
		$this->daogenerico->setData($dto);
		$this->eliminarEspxproveedor($dto->cod_pro);
		$errorNo = '';
		$this->pasarDatos ( $dto );
		try {
			if(!$this->daogenerico->eliminar ('cod_pro',$dto->cod_pro)){
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
	 * @see modelo/servicio/rpc/iparametroclasificacion::eliminarParametro()
	 */
	public function eliminarProveedor($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
		DaoGenerico::iniciarTrans();
		$existe = $this->eliminarDto($objson, $codemp);
		if($existe==''){
			//obteniendo las instacias de los dao's
			$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_proveedor");
			
			//seteando la data e iniciando transaccion de base de datos
			$this->daoProveedor->setData($objson);
			$this->daoProveedor->codemp=$codemp;
			$this->daoProveedor->eliminar();
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
				$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
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
		unset($this->daoProveedor);
		unset($this->daoRegistroEvento);
		return $resultado;
	}

	public function existeProveedor($codemp, $codpro)
	{
		$existe = false;
		$conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSql         = "SELECT cod_pro ".
  							 "	FROM rpc_proveedor ".
  							 " WHERE codemp ='{$codemp}' ".
							 "   AND cod_pro LIKE '%{$codpro}%'";		
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
	
	public function buscarProveedores($codpro,$nompro,$dirpro)
	{
		$criterio="";
		if($codpro!="")
		{
			$criterio .= " AND cod_pro like '%".$codpro."%'";
		}
		if($nompro!="")
		{
			$criterio .= " AND nompro like '%".$nompro."%'";
		}
		if($nompro!="")
		{
			$criterio .= " AND dirpro like '%".$dirpro."%'";
		}
		$cadenasql="SELECT cod_pro,nompro,sc_cuenta,rifpro FROM rpc_proveedor  ".
                " WHERE codemp = '".$_SESSION['la_empresa']['codemp']."' ".
				"   AND cod_pro <> '----------' ".
				"   AND estprov = 0 ".
				$criterio;
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$dataSOC = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $dataSOC;
	}
	
	public function guardarProveedorSocios($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
		
		//obteniendo las instacias de los dao's
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_proveedorsocios");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoProveedor->setData($objson);
		$this->daoProveedor->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//insertando el registro y escribiendo en el log
		$this->daoProveedor->incluir();
		
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
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
		
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoProveedor);
		return $resultado;
	}
	
	public function buscarSocios($codpro)
	{
		$cadenasql=" SELECT * ".
					" FROM rpc_proveedorsocios ".
					" WHERE cod_pro='".$codpro."' ".
					" ORDER BY cedsocio ASC" ;

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	public function eliminarProveedorSocios($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
		
		//obteniendo las instacias de los dao's
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_proveedorsocios");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoProveedor->setData($objson);
		$this->daoProveedor->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		$this->daoProveedor->eliminar();
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
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
		
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoProveedor);
		unset($this->daoRegistroEvento);
		return $resultado;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::modificarParametro()
	 */
	public function modificarProveedorSocios($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
		
		//obteniendo las instacias de los dao's
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_proveedorsocios");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoProveedor->setData($objson);
		$this->daoProveedor->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//modificando el registro y escribiendo en el log
		$respuesta = $this->daoProveedor->modificar();
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
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
		
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoProveedor);
		return $resultado;
	}
	
	public function guardarProveedorDocumentos($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
		
		//obteniendo las instacias de los dao's
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_docxprov");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoProveedor->setData($objson);
		$this->daoProveedor->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//insertando el registro y escribiendo en el log
		$this->daoProveedor->incluir();
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
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
		
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoProveedor);
		return $resultado;
	}
	
	public function buscarProveedorDoc($cod_pro)
	{
		
		$cadenasql="SELECT rpc_docxprov.coddoc as coddoc, rpc_documentos.dendoc as dendoc, rpc_docxprov.fecrecdoc as fecrecdoc, rpc_docxprov.fecvendoc, ".
				   "       rpc_docxprov.estdoc as estdoc, rpc_docxprov.estorig as estorig ".
				   "  FROM rpc_documentos, rpc_docxprov  ".		
				   " WHERE rpc_documentos.codemp='".$_SESSION['la_empresa']['codemp']."' ".
				   "   AND cod_pro='".$cod_pro."' ".
				   "   AND rpc_documentos.codemp=rpc_docxprov.codemp  ".
				   "   AND rpc_documentos.coddoc=rpc_docxprov.coddoc ".
				   " ORDER BY  rpc_documentos.coddoc";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	public function eliminarProveedorDocumentos($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
		
		//obteniendo las instacias de los dao's
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_docxprov");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoProveedor->setData($objson);
		$this->daoProveedor->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		$this->daoProveedor->eliminar();
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
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
		
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoProveedor);
		return $resultado;
	}

	public function modificarProveedorDocumentos($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
		
		//obteniendo las instacias de los dao's
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_docxprov");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoProveedor->setData($objson);
		$this->daoProveedor->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//modificando el registro y escribiendo en el log
		$respuesta = $this->daoProveedor->modificar();
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
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
		
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoProveedor);
		return $resultado;
	}
	
	public function guardarProveedorCalificacion($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
		
		//obteniendo las instacias de los dao's
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_clasifxprov");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoProveedor->setData($objson);
		$this->daoProveedor->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//insertando el registro y escribiendo en el log
		$this->daoProveedor->incluir();
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
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
		
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoProveedor);
		return $resultado;
	}
	
	public function modificarProveedorCalificacion($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
		
		//obteniendo las instacias de los dao's
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_clasifxprov");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoProveedor->setData($objson);
		$this->daoProveedor->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//modificando el registro y escribiendo en el log
		$respuesta = $this->daoProveedor->modificar();
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
		unset($this->daoProveedor);
		return $resultado;
	}
	
	public function buscarProveedorCla($cod_pro)
	{
		$cadenasql=" SELECT rpc_clasifxprov.codclas, rpc_clasificacion.denclas, ".
                 " rpc_clasifxprov.status, rpc_clasifxprov.nivstatus, rpc_niveles.codniv, ".
				 " rpc_niveles.desniv, rpc_niveles.monmincon, rpc_niveles.monmaxcon, ".
				 " rpc_clasifxprov.monfincon ".
            	 " FROM rpc_clasificacion, rpc_clasifxprov, rpc_niveles ".
		  	 	 " WHERE rpc_clasificacion.codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
			 	 " AND cod_pro= '".$cod_pro."' ".
		     	 " AND rpc_clasificacion.codemp=rpc_clasifxprov.codemp ".
		     	 " AND rpc_clasificacion.codclas=rpc_clasifxprov.codclas ".
			 	 " AND rpc_clasifxprov.codemp=rpc_niveles.codemp ".
			 	 " AND rpc_clasifxprov.codniv=rpc_niveles.codniv";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	public function eliminarProveedorCalif($codemp,$objson,$arrevento) {
		$resultado = 0;//variable que indica el resultado de la operacion
		
		//obteniendo las instacias de los dao's
		$this->daoProveedor = FabricaDao::CrearDAO("N", "rpc_clasifxprov");
		
		//seteando la data e iniciando transaccion de base de datos
		$this->daoProveedor->setData($objson);
		$this->daoProveedor->codemp=$codemp;
		DaoGenerico::iniciarTrans();
		
		//modificando el registro y escribiendo en el log
		$this->daoProveedor->eliminar();
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
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
		
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoProveedor);
		return $resultado;
	}
	
	public function buscarProveedorEspecialidades($cod_pro)
	{
		$cadenasql="SELECT rpc_espexprov.codesp, rpc_especialidad.denesp  ".
				" FROM rpc_especialidad, rpc_espexprov ".
				" WHERE rpc_espexprov.codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
				" AND rpc_especialidad.codesp<>'---'   ".
				" AND rpc_espexprov.cod_pro = '".$cod_pro."' ".
				" AND rpc_espexprov.codesp = rpc_especialidad.codesp ".
				" ORDER BY rpc_espexprov.codesp ASC"; 
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	public function buscarProveedorEspecialidadesDisp($cod_pro)
	{
		$cadenasql=" SELECT codesp, denesp ".
				" FROM rpc_especialidad ".
				" WHERE codesp<>'---' ".
				" AND codesp NOT IN (SELECT codesp ".
								"	FROM rpc_espexprov ".
								"	WHERE rpc_espexprov.codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
								"	AND rpc_espexprov.cod_pro = '".$cod_pro."' ".
								"	AND rpc_espexprov.codesp = rpc_especialidad.codesp) ".
				" ORDER BY codesp ASC"; 

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	public function eliminarEspxproveedor($cod_pro)
	{
		$cadenasql=" DELETE ".
				   " FROM rpc_espexprov ".
				   " WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
				   " AND cod_pro='".$cod_pro."'";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	public function guardarProveedorEspecialidades($codemp, $arrjson, $cod_pro, $arrEvento)
	{
		$resultado = '0';
		$valido = true;
		DaoGenerico::iniciarTrans();
		$this->eliminarEspxproveedor($cod_pro);
		$espcInsertar = $arrjson->arrEspIncluir;
		$numFueInc = count((array)$espcInsertar);
		$this->daoProveedor = FabricaDao::CrearDAO('N','rpc_espexprov');
		$this->daoProveedor->codesp='---';
		$this->daoProveedor->codemp = $codemp;
		$this->daoProveedor->cod_pro = $cod_pro;
		if(!$this->daoProveedor->incluir(false,'',false,0,true))
		{
			$valido = false;
		}

		for ($i = 0; ($i < $numFueInc)&&($valido); $i++)
		{
			$this->daoProveedor = FabricaDao::CrearDAO('N','rpc_espexprov');
			$this->daoProveedor->setData($espcInsertar[$i]);
			$this->daoProveedor->codemp = $codemp;
			$this->daoProveedor->cod_pro = $cod_pro;
			if(!$this->daoProveedor->incluir(false,'',false,0,true))
			{
				$valido = false;
			}
		}
		
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];
		
		if (DaoGenerico::completarTrans($valido))
		{
			$resultado = '1';
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento(); 
		}
		else
		{
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		}
		
		unset($this->daoProveedor);
		return $resultado;
	}

	public function buscarProveedorDeducciones($cod_pro)
	{
		$cadenasql="SELECT rpc_deduxprov.codded, sigesp_deducciones.dended  ".
				" FROM sigesp_deducciones, rpc_deduxprov ".
				" WHERE rpc_deduxprov.codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
				" AND sigesp_deducciones.codded<>'-----'   ".
				" AND rpc_deduxprov.cod_pro = '".$cod_pro."' ".
				" AND rpc_deduxprov.codded = sigesp_deducciones.codded ".
				" ORDER BY rpc_deduxprov.codded ASC"; 

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}

	public function buscarProveedorDeduccionesDisp($cod_pro)
	{
		$cadenasql=" SELECT codded, dended ".
				" FROM sigesp_deducciones ".
				" WHERE codded<>'---' ".
				" AND codded NOT IN (SELECT codded ".
								"	FROM rpc_deduxprov ".
								"	WHERE rpc_deduxprov.codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
								"	AND rpc_deduxprov.cod_pro = '".$cod_pro."' ".
								"	AND rpc_deduxprov.codded = sigesp_deducciones.codded) ".
				" ORDER BY codded ASC"; 

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	public function eliminarDedxproveedor($cod_pro)
	{
		$cadenasql=" DELETE ".
				   " FROM rpc_deduxprov ".
				   " WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
				   " AND cod_pro='".$cod_pro."'";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	public function guardarProveedorDeducciones($codemp, $arrjson, $cod_pro, $arrEvento)
	{
		$resultado = '0';
		$valido = true;
		DaoGenerico::iniciarTrans();
		$this->eliminarDedxproveedor($cod_pro);
		$dedInsertar = $arrjson->arrDedIncluir;
		$numFueInc = count((array)$dedInsertar);
		for ($i = 0; $i < $numFueInc; $i++)
		{
			$this->daoProveedor = FabricaDao::CrearDAO('N','rpc_deduxprov');
			$this->daoProveedor->setData($dedInsertar[$i]);
			$this->daoProveedor->codemp = $codemp;
			$this->daoProveedor->cod_pro = $cod_pro;
			if(!$this->daoProveedor->incluir(false,'',false,0,true))
			{
				$valido = false;
			}
		}
		
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];
		
		if (DaoGenerico::completarTrans($valido))
		{
			$resultado = '1';
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento(); 		
		}
		 else{
			$arrevento ['desevetra'] = $this->daoProveedor->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		 }
		unset($this->daoProveedor);
		return $resultado;
	}
	
	public function buscarDenomEstado($codpai)
	{
		$cadenasql=" SELECT codest, desest ".
				" FROM sigesp_estados ".
				" WHERE codpai='".$codpai."' ".
				" ORDER BY codest ASC"; 

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	public function buscarDenomMunicipio($codpai,$codest)
	{
		$cadenasql=" SELECT codmun, denmun ".
				" FROM sigesp_municipio ".
				" WHERE codpai='".$codpai."' ".
				" AND codest='".$codest."' ".
				" ORDER BY codmun ASC"; 

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	public function buscarDenomParroquia($codpai,$codest,$codmun)
	{
		$cadenasql=" SELECT codpar, denpar ".
				" FROM sigesp_parroquia ".
				" WHERE codpai='".$codpai."' ".
				" AND codest='".$codest."' ".
				" AND codmun='".$codmun."' ".
				" ORDER BY codpar ASC"; 

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	public function buscarRifProv($rifpro, $seniat)
	{
		$this->valido=false;			
		$this->mensaje="";
		$nompro="";
		$ageret="";
		$contribuyente="";
		$cadenasql="SELECT rifpro, nompro ".
				   "  FROM rpc_proveedor ".
				   " WHERE rifpro='".$rifpro."' ";
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute($cadenasql);
		if($resultado->EOF)
		{
			$this->valido=true;		
			if($seniat)
			{
				if(extension_loaded('curl'))
				{
					$rifpro=str_replace("-","", $rifpro);
					$url = "http://contribuyente.seniat.gob.ve/getContribuyente/getrif?rif=".$rifpro;
					$xml = curl_init($url);
					curl_setopt($xml, CURLOPT_HEADER, 0);
					curl_setopt($xml, CURLOPT_RETURNTRANSFER, 1);
					$output = curl_exec($xml);
					curl_close($xml);
					$xml= @simplexml_load_string(str_replace("rif:","rif-",$output));
					if ($xml)
					{
						$xml1=(array) $xml;
						$nompro= (string) $xml1["rif-Nombre"];
						$li_pos=strpos($nompro, " (");
						$nompro=substr($nompro,0,$li_pos);
						$this->valido= true;
						$ageret=(string) $xml1["rif-AgenteRetencionIVA"];
						$contribuyente=(string) $xml1["rif-ContribuyenteIVA"];			
					}
					else
					{			
						 $error=substr($output,0,3);
						 switch($error)
						 {
							 case '450':
								$this->mensaje="El RIF del Contribuyente no es Valido segun el SENIAT"; 
								$this->valido=false;
								break;
							 case '452':
								$this->mensaje="El RIF del Contribuyente no esta Registrado en el SENIAT"; 
								$this->valido=false;
								break;
							 default:
								$this->mensaje="El servicio del SENIAT esta ocupado o no tiene conexion a internet, intente nuevamente."; 
								break;
						 }	
					}
				}
				else
				{
					$this->mensaje="No se pudo Verificar el Rif con el SENIAT. Debe Habilitar la Extencion curl.";		
				}
			}
		}
		else
		{
			$this->mensaje="El Rif ya esta Registrado en el sistema.";		
		}
		unset($resultado);
		unset($conexionbd);
		$resultado["valido"]=$this->valido;
		$resultado["mensaje"]=$this->mensaje;
		$resultado["nompro"]=$nompro;
		$resultado["ageret"]=$ageret;
		$resultado["contribuyente"]=$contribuyente;
		return $resultado;
	}
}
?>