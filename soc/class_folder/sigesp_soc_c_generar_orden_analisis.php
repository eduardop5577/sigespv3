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

class sigesp_soc_c_generar_orden_analisis
{
  public function __construct($as_path)
  {
	////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: sigesp_soc_c_generar_orden_analisis
	//		   Access: public 
	//	  Description: Constructor de la Clase
	//	   Creado Por: Ing. Laura Cabré
	// Fecha Creación: 05/08/2007 								Fecha Última Modificación : 29/05/2007 
	////////////////////////////////////////////////////////////////////////////////////////////////////
        require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_datastore.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_datastore.php");
		require_once($as_path."shared/class_folder/sigesp_c_generar_consecutivo.php");
        require_once($as_path."shared/class_folder/evaluate_formula.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_fecha.php");
		$io_include			= new sigesp_include();
		$io_conexion		= $io_include->uf_conectar();
		//$io_conexion->debug = true;
		$this->io_sql       = new class_sql($io_conexion);	
		$this->io_mensajes  = new class_mensajes();		
		$this->io_funciones = new class_funciones();	
		$this->io_seguridad = new sigesp_c_seguridad();
		$this->ls_codemp    = $_SESSION["la_empresa"]["codemp"];
		$this->io_dscuentas = new class_datastore();
		$this->io_dscargos  = new class_datastore();
		$this->io_keygen    = new sigesp_c_generar_consecutivo(); 
		$this->io_evaluate  = new evaluate_formula(); 
		$this->io_fecha  = new class_fecha();		
  }

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_analisis_cotizacion($as_numanacot,$ad_fecdes,$ad_fechas,$as_tipanacot)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_analisis_cotizacion
		//		   Access: public
		//		 Argument: 
		//   $as_numanacot //Número del Análisis de Cotizacion
		//      $ad_fecdes //Fecha a partir del cual comenzará la búsqueda de los Análisis de Cotizacion
		//      $ad_fechas //Fecha hasta el cual comenzará la búsqueda de los Análisis de Cotizacion
		//   $as_tipanacot//Tipo del Analisis de Cotizacion B=Bienes , S=Servicios.
		//      $as_tipope //Tipo de la Operación a ejecutar A=Aprobacion, R=Reverso de la Aprobación.
		//	  Description: Función que busca los Analisis de Cotizacion que esten dispuestas para Aprobacion/Reverso.
		//	   Creado Por: Ing. Laura Cabre
		// Fecha Creación: 05/08/2007								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
        $ls_straux = "";
		
