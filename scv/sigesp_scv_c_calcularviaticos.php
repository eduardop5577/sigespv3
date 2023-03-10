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

require_once("../base/librerias/php/general/sigesp_lib_sql.php");
class sigesp_scv_c_calcularviaticos
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;

	public function __construct()
	{
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once("../shared/class_folder/class_sigesp_int.php");
		require_once("../shared/class_folder/class_sigesp_int_int.php");
		require_once("../shared/class_folder/class_sigesp_int_spg.php");
		require_once("../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../shared/class_folder/class_sigesp_int_spi.php");
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		$this->io_msg=new class_mensajes();
		$in=new sigesp_include();
		$this->con=$in->uf_conectar();
        $this->io_sigesp_int=new class_sigesp_int_int();
		$this->io_sigesp_int_spg=new class_sigesp_int_spg();
		$this->io_sigesp_int_scg=new class_sigesp_int_scg();		
		$this->io_sql= new class_sql($this->con);
		$this->io_seguridad=   new sigesp_c_seguridad();
		$this->io_funcion = new class_funciones();
		$this->is_codemp= $_SESSION["la_empresa"]["codemp"];
		
	} // end function sigesp_scv_c_calcularviaticos
		
	function uf_agregarlineablanca($aa_object,$ai_totrows)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 
		//				   $ai_totrows // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que agrega una linea en blanco al final del grid
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 04/10/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]="<input name=txtproasig".$ai_totrows."  type=text   id=txtproasig".$ai_totrows."  class=sin-borde size=16 style='text-align:center'>";
		$aa_object[$ai_totrows][2]="<input name=txtcodasig".$ai_totrows."  type=text   id=txtcodasig".$ai_totrows."  class=sin-borde size=11 >";
		$aa_object[$ai_totrows][3]="<input name=txtdenasig".$ai_totrows."  type=text   id=txtdenasig".$ai_totrows."  class=sin-borde size=55 >";
		$aa_object[$ai_totrows][4]="<input name=txtcantidad".$ai_totrows." type=text   id=txtcantidad".$ai_totrows." class=sin-borde size=12  style='text-align:right'>";
		$aa_object[$ai_totrows][5]="<a href=javascript:uf_delete_dt_asignaciones(".$ai_totrows.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Eliminar width=15 height=15 border=0></a>";
		return $aa_object; 
	} // end function uf_agregarlineablanca

	function uf_agregarlineablancapersonal($aa_objectpersonal,$ai_totrowspersonal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_objectpersonal  // arreglo de titulos 
		//				   $ai_totrowspersonal // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que agrega una linea en blanco al final del grid
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 04/10/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_objectpersonal[$ai_totrowspersonal][1]="<input name=txtcodper".$ai_totrowspersonal."    type=text   id=txtcodper".$ai_totrowspersonal."    class=sin-borde size=15 >";
		$aa_objectpersonal[$ai_totrowspersonal][2]="<input name=txtnomper".$ai_totrowspersonal."    type=text   id=txtnomper".$ai_totrowspersonal."    class=sin-borde size=40 >";
		$aa_objectpersonal[$ai_totrowspersonal][3]="<input name=txtcedper".$ai_totrowspersonal."    type=text   id=txtcedper".$ai_totrowspersonal."    class=sin-borde size=11 >";
		$aa_objectpersonal[$ai_totrowspersonal][4]="<input name=txtcodcar".$ai_totrowspersonal."    type=text   id=txtcodcar".$ai_totrowspersonal."    class=sin-borde size=30 >";
   		$aa_objectpersonal[$ai_totrowspersonal][5]="<input name=txtcodclavia".$ai_totrowspersonal." type=text   id=txtcodclavia".$ai_totrowspersonal." class=sin-borde size=10 style='text-align:center'>";
		$aa_objectpersonal[$ai_totrowspersonal][6]="<a href=javascript:uf_delete_dt_personal(".$ai_totrowspersonal.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Eliminar width=15 height=15 border=0></a>";
		return $aa_objectpersonal;	
	} // end function uf_agregarlineablancapersonal
   
	function uf_agregarlineablancapresupuesto($aa_object,$ai_totrows)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 
		//				   $ai_totrows // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que agrega una linea en blanco al final del grid
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 04/10/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]="<input name=txtestpre".$ai_totrows."    type=text   id=txtestpre".$ai_totrows."    class=sin-borde size=60 style='text-align:center' readonly>";
		$aa_object[$ai_totrows][2]="<input name=txtestcla".$ai_totrows."    type=text   id=txtestcla".$ai_totrows."    class=sin-borde size=10 style='text-align:center' readonly>";
		$aa_object[$ai_totrows][3]="<input name=txtspgcuenta".$ai_totrows." type=text   id=txtspgcuenta".$ai_totrows." class=sin-borde size=20 style='text-align:left'   readonly>";
		$aa_object[$ai_totrows][4]="<input name=txtmonpre".$ai_totrows."    type=text   id=txtmonpre".$ai_totrows."    class=sin-borde size=20 style='text-align:right'  readonly>";
		return $aa_object;	
	} // end function uf_agregarlineablancapresupuesto

	function uf_agregarlineablancacontable($aa_object,$ai_totrows)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 
		//				   $ai_totrows // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que agrega una linea en blanco al final del grid
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 04/10/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]="<input name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows."    class=sin-borde size=60 style='text-align:center'>";
		$aa_object[$ai_totrows][2]="<input name=txtdebhab".$ai_totrows."   type=text   id=txtspgcuenta".$ai_totrows." class=sin-borde size=30 style='text-align:left'   readonly>";
		$aa_object[$ai_totrows][3]="<input name=txtmoncon".$ai_totrows."   type=text   id=txtmoncon".$ai_totrows."    class=sin-borde size=30 style='text-align:right'>";
		return $aa_object;	
	} // end function uf_agregarlineablancacontable


	function uf_repintarasignaciones($aa_object,$ai_totrows)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_repintarasignaciones
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 
		//				   $ai_totrows // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que se encarga de repintar lo que esta impreso en el grid.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 17/10/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_fun_viaticos=new class_funciones_viaticos();
		for($li_i=1;$li_i<=$ai_totrows;$li_i++)
		{
			$ls_proasi= $io_fun_viaticos->uf_obtenervalor("txtproasig".$li_i,"");
			$ls_codasi= $io_fun_viaticos->uf_obtenervalor("txtcodasig".$li_i,"");
			$ls_denasi= $io_fun_viaticos->uf_obtenervalor("txtdenasig".$li_i,"");
			$li_canasi= $io_fun_viaticos->uf_obtenervalor("txtcantidad".$li_i,"");
			
			$aa_object[$li_i][1]="<input name=txtproasig".$li_i."  type=text   id=txtproasig".$li_i."  class=sin-borde size=16 value='". $ls_proasi ."' style='text-align:center'>";
			$aa_object[$li_i][2]="<input name=txtcodasig".$li_i."  type=text   id=txtcodasig".$li_i."  class=sin-borde size=11 value='". $ls_codasi ."'>";
			$aa_object[$li_i][3]="<input name=txtdenasig".$li_i."  type=text   id=txtdenasig".$li_i."  class=sin-borde size=55 value='". $ls_denasi ."'>";
			$aa_object[$li_i][4]="<input name=txtcantidad".$li_i." type=text   id=txtcantidad".$li_i." class=sin-borde size=12 value='". $li_canasi ."' style='text-align:right'>";
			$aa_object[$li_i][5]="<a href=javascript:uf_delete_dt_asignaciones(".$li_i.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Eliminar width=15 height=15 border=0></a>";
		}
		$arrResultado['aa_object']=$aa_object;
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['valido']=true;
		return $arrResultado;		
	
	} // end function uf_repintarasignaciones
   
	function uf_repintarpersonal($aa_objectpersonal,$ai_totrowspersonal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_repintarpersonal
		//         Access: private
		//      Argumento: $aa_objectpersonal  // arreglo de titulos 
		//				   $ai_totrowspersonal // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que se encarga de repintar lo que esta impreso en el grid de personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 19/10/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_fun_viaticos=new class_funciones_viaticos();
		for($li_i=1;$li_i<=$ai_totrowspersonal;$li_i++)
		{
			$ls_codper=    $io_fun_viaticos->uf_obtenervalor("txtcodper".$li_i,"");
			$ls_nomper=    $io_fun_viaticos->uf_obtenervalor("txtnomper".$li_i,"");
			$ls_codnom=    $io_fun_viaticos->uf_obtenervalor("txtcodnom".$li_i,"");
			$ls_codcar=    $io_fun_viaticos->uf_obtenervalor("txtcodcar".$li_i,"");
			$ls_cedper=    $io_fun_viaticos->uf_obtenervalor("txtcedper".$li_i,"");
			$ls_codclavia= $io_fun_viaticos->uf_obtenervalor("txtcodclavia".$li_i,"");
			$ls_cargo=     $io_fun_viaticos->uf_obtenervalor("txtcargo".$li_i,"");
	
			$aa_objectpersonal[$li_i][1]="<input name=txtcodper".$li_i."    type=text   id=txtcodper".$li_i."    class=sin-borde size=15 value='". $ls_codper ."'>";
			$aa_objectpersonal[$li_i][2]="<input name=txtnomper".$li_i."    type=text   id=txtnomper".$li_i."    class=sin-borde size=40 value='". $ls_nomper ."'>";
			$aa_objectpersonal[$li_i][3]="<input name=txtcedper".$li_i."    type=text   id=txtcedper".$li_i."    class=sin-borde size=11 value='". $ls_cedper ."'>";
			$aa_objectpersonal[$li_i][4]="<input name=txtcodcar".$li_i."    type=text   id=txtcodcar".$li_i."    class=sin-borde size=30 value='". $ls_codcar ."'     readonly>".
										 "<input name=txtcodnom".$li_i."    type=hidden id=txtcodnom".$li_i."    class=sin-borde size=30 value='". $ls_codnom ."'     readonly>".
										 "<input name=txtcargo".$li_i."    type=hidden id=txtcargo".$li_i."    class=sin-borde size=30 value='". $ls_cargo ."'     readonly>";
			$aa_objectpersonal[$li_i][5]="<input name=txtcodclavia".$li_i." type=text   id=txtcodclavia".$li_i." class=sin-borde size=10 value='". $ls_codclavia ."' style='text-align:center'>";
			$aa_objectpersonal[$li_i][6]="<a href=javascript:uf_delete_dt_personal(".$li_i.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Eliminar width=15 height=15 border=0></a>";
		}
		$arrResultado['aa_objectpersonal']=$aa_objectpersonal;
		$arrResultado['ai_totrowspersonal']=$ai_totrowspersonal;
		$arrResultado['valido']=true;
		return $arrResultado;		
	} // end function uf_repintarpersonal

	function uf_scv_select_solicitudviaticos($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_solicitudviaticos
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica la existencia de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 20/10/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codsolvia FROM scv_solicitudviatico".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codsolvia='". $as_codsolvia ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_solicitudviaticos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}  // end function uf_scv_select_solicitudviaticos

	function uf_scv_update_solicitudviatico($as_codemp,$as_codsolvia,$as_codtipdoc,$ai_monsolvia,$as_tipodoc,$as_codcla,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_update_solicitudviatico
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa 
		//                 $as_codsolvia // codigo de solicitud de viaticos
		//                 $ai_monsolvia    // codigo de mision
		//				   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica un maestro de solicitud de viaticos en la tabla scv_solicitudviatico
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 06/11/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 	$lb_valido=true;
		$ls_sql= "UPDATE scv_solicitudviatico ".
				 "   SET monsolvia='". $ai_monsolvia ."',".
				 "       estsolvia='C',".
				 "       codtipdoc='". $as_codtipdoc ."',".
				 "       tipodoc='". $as_tipodoc ."', ".
				 "       codcla='". $as_codcla ."' ".
				 " WHERE codemp='" . $as_codemp ."'".
				 "   AND codsolvia='" . $as_codsolvia ."'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->solicitudviaticos M?TODO->uf_scv_update_solicitudviatico ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Realiz? el calculo de la solicitud de viaticos <b>".$as_codsolvia."</b> con un total de <b>Bs. ".$ai_monsolvia.
							 "</b>, Asociado a la Empresa ".$as_codemp;
			$lb_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
						
		}
	    return $lb_valido;
	} // end  function uf_scv_update_solicitudviatico
	
	function uf_scv_update_solicitudviatico_dol($as_codemp,$as_codsolvia,$as_codtipdoc,$ai_monsolvia,$as_tipodoc,$ai_totsolviadol,$ai_tasacambio,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_update_solicitudviatico_dol
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa 
		//                 $as_codsolvia // codigo de solicitud de viaticos
		//                 $ai_monsolvia    // codigo de mision
		//				   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica un maestro de solicitud de viaticos en la tabla scv_solicitudviatico
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 06/11/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 	$lb_valido=true;
		$ls_sql= "UPDATE scv_solicitudviatico".
				 "   SET monsolvia='". $ai_monsolvia ."',".
				 "       estsolvia='C',".
				 "       codtipdoc='". $as_codtipdoc ."',".
				 "       tipodoc='". $as_tipodoc ."',".
				 "       mondolsol=". $ai_totsolviadol .",".
				 "       tascamsol=". $ai_tasacambio ."".
				 " WHERE codemp='" . $as_codemp ."'".
				 "   AND codsolvia='" . $as_codsolvia ."'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->solicitudviaticos M?TODO->uf_scv_update_solicitudviatico ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Realiz? el calculo de la solicitud de viaticos <b>".$as_codsolvia."</b> con un total de <b>Bs. ".$ai_monsolvia.
							 "</b>, Asociado a la Empresa ".$as_codemp;
			$lb_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
						
		}
	    return $lb_valido;
	} // end  function uf_scv_update_solicitudviatico
	
	function uf_scv_update_misiones($as_codemp,$as_codsolvia,$as_codmis,$ai_tarifa,$ai_aprobado)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_update_solicitudviatico
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa 
		//                 $as_codsolvia // codigo de solicitud de viaticos
		//                 $ai_monsolvia    // codigo de mision
		//				   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica un maestro de solicitud de viaticos en la tabla scv_solicitudviatico
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 06/11/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 	$lb_valido=true;
		$ai_tarifa= str_replace(".","",$ai_tarifa);
		$ai_tarifa= str_replace(",",".",$ai_tarifa);
		$ai_aprobado= str_replace(".","",$ai_aprobado);
		$ai_aprobado= str_replace(",",".",$ai_aprobado);
		$ls_sql= "UPDATE scv_dt_misiones".
				 "   SET montar='". $ai_tarifa ."',".
				 "       monaut='". $ai_aprobado ."'".
				 " WHERE codemp='" . $as_codemp ."'".
				 "   AND codsolvia='" . $as_codsolvia ."'".
				 "   AND codmis='" . $as_codmis ."'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->solicitudviaticos M?TODO->uf_scv_update_misiones ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
						
		}
	    return $lb_valido;
	} // end  function uf_scv_update_misiones

	function uf_scv_update_montopersonal($as_codemp,$as_codsolvia,$as_codper,$ai_montotper,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_update_montopersonal
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa 
		//                 $as_codsolvia // codigo de solicitud de viaticos
		//                 $ai_monsolvia    // codigo de mision
		//				   $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica un maestro de solicitud de viaticos en la tabla scv_solicitudviatico
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 06/11/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 	$lb_valido=true;
		$ls_sql= "UPDATE scv_dt_personal".
				 "   SET monpervia=". $ai_montotper ."".
				 " WHERE codemp='" . $as_codemp ."'".
				 "   AND codsolvia='" . $as_codsolvia ."'".
				 "   AND codper='".$as_codper."'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->solicitudviaticos M?TODO->uf_scv_update_montopersonal ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? el calculo del beneficiario ".$as_codper." relacionado a  la solicitud de viaticos <b>".$as_codsolvia."</b> con un total de <b>Bs. ".$ai_montotper.
							 "</b>, Asociado a la Empresa ".$as_codemp;
			$lb_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
						
		}
	    return $lb_valido;
	} // end  function uf_scv_update_solicitudviatico

	function uf_scv_load_dt_asignacion($as_codemp,$as_codsolvia,$ai_totrows,$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_load_dt_asignacion
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $ai_totrows   // total de lineas del grid
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que carga el grid con las asignaciones de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 07/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql= "SELECT scv_dt_asignaciones.*,".
				 "       (CASE scv_dt_asignaciones.proasi".
				 "        WHEN 'TVS' THEN (SELECT scv_tarifas.dentar".
				 "                           FROM scv_tarifas".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_tarifas.codemp".
				 " 							  AND scv_dt_asignaciones.codasi=scv_tarifas.codtar)".
				 "        WHEN 'TRP' THEN (SELECT scv_transportes.dentra".
				 "                           FROM scv_transportes".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_transportes.codemp".
				 "                            AND scv_dt_asignaciones.codasi=scv_transportes.codtra)".
				 "        WHEN 'TOA' THEN (SELECT scv_otrasasignaciones.denotrasi".
				 "                           FROM scv_otrasasignaciones".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_otrasasignaciones.codemp".
				 "                            AND scv_dt_asignaciones.codasi=scv_otrasasignaciones.codotrasi)".
				 "		  ELSE (SELECT scv_tarifakms.dentar".
				 "                FROM scv_tarifakms".
				 "               WHERE scv_dt_asignaciones.codemp=scv_tarifakms.codemp".
				 "                 AND scv_dt_asignaciones.codasi=scv_tarifakms.codtar) END) AS denasi".
				 "  FROM scv_solicitudviatico,scv_dt_asignaciones".
				 " WHERE scv_solicitudviatico.codemp='".$as_codemp."'".
				 "   AND scv_solicitudviatico.codsolvia='".$as_codsolvia."'".
				 "   AND scv_solicitudviatico.codsolvia=scv_dt_asignaciones.codsolvia";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_load_dt_asignacion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codasi=$row["codasi"];
				$ls_proasi=$row["proasi"];
				$ls_denasi=$row["denasi"];
				$li_canasi=$row["canasi"];
				$li_canasi=number_format($li_canasi,2,',','.');
				$ai_totrows++;
				
				$ao_object[$ai_totrows][1]="<input name=txtproasig".$ai_totrows."  type=text   id=txtproasig".$ai_totrows."  class=sin-borde size=16 value='". $ls_proasi ."' style='text-align:center' readonly>";
				$ao_object[$ai_totrows][2]="<input name=txtcodasig".$ai_totrows."  type=text   id=txtcodasig".$ai_totrows."  class=sin-borde size=11 value='". $ls_codasi ."' readonly>";
				$ao_object[$ai_totrows][3]="<input name=txtdenasig".$ai_totrows."  type=text   id=txtdenasig".$ai_totrows."  class=sin-borde size=55 value='". $ls_denasi ."' readonly>";
				$ao_object[$ai_totrows][4]="<input name=txtcantidad".$ai_totrows." type=text   id=txtcantidad".$ai_totrows." class=sin-borde size=12 value='". $li_canasi ."' style='text-align:right' readonly>";
				
			}
			$lb_valido=true;
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_scv_load_dt_asignacion

	function uf_scv_load_dt_personal($as_codemp,$as_codsolvia,$ai_totrows,$ao_objectpersonal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_load_dt_personal
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $ai_totrows   // total de lineas del grid
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que carga el grid con el personal de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 07/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$lb_existe=$this->uf_scv_select_categoria_personal($as_codemp,$as_codsolvia);
		if($lb_existe)
		{
			$ls_sql="SELECT (CASE sno_nomina.racnom WHEN '1' THEN sno_asignacioncargo.denasicar ELSE sno_cargo.descar END) AS cargo,".
					"       scv_dt_personal.codclavia,sno_personalnomina.codper,".
					"		(SELECT nomper FROM sno_personal".
					"  		  WHERE sno_personal.codper=sno_personalnomina.codper) as nomper,".
					"		(SELECT apeper FROM sno_personal".
					"   	  WHERE sno_personal.codper=sno_personalnomina.codper) as apeper,".
					"		(SELECT cedper FROM sno_personal".
					"		  WHERE sno_personal.codper=sno_personalnomina.codper) as cedper,scv_dt_personal.codnom".
					"  FROM sno_personalnomina, sno_nomina, sno_cargo, sno_asignacioncargo,sno_personal,scv_dt_personal".
					" WHERE scv_dt_personal.codemp='".$as_codemp."'".
					"   AND scv_dt_personal.codsolvia='".$as_codsolvia."'".
					"   AND scv_dt_personal.codemp=sno_personal.codemp".
					"   AND scv_dt_personal.codper=sno_personal.codper".
					"   AND scv_dt_personal.codemp=sno_personalnomina.codemp".
					"   AND scv_dt_personal.codnom=sno_personalnomina.codnom".
					"   AND sno_nomina.espnom='0'".
					"   AND sno_personalnomina.codemp = sno_nomina.codemp".
					"   AND sno_personalnomina.codnom = sno_nomina.codnom".
					"   AND sno_personalnomina.codper = sno_personal.codper".
					"   AND sno_personalnomina.codemp = sno_cargo.codemp".
					"   AND sno_personalnomina.codnom = sno_cargo.codnom".
					"   AND sno_personalnomina.codcar = sno_cargo.codcar".
					"   AND sno_personalnomina.codemp = sno_asignacioncargo.codemp".
					"   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom".
					"   AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar".
					" GROUP BY sno_personalnomina.codper,  sno_personalnomina.codper, sno_nomina.racnom,  ".
					" sno_asignacioncargo.denasicar, sno_cargo.descar,scv_dt_personal.codclavia,scv_dt_personal.codnom".
					" ORDER BY sno_personalnomina.codper,codclavia";
		}
		else
		{
			$ls_sql="SELECT scv_dt_personal.codper,rpc_beneficiario.ced_bene,".
					"       (SELECT nombene ".
					"          FROM rpc_beneficiario".
					"         WHERE scv_dt_personal.codemp=rpc_beneficiario.codemp".
					"           AND scv_dt_personal.codper=rpc_beneficiario.ced_bene) AS nombene,".
					"       (SELECT apebene ".
					"          FROM rpc_beneficiario".
					"         WHERE scv_dt_personal.codemp=rpc_beneficiario.codemp".
					"           AND scv_dt_personal.codper=rpc_beneficiario.ced_bene) AS apebene".
					"  FROM scv_dt_personal,rpc_beneficiario".
					" WHERE scv_dt_personal.codemp='".$as_codemp."'".
					"   AND scv_dt_personal.codsolvia='".$as_codsolvia."'".
					"   AND scv_dt_personal.codper=rpc_beneficiario.ced_bene";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_load_dt_personal ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				if($lb_existe)
				{
					$ls_codper=$row["codper"];
					$ls_cedper=$row["cedper"];
					$ls_codnom=$row["codnom"];
					$ls_nomper=$row["nomper"]." ".$row["apeper"];
					$ls_codcar= $row["cargo"];				
					$ls_codclavia=$row["codclavia"];			
				}
				else
				{
					$ls_codper=$row["codper"];
					$ls_cedper=$row["ced_bene"];

					$ls_nomper=$row["nombene"]." ".$row["apebene"];
					$ls_codcar="";
					$ls_codnom="";				
					$ls_codclavia="";			
				}
				$ai_totrows++;
				
				$ao_objectpersonal[$ai_totrows][1]="<input name=txtcodper".$ai_totrows."    type=text   id=txtcodper".$ai_totrows."    class=sin-borde size=15 value='". $ls_codper ."'     readonly>";
				$ao_objectpersonal[$ai_totrows][2]="<input name=txtnomper".$ai_totrows."    type=text   id=txtnomper".$ai_totrows."    class=sin-borde size=40 value='". $ls_nomper ."'     readonly>";
				$ao_objectpersonal[$ai_totrows][3]="<input name=txtcedper".$ai_totrows."    type=text   id=txtcedper".$ai_totrows."    class=sin-borde size=11 value='". $ls_cedper ."'     readonly>";
				$ao_objectpersonal[$ai_totrows][4]="<input name=txtcodcar".$ai_totrows."    type=text   id=txtcodcar".$ai_totrows."    class=sin-borde size=30 value='". $ls_codcar ."'     readonly>";
				$ao_objectpersonal[$ai_totrows][5]="<input name=txtcodclavia".$ai_totrows." type=text   id=txtcodclavia".$ai_totrows." class=sin-borde size=10 value='". $ls_codclavia ."'  readonly style='text-align:center'>";
				$ao_objectpersonal[$ai_totrows][6]="<a href=javascript:uf_delete_dt_personal(".$ai_totrows.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Eliminar width=15 height=15 border=0></a>";
				
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_objectpersonal']=$ao_objectpersonal;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_scv_load_dt_personal

	function uf_scv_load_dt_personal_int($as_codemp,$as_codsolvia,$ai_totrows,$ao_objectpersonal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_load_dt_personal_int
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $ai_totrows   // total de lineas del grid
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que carga el grid con el personal de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 07/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT (CASE sno_nomina.racnom WHEN '1' THEN sno_asignacioncargo.denasicar ELSE sno_cargo.descar END) AS cargo,".
				"       (CASE sno_nomina.racnom WHEN '1' THEN sno_asignacioncargo.codasicar ELSE sno_cargo.codcar END) AS codcar,".
				"       scv_dt_personal.codclavia,sno_personalnomina.codper,".
				"		(SELECT nomper FROM sno_personal".
				"  		  WHERE sno_personal.codper=sno_personalnomina.codper) as nomper,".
				"		(SELECT apeper FROM sno_personal".
				"   	  WHERE sno_personal.codper=sno_personalnomina.codper) as apeper,".
				"		(SELECT cedper FROM sno_personal".
				"		  WHERE sno_personal.codper=sno_personalnomina.codper) as cedper,MAX(scv_dt_personal.codnom) AS codnom".
				"  FROM sno_personalnomina, sno_nomina, sno_cargo, sno_asignacioncargo,sno_personal,scv_dt_personal".
				" WHERE scv_dt_personal.codemp='".$as_codemp."'".
				"   AND scv_dt_personal.codsolvia='".$as_codsolvia."'".
				"   AND scv_dt_personal.codemp=sno_personal.codemp".
				"   AND scv_dt_personal.codper=sno_personal.codper".
				"   AND scv_dt_personal.codemp=sno_personalnomina.codemp".
				"   AND scv_dt_personal.codnom=sno_personalnomina.codnom".
				"   AND sno_nomina.espnom='0'".
				"   AND sno_personalnomina.codemp = sno_nomina.codemp".
				"   AND sno_personalnomina.codnom = sno_nomina.codnom".
				"   AND sno_personalnomina.codper = sno_personal.codper".
				"   AND sno_personalnomina.codemp = sno_cargo.codemp".
				"   AND sno_personalnomina.codnom = sno_cargo.codnom".
				"   AND sno_personalnomina.codcar = sno_cargo.codcar".
				"   AND sno_personalnomina.codemp = sno_asignacioncargo.codemp".
				"   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom".
				"   AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar".
				" GROUP BY sno_personalnomina.codper,  sno_personalnomina.codper, sno_nomina.racnom,  ".
				" sno_asignacioncargo.denasicar,sno_asignacioncargo.codasicar, sno_cargo.descar, sno_cargo.codcar,scv_dt_personal.codclavia".
				" ORDER BY sno_personalnomina.codper,codclavia";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_load_dt_personal_int ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codper=$row["codper"];
				$ls_cedper=$row["cedper"];
				$ls_codnom=$row["codnom"];
				$ls_nomper=$row["nomper"]." ".$row["apeper"];
				$ls_codcar= $row["cargo"];				
				$ls_cargo= $row["codcar"];				
				$ls_codclavia=$row["codclavia"];			

				$ai_totrows++;
				
				$ao_objectpersonal[$ai_totrows][1]="<input name=txtcodper".$ai_totrows."    type=text   id=txtcodper".$ai_totrows."    class=sin-borde size=15 value='". $ls_codper ."'     readonly>";
				$ao_objectpersonal[$ai_totrows][2]="<input name=txtnomper".$ai_totrows."    type=text   id=txtnomper".$ai_totrows."    class=sin-borde size=40 value='". $ls_nomper ."'     readonly>";
				$ao_objectpersonal[$ai_totrows][3]="<input name=txtcedper".$ai_totrows."    type=text   id=txtcedper".$ai_totrows."    class=sin-borde size=11 value='". $ls_cedper ."'     readonly>";
				$ao_objectpersonal[$ai_totrows][4]="<input name=txtcodcar".$ai_totrows."    type=text   id=txtcodcar".$ai_totrows."    class=sin-borde size=30 value='". $ls_codcar ."'     readonly>".
												   "<input name=txtcodnom".$ai_totrows."    type=hidden id=txtcodnom".$ai_totrows."    class=sin-borde size=30 value='". $ls_codnom ."'     readonly>".
												   "<input name=txtcargo".$ai_totrows."    type=hidden id=txtcargo".$ai_totrows."    class=sin-borde size=30 value='". $ls_cargo ."'     readonly>";
				$ao_objectpersonal[$ai_totrows][5]="<input name=txtcodclavia".$ai_totrows." type=text   id=txtcodclavia".$ai_totrows." class=sin-borde size=10 value='". $ls_codclavia ."'  readonly style='text-align:center'>";
				$ao_objectpersonal[$ai_totrows][6]="<a href=javascript:uf_delete_dt_personal(".$ai_totrows.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Eliminar width=15 height=15 border=0></a>";
				
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_objectpersonal']=$ao_objectpersonal;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}  // end function uf_scv_load_dt_personal

	function uf_scv_select_categoriaviaticos($as_codemp,$as_codtar,$as_codcatper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_solicitudviaticos
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de tarifa
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codcat".
				"  FROM scv_tarifas".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codtar='". $as_codtar ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_solicitudviaticos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codcat=$row["codcat"];
				if($ls_codcat==$as_codcatper)
				{
					$lb_valido=true;
				}
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}  // end function uf_scv_select_solicitudviaticos

	function uf_scv_select_tarifasviaticos($as_codemp,$as_codtar,$as_codcatper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_solicitudviaticos
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codcat".
				"  FROM scv_tarifas".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codtar='". $as_codtar ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_solicitudviaticos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codcat=$row["codcat"];
				if($ls_codcat==$as_codcatper)
				{
					$lb_valido=true;
				}
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}  // end function uf_scv_select_solicitudviaticos

	function uf_scv_load_tarifasviaticos($as_codemp,$as_proasi,$as_codasi,$ai_canasi,$ai_monasi,$as_codsolvia,$as_tascam1,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_load_tarifasviaticos
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_proasi    // procedencia de la asignacion
		//  			   $as_codasi    // codigo de la asignacion
		//  			   $ai_canasi    // cantidad de asignaciones
		//  			   $ai_monasi    // monto por asignaciones
		//  			   $as_codsolvia // codigo de la solicitud de viaticos
		//  			   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que obtiene los montos de las tarifas de los viaticos incluidos en una solicitud
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_proasi=trim($as_proasi);
		switch ($as_proasi)
		{
			case "TVS":
				$arrResultado=$this->uf_scv_select_tarifas($as_codemp,$as_codasi,$ls_montar);
				$ls_montar=$arrResultado['as_montar'];
				$lb_valido=$arrResultado['lb_valido'];
			break;
			case "TRP":
				$arrResultado=$this->uf_scv_select_tarifastransporte($as_codemp,$as_codasi,$ls_montar);
				$ls_montar=$arrResultado['as_montar'];
				$lb_valido=$arrResultado['lb_valido'];
			break;
			case "TDS":
				$arrResultado=$this->uf_scv_select_tarifasdistancias($as_codemp,$as_codasi,$ls_montar);
				$ls_montar=$arrResultado['as_montar'];
				$lb_valido=$arrResultado['lb_valido'];
			break;
			case "TOA":
				$arrResultado=$this->uf_scv_select_otrasasignaciones($as_codemp,$as_codasi,$ls_montar);
				$ls_montar=$arrResultado['as_montar'];
				$lb_valido=$arrResultado['lb_valido'];
				$ls_codmon=$arrResultado['as_codmon'];
				if($ls_codmon!="001")
				{
					$ls_montar=($ls_montar*$as_tascam1);
				}
			break;
		}
		if($lb_valido)
		{
			$ai_monasi=($ls_montar*$ai_canasi);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Calcul? el monto de la asignacion <b>".$as_codasi."</b> de procedencia  <b>".$as_proasi.
							 "</b> perteneciente a la Solicitud de Viaticos <b>".$as_codsolvia."</b> Asociado a la Empresa ".$as_codemp;
			$lb_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		$arrResultado['ai_monasi']=$ai_monasi;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_scv_load_tarifasviaticos

	function uf_scv_select_tarifas($as_codemp,$as_codtar,$as_montar)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_tarifas
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_montar   // monto de la tarifa
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica busca el monto total de la tarifa de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codcat,monbol,mondol,monpas,monhos,monali,monmov,nacext,codpai".
				"  FROM scv_tarifas".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codtar='". $as_codtar ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_tarifas ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_monbol=$row["monbol"];
				$li_mondol=$row["mondol"];
				$li_monpas=$row["monpas"];
				$li_monhos=$row["monhos"];
				$li_monali=$row["monali"];
				$li_monmov=$row["monmov"];
				$ls_nacext=$row["nacext"];
				if($ls_nacext!=1)
				{
					$as_montar=($li_monbol+$li_mondol+$li_monpas+$li_monali+$li_monhos+$li_monmov);
				}
				else
				{
					$ls_codpai=$row["codpai"];
					$li_tascam=$this->uf_scv_select_tarifaextranjero($as_codemp,$ls_codpai);
					$as_montar=($li_mondol*$li_tascam);
				}
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_montar']=$as_montar;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_scv_select_tarifas


	function uf_scv_select_tarifaextranjero($as_codemp,$as_codpai)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_tarifaextranjero
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_montar   // monto de la tarifa
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica busca el monto de la tarifa por distancias
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT sigesp_dt_moneda.tascam1".
				"  FROM sigesp_moneda,sigesp_dt_moneda".
				" WHERE sigesp_moneda.codpai='".$as_codpai."'".
				"   AND sigesp_moneda.codmon=sigesp_dt_moneda.codmon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{print $this->io_sql->message;
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_tarifaextranjero ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_tascam1=$row["tascam1"];
				$lb_valido=true;
			}
			else
			{
				$this->io_msg->message("Debe Configurar la tasa de cambio.");
			}
			$this->io_sql->free_result($rs_data);
		}
		return $as_tascam1;
	}  // end function uf_scv_select_tarifaextranjero


	function uf_scv_select_tarifasdistancias($as_codemp,$as_codtar,$as_montar)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_tarifas
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_montar   // monto de la tarifa
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica busca el monto de la tarifa por distancias
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT montar".
				"  FROM scv_tarifakms".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codtar='". $as_codtar ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_tarifas ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_montar=$row["montar"];
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_montar']=$as_montar;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}  // end function uf_scv_select_tarifas

	function uf_scv_select_tarifastransporte($as_codemp,$as_codtra,$as_montar)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_tarifastransporte
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtra    // codigo de transporte
		//  			   $as_montar   // monto de la tarifa
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica busca el monto de la tarifa de transporte
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT tartra".
				"  FROM scv_transportes".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codtra='". $as_codtra ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_tarifastransporte ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_montar=$row["tartra"];
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_montar']=$as_montar;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}  // end function uf_scv_select_tarifastransporte

	function uf_scv_select_otrasasignaciones($as_codemp,$as_codotrasi,$as_montar)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_otrasasignaciones
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codotrasi // codigo de otras asignaciones
		//  			   $as_montar    // monto de la tarifa
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica busca el monto de otras asignaciones de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 17/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT tarotrasi,codmon".
				"  FROM scv_otrasasignaciones".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codotrasi='". $as_codotrasi ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_otrasasignaciones ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_montar=$row["tarotrasi"];
				$as_codmon=$row["codmon"];
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_montar']=$as_montar;
		$arrResultado['lb_valido']=$lb_valido;
		$arrResultado['as_codmon']=$as_codmon;
		return $arrResultado;
	}  // end function uf_scv_select_otrasasignaciones

	function uf_scv_load_config($as_codemp,$as_codsis,$as_seccion,$as_entry,$as_spgcuenta) 
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_load_config
		//	          Access:  public
		//	       Arguments:  $as_codemp    // c?digo de la Empresa.
		//        			   $as_codsis    //  c?digo de sistema
		//        			   $as_seccion   //  tipo de dato
		//        			   $as_entry     // 
		//        			   $as_spgcuenta // cuenta presupuestaria
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de cargar la cuenta asociada a los viaticos
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  14/11/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$lb_valido=false;
		$ls_sql=" SELECT value".
				"   FROM sigesp_config".
				"  WHERE codemp='".$as_codemp."'".
				"    AND codsis='".$as_codsis."'".
				"    AND seccion='".$as_seccion."'".
				"    AND entry='".$as_entry."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->sigesp_scv_c_solicitudviaticos METODO->uf_scv_load_config ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_spgcuenta=$row["value"];
				$lb_valido=true;
			}
		}
		$arrResultado['as_spgcuenta']=$as_spgcuenta;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	} // end function uf_scv_load_config

	function uf_scv_load_estructuraunidad($as_codemp,$as_codsolvia,$as_coduniadm,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
										  $as_codestpro5,$as_estcla) 
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_load_estructuraunidad
		//	          Access:  public
		//	       Arguments:  $as_codemp     //  codigo de la Empresa.
		//        			   $as_coduniadm  //  codigo de unidad ejecutora
		//                     $as_codsolvia  //  codigo de la solicutd de viaticos
		//        			   $as_codestpro1 //  codigo de estructura programatica nivel 1
		//        			   $as_codestpro2 //  codigo de estructura programatica nivel 2
		//        			   $as_codestpro3 //  codigo de estructura programatica nivel 3
		//        			   $as_codestpro4 //  codigo de estructura programatica nivel 4
		//        			   $as_codestpro5 //  codigo de estructura programatica nivel 5
		//                     $as_estcla     //  estatus de la estructura programatica
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de cargar la estructura presupuestaria de una unidad ejecutora
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  14/11/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$lb_valido=false;
		$ls_sql=" SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla".
				"   FROM  scv_solicitudviatico ".
				"  WHERE codemp='".$as_codemp."'".
				"    AND codsolvia='".$as_codsolvia."'".
				"    AND coduniadm='".$as_coduniadm."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->sigesp_scv_c_solicitudviaticos METODO->uf_scv_load_estructuraunidad ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
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
				$as_estcla=$row["estcla"];
				$lb_valido=true;
			}
		}
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['as_estcla']=$as_estcla;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	} // end function uf_scv_load_estructuraunidad

	function uf_scv_load_unidad($as_codemp,$as_codsolvia) 
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_load_estructuraunidad
		//	          Access:  public
		//	       Arguments:  $as_codemp     //  codigo de la Empresa.
		//        			   $as_coduniadm  //  codigo de unidad ejecutora
		//                     $as_codsolvia  //  codigo de la solicutd de viaticos
		//        			   $as_codestpro1 //  codigo de estructura programatica nivel 1
		//        			   $as_codestpro2 //  codigo de estructura programatica nivel 2
		//        			   $as_codestpro3 //  codigo de estructura programatica nivel 3
		//        			   $as_codestpro4 //  codigo de estructura programatica nivel 4
		//        			   $as_codestpro5 //  codigo de estructura programatica nivel 5
		//                     $as_estcla     //  estatus de la estructura programatica
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de cargar la estructura presupuestaria de una unidad ejecutora
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  14/11/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$lb_valido=false;
		$ls_coduniadm="";
		$ls_sql=" SELECT coduniadm".
				"   FROM scv_solicitudviatico ".
				"  WHERE codemp='".$as_codemp."'".
				"    AND codsolvia='".$as_codsolvia."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->sigesp_scv_c_solicitudviaticos METODO->uf_scv_load_estructuraunidad ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_coduniadm=$row["coduniadm"];
			}
		}
		return $ls_coduniadm;
	} // end function uf_scv_load_estructuraunidad

	function uf_scv_select_cuentaspg($as_codemp,$as_spgcta,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
									 $as_codestpro5,$as_estcla) 
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_load_estructuraunidad
		//	          Access:  public
		//	       Arguments:  $as_codemp     //  codigo de la Empresa.
		//        			   $as_spgcta     //  cuenta presupuestaria de gasto
		//        			   $as_codestpro1 //  codigo de estructura programatica nivel 1
		//        			   $as_codestpro2 //  codigo de estructura programatica nivel 2
		//        			   $as_codestpro3 //  codigo de estructura programatica nivel 3
		//        			   $as_codestpro4 //  codigo de estructura programatica nivel 4
		//        			   $as_codestpro5 //  codigo de estructura programatica nivel 5
		//                     $as_estcla     //  estatus de la estructura programatica
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de verificar la existencia de una cuenta presupuestaria en una estructura 
		//                     programatica
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  14/11/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$lb_valido=false;
		$ls_sql=" SELECT spg_cuenta".
				"   FROM spg_cuentas".
				"  WHERE codemp='".$as_codemp."'".
				"    AND spg_cuenta='".$as_spgcta."'".
				"    AND codestpro1='".$as_codestpro1."'".
				"    AND codestpro2='".$as_codestpro2."'".
				"    AND codestpro3='".$as_codestpro3."'".
				"    AND codestpro4='".$as_codestpro4."'".
				"    AND codestpro5='".$as_codestpro5."'".
				"    AND estcla='".$as_estcla."' " ;
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->sigesp_scv_c_solicitudviaticos METODO->uf_scv_load_estructuraunidad ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
			}
		}
		return $lb_valido;
	} // end function uf_scv_load_estructuraunidad
	
	function uf_scv_select_disponibilidad($as_codemp,$as_spgcuenta,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
										  $as_codestpro5,$as_estcla,$as_sccuenta,$li_disponibilidad,$ai_totsolvia) 
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_load_estructuraunidad
		//	          Access:  public
		//	       Arguments:  $as_codemp     //  codigo de la Empresa.
		//        			   $as_spgcuenta  //  cuenta presupuestaria
		//        			   $as_codestpro1 //  codigo de estructura programatica nivel 1
		//        			   $as_codestpro2 //  codigo de estructura programatica nivel 2
		//        			   $as_codestpro3 //  codigo de estructura programatica nivel 3
		//        			   $as_codestpro4 //  codigo de estructura programatica nivel 4
		//        			   $as_codestpro5 //  codigo de estructura programatica nivel 5
		//                     $as_estcla     //  estatus de la estructura programatica
		//        			   $as_sccuenta   //  cuenta contable asociada
		//        			   $ai_disponible //  disponibilidad en la cuenta
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de cargar la cuenta contable y calcula la disponibilidad
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  16/11/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$lb_valido=false;
		require_once("../shared/class_folder/class_sigesp_int.php");
		require_once("../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../shared/class_folder/class_sigesp_int_spg.php");
		require_once("class_folder/class_funciones_viaticos.php");
		$io_fun_viaticos=new class_funciones_viaticos();
		$io_intspg=new class_sigesp_int_spg();		
		$estprog[0]=$as_codestpro1;
		$estprog[1]=$as_codestpro2;
		$estprog[2]=$as_codestpro3;
		$estprog[3]=$as_codestpro4;
		$estprog[4]=$as_codestpro5;
		$estprog[5]=$as_estcla;
		$ls_codestpro=$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5;
		$ls_programatica="";
		$ls_programatica=$io_fun_viaticos->uf_formatoprogramatica($ls_codestpro,$ls_programatica);
		$arrResultado=$io_intspg->uf_spg_saldo_select($as_codemp, $estprog, $as_spgcuenta, $ls_status, $adec_asignado, 
												      $adec_aumento,$adec_disminucion,$adec_precomprometido,
												      $adec_comprometido,$adec_causado,$adec_pagado);
		$ls_status = $arrResultado['as_status'];
		$adec_asignado = $arrResultado['adec_asignado'];
		$adec_aumento = $arrResultado['adec_aumento'];
		$adec_disminucion = $arrResultado['adec_disminucion'];
		$adec_precomprometido = $arrResultado['adec_precomprometido'];
		$adec_comprometido = $arrResultado['adec_comprometido'];
		$adec_causado = $arrResultado['adec_causado'];
		$adec_pagado = $arrResultado['adec_pagado'];
		$lb_valido = $arrResultado['lb_valido'];
		$li_disponibilidad=($adec_asignado-($adec_comprometido+$adec_precomprometido)+$adec_aumento-$adec_disminucion);
		if($li_disponibilidad<$ai_totsolvia)
		{
			$this->io_msg->message( '            NO EXISTE DISPONIBILIDAD a la fecha '.$_SESSION["fechacomprobante"].' \n' .
									'      Estructura : '.$ls_programatica.'\n'.
									'          Cuenta : '.$as_spgcuenta .'\n'.
									'        Asignado : '.number_format($adec_asignado,2,",",".").'\n'.
									'Pre-Comprometido : '.number_format($adec_precomprometido,2,",",".").'\n'.
									'    Comprometido : '.number_format($adec_comprometido,2,",",".").'\n'.
									'         Causado : '.number_format($adec_causado,2,",","."). '\n'.
									'         Aumento : '.number_format($adec_aumento,2,",",".").'\n'.
									'     Disminuci?n : '.number_format($adec_disminucion,2,",",".").'\n'.
									'      Disponible : '.number_format($li_disponibilidad,2,",","."));

		}
		$ls_sql="SELECT sc_cuenta".
				"  FROM spg_cuentas ".
				" WHERE codemp = '".$as_codemp."'".
				"   AND spg_cuenta= '".$as_spgcuenta."'".
				"   AND codestpro1= '".$as_codestpro1."'".
				"   AND codestpro2= '".$as_codestpro2."'".
				"   AND codestpro3= '".$as_codestpro3."'".
				"   AND codestpro4= '".$as_codestpro4."'".
				"   AND codestpro5= '".$as_codestpro5."'".
				"   AND estcla= '".$as_estcla."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->sigesp_scv_c_solicitudviaticos METODO->uf_scv_load_estructuraunidad ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$lb_valido=true;
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_sccuenta=   $row["sc_cuenta"];
			}
			else
			{
				$this->io_msg->message('No existe cuenta contable asociada a la cuenta: '.$as_spgcuenta);
			}
		}
		$arrResultado['as_sccuenta']=$as_sccuenta;
		$arrResultado['li_disponibilidad']=$li_disponibilidad;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end function uf_scv_load_estructuraunidad

	function uf_scv_procesar_asientos($as_codemp,$as_coduniadm,$as_codsis,$as_seccion,$as_entry,$as_spgcuenta,$as_estpre,
									  $as_sccuenta,$ai_disponible,$as_codestpro1,$as_codestpro2,$as_codestpro3,
									  $as_codestpro4,$as_codestpro5,$as_estcla,$as_codsolvia,$ai_totsolvia) 
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_load_estructuraunidad
		//	          Access:  public
		//	       Arguments:  $as_codemp     // codigo de empresa.
		//        			   $as_coduniadm  //  c?digo de unidad ejecutora
		//        			   $as_codsis     //  c?digo de sistema
		//        			   $as_seccion    //  tipo de dato
		//        			   $as_entry      // 
		//        			   $as_spgcuenta  // cuenta presupuestaria
		//        			   $as_estpre     // estructura presupuestaria
		//        			   $as_sccuenta   // cuenta contable de gasto
		//        			   $ai_disponible // disponibilidad de cuenta
		//        			   $as_codestpro1 // codigo de estructura programatica nivel 1
		//        			   $as_codestpro2 // codigo de estructura programatica nivel 2
		//        			   $as_codestpro3 // codigo de estructura programatica nivel 3
		//        			   $as_codestpro4 // codigo de estructura programatica nivel 4
		//        			   $as_codestpro5 // codigo de estructura programatica nivel 5
		//                     $as_estcla     // estatus de la estructura programatica
		//                     $as_codsolvia  // codigo de la solicitud de viaticos
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de verificar la existencia de una cuenta presupuestaria en una estructura 
		//                     programatica
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  14/11/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$lb_valido=false;
		if(trim($as_coduniadm)=="")
			$as_coduniadm=$this->uf_scv_load_unidad($as_codemp,$as_codsolvia);
			
		$arrResultado=$this->uf_scv_load_config($as_codemp,$as_codsis,$as_seccion,$as_entry,$as_spgcuenta);
		$as_spgcuenta=$arrResultado['as_spgcuenta'];
		$lb_valido=$arrResultado['lb_valido'];
		if($lb_valido)
		{
			$arrResultado=$this->uf_scv_load_estructuraunidad($as_codemp,$as_codsolvia,$as_coduniadm,$as_codestpro1,$as_codestpro2,$as_codestpro3,
														      $as_codestpro4,$as_codestpro5,$as_estcla);
			$as_codestpro1=$arrResultado['as_codestpro1'];
			$as_codestpro2=$arrResultado['as_codestpro2'];
			$as_codestpro3=$arrResultado['as_codestpro3'];
			$as_codestpro4=$arrResultado['as_codestpro4'];
			$as_codestpro5=$arrResultado['as_codestpro5'];
			$as_estcla=$arrResultado['as_estcla'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				$as_estpre=$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5.$as_estcla;
				$arrResultado=$this->uf_scv_select_disponibilidad($as_codemp,$as_spgcuenta,$as_codestpro1,$as_codestpro2,
															   $as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
															   $as_sccuenta, $ai_disponible,$ai_totsolvia);
				$as_sccuenta=$arrResultado['as_sccuenta'];
				$ai_disponible=$arrResultado['li_disponibilidad'];
				$lb_valido=$arrResultado['lb_valido'];
			}
		}
		$arrResultado['as_spgcuenta']=$as_spgcuenta;
		$arrResultado['as_estpre']=$as_estpre;
		$arrResultado['as_sccuenta']=$as_sccuenta;
		$arrResultado['ai_disponible']=$ai_disponible;
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['as_estcla']=$as_estcla;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end function uf_scv_load_estructuraunidad
	
	function uf_scv_load_presupuesto($as_estpreaux,$as_spgcuenta,$ai_totsolvia,$as_estpre,$as_estcla,$aa_object,$ai_totrows,$as_programatica)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_load_presupuesto
		//         Access: private
		//      Argumento: $as_estpre    // estructura presupuestaria de gasto
		//				   $as_spgcuenta // cuenta presupuestaria de viaticos
		//				   $ai_totsolvia // monto total del viatico
		//				   $aa_object    // arreglo de titulos 
		//				   $ai_totrows   // ultima fila pintada en el grid
		//	      Returns: $lb_valido
		//    Description: Funcion que carga un asiento presupuestario 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 16/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_totsolvia= number_format($ai_totsolvia,2,',','.');
		if($as_estcla=="A")
		{
			$as_estcla="ACCION";
		}
		else
		{
			$as_estcla="PROYECTO";
		}
		$aa_object[$ai_totrows][1]="<input name=txtestpreaux".$ai_totrows." type=text   id=txtestpreaux".$ai_totrows." class=sin-borde size=60 value='". $as_programatica ."' style='text-align:center' readonly>".
								   "<input name=txtestpre".$ai_totrows."    type=hidden id=txtestpre".$ai_totrows."    class=sin-borde size=60 value='". $as_estpre ."'    style='text-align:center' readonly>";
		$aa_object[$ai_totrows][2]="<input name=txtestclaaux".$ai_totrows." type=text   id=txtestclaaux".$ai_totrows." class=sin-borde size=10 value='".$as_estcla."' style='text-align:center' readonly>";
		$aa_object[$ai_totrows][3]="<input name=txtspgcuenta".$ai_totrows." type=text   id=txtspgcuenta".$ai_totrows." class=sin-borde size=20 value='". $as_spgcuenta ."' style='text-align:left'   readonly>";
		$aa_object[$ai_totrows][4]="<input name=txtmonpre".$ai_totrows."    type=text   id=txtmonpre".$ai_totrows."    class=sin-borde size=20 value='". $li_totsolvia ."' style='text-align:right'  readonly>";
		$ai_totrows=$ai_totrows+1;
		$aa_object=$this->uf_agregarlineablancapresupuesto($aa_object,$ai_totrows);
		$arrResultado['aa_object']=$aa_object;
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['lb_valido']=true;
		return $arrResultado;		
	} // end function uf_scv_load_presupuesto

	function uf_scv_load_contable($as_sccuenta,$ai_tothaber,$as_scben,$ai_totsolvia,$as_sccuentadis,$ai_totdisvia,$aa_object,$ai_totrows)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 
		//				   $ai_totrows // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que agrega una linea en blanco al final del grid
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 04/10/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ai_totrows=1;
		if(($ai_totsolvia>0)&&($ai_totdisvia>0)&&($as_sccuenta==$as_sccuentadis))
		{
			$li_total= number_format($ai_totsolvia+$ai_totdisvia,2,',','.');
			$aa_object[$ai_totrows][1]="<input name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows." class=sin-borde size=60 value='". $as_sccuenta ."'  style='text-align:center' readonly>";
			$aa_object[$ai_totrows][2]="<input name=txtdebhab".$ai_totrows."   type=text   id=txtdebhab".$ai_totrows."   class=sin-borde size=20 value='DEBE'    style='text-align:left'   readonly>";
			$aa_object[$ai_totrows][3]="<input name=txtmoncon".$ai_totrows."   type=text   id=txtmoncon".$ai_totrows."   class=sin-borde size=30 value='". $li_total ."' style='text-align:right'  readonly>";
			$ai_totrows++;
		}
		else
		{
			if($ai_totsolvia>0)
			{
				$li_totsolvia= number_format($ai_totsolvia,2,',','.');
				$aa_object[$ai_totrows][1]="<input name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows." class=sin-borde size=60 value='". $as_sccuenta ."'  style='text-align:center' readonly>";
				$aa_object[$ai_totrows][2]="<input name=txtdebhab".$ai_totrows."   type=text   id=txtdebhab".$ai_totrows."   class=sin-borde size=20 value='DEBE'    style='text-align:left'   readonly>";
				$aa_object[$ai_totrows][3]="<input name=txtmoncon".$ai_totrows."   type=text   id=txtmoncon".$ai_totrows."   class=sin-borde size=30 value='". $li_totsolvia ."' style='text-align:right'  readonly>";
				$ai_totrows++;
			}
			if($ai_totdisvia>0)
			{
				$ai_totdisvia= number_format($ai_totdisvia,2,',','.');
				$aa_object[$ai_totrows][1]="<input name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows." class=sin-borde size=60 value='". $as_sccuentadis ."'  style='text-align:center' readonly>";
				$aa_object[$ai_totrows][2]="<input name=txtdebhab".$ai_totrows."   type=text   id=txtdebhab".$ai_totrows."   class=sin-borde size=20 value='DEBE'    style='text-align:left'   readonly>";
				$aa_object[$ai_totrows][3]="<input name=txtmoncon".$ai_totrows."   type=text   id=txtmoncon".$ai_totrows."   class=sin-borde size=30 value='". $ai_totdisvia ."' style='text-align:right'  readonly>";
				$ai_totrows++;
			}
		}
		$ai_tothaber= number_format($ai_tothaber,2,',','.');
		$aa_object[$ai_totrows][1]="<input name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows." class=sin-borde size=60 value='". $as_scben ."'  style='text-align:center' readonly>";
		$aa_object[$ai_totrows][2]="<input name=txtdebhab".$ai_totrows."   type=text   id=txtdebhab".$ai_totrows."   class=sin-borde size=20 value='HABER'    style='text-align:left'   readonly>";
		$aa_object[$ai_totrows][3]="<input name=txtmoncon".$ai_totrows."   type=text   id=txtmoncon".$ai_totrows."   class=sin-borde size=30 value='". $ai_tothaber ."' style='text-align:right'  readonly>";
		$ai_totrows++;
		
