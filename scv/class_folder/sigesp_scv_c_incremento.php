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

class sigesp_scv_c_incremento
{
	var $ls_sql;
	var $ds_dtregion;
	
	public function __construct($conn)
	{
		require_once("../shared/class_folder/sigesp_c_seguridad.php");	  
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");		  
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php"); 
		$this->seguridad= new sigesp_c_seguridad();		
		$this->io_funcion= new class_funciones();
		$this->io_sql= new class_sql($conn);
		$this->io_msg= new class_mensajes();
        $this->io_database  = $_SESSION["ls_database"];
		$this->io_gestor    = $_SESSION["ls_gestor"];
		$this->ls_codemp= $_SESSION["la_empresa"]["codemp"];
		$this->io_funciondb= new class_funciones_db($conn);
		require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
	} // fin de la function sigesp_scv_c_regiones

	function uf_insert_incremento($as_codinc,$as_codreg,$as_codmis,$as_deninc,$ar_grid,$ai_total,$aa_seguridad) 
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_insert_region
		//	          Access:  public
		// 	       Arguments:  $as_codemp    // C�digo de la Empresa.  
		//        			   $as_codreg    // C�digo de la Regi�n.
		//        			   $as_codpai    // C�digo del Pa�s al cual pertenece la Regi�n.
		//        			   $as_denreg    // Denominaci�n de la Regi�n.
		//   				   $ar_grid      // Objeto grid de donde insertaremos los detalles.
		//         			   $ai_total     // Total de filas del grid de Detalles de Estados.
		//     				   $aa_seguridad // Arreglo de Seguridad cargado con la informaci�n de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de insertar una nueva modalidad en la tabla scv_regiones. 
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  23/06/2006
		//    Modificado Por:  Ing. Luis Anibal Lang
		//    Fecha de Modif:  19/09/2006      
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_codincaux = $as_codinc;
		$arrResultado= $this->io_keygen->uf_verificar_numero_generado("SCV","scv_incremento","codinc","",4,"","","",$as_codinc);
		$as_codinc=$arrResultado['as_numero'];
		$lb_valido=$arrResultado['lb_valido'];
//		$as_codreg=$this->io_funciondb->uf_generar_codigo(true,$as_codemp,'scv_regiones','codreg',$as_codpai);
		$this->io_sql->begin_transaction();
		$ls_sql=" INSERT INTO scv_incremento (codemp, codinc, codregori, codmis, deninc)".
				"      VALUES ('".$this->ls_codemp."','".$as_codinc."','".$as_codreg."','".$as_codmis."','".$as_deninc."')";
		$rs_data = $this->io_sql->execute($ls_sql);
		if ($rs_data===false)
		{
			$this->io_sql->rollback();
			if($this->io_sql->errno=='23505' || $this->io_sql->errno=='1062' || $this->io_sql->errno=='-239' || $this->io_sql->errno=='-5'|| $this->io_sql->errno=='-1')
			{
				$lb_valido=$this->uf_insert_incremento($as_codinc,$as_codreg,$as_codmis,$as_deninc,$ar_grid,$ai_total,$aa_seguridad);
			}
			else
			{
				$this->io_msg->message("CLASE->SIGESP_SCV_C_INCREMENTO; METODO->uf_insert_incremento; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			}
		}
		else
		{
			if ($this->uf_insert_dt_incremento($as_codinc,$ar_grid,$ai_total,$aa_seguridad))
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               ////////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion =" Insert�  el Incremento ".$as_codinc." de la region ".$as_codreg." Asociada a la empresa ".$this->ls_codemp;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
										$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
										$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               ////////////////////////////////	
				$this->io_sql->commit();
				if($ls_codincaux!=$as_codinc)
				{
					$this->io_msg->message("Se Asigno el C�digo de Incremento: ".$as_codinc);
				}
			}
		}
		return $lb_valido;
	} // fin de la function sigesp_scv_c_regiones
	
