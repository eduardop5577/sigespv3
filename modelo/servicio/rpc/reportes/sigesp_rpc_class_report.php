<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_rpc_class_report
{
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct($conn)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_rpc_class_report
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: 
		// Fecha Creación: 24/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funcion = new class_funciones();
		require_once("../../../base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		$this->io_msg= new class_mensajes();		
	}// end function sigesp_rpc_class_report
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_beneficiario($ai_orden,$as_cedula1,$as_cedula2,$lb_valido) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_beneficiario
		//         Access: public 
		//	    Arguments: ai_orden // Orden del reportes
		//	  			   as_cedula1 // Rango de cédula Desde		  
		//	  			   as_cedula2 // Rango de cédula Hasta	  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del beneficiario
		//	   Creado Por: 
		// Fecha Creación: 24/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp = $_SESSION["la_empresa"]["codemp"];
		$lb_valido=false;
		$ls_criterio="";
		switch($ai_orden)
		{
			case "0":
				$ls_orden="ced_bene";
				break;
			case "1":
				$ls_orden="nombene";
				break;
			default:
				$ls_orden="apebene";
				break;
		}
		if((!empty($as_cedula1)) && (!empty($as_cedula2)))
		{
			$ls_criterio="   AND ced_bene BETWEEN '".$as_cedula1."' AND '".$as_cedula2."'";
		}
		$ls_sql="SELECT ced_bene,nombene,apebene,sc_cuenta,rifben,telbene,dirbene,fecregben ".
				"  FROM rpc_beneficiario ".
				" WHERE codemp='".$ls_codemp."' ".$ls_criterio.				
				" ORDER BY ".$ai_orden."  ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$lb_valido = false;
			$this->io_msg->message("CLASE->Report MÉTODO->uf_prenomina_personal ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{	  
			$li_numrows = $this->io_sql->num_rows($rs_data);	   
			if($li_numrows>0)
			{
				$lb_valido=true;
			}
		}
		$arrResultado["rs_data"] = $rs_data;
		$arrResultado["lb_valido"] = $lb_valido;		
		return $arrResultado;         
	}// end function uf_select_beneficiario
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_proveedores($as_codemp,$ai_orden,$as_codprov1,$as_codprov2,$lb_valido) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_proveedores
		//         Access: public 
		//	    Arguments: as_codemp // código de Empresa
		//	    		   ai_orden // Orden del reportes
		//	  			   as_codprov1 // Rango de proveedor Desde		  
		//	  			   as_codprov2 // Rango de proveedor Hasta	  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del Proveedor
		//	   Creado Por: 
		// Fecha Creación: 24/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		switch($ai_orden)
		{
			case "0":
				$ls_orden="P.cod_pro";
				break;
			default:
				$ls_orden="P.nompro";
				break;
		}
		if((empty($as_codprov1)) || (empty($as_codprov2)))
		{
			$as_codprov1="0000000000";
			$as_codprov2="9999999999";			
		}
		$ls_sql="SELECT P.*,C.despai as pais, sigesp_estados.desest AS estado ".
				"  FROM rpc_proveedor P, sigesp_pais C , sigesp_estados ".
				" WHERE P.codemp='".$as_codemp."'  ".
				"   AND P.cod_pro BETWEEN '".$as_codprov1."' AND '".$as_codprov2."' ".
				"   AND P.codpai=C.codpai ".
				"   AND P.codpai=sigesp_estados.codpai ".
				"   AND P.codest=sigesp_estados.codest ".
				" ORDER BY ".$ls_orden." ASC ";
		$rs_data = $this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_RPC_CLASS_REPORT; METODO->uf_select_proveedores; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$li_numrows=$this->io_sql->num_rows($rs_data);	   
			if ($li_numrows>0)
			{
				$lb_valido=true;
			}
		}
		$arrResultado["rs_data"] = $rs_data;
		$arrResultado["lb_valido"] = $lb_valido;		
		return $arrResultado;         
	}// end function uf_select_proveedores
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_especialidadproveedor($as_codprov,$lb_valido) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_especialidadproveedor
		//         Access: public 
		//	    Arguments: as_codprov // código del Proveedor
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca las especialidades del Proveedor
		//	   Creado Por: 
		// Fecha Creación: 24/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_especialidad="";
		$ls_codemp = $_SESSION["la_empresa"]["codemp"];
		$ls_sql="SELECT rpc_especialidad.codesp, rpc_especialidad.denesp  ".
				"  FROM rpc_espexprov, rpc_especialidad ".
				" WHERE rpc_espexprov.codemp='".$ls_codemp."'  ".
				"   AND rpc_espexprov.cod_pro = '".$as_codprov."' ".
				"   AND rpc_espexprov.codesp <> '---' ".
				"	AND rpc_espexprov.codesp=rpc_especialidad.codesp ".
				" ORDER BY rpc_especialidad.denesp ASC ";
		$rs_data = $this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_RPC_CLASS_REPORT; METODO->uf_select_proveedores; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_especialidad=$ls_especialidad.", ".$row["codesp"]." - ".$row["denesp"];
			}
			$ls_especialidad=substr($ls_especialidad,1,strlen($ls_especialidad)-1);
		}
		$arrResultado["ls_especialidad"] = $ls_especialidad;
		$arrResultado["lb_valido"] = $lb_valido;		
		return $arrResultado;         
	}// end function uf_load_especialidadproveedor
	//-----------------------------------------------------------------------------------------------------------------------------------
