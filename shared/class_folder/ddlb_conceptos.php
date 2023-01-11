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

class ddlb_conceptos
{
	var $SQL;
	public function __construct($con)
	{
		$this->SQL=new class_sql($con);
	}
	
	function uf_cargar_conceptos($as_CodOpe,$as_seleccionado)
	{
		$ls_sql="SELECT codconmov,denconmov,codope
			     FROM scb_concepto
				 WHERE codope = '".$as_CodOpe."' OR codope='--' ORDER BY codconmov";
		$rs_conceptos=$this->SQL->select($ls_sql);
		
		if($rs_conceptos==false)
		{
			print "Error".$this->SQL->message;
			print "<select name=ddlb_conceptos style=width:200px>";
		    print "<option value=--->Ninguno</option>";
			print "</select>";
		}
		else
		{
			print "<select name=ddlb_conceptos style=width:200px onChange=javascript:uf_selectctaconcepto()>";
			while($row=$this->SQL->fetch_row($rs_conceptos))
			{
				 $as_operacion=$row["codconmov"];

				 if($as_seleccionado==$as_operacion)
				 {
					 print "<option value=".$row["codconmov"]." selected>".$row["denconmov"]."</option>";
				 }
				 else
				 {
					 print "<option value=".$row["codconmov"].">".$row["denconmov"]."</option>";
				 }
					
			}
			print "</select>";
			$this->SQL->free_result($rs_conceptos);
		}
	}
}
?>
