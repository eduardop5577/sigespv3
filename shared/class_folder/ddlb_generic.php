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

class ddlb_generic
{
	var $ia_value;
	var $ia_text;	
	var $is_selected;
	var $is_name;	
	var $is_accion; 
	var $ii_width;
	
	public function __construct($aa_value,$aa_text,$as_selected,$as_name,$as_accion,$ai_width)
	{
		$this->ia_value=$aa_value;
		$this->ia_text=$aa_text;
		$this->is_selected=$as_selected;
		$this->is_name=$as_name;				
		$this->is_accion=$as_accion;				
		$this->ii_width=$ai_width;				
	}
	
	function uf_cargar_combo()
	{
		$li_total=count((array)$this->ia_value);
		print "<select name=".$this->is_name."  id=".$this->is_name." style=width:".$this->ii_width."px ".$this->is_accion.">";
		print "<option value=--->---Seleccione---</option>";
		for($li_i=0;$li_i<$li_total;$li_i++)
		{
			$ls_valor=$this->ia_value[$li_i];
			$ls_text=$this->ia_text[$li_i];

			 if($this->is_selected==$ls_valor)
			 {
				 print "<option value=".$ls_valor." selected>".$ls_text."</option>";
			 }
			 else
			 {
				 print "<option value=".$ls_valor." >".$ls_text."</option>";
			 }
		}
		print "</select>";
	}
}
?>