/*		for($ai_totrows=1;$ai_totrows<3;$ai_totrows++)
		{
			if($ai_totrows==1)
			{
				$ls_debhab="DEBE";
				$ls_sccuenta=$as_sccuenta;
			}
			else
			{
				$ls_debhab="HABER";
				$ls_sccuenta=$as_scben;
			}
			$aa_object[$ai_totrows][1]="<input name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows."  class=sin-borde size=60 value='". $ls_sccuenta ."'  style='text-align:center' readonly>";
			$aa_object[$ai_totrows][2]="<input name=txtdebhab".$ai_totrows."   type=text   id=txtspgcuenta".$ai_totrows." class=sin-borde size=30 value='". $ls_debhab ."'    style='text-align:left'   readonly>";
			$aa_object[$ai_totrows][3]="<input name=txtmoncon".$ai_totrows."   type=text   id=txtmoncon".$ai_totrows."    class=sin-borde size=30 value='". $li_totsolvia ."' style='text-align:right'  readonly>";
		}*/
		$aa_object=$this->uf_agregarlineablancacontable($aa_object,$ai_totrows);
		$arrResultado['aa_object']=$aa_object;
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['lb_valido']=true;
		return $arrResultado;		
	} // end function uf_scv_load_contable

	function  uf_scv_insert_dt_spg($as_codemp,$as_codsolvia,$as_codcom,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
								   $as_codestpro5,$as_estcla,$as_spgcuenta,$as_operacion,$as_codpro,$as_cedbene,$as_tipodestino,
								   $as_descripcion,$ai_monto,$as_codfuefin,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_insert_dt_spg
		//         Access: public 
		//      Argumento: $as_codemp      // codigo de empresa 
		//                 $as_codsolvia   // codigo de solicitud de viaticos
		//                 $as_codcom      // codigo de comprobante
		//                 $as_codestpro1  // codigo de estructura programatica nivel 1
		//                 $as_codestpro2  // codigo de estructura programatica nivel 2
		//                 $as_codestpro3  // codigo de estructura programatica nivel 3
		//                 $as_codestpro4  // codigo de estructura programatica nivel 4
		//                 $as_codestpro5  // codigo de estructura programatica nivel 5
		//                 $as_estcla      // estatus de la estructura programatica
		//                 $as_spgcuenta   // cuenta de presupuesto de gasto
		//                 $as_operacion   // tipo de operacion
		//                 $as_codpro      // codigo de proveedor
		//                 $as_cedbene     // cedula de beneficiario
		//                 $as_tipodestino // tipo de destino
		//                 $as_descripcion // descripcion del comprobante
		//                 $ai_monto       // monto del comprobante
		//                 $as_codfuefin   // c?digo fuente de financiamiento
		//				   $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el detalle presupuestario de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 21/11/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql= "INSERT INTO scv_dt_spg (codemp,codsolvia,codcom,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,".
				 "                        estcla,spg_cuenta,operacion,cod_pro,ced_bene,tipo_destino,descripcion,monto,estatus, ".
				 "                        codfuefin) ".
				 "     VALUES('".$as_codemp."','".$as_codsolvia."','".$as_codcom."','".$as_codestpro1."','".$as_codestpro2."',".
				 "            '".$as_codestpro3."','".$as_codestpro4."','".$as_codestpro5."','".$as_estcla."','".$as_spgcuenta."','".$as_operacion."',".
				 "            '".$as_codpro."','".$as_cedbene."','".$as_tipodestino."','".$as_descripcion."','".$ai_monto."',0, ".
				 "            '".$as_codfuefin."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_insert_dt_spg ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion="Insert? el comprobante presupuestario <b>".$as_codcom."</b> de la Solicitud de Viaticos <b>".$as_codsolvia.
								"</b> al beneficiario <b>".$as_cedbene."</b> en la cuenta <b>".$as_spgcuenta."</b> Asociado a la Empresa ".$as_codemp;
				$lb_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				
		}
		return $lb_valido;
	} //end function  uf_scv_insert_dt_spg

	function  uf_scv_insert_dt_scg($as_codemp,$as_codsolvia,$as_codcom,$as_sccuenta,$as_debhab,$as_codpro,$as_cedbene,
								   $as_tipodestino,$as_descripcion,$ai_monto,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_insert_dt_scg
		//         Access: public 
		//      Argumento: $as_codemp      // codigo de empresa 
		//                 $as_codsolvia   // codigo de solicitud de viaticos
		//                 $as_codcom      // codigo de comprobante
		//                 $as_sccuenta    // cuenta contable
		//                 $as_debhab      // indica si el asiento va por el debe o por el haber
		//                 $as_codpro      // codigo de proveedor
		//                 $as_cedbene     // cedula de beneficiario
		//                 $as_tipodestino // tipo de destino
		//                 $as_descripcion // descripcion del comprobante
		//                 $ai_monto       // monto del comprobante
		//				   $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el detalle contable de la solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 21/11/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql= "INSERT INTO scv_dt_scg (codemp,codsolvia,codcom,sc_cuenta,debhab,cod_pro,ced_bene,tipo_destino,
													descripcion,monto,estatus) ".
				 "     VALUES('".$as_codemp."','".$as_codsolvia."','".$as_codcom."','".$as_sccuenta."','".$as_debhab."',".
				 "            '".$as_codpro."','".$as_cedbene."','".$as_tipodestino."','".$as_descripcion."','".$ai_monto."',0)";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_insert_dt_scg ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion="Insert? el comprobante contable <b>".$as_codcom."</b> de la Solicitud de Viaticos <b>".$as_codsolvia.
							"</b> al beneficiario <b>".$as_cedbene."</b> Asociado a la Empresa ".$as_codemp;
			$lb_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											   $aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											   $aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			
			
		}
		return $lb_valido;
	} //end function  uf_scv_insert_dt_scg
	
	function uf_scv_procesar_recepcion_documento_viatico($as_codsolvia,$as_comprobante,$as_cedbene,$as_codtipdoc,
														 $as_descripcion,$ad_fecha,$ai_monto,$as_codfuefin,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_procesar_recepcion_documento_viatico
		//		   Access: private
		//	    Arguments: $as_codsolvia    // codigo de solicitud de viaticos
		//                 $as_comprobante  // Codigo de Comprobante
		//				   $as_cedbene 		// cedula de beneficiario
		//				   $as_codtipdoc	// codigo de tipo de documento
		//				   $as_descripcion	// descripcion del documento
		//				   $ad_fecha  		// Fecha de contabilizaci?n
		//				   $ad_fecha  		// Fecha de contabilizaci?n
		//                 $as_codfuefin    // C?digo de la fuente de financiamiento
		//				   $aa_seguridad    // Arreglo de las variables de seguridad
		//	      Returns: $lb_valido True si se genero la recepci?n de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 07/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
        $ls_tipodestino= "B";			
		$ls_codpro= "----------";	
		$ad_fecha= $this->io_funcion->uf_convertirdatetobd($ad_fecha);
		$ls_sql="INSERT INTO cxp_rd (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,dencondoc,fecemidoc, fecregdoc, fecvendoc,".
 		        "                    montotdoc, mondeddoc,moncardoc,tipproben,numref,estprodoc,procede,estlibcom,estaprord,".
				"                    fecaprord,usuaprord,estimpmun,codcla,codfuefin)".
				"     VALUES ('".$this->is_codemp."','".$as_comprobante."','".$as_codtipdoc."','".$as_cedbene."',".
				"             '".$ls_codpro."','".$as_descripcion."','".$ad_fecha."','".$ad_fecha."','".$ad_fecha."',
				"               .$ai_monto.",0,0,'".$ls_tipodestino."','".$as_comprobante."','R','SCVSOV',0,0,'1900-01-01','',0,'--','".$as_codfuefin."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{  
           	print ($this->io_sql->message);
			$this->io_msg->message("CLASE->sigesp_scv_c_calcularviaticos M?TODO->uf_scv_procesar_recepcion_documento_viatico ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Gener? la Recepci?n de Documento Solicitud de Vi?ticos <b>".$as_codsolvia."</b>, ".
							"Comprobante <b>".$as_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											  $aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											  $aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$li_mondeddoc=0;
			$li_moncardoc=0;
			
		}
		return $lb_valido;
	}  // end function uf_scv_procesar_recepcion_documento_viatico

	function uf_insert_recepcion_documento_gasto($as_comprobante,$as_codtipdoc,$as_cedbene,$as_codpro,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento_gasto
		//		   Access: private
		//	    Arguments: $as_comprobante // C?digo de Comprobante
		//				   $as_codtipdoc   // Tipo de Documento
		//				   $as_cedbene     // C?dula del Beneficiario
		//				   $as_codpro      // C?digo del Proveedor
		//				   $ai_monto       // monto del comprobante
		//	      Returns: $lb_valido True si se inserto los detalles presupuestario en la recepci?n de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 07/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SCVSOV";
		$ls_sql="SELECT codemp, codsolvia, codcom, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, ".
		        " spg_cuenta, operacion, cod_pro, ced_bene, tipo_destino, descripcion, monto, estatus,codfuefin ".
				"  FROM scv_dt_spg ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codcom='".$as_comprobante."'";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_msg->message("CLASE->sigesp_scv_c_calcularviaticos M?TODO->uf_insert_recepcion_documento_gasto ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
			{
				$ls_codestpro=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
				$ls_estcla=$row["estcla"];
				$ls_spg_cuenta= $row["spg_cuenta"];
				$ls_documento=  $row["codcom"];								 
				$ls_cedbene=    $row["ced_bene"];								 
				$ls_codpro=     $row["cod_pro"];
				$ls_codfuefin=  $row["codfuefin"];								 
				$ls_documento=$this->io_sigesp_int->uf_fill_comprobante($ls_documento);
				$ls_sql="INSERT INTO cxp_rd_spg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,codestpro,".
						"						 spg_cuenta,monto,estcla,codfuefin)".
						"     VALUES ('".$this->is_codemp."','".$as_comprobante."','".$as_codtipdoc."',".
						"             '".$ls_cedbene."','".$ls_codpro."','".$ls_procede."','".$ls_documento."','".$ls_codestpro."',".
						"             '".$ls_spg_cuenta."',".$ai_monto.",'".$ls_estcla."','".$ls_codfuefin."')";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
           			$this->io_msg->message("CLASE->sigesp_scv_c_calcularviaticos M?TODO->uf_insert_recepcion_documento_gasto ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));			
				   	$lb_valido=false;
				   	break;
				}
				
			} // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_insert_recepcion_documento_gasto

	function uf_insert_recepcion_documento_contable($as_comprobante,$as_codtipdoc,$as_cedbene,$as_codpro,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento_contable
		//		   Access: private
		//	    Arguments: $as_comprobante // C?digo de Comprobante
		//				   $as_codtipdoc   // Tipo de Documento
		//				   $as_cedbene     // C?dula del Beneficiario
		//				   $as_codpro      // C?digo del Proveedor
		//				   $ai_monto       // monto del comprobante
		//	      Returns: $lb_valido True si se inserto los detalles contables en la recepci?n de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 07/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SCVSOV";
		$ls_sql="SELECT codemp, codsolvia, codcom, sc_cuenta, debhab, cod_pro, ced_bene, tipo_destino, descripcion, monto, estatus".
				"  FROM scv_dt_scg ".
				" WHERE codemp='".$this->is_codemp."' ".
				"   AND codcom='".$as_comprobante."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_msg->message("CLASE->sigesp_scv_c_calcularviaticos M?TODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
			{
				$ls_sccuenta= $row["sc_cuenta"];
				$ls_debhab=     $row["debhab"];				
				$ls_documento=  $row["codcom"];								 
				$ls_cedbene=    $row["ced_bene"];								 
				$ls_codpro=     $row["cod_pro"];								 
				$ls_documento= $this->io_sigesp_int->uf_fill_comprobante($ls_documento);
				$ls_sql="INSERT INTO cxp_rd_scg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,".
						"						 sc_cuenta,monto)".
						"     VALUES ('".$this->is_codemp."','".$as_comprobante."','".$as_codtipdoc."','".$ls_cedbene."',".
						"             '".$ls_codpro."','".$ls_procede."','".$ls_documento."','".$ls_debhab."',".
						"             '".$ls_sccuenta."',".$ai_monto.")";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
		           	$this->io_msg->message("CLASE->sigesp_scv_c_calcularviaticos M?TODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));			
				    $lb_valido=false;
				    break;
				}
				
			} // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_insert_recepcion_documento_contable

	function uf_scv_select_categoria_personal($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_categoria_personal
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica la existencia de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codclavia".
		        "  FROM scv_dt_personal".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codsolvia='". $as_codsolvia ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_categoria_personal ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codclavia=$row["codclavia"];
				if($ls_codclavia!="")
				{$lb_valido=true;}
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}  // end function uf_scv_select_categoria_personal
	
	
	
	function uf_scv_load_maxinter($as_codemp,$as_codsis,$as_seccion,$as_entry) 
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_load_maxinter
		//	          Access:  public
		//	       Arguments:  $as_codemp    // c?digo de la Empresa.
		//        			   $as_codmis    //  c?digo de la Misi?n.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de verificar si existe o no la configuracion de viaticos
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  13/11/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$as_valor="";
		if($as_entry=="MAXINTER")
		{
			$ls_sql=" SELECT value".
					"   FROM sigesp_config".
					"  WHERE codemp='".$as_codemp."'".
					"    AND codsis='".$as_codsis."'".
					"    AND seccion='".$as_seccion."'".
					"    AND entry='".$as_entry."'";
		}

		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->sigesp_scv_c_config METODO->uf_scv_load_config ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_valor=$row["value"];
			}
		}
		return $as_valor;
	} // fin de la function uf_scv_load_config
	function uf_scv_select_tarifacargo($as_codemp,$as_codcar,$as_codnom,$as_tipvia='-')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_solicitudviaticos
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_montarcar="";
		$ls_sql="SELECT montarcar".
				"  FROM scv_tarifacargos,scv_dt_tarifacargos".
				" WHERE scv_tarifacargos.codemp='". $as_codemp ."'".
				"   AND scv_tarifacargos.tipvia='". $as_tipvia ."'".
				"   AND scv_dt_tarifacargos.codcar='". $as_codcar ."'".
				"   AND scv_dt_tarifacargos.codnom= '".$as_codnom."'".
				"   AND scv_tarifacargos.codemp=scv_dt_tarifacargos.codemp".
				"   AND scv_tarifacargos.codtar=scv_dt_tarifacargos.codtar"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_tarifacargo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montarcar=$row["montarcar"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_montarcar;
	}  // end function uf_scv_select_solicitudviaticos

	function uf_scv_select_tasacambio($as_codemp,$as_codcar,$as_codnom,$as_tipvia='-')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_solicitudviaticos
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_tascam="";
		$ls_sql="SELECT sigesp_dt_moneda.codmon,MAX(tascam1) AS tascam".
				"  FROM scv_tarifacargos,scv_dt_tarifacargos,sigesp_dt_moneda".
				" WHERE scv_tarifacargos.codemp='". $as_codemp ."'".
				"   AND scv_tarifacargos.tipvia='". $as_tipvia ."'".
				"   AND scv_dt_tarifacargos.codcar='". $as_codcar ."'".
				"   AND scv_dt_tarifacargos.codnom= '".$as_codnom."'".
				"   AND scv_tarifacargos.codemp=scv_dt_tarifacargos.codemp".
				"   AND scv_tarifacargos.codtar=scv_dt_tarifacargos.codtar".
				"   AND scv_tarifacargos.codmon=sigesp_dt_moneda.codmon".
				" GROUP BY sigesp_dt_moneda.codmon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_tarifacargo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_tascam=$row["tascam"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_tascam;
	}  // end function uf_scv_select_solicitudviaticos

	function uf_scv_select_tasacambio_internacional($as_codemp,$as_codcar,$as_codnom,$as_tipvia='-')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_tasacambio_internacional
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_tascam="";
		if($as_codnom!="")
		{
			$ls_sql="SELECT sigesp_dt_moneda.codmon,MAX(tascam1) AS tascam".
					"  FROM scv_catcargos,scv_dt_catcargos,sigesp_dt_moneda".
					" WHERE scv_catcargos.codemp='". $as_codemp ."'".
					"   AND scv_dt_catcargos.codcar='". $as_codcar ."'".
					"   AND scv_dt_catcargos.codnom= '".$as_codnom."'".
					"   AND scv_catcargos.codemp=scv_dt_catcargos.codemp".
					"   AND scv_catcargos.codcatcar=scv_dt_catcargos.codcatcar".
					"   AND scv_catcargos.codmon=sigesp_dt_moneda.codmon".
					" GROUP BY sigesp_dt_moneda.codmon";
		}
		else
		{
			$ls_sql="SELECT sigesp_dt_moneda.codmon,MAX(tascam1) AS tascam".
					"  FROM scv_catcargos,sigesp_dt_moneda".
					" WHERE scv_catcargos.codemp='". $as_codemp ."'".
					"   AND scv_catcargos.foraneo='1'".
					"   AND scv_catcargos.codmon=sigesp_dt_moneda.codmon".
					" GROUP BY sigesp_dt_moneda.codmon";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_tasacambio_internacional ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_tascam=$row["tascam"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_tascam;
	}  // end function uf_scv_select_tasacambio_internacional

	function uf_scv_select_origensolicitud($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_origensolicitud
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codpai".
				"  FROM scv_solicitudviatico,scv_misiones".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'".
				"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
				"   AND scv_solicitudviatico.codmis=scv_misiones.codmis"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_origensolicitud ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codpai=$row["codpai"];
				if($ls_codpai=='058')
				{
					$lb_valido=true;
				}
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}  // end function uf_scv_select_solicitudviaticos

/*	function uf_scv_select_incrementoorden($as_codemp,$as_codsolvia,$as_codregdes)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_incrementoorden
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_montarcar="";
		$ls_sql="SELECT porinc".
				"  FROM scv_solicitudviatico,scv_dt_incremento".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'".
				"   AND scv_dt_incremento.codregdes='".$as_codregdes."'".
				"   AND scv_solicitudviatico.codemp=scv_dt_incremento.codemp".
				"   AND scv_solicitudviatico.codinc=scv_dt_incremento.codinc"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_incrementoorden ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montarcar=$row["porinc"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_montarcar;
	}  // end function uf_scv_select_solicitudviaticos
*/	
	function uf_scv_select_incrementoorden($as_codemp,$as_codsolvia,$as_codregdes)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_incrementoorden
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data="";
		$ls_sql="SELECT codreg AS codregori,scv_misiones.denmis AS denmisori,".
				"       (SELECT denmis FROM scv_misiones".
				"         WHERE scv_solicitudviatico.codemp=scv_misiones.codemp".
				"           AND scv_solicitudviatico.codmisdes=scv_misiones.codmis) AS denmisdes".
				"  FROM scv_solicitudviatico,scv_misiones,scv_dt_regiones_int".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'".
				"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
				"   AND scv_solicitudviatico.codmis=scv_misiones.codmis".
				"   AND scv_misiones.codemp=scv_dt_regiones_int.codemp".
				"   AND scv_misiones.codpai=scv_dt_regiones_int.codpai"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{print $this->io_sql->message;
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_incrementoorden ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codregori=$row["codregori"];
				$ls_denmisori=$row["denmisori"];
				$ls_denmisdes=$row["denmisdes"];
				$ls_sql="SELECT porinc,deninc".
						"  FROM scv_incremento,scv_dt_incremento".
						" WHERE scv_incremento.codemp='". $as_codemp ."'".
						"   AND scv_incremento.codregori='". $ls_codregori ."'".
						"   AND scv_dt_incremento.codregdes='".$as_codregdes."'".
						"   AND scv_incremento.codemp=scv_dt_incremento.codemp".
						"   AND scv_incremento.codinc=scv_dt_incremento.codinc"; 
				$rs_data2=$this->io_sql->select($ls_sql);
				if($row=$this->io_sql->fetch_row($rs_data2))
				{
					$li_montarcar=$row["porinc"];
					$ls_deninc=$row["deninc"];
					$la_data[1]=$li_montarcar;
					$la_data[2]=$ls_deninc;
					$la_data[3]=$ls_denmisori;
					$la_data[4]=$ls_denmisdes;
				}
				$this->io_sql->free_result($rs_data2);
			}
			$this->io_sql->free_result($rs_data);
		}
		return $la_data;
	}  // end function uf_scv_select_solicitudviaticos
	
	function uf_scv_select_destino($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_destino
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codreg="";
		$ls_sql="SELECT codreg".
				"  FROM scv_solicitudviatico,scv_misiones,scv_dt_regiones_int".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'".
				"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
				"   AND scv_solicitudviatico.codmisdes=scv_misiones.codmis".
				"   AND scv_misiones.codemp=scv_dt_regiones_int.codemp".
				"   AND scv_misiones.codpai=scv_dt_regiones_int.codpai"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_destino ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codreg=$row["codreg"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_codreg;
	}  // end function uf_scv_select_destino
	
	function uf_scv_select_mision_destino($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_destino
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_denmis="";
		$ls_sql="SELECT denmis".
				"  FROM scv_solicitudviatico,scv_misiones".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'".
				"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
				"   AND scv_solicitudviatico.codmisdes=scv_misiones.codmis"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_destino ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_denmis=$row["denmis"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_denmis;
	}  // end function uf_scv_select_destino
	
	function uf_scv_select_cargafamiliar($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_cargafamiliar
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_datos="";
		$ls_sql="SELECT porcar,dencar".
				"  FROM scv_solicitudviatico,scv_cargafamiliar".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'".
				"   AND scv_solicitudviatico.codemp=scv_cargafamiliar.codemp".
				"   AND scv_solicitudviatico.codcar=scv_cargafamiliar.codcar"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_cargafamiliar ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montarcar=$row["porcar"];
				$li_dencar=$row["dencar"];
				$la_datos[1]=$li_montarcar;
				$la_datos[2]=$li_dencar;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $la_datos;
	}  // end function uf_scv_select_solicitudviaticos
	
	function uf_scv_select_cargo($as_codemp,$as_codcar,$as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_cargo
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_dencar="";
		$ls_sql="SELECT descar".
				"  FROM sno_cargo".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codcar='". $as_codcar ."'".
				"   AND codnom='". $as_codnom ."'"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_select_cargo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_dencar=$row["descar"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_dencar;
	}  // end function uf_scv_select_cargo
	
	function uf_agregarlineablancacontable_int($aa_object,$ai_totrows)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablancacontable_int
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 
		//				   $ai_totrows // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que agrega una linea en blanco al final del grid
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 04/10/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]="<input name=txtcedbene".$ai_totrows." type=text   id=txtcedbene".$ai_totrows."    class=sin-borde size=30 style='text-align:center'>";
		$aa_object[$ai_totrows][2]="<input name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows."    class=sin-borde size=20 style='text-align:center'>";
		$aa_object[$ai_totrows][3]="<input name=txtdebhab".$ai_totrows."   type=text   id=txtdebhab".$ai_totrows." class=sin-borde size=30 style='text-align:left'   readonly>";
		$aa_object[$ai_totrows][4]="<input name=txtmoncon".$ai_totrows."   type=text   id=txtmoncon".$ai_totrows."    class=sin-borde size=30 style='text-align:right'>";
		return $aa_object;
	} // end function uf_agregarlineablancacontable

	function uf_scv_load_contable_int($as_cedper,$as_sccuenta,$ai_tothaber,$as_scben,$ai_totsolvia,$as_sccuentadis,$ai_totdisvia,$aa_object,$ai_totrows)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 
		//				   $ai_totrows // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que agrega una linea en blanco al final del grid
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 04/10/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ai_totrows=1;
		$li_totsolvia= number_format($ai_totsolvia,2,',','.');
		$aa_object[$ai_totrows][1]="<input name=txtcedbene".$ai_totrows." type=text   id=txtcedbene".$ai_totrows."  class=sin-borde size=30 value='". $as_cedper ."'  style='text-align:center' readonly>";
		$aa_object[$ai_totrows][2]="<input name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows."  class=sin-borde size=20 value='". $as_sccuenta ."'  style='text-align:center' readonly>";
		$aa_object[$ai_totrows][3]="<input name=txtdebhab".$ai_totrows."   type=text   id=txtdebhab".$ai_totrows." class=sin-borde size=30 value='DEBE'    style='text-align:left'   readonly>";
		$aa_object[$ai_totrows][4]="<input name=txtmoncon".$ai_totrows."   type=text   id=txtmoncon".$ai_totrows."    class=sin-borde size=30 value='". $li_totsolvia ."' style='text-align:right'  readonly>";
		$ai_totrows++;

		$aa_object[$ai_totrows][1]="<input name=txtcedbene".$ai_totrows." type=text   id=txtcedbene".$ai_totrows."  class=sin-borde size=30 value='". $as_cedper ."'  style='text-align:center' readonly>";
		$aa_object[$ai_totrows][2]="<input name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows."  class=sin-borde size=20 value='". $as_scben ."'  style='text-align:center' readonly>";
		$aa_object[$ai_totrows][3]="<input name=txtdebhab".$ai_totrows."   type=text   id=txtdebhab".$ai_totrows." class=sin-borde size=30 value='HABER'    style='text-align:left'   readonly>";
		$aa_object[$ai_totrows][4]="<input name=txtmoncon".$ai_totrows."   type=text   id=txtmoncon".$ai_totrows."    class=sin-borde size=30 value='". $li_totsolvia ."' style='text-align:right'  readonly>";
		$ai_totrows++;
		
		$aa_object=$this->uf_agregarlineablancacontable_int($aa_object,$ai_totrows);
		$arrResultado['aa_object']=$aa_object;
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['lb_valido']=true;
		return $arrResultado;
	} // end function uf_scv_load_contable

	function uf_repintarpersonal_int($aa_objectpersonal,$ai_totrowspersonal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_repintarpersonal
		//         Access: private
		//      Argumento: $aa_objectpersonal  // arreglo de titulos 
		//				   $ai_totrowspersonal // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que se encarga de repintar lo que esta impreso en el grid de personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 19/10/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_fun_viaticos=new class_funciones_viaticos();
		for($li_i=1;$li_i<=$ai_totrowspersonal;$li_i++)
		{
			$ls_codper=    $io_fun_viaticos->uf_obtenervalor("txtcodper".$li_i,"");
			$ls_nomper=    $io_fun_viaticos->uf_obtenervalor("txtnomper".$li_i,"");
			$ls_codcar=    $io_fun_viaticos->uf_obtenervalor("txtcodcar".$li_i,"");
			$ls_cedper=    $io_fun_viaticos->uf_obtenervalor("txtcedper".$li_i,"");
			$ls_codclavia= $io_fun_viaticos->uf_obtenervalor("txtcodclavia".$li_i,"");
			$ls_codnom= $io_fun_viaticos->uf_obtenervalor("txtcodnom".$li_i,"");
			$ls_cargo= $io_fun_viaticos->uf_obtenervalor("txtcargo".$li_i,"");
	
			$aa_objectpersonal[$li_i][1]="<input name=txtcodper".$li_i."    type=text   id=txtcodper".$li_i."    class=sin-borde size=15 value='". $ls_codper ."'>";
			$aa_objectpersonal[$li_i][2]="<input name=txtnomper".$li_i."    type=text   id=txtnomper".$li_i."    class=sin-borde size=40 value='". $ls_nomper ."'>";
			$aa_objectpersonal[$li_i][3]="<input name=txtcedper".$li_i."    type=text   id=txtcedper".$li_i."    class=sin-borde size=11 value='". $ls_cedper ."'>";
			$aa_objectpersonal[$li_i][4]="<input name=txtcodcar".$li_i."    type=text   id=txtcodcar".$li_i."    class=sin-borde size=30 value='". $ls_codcar ."'>".
									     "<input name=txtcodnom".$li_i."    type=hidden id=txtcodnom".$li_i."    class=sin-borde size=30 value='". $ls_codnom ."'>".
									     "<input name=txtcargo".$li_i."     type=hidden id=txtcargo".$li_i."     class=sin-borde size=30 value='". $ls_cargo ."'>";
			$aa_objectpersonal[$li_i][5]="<input name=txtcodclavia".$li_i." type=text   id=txtcodclavia".$li_i." class=sin-borde size=10 value='". $ls_codclavia ."' style='text-align:center'>";
			$aa_objectpersonal[$li_i][6]="<a href=javascript:uf_delete_dt_personal(".$li_i.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Eliminar width=15 height=15 border=0></a>";
		}
		$arrResultado['aa_objectpersonal']=$aa_objectpersonal;
		$arrResultado['ai_totrowspersonal']=$ai_totrowspersonal;
		$arrResultado['valido']=true;
		return $arrResultado;		
	} // end function uf_repintarpersonal
