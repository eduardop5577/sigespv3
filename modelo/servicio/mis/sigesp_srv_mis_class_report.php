<?php 
/***********************************************************************************
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_mis_class_report
{

	public function __construct()
	{
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
    }

   //----------------------------------------------------------------------------------------------------------------------------
   function uf_load_seguridad_reporte($as_sistema,$as_ventanas,$as_descripcion)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_seguridad_reporte
		//		   Access: public (en todas las clases que usen seguridad)
		//	    Arguments: as_sistema // Sistema del que se desea verificar la seguridad
		//				   as_ventanas // Ventana del que se desea verificar la seguridad
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	  Description: Función que obtiene el valor de una variable que viene de un submit
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/04/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		require_once("../../../shared/class_folder/sigesp_c_seguridad.php");
		$io_seguridad= new sigesp_c_seguridad();

		$lb_valido=true;	
		$ls_empresa=$_SESSION["la_empresa"]["codemp"];
		$ls_logusr=$_SESSION["la_logusr"];
		$la_seguridad["empresa"]=$ls_empresa;
		$la_seguridad["logusr"]=$ls_logusr;
		$la_seguridad["sistema"]=$as_sistema;
		$la_seguridad["ventanas"]=$as_ventanas;
		$arrResultado=$io_seguridad->uf_sss_load_permisos($ls_empresa,$ls_logusr,$as_sistema,$as_ventanas,$aa_permisos);
		$aa_permisos=$arrResultado['aa_permisos'];
		$as_permisos= $arrResultado['lb_valido'];
		if (($as_permisos)||($ls_logusr=="PSEGIS"))
		{
			if($aa_permisos["imprimir"]=="1")
			{			
				$ls_evento="REPORT";
				$lb_valido= $io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
										$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
										$la_seguridad["ventanas"],$as_descripcion);
			}
			else
			{
				print("<script language=JavaScript>");
				print("alert('No tiene permiso para realizar esta operación.');");
				print("</script>");		
				$lb_valido=false;	
			}
		}
		else
		{
			$lb_valido=false;
		}		
		unset($io_seguridad);
		return $lb_valido;
   }// end function uf_load_seguridad
   //----------------------------------------------------------------------------------------------------------------------------
    
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Funcion que busca en la base de datos los documentod contabilizados
	 *       por un usuario, segun los parametros espesificados
	 * @param string $modulo - modulo por el cual se desea filtrar los documentos
	 * @param string $concepto - concepto por el cual se desea filtrar los documentos
	 * @param string $order - orden en el cual se presentara la informacion
	 * @return resulset - arreglo con los datos obtenidos en el sql
	 */
	public function uf_select_documentos_contabilizados($codusu,$fecdes,$fechas,$modulo,$concepto,$order)
	{
		if($fecdes!='' && $fechas!=''){
			$ls_filtro = "AND cmp.fecha  between '".$this->io_funciones->uf_convertirdatetobd($fecdes)."' AND '".$this->io_funciones->uf_convertirdatetobd($fechas)."' ";
		}
		if ($codusu!='') {
			$ls_filtro .= "AND cmp.codusu like '%".$codusu."%' ";
		}
		
		if($modulo!="NSD"){
			$ls_filtro .= "AND cmp.procede like '%".$modulo."%' ";	
		}
		
		if($concepto!=""){
			$ls_filtro .= "AND cmp.descripcion like '%".$concepto."%'";
		}
	
		$ls_sql="SELECT cmp.comprobante AS numdoc,cmp.total as monto,cmp.fecha,cmp.procede,pro.desproc,cmp.codusu
				 FROM sigesp_cmp cmp
				 INNER JOIN
				 sigesp_procedencias pro ON cmp.procede=pro.procede
				 WHERE cmp.codemp = '".$_SESSION["la_empresa"]["codemp"]."' 
				       ".$ls_filtro." 
				   AND cmp.procede NOT IN ('SCGCMP','SPGCMP','SPICMP','SPGAPR','SPIAPR')	   
				 ORDER BY cmp.comprobante ".$order; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false){
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_documentos_contabilizados ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
				
		return $rs_data;
	}
}
?>