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

class sigesp_siv_c_cerraroc
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
	
	function uf_siv_load_ordenes($ai_totrows,$ao_object,$as_estmov,$ad_fecdes,$ad_fechas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_ordenes
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
		$ad_fecdes=$this->io_funcion->uf_convertirdatetobd($ad_fecdes);
		$ad_fechas=$this->io_funcion->uf_convertirdatetobd($ad_fechas);
		$ls_sql="SELECT numordcom,cod_pro,fecordcom,montot,coduniadm,".
		 		"     (SELECT nompro FROM rpc_proveedor".
				"       WHERE rpc_proveedor.cod_pro=soc_ordencompra.cod_pro) AS nompro,".	
		 		"     (SELECT denuniadm FROM spg_unidadadministrativa".
			 	"       WHERE spg_unidadadministrativa.coduniadm=soc_ordencompra.coduniadm) AS denuniadm".	
				"  FROM soc_ordencompra".
				" WHERE codemp='". $this->ls_codemp ."'".
				"   AND fecordcom >= '". $ad_fecdes ."'".
				"   AND fecordcom <= '". $ad_fechas ."'".
				"   AND estcom>='2'".
				"   AND estpenalm='". $as_estmov ."'";
		if($as_estmov=="0")
		{		
			$ls_sql=$ls_sql."   AND (estcondat = 'B' OR estcondat = '-')".
							"   AND numordcom IN (SELECT numdoccom FROM cxp_rd_spg,cxp_rd".
							"                      WHERE cxp_rd_spg.codemp='0001'".
							"                        AND cxp_rd_spg.procede_doc='SOCCOC'".
							"                        AND cxp_rd.estprodoc<>'A'".
							"                        AND cxp_rd_spg.codemp=cxp_rd.codemp".
							"						 AND cxp_rd_spg.numrecdoc=cxp_rd.numrecdoc".
							"						 AND cxp_rd_spg.codtipdoc=cxp_rd.codtipdoc".
							"						 AND cxp_rd_spg.cod_pro=cxp_rd.cod_pro".
							"						 AND cxp_rd_spg.ced_bene=cxp_rd.ced_bene)";	
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_siv_load_ordenes ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_i=$li_i + 1;
				$ls_numordcom= $row["numordcom"];
				$ls_nompro=    $row["nompro"];
				$ls_codpro=    $row["cod_pro"];
				$ld_fecordcom= $row["fecordcom"];
				$li_montot=    $row["montot"];
				$ls_coduniadm= $row["coduniadm"];
				$ls_denuniadm= $row["denuniadm"];
				$ld_fecordcom=$this->io_funcion->uf_convertirfecmostrar($ld_fecordcom);
				if($ls_coduniadm=="")
				{
					$aa_coduniadm= "";
					$aa_denuniadm="";
					$arrResultado=$this->uf_load_soc_enlace_sep($this->ls_codemp,$ls_numordcom,$aa_coduniadm,$aa_denuniadm);	
					$aa_coduniadm = $arrResultado['aa_coduniadm'];
					$aa_denuniadm = $arrResultado['aa_denuniadm'];
					$lb_valido = $arrResultado['lb_valido'];
					if($lb_valido)
					{
						$li_total=count((array)$aa_coduniadm);
						$li_montot=number_format($li_montot,2,",",".");
						for($li_j=1;$li_j<=$li_total;$li_j++)
						{
							$ls_coduniadm= $aa_coduniadm[$li_j];
							$ls_denuniadm= $aa_denuniadm[$li_j];
							$ai_totrows=$ai_totrows+1;
							$ao_object[$ai_totrows][1]="<input  name=txtnumordcom".$ai_totrows."  type=text   id=txtnumordcom".$ai_totrows."  class=sin-borde size=20  maxlength=15 value='".$ls_numordcom."' readonly>";
							$ao_object[$ai_totrows][2]="<input  name=txtnompro".$ai_totrows."     type=text   id=txtnompro".$ai_totrows."     class=sin-borde size=40  maxlength=40 value='".$ls_nompro."'    readonly>".
													   "<input  name=txtcodpro".$ai_totrows."     type=hidden id=txtcodpro".$ai_totrows."     class=sin-borde size=40  maxlength=40 value='".$ls_codpro."'    readonly>";
							$ao_object[$ai_totrows][3]="<input  name=txtdenuniadm".$ai_totrows."  type=text   id=txtdenuniadm".$ai_totrows."  class=sin-borde size=45 maxlength=100 value='".$ls_denuniadm."' readonly>".
													   "<input  name=txtcoduniadm".$ai_totrows."  type=hidden id=txtcoduniadm".$ai_totrows."  class=sin-borde size=40  maxlength=40 value='".$ls_coduniadm."' readonly>";
							$ao_object[$ai_totrows][4]="<input  name=txtfecordcom".$ai_totrows."  type=text   id=txtfecordcom".$ai_totrows."  class=sin-borde size=12  maxlength=12 value='".$ld_fecordcom."' readonly>";
							$ao_object[$ai_totrows][5]="<input  name=txtmontot".$ai_totrows."     type=text   id=txtmontot".$ai_totrows."     class=sin-borde size=15  maxlength=15 value='".$li_montot."'    readonly style=text-align:right>";
							$ao_object[$ai_totrows][6]="<input  name=chkprocesar".$ai_totrows."   type='checkbox' class= sin-borde value=1 onchange=ue_validar_oc(".$ai_totrows.")>";
						}
					}

				}
				else
				{
					$ai_causado = 0;
					$arrResultado=$this->uf_load_monto_causado_anterior($ls_numordcom,"SOCCOC",$ai_causado);
					$ai_causado = $arrResultado['ai_monto'];
					$lb_valido=$arrResultado['lb_valido'];
					if(($lb_valido)&&($li_montot>$ai_causado))
					{
						$li_monreverso=($li_montot-$ai_causado);
						$li_montot=number_format($li_montot,2,",",".");
						$li_monreverso=number_format($li_monreverso,2,",",".");
						$ai_totrows=$ai_totrows+1;
						$ao_object[$ai_totrows][1]="<input  name=txtnumordcom".$ai_totrows."  type=text   id=txtnumordcom".$ai_totrows."  class=sin-borde size=20 maxlength=15  value='".$ls_numordcom."' readonly>";
						$ao_object[$ai_totrows][2]="<input  name=txtnompro".$ai_totrows."     type=text   id=txtnompro".$ai_totrows."     class=sin-borde size=35 maxlength=40  value='".$ls_nompro."'    readonly>".
												   "<input  name=txtcodpro".$ai_totrows."     type=hidden id=txtcodpro".$ai_totrows."     class=sin-borde size=35 maxlength=40  value='".$ls_codpro."'    readonly>";
						$ao_object[$ai_totrows][3]="<input  name=txtdenuniadm".$ai_totrows."  type=text   id=txtdenuniadm".$ai_totrows."  class=sin-borde size=35 maxlength=100 value='".$ls_denuniadm."' readonly>".
												   "<input  name=txtcoduniadm".$ai_totrows."  type=hidden id=txtcoduniadm".$ai_totrows."  class=sin-borde size=35 maxlength=40  value='".$ls_coduniadm."' readonly>";
						$ao_object[$ai_totrows][4]="<input  name=txtfecordcom".$ai_totrows."  type=text   id=txtfecordcom".$ai_totrows."  class=sin-borde size=12 maxlength=12  value='".$ld_fecordcom."' readonly>";
						$ao_object[$ai_totrows][5]="<input  name=txtmontot".$ai_totrows."     type=text   id=txtmontot".$ai_totrows."     class=sin-borde size=12 maxlength=15  value='".$li_montot."'    readonly style=text-align:right>";
						$ao_object[$ai_totrows][6]="<input  name=txtmonrev".$ai_totrows."     type=text   id=txtmonrev".$ai_totrows."     class=sin-borde size=12 maxlength=15  value='".$li_monreverso."' readonly style=text-align:right>";
						$ao_object[$ai_totrows][7]="<input  name=chkprocesar".$ai_totrows."   type='checkbox' class= sin-borde value=1>";
					}
				}

			}//while
			if ($li_i==0)
			{
				$ai_totrows=$ai_totrows+1;
				$ao_object[$ai_totrows][1]="<input  name=txtnumordcom".$ai_totrows."  type=text id=txtnumordcom".$ai_totrows."  class=sin-borde size=15 maxlength=15 readonly>";
				$ao_object[$ai_totrows][2]="<input  name=txtnompro".$ai_totrows."     type=text id=txtnompro".$ai_totrows."     class=sin-borde size=40 maxlength=40 readonly>";
				$ao_object[$ai_totrows][3]="<input  name=txtdenuniadm".$ai_totrows."  type=text id=txtdenuniadm".$ai_totrows."  class=sin-borde size=40 maxlength=40 readonly>";
				$ao_object[$ai_totrows][4]="<input  name=txtfecordcom".$ai_totrows."  type=text id=txtfecordcom".$ai_totrows."  class=sin-borde size=12 maxlength=12 readonly>";
				$ao_object[$ai_totrows][5]="<input  name=txtmontot".$ai_totrows."     type=text id=txtmontot".$ai_totrows."     class=sin-borde size=15 maxlength=15 readonly>";
				$ao_object[$ai_totrows][6]="<input  name=txtmonrev".$ai_totrows."     type=text   id=txtmonrev".$ai_totrows."   class=sin-borde size=12 maxlength=15 readonly >";
				$ao_object[$ai_totrows][7]="<input  name=chkprocesar".$ai_totrows."   type='checkbox'class= sin-borde value=1>";
			}
			$this->io_sql->free_result($rs_data);
		}
		if ($ai_totrows==0)
		{$lb_valido=false;}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end  function uf_siv_load_ordenes

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
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_load_soc_enlace_sep ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
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


	function uf_siv_update_statusorden($as_codemp,$as_numordcom,$as_estpenalm,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_statusorden
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
		$ls_sql= "UPDATE soc_ordencompra".
				 "   SET estpenalm='".$as_estpenalm."'".
				 " WHERE codemp='".$as_codemp."'".
				 "   AND numordcom='".$as_numordcom."'";
		$rs_data=$this->io_sql->execute($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_siv_update_statusorden ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Modificó el estatus de la orden de compra numero ".$as_numordcom." Asociada a la Empresa ".$as_codemp;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion); 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}  // end  function uf_siv_update_statusorden

	function uf_siv_load_dt_ordencompra($as_codemp,$as_numordcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_dt_ordencompra
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $ai_totrows   // total de filas encontradas
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los articulos asociados a una  orden de compra ordenados por el campo "orden" en la
		//				   tabla de  soc_dt_bienes 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/09/2006							Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT soc_dt_bienes.*,siv_articulo.codunimed,".
			    "       (SELECT unidad FROM siv_unidadmedida ".
			    "	      WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) AS unidades".
				" FROM soc_dt_bienes,siv_articulo".
				" WHERE soc_dt_bienes.codemp='". $as_codemp ."'".
 			    " AND soc_dt_bienes.codart=siv_articulo.codart".
				" AND numordcom='". $as_numordcom ."'".
				" ORDER BY orden";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_obtener_dt_bienes ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codart=    $row["codart"];
				$ls_unidad=    $row["unidad"];
				$li_unidad=    $row["unidades"];
				$li_preuniart= $row["preuniart"];
				$li_canoriart= $row["canart"];
			}//while
		}//else
		$this->io_sql->free_result($rs_data);
		return $lb_valido;
	} // end function uf_siv_load_dt_ordencompra

	function uf_siv_load_ordencompra($as_codemp,$as_numordcom,$ad_fecordcom,$as_coduniadm)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_ordencompra
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa 
		//                 $as_numordcom // numero de orden de compra
		//                 $ad_fecordcom // fecha orden de compra
		//                 $as_coduniadm // codigo de unidad administradora (ejecutora)
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que obtiene la fecha y la unidad ejecutora de una orden de compra
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/09/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT * FROM soc_ordencompra  ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND numordcom='".$as_numordcom."'".
				  "   AND (estcondat='B' OR estcondat='-')";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_siv_load_ordencompra ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ad_fecordcom=$row["fecordcom"];
				$as_coduniadm=$row["coduniadm"];
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['ad_fecordcom']=$ad_fecordcom;
		$arrResultado['as_coduniadm']=$as_coduniadm;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_siv_load_ordencompra
	
	function uf_siv_load_cargosarticulo($as_codemp,$as_codart,$as_numordcom,$ai_preuniart,$ai_pendiente,$ai_unidad,$aa_moncar,$aa_spgcuenta,$ai_i)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_cargosarticulo
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa 
		//                 $as_codart    // codigo de articulo
		//                 $ai_preuniart // precio unitario del articulo
		//                 $ai_pendiente // cantidad de articulos que no se entregaron en el almacen
		//                 $ai_unidad    // cantidad de articulos por unidad
		//                 $aa_moncar    // arreglo de montos  de los cargos por articulo
		//                 $aa_spgcuenta // arreglo de cuentas presupuestarias de los  cargos
		//                 $ai_i         // cantidad de cargos por articulos
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que obtiene los cargos asociados al determinado articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/09/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		require_once("../shared/class_folder/evaluate_formula.php");
		$io_evaluate= new evaluate_formula();
		
		$ls_sql="SELECT codcar,formula,".
				"      (SELECT spg_cuenta FROM sigesp_cargos".
				"	     WHERE soc_dta_cargos.codemp = sigesp_cargos.codemp".
				"          AND soc_dta_cargos.codcar = sigesp_cargos.codcar) AS spg_cuenta".
				"  FROM soc_dta_cargos".
				" WHERE codemp='".$as_codemp."'".
				"   AND numordcom='".$as_numordcom."'".
				"   AND codart='".$as_codart."'";
		
		/*$ls_sql = "SELECT codcar,".
				  "       (SELECT spg_cuenta FROM sigesp_cargos".
				  "	        WHERE siv_cargosarticulo.codemp = sigesp_cargos.codemp".
				  "           AND siv_cargosarticulo.codcar = sigesp_cargos.codcar) AS spg_cuenta,".
				  "       (SELECT porcar FROM sigesp_cargos".
				  "	        WHERE siv_cargosarticulo.codemp = sigesp_cargos.codemp".
				  "           AND siv_cargosarticulo.codcar = sigesp_cargos.codcar) AS porcar".
				  "  FROM siv_cargosarticulo".
				  "	WHERE siv_cargosarticulo.codemp='".$as_codemp."'".
				  "   AND siv_cargosarticulo.codart='".$as_codart."'";*/
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_siv_load_cargosarticulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_formula=$row["formula"];
				$ls_spgcuenta= $row["spg_cuenta"];
				$li_moncarart=$io_evaluate->uf_evaluar($ai_formula,$ai_preuniart,$lb_valido);
				$li_moncar=($ai_pendiente  * $li_moncarart);
				$this->ds->insertRow("spg_cuenta",$ls_spgcuenta);
				$this->ds->insertRow("moncar",$li_moncar);
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['aa_moncar']=$aa_moncar;
		$arrResultado['aa_spgcuenta']=$aa_spgcuenta;
		$arrResultado['ai_i']=$ai_i;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_siv_load_cargosarticulo

	function uf_siv_load_estpre($as_codemp,$as_coduniadm,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_estpre
		//         Access: public  
		//      Argumento: $as_codemp      // codigo de empresa 
		//                 $as_coduniadm   // codigo de la unidad ejecutora
		//				   $as_codestpro1  //codigo de estructura programatica nivel 1
		//				   $as_codestpro2  //codigo de estructura programatica nivel 2
		//				   $as_codestpro3  //codigo de estructura programatica nivel 3
		//				   $as_codestpro4  //codigo de estructura programatica nivel 4
		//				   $as_codestpro5  //codigo de estructura programatica nivel 5
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que obtiene la estructura presupuestaria de una unidad ejecutora
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/09/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5".
				  "  FROM spg_unidadadministrativa  ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND coduniadm='".$as_coduniadm."'";// print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_siv_load_estpre ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_codestpro1=$row["codestpro1"];
				$as_codestpro2=$row["codestpro2"];
				$as_codestpro3=$row["codestpro3"];
				$as_codestpro4=$row["codestpro4"];
				$as_codestpro5=$row["codestpro5"];
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_siv_load_estpre

	function uf_siv_load_cuentaspg($as_codemp,$as_codart,$as_spgcuenta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_cuentaspg
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa 
		//                 $as_codart    // codigo de articulo
		//                 $as_spgcuenta // cuenta presupuestaria
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que obtiene la cuenta presupuestaria de un articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/09/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT spg_cuenta".
				  "  FROM siv_articulo  ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codart='".$as_codart."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_siv_load_cuentaspg ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_spgcuenta=$row["spg_cuenta"];
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['as_spgcuenta']=$as_spgcuenta;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_siv_load_cuentaspg

	function uf_siv_procesar_comprobante($as_codemp,$as_procede,$as_numordcom,$ad_newfeccmp,$as_newprocede,$as_newcomprobante,
										 $ad_feccmp,$as_tipodestino,$as_codpro,$as_cedbene,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_comprobante
		//         Access: public  
		//      Argumento: $as_codemp         // codigo de empresa 
		//                 $as_procede        // procedencia del comprobante
		//                 $as_numordcom      // numero de orden de compra
		//                 $ad_feccmp         // fecha del comprobante actual
		//                 $ad_newfeccmp      // fecha del comprobante a registrar
		//                 $ad_newprocede     // procedencia del comprobante a registrar
		//                 $as_newcomprobante // numero del comprobante a registrar
		//                 $as_newcomprobante // numero del comprobante a registrar
		//                 $aa_seguridad      // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que obtiene la cuenta presupuestaria de un articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 11/09/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_tipocomp="";
		$arrResultado=$this->uf_siv_load_cmp($as_codemp,$as_procede,$as_numordcom,$ad_feccmp,$as_tipocomp,$as_tipodestino,$as_codpro,$as_cedbene);
		$ad_feccmp = $arrResultado['ad_feccmp'];
		$as_tipocomp = $arrResultado['as_tipocomp'];
		$as_tipodestino = $arrResultado['as_tipodestino'];
		$as_codpro = $arrResultado['as_codpro'];
		$as_cedbene = $arrResultado['as_cedbene'];
		$lb_valido=$arrResultado['lb_valido'];
		if($lb_valido)
		{
			$arrResultado=$this->uf_siv_obtener_numerocomprobante($as_codemp,$as_newprocede,$as_newcomprobante);
			$as_newcomprobante = $arrResultado['as_comprobante'];
			$lb_valido = $arrResultado['lb_valido'];
		}
		$arrResultado['as_newcomprobante']=$as_newcomprobante;
		$arrResultado['ad_feccmp']=$ad_feccmp;
		$arrResultado['as_tipodestino']=$as_tipodestino;
		$arrResultado['as_codpro']=$as_codpro;
		$arrResultado['as_cedbene']=$as_cedbene;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_siv_procesar_comprobante
	
	function uf_siv_load_dt_pendiente($as_codemp,$as_numordcom,$as_coduniadm,$li_totmonart,$li_totmoncar,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_dt_pendiente
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $li_totmonart // total de monto por recibir (costo de articulos)
		//  			   $li_totmoncar // total de monto por recibir (cargos)
		//  			   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los articulos asociados a una orden de compra ordenados por el campo "orden" en la
		//				   tabla de soc_dt_bienes, y por articulo busca en la tabla siv_dt_recepcion los pendientes asociados a esos 
		//				   articulos para luego calcular los montos que no se recibieron en el proceso de recepcion de sumistros
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/09/2006							Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_totmonart=0;
		$li_totmoncar=0;
		$ad_fecordcom="";
/*		$lb_valido=$this->uf_siv_load_estpre($as_codemp,$as_coduniadm,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5);*/
		if($lb_valido)
		{
			$ls_sql="SELECT codart,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla".
					"  FROM soc_dt_bienes".
					" WHERE codemp='". $as_codemp ."'".
					"   AND numordcom='". $as_numordcom ."'".
					" GROUP BY codart,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla"; //print $ls_sql;
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$lb_valido=false;
				$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_load_dt_pendiente_I ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				return false;
			}
			else
			{
				$ls_gestor=$_SESSION["ls_gestor"];
				$ls_procede="SOCCOC";
				$ls_descmp="Cierre de Orden de Compra";
				$ld_newfeccmp=date("Y-m-d");
				$ls_newprocede="SOCROC";
				$ls_tipo_destino= "-";
				$ls_codigo_destino="----------";
				$li_tipo_comp='1';
				$ls_codban  = "---";
				$ls_ctaban  = "-------------------------";
				$arrResultado=$this->uf_siv_procesar_comprobante($as_codemp,$ls_procede,$as_numordcom,$ld_newfeccmp,$ls_newprocede,
															     $as_newcomprobante,$ad_feccmp,$ls_tipo_destino,$ls_codpro,$ls_cedbene,
															     $aa_seguridad);
				$as_newcomprobante = $arrResultado['as_newcomprobante'];
				$ad_feccmp = $arrResultado['ad_feccmp'];
				$ls_tipo_destino = $arrResultado['as_tipodestino'];
				$ls_codpro = $arrResultado['as_codpro'];
				$ls_cedbene = $arrResultado['as_cedbene'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{ 
					switch ($ls_tipo_destino)
					{
						case "P":
							$ls_codigo_destino=$ls_codpro;
						break;
						case "B":
							$ls_codigo_destino=$ls_cedbene;
						break;
					}
					
					$lb_valido = $this->io_sigesp_int->uf_int_init($as_codemp,$ls_newprocede,$as_newcomprobante,$ld_newfeccmp,
					                                               $ls_descmp,$ls_tipo_destino,$ls_codigo_destino,false,$ls_codban,
																   $ls_ctaban,$li_tipo_comp);/////modificado 04/12/2007 
				}
				while($row=$this->io_sql->fetch_row($rs_data))
				{
					$ls_codart=$row["codart"];
					$ls_aux="";
					$la_alternos=$this->uf_obtener_alternos($ls_codart);
					if(!empty($la_alternos))
					{
						$li_total=count((array)$la_alternos);
						if($li_total>0)
						{
							for($li_i=1;$li_i<=$li_total;$li_i++)
							{
								$ls_aux=$ls_aux."OR siv_dt_recepcion.codart='".$la_alternos[$li_i]."'";
							}
						}
					}
					$ls_aux=$ls_aux. ")";
					$as_codestpro1=$row["codestpro1"];
					$as_codestpro2=$row["codestpro2"];
					$as_codestpro3=$row["codestpro3"];
					$as_codestpro4=$row["codestpro4"];
					$as_codestpro5=$row["codestpro5"];
					$as_estcla=$row["estcla"];
					$ls_sql=  "SELECT siv_dt_recepcion.*,siv_articulo.codunimed,siv_cargosarticulo.codcar,".
							  "      (SELECT unidad FROM siv_unidadmedida ".
							  "	       WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) AS unidades,".
							  "     (SELECT unidad FROM siv_articulo as padre,siv_unidadmedida ".
							  "       WHERE padre.codart=siv_articulo.codartpri".
							  "         AND padre.codunimed=siv_unidadmedida.codunimed) AS unidadespri".
							  "  FROM siv_dt_recepcion, siv_recepcion,siv_articulo,siv_cargosarticulo".
							  " WHERE  siv_dt_recepcion.codemp=siv_recepcion.codemp".
							  "   AND siv_dt_recepcion.codart=siv_articulo.codart".
							  "   AND siv_dt_recepcion.numordcom=siv_recepcion.numordcom".
							  "   AND siv_dt_recepcion.numconrec=siv_recepcion.numconrec ".
							  "   AND siv_dt_recepcion.codemp='".$as_codemp."'".
							  "   AND siv_dt_recepcion.numordcom='".$as_numordcom."'".
							  "   AND siv_recepcion.estrec=0".
							  "   AND (siv_dt_recepcion.codart='".$ls_codart."'".
							  $ls_aux.
							  " ORDER BY siv_dt_recepcion.numconrec DESC LIMIT  1";
					$rs_data1=$this->io_sql->select($ls_sql);
					if($rs_data1===false)
					{
						$lb_valido=false;
						$this->io_msg->message("CLASE->recepcion MÉTODO->uf_siv_load_dt_pendiente_II ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
					}
					else
					{
						if($row=$this->io_sql->fetch_row($rs_data1))
						{
							$li_penart= $row["penart"];
							$li_adicionales=0;
							$arrResultado=$this->uf_select_articulosrelacionados($as_codemp,$as_numordcom,$ls_codart,$li_adicionales);
							$li_adicionales = $arrResultado['ai_adicionales'];
							$lb_valido=$arrResultado['lb_valido'];
							
							$li_penart=$li_penart-$li_adicionales;
							if($li_penart>0)
							{
								$ls_unidad=    $row["unidad"];
								$li_unidad=    $row["unidadespri"];
								if($li_unidad=="")
								{
									$li_unidad=    $row["unidades"];
								}
								$li_preuniart= $row["preuniart"];
								$li_canoriart= $row["canoriart"];
								if($ls_unidad=="D")
								{$li_unidad=1;}
								$as_spgcuenta="";
								$li_monart=($li_preuniart * $li_penart * $li_unidad);
								$arrResultado=$this->uf_siv_load_cuentaspg($as_codemp,$ls_codart,$as_spgcuenta);
								$as_spgcuenta=$arrResultado['as_spgcuenta'];
								$lb_valido=$arrResultado['lb_valido'];
								if($lb_valido)
								{
									$arrResultado=$this->uf_siv_load_cargosarticulo($as_codemp,$ls_codart,$as_numordcom,$li_preuniart,
																				 $li_penart,$li_unidad,$la_moncar,
																				 $aa_spgcargos,$li_i);
									$la_moncar = $arrResultado['aa_moncar'];
									$aa_spgcargos = $arrResultado['aa_spgcuenta'];
									$li_i = $arrResultado['ai_i'];
									$lb_valido=$arrResultado['lb_valido'];
								}
								if($lb_valido)
								{
									$li_totmonart=$li_totmonart + $li_monart;
									$li_monartaux="-".$li_monart;
									$arrResultado=$this->uf_siv_load_dt_cmp($as_codemp,$ls_procede,$as_numordcom,$as_codestpro1,
																		    $as_codestpro2,$as_codestpro3,$as_codestpro4,
																		    $as_codestpro5,$as_estcla,$as_spgcuenta,$as_operacion,
																		    $as_documento,$as_procede,$ai_orden);
									$as_operacion = $arrResultado['as_operacion'];
									$as_documento = $arrResultado['as_documento'];
									$as_procede = $arrResultado['as_procededoc'];
									$ai_orden = $arrResultado['ai_orden'];
									$lb_valido=$arrResultado['lb_valido'];

									if($lb_valido)
									{
										$ls_mensaje=$this->io_sigesp_spg->uf_operacion_codigo_mensaje($as_operacion);				
										$lb_valido = $this->io_sigesp_int->uf_spg_insert_datastore($as_codemp,$as_codestpro1,
																		  $as_codestpro2,$as_codestpro3,$as_codestpro4,
																		  $as_codestpro5,$as_estcla,$as_spgcuenta,$ls_mensaje,
																		  $li_monartaux,$as_documento,$ls_procede,$ls_descmp);
									}
																		
								}
							}
						}//if($row=$this->io_sql->fetch_row($rs_data1))
						else
						{
							$this->io_msg->message("No tiene Entradas al Almacen asociadas");	
							return false;
						}
					}//else
				}//while($row=$this->io_sql->fetch_row($li_exec))
				$this->ds->group_by(array('0'=>'spg_cuenta'),array('0'=>'moncar'),'moncar');	
				$totrow=$this->ds->getRowCount("spg_cuenta");
				for($z=1;$z<=$totrow;$z++)
				{
					$ls_spgcuenta=$this->ds->data["spg_cuenta"][$z];
					$li_moncar=   $this->ds->data["moncar"][$z];
					$li_moncaraux="-".$li_moncar;
					$arrResultado=$this->uf_siv_load_dt_cmp($as_codemp,$ls_procede,$as_numordcom,$as_codestpro1,$as_codestpro2,
														    $as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,$ls_spgcuenta,$as_operacion,
														    $as_documento,$as_procede,$ai_orden);
					$as_operacion = $arrResultado['as_operacion'];
					$as_documento = $arrResultado['as_documento'];
					$as_procede = $arrResultado['as_procededoc'];
					$ai_orden = $arrResultado['ai_orden'];
					$lb_valido=$arrResultado['lb_valido'];
					if($lb_valido)
					{
						$ls_mensaje=$this->io_sigesp_spg->uf_operacion_codigo_mensaje($as_operacion);				
						$lb_valido = $this->io_sigesp_int->uf_spg_insert_datastore($as_codemp,$as_codestpro1,
														  $as_codestpro2,$as_codestpro3,$as_codestpro4,
														  $as_codestpro5,$as_estcla,$ls_spgcuenta,$ls_mensaje,
														  $li_moncaraux,$as_documento,$ls_procede,$ls_descmp);
					}
				}
			}
			if  ($lb_valido)
			{
			   $lb_valido = $this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
			   if ( $lb_valido===false)
			   {		   	 
				 $this->io_msg->message("".$this->io_sigesp_int->is_msg_error);
			   }
		   
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['li_totmonart']=$li_totmonart;
		$arrResultado['li_totmoncar']=$li_totmoncar;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end function uf_siv_obtener_dt_pendiente

	//-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_obtener_alternos($as_codart)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_formatonumerico
		//	Arguments:    as_codart  // Codigo de articulo
		//	Returns:	  $la_alternos arreglo que contiene codigos alternos
		//	Description:  Función que obtiene los codigos alternos relacionados con determinado articulo
		//////////////////////////////////////////////////////////////////////////////
		global $io_sql;
		$la_alternos="";
		$li_i=0;
		$ls_sql="SELECT codart".
				"  FROM siv_articulo".
				" WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'".
				"   AND codartpri='".$as_codart."' ";
		$rs_data=$io_sql->select($ls_sql);
		while(!$rs_data->EOF)
		{
			$li_i++;
			$la_alternos[$li_i]= $rs_data->fields["codart"];
			$rs_data->fields["codart"];
			$rs_data->MoveNext();
		}
		return $la_alternos;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_select_articulosrelacionados($as_codemp,$as_numordcom,$ls_codart,$ai_adicionales)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_articulosrelacionados
		//         Access: public (sigesp_siv_d_articulo)
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ai_adicionales=0;
		$ls_sql="SELECT SUM(siv_dt_recepcion.canart) AS canart".
			  	"  FROM siv_dt_recepcion, siv_recepcion,siv_articulo".
			  	" WHERE  siv_dt_recepcion.codemp=siv_recepcion.codemp".
			  	"   AND siv_dt_recepcion.codart=siv_articulo.codart".
			  	"   AND siv_dt_recepcion.numordcom=siv_recepcion.numordcom".
			  	"   AND siv_dt_recepcion.numconrec=siv_recepcion.numconrec ".
			  	"   AND siv_dt_recepcion.codemp='". $as_codemp ."'".
			  	"   AND siv_dt_recepcion.numordcom='". $as_numordcom ."'".
			  	"   AND siv_recepcion.estrec=0".
				"   AND siv_articulo.codartpri='". $ls_codart ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_siv_select_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ai_adicionales=$row["canart"];
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['ai_adicionales']=$ai_adicionales;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_siv_select_articulo
	//-----------------------------------------------------------------------------------------------------------------------------

	function uf_siv_load_cmp($as_codemp,$as_procede,$as_comprobante,$ad_feccmp,$as_tipocomp,$as_tipodestino,$as_codpro,$as_cedbene)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_cmp
		//         Access: public  
		//      Argumento: $as_codemp      // codigo de empresa 
		//                 $as_procede     // codigo de procedencia
		//                 $as_comprobante // numero de comprobante
		//                 $ad_feccmp      // fecha de comprobante
		//                 $as_tipocomp    // tipo de comprobante
		//                 $as_tipodestino // tipo de destino
		//                 $as_codpro      // codigo de proveedor
		//                 $as_cedbene     // cedula de beneficiario
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que obtiene los datos (maestro) de un comprobante
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/09/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT * FROM sigesp_cmp  ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND   procede='".$as_procede."'".
				  "   AND   comprobante='".$as_comprobante."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_siv_load_cmp ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ad_feccmp=      $row["fecha"];
				$as_tipocomp=    $row["tipo_comp"];
				$as_tipodestino= $row["tipo_destino"];
				$as_codpro=      $row["cod_pro"];
				$as_cedbene=     $row["ced_bene"];
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
			else
			$this->io_msg->message("La orden de compra no ha sido contabilizada");
		}
		$arrResultado['ad_feccmp']=$ad_feccmp;
		$arrResultado['as_tipocomp']=$as_tipocomp;
		$arrResultado['as_tipodestino']=$as_tipodestino;
		$arrResultado['as_codpro']=$as_codpro;
		$arrResultado['as_cedbene']=$as_cedbene;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_siv_load_cmp

	function uf_siv_load_dt_cmp($as_codemp,$as_procede,$as_comprobante,$as_codestpro1,$as_codestpro2,$as_codestpro3,
								$as_codestpro4,$as_codestpro5,$as_estcla,$as_spgcuenta,$as_operacion,$as_documento,$as_procededoc,$ai_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_dt_cmp
		//         Access: public  
		//      Argumento: $as_codemp      // codigo de empresa 
		//                 $as_procede     // codigo de procedencia
		//                 $as_comprobante // numero de comprobante
		//                 $as_codestpro1  // codigo de estructura programatica nivel 1
		//                 $as_codestpro2  // codigo de estructura programatica nivel 2
		//                 $as_codestpro3  // codigo de estructura programatica nivel 3
		//                 $as_codestpro4  // codigo de estructura programatica nivel 4 
		//                 $as_codestpro5  // codigo de estructura programatica nivel 5
		//                 $as_spgcuenta   // codigo de estructura programatica nivel 5
		//                 $as_operacion   // tipo de operacion
		//                 $as_documento   // numero de documento
		//                 $as_procededoc  // procedencia del documento
		//                 $ai_orden       // orden de registro
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que obtiene los datos relacionados a un comprobante
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/09/2006 								Fecha Última Modificación : 11/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT * FROM spg_dt_cmp  ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND procede='".$as_procede."'".
				  "   AND comprobante='".$as_comprobante."'".
				  "   AND codestpro1='".$as_codestpro1."'".
				  "   AND codestpro2='".$as_codestpro2."'".
				  "   AND codestpro3='".$as_codestpro3."'".
				  "   AND codestpro4='".$as_codestpro4."'".
				  "   AND codestpro5='".$as_codestpro5."'".
				  "   AND estcla='".$as_estcla."'".
				  "   AND spg_cuenta='".$as_spgcuenta."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_siv_load_dt_cmp ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_operacion  = $row["operacion"];
				$as_documento  = $row["documento"];
				$as_procededoc = $row["procede_doc"];
				$ai_orden =      $row["orden"];
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['as_operacion']=$as_operacion;
		$arrResultado['as_documento']=$as_documento;
		$arrResultado['as_procededoc']=$as_procededoc;
		$arrResultado['ai_orden']=$ai_orden;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_siv_load_dt_cmp

	function uf_siv_obtener_numerocomprobante($as_codemp,$as_procede,$as_comprobante)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_obtener_numerocomprobante
		//         Access: public  
		//      Argumento: $as_codemp      // codigo de empresa 
		//                 $as_procede     // codigo de procedencia
		//                 $as_comprobante // numero de comprobante
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que obtiene el numero del comprobante que se desea generar
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/09/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT comprobante FROM sigesp_cmp ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND procede='".$as_procede."'".
				  " ORDER BY comprobante DESC";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_siv_obtener_numerocomprobante ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_comprobante= $row["comprobante"];
				$li_len=strlen($as_comprobante);
				$ls_nro=substr($as_comprobante,6,$li_len);
				settype($ls_nro,'int');
				$as_comprobante=$ls_nro + 1;
				$as_comprobante=$this->io_funcion->uf_cerosizquierda($as_comprobante,15);
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
			else{
				$as_comprobante=$this->io_funcion->uf_cerosizquierda('1',15);
				$lb_valido=true;
			}
		} 
		$arrResultado['as_comprobante']=$as_comprobante;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_siv_obtener_numerocomprobante

	function uf_load_comprobante($as_codemp,$as_numordcom,$as_comprobante,$ad_feccmp,$as_codban,$as_ctaban)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_load_comprobante
		//	           Access: public
		//  		Arguments: $as_codemp      // codigo de empresa
		//  			       $as_numordcom   // numero de orden de compra
		//  			       $as_comprobante // numero de comprobante
		//  			       $ad_feccmp      // fecha del comprobante
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de buscar el numero y la fecha del comprobante dado una orden de compra
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 13/09/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT comprobante,fecha,MAX(codban) AS codban,MAX(ctaban) AS ctaban".
				"  FROM spg_dt_cmp".
				" WHERE codemp='". $as_codemp ."'".
				"   AND procede='SOCROC'".
				"   AND procede_doc='SOCCOC'".
				"   AND operacion='CS'".
				"   AND documento='".$as_numordcom."'".
				" GROUP BY comprobante,fecha".
				" ORDER BY comprobante,fecha DESC LIMIT 1";
	    $rs_data=$this->io_sql->select($ls_sql);
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_load_comprobante ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_comprobante= $row["comprobante"];
				$ad_feccmp= $row["fecha"];
				$as_codban= $row["codban"];
				$as_ctaban= $row["ctaban"];
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_comprobante']=$as_comprobante;
		$arrResultado['ad_feccmp']=$ad_feccmp;
		$arrResultado['as_codban']=$as_codban;
		$arrResultado['as_ctaban']=$as_ctaban;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} //fin  function uf_load_comprobante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_compromisos($as_codemp,$as_numdoccom,$as_codpro,$aa_seguridad)
   	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_compromisos
		//		   Access: private
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda de compromisos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 09/05/2007 								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_gestor=$_SESSION["ls_gestor"];
		$ls_procede="SOCCOC";
		$ls_descmp="Cierre de Orden de Compra ".$as_numdoccom;
		$ld_newfeccmp=date("Y-m-d");
		$ls_newprocede="SOCROC";
		$ls_tipo_destino= "-";
		$ls_codigo_destino="----------";
		$li_tipo_comp='1';
		$ls_codban  = "---";
		$ls_ctaban  = "-------------------------";
		$ls_cedbene="----------";
		$ls_tipo_destino="P";
		$li_orden="";
		$as_newcomprobante="";
		$ad_feccmp="";
		$this->io_sigesp_int->uf_int_init_transaction_begin();
		$arrResultado=$this->uf_siv_procesar_comprobante($as_codemp,$ls_procede,$as_numdoccom,$ld_newfeccmp,$ls_newprocede,
													  $as_newcomprobante,$ad_feccmp,$ls_tipo_destino,$as_codpro,$ls_cedbene,
													  $aa_seguridad);
		$as_newcomprobante = $arrResultado['as_newcomprobante'];
		$ad_feccmp = $arrResultado['ad_feccmp'];
		$ls_tipo_destino = $arrResultado['as_tipodestino'];
		$ls_codpro = $arrResultado['as_codpro'];
		$ls_cedbene = $arrResultado['as_cedbene'];
		$lb_valido=$arrResultado['lb_valido'];
		if($lb_valido)
		{
			$lb_valido=$this->io_sigesp_int->uf_int_init($as_codemp,$ls_newprocede,$as_newcomprobante,$ld_newfeccmp,
														 $ls_descmp,$ls_tipo_destino,$as_codpro,false,$ls_codban,
														 $ls_ctaban,$li_tipo_comp);
			if($lb_valido)
			{
				$rs_cuentascompromiso="";
				$arrResultado=$this->uf_load_cuentas_compromiso($as_numdoccom,$rs_cuentascompromiso);
				$rs_cuentascompromiso = $arrResultado['rs_data'];
				$lb_valido = $arrResultado['lb_valido'];
				while((!$rs_cuentascompromiso->EOF)&&($lb_valido))
				{
					$ls_spgcuenta=$rs_cuentascompromiso->fields["spg_cuenta"];
					$ls_codestpro1=$rs_cuentascompromiso->fields["codestpro1"];
					$ls_codestpro2=$rs_cuentascompromiso->fields["codestpro2"];
					$ls_codestpro3=$rs_cuentascompromiso->fields["codestpro3"];
					$ls_codestpro4=$rs_cuentascompromiso->fields["codestpro4"];
					$ls_codestpro5=$rs_cuentascompromiso->fields["codestpro5"];
					$ls_estcla=$rs_cuentascompromiso->fields["estcla"];
					$ls_monto_compromiso=$rs_cuentascompromiso->fields["monto"];
					$li_monto_causado = 0;
					$arrResultado=$this->uf_load_monto_causados_cuenta($as_numdoccom,$ls_procede,$ls_tipo_destino,$as_codpro,$ls_cedbene,
																       $ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
																       $ls_estcla,$ls_spgcuenta,$li_monto_causado);
					$li_monto_causado = $arrResultado['ai_monto'];
					$lb_valido=$arrResultado['lb_valido'];
					$montoCausadoAnulado = $this->uf_buscar_monto_causados_anulados($as_numdoccom,$ls_procede,$ls_tipo_destino,$as_codpro,$ls_cedbene,
																   $ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
																   $ls_estcla,$ls_spgcuenta);
					$li_monto_causado = $li_monto_causado + $montoCausadoAnulado;
					if($lb_valido)
					{
						$ls_monto_reverso=($ls_monto_compromiso-$li_monto_causado);
						if($ls_monto_reverso>0)
						{	
							$ls_monto_reverso=($ls_monto_reverso*-1);
							$ls_operacion = "";
							$ls_documento = "";

							$arrResultado=$this->uf_siv_load_dt_cmp($as_codemp,$ls_procede,$as_numdoccom,$ls_codestpro1,
																    $ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																    $ls_codestpro5,$ls_estcla,$ls_spgcuenta,$ls_operacion,
																    $ls_documento,$ls_procede,$li_orden);
							$ls_operacion = $arrResultado['as_operacion'];
							$ls_documento = $arrResultado['as_documento'];
							$ls_procede = $arrResultado['as_procededoc'];
							$li_orden = $arrResultado['ai_orden'];
							$lb_valido=$arrResultado['lb_valido'];
							if($lb_valido)
							{
								$ls_mensaje=$this->io_sigesp_spg->uf_operacion_codigo_mensaje($ls_operacion);				
								$lb_valido = $this->io_sigesp_int->uf_spg_insert_datastore($as_codemp,$ls_codestpro1,
																  $ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																  $ls_codestpro5,$ls_estcla,$ls_spgcuenta,$ls_mensaje,
																  $ls_monto_reverso,$ls_documento,$ls_procede,$ls_descmp);
							}
						}
					}
					$rs_cuentascompromiso->MoveNext();
				}
				if  ($lb_valido)
				{
				   $lb_valido = $this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
				   if ( $lb_valido===false)
				   {		   	 
					 $this->io_msg->message("".$this->io_sigesp_int->is_msg_error);
				   }
			   
				}
			}
		}
		return $lb_valido;
	}// end function uf_print_compromisos
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_load_cuentas_compromiso($as_numordcom,$rs_data)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_load_comprobante
		//	           Access: public
		//  		Arguments: $as_codemp      // codigo de empresa
		//  			       $as_numordcom   // numero de orden de compra
		//  			       $as_comprobante // numero de comprobante
		//  			       $ad_feccmp      // fecha del comprobante
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de buscar el numero y la fecha del comprobante dado una orden de compra
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 13/09/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data="";
		$ls_sql="SELECT comprobante,spg_cuenta,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,monto".
				"  FROM spg_dt_cmp".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND procede='SOCCOC'".
				"   AND procede_doc='SOCCOC'".
				"   AND operacion='CS'".
				"   AND comprobante='".$as_numordcom."'".
				" ORDER BY comprobante";
	    $rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->cerrar_oc MÉTODO->uf_load_comprobante ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} //fin  function uf_load_comprobante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_monto_causados_cuenta($as_comprobante,$as_procedencia,$as_tipodestino,$as_codpro,$as_cedbene,
										   $as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
										   $as_estcla,$as_spguenta,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_monto_causados_cuenta
		//		   Access: private
		//	    Arguments: as_comprobante  // Número de Comprobante
		//				   as_procedencia  // Procede del Comprobante
		//				   as_tipodestino  // Tipo Destino
		//				   as_codpro  // Código de proveedor
		//				   as_cedbene  // Código de beneficiario
		//				   as_fechahasta  // Fecha hasta donde se van a tomar los comprobantes
		//				   as_programatica  // Programatica
		//				   as_spguenta  // Cuenta presupuestaria
		//				   ai_monto  // Monto de los ajustes
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Función que se encarga de extraer todos aquellos comprobantes asociados al proveedor y/o beneficiario 
		//				   en estatus 'CG' Compromiso y Gasto Causado y 'CP' Gasto Causado y Pagado 
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 12/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_operacion1="GC";
		$ls_operacion2="CP";
		$ai_monto=0;
		$lb_valido=true; 
		$ls_sql="SELECT SUM(spg_dt_cmp.monto) AS monto ".
				" FROM spg_dt_cmp, sigesp_cmp ".
				" WHERE sigesp_cmp.codemp='".$this->ls_codemp."' ".
				"	AND sigesp_cmp.tipo_destino='".$as_tipodestino."'".
				"	AND TRIM(sigesp_cmp.cod_pro)='".$as_codpro."'".
				"   AND TRIM(sigesp_cmp.ced_bene)='".$as_cedbene."' ".
				"   AND (spg_dt_cmp.operacion='".$ls_operacion1."' OR spg_dt_cmp.operacion='".$ls_operacion2."')".
				"   AND spg_dt_cmp.documento='".$as_comprobante."'".
				"   AND spg_dt_cmp.procede_doc='".$as_procedencia."'".
  		     	"   AND spg_dt_cmp.codestpro1='".$as_codestpro1."' ".
				"   AND spg_dt_cmp.codestpro2='".$as_codestpro2."' ".
			 	"   AND spg_dt_cmp.codestpro3='".$as_codestpro3."' ".
				"   AND spg_dt_cmp.codestpro4='".$as_codestpro4."' ".
			 	"   AND spg_dt_cmp.codestpro5='".$as_codestpro5."' ".
				"   AND spg_dt_cmp.estcla='".$as_estcla."' ".
				"   AND spg_dt_cmp.spg_cuenta='".$as_spguenta."' ".
				"   AND sigesp_cmp.codemp=spg_dt_cmp.codemp ".
				"	AND sigesp_cmp.procede=spg_dt_cmp.procede ".
				"   AND sigesp_cmp.comprobante=spg_dt_cmp.comprobante ".
				"   AND sigesp_cmp.fecha=spg_dt_cmp.fecha ".
				"   AND sigesp_cmp.codban=spg_dt_cmp.codban ".
				"   AND sigesp_cmp.ctaban=spg_dt_cmp.ctaban ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$lb_valido=false; 
			$this->io_mensajes->message("CLASE->Recepción MÉTODO->uf_load_monto_causados_cuenta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	}// end function uf_load_monto_causados_cuenta
	//-----------------------------------------------------------------------------------------------------------------------------------


	function uf_buscar_monto_causados_anulados($as_comprobante,$as_procedencia,$as_tipodestino,$as_codpro,$as_cedbene,
										$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
										$as_estcla,$as_spguenta)
	{
		$ls_operacion1="GC";
		$ls_operacion2="CP";
		$montoAnulado = 0;
		 
		$ls_sql="SELECT spg_dt_cmp.comprobante, spg_dt_cmp.procede ".
				" FROM spg_dt_cmp, sigesp_cmp ".
				" WHERE sigesp_cmp.codemp='".$this->ls_codemp."' ".
				"	AND sigesp_cmp.tipo_destino='".$as_tipodestino."'".
				"	AND TRIM(sigesp_cmp.cod_pro)='".$as_codpro."'".
				"   AND TRIM(sigesp_cmp.ced_bene)='".$as_cedbene."' ".
				"   AND (spg_dt_cmp.operacion='".$ls_operacion1."' OR spg_dt_cmp.operacion='".$ls_operacion2."')".
				"   AND spg_dt_cmp.documento='".$as_comprobante."'".
				"   AND spg_dt_cmp.procede_doc='".$as_procedencia."'".
	  	     	"   AND spg_dt_cmp.codestpro1='".$as_codestpro1."' ".
				"   AND spg_dt_cmp.codestpro2='".$as_codestpro2."' ".
			 	"   AND spg_dt_cmp.codestpro3='".$as_codestpro3."' ".
				"   AND spg_dt_cmp.codestpro4='".$as_codestpro4."' ".
			 	"   AND spg_dt_cmp.codestpro5='".$as_codestpro5."' ".
				"   AND spg_dt_cmp.estcla='".$as_estcla."' ".
				"   AND spg_dt_cmp.spg_cuenta='".$as_spguenta."' ".
				"   AND sigesp_cmp.codemp=spg_dt_cmp.codemp ".
				"	AND sigesp_cmp.procede=spg_dt_cmp.procede ".
				"   AND sigesp_cmp.comprobante=spg_dt_cmp.comprobante ".
				"   AND sigesp_cmp.fecha=spg_dt_cmp.fecha ".
				"   AND sigesp_cmp.codban=spg_dt_cmp.codban ".
				"   AND sigesp_cmp.ctaban=spg_dt_cmp.ctaban ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false){
			$this->io_mensajes->message("CLASE->Recepción MÉTODO->uf_load_monto_causados_cuenta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else{
			while (!$rs_data->EOF) {
				$ls_comprobante = $rs_data->fields['comprobante'];
				$ls_procede     = $rs_data->fields['procede'];
				
				$ls_sql="SELECT SUM(spg_dt_cmp.monto) AS monto".
				" FROM spg_dt_cmp, sigesp_cmp ".
				" WHERE sigesp_cmp.codemp='".$this->ls_codemp."' ".
				"	AND sigesp_cmp.tipo_destino='".$as_tipodestino."'".
				"	AND TRIM(sigesp_cmp.cod_pro)='".$as_codpro."'".
				"   AND TRIM(sigesp_cmp.ced_bene)='".$as_cedbene."' ".
				"   AND (spg_dt_cmp.operacion='".$ls_operacion1."' OR spg_dt_cmp.operacion='".$ls_operacion2."')".
				"   AND spg_dt_cmp.documento='".$ls_comprobante."'".
				"   AND spg_dt_cmp.procede_doc='".$ls_procede."'".
	  	     	"   AND spg_dt_cmp.codestpro1='".$as_codestpro1."' ".
				"   AND spg_dt_cmp.codestpro2='".$as_codestpro2."' ".
			 	"   AND spg_dt_cmp.codestpro3='".$as_codestpro3."' ".
				"   AND spg_dt_cmp.codestpro4='".$as_codestpro4."' ".
			 	"   AND spg_dt_cmp.codestpro5='".$as_codestpro5."' ".
				"   AND spg_dt_cmp.estcla='".$as_estcla."' ".
				"   AND spg_dt_cmp.spg_cuenta='".$as_spguenta."' ".
				"   AND sigesp_cmp.codemp=spg_dt_cmp.codemp ".
				"	AND sigesp_cmp.procede=spg_dt_cmp.procede ".
				"   AND sigesp_cmp.comprobante=spg_dt_cmp.comprobante ".
				"   AND sigesp_cmp.fecha=spg_dt_cmp.fecha ".
				"   AND sigesp_cmp.codban=spg_dt_cmp.codban ".
				"   AND sigesp_cmp.ctaban=spg_dt_cmp.ctaban ";
				$rs_data2 = $this->io_sql->select($ls_sql);
				if ($rs_data2===false){
					$this->io_mensajes->message("CLASE->Recepción MÉTODO->uf_load_monto_causados_cuenta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else{
					if($row=$this->io_sql->fetch_row($rs_data2)){
						$montoAnulado = $montoAnulado + $row["monto"];
					}  
					$this->io_sql->free_result($rs_data2);
				}
				$rs_data->MoveNext();
			} 
			$this->io_sql->free_result($rs_data);
		}
		return $montoAnulado;
	}

}//end  class sigesp_siv_c_cerraroc
?>
