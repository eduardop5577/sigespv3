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
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_icomprobantespg.php');

class ServicioComprobanteSPG implements IComprobanteSPG 
{
	public  $mensaje; 
	public  $valido; 
	public  $daoDetalleSpg;
	public  $conexionBaseDatos; 
	private $niveles_spg;
	private $estmodape;
	private $estmodprog;
	private $periodo;
	private $vali_nivel;
	public $mensajespg;
	private $formpre;
	private $estvalest;
	private $nivelest;
	public  $status;
	public 	$asignado;
	public 	$aumento;
	public 	$disminucion;
	public 	$precomprometido;
	public 	$comprometido;
	public 	$causado;
	public 	$pagado;
	
	public function __construct() 
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->niveles_spg = null;
		$this->estmodape=$_SESSION['la_empresa']['estmodape'];
		$this->estmodprog=$_SESSION['la_empresa']['estmodprog'];
		$this->periodo=convertirFechaBd($_SESSION['la_empresa']['periodo']);
		$this->vali_nivel=$_SESSION['la_empresa']['vali_nivel'];
		$this->formpre=$_SESSION['la_empresa']['formpre'];
		$this->estvaldis=$_SESSION['la_empresa']['estvaldis'];
   	    $this->estvalest =	$_SESSION['la_empresa']['valestpre'];
		$this->nivelest  =	$_SESSION['la_empresa']['nivvalest'];
		$this->mensajespg='';
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		//$this->conexionBaseDatos->debug=true;
		$this->cargarNiveles();
		if($this->vali_nivel==5)
		{
			$this->vali_nivel=$this->obtenerNivel(str_replace('-','',$this->formpre));
		}
		if($this->estvaldis==0)
		{
			$this->vali_nivel=0;
		}
		$this->status='';
		$this->asignado=0;
		$this->aumento=0;
		$this->disminucion=0;
		$this->precomprometido=0;
		$this->comprometido=0;
		$this->causado=0;
		$this->pagado=0;
	}
	
	public function existeCuenta() 
	{
		$existe = false;
		$cadenaSql = "SELECT status ".
					 "	FROM spg_cuentas ".
					 " WHERE codemp='".$this->daoDetalleSpg->codemp."' ".
					 "   AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
					 "   AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
					 "   AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
					 "   AND codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
					 "   AND codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
					 "   AND estcla='".$this->daoDetalleSpg->estcla."' ".
					 "   AND trim(spg_cuenta)='".$this->daoDetalleSpg->spg_cuenta."' ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				$status = $dataSet->fields['status']; 
				if($status==='C')
				{
					$existe = true;
				}
				else
				{
					$this->mensaje .= '  -> La cuenta '.$this->daoDetalleSpg->spg_cuenta.'::'.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.' No es de movimiento';
					$this->valido = false;
				}
			}
			else
			{
				$this->mensaje .= '  -> La cuenta '.$this->daoDetalleSpg->spg_cuenta.'::'.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.' No existe en la estructura ';
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $existe;
	}

	public function existeCuentaFuenteFinanciamiento() 
	{
		$existe = true;
		$cadenaSql = "SELECT codemp ".
					 "	FROM spg_cuenta_fuentefinanciamiento ".
					 " WHERE codemp='".$this->daoDetalleSpg->codemp."' ".
					 "   AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
					 "   AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
					 "   AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
					 "   AND codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
					 "   AND codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
					 "   AND estcla='".$this->daoDetalleSpg->estcla."' ".
					 "   AND codfuefin='".$this->daoDetalleSpg->codfuefin."' ".
					 "   AND trim(spg_cuenta)='".$this->daoDetalleSpg->spg_cuenta."' ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->EOF)
			{
				$this->mensaje .= '  -> La cuenta '.$this->daoDetalleSpg->spg_cuenta.'::'.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.' No esta asociada a la fuente de financiamiento '.$this->daoDetalleSpg->codfuefin;
				$this->valido = false;
				$existe = false;
			}
		}
		unset($dataSet);
		return $existe;
	}
		
	public function existeCierreSPG()
	{
		$existe = false;
		$cadenaSql = "SELECT estciespg ".
					 "	FROM sigesp_empresa ".
					 " WHERE codemp='".$_SESSION['la_empresa']['codemp']."' ".
					 "   AND estciespg=1";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				$existe = true;
				$this->mensaje .= '  -> Ya se realizo el cierre presupuestario de gasto no se pueden registrar movimientos de este tipo';
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $existe;
	}

	public function buscarOperacion($mensaje)
	{
		$asignar=0;
		$aumento=0;
		$disminucion=0;
		$precomprometer=0;
		$comprometer=0;
		$causar=0;
		$pagar=0; 
		$operacion=''; 
		$mensaje=strtoupper(trim($mensaje));
		if(!(strpos($mensaje,'I')===false))
		{
			$asignar=1;
		}
		if(!(strpos($mensaje,'A')===false))
		{
			$aumento=1;
		}
		if(!(strpos($mensaje,'D')===false))
		{
			$disminucion=1;
		}
		if(!(strpos($mensaje,'R')===false))
		{
			$precomprometer=1;
		}
		if(!(strpos($mensaje,'O')===false))
		{
			$comprometer=1;
		}
		if(!(strpos($mensaje,'C')===false))
		{
			$causar=1;
		}
		if(!(strpos($mensaje,'P')===false))
		{
			$pagar=1;
		}
		$existe = false;
		$cadenaSql = "SELECT operacion ".
					 "  FROM spg_operaciones ".
				     " WHERE asignar=".$asignar.
				     "   AND aumento=".$aumento.
				     "   AND disminucion=".$disminucion.
				     "   AND precomprometer=".$precomprometer.
				     "   AND comprometer=".$comprometer.
				     "   AND causar=".$causar.
				     "   AND pagar=".$pagar;
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				$operacion=$dataSet->fields['operacion'];
			}
			else
			{
				$this->mensaje .= '  -> No hay operacion asociada al mensaje '.$mensaje;
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $operacion;
	}

	public function buscarMensaje($operacion)
	{
   	    $mensaje='';
		$cadenaSql = "SELECT  asignar, aumento, disminucion, precomprometer, comprometer, causar, pagar".
					 "  FROM spg_operaciones ".
				     " WHERE operacion = '".$operacion."'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				if($dataSet->fields['asignar']==1)
				{
					$mensaje .='I';
				}
				if($dataSet->fields['aumento']==1)
				{
					$mensaje .='A';
				}
				if($dataSet->fields['disminucion']==1)
				{
					$mensaje .='D';
				}
				if($dataSet->fields['precomprometer']==1)
				{
					$mensaje .='R';
				}
				if($dataSet->fields['comprometer']==1)
				{
					$mensaje .='O';
				}
				if($dataSet->fields['causar']==1)
				{
					$mensaje .='C';
				}
				if($dataSet->fields['pagar']==1)
				{
					$mensaje .='P';
				}
				$mensaje=trim($mensaje);
			}
			else
			{
				$this->mensaje .= '  -> No hay mensaje asociada a la operacion '.$operacion;
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $mensaje;
    }
    
	public function existeMovimiento($tipo_comp)
	{
 	    $existe=false;
 	    $monto=0;
 	    $orden=0;
		if(($this->estmodprog==1)&&($tipo_comp=='2'))
		{
			$mes=substr($cabecera->fecha,5,2);
			$cadenaSql="SELECT SUM(enero+febrero+marzo) as trimestre1, SUM(abril+mayo+junio) as trimestre2,".
					   "       SUM(julio+agosto+septiembre) as trimestre3, SUM(octubre+noviembre+diciembre) as trimestre4,".
					   "       SUM(enero) as enero, SUM(febrero) as febrero, SUM(marzo) as marzo, SUM(abril) as abril, SUM(mayo) as mayo,".
					   "       SUM(junio) as junio, SUM(julio) as julio, SUM(agosto) as agosto, SUM(septiembre) as septiembre,".
					   "       SUM(octubre) as octubre, SUM(noviembre) as noviembre, SUM(diciembre) as diciembre, SUM(orden) AS orden".
					   "  FROM spg_dtmp_mensual, spg_dtmp_cmp, sigesp_cmp_md  ".
					   " WHERE spg_dtmp_mensual.codemp='".$this->daoDetalleSpg->codemp."' ".
					   "   AND spg_dtmp_mensual.spg_cuenta = '".$this->daoDetalleSpg->spg_cuenta."' ".
					   "   AND spg_dtmp_mensual.codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
					   "   AND spg_dtmp_mensual.codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
					   "   AND spg_dtmp_mensual.codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
					   "   AND spg_dtmp_mensual.codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
					   "   AND spg_dtmp_mensual.codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
					   "   AND spg_dtmp_mensual.estcla='".$this->daoDetalleSpg->estcla."' ".
					   "   AND spg_dtmp_mensual.procede='".$this->daoDetalleSpg->procede."' ".
					   "   AND spg_dtmp_mensual.comprobante='".$this->daoDetalleSpg->comprobante."' ".
					   "   AND sigesp_cmp_md.fechaconta = '".$this->daoDetalleSpg->fecha."' ".
					   "   AND spg_dtmp_mensual.procede_doc = '".$this->daoDetalleSpg->procede_doc."' ".
				  	   "   AND spg_dtmp_mensual.documento = '".$this->daoDetalleSpg->documento."' ".
					   "   AND spg_dtmp_mensual.operacion = '".$this->daoDetalleSpg->operacion."' ".
					   "   AND spg_dtmp_cmp.codemp=spg_dtmp_mensual.codemp".
					   "   AND spg_dtmp_cmp.procede=spg_dtmp_mensual.procede".
					   "   AND spg_dtmp_cmp.comprobante=spg_dtmp_mensual.comprobante".
					   "   AND spg_dtmp_cmp.fecha=spg_dtmp_mensual.fecha".
					   "   AND spg_dtmp_cmp.codestpro1=spg_dtmp_mensual.codestpro1".
					   "   AND spg_dtmp_cmp.codestpro2=spg_dtmp_mensual.codestpro2".
					   "   AND spg_dtmp_cmp.codestpro3=spg_dtmp_mensual.codestpro3".
					   "   AND spg_dtmp_cmp.codestpro4=spg_dtmp_mensual.codestpro4".
					   "   AND spg_dtmp_cmp.codestpro5=spg_dtmp_mensual.codestpro5".
					   "   AND spg_dtmp_cmp.estcla=spg_dtmp_mensual.estcla".
					   "   AND spg_dtmp_cmp.spg_cuenta=spg_dtmp_mensual.spg_cuenta".
					   "   AND spg_dtmp_cmp.operacion=spg_dtmp_mensual.operacion".
					   "   AND spg_dtmp_cmp.procede_doc=spg_dtmp_mensual.procede_doc".
					   "   AND spg_dtmp_cmp.documento=spg_dtmp_mensual.documento".
					   "   AND spg_dtmp_cmp.codemp=sigesp_cmp_md.codemp".
					   "   AND spg_dtmp_cmp.procede=sigesp_cmp_md.procede".
					   "   AND spg_dtmp_cmp.comprobante=sigesp_cmp_md.comprobante".
					   "   AND spg_dtmp_cmp.fecha=sigesp_cmp_md.fecha".
					   " GROUP BY spg_dtmp_mensual.codemp,spg_dtmp_mensual.procede,spg_dtmp_mensual.comprobante,spg_dtmp_mensual.fecha,".
					   "          spg_dtmp_mensual.codestpro1,spg_dtmp_mensual.codestpro2,spg_dtmp_mensual.codestpro3,spg_dtmp_mensual.codestpro4,".
					   "          spg_dtmp_mensual.codestpro5,spg_dtmp_mensual.estcla,spg_dtmp_mensual.spg_cuenta,spg_dtmp_mensual.operacion,".
					   "          spg_dtmp_mensual.procede_doc,spg_dtmp_mensual.documento";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			else
			{
				if (!$dataSet->EOF)
				{
					$trimestre1 = number_format($dataSet->fields['trimestre1'],2,'.','');
					$trimestre2 = number_format($dataSet->fields['trimestre2'],2,'.','');
					$trimestre3 = number_format($dataSet->fields['trimestre3'],2,'.','');
					$trimestre4 = number_format($dataSet->fields['trimestre4'],2,'.','');
					$enero = number_format($dataSet->fields['enero'],2,'.','');
					$febrero = number_format($dataSet->fields['febrero'],2,'.','');
					$marzo = number_format($dataSet->fields['marzo'],2,'.','');
					$abril = number_format($dataSet->fields['abril'],2,'.','');
					$mayo = number_format($dataSet->fields['mayo'],2,'.','');
					$junio = number_format($dataSet->fields['junio'],2,'.','');
					$julio = number_format($dataSet->fields['julio'],2,'.','');
					$agosto = number_format($dataSet->fields['agosto'],2,'.','');
					$septiembre = number_format($dataSet->fields['septiembre'],2,'.','');
					$octubre = number_format($dataSet->fields['octubre'],2,'.','');
					$noviembre = number_format($dataSet->fields['noviembre'],2,'.','');
					$diciembre = number_format($dataSet->fields['diciembre'],2,'.','');
					$orden=$dataSet->fields['orden'];
					$existe=true;
					switch($mes)
					{
						case'01':
							$monto=$trimestre1;
							if($this->estmodape==0)
							{
								$monto=$enero;
							}
						break;
						case'02':
							$monto=$trimestre1;
							if($this->estmodape==0)
							{
								$monto=$febrero;
							}
						break;
						case'03':
							$monto=$trimestre1;
							if($this->estmodape==0)
							{
								$monto=$marzo;
							}
						break;
						case'04':
							$monto=$trimestre2;
							if($this->estmodape==0)
							{
								$monto=$abril;
							}
						break;
						case'05':
							$monto=$trimestre2;
							if($this->estmodape==0)
							{
								$monto=$mayo;
							}
						break;
						case'06':
							$monto=$trimestre2;
							if($this->estmodape==0)
							{
								$monto=$junio;
							}
						break;
						case'07':
							$monto=$trimestre3;
							if($this->estmodape==0)
							{
								$monto=$julio;
							}
						break;
						case'08':
							$monto=$trimestre3;
							if($this->estmodape==0)
							{
								$monto=$agosto;
							}
						break;
						case'09':
							$monto=$trimestre3;
							if($this->estmodape==0)
							{
								$monto=$septiembre;
							}
						break;
						case'10':
							$monto=$trimestre4;
							if($this->estmodape==0)
							{
								$monto=$octubre;
							}
						break;
						case'11':
							$monto=$trimestre4;
							if($this->estmodape==0)
							{
								$monto=$noviembre;
							}
						break;
						case'12':
							$monto=$trimestre4;
							if($this->estmodape==0)
							{
								$monto=$diciembre;
							}
						break;
					}
				}
			}
			unset($dataSet);
		}
		else
		{
			$cadenaSql="SELECT monto, orden ".
					   "  FROM spg_dt_cmp ".		
					   " WHERE codemp='".$this->daoDetalleSpg->codemp."' ".
					   "   AND codban = '".$this->daoDetalleSpg->codban."' ".
					   "   AND ctaban = '".$this->daoDetalleSpg->ctaban."' ".
					   "   AND spg_cuenta = '".$this->daoDetalleSpg->spg_cuenta."' ".
					   "   AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
					   "   AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
					   "   AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
					   "   AND codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
					   "   AND codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
					   "   AND estcla='".$this->daoDetalleSpg->estcla."' ".
					   "   AND codfuefin='".$this->daoDetalleSpg->codfuefin."' ".
					   "   AND procede='".$this->daoDetalleSpg->procede."' ".
					   "   AND comprobante='".$this->daoDetalleSpg->comprobante."' ".
					   "   AND procede_doc = '".$this->daoDetalleSpg->procede_doc."' ".
				  	   "   AND documento = '".$this->daoDetalleSpg->documento."' ".
					   "   AND operacion = '".$this->daoDetalleSpg->operacion."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			else
			{
				if (!$dataSet->EOF)
				{
					$orden=$dataSet->fields['orden'];
					$monto=number_format($dataSet->fields['monto'],2,'.','');
					$existe=true;
				}			
			}
			unset($dataSet);
		}
		return $existe;
	}	

	public function cargarNiveles()
	{
		$formato=$this->formpre.'-';
		$posicion=1;
		$indice=1;
		$posicion = posocurrencia($formato,'-' , $indice ) - $indice;	
		do
		{
			$this->niveles_spg[$indice] = $posicion ;
			$indice = $indice + 1;
			$posicion = posocurrencia($formato,'-' , $indice ) - $indice;
		} while ($posicion>=0);
	}

	public function obtenerNivel($cuenta)
	{
		$nivel=0;
		$anterior=0;
		$longitud=0;
		$cadena='';
		$nivel=count((array)$this->niveles_spg);
		do
		{
			$anterior=$this->niveles_spg[$nivel-1]+1;
			$longitud=$this->niveles_spg[$nivel]-$this->niveles_spg[$nivel-1];
			$cadena=substr(trim($cuenta),$anterior,$longitud); 
			$li=intval($cadena);
		    if($li>0)
			{
				return $nivel;
			}
			$nivel=$nivel-1;
		}while($nivel>1);
		return $nivel;
	}

	public function obtenerCuentaSiguiente($cuenta)
	{
  		$MaxNivel=count((array)$this->niveles_spg);
		$nivel=$this->obtenerNivel($cuenta);
		$anterior=0;
		$longitud=0;
		$cadena='';
		if($nivel>1)
		{
			$anterior=$this->niveles_spg[$nivel - 1]; 
			$cadena=substr($cuenta,0,$anterior+1);
			$longitud=strlen($cadena);
			$long=(($this->niveles_spg[$MaxNivel]+1) - $longitud);
			$cadena=str_pad(trim($cadena),$long+$longitud,'0');
		} 
		return $cadena;
	} 
	
	public function validarEstructura()
    {
    	$existe = false;
    	$cadenaSql="SELECT codemp ".
  				   "  FROM spg_val_estructura ". 
  				   " WHERE codemp='".$this->daoDetalleSpg->codemp."' ". 
  				   "   AND codestpro1 = '".$this->daoDetalleSpg->codestpro1."' ".
  				   "   AND codestpro2 = '".$this->daoDetalleSpg->codestpro2."' ". 
  				   "   AND codestpro3 = '".$this->daoDetalleSpg->codestpro3."' ".
  				   "   AND codestpro4 = '".$this->daoDetalleSpg->codestpro4."' ". 
  				   "   AND codestpro5 = '".$this->daoDetalleSpg->codestpro5."' ". 
  				   "   AND estcla = '".$this->daoDetalleSpg->estcla."'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$dataSet->EOF)
			{
  				$existe = true;
			}
		}
		unset($dataSet);
		return $existe;
    }
	
	public function calcularAsignadoProgramado($fechavalidacion)
	{
		$monto=0;
		$mes=substr($fechavalidacion,5,2);
		$cadenaSql="SELECT SUM(enero+febrero+marzo) as trimestre1, SUM(abril+mayo+junio) as trimestre2,".
				   "       SUM(julio+agosto+septiembre) as trimestre3, SUM(octubre+noviembre+diciembre) as trimestre4,".
				   "       MAX(enero) as enero, MAX(febrero) as febrero, MAX(marzo) as marzo, MAX(abril) as abril, MAX(mayo) as mayo,".
				   "       MAX(junio) as junio, MAX(julio) as julio, MAX(agosto) as agosto, MAX(septiembre) as septiembre,".
				   "       MAX(octubre) as octubre, MAX(noviembre) as noviembre, MAX(diciembre) as diciembre".
				   "  FROM spg_cuentas".
				   " WHERE codemp='".$this->daoDetalleSpg->codemp."'".
				   "   AND trim(spg_cuenta)='".$this->daoDetalleSpg->spg_cuenta."'".
				   "   AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
				   "   AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
			       "   AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
				   "   AND codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
				   "   AND codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
				   "   AND estcla='".$this->daoDetalleSpg->estcla."'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				switch($mes)
				{
					case'01':
						$monto=number_format($dataSet->fields['trimestre1'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['enero'],2,'.','');
						}
					break;
					case'02':
						$monto=number_format($dataSet->fields['trimestre1'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['febrero'],2,'.','');
						}
					break;
					case'03':
						$monto=number_format($dataSet->fields['trimestre1'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['marzo'],2,'.','');
						}
					break;
					case'04':
						$monto=number_format($dataSet->fields['trimestre2'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['abril'],2,'.','');
						}
					break;
					case'05':
						$monto=number_format($dataSet->fields['trimestre2'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['mayo'],2,'.','');
						}
					break;
					case'06':
						$monto=number_format($dataSet->fields['trimestre2'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['junio'],2,'.','');
						}
					break;
					case'07':
						$monto=number_format($dataSet->fields['trimestre3'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['julio'],2,'.','');
						}
					break;
					case'08':
						$monto=number_format($dataSet->fields['trimestre3'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['agosto'],2,'.','');
						}
					break;
					case'09':
						$monto=number_format($dataSet->fields['trimestre3'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['septiembre'],2,'.','');
						}
					break;
					case'10':
						$monto=number_format($dataSet->fields['trimestre4'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['octubre'],2,'.','');
						}
					break;
					case'11':
						$monto=number_format($dataSet->fields['trimestre4'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['noviembre'],2,'.','');
						}
					break;
					case'12':
						$monto=number_format($dataSet->fields['trimestre4'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['diciembre'],2,'.','');
						}
					break;
				}
			}
		}
		unset($dataSet);
		return $monto;
	}

	public function calcularSaldoEstructura($fechavalidacion,$operacion)
	{
   	    $filtroestructura = '';
		$monto=0;
		switch ($this->nivelest) 
		{
			case 'N1':
				$filtroestructura = " AND spg_dt_cmp.codestpro1='".$this->daoDetalleSpg->codestpro1."'". 
				                    " AND spg_dt_cmp.estcla='".$this->daoDetalleSpg->estcla."' ";
				break;
			
			case 'N2':
				$filtroestructura = " AND spg_dt_cmp.codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
				                    " AND spg_dt_cmp.codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
				                    " AND spg_dt_cmp.estcla='".$this->daoDetalleSpg->estcla."' ";
				break;
				
			case 'N3':
				$filtroestructura = " AND spg_dt_cmp.codestpro1='".$this->daoDetalleSpg->codestpro1."' ". 
				                    " AND spg_dt_cmp.codestpro2='".$this->daoDetalleSpg->codestpro2."' ". 
				                    " AND spg_dt_cmp.codestpro3='".$this->daoDetalleSpg->codestpro3."' ". 
				                    " AND spg_dt_cmp.estcla='".$this->daoDetalleSpg->estcla."' ";
				break;
				
			case 'N4':
				$filtroestructura = " AND spg_dt_cmp.codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
									" AND spg_dt_cmp.codestpro2='".$this->daoDetalleSpg->codestpro2."' ". 
									" AND spg_dt_cmp.codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
									" AND spg_dt_cmp.codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
									" AND spg_dt_cmp.estcla='".$this->daoDetalleSpg->estcla."' ";
				break;
		}
		$cadenaSql="SELECT SUM(CASE WHEN spg_dt_cmp.monto is null then 0 else spg_dt_cmp.monto end)  As monto ".
                   "  FROM spg_dt_cmp ".
	  			   " INNER JOIN  spg_operaciones  ".
                   "    ON spg_dt_cmp.codemp='".$this->daoDetalleSpg->codemp."' ".
                   "   AND spg_operaciones.".$operacion."=1 ".
				   "   AND spg_dt_cmp.spg_cuenta = '".$this->daoDetalleSpg->spg_cuenta."' ".
				   "   AND spg_dt_cmp.fecha >='".$this->periodo."' ".
				   "   AND spg_dt_cmp.fecha <='".$fechavalidacion."' ".
				  $filtroestructura.
				   "   AND spg_dt_cmp.operacion=spg_operaciones.operacion ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
			   $monto = number_format($dataSet->fields['monto'],2,'.','');
			}
		}
		unset($dataSet);
		return $monto;
	}
	
	public function calcularSaldoRango($fechavalidacion,$operacion)
	{
		$monto=0;
		$cadenaSql="SELECT SUM(CASE WHEN monto is null then 0 else monto end)  As monto ".
                   "  FROM spg_dt_cmp ".
				   " INNER JOIN spg_operaciones  ".
                   "    ON spg_dt_cmp.codemp='".$this->daoDetalleSpg->codemp."' ".
                   "   AND spg_operaciones.".$operacion."=1 ".
				   "   AND spg_dt_cmp.spg_cuenta = '".$this->daoDetalleSpg->spg_cuenta."' ".
			       "   AND spg_dt_cmp.fecha >='".$this->periodo."' ".
				   "   AND spg_dt_cmp.fecha <='".$fechavalidacion."' ".
				   "   AND spg_dt_cmp.codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
				   "   AND spg_dt_cmp.codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
			       "   AND spg_dt_cmp.codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
				   "   AND spg_dt_cmp.codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
				   "   AND spg_dt_cmp.codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
				   "   AND spg_dt_cmp.estcla='".$this->daoDetalleSpg->estcla."' ".
				   "   AND spg_dt_cmp.operacion=spg_operaciones.operacion ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
			   $monto = number_format($dataSet->fields['monto'],2,'.','');
			}
		}
		unset($dataSet);
		return $monto;
	}
	
	public function calcularSaldoProgramadoMP($fechavalidacion,$operacion)
	{
 		$monto=0;
		$mes=substr($fechavalidacion,5,2);
		$cadenaSql="SELECT SUM(enero+febrero+marzo) as trimestre1, SUM(abril+mayo+junio) as trimestre2,".
				   "       SUM(julio+agosto+septiembre) as trimestre3, SUM(octubre+noviembre+diciembre) as trimestre4,".
				   "       SUM(enero) as enero, SUM(febrero) as febrero, SUM(marzo) as marzo, SUM(abril) as abril, SUM(mayo) as mayo,".
				   "       SUM(junio) as junio, SUM(julio) as julio, SUM(agosto) as agosto, SUM(septiembre) as septiembre,".
				   "       SUM(octubre) as octubre, SUM(noviembre) as noviembre, SUM(diciembre) as diciembre".
				   "  FROM spg_dtmp_mensual, spg_operaciones, sigesp_cmp_md  ".
				   " WHERE spg_dtmp_mensual.codemp='".$this->daoDetalleSpg->codemp."' ".
				   "   AND spg_operaciones.".$operacion."=1 ".
				   "   AND spg_dtmp_mensual.spg_cuenta = '".$this->daoDetalleSpg->spg_cuenta."' ".
				   "   AND spg_dtmp_mensual.codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
				   "   AND spg_dtmp_mensual.codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
				   "   AND spg_dtmp_mensual.codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
				   "   AND spg_dtmp_mensual.codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
				   "   AND spg_dtmp_mensual.codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
				   "   AND spg_dtmp_mensual.estcla='".$this->daoDetalleSpg->estcla."' ".
				   "   AND sigesp_cmp_md.estapro=1".
				   "   AND sigesp_cmp_md.codemp=spg_dtmp_mensual.codemp".
				   "   AND sigesp_cmp_md.procede=spg_dtmp_mensual.procede".
				   "   AND sigesp_cmp_md.comprobante=spg_dtmp_mensual.comprobante".
				   "   AND sigesp_cmp_md.fecha=spg_dtmp_mensual.fecha".
				   "   AND spg_dtmp_mensual.operacion=spg_operaciones.operacion "; 
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!($dataSet->EOF))
			{
				switch($mes)
				{
					case'01':
						$monto=number_format($dataSet->fields['trimestre1'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['enero'],2,'.','');
						}
					break;
					case'02':
						$monto=number_format($dataSet->fields['trimestre1'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['febrero'],2,'.','');
						}
					break;
					case'03':
						$monto=number_format($dataSet->fields['trimestre1'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['marzo'],2,'.','');
						}
					break;
					case'04':
						$monto=number_format($dataSet->fields['trimestre2'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['abril'],2,'.','');
						}
					break;
					case'05':
						$monto=number_format($dataSet->fields['trimestre2'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['mayo'],2,'.','');
						}
					break;
					case'06':
						$monto=number_format($dataSet->fields['trimestre2'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['junio'],2,'.','');
						}
					break;
					case'07':
						$monto=number_format($dataSet->fields['trimestre3'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['julio'],2,'.','');
						}
					break;
					case'08':
						$monto=number_format($dataSet->fields['trimestre3'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['agosto'],2,'.','');
						}
					break;
					case'09':
						$monto=number_format($dataSet->fields['trimestre3'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['septiembre'],2,'.','');
						}
					break;
					case'10':
						$monto=number_format($dataSet->fields['trimestre4'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['octubre'],2,'.','');
						}
					break;
					case'11':
						$monto=number_format($dataSet->fields['trimestre4'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['noviembre'],2,'.','');
						}
					break;
					case'12':
						$monto=number_format($dataSet->fields['trimestre4'],2,'.','');
						if($this->estmodape==0)
						{
							$monto=number_format($dataSet->fields['diciembre'],2,'.','');
						}
					break;
				}
			}
		}
		return $monto;
	}	

	public function calcularSaldoProgramado($fechavalidacion,$operacion)
	{
 		$monto=0;
		$mes=substr($fechavalidacion,5,2);
		$anio=substr($fechavalidacion,0,4);
		$fechainicio=$anio.'-'.$mes.'-01';
		$diafin=substr(ultimoDiaMes($mes,$anio),0,2);
		$fechafin=$anio.'-'.$mes.'-'.$diafin;
		if ($this->estmodape!=0)
		{
			if((intval($mes)>=1) && (intval($mes)<=3))
			{
				$fechainicio=$anio.'-01-01';
				$fechafin=$anio.'-03-31';
			}
			if((intval($mes)>=4) && (intval($mes)<=6))
			{
				$fechainicio=$anio.'-04-01';
				$fechafin=$anio.'-06-30';
			}
			if((intval($mes)>=7) && (intval($mes)<=9))
			{
				$fechainicio=$anio.'-07-01';
				$fechafin=$anio.'-09-30';
			}
			if((intval($mes)>=10) && (intval($mes)<=12))
			{
				$fechainicio=$anio.'-10-01';
				$fechafin=$anio.'-12-31';
			}
		}
		$ls_sql="SELECT SUM(CASE WHEN monto is null then 0 else monto end)  As monto ".
                "  FROM spg_dt_cmp ".
				" INNER JOIN  spg_operaciones  ".
                "    ON spg_dt_cmp.codemp='".$this->daoDetalleSpg->codemp."' ".
                "   AND spg_operaciones.".$operacion."=1 ".
				"   AND spg_dt_cmp.spg_cuenta = '".$this->daoDetalleSpg->spg_cuenta."' ".
				"   AND spg_dt_cmp.fecha >='".$fechainicio."' AND fecha <='".$fechafin."' ".
				"   AND spg_dt_cmp.codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
				"   AND spg_dt_cmp.codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
			    "   AND spg_dt_cmp.codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
				"   AND spg_dt_cmp.codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
				"   AND spg_dt_cmp.codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
				"   AND spg_dt_cmp.estcla='".$this->daoDetalleSpg->estcla."' ".
				"   AND spg_dt_cmp.operacion=spg_operaciones.operacion ";
				$dataSet  = $this->conexionBaseDatos->Execute ( $ls_sql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!($dataSet->EOF))
			{
				$monto = number_format($dataSet->fields['monto'],2,'.','');
			}
		}
		return $monto;
	} 	
		
	public function saldoSelect($tipovalidacion='COMPROBANTE')
	{
		$fechavalidacion=convertirFechaBd($_SESSION['fechacomprobante']);
		$this->status='';
		if(($tipovalidacion=='ACTUAL')&&($this->estmodprog!=1))
		{
			$fechavalidacion=date('Y-m-d');
		}
		$cadenaSql="SELECT status ".
				   "  FROM spg_cuentas ".
				   " WHERE codemp='".$this->daoDetalleSpg->codemp."' ".
				   "   AND codestpro1 = '".$this->daoDetalleSpg->codestpro1."' ".
				   "   AND codestpro2 = '".$this->daoDetalleSpg->codestpro2."' ".
				   "   AND codestpro3 = '".$this->daoDetalleSpg->codestpro3."' ".
				   "   AND codestpro4 = '".$this->daoDetalleSpg->codestpro4."' ".
				   "   AND codestpro5 = '".$this->daoDetalleSpg->codestpro5."' ".
				   "   AND estcla = '".$this->daoDetalleSpg->estcla."'     ".
				   "   AND trim(spg_cuenta) = '".$this->daoDetalleSpg->spg_cuenta."'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$dataSet->EOF)
			{
				$this->status=$dataSet->fields['status'];
			}
			else
			{
				$this->mensaje .= '  -> La cuenta '.$this->daoDetalleSpg->spg_cuenta.'::'.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.' No existe en la estructura.';
				$this->valido = false;				
			}
		}
		unset($dataSet);
		if($this->valido)
		{
			$validarestructura=false;
			if ($this->estvalest==1)
			{
				$validarestructura = $this->validarEstructura();	
			}				
			if($this->status=='C') // Cuenta de Movimiento
			{
				if($this->valido)
				{
					$operacion='asignar';
					if($this->estmodprog==1)
					{
						$this->asignado=$this->calcularAsignadoProgramado($fechavalidacion,$operacion);
					}
					else
					{
						if (($this->estvalest==1)&&($validarestructura))
						{ 
							$this->asignado=$this->calcularSaldoEstructura($fechavalidacion,$operacion);
						}
						else
						{
							$this->asignado=$this->calcularSaldoRango($fechavalidacion,$operacion);
						}
					}
				}
				if($this->valido)
				{
					$operacion='aumento';
					if($this->estmodprog==1)
					{
						$this->aumento=$this->calcularSaldoProgramadoMP($fechavalidacion,$operacion);
					}
					else
					{
						if (($this->estvalest==1)&&($validarestructura))
						{ 
							$this->aumento=$this->calcularSaldoEstructura($fechavalidacion,$operacion);
						}
						else
						{
							$this->aumento=$this->calcularSaldoRango($fechavalidacion,$operacion);
						}
					}
				}
				if($this->valido)
				{
					$operacion='disminucion';
					if($this->estmodprog==1)
					{
						$this->disminucion=$this->calcularSaldoProgramadoMP($fechavalidacion,$operacion);
					}
					else
					{
						if (($this->estvalest==1)&&($validarestructura))
						{ 
							$this->disminucion=$this->calcularSaldoEstructura($fechavalidacion,$operacion);
						}
						else
						{
							$this->disminucion=$this->calcularSaldoRango($fechavalidacion,$operacion);
						}
					}
				}
				if($this->valido)
				{
					$operacion='precomprometer';
					if($this->estmodprog==1)
					{
							$this->precomprometido=$this->calcularSaldoProgramado($fechavalidacion,$operacion);
					}
					else
					{
						if (($this->estvalest==1)&&($validarestructura))
						{ 
							$this->precomprometido=$this->calcularSaldoEstructura($fechavalidacion,$operacion);
						}
						else
						{
							$this->precomprometido=$this->calcularSaldoRango($fechavalidacion,$operacion);
						}
					}
				}
				if($this->valido)
				{
					$operacion='comprometer';
					if($this->estmodprog==1)
					{
							$this->comprometido=$this->calcularSaldoProgramado($fechavalidacion,$operacion);
					}
					else
					{
						if (($this->estvalest==1)&&($validarestructura))
						{ 
							$this->comprometido=$this->calcularSaldoEstructura($fechavalidacion,$operacion);
						}
						else
						{
							$this->comprometido=$this->calcularSaldoRango($fechavalidacion,$operacion);
						}
					}
				}
				if($this->valido)
				{
					$operacion='causar';
					if($this->estmodprog==1)
					{
							$this->causado=$this->calcularSaldoProgramado($fechavalidacion,$operacion);
					}
					else
					{
						if (($this->estvalest==1)&&($validarestructura))
						{ 
							$this->causado=$this->calcularSaldoEstructura($fechavalidacion,$operacion);
						}
						else
						{
							$this->causado=$this->calcularSaldoRango($fechavalidacion,$operacion);
						}
					}
				}
				if($this->valido)
				{
					$operacion='pagar';
					if($this->estmodprog==1)
					{
							$this->pagado=$this->calcularSaldoProgramado($fechavalidacion,$operacion);
					}
					else
					{
						if (($this->estvalest==1)&&($validarestructura))
						{ 
							$this->pagado=$this->calcularSaldoEstructura($fechavalidacion,$operacion);
						}
						else
						{
							$this->pagado=$this->calcularSaldoRango($fechavalidacion,$operacion);
						}
					}
				}
			}
			if($this->status=='S') // Cuenta Madre
			{
				$select = '';
				$filtro = '';
				$group = ''; 
				if (($this->estvalest==1)&&($validarestructura))
				{
					switch ($this->nivelest)
					{
						case 'N1':
							$select = 'codemp, codestpro1, estcla, spg_cuenta, status, SUM(asignado) AS asignado, SUM(aumento) AS aumento, '.
							          'SUM(disminucion) AS disminucion, SUM(precomprometido) AS precomprometido, '.
       							  	  'SUM(comprometido) AS comprometido,SUM(causado) AS causado,SUM(pagado) AS pagado';
							$group = 'GROUP BY codemp, codestpro1, estcla, spg_cuenta, status ';
							$filtro = " AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
							          " AND estcla='".$this->daoDetalleSpg->estcla."' ";
							break;
						case 'N2':
							$select = 'codemp, codestpro1, codestpro2, estcla, spg_cuenta, status, SUM(asignado) AS asignado, '.
							          'SUM(aumento) AS aumento, SUM(disminucion) AS disminucion, SUM(precomprometido) AS precomprometido, '.
       							  	  'SUM(comprometido) AS comprometido,SUM(causado) AS causado,SUM(pagado) AS pagado';
							$group = 'GROUP BY codemp, codestpro1, codestpro2, estcla, spg_cuenta, status ';
							$filtro = " AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
							          " AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
							          " AND estcla='".$this->daoDetalleSpg->estcla."' ";
						break;
						case 'N3':
							$select = 'codemp, codestpro1, codestpro2, codestpro3, estcla, spg_cuenta, status, SUM(asignado) AS asignado, '.
							          'SUM(aumento) AS aumento, SUM(disminucion) AS disminucion, SUM(precomprometido) AS precomprometido, '.
       							  	  'SUM(comprometido) AS comprometido,SUM(causado) AS causado,SUM(pagado) AS pagado';
							$group = 'GROUP BY codemp, codestpro1, codestpro2, codestpro3, estcla, spg_cuenta, status ';
							$filtro = " AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
							          " AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
							          " AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
									  " AND estcla='".$this->daoDetalleSpg->estcla."' ";
						break;
						case 'N4':
							$select = 'codemp, codestpro1, codestpro2, codestpro3, codestpro4, estcla, spg_cuenta, status, SUM(asignado) AS asignado, '.
							          'SUM(aumento) AS aumento, SUM(disminucion) AS disminucion, SUM(precomprometido) AS precomprometido, '.
       							  	  'SUM(comprometido) AS comprometido,SUM(causado) AS causado,SUM(pagado) AS pagado';
							$group = 'GROUP BY codemp, codestpro1, codestpro2, codestpro3, codestpro4, estcla, spg_cuenta, status ';
							$filtro = " AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
							          " AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
							          " AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
							          " AND codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
									  " AND estcla='".$this->daoDetalleSpg->estcla."' ";
						break;
						case 'N5':
							$select = 'codemp, codestpro1, codestpro2, codestpro3, codestpro4, estcla, spg_cuenta, status, SUM(asignado) AS asignado, '.
							          'SUM(aumento) AS aumento, SUM(disminucion) AS disminucion, SUM(precomprometido) AS precomprometido, '.
       							  	  'SUM(comprometido) AS comprometido,SUM(causado) AS causado,SUM(pagado) AS pagado';
							$group = 'GROUP BY codemp, codestpro1, codestpro2, codestpro3, codestpro4, estcla, spg_cuenta, status ';
							$filtro = " AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
							          " AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
							          " AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
							          " AND codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
							          " AND codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
									  " AND estcla='".$this->daoDetalleSpg->estcla."' ";
						break;
					}
				}
				else 
				{
					$select = 'asignado,aumento,disminucion,precomprometido,comprometido,causado,pagado';
					$filtro = " AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
							  " AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
							  " AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
							  " AND codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
							  " AND codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
							  " AND estcla='".$this->daoDetalleSpg->estcla."' ";
				}
				$cadenaSql="SELECT {$select} ".
						   "  FROM spg_cuentas ".
						   " WHERE codemp='".$this->daoDetalleSpg->codemp."' ".
						   "   AND trim(spg_cuenta) = '".$this->daoDetalleSpg->spg_cuenta."'".
						   $filtro.' '.
						   $group;
				$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
				if ($dataSet===false)
				{
					$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
					$this->valido = false;
				}
				else
				{
					if(!$dataSet->EOF)
					{
						$this->asignado=number_format($dataSet->fields['asignado'],2,'.','');
						$this->aumento=number_format($dataSet->fields['aumento'],2,'.','');
						$this->disminucion=number_format($dataSet->fields['disminucion'],2,'.','');
						$this->precomprometido=number_format($dataSet->fields['precomprometido'],2,'.','');
						$this->comprometido=number_format($dataSet->fields['comprometido'],2,'.','');
						$this->causado=number_format($dataSet->fields['causado'],2,'.','');
						$this->pagado=number_format($dataSet->fields['pagado'],2,'.','');
					}
				}
				unset($dataSet);
			}
		}
		return $this->valido;
	} 

	public function saldosAjusta($monto_anterior,$monto_actual,$tipovalidacion='COMPROBANTE')
	{
		$fechavalidacion=convertirFechaBd($_SESSION['fechacomprobante']);
		if(($tipovalidacion=='ACTUAL')&&($this->estmodprog!=1))
		{
			$fechavalidacion=date('Y-m-d');
		}
		$disponible=(($this->asignado+$this->aumento)-($this->disminucion+$this->comprometido+$this->precomprometido));
		$nivel=$this->obtenerNivel($this->daoDetalleSpg->spg_cuenta);
		if (!(strpos($this->mensajespg,'I')===false)) // asignación
		{
			$this->asignado=$this->asignado-$monto_anterior+$monto_actual;
			$procesado=true;
		}
		if (!(strpos($this->mensajespg,'A')===false))// A-Aumento 
		{ 
			if ($nivel <= $this->vali_nivel)
			{
                            $monto = $disponible-$monto_anterior+$monto_actual;
                            if(number_format($monto,2,'.','')>=0)  
                            { 
                                    $this->aumento=$this->aumento-$monto_anterior+$monto_actual;
                            }
                            else
                            {
                                    $this->valido = false;
                                    $this->mensaje .= 'La disminuci&#243;n del Aumento sobregira el presupuesto. '.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.'::'.$this->daoDetalleSpg->spg_cuenta.' a la fecha '.$fechavalidacion;			
                            }
                        }
                        else
                        {
                            $this->aumento=$this->aumento-$monto_anterior+$monto_actual;
                        }
			$procesado=true;
		}
		if (!(strpos($this->mensajespg,'D')===false)) //	D-Disminucion
		{
			if ($nivel <= $this->vali_nivel)
			{
                            $monto = $disponible + $monto_anterior;
                            if(number_format($monto_actual,2,'.','') <= number_format($monto,2,'.',''))  
                            { 
                                    $this->disminucion=$this->disminucion-$monto_anterior+$monto_actual; 
                            }
                            else
                            {
                                    $this->valido = false;
                                    $this->mensaje .= 'El monto a disminuir es mayor que la Disponibilidad. '.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.'::'.$this->daoDetalleSpg->spg_cuenta.' a la fecha '.$fechavalidacion;			
                            }
                        }
                        else
                        {
                            $this->disminucion=$this->disminucion-$monto_anterior+$monto_actual; 
                        }
			$procesado=true;
		}
		if (!(strpos($this->mensajespg,'R')===false))//R-PreComprometer
		{
			if ($nivel <= $this->vali_nivel)
			{
				$monto = $disponible + $monto_anterior;
				if(number_format($monto_actual,2,'.','') > number_format($monto,2,'.',''))
				{
					$this->valido = false;
					$this->mensaje .= 'El monto a Precomprometer es mayor que la Disponibilidad.'.number_format($monto_actual,2,'.','').'>'.number_format($monto,2,'.','').' '.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.'::'.$this->daoDetalleSpg->spg_cuenta.' a la fecha '.$fechavalidacion;			
				}				
				else
				{
					$this->precomprometido=$this->precomprometido-$monto_anterior+$monto_actual;
				}
			} 	
			else
			{
				$this->precomprometido=$this->precomprometido-$monto_anterior+$monto_actual;
			}
			$procesado=true;
		}
		if (!(strpos($this->mensajespg,'O')===false))//	O-Comprometer
		{
			if ($nivel <= $this->vali_nivel)
			{
				$monto = $disponible + $monto_anterior;
				if(number_format($monto_actual,2,'.','') > number_format($monto,2,'.',''))
				{
					$this->valido = false;
					$this->mensaje .= 'El monto a Comprometer es mayor que la Disponibilidad.'.number_format($monto_actual,2,'.','').'>'.number_format($monto,2,'.','').' '.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.'::'.$this->daoDetalleSpg->spg_cuenta.' a la fecha '.$fechavalidacion;			
				}			
				else
				{
					$this->comprometido=$this->comprometido-$monto_anterior+$monto_actual;
				}
			}	
			else
			{
				$this->comprometido=$this->comprometido-$monto_anterior+$monto_actual;
			}
			$procesado=true;
		}
		if (!(strpos($this->mensajespg,'C')===false))//	C-Causar
		{
			$monto = $this->causado - $monto_anterior + $monto_actual;
			if(number_format($monto,2,'.','') <=  number_format($this->comprometido,2,'.','') )
			{
				$this->causado = $this->causado - $monto_anterior + $monto_actual;
			}
			else
			{		
                                $this->valido = false;
                                $this->mensaje .= 'El monto a Causar es mayor que el Comprometido.'.number_format($monto,2,'.','').'>'.number_format($this->comprometido,2,'.','').' '.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.'::'.$this->daoDetalleSpg->spg_cuenta.' a la fecha '.$fechavalidacion;			
			}
			$procesado = true;
		} 
		if (!(strpos($this->mensajespg,'P')===false)) // P-Pagar
		{
			$monto = $this->pagado - $monto_anterior + $monto_actual;
			if (number_format($monto,2,'.','') <= number_format($this->causado,2,'.',''))
			{
				$this->pagado = $this->pagado - $monto_anterior + $monto_actual;
			}
			else
			{
				$this->valido = false;
				$this->mensaje .= 'El monto a Pagar es mayor que el Causado.'.number_format($monto,2,'.','').'>'.number_format($this->causado,2,'.','').' '.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.'::'.$this->daoDetalleSpg->spg_cuenta.' a la fecha '.$fechavalidacion;			
			}
			$procesado = true;
		}
		if(!$procesado)
		{
			$this->valido = false;
			$this->mensaje .= 'El codigo de mensaje es Invalido : '.$this->mensajespg;			
		}
		return $this->valido;
    } 

	public function saldosUpdate($monto_anterior,$monto_actual,$tipovalidacion)
	{
		if($this->estvalest==1)
		{
			$this->estvalest=0;
			$this->saldoSelect($tipovalidacion);
			$this->saldosAjusta($monto_anterior,$monto_actual,$tipovalidacion);			
			$this->estvalest=1;
		}
		$cadenaSql="UPDATE spg_cuentas ".
				   "   SET asignado=".$this->asignado.", ".
				   "       aumento=".$this->aumento.", ".
				   "       disminucion=".$this->disminucion.", ".
			       "       precomprometido=".$this->precomprometido.", ".
				   "       comprometido=".$this->comprometido.", ".
				   "       causado=".$this->causado.", ".
			       "  	   pagado=".$this->pagado." ".
				   " WHERE codemp='".$this->daoDetalleSpg->codemp."' ".
				   "   AND codestpro1 ='".$this->daoDetalleSpg->codestpro1."' ".
			       "   AND codestpro2 ='".$this->daoDetalleSpg->codestpro2."' ".
				   "   AND codestpro3 ='".$this->daoDetalleSpg->codestpro3."' ".
				   "   AND codestpro4 ='".$this->daoDetalleSpg->codestpro4."' ".
			       "   AND codestpro5 ='".$this->daoDetalleSpg->codestpro5."' ".
			       "   AND estcla ='".$this->daoDetalleSpg->estcla."'".
				   "   AND spg_cuenta = '".$this->daoDetalleSpg->spg_cuenta."' ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $this->valido;
	}
	
    public function saldoActual($monto_anterior,$monto_actual)
    {
		$cuentaactual=$this->daoDetalleSpg->spg_cuenta;
		$nivel=$this->obtenerNivel($cuentaactual);
		while(($nivel>=1)and($this->valido)and($nivel!=''))
		{  
			$this->status='';
			$this->asignado=0;
			$this->aumento=0;
			$this->disminucion=0;
			$this->precomprometido=0;
			$this->comprometido=0;
			$this->causado=0;
			$this->pagado=0;
			$validacion = 'ACTUAL';
			if ($this->saldoSelect($validacion))
			{
				if ($this->saldosAjusta($monto_anterior,$monto_actual,$validacion))
				{
					$validacion = 'COMPROBANTE';
					if ($this->saldoSelect($validacion))
					{
						if ($this->saldosAjusta($monto_anterior,$monto_actual,$validacion))
						{
							if(!$this->saldosUpdate($monto_anterior,$monto_actual,$validacion))
							{
								$this->valido=false;
								return $this->valido;
							}
						}
					}
				}
			}
			if(($this->obtenerNivel($this->daoDetalleSpg->spg_cuenta)==1)||(!$this->valido))
			{
				break;
			}
			$this->daoDetalleSpg->spg_cuenta=$this->obtenerCuentaSiguiente($this->daoDetalleSpg->spg_cuenta);
			$nivel=$this->obtenerNivel($this->daoDetalleSpg->spg_cuenta);
		}
		$this->daoDetalleSpg->spg_cuenta=$cuentaactual;
		return $this->valido;
	}

    public function validaIntegridadComprobanteAjuste($daoComprobante)
    {
	    $existe=false;
    	$cadenaSql="SELECT codemp ".
			       "  FROM spg_dt_cmp ".
			       " WHERE codemp='".$this->daoDetalleSpg->codemp."' ".
				   "   AND codestpro1 ='".$this->daoDetalleSpg->codestpro1."'  ".
	    		   "   AND codestpro2 ='".$this->daoDetalleSpg->codestpro2."' ". 
			       "   AND codestpro3 ='".$this->daoDetalleSpg->codestpro3."'  ".
	    		   "   AND codestpro4 = '".$this->daoDetalleSpg->codestpro4."' ".
				   "   AND codestpro5 ='".$this->daoDetalleSpg->codestpro5."'  ".
	    		   "   AND estcla ='".$this->daoDetalleSpg->estcla."'  ".
				   "   AND procede_doc='".$this->daoDetalleSpg->procede."' ".
	    		   "   AND documento='".$this->daoDetalleSpg->comprobante."'  ".
				   "   AND spg_cuenta ='".$this->daoDetalleSpg->spg_cuenta."' ".
	    		   "   AND operacion='".$this->daoDetalleSpg->operacion."'  ".
				   "   AND monto<0  ".
				   "   AND procede<>'".$this->daoDetalleSpg->procede."' ".
	    		   "   AND comprobante<>'".$this->daoDetalleSpg->comprobante."'  ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				 $existe=true;
				 $this->valido = false;
				 $this->mensaje .= 'El Comprobante '.$daoComprobante->codemp.'::'.$daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.$daoComprobante->codban.'::'.$daoComprobante->ctaban.' Esta referenciado en otro.';
			}
		}
		unset($dataSet);
		return $existe;
	} 

    public function validaIntegridadComprobanteOtros($daoComprobante)
    {
         $existe=false;
    	if(!(strpos($this->mensajespg,'O')===false)and!(strpos($this->mensajespg,'C')===false)and!(strpos($this->mensajespg,'P')===false))
		{
			return $existe;
		}
		$incluir='';
	    $excluir='';
	    if(!(strpos($this->mensajespg,'O')===false))
		{
			$excluir=$excluir.'spg_operaciones.comprometer=0 AND ';
		}
	    if(!(strpos($this->mensajespg,'C')===false))
		{
			$excluir=$excluir.'spg_operaciones.causar=0 AND ';
		}
 		else
		{
			$incluir=$incluir.'spg_operaciones.causar=1 OR ';
		}
        if(!(strpos($this->mensajespg,'P')===false))
		{
			$excluir=$excluir.'spg_operaciones.pagar=0 AND ';
		}
 		else
		{
			$incluir=$incluir.'spg_operaciones.pagar=1 OR ';
		}
        $condicion='';         
        if(!empty($excluir)) 
		{
		    $excluir='('.substr($excluir,0,strlen($excluir)- 4).')';
            $condicion=$condicion.$excluir.' AND';
		}
        if(!empty($incluir)) 
		{
		    $incluir = '('.substr($incluir,0,strlen($incluir)- 3).')';
            $condicion =$condicion.$incluir.' AND';
		}
	    $cadenaSql="SELECT spg_dt_cmp.procede As procede, spg_dt_cmp.comprobante As comprobante, spg_dt_cmp.fecha as fecha ".
			       "  FROM spg_dt_cmp ".
	    		   " INNER JOIN  sigesp_cmp ".		
			       "    ON spg_dt_cmp.codemp='".$this->daoDetalleSpg->codemp."' ".
				   "   AND sigesp_cmp.tipo_comp=1 ".
				   "   AND spg_dt_cmp.codestpro1 ='".$this->daoDetalleSpg->codestpro1."' ".
				   "   AND spg_dt_cmp.codestpro2 ='".$this->daoDetalleSpg->codestpro2."' ".
				   "   AND spg_dt_cmp.codestpro3 ='".$this->daoDetalleSpg->codestpro3."' ".
				   "   AND spg_dt_cmp.codestpro4 ='".$this->daoDetalleSpg->codestpro4."' ".
				   "   AND spg_dt_cmp.codestpro5 ='".$this->daoDetalleSpg->codestpro5."' ".
				   "   AND spg_dt_cmp.estcla ='".$this->daoDetalleSpg->estcla."' ".
			       "   AND spg_dt_cmp.documento='".$this->daoDetalleSpg->comprobante."'  ".
			       "   AND spg_dt_cmp.procede_doc='".$this->daoDetalleSpg->procede."' ".
				   "   AND spg_dt_cmp.spg_cuenta ='".$this->daoDetalleSpg->spg_cuenta."' ".
				   "   AND spg_dt_cmp.operacion='".$this->daoDetalleSpg->operacion."' ".
	    		   "   AND spg_dt_cmp.monto>0 ".
				   "   AND spg_dt_cmp.codemp=sigesp_cmp.codemp ".
				   "   AND spg_dt_cmp.procede=sigesp_cmp.procede ".
				   "   AND spg_dt_cmp.comprobante=sigesp_cmp.comprobante ".
				   "   AND spg_dt_cmp.fecha=sigesp_cmp.fecha ".
				   "   AND spg_dt_cmp.codban=sigesp_cmp.codban ".
				   "   AND spg_dt_cmp.ctaban=sigesp_cmp.ctaban ".
	    		   " INNER JOIN  spg_operaciones ".		
			       "    ON spg_dt_cmp.codemp='".$this->daoDetalleSpg->codemp."' ".
				   "   AND spg_dt_cmp.codestpro1 ='".$this->daoDetalleSpg->codestpro1."' ".
				   "   AND spg_dt_cmp.codestpro2 ='".$this->daoDetalleSpg->codestpro2."' ".
				   "   AND spg_dt_cmp.codestpro3 ='".$this->daoDetalleSpg->codestpro3."' ".
				   "   AND spg_dt_cmp.codestpro4 ='".$this->daoDetalleSpg->codestpro4."' ".
				   "   AND spg_dt_cmp.codestpro5 ='".$this->daoDetalleSpg->codestpro5."' ".
				   "   AND spg_dt_cmp.estcla ='".$this->daoDetalleSpg->estcla."' ".
			       "   AND spg_dt_cmp.documento='".$this->daoDetalleSpg->comprobante."'  ".
			       "   AND spg_dt_cmp.procede_doc='".$this->daoDetalleSpg->procede."' ".
				   "   AND spg_dt_cmp.spg_cuenta ='".$this->daoDetalleSpg->spg_cuenta."' ".
				   "   AND spg_dt_cmp.operacion='".$this->daoDetalleSpg->operacion."' ".
	    		   "   AND spg_dt_cmp.monto>0 ".
	               "   AND ".$condicion." ".
				   "    spg_dt_cmp.operacion=spg_operaciones.operacion ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				 $existe=true;
				 $this->valido = false;
				 $this->mensaje .= 'El Comprobante '.$daoComprobante->codemp.'::'.$daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.$daoComprobante->codban.'::'.$daoComprobante->ctaban.' Esta referenciado en otro.';
			}
		}
		unset($dataSet);
		return $existe;
	}
	
	public function guardarDetalleSPG($daoComprobante,$arrdetallespg,$arrevento) 
	{
		if(!$this->existeCierreSPG())
		{
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrevento['evento'];
			$servicioEvento->codemp=$arrevento['codemp'];
			$servicioEvento->codsis=$arrevento['codsis'];
			$servicioEvento->nomfisico=$arrevento['nomfisico'];
			$totalspg = count((array)$arrdetallespg);
			for($i=1;($i<=$totalspg)&&($this->valido);$i++)
			{
				if ($arrdetallespg[$i]['mensaje']=='')
				{
					$this->mensajespg = trim(strtoupper($this->buscarMensaje($arrdetallespg[$i]['operacion'])));					
				}
				else
				{
					$this->mensajespg = trim(strtoupper($arrdetallespg[$i]['mensaje']));
				}
				$this->daoDetalleSpg = FabricaDao::CrearDAO('N', 'spg_dt_cmp');				
				$this->daoDetalleSpg->codemp=$arrdetallespg[$i]['codemp'];
				$this->daoDetalleSpg->procede=$arrdetallespg[$i]['procede'];
				$this->daoDetalleSpg->comprobante=$daoComprobante->comprobante;
				$this->daoDetalleSpg->codban=$arrdetallespg[$i]['codban'];
				$this->daoDetalleSpg->ctaban=$arrdetallespg[$i]['ctaban'];
				$this->daoDetalleSpg->estcla=$arrdetallespg[$i]['estcla'];
				$this->daoDetalleSpg->codestpro1=$arrdetallespg[$i]['codestpro1'];
				$this->daoDetalleSpg->codestpro2=$arrdetallespg[$i]['codestpro2'];
				$this->daoDetalleSpg->codestpro3=$arrdetallespg[$i]['codestpro3'];
				$this->daoDetalleSpg->codestpro4=$arrdetallespg[$i]['codestpro4'];
				$this->daoDetalleSpg->codestpro5=$arrdetallespg[$i]['codestpro5'];
				$this->daoDetalleSpg->spg_cuenta=$arrdetallespg[$i]['spg_cuenta'];
				$this->daoDetalleSpg->procede_doc=$arrdetallespg[$i]['procede_doc'];
				$this->daoDetalleSpg->documento=$arrdetallespg[$i]['documento'];
				$this->daoDetalleSpg->operacion=$this->buscarOperacion($this->mensajespg);
				$this->daoDetalleSpg->codfuefin=$arrdetallespg[$i]['codfuefin'];
				$this->daoDetalleSpg->fecha=$arrdetallespg[$i]['fecha'];
				$_SESSION['fechacomprobante']=$this->daoDetalleSpg->fecha;
				$this->daoDetalleSpg->descripcion=$arrdetallespg[$i]['descripcion'];
				$this->daoDetalleSpg->monto=$arrdetallespg[$i]['monto'];
				$this->daoDetalleSpg->orden=$arrdetallespg[$i]['orden'];
				$this->daoDetalleSpg->codcencos='---';
				if((is_null($this->daoDetalleSpg->documento)) or (empty($this->daoDetalleSpg->documento)))
				{
					$this->mensaje .= 'El N° de Documento no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleSpg->procede_doc)) or (empty($this->daoDetalleSpg->procede_doc)))
				{
					$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleSpg->descripcion)) or (empty($this->daoDetalleSpg->descripcion)))
				{
					$this->mensaje .= 'La Descripci&#243;n no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if(($this->existeCuenta())&&($this->valido))
				{
					if ($this->existeCuentaFuenteFinanciamiento())
					{
						if(!$this->existeMovimiento($daoComprobante->tipo_comp))
						{
							if($this->saldoActual(0,$this->daoDetalleSpg->monto))
							{
								$this->valido=$this->daoDetalleSpg->incluir();
								if(!$this->valido)
								{
									$this->mensaje .= $this->daoDetalleSpg->ErrorMsg;
								}
								$servicioEvento->tipoevento=$this->valido; 
								if($this->valido)
								{
									$servicioEvento->desevetra='Incluyo detalle presupuestario '.$this->daoDetalleSpg->codestpro1.'::'.
															   $this->daoDetalleSpg->codestpro2.'::'.$this->daoDetalleSpg->codestpro3.'::'.
															   $this->daoDetalleSpg->codestpro4.'::'.$this->daoDetalleSpg->codestpro5.'::'.
															   $this->daoDetalleSpg->spg_cuenta.'::'.$this->daoDetalleSpg->procede_doc.'::'.
															   $this->daoDetalleSpg->documento.'::'.$this->daoDetalleSpg->operacion.'::'.
															   $this->daoDetalleSpg->codfuefin.'::'.$this->daoDetalleSpg->fecha.'::'.
															   $this->daoDetalleSpg->monto.'   del comprobante '.$daoComprobante->codemp.'::'.
															   $daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.
															   $daoComprobante->codban.'::'.$daoComprobante->ctaban;			
									$servicioEvento->incluirEvento();
								}
								else
								{
									$this->valido=false;
									$servicioEvento->desevetra=$this->mensaje;
									$servicioEvento->incluirEvento();
								}							
							}
							else
							{
								$this->valido=false;
								$servicioEvento->tipoevento=$this->valido; 
								$servicioEvento->desevetra=$this->mensaje;
								$servicioEvento->incluirEvento();
							}
						}
						else
						{
							$this->valido=false;
							$this->mensaje .= ' -> El movimiento '.$this->daoDetalleSpg->codestpro1.'::'.
															   $this->daoDetalleSpg->codestpro2.'::'.$this->daoDetalleSpg->codestpro3.'::'.
															   $this->daoDetalleSpg->codestpro4.'::'.$this->daoDetalleSpg->codestpro5.'::'.
															   $this->daoDetalleSpg->spg_cuenta.'::'.$this->daoDetalleSpg->procede_doc.'::'.
															   $this->daoDetalleSpg->documento.'::'.$this->daoDetalleSpg->operacion.'::'.
															   $this->daoDetalleSpg->codfuefin.'::'.$this->daoDetalleSpg->fecha.'::'.
															   $this->daoDetalleSpg->monto.'   del comprobante '.$daoComprobante->codemp.'::'.
															   $daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.
															   $daoComprobante->codban.'::'.$daoComprobante->ctaban.', Ya existe.';
							$servicioEvento->tipoevento=$this->valido; 
							$servicioEvento->desevetra=$this->mensaje;
							$servicioEvento->incluirEvento();
						}
					}
					else
					{
						$this->valido=false;						
						$servicioEvento->tipoevento=$this->valido; 
						$servicioEvento->desevetra=$this->mensaje;
						$servicioEvento->incluirEvento();
					}
				}
				else
				{
					$this->valido=false;
					$servicioEvento->tipoevento=$this->valido; 
					$servicioEvento->desevetra=$this->mensaje;
					$servicioEvento->incluirEvento();
				}
			}
			unset($servicioEvento);
		}
		return $this->valido;	
	}
	
	public function eliminarDetalleSPG($daoComprobante,$arrdetallespg,$arrevento) 
	{
		if(!$this->existeCierreSPG())
		{
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrevento['evento'];
			$servicioEvento->codemp=$arrevento['codemp'];
			$servicioEvento->codsis=$arrevento['codsis'];
			$servicioEvento->nomfisico=$arrevento['nomfisico'];
			$totalspg = count((array)$arrdetallespg);
			for($i=1;($i<=$totalspg)&&($this->valido);$i++)
			{
				$this->mensajespg = trim(strtoupper($this->buscarMensaje($arrdetallespg[$i]['operacion'])));
				$criterio="     codemp  = '".$arrdetallespg[$i]['codemp']."'".
				          " AND procede = '".$arrdetallespg[$i]['procede']."' ".
						  " AND comprobante = '".$arrdetallespg[$i]['comprobante']."' ".
						  " AND codban = '".$arrdetallespg[$i]['codban']."' ".
						  " AND ctaban = '".$arrdetallespg[$i]['ctaban']."' ".
						  " AND estcla = '".$arrdetallespg[$i]['estcla']."' ".
						  " AND codestpro1 = '".$arrdetallespg[$i]['codestpro1']."' ".
						  " AND codestpro2 = '".$arrdetallespg[$i]['codestpro2']."' ".
						  " AND codestpro3 = '".$arrdetallespg[$i]['codestpro3']."' ".
						  " AND codestpro4 = '".$arrdetallespg[$i]['codestpro4']."' ".
						  " AND codestpro5 = '".$arrdetallespg[$i]['codestpro5']."' ".
						  " AND spg_cuenta = '".$arrdetallespg[$i]['spg_cuenta']."' ".
						  " AND procede_doc = '".$arrdetallespg[$i]['procede_doc']."' ".
						  " AND documento = '".$arrdetallespg[$i]['documento']."' ".
						  " AND operacion = '".$arrdetallespg[$i]['operacion']."' ".
						  " AND codfuefin = '".$arrdetallespg[$i]['codfuefin']."' ";					  
				$this->daoDetalleSpg = FabricaDao::CrearDAO('C','spg_dt_cmp','',$criterio);

				if((is_null($this->daoDetalleSpg->documento)) or (empty($this->daoDetalleSpg->documento)))
				{
					$this->mensaje .= 'El N° de Documento no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleSpg->procede_doc)) or (empty($this->daoDetalleSpg->procede_doc)))
				{
					$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleSpg->descripcion)) or (empty($this->daoDetalleSpg->descripcion)))
				{
					$this->mensaje .= 'La Descripcion no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if(($this->existeCuenta())&&($this->valido))
				{
					if ($this->existeCuentaFuenteFinanciamiento())
					{
						if($this->existeMovimiento($daoComprobante->tipo_comp))
						{
							if(!$this->validaIntegridadComprobanteAjuste($daoComprobante))
							{
								if(!$this->validaIntegridadComprobanteOtros($daoComprobante))
								{
									if($this->saldoActual($this->daoDetalleSpg->monto,0))
									{
										$this->valido=$this->daoDetalleSpg->eliminar('','',true);
										if(!$this->valido)
										{
											$this->mensaje .= $this->daoDetalleSpg->ErrorMsg;
										}
										$servicioEvento->tipoevento=$this->valido; 
										if($this->valido)
										{
											$servicioEvento->desevetra='Elimino detalle presupuestario '.$this->daoDetalleSpg->codestpro1.'::'.
																	   $this->daoDetalleSpg->codestpro2.'::'.$this->daoDetalleSpg->codestpro3.'::'.
																	   $this->daoDetalleSpg->codestpro4.'::'.$this->daoDetalleSpg->codestpro5.'::'.
																	   $this->daoDetalleSpg->spg_cuenta.'::'.$this->daoDetalleSpg->procede_doc.'::'.
																	   $this->daoDetalleSpg->documento.'::'.$this->daoDetalleSpg->operacion.'::'.
																	   $this->daoDetalleSpg->codfuefin.'::'.$this->daoDetalleSpg->fecha.'::'.
																	   $this->daoDetalleSpg->monto.'   del comprobante '.$daoComprobante->codemp.'::'.
																	   $daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.
																	   $daoComprobante->codban.'::'.$daoComprobante->ctaban;			
											$servicioEvento->incluirEvento();
										}
										else
										{
											$this->valido=false;
											$servicioEvento->desevetra=$this->mensaje;
											$servicioEvento->incluirEvento();
										}
									}	
									else
									{
										$this->valido=false;
										$servicioEvento->tipoevento=$this->valido; 
										$servicioEvento->desevetra=$this->mensaje;
										$servicioEvento->incluirEvento();
										
									}						
								}
								else
								{
									$this->valido=false;
									$servicioEvento->tipoevento=$this->valido; 
									$servicioEvento->desevetra=$this->mensaje;
									$servicioEvento->incluirEvento();
								}
							}
							else
							{
								$this->valido=false;
								$servicioEvento->tipoevento=$this->valido; 
								$servicioEvento->desevetra=$this->mensaje;
								$servicioEvento->incluirEvento();
							}
						}
						else
						{
							$this->valido=false;
							$servicioEvento->tipoevento=$this->valido; 
							$servicioEvento->desevetra=$this->mensaje;
							$servicioEvento->incluirEvento();
						}
					}
					else
					{
						$this->valido=false;						
						$servicioEvento->tipoevento=$this->valido; 
						$servicioEvento->desevetra=$this->mensaje;
						$servicioEvento->incluirEvento();
					}
				}
				else
				{
					$this->valido=false;
					$servicioEvento->tipoevento=$this->valido; 
					$servicioEvento->desevetra=$this->mensaje;
					$servicioEvento->incluirEvento();
				}
				unset($this->daoDetalleSpg);
			}
			unset($servicioEvento);
		}
		return $this->valido;	
	}
	
	public function setDaoDetalleSpg($arrdetallespg) {
		$this->daoDetalleSpg = FabricaDao::CrearDAO('N', 'spg_dt_cmp');				
		$this->daoDetalleSpg->codemp     = $arrdetallespg['codemp'];
		$this->daoDetalleSpg->codestpro1 = $arrdetallespg['codestpro1'];
		$this->daoDetalleSpg->codestpro2 = $arrdetallespg['codestpro2'];
		$this->daoDetalleSpg->codestpro3 = $arrdetallespg['codestpro3'];
		$this->daoDetalleSpg->codestpro4 = $arrdetallespg['codestpro4'];
		$this->daoDetalleSpg->codestpro5 = $arrdetallespg['codestpro5'];
		$this->daoDetalleSpg->estcla     = $arrdetallespg['estcla'];
		$this->daoDetalleSpg->spg_cuenta = $arrdetallespg['spg_cuenta'];
	}
}
?>