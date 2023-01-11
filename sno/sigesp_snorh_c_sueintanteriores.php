<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_snorh_c_sueintanteriores
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_snorh_c_sueintanteriores
		//		   Access: public (sigesp_snorh_d_profesion)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		require_once("sigesp_snorh_c_personal.php");
		$this->io_personal= new sigesp_snorh_c_personal();
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_snorh_d_trabajoanterior)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
        unset($this->ls_codemp);
      
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_sueldoanterior($as_codper,$as_hidano,$as_hidmes)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_trabajoanterior
		//		   Access: public (sigesp_snorh_d_trabajoanterior)
		//	    Arguments: as_codper // Código de Personal
		//			       ai_codtraant  // Código de trabajo anterior
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si el trabajo anterior está registrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codper ".
				"  FROM sno_sueintegral ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND anosue='".$as_hidano."' ". 
				"	AND messue='".$as_hidmes."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Sueldo Integral Anterior MÉTODO->uf_select_sueldoanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if(!$row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_existe;
	}// end function uf_select_sueldoanterior

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_sueldoanterior($as_codper,$as_hidano,$as_hidmes,$ai_suelbase,$ai_suelint,$ai_bonvac,$ai_bonfinanio,$ai_otrasig,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_trabajoanterior
		//		   Access: private
		//	    Arguments: as_codper // Código de Personal
		//			       ai_codtraant  // Código de trabajo anterior
		//			       as_emptraant  // empresa
		//			       as_codded  // Código de Dedicación
		//			       ai_dialab  // Días Laborados
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el trabajo anterior
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_sueintegral".
				"(codemp,codper,anosue,messue,suebase,sueint,bonvac,bonfinanio,otrasig)".
				" VALUES ('".$this->ls_codemp."','".$as_codper."','".$as_hidano."','".$as_hidmes."',".
				" ".$ai_suelbase.",".$ai_suelint.",".$ai_bonvac.",".$ai_bonfinanio.",".$ai_otrasig.")";
				
		$this->io_sql->begin_transaction()	;
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Sueldo Integral Anterior MÉTODO->uf_insert_sueldoanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó el Sueldo Integral Anterior asociada al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Sueldo Integral Anterior fue Registrado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
        		$this->io_mensajes->message("CLASE->Sueldo Integral Anterior MÉTODO->uf_insert_sueldoanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_insert_trabajoanterior
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_sueldoanterior($as_codper,$as_hidano,$as_hidmes,$ai_suelbase,$ai_suelint,$ai_bonvac,$ai_bonfinanio,$ai_otrasig,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_trabajoanterior
		//		   Access: private
		//	    Arguments: as_codper // Código de Personal
		//			       ai_codtraant  // Código de trabajo anterior
		//			       as_emptraant  // empresa
		//			       as_ultcartraant  // último cargo
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el update ó False si hubo error en el update
		//	  Description: Funcion que actualiza el estudio realizado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_sueintegral ".
				"   SET suebase=".$ai_suelbase.", ".
				"       sueint=".$ai_suelint.", ".
				"       bonvac=".$ai_bonvac.", ".
				"       bonfinanio=".$ai_bonfinanio.", ".
				"       otrasig=".$ai_otrasig." ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND anosue='".$as_hidano."'".
				"   AND messue='".$as_hidmes."'";
		
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{

			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Sueldo Integral Anterior MÉTODO->uf_update_sueldoanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó el Sueldo Integral Anterior asociada al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Sueldo Integral Anterior fue Actualizado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
        		$this->io_mensajes->message("CLASE->Sueldo Integral Anterior MÉTODO->uf_update_sueldoanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_update_trabajoanterior
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($as_existe,$as_codper,$as_hidano,$as_hidmes,$ai_suelbase,$ai_suelint,$ai_bonvac,$ai_bonfinanio,$ai_otrasig,$aa_seguridad)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_snorh_d_trabajoanterior)
		//	    Arguments: as_codper // Código de Personal
		//			       ai_codtraant  // Código de trabajo anterior
		//			       as_emptraant  // empresa
		//			       as_ultcartraant  // último cargo
		//			       ai_ultsuetraant  // último sueldo
		//			       ad_fecingtraant  // Fecha de ingreso del trabajo
		//			       ad_fecrettraant  // Fecha de Retiro del trabajo
		//			       as_emppubtraant  // Si la empresa fué pública
		//			       as_codded  // Código de Dedicación
		//			       ai_anolab  // Años Laborados
		//			       ai_meslab  // Meses Laborados
		//			       ai_dialab  // Días Laborados
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Funcion que actualiza el estudio realizado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ai_suelbase=str_replace(".","",$ai_suelbase);
		$ai_suelbase=str_replace(",",".",$ai_suelbase);
		$ai_suelint=str_replace(".","",$ai_suelint);
		$ai_suelint=str_replace(",",".",$ai_suelint);
		$ai_bonvac=str_replace(".","",$ai_bonvac);
		$ai_bonvac=str_replace(",",".",$ai_bonvac);
		$ai_bonfinanio=str_replace(".","",$ai_bonfinanio);
		$ai_bonfinanio=str_replace(",",".",$ai_bonfinanio);
		$ai_otrasig=str_replace(".","",$ai_otrasig);
		$ai_otrasig=str_replace(",",".",$ai_otrasig);					
		$lb_valido=false;		
		switch ($as_existe)
		{
			case "FALSE":
				if($this->uf_select_sueldoanterior($as_codper,$as_hidano,$as_hidmes)===false)
				{
						$lb_valido=$this->uf_insert_sueldoanterior($as_codper,$as_hidano,$as_hidmes,$ai_suelbase,$ai_suelint,$ai_bonvac,$ai_bonfinanio,$ai_otrasig,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("El Sueldo a registrar ya se encuentra en ese periodo , no lo puede incluir.");
				}
				break;
							
			case "TRUE":
				if(($this->uf_select_sueldoanterior($as_codper,$as_hidano,$as_hidmes)))
				{
					$lb_valido=$this->uf_update_sueldoanterior($as_codper,$as_hidano,$as_hidmes,$ai_suelbase,$ai_suelint,$ai_bonvac,$ai_bonfinanio,$ai_otrasig,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("El Sueldo no existe para esa fecha, no lo puede actualizar.");
				}
				break;
		}
		
		return $lb_valido;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_sueldoanterior($as_codper,$as_hidano,$as_hidmes,$ai_suelbase,$ai_suelint,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_trabajoanterior
		//		   Access: public (sigesp_snorh_d_trabajoanterior)
		//	    Arguments: as_codper // Código de Personal
		//			       ai_codtraant  // Código de trabajo anterior
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete ó False si hubo error en el delete
		//	  Description: Funcion que elimina el trabajo anterior
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE ".
				"  FROM sno_sueintegral ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND anosue='".$as_hidano."'".
				"   AND messue='".$as_hidmes."'";
       	$this->io_sql->begin_transaction();
	   	$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Sueldo Integral Anterior MÉTODO->uf_delete_sueldoanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Eliminó el Sueldo Integral Anterior asociada al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Sueldo Integral Anterior fue Eliminado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
        		$this->io_mensajes->message("CLASE->Sueldo Integral Anterior MÉTODO->uf_delete_sueldoanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
    }
	//-----------------------------------------------------------------------------------------------------------------------------------

	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
?>