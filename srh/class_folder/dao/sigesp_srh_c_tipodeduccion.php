<?php
/***********************************************************************************
* @fecha de modificacion: 07/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_srh_c_tipodeduccion
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;
	var $ls_codemp;

	public function __construct($path)
	{   require_once($path."base/librerias/php/general/sigesp_lib_sql.php");
		require_once($path."base/librerias/php/general/sigesp_lib_datastore.php");
		require_once($path."base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once($path."base/librerias/php/general/sigesp_lib_include.php");
		require_once($path."shared/class_folder/sigesp_c_seguridad.php");
		require_once($path."base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_msg=new class_mensajes();
		$this->io_funcion = new class_funciones();
		$this->la_empresa=$_SESSION["la_empresa"];
		$in=new sigesp_include();
		$this->con=$in->uf_conectar();
		$this->io_sql=new class_sql($this->con);
		$this->seguridad= new sigesp_c_seguridad();
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		
		
		
	}
	
	
	function uf_srh_getProximoCodigo()
  {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_getProximoCodigo
		//         Access: public (sigesp_srh_d_concurso)
		//      Argumento: 
		//	      Returns: Retorna el nuevo c?digo de un tipo de deducci?n 
		//    Description: Funcion que genera un c?digo de un tipo de deduccion
		//	   Creado Por: Maria Beatriz Unda
		// Fecha Creaci?n:26/03/08							Fecha ?ltima Modificaci?n:26/03/08
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
    $ls_sql = "SELECT MAX(codtipded) AS codigo FROM srh_tipodeduccion  ";
	$ls_codtipded =1;
    $arrResultado = $this->io_sql->seleccionar($ls_sql, $la_datos);
	$lb_hay = $arrResultado['valido'];
	$la_datos = $arrResultado['pa_datos'];
	if ($lb_hay)
    $ls_codtipded = $la_datos["codigo"][0]+1;
    $ls_codtipded = str_pad ($ls_codtipded,10,"0",0);
	return $ls_codtipded;
  }

	
	function uf_srh_select_tipodeduccion($as_codtipded)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_select_tipodeduccion
		//      Argumento: $as_codtipded    // codigo de tipo de deducci?n 
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda de un tipo de deducci?n  en la tabla de  
		//                 srh_tipodeduccion
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 26/03/08							Fecha ?ltima Modificaci?n: 26/03/08
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT * FROM srh_tipodeduccion  ".
				  " WHERE codtipded='".trim($as_codtipded)."'".
				  " AND codemp='".$this->ls_codemp."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_select_tipodeduccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
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
	}  //  end function uf_srh_select_tipodeduccion

 function  uf_srh_insert_tipodeduccion($as_codtipded,$as_dentipded,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_insert_tipodeduccion
		//      Argumento: $as_codtipded   // codigo de tipo de deducci?n 
	    //                 $as_dentipded   // denominacion de tipo de deducci?n 	    
	    //                 $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un tipo de deducci?n  en la tabla de srh_tipodeduccion
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 26/03/08							Fecha ?ltima Modificaci?n: 26/03/08
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
        $this->io_sql->begin_transaction();
		$ls_sql = "INSERT INTO srh_tipodeduccion (codtipded, dentipded,codemp) ".
				" VALUES('".$as_codtipded."','".$as_dentipded."','".$this->ls_codemp."')" ;
		
		
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_insert_tipodeduccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
				
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insert? el Tipo de Deduccion de Seguro ".$as_codtipded;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$this->io_sql->commit();
		}
		return $lb_valido;
	} // end  function  uf_srh_insert_tipodeduccion

	function uf_srh_update_tipodeduccion($as_codtipded,$as_dentipded,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_update_tipodeduccion
		//      Argumento: $as_codtipded   // codigo de tipo de deducci?n 
	    //                 $as_dentipded   // denominacion de tipo de deducci?n 
	    //                 $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica un tipo de deducci?n  en la tabla de srh_tipodeduccion
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 26/03/08							Fecha ?ltima Modificaci?n: 26/03/08
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $ls_sql = "UPDATE srh_tipodeduccion SET   dentipded='". $as_dentipded ."'". 
				   " WHERE codtipded='" . $as_codtipded ."'".
				   " AND codemp='".$this->ls_codemp."'";
	   
        $this->io_sql->begin_transaction();
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_update_tipodeduccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Modific? el Tipo de Deduccion de Seguro".$as_codtipded;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end  function uf_srh_update_tipodeduccion
	
	
 function uf_select_tipo_deduccion ($as_codtipded)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function:uf_select_tipo_dedccion
		//		   Access: private
 		//	    Arguments: $as_codtipded // c?digo del tipo de deduccion 
		//	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si el tipo de deducci?n esta asociada a una configuracion de tipo de deduccion
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 24/04/2008								Fecha ?ltima Modificaci?n : 		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;	    
		$ls_sql= "SELECT codtipded ".
				 "  FROM srh_dt_tipodeduccion ".
				 "  WHERE codemp='".$this->ls_codemp."' ".
				 "    AND codtipded = '".$as_codtipded."' ";
				
				 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_msg->message("CLASE->tipo deduccion  M?TODO->uf_select_tipo_deduccion  ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message)); 
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
	}
	
	
	
function uf_select_deduccion_personal ($as_codtipded)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function:uf_select_deduccion_personal
		//		   Access: private
 		//	    Arguments: $as_codtipded // c?digo del tipo de deduccion 
		//	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si el tipo de deducci?n esta asociada a un personal
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 09/05/2008								Fecha ?ltima Modificaci?n : 		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;	    
		$ls_sql= "SELECT codtipded ".
				 "  FROM sno_personaldeduccion ".
				 "  WHERE codemp='".$this->ls_codemp."' ".
				 "    AND codtipded = '".$as_codtipded."' ";
				
				 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_msg->message("CLASE->tipo deduccion  M?TODO->uf_select_tipo_deduccion  ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message)); 
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
	}
	
	
function uf_select_deduccion_familiar ($as_codtipded)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_deduccion_familiar
		//		   Access: private
 		//	    Arguments: $as_codtipded // c?digo del tipo de deduccion 
		//	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si el tipo de deducci?n esta asociada a un familiar de un personal
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 09/05/2008								Fecha ?ltima Modificaci?n : 		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;	    
		$ls_sql= "SELECT codtipded ".
				 "  FROM sno_familiardeduccion ".
				 "  WHERE codemp='".$this->ls_codemp."' ".
				 "    AND codtipded = '".$as_codtipded."' ";
				
				 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_msg->message("CLASE->tipo deduccion  M?TODO->uf_select_deduccion_familiar  ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message)); 
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
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function uf_srh_calcular_monto_deduccionnew ($as_codper, $as_codtipded, $as_sexper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_calcular_monto_deduccionnew 
		//		   Access: private
		//	    Arguments: as_codper  // c?digo del personal
		//                 as_codtipded // c?digo del tipo de deducci?n
		//	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si el estudiorealizado est? registrado
		//	   Creado Por: Carlos Zambrano
		// Fecha Creaci?n: 09/06/2008 								Fecha ?ltima Modificaci?n : 		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_sql=" SELECT  srh_dt_tipodeduccion.valprim, srh_dt_tipodeduccion.aporemple,srh_dt_tipodeduccion.edadmin,  ".
				" srh_dt_tipodeduccion.edadmax, srh_dt_tipodeduccion.suelbene,sno_personalnomina.sueper,sno_personal.fecnacper,srh_dt_tipodeduccion.sexbene ". 
				" FROM srh_dt_tipodeduccion,  sno_personalnomina, sno_nomina, sno_personal ".
				" WHERE srh_dt_tipodeduccion.codemp='".$this->ls_codemp."'".
				" AND  srh_dt_tipodeduccion.codtipded='".$as_codtipded."'   ".				
				" AND sno_personalnomina.codemp='".$this->ls_codemp."'  ".
				" AND sno_personalnomina.codper='".$as_codper."' ".
				" AND sno_personalnomina.codemp=sno_nomina.codemp ".
				" AND sno_personalnomina.codnom=sno_nomina.codnom ".
				" AND sno_nomina.espnom='0' ".
				" AND sno_personalnomina.codemp=sno_personal.codemp  ".
				" AND sno_personalnomina.codper=sno_personal.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->personal M?TODO->uf_srh_calcular_monto_deduccionnew ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ls_valor=0;
			$lb_pass=0; 	
			while($row=$this->io_sql->fetch_row($rs_data))
			{  
				$ls_sueldo=trim ($row["suelbene"]);
				$ls_fecnacper=$row["fecnacper"];
				$ld_fecact=	date("Y-m-d");
				$ls_edadper=$this->calcular_edad(strtotime($ls_fecnacper),strtotime($ld_fecact));
				$ls_edadmin=$row["edadmin"];
				$ls_edadmax=$row["edadmax"];
				$li_aporemple=$row["aporemple"];
				$ls_sueldoper=$row["sueper"];
				$li_prima=$row["valprim"];
				$ls_sexoper=$row["sexbene"];				
				 if (($ls_sueldoper >= $ls_sueldo)&&($ls_edadper >= $ls_edadmin)&&($ls_edadper <= $ls_edadmax)&&($ls_sexoper==$as_sexper)&&($lb_pass==0))
				 {
					$ls_valor=  $ls_valor + round ($li_prima * $li_aporemple)/100;
					$lb_pass=1;
				 }
			
			} // Cierre del While
				
		}
		return $ls_valor;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function uf_srh_calcular_monto_deduccionnew_cod ($as_codper, $as_codtipded, $as_sexper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_calcular_monto_deduccionnew 
		//		   Access: private
		//	    Arguments: as_codper  // c?digo del personal
		//                 as_codtipded // c?digo del tipo de deducci?n
		//	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si el estudiorealizado est? registrado
		//	   Creado Por: Carlos Zambrano
		// Fecha Creaci?n: 09/06/2008 								Fecha ?ltima Modificaci?n : 		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_sql=" SELECT  srh_dt_tipodeduccion.valprim, srh_dt_tipodeduccion.aporemple,srh_dt_tipodeduccion.edadmin,  ".
				" srh_dt_tipodeduccion.edadmax, srh_dt_tipodeduccion.suelbene,sno_personalnomina.sueper,sno_personal.fecnacper,srh_dt_tipodeduccion.sexbene,srh_dt_tipodeduccion.coddettipded ". 
				" FROM srh_dt_tipodeduccion,  sno_personalnomina, sno_nomina, sno_personal ".
				" WHERE srh_dt_tipodeduccion.codemp='".$this->ls_codemp."'".
				" AND  srh_dt_tipodeduccion.codtipded='".$as_codtipded."'   ".				
				" AND sno_personalnomina.codemp='".$this->ls_codemp."'  ".
				" AND sno_personalnomina.codper='".$as_codper."' ".
				" AND sno_personalnomina.codemp=sno_nomina.codemp ".
				" AND sno_personalnomina.codnom=sno_nomina.codnom ".
				" AND sno_nomina.espnom='0' ".
				" AND sno_personalnomina.codemp=sno_personal.codemp  ".
				" AND sno_personalnomina.codper=sno_personal.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->personal M?TODO->uf_srh_calcular_monto_deduccionnew_cod ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ls_valor=0;
			$lb_pass=0; 	
			while($row=$this->io_sql->fetch_row($rs_data))
			{  
				$ls_sueldo=trim ($row["suelbene"]);
				$ls_fecnacper=$row["fecnacper"];
				$ld_fecact=	date("Y-m-d");
				$ls_edadper=$this->calcular_edad(strtotime($ls_fecnacper),strtotime($ld_fecact));
				$ls_edadmin=$row["edadmin"];
				$ls_edadmax=$row["edadmax"];
				$li_aporemple=$row["aporemple"];
				$ls_sueldoper=$row["sueper"];
				$li_prima=$row["valprim"];
				$ls_sexoper=$row["sexbene"];
				$ls_codigotot=$row["coddettipded"];				
				 if (($ls_sueldoper >= $ls_sueldo)&&($ls_edadper >= $ls_edadmin)&&($ls_edadper <= $ls_edadmax)&&($ls_sexoper==$as_sexper)&&($lb_pass==0))
				 {
					$ls_valor=  $ls_codigotot;
					$lb_pass=1;
				 }
			
			} // Cierre del While
				
		}
		return $ls_valor;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function uf_srh_calcular_monto_deduccionnewfam ($as_codper, $as_codtipded, $as_sexper, $as_cedfam)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_calcular_monto_deduccionnewfam
		//		   Access: private
		//	    Arguments: as_codper  // c?digo del personal
		//                 as_codtipded // c?digo del tipo de deducci?n
		//                 as-cedfam // c?digo del tipo de deducci?n
		//	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si el estudiorealizado est? registrado
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 09/06/2008 								Fecha ?ltima Modificaci?n : 		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_valor=0; 
		$ls_sql=" SELECT srh_dt_tipodeduccion.valprim,srh_dt_tipodeduccion.aporemple, sno_personalnomina.sueper,  ".
		        "  srh_dt_tipodeduccion.suelbene, srh_dt_tipodeduccion.edadmin, srh_dt_tipodeduccion.edadmax,srh_dt_tipodeduccion.sexbene,srh_dt_tipodeduccion.coddettipded, ".
				"      (SELECT sno_familiar.fecnacfam from sno_familiar          ".
				"        WHERE sno_familiar.codemp= '".$this->ls_codemp."' ".
				"		   AND sno_familiar.codper='".$as_codper."'  ".
				"		   AND sno_familiar.cedfam='".$as_cedfam."') as fecha_nac, ".
				"      (SELECT sno_familiar.sexfam from sno_familiar          ".
				"        WHERE sno_familiar.codemp= '".$this->ls_codemp."' ".
				"		   AND sno_familiar.codper='".$as_codper."'  ".
				"		   AND sno_familiar.cedfam='".$as_cedfam."') as sex_fam ".		
				" FROM srh_dt_tipodeduccion,  sno_personalnomina, sno_nomina ". 			
				"  WHERE srh_dt_tipodeduccion.codemp ='".$this->ls_codemp."' ".
				"  AND  srh_dt_tipodeduccion.codtipded='".$as_codtipded."' ".				
				"  AND   sno_personalnomina.codemp= '".$this->ls_codemp."'  ".
				"  AND   sno_personalnomina.codper='".$as_codper."'   ". 
				"  AND   sno_personalnomina.codemp=sno_nomina.codemp   ".
				"  AND   sno_personalnomina.codnom=sno_nomina.codnom   ". 
				"  AND   sno_nomina.espnom='0' ".					
				"  ORDER BY coddettipded ";	

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->personal M?TODO->uf_srh_calcular_monto_deduccionnewfam ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
         
			$lb_pass=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{ 
				$ls_sueldobene=$row["suelbene"];
				$ls_edadmin=$row["edadmin"];
				$ls_edadmax=$row["edadmax"];
				$ls_valorprima=$row["valprim"];			
				$apor_empleado=$row["aporemple"];								
				$ls_sueldoper=$row["sueper"];
				$fechanac_familiar=$row["fecha_nac"];
				$ld_fecact=	date("Y-m-d");
				$edad_familiar=$this->calcular_edad(strtotime($fechanac_familiar),strtotime($ld_fecact));				
				$ls_sueldoper=$row["sueper"];
				$ls_sexoper=$row["sexbene"];
				$ls_sexfam=$row["sex_fam"];
				if (($ls_sueldoper>=$ls_sueldobene)&&($edad_familiar>=$ls_edadmin)&&($edad_familiar<=$ls_edadmax)&&($ls_sexoper==$ls_sexfam)&&($lb_pass==0))
				{
				    $ls_valor=  round ($ls_valorprima * $apor_empleado)/100;
					$lb_pass=1;
				}
			}///fin del while
		
	 }//fin del else

	return $ls_valor;
	} // end uf_srh_calcular_monto_deduccionnewfam
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function uf_srh_calcular_monto_deduccionnew_codfam ($as_codper, $as_codtipded, $as_sexper, $as_cedfam)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_calcular_monto_deduccionnewfam
		//		   Access: private
		//	    Arguments: as_codper  // c?digo del personal
		//                 as_codtipded // c?digo del tipo de deducci?n
		//                 as-cedfam // c?digo del tipo de deducci?n
		//	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si el estudiorealizado est? registrado
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 09/06/2008 								Fecha ?ltima Modificaci?n : 		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_valor=0; 
		$ls_sql=" SELECT srh_dt_tipodeduccion.valprim,srh_dt_tipodeduccion.aporemple, sno_personalnomina.sueper,  ".
		        "  srh_dt_tipodeduccion.suelbene, srh_dt_tipodeduccion.edadmin, srh_dt_tipodeduccion.edadmax,srh_dt_tipodeduccion.sexbene,srh_dt_tipodeduccion.coddettipded, ".
				"      (SELECT sno_familiar.fecnacfam from sno_familiar          ".
				"        WHERE sno_familiar.codemp= '".$this->ls_codemp."' ".
				"		   AND sno_familiar.codper='".$as_codper."'  ".
				"		   AND sno_familiar.cedfam='".$as_cedfam."') as fecha_nac, ".	
				"      (SELECT sno_familiar.sexfam from sno_familiar          ".
				"        WHERE sno_familiar.codemp= '".$this->ls_codemp."' ".
				"		   AND sno_familiar.codper='".$as_codper."'  ".
				"		   AND sno_familiar.cedfam='".$as_cedfam."') as sex_fam ".	
				" FROM srh_dt_tipodeduccion,  sno_personalnomina, sno_nomina ". 			
				"  WHERE srh_dt_tipodeduccion.codemp ='".$this->ls_codemp."' ".
				"  AND  srh_dt_tipodeduccion.codtipded='".$as_codtipded."' ".				
				"  AND   sno_personalnomina.codemp= '".$this->ls_codemp."'  ".
				"  AND   sno_personalnomina.codper='".$as_codper."'   ". 
				"  AND   sno_personalnomina.codemp=sno_nomina.codemp   ".
				"  AND   sno_personalnomina.codnom=sno_nomina.codnom   ". 
				"  AND   sno_nomina.espnom='0' ".					
				"  ORDER BY coddettipded ";	

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->personal M?TODO->uf_srh_calcular_monto_deduccionnew_codfam ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
         
			$lb_pass=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{ 
				$ls_sueldobene=$row["suelbene"];
				$ls_edadmin=$row["edadmin"];
				$ls_edadmax=$row["edadmax"];
				$ls_valorprima=$row["valprim"];			
				$apor_empleado=$row["aporemple"];								
				$ls_sueldoper=$row["sueper"];
				$fechanac_familiar=$row["fecha_nac"];
				$ld_fecact=	date("Y-m-d");
				$edad_familiar=$this->calcular_edad(strtotime($fechanac_familiar),strtotime($ld_fecact));				
				$ls_sueldoper=$row["sueper"];
				$ls_sexoper=$row["sexbene"];
				$ls_codigotot=$row["coddettipded"];
				$ls_sexfam=$row["sex_fam"];
				if (($ls_sueldoper>=$ls_sueldobene)&&($edad_familiar>=$ls_edadmin)&&($edad_familiar<=$ls_edadmax)&&($ls_sexoper==$ls_sexfam)&&($lb_pass==0))
				{
				    $ls_valor= $ls_codigotot;
					$lb_pass=1;
				}
			}///fin del while
		
	 }//fin del else

	return $ls_valor;
	} // end uf_srh_calcular_monto_deduccionnewfam
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function uf_srh_delete_tipodeduccion($as_codtipded,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_delete_tipodeduccion
		//      Argumento: $as_codtipded   // codigo de tipo de deducci?n 
	    //                 $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que elimina un tipo de deducci?n  en la tabla de srh_tipodeduccion 
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 26/03/08							Fecha ?ltima Modificaci?n: 26/03/08
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=false;
	     $lb_existe=true;
		
		if (($this->uf_select_tipo_deduccion ($as_codtipded)===false)&&
		     ($this->uf_select_deduccion_personal ($as_codtipded)===false)&&
			 ($this->uf_select_deduccion_familiar ($as_codtipded)===false))
		 {
		    $lb_existe=false;
		    $this->uf_srh_eliminar_dt_configuracion_deduccion($as_codtipded,$aa_seguridad);
			$this->io_sql->begin_transaction();	
			$ls_sql = " DELETE FROM srh_tipodeduccion".
						 " WHERE codtipded= '".$as_codtipded. "'".
						 "AND codemp='".$this->ls_codemp."'"; 
			$li_row=$this->io_sql->execute($ls_sql);
			
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_delete_tipodeduccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
				$this->io_sql->rollback();
			}
			else
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion ="Elimin? el Tipo de Deduccion de Seguro ".$as_codtipded;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////			
				$this->io_sql->commit();
			}
		}
		else
		{
		  $lb_existe=true;
		  $lb_valido=false;
		}
		return array($lb_valido,$lb_existe);
	} // end function uf_srh_delete_tipodeduccion
	
	
	
	
	function uf_srh_buscar_tipodeduccion($as_codtipded,$as_dentipded)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_buscar_tipodeduccion
		//      Argumento: $as_codtipded  // codigo de la tipodeduccion
		//	      Returns: Retorna un Booleano
		//    Description: Funcion busca un tipodeduccion  para luego mostrarla
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 04/09/2007							Fecha ?ltima Modificaci?n: 04/09/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$ls_coddestino="txtcodtipded";
		$ls_dendestino="txtdentipded";
	
		
		$lb_valido=true;
		$ls_sql="SELECT * FROM srh_tipodeduccion".
				" WHERE codtipded like '".$as_codtipded."' ".
				"   AND dentipded like '".$as_dentipded."' ".
			   " ORDER BY codtipded";

		$rs_data=$this->io_sql->select($ls_sql);

		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_buscar_tipodeduccion( ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{	
		
		    $dom = new DOMDocument('1.0', 'iso-8859-1');
		     $team = $dom->createElement('rows');
		     $dom->appendChild($team);			
			while ($row=$this->io_sql->fetch_row($rs_data)) 
			{
			     
					$ls_codtipded=$row["codtipded"];
					$ls_dentipded= htmlentities($row["dentipded"]);
					$row_ = $team->appendChild($dom->createElement('row'));
					$row_->setAttribute("id",$row['codtipded']);
					$cell = $row_->appendChild($dom->createElement('cell'));   
					$cell->appendChild($dom->createTextNode($row['codtipded']." ^javascript:aceptar(\"$ls_codtipded\",\"$ls_dentipded\",\"$ls_coddestino\",\"$ls_dendestino\");^_self"));
					$cell = $row_->appendChild($dom->createElement('cell'));
					$cell->appendChild($dom->createTextNode($ls_dentipded));												
					$row_->appendChild($cell);
					
					
			
			}
			return $dom->saveXML();
		
			
			
			

		}
        
		
	} // end function uf_srh_buscar_tipodeduccion(
	
	
	function uf_srh_buscar_configuracion_deduccion($as_codtipded,$as_dentipded)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_buscar_configuracion_deduccion
		//      Argumento: $as_codtipded  // codigo de la tipodeduccion
		//	      Returns: Retorna un Booleano
		//    Description: Funcion busca un tipodeduccion  para luego mostrarla
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 04/09/2007							Fecha ?ltima Modificaci?n: 04/09/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$ls_coddestino="txtcodtipded";
		$ls_dendestino="txtdentipded";
	
		
		$lb_valido=true;
		$ls_sql="SELECT DISTINCT (srh_dt_tipodeduccion.codtipded), srh_tipodeduccion.* FROM srh_tipodeduccion, srh_dt_tipodeduccion ".
		        "  WHERE srh_tipodeduccion.codtipded = srh_dt_tipodeduccion.codtipded ".
				" AND srh_tipodeduccion.codtipded like '".$as_codtipded."' ".
				"   AND srh_tipodeduccion.dentipded like '".$as_dentipded."' ".
			   " ORDER BY srh_tipodeduccion.codtipded";

		$rs_data=$this->io_sql->select($ls_sql);

		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_buscar_configuracion_deduccion( ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{	
		
		    $dom = new DOMDocument('1.0', 'iso-8859-1');
		     $team = $dom->createElement('rows');
		     $dom->appendChild($team);			
			while ($row=$this->io_sql->fetch_row($rs_data)) 
			{
			     
					$ls_codtipded=$row["codtipded"];
					$ls_dentipded= htmlentities ($row["dentipded"]);
					$row_ = $team->appendChild($dom->createElement('row'));
					$row_->setAttribute("id",$row['codtipded']);
					$cell = $row_->appendChild($dom->createElement('cell'));   
					$cell->appendChild($dom->createTextNode($row['codtipded']." ^javascript:aceptar(\"$ls_codtipded\",\"$ls_dentipded\",\"$ls_coddestino\",\"$ls_dendestino\");^_self"));
					$cell = $row_->appendChild($dom->createElement('cell'));
					$cell->appendChild($dom->createTextNode($ls_dentipded));												
					$row_->appendChild($cell);
					
					
			
			}
			return $dom->saveXML();
		
			
			
			

		}
        
		
	} // end function uf_srh_buscar_tipodeduccion(
	

//FUNCIONES PARA EL MANEJO DE LOS DETALLES DE LAS DEDUCCIONES DE SEGURO

function uf_srh_load_configuracion_deduccion ($as_codtipded,$ai_totrows,$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_load_requerimiento_cargo_campos
		//	    Arguments: as_codtipded  // c?digo de la deducci?n 
		//				   ai_totrows  // total de filas del detalle
		//				   ao_object  // objetos del detalle
		//	      Returns: lb_valido True si se ejecuto el buscar ? False si hubo error en el buscar
		//	  Description: Funcion que obtiene todos los campos de una deducci?n 
		// Fecha Creaci?n: 07/04/2007							Fecha ?ltima Modificaci?n : 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		
		switch(strtoupper($_SESSION["ls_gestor"]))
		{
			case "MYSQLT":
				$ls_orden =" CONVERT(coddettipded USING smallint) ";
				break;
			case "MYSQLI":
				$ls_orden =" CONVERT(coddettipded USING smallint) ";
				break;
			case "POSTGRES":
				$ls_orden = " ORDER BY (CAST(coddettipded AS smallint)) ";
				break;					
			case "OCI8PO":
				$ls_orden = " ORDER BY (CAST(coddettipded AS smallint)) ";
				break;					
					
		}
		
		$ls_sql="SELECT * ". 
				"  FROM srh_dt_tipodeduccion ".
				" WHERE srh_dt_tipodeduccion.codemp='".$this->ls_codemp."'".
				"   AND codtipded='".$as_codtipded."'";//.$ls_orden; Comentado por Carlos Zambrano
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_load_configuracion_deduccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
		 $num=$this->io_sql->num_rows($rs_data);
           
		  if ($num!=0) {
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_totrows++;
				
				$punto='"."';
				$coma='","';
				
				$ls_titular=$row["titular"];
				$la_titular[0]="";
				$la_titular[1]="";
				$ls_disable="";
				$ls_sueldo=trim ($row["suelbene"]);
				$ls_edadmin=$row["edadmin"];
				$ls_edadmax=$row["edadmax"];
				$ls_sexo=$row["sexbene"];
				$la_sexo[0]="";
				$la_sexo[1]="";
				$ls_hcm=$row["hcm"];
				$la_hcm[0]="";
				$la_hcm[1]="";
				$ls_nexo=$row["nexfam"];
				$la_nexo[0]="";
				$la_nexo[1]="";
				$la_nexo[2]="";
				$la_nexo[3]="";
				$li_prima=$row["valprim"];
				$li_aporempre=$row["aporempre"];
				$li_aporemple=$row["aporemple"];
						
				 switch($ls_sexo)
				{
					case "F":
						$la_sexo[0]="selected";
						break;
					case "M":
						$la_sexo[1]="selected";
						break;
				}
			   switch($ls_nexo)
				{
					case "C":
						$la_nexo[0]="selected";
						break;
					case "H":
						$la_nexo[1]="selected";
						break;
					case "P":
						$la_nexo[2]="selected";
						break;
					case "E":
						$la_nexo[3]="selected";
						break;
				}
				switch($ls_titular)
				{
					case "S":
						$la_titular[0]="selected";
						$ls_disable="disabled";
						break;
					case "N":
						$la_titular[1]="selected";
						$ls_disable="";
						break;
				}
				switch($ls_hcm)
				{
					case "S":
						$la_hcm[0]="selected";
						break;
					case "N":
						$la_hcm[1]="selected";
						break;
						
					case "1":
						$la_hcm[0]="selected";
						break;
					case "0":
						$la_hcm[1]="selected";
						break;
				}
		
				
				$ao_object[$ai_totrows][1]=" <select name=cmbtitular".$ai_totrows." id=cmbtitular".$ai_totrows." onChange='javascript:chequear_titular(this,".$ai_totrows.");'><option value=''>--Seleccione--</option>
				<option value='S' ".$la_titular[0].">Si</option>
				<option value='N' ".$la_titular[1]." >No</option></select> ";
				$ao_object[$ai_totrows][2]="<input name=txtsueldo".$ai_totrows." type=text id=txtsueldo".$ai_totrows." class=sin-borde size=14 onKeyPress='return(ue_formatonumero(this,".$punto.", ".$coma.",event))' style='text-align:center'  value='".number_format($ls_sueldo,2,",",".")."'>";
				$ao_object[$ai_totrows][3]="<input name=txtedadmin".$ai_totrows." type=text id=txtedadmin".$ai_totrows." class=sin-borde maxlength=2 size=8 onKeyUp='javascript: ue_validarnumero(this);' style='text-align:center' value='".$ls_edadmin."'>";		
				$ao_object[$ai_totrows][4]="<input name=txtedadmax".$ai_totrows." type=text id=txtedadmax".$ai_totrows." class=sin-borde maxlength=3 size=8 onKeyUp='javascript: ue_validarnumero(this);' style='text-align:center' onChange='javascript:valida_edad(this,txtedadmin".$ai_totrows.")';  value='".$ls_edadmax."'>";
				$ao_object[$ai_totrows][5]="<select name=cmbsexper".$ai_totrows." id=cmbsexper".$ai_totrows."><option value=''>--Seleccione--</option>       <option value='F' ".$la_sexo[0]."  > Femenino</option>
		        <option value='M' ".$la_sexo[1]." > Masculino</option></select>";
				$ao_object[$ai_totrows][6]="  <select name=cmbhcm".$ai_totrows." id=cmbhcm".$ai_totrows." >	
				<option value='1' ".$la_hcm[0].">Si</option>
				<option value='0' ".$la_hcm[1]." >No</option></select> ";
				$ao_object[$ai_totrows][7]="<select name=cmbnexfam".$ai_totrows." id=cmbnexfam".$ai_totrows."  ".$ls_disable.">
				  <option value='' selected>--Seleccione--</option>
				  <option value='C' ".$la_nexo[0]." >Conyuge</option>
				  <option value='H' ".$la_nexo[1]."  >Hijo</option>
				  <option value='P' ".$la_nexo[2]."  >Progenitor</option>
				  <option value='E' ".$la_nexo[3]."  >Hermano</option>
				</select>";
			$ao_object[$ai_totrows][8]="<input name=txtprima".$ai_totrows." type=text id=txtprima".$ai_totrows." class=sin-borde size=8 onKeyPress='return(ue_formatonumero(this,".$punto.", ".$coma.",event))'  value='".number_format($li_prima,2,",",".")."'>";
			$ao_object[$ai_totrows][9]="<input name=txtaporempre".$ai_totrows." type=text id=txtaporempre".$ai_totrows." class=sin-borde size=8 onKeyPress='return(ue_formatonumero(this,".$punto.", ".$coma.",event))' value='".number_format($li_aporempre,2,",",".")."' >";
			$ao_object[$ai_totrows][10]="<input name=txtaporemple".$ai_totrows." type=text id=txtaporemple".$ai_totrows." class=sin-borde size=8 onKeyPress='return(ue_formatonumero(this,".$punto.", ".$coma.",event))' value='".number_format($li_aporemple,2,",",".")."'>";
			$ao_object[$ai_totrows][11]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");  align=center><img src=../../../../shared/imagebank/tools15/aprobado.gif alt=Aceptar width=15 height=15 border=0 align=center></a>";
			$ao_object[$ai_totrows][12]="<a href=javascript:uf_delete_dt(".$ai_totrows.");   align=center><img src=../../../../shared/imagebank/tools15/eliminar.gif alt=Eliminar width=15 height=15 border=0 align=center></a>";				
							
			}
			$this->io_sql->free_result($rs_data);
			}
		else 
		 {
		    $this->io_msg->message("No hay detalles asociados a esa Deducci?n de Seguro.");
	 		$ai_totrows=0;	
			
		
		  }
			$arrResultado['ai_totrows']=$ai_totrows;
			$arrResultado['ao_object']=$ao_object;
			$arrResultado['lb_valido']=$lb_valido;
			return $arrResultado;		
		}
	}
	

//FUNCIONES PARA GUARDAR Y ELIMINAR LAS DEDUCCIONES DE SEGURO

function uf_srh_guardar_configuracion_deduccion($ao_deduccion, $aa_seguridad)
  { 
  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_guardar_configuracion_deduccion																	
	  	//      Argumento: $ao_requerimiento    // arreglo con los datos de la deduccion 							
		//                 $aa_seguridad    //   arreglo de registro de seguridad                                               
		//	      Returns: Retorna un Booleano																					
		//    Description:Funcion que inserta o modifica un detalle de las deducciones  la tabla 
		//                 srh_dt_tipodeduccion    
		//	   Creado Por: Maria Beatriz Unda																				    
		// Fecha Creaci?n: 08/04/2008							Fecha ?ltima Modificaci?n: 08/04/2008							
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	
  	 //Borramos los registros anteriores 
	$this-> uf_srh_eliminar_dt_configuracion_deduccion($ao_deduccion->codtipded, $aa_seguridad);
	  
	//Ahora guardamos
	$lb_guardo = true;
	$li_det = 0;
	while (($li_det < count((array)$ao_deduccion->deduccion)) &&
	       ($lb_guardo))
	{
	  $lb_guardo = $this->uf_srh_guardar_dt_configuracion_deduccion($ao_deduccion->deduccion[$li_det], $aa_seguridad);
	  $li_det++;
	}
	
	return $lb_guardo;  
  }
	
	


function uf_srh_guardar_dt_configuracion_deduccion($ao_deduccion, $aa_seguridad)
  { 
  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_guardar_dt_configuracion_deduccion															     														
		//      Argumento: $ao_deduccion    // arreglo con los datos de los detalle de las deducciones 				
		//                 $as_operacion    //  variable que guarda la operacion a ejecutar (insertar o modificar)              
		//                 $aa_seguridad    //   arreglo de registro de seguridad                                               
		//	      Returns: Retorna un Booleano																					
		//    Description: Funcion que inserta o modifica un detalle de las deducciones  la tabla 
		//                 srh_dt_tipodeduccion           
		//	   Creado Por: Maria Beatriz Unda																				    
		// Fecha Creaci?n: 08/04/2008							Fecha ?ltima Modificaci?n: 08/04/2008							
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	
  
	 $this->io_sql->begin_transaction();
	 $ao_deduccion->sueldo=str_replace(".","",$ao_deduccion->sueldo);
	 $ao_deduccion->sueldo=str_replace(",",".",$ao_deduccion->sueldo);
	 
	 $ao_deduccion->prima=str_replace(".","",$ao_deduccion->prima);
	 $ao_deduccion->prima=str_replace(",",".",$ao_deduccion->prima);
	 
	 $ao_deduccion->aporempre=str_replace(".","",$ao_deduccion->aporempre);
	 $ao_deduccion->aporempre=str_replace(",",".",$ao_deduccion->aporempre);
	 
	 $ao_deduccion->aporemple=str_replace(".","",$ao_deduccion->aporemple);
	 $ao_deduccion->aporemple=str_replace(",",".",$ao_deduccion->aporemple);	
		
	 	 
	  $ls_sql = "INSERT INTO srh_dt_tipodeduccion (codtipded,coddettipded, titular, suelbene,edadmin, edadmax, sexbene, nexfam, hcm, valprim, aporempre, aporemple, codemp) ".	  
	            " VALUES ('$ao_deduccion->codtipded','$ao_deduccion->coddettipded','$ao_deduccion->titular','$ao_deduccion->sueldo','$ao_deduccion->edadmin','$ao_deduccion->edadmax','$ao_deduccion->sexo','$ao_deduccion->nexo','$ao_deduccion->hcm','$ao_deduccion->prima','$ao_deduccion->aporempre','$ao_deduccion->aporemple','".$this->ls_codemp."')";

		/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
	     		$ls_descripcion ="Insert? el detalle de deducci?n  ".$ao_deduccion->coddettipded;				
	    		$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"], 
	   							$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
										$aa_seguridad["ventanas"],$ls_descripcion);
		/////////////////////////////////         SEGURIDAD               /////////////////////////////		
	
	$lb_guardo = $this->io_sql->execute($ls_sql);

     if($lb_guardo===false)
		{
			$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_guardar_dt_configuracion_deduccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
				$lb_valido=true;
				$this->io_sql->commit();
		}
		
	return $lb_guardo;
  }
  
  
 
function uf_srh_eliminar_dt_configuracion_deduccion($as_codtipded, $aa_seguridad)
  {
  
         /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_eliminar_dt_configuracion_deduccion																
		//        access:  public (sigesp_srh_dt_tipodeduccion)														
		//      Argumento: $as_codtipded        // c?digo del tipo de deducci?nm 
		//                 $aa_seguridad    //  arreglo de registro de seguridad                                                
		//	      Returns: Retorna un Booleano																				    
		//    Description: Funcion que elimina un detalle de deducci?n  en la tabla srh_dt_tipodeduccion   		//	        // Creado Por: Maria Beatriz Unda																				    
		// Fecha Creaci?n: 20/02/2008							Fecha ?ltima Modificaci?n: 20/02/2008							
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
    $this->io_sql->begin_transaction();	
    $ls_sql = "DELETE FROM srh_dt_tipodeduccion ".
	          " WHERE codtipded='$as_codtipded'  AND codemp='".$this->ls_codemp."'";
			  

	$lb_borro=$this->io_sql->execute($ls_sql);
	if($lb_borro===false)
	 {
		$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_eliminar_dt_configuracion_deduccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		$lb_valido=false;
		$this->io_sql->rollback();
	 }
	else
	 {
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion ="Elimin? el detalles de la deducci?n ".$as_codtipded;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////			
				$this->io_sql->commit();
			}
			
	
	return $lb_borro;
	
  }

	///--------------------------------------------------------------------------------------------------------------------------------
    function uf_srh_buscar_deduccion($as_codper,$as_codtipded,$as_tipo,$as_sueldo,$as_edad,$as_sexo,$as_valor) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_buscar_deduccion
		//	    Arguments: as_codper      // c?digo de la deducci?n
		//                 as_codtipded  // c?digo de la deducci?n 
		//				   as_tipo      // 1 si es deduccion del personal y 2 si es aporte patronal
		//				   as_valor    // valor de la deducci?n
		//	      Returns: lb_valido True si se ejecuto el buscar ? False si hubo error en el buscar
		//	  Description: Funcion que obtiene el valor de una deducci?n de personal 
		//     Creado Por: Maria Beatriz Unda		
		// Fecha Creaci?n: 29/05/2008							Fecha ?ltima Modificaci?n : 08/03/2010 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT srh_dt_tipodeduccion.suelbene, srh_dt_tipodeduccion.edadmin, srh_dt_tipodeduccion.edadmax,".
				" 		srh_dt_tipodeduccion.valprim, srh_dt_tipodeduccion.aporempre, srh_dt_tipodeduccion.aporemple, ". 
				"		srh_dt_tipodeduccion.sexbene, srh_dt_tipodeduccion.hcm, sno_personal.hcmper	".
				"  FROM sno_personaldeduccion ".
				" INNER JOIN sno_personal  ".
				"    ON sno_personaldeduccion.codemp='".$this->ls_codemp."'".
				"   AND sno_personaldeduccion.codper='".$as_codper."'".
				"   AND sno_personaldeduccion.codtipded='".$as_codtipded."'  ".
				"   AND sno_personaldeduccion.codemp = sno_personal.codemp  ".
				"   AND sno_personaldeduccion.codper = sno_personal.codper  ".
				" INNER JOIN srh_dt_tipodeduccion  ".
				"    ON sno_personaldeduccion.codemp='".$this->ls_codemp."'".
				"   AND sno_personaldeduccion.codper='".$as_codper."'".
				"   AND sno_personaldeduccion.codtipded='".$as_codtipded."'  ".
				"   AND srh_dt_tipodeduccion.titular='S'  ".
				"   AND sno_personaldeduccion.codemp=srh_dt_tipodeduccion.codemp  ".				
				"   AND sno_personaldeduccion.codtipded = srh_dt_tipodeduccion.codtipded   ".	
			    " ORDER BY sno_personaldeduccion.codtipded "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_buscar_deduccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$as_valor=0; 
			$lb_listo=false;
			while(!$rs_data->EOF)
			{   
                $ls_sueldo=trim ($rs_data->fields["suelbene"]);
                $ls_edadmin=$rs_data->fields["edadmin"];
                $ls_edadmax=$rs_data->fields["edadmax"];                    
                $ls_sexo=$rs_data->fields["sexbene"];   
                $ls_hcm=$rs_data->fields["hcm"];               
                $li_prima=$rs_data->fields["valprim"];
                $li_aporempre=$rs_data->fields["aporempre"];
                $li_aporemple=$rs_data->fields["aporemple"];
                $ls_hcmper=$rs_data->fields["hcmper"]; 
                if (trim($ls_hcm)=='S')   
                {   
                    $ls_hcm='1';   
                }   
                elseif (trim($ls_hcm)=='N')   
                {   
                    $ls_hcm='0';   
                }   
                if (($ls_hcmper!="")&&($ls_hcm=='1')) 
                {                      
					if (($as_sueldo >= $ls_sueldo)&&($as_edad >= $ls_edadmin)&&($as_edad <= $ls_edadmax)&&($as_sexo=="'".$ls_sexo."'")&&($ls_hcm==$ls_hcmper))   
					{                         
						switch($as_tipo)   
						{   
							case "1":   
								$as_valor=  round ($li_prima * $li_aporemple)/100;   
							break;   
							case "2":                               
								$as_valor=  round ($li_prima * $li_aporempre)/100;   
							break;   
						}
						break;// al encontrar la primera coincidencia terminamos el ciclo
					}   
				}   
				else   
				{     
					if (($as_sueldo >= $ls_sueldo)&&($as_edad >= $ls_edadmin)&&($as_edad <= $ls_edadmax)&&($as_sexo=="'".$ls_sexo."'"))   
					{      
						switch($as_tipo)   
						{ 
							case "1":
								$as_valor= round ($li_prima * $li_aporemple)/100;
							break;
							case "2":
								$as_valor= round ($li_prima * $li_aporempre)/100;
							break;
						}
						break;// al encontrar la primera coincidencia terminamos el ciclo
					}   
				} 
				$rs_data->MoveNext();
			} // Cierre del While
		}
		return $lb_valido;
	}//fin de function uf_srh_buscar_deduccion
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function calcular_edad($fecha_nac,$fecha_hasta)
	{  	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: calcular_edad
		//	    Arguments: fecha_nac  // fecha de nacimiento
		//                 fecha_hasta	 fecha hasta 	 
		//	      Returns: anos
		//	  Description: Funcion que obtiene la edad de una persona dada una fecha de nacimiento
		//     Creado Por: Maria Beatriz Unda		
		// Fecha Creaci?n: 29/05/2008							Fecha ?ltima Modificaci?n : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$c = date("Y",$fecha_nac);	   
		$b = date("m",$fecha_nac);	  
		$a = date("d",$fecha_nac); 	
		$anos = date("Y",$fecha_hasta)-$c; 
		if(date("m",$fecha_hasta)-$b > 0)
		{
		}
		elseif(date("m",$fecha_hasta)-$b == 0)
		{
			if(date("d",$fecha_hasta)-$a <= 0)
			{		  
				$anos = $anos-1;	        
			}
		}
		else
		{		  
			$anos = $anos-1;		          
		}  
		return $anos;	 
	}// fin de function calcular_edad($fecha_nac,$fecha_hasta)
	//-------------------------------------------------------------------------------------------------------------------------------
    
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_srh_buscar_deduccion_familiar($as_codper,$as_codtipded,$as_tipo,$as_sueldo,$as_fecha_has, $as_valor) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_buscar_deduccion_familiar
		//	    Arguments: as_codper      // c?digo de la deducci?n
		//                 as_codtipded  // c?digo de la deducci?n 
		//				   as_tipo      // 1 si es deduccion del personal y 2 si es aporte patronal
		//                 as_sueldo    // sueldo del empleado que posee nexo con el familiar
		//				   as_valor    // valor de la deducci?n
		//	      Returns: lb_valido True si se ejecuto el buscar ? False si hubo error en el buscar
		//	  Description: Funcion que obtiene el valor de una deducci?n del familiar
		//     Creado Por: Ing. Jennifer Rivero	
		// Fecha Creaci?n: 29/05/2008							Fecha ?ltima Modificaci?n : 08/03/2010  
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT srh_dt_tipodeduccion.suelbene, srh_dt_tipodeduccion.edadmin, srh_dt_tipodeduccion.edadmax,".
				" 		srh_dt_tipodeduccion.valprim, srh_dt_tipodeduccion.aporempre, srh_dt_tipodeduccion.aporemple, ". 
				"		srh_dt_tipodeduccion.sexbene, srh_dt_tipodeduccion.hcm, srh_dt_tipodeduccion.nexfam, sno_familiar.hcfam,	".
				"		sno_familiar.hcmfam, (sno_familiar.nexfam) AS nexofam, (sno_familiar.sexfam) AS sexofam, sno_familiar.fecnacfam, sno_familiardeduccion.cedfam ".
				"  FROM sno_familiardeduccion ".
				" INNER JOIN sno_familiar  ".
				"    ON sno_familiardeduccion.codemp='".$this->ls_codemp."'".
				"   AND sno_familiardeduccion.codper='".$as_codper."'".
				"   AND sno_familiardeduccion.codtipded='".$as_codtipded."'  ".
				"   AND sno_familiardeduccion.codemp = sno_familiar.codemp  ".
				"   AND sno_familiardeduccion.codper = sno_familiar.codper  ".
				"   AND sno_familiardeduccion.cedfam = sno_familiar.cedfam  ".
				" INNER JOIN srh_dt_tipodeduccion  ".
				"    ON sno_familiardeduccion.codemp='".$this->ls_codemp."'".
				"   AND sno_familiardeduccion.codper='".$as_codper."'".
				"   AND sno_familiardeduccion.codtipded='".$as_codtipded."'  ".
				"   AND srh_dt_tipodeduccion.titular='N'  ".
				"   AND sno_familiardeduccion.codemp=srh_dt_tipodeduccion.codemp  ".				
				"   AND sno_familiardeduccion.codtipded = srh_dt_tipodeduccion.codtipded   ".	
			    " ORDER BY sno_familiardeduccion.codtipded "; 
		$rs_data=$this->io_sql->execute($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_buscar_deduccion_familiar ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$as_valor=0; 
			while(!$rs_data->EOF)
			{							        
				$ls_cedfam=$rs_data->fields["cedfam"];
				$ls_sueldobene=$rs_data->fields["suelbene"];
				$ls_edadmin=$rs_data->fields["edadmin"];
				$ls_edadmax=$rs_data->fields["edadmax"];
				$ls_sexoben=$rs_data->fields["sexbene"];
				$ls_nexo=$rs_data->fields["nexfam"];
				$ls_hcm=$rs_data->fields["hcm"];////de la tabla dt_tipodeduccion
				$ls_valorprima=$rs_data->fields["valprim"];
				$apor_empresa=$rs_data->fields["aporempre"];
				$apor_empleado=$rs_data->fields["aporemple"];
				$hc_familiar=$rs_data->fields["hcfam"];
				$hcm_familiar=$rs_data->fields["hcmfam"];
				$nexo_familiar=$rs_data->fields["nexofam"];
				$sexo_familiar=$rs_data->fields["sexofam"];
				$fechanac_familiar=$rs_data->fields["fecnacfam"];
				$edad_familiar=$this->calcular_edad(strtotime($fechanac_familiar),strtotime($as_fecha_has));
				if (trim($ls_hcm)=='S')
				{   
					$ls_hcm='1';   
				}   
				elseif (trim($ls_hcm)=='N')   
				{   
					$ls_hcm='0';   
				}               
				if ($hcm_familiar!="")   
				{       
					if (($as_sueldo>=$ls_sueldobene)&&($edad_familiar>=$ls_edadmin)&&($edad_familiar<=$ls_edadmax)&&($ls_sexoben==$sexo_familiar)&&($ls_nexo==$nexo_familiar)&&($ls_hcm==$hcm_familiar))   
					{                         
						switch($as_tipo)
						{              
							case "1":    
								$as_valor= $as_valor + round ($ls_valorprima * $apor_empleado)/100;
							break;
							
							case "2": 
								$as_valor=  $as_valor + round ($ls_valorprima * $apor_empresa)/100;
							break; 
						}            
					}                           
				}                   
				else                                                
				{                   
					if (($as_sueldo>=$ls_sueldobene)&&($edad_familiar>=$ls_edadmin)&&($edad_familiar<=$ls_edadmax)&&($ls_sexoben==$sexo_familiar)&&($ls_nexo==$nexo_familiar))   
					{   
						switch($as_tipo)   
						{   
							case "1":   
								$as_valor= $as_valor + round ($ls_valorprima * $apor_empleado)/100;   
							break;   
							case "2":                                       
								$as_valor= $as_valor + round ($ls_valorprima * $apor_empresa)/100;   
							break;   
						}   
					}  
				}				
				$rs_data->MoveNext();
			} // Cierre del While
		}
		$as_valor=number_format($as_valor,2,".","");
		return $lb_valido;			    
	}// fin de  uf_srh_buscar_deduccion_familiar
	//---------------------------------------------------------------------------------------------------------------------------------


function uf_srh_buscar_detalles_deducciones($as_codtipded, $as_tipo, $as_nexfam, $as_sexper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_buscar_detalles_deducciones
		//	    Arguments: as_codtipded  // c?digo de la deducci?n 
		//	      Returns: lb_valido True si se ejecuto el buscar ? False si hubo error en el buscar
		//	  Description: Funcion que obtiene todos los campos de una deducci?n 
		// Fecha Creaci?n: 07/04/2007							Fecha ?ltima Modificaci?n : 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";	
		switch(strtoupper($_SESSION["ls_gestor"]))
		{
			case "MYSQLT":
				$ls_orden ="CONVERT(coddettipded USING smallint) ";
				break;
			case "MYSQLI":
				$ls_orden ="CONVERT(coddettipded USING smallint) ";
				break;
			case "POSTGRES":
				$ls_orden = " ORDER BY (CAST(coddettipded AS smallint)) ";
				break;					
			case "OCI8PO":
				$ls_orden = " ORDER BY (CAST(coddettipded AS smallint)) ";
				break;					
					
		}
		
		switch($as_tipo)
		{
			case "dedper":
				$ls_criterio =" AND titular ='S' ";
				break;
			case "dedfam":
				$ls_criterio = " AND titular ='N' ";
				break;					
					
		}
		if ($as_sexper!="")
		{
			$ls_criterio =$ls_criterio." AND sexbene ='".trim($as_sexper)."' ";
		}
		if ($as_nexfam!="")
		{
			$ls_criterio =$ls_criterio." AND nexfam ='".trim($as_nexfam)."' ";
		}
		
		$ls_sql="SELECT * ". 
				"  FROM srh_dt_tipodeduccion ".
				" WHERE srh_dt_tipodeduccion.codemp='".$this->ls_codemp."'".
				"   AND codtipded='".$as_codtipded."'".$ls_criterio.$ls_orden;			
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->tipodeduccion M?TODO->uf_srh_buscar_detalles_deducciones ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
		 
			 $dom = new DOMDocument('1.0', 'iso-8859-1');
		     $team = $dom->createElement('rows');
		     $dom->appendChild($team);	
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				
				$ls_coddettipded=$row["coddettipded"];
				$ls_titular=$row["titular"];				
				$ls_sueldo=number_format(trim($row["suelbene"]),2,',','.');
				$ls_edadmin=$row["edadmin"];
				$ls_edadmax=$row["edadmax"];
				$ls_sexo=$row["sexbene"];				
				$ls_hcm=$row["hcm"];				
				$ls_nexo=$row["nexfam"];				
				$li_prima=number_format($row["valprim"],2,',','.');
				$li_aporempre=number_format($row["aporempre"],2,',','.');
				$li_aporemple=number_format($row["aporemple"],2,',','.');
						
				 switch($ls_sexo)
				{
					case "F":
						$ls_sexo="Femenino";
						break;
					case "M":
						$ls_sexo="Masculino";
						break;
				}
			   switch($ls_nexo)
				{
					case "C":
						$ls_nexo="Conyugue";
						break;
					case "H":
						$ls_nexo="Hijo";
						break;
					case "P":
						$ls_nexo="Padre";
						break;
					case "E":
						$ls_nexo="Hermano";
						break;
					default : 
						$ls_nexo="Titular";
						break;
				}
				switch($ls_titular)
				{
					case "S":
						$ls_titular="Si";
						break;
					case "N":
						$ls_titular="No";						
						break;
				}
				switch($ls_hcm)
				{
					case "1":
						$ls_hcm="Si";
						break;
					case "0":
						$ls_hcm="No";
						break;
				}
				
				
				$row_ = $team->appendChild($dom->createElement('row'));
				$row_->setAttribute("id",$ls_coddettipded);
				$cell = $row_->appendChild($dom->createElement('cell'));   
				$cell->appendChild($dom->createTextNode($ls_coddettipded." ^javascript:aceptar(\"$ls_coddettipded\");^_self"));
				
				$cell = $row_->appendChild($dom->createElement('cell'));
				$cell->appendChild($dom->createTextNode($ls_titular));												
				$row_->appendChild($cell);

				$cell = $row_->appendChild($dom->createElement('cell'));
				$cell->appendChild($dom->createTextNode($ls_sueldo));												
				$row_->appendChild($cell);
				
				$cell = $row_->appendChild($dom->createElement('cell'));
				$cell->appendChild($dom->createTextNode($ls_edadmin));												
				$row_->appendChild($cell);
		
				$cell = $row_->appendChild($dom->createElement('cell'));
				$cell->appendChild($dom->createTextNode($ls_edadmax));												
				$row_->appendChild($cell);
				
				$cell = $row_->appendChild($dom->createElement('cell'));
				$cell->appendChild($dom->createTextNode($ls_sexo));												
				$row_->appendChild($cell);
				
				$cell = $row_->appendChild($dom->createElement('cell'));
				$cell->appendChild($dom->createTextNode($ls_hcm));												
				$row_->appendChild($cell);
				
				$cell = $row_->appendChild($dom->createElement('cell'));
				$cell->appendChild($dom->createTextNode($ls_nexo));												
				$row_->appendChild($cell);
				
				$cell = $row_->appendChild($dom->createElement('cell'));
				$cell->appendChild($dom->createTextNode($li_prima));												
				$row_->appendChild($cell);
				
				$cell = $row_->appendChild($dom->createElement('cell'));
				$cell->appendChild($dom->createTextNode($li_aporempre));												
				$row_->appendChild($cell);
				
				$cell = $row_->appendChild($dom->createElement('cell'));
				$cell->appendChild($dom->createTextNode($li_aporemple));												
				$row_->appendChild($cell);
		
				
							
							
			}
			$this->io_sql->free_result($rs_data);		
		 	return $dom->saveXML();
		}
		
	}// end function uf_srh_buscar_detalles_deducciones
}// end   class sigesp_srh_c_tipodeduccion
?>