	function uf_insert_dt_incremento($as_codinc,$ar_grid,$ai_total,$aa_seguridad)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Funcion:  uf_insert_dt_incremento
		//	          Access:  public
		// 	       Arguments:  $as_codemp    //  C�digo de la Empresa.
		//        			   $as_codpai    //  C�digo del Pais.
		//                     $ar_grid      //  Arreglo cargado con los estados que ser�n insertados para una Regi�n.
		//                     $ai_total     //  Variable que contiene la cantidad de estados que van a ser insertados a la Regi�n.
		//                     $aa_seguridad //  Arreglo de Seguridad cargado con la informaci�n de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de insertar detalles para una modalidad en la tabla soc_dtm_clausulas. 
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  20/02/2006  
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		for ($li_i=1;$li_i<=$ai_total;$li_i++)
		{
			if ($lb_valido)
			{
				$ls_codregdes = $ar_grid["codregdes"][$li_i];    
				$ls_porinc = $ar_grid["porinc"][$li_i];
				if($ls_porinc=="")
					$ls_porinc=0;
				$li_porinc=    str_replace(".","",$ls_porinc);
				$li_porinc=    str_replace(",",".",$li_porinc);
				if (!empty($ls_codregdes))			            
				{
					$ls_sql=" INSERT INTO scv_dt_incremento (codemp, codinc, codregdes, porinc) ".
							"      VALUES ('".$this->ls_codemp."','".$as_codinc."','".$ls_codregdes."',".$li_porinc.")";
					$rs_data = $this->io_sql->execute($ls_sql);              
					if ($rs_data===false)
					{				 
						$this->io_sql->rollback();
						$this->io_msg->message("CLASE->SIGESP_SCV_C_INCREMENTO; METODO->uf_insert_dt_incremento; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
					}
					else
					{				 
						$lb_valido=true;  		                    
						/////////////////////////////////         SEGURIDAD               /////////////////////////////		
						$ls_evento      ="INSERT";
						$ls_descripcion =" Insert� el detalle de incremento ".$as_codinc." - ".$ls_codregdes." asociado a la Empresa ".$this->ls_codemp;
						$ls_variable    = $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
						$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
						$aa_seguridad["ventanas"],$ls_descripcion);
						/////////////////////////////////         SEGURIDAD               ///////////////////////////// 
					}  				
				}
			}
		} 
		return $lb_valido;
	} // fin de la function uf_insert_dt_region
	
	function uf_update_incremento($as_codinc,$as_codreg,$as_codmis,$as_deninc,$ar_grid,$ai_total,$aa_seguridad) 
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_update_incremento
		//	          Access:  public
		// 	       Arguments:  $as_codemp    //  C�digo de la Empresa.
		//       			   $as_codpai    //  C�digo del Pais.
		//                     $ar_grid      //  Arreglo cargado con los estados que ser�n insertados para una Regi�n.
		//                     $ai_total     //  Variable que contiene la cantidad de estados que van a ser insertados a la Regi�n.
		//                     $aa_seguridad // Arreglo de Seguridad cargado con la informaci�n de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de actualizar los datos de una modalidad en la tabla scv_regiones.  
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  20/02/2006     
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
		$lb_valido= false;
		$this->io_sql->begin_transaction();
		$ls_sql= "UPDATE scv_incremento".
				 "   SET codregori='".$as_codreg."',".
				 "       codmis='".$as_codmis."',".
				 "       deninc='".$as_deninc."'".
				 " WHERE codemp='".$this->ls_codemp."'".
				 "   AND codinc='".$as_codinc."'";
		$rs_data = $this->io_sql->execute($ls_sql);
		if ($rs_data===false)
		{
			$this->io_sql->rollback();
			$this->io_msg->message("CLASE->SIGESP_SCV_C_REGIONES; METODO->uf_update_incremento; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ($this->uf_delete_dt_incremento($as_codinc,$aa_seguridad))//Eliminar todos los estados asociados a una                                                                                   regi�n.
			{                  
				if ($this->uf_insert_dt_incremento($as_codinc,$ar_grid,$ai_total,$aa_seguridad))
				{                        
					$lb_valido=true;
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Actualiz� el Incremento ".$as_codinc." Asociada a la empresa ".$this->ls_codemp;
					$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////
					$this->io_sql->commit();
				}
			}
		}
		return $lb_valido;
	} // fin de la function uf_update_incremento
	
	function uf_delete_incremento($as_codinc,$aa_seguridad)
	{          		 
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_delete_region
		//	          Access:  public
		// 	       Arguments:  $as_codemp    // C�digo de la Empresa.
		//       			   $as_codpai    // C�digo del Pais.
		//       			   $as_codreg    // C�digo de la Regi�n. 
		//     				   $aa_seguridad // Arreglo de Seguridad cargado con la informaci�n de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de eliminar una modalidad en la tabla scv_regiones.  
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  20/02/2006 
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////  
		$lb_valido=false;
		if ($this->uf_delete_dt_incremento($as_codinc,$aa_seguridad))  
		{
			$ls_sql= " DELETE FROM scv_incremento".
					 "  WHERE codemp='".$this->ls_codemp."'".
					 "    AND codinc='".$as_codinc."'";	 
			$rs_data= $this->io_sql->execute($ls_sql);
			if ($rs_data===false)
			{
				$this->io_sql->rollback();
				$this->io_msg->message("CLASE->SIGESP_SCV_C_REGIONES; METODO->uf_delete_region; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			}
			else
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="DELETE";
				$ls_descripcion ="Elimin� el Incremento ".$as_codinc." Asociada a la empresa ".$this->ls_codemp;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$this->io_sql->commit();
			}
		}
		return $lb_valido;
	} // fin de la function uf_delete_region
	
	function uf_delete_dt_incremento($as_codinc,$aa_seguridad)
	{          		 
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_delete_dt_incremento
		//	          Access:  public
		// 	       Arguments:  $as_codemp    // C�digo de la Empresa.
		//       			   $as_codpai    // C�digo del Pais.
		//       			   $as_codreg    // C�digo de la Regi�n. 
		//     				   $aa_seguridad // Arreglo de Seguridad cargado con la informaci�n de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de eliminar las modalidades por clausulas en la tabla soc_dtm_clausulas.  
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  20/02/2006
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////  
		$lb_valido= false;        
		$ls_sql= "DELETE FROM scv_dt_incremento".
				 " WHERE codemp='".$this->ls_codemp."'".
				 "   AND codinc='".$as_codinc."'";	 
		$rs_data=$this->io_sql->execute($ls_sql);
		if ($rs_data===false)
		{
			$this->io_sql->rollback();
			$this->io_msg->message("CLASE->SIGESP_SCV_C_INCREMENTO; METODO->uf_delete_dt_incremento; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$lb_valido=true;
		} 		 
		return $lb_valido;
	} // fin de la function  uf_delete_dt_incremento
	
	function uf_load_incremento($as_codinc) 
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_load_region
		// 	          Access:  public
		// 	       Arguments:  $as_codinc // C�digo de la Empresa.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de verificar si existe o no una region, la funcion devuelve true si el
		//                     registro es encontrado caso contrario devuelve false. 
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  26/06/2006 
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////  
		$lb_valido= false;
		$ls_sql= " SELECT codinc FROM scv_incremento".
				 "  WHERE codemp='".$this->ls_codemp."'".
				 "    AND codinc='".$as_codinc."' ";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_SCV_C_INCREMENTO; METODO->uf_load_incremento; ERROR->".$this->io_funcion->uf_convertirmsg(       $this->io_sql->message));
		}
		else
		{
			$li_numrows= $this->io_sql->num_rows($rs_data);
			if ($li_numrows>0)
			{
				$lb_valido= true;
				$this->io_sql->free_result($rs_data);
			}
		} 
		return $lb_valido;
	} // fin de la function uf_load_incremento
	
	
	function uf_load_dt_incremento($as_codinc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_load_dt_incremento
		//	          Access:  public
		// 	       Arguments:  $as_codemp // C�digo de la Empresa.
		//       			   $as_codreg // C�digo de la Regi�n. 
		//       			   $as_codpai // C�digo del Pais.
		//           Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de extraer todos los detalles(Estados) asociados a un Regi�n. 
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  26/06/2006 
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido= false;
		$ls_sql=" SELECT codinc,codregdes,porinc,".
				"       (SELECT denreg FROM scv_regiones_int".
				"         WHERE scv_dt_incremento.codemp=scv_regiones_int.codemp".
				"           AND scv_dt_incremento.codregdes=scv_regiones_int.codreg) AS denreg".
				"   FROM scv_dt_incremento ".
				"  WHERE codemp='".$this->ls_codemp."'".
				"    AND codinc='".$as_codinc."'";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_SCV_C_INCREMENTO; METODO->uf_load_dt_incremento; ERROR->".$this->io_funcion->uf_convertirmsg(       $this->io_sql->message));
		}
		else
		{
			$li_numrows=$this->io_sql->num_rows($rs_data);
			if ($li_numrows>0)
			{
				$datos=$this->io_sql->obtener_datos($rs_data);
				$this->ds_dtregion = new class_datastore();
				$this->ds_dtregion->data=$datos;
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
		}		
		return $lb_valido;
	} // fin de la function uf_load_dt_incremento
	
	function uf_load_paises()
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_load_paises
		//	          Access:  public
		// 	       Arguments:  
		//           Returns:  $rs_data.
		//		 Description:  Devuelve un resulset con todos los paises de la tabla sigesp_pais.
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  26/06/2006 
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql= "SELECT codpai,despai FROM sigesp_pais".
				 " ORDER BY codpai ASC";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_SCV_C_REGIONES; METODO->uf_load_paises; ERROR->".$this->io_funcion->uf_convertirmsg(       $this->io_sql->message));
		}
		else
		{
			$li_numrows = $this->io_sql->num_rows($rs_data);	    
			if ($li_numrows>0)
			{
				$lb_valido=true;
			}
		}	
		return $rs_data;
	}  // fin de la function uf_load_paises
	
	function uf_generar_codigo($ab_empresa,$as_codemp,$as_tabla,$as_columna,$as_columna2)
	{ 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_generar_codigo
		//	          Access:  public
		//	       Arguments:  $as_codpais // codigo de pais
		//					   $ab_empresa // Si usara el campo empresa como filtro    
		//					   $as_codemp    // codigo de la empresa
		//					   $as_tabla     // Nombre de la tabla 
		//					   $ai_length    // longitud del campo
		//	         Returns:  $lb_valido.
		//		 Description:   Este m�todo genera el numero consecutivo del c�digo de cualquier tabla deseada
		//     Elaborado Por:  Ing. Nestor Falc�n
		// Fecha de Creaci�n:  02/08/2006      
		//	  Modificado Por:  Ing. Luis Anibal Lang
		// 		Fecha Modif.:  02/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$lb_existe=$this->existe_tabla($as_tabla);
		if ($lb_existe)
		{
			$lb_existe=$this->existe_columna($as_tabla,$as_columna);
			if ($lb_existe)
			{
				$li_longitud=$this->longitud_campo($as_tabla,$as_columna) ;
				if ($ab_empresa)
				{	
					$ls_sql=" SELECT ".$as_columna."".
							"   FROM ".$as_tabla."".
							"  WHERE codemp='".$as_codemp."'".
							"    AND codpai='".$as_columna2."'".
							"  ORDER BY ".$as_columna." DESC";	
					$rs_funciondb=$this->io_sql->select($ls_sql);
					if ($row=$this->io_sql->fetch_row($rs_funciondb))
					{ 
						$codigo=$row[$as_columna];
						settype($codigo,'int');                             // Asigna el tipo a la variable.
						$codigo = $codigo + 1;                              // Le sumo uno al entero.
						settype($codigo,'string');                          // Lo convierto a varchar nuevamente.
						$ls_codigo=$this->io_funcion->uf_cerosizquierda($codigo,$li_longitud);
					}
					else
					{
						$codigo="1";
						$ls_codigo=$this->io_funcion->uf_cerosizquierda($codigo,$li_longitud);
					}
				}	
				else
				{
					$ls_sql=" SELECT ".$as_columna."".
							"   FROM ".$as_tabla."".
							"  WHERE codpai='".$as_columna2."'".
							"  ORDER BY ".$as_columna." DESC";	
					$rs_funciondb=$this->io_sql->select($ls_sql);
					if ($row=$this->io_sql->fetch_row($rs_funciondb))
					{ 
						$codigo=$row[$as_columna];
						settype($codigo,'int');                                          // Asigna el tipo a la variable.
						$codigo = $codigo + 1;                                           // Le sumo uno al entero.
						settype($codigo,'string');                                       // Lo convierto a varchar nuevamente.
						$ls_codigo=$this->io_funcion->uf_cerosizquierda($codigo,$li_longitud); 
					}   
					else
					{
						$codigo="1";
						$ls_codigo=$this->io_funcion->uf_cerosizquierda($codigo,$li_longitud);
					}
				}// SI NO TIENE CODIGO DE EMPRESA
			}
			else
			{
			$ls_codigo="";
			$this->is_msg_error="No existe el campo" ;
			}
		}
		else
		{
			$ls_codigo="";
			$this->is_msg_error="No existe la tabla	" ;
		}
		return $ls_codigo;
	} // fin function uf_generar_codigo

	function longitud_campo($as_tabla,$as_columna)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_generar_codigo
		//	          Access:  public
		//	       Arguments:  $as_tabla   // nombre de la tabla
		//					   $as_columna // nombre de la columna
		//	         Returns:  $lb_valido.
		//		 Description:  Este m�todo verifica la longitud de un campo
		//     Elaborado Por:  Ing. Nestor Falc�n
		// Fecha de Creaci�n:  02/08/2006      
		//	  Modificado Por:  Ing. Luis Anibal Lang
		// 		Fecha Modif.:  02/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
	   $li_length = 0;
	   switch ($this->io_gestor)
	   {
	   		case "MYSQLT":
			   $ls_sql=" SELECT character_maximum_length AS width ".
					   " FROM information_schema.columns ".
					   " WHERE TABLE_SCHEMA='".$this->io_database."' AND UPPER(table_name)=UPPER('".$as_tabla."') AND ".
					   "       UPPER(column_name)=UPPER('".$as_columna."')";
			break;
	   		case "MYSQLI":
			   $ls_sql=" SELECT character_maximum_length AS width ".
					   " FROM information_schema.columns ".
					   " WHERE TABLE_SCHEMA='".$this->io_database."' AND UPPER(table_name)=UPPER('".$as_tabla."') AND ".
					   "       UPPER(column_name)=UPPER('".$as_columna."')";
			break;
	   		case "POSTGRES":
			  $ls_sql = " SELECT character_maximum_length AS width ".
						"   FROM INFORMATION_SCHEMA.COLUMNS ".
						"  WHERE table_catalog='".$this->io_database."'".
						"    AND UPPER(table_name)=UPPER('".$as_tabla."')".
						"    AND UPPER(column_name)=UPPER('".$as_columna."')";
			break;
	   }
	   $rs_data=$this->io_sql->select($ls_sql);
	   if ($row=$this->io_sql->fetch_row($rs_data))   {  $li_length=$row["width"];  } 
	   return $li_length; 
	} // fin function longitud_campo

	function existe_tabla($as_tabla)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  existe_tabla
		//	          Access:  public
		//	       Arguments:  $as_tabla   // nombre de la tabla
		//	         Returns:  $lb_valido.
		//		 Description:  Este m�todo verifica la existencia de una tabla
		//     Elaborado Por:  Ing. Nestor Falc�n
		// Fecha de Creaci�n:  02/08/2006      
		//	  Modificado Por:  Ing. Luis Anibal Lang
		// 		Fecha Modif.:  02/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
       $lb_existe = false;
	   switch ($this->io_gestor)
	   {
	   		case "MYSQLT":
			   $ls_sql= " SELECT * FROM ".
						" INFORMATION_SCHEMA.TABLES ".
						" WHERE TABLE_SCHEMA='".$this->io_database."' AND (UPPER(TABLE_NAME)=UPPER('".$as_tabla."'))";				
			break;
	   		case "MYSQLI":
			   $ls_sql= " SELECT * FROM ".
						" INFORMATION_SCHEMA.TABLES ".
						" WHERE TABLE_SCHEMA='".$this->io_database."' AND (UPPER(TABLE_NAME)=UPPER('".$as_tabla."'))";				
			break;
	   		case "POSTGRES":
			   $ls_sql= " SELECT * FROM ".
						" INFORMATION_SCHEMA.TABLES ".
						" WHERE table_catalog='".$this->io_database."' AND (UPPER(table_name)=UPPER('".$as_tabla."'))";	
			break;
	   }
	   $rs_data=$this->io_sql->select($ls_sql);
	   if($rs_data===false)
	   {   
          $this->io_msg->message("ERROR en uf_select_table()".$this->io_funcion->uf_convertirmsg($this->io_sql->message));			
 		 return false; 
	   }
	   else
	   {
	 	  if ($row=$this->io_sql->fetch_row($rs_data)) { $lb_existe=true; } 
   		  $this->io_sql->free_result($rs_data);	 
   	   }	  
	   return $lb_existe;
	} // fin function existe_tabla

	function existe_columna($as_tabla,$as_columna)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  existe_columna
		//	          Access:  public
		//	       Arguments:  $as_tabla   // nombre de la tabla
		//					   $as_columna // nombre de la columna
		//	         Returns:  $lb_valido.
		//		 Description:  Este m�todo verifica la existencia de una tabla
		//     Elaborado Por:  Ing. Nestor Falc�n
		// Fecha de Creaci�n:  02/08/2006      
		//	  Modificado Por:  Ing. Luis Anibal Lang
		// 		Fecha Modif.:  02/10/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
       $lb_existe = false;
	   switch ($this->io_gestor)
	   {
	   		case "MYSQLT":
			  $ls_sql = " SELECT COLUMN_NAME ".
						" FROM INFORMATION_SCHEMA.COLUMNS ".
						" WHERE TABLE_SCHEMA='".$this->io_database."' AND UPPER(TABLE_NAME)=UPPER('".$as_tabla."') AND UPPER(COLUMN_NAME)=UPPER('".$as_columna."')";
			break;
	   		case "MYSQLI":
			  $ls_sql = " SELECT COLUMN_NAME ".
						" FROM INFORMATION_SCHEMA.COLUMNS ".
						" WHERE TABLE_SCHEMA='".$this->io_database."' AND UPPER(TABLE_NAME)=UPPER('".$as_tabla."') AND UPPER(COLUMN_NAME)=UPPER('".$as_columna."')";
			break;
	   		case "POSTGRES":
			  $ls_sql = " SELECT COLUMN_NAME ".
						" FROM INFORMATION_SCHEMA.COLUMNS ".
						" WHERE table_catalog='".$this->io_database."' AND UPPER(table_name)=UPPER('".$as_tabla."') AND UPPER(column_name)=UPPER('".$as_columna."')";
			break;
	   }
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  {   
         $this->io_msg->message("ERROR en uf_select_column()".$this->io_funcion->uf_convertirmsg($this->io_sql->message));			
		 return false;
	  }
	  else
	  {
		  if ($row=$this->io_sql->fetch_row($rs_data)) { $lb_existe=true; } 
  		  $this->io_sql->free_result($rs_data);	 
	  }	  
	  return $lb_existe;
	} // fin function existe_columna
	function uf_load_continentes()
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_load_continentes
		//	          Access:  public
		// 	       Arguments:  
		//           Returns:  $rs_data.
		//		 Description:  Devuelve un resulset con todos los paises de la tabla sigesp_pais.
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  26/06/2006 
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codcont,dencont".
				"  FROM sigesp_continente".
				" ORDER BY codcont ASC";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_SCV_C_REGIONES; METODO->uf_load_continentes; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$li_numrows = $this->io_sql->num_rows($rs_data);	    
			if ($li_numrows>0)
			{
				$lb_valido=true;
			}
		}	
		return $rs_data;
	}  // fin de la function uf_load_paises
	
	function uf_insert_region_int($as_codemp,$as_codreg,$as_codcont,$as_denreg,$ar_grid,$ai_total,$aa_seguridad) 
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_insert_region_int
		//	          Access:  public
		// 	       Arguments:  $as_codemp    // C�digo de la Empresa.  
		//        			   $as_codreg    // C�digo de la Regi�n.
		//        			   $as_codpai    // C�digo del Pa�s al cual pertenece la Regi�n.
		//        			   $as_denreg    // Denominaci�n de la Regi�n.
		//   				   $ar_grid      // Objeto grid de donde insertaremos los detalles.
		//         			   $ai_total     // Total de filas del grid de Detalles de Estados.
		//     				   $aa_seguridad // Arreglo de Seguridad cargado con la informaci�n de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de insertar una nueva modalidad en la tabla scv_regiones. 
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  23/06/2006
		//    Modificado Por:  Ing. Luis Anibal Lang
		//    Fecha de Modif:  19/09/2006      
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_codregaux = $as_codreg;
		$arrResultado= $this->io_keygen->uf_verificar_numero_generado("SCV","scv_regiones_int","codreg","",3,"","","",$as_codreg);
		$as_codreg=$arrResultado['as_numero'];
		$lb_valido=$arrResultado['lb_valido'];
		//$as_codreg=$this->io_funciondb->uf_generar_codigo(true,$as_codemp,'scv_regiones_int','codreg',$as_codpai);
		$this->io_sql->begin_transaction();
		$ls_sql=" INSERT INTO scv_regiones_int (codemp, codreg, codcont, denreg)".
				"      VALUES ('".$as_codemp."','".$as_codreg."','".$as_codcont."','".$as_denreg."')";
		$rs_data = $this->io_sql->execute($ls_sql);
		if ($rs_data===false)
		{
			$this->io_sql->rollback();
			if($this->io_sql->errno=='23505' || $this->io_sql->errno=='1062' || $this->io_sql->errno=='-239' || $this->io_sql->errno=='-5'|| $this->io_sql->errno=='-1')
			{
				$lb_valido=$this->uf_insert_region_int($as_codemp,$as_codreg,$as_codcont,$as_denreg,$ar_grid,$ai_total,$aa_seguridad);
			}
			else
			{
				$this->io_msg->message("CLASE->SIGESP_SCV_C_REGIONES; METODO->uf_insert_region_int; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			}
		}
		else
		{
			if ($this->uf_insert_dt_region_int($as_codemp,$as_codcont,$as_codreg,$ar_grid,$ai_total,$aa_seguridad))
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               ////////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion =" Insert�  la Regi�n ".$as_codreg." del Pais ".$as_codpai." Asociada a la empresa ".$as_codemp;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
										$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
										$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               ////////////////////////////////	
				$this->io_sql->commit();
				if($ls_codregaux!=$as_codreg)
				{
					$this->io_msg->message("Se Asigno el C�digo de Regi�n: ".$as_codreg);
				}
			}
		}
		return $lb_valido;
	} // fin de la function sigesp_scv_c_regiones
	
	function uf_insert_dt_region_int($as_codemp,$as_codpai,$as_codreg,$ar_grid,$ai_total,$aa_seguridad)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Funcion:  uf_insert_dt_modalidad
		//	          Access:  public
		// 	       Arguments:  $as_codemp    //  C�digo de la Empresa.
		//        			   $as_codpai    //  C�digo del Pais.
		//                     $ar_grid      //  Arreglo cargado con los estados que ser�n insertados para una Regi�n.
		//                     $ai_total     //  Variable que contiene la cantidad de estados que van a ser insertados a la Regi�n.
		//                     $aa_seguridad //  Arreglo de Seguridad cargado con la informaci�n de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de insertar detalles para una modalidad en la tabla soc_dtm_clausulas. 
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  20/02/2006  
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		for ($li_i=1;$li_i<=$ai_total;$li_i++)
		{
			if ($lb_valido)
			{
				$ls_codpai = $ar_grid["pais"][$li_i];    
				if (!empty($ls_codpai))			            
				{
					$ls_sql=" INSERT INTO scv_dt_regiones_int (codemp, codreg, codpai) ".
							"      VALUES ('".$as_codemp."','".$as_codreg."','".$ls_codpai."')"; 
					$rs_data = $this->io_sql->execute($ls_sql);              
					if ($rs_data===false)
					{				 
						$this->io_sql->rollback();
						$this->io_msg->message("CLASE->SIGESP_SCV_C_REGIONES; METODO->uf_insert_dt_region_int; ERROR->".$this->io_funcion->                     uf_convertirmsg($this->io_sql->message));
					}
					else
					{				 
						$lb_valido=true;  		                    
						/////////////////////////////////         SEGURIDAD               /////////////////////////////		
						$ls_evento      ="INSERT";
						$ls_descripcion =" Insert� el estado ".$ls_codest." del la Regi�n ".$as_codreg." asociado a la Empresa ".$as_codemp;
						$ls_variable    = $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
						$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
						$aa_seguridad["ventanas"],$ls_descripcion);
						/////////////////////////////////         SEGURIDAD               ///////////////////////////// 
					}  				
				}
			}
		} 
		return $lb_valido;
	} // fin de la function uf_insert_dt_region_int
	
	function uf_update_region_int($as_codemp,$as_codreg,$as_codcont,$as_denreg,$ar_grid,$ai_total,$aa_seguridad) 
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_update_region_int
		//	          Access:  public
		// 	       Arguments:  $as_codemp    //  C�digo de la Empresa.
		//       			   $as_codpai    //  C�digo del Pais.
		//                     $ar_grid      //  Arreglo cargado con los estados que ser�n insertados para una Regi�n.
		//                     $ai_total     //  Variable que contiene la cantidad de estados que van a ser insertados a la Regi�n.
		//                     $aa_seguridad // Arreglo de Seguridad cargado con la informaci�n de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de actualizar los datos de una modalidad en la tabla scv_regiones.  
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  20/02/2006     
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
		$lb_valido= false;
		$this->io_sql->begin_transaction();
		$ls_sql= "UPDATE scv_regiones_int".
				 "   SET denreg='".$as_denreg."'".
				 " WHERE codemp='".$as_codemp."'".
				 "   AND codreg='".$as_codreg."'";
		$rs_data = $this->io_sql->execute($ls_sql);
		if ($rs_data===false)
		{
			$this->io_sql->rollback();
			$this->io_msg->message("CLASE->SIGESP_SCV_C_REGIONES; METODO->uf_update_region_int; ERROR->".$this->io_funcion->uf_convertirmsg(       $this->io_sql->message));
		}
		else
		{
			if ($this->uf_delete_paises_region_int($as_codemp,$as_codcont,$as_codreg,$aa_seguridad))//Eliminar todos los estados asociados a una                                                                                   regi�n.
			{                  
				if ($this->uf_insert_dt_region_int($as_codemp,$as_codcont,$as_codreg,$ar_grid,$ai_total,$aa_seguridad))
				{                        
					$lb_valido=true;
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Actualiz� la Regi�n ".$as_codreg." del Pais ".$as_codpai." Asociada a la empresa ".$as_codemp;
					$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////
					$this->io_sql->commit();
				}
			}
		}
		return $lb_valido;
	} // fin de la function uf_update_region_int
	
	function uf_delete_region_int($as_codemp,$as_codcont,$as_codreg,$aa_seguridad)
	{          		 
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_delete_region_int
		//	          Access:  public
		// 	       Arguments:  $as_codemp    // C�digo de la Empresa.
		//       			   $as_codpai    // C�digo del Pais.
		//       			   $as_codreg    // C�digo de la Regi�n. 
		//     				   $aa_seguridad // Arreglo de Seguridad cargado con la informaci�n de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de eliminar una modalidad en la tabla scv_regiones.  
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  20/02/2006 
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////  
		$lb_valido=false;
		$lb_relacion= $this->uf_validar_delete($as_codemp,$as_codreg);
		if (!$lb_relacion)
		{
			if ($this->uf_delete_paises_region_int($as_codemp,$as_codcont,$as_codreg,$aa_seguridad))  
			{
				$ls_sql= " DELETE FROM scv_regiones_int".
						 "  WHERE codemp='".$as_codemp."'".
						 "    AND codcont='".$as_codcont."'".
						 "    AND codreg='".$as_codreg."'";	 
				$rs_data= $this->io_sql->execute($ls_sql);
				if ($rs_data===false)
				{
					$this->io_sql->rollback();
					$this->io_msg->message("CLASE->SIGESP_SCV_C_REGIONES; METODO->uf_delete_region_int; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				}
				else
				{
					$lb_valido=true;
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="DELETE";
					$ls_descripcion ="Elimin� la Regi�n ".$as_codreg." del Pais ".$as_codpai." Asociada a la empresa ".$as_codemp;
					$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////
					$this->io_sql->commit();
				}
			}
		}
		else
		{
			$this->io_msg->message('La Regi�n no puede ser eliminada, posee registros asociados a otras tablas'); 
		}
		return $lb_valido;
	} // fin de la function uf_delete_region_int
	
	function uf_delete_paises_region_int($as_codemp,$as_codcont,$as_codreg)
	{          		 
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_delete_estados_region_int
		//	          Access:  public
		// 	       Arguments:  $as_codemp    // C�digo de la Empresa.
		//       			   $as_codpai    // C�digo del Pais.
		//       			   $as_codreg    // C�digo de la Regi�n. 
		//     				   $aa_seguridad // Arreglo de Seguridad cargado con la informaci�n de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de eliminar las modalidades por clausulas en la tabla soc_dtm_clausulas.  
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  20/02/2006
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////  
		$lb_valido= false;        
		$ls_sql= "DELETE FROM scv_dt_regiones_int".
				 " WHERE codemp='".$as_codemp."'".
				 "   AND codreg='".$as_codreg."'";	 
		$rs_data=$this->io_sql->execute($ls_sql);
		if ($rs_data===false)
		{
			$this->io_sql->rollback();
			$this->io_msg->message("CLASE->SIGESP_SCV_C_REGIONES; METODO->uf_delete_estados_region_int; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$lb_valido=true;
		} 		 
		return $lb_valido;
	} // fin de la function  uf_delete_estados_region_int


	function uf_load_dt_region_int($as_codemp,$as_codreg,$as_codcont)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_load_dt_region
		//	          Access:  public
		// 	       Arguments:  $as_codemp // C�digo de la Empresa.
		//       			   $as_codreg // C�digo de la Regi�n. 
		//       			   $as_codpai // C�digo del Pais.
		//           Returns:  $lb_valido.
		//	     Description:  Funci�n que se encarga de extraer todos los detalles(Estados) asociados a un Regi�n. 
		//     Elaborado Por:  Ing. N�stor Falc�n.
		// Fecha de Creaci�n:  26/06/2006 
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido= false;
		$ls_sql=" SELECT scv_dt_regiones_int.codpai,sigesp_pais.despai".
				"   FROM scv_dt_regiones_int,sigesp_pais ".
				"  WHERE scv_dt_regiones_int.codemp='".$as_codemp."'".
				"    AND scv_dt_regiones_int.codreg='".$as_codreg."'".
				"    AND scv_dt_regiones_int.codpai=sigesp_pais.codpai";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_SCV_C_REGIONES; METODO->uf_load_dt_region; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$li_numrows=$this->io_sql->num_rows($rs_data);
			if ($li_numrows>0)
			{
				$datos=$this->io_sql->obtener_datos($rs_data);
				$this->ds_dtregion = new class_datastore();
				$this->ds_dtregion->data=$datos;
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
		}		
		return $lb_valido;
	} // fin de la function uf_load_dt_region
	
	
	
}   // fin de la class sigesp_scv_c_regiones
?> 