function uf_load_especialidadproveedor2($as_codprov,$lb_valido) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_especialidadproveedor
		//         Access: public 
		//	    Arguments: as_codprov // código del Proveedor
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca las especialidades del Proveedor
		//	   Creado Por: 
		// Fecha Creación: 24/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_especialidad="";
		$ls_codemp = $_SESSION["la_empresa"]["codemp"];
		$ls_sql="SELECT rpc_especialidad.codesp, rpc_especialidad.denesp  ".
				"  FROM rpc_espexprov, rpc_especialidad ".
				" WHERE rpc_espexprov.codemp='".$ls_codemp."'  ".
				"   AND rpc_espexprov.cod_pro = '".$as_codprov."' ".
				"   AND rpc_espexprov.codesp <> '---' ".
				"	AND rpc_espexprov.codesp=rpc_especialidad.codesp ".
				" ORDER BY rpc_especialidad.denesp ASC ";
		$rs_data = $this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_RPC_CLASS_REPORT; METODO->uf_select_proveedores; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_especialidad=$ls_especialidad.", ".$row["denesp"];
			}
			$ls_especialidad=substr($ls_especialidad,1,strlen($ls_especialidad)-1);
		}
		$arrResultado["ls_especialidad"] = $ls_especialidad;
		$arrResultado["lb_valido"] = $lb_valido;		
		return $arrResultado;         
	}// end function uf_load_especialidadproveedor2


//----------------------------------------------------------------------------------------------------------------------------------

function uf_load_niveldecontratacionporproveedor($as_codprov,$lb_valido) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_especialidadproveedor
		//         Access: public 
		//	    Arguments: as_codprov // código del Proveedor
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca las especialidades del Proveedor
		//	   Creado Por: 
		// Fecha Creación: 24/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nivel="";
		$ls_codemp = $_SESSION["la_empresa"]["codemp"];
		$ls_sql="SELECT rpc_niveles.codniv, rpc_niveles.desniv  ".
				"  FROM rpc_clasifxprov, rpc_niveles ".
				" WHERE rpc_clasifxprov.codemp='".$ls_codemp."'  ".
				"   AND rpc_clasifxprov.cod_pro = '".$as_codprov."' ".
				"   AND rpc_clasifxprov.codniv <> '---' ".
				"	AND rpc_clasifxprov.codniv=rpc_niveles.codniv ".
				" ORDER BY rpc_niveles.desniv ASC "; 
		$rs_data = $this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_RPC_CLASS_REPORT; METODO->uf_select_proveedores; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_nivel=$ls_nivel.", ".$row["desniv"];
			}
			$ls_nivel=substr($ls_nivel,1,strlen($ls_nivel)-1);
		}
		$arrResultado["ls_nivel"] = $ls_nivel;
		$arrResultado["lb_valido"] = $lb_valido;		
		return $arrResultado;         
	}// end 

//-----------------------------------------------------------------------------------------------------------------------------------

function uf_load_niveldeclasificacionporproveedor($as_codprov,$lb_valido) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_especialidadproveedor
		//         Access: public 
		//	    Arguments: as_codprov // código del Proveedor
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca las especialidades del Proveedor
		//	   Creado Por: 
		// Fecha Creación: 24/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_clas="";
		$ls_codemp = $_SESSION["la_empresa"]["codemp"];
		$ls_sql="SELECT rpc_clasificacion.codclas, rpc_clasificacion.denclas  ".
				"  FROM rpc_clasifxprov, rpc_clasificacion ".
				" WHERE rpc_clasifxprov.codemp='".$ls_codemp."'  ".
				"   AND rpc_clasifxprov.cod_pro = '".$as_codprov."' ".
				"   AND rpc_clasifxprov.codclas <> '---' ".
				"	AND rpc_clasifxprov.codclas=rpc_clasificacion.codclas ".
				" ORDER BY rpc_clasificacion.denclas ASC "; 
		$rs_data = $this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_RPC_CLASS_REPORT; METODO->uf_select_proveedores; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_clas=$ls_clas.", ".$row["denclas"];
			}
			$ls_clas=substr($ls_clas,1,strlen($ls_clas)-1);
		}
		$arrResultado["ls_clas"] = $ls_clas;
		$arrResultado["lb_valido"] = $lb_valido;		
		return $arrResultado;         
	}// end 



