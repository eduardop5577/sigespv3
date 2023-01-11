<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../shared/class_folder/sigesp_c_seguridad.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");

class sigesp_saf_c_catsudeban
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;

	public function __construct()
	{
		$this->io_msg=new class_mensajes();
		$this->dat_emp=$_SESSION["la_empresa"];
		$in=new sigesp_include();
		$this->con=$in->uf_conectar();
		$this->io_sql=new class_sql($this->con);
		$this->seguridad= new sigesp_c_seguridad();
		$this->io_funcion = new class_funciones();
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}//fin de la function sigesp_saf_c_metodos()
	
	function uf_saf_select_catsudeban($as_codigo)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  
		//	Access:    public
		//	Arguments:
		//  $as_codigo    // codigo de condicion del bien
		//	Returns:		$lb_valido-----> true: encontrado false: no encontrado
		//	Description:  Esta funcion busca una condicion en la tabla de  saf_conservacionbien
		//              
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=false;
		$ls_sql="SELECT codemp,codcat,dencat".
				"  FROM saf_conservacionbien  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codcat='".$as_codigo."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->CatSUDEBAN MÉTODO->uf_saf_select_catsudeban ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
			}
		}
		$this->io_sql->free_result($rs_data);
		return $lb_valido;
	}//fin de la function uf_saf_select_condicion


	function  uf_saf_insert_catsudeban($as_codigo,$as_denominacion,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_saf_insert_catsudeban
		//	Access:    public
		//	Arguments:
		//  $as_codigo    // codigo de condicion del bien
		//  as_denominacion // denominacion de la condicion del bien
		//  as_descripcion       // descricion de la condicion del bien
		//  aa_seguridad   // arreglo de registro de seguridad
		//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
		//              
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=false;
        $this->io_sql->begin_transaction();
		$ls_sql="INSERT INTO saf_catsudeban (codemp, codcat, dencat) ".
					" VALUES('".$this->ls_codemp."','".$as_codigo."','".$as_denominacion."')" ;	
		$rs_data=$this->io_sql->execute($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->CatSUDEBAN MÉTODO->uf_saf_insert_catsudeban ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó la categoria de SUDEBAN ".$as_codigo;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$this->io_sql->commit();
		}
		return $lb_valido;
	}//fin de la uf_saf_insert_catsudeban

	function uf_saf_update_catsudeban($as_codigo,$as_denominacion,$aa_seguridad) 
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_saf_update_catsudeban
		//	Access:    public
		//	Arguments:
		//  $as_codigo      // codigo de condicion del bien
		//  as_denominacion // denominacion de la condicion del bien
		//  as_descripcion  // descricion de la condicion del bien
		//  aa_seguridad    // arreglo de registro de seguridad
		//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=false;
		$ls_sql=" UPDATE saf_catsudeban".
				"    SET dencat='". $as_denominacion ."'". 
				"  WHERE codemp='".$this->ls_codemp."'".
				"    AND codcat='".$as_codigo."'" ;
	 
        $this->io_sql->begin_transaction();
		$li_exec = $this->io_sql->execute($ls_sql);
		if($li_exec===false)
		{
			$this->io_msg->message("CLASE->CatSUDEBAN MÉTODO->uf_saf_update_catsudeban ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();

		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Modificó la categoria sudeban ".$as_codigo;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}

 
	  return $lb_valido;

	}// fin de la function uf_saf_update_catsudeban

	function uf_saf_delete_catsudeban($as_codigo,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_saf_delete_catsudeban
		//	Access:    public
		//	Arguments:
		//  $as_codigo     // codigo de condicion del bien
		//  aa_seguridad   // arreglo de registro de seguridad
		//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
		//	Description:  Esta funcion elimina una condicion en la tabla de  saf_conservacionbien
		//              
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=false;
		$lb_existe=$this->uf_saf_select_catsudebanactivo($as_codigo);
		if(!$lb_existe)
		{
			$ls_sql=" DELETE FROM saf_catsudeban".
					"  WHERE codemp='".$this->ls_codemp."'".
					"    AND codcat='".$as_codigo."'" ;
			$this->io_sql->begin_transaction();	
			$li_exec=$this->io_sql->execute($ls_sql);
			if($li_exec===false)
			{
				$this->io_msg->message("CLASE->CatSUDEBAN MÉTODO->uf_saf_delete_catsudeban ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
			else
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion ="Eliminó la Categoria sudeban ".$as_codigo;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////			
				$this->io_sql->commit();
			}
		}
		else
		{
			$this->io_msg->message("La categoria tiene Activos Asociados");
		}
		return $lb_valido;
	} //fin de uf_saf_delete_catsudeban
	function uf_saf_select_catsudebanactivo($as_codigo)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_saf_select_catsudebanactivo
		//	Access:    public
		//	Arguments:
		//  $as_codigo    // codigo de condicion del bien
		//	Returns:		$lb_valido-----> true: encontrado false: no encontrado
		//	Description:  Esta funcion busca una condicion en la tabla de  saf_conservacionbien
		//              
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=false;
		$ls_sql="SELECT codemp,codact".
				"  FROM saf_activo".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codcat='".$as_codigo."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->CatSUDEBAN MÉTODO->uf_saf_select_catsudebanactivo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
			}
		}
		$this->io_sql->free_result($rs_data);
		return $lb_valido;
	}//fin de la function uf_saf_select_catsudebanactivo


}//fin de la class sigesp_saf_c_catsudeban
?>
