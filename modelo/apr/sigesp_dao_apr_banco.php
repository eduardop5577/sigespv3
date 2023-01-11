<?php
/***********************************************************************************
* @fecha de modificacion: 01/08/2022, para la version de php 8.1 
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

class  TraspasoSaldos extends DaoGenerico
{
	var $_table = 'scb_movbco';
	public $mensaje;
	public $valido = true;
	public $existe;
	public $criterio;
	public $servidor;
	public $usuario;
	public $clave;
	public $basedatos;
	public $gestor;
	public $puerto;
	public $tipoconexionbd = 'DEFECTO';
	public $archivo;
	public $resulttransito;
	public $resultcol;

	public function __construct() {
		parent::__construct ( 'scb_movbco' );
		$this->conexionbd = $this->obtenerConexionBd(); 
		$this->objlibcon = new ConexionBaseDatos();
	}
	
/***********************************************************************************
* @Función para procesar el traspaso de los saldos y movimientos en tránsito.   
* @parametros: 
* @retorno:
* @fecha de creación: 05/12/2008.
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function procesarSaldos() 
	{
		
		$this->conexionbdorigen =$this->objlibcon->conectarBD($_SESSION['sigesp_servidor_apr'], $_SESSION['sigesp_usuario_apr'], $_SESSION['sigesp_clave_apr'],
									         $_SESSION['sigesp_basedatos_apr'], $_SESSION['sigesp_gestor_apr'], $_SESSION['sigesp_puerto_apr']);
		
		
		$this->mensaje='Realizó el traspaso de saldos y Movimientos en transito';		
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		escribirArchivo($this->archivo,'                            TRASPASO DE SALDOS Y MOVIMIENTOS EN TRANSITO');
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		$this->conexionbd->StartTrans();
		try 
		{ 												 
			// se obtienen las colocaciones para ese banco y cuenta
			$consulta = "SELECT numcol,codtipcol,codban,ctaban, 0.0000  AS saldo ".
			   			"  FROM scb_colocacion ".
			   			" WHERE codemp='{$this->codemp}' ".
						"   AND codban='{$this->codban}' ".							
						"   AND ctaban='{$this->ctaban}' ".
			  			" GROUP BY codban,ctaban,numcol,codtipcol ";			
			$this->resultcol = $this->conexionbdorigen->Execute($consulta);
			if ($this->resultcol===false)
			{
				$this->mensaje='Error obtener las colocaciones.';		
				escribirArchivo($this->archivo,'* Error obtener las colocaciones.'.$this->conexionbdorigen->ErrorMsg());
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				$this->valido = false; 
			}
			if ($this->valido)
			{				
				//calcular los saldos de los documentos
				$saldodocumentosaux = 0;
				$saldodocumentosaux = $this->calcularSaldoDocumento();				
				while ((!$this->resultcol->EOF) && ($this->valido))
				{
					$this->banco  = $this->resultcol->fields['codban'];
					$this->numcol = $this->resultcol->fields['numcol'];
					$this->cuenta = $this->resultcol->fields['ctaban'];					
					$this->saldo  = $this->calcularSaldoColocacion();
					$this->resultcol->MoveNext();	
				}			
				//chequear los movimientos en tránsito
				if (($this->movtransito===true) && ($this->valido))
				{
					// Movimientos en transito en forma resumida								
					$consulta = "SELECT codban,ctaban,codope,SUM(monto-monret) AS total, estmov ". 
							  	"  FROM scb_movbco ".
							  	" WHERE codemp='$this->codemp'". 
								"   AND codban='$this->codban' ".
							    "   AND ctaban='$this->ctaban' ".
								"   AND estcon=0 ".
								"   AND (estmov='C' OR estmov='L')".
							    " GROUP by codban,ctaban,codope,estmov". 
							    " ORDER BY codban,ctaban,codope,estmov";
					$result = $this->conexionbdorigen->Execute($consulta);
					if ($result===false)
					{
						$this->mensaje='Error obtener las colocaciones.';		
						escribirArchivo($this->archivo,'*Error Obtención de los movimientos en tránsito resumidos.'.''.$this->conexionbdorigen->ErrorMsg());
						escribirArchivo($this->archivo,'*******************************************************************************************************');
						$this->valido = false; 
					}	
					else
					{
						$debitosaux  = 0;
						$creditosaux = 0;
						$debitosnegativosaux  = 0;
						$creditosnegativosaux = 0;
						while (!$result->EOF)
						{
							$this->operacion = $result->fields['codope'];
							$this->estado    = $result->fields['estmov'];
							$this->monto     = $result->fields['total'];
							if((($this->operacion=='CH') || ($this->operacion=='ND') || ($this->operacion=='RE')) && ($this->estado!='A'))
							{	
								$creditosaux += $this->monto;
							}	
							elseif((($this->operacion=='CH') || ($this->operacion=='ND') || ($this->operacion=='RE')) && ($this->estado=='A'))
							{	
								$creditosnegativosaux += $this->monto;
							}	
							elseif((($this->operacion=='DP') || ($this->operacion=='NC')) && ($this->estado!='A'))
							{	
								$debitosaux += $this->monto;
							}
							elseif((($this->operacion=='DP') || ($this->operacion=='NC')) && ($this->estado=='A'))
							{
								$debitosnegativosaux += $this->monto;
							}
							$result->MoveNext();
						}
						$debitos  = $debitosaux  - $debitosnegativosaux;
						$creditos = $creditosaux - $creditosnegativosaux;
						$saldoaux = $saldodocumentosaux;		
						$this->saldo = number_format($saldoaux + $creditos - $debitos,2,'.','');						
					}  		  
				}			
				//traspaso de los saldos de banco
				if (($this->verificarCuenta()) && ($this->valido))
				{
					$saldo = $this->saldo; 
					if ($saldo>=0)
					{
						$this->operacion = 'NC';
					}
					else
					{
						$this->operacion = 'ND';
					}					
					$this->saldo = abs($this->saldo);
					$this->numdoc = '0000000APERTURA';
					if (!$this->verificarApertura())
					{
						escribirArchivo($this->archivo,'*		MOVIMIENTO DE APERTURA');
						$this->fecmov = $this->fecfin;
						$this->codope = $this->operacion;						
						$this->conmov = 'SALDO INICIAL DE LA CUENTA';
						$this->nomproben = 'APERTURA';
						$this->codconmov = '---';
						$this->tipo_destino = '-';
						$this->estmov    = 'L';
						$this->estcondoc = 'S';
						$this->monto     = $this->saldo;
						$this->estbpd    = 'M';
						$this->estcon    = 0;
						$this->estcobing = 0;
						$this->esttra    = 0;
						$this->estimpche = 1;
						$this->monret    = 0.0000;
						$this->cod_pro   = '----------';
						$this->ced_bene  = '----------';
						$this->feccon    = '1900-01-01';
						$this->monobjret = $this->saldo;
						$this->codbansig = '---';
						$this->codfuefin = '--';
						$this->codcencos = '---';
						escribirArchivo($this->archivo,'* '.$this->codban.'//'.$this->ctaban.'//'.$this->numdoc.'//'.$this->codope.'//'.$this->estmov.'//'.$this->saldo);
						$exito = $this->Save();
						if ($exito===false)
						{
							$this->mensaje='Error al Insertar movimiento de Apertura.';		
							escribirArchivo($this->archivo,'* Error al Insertar movimiento de Apertura.'.$this->conexionbd->ErrorMsg());
							escribirArchivo($this->archivo,'*******************************************************************************************************');
							$this->valido = false; 
						}
						else
						{
							$consulta="UPDATE scb_movbco SET numconint=null WHERE numconint=''";
							$result2 = $this->conexionbd->Execute($consulta);
						}
					}
				}
				else
				{
					$this->mensaje = 'La cuenta No. '. $this->ctaban.' no existe en la Base de Datos';
					escribirArchivo($this->archivo,'* La cuenta No. '. $this->ctaban.' no existe en la Base de Datos.'.$this->conexionbd->ErrorMsg());
					escribirArchivo($this->archivo,'*******************************************************************************************************');
					$this->valido = false;
				}
				//insertar los movimientos en tránsito				
				$indicesrepetidos = array();
				$indiceresult = 0;
				if ($this->movtransito && $this->valido)
				{ 
					$consulta = "SELECT * ".
								"  FROM scb_movbco ".
								" WHERE codemp='$this->codemp' ".
								"	AND codban='$this->codban' ".
								"	AND ctaban='$this->ctaban' ".
								"	AND estcon=0 ".
								"	AND (estmov='C' OR estmov='L')".
								" ORDER BY codban,ctaban,numdoc";						  				  						  				  
					$this->resulttransito = $this->conexionbdorigen->Execute($consulta);
					$pos = 0;
					$numdoctransito = array();
					if ($this->resulttransito===false)
					{
						$this->mensaje='Error obtener los movimientos en transito.';		
						escribirArchivo($this->archivo,'*Error Obtención de los movimientos en tránsito.'.$this->conexionbdorigen->ErrorMsg());
						escribirArchivo($this->archivo,'*******************************************************************************************************');
						$this->valido = false; 
					}	
					else
					{
						escribirArchivo($this->archivo,'*		MOVIMIENTOS EN TRANSITO ');
						while (!$this->resulttransito->EOF)
						{									
							$this->banco    = $this->resulttransito->fields['codban'];
							$this->cuenta    = $this->resulttransito->fields['ctaban'];
							$this->codban = $this->banco;
							$this->ctaban = $this->cuenta;
							$this->numdoc    = $this->resulttransito->fields['numdoc'];
							$numdoctransito[$pos] = $this->numdoc;
							$this->codope    = $this->resulttransito->fields['codope'];
							$this->estmov    = 'L';//$this->resulttransito->fields['estmov'];
							$this->cod_pro    = cerosIzquierda($this->resulttransito->fields['cod_pro'],10);
							$this->ced_bene   = $this->resulttransito->fields['ced_bene'];
							$this->tipo_destino = $this->resulttransito->fields['tipo_destino'];
							$this->codconmov   = $this->resulttransito->fields['codconmov'];						
							$this->fecmov    = $this->resulttransito->fields['fecmov'];
							$this->conmov    = $this->resulttransito->fields['conmov'].'. Fecha Original del Documento.'.$this->fecmov;
							$this->nomproben = $this->resulttransito->fields['nomproben'];
							$this->monto     = $this->resulttransito->fields['monto'];
							$this->estbpd    = $this->resulttransito->fields['estbpd'];
							$this->estcon    = $this->resulttransito->fields['estcon'];
							$this->estcobing = $this->resulttransito->fields['estcobing'];
							$this->esttra    = $this->resulttransito->fields['esttra'];
							$this->chevau    = $this->resulttransito->fields['chevau'];
							$this->estimpche = $this->resulttransito->fields['estimpche'];
							$this->monobjret = $this->resulttransito->fields['monobjret'];
							$this->monret    = $this->resulttransito->fields['monret'];
							$this->procede   = $this->resulttransito->fields['procede'];
							$this->comprobante = $this->resulttransito->fields['comprobante'];
							$this->fecha     = $this->resulttransito->fields['fecha'];
							$this->id_mco    = $this->resulttransito->fields['id_mco'];
							$this->emicheproc = $this->resulttransito->fields['emicheproc'];
							$this->emicheced = $this->resulttransito->fields['emicheced'];
							$this->emichenom = $this->resulttransito->fields['emichenom'];
							$this->emichefec = $this->resulttransito->fields['emichefec'];
							$this->estmovint = $this->resulttransito->fields['estmovint'];
							$this->codusu    = $this->resulttransito->fields['codusu'];
							$this->codopeidb = $this->resulttransito->fields['codopeidb'];
							$this->aliidb    = $this->resulttransito->fields['aliidb'];
							$this->feccon    = $this->resulttransito->fields['feccon'];
							$this->estreglib = $this->resulttransito->fields['estreglib'];
							$this->numcarord = $this->resulttransito->fields['numcarord'];
							$this->numpolcon = $this->resulttransito->fields['numpolcon'];
							$this->coduniadmsig = $this->resulttransito->fields['coduniadmsig'];
							$this->codbansig    = $this->resulttransito->fields['codbansig'];
							$this->fecordpagsig = $this->resulttransito->fields['fecordpagsig'];
							$this->tipdocressig = $this->resulttransito->fields['tipdocressig'];
							$this->numdocressig = $this->resulttransito->fields['numdocressig'];
							$this->estmodordpag = $this->resulttransito->fields['estmodordpag'];
							$this->codfuefin = $this->resulttransito->fields['codfuefin'];
							$this->forpagsig = $this->resulttransito->fields['forpagsig'];
							$this->medpagsig = $this->resulttransito->fields['medpagsig'];
							$this->codestprosig = $this->resulttransito->fields['codestprosig'];
							$this->nrocontrolop = $this->resulttransito->fields['nrocontrolop'];
							$this->fechaconta = $this->resulttransito->fields['fechaconta'];
							$this->fechaanula = $this->resulttransito->fields['fechaanula'];
							$this->conanu = $this->resulttransito->fields['conanu'];
							$this->estant = $this->resulttransito->fields['estant'];
							$this->docant = $this->resulttransito->fields['docant'];
							$this->monamo = $this->resulttransito->fields['monamo'];
							$this->numordpagmin = $this->resulttransito->fields['numordpagmin'];
							$this->codtipfon = $this->resulttransito->fields['codtipfon'];
							$this->estserext = $this->resulttransito->fields['estserext'];
							$this->estmovcob = $this->resulttransito->fields['estmovcob'];
							$this->numconint = NULL;
							$this->estapribs = $this->resulttransito->fields['estapribs'];
							$this->estxmlibs = $this->resulttransito->fields['estxmlibs'];
							$this->codper = $this->resulttransito->fields['codper'];
							$this->codperi = $this->resulttransito->fields['codperi'];
							$this->tranoreglib = $this->resulttransito->fields['tranoreglib'];
							$this->estcondoc = $this->resulttransito->fields['estcondoc'];
							$this->fecenvfir = $this->resulttransito->fields['fecenvfir'];
							$this->fecenvcaj = $this->resulttransito->fields['fecenvcaj'];
							escribirArchivo($this->archivo,'* '.$this->codban.'//'.$this->ctaban.'//'.$this->numdoc.'//'.$this->codope.'//'.$this->estmov);
							if ($this->verificarCuenta()) 
							{
								if ($this->verificarDocumento())  //Si el documento ya existe en la bd, guardamos su indice para luego imprimirlo.
								{
									array_push($indicesrepetidos,$indiceresult);	
								}
								else	
								{								
									$exito = $this->Insert();
									if ($exito===false)
									{
										$this->mensaje='Error al Insertar el movimiento en tránsito';	
										escribirArchivo($this->archivo,'Error al Insertar el movimiento en tránsito.'.$this->conexionbd->ErrorMsg());
										escribirArchivo($this->archivo,'*******************************************************************************************************');
										$this->valido = false;
									}
									else
									{
										$consulta="UPDATE scb_movbco SET numconint=null WHERE numconint=''";
										$result2 = $this->conexionbd->Execute($consulta);									
									}
								}
							}
							else
							{
								$this->valido = false;
								$this->mensaje='La cuenta No. '.$this->ctaban.' no existe en la Base de Datos';		
								escribirArchivo($this->archivo,'La cuenta No. '.$this->ctaban.' no existe en la Base de Datos');
								escribirArchivo($this->archivo,'*******************************************************************************************************');
							}
							$indiceresult++;
							$pos++;
							$this->resulttransito->MoveNext();
						}
					}
				}
				//se insertar los movimientos de colocación
				if ($this->valido)
				{					
					$this->resultcol->MoveFirst();
					while (!$this->resultcol->EOF)
					{
						$this->banco = $this->resultcol->fields['codban'];
						$this->numcol = $this->resultcol->fields['numcol'];
						$this->cuenta = $this->resultcol->fields['ctaban'];	
						$this->saldo  = $this->resultcol->fields['saldo'];	
						$this->codban = $this->banco;
						$this->ctaban = $this->cuenta;			
						if ($this->verificarColocacion()) //Si la colocacion existe en la nueva bd
						{
							if ($this->saldo >= 0)
							{
								$this->operacion = 'NC';
							}
							else
							{
								$this->operacion = 'ND';
							}
							$this->saldo = abs($this->saldo);
							$this->codban = $this->banco;
							$this->ctaban = $this->cuenta;
							$this->numdoc = '0000000APERTURA';	
							$this->codope = $this->operacion;
							$this->estcol = 'L';
							if (!$this->verificarMovimientoColocacion())
							{
								$consulta = " INSERT INTO scb_movcol (codemp,codban,ctaban,numcol, ".
											"			numdoc,codope,estcol,fecmovcol,monmovcol, ".
											"			tasmovcol,conmov,estcob,esttranf) ".
											" VALUES ('$this->codemp','$this->banco','$this->cuenta', ".
											"		'$this->numcol','0000000APERTURA','$this->operacion', ".
											"		 'L','$this->fecfin','$this->saldo',0, ".
											"		'SALDO INICIAL DE LA COLOCACION',0,0)";
								$result = $this->conexionbd->Execute($consulta);
								if ($result===false)
								{
									$this->mensaje='Error al Insertar movimientos de colocación';		
									escribirArchivo($this->archivo,'Error al Insertar movimientos de colocación'.$this->conexionbd->ErrorMsg());
									escribirArchivo($this->archivo,'*******************************************************************************************************');
									$this->valido = false;
								}	
								else
								{
									$consulta="UPDATE scb_movbco SET numconint=null WHERE numconint=''";
									$result2 = $this->conexionbd->Execute($consulta);								
								}				 
							}
							else
							{
								$this->valido = false;
							}
						}
						else
						{
							$this->mensaje = 'La colocación No. '.$this->numcol.' NO existe en la Base de Datos';
							escribirArchivo($this->archivo,'La colocación No. '.$this->numcol.' NO existe en la Base de Datos');
							escribirArchivo($this->archivo,'*******************************************************************************************************');
							$this->valido = false;
						}
						$this->resultcol->MoveNext();
					}
				}								
			}
			if ($this->valido===true)
			{
				$total = count((array)$indicesrepetidos);
				if ($total > 0)
				{
					$fecha = date('Y_m_d_H_i');
					$nombre   = '../../vista/apr/resultados/documentos_repetidos'.$fecha.'.txt';
					$this->archivo2 = @fopen($nombre,'a+');
					$this->mensaje = 'Se ha generado un archivo (documentos_repetidos.txt), el cual contiene los documentos que no se pueden traspasar, debido a que ya se encuentran registrados en la nueva Base de Datos.';
					
					for ($i=0; $i<$total; $i++)
					{
						$indice    = $indicesrepetidos[$i];
						$this->numdoc = $numdoctransito[$indice];
						escribirArchivo($this->archivo2,'* El Movimiento No. '.$this->numdoc.' no pudo ser traspasado debido a que ya existía en la nueva Base de Datos .');
					}
				}
				escribirArchivo($this->archivo,'*Proceso ejecutado sin errores.');
				escribirArchivo($this->archivo,'*******************************************************************************************************');
			}
		}
		catch (exception $e) 
		{
			$this->valido = false;
			$this->mensaje='Ocurrio un error en la Transferencia. '.$this->conexionbd->ErrorMsg();
			escribirArchivo($this->archivo,'* Ocurrio un error en la Transferencia. ');
			escribirArchivo($this->archivo,'* Error  '.$this->conexionbd->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}			
		$this->conexionbd->CompleteTrans($this->valido);
		$this->incluirSeguridad('PROCESAR',$this->valido);			
	}
	
	

/*********************************************************************************
* @Funcion que calcula el saldo de las colocaciones.
* @parametros:
* @retorno: el saldo si se ejecuto correctamente, de lo contrario retorna falso.
* @fecha de creación: 05/12/2008.
* @autor: Ing. Gusmary Balza B.
**********************************************************
* @fecha modificacion
* @autor
* @descripcion
**********************************************************************************/			
	public function calcularSaldoColocacion()
	{
		
		$debitos=0;
		$creditos=0;
		$consulta = "SELECT COALESCE(SUM(monmovcol),0) AS creditosaux, 0 AS creditonegativosaux, 0 AS debitosaux, 0 AS debitosnegativosaux ".
			   		"  FROM scb_movcol ".
			  		" WHERE codemp='{$this->codemp}' ".
			    	"   AND codban='{$this->banco}' ".
					"   AND numcol='{$this->numcol}' ".
					"   AND (codope='CH' OR codope='ND' OR codope='RE') ".
					"   AND estcol<>'A'".			  
					" UNION ".
					"SELECT 0 AS creditosaux, COALESCE(SUM(monmovcol),0) AS creditonegativosaux, 0 AS debitosaux, 0 AS debitosnegativosaux ".
				   	"  FROM scb_movcol ".
				  	" WHERE codemp='{$this->codemp}' ".
				    "   AND codban='{$this->banco}' ".
					"   AND numcol='{$this->numcol}' ".
					"   AND (codope='CH' OR codope='ND' OR codope='RE') ".
					"   AND estcol='A'".			  
					" UNION ".
					"SELECT 0 AS creditosaux, 0 AS creditonegativosaux, COALESCE(SUM(monmovcol),0) AS debitosaux, 0 AS debitosnegativosaux ". 
					"  FROM scb_movcol ".
					" WHERE codemp='{$this->codemp}' ". 
					"   AND codban='{$this->codban}' ".
					"   AND numcol='{$this->numcol}' ".
					"   AND (codope='DP' OR codope='NC') ".
					"   AND estcol<>'A'". 
					" UNION ".
					"SELECT 0 AS creditosaux, 0 AS creditonegativosaux, 0 AS debitosaux, COALESCE(SUM(monmovcol),0) AS debitosnegativosaux ". 
					"  FROM scb_movcol ".
					" WHERE codemp='{$this->codemp}' ". 
					"   AND codban='{$this->codban}' ".
					"   AND numcol='{$this->numcol}' ".
					"   AND (codope='DP' OR codope='NC') ".
					"   AND estcol='A'"; 
		
		$result = $this->conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->mensaje = 'Error al Calcular el saldo de las colocaciones.';
			escribirArchivo($this->archivo,'* Error al Calcular el saldo de las colocaciones. '.$this->conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
			$this->valido = false;
		}
		else
		{
			$this->creditosaux = 0;
			$this->creditonegativosaux = 0;
			$this->debitosaux = 0;
			$this->debitosnegativosaux = 0;
			while (!$result->EOF)
			{
				$this->creditosaux = $this->creditosaux + $result->fields['creditosaux'];
				$this->creditonegativosaux = $this->creditonegativosaux + $result->fields['creditonegativosaux'];
				$this->debitosaux = $this->debitosaux +$result->fields['debitosaux'];
				$this->debitosnegativosaux = $this->debitosnegativosaux + $result->fields['debitosnegativosaux'];
				$result->MoveNext();
			}
			$debitos  = $this->debitosaux - $this->debitosnegativosaux;
			$creditos = $this->creditosaux - $this->creditonegativosaux;
			$saldo    = $creditos    - $debitos; 
		}			  
		return ($debitos - $creditos);				  			  
	}
	
	