/*	function uf_scv_load_dt_personal_internacional($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_load_dt_personal_int
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $ai_totrows   // total de lineas del grid
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que carga el grid con el personal de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 07/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_monto=0;
		$ls_sql="SELECT monbol".
				"  FROM scv_dt_misiones,scv_tarifas".
				" WHERE scv_dt_misiones.codemp='".$as_codemp."'".
				"   AND scv_dt_misiones.codsolvia='".$as_codsolvia."'".
				"   AND scv_dt_misiones.codemp=scv_tarifas.codemp".
				"   AND scv_dt_misiones.codmis=scv_tarifas.codmis".
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_load_dt_personal_int ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_monbol=$row["monbol"];
				$ls_monto=$ls_monto+$ls_monbol;
				
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_monto;
	}  // end function uf_scv_load_dt_personal
*/
	function uf_load_misiones_internacionales($as_codemp,$as_codsolvia,$as_cargo,$as_codnom,$aa_object,$li_i)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_misiones_internacionales
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $ai_totrows   // total de lineas del grid
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que carga el grid con el personal de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 07/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$li_i=0;
		$li_totol=0;
		$li_totolgeneral=0;
		$ls_sql="SELECT codmis,cantidad,".
				"       (SELECT denmis FROM scv_misiones".
				"		  WHERE scv_dt_misiones.codemp=scv_misiones.codemp".
				"           AND scv_dt_misiones.codmis=scv_misiones.codmis) AS denmis".
				"  FROM scv_dt_misiones".
				" WHERE scv_dt_misiones.codemp='".$as_codemp."'".
				"   AND scv_dt_misiones.codsolvia='".$as_codsolvia."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_load_misiones_internacionales ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codmis=$row["codmis"];
				$li_cantidad=$row["cantidad"];
				$ls_denmis=$row["denmis"];
				$li_tarifa=$this->uf_scv_tarifas_internacionales($as_codemp,$ls_codmis,$as_cargo,$as_codnom);
				$li_totol=($li_cantidad*$li_tarifa);
				$li_totolgeneral=$li_totolgeneral+$li_totol;
				$li_i=$li_i+1;
			//	$li_cantidad= number_format($li_cantidad,2,',','.');
				$li_tarifa= number_format($li_tarifa,2,',','.');
				$li_totol= number_format($li_totol,2,',','.');
				$aa_object[$li_i][1]="<input name=txtcodmisdes".$li_i."  type=text  id=txtcodmisdes".$li_i."  class=sin-borde size=8  value='". $ls_codmis ."' readonly style='text-align:center'>";
				$aa_object[$li_i][2]="<input name=txtdenmisdes".$li_i."  type=text  id=txtdenmisdes".$li_i."  class=sin-borde size=50  value='". $ls_denmis ."'readonly >";
				$aa_object[$li_i][3]="<input name=txtcantidad".$li_i."   type=text  id=txtcantidad".$li_i."   class=sin-borde size=8  value='". $li_cantidad ."'  style='text-align:right' readonly>";
				$aa_object[$li_i][4]="<input name=txttarifa".$li_i."     type=text  id=txttarifa".$li_i."   class=sin-borde size=10  value='". $li_tarifa ."'  style='text-align:right'   readonly>";
				$aa_object[$li_i][5]="<input name=txtautorizado".$li_i." type=text  id=txtautorizado".$li_i."   class=sin-borde size=10  value='". $li_tarifa ."'  style='text-align:right'   onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur=ue_recalcular_monto(".$li_i.");>";
				$aa_object[$li_i][6]="<input name=txttotal".$li_i."      type=text  id=txttotal".$li_i."   class=sin-borde size=10  value='". $li_totol ."'  style='text-align:right' readonly>";
				$lb_valido=true;
				
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['aa_object']=$aa_object;
		$arrResultado['li_i']=$li_i;
		$arrResultado['li_totolgeneral']=$li_totolgeneral;
		return $arrResultado;		
	}  // end function uf_scv_load_dt_personal

	function uf_scv_tarifas_internacionales($as_codemp,$as_codmis,$as_cargo,$as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_tarifas_internacionales
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_montar="";
		if($as_codnom!="")
		{
			$ls_sql="SELECT codcat".
					"  FROM scv_catcargos,scv_dt_catcargos".
					" WHERE scv_catcargos.codemp='". $as_codemp ."'".
					"   AND scv_dt_catcargos.codcar='". $as_cargo ."'".
					"   AND scv_dt_catcargos.codnom= '".$as_codnom."'".
					"   AND scv_catcargos.codemp=scv_dt_catcargos.codemp".
					"   AND scv_catcargos.codcatcar=scv_dt_catcargos.codcatcar";
		}
		else
		{
			$ls_sql="SELECT codcat".
					"  FROM scv_catcargos".
					" WHERE scv_catcargos.codemp='". $as_codemp ."'".
					"   AND scv_catcargos.foraneo='1'";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_tarifas_internacionales ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codcat=$row["codcat"];
				if($ls_codcat!="")
				{
					$ls_sql="SELECT mondol".
							"  FROM scv_tarifas".
							" WHERE scv_tarifas.codemp='". $as_codemp ."'".
							"   AND scv_tarifas.codmis='". $as_codmis ."'".
							"   AND scv_tarifas.codcat='".$ls_codcat."'";
					$rs_data2=$this->io_sql->select($ls_sql);
					if($row=$this->io_sql->fetch_row($rs_data2))
					{
						$li_montar=$row["mondol"];
					}
					$this->io_sql->free_result($rs_data2);
				}
				
			}
			else
			{
				$this->io_msg->message("La Persona no tiene Categoria de Viatico Asignada. No se puede realizar el Calculo");
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_montar;
	}  // end function uf_scv_select_solicitudviaticos
	
	function uf_scv_load_calculo_personal_int($as_codemp,$as_codsolvia,$as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_load_calculo_personal_int
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $ai_totrows   // total de lineas del grid
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que carga el grid con el personal de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 07/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT (CASE sno_nomina.racnom WHEN '1' THEN sno_asignacioncargo.codasicar ELSE sno_cargo.codcar END) AS codcar,".
				"		(SELECT cedper FROM sno_personal".
				"		  WHERE sno_personal.codper=sno_personalnomina.codper) as cedper,MAX(scv_dt_personal.codnom) AS codnom".
				"  FROM sno_personalnomina, sno_nomina, sno_cargo, sno_asignacioncargo,sno_personal,scv_dt_personal".
				" WHERE scv_dt_personal.codemp='".$as_codemp."'".
				"   AND scv_dt_personal.codsolvia='".$as_codsolvia."'".
				"   AND scv_dt_personal.codper='".$as_codper."'".
				"   AND scv_dt_personal.codemp=sno_personal.codemp".
				"   AND scv_dt_personal.codper=sno_personal.codper".
				"   AND scv_dt_personal.codemp=sno_personalnomina.codemp".
				"   AND scv_dt_personal.codnom=sno_personalnomina.codnom".
				"   AND sno_nomina.espnom='0'".
				"   AND sno_personalnomina.codemp = sno_nomina.codemp".
				"   AND sno_personalnomina.codnom = sno_nomina.codnom".
				"   AND sno_personalnomina.codper = sno_personal.codper".
				"   AND sno_personalnomina.codemp = sno_cargo.codemp".
				"   AND sno_personalnomina.codnom = sno_cargo.codnom".
				"   AND sno_personalnomina.codcar = sno_cargo.codcar".
				"   AND sno_personalnomina.codemp = sno_asignacioncargo.codemp".
				"   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom".
				"   AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar".
				" GROUP BY sno_personalnomina.codper,  sno_personalnomina.codper, sno_nomina.racnom,  ".
				" sno_asignacioncargo.denasicar,sno_asignacioncargo.codasicar, sno_cargo.descar, sno_cargo.codcar,scv_dt_personal.codclavia".
				" ORDER BY sno_personalnomina.codper"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos M?TODO->uf_scv_load_dt_personal_int ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codnom=$row["codnom"];
				$ls_cargo= $row["codcar"];
				$lb_monto=$this->uf_scv_select_tarifacargos($as_codemp,$ls_cargo,$ls_codnom);
				
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_monto;
	}  // end function uf_scv_load_dt_personal

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_select_tarifacargos($as_codemp,$as_codcar,$as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_tarifacargos
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/11/2006 								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_montarcar="";
		$ls_sql="SELECT montarcar".
				"  FROM scv_dt_tarifacargos".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codcar='". $as_codcar ."'".
				"   AND codnom= '".$as_codnom."'"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integraci?n M?TODO->uf_scv_select_tarifacargos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montarcar=$row["montarcar"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_montarcar;
	}  // end function uf_scv_select_tarifacargos
	//-----------------------------------------------------------------------------------------------------------------------------------


} //end class sigesp_scv_c_calcularviaticos  
?>
