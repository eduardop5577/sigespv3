<?php
/***********************************************************************************
* @Modelo para el traspaso de solicitudes
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
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_conexion.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_funciones.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/mis/sigesp_srv_mis_comprobante.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/mis/sigesp_srv_mis_iintegracionsep.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/servicio/sss/sigesp_srv_sss_evento.php');

class  TraspasoObras extends DaoGenerico
{
	var $_table = 'sob_obra';
	var $obra = array();
	public $mensaje;
	public $valido = true;
	public $existe;
	public $criterio;
	public $numsolorigen;
	public $numobr;
	public $archivo;

	public function __construct() {
		parent::__construct ( 'sob_obra' );
		$this->conexionbd = $this->obtenerConexionBd(); 
		$this->objlibcon = new ConexionBaseDatos();
	}
	
	
/***********************************************************************************
* @Función para seleccionar con que conexion a Base de Datos se va a trabajar
* @parametros: 
* @retorno:
* @fecha de creación: 06/11/2008.
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	public function seleccionarConexion($conexionbd)
	{
		global $conexionbd;
		
		if ($this->tipoconexionbd != 'DEFECTO')
		{
			$conexionbd = $this->objlibcon->conectarBD($this->servidor, $this->usuario, $this->clave, $this->basedatos, $this->gestor, $this->puerto);
		}
	}	
	

/***********************************************************************************
* @Función que Busca uno o todas las obras
* @parametros: 
* @retorno:
* @fecha de creación: 11/12/2015
* @autor: Ing. Luis Anibal Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
************************************************************************************/		
	public function leer() 
 	{	
 		global $conexionbd; 		
		$this->seleccionarConexion($conexionbd);			
		$consulta = " SELECT codobr,desobr,	                                ".
					" 	(SELECT MAX(codasi)       			                ".
					"		FROM sob_asignacion 							".
					"		WHERE sob_asignacion.codobr=sob_obra.codobr 	".
					"		GROUP BY codobr) as codasi, 					".
					" 	(SELECT MAX(codcon)       			                ".
					"	   FROM sob_asignacion,sob_contrato 				".
					"	  WHERE sob_asignacion.codobr=sob_obra.codobr 		".
					"		AND sob_contrato.codasi=sob_asignacion.codasi 	".
					"		GROUP BY sob_asignacion.codobr) as codcon ,					".
					" 	(SELECT MAX(feccon)       			                ".
					"	   FROM sob_asignacion,sob_contrato 				".
					"	  WHERE sob_asignacion.codobr=sob_obra.codobr 		".
					"		AND sob_contrato.codasi=sob_asignacion.codasi 	".
					"		GROUP BY sob_asignacion.codobr) as feccon 						".
					" FROM {$this->_table} 									".
					" WHERE codemp='{$this->codemp}'";
		$cadena=" ";
        $total = count((array)$this->criterio);
        for ($contador = 0; $contador < $total; $contador++)
		{
            $cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 		               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
        }
        $consulta.= $cadena;            
        $consulta.= " ORDER BY codobr";
        $result = $conexionbd->Execute($consulta);           	  
		return $result;
 	}

