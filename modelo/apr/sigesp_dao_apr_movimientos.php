<?php
/***********************************************************************************
* @Modelo para el traspaso de datos básicos
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_registroeventos.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_registrofallas.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistema.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/cfg/sigesp_srv_cfg_configuracion.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sno/sigesp_dao_sno_nomina.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_conexion.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_funciones.php');

class Movimientos extends DaoGenerico
{
	public $archivo;
	public $mensaje;
	public $valido= true;
	public $existe;
	public $tablas = Array();
	public $campos = Array();
	public $criterio;
	public $sistema;
	public $tipo;
	public $codsis;
	public $nomfisico;
	public $consultaactual = "";
	private $objlibcon;
	private $conexionbdorigen;
	private $conexionbd;

	public function __construct() {
		parent::__construct ( 'sigesp_config' );
		$this->conexionbd = $this->obtenerConexionBd(); 
		$this->objlibcon = new ConexionBaseDatos();
	}
	
/***********************************************************************************
* @Función que busca en la base de datos origen los campos y los verifica en la destino
* @parametros: 
* @retorno: 
* @fecha de creación: 21/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function procesarMovimientos()
	{	
		$this->conexionbdorigen = $this->objlibcon->conectarBD($_SESSION['sigesp_servidor_apr'], $_SESSION['sigesp_usuario_apr'], $_SESSION['sigesp_clave_apr'],
									   $_SESSION['sigesp_basedatos_apr'], $_SESSION['sigesp_gestor_apr'], $_SESSION['sigesp_puerto_apr']);
		
		$this->mensaje='Realizó el traspaso de movimientos del sistema '.$this->sistema;		
		$this->conexionbd->StartTrans();
		try 
		{ 
			// Se recorre el arreglo de tablas por sistema.
			$total=count((array)$this->tablas);
			for ( $contador = 0; (($contador < $total) && $this->valido); $contador++ )
			{
				$this->_table=$this->tablas[$contador]['tabla'];
				$this->criterio=$this->tablas[$contador]['criterio'];
				$this->tipo=$this->tablas[$contador]['tipo'];
				$this->consultaactual='';
				$totalorigen=0;
				$totaldestino=0;
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				escribirArchivo($this->archivo,'*		Conversión tabla '.$this->_table);
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				
				// Verifico que la tabla Exista en el origen.
				$this->verificarExistenciaTabla($this->conexionbdorigen,$this->conexionbd);
				if (($this->valido) && ($this->existe))
				{
					// Obtengo los datos de la base de datos de origen según la configuració cargada
					$result = $this->obtenerDatosOrigen($this->conexionbdorigen);
					if ((!$result->EOF) && $this->valido)
					{						
						$result->MoveFirst();
						$this->cargarCampos($result,$this->conexionbd);
						$result->MoveFirst();
						$totcolumna=count((array)$result->FetchRow());
						$result->MoveFirst();
						while ((!$result->EOF) && $this->valido)
						{
							$totalorigen++;
							$cadenacampos  = '';
							$cadenavalores = '';
							$consulta      = '';							
							for ($columna = 0; (($columna < $totcolumna) && $this->valido); $columna++)
							{
								$tipodato   = '';
								$valor      = '';
								$objeto     = $result->FetchField($columna);
								$campo      = $objeto->name;
								$tipodato   = $result->MetaType($objeto->type);
								$valor = $result->fields[$objeto->name];		
								$clave = array_search($campo, $this->campos);
								if (is_numeric($clave))
								{		
									// Actualizo el valor según el tipo de dato
									$valor=$this->actualizarValor($tipodato,$valor);
									if(($this->campos[$columna] == 'numconint') && (substr($this->_table,0,4) == 'scb_') && ($valor="''"))
									{										
									}
									else
									{
										switch($this->tipo)
										{
											case 'INSERT':
												$cadenacampos.=','.$this->campos[$columna];
												$cadenavalores.=','.$valor;
											break;
											
											case 'UPDATE':
												$cadenavalores.=','.$this->campos[$columna].'='.$valor;
											break;							
										}
									}	
								}
							}
							switch($this->tipo)
							{
								case 'INSERT':
									$consulta='INSERT INTO '.$this->_table.' ('.substr($cadenacampos,1).')'.
											  ' VALUES ('.substr($cadenavalores,1).')';
								break;
								
								case 'UPDATE':
									$consulta='UPDATE '.$this->_table.' '.
											  '   SET '.substr($cadenavalores,1).' '.
											  $this->criterio;
								break;							
							}
							if ($consulta != '')
							{
								$this->consultaactual = $consulta;
								// Ejecuto la Consulta en la Base de Datos Destino.
								$resultado = $this->conexionbd->Execute($consulta);
								if($resultado==false)
								{
									escribirArchivo($this->archivo,'*******************************************************************************************************');
									escribirArchivo($this->archivo,'* Ocurrio un error en la Transferencia. ');
									escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
									escribirArchivo($this->archivo,'* Error BD Origen  '.$this->conexionbdorigen->ErrorMsg());
									escribirArchivo($this->archivo,'* Error BD Destino '.$this->conexionbd->ErrorMsg());
									escribirArchivo($this->archivo,'* Tabla '.$this->_table);
									escribirArchivo($this->archivo,'* Sistema '.$this->sistema);
									escribirArchivo($this->archivo,'* Consulta '.$this->consultaactual);
									escribirArchivo($this->archivo,'*******************************************************************************************************');

								}
							} 							
							$result->MoveNext();
							$totaldestino++;
						}
						$result->Close();
					}	
				}
				escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
				escribirArchivo($this->archivo,'* Registros Origen  '.$this->valornuevo.'-> '.$totalorigen);
				escribirArchivo($this->archivo,'* Registros Destino '.$this->valornuevo.'-> '.$totaldestino);
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				escribirArchivo($this->archivo,'');
			}
			$objconfiguracion = new Configuracion();
			$objconfiguracion->codemp = $this->codemp;
			$objconfiguracion->codsis = $this->sistema;
			$objconfiguracion->seccion = 'MOVIMIENTO';
			$objconfiguracion->entry = 'MOVIMIENTO';
			$objconfiguracion->type = 'C';
			$objconfiguracion->value = 'TRUE';
			$objconfiguracion->incluirLocal();
			unset($objconfiguracion);
		}
		catch (exception $e) 
		{
			$this->valido = false;
			$this->mensaje='Ocurrio un error en la Transferencia. '.$this->conexionbdorigen->ErrorMsg().' '.$this->conexionbd->ErrorMsg();
			escribirArchivo($this->archivo,'* Ocurrio un error en la Transferencia. ');
			escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
			escribirArchivo($this->archivo,'* Error BD Origen  '.$this->conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'* Error BD Destino '.$this->conexionbd->ErrorMsg());
			escribirArchivo($this->archivo,'* Consulta '.$this->consultaactual);
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		$this->conexionbd->CompleteTrans($this->valido);
		$_SESSION['session_activa']=time();
		$this->incluirSeguridad('PROCESAR',$this->valido);
		unset($this->conexionbdorigen);
	}
	

/***********************************************************************************
* @Función que verifica si la tabla existe
* @parametros: 
* @retorno: 
* @fecha de creación: 22/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function  verificarExistenciaTabla($conexionorigen,$conexionbd)
	{
		$this->existe=true;
		$this->valido=true;
		$tablas =$conexionorigen->MetaTables('TABLES');
		$clave = array_search($this->_table, $tablas);
		if (!is_numeric($clave))
		{	
			$this->existe=false;
			$validos = array(0=>'sss_usuariosdetalle', 1=>'siv_dt_transferencia_scg', 2=>'siv_produccion', 3=>'siv_dt_produccion', 4=>'siv_dt_produccion_scg',
							 5=>'siv_dt_spg', 6=>'siv_empaquetado', 7=>'siv_dt_empaquetado', 8=>'siv_dt_empaquetado_scg', 9=>'scv_tarifacargos',
							 10=>'scv_dt_tarifacargos', 11=>'scv_incremento', 12=>'scv_dt_incremento', 13=>'scv_cargafamiliar', 14=>'scv_catcargos',
							 15=>'scv_dt_catcargos', 16=>'sob_cargoanticipo', 17=>'sob_cuentaanticipo',  18=>'cxp_dc_spi', 19=>'scb_movimientoconciliar',
							 20=>'sigesp_cmp_int', 21=>'spg_dt_cmp_int', 22=>'siv_asignacion', 23=>'siv_dt_asignacion');
			$clave = array_search($this->_table, $validos);
			if (!is_numeric($clave))
			{	
				escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
				escribirArchivo($this->archivo,'* La tabla '.$this->_table.' No Existe en la Base de Datos Origen. Debe Ejecutar el Release.');
				$this->valido=false;
				$this->mensaje = ' La tabla '.$this->_table.' No Existe en la Base de Datos Origen. Debe Ejecutar el Release.';
			}		
		}		

		unset($tablas);
		unset($clave);
		$tablas =$conexionbd->MetaTables('TABLES');
		$clave = array_search($this->_table, $tablas);
		if (!is_numeric($clave))
		{	
			escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
			escribirArchivo($this->archivo,'* La tabla '.$this->_table.' No Existe en la Base de Datos DEstino. Debe Ejecutar el Release.');
			$this->valido=false;
			$this->mensaje = ' La tabla '.$this->_table.' No Existe en la Base de Datos Destino. Debe Ejecutar el Release.';
		}		
	}	
	
	
/***********************************************************************************
* @Función que Obtiene los registros de la Base de Datos Origen
* @parametros: 
* @retorno: 
* @fecha de creación: 22/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* 
* @descripción:
* @autor:
***********************************************************************************/
	function  obtenerDatosOrigen($conexionorigen)
	{
		$consulta = 'SELECT * '.
					'  FROM '.$this->_table.' '.
					$this->criterio;
		$result = $conexionorigen->Execute($consulta);
		if($conexionorigen->HasFailedTrans())
		{
			escribirArchivo($this->archivo,'* Ocurrio un error en la Transferencia. '.$consulta);
			escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
			escribirArchivo($this->archivo,'* '.$conexionorigen->ErrorMsg());
			$this->valido=false;
			$this->mensaje=' Ocurrio un error en la Transferencia.'.$conexionorigen->ErrorMsg();
		}
		return $result;
	}	

	