//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_load_proveedores($as_codemp,$ai_orden,$as_tipo,$as_codprov1,$as_codprov2,$as_codigoesp,$lb_valido) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_proveedores
		//         Access: public 
		//	    Arguments: as_codemp // código de Empresa
		//	    		   ai_orden // Orden del reportes
		//	    		   as_tipo // Tipo de Proveedor
		//	  			   as_codprov1 // Rango de proveedor Desde		  
		//	  			   as_codprov2 // Rango de proveedor Hasta	  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del Proveedor
		//	   Creado Por: 
		// Fecha Creación: 24/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		switch($ai_orden)
		{
			case "0":
				$ls_orden="cod_pro";
				break;
			case "1":
				$ls_orden="nompro";
				break;
		}
		
		
		if($as_tipo=="P")
		{
			$ls_categoria="rpc_proveedor.estpro ";
		}
		else
		{
			$ls_categoria="rpc_proveedor.estcon ";
		}
		$ls_cadena="";		
		if((!empty($as_codprov1)) || (!empty($as_codprov2)))
		{
			$ls_cadena="   AND rpc_proveedor.cod_pro BETWEEN '".$as_codprov1."' AND '".$as_codprov2."' ";
		}
		if($as_codigoesp=="")
		{
			$join='LEFT JOIN rpc_espexprov ON rpc_proveedor.cod_pro = rpc_espexprov.cod_pro';
			$tira="";
		}else
		{
		  $join='INNER JOIN rpc_espexprov ON rpc_proveedor.cod_pro = rpc_espexprov.cod_pro';
		  $tira=" AND rpc_espexprov.codesp like '%".$as_codigoesp."%' ";
		}
		
		$ls_sql="SELECT rpc_proveedor.cod_pro, MAX(rpc_proveedor.nompro) AS nompro, MAX(rpc_proveedor.rifpro) AS rifpro, ".
				"		MAX(rpc_proveedor.nitpro) AS nitpro, MAX(rpc_proveedor.telpro) AS telpro, MAX(rpc_proveedor.dirpro) AS dirpro, ".
				"		MAX(rpc_proveedor.ocei_no_reg) AS ocei_no_reg, MAX(rpc_proveedor.obspro) AS obspro, MAX(rpc_proveedor.capital) AS capital,".
				"		MAX(rpc_proveedor.sc_cuenta) AS sc_cuenta,".
				"       (SELECT MAX(monfincon)".
				"          FROM rpc_clasifxprov".
				"         WHERE rpc_proveedor.codemp=rpc_clasifxprov.codemp".
				"           AND rpc_proveedor.cod_pro=rpc_clasifxprov.cod_pro".
				"         GROUP BY codemp,cod_pro) as monfincon,".
				"  MAX(rpc_proveedor.sc_cuenta) AS sc_cuenta ".
				"  FROM rpc_proveedor ".
		  		"   {$join} ".
				"   WHERE rpc_proveedor.codemp='".$as_codemp."'  ".$tira.
				"   AND ".$ls_categoria." = 1 ".
				"   AND rpc_proveedor.cod_pro <> '----------'".
				$ls_cadena. 
				" GROUP BY rpc_proveedor.codemp,rpc_proveedor.cod_pro ".
				" ORDER BY ".$ls_orden." ASC ";
		$rs_data = $this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
		}
		else
		{
			$li_numrows=$this->io_sql->num_rows($rs_data);	   
			if ($li_numrows>0)
			{
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
				if ($this->io_sql->message!="")
				{                               
					$this->io_msg->message($this->io_funcion->uf_convertirmsg($this->io_sql->message));		 
				}           
			}	
		}
		$arrResultado["rs_data"] = $rs_data;
		$arrResultado["lb_valido"] = $lb_valido;		
		return $arrResultado;         
	}// end function uf_load_proveedor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_documentosproveedores($as_codemp,$as_codprov,$aa_documentos,$as_estatus,$lb_valido) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_documentosproveedores
		//         Access: public 
		//	    Arguments: as_codemp // código de Empresa
		//	    		   as_codprov // Código de Proveedor
		//	  			   aa_documentos // Arreglo de Documentos a Imprimir
		//	  			   as_estatus //  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los documentos del proveedor
		//	   Creado Por: 
		// Fecha Creación: 24/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;

		$ls_sql = "SELECT rpc_documentos.dendoc, rpc_docxprov.fecrecdoc, rpc_docxprov.fecvendoc, ".
				  "       rpc_docxprov.estdoc, rpc_docxprov.estorig, rpc_documentos.tipdoc ".
				  "  FROM rpc_docxprov, rpc_documentos ".
				  " WHERE rpc_docxprov.codemp = '".$as_codemp."' ".
				  "   AND rpc_docxprov.cod_pro = '".$as_codprov."' ".
				  "   AND rpc_docxprov.coddoc = rpc_documentos.coddoc ";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->SIGESP_RPC_CLASS_REPORT; METODO->uf_select_documentosproveedores; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if (!$rs_data->EOF)
			{
				$lb_valido=true;
			}
		}
		$arrResultado["rs_data"] = $rs_data;
		$arrResultado["lb_valido"] = $lb_valido;		
		return $arrResultado;         
		
	}// end function uf_select_documentosproveedores
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>