/***********************************************************************************
 * @Función que pasa las Obras de Años anteriores para el año Actual
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/	
	public function procesarTraspasos() 
	{
		

		try
		{
			escribirArchivo($this->archivo,'*******************************************************************************************************');
			escribirArchivo($this->archivo,'                                 TRASPASO DE OBRAS');
			escribirArchivo($this->archivo,'*******************************************************************************************************');
			$conexionbdorigen = $this->objlibcon->conectarBD($_SESSION['sigesp_servidor_apr'], $_SESSION['sigesp_usuario_apr'], $_SESSION['sigesp_clave_apr'],
										   $_SESSION['sigesp_basedatos_apr'], $_SESSION['sigesp_gestor_apr'], $_SESSION['sigesp_puerto_apr']);
			$total = count((array)$this->obra);
			for ($i=0; $i < $total; $i++)
			{
				$this->conexionbd->StartTrans();
				$this->codobrorigen = $this->obra[$i]->codobr;
				$this->codobr = $this->obra[$i]->codobr;
				if( !$this->existeEnDestino())
				{
					escribirArchivo($this->archivo,'Obra Origen '.$this->codobrorigen);
					$this->procesar($conexionbdorigen);
					if ($this->valido)
					{
						$this->mensaje = 'El proceso se ejecutó satisfactoriamente.';
						escribirArchivo($this->archivo,'El proceso se ejecutó satisfactoriamente.');
						escribirArchivo($this->archivo,'*******************************************************************************************************');
					}
				}
				else
				{
					$this->valido  = false;
					$this->mensaje = 'La Obra '.$this->codobrorigen.' ya existe, en la Base de Datos Destino ';
					escribirArchivo($this->archivo,'La Obra '.$this->codobrorigen.' ya existe en la Base de Datos Destino ');
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$this->conexionbd->CompleteTrans($this->valido);
			}
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;
			$this->mensaje = 'Ocurrio un error en la Transferencia. '.$this->conexionbd->ErrorMsg();
			escribirArchivo($this->archivo,'* Ocurrio un error en la Transferencia. ');
			escribirArchivo($this->archivo,'* Error  '.$this->conexionbd->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
	   	} 
		//$this->incluirSeguridad('PROCESAR',$this->valido);
	}
 	
/***********************************************************************************
 * @Función para buscar si la obra existe en la base de datos destino.
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function existeEnDestino()
	{
		
				
		$existe = false;
		$consulta="SELECT count(codemp) as total ".
				  "  FROM {$this->_table} ".
				  " WHERE codemp='{$this->codemp}' ".
				  "   AND codobr='{$this->codobr}' ";
		$result = $this->conexionbd->Execute($consulta);
		if ($result===false)
		{
			$this->mensaje = $this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$result->EOF)
			{
				if (number_format($result->fields['total'],0) > 0)
				{
					$existe = true;
				}				
			}
			$result->Close();
		}
		return $existe;
	}

/***********************************************************************************
 * @Función para procesar la obra y sus relaciones
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function procesar($conexionbdorigen)
	{
		if (!$this->existeEnDestino()) // Se verifica que la solicitud de pago no se haya procesado
		{
			$this->copiarObra($conexionbdorigen);
			if ($this->valido)
			{
				$this->copiarPartidaObra($conexionbdorigen);
			}
			if ($this->valido)
			{
				$this->copiarFuenteFinanciamientoObra($conexionbdorigen);
			}
			if ($this->valido)
			{
				$this->copiarAsignacion($conexionbdorigen);
			}
		}
	}

/***********************************************************************************
 * @Función para insertar la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarObra($conexionbdorigen)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_obra ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codobr = '{$this->codobr}' ".
					"   AND staobr <> 3";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			if (!$result->EOF)
			{
				if($result->fields['estapr']=="")
				{
					$result->fields['estapr']=0;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_obra (codemp, codobr,codten,codtipest,codsiscon,codpro, 			".
							"			codtob, codpai,codest, codmun, codpar,codcom, desobr, dirobr, 			".
							"			obsobr, resobr, feciniobr, fecfinobr, cantobr, monto,feccreobr,staobr,                ".
  							"           fecapr, estapr,basimp,monimp) 																".
							" VALUES ('{$this->codemp}','{$this->codobr}','".$result->fields['codten']."', 								".
							" 		'".$result->fields['codtipest']."','".$result->fields['codsiscon']."',					".
							"		'".$result->fields['codpro']."','".$result->fields['codtob']."','".$result->fields['codpai']."', 			".
							"		'".$result->fields['codest']."','".$result->fields['codmun']."','".$result->fields['codpar']."',".
							"		'".$result->fields['codcom']."','".$result->fields['desobr']."','".$result->fields['dirobr']."',".
							"		'".$result->fields['obsobr']."','".$result->fields['resobr']."','".$result->fields['feciniobr']."',".
							"		'".$result->fields['fecfinobr']."','".$result->fields['cantobr']."',".$result->fields['monto'].",".
							"		'".$result->fields['feccreobr']."','".$result->fields['staobr']."','".$result->fields['fecapr']."',".
							"		'".$result->fields['estapr']."',".$result->fields['basimp'].",".
							"		".$result->fields['monimp'].")";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Obra';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Obra: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
			}
			else
			{
				$this->mensaje = 'La Obra Origen no existe';
				escribirArchivo($this->archivo,'La Obra Origen no existe ');
				escribirArchivo($this->archivo,'*******************************************************************************************************');
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar las Partidas de la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarPartidaObra($conexionbdorigen)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_partidaobra ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codobr = '{$this->codobr}' ";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['canparobr']=="")
				{
					$result->fields['canparobr']=0;
				}
				if($result->fields['canparasi']=="")
				{
					$result->fields['canparasi']=0;
				}
				if($result->fields['canpareje']=="")
				{
					$result->fields['canpareje']=0;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_partidaobra (codemp, codobr,codpar,canparobr,canparasi,canpareje) 																".
							" VALUES ('{$this->codemp}','{$this->codobr}','".$result->fields['codpar']."', 								".
							" 		".$result->fields['canparobr'].",".$result->fields['canparasi'].",					".
							"		".$result->fields['canpareje'].")";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Partidas de Obra';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Partidas de Obra: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar la Fuente de Financiamiento de la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarFuenteFinanciamientoObra($conexionbdorigen)
	{
		$this->valido = true;
		// Se seleccionan  del Origen
		$consulta = "SELECT * ".
					"  FROM sob_fuentefinanciamientoobra ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codobr = '{$this->codobr}' ";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_fuentefinanciamientoobra (codemp, codobr,codfuefin,monto) 																".
							" VALUES ('{$this->codemp}','{$this->codobr}','".$result->fields['codfuefin']."', 								".
							" 		".$result->fields['monto'].")";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Fuente de Financiamiento';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Fuente de Financiamiento: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarAsignacion($conexionbdorigen)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_asignacion ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codobr = '{$this->codobr}' ".
					"   AND estasi <> 3";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monparasi']=="")
				{
					$result->fields['monparasi']=0;
				}
				if($result->fields['basimpasi']=="")
				{
					$result->fields['basimpasi']=0;
				}
				if($result->fields['montotasi']=="")
				{
					$result->fields['montotasi']=0;
				}
				$codasi=$result->fields['codasi'];
				$codpro=$result->fields['cod_pro'];
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_asignacion (codemp, codobr,codasi,cod_pro,cod_pro_ins,puncueasi, 			".
							"			fecasi, obsasi,monparasi, basimpasi, montotasi,estasi, fecapr, estapr, 			".
							"			estspgscg,  fechaconta, fechaanula) 																".
							" VALUES ('{$this->codemp}','{$this->codobr}','".$result->fields['codasi']."', 								".
							" 		'".$result->fields['cod_pro']."','".$result->fields['cod_pro_ins']."',					".
							"		'".$result->fields['puncueasi']."','".$result->fields['fecasi']."','".$result->fields['obsasi']."', 			".
							"		".$result->fields['monparasi'].",".$result->fields['basimpasi'].",".$result->fields['montotasi'].",".
							"		'".$result->fields['estasi']."','".$result->fields['fecapr']."','".$result->fields['estapr']."',".
							"		".$result->fields['estspgscg'].",'".$result->fields['fechaconta']."',".
							"		'".$result->fields['fechaanula']."')";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Asignacion';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Asignacion: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				else
				{
					$this->copiarAsignacionPartida($conexionbdorigen,$codasi);
					if ($this->valido)
					{
						$this->copiarCuentasAsignacion($conexionbdorigen,$codasi);
					}
					if ($this->valido)
					{
						$this->copiarCargoAsignacion($conexionbdorigen,$codasi);
					}
					if ($this->valido)
					{
						$this->copiarContrato($conexionbdorigen,$codasi,$codpro);
					}
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarAsignacionPartida($conexionbdorigen,$codasi)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_asignacionpartidaobra ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codobr = '{$this->codobr}' ".
					"   AND codasi= '".$codasi."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monparasi']=="")
				{
					$result->fields['monparasi']=0;
				}
				if($result->fields['basimpasi']=="")
				{
					$result->fields['basimpasi']=0;
				}
				if($result->fields['montotasi']=="")
				{
					$result->fields['montotasi']=0;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_asignacionpartidaobra (codemp, codobr,codasi,codpar,canparobrasi,preparasi, 			".
							"			prerefparasi, canasipareje,canvarpar, estvar) 																".
							" VALUES ('{$this->codemp}','{$this->codobr}','".$result->fields['codasi']."', 								".
							" 		'".$result->fields['codpar']."',".$result->fields['canparobrasi'].",					".
							"		".$result->fields['preparasi'].",".$result->fields['prerefparasi'].",".$result->fields['canasipareje'].",".
							"		".$result->fields['canvarpar'].",'".$result->fields['estvar']."')";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Asignacion Partida';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Asignacion Partida: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}


/***********************************************************************************
 * @Función para insertar la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarCuentasAsignacion($conexionbdorigen,$codasi)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_cuentasasignacion ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codasi= '".$codasi."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				if($this->codestpro1!="")
				{
					$result->fields['codestpro1']=$this->codestpro1;
				}
				if($this->codestpro2!="")
				{
					$result->fields['codestpro2']=$this->codestpro2;
				}
				if($this->codestpro3!="")
				{
					$result->fields['codestpro3']=$this->codestpro3;
				}
				if($this->codestpro4!="")
				{
					$result->fields['codestpro4']=$this->codestpro4;
				}
				if($this->codestpro5!="")
				{
					$result->fields['codestpro5']=$this->codestpro5;
				}
				if($this->cuenta!="")
				{
					$result->fields['spg_cuenta']=$this->cuenta;
				}
				if($this->estcla!="")
				{
					$result->fields['estcla']=$this->estcla;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_cuentasasignacion (codemp, codasi,estcla,codestpro1,codestpro2, 			".
							"			codestpro3, codestpro4,codestpro5, spg_cuenta, codfuefin, codcencos, monto) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codasi']."', 								".
							" 		'".$result->fields['estcla']."','".$result->fields['codestpro1']."',					".
							"		'".$result->fields['codestpro2']."','".$result->fields['codestpro3']."','".$result->fields['codestpro4']."',".
							"		'".$result->fields['codestpro5']."','".$result->fields['spg_cuenta']."',".
							"       '".$result->fields['codfuefin']."','".$result->fields['codcencos']."',".$result->fields['monto'].")";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Cuenta de asignacion';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Cuenta de asignacion: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function actualizarAsignacion($conexionbdorigen,$codasi)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = " UPDATE sob_asignacion SET estasi='1'".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codasi= '".$codasi."'";
		$result = $this->conexionbd->Execute($consulta);
		if ($result === false)
		{	
			$this->mensaje = 'Error al Actualizar la asignacion';
			$this->valido = false;
			escribirArchivo($this->archivo,'* Error al Actualizar la asignacion: '.$this->conexionbd->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
	}

/***********************************************************************************
 * @Función para insertar la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarCargoAsignacion($conexionbdorigen,$codasi)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_cargoasignacion ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codasi= '".$codasi."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['basimp']=="")
				{
					$result->fields['basimp']=0;
				}
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				if($this->codestpro1!="")
				{
					$result->fields['codestprog']=$this->codestpro1.$this->codestpro2.$this->codestpro3.$this->codestpro4.$this->codestpro5;
				}
				if($this->cuenta!="")
				{
					$result->fields['spg_cuenta']=$this->cuenta;
				}
				if($this->estcla!="")
				{
					$result->fields['estcla']=$this->estcla;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_cargoasignacion (codemp, codasi,codcar,basimp,monto, 			".
							"			formula, codestprog,estcla, spg_cuenta, codfuefin, codcencos) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codasi']."', 								".
							" 		'".$result->fields['codcar']."',".$result->fields['basimp'].",					".
							"		".$result->fields['monto'].",'".$result->fields['formula']."','".$result->fields['codestprog']."',".
							"		'".$result->fields['estcla']."','".$result->fields['spg_cuenta']."',".
							"       '".$result->fields['codfuefin']."','".$result->fields['codcencos']."')";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Cuenta de asignacion';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Cuenta de asignacion: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar el Contrato
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarContrato($conexionbdorigen,$codasi,$codpro)
	{
		$_SESSION["valores"]["valuacion"]=0;
		$_SESSION["valores"]["anticipo"]=0;
		$_SESSION["valores"]["variacion"]=0;
		$arrevento['codemp']    = $this->codemp;
		$arrevento['codusu']    = $_SESSION['la_logusr'];
		$arrevento['codsis']    = "APR";
		$arrevento['evento']    = 'PROCESAR';
		$arrevento['nomfisico'] = "sigesp_vis_apr_traspaso_obras.html";
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_contrato ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codasi= '".$codasi."'".
					"   AND estcon <> 3";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				if($result->fields['porejefiscon']=="")
				{
					$result->fields['porejefiscon']=0;
				}
				if($result->fields['monejefincon']=="")
				{
					$result->fields['monejefincon']=0;
				}
				if($result->fields['ultactcon']=="")
				{
					$result->fields['ultactcon']=0;
				}
				$codcon=$result->fields['codcon'];
				$estcon=$result->fields['estcon'];
				$feccon=$result->fields['feccon'];
				$obscon=$result->fields['obscon'];
				$monto=$result->fields['monto'];
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_contrato (codemp, codasi,codcon,monto,feccon,".
							"			fecinicon, placon,mulcon, tiemulcon, mulreuni, lapgarcon, lapgaruni, codtco, monmaxcon,pormaxcon,".
							"           estcon, obscon, porejefiscon, monejefincon, fecfincon, ultactcon, placonuni, fecapr, estapr, precon,".
							"           fecinireacon, fecfinreacon, monreacon, estspgscg, fechaconta, fechaanula, codalt) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codasi']."', 								".
							" 		'".$result->fields['codcon']."',".$result->fields['monto'].",					".
							"		'".$result->fields['feccon']."','".$result->fields['fecinicon']."',".$result->fields['placon'].",".
							"		".$result->fields['mulcon'].",".$result->fields['tiemulcon'].",".
							"       '".$result->fields['mulreuni']."',".$result->fields['lapgarcon'].",'".$result->fields['lapgaruni']."',".
							"       '".$result->fields['codtco']."',".$result->fields['monmaxcon'].",".$result->fields['pormaxcon'].",".
							"       ".$result->fields['estcon'].",'".$result->fields['obscon']."',".$result->fields['porejefiscon'].",".
							"       ".$result->fields['monejefincon'].",'".$result->fields['fecfincon']."',".
							"       ".$result->fields['ultactcon'].",'".$result->fields['placonuni']."','".$result->fields['fecapr']."',".
							"       ".$result->fields['estapr'].",'".$result->fields['precon']."','".$result->fields['fecinireacon']."',".
							"       '".$result->fields['fecfinreacon']."',".$result->fields['monreacon'].",".$result->fields['estspgscg'].",".
							"       '".$result->fields['fechaconta']."','".$result->fields['fechaanula']."','".$result->fields['codalt']."')";
				if($result->fields['estcon']==1)
				{
					//actualizarAsignacion($conexionbdorigen,$codasi)
				}
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar el Contrato';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar el Contrato: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				else
				{
					$this->copiarRetencionContrato($conexionbdorigen,$codcon);
					if ($this->valido)
					{
						$this->copiarAnticipo($conexionbdorigen,$codcon);
					}
					if ($this->valido)
					{
						$this->copiarVariacionContrato($conexionbdorigen,$codcon);
					}
					if ($this->valido)
					{
						$this->copiarValuacion($conexionbdorigen,$codcon);
					}
					if ($this->valido)
					{
						$this->copiarActa($conexionbdorigen,$codcon);
					}
					if($estcon>=5)
					{
						$saldo=($monto-$_SESSION["valores"]["anticipo"]-$_SESSION["valores"]["valuacion"]+$_SESSION["valores"]["variacion"]);
						$anio=date("Y");
						//$fecha=$anio."-01-01";
						$fecha=$this->fecha;
						//$fecha;
						$arrcabecera['codemp'] = $this->codemp;
						$arrcabecera['procede'] = 'SOBCON';
						$arrcabecera['comprobante'] = fillComprobante($codcon);
						$arrcabecera['codban'] = '---';
						$arrcabecera['ctaban'] = '-------------------------';
						$arrcabecera['fecha'] = $fecha;
						$arrcabecera['descripcion'] = $obscon;
						$arrcabecera['tipo_comp'] = 1;
						$arrcabecera['tipo_destino'] = "P";
						$arrcabecera['cod_pro'] = $codpro;
						$arrcabecera['ced_bene'] = "----------";
						//print $monto." - ".$_SESSION["valores"]["anticipo"]." - ".$_SESSION["valores"]["valuacion"]." + ".$_SESSION["valores"]["variacion"];
						$arrcabecera['total'] = $saldo;
						$arrcabecera['numpolcon'] = 0;
						$arrcabecera['esttrfcmp'] = 0;
						$arrcabecera['estrenfon'] = 0;
						$arrcabecera['codfuefin'] = "--";
						$arrcabecera['codusu'] = $_SESSION['la_logusr'];
						$arrdetallespg=$this->buscarDetallePresupuestario($conexionbdorigen,$codasi,$codcon,$arrcabecera);
						if ($this->valido)
						{
							$serviciocomprobante = new ServicioComprobante();
							$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,null,null,$arrevento);
							$this->mensaje .= $serviciocomprobante->mensaje;
							if(!$this->valido)
							{
								escribirArchivo($this->archivo,'* Error al Insertar el Comprobante: '.$this->mensaje);
								escribirArchivo($this->archivo,'*******************************************************************************************************');
							}
							unset($serviciocomprobante);
						}
					}
				
				}
				$result->MoveNext();
			}
		}	
	}
	public function buscarDetallePresupuestario($conexionbdorigen,$codasi,$codcon,$arrcabecera)
	{
		$arregloSPG = null;
		
		$consulta="SELECT sob_cuentasasignacion.codestpro1, sob_cuentasasignacion.codestpro2, sob_cuentasasignacion.codestpro3, ".
				  "       sob_cuentasasignacion.codestpro4, sob_cuentasasignacion.codestpro5, sob_cuentasasignacion.estcla,".
				  "       sob_cuentasasignacion.codfuefin, sob_cuentasasignacion.spg_cuenta,SUM(sob_cuentasasignacion.monto) AS monto  ".
                  "  FROM sob_cuentasasignacion ".
				  " WHERE sob_cuentasasignacion.codemp='".$this->codemp."'".
				  "   AND sob_cuentasasignacion.codasi='".$codasi."'".		
				  " GROUP BY sob_cuentasasignacion.codestpro1, sob_cuentasasignacion.codestpro2, sob_cuentasasignacion.codestpro3, sob_cuentasasignacion.codestpro4, sob_cuentasasignacion.codestpro5, sob_cuentasasignacion.estcla, sob_cuentasasignacion.codfuefin, sob_cuentasasignacion.spg_cuenta".
				  " ORDER BY sob_cuentasasignacion.codestpro1, sob_cuentasasignacion.codestpro2, sob_cuentasasignacion.codestpro3, sob_cuentasasignacion.codestpro4, sob_cuentasasignacion.codestpro5, sob_cuentasasignacion.estcla, sob_cuentasasignacion.codfuefin, sob_cuentasasignacion.spg_cuenta";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while (!$result->EOF)
			{
				$i++;
				$codestpro1=$result->fields['codestpro1'];
				$codestpro2=$result->fields['codestpro2'];
				$codestpro3=$result->fields['codestpro3'];
				$codestpro4=$result->fields['codestpro4'];
				$codestpro5=$result->fields['codestpro5'];
				$estcla=$result->fields['estcla'];
				$spg_cuenta=$result->fields['spg_cuenta'];
				$monto=$result->fields['monto'];
				$anticipo=$this->montoCuentaAnticipo($conexionbdorigen,$codcon,$codestpro1,$codestpro2,$codestpro3,$codestpro4,
										             $codestpro5,$estcla,$spg_cuenta);
				$valuacion=$this->montoCuentaValuacion($conexionbdorigen,$codcon,$codestpro1,$codestpro2,$codestpro3,$codestpro4,
										               $codestpro5,$estcla,$spg_cuenta);
				$variacion=$this->montoCuentaVariacion($conexionbdorigen,$codcon,$codestpro1,$codestpro2,$codestpro3,$codestpro4,
										               $codestpro5,$estcla,$spg_cuenta);
				$montoTotal=($monto-$anticipo-$valuacion+$variacion);
				if($this->codestpro1!="")
				{
					$result->fields['codestpro1']=$this->codestpro1;
				}
				if($this->codestpro2!="")
				{
					$result->fields['codestpro2']=$this->codestpro2;
				}
				if($this->codestpro3!="")
				{
					$result->fields['codestpro3']=$this->codestpro3;
				}
				if($this->codestpro4!="")
				{
					$result->fields['codestpro4']=$this->codestpro4;
				}
				if($this->codestpro5!="")
				{
					$result->fields['codestpro5']=$this->codestpro5;
				}
				if($this->estcla!="")
				{
					$result->fields['estcla']=$this->estcla;
				}
				
				if($result->fields['spg_cuenta']!="403180100")
				{
					if($this->cuenta!="")
					{
						$result->fields['spg_cuenta']=$this->cuenta;
					}
				}
				$arregloSPG[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSPG[$i]['procede']= $arrcabecera['procede'];
				$arregloSPG[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSPG[$i]['codban']= $arrcabecera['codban'];
				$arregloSPG[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSPG[$i]['procede_doc']= $arrcabecera['procede'];
				$arregloSPG[$i]['documento']= $arrcabecera['comprobante'];
				$arregloSPG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSPG[$i]['descripcion']= $arrcabecera['descripcion'];
				$arregloSPG[$i]['orden']= $i;
				$arregloSPG[$i]['codestpro1']=$result->fields['codestpro1'];
				$arregloSPG[$i]['codestpro2']=$result->fields['codestpro2'];
				$arregloSPG[$i]['codestpro3']=$result->fields['codestpro3'];
				$arregloSPG[$i]['codestpro4']=$result->fields['codestpro4'];
				$arregloSPG[$i]['codestpro5']=$result->fields['codestpro5'];
				$arregloSPG[$i]['estcla']=$result->fields['estcla'];
				$arregloSPG[$i]['codfuefin']=$result->fields['codfuefin'];
				$arregloSPG[$i]['spg_cuenta']=$result->fields['spg_cuenta'];
				$arregloSPG[$i]['monto']=$montoTotal;
				$arregloSPG[$i]['mensaje']= "";
				$arregloSPG[$i]['operacion']= "CS";
				$result->MoveNext();
			}			
		}
		unset($result);
		return $arregloSPG;
	}
	
/***********************************************************************************
 * @Función para buscar el monto por cuenta del anticipo
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function montoCuentaAnticipo($conexionbdorigen,$codcon,$codestpro1,$codestpro2,$codestpro3,$codestpro4,
										$codestpro5,$estcla,$spg_cuenta)
	{
		$this->valido = true;
		$anticipo=0;
		// Se seleccionan del Origen
		$consulta = "SELECT SUM(monto) AS monto ".
					"  FROM sob_cuentaanticipo ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codestpro1= '".$codestpro1."'".
					"   AND codestpro2= '".$codestpro2."'".
					"   AND codestpro3= '".$codestpro3."'".
					"   AND codestpro4= '".$codestpro4."'".
					"   AND codestpro5= '".$codestpro5."'".
					"   AND estcla= '".$estcla."'".
					"   AND spg_cuenta= '".$spg_cuenta."'".		
				    " GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			if(!$result->EOF)
			{
				$anticipo=$result->fields['monto'];
			}
		}	
		return $anticipo;
	}

	
/***********************************************************************************
 * @Función para buscar el monto por cuenta de la valuacion
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function montoCuentaValuacion($conexionbdorigen,$codcon,$codestpro1,$codestpro2,$codestpro3,$codestpro4,
										$codestpro5,$estcla,$spg_cuenta)
	{
		$this->valido = true;
		$valuacion=0;
		// Se seleccionan del Origen
		$consulta = "SELECT SUM(monto) AS monto ".
					"  FROM sob_cuentaanticipo ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codestpro1= '".$codestpro1."'".
					"   AND codestpro2= '".$codestpro2."'".
					"   AND codestpro3= '".$codestpro3."'".
					"   AND codestpro4= '".$codestpro4."'".
					"   AND codestpro5= '".$codestpro5."'".
					"   AND estcla= '".$estcla."'".
					"   AND spg_cuenta= '".$spg_cuenta."'".		
				    " GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			if(!$result->EOF)
			{
				$valuacion=$result->fields['monto'];
			}
		}	
		return $valuacion;
	}

/***********************************************************************************
 * @Función para buscar el monto por cuenta de la valuacion
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function montoCuentaVariacion($conexionbdorigen,$codcon,$codestpro1,$codestpro2,$codestpro3,$codestpro4,
										$codestpro5,$estcla,$spg_cuenta)
	{
		$this->valido = true;
		$variacion=0;
		// Se seleccionan del Origen
		$consulta = "SELECT SUM(monto) AS monto ".
					"  FROM sob_cuentavariacion ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codestpro1= '".$codestpro1."'".
					"   AND codestpro2= '".$codestpro2."'".
					"   AND codestpro3= '".$codestpro3."'".
					"   AND codestpro4= '".$codestpro4."'".
					"   AND codestpro5= '".$codestpro5."'".
					"   AND estcla= '".$estcla."'".
					"   AND spg_cuenta= '".$spg_cuenta."'".		
				    " GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			if(!$result->EOF)
			{
				$variacion=$result->fields['monto'];
			}
		}	
		return $variacion;
	}


/***********************************************************************************
 * @Función para insertar la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarRetencionContrato($conexionbdorigen,$codcon)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_retencioncontrato ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_retencioncontrato (codemp, codcon,codded) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."', 								".
							" 		'".$result->fields['codded']."')";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Retencion de Contrato';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Retencion de Contrato: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}


/***********************************************************************************
 * @Función para insertar el Anticipo
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarAnticipo($conexionbdorigen,$codcon)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_anticipo ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				$codant=$result->fields['codant'];
				$lb_existe=$this->validarCuentaAnticipo($conexionbdorigen,$codcon,$codant);
				if($lb_existe)
				{
					$_SESSION["valores"]["anticipo"]=$result->fields['monto'];
				}
				else
				{
					$_SESSION["valores"]["anticipo"]=0;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_anticipo (codemp, codcon,codant,fecant,fecintant,porant,monto,conant,montotant,sc_cuenta,".
							"                           estant,fecapr,estapr,estspgscg,estgenrd,fechaconta,fechaanula,numrecdoc,numref,".
							"                           fecfac,montoiva,montoret)".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."','".$result->fields['codant']."',".
							" 		'".$result->fields['fecant']."','".$result->fields['fecintant']."',".$result->fields['porant'].",".
							"       ".$result->fields['monto'].",'".$result->fields['conant']."',".$result->fields['montotant'].",".
							"       '".$result->fields['sc_cuenta']."',".$result->fields['estant'].",'".$result->fields['fecapr']."',".
							"       ".$result->fields['estapr'].",".$result->fields['estspgscg'].",'".$result->fields['estgenrd']."',".
							"       '".$result->fields['fechaconta']."','".$result->fields['fechaanula']."','".$result->fields['numrecdoc']."',".
							"       '".$result->fields['numref']."','".$result->fields['fecfac']."',".$result->fields['montoiva'].",".$result->fields['montoret']." )";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar el Anticipo';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar el Anticipo: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				else
				{
					$this->copiarCuentaAnticipo($conexionbdorigen,$codcon,$codant);
					if ($this->valido)
					{
						$this->copiarCargoAnticipo($conexionbdorigen,$codcon,$codant);
					}
					if ($this->valido)
					{
						$this->copiarRetencionAnticipo($conexionbdorigen,$codcon,$codant);
					}
				
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar el Anticipo
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarCuentaAnticipo($conexionbdorigen,$codcon,$codant)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_cuentaanticipo ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codant= '".$codant."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				if($this->codestpro1!="")
				{
					$result->fields['codestpro1']=$this->codestpro1;
				}
				if($this->codestpro2!="")
				{
					$result->fields['codestpro2']=$this->codestpro2;
				}
				if($this->codestpro3!="")
				{
					$result->fields['codestpro3']=$this->codestpro3;
				}
				if($this->codestpro4!="")
				{
					$result->fields['codestpro4']=$this->codestpro4;
				}
				if($this->codestpro5!="")
				{
					$result->fields['codestpro5']=$this->codestpro5;
				}
				if($this->cuenta!="")
				{
					$result->fields['spg_cuenta']=$this->cuenta;
				}
				if($this->estcla!="")
				{
					$result->fields['estcla']=$this->estcla;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_cuentaanticipo (codemp, codcon,codant,codestpro1,codestpro2,codestpro3,codestpro4,".
							"                                 codestpro5,estcla,spg_cuenta,monto) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."','".$result->fields['codant']."', 								".
							" 		'".$result->fields['codestpro1']."','".$result->fields['codestpro2']."','".$result->fields['codestpro3']."',".
							"       '".$result->fields['codestpro4']."','".$result->fields['codestpro5']."','".$result->fields['estcla']."',".
							"       '".$result->fields['spg_cuenta']."',".$result->fields['monto'].")";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Cuenta de Anticipo';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Cuenta de Anticipo: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}
/***********************************************************************************
 * @Función para insertar el Anticipo
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function validarCuentaAnticipo($conexionbdorigen,$codcon,$codant)
	{
		$this->valido = true;
		$lb_existe=false;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_cuentaanticipo ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codant= '".$codant."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			if(!$result->EOF)
			{
				$lb_existe=true;
			}
		}	
		return $lb_existe;
	}

/***********************************************************************************
 * @Función para insertar el Anticipo
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarCargoAnticipo($conexionbdorigen,$codcon,$codant)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_cargoanticipo ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codant= '".$codant."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['basimp']=="")
				{
					$result->fields['basimp']=0;
				}
				if($this->codestpro1!="")
				{
					$result->fields['codestprog']=$this->codestpro1.$this->codestpro2.$this->codestpro3.$this->codestpro4.$this->codestpro5;
				}
				if($this->cuenta!="")
				{
					$result->fields['spg_cuenta']=$this->cuenta;
				}
				if($this->estcla!="")
				{
					$result->fields['estcla']=$this->estcla;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_cargoanticipo (codemp, codcon,codant,codcar,basimp,monto,formula,codestprog,estcla,spg_cuenta,".
							"                           codfuefin) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."','".$result->fields['codant']."', 								".
							" 		'".$result->fields['codcar']."',".$result->fields['basimp'].",".$result->fields['monto'].",".
							"       '".$result->fields['formula']."','".$result->fields['codestprog']."','".$result->fields['estcla']."',".
							"       '".$result->fields['spg_cuenta']."','".$result->fields['codfuefin']."')";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar el Cargo de Anticipo';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar el Cargo de Anticipo: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar el Anticipo
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarRetencionAnticipo($conexionbdorigen,$codcon,$codant)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_retencionanticipo ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codant= '".$codant."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['montotret']=="")
				{
					$result->fields['montotret']=0;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_retencionanticipo (codemp, codcon,codant,codded,monret,montotret) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."','".$result->fields['codant']."', 								".
							" 		'".$result->fields['codded']."',".$result->fields['monret'].",".$result->fields['montotret'].")";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Retencion de Anticipo';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Retencion de Anticipo: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}


/***********************************************************************************
 * @Función para insertar la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarVariacionContrato($conexionbdorigen,$codcon)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_variacioncontrato ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				// Se inserta en la Base de Datos Destino
				$codvar=$result->fields['codvar'];
				if($result->fields['tipvar']==0)
				{
					$_SESSION["valores"]["variacion"]=($result->fields['monto']* (-1));
				}
				else
				{
					$_SESSION["valores"]["variacion"]=$result->fields['monto'];
				}

				$consulta = " INSERT INTO sob_variacioncontrato (codemp, codcon,codvar,tipvar,motvar,fecvar,monto,estvar,estspgscg,".
							"                                    estapr,fecapr,fechaconta,fechaanula) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."','".$result->fields['codvar']."', 								".
							" 		".$result->fields['tipvar'].",'".$result->fields['motvar']."','".$result->fields['fecvar']."',".
							"       ".$result->fields['monto'].",".$result->fields['estvar'].",".$result->fields['estspgscg'].",".
							"       '".$result->fields['estapr']."','".$result->fields['fecapr']."',".
							"       '".$result->fields['fechaconta']."','".$result->fields['fechaanula']."')";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Retencion de Contrato';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Retencion de Contrato: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				else
				{
					$this->copiarVariacionPartida($conexionbdorigen,$codcon,$codvar);
					if ($this->valido)
					{
						$this->copiarCuentaVariacion($conexionbdorigen,$codcon,$codvar);
					}
				
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarVariacionPartida($conexionbdorigen,$codcon,$codvar)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_variacionpartida ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codvar= '".$codvar."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['precio_nuevo']=="")
				{
					$result->fields['precio_nuevo']=0;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_variacionpartida (codemp, codcon,codvar,codasi,codpar,codobr,cantidad_anterior,cantidad_nueva,".
							"                                    precio_anterior,precio_nuevo) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."','".$result->fields['codvar']."', 								".
							" 		'".$result->fields['codasi']."','".$result->fields['codpar']."','".$result->fields['codobr']."',".
							"       ".$result->fields['cantidad_anterior'].",".$result->fields['cantidad_nueva'].",".$result->fields['precio_anterior'].",".
							"       ".$result->fields['precio_nuevo'].")";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Variacion de Partida';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Variacion de Partida: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar el Anticipo
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarCuentaVariacion($conexionbdorigen,$codcon,$codvar)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_cuentavariacion ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codvar= '".$codvar."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				if($this->codestpro1!="")
				{
					$result->fields['codestpro1']=$this->codestpro1;
				}
				if($this->codestpro2!="")
				{
					$result->fields['codestpro2']=$this->codestpro2;
				}
				if($this->codestpro3!="")
				{
					$result->fields['codestpro3']=$this->codestpro3;
				}
				if($this->codestpro4!="")
				{
					$result->fields['codestpro4']=$this->codestpro4;
				}
				if($this->codestpro5!="")
				{
					$result->fields['codestpro5']=$this->codestpro5;
				}
				if($this->cuenta!="")
				{
					$result->fields['spg_cuenta']=$this->cuenta;
				}
				if($this->estcla!="")
				{
					$result->fields['estcla']=$this->estcla;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_cuentavariacion (codemp, codcon,codvar,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,spg_cuenta,".
							"                           codfuefin,monto) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."','".$result->fields['codvar']."', 								".
							" 		'".$result->fields['codestpro1']."','".$result->fields['codestpro2']."','".$result->fields['codestpro3']."',".
							"       '".$result->fields['codestpro4']."','".$result->fields['codestpro5']."','".$result->fields['estcla']."',".
							"       '".$result->fields['spg_cuenta']."','".$result->fields['codfuefin']."',".$result->fields['monto'].")";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Cuenta de Variacion';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Cuenta de Variacion: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar el Anticipo
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarValuacion($conexionbdorigen,$codcon)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_valuacion ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				$codval=$result->fields['codval'];
				$_SESSION["valores"]["valuacion"]=$result->fields['monto'];
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_valuacion (codemp, codcon,codval,fecinival,fecfinval,obsval,amoval,obsamoval,amoantval, amototval, amoresval,".
							"                           subtotpar,basimpval,subtot,totreten,montotval,estval,fecha,fecapr,estapr,estspgscg,".
							"                           estgenrd,fechaconta,fechaanula,numrecdoc,numref,poramo,fecrecdoc) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."','".$result->fields['codval']."',".
							" 		'".$result->fields['fecinival']."','".$result->fields['fecfinval']."','".$result->fields['obsval']."',".
							"       ".$result->fields['amoval'].",'".$result->fields['obsamoval']."',".$result->fields['amoantval'].",".
							"       ".$result->fields['amototval'].",".$result->fields['amoresval'].",".$result->fields['subtotpar'].",".
							"       ".$result->fields['basimpval'].",".$result->fields['subtot'].",".$result->fields['totreten'].",".
							"       ".$result->fields['montotval'].",".$result->fields['estval'].",'".$result->fields['fecha']."',".
							"       '".$result->fields['fecapr']."',".$result->fields['estapr'].",".$result->fields['estspgscg'].",".
							"       '".$result->fields['estgenrd']."','".$result->fields['fechaconta']."','".$result->fields['fechaanula']."',".
							"       '".$result->fields['numrecdoc']."','".$result->fields['numref']."',".$result->fields['poramo'].",'".$result->fields['fecrecdoc']."')";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Valuacion';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Valuacion: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				else
				{
					$this->copiarValuacionPartida($conexionbdorigen,$codcon,$codval);
					if ($this->valido)
					{
						$this->copiarCuentaValuacion($conexionbdorigen,$codcon,$codval);
					}
					if ($this->valido)
					{
						$this->copiarCargoValuacion($conexionbdorigen,$codcon,$codval);
					}
				
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar el Anticipo
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarValuacionPartida($conexionbdorigen,$codcon,$codval)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_valuacionpartida ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codval= '".$codval."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['canvalpar']=="")
				{
					$result->fields['canvalpar']=0;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_valuacionpartida (codemp, codcon,codval,codasi,codpar,codobr,canvalpar,prerefparasi,".
							"                                    preparval) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."','".$result->fields['codval']."', 								".
							" 		'".$result->fields['codasi']."','".$result->fields['codpar']."','".$result->fields['codobr']."',".
							"       ".$result->fields['canvalpar'].",".$result->fields['prerefparasi'].",".$result->fields['preparval'].")";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Variacion de Partida';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Variacion de Partida: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar el Anticipo
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarCuentaValuacion($conexionbdorigen,$codcon,$codval)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_cuentavaluacion ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codval= '".$codval."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				if($this->codestpro1!="")
				{
					$result->fields['codestpro1']=$this->codestpro1;
				}
				if($this->codestpro2!="")
				{
					$result->fields['codestpro2']=$this->codestpro2;
				}
				if($this->codestpro3!="")
				{
					$result->fields['codestpro3']=$this->codestpro3;
				}
				if($this->codestpro4!="")
				{
					$result->fields['codestpro4']=$this->codestpro4;
				}
				if($this->codestpro5!="")
				{
					$result->fields['codestpro5']=$this->codestpro5;
				}
				if($this->cuenta!="")
				{
					$result->fields['spg_cuenta']=$this->cuenta;
				}
				if($this->estcla!="")
				{
					$result->fields['estcla']=$this->estcla;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_cuentavaluacion (codemp, codcon,codval,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,spg_cuenta,".
							"                           monto) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."','".$result->fields['codval']."', 								".
							" 		'".$result->fields['codestpro1']."','".$result->fields['codestpro2']."','".$result->fields['codestpro3']."',".
							"       '".$result->fields['codestpro4']."','".$result->fields['codestpro5']."','".$result->fields['estcla']."',".
							"       '".$result->fields['spg_cuenta']."',".$result->fields['monto'].")";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Cuenta de Variacion';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Cuenta de Variacion: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarCargoValuacion($conexionbdorigen,$codcon,$codval)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_cargovaluacion ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'".
					"   AND codval= '".$codval."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['basimp']=="")
				{
					$result->fields['basimp']=0;
				}
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				if($this->codestpro1!="")
				{
					$result->fields['codestprog']=$this->codestpro1.$this->codestpro2.$this->codestpro3.$this->codestpro4.$this->codestpro5;
				}
				if($this->cuenta!="")
				{
					$result->fields['spg_cuenta']=$this->cuenta;
				}
				if($this->estcla!="")
				{
					$result->fields['estcla']=$this->estcla;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_cargovaluacion (codemp, codval,codcon,codcar,basimp,monto, 			".
							"			formula, codestprog,estcla, spg_cuenta, codfuefin, codcencos) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codval']."','".$result->fields['codcon']."', 								".
							" 		'".$result->fields['codcar']."',".$result->fields['basimp'].",					".
							"		".$result->fields['monto'].",'".$result->fields['formula']."','".$result->fields['codestprog']."',".
							"		'".$result->fields['estcla']."','".$result->fields['spg_cuenta']."',".
							"       '".$result->fields['codfuefin']."','".$result->fields['codcencos']."')";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Cuenta de asignacion';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Cuenta de asignacion: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
		}	
	}

/***********************************************************************************
 * @Función para insertar la Obra
 * @parametros:
 * @retorno:
 * @fecha de creación: 14/12/2015
 * @autor: Ing. Luis Anibal Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function copiarActa($conexionbdorigen,$codcon)
	{
		$this->valido = true;
		// Se seleccionan del Origen
		$consulta = "SELECT * ".
					"  FROM sob_acta ".
					" WHERE codemp = '{$this->codemp}' ".
					"   AND codcon= '".$codcon."'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$this->mensaje = '* Error: '.$conexionbdorigen->ErrorMsg();
			escribirArchivo($this->archivo,'* Error: '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		else
		{
			while(!$result->EOF)
			{
				if($result->fields['basimp']=="")
				{
					$result->fields['basimp']=0;
				}
				if($result->fields['monto']=="")
				{
					$result->fields['monto']=0;
				}
				// Se inserta en la Base de Datos Destino
				$consulta = " INSERT INTO sob_acta (codemp, codcon,codact,tipact,fecact, 			".
							"			feciniact, fecfinact,fecrecact, persusact, coduni, cedresact,cedsupact,cedinsact,motact,".
							"           repempact,fecregvalact,obsact,estact,civinsact,civresact,nomresact) 																".
							" VALUES ('{$this->codemp}','".$result->fields['codcon']."', 								".
							" 		'".$result->fields['codact']."',".$result->fields['tipact'].",					".
							"		'".$result->fields['fecact']."','".$result->fields['feciniact']."','".$result->fields['fecfinact']."',".
							"		'".$result->fields['fecrecact']."',".$result->fields['persusact'].",".
							"       '".$result->fields['coduni']."','".$result->fields['cedresact']."',".
							"       '".$result->fields['cedsupact']."','".$result->fields['cedinsact']."',".
							"       '".$result->fields['motact']."','".$result->fields['repempact']."',".
							"       '".$result->fields['fecregvalact']."','".$result->fields['obsact']."',".
							"       ".$result->fields['estact'].",'".$result->fields['civinsact']."',".
							"       '".$result->fields['civresact']."','".$result->fields['nomresact']."')";
				$resultinsert = $this->conexionbd->Execute($consulta);
				if ($resultinsert === false)
				{	
					$this->mensaje = 'Error al Insertar la Cuenta de asignacion';
					$this->valido = false;
					escribirArchivo($this->archivo,'* Error al Insertar la Cuenta de asignacion: '.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
				}
				$result->MoveNext();
			}
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
	public function incluirSeguridad($evento,$tipotransaccion)
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