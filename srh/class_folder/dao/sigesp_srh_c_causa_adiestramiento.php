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

class sigesp_srh_c_causa_adiestramiento
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
		//         Access: public (sigesp_srh_d_causa_adiestramiento)
		//      Argumento: 
		//	      Returns: Retorna el nuevo c?digo de un causa de adiestramiento
		//    Description: Funcion que genera un c?digo de un causa de adiestramiento
		//	   Creado Por: Maria Beatriz Unda
		// Fecha Creaci?n:13/01/2008							Fecha ?ltima Modificaci?n:13/01/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
    $ls_sql = "SELECT MAX(codcauadi) AS codigo FROM srh_causas_adiestramiento  ";
	$ls_codcauadi =1;
    $arrResultado = $this->io_sql->seleccionar($ls_sql, $la_datos);
	$lb_hay = $arrResultado['valido'];
	$la_datos = $arrResultado['pa_datos'];
	if ($lb_hay)
    $ls_codcauadi = $la_datos["codigo"][0]+1;
    $ls_codcauadi = str_pad ($ls_codcauadi,15,"0",0);
	 
    return $ls_codcauadi;
  }
	
	function uf_srh_select_causa_adiestramiento($as_codcauadi)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_select_causa_adiestramiento
		//      Argumento: $as_codcauadi    // codigo de la causa de adiestramiento
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda de causa de adiestramiento en la tabla de  srh_causas_adiestramiento
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 13/05/2008							Fecha ?ltima Modificaci?n: 13/05/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT * FROM srh_causas_adiestramiento  ".
				  " WHERE codcauadi='".trim($as_codcauadi)."'".
				  " AND codemp='".$this->ls_codemp."'" ;
	 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Causa Adiestramiento M?TODO->uf_srh_select_causa_adiestramiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
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
	}  //  end function uf_srh_select_causa_adiestramiento

	function  uf_srh_insert_causa_adiestramiento($as_codcauadi,$as_dencauadi,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_insert_causa_adiestramiento
		//         areaess: public 
		//      Argumento: $as_codcauadi   // codigo de causa de adiestramiento
	    //                 $as_dencauadi   // denominacion de causa de adiestramiento
	    //		           $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta causa de adiestramiento en la tabla de srh_causas_adiestramiento
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 13/05/2008							Fecha ?ltima Modificaci?n: 13/05/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
        $this->io_sql->begin_transaction();
		$ls_sql = "INSERT INTO srh_causas_adiestramiento (codcauadi, dencauadi,codemp) ".
					" VALUES('".$as_codcauadi."','".$as_dencauadi."','".$this->ls_codemp."')" ;
		
		
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Causa Adiestramiento M?TODO->uf_srh_insert_causa_adiestramiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
				
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insert? la causa de adiestramiento ".$as_codcauadi;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$this->io_sql->commit();
		}
		return $lb_valido;
	} // end  function  uf_srh_insert_causa_adiestramiento

	function uf_srh_update_causa_adiestramiento($as_codcauadi,$as_dencauadi,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_update_causa_adiestramiento
		//         areaess: public 
		//      Argumento: $as_codcauadi        // c?digo de causa de adiestramiento
	    //                 $as_dencauadi       // denominaci?n de causa de adiestramiento
	    //                 $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica causa de adiestramiento en la tabla de srh_causas_adiestramiento
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 13/05/2008							Fecha ?ltima Modificaci?n: 13/05/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $ls_sql = "UPDATE srh_causas_adiestramiento SET   dencauadi='". $as_dencauadi."'". 
				   " WHERE codcauadi='" . $as_codcauadi ."'".
				   " AND codemp='".$this->ls_codemp."'";
		
        $this->io_sql->begin_transaction();
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Causa Adiestramiento M?TODO->uf_srh_update_causa_adiestramiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Modific? la causa de adiestramiento ".$as_codcauadi;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end  function uf_srh_update_causa_adiestramiento
	
	
	function uf_select_causa_adiestramiento_necesidad ($as_codcauadi)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function:uf_select_causa_adiestramiento_necesidad
		//		   Access: private
 		//	    Arguments: as_codcauadi // c?digo de la causa de adiestramiento
		//	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si el ?rea esta asociada a una solicitud de empleo
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 13/05/2008								Fecha ?ltima Modificaci?n : 		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;	    
		$ls_sql= "SELECT codcauadi ".
				 "  FROM srh_dt_causas_adiestramiento ".
				 "  WHERE codemp='".$this->ls_codemp."' ".
				 "    AND codcauadi = '".$as_codcauadi."' ";
				
				 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_msg->message("CLASE->Causa Adiestramiento  M?TODO->uf_select_causa_adiestramiento_necesidad  ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message)); 
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

	function uf_srh_delete_causa_adiestramiento($as_codcauadi,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_delete_causa_adiestramiento
		//         areaess: public 
		//      Argumento: $as_codcauadi       // c?digo de la causa de adiestramiento
	    //                 $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que elimina causa de adiestramiento en la tabla de srh_causas_adiestramiento
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 13/05/2008							Fecha ?ltima Modificaci?n: 13/05/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_existe=true;
		$lb_existe= $this->uf_select_causa_adiestramiento_necesidad ($as_codcauadi);
		if($lb_existe)
		{
				
			$lb_valido=false;
			
		}
		else
		{
			$this->io_sql->begin_transaction();	
			$ls_sql = " DELETE FROM srh_causas_adiestramiento".
						 " WHERE codcauadi= '".$as_codcauadi. "'".
						 "AND codemp='".$this->ls_codemp."'"; 
			

			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->Causa Adiestramiento M?TODO->uf_srh_delete_causa_adiestramiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
				$this->io_sql->rollback();
			}
			else
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion ="Elimin? la causa de adiestramiento ".$as_codcauadi;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////			
				$this->io_sql->commit();
			}
		}
		return array($lb_valido,$lb_existe);
	} // end function uf_srh_delete_causa_adiestramiento
	
	
	
