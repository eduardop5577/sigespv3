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

class evaluate_formula
{
	var $io_msg;
	
	public function __construct()
	{
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_mensajes.php');
		$this->io_msg=new class_mensajes();
	}
	
	
	function iif($ad_condicional,$ad_true,$ad_false=0)
	{
		if(eval("return $ad_condicional;"))
		{
			$ad_return=doubleval($ad_true);
		}
		else
		{
			$ad_return=doubleval($ad_false);
		}
		return $ad_return;
	}
	
	
	function uf_evaluar($ls_formula,$ldec_monto,$lb_valido)
	{
	  $ls_form = str_replace("IIF","\$this->iif",$ls_formula);
	  $ls_form = str_replace("\$LD_MONTO",$ldec_monto,$ls_form);
	  $result  = @eval("return $ls_form;");
	  if ($result===false)
	     {
		   $lb_valido = false;
		   return 0;
		 }
	  else
	     {
	       $lb_valido = true;
		 }
	  $arrResultado['result']=$result;
	  $arrResultado['lb_valido']=$lb_valido;
	  return $arrResultado;
	}

	function uf_evaluar_nomina($ls_formula,$result)
	{
		$ls_codconc="";
		if(array_key_exists("la_conceptopersonal",$_SESSION))
		{
			$ls_codconc=$_SESSION["la_conceptopersonal"]["codconc"];
		}
		$ls_form=str_replace("IIF","\$this->iif",$ls_formula);
		$result=@eval("return $ls_form;");
		if ($result===false)
		   {
			 $result=0;
			 $lb_valido=false;
			 $this->io_msg->message("F?rmula Inv?lida ".$ls_form." CONCEPTO ".$ls_codconc);
		   }
		else
		   {
			 if ($result>=0)
			    {
				  $result=doubleval($result);
				  $lb_valido=true;
		 	    }
			 else
			    {
				  $result=0;
				  $lb_valido=false;
				  $this->io_msg->message("F?rmula Inv?lida ".$ls_form." CONCEPTO ".$ls_codconc);
			    }		
		   }
	  $arrResultado['result']=$result;
	  $arrResultado['lb_valido']=$lb_valido;
	  return $arrResultado;
	}

    function uf_evaluar_formula($ls_formula,$ldec_monto)
	{
	  $ls_form = str_replace("IIF","\$this->iif",$ls_formula);
	  $ls_form = str_replace("\$LD_MONTO",doubleval($ldec_monto),$ls_form);
	  $result  = @eval("return $ls_form;");
	  if ($result===false)
		 {
		   $result=0; 
                //$lb_valido = false;
		 }
	  else
	     {
		  $result = doubleval($result); 
                //$lb_valido = true;
		 }
	  return $result;
	}

 function uf_validar_formula($ls_formula,$ldec_monto)
 {
   $ls_form = str_replace("IIF","\$this->iif",$ls_formula);
   $ls_form = str_replace("\$LD_MONTO",doubleval($ldec_monto),$ls_form);
   $result  = @eval("return $ls_form;");
   if ($result===false)
 	  {
	    return -1;
      }
   else
      {
		$lb_valido = true;
	  }
   return $lb_valido;
 }
}	
?>