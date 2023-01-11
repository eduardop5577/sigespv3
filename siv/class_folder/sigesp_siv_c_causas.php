<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_siv_c_causas
{

 var $io_funcion;
 var $io_msgc;
 var $io_sql;
 var $datoemp;
 var $io_msg;

	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->seguridad=   new sigesp_c_seguridad();
		$this->io_funcion = new class_funciones();		
		$io_include = new sigesp_include();
		$io_connect = $io_include->uf_conectar();		
		$this->io_sql= new class_sql($io_connect);
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_causas($ls_codcau)
	{
	    //////////////////////////////////////////////////////////////////////////////////////////
		// Function:    - uf_select_causas
		// Parameters:  - $ls_codcau( Codigo de causas).		
		// Descripcion: - 
		//////////////////////////////////////////////////////////////////////////////////////////	
	
		$ls_sql="SELECT codcau ".
				"  FROM siv_causas ".
		        " WHERE codemp='".$this->ls_codemp."'".
				"   AND codcau='".$ls_codcau."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("Error en consulta ".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("Registro no Encontrado."); 
			}
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar_causas($ls_codcau,$ls_dencau,$ls_status,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////////////////
		// Function:    - uf_guardar_causas
		// Parameters:  
		// Descripcion: 
		//////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		if($ls_status=="C")
		{
			$lb_existe=$this->uf_select_causas($ls_codcau);
			if($lb_existe)
			{
				$lb_valido=$this->uf_update_causas($ls_codcau,$ls_dencau,$aa_seguridad);
			}
			else
			{
				$this->io_mensajes->message("No se Puede Actualizar el Registro."); 
			}
		
		}
		else
		{
			$lb_valido=$this->uf_insert_causas($ls_codcau,$ls_dencau,$aa_seguridad);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_causas($ls_codcau,$ls_dencau,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:	    uf_insert_causas
		// Access:			public
		//	Returns:		Boolean, Retorna true si procesa correctamente
		//	Description:	
		//  Fecha:          
		//	Autor:          
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="INSERT INTO siv_causas(codemp,codcau,dencau) ".
				"	VALUES('".$this->ls_codemp."','".$ls_codcau."','".$ls_dencau."') ";
		$this->io_sql->begin_transaction();	
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{			
			$this->io_sql->rollback();
			$this->io_mensajes->message("CLASE->causas MÉTODO->uf_insert_causas ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message)); 
			$this->io_mensajes->message("No se Puede Incluir el Registro."); 
		}
		else
		{
			$this->io_mensajes->message("Registro Incluido."); 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó el causas".$ls_codcau." Asociado a la Empresa ".$ls_codemp;
			$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$lb_valido=true;
			$this->io_sql->commit();
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_causas($ls_codcau,$ls_dencau,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:	    uf_update_causas
		// Access:			public
		//	Returns:		Boolean, Retorna true si procesa correctamente
		//	Description:	
		//  Fecha:          
		//	Autor:          	
		/////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="UPDATE siv_causas ".
				"   SET dencau='".$ls_dencau."'".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codcau='".$ls_codcau."'";
		$this->io_sql->begin_transaction();	
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{			
			$this->io_mensajes->message("No se Puede Actualizar el Registro."); 
			$this->io_msg->message("CLASE->causas MÉTODO->uf_update_causas ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			$this->io_mensajes->message("Registro Actualizado."); 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó el causas".$ls_codcau." Asociado a la Empresa ".$ls_codemp;
			$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$lb_valido=true;
			$this->io_sql->commit();
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_causas($ls_codcau,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////////////////
		// Function:    - uf_delete_causas
		// Parameters:  - 
		// Descripcion: - 
		//////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$lb_existe=$this->uf_select_asignacion($ls_codcau);
		if((!$lb_existe))
		{
			$ls_sql="DELETE FROM siv_causas ".
					" WHERE codemp='".$this->ls_codemp."'".
					"   AND codcau='".$ls_codcau."'";
			$this->io_sql->begin_transaction();
			$li_rows=$this->io_sql->execute($ls_sql);
			if($li_rows==false)
			{
				$lb_valido=false;
				$this->io_sql->rollback();
				$this->io_msg->message("CLASE->causas MÉTODO->uf_delete_causas ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message)); 
				$this->io_mensajes->message("No se Puede Eliminar el Registro."); 
			}
			else
			{
			$this->io_mensajes->message("Registro Eliminado."); 
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion ="Eliminó la unidad ".$ls_coduni." Asociado a la Empresa ".$ls_codemp;
				$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$this->io_sql->commit();
			}
		}
		else
		{
			$this->io_mensajes->message("El registro esta utilizado en otras tablas."); 
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_asignacion($ls_codcau)
	{
	    //////////////////////////////////////////////////////////////////////////////////////////
		// Function:    - uf_select_ente
		// Parameters:  - $ls_codente( Codigo de ente).		
		// Descripcion: - 
		//////////////////////////////////////////////////////////////////////////////////////////	
		$lb_valido=false;
		$ls_sql="SELECT codcau ".
				"  FROM siv_asignacion".
		        " WHERE codemp='".$this->ls_codemp."'".
				"   AND codcau='".$ls_codcau."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("Error en consulta ".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
			}
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	
}
?>
