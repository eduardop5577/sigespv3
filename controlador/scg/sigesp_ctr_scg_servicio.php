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

require_once ("../../base/librerias/php/general/sigesp_lib_daogenerico.php");

class ServicioScg
{
	private $daogenerico;
	
	public function __construct($tabla)
	{
		$this->daogenerico = new DaoGenerico ( $tabla );
	}
	
	public function getCodemp() {
		return $this->daogenerico->codemp;
	}
	
	public function setCodemp($codemp) {
		
		$this->daogenerico->codemp = $codemp;
	}
	
	/***********************************/
	/* Metodos Estandar DAO Generico   */
	/***********************************/
	
	public static function iniTransaccion() {
		DaoGenerico::iniciarTrans ();
	}
	
	public static function comTransaccion() {
		return DaoGenerico::completarTrans ();
	}
	
	public function incluirDto($dto) {
		
		$this->pasarDatos ( $dto );
		$this->daogenerico->incluir ();
	}
	
	public function modificarDto($dto) {
		
		$this->pasarDatos ( $dto );
		$this->daogenerico->modificar ();
	}
	
	public function eliminarDto($dto) {
		
		$this->pasarDatos ( $dto );
		$this->daogenerico->eliminar ();
	}
	
	function pasarDatos($ObJson) {
		$arratributos = $this->daogenerico->getAttributeNames();
		foreach ( $arratributos as $IndiceDAO ) {
			foreach ( $ObJson as $IndiceJson => $valorJson ) {
				if ($IndiceJson == $IndiceDAO && $IndiceJson != "codemp") {
					$this->daogenerico->$IndiceJson = utf8_decode ( $valorJson );
				} else {
					$GLOBALS [$IndiceJson] = $valorJson;
				}
			}
		}
	}
	
	public function buscarTodos($campoorden="",$tipoorden=0) {
		
		return $this->daogenerico->leerTodos ($campoorden,$tipoorden);
	}
	
	public function buscarCampo($campo, $valor) {
		
		return $this->daogenerico->buscarCampo ( $campo, $valor );
	}
	
	public function buscarCampoRestriccion($restricciones)  {
		
		return $this->daogenerico->buscarCampoRestriccion($restricciones) ;
	}
	
	public function buscarSql($cadenasql)  {
		
		return $this->daogenerico->buscarSql($cadenasql) ;
	}
	
	public function obtenerPrimaryKey()
	{
		return $this->daogenerico->obtenerArregloPk();
	}
	
	public function concatenarSQL($arreglocadena)
	{
		return $this->daogenerico->concatenarCadena($arreglocadena);
	}
	/***************************************/
	/* Fin Metodos Estandar DAO Generico   */
	/***************************************/
	
	/***************************************/
	/* Metodos Asociados al Servicio       */
	/***************************************/
	public function buscarCuentasResultado() {
	
		$cadenasql = "";
			
		$arreglo[0]="(SELECT CASE esttipcont WHEN 1 THEN resultado ".
			        "    ELSE resultado_h ".
			        " END AS resultado FROM sigesp_empresa WHERE codemp = '".$this->daogenerico->codemp."') ";
		$arreglo[1]="'%'";
		
		$criterio = $this->concatenarSQL($arreglo);
		
		$cadenasql = "SELECT sc_cuenta, denominacion ".
					 " FROM scg_cuentas WHERE codemp= '".$this->daogenerico->codemp."'".
		             " AND sc_cuenta like ".$criterio." AND status = 'C' ORDER BY sc_cuenta";
		
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		
		return $resultado;
	
	}
	
	public function buscarCuentasSCG($codemp, $cuenta, $denominacion)
	{
		$cadenasql = "SELECT sc_cuenta, denominacion 
						FROM scg_cuentas 
						WHERE codemp='{$codemp}' 
						  AND sc_cuenta like '{$cuenta}%' 
						  AND denominacion like '%{$denominacion}%'  
						ORDER BY sc_cuenta";
		return $this->daogenerico->buscarSql ( $cadenasql );
	}
	
	public function buscarCuentasFinancieras()
	{
		$cadenasql = "SELECT sc_cuenta, denominacion ".
					 " FROM scg_cuentas WHERE codemp='".$this->daogenerico->codemp."'".
		             " AND sc_cuenta like '13%' ORDER BY sc_cuenta";
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		
		return $resultado;
	}
	
