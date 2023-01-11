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

class evaluarFormula
{
	public function __construct() 
	{
		
	}
	
	public function iif($condicional,$true,$false)
	{
		if(eval("return $condicional;"))
		{
			$return=doubleval($true);
		}
		else
		{
			$return=doubleval($false);
		}
		return $return;
	}


	public function evaluar($formula,$monto)
	{
		$form = str_replace("IIF","\$this->iif",$formula);
		$form = str_replace("\$LD_MONTO",$monto,$form);
		$result  = @eval("return $form;");
		if ($result===false)
		{
			$valido = false;
			return 0;
		}
		else
		{
			$valido = true;
		}
		$arrResultado['result']=$result;
		$arrResultado['valido']=$valido;
		return $arrResultado;
	}

	public function evaluarNomina($formula,$result)
	{
		$codconc="";
		if(array_key_exists("la_conceptopersonal",$_SESSION))
		{
			$codconc=$_SESSION["la_conceptopersonal"]["codconc"];
		}
		$form=str_replace("IIF","\$this->iif",$formula);
		$result=@eval("return $form;");
		if ($result===false)
		{
			$result=0;
			$valido=false;
			$this->io_msg->message("Fórmula Inválida ".$form." CONCEPTO ".$codconc);
		}
		else
		{
			if ($result>=0)
			{
				$result=doubleval($result);
				$valido=true;
			}
			else
			{
				$result=0;
				$valido=false;
				$this->io_msg->message("Fórmula Inválida ".$form." CONCEPTO ".$codconc);
			}
		}
	  $arrResultado['result']=$result;
	  $arrResultado['valido']=$valido;
	  return $arrResultado;
	}

	public function evaluarFormula($formula,$monto)
	{
		$form = str_replace("IIF","\$this->iif",$formula);
		$form = str_replace("\$LD_MONTO",doubleval($monto),$form);
		$result  = @eval("return $ls_form;");
		if ($result===false)
		{
			$result=0;
		}
		else
		{
			$result = doubleval($result);
		}
		return $result;
	}

	public function validarFormula($formula,$monto)
	{
		$form = str_replace("IIF","\$this->iif",$formula);
		$form = str_replace("\$LD_MONTO",doubleval($monto),$form);
		$result  = @eval("return $ls_form;");
		if ($result===false)
		{
			return -1;
		}
		else
		{
			$valido = true;
		}
		return $valido;
	}
}
?>