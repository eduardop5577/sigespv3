<?php
/***********************************************************************************
* @fecha de modificacion: 14/11/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_scv_c_cargafamiliar
{
	var $ls_sql;
	var $is_msg_error;
		
	public function __construct($conn)
	{
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->seguridad = new sigesp_c_seguridad();		  
		$this->io_funcion = new class_funciones();
		$this->io_sql= new class_sql($conn);
		$this->io_msg= new class_mensajes();		
	}
	
	function uf_scv_select_cargafamiliar($as_codemp,$as_codcar) 
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_select_categoria
		//	          Access:  public
		//	       Arguments:  $as_codemp    // c?digo de empresa.
		//        			   $as_codcat    // c?digo de categoria
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de verificar la existencia de una categoria
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  22/09/2006      
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$lb_valido=false;
		$ls_sql=" SELECT * FROM scv_cargafamiliar".
				"  WHERE codemp='".$as_codemp."'".
				"    AND codcar='".$as_codcar."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->sigesp_scv_c_cargafamiliar METODO->uf_scv_select_categoria ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$li_numrows=$this->io_sql->num_rows($rs_data);
			if($li_numrows>0)
			{
				$lb_valido=true;
			}
		}
		return $lb_valido;
	} // fin de la function uf_scv_select_categoria

	function uf_scv_insert_cargafamiliar($as_codemp,$as_codcar,$as_dencar,$as_porcar,$aa_seguridad) 
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_insert_cagafamiliar
		//	          Access:  public
		//	        Arguments  $as_codemp    // c?digo de empresa.
		//        			   $as_codcat    // c?digo de categoria
		//    			       $as_dencat    // denominaci?n de la categoria
		//     				   $aa_seguridad // arreglo de seguridad
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de insertar una nueva categoria de viaticos en la tabla scv_categorias
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  22/09/2006      
		////////////////////////////////////////////////////////////////////////////// 
		$lb_valido=false;
		$this->io_sql->begin_transaction();
		if($as_porcar=="")
			$as_porcar=0;
		$li_porcar=    str_replace(".","",$as_porcar);
		$li_porcar=    str_replace(",",".",$li_porcar);
		$ls_sql=" INSERT INTO scv_cargafamiliar (codemp,codcar,dencar,porcar)".
				"      VALUES ('".$as_codemp."','".$as_codcar."','".$as_dencar."',".$li_porcar.")";
		$rs_data=$this->io_sql->execute($ls_sql);
		if ($rs_data===false)		     
		{
			$this->io_sql->rollback();
			$this->io_msg->message("CLASE->sigesp_scv_c_cargafamiliar METODO->uf_scv_insert_cagafamiliar ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? la Carga Familiar ".$as_codcar." Asociada a la Empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////// 		     
			$lb_valido=true;
			$this->io_sql->commit();
		}
		return $lb_valido;
	} // fin de la function uf_scv_insert_categoria
	
	function uf_scv_update_cargafamiliar($as_codemp,$as_codcar,$as_dencar,$as_porcar,$aa_seguridad) 
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_update_cargafamiliar
		//	          Access:  public
		//	        Arguments  $as_codemp    // c?digo de empresa.
		//        			   $as_codcat    // c?digo de categoria
		//    			       $as_dencat    // denominaci?n de la categoria
		//     				   $aa_seguridad // arreglo de seguridad
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de modificar una categoria de viaticos en la tabla scv_categorias
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  22/09/2006      
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$this->io_sql->begin_transaction();
		if($as_porcar=="")
			$as_porcar=0;
		$li_porcar=    str_replace(".","",$as_porcar);
		$li_porcar=    str_replace(",",".",$li_porcar);
		$ls_sql=" UPDATE scv_cargafamiliar SET dencar='".$as_dencar."', porcar=".$li_porcar."".
				"  WHERE codemp='" .$as_codemp. "'".
				"    AND codcar='".$as_codcar."'";
		$rs_data = $this->io_sql->execute($ls_sql);
		if ($rs_data===false)
		{
			$this->io_sql->rollback();
			$this->io_msg->message("CLASE->sigesp_scv_c_cargafamiliar METODO->uf_scv_update_cargafamiliar; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? la Carga Familiar ".$as_codcar." Asociada a la Empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		     
			$this->io_sql->commit();
		}  		      
		return $lb_valido;
	} // fin de la function uf_scv_update_cargafamiliar
			
	function uf_scv_delete_cargafamiliar($as_codemp,$as_codcar,$aa_seguridad)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_delete_cargafamiliar
		//	          Access:  public
		//	       Arguments:  $as_codemp    // c?digo de empresa.
		//        			   $as_codcat    // c?digo de categoria
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de eliminar una categoria de la tabla scv_categorias
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  22/09/2006      
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$lb_valido= false;
		$lb_relacion=$this->uf_scv_check_relaciones($as_codemp,$as_codcat,"sno_personalnomina");
		if (!$lb_relacion)
		{
			$lb_relacion=$this->uf_scv_check_relaciones($as_codemp,$as_codcat,"scv_tarifas");
			if (!$lb_relacion)
			{
				$this->io_sql->begin_transaction();
				$ls_sql= " DELETE FROM scv_cargafamiliar".
						 "  WHERE codemp='".$as_codemp."'".
						 "    AND codcar='".$as_codcar."'";	    
				$rs_data=$this->io_sql->execute($ls_sql);
				if ($rs_data===false)
				{
					$this->io_sql->rollback();
					$this->io_msg->message("CLASE->sigesp_scv_c_categoria METODO->uf_scv_delete_cargafamiliar; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				}
				else
				{
					$lb_valido=true;
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="DELETE";
					$ls_descripcion ="Elimin? la Carga Familiar ".$as_codcar." Asociada a la Empresa ".$as_codemp;
					$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               ///////////////////////////// 		     
					$this->io_sql->commit();
				}
			}	  		 
		}
		return $lb_valido;
	}// fin de la function uf_scv_delete_cargafamiliar
	
	
	function uf_scv_check_relaciones($as_codemp,$as_codcat,$as_tabla)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_check_relaciones
		//	          Access:  public
		//	       Arguments:  $as_codemp    // c?digo de empresa.
		//        			   $as_codcat    // c?digo de categoria
		//        			   $as_tabla     // tabla para la busqueda
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de eliminar una categoria de la tabla scv_categorias
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  22/09/2006      
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$lb_valido=false;
/*		if($as_tabla=="sno_personalnomina")
		{
			$ls_sql="SELECT * FROM sno_personalnomina".
					" WHERE codemp='".$as_codemp."'".
					"   AND codclavia='".$as_codcat."'";
		}
		else
		{
			$ls_sql="SELECT * FROM scv_tarifas".
					" WHERE codemp='".$as_codemp."'".
					"   AND codcat='".$as_codcat."'";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->sigesp_scv_c_cargafamiliar METODO->uf_scv_check_relaciones ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$this->io_msg->message("La Categor?a de viaticos tiene registros asociados");
			}
			else
			{
				$lb_valido=false;
			}
		}
*/		return $lb_valido;	
	} //Fin de la function uf_scv_check_relaciones
	
} //Fin de la class sigesp_scv_c_misiones
?> 