        if (!empty($as_numordcom))
		   {
		     $ls_straux = " AND a.numanacot LIKE '%".$as_numanacot."%'";
		   } 
		if (!empty($ad_fecdes) && !empty($ad_fechas))
		   {  
		     $ld_fecdes = $this->io_funciones->uf_convertirdatetobd($ad_fecdes);
			 $ld_fechas = $this->io_funciones->uf_convertirdatetobd($ad_fechas);
			 $ls_straux = $ls_straux." AND a.fecanacot BETWEEN '".$ld_fecdes."' AND '".$ld_fechas."'";
		   }
		if ($as_tipanacot!='-')
		   {  
		     $ls_straux = $ls_straux." AND tipsolcot='".$as_tipanacot."'";
		   }
		$ls_sql ="SELECT DISTINCT a.numanacot,a.obsana,a.fecanacot,a.tipsolcot,a.fecapro,a.recanacot
				    FROM soc_analisicotizacion a
		           WHERE a.codemp='$this->ls_codemp' $ls_straux 
					 AND a.estana=1 
					 AND a.numanacot NOT IN (SELECT CASE WHEN numanacot IS NULL THEN '-------' ELSE numanacot END
					                           FROM soc_ordencompra 
											  WHERE codemp='$this->ls_codemp' AND estcom<>3) 
				 ORDER BY numanacot ASC";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php->MÉTODO->uf_load_analisis_cotizacion.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	//     $ai_totrows //Total de elementos cargados en el Grid de Analisis de Cotizacion
	//      $as_tipope //Tipo de la Operación a realizar A=Aprobación, R=Reverso de Aprobación.
	//      $ad_fecope //Fecha en la cual se ejecuta la Operación.
	//   $aa_seguridad //Arreglo de seguridad cargado de la informacion de usuario y pantalla.
	//	  Description: Función que recorre el grid de los analisis de cotizacion y genera las respectivas ordenes de compra
	//	   Creado Por: Ing. Laura Cabré
	// Fecha Creación: 09/08/2007								Fecha Última Modificación : 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = false;
	  $ld_fecha=$this->io_funciones->uf_convertirdatetobd($ad_fecope);
	  $ls_tipafeiva = $_SESSION["la_empresa"]["confiva"]; 
	  $this->io_sql->begin_transaction();
	  for ($i=1;$i<=$ai_totrows;$i++)
		  {
			if (array_key_exists("chk".$i,$_POST))
			   {
					$ls_numanacot = $_POST["txtnumanacot".$i];
					$ls_fecapro = $_POST["txtfecapro".$i];
					if($_POST["txttipanacot".$i] == "Bienes")
					 	$ls_tipsolcot = "B";
					else
						$ls_tipsolcot = "S";		 	
					$ls_obsana = $_POST["txtobsanacot".$i];
					$ls_prefijo = $_POST["cmbprefijo".$i];
					$ls_estpagele=0;
					if(array_key_exists("chkpagele".$i,$_POST))
					{
						$ls_estpagele="1";
					}
					$la_ganadores=$this->uf_select_cotizacion_analisis($ls_numanacot,$ls_tipsolcot);
					$li_totalganadores=count((array)$la_ganadores);
					$ls_numordcom = $_POST["txtnumordcom".$i];
				    $lb_valido=$this->io_fecha->uf_comparar_fecha($ls_fecapro,$ad_fecope);
					if($lb_valido)
					{
						if($ls_numordcom!="")
						{
							$lb_existe=$this->uf_select_orden_compra($ls_numordcom,$ls_tipsolcot);
							if($lb_existe)
							{
								$this->io_mensajes->message("El numero de Orden de Compra/Servicio ya existe");
								return false;

							}
						}
					}
					if($lb_valido)
					{
					
						for($li_i=0;$li_i<$li_totalganadores;$li_i++)
						{
							$lb_validamonto=false;
							$ls_proveedor		= $la_ganadores[$li_i]["cod_pro"];
							$ls_cotizacion		= $la_ganadores[$li_i]["numcot"];
							$ls_tipo_proveedor	= $la_ganadores[$li_i]["tipconpro"];
							$arrResultado = $this->uf_select_items($ls_cotizacion,$ls_proveedor,$ls_numanacot,$ls_tipsolcot,$la_items,$li_totrow);
							$la_items = $arrResultado['aa_items'];
							$li_totrow = $arrResultado['li_i'];
							
							$arrResultado = $this->uf_select_items_cotizacion($ls_cotizacion,$ls_proveedor,$ls_numanacot,$ls_tipsolcot,$la_items_cotizacion,$li_totrow_cotizacion);
							$la_items_cotizacion = $arrResultado['aa_items'];
							$li_totrow_cotizacion = $arrResultado['li_i'];
							$arrResultado = $this->uf_viene_de_sep($ls_cotizacion,$ls_proveedor,$lb_viene_sep);
							$lb_viene_sep =	$arrResultado['ab_viene_sep'];
							$lb_valido = $arrResultado['lb_valido'];
							$la_items_cotizacion = $this->uf_calculardetalles_montos($li_totrow_cotizacion,$la_items_cotizacion,$ls_tipsolcot,$ls_tipo_proveedor,$ls_cotizacion,$lb_viene_sep,$ls_estpagele);
							$la_totales = $this->uf_calcular_montos($li_totrow_cotizacion,$la_items_cotizacion,$la_totales,$ls_tipo_proveedor);
							$li_subtotal   = $la_totales["subtotal"];
							$li_totaliva   = $la_totales["totaliva"];
							$li_total      = $la_totales["total"];
							if(($li_subtotal>2000000)&&($ls_estpagele=="2"))// Condicionante establecido para el porcentaje del IVA.
							{
								$ls_estpagele="3";
								unset($la_totales);
								$la_items_cotizacion = $this->uf_calculardetalles_montos($li_totrow_cotizacion,$la_items_cotizacion,$ls_tipsolcot,$ls_tipo_proveedor,$ls_cotizacion,$lb_viene_sep,$ls_estpagele);
								$la_totales = $this->uf_calcular_montos($li_totrow_cotizacion,$la_items_cotizacion,$la_totales,$ls_tipo_proveedor);
								$li_subtotal   = $la_totales["subtotal"];
								$li_totaliva   = $la_totales["totaliva"];
								$li_total      = $la_totales["total"];
							}
							if($li_subtotal>0)
							{
								$lb_validamonto=true;
							}
							if($li_total>0)
							{
								$lb_validamonto=true;
							}
							if($lb_validamonto)
							{
								if ($ls_tipsolcot=='B')
								   {
									 $ls_procede = 'SOCCOC';
									 $ls_numini  = 'numordcom';//Número Inicial.
								   }
								elseif($ls_tipsolcot=='S')
								   {
									 $ls_procede = 'SOCCOS';
									 $ls_numini  = 'numordser';//Número Inicial.
								   }
								   if(trim($ls_numordcom)=="")
								   {
										$ls_numordcom = $this->io_keygen->uf_generar_numero_nuevo3('SOC','soc_ordencompra','numordcom',$ls_procede,15,$ls_numini,"estcondat",$ls_tipsolcot,$_SESSION["la_logusr"],$ls_prefijo);
									}
								if ($ls_numordcom==false)
								   {
									 $this->io_mensajes->message("Este documento está configurado para el manejo de Prefijos, y en este momento Ud. No tiene acceso a ninguno. Por favor diríjase al Administrador del Sistema");
									 echo "<script language=JavaScript>";
									 echo "location.href='sigespwindow_blank.php'";
									 echo "</script>";
								   }
								else
								   {
									 $ls_numsolaux  = $ls_numordcom;
									 $arrResultado=$this->uf_select_solicitud($ls_numanacot,$ls_concepto,$ls_unidad,$ls_uniejeaso,$ls_tipbiesolcot,$ls_recanacot);
									 $ls_tipbiesolcot =	$arrResultado['as_tipbiesolcot'];
									 $ls_uniejeaso = $arrResultado['as_uniejeaso'];
									 $ls_unidad = $arrResultado['as_unidad'];
									 $ls_concepto =	$arrResultado['as_concepto'];
									 $ls_recanacot =	$arrResultado['ls_recanacot'];
									 $lb_valido = $arrResultado['lb_valido'];
									 if ($lb_valido)
										{ 
										  $ls_codestpro1 = $ls_codestpro2 = $ls_codestpro3 = $ls_codestpro4 = $ls_codestpro5 = $ls_estcla = "";
										  $arrResultado = $this->uf_select_unidades_ejecutoras($ls_numanacot, $lb_viene_sep,$la_items,$li_totrow,$la_unidades,$ls_concepto,$ls_unidad,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_cotizacion,$ls_proveedor,$ls_tipsolcot);
										  $la_unidades = $arrResultado['aa_unidades'];
										  $ls_concepto = $arrResultado['as_concepto'];
										  $ls_unidad = $arrResultado['as_unidad'];
										  $ls_codestpro1 = $arrResultado['as_codestpro1'];
										  $ls_codestpro2 = $arrResultado['as_codestpro2'];
										  $ls_codestpro3 = $arrResultado['as_codestpro3'];
										  $ls_codestpro4 = $arrResultado['as_codestpro4'];
										  $ls_codestpro5 = $arrResultado['as_codestpro5'];
										  $ls_estcla = $arrResultado['as_estcla'];
										  $lb_valido = $arrResultado['lb_valido'];
										  if ($lb_valido)
											 { 
											   $arrResultado=$this->uf_insert_orden_compra($ls_proveedor,$li_total,$li_totaliva, $li_subtotal,$aa_seguridad,$ls_tipsolcot,$ls_numanacot,$ls_obsana,$ld_fecha,$ls_concepto,$ls_unidad,
																				 $ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_uniejeaso,$ls_tipbiesolcot,$ls_recanacot,$ls_numordcom,$ls_estpagele);
											   $ls_numordcom = $arrResultado['ls_numordcom'];
											   $lb_valido = $arrResultado['lb_valido'];
											   if ($lb_valido)
												  { 
												  
													if ($lb_valido)
													   { 			 	
													if ($ls_tipsolcot=="B")
													   {
														 $lb_valido=$this->uf_insert_bienes($ls_numordcom,$aa_seguridad,$ls_proveedor,$ls_cotizacion,$la_items_cotizacion,$li_totrow_cotizacion,$ls_tipo_proveedor,$lb_viene_sep,$ls_estpagele);
													   }
													elseif($ls_tipsolcot=="S")
													   {
														 $lb_valido=$this->uf_insert_servicios($ls_numordcom,$aa_seguridad,$ls_proveedor,$ls_cotizacion,$la_items_cotizacion,$li_totrow_cotizacion,$ls_tipo_proveedor,$lb_viene_sep,$ls_estpagele);
													   }
												   if($lb_valido)//Si la afectacion del Iva es Presupuestaria.
													   {
														 $lb_valido=$this->uf_insert_cuentas_presupuestarias($ls_cotizacion,$ls_proveedor,$ls_numanacot,$ls_tipsolcot,$ls_numordcom,$ls_tipsolcot,$la_items,$li_totrow,$la_items_cotizacion,$li_totrow_cotizacion,$aa_seguridad,$ls_tipo_proveedor,$ls_cotizacion,$lb_viene_sep,$ls_proveedor,$ls_estpagele,$ls_unidad);											
													   }
													if ($lb_valido && ($ls_tipo_proveedor != "F")) // si el proveedor es de tipo formal no se le calculan los cargos
													   { 
														 $lb_valido=$this->uf_insert_cuentas_cargos($ls_numordcom,$ls_tipsolcot,$la_items,$li_totrow,$aa_seguridad,$lb_viene_sep,$ls_unidad,$ls_cotizacion,$ls_proveedor,$ls_numanacot,$ls_tipsolcot,$ls_estpagele,$ls_unidad);
													   }
													if ($lb_valido)
													   {
														 $ls_estcom=0;
														 $arrResultado=$this->uf_validar_cuentas($ls_numordcom,$ls_estcom,$ls_tipsolcot);
														 $ls_estcom = $arrResultado['as_estcom'];
														 $lb_valido = $arrResultado['lb_valido'];
													   }
													if ($lb_viene_sep)
													   { 
														 $lb_valido=$this->uf_insert_enlace_sep($ls_numordcom,$ls_tipsolcot,$ls_estcom,$la_unidades,$aa_seguridad);								
													   }
													if (!$lb_valido)
													   {
														 break;
													   }
												  }								
										}
											 }
										}
								   }
								if (!$lb_valido)
								{
									break;
								}
							}
						}
					}//Comparar Fechas
					else
					{
						$this->io_mensajes->message("La fecha de la Generacion no debe ser menor a la de aprobacion");
						break;
					}
			   }
		  }//$lb_valido=false;
	   if ($lb_valido)
		  {
		  	if($ls_numsolaux!=$ls_numordcom)
			{
				$this->io_mensajes->message("Se Asigno el Numero a la Orden de Compra: ".$ls_numordcom);
			}
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
    //---------------------------------------------------------------------------------------------------------------------------------------	

    //---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cotizacion_analisis($as_numanacot, $ls_tipanacot)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cotizacion_analisis
		//		   Access: public
		//		  return :	arreglo que contiene las cotizaciones que participaron en un determinado analisis 
		//	  Description: Metodo que  devuelve las cotizaciones que participaron en un determinado analisis
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 14/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_proveedores=array();
		$lb_valido=false;	
		if($ls_tipanacot == "B")
			$ls_tabla = "soc_dtac_bienes";
		elseif($ls_tipanacot == "S")	
			$ls_tabla = "soc_dtac_servicios";
		$ls_sql= "SELECT cxa.numcot, cxa.cod_pro, p.tipconpro
				  FROM soc_cotxanalisis cxa, rpc_proveedor p
				  WHERE cxa.codemp='$this->ls_codemp' AND cxa.numanacot='$as_numanacot' 
				  AND cxa.codemp=p.codemp AND  cxa.cod_pro = p.cod_pro
				  AND cxa.numcot IN 
				  (SELECT numcot FROM $ls_tabla WHERE codemp='$this->ls_codemp')";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_cotizacion_analisis".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;	
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$aa_proveedores[$li_i]=$row;					
				$li_i++;
			}																
		}
		return $aa_proveedores;
	}//fin de uf_select_cotizacion_analisis
	//---------------------------------------------------------------------------------------------------------------------------------------	

    //---------------------------------------------------------------------------------------------------------------------------------------	
    function uf_insert_orden_compra($as_codpro,$ai_total,$ai_totaliva, $ai_subtotal,$aa_seguridad,$as_tipsolcot,$as_numanacot,
                                    $as_observacion,$ad_fecha,$as_concepto,$ls_unidad,$as_codestpro1,$as_codestpro2,$as_codestpro3,
								    $as_codestpro4,$as_codestpro5,$as_estcla,$as_uniejeaso,$as_tipbieordcom,$as_recanacot,$ls_numordcom,$ls_estpagele)
	{/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_insert_orden_compra
	//	    Arguments: as_codpro  --->   Codigo del proveedor al cual se le esta haciendo la orden de compra
	//	      Returns: devuelve true si se inserto correctamente la cabecera de la orden de compra o false en caso contrario
	//	  Description: Funcion que que se encarga dde insertar una orden de compra
	//	   Creado Por: Ing. Laura Cabré
	// Fecha Creación: 20/06/2007 								Fecha Última Modificación : 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$as_forpag="-";
	$as_diaplacom=0;
	$arrResultado=$this->uf_load_datos_entrega($as_numanacot,$as_codpro,$as_diaplacom,$as_forpag);
	$as_diaplacom = $arrResultado['as_diaentcom'];
	$as_forpag = $arrResultado['as_forpagcom'];
	$lb_valido = $arrResultado['lb_valido'];
	
	$ls_fecordcom=$this->io_funciones->uf_convertirdatetobd($ad_fecha);	
	if($as_tipsolcot=="B")  
	{		
		$arrResultado = $this->io_keygen->uf_verificar_numero_generado('SOC','soc_ordencompra','numordcom','SOCCOC',15,"","estcondat","B",$ls_numordcom);
		$ls_numordcom = $arrResultado['as_numero'];
		$lb_valido = $arrResultado['lb_valido'];

		$ld_monsubtotbie=$ai_subtotal;
		$ld_monsubtotser=0;
	}
	else
	{
		$arrResultado = $this->io_keygen->uf_verificar_numero_generado('SOC','soc_ordencompra','numordcom','SOCCOS',15,"","estcondat","S",$ls_numordcom);
		$ls_numordcom = $arrResultado['as_numero'];
		$lb_valido = $arrResultado['lb_valido'];
		$ld_monsubtotbie=0;
		$ld_monsubtotser=$ai_subtotal;
	}
	$lb_valido=true;	
	if($lb_valido)
	{
     	$ld_monsubtotbie = 0;
     	$ld_monsubtotser = 0;
     	$ld_monbasimp = 0;
     	$ld_mondes = 0;
		$li_estpenalm = 0;
		$li_estapro   = 0;
		$ld_fecaprord = "1900-01-01";
		$ls_codusuapr = "";
		$ls_numpolcon = 0;
		$ls_fecent = "1900-01-01";
		$as_rb_rblugcom = 0;
		$as_codmon='---';
		$as_codfuefin='--';
		$as_estcom=0;
		$as_codtipmod="--";
		
		$as_coduniadm=$ls_unidad;
		
		$ai_estsegcom=0;   	
		$ad_porsegcom=0;
		$ad_monsegcom=0;
		$as_concom="-";
		$as_conordcom=$as_concepto; 
		$ld_mondes=0;
		$as_codpai="---";
		$as_codest="---";
		$as_codmun="---";
		$as_codpar="---";
		$as_lugentnomdep="";
		$as_lugentdir="";
		$ad_antpag=0;
		$ad_tascamordcom=0;
		$ad_montotdiv=0;
		$as_obscom='';
		$ls_forpag="F";
		if($ls_estpagele!="0")
		{
			$ls_forpag="E";
		}
		$ls_sql=" INSERT INTO soc_ordencompra (codemp, numordcom, estcondat, cod_pro, codmon, codfuefin, ".
		        "                              fecordcom, estsegcom, porsegcom, monsegcom, forpagcom, estcom, diaplacom, ".
				"							   concom, obscom, monsubtotbie, monsubtotser, monsubtot, monbasimp, monimp, ".
				"							   mondes, montot, estpenalm, codpai, codest, codmun, codpar, lugentnomdep, ".
				"							   lugentdir, monant, estlugcom, tascamordcom, montotdiv, estapro, fecaprord, ".
				"                              codusuapr, numpolcon, coduniadm, codestpro1,codestpro2,codestpro3,codestpro4,codestpro5, 
				                               estcla,obsordcom, fecent,numanacot,codtipmod,uniejeaso,tipbieordcom,fechentdesde,fechenthasta,codusureg,forpag,estoricom) ".
				" VALUES ('".$this->ls_codemp."','".$ls_numordcom."','".$as_tipsolcot."','".$as_codpro."','".$as_codmon."', ".
				"         '".$as_codfuefin."','".$ls_fecordcom."','".$ai_estsegcom."',".$ad_porsegcom.",".
				"         '".$ad_monsegcom."','".$as_forpag."','".$as_estcom."','".$as_diaplacom."','".$as_concom."', ".
				"         '".$as_conordcom."',".$ld_monsubtotbie.",".$ld_monsubtotser.",".$ai_subtotal.",".$ld_monbasimp.", ".
				"         ".$ai_totaliva.",".$ld_mondes.",".$ai_total.",".$li_estpenalm.",'".$as_codpai."', ".
				"         '".$as_codest."','".$as_codmun."','".$as_codpar."','".$as_lugentnomdep."','".$as_lugentdir."', ".
				"         ".$ad_antpag.",".$as_rb_rblugcom.",".$ad_tascamordcom.",".$ad_montotdiv.",".$li_estapro.", ".
				"         '".$ld_fecaprord."','".$ls_codusuapr."','".$ls_numpolcon."','".$as_coduniadm."','".$as_codestpro1."',
				          '".$as_codestpro2."','".$as_codestpro3."','".$as_codestpro4."','".$as_codestpro5."','".$as_estcla."',
						  '".$as_recanacot."','".$ls_fecent."','".$as_numanacot."','".$as_codtipmod."','".$as_uniejeaso."','".$as_tipbieordcom."','".$ls_fecordcom."','".$ls_fecordcom."','".$_SESSION["la_logusr"]."','".$ls_forpag."','1')";        
		$rs_data=$this->io_sql->execute($ls_sql);
		if($rs_data===false)
		{
			$this->io_sql->rollback();
		    if($this->io_sql->errno=='23505' || $this->io_sql->errno=='1062')
			{
			 	$arrResultado = $this->uf_insert_orden_compra($as_codpro,$ai_total,$ai_totaliva, $ai_subtotal,$aa_seguridad,$as_tipsolcot,
				                              				  $as_numanacot,$as_observacion,$ad_fecha,$as_concepto,$ls_unidad,$as_codestpro1,
											  				  $as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
											  				  $as_uniejeaso,$as_tipbieordcom,$as_recanacot,$ls_numordcom,$ls_estpagele);
				$ls_numordcom = $arrResultado['ls_numordcom'];
				$lb_valido = $arrResultado['lb_valido'];
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->sigesp_soc_c_generar_orden_analisis.php; MÉTODO->uf_insert_orden_compra ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}			
						
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó la Orden de Compra ".$ls_numordcom." tipo ".$as_tipsolcot." de fecha".$ls_fecordcom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			$this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
			if($this->ls_supervisor!=0)
			{
				$ls_fromname="Generación de Orden de Compra";
				$ls_bodyenv="Se le envia la notificación de actualización en el modulo de SOC, se generó la orden de compra N°.. ";
				$ls_nomper=$_SESSION["la_nomusu"];
				$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$ls_numordcom,$ls_bodyenv,$ls_nomper);
			}
			/////////////////////////////////         SEGURIDAD               /////////////////////////////			
	    }
	}
		$arrResultado['ls_numordcom']=$ls_numordcom;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// fin uf_insert_orden_compra
    //---------------------------------------------------------------------------------------------------------------------------------------	
    
	//---------------------------------------------------------------------------------------------------------------------------------------
	function  uf_select_items($as_numcot,$as_codpro,$as_numanacot,$as_tipsolcot,$aa_items,$li_i)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_items
		//		   Access: public
		//		  return :	arreglo que contiene los items que participaron en un determinado analisis, de manera detallada en caso de que
		// 					los items se repitan
		//	  Description: Metodo que  devuelve los items que participaron en un determinado analisis, de manera detallada en caso de que
		// 					los items se repitan
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 10/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$aa_items=array();
		$lb_valido=false;
		if($as_tipsolcot=="B")
		{				
			$ls_sql="SELECT d.codart as codigo, MAX(a.denart) as denominacion, MAX(p.nompro) AS nompro, MAX(dt.canart) as cantidad,
							MAX(dt.preuniart) as precio,MAX(dt.moniva) AS moniva,MAX(dt.montotart) as monto,MAX(d.obsanacot) AS obsanacot,
							MAX(d.numcot) AS numcot, MAX(d.cod_pro) AS cod_pro
					   FROM soc_dtac_bienes d,siv_articulo a, rpc_proveedor p,soc_dtcot_bienes dt, soc_dtsc_bienes dts , soc_cotizacion c					  
					  WHERE d.codemp='".$this->ls_codemp."' 
					    AND d.numanacot='".$as_numanacot."'
						AND dt.cod_pro='".$as_codpro."' 
						AND dt.numcot='".$as_numcot."'
						AND d.codemp=a.codemp 
						AND a.codemp=p.codemp 
						AND p.codemp=dt.codemp
						AND dt.codemp=dts.codemp
						AND dts.codemp=c.codemp											
						AND dt.cod_pro=dts.cod_pro
						AND dt.codart=dts.codart											
						AND dt.cod_pro=c.cod_pro
						AND dt.numcot=c.numcot								
						AND c.numsolcot=dts.numsolcot												 
						AND	d.codart=a.codart 
						AND d.cod_pro=p.cod_pro 
						AND d.numcot=dt.numcot 
						AND d.cod_pro=dt.cod_pro 
						AND d.codart=dt.codart
					  GROUP BY d.codart";
		}
		else
		{
				
				$ls_sql="SELECT d.codser as codigo, MAX(a.denser) as denominacion, MAX(p.nompro) AS nompro, MAX(dt.canser) as cantidad,
								MAX(dt.monuniser) as precio, MAX(dt.moniva) AS moniva,MAX(dt.montotser) as monto,
					            MAX(d.obsanacot) AS obsanacot, MAX(d.numcot) AS numcot, MAX(d.cod_pro) AS cod_pro
					   FROM soc_dtac_servicios d,soc_servicios a, rpc_proveedor p,soc_dtcot_servicio dt, soc_dtsc_servicios dts , soc_cotizacion c					  
					  WHERE d.codemp='".$this->ls_codemp."' 
					    AND d.numanacot='".$as_numanacot."'
						AND dt.cod_pro='".$as_codpro."' 
						AND dt.numcot='".$as_numcot."'
						AND d.codemp=a.codemp 
						AND a.codemp=p.codemp 
						AND p.codemp=dt.codemp
						AND dt.codemp=dts.codemp
						AND dts.codemp=c.codemp											
						AND dt.cod_pro=dts.cod_pro
						AND dt.codser=dts.codser											
						AND dt.cod_pro=c.cod_pro
						AND dt.numcot=c.numcot								
						AND c.numsolcot=dts.numsolcot												 
						AND	d.codser=a.codser
						AND d.cod_pro=p.cod_pro 
						AND d.numcot=dt.numcot 
						AND d.cod_pro=dt.cod_pro 
						AND d.codser=dt.codser
					  GROUP BY d.codser";	
				
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;	
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_i++;
				$aa_items[$li_i]=$row;					
			}																
		}
		$arrResultado['aa_items']=$aa_items;
		$arrResultado['li_i']=$li_i;
		return $arrResultado;
	}
	//--------------------------------------------------------------------------------------------------------------------
	
	//---------------------------------------------------------------------------------------------------------------------------------------
	function  uf_select_items_sep($as_numcot,$as_codpro,$as_numanacot,$as_tipsolcot,$as_codart)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_items
		//		   Access: public
		//		  return :	arreglo que contiene los items que participaron en un determinado analisis, de manera detallada en caso de que
		// 					los items se repitan
		//	  Description: Metodo que  devuelve los items que participaron en un determinado analisis, de manera detallada en caso de que
		// 					los items se repitan
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 10/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$la_items=array();
		$lb_valido=false;
		if($as_tipsolcot=="B")
		{				
			$ls_sql="SELECT d.codart as codigo, MAX(a.denart) as denominacion, MAX(p.nompro) AS nompro, MAX(dt.canart) as cantidad,
							MAX(dt.preuniart) as precio,MAX(dt.moniva) AS moniva,MAX(dt.montotart) as monto,MAX(d.obsanacot) AS obsanacot,
							MAX(d.numcot) AS numcot, MAX(d.cod_pro) AS cod_pro, dts.numsep AS numsep 
					   FROM soc_dtac_bienes d,siv_articulo a, rpc_proveedor p,soc_dtcot_bienes dt, soc_dtsc_bienes dts , soc_cotizacion c					  
					  WHERE d.codemp='".$this->ls_codemp."' 
					    AND d.numanacot='".$as_numanacot."'
						AND dt.cod_pro='".$as_codpro."' 
						AND dt.numcot='".$as_numcot."'
						AND dts.codart='".$as_codart."'
						AND d.codemp=a.codemp 
						AND a.codemp=p.codemp 
						AND p.codemp=dt.codemp
						AND dt.codemp=dts.codemp
						AND dts.codemp=c.codemp											
						AND dt.cod_pro=dts.cod_pro
						AND dt.codart=dts.codart											
						AND dt.cod_pro=c.cod_pro
						AND dt.numcot=c.numcot								
						AND c.numsolcot=dts.numsolcot												 
						AND	d.codart=a.codart 
						AND d.cod_pro=p.cod_pro 
						AND d.numcot=dt.numcot 
						AND d.cod_pro=dt.cod_pro 
						AND d.codart=dt.codart
					  GROUP BY d.codart,dts.numsep";
		}
		else
		{
				
				$ls_sql="SELECT d.codser as codigo, MAX(a.denser) as denominacion, MAX(p.nompro) AS nompro, MAX(dt.canser) as cantidad,
								MAX(dt.monuniser) as precio, MAX(dt.moniva) AS moniva,MAX(dt.montotser) as monto,
					            MAX(d.obsanacot) AS obsanacot, MAX(d.numcot) AS numcot, MAX(d.cod_pro) AS cod_pro, dts.numsep AS numsep 
					   FROM soc_dtac_servicios d,soc_servicios a, rpc_proveedor p,soc_dtcot_servicio dt, soc_dtsc_servicios dts , soc_cotizacion c					  
					  WHERE d.codemp='".$this->ls_codemp."' 
					    AND d.numanacot='".$as_numanacot."'
						AND dt.cod_pro='".$as_codpro."' 
						AND dt.numcot='".$as_numcot."'
						AND dts.codser='".$as_codart."'
						AND d.codemp=a.codemp 
						AND a.codemp=p.codemp 
						AND p.codemp=dt.codemp
						AND dt.codemp=dts.codemp
						AND dts.codemp=c.codemp											
						AND dt.cod_pro=dts.cod_pro
						AND dt.codser=dts.codser											
						AND dt.cod_pro=c.cod_pro
						AND dt.numcot=c.numcot								
						AND c.numsolcot=dts.numsolcot												 
						AND	d.codser=a.codser
						AND d.cod_pro=p.cod_pro 
						AND d.numcot=dt.numcot 
						AND d.cod_pro=dt.cod_pro 
						AND d.codser=dt.codser
					  GROUP BY d.codser,dts.numsep";	
				
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;	
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))//
			{
				$la_items[$li_i]["numsep"]       = $row["numsep"];
				$li_i++;
			}			
		}
		return $la_items;
	}
	//--------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------
	function uf_calcular_montos($ai_totrow,$aa_items,$aa_totales,$as_tipo_proveedor)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_calcular_montos
		//		   Access: public
		//		  return :	arreglo  montos totalizados
		//	  Description: Metodo que  devuelve arreglo  montos totalizados
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 09/08/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$li_subtotal=0;
		 	$li_totaliva=0;
		 	$li_total=0;
		 	$aa_totales=array();
			for($li_j=1;$li_j<=$ai_totrow;$li_j++)
		 	{
				$li_subtotal+=(($aa_items[$li_j]["precio"]) * ($aa_items[$li_j]["cantidad"]));
			if($as_tipo_proveedor != "F") //En caso de que el roveedor sea formal no se le calculan los cargos
//				$li_totaliva=number_format($li_totaliva+$aa_items[$li_j]["moniva"],2,'.','');
//				$li_totaliva=$li_totaliva+$aa_items[$li_j]["moniva"];
				$li_totaliva+=number_format($aa_items[$li_j]["moniva"],2,'.','');	
			}
			$li_totaliva=number_format($li_totaliva,3,'.','');
			$li_totaliva=number_format($li_totaliva,2,'.','');
			$li_total=$li_totaliva+$li_subtotal;		 
			$li_total=number_format($li_total,2,'.','');
			$aa_totales["subtotal"]=$li_subtotal;
			$aa_totales["totaliva"]=$li_totaliva;
			$aa_totales["total"]=$li_total;
		return $aa_totales;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------
	function uf_calculardetalles_montos($ai_totrow,$aa_items,$as_tipsolcot,$as_tipo_proveedor,$as_cotizacion,$ab_viene_sep,$as_estpagele)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_calculardetalles_montos
		//		   Access: public
		//		  return :	arreglo  montos totalizados
		//	  Description: Metodo que  devuelve arreglo  montos totalizados
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 09/08/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		for($li_j=1;$li_j<=$ai_totrow;$li_j++)
		{
			$ls_codart=$aa_items[$li_j]["codigo"];
			$ls_numsep=$aa_items[$li_j]["numsep"];
			$li_cantidad=$aa_items[$li_j]["cantidad"];
			$li_precio=$aa_items[$li_j]["precio"];
			$li_subtotart=($li_cantidad*$li_precio);

			$ls_formula  = $this->uf_load_formula_cargo($ls_codart,$as_tipsolcot,$as_estpagele);
			$arrResultado=$this->io_evaluate->uf_evaluar($ls_formula,$li_subtotart,$lb_valido);
			$li_moncargo = $arrResultado['result'];
			$lb_valido = $arrResultado['lb_valido'];
			
//////////////////  CODIGO SIN PAGOS ELECTRONICOS    ////////////////			
//			if($ab_viene_sep)
//			{
//				$ls_formula  = $this->uf_load_formula($ls_numsep,$ls_codart,$as_tipsolcot);
//				$arrResultado=$this->io_evaluate->uf_evaluar($ls_formula,$li_subtotart,$lb_valido);
//				$li_moncargo = $arrResultado['result'];
//				$lb_valido = $arrResultado['lb_valido'];
//			}
//			else
//			{
//				$arrResultado=$this->uf_obtenercargos_items($ls_codart,$ls_numsep,$as_tipsolcot,$li_subtotart,$li_moncargo,$as_cotizacion);
//				$li_moncargo = $arrResultado['ai_moncargo'];
//				$lb_valido = $arrResultado['lb_valido'];
//				
//			}
//////////////////  CODIGO SIN PAGOS ELECTRONICOS    ////////////////			
			if($as_tipo_proveedor == "F") //En caso de que el roveedor sea formal no se le calculan los cargos
			{
				$li_moncargo=0;
			}
			$li_montotart=($li_subtotart+$li_moncargo);
			$aa_items[$li_j]["moniva"]=$li_moncargo;
			$aa_items[$li_j]["monto"]=$li_montotart;
			
		}
		return $aa_items;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------------
	function  uf_obtenercargos_items($as_codart,$as_numsep,$as_tipsolcot,$ai_precio,$ai_moncargo,$as_cotizacion)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtenercargos_items
		//		   Access: public
		//		  return :	arreglo que contiene los items que participaron en un determinado analisis, de manera detallada en caso de que
		// 					los items se repitan
		//	  Description: Metodo que  devuelve los items que participaron en un determinado analisis, de manera detallada en caso de que
		// 					los items se repitan
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 10/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ai_moncargo=0;
		$lb_valido=true;
		if($as_tipsolcot=="B")
		{				
			$ls_sql="SELECT moniva".
					"   FROM soc_dtcot_bienes".
					"  WHERE soc_dtcot_bienes.codemp='".$this->ls_codemp."' ".
					"    AND soc_dtcot_bienes.numcot='".$as_cotizacion."'".
					"    AND soc_dtcot_bienes.codart='".$as_codart."' ";
		}
		else
		{
			$ls_sql="SELECT moniva".
					"   FROM soc_dtcot_servicio".
					"  WHERE soc_dtcot_servicio.codemp='".$this->ls_codemp."' ".
					"    AND soc_dtcot_servicio.numcot='".$as_cotizacion."'".
					"    AND soc_dtcot_servicio.codser='".$as_codart."' ";				
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("Funtion-> uf_obtenercargos_items ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;	
		}
		else
		{
			$li_i=0;
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				//$li_i++;
				$ai_moncargo=number_format($row["moniva"],2,'.','');
				//$li_moncar=$this->io_evaluate->uf_evaluar($ls_formula,$ai_precio,$lb_valido);
				//$ai_moncargo += $li_moncar;
			}																
		}
		$arrResultado['ai_moncargo']=$ai_moncargo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}
	//--------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_load_datos_entrega($as_numanacot,$as_codpro,$as_diaentcom,$as_forpagcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_datos_entrega
		//		   Access: public
		//		  return :	
		//	  Description: 
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 10/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT diaentcom,forpagcom".
				"   FROM soc_cotizacion,soc_cotxanalisis".
				"  WHERE soc_cotxanalisis.codemp='".$this->ls_codemp."' ".
				"    AND soc_cotxanalisis.cod_pro='".$as_codpro."'".
				"    AND soc_cotxanalisis.numanacot='".$as_numanacot."' ".
				"    AND soc_cotizacion.codemp=soc_cotxanalisis.codemp".
				"    AND soc_cotizacion.numcot=soc_cotxanalisis.numcot";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("Funtion-> uf_load_datos_entrega ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;	
		}
		else
		{
			$li_i=0;
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_diaentcom=$row["diaentcom"];
				$as_forpagcom=strtoupper($row["forpagcom"]);
			}																
		}
		$arrResultado['as_diaentcom']=$as_diaentcom;
		$arrResultado['as_forpagcom']=$as_forpagcom;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}
	//--------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_load_formula($as_numsol,$as_codbieser,$as_estcondat)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_formula
		//		   Access: public
		//		  return :	
		//	  Description: 
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 10/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_formula="";
		if($as_estcondat=="B")
		{
			$ls_sql="SELECT formula".
					"   FROM sep_dta_cargos".
					"  WHERE sep_dta_cargos.codemp='".$this->ls_codemp."' ".
					"    AND sep_dta_cargos.numsol='".$as_numsol."'".
					"    AND sep_dta_cargos.codart='".$as_codbieser."' ";
		}
		else
		{
			$ls_sql="SELECT formula".
					"   FROM sep_dts_cargos".
					"  WHERE sep_dts_cargos.codemp='".$this->ls_codemp."' ".
					"    AND sep_dts_cargos.numsol='".$as_numsol."'".
					"    AND sep_dts_cargos.codser='".$as_codbieser."' ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("Funtion-> uf_load_formula ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;	
		}
		else
		{
			$li_i=0;
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_formula=$row["formula"];
			}																
		}
		return $ls_formula;
	}
	//--------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_load_formula_cargo($as_codbieser,$as_estcondat,$as_estpagele)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_formula_cargo
		//		   Access: public
		//		  return :	
		//	  Description: 
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 10/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_formula="";
		if($as_estcondat=="B")
		{
			$ls_sql="SELECT formula".
					"   FROM sigesp_cargos, siv_cargosarticulo".
					"  WHERE sigesp_cargos.codemp='".$this->ls_codemp."' ".
					"    AND sigesp_cargos.estpagele='0'".
					"    AND siv_cargosarticulo.codart='".$as_codbieser."' ".
					"    AND sigesp_cargos.codemp= siv_cargosarticulo.codemp".
					"    AND sigesp_cargos.codcar= siv_cargosarticulo.codcar";
		}
		else
		{
			$ls_sql="SELECT formula".
					"   FROM sigesp_cargos, soc_serviciocargo".
					"  WHERE sigesp_cargos.codemp='".$this->ls_codemp."' ".
					"    AND sigesp_cargos.estpagele='0'".
					"    AND soc_serviciocargo.codser='".$as_codbieser."' ".
					"    AND sigesp_cargos.codemp= soc_serviciocargo.codemp".
					"    AND sigesp_cargos.codcar= soc_serviciocargo.codcar";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("Funtion-> uf_load_formula_cargo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;	
		}
		else
		{
			$li_i=0;
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_formula=$row["formula"];
			}
			else
			{
				if($as_estcondat=="B")
				{
					$ls_sql="SELECT formula".
							"   FROM sigesp_cargos, siv_cargosarticulo".
							"  WHERE sigesp_cargos.codemp='".$this->ls_codemp."' ".
							"    AND sigesp_cargos.estpagele='".$as_estpagele."'".
							"    AND siv_cargosarticulo.codart='".$as_codbieser."' ".
							"    AND sigesp_cargos.codemp= siv_cargosarticulo.codemp".
							"    AND sigesp_cargos.codcar= siv_cargosarticulo.codcar";
				}
				else
				{
					$ls_sql="SELECT formula".
							"   FROM sigesp_cargos, soc_serviciocargo".
							"  WHERE sigesp_cargos.codemp='".$this->ls_codemp."' ".
							"    AND sigesp_cargos.estpagele='".$as_estpagele."'".
							"    AND soc_serviciocargo.codser='".$as_codbieser."' ".
							"    AND sigesp_cargos.codemp= soc_serviciocargo.codemp".
							"    AND sigesp_cargos.codcar= soc_serviciocargo.codcar";
				}
				$rs_data=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("Funtion-> uf_load_formula_cargo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$lb_valido=false;	
				}
				else
				{
					$li_i=0;
					if($row=$this->io_sql->fetch_row($rs_data))
					{
						$ls_formula=$row["formula"];
					}
				}
			
			}//		
		}
		return $ls_formula;
	}
	//--------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_bienes($as_numordcom,$aa_seguridad,$as_codpro,$ls_numcot,$aa_items_cotizacion,$ai_totrow_cotizacion,$as_tipo_proveedor,$ab_viene_sep,$ls_estpagele)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_bienes
		//		   Access: private
		//	    Arguments: as_numordcom  ---> número de la Orden de Compra
		//                 as_items  ---> listado de indices de items q van a ser guardados
		//				   as_numanacot--->numero de analisis de cotizacion
		//	      Returns: true si se insertaron los bienes correctamente o false en caso contrario
		//	  Description: este metodo inserta los bienes de una   orden de compra
		//	   Creado Por: Ing. Laura Cabre
		// Fecha Creación: 21/06/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;	
		for($li_i=1;($li_i<=$ai_totrow_cotizacion)&&($lb_valido);$li_i++)
		{
			$ls_numsep     = trim($aa_items_cotizacion[$li_i]["numsep"]);
			$ls_codart     = $aa_items_cotizacion[$li_i]["codigo"];
			$ls_denart     = $aa_items_cotizacion[$li_i]["denominacion"];
			$li_canart     = $aa_items_cotizacion[$li_i]["cantidad"];
			$ld_preuniart  = $aa_items_cotizacion[$li_i]["precio"];
			$ld_monsubart  = ($aa_items_cotizacion[$li_i]["precio"]) * ($aa_items_cotizacion[$li_i]["cantidad"]);
			$ld_montotart  = $aa_items_cotizacion[$li_i]["monto"];
			$ld_monimp     = $aa_items_cotizacion[$li_i]["moniva"];
//			if(trim($ls_numsep)!="")
//			{
//				$ls_formula  = $this->uf_load_formula($ls_numsep,$ls_codart,"B");
//				////////////////////////////////////////////////////////////////////////////////////
//				// código agregado a solicitud Lic. Anibal Barraez
//				// Si la SEP no tiene cargos y los agregan en el análisis de cotización
//				// igual debe calcular el cargo para gregarlo a la orden de compra
//				////////////////////////////////////////////////////////////////////////////////////
//				if($ls_formula=="")
//				{
//					$la_cargos=$this->uf_select_cargos($ls_codart,"B");
//					$ls_formula=$la_cargos['formula'];
//					unset($la_cargos);
//				}
//				///////////////////////////////////////////////////////////////////////////////////
//				$arrResultado=$this->io_evaluate->uf_evaluar($ls_formula,$ld_monsubart,$lb_valido);
//				$ld_monimp = $arrResultado['result'];
//				$lb_valido = $arrResultado['lb_valido'];
//                if($as_tipo_proveedor!="F")
//                {
//                    $ld_montotart=($ld_monsubart+$ld_monimp);
//                }
//                else
//                {
//                    $ld_montotart=($ld_monsubart);
//                }
//				
//			}
//			else
//			{
//				$ld_montotart  = $aa_items_cotizacion[$li_i]["monto"];
//				$ld_monimp     = $aa_items_cotizacion[$li_i]["moniva"];
//			}
			$ls_codunieje  = trim($aa_items_cotizacion[$li_i]["coduniadm"]);
			$ls_codestpro1 = trim($aa_items_cotizacion[$li_i]["codestpro1"]);
			$ls_codestpro2 = trim($aa_items_cotizacion[$li_i]["codestpro2"]);
			$ls_codestpro3 = trim($aa_items_cotizacion[$li_i]["codestpro3"]);
			$ls_codestpro4 = trim($aa_items_cotizacion[$li_i]["codestpro4"]);
			$ls_codestpro5 = trim($aa_items_cotizacion[$li_i]["codestpro5"]);
			$ls_estcla     = trim($aa_items_cotizacion[$li_i]["estcla"]);
			$ls_codfuefin  = trim($aa_items_cotizacion[$li_i]["codfuefin"]);
			$la_data       = $this->uf_select_bienes_servicios($ls_codart,"B",$as_codpro,$ls_numcot);
			if($ab_viene_sep)
			{
				$ls_unidad= $this->uf_select_unidad_bienes_servicios($ls_codart,"B",$ls_numsep);
				$ls_codfuefin= $this->uf_select_fuentefinanciamiento_sep($ls_numsep);
			}
			else
			{
				$ls_unidad     = $la_data["unidad"];	
			}
			if($ld_montotart>0)
			{			
				$ls_sql = "INSERT INTO soc_dt_bienes (codemp,numordcom,estcondat,codart,unidad,canart,penart,preuniart,monsubart,
													  montotart,orden,numsol,coduniadm,codestpro1,codestpro2,codestpro3,codestpro4,
													  codestpro5,estcla,codfuefin)".
						"  VALUES ('".$this->ls_codemp."','".$as_numordcom."','B','".$ls_codart."','".$ls_unidad."',".$li_canart.",0, 
								   ".$ld_preuniart.",".$ld_monsubart.",".$ld_montotart.",".$li_i.",'".$ls_numsep."','".$ls_codunieje."',
								   '".$ls_codestpro1."','".$ls_codestpro2."','".$ls_codestpro3."','".$ls_codestpro4."',
								   '".$ls_codestpro5."','".$ls_estcla."','".$ls_codfuefin."')";
				$li_row = $this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->sigesp_soc_c_generar_orden_analisis.php;MÉTODO->uf_insert_bienes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					echo $this->io_sql->message;
				}
				else
				{
					if($lb_valido)
					{
						/////////////////////////////////         SEGURIDAD               /////////////////////////////		
						$ls_evento="INSERT";
						$ls_descripcion ="Insertó el Articulo ".$ls_codart." a la Orden de Compra  ".$as_numordcom." Asociado a la empresa ".$this->ls_codemp;
						$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
														$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
														$aa_seguridad["ventanas"],$ls_descripcion);
						/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					}
					if($as_tipo_proveedor!="F")
					{
						$lb_valido=$this->uf_insert_cargos($as_numordcom,"B",$aa_seguridad,$ls_codart,$ld_monsubart,$ld_monimp,$ld_montotart,$ls_numsep,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_estpagele);
					}
				}
			}
		}
		return $lb_valido;
	}// end function uf_insert_bienes
	//-----------------------------------------------------------------------------------------------------------------------------------	
	//--------------------------------------------------------------------------------------------------------------------
	function uf_insert_servicios($as_numordcom,$aa_seguridad,$as_codpro,$ls_numcot,$aa_items_cotizacion,$ai_totrow_cotizacion,$as_tipo_proveedor,$ab_viene_sep,$ls_estpagele)
								
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_servicios
		//		   Access: private
		//	    Arguments: as_numordcom  ---> número de la Orden de Compra
		//                 as_items  ---> listado de indices de items q van a ser guardados
		//				   $as_numanacot--->numero de analisis de cotizacion
		//	      Returns: true si se insertaron los bienes correctamente o false en caso contrario
		//	  Description: este metodo inserta los bienes de una   orden de compra
		//	   Creado Por: Ing. Laura Cabre
		// Fecha Creación: 21/06/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		for($li_i=1;($li_i<=$ai_totrow_cotizacion)&&($lb_valido);$li_i++)
		{
			$ls_codser     = $aa_items_cotizacion[$li_i]["codigo"];
			$ls_denser     = $aa_items_cotizacion[$li_i]["denominacion"];
			$li_canser     = $aa_items_cotizacion[$li_i]["cantidad"];
			$ld_preuniser  = $aa_items_cotizacion[$li_i]["precio"];
			$ld_monsubser  = ($aa_items_cotizacion[$li_i]["precio"]) * ($aa_items_cotizacion[$li_i]["cantidad"]);
			$ls_numsep     = trim($aa_items_cotizacion[$li_i]["numsep"]);
				$ld_montotser  = $aa_items_cotizacion[$li_i]["monto"];
				$ld_monimp     = $aa_items_cotizacion[$li_i]["moniva"];
//			if(trim($ls_numsep)!="")
//			{
//				$ls_formula  = $this->uf_load_formula($ls_numsep,$ls_codser,"S");
//				////////////////////////////////////////////////////////////////////////////////////
//				// código agregado a solicitud Lic. Anibal Barraez
//				// Si la SEP no tiene cargos y los agregan en el análisis de cotización
//				// igual debe calcular el cargo para gregarlo a la orden de compra
//				////////////////////////////////////////////////////////////////////////////////////
//				if($ls_formula=="")
//				{
//					$la_cargos=$this->uf_select_cargos($ls_codser,"S",$ls_estpagele);
//					$ls_formula=$la_cargos['formula'];
//					unset($la_cargos);
//				}
//				///////////////////////////////////////////////////////////////////////////////////
//				$arrResultado=$this->io_evaluate->uf_evaluar($ls_formula,$ld_monsubser,$lb_valido);
//				$ld_monimp = $arrResultado['result'];
//				$lb_valido = $arrResultado['lb_valido'];
//                if($as_tipo_proveedor!="F")
//                {
//					$ld_montotser=($ld_monsubser+$ld_monimp);
//                }
//                else
//                {
//                    $ld_montotser=($ld_monsubser);
//                }
//			}
//			else
//			{
//				$ld_montotser  = $aa_items_cotizacion[$li_i]["monto"];
//				$ld_monimp     = $aa_items_cotizacion[$li_i]["moniva"];
//			}

			$ls_codunieje  = trim($aa_items_cotizacion[$li_i]["coduniadm"]);
			$ls_codestpro1 = trim($aa_items_cotizacion[$li_i]["codestpro1"]);
			$ls_codestpro2 = trim($aa_items_cotizacion[$li_i]["codestpro2"]);
			$ls_codestpro3 = trim($aa_items_cotizacion[$li_i]["codestpro3"]);
			$ls_codestpro4 = trim($aa_items_cotizacion[$li_i]["codestpro4"]);
			$ls_codestpro5 = trim($aa_items_cotizacion[$li_i]["codestpro5"]);
			$ls_estcla     = trim($aa_items_cotizacion[$li_i]["estcla"]);
			$ls_codfuefin  = trim($aa_items_cotizacion[$li_i]["codfuefin"]);
			if($ab_viene_sep)
			{
				$ls_codfuefin= $this->uf_select_fuentefinanciamiento_sep($ls_numsep);
			}
			
	        $ls_sql=" INSERT INTO soc_dt_servicio (codemp, numordcom, estcondat, codser, canser, monuniser, monsubser, montotser, 
			                                       orden, numsol,coduniadm, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla,codfuefin)".
                    "  VALUES ('".$this->ls_codemp."','".$as_numordcom."','S','".$ls_codser."',".$li_canser.",".$ld_preuniser.",
					           ".$ld_monsubser.",".$ld_montotser.",'".$li_i."','".$ls_numsep."','".$ls_codunieje."','".$ls_codestpro1."','".$ls_codestpro2."',
							   '".$ls_codestpro3."','".$ls_codestpro4."','".$ls_codestpro5."','".$ls_estcla."','".$ls_codfuefin."')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
			    $this->io_mensajes->message("CLASE->sigesp_soc_c_generar_orden_analisis.php;MÉTODO->uf_insert_servicios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				echo $this->io_sql->message.'<br>';
			}
			else
			{
				if($lb_valido)
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="INSERT";
					$ls_descripcion ="Insertó el servicio ".$ls_codser." a la Orden de Compra  ".$as_numordcom." Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////	
					if($as_tipo_proveedor!="F")
						$lb_valido=$this->uf_insert_cargos($as_numordcom,"S",$aa_seguridad,$ls_codser,$ld_monsubser,$ld_monimp,$ld_montotser,$ls_numsep,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_estpagele);	
			    }
			}
		}
		return $lb_valido;
	}// end function uf_insert_servicios
	//-----------------------------------------------------------------------------------------------------------------------------------	
	//--------------------------------------------------------------------------------------------------------------------
	function uf_insert_cuentas_presupuestarias($as_numcot,$as_codpro,$as_numanacot,$as_tipsolcot,$as_numordcom,$as_estcondat,$aa_items,$li_totrow,$aa_items_cotizacion,$ai_totrow_cotizacion,$aa_seguridad,$as_tipo_proveedor,$as_numcot1,$ab_vienesep, $as_codpro1,$ls_estpagele,$ls_unidad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_cuentas_presupuestarias
		//		   Access: private
		//	    Arguments: as_numordcom  ---> Número de la orden de compra 
		//                 as_estcondat  ---> estatus de la orden de compra  bienes o servicios
		//				   as_items  	---> items de la orden de compra
		//				   aa_seguridad  ---> arreglo de las variables de seguridad
		//				   aa_numcot------>numero de cotizacion
		//				   ab_vienesep--->booleano que indica si la solicitud viene de sep o no
		//				   as_codpro----> codigo del proveedor
		//				   aa_items_cotizacion--->items sumarizados en la cotizacion
		//				   ai_totrow_cotizacion--->cantidad de elementos en el arreglo anterior
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta las cuentas de una Solicitud de Ejecución Presupuestaria
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Modificado Por: Ing. Yozelin Barrgan, Ing. Laura Cabre
		// Fecha Creación: 17/03/2007 								Fecha Última Modificación : 21/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_dscuentas->data=array();
		$ls_tipafeiva = $_SESSION["la_empresa"]["confiva"];
		for($li_i=1;($li_i<=$li_totrow)&&($lb_valido);$li_i++)
		{
			$ls_codart  = $aa_items[$li_i]["codigo"];
			//$ls_numsep  = $aa_items[$li_i]["numsep"];	
			$li_totalgeneral = $aa_items[$li_i]["cantidad"];
			$la_numsep  = $this->uf_select_items_sep($as_numcot,$as_codpro,$as_numanacot,$as_tipsolcot,$ls_codart);
			for ($li_z=0;$li_z<count((array)$la_numsep);$li_z++)
			{
				$ls_numsep = $la_numsep[$li_z]["numsep"];
				$la_cuentas = $this->uf_select_cuentas_presupuestarias($as_numcot,$ls_codart,$ls_numsep,$as_estcondat,$ab_vienesep,$as_codpro);
				for ($li_j=0;$li_j<count((array)$la_cuentas);$li_j++)
					{
					  $ls_estcla    = $la_cuentas[$li_j]["estcla"];
					  $ls_codestpro = $la_cuentas[$li_j]["programatica"];
					  $ls_spgcta    = $la_cuentas[$li_j]["spg_cuenta"];
					  $ls_codfuefin = $la_cuentas[$li_j]["codfuefin"];

						  if($ab_vienesep)//Si viene de una sep
						  {
							  $ls_cantidad    = $la_cuentas[$li_j]["cantidad"];
							//  print $ls_cantidad."  --  ".$li_totalgeneral."<br>";
							  if ($ls_cantidad<=$li_totalgeneral)
							  {
									$li_totalgeneral=$li_totalgeneral-$ls_cantidad;
							  }
							  else
							  {
								$ls_cantidad= $li_totalgeneral;
								$li_totalgeneral=0;
							  }
							  $ld_moncue    = ($aa_items[$li_i]["precio"]) * ($ls_cantidad);
						  }
						  else
						  {
							  $ld_moncue    = ($aa_items[$li_i]["precio"]) * ($aa_items[$li_i]["cantidad"]);
						  }
					
					//  $ld_moncue    = ($aa_items[$li_i]["precio"]) * ($la_cuentas[$li_j]["cantidad"]);
					  
					//  print $ls_codart." SEP-> Precio->".$aa_items[$li_i]["precio"]." Cantidad->".$aa_items[$li_i]["cantidad"]." Cant. SEP->".$ls_cantidad."<br>";
					  $this->io_dscuentas->insertRow("coditem",$ls_codart);
					  $this->io_dscuentas->insertRow("moncue",$ld_moncue);
					  $this->io_dscuentas->insertRow("cuenta",$ls_spgcta);
					  $this->io_dscuentas->insertRow("codestpro",$ls_codestpro);	
					  $this->io_dscuentas->insertRow("estcla",$ls_estcla);
					  $this->io_dscuentas->insertRow("codfuefin",$ls_codfuefin);
					}	
				}
		}
		
		//Por cada item se guarda su respectiva cuenta de cargo
		if($as_tipo_proveedor != "F" && $ls_tipafeiva=='P')// En caso de que el proveedor sea tipo formal, no se le calculan los cargos
		{
			for($li_i=1;($li_i<=$li_totrow)&&($lb_valido);$li_i++)
			{
				$ls_codart = trim($aa_items[$li_i]["codigo"]);
				$li_totalgeneral = $aa_items[$li_i]["cantidad"];
				//$ls_numsep = $aa_items[$li_i]["numsep"];
				$la_numsep  = $this->uf_select_items_sep($as_numcot,$as_codpro,$as_numanacot,$as_tipsolcot,$ls_codart);
				for ($li_z=0;$li_z<count((array)$la_numsep);$li_z++)
				{
					$ls_numsep = $la_numsep[$li_z]["numsep"];
	
					$la_cargos=$this->uf_select_cargos_cot($as_numcot,$as_codpro,$ls_codart,$as_estcondat,$ls_estpagele);
					$ls_cantidad    = $aa_items[$li_i]["cantidad"];
					if(count((array)$la_cargos)>0)
					{
						$ls_estcla    = $la_cargos["estcla"];
						$ls_codfuefin    = $la_cargos["codfuefin"];
						$ls_codestpro = trim($la_cargos["codestpro1"]).trim($la_cargos["codestpro2"]).trim($la_cargos["codestpro3"]).trim($la_cargos["codestpro4"]).trim($la_cargos["codestpro5"]);
						$ls_spgcta    = $la_cargos["spg_cuenta"];
						$ld_monto     = $ls_cantidad * $aa_items[$li_i]["precio"];
						$ls_formula   = str_replace('$LD_MONTO',$ld_monto,$la_cargos["formula"]);
						eval('$li_moncue ='.$ls_formula.";");
						$li_moncue=number_format($li_moncue,2,'.','');
					  
						$ls_estceniva=$_SESSION["la_empresa"]["estceniva"];
						if($ls_estceniva=="1")
						{
							$arrResultado= $this->uf_load_estructura_central($ls_unidad,$ls_codestprocen1,$ls_codestprocen2,$ls_codestprocen3,$ls_codestprocen4,$ls_codestprocen5,$ls_esclacen);
							$ls_codestprocen1 = $arrResultado['as_codestprocen1'];
							$ls_codestprocen2 = $arrResultado['as_codestprocen2'];
							$ls_codestprocen3 = $arrResultado['as_codestprocen3'];
							$ls_codestprocen4 = $arrResultado['as_codestprocen4'];
							$ls_codestprocen5 = $arrResultado['as_codestprocen5'];
							$ls_esclacen = $arrResultado['as_esclacen'];
							$lb_valido = $arrResultado['lb_valido'];
				
							
							$ls_codestpro=$ls_codestprocen1.$ls_codestprocen2.$ls_codestprocen3.$ls_codestprocen4.$ls_codestprocen5;
						}

						$this->io_dscuentas->insertRow("estcla",$ls_estcla);
						$this->io_dscuentas->insertRow("codestpro",$ls_codestpro);	
						$this->io_dscuentas->insertRow("cuenta",$ls_spgcta);			
						$this->io_dscuentas->insertRow("moncue",$li_moncue);	
						$this->io_dscuentas->insertRow("coditem",$ls_codart);		
						$this->io_dscuentas->insertRow("codfuefin",$ls_codfuefin);		
					}
				}
			}
		}
		if(count((array)$this->io_dscuentas->data)>0)
		{
			$this->io_dscuentas->group_by(array('0'=>'codestpro','1'=>'cuenta','2'=>'estcla','3'=>'codfuefin'),array('0'=>'moncue'),'moncue');
			$li_total=$this->io_dscuentas->getRowCount('codestpro');
			for ($li_fila=1;$li_fila<=$li_total;$li_fila++)
			    {
				  $ls_estcla     = $this->io_dscuentas->getValue('estcla',$li_fila);
				  $ls_codfuefin     = $this->io_dscuentas->getValue('codfuefin',$li_fila);
				  $ls_codpro     = $this->io_dscuentas->getValue('codestpro',$li_fila);
				  $ls_cuenta     = $this->io_dscuentas->getValue('cuenta',$li_fila);
				  $li_moncue     = $this->io_dscuentas->getValue('moncue',$li_fila);
				  $ls_codestpro1 = substr($ls_codpro,0,25);
				  $ls_codestpro2 = substr($ls_codpro,25,25);
				  $ls_codestpro3 = substr($ls_codpro,50,25);
				  $ls_codestpro4 = substr($ls_codpro,75,25);
				  $ls_codestpro5 = substr($ls_codpro,100,25);
				$li_moncue=number_format($li_moncue,3,'.','');
				$li_moncue=number_format($li_moncue,2,'.','');
				$ls_sql="INSERT INTO soc_cuentagasto (codemp, numordcom, estcondat, codestpro1, codestpro2, codestpro3, codestpro4,  ".
						"							  codestpro5, estcla, spg_cuenta, monto, codfuefin)".
						"	  VALUES ('".$this->ls_codemp."','".$as_numordcom."','".$as_estcondat."','".$ls_codestpro1."','".$ls_codestpro2."',".
						" 			  '".$ls_codestpro3."','".$ls_codestpro4."','".$ls_codestpro5."','".$ls_estcla."','".$ls_cuenta."',".$li_moncue.",'".$ls_codfuefin."')";        
				
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->sigesp_soc_c_generar_orden_analisis.php; MÉTODO->uf_insert_cuentas_presupuestarias ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="INSERT";
					$ls_descripcion ="Insertó la Cuenta ".$ls_cuenta." de programatica ".$ls_codpro." a la orden de compra ".$as_numordcom. " Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				}
			}
		}
		return $lb_valido;
	}// end function uf_insert_cuentas_presupuestarias
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cuentas_presupuestarias($as_numcot,$as_coditem,$as_numsep,$ls_tipsolcot,$ab_vienesep,$as_codpro)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cuentas_presupuestarias
		//		   Access: public
		//	    Arguments: $as_coditem-->codigo del item
		//		return	 : arreglo con las cuentas de gasto asociadas a un item
		//	  Description: Metodo que  retorna  las cuentas de gasto asociadas a un item
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 23/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_cuentas=array();
		$lb_valido=false;
		if($ab_vienesep)//Si viene de una sep
		{
			if($ls_tipsolcot=="B")
			{
				$ls_sql="SELECT sep_dt_articulos.spg_cuenta, 
				                sep_dt_articulos.codestpro1, 
								sep_dt_articulos.codestpro2, 
				                sep_dt_articulos.codestpro3, 
								sep_dt_articulos.codestpro4, 
								sep_dt_articulos.codestpro5,
								sep_dt_articulos.estcla,
								sep_dt_articulos.canart as cantidad,
								sep_solicitud.codfuefin as codfuefin
						   FROM sep_dt_articulos, soc_solcotsep, soc_cotizacion,sep_solicitud
						  WHERE sep_dt_articulos.codemp = '".$this->ls_codemp."' 
						    AND soc_cotizacion.numcot = '".$as_numcot."' 
							AND	soc_cotizacion.cod_pro = '".$as_codpro."' 
							AND sep_dt_articulos.codart = '".$as_coditem."' 
							AND	soc_solcotsep.numsol = '".$as_numsep."' 
							AND soc_cotizacion.codemp = soc_solcotsep.codemp 
							AND soc_cotizacion.numsolcot = soc_solcotsep.numsolcot 
							AND soc_solcotsep.codemp = sep_dt_articulos.codemp
							AND soc_solcotsep.numsol = sep_dt_articulos.numsol
							AND sep_solicitud.codemp = sep_dt_articulos.codemp
							AND sep_solicitud.numsol = sep_dt_articulos.numsol";
			}
			else
			{
				$ls_sql="SELECT sep_dt_servicio.spg_cuenta, 
				                sep_dt_servicio.codestpro1, 
								sep_dt_servicio.codestpro2, 
								sep_dt_servicio.codestpro3, 
								sep_dt_servicio.codestpro4, 
								sep_dt_servicio.codestpro5,
								sep_dt_servicio.estcla,
								sep_dt_servicio.canser as cantidad,
								sep_solicitud.codfuefin as codfuefin
						   FROM sep_dt_servicio, soc_solcotsep, soc_cotizacion,sep_solicitud
						  WHERE sep_dt_servicio.codemp = '".$this->ls_codemp."' 
						    AND soc_cotizacion.numcot = '".$as_numcot."' 
							AND	soc_cotizacion.cod_pro = '".$as_codpro."' 
							AND sep_dt_servicio.codser = '".$as_coditem."'
							AND soc_solcotsep.numsol = '".$as_numsep."' 
							AND soc_cotizacion.codemp = soc_solcotsep.codemp
							AND soc_cotizacion.numsolcot = soc_solcotsep.numsolcot 
							AND soc_solcotsep.codemp = sep_dt_servicio.codemp
							AND	soc_solcotsep.numsol = sep_dt_servicio.numsol
							AND sep_solicitud.codemp = sep_dt_servicio.codemp
							AND sep_solicitud.numsol = sep_dt_servicio.numsol";
			}
		}
		else//Si no viene de una sep
		{
			if($ls_tipsolcot=="B")
			{
				$ls_sql="SELECT siv_articulo.spg_cuenta, 
				                spg_dt_unidadadministrativa.codestpro1, 
								spg_dt_unidadadministrativa.codestpro2, 
								spg_dt_unidadadministrativa.codestpro3, 
								spg_dt_unidadadministrativa.codestpro4, 
								spg_dt_unidadadministrativa.codestpro5,
								spg_dt_unidadadministrativa.estcla,
								soc_sol_cotizacion.codfuefin
						   FROM siv_articulo, spg_unidadadministrativa, spg_dt_unidadadministrativa, soc_sol_cotizacion, soc_cotizacion, soc_dtsc_bienes
						  WHERE siv_articulo.codemp = '".$this->ls_codemp."' 
						    AND	siv_articulo.codart = '".$as_coditem."'
							AND soc_cotizacion.numcot = '".$as_numcot."' 
							AND soc_cotizacion.cod_pro= '".$as_codpro."' 
						    AND siv_articulo.codemp = soc_sol_cotizacion.codemp 
						    AND siv_articulo.codart = soc_dtsc_bienes.codart
						    AND soc_cotizacion.codemp = soc_sol_cotizacion.codemp 
						    AND soc_cotizacion.numsolcot = soc_sol_cotizacion.numsolcot 
						    AND soc_cotizacion.cod_pro=soc_dtsc_bienes.cod_pro
						    AND spg_unidadadministrativa.codemp = spg_dt_unidadadministrativa.codemp 
						    AND spg_unidadadministrativa.coduniadm = spg_dt_unidadadministrativa.coduniadm
						    AND soc_dtsc_bienes.codemp=soc_sol_cotizacion.codemp   
						    AND soc_dtsc_bienes.numsolcot=soc_sol_cotizacion.numsolcot
						    AND soc_dtsc_bienes.codemp=spg_dt_unidadadministrativa.codemp 
						    AND soc_dtsc_bienes.coduniadm=spg_dt_unidadadministrativa.coduniadm
						    AND soc_dtsc_bienes.codestpro1=spg_dt_unidadadministrativa.codestpro1
						    AND soc_dtsc_bienes.codestpro2=spg_dt_unidadadministrativa.codestpro2
						    AND soc_dtsc_bienes.codestpro3=spg_dt_unidadadministrativa.codestpro3
						    AND soc_dtsc_bienes.codestpro4=spg_dt_unidadadministrativa.codestpro4
						    AND soc_dtsc_bienes.codestpro5=spg_dt_unidadadministrativa.codestpro5
						    AND soc_dtsc_bienes.estcla=spg_dt_unidadadministrativa.estcla";
			}
			else
			{
				$ls_sql="SELECT soc_servicios.spg_cuenta, 
				                spg_dt_unidadadministrativa.codestpro1, 
								spg_dt_unidadadministrativa.codestpro2, 
								spg_dt_unidadadministrativa.codestpro3, 
								spg_dt_unidadadministrativa.codestpro4, 
								spg_dt_unidadadministrativa.codestpro5,
								spg_dt_unidadadministrativa.estcla,
								soc_sol_cotizacion.codfuefin
						   FROM soc_servicios, spg_unidadadministrativa, spg_dt_unidadadministrativa, soc_sol_cotizacion, soc_cotizacion, soc_dtsc_servicios
						  WHERE soc_servicios.codemp = '".$this->ls_codemp."' 
						    AND soc_servicios.codser = '".$as_coditem."' 
							AND	soc_cotizacion.numcot = '".$as_numcot."' 
							AND soc_cotizacion.cod_pro= '".$as_codpro."'
							AND soc_servicios.codemp = soc_sol_cotizacion.codemp 
						    AND soc_servicios.codser = soc_dtsc_servicios.codser
						    AND soc_cotizacion.codemp = soc_sol_cotizacion.codemp 
						    AND soc_cotizacion.numsolcot = soc_sol_cotizacion.numsolcot 
						    AND soc_cotizacion.cod_pro=soc_dtsc_servicios.cod_pro
						    AND spg_unidadadministrativa.codemp = spg_dt_unidadadministrativa.codemp 
						    AND spg_unidadadministrativa.coduniadm = spg_dt_unidadadministrativa.coduniadm
						    AND soc_dtsc_servicios.codemp=soc_sol_cotizacion.codemp   
						    AND soc_dtsc_servicios.numsolcot=soc_sol_cotizacion.numsolcot
						    AND soc_dtsc_servicios.codemp=spg_dt_unidadadministrativa.codemp 
						    AND soc_dtsc_servicios.coduniadm=spg_dt_unidadadministrativa.coduniadm
						    AND soc_dtsc_servicios.codestpro1=spg_dt_unidadadministrativa.codestpro1
						    AND soc_dtsc_servicios.codestpro2=spg_dt_unidadadministrativa.codestpro2
						    AND soc_dtsc_servicios.codestpro3=spg_dt_unidadadministrativa.codestpro3
						    AND soc_dtsc_servicios.codestpro4=spg_dt_unidadadministrativa.codestpro4
						    AND soc_dtsc_servicios.codestpro5=spg_dt_unidadadministrativa.codestpro5
						    AND soc_dtsc_servicios.estcla=spg_dt_unidadadministrativa.estcla";
			}
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_cuentas_presupuestarias".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))//
			{
				$ls_codestpro1 = str_pad(trim($row["codestpro1"]),25,0,0);
				$ls_codestpro2 = str_pad(trim($row["codestpro2"]),25,0,0);
				$ls_codestpro3 = str_pad(trim($row["codestpro3"]),25,0,0);
				$ls_codestpro4 = str_pad(trim($row["codestpro4"]),25,0,0);
				$ls_codestpro5 = str_pad(trim($row["codestpro5"]),25,0,0);
				if($ab_vienesep)//Si viene de una sep
				{
					$la_cuentas[$li_i]["cantidad"]       = $row["cantidad"];
				}
				$la_cuentas[$li_i]["estcla"]       = $row["estcla"];
				$la_cuentas[$li_i]["spg_cuenta"]   = trim($row["spg_cuenta"]);
				$la_cuentas[$li_i]["programatica"] = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
				$la_cuentas[$li_i]["codfuefin"]       = $row["codfuefin"];
				$li_i++;
			}			
		}
		return $la_cuentas;	
	}//fin de uf_select_cuentas_presupuestarias
    //---------------------------------------------------------------------------------------------------------------------------------------	
    //---------------------------------------------------------------------------------------------------------------------------------------
    function uf_insert_cuentas_cargos($as_numordcom,$as_estcondat,$aa_items,$li_totrow,$aa_seguridad,$ab_vienesep,$as_unidad,$as_numcot,$as_codpro,$as_numanacot,$as_tipsolcot,$ls_estpagele,$ls_unidad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_cuentas_cargos
		//		   Access: private
		//	    Arguments: as_numordcom  ---> numero de la orden de compra
		//                 as_estcondat  ---> estatus de la orden de compra  bienes o servicios
		//				   ai_totrowcuentascargo  ---> filas del grid cuentas cargos
		//				   ai_totrowcargos  ---> filas del grid de los creditos
		//				   aa_seguridad  ---> variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: este metodo inserta la cuentas de los cargos asociadas a una orden de compra
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Modificado Por: Ing. Yozelin Barrgan, Ing Laura Cabre
		// Fecha Creación: 24/06/2007 								Fecha Última Modificación : 01/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_dscargos->data=array();	
		$ls_tipafeiva = $_SESSION["la_empresa"]["confiva"];
		$ls_estceniva=$_SESSION["la_empresa"]["estceniva"];
		if($ls_estceniva=="1")
		{
			$arrResultado= $this->uf_load_estructura_central($ls_unidad,$ls_codestprocen1,$ls_codestprocen2,$ls_codestprocen3,$ls_codestprocen4,$ls_codestprocen5,$ls_esclacen);
			$ls_codestprocen1 = $arrResultado['as_codestprocen1'];
			$ls_codestprocen2 = $arrResultado['as_codestprocen2'];
			$ls_codestprocen3 = $arrResultado['as_codestprocen3'];
			$ls_codestprocen4 = $arrResultado['as_codestprocen4'];
			$ls_codestprocen5 = $arrResultado['as_codestprocen5'];
			$ls_esclacen = $arrResultado['as_esclacen'];
			$lb_valido = $arrResultado['lb_valido'];

			
			$ls_codestprocen=$ls_codestprocen1.$ls_codestprocen2.$ls_codestprocen3.$ls_codestprocen4.$ls_codestprocen5;
		}
		for($li_i=1;($li_i<=$li_totrow)&&($lb_valido);$li_i++)
		{
			$ls_codart=$aa_items[$li_i]["codigo"];
			$li_totalgeneral = $aa_items[$li_i]["cantidad"];
			//$ls_numsep=$aa_items[$li_i]["numsep"];
			$la_numsep  = $this->uf_select_items_sep($as_numcot,$as_codpro,$as_numanacot,$as_tipsolcot,$ls_codart);
			for ($li_z=0;$li_z<count((array)$la_numsep);$li_z++)
			{
					$ls_numsep = $la_numsep[$li_z]["numsep"];
	
					////////////////////////////////////////////////////////////////////////////////////
					// código agregado a solicitud LicP no tiene cargos y los agregan en el análisis de cotización
					// igual debe calcular el cargo para gregarlo a la orden de compra. Anibal Barraez
					// Si la SE
					////////////////////////////////////////////////////////////////////////////////////
//					if($ab_vienesep){
//						$la_cargos=$this->uf_select_cargos_sep($ls_codart,$ls_numsep,$as_estcondat);
//						 $ls_cantidad    = $la_cargos["cantidad"];
//						  if ($ls_cantidad<=$li_totalgeneral)
//						  {
//								$li_totalgeneral=$li_totalgeneral-$ls_cantidad;
//						  }
//						  else
//						  {
//							$ls_cantidad= $li_totalgeneral;
//							$li_totalgeneral=0;
//						  }
//						////////////////////////////////////////////////////////////////////////////////////
//						// código agregado a solicitud Lic. Anibal Barraez
//						// Si la SEP no tiene cargos y los agregan en el análisis de cotización
//						// igual debe calcular el cargo para gregarlo a la orden de compra
//						////////////////////////////////////////////////////////////////////////////////////
//						if(empty($la_cargos)){
//							$la_cargos=$this->uf_select_cargos_cot($as_numcot,$as_codpro,$ls_codart,$as_estcondat);
//							$ls_cantidad    = $aa_items[$li_i]["cantidad"];
//						}
//						
//					}
//					else{
//						$la_cargos=$this->uf_select_cargos_cot($as_numcot,$as_codpro,$ls_codart,$as_estcondat);
//						$ls_cantidad    = $aa_items[$li_i]["cantidad"];
//						
//					}
			$la_cargos=$this->uf_select_cargos_cot($as_numcot,$as_codpro,$ls_codart,$as_estcondat,$ls_estpagele);
			$ls_cantidad    = $aa_items[$li_i]["cantidad"];
				if(count((array)$la_cargos))
				{
					$ls_codcar  = $la_cargos["codcar"];
					$ld_bascar  = ($aa_items[$li_i]["precio"]) * ($aa_items[$li_i]["cantidad"]);
					$ld_monto   = $ls_cantidad  * $aa_items[$li_i]["precio"];
					$ls_formula = str_replace('$LD_MONTO',$ld_monto,$la_cargos["formula"]);
					eval('$ld_moncar ='.$ls_formula.";");	
					$ls_formulacargo = $la_cargos["formula"];		
					//if(($ls_estceniva=="1")&&(!$ab_vienesep)&&($ls_codestprocen!=""))
					if(($ls_estceniva=="1")&&($ls_codestprocen!=""))
					{
						$ls_codpro= $ls_codestprocen;
						$ls_estcla=$ls_esclacen;
					}
					else
					{
						$ls_codpro = trim($la_cargos["codestpro1"]).trim($la_cargos["codestpro2"]).trim($la_cargos["codestpro3"]).trim($la_cargos["codestpro4"]).trim($la_cargos["codestpro5"]);
//						$ls_codpro       = $la_cargos["codestpro1"."codestpro2"."codestpro3"."codestpro4"."codestpro5"];
						$ls_estcla       = $la_cargos["estcla"];
					}
					$ls_spg_cuenta   = $la_cargos["spg_cuenta"];
					$ls_codfuefin   = $la_cargos["codfuefin"];
	/*				$ld_moncar= number_format($ld_moncar,3,'.','');*/
					$ld_moncar= number_format($ld_moncar,2,'.','');
					$this->io_dscargos->insertRow("codcar",$ls_codcar);	
					$this->io_dscargos->insertRow("monobjret",$ld_bascar);	
					$this->io_dscargos->insertRow("monret",$ld_moncar);	
					$this->io_dscargos->insertRow("formula",$ls_formulacargo);
					$this->io_dscargos->insertRow("codestpro",$ls_codpro);
					$this->io_dscargos->insertRow("estcla",$ls_estcla);	
					$this->io_dscargos->insertRow("spg_cuenta",$ls_spg_cuenta);
					$this->io_dscargos->insertRow("codfuefin",$ls_codfuefin);
				}
			}			
		}
		$this->io_dscargos->group_by(array('0'=>'codestpro','1'=>'spg_cuenta','2'=>'estcla','3'=>'codcar','4'=>'codfuefin'),array('0'=>'monobjret','1'=>'monret'),'monobjret');
		$li_totrow=$this->io_dscargos->getRowCount("codcar");
		if ($ls_tipafeiva=='P')
		   {
				for($li_i=1;($li_i<=$li_totrow)&&($lb_valido);$li_i++)
				{
					$ls_codcargo   = $this->io_dscargos->getValue("codcar",$li_i);
					$ls_codpro     = $this->io_dscargos->getValue("codestpro",$li_i);
					$ls_estcla     = $this->io_dscargos->getValue("estcla",$li_i);
					$ls_spg_cuenta = trim($this->io_dscargos->getValue("spg_cuenta",$li_i));
					$ld_monobjret  = $this->io_dscargos->getValue("monobjret",$li_i);
					$ld_monret     = $this->io_dscargos->getValue("monret",$li_i);
					$ls_formula    = $this->io_dscargos->getValue("formula",$li_i);		
					$ls_codfuefin    = $this->io_dscargos->getValue("codfuefin",$li_i);		
					$ls_codestpro1 = substr($ls_codpro,0,25);
					$ls_codestpro2 = substr($ls_codpro,25,25);
					$ls_codestpro3 = substr($ls_codpro,50,25);
					$ls_codestpro4 = substr($ls_codpro,75,25);
					$ls_codestpro5 = substr($ls_codpro,100,25);
					$ls_sc_cuenta  = "";
					$arrResultado=$this->uf_select_cuentacontable($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_spg_cuenta,$ls_estcla,$ls_sc_cuenta);
					$ls_sc_cuenta = $arrResultado['as_sccuenta'];
					$lb_valido = $arrResultado['lb_valido'];
					if($lb_valido)
					{
						$ld_monret=number_format($ld_monret,3,'.','');
						$ld_monret=number_format($ld_monret,2,'.','');
						$ls_sql="INSERT INTO soc_solicitudcargos (codemp, numordcom,  estcondat, codcar, monobjret, monret, codestpro1, ".
								"                                 codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, sc_cuenta, ".
								"								  formula, monto,codfuefin) ".
								"	  VALUES ('".$this->ls_codemp."','".$as_numordcom."','".$as_estcondat."','".$ls_codcargo."',".$ld_monobjret.", ".
								"			  ".$ld_monret.",'".$ls_codestpro1."','".$ls_codestpro2."','".$ls_codestpro3."', ".
								" 			  '".$ls_codestpro4."','".$ls_codestpro5."','".$ls_estcla."','".$ls_spg_cuenta."','".$ls_sc_cuenta."','".$ls_formula."', ".
								"			   ".$ld_monret.",'".$ls_codfuefin."')";
						$li_row=$this->io_sql->execute($ls_sql);
						if($li_row===false)
						{
							$lb_valido=false;
							$this->io_mensajes->message("CLASE->sigesp_soc_c_generar_orden_analisis.php; MÉTODO->uf_insert_cuentas_cargos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
						}
						else
						{
						    $lb_valido=true;
							/////////////////////////////////         SEGURIDAD               /////////////////////////////		
							$ls_evento="INSERT";
							$ls_descripcion ="Insertó la Cuenta ".$ls_spg_cuenta." de programatica ".$ls_codpro."Tipo = ".$ls_estcla." al cargo ".$ls_codcargo." de la orden de compra  ".$as_numordcom. " Asociado a la empresa ".$this->ls_codemp;
							$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
															$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
															$aa_seguridad["ventanas"],$ls_descripcion);
							/////////////////////////////////         SEGURIDAD               /////////////////////////////		
						}
					}
					else
					{
						$this->io_mensajes->message("ERROR-> La cuenta Presupuestaria ".$ls_spg_cuenta." No tiene cuenta contable asociada."); 
					}
				}
			}
			elseif($ls_tipafeiva=='C')
			{
			  for ($li_i=1;($li_i<=$li_totrow)&&($lb_valido);$li_i++)
			     {
				   $ls_codcargo   = $this->io_dscargos->getValue("codcar",$li_i);
				   $ls_codctascg  = $this->io_dscargos->getValue("spg_cuenta",$li_i);
				   $ld_monobjret  = $this->io_dscargos->getValue("monobjret",$li_i);
				   $ld_monret	  = $this->io_dscargos->getValue("monret",$li_i);
				   $ls_formula	  = $this->io_dscargos->getValue("formula",$li_i);
				   $ls_codfuefin    = $this->io_dscargos->getValue("codfuefin",$li_i);		
				 /*  $ls_codestpro1 = $this->io_dscargos->getValue("codestpro1",$li_i);
				   $ls_codestpro2 = $this->io_dscargos->getValue("codestpro2",$li_i);
				   $ls_codestpro3 = $this->io_dscargos->getValue("codestpro3",$li_i);
				   $ls_codestpro4 = $this->io_dscargos->getValue("codestpro4",$li_i);
				   $ls_codestpro5 = $this->io_dscargos->getValue("codestpro5",$li_i);
				   $ls_estcla = $this->io_dscargos->getValue("estcla",$li_i);*/
				   $ls_codestpro1 = '-------------------------';
				   $ls_codestpro2 = '-------------------------';
				   $ls_codestpro3 = '-------------------------';
				   $ls_codestpro4 = '-------------------------';
				   $ls_codestpro5 = '-------------------------';
				   $ls_estcla ='-';
				   
				   $ld_monret=number_format($ld_monret,3,'.','');
				   $ld_monret=number_format($ld_monret,2,'.','');
		           $ls_sql        = "INSERT INTO soc_solicitudcargos (codemp,numordcom,estcondat,codcar,monobjret,monret,
				                                                      codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,
																	  estcla,spg_cuenta, sc_cuenta,formula, monto,codfuefin)
					    			 VALUES ('".$this->ls_codemp."','".$as_numordcom."','".$as_estcondat."','".$ls_codcargo."',
							    			 ".$ld_monobjret.",".$ld_monret.",'".$ls_codestpro1."','".$ls_codestpro2."',
							    			 '".$ls_codestpro3."','".$ls_codestpro4."','".$ls_codestpro5."','".$ls_estcla."',
							    			 '".$ls_codctascg."','".$ls_codctascg."','".$ls_formula."',".$ld_monret.",'".$ls_codfuefin."')";
				   $rs_data = $this->io_sql->execute($ls_sql);
				   if ($rs_data===false)
				      {
					    $lb_valido = false;
						$this->io_mensajes->message("CLASE->sigesp_soc_c_generar_orden_analisis.php(Iva Contable);MÉTODO->uf_insert_cuentas_cargos (Iva Contable);ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					  } 
				   else
				      {
					     $lb_valido = true;
						 /////////////////////////////////         SEGURIDAD               /////////////////////////////		
						 $ls_evento="INSERT";
						 $ls_descripcion ="Insertó la Cuenta Contable ".$ls_codctascg." al cargo ".$ls_codcargo." de la orden de compra  ".$as_numordcom. " de tipo ".$as_estcondat." Asociado a la empresa ".$this->ls_codemp;
						 $lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
						 /////////////////////////////////         SEGURIDAD               /////////////////////////////		
					  }
				 }
			}// fin del if de $ls_tipafeiva

		return $lb_valido;

	}// end function uf_insert_cuentas_cargos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_estructura_central($as_coduniadm,$as_codestprocen1,$as_codestprocen2,$as_codestprocen3,$as_codestprocen4,$as_codestprocen5,$as_esclacen)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_fecha_sep
		//		   Access: private
		//		 Argument: $ad_fecregsol // fecha de registro dee solicitud de la nueva sep
		//	  Description: Función que busca la fecha de la última sep y la compara con la fecha actual
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$as_codestprocen1="";
		$as_codestprocen2="";
		$as_codestprocen3="";
		$as_codestprocen4="";
		$as_codestprocen5="";
		$as_esclacen="";
		$lb_valido=true;
		$ls_sql="SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla ".
				"  FROM spg_dt_unidadadministrativa  ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND coduniadm='".$as_coduniadm."' ".
				"   AND central='1' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_validar_fecha_sep ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_codestprocen1=$row["codestpro1"];
				$as_codestprocen2=$row["codestpro2"];
				$as_codestprocen3=$row["codestpro3"];
				$as_codestprocen4=$row["codestpro4"];
				$as_codestprocen5=$row["codestpro5"];
				$as_esclacen=$row["estcla"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['as_codestprocen1']=$as_codestprocen1;
		$arrResultado['as_codestprocen2']=$as_codestprocen2;
		$arrResultado['as_codestprocen3']=$as_codestprocen3;
		$arrResultado['as_codestprocen4']=$as_codestprocen4;
		$arrResultado['as_codestprocen5']=$as_codestprocen5;
		$arrResultado['as_esclacen']=$as_esclacen;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_validar_fecha_sep
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_cuentas($as_numordcom,$as_estcom,$as_estcondat)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_cuentas
		//		   Access: private
		//		 Argument: as_numordcom ---> mumero de la orden de compra
		//				   as_estcom  ---> estatus de la orden de compra
		//                 as_estcondat ---> tipo de la orden de compra bienes o servicios
		//	  Description: Función que busca que las cuentas presupuestarias estén en la programática seleccionada
		//				   de ser asi coloca la sep en emitida sino la coloca en registrada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Modificado Por: Ing. Yozelin Barragan
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 12/05/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, TRIM(spg_cuenta) AS spg_cuenta, monto, ".
				"	    (SELECT (asignado-(comprometido+precomprometido)+aumento-disminucion) ".
				"		   FROM spg_cuentas ".
				"		  WHERE spg_cuentas.codemp = soc_cuentagasto.codemp ".
				"			AND spg_cuentas.codestpro1 = soc_cuentagasto.codestpro1 ".
				"		    AND spg_cuentas.codestpro2 = soc_cuentagasto.codestpro2 ".
				"		    AND spg_cuentas.codestpro3 = soc_cuentagasto.codestpro3 ".
				"		    AND spg_cuentas.codestpro4 = soc_cuentagasto.codestpro4 ".
				"		    AND spg_cuentas.codestpro5 = soc_cuentagasto.codestpro5 ".
				"		    AND spg_cuentas.estcla = soc_cuentagasto.estcla ".
				"			AND spg_cuentas.spg_cuenta = soc_cuentagasto.spg_cuenta) AS disponibilidad, ".		
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
				"  FROM soc_cuentagasto  ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numordcom='".$as_numordcom."' ".
				"   AND estcondat='".$as_estcondat."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_generar_orden_analisis.php; MÉTODO->uf_validar_cuentas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$lb_existe=true;
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_existe))
			{
				$ls_estcla     = trim($row["estcla"]);
				$ls_codestpro1 = trim($row["codestpro1"]);
				$ls_codestpro2 = trim($row["codestpro2"]);
				$ls_codestpro3 = trim($row["codestpro3"]);
				$ls_codestpro4 = trim($row["codestpro4"]);
				$ls_codestpro5 = trim($row["codestpro5"]);
				$ls_spg_cuenta = trim($row["spg_cuenta"]);
				$li_monto      = $row["monto"];
				$li_disponibilidad=$row["disponibilidad"];
				$li_existe=$row["existe"];
				if($li_existe>0)
				{
					if($li_monto>$li_disponibilidad)
					{
						$li_monto=number_format($li_monto,2,",",".");
						$li_disponibilidad=number_format($li_disponibilidad,2,",",".");
						$this->io_mensajes->message("No hay Disponibilidad en la cuenta ".$ls_spg_cuenta." Disponible=[".$li_disponibilidad."] Cuenta=[".$li_monto."]"); 
					}
				}
				else
				{
					$lb_existe = false;
					$this->io_mensajes->message("La cuenta ".$ls_spg_cuenta." No Existe en la Estructura ".$ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3.'-'.$ls_codestpro4.'-'.$ls_codestpro5."; Tipo = ".$ls_estcla); 
				}
				
			}
			$this->io_sql->free_result($rs_data);	
			if($lb_existe)
			{
				$as_estcom=1; // EMITIDA SE DEBE CAMBIAR EN LETRAS (E)
			}
			else
			{
				$as_estcom=0; // REGISTRO SE DEBE CAMBIAR EN LETRAS (R)
			}
			$ls_sql="UPDATE soc_ordencompra ".
					"   SET estcom='".$as_estcom."' ".
					" WHERE codemp = '".$this->ls_codemp."' AND ".
					"	    numordcom = '".$as_numordcom."' AND ".
					"       estcondat= '".$as_estcondat."'  ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->sigesp_soc_c_generar_orden_analisis.php; MÉTODO->uf_validar_cuentas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}			
		}
		$arrResultado['as_estcom']=$as_estcom;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_validar_cuentas
	//-----------------------------------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cargos($as_coditem,$ls_tipsolcot,$ls_estpagele)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cargos
		//		   Access: public
		//	    Arguments: $as_coditem-->codigo del item
		//		return	 : arreglo con los cargos asociados al item
		//	  Description: Metodo que  retorna los cargos asociados al item
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 22/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_capiva=$_SESSION["la_empresa"]["capiva"];
		$ls_parcapiva=trim($_SESSION["la_empresa"]["parcapiva"]);
		$la_cargos=array();
		$lb_valido=false;
		if($ls_tipsolcot=="B")
		{				
			if($ls_capiva=="1")
			{
				$ls_sql="SELECT siv_articulo.codart, sigesp_cargos.codcar, sigesp_cargos.dencar, sigesp_cargos.estcla,".
						"		TRIM(siv_articulo.spg_cuenta) AS spg_cuenta, sigesp_cargos.formula, sigesp_cargos.codestpro ".
						"  FROM sigesp_cargos, siv_cargosarticulo,siv_articulo ".
						" WHERE siv_articulo.codemp = '".$this->ls_codemp."' ".
						"   AND siv_articulo.codart = '".$as_coditem."' ".
						"   AND sigesp_cargos.estpagele='".$ls_estpagele."'".
						"   AND siv_articulo.codemp = siv_cargosarticulo.codemp ".
						"   AND siv_articulo.codart = siv_cargosarticulo.codart ".
						"	AND sigesp_cargos.codemp = siv_cargosarticulo.codemp ".
						"   AND sigesp_cargos.codcar = siv_cargosarticulo.codcar ";
				$la_spg_cuenta=explode(",",$ls_parcapiva);
				$li_total=count((array)$la_spg_cuenta);
				for($li_i=0;$li_i<$li_total;$li_i++)
				{
					if($li_i==0)
					{
						$ls_sql=$ls_sql."   AND (siv_articulo.spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
					}
					else
					{
						$ls_sql=$ls_sql."    OR siv_articulo.spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
					}
				
				}
				if($li_total>0)
				{
					$ls_sql=$ls_sql." )";
				}
				$rs_data=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_load_cargosbienes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					return false;
				}
		
			}
			else
			{
				$ls_sql= "SELECT s.codart, s.codcar, c.formula,c.codestpro,c.estcla, c.spg_cuenta 
							FROM siv_cargosarticulo s, sigesp_cargos c
						   WHERE s.codemp='$this->ls_codemp' 
						     AND c.estpagele='$ls_estpagele'
							 AND s.codart='$as_coditem' 
							 AND s.codemp=c.codemp
							 AND s.codcar=c.codcar";
			}
		}
		else
		{
			if($ls_capiva=="1")
			{
				$ls_sql="SELECT soc_servicios.codser , sigesp_cargos.codcar, sigesp_cargos.dencar, sigesp_cargos.estcla,".
						"		TRIM(soc_servicios.spg_cuenta) AS spg_cuenta, sigesp_cargos.formula, sigesp_cargos.codestpro".
						"  FROM sigesp_cargos, soc_serviciocargo,soc_servicios ".
						" WHERE soc_servicios.codemp = '".$this->ls_codemp."' ".
						"   AND soc_servicios.codser = '".$as_coditem."' ".
						"   AND sigesp_cargos.estpagele='".$ls_estpagele."'".
						"	AND soc_servicios.codemp = soc_serviciocargo.codemp ".
						"   AND soc_servicios.codser = soc_serviciocargo.codser ".
						"	AND sigesp_cargos.codemp = soc_serviciocargo.codemp ".
						"   AND sigesp_cargos.codcar = soc_serviciocargo.codcar ";
				$la_spg_cuenta=explode(",",$ls_parcapiva);
				$li_total=count((array)$la_spg_cuenta);
				for($li_i=0;$li_i<$li_total;$li_i++)
				{
					if($li_i==0)
					{
						$ls_sql=$ls_sql."   AND (soc_servicios.spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
					}
					else
					{
						$ls_sql=$ls_sql."    OR soc_servicios.spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
					}
				
				}
				if($li_total>0)
				{
					$ls_sql=$ls_sql." )";
				}
				$rs_data=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_load_cargosbienes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					return false;
				}
		
			}
			else
			{
				
				$ls_sql= "SELECT s.codser, s.codcar, c.formula ,c.codestpro, c.estcla, c.spg_cuenta 
							FROM soc_serviciocargo s, sigesp_cargos c
						   WHERE s.codemp='$this->ls_codemp' 
						     AND c.estpagele='$ls_estpagele'
							 AND s.codser='$as_coditem' 
							 AND s.codemp=c.codemp
							 AND s.codcar=c.codcar";			
			}
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_cargos".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))//
			{
				$la_cargos=$row;
				unset($row);
				$this->io_sql->free_result($rs_data);				
			}			
		}
		return $la_cargos;	
	}//fin de uf_select_cargos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cargos_cot($as_numcot,$as_codpro,$as_coditem,$ls_tipsolcot,$ls_estpagele)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cargos_cot
		//		   Access: public
		//	    Arguments: $as_coditem-->codigo del item
		//		return	 : arreglo con los cargos asociados al item
		//	  Description: Metodo que  retorna los cargos asociados al item
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 22/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_capiva=$_SESSION["la_empresa"]["capiva"];
		$ls_parcapiva=trim($_SESSION["la_empresa"]["parcapiva"]);
		$la_cargos=array();
		$lb_valido=false;
		$lb_estivared=$this->uf_select_estatus_ivared($ls_tipsolcot,$as_coditem);
		if($lb_estivared)
		{
			$ls_estpagele="0";
		}
		if($ls_tipsolcot=="B")
		{				
			if($ls_capiva=="1")
			{
				$ls_sql="SELECT siv_articulo.codart, sigesp_cargos.codcar, sigesp_cargos.dencar, sigesp_cargos.estcla,".
						"		TRIM(siv_articulo.spg_cuenta) AS spg_cuenta, sigesp_cargos.formula, sigesp_cargos.codestpro,'--' AS codfuefin ".
						"  FROM sigesp_cargos, siv_cargosarticulo,siv_articulo ".
						" WHERE siv_articulo.codemp = '".$this->ls_codemp."' ".
						"   AND siv_articulo.codart = '".$as_coditem."' ".
						"   AND sigesp_cargos.estpagele = '".$ls_estpagele."' ".
						"   AND siv_articulo.codemp = siv_cargosarticulo.codemp ".
						"   AND siv_articulo.codart = siv_cargosarticulo.codart ".
						"	AND sigesp_cargos.codemp = siv_cargosarticulo.codemp ".
						"   AND sigesp_cargos.codcar = siv_cargosarticulo.codcar ";
				$la_spg_cuenta=explode(",",$ls_parcapiva);
				$li_total=count((array)$la_spg_cuenta);
				for($li_i=0;$li_i<$li_total;$li_i++)
				{
					if($li_i==0)
					{
						$ls_sql=$ls_sql."   AND (siv_articulo.spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
					}
					else
					{
						$ls_sql=$ls_sql."    OR siv_articulo.spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
					}
				
				}
				if($li_total>0)
				{
					$ls_sql=$ls_sql." )";
				}
				$rs_data=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_load_cargosbienes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					return false;
				}
		
			}
	
			if(($rs_data->EOF)||($ls_capiva!="1"))
			{
				$ls_sql= "SELECT siv_cargosarticulo.codart, siv_cargosarticulo.codcar, sigesp_cargos.formula,
								 soc_dtsc_bienes.codestpro1,soc_dtsc_bienes.codestpro2,soc_dtsc_bienes.codestpro3,soc_dtsc_bienes.codestpro4,
								 soc_dtsc_bienes.codestpro5,soc_dtsc_bienes.estcla, sigesp_cargos.spg_cuenta ,soc_sol_cotizacion.codfuefin
							FROM siv_cargosarticulo, sigesp_cargos, soc_cotizacion, soc_dtsc_bienes,soc_sol_cotizacion
						   WHERE siv_cargosarticulo.codemp='$this->ls_codemp' 
							 AND soc_cotizacion.numcot='$as_numcot' 
							 AND sigesp_cargos.estpagele='$ls_estpagele' 
							 AND soc_cotizacion.cod_pro='$as_codpro' 
							 AND siv_cargosarticulo.codart='$as_coditem' 
							 AND siv_cargosarticulo.codemp=sigesp_cargos.codemp
							 AND siv_cargosarticulo.codcar=sigesp_cargos.codcar
							 AND soc_cotizacion.codemp= soc_dtsc_bienes.codemp
							 AND soc_cotizacion.numsolcot= soc_dtsc_bienes.numsolcot
							 AND siv_cargosarticulo.codart=soc_dtsc_bienes.codart
							 AND soc_sol_cotizacion.codemp= soc_dtsc_bienes.codemp
							 AND soc_sol_cotizacion.numsolcot= soc_dtsc_bienes.numsolcot";
			}
		}
		else
		{
			if($ls_capiva=="1")
			{
				$ls_sql="SELECT soc_servicios.codser , sigesp_cargos.codcar, sigesp_cargos.dencar, sigesp_cargos.estcla,".
						"		TRIM(soc_servicios.spg_cuenta) AS spg_cuenta, sigesp_cargos.formula, sigesp_cargos.codestpro,'--' AS codfuefin".
						"  FROM sigesp_cargos, soc_serviciocargo,soc_servicios ".
						" WHERE soc_servicios.codemp = '".$this->ls_codemp."' ".
						"   AND soc_servicios.codser = '".$as_coditem."' ".
						"   AND sigesp_cargos.estpagele = '".$ls_estpagele."' ".
						"	AND soc_servicios.codemp = soc_serviciocargo.codemp ".
						"   AND soc_servicios.codser = soc_serviciocargo.codser ".
						"	AND sigesp_cargos.codemp = soc_serviciocargo.codemp ".
						"   AND sigesp_cargos.codcar = soc_serviciocargo.codcar ";
				$la_spg_cuenta=explode(",",$ls_parcapiva);
				$li_total=count((array)$la_spg_cuenta);
				for($li_i=0;$li_i<$li_total;$li_i++)
				{
					if($li_i==0)
					{
						$ls_sql=$ls_sql."   AND (soc_servicios.spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
					}
					else
					{
						$ls_sql=$ls_sql."    OR soc_servicios.spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
					}
				
				}
				if($li_total>0)
				{
					$ls_sql=$ls_sql." )";
				}
				$rs_data=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_load_cargosbienes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					return false;
				}
		
			}
	
			if(($rs_data->EOF)||($ls_capiva!="1"))
			{
				
				$ls_sql= "SELECT soc_serviciocargo.codser, soc_serviciocargo.codcar, sigesp_cargos.formula,
								 soc_dtsc_servicios.codestpro1,soc_dtsc_servicios.codestpro2,soc_dtsc_servicios.codestpro3,soc_dtsc_servicios.codestpro4,
								 soc_dtsc_servicios.codestpro5,soc_dtsc_servicios.estcla, sigesp_cargos.spg_cuenta ,soc_sol_cotizacion.codfuefin
							FROM soc_serviciocargo, sigesp_cargos, soc_cotizacion, soc_dtsc_servicios,soc_sol_cotizacion
						   WHERE soc_serviciocargo.codemp='$this->ls_codemp' 
							 AND soc_cotizacion.numcot='$as_numcot' 
							 AND soc_cotizacion.cod_pro='$as_codpro' 
							 AND sigesp_cargos.estpagele='$ls_estpagele' 
							 AND soc_serviciocargo.codser='$as_coditem' 
							 AND soc_serviciocargo.codemp=sigesp_cargos.codemp
							 AND soc_serviciocargo.codcar=sigesp_cargos.codcar
							 AND soc_cotizacion.codemp= soc_dtsc_servicios.codemp
							 AND soc_cotizacion.numsolcot= soc_dtsc_servicios.numsolcot
							 AND soc_serviciocargo.codser=soc_dtsc_servicios.codser
							 AND soc_sol_cotizacion.codemp= soc_dtsc_servicios.codemp
							 AND soc_sol_cotizacion.numsolcot= soc_dtsc_servicios.numsolcot";

			}
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_cargos_cot".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))//
			{
				$la_cargos=$row;
				unset($row);
				$this->io_sql->free_result($rs_data);				
			}			
		}
		return $la_cargos;	
	}//fin de uf_select_cargos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_estatus_ivared($ls_tipsolcot,$as_coditem)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_estatus_ivared
		//		   Access: public
		//	    Arguments: $as_coditem-->codigo del item
		//				   $as_numsep--->numero de la sep a la cual esta asociada el item
		//				   $ls_tipsolcot--->Si es de bienes o de servicio
		//		return	 : arreglo con los cargos asociados al item, si la solicitud esta asociada a una sep
		//	  Description: Metodo que  retorna los cargos asociados al item, si la solicitud esta asociada a una sep
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 22/06/2007								Fecha Última Modificación : 13/11/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_estivared=false;
		if($ls_tipsolcot=="B")
		{				
			$ls_sql="SELECT estpagele
					   FROM sigesp_cargos,siv_cargosarticulo
					  WHERE siv_cargosarticulo.codemp = '$this->ls_codemp' AND
						    siv_cargosarticulo.codart = '".trim($as_coditem)."' AND
						    sigesp_cargos.estpagele = '0' AND
							sigesp_cargos.codemp = siv_cargosarticulo.codemp AND 
							sigesp_cargos.codcar = siv_cargosarticulo.codcar";	
		}
		else
		{
			$ls_sql="SELECT estpagele
					   FROM sigesp_cargos,soc_serviciocargo
					  WHERE soc_serviciocargo.codemp = '$this->ls_codemp' AND
						    soc_serviciocargo.codser = '".trim($as_coditem)."' AND
						    sigesp_cargos.estpagele = '0' AND
							sigesp_cargos.codemp = soc_serviciocargo.codemp AND 
							sigesp_cargos.codcar = soc_serviciocargo.codcar";	
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_cargos_sep".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
		  if ($row=$this->io_sql->fetch_row($rs_data))//
			 {
				$lb_estivared=true;
			 }			
		   $this->io_sql->free_result($rs_data);
		}
		return $lb_estivared;	
	}//fin de uf_select_cargos_sep

	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cargos_sep($as_coditem,$as_numsep,$ls_tipsolcot)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cargos_sep
		//		   Access: public
		//	    Arguments: $as_coditem-->codigo del item
		//				   $as_numsep--->numero de la sep a la cual esta asociada el item
		//				   $ls_tipsolcot--->Si es de bienes o de servicio
		//		return	 : arreglo con los cargos asociados al item, si la solicitud esta asociada a una sep
		//	  Description: Metodo que  retorna los cargos asociados al item, si la solicitud esta asociada a una sep
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 22/06/2007								Fecha Última Modificación : 13/11/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_cargos=array();
		$lb_valido=false;
		if($ls_tipsolcot=="B")
		{				
			$ls_sql="SELECT dta.formula, dta.codcar, sep_solicitud.codfuefin, 
			                sc.codestpro1,sc.codestpro2,sc.codestpro3,sc.codestpro4,sc.codestpro5,sc.estcla,sc.spg_cuenta, sep_dt_articulos.canart as cantidad
					   FROM sep_dta_cargos dta, sep_solicitudcargos sc, sep_dt_articulos,sep_solicitud
					  WHERE dta.codemp = '$this->ls_codemp' AND
						    dta.codart = '".trim($as_coditem)."' AND
						    dta.numsol = '".trim($as_numsep)."' AND
						    dta.codemp = sc.codemp AND
						    dta.numsol = sc.numsol AND
						    dta.spg_cuenta = sc.spg_cuenta AND
						    dta.codcar = sc.codcar AND 
							dta.codemp = sep_dt_articulos.codemp AND 
							dta.numsol = sep_dt_articulos.numsol AND
							dta.codart = sep_dt_articulos.codart AND
							dta.codemp = sep_solicitud.codemp AND
							dta.numsol = sep_solicitud.numsol ";	
		}
		else
		{
			$ls_sql= "SELECT dta.formula, dta.codcar, sc.codestpro1,sc.codestpro2,sc.codestpro3,sc.codestpro4,sc.codestpro5,
							 sc.estcla,sc.spg_cuenta, sep_dt_servicio.canser as cantidad, sep_solicitud.codfuefin
					    FROM sep_dts_cargos dta, sep_solicitudcargos sc, sep_dt_servicio,sep_solicitud
					   WHERE dta.codemp = '$this->ls_codemp' 
					     AND dta.codser = '".trim($as_coditem)."' 
					     AND dta.numsol = '".trim($as_numsep)."' 
					     AND dta.codemp = sc.codemp 
						 AND dta.numsol = sc.numsol 
						 AND dta.spg_cuenta = sc.spg_cuenta 
						 AND dta.codcar = sc.codcar	AND		
							 dta.codemp = sep_dt_servicio.codemp AND 
							 dta.numsol = sep_dt_servicio.numsol AND
							 dta.codser = sep_dt_servicio.codser AND 
							 dta.codemp = sep_solicitud.codemp AND
							 dta.numsol = sep_solicitud.numsol ";	
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_cargos_sep".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
		  if ($row=$this->io_sql->fetch_row($rs_data))//
			 {
//			   $ls_codestpro1 = str_pad(trim($row["codestpro1"]),25,0,0);
//			   $ls_codestpro2 = str_pad(trim($row["codestpro2"]),25,0,0);
//			   $ls_codestpro3 = str_pad(trim($row["codestpro3"]),25,0,0);
//			   $ls_codestpro4 = str_pad(trim($row["codestpro4"]),25,0,0);
//			   $ls_codestpro5 = str_pad(trim($row["codestpro5"]),25,0,0);
				
			   $la_cargos["codestpro1"] = str_pad(trim($row["codestpro1"]),25,0,0);
			   $la_cargos["codestpro2"] = str_pad(trim($row["codestpro2"]),25,0,0);
			   $la_cargos["codestpro3"] = str_pad(trim($row["codestpro3"]),25,0,0);
			   $la_cargos["codestpro4"] = str_pad(trim($row["codestpro4"]),25,0,0);
			   $la_cargos["codestpro5"] = str_pad(trim($row["codestpro5"]),25,0,0);
			  // $la_cargos["codestpro"]  = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;;				
			   $la_cargos["formula"]    = $row["formula"];
			   $la_cargos["spg_cuenta"] = trim($row["spg_cuenta"]);
			   $la_cargos["codcar"]     = $row["codcar"];
			   $la_cargos["estcla"]     = $row["estcla"];
			   $la_cargos["codfuefin"]  = $row["codfuefin"];
			   $la_cargos["cantidad"]     = $row["cantidad"];
			   unset($row);
			 }			
		   $this->io_sql->free_result($rs_data);
		}
		return $la_cargos;	
	}//fin de uf_select_cargos_sep
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cuentacontable($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_spgcuenta,$as_estcla,$as_sccuenta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cuentacontable
		//		   Access: private
		//	    Arguments: as_codestpro1  --->  Còdigo de Estructura Programàtica
		//	    		   as_codestpro2  --->  Còdigo de Estructura Programàtica
		//	    		   as_codestpro3  --->  Còdigo de Estructura Programàtica
		//	    		   as_codestpro4  --->  Còdigo de Estructura Programàtica
		//	    		   as_codestpro5  --->  Còdigo de Estructura Programàtica
		//	    		   as_spgcuenta   --->  Cuentas Presupuestarias
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que obtiene la cuenta contable 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_sccuenta="";
		$ls_sql="SELECT sc_cuenta ".
				"  FROM spg_cuentas ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codestpro1='".trim($as_codestpro1)."' ".
				"   AND codestpro2='".trim($as_codestpro2)."' ".
				"   AND codestpro3='".trim($as_codestpro3)."' ".
				"   AND codestpro4='".trim($as_codestpro4)."' ".
				"   AND codestpro5='".trim($as_codestpro5)."' ".
				"   AND spg_cuenta='".trim($as_spgcuenta)."' ".
				"   AND estcla='".$as_estcla."'";// print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_generar_orden_analisis.php; MÉTODO->uf_select_cuentacontable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_sccuenta=$row["sc_cuenta"];
				unset($row);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['as_sccuenta']=$as_sccuenta;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_select_cuentacontable
//---------------------------------------------------------------------------------------------------------------------------------------	
//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_bienes_servicios($as_coditem,$as_tipo,$as_codpro,$as_numcot)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_bienes_servicios
		//		   Access: public
		//		  return :	arreglo que contiene algunos datos basicos que faltan de los bienes/servicios
		//	  Description: Metodo que  devuelve algunos datos basicos que faltan de los bienes/servicios
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 21/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_datos=array();
		$lb_valido=false;
		if($as_tipo=="B")
		{
			$ls_sql= "SELECT a.spg_cuenta, d.unidad 
					    FROM siv_articulo a, soc_dtcot_bienes d
					   WHERE a.codemp='$this->ls_codemp' 
					     AND a.codemp=d.codemp
						 AND a.codart='$as_coditem' 
						 AND d.cod_pro='$as_codpro' 
						 AND d.numcot='$as_numcot' 
						 AND a.codart=d.codart";				
		}
		else
		{
			$ls_sql= "SELECT a.spg_cuenta
						FROM soc_servicios a, soc_dtcot_servicio d
					   WHERE a.codemp='$this->ls_codemp' 
					     AND a.codemp=d.codemp
						 AND a.codser='$as_coditem' 
						 AND d.cod_pro='$as_codpro' 
						 AND d.numcot='$as_numcot' 
						 AND a.codser=d.codser";	
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_bienes_servicios".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;	
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$aa_datos["spg_cuenta"]=$row["spg_cuenta"];	
				
				if(array_key_exists("unidad",$row))
					$aa_datos["unidad"]=$row["unidad"];					
			}																
		}
		return $aa_datos;
	}//fin de uf_select_bienes_servicios
