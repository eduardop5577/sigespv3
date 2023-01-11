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
require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");

class sigesp_saf_c_incorporacionhistorial
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
		$this->io_funciones = new class_funciones();
		$this->io_consecutivo = new sigesp_c_generar_consecutivo();
	}//fin de la function sigesp_saf_c_metodos()
	
	function uf_load_config($as_codsis,$as_seccion,$as_entry)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_config
		//		   Access: public
		//	    Arguments: 
		//	      Returns: $ls_valor
		//	  Description: Determina si el registro ya existe dentro de la Tabla sigesp_config.
		// Modificado por: Ing. Luis Anibal Lang      
		// Fecha Creación: 09/05/2015 	 Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor = false;
		$ls_sql = "SELECT value 
					 FROM sigesp_config 
					WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."' 
					  AND codsis = '".$as_codsis."'
					  AND seccion = '".$as_seccion."'
					  AND entry = '".$as_entry."'";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->sigesp_saf_c_activo.php->uf_load_config;ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido = false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor = $row["value"];
			}
		}
		return $ls_valor;
	}
	
   //-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Sección a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Función que inserta la variable de configuración
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 				Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		$ls_sql="INSERT INTO sigesp_config(codemp, codsis, seccion, entry, value, type)VALUES ".
				"('".$_SESSION["la_empresa"]["codemp"]."','".$as_sistema."','".$as_seccion."','".$as_variable."','".$as_valor."','".$as_tipo."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->articulo ->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			$this->io_sql->commit();
		}
		return $lb_valido;
	}// end function uf_insert_config	
	//-----------------------------------------------------------------------------------------------------------------------------------
  
	
	function uf_saf_select_historialactivos()
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_saf_select_historialactivos
		//		   Access: public
		//		 Argument: 
		//	  Description: Función que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$periodo = (date("Y")-1);
		$periodo=$periodo."-01-01";
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sql="SELECT saf_dta.codemp,sum(costo) AS monact,saf_dta.coduniadm,saf_dta.estact".
				"  FROM saf_activo,saf_dta".
				" WHERE saf_dta.codemp='".$ls_codemp."'".
				"   AND (saf_dta.estact='I' OR saf_dta.estact='D')".
				"   AND saf_dta.fecincact<'".$periodo."'".
				"   AND saf_activo.codemp=saf_dta.codemp".
				"   AND saf_activo.codact=saf_dta.codact".
				" GROUP BY saf_dta.codemp,saf_dta.coduniadm,saf_dta.estact".
				" ORDER BY saf_dta.coduniadm";
				//print $ls_sql."<br>"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Historial MÉTODO->uf_saf_select_historialactivos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}//fin de la function uf_saf_select_historialactivos
	
	
	
	function uf_saf_procesar_historialactivos()
	{
		$lb_valido=true;
		$i=0;
		$ls_procesado=$this->uf_load_config("SAF","CONFIGURACION","HISTORIAL");
		if($ls_procesado!="1")
		{
			$this->io_sql->begin_transaction();
			$rs_data=$this->uf_saf_select_historialactivos();
			if(!$rs_data->EOF)
			{
				$periodo = (date("Y")-1);
				$feccmp=$periodo."-12-31";
				$i=0;
				$ls_sql="SELECT codact".
						"  FROM saf_activo".
						" WHERE codact='---------------' ";
				$resultact = $this->io_sql->select($ls_sql);
				if ($resultact->EOF)
				{
					$ls_sql="INSERT INTO saf_activo (codemp,codact,denact) ".
							"VALUES ('0001','---------------','Activo por Defecto');";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$this->io_msg->message("CLASE->Historial MÉTODO->uf_saf_procesar_historialactivos_I ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
						$lb_valido=false;
						$this->io_sql->rollback();
					}
				}
				$ls_sql="SELECT codact,ideact".
						"  FROM saf_dta".
						" WHERE codact='---------------'".
						"   AND ideact='---------------' ";
				$resultdt = $this->io_sql->select($ls_sql);
				if ($resultdt->EOF)
				{
					$ls_sql="INSERT INTO saf_dta (codemp,codact,ideact,seract,idchapa,estact,estcon) ".
							"VALUES ('0001','---------------','---------------','0000000000000000000000000','---------------','A',0);";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$this->io_msg->message("CLASE->Historial MÉTODO->uf_saf_procesar_historialactivos_II ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
						$lb_valido=false;
						$this->io_sql->rollback();
					}
				}
			}// Fin
			while (!$rs_data->EOF)
			{
				$i++;
			//	$comprobante=str_pad($i,15,'0',STR_PAD_LEFT);
				$comprobante=$this->io_consecutivo->uf_generar_numero_nuevo("SAF","saf_movimiento","cmpmov","SAFCMP",15,"","","");
				$codemp = $rs_data->fields['codemp'];
				$estact = $rs_data->fields['estact'];
				$monact = $rs_data->fields['monact'];
				$coduniadm = $rs_data->fields['coduniadm'];
				if($estact=="I")
				{
					$codcau="018";
					$tipcmp="IN";
				}
				else
				{
					$codcau="059";
					$tipcmp="DE";
				}
					
				$ls_sql = "INSERT INTO saf_movimiento (codemp,cmpmov,codcau,estcat,feccmp,estpromov,numcmp,tipcmp,coduniadm) ".
							"     VALUES ('".$codemp."','".$comprobante."','".$codcau."',2,'".$feccmp."',0,'-','".$tipcmp."','".$coduniadm."')";
				$li_row=$this->io_sql->execute($ls_sql);
				if ($li_row===false)
				{
						$this->io_msg->message("CLASE->Historial MÉTODO->uf_saf_procesar_historialactivos_III ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
						$lb_valido=false;
						$this->io_sql->rollback();
				}

				$ls_sql = " INSERT INTO saf_dt_movimiento (codemp, cmpmov, codcau, estcat, ".
							"		feccmp, codact, ideact, monact, coduniadm) ".
							" VALUES ('".$codemp."','".$comprobante."','".$codcau."',2, ".
							"		'".$feccmp."','---------------','---------------',".$monact.",'".$coduniadm."')";
				$li_row=$this->io_sql->execute($ls_sql);
				if ($li_row===false)
				{
						$this->io_msg->message("CLASE->Historial MÉTODO->uf_saf_procesar_historialactivos_IV ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
						$lb_valido=false;
						$this->io_sql->rollback();
				}
				$rs_data->MoveNext();
			}
		}
		else
		{
			$this->io_msg->message("Ya este proceso esta ejecutado");
			$lb_valido=false;
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_insert_config("SAF", "CONFIGURACION","HISTORIAL","1", "C");
		}
		if($lb_valido)
		{
			$this->io_sql->commit();
			$this->io_msg->message("El proceso se realizo con Exito");
		}
		else
		{
			$this->io_sql->rollback();
			$this->io_msg->message("Ocurrio un error al procesar la informacion");
		}
		return $lb_valido;
	}
		
		
}//fin de la class sigesp_saf_c_condicion
?>
