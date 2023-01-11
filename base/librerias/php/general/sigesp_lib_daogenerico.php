<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once ("sigesp_lib_funciones.php");
require_once ("sigesp_lib_conexion.php");

class DaoGenerico extends ADOdb_Active_Record
{
	
	public $errorValidacion = false;
	public $errorDuplicate  = false;
	public $registroExiste  = false;
	
	/**
	 * @desc Metodo constructor de la clase, llama al metodo estatico conectar para crear
	 *       el objeto de conexion el cual es seteado en el atributo privado conexionbd
	 * @param string $table - nombre de la tabla a instaciar con active record
	 * @author Ing. Gerardo Cordero
	 */
	function __construct($table = false)
	{
		ConexionBaseDatos::getInstanciaConexion();
		parent::__construct ( $table );
		//ConexionBaseDatos::$conexionbd->debug = true;
	}
	
	
	/**
	 * @desc Metodo que inicia una transaccion de base de datos
	 * @author Ing. Gerardo Cordero
	 */
	public static function iniciarTrans() {
		ConexionBaseDatos::getInstanciaConexion(); 
		ConexionBaseDatos::$conexionbd->StartTrans();
	}
	
	/**
	 * @desc Metodo que finaliza una transaccion de base de datos
	 * @return boolean - true si el commit se realizo satisfactoriamente
	 * @author Ing. Gerardo Cordero
	 */
	public static function completarTrans($valido=true) {
		ConexionBaseDatos::getInstanciaConexion(); 
		if (ConexionBaseDatos::$conexionbd->CompleteTrans($valido)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * @desc  Este metodo invoca al metodo statico para establecer una conexion alterna
	 *        a una base de datos
	 * @param string  $servidor - ip del servidor de datos
	 * @param string  $usuario - cuenta de usuario servidor de datos
	 * @param strinf  $clave - clave cuenta de usuario servidor de datos
	 * @param string  $basedatos - nombre de la base de datos
	 * @param string  $gestor - identificador del gestor, ver documentacion adodb
	 * @param string  $puerto -  numero del puerto del servidor de datos
	 * @param boolean $flagactiverecord - true activa la capacidad active record de adodb
	 * @return conexionAlternabd - instacia de conexion alterna
	 * @author Ing. Gerardo Cordero
	 */
	public function obtenerConexionAlterna($servidor, $usuario, $clave, $basedatos, $gestor, $puerto,$flagactiverecord=false){
		return ConexionBaseDatos::conectarAlternaBD($servidor, $usuario, $clave, $basedatos, $gestor, $puerto,$flagactiverecord);
	}
	
	
	/**
	 * @desc  Metodo que instacia la clase de conecion para crea un objeto de conecion
	 * @param array $arrdatcon - parametros de coneccion
	 * @return objlibcom - objeto de conexion
	 * @author Ing. Gerardo Cordero
	 */
	public function getObjetoConexion($arrdatcon){
		$objlibcom = new ConexionBaseDatos();
		return $objlibcom->conectarBD($arrdatcon['host'],$arrdatcon['login'],$arrdatcon['password'],$arrdatcon['basedatos'],$arrdatcon['gestor']);
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $multiusuario
	 * @param unknown_type $consecutivo
	 * @param unknown_type $validarempresa
	 * @return boolean|string
	 * @author Ing. Gerardo Cordero
	 */
	public function incluir($multiusuario=false, $consecutivo="", $validarempresa=false, $longitud=0, $validaexiste=false,
							$arrFiltro=array(), $codsis='', $procede='', $codusu='')
	{
		if(!$multiusuario)
		{
			try
			{
				if ($validaexiste)
				{
					$resultado = $this->buscarPk();
					$cantcampo = $resultado->_numOfRows;
					unset($resultado);
					if($cantcampo>0)
					{
						return true;
						$this->registroExiste = true;
					}
					else
					{
						return $this->Save();
					}
				}
				else
				{
					return $this->Save();
				}
			} 
			catch (Exception $e)
			{
				if($this->ErrorNo()==-5 || $this->ErrorNo()==-1 || $this->ErrorNo()==-239 || $this->ErrorNo()==1062 || $this->ErrorNo()==23505)
				{
					$this->errorDuplicate = true;	
				}
				return false;
			}
		}
		else
		{
			try
			{
				return $this->Save();
			} 
			catch (Exception $e)
			{
				if($this->ErrorNo()==-5 || $this->ErrorNo()==-1 || $this->ErrorNo()==-239 || $this->ErrorNo()==1062 || $this->ErrorNo()==23505)
				{
					$this->errorDuplicate = true;
					$this->$consecutivo = $this->buscarCodigo($consecutivo,$validarempresa,$longitud,$arrFiltro,$codsis,$procede,$codusu); 
					$resultado = $this->incluir(true,$consecutivo,$validarempresa,$longitud,$validaexiste,$arrFiltro,$codsis,$procede,$codusu);
					if($resultado)
					{
						return "-1,".$this->$consecutivo;
					}
					else
					{
						return "0";
					}
				}
			}
		}
		
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $validaexiste
	 * @return Ambigous <boolean, unknown>|number
	 * @author Ing. Gerardo Cordero
	 */
	public function modificar($validaexiste = false)
	{
		if(!$validaexiste)
		{
			$this->setDataNull();
			return $this->Replace ();
		}
		else
		{
			$resultado = $this->buscarPk();
			$cantcampo = $resultado->_numOfRows;
			unset($resultado);
			if($cantcampo>0)
			{
				return 1;
			}
			else
			{
				$this->setDataNull();
				return $this->Replace ();
			}
		}
	}
	
	/**
	 * Enter description here ...
	 * @return boolean
	 * @author Ing. Gerardo Cordero
	 */
	public function eliminar($campovalidar='', $valor='', $load=false)
	{
		if ($campovalidar!='')
		{
			if(!$this->validarRelacionesPlus($campovalidar, $valor))
			{
				return $this->Delete ($load);
			}
			else
			{
				$this->errorValidacion = true;
				return false;
			}
		}
		else
		{
			return $this->Delete ($load);
		}
	}
	
	/**
	 * Enter description here ...
	 * @param json $ObJson
	 * @author Ing. Gerardo Cordero
	 */
	public function setData($ObJson) {
		$arratributos = $this->GetAttributeNames();
		foreach ( $arratributos as $IndiceDAO ) {
			foreach ( $ObJson as $IndiceJson => $valorJson ) {
				if ($IndiceJson == $IndiceDAO) {
					$this->$IndiceJson = utf8_decode ( trim($valorJson) );
				}
			}
		}
	}
	
	/**
	 * Enter description here ...
	 * @param array $arrfk
	 * @return boolean
	 * @author Ing. Gerardo Cordero
	 */
	public function validarRelaciones($arrfk)
	{
		$existerelacion=true;
		foreach ($arrfk as $nomtabenlace => $tablaenlace) {
			$cantcampo = ((count($tablaenlace))-1);
			$restriccion = array();
			$cantfk=0;
			foreach ($tablaenlace as $campoenlace => $valorenlace) {
				if($cantcampo==$cantfk){
					$restriccion[$cantfk][0] = $campoenlace;
            		$restriccion[$cantfk][1] = '=';
                    $restriccion[$cantfk][2] = $valorenlace;
                    $restriccion[$cantfk][3] = 2;
				}else{
					$restriccion[$cantfk][0] = $campoenlace;
                    $restriccion[$cantfk][1] = '=';
                    $restriccion[$cantfk][2] = $valorenlace;
                    $restriccion[$cantfk][3] = 0;
				}
				$cantfk++;
			}
			$resulatado=$this->buscarCampoRestriccion($restriccion,true,$nomtabenlace);
			if (($resultado->_numOfRows)>0){
				return false;
			}
		}
		
		return $existerelacion;
	}
	
	public function validarRelacionesPlus($campo,$valor,$arrtablaignorar = null,$obtenerModulo = false, $filtrotablas = '', $filtroadicional = '')
	{
		$existerelacion = false;
		$modulos='';
		switch ($_SESSION["ls_gestor"])
		{
			case 'MYSQLT':
				if($arrtablaignorar!=null)
				{
					foreach ($arrtablaignorar as $tabla => $nomtabla)
					{
						$filtrotablas .= " AND TABLE_NAME<>'{$nomtabla}' ";
					}
				}
				$cadenaSql ="SELECT DISTINCT TABLE_NAME AS table_name,column_name ". 
							"  FROM INFORMATION_SCHEMA.COLUMNS  ".
				 			" WHERE TABLE_SCHEMA='{$_SESSION['ls_database']}'  ". 
				 			"   AND (column_name='{$campo}')  ". 
				 			"   AND TABLE_NAME<>'{$this->_table}'".$filtrotablas;
				break;

			case 'MYSQLI':
				if($arrtablaignorar!=null)
				{
					foreach ($arrtablaignorar as $tabla => $nomtabla)
					{
						$filtrotablas .= " AND TABLE_NAME<>'{$nomtabla}' ";
					}
				}
				$cadenaSql ="SELECT DISTINCT TABLE_NAME AS table_name,column_name ". 
							"  FROM INFORMATION_SCHEMA.COLUMNS  ".
				 			" WHERE TABLE_SCHEMA='{$_SESSION['ls_database']}'  ". 
				 			"   AND (column_name='{$campo}')  ". 
				 			"   AND TABLE_NAME<>'{$this->_table}'".$filtrotablas;
				break;
			
			case 'POSTGRES':
				if($arrtablaignorar!=null)
				{
					foreach ($arrtablaignorar as $tabla => $nomtabla)
					{
						$filtrotablas .= " AND table_name<>'{$nomtabla}' ";
					}
				}
				$cadenaSql = "SELECT DISTINCT table_name ".
							 "  FROM INFORMATION_SCHEMA.COLUMNS ". 
							 " WHERE table_catalog='{$_SESSION['ls_database']}' ".
							 "	 AND (column_name='{$campo}') ".
							 "   AND table_name<>'{$this->_table}'".$filtrotablas;
				break;
		}
		$resultado = ConexionBaseDatos::$conexionbd->Execute($cadenaSql);
		while (!$resultado->EOF)
		{
			$tabla = $resultado->fields['table_name'];
			$arrpk = ConexionBaseDatos::$conexionbd->MetaPrimaryKeys($tabla);
			if ($arrpk!==false)
			{
				if (in_array('codemp',$arrpk))
				{
					if(isset($this->codemp))
					{
						$cadenaFiltro = " codemp = '{$this->codemp}' AND {$campo} = '{$valor}'";
					}
					else
					{
						$cadenaFiltro = " codemp = '0001' AND {$campo} = '{$valor}'";
					}
				}
				else
				{
					$cadenaFiltro = " {$campo} = '{$valor}'";
				}
			}
			else
			{
				$cadenaFiltro = " {$campo} = '{$valor}'";
			}
			if (trim($filtroadicional) != '')
			{
				$cadenaFiltro .= $filtroadicional;
			}
			$cadenaSql = "SELECT {$campo} ".
			           	 "	FROM {$tabla} ".
			           	 " WHERE ".$cadenaFiltro;
			$datacampo = ConexionBaseDatos::$conexionbd->Execute($cadenaSql);
			if($datacampo->_numOfRows > 0)
			{
				if($obtenerModulo)
				{
					$modulo = $this->obtenerModulo(substr($tabla, 0,3));
					if($modulo != '')
					{
						if (strlen($modulos)>strlen($modulo))
						{
							if(substr_compare($modulos, $modulo, -strlen($modulo), strlen($modulo))!=0)
							{
								if ($modulos=='')
								{
									$modulos .= $modulo;
								}
								else
								{
									$modulos .= ', '.$modulo;
								}
								
							}
						}
						else
						{
							if ($modulos=='')
							{
								$modulos .= $modulo;
							}
							else
							{
								$modulos .= ', '.$modulo;
							}
						}
						
					}
					$existerelacion = $modulos;
				}
				else
				{
					$existerelacion = true;
					break;					
				}
			}
			$resultado->MoveNext();
		}
		if ($existerelacion=='')
		{
			$existerelacion=false;
		}
				
		return $existerelacion;
	}
	
	
	public function obtenerModulo($acronimo) {
		$nombreModulo = '';
		switch ($acronimo) {
			case 'sig'://1
				$nombreModulo = 'Configuracion';
				break;
			case 'cxp'://2
				$nombreModulo = 'Cuentas por Pagar';
				break;
			case 'rpc'://3
				$nombreModulo = 'Proveedores y Beneficiarios';
				break;
			case 'scb'://4
				$nombreModulo = 'Banco';
				break;
			case 'scv'://5
				$nombreModulo = 'Control de Viaticos';
				break;
			case 'sep'://6
				$nombreModulo = 'Solicitud de Ejecucion Presupuestaria';
				break;
			case 'siv'://7
				$nombreModulo = 'Inventario';
				break;
			case 'sno'://8
				$nombreModulo = 'Nomina';
				break;
			case 'sob'://9
				$nombreModulo = 'Obras';
				break;
			case 'soc'://10
				$nombreModulo = 'Compras';
				break;
			case 'spg'://11
				$nombreModulo = 'Presupuesto de Gasto';
				break;
			case 'spi'://12
				$nombreModulo = 'Presupuesto de Ingreso';
				break;
			case 'srh'://13
				$nombreModulo = 'Recursos Humanos';
				break;
			case 'sss'://14
				$nombreModulo = 'Seguridad';
				break;
			case 'scg'://15
				$nombreModulo = 'Contabilidad General';
				break;
			case 'saf'://16
				$nombreModulo = 'Activos';
				break;	
			
		}
		
		return $nombreModulo;
	}
	
	/**
	 * Enter description here ...
	 * @author Ing. Gerardo Cordero
	 */
	public function buscarPk()
	{
		$restriccion = array();
		$arrpk       = $this->obtenerArregloPk();
		$arrcampos   = $this->getAttributeNames();
		$cantcampo   = count($arrpk);
		$cantpk      = 0;
		foreach ($arrpk as $campopk =>$indicepk)
		{
			$cantpk++;
			foreach ($arrcampos as $regcampo => $indicecampo)
			{
				if($indicepk==$indicecampo)
				{
					if($cantcampo==$cantpk)
					{
						$restriccion[$cantpk][0] = $indicepk;
                    	$restriccion[$cantpk][1] = '=';
                    	$restriccion[$cantpk][2] = $this->$indicepk;
                    	$restriccion[$cantpk][3] = 2;
					}
					else
					{
						$restriccion[$cantpk][0] = $indicepk;
                    	$restriccion[$cantpk][1] = '=';
                    	$restriccion[$cantpk][2] = $this->$indicepk;
                    	$restriccion[$cantpk][3] = 0;
					}
				}
			}
		}
		
		return $this->buscarCampoRestriccion($restriccion);
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $cadenafiltro
	 * @return DaoGenerico|boolean
	 * @author Ing. Gerardo Cordero
	 */
	public function getObjetoDto($cadenafiltro)
	{
		$resultado=$this->load($cadenafiltro);
		if($resultado)
		{
			return $this;
		}
		else
		{
			return $resultado;
		}
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $campoorden
	 * @param unknown_type $tipoorden
	 * @return null
	 * @author Ing. Gerardo Cordero
	 */
	public function leerTodos($campoorden="",$tipoorden=0,$empresa="") {
		$cadena="";
		
		if($empresa != ""){
			$cadena = " where codemp='".$empresa."'"; 
		}
		
		if($campoorden != "")
		{
			$cadena .= " order by ".$campoorden;
			
			switch($tipoorden)
			{
				case 1: $cadena = $cadena." ASC";
						break;
						
				case 2: $cadena = $cadena." DESC";
						break;
						
				default: $cadena = $cadena." ASC";
			}
		}
		$resultado = ConexionBaseDatos::$conexionbd->Execute ( "select * from {$this->_table} ".$cadena );
		return $resultado;
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $campo
	 * @param unknown_type $valor
	 * @return Ambigous <boolean, unknown>
	 * @author Ing. Gerardo Cordero
	 */
	public function buscarCampo($campo, $valor) {
		$resultado = $this->Find ( "{$campo} like  '%{$valor}%' " );
		return $resultado;
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $restricciones
	 * @param unknown_type $banderatabla
	 * @param unknown_type $tabla
	 * @author Ing. Gerardo Cordero
	 */
	public function buscarCampoRestriccion($restricciones,$banderatabla=false,$tabla='')
	{
		$modelo = "";
		
		foreach ( $restricciones as $restriccion )
		{
			$campo = $restriccion [0];
			$evaluador = $restriccion [1];
			$valor = $restriccion [2];
			$andor = $restriccion [3];
						
			if($evaluador == 'ORDER BY')
			{
				$modelo .= $evaluador . " " . $campo . "  " . $valor;				
			}else
			{
				$modelo .= $campo . " " . $evaluador . " '" . $valor . "'";	
			}
			
			if ($andor == 0)
			{
				$modelo .= " AND ";
			} elseif ($andor == 1)
			{
				$modelo .= " OR ";
			} elseif ($andor == 2)
			{
				$modelo .= " ";
			}
		}
        if(!$banderatabla)
		{
        	return $resultado = ConexionBaseDatos::$conexionbd->Execute ( "select * from {$this->_table} where " . $modelo );	
        }
		else
		{
			return $resultado = ConexionBaseDatos::$conexionbd->Execute ( "select * from {$tabla} where " . $modelo );
		}
	}
	
	/**
	 * Funcion que busca el numero de siguiente de una secuencia, el metodo tambien soporta el manejo
	 * de prefijo se debe especificar los parametros codigo de sistema, procedencia y usuario para que
	 * retorne el codigo con prefijo.
	 * @param string  $codigo - nombre del campo sobre el cual se calcula la secuencia
	 * @param boolean $validarempresa - true si se requiere filtrar la busqueda del siguiete numero por la empresa
	 * @param number  $longitud  - numero de caracteres del codigo
	 * @param array   $arrFiltro - arreglo que contiene campos para filtrar la busqueda del siguiente
	 *                             estructura del arreglo ['campo']='valor'
	 * @param string  $codsis    - codigo del sistema (uso de prefijo)
	 * @param string  $procede   - codigo de procedencia (uso de prefijo)
	 * @param string  $codusu    - codigo de usuario (uso de prefijo)                              
	 * @return number - numero siguiente
	 * @author Ing. Gerardo Cordero
	 */
	public function buscarCodigo($codigo, $validarempresa=true, $longitud=0, $arrFiltro=array(), $codsis='', $procede='', $codusu='',$campoinicial='', $prefijo='')
	{
		$numero = '';
		$longitudprefijo=strlen($prefijo);
		$filtro = '';
		if(($longitud>10)&&($prefijo==''))
		{
			$prefijo=$this->buscarPrefijo($codsis,$procede,$codusu);
			if($prefijo!='')
			{
				$longitudprefijo=strlen($prefijo);
			}
		}
		if ($prefijo=='-2')
		{
			$numero = $prefijo;
		}
		else
		{
			if($validarempresa)
			{		
				$filtro = " WHERE codemp='{$this->codemp}' ";
			}
			if (!empty($arrFiltro))
			{
				foreach ($arrFiltro as $campo => $valor)
				{
					if ($filtro == '')
					{
						//Recordar validar el tipo de dato para colocar o no las comillas
						$filtro .= " WHERE {$campo} = '{$valor}'"; 
					}
					else
					{
						$filtro .= " AND {$campo} = '{$valor}'";
					}
				}
			}
			$valorDefecto = str_pad('-', $longitud, '-', STR_PAD_LEFT);
			if ($filtro == '')
			{
				$filtro .= " WHERE {$codigo} <> '{$valorDefecto}'";
			}
			else
			{
				$filtro .= " AND {$codigo} <> '{$valorDefecto}'";
			}
			if ($filtro == '')
			{
				$filtro .= " WHERE {$codigo} LIKE '{$prefijo}%'";
			}
			else
			{
				$filtro .= " AND {$codigo} LIKE '{$prefijo}%'";
			}
			$campo="";
			switch ($_SESSION["ls_gestor"])
			{
					case "INFORMIX":
						$cadenaSQL="SELECT LIMIT 1 ".$codigo." as codigo ".
								   "  FROM {$this->_table} ".
								   " {$filtro} ".
								   " ORDER BY ".$codigo." DESC ";
					break;
					
					case "OCI8PO":
						$cadenaSQL="SELECT * FROM".
								   "(SELECT ".$codigo." as codigo ".
								   "  FROM {$this->_table} ".
								   "  {$filtro} ".
								   " ORDER BY ".$codigo." DESC)".
								   " WHERE rownum<=1 ";
					break;
	
					default: // MYSQL
						$cadenaSQL="SELECT ".$codigo." as codigo ".
								   "  FROM {$this->_table} ".
								   " {$filtro} ".
								   " ORDER BY ".$codigo." DESC LIMIT 1";
					break;
			}
			$resultado = ConexionBaseDatos::$conexionbd->Execute ($cadenaSQL);
			if ($resultado->fields ['codigo'] == '')
			{
				$numero=$this->numeroinicial($campoinicial);
				if($numero>0)
				{
					$numero=$numero-1;
				}
			} 
			else
			{
				if($prefijo!='')
				{
					$nrolen=$longitud-$longitudprefijo;
					$numpre=substr($resultado->fields['codigo'],0,$longitudprefijo);
					$numero=substr($resultado->fields['codigo'],$longitudprefijo,$nrolen);
				}
				else
				{
					$numero=$resultado->fields['codigo'];
					$nrolen=$longitud;
				}
			}
			if(str_word_count($numero))
			{
				$numero=agregarUno(0, $longitud);
			}
			else
			{
				settype($numero,'int');
				$numero=agregarUno($numero, $longitud-$longitudprefijo);
				if($numero<0)
				{
					$numero=agregarUno(0, $longitud-$longitudprefijo);				
				}
				if($prefijo!="")
				{
					$numero= $prefijo.$numero;
				}
			}
                        if($numero<0)
                        { 
                            $numero=agregarUno(0, $longitud);
                        }
		}
                $arrResultado=$this->verificarNumero($codigo, $validarempresa, $longitud, $arrFiltro, $codsis, $procede, $codusu, $campoinicial,$numero);                        
		$numero = $arrResultado['numero'];
		$valido = $arrResultado['valido'];
		return $numero;
	}
	
	public function buscarPrefijo($codsis, $procede, $codusu)
	{
		$prefijo = '';
		$cadenaSQL="SELECT sigesp_dt_prefijos.prefijo, sigesp_dt_prefijos.codusu ".
                           "  FROM sigesp_dt_prefijos ". 
                           " INNER JOIN sigesp_prefijos".
			   "    ON sigesp_dt_prefijos.codemp='".$this->codemp."' ".
		           "   AND sigesp_dt_prefijos.codsis='{$codsis}' ".
                           "   AND sigesp_dt_prefijos.procede='{$procede}' ".
			   "   AND sigesp_prefijos.estact=1".
			   "   AND sigesp_dt_prefijos.codemp=sigesp_prefijos.codemp ".
                           "   AND sigesp_dt_prefijos.id=sigesp_prefijos.id ".
		           "   AND sigesp_dt_prefijos.codsis=sigesp_prefijos.codsis ".
                           "   AND sigesp_dt_prefijos.procede=sigesp_prefijos.procede ".
                           "   AND sigesp_dt_prefijos.prefijo=sigesp_prefijos.prefijo ";
		$dataPrefijo = ConexionBaseDatos::$conexionbd->Execute ($cadenaSQL);
		if($dataPrefijo->_numOfRows > 0)
		{
			while (!$dataPrefijo->EOF)
			{
				if (trim($dataPrefijo->fields ['codusu'])==trim($codusu))
				{
					$prefijo = $dataPrefijo->fields ['prefijo'];
				}
				$dataPrefijo->MoveNext();
			}
			if ($prefijo=='') 
			{
				$prefijo = '-2';
			}
		}
		unset($dataPrefijo);
		return $prefijo;
	}

	public function utilizaPrefijo($codsis, $procede, $codusu)
	{
		$prefijo = false;
		$cadenaSQL="SELECT sigesp_dt_prefijos.prefijo, sigesp_dt_prefijos.codusu ".
                           "  FROM sigesp_dt_prefijos ". 
                           " INNER JOIN sigesp_prefijos".
			   "    ON sigesp_dt_prefijos.codemp='".$this->codemp."' ".
		           "   AND sigesp_dt_prefijos.codsis='{$codsis}' ".
                           "   AND sigesp_dt_prefijos.procede='{$procede}' ".
			   "   AND sigesp_prefijos.estact=1".
			   "   AND sigesp_dt_prefijos.codemp=sigesp_prefijos.codemp ".
                           "   AND sigesp_dt_prefijos.id=sigesp_prefijos.id ".
		           "   AND sigesp_dt_prefijos.codsis=sigesp_prefijos.codsis ".
                           "   AND sigesp_dt_prefijos.procede=sigesp_prefijos.procede ".
                           "   AND sigesp_dt_prefijos.prefijo=sigesp_prefijos.prefijo ";
		$dataPrefijo = ConexionBaseDatos::$conexionbd->Execute ($cadenaSQL);
		if($dataPrefijo->_numOfRows > 0)
		{
			while (!$dataPrefijo->EOF)
			{
				if (trim($dataPrefijo->fields ['codusu'])==trim($codusu))
				{
					$prefijo = true;
				}
				$dataPrefijo->MoveNext();
			}
		}
		unset($dataPrefijo);
		return $prefijo;
	}
	
	public function utilizaPrefijoGenerico($codsis, $procede)
	{
		$prefijo = false;
		$cadenaSQL="SELECT sigesp_dt_prefijos.prefijo, sigesp_dt_prefijos.codusu ".
                           "  FROM sigesp_dt_prefijos ". 
                           " INNER JOIN sigesp_prefijos".
			   "    ON sigesp_dt_prefijos.codemp='".$this->codemp."' ".
		           "   AND sigesp_dt_prefijos.codsis='{$codsis}' ".
                           "   AND sigesp_dt_prefijos.procede='{$procede}' ".
			   "   AND sigesp_prefijos.estact=1".
			   "   AND sigesp_dt_prefijos.codemp=sigesp_prefijos.codemp ".
                           "   AND sigesp_dt_prefijos.id=sigesp_prefijos.id ".
		           "   AND sigesp_dt_prefijos.codsis=sigesp_prefijos.codsis ".
                           "   AND sigesp_dt_prefijos.procede=sigesp_prefijos.procede ".
                           "   AND sigesp_dt_prefijos.prefijo=sigesp_prefijos.prefijo ";
		$dataPrefijo = ConexionBaseDatos::$conexionbd->Execute ($cadenaSQL);
		if($dataPrefijo->_numOfRows > 0)
		{
			if (!$dataPrefijo->EOF)
			{
				$prefijo = true;
			}
		}
		unset($dataPrefijo);
		return $prefijo;
	}
	
	public function numeroinicial($campo)
	{
		$nroini=0;
		if($campo=='')
		{
			return $nroini;
		}
		$cadenasql="SELECT ".$campo." as campo ".
				   "  FROM sigesp_empresa ".
				   " WHERE codemp='".$this->codemp."'";
		$data = ConexionBaseDatos::$conexionbd->Execute ($cadenasql);
		if($data===false)
		{
			return $nroini;
		}
		else
		{
			if(!$data->EOF)
			{
				$nroini=$data->fields['campo'];
			}
		}
		return $nroini;
	} 

	public function verificarNumero($codigo, $validarempresa, $longitud, $arrFiltro, $codsis, $procede, $codusu, $campoinicial,$numero)
	{
		$valido=false;
		$nroact='';
		$nroant=$numero;
		$filtro='';
		if($validarempresa)
		{		
			$filtro = " WHERE codemp='{$this->codemp}' ";
		}
		if (!empty($arrFiltro))
		{
			foreach ($arrFiltro as $campo => $valor)
			{
				if ($filtro == '')
				{
					//Recordar validar el tipo de dato para colocar o no las comillas
					$filtro .= " WHERE {$campo} = '{$valor}'"; 
				}
				else
				{
					$filtro .= " AND {$campo} = '{$valor}'";
				}
			}
		}
		$valorDefecto = str_pad('-', $longitud, '-', STR_PAD_LEFT);
		if ($filtro == '')
		{
			$filtro .= " WHERE {$codigo} <> '{$valorDefecto}'";
			$filtro .= "   AND {$codigo} = '{$numero}'";
		}
		else
		{
			$filtro .= "   AND {$codigo} <> '{$valorDefecto}'";
			$filtro .= "   AND {$codigo} = '{$numero}'";
		}
		$cadenaSql="SELECT ".$codigo."".
				   "  FROM {$this->_table} ".
				   " {$filtro} ";
		$resultado = ConexionBaseDatos::$conexionbd->Execute ($cadenaSql);
		if($resultado===false)
		{
		}
		else
		{
			$valido=true;
			if(!$resultado->EOF)
			{
				$numero=$this->buscarCodigo($codigo, $validarempresa, $longitud, $arrFiltro, $codsis, $procede, $codusu, $campoinicial);
			}
			else
			{
				if($nroant!=$numero)
				{
					//$this->io_mensajes->message("Se le Asignó un nuevo número de documento el cual es :".$as_numero);
				}
			}
		}
		$arrResultado['numero']=$numero;
		$arrResultado['valido']=$valido;
		return $arrResultado;
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $cadenasql
	 * @author Ing. Gerardo Cordero
	 */
	public function buscarSql($cadenasql) {
		return $resultado = ConexionBaseDatos::$conexionbd->Execute ( $cadenasql );
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $cadenasql
	 * @author Ing. Gerardo Cordero
	 */
	public function ejecutarSql($cadenasql) {
		return $resultado = ConexionBaseDatos::$conexionbd->Execute ( $cadenasql );
	}
	
	/**
	 * Enter description here ...
	 * @author Ing. Gerardo Cordero
	 */
	public function obtenerArregloPk() {
		return ConexionBaseDatos::$conexionbd->MetaPrimaryKeys ($this->_table);
	}
	
	public function obtenerConexionBd() {
		return ConexionBaseDatos::$conexionbd;
	}
		
	/**
	 * Enter description here ...
	 * @param unknown_type $cadena
	 * @author Ing. Gerardo Cordero
	 */
	public function concatenarCadena($cadena) {
		$longitud = count($cadena);
		$tira = "";
		$i=0;
		$j=1;
		while($j < $longitud)
		{
			
			$tiraaux = ConexionBaseDatos::$conexionbd->Concat($cadena[$i],$cadena[$j]);
			if($tira != "")
			{
				$tira 	= ConexionBaseDatos::$conexionbd->Concat($tira,$tiraaux);
			}
			else
			{
				$tira = $tiraaux;
			}
			$i=$j+1;
			$j=$i+1;
		}
		if(($longitud%2)!=0) 
		{
			$tira 	= ConexionBaseDatos::$conexionbd->Concat($tira,$cadena[$i]);	
		}	
		return ConexionBaseDatos::$conexionbd->Concat($tira);
	}
	
	/****************************************************************************************************
	 *Funcion: buscarSqlLimitado
	 *Argumentos: $restricciones // Arreglo que contiene restricciones para los registros a devolver
	 *            $arrcampos     // Arreglo que contiene los campos a devolver durante la consulta
	 *            $numregsitros  // Cantidad que indica enl numero de registros a devolver
	 *            $reginicio     // Registro desde el que se comenzara a hacer la busqueda
	 *            $arreglo       // Arreglo para el control interno de la funcion
	 *            $tabla		 // Tabla auxiliar para realizar la busqueda en una distinta a la instanciada
	 * Descripcion: Funcion que se encarga de realizar un busqueda filtrada de registros, indicando el
	 *              numero de registros a devolver y desde cual se tomara en cuenta.
	 ****************************************************************************************************/
	public function buscarSqlLimitado($restricciones,$arrcampos,$numregistros,$reginicio,$arreglo=false,$tabla="")
	{
		$modelo = "";
		$campos = implode(',',$arrcampos);
		foreach ( $restricciones as $restriccion ) {
				$campo = $restriccion [0];
				$evaluador = $restriccion [1];
				$valor = $restriccion [2];
				$andor = $restriccion [3];
							
				if($evaluador == 'ORDER BY'){
					$modelo .= $evaluador . " " . $campo . "  " . $valor;				
				}else{
					$modelo .= $campo . " " . $evaluador . " '" . $valor . "'";	
				}
				
				if ($andor == 0) {
					$modelo .= " AND ";
				} elseif ($andor == 1) {
					$modelo .= " OR ";
				} elseif ($andor == 2) {
					$modelo .= " ";
				}
			}
		if(!empty($tabla))
		{
			$sentencia = "SELECT ".$campos." FROM {$tabla}";	
		}
		else
		{
			$sentencia = "SELECT ".$campos." FROM {$this->_table}";
		}	
		
			
		if(!empty($modelo))
		{
			$sentencia .= " WHERE ".$modelo;
		}
		return ConexionBaseDatos::$conexionbd->SelectLimit($sentencia,$numregistros,$reginicio,$arreglo);
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $restricciones
	 * @param unknown_type $banderatabla
	 * @param unknown_type $tabla
	 * @author Ing. Gerardo Cordero
	 */
	public function borrarCampoRestriccion($restricciones,$banderatabla=false,$tabla='') {
		$modelo = "";
				
		foreach ( $restricciones as $restriccion ) {
			$campo = $restriccion [0];
			$evaluador = $restriccion [1];
			$valor = $restriccion [2];
			$andor = $restriccion [3];
						
			$modelo .= $campo . " " . $evaluador . " '" . $valor . "'";	
			
			if ($andor == 0) {
				$modelo .= " AND ";
			} elseif ($andor == 1) {
				$modelo .= " OR ";
			} elseif ($andor == 2) {
				$modelo .= " ";
			}
		}
		
        if(!$banderatabla){
        	return $resultado = ConexionBaseDatos::$conexionbd->Execute ( "delete from {$this->_table} where " . $modelo );
				
        }
		else{
			$resultado = ConexionBaseDatos::$conexionbd->Execute ( "delete from {$tabla} where " . $modelo );
		}
		
	}
	
	public function deleteDetalle($tabdetalle){
		$restriccion = array();
		$arrpk       = $this->obtenerArregloPk();
		$arrcampos   = $this->getAttributeNames();
		$cantcampo   = count($arrpk);
		$cantpk      = 0;
		foreach ($arrpk as $campopk =>$indicepk) {
			$cantpk++;
			foreach ($arrcampos as $regcampo => $indicecampo) {
				if($indicepk==$indicecampo){
					if($cantcampo==$cantpk){
						$restriccion[$cantpk][0] = $indicepk;
                    	$restriccion[$cantpk][1] = '=';
                    	$restriccion[$cantpk][2] = $this->$indicepk;
                    	$restriccion[$cantpk][3] = 2;
					}else{
						$restriccion[$cantpk][0] = $indicepk;
                    	$restriccion[$cantpk][1] = '=';
                    	$restriccion[$cantpk][2] = $this->$indicepk;
                    	$restriccion[$cantpk][3] = 0;
					}
				}
			}
		}
		
		return $this->borrarCampoRestriccion($restriccion,true,$tabdetalle);
		
	}

	public function setDataNull()
	{
		$arratributos = $this->GetAttributeNames();
		foreach ( $arratributos as $IndiceDAO )
		 {
			if ($this->$IndiceDAO === null || $this->$IndiceDAO === '')
			{
					$this->$IndiceDAO = null;
			}
		}
	}

	public function buscarCodigoSinPrefijo($codigo, $validarempresa=true, $longitud=0, $arrFiltro=array(), $codsis='', $procede='', $codusu='')
	{
		$filtro = '';
		if($validarempresa)
		{		
			$filtro = " WHERE codemp='{$this->codemp}' ";
		}
		if (!empty($arrFiltro))
		{
			foreach ($arrFiltro as $campo => $valor)
			{
				if ($filtro == '')
				{
					//Recordar validar el tipo de dato para colocar o no las comillas
					$filtro .= " WHERE {$campo} = '{$valor}'"; 
				}
				else
				{
					$filtro .= " AND {$campo} = '{$valor}'";
				}
			}
		}
		$valorDefecto = str_pad('-', $longitud, '-', STR_PAD_LEFT);
		if ($filtro == '')
		{
			$filtro .= " WHERE {$codigo} <> '{$valorDefecto}'";
		}
		else
		{
			$filtro .= " AND {$codigo} <> '{$valorDefecto}'";
		}
		$campo="";
		switch ($_SESSION["ls_gestor"])
		{
			case 'MYSQLT':
				$campo =" MAX($codigo) as codigo ";
				break;

			case 'MYSQLI':
				$campo =" MAX($codigo ) as codigo ";
				break;
			
			case 'POSTGRES':
				$campo =" MAX($codigo)  as codigo ";
				break;
		}
		$cadenaSQL = "SELECT ".$campo." FROM {$this->_table} {$filtro} ";
		$resultado = ConexionBaseDatos::$conexionbd->Execute ($cadenaSQL);
		if ($resultado->fields ['codigo'] == '')
		{
			if ($longitud!=0)
			{
				return agregarUno(0, $longitud);
			}
			else
			{
				return 0;
			}
		} 
		else
		{
			if ($longitud!=0)
			{
				return agregarUno($resultado->fields ['codigo'], $longitud);
			}
			else
			{
				return $resultado->fields ['codigo'];
			}
		}
	}
}
?>