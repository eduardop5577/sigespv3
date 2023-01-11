<?php
/***********************************************************************************
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_fabricadao.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_conexion.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');

class ServicioIntSigFonas
{
	public  $mensaje;
	public  $errorSQL; 
	private $conexionBaseDatos; 
	private $conexionAlterna;
		
	public function __construct()
	{
		$this->mensaje             = null;
		$this->errorSQL            = null;
		$this->conexionBaseDatos   = null;
		$this->conexionAlterna     = null;
	}
	
	public function buscarPersonalSIGESP($cedper, $nomper, $apeper, $codact) {
		$dataComprobante = null;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		
		$filtro = '';
		if (!empty($cedper)) {
			$filtro .= " AND cedper LIKE '%{$cedper}%' ";
		}
		
		if (!empty($nomper)) {
			$filtro .= " AND nomper ILIKE '%{$nomper}%' ";
		}
		
		if (!empty($apeper)) {
			$filtro .= " AND apeper ILIKE '%{$apeper}%' ";
		}
		
		if ($codact == 'M') {
			$cadenaSQL  = "SELECT PER.codper, PER.cedper, PER.nomper, PER.apeper, PER.dirper, PER.fecnacper, PER.edocivper, PER.telhabper, PER.telmovper, PER.sexper,
								  PER.estaper, PER.pesper, EST.desest AS lugnac, PER.nacper, PER.coreleper, COUNT(FAM.nomfam) AS numfam, PER.migfonas
								FROM sno_personal PER
									LEFT OUTER JOIN sigesp_estados EST ON PER.codpainac=EST.codpai AND PER.codestnac = EST.codest
									LEFT OUTER JOIN sno_familiar FAM ON PER.codemp=FAM.codemp AND PER.codper=FAM.codper
								WHERE (FAM.migfonas = '0' OR PER.migfonas = '0') AND estper = '1' {$filtro}
								GROUP BY PER.codper, PER.cedper, PER.nomper, PER.apeper, PER.dirper, PER.fecnacper, PER.edocivper, PER.telhabper, PER.telmovper, PER.sexper,
										 PER.estaper, PER.pesper, EST.desest, PER.nacper, PER.coreleper,PER.migfonas
								ORDER BY PER.cedper";
    	}
    	else if($codact == 'B') {
    		$cadenaSQL  = "SELECT PER.codper, PER.cedper, PER.nomper, PER.apeper, PER.dirper, PER.fecnacper, PER.edocivper, PER.telhabper, PER.telmovper, PER.sexper,
    							  PER.estaper, PER.pesper, EST.desest AS lugnac, PER.nacper, PER.coreleper, COUNT(FAM.nomfam) AS numfam
    							FROM sno_personal PER
    								LEFT OUTER JOIN sigesp_estados EST ON PER.codpainac=EST.codpai AND PER.codestnac = EST.codest
    								LEFT OUTER JOIN sno_familiar FAM ON PER.codemp=FAM.codemp AND PER.codper=FAM.codper
    							WHERE PER.migfonas = '1' AND estper <> '1' {$filtro}
    							GROUP BY PER.codper, PER.cedper, PER.nomper, PER.apeper, PER.dirper, PER.fecnacper, PER.edocivper, PER.telhabper, PER.telmovper, PER.sexper,
    									 PER.estaper, PER.pesper, EST.desest, PER.nacper, PER.coreleper
    							ORDER BY PER.cedper";
    	}
		
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function existeTitular($cedper) {
		$existe = false;
		$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
		$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT2'], $arrConfi['LOGG_DBALT2'], $arrConfi['PASS_DBALT2'],
				                                                      $arrConfi['NOMB_DBALT2'], $arrConfi['GEST_DBALT2'], $arrConfi['PORT_DBALT2']);
		$cadenaSQL = "SELECT COD_IDENT_TITULAR
						FROM FONAS.BFONAS_T_BENEFICIARIO 
						WHERE COD_IDENT_TITULAR = '{$cedper}'";
		$rsData = $this->conexionAlterna->Execute($cadenaSQL);
		if ($rsData->_numOfRows > 0) {
			$existe = true;
		}
		unset($rsData);
		
		return $existe;
	}
	
	public function existeBeneficiario($cedper) {
		$existe = false;
		$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
		$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT2'], $arrConfi['LOGG_DBALT2'], $arrConfi['PASS_DBALT2'],
																	  $arrConfi['NOMB_DBALT2'], $arrConfi['GEST_DBALT2'], $arrConfi['PORT_DBALT2']);
		$cadenaSQL = "SELECT COD_IDENT_TITULAR
						FROM FONAS.BFONAS_T_BENEFICIARIO
						WHERE NRO_CEDULA_BENEF = '{$cedper}'";
		$rsData = $this->conexionAlterna->Execute($cadenaSQL);
		if ($rsData->_numOfRows > 0) {
			$existe = true;
		}
		unset($rsData);
		
		return $existe;
	}
	
	public function insertarPersonalFONAS($arrBen) {
		$respuesta = true;
		$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
		$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT2'], $arrConfi['LOGG_DBALT2'], $arrConfi['PASS_DBALT2'],
																	  $arrConfi['NOMB_DBALT2'], $arrConfi['GEST_DBALT2'], $arrConfi['PORT_DBALT2']);
		//$this->conexionAlterna->debug = true;
		$COD_IDENT_TITULAR = 'NULL';
		if (!empty($arrBen['cedtit'])) {
			$COD_IDENT_TITULAR = "'{$arrBen['cedtit']}'";
		}
		$COD_IDENT_BENEF = 'NULL';
		if (!empty($arrBen['cedben'])) {
			$COD_IDENT_BENEF = "'{$arrBen['cedben']}'";
		} 
		$IND_TIPO_BENEF = 'NULL';
		if (!empty($arrBen['tipben'])) {
			$IND_TIPO_BENEF = "'{$arrBen['tipben']}'";
		}
		$PRIMER_NOMBRE_BENEF = 'NULL';
		if (!empty($arrBen['nomper'])) {
			$PRIMER_NOMBRE_BENEF = "'{$arrBen['nomper']}'";
		}
		$PRIMER_APELLIDO_BENEF = 'NULL';
		if (!empty($arrBen['apeper'])) {
			$PRIMER_APELLIDO_BENEF = "'{$arrBen['apeper']}'";
		}
		$FECHA_NACIMIENTO = 'NULL';
		if (!empty($arrBen['fecnac'])) {
			$FECHA_NACIMIENTO = "TO_DATE('{$arrBen['fecnac']}','MM/DD/YYYY HH24:MI:SS')";
		}
		$NACIONALIDAD = 'NULL';
		if (!empty($arrBen['nacper'])) {
			$NACIONALIDAD = "'{$arrBen['nacper']}'";
		}
		$LUGAR_NACIMIENTO = 'NULL';
		if (!empty($arrBen['lugnac'])) {
			$LUGAR_NACIMIENTO = "'{$arrBen['lugnac']}'";
		}
		$SEXO = 'NULL';
		if (!empty($arrBen['sexper'])) {
			$SEXO = "'{$arrBen['sexper']}'";
		}
		$EDO_CIVIL = 'NULL';
		if (!empty($arrBen['edociv'])) {
			$EDO_CIVIL = "'{$arrBen['edociv']}'";
		}
		$CORREO_ELECTRONICO = 'NULL';
		if (!empty($arrBen['coreleper'])) {
			$CORREO_ELECTRONICO = "'{$arrBen['coreleper']}'";
		}
		$PARENTESCO = 'NULL';
		if (!empty($arrBen['parper'])) {
			$PARENTESCO = "'{$arrBen['parper']}'";
		}
		$NRO_TELEF_HABITACION = 'NULL';
		if (!empty($arrBen['telhab'])) {
			$NRO_TELEF_HABITACION = "'{$arrBen['telhab']}'";
		}
		$NRO_TELEF_CELULAR = 'NULL';
		if (!empty($arrBen['telmov'])) {
			$NRO_TELEF_CELULAR = "'{$arrBen['telmov']}'";
		}
		$DESC_DIRECCION = 'NULL';
		if (!empty($arrBen['dirper'])) {
			$DESC_DIRECCION = "'{$arrBen['dirper']}'";
		}
		$PESO = 'NULL';
		if (!empty($arrBen['pesper'])) {
			$PESO = "'{$arrBen['pesper']}'";
		}
		$TALLA = 'NULL';
		if (!empty($arrBen['talper'])) {
			$TALLA = "'{$arrBen['talper']}'";
		}
		$CONPAR = '0';
		if (!empty($arrBen['conpar'])) {
			$CONPAR = "{$arrBen['conpar']}";
		}
		$NRO_CEDULA_BENEF = 'NULL';
		if (!empty($arrBen['cedfam'])) {
			$NRO_CEDULA_BENEF = "{$arrBen['cedfam']}";
		}
		
		//DATA POR DEFECTO
		$COD_CIA = "'01'";
		$SUBTIP  = "'2'";
		$AUDIT_USUARIO_INGRESA = "'SIGESP_GESTOR'"; 
		$AUDIT_FECHA_INGRESA = "TO_DATE('".date('m/d/Y H:i:s')."','MM/DD/YYYY HH24:MI:SS')";
		
		//INSTRUCCION SQL INSERTANDO BENEFICIARIO
		$cadenaSQL = "INSERT INTO FONAS.BFONAS_T_BENEFICIARIO (COD_IDENT_TITULAR, COD_IDENT_BENEF, IND_TIPO_BENEF, PRIMER_NOMBRE_BENEF, 
															   PRIMER_APELLIDO_BENEF, FECHA_NACIMIENTO, NACIONALIDAD, LUGAR_NACIMIENTO, 
															   SEXO, EDO_CIVIL, CORREO_ELECTRONICO, PARENTESCO, NRO_TELEF_HABITACION,
															   NRO_TELEF_CELULAR, COD_CIA, SUBTIP, DESC_DIRECCION, PESO, TALLA, CONPAR, 
															   IND_STATUS_BENEF, NRO_CEDULA_BENEF, AUDIT_USUARIO_INGRESA, AUDIT_FECHA_INGRESA) 
						VALUES ({$COD_IDENT_TITULAR},{$COD_IDENT_BENEF},{$IND_TIPO_BENEF},{$PRIMER_NOMBRE_BENEF},{$PRIMER_APELLIDO_BENEF},
						        {$FECHA_NACIMIENTO},{$NACIONALIDAD},{$LUGAR_NACIMIENTO},{$SEXO},{$EDO_CIVIL},{$CORREO_ELECTRONICO},
								{$PARENTESCO},{$NRO_TELEF_HABITACION},{$NRO_TELEF_CELULAR},{$COD_CIA},{$SUBTIP},{$DESC_DIRECCION},
								{$PESO},{$TALLA},{$CONPAR},'ACTIVO',{$NRO_CEDULA_BENEF},{$AUDIT_USUARIO_INGRESA},{$AUDIT_FECHA_INGRESA})";
		
		if ($this->conexionAlterna->Execute($cadenaSQL) === false) {
			$respuesta = false;
			$this->errorSQL = $this->conexionAlterna->ErrorMsg();
		}
		
		return $respuesta;
	}
	
	public function obtenerTipoBene($codtippersss) {
		$tipben = 'E';
		switch ($codtippersss) {
			case '0000001':
				$tipben = 'E';
				break;
		
			case '0000002':
				$tipben = 'C';
				break;
		
			case '0000004':
				$tipben = 'O';
				break;
					
			case '0000005':
				$tipben = 'O';
				break;
				
			case '0000010':
				$tipben = 'J';
				break;
				
			case '0000011':
				$tipben = 'S';
				break;
				
			case '0000014':
				$tipben = 'P';
				break;
		}
		
		return $tipben;
	}
	
	public function buscarFamiliar($codper) {
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSQL = "SELECT cedula, nomfam, apefam, sexfam, fecnacfam, nexfam, cedfam
  						FROM sno_familiar
						WHERE codper = '{$codper}' AND migfonas = '0'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function calcularCONPAR($cedper) {
		$conpar = 0;
		$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
		$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT2'], $arrConfi['LOGG_DBALT2'], $arrConfi['PASS_DBALT2'],
																	  $arrConfi['NOMB_DBALT2'], $arrConfi['GEST_DBALT2'], $arrConfi['PORT_DBALT2']);
		$cadenaSQL = "SELECT MAX(CONPAR) AS CONPAR
						FROM FONAS.BFONAS_T_BENEFICIARIO
						WHERE COD_IDENT_TITULAR = '{$cedper}'";
		$rsData = $this->conexionAlterna->Execute($cadenaSQL);
		if (!$rsData->EOF) {
			$conpar = floatval($rsData->fields['CONPAR']);
			if ($conpar==0 || $conpar==0.00) {
				$conpar = 2;
			}
			else {
				$conpar++;
			}
		}
		unset($rsData);
	
		return $conpar;
	}
	
	public function marcarPersonalMigrado($codper) {
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSQL = "UPDATE sno_personal
						SET  migfonas = '1'
						WHERE codper = '{$codper}'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function marcarFamiliarMigrado($codper, $cedfam) {
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSQL = "UPDATE sno_familiar
						SET  migfonas = '1'
						WHERE codper = '{$codper}' AND cedfam = '{$cedfam}'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function actualizarBeneFonas($cedbene) {
		$respuesta = true;
		$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
		$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT2'], $arrConfi['LOGG_DBALT2'], $arrConfi['PASS_DBALT2'],
																	  $arrConfi['NOMB_DBALT2'], $arrConfi['GEST_DBALT2'], $arrConfi['PORT_DBALT2']);
		
		$cadenaSQL = "UPDATE FONAS.BFONAS_T_BENEFICIARIO 
						SET IND_STATUS_BENEF = 'INACTIVO' 
						WHERE COD_IDENT_TITULAR = '{$cedbene}'";
		if ($this->conexionAlterna->Execute($cadenaSQL) === false) {
				$respuesta = false;
				$this->errorSQL = $this->conexionAlterna->ErrorMsg();
		}
		
		return $respuesta;
	}
		
	public function procesarPersonal($objJson) {
		$respuesta = true;
		$nCom = count((array)$objJson->arrPersonal);
		if ($objJson->codact == 'M') {
			for($j=0;$j<=$nCom-1;$j++) {
				//INSERTANDO EMPLEADO
				$insEmp = true;
				if ($objJson->arrPersonal[$j]->migfonas == '0') {
					if (!$this->existeTitular($objJson->arrPersonal[$j]->cedper)) {
						if (!$this->existeBeneficiario($objJson->arrPersonal[$j]->cedper)) {
							$objFecNacPer = new DateTime($objJson->arrPersonal[$j]->fecnacper);
							$fechaNacPer = $objFecNacPer->format('m/d/Y H:i:s');
							$arrBen['cedtit'] = $objJson->arrPersonal[$j]->cedper;
							$arrBen['cedben'] = $objJson->arrPersonal[$j]->cedper;
							$arrBen['tipben'] = $this->obtenerTipoBene($objJson->arrPersonal[$j]->codtippersss);
							$arrBen['nomper'] = $objJson->arrPersonal[$j]->nomper;
							$arrBen['apeper'] = $objJson->arrPersonal[$j]->apeper;
							$arrBen['fecnac'] = $fechaNacPer;
							if ($objJson->arrPersonal[$j]->nacper == 'V') {
								$arrBen['nacper'] = 'VENEZOLANA';
							}
							else {
								$arrBen['nacper'] = 'EXTRANJERO';
							}
							$arrBen['lugnac'] = $objJson->arrPersonal[$j]->lugnac;
							$arrBen['sexper'] = $objJson->arrPersonal[$j]->sexper;
							$arrBen['edociv'] = $objJson->arrPersonal[$j]->edocivper;
							$arrBen['coreleper'] = $objJson->arrPersonal[$j]->coreleper;
							$arrBen['parper'] = '';
							$arrBen['telhab'] = $objJson->arrPersonal[$j]->telhabper;
							$arrBen['telmov'] = $objJson->arrPersonal[$j]->telmovper;
							$arrBen['dirper'] = $objJson->arrPersonal[$j]->dirper;
							$arrBen['pesper'] = $objJson->arrPersonal[$j]->pesper;
							$arrBen['talper'] = $objJson->arrPersonal[$j]->estaper;
							$arrBen['conpar'] = 0;
							$arrBen['cedfam'] = '';
		
							$insEmp = $this->insertarPersonalFONAS($arrBen);
							if ($insEmp) {
								$this->marcarPersonalMigrado($objJson->arrPersonal[$j]->codper);
							}
							else {
								$this->mensaje = 'Error insertando a el empleado '.$objJson->arrPersonal[$j]->cedper;
							}
						}
						else {
							$this->mensaje = 'El empleado '.$objJson->arrPersonal[$j]->cedper.' esta registrado como beneficiario';
						}
					}
					else {
					 	$this->mensaje = 'El empleado '.$objJson->arrPersonal[$j]->cedper.'ya fue registrado';
					}
				}
				
				//INSERTANDO FAMILIARES DEL EMPLEADO
				if ($insEmp) {
					if ($objJson->arrPersonal[$j]->nacper == 'V') {
						$nacfam = 'VENEZOLANA';
					}
					else {
						$nacfam = 'EXTRANJERO';
					}
						
					$x = 1;
					$dataFamiliar = $this->buscarFamiliar($objJson->arrPersonal[$j]->codper);
					while (!$dataFamiliar->EOF) {
						$arrBen['cedfam'] = $dataFamiliar->fields['cedula'];
						if (!$this->existeBeneficiario($arrBen['cedfam'])) {
							$objFecNacFam = new DateTime($dataFamiliar->fields['fecnacfam']);
							$fechaNacFam  = $objFecNacFam->format('m/d/Y H:i:s');
							$arrBen['cedtit'] = $objJson->arrPersonal[$j]->cedper;
							$arrBen['cedben'] = $objJson->arrPersonal[$j]->cedper.'-'.$x;
							$arrBen['tipben'] = 'F';
							$arrBen['nomper'] = $dataFamiliar->fields['nomfam'];
							$arrBen['apeper'] = $dataFamiliar->fields['apefam'];
							$arrBen['fecnac'] = $fechaNacFam;
							$arrBen['nacper'] = $nacfam;
							$arrBen['lugnac'] = null;
							$arrBen['sexper'] = $dataFamiliar->fields['sexfam'];
							$arrBen['edociv'] = null;
							$arrBen['coreleper'] = null;
							$arrBen['telhab'] = null;
							$arrBen['telmov'] = null;
							$arrBen['dirper'] = null;
							$arrBen['pesper'] = null;
							$arrBen['talper'] = null;
						
							$parper = '';
							$conpar = 0;
							switch ($dataFamiliar->fields['nexfam']) {
								case 'C':
									$parper = 'CON';
									$conpar = 1;
									break;
								case 'H':
									$parper = 'HIO';
									$conpar = $this->calcularCONPAR($objJson->arrPersonal[$j]->cedper);
									break;
								case 'E':
									$parper = 'HNA';
									$conpar = $this->calcularCONPAR($objJson->arrPersonal[$j]->cedper);
									break;
								case 'P':
									if ($dataFamiliar->fields['sexfam'] == 'F') {
										$parper = 'MAD';
									}
									else {
										$parper = 'PAD';
									}
									$conpar = $this->calcularCONPAR($objJson->arrPersonal[$j]->cedper);
									break;
							}
						
							$arrBen['parper'] = $parper;
							$arrBen['conpar'] = $conpar;
							$arrBen['cedfam'] = $dataFamiliar->fields['cedula'];
						
							$x++;
							if (!$this->insertarPersonalFONAS($arrBen)) {
								$this->mensaje = 'Error insertando familiar del empleado '.$objJson->arrPersonal[$j]->cedper;
								$respuesta = false;
								break;
							}
							else {
								$this->marcarFamiliarMigrado($objJson->arrPersonal[$j]->codper, $dataFamiliar->fields['cedfam']);
							}
						}
					
						$dataFamiliar->MoveNext();
					}
					unset($dataFamiliar);
				}
				else {
					$respuesta = false;
				}
			}
		}
		else if($objJson->codact == 'B') {
			for($j=0;$j<=$nCom-1;$j++) {
				if ($this->existeTitular($objJson->arrPersonal[$j]->cedper)) {
					if (!$this->actualizarBeneFonas($objJson->arrPersonal[$j]->cedper)) {
						$this->mensaje = 'Error al darle de baja al empleado '.$objJson->arrPersonal[$j]->cedper;
						$respuesta = false;
						break;
					}
				}	
			}
		}
		
		$this->mensaje .= '  '.$this->errorSQL;
		
		return $respuesta;
	}
}
?>