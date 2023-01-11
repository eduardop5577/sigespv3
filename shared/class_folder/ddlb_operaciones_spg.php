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

class ddlb_operaciones_spg
{
	var $SQL;
	
	public function __construct($con)
	{
		$this->SQL=new class_sql($con);
	}
	
	function uf_cargar_ddlb_spg($ai_reservado,$as_seleccionado,$as_mov_operacion)
	{
		$ls_sql="SELECT operacion,denominacion,asignar,aumento,disminucion,precomprometer,comprometer,causar,pagar,reservado
			     FROM spg_operaciones
				 WHERE reservado = ".$ai_reservado." ORDER BY operacion";
//	print $ls_sql;
		$rs_operaciones=$this->SQL->select($ls_sql);
		
		if($rs_operaciones==false)
		{
		print "Error".$this->SQL->message;
		}
		else
		{
			print "<select name=ddlb_spg id=ddlb_spg style=width:120px>";
			while($row=$this->SQL->fetch_row($rs_operaciones))
			{
				 $as_operacion=trim($row["operacion"]);
				 if($as_mov_operacion=='ND')
				 {
					 if($as_operacion=='CCP')
					 {
						 if($as_seleccionado==0)
						 {
							 print "<option value=0 selected>".$row["denominacion"]."</option>";
						 }
						 else
						 {
							 print "<option value=0>".$row["denominacion"]."</option>";
						 }
					}
					elseif($as_operacion=='PG')
					{
						 if($as_seleccionado==1)
						 {
							 print "<option value=1 selected>".$row["denominacion"]."</option>";
						 }
						 else
						 {
							 print "<option value=1>".$row["denominacion"]."</option>";
						 }
					}
				}
				if($as_mov_operacion=='CH')
				 {
					 if($as_operacion=='PG')
					 {
						 if($as_seleccionado==1)
						 {
							 print "<option value=1 selected>".$row["denominacion"]."</option>";
						 }
						 else
						 {
							 print "<option value=1>".$row["denominacion"]."</option>";
						 }
					}
					if($as_operacion=='CCP')
					 {
						 if($as_seleccionado==0)
						 {
							 print "<option value=0 selected>".$row["denominacion"]."</option>";
						 }
						 else
						 {
							 print "<option value=0>".$row["denominacion"]."</option>";
						 }
					}
				}
			}
			print "</select>";
			$this->SQL->free_result($rs_operaciones);
		}
	}
}
?>
