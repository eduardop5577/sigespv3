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

class class_funciones_spi
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;
//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{
		//////////////////////////////////////////////////////////////////////////////
		//Function: class_funciones_nomina
		// Access: public
		// Description: Constructor de la Clase
		//	   
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
   		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();				
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_convertir_spidtcmp($as_procede,$as_comprobante,$ad_fecha,$as_codban,$as_ctaban,$aa_seguridad)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_convertir_spidtcmp
		//		   Access: private
		//	    Arguments: as_procede  // procede del comprobante
		//				   as_comprobante  //  número del comprobante
		//				   ad_fecha  // fecha del comprobante
		//				   as_codban  //  código de banco del comprobante
		//				   as_ctaban  //  cuenta del banco del comprobante
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que actualizamos los montos en el valor reconvertido
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/08/2007 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT spi_cuenta, procede_doc, documento, operacion, monto".
				"  FROM spi_dt_cmp".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND procede = '".$as_procede."'".
				"   AND comprobante = '".$as_comprobante."'".
				"   AND fecha = '".$ad_fecha."'".
				"   AND codban = '".$as_codban."'".
				"   AND ctaban = '".$as_ctaban."'"; ///print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
			$this->io_mensajes->message("CLASE->class_funciones MÉTODO->SELECT->uf_convertir_spidtcmp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$ls_spi_cuenta=$row["spi_cuenta"];
				$ls_procede_doc=$row["procede_doc"];
				$ls_documento=$row["documento"]; 
				$ls_operacion=$row["operacion"];
				$ld_monto=$row["monto"];
			}
		}		
		///unset($this->io_rcbsf);
		return $lb_valido;
	}// end function uf_convertir_spidtcmp
	//-----------------------------------------------------------------------------------------------------------------------------

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_convertir_sigespcmp($as_procede,$as_comprobante,$ad_fecha,$as_codban,$as_ctaban,$aa_seguridad)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_convertir_sigespcmp
		//		   Access: private
		//	    Arguments: as_procede  // procede del comprobante
		//				   as_comprobante  //  número del comprobante
		//				   ad_fecha  // fecha del comprobante
		//				   as_codban  //  código de banco del comprobante
		//				   as_ctaban  //  cuenta del banco del comprobante
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que actualizamos los montos en el valor reconvertido
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/08/2007 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT total ".
				"  FROM sigesp_cmp ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND procede = '".$as_procede."'".
				"   AND comprobante = '".$as_comprobante."'".
				"   AND fecha = '".$ad_fecha."'".
				"   AND codban = '".$as_codban."'".
				"   AND ctaban = '".$as_ctaban."'"; ////print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
			$this->io_mensajes->message("CLASE->class_funciones MÉTODO->uf_convertir_sigespcmp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
			
			}
		}
		///unset($this->io_rcbsf);
		return $lb_valido;
	}// end function uf_convertir_sigespcmp
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_convertir_scgdtcmp($as_procede,$as_comprobante,$ad_fecha,$as_codban,$as_ctaban,$aa_seguridad)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_convertir_sigespcmp
		//		   Access: private
		//	    Arguments: as_procede  // procede del comprobante
		//				   as_comprobante  //  número del comprobante
		//				   ad_fecha  // fecha del comprobante
		//				   as_codban  //  código de banco del comprobante
		//				   as_ctaban  //  cuenta del banco del comprobante
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que actualizamos los montos en el valor reconvertido
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/08/2007 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT sc_cuenta, procede_doc, documento, debhab, monto ".
				"  FROM scg_dt_cmp ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND procede = '".$as_procede."'".
				"   AND comprobante = '".$as_comprobante."'".
				"   AND fecha = '".$ad_fecha."'".
				"   AND codban = '".$as_codban."'".
				"   AND ctaban = '".$as_ctaban."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
			$this->io_mensajes->message("CLASE->class_funciones MÉTODO->uf_convertir_scgdtcmp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$ls_sc_cuenta= $row["sc_cuenta"];
				$ls_procede_doc= $row["procede_doc"];
				$ls_documento= $row["documento"];
				$ls_debhab= $row["debhab"];
				$li_monto= $row["monto"];
			}
		}
		///unset($this->io_rcbsf);
		return $lb_valido;
	}// end function uf_convertir_scgdtcmp
	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_convertir_spgdtcmp($as_procede,$as_comprobante,$ad_fecha,$as_codban,$as_ctaban,$aa_seguridad)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_convertir_spgdtcmp
		//		   Access: private
		//	    Arguments: as_procede  // procede del comprobante
		//				   as_comprobante  //  número del comprobante
		//				   ad_fecha  // fecha del comprobante
		//				   as_codban  //  código de banco del comprobante
		//				   as_ctaban  //  cuenta del banco del comprobante
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que actualizamos los montos en el valor reconvertido
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/08/2007 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, spg_cuenta, procede_doc, documento, operacion, monto ".
				"  FROM spg_dt_cmp".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND procede = '".$as_procede."'".
				"   AND comprobante = '".$as_comprobante."'".
				"   AND fecha = '".$ad_fecha."'".
				"   AND codban = '".$as_codban."'".
				"   AND ctaban = '".$as_ctaban."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
			$this->io_mensajes->message("CLASE->class_funciones MÉTODO->SELECT->uf_convertir_spgdtcmp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$ls_codestpro1=$row["codestpro1"];
				$ls_codestpro2=$row["codestpro2"];
				$ls_codestpro3=$row["codestpro3"];
				$ls_codestpro4=$row["codestpro4"];
				$ls_codestpro5=$row["codestpro5"]; 
				$ls_spg_cuenta=$row["spg_cuenta"];
				$ls_procede_doc=$row["procede_doc"];
				$ls_documento=$row["documento"]; 
				$ls_operacion=$row["operacion"];
				$ld_monto=$row["monto"];
			}
		}		
		//unset($this->io_rcbsf);
		return $lb_valido;
	}// end function uf_convertir_spgdtcmp
