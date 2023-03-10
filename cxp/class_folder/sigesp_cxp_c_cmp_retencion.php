<?Php
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

class sigesp_cxp_c_cmp_retencion
{
    var $io_function;
    var $la_empresa;
	var $ls_codusu;
    var $io_sql;
    var $io_msg;
    var $io_fec;
	var $io_seguridad;
	var $io_dataprov;
	var $io_datadocu;
	var $io_connect;
	
	public function __construct($as_path)
	{
      	require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
	    require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
	    require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
	    require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
        require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
	    require_once($as_path."base/librerias/php/general/sigesp_lib_fecha.php");
	    require_once($as_path."base/librerias/php/general/sigesp_lib_datastore.php");
		require_once($as_path."shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
         
		$this->io_include=new sigesp_include();
	    $this->io_connect=$this->io_include->uf_conectar();
	    $this->io_dataprov= new class_datastore();
		$this->io_datadocu= new class_datastore();
		$this->io_datavali= new class_datastore();
        $this->io_seguridad= new sigesp_c_seguridad();
	    $this->io_sql= new class_sql($this->io_connect);	
	    $this->io_function= new class_funciones();
	    $this->io_msg= new class_mensajes();
	    $this->io_fec= new class_fecha();
		$this->la_empresa= $_SESSION["la_empresa"];
		$this->ls_codusu= $_SESSION["la_logusr"];
        $this->ls_basdatcmp=$_SESSION["la_empresa"]["basdatcmp"];
        $this->ls_estcanret=$_SESSION["la_empresa"]["estcanret"];//$this->io_connect->debug=true;
		if($this->ls_basdatcmp!="")
		{
			$arrResultado=$this->io_include->uf_obtener_parametros_conexion($as_path,$this->ls_basdatcmp,$as_hostname,$as_login,$as_password,$as_gestor);
			$as_hostname=$arrResultado["as_hostname"];
			$as_login=$arrResultado["as_login"];
			$as_password=$arrResultado["as_password"];
			$as_gestor=$arrResultado["as_gestor"];
			if($as_hostname!="")
			{
				$this->io_keygen->io_conexion=$this->io_include->uf_conectar_otra_bd($as_hostname, $as_login, $as_password,$this->ls_basdatcmp,$as_gestor);
				$this->io_keygen->io_sql=new class_sql($this->io_keygen->io_conexion);
		
				$io_connectaux=$this->io_include->uf_conectar_otra_bd($as_hostname, $as_login, $as_password,$this->ls_basdatcmp,$as_gestor);
				$this->io_sqlaux=new class_sql($io_connectaux);
			}
			else
			{
				$this->io_msg->message("Esta mal configurada la BD integradora");
				print "<script language=JavaScript>";
				print "location.href='sigespwindow_blank.php'";
				print "</script>";		
			}
		}
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}
	
/******************************************************************************************************************/
/***********************************    FUNCIONES DE BUSQUEDA      ************************************************/	
/******************************************************************************************************************/		
	function uf_get_provbene($as_mes,$as_agno,$as_probendesde,$as_probenhasta,$as_tipo,$as_tiporet,$aa_sujret)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function: uf_get_provbene
		//	 Access: public
		//	 Argument: $as_mes // Mes  | $as_agno // A?o
		//             $as_probendesde // Codigo de Proveedor o Benficiario | $as_probenhasta // Codigo de Proveedor o Benficiario
		//             $as_tipo // Indica si se trabaja con proveedores o beneficiarios 
		//             $as_tiporet // Indica el tipo de retencion
		//             $aa_sujret // arreglo con el resultado de la busqueda 
		//  Description: Funci?n que genera una lista con proveedores o beneficiarios con posibilidad de tener movimientos
		//               que ameriten la generacion de un comprobante de retencion  
		//	Creado Por: Ing. Gerardo Cordero
		//  Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 14/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$ld_fecdesde= $this->io_function->uf_convertirdatetobd("01/".$as_mes."/".$as_agno);
		$ld_hasta   = $this->io_fec->uf_last_day($as_mes,$as_agno);
		$ld_fechasta= $this->io_function->uf_convertirdatetobd($ld_hasta);
		$lb_valido  = true;
		
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		$ls_filtrofrom ="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_connect->Concat('DRD.codestpro','DRD.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                "  							WHERE sss_permisos_internos.codemp='{$this->la_empresa["codemp"]}' ".
			                "     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND DRD.codemp = RD.codemp ".
							" AND DRD.numrecdoc = RD.numrecdoc ".
							" AND DRD.codtipdoc = RD.codtipdoc ".
							" AND DRD.ced_bene = RD.ced_bene ".
							" AND DRD.cod_pro = RD.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg DRD ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		
		if($as_tiporet=="I")
		 {
		     $ls_sqlaux = "";
			 if (!empty($as_probendesde) && !empty($as_probenhasta))
			    {
				  $ls_sqlaux = "AND (RD.cod_pro BETWEEN '".$as_probendesde."' AND '".$as_probenhasta."')";
				}
		   if($as_tipo=='P')
		    {
  		     $ls_sql = "SELECT RD.cod_pro,MAX(RDD.codded) AS codded,MAX(RDD.numrecdoc) AS numrecdoc,MAX(PRO.nompro) AS nompro,
			                   MAX(PRO.dirpro) AS dirpro,MAX(PRO.rifpro) AS rifpro,MAX(RD.codproalt) as codproalt
			              FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,rpc_proveedor PRO,
						       cxp_dt_solicitudes DS,cxp_solicitudes SO $ls_filtrofrom
					     WHERE SD.codemp='".$this->la_empresa["codemp"]."'
						   AND SD.iva=1 $ls_sqlaux
						   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."') $ls_filtroest
						   AND RD.estprodoc='C'
						   AND RDD.estcmp='0'
						   AND SD.codemp=RDD.codemp
						   AND SD.codemp=RD.codemp
					       AND SD.codemp=PRO.codemp
						   AND SD.codemp=DS.codemp
						   AND SD.codemp=SO.codemp					       
						   AND SD.codded=RDD.codded
						   AND RDD.numrecdoc=RD.numrecdoc						   
						   AND RD.cod_pro=PRO.cod_pro
						   AND RDD.cod_pro=RD.cod_pro
						   AND RDD.codtipdoc=RD.codtipdoc						   
						   AND RD.numrecdoc=DS.numrecdoc
						   AND RD.cod_pro=DS.cod_pro
						   AND DS.numsol=SO.numsol
					     GROUP BY RD.cod_pro,RD.codproalt";
		    	 
		    }
		    else
		    {
			  $ls_sqlaux = "";
			  if (!empty($as_probendesde) && !empty($as_probenhasta))
			     {
				   $ls_sqlaux = "AND (RD.ced_bene BETWEEN '".$as_probendesde."' AND '".$as_probenhasta."')";
				 }
			 if ((strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqlt")) || (strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqli")))
			 {
		       $ls_parametro="CONCAT(BEN.nombene,BEN.apebene) AS nompro";
		     }
		     elseif(strtoupper($_SESSION["ls_gestor"])==strtoupper("postgres")){
		       $ls_parametro="(MAX(BEN.nombene)||MAX(BEN.apebene)) AS nompro";
		     }
			 $ls_sql =  "SELECT RD.ced_bene as cod_pro,MAX(RDD.codded) AS codded,MAX(RDD.numrecdoc) AS numrecdoc,$ls_parametro,
			                    MAX(BEN.dirbene) as dirpro,MAX(BEN.rifben) as rifpro,RD.codproalt
			               FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,rpc_beneficiario BEN,cxp_dt_solicitudes DS,
						        cxp_solicitudes SO
		  	              WHERE SD.codemp='".$this->la_empresa["codemp"]."'
						    AND SD.iva=1 
							AND RD.estprodoc='C' $ls_sqlaux							 
							AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."') $ls_filtroest
							AND RDD.estcmp='0'
							AND SD.codemp=RDD.codemp
							AND SD.codemp=RD.codemp
							AND SD.codemp=BEN.codemp
							AND SD.codemp=DS.codemp
							AND SD.codemp=SO.codemp					        
							AND SD.codded=RDD.codded
							AND RDD.numrecdoc=RD.numrecdoc							
							AND RD.ced_bene=BEN.ced_bene
							AND RDD.ced_bene=RD.ced_bene 
							AND RDD.codtipdoc=RD.codtipdoc							
							AND RD.numrecdoc=DS.numrecdoc
							AND RD.ced_bene=DS.ced_bene
							AND DS.numsol=SO.numsol
					      GROUP BY RD.ced_bene,RD.codproalt";
		    }
	     }
		 elseif($as_tiporet=="M")
		 {
		 if($as_tipo=='P')
		   {
			$ls_sql =  " SELECT RD.cod_pro,MAX(RDD.codded) AS codded,MAX(RDD.numrecdoc) AS numrecdoc,".
			           " MAX(PRO.nompro) AS nompro,MAX(PRO.dirpro) AS dirpro,MAX(PRO.rifpro) AS rifpro,RD.codproalt".
			           " FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,rpc_proveedor PRO
					     ,cxp_dt_solicitudes DS,cxp_solicitudes SO".
					   " WHERE (SD.codemp='".$this->la_empresa["codemp"]."') AND (SD.codemp=RDD.codemp) AND (SD.codemp=RD.codemp)". 
					   " AND (SD.codemp=PRO.codemp) AND (SD.codemp=DS.codemp) AND (SD.codemp=SO.codemp)".
					   " AND (SD.estretmun=1) AND (SD.codded=RDD.codded) AND (RDD.numrecdoc=RD.numrecdoc) AND RDD.estcmp='0' AND".
					   " (RD.cod_pro BETWEEN '".$as_probendesde."' AND '".$as_probenhasta."') AND".
					   " (RD.cod_pro=PRO.cod_pro) AND (RDD.cod_pro=RD.cod_pro) AND (RDD.codtipdoc=RD.codtipdoc)".
					   " AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."') AND (RD.estprodoc='C') AND".
					   " (RD.numrecdoc=DS.numrecdoc) AND (RD.cod_pro=DS.cod_pro) AND (DS.numsol=SO.numsol)".$ls_filtroest.
					   " GROUP BY RD.cod_pro,RD.codproalt";
		    	   
		       }
		       else
		       {
			     if ((strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqlt")) ||  (strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqli")))
				 {
		            $ls_parametro="CONCAT(BEN.nombene,BEN.apebene) AS nompro";
		          }
		          elseif(strtoupper($_SESSION["ls_gestor"])==strtoupper("postgres")){
		            $ls_parametro="(MAX(BEN.nombene) ||MAX(BEN.apebene)) AS nompro";
		          }
			    
				  $ls_sql = " SELECT RD.ced_bene as cod_pro,MAX(RDD.codded) AS codded,MAX(RDD.numrecdoc)".
				            "  AS numrecdoc,".$ls_parametro.",MAX(BEN.dirbene) as dirpro,MAX(BEN.rifben) as rifpro,RD.codproalt".
			                " FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,rpc_beneficiario BEN".
					        " ,cxp_dt_solicitudes DS,cxp_solicitudes SO".
		  	                " WHERE (SD.codemp='".$this->la_empresa["codemp"]."') AND (SD.codemp=RDD.codemp) AND RDD.estcmp='0' AND". 
					        " (SD.codemp=RD.codemp) AND (SD.codemp=BEN.codemp) AND (SD.codemp=DS.codemp) AND (SD.codemp=SO.codemp)".
					        " AND (SD.estretmun=1) AND (SD.codded=RDD.codded) AND (RDD.numrecdoc=RD.numrecdoc) AND".
					        " (RD.ced_bene BETWEEN '".$as_probendesde."' AND '".$as_probenhasta."') AND".
					        " (RD.ced_bene=BEN.ced_bene) AND (RDD.ced_bene=RD.ced_bene) AND (RDD.codtipdoc=RD.codtipdoc)".
					        " AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."') AND (RD.estprodoc='C') AND".
					        " (RD.numrecdoc=DS.numrecdoc) AND (RD.ced_bene=DS.ced_bene) AND (DS.numsol=SO.numsol)".$ls_filtroest.
					        " GROUP BY RD.ced_bene,RD.codproalt";
		       }
		
		 }
		 elseif($as_tiporet=="A")
		 {
		  if($as_tipo=='P')
		   {
			$ls_sql =  "SELECT RD.cod_pro,MAX(RDD.codded) AS codded,MAX(RDD.numrecdoc) AS numrecdoc,".
			           "       MAX(PRO.nompro) AS nompro,MAX(PRO.dirpro) AS dirpro,MAX(PRO.rifpro) AS rifpro,RD.codproalt".
			           "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,rpc_proveedor PRO,".
					   "        cxp_dt_solicitudes DS,cxp_solicitudes SO".
					   " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
					   "   AND SD.codemp=RDD.codemp".
					   "   AND SD.codemp=RD.codemp". 
					   "   AND SD.codemp=PRO.codemp".
					   "   AND SD.codemp=DS.codemp".
					   "   AND SD.codemp=SO.codemp".
					   "   AND SD.retaposol=1".
					   "   AND SD.codded=RDD.codded".
					   "   AND RDD.numrecdoc=RD.numrecdoc".
					   "   AND (RD.cod_pro BETWEEN '".$as_probendesde."' AND '".$as_probenhasta."')".
					   "   AND RDD.estcmp='0'".
					   "   AND RD.cod_pro=PRO.cod_pro".
					   "   AND RDD.cod_pro=RD.cod_pro".
					   "   AND RDD.codtipdoc=RD.codtipdoc".
					   "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
					   "   AND RD.estprodoc='C'".
					   "   AND RD.numrecdoc=DS.numrecdoc".
					   "   AND RD.cod_pro=DS.cod_pro".
					   "   AND DS.numsol=SO.numsol".$ls_filtroest.
					   " GROUP BY RD.cod_pro,RD.codproalt";
		    	   
		       }
		       else
		       {
			     if ((strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqlt")) || (strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqli")))
				 {
		            $ls_parametro="CONCAT(BEN.nombene,BEN.apebene) AS nompro";
		          }
		          elseif(strtoupper($_SESSION["ls_gestor"])==strtoupper("postgres")){
		            $ls_parametro="(MAX(BEN.nombene) ||MAX(BEN.apebene)) AS nompro";
		          }
			    
				  $ls_sql ="SELECT RD.ced_bene as cod_pro,MAX(RDD.codded) AS codded,MAX(RDD.numrecdoc)".
				           "       AS numrecdoc,".$ls_parametro.",MAX(BEN.dirbene) as dirpro,MAX(BEN.rifben) as rifpro,RD.codproalt".
			               "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,rpc_beneficiario BEN,".
					       "       cxp_dt_solicitudes DS,cxp_solicitudes SO".
		  	               " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
						   "   AND SD.codemp=RDD.codemp ". 
					       "   AND SD.codemp=RD.codemp".
						   "   AND SD.codemp=BEN.codemp".
						   "   AND SD.codemp=DS.codemp".
						   "   AND SD.codemp=SO.codemp".
					       "   AND SD.retaposol=1".
						   "   AND RDD.estcmp='0'".
						   "   AND SD.codded=RDD.codded".
						   "   AND RDD.numrecdoc=RD.numrecdoc".
						   "   AND (RD.ced_bene BETWEEN '".$as_probendesde."' AND '".$as_probenhasta."') ".
					       "   AND RD.ced_bene=BEN.ced_bene".
						   "   AND RDD.ced_bene=RD.ced_bene".
						   "   AND RDD.codtipdoc=RD.codtipdoc".
					       "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
						   "   AND (RD.estprodoc='C') ".
					       "   AND RD.numrecdoc=DS.numrecdoc".
						   "   AND RD.ced_bene=DS.ced_bene".
						   "   AND DS.numsol=SO.numsol".$ls_filtroest.
					       " GROUP BY RD.ced_bene,RD.codproalt";
		       }
		 
		 }
		 elseif($as_tiporet=="1")
		 {
		  if($as_tipo=='P')
		   {
			$ls_sql =  "SELECT RD.cod_pro,MAX(RDD.codded) AS codded,MAX(RDD.numrecdoc) AS numrecdoc,".
			           "       MAX(PRO.nompro) AS nompro,MAX(PRO.dirpro) AS dirpro,MAX(PRO.rifpro) AS rifpro,RD.codproalt".
			           "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,rpc_proveedor PRO,".
					   "        cxp_dt_solicitudes DS,cxp_solicitudes SO".
					   " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
					   "   AND SD.codemp=RDD.codemp".
					   "   AND SD.codemp=RD.codemp". 
					   "   AND SD.codemp=PRO.codemp".
					   "   AND SD.codemp=DS.codemp".
					   "   AND SD.codemp=SO.codemp".
					   "   AND SD.estretmil='1'".
					   "   AND RDD.estcmp='0'".
					   "   AND SD.codded=RDD.codded".
					   "   AND RDD.numrecdoc=RD.numrecdoc".
					   "   AND (RD.cod_pro BETWEEN '".$as_probendesde."' AND '".$as_probenhasta."')".
					   "   AND RD.cod_pro=PRO.cod_pro".
					   "   AND RDD.cod_pro=RD.cod_pro".
					   "   AND RDD.codtipdoc=RD.codtipdoc".
					   "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
					   "   AND RD.estprodoc='C'".
					   "   AND RD.numrecdoc=DS.numrecdoc".
					   "   AND RD.cod_pro=DS.cod_pro".
					   "   AND DS.numsol=SO.numsol".$ls_filtroest.
					   " GROUP BY RD.cod_pro,RD.codproalt";
		    	   
		       }
		       else
		       {
			     if ((strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqlt")) || (strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqli")))
				 {
		            $ls_parametro="CONCAT(BEN.nombene,BEN.apebene) AS nompro";
		          }
		          elseif(strtoupper($_SESSION["ls_gestor"])==strtoupper("postgres")){
		            $ls_parametro="(MAX(BEN.nombene) ||MAX(BEN.apebene)) AS nompro";
		          }
			    
				  $ls_sql ="SELECT RD.ced_bene as cod_pro,MAX(RDD.codded) AS codded,MAX(RDD.numrecdoc)".
				           "       AS numrecdoc,".$ls_parametro.",MAX(BEN.dirbene) as dirpro,MAX(BEN.rifben) as rifpro,RD.codproalt".
			               "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,rpc_beneficiario BEN,".
					       "       cxp_dt_solicitudes DS,cxp_solicitudes SO".
		  	               " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
						   "   AND SD.codemp=RDD.codemp ". 
					       "   AND SD.codemp=RD.codemp".
						   "   AND SD.codemp=BEN.codemp".
						   "   AND SD.codemp=DS.codemp".
						   "   AND SD.codemp=SO.codemp".
					       "   AND SD.estretmil='1'".
						   "   AND RDD.estcmp='0'".
						   "   AND SD.codded=RDD.codded".
						   "   AND RDD.numrecdoc=RD.numrecdoc".
						   "   AND (RD.ced_bene BETWEEN '".$as_probendesde."' AND '".$as_probenhasta."') ".
					       "   AND RD.ced_bene=BEN.ced_bene".
						   "   AND RDD.ced_bene=RD.ced_bene".
						   "   AND RDD.codtipdoc=RD.codtipdoc".
					       "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
						   "   AND (RD.estprodoc='C') ".
					       "   AND RD.numrecdoc=DS.numrecdoc".
						   "   AND RD.ced_bene=DS.ced_bene".
						   "   AND DS.numsol=SO.numsol".$ls_filtroest.
					       " GROUP BY RD.ced_bene,RD.codproalt";
		       }
		 
		 }
		 elseif($as_tiporet=="R")
		 {
		  if($as_tipo=='P')
		   {
			$ls_sql =  "SELECT RD.cod_pro,MAX(RDD.codded) AS codded,MAX(RDD.numrecdoc) AS numrecdoc,".
			           "       MAX(PRO.nompro) AS nompro,MAX(PRO.dirpro) AS dirpro,MAX(PRO.rifpro) AS rifpro,RD.codproalt".
			           "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,rpc_proveedor PRO,".
					   "        cxp_dt_solicitudes DS,cxp_solicitudes SO".
					   " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
					   "   AND SD.codemp=RDD.codemp".
					   "   AND SD.codemp=RD.codemp". 
					   "   AND SD.codemp=PRO.codemp".
					   "   AND SD.codemp=DS.codemp".
					   "   AND SD.codemp=SO.codemp".
					   "   AND SD.islr=1".
					   "   AND RDD.estcmp='0'".
					   "   AND SD.codded=RDD.codded".
					   "   AND RDD.numrecdoc=RD.numrecdoc".
					   "   AND (RD.cod_pro BETWEEN '".$as_probendesde."' AND '".$as_probenhasta."')".
					   "   AND RD.cod_pro=PRO.cod_pro".
					   "   AND RDD.cod_pro=RD.cod_pro".
					   "   AND RDD.codtipdoc=RD.codtipdoc".
					   "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
					   "   AND RD.estprodoc='C'".
					   "   AND RD.numrecdoc=DS.numrecdoc".
					   "   AND RD.cod_pro=DS.cod_pro".
					   "   AND DS.numsol=SO.numsol".$ls_filtroest.
					   " GROUP BY RD.cod_pro,RD.codproalt";
		    	   
		       }
		       else
		       {
			     if ((strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqlt")) || (strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqli")))
				 {
		            $ls_parametro="CONCAT(BEN.nombene,BEN.apebene) AS nompro";
		          }
		          elseif(strtoupper($_SESSION["ls_gestor"])==strtoupper("postgres")){
		            $ls_parametro="(MAX(BEN.nombene) ||MAX(BEN.apebene)) AS nompro";
		          }
			    
				  $ls_sql ="SELECT RD.ced_bene as cod_pro,MAX(RDD.codded) AS codded,MAX(RDD.numrecdoc)".
				           "       AS numrecdoc,".$ls_parametro.",MAX(BEN.dirbene) as dirpro,MAX(BEN.rifben) as rifpro,RD.codproalt".
			               "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,rpc_beneficiario BEN,".
					       "       cxp_dt_solicitudes DS,cxp_solicitudes SO".
		  	               " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
						   "   AND SD.codemp=RDD.codemp ". 
					       "   AND SD.codemp=RD.codemp".
						   "   AND SD.codemp=BEN.codemp".
						   "   AND SD.codemp=DS.codemp".
						   "   AND SD.codemp=SO.codemp".
					       "   AND SD.islr=1".
						   "   AND RDD.estcmp='0'".
						   "   AND SD.codded=RDD.codded".
						   "   AND RDD.numrecdoc=RD.numrecdoc".
						   "   AND (RD.ced_bene BETWEEN '".$as_probendesde."' AND '".$as_probenhasta."') ".
					       "   AND RD.ced_bene=BEN.ced_bene".
						   "   AND RDD.ced_bene=RD.ced_bene".
						   "   AND RDD.codtipdoc=RD.codtipdoc".
					       "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
						   "   AND (RD.estprodoc='C') ".
					       "   AND RD.numrecdoc=DS.numrecdoc".
						   "   AND RD.ced_bene=DS.ced_bene".
						   "   AND DS.numsol=SO.numsol".$ls_filtroest.
					       " GROUP BY RD.ced_bene,RD.codproalt";
		       }
		 
		 }
        $rs_result=$this->io_sql->select($ls_sql);
		if($rs_result===false)
		{
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_get_provbene ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;	
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_result))
			{
				$aa_sujret=$this->io_sql->obtener_datos($rs_result);
			}
			else
			{
				$lb_valido=false;	
			}
			$this->io_sql->free_result($rs_result);
		}
		$arrResultado["aa_sujret"]=$aa_sujret;
		$arrResultado["lb_valido"]=$lb_valido;
		return $arrResultado;	
	}//FIN DE LA FUNCION uf_get_provbene 	

	function uf_get_documento($as_mes,$as_agno,$as_codprobene,$as_tipo,$as_tiporet,$as_codproalt,$as_RD)
	{
	   //////////////////////////////////////////////////////////////////////////////
	   //	Function: uf_get_documento
	   //	 Access: public
	   //	 Argument: $as_mes // Mes  | $as_agno // A?o
	   //             $as_codpro // Codigo del proveedor o beneficiaro
	   //             $as_tipo // Indica si se trabaja con proveedores o beneficiarios | $as_tiporet // Indica el tipo de retencion
	   //             $aa_sujret // arreglo con el resultado de la busqueda 
	   //  Description: Funci?n que genera una lista con proveedores o beneficiarios con posibilidad de tener movimientos
	   //               que ameriten la generacion de un comprobante de retencion  
	   //	Creado Por: Ing. Gerardo Cordero
	   //  Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
	   //////////////////////////////////////////////////////////////////////////////
	   $ld_fecdesde= $this->io_function->uf_convertirdatetobd("01/".$as_mes."/".$as_agno);
	   $ld_hasta   = $this->io_fec->uf_last_day($as_mes,$as_agno);
	   $ld_fechasta= $this->io_function->uf_convertirdatetobd($ld_hasta);
	   $lb_valido  = true;
	   if($this->ls_estcanret==1)
	   {
	   		$ls_limit="";
	   }
	   else
	   {
	   		$ls_limit="LIMIT 10";
	   }
	   if ((strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqlt")) || (strtoupper($_SESSION["ls_gestor"])==strtoupper("mysqli")))
		{
		   	if($as_tiporet=='I')
			{	   	   
			  $ls_id="CONCAT(RD.numrecdoc,RDC.porcar)";
			}
			else
			{
			  $ls_id="CONCAT(RD.numrecdoc,RDD.porded)";
			}
		}
		elseif(strtoupper($_SESSION["ls_gestor"])==strtoupper("postgres") || strtoupper($_SESSION["ls_gestor"])==strtoupper("oci8po") )
		{
		   	if($as_tiporet=='I')
			{	   	   
				$ls_id="RD.numrecdoc || RDC.porcar";
			}
			else
			{
				$ls_id="RD.numrecdoc || RDD.porded";
			}
		}
		 $ls_joinalt="";
		if($as_codproalt!="")
			$ls_joinalt="AND RD.codproalt='".$as_codproalt."'";
			
	   if($as_tiporet=='I')
        {	   	   
	      if($as_tipo=='P')
		   {
			$ls_sql = " SELECT ".$ls_id." as id,RD.numrecdoc,RD.fecemidoc,SUM(RDC.monobjret) AS basimpiva,MAX(RD.codproalt) as codproalt,".
			          " MAX(RD.montotdoc+RD.mondeddoc) AS totconiva,SUM(RDC.monobjret) AS monobjret,MAX(RDC.porcar) AS porcar,SUM(RDC.monret) AS totiva,".
					  " MAX(RDD.monret) AS ivaret,DS.numsol as numsop,RDD.codded,MAX(RDD.sc_cuenta) as cuenta, ".
					  " RD.numref, MAX(RD.codtipdoc) as codtipdoc ,MAX(RD.cod_pro) as cod_pro, MAX(RD.ced_bene) as  ced_bene,".
					  " MAX(RD.codemp), MAX(RD.tipdoctesnac) as  tipdoctesnac".
					  " FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,cxp_rd_cargos RDC".
                      " ,cxp_dt_solicitudes DS,cxp_solicitudes SO".
                      " WHERE (SD.codemp='".$this->la_empresa["codemp"]."') AND (SD.codemp=RDD.codemp) AND".
                      " (SD.codemp=RD.codemp) AND (SD.codemp=RDC.codemp) AND (SD.codemp=DS.codemp) AND". 
					  " (SD.codemp=SO.codemp) AND (SD.iva=1) AND (SD.codded=RDD.codded) AND (RDD.estcmp='0')". 
					  " AND (RDD.numrecdoc=RD.numrecdoc) AND (RDC.numrecdoc=RD.numrecdoc ) AND (RDD.codtipdoc=RD.codtipdoc)".
					  " AND (RDD.cod_pro='".$as_codprobene."') AND (RDD.cod_pro=RD.cod_pro) AND (RDC.cod_pro=RD.cod_pro)". 
                      " AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')AND (RD.estprodoc='C')".
					  $ls_joinalt."  AND".
                      " (RD.numrecdoc=DS.numrecdoc) AND (RD.cod_pro=DS.cod_pro) AND (DS.numsol=SO.numsol) AND (SO.estprosol<>'A') AND (SO.estprosol<>'N') ".
					  " AND DS.codemp=RD.codemp".
					  " AND DS.numrecdoc=RD.numrecdoc".
					  " AND DS.codtipdoc=RD.codtipdoc".
					  " AND DS.cod_pro=RD.cod_pro".
					  " AND DS.ced_bene=RD.ced_bene".
					  " GROUP by ".$ls_id.",RD.numrecdoc,RD.fecemidoc,DS.numsol,RDD.codded,RD.numref ORDER BY RD.numrecdoc ".$ls_limit."";
           }
		   else
		   {
			$ls_sql = " SELECT ".$ls_id." as id,RD.numrecdoc,RD.fecemidoc,SUM(RDC.monobjret) AS basimpiva,MAX(RD.codproalt) as codproalt,".
			          " MAX(RD.montotdoc+RD.mondeddoc) AS totconiva,SUM(RDC.monobjret) AS monobjret,MAX(RDC.porcar) AS porcar,SUM(RDC.monret) AS totiva,".
					  " MAX(RDD.monret) AS ivaret,DS.numsol as numsop,RDD.codded,MAX(RDD.sc_cuenta) as cuenta, ".
					  " RD.numref, MAX(RD.codtipdoc) as codtipdoc ,MAX(RD.cod_pro) as cod_pro, MAX(RD.ced_bene) as  ced_bene,".
					  " MAX(RD.codemp), MAX(RD.tipdoctesnac) as  tipdoctesnac ".
					  " FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,cxp_rd_cargos RDC".
                      " ,cxp_dt_solicitudes DS,cxp_solicitudes SO".
                      " WHERE (SD.codemp='".$this->la_empresa["codemp"]."') AND (SD.codemp=RDD.codemp) AND".
                      " (SD.codemp=RD.codemp) AND (SD.codemp=RDC.codemp) AND (SD.codemp=DS.codemp) AND". 
					  " (SD.codemp=SO.codemp) AND (SD.iva=1) AND (SD.codded=RDD.codded) AND (RDD.estcmp='0')". 
					  $ls_joinalt.
					  " AND (RDD.numrecdoc=RD.numrecdoc) AND (RDC.numrecdoc=RD.numrecdoc ) AND (RDD.codtipdoc=RD.codtipdoc)".
					  " AND (RDD.ced_bene='".$as_codprobene."') AND (RDD.ced_bene=RD.ced_bene) AND (RDC.ced_bene=RD.ced_bene)". 
                      " AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')AND (RD.estprodoc='C') AND".
                      " (RD.numrecdoc=DS.numrecdoc) AND (RD.cod_pro=DS.cod_pro) AND (DS.numsol=SO.numsol) AND (SO.estprosol<>'A')  AND (SO.estprosol<>'N')".
					  " AND DS.codemp=RD.codemp".
					  " AND DS.numrecdoc=RD.numrecdoc".
					  " AND DS.codtipdoc=RD.codtipdoc".
					  " AND DS.cod_pro=RD.cod_pro".
					  " AND DS.ced_bene=RD.ced_bene".
					  " GROUP by ".$ls_id.",RD.numrecdoc,RD.fecemidoc,DS.numsol,RDD.codded,RD.numref  ORDER BY RD.numrecdoc ".$ls_limit."";
                  }
		}
		elseif($as_tiporet=='M')
		{
		 if($as_tipo=='P')
		  {
			$ls_sql =" SELECT ".$ls_id." as id,RD.numrecdoc,RD.fecemidoc,SUM(RDD.monobjret) AS basimpiva,MAX(RD.codproalt) as codproalt,".
			         " MAX(RD.montotdoc+RD.mondeddoc) AS totconiva,SUM(RDD.monobjret) AS monobjret,MAX(RDD.porded) AS porcar,SUM(RDD.monret) AS totiva,".
					 " MAX(RDD.monret) AS ivaret,DS.numsol as numsop,RDD.codded,MAX(RDD.sc_cuenta) as cuenta, ".
					 " RD.numref, MAX(RD.codtipdoc) as codtipdoc ,MAX(RD.cod_pro) as cod_pro, MAX(RD.ced_bene) as  ced_bene,".
					 " MAX(RD.codemp), MAX(RD.tipdoctesnac) as  tipdoctesnac ".
					 " FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,cxp_dt_solicitudes DS,cxp_solicitudes SO".
					 " WHERE (SD.codemp='".$this->la_empresa["codemp"]."') AND (SD.codemp=RDD.codemp) AND (SD.codemp=RD.codemp)".
					 $ls_joinalt.
					 " AND (SD.codemp=SO.codemp) AND (SD.codemp=DS.codemp )  AND (SD.estretmun=1) AND (SD.codded=RDD.codded)".
					 " AND (RDD.estcmp='0') AND (RDD.numrecdoc=RD.numrecdoc) AND (RDD.codtipdoc=RD.codtipdoc) AND".
					 " (RDD.cod_pro='".$as_codprobene."') AND (RDD.cod_pro=RD.cod_pro) AND". 
					 " (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."') AND (RD.estprodoc='C')". 
					 " AND (RD.numrecdoc=DS.numrecdoc) AND (RD.cod_pro=DS.cod_pro) AND (DS.numsol=SO.numsol) AND (SO.estprosol<>'A')  AND (SO.estprosol<>'N')".
					  " AND DS.codemp=RD.codemp".
					  " AND DS.numrecdoc=RD.numrecdoc".
					  " AND DS.codtipdoc=RD.codtipdoc".
					  " AND DS.cod_pro=RD.cod_pro".
					  " AND DS.ced_bene=RD.ced_bene".
					 " GROUP by ".$ls_id.",RD.numrecdoc,RD.fecemidoc,RDD.porded,DS.numsol,RDD.codded,RD.numref  ORDER BY RD.numrecdoc ".$ls_limit."";
		  }
		  else
		  {
			$ls_sql =" SELECT ".$ls_id." as id,RD.numrecdoc,RD.fecemidoc,SUM(RDD.monobjret) AS basimpiva,MAX(RD.codproalt) as codproalt,".
			         " MAX(RD.montotdoc+RD.mondeddoc) AS totconiva,SUM(RDD.monobjret) AS monobjret,MAX(RDD.porded) AS porcar,SUM(RDD.monret) AS totiva,".
					 " MAX(RDD.monret) AS ivaret,DS.numsol as numsop,RDD.codded,MAX(RDD.sc_cuenta) as cuenta, ".
					 " RD.numref, MAX(RD.codtipdoc) as codtipdoc ,MAX(RD.cod_pro) as cod_pro, MAX(RD.ced_bene) as  ced_bene,".
					 " MAX(RD.codemp), MAX(RD.tipdoctesnac) as  tipdoctesnac".
					 " FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,cxp_dt_solicitudes DS,cxp_solicitudes SO".
					 " WHERE (SD.codemp='".$this->la_empresa["codemp"]."') AND (SD.codemp=RDD.codemp) AND (SD.codemp=RD.codemp)".
					 " AND (SD.codemp=SO.codemp) AND (SD.codemp=DS.codemp )  AND (SD.estretmun=1) AND (SD.codded=RDD.codded)".
					 $ls_joinalt.
					 " AND (RDD.estcmp='0') AND (RDD.numrecdoc=RD.numrecdoc) AND (RDD.codtipdoc=RD.codtipdoc) AND".
					 " (RDD.ced_bene='".$as_codprobene."') AND (RDD.ced_bene=RD.ced_bene) AND". 
					 " (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."') AND (RD.estprodoc='C')". 
					 " AND (RD.numrecdoc=DS.numrecdoc) AND (RD.ced_bene=DS.ced_bene) AND (DS.numsol=SO.numsol) AND (SO.estprosol<>'A')  AND (SO.estprosol<>'N')".
					  " AND DS.codemp=RD.codemp".
					  " AND DS.numrecdoc=RD.numrecdoc".
					  " AND DS.codtipdoc=RD.codtipdoc".
					  " AND DS.cod_pro=RD.cod_pro".
					  " AND DS.ced_bene=RD.ced_bene".
					 " GROUP by ".$ls_id.",RD.numrecdoc,RD.fecemidoc,RDD.porded,DS.numsol,RDD.codded,RD.numref  ORDER BY RD.numrecdoc ".$ls_limit."";
		   }
    	}
		elseif($as_tiporet=='A')
		{
		 if($as_tipo=='P')
		  {
			$ls_sql ="SELECT ".$ls_id." as id,RD.numrecdoc,RD.fecemidoc,SUM(RDD.monobjret) AS basimpiva,MAX(RD.codproalt) as codproalt,".
			         "       MAX(RD.montotdoc+RD.mondeddoc) AS totconiva,SUM(RDD.monobjret) AS monobjret,".
					 "       MAX(RDD.porded) AS porcar,0 AS totiva,MAX(RDD.monret) AS ivaret,DS.numsol as numsop, ".
					 "       RDD.codded,MAX(RDD.sc_cuenta) as cuenta,RD.numref, MAX(RD.tipdoctesnac) as  tipdoctesnac ".
					 "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,cxp_dt_solicitudes DS,cxp_solicitudes SO".
					 " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
					 "   AND SD.codemp=RDD.codemp".
					 "   AND SD.codemp=RD.codemp".
					 "   AND SD.codemp=SO.codemp".
					 "   AND SD.codemp=DS.codemp".
					 "   AND SD.retaposol=1".
					 "   AND SD.codded=RDD.codded".
					 "   AND RDD.estcmp='0'".
					 $ls_joinalt.
					 "   AND RDD.numrecdoc=RD.numrecdoc".
					 "   AND RDD.codtipdoc=RD.codtipdoc ".
					 "   AND RDD.cod_pro='".$as_codprobene."'".
					 "   AND (RDD.cod_pro=RD.cod_pro) ". 
					 "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
					 "   AND RD.estprodoc='C'". 
					 "   AND RD.numrecdoc=DS.numrecdoc".
					 "   AND RD.cod_pro=DS.cod_pro".
					 "   AND DS.numsol=SO.numsol".
					 "   AND SO.estprosol<>'A'  AND SO.estprosol<>'N'".
					 "   AND DS.codemp=RD.codemp".
					 "   AND DS.numrecdoc=RD.numrecdoc".
					 "   AND DS.codtipdoc=RD.codtipdoc".
					 "   AND DS.cod_pro=RD.cod_pro".
					 "   AND DS.ced_bene=RD.ced_bene".
					 " GROUP by ".$ls_id.",RD.numrecdoc,RD.fecemidoc,RDD.porded,DS.numsol,RDD.codded,RD.numref  ORDER BY RD.numrecdoc ".$ls_limit."";
		  }
		  else
		  {
			$ls_sql ="SELECT ".$ls_id." as id,RD.numrecdoc,RD.fecemidoc,SUM(RDD.monobjret) AS basimpiva,MAX(RD.codproalt) as codproalt,".
			         "       MAX(RD.montotdoc+RD.mondeddoc) AS totconiva,SUM(RDD.monobjret) AS monobjret,".
					 "       MAX(RDD.porded) AS porcar,SUM(RDD.monret) AS totiva,MAX(RDD.monret) AS ivaret,DS.numsol as numsop, ".
					 "       RDD.codded,MAX(RDD.sc_cuenta) as cuenta,RD.numref, MAX(RD.tipdoctesnac) as  tipdoctesnac ".
					 "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,cxp_dt_solicitudes DS,cxp_solicitudes SO".
					 " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
					 "   AND SD.codemp=RDD.codemp".
					 "   AND SD.codemp=RD.codemp".
					 "   AND SD.codemp=SO.codemp".
					 "   AND SD.codemp=DS.codemp".
					 "   AND SD.retaposol=1".
					 "   AND SD.codded=RDD.codded".
					 "   AND RDD.estcmp='0'".
					 $ls_joinalt.
					 "   AND RDD.numrecdoc=RD.numrecdoc".
					 "   AND RDD.codtipdoc=RD.codtipdoc ".
					 "   AND RDD.ced_bene='".$as_codprobene."'".
					 "   AND RDD.ced_bene=RD.ced_bene ". 
					 "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
					 "   AND RD.estprodoc='C'". 
					 "   AND RD.numrecdoc=DS.numrecdoc".
					 "   AND RD.ced_bene=DS.ced_bene".
					 "   AND DS.numsol=SO.numsol".
					 "   AND SO.estprosol<>'A'  AND SO.estprosol<>'N'".
					 "   AND DS.codemp=RD.codemp".
					 "   AND DS.numrecdoc=RD.numrecdoc".
					 "   AND DS.codtipdoc=RD.codtipdoc".
					 "   AND DS.cod_pro=RD.cod_pro".
					 "   AND DS.ced_bene=RD.ced_bene".
					 " GROUP by ".$ls_id.",RD.numrecdoc,RD.fecemidoc,RDD.porded,DS.numsol,RDD.codded,RD.numref  ORDER BY RD.numrecdoc ".$ls_limit." ";
		   }
		
		}
		elseif($as_tiporet=='1')
		{
		 if($as_tipo=='P')
		  {
			$ls_sql ="SELECT ".$ls_id." as id,RD.numrecdoc,RD.fecemidoc,SUM(RDD.monobjret) AS basimpiva,MAX(RD.codproalt) as codproalt,".
			         "       MAX(RD.montotdoc+RD.mondeddoc) AS totconiva,SUM(RDD.monobjret) AS monobjret,".
					 "       MAX(RDD.porded) AS porcar,0 AS totiva,MAX(RDD.monret) AS ivaret,DS.numsol as numsop, ".
					 "       RDD.codded,MAX(RDD.sc_cuenta) as cuenta,RD.numref, MAX(RD.tipdoctesnac) as  tipdoctesnac ".
					 "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,cxp_dt_solicitudes DS,cxp_solicitudes SO".
					 " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
					 "   AND SD.codemp=RDD.codemp".
					 "   AND SD.codemp=RD.codemp".
					 "   AND SD.codemp=SO.codemp".
					 "   AND SD.codemp=DS.codemp".
					 "   AND SD.estretmil='1'".
					 "   AND SD.codded=RDD.codded".
					 "   AND RDD.estcmp='0'".
					 $ls_joinalt.
					 "   AND RDD.numrecdoc=RD.numrecdoc".
					 "   AND RDD.codtipdoc=RD.codtipdoc ".
					 "   AND RDD.cod_pro='".$as_codprobene."'".
					 "   AND (RDD.cod_pro=RD.cod_pro) ". 
					 "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
					 "   AND RD.estprodoc='C'". 
					 "   AND RD.numrecdoc=DS.numrecdoc".
					 "   AND RD.cod_pro=DS.cod_pro".
					 "   AND DS.numsol=SO.numsol".
					 "   AND SO.estprosol<>'A'  AND SO.estprosol<>'N'".
					 "   AND DS.codemp=RD.codemp".
					 "   AND DS.numrecdoc=RD.numrecdoc".
					 "   AND DS.codtipdoc=RD.codtipdoc".
					 "   AND DS.cod_pro=RD.cod_pro".
					 "   AND DS.ced_bene=RD.ced_bene".
					 " GROUP by ".$ls_id.",RD.numrecdoc,RD.fecemidoc,RDD.porded,DS.numsol,RDD.codded,RD.numref   ORDER BY RD.numrecdoc ".$ls_limit."";
		  }
		  else
		  {
			$ls_sql ="SELECT ".$ls_id." as id,RD.numrecdoc,RD.fecemidoc,SUM(RDD.monobjret) AS basimpiva,MAX(RD.codproalt) as codproalt,".
			         "       MAX(RD.montotdoc+RD.mondeddoc) AS totconiva,SUM(RDD.monobjret) AS monobjret,".
					 "       MAX(RDD.porded) AS porcar,SUM(RDD.monret) AS totiva,MAX(RDD.monret) AS ivaret,DS.numsol as numsop, ".
					 "       RDD.codded,MAX(RDD.sc_cuenta) as cuenta,RD.numref, MAX(RD.tipdoctesnac) as  tipdoctesnac ".
					 "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,cxp_dt_solicitudes DS,cxp_solicitudes SO".
					 " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
					 "   AND SD.codemp=RDD.codemp".
					 "   AND SD.codemp=RD.codemp".
					 "   AND SD.codemp=SO.codemp".
					 "   AND SD.codemp=DS.codemp".
					 "   AND SD.estretmil='1'".
					 $ls_joinalt.
					 "   AND SD.codded=RDD.codded".
					 "   AND RDD.estcmp='0'".
					 "   AND RDD.numrecdoc=RD.numrecdoc".
					 "   AND RDD.codtipdoc=RD.codtipdoc ".
					 "   AND RDD.ced_bene='".$as_codprobene."'".
					 "   AND RDD.ced_bene=RD.ced_bene ". 
					 "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
					 "   AND RD.estprodoc='C'". 
					 "   AND RD.numrecdoc=DS.numrecdoc".
					 "   AND RD.ced_bene=DS.ced_bene".
					 "   AND DS.numsol=SO.numsol".
					 "   AND SO.estprosol<>'A'  AND SO.estprosol<>'N'".
					 "   AND DS.codemp=RD.codemp".
					 "   AND DS.numrecdoc=RD.numrecdoc".
					 "   AND DS.codtipdoc=RD.codtipdoc".
					 "   AND DS.cod_pro=RD.cod_pro".
					 "   AND DS.ced_bene=RD.ced_bene".
					 " GROUP by ".$ls_id.",RD.numrecdoc,RD.fecemidoc,RDD.porded,DS.numsol,RDD.codded,RD.numref  ORDER BY RD.numrecdoc ".$ls_limit." ";
		   }
		}
		elseif($as_tiporet=='R')
		{
		 if($as_tipo=='P')
		  {
			$ls_sql ="SELECT ".$ls_id." as id,RD.numrecdoc,RD.fecemidoc,SUM(RDD.monobjret) AS basimpiva,MAX(RD.codproalt) as codproalt,".
			         "       MAX(RD.montotdoc+RD.mondeddoc) AS totconiva,SUM(RDD.monobjret) AS monobjret,".
					 "       MAX(RDD.porded) AS porcar,0 AS totiva,MAX(RDD.monret) AS ivaret,DS.numsol as numsop, ".
					 "       RDD.codded,MAX(RDD.sc_cuenta) as cuenta,RD.numref, MAX(RD.tipdoctesnac) as  tipdoctesnac ".
					 "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,cxp_dt_solicitudes DS,cxp_solicitudes SO".
					 " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
					 "   AND SD.codemp=RDD.codemp".
					 "   AND SD.codemp=RD.codemp".
					 "   AND SD.codemp=SO.codemp".
					 "   AND SD.codemp=DS.codemp".
					 "   AND SD.islr=1".
					 "   AND SD.codded=RDD.codded".
					 "   AND RDD.estcmp='0'".
					 $ls_joinalt.
					 "   AND RDD.numrecdoc=RD.numrecdoc".
					 "   AND RDD.codtipdoc=RD.codtipdoc ".
					 "   AND RDD.cod_pro='".$as_codprobene."'".
					 "   AND (RDD.cod_pro=RD.cod_pro) ". 
					 "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
					 "   AND RD.estprodoc='C'". 
					 "   AND RD.numrecdoc=DS.numrecdoc".
					 "   AND RD.cod_pro=DS.cod_pro".
					 "   AND DS.numsol=SO.numsol".
					 "   AND SO.estprosol<>'A'  AND SO.estprosol<>'N'".
					 "   AND DS.codemp=RD.codemp".
					 "   AND DS.numrecdoc=RD.numrecdoc".
					 "   AND DS.codtipdoc=RD.codtipdoc".
					 "   AND DS.cod_pro=RD.cod_pro".
					 "   AND DS.ced_bene=RD.ced_bene".
					 " GROUP by ".$ls_id.",RD.numrecdoc,RD.fecemidoc,RDD.porded,DS.numsol,RDD.codded,RD.numref  ORDER BY RD.numrecdoc ".$ls_limit." ";
		  }
		  else
		  {
			$ls_sql ="SELECT ".$ls_id." as id,RD.numrecdoc,RD.fecemidoc,SUM(RDD.monobjret) AS basimpiva,MAX(RD.codproalt) as codproalt,".
			         "       MAX(RD.montotdoc+RD.mondeddoc) AS totconiva,SUM(RDD.monobjret) AS monobjret,".
					 "       MAX(RDD.porded) AS porcar,SUM(RDD.monret) AS totiva,MAX(RDD.monret) AS ivaret,DS.numsol as numsop, ".
					 "       RDD.codded,MAX(RDD.sc_cuenta) as cuenta,RD.numref, MAX(RD.tipdoctesnac) as  tipdoctesnac ".
					 "  FROM sigesp_deducciones SD,cxp_rd_deducciones RDD,cxp_rd RD,cxp_dt_solicitudes DS,cxp_solicitudes SO".
					 " WHERE SD.codemp='".$this->la_empresa["codemp"]."'".
					 "   AND SD.codemp=RDD.codemp".
					 "   AND SD.codemp=RD.codemp".
					 "   AND SD.codemp=SO.codemp".
					 "   AND SD.codemp=DS.codemp".
					 "   AND SD.islr=1".
					 $ls_joinalt.
					 "   AND SD.codded=RDD.codded".
					 "   AND RDD.estcmp='0'".
					 "   AND RDD.numrecdoc=RD.numrecdoc".
					 "   AND RDD.codtipdoc=RD.codtipdoc ".
					 "   AND RDD.ced_bene='".$as_codprobene."'".
					 "   AND RDD.ced_bene=RD.ced_bene ". 
					 "   AND (SO.fecemisol BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."')".
					 "   AND RD.estprodoc='C'". 
					 "   AND RD.numrecdoc=DS.numrecdoc".
					 "   AND RD.ced_bene=DS.ced_bene".
					 "   AND DS.numsol=SO.numsol".
					 "   AND SO.estprosol<>'A'  AND SO.estprosol<>'N'".
					 "   AND DS.codemp=RD.codemp".
					 "   AND DS.numrecdoc=RD.numrecdoc".
					 "   AND DS.codtipdoc=RD.codtipdoc".
					 "   AND DS.cod_pro=RD.cod_pro".
					 "   AND DS.ced_bene=RD.ced_bene".
					 " GROUP by ".$ls_id.",RD.numrecdoc,RD.fecemidoc,RDD.porded,DS.numsol,RDD.codded,RD.numref  ORDER BY RD.numrecdoc ".$ls_limit." ";
		   }
		}//print $ls_sql."<br><br>";
		$rs_result=$this->io_sql->select($ls_sql);
		if($rs_result===false)
		{
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_get_documento ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;	
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_result))
			{
				$as_RD=$this->io_sql->obtener_datos($rs_result);
			}
			else
			{
			  $lb_valido=false;
			}
			$this->io_sql->free_result($rs_result);
		}
		$arrResultado["valido"]=$lb_valido;	
		$arrResultado["as_RD"]=$as_RD;	
		return $arrResultado;
	}//FIN DE LA FUNCION uf_get_documento 