	public function buscarCuentasFiscales()
	{
		$cadenasql = "SELECT sc_cuenta, denominacion ".
					 " FROM scg_cuentas WHERE codemp='".$this->daogenerico->codemp."'".
		             " AND sc_cuenta like '21%' ORDER BY sc_cuenta";
		
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		
		return $resultado;
	}
	
	public function buscarCuentasSinMovimiento()
	{
		$criterio[0][0]="codemp";
		$criterio[0][1]="=";
		$criterio[0][2]=$this->daogenerico->codemp;
		$criterio[0][3]=0;
		$criterio[1][0]="status";
		$criterio[1][1]="=";
		$criterio[1][2]="S";
		$criterio[1][3]=2;
		$criterio[2][0]="sc_cuenta";
		$criterio[2][1]="ORDER BY";
		$criterio[2][2]="ASC";
		$criterio[2][3]=2;
		
		return $this->buscarCampoRestriccion($criterio);
	}
	
	public function buscarCuentasConMovimiento()
	{
		$criterio[0][0]="codemp";
		$criterio[0][1]="=";
		$criterio[0][2]=$this->daogenerico->codemp;
		$criterio[0][3]=0;
		$criterio[1][0]="status";
		$criterio[1][1]="=";
		$criterio[1][2]="C";
		$criterio[1][3]=2;
		$criterio[2][0]="sc_cuenta";
		$criterio[2][1]="ORDER BY";
		$criterio[2][2]="ASC";
		$criterio[2][3]=2;
		
		return $this->buscarCampoRestriccion($criterio);
	}
	public function buscarCuentasclasificadorEconomico()
	{
		$cadenasql = "SELECT codcuecla AS sc_cuenta, descuecla AS denominacion ".
                             " FROM sigesp_clasificador_economico WHERE codemp='".$this->daogenerico->codemp."'".
		             " ORDER BY codcuecla";
		
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		
		return $resultado;
	}
        
	public function buscarCuentasOncop()
	{
		$cadenasql = "SELECT sc_cuenta, denominacion ".
                             " FROM sigesp_plan_unico ".
		             " ORDER BY sc_cuenta";
		
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		
		return $resultado;
	}
        
	public function buscarCuentasConMovimientoSPG($cuentas)
	{
            $cadenacuenta = "";
            $cadenaoncop = "";
            $i=0;
            $cadenasql = "SELECT sc_cuenta, cueoncop ".
                         "  FROM scg_casa_presu ".
                         " WHERE sig_cuenta IN('".$cuentas."') ".
                         " ORDER BY sc_cuenta";
            $resultado = $this->daogenerico->buscarSql ( $cadenasql );
            while(!$resultado->EOF)
            {   $cuenta_oncop=uf_spg_cuenta_sin_cero($resultado->fields["cueoncop"]);
                if ($i==0)
                {
                  $cadenacuenta = $cadenacuenta." AND sc_cuenta IN ('".$resultado->fields["sc_cuenta"]."'";  
                  $cadenaoncop = $cadenaoncop." OR sc_cuenta LIKE '".$cuenta_oncop."%'";  
                }
                else
                {
                    $cadenacuenta = $cadenacuenta.",'".$resultado->fields["sc_cuenta"]."'";
                    $cadenaoncop = $cadenaoncop." OR sc_cuenta LIKE '".$cuenta_oncop."%'";  
                }
                $i++;
                $resultado->MoveNext();
            }
            if ($cadenacuenta <> "")
            {
               $cadenacuenta = $cadenacuenta.")";
            }
            $cadenasql = "SELECT sc_cuenta, denominacion ".
                         " FROM scg_cuentas WHERE codemp='".$this->daogenerico->codemp."'".
                         " AND status='C' ".
                         " ".$cadenacuenta." ".
                         " ".$cadenaoncop." ".
                         " ORDER BY sc_cuenta";
            $resultado = $this->daogenerico->buscarSql ( $cadenasql );

            return $resultado;
	}
        
