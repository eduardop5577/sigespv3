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

require_once("../base/librerias/php/general/sigesp_lib_sql.php");

class sigesp_siv_c_configuracion
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;

   //---------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{
		require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		$in=new sigesp_include();
		$this->con=$in->uf_conectar();
//		$this->con->debug=true;
		$this->io_sql=    new class_sql($this->con);
		$this->seguridad= new sigesp_c_seguridad();
		$this->io_msg=    new class_mensajes();
		$this->io_funcion=new class_funciones();
		$this->io_fec= new class_fecha();
	}
   //---------------------------------------------------------------------------------------------------------------------------
	
   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_configuracion($as_metodo,$as_estcatsig,$as_estnum,$as_estcmp)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_configuracion
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_metodo     // metodo de inventario
		//                 $as_estcatsig  // estatus de uso del catalogo sigescof
		//                 $as_estnum     // estatus de la codificacion de los articulos
		//                 $as_estcmp     // estatus que indica si se desea autocompletar con ceros a la izquierda
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del metodo que esta registrado en la tabla siv_config
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 25/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT id, metodo, estcatsig, estnum, estcmp".
				"  FROM siv_config  ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_select_configuracion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_metodo=$row["metodo"];
				$as_estcatsig=$row["estcatsig"];
				$as_estnum=$row["estnum"];
				$as_estcmp=$row["estcmp"];
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado['as_metodo']=$as_metodo;
		$arrResultado['as_estcatsig']=$as_estcatsig;
		$arrResultado['as_estnum']=$as_estnum;
		$arrResultado['as_estcmp']=$as_estcmp;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end  function uf_siv_load_configuracion
   //---------------------------------------------------------------------------------------------------------------------------
	
   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_select_configuracion($as_id,$as_metodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_configuracion
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_id        // codigo del metodo
		//                 $as_metodo    // denominaci?n del metodo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del metodo que esta registrado en la tabla siv_config
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT * FROM siv_config  ".
				  " WHERE id='".$as_id."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_select_configuracion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;

	}// end  function uf_siv_select_configuracion

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_process_configuracion($as_codemp,$as_id,$as_metodo,$ai_estcatsig,$ai_estnum,$ai_estcmp,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_process_configuracion
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp    // codigo de empresa
		//                 $as_id        // codigo del metodo
		//                 $as_metodo    // denominaci?n del metodo
		//                 $ai_estcatsig // estatus de uso del catalogo SIGECOF
		//                 $ai_estnum    // estatus de uso del codigo de articulo
		//                 $ai_estcmp    // estatus que indica si se desea autocompletar con ceros a la izquierda
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion donde se procesa la configuracion del inventario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 03/10/2007							Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe= $this->uf_siv_select_configuracion($as_id,$as_metodo);
		if ($lb_existe)
		{
			$ld_date= date("d/m/Y");
			$lb_existe=$this->uf_siv_select_articulos($as_codemp);
			if($lb_existe)
			{
				$lb_existe=$this->uf_siv_select_movimientos($as_codemp);
				if($lb_existe)
				{
					$lb_pervalido=$this->io_fec->uf_valida_fecha_periodo($ld_date,$as_codemp);					
					if(!$lb_pervalido)
					{   $lb_valido=$this->uf_siv_procesar_configuracion($as_id,$as_metodo,$ai_estcatsig,$ai_estnum,$ai_estcmp,$aa_seguridad);
						if($lb_valido)
						{$this->io_msg->message("La configuraci?n de inventario fue actualizada.");}	
						else
						{$this->io_msg->message("La configuraci?n de inventario no pudo ser actualizada");}
					}
					else
					{$this->io_msg->message("Ya existe un metodo de Inventario para este periodo");}
						
				}
				else
				{
					$lb_valido=$this->uf_siv_procesar_configuracion($as_id,$as_metodo,$ai_estcatsig,$ai_estnum,$ai_estcmp,$aa_seguridad);
					if($lb_valido)
					{$this->io_msg->message("La configuraci?n de inventario fue actualizada.");}	
					else
					{$this->io_msg->message("La configuraci?n de inventario no pudo ser actualizada");}
				}
			}
			else
			{
				$lb_valido=$this->uf_siv_procesar_configuracion($as_id,$as_metodo,$ai_estcatsig,$ai_estnum,$ai_estcmp,$aa_seguridad);

				if($lb_valido)
				{$this->io_msg->message("La configuraci?n de inventario fue actualizada.");}	
				else
				{$this->io_msg->message("La configuraci?n de inventario no pudo ser actualizada");}
			}
		}
		else
		{
			$lb_valido=$this->uf_siv_procesar_configuracion($as_id,$as_metodo,$ai_estcatsig,$ai_estnum,$ai_estcmp,$aa_seguridad);
			if($lb_valido)
			{$this->io_msg->message("La configuraci?n de inventario fue actualizada.");}	
			else
			{$this->io_msg->message("La configuraci?n de inventario no pudo ser actualizada");}
		}
					
	}// end  function uf_process_configuracion
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_configuracion($as_id,$as_metodo,$ai_estcatsig,$ai_estnum,$ai_estcmp,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_configuracion
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_id        // codigo del metodo
		//                 $as_metodo    // denominaci?n del metodo
		//                 $ai_estcatsig // estatus de uso del catalogo SIGECOF
		//                 $ai_estnum    // estatus de uso del codigo de articulo
		//                 $ai_estcmp    // estatus que indica si se desea autocompletar con ceros a la izquierda
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un metodo de inventario en la tabla de siv_config. Solo debe existir un metodo 
	    //				   registrado en esta tabla.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 25/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();
		$ls_sql = "INSERT INTO siv_config (id,metodo,estcatsig,estnum,estcmp) ".
				  "VALUES(".$as_id.",'".$as_metodo."',".$ai_estcatsig.",".$ai_estnum.",".$ai_estcmp.")" ;
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_insert_configuracion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? la configuracion ".$as_metodo;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
		return $lb_valido;
	} // end function uf_siv_insert_configuracion
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_update_configuracion($as_id,$as_metodo,$ai_estcatsig,$ai_estnum,$ai_estcmp,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_configuracion
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_id        // codigo del metodo
		//                 $as_metodo    // denominaci?n del metodo
		//                 $li_estcatsig    // estatus de uso del catalogo SIGECOF
		//                 $ai_estnum    // estatus de uso del codigo de articulo
		//                 $ai_estcmp    // estatus que indica si se desea autocompletar con ceros a la izquierda
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica el metodo de inventario en la tabla de siv_config. Solo debe existir un metodo 
	    //				   registrado en esta tabla.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 25/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $this->io_sql->begin_transaction();
		 $ls_sql = "UPDATE siv_config".
		 		   "   SET metodo='". $as_metodo ."',".
				   "       estcatsig='". $ai_estcatsig ."',".
				   "       estnum='". $ai_estnum ."',".
				   "       estcmp='". $ai_estcmp ."'".
				   " WHERE id='" . $as_id ."'";
		 $li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_update_configuracion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? la configuracion ".$as_metodo;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end function uf_siv_update_configuracion
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_configuracion($as_id,$as_metodo,$ai_estcatsig,$ai_estnum,$ai_estcmp,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_configuracion
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_id        // codigo del metodo
		//                 $as_metodo    // denominaci?n del metodo
		//                 $ai_estcatsig    // estatus de uso del catalogo SIGECOF
		//                 $ai_estnum    // estatus de uso del codigo de articulo
		//                 $ai_estcmp    // estatus que indica si se desea autocompletar con ceros a la izquierda
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza el proceso de buscar el metodo de una configuraci?n, en caso de no existir ninguna
	    //                 insertarlo, caso contrario actualizarlo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_existe=false;
		$lb_existe=$this->uf_siv_select_configuracion($as_id,$as_metodo);
		if($lb_existe)
		{
			$lb_valido=$this->uf_siv_update_configuracion($as_id,$as_metodo,$ai_estcatsig,$ai_estnum,$ai_estcmp,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_insert_configuracion($as_id,$as_metodo,$ai_estcatsig,$ai_estnum,$ai_estcmp,$aa_seguridad);
		}			
		return $lb_valido;

	} // end function uf_siv_procesar_configuracion
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_select_articulos($as_codemp)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_articulos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp  // codigo de empresa
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existen articulos asociados a la empresa
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 07/06/2006							Fecha ?ltima Modificaci?n : 07/06/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codart".
				"  FROM siv_articulo  ".
				" WHERE codemp='".$as_codemp."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_select_articulos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;

	}// end  function uf_siv_select_articulos
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_select_movimientos($as_codemp)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_movimientos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp  // codigo de empresa
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existen movimientos de inventario  asociados a la empresa
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 25/07/2006							Fecha ?ltima Modificaci?n : 25/07/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT * FROM siv_movimiento";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_select_movimientos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;

	}// end  function uf_siv_select_movimientos
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_configuraciondespacho($as_codemp,$as_value)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_configuraciondespacho
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del estatus de contabilizacion de los despachos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT value".
		          "  FROM sigesp_config".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codsis='SIV'".
				  "   AND seccion='CONFIG'".
				  "   AND entry='CONTA DESPACHO'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_load_configuraciondespacho ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{				
				$as_value=$row["value"];
				$this->io_sql->free_result($rs_data);
				if(trim($as_value)!="")
					$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado['as_value']=$as_value;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end  function uf_siv_load_configuraciondespacho
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_configuraciondespacho($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_configuraciondespacho
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el estatus de la contabilizacion del despacho de inventario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();
		$this->ls_gestor=   $_SESSION["ls_gestor"];
		if($this->ls_gestor=="oci8po")
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','CONTA DESPACHO','-','".$as_value."')" ;
		}
		else
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','CONTA DESPACHO','','".$as_value."')" ;
		}
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_insert_configuraciondespacho ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el estatus de contabilizaci?n de despacho ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
		return $lb_valido;
	} // end function uf_siv_insert_configuraciondespacho
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_update_configuraciondespacho($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_configuraciondespacho
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica el metodo de inventario en la tabla de siv_config. Solo debe existir un metodo 
	    //				   registrado en esta tabla.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 25/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $this->io_sql->begin_transaction();
		 $ls_sql = "UPDATE sigesp_config".
		 		   "   SET value='".$as_value."'".
				   " WHERE codemp='".$as_codemp."'".
				   "   AND codsis='SIV'".
				   "   AND seccion='CONFIG'".
				   "   AND entry='CONTA DESPACHO'";
		 $li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_update_configuraciondespacho ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? el estatus de contabilizaci?n de despacho ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end function uf_siv_update_configuraciondespacho
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_configuraciondespacho($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_configuraciondespacho
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza el proceso de buscar el estatus de contabilizacion del despacho, en caso de 
	    //                 no existir ninguna insertarlo, caso contrario actualizarlo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2006							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_existe=false;
		$as_valueaux=$as_value;
		$arrResultado=$this->uf_siv_load_configuraciondespacho($as_codemp,$as_valueaux);
		$as_valueaux=$arrResultado['as_value'];
		$lb_existe=$arrResultado['lb_valido'];
		if($lb_existe)
		{
			$lb_valido=$this->uf_siv_update_configuraciondespacho($as_codemp,$as_value,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_insert_configuraciondespacho($as_codemp,$as_value,$aa_seguridad);
		}			
		return $lb_valido;

	} // end function uf_siv_procesar_configuraciondespacho
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_articulos_primarios($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_articulos_primarios
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza el proceso de buscar el estatus de contabilizacion del despacho, en caso de 
	    //                 no existir ninguna insertarlo, caso contrario actualizarlo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2006							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_valueaux=$as_value;
		$arrResultado=$this->uf_siv_load_articulos_primarios($as_codemp,$as_valueaux);
		$as_valueaux = $arrResultado['as_value'];
		$lb_existe=$arrResultado['lb_valido'];
		if($lb_existe)
		{
			$lb_valido=$this->uf_siv_update_articulos_primarios($as_codemp,$as_value,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_insert_articulos_primarios($as_codemp,$as_value,$aa_seguridad);
		}			
		return $lb_valido;

	} // end function uf_siv_procesar_configuraciondespacho
   //---------------------------------------------------------------------------------------------------------------------------
   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_articulos_primarios($as_codemp,$as_value)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_articulos_primarios
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del estatus de contabilizacion de los despachos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT value".
		          "  FROM sigesp_config".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codsis='SIV'".
				  "   AND seccion='CONFIG'".
				  "   AND entry='ARTICULO_PRI'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_load_articulos_primarios ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_value=$row["value"];
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado['as_value']=$as_value;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end  function uf_siv_load_articulos_primarios
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_articulos_primarios($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_articulos_primarios
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el estatus de la contabilizacion del despacho de inventario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();
		$this->ls_gestor=   $_SESSION["ls_gestor"];
		if($this->ls_gestor=="oci8po")
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','ARTICULO_PRI','-','".$as_value."')" ;
		}
		else
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','ARTICULO_PRI','','".$as_value."')" ;
		}
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_insert_articulos_primarios ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el estatus de contabilizaci?n de despacho ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
		return $lb_valido;
	} // end function uf_siv_insert_articulos_primarios
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_update_articulos_primarios($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_articulos_primarios
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica el metodo de inventario en la tabla de siv_config. Solo debe existir un metodo 
	    //				   registrado en esta tabla.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 25/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $this->io_sql->begin_transaction();
		 $ls_sql = "UPDATE sigesp_config".
		 		   "   SET value='".$as_value."'".
				   " WHERE codemp='".$as_codemp."'".
				   "   AND codsis='SIV'".
				   "   AND seccion='CONFIG'".
				   "   AND entry='ARTICULO_PRI'";
		 $li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_update_articulos_primarios ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? el estatus de contabilizaci?n de despacho ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end function uf_siv_update_articulos_primarios
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_centro_costos($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_centro_costos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza el proceso de buscar el estatus de contabilizacion del despacho, en caso de 
	    //                 no existir ninguna insertarlo, caso contrario actualizarlo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2006							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_valueaux=$as_value;
		$arrResultado=$this->uf_siv_load_centro_costos($as_codemp,$as_valueaux);
		$as_valueaux = $arrResultado['as_value'];
		$lb_existe=$arrResultado['lb_valido'];
		if($lb_existe)
		{
			$lb_valido=$this->uf_siv_update_centro_costos($as_codemp,$as_value,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_insert_centro_costos($as_codemp,$as_value,$aa_seguridad);
		}			
		return $lb_valido;

	} // end function uf_siv_procesar_configuraciondespacho
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_centro_costos($as_codemp,$as_value)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_centro_costos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del estatus de contabilizacion de los despachos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT value".
		          "  FROM sigesp_config".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codsis='SIV'".
				  "   AND seccion='CONFIG'".
				  "   AND entry='CENTRO_COSTOS'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_load_centro_costos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_value=$row["value"];
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado['as_value']=$as_value;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end  function uf_siv_load_centro_costos
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_centro_costos($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_centro_costos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el estatus de la contabilizacion del despacho de inventario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();
		$this->ls_gestor=   $_SESSION["ls_gestor"];
		if($this->ls_gestor=="oci8po")
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','CENTRO_COSTOS','-','".$as_value."')" ;
		}
		else
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','CENTRO_COSTOS','','".$as_value."')" ;
		}
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_insert_centro_costos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el estatus de Centro de Costos a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],

											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
		return $lb_valido;
	} // end function uf_siv_insert_centro_costos
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_update_centro_costos($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_centro_costos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica el metodo de inventario en la tabla de siv_config. Solo debe existir un metodo 
	    //				   registrado en esta tabla.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 25/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $this->io_sql->begin_transaction();
		 $ls_sql = "UPDATE sigesp_config".
		 		   "   SET value='".$as_value."'".
				   " WHERE codemp='".$as_codemp."'".
				   "   AND codsis='SIV'".
				   "   AND seccion='CONFIG'".
				   "   AND entry='CENTRO_COSTOS'";
		 $li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_update_centro_costos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? el estatus de Centro de Costos a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end function uf_siv_update_centro_costos
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_almacenes_produccion($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_centro_costos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza el proceso de buscar el estatus de contabilizacion del despacho, en caso de 
	    //                 no existir ninguna insertarlo, caso contrario actualizarlo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2006							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_valueaux=$as_value;
		$arrResultado=$this->uf_siv_load_almacenes_produccion($as_codemp,$as_valueaux);
		$as_valueaux = $arrResultado['as_value'];
		$lb_existe=$arrResultado['lb_valido'];
		if($lb_existe)
		{
			$lb_valido=$this->uf_siv_update_almacenes_produccion($as_codemp,$as_value,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_insert_almacenes_produccion($as_codemp,$as_value,$aa_seguridad);
		}			
		return $lb_valido;

	} // end function uf_siv_procesar_configuraciondespacho
   //---------------------------------------------------------------------------------------------------------------------------


   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_almacenes_produccion($as_codemp,$as_value)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_almacenes_produccion
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del estatus de contabilizacion de los despachos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT value".
		          "  FROM sigesp_config".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codsis='SIV'".
				  "   AND seccion='CONFIG'".
				  "   AND entry='PRODUCCION'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_load_almacenes_produccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_value=$row["value"];
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado['as_value']=$as_value;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;

	}// end  function uf_siv_load_centro_costos
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_almacenes_produccion($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_centro_costos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el estatus de la contabilizacion del despacho de inventario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();
		$this->ls_gestor=   $_SESSION["ls_gestor"];
		if($this->ls_gestor=="oci8po")
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','PRODUCCION','-','".$as_value."')" ;
		}
		else
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','PRODUCCION','','".$as_value."')" ;
		}
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_insert_almacenes_produccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el estatus de Almacen de Produccion a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],

											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
		return $lb_valido;
	} // end function uf_siv_insert_centro_costos
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_update_almacenes_produccion($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_centro_costos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica el metodo de inventario en la tabla de siv_config. Solo debe existir un metodo 
	    //				   registrado en esta tabla.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 25/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $this->io_sql->begin_transaction();
		 $ls_sql = "UPDATE sigesp_config".
		 		   "   SET value='".$as_value."'".
				   " WHERE codemp='".$as_codemp."'".
				   "   AND codsis='SIV'".
				   "   AND seccion='CONFIG'".
				   "   AND entry='PRODUCCION'";
		 $li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_update_almacenes_produccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? el estatus de Almacen de Produccion a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end function uf_siv_update_centro_costos
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_cuenta_contable($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_cuenta_contable
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza el proceso de buscar el estatus de contabilizacion del despacho, en caso de 
	    //                 no existir ninguna insertarlo, caso contrario actualizarlo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2006							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_valueaux=$as_value;
		$ls_descripcion="";
		$arrResultado=$this->uf_siv_load_cuenta_contable($as_codemp,$as_valueaux,$ls_descripcion);
		$as_valueaux = $arrResultado['as_value'];
		$ls_descripcion = $arrResultado['as_descripcion'];
		$lb_existe = $arrResultado['lb_valido'];
		if($lb_existe)
		{
			$lb_valido=$this->uf_siv_update_cuenta_contable($as_codemp,$as_value,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_insert_cuenta_contable($as_codemp,$as_value,$aa_seguridad);
		}			
		return $lb_valido;

	} // end function uf_siv_procesar_cuenta_contable
   //---------------------------------------------------------------------------------------------------------------------------


   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_cuenta_contable($as_codemp,$as_value,$as_descripcion)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_cuenta_contable
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del estatus de contabilizacion de los despachos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT value, ".
				  "      (SELECT denominacion FROM scg_cuentas".
				  "        WHERE sigesp_config.codemp=scg_cuentas.codemp".
				  "          AND trim(sigesp_config.value)=scg_cuentas.sc_cuenta) AS denominacion".
		          "  FROM sigesp_config".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codsis='SIV'".
				  "   AND seccion='CONFIG'".
				  "   AND entry='SCCUENTA_VENTA'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_load_cuenta_contable ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_value=$row["value"];
				$as_descripcion=$row["denominacion"];
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado['as_value']=$as_value;
		$arrResultado['as_descripcion']=$as_descripcion;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end  function uf_siv_load_cuenta_contable
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_cuenta_contable($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_cuenta_contable
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el estatus de la contabilizacion del despacho de inventario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();
		$this->ls_gestor=   $_SESSION["ls_gestor"];
		if($this->ls_gestor=="oci8po")
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','SCCUENTA_VENTA','-','".$as_value."')" ;
		}
		else
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','SCCUENTA_VENTA','','".$as_value."')" ;
		}
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_insert_cuenta_contable ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el estatus de Almacen de Produccion a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],

											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
		return $lb_valido;
	} // end function uf_siv_insert_cuenta_contable
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_update_cuenta_contable($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_cuenta_contable
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica el metodo de inventario en la tabla de siv_config. Solo debe existir un metodo 
	    //				   registrado en esta tabla.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 25/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $this->io_sql->begin_transaction();
		 $ls_sql = "UPDATE sigesp_config".
		 		   "   SET value='".$as_value."'".
				   " WHERE codemp='".$as_codemp."'".
				   "   AND codsis='SIV'".
				   "   AND seccion='CONFIG'".
				   "   AND entry='SCCUENTA_VENTA'";
		 $li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_update_cuenta_contable ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? el estatus de Almacen de Produccion a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end function uf_siv_update_cuenta_contable
   //---------------------------------------------------------------------------------------------------------------------------

    //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_almacenes_mercado($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_centro_costos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza el proceso de buscar el estatus de contabilizacion del despacho, en caso de 
	    //                 no existir ninguna insertarlo, caso contrario actualizarlo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2006							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_valueaux=$as_value;
		$arrResultado=$this->uf_siv_load_almacenes_mercado($as_codemp);
		$lb_existe=$arrResultado['lb_valido'];
		if($lb_existe)
		{
			$lb_valido=$this->uf_siv_update_almacenes_mercado($as_codemp,$as_value,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_insert_almacenes_mercado($as_codemp,$as_value,$aa_seguridad);
		}			
		return $lb_valido;

	} // end function uf_siv_procesar_configuraciondespacho
   //---------------------------------------------------------------------------------------------------------------------------
  //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_almacenes_mercado($as_codemp)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_almacenes_mercado
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del estatus de contabilizacion de los despachos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT value".
		          "  FROM sigesp_config".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codsis='SIV'".
				  "   AND seccion='CONFIG'".
				  "   AND entry='MERCADO'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_load_almacenes_mercado ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_value=$row["value"];
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado['as_value']=$as_value;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;

	}// end  function uf_siv_load_centro_costos
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_almacenes_mercado($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_almacenes_mercado
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el estatus de la contabilizacion del despacho de inventario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();
		$this->ls_gestor=   $_SESSION["ls_gestor"];
		if($this->ls_gestor=="oci8po")
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','MERCADO','-','".$as_value."')" ;
		}
		else
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','MERCADO','','".$as_value."')" ;
		}
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_insert_almacenes_mercado ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el estatus de Almacen de Mercado a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],

											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
		return $lb_valido;
	} // end function uf_siv_insert_almacenes_mercado
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_update_almacenes_mercado($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_almacenes_mercado
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica el metodo de inventario en la tabla de siv_config. Solo debe existir un metodo 
	    //				   registrado en esta tabla.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 25/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $this->io_sql->begin_transaction();
		 $ls_sql = "UPDATE sigesp_config".
		 		   "   SET value='".$as_value."'".
				   " WHERE codemp='".$as_codemp."'".
				   "   AND codsis='SIV'".
				   "   AND seccion='CONFIG'".
				   "   AND entry='MERCADO'";
		 $li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_update_almacenes_mercado ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? el estatus de Almacen de Mercado a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end function uf_siv_update_almacenes_mercado
   //---------------------------------------------------------------------------------------------------------------------------

    //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_tipoarticulo($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_tipoarticulo
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza el proceso de buscar el estatus de contabilizacion del despacho, en caso de 
	    //                 no existir ninguna insertarlo, caso contrario actualizarlo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2006							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_valueaux=$as_value;
		$arrResultado=$this->uf_siv_load_tipoarticulo($as_codemp);
		$as_valueaux = $arrResultado['as_value'];
		$lb_existe=$arrResultado['lb_valido'];
		if($lb_existe)
		{
			$lb_valido=$this->uf_siv_update_tipoarticulo($as_codemp,$as_value,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_insert_tipoarticulo($as_codemp,$as_value,$aa_seguridad);
		}			
		return $lb_valido;

	} // end function uf_siv_procesar_configuraciondespacho
   //---------------------------------------------------------------------------------------------------------------------------
   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_tipoarticulo($as_codemp)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_tipoarticulo
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del estatus de contabilizacion de los despachos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT value".
		          "  FROM sigesp_config".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codsis='SIV'".
				  "   AND seccion='CONFIG'".
				  "   AND entry='TIPOART'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_load_tipoarticulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_value=$row["value"];
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado['as_value']=$as_value;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;

	}// end  function uf_siv_load_centro_costos
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_tipoarticulo($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_almacenes_mercado
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el estatus de la contabilizacion del despacho de inventario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();
		$this->ls_gestor=   $_SESSION["ls_gestor"];
		if($this->ls_gestor=="oci8po")
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','TIPOART','-','".$as_value."')" ;
		}
		else
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','TIPOART','','".$as_value."')" ;
		}
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_insert_almacenes_mercado ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el tipo de Articulo para Mercados a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],

											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
		return $lb_valido;
	} // end function uf_siv_insert_almacenes_mercado
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_update_tipoarticulo($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_almacenes_mercado
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica el metodo de inventario en la tabla de siv_config. Solo debe existir un metodo 
	    //				   registrado en esta tabla.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 25/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $this->io_sql->begin_transaction();
		 $ls_sql = "UPDATE sigesp_config".
		 		   "   SET value='".$as_value."'".
				   " WHERE codemp='".$as_codemp."'".
				   "   AND codsis='SIV'".
				   "   AND seccion='CONFIG'".
				   "   AND entry='TIPOART'";
		 $li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_update_almacenes_mercado ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? tipo de Articulo para Mercado a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end function uf_siv_update_almacenes_mercado
   //---------------------------------------------------------------------------------------------------------------------------

    //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_detalles_articulos($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_detalles_articulos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza el proceso de buscar el estatus de contabilizacion del despacho, en caso de 
	    //                 no existir ninguna insertarlo, caso contrario actualizarlo.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2006							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_valueaux=$as_value;
		$arrResultado=$this->uf_siv_load_detalles_articulos($as_codemp);
		$as_valueaux = $arrResultado['as_value'];
		$lb_existe=$arrResultado['lb_valido'];
		if($lb_existe)
		{
			$lb_valido=$this->uf_siv_update_detalles_articulos($as_codemp,$as_value,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_insert_detalles_articulos($as_codemp,$as_value,$aa_seguridad);
		}			
		return $lb_valido;

	} // end function uf_siv_procesar_configuraciondespacho
   //---------------------------------------------------------------------------------------------------------------------------
  //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_detalles_articulos($as_codemp)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_detalles_articulos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del estatus de contabilizacion de los despachos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT value".
		          "  FROM sigesp_config".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codsis='SIV'".
				  "   AND seccion='CONFIG'".
				  "   AND entry='DETALLE_ARTICULOS'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_load_almacenes_mercado ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_value=$row["value"];
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado['as_value']=$as_value;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;

	}// end  function uf_siv_load_centro_costos
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_detalles_articulos($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_detalles_articulos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el estatus de la contabilizacion del despacho de inventario
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/01/2007							Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();
		$this->ls_gestor=   $_SESSION["ls_gestor"];
		if($this->ls_gestor=="oci8po")
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','DETALLE_ARTICULOS','-','".$as_value."')" ;
		}
		else
		{
			$ls_sql = "INSERT INTO sigesp_config (codemp,codsis,seccion,entry,type,value) ".
					  "VALUES('".$as_codemp."','SIV','CONFIG','DETALLE_ARTICULOS','','".$as_value."')" ;
		}
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_insert_almacenes_mercado ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el estatus de Almacen de Mercado a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],

											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
		return $lb_valido;
	} // end function uf_siv_insert_almacenes_mercado
   //---------------------------------------------------------------------------------------------------------------------------

   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_update_detalles_articulos($as_codemp,$as_value,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_detalles_articulos
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
	    //  			   $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica el metodo de inventario en la tabla de siv_config. Solo debe existir un metodo 
	    //				   registrado en esta tabla.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 10/02/2006							Fecha ?ltima Modificaci?n : 25/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $this->io_sql->begin_transaction();
		 $ls_sql = "UPDATE sigesp_config".
		 		   "   SET value='".$as_value."'".
				   " WHERE codemp='".$as_codemp."'".
				   "   AND codsis='SIV'".
				   "   AND seccion='CONFIG'".
				   "   AND entry='DETALLE_ARTICULOS'";
		 $li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->configuracion M?TODO->uf_siv_update_almacenes_mercado ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? el estatus de Almacen de Mercado a ".$as_value." para la empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end function uf_siv_update_almacenes_mercado
   //---------------------------------------------------------------------------------------------------------------------------


} // end class sigesp_siv_c_configuracion
?>