/******************************************************************************************************************/
/***********************************    INSERCION DE DATA        **************************************************/	
/******************************************************************************************************************/

    function uf_crear_comprobante($as_codret,$as_numcom,$as_fecrep,$as_perfiscal,$as_codsujret,$as_nomsujret,$as_dirsujret,$as_rif,$as_nit,$as_estcmpret,$as_codusu,$as_numlic,
								  $as_origen,$aa_seguridad,$li_i=1)
	{
	    //////////////////////////////////////////////////////////////////////////////
		//	      Function: uf_crear_comprobante
		//	        Access: public
		//	      Argument: $as_codret // Codigo de la retencion,$as_numcom // Numero del comprobante
		//                  $as_fecrep // Fecha del comprobante,$as_perfiscal // perido fiscal
		//                  $as_codsujret // Codigo del proveedor o beneficiario,$as_nomsujret // Nombre del proveedor o beneficiario
		//                  $as_dirsujret // Direccion del proveedor o beneficiario ,$as_rif // RIF del proveedor o beneficiario
		//                  $as_nit // NIT del proveedor ,$as_estcmpret // Estatus del comprobante,
		//                  $as_codusu // codigo del usuario ,$as_numlic // Numero de licencia del proveedor,$as_origen 
		//     Description: Funci?n que guarda la cabezera de un comprobante de retencion  
		//	    Creado Por: Ing. Gerardo Cordero
		//  Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_numcom=$this->uf_get_nrocomprobante($as_codret,$as_perfiscal,$as_numcom);
		$ls_sql=" INSERT INTO scb_cmp_ret (codemp,codret,numcom,fecrep,perfiscal,codsujret,nomsujret,dirsujret,rif,".
				"                          nit,estcmpret,codusu,numlic,origen)".
				  " VALUES ('".$this->la_empresa["codemp"]."','".$as_codret."','".$as_numcom."','".$as_fecrep."',".
				  "         '".$as_perfiscal."','".$as_codsujret."','". $as_nomsujret."','".$as_dirsujret."','".$as_rif."',".
				  "         '".$as_nit."','".$as_estcmpret."','".$as_codusu."','".$as_numlic."','".$as_origen."')";
		$li_result=$this->io_sql->execute($ls_sql);
		if($li_result===false)
		{	
			if($li_i<4)
			{
				$li_i++;
				$as_numcom=$this->uf_get_nrocomprobante($as_codret,$as_perfiscal,$as_numcom);
				$arrResultado2=$this->uf_crear_comprobante($as_codret,$as_numcom,$as_fecrep,$as_perfiscal,$as_codsujret,$as_nomsujret,$as_dirsujret,$as_rif,
													  $as_nit,$as_estcmpret,$as_codusu,$as_numlic,$as_origen,$aa_seguridad,$li_i);
				$lb_valido=$arrResultado2["lb_valido"];
				$as_numcom=$arrResultado2["as_numcom"];
				
			}
			else
			{
				$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_crear_comprobante ERROR->".$this->io_function->uf_convertirmsg($this->io_sqlaux->message));
				$lb_valido=false;
			}
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el Comprobante ".$as_numcom.
							 " Asociado a la empresa ".$this->la_empresa["codemp"];
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["as_numcom"]=$as_numcom;
		return $arrResultado;
    }//FIN DE LA FUNCION uf_crear_comprobante

    function uf_crear_comprobante_consolida($as_codret,$as_numcom,$as_fecrep,$as_perfiscal,$as_codsujret,$as_nomsujret,$as_dirsujret,$as_rif,$as_nit,$as_estcmpret,$as_codusu,$as_numlic,$as_origen,$aa_seguridad)
	{
	    //////////////////////////////////////////////////////////////////////////////
		//	      Function: uf_crear_comprobante_consolida
		//	        Access: public
		//	      Argument: $as_codret // Codigo de la retencion,$as_numcom // Numero del comprobante
		//                  $as_fecrep // Fecha del comprobante,$as_perfiscal // perido fiscal
		//                  $as_codsujret // Codigo del proveedor o beneficiario,$as_nomsujret // Nombre del proveedor o beneficiario
		//                  $as_dirsujret // Direccion del proveedor o beneficiario ,$as_rif // RIF del proveedor o beneficiario
		//                  $as_nit // NIT del proveedor ,$as_estcmpret // Estatus del comprobante,
		//                  $as_codusu // codigo del usuario ,$as_numlic // Numero de licencia del proveedor,$as_origen 
		//     Description: Funci?n que guarda la cabezera de un comprobante de retencion  
		//	    Creado Por: Ing. Gerardo Cordero
		//  Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_basdatori=$_SESSION["ls_database"];
		$ls_sql=" INSERT INTO scb_cmp_ret (codemp,codret,numcom,fecrep,perfiscal,codsujret,nomsujret,dirsujret,rif,".
				"                          nit,estcmpret,codusu,numlic,origen,basdatori)".
				  " VALUES ('".$this->la_empresa["codemp"]."','".$as_codret."','".$as_numcom."','".$as_fecrep."',".
				  "         '".$as_perfiscal."','".$as_codsujret."','". $as_nomsujret."','".$as_dirsujret."','".$as_rif."',".
				  "         '".$as_nit."','".$as_estcmpret."','".$as_codusu."','".$as_numlic."','".$as_origen."','".$ls_basdatori."')";
		$li_result=$this->io_sqlaux->execute($ls_sql);
		if($li_result===false)
		{	
				if($this->io_sql->errno=='23505' || $this->io_sql->errno=='1062' || $this->io_sql->errno=='-239' || $this->io_sql->errno=='-5'|| $this->io_sql->errno=='-1')
				{
					$as_numcom=$this->uf_get_nrocomprobante($as_codret,$as_perfiscal,$as_numcom);
					$arrResultado=$this->uf_crear_comprobante_consolida($as_codret,$as_numcom,$as_fecrep,$as_perfiscal,$as_codsujret,$as_nomsujret,$as_dirsujret,$as_rif,
												  		  $as_nit,$as_estcmpret,$as_codusu,$as_numlic,$as_origen,$aa_seguridad);
					
														  
				}
				else
				{
					$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_crear_comprobante_consolida ERROR->".$this->io_function->uf_convertirmsg($this->io_sqlaux->message));
					$lb_valido=false;
				}
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el Comprobante ".$as_numcom.
							 " Asociado a la empresa ".$this->la_empresa["codemp"];
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["as_numcom"]=$as_numcom;
		return $arrResultado;
    }//FIN DE LA FUNCION uf_crear_comprobante_consolida

    function uf_guardar_detallecmp($as_codret,$as_numcom,$as_numope,$as_fecfac,$as_numfac,$as_numcon,$as_numnd,$as_numnc,
								   $as_tiptrans,$as_tot_cmp_sin_iva,$as_tot_cmp_con_iva,$as_basimp,$as_porimp,$as_totimp,
								   $as_ivaret,$as_desope,$as_numsop,$as_codban,$as_ctaban,$as_numdoc,$as_codope,$as_tipdoctesnac=0)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	      Function: uf_crear_comprobante
		//	        Access: public
		//	      Argument: $as_codret // Codigo de la retencion,$as_numcom // Numero del comprobante
		//                  $as_fecrep // Fecha del comprobante,$as_perfiscal // perido fiscal
		//                  $as_codsujret // Codigo del proveedor o beneficiario,$as_nomsujret // Nombre del proveedor o beneficiario
		//                  $as_dirsujret // Direccion del proveedor o beneficiario ,$as_rif // RIF del proveedor o beneficiario
		//                  $as_nit // NIT del proveedor ,$as_estcmpret // Estatus del comprobante,
		//                  $as_codusu // codigo del usuario ,$as_numlic // Numero de licencia del proveedor,$as_origen 
		//     Description: Funci?n que guarda la cabezera de un comprobante de retencion  
		//	    Creado Por: Ing. Gerardo Cordero
		//  Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = " INSERT INTO scb_dt_cmp_ret (codemp,codret,numcom,numope,fecfac,numfac,numcon,numnd,numnc,tiptrans,".
		          "                             totcmp_sin_iva,totcmp_con_iva,basimp,porimp,totimp,iva_ret,desope,". 
				  "                              numsop,codban,ctaban,numdoc,codope,tipdoctesnac) ".
				  " VALUES  ('".$this->la_empresa["codemp"]."','".$as_codret."','".$as_numcom."','".$as_numope."',".
				  "          '".$as_fecfac."','".$as_numfac."','".$as_numcon."','".$as_numnd."','".$as_numnc."',".
				  "          '".$as_tiptrans."','".$as_tot_cmp_sin_iva."','".$as_tot_cmp_con_iva."','".$as_basimp."',".
				  "          '".$as_porimp."','".$as_totimp."','".$as_ivaret."','".$as_desope."','".$as_numsop."',".
				  "          '".$as_codban."','".$as_ctaban."','".$as_numdoc."','".$as_codope."','".$as_tipdoctesnac."')";
		$li_result=$this->io_sql->execute($ls_sql);
		if($li_result ===false)
		{	
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_guardar_detallecmp ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}//FIN DE LA FUNCION uf_guardar_detallecmp

    function uf_guardar_detallecmp_consolida($as_codret,$as_numcom,$as_numope,$as_fecfac,$as_numfac,$as_numcon,$as_numnd,$as_numnc,$as_tiptrans,$as_tot_cmp_sin_iva,
											 $as_tot_cmp_con_iva,$as_basimp,$as_porimp,$as_totimp,$as_ivaret,$as_desope,$as_numsop,$as_codban,$as_ctaban,
											 $as_numdoc,$as_codope)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	      Function: uf_crear_comprobante
		//	        Access: public
		//	      Argument: $as_codret // Codigo de la retencion,$as_numcom // Numero del comprobante
		//                  $as_fecrep // Fecha del comprobante,$as_perfiscal // perido fiscal
		//                  $as_codsujret // Codigo del proveedor o beneficiario,$as_nomsujret // Nombre del proveedor o beneficiario
		//                  $as_dirsujret // Direccion del proveedor o beneficiario ,$as_rif // RIF del proveedor o beneficiario
		//                  $as_nit // NIT del proveedor ,$as_estcmpret // Estatus del comprobante,
		//                  $as_codusu // codigo del usuario ,$as_numlic // Numero de licencia del proveedor,$as_origen 
		//     Description: Funci?n que guarda la cabezera de un comprobante de retencion  
		//	    Creado Por: Ing. Gerardo Cordero
		//  Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = " INSERT INTO scb_dt_cmp_ret (codemp,codret,numcom,numope,fecfac,numfac,numcon,numnd,numnc,tiptrans,".
		          "                             totcmp_sin_iva,totcmp_con_iva,basimp,porimp,totimp,iva_ret,desope,". 
				  "                              numsop,codban,ctaban,numdoc,codope) ".
				  " VALUES  ('".$this->la_empresa["codemp"]."','".$as_codret."','".$as_numcom."','".$as_numope."',".
				  "          '".$as_fecfac."','".$as_numfac."','".$as_numcon."','".$as_numnd."','".$as_numnc."',".
				  "          '".$as_tiptrans."','".$as_tot_cmp_sin_iva."','".$as_tot_cmp_con_iva."','".$as_basimp."',".
				  "          '".$as_porimp."','".$as_totimp."','".$as_ivaret."','".$as_desope."','".$as_numsop."',".
				  "          '".$as_codban."','".$as_ctaban."','".$as_numdoc."','".$as_codope."')";
		$li_result=$this->io_sqlaux->execute($ls_sql);
		if($li_result===false)
		{	
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_guardar_detallecmp ERROR->".$this->io_function->uf_convertirmsg($this->io_sqlaux->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}//FIN DE LA FUNCION uf_crear_comprobante
	
/******************************************************************************************************************/
/****************************    PROCESO CREACION COMPROBANTE         *********************************************/	
/******************************************************************************************************************/

	function uf_procesar_cmp_retencion($as_mes,$as_agno,$as_probendesde,$as_probenhasta,$as_tipo,$as_tiporet,$aa_numcmp,$aa_seguridad)
	{
	    //////////////////////////////////////////////////////////////////////////////
		//	Function: uf_procesar_cmp_retencion
		//	 Access: public
		//	 Argument: $as_mes // Mes  | $as_agno // A?o
		//             $as_probendesde // Codigo de Proveedor o Benficiario | $as_probenhasta // Codigo de Proveedor o Benficiario
		//             $as_tipo // Indica si se trabaja con proveedores o beneficiarios 
		//             $as_tiporet // Indica el tipo de retencion
		//             $aa_numcmp // Arreglo con los numeros de los comprobantes generados 
		//  Description: Funci?n que se encarga de generar los comprobante de retencion  
		//	Creado Por: Ing. Gerardo Cordero
		//  Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 14/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$aa_numcmp [0] ="";
		$li_numcmp= 0;
		$ls_codemp= $this->la_empresa["codemp"];
		$ls_fecact= date('Y-m-d');
		$ls_perfis= $as_agno.$as_mes;
		$la_sujret="";
		$arrResultado=$this->uf_get_provbene($as_mes,$as_agno,$as_probendesde,$as_probenhasta,$as_tipo,$as_tiporet,$la_sujret);
		$lb_provbene=$arrResultado["lb_valido"];
		$la_sujret=$arrResultado["aa_sujret"];
		unset($arrResultado);
		if($as_tiporet=="I")
		{
			$ls_codded="0000000001";
		}
		elseif($as_tiporet=="M")
		{
			$ls_codded="0000000003";
		}
		elseif($as_tiporet=="A")
		{
			$ls_codded="0000000004";
		}
		elseif($as_tiporet=="1")
		{
			$ls_codded="0000000005";
		}
		elseif($as_tiporet=="R")
		{
			$ls_codded="0000000006";
		}
		if($lb_provbene)
		{
			$this->io_dataprov->data=$la_sujret;
			$li_totalfilaspro=$this->io_dataprov->getRowCount("cod_pro");
		}
		else
			{$li_totalfilaspro=0;}
		$this->io_sql->begin_transaction();
		$lb_valido=false;
		for($li_i=1;$li_i<=$li_totalfilaspro;$li_i++)
		{
			$ls_codpro=$this->io_dataprov->getValue("cod_pro",$li_i);
			$ls_nompro=$this->io_dataprov->getValue("nompro",$li_i);
			$ls_dirpro=$this->io_dataprov->getValue("dirpro",$li_i);
			$ls_rifpro=$this->io_dataprov->getValue("rifpro",$li_i);
			$ls_codproalt=$this->io_dataprov->getValue("codproalt",$li_i);
			$ls_nitpro=" ";//eliminado del select
			$ls_numlic=" ";//eliminado del select
			$la_RD="";
			$arrResultado=$this->uf_get_documento($as_mes,$as_agno,$ls_codpro,$as_tipo,$as_tiporet,$ls_codproalt,$la_RD);
			$la_RD=$arrResultado["as_RD"];
			unset($arrResultado);
			$li_rowrd=count((array)$la_RD); 
			if($li_rowrd>0)
			{
				$lb_valido=$this->uf_validar_rdmanual($ls_codpro,$la_RD,$as_tipo);
				if($lb_valido)
				{
					$this->io_datadocu->data=$la_RD;
					$li_totalfilas=$this->io_datadocu->getRowCount("id");
					$ls_nrocomp="";
					$ls_nrocomp=$this->uf_get_nrocomprobante($ls_codded,$ls_perfis,$ls_nrocomp);
					if($this->ls_basdatcmp!="")
					{
						$arrResultado=$this->uf_crear_comprobante_consolida($ls_codded,$ls_nrocomp,$ls_fecact,$ls_perfis,$ls_codpro,$ls_nompro,$ls_dirpro,$ls_rifpro,
																		$ls_nitpro,"1",$this->ls_codusu,$ls_numlic,"A",$aa_seguridad);
						$lb_valido=$arrResultado["lb_valido"];
					}
					if($lb_valido)
					{
						$arrResultado=$this->uf_crear_comprobante($ls_codded,$ls_nrocomp,$ls_fecact,$ls_perfis,$ls_codpro,$ls_nompro,$ls_dirpro,$ls_rifpro,
															   $ls_nitpro,"1",$this->ls_codusu,$ls_numlic,"A",$aa_seguridad);
						$lb_valido=$arrResultado["lb_valido"];
						$ls_nrocompaux=$arrResultado["as_numcom"];
					}
					if($lb_valido)
					{
						$li_numcmp++;
						$aa_numcmp [$li_numcmp]=$ls_nrocomp;
						$li_numope=0;
						//$ls_codproalt="";
						for($li_j=1;$li_j<=$li_totalfilas;$li_j++)
						{
							$li_numope++;
							$ls_coddoc=$this->io_datadocu->getValue("numrecdoc",$li_j);
							$ls_fecha=$this->io_datadocu->getValue("fecemidoc",$li_j);
							$ls_basimpiva=$this->io_datadocu->getValue("basimpiva",$li_j);
							$ls_totconiva=$this->io_datadocu->getValue("totconiva",$li_j);
							$ls_monobjret=$this->io_datadocu->getValue("monobjret",$li_j);
							$ls_porcar=$this->io_datadocu->getValue("porcar",$li_j);
							$ls_totiva=$this->io_datadocu->getValue("totiva",$li_j);
							$ls_ivaret=$this->io_datadocu->getValue("ivaret",$li_j);
							$ls_numsop=$this->io_datadocu->getValue("numsop",$li_j);
							$ls_corete=$this->io_datadocu->getValue("codded",$li_j);
							$ls_cuenta=$this->io_datadocu->getValue("cuenta",$li_j);
							$ls_numref=$this->io_datadocu->getValue("numref",$li_j);
							$ls_codtipdoc=$this->io_datadocu->getValue("codtipdoc",$li_j);
							$ls_cod_pro=$this->io_datadocu->getValue("cod_pro",$li_j);
							$ls_ced_bene=$this->io_datadocu->getValue("ced_bene",$li_j);
							$ls_ced_bene=$this->io_datadocu->getValue("ced_bene",$li_j);
							$ls_tipdoctesnac=$this->io_datadocu->getValue("tipdoctesnac",$li_j);
						//	$ls_codproalt=$this->io_datadocu->getValue("codproalt",$li_j);
							$ls_numope=$this->uf_get_nrooperacion($li_numope);
							$ls_totconiva=$ls_totconiva+$this->uf_buscar_monto_rdmanual($ls_codpro,$ls_coddoc,$as_tipo);
							if($as_tiporet=="I")
							{
								$ls_totsiniva=$ls_totconiva-($ls_basimpiva+$ls_totiva);
							}
							else
							{
								$ls_totsiniva=$ls_totconiva-$ls_basimpiva;
							}
							if($as_tiporet=="A")
							{
								$ls_totsiniva=0;
								$ls_totiva=$ls_totconiva-$ls_basimpiva;
							}
							$lb_valido=$this->uf_validar_dt_rdmanual($ls_codpro,$ls_coddoc,$ls_cuenta,$as_tipo);
							if($lb_valido)
							{
								if($this->ls_basdatcmp!="")
								{
									$lb_valido=$this->uf_guardar_detallecmp_consolida($ls_codded,$ls_nrocomp,$ls_numope,$ls_fecha,$ls_coddoc,$ls_numref,' ',' ',"01-reg",
																					  $ls_totsiniva,$ls_totconiva,$ls_monobjret,$ls_porcar,$ls_totiva,$ls_ivaret," ",
																					  $ls_numsop," "," ",$ls_coddoc,"01");
								}
								if($lb_valido)
								{
									$lb_valido=$this->uf_guardar_detallecmp($ls_codded,$ls_nrocomp,$ls_numope,$ls_fecha,$ls_coddoc,$ls_numref,' ',' ',
																			"01-reg",$ls_totsiniva,$ls_totconiva,$ls_monobjret,$ls_porcar,$ls_totiva,
																			$ls_ivaret," ",$ls_numsop," "," ",$ls_coddoc,"01",$ls_tipdoctesnac);
									if($lb_valido){
										$lb_valido=$this->uf_actualizar_estcmp($ls_coddoc,$ls_codpro,$ls_corete,$as_tipo);
									}
								}
							}
							if(($lb_valido)&&($ls_codded=="0000000001"))
							{	
								$arrResultado="";
								$arrResultado=$this->uf_procesar_ndnc($ls_numsop,$ls_coddoc,$ls_codtipdoc,$ls_cod_pro,$ls_ced_bene,
																   $li_numope,$ls_nrocomp,$ls_fecha,$ls_numref,$ls_codded);
								$lb_valido=$arrResultado["lb_valido"];
								$li_numope=$arrResultado["li_i"];
								unset($arrResultado);
							}
						}
						if(trim($ls_codproalt)!="")
						{
							$lb_valido=$this->uf_update_datos_proveedor($ls_codded,$ls_nrocomp,$ls_numsop,$ls_codproalt);
						}
					}
					else
						$li_totalfilas=0;
				}
				else
				{
					$this->io_msg->message("No se logro verificar la deducci?n en la Recepci?n de Documentos");
				} 
			}
			else
			{
				$this->io_msg->message("No existen documentos validos para realizar el proceso");
			}
		}//  $lb_valido=false;
		if(($lb_valido)&&($li_numcmp>0))
		{
			$this->io_sql->commit();
		}
		else
		{
			$this->io_sql->rollback();
			$li_numcmp=0;
		}
		
		$arrResultado["aa_numcmp"]=$aa_numcmp;
		$arrResultado["li_numcmp"]=$li_numcmp;
		return $arrResultado;
	}
	
	function uf_procesar_ndnc($as_numsop,$as_coddoc,$as_codtipdoc,$as_cod_pro,$as_ced_bene,$ai_i,$as_nrocomp,$as_fecha,$as_numref,$as_codded)
	{
	    //////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_ndnc
		//		   Access: public
		//		 Argument: $ls_numsol // Numero de solicitud de pago
		//                 $ls_numrecdoc // N?mero de Recepcion de Documento
		//                 $ls_codtipdoc // Codigo de Tipo de documento 
		//                 $ls_cod_pro // Codigo de proveedor
		//                 $ls_ced_bene // Cedula de Beneficiario
		//	  Description: Funci?n que verifica si existen notas de debito o credito asociadas al pago
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 16/09/2008								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_connect->Concat('cxp_dc_spg.codestpro','cxp_dc_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->la_empresa["codemp"]}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			                "  						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
							"          				   AND codsis='CXP' ".
				            "                          AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1)" .
							" AND cxp_dc_spg.codemp = cxp_rd.codemp ".
							" AND cxp_dc_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_dc_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_dc_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_dc_spg.cod_pro = cxp_rd.cod_pro ".
							" AND cxp_rd.codemp = cxp_sol_dc.codemp ".
							" AND cxp_rd.numrecdoc = cxp_sol_dc.numrecdoc ".
							" AND cxp_rd.codtipdoc = cxp_sol_dc.codtipdoc ".
							" AND cxp_rd.ced_bene = cxp_sol_dc.ced_bene ".
							" AND cxp_rd.cod_pro = cxp_sol_dc.cod_pro ";
			$ls_filtrofrom = " , cxp_rd, cxp_dc_spg";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_sol_dc.codope,cxp_sol_dc.numrecdoc,cxp_sol_dc.numdc,cxp_sol_dc.fecope,cxp_sol_dc.monto,".
				"       cxp_sol_dc.moncar,cxp_dc_cargos.porcar ".
				"  FROM cxp_sol_dc,cxp_dc_cargos".$ls_filtrofrom.
				" WHERE cxp_sol_dc.codemp='".$this->ls_codemp."'".
				"   AND cxp_sol_dc.numsol='".$as_numsop."'".
				"   AND cxp_sol_dc.numrecdoc='".$as_coddoc."'".
				"   AND cxp_sol_dc.codtipdoc='".$as_codtipdoc."'".
				"   AND cxp_sol_dc.cod_pro='".$as_cod_pro."'".
				"   AND cxp_sol_dc.ced_bene='".$as_ced_bene."'".$ls_filtroest.
				"   AND cxp_sol_dc.estnotadc='C'".
				"   AND cxp_sol_dc.codemp=cxp_dc_cargos.codemp".
				"   AND cxp_sol_dc.numsol=cxp_dc_cargos.numsol".
				"   AND cxp_sol_dc.numrecdoc=cxp_dc_cargos.numrecdoc".
				"   AND cxp_sol_dc.ced_bene=cxp_dc_cargos.ced_bene".
				"   AND cxp_sol_dc.cod_pro=cxp_dc_cargos.cod_pro".
				"   AND cxp_sol_dc.codope=cxp_dc_cargos.codope".
				"   AND cxp_sol_dc.numdc=cxp_dc_cargos.numdc";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_procesar_ndnc ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_i=$ai_i+1;
				$ls_codope=$row["codope"];
				$ls_numdc=$row["numdc"];
				$ld_fecope=$row["fecope"];
				$li_monto=$row["monto"];
				$li_moncar=$row["moncar"];
				$ls_porcar=$row["porcar"];
				$li_basimp=$li_monto-$li_moncar;
				$ls_numope=$this->uf_get_nrooperacion($ai_i);
				if($ls_codope=="NC")
				{
					$ls_numnd="";
					$ls_numnc=$ls_numdc;
					$li_monto=$li_monto*(-1);
					$li_basimp=$li_basimp*(-1);
					$li_moncar=$li_moncar*(-1);
				}
				else
				{
					$ls_numnd=$ls_numdc;
					$ls_numnc="";
				}
				if($this->ls_basdatcmp!="")
				{
					$lb_valido=$this->uf_guardar_detallecmp_consolida($as_codded,$as_nrocomp,$ls_numope,$ld_fecope,$as_coddoc,$as_numref,$ls_numnd,$ls_numnc,"01-reg",
																	  0,$li_monto,$li_basimp,$ls_porcar,$li_moncar,0,"",
																	  $as_numsop,"","",$as_coddoc,"01");
				}
				if($lb_valido)
				{
					$lb_valido=$this->uf_guardar_detallecmp($as_codded,$as_nrocomp,$ls_numope,$ld_fecope,$as_coddoc,$as_numref,$ls_numnd,$ls_numnc,
															"01-reg",0,$li_monto,$li_basimp,$ls_porcar,$li_moncar,
															0,"",$as_numsop,"","",$as_coddoc,"01");
				}
			//	$ls_concomiva=$row["concomiva"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["li_i"]=$ai_i;
		return $arrResultado;
	}

	function uf_update_datos_proveedor($as_codded,$as_nrocomp,$as_numsop,$as_codproalt)
	{
	    //////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_datos_proveedor
		//		   Access: public
		//		 Argument: $ls_numsol // Numero de solicitud de pago
		//                 $ls_numrecdoc // N?mero de Recepcion de Documento
		//                 $ls_codtipdoc // Codigo de Tipo de documento 
		//                 $ls_cod_pro // Codigo de proveedor
		//                 $ls_ced_bene // Cedula de Beneficiario
		//	  Description: Funci?n que verifica si existen notas de debito o credito asociadas al pago
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 16/09/2008								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT cxp_rd.codproalt, rpc_proveedor.cod_pro, rpc_proveedor.nompro,".
				"       rpc_proveedor.dirpro, rpc_proveedor.rifpro, rpc_proveedor.nitpro".
				"  FROM cxp_solicitudes,cxp_dt_solicitudes,cxp_rd,rpc_proveedor".
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."'".
				"   AND cxp_solicitudes.numsol='".$as_numsop."'".
				"   AND cxp_rd.codproalt='".$as_codproalt."'".
				"   AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_rd.codemp=rpc_proveedor.codemp".
				"   AND cxp_rd.codproalt=rpc_proveedor.cod_pro";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_update_datos_proveedor ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_i=$ai_i+1;
				$ls_codproalt=$row["cod_pro"];
				$ls_nomproalt=$row["nompro"];
				$ls_dirproalt=$row["dirpro"];
				$ls_rifproalt=$row["rifpro"];
				$ls_nitproalt=$row["nitpro"];
				$lb_valido=$this->uf_update_cabecera($as_codded,$as_nrocomp,$ls_codproalt,$ls_nomproalt,$ls_dirproalt,$ls_rifproalt,$ls_nitproalt);
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}

	function uf_update_cabecera($as_codded,$as_nrocomp,$as_codproalt,$as_nomproalt,$as_dirproalt,$as_rifproalt,$as_nitproalt)
	{
	    //////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_cabecera
		//		   Access: public
		//		 Argument: $as_numrecdoc // N?mero de Recepcion de Documento
		//                 $as_codprobene // Codigo del proveedor o beneficiario 
		//                 $as_codret // Codigo de Retencion 
		//                 $as_tipo // Indica si el codprobene es un proveedor o un beneficiario 
		//	  Description: Funci?n que actualiza el campo estcmp al valor 1 en la tabla cxp_rd_deducciones lo
		//                 que indica que ese item ya fue procesado en un comprobante
		//	   Creado Por: Ing. Gerardo Cordero
		// Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE scb_cmp_ret".
				"   SET codsujret='".$as_codproalt."',nomsujret='".$as_nomproalt."',".
				"       dirsujret='".$as_dirproalt."',rif='".$as_rifproalt."',nit='".$as_nitproalt."'".
		        " WHERE codemp='".$this->la_empresa["codemp"]."'".
				"   AND codret='".$as_codded."'". 
				"   AND numcom='".$as_nrocomp."'";
		$li_result=$this->io_sql->execute($ls_sql);
		if($li_result===false)
		{	
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_update_cabecera ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
    }	  
	

	function uf_actualizar_estcmp($as_numrecdoc,$as_codprobene,$as_codret,$as_tipo)
	{
	    //////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_actualizar_estcmp
		//		   Access: public
		//		 Argument: $as_numrecdoc // N?mero de Recepcion de Documento
		//                 $as_codprobene // Codigo del proveedor o beneficiario 
		//                 $as_codret // Codigo de Retencion 
		//                 $as_tipo // Indica si el codprobene es un proveedor o un beneficiario 
		//	  Description: Funci?n que actualiza el campo estcmp al valor 1 en la tabla cxp_rd_deducciones lo
		//                 que indica que ese item ya fue procesado en un comprobante
		//	   Creado Por: Ing. Gerardo Cordero
		// Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($as_tipo=="P"){
		   $ls_filtro="cod_pro='".$as_codprobene."'";
		 }
		 elseif($as_tipo="B"){
		   $ls_filtro="ced_bene='".$as_codprobene."'";
		 }
		$ls_sql="UPDATE cxp_rd_deducciones".
				"   SET estcmp='1'".
		        " WHERE codemp='".$this->la_empresa["codemp"]."'".
				"   AND numrecdoc='".$as_numrecdoc."'". 
				"   AND ".$ls_filtro."".
				"   AND codded='".$as_codret."'";
		$li_result=$this->io_sql->execute($ls_sql);
		if($li_result===false)
		{	
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_actualizar_estcmp ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
    }	  
	
/******************************************************************************************************************/
/************************   UTILIDADES (CORELATIVOS,VALIDACIONES)       *******************************************/	
/******************************************************************************************************************/

function uf_get_nrocomprobante($as_codret,$as_periodofiscal,$as_nrocomp)
{
 	    //////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_get_nrocomprobante
		//		   Access: public
		//		 Argument: $as_codret // Codigo de Retencion 
		//                 $as_periodofiscal // Perido fiscal AAAAMM 
		//                 $as_nrocomp // Numero del Comprobante generado
		//	  Description: Funci?n que genera el numero del comprobante
		//	   Creado Por: Ing. Gerardo Cordero
		// Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql=" SELECT numcom ".
				"   FROM scb_cmp_ret".
				"  WHERE codemp='".$this->la_empresa["codemp"]."'".
				"    AND codret='".$as_codret."'".
				"  ORDER by numcom desc ";
		$rs_result=$this->io_sql->select($ls_sql);		
		if($rs_result===false)
		{
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_get_nrocomprobante ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			return false;			
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_result))
			{
				$ls_nrocom=$this->io_keygen->uf_generar_numero_nuevo("CXP","scb_cmp_ret","SUBSTR(numcom,7,14)","CXPCMP",8,"","codret",$as_codret);
				$as_nrocomp=$as_periodofiscal.$ls_nrocom;
			}
			else
			{
			   $codigo=$this->uf_load_numeroinicial($as_codret);
			   if($codigo==0)
			   {
			   		$codigo=1;
			   }
			   $as_nrocomp=$this->io_function->uf_cerosizquierda($codigo,8);
			   $this->io_sql->free_result($rs_result);
			   $as_nrocomp=$as_periodofiscal.$as_nrocomp;
			}
		}

/*		$this->ds_numcmp= new class_datastore();
		$ls_sql=" SELECT numcom ".
				"   FROM scb_cmp_ret".
				"  WHERE codemp='".$this->la_empresa["codemp"]."'".
				"    AND codret='".$as_codret."'".
				"  ORDER by numcom desc ";
		$rs_result=$this->io_sql->select($ls_sql);		
		if($rs_result===false)
		{
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_get_nrocomprobante ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			return false;			
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_result))
			{
				$li_i=$li_i+1;
			   	$codigo =$row["numcom"];				   
			    $codigo =substr($codigo,6,9);			      			   		   
				$this->ds_numcmp->insertRow("codigo",$codigo);
			}
			if($li_i>0)
			{
				$this->ds_numcmp->sortData("codigo");
				$ls_codigo=$this->ds_numcmp->getValue("codigo",$li_i);
				settype($ls_codigo,'int');
			    $li_newcodigo =$ls_codigo + 1;                             
			    settype($li_newcodigo,'string');  
				$ls_nrocomp=$this->io_function->uf_cerosizquierda($li_newcodigo,8);
			    $as_nrocomp=$as_periodofiscal.$ls_nrocomp;
			    $this->io_sql->free_result($rs_result);
			    return true;
			}
		    else
		    {
			   $codigo=$this->uf_load_numeroinicial();
			   $as_nrocomp=$this->io_function->uf_cerosizquierda($codigo,8);
			   $this->io_sql->free_result($rs_result);
			   $as_nrocomp=$as_periodofiscal.$as_nrocomp;
   			   return true;
		    }							
		}	*/	
		return 	$as_nrocomp;
	}	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_numeroinicial($as_codret)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_numeroinicial
		//		   Access: public
		//		 Argument: $as_codret // Codigo de retencion
		//	  Description: Funci?n que busca la configuracion del numero inicial
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 26/02/2008								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_concomiva=1;
		$ls_sql="SELECT concomiva,concommun,concommil,valiniislr ".
				"  FROM sigesp_empresa ".
				" WHERE codemp='".$this->ls_codemp."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_load_numeroinicial ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				switch($as_codret)
				{
					case "0000000001":
						$ls_concomiva=$row["concomiva"];
					break;
					case "0000000003":
						$ls_concomiva=$row["concommun"];
					break;
					case "0000000005":
						$ls_concomiva=$row["concommil"];
					break;
					case "0000000004":
						$ls_concomiva=$row["concommun"];
					break;
					case "0000000006":
						$ls_concomiva=$row["valiniislr"];
					break;
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $ls_concomiva;
	}// end function uf_load_numeroinicial
	//-----------------------------------------------------------------------------------------------------------------------------------
	
function uf_get_nrooperacion($as_num)
	{
 	    //////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_get_nrooperacion
		//		   Access: public
		//		 Argument: $as_num // Numero de operacion 
		//	  Description: Funci?n que le da el formato al numero de operacion
		//	   Creado Por: Ing. Gerardo Cordero
		// Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
	                          
	   settype($as_num,'string');                         
	   $ls_codigo=$this->io_function->uf_cerosizquierda($as_num,10);
	   return $ls_codigo;
				
	}
	
	function uf_validar_estempresa()
	{
 	     //////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estempresa
		//		   Access: public
		//		 Argument: Sin argumentos 
		//	  Description: Funci?n que valida segun la configuracion de empresa si los comprobantes de
		//                 impuesto municipal pueden ser generados por Cuentas por pagar 
		//	   Creado Por: Ing. Gerardo Cordero
		// Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
	    $lb_flag=true;
	    $ls_sql="SELECT modageret ".
				"  FROM sigesp_empresa". 
				" WHERE codemp='".$this->la_empresa["codemp"]."'";
		$rs_result=$this->io_sql->select($ls_sql);		
		if($rs_result===false)
		{
		    $this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_validar_estempresa ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			return false;			
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_result))
			{  
				$ls_modret=$row["modageret"];
				if($ls_modret=='B')
				{
					$lb_flag=false;
				}
			}
		}
		return $lb_flag;	
	}
	
	function uf_validar_rdmanual($as_codprobene,$aa_rd,$as_tipo)
	{
 	    ///////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_rdmanual
		//		   Access: public
		//		 Argument: $as_codprobene // Codigo del proveedo o beneficiario 
		//                 $as_tipo // Indica si el codprobene es un proveedor o un beneficiario 
	    //                 $aa_rd // arreglo que contiene los detalles de la recepcion 
		//	  Description: Funci?n que valida un grupo de detalles en el caso de las recepciones manuales
		//	   Creado Por: Ing. Gerardo Cordero
		// Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
	    $lb_flag=false;
		if($as_tipo=="P"){
		   $ls_filtro="cod_pro='".$as_codprobene."'";
		 }
		 elseif($as_tipo="B"){
		   $ls_filtro="ced_bene='".$as_codprobene."'";
		 }
		$this->io_datavali->data=$aa_rd;
        $li_tfilas=$this->io_datavali->getRowCount("id");

        for($li_j=1;$li_j<=$li_tfilas;$li_j++)
		{
			$ls_coddoc=$this->io_datavali->getValue("numrecdoc",$li_j);
			$ls_sql=" SELECT numrecdoc".
					"   FROM cxp_rd_scg". 
					"  WHERE codemp='".$this->la_empresa["codemp"]."'".
					"    AND numrecdoc='".$ls_coddoc."'". 
					"    AND ".$ls_filtro."".
					"    AND (estasicon='M' OR estasicon='A')".
					"    AND debhab='H'";
			$rs_result=$this->io_sql->select($ls_sql);		
			if($rs_result===false)
			{
				$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_validar_rdmanual ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
				return false;			
			}
			else
			{
				if ($row=$this->io_sql->fetch_row($rs_result))
				{
					$lb_flag=true;  
				}
			}
		}
		return $lb_flag;	
	}
	
	function uf_validar_dt_rdmanual($as_codprobene,$as_numrd,$as_cuenta,$as_tipo)
	{
 	    ///////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_dt_rdmanual
		//		   Access: public
		//		 Argument: $as_codprobene // Codigo del proveedo o beneficiario 
		//                 $as_tipo // Indica si el codprobene es un proveedor o un beneficiario 
	    //                 $as_munrd // numero de la recepcion de documento
		//				   $as_cuenta //Codigo de la cuenta contable asociada a ese detalle
		//	  Description: Funci?n que valida un grupo de detalles en el caso de las recepciones manuales
		//	   Creado Por: Ing. Gerardo Cordero
		// Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
	    $lb_flag=false;
		if($as_tipo=="P"){
		   $ls_filtro="cod_pro='".$as_codprobene."'";
		 }
		 elseif($as_tipo="B"){
		   $ls_filtro="ced_bene='".$as_codprobene."'";
		 }
		$ls_sql="SELECT estasicon,debhab". 
		        "  FROM cxp_rd_scg ".
			    " WHERE codemp='".$this->la_empresa["codemp"]."'".
				"   AND numrecdoc='".$as_numrd."'". 
				"   AND ".$ls_filtro."".
				"   AND sc_cuenta='".$as_cuenta."'";
		$rs_result=$this->io_sql->select($ls_sql);		
		if($rs_result===false)
		{
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_validar_dt_rdmanual ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			return false;			
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_result))
			{
				$ls_estasi=$row["estasicon"];
				$ls_debhab=$row["debhab"];
				if($ls_estasi=='A')
				{
					$lb_flag=true;  
				}
			    elseif($ls_estasi=='M')
				{
					if($ls_debhab=='D')
					{
						$lb_flag=true;  
					} 
				}
			}
		}
		return $lb_flag;	
	}
	
	function uf_buscar_monto_rdmanual($as_codprobene,$as_numrd,$as_tipo)
	{
 	    ///////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_monto_rdmanual
		//		   Access: public
		//		 Argument: $as_codprobene // Codigo del proveedo o beneficiario 
		//                 $as_tipo // Indica si el codprobene es un proveedor o un beneficiario 
	    //                 $as_munrd // numero de la recepcion de documento
		//	  Description: Funci?n que ubica el monto de el detalle manual incluido por el haber para balancear
		//                 el monto total presentado en el comprobante
		//	   Creado Por: Ing. Gerardo Cordero
		// Fecha Creaci?n: 18/09/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
	    $ld_monto=0;
		if($as_tipo=="P"){
		   $ls_filtro="cod_pro='".$as_codprobene."'";
		 }
		 elseif($as_tipo="B"){
		   $ls_filtro="ced_bene='".$as_codprobene."'";
		 }
		$ls_sql="SELECT  SUM(monto) AS monto ". 
		        "  FROM   cxp_rd_scg ".
			    " WHERE  codemp='".$this->la_empresa["codemp"]."'".
				"   AND numrecdoc='".$as_numrd."'". 
				"   AND ".$ls_filtro."".
				"   AND estasicon='M'".
				"   AND debhab='H'";
		$rs_result=$this->io_sql->select($ls_sql);		
		if($rs_result===false)
		 {
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_buscar_monto_rdmanual ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			return false;			
		 }
		 else
		 {
			if ($row=$this->io_sql->fetch_row($rs_result))
			{
				$ld_monto=$row["monto"];
			}
		 }
		 return $ld_monto;	
	}
	
	function uf_cmb_mes($as_mes)
	{
		///////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cmb_mes
		//		   Access: public
		//		 Argument: $as_mes // numero que representa el mes en curso 
		//	  Description: Funci?n que construye el combo de meses
		//	   Creado Por: Ing. Gerardo Cordero
		// Fecha Creaci?n: 26/09/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		switch ($as_mes) {
		   case '01':
			   $lb_selEnero="selected";
			   $lb_selFebrero="";
			   $lb_selMarzo="";
			   $lb_selAbril="";
			   $lb_selMayo="";
			   $lb_selJunio="";
			   $lb_selJulio="";
			   $lb_selAgosto="";
			   $lb_selSeptiembre="";
			   $lb_selOctubre="";
			   $lb_selNoviembre="";	
			   $lb_selDiciembre="";
			   break;
		   case '02':
			   $lb_selEnero="";
			   $lb_selFebrero="selected";
			   $lb_selMarzo="";
			   $lb_selAbril="";
			   $lb_selMayo="";
			   $lb_selJunio="";
			   $lb_selJulio="";
			   $lb_selAgosto="";
			   $lb_selSeptiembre="";
			   $lb_selOctubre="";
			   $lb_selNoviembre="";	
			   $lb_selDiciembre="";
			   break;
		   case '03':
			   $lb_selEnero="";
			   $lb_selFebrero="";
			   $lb_selMarzo="selected";
			   $lb_selAbril="";
			   $lb_selMayo="";
			   $lb_selJunio="";
			   $lb_selJulio="";
			   $lb_selAgosto="";
			   $lb_selSeptiembre="";
			   $lb_selOctubre="";
			   $lb_selNoviembre="";	
			   $lb_selDiciembre="";
			   break;
		   case '04':
			   $lb_selEnero="";
			   $lb_selFebrero="";
			   $lb_selMarzo="";
			   $lb_selAbril="selected";
			   $lb_selMayo="";
			   $lb_selJunio="";
			   $lb_selJulio="";
			   $lb_selAgosto="";
			   $lb_selSeptiembre="";
			   $lb_selOctubre="";
			   $lb_selNoviembre="";	
			   $lb_selDiciembre="";
			   break;
		   case '05':
			   $lb_selEnero="";
			   $lb_selFebrero="";
			   $lb_selMarzo="";
			   $lb_selAbril="";
			   $lb_selMayo="selected";
			   $lb_selJunio="";
			   $lb_selJulio="";
			   $lb_selAgosto="";
			   $lb_selSeptiembre="";
			   $lb_selOctubre="";
			   $lb_selNoviembre="";	
			   $lb_selDiciembre="";
			   break;
		   case '06':
			   $lb_selEnero="";
			   $lb_selFebrero="";
			   $lb_selMarzo="";
			   $lb_selAbril="";
			   $lb_selMayo="";
			   $lb_selJunio="selected";
			   $lb_selJulio="";
			   $lb_selAgosto="";
			   $lb_selSeptiembre="";
			   $lb_selOctubre="";
			   $lb_selNoviembre="";	
			   $lb_selDiciembre="";
			   break;		   
		   case '07':
			   $lb_selEnero="";
			   $lb_selFebrero="";
			   $lb_selMarzo="";
			   $lb_selAbril="";
			   $lb_selMayo="";
			   $lb_selJunio="";
			   $lb_selJulio="selected";
			   $lb_selAgosto="";
			   $lb_selSeptiembre="";
			   $lb_selOctubre="";
			   $lb_selNoviembre="";	
			   $lb_selDiciembre="";
			   break;		   		 
		   case '08':
			   $lb_selEnero="";
			   $lb_selFebrero="";
			   $lb_selMarzo="";
			   $lb_selAbril="";
			   $lb_selMayo="";
			   $lb_selJunio="";
			   $lb_selJulio="";
			   $lb_selAgosto="selected";
			   $lb_selSeptiembre="";
			   $lb_selOctubre="";
			   $lb_selNoviembre="";	
			   $lb_selDiciembre="";
			   break;		   		 		     
		   case '09':
			   $lb_selEnero="";
			   $lb_selFebrero="";
			   $lb_selMarzo="";
			   $lb_selAbril="";
			   $lb_selMayo="";
			   $lb_selJunio="";
			   $lb_selJulio="";
			   $lb_selAgosto="";
			   $lb_selSeptiembre="selected";
			   $lb_selOctubre="";
			   $lb_selNoviembre="";	
			   $lb_selDiciembre="";
			   break;
		   case '10':
			   $lb_selEnero="";
			   $lb_selFebrero="";
			   $lb_selMarzo="";
			   $lb_selAbril="";
			   $lb_selMayo="";
			   $lb_selJunio="";
			   $lb_selJulio="";
			   $lb_selAgosto="";
			   $lb_selSeptiembre="";
			   $lb_selOctubre="selected";
			   $lb_selNoviembre="";	
			   $lb_selDiciembre="";
			   break;		   
		   case '11':
			   $lb_selEnero="";
			   $lb_selFebrero="";
			   $lb_selMarzo="";
			   $lb_selAbril="";
			   $lb_selMayo="";
			   $lb_selJunio="";
			   $lb_selJulio="";
			   $lb_selAgosto="";
			   $lb_selSeptiembre="";
			   $lb_selOctubre="";
			   $lb_selNoviembre="selected";	
			   $lb_selDiciembre="";
			   break;		   
		   case '12':
			   $lb_selEnero="";
			   $lb_selFebrero="";
			   $lb_selMarzo="";
			   $lb_selAbril="";
			   $lb_selMayo="";
			   $lb_selJunio="";
			   $lb_selJulio="";
			   $lb_selAgosto="";

			   $lb_selSeptiembre="";
			   $lb_selOctubre="";
			   $lb_selNoviembre="";	
			   $lb_selDiciembre="selected";
			   break;		   
		}
	
		print "<select name=mes id=mes onchange=validarmes();>";
		print "<option value=01 ".$lb_selEnero.">ENERO</option>";
		print "<option value=02 ".$lb_selFebrero.">FEBRERO</option>";
		print "<option value=03 ".$lb_selMarzo.">MARZO</option>";
		print "<option value=04 ".$lb_selAbril.">ABRIL</option>";
		print "<option value=05 ".$lb_selMayo.">MAYO</option>";
		print "<option value=06 ".$lb_selJunio.">JUNIO</option>";
		print "<option value=07 ".$lb_selJulio.">JULIO</option>";
		print "<option value=08 ".$lb_selAgosto.">AGOSTO</option>";
		print "<option value=09 ".$lb_selSeptiembre.">SEPTIEMBRE</option>";
		print "<option value=10 ".$lb_selOctubre.">OCTUBRE</option>";
		print "<option value=11 ".$lb_selNoviembre.">NOVIEMBRE</option>";
		print "<option value=12 ".$lb_selDiciembre.">DICIEMBRE</option>";
		print "</select>";
	}
	
