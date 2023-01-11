<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_cxp_c_recepcionlote
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;
	private $io_conexion;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct($as_path)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_cxp_c_aprobacionrecepcion
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 05/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion = $io_include->uf_conectar();
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
	    require_once($as_path."base/librerias/php/general/sigesp_lib_fecha.php");		
		$this->io_fecha= new class_fecha();
		require_once($as_path."shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->ls_estrescxp=$_SESSION["la_empresa"]["estrescxp"];
        $this->ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
	}// end function sigesp_sep_c_aprobacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_sep_p_solicitud.php)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/05/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($this->io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fecha);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_tipodocumento($as_seleccionado,$as_tipo)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_tipodocumento
		//		   Access: public
		//		 Argument: $as_seleccionado // Valor del campo que va a ser seleccionado
		//		 		   $as_tipo // Tipo de documento por el cual se debe filtrar
		//	  Description: Función que busca en la tabla de tipo de documento los tipos de Recepciones
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codtipdoc, dentipdoc, estcon, estpre,tipdocdon,tipdoctesnac ".
				"  FROM cxp_documento ".
				" WHERE (estcon='1'".
				"   AND estpre='2')".
				"    OR (estcon='1'".
				"   AND estpre='1')".
				" ORDER BY codtipdoc ";	
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Recepcion MÉTODO->uf_load_tipodocumento ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			print "<select name='cmbcodtipdoc' id='cmbcodtipdoc' onChange='javascript: ue_cambiartipodocumento();'>";
			print " <option value='-'>-- Seleccione Uno --</option>";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_seleccionado="";
				$ls_codtipdoc=$row["codtipdoc"];
				$ls_dentipdoc=$row["dentipdoc"];
				$ls_estcon=$row["estcon"];
				$ls_estpre=$row["estpre"];
				$ls_tipdocdon=$row["tipdocdon"];
				$ls_tipdoctesnac=$row["tipdoctesnac"];
				$ls_tipdoc=$ls_estcon.$ls_estpre;
				switch($as_tipo)
				{
					case "C":
						if($as_seleccionado==$ls_codtipdoc."-".$ls_estcon."-".$ls_estpre."-".$ls_tipdocdon."-".$ls_tipdoctesnac)
						{
							$ls_seleccionado="selected";
						}
						print "<option value='".$ls_codtipdoc."-".$ls_estcon."-".$ls_estpre."-".$ls_tipdocdon."-".$ls_tipdoctesnac."' ".$ls_seleccionado.">".$ls_dentipdoc."</option>";
						break;
						
					case "D": // cuando viene de solicitud de desembolso
						if($ls_tipdoc=="11")
						{
							if($as_seleccionado==$ls_codtipdoc."-".$ls_estcon."-".$ls_estpre."-".$ls_tipdocdon."-".$ls_tipdoctesnac)
							{
								$ls_seleccionado="selected";
							}
							print "<option value='".$ls_codtipdoc."-".$ls_estcon."-".$ls_estpre."-".$ls_tipdocdon."-".$ls_tipdoctesnac."' ".$ls_seleccionado.">".$ls_dentipdoc."</option>";
						}
						break;
						
					default:
						if(($ls_tipdoc!="13")&&($ls_tipdoc!="14"))
						{
							if($as_seleccionado==$ls_codtipdoc."-".$ls_estcon."-".$ls_estpre."-".$ls_tipdocdon."-".$ls_tipdoctesnac)
							{
								$ls_seleccionado="selected";
							}
							print "<option value='".$ls_codtipdoc."-".$ls_estcon."-".$ls_estpre."-".$ls_tipdocdon."-".$ls_tipdoctesnac."' ".$ls_seleccionado.">".$ls_dentipdoc."</option>";
						}
						break;
				}
			}
			$this->io_sql->free_result($rs_data);	
			print "</select>";
		}
		return $lb_valido;
	}// end function uf_load_tipodocumento
	//-----------------------------------------------------------------------------------------------------------------------------------

}
?>