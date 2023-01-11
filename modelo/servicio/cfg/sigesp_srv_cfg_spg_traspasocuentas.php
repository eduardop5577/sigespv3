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

$dirsrvcfg = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirsrvcfg."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once ($dirsrvcfg."/modelo/servicio/cfg/sigesp_srv_cfg_spg_itraspasocuentas.php");

class ServicioTraspasoCuentas implements ITraspasoCuentas
{
	private $daoCuenta;
	private $daoRegistroEvento;
	private $conexionBaseDatos;
	
	public function __construct()
	{
		$this->daoCuenta = null;
		$this->daoRegistroEvento = null;
		$this->valido = true;
		$this->mensaje = '';
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
	}

	public function traspasarCuentas($codemp, $arrjson)
	{
		$cuentaInc    = 0;
		$cuentaExi    = 0;
		$estOrigen    = $arrjson->estOrigen[0];
		$estDestino   = $arrjson->estDestino[0];
		$arrResultado = array();
		$strCuentaExi = '';
		
		DaoGenerico::iniciarTrans();
		$dataCuentas = $this->buscarCuentas($codemp, $estOrigen->codestpro1, $estOrigen->codestpro2, $estOrigen->codestpro3,$estOrigen->codestpro4, $estOrigen->codestpro5, $estOrigen->estcla);
		
		while (!$dataCuentas->EOF)
		{
			$spg_cuenta		=	$dataCuentas->fields["spg_cuenta"];
			$existeCuenta = $this->validarCuentaDestino($codemp, $estDestino->codestpro1, $estDestino->codestpro2, $estDestino->codestpro3,$estDestino->codestpro4, $estDestino->codestpro5, $estDestino->estcla,$spg_cuenta);
			if (!$existeCuenta)
			{
				$this->daoCuenta = FabricaDao::CrearDAO('N','spg_cuentas');
				$this->daoCuenta->codemp        =   $codemp;
				$this->daoCuenta->codestpro1    =	$estDestino->codestpro1;
				$this->daoCuenta->codestpro2    =	$estDestino->codestpro2;
				$this->daoCuenta->codestpro3    =	$estDestino->codestpro3;
				$this->daoCuenta->codestpro4    =	$estDestino->codestpro4;
				$this->daoCuenta->codestpro5    =	$estDestino->codestpro5;
				$this->daoCuenta->estcla    	=	$estDestino->estcla;
				$this->daoCuenta->spg_cuenta    =   $spg_cuenta;  
				$this->daoCuenta->denominacion	=	$dataCuentas->fields["denominacion"];
				$this->daoCuenta->status		=	$dataCuentas->fields["status"];
				$this->daoCuenta->sc_cuenta		=	$dataCuentas->fields["sc_cuenta"];
				$this->daoCuenta->nivel			=	$dataCuentas->fields["nivel"];
				$this->daoCuenta->referencia	=	$dataCuentas->fields["referencia"];
				$this->daoCuenta->scgctaint		=	$dataCuentas->fields["scgctaint"];
				if ($arrjson->inlcuirMonto)
				{
					$this->daoCuenta->asignado    = $dataCuentas->fields["asignado"];
		            $this->daoCuenta->distribuir  = $dataCuentas->fields["distribuir"];
		            $this->daoCuenta->enero       = $dataCuentas->fields["enero"];
		            $this->daoCuenta->febrero     = $dataCuentas->fields["febrero"];
		            $this->daoCuenta->marzo       = $dataCuentas->fields["marzo"];
		            $this->daoCuenta->abril       = $dataCuentas->fields["abril"];
		            $this->daoCuenta->mayo        = $dataCuentas->fields["mayo"];
		            $this->daoCuenta->junio       = $dataCuentas->fields["junio"];
		            $this->daoCuenta->julio       = $dataCuentas->fields["julio"];
		            $this->daoCuenta->agosto      = $dataCuentas->fields["agosto"];
		            $this->daoCuenta->septiembre  = $dataCuentas->fields["septiembre"];
		            $this->daoCuenta->octubre     = $dataCuentas->fields["octubre"];
		            $this->daoCuenta->noviembre   = $dataCuentas->fields["noviembre"];
		            $this->daoCuenta->diciembre   = $dataCuentas->fields["diciembre"];
				}
				else
				{
					$this->daoCuenta->asignado    = 0;
		            $this->daoCuenta->distribuir  = 0;
		            $this->daoCuenta->enero       = 0;
		            $this->daoCuenta->febrero     = 0;
		            $this->daoCuenta->marzo       = 0;
		            $this->daoCuenta->abril       = 0;
		            $this->daoCuenta->mayo        = 0;
		            $this->daoCuenta->junio       = 0;
		            $this->daoCuenta->julio       = 0;
		            $this->daoCuenta->agosto      = 0;
		            $this->daoCuenta->septiembre  = 0;
		            $this->daoCuenta->octubre     = 0;
		            $this->daoCuenta->noviembre   = 0;
		            $this->daoCuenta->diciembre   = 0;
				}
				
				$this->daoCuenta->precomprometido = 0;
  				$this->daoCuenta->comprometido    = 0;
  				$this->daoCuenta->causado         = 0;
  				$this->daoCuenta->pagado 		  = 0;
  				$this->daoCuenta->aumento 		  = 0;
  				$this->daoCuenta->disminucion     = 0;
				if ($this->daoCuenta->incluir())
				{
					$cuentaInc++;
				}
				else
				{
					break;
				}
			}
			else
			{
				$cuentaExi++;
				$strCuentaExi .= $spg_cuenta."- ";
			}
			unset($this->daoCuenta);
			$dataCuentas->MoveNext();
		}
		
		if (DaoGenerico::completarTrans())
		{
			$arrResultado[0] = $cuentaInc;
			$arrResultado[1] = $cuentaExi;
			$arrResultado[2] = $strCuentaExi;
		}
		return $arrResultado;
	}

	public function buscarCuentas($codemp, $codestpro1, $codestpro2, $codestpro3, $codestpro4, $codestpro5, $estcla)
	{
		$cadenaSQL = "SELECT * ".
					 "	FROM spg_cuentas ". 
					 " WHERE codemp='{$codemp}' ".
					 "	 AND codestpro1='{$codestpro1}' ".
					 "	 AND codestpro2='{$codestpro2}' ".
					 "	 AND codestpro3='{$codestpro3}' ".
					 "	 AND codestpro4='{$codestpro4}' ".
					 "	 AND codestpro5='".$codestpro5."' ".
					 "	 AND estcla='".$estcla."' ";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	
	public function validarCuentaDestino($codemp, $codestpro1, $codestpro2, $codestpro3, $codestpro4, $codestpro5, $estcla, $cuenta)
	{
		$existeCuenta = false;
		$cadenaSQL = "SELECT spg_cuenta ".
					 "	FROM spg_cuentas ". 
					 " WHERE codemp='{$codemp}' ".
					 "	 AND codestpro1='{$codestpro1}' ".
					 "	 AND codestpro2='{$codestpro2}' ".
					 "	 AND codestpro3='{$codestpro3}' ".
					 "	 AND codestpro4='{$codestpro4}' ".
					 "	 AND codestpro5='".$codestpro5."' ".
					 "	 AND estcla='".$estcla."' ".
					 "   AND spg_cuenta='".$cuenta."'";
		$dataSet = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($dataSet->_numOfRows > 0)
		{
			$existeCuenta = true;
		}
		unset($dataSet);
		return $existeCuenta;
	}	
}