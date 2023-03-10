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

class sigesp_cxp_c_ncnd
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_id_process;
	var $ls_codemp;
	var $io_dscuentas;
	var $io_fun_cxp;
	
	public function __construct($as_path)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_cxp_c_ncnd
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Nelson Barraez
		//  Fecha Creacin: 06/04/2007 								Fecha Ultima Modificacin : 03/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
	    require_once($as_path."base/librerias/php/general/sigesp_lib_fecha.php");		
		$this->io_fecha= new class_fecha();
		require_once($as_path."shared/class_folder/class_generar_id_process.php");
		$this->io_id_process=new class_generar_id_process();		
		require_once($as_path."base/librerias/php/general/sigesp_lib_datastore.php");
		$this->ds_cargos=new class_datastore();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->io_fun_cxp=new class_funciones_cxp();
		require_once("class_funciones_cxp.php");
		$this->io_cxp= new class_funciones_cxp();
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_cxp_p_ncnd.php)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing.Nelson Barraez
		//  Fecha Creacin: 06/04/2007								Fecha ltima Modificacin : 03/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fecha);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_guardar($la_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_cxp_p_ncnd.php)
		//	  Description: Funcion que procesa los datos relacionados a las notas de debito o credito
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacion: 28/05/2007								Fecha Ultima Modificacion : 02/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_numncnd   = trim($this->io_fun_cxp->uf_obtenervalor("txtnumncnd",""));
		$ls_numord    = $this->io_fun_cxp->uf_obtenervalor("txtnumord","");
	    $ls_tipproben = $this->io_fun_cxp->uf_obtenervalor("tipproben","");
		$ls_codproben = trim($this->io_fun_cxp->uf_obtenervalor("txtcodproben",""));
		$ls_numrecdoc = trim($this->io_fun_cxp->uf_obtenervalor("txtnumrecdoc",""));
		$ls_tipdoc    = $this->io_fun_cxp->uf_obtenervalor("txttipdoc","");
		$ls_connota   = $this->io_fun_cxp->uf_obtenervalor("txtconnota","");
		$ld_fecha     = $this->io_fun_cxp->uf_obtenervalor("txtfecregsol","");
		$li_numrowspre= $this->io_fun_cxp->uf_obtenervalor("numrowsprenota",0);
		$li_numrowscon= $this->io_fun_cxp->uf_obtenervalor("numrowsconnota",0);
		$ls_tiponota  = $this->io_fun_cxp->uf_obtenervalor("tiponota","");
		$ldec_monto   = $this->io_fun_cxp->uf_obtenervalor("txtmonto","");
		$ldec_moncar  = $this->io_fun_cxp->uf_obtenervalor("txtmontocargo","");
		$ls_existe    = $this->io_fun_cxp->uf_obtenervalor("existe","");//Variable que controla la operacion en la pantalla de Nota de Debito/Credito
		$ls_estsol    = $this->io_fun_cxp->uf_obtenervalor("txtestsol","");
		$ld_fecemi    = $this->io_fun_cxp->uf_obtenervalor("txtfecemi","");
		$ls_estlibcom    = $this->io_fun_cxp->uf_obtenervalor("chkestlibcom","0");
		$ldec_monto   = str_replace(".","",$ldec_monto);
		$ldec_monto   = str_replace(",",".",$ldec_monto);
		$ldec_moncar   = str_replace(".","",$ldec_moncar);
		$ldec_moncar   = str_replace(",",".",$ldec_moncar);
		$this->io_sql->begin_transaction();
		if(!$this->uf_existe_cabecera($ls_numncnd,$ls_numord,$ls_tipproben,$ls_codproben,$ls_numrecdoc,$ls_tipdoc,$ls_tiponota))
		{
			$lb_valido=$this->uf_guardar_cabecera($ls_numncnd,$ls_numord,$ls_tipproben,$ls_codproben,$ls_numrecdoc,$ls_tipdoc,
												  $ls_connota,$ld_fecha,$ls_tiponota,$ldec_monto,$ls_estsol,$ldec_moncar,$ld_fecemi,$ls_estlibcom,$la_seguridad);
			if($lb_valido)			
			{
				$lb_valido=$this->uf_guardar_detalle($ls_numncnd,$ls_numord,$ls_tipproben,$ls_codproben,$ls_numrecdoc,$ls_tipdoc,$ls_tiponota,$li_numrowspre,$li_numrowscon,$lb_existe=false,$la_seguridad);
			}
		}
		elseif($ls_existe=='TRUE')
		{
			$lb_valido=$this->uf_actualizar_cabecera($ls_numncnd,$ls_numord,$ls_tipproben,$ls_codproben,$ls_numrecdoc,$ls_tipdoc,$ls_connota,$ld_fecha,$ls_tiponota,$ldec_monto,$ls_estsol,$ld_fecemi,$ls_estlibcom,$la_seguridad);
			$lb_valido=$this->uf_guardar_detalle($ls_numncnd,$ls_numord,$ls_tipproben,$ls_codproben,$ls_numrecdoc,$ls_tipdoc,$ls_tiponota,$li_numrowspre,$li_numrowscon,$lb_existe=true,$la_seguridad);
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("La Nota que intenta registrar ya existe");
		}
		
		$this->uf_procesar_solicitud($_SESSION["la_empresa"]["codemp"], $ls_numord, $ldec_monto);
		
		if($lb_valido)
		{	
			$this->io_mensajes->message("La Nota fue Registrada");
			$this->io_sql->commit();
		}
		else
		{
			$this->io_sql->rollback();
		}	
		return $lb_valido;			
	}
	
	function uf_existe_cabecera($ls_numncnd,$ls_numord,$ls_tipproben,$ls_codproben,$ls_numrecdoc,$ls_tipdoc,$ls_tiponota)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_existe_cabecera
		//	  Description: Funcion que verifica si existe la nota de debito o credito asociada a la recepcion de documento y solicitud especificada
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacion: 02/06/2007								Fecha ltima Modificacin : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		if($ls_tipproben=='P')
		{
			$ls_codpro=$ls_codproben;
			$ls_cedbene='----------';
		}
		else
		{
			$ls_codpro='----------';
			$ls_cedbene=$ls_codproben;
		}
		$ls_sql="SELECT numsol 
				 FROM cxp_sol_dc 
				 WHERE codemp='".$ls_codemp."'  AND numsol='".$ls_numord."' AND numrecdoc='".$ls_numrecdoc."' 
				 AND codtipdoc='".$ls_tipdoc."' AND ced_bene='".$ls_cedbene."' AND cod_pro='".$ls_codpro."' 
				 AND codope='".$ls_tiponota."'  AND numdc='".$ls_numncnd."'";
		$rs_data=$this->io_sql->select($ls_sql);

		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_existe_cabecera ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				return true;
			}
			else
			{
				return false;	
			}			
		}
	}
	
	function uf_guardar_cabecera($ls_numncnd,$ls_numord,$ls_tipproben,$ls_codproben,$ls_numrecdoc,$ls_tipdoc,$ls_connota,
								 $ld_fecha,$ls_tiponota,$ldec_monto,$ls_estsol,$ldec_moncar,$ld_fecemi,$ls_estlibcom,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar_cabecera
		//	  Description: Funcion que guarda los datos de la cabecera relacionados a las notas de debito o credito
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacion: 28/05/2007								Fecha ltima Modificacin : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		if($ls_tipproben=='P')
		{
			$ls_codpro=$ls_codproben;
			$ls_cedbene='----------';
		}
		else
		{
			$ls_codpro='----------';
			$ls_cedbene=$ls_codproben;
		}
		if($ld_fecemi=="")
			$ld_fecemi=$ld_fecha;
		$ls_sql="INSERT INTO cxp_sol_dc(codemp, numsol, numrecdoc, codtipdoc, ced_bene, cod_pro, 
										codope, numdc,desope, fecope, 
										monto, estnotadc, estafe, estapr, codusuapr, fecaprnc,moncar,fecemi,estlibcom)
				 VALUES('".$ls_codemp."','".$ls_numord."','".$ls_numrecdoc."','".$ls_tipdoc."','".$ls_cedbene."','".$ls_codpro."',
				 		'".$ls_tiponota."','".$ls_numncnd."','".$ls_connota."','".$this->io_funciones->uf_convertirdatetobd($ld_fecha)."',
						'".$ldec_monto."','".$ls_estsol."','0','0','','1900-01-01','".$ldec_moncar."','".$this->io_funciones->uf_convertirdatetobd($ld_fecemi)."','".$ls_estlibcom."')";						
		$li_rows=$this->io_sql->execute($ls_sql);
		if($li_rows===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_guardar_cabacera ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Inserto la Nota ".$ls_numncnd." de tipo ".$ls_tiponota." Asociado a la solicitud ".$ls_numord." y a la recepcion ".$ls_numrecdoc;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
			if($this->ls_supervisor!=0)
			{
				$ls_fromname="Nota Debito - Credito";
				$ls_bodyenv="Se le envia la notificaci?n de actualizaci?n en el modulo de CXP, se insert? la Nota de Credito - Debito  N?.. ";
				$ls_nomper=$_SESSION["la_nomusu"];
				$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$ls_numncnd,$ls_bodyenv,$ls_nomper);
			}
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			return true;
		}
	}
	
	function uf_actualizar_cabecera($ls_numncnd,$ls_numord,$ls_tipproben,$ls_codproben,$ls_numrecdoc,$ls_tipdoc,$ls_connota,$ld_fecha,$ls_tiponota,$ldec_monto,
								    $ls_estsol,$ld_fecemi,$ls_estlibcom,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar_cabecera
		//	  Description: Funcion que guarda los datos de la cabecera relacionados a las notas de debito o credito
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacion: 28/05/2007								Fecha ltima Modificacin : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		if($ls_tipproben=='P')
		{
			$ls_codpro=$ls_codproben;
			$ls_cedbene='----------';
		}
		else
		{
			$ls_codpro='----------';
			$ls_cedbene=$ls_codproben;
		}
		if($ld_fecemi=="")
			$ld_fecemi=$ld_fecha;
		$ls_sql="UPDATE cxp_sol_dc SET desope='".$ls_connota."', monto='".$ldec_monto."', fecemi='".$this->io_funciones->uf_convertirdatetobd($ld_fecemi)."',estlibcom ='".$ls_estlibcom."'
				 WHERE codemp='".$ls_codemp."'    AND numsol='".$ls_numord."' AND numrecdoc='".$ls_numrecdoc."'
				 AND   codtipdoc='".$ls_tipdoc."' AND ced_bene='".$ls_cedbene."' AND  cod_pro='".$ls_codpro."' 
				 AND   codope='".$ls_tiponota."'  AND numdc='".$ls_numncnd."'";
		$li_rows=$this->io_sql->execute($ls_sql);
		if($li_rows===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_actualizar_cabacera ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizo la Nota ".$ls_numncnd." de tipo ".$ls_tiponota." Asociado a la solicitud ".$ls_numord." y a la recepcion ".$ls_numrecdoc;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
			if($this->ls_supervisor!=0)
			{
				$ls_fromname="Nota Debito - Credito";
				$ls_bodyenv="Se le envia la notificaci?n de actualizaci?n en el modulo de CXP, se actualiz? la Nota de Credito - Debito  N?.. ";
				$ls_nomper=$_SESSION["la_nomusu"];
				$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$ls_numncnd,$ls_bodyenv,$ls_nomper);
			}
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			return true;
		}
	}
	
	
	function uf_guardar_detalle($ls_numncnd,$ls_numord,$ls_tipproben,$ls_codproben,$ls_numrecdoc,$ls_tipdoc,$ls_tiponota,
								$li_numrowspre,$li_numrowscon,$lb_existe,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar_detalle
		//	  Description: Funcion que se encarga de registrar los detalles de la nota de debito o credito 
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacion: 02/06/2007								Fecha ltima Modificacin : 03/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido	  = true;
		$ls_estafenc  = $_SESSION["la_empresa"]["estafenc"];	
		$ls_codemp	  = $_SESSION["la_empresa"]["codemp"];	
		$ls_modalidad = $_SESSION['la_empresa']['estmodest'];	
		if ($ls_tipproben=='P')
		   {
			 $ls_codpro=$ls_codproben;
			 $ls_cedbene='----------';
		   }
		else
		   {
			 $ls_codpro='----------';
			 $ls_cedbene=$ls_codproben;
		   }
		if ($li_numrowspre>=1)
		   {
			 $arrResultado=$this->io_cxp->uf_verificar_cierre_spg("../",$ls_estciespg);
			$lb_valido=$arrResultado["lb_valido"];
			$ls_estciespg=$arrResultado["as_estciespg"];
			 if ($ls_estciespg=="1")
			    {
				  $this->io_mensajes->message("Esta procesado el cierre presupuestario");
				  return false;
			    }
		   }
		for($li=1;$li<=$li_numrowspre;$li++)
		   {
			 $ls_cuentaspg    = trim($this->io_fun_cxp->uf_obtenervalor("txtcuentaspgncnd".$li,""));
			 $ls_codestpro    = $this->io_fun_cxp->uf_obtenervalor("txtcodestproncnd".$li,"");
			 $ls_codestproaux = $this->io_fun_cxp->uf_obtenervalor("txtcodpro".$li,"");
			 $ls_estcla 	  = $this->io_fun_cxp->uf_obtenervalor("txtestclancnd".$li,"");
			 $ldec_monto      = $this->io_fun_cxp->uf_obtenervalor("txtmontoncnd".$li,"");
			 $ls_procede    = trim($this->io_fun_cxp->uf_obtenervalor("txtprocede".$li,""));
			 $ls_numdoccom    = trim($this->io_fun_cxp->uf_obtenervalor("txtnumdoccom".$li,""));
			 $ls_codfuefin    = trim($this->io_fun_cxp->uf_obtenervalor("txtcodfuefinncnd".$li,"--"));
			 $ldec_monto      = str_replace(".","",$ldec_monto);
			 $ldec_monto      = str_replace(",",".",$ldec_monto);
			 $ldec_monto      = abs($ldec_monto);
			 switch($ls_modalidad)
			 {
				case "1": // Modalidad por Proyecto
					$ls_codestpro=$ls_codestpro."0000";
					break;						
				case "2": // Modalidad por Programa
					$ls_codestpronew=$this->io_funciones->uf_cerosizquierda(substr($ls_codestpro,0,2),20);
					$ls_codestpronew=$ls_codestpronew.$this->io_funciones->uf_cerosizquierda(substr($ls_codestpro,3,2),6);
					$ls_codestpronew=$ls_codestpronew.$this->io_funciones->uf_cerosizquierda(substr($ls_codestpro,6,2),3);
					$ls_codestpronew=$ls_codestpronew.substr($ls_codestpro,9,2);
					$ls_codestpronew=$ls_codestpronew.substr($ls_codestpro,12,2);
					$ls_codestpro=$ls_codestpronew;
					break;
			 }
				if(($ls_tiponota=="ND")||($ls_estafenc==0))
				{
				 if ($lb_existe && $li==1)
					{
					  $ls_sql = "DELETE FROM cxp_dc_spg 
								  WHERE codemp='".$ls_codemp."'
									AND numsol='".$ls_numord."'
									AND numrecdoc='".$ls_numrecdoc."'
									AND codtipdoc='".$ls_tipdoc."'
									AND ced_bene='".$ls_cedbene."'
									AND cod_pro='".$ls_codpro."' 
									AND codope='".$ls_tiponota."'
									AND numdc='".$ls_numncnd."'";
					  $li_rows = $this->io_sql->execute($ls_sql);
					  if ($li_rows===false)
						 {
						   $this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_guardar_detalle1 ERROR-> ".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
						   $lb_valido=false;
						 }
					  else
						 {
						   $lb_valido=true;
						 }	
					}
					$lb_update=$this->uf_select_presupuesto($ls_codemp, $ls_numord, $ls_numrecdoc, $ls_tipdoc, $ls_cedbene, $ls_codpro, $ls_tiponota, $ls_numncnd, 
															$ls_codestproaux, $ls_estcla, $ls_codfuefin, $ls_cuentaspg);
															
					if($lb_update)
					{
						$ls_sql =  "UPDATE cxp_dc_spg".
								   "  SET monto = monto+".$ldec_monto." ".
								   " WHERE cxp_dc_spg.codemp='".$ls_codemp."' ".
								   "   AND cxp_dc_spg.numsol='".$ls_numord."' ".
								   "   AND cxp_dc_spg.numrecdoc='".$ls_numrecdoc."' ".
								   "   AND cxp_dc_spg.codtipdoc='".$ls_tipdoc."' ".
								   "   AND cxp_dc_spg.ced_bene='".$ls_cedbene."' ".
								   "   AND cxp_dc_spg.cod_pro='".$ls_codpro."' ".
								   "   AND cxp_dc_spg.codope='".$ls_tiponota."' ".
								   "   AND cxp_dc_spg.numdc='".$ls_numncnd."' ".
								   "   AND cxp_dc_spg.codestpro='".$ls_codestproaux."' ".
								   "   AND cxp_dc_spg.estcla='".$ls_estcla."' ".
								   "   AND cxp_dc_spg.codfuefin='".$ls_codfuefin."' ".
								   "   AND cxp_dc_spg.spg_cuenta='".$ls_cuentaspg."' ";
						  $li_rows=$this->io_sql->execute($ls_sql);
						  if ($li_rows===false)
							 {
							   $this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_guardar_detalle2 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
							   return false;
							 }
					}
					else
					{
						if ($lb_valido)
						{
						  $ls_sql = "INSERT INTO cxp_dc_spg(codemp, numsol, numrecdoc, codtipdoc, ced_bene, cod_pro, codope, numdc,".
									"                       codestpro, estcla, spg_cuenta, monto, procede_doc, numdoccom, codfuefin)
									 VALUES('".$ls_codemp."','".$ls_numord."','".$ls_numrecdoc."','".$ls_tipdoc."','".$ls_cedbene."',".
									 "      '".$ls_codpro."','".$ls_tiponota."','".$ls_numncnd."','".$ls_codestproaux."',".
									 "      '".$ls_estcla."','".$ls_cuentaspg."','".$ldec_monto."','".$ls_procede."',".
									 "      '".$ls_numdoccom."','".$ls_codfuefin."')";
						  $li_rows=$this->io_sql->execute($ls_sql);
						  if ($li_rows===false)
							 {
							   $this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_guardar_detalle2 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
							   return false;
							 }
						  else
							 {
							   $lb_valido=true;
							   /////////////////////////////////         SEGURIDAD               /////////////////////////////		
							   $ls_evento="INSERT";
							   $ls_descripcion ="Registro el detalle presupuestario de la Nota ".$ls_numncnd." de tipo ".$ls_tiponota." Asociado a la solicitud ".$ls_numord." y a la recepcion ".$ls_numrecdoc." a la estructura ".$ls_codestpro." y cuenta ".$ls_cuentaspg;
							   $lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
															$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
															$aa_seguridad["ventanas"],$ls_descripcion);
							   /////////////////////////////////         SEGURIDAD               /////////////////////////////
							 }		
						}
					}
				}
				else
				{
				 if ($lb_existe && $li==1)
					{
					  $ls_sql = "DELETE FROM cxp_dc_spi 
								  WHERE codemp='".$ls_codemp."'
									AND numsol='".$ls_numord."'
									AND numrecdoc='".$ls_numrecdoc."'
									AND codtipdoc='".$ls_tipdoc."'
									AND ced_bene='".$ls_cedbene."'
									AND cod_pro='".$ls_codpro."' 
									AND codope='".$ls_tiponota."'
									AND numdc='".$ls_numncnd."'";
					  $li_rows = $this->io_sql->execute($ls_sql);
					  if ($li_rows===false)
						 {
						   $this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_guardar_detalle3 ERROR-> ".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
						   $lb_valido=false;
						 }
					  else
						 {
						   $lb_valido=true;
						 }	
					 }
					 if ($lb_valido)
					 {
					  if(trim($ls_codestproaux)=="")
					  {
						$ls_codestproaux="-----------------------------------------------------------------------------------------------------------------------------";
					  }
					  $ls_sql = "INSERT INTO cxp_dc_spi(codemp, numsol, numrecdoc, codtipdoc, ced_bene, cod_pro, codope, numdc,".
								"                       codestpro, estcla, spi_cuenta, monto, codfuefin)
								 VALUES('".$ls_codemp."','".$ls_numord."','".$ls_numrecdoc."','".$ls_tipdoc."','".$ls_cedbene."',".
								 "      '".$ls_codpro."','".$ls_tiponota."','".$ls_numncnd."','".$ls_codestproaux."',".
								 "      '".$ls_estcla."','".$ls_cuentaspg."','".$ldec_monto."','".$ls_codfuefin."')";
					  $li_rows=$this->io_sql->execute($ls_sql);
					  if ($li_rows===false)
						 {
						   $this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_guardar_detalle4 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
						   return false;
						 }
					  else
						 {
						   $lb_valido=true;
						   /////////////////////////////////         SEGURIDAD               /////////////////////////////		
						   $ls_evento="INSERT";
						   $ls_descripcion ="Registro el detalle presupuestario de la Nota ".$ls_numncnd." de tipo ".$ls_tiponota." Asociado a la solicitud ".$ls_numord." y a la recepcion ".$ls_numrecdoc." a la estructura ".$ls_codestpro." y cuenta ".$ls_cuentaspg;
						   $lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
														$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
														$aa_seguridad["ventanas"],$ls_descripcion);
						   /////////////////////////////////         SEGURIDAD               /////////////////////////////
						 }		
					}
					}
		   }
		if ($li_numrowscon>=1)
		   {
			 $arrResultado=$this->io_cxp->uf_verificar_cierre_scg("../",$ls_estciescg);
					$lb_valido=$arrResultado["lb_valido"];
					$ls_estciescg=$arrResultado["as_estciescg"];
			 if ($ls_estciescg=="1")
			    {
				  $this->io_mensajes->message("Esta procesado el cierre contable");
				  return false;
			    }
		   }
		for($la=1;$la<=$li_numrowscon;$la++)
		   {
			 $ls_cuentascg = trim($this->io_fun_cxp->uf_obtenervalor("txtscgcuentancnd".$la,""));
			 $ldec_mondeb  = $this->io_fun_cxp->uf_obtenervalor("txtdebencnd".$la,"");
			 $ldec_monhab  = $this->io_fun_cxp->uf_obtenervalor("txthaberncnd".$la,"");
			 $ldec_mondeb  = str_replace(".","",$ldec_mondeb);
			 $ldec_mondeb  = str_replace(",",".",$ldec_mondeb);
			 $ldec_monhab  = str_replace(".","",$ldec_monhab);
			 $ldec_monhab  = str_replace(",",".",$ldec_monhab);
			 if ($ldec_mondeb==0)
				{
				  $ls_debhab='H';
				  $ldec_monto=$ldec_monhab;
				}
			 else
				{
				  $ls_debhab='D';
				  $ldec_monto=$ldec_mondeb;
				}
			 if ($lb_existe && $la==1)
			    {
				  $ls_sql = "DELETE FROM cxp_dc_scg 
						      WHERE codemp='".$ls_codemp."'
							    AND numsol='".$ls_numord."'
								AND numrecdoc='".$ls_numrecdoc."'
						        AND codtipdoc='".$ls_tipdoc."'
								AND ced_bene='".$ls_cedbene."'
								AND cod_pro='".$ls_codpro."' 
						        AND codope='".$ls_tiponota."'
								AND numdc='".$ls_numncnd."'";
				  $li_rows = $this->io_sql->execute($ls_sql);
				  if ($li_rows===false)
					 {
					   $this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_guardar_detalle5 ERROR-> ".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					   $lb_valido=false;
					 }
				  else
					 {
					   $lb_valido=true;
					 }	
			    }
			 if ($lb_valido)
			    { 
				  $ls_sql = "INSERT INTO cxp_dc_scg(codemp, numsol, numrecdoc, codtipdoc, ced_bene, cod_pro, codope, numdc, debhab, sc_cuenta, monto,estgenasi)
						     VALUES('".$ls_codemp."','".$ls_numord."','".$ls_numrecdoc."','".$ls_tipdoc."','".$ls_cedbene."','".$ls_codpro."','".$ls_tiponota."','".$ls_numncnd."','".$ls_debhab."','".$ls_cuentascg."','".$ldec_monto."','".$la."')";
				  $li_rows = $this->io_sql->execute($ls_sql);
				  if ($li_rows===false)
				     {
					   $this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_guardar_detalle6 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					   return false;
				     }
				  else
					 {
					   $lb_valido=true;
					   /////////////////////////////////         SEGURIDAD               /////////////////////////////		
					   $ls_evento="INSERT";
					   $ls_descripcion ="Registro el detalle contable de la Nota ".$ls_numncnd." de tipo ".$ls_tiponota." Asociado a la solicitud ".$ls_numord." y a la recepcion ".$ls_numrecdoc." a la cuante ".$ls_cuentascg." con operacion ".$ls_debhab." y un monto de ".$ldec_monto;
					   $lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
														$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
														$aa_seguridad["ventanas"],$ls_descripcion);
					   /////////////////////////////////         SEGURIDAD               /////////////////////////////
					 }
			    }		
		   }
		if (array_key_exists("la_crenotas",$_SESSION))
		   {
		     $la_cargos    = $_SESSION["la_crenotas"];
			 $li_totcargos = count((array)$la_cargos["codcar"]);
			 for ($li_i=1;$li_i<=$li_totcargos;$li_i++)
			     {
				   if ($lb_existe && $li_i==1)
				      {
					    $ls_sql = "DELETE FROM cxp_dc_cargos". 
								  " WHERE codemp='".$ls_codemp."'".
								  "   AND numsol='".$ls_numord."'".
								  "   AND numrecdoc='".$ls_numrecdoc."'".
								  "   AND codtipdoc='".$ls_tipdoc."'".
								  "   AND ced_bene='".$ls_cedbene."'".
								  "   AND cod_pro='".$ls_codpro."'". 
								  "   AND codope='".$ls_tiponota."'".
								  "   AND numdc='".$ls_numncnd."'";
					    $li_rows=$this->io_sql->execute($ls_sql);
					    if ($li_rows===false)
						   {
							 $this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_guardar_detalle7 ERROR-> ".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
						     $lb_valido=false;
						   }
					    else
						   {
						     $lb_valido=true;
						   }	
				      }			
				   if ($lb_valido)
				      {
						$ls_codcar     = $la_cargos["codcar"][$li_i];
						$ls_codestpro  = $la_cargos["codestpro"][$li_i];
						$ls_codestpro1 = substr($ls_codestpro,0,25);
						$ls_codestpro2 = substr($ls_codestpro,25,25);
						$ls_codestpro3 = substr($ls_codestpro,50,25);
						$ls_codestpro4 = substr($ls_codestpro,75,25);
						$ls_codestpro5 = substr($ls_codestpro,100,25);
						$ls_estcla     = $la_cargos["estcla"][$li_i];
						$ls_spgcuenta  = trim($la_cargos["spg_cuenta"][$li_i]);
						$ls_porcar     = $la_cargos["porcar"][$li_i];
						$ls_formula    = $la_cargos["formula"][$li_i];
						$ls_monobjret  = $la_cargos["monobjret"][$li_i];
						$ls_monret     = $la_cargos["monret"][$li_i];
						$ls_monobjret  = str_replace(".","",$ls_monobjret);
						$ls_monobjret  = str_replace(",",".",$ls_monobjret);
						$ls_monret     = str_replace(".","",$ls_monret);
						$ls_monret     = str_replace(",",".",$ls_monret);
						$ls_codfuefin  = $la_cargos["codfuefin"][$li_i];
						$ls_sql = "INSERT INTO cxp_dc_cargos(codemp, numsol, numrecdoc, cod_pro, ced_bene, codtipdoc, codope, numdc,".
								  "	 					   codcar, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla,".
								  "                          spg_cuenta, porcar, formula, monobjret, monret,codfuefin)".
								  " VALUES('".$ls_codemp."','".$ls_numord."','".$ls_numrecdoc."','".$ls_codpro."','".$ls_cedbene."',".
								  "        '".$ls_tipdoc."','".$ls_tiponota."','".$ls_numncnd."','".$ls_codcar."','".$ls_codestpro1."',".
								  "        '".$ls_codestpro2."','".$ls_codestpro3."','".$ls_codestpro4."','".$ls_codestpro5."',".
								  "        '".$ls_estcla."','".$ls_spgcuenta."','".$ls_porcar."','".$ls_formula."',".$ls_monobjret.",".
								  "        ".$ls_monret.",'".$ls_codfuefin."')";
						$li_rows=$this->io_sql->execute($ls_sql);
						if ($li_rows===false)
						   {
							 $this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_guardar_detalle (Cargos) ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
							 return false;
						   }
						else
						   {
							 $lb_valido=true;
							 /////////////////////////////////         SEGURIDAD               /////////////////////////////		
							 $ls_evento="INSERT";
							 $ls_descripcion ="Registro el detalle de cargos de la Nota ".$ls_numncnd." de tipo ".$ls_tiponota." Asociado a la solicitud ".$ls_numord." y a la recepcion ".$ls_numrecdoc." a la estructura ".$ls_codestpro." y cuenta ".$ls_cuentaspg;
							 $lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
						 									$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
															$aa_seguridad["ventanas"],$ls_descripcion);
							 /////////////////////////////////         SEGURIDAD               /////////////////////////////
						   }		
				      }
			     }
		   }
		return $lb_valido;
	}
	
	function uf_delete_nota($aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_nota
		//		   Access: public (sigesp_cxp_p_ncnd.php)
		//	  Description: Funcion que elimina por completo la nota de debito 0 credito
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creacion: 02/06/2007								Fecha Ultima Modificacion : 03/06/2007	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql->begin_transaction();
		$lb_valido=true;
		$ls_codemp    = $_SESSION["la_empresa"]["codemp"];
		$ls_numncnd   = $this->io_fun_cxp->uf_obtenervalor("txtnumncnd","");
		$ls_numord    = $this->io_fun_cxp->uf_obtenervalor("txtnumord","");
	    $ls_tipproben = $this->io_fun_cxp->uf_obtenervalor("tipproben","");
		$ls_codproben = $this->io_fun_cxp->uf_obtenervalor("txtcodproben","");
		$ls_numrecdoc = $this->io_fun_cxp->uf_obtenervalor("txtnumrecdoc","");
		$ls_tipdoc    = $this->io_fun_cxp->uf_obtenervalor("txttipdoc","");
		$ld_fecha     = $this->io_fun_cxp->uf_obtenervalor("txtfecregsol","");
		$ls_tiponota  = $this->io_fun_cxp->uf_obtenervalor("tiponota","");
		if($ls_tipproben=='P')
		{
			$ls_codpro=$ls_codproben;
			$ls_cedbene='----------';
		}
		else
		{
			$ls_codpro='----------';
			$ls_cedbene=$ls_codproben;
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_dc_cargos". 
					" WHERE codemp='".$ls_codemp."'".
					"   AND numsol='".$ls_numord."'".
					"   AND numrecdoc='".$ls_numrecdoc."'".
					"   AND codtipdoc='".$ls_tipdoc."'".
					"   AND ced_bene='".$ls_cedbene."'".
					"   AND cod_pro='".$ls_codpro."'". 
					"   AND codope='".$ls_tiponota."'".
					"   AND numdc='".$ls_numncnd."'";
			$li_rows=$this->io_sql->execute($ls_sql);
			if($li_rows===false)
			{
				$this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_delete_nota ERROR-> ".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				$lb_valido=true;
			}	
		}
		if($lb_valido)	
		{
			$ls_sql="DELETE FROM cxp_dc_spg 
					 WHERE codemp='".$ls_codemp."'    AND numsol='".$ls_numord."' AND numrecdoc='".$ls_numrecdoc."'
					 AND   codtipdoc='".$ls_tipdoc."' AND ced_bene='".$ls_cedbene."' AND  cod_pro='".$ls_codpro."' 
					 AND   codope='".$ls_tiponota."'  AND numdc='".$ls_numncnd."'";
			$li_rows=$this->io_sql->execute($ls_sql);
			if($li_rows===false)
			{
				$this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_delete_nota ERROR-> ".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				$lb_valido=true;
			}
		}
		if($lb_valido)	
		{
			$ls_sql="DELETE FROM cxp_dc_spi 
					 WHERE codemp='".$ls_codemp."'    AND numsol='".$ls_numord."' AND numrecdoc='".$ls_numrecdoc."'
					 AND   codtipdoc='".$ls_tipdoc."' AND ced_bene='".$ls_cedbene."' AND  cod_pro='".$ls_codpro."' 
					 AND   codope='".$ls_tiponota."'  AND numdc='".$ls_numncnd."'";
			$li_rows=$this->io_sql->execute($ls_sql);
			if($li_rows===false)
			{
				$this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_delete_nota ERROR-> ".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				$lb_valido=true;
			}
		}
		if($lb_valido)	
		{
			$ls_sql="DELETE FROM cxp_dc_scg 
					 WHERE codemp='".$ls_codemp."'    AND numsol='".$ls_numord."' AND numrecdoc='".$ls_numrecdoc."'
					 AND   codtipdoc='".$ls_tipdoc."' AND ced_bene='".$ls_cedbene."' AND  cod_pro='".$ls_codpro."' 
					 AND   codope='".$ls_tiponota."'  AND numdc='".$ls_numncnd."'";
			$li_rows=$this->io_sql->execute($ls_sql);
			if($li_rows===false)
			{
				$this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_delete_nota ERROR-> ".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				$lb_valido=true;
			}	
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_sol_dc 
					 WHERE codemp='".$ls_codemp."'    AND numsol='".$ls_numord."' AND numrecdoc='".$ls_numrecdoc."'
					 AND   codtipdoc='".$ls_tipdoc."' AND ced_bene='".$ls_cedbene."' AND  cod_pro='".$ls_codpro."' 
					 AND   codope='".$ls_tiponota."'  AND numdc='".$ls_numncnd."'";
			$li_rows=$this->io_sql->execute($ls_sql);
			if($li_rows===false)
			{
				$this->io_mensajes->message("CLASE->sigesp_cxp_c_ncnd METODO->uf_delete_nota ERROR-> ".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				$lb_valido=true;
			}	
		}
		if($lb_valido)
		{	
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Elimino la Nota ".$ls_numncnd." de tipo ".$ls_tiponota." Asociado a la solicitud ".$ls_numord." y a la recepcion ".$ls_numrecdoc;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			$this->io_mensajes->message("La Nota fue Eliminada");
			$this->io_sql->commit();
			unset($_SESSION["la_crenotas"]);
		}
		else
		{
			$this->io_sql->rollback();
		}	
		return $lb_valido;
	}

    function uf_load_creditos_nota($as_codemp,$as_numncnd,$as_numrecdoc,$as_codtipdoc,$as_numsol,$as_codope,$as_tipproben,$as_codproben)
	{
	  require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	  $io_include=new sigesp_include();
	  $io_conexion=$io_include->uf_conectar();
	  require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
	  $io_sql=new class_sql($io_conexion);	
	  require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
	  $io_mensajes=new class_mensajes();		
	  require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	  $io_funciones=new class_funciones();
	  require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
	  $io_ds_cargos = new class_datastore();
	  
	  $ls_sqlaux = "";
	  $ld_montotcre = 0;
	  if ($as_tipproben=='P')
	     {
		   $ls_sqlaux = " AND cxp_dc_cargos.cod_pro='".$as_codproben."' AND cxp_dc_cargos.ced_bene='----------'";
		 }
	  elseif($as_tipproben=='B')
	     {
		   $ls_sqlaux = " AND trim(cxp_dc_cargos.ced_bene)='".trim($as_codproben)."' AND cxp_dc_cargos.cod_pro='----------'";
		 }
	  else
	     {
		   $ls_sqlaux = " AND cxp_dc_cargos.cod_pro='----------'"; 
		 }
	  $ls_sql = "SELECT cxp_dc_cargos.* 
	               FROM cxp_dc_cargos, cxp_rd, cxp_solicitudes, cxp_dt_solicitudes
				  WHERE cxp_dc_cargos.codemp='".$as_codemp."'
				    AND cxp_dc_cargos.numsol='".$as_numsol."'
					AND trim(cxp_dc_cargos.numrecdoc)='".trim($as_numrecdoc)."'
					AND cxp_dc_cargos.codtipdoc='".$as_codtipdoc."'
					AND cxp_dc_cargos.codope='".$as_codope."'
					AND trim(cxp_dc_cargos.numdc)='".trim($as_numncnd)."' $ls_sqlaux
					AND cxp_dc_cargos.codemp=cxp_solicitudes.codemp
					AND cxp_dc_cargos.numsol=cxp_solicitudes.numsol					
					AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
					AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol					
					AND cxp_dc_cargos.codemp=cxp_dt_solicitudes.codemp
					AND cxp_dc_cargos.codtipdoc=cxp_dt_solicitudes.codtipdoc
					AND cxp_dc_cargos.numrecdoc=cxp_dt_solicitudes.numrecdoc
					AND cxp_dc_cargos.cod_pro=cxp_dt_solicitudes.cod_pro
					AND cxp_dc_cargos.ced_bene=cxp_dt_solicitudes.ced_bene					
					AND cxp_rd.codemp=cxp_dt_solicitudes.codemp
					AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc
					AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc
					AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro
					AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene";//echo $ls_sql.'<br>';
	  $rs_data = $io_sql->select($ls_sql);
	  if ($rs_data===false)
		 {
		   $this->io_mensajes->message("CLASE->Recepcion M?TODO->uf_load_creditos_nota ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		   $rs_data=false;
		 }	 
	  else
	     {
		   $li_numrows = $io_sql->num_rows($rs_data);
		   if ($li_numrows>0)
		      {
			    while ($row=$io_sql->fetch_row($rs_data))
				      {
					    $ls_codcar = $row["codcar"];
						$ld_monret = $row["monret"];
						$ls_spgcta = trim($row["spg_cuenta"]);
						$ls_estcla = $row["estcla"];
						$ls_codfuefin = $row["codfuefin"];
						$ld_monret = $row["monret"];
						$ls_porcar = $row["porcar"];
						$ls_formula = $row["formula"];
						$ld_monobjret = $row["monobjret"];
						$ls_codestpro = $row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
						$io_ds_cargos->insertRow("codcar",$ls_codcar);
						$io_ds_cargos->insertRow("spg_cuenta",$ls_spgcta);
						$io_ds_cargos->insertRow("estcla",$ls_estcla);
						$io_ds_cargos->insertRow("codestpro",$ls_codestpro);
					    $io_ds_cargos->insertRow("monret",number_format($ld_monret,2,',','.'));
						$io_ds_cargos->insertRow("monobjret",number_format($ld_monobjret,2,',','.'));
						$io_ds_cargos->insertRow("porcar",$ls_porcar);
						$io_ds_cargos->insertRow("formula",$ls_formula);
						$io_ds_cargos->insertRow("codfuefin",$ls_codfuefin);
						$ld_montotcre += $ld_monret;
					  }
				$_SESSION["la_crenotas"] = $io_ds_cargos->data;
			  }	 
		 }
	  unset($io_include,$io_conexion,$io_sql,$io_mensajes,$io_funciones,$io_ds_cargos);
      return $ld_montotcre;
	}
	
	function uf_monto_solicitud($as_codemp,$as_numsol) {
		$ldec_montocancelado=0;
	
		$ls_sql = "SELECT monsol
					FROM cxp_solicitudes
					WHERE codemp='{$as_codemp}'
					AND numsol='{$as_numsol}'";
		$rs_data = $this->io_sql->select($ls_sql);
		if($rs_data===false) {
			$this->io_mensajes->message("Error en consulta,".$this->io_sql->message);
		}
		else {
			if(!$rs_data->EOF) {
				$ldec_montocancelado = $rs_data->fields['monsol'];
			}
		}
		unset($rs_data);
		return $ldec_montocancelado;
	}
	
	function uf_solcxp_montocancelado($ls_codemp,$ls_numsol) {
		$ldec_montocancelado=0;
		
		$ls_sql = "SELECT sum(monto) as monto
			     	FROM cxp_sol_banco 
			    	WHERE codemp='{$ls_codemp}'
				  	AND numsol='{$ls_numsol}'
				  	AND estmov<>'A' 
				  	AND estmov<>'O'";
		$rs_data = $this->io_sql->select($ls_sql);
		if($rs_data===false) {
			$this->io_mensajes->message("Error en consulta,".$this->io_sql->message);
		}
		else {
			if(!$rs_data->EOF) {
				$ldec_montocancelado = $rs_data->fields['monto'];
			}
		}
		unset($rs_data);
		return $ldec_montocancelado;
	}

	function uf_load_notas_asociadas($as_codemp,$as_numsol) {
		$ldec_montonotas=0;
		
		$ls_sql= "SELECT SUM(CASE cxp_sol_dc.codope WHEN 'NC' THEN (-1*cxp_sol_dc.monto) ".
		   "                                 			ELSE (cxp_sol_dc.monto) END) as total ".
		   "  FROM cxp_dt_solicitudes, cxp_sol_dc ".
		   " WHERE cxp_dt_solicitudes.codemp='".$as_codemp."' ".
		   "   AND cxp_dt_solicitudes.numsol='".$as_numsol."' ".
		   "   AND cxp_sol_dc.estnotadc= 'C' ".
		   "   AND cxp_dt_solicitudes.codemp = cxp_sol_dc.codemp ".
		   "   AND cxp_dt_solicitudes.numsol = cxp_sol_dc.numsol ".
		   "   AND cxp_dt_solicitudes.numrecdoc = cxp_sol_dc.numrecdoc ".
		   "   AND cxp_dt_solicitudes.codtipdoc = cxp_sol_dc.codtipdoc ".
		   "   AND cxp_dt_solicitudes.ced_bene = cxp_sol_dc.ced_bene ".
		   "   AND cxp_dt_solicitudes.cod_pro = cxp_sol_dc.cod_pro";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false) {
			$this->io_mensajes->message("Error en consulta,".$this->io_sql->message);
		}
		else {
			if(!$rs_data->EOF) {
				$ldec_montonotas = $rs_data->fields['total'];
			}
		}
		unset($rs_data);
		return $ldec_montonotas;
	}
	
	function uf_select_presupuesto($as_codemp, $as_numsol, $as_numrecdoc, $as_codtipdoc, $as_ced_bene, $as_cod_pro, $as_codope, $as_numdc, $as_codestpro, $as_estcla, 
								   $as_codfuefin, $as_spg_cuenta) 
	{
		$lb_existe=false;
		
		$ls_sql= "SELECT * ".
		   "  FROM cxp_dc_spg ".
		   " WHERE cxp_dc_spg.codemp='".$as_codemp."' ".
		   "   AND cxp_dc_spg.numsol='".$as_numsol."' ".
		   "   AND cxp_dc_spg.numrecdoc='".$as_numrecdoc."' ".
		   "   AND cxp_dc_spg.codtipdoc='".$as_codtipdoc."' ".
		   "   AND cxp_dc_spg.ced_bene='".$as_ced_bene."' ".
		   "   AND cxp_dc_spg.cod_pro='".$as_cod_pro."' ".
		   "   AND cxp_dc_spg.codope='".$as_codope."' ".
		   "   AND cxp_dc_spg.numdc='".$as_numdc."' ".
		   "   AND cxp_dc_spg.codestpro='".$as_codestpro."' ".
		   "   AND cxp_dc_spg.estcla='".$as_estcla."' ".
		   "   AND cxp_dc_spg.codfuefin='".$as_codfuefin."' ".
		   "   AND cxp_dc_spg.spg_cuenta='".$as_spg_cuenta."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false) {
			$this->io_mensajes->message("Error en consulta,".$this->io_sql->message);
		}
		else {
			if(!$rs_data->EOF) {
				$lb_existe = true;
			}
		}
		unset($rs_data);
		return $lb_existe;
	}
	

	function uf_procesar_solicitud($as_codemp,$as_numsol,$as_monnot) {
		$monsol    = $this->uf_monto_solicitud($as_codemp, $as_numsol);
		$moncan    = $this->uf_solcxp_montocancelado($as_codemp, $as_numsol);
		$monnotant = $this->uf_load_notas_asociadas($as_codemp, $as_numsol);
		$monresta  = ($monsol - $moncan) + $monnotant;
		
		if ($monresta == $as_monnot) {
			$ls_sql = "UPDATE scb_prog_pago
					      SET estmov = 'C'
					    WHERE codemp='".$as_codemp."'
						  AND numsol='".$as_numsol."'";
			$rs_data = $this->io_sql->execute($ls_sql);
			if ($rs_data===false) {
				$this->io_mensajes->message("Error en consulta,".$this->io_sql->message);
			}
		 	else {
		 		unset($rs_data);
			    $ls_sql = "UPDATE cxp_solicitudes
					          SET estprosol = 'P'
					        WHERE codemp='".$as_codemp."'
						      AND numsol='".$as_numsol."'";
				$rs_data = $this->io_sql->execute($ls_sql);
			    if ($rs_data===false) {
			    	$this->io_mensajes->message("Error en consulta,".$this->io_sql->message);
			    }
		 	}				
		} 
	}
}
?>