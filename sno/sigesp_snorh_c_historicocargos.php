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

class sigesp_snorh_c_historicocargos
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_fun_nomina;
	var $io_fideiconfigurable;
	var $io_personal;
	var $io_sno;
	var $ls_codemp;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_snorh_c_historicocargos
		//		   Access: public (sigesp_snorh_d_historicocargos)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/03/2018 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad=new sigesp_c_seguridad();
		require_once("class_folder/class_funciones_nomina.php");
		$this->io_fun_nomina=new class_funciones_nomina();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}// end function sigesp_snorh_c_historicocargos
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_snorh_d_historicocargos)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/03/2018 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fun_nomina);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_historicocargos($as_codper,$ad_fecmov,$as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_historicocargos
		//		   Access: private
		//   	Arguments: as_codper  // Código del Personal
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si el Sueldo Historico está registrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/03/2018 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codper ".
				"  FROM sno_historicocargo ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND codnom='".$as_codnom."'".
				"   AND fecmov='".$ad_fecmov."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Histórico Cargos MÉTODO->uf_select_historicocargos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($rs_data->EOF)
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_existe;
	}// end function uf_select_historicocargos
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_historicocargos($as_codper,$ad_fecmov,$as_codnom,$as_desnom,$as_codcar,$as_descar,$as_codasicar,$as_desasicar,$as_codtab,$as_destab,
	                    			   $as_codpas,$as_codgra,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_historicocargos
		//		   Access: private
		//	    Arguments: as_codper  // Código del Personal
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla de Histórico Cargos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/03/2018 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_historicocargo(codemp, fecmov, codnom, codper, codasicar, codtab, codgra, codpas, codcar, desnom, descar, desasicar, destab) ".
				"VALUES ('".$this->ls_codemp."','".$ad_fecmov."','".$as_codnom."','".$as_codper."','".$as_codasicar."','".$as_codtab."','".$as_codgra."',".
				"'".$as_codpas."','".$as_codcar."','".$as_desnom."','".$as_descar."','".$as_desasicar."','".$as_destab."')";
       	$this->io_sql->begin_transaction();
	   	$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Histórico Cargos MÉTODO->uf_insert_historicocargos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó el Histórico Cargos ".$as_codfid." asociado al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Histórico Cargos fue registrado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
        		$this->io_mensajes->message("CLASE->Histórico Cargos MÉTODO->uf_insert_historicocargos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_insert_historicocargos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_historicocargos($as_codper,$ad_fecmov,$as_codnom,$as_desnom,$as_codcar,$as_descar,$as_codasicar,$as_desasicar,$as_codtab,$as_destab,
	                    				$as_codpas,$as_codgra,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_historicocargos
		//		   Access: private
		//	    Arguments: as_codper  // Código del Personal
		//	      Returns: lb_valido True si se ejecuto el update ó False si hubo error en el update
		//	  Description: Funcion que actualiza en la tabla de Histórico Cargos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/03/2018 								Fecha Última Modificación : 		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_historicocargo ".
				"   SET codasicar='".$as_codasicar."', ".
				"       codtab='".$as_codtab."', ".
       			"       codgra='".$as_codgra."', ".
				"       codpas='".$as_codpas."', ".
				"       codcar='".$as_codcar."', ".
				"       desnom='".$as_desnom."', ".
				"       descar='".$as_descar."', ".
				"       desasicar='".$as_desasicar."', ".
       			"       destab='".$as_destab."' ". 
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND codnom='".$as_codnom."' ".
				" 	AND fecmov='".$ad_fecmov."'"; 
       	$this->io_sql->begin_transaction();
	   	$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Histórico Cargos MÉTODO->uf_update_historicocargos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó el Histórico Cargos asociado al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Histórico Cargos fue Actualizado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
        		$this->io_mensajes->message("CLASE->Histórico Cargos MÉTODO->uf_update_historicocargos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_update_historicocargos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($as_codper,$ad_fecmov,$as_codnom,$as_desnom,$as_codcar,$as_descar,$as_codasicar,$as_desasicar,$as_codtab,$as_destab,
	                    $as_codpas,$as_codgra,$as_existe,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public ()
		//	    Arguments: as_codper  // Código del Personal
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el grabar ó False si hubo error en el grabar
		//	  Description: Funcion que graba en la tabla de Cargos Históricos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/03/2018 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
		$ad_fecmov=$this->io_funciones->uf_convertirdatetobd($ad_fecmov);
		switch ($as_existe)
		{
			case "FALSE":
				if($this->uf_select_historicocargos($as_codper,$ad_fecmov,$as_codnom)===false)
				{
						$lb_valido=$this->uf_insert_historicocargos($as_codper,$ad_fecmov,$as_codnom,$as_desnom,$as_codcar,$as_descar,$as_codasicar,
																	$as_desasicar,$as_codtab,$as_destab,$as_codpas,$as_codgra,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("El Histórico del cargo ya existe, no lo puede incluir.");
				}
				break;
							
			case "TRUE":
				if(($this->uf_select_historicocargos($as_codper,$ad_fecmov,$as_codnom)))
				{
					$lb_valido=$this->uf_update_historicocargos($as_codper,$ad_fecmov,$as_codnom,$as_desnom,$as_codcar,$as_descar,$as_codasicar,
																$as_desasicar,$as_codtab,$as_destab,$as_codpas,$as_codgra,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("El Histórico del cargo no existe, no lo puede actualizar.");
				}
				break;
		}		
		return $lb_valido;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_sueldoshistorios($as_codper,$ad_fecmov,$as_codnom,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_sueldoshistorios
		//		   Access: public (sigesp_snorh_d_sueldoshistorios)
		//	    Arguments: as_codper  // Código del Personal
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete ó False si hubo error en el delete
		//	  Description: Funcion que elimina en la tabla de Histórico Cargos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/03/2018 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fecmov=$this->io_funciones->uf_convertirdatetobd($ad_fecmov);
		$ls_sql="DELETE ".
				"  FROM sno_historicocargo ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND codnom='".$as_codnom."'".
				"   AND fecmov='".$ad_fecmov."'";
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Histórico Cargos MÉTODO->uf_delete_historicocargos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Eliminó el Histórico Cargos asociado al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Histórico Cargos fue Eliminado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Histórico Cargos MÉTODO->uf_delete_historicocargos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
    }// end function uf_delete_sueldoshistorios
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>