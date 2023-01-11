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

class sigesp_snorh_c_tipoescuela
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
		//	     Function: sigesp_snorh_c_tipoescuela
		//		   Access: public (sigesp_snorh_d_tipoescuela)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/12/2019 								Fecha Última Modificación : 
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
	}// end function sigesp_snorh_c_metodobanco
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_snorh_d_tipoescuela)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/12/2019 								Fecha Última Modificación : 
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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_tipoescuela($as_codtipesc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_tipoescuela
		//		   Access: private
		//	    Arguments: as_codtipesc  // Código de Método
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si está registrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/12/2019 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codtipesc ".
			"  FROM sno_tipoescuela ".
			" WHERE codemp='".$this->ls_codemp."'".
			"   AND codtipesc='".$as_codtipesc."'";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Tipo Escuela MÉTODO->uf_select_tipoescuela ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	}// end function uf_select_tipoescuela
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_tipoescuela($as_codtipesc,$as_dentipesc,$as_escbol,$ai_tophor,$as_difacc,$as_medacc,
                                       $as_rural,$as_colnoc,$as_colesp,$as_colpen,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_tipoescuela
		//		   Access: private
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla sno_tipoescuela
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/12/2019 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_tipoescuela".
		        "(codemp,codtipesc,dentipesc,escbol,tophor,difacc,medacc,rural,colnoc,colesp,colpen) VALUES".
			"('".$this->ls_codemp."','".$as_codtipesc."','".$as_dentipesc."','".$as_escbol."',".$ai_tophor.",'".$as_difacc."',".
			"'".$as_medacc."','".$as_rural."','".$as_colnoc."','".$as_colesp."','".$as_colpen."')";
		$this->io_sql->begin_transaction()	;
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tipo Escuela MÉTODO->uf_insert_tipoescuela ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó el Tipo Escuela ".$as_codtipesc;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Tipo Escuela fue registrado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Tipo Escuela MÉTODO->uf_insert_tipoescuela ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_insert_tipoescuela
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_tipoescuela($as_codtipesc,$as_dentipesc,$as_escbol,$ai_tophor,$as_difacc,$as_medacc,
                                       $as_rural,$as_colnoc,$as_colesp,$as_colpen,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_tipoescuela
		//		   Access: private
		//	      Returns: lb_valido True si se ejecuto el update ó False si hubo error en el update
		//	  Description: Funcion que actualiza en la tabla sno_tipoescuela
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/12/2019 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_tipoescuela ".
                        "   SET dentipesc='".$as_dentipesc."', ".
                        "	escbol='".$as_escbol."', ".
                        "	tophor=".$ai_tophor.", ".
                        "	difacc='".$as_difacc."', ".
                        "	medacc='".$as_medacc."', ".
                        "       rural='".$as_rural."', ".
                        "	colnoc='".$as_colnoc."', ".
                        "	colesp='".$as_colesp."', ".
                        "       colpen='".$as_colpen."' ".
                        " WHERE codemp='".$this->ls_codemp."'".
                        "   AND codtipesc='".$as_codtipesc."'";
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tipo Escuela MÉTODO->uf_update_tipoescuela ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó el Tipo Escuela ".$as_codtipesc;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Tipo Escuela fue Actualizado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Tipo Escuela MÉTODO->uf_update_tipoescuela ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_update_tipoescuela
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($as_existe,$as_codtipesc,$as_dentipesc,$as_escbol,$ai_tophor,$as_difacc,$as_medacc,
                            $as_rural,$as_colnoc,$as_colesp,$as_colpen,$aa_seguridad)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_snorh_d_tipoescuela)
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Funcion que guarda en la tabla sno_tipoescuela
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/12/2019 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
                if ($ai_tophor=="")
                {
                    $ai_tophor=0;
                }
		switch ($as_existe)
		{
			case "FALSE":
				if(!($this->uf_select_tipoescuela($as_codtipesc)))
				{
					$lb_valido=$this->uf_insert_tipoescuela($as_codtipesc,$as_dentipesc,$as_escbol,$ai_tophor,$as_difacc,$as_medacc,
                                                                                $as_rural,$as_colnoc,$as_colesp,$as_colpen,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("El Tipo Escuela ya existe, no lo puede incluir.");
				}
				break;
							
			case "TRUE":
				if(($this->uf_select_tipoescuela($as_codtipesc)))
				{
					$lb_valido=$this->uf_update_tipoescuela($as_codtipesc,$as_dentipesc,$as_escbol,$ai_tophor,$as_difacc,$as_medacc,
                                                                                $as_rural,$as_colnoc,$as_colesp,$as_colpen,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("El Tipo Escuela no existe, no lo puede actualizar.");
				}
				break;
		}		
		return $lb_valido;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_tipoescuela($as_codtipesc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_tipoescuela
		//		   Access: public (sigesp_snorh_d_tipoescuela)
		//	      Returns: lb_valido True si se ejecuto el buscar ó False si hubo error en el buscar
		//	  Description: Funcion que busca en la tabla sno_tipoescuela
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/12/2019 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codubifis,desubifis ".
			"  FROM sno_ubicacionfisica ".
			" WHERE codemp='".$this->ls_codemp."'".
			"   AND codtipesc='".$as_codtipesc."'";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Tipo Escuela MÉTODO->uf_select_tipoescuela ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
                    if($rs_data->EOF)
                    {
                        $ls_sql="DELETE ".
                                "  FROM sno_tipoescuela ".
                                " WHERE codemp='".$this->ls_codemp."'".
                                "   AND codtipesc='".$as_codtipesc."'";

                        $this->io_sql->begin_transaction();
                        $li_row=$this->io_sql->execute($ls_sql);
                        if($li_row===false)
                        {
                                $lb_valido=false;
                                $this->io_mensajes->message("CLASE->Tipo Escuela MÉTODO->uf_delete_tipoescuela ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
                                $this->io_sql->rollback();
                        }
                        else
                        {
                                /////////////////////////////////         SEGURIDAD               /////////////////////////////
                                $ls_evento="DELETE";
                                $ls_descripcion ="Eliminó el Tipo Escuela ".$as_codtipesc;
                                $lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
                                                                                                $aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
                                                                                                $aa_seguridad["ventanas"],$ls_descripcion);
                                /////////////////////////////////         SEGURIDAD               /////////////////////////////	
                                if($lb_valido)
                                {	
                                        $this->io_mensajes->message("El Tipo Escuela fue Eliminado.");
                                        $this->io_sql->commit();
                                }
                                else
                                {
                                        $lb_valido=false;
                                        $this->io_mensajes->message("CLASE->Tipo Escuela MÉTODO->uf_delete_tipoescuela ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
                                        $this->io_sql->rollback();
                                }
                        }
                    }
                    else
                    {
                        $lb_valido=false;
                        $this->io_mensajes->message("El Tipo Escuela, no se puede eliminar, esta asociado a una Ubicación Física");                        
                    }
                }
		return $lb_valido;
    }// end function uf_delete_tipoescuela
	//-----------------------------------------------------------------------------------------------------------------------------------

}
?>