//-----------------------------------------------------------------------------------------------------------------------------

	function uf_convertir_spicuenta($as_spicuenta,$aa_seguridad)

	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_convertir_spicuenta
		//		   Access: private
		//	    Arguments: as_spicuenta  // cuenta de la estructura presupuestaria de ingreso
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que actualizamos los montos en el valor reconvertido
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creación: 04/10/2007 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT previsto, devengado, cobrado, cobrado_anticipado, aumento, disminucion, enero, febrero, marzo, abril, mayo, junio, julio, agosto, septiembre, octubre, noviembre, diciembre".
				"  FROM spi_cuentas".
				" WHERE codemp = '".$this->ls_codemp."'".
				" AND spi_cuenta = '".$as_spicuenta."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
			$this->io_mensajes->message("CLASE->class_funciones MÉTODO->SELECT->uf_convertir_spicuenta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$ld_previsto=$row["previsto"];
				$ld_devengado=$row["devengado"];
				$ld_cobrado=$row["cobrado"];
				$ld_cobrado_anticipado=$row["cobrado_anticipado"];
				$ld_aumento=$row["aumento"]; 
				$ld_disminucion=$row["disminucion"];
				$ld_enero=$row["enero"];
				$ld_febrero=$row["febrero"]; 
				$ld_marzo=$row["marzo"];
				$ld_abril=$row["abril"];
				$ld_mayo=$row["mayo"];
				$ld_junio=$row["junio"]; 
				$ld_julio=$row["julio"];
				$ld_agosto=$row["agosto"];
				$ld_septiembre=$row["septiembre"];
				$ld_octubre=$row["octubre"]; 
				$ld_noviembre=$row["noviembre"];
				$ld_diciembre=$row["diciembre"];
			}
		}		
		///unset($this->io_rcbsf);
		return $lb_valido;
	}// end function uf_convertir_spicuentas
//-----------------------------------------------------------------------------------------------------------------------------



}
?>