//---------------------------------------------------------------------------------------------------------------------------------------		
//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_unidad_bienes_servicios($as_coditem,$as_tipo,$as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_unidad_bienes_servicios
		//		   Access: public
		//		  return :	arreglo que contiene algunos datos basicos que faltan de los bienes/servicios
		//	  Description: Metodo que  devuelve algunos datos basicos que faltan de los bienes/servicios
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 21/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_unidad="";
		$lb_valido=false;
		if($as_tipo=="B")
		{
			$ls_sql= "SELECT unidad
					    FROM sep_dt_articulos
					   WHERE sep_dt_articulos.codemp='$this->ls_codemp' 
						 AND sep_dt_articulos.codart='$as_coditem' 
						 AND sep_dt_articulos.numsol='$as_numsol'";				
		}
		else
		{
			$ls_sql= "SELECT a.spg_cuenta
						FROM soc_servicios a, soc_dtcot_servicio d
					   WHERE a.codemp='$this->ls_codemp' 
					     AND a.codemp=d.codemp
						 AND a.codser='$as_coditem' 
						 AND d.cod_pro='$as_codpro' 
						 AND d.numcot='$as_numcot' 
						 AND a.codser=d.codser";	
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_bienes_servicios".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;	
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
					$ls_unidad=$row["unidad"];					
			}																
		}
		return $ls_unidad;
	}//fin de uf_select_unidad_bienes_servicios
