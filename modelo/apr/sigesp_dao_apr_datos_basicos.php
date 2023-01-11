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

class DatosBasicos extends DaoGenerico
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
	public $estructura1;
	public $codestpro1;
	public $valornuevo;
	public $valoractual;
	public $periodo;
	public $fecinisem;
	public $fecinimen;	
	public $tippernom;
	public $unidadadmin;
	public $nominas = Array();
	public $historicos = Array();
	public $consultaactual = "";
	public $conversion = false;
	public $v2 = true;
	public $reiniciar = true;
	public $bsf = false;
	public $prestamosactivos = false;
	private $objlibcon;
	private $conexionbdorigen;
	private $conexionbd;
	
	public function __construct() {
		parent::__construct ( 'sigesp_config' );
		$this->conversion = false;
		$this->v2 = true;
		$this->reiniciar =true;
		$this->bsf =false;
		$this->conexionbd = $this->obtenerConexionBd(); 
		//$this->conexionbd->debug=true;
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
	public function procesarDatosBasicos()
	{	
		$this->conexionbdorigen = $this->objlibcon->conectarBD($_SESSION['sigesp_servidor_apr'], $_SESSION['sigesp_usuario_apr'], $_SESSION['sigesp_clave_apr'],
									   $_SESSION['sigesp_basedatos_apr'], $_SESSION['sigesp_gestor_apr'], $_SESSION['sigesp_puerto_apr']);
		$this->mensaje='Realizó la apertura del sistema '.$this->sistema;		
		set_time_limit(0);
		$this->conexionbd->StartTrans();
		try 
		{ 
			if ($this->sistema == 'SNO')
			{
				$this->asociarCodigosNomina();
			}
			// Se recorre el arreglo de tablas por sistema.
			$total=count((array)$this->tablas);
			for ( $contador = 0; (($contador < $total) && $this->valido); $contador++ )
			{
				$this->_table=$this->tablas[$contador]['tabla'];
				$this->criterio=$this->tablas[$contador]['criterio'];
				$this->tipo=$this->tablas[$contador]['tipo'];
				$this->valornuevo=$this->tablas[$contador]['valornuevo'];
				$this->valoractual=$this->tablas[$contador]['valornuevo'];
				$this->consultaactual='';
				$totalorigen=0;
				$totaldestino=0;
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				escribirArchivo($this->archivo,'*		Conversión tabla '.$this->_table);
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				
				// Verifico que la tabla Exista en el origen.
				$this->verificarExistenciaTabla($this->conexionbdorigen);
				if (($this->valido) && ($this->existe))
				{
					// Obtengo los datos de la base de datos de origen según la configuració cargada
					$result = $this->obtenerDatosOrigen($this->conexionbdorigen);
					if ((!$result->EOF) && $this->valido)
					{						
						$this->unidadadmin=false; // para validar el presupuesto de gasto cuando sea BSF
						$this->estructura1=false; // para validar el presupuesto de gasto cuando sea BSF
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
							$this->codestpro1='';
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
									// Aplico Criterios de Conversión en caso de ser necesario
									$valor=$this->criterioConversion($columna,$valor);
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
							if (($this->unidadadmin !=false) && ($this->campos[$columna] == 'codestpro1'))
							{	
								$tot=$columna+6;																
								for ($colum=$columna; (($colum < $tot) ); $colum++)
								{
									$valor="";									
									$valor=$this->actualizarValor('C',$valor);
									$valor=$this->criterioConversion($colum,$valor);
									switch($this->tipo)
									{
										case 'INSERT':
											$cadenacampos.=','.$this->campos[$colum];											
											$cadenavalores.=','.$valor;											
										break;
										
										case 'UPDATE':
											$cadenavalores.=','.$this->campos[$colum].'='.$valor;
										break;							
									}				
								}	
							}
							// para validar el presupuesto de gasto cuando sea BSF
							if (($this->estructura1) && ($this->campos[$columna] == 'estcla') && ($this->bsf) && ($this->conversion))
							{
								if ($this->codestpro1 == '')
								{
									$this->codestpro1=str_replace("/","_",$result->fields['codestpro1']);
									$this->codestpro1=str_pad($this->codestpro1,25,'0',0);
								}
								$valor = "'".$this->obtenerEstatusClasificacion()."'";
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
							if (($this->campos[$columna] == 'enabled') && (($this->_table=='sss_permisos_internos') || ($this->_table=='sss_permisos_internos_grupos')))
							{
								$valor = "1";
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
							if (($this->v2) && (($this->_table=='sss_permisos_internos') || ($this->_table=='sss_permisos_internos_grupos')))
							{
								$valor = "1";
								if (trim($this->campos[$columna])<>'')
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
							if (($this->_table =='saf_activo') && (!$this->v2))
							{	
								$tot=$columna+2;																
								for ($colum=$columna; (($colum < $tot) ); $colum++)
								{
									$valor="";									
									$valor=$this->actualizarValor('C',$valor);
									$valor=$this->criterioConversion($colum,$valor);
									switch($this->tipo)
									{
										case 'INSERT':
											$cadenacampos.=','.$this->campos[$colum];											
											$cadenavalores.=','.$valor;											
										break;
										
										case 'UPDATE':
											$cadenavalores.=','.$this->campos[$colum].'='.$valor;
										break;							
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
								//escribirArchivo($this->archivo,'* '.date("Y-m-d H:i:s"));
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
				if ($this->sistema == 'RPC')
				{
					switch ($this->_table)
					{
						case 'sigesp_estados':
							$this->completarUbicacion('ESTADO');
						break;

						case 'sigesp_municipio':
							$this->completarUbicacion('MUNICIPIO');
						break;

						case 'sigesp_parroquia':
							$this->completarUbicacion('PARROQUIA');
						break;
					}
				}
				if (($this->sistema == 'SPG') )
				{
					switch ($this->_table)
					{
						case 'spg_dt_unidadadministrativa':
							$this->insertarUnidadAdministrativa($this->conexionbdorigen);
						break;
						
						case 'spg_dt_fuentefinanciamiento':
							$this->insertarFuenteFinanciamiento();
						break;
						
						case 'spg_cuenta_fuentefinanciamiento':
							$this->insertarCuentaFuenteFinanciamiento();
						break;
					}
				}
				if (($this->sistema == 'SPI') && ($this->_table == 'spi_cuentas_estructuras'))
				{
					$this->insertarCuentaEstructura();
				}
				if (($this->sistema == 'SNO')&&(!$this->conversion))
				{
					switch ($this->_table)
					{
						case 'sno_nomina':
							$this->actualizarNomina();
							$this->generarHistoricosAdicionales($this->conexionbdorigen);
						break;
					}
				}
			}
			if (($this->sistema == 'SSS') && ($this->valido))
			{
				if(!$this->v2)
				{
					$this->procesarSistemaVentana(); 
					$this->procesarDerechosUsuarios($this->conexionbdorigen);
					//$this->procesarDerechosGrupos($this->conexionbdorigen);
				}
			}
			if(!$this->conversion)
			{
				$objconfiguracion = new Configuracion();
				$objconfiguracion->codemp = $this->codemp;
				$objconfiguracion->codsis = $this->sistema;
				$objconfiguracion->seccion = 'APERTURA';
				$objconfiguracion->entry = 'APERTURA';
				$objconfiguracion->type = 'C';
				$objconfiguracion->value = 'TRUE';
				$objconfiguracion->incluirLocal();
				unset($objconfiguracion);
			}	
		}
		catch (exception $e) 
		{
			$this->valido = false;
			$this->mensaje='Ocurrio un error en la Transferencia. Sistema->'.$this->sistema.' Tabla->'.$this->_table.' '.$this->conexionbdorigen->ErrorMsg().' '.$this->conexionbd->ErrorMsg();
			escribirArchivo($this->archivo,'* Ocurrio un error en la Transferencia. ');
			escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
			escribirArchivo($this->archivo,'* Error BD Origen  '.$this->conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'* Error BD Destino '.$this->conexionbd->ErrorMsg());
			escribirArchivo($this->archivo,'* Consulta '.$this->consultaactual);
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		$_SESSION['session_activa']=time();
		$this->conexionbd->CompleteTrans($this->valido);
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
	function  verificarExistenciaTabla($conexionorigen)
	{
		$this->existe=true;
		$this->valido=true;
		$tablas =$conexionorigen->MetaTables('TABLES');
		$clave = array_search($this->_table, $tablas);
		if (!is_numeric($clave))
		{
			$this->existe=false;
			$validos = array(0 => 'sigesp_proc_cons', 1 => 'sigesp_dt_proc_cons', 2 => 'rpc_niveles', 
							 3 => 'spg_dt_fuentefinanciamiento', 4 => 'spg_cuenta_fuentefinanciamiento',
							 5 => 'scg_cuentas_consolida', 6 => 'spg_dt_unidadadministrativa', 
							 7 => 'spi_cuentas_estructuras', 8 => 'saf_depreciacion_int',
							 9 => 'scb_dt_colocacion', 10 => 'sno_causales', 11 =>'sno_tipopersonalsss',
						     12 => 'sno_categoria_rango', 13 => 'srh_tipodeduccion', 14 => 'sno_personaldeduccion',
						     15 => 'sno_familiardeduccion', 16 => 'sno_codigounicorac', 17 => 'sno_personalpension',
						     18 => 'sno_hcodigounicorac', 19 => 'sno_hpersonalpension', 20 => 'scb_casamientoconcepto',
						     21 => 'spg_tipomodificacion', 22 => 'siv_segmento', 23 => 'siv_familia', 24 => 'sigesp_dt_moneda',
							 25 => 'srh_organigrama', 26 => 'soc_dtsc_servicio', 27 => 'cxp_dc_cargos', 28 => 'cxp_cmp_islr',
							 29 => 'cxp_dt_comp_islr', 30 => 'cxp_scg_inter', 31 => 'cxp_solicitudes_scg', 32 => 'cxp_dt_amortizacion',
							 33 => 'cxp_rd_amortizacion', 34 => 'sno_rd', 35 => 'sno_dt_scg_int', 36 => 'sno_dt_spi', 37 => 'scb_tipofondo',
							 38 => 'scb_movbcoanticipo', 39 => 'sigesp_unidad_tributaria', 40 => 'sss_permisos_internos_grupos',
							 41 => 'saf_tipoestructura', 42 => 'saf_componente', 43 => 'saf_item', 44 => 'saf_edificios',
							 45 => 'saf_edificiotipest', 46 => 'siv_clase', 47 => 'siv_producto', 48 => 'sno_clasificacionobrero',
							 49 => 'sno_sueldominimo', 59 => 'sno_hclasificacionobrero', 60 => 'saf_dt_entrega', 61 => 'saf_dt_prestamo', 
							 62 => 'saf_entrega', 63=> 'sigesp_cencosto', 64=> 'sigesp_nivel', 65=> 'sigesp_asig_nivel', 66=>'sss_niv_usuarios',
							 67 => 'saf_catsudeban', 68=>'srh_gerencia', 68=>'srh_departamento', 69=>'srh_bono_merito', 70 => 'srh_puntuacion_bono_merito',
							 71 => 'srh_tipopersonal', 72=> 'srh_puntosunitri', 73=>'srh_dt_puntosunitri', 74=>'scg_casa_presu', 75=>'spg_val_estructura', 
							 76 => 'sep_tipooperacion', 77=>'sigesp_continente', 78 => 'sigesp_conceptoretencion', 79 => 'sigesp_correo', 
							 80 => 'sigesp_sucursales', 81 => 'rpc_deduxprov', 82 => 'rpc_deduxbene', 83 => 'scg_confvariacion', 84 => 'sigesp_consolidacion',
							 85 => 'spg_regmodprogramado', 86 => 'scb_medidas', 87 => 'scv_dt_misiones', 88 => 'scv_regiones_int', 89 => 'scv_dt_regiones_int',
							 90 => 'scv_tarifacargos', 91 => 'scv_dt_tarifacargos', 92 => 'scv_incremento', 93 => 'scv_dt_incremento',
							 94 => 'scv_cargafamiliar', 95 => 'scv_catcargos', 96 => 'scv_dt_catcargos', 97 => 'sob_covenin', 98 => 'sob_documento',
							 99 => 'sno_horario', 100 =>'srh_gerencia', 101 => 'sno_entes', 102 =>'sno_tipo_pensionado', 103 => 'sno_anticipoprestaciones',
							 104 => 'sno_deudaanterior', 105 => 'sno_sueintegral', 106 => 'sno_sueldoshistoricos', 107 =>'sno_guarderias', 108 => 'sno_jubilados',
							 109 => 'sno_fideiintereses', 110 => 'sno_primasdocentes', 111 => 'sno_hojatiempo', 112 => 'sno_encargaduria',
							 113 => 'sno_primadocentepersonal', 114 =>'sno_fideiperiodointereses', 115=>'sno_hhorario', 116 =>'sno_hhojatiempo',
							 117 => 'sno_hprimadocentepersonal', 118 => 'sno_hencargaduria', 119 =>'sno_hprimasdocentes', 120 => 'scb_confconciliacion', 
							 121 => 'sss_usuario_sistema', 122 => 'sss_envio_correo', 123 => 'saf_sede', 124 => 'cxp_confacta', 125 => 'siv_causas', 126 => 'siv_dt_articulo');
			$clave = array_search($this->_table, $validos);
			if (!is_numeric($clave))
			{	
				escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
				escribirArchivo($this->archivo,'* La tabla '.$this->_table.' No Existe en la Base de Datos Origen. Debe Ejecutar el Release.');
				$this->valido=false;
				$this->mensaje = ' La tabla '.$this->_table.' No Existe en la Base de Datos Origen. Debe Ejecutar el Release.';
			}		
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
		if($this->prestamosactivos)			
		{
			if($this->_table==='sno_prestamos')
			{
				$consulta = 'SELECT * FROM '.$this->_table.' '.$this->criterio.' AND stapre<>3 ';
			}
			if($this->_table==='sno_prestamosperiodo')
			{
				$concat = $conexionorigen->Concat("codemp","codtippre","codnom","codper","numpre");
				$consulta = 'SELECT * FROM '.$this->_table.' '.$this->criterio.' AND '.$concat.' IN (SELECT '.$concat.' FROM sno_prestamos WHERE stapre<>3) ';
			}
			if($this->_table==='sno_prestamosamortizado')
			{
				$concat = $conexionorigen->Concat("codemp","codtippre","codnom","codper","numpre");
				$consulta = 'SELECT * FROM '.$this->_table.' '.$this->criterio.' AND '.$concat.' IN (SELECT '.$concat.' FROM sno_prestamos WHERE stapre<>3) ';
			}
		}			
		if(!$this->v2)			
		{
			if($this->_table==='sss_derechos_usuarios')
			{
				$consulta = 'SELECT codemp,codusu,codsis,nomven,MIN(visible) AS visible,MIN(enabled) AS enabled,MIN(leer) AS leer,MIN(incluir) AS incluir,MIN(cambiar) AS cambiar,MIN(eliminar) AS eliminar,MIN(imprimir) AS imprimir,MIN(anular) anular,MIN(ejecutar) ejecutar,0 AS administrativo,1 AS ayuda,0 AS cancelar,0 AS enviarcorreo, 0 AS descargar FROM '.$this->_table.' '.$this->criterio.' ORDER BY codemp,codusu,codsis,nomven ';
			}
		}			
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
		$tablasspg = array(0 => 'spg_ep1', 1 => 'spg_ep2', 2 => 'spg_ep3', 
						   3 => 'spg_ep4', 4 => 'spg_ep5', 5 => 'spg_dt_fuentefinanciamiento', 
						   6 => 'spg_dt_unidadadministrativa', 7 => 'spg_cuentas',
						   8 => 'spg_cuenta_fuentefinanciamiento', 9 => 'saf_activo', 
						   10 => 'sno_unidadadmin', 11 => 'sno_asignacioncargo', 12 => 'sno_hasignacioncargo',
						   13 => 'sno_proyecto', 14 => 'sno_hproyecto', 15 => 'sno_hunidadadmin',
						   16 => 'sigesp_cargos', 17 => 'sno_concepto', 18 => 'sno_hconcepto', 
						   19 => 'spg_dt_cmp', 20 => 'soc_cuentagasto', 21 => 'soc_solicitudcargos',
						   22 => 'cxp_rd_spg', 23=> 'scb_movbco_spg', 24 => 'sno_dt_spg', 25 => 'spg_dtmp_cmp', 
						   26 => 'sep_dt_concepto', 27 => 'sep_solicitudcargos', 28 => 'sep_dt_servicio',
						   29 => 'sep_dt_articulos', 30 => 'sep_cuentagasto', 31 => 'cxp_rd_cargos',
						   32 => 'cxp_dc_spg', 33 => 'spg_plantillareporte', 34 => 'soc_dt_servicio',
						   35 => 'soc_ordencompra', 36 => 'sep_solicitud', 37 => 'soc_enlace_sep',
						   38 => 'soc_dtsc_bienes', 39 => 'soc_sol_cotizacion', 40 =>'soc_dt_bienes',
						   41 => 'soc_solcotsep');
		$clave = array_search($this->_table, $tablasspg);
		if (is_numeric($clave))
		{	
			$clave = array_search('codestpro1', $this->campos);
			if (!is_numeric($clave))
			{
				$this->campos[$columna]='codestpro1';
				$columna++;
			}
			$clave = array_search('estcla', $this->campos);
			if (!is_numeric($clave))
			{
				$this->estructura1=true;
				$this->campos[$columna]='estcla';
				$columna++;
			}
			$clave = array_search('codestpro2', $this->campos);
			if (!is_numeric($clave))
			{
				$this->campos[$columna]='codestpro2';
				$columna++;
			}
			$clave = array_search('codestpro3', $this->campos);
			if (!is_numeric($clave))
			{
				$this->campos[$columna]='codestpro3';
				$columna++;
			}
			$clave = array_search('codestpro4', $this->campos);
			if (!is_numeric($clave))
			{
				$this->campos[$columna]='codestpro4';
				$columna++;
			}
			$clave = array_search('codestpro5', $this->campos);
			if (!is_numeric($clave))
			{
				$this->campos[$columna]='codestpro5';
				$columna++;
			}
		}
		unset($tablasspg);
		$tablasspg = array(0 => 'sss_permisos_internos', 1 => 'sss_permisos_internos_grupos'); 
		$clave = array_search($this->_table, $tablasspg);
		if (is_numeric($clave))
		{	
			$clave = array_search('enabled', $this->campos);
			if (!is_numeric($clave))
			{
				$this->campos[$columna]='enabled';
				$columna++;
			}
		}
		unset($tablasspg);
		$tablasspg = array(0 => 'saf_activo'); 
		$clave = array_search($this->_table, $tablasspg);
		if (is_numeric($clave))
		{	
			$clave = array_search('codcat', $this->campos);
			if (!is_numeric($clave))
			{
				$this->campos[$columna]='codcat';
				$columna++;
			}
			$clave = array_search('expediente', $this->campos);
			if (!is_numeric($clave))
			{
				$this->campos[$columna]='expediente';
				$columna++;
			}
			$clave = array_search('codcencos', $this->campos);
			if (!is_numeric($clave))
			{
				$this->campos[$columna]='codcencos';
				$columna++;
			}
			$clave = array_search('colact', $this->campos);
			if (!is_numeric($clave))
			{
				$this->campos[$columna]='colact';
				$columna++;
			}
		}
		unset($tablasspg);
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
				case 'siv_unidadmedida':
					$validos = array(0 => 'tiposep');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'siv_despacho':
					$validos = array(0 => 'nomproy',1 => 'codproy',2 => 'direccproy');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'soc_dt_servicio':
					$validos = array(0 => 'monuniseraux',1 => 'monsubseraux',2 => 'montotseraux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'soc_dts_cargos':
					$validos = array(0 => 'monbasimpaux', 1 =>'monimpaux', 2 =>'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'soc_servicios':
					$validos = array(0 => 'codunimed',1 => 'preseraux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'soc_cotizacion':
					$validos = array(0 => 'monsubtotaux',1 =>'monimpcotaux',2 =>'mondesaux',3 =>'montotcotaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				
				case 'soc_dtcot_bienes':
					$validos = array(0 => 'preuniartaux',1 =>'monivaaux',2 =>'monsubartaux',3 =>'montotartaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'soc_dtcot_servicio':
					$validos = array(0 => 'monuniseraux',1 =>'monivaaux',2 => 'monsubseraux',3=>'montotseraux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'soc_ordencompra':
					$validos = array(0 =>'monsegcomaux',1 =>'monsubtotbieaux',2 =>'monsubtotseraux',3 =>'monsubtotaux',
					                 4 =>'monbasimpaux',5 =>'monimpaux',6 =>'mondesaux',7 =>'montotaux',8 =>'monantaux',
									 9 =>'tascamordcomaux',10 =>'montotdivaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'soc_cuentagasto':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'soc_solicitudcargos':
					$validos = array(0 => 'monobjretaux',1 => 'monretaux',2 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'soc_dt_bienes':
					$validos = array(0 => 'preuniartaux',1 => 'monsubartaux',2 => 'montotartaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'soc_dta_cargos':
					$validos = array(0 => 'monbasimpaux',1 => 'monimpaux',2 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'soc_analisicotizacion':
					$validos = array(0 => 'estasitec',1 => 'estesp',2 => 'garanacot');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'cxp_solicitudes':
					$validos = array(0 => 'monsolaux',1 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'cxp_sol_banco':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'cxp_rd':
					$validos = array(0 => 'montotdocaux',1 => 'mondeddocaux',2 => 'moncardocaux',3 => 'montotaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'cxp_sol_dc':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'cxp_rd_cargos':
					$validos = array(0 => 'monobjretaux',1 => 'monretaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'cxp_rd_spg':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'cxp_rd_scg':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'cxp_dt_solicitudes':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'cxp_dc_spg':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'cxp_dc_scg':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
								
				case 'cxp_rd_deducciones':
					$validos = array(0 => 'monobjretaux',1 =>'monretaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sigesp_empresa':
					$validos = array(0 => 'saliniproaux', 1 => 'saliniejeaux', 2 => 'candeccon', 3 => 'tipconmon', 
									 4 => 'redconmon', 5 => 'sueintivss', 6 => 'suenetivss', 7 => 'estintban', 8 => 'estspidecimal');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sigesp_cmp':
					$validos = array(0 => 'totalaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sigesp_cmp_md':
					$validos = array(0 => 'totalaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sigesp_moneda':
					$validos = array(0 => 'tascamaux', 1 => 'imamon', 2 => 'tascam', 3 =>'abrmon');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sss_usuarios':
					$validos = array(0 => 'actusu',1 => 'blkusu');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sss_derechos_usuarios':
					$validos = array(0 => 'nomven');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}	
					else
					{
						$campo='codmenu';
						$this->existe=true;
					}
				break;
				
				case 'sss_derechos_grupos':
					$validos = array(0 => 'nomven');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}	
					else
					{
						$campo='codmenu';
						$this->existe=true;
					}
				break;
										
				case 'rpc_proveedor':
					$validos = array(0 => 'capitalaux',1 => 'monmaxaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'scg_cuentas':
					$validos = array(0 => 'asignadoaux', 1 => 'eneroaux', 2 => 'febreroaux', 3 => 'marzoaux',
								     4 => 'abrilaux', 5 => 'mayoaux', 6 => 'junioaux', 7 => 'julioaux',
								     8 => 'agostoaux', 9 => 'septiembreaux', 10 => 'octubreaux', 11 => 'noviembreaux',
								     12 => 'diciembreaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'scg_saldos':
					$validos = array(0 => 'debe_mesaux',1 => 'haber_mesaux' );
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'scg_dt_cmp':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'spg_ep3':
					$validos = array(0 => 'codfuefin');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'spg_ep5':
					$validos = array(0 => 'codfuefin');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'spg_unidadadministrativa':
					$validos = array(0 => 'codestpro1', 1 => 'codestpro2', 2 => 'codestpro3', 3 => 'codestpro4',
									 4 => 'codestpro5');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'spg_cuentas':
					$validos = array(0 => 'asignadoaux', 1 => 'precomprometidoaux', 2 => 'comprometidoaux', 3 => 'causadoaux',
									 4 => 'pagadoaux', 5 => 'aumentoaux', 6 => 'disminucionaux', 7 => 'eneroaux', 8 => 'febreroaux', 
									 9 => 'marzoaux', 10 => 'abrilaux', 11 => 'mayoaux', 12 => 'junioaux', 13 => 'julioaux',
								     14 => 'agostoaux', 15 => 'septiembreaux', 16 => 'octubreaux', 17 => 'noviembreaux',
								     18 => 'diciembreaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'spg_dt_cmp':
					$validos = array(0 => 'asignadoaux', 1 => 'precomprometidoaux', 2 => 'comprometidoaux', 3 => 'causadoaux',
									 4 => 'pagadoaux', 5 => 'aumentoaux', 6 => 'disminucionaux',7 => 'eneroaux', 8 => 'febreroaux', 
									 9 => 'marzoaux',10 => 'abrilaux', 11 => 'mayoaux', 12 => 'junioaux', 13 => 'julioaux',
								     14 => 'agostoaux', 15 => 'septiembreaux', 16 => 'octubreaux', 17 => 'noviembreaux',
								     18 => 'diciembreaux', 19 => 'codfuefin',20 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'spg_plantillareporte':
					$validos = array(0 => 'asignadoaux', 1 => 'precomprometidoaux', 2 => 'comprometidoaux', 3 => 'causadoaux',
									 4 => 'pagadoaux', 5 => 'aumentoaux', 6 => 'disminucionaux',7 => 'eneroaux', 8 => 'febreroaux', 
									 9 => 'marzoaux',10 => 'abrilaux', 11 => 'mayoaux', 12 => 'junioaux', 13 => 'julioaux',
								     14 => 'agostoaux', 15 => 'septiembreaux', 16 => 'octubreaux', 17 => 'noviembreaux',
								     18 => 'diciembreaux',19 => 'codfuefin',20 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'spg_dtmp_cmp':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'spi_cuentas':
					$validos = array(0 => 'previstoaux', 1 => 'devengadoaux', 2 => 'cobradoaux', 3 => 'cobrado_anticipadoaux',
									 4 => 'aumentoaux', 5 => 'disminucionaux', 6 => 'eneroaux', 7 => 'febreroaux', 8 => 'marzoaux',
								     9 => 'abrilaux', 10 => 'mayoaux', 11 => 'junioaux', 12 => 'julioaux',13 => 'agostoaux', 
									 14 => 'septiembreaux', 15 => 'octubreaux', 16 => 'noviembreaux', 17 => 'diciembreaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'spi_dt_cmp':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'saf_activo':
					$validos = array(0 => 'costoaux', 1 => 'cossalaux', 2 => 'monordcomaux', 3 => 'moncobaseaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'saf_depreciacion':
					$validos = array(0 => 'mondepmenaux', 1 => 'mondepanoaux', 2 => 'mondepacuaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'sigesp_deducciones':
					$validos = array(0 => 'mondedaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'siv_articulo':
					$validos = array(0 => 'preartaaux', 1 => 'preartbaux', 2 => 'preartcaux', 3 => 'preartdaux', 4 => 'ultcosartaux', 
									 5 => 'cosproartaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'siv_dt_movimiento':
					$validos = array(0 => 'cosartaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sep_solicitud':
					$validos = array(0 => 'montoaux',1 =>'monbasinmaux',2 =>'montotcaraux',3 =>'firnivsol',4 =>'firnivadm',5 =>'firnivpre');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sep_solicitudcargos':
					$validos = array(0 => 'monobjretaux',1 =>'monretaux',2 =>'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'sep_conceptos':      
					$validos = array(0 => 'monconsepeaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}
				break;
				
				case 'sep_dt_concepto':
					$validos = array(0 => 'monpreaux',1 => 'monconaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}					
				break;
				
				case 'sep_dt_servicio':
					$validos = array(0 => 'monpreaux',1 => 'monseraux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}					
				break;
				
				case 'sep_dt_articulos':
					$validos = array(0 => 'monpreaux',1 => 'monartaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}					
				break;
				
				case 'sep_dta_cargos':
					$validos = array(0 => 'monbasimpaux',1 => 'monimpaux',2 => 'montoaux',3 => 'monbasimpaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}					
				break;
				
				case 'sep_dtc_cargos':
					$validos = array(0 => 'monimpaux',1 => 'monbasimpaux',2 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}					
				break;
				
				case 'sep_dts_cargos':
					$validos = array(0 => 'monbasimpaux', 1 => 'monimpaux',2 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}					
				break;
				
				case 'sep_cuentagasto':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}					
				break;
				
				case 'scb_colocacion':
					$validos = array(0 => 'montoaux', 1 => 'monintaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'scb_movbco':
					$validos = array(0 => 'montoaux', 1 => 'monobjretaux', 2 => 'monretaux', 3 => 'aliidbaux', 4 => 'numsolmin');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'scb_movbco_spg':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'scb_movbco_scg':
					$validos = array(0 => 'montoaux', 1 => 'monobjretaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'scb_movbco_spi':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				
				case 'scb_dt_cmp_ret':
					$validos = array(0 => 'totcmp_sin_ivaaux', 1=>'totcmp_con_ivaaux', 2=>'basimpaux', 3=>'totimpaux',
							         4=>'iva_retaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'scv_tarifas':
					$validos = array(0 => 'monbolaux', 1 => 'mondolaux', 2 => 'monpasaux', 3 => 'monhosaux',
									 4 => 'monaliaux', 5 => 'monmovaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'scv_tarifakms':
					$validos = array(0 => 'montaraux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'scv_otrasasignaciones':
					$validos = array(0 => 'tarotrasiaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'scv_transportes':
					$validos = array(0 => 'tartraaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'scv_solicitudviatico':
					$validos = array(0 => 'monsolviaaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_nomina':
					$validos = array(0 => 'cersindis');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_programacionreporte':
					$validos = array(0 => 'totasiaux', 1 => 'moneneaux', 2 => 'monfebaux', 3 => 'monmaraux', 4 => 'monabraux',
									 5 => 'monmayaux', 6 => 'monjunaux', 7 => 'monjulaux', 8 => 'monagoaux', 9 => 'monsepaux',
									 10 => 'monoctaux', 11 => 'monnovaux', 12 => 'mondicaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_cestaticket':
					$validos = array(0 => 'moncesticaux', 1 => 'mondesdia');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_clasificaciondocente':
					$validos = array(0 => 'suesupcladocaux', 1=> 'suedircladocaux', 2=>'suedoccladocaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_dt_spg':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_dt_scg':
					$validos = array(0 => 'montoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_personal':
					$validos = array(0 => 'monpagvivperaux', 1 => 'ingbrumenaux', 2 => 'sno_codemp', 3 => 'cedaut', 4 => 'nomaut', 5 => 'cedmil', 6 => 'tippensionado', 7 => 'suebasafi', 
					8 => 'porpenafi', 9 => 'parentesco', 10 => 'nropersona', 11 => 'parfam', 12 => 'medjudnro', 13 => 'porpension', 14 => 'tipmedjudcod');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_trabajoanterior':
					$validos = array(0 => 'ultsuetraantaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_familiar':
					$validos = array(0 => 'ced_menor');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_vacacpersonal':
					$validos = array(0 => 'sueintbonvacaux', 1 => 'sueintvacaux', 2 => 'monto_1aux', 3 => 'monto_2aux', 
								     4 => 'monto_3aux', 5 => 'monto_4aux', 6 => 'monto_5aux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_grado':
					$validos = array(0 => 'moncomgraaux', 1 => 'monsalgraaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_personalnomina':
					$validos = array(0 => 'sueperaux', 1 => 'sueintperaux', 2 => 'sueproperaux', 3 => 'anovig', 4 => 'suebenef', 5 => 'codper_medidas', 6 => 'por_militares', 7 => 'ced_militares');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_unidadadmin':
					$validos = array(0 => 'codestpro1', 1 => 'codestpro2', 2 => 'codestpro3',3 => 'codestpro4', 4 => 'codestpro5', 5 => 'codprouniadm', 6 => 'loncodestpro1', 7 => 'codproviauniadm');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'sno_proyecto':
					$validos = array(0 => 'codestpro1', 1 => 'codestpro2', 2 => 'codestpro3',3 => 'codestpro4', 4 => 'codestpro5', 5 => 'estproproy');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_asignacioncargo':
					$validos = array(0 => 'codestpro1', 1 => 'codestpro2', 2 => 'codestpro3',3 => 'codestpro4', 4 => 'codestpro5', 5 => 'codproasicar');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'sno_concepto':
					$validos = array(0 => 'acumaxconaux', 1 => 'valminconaux', 2 => 'valmaxconaux', 3 => 'valminpatconaux',
									 4 => 'valmaxpatconaux', 5 => 'codente', 6 => 'codestpro1', 7 => 'codestpro2', 8 => 'codestpro3',
									 9 => 'codestpro4', 10 => 'codestpro5', 11 => 'codpro');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_conceptopersonal':
					$validos = array(0 => 'acuinipataux', 1 => 'acupataux', 2 => 'acuiniempaux', 3 => 'acuempaux',
									 4 => 'valconaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_conceptovacacion':
					$validos = array(0 => 'acumaxsalvacaux', 1 => 'minsalvacaux', 2 => 'maxsalvacaux', 3 => 'minpatsalvacaux',
									 4 => 'maxpatsalvacaux', 5 => 'acumaxreivacaux', 6 => 'minreivacaux', 
									 7 => 'maxreivacaux', 8 => 'minpatreivacaux', 9 => 'maxpatreivacaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_constante':
					$validos = array(0 => 'equconaux', 1 => 'topconaux', 2 => 'valconaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_constantepersonal':
					$validos = array(0 => 'monconaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_prestamos':
					$validos = array(0 => 'monpreaux', 1 => 'monamopreaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_prestamosamortizado':
					$validos = array(0 => 'monamoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_prestamosperiodo':
					$validos = array(0 => 'moncuoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_fideiperiodo':
					$validos = array(0 => 'bonvacperaux', 1 => 'bonfinperaux', 2 => 'sueintperaux', 
					                 3 => 'apoperaux', 4 => 'bonextperaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_primagrado':
					$validos = array(0 => 'monpriaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_periodo':
					$validos = array(0 => 'totperaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hperiodo':
					$validos = array(0 => 'totperaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hgrado':
					$validos = array(0 => 'moncomgraaux', 1 => 'monsalgraaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hpersonalnomina':
					$validos = array(0 => 'sueperaux', 1 => 'sueintperaux', 2 => 'sueproperaux', 3 => 'anovig');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hunidadadmin':
					$validos = array(0 => 'codestpro1', 1 => 'codestpro2', 2 => 'codestpro3',3 => 'codestpro4', 4 => 'codestpro5', 5 => 'codprouniadm', 6 => 'loncodestpro1');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'sno_hproyecto':
					$validos = array(0 => 'codestpro1', 1 => 'codestpro2', 2 => 'codestpro3',3 => 'codestpro4', 4 => 'codestpro5', 5 => 'estproproy');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hasignacioncargo':
					$validos = array(0 => 'codestpro1', 1 => 'codestpro2', 2 => 'codestpro3',3 => 'codestpro4', 4 => 'codestpro5', 5 => 'codproasicar');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'sno_hconcepto':
					$validos = array(0 => 'acumaxconaux', 1 => 'valminconaux', 2 => 'valmaxconaux', 3 => 'valminpatconaux',
									 4 => 'valmaxpatconaux', 5 => 'codestpro1', 6 => 'codestpro2', 7 => 'codestpro3',
									 8 => 'codestpro4', 9 => 'codestpro5', 10 => 'codpro' );
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hconceptopersonal':
					$validos = array(0 => 'acuinipataux', 1 => 'acupataux', 2 => 'acuiniempaux', 3 => 'acuempaux',
									 4 => 'valconaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hconceptovacacion':
					$validos = array(0 => 'acumaxsalvacaux', 1 => 'minsalvacaux', 2 => 'maxsalvacaux', 3 => 'minpatsalvacaux',
									 4 => 'maxpatsalvacaux', 5 => 'acumaxreivacaux', 6 => 'minreivacaux', 
									 7 => 'maxreivacaux', 8 => 'minpatreivacaux', 9 => 'maxpatreivacaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hconstante':
					$validos = array(0 => 'equconaux', 1 => 'topconaux', 2 => 'valconaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hconstantepersonal':
					$validos = array(0 => 'monconaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hprestamos':
					$validos = array(0 => 'monpreaux', 1 => 'monamopreaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hprestamosamortizado':
					$validos = array(0 => 'monamoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hprestamosperiodo':
					$validos = array(0 => 'moncuoaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hprimagrado':
					$validos = array(0 => 'monpriaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hvacacpersonal':
					$validos = array(0 => 'sueintbonvacaux', 1 => 'sueintvacaux', 2 => 'monto_1aux', 3 => 'monto_2aux', 
								     4 => 'monto_3aux', 5 => 'monto_4aux', 6 => 'monto_5aux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hprenomina':
					$validos = array(0 => 'valprenomaux', 1 => 'valhisaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hsalida':
					$validos = array(0 => 'valsalaux', 1 => 'monacusalaux', 2 => 'salsalaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;
				
				case 'sno_hresumen':
					$validos = array(0 => 'asiresaux', 1 => 'dedresaux', 2 => 'apoempresaux', 3 => 'apopatresaux',
								     4 => 'priquiresaux', 5 => 'segquiresaux', 6 => 'monnetresaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'scb_conciliacion':
					$validos = array(0 => 'salseglibaux', 1 => 'salsegbcoaux', 2 => 'conciliacionaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'scb_errorconcbco':
					$validos = array(0 => 'monmovaux', 1 => 'monretaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'siv_dt_recepcion':
					$validos = array(0 => 'preuniartaux', 1 => 'monsubartaux', 2 => 'montotartaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'siv_dt_despacho':
					$validos = array(0 => 'preuniartaux', 1 => 'monsubartaux', 2=> 'montotartaux');
					$clave = array_search($campo, $validos);
					if (!is_numeric($clave))
					{	
						$this->valido=false;
					}		
				break;

				case 'scg_dtmp_cmp':
					$validos = array(0 => 'montoaux');
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
* @Función que realiza los criterios de conversión
* @parametros: 
* @retorno: 
* @fecha de creación: 28/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function  criterioConversion($columna,$valor)
	{
		if(($this->campos[$columna] == 'codtipesc') && ($this->_table == 'sno_ubicacionfisica'))
		{
			if (empty($valor)||($valor=="''"))
			{
				$valor = "'----------'";
			}
		}
		if (($this->_table == 'sss_usuarios') && ($this->campos[$columna] == 'pwdusu'))
		{
			if($this->v2)
			{
				if(($this->reiniciar))
				{
					$valor = "'FKvX0oSRuHEz8xsIZVyVN6YLIwI'";
				}
			}
			else
			{
				$valor = "'FKvX0oSRuHEz8xsIZVyVN6YLIwI'";
			}
		}
		if (($this->_table == 'sigesp_empresa') && ($this->campos[$columna] == 'periodo') &&(!$this->conversion))
		{
			$valorant = str_replace("'","",$valor);
			$anio = substr($valorant,0,4);
			$anio = intval($anio+1);
			$valor = "'".$anio."-01-01'";
		}
		if (($this->_table == 'sigesp_empresa') && ($this->campos[$columna] == 'dirvirtual'))
		{
			$valor = "'sigesp2011'";
		}
		if (($this->_table == 'sno_personal') && ($this->campos[$columna] == 'fecreg'))
		{
			if (trim($valor) == '')
			{
				$valor = "'1900-01-01'";
			}
		}

		if (($this->_table == 'sigesp_empresa') && (($this->campos[$columna] == 'estciespg') || ($this->campos[$columna] == 'estciespi') || 
			($this->campos[$columna] == 'estciescg') || ($this->campos[$columna] == 'ciesem1') || ($this->campos[$columna] == 'ciesem2')))
		{
			$valor = "0";
		}
		 
		if (($this->_table == 'sigesp_empresa') && (($this->campos[$columna] == 'm01') || ($this->campos[$columna] == 'm02') || 
			($this->campos[$columna] == 'm03') || ($this->campos[$columna] == 'm04') || ($this->campos[$columna] == 'm05') || 
			($this->campos[$columna] == 'm06') || ($this->campos[$columna] == 'm07') ||	($this->campos[$columna] == 'm08') || 
			($this->campos[$columna] == 'm09') || ($this->campos[$columna] == 'm10') || ($this->campos[$columna] == 'm11') || 
			($this->campos[$columna] == 'm12')))
		{
			$valor = "0";
		}
		if (($this->campos[$columna] == 'codpai') || ($this->campos[$columna] == 'codest') || ($this->campos[$columna] == 'codmun') || 
		     ($this->campos[$columna] == 'codpar'))
		{
			if (trim(str_replace("'","",$valor)) == '')
			{
				$valor = "'---'";
			}
		}
		if ($this->campos[$columna] == 'codfuefin')
		{
		    $valor=str_replace("'","",$valor);
			if ($valor == '')
			{
				$valor = '--';
			}	
			$valor=str_replace("'","",$valor);
		    $valor = "'".$valor."'";
		}
		
		if ($this->campos[$columna] == 'codtipmod')
		{
		    $valor=str_replace("'","",$valor);
			if ($valor == '')
			{
				$valor = '--';
			}	
			$valor=str_replace("'","",$valor);
		    $valor = "'".$valor."'";
		}
		
		if ($this->campos[$columna] == 'cod_pro')
		{
			if (strlen(trim($valor)) < 10 )
			{
				$valorant=str_replace("'","",$valor);
				if ($valorant == '')
				{
					$valor = "----------";
				}
				else
				{
					$valor=str_pad(substr($valorant,0,8),8,'0',0).'00';
					escribirArchivo($this->archivo,'* Se cambio el código del proveedor por que la longitud no coincide de '.$valorant.' al valor '.$valor);
				}
				$valor = "'".$valor."'";
			}
		}
		if ($this->campos[$columna] == 'coduniadm')
		{
			if (empty($valor)||($valor=="''"))
			{
				$valor="----------";
				escribirArchivo($this->archivo,'* Se cambio el código de la Unidad Administrativa al valor '.$valor.' ya que el origen no tenia valor');
			}
			else
			{
				$valor=str_replace("'","",$valor);
				$valor=trim($valor);
				if (strlen(trim($valor)) < 10 )
				{
					$valorant=str_replace("'","",$valor);
					$valor=str_pad($valorant,8,'0',0).'00';
					escribirArchivo($this->archivo,'* Se cambio el código de la Unidad Administrativa por que la longitud no coincide de '.$valorant.' al valor '.$valor);
				}
			}
			$valor = "'".$valor."'";
		}
		if ($this->campos[$columna] == 'ced_bene')
		{
			$valorant=str_replace("'","",$valor);
			if (strlen(trim($valorant)) <= 0 )
			{
				$valor = "'----------'";
			}
		}
		if (($this->campos[$columna] == 'codestpro1') || ($this->campos[$columna] == 'codestpro2') || ($this->campos[$columna] == 'codestpro3') || 
		    ($this->campos[$columna] == 'codestpro4') || ($this->campos[$columna] == 'codestpro5'))
		{
			$valor = str_replace("'","",$valor);
			$valor = str_replace("/","_",$valor);
			if ($valor == '-')
			{
				$valor = '';
			}
			if (strlen(trim($valor)) > 0 )
			{
				$valor = str_replace("'","",$valor);
				$valor = str_pad($valor,25,'0',0);
				$valor = "'".$valor."'";
			}
			else
			{
				$valor = "'-------------------------'";
			}
		}
		if ($this->campos[$columna] == 'codsis')
		{
			$valor = strtoupper($valor);
		}
		if ($this->campos[$columna] == 'nomgru')
		{
			$valor = strtolower($valor);
		}
		if ($this->campos[$columna] == 'estcla')
		{
			if (trim(str_replace("'","",$valor)) == '')
			{
				$valor = "'-'";
			}
		}
		if ($this->_table == 'saf_activo')
		{
			if (($this->campos[$columna] == 'codrot') && (trim(str_replace("'","",$valor)) == ''))
			{
				$valor = "'--'";
			}
			if (($this->campos[$columna] == 'codmetdep') && (trim(str_replace("'","",$valor)) == ''))
			{
				$valor = "'001'";
			}
			if (($this->campos[$columna] == 'codgru') && (trim(str_replace("'","",$valor)) == ''))
			{
				$valor = "'---'";
			}
			if (($this->campos[$columna] == 'codsubgru') && (trim(str_replace("'","",$valor)) == ''))
			{
				$valor = "'---'";
			}
			if (($this->campos[$columna] == 'codsec') && (trim(str_replace("'","",$valor)) == ''))
			{
				$valor = "'---'";
			}
			if (($this->campos[$columna] == 'codite') && (trim(str_replace("'","",$valor)) == ''))
			{
				$valor = "'---'";
			}
			if (($this->campos[$columna] == 'codcat') && (trim(str_replace("'","",$valor)) == ''))
			{
				$valor = "'-----'";
			}
			if (($this->campos[$columna] == 'codcencos') && (trim(str_replace("'","",$valor)) == ''))
			{
				$valor = "'---'";
			}
		}
		if ($this->campos[$columna] == 'codconmov')
		{
			if (trim(str_replace("'","",$valor)) == '')
			{
				$valor = "'---'";
			}
		}
		if ($this->campos[$columna] == 'codtippersss')
		{
			$valorant = str_replace("'","",$valor);
			if (strlen(trim($valorant)) <= 0 )
			{
				$valor = "'-------'";
			}
		}
		if (($this->campos[$columna] == 'codcar') && (($this->sistema == 'SNO')||($this->sistema == 'SNR')))
		{
			$valorant = str_replace("'","",$valor);
			if (strlen(trim($valorant)) <= 0 )
			{
				$valor = "'----------'";
			}
		}		
		if (($this->campos[$columna] == 'codnom') && ($this->sistema == 'SNO'))
		{
			$this->valoractual = str_replace("'","",$valor);
			$valor = "'".$this->obtenerValorNomina($valor)."'";
		}
		if (($this->campos[$columna] == 'codconc') && (($this->sistema == 'SNO')||($this->sistema == 'SNR')))
		{
			$valor = str_replace("-","0",$valor);
		}
		if ($this->campos[$columna] == 'tippernom')
		{
			$this->tippernom = str_replace("'","",$valor);
		}
		if(($this->campos[$columna] == 'numconint') && (substr($this->_table,0,4) == 'scb_'))
		{
			$valorant = str_replace("'","",$valor);
			if (strlen(trim($valorant)) <= 0 )
			{
				$valor = "''";
			}
		}		
		if ($this->_table == 'scg_cuentas')
		{
			$validos = array(0 => 'asignado', 1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio', 7 => 'julio',
				     		 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre');
			$clave = array_search($this->campos[$columna], $validos);
			if (is_numeric($clave))
			{	
				if (!$this->conversion)
				{
					$valor = "0";
				}
			}		
		}
		if ($this->_table == 'spg_cuentas')
		{
			$validos = array(0 => 'asignado', 1 => 'precomprometido', 2 => 'comprometido', 3 => 'causado', 4 => 'pagado', 5 => 'aumento', 
			                 6 => 'disminucion', 7 => 'enero', 8 => 'febrero', 9 => 'marzo', 10 => 'abril', 11 => 'mayo', 12 => 'junio', 
							 13 => 'julio', 14 => 'agosto', 15 => 'septiembre', 16 => 'octubre', 17 => 'noviembre', 18 => 'diciembre');
			$clave = array_search($this->campos[$columna], $validos);
			if (is_numeric($clave))
			{	
				if (!$this->conversion)
				{
					$valor = "0";
				}
			}		
		}
		if ($this->_table == 'spi_cuentas')
		{
			$validos = array(0 => 'previsto', 1 => 'devengado', 2 => 'cobrado', 3 => 'cobrado_anticipado', 4 => 'aumento', 5 => 'disminucion', 
			                 6 => 'enero', 7 => 'febrero', 8 => 'marzo', 9 => 'abril', 10 => 'mayo', 11 => 'junio', 12 => 'julio',
						     13 => 'agosto', 14 => 'septiembre', 15 => 'octubre', 16 => 'noviembre', 17 => 'diciembre');
			$clave = array_search($this->campos[$columna], $validos);
			if (is_numeric($clave))
			{	
				if (!$this->conversion)
				{
					$valor = "0";
				}
			}
		}
		if ((($this->_table == 'sigesp_cargos') || ($this->_table == 'cxp_rd_spg') || ($this->_table == 'scb_movbco_spg') || ($this->_table == 'cxp_dc_spg')) && ($this->campos[$columna] == 'codestpro'))
		{
			$valorant = str_replace("'","",$valor);
			$valorant=$this->convertirLongitudEstructuras($valorant);
			$valor = "'".$valorant."'";
		}
		if ((($this->_table == 'sno_concepto') || ($this->_table == 'sno_hconcepto')) && ($this->campos[$columna] == 'codpro'))
		{
			$valorant = str_replace("'","",$valor);
			$valorant=$this->convertirLongitudEstructuras($valorant);
			$valor = "'".$valorant."'";
		}
		if ((($this->_table == 'sno_asignacioncargo') || ($this->_table == 'sno_hasignacioncargo')) && ($this->campos[$columna] == 'codproasicar'))
		{
			$valorant = str_replace("'","",$valor);
			$valorant=$this->convertirLongitudEstructuras($valorant);
			$valor = "'".$valorant."'";
		}
		if ((($this->_table == 'sno_unidadadmin') || ($this->_table == 'sno_hunidadadmin')) && ($this->campos[$columna] == 'codprouniadm'))
		{
			$valorant = str_replace("'","",$valor);
			$valorant=$this->convertirLongitudEstructuras($valorant);
			$valor = "'".$valorant."'";
		}
		if ((($this->_table == 'sno_proyecto') || ($this->_table == 'sno_hproyecto')) && ($this->campos[$columna] == 'estproproy'))
		{
			$valorant = str_replace("'","",$valor);
			$valorant=$this->convertirLongitudEstructuras($valorant);
			$valor = "'".$valorant."'";
		}
		if ((($this->_table == 'sss_derechos_grupos') || ($this->_table == 'sss_derechos_usuarios')) && ($this->campos[$columna] == 'codintper'))
		{
			$valorant = str_replace("'","",$valor);
			if ($valorant != '---------------------------------')
			{
				$valorant=$this->convertirLongitudEstructurasEstatus($valorant);
			}
			$valor = "'".$valorant."'";
		}
		if ((($this->_table == 'sss_permisos_internos') || ($this->_table == 'sss_permisos_internos_grupos')) && ($this->campos[$columna] == 'codintper'))
		{
			$valorant = str_replace("'","",$valor);
			if ($valorant != '---------------------------------')
			{
				$valorant=$this->convertirLongitudEstructurasEstatus($valorant);
			}
			$valor = "'".$valorant."'";
		}
		if (((substr($this->_table,0,5) == 'sno_h') && ($this->sistema == 'SNO'))||($this->_table == 'sno_banco'))
		{
			switch ($this->campos[$columna])
			{
				case 'anocur':
					$valor = "'".substr($this->fecinimen,0,4)."'";
				break;
					
				case 'codperi':
					$valor = "'".$this->periodo."'";
				break;
				
				case 'anocurnom':
					$valor = "'".substr($this->fecinimen,0,4)."'";
				break;
					
				case 'peractnom':
					$valor = "'".$this->periodo."'";
				break;

				case 'fecininom':
					$valor = "'".$this->fecinimen."'";
				break;
			}
		}
		if ($this->campos[$columna] == 'tippernom')
		{
			$this->tippernom = str_replace("'","",$valor);
		}
		if (($this->campos[$columna] == 'codtipsol') && ($this->_table == 'sep_solicitud') && ($this->conversion))
		{
			$valorant = str_replace("'","",$valor);
			if (strlen(trim($valorant)) <= 0 )
			{
				$valor = "'01'";
			}
		}		
		if ((($this->_table == 'soc_ordencompra') || ($this->_table == 'soc_dt_servicio') || 
			 ($this->_table == 'sep_solicitud') || ($this->_table == 'soc_enlace_sep') || 
			 ($this->_table == 'soc_dtsc_bienes') || ($this->_table == 'soc_sol_cotizacion') || 
			 ($this->_table == 'soc_dt_bienes') || ($this->_table == 'soc_solcotsep')) && 
			 ($this->campos[$columna] == 'coduniadm') || ($this->campos[$columna] == 'codunieje'))
		{
			$this->unidadadmin = str_replace("'","",$valor);
		}
		if ((($this->_table == 'soc_dt_servicio') || ($this->_table == 'soc_ordencompra') || 
		     ($this->_table == 'sep_solicitud') || ($this->_table == 'soc_enlace_sep') || 
		     ($this->_table == 'soc_dtsc_bienes')  || ($this->_table == 'soc_sol_cotizacion') || 
		     ($this->_table == 'soc_dt_bienes') || ($this->_table == 'soc_solcotsep')) && 
		     ($this->campos[$columna] == 'codestpro1') && ($this->bsf) && ($this->conversion))
		{
			if($this->obtenerCodestpro1('codestpro1')<>'')
			{
				$valor = $this->obtenerCodestpro1('codestpro1');
			}
			$this->codestpro1=$valor;
			$valor=str_pad($valor,25,"0",0);
			$valor = "'".$valor."'";			
		}
		if ((($this->_table == 'soc_dt_servicio') || ($this->_table == 'soc_ordencompra') || 
		     ($this->_table == 'sep_solicitud') || ($this->_table == 'soc_enlace_sep') || 
		     ($this->_table == 'soc_dtsc_bienes')  || ($this->_table == 'soc_sol_cotizacion') || 
		     ($this->_table == 'soc_dt_bienes') || ($this->_table == 'soc_solcotsep') || 
		     ($this->_table == 'cxp_rd_spg')) && 
		     ($this->campos[$columna] == 'estcla') && ($this->bsf) && ($this->conversion))
		{
			if($this->obtenerEstatusClasificacion('estcla')<>'')
			{
				$valor = $this->obtenerEstatusClasificacion('estcla');
			}
			$this->estcla=$valor;
			$valor = "'".$valor."'";			
		}
		if ((($this->_table == 'soc_dt_servicio') || ($this->_table == 'soc_ordencompra') || 
		     ($this->_table == 'sep_solicitud') || ($this->_table == 'soc_enlace_sep') || 
		     ($this->_table == 'soc_dtsc_bienes') || ($this->_table == 'soc_sol_cotizacion') || 
		     ($this->_table == 'soc_dt_bienes') || ($this->_table == 'soc_solcotsep')) && 
		     ($this->campos[$columna] == 'codestpro2') && ($this->bsf) && ($this->conversion))
		{
			if($this->obtenerCodestpro1('codestpro2')<>'')
			{
				$valor = $this->obtenerCodestpro1('codestpro2');
			}
			$valor=str_pad($valor,25,"0",0);
			$valor = "'".$valor."'";
		}
		if ((($this->_table == 'soc_dt_servicio') || ($this->_table == 'soc_ordencompra') || 
		     ($this->_table == 'sep_solicitud') || ($this->_table == 'soc_enlace_sep') || 
		     ($this->_table == 'soc_dtsc_bienes') || ($this->_table == 'soc_sol_cotizacion') || 
		     ($this->_table == 'soc_dt_bienes') || ($this->_table == 'soc_solcotsep')) && 
		     ($this->campos[$columna] == 'codestpro3') && ($this->bsf) && ($this->conversion))
		{
			if($this->obtenerCodestpro1('codestpro3')<>'')
			{
				$valor = $this->obtenerCodestpro1('codestpro3');
			}
			$valor=str_pad($valor,25,"0",0);
			$valor = "'".$valor."'";
		}
		if ((($this->_table == 'soc_dt_servicio') || ($this->_table == 'soc_ordencompra') || 
		     ($this->_table == 'sep_solicitud') || ($this->_table == 'soc_enlace_sep') || 
		     ($this->_table == 'soc_dtsc_bienes') || ($this->_table == 'soc_sol_cotizacion') || 
		     ($this->_table == 'soc_dt_bienes') || ($this->_table == 'soc_solcotsep')) && 
		     ($this->campos[$columna] == 'codestpro4') && ($this->bsf) && ($this->conversion))
		{
			if($this->obtenerCodestpro1('codestpro4')<>'')
			{
				$valor = $this->obtenerCodestpro1('codestpro4');
			}
			$valor=str_pad($valor,25,"0",0);
			$valor = "'".$valor."'";
		}
		if ((($this->_table == 'soc_dt_servicio') || ($this->_table == 'soc_ordencompra') || 
		     ($this->_table == 'sep_solicitud') || ($this->_table == 'soc_enlace_sep') || 
		     ($this->_table == 'soc_dtsc_bienes') || ($this->_table == 'soc_sol_cotizacion') || 
		     ($this->_table == 'soc_dt_bienes') || ($this->_table == 'soc_solcotsep')) && 
		     ($this->campos[$columna] == 'codestpro5') && ($this->bsf) && ($this->conversion))
		{
			if($this->obtenerCodestpro1('codestpro5')<>'')
			{
				$valor = $this->obtenerCodestpro1('codestpro5');
			}
			$valor=str_pad($valor,25,"0",0);
			$valor = "'".$valor."'";
		}
		return $valor;
	}
	
    
/***********************************************************************************
* @Función que completa los Estados Municipios y Parroquias por Defecto
* @parametros: 
* @retorno: 
* @fecha de creación: 31/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function completarUbicacion($tipo)
	{
		switch ($tipo)
		{
			case 'ESTADO':
				$consulta= " INSERT INTO sigesp_estados(codpai, codest, desest) ".
						   " SELECT codpai, '---', 'por defecto' ".
						   "   FROM sigesp_pais ".
						   "  WHERE codpai<>'---' ".
				  		   "    AND codpai NOT IN (SELECT codpai ".
						   "					     FROM sigesp_estados ".
						   "                        WHERE sigesp_pais.codpai=sigesp_estados.codpai ".
						   "                          AND codest='---') ".
						   "  GROUP BY codpai ";
			break;
			
			case 'MUNICIPIO':
				$consulta= " INSERT INTO sigesp_municipio(codpai, codest, codmun, denmun) ".
						   " SELECT codpai, codest, '---', 'por defecto' ".
						   "   FROM sigesp_estados ".
						   "  WHERE codpai<>'---' ".
						   "    AND codpai NOT IN (SELECT codpai ".
						   "					     FROM sigesp_municipio ".
						   "                        WHERE sigesp_estados.codpai=sigesp_municipio.codpai ".
						   "                          AND sigesp_estados.codest=sigesp_municipio.codest ".
						   "                          AND codmun='---') ".
						   "  GROUP BY codpai, codest ";
			break;
			
			case 'PARROQUIA':
				$consulta= " INSERT INTO sigesp_parroquia(codpai, codest, codmun, codpar, denpar) ".
						   " SELECT codpai, codest, codmun, '---', 'por defecto' ".
						   "   FROM sigesp_municipio ".
						   "  WHERE codpai<>'---' ".
						   "    AND codpai NOT IN (SELECT codpai ".
						   "						 FROM sigesp_parroquia ".
						   "                        WHERE sigesp_municipio.codpai=sigesp_parroquia.codpai ".
						   "                          AND sigesp_municipio.codest=sigesp_parroquia.codest ".
						   "                          AND sigesp_municipio.codmun=sigesp_parroquia.codmun ".
						   "                          AND codpar='---') ".
						   "  GROUP BY codpai, codest, codmun  ";
			break;
		}
		$this->consultaactual = $consulta;									
		$resultado = $this->conexionbd->Execute($consulta); 	

	}
	
	
/***********************************************************************************
* @Función que busca en los arboles de seguridad y los inserta en Sistema Ventana
* @parametros: 
* @retorno: 
* @fecha de creación: 21/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function procesarSistemaVentana()
    {   
    	escribirArchivo($this->archivo,'*******************************************************************************************************');
		escribirArchivo($this->archivo,'*		Conversión tabla sss_sistemas_ventanas');
		escribirArchivo($this->archivo,'*******************************************************************************************************');
    	$objsistema =  New Sistema();
		$resultsistema = $objsistema->leer();
    	$codmenu=31;
		while (!$resultsistema->EOF)
		{
			$codsis = $resultsistema->fields['codsis'];
			$codsisaux = strtolower($codsis);
			$inicio=($codmenu-1);
			if (($codsisaux != 'apr') && ($codsisaux != 'sss'))
			{
				if(file_exists('../../sss/arbol/sigesp_arbol_'.$codsisaux.'.php'))
				{
					include('../../sss/arbol/sigesp_arbol_'.$codsisaux.'.php');
					for ($contador = 1; $contador <= $gi_total; $contador++)
					{
						$codpadre=0;
						if (intval($arbol['padre'][$contador])>0)
						{
							$codpadre=$inicio+$arbol['padre'][$contador];
						}
						$hijo = 0;
						if (intval($arbol['numero_hijos'][$contador])>0)
						{
							$hijo = 1;
						}
						$objsistemaventana =  New SistemaVentana();
						$objsistemaventana->codmenu   = $codmenu;
						$objsistemaventana->codsis    = $codsis;
						$objsistemaventana->nomlogico = $arbol['nombre_logico'][$contador];
						$objsistemaventana->nomfisico = $arbol['nombre_fisico'][$contador];
						$objsistemaventana->codpadre  = $codpadre;
						$objsistemaventana->nivel     = $arbol['nivel'][$contador]+1;
						$objsistemaventana->hijo      = $hijo;
						$objsistemaventana->marco     = 'principal';
						$objsistemaventana->orden     = $arbol['id'][$contador];
						$objsistemaventana->visible   = 1;
						$objsistemaventana->enabled   = 1;
						$objsistemaventana->leer      = 1;
						$objsistemaventana->incluir   = 1;
						$objsistemaventana->cambiar   = 1;
						$objsistemaventana->eliminar  = 1;
						$objsistemaventana->imprimir  = 1;
						$objsistemaventana->administrativo = 1;
						$objsistemaventana->anular         = 1;
						$objsistemaventana->ejecutar       = 1;
						$objsistemaventana->ayuda          = 1;
						$objsistemaventana->cancelar       = 1;
						$objsistemaventana->enviarcorreo   = 0;
						$objsistemaventana->descargar      = 1;
						if($objsistemaventana->nomfisico=='')
						{
							$objsistemaventana->nomfisico=' ';
						}

				    	$objsistemaventana->incluirLocal();			    	
						$codmenu++;
						unset($objsistemaventana);
						escribirArchivo($this->archivo,'* '.date("Y-m-d H:i:s"));
					}
				}
			}
			$resultsistema->MoveNext();
		}
		unset($objsistema);
		escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		escribirArchivo($this->archivo,'');
    }

    
/***********************************************************************************
* @Función que busca los derechos usuario y los inserta en la tabla según el código de Menu
* @parametros: 
* @retorno: 
* @fecha de creación: 24/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function procesarDerechosUsuarios($conexionorigen)
    {
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		escribirArchivo($this->archivo,'*		Conversión tabla sss_derechos_usuarios');
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		$totalorigen=0;
		$totaldestino=0;
		$this->_table='sss_derechos_usuarios';
		$this->criterio=" WHERE codsis <> 'APR' AND codsis <> 'SFP' ".
						"   AND codusu <> 'apertura' ".
						" GROUP BY codemp, codusu, codsis, nomven";
		$result = $this->obtenerDatosOrigen($conexionorigen);
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
				$cadenacampos = '';
				$cadenavalores = '';							
				for ($columna = 0; (($columna < $totcolumna) && $this->valido); $columna++)
				{
					$objsistemaventana =  New SistemaVentana();
					$tipodato  = '';
					$valor = '';
					$objeto = $result->FetchField($columna);
					$campo  = $objeto->name;
					if ($campo == 'nomven')
					{
						$codsis = strtoupper(rtrim($result->fields['codsis']));
						$nomven = rtrim($result->fields[$objeto->name]);
						$objsistemaventana->codsis   = $codsis;
						$objsistemaventana->nomfisico = $nomven;
						if($objsistemaventana->nomfisico=='')
						{
							$objsistemaventana->nomfisico='-';
						}
						if ($codsis == 'SSS')
						{
							switch ($nomven)
							{
								case 'sigespwindow_sss_sistemas.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_sistema.html';
								break;
								
								case 'sigespwindow_sss_grupos.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_grupo.html';
								break;
								
								case 'sigespwindow_sss_usuarios.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_usuario.html';
								break;
								
								case 'sigesp_sss_p_usuariosnominas.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_usuariosnomina.html';
								break;
								
								case 'sigesp_sss_p_usuariospresupuesto.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_usuariospresupuesto.html';
								break;
								
								case 'sigesp_sss_p_usuariosunidad.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_usuariosunidad.html';
								break;
								
								case 'sigesp_sss_p_usuariosconstantes.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_usuariosconstante.html';
								break;

								case 'sigesp_sss_p_traspasar_usuarios.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_transferirusuario.html';
								break;
								
								case 'sigesp_c_permisos_globales.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_perfiles.html';
								break;
								
								case 'sigespwindow_sss_auditoria.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_auditoria.html';
								break;
								
								case 'sigesp_sss_r_permisos.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_permisos.html';
								break;
								
								case 'sigesp_sss_r_traspasos.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_traspasos.html';
								break;
								
								default:
									$objsistemaventana->nomfisico = $nomven;
								break;
								
							}
						}
						$nomven = $objsistemaventana->nomfisico;
						$codmenu=intval($objsistemaventana->obtenerCodigoMenu());
						$campo = 'codmenu';
						$tipodato   = 'N';
						$valor=$codmenu;
					}
					else
					{
						$tipodato   = $result->MetaType($objeto->type);
						$valor = $result->fields[$objeto->name];								
					}
					if ($campo == 'codsis')
					{
						$valor = strtoupper($valor);
						$objsistemaventana->codsis   = $valor;
						$codsis = $valor;
					}
					if ($campo == 'codmenu')
					{
						$valor = strtoupper($valor);
						$objsistemaventana->codmenu   = $valor;
						$codmenu = $valor;
					}
					if (($campo == 'visible') || ($campo == 'enabled') || ($campo == 'leer') || ($campo == 'incluir') ||
					    ($campo == 'cambiar') || ($campo == 'eliminar') || ($campo == 'imprimir') || ($campo == 'administrativo') ||
					    ($campo == 'anular') || ($campo == 'ejecutar') || ($campo=='cancelar') || 
					    ($campo=='enviarcorreo') || ($campo=='descargar')|| ($campo=='ayuda'))
					{
						$objsistemaventana->codsis   = $codsis;
						$objsistemaventana->codmenu = $codmenu;
						$objsistemaventana->campo = $campo;
						if (($codsis == 'SSS') && ($valor=='1'))
						{
							$valor = intval($objsistemaventana->verificarCampoMenu());
						}
						if($campo == 'visible')
						{
							$valor = 1;
						}
					}
 					$clave = array_search($campo, $this->campos);
					if (is_numeric($clave))
					{
						// Actualizo el valor según el tipo de dato
						$valor=$this->actualizarValor($tipodato,$valor);
						$valor=$this->criterioConversion($columna,$valor);
						$cadenacampos.=','.$this->campos[$columna];
						$cadenavalores.=','.$valor;
					}
					unset($objsistemaventana);
				}
				$consulta= "INSERT INTO sss_derechos_usuarios (".substr($cadenacampos,1).") ".
						   "SELECT ".substr($cadenavalores,1)." ".
						   "  FROM sss_sistemas_ventanas ".
						   " WHERE codsis = '".$codsis."' ".
						   "   AND codmenu = ".$codmenu." ";
				// Ejecuto la Consulta en la Base de Datos Destino.
				$this->consultaactual = $consulta;									
				$resultado = $this->conexionbd->Execute($consulta);
				if ($resultado===false) {
					escribirArchivo($this->archivo,'* '.date("Y-m-d H:i:s").' SQL -> '.$consulta);
					break;
				}
				escribirArchivo($this->archivo,'* '.date("Y-m-d H:i:s").' contador->'.$totaldestino);
				$totaldestino++;
				$result->MoveNext();
			}
			$result->Close();
		}
		escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
		escribirArchivo($this->archivo,'* Registros Origen derecho usuario -> '.$totalorigen);
		escribirArchivo($this->archivo,'* Registros Destino derecho usuario -> '.$totaldestino);		
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		escribirArchivo($this->archivo,'');
    }    


/***********************************************************************************
* @Función que busca los derechos Grupos y los inserta en la tabla según el código de Menu
* @parametros: 
* @retorno: 
* @fecha de creación: 27/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function procesarDerechosGrupos($conexionorigen)
    {
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		escribirArchivo($this->archivo,'*		Conversión tabla sss_derechos_grupos');
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		$totalorigen=0;
		$totaldestino=0;
		$this->_table='sss_derechos_grupos';
		$this->criterio=" WHERE codsis <> 'APR' AND codsis <> 'SFP' ".
						"   AND codintper IN (SELECT codintper ".
						"						FROM sss_permisos_internos_grupos ".
						"					   WHERE sss_permisos_internos_grupos.codsis = sss_derechos_grupos.codsis ".
						" 						 AND sss_permisos_internos_grupos.nomgru = sss_derechos_grupos.nomgru)";
		$result = $this->obtenerDatosOrigen($conexionorigen);
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
				$cadenacampos = '';
				$cadenavalores = '';							
				for ($columna = 0; (($columna < $totcolumna) && $this->valido); $columna++)
				{
					$tipodato  = '';
					$valor = '';
					$objeto = $result->FetchField($columna);
					$campo  = $objeto->name;
					if ($campo == 'nomven')
					{
						$codsis = strtoupper(rtrim($result->fields['codsis']));
						$nomven = rtrim($result->fields[$objeto->name]);
						$objsistemaventana =  New SistemaVentana();
						$objsistemaventana->codsis   = $codsis;
						$objsistemaventana->nomfisico = $nomven;	
						if ($codsis == 'SSS')
						{
							switch ($nomven)
							{
								case 'sigespwindow_sss_sistemas.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_sistema.html';
								break;
								
								case 'sigespwindow_sss_grupos.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_grupo.html';
								break;
								
								case 'sigespwindow_sss_usuarios.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_usuario.html';
								break;
								
								case 'sigesp_sss_p_usuariosnominas.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_usuariosnomina.html';
								break;
								
								case 'sigesp_sss_p_usuariospresupuesto.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_usuariospresupuesto.html';
								break;
								
								case 'sigesp_sss_p_usuariosunidad.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_usuariosunidad.html';
								break;
								
								case 'sigesp_sss_p_usuariosconstantes.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_usuariosconstante.html';
								break;

								case 'sigesp_sss_p_traspasar_usuarios.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_transferirusuario.html';
								break;
								
								case 'sigespwindow_sss_derecho_grupo.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_perfiles.html';
								break;
								
								case 'sigesp_c_permisos_globales.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_perfiles.html';
								break;
								
								case 'sigespwindow_sss_auditoria.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_auditoria.html';
								break;
								
								case 'sigesp_sss_r_permisos.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_permisos.html';
								break;
								
								case 'sigesp_sss_r_traspasos.php':
									$objsistemaventana->nomfisico = 'sigesp_vis_sss_traspasos.html';
								break;
								
								default:
									$objsistemaventana->nomfisico = $nomven;
								break;
								
							}
						}
						$codmenu=intval($objsistemaventana->obtenerCodigoMenu());
						unset($objsistemaventana);
						$campo = 'codmenu';
						$tipodato   = 'N';
						$valor=$codmenu;
					}
					else
					{
						$tipodato   = $result->MetaType($objeto->type);
						$valor = $result->fields[$objeto->name];								
					}
					if ($campo == 'codsis')
					{
						$valor = strtoupper($valor);
						$objsistemaventana->codsis   = $valor;
						$codsis = $valor;
					}
					if ($campo == 'codmenu')
					{
						$valor = strtoupper($valor);
						$objsistemaventana->codmenu   = $valor;
						$codmenu = $valor;
					}
					if (($campo == 'visible') || ($campo == 'enabled') || ($campo == 'leer') || ($campo == 'incluir') ||
					    ($campo == 'cambiar') || ($campo == 'eliminar') || ($campo == 'imprimir') || ($campo == 'administrativo') ||
					    ($campo == 'anular') || ($campo == 'ejecutar') || ($campo=='cancelar') || 
					    ($campo=='enviarcorreo') || ($campo=='descargar')|| ($campo=='ayuda'))
					{
						$objsistemaventana->codsis   = $codsis;
						$objsistemaventana->codmenu = $codmenu;
						$objsistemaventana->campo = $campo;
						if (($codsis == 'SSS') && ($valor=='1'))
						{
							$valor = intval($objsistemaventana->verificarCampoMenu());
						}
					}
					$clave = array_search($campo, $this->campos);
					if (is_numeric($clave))
					{
						// Actualizo el valor según el tipo de dato
						$valor=$this->actualizarValor($tipodato,$valor);
						$valor=$this->criterioConversion($columna,$valor);
						$cadenacampos.=','.$this->campos[$columna];
						$cadenavalores.=','.$valor;
					}
				}
				$consulta= "INSERT INTO sss_derechos_grupos (".substr($cadenacampos,1).") ".
						   "SELECT ".substr($cadenavalores,1)." ".
						   "  FROM sss_sistemas_ventanas ".
						   " WHERE codsis = '".$codsis."' ".
						   "   AND codmenu = ".$codmenu." ";
				// Ejecuto la Consulta en la Base de Datos Destino.
				$this->consultaactual = $consulta;
				$resultado = $this->conexionbd->Execute($consulta);
				escribirArchivo($this->archivo,'* '.date("Y-m-d H:i:s"));
				$totaldestino++;
				$result->MoveNext();
			}
			$result->Close();
		}		
		escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
		escribirArchivo($this->archivo,'* Registros Origen  -> '.$totalorigen);
		escribirArchivo($this->archivo,'* Registros Destino -> '.$totaldestino);		
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		escribirArchivo($this->archivo,'');
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
	public function eliminarDatosBasicos()
	{
		$this->mensaje='Elimino la apertura del sistema '.$this->sistema;
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
			$objconfiguracion->seccion = 'APERTURA';
			$objconfiguracion->entry = 'APERTURA';
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
* @Función que obtiene el estatus de clasificación de una estructura presupuestaria
* @parametros: 
* @retorno:
* @fecha de creación: 29/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function obtenerCodestpro1($campo)
	{
		$this->conexionbdorigen = conectarBD($_SESSION['sigesp_servidor'], $_SESSION['sigesp_usuario'], $_SESSION['sigesp_clave'],
									   $_SESSION['sigesp_basedatos'], $_SESSION['sigesp_gestor'], $_SESSION['sigesp_puerto']);
		 		
		$codestpro ='';
		if ($this->unidadadmin == '----------')
		{
			$codestpro = '-------------------------';
		}
		else
		{	
			$consulta="SELECT ".$campo." ".
					  " FROM spg_unidadadministrativa".
					  " WHERE codemp = '{$this->codemp}'".
					  " AND coduniadm = '".$this->unidadadmin."'";
			$result = $this->conexionbdorigen->Execute($consulta);
			if (!$result->EOF)
			{		
				$codestpro = $result->fields[$campo];		
			}
			else
			{
				$codestpro="";
			}
			$result->Close();
		}
		return $codestpro;
	}


/***********************************************************************************
* @Función que obtiene el estatus de clasificación de una estructura presupuestaria
* @parametros: 
* @retorno:
* @fecha de creación: 29/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function obtenerEstatusClasificacion()
	{
		$estcla ='';
		if ($this->codestpro1 == '-------------------------')
		{
			$estcla = '-';
		}
		else
		{
			$consulta="SELECT estcla ".
					  "  FROM spg_ep1 ".
					  " WHERE codemp = '{$this->codemp}' ".
					  "   AND codestpro1 = '".str_pad($this->codestpro1,25,0,0)."' ";
			$result = $this->conexionbd->Execute($consulta);
			if (!$result->EOF)
			{		
				$estcla = $result->fields['estcla'];		
			}
			else
			{
				$estcla='';
			}
			$result->Close();
		} 
		return $estcla;
	}

/***********************************************************************************
* @Función que convierte la estructura presupuestaria de la versión bsf a versión ipsfa 
* @parametros: 
* @retorno:
* @fecha de creación: 12/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function convertirLongitudEstructuras($estructura)
	{
		$estructura = str_replace("/","_",$estructura);
		if (strlen(trim($estructura)) == 33)
		{
			$codestpro1=str_pad(substr(trim($estructura),0,20),25,'0',0);
			$codestpro2=str_pad(substr(trim($estructura),20,6),25,'0',0);
			$codestpro3=str_pad(substr(trim($estructura),26,3),25,'0',0);
			$codestpro4=str_pad(substr(trim($estructura),29,2),25,'0',0);
			$codestpro5=str_pad(substr(trim($estructura),31,2),25,'0',0);
			$estructura = $codestpro1.$codestpro2.$codestpro3.$codestpro4.$codestpro5;	
		}
		$this->estructura1=true;
		$this->codestpro1=substr(trim($estructura),0,25);
		return $estructura; 
	}

/***********************************************************************************
* @Función que convierte la estructura presupuestaria de la versión bsf a versión ipsfa con estatus de Clasificación
* @parametros: 
* @retorno:
* @fecha de creación: 12/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function convertirLongitudEstructurasEstatus($estructura)
	{
		$estructura = str_replace("/","_",$estructura);
		if (strlen(trim($estructura)) == 33)
		{
			$codestpro1=str_pad(substr(trim($estructura),0,20),25,'0',0);
			$codestpro2=str_pad(substr(trim($estructura),20,6),25,'0',0);
			$codestpro3=str_pad(substr(trim($estructura),26,3),25,'0',0);
			$codestpro4=str_pad(substr(trim($estructura),29,2),25,'0',0);
			$codestpro5=str_pad(substr(trim($estructura),31,2),25,'0',0);
			$this->estructura1=true;
			$this->codestpro1=$codestpro1;
			$estcla = $this->obtenerEstatusClasificacion();
			$estructura = $codestpro1.$codestpro2.$codestpro3.$codestpro4.$codestpro5.$estcla;	
		}
		return $estructura;
	}
	
/***********************************************************************************
* @Función que Verifica que los detalles de las unidades Administrativas estén en blanco 
* y de ser así busca en la BD origen
* @parametros: 
* @retorno:
* @fecha de creación: 11/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function insertarUnidadAdministrativa($conexionbdorigen)
	{
		if ($this->estructura1)
		{
			$this->campos = Array();
			$this->campos[0] = 'codestpro1';
			$this->campos[1] = 'codestpro2';
			$this->campos[2] = 'codestpro3';
			$this->campos[3] = 'codestpro4';
			$this->campos[4] = 'codestpro5';
			$this->campos[5] = 'estcla';
			$this->campos[6] = 'coduniadm';
			$consulta="SELECT codemp, coduniadm, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, '' AS estcla ".
					  "  FROM spg_unidadadministrativa ";  
			$result = $this->conexionbdorigen->Execute($consulta);
			while (!$result->EOF)
			{
				$codemp = $result->fields['codemp'];
				$coduniadm = $result->fields['coduniadm'];
				$codestpro1 = $result->fields['codestpro1'];
				$codestpro2 = $result->fields['codestpro2'];
				$codestpro3 = $result->fields['codestpro3'];
				$codestpro4 = $result->fields['codestpro4'];
				$codestpro5 = $result->fields['codestpro5'];
				$estcla = $result->fields['estcla'];
				
				$codestpro1=$this->criterioConversion(0,$codestpro1);	
				$codestpro2=$this->criterioConversion(1,$codestpro2);
				$codestpro3=$this->criterioConversion(2,$codestpro3);
				$codestpro4=$this->criterioConversion(3,$codestpro4);
				$codestpro5=$this->criterioConversion(4,$codestpro5);
				$coduniadm=$this->criterioConversion(6,$coduniadm);
				$this->codestpro1 = str_replace("'","",$codestpro1);
				$estcla = "'".$this->obtenerEstatusClasificacion()."'";
				
				$consulta="INSERT INTO  spg_dt_unidadadministrativa (codemp, coduniadm, codestpro1, codestpro2, codestpro3, ".
						  "codestpro4, codestpro5, estcla) VALUES ('".$codemp."', ".$coduniadm.", ".$codestpro1.", ".
						  "".$codestpro2.",".$codestpro3.",".$codestpro4.",".$codestpro5.",".$estcla.") "; 
				$this->consultaactual = $consulta;
				$resultado = $this->conexionbd->Execute($consulta);
				$result->MoveNext();
			}
			$result->Close();
		}
		$consulta="INSERT INTO spg_dt_unidadadministrativa (codemp, coduniadm, codestpro1, codestpro2, codestpro3, codestpro4, ".
				  "											codestpro5, estcla) ".
				  " SELECT codemp, coduniadm, '-------------------------', '-------------------------', '-------------------------', ".
				  "		   '-------------------------', '-------------------------', '-' ".
				  "   FROM spg_unidadadministrativa ".
				  "  WHERE coduniadm <> '----------'  ";
		$this->consultaactual = $consulta;
		$result = $this->conexionbd->Execute($consulta);
		$result->Close();	}

	
/***********************************************************************************
* @Función que inserta a todas las estructuras presupuestarias la fuente de financiamiento 
* por defecto
* @parametros: 
* @retorno:
* @fecha de creación: 11/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function insertarFuenteFinanciamiento()
	{
		unset ($this->campos);
		$consulta="INSERT INTO spg_dt_fuentefinanciamiento (codemp, codfuefin, codestpro1, codestpro2, codestpro3, codestpro4, ".
				  "											codestpro5, estcla) ".
				  " SELECT codemp, '--', codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla ".
				  "   FROM spg_ep5 ".
				  "  WHERE codestpro1 <> '-------------------------' ".
				  "    AND codestpro2 <> '-------------------------' ".
				  "    AND codestpro3 <> '-------------------------' ".
				  "    AND codestpro4 <> '-------------------------' ".
				  "    AND codestpro5 <> '-------------------------'  ".
				  "    AND estcla <> '-'  ".
				  "    AND codemp NOT IN (SELECT codemp FROM spg_dt_fuentefinanciamiento ".
				  "						   WHERE spg_dt_fuentefinanciamiento.codfuefin = '--' ".
				  "                          AND spg_dt_fuentefinanciamiento.codemp = spg_ep5.codemp ".
				  "                          AND spg_dt_fuentefinanciamiento.codestpro1 = spg_ep5.codestpro1 ".
				  "                          AND spg_dt_fuentefinanciamiento.codestpro2 = spg_ep5.codestpro2 ".
				  "                          AND spg_dt_fuentefinanciamiento.codestpro3 = spg_ep5.codestpro3 ".
				  "                          AND spg_dt_fuentefinanciamiento.codestpro4 = spg_ep5.codestpro4 ".
				  "                          AND spg_dt_fuentefinanciamiento.codestpro5 = spg_ep5.codestpro5 ".
				  "                          AND spg_dt_fuentefinanciamiento.estcla = spg_ep5.estcla) ";  
		$this->consultaactual = $consulta;
		$result = $this->conexionbd->Execute($consulta);
		$result->Close();
	}

	
/***********************************************************************************
* @Función que inserta a todas las cuentas la fuente de financiamiento 
* por defecto
* @parametros: 
* @retorno:
* @fecha de creación: 11/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function insertarCuentaFuenteFinanciamiento()
	{
		unset ($this->campos);
		$consulta="INSERT INTO spg_cuenta_fuentefinanciamiento (codemp, codfuefin, codestpro1, codestpro2, codestpro3, codestpro4, ".
				  "											codestpro5, estcla, spg_cuenta) ".
				  " SELECT codemp, '--', codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta ".
				  "   FROM spg_cuentas ". 
				  "  WHERE codemp NOT IN (SELECT codemp FROM spg_cuenta_fuentefinanciamiento ".
				  "						   WHERE spg_cuenta_fuentefinanciamiento.codfuefin = '--' ".
				  "                          AND spg_cuenta_fuentefinanciamiento.codemp = spg_cuentas.codemp ".
				  "                          AND spg_cuenta_fuentefinanciamiento.codestpro1 = spg_cuentas.codestpro1 ".
				  "                          AND spg_cuenta_fuentefinanciamiento.codestpro2 = spg_cuentas.codestpro2 ".
				  "                          AND spg_cuenta_fuentefinanciamiento.codestpro3 = spg_cuentas.codestpro3 ".
				  "                          AND spg_cuenta_fuentefinanciamiento.codestpro4 = spg_cuentas.codestpro4 ".
				  "                          AND spg_cuenta_fuentefinanciamiento.codestpro5 = spg_cuentas.codestpro5 ". 
				  "                          AND spg_cuenta_fuentefinanciamiento.estcla = spg_cuentas.estcla ".  
				  "                          AND spg_cuenta_fuentefinanciamiento.spg_cuenta = spg_cuentas.spg_cuenta) ";  
		$this->consultaactual = $consulta;
		$result = $this->conexionbd->Execute($consulta);
		$result->Close();
	}

	
/***********************************************************************************
* @Función que inserta a todas las cuentas de ingreso la estructura por defecto
* @parametros: 
* @retorno:
* @fecha de creación: 11/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function insertarCuentaEstructura()
	{
		unset ($this->campos);
		$consulta="SELECT * ".
				  "  FROM spg_ep5 ".
				  " WHERE codestpro1 = '-------------------------' ".
				  "   AND codestpro2 = '-------------------------' ".  
				  "   AND codestpro3 = '-------------------------' ".  
				  "   AND codestpro4 = '-------------------------' ".  
				  "   AND codestpro5 = '-------------------------' ".  
				  "   AND estcla = '-' ";  
		$result = $this->conexionbd->Execute($consulta);
		if ($result->EOF)
		{
			$consulta="INSERT INTO spg_ep1 (codemp, codestpro1, estcla, denestpro1) ".
					  " VALUES ('0001', '-------------------------', '-', 'POR DEFECTO')";  
			$this->consultaactual = $consulta;
			$resultado = $this->conexionbd->Execute($consulta);		
			
			$consulta="INSERT INTO spg_ep2 (codemp, codestpro1, codestpro2, estcla, denestpro2) ".
					  " VALUES ('0001', '-------------------------', '-------------------------', '-', 'POR DEFECTO')";  
			$this->consultaactual = $consulta;
			$resultado = $this->conexionbd->Execute($consulta);		
			
			$consulta="INSERT INTO spg_ep3 (codemp, codestpro1, codestpro2, codestpro3, estcla, denestpro3) ".
					  " VALUES ('0001', '-------------------------', '-------------------------', '-------------------------',".
					  "  '-', 'POR DEFECTO')";  
			$this->consultaactual = $consulta;
			$resultado = $this->conexionbd->Execute($consulta);		
			
			$consulta="INSERT INTO spg_ep4 (codemp, codestpro1, codestpro2, codestpro3, codestpro4, ".
					  "						estcla, denestpro4) ".
					  " VALUES ('0001', '-------------------------', '-------------------------', '-------------------------',".
					  " '-------------------------', '-', 'POR DEFECTO')";  
			$this->consultaactual = $consulta;
			$resultado = $this->conexionbd->Execute($consulta);		
			
			$consulta="INSERT INTO spg_ep5 (codemp, codestpro1, codestpro2, codestpro3, codestpro4, ".
					  "						codestpro5, estcla, denestpro5) ".
					  " VALUES ('0001', '-------------------------', '-------------------------', '-------------------------',".
					  " '-------------------------', '-------------------------', '-', 'POR DEFECTO')";  
			$this->consultaactual = $consulta;
			$resultado = $this->conexionbd->Execute($consulta);		
		}
		$result->Close();
		$consulta="INSERT INTO spi_cuentas_estructuras (codemp, codestpro1, codestpro2, codestpro3, codestpro4, ".
				  "										codestpro5, estcla, spi_cuenta) ".
				  " SELECT codemp, '-------------------------', '-------------------------', '-------------------------', ".
				  "		   '-------------------------', '-------------------------', '-', spi_cuenta ".
				  "   FROM spi_cuentas ".
				  "  WHERE spi_cuenta NOT IN (SELECT spi_cuenta FROM spi_cuentas_estructuras ".  
				  " 						   WHERE codestpro1 = '-------------------------' ".
				  "     						 AND codestpro2 = '-------------------------' ".  
				  "   							 AND codestpro3 = '-------------------------' ".  
				  "   							 AND codestpro4 = '-------------------------' ".  
				  "   							 AND codestpro5 = '-------------------------' ".  
				  "   							 AND estcla = '-') ";  
		$this->consultaactual = $consulta;
		$result = $this->conexionbd->Execute($consulta);
		$result->Close();
	}
		
	
/***********************************************************************************
* @Función que obtiene el valor nuevo de la Nómina
* @parametros: 
* @retorno:
* @fecha de creación: 07/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function obtenerValorNomina($codnom)
	{
		$codnuenom ='';
		if ($this->valornuevo == '')
		{
			$consulta="SELECT codnuenom ".
					  "  FROM apr_nomina ".
					  " WHERE codnom = '".$codnom."' ";
			$result = $this->conexionbd->Execute($consulta);
			if (!$result->EOF)
			{		
				$codnuenom = $result->fields['codnuenom'];		
			}
			$result->Close();
		}
		else
		{
			$codnuenom = $this->valornuevo;
		} 
		return $codnuenom;
	}
	
	
/***********************************************************************************
* @Función que Actualiza las nóminas para la fecha y período que corresponden
* @parametros: 
* @retorno:
* @fecha de creación: 04/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function actualizarNomina()
	{
		$consulta="UPDATE sno_nomina ".
				  "  SET anocurnom = '".substr($this->fecinimen,0,4)."', ".
				  "      fecininom = '".$this->fecinisem."',".
				  "      peractnom = '001'  ".
				  " WHERE codemp = '{$this->codemp}'  ".
				  "  AND tippernom = '0' ".
				  "  AND codnom = '".$this->valornuevo."'";  
		$this->consultaactual = $consulta;
		$result = $this->conexionbd->Execute($consulta);

		$consulta="UPDATE sno_nomina ".
				  "  SET anocurnom = '".substr($this->fecinimen,0,4)."', ".
				  "      fecininom = '".$this->fecinimen."',".
				  "      peractnom = '001'  ".
				  " WHERE codemp = '{$this->codemp}'  ".
				  "  AND tippernom <> '0' ".
				  "  AND codnom = '".$this->valornuevo."'";  
		$this->consultaactual = $consulta;
		$result = $this->conexionbd->Execute($consulta);

		$result->Close(); 
		$objNomina = new Nomina();
		$objNomina->codemp = $this->codemp;
		$objNomina->codnom = $this->valornuevo;
		$objNomina->tippernom = $this->tippernom;
		$objNomina->fecininom = $this->fecinimen;
		if ($this->tippernom == 0)
		{
			$objNomina->fecininom = $this->fecinisem;
		}
		$objNomina->generarPeriodos();
	}


/***********************************************************************************
* @Función que asocia los códigos de nómina anteriores con los actuales
* @parametros: 
* @retorno:
* @fecha de creación: 04/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function generarHistoricosAdicionales($conexionbdorigen)
	{
		// Se ubican todos los períodos Adicionales de la Base de datos Origen
		$consulta=" SELECT sno_periodo.fecdesper, sno_periodo.fechasper, sno_periodo.codperi, ".
				  "	 	   sno_periodo.totper, sno_nomina.anocurnom ".
				  "   FROM sno_nomina ".
				  "  INNER JOIN sno_periodo ".
				  "     ON sno_periodo.peradi = 1 ".
				  "    AND sno_periodo.cerper = 1 ".
				  "    AND sno_nomina.codemp = sno_periodo.codemp ".
				  "    AND sno_nomina.codnom = sno_periodo.codnom ".
				  "  WHERE sno_nomina.codnom = '".$this->valoractual."'".
				  "  ORDER BY sno_nomina.anocurnom,sno_periodo.codperi,sno_periodo.fecdesper, sno_periodo.fechasper";
		$result = $this->conexionbdorigen->Execute($consulta);
		while (!$result->EOF)
		{
			$anocurnom=$result->fields['anocurnom'];
			$totper=$result->fields['totper'];
			$fecdesper=substr($result->fields['fecdesper'],0,10);
			$fechasper=substr($result->fields['fechasper'],0,10);
			$codperiadi=$result->fields['codperi'];
			// Actualizo según la fecha de Inicio y Fin los periodos Adicionales
			$consulta=" UPDATE sno_periodo ".
					  "    SET cerper = 1,  ".
					  "        totper = ".$totper." ".
					  "  WHERE codnom = '".$this->valornuevo."' ".
					  "    AND fecdesper = '".$fecdesper."' ".
					  "    AND fechasper = '".$fechasper."' ";
			$this->consultaactual = $consulta;
			$resultdestino = $this->conexionbd->Execute($consulta);
			$resultdestino->Close();
			// Obtengo el Código del periodo Cerrado
			$consulta=" SELECT codperi ".
					  "   FROM sno_periodo ".
					  "	 WHERE codnom = '".$this->valornuevo."' ".
					  "    AND cerper = 1 ".
					  "    AND fecdesper = '".$fecdesper."' ".
					  "	   AND fechasper = '".$fechasper."' ";				
			$resultdestino = $this->conexionbd->Execute($consulta);
			$resultdestino->Close();
			
			if (!$resultdestino->EOF)
			{
				$peractnom = $resultdestino->fields['codperi'];
				$this->periodo = $peractnom;
				$peractnom = intval($resultdestino->fields['codperi'])+1;		
				$peractnom = str_pad($peractnom,3,'0',0);	
				// Se actualiza el período actual de la nómina al periodo Cerrado mas 1
				$consulta=" UPDATE sno_nomina ".
				 		  "    SET peractnom = '".$peractnom."' ".
						  "  WHERE codnom='".$this->valornuevo."' ";
				$this->consultaactual = $consulta;
				$resultdestino = $this->conexionbd->Execute($consulta);
				$resultdestino->Close();
			}				
			
			// Se recorre el arreglo de tablas de los históricos.
			$total=count((array)$this->historicos);
			for ( $contador = 0; (($contador < $total) && $this->valido); $contador++ )
			{
				$this->_table=$this->historicos[$contador]['tabla'];
				$this->tipo=$this->historicos[$contador]['tipo'];
				$this->criterio=" WHERE codnom = '".$this->valoractual."' AND codperi = '".$codperiadi."' AND anocur ='".$anocurnom."'";
				if ($this->_table == 'sno_hnomina')
				{
					$this->criterio=" WHERE codnom = '".$this->valoractual."' AND peractnom = '".$codperiadi."' AND anocurnom ='".$anocurnom."'";
				} 
				if ($this->_table == 'sno_banco')
				{
					$this->criterio=" WHERE codnom = '".$this->valoractual."' AND codperi = '".$codperiadi."'";
				} 
				$totalorigen=0;
				$totaldestino=0;

				escribirArchivo($this->archivo,'*******************************************************************************************************');
				escribirArchivo($this->archivo,'*		Conversión tabla Historica '.$this->_table);
				escribirArchivo($this->archivo,'*******************************************************************************************************');
			
				// Verifico que la tabla Exista en el origen.
				$this->verificarExistenciaTabla($this->conexionbdorigen);
				if (($this->valido) && ($this->existe))
				{
					// Obtengo los datos de la base de datos de origen según la configuració cargada
					$resultdestino = $this->obtenerDatosOrigen($this->conexionbdorigen);
					if ((!$resultdestino->EOF) && $this->valido)
					{
						$this->estructura1=false; // para validar el presupuesto de gasto cuando sea BSF
						$resultdestino->MoveFirst();
						$this->cargarCampos($resultdestino,$this->conexionbd);
						$resultdestino->MoveFirst();
						$totcolumna=count((array)$resultdestino->FetchRow());
						$resultdestino->MoveFirst();
						while ((!$resultdestino->EOF) && $this->valido)
						{
							$totalorigen++;
							$cadenacampos = '';
							$cadenavalores = '';
							$consulta = '';							
							for ($columna = 0; (($columna < $totcolumna) && $this->valido); $columna++)
							{
								$tipodato  = '';
								$valor = '';
								$objeto = $resultdestino->FetchField($columna);
								$campo  = $objeto->name;
								$tipodato   = $resultdestino->MetaType($objeto->type);
								$valor = $resultdestino->fields[$objeto->name];								
								$clave = array_search($campo, $this->campos);
								if (is_numeric($clave))
								{		
									// Actualizo el valor según el tipo de dato
									$valor=$this->actualizarValor($tipodato,$valor);
									// Aplico Criterios de Conversión en caso de ser necesario
									$valor=$this->criterioConversion($columna,$valor);
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
							// para validar el presupuesto de gasto cuando sea BSF
							if (($this->estructura1) && ($this->campos[$columna] == 'estcla'))
							{
								$this->estructura1=str_pad($resultdestino->fields['codestpro1'],25,'0',0);
								$valor = "'".$this->obtenerEstatusClasificacion()."'";
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
								// Ejecuto la Consulta en la Base de Datos Destino.
								$this->consultaactual = $consulta;
								$resultado = $this->conexionbd->Execute($consulta);
							} 							
							$resultdestino->MoveNext();
							$totaldestino++;
						}
					}
					$resultdestino->Close();	
				}
				escribirArchivo($this->archivo,'* Fecha  '.date("Y-m-d H:i:s"));
				escribirArchivo($this->archivo,'* Registros Origen  '.$this->valornuevo.' '.$codperiadi.'-> '.$totalorigen);
				escribirArchivo($this->archivo,'* Registros Destino '.$this->valornuevo.' '.$codperiadi.'-> '.$totaldestino);
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				escribirArchivo($this->archivo,'');
			}
			$result->moveNext();
		}
	}
	
	
/***********************************************************************************
* @Función que asocia los códigos de nómina anteriores con los actuales
* @parametros: 
* @retorno:
* @fecha de creación: 04/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function asociarCodigosNomina()
	{
		$consulta="DELETE FROM apr_nomina ";  
		$result = $this->conexionbd->Execute($consulta);
		
		$total=count((array)$this->nominas);
		for ( $contador = 0; (($contador < $total) && $this->valido); $contador++ )
		{
			$codnom = $this->nominas[$contador]->codnom;
			$codnuenom = $this->nominas[$contador]->codnuenom;
			$consulta="INSERT INTO apr_nomina (codnom, codnuenom) VALUES ('".$codnom."', '".$codnuenom."')";  
			$result = $this->conexionbd->Execute($consulta);
		}		
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