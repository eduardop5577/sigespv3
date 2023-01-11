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
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../shared/class_folder/sigesp_c_seguridad.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../shared/class_folder/class_sigesp_int.php");
require_once("../shared/class_folder/class_sigesp_int_int.php");
require_once("../shared/class_folder/class_sigesp_int_spg.php");
require_once("../shared/class_folder/class_sigesp_int_spi.php");
require_once("../shared/class_folder/class_sigesp_int_scg.php");
require_once("sigesp_siv_c_articuloxalmacen.php");
require_once("sigesp_siv_c_movimientoinventario.php");

class sigesp_siv_c_cerrarsep
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;

	public function __construct()
	{
		$in=              new sigesp_include();
		$this->sig_int=   new class_sigesp_int();
        $this->io_sigesp_int=new class_sigesp_int_int();
        $this->io_sigesp_spg=new class_sigesp_int_spg();
		$this->con=       $in->uf_conectar();
		$this->io_sql=    new class_sql($this->con);
		$this->seguridad= new sigesp_c_seguridad();
		$this->io_fun=    new class_funciones_db($this->con);
		$this->io_msg=    new class_mensajes();
		$this->io_funcion=new class_funciones();
		$this->io_mov=  new sigesp_siv_c_movimientoinventario();
		$this->ds=new class_datastore();
		$arre=$_SESSION["la_empresa"];
		$this->ls_codemp=$arre["codemp"];
	}
	
	function uf_siv_load_solicitudes($ai_totrows,$ao_object,$as_estmov,$ad_fecdes,$ad_fechas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_solicitudes
		//         Access: public 
		//      Argumento: $ai_totrows // total de filas del grid
		//  			   $ao_object  // arreglo de objetos
		//  			   $as_estmov  // estatus del movimiento (cerrar o reversar cierre)
		//  			   $ad_fecdes  // fecha de inicio de la busqueda 
		//  			   $ad_fechas  // fecha de cierre de la busqueda
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca las ordenes de compra dependiendo del estatus de pendiente de almacén
		//				   en la tabla soc_ordencompra.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/07/2006							Fecha Última Modificación : 29/07/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ao_object=Array();
		$ad_fecdes=$this->io_funcion->uf_convertirdatetobd($ad_fecdes);
		$ad_fechas=$this->io_funcion->uf_convertirdatetobd($ad_fechas);
		$ls_sql="SELECT numsol,fecregsol,consol".
				"  FROM sep_solicitud".
				" WHERE codemp='". $this->ls_codemp ."'".
				"   AND fecregsol >= '". $ad_fecdes ."'".
				"   AND fecregsol <= '". $ad_fechas ."'".
				"   AND estsol='L'".
				"   AND numsol IN (SELECT numsol FROM siv_despacho)";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->cerrar_sep MÉTODO->uf_siv_load_solicitudes ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_i=$li_i + 1;
				$ls_numsol= $row["numsol"];
				$ld_fecsol= $row["fecregsol"];
				$ls_consol= $row["consol"];
				$ld_fecsol=$this->io_funcion->uf_convertirfecmostrar($ld_fecsol);
				$ai_totrows=$ai_totrows+1;
				$ao_object[$ai_totrows][1]="<input  name=txtnumsol".$ai_totrows." type=text id=txtnumsol".$ai_totrows." class=sin-borde size=15 maxlength=20 value='".$ls_numsol."' readonly>";
				$ao_object[$ai_totrows][2]="<input  name=txtfecsol".$ai_totrows." type=text id=txtfecsol".$ai_totrows." class=sin-borde size=15 maxlength=20 value='".$ld_fecsol."' readonly>";
				$ao_object[$ai_totrows][3]="<input  name=txtconsol".$ai_totrows." type=text id=txtconsol".$ai_totrows." class=sin-borde size=60 maxlength=500 value='".$ls_consol."' readonly>";
				$ao_object[$ai_totrows][4]="<input  name=chkprocesar".$ai_totrows."   type='checkbox' class= sin-borde value=1>";

			}//while
			if ($li_i==0)
			{
				$ai_totrows=$ai_totrows+1;
				$ao_object[$ai_totrows][1]="<input  name=txtnumsol".$ai_totrows." type=text id=txtnumsol".$ai_totrows." class=sin-borde size=15 maxlength=20 value='' readonly>";
				$ao_object[$ai_totrows][2]="<input  name=txtfecsol".$ai_totrows." type=text id=txtfecsol".$ai_totrows." class=sin-borde size=15 maxlength=20 value='' readonly>";
				$ao_object[$ai_totrows][3]="<input  name=txtconsol".$ai_totrows." type=text id=txtconsol".$ai_totrows." class=sin-borde size=60 maxlength=100 value='' readonly>";
				$ao_object[$ai_totrows][4]="<input  name=chkprocesar".$ai_totrows."   type='checkbox' class= sin-borde value=1>";
			}
			$this->io_sql->free_result($rs_data);
		}
		if ($ai_totrows==0)
		{
			$lb_valido=false;
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end  function uf_siv_load_solicitudes

	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_monto_causado_anterior($as_comprobante,$as_procede,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_monto_causado_anterior
		//		   Access: public (sigesp_cxp_c_recepcion_ajax.php)
		//	    Arguments: as_comprobante  // Número de comprobante
		//				   as_procede  // Procede de la cuenta
		//				   as_spgcuenta  // Cuenta del movimiento
		//				   as_codestpro  // Código de Programatica
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Función que se encarga de buscar la suma de los montos causadoas anteriormente
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 21/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ai_monto=0;
		$lb_valido=true; 
		$ls_sql="SELECT SUM(CASE WHEN cxp_rd_spg.monto is null THEN 0 ELSE cxp_rd_spg.monto END) AS monto ".
				"  FROM cxp_rd_spg, cxp_rd ".
				" WHERE cxp_rd_spg.codemp='".$this->ls_codemp."' ".
				"   AND cxp_rd_spg.procede_doc='".$as_procede."' ".
				"   AND cxp_rd_spg.numdoccom='".$as_comprobante."' ".
				"   AND cxp_rd_spg.codemp=cxp_rd.codemp ".
				"   AND trim(cxp_rd_spg.numrecdoc) = trim(cxp_rd.numrecdoc) ".
				"   AND cxp_rd_spg.codtipdoc=cxp_rd.codtipdoc ".
				"   AND trim(cxp_rd_spg.ced_bene) = trim(cxp_rd.ced_bene) ".
				"   AND cxp_rd_spg.cod_pro=cxp_rd.cod_pro ".
				"   AND cxp_rd.estprodoc<>'A' "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false; 
			$this->io_mensajes->message("CLASE->Recepción MÉTODO->uf_load_monto_causado_anterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_monto=$row["monto"];
			}  
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_monto']=$ai_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_load_monto_causado_anterior
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_soc_enlace_sep($as_codemp,$as_numordcom,$aa_coduniadm,$aa_denuniadm)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_load_soc_enlace_sep
		//	           Access: public
		//  		Arguments: $as_codemp    // codigo de empresa
		//  			       $as_numordcom // numero de orden de compra
		//  			       $aa_coduniadm // arreglo de codigos de unidad administrativa
		//  			       $aa_denuniadm // arreglo de denominaciones de unidad administrativa
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de buscar las sep asociadas a una orden de compra al igual que las unidades 
		//                     administrativas
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 03/11/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT soc_ordencompra.coduniadm, soc_ordencompra.numordcom, soc_enlace_sep.numsol,".
			    "       sep_solicitud.coduniadm, spg_unidadadministrativa.denuniadm".
				"  FROM soc_ordencompra, soc_enlace_sep, sep_solicitud, spg_unidadadministrativa".
				" WHERE soc_ordencompra.codemp='".$as_codemp."'".
				"   AND soc_ordencompra.coduniadm=''".
		  		"   AND soc_ordencompra.numordcom='".$as_numordcom."'".
		 		"   AND soc_ordencompra.codemp =  soc_enlace_sep.codemp".
		 		"   AND soc_ordencompra.numordcom =  soc_enlace_sep.numordcom".
				"   AND soc_enlace_sep.codemp = sep_solicitud.codemp".
				"   AND soc_enlace_sep.numsol = sep_solicitud.numsol".
				"   AND sep_solicitud.codemp = spg_unidadadministrativa.codemp".
			 	"   AND sep_solicitud.coduniadm = spg_unidadadministrativa.coduniadm";
	    $rs_data=$this->io_sql->select($ls_sql);
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->cerrar_sep MÉTODO->uf_load_soc_enlace_sep ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$li_i=1;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$aa_coduniadm[$li_i]= $row["coduniadm"];
				$aa_denuniadm[$li_i]= $row["denuniadm"];
				$li_i++;
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['aa_coduniadm']=$aa_coduniadm;
		$arrResultado['aa_denuniadm']=$aa_denuniadm;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} //fin  function uf_load_soc_enlace_sep


	function uf_siv_update_status($as_codemp,$as_numsol,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_status
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $as_estpenalm // estatus de pendiente de almacen
		//  			   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que actualiza el estatus de la orden de compra que indica si ya fue recibida por el almacen.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 31/07/2006							Fecha Última Modificación : 31/07/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql= "UPDATE sep_solicitud".
				 "   SET estsol='I',feccieinv='".date("Y-m-d")."'".
				 " WHERE codemp='".$as_codemp."'".
				 "   AND numsol='".$as_numsol."'";
		$rs_data=$this->io_sql->execute($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->cerrar_sep MÉTODO->uf_siv_update_status ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Cerro la Solicitud de ejecucion presupuestaria numero ".$as_numsol." Asociada a la Empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion); 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}  // end  function uf_siv_update_status

}//end  class sigesp_siv_c_cerrarsep
?>