//---------------------------------------------------------------------------------------------------------------------------------------		

//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_fuentefinanciamiento_sep($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_fuentefinanciamiento_sep
		//		   Access: public
		//		  return :	arreglo que contiene algunos datos basicos que faltan de los bienes/servicios
		//	  Description: Metodo que  devuelve algunos datos basicos que faltan de los bienes/servicios
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 21/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codfuefin="";
		$lb_valido=false;
		$ls_sql= "SELECT sep_solicitud.codfuefin
					FROM sep_solicitud
				   WHERE sep_solicitud.codemp='$this->ls_codemp' 
					 AND sep_solicitud.numsol='$as_numsol'";				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_fuentefinanciamiento_sep".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;	
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
					$ls_codfuefin=$row["codfuefin"];					
			}																
		}
		return $ls_codfuefin;
	}//fin de uf_select_unidad_bienes_servicios
//---------------------------------------------------------------------------------------------------------------------------------------		
//---------------------------------------------------------------------------------------------------------------------------------------
	function  uf_select_items_cotizacion($as_numcot,$as_codpro,$as_numanacot,$as_tipsolcot,$aa_items,$li_i)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_items
		//		   Access: public
		//		  return :	arreglo que contiene los items que participaron en un determinado analisis, de manera combinada en caso de que
		//					los items se repitan 
		//	  Description: Metodo que  devuelve los items que participaron en un determinado analisis, de manera combinada en caso de que
		//					los items se repitan 
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 10/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$aa_items=array();
		$lb_valido=false;
		if($as_tipsolcot=="B")
		{				
			$ls_sql="SELECT d.codart as codigo, MAX(a.denart) as denominacion, MAX(p.nompro) as nompro, MAX(dt.canart) as cantidad, 
							MAX(dt.preuniart) as precio, MAX(dt.moniva) as moniva,MAX(dt.montotart) as monto, MAX(d.obsanacot) as obsanacot, 
							MAX(d.numcot) as numcot, MAX(d.cod_pro) as cod_pro, MAX(soc_dtsc_bienes.numsep) as numsep, MAX(soc_dtsc_bienes.coduniadm) as coduniadm,
							MAX(soc_dtsc_bienes.codestpro1) as codestpro1,MAX(soc_dtsc_bienes.codestpro2) as codestpro2,
						    MAX(soc_dtsc_bienes.codestpro3) as codestpro3,MAX(soc_dtsc_bienes.codestpro4) as codestpro4,MAX(soc_dtsc_bienes.codestpro5) as codestpro5,
							MAX(soc_dtsc_bienes.estcla) as estcla,MAX(soc_sol_cotizacion.codfuefin) as codfuefin
				       FROM soc_dtac_bienes d,siv_articulo a, rpc_proveedor p,soc_dtcot_bienes dt, soc_sol_cotizacion,
					        soc_dtsc_bienes, soc_cotizacion
					  WHERE d.codemp='".$this->ls_codemp."' 
					    AND d.numanacot='".$as_numanacot."' 
						AND dt.cod_pro='".$as_codpro."' 
						AND dt.numcot='".$as_numcot."' 
					    AND soc_cotizacion.codemp=soc_sol_cotizacion.codemp    
					    AND soc_cotizacion.numsolcot=soc_sol_cotizacion.numsolcot 
					    AND soc_sol_cotizacion.codemp=soc_dtsc_bienes.codemp
					    AND soc_sol_cotizacion.numsolcot=soc_dtsc_bienes.numsolcot
					    AND soc_dtsc_bienes.codemp=dt.codemp
					    AND soc_dtsc_bienes.codart=dt.codart
					    AND soc_dtsc_bienes.codemp=d.codemp
					    AND soc_dtsc_bienes.codart=d.codart  
					    AND d.codemp=soc_cotizacion.codemp
					    AND d.numcot=soc_cotizacion.numcot
					    AND d.codemp=a.codemp 
					    AND a.codemp=p.codemp 
					    AND p.codemp=dt.codemp 
					    AND d.codart=a.codart 
					    AND d.cod_pro=p.cod_pro 
					    AND d.numcot=dt.numcot 
					    AND d.cod_pro=dt.cod_pro 
					    AND d.codart=dt.codart
					  GROUP BY d.codart";
		}
		else
		{
			$ls_sql="SELECT MAX(d.codser) as codigo, MAX(a.denser) as denominacion, MAX(p.nompro) as nompro, MAX(dt.canser) as cantidad,
							MAX(dt.monuniser) as precio, MAX(dt.moniva) as moniva,MAX(dt.montotser) as monto,
							MAX(d.obsanacot) as obsanacot, MAX(d.numcot) as numcot, MAX(d.cod_pro) as cod_pro,MAX(soc_dtsc_servicios.numsep) as numsep,
							MAX(soc_dtsc_servicios.coduniadm) as coduniadm,MAX(soc_dtsc_servicios.codestpro1) as codestpro1,
							MAX(soc_dtsc_servicios.codestpro2) as codestpro2,MAX(soc_dtsc_servicios.codestpro3) as codestpro3,
							MAX(soc_dtsc_servicios.codestpro4) as codestpro4,MAX(soc_dtsc_servicios.codestpro5) as codestpro5,
							MAX(soc_dtsc_servicios.estcla) as estcla,MAX(soc_sol_cotizacion.codfuefin) as codfuefin
					   FROM soc_dtac_servicios d,soc_servicios a, rpc_proveedor p,soc_dtcot_servicio dt, soc_sol_cotizacion,
							soc_dtsc_servicios, soc_cotizacion
					  WHERE d.codemp='".$this->ls_codemp."' 
						AND d.numanacot='".$as_numanacot."'
						AND dt.cod_pro='".$as_codpro."'
						AND dt.numcot='".$as_numcot."' 
						AND soc_cotizacion.codemp=soc_sol_cotizacion.codemp    
						AND soc_cotizacion.numsolcot=soc_sol_cotizacion.numsolcot 
						AND soc_sol_cotizacion.codemp=soc_dtsc_servicios.codemp
						AND soc_sol_cotizacion.numsolcot=soc_dtsc_servicios.numsolcot
						AND soc_dtsc_servicios.codemp=dt.codemp
						AND soc_dtsc_servicios.codser=dt.codser
						AND soc_dtsc_servicios.codemp=d.codemp
						AND soc_dtsc_servicios.codser=d.codser  
						AND d.codemp=soc_cotizacion.codemp
						AND d.numcot=soc_cotizacion.numcot
						AND d.codemp=a.codemp 
						AND a.codemp=p.codemp 
						AND p.codemp=dt.codemp 
						AND d.codser=a.codser 
						AND d.cod_pro=p.cod_pro 
						AND d.numcot=dt.numcot 
						AND d.cod_pro=dt.cod_pro 
						AND d.codser=dt.codser
					  GROUP BY d.codser";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASS->sigesp_soc_c_generar_orden_analisis.php->Metodo->uf_select_items_cotizacion.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))//Se verifica si la solicitud es de bienes o de servicios
			{
				$li_i++;
				$aa_items[$li_i]=$row;					
			}																
		    unset($row);
			$this->io_sql->free_result($rs_data); 
		}
		$arrResultado['aa_items']=$aa_items;
		$arrResultado['li_i']=$li_i;
		return $arrResultado;
	}
	
	//--------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------
	function uf_insert_cargos($as_numordcom,$as_estcondat,$aa_seguridad,$as_coditem,$ad_monbasimp,$as_monimp,$as_monto,$as_numsep,
							  $as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,$ls_estpagele)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_cargos
		//		   Access: private
		//	    Arguments: as_numordcom  ---> número de la orden de compra		
		//                 as_estcondat  ---> estatus de la orden de compra  bienes o servicios
		//				   aa_seguridad  ---> arreglo con los parametros de seguridad
		//	      Returns: true si se insertaron los cargos correctamente o false en caso contrario
		//	  Description: Funcion que inserta los cargos de una Orden de Compra en la tabla segun el tipo de la orden 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Modificado Por. Yozelin Barragan 
		// Fecha Creación: 17/03/2007 								Fecha Última Modificación : 12/05/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$la_cargos=$this->uf_select_cargos($as_coditem,$as_estcondat,$ls_estpagele);
		$lb_valido=true;
		if(count((array)$la_cargos)>0)
			{
			switch($as_estcondat)
			{
				case "B": // si es de Bienes
					$ls_tabla="soc_dta_cargos";
					$ls_campo="codart";
				break;
				
				case "S": // si es de Servicios
					$ls_tabla="soc_dts_cargos";
					$ls_campo="codser";
				break;
			}	
			$ls_codcar=$la_cargos["codcar"];
			$ls_formulacargo=$la_cargos["formula"];	
	
			$ls_sql="INSERT INTO ".$ls_tabla." (codemp, numordcom, estcondat, ".$ls_campo.", codcar, numsol,monbasimp, monimp,".
					" 						    monto, formula, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla)".
					"	  VALUES ('".$this->ls_codemp."','".$as_numordcom."','".$as_estcondat."','".$as_coditem."','".$ls_codcar."','".$as_numsep."',".
					" 			  ".$ad_monbasimp.",".$as_monimp.",".$as_monto.",'".$ls_formulacargo."','".$as_codestpro1."','".$as_codestpro2."',".
					"			  '".$as_codestpro3."','".$as_codestpro4."','".$as_codestpro5."','".$as_estcla."')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->sigesp_soc_c_generar_orden_analisis.php;MÉTODO->uf_insert_cargos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó el Cargo ".$ls_codcar." a la Orden de Compra ".$as_numordcom. "Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			}
		}
	
		return $lb_valido;
	}// end function uf_insert_cargos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------------------
	function uf_select_solicitud($as_numanacot,$as_concepto,$as_unidad,$as_uniejeaso,$as_tipbiesolcot,$as_recanacot)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_solicitud
		//		   Access: public
		//		  return : variable con el concepto de la solicitud de cotizacion y la unidad ejecutora
		//	  Description: Metodo que  devuelve el concepto de la solicitud de cotizacion y la unidad ejecutora
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 31/10/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$as_concepto = array();
		$lb_valido   = true;
		$ls_sql = "SELECT soc_sol_cotizacion.uniejeaso,soc_sol_cotizacion.consolcot,
		                  soc_sol_cotizacion.coduniadm, soc_sol_cotizacion.tipsolbie,soc_analisicotizacion.recanacot
					 FROM soc_sol_cotizacion , soc_analisicotizacion
					WHERE soc_sol_cotizacion.codemp = '".$this->ls_codemp."'
					  AND soc_analisicotizacion.numanacot = '".$as_numanacot."'
					  AND soc_analisicotizacion.codemp = soc_sol_cotizacion.codemp
					  AND soc_analisicotizacion.numsolcot = soc_sol_cotizacion.numsolcot";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->uf_select_solicitud".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
		  if ($row=$this->io_sql->fetch_row($rs_data))
			 {
			   $as_concepto     = $row["consolcot"];
			   $as_uniejeaso    = $row["uniejeaso"];	
			   $as_unidad       = $row["coduniadm"];
			   $as_tipbiesolcot = $row["tipsolbie"];
			   $as_recanacot = $row["recanacot"];
			}																
		}		
		$arrResultado['as_tipbiesolcot']=$as_tipbiesolcot;
		$arrResultado['as_uniejeaso']=$as_uniejeaso;
		$arrResultado['as_unidad']=$as_unidad;
		$arrResultado['as_concepto']=$as_concepto;
		$arrResultado['as_recanacot']=$as_recanacot;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}//fin de uf_select_solicitud
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_select_unidades_ejecutoras($as_numanacot, $ab_viene_sep,$aa_items, $ai_totrow,$aa_unidades,$as_concepto,$as_unidad,
	                                       $as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
										   $as_numcot,$as_codpro,$as_tipsolcot)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_unidades_ejecutoras
		//		   Access: public
		//			Param: as_numanacot---->numero del analisis de cotizacion
		//				   ab_viene_sep---->variable que indica si la solicitud posee sep asociadas.
		//				   aa_items---->arreglo con los items, es usado en caso de q la variable anterior venga en true
		//				   ai_totrow--->cantidad de items
		//		  return :	arreglo con la(s) unidad(es) ejecutora(s), una variable con el concepto de la colicitud de cotizacion
		//					y una variable con la unidad ejecutora a ser guardada en la cabecera de la orden de compra
		//	   Creado Por: Ing. Laura Cabré
		// 			  Fecha: 30/10/2007								Fecha Última Modificación : 11/11/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_unidades = array();
		$lb_valido=true;
		$as_concepto="";
		require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
		$this->io_dsunidades = new class_datastore();
		if($ab_viene_sep)//Si la solicitud de cotizacion tiene asociada al menos una sep
		{
			$la_sep = array();
			$ai_totrow=count((array)$aa_items);
			$li_j=0;
			//Se obtienen las sep a las cuales estan asociados los items que formaran parte de la orden de compra
			for($li_i=1; $li_i<=$ai_totrow; $li_i++)
			{
				$ls_codart = $aa_items[$li_i]["codigo"];
				$la_numsep  = $this->uf_select_items_sep($as_numcot,$as_codpro,$as_numanacot,$as_tipsolcot,$ls_codart);
				for ($li_z=0;$li_z<count((array)$la_numsep);$li_z++)
				{
					$la_sep[$li_j] = $la_numsep[$li_z]["numsep"];
					$li_j++;
				}
			}
			$la_sep = array_unique($la_sep);//se eliminan los repetidos	
			sort($la_sep);
			$li_j=0;
			for($li_i=0; $li_i<count((array)$la_sep); $li_i++)
			{
				$ls_sep = $la_sep[$li_i];
				$ls_sql = "SELECT soc_solcotsep.numsol, 
				                  soc_solcotsep.codunieje,
								  soc_solcotsep.codestpro1,
								  soc_solcotsep.codestpro2,
								  soc_solcotsep.codestpro3,
								  soc_solcotsep.codestpro4,
								  soc_solcotsep.codestpro5,
								  soc_solcotsep.estcla,
								  spg_unidadadministrativa.denuniadm,soc_sol_cotizacion.consolcot
					         FROM soc_solcotsep, soc_analisicotizacion, spg_unidadadministrativa,soc_sol_cotizacion
					        WHERE soc_solcotsep.codemp = '".$this->ls_codemp."'
					          AND soc_analisicotizacion.numanacot = '".$as_numanacot."'
					          AND soc_solcotsep.numsol = '".$ls_sep."'
					          AND soc_analisicotizacion.codemp = soc_solcotsep.codemp
							  AND soc_analisicotizacion.codemp = soc_sol_cotizacion.codemp
					          AND soc_analisicotizacion.numsolcot = soc_solcotsep.numsolcot
							  AND soc_analisicotizacion.numsolcot = soc_sol_cotizacion.numsolcot
							  AND soc_solcotsep.codemp = spg_unidadadministrativa.codemp
					          AND soc_solcotsep.codunieje = spg_unidadadministrativa.coduniadm";
				$rs_data=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("ERROR->uf_select_unidades_ejecutoras ".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$lb_valido=false;	
				}
				else
				{				
				  if ($row=$this->io_sql->fetch_row($rs_data))
					 {
					   $aa_unidades[$li_j]=$row;
					   $this->io_dsunidades->insertRow("codunieje",$row["codunieje"]);
					   $this->io_dsunidades->insertRow("codestpro1",$row["codestpro1"]);
					   $this->io_dsunidades->insertRow("codestpro2",$row["codestpro2"]);
					   $this->io_dsunidades->insertRow("codestpro3",$row["codestpro3"]);
					   $this->io_dsunidades->insertRow("codestpro4",$row["codestpro4"]);
					   $this->io_dsunidades->insertRow("codestpro5",$row["codestpro5"]);
					   $this->io_dsunidades->insertRow("estcla",$row["estcla"]);
					   $as_concepto =$row["consolcot"];	
					   //$as_concepto = $as_concepto."Nro. SEP:".$row["numsol"].".Unidad Ejecutora:".$row["codunieje"]." - ".$row["denuniadm"].";  ";	
					   $li_j++;
					}	
				} 
			}
			$la_campos = array("codunieje","codestpro1","codestpro2","codestpro3","codestpro4","codestpro5","estcla");
			$la_monto  = array("monto");
		    $this->io_dsunidades->group_by($la_campos,$la_monto,"monto");
			$li_totrowuni = $this->io_dsunidades->getRowCount("codunieje");
			if ($li_totrowuni==1)
			   { 
				 $as_unidad     = $this->io_dsunidades->getValue("codunieje",1);
				 $as_codestpro1 = $this->io_dsunidades->getValue("codestpro1",1);
				 $as_codestpro2 = $this->io_dsunidades->getValue("codestpro2",1);
				 $as_codestpro3 = $this->io_dsunidades->getValue("codestpro3",1);
				 $as_codestpro4 = $this->io_dsunidades->getValue("codestpro4",1);
				 $as_codestpro5 = $this->io_dsunidades->getValue("codestpro5",1);
				 $as_estcla     = $this->io_dsunidades->getValue("estcla",1);
				// $as_concepto   = "";
			   }				
			else
			   {
				 $as_unidad     = "----------";
				 $as_codestpro1 = "-------------------------";
				 $as_codestpro2 = "-------------------------";
				 $as_codestpro3 = "-------------------------";
				 $as_codestpro4 = "-------------------------";
				 $as_codestpro5 = "-------------------------"; 
				 $as_estcla     = "-";
			   }
		    unset($this->io_dsunidades);
		}
		else//En caso de que la solicitud no este asociada a alguna sep, se busca la unidad ejecutora de la solicitud
		{
			$ls_sql = "SELECT c.coduniadm,c.codestpro1,c.codestpro2,c.codestpro3,c.codestpro4,c.codestpro5,c.estcla,c.consolcot
						 FROM soc_analisicotizacion a, soc_sol_cotizacion c
						WHERE a.codemp = '$this->ls_codemp'
						  AND a.numanacot = '$as_numanacot'
					 	  AND a.codemp = c.codemp
						  AND a.numsolcot = c.numsolcot";
			$rs_data=$this->io_sql->select($ls_sql);					
			if($rs_data===false)
			{
				$this->io_mensajes->message("ERROR->uf_select_unidades_ejecutoras 2".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$lb_valido=false;	
			}
			else
			{				
			  if ($row=$this->io_sql->fetch_row($rs_data))
				 {
				   $as_unidad     = trim($row["coduniadm"]);
				   $as_codestpro1 = trim($row["codestpro1"]);
				   $as_codestpro2 = trim($row["codestpro2"]);
				   $as_codestpro3 = trim($row["codestpro3"]);
				   $as_codestpro4 = trim($row["codestpro4"]);
				   $as_codestpro5 = trim($row["codestpro5"]);
				   $as_estcla     = $row["estcla"];
				   $as_concepto   = $row["consolcot"];
				 }																
			}			
		}
		$arrResultado['aa_unidades']=$aa_unidades;
		$arrResultado['as_concepto']=$as_concepto;
		$arrResultado['as_unidad']=$as_unidad;
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['as_estcla']=$as_estcla;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}//fin de uf_select_unidades_ejecutoras
	
	//---------------------------------------------------------------------------------------------------------------------------------------	
	//---------------------------------------------------------------------------------------------------------------------------------------	
	function uf_insert_enlace_sep($as_numordcom,$as_estcondat,$as_estcom,$aa_unidades,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_enlace_sep
		//		   Access: private
		//	    Arguments: as_numordcom  ---> número de la Orden de Compra
		//                 as_estcondat  ---> estatus de la orden de compra  bienes o servicios
		//				   ai_totrowbienes  ---> total de filas de bienes
		//                 as_estcom   ---> estatus de la orden de compra 
		//				   aa_seguridad  ---> arreglo con los parametros de seguridad
		//	      Returns: true si se insertaron los bienes correctamente o false en caso contrario
		//	  Description: este metodo inserta los bienes de una   orden de compra
		//	   Creado Por: Ing. Yozelin Barragan
		// Modificado por: Ing. Laura Cabre
		// Fecha Creación: 12/05/2007 								Fecha Última Modificación : 30/10/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_total = count((array)$aa_unidades);
		for($li_fila=0;$li_fila<$li_total;$li_fila++)
		{
			$ls_numsol     = $aa_unidades[$li_fila]["numsol"];
			$ls_estcla     = $aa_unidades[$li_fila]["estcla"];
			$ls_codunieje  = $aa_unidades[$li_fila]["codunieje"];
			$ls_codestpro1 = str_pad(trim($aa_unidades[$li_fila]["codestpro1"]),25,0,0);
			$ls_codestpro2 = str_pad(trim($aa_unidades[$li_fila]["codestpro2"]),25,0,0);
			$ls_codestpro3 = str_pad(trim($aa_unidades[$li_fila]["codestpro3"]),25,0,0);
			$ls_codestpro4 = str_pad(trim($aa_unidades[$li_fila]["codestpro4"]),25,0,0);
			$ls_codestpro5 = str_pad(trim($aa_unidades[$li_fila]["codestpro5"]),25,0,0);
			
			$ls_sql=" INSERT INTO soc_enlace_sep (codemp, numordcom, estcondat, numsol, estordcom, coduniadm, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla)".
					"  VALUES ('".$this->ls_codemp."','".$as_numordcom."','".$as_estcondat."','".$ls_numsol."','".$as_estcom."','".$ls_codunieje."','".$ls_codestpro1."', 
					           '".$ls_codestpro2."','".$ls_codestpro3."','".$ls_codestpro4."','".$ls_codestpro5."','".$ls_estcla."')";                                                                       
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Generar Orden Analisis MÉTODO->uf_insert_enlace_sep ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			    echo $this->io_sql->message;
			}
			else
			{
				if($lb_valido)
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="INSERT";
					$ls_descripcion ="Insertó el enlace de la sep ".$ls_numsol." a la Orden de Compra  ".$as_numordcom." tipo ".$as_estcondat." Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				}
			}
		}
		return $lb_valido;
	}// end function uf_insert_enlace_sep
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_viene_de_sep($as_numcot,$as_codpro, $ab_viene_sep)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_viene_de_sep
		//		   Access: public
		//		  return :	variable que indica si la solicitud esta o no asociada a una sep
		//	  Description: Metodo que indica si la solicitud esta o no asociada a una sep
		//	   Creado Por: Ing. Laura Cabré
		// 			Fecha: 11/11/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ab_viene_sep = false;
		$lb_valido=true;
		if($lb_valido)
		{
			$ls_sql = "SELECT soc_solcotsep.numsol
					     FROM soc_solcotsep, soc_cotizacion
					    WHERE soc_solcotsep.codemp='".$this->ls_codemp."'
						  AND soc_cotizacion.numcot='".$as_numcot."' 
					      AND soc_cotizacion.cod_pro = '".$as_codpro."'
						  AND soc_solcotsep.codemp = soc_cotizacion.codemp
						  AND soc_solcotsep.numsolcot = soc_cotizacion.numsolcot"; 
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("ERROR->uf_viene_de_sep".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$lb_valido=false;	
			}
			else
			{				
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$ab_viene_sep = true;
				}																
			}
		}	
		$arrResultado['ab_viene_sep']=$ab_viene_sep;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}//fin de uf_viene_de_sep

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_orden_compra($as_numordcom,$as_estcondat)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_orden_compra
		//		   Access: private
		//	    Arguments: as_numordcom  --->  Número de la orden de compra
		//                 $as_estcondat --->  Estatus de la orden de compra
		// 	      Returns: true si se existe la orden de compra o false en caso contrario
		//	  Description: Funcion que verifica si existe una orden de compra
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 12/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql=" SELECT numordcom ".
				"   FROM soc_ordencompra ".
				"  WHERE codemp='".$this->ls_codemp."' AND ".
				"        numordcom='".$as_numordcom."' AND ".
				"        estcondat='".$as_estcondat."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_registro_orden_compra.php;MÉTODO->uf_select_orden_compra ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if(!$row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_existe;
	}// end function uf_select_orden_compra
	//-----------------------------------------------------------------------------------------------------------------------------------	
	

}
?>