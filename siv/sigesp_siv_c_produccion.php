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

class sigesp_siv_c_produccion
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
		require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
		require_once("sigesp_siv_c_movimientoinventario.php");
		  
		$this->dat_emp=   $_SESSION["la_empresa"];
		$this->ls_gestor= $_SESSION["ls_gestor"];
		$in=              new sigesp_include();
		$this->con=       $in->uf_conectar();
		$this->io_sql=    new class_sql($this->con);
		$this->seguridad= new sigesp_c_seguridad();
		$this->fun=       new class_funciones_db($this->con);
		$this->io_msg=    new class_mensajes();
		$this->io_funcion=new class_funciones();
		$this->io_mov= new sigesp_siv_c_movimientoinventario();
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}

	function uf_siv_select_produccion($as_codemp,$as_numtra,$ad_fecemi)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp //codigo de empresa 
		//                 $as_numtra //numero de transferencia 
		//                 $ad_fecemi //fecha de emision
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion busca si existe una transferencia entre almacenes en la tabla de  siv_transferencia
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_sql = "SELECT * FROM siv_produccion  ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND numpro='".$as_numtra."'";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->produccion M?TODO->uf_siv_select_produccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
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
	} // end  function uf_siv_select_transferencia

	function uf_siv_insert_produccion($as_codemp,$as_numtra,$ad_fecemi,$as_codusu,$as_codalmori,$as_codalmdes,$as_obstra,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp    //codigo de empresa				$as_numtra    // numero de transferencia
		//                 $ad_fecemi    // fecha de emision				$as_codusu    // codigo del usuario
		//                 $as_codalmori // codigo de almacen de origen		$as_codalmdes // codigo de almacen de destino
		//                 $as_obstra    // observacion de la transferencia	$aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion inserta una operacion de transferencia entre almacenes  en la tabla de  siv_transferencia
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_emp="";
		$ls_empresa="";
		$ls_tabla="siv_produccion";
		$ls_columna="numpro";
		$as_numtra=$this->fun->uf_generar_codigo($ls_emp,$ls_empresa,$ls_tabla,$ls_columna);
		$ls_sql="INSERT INTO siv_produccion (codemp, numpro, fecemi, codusu, obspro, codalmsal, codalment)".
				" VALUES ('".$as_codemp."','".$as_numtra."','".$ad_fecemi."','".$as_codusu."','".$as_obstra."',".
				"         '".$as_codalmori."','".$as_codalmdes."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->produccion M?TODO->uf_siv_insert_produccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Realizo la Produccion ".$as_numtra." del Almac?n ".$as_codalmori." al Almac?n ".$as_codalmdes.". Asociados a la Empresa ".$as_codemp;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		$arrResultado['as_numtra']=$as_numtra;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end  function uf_siv_insert_transferencia

	function uf_siv_update_transferencia($as_codemp,$as_numtra,$ad_fecemi,$as_codusu,$as_codalmori,$as_codalmdes,$as_obstra,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp    //codigo de empresa				$as_numtra    // numero de transferencia
		//                 $ad_fecemi    // fecha de emision				$as_codusu    // codigo del usuario
		//                 $as_codalmori // codigo de almacen de origen		$as_codalmdes // codigo de almacen de destino
		//                 $as_obstra    // observacion de la transferencia	$aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion modifica una operacion de transferencia entre almacenes en la tabla de  siv_transferencia
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $ls_sql= "UPDATE siv_transferencia".
		 		  "   SET codusu='".$as_codusu."',".
				  "       codalmori='".$as_codalmori."',".
				  "       codalmdes='".$as_codalmdes."',".
				  "       obstra='".$as_obstra."' ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND numtra='".$as_numtra."'".
				  "   AND fecemi='".$ad_fecemi."'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_update_transferencia ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		$arrResultado['as_numtra']=$as_numtra;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end  function uf_siv_update_transferencia

	function uf_siv_delete_transferencia($as_codemp,$as_numtra,$ad_fecemi,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_delete_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp    //codigo de empresa				$as_numtra    // numero de transferencia
		//                 $ad_fecemi    // fecha de emision				$aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion elimina una transferencia entre almacenes en la tabla de  siv_transferencia
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = " DELETE FROM siv_transferencia".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND numtra='".$as_numtra."'".
				  "   AND fecemi='".$ad_fecemi."'";
		$this->io_sql->begin_transaction();	
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_delete_transferencia1 ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	} // end  function uf_siv_delete_transferencia 

	function uf_siv_select_dt_produccion($as_codemp,$as_numtra,$ad_fecemi,$as_codart)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_dt_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp    //codigo de empresa 
		//                 $as_numtra    // numero de transferencia
		//                 $ad_fecemi    // fecha de emision 
		//                 $as_codart    // codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los detalles asociados a una transferencia entre almacenes en la tabla siv_dt_transferencia
		//	   Creado Por: Ing. Luis Anibal Lang
		// Modificado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 31/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT * FROM siv_dt_produccion".
				" WHERE codemp='".$as_codemp."'".
				"   AND numpro='".$as_numtra."'".
				"   AND codart='".$as_codart."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_select_dt_transferencia ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
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
	}  // end function uf_siv_select_dt_transferencia
	
	function uf_siv_insert_dt_produccion($as_codemp,$as_numtra,$ad_fecemi,$as_codart,$as_unidad,$ai_cantidad,$ai_cosuni,$ai_costot,$as_opeinv,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_dt_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp    //codigo de empresa				$as_numtra    // numero de transferencia
		//                 $ad_fecemi    // fecha de emision				$as_codart    // codigo de articulo
		//                 $ai_cosuni    // costo unitario 					$as_unidad    // unidad de medida M->Mayor D->Detal
		//                 $ai_costot    // costo total 					$ai_cantidad  // cantidad de articulos a ser transferidos
		//                 $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un detalle de una transferencia entre almacenes en la tabla de  siv_dt_transferencia
		//	   Creado Por: Ing. Luis Anibal Lang
		// Modificado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 31/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO siv_dt_produccion (codemp, numpro, fecemi, codart, unidad, cantidad, cosuni, costot,opeinv)".
				" VALUES ('".$as_codemp."','".$as_numtra."','".$ad_fecemi."','".$as_codart."','".$as_unidad."', ".
				"         ".$ai_cantidad.",".$ai_cosuni.",".$ai_costot.",'".$as_opeinv."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->produccion M?TODO->uf_siv_insert_dt_produccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	} // end function uf_siv_insert_dt_transferencia

	function uf_siv_update_dt_produccion($as_codemp,$as_numtra,$ad_fecemi,$as_codart,$as_codunimed,$ai_cantidad,$ai_cosuni,$ai_costot,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_dt_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp    //codigo de empresa				$as_numtra    // numero de transferencia
		//                 $ad_fecemi    // fecha de emision				$as_codart    // codigo de articulo
		//                 $as_codunimed // codigo de unidad de medida		$ai_cantidad  // cantidad de articulos a ser transferidos
		//                 $ai_cosuni    // costo unitario					$ai_costot    // costo total 
		//                 $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un detalle de una transferencia entre almacenes en la tabla de  siv_dt_transferencia
		//	   Creado Por: Ing. Luis Anibal Lang
		// Modificado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 31/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $ls_sql= "UPDATE siv_dt_produccion".
		 		  "   SET codunimed='".$as_codunimed."',".
				  "       cantidad='".$ai_cantidad."',".
				 // "       cosuni='".$ai_cantidad."',".  estaba anteriormente fuen modificado en al sigte linea
				  "       cosuni='".$ai_cosuni."',".
				  "       costot='".$ai_costot."' ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND numpro='".$as_numtra."'".
				  "   AND fecemi='".$ad_fecemi."'".
				  "   AND codart='".$as_codart."'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row==false&&($this->io_sql->message!=""))
		{
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_update_dt_transferencia ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
	  return $lb_valido;
	} // end  function uf_siv_update_dt_transferencia

	function uf_siv_guardar_dt_produccion($as_codemp,$as_numtra,$ad_fecemi,$as_codart,$as_codunimed,$ai_cantidad,$ai_cosuni,$ai_costot,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_guardar_dt_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp    //codigo de empresa				$as_numtra    // numero de transferencia
		//                 $ad_fecemi    // fecha de emision				$as_codart    // codigo de articulo
		//                 $as_codunimed // codigo de unidad de medida		$ai_cantidad  // cantidad de articulos a ser transferidos
		//                 $ai_cosuni    // costo unitario					$ai_costot    // costo total 
		//                 $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion deacuerdo a una busqueda (select) inserta ? modifica un  detalle de la transferencia 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Modificado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 31/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if(!($this->uf_siv_select_dt_produccion($as_codemp,$as_numtra,$ad_fecemi,$as_codart)))
		{
			$lb_valido=$this->uf_siv_insert_dt_produccion($as_codemp,$as_numtra,$ad_fecemi,$as_codart,$as_codunimed,
			                                                 $ai_cantidad,$ai_cosuni,$ai_costot,"S",$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_update_dt_produccion($as_codemp,$as_numtra,$ad_fecemi,$as_codart,$as_codunimed,
			                                                 $ai_cantidad,$ai_cosuni,$ai_costot,$aa_seguridad);
		}
		return $lb_valido;
	} // end function uf_siv_guardar_dt_transferencia

	function uf_siv_delete_dt_transferencia($as_codemp,$as_numtra,$ad_fecemi,$as_codart)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_siv_delete_dt_transferencia
	//	Access:    public
	//	Arguments:
	//  as_codemp    // codigo de empresa
	//  as_numtra    // numero de transferencia
	//  ad_fecemi    // fecha de emision
	//  as_codart    // codigo de articulo
	//  aa_seguridad // arreglo de registro de seguridad
	//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
	//	Description:  Esta funcion elimina los detalles asociados a una transferencia 
	//                entre almacenes en la tabla de  siv_dt_transferencia
	// Modificado Por: Ing. Yozelin Barragan
	// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 31/08/2007 
	//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=true;
		$ls_sql = " DELETE FROM siv_dt_transferencia".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND numtra='".$as_numtra."'".
				  "   AND fecemi='".$ad_fecemi."'".
				  "   AND codart='".$as_codart."'";
				
		$li_row=$this->io_sql->select($ls_sql);

		if($li_row==false)
		{
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_delete_dt_transferencia ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	}

	function uf_siv_obtener_dt_produccion($as_codemp,$as_numtra,$ad_fecemi,$ai_totrows,$ao_object,$ai_totentsum)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_siv_obtener_dt_transferencia
	//	Access:    public
	//	Arguments:
	//  as_codemp    // codigo de empresa
	//  as_numtra    // numero de transferencia
	//  ad_fecemi    // fecha de emision
	//  ai_totrows   // total de filas encontradas
	//  ao_object    // arreglo de objetos para pintar el grid
	//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
	//	Description:  Esta funcion busca los detalles asociados a un  movimientos  en la tabla de  siv_dt_movimiento y los imprime en el grid
	//              
	//////////////////////////////////////////////////////////////////////////////		
		$li_numdecper=$_SESSION["la_empresa"]["numdecper"];
		$lb_valido=true;
		$ai_totentsum=0;
		$ls_sql="SELECT siv_dt_produccion.*,siv_articulo.codunimed,siv_unidadmedida.unidad AS unidades,siv_unidadmedida.denunimed AS denunimed,".
				"       (SELECT denart FROM siv_articulo ".
				"         WHERE siv_dt_produccion.codart=siv_articulo.codart) AS denart".
				"  FROM siv_dt_produccion,siv_articulo,siv_unidadmedida".
				" WHERE siv_dt_produccion.codemp='".$as_codemp."'".
				"   AND siv_dt_produccion.numpro='".$as_numtra."'".
				"   AND siv_dt_produccion.opeinv='S'".
				"   AND siv_dt_produccion.codart=siv_articulo.codart".
				"   AND siv_articulo.codunimed=siv_unidadmedida.codunimed";

		$rs_dada=$this->io_sql->select($ls_sql);
		if($rs_dada===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_obtener_dt_transferencia ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_dada))
			{
					$ls_codart=     $row["codart"];
					$ls_denart=     $row["denart"];
					$ls_denunimed=  $row["denunimed"];
					$ls_unidad=     $row["unidad"];
					$li_unidad=     $row["unidades"];
					$li_cantidad=   $row["cantidad"];
					$li_cosuni=     $row["cosuni"];
					$li_costot=     $row["costot"];
					$ai_totentsum=$ai_totentsum+$li_costot;
					switch ($ls_unidad) 
					{
						case "M":
							$ls_unidadaux="Mayor";
							$li_cantidad= ($li_cantidad/$li_unidad);
							$li_cosuni=($li_cosuni*$li_unidad);
							break;
						case "D":
							$ls_unidadaux="Detal";
							break;
					}
					$ai_totrows=$ai_totrows+1;
					if($li_numdecper!="3")
					{
						$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
						$li_cantidad=number_format($li_cantidad,2,",",".");
					}
					else
					{
						$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
						$li_cantidad=number_format($li_cantidad,3,",",".");
					}
					$ao_object[$ai_totrows][1]="<input name=txtdenart".$ai_totrows."   type=text id=txtdenart".$ai_totrows."   class=sin-borde size=15 maxlength=50 value='".$ls_denart."' readonly><input name=txtcodart".$ai_totrows." type=hidden id=txtcodart".$ai_totrows." class=sin-borde size=21 maxlength=20 value='".$ls_codart."' onKeyUp='javascript: ue_validarcomillas(this);' readonly>";
					$ao_object[$ai_totrows][2]="<input name=txtdenunimed".$ai_totrows."   type=text id=txtdenunimed".$ai_totrows."   class=sin-borde size=14 maxlength=12 value='".$ls_denunimed."' readonly>";
					$ao_object[$ai_totrows][3]="<input name=txtcoduni".$ai_totrows."   type=text id=txtcoduni".$ai_totrows."   class=sin-borde size=14 maxlength=12 value='".$ls_unidadaux."' onKeyUp='javascript: ue_validarcomillas(this);' readonly><input name='hidunidad".$ai_totrows."' type='hidden' id='hidunidad".$ai_totrows."' value='". $li_unidad ."'>";
					$ao_object[$ai_totrows][4]="<input name=txtcantidad".$ai_totrows." type=text id=txtcantidad".$ai_totrows." class=sin-borde size=14 maxlength=12 value='".$li_cantidad."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";
					$ao_object[$ai_totrows][5]="<input name=txtcosuni".$ai_totrows."   type=text id=txtcosuni".$ai_totrows."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_cosuni,2,",",".")."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";
					$ao_object[$ai_totrows][6]="<input name=txtcostot".$ai_totrows."   type=text id=txtcostot".$ai_totrows."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_costot,2,",",".")."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";
					$ao_object[$ai_totrows][7]="";
					$ao_object[$ai_totrows][8]="";			

			}//while
		}//else
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['ai_totentsum']=$ai_totentsum;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}

	function uf_siv_obtener_dt_produccion2($as_codemp,$as_numtra,$ad_fecemi,$ai_totrows,$ao_object)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_siv_obtener_dt_transferencia
	//	Access:    public
	//	Arguments:
	//  as_codemp    // codigo de empresa
	//  as_numtra    // numero de transferencia
	//  ad_fecemi    // fecha de emision
	//  ai_totrows   // total de filas encontradas
	//  ao_object    // arreglo de objetos para pintar el grid
	//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
	//	Description:  Esta funcion busca los detalles asociados a un  movimientos  en la tabla de  siv_dt_movimiento y los imprime en el grid
	//              
	//////////////////////////////////////////////////////////////////////////////		
		$li_numdecper=$_SESSION["la_empresa"]["numdecper"];
		$lb_valido=true;
		$ls_sql="SELECT siv_dt_produccion.*,siv_articulo.codunimed,siv_unidadmedida.unidad AS unidades,siv_unidadmedida.denunimed AS denunimed,".
				"       (SELECT denart FROM siv_articulo ".
				"         WHERE siv_dt_produccion.codart=siv_articulo.codart) AS denart".
				"  FROM siv_dt_produccion,siv_articulo,siv_unidadmedida".
				" WHERE siv_dt_produccion.codemp='".$as_codemp."'".
				"   AND siv_dt_produccion.numpro='".$as_numtra."'".
				"   AND siv_dt_produccion.opeinv='E'".
				"   AND siv_dt_produccion.codart=siv_articulo.codart".
				"   AND siv_articulo.codunimed=siv_unidadmedida.codunimed";

		$rs_dada=$this->io_sql->select($ls_sql);
		if($rs_dada===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_obtener_dt_transferencia ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_dada))
			{
					$ls_codart=     $row["codart"];
					$ls_denart=     $row["denart"];
					$ls_denunimed=  $row["denunimed"];
					$ls_unidad=     $row["unidad"];
					$li_unidad=     $row["unidades"];
					$li_cantidad=   $row["cantidad"];
					$li_cosuni=     $row["cosuni"];
					$li_costot=     $row["costot"];
					switch ($ls_unidad) 
					{
						case "M":
							$ls_unidadaux="Mayor";
							$li_cantidad= ($li_cantidad/$li_unidad);
							$li_cosuni=($li_cosuni*$li_unidad);
							break;
						case "D":
							$ls_unidadaux="Detal";
							break;
					}
					if($li_numdecper!="3")
					{
						$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
						$li_cantidad=number_format($li_cantidad,2,",",".");
					}
					else
					{
						$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
						$li_cantidad=number_format($li_cantidad,3,",",".");
					}
					$ai_totrows=$ai_totrows+1;
					$ao_object[$ai_totrows][1]="<input name=txtdenartent".$ai_totrows."   type=text id=txtdenartent".$ai_totrows."   class=sin-borde size=15 maxlength=50 value='".$ls_denart."' readonly><input name=txtcodartent".$ai_totrows." type=hidden id=txtcodartent".$ai_totrows." class=sin-borde size=21 maxlength=20 value='".$ls_codart."' onKeyUp='javascript: ue_validarcomillas(this);' readonly>";
					$ao_object[$ai_totrows][2]="<input name=txtdenunimedent".$ai_totrows."   type=text id=txtdenunimedent".$ai_totrows."   class=sin-borde size=14 maxlength=12 value='".$ls_denunimed."' readonly>";
					$ao_object[$ai_totrows][3]="<input name=txtcodunient".$ai_totrows."   type=text id=txtcodunient".$ai_totrows."   class=sin-borde size=14 maxlength=12 value='".$ls_unidadaux."' onKeyUp='javascript: ue_validarcomillas(this);' readonly><input name='hidunidadent".$ai_totrows."' type='hidden' id='hidunidadent".$ai_totrows."' value='". $li_unidad ."'>";
					$ao_object[$ai_totrows][4]="<input name=txtcantidadent".$ai_totrows." type=text id=txtcantidadent".$ai_totrows." class=sin-borde size=14 maxlength=12 value='".$li_cantidad."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";
					$ao_object[$ai_totrows][5]="<input name=txtcosunient".$ai_totrows."   type=text id=txtcosunient".$ai_totrows."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_cosuni,2,",",".")."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";
					$ao_object[$ai_totrows][6]="<input name=txtcostotent".$ai_totrows."   type=text id=txtcostotent".$ai_totrows."   class=sin-borde size=14 maxlength=15 value='".number_format ($li_costot,2,",",".")."' onKeyUp='javascript: ue_validarnumero(this);' readonly>";

			}//while
		}//else
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}

	function uf_select_metodo($ls_metodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_metodo
		//         Access: private
		//      Argumento: $ls_metodo    // metodo de inventario
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que metodo de inventario esta siendo utilizado actualmente.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/02/2006 								Fecha ?ltima Modificaci?n :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT * FROM siv_config";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_select_metodo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_metodo=$row["metodo"];
			}
			else
			{
				$lb_valido=false;
				$this->io_msg->message("No se ha definido la configuraci?n de inventario");
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ls_metodo']=$ls_metodo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	} // end  function uf_select_metodo
	
	function uf_select_movimiento($ls_metodo,$rs_metodo,$as_codart,$as_codalm)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_movimiento
		//         Access: private
		//      Argumento: $ls_metodo    // metodo de inventario
		//                 $rs_metodo    // result set de la operacion del select
		//                 $as_codart    // codigo de articulo
		//                 $as_codalm    // codigo de almac?n
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los movimientos que no han sido reversados y los ordena segun sea el el metodo 
	    //				   de inventario (en caso de ser FIFO ? LIFO), o saca el promedio si es Costo Promedio Ponderado
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/02/2006 								Fecha ?ltima Modificaci?n :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($ls_metodo=="FIFO")
		{
			if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND CONCAT(promov,numdocori) NOT IN".
						" (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov";
			}
			else
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE  codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND promov || numdocori NOT IN".
						" (SELECT promov || numdocori FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov"; 
			}
			
			$rs_metodo=$this->io_sql->select($ls_sql);
		}
		if($ls_metodo=="LIFO")
		{
			if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND CONCAT(promov,numdocori) NOT IN".
						" (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov DESC";
			}
			else
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE  codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND promov || numdocori NOT IN".
						" (SELECT promov || numdocori FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov DESC";
			}
			$rs_metodo=$this->io_sql->select($ls_sql);
		}	
		if($ls_metodo=="CPP")
		{
			if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
			{
				$ls_sql="SELECT Avg(cosart) as cosart".
						" FROM siv_dt_movimiento".
						" WHERE  codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND CONCAT(promov,numdocori) NOT IN".
						" (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov DESC";
			}
			if($this->ls_gestor=="INFORMIX")
			{
				$ls_sql="SELECT Avg(cosart) as cosart, nummov".
						" FROM siv_dt_movimiento".
						" WHERE  codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND promov || numdocori NOT IN".
						" (SELECT promov || numdocori FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" GROUP BY cosart,nummov".
						" ORDER BY nummov DESC"; 
			}
			else
			{
				$ls_sql="SELECT Avg(cosart) as cosart".
						" FROM siv_dt_movimiento".
						" WHERE  codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND promov || numdocori NOT IN".
						" (SELECT promov || numdocori FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" GROUP BY cosart,nummov".
						" ORDER BY nummov DESC"; 
			}
			$rs_metodo=$this->io_sql->select($ls_sql);
		}	
		if($rs_metodo===false)
		{
			$this->io_msg->message("CLASE->transferencias M?TODO->uf_select_movimiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_metodo']=$rs_metodo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	} // end function uf_select_movimiento

	function uf_siv_procesar_dt_movimientotransferencia($as_codemp,$as_nummov,$as_codart,$as_codalm,$as_unidad,$ai_canart,
	                                                    $ai_preuniart,$ad_fecemi,$as_numtra,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_dt_movimientotransferencia
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa							$as_numorddes // numero de orden de despacho
		//                 $as_codart    // codigo de articulo							$as_codalm    // codigo de almac?n								
		//                 $as_unidad    // codigo de unidad M-->Mayor D->Detal		 	$ai_canorisolsep // cantidad de articulos de la SEP
		//                 $ai_canart    // cantidad despachada de articulos			$ai_preuniart    // precio unitario del articulo
		//                 $ai_canoriart // codigo de procedencia del documento			$as_nummov       // numero de movimiento
		//                 $ad_fecdesaux // fecha del despacho							$as_numsol      // numero de la SEP
		//                 $as_numconrec // comprobante (numero concecutivo para hacer unica la recepcion)
		//                 $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funci?n que verifica que metodo de inventario se esta utilizando y adem?s va buscando los precios unitarios 
	    //				   en caso de que no existan suficientes artiulos al mismo precio y procede a llamar al metodo de insert_dt_movimientos
	    //				   y al insert_dt_despacho para ingresarlo en la tabla siv_dt_despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/02/2006 								Fecha ?ltima Modificaci?n :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_metodo="";
		$rs_metodo="";
		$arrResultado=$this->uf_select_metodo($ls_metodo);
		$ls_metodo = $arrResultado['ls_metodo'];
		$lb_valido = $arrResultado['lb_valido'];
		if ($lb_valido)
		{
			$arrResultado=$this->uf_select_movimiento($ls_metodo,$rs_metodo,$as_codart,$as_codalm);
			$rs_metodo = $arrResultado['rs_metodo'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido)
			{
				if($ls_metodo!="CPP")
				{
					$lb_break=false;
					$li_diferencia=0;
					$li_i=0;
					while(($row=$this->io_sql->fetch_row($rs_metodo))&&(!$lb_break))
					{
						$li_preuniart=$row["cosart"];
						$ls_numdocori=$row["numdocori"];
						$ls_nummov=$row["nummov"];
						$ls_codalm=$row["codalm"];
						
						if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
						{
							$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN candesart ELSE -candesart END) total FROM siv_dt_movimiento".
									" WHERE codemp='".$as_codemp."'".
									"   AND codart='".$as_codart."'".
									"   AND codalm='".$as_codalm."'".
									"   AND numdocori='".$ls_numdocori."'".
									"   AND CONCAT(promov,numdocori) NOT IN".
									" (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
									"   WHERE opeinv ='REV')".
									" ORDER BY nummov";
						}
						if($this->ls_gestor=="INFORMIX")
						{
							$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN candesart ELSE -candesart END) AS total FROM siv_dt_movimiento".
									" WHERE codemp='".$as_codemp."'".
									"   AND codart='".$as_codart."'".
									"   AND codalm='".$as_codalm."'".
									"   AND numdocori='".$ls_numdocori."'".
									"   AND promov  || numdocori NOT IN".
									" (SELECT promov || numdocori FROM siv_dt_movimiento".
									"   WHERE opeinv ='REV')".
									" GROUP BY nummov"; 
						}
						else
						  {
							$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN candesart ELSE -candesart END) AS total FROM siv_dt_movimiento".
									" WHERE codemp='".$as_codemp."'".
									"   AND codart='".$as_codart."'".
									"   AND codalm='".$as_codalm."'".
									"   AND numdocori='".$ls_numdocori."'".
									"   AND promov  || numdocori NOT IN".
									" (SELECT promov || numdocori FROM siv_dt_movimiento".
									"   WHERE opeinv ='REV')".
									" GROUP BY nummov".
									" ORDER BY nummov"; 
						  }
						$rs_data1=$this->io_sql->select($ls_sql);
						if($row1=$this->io_sql->fetch_row($rs_data1))
						{
							$li_existencia=$row1["total"];
							if ($li_existencia > 0)
							{
								$lb_encontrado=true;
								$li_i=$li_i + 1;

								if ($li_existencia < $ai_canart)
								{
									$ai_canart= $ai_canart-$li_existencia;

									$lb_valido=$this->uf_siv_disminuir_articuloxmovimiento($as_codemp,$as_codart,$ls_codalm,$ls_nummov,
																							$ls_numdocori,$li_existencia);
									if ($lb_valido)
									{
										$ls_opeinv="SAL";
										$ls_promov="PRO";
										$ls_codprodoc="ALM";
										$li_candesart="0.00";
										$lb_valido=$this->io_mov->uf_siv_insert_dt_movimiento($as_codemp,$as_nummov,$ad_fecemi,
																						  	  $as_codart,$as_codalm,$ls_opeinv,$ls_codprodoc,
																							  $as_numtra,$li_existencia,$li_preuniart,$ls_promov,
																						  	  $as_numtra,$li_candesart,$ad_fecemi,
																							  $aa_seguridad);
									}			
															
								}  // fin  if ($li_existencia < $ai_canart)
								elseif($li_existencia >= $ai_canart)
								{
									$lb_valido=$this->uf_siv_disminuir_articuloxmovimiento($as_codemp,$as_codart,$ls_codalm,
																						   $ls_nummov,$ls_numdocori,$ai_canart);
									if ($lb_valido)
									{
										$ls_opeinv="SAL";
										$ls_promov="PRO";
										$ls_codprodoc="ALM";
										$li_candesart="0.00";
										$lb_valido=$this->io_mov->uf_siv_insert_dt_movimiento($as_codemp,$as_nummov,$ad_fecemi,
																						  	  $as_codart,$as_codalm,$ls_opeinv,$ls_codprodoc,
																							  $as_numtra,$ai_canart,$li_preuniart,$ls_promov,
																						  	  $as_numtra,$li_candesart,$ad_fecemi,
																							  $aa_seguridad);
										if($lb_valido)
										{
											$lb_break=true;
										}
									}
								}
								if(!$lb_valido)
								{
									$lb_break=true;
								}
							}  // fin  ($li_existencia > 0)
						}  //fin  if($row1=$io_sql->fetch_row($rs_datas1))
					}// fin  while(($row=$io_sql->fetch_row($rs_metodo))&&(!$lb_break))
				}// fin  if($ls_metodo!="CPP")
				else
				{
					if($row=$this->io_sql->fetch_row($rs_metodo))
					{
						$li_preuniart=$row["cosart"];
						$ls_numdocori="";   
						$ls_opeinv="SAL";
						$ls_promov="PRO";
						$ls_codprodoc="ALM";
						$li_candesart="0.00";
						$lb_valido=$this->io_mov->uf_siv_insert_dt_movimiento($as_codemp,$as_nummov,$ad_fecemi,
																			  $as_codart,$as_codalm,$ls_opeinv,$ls_codprodoc,
																			  $as_numtra,$ai_canart,$li_preuniart,$ls_promov,
																			  $as_numtra,$li_candesart,$ad_fecemi,
																			  $aa_seguridad);
					}// fin  if($row=$this->io_sql->fetch_row($rs_metodo))
				}// fin  else($ls_metodo!="CPP")
			}
		}
		return $lb_valido;
	}// end  function uf_siv_procesar_dt_movimientotransferencia

	function uf_siv_disminuir_articuloxmovimiento($as_codemp,$as_codart,$as_codalm,$as_nummov,$ls_numdocori,$ai_cantidad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_disminuir_articuloxmovimiento
		//         Access: private
		//      Argumento: $as_codemp       // codigo de empresa
		//                 $as_codart       // codigo de articulo
		//                 $as_codalm       // codigo de almacen
		//                 $ls_numdocori    // numero original de la entrada de suministros a almac?n
		//                 $as_nummov       // numero de movimiento
		//                 $as_cantidad     // cantidad de articulos
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que disminuye la cantidad de articulos proveniente de un movimiento en la tabla siv_dt_movimiento
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/02/2006 								Fecha ?ltima Modificaci?n :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $rs_disart=-1;
		 $ld_date= date("Y-m-d");
		 $ls_sql= "UPDATE siv_dt_movimiento".
		 		  "   SET candesart= (candesart - '". $ai_cantidad ."'), ".
		 		  "       fecdesart='".$ld_date."'".
				  " WHERE codemp='".$as_codemp."'".
				  " AND   opeinv='ENT'".
				  " AND   nummov='".$as_nummov."'".
				  " AND   codart='".$as_codart."'".
				  " AND   codalm='".$as_codalm."'".
				  " AND   numdocori='" . $ls_numdocori ."'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_disminuir_articuloxmovimiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	} // end  function uf_siv_disminuir_articuloxmovimiento

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_select_articulo($as_codart,$as_codalm,$as_denart,$ai_unidad,$ai_cosart,$ai_existencia,$as_denunimed)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_articulo
		//         Access: public (sigesp_siv_d_articulo)
		//      Argumento: $as_codart //codigo de articulo
		//				   $as_codalm // codigo de almancen
		//				   $as_denart // denominacion de articulo
		//				   $ai_unidad // unidad de medida 
		//				   $ai_cosart // costo del articulo
		//				   $ai_existencia // existencia en almacen determinado 
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que la disponibilidad de los articulos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 20/04/2010 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_denart="";
		$as_denunimed="";
		$ai_unidad=0;
		$ai_cosart=0;
		$ai_existencia=0;
		if(($this->ls_gestor=='MYSQLT') || ($this->ls_gestor=='MYSQLI'))
		{
			$ls_sql="SELECT siv_dt_movimiento.*,siv_articulo.denart,siv_articulo.codunimed,".
					"      (SELECT unidad FROM siv_unidadmedida".
					"        WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) unidad,".
					"      (SELECT denunimed FROM siv_unidadmedida".
					"        WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) denunimed,".
					"      (SELECT existencia FROM siv_articuloalmacen".
					"        WHERE siv_dt_movimiento.codart=siv_articuloalmacen.codart ".
					"          AND siv_dt_movimiento.codalm=siv_articuloalmacen.codalm) existencia".
					"  FROM siv_dt_movimiento,siv_articulo".
					" WHERE siv_dt_movimiento.codart=siv_articulo.codart".
					"   AND siv_dt_movimiento.codemp='".$this->ls_codemp."'".
					"   AND siv_dt_movimiento.codalm='".$as_codalm."'".
					"   AND siv_dt_movimiento.codart='".$as_codart."'".
					"   AND CONCAT(siv_dt_movimiento.promov,siv_dt_movimiento.numdocori) NOT IN".
					"      (SELECT CONCAT(siv_dt_movimiento.promov,siv_dt_movimiento.numdocori)".
					"         FROM siv_dt_movimiento".
					"        WHERE opeinv ='REV')".
					" GROUP BY codart ";
		}
		else
		{
			$ls_sql="SELECT siv_dt_movimiento.codart,MIN(siv_dt_movimiento.cosart) AS cosart,siv_dt_movimiento.codalm,".
					"       siv_articulo.denart,siv_articulo.codunimed,".
					"      (SELECT unidad FROM siv_unidadmedida".
					"        WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) AS unidad,".
					"      (SELECT denunimed FROM siv_unidadmedida".
					"        WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) AS denunimed,".
					"      (SELECT existencia FROM siv_articuloalmacen".
					"        WHERE siv_dt_movimiento.codart=siv_articuloalmacen.codart ".
					"          AND siv_dt_movimiento.codalm=siv_articuloalmacen.codalm) AS existencia".
					"  FROM siv_dt_movimiento,siv_articulo".
					" WHERE siv_dt_movimiento.codart=siv_articulo.codart".
					"   AND siv_dt_movimiento.codemp='".$this->ls_codemp."'".
					"   AND siv_dt_movimiento.codalm='".$as_codalm."'".
					"   AND siv_dt_movimiento.codart='".$as_codart."'".
					"   AND siv_dt_movimiento.promov || siv_dt_movimiento.numdocori NOT IN".
					"      (SELECT siv_dt_movimiento.promov || siv_dt_movimiento.numdocori".
					"         FROM siv_dt_movimiento".
					"        WHERE opeinv ='REV')".
					" GROUP BY siv_dt_movimiento.codart,siv_articulo.denart,siv_articulo.codunimed,".
					"          siv_dt_movimiento.codalm ".
					" ORDER BY siv_dt_movimiento.codart";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo M?TODO->uf_siv_select_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_denart=$row["denart"];
				$as_denunimed=$row["denunimed"];
				$ai_unidad=$row["unidad"];
				$ai_cosart=$row["cosart"];
				$ai_existencia=$row["existencia"];
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['as_denart']=$as_denart;
		$arrResultado['ai_unidad']=$ai_unidad;
		$arrResultado['ai_cosart']=$ai_cosart;
		$arrResultado['ai_existencia']=$ai_existencia;
		$arrResultado['as_denunimed']=$as_denunimed;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_siv_select_articulo
	//-----------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_centrocostos_almacen($as_codalm)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_centrocostos_almacen
		//         Access: public
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 05/04/2010 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_codcencos="";
		$ls_sql="SELECT codcencos".
				"  FROM siv_almacen  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codalm='".$as_codalm."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo M?TODO->uf_buscar_centrocostos_almacen ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_codcencos=$row["codcencos"];
				$this->io_sql->free_result($rs_data);
			}
		}
		return $ls_codcencos;
	}// end function uf_buscar_centrocostos_almacen
	//-----------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_produccion_almacen($as_codalm)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_produccion_almacen
		//         Access: public
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 05/04/2010 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_codcencos="";
		$ls_sql="SELECT codcencos".
				"  FROM siv_almacen  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codalm='".$as_codalm."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo M?TODO->uf_buscar_centrocostos_almacen ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_codcencos=$row["codcencos"];
				$this->io_sql->free_result($rs_data);
			}
		}
		return $ls_codcencos;
	}// end function uf_buscar_centrocostos_almacen
	//-----------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_centrocostos_articulo($as_codart)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_centrocostos_articulo
		//         Access: public
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 05/04/2010 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sc_cuentainv="";
		$ls_sql="SELECT sc_cuentainv".
				"  FROM siv_articulo  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codart='".$as_codart."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo M?TODO->uf_buscar_centrocostos_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_sc_cuentainv=$row["sc_cuentainv"];
				$this->io_sql->free_result($rs_data);
			}
		}
		return $ls_sc_cuentainv;
	}// end function uf_buscar_centrocostos_articulo
	//-----------------------------------------------------------------------------------------------------------------------------

