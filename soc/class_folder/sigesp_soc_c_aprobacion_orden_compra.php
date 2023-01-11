<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_soc_c_aprobacion_orden_compra
{
//  var $io_conexion;
  public function __construct($as_path)
  {
	////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: sigesp_soc_c_aprobacion_orden_compra
	//		   Access: public 
	//	  Description: Constructor de la Clase
	//	   Creado Por: Ing. Néstor Falcón.
	// Fecha Creación: 29/05/2007 								Fecha Última Modificación : 29/05/2007 
	////////////////////////////////////////////////////////////////////////////////////////////////////
        require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_include			= new sigesp_include();
		$this->io_conexion	= $io_include->uf_conectar();
		$this->io_sql       = new class_sql($this->io_conexion);	
		$this->io_mensajes  = new class_mensajes();		
		$this->io_funciones = new class_funciones();	
		$this->io_seguridad = new sigesp_c_seguridad();
		$this->ls_codemp    = $_SESSION["la_empresa"]["codemp"];
		$this->ls_path = $as_path;
  }

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_ordenes_compra($as_numordcom,$as_codpro,$ad_fecdes,$ad_fechas,$as_tipordcom,$as_tipope)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_ordenes_compra
		//		   Access: public
		//		 Argument: 
		//   $as_numordcom //Número de la Orden de Compra.
		//      $as_codpro //Código del Proveedor asociado a la Orden de Compra.
		//      $ad_fecdes //Fecha a partir del cual comenzará la búsqueda de las Ordenes de Compra. 
		//      $ad_fechas //Fecha hasta el cual comenzará la búsqueda de las Ordenes de Compra. 
		//   $as_tipordcom //Tipo de la Orden de Compra B=Bienes , S=Servicios.
		//      $as_tipope //Tipo de la Operación a ejecutar A=Aprobacion, R=Reverso de la Aprobación.
		//	  Description: Función que busca las ordenes de compra que esten dispuestas para Aprobacion/Reverso.
		//	   Creado Por: Ing. Nestor Falcon.
		// Fecha Creación: 16/05/2007								Fecha Última Modificación : 16/05/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
        $ls_straux = "";
		
        if (!empty($as_numordcom))
		   {
		     $ls_straux = " AND soc_ordencompra.numordcom LIKE '%".$as_numordcom."%'";
		   } 
		if (!empty($as_codpro))
		   {  
		     $ls_straux = $ls_straux." AND soc_ordencompra.cod_pro LIKE '%".$as_codpro."%'";
		   }
		if (!empty($ad_fecdes) && !empty($ad_fechas))
		   {  
		     $ld_fecdes = $this->io_funciones->uf_convertirdatetobd($ad_fecdes);
			 $ld_fechas = $this->io_funciones->uf_convertirdatetobd($ad_fechas);
			 $ls_straux = $ls_straux." AND soc_ordencompra.fecordcom BETWEEN '".$ld_fecdes."' AND '".$ld_fechas."'";
		   }
		if ($as_tipordcom!='-')
		   {  
		     $ls_straux = $ls_straux." AND soc_ordencompra.estcondat='".$as_tipordcom."'";
		   }
		if ($as_tipope=='A')//Aprobacion
		   {  
		     $ls_straux = $ls_straux." AND soc_ordencompra.estapro='0'";
		   }
		elseif($as_tipope=='R')//Reverso.
		   {
			 $ls_straux = $ls_straux." AND soc_ordencompra.estapro='1'";
		   }
		   //FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('soc_ordencompra.codestpro1','soc_ordencompra.codestpro2','soc_ordencompra.codestpro3','soc_ordencompra.codestpro4','soc_ordencompra.codestpro5','soc_ordencompra.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos 
			                   							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' 
			                     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) 
			                  AND soc_ordencompra.coduniadm IN (SELECT codintper FROM sss_permisos_internos 
			                  							        WHERE sss_permisos_internos.codemp='{$this->ls_codemp}'".
				"                                				  AND codsis='SOC'".
				"                                                 AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql ="SELECT soc_ordencompra.numordcom,soc_ordencompra.fecordcom,".
		         "       soc_ordencompra.estcondat,soc_ordencompra.montot,   ".
				 "       soc_ordencompra.obscom,soc_ordencompra.fecaprord,   ".
				 "       soc_ordencompra.cod_pro,rpc_proveedor.nompro        ".
				 "  FROM soc_ordencompra, rpc_proveedor                      ".
		         " WHERE soc_ordencompra.codemp='".$this->ls_codemp."'       ".
				 "   AND soc_ordencompra.numordcom<>'000000000000000'  		 ".
				 "   AND soc_ordencompra.estcom='1'                          ".
				 "   $ls_straux											     ".
				 "   AND rpc_proveedor.codemp=soc_ordencompra.codemp   		 ".
				 "   AND rpc_proveedor.cod_pro=soc_ordencompra.cod_pro 		 ".$ls_filtroest.
				 " ORDER BY soc_ordencompra.numordcom ASC              		 ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_orden_compra.php->MÉTODO->uf_load_ordenes_compra.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_ordenes_compra
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($ai_totrows,$as_tipope,$ad_fecope,$aa_seguridad)
	{
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_guardar
	//		   Access: public
	//		 Argument: 
	//     $ai_totrows //Total de elementos cargados en el Grid de la Ordenes de Compra.
	//      $as_tipope //Tipo de la Operación a realizar A=Aprobación, R=Reverso de Aprobación.
	//      $ad_fecope //Fecha en la cual se ejecuta la Operación.
	//   $aa_seguridad //Arreglo de seguridad cargado de la informacion de usuario y pantalla.
	//	  Description: Función que recorre el grid de las ordenes de compra que esten dispuestas para Aprobacion/Reverso.
	//	   Creado Por: Ing. Nestor Falcon.
	// Fecha Creación: 16/05/2007								Fecha Última Modificación : 16/05/2007
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
		$lb_valido = true;
		$this->io_sql->begin_transaction();
		for ($i=1;$i<=$ai_totrows;$i++)
		{
			if (array_key_exists("chk".$i,$_POST))
			{
				$ls_numordcom = $_POST["txtnumord".$i];
				$ls_tipordcom = $_POST["txttipordcom".$i];
				$ls_codpro    = $_POST["hidcodpro".$i];
				$lb_valido=true;
				if($as_tipope=='A') // en el caso que se este aprobando valido la disponibilidad
				{
					$lb_valido=$this->uf_validar_cuentas($ls_numordcom,$ls_tipordcom);
				}
				if($lb_valido)
				{
					$lb_valido    = $this->uf_update_estatus_aprobacion($ls_numordcom,$ls_codpro,$as_tipope,$ls_tipordcom,$ad_fecope,$aa_seguridad);
					if (!$lb_valido)
					{
						break;
					}
				}
				else
				{
					break;
				}
			}
		}
		if ($lb_valido)
		{
			$this->io_sql->commit();
			$this->io_mensajes->message("Operación realizada con Éxito !!!");
			$this->io_sql->close();
		}
		else 
		{
			$this->io_sql->rollback();
			$this->io_mensajes->message("Error Operación !!!");
			$this->io_sql->close();
		}
	}// end function uf_guardar

	function uf_update_estatus_aprobacion($as_numordcom,$as_codpro,$as_tipope,$as_tipordcom,$ad_fecope,$aa_seguridad)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_update_estatus_aprobacion
	//		   Access: public
	//		 Argument: 
	//   $as_numordcom //Número de la Orden de Compra.
	//      $as_codpro //Código del Proveedor asociado a la Orden de Compra.
	//      $as_tipope //Tipo de la Operación a ejecutar A=Aprobacion, R=Reverso de la Aprobación.
	//   $as_tipordcom //Tipo de la Orden de Compra B=Bienes , S=Servicios.
	//      $ad_fecope //Fecha en la cual se ejecuta la Operación.
	//   $aa_seguridad //Arreglo de seguridad cargado de la informacion de usuario y pantalla.
	//	  Description: Función que recorre el grid de las ordenes de compra que esten dispuestas para Aprobacion/Reverso.
	//	   Creado Por: Ing. Nestor Falcon.
	// Fecha Creación: 02/06/2007								Fecha Última Modificación : 02/06/2007
	//////////////////////////////////////////////////////////////////////////////
	
  	  $lb_valido    = true;
	  $ls_tipordcom = "";
	  if ($as_tipope=='A')
		 {
		   $li_aprest = 1;//Colocar en Aprobada
		   $li_estapr = 0;//Cuando este en No Aprobada.
		   $ad_fecope = $this->io_funciones->uf_convertirdatetobd($ad_fecope);
		   $ls_nomusu = $aa_seguridad["logusr"];
		 }
	  elseif($as_tipope=='R')
		 {
		   $ad_fecope = '1900-01-01';
		   $li_aprest = 0;//Colocar en No Aprobada.
		   $li_estapr = 1;//Cuando este Aprobada.
		   $ls_nomusu = '';
		 }
	  if ($as_tipordcom=='Bienes')
		 {
		   $ls_tipordcom = 'B';
		 }
	  elseif($as_tipordcom=='Servicios')
		 {
		   $ls_tipordcom = 'S';
		 }
	  
	  $ls_sql    = "UPDATE soc_ordencompra
					   SET estapro='".$li_aprest."', fecaprord='".$ad_fecope."', codusuapr = '".$ls_nomusu."'
					 WHERE codemp='".$this->ls_codemp."'
					   AND numordcom='".$as_numordcom."'
					   AND cod_pro='".$as_codpro."'
					   AND estcondat='".$ls_tipordcom."'
					   AND estapro='".$li_estapr."'
					   AND estcom='1'";//print $ls_sql;
	  $rs_data   = $this->io_sql->execute($ls_sql);
	  if ($rs_data===false)
		 {
		   $lb_valido=false;
		   $this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_orden_compra; METODO->uf_update_estatus_aprobacion;ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));	
		 }
	  else
		 {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Actualizó el Estatus de la Orden de Compra ".$as_numordcom." en ".$li_aprest." del proveedor ".$as_codpro." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			 $this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
			if($this->ls_supervisor!=0)
			{
				$ls_fromname="Orden de Compra";
				$ls_bodyenv="Se le envia la notificación de actualización en el modulo de SOC, se aprobó la orden de compra  N°.. ";
				$ls_nomper=$_SESSION["la_nomusu"];
				$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$as_numordcom,$ls_bodyenv,$ls_nomper);
			}
			/////////////////////////////////         SEGURIDAD               /////////////////////////////			
		 }
	  return $lb_valido;
	}// end function uf_update_estatus_aprobacion


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_cuentas($as_numordcom,$as_tipordcom)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_cuentas
		//		   Access: private
		//		 Argument: as_numordcom // Número de orden de compras
		//		 		   as_tipordcom // tipo si es de bienes ó servicios
		//	  Description: Función que busca que las cuentas presupuestarias estén en la programática seleccionada
		//				   de ser asi puede aprobar la soc de lo contrario no la apruebas
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 09/04/2010								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		require_once($this->ls_path."base/librerias/php/general/sigesp_lib_fecha.php");
		require_once($this->ls_path."shared/class_folder/class_sigesp_int.php");
		require_once($this->ls_path."shared/class_folder/class_sigesp_int_int.php");
		require_once($this->ls_path."shared/class_folder/class_sigesp_int_scg.php");
		require_once($this->ls_path."shared/class_folder/class_sigesp_int_spg.php");
		$io_int_spg=new class_sigesp_int_spg();
		$as_estcondat ='';
		if ($as_tipordcom=='Bienes')
		{
			$as_estcondat = 'B';
		}
		elseif($as_tipordcom=='Servicios')
		{
			$as_estcondat = 'S';
		}

		$ls_sql="SELECT soc_cuentagasto.codestpro1, soc_cuentagasto.codestpro2, soc_cuentagasto.codestpro3, soc_cuentagasto.codestpro4, ".
				"		soc_cuentagasto.codestpro5, soc_cuentagasto.estcla, soc_cuentagasto.spg_cuenta, soc_cuentagasto.monto, soc_ordencompra.fecordcom, ".
				"		(SELECT COUNT(codemp) ".
				"		   FROM spg_cuentas ".
				"		  WHERE spg_cuentas.codemp = soc_cuentagasto.codemp ".
				"			AND spg_cuentas.codestpro1 = soc_cuentagasto.codestpro1 ".
				"		    AND spg_cuentas.codestpro2 = soc_cuentagasto.codestpro2 ".
				"		    AND spg_cuentas.codestpro3 = soc_cuentagasto.codestpro3 ".
				"		    AND spg_cuentas.codestpro4 = soc_cuentagasto.codestpro4 ".
				"		    AND spg_cuentas.codestpro5 = soc_cuentagasto.codestpro5 ".
				"		    AND spg_cuentas.estcla = soc_cuentagasto.estcla ".
				"			AND spg_cuentas.spg_cuenta = soc_cuentagasto.spg_cuenta) AS existe ".		
				"  FROM soc_cuentagasto ".
				" INNER JOIN soc_ordencompra  ".
				"    ON soc_cuentagasto.codemp='".$this->ls_codemp."' ".
				"   AND soc_cuentagasto.numordcom='".$as_numordcom."'".
				"   AND soc_cuentagasto.estcondat='".$as_estcondat."'".
				"   AND soc_cuentagasto.codemp=soc_ordencompra.codemp".
				"   AND soc_cuentagasto.numordcom=soc_ordencompra.numordcom".
				"   AND soc_cuentagasto.estcondat=soc_ordencompra.estcondat";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_cuentas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$ls_estaprsoc=$_SESSION["la_empresa"]["estaprsoc"];
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$li_existe=$rs_data->fields["existe"];
				$ls_cuenta=$rs_data->fields["spg_cuenta"];
				$ls_codestpro1=substr($rs_data->fields["codestpro1"],(25-$_SESSION["la_empresa"]["loncodestpro1"]),$_SESSION["la_empresa"]["loncodestpro1"]);
				$ls_codestpro2=substr($rs_data->fields["codestpro2"],(25-$_SESSION["la_empresa"]["loncodestpro2"]),$_SESSION["la_empresa"]["loncodestpro2"]);
				$ls_codestpro3=substr($rs_data->fields["codestpro3"],(25-$_SESSION["la_empresa"]["loncodestpro3"]),$_SESSION["la_empresa"]["loncodestpro3"]);
				$ls_codestpro4=substr($rs_data->fields["codestpro4"],(25-$_SESSION["la_empresa"]["loncodestpro4"]),$_SESSION["la_empresa"]["loncodestpro4"]);
				$ls_codestpro5=substr($rs_data->fields["codestpro5"],(25-$_SESSION["la_empresa"]["loncodestpro5"]),$_SESSION["la_empresa"]["loncodestpro5"]);
				$ls_estcla=$rs_data->fields["estcla"];
				$_SESSION["fechacomprobante"]=$rs_data->fields["fecordcom"];
				if($li_existe>0)
				{
					$ls_estprog[0]=$rs_data->fields["codestpro1"];
					$ls_estprog[1]=$rs_data->fields["codestpro2"];
					$ls_estprog[2]=$rs_data->fields["codestpro3"];
					$ls_estprog[3]=$rs_data->fields["codestpro4"];
					$ls_estprog[4]=$rs_data->fields["codestpro5"];
					$ls_estprog[5]=$rs_data->fields["estcla"];
					$ls_vali_nivel=$_SESSION["la_empresa"]["vali_nivel"];
					if($ls_vali_nivel==5)
					{
						$ls_formpre=str_replace("-","",$_SESSION["la_empresa"]["formpre"]);
						$ls_vali_nivel=$io_int_spg->uf_spg_obtener_nivel($ls_formpre);
					}
					if($_SESSION["la_empresa"]["estvaldis"]==0)
					{
						$ls_vali_nivel=0;
					}
					$li_nivel=$io_int_spg->uf_spg_obtener_nivel($ls_cuenta);
					if ($li_nivel <= $ls_vali_nivel)
					{
						$ls_status="";
						$li_asignado=0;
						$li_aumento=0;
						$li_disminucion=0;
						$li_precomprometido=0;
						$li_comprometido=0;
						$li_causado=0;
						$li_pagado=0;
						$arrResultado = "";
						$arrResultado = $io_int_spg->uf_spg_saldo_select($this->ls_codemp,$ls_estprog,$ls_cuenta,$ls_status,$li_asignado,$li_aumento,$li_disminucion,
																	 $li_precomprometido,$li_comprometido,$li_causado,$li_pagado,'ACTUAL');
						$ls_status = $arrResultado['as_status'];
						$li_asignado = $arrResultado['adec_asignado'];
						$li_aumento = $arrResultado['adec_aumento'];
						$li_disminucion = $arrResultado['adec_disminucion'];
						$li_precomprometido = $arrResultado['adec_precomprometido'];
						$li_comprometido = $arrResultado['adec_comprometido'];
						$li_causado = $arrResultado['adec_causado'];
						$li_pagado = $arrResultado['adec_pagado'];
						$lb_valido = $arrResultado['lb_valido'];

						$li_disponibilidad=(($li_asignado + $li_aumento) - ( $li_disminucion + $li_comprometido + $li_precomprometido));
						$arrResultado="";
						$arrResultado=$this->uf_verificar_precompromiso($as_numordcom,$as_estcondat,$ls_estprog,$ls_cuenta,$as_montocuentasep);
						$as_montocuentasep = $arrResultado['as_montocuentasep'];
						$lb_valido=$arrResultado['lb_valido'];						
						if($lb_valido)
						{
							$li_disponibilidad=$li_disponibilidad+$as_montocuentasep;
						}
						if(round($rs_data->fields["monto"],2) > round($li_disponibilidad,2))
						{
							$li_monto=number_format($rs_data->fields["monto"],2,",",".");
							$li_disponibilidad=number_format($li_disponibilidad,2,",",".");
							$this->io_mensajes->message("No hay Disponibilidad en la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." Disponible=[".$li_disponibilidad."] Cuenta=[".$li_monto."]"); 
							if($ls_estaprsoc!="1")
							{
								$lb_valido=false;
							}
						}
						elseif (round($rs_data->fields["monto"],2) == round($li_disponibilidad,2)){
							$this->io_mensajes->message("El saldo de la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." quedara en 0");
						}
						if($lb_valido)
						{
							$ls_status="";
							$li_asignado=0;
							$li_aumento=0;
							$li_disminucion=0;
							$li_precomprometido=0;
							$li_comprometido=0;
							$li_causado=0;
							$li_pagado=0;
							$arrResultado = "";
							$arrResultado = $io_int_spg->uf_spg_saldo_select($this->ls_codemp,$ls_estprog,$ls_cuenta,$ls_status,$li_asignado,$li_aumento,$li_disminucion,
																			 $li_precomprometido,$li_comprometido,$li_causado,$li_pagado,'COMPROBANTE');
							$ls_status = $arrResultado['as_status'];
							$li_asignado = $arrResultado['adec_asignado'];
							$li_aumento = $arrResultado['adec_aumento'];
							$li_disminucion = $arrResultado['adec_disminucion'];
							$li_precomprometido = $arrResultado['adec_precomprometido'];
							$li_comprometido = $arrResultado['adec_comprometido'];
							$li_causado = $arrResultado['adec_causado'];
							$li_pagado = $arrResultado['adec_pagado'];
							$lb_valido = $arrResultado['lb_valido'];
							$li_disponibilidad=(($li_asignado + $li_aumento) - ( $li_disminucion + $li_comprometido + $li_precomprometido));
							$arrResultado="";
							$arrResultado=$this->uf_verificar_precompromiso($as_numordcom,$as_estcondat,$ls_estprog,$ls_cuenta,$as_montocuentasep);
							$as_montocuentasep = $arrResultado['as_montocuentasep'];
							$lb_valido=$arrResultado['lb_valido'];						
							if($lb_valido)
							{
								$li_disponibilidad=$li_disponibilidad+$as_montocuentasep;
							}
							if(round($rs_data->fields["monto"],2) > round($li_disponibilidad,2))
							{
								$li_monto=number_format($rs_data->fields["monto"],2,",",".");
								$li_disponibilidad=number_format($li_disponibilidad,2,",",".");
								$this->io_mensajes->message("No hay Disponibilidad en la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." Disponible=[".$li_disponibilidad."] Cuenta=[".$li_monto."]"); 
								if($ls_estaprsoc!="1")
								{
									$lb_valido=false;
								}
							}
							elseif (round($rs_data->fields["monto"],2) == round($li_disponibilidad,2)){
								$this->io_mensajes->message("El saldo de la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." quedara en 0");
							}
						}				
					} 	
				}
				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("La cuenta ".$ls_cuenta." No Existe en la Estructura ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla.""); 
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_validar_cuentas
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_verificar_precompromiso($as_numordcom,$as_estcondat,$as_estprog,$as_cuenta,$as_montocuentasep)
	{
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_precompromiso
		//         Access: private
		//     Argumentos: as_numordcom // numero de la orden de compra
		//                 as_estcondat // tipo de orden de compra (B=Bienes,S=Servicio,-=Ambos)		
		//                 adt_fecha // fecha de contabilizacion      
		//				   aa_seguridad // Arreglo de las variables de seguridad
		//	      Returns: Retorna un boleano 
		//	  Description: Este metodo tiene como fin reversar el precompromiso generado por la solicitud sep 
		//                 en el sistema de gastos.          
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 21/12/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_montocuentasep=0; 
		$ls_sql="SELECT soc_enlace_sep.numsol, sep_solicitud.fechaconta ".
                "  FROM sep_solicitud , soc_enlace_sep ".
                " WHERE soc_enlace_sep.codemp='".$this->ls_codemp."' ".
				"   AND soc_enlace_sep.numordcom='".$as_numordcom."' ".
				"   AND soc_enlace_sep.estcondat='".$as_estcondat."' ".
				"   AND sep_solicitud.estsol='P'".
				"   AND sep_solicitud.codemp=soc_enlace_sep.codemp ".
				"   AND sep_solicitud.numsol=soc_enlace_sep.numsol ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integración SOC MÉTODO->uf_reversar_en_gasto_solicitud_presupuestaria ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
			while($row=$this->io_sql->fetch_row($rs_data)and($lb_valido))
			{
				$ls_numsol=$row["numsol"];
				$arrResultado="";
				$arrResultado=$this->uf_obtener_montocuentasep($ls_numsol,$as_estprog,$as_cuenta,$ls_monto);
				$ls_monto = $arrResultado['as_monto'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_montocuentasep= $as_montocuentasep+$ls_monto;
				}
			}
		}
		$arrResultado=array();
		$arrResultado['as_montocuentasep']=$as_montocuentasep;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_obtener_montocuentasep($as_numsol,$as_estprog,$as_cuenta,$as_monto)
	{
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_montocuentasep
		//         Access: private
		//     Argumentos: as_numordcom // numero de la orden de compra
		//                 as_estcondat // tipo de orden de compra (B=Bienes,S=Servicio,-=Ambos)		
		//                 adt_fecha // fecha de contabilizacion      
		//				   aa_seguridad // Arreglo de las variables de seguridad
		//	      Returns: Retorna un boleano 
		//	  Description: Este metodo tiene como fin reversar el precompromiso generado por la solicitud sep 
		//                 en el sistema de gastos.          
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 21/12/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_codestpro1=$as_estprog[0];
		$ls_codestpro2=$as_estprog[1];
		$ls_codestpro3=$as_estprog[2];
		$ls_codestpro4=$as_estprog[3];
		$ls_codestpro5=$as_estprog[4];
		$ls_estcla=$as_estprog[5];
		$as_monto=0;
		$ls_sql="SELECT monto ".
                "  FROM sep_cuentagasto ".
                " WHERE codemp='".$this->ls_codemp."' ".
				"   AND numsol='".$as_numsol."' ".
				"   AND codestpro1='".$ls_codestpro1."' ".
				"   AND codestpro2='".$ls_codestpro2."' ".
				"   AND codestpro3='".$ls_codestpro3."' ".
				"   AND codestpro4='".$ls_codestpro4."' ".
				"   AND codestpro5='".$ls_codestpro5."' ".
				"   AND estcla='".$ls_estcla."' ".
				"   AND spg_cuenta='".$as_cuenta."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Integración SOC MÉTODO->uf_reversar_en_gasto_solicitud_presupuestaria ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
			if($row=$this->io_sql->fetch_row($rs_data)and($lb_valido))
			{
				$as_monto=$row["monto"];
			}
		}
		$arrResultado['as_monto']=$as_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------	
function uf_nivel_aprobacion_usu($as_codusu,$as_codtipniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$as_codniv="";
		$ls_sql="SELECT codasiniv ".
				"  FROM sss_niv_usuarios ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codusu='".$as_codusu."' ".
				"   AND codtipniv='".$as_codtipniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php->uf_nivel_aprobacion_usu ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_codniv=$row["codasiniv"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $as_codniv;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_nivel_aprobacion_montohasta($as_codniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ai_monhas=0;
		$ls_sql="SELECT monnivhas ".
				"  FROM sigesp_nivel ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codniv='".$as_codniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php-> ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_monhas=$row["monnivhas"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $ai_monhas;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_nivel($as_codniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$as_nivel="";
		$ls_sql="SELECT codniv ".
				"  FROM sigesp_asig_nivel ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codasiniv='".$as_codniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php-> ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_nivel=$row["codniv"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $as_nivel;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------

}
?>