function uf_srh_buscar_causa_adiestramiento($as_codcauadi,$as_dencauadi)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_srh_buscar_causa_adiestramiento
		//         Access: private
		//      Argumento: $as_codcauadi  // codigo de la area
		//	      Returns: Retorna un Booleano
		//    Description: Funcion busca un area  para luego mostrarla
		//	   Creado Por: Mar?a Beatriz Unda
		// Fecha Creaci?n: 13/05/2008							Fecha ?ltima Modificaci?n: 13/05/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$ls_coddestino="txtcodcauadi";
		$ls_dendestino="txtdencauadi";
	
		
		$lb_valido=true;
		$ls_sql="SELECT * FROM srh_causas_adiestramiento".
				" WHERE codcauadi like '".$as_codcauadi."' ".
				"   AND dencauadi like '".$as_dencauadi."' ".
			   " ORDER BY codcauadi";
	 
		$rs_data=$this->io_sql->select($ls_sql);

		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Causa Adiestramiento M?TODO->uf_srh_buscar_causa_adiestramiento( ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{	
		
		    $dom = new DOMDocument('1.0', 'iso-8859-1');
		     $team = $dom->createElement('rows');
		     $dom->appendChild($team);			
			while ($row=$this->io_sql->fetch_row($rs_data)) 
			{
			     
					$ls_codcauadi=$row["codcauadi"];
					$ls_dencauadi=htmlentities ($row["dencauadi"]);
					$row_ = $team->appendChild($dom->createElement('row'));
					$row_->setAttribute("id",$row['codcauadi']);
					$cell = $row_->appendChild($dom->createElement('cell'));   
					$cell->appendChild($dom->createTextNode($row['codcauadi']." ^javascript:aceptar(\"$ls_codcauadi\",\"$ls_dencauadi\",\"$ls_coddestino\",\"$ls_dendestino\");^_self"));
					
					$cell = $row_->appendChild($dom->createElement('cell'));
					$cell->appendChild($dom->createTextNode($ls_dencauadi));												
					$row_->appendChild($cell);
					
					
			
			}
			return $dom->saveXML();
		
			
			
		
		}
      
		
	} // end function uf_srh_buscar_causa_adiestramiento
	

}// end   class sigesp_srh_c_causa_adiestramiento
?>