/*	function uf_siv_insert_contable($as_numtra,$ad_fecemi,$as_codart,$as_sccuenta,$as_debhab,$ai_monto,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_dt_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp    //codigo de empresa				$as_numtra    // numero de transferencia
		//                 $ad_fecemi    // fecha de emision				$as_codart    // codigo de articulo
		//                 $ai_cosuni    // costo unitario 					$as_unidad    // unidad de medida M->Mayor D->Detal
		//                 $ai_costot    // costo total 					$ai_cantidad  // cantidad de articulos a ser transferidos
		//                 $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un detalle de una transferencia entre almacenes en la tabla de  siv_dt_transferencia
		//	   Creado Por: Ing. Luis Anibal Lang
		// Modificado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 31/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO siv_dt_transferencia_scg (codemp, codart, codcmp, feccmp, sc_cuenta, debhab, monto, estint)".
				" VALUES ('".$this->ls_codemp."','".$as_codart."','".$as_numtra."','".$ad_fecemi."','".$as_sccuenta."', ".
				"         '".$as_debhab."',".$ai_monto.",'0')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{print $this->io_sql->message;
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_insert_contable ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	} // end function uf_siv_insert_dt_transferencia
	function uf_siv_obtener_dt_contable($as_codemp,$as_numtra,$ad_fecemi,$ai_totrows,$ao_object)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_siv_obtener_dt_transferencia
	//	Access:    public
	//	Arguments:
	//  as_codemp    // codigo de empresa
	//  as_numtra    // numero de transferencia
	//  ad_fecemi    // fecha de emision
	//  ai_totrows   // total de filas encontradas
	//  ao_object    // arreglo de objetos para pintar el grid
	//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
	//	Description:  Esta funcion busca los detalles asociados a un  movimientos  en la tabla de  siv_dt_movimiento y los imprime en el grid
	//              
	//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=true;
		$ls_sql="SELECT siv_dt_transferencia_scg.*,siv_articulo.denart".
				"  FROM siv_dt_transferencia_scg,siv_articulo".
				" WHERE siv_dt_transferencia_scg.codemp='".$as_codemp."'".
				"   AND siv_dt_transferencia_scg.codcmp='".$as_numtra."'".
				"   AND siv_dt_transferencia_scg.codart=siv_articulo.codart".
				"   AND siv_dt_transferencia_scg.feccmp='".$ad_fecemi."'".
				" ORDER BY debhab";

		$li_row=$this->io_sql->select($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_obtener_dt_transferencia ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($li_row))
			{
					$ls_codart=     $row["codart"];
					$ls_denart=     $row["denart"];
					$ls_sccuenta=  $row["sc_cuenta"];
					$ls_debhab=     $row["debhab"];
					$li_monto=     $row["monto"];
					$ai_totrows=$ai_totrows+1;
					$ao_object[$ai_totrows][1]="<input  name=txtdenartc".$ai_totrows."  type=text   id=txtdenartc".$ai_totrows."  class=sin-borde size=40 maxlength=50 value='".$ls_denart."' readonly  style=text-align:left>".
												 "<input  name=txtcodartc".$ai_totrows."  type=hidden id=txtcodartc".$ai_totrows."  class=sin-borde size=30 maxlength=50 value='".$ls_codart."' readonly  style=text-align:center>";
					$ao_object[$ai_totrows][2]="<input  name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows." class=sin-borde size=20              value='".$ls_sccuenta."' readonly  style=text-align:center>";
					$ao_object[$ai_totrows][3]="<input  name=txtdebhab".$ai_totrows."   type=text   id=txtdebhab".$ai_totrows."   class=sin-borde size=10              value='".$ls_debhab."' readonly style='text-align:center'>";
					$ao_object[$ai_totrows][4]="<input  name=txtmonto".$ai_totrows."    type=text   id=txtcansolc".$ai_totrows."  class=sin-borde size=20              value='".number_format ($li_monto,2,",",".")."' style='text-align:right' readonly>";

			}//while
		}//else
		return $lb_valido;
	}
*/
	function uf_siv_delete_movimiento($as_codemp,$as_numtra,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_delete_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp    //codigo de empresa				$as_numtra    // numero de transferencia
		//                 $ad_fecemi    // fecha de emision				$aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion elimina una transferencia entre almacenes en la tabla de  siv_transferencia
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nummov=$this->uf_siv_select_movimiento($as_codemp,$as_numtra);
		if($ls_nummov!="")
		{
			$lb_valido=$this->uf_siv_delete_dt_movimiento($as_codemp,$as_numtra);
			if($lb_valido)
			{
				$ls_sql = " DELETE FROM siv_movimiento".
						  " WHERE nummov='".$ls_nummov."'".
						  "   AND fecmov='".$ad_fecemi."'";
				$this->io_sql->begin_transaction();	
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_delete_movimiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					$lb_valido=true;
				}
			}
		}
		return $lb_valido;
	} // end  function uf_siv_delete_transferencia 

	function uf_siv_delete_dt_movimiento($as_codemp,$as_numtra)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_siv_delete_dt_transferencia
	//	Access:    public
	//	Arguments:
	//  as_codemp    // codigo de empresa
	//  as_numtra    // numero de transferencia
	//  ad_fecemi    // fecha de emision
	//  as_codart    // codigo de articulo
	//  aa_seguridad // arreglo de registro de seguridad
	//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
	//	Description:  Esta funcion elimina los detalles asociados a una transferencia 
	//                entre almacenes en la tabla de  siv_dt_transferencia
	// Modificado Por: Ing. Yozelin Barragan
	// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 31/08/2007 
	//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=true;
		$ls_sql="DELETE FROM siv_dt_movimiento".
				" WHERE siv_dt_movimiento.codemp='".$as_codemp."'".
				"   AND siv_dt_movimiento.numdoc='".$as_numtra."'".
				"   AND siv_dt_movimiento.promov='PRO'";
		$li_row=$this->io_sql->select($ls_sql);
		if($li_row==false)
		{
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_delete_dt_transferencia ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	}

	function uf_siv_delete_produccion($as_codemp,$as_numtra,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_delete_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp    //codigo de empresa				$as_numtra    // numero de transferencia
		//                 $ad_fecemi    // fecha de emision				$aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Esta funcion elimina una transferencia entre almacenes en la tabla de  siv_transferencia
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_valido=$this->uf_siv_delete_dt_procuccion($as_codemp,$as_numtra);
		if($lb_valido)
		{
			$lb_valido=$this->uf_siv_delete_dt_scg($as_codemp,$as_numtra);
		}
		if($lb_valido)
		{
			$ls_sql = " DELETE FROM siv_produccion".
					  " WHERE codemp='".$as_codemp."'".
					  "   AND numpro='".$as_numtra."'";
			$this->io_sql->begin_transaction();	
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_delete_produccion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				$lb_valido=true;
			}
		}
		return $lb_valido;
	} // end  function uf_siv_delete_transferencia 

	function uf_siv_delete_dt_procuccion($as_codemp,$as_numtra)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_siv_delete_dt_transferencia
	//	Access:    public
	//	Arguments:
	//  as_codemp    // codigo de empresa
	//  as_numtra    // numero de transferencia
	//  ad_fecemi    // fecha de emision
	//  as_codart    // codigo de articulo
	//  aa_seguridad // arreglo de registro de seguridad
	//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
	//	Description:  Esta funcion elimina los detalles asociados a una transferencia 
	//                entre almacenes en la tabla de  siv_dt_transferencia
	// Modificado Por: Ing. Yozelin Barragan
	// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 31/08/2007 
	//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=true;
		$ls_sql = " DELETE FROM siv_dt_produccion".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND numpro='".$as_numtra."'";
				
		$li_row=$this->io_sql->select($ls_sql);

		if($li_row==false)
		{
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_delete_dt_transferencia ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	}

	function uf_siv_delete_dt_scg($as_codemp,$as_numtra)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_siv_delete_dt_scg
	//	Access:    public
	//	Arguments:
	//  as_codemp    // codigo de empresa
	//  as_numtra    // numero de transferencia
	//  ad_fecemi    // fecha de emision
	//  as_codart    // codigo de articulo
	//  aa_seguridad // arreglo de registro de seguridad
	//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
	//	Description:  Esta funcion elimina los detalles asociados a una transferencia 
	//                entre almacenes en la tabla de  siv_dt_transferencia
	// Modificado Por: Ing. Yozelin Barragan
	// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 31/08/2007 
	//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=true;
		$ls_sql = " DELETE FROM siv_dt_produccion_scg".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codcmp='".$as_numtra."'";
				
		$li_row=$this->io_sql->select($ls_sql);

		if($li_row==false)
		{
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_delete_dt_scg ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	}


	function uf_siv_select_movimiento($as_codemp,$as_numtra)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_siv_obtener_dt_transferencia
	//	Access:    public
	//	Arguments:
	//  as_codemp    // codigo de empresa
	//  as_numtra    // numero de transferencia
	//  ad_fecemi    // fecha de emision
	//  ai_totrows   // total de filas encontradas
	//  ao_object    // arreglo de objetos para pintar el grid
	//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
	//	Description:  Esta funcion busca los detalles asociados a un  movimientos  en la tabla de  siv_dt_movimiento y los imprime en el grid
	//              
	//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=true;
		$ls_nummov="";
		$ls_sql="SELECT nummov".
				"  FROM siv_dt_movimiento".
				" WHERE siv_dt_movimiento.codemp='".$as_codemp."'".
				"   AND siv_dt_movimiento.numdoc='".$as_numtra."'".
				"   AND siv_dt_movimiento.promov='PRO'";

		$rs_dada=$this->io_sql->select($ls_sql);
		if($rs_dada===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_obtener_dt_transferencia ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			if($row=$this->io_sql->fetch_row($rs_dada))
			{
				$ls_nummov= $row["nummov"];
			}//while
		}//else
		return $ls_nummov;
	}
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_siv_buscar_cuenta($as_codartent)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_centrocostos_almacen
		//         Access: public
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 05/04/2010 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sc_cuenta="";
		$ls_sql="SELECT sc_cuentainv".
				"  FROM siv_articulo  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codart='".$as_codartent."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->produccion M?TODO->uf_siv_buscar_cuenta ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_sc_cuenta=$row["sc_cuentainv"];
				$this->io_sql->free_result($rs_data);
			}
		}
		return $ls_sc_cuenta;
	}// end function uf_buscar_centrocostos_almacen
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_contable($as_numtra,$ad_fecemi,$as_codart,$as_sccuenta,$as_debhab,$ai_monto,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_dt_transferencia
		//         Access: public (sigesp_siv_p_transferencia)
		//      Argumento: $as_codemp    //codigo de empresa				$as_numtra    // numero de transferencia
		//                 $ad_fecemi    // fecha de emision				$as_codart    // codigo de articulo
		//                 $ai_cosuni    // costo unitario 					$as_unidad    // unidad de medida M->Mayor D->Detal
		//                 $ai_costot    // costo total 					$ai_cantidad  // cantidad de articulos a ser transferidos
		//                 $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un detalle de una transferencia entre almacenes en la tabla de  siv_dt_transferencia
		//	   Creado Por: Ing. Luis Anibal Lang
		// Modificado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 31/08/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO siv_dt_produccion_scg (codemp, codart, codcmp, feccmp, sc_cuenta, debhab, monto, estint)".
				" VALUES ('".$this->ls_codemp."','".$as_codart."','".$as_numtra."','".$ad_fecemi."','".$as_sccuenta."', ".
				"         '".$as_debhab."',".$ai_monto.",'0')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{print $this->io_sql->message;
			$this->io_msg->message("CLASE->produccion M?TODO->uf_siv_insert_contable ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	} // end function uf_siv_insert_dt_transferencia

	function uf_siv_obtener_dt_contable($as_codemp,$as_numtra,$ad_fecemi,$ai_totrows,$ao_object)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_siv_obtener_dt_transferencia
	//	Access:    public
	//	Arguments:
	//  as_codemp    // codigo de empresa
	//  as_numtra    // numero de transferencia
	//  ad_fecemi    // fecha de emision
	//  ai_totrows   // total de filas encontradas
	//  ao_object    // arreglo de objetos para pintar el grid
	//	Returns:		$lb_valido-----> true: operacion exitosa false: operacion no exitosa
	//	Description:  Esta funcion busca los detalles asociados a un  movimientos  en la tabla de  siv_dt_movimiento y los imprime en el grid
	//              
	//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=true;
		$ls_sql="SELECT siv_dt_produccion_scg.*,siv_articulo.denart".
				"  FROM siv_dt_produccion_scg,siv_articulo".
				" WHERE siv_dt_produccion_scg.codemp='".$as_codemp."'".
				"   AND siv_dt_produccion_scg.codcmp='".$as_numtra."'".
				"   AND siv_dt_produccion_scg.codart=siv_articulo.codart".
				"   AND siv_dt_produccion_scg.feccmp='".$ad_fecemi."'".
				" ORDER BY debhab";

		$li_row=$this->io_sql->select($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->transferencia M?TODO->uf_siv_obtener_dt_transferencia ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($li_row))
			{
					$ls_codart=     $row["codart"];
					$ls_denart=     $row["denart"];
					$ls_sccuenta=  $row["sc_cuenta"];
					$ls_debhab=     $row["debhab"];
					$li_monto=     $row["monto"];
					$ai_totrows=$ai_totrows+1;
					switch ($ls_debhab) 
					{
						case "D":
							$ls_debhab="DEBE";
							break;
						case "H":
							$ls_debhab="HABER";
							break;
					}
					$ao_object[$ai_totrows][1]="<input  name=txtdenartc".$ai_totrows."  type=text   id=txtdenartc".$ai_totrows."  class=sin-borde size=40 maxlength=50 value='".$ls_denart."' readonly  style=text-align:left>".
												 "<input  name=txtcodartc".$ai_totrows."  type=hidden id=txtcodartc".$ai_totrows."  class=sin-borde size=30 maxlength=50 value='".$ls_codart."' readonly  style=text-align:center>";
					$ao_object[$ai_totrows][2]="<input  name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows." class=sin-borde size=20              value='".$ls_sccuenta."' readonly  style=text-align:center>";
					$ao_object[$ai_totrows][3]="<input  name=txtdebhab".$ai_totrows."   type=text   id=txtdebhab".$ai_totrows."   class=sin-borde size=10              value='".$ls_debhab."' readonly style='text-align:center'>";
					$ao_object[$ai_totrows][4]="<input  name=txtmonto".$ai_totrows."    type=text   id=txtcansolc".$ai_totrows."  class=sin-borde size=20              value='".number_format ($li_monto,2,",",".")."' style='text-align:right' readonly>";

			}//while
		}//else
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}


} 
?>