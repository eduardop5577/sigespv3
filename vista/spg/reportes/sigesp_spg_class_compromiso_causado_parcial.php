<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_spg_class_compromiso_causado_parcial
{
	private $datemp;
	private $conexion;
	private $io_sql;

	public function __construct()
	{
		require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
		$in = new sigesp_include();
		$this->conexion = $in->uf_conectar();
		$this->io_sql = new class_sql($this->conexion);
		$this->datemp = $_SESSION["la_empresa"];
	}

	public function uf_obtener_compromisos($fecdes, $fechas)
	{
		$cadenaSql = "SELECT CMP.total,PMV.*,
       						CASE CMP.tipo_destino
								WHEN 'P'
	  								THEN 
            							CMP.cod_pro 
          							ELSE
            							CMP.ced_bene 
         					END AS codigo,
       						CASE CMP.tipo_destino
								WHEN 'P'
	  								THEN 
            							(SELECT nompro
											FROM rpc_proveedor RPC
                							WHERE RPC.codemp=PMV.codemp AND RPC.cod_pro=CMP.cod_pro)
          							ELSE
            							(SELECT nombene || apebene
											FROM rpc_beneficiario BEN
                							WHERE BEN.codemp=PMV.codemp AND BEN.ced_bene=CMP.ced_bene)
         					END AS nombre, CMP.tipo_destino 
						FROM spg_dt_cmp PMV 
							INNER JOIN sigesp_cmp CMP ON PMV.codemp=CMP.codemp AND PMV.procede=CMP.procede AND PMV.comprobante=CMP.comprobante AND
                             							 PMV.fecha=CMP.fecha  AND PMV.codban=CMP.codban AND  PMV.ctaban=CMP.ctaban 
							INNER JOIN spg_operaciones POP ON PMV.operacion=POP.operacion 
						WHERE PMV.codemp='{$this->datemp['codemp']}' AND
						      PMV.fecha BETWEEN '{$fecdes}' AND '{$fechas}' AND 
      						  POP.comprometer=1 AND 
        					  POP.causar=0 AND 
      						  POP.pagar=0
						ORDER BY  codigo, nombre, PMV.comprobante, PMV.fecha";//print $cadenaSql."<br /><br />";
		return $this->io_sql->select($cadenaSql);
	}

	public function uf_buscar_causado($procede, $documento, $spg_cuenta, $codestpro1, $estcla, $codestpro2, $codestpro3, $codestpro4, $codestpro5) {
		$resultado = array();
		$cadenaSql = "SELECT PMV.procede, PMV.comprobante as documento, PMV.monto, PMV.spg_cuenta, PMV.codestpro1, PMV.estcla, PMV.codestpro2, PMV.codestpro3, PMV.codestpro4, PMV.codestpro5
						FROM spg_dt_cmp PMV 
							INNER JOIN spg_operaciones POP ON PMV.operacion=POP.operacion 
						WHERE PMV.codemp='{$this->datemp['codemp']}' AND
      						  PMV.procede_doc='{$procede}' AND 
      						  PMV.documento='{$documento}' AND
      						  PMV.spg_cuenta='{$spg_cuenta}' AND
      						  PMV.codestpro1='{$codestpro1}' AND 
      						  PMV.estcla='{$estcla}' AND 
      						  PMV.codestpro2='{$codestpro2}' AND 
      						  PMV.codestpro3='{$codestpro3}' AND 
      						  PMV.codestpro4='{$codestpro4}' AND 
      						  PMV.codestpro5='{$codestpro5}' AND POP.comprometer=0 AND POP.causar=1 AND POP.pagar=0"; //print $cadenaSql."<br /><br />";
		$data_causado  = $this->io_sql->select($cadenaSql);
		$total_causado = 0;
		while(!$data_causado->EOF){
			$total_causado = $total_causado + $data_causado->fields['monto'];
			$data_causado->MoveNext();
		}
		$data_causado->MoveFirst();

		$resultado[0] = $total_causado;
		$resultado[1] = $data_causado;

		return $resultado;
	}

	public function uf_buscar_pagado($procede, $documento, $spg_cuenta, $codestpro1, $estcla, $codestpro2, $codestpro3, $codestpro4, $codestpro5) {
		$cadenaSql = "SELECT PMV.procede, PMV.comprobante as documento, PMV.monto, PMV.spg_cuenta, PMV.codestpro1, PMV.estcla, PMV.codestpro2, PMV.codestpro3, PMV.codestpro4, PMV.codestpro5
						FROM spg_dt_cmp PMV 
							INNER JOIN spg_operaciones POP ON PMV.operacion=POP.operacion 
						WHERE PMV.codemp='{$this->datemp['codemp']}' AND
      						  PMV.procede_doc='{$procede}' AND 
      						  PMV.documento='{$documento}' AND
      						  PMV.spg_cuenta='{$spg_cuenta}' AND
      						  PMV.codestpro1='{$codestpro1}' AND 
      						  PMV.estcla='{$estcla}' AND 
      						  PMV.codestpro2='{$codestpro2}' AND 
      						  PMV.codestpro3='{$codestpro3}' AND 
      						  PMV.codestpro4='{$codestpro4}' AND 
      						  PMV.codestpro5='{$codestpro5}' AND POP.comprometer=0 AND POP.causar=0 AND POP.pagar=1";
		return $this->io_sql->select($cadenaSql);
	}

	public function uf_buscar_anulado($procede, $documento, $spg_cuenta, $codestpro1, $estcla, $codestpro2, $codestpro3, $codestpro4, $codestpro5) {
		$ld_monto  = 0;
		$cadenaSql = "SELECT PMV.procede, PMV.documento as documento, PMV.monto, PMV.spg_cuenta, PMV.codestpro1, PMV.estcla, PMV.codestpro2, PMV.codestpro3, PMV.codestpro4, PMV.codestpro5
						FROM spg_dt_cmp PMV 
							INNER JOIN spg_operaciones POP ON PMV.operacion=POP.operacion 
						WHERE PMV.codemp='{$this->datemp['codemp']}' AND
      						   PMV.procede='SCBBAH' AND
						      PMV.procede_doc='{$procede}' AND 
      						  PMV.documento='{$documento}' AND
      						  PMV.spg_cuenta='{$spg_cuenta}' AND
      						  PMV.codestpro1='{$codestpro1}' AND 
      						  PMV.estcla='{$estcla}' AND 
      						  PMV.codestpro2='{$codestpro2}' AND 
      						  PMV.codestpro3='{$codestpro3}' AND 
      						  PMV.codestpro4='{$codestpro4}' AND 
      						  PMV.codestpro5='{$codestpro5}' AND POP.comprometer=0 AND POP.causar=0 AND POP.pagar=1";
		$data_anulado = $this->io_sql->select($cadenaSql);

		if(!$data_anulado->EOF){
			$ld_monto = $data_anulado->fields['monto'];
		}

		return $ld_monto;
	}

	public function uf_buscar_reverso_compromiso($procede, $documento, $spg_cuenta, $codestpro1, $estcla, $codestpro2, $codestpro3, $codestpro4, $codestpro5)
	{
		$ld_monto  = 0;
		
		$cadenaSql = "SELECT PMV.procede, PMV.documento as documento, PMV.monto, PMV.spg_cuenta, PMV.codestpro1, PMV.estcla, PMV.codestpro2, PMV.codestpro3, PMV.codestpro4, PMV.codestpro5
						FROM spg_dt_cmp PMV 
							INNER JOIN spg_operaciones POP ON PMV.operacion=POP.operacion 
						WHERE PMV.codemp='{$this->datemp['codemp']}' AND
      						   PMV.procede='SOCROC' AND
						      PMV.procede_doc='{$procede}' AND 
      						  PMV.documento='{$documento}' AND
      						  PMV.spg_cuenta='{$spg_cuenta}' AND
      						  PMV.codestpro1='{$codestpro1}' AND 
      						  PMV.estcla='{$estcla}' AND 
      						  PMV.codestpro2='{$codestpro2}' AND 
      						  PMV.codestpro3='{$codestpro3}' AND 
      						  PMV.codestpro4='{$codestpro4}' AND 
      						  PMV.codestpro5='{$codestpro5}' AND POP.comprometer=1 AND POP.causar=0 AND POP.pagar=0";
		$data_reverso = $this->io_sql->select($cadenaSql);

		if(!$data_reverso->EOF)
		{
			$ld_monto = $data_reverso->fields['monto'];
		}

		return $ld_monto;
	}

    public function uf_buscar_anulado_compromiso($procede, $documento, $spg_cuenta, $codestpro1, $estcla, $codestpro2, $codestpro3, $codestpro4, $codestpro5)
    {
        $ld_monto  = 0;
        $cadenaSql = "SELECT PMV.procede, PMV.documento as documento, PMV.monto, PMV.spg_cuenta, PMV.codestpro1, PMV.estcla, PMV.codestpro2, PMV.codestpro3, PMV.codestpro4, PMV.codestpro5 ".
                     "  FROM spg_dt_cmp PMV ".
                     " INNER JOIN spg_operaciones POP ".
                     "    ON PMV.operacion=POP.operacion ".
                     " WHERE PMV.codemp='{$this->datemp['codemp']}' ".
                     "   AND PMV.procede_doc='{$procede}'  ".
                     "   AND PMV.documento='{$documento}'  ".
                     "   AND PMV.spg_cuenta='{$spg_cuenta}'  ".
                     "   AND PMV.codestpro1='{$codestpro1}'  ".
                     "   AND PMV.estcla='{$estcla}'  ".
                     "   AND PMV.codestpro2='{$codestpro2}'  ".
                     "   AND PMV.codestpro3='{$codestpro3}'  ".
                     "   AND PMV.codestpro4='{$codestpro4}'  ".
                     "   AND PMV.codestpro5='{$codestpro5}'  ".
                     "   AND PMV.monto < 0  ".
                     "   AND POP.comprometer=1  ".
                     "   AND POP.causar=0  ".
                     "   AND POP.pagar=0";
        $data_anulado = $this->io_sql->select($cadenaSql);

        if(!$data_anulado->EOF)
        {
            $ld_monto = $data_anulado->fields['monto'];
        }

        return $ld_monto;
    }

}