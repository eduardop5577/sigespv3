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
require_once("../shared/class_folder/sigesp_c_seguridad.php");
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");

class sigesp_saf_c_sede
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;

	public function __construct()
	{
		$this->io_msg=new class_mensajes();
		$in=new sigesp_include();
		$this->con=$in->uf_conectar();
		$this->io_sql=new class_sql($this->con);
		$this->seguridad= new sigesp_c_seguridad();
		$this->io_funcion = new class_funciones();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->msg=new class_mensajes();
	}
	
	function uf_saf_select_sede($as_codsed)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_saf_select_catalogo
		//         Access: public 
		//      Argumento: $as_catalogo    // codigo de catalogo sigecof
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion busca una codificacion del catalogo SIGECOF en la tabla de  saf_catalogo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT * FROM saf_sede  ".
				  " WHERE codemp='".$this->ls_codemp."'".
				  "   AND codsed='".$as_codsed."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->sede MÉTODO->uf_saf_select_sede ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
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
			}
		}
			
		$this->io_sql->free_result($rs_data);
		return $lb_valido;

	}//fin de la function uf_saf_select_catalogo 

	function  uf_saf_insert_sede($as_codsed,$as_densed,$as_dirsed,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_saf_insert_sede
		//         Access: public 
		//      Argumento: $as_catalogo     // codigo de catalogo sigecof
		//                 $as_denominacion // denominacion del catalogo
		//                 $as_cuenta       // cuenta presupuestaria asociada
		//                 $aa_seguridad    // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion inserta un nuevo registro al catalogo SIGECOF
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();
		$ls_sql = "INSERT INTO saf_sede (codemp, codsed, densed, dirsed) ".
				  " VALUES ( '".$this->ls_codemp."','".trim($as_codsed)."','".$as_densed."','".$as_dirsed."' )" ;
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->sede MÉTODO->uf_saf_insert_sede ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();

		}
		else
		{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó la sede ".$as_codsed;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$this->io_sql->commit();
		}
		
		return $lb_valido;

	}//fin de la uf_saf_insert_catalogo

	function uf_saf_update_sede($as_codsed,$as_densed,$as_dirsed,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_saf_update_sede
		//         Access: public 
		//      Argumento: $as_catalogo     // codigo de catalogo sigecof
		//                 $as_denominacion // denominacion del catalogo
		//                 $as_cuenta       // cuenta presupuestaria asociada
		//                 $aa_seguridad    // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion actualiza un registro al catalogo SIGECOF
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=false;
		 $ls_sql = "UPDATE saf_sede SET   densed='". $as_densed ."', dirsed='". $as_dirsed ."' ".
				  "  WHERE codemp='".$this->ls_codemp."'".
				  "   AND codsed='".$as_codsed."'" ;
			$this->io_sql->begin_transaction();
			$li_row = $this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->sede MÉTODO->uf_saf_update_sede ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
				$this->io_sql->rollback();
			}
			else
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="UPDATE";
				$ls_descripcion ="Actualizó la sede".$as_codsed;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$this->io_sql->commit();
			}
		  return $lb_valido;
	}// fin uf_saf_update_catalogo

	function uf_saf_delete_sede($as_codsed,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_saf_delete_catalogo
		//         Access: public 
		//      Argumento: $as_catalogo     // codigo de catalogo sigecof
		//                 $aa_seguridad    // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion elimina un registro al catalogo SIGECOF
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$lb_existe=$this->uf_saf_select_movimientos($as_catalogo);
		if($lb_existe)
		{
			$this->io_msg->message("El registro tiene movimientos asociados");
		}
		else
		{
			$ls_sql = " DELETE FROM saf_sede".
					  " WHERE codsed= '".$as_codsed. "'"; 
			$this->io_sql->begin_transaction();	
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->catalogo MÉTODO->uf_saf_delete_catalogo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
				$this->io_sql->rollback();
			}
			else
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion ="Eliminó la Sede ".$as_catalogo;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////			
				$this->io_sql->commit();
			}
		}
		return $lb_valido;
	} //fin  uf_saf_delete_catalogo

	function uf_saf_select_movimientos($as_codsed)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_saf_select_activos
		//         Access: public 
		//      Argumento: $as_catalogo    // codigo de catalogo sigecof
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion verifica si hay activos asociados al renglon del catalogo SIGECOF
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT * FROM saf_movimiento  ".
				  " WHERE codemp='".$this->ls_codemp."'".
				  " AND codsed='".$as_codsed."'" ;
				 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->catalogo MÉTODO->uf_saf_select_activos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
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
	}//fin uf_saf_select_activos
	

}//fin sigesp_saf_c_sede
?>
