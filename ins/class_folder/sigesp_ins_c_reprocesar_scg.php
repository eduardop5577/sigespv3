<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_ins_c_reprocesar_scg
{
	var $io_sql;
	var $io_message;
	var $io_function;
	var $is_msg_error;
	
	public function __construct()
	{
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");//Necesaria para la clase integradora
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");	
		require_once("../shared/class_folder/class_sigesp_int.php");	//Necesaria para la clase integradora
		require_once("../shared/class_folder/class_sigesp_int_scg.php");	
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$io_siginc=new sigesp_include();
		$con=$io_siginc->uf_conectar();
		$this->io_sql=new class_sql($con);
		$this->io_message=new class_mensajes();
		$this->io_function=new class_funciones();
		$this->io_seguridad=new sigesp_c_seguridad();		
	}//end sigesp_ins_c_reprocesar_scg
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_redondear_monto_detalles()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////
		// 	 Function:  uf_redondear_monto_detalles
		// 	   Access:  public
		//  Arguments:  
		//	  Returns:  Boolean
		//Description:  Este m?todo redondea a dos cifras el monto de cada detalle de comprobante
		////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_cadena="";
		switch(strtoupper($_SESSION["ls_gestor"]))
		{
			case "MYSQLT":
				$ls_cadena=" ROUND(monto,2) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND(monto,2) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST(monto AS NUMERIC),2) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(monto,2) ";
				break;					
			case "OCI8PO":
				$ls_cadena=" ROUND(monto,2) ";
				break;					
		}
		$ls_sql="UPDATE scg_dt_cmp 	SET monto=".$ls_cadena;		
		$li_numrow=$this->io_sql->execute($ls_sql);
		if($li_numrow===false)
		{
            $this->io_message->message("CLASE->Reprocesar SCG M?TODO->uf_redondear_monto_detalles ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_redondear_monto_saldos()
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////
		// 	 Function:  uf_redondear_monto_saldos
		// 	   Access:  public
		//  Arguments:  
		//	  Returns:  Boolean
		//Description:  Este m?todo redondea a dos cifras el monto debe y haber de cada cuenta en la tabla saldos
		////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_cadenadebe="";
		$ls_cadenahaber="";
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadenadebe=" ROUND(debe_mes,2) ";
				$ls_cadenahaber=" ROUND(haber_mes,2) ";
				break;
			case "MYSQLI":
				$ls_cadenadebe=" ROUND(debe_mes,2) ";
				$ls_cadenahaber=" ROUND(haber_mes,2) ";
				break;
			case "POSTGRES":
				$ls_cadenadebe=" ROUND(CAST(debe_mes AS NUMERIC),2)";
				$ls_cadenahaber=" ROUND(CAST(haber_mes AS NUMERIC),2)";
				break;					
			case "INFORMIX":
				$ls_cadenadebe=" ROUND(debe_mes,2) ";
				$ls_cadenahaber=" ROUND(haber_mes,2) ";
				break;					
			case "OCI8PO":
				$ls_cadenadebe=" ROUND(debe_mes,2) ";
				$ls_cadenahaber=" ROUND(haber_mes,2) ";
				break;
		}
		$ls_sql=" UPDATE scg_saldos SET debe_mes=".$ls_cadenadebe.", haber_mes=".$ls_cadenahaber;		
		$li_numrow=$this->io_sql->execute($ls_sql);
		if($li_numrow===false)
		{
            $this->io_message->message("CLASE->Reprocesar SCG M?TODO->uf_redondear_monto_detalles ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reprocesar_saldos($ls_codemp,$aa_seguridad)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////
		// 	 Function:  uf_reprocesar_saldos
		// 	   Access:  public
		//  Arguments:  
		//	  Returns:  Boolean
		//Description:  Este m?todo realiza la actualizaci?n de los saldos de las cuentas contables 
		//				segun los movimientos realizado en base a las mismas.
		////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$int_scg=new class_sigesp_int_scg();
		$int_scg->is_codemp=$ls_codemp;
		$this->uf_redondear_monto_detalles();
		$this->io_sql->begin_transaction();
		//Actualizo los saldos de las cuentas a 0 
		$ls_sql="UPDATE scg_saldos ".
				"   SET debe_mes=0,haber_mes=0 ".
				" WHERE codemp ='".$ls_codemp."'";
		$li_numrow=$this->io_sql->execute($ls_sql);
		if($li_numrow===false)
		{
            $this->io_message->message("CLASE->Reprocesar SCG M?TODO->uf_reprocesar_saldos ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}		
		if($lb_valido)
		{		
			//Obtengo los movimientos contables realizados
			$ls_sql="SELECT debhab,SUM(monto) AS monto,fecha,sc_cuenta ".
					"  FROM scg_dt_cmp ".
					" WHERE codemp ='".$ls_codemp."'".
					" GROUP BY sc_cuenta,fecha,debhab ".
					" ORDER BY sc_cuenta,fecha,debhab ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_message->message("CLASE->Reprocesar SCG M?TODO->uf_reprocesar_saldos ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
			else
			{
				$debe=0;
				$haber=0;
				while((!$rs_data->EOF)&&($lb_valido))
				{
					$ls_debhab=$rs_data->fields["debhab"];
					$ldec_monto=number_format($rs_data->fields["monto"],2,'.','');
					$ld_fecha=$rs_data->fields["fecha"];
					$int_scg->id_fecha=$ld_fecha;
					$ls_cuenta=$rs_data->fields["sc_cuenta"];
					$lb_valido=$int_scg->uf_scg_procesar_saldos_contables($ls_cuenta,$ls_debhab,0, $ldec_monto );
					if(!$lb_valido)
					{					
						$this->io_message->message("ERROR EN LA CUENTA->".$ls_cuenta);			
						break;					
						$lb_valido=false;
					}
					$rs_data->MoveNext();
				}
			}
		}	
		if($lb_valido)
		{
			$this->uf_redondear_monto_saldos();			
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reproceso los Saldos de Contabilidad";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		if($lb_valido)
		{
			$this->io_sql->commit(); 
		}
		else
		{
			$this->io_sql->rollback();
		}
		return $lb_valido;	
	}//end uf_reprocesar_saldos
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reprocesar_niveles($ls_codemp,$aa_seguridad)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////
		// 	 Function:  uf_reprocesar_niveles
		// 	   Access:  public
		//  Arguments:  
		//	  Returns:  Boolean
		//Description:  Este m?todo realiza la actualizaci?n de los niveles de las cuentas contables 
		////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$int_scg=new class_sigesp_int_scg();
		$int_scg->is_codemp=$ls_codemp;
		$this->io_sql->begin_transaction();
		//Actualizo los saldos de las cuentas a 0 
		$ls_sql="UPDATE scg_cuentas ".
				"   SET nivel=0 ".
				" WHERE codemp ='".$ls_codemp."'";
		$li_numrow=$this->io_sql->execute($ls_sql);
		if($li_numrow===false)
		{
            $this->io_message->message("CLASE->Reprocesar SCG M?TODO->uf_reprocesar_niveles ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}		
		if($lb_valido)
		{		
			//Obtengo los movimientos contables realizados
			$ls_sql="SELECT sc_cuenta ".
					"  FROM scg_cuentas ".
					" WHERE codemp ='".$ls_codemp."'".
					" ORDER BY sc_cuenta ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_message->message("CLASE->Reprocesar SCG M?TODO->uf_reprocesar_saldos ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
				$lb_valido=false;
			}
			else
			{
				while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
				{
					$ls_cuenta=$row["sc_cuenta"];
					$li_nivel=$int_scg->uf_scg_obtener_nivel($ls_cuenta);
					$ls_referencia="";
					if($li_nivel>1)
					{
						$ls_referencia=$int_scg->uf_scg_next_cuenta_nivel($ls_cuenta);
					}
					$ls_sql="UPDATE scg_cuentas ".
							"   SET nivel = ".$li_nivel.", ".
							"       referencia = '".$ls_referencia."' ".
							" WHERE codemp ='".$ls_codemp."'".
							"   AND sc_cuenta = '".$ls_cuenta."' ";
					$li_numrow=$this->io_sql->execute($ls_sql);
					if($li_numrow===false)
					{
						$this->io_message->message("CLASE->Reprocesar SCG M?TODO->uf_reprocesar_niveles ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
						$lb_valido=false;
					}		
					if(!$lb_valido)
					{					
						break;					
						$lb_valido=false;
					}
				}
			}
		}	
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reproceso los Niveles de Contabilidad";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		if($lb_valido)
		{
			$this->io_sql->commit(); 
		}
		else
		{
			$this->io_sql->rollback();
		}
		return $lb_valido;	
	}//end uf_reprocesar_niveles
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reprocesar_plancuenta($ls_codemp,$aa_seguridad)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////
		// 	 Function:  uf_reprocesar_plancuenta
		// 	   Access:  public
		//  Arguments:  
		//	  Returns:  Boolean
		//Description:  Este m?todo realiza la actualizaci?n del plan de cuenta con cuentas contables que no existan
		////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$int_scg=new class_sigesp_int_scg();
		$int_scg->is_codemp=$ls_codemp;
		$this->io_sql->begin_transaction();
		$ls_mensaje="";
		//Obtengo los movimientos contables realizados
		$ls_sql="SELECT sc_cuenta ".
				"  FROM scg_cuentas ".
				" WHERE codemp ='".$ls_codemp."'".
				"   AND status ='C'".
				" ORDER BY sc_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_message->message("CLASE->Reprocesar SCG M?TODO->uf_reprocesar_saldos ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		else
		{
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_cuenta=$rs_data->fields["sc_cuenta"];
				$ls_nextCuenta=$ls_cuenta;
				$li_nivel=$int_scg->uf_scg_obtener_nivel($ls_nextCuenta);
				do 
				{
					$ls_status='';
					$ls_denominacion='';
					$arrResultado = $int_scg->uf_scg_select_cuenta($ls_codemp,$ls_nextCuenta,$ls_status,$ls_denominacion);
					$ls_status=$arrResultado['as_status'];
					$ls_denominacion = $arrResultado['as_denominacion'];
					$lb_existe = $arrResultado['lb_existe'];
					if ($lb_existe) 
					{
						$ls_denominacionant=$ls_denominacion;
					}
					else
					{
						$li_nivel=($int_scg->uf_scg_obtener_nivel($ls_nextCuenta));
						$ls_referencia=$int_scg->uf_scg_next_cuenta_nivel($ls_nextCuenta);
						$ls_mensaje = $ls_mensaje.' CUENTA '.$ls_nextCuenta.'  -  NIVEL '.$li_nivel.' - REFERENCIA '.$ls_referencia.'\n';
						$lb_valido=$int_scg->uf_scg_insert_cuenta($ls_codemp,$ls_nextCuenta,$ls_denominacionant,'S',$li_nivel,$ls_referencia,'');
					}
					if($int_scg->uf_scg_obtener_nivel($ls_nextCuenta)==0)
					{
						break;
					}
					$ls_nextCuenta=$int_scg->uf_scg_next_cuenta_nivel($ls_nextCuenta);
					if($ls_nextCuenta!="")
					{
						$li_nivel=($int_scg->uf_scg_obtener_nivel($ls_nextCuenta));
					}
				}while(($li_nivel>=1)&&($lb_valido)&&($ls_nextCuenta!=""));
				$rs_data->MoveNext();
			}
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Reproceso el plan de cuentas Contable";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		if($lb_valido)
		{
			$this->io_sql->commit(); 
		}
		else
		{
			$this->io_sql->rollback();
		}
		if($ls_mensaje!="")
		{
			$ls_mensaje=' SE INSERTARON LAS SIGUIENTES CUENTAS CONTABLES  \n\n  '.$ls_mensaje;
			$this->io_message->message($ls_mensaje);
		}
		else
		{
			$ls_mensaje=' EL PLAN DE CUENTAS ESTA CORRECTO  \n\n  '.$ls_mensaje;
			$this->io_message->message($ls_mensaje);
		}
		return $lb_valido;	
	}//end uf_reprocesar_niveles
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>