/***********************************************************************************
* @Función que Obtiene y validad los campos de la base de datos origen
* @parametros: 
* @retorno: 
* @fecha de creación: 22/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function  cargarCampos($result,$conexionbd)
	{
		$totcolumna=count((array)$result->FetchRow());
		$this->campos = Array();
		for ($columna = 0; (($columna < $totcolumna) && $this->valido); $columna++)
		{
			$campo = '';
			$objeto = $result->FetchField($columna);
			$campo  = $objeto->name;		
			$campo=$this->verificarExistenciaCampo($campo,$this->conexionbd);
			if ($this->existe && $this->valido)
			{
				$this->campos[$columna] = $campo;
			}
		}
	}	

		
/***********************************************************************************
* @Función que verifica si el campo a insertar existe en el modelo nuevo
* @parametros: 
* @retorno: 
* @fecha de creación: 22/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function  verificarExistenciaCampo($campo,$conexionbd)
	{
		$this->existe=false;
		$campos =$this->conexionbdorigen->MetaColumnNames($this->_table);
		if ($campos[strtoupper($campo)]===$campo)
		{
			$this->existe=true;
		}
		if (!$this->existe)
		{
			$this->mensaje=' El campo '.$campo.' en la tabla '.$this->_table.' No Existe en la Base de Datos Origen.';
			escribirArchivo($this->archivo,'* Ocurrio un error en la Transferencia. ');
			escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
			escribirArchivo($this->archivo,'* El campo '.$campo.' en la tabla '.$this->_table.' No Existe en la Base de Datos Origen.');
			escribirArchivo($this->archivo,'* Error BD Origen  '.$this->conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'* Error BD Destino '.$this->conexionbd->ErrorMsg());
			escribirArchivo($this->archivo,'* Consulta '.$this->consultaactual);
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		unset($campos);
		$this->existe=false;
		$campos =$this->conexionbd->MetaColumnNames($this->_table);
		if ($campos[strtoupper($campo)]===$campo)
		{
			$this->existe=true;
		}
		else
		{
			// Verificar que el campo que no existe sea válido
			switch($this->_table)
			{
				case 'scv_solicitudviatico':
					$validos = array(0=>'monsolviaaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'sep_solicitud':
					$validos = array(0=>'firnivsol', 1=>'firnivadm', 2=>'firnivpre');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'scb_movbco':
					$validos = array(0=>'montoaux', 1=>'monobjretaux', 2=>'monretaux', 3=>'aliidbaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'scb_movbco_scg':
					$validos = array(0=>'montoaux', 1=>'monobjretaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'sno_programacionreporte':
					$validos = array(0=>'totasiaux', 1=>'moneneaux', 2=>'monfebaux', 3=>'monmaraux', 4=>'monabraux', 5=>'monmayaux', 
									 6=>'monjunaux', 7=>'monjulaux', 8=>'monagoaux', 9=>'monsepaux', 10=>'monoctaux', 11=>'monnovaux', 
									 12=>'mondicaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'sno_dt_scg':
					$validos = array(0=>'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'sno_dt_spg':
					$validos = array(0=>'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'sigesp_cmp':
					$validos = array(0=>'totalaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'scg_dt_cmp':
					$validos = array(0=>'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'spg_dt_cmp':
					$validos = array(0=>'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'spi_dt_cmp':
					$validos = array(0=>'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				default:
					$this->valido=false;
				break;
			}
		}
		if (!$this->valido)
		{
			$this->mensaje=' El campo '.$campo.' en la tabla '.$this->_table.' No Existe en la Base de Datos Destino.';
			escribirArchivo($this->archivo,'* Ocurrio un error en la Transferencia. ');
			escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
			escribirArchivo($this->archivo,'* El campo '.$campo.' en la tabla '.$this->_table.' No Existe en la Base de Datos Destino.');
			escribirArchivo($this->archivo,'* Error BD Origen  '.$this->conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'* Error BD Destino '.$this->conexionbd->ErrorMsg());
			escribirArchivo($this->archivo,'* Consulta '.$this->consultaactual);
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		return $campo;
	}	
	
	
/***********************************************************************************
* @Función que actualiza el valor según su tipo de datos
* @parametros: 
* @retorno: 
* @fecha de creación: 21/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function  actualizarValor($tipodato,$valor)
	{
		switch($tipodato)
		{
			case 'C':		
				$valor=rtrim($valor);		
				if($valor=='')
				{
					$valor="''";
				}
				elseif($valor=='(null)')
				{
					$valor="''";
				}
				elseif(is_string($valor)===false)
				{
					$valor="''";
				}
				else
				{
					$valor = str_replace("'","`",$valor);
					$valor = str_replace("\\","",$valor);
					$valor="'".$valor."'";
				}
			break;

			case 'D':
				$valor=str_replace('/','-',$valor);
				if($valor=='')
				{
					$valor="1900-01-01";
				}
				elseif($valor=='(null)')
				{
					$valor="1900-01-01";
				}
				$ls_dia=substr($valor,8,2);
				$ls_mes=substr($valor,5,2);
				$ls_ano=substr($valor,0,4);
				if(checkdate($ls_mes,$ls_dia,$ls_ano)===false)
				{
					 $valor="'1900-01-01'";
				}
				else
				{
					$valor="'".$valor."'";
				}
			break;
					
			case 'T':
				$valor=str_replace('/','-',$valor);
				if($valor=='')
				{
					$valor="1900-01-01";
				}
				elseif($valor=='(null)')
				{
					$valor="1900-01-01";
				}
				$dia=substr($valor,8,2);
				$mes=substr($valor,5,2);
				$anio=substr($valor,0,4);
				if(checkdate($mes,$dia,$anio)===false)
				{
					 $valor="'1900-01-01'";
				}
				else
				{
					$valor="'".substr($valor,0,10)."'";
				}
			break;
			
			case 'I':
				if($valor=='')
				{
					$valor='0';
				}
				elseif($valor=='(null)')
				{
					$valor='0';
				}
				elseif(is_numeric($valor)===false)
				{
					$valor='0';
				}
			break;
					
			case 'X':
				$valor=rtrim($valor);		
				if($valor=='')
				{
					$valor="''";
				}
				elseif($valor=='(null)')
				{
					$valor="''";
				}
				elseif(is_string($valor)===false)
				{
					$valor="''";
				}
				else
				{
					$valor = str_replace("'","`",$valor);
					$valor = str_replace("\\","",$valor);
					$valor="'".$valor."'";
				}
			break;
			
			case 'N':
				if($valor=='')
				{
					$valor='0';
				}
				elseif($valor=='(null)')
				{
					$valor='0';
				}
				elseif(is_numeric($valor)===false)
				{
					$valor='0';
				}
			break;
		}
		return $valor;
	}
	
     
/***********************************************************************************
* @Función que busca en la base de datos origen los campos y los verifica en la destino
* @parametros: 
* @retorno: 
* @fecha de creación: 21/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function eliminarMovimientos()
	{
		$this->mensaje='Elimino los movimientos del sistema '.$this->sistema;
		//  Creo la conexión de Origen.
		$this->conexionbd->StartTrans();

		try 
		{ 
			// Se recorre el arreglo de tablas por sistema.
			$total=count((array)$this->tablas);
			for ( $contador = 0; (($contador < $total) && $this->valido); $contador++ )
			{
				$this->_table=$this->tablas[$contador]['tabla'];
				$this->criterio=$this->tablas[$contador]['criterio'];
				$consulta='DELETE FROM '.$this->_table.' '.$this->criterio;
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				escribirArchivo($this->archivo,'*		Eliminio los datos de la tabla '.$this->_table);
				// Ejecuto la Consulta en la Base de Datos Destino.
				$this->consultaactual = $consulta;
				$resultado = $this->conexionbd->Execute($consulta); 							
				escribirArchivo($this->archivo,$consulta);
				escribirArchivo($this->archivo,'');
				escribirArchivo($this->archivo,'*******************************************************************************************************');
			}
			$objconfiguracion = new Configuracion();
			$objconfiguracion->codemp = $this->codemp;
			$objconfiguracion->codsis = $this->sistema;
			$objconfiguracion->seccion = 'MOVIMIENTO';
			$objconfiguracion->entry = 'MOVIMIENTO';
			$objconfiguracion->eliminarLocal();
			unset($objconfiguracion);
			
		}
		catch (exception $e) 
		{
			$this->valido = false;
			$this->mensaje=' Ocurrio un error al eliminar los datos.'.$this->conexionbd->ErrorMsg();
			escribirArchivo($this->archivo,'* Ocurrio un error al eliminar los datos. ');
			escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
			escribirArchivo($this->archivo,'* '.$consulta.' '.$this->conexionbd->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		$this->conexionbd->CompleteTrans($this->valido);
		$_SESSION['session_activa']=time();
		$this->incluirSeguridad('ELIMINAR',$this->valido);	
	}

	
	
/***********************************************************************************
* @Función que Incluye el registro de la transacción exitosa
* @parametros: $evento
* @retorno:
* @fecha de creación: 10/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function incluirSeguridad($evento,$tipotransaccion)
	{
		if($tipotransaccion) // Transacción Exitosa
		{
			$objEvento = new RegistroEventos();
		}
		else // Transacción fallida
		{
			$objEvento = new RegistroFallas();
		}
		// Registro del Evento
		$objEvento->codemp = $this->codemp;
		$objEvento->codsis = $this->codsis;
		$objEvento->nomfisico = $this->nomfisico;
		$objEvento->evento = $evento;
		$objEvento->desevetra = $this->mensaje;
		$objEvento->incluirEvento();
		unset($objEvento);
	}	
}
?>