/*********************************************************************************
* @Funcion que calcula el saldo de los documentos
* @parametros:
* @retorno: el saldo si se ejecuto correctamente, de lo contrario retorna falso.
* @fecha de creación: 05/12/2008.
* @autor: Ing. Gusmary Balza B.
**********************************************************
* @fecha modificacion
* @autor
* @descripcion
**********************************************************************************/				
	public function calcularSaldoDocumento() 
	{
		
		
		$saldo=0;
		$consulta ="SELECT codope AS operacion, (monto-monret) AS monto, estmov AS estado ".
			   	   "  FROM scb_movbco ".
			  	   " WHERE codemp='{$this->codemp}' ". 
				   "   AND codban='{$this->codban}' ". 
				   "   AND ctaban='{$this->ctaban}' ";		
		$result = $this->conexionbdorigen->Execute($consulta);			  
		if ($result==false)
		{
			$this->mensaje = 'Error al Calcular el saldo del documento.';
			escribirArchivo($this->archivo,'* Error al Calcular el saldo del documento. '.$this->conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
			$this->valido = false; 
		}	
		else
		{
			$debitosaux  = 0;
			$creditosaux = 0;
			$debitosnegativosaux = 0;
			$creditosnegativosaux = 0;
			while (!$result->EOF)
			{
				$this->operacion = $result->fields['operacion'];
				$this->estado    = $result->fields['estado'];
				$this->monto     = $result->fields['monto'];
				if (($this->operacion=='CH' || $this->operacion=='ND' || $this->operacion=='RE') && ($this->estado!='A'))
				{
					$creditosaux += $this->monto;
				}	
				elseif(($this->operacion=='CH' || $this->operacion=='ND' || $this->operacion=='RE') && ($this->estado=='A'))
			  	{	
					$creditosnegativosaux += $this->monto;
				} 	
				elseif(($this->operacion=='DP' || $this->operacion=='NC') && ($this->estado!='A'))
				{	
					$debitosaux += $this->monto;
				}
				elseif(($this->operacion=='DP' || $this->operacion=='NC') && ($this->estado=='A'))
				{
					$debitosnegativosaux += $this->monto;
				}				
				$result->MoveNext();
			}	
			$debitos  = $debitosaux  - $debitosnegativosaux;
	 		$creditos = $creditosaux - $creditosnegativosaux;
			print "Debitos->".$debitos."<br>";
			print "Creditos->".$creditos."<br>";
	  		$saldo    = number_format($debitos,2,'.','') - number_format($creditos,2,'.','');
			print "Saldo->".$saldo."<br>";
		}
		return $saldo;		
	}
	
	
/*********************************************************************************
* @ Funcion que determina si ya se realizo la apertura,
* @parametros:
* @retorno: 0 si no existe, de lo contrario retorna 1.
* @fecha de creación: 04/12/2008.
* @autor: Ing. Gusmary Balza B.
**********************************************************
* @fecha modificacion
* @autor
* @descripcion
**********************************************************************************/			
	public function verificarApertura()
	{
		
		
		$existe=false;
		$consulta = "SELECT COUNT(numdoc) as cantidad ".
			   		"  FROM scb_movbco ".
				 	" WHERE codemp='{$this->codemp}' ".
					"	AND codban='{$this->codban}' ". 					 
					"   AND ctaban='{$this->ctaban}' ".
					"   AND numdoc='{$this->numdoc}' ";
		$result = $this->conexionbd->Execute($consulta);			  
		if ($result==false)
		{
			$this->mensaje = 'Error al Verificar si existe la Apertura.';
			escribirArchivo($this->archivo,'* Error al Verificar si existe la Apertura. '.$this->conexionbd->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
			$this->valido = false; 
		}
		elseif ((!$result->EOF)&&($result->fields['cantidad']>0))
		{
			$existe=true;
		}		  			  
		return $existe;	
	}
	
	
/*********************************************************************************
* @ Funcion que determina si existen colocaciones.
* @parametros:
* @retorno: 0 si no existe, de lo contrario retorna 1.
* @fecha de creación: 04/12/2008.
* @autor: Ing. Gusmary Balza B.
**********************************************************
* @fecha modificacion
* @autor
* @descripcion
**********************************************************************************/		
	public function verificarColocacion()
	{
		
		
		$existe=false;
		$consulta = "SELECT COUNT(numcol) AS cantidad ".
			     	"  FROM scb_colocacion ".
			    	" WHERE codemp='{$this->codemp}' ". 
			    	"   AND ctaban='{$this->ctaban}'  ".
					"   AND codban='{$this->codban}'  ".
					"   AND numcol='{$this->numcol}'";
		$result = $this->conexionbd->Execute($consulta);	
		if ($result==false)
		{
			$this->mensaje='Error al obtener la Colocación.';
			escribirArchivo($this->archivo,'* Error al obtener la Colocación. '.$this->conexionbd->ErrorMsg());
			$this->valido = false; //return false;
		}
		elseif ((!$result->EOF) && ($result->fields['cantidad']>0))
		{
			$existe=true;
		}	
		return $existe;	  
	}
	
	
/*********************************************************************************
* @ Funcion que determina si determina si existe la cuenta.
* @parametros:
* @retorno: 0 si no existe, de lo contrario retorna 1.
* @fecha de creación: 04/12/2008.
* @autor: Ing. Gusmary Balza B.
**********************************************************
* @fecha modificacion
* @autor
* @descripcion
**********************************************************************************/			
	public function verificarCuenta()
	{
		
		
		$existe=false;
		$consulta = " SELECT ctaban ".
					" FROM scb_ctabanco ".
					" WHERE codemp='{$this->codemp}' ".
					" AND codban='{$this->codban}' ".
					" AND ctaban='{$this->ctaban}'";
		$result = $this->conexionbd->Execute($consulta);
		if ($result==false)
		{
			$this->mensaje='Error al obtener la cuenta.';
			escribirArchivo($this->archivo,'* Error al Verificar Cuenta. '.$this->conexionbd->ErrorMsg());
			$this->valido = false;
		}
		elseif (!$result->EOF)
		{
			$existe=true;
		}
		return $existe;
	}
	
	
	
/*********************************************************************************
* @ Funcion que determina si determina si existe el documento.
* @parametros:
* @retorno: 0 si no existe, de lo contrario retorna 1.
* @fecha de creación: 04/12/2008.
* @autor: Ing. Gusmary Balza B.
**********************************************************
* @fecha modificacion
* @autor
* @descripcion
**********************************************************************************/		
	public function verificarDocumento() 
	{
		
		
		$existe=false;
		$consulta = " SELECT numdoc ".
	                " FROM scb_movbco ".
			 	 	" WHERE codemp='{$this->codemp}' ".
				    " AND codban='{$this->codban}' ".
					" AND ctaban='{$this->ctaban}' ".
			 	    " AND numdoc='{$this->numdoc}' ".
					" AND codope='{$this->codope}' ".
					" AND estmov='{$this->estmov}' ";
		$result = $this->conexionbd->Execute($consulta);
		if ($result==false)
		{
			$this->mensaje='Error al obtener el documento.';
			escribirArchivo($this->archivo,'* Error al Verificar Documento. '.$this->conexionbd->ErrorMsg());
			$this->valido = false; 
		}
		elseif (!$result->EOF)
		{
			$existe=true;
		}
		return $existe;
	}
	

/*********************************************************************************
* @Funcion que determina si existen el movimiento de colocacion en la nueva BD
* @parametros:
* @retorno: Retorna 0 si no existe, de lo contrario retorna 1
* @fecha de creación: 04/12/2008.
* @autor: Ing. Gusmary Balza B.
**********************************************************
* @fecha modificacion
* @autor
* @descripcion
**********************************************************************************/	
	public function verificarMovimientoColocacion() 
	{
		

		$existe=false;
		$consulta = "SELECT numcol ".
			   		"  FROM scb_movcol ".
			  		" WHERE codemp='{$this->codemp}' ".
			    	"   AND codban='{$this->codban}' ".
					"   AND ctaban='{$this->ctaban}' ".
					"   AND numcol='{$this->numcol}' ".
					"	AND numdoc='{$this->numdoc}' ".
					"   AND codope='{$this->codope}' ".
					"	AND estcol='{$this->estcol}' ";
		$result = $this->conexionbd->Execute($consulta);
		if ($result==false)
		{
			$this->mensaje='Error al Obtener Movimiento de Colocación.';
			escribirArchivo($this->archivo,'* Error al Obtener Movimiento de Colocación. '.$this->conexionbd->ErrorMsg());
			$this->valido = false; 
		}
		elseif (!$result->EOF)
		{
			$existe=true;
		}
		return $existe;
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