	public function buscarCuentasConMovimientoSPI($cuentas)
	{
            $cadenacuenta = "";
            $cadenaoncop = "";
            $i=0;
		$cadenasql = "SELECT sc_cuenta, cueoncop ".
                             "  FROM scg_casa_presu ".
                             " WHERE sig_cuenta IN('".$cuentas."') ".
		             " ORDER BY sc_cuenta";
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		while(!$resultado->EOF)
		{   $cuenta_oncop=uf_spg_cuenta_sin_cero($resultado->fields["cueoncop"]);
                    if ($i==0)
                    {
                      $cadenacuenta = $cadenacuenta." AND sc_cuenta IN ('".$resultado->fields["sc_cuenta"]."'";  
                      $cadenaoncop = $cadenaoncop." OR sc_cuenta LIKE '".$cuenta_oncop."%'";  
                    }
                    else
                    {
                        $cadenacuenta = $cadenacuenta.",'".$resultado->fields["sc_cuenta"]."'";
                        $cadenaoncop = $cadenaoncop." OR sc_cuenta LIKE '".$cuenta_oncop."%'";  
                    }
                    $i++;
                    $resultado->MoveNext();
		}
                if ($cadenacuenta <> "")
                {
                   $cadenacuenta = $cadenacuenta.")";
                }
		$cadenasql = "SELECT sc_cuenta, denominacion ".
                             " FROM scg_cuentas WHERE codemp='".$this->daogenerico->codemp."'".
		             " AND status='C' ".
                             " ".$cadenacuenta." ".
                             " ".$cadenaoncop." ".
                             " ORDER BY sc_cuenta";
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		
		return $resultado;
	}
						
	public function buscarCuentasclasificadorEconomicoSPG($cuentas)
	{
            $cadenacuenta = "";
            $i=0;
		$cadenasql = "SELECT cueclaeco ".
                             "  FROM scg_casa_presu ".
                             " WHERE sig_cuenta IN('".$cuentas."') ".
		             " ORDER BY sc_cuenta";
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		while(!$resultado->EOF)
		{
                    if ($i==0)
                    {
                      $cadenacuenta = $cadenacuenta." AND codcuecla IN ('".$resultado->fields["cueclaeco"]."'";  
                    }
                    else
                    {
                        $cadenacuenta = $cadenacuenta.",'".$resultado->fields["cueclaeco"]."'";
                    }
                    $i++;
                    $resultado->MoveNext();
		}
                if ($cadenacuenta <> "")
                {
                   $cadenacuenta = $cadenacuenta.")";
                }
		$cadenasql = "SELECT codcuecla AS sc_cuenta, descuecla AS denominacion ".
                             " FROM sigesp_clasificador_economico WHERE codemp='".$this->daogenerico->codemp."'".
                             " ".$cadenacuenta." ".
		             " ORDER BY codcuecla";
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		
		return $resultado;
	}
	
        public function buscarCuentasclasificadorEconomicoSPI($cuentas)
	{
            $cadenacuenta = "";
            $i=0;
		$cadenasql = "SELECT cueclaeco ".
                             "  FROM scg_casa_presu ".
                             " WHERE sig_cuenta IN('".$cuentas."') ".
		             " ORDER BY sc_cuenta";
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		while(!$resultado->EOF)
		{
                    if ($i==0)
                    {
                      $cadenacuenta = $cadenacuenta." AND codcuecla IN ('".$resultado->fields["cueclaeco"]."'";  
                    }
                    else
                    {
                        $cadenacuenta = $cadenacuenta.",'".$resultado->fields["cueclaeco"]."'";
                    }
                    $i++;
                    $resultado->MoveNext();
		}
                if ($cadenacuenta <> "")
                {
                   $cadenacuenta = $cadenacuenta.")";
                }
		$cadenasql = "SELECT codcuecla AS sc_cuenta, descuecla AS denominacion ".
                             " FROM sigesp_clasificador_economico WHERE codemp='".$this->daogenerico->codemp."'".
                             " ".$cadenacuenta." ".
		             " ORDER BY codcuecla";
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		
		return $resultado;
	}
	
	public function buscarMonedas()
	{
		$cadenasql = "SELECT codmon, denmon, estatuspri".
					 " FROM sigesp_moneda WHERE codmon<>'---' ".
					 "ORDER BY denmon";
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		
		return $resultado;
	}
	/***************************************/
	/* Fin Metodos Asociados al Servicio   */
	/***************************************/	
}

?>