function uf_validar_periodo_fiscal($as_codret,$as_periodofiscal)
{
 	    //////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_periodo_fiscal
		//		   Access: public
		//		 Argument: $as_codret // Codigo de Retencion 
		//                 $as_periodofiscal // Perido fiscal AAAAMM 
		//                 $as_nrocomp // Numero del Comprobante generado
		//	  Description: Funci?n que genera el numero del comprobante
		//	   Creado Por: Ing. Gerardo Cordero
		// Fecha Creaci?n: 13/09/2007								Fecha ?ltima Modificaci?n : 13/09/2007
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql=" SELECT numcom ".
				"   FROM scb_cmp_ret".
				"  WHERE codemp='".$this->la_empresa["codemp"]."'".
				"    AND codret='".$as_codret."'".
				"  ORDER by numcom desc ";
		$rs_result=$this->io_sql->select($ls_sql);		
		if($rs_result===false)
		{
			$this->io_msg->message("CLASE->Generar Comprobate M?TODO->uf_validar_periodo_fiscal ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			return false;			
		}
		else
		{
			if((!$rs_result->EOF))
			{	
				$ls_nroactual=$rs_result->fields["numcom"];
				$ls_periodobd=substr($ls_nroactual,0,6);
				if($ls_periodobd<=$as_periodofiscal)
				{
					$lb_valido=true;
				}
			}
			else
			{
				$lb_valido=true;
			}
		}

		return 	$lb_valido;
	}	
	
}
?>
