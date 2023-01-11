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

class sigesp_scv_c_tarifacargos
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
		$this->io_funciondb= new class_funciones_db($conn);
		require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
	} // fin de la function SIGESP_SCV_C_TARIFACARGOS

	function uf_insert_tarifa($as_codemp,$as_codtar,$as_dentar,$as_tipvia,$ar_grid,$ai_total,$as_exterior,$as_codmon,$aa_seguridad) 
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_insert_region
		//	          Access:  public
		// 	       Arguments:  $as_codemp    // Código de la Empresa.  
		//        			   $as_codreg    // Código de la Región.
		//        			   $as_codpai    // Código del País al cual pertenece la Región.
		//        			   $as_denreg    // Denominación de la Región.
		//   				   $ar_grid      // Objeto grid de donde insertaremos los detalles.
		//         			   $ai_total     // Total de filas del grid de Detalles de Estados.
		//     				   $aa_seguridad // Arreglo de Seguridad cargado con la información de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Función que se encarga de insertar una nueva modalidad en la tabla scv_regiones. 
		//     Elaborado Por:  Ing. Néstor Falcón.
		// Fecha de Creación:  23/06/2006
		//    Modificado Por:  Ing. Luis Anibal Lang
		//    Fecha de Modif:  19/09/2006      
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_codtaraux = $as_codtar;
		$arrResultado= $this->io_keygen->uf_verificar_numero_generado("SCV","scv_tarifacargos","codtar","",4,"","","",$as_codtar);
		$as_codtar=$arrResultado['as_numero'];
		$lb_valido=$arrResultado['lb_valido'];
		$this->io_sql->begin_transaction();
		$ls_sql=" INSERT INTO scv_tarifacargos (codemp, codtar, dentar,tipvia,exterior,codmon)".
				"      VALUES ('".$as_codemp."','".$as_codtar."','".$as_dentar."','".$as_tipvia."','".$as_exterior."','".$as_codmon."')";
		$rs_data = $this->io_sql->execute($ls_sql);
		if ($rs_data===false)
		{
			$this->io_sql->rollback();
			if($this->io_sql->errno=='23505' || $this->io_sql->errno=='1062' || $this->io_sql->errno=='-239' || $this->io_sql->errno=='-5'|| $this->io_sql->errno=='-1')
			{
				$arrResultado=$this->uf_insert_tarifa($as_codemp,$as_codtar,$as_dentar,$as_tipvia,$ar_grid,$ai_total,$as_exterior,$as_codmon,$aa_seguridad);
				$as_codtar=$arrResultado['as_codtar'];
				$lb_valido=$arrResultado['lb_valido'];
			}
			else
			{
				$this->io_msg->message("CLASE->SIGESP_SCV_C_TARIFACARGOS; METODO->uf_insert_tarifa; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			}
		}
		else
		{
			if ($this->uf_insert_dt_tarifas($as_codemp,$as_codtar,$ar_grid,$ai_total,$aa_seguridad))
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               ////////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion =" Insertó  la Tarifa ".$as_codtar." Asociada a la empresa ".$as_codemp;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
										$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
										$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               ////////////////////////////////	
				$this->io_sql->commit();
				if($ls_codtaraux!=$as_codtar)
				{
					$this->io_msg->message("Se Asigno el Código de Tarifa: ".$as_codtar);
				}
			}
		}
		$arrResultado['as_codtar']=$as_codtar;
		$arrResultado['lb_valido']=$lb_valido;
		return $lb_valido;
	} // fin de la function SIGESP_SCV_C_TARIFACARGOS
	
	function uf_insert_dt_tarifas($as_codemp,$as_codtar,$ar_grid,$ai_total,$aa_seguridad)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Funcion:  uf_insert_dt_modalidad
		//	          Access:  public
		// 	       Arguments:  $as_codemp    //  Código de la Empresa.
		//        			   $as_codpai    //  Código del Pais.
		//                     $ar_grid      //  Arreglo cargado con los estados que serán insertados para una Región.
		//                     $ai_total     //  Variable que contiene la cantidad de estados que van a ser insertados a la Región.
		//                     $aa_seguridad //  Arreglo de Seguridad cargado con la información de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Función que se encarga de insertar detalles para una modalidad en la tabla soc_dtm_clausulas. 
		//     Elaborado Por:  Ing. Néstor Falcón.
		// Fecha de Creación:  20/02/2006  
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true; 
		for ($li_i=1;$li_i<=$ai_total;$li_i++)
		{
			if ($lb_valido)
			{
				$ls_codcar = $ar_grid["cargo"][$li_i];    
				$ls_codnom = $ar_grid["nomina"][$li_i]; 
				$ls_montarcar = $ar_grid["montarcar"][$li_i];    
				$ls_montarcar=    str_replace(".","",$ls_montarcar);
				$ls_montarcar=    str_replace(",",".",$ls_montarcar);
				if (!empty($ls_codcar))			            
				{
					$ls_sql=" INSERT INTO scv_dt_tarifacargos (codemp, codtar, codnom, codcar,montarcar) ".
							"      VALUES ('".$as_codemp."','".$as_codtar."','".$ls_codnom."','".$ls_codcar."',".$ls_montarcar.")";
					$rs_data = $this->io_sql->execute($ls_sql);         
					if ($rs_data===false)
					{				 
						$this->io_sql->rollback();
						$this->io_msg->message("CLASE->SIGESP_SCV_C_TARIFACARGOS; METODO->uf_insert_dt_tarifas; ERROR->".$this->io_funcion->                     uf_convertirmsg($this->io_sql->message));
					}
					else
					{				 
						$lb_valido=true;  		                    
						/////////////////////////////////         SEGURIDAD               /////////////////////////////		
						$ls_evento      ="INSERT";
						$ls_descripcion =" Insertó la tarifa ".$as_codtar." del la Nomina ".$ls_codnom." asociado a la Empresa ".$as_codemp;
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
	
	function uf_update_tarifa($as_codemp,$as_codtar,$as_dentar,$as_tipvia,$ar_grid,$ai_total,$as_exterior,$as_codmon,$aa_seguridad) 
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_update_region
		//	          Access:  public
		// 	       Arguments:  $as_codemp    //  Código de la Empresa.
		//       			   $as_codpai    //  Código del Pais.
		//                     $ar_grid      //  Arreglo cargado con los estados que serán insertados para una Región.
		//                     $ai_total     //  Variable que contiene la cantidad de estados que van a ser insertados a la Región.
		//                     $aa_seguridad // Arreglo de Seguridad cargado con la información de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Función que se encarga de actualizar los datos de una modalidad en la tabla scv_regiones.  
		//     Elaborado Por:  Ing. Néstor Falcón.
		// Fecha de Creación:  20/02/2006     
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
		$lb_valido= false;
		$this->io_sql->begin_transaction();
		$ls_sql= "UPDATE scv_tarifacargos".
				 "   SET dentar='".$as_dentar."',".
				 "       tipvia='".$as_tipvia."',".
				 "       exterior='".$as_exterior."',".
				 "       codmon='".$as_codmon."'".
				 " WHERE codemp='".$as_codemp."'".
				 "   AND codtar='".$as_codtar."'";
		$rs_data = $this->io_sql->execute($ls_sql);
		if ($rs_data===false)
		{
			$this->io_sql->rollback();
			$this->io_msg->message("CLASE->SIGESP_SCV_C_TARIFACARGOS; METODO->uf_update_region; ERROR->".$this->io_funcion->uf_convertirmsg(       $this->io_sql->message));
		}
		else
		{
			if ($this->uf_delete_dt_tarifas($as_codemp,$as_codtar,$aa_seguridad))//Eliminar todos los estados asociados a una                                                                                   región.
			{                  
				if ($this->uf_insert_dt_tarifas($as_codemp,$as_codtar,$ar_grid,$ai_total,$aa_seguridad))
				{     
					$lb_valido=true;
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Actualizó la Tarifa ".$as_codtar." Asociada a la empresa ".$as_codemp;
					$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////
					$this->io_sql->commit();
				}
			}
		}
		return $lb_valido;
	} // fin de la function uf_update_region
	
	function uf_delete_region($as_codemp,$as_codtar,$aa_seguridad)
	{          		 
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_delete_region
		//	          Access:  public
		// 	       Arguments:  $as_codemp    // Código de la Empresa.
		//       			   $as_codpai    // Código del Pais.
		//       			   $as_codreg    // Código de la Región. 
		//     				   $aa_seguridad // Arreglo de Seguridad cargado con la información de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Función que se encarga de eliminar una modalidad en la tabla scv_regiones.  
		//     Elaborado Por:  Ing. Néstor Falcón.
		// Fecha de Creación:  20/02/2006 
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////  
		$lb_valido=false;
		if ($this->uf_delete_dt_tarifas($as_codemp,$as_codtar))  
		{
			$ls_sql= " DELETE FROM scv_tarifacargos".
					 "  WHERE codemp='".$as_codemp."'".
					 "    AND codtar='".$as_codtar."'";
			$rs_data= $this->io_sql->execute($ls_sql);
			if ($rs_data===false)
			{
				$this->io_sql->rollback();
				$this->io_msg->message("CLASE->SIGESP_SCV_C_TARIFACARGOS; METODO->uf_delete_region; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			}
			else
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="DELETE";
				$ls_descripcion ="Eliminó la Tarifa ".$as_codtar." Asociada a la empresa ".$as_codemp;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$this->io_sql->commit();
			}
		}
		return $lb_valido;
	} // fin de la function uf_delete_region
	
	function uf_delete_dt_tarifas($as_codemp,$as_codtar)
	{          		 
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_delete_estados_region
		//	          Access:  public
		// 	       Arguments:  $as_codemp    // Código de la Empresa.
		//       			   $as_codpai    // Código del Pais.
		//       			   $as_codreg    // Código de la Región. 
		//     				   $aa_seguridad // Arreglo de Seguridad cargado con la información de usuario,ventana,etc.
		//	         Returns:  $lb_valido.
		//	     Description:  Función que se encarga de eliminar las modalidades por clausulas en la tabla soc_dtm_clausulas.  
		//     Elaborado Por:  Ing. Néstor Falcón.
		// Fecha de Creación:  20/02/2006
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////  
		$lb_valido= false;        
		$ls_sql= "DELETE FROM scv_dt_tarifacargos".
				 " WHERE codemp='".$as_codemp."'".
				 "   AND codtar='".$as_codtar."'";	
		$rs_data=$this->io_sql->execute($ls_sql);
		if ($rs_data===false)
		{
			$this->io_sql->rollback();
			$this->io_msg->message("CLASE->SIGESP_SCV_C_TARIFACARGOS; METODO->uf_delete_dt_tarifas; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$lb_valido=true;
		} 
		return $lb_valido;
	} // fin de la function  uf_delete_estados_region
	
	function uf_load_tarifa($as_codemp,$as_codtar) 
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_load_region
		// 	          Access:  public
		// 	       Arguments:  $as_codemp // Código de la Empresa.
		//       			   $as_codpai // Código del Pais.
		//       			   $as_codreg // Código de la Región. 
		//	         Returns:  $lb_valido.
		//	     Description:  Función que se encarga de verificar si existe o no una region, la funcion devuelve true si el
		//                     registro es encontrado caso contrario devuelve false. 
		//     Elaborado Por:  Ing. Néstor Falcón.
		// Fecha de Creación:  26/06/2006 
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////  
		$lb_valido= false;
		$ls_sql= " SELECT codtar FROM scv_tarifacargos".
				 "  WHERE codemp='".$as_codemp."'".
				 "    AND codtar='".$as_codtar."' ";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_SCV_C_TARIFACARGOS; METODO->uf_load_region; ERROR->".$this->io_funcion->uf_convertirmsg(       $this->io_sql->message));
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
	} // fin de la function uf_load_region
	
	function uf_validar_delete($as_codemp,$as_codreg) 
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_validar_delete
		//	          Access:  public
		// 	       Arguments:  $as_codemp // Código de la Empresa.
		//       			   $as_codreg // Código de la Región. 
		//	         Returns:  $lb_valido.
		//	     Description:  Función que se encarga de verificar si existe o no una modalidad dentro de la tabla soc_ordencompra, 
		//                     la funcion devuelve true si el registro es encontrado, caso contrario devuelve false. 
		//     Elaborado Por:  Ing. Néstor Falcón.
		// Fecha de Creación:  20/02/2006 
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido= false;
		$ls_sql= " SELECT * FROM scv_tarifas".
				 "  WHERE codemp='".$as_codemp."'".
				 "    AND codreg='".$as_codreg."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$lb_valido=false;
		}
		else
		{
			$li_numrows = $this->io_sql->num_rows($rs_data);
			if ($li_numrows>0)
			{
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
		}
		return $lb_valido;
	} // fin de la function uf_validar_delete
	
//////////////////////////////////////////////////////////////////////////////
//	          Metodo:  uf_load_dt_region
//	          Access:  public
// 	       Arguments:  $as_codemp // Código de la Empresa.
//       			   $as_codreg // Código de la Región. 
//       			   $as_codpai // Código del Pais.
//           Returns:  $lb_valido.
//	     Description:  Función que se encarga de extraer todos los detalles(Estados) asociados a un Región. 
//     Elaborado Por:  Ing. Néstor Falcón.
// Fecha de Creación:  26/06/2006 
//    Modificado Por:  Ing. Luis Anibal Lang
//   
//   Fecha de Modif.:  28/11/2013  
//   POR ING. ANIBAL GERARDO TILLERO OLARTE
//         
//////////////////////////////////////////////////////////////////////////////
function uf_load_dt_tarifacargo($as_codemp,$as_codtar)
{
   $lb_valido= false;
//		$ls_sql=" SELECT codnom,codcar,montarcar".
//				"   FROM scv_dt_tarifacargos ".
//				"  WHERE codemp='".$as_codemp."'".
//				"    AND codtar='".$as_codtar."'";

   $ls_sql=" SELECT scv_dt_tarifacargos.codnom, scv_dt_tarifacargos.codcar, ".
            " sno_cargo.descar,   scv_dt_tarifacargos.montarcar ".
            " FROM scv_dt_tarifacargos ".
            " INNER JOIN sno_cargo on ( scv_dt_tarifacargos.codemp='".$as_codemp."' and ".
            " scv_dt_tarifacargos.codcar= sno_cargo.codcar) ".
            "  WHERE scv_dt_tarifacargos.codemp='".$as_codemp."'".
            "    AND scv_dt_tarifacargos.codtar='".$as_codtar."'" .
            " ORDER BY scv_dt_tarifacargos.montarcar desc, sno_cargo.descar ASC ";
   
   //echo $ls_sql;
   
   $rs_data = $this->io_sql->select($ls_sql);
   if ($rs_data===false)
   {
         $this->io_msg->message("CLASE->SIGESP_SCV_C_TARIFACARGOS; METODO->uf_load_dt_region; ERROR->".$this->io_funcion->uf_convertirmsg(       $this->io_sql->message));
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

	function uf_load_paises()
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_load_paises
		//	          Access:  public
		// 	       Arguments:  
		//           Returns:  $rs_data.
		//		 Description:  Devuelve un resulset con todos los paises de la tabla sigesp_pais.
		//     Elaborado Por:  Ing. Néstor Falcón.
		// Fecha de Creación:  26/06/2006 
		//    Modificado Por:  Ing. Luis Anibal Lang
		//   Fecha de Modif.:  19/09/2006      
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql= "SELECT codpai,despai FROM sigesp_pais".
				 " ORDER BY codpai ASC";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_SCV_C_TARIFACARGOS; METODO->uf_load_paises; ERROR->".$this->io_funcion->uf_convertirmsg(       $this->io_sql->message));
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
	
	function uf_load_codigo_moneda_nacional()
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_load_codigo_moneda_nacional
		//	          Access:  public
		// 	       Arguments:  
		//           Returns:  $as_moneda.
		//		 Description:  Devuelve el codigo de la moneda nacional.
		//     Elaborado Por:  Ing. Maryoly Caceres.
		// Fecha de Creación:  02/01/2014  
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$arrcols="";
		$ls_sql= " SELECT sigesp_moneda.*, sigesp_pais.despai as despai".
		         " FROM sigesp_moneda, sigesp_pais ".
				 " WHERE sigesp_moneda.codmon <> '---'".
				 "   AND sigesp_moneda.estmonnac= '"."1"."' ".
				 "   AND sigesp_pais.codpai=sigesp_moneda.codpai".
				 " ORDER BY sigesp_moneda.codmon";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_SCV_C_TARIFACARGOS; METODO->uf_load_paises; ERROR->".$this->io_funcion->uf_convertirmsg(       $this->io_sql->message));
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data)){
				$data=$this->io_sql->obtener_datos($rs_data);
				$ls_codigo=$data["codmon"][1];
			}
		}	
		return $ls_codigo;
	}  // fin de la function uf_load_codigo_moneda_nacional
	
	function uf_load_nombre_moneda_nacional()
	{
		//////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_load_nombre_moneda_nacional
		//	          Access:  public
		// 	       Arguments:  
		//           Returns:  $as_moneda.
		//		 Description:  Devuelve el nombre de la moneda nacional.
		//     Elaborado Por:  Ing. Maryoly Caceres.
		// Fecha de Creación:  02/01/2014  
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$arrcols="";
		$ls_sql= " SELECT sigesp_moneda.*, sigesp_pais.despai as despai".
		         " FROM sigesp_moneda, sigesp_pais ".
				 " WHERE sigesp_moneda.codmon <> '---'".
				 "   AND sigesp_moneda.estmonnac= '"."1"."' ".
				 "   AND sigesp_pais.codpai=sigesp_moneda.codpai".
				 " ORDER BY sigesp_moneda.codmon";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_SCV_C_TARIFACARGOS; METODO->uf_load_paises; ERROR->".$this->io_funcion->uf_convertirmsg(       $this->io_sql->message));
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data)){
				$data=$this->io_sql->obtener_datos($rs_data);			  
				$ls_desmon=$data["denmon"][1];
			}
		}	
		return $ls_desmon;
	}  // fin de la function uf_load_nombre_moneda_nacional
	
	
}   // fin de la class SIGESP_SCV_C_TARIFACARGOS
?> 