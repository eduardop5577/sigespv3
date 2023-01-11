<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_sno_c_evaluador
{
	var	$io_sql;
	var	$io_mensajes;
	var	$io_funciones;
	var	$io_sno;
	var	$io_eval;
	var	$io_familiar;
	var	$io_isr;
	var	$io_concepto;
	var	$io_constante;
	var	$io_primaconcepto;
	var $io_feriado;
	var $io_permiso;
	var $io_cestaticket;
	var	$ls_codemp;
	var $ls_codnom;
	var $io_fecha;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno_c_evaluador
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
                //$io_conexion->debug=true;
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once("sigesp_sno.php");
		$this->io_sno=new sigesp_sno();
		require_once("../shared/class_folder/evaluate_formula.php");
		$this->io_eval=new evaluate_formula();
		require_once("sigesp_snorh_c_familiar.php");
		$this->io_familiar=new sigesp_snorh_c_familiar();
		require_once("sigesp_snorh_c_isr.php");
		$this->io_isr=new sigesp_snorh_c_isr();
		require_once("sigesp_sno_c_concepto.php");
		$this->io_concepto=new sigesp_sno_c_concepto();
		require_once("sigesp_sno_c_constantes.php");
		$this->io_constante=new sigesp_sno_c_constantes();
		require_once("sigesp_sno_c_primaconcepto.php");		
		$this->io_primaconcepto=new sigesp_sno_c_primaconcepto();
		require_once("sigesp_snorh_c_diaferiado.php");		
		$this->io_feriado=new sigesp_snorh_c_diaferiado();
		require_once("sigesp_snorh_c_permiso.php");		
		$this->io_permiso=new sigesp_snorh_c_permiso();
		require_once("sigesp_snorh_c_ct_met.php");		
		$this->io_cestaticket=new sigesp_snorh_c_ct_met();
		require_once("../srh/class_folder/dao/sigesp_srh_c_tipodeduccion.php");
		$this->io_tipodeduccion=new sigesp_srh_c_tipodeduccion("../");
		require_once("sigesp_sno_c_registrarencargaduria.php");		
		$this->io_encargaduria=new sigesp_sno_c_registrarencargaduria();		
		require_once("sigesp_snorh_c_nominas.php");		
		$this->io_nomina=new sigesp_snorh_c_nominas();			
		require_once("sigesp_snorh_c_fideicomiso.php");		
		$this->io_fideicomiso=new sigesp_snorh_c_fideicomiso();			
		require_once("sigesp_snorh_c_beneficiario.php");
		$this->io_beneficiario=new sigesp_snorh_c_beneficiario();			
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		$this->io_fecha=new class_fecha();	
		require_once("sigesp_sno_c_hojatiempo.php");
		$this->io_hojatiempo=new sigesp_sno_c_hojatiempo();			
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->ls_codnom="0000";
		if(array_key_exists("la_nomina",$_SESSION))
		{
			if(array_key_exists("codnom",$_SESSION["la_nomina"]))
			{
				$this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
			}
		}
		$_SESSION["la_nomina"]["calculandosueldointegral"]=false;
		$_SESSION["la_nomina"]["calculandosueldonormal"]=false;
		require_once("class_folder/class_personal.php");
		$this->personal=new class_personal();
		$this->conceptospersona =Array();			
	}// end function sigesp_sno_c_evaluador
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_config_session($as_codper)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_config_session
		//		   Access: public 
		//	    Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se crearon las variable sesion ó False si no se crearon
		//	  Description: función que dado el código de personal y el código del concetpo crea las variables session necesarias
		//				   para el calculo del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($lb_valido)
		{
			$lb_valido=$this->uf_crear_constantepersonal($as_codper);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_crear_tablasueldo($as_codper);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_crear_personalnomina($as_codper);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_crear_vacacionpersonal($as_codper);
		}
                
		return $lb_valido;
	}// end function uf_config_session
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_crear_personalnomina($as_codper)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_crear_personalnomina
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal crea una variable session con todos los datos
		//				   de personal nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codper, sueper, sueproper, horper, staper, fecculcontr, nivacaper, fecingper, cedper, nomper, apeper, sexper, ".
				"  		numhijper, anoservpreper, fecnacper, fecingadmpubper, codtabvac, cajahoper, porcajahoper,  suebasper, priespper, ".
				"		pritraper, priproper, prianoserper, pridesper, porpenper, prinoascper, monpenper, subtotper, messervpreper, diasserprevper,".
				"		codded, codtipper, codcladoc, codescdoc, fecingnom,  capantcom,   tipjub,   suemingra, fecegrper, codcar, codasicar, fecjubper  ".
				"  FROM calculo_personal ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codper='".$as_codper."' ".
				" ORDER BY codemp, codnom, codper";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_crear_personalnomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$this->conceptospersona = Array();
				$this->personal=new class_personal();			
				$this->personal->codper=$rs_data->fields["codper"];
				$this->personal->sueper=number_format((float)$rs_data->fields["sueper"],2,".","");
				$this->personal->sueproper=number_format((float)$rs_data->fields["sueproper"],2,".","");
				$this->personal->horper=$rs_data->fields["horper"];
				$this->personal->staper=$rs_data->fields["staper"];
				$this->personal->nivacaper=$rs_data->fields["nivacaper"];
				$this->personal->cedper=$rs_data->fields["cedper"];
				$this->personal->nomper=$rs_data->fields["nomper"];
				$this->personal->apeper=$rs_data->fields["apeper"];
				$this->personal->sexper=$rs_data->fields["sexper"];
				$this->personal->numhijper=$rs_data->fields["numhijper"];
				$this->personal->anoservpreper=$rs_data->fields["anoservpreper"];
				$this->personal->messervpreper=$rs_data->fields["messervpreper"];
                                $this->personal->diasserprevper=$rs_data->fields["diasserprevper"];
				$this->personal->codtabvac=$rs_data->fields["codtabvac"];
				$this->personal->cajahoper=$rs_data->fields["cajahoper"];
				$this->personal->porcajahoper=$rs_data->fields["porcajahoper"];	
				$this->personal->suebasper=number_format((float)$rs_data->fields["suebasper"],2,".","");
				$this->personal->priespper=number_format((float)$rs_data->fields["priespper"],2,".","");
				$this->personal->pritraper=number_format((float)$rs_data->fields["pritraper"],2,".","");
				$this->personal->priproper=number_format((float)$rs_data->fields["priproper"],2,".","");
				$this->personal->prianoserper=number_format((float)$rs_data->fields["prianoserper"],2,".","");
				$this->personal->pridesper=number_format((float)$rs_data->fields["pridesper"],2,".","");
				$this->personal->porpenper=$rs_data->fields["porpenper"];
				$this->personal->prinoascper=number_format((float)$rs_data->fields["prinoascper"],2,".","");
				$this->personal->monpenper=number_format((float)$rs_data->fields["monpenper"],2,".","");
				$this->personal->subtotper=number_format((float)$rs_data->fields["subtotper"],2,".","");
				$this->personal->codded="'".$rs_data->fields["codded"]."'";
				$this->personal->codtipper="'".$rs_data->fields["codtipper"]."'";
				$this->personal->codcladoc="'".$rs_data->fields["codcladoc"]."'";
				$this->personal->codescdoc="'".$rs_data->fields["codescdoc"]."'";
				$this->personal->capantcom=$rs_data->fields["capantcom"];
				$this->personal->tipjub=$rs_data->fields["tipjub"];
				$this->personal->codcar=$rs_data->fields["codcar"];
				$this->personal->codasicar=$rs_data->fields["codasicar"];
				$this->personal->suemingra=number_format((float)$rs_data->fields["suemingra"],2,".","");
				$this->personal->fecculcontr=$this->io_funciones->uf_formatovalidofecha($rs_data->fields["fecculcontr"]);
				$this->personal->fecingper=$this->io_funciones->uf_formatovalidofecha($rs_data->fields["fecingper"]);
				$this->personal->fecegrper=$this->io_funciones->uf_formatovalidofecha($rs_data->fields["fecegrper"]);
				$this->personal->fecnacper=$this->io_funciones->uf_formatovalidofecha($rs_data->fields["fecnacper"]);
				$this->personal->fecingnom=$this->io_funciones->uf_formatovalidofecha($rs_data->fields["fecingnom"]);
				$this->personal->fecjubper=$this->io_funciones->uf_formatovalidofecha($rs_data->fields["fecjubper"]);
				$this->personal->fecingadmpubper=$this->io_funciones->uf_formatovalidofecha($rs_data->fields["fecingadmpubper"]);
				$this->personal->mettabvac=$this->io_sno->uf_select_config("SNO","CONFIG","METODO_VACACIONES","0","C");
				$this->personal->salarionormal=0;
				$this->personal->sueldointegral=0;
				
				$ai_sueldointegral=0;
				$ai_salarionormal=0;
				$ai_totalarc=0;
				$arrResultado=$this->uf_obtener_salarionormal($as_codper,$ai_salarionormal);
				$ai_salarionormal=$arrResultado['ai_salarionormal'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$this->personal->salarionormal=number_format((float)$ai_salarionormal,2,".","");
				}
				$arrResultado=$this->uf_obtener_sueldointegral($as_codper,$ai_sueldointegral);
				$ai_sueldointegral=$arrResultado['ai_sueldointegral'];
				$lb_valido=$arrResultado['lb_valido'];

				if($lb_valido)
				{
					$this->personal->sueldointegral=number_format((float)$ai_sueldointegral,2,".","");
				}
				$arrResultado=$this->uf_obtener_montoarc($as_codper,$ai_totalarc);
				$ai_totalarc=$arrResultado['ai_totalarc'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$this->personal->totalarc=number_format((float)$ai_totalarc,2,".","");
				}
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_crear_personalnomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_sueldointegral($as_codper,$ai_sueldointegral)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_sueldointegral
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal obtiene la suma de todos los 
		//				   conceptos que pertenecen al sueldo integral
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueldointegral=0;
		$ls_sql="SELECT codemp, codnom, codper, codconc, nomcon, titcon, sigcon, forcon, glocon, acumaxcon, valmincon, valmaxcon, concon, cueprecon, cueconcon, ".
				"		aplisrcon, sueintcon, intprocon, forpatcon, cueprepatcon, cueconpatcon, titretempcon, titretpatcon, ".
				"		valminpatcon, valmaxpatcon, codprov, cedben, conprenom, sueintvaccon, aplarccon, aplcon, valcon, acuemp, ".
				"  		acuiniemp, acupat, acuinipat, quirepcon ".
				"  FROM calculo_conceptospersonal ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codper='".$as_codper."' ".
				"   AND (sigcon='A' OR sigcon='R')".
				"   AND sueintcon=1".
				" ORDER BY codemp, codnom, codconc, codper";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_sueldointegral ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$_SESSION["la_nomina"]["calculandosueldointegral"]=true;
			while ((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codconc=$rs_data->fields["codconc"];
				$_SESSION["la_conceptopersonal"]["codconc"]=$ls_codconc;
                                if (!array_key_exists($ls_codconc,$this->conceptospersona))
				//if((is_null($this->conceptospersona[$ls_codconc])))
				{
					$this->conceptospersona[$ls_codconc]=0;
				}
				$li_sueldo=0;
				$ls_concon=$rs_data->fields["concon"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_glocon=$rs_data->fields["glocon"];
				$li_aplcon=$rs_data->fields["aplcon"];
				$ls_forcon=$rs_data->fields["forcon"];
				$ls_sigcon=$rs_data->fields["sigcon"];
				$ls_forpatcon=$rs_data->fields["forpatcon"];
				$ls_acuemp=$rs_data->fields["acuemp"];
				$ls_acupat=$rs_data->fields["acupat"];
				$ls_quirepcon=$rs_data->fields["quirepcon"];
				$li_valmincon=$rs_data->fields["valmincon"];
				$li_valmaxcon=$rs_data->fields["valmaxcon"];
				if($li_glocon==1)// si el concepto es global
				{
					if(!(trim($ls_concon)==""))// si tiene condición
					{
						$lb_filtro=false;
						$arrResultado=$this->uf_evaluar($as_codper,$ls_concon,$lb_filtro);
						$lb_filtro=$arrResultado['as_valor'];
						$lb_valido=$arrResultado['lb_valido'];
						if(($lb_filtro)&&($lb_valido)) // si la condición es válida
						{
							$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);
							$li_sueldo=$arrResultado['as_valor'];
							$lb_valido=$arrResultado['lb_valido'];
						}
					}
					else
					{
						$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);					
						$li_sueldo=$arrResultado['as_valor'];
						$lb_valido=$arrResultado['lb_valido'];
					}
				}
				else
				{
					if($li_aplcon==1)// si se aplica el concepto
					{
						if(!(trim($ls_concon)==""))// si tiene condición
						{
							$lb_filtro=false;
							$arrResultado=$this->uf_evaluar($as_codper,$ls_concon,$lb_filtro);
							$lb_filtro=$arrResultado['as_valor'];
							$lb_valido=$arrResultado['lb_valido'];
							if(($lb_filtro)&&($lb_valido)) // si la condición es válida
							{
								$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);
								$li_sueldo=$arrResultado['as_valor'];
								$lb_valido=$arrResultado['lb_valido'];
							}
						}
						else
						{
							$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);					
							$li_sueldo=$arrResultado['as_valor'];
							$lb_valido=$arrResultado['lb_valido'];
						}
					}
				}
				if($li_valmincon>0)
				{
					if($li_sueldo<$li_valmincon)
					{
						$li_sueldo=$li_valmincon;
					}
				}
				if($li_valmaxcon>0)
				{
					if($li_sueldo>$li_valmaxcon)
					{
						$li_sueldo=$li_valmaxcon;
					}
				}
				$this->conceptospersona[$ls_codconc]=$li_sueldo;
				$ai_sueldointegral=$ai_sueldointegral+$li_sueldo;
				unset($_SESSION["la_conceptopersonal"]);
				$rs_data->MoveNext();
			}
			$_SESSION["la_nomina"]["calculandosueldointegral"]=false;
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_sueldointegral']=$ai_sueldointegral;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_obtener_sueldointegral
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_montoarc($as_codper,$ai_totalarc)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_montoarc
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal obtiene la suma de todos los 
		//				   conceptos que pertenecen al arc
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 18/09/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_totalarc=0;
		$ls_sql="SELECT codemp, codnom, codper, codconc, nomcon, titcon, sigcon, forcon, glocon, acumaxcon, valmincon, valmaxcon, concon, cueprecon, cueconcon, ".
				"		aplisrcon, sueintcon, intprocon, forpatcon, cueprepatcon, cueconpatcon, titretempcon, titretpatcon, ".
				"		valminpatcon, valmaxpatcon, codprov, cedben, conprenom, sueintvaccon, aplarccon, aplcon, valcon, acuemp, ".
				"  		acuiniemp, acupat, acuinipat, quirepcon ".
				"  FROM calculo_conceptospersonal ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codper='".$as_codper."' ".
				"   AND sigcon='A'".
				"   AND aplarccon=1".
				" ORDER BY codemp, codnom, codconc, codper";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_montoarc ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$_SESSION["la_nomina"]["calculandomontoarc"]=true;
			while ((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codconc=$rs_data->fields["codconc"];
				$_SESSION["la_conceptopersonal"]["codconc"]=$ls_codconc;
                                if (!array_key_exists($ls_codconc,$this->conceptospersona))
				//if((is_null($this->conceptospersona[$ls_codconc])))
				{
					$this->conceptospersona[$ls_codconc]=0;
				}
				$li_sueldo=0;
				$ls_concon=$rs_data->fields["concon"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_glocon=$rs_data->fields["glocon"];
				$li_aplcon=$rs_data->fields["aplcon"];
				$ls_forcon=$rs_data->fields["forcon"];
				$ls_sigcon=$rs_data->fields["sigcon"];
				$ls_forpatcon=$rs_data->fields["forpatcon"];
				$ls_acuemp=$rs_data->fields["acuemp"];
				$ls_acupat=$rs_data->fields["acupat"];
				$ls_quirepcon=$rs_data->fields["quirepcon"];
				$li_valmincon=$rs_data->fields["valmincon"];
				$li_valmaxcon=$rs_data->fields["valmaxcon"];
				
				if($li_glocon==1)// si el concepto es global
				{
					if(!(trim($ls_concon)==""))// si tiene condición
					{
						$lb_filtro=false;
						$arrResultado=$this->uf_evaluar($as_codper,$ls_concon,$lb_filtro);
						$lb_filtro=$arrResultado['as_valor'];
						$lb_valido=$arrResultado['lb_valido'];
						if(($lb_filtro)&&($lb_valido)) // si la condición es válida
						{
							$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);
							$li_sueldo=$arrResultado['as_valor'];
							$lb_valido=$arrResultado['lb_valido'];
						}
					}
					else
					{
						$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);					
						$li_sueldo=$arrResultado['as_valor'];
						$lb_valido=$arrResultado['lb_valido'];
					}
				}
				else
				{
					if($li_aplcon==1)// si se aplica el concepto
					{
						if(!(trim($ls_concon)==""))// si tiene condición
						{
							$lb_filtro=false;
							$arrResultado=$this->uf_evaluar($as_codper,$ls_concon,$lb_filtro);
							$lb_filtro=$arrResultado['as_valor'];
							$lb_valido=$arrResultado['lb_valido'];
							if(($lb_filtro)&&($lb_valido)) // si la condición es válida
							{
								$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);
								$li_sueldo=$arrResultado['as_valor'];
								$lb_valido=$arrResultado['lb_valido'];
							}
						}
						else
						{
							$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);					
							$li_sueldo=$arrResultado['as_valor'];
							$lb_valido=$arrResultado['lb_valido'];
						}
					}
				}
				if($li_valmincon>0)
				{
					if($li_sueldo<$li_valmincon)
					{
						$li_sueldo=$li_valmincon;
					}
				}
				if($li_valmaxcon>0)
				{
					if($li_sueldo>$li_valmaxcon)
					{
						$li_sueldo=$li_valmaxcon;
					}
				}
				$ai_totalarc=$ai_totalarc+$li_sueldo;
				$this->conceptospersona[$ls_codconc]=$li_sueldo;
				unset($_SESSION["la_conceptopersonal"]);
				$rs_data->MoveNext();
			}
			$_SESSION["la_nomina"]["calculandomontoarc"]=false;
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_totalarc']=$ai_totalarc;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_obtener_montoarc
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_salarionormal($as_codper,$ai_salarionormal)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_salarionormal
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal obtiene la suma de todos los 
		//				   conceptos que pertenecen al salario normal
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 18/11/2008					Fecha Última Modificación : 		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_salarionormal=0;
		$ls_sql="SELECT codemp, codnom, codper, codconc, nomcon, titcon, sigcon, forcon, glocon, acumaxcon, valmincon, valmaxcon, concon, cueprecon, cueconcon, ".
				"		aplisrcon, sueintcon, intprocon, forpatcon, cueprepatcon, cueconpatcon, titretempcon, titretpatcon, ".
				"		valminpatcon, valmaxpatcon, codprov, cedben, conprenom, sueintvaccon, aplarccon, aplcon, valcon, acuemp, ".
				"  		acuiniemp, acupat, acuinipat, quirepcon ".
				"  FROM calculo_conceptospersonal ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codper='".$as_codper."' ".
				"   AND (sigcon='A' OR sigcon='R')".
				"   AND persalnor='1'".
				" ORDER BY codemp, codnom, codconc, codper";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_salarionormal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$_SESSION["la_nomina"]["calculandosueldonormal"]=true;
			while ((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codconc=$rs_data->fields["codconc"];
				$_SESSION["la_conceptopersonal"]["codconc"]=$ls_codconc;
                                if (!array_key_exists($ls_codconc,$this->conceptospersona))
				//if((is_null($this->conceptospersona[$ls_codconc])))
				{
					$this->conceptospersona[$ls_codconc]=0;
				}
				$li_sueldo=0;
				$ls_concon=$rs_data->fields["concon"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_glocon=$rs_data->fields["glocon"];
				$li_aplcon=$rs_data->fields["aplcon"];
				$ls_forcon=$rs_data->fields["forcon"];
				$ls_sigcon=$rs_data->fields["sigcon"];
				$ls_forpatcon=$rs_data->fields["forpatcon"];
				$ls_acuemp=$rs_data->fields["acuemp"];
				$ls_acupat=$rs_data->fields["acupat"];
				$ls_quirepcon=$rs_data->fields["quirepcon"];
				$li_valmincon=$rs_data->fields["valmincon"];
				$li_valmaxcon=$rs_data->fields["valmaxcon"];
				if($li_glocon==1)// si el concepto es global
				{
					if(!(trim($ls_concon)==""))// si tiene condición
					{
						$lb_filtro=false;
						$arrResultado=$this->uf_evaluar($as_codper,$ls_concon,$lb_filtro);
						$lb_filtro=$arrResultado['as_valor'];
						$lb_valido=$arrResultado['lb_valido'];
						if(($lb_filtro)&&($lb_valido)) // si la condición es válida
						{
							$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);
							$li_sueldo=$arrResultado['as_valor'];
							$lb_valido=$arrResultado['lb_valido'];
						}
					}
					else
					{
						$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);					
						$li_sueldo=$arrResultado['as_valor'];
						$lb_valido=$arrResultado['lb_valido'];
					}
				}
				else
				{
					if($li_aplcon==1)// si se aplica el concepto
					{
						if(!(trim($ls_concon)==""))// si tiene condición
						{
							$lb_filtro=false;
							$arrResultado=$this->uf_evaluar($as_codper,$ls_concon,$lb_filtro);
							$lb_filtro=$arrResultado['as_valor'];
							$lb_valido=$arrResultado['lb_valido'];
							if(($lb_filtro)&&($lb_valido)) // si la condición es válida
							{
								$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);
								$li_sueldo=$arrResultado['as_valor'];
								$lb_valido=$arrResultado['lb_valido'];
							}
						}
						else
						{
							$arrResultado=$this->uf_evaluar($as_codper,$ls_forcon,$li_sueldo);					
							$li_sueldo=$arrResultado['as_valor'];
							$lb_valido=$arrResultado['lb_valido'];
						}
					}
				}
				if($li_valmincon>0)
				{
					if($li_sueldo<$li_valmincon)
					{
						$li_sueldo=$li_valmincon;
					}
				}
				if($li_valmaxcon>0)
				{
					if($li_sueldo>$li_valmaxcon)
					{
						$li_sueldo=$li_valmaxcon;
					}
				}
				$this->conceptospersona[$ls_codconc]=$li_sueldo;
				$ai_salarionormal=$ai_salarionormal+$li_sueldo;
				unset($_SESSION["la_conceptopersonal"]);
				$rs_data->MoveNext();
			}
			$_SESSION["la_nomina"]["calculandosueldonormal"]=false;
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_salarionormal']=$ai_salarionormal;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_obtener_salarionormal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_crear_vacacionpersonal($as_codper)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_crear_vacacionpersonal
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal crea una variable session con todos los datos
		//				   de vacación personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_metodovacaciones=trim($this->personal->mettabvac);
		switch ($ls_metodovacaciones)
		{
			case "1": //METODO #0
				$ld_desde_s=$this->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
				$ld_desde_s=$this->io_sno->uf_suma_fechas($ld_desde_s,1);
				$ld_desde_s=$this->io_funciones->uf_convertirdatetobd($ld_desde_s);	
				switch($_SESSION["la_nomina"]["tippernom"])
				{
					case "0": // Nóminas Semanales
						$li_dias=7;
						break;
					case "1": // Nóminas Quincenales
						$li_dias=15;
						break;
					case "2": // Nóminas Mensuales
						$li_dias=30;
						break;
					case "3": // Nóminas Anuales
						$li_dias=365;
						break;
				}
				
				$ld_hasta_s=$this->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
				$ld_hasta_s=$this->io_sno->uf_suma_fechas($ld_hasta_s,$li_dias);
				$ls_dia=substr($ld_hasta_s,0,2);
				$ls_mes=substr($ld_hasta_s,3,2);
				$ls_ano=substr($ld_hasta_s,6,4);
				while(checkdate($ls_mes,$ls_dia,$ls_ano)==false)
				{ 
				   	$ls_dia=$ls_dia-1;
				} 
				$ld_hasta_s=$ls_dia."/".$ls_mes."/".$ls_ano;
				$ld_hasta_s=$this->io_funciones->uf_convertirdatetobd($ld_hasta_s);
				$ld_desde_r=$_SESSION["la_nomina"]["fecdesper"];
				$ld_hasta_r=$_SESSION["la_nomina"]["fechasper"];				
				$ls_sql="SELECT codvac, sueintbonvac, sueintvac, fecdisvac, fecreivac, diavac, diabonvac, diaadibon, diaadivac, ".
						"		diafer, sabdom, quisalvac, quireivac, profueper, candiaadi ".
						"  FROM sno_vacacpersonal ".
						" WHERE codemp='".$this->ls_codemp."'".
						"   AND codper='".$as_codper."' ".
						"   AND ((stavac='2' AND (fecdisvac between '".$ld_desde_s."' AND '".$ld_hasta_s."'))".
						"    OR ( stavac='3' AND (fecreivac between '".$ld_desde_s."' AND '".$ld_hasta_s."')))".
						"   AND pagpersal='0' ".
						"UNION ".
						"SELECT codvac, sueintbonvac, sueintvac, fecdisvac, fecreivac, diavac, diabonvac, diaadibon, diaadivac, ".
						"		diafer, sabdom, quisalvac, quireivac, profueper, candiaadi ".
						"  FROM sno_vacacpersonal ".
						" WHERE codemp='".$this->ls_codemp."'".
						"   AND codper='".$as_codper."' ".
						"   AND ((stavac='2' AND (fecdisvac between '".$ld_desde_r."' AND '".$ld_hasta_r."'))".
						"    OR ( stavac='3' AND (fecreivac between '".$ld_desde_r."' AND '".$ld_hasta_r."')))".
						"   AND pagpersal='1'".
						"UNION ".
						"SELECT codvac, sueintbonvac, sueintvac, fecdisvac, fecreivac, diavac, diabonvac, diaadibon, diaadivac, ".
						"		diafer, sabdom, quisalvac, quireivac, profueper, candiaadi ".
						"  FROM sno_vacacpersonal ".
						" WHERE codemp='".$this->ls_codemp."'".
						"   AND codper='".$as_codper."' ".
						"	AND stavac<>'1' ".
						"   AND pagcan=0 ".
						"	AND fecdisvac < '".$ld_desde_r."' ";
				break;
		}
		if($ls_metodovacaciones!="0")
		{
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data==false)
			{
				$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_crear_vacacionpersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$la_vacacionpersonal=$row;   
					$_SESSION["la_vacacionpersonal"]=$la_vacacionpersonal;
					$ld_fecdisvac=$_SESSION["la_vacacionpersonal"]["fecdisvac"];
					$ld_fecreivac=$_SESSION["la_vacacionpersonal"]["fecreivac"];
					$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
					$_SESSION["la_vacacionpersonal"]["nrolunes_s"]=$this->io_sno->uf_nro_lunes($ld_fecdisvac,$ld_fecreivac);
					$_SESSION["la_vacacionpersonal"]["nrolunes_r"]=$this->io_sno->uf_nro_lunes($ld_fecreivac,$ld_fechasper);
					$_SESSION["la_vacacionpersonal"]["primera_quincena"]=false;
					$_SESSION["la_vacacionpersonal"]["segunda_quincena"]=false;
				}
				else
				{
					$_SESSION["la_vacacionpersonal"]["codvac"]=0;
					$_SESSION["la_vacacionpersonal"]["sueintbonvac"]=0;
					$_SESSION["la_vacacionpersonal"]["sueintvac"]=0;
					$_SESSION["la_vacacionpersonal"]["fecdisvac"]="1900-01-01";
					$_SESSION["la_vacacionpersonal"]["fecreivac"]="1900-01-01";
					$_SESSION["la_vacacionpersonal"]["diavac"]=0;
					$_SESSION["la_vacacionpersonal"]["diabonvac"]=0;
					$_SESSION["la_vacacionpersonal"]["diaadibon"]=0;
					$_SESSION["la_vacacionpersonal"]["diaadivac"]=0;
					$_SESSION["la_vacacionpersonal"]["diafer"]=0;
					$_SESSION["la_vacacionpersonal"]["sabdom"]=0;
					$_SESSION["la_vacacionpersonal"]["quisalvac"]=0;
					$_SESSION["la_vacacionpersonal"]["quireivac"]=0;
					$_SESSION["la_vacacionpersonal"]["nrolunes_s"]=0;
					$_SESSION["la_vacacionpersonal"]["nrolunes_r"]=0;
					$_SESSION["la_vacacionpersonal"]["candiaadi"]=0;
					$_SESSION["la_vacacionpersonal"]["primera_quincena"]=false;
					$_SESSION["la_vacacionpersonal"]["segunda_quincena"]=false;
				}
			}
			$this->io_sql->free_result($rs_data);
		}	
		return $lb_valido;
	}// end function uf_crear_vacacionpersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_crear_tablasueldo($as_codper)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_crear_tablasueldo
		//		   Access: private
		//   	Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal crea una variable session con todos los datos
		//				   de sueldo que tiene asociado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codtab, codgra, codpas, monsalgra, moncomgra, monto_primas ".
				"  FROM calculo_personaltabulador  ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codper='".$as_codper."' ".
				" ORDER BY codemp, codnom, codper";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_crear_tablasueldo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$la_tablasueldo=$row;   
				$_SESSION["la_tablasueldo"]=$la_tablasueldo;
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_crear_tablasueldo ERROR->Verifique el Tabulador ó grados asociados al personal ".$as_codper);
			}
			$this->io_sql->free_result($rs_data);	
		}		
		return $lb_valido;
	}// end function uf_crear_tablasueldo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_crear_constantepersonal($as_codper)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_crear_constantepersonal
		//		   Access: private
		//   	Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal crea una variable session con todos los datos
		//				   de sueldo que tiene asociado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT sno_constantepersonal.codcons, sno_constantepersonal.moncon ".
			"  FROM sno_constantepersonal, sno_constante ".
			" WHERE sno_constantepersonal.codemp='".$this->ls_codemp."' ".
			"   AND sno_constantepersonal.codnom='".$this->ls_codnom."' ".
			"   AND sno_constantepersonal.codper='".$as_codper."' ".
			"   AND sno_constantepersonal.codemp=sno_constante.codemp ".
			"   AND sno_constantepersonal.codnom=sno_constante.codnom ".
			"   AND sno_constantepersonal.codcons=sno_constante.codcons ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_crear_constantepersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
                            $codcons = $rs_data->fields["codcons"];
                            $moncon = $rs_data->fields["moncon"];
                            $_SESSION["constantepersonal"][$codcons]=$moncon;
                            $rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}	
		return $lb_valido;
	}// end function uf_crear_constantepersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

        //-----------------------------------------------------------------------------------------------------------------------------------
	function uf_evaluar($as_codper,$as_formula,$as_valor)
	{	
	  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_evaluar
		//		   Access: public
		//	    Arguments: as_codper // código de personal
		//				   as_formula // fórmula del concepto
		//				   as_valor // valor que se obtiene de la fórmula
		//	      Returns: lb_valido True si se evalua correctamente la fórmula ó False si hubo error 
		//	  Description: función que dado una formula de devuelve el valor que arroja
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_formula=trim($as_formula);
		$as_formula=strtoupper(trim($as_formula));
		if($lb_valido)
		{
			// Variables de Nómina
			$arrResultado=$this->uf_sustituir($as_codper,"FN[",$as_formula);
			$as_formula=$arrResultado['as_formula'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		if($lb_valido)
		{
			// Variables de Personal
			$arrResultado=$this->uf_sustituir($as_codper,"PS[",$as_formula);
			$as_formula=$arrResultado['as_formula'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		if($lb_valido)
		{
			// Variables de Tabla de Sueldo
			$arrResultado=$this->uf_sustituir($as_codper,"TB[",$as_formula);
			$as_formula=$arrResultado['as_formula'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		if($lb_valido)
		{
			// Variables de Conceptos
			$arrResultado=$this->uf_sustituir($as_codper,"CN[",$as_formula);
			$as_formula=$arrResultado['as_formula'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		if($lb_valido)
		{
			// Variables de Constantes
			$arrResultado=$this->uf_sustituir($as_codper,"CT[",$as_formula);	
			$as_formula=$arrResultado['as_formula'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		if($lb_valido)
		{
			// Evaluar la Fórmula
			$arrResultado=$this->io_eval->uf_evaluar_nomina($as_formula,$as_valor);
		    $as_valor=$arrResultado['result'];
		    $lb_valido=$arrResultado['lb_valido'];
			$as_valor=round($as_valor,2);
		}
		if(!$lb_valido)
		{
			$this->io_mensajes->message("ERROR->EN FORMULA CONCEPTO[".$_SESSION["la_conceptopersonal"]["codconc"]."].");
		}
		$arrResultado['as_valor']=$as_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_evaluar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_sustituir($as_codper,$as_exp,$as_formula)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sustituir
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_exp // Expresión que me identifica que tipo de valor se va a buscar
		//				   as_formula // fórmula del concepto
		//	      Returns: lb_valido True si se sustituye correctamente la fórmula ó False si hubo error 
		//	  Description: función que dado una formula sustituye los valores que son de la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_formula=trim($as_formula);
		$li_pos=strpos($as_formula,$as_exp);
		if($li_pos===false)
		{
			$li_pos=-1;
		}
		while (($li_pos>=0)&&($lb_valido))
		{
			$li=$li_pos;
			while (($li<strlen($as_formula))&&(substr($as_formula,$li,1)<>"]"))
			{
				$li=$li+1;
			}
			if($li==0)
			{
				$lb_valido=false;
				$li_pos=-1;
				break;
			}
			$ls_token=substr($as_formula,(strlen($as_exp)+$li_pos),($li-strlen($as_exp)-$li_pos));
			$ls_valor=0;
			switch ($as_exp)
			{
				case "FN["://Valor de Nómina
					$arrResultado=$this->uf_valor_nomina($as_codper,$ls_token,$ls_valor);
					$ls_valor=$arrResultado['as_valor'];
					$lb_valido=$arrResultado['lb_valido'];
					break;
					
				case "PS["://Valor de Personal
					$arrResultado=$this->uf_valor_personal($as_codper,$ls_token,$ls_valor);
					$ls_valor=$arrResultado['as_valor'];
					$lb_valido=$arrResultado['lb_valido'];
					break;

				case "TB["://Valor de Tabla de Sueldo
					$ls_token=str_pad($ls_token,20,"0",0);
					$arrResultado=$this->uf_valor_tabla($as_codper,$ls_token,$ls_valor);
					$ls_valor=$arrResultado['as_valor'];
					$lb_valido=$arrResultado['lb_valido'];
					break;

				case "CN["://Valor de Concepto
					$ls_token=str_pad($ls_token,10,"0",0);
					$arrResultado=$this->uf_valor_concepto($as_codper,$ls_token,$ls_valor);
					$ls_valor=$arrResultado['as_valor'];
					$lb_valido=$arrResultado['lb_valido'];
					break;

				case "CT["://Valor de Constante
					$ls_token=str_pad($ls_token,10,"0",0);
					$arrResultado=$this->uf_valor_constante($as_codper,$ls_token,$ls_valor);
					$ls_valor=$arrResultado['as_valor'];
					$lb_valido=$arrResultado['lb_valido'];
					break;
			}
			if($lb_valido)
			{
				$ls_token=substr($as_formula,$li_pos,$li-$li_pos+1);
				$as_formula=str_replace($ls_token,$ls_valor,$as_formula);
				if(strlen($as_formula)>$li_pos)
				{
					$li_pos=strpos($as_formula,$as_exp,$li_pos);
					if($li_pos===false)
					{
						$li_pos=-1;
					}				
				}
				else
				{
					$li_pos=-1;
				}
			}
		}
		$arrResultado['as_formula']=$as_formula;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_sustituir
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_valor_nomina($as_codper,$as_token,$as_valor)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_valor_nomina
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_token // token que va a ser reemplazado
		//				   as_valor // valor del token
		//	      Returns: lb_valido True si se sustituye correctamente el valor ó False si hubo error 
		//	  Description: función que dado un token se sutituye por su valor respectivo de la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$as_valor="";
		$lb_valido=true;
		$as_token=trim($as_token);
		switch ($as_token) 
		{
			case "NRO_SEMANA": // Semana en la que se encuentra el periodo
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_dia= substr($ld_fecdesper,8,2);
				$ld_mes=substr($ld_fecdesper,5,2);
				$ld_ano=substr($ld_fecdesper,0,4);
				$lb_encontro=false;
				$ld_dia_comparar=1;
				$li_i=0;
				while((!$lb_encontro)&&($li_i<=31))
				{
				   $li_i=$li_i+1;
				   if ($li_i==1)
				   {
					   $dia=date(w,mktime(0,0,0,1,$ld_mes,$ld_ano));
					   switch ($dia)
					   {
					   		case 1: //si es lunes
								$li_resto=0;
							break;
							case 2: //si es martes
								$li_resto=6;
							break;
							case 3: //si es miércoles
								$li_resto=5;
							break;
							case 4: //si es jueves
								$li_resto=4;
							break;
							case 5: //si es viernes
								$li_resto=3;
							break;
							case 6: //si es sábado
								$li_resto=2;
							break;
							case 0: //si es domingo
								$li_resto=1;
							break;							
					   }
					   
					}
					else
					{
						$li_resto=0;
					}
					
					if (($ld_dia_comparar <= intval($ld_dia)) && (intval($ld_dia) <= ($ld_dia_comparar+7-$li_resto)))
				   {
						$lb_encontro=true;
						$as_valor=$li_i;
				   }
				   else
				   {
						$ld_dia_comparar=$ld_dia_comparar+7-$li_resto;
				   }
					
				} //fin del while
				break;

			case "PRIMERA_QUINCENA": // Si es la primera quincena del mes
				if(substr($_SESSION["la_nomina"]["fechasper"],8,2)=="15")
				{
					$as_valor=true;
				}
				else
				{
					$as_valor=0;
				}
				break;

			case "SEGUNDA_QUINCENA": // Si es la segunda quincena del mes
				if(intval(substr($_SESSION["la_nomina"]["fechasper"],8,2))>15)
				{
					$as_valor=true;
				}
				else
				{
					$as_valor=0;
				}
				break;
				
			case "NRO_LUNES": // Número de lunes que tiene el período
				$ld_fecdes=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechas=$_SESSION["la_nomina"]["fechasper"];
				$as_valor=$this->io_sno->uf_nro_lunes($ld_fecdes,$ld_fechas);
				break;

			case "NRO_LUNESMES": // Número de lunes que tiene el mes
				$ld_fecdes=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechas=$_SESSION["la_nomina"]["fechasper"];
				$ld_diahasta=strftime("%d",mktime(0,0,0,(substr($ld_fecdes,5,2)+1),0,substr($ld_fecdes,0,4)));
				$ld_desde=substr($ld_fecdes,0,8)."01";
				$ld_hasta=substr($ld_fecdes,0,8).$ld_diahasta;
				$as_valor=$this->io_sno->uf_nro_lunes($ld_desde,$ld_hasta);
				break;

			case "NRO_DIAS_BV_S": // Número de días de bono vacacional 
				$as_valor=intval($_SESSION["la_nomina"]["diabonvacnom"]);
				break;

			case "NRO_DIAS_BV_R": // Número de días de reintegro
				$as_valor=intval($_SESSION["la_nomina"]["diareivacnom"]);
				break;

			case "FIN_MES": // Si es fin de mes
				$ld_fecdes=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechas=$_SESSION["la_nomina"]["fechasper"];
				$as_valor=$this->io_sno->uf_fin_mes($ld_fecdes,$ld_fechas);
				break;

			case "DIF_DIA_FIN_PERIODO": // Día del Fin del período
				$ai_diafin=intval(substr($_SESSION["la_nomina"]["fechasper"],8,2));
				$as_valor=30-$ai_diafin;
				if($as_valor<0)
				{
					$as_valor="(".$as_valor.")";
				}
				break;

			case "DHABILES": // Días Hábiles que tuvo el periodo
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_sabdom=$this->io_sno->uf_nro_sabydom($ld_fecdesper,$ld_fechasper);
				$ld_diades=substr($ld_fecdesper,8,2);
				$ld_mesdes=substr($ld_fecdesper,5,2);
				$ld_anodes=substr($ld_fecdesper,0,4);
				$ld_diahas=substr($ld_fechasper,8,2);
				$ld_meshas=substr($ld_fechasper,5,2);
				$ld_anohas=substr($ld_fechasper,0,4);
				$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
				$as_valor=round($as_valor-$li_sabdom);
				break;
				
			case "DHABILES_MES":
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_diades="01";
				$ld_mesdes=substr($ld_fecdesper,5,2);
				$ld_anodes=substr($ld_fecdesper,0,4);
				$ld_fecdesper=$ld_anodes."-".$ld_mesdes."-".$ld_diades;
				$ld_fecfinmes=$this->io_fecha->uf_last_day($ld_mesdes,$ld_anodes);
				$ld_fecfinmes=$this->io_fecha->uf_convert_date_to_db($ld_fecfinmes);
				$ld_diahas=substr($ld_fecfinmes,8,2);
				$ld_meshas=substr($ld_fecfinmes,5,2);
				$ld_anohas=substr($ld_fecfinmes,0,4);
				$li_sabdom=$this->io_sno->uf_nro_sabydom($ld_fecdesper,$ld_fecfinmes);
				$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
				$as_valor=round($as_valor-$li_sabdom);
				break;
			
			case "VALOR_CT": // Monto del cesta ticket según la nómina
				$ls_codnom=$_SESSION["la_nomina"]["codnom"];
				$as_valor=$this->io_cestaticket->uf_select_valor_ct($ls_codnom);
				break;

			case substr($as_token,0,16)=="UNIDADTRIBUTARIA": // Valor de la unidad tributaria
				$li_ano=intval(substr($as_token,17,4));
				if($li_ano==0)
				{
					$li_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);
				}
				$as_valor=0;
				$arrResultado=$this->uf_obtener_unidadtributaria($li_ano,$as_valor);
				$as_valor=$arrResultado['ai_unidadtributaria'];
				$lb_valido=$arrResultado['lb_valido'];
				break;
			
			case "NRO_PERIODO": // Nro dle periodo actual de la nomina
				$as_valor=$_SESSION["la_nomina"]["codperi"];
				break;

			case "MES_31": // Si el mes termina en 31
				$anio=intval(substr($_SESSION["la_nomina"]["fechasper"],0,4));
				$mes=intval(substr($_SESSION["la_nomina"]["fechasper"],5,2));
				$dia=28; 
				$as_valor=0;
				while (checkdate($mes,$dia + 1,$anio))
				{ 
				   $dia++; 
				} 
				if($dia==31)
				{
					$as_valor=1;
				}
				break;

			default: // si el token no existe
				$this->io_mensajes->message("ERROR->NOMINA FN[".$as_token."] Nó Válido.");			
				$lb_valido=false;
				break;
		}
		$arrResultado['as_valor']=$as_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_valor_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_valor_personal($as_codper,$as_token,$as_valor)
	{			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_valor_personal
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_token // valor que va a ser reemplazado
		//				   as_valor // valor del token
		//	      Returns: lb_valido True si se sustituye correctamente el valor ó False si hubo error 
		//	  Description: función que dado un token se sutituye por su valor respectivo del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$as_valor="";
		$lb_valido=true;
		$as_token=trim($as_token);
		switch ($as_token)
		{
			case "DIAS_LABORADO": // Número de días laborados por la persona desde que llegó a la institución
				$ld_fechatope=$this->io_funciones->uf_convertirdatetobd($this->io_sno->uf_select_config("SNO","ANTIGUEDAD","FECHA_TOPE","1900-01-01","C"));
				$ld_fecingper=$this->personal->fecingper;
				$ld_diades=substr($ld_fecingper,8,2);
				$ld_mesdes=substr($ld_fecingper,5,2);
				$ld_anodes=substr($ld_fecingper,0,4);
				if(($ld_fechatope!="1900-01-01")&&($ld_fechatope!=""))
				{
					$ld_diahas=substr($ld_fechatope,8,2);
					$ld_meshas=substr($ld_fechatope,5,2);
					$ld_anohas=substr($ld_fechatope,0,4);
				}
				else
				{
					$ld_fechatope=$_SESSION["la_nomina"]["fechasper"];
					$ld_diahas=substr($ld_fechatope,8,2);
					$ld_meshas=substr($ld_fechatope,5,2);
					$ld_anohas=substr($ld_fechatope,0,4);
				}
				$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
				$as_valor=round($as_valor);
				break;

			case "MESES_LABORADO": // Número de meses laborados por la persona desde que llegó
				$ld_fechatope=$this->io_funciones->uf_convertirdatetobd($this->io_sno->uf_select_config("SNO","ANTIGUEDAD","FECHA_TOPE","1900-01-01","C"));
				$ld_fecingper=$this->personal->fecingper;
				$ld_diades=substr($ld_fecingper,8,2);
				$ld_mesdes=substr($ld_fecingper,5,2);
				$ld_anodes=substr($ld_fecingper,0,4);
				if (($ld_fechatope!="1900-01-01")&&($ld_fechatope!=""))
				{
					$ld_diahas=substr($ld_fechatope,8,2);
					$ld_meshas=substr($ld_fechatope,5,2);
					$ld_anohas=substr($ld_fechatope,0,4);
				}
				else
				{
					$ld_fechatope=$_SESSION["la_nomina"]["fechasper"];
					$ld_diahas=substr($ld_fechatope,8,2);
					$ld_meshas=substr($ld_fechatope,5,2);
					$ld_anohas=substr($ld_fechatope,0,4);
				}
				$as_valor=(((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1)/30;
				$as_valor=round($as_valor);
				break;

			case "MESES_LABORADO_PERIODO": // Número de meses laborados por la persona desde el comienzo del periodo fiscal 
				$ld_fechatope=$this->io_funciones->uf_convertirdatetobd($this->io_sno->uf_select_config("SNO","ANTIGUEDAD","FECHA_TOPE","1900-01-01","C"));
				$ld_fecingper=$this->personal->fecingper;

				$ld_diades1=substr($_SESSION['la_empresa']['periodo'],8,2);
				$ld_mesdes1=substr($_SESSION['la_empresa']['periodo'],5,2);
				$ld_anodes1=substr($_SESSION['la_empresa']['periodo'],0,4);
				
				$ld_diades=substr($ld_fecingper,8,2);
				$ld_mesdes=substr($ld_fecingper,5,2);
				$ld_anodes=substr($ld_fecingper,0,4);
				if($ld_anodes<$ld_anodes1)
				{
					$ld_anodes=$ld_anodes1;
					$ld_mesdes=$ld_mesdes1;
					$ld_diades=$ld_diades1;
				}
				if (($this->personal->fecculcontr!="1900-01-01")&&($this->personal->fecculcontr!=""))
				{
					$ld_diahas=substr($this->personal->fecculcontr,8,2);
					$ld_meshas=substr($this->personal->fecculcontr,5,2);
					$ld_anohas=substr($this->personal->fecculcontr,0,4);
				}
				else
				{				
					if (($ld_fechatope!="1900-01-01")&&($ld_fechatope!=""))
					{
						$ld_diahas=substr($ld_fechatope,8,2);
						$ld_meshas=substr($ld_fechatope,5,2);
						$ld_anohas=substr($ld_fechatope,0,4);
					}
					else
					{
						$ld_fechatope=$_SESSION["la_nomina"]["fechasper"];
						$ld_diahas=substr($ld_fechatope,8,2);
						$ld_meshas=substr($ld_fechatope,5,2);
						$ld_anohas=substr($ld_fechatope,0,4);
					}
				}
				$as_valor=(((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1)/30;
				$as_valor=round(abs($as_valor),2);
				$as_valor=round(abs($as_valor),0);
				break;
			
			case "ANIO_TRABAJOANT": // Preavisos
				$li_anio=0;
				$li_mes=0;
				$li_dias=0;
				$arrResultado=$this->uf_obtener_anio_trabajado_anterior($as_codper,$li_anio,$li_mes,$li_dias,"");
				$li_anio=$arrResultado['ai_anio'];
				$li_mes=$arrResultado['ai_mes'];
				$li_dias=$arrResultado['ai_dia'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_anio;
				}
			break;			

			case "ANTIGUEDAD_TRABAJOANT_ANIO": // 
				$li_anioant=0;
				$li_mesant=0;
				$li_diaant=0;
				$li_ano=0;
				$li_mes=0;
				$li_dia=0;
				$arrResultado=$this->uf_obtener_anio_trabajado_anterior($as_codper,$li_anioant,$li_mesant,$li_diaant,"");
				$li_anioant=$arrResultado['ai_anio'];
				$li_mesant=$arrResultado['ai_mes'];
				$li_diaant=$arrResultado['ai_dia'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$ld_fecingper=$this->personal->fecingper;
					$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
					$ld_fecing=substr($ld_fecingper,0,4);
					$li_ano=$ld_fechasper-$ld_fecing;
					$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
					$li_mes=intval(substr($ld_fechasper,5,2));
					$li_dia=intval(substr($ld_fechasper,8,2));
					if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
					{
						$li_ano=$li_ano-1;
					}
					else
					{
						if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,5,2)))
						{
							if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,8,2)))
							{
								$li_ano=$li_ano-1;
							}
						}
					}
					$li_dias=floor(($li_diaant+$li_dia)/30);
					if($li_dias>0)
					{
						$li_mes=$li_mes+$li_dias;
					}
					$li_meses=floor(($li_mes+$li_mesant)/12);
					if($li_meses>0)
					{
						$li_ano=$li_ano+$li_meses+$li_anioant;
					}
					else
					{
						$li_ano=$li_ano+$li_anioant;
					}					
				}
				$as_valor=$li_ano;
			break;

			case "ANTIGUEDAD_ANIOSERVPREVIO": // 
				$li_anioant=$this->personal->anoservpreper;
				$li_mesant=$this->personal->messervpreper;
				$li_ano=0;
				$li_mes=0;
				$ld_fecingper=$this->personal->fecingper;
				$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$ld_fecing=substr($ld_fecingper,0,4);
				$li_ano=$ld_fechasper-$ld_fecing;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_mes=intval(substr($ld_fechasper,5,2));
				$li_dia=intval(substr($ld_fechasper,8,2));
				if(intval(substr($ld_fechasper,5,2))<=intval(substr($ld_fecingper,5,2)))
				{
					if($li_ano>0)
					{
						$li_ano=$li_ano-1;
					}
				}
				else
				{
					if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,5,2)))
					{
						if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,8,2)))
						{
							if($li_ano>0)
							{
								$li_ano=$li_ano-1;
							}
						}
					}
					else
					{
						$li_mes = ($li_mes - intval(substr($ld_fecingper,5,2)));
					}
				}
				$li_meses=floor(($li_mes+$li_mesant)/12);
				if($li_meses>0)
				{
					$li_ano=$li_ano+$li_meses+$li_anioant;
				}
				else
				{
					$li_ano=$li_ano+$li_anioant;
				}
				$li_mes = ($li_mes+$li_mesant) - (12*$li_meses);
				$as_valor=$li_ano.".".$li_mes;
			break;

			case "SUELDO": // Sueldo de la persona
				$as_valor=$this->personal->sueper;
				break;

			case "SUELDO_DOCENTE": // Monto del sueldo de la Clasificación docente que tiene
				$ls_codper=$this->personal->codper;
				$ai_sueldo=0;
				$as_valor=0;
				$arrResultado=$this->uf_obtener_sueldo_docente($ls_codper,$ai_sueldo);
				$ai_sueldo=$arrResultado['ai_sueldo'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ai_sueldo;
				}
				break;

			case "PRIMA_DOC_JERARQUIA": // Monto de la prima docente de Jerarquia
				$ls_codper=$this->personal->codper;
				$ai_monto=0;
				$as_valor=0;
				$tipo_prima="0";
				$arrResultado=$this->uf_obtener_prima_docente($ls_codper,$tipo_prima,$ai_monto);
				$ai_monto=$arrResultado['ai_prima'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ai_monto;
				}
				break;

			case "PRIMA_DOC_ANTIGUEDAD": // Monto de la prima docente de Antiguedad
				$ls_codper=$this->personal->codper;
				$ai_monto=0;
				$as_valor=0;
				$tipo_prima="1";
				$arrResultado=$this->uf_obtener_prima_docente($ls_codper,$tipo_prima,$ai_monto);
				$ai_monto=$arrResultado['ai_prima'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ai_monto;
				}
				break;
				
			case "PRIMA_DOC_HOGAR": // Monto de la prima docente de Hogar
				$ls_codper=$this->personal->codper;
				$ai_monto=0;
				$as_valor=0;
				$tipo_prima="2";
				$arrResultado=$this->uf_obtener_prima_docente($ls_codper,$tipo_prima,$ai_monto);
				$ai_monto=$arrResultado['ai_prima'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ai_monto;
				}
				break;

			case "SUELDO_MINIMO": //  Retorna el ultimo sueldo minimo
				$as_valor=0;
				$li_sueldominimo=0;
                                $arrResultado=$this->uf_obtener_sueldominimo($li_sueldominimo);
				$li_sueldominimo=$arrResultado['ai_sueldominimo'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$li_sueldominimo=number_format((float)$li_sueldominimo,2,".","");
					$as_valor=$li_sueldominimo;
				}
				break;

                        case substr($as_token,0,14)=="SUELDO_MINIMO_": // Número de Hijos Menores de la persona
                                $ls_codsuemin=substr($as_token,14,4);
				$as_valor=0;
				$li_sueldominimo=0;
                                $arrResultado=$this->uf_obtener_sueldominimo($li_sueldominimo,$ls_codsuemin);
				$li_sueldominimo=$arrResultado['ai_sueldominimo'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$li_sueldominimo=number_format((float)$li_sueldominimo,2,".","");
					$as_valor=$li_sueldominimo;
				}
				break;

			case "SUELDO_MIN_GRADO": // Sueldo Mínimo según el grado que tenga el obrero
				$as_valor=$this->personal->suemingra;
				break;
				
			case "DIF_SUELDOMIN": //  Diferencia del sueldo Mínimo con respecto al sueldo base
				$as_valor=0;
				$li_sueldominimo=0;
			    $arrResultado=$this->uf_obtener_sueldominimo($li_sueldominimo);
				$li_sueldominimo=$arrResultado['ai_sueldominimo'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$li_diferencia=number_format((float)$li_sueldominimo-$this->personal->suemingra,2,".","");
					if($li_diferencia>0)
					{
						$as_valor=$li_diferencia;
					}
				}
				break;
				
			case "COMPENSACION_OBRERO": // Monto de la Compensación para las nóminas de obreros con clasificación
				$as_valor=$this->personal->sueper-$this->personal->suemingra;
				if($as_valor<0)
				{
					$as_valor=0;
				}
				break;

			case substr($as_token,0,10)=="SUELDO_ANT": // SUELDO DEL MES ANTERIOR
				$as_valor=0;
				$as_sueldo=0;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$ls_ano=substr($ld_fechasper,0,4);
				$li_mes=intval(substr($as_token,11,2));
				if($li_mes==0)
				{
					$ls_mes=substr($ld_fechasper,5,2)-1;
					$ls_mes=str_pad($ls_mes,2,"0",0);
					if($ls_mes=="00")
					{
						$ls_ano=$ls_ano-1;
						$ls_mes="12";
					}
				}
				else
				{
					$ls_mes=str_pad($li_mes,2,"0",0);
					if($ls_mes=="12")
					{
						$ls_ano=$ls_ano-1;
					}
				}
				$arrResultado=$this->uf_obtener_sueldo_ante($as_codper,$ls_mes,$ls_ano,$as_sueldo);
				$as_sueldo=$arrResultado['ai_sueldo'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$as_sueldo;
				}
				break;

			case "SUELDO_PERIODO_ANT": // SUELDO DE LA QUINCENA ANTERIOR
				$as_valor=0;
				$as_sueldo=0;
				$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$ls_ano=substr($ld_fechasper,0,4);
				$ls_perant=(intval($ls_peractnom)-1);
				$ls_perant=str_pad($ls_perant,3,"0",0);
				if($ls_perant=='000')
				{
					$ls_ano=$ls_ano-1;
					switch($_SESSION["la_nomina"]["tippernom"])
					{
						case 0://Semanal
							$ls_perant='052';
							break;
			
						case 1://Quincenal
							$ls_perant='024';
							break;
			
						case 2://Mensual
							$ls_perant='012';
							break;
			
						case 3://Anual
							$ls_perant='001';
							break;
					}
				}
				$arrResultado=$this->uf_obtener_sueldo_quincena_ante($as_codper,$ls_perant,$ls_ano,$as_sueldo);
				$as_sueldo=$arrResultado['ai_sueldo'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$as_sueldo;
				}
				break;

			case "SUELDO_PROMEDIO": // Sueldo Integral de la persona
				$as_valor=$this->personal->sueproper;
				break;

			case "HORAS": // Horas que labora la persona
				$as_valor=$this->personal->horper;
				break;

			case "SEXO": // Sexo de la persona
				$as_valor="".$this->personal->sexper."";
				break;

			case "NRO_HIJOS": // Número de Hijos de la persona
				$as_valor=$this->personal->numhijper;
				break;

			case "HIJO_ESPECIAL": // Cantidad de hijos especiales que tiene la persona
				$ls_codper=$this->personal->codper;
				$as_valor=$this->io_familiar->uf_select_hijos_especiales($ls_codper);
				break;

			case "HIJOS_BONO_JUGUETE": // Cantidad de hijos que reciben el bono juguete de la persona
				$ls_codper=$this->personal->codper;
				$as_valor=$this->io_familiar->uf_select_hijos_bono_juguetes($ls_codper);
				break;

			case "A_SERVICIO": // Años de Servicios previos de la persona
				$as_valor=$this->personal->anoservpreper;
				break;
				
			case "EDAD": // Edad de la persona
				$ld_fechas=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$ld_fecnacper=substr($this->personal->fecnacper,0,4);
				$as_valor=$ld_fechas-$ld_fecnacper;
				$ld_fechas=$_SESSION["la_nomina"]["fechasper"];
				$ld_fecnacper=$this->personal->fecnacper;
				if(intval(substr($ld_fechas,5,2))<intval(substr($ld_fecnacper,5,2)))
				{
					$as_valor=$as_valor-1;
				}
				else
				{
					if(intval(substr($ld_fechas,5,2))==intval(substr($ld_fecnacper,5,2)))
					{
						if(intval(substr($ld_fechas,8,2))<intval(substr($ld_fecnacper,8,2)))
						{
							$as_valor=$as_valor-1;
						}
					}
				}
				break;
				
			case "SUELDO_INTEGRAL": // Sueldo Integral de la persona
				if($_SESSION["la_nomina"]["calculandosueldointegral"])
				{
					$as_valor=0;
					$lb_valido=false;
					$this->io_mensajes->message("ERROR->De anidamiento con SUELDO_INTEGRAL concepto ".$_SESSION["la_conceptopersonal"]["codconc"].".");

				}
				else
				{
					$as_valor=$this->personal->sueldointegral;
				}
				break;

			case "SALARIO_NORMAL": // Salario Normal de la personaL
				if($_SESSION["la_nomina"]["calculandosueldonormal"])
				{
					$as_valor=0;
					$lb_valido=false;
					$this->io_mensajes->message("ERROR->De anidamiento con SALARIO_NORMAL concepto ".$_SESSION["la_conceptopersonal"]["codconc"].".");

				}
				else
				{
					$as_valor=$this->personal->salarionormal;
				}
				break;
				
			case "ESTATUS": // Estatus de la persona
				$ls_staper=$this->personal->staper;
				switch ($ls_staper)
				{
					case "1":
						$as_valor="'A'";
						break;

					case "2":
						$as_valor="'V'";
						break;

					case "3":
						$as_valor="'E'";
						break;
				}
				break;

			case "V_DIASBONO": // Días de bono vacacional de la persona
				$as_valor=intval($_SESSION["la_vacacionpersonal"]["diabonvac"]);
				break;

			case "V_DIASBONO_ADIC": // Días adicionales de bono vacacional de la persona
				$as_valor=intval($_SESSION["la_vacacionpersonal"]["diaadibon"]);
				break;

			case "V_NRO_DIAS": // número de días hábiles de vacaciones de la persona
				$as_valor=intval($_SESSION["la_vacacionpersonal"]["diavac"]);
				break;

			case "V_DIASVAC_ADIC": // Dias adicionales de vacaciones de la persona
				$as_valor=intval($_SESSION["la_vacacionpersonal"]["diaadivac"]);
				break;

			case "V_CANCELAR_DIASVAC_ADIC": // Dias adicionales de vacaciones de la persona
				$as_valor=intval($_SESSION["la_vacacionpersonal"]["candiaadi"]);
				break;

			case "NRO_LUNES_S": // número de días lunes de salida de vacaciones de la persona
				$as_valor=$_SESSION["la_vacacionpersonal"]["nrolunes_s"];
				break;

			case "NRO_LUNES_R": // número de días lunes de reingreso de vacaciones de la persona
				$as_valor=$_SESSION["la_vacacionpersonal"]["nrolunes_r"];
				break;

			case "V_NRO_QNA_S": // quincena de salida de vacaciones de la persona
				$as_valor=$_SESSION["la_vacacionpersonal"]["quisalvac"];
				break;

			case "V_NRO_QNA_R": // quincena de reingreso de vacaciones de la persona
				$as_valor=$_SESSION["la_vacacionpersonal"]["quireivac"];
				break;

			case "SIV": // sueldo integral de vacaciones de la persona
				$as_valor=$_SESSION["la_vacacionpersonal"]["sueintvac"];
				break;

			case "SISV": // Sueldo integral de bono vacacional de la persona
				$as_valor=$_SESSION["la_vacacionpersonal"]["sueintbonvac"];
				break;

			case "NRO_DIAS_CALEN_S": // número de días calendario de salida de vacaciones de la persona
				$ld_fecreivac=$_SESSION["la_vacacionpersonal"]["fecreivac"];
				$ld_fecdisvac=$_SESSION["la_vacacionpersonal"]["fecdisvac"];
				$ld_diades=substr($ld_fecdisvac,8,2);
				$ld_mesdes=substr($ld_fecdisvac,5,2);
				$ld_anodes=substr($ld_fecdisvac,0,4);
				$ld_diahas=substr($ld_fecreivac,8,2);
				$ld_meshas=substr($ld_fecreivac,5,2);
				$ld_anohas=substr($ld_fecreivac,0,4);
				$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
				$as_valor=round($as_valor);
				break;

			case "NRO_DIAS_CALEN_R": // número de días calendario de reingreso de vacaciones de la persona
				$ld_fecreivac=$_SESSION["la_vacacionpersonal"]["fecreivac"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$ld_diades=substr($ld_fecreivac,8,2);
				$ld_mesdes=substr($ld_fecreivac,5,2);
				$ld_anodes=substr($ld_fecreivac,0,4);
				$ld_diahas=substr($ld_fechasper,8,2);
				$ld_meshas=substr($ld_fechasper,5,2);
				$ld_anohas=substr($ld_fechasper,0,4);
				$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
				$as_valor=round($as_valor);
				break;

			case "NRO_DIAS_HABILES": // número de días hábiles de vacaciones de la persona
				$as_valor=$_SESSION["la_vacacionpersonal"]["diavac"];
				break;

			case "NRO_DIAS_FERIADOS": // número de días feriados de vacaciones de la persona
				$as_valor=$_SESSION["la_vacacionpersonal"]["diafer"];
				break;

			case "NRO_DIAS_SABYDOM": // número de días sabados y domingos de vacaciones de la persona
				$as_valor=$_SESSION["la_vacacionpersonal"]["sabdom"];
				break;
				
			case "VAC_PROX_PER": // Indica si la persona sale de vacaciones en el proximo periodo de la nómina
				$ls_codper=$this->personal->codper;				
				$li_numpernom=intval($_SESSION["la_nomina"]["numpernom"]);
				$li_peractnom=intval($_SESSION["la_nomina"]["peractnom"]);
				$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];				
				$ls_codnom=$_SESSION["la_nomina"]["codnom"];
				$ls_tippernom=$_SESSION["la_nomina"]["tippernom"];							
				$as_valor=0;//false;	
				if(($li_numpernom>$li_peractnom)&&($li_peractnom!=0))
				{
					$ls_perpronom=$li_peractnom+1;
					$ls_perpronom=str_pad($ls_perpronom,3,"0",0);	
					$arrResultado=$this->io_nomina->uf_buscar_prox_periodo($ls_codnom,$ls_perpronom,$ld_fecdespro,$ld_fechaspro);
					$ld_fecdespro=$arrResultado['ad_fecdesper'];
					$ld_fechaspro=$arrResultado['ad_fechasper'];
					$lb_valido=$arrResultado['lb_existe'];
					$ls_fecingper=$this->personal->fecingper;
					$ls_diames=substr($ls_fecingper,4,9);
					$ls_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);					
					$ls_fecha=$ls_ano.$ls_diames; 									
					$ls_fecha=$this->io_funciones->uf_convertirfecmostrar($ls_fecha);
					
					if (($this->io_fecha->uf_comparar_fecha($ld_fecdespro,$ls_fecha))&&
					    ($this->io_fecha->uf_comparar_fecha($ls_fecha,$ld_fechaspro)))
					{
						$as_valor=1;  //true												
					}
					else
					{
						$as_valor=0;  //false					
					}
				}
				elseif($li_numpernom==$li_peractnom)
				{
					$arrResultado=$this->io_nomina->uf_seleccionar_periodoadicional($ls_codnom,$ls_tippernom,$ls_codperi,$ld_fecdespro,$ld_fechaspro);
					$ls_codperi=$arrResultado['as_codperi'];
					$ld_fecdespro=$arrResultado['ad_fecdesper'];
					$ld_fechaspro=$arrResultado['ad_fechasper'];
					$lb_valido=$arrResultado['lb_existe'];
					$ls_fecingper=$this->personal->fecingper;
					$ls_diames=substr($ls_fecingper,4,9);
					$ls_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);					
					$ls_fecha=$ls_ano.$ls_diames; 									
					$ls_fecha=$this->io_funciones->uf_convertirfecmostrar($ls_fecha);
										
					if (($this->io_fecha->uf_comparar_fecha($ld_fecdespro,$ls_fecha))&&
					    ($this->io_fecha->uf_comparar_fecha($ls_fecha,$ld_fechaspro)))
					{	
						$as_valor=1; // true												
					}
					else
					{
						$as_valor=0;  // false
					}							
				}							
				break;

			case "PRIMAS_POR_HIJO": // Primas por Hijo de la persona
				$li_numhijper=$this->personal->numhijper;
				$as_valor=0;
				if($li_numhijper>0)
				{
					$ls_codconc=$_SESSION["la_conceptopersonal"]["codconc"];
					$arrResultado=$this->io_primaconcepto->uf_select_primahijos($ls_codconc,$li_valpri);
					$li_valpri=$arrResultado['ai_valor'];
					$lb_valido=$arrResultado['lb_valido'];
					if($lb_valido)
					{
						$as_valor=$li_numhijper*$li_valpri;
					}
				}
				break;

			case "PRIMA_POR_ANTIGUEDAD": // Primas por Antiguedad de la persona
				$as_valor=0;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fecingper=$this->personal->fecingper;
				$ai_mesingper=intval(substr($ld_fecingper,6,2));
				$ai_meshasper=intval(substr($ld_fechasper,6,2));
				if($ai_mesingper==$ai_meshasper)
				{
					$li_diaingper=intval(substr($ld_fecingper,8,2));
					$li_diadesper=intval(substr($ld_fecdesper,8,2));
					$li_diahasper=intval(substr($ld_fechasper,8,2));
					if(($li_diaingper>=$li_diadesper)&&($li_diaingper<=$li_diahasper))
					{
						$ls_codconc=$_SESSION["la_conceptopersonal"]["codconc"];
						$ld_diades=substr($ld_fecingper,8,2);
						$ld_mesdes=substr($ld_fecingper,5,2);
						$ld_anodes=substr($ld_fecingper,0,4);
						$ld_diahas=substr($ld_fechasper,8,2);
						$ld_meshas=substr($ld_fechasper,5,2);
						$ld_anohas=substr($ld_fechasper,0,4);
						$ai_ano=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
						$ai_ano=round($ai_ano/365);
						$li_valpri=0;
						$arrResultado=$this->io_primaconcepto->uf_select_primaantiguedad($ls_codconc,$ai_ano,$li_valpri);
						$li_valpri=$arrResultado['ai_valor'];
						$lb_valido=$arrResultado['lb_valido'];
						if($lb_valido)
						{
							$as_valor=$li_valpri;
						}
					}
				}				
				break;

			case "COMPENSACION": // Monto Compensación del grado de la persona
				$as_valor=$_SESSION["la_tablasueldo"]["moncomgra"];
				break;

			case "PRIMA_TABULADOR": // Suma de las primas asociadas al tabulador, paso, y grado del personal
				$as_valor=$_SESSION["la_tablasueldo"]["monto_primas"];
				break;

			case substr($as_token,0,12)=="ANTIGUEDAD_A": // Antiguedad en años de la persona
				$li_ano=intval(substr($as_token,13,4));
				if($li_ano==0)
				{
					$ld_fecingper=$this->personal->fecingper;
				}
				else
				{
					$ld_fecingper=$li_ano.substr($this->personal->fecingper,4,6);
				}
				$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$ld_fecing=substr($ld_fecingper,0,4);
				$as_valor=$ld_fechasper-$ld_fecing;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
				{
					$as_valor=$as_valor-1;
				}
				else
				{
					if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,5,2)))
					{
						if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,8,2)))
						{
							$as_valor=$as_valor-1;
						}
					}
				}
				break;

			case "ANTIG_DC": // Antiguedad en días de la persona
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$ld_fecingper=$this->personal->fecingper;
				$ld_diades=substr($ld_fecingper,8,2);
				$ld_mesdes=substr($ld_fecingper,5,2);
				$ld_anodes=substr($ld_fecingper,0,4);
				$ld_diahas=substr($ld_fechasper,8,2);
				$ld_meshas=substr($ld_fechasper,5,2);
				$ld_anohas=substr($ld_fechasper,0,4);
				$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
				$as_valor=round($as_valor);
				break;

			case "ANTIGUEDAD_M": // Antiguedad en meses de la persona
				$ld_fecingper=$this->personal->fecingper;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$ld_diahas = substr($ld_fechasper, 8, 2);  
				$ld_meshas = substr($ld_fechasper, 5, 2);  
				$ld_anohas = substr($ld_fechasper, 0, 4); 
				$ld_diades = substr($ld_fecingper, 8, 2);  
				$ld_mesdes = substr($ld_fecingper, 5, 2);  
				$ld_anodes = substr($ld_fecingper, 0, 4);  
				$b = 0;  
				$mes = $ld_mesdes-1; 				  
				if($mes==2)
				{  
					if(($ld_anohas%4==0 && $ld_anohas%100!=0) || $ld_anohas%400==0)
					{  
						$b = 29;  
					}
					else
					{  
						$b = 28;  
					}  
				}  
				else if($mes<=7)
				{  
					if($mes==0)
					{  
						$b = 31;  
					}  
					else if($mes%2==0)
					{  
						$b = 30;  
					}  
				   else
				   {  
						$b = 31;  
				   }  
				}  
				else if($mes>7)
				{  
				   if($mes%2==0)
				   {  
						$b = 31;  
				   }  
				   else
				   {  
						$b = 30;  
				   }  
				}  
				if($ld_mesdes <= $ld_meshas)
				{  
				   $anios = $ld_anohas - $ld_anodes;  
				   if($ld_diades <= $ld_diahas)
				   {  
						$meses = $ld_meshas - $ld_mesdes;  
						$dies = $ld_diahas - $ld_diades;  
				   }
				   else
				   {  
						if($ld_meshas == $ld_mesdes)
						{  
							$anios = $anios - 1;  
						}  
						$meses = ($ld_meshas - $ld_mesdes - 1 + 12) % 12;  
						$dies = $b-($ld_diades-$ld_diahas);  
				   }  
				}
				else
				{  
				   $anios = $ld_anohas - $ld_anodes - 1;  
				   if($ld_diades > $ld_diahas)
				   {  
						$meses = $ld_meshas - $ld_mesdes -1 +12;  
						$dies = $b - ($ld_diades-$ld_diahas);  
				   }
				   else
				   {  
						$meses = $ld_meshas - $ld_mesdes + 12;  
						$dies = $ld_diahas - $ld_diades;  
				   }  
				}
				 $total_mes=($anios*12)+$meses+($dies/30);
				 $as_valor=round($total_mes,2);
				break;

			case "ANTIGUEDAD_M_ENTERO": // Antiguedad en meses de la persona pero en valores enteros
				$ld_fecingper=$this->personal->fecingper;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$ld_diahas = substr($ld_fechasper, 8, 2);  
				$ld_meshas = substr($ld_fechasper, 5, 2);  
				$ld_anohas = substr($ld_fechasper, 0, 4); 
				$ld_diades = substr($ld_fecingper, 8, 2);  
				$ld_mesdes = substr($ld_fecingper, 5, 2);  
				$ld_anodes = substr($ld_fecingper, 0, 4);  
				$b = 0;  
				$mes = $ld_mesdes-1; 				  
				if($mes==2)
				{  
					if(($ld_anohas%4==0 && $ld_anohas%100!=0) || $ld_anohas%400==0)
					{  
						$b = 29;  
					}
					else
					{  
						$b = 28;  
					}  
				}  
				else if($mes<=7)
				{  
					if($mes==0)
					{  
						$b = 31;  
					}  
					else if($mes%2==0)
					{  
						$b = 30;  
					}  
				   else
				   {  
						$b = 31;  
				   }  
				}  
				else if($mes>7)
				{  
				   if($mes%2==0)
				   {  
						$b = 31;  
				   }  
				   else
				   {  
						$b = 30;  
				   }  
				}  
				if($ld_mesdes <= $ld_meshas)
				{  
				   $anios = $ld_anohas - $ld_anodes;  
				   if($ld_diades <= $ld_diahas)
				   {  
						$meses = $ld_meshas - $ld_mesdes;  
						$dies = $ld_diahas - $ld_diades;  
				   }
				   else
				   {  
						if($ld_meshas == $ld_mesdes)
						{  
							$anios = $anios - 1;  
						}  
						$meses = ($ld_meshas - $ld_mesdes - 1 + 12) % 12;  
						$dies = $b-($ld_diades-$ld_diahas);  
				   }  
				}
				else
				{  
				   $anios = $ld_anohas - $ld_anodes - 1;  
				   if($ld_diades > $ld_diahas)
				   {  
						$meses = $ld_meshas - $ld_mesdes -1 +12;  
						$dies = $b - ($ld_diades-$ld_diahas);  
				   }
				   else
				   {  
						$meses = $ld_meshas - $ld_mesdes + 12;  
						$dies = $ld_diahas - $ld_diades;  
				   }  
				}
				 $total_mes= (($anios*12)+$meses);
				 $as_valor=$total_mes;
				break;

			case substr($as_token,0,7)=="MENORES": // Número de Hijos Menores de la persona
				$ls_codper=$this->personal->codper;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_edad=intval(substr($as_token,8,2));
				if($li_edad==0)
				{
					$li_edad=18;
				}
				$arrResultado=$this->io_familiar->uf_load_hijosmenores($ls_codper,$li_edad,$ld_fechasper,$as_valor);
				$as_valor=$arrResultado['li_total'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case substr($as_token,0,16)=="ESTUDIANTE_MENOR": // Número de Hijos Menores de la persona
				$ls_codper=$this->personal->codper;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_edaddesde=intval(substr($as_token,17,2));
				$li_edadhasta=intval(substr($as_token,20,2));
				$arrResultado=$this->io_familiar->uf_load_hijosmenores_estudiantes($ls_codper,$li_edaddesde,$li_edadhasta,$ld_fechasper,$as_valor);
				$as_valor=$arrResultado['li_total'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "AR-C": // Monto de ISR del mes en curso de la persona
				$ls_codper=$this->personal->codper;
				$ls_mes=substr($_SESSION["la_nomina"]["fechasper"],5,2);
				$arrResultado=$this->io_isr->uf_load_isrpersonal($ls_codper,$ls_mes,$as_valor);
				$as_valor=$arrResultado['li_porisr'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case substr($as_token,0,3)=="VCA": // Total del Concepto en un período anterior
				$ls_codconc=str_pad(substr($as_token,4,10),10,"0",0);
				$ls_codper=$this->personal->codper;
				$as_valor=0;
				$li_vca=0;
				$li_anopre=0;
				$li_perpre=0;
				$arrResultado=$in_class_sno->uf_periodo_previo($li_anopre,$li_perpre,$li_vca);
				$li_anopre=$arrResultado['ai_anoprev'];
				$li_perpre=$arrResultado['ai_periprev'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$arrResultado=$this->uf_obtener_vca($ls_codper,$ls_codconc,$li_anopre,$li_perpre,$li_vca);
					$li_vca=$arrResultado['ai_vca'];
					$lb_valido=$arrResultado['lb_valido'];
					if($lb_valido)
					{
						$as_valor=$li_vca;
					}
				}
				break;

			case "DHABILES": // Días Hábiles que tuvo el mes sin los feriados, sin los días de permiso que tuvo el personal
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_sabdom=$this->io_sno->uf_nro_sabydom($ld_fecdesper,$ld_fechasper);
				$li_diafer=$this->io_feriado->uf_select_feriados($ld_fecdesper,$ld_fechasper,$_SESSION["la_nomina"]["codnom"],$as_codper,0);
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1','');
				$ld_diades=substr($ld_fecdesper,8,2);
				$ld_mesdes=substr($ld_fecdesper,5,2);
				$ld_anodes=substr($ld_fecdesper,0,4);
				$ld_diahas=substr($ld_fechasper,8,2);
				$ld_meshas=substr($ld_fechasper,5,2);
				$ld_anohas=substr($ld_fechasper,0,4);
				$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
				$as_valor=round($as_valor-($li_sabdom+$li_diafer+$li_diaper));
				break;

			case "DHABILES_CONTRATADOS": // Días Hábiles que laboro el personal contratado en el período actual
				$ld_fecculcontr=$this->personal->fecculcontr;
				$as_valor=0;
				if(substr($ld_fecculcontr,0,10)!="1900-01-01")
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
					$ld_dia=substr($_SESSION["la_nomina"]["fechasper"],8,2);
					$ld_diades=substr($ld_fecdesper,8,2);
					$ld_mesdes=substr($ld_fecdesper,5,2);
					$ld_anodes=substr($ld_fecdesper,0,4);
					$ld_diahas=substr($ld_fecculcontr,8,2);
					$ld_meshas=substr($ld_fecculcontr,5,2);
					$ld_anohas=substr($ld_fecculcontr,0,4);
					if((($ld_diahas>=$ld_diades)&&($ld_diahas<=$ld_dia))&&($ld_mesdes==$ld_meshas)&&($ld_anodes==$ld_anohas))
					{
						$li_sabdom=$this->io_sno->uf_nro_sabydom($ld_fecdesper,$ld_fecculcontr);
						$li_diafer=$this->io_feriado->uf_select_feriados($ld_fecdesper,$ld_fecculcontr,$_SESSION["la_nomina"]["codnom"],$as_codper,0);
						$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
						$as_valor=round($as_valor-($li_sabdom+$li_diafer));					
					}
				}
				break;

			case "DCALENDARIO_CONTRATADOS": // Días Hábiles que laboro el personal contratado en el período actual
				$ld_fecculcontr=$this->personal->fecculcontr;
				$as_valor=0;
				if(substr($ld_fecculcontr,0,10)!="1900-01-01")
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
					$ld_dia=substr($_SESSION["la_nomina"]["fechasper"],8,2);
					$ld_diades=substr($ld_fecdesper,8,2);
					$ld_mesdes=substr($ld_fecdesper,5,2);
					$ld_anodes=substr($ld_fecdesper,0,4);
					$ld_diahas=substr($ld_fecculcontr,8,2);
					$ld_meshas=substr($ld_fecculcontr,5,2);
					$ld_anohas=substr($ld_fecculcontr,0,4);
					if((($ld_diahas>=$ld_diades)&&($ld_diahas<=$ld_dia))&&($ld_mesdes==$ld_meshas)&&($ld_anodes==$ld_anohas))
					{
						$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
						$as_valor=round($as_valor);					
					}
				}
				break;
			
			case "CUMP_ORGV": // Si en este período cumple año en el organismo
				$ls_tipo = $_SESSION["la_nomina"]["tippernom"];
				$li_anoact = intval(substr($_SESSION["la_nomina"]["fechasper"],0,4));
				$li_mesact = intval(substr($_SESSION["la_nomina"]["fechasper"],5,2));
				$li_diaact = intval(substr($_SESSION["la_nomina"]["fechasper"],8,2));
				$li_anoper = intval(substr($this->personal->fecingper,0,4));
				$li_mesper = intval(substr($this->personal->fecingper,5,2));
				$li_diaper = intval(substr($this->personal->fecingper,8,2));
				$li_mesdes = intval(substr($_SESSION["la_nomina"]["fecdesper"],5,2));
				$li_diades = intval(substr($_SESSION["la_nomina"]["fecdesper"],8,2));
				$as_valor=0;
				if($li_anoact > $li_anoper)
				{
					if($ls_tipo==0) // es una nómina Semanal
					{
						if($li_mesdes==$li_mesact)
						{
							if($li_mesper==$li_mesact)
							{
								if(($li_diaper>=$li_diades)&&($li_diaper<=$li_diaact))
								{
									$as_valor=1;
								}
							}
						}
						else
						{
							if($li_mesper==$li_mesact)
							{
								if($li_diaper<=$li_diaact)
								{
									$as_valor=1;
								}
							}
							if($li_mesper==$li_mesdes)
							{
								if($li_diaper>=$li_diades)
								{
									$as_valor=1;
								}
							}
						}
					}
					else
					{
						if($li_mesper==$li_mesact)
						{
							if(($li_diaper>=$li_diades)&&($li_diaper<=$li_diaact))
							{
								$as_valor=1;
							}
						}
					}
				}
				break;
				
			case "CUMP_ORGV_MES": // Si en el mes cumple año en el organismo
				$ls_tipo = $_SESSION["la_nomina"]["tippernom"];
				$li_anoact = intval(substr($_SESSION["la_nomina"]["fechasper"],0,4));
				$li_mesact = intval(substr($_SESSION["la_nomina"]["fechasper"],5,2));
				$li_diaact = intval(substr($_SESSION["la_nomina"]["fechasper"],8,2));
				$li_anoper = intval(substr($this->personal->fecingper,0,4));
				$li_mesper = intval(substr($this->personal->fecingper,5,2));
				$li_diaper = intval(substr($this->personal->fecingper,8,2));
				$li_mesdes = intval(substr($_SESSION["la_nomina"]["fecdesper"],5,2));
				$li_diades = intval(substr($_SESSION["la_nomina"]["fecdesper"],8,2));
				$as_valor=0;
				if($li_anoact > $li_anoper)
				{
					if($ls_tipo==0) // es una nómina Semanal
					{
						if($li_mesdes==$li_mesact)
						{
							if($li_mesper==$li_mesact)
							{
								$as_valor=1;
							}
						}
						else
						{
							if($li_mesper==$li_mesact)
							{
								$as_valor=1;
							}
							if($li_mesper==$li_mesdes)
							{
								$as_valor=1;
							}
						}
					}
					else
					{
						if($li_mesper==$li_mesact)
						{
							$as_valor=1;
						}
					}
				}
				break;

			case "ANT_INST": // antiguedad en la institución
				$ld_fecingper=substr($this->personal->fecingper,0,4);
				$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$as_valor=$ld_fechasper-$ld_fecingper;
				$ld_fecingper=$this->personal->fecingper;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
				{
					$as_valor=$as_valor-1;
				}
				else
				{
					if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,5,2)))
					{
						if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,8,2)))
						{
							$as_valor=$as_valor-1;
						}
					}
				}
				break;

			case "ANT_ADMP": // Antiguedad en la Intitucion desde Personal + Años de Servicio 
				$li_anoprev=$this->personal->anoservpreper;
				$ld_fecingper=substr($this->personal->fecingadmpubper,0,4);
				$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$as_valor=$ld_fechasper-$ld_fecingper;
				$ld_fecingper=$this->personal->fecingadmpubper;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
				{
					$as_valor=$as_valor-1;
				}
				else
				{
					if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,5,2)))
					{
						if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,8,2)))
						{
							$as_valor=$as_valor-1;
						}
					}
				}
				$as_valor=$as_valor+$li_anoprev;
				break;
				
			case "ANT_ADMP_DIAS": // Antiguedad en la Intitucion en días 
				$ld_fecingper=$this->personal->fecingadmpubper;
				$ld_diades=substr($ld_fecingper,8,2);
				$ld_mesdes=substr($ld_fecingper,5,2);
				$ld_anodes=substr($ld_fecingper,0,4);
				$ld_fechatope=$_SESSION["la_nomina"]["fechasper"];
				$ld_diahas=substr($ld_fechatope,8,2);
				$ld_meshas=substr($ld_fechatope,5,2);
				$ld_anohas=substr($ld_fechatope,0,4);
				$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
				$as_valor=round($as_valor);
				break;
					
			case "ANT_EDU": // Devuelve la antiguedad de los educadores 
				$as_valor=0;
				$ld_fecingper=substr($this->personal->fecingper,0,4);
				$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$ai_ano=$ld_fechasper-$ld_fecingper;
				$ld_fecingper=$this->personal->fecingper;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
				{
					$ai_ano=$ai_ano-1;
				}
				else
				{
					if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,5,2)))
					{
						if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,8,2)))
						{
							$ai_ano=$ai_ano-1;
						}
					}
				}
				if(($ai_ano>=1)&&($ai_ano<=5))
				{
					$as_valor=0.05;
				}
				if(($ai_ano>=6)&&($ai_ano<=10))
				{
					$as_valor=0.10;
				}
				if(($ai_ano>=11)&&($ai_ano<=15))
				{
					$as_valor=0.15;
				}
				if(($ai_ano>=16)&&($ai_ano<=20))
				{
					$as_valor=0.20;
				}
				if(($ai_ano>=21)&&($ai_ano<=25))
				{
					$as_valor=0.25;
				}
				if($ai_ano>=26)
				{
					$as_valor=0.30;
				}
				break;
				
			case "MONTOARC": // total de los conceptos que tienen ARC 
				$as_valor=$this->personal->totalarc;
				break;
				
			case "MONTOARC_MENSUAL": // total de los conceptos que tienen ARC pero en el mes
				$ls_codper=$this->personal->codper;
				$as_valor=0;
				$arrResultado=$this->uf_obtener_montoarc_mesactual($ls_codper,$as_valor);
				$as_valor=$arrResultado['ai_totalarcant'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$as_valor+$this->personal->totalarc;
				}
				break;
				
			case "ANTIG_REINGRESO": // antiguedad em fecha de Reingreso en días
				$as_valor=0;
				$ld_fecculcontr=$this->personal->fecculcontr;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				if(substr($ld_fecculcontr,0,10)!="1900-01-01")
				{
					$ld_diades=substr($ld_fecculcontr,8,2);
					$ld_mesdes=substr($ld_fecculcontr,5,2);
					$ld_anodes=substr($ld_fecculcontr,0,4);
					$ld_diahas=substr($ld_fechasper,8,2);
					$ld_meshas=substr($ld_fechasper,5,2);
					$ld_anohas=substr($ld_fechasper,0,4);
					$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
					$as_valor=round($as_valor);
				}
				break;
				
			case "CAPITALIZA_ANT_COMP": // si Capitaliza antiguedad complementaria 1 sino 0 
				$as_valor=$this->personal->capantcom;
				switch($as_valor)
				{
					case "0": // No capitaliza
						$as_valor=1;
						break;
					case "": // No capitaliza
						$as_valor=1;
						break;
					case "1": // Si capitaliza
						$as_valor=0;
						break;
				}
				break;

			case "NEXO_CONYUGUE": // Si tiene un familiar con nexo tipo conyugue
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->io_familiar->uf_load_nexofamiliar($ls_codper,'C',$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "NEXO_HIJO": // Si tiene un familiar con nexo tipo Hijo
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->io_familiar->uf_load_nexofamiliar($ls_codper,'H',$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "NEXO_PROGENITOR": // Si tiene un familiar con nexo tipo Progenitor
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->io_familiar->uf_load_nexofamiliar($ls_codper,'P',$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "NEXO_HERMANO": // Si tiene un familiar con nexo tipo Hermano
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->io_familiar->uf_load_nexofamiliar($ls_codper,'E',$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "SEXO_CONYUGUE": // Si tiene un familiar con nexo tipo Conyugue obtiene el Sexo
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->io_familiar->uf_load_sexofamiliar($ls_codper,'C',$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "HC_CONYUGUE": // Si tiene un familiar con nexo tipo conyugue y tiene HC
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->io_familiar->uf_load_hcfamiliar($ls_codper,'C',$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "HC_HIJO": // Si tiene un familiar con nexo tipo Hijo y tiene HC
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->io_familiar->uf_load_hcfamiliar($ls_codper,'H',$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "HC_PROGENITOR": // Si tiene un familiar con nexo tipo Progenitor y tiene HC
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->io_familiar->uf_load_hcfamiliar($ls_codper,'P',$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "HC_HERMANO": // Si tiene un familiar con nexo tipo Hermano y tiene HC
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->io_familiar->uf_load_hcfamiliar($ls_codper,'E',$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "HCM_CONYUGUE": // Si tiene un familiar con nexo tipo conyugue y tiene HCM
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->io_familiar->uf_load_hcmfamiliar($ls_codper,'C',$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case substr($as_token,0,13)=="EDAD_CONYUGUE": // Número de Conyugues comprendidos en un rango de edades
				$ls_codper=$this->personal->codper;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_edaddesde=intval(substr($as_token,14,2));
				$li_edadhasta=intval(substr($as_token,17,2));
				$arrResultado=$this->io_familiar->uf_load_totalfamiliar($ls_codper,'C',$li_edaddesde,$li_edadhasta,$ld_fechasper,$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case substr($as_token,0,9)=="EDAD_HIJO": // Número de Hijos comprendidos en un rango de edades
				$ls_codper=$this->personal->codper;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_edaddesde=intval(substr($as_token,10,2));
				$li_edadhasta=intval(substr($as_token,13,2));
				$arrResultado=$this->io_familiar->uf_load_totalfamiliar($ls_codper,'H',$li_edaddesde,$li_edadhasta,$ld_fechasper,$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case substr($as_token,0,15)=="EDAD_PROGENITOR": // Número de Progenitores comprendidos en un rango de edades
				$ls_codper=$this->personal->codper;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_edaddesde=intval(substr($as_token,16,2));
				$li_edadhasta=intval(substr($as_token,19,2));
				$arrResultado=$this->io_familiar->uf_load_totalfamiliar($ls_codper,'P',$li_edaddesde,$li_edadhasta,$ld_fechasper,$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case substr($as_token,0,12)=="EDAD_HERMANO": // Número de Hermanos comprendidos en un rango de edades
				$ls_codper=$this->personal->codper;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_edaddesde=intval(substr($as_token,13,2));
				$li_edadhasta=intval(substr($as_token,16,2));
				$arrResultado=$this->io_familiar->uf_load_totalfamiliar($ls_codper,'E',$li_edaddesde,$li_edadhasta,$ld_fechasper,$as_valor);
				$as_valor=$arrResultado['ai_resultado'];
				$lb_valido=$arrResultado['lb_valido'];
				break;
					
			case substr($as_token,0,6)=="CN_ANT": // Verifica si el concepto fue cobrado por el personal los 3 períodos Anteriores
				$ls_codper=$this->personal->codper;
				$as_valor=false;
				$li_anocurnom=intval($_SESSION["la_nomina"]["anocurnom"]);
				$li_anoantnom=$li_anocurnom-1;
				$li_peractnom=intval($_SESSION["la_nomina"]["peractnom"]);
				$li_numpernom=intval($_SESSION["la_nomina"]["numpernom"]);
				switch($li_peractnom)
				{
					case 3:
						$li_numpernom=str_pad($li_numpernom,3,"0",0);
						$ls_criterio=" AND ((sno_hsalida.anocur='".$li_anocurnom."' ".
									 " AND (sno_hsalida.codperi='001' OR sno_hsalida.codperi='002')) ".
									 "	OR  (sno_hsalida.anocur='".$li_anoantnom."' AND sno_hsalida.codperi='".$li_numpernom."'))";
						break;

					case 2:
						$li_numperant=$li_numpernom-1;
						$li_numpernom=str_pad($li_numpernom,3,"0",0);
						$li_numperant=str_pad($li_numperant,3,"0",0);
						$ls_criterio=" AND ((sno_hsalida.anocur='".$li_anocurnom."' AND sno_hsalida.codperi='001') ".
									 "	OR  (sno_hsalida.anocur='".$li_anoantnom."' ".
									 " AND (sno_hsalida.codperi='".$li_numpernom."' OR sno_hsalida.codperi='".$li_numperant."')))";
						break;

					case 1:
						$li_numperant2=$li_numpernom-2;
						$li_numperant=$li_numpernom-1;
						$li_numpernom=str_pad($li_numpernom,3,"0",0);
						$li_numperant=str_pad($li_numperant,3,"0",0);
						$li_numperant2=str_pad($li_numperant2,3,"0",0);
						$ls_criterio=" AND (sno_hsalida.anocur='".$li_anoantnom."' AND (sno_hsalida.codperi='".$li_numpernom."' ".
									 "  OR sno_hsalida.codperi='".$li_numperant."' OR sno_hsalida.codperi='".$li_numperant2."'))";
						break;
						
					default:
						$li_numperant2=$li_peractnom-3;
						$li_numperant=$li_peractnom-2;
						$li_peractnom=$li_peractnom-1;
						$li_peractnom=str_pad($li_peractnom,3,"0",0);
						$li_numperant=str_pad($li_numperant,3,"0",0);
						$li_numperant2=str_pad($li_numperant2,3,"0",0);
						$ls_criterio=" AND (sno_hsalida.anocur='".$li_anocurnom."'  AND (sno_hsalida.codperi='".$li_peractnom."' ".
									 "  OR sno_hsalida.codperi='".$li_numperant."' OR sno_hsalida.codperi='".$li_numperant2."')) ";
						break;
				}
				$ls_concepto=substr($as_token,7,10);
				$ls_concepto=str_pad($ls_concepto,10,"0",0);
				$arrResultado=$this->uf_obtener_concepto_ante($ls_codper,$ls_concepto,$ls_criterio,$lb_cobrado);
				$lb_cobrado=$arrResultado['ab_cobrado'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$lb_cobrado;
				}
				break;

			case "PROFESIONAL": // Si el personal es profesional
				$as_valor=0;
				$li_nivacaper=intval($this->personal->nivacaper);
				if(($li_nivacaper==4)||($li_nivacaper==5)||($li_nivacaper==6)||($li_nivacaper==7))
				{
					$as_valor=1;
				}
				break;

			case "TSU": // Si el personal es Tecnico Superior
				$as_valor=0;
				$li_nivacaper=intval($this->personal->nivacaper);
				if($li_nivacaper==3)
				{
					$as_valor=1;
				}
				break;

			case "NIVEL_ACADEMICO": // Devuelve el Nivel academico de las persona
				$as_valor=intval($this->personal->nivacaper);
				break;

			case "DIAS_BONO_TABVAC": // Días Tabla de vacaciones según el personal
				$as_valor=0;
				$ls_codtabvac=$this->personal->codtabvac;
				$li_anoservpreper=$this->personal->anoservpreper;
				$ld_fecingper=$this->personal->fecingper;
				$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$ld_fecing=substr($ld_fecingper,0,4);
				$li_anios=$ld_fechasper-$ld_fecing;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
				{
					$li_anios=$li_anios-1;
				}
				else
				{
					if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,5,2)))
					{
						if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,8,2)))
						{
							$li_anios=$li_anios-1;
						}
					}
				}
				$arrResultado=$this->uf_obtener_dias_tabla_vacacion($ls_codtabvac,$li_anios,$li_anoservpreper,$as_valor);
				$as_valor=$arrResultado['ai_diabonvac'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "DIAS_BONO_TABVAC_MES": // Días Tabla de vacaciones según el personal
				$as_valor=0;
				$ls_codtabvac=$this->personal->codtabvac;
				$li_anoservpreper=$this->personal->anoservpreper;
				$ld_fecingper=$this->personal->fecingper;
				$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$ld_fecing=substr($ld_fecingper,0,4);
				$li_anios=$ld_fechasper-$ld_fecing;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
				{
					$li_anios=$li_anios-1;
				}
				$arrResultado=$this->uf_obtener_dias_tabla_vacacion($ls_codtabvac,$li_anios,$li_anoservpreper,$as_valor);
				$as_valor=$arrResultado['ai_diabonvac'];
				$lb_valido=$arrResultado['lb_valido'];
				break;

			case "CAJA_AHORRO": // Si el personal tiene caja de ahorro
				$as_valor=intval($this->personal->cajahoper);
				break;

			case "SUELDO_BAS_PENSION": // Sueldo Básico para los pensionados
				$as_valor=number_format((float)$this->personal->suebasper,2,".","");
				break;

			case "PRIMA_TRANSPORTE_PENSION": // Prima de Tranporte para los pensionados
				$as_valor=number_format((float)$this->personal->pritraper,2,".","");
				break;

			case "PRIMA_DESCENDENCIA_PENSION": // Prima de Tranporte para los pensionados
				$as_valor=number_format((float)$this->personal->pridesper,2,".","");
				break;

			case "PRIMA_SERVICIO_PENSION": // Prima años de servicio para los pensionados
				$as_valor=number_format((float)$this->personal->prianoserper,2,".","");
				break;

			case "PRIMA_NOASCENSO_PENSION": // Prima por no ascenso para los pensionados
				$as_valor=number_format((float)$this->personal->prinoascper,2,".","");
				break;

			case "PRIMA_ESPECIAL_PENSION": // Prima especial para los pensionados
				$as_valor=number_format((float)$this->personal->priespper,2,".","");
				break;

			case "PRIMA_PROFESIONAL_PENSION": // Prima paRa profesionales para los pensionados
				$as_valor=number_format((float)$this->personal->priproper,2,".","");
				break;

			case "SUBTOTAL_PENSION": // Sub Total de la pension para los pensionados
				$as_valor=number_format((float)$this->personal->subtotper,2,".","");
				break;

			case "PORCENTAJE_PENSION": // Porcentaje de la pension para los pensionados
				$as_valor=number_format((float)$this->personal->porpenper,2,".","");
				break;
			
		    case substr(trim ($as_token),0,9)=="DEDUCCION": // Deducciones personales	
			   	$ls_codper=$this->personal->codper; // código del personal
				$ls_codtipded=substr($as_token,10,10);  // código del tipo de deducción	
				$ls_codtipded=str_pad($ls_codtipded,10,"0",0);
				$ls_sueldo=$this->personal->sueper; // Sueldo del personal					
				// ---------------------------- Para calcular la edad de la persona-----------------------------------------
				$ld_fechas=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$ld_fecnacper=substr($this->personal->fecnacper,0,4);
				$ls_edad=$ld_fechas-$ld_fecnacper;
				$ld_fechas=$_SESSION["la_nomina"]["fechasper"];
				$ld_fecnacper=$this->personal->fecnacper;
				if(intval(substr($ld_fechas,5,2))<intval(substr($ld_fecnacper,5,2)))
				{
					$ls_edad=$ls_edad-1;
				}
				else
				{
					if(intval(substr($ld_fechas,5,2))==intval(substr($ld_fecnacper,5,2)))
					{
						if(intval(substr($ld_fechas,8,2))<intval(substr($ld_fecnacper,8,2)))
						{
							$ls_edad=$ls_edad-1;
						}
					}
				}	
				///----------------------fin del calculo de la edad de la persona----------------------------------------------			
				$ls_sexo="'".$this->personal->sexper."'"; // Sexo de la persona
				
				$lb_valido=$this->io_tipodeduccion->uf_srh_buscar_deduccion($ls_codper,$ls_codtipded,'1', $ls_sueldo, $ls_edad,
				                                                            $ls_sexo,$as_valor);
				if ($as_valor=="")
				{
					$as_valor=0;
				}
																			
				break;
				
				case substr(trim ($as_token),0,14)=="DEDUC_FAMILIAR": // Deducciones de los familiares
					$ls_codper=$this->personal->codper; // código del personal
					$ls_codtipded=substr($as_token,15,10); // código del tipo de deducción						
					$ls_codtipded=str_pad($ls_codtipded,10,"0",0);												
					$ls_sueldo=$this->personal->sueper; // Sueldo del personal
					$ld_fecha_hasta=$_SESSION["la_nomina"]["fechasper"]; /// fecha hasta del lapso de la nomina
					$lb_valido=$this->io_tipodeduccion->uf_srh_buscar_deduccion_familiar($ls_codper,$ls_codtipded,'1',$ls_sueldo,
																						 $ld_fecha_hasta, $as_valor);
					if ($as_valor=="")
					{
						$as_valor=0;
					}
				break;
				
			   case substr(trim ($as_token),0,16)=="PATRON_DEDUCCION": // Aporte del Patrono para las deducciones personales
			        $ls_codper=$this->personal->codper; // código del personal
					$ls_codtipded=substr($as_token,17,10); // código del tipo de deducción	
					$ls_codtipded=str_pad($ls_codtipded,10,"0",0);															
					$ls_sueldo=$this->personal->sueper; // Sueldo del personal
					// ---------------------------- Para calcular la edad de la persona-----------------------------------
					$ld_fechas=substr($_SESSION["la_nomina"]["fechasper"],0,4);
					$ld_fecnacper=substr($this->personal->fecnacper,0,4);
					$ls_edad=$ld_fechas-$ld_fecnacper;
					$ld_fechas=$_SESSION["la_nomina"]["fechasper"];
					$ld_fecnacper=$this->personal->fecnacper;
					if(intval(substr($ld_fechas,5,2))<intval(substr($ld_fecnacper,5,2)))
					{
						$ls_edad=$ls_edad-1;
					}
					else
					{
						if(intval(substr($ld_fechas,5,2))==intval(substr($ld_fecnacper,5,2)))
						{
							if(intval(substr($ld_fechas,8,2))<intval(substr($ld_fecnacper,8,2)))
							{
								$ls_edad=$ls_edad-1;
							}
						}
					}
					// ------------------------------fin del calculo de la persona-----------------------------------------
					$ls_sexo="'".$this->personal->sexper."'"; // Sexo de la persona
				
					$lb_valido=$this->io_tipodeduccion->uf_srh_buscar_deduccion($ls_codper,$ls_codtipded,'2',$ls_sueldo, $ls_edad,
				                                                            $ls_sexo, $as_valor);	
					if ($as_valor=="")
					{
						$as_valor=0;
					}			
				break;
							
			case substr(trim($as_token),0,21)=="DEDUC_PATRON_FAMILIAR": //Aporte del Patrono para las deducciones de los familiares
			    
				$ls_codper=$this->personal->codper; // código del personal
				$ls_codtipded=substr($as_token,22,10); // código del tipo de deducción
				$ls_codtipded=str_pad($ls_codtipded,10,"0",0);											
				$ls_sueldo=$this->personal->sueper; // Sueldo del personal
				$ld_fecha_hasta=$_SESSION["la_nomina"]["fechasper"]; /// fecha hasta del lapso de la nomina
				$lb_valido=$this->io_tipodeduccion->uf_srh_buscar_deduccion_familiar($ls_codper,$ls_codtipded,'2',$ls_sueldo,
																					 $ld_fecha_hasta, $as_valor);
				if ($as_valor=="")
				{
					$as_valor=0;
				}				
				break;

			case substr($as_token,0,9)=="MONTO_ANT": // Monto total de conceptos anteriores
				$ls_tipsal=substr($as_token,10,2);
				$li_meses=substr($as_token,13,2);
				$ls_codper=$this->personal->codper;
				$as_valor=0;
				$li_monto=0;
				$arrResultado=$this->uf_obtener_montoanterior($ls_codper,$ls_tipsal,$li_meses,$li_monto);
				$li_monto=$arrResultado['ai_monto'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;
				
			case "DEDICACION": // Código de la dedicación del personal
				$as_valor=$this->personal->codded;
				break;
            
			case "TIP_PERSONAL": // Código del tipo de personal
				$as_valor=$this->personal->codtipper;
				break;
            
			case "CLASIFICACION_DOC": // Código de la dedicación del personal
				$as_valor=$this->personal->codcladoc;
				break;
            
			case "ESCALA_DOC": // Código del tipo de personal
				$as_valor=$this->personal->codescdoc;
				break;	 

			case "NRO_LUNES": // Número de lunes que tiene el período			
				$ld_fecdes=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechas=$_SESSION["la_nomina"]["fechasper"];
				$ls_fechaI=$this->personal->fecingper;
				$ls_valido=$this->io_fecha->uf_comparar_fecha($ls_fechaI,$ld_fecdes);
				if ($ls_valido!=true)
				{
				 	$ld_fecdes=$ls_fechaI;
				}
				$as_valor=$this->io_sno->uf_nro_lunes($ld_fecdes,$ld_fechas); 
				break;

			case "NRO_LUNESMES": // Número de lunes que tiene el mes
				$ld_fecdes=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechas=$_SESSION["la_nomina"]["fechasper"];
				$ls_fechaI=$this->personal->fecingper;
				$ls_valido=$this->io_fecha->uf_comparar_fecha($ls_fechaI,$ld_fecdes);
				if ($ls_valido==true)
				{
				  	$ls_fecha=substr($ld_fecdes,0,8)."01";
					$ls_valido2=$this->io_fecha->uf_comparar_fecha($ls_fecha,$ls_fechaI);
					if ($ls_valido2==true)
					{
					  	$ld_desde=substr($ls_fechaI,0,8).substr($ls_fechaI,8,10);				
					}
					else
					{
						$ld_desde=substr($ld_fecdes,0,8)."01";
					}
				}
				else
				{
					$ld_fecdes=$ls_fechaI;
				    $ld_desde=substr($ld_fecdes,0,8).substr($ld_fecdes,8,10);
					 
				}
				$ld_diahasta=strftime("%d",mktime(0,0,0,(substr($ld_fecdes,5,2)+1),0,substr($ld_fecdes,0,4)));
				 
				$ld_hasta=substr($ld_fecdes,0,8).$ld_diahasta;
				$as_valor=$this->io_sno->uf_nro_lunes($ld_desde,$ld_hasta);
				break;

			case "SUELDO_SOBREVIVIENTE": // Sueldo del Sobreviviente
			    $ls_cedper=$this->personal->cedper;
				$as_valor=$this->io_beneficiario->uf_select_sueldo_beneficiario($ls_cedper);
				break;

			case "PORC_CAJA_AHORRO": // Porcentaje de Ahorro de la Caja de Ahorro
				$li_porc=$this->personal->porcajahoper/100;
				$as_valor=$li_porc;
				break;

			case "FIN_ENCARGADO": // Indica si la persona finaliza el rol de encargado en el periodo actual de la nomina
			    $ld_fecdes=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechas=$_SESSION["la_nomina"]["fechasper"];
				$ls_codper=$this->personal->codper; // código del personal
				$as_valor=$this->io_encargaduria->uf_verficar_encargado($ls_codper,$ld_fecdes,$ld_fechas);				
				break;	

			case "FIN_ENCARGADURIA": // Indica si la persona finaliza una encargaduría en el periodo actual de la nomina
				$ld_fecdes=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechas=$_SESSION["la_nomina"]["fechasper"];
				$ls_codper=$this->personal->codper; // código del personal
				$as_valor=$this->io_encargaduria->uf_verficar_encargaduria($ls_codper,$ld_fecdes,$ld_fechas);
				break;	

			case substr(trim($as_token),0,23)=="DIAS_TOTAL_ENCARGADURIA": // Número de días totales de la Encargaduría
			     $ls_codenc=substr($as_token,24,10); // código de la encargaduría	
				 $ls_codenc=str_pad($ls_codenc,10,"0",0);
				 $ls_codnomenc=substr($as_token,35,4); // código de nómina de la encargaduría	
				 $ls_codnomenc=str_pad($ls_codnomenc,4,"0",0);	
				 $ls_dias=0;
				 $arrResultado=$this->io_encargaduria->uf_calcular_dias_encargaduria($ls_codenc,$ls_codnomenc,$ls_dias);
				 $ls_dias=$arrResultado['as_dias'];
				 $lb_valido=$arrResultado['lb_valido'];
				 if (!$lb_valido)
				 {
				 	$this->io_mensajes->message("ERROR->ENCARGADURIA ".$ls_codenc."  NO TIENE FECHA DE FINALIZACIÓN.");
				 }
				 $as_valor=$ls_dias;
				break;

			case substr(trim($as_token),0,21)=="DIF_DIAS_ENCARGADURIA": // Diferencia de Días entre la fecha final
			                                                             // de la Encargaduría y la fecha final del periodo 
				 $ls_codenc=substr($as_token,22,10); // código de la encargaduría	
				 $ls_codenc=str_pad($ls_codenc,10,"0",0);
				 $ls_codnomenc=substr($as_token,34,4); // código de nómina de la encargaduría	
				 $ls_codnomenc=str_pad($ls_codnomenc,4,"0",0);					
				 $ld_fechas=$_SESSION["la_nomina"]["fechasper"];	
				 $ls_dias=0;										 
				 $arrResultado=$this->io_encargaduria->uf_calcular_diferencia_dias_encargaduria($ls_codenc,$ls_codnomenc,$ld_fechas,$ls_dias);
				 $ls_dias=$arrResultado['as_dias'];
				 $lb_valido=$arrResultado['lb_valido'];
				 if (!$lb_valido)
				 {
				 	$this->io_mensajes->message("ERROR->ENCARGADURIA ".$ls_codenc."  NO TIENE FECHA DE FINALIZACIÓN.");
				 }
				 $as_valor=$ls_dias;
				break;
				
			case "SOBREVIVIENTE": // Indica el tipo de pension del personal
				  $ls_tipjub=$this->personal->tipjub;
				  if($ls_tipjub=="1")
				  {
					$as_valor=1;	
				  }
                  else
				  {
					$as_valor=0;	
				  }				  
				break;

			case "PORCENTAJE_BENEFICIARIO": // Porcentaje del Beneficiario
			        $ls_codper=$this->personal->codper;
					$as_valor=$this->io_beneficiario->uf_select_porcentaje_beneficiario($ls_codper,"P");
					$as_valor=number_format((float)$as_valor,2,".","");
				break;

			case "MONTO_BENEFICIARIO": // Montos del Beneficiario
			        $ls_codper=$this->personal->codper;
					$as_valor=$this->io_beneficiario->uf_select_porcentaje_beneficiario($ls_codper,"M");
					$as_valor=number_format((float)$as_valor,2,".","");
				break;

			case "DIAS_LABORADO_NOM": // Número de días laborados por la persona desde que seincluyo en la nomina
				$ld_fechatope=$this->io_funciones->uf_convertirdatetobd($this->io_sno->uf_select_config("SNO","ANTIGUEDAD","FECHA_TOPE","1900-01-01","C"));
				$ld_fecingnom=$this->personal->fecingnom;
				$ld_diades=substr($ld_fecingnom,8,2);
				$ld_mesdes=substr($ld_fecingnom,5,2);
				$ld_anodes=substr($ld_fecingnom,0,4);
				if(($ld_fechatope!="1900-01-01")&&($ld_fechatope!=""))
				{
					$ld_diahas=substr($ld_fechatope,8,2);
					$ld_meshas=substr($ld_fechatope,5,2);
					$ld_anohas=substr($ld_fechatope,0,4);
				}
				else
				{
					$ld_fechatope=$_SESSION["la_nomina"]["fechasper"];
					$ld_diahas=substr($ld_fechatope,8,2);
					$ld_meshas=substr($ld_fechatope,5,2);
					$ld_anohas=substr($ld_fechatope,0,4);
				}
				$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
				$as_valor=round($as_valor);
				break;

			case "MESES_LABORADO_NOM": // Número de meses laborados por la persona desde que se incluyo en la nomina
				$ld_fecingnom=$this->personal->fecingnom;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$ld_diahas = substr($ld_fechasper, 8, 2);  
				$ld_meshas = substr($ld_fechasper, 5, 2);  
				$ld_anohas = substr($ld_fechasper, 0, 4); 
				$ld_diades = substr($ld_fecingnom, 8, 2);  
				$ld_mesdes = substr($ld_fecingnom, 5, 2);  
				$ld_anodes = substr($ld_fecingnom, 0, 4);  
				$b = 0;  
				$mes = $ld_mesdes-1; 				  
				if($mes==2)
				{  
					if(($ld_anohas%4==0 && $ld_anohas%100!=0) || $ld_anohas%400==0)
					{  
						$b = 29;  
					}
					else
					{  
						$b = 28;  
					}  
				}  
				else if($mes<=7)
				{  
					if($mes==0)
					{  
						$b = 31;  
					}  
					else if($mes%2==0)
					{  
						$b = 30;  
					}  
				   else
				   {  
						$b = 31;  
				   }  
				}  
				else if($mes>7)
				{  
				   if($mes%2==0)
				   {  
						$b = 31;  
				   }  
				   else
				   {  
						$b = 30;  
				   }  
				}  
				if($ld_mesdes <= $ld_meshas)
				{  
				   $anios = $ld_anohas - $ld_anodes;  
				   if($ld_diades <= $ld_diahas)
				   {  
						$meses = $ld_meshas - $ld_mesdes;  
						$dies = $ld_diahas - $ld_diades;  
				   }
				   else
				   {  
						if($ld_meshas == $ld_mesdes)
						{  
							$anios = $anios - 1;  
						}  
						$meses = ($ld_meshas - $ld_mesdes - 1 + 12) % 12;  
						$dies = $b-($ld_diades-$ld_diahas);  
				   }  
				}
				else
				{  
				   $anios = $ld_anohas - $ld_anodes - 1;  
				   if($ld_diades > $ld_diahas)
				   {  
						$meses = $ld_meshas - $ld_mesdes -1 +12;  
						$dies = $b - ($ld_diades-$ld_diahas);  
				   }
				   else
				   {  
						$meses = $ld_meshas - $ld_mesdes + 12;  
						$dies = $ld_diahas - $ld_diades;  
				   }  
				}
				 $total_mes=($anios*12)+$meses+($dies/30);
				 $as_valor=round($total_mes,2);
				break;

			case "ANT_NOMINA": // antiguedad en la NOMINA
			    $ld_fecingnom=$this->personal->fecingnom;// fecha de ingreso en la institucióm
				$ld_fecingnom=substr($ld_fecingnom,0,4);
				$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$as_valor=$ld_fechasper-$ld_fecingnom;
				$ld_fecingnom=$this->personal->fecingnom;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingnom,5,2)))
				{
					$as_valor=$as_valor-1;
				}
				else
				{
					if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingnom,5,2)))
					{
						if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingnom,8,2)))
						{
							$as_valor=$as_valor-1;
						}
					}
				}
				break;
			
			case substr(trim($as_token),0,21)=="SUELDO_PROM_HISTORICO": // Monto del sueldo promedio			
			     $ls_nromeses=intval(substr($as_token,22,2)); // código del concepto
				 $ls_codper=$this->personal->codper;
				 $ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				 $ld_meshas = substr($ld_fechasper, 5, 2);  
				 $ld_anohas = substr($ld_fechasper, 0, 4); 
				 $as_valor=$this->uf_obtener_sueldopromedioanterior($ls_codper,$ls_nromeses,$ld_meshas,$ld_anohas);	
				 break;

			case substr(trim($as_token),0,10)=="MONTO_CONC": // Monto del Concepto en un periodo			
			     $ls_codconc=substr($as_token,11,10); // código del concepto
				 $ls_codconc=str_pad($ls_codconc,10,"0",0);
				 $ls_codperi=substr($as_token,23,3); // código del periodo
				 $ls_codperi=str_pad($ls_codperi,3,"0",0);				 
				 $ls_codper=$this->personal->codper;
				 $li_anocurnom=intval($_SESSION["la_nomina"]["anocurnom"]);				 								
				 $as_valor=$this->io_concepto->uf_buscar_valor_periodo($ls_codconc,$ls_codperi,$ls_codper,$li_anocurnom);	
				 break;

			case substr($as_token,0,14)=="ACU_MONTO_CONC": // Monto Acumulado del Concepto en un numero de periodos
				$ls_codconc=substr($as_token,15,10); // código del concepto
				$ls_codconc=str_pad($ls_codconc,10,"0",0);
				$li_numper=substr($as_token,26,2);
				$li_numper=intval($li_numper);
				$ls_codper=$this->personal->codper;
				$li_anocurnom=intval($_SESSION["la_nomina"]["anocurnom"]);
				$li_anoantnom=$li_anocurnom-1;
				$li_peractnom=intval($_SESSION["la_nomina"]["peractnom"]);
				$li_numpernom=intval($_SESSION["la_nomina"]["numpernom"]);
				if ((intval($li_peractnom)-$li_numper)>0)
				{
					$ls_codperides=intval($li_peractnom)-$li_numper;
					$ls_codperides=str_pad($ls_codperides,3,"0",0);
					$ls_codperihas=intval($li_peractnom)-1;
					$ls_codperihas=str_pad($ls_codperihas,3,"0",0);
					$ls_criterio=" AND sno_hsalida.anocur='".$li_anocurnom."' ".
								 " AND sno_hsalida.codperi BETWEEN '".$ls_codperides."' AND '".$ls_codperihas."' ";
					
				}
				else
				{
					$ls_codperihas1=intval($li_peractnom)-1;
					$ls_codperihas1=str_pad($ls_codperihas1,3,"0",0);
					$li_codperides1=$li_numpernom - ($li_numper-intval($li_peractnom));
					$li_codperides1=str_pad($li_codperides1,3,"0",0);
					$li_codperides2=str_pad($li_numpernom,3,"0",0);
					$ls_criterio=" AND ((sno_hsalida.anocur='".$li_anocurnom."' ".
								 " AND  sno_hsalida.codperi BETWEEN '001' AND '".$ls_codperihas1."') ".
								 "	OR (sno_hsalida.anocur='".$li_anoantnom."' ".
								 " AND  sno_hsalida.codperi BETWEEN '".$li_codperides1."' AND '".$li_codperides2."'))";
				}					
				$arrResultado=$this->io_concepto->uf_buscar_valor_acumulado_periodo($ls_codper,$ls_codconc,$ls_criterio,$ld_monto,'');
				$ld_monto=$arrResultado['as_monto'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_monto===0)//Modificado por Carlos Zambrano
				{
					$lb_valido=true;
				}//Modificado por Carlos Zambrano
				if($lb_valido)
				{
					$as_valor=$ld_monto;
				}
				break;

			case substr($as_token,0,18)=="NOM_ACU_MONTO_CONC": // Monto Acumulado del Concepto en un numero de periodos
				$ls_codnom=substr($as_token,19,4); // código del concepto
				$ls_codnom=str_pad($ls_codnom,4,"0",0);
				$ls_codconc=substr($as_token,24,10); // código del concepto
				$ls_codconc=str_pad($ls_codconc,10,"0",0);
				$li_numper=substr($as_token,35,2);
				$li_numper=intval($li_numper);
				$ls_codper=$this->personal->codper;
				$li_anocurnom=intval($_SESSION["la_nomina"]["anocurnom"]);
				$li_anoantnom=$li_anocurnom-1;
				$li_peractnom=intval($_SESSION["la_nomina"]["peractnom"]);
				$li_numpernom=intval($_SESSION["la_nomina"]["numpernom"]);
				if ((intval($li_peractnom)-$li_numper)>0)
				{
					$ls_codperides=intval($li_peractnom)-$li_numper;
					$ls_codperides=str_pad($ls_codperides,3,"0",0);
					$ls_codperihas=intval($li_peractnom)-1;
					$ls_codperihas=str_pad($ls_codperihas,3,"0",0);
					$ls_criterio=" AND sno_hsalida.anocur='".$li_anocurnom."' ".
								 " AND sno_hsalida.codnom='".$ls_codnom."' ".
								 " AND sno_hsalida.codperi BETWEEN '".$ls_codperides."' AND '".$ls_codperihas."' ";
					
				}
				else
				{
					$ls_codperihas1=intval($li_peractnom)-1;
					$ls_codperihas1=str_pad($ls_codperihas1,3,"0",0);
					$li_codperides1=$li_numpernom - ($li_numper-intval($li_peractnom));
					$li_codperides1=str_pad($li_codperides1,3,"0",0);
					$li_codperides2=str_pad($li_numpernom,3,"0",0);
					$ls_criterio=" AND sno_hsalida.codnom='".$ls_codnom."' ".
								 " AND ((sno_hsalida.anocur='".$li_anocurnom."' ".
								 " AND  sno_hsalida.codperi BETWEEN '001' AND '".$ls_codperihas1."') ".
								 "	OR (sno_hsalida.anocur='".$li_anoantnom."' ".
								 " AND  sno_hsalida.codperi BETWEEN '".$li_codperides1."' AND '".$li_codperides2."'))";
				}	
				$arrResultado=$this->io_concepto->uf_buscar_valor_acumulado_periodo($ls_codper,$ls_codconc,$ls_criterio,$ld_monto,'');
				$ld_monto=$arrResultado['as_monto'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_monto===0)//Modificado por Carlos Zambrano
				{
					$lb_valido=true;
				}//Modificado por Carlos Zambrano
				if($lb_valido)
				{
					$as_valor=$ld_monto;
				}
				break;

			case "CATEGORIA_MILITAR": // Monto de la prima docente de Hogar
				$ls_codper=$this->personal->codper;
				$as_valor="";
				$as_categoria='';
				$arrResultado=$this->uf_obtener_categoria_militar($ls_codper,$as_categoria);
				$as_categoria=$arrResultado['as_categoria'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor="".$as_categoria."";
				}
				break;
				
			case "ES_PERSONAL": // DEVUELVE TRUE SI LA PERSONA ES PERSONAL
				$ls_codper=$this->personal->codper;
				$ls_cedper=$this->personal->cedper;
				$as_valor=0;
				$ab_pensionado=0;
				$arrResultado=$this->uf_verificar_personal($ls_codper,$ls_cedper,$ab_pensionado);
				$ab_pensionado=$arrResultado['as_valor'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ab_pensionado;
				}
				break;
				
			case "ES_BENEFICIARIO": // DEVUELVE TRUE SI LA PERSONA ES BENEFICIARIO
				$ls_codper=$this->personal->codper;
				$ls_cedper=$this->personal->cedper;
				$as_valor=0;
				$ab_pensionado=0;
				$arrResultado=$this->uf_verificar_beneficiario($ls_codper,$ls_cedper,$ab_pensionado);
				$ab_pensionado=$arrResultado['as_valor'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ab_pensionado;
				}
				break;
				
			case "NETO_PRESTACION_ANTIGUEDAD": // Monto Neto de la prestación antiguedad Acumulada 
				$as_valor=0;
				$li_monto=0;
				$arrResultado=$this->uf_obtener_prestacionantiguedad($this->personal->codper,"0",$li_monto);
				$li_monto=$arrResultado['ai_monto'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;
				
			case "NETO_INTERESES_PRESTACION": // Monto Neto de los intereses de prestación antiguedad Acumulada 
				$as_valor=0;
				$li_monto=0;
				$arrResultado=$this->uf_obtener_interesesprestacion($this->personal->codper,"0",$li_monto);
				$li_monto=$arrResultado['ai_monto'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;
				
			case "PRESTACION_ANTIGUEDAD": // Monto Acumulado de la prestación antiguedad
				$as_valor=0;
				$li_monto=0;
				$arrResultado=$this->uf_obtener_prestacionantiguedad($this->personal->codper,"1",$li_monto);
				$li_monto=$arrResultado['ai_monto'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;
				
			case "INTERESES_PRESTACION": // Monto Acumulado de los intereses de prestación antiguedad
				$as_valor=0;
				$li_monto=0;
				$arrResultado=$this->uf_obtener_interesesprestacion($this->personal->codper,"1",$li_monto);
				$li_monto=$arrResultado['ai_monto'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;
		
			case "ANTICIPOS_PRESTACION": // Monto de los Anticipos de la prestación antiguedad
				$as_valor=0;
				$li_monto=0;
				$arrResultado=$this->uf_obtener_prestacionantiguedad($this->personal->codper,"2",$li_monto);
				$li_monto=$arrResultado['ai_monto'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;
				
			case "ANTICIPOS_INTERESES": // Monto de los Anticipos de los intereses
				$as_valor=0;
				$li_monto=0;
				$arrResultado=$this->uf_obtener_interesesprestacion($this->personal->codper,"2",$li_monto);
				$li_monto=$arrResultado['ai_monto'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;

			case "FERIADO_NACIONAL": // Numero de Días Feriados nacionales
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$as_valor=$this->io_feriado->uf_select_feriados($ld_fecdesper,$ld_fechasper,$_SESSION["la_nomina"]["codnom"],$as_codper,1);
				$as_valor=round($as_valor,0);
				break;

			case "FERIADO_REGIONAL": // Numero de Días Feriados regionales
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$as_valor=$this->io_feriado->uf_select_feriados($ld_fecdesper,$ld_fechasper,$_SESSION["la_nomina"]["codnom"],$as_codper,2);
				$as_valor=round($as_valor,0);
				break;

			case "PERMISO_RESTUDIO": // Numero de Días de permiso remunerado por estudio.Dias corridos
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',1,1);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_ESTUDIO": // Numero de Días de permiso no remunerado por estudio. Días hábiles
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',1,0);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,21)=="PERMISO_ACUM_RESTUDIO": // Numero de Días de permiso remunerado por estudio.Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,22,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',1,1);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,20)=="PERMISO_ACUM_ESTUDIO": // Numero de Días de permiso NO remunerado por estudio.Dias habiles, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,21,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',1,0);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_RMEDICO": // Numero de Días de permiso remunerado por medico.
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',2,1);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_MEDICO": // Numero de Días de permiso no remunerado por medico.
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',2,0);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,20)=="PERMISO_ACUM_RMEDICO": // Numero de Días de permiso remunerado por medico.Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,21,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',2,1);
				$as_valor=round($li_diaper);
				break;


			case substr($as_token,0,19)=="PERMISO_ACUM_MEDICO": // Numero de Días de permiso NO remunerado por medico.Dias habiles, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,20,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',2,0);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_RTRAMITES": // Numero de Días de permiso remunerado por tramites.
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',3,1);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_TRAMITES": // Numero de Días de permiso no remunerado por tramites.
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',3,0);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,22)=="PERMISO_ACUM_RTRAMITES": // Numero de Días de permiso remunerado por tramites.Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,23,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',3,1);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,21)=="PERMISO_ACUM_TRAMITES": // Numero de Días de permiso NO remunerado por tramites.Dias corridos, acumulados segun los periodos
                                $li_nroperiodos =substr($as_token,22,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',3,0);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_ROTROS": // Numero de Días de permiso remunerado por otros.
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',4,1);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_OTROS": // Numero de Días de permiso no remunerado por otros.
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',4,0);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,19)=="PERMISO_ACUM_ROTROS": // Numero de Días de permiso remunerado por OTROS.Dias corridos, acumulados segun los periodos
                                $li_nroperiodos =substr($as_token,20,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',4,1);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,18)=="PERMISO_ACUM_OTROS": // Numero de Días de permiso NO remunerado por OTROS.Dias corridos, acumulados segun los periodos
                                $li_nroperiodos =substr($as_token,19,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',4,0);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,19)=="PERMISOM_ACUM_OTROS": // Numero de Días de permiso NO remunerado por ausencia.Dias corridos, acumulados segun los MESES
                                $li_nromeses =substr($as_token,20,2);
				$as_aniodes = substr($_SESSION["la_nomina"]["fecdesper"],0,4);
				$as_aniohas = substr($_SESSION["la_nomina"]["fecdesper"],0,4);
				$as_mesdes = str_pad(intval(substr($_SESSION["la_nomina"]["fecdesper"],5,2)-$li_nromeses),2,'0',0);
				$as_meshas = str_pad(intval(substr($_SESSION["la_nomina"]["fecdesper"],5,2)-1),2,'0',0);
                                if ($as_mesdes<1)
				{
					$as_mesdes = str_pad(intval(12+($as_mesdes)),2,'0',0);
					$as_aniodes = $as_aniodes-1;
				}				
                                if ($as_meshas<1)
				{
					$as_meshas = str_pad(intval(12+($as_meshas)),2,'0',0);
					$as_aniohas = $as_aniohas-1;
				}				
				$ld_fechadesde =$as_aniodes."-".$as_mesdes."-01";
				$ls_last_day=28; 
				while (checkdate($as_meshas,$ls_last_day + 1,$as_aniohas))
				{ 
				   $ls_last_day++; 
				} 
				$ld_fechahasta =$as_aniohas."-".$as_meshas."-".$ls_last_day;
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fechadesde,$ld_fechahasta,'0',4,0);
				$as_valor=round($li_diaper);
				break;


			case "PERMISO_RMCREPOSO": // Numero de Días de permiso remunerado, mensual y dias continuos por reposo.
				$ld_fecdesper=substr($_SESSION["la_nomina"]["fecdesper"],0,8)."01";
				$anio=intval(substr($_SESSION["la_nomina"]["fecdesper"],0,4));
				$mes=intval(substr($_SESSION["la_nomina"]["fecdesper"],5,2));
				$dia=28; 
				$as_valor=false;
				while (checkdate($mes,$dia + 1,$anio))
				{ 
				   $dia++; 
				} 
				$ld_fechasper=substr($_SESSION["la_nomina"]["fecdesper"],0,8).$dia;				
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',5,'1');
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_RREPOSO": // Numero de Días de permiso remunerado por reposo.
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',5,'1');
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_REPOSO": // Numero de Días de permiso no remunerado por reposo.
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',5,'0');
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,20)=="PERMISO_ACUM_RREPOSO": // Numero de Días de permiso remunerado por reposo.Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,21,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',5,1);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,19)=="PERMISO_ACUM_REPOSO": // Numero de Días de permiso remunerado por reposo.Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,20,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',5,0);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_RREPOSOLABORAL": // Numero de Días de permiso remunerado por reposo laboral.
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',6,1);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_REPOSOLABORAL": // Numero de Días de permiso no remunerado por reposo laboral.
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',6,0);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,27)=="PERMISO_ACUM_RREPOSOLABORAL": // Numero de Días de permiso remunerado por reposo laboral .Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,28,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',6,1);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,26)=="PERMISO_ACUM_REPOSOLABORAL": // Numero de Días de permiso no remunerado por reposo laboral .Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,25,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',6,0);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_RAUSENCIA": //Numero de Días de permiso remunerado por Permiso Ausencia
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',7,1);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_AUSENCIA": //Numero de Días de permiso NO remunerado por Permiso Ausencia
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',7,0);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,22)=="PERMISO_ACUM_RAUSENCIA": // Numero de Días de permiso remunerado por ausencia.Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,23,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',7,1);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,21)=="PERMISO_ACUM_AUSENCIA": // Numero de Días de permiso NO remunerado por ausencia.Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,22,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',7,0);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,22)=="PERMISOM_ACUM_AUSENCIA": // Numero de Días de permiso NO remunerado por ausencia.Dias corridos, acumulados segun los MESES
			    $li_nromeses =substr($as_token,23,2);
				$as_aniodes = substr($_SESSION["la_nomina"]["fecdesper"],0,4);
				$as_aniohas = substr($_SESSION["la_nomina"]["fecdesper"],0,4);
				$as_mesdes = str_pad(intval(substr($_SESSION["la_nomina"]["fecdesper"],5,2)-$li_nromeses),2,'0',0);
				$as_meshas = str_pad(intval(substr($_SESSION["la_nomina"]["fecdesper"],5,2)-1),2,'0',0);
                                if ($as_mesdes<1)
				{
					$as_mesdes = str_pad(intval(12+($as_mesdes)),2,'0',0);
					$as_aniodes = $as_aniodes-1;
				}				
                                if ($as_meshas<1)
				{
					$as_meshas = str_pad(intval(12+($as_meshas)),2,'0',0);
					$as_aniohas = $as_aniohas-1;
				}				
				$ld_fechadesde =$as_aniodes."-".$as_mesdes."-01";
				$ls_last_day=28; 
				while (checkdate($as_meshas,$ls_last_day + 1,$as_aniohas))
				{ 
				   $ls_last_day++; 
				} 
				$ld_fechahasta =$as_aniohas."-".$as_meshas."-".$ls_last_day;
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fechadesde,$ld_fechahasta,'0',7,0);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_RSINDICAL": //Numero de Días de permiso remunerado por Permiso sindical
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',8,1);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_SINDICAL": //Numero de Días de permiso NO remunerado por Permiso sindical
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',8,0);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,22)=="PERMISO_ACUM_RSINDICAL": // Numero de Días de permiso remunerado por sindical.Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,23,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',8,1);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,21)=="PERMISO_ACUM_SINDICAL": // Numero de Días de permiso remunerado por sindical.Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,20,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',8,0);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_RCOMPENSATORIO": //Numero de Días de permiso remunerado por Permiso compensatorio
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',9,1);
				$as_valor=round($li_diaper);
				break;

			case "PERMISO_RCOMPENSATORIO": //Numero de Días de permiso NO remunerado por Permiso compensatorio
				$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',9,0);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,27)=="PERMISO_ACUM_RCOMPENSATORIO": // Numero de Días de permiso remunerado compensatorio.Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,26,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'1',9,1);
				$as_valor=round($li_diaper);
				break;

			case substr($as_token,0,26)=="PERMISO_ACUM_COMPENSATORIO": // Numero de Días de permiso remunerado compensatorio.Dias corridos, acumulados segun los periodos
			    $li_nroperiodos =substr($as_token,25,2);
				$as_codperi = str_pad(intval($_SESSION["la_nomina"]["peractnom"] - $li_nroperiodos + 1),3,'0',0);
				$ld_fechadesde ='';
				$ld_fechahasta ='';
				$arrResultado=$this->uf_fechaperiodo($as_codperi,$ld_fechadesde,$ld_fechahasta);
				$ld_fechadesde =$arrResultado['ad_fechadesde'];
				$ld_fechahasta =$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_fechadesde ==='1900-01-01')
				{
					$ld_fecdesper=$_SESSION["la_nomina"]["fecdesper"];				
				}
				else
				{
					$ld_fecdesper= $ld_fechadesde;
				}
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$li_diaper=$this->io_permiso->uf_select_diaspermisos($as_codper,$ld_fecdesper,$ld_fechasper,'0',9,0);
				$as_valor=round($li_diaper);
				break;

			case "HT_HORASLABORADAS": // Numero de Horas laboradas registradas en la hoja de tiempo
				$as_valor=0;
				$li_monto=0;
				$arrResultado=$this->io_hojatiempo->uf_select_hojatiempo($as_codper," SUM(sno_hojatiempo.horlab) AS total ","",$li_monto);
				$li_monto=$arrResultado['ai_total'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;

			case "HT_HORASEXTRASLABORADAS": // Numero de Horas Extras laboradas registradas en la hoja de tiempo
				$as_valor=0;
				$li_monto=0;
				$arrResultado=$this->io_hojatiempo->uf_select_hojatiempo($as_codper," SUM(horextlab) AS total ","",$li_monto);
				$li_monto=$arrResultado['ai_total'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;

			case "HT_DIASLABORADOS": // Numero de Días laborados registrados en la hoja de tiempo
				$as_valor=0;
				$li_monto=0;
				$arrResultado=$this->io_hojatiempo->uf_select_hojatiempo($as_codper," COUNT(fechojtie) AS total ","",$li_monto);
				$li_monto=$arrResultado['ai_total'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;

			case "HT_SUBTERRANEO": // Numero de Días en subterraneo
				$as_valor=0;
				$li_monto=0;
				$ls_cadena="";
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":
						$ls_cadena=" SUM(ROUND(CAST(trasub AS NUMERIC),0)) AS total ";
						break;
					case "MYSQLI":
						$ls_cadena=" SUM(ROUND(CAST(trasub AS NUMERIC),0)) AS total ";
						break;
					case "POSTGRES":
						$ls_cadena=" SUM(ROUND(CAST(trasub AS NUMERIC),0)) AS total ";
						break;					
					case "INFORMIX":
						$ls_cadena=" SUM(ROUND(CAST(trasub AS FLOAT),0)) AS total ";
						break;					
				}
				$arrResultado=$this->io_hojatiempo->uf_select_hojatiempo($as_codper,$ls_cadena,"",$li_monto);
				$li_monto=$arrResultado['ai_total'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;

			case "HT_REPOSOCOMIDA": // Numero de Días de reposo comida
				$as_valor=0;
				$li_monto=0;
				$ls_cadena="";
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":
						$ls_cadena=" SUM(ROUND(CAST(repcom AS NUMERIC),0)) AS total ";
						break;
					case "MYSQLI":
						$ls_cadena=" SUM(ROUND(CAST(repcom AS NUMERIC),0)) AS total ";
						break;
					case "POSTGRES":
						$ls_cadena=" SUM(ROUND(CAST(repcom AS NUMERIC),0)) AS total ";
						break;					
					case "INFORMIX":
						$ls_cadena=" SUM(ROUND(CAST(repcom AS FLOAT),0)) AS total ";
						break;					
				}
				$arrResultado=$this->io_hojatiempo->uf_select_hojatiempo($as_codper,$ls_cadena,"",$li_monto);
				$li_monto=$arrResultado['ai_total'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;

			case "HT_ESCALERA": // Numero de Días de escalera
				$as_valor=0;
				$li_monto=0;
				$ls_cadena="";
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":
						$ls_cadena=" SUM(ROUND(CAST(traesc AS NUMERIC),0)) AS total ";
						break;
					case "MYSQLI":
						$ls_cadena=" SUM(ROUND(CAST(traesc AS NUMERIC),0)) AS total ";
						break;
					case "POSTGRES":
						$ls_cadena=" SUM(ROUND(CAST(traesc AS NUMERIC),0)) AS total ";
						break;					
					case "INFORMIX":
						$ls_cadena=" SUM(ROUND(CAST(traesc AS FLOAT),0)) AS total ";
						break;					
				}
				$arrResultado=$this->io_hojatiempo->uf_select_hojatiempo($as_codper,$ls_cadena,"",$li_monto);
				$li_monto=$arrResultado['ai_total'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;
				
			case substr(trim($as_token),0,8)=="HC_TURNO": // Turno de hoja de tiempo
				$ls_codhor=substr($as_token,10,3); // código del horario
				$ls_codhor=str_pad($ls_codhor,3,"0",0);
				$ls_criterio=" AND sno_hojatiempo.codhor = '".$ls_codhor."'";
				$li_monto=0;
				$arrResultado=$this->io_hojatiempo->uf_select_hojatiempo($as_codper," COUNT(sno_hojatiempo.codhor) AS total ",$ls_criterio,$li_monto);
				$li_monto=$arrResultado['ai_total'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;

			case "PORCENTAJE_JUDICIAL_ALIMENTARIA": // Porcentaje DE MEDIDA JUDICIAL
			        $ls_codper=$this->personal->codper;
					$as_valor=$this->io_beneficiario->uf_select_porcentaje_judicial_alimentaria($ls_codper);
					$as_valor=number_format((float)$as_valor,2,".","");
				break;
				
			case "TRASLADOS": //Horas de los Traslados
				$li_horastraslados=0;
				$ls_codnom=$_SESSION["la_nomina"]["codnom"];
				$arrResultado=$this->uf_obtener_horatraslado($as_codper,$ls_codnom,$li_horastraslados);
				$li_horastraslados=$arrResultado['ai_tietras'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_horastraslados;
				}
				break;
				
                        case "ANTIG_COMP": // Dias Antiguedad Complementaria
				$as_valor=0;
				$li_diabonvac=0;
				$li_diaagui=0;
				$lb_calcular=false;
				$li_diainc_vac=0;
				$li_diainc_agui=0;
				$li_diaadic=0;
				$li_diafide=0; 
				$li_antiguedad=0;
				$ld_fecgen=$_SESSION["la_nomina"]["fechasper"];
				$arrResultado=$this->io_fideicomiso->io_fideiconfigurable->uf_load_dias_vacaagui(substr($_SESSION["la_nomina"]["fechasper"],0,4),$this->personal->codded,
															 				  			   		$this->personal->codtipper,$li_diabonvac,$li_diaagui);
				$li_diabonvac=$arrResultado['ai_diavac'];
				$li_diaagui=$arrResultado['ai_diaagui'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$arrResultado=$this->io_fideicomiso->uf_verificar_personal_version2($this->personal->codper,$ld_fecgen,$li_diabonvac,$li_diaagui,
																				  $lb_calcular,$li_diainc_vac,$li_diainc_agui,$li_diaadic,$li_diafide, 
																				  $li_antiguedad);
					$lb_calcular=$arrResultado['ab_calcular'];
					$li_diainc_vac=$arrResultado['ai_diainc_vac'];
					$li_diainc_agui=$arrResultado['ai_diainc_agui'];
					$li_diaadic=$arrResultado['ai_diaadic'];
					$li_diafide=$arrResultado['ai_diafide'];
					$li_antiguedad=$arrResultado['ai_antiguedad'];
					$lb_valido=$arrResultado['lb_valido'];
					if(($lb_calcular)&&($lb_valido))
					{
						$as_valor=$li_diaadic;
					}
				}
				break;

			case "ANTIG_COMP_MONTO":
				$as_valor=0;
				$li_monto=0;
				$li_anocurper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$li_mescurper=substr($_SESSION["la_nomina"]["fechasper"],5,2);
				$arrResultado=$this->io_fideicomiso->uf_monto_asoc_integral($li_anocurper,$li_mescurper,$this->personal->codper,$li_monto);
				$li_monto=$arrResultado['ai_sueint'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_monto;
				}
				break;
			
			case substr($as_token,0,12)=="CONCEPTO_NOM": // Monto Acumulado del Concepto en un numero de periodos
				$ls_codconc=substr($as_token,13,10); // código del concepto
				$ls_codconc=str_pad($ls_codconc,10,"0",0);
				$ls_perdes=substr($as_token,24,3);
				$ls_perdes=str_pad($ls_perdes,3,"0",0);
				$ls_perhas=substr($as_token,28,3);
				$ls_perhas=str_pad($ls_perhas,3,"0",0);
				$ls_codnom=substr($as_token,32,4);
				$ls_codnom=str_pad($ls_codnom,4,"0",0);
				$ls_tipsal=substr($as_token,37,3);
				$ls_criterio="";
				switch($ls_tipsal)
				{
					case "APE": // Deducción
						$ls_criterio=$ls_criterio." AND (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3') ";
						break;
					case "APP": // Aportes
						$ls_criterio=$ls_criterio." AND (sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
						break;
				}
				$ls_codper=$this->personal->codper;
				if ((trim($ls_perdes)<>'') && (trim($ls_perhas)<>''))
				{
					$ls_criterio=$ls_criterio." AND sno_hsalida.anocur='".intval($_SESSION["la_nomina"]["anocurnom"])."' ".
								 			  " AND sno_hsalida.codperi BETWEEN '".$ls_perdes."' AND '".$ls_perhas."' ";
					
				}
				$arrResultado=$this->io_concepto->uf_buscar_valor_acumulado_periodo($ls_codper,$ls_codconc,$ls_criterio,$ld_monto,$ls_codnom);
				$ld_monto=$arrResultado['as_monto'];
				$lb_valido=$arrResultado['lb_valido'];
				if($ld_monto===0)//Modificado por Carlos Zambrano
				{
					$lb_valido=true;
				}//Modificado por Carlos Zambrano
				if($lb_valido)
				{
					$as_valor=$ld_monto;
				}
			break;

			case "CARGO_PERSONAL": // Cantidad de tipos de Becas
				if($this->personal->codcar=='0000000000')
				{
					$as_valor=number_format((float)$this->personal->codasicar,0,'','');
				}
				else
				{
					$as_valor=number_format((float)$this->personal->codcar,0,'','');
				}
			break;
			
			case "DENO_CARGO": //Denominación del Cargo
				$ls_cargo="";
				$arrResultado = $this->uf_obtener_denominacion_cargo_personal($as_codper,$ls_cargo);
				$ls_cargo = $arrResultado['ls_cargo'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ls_cargo;
				}
			break;
			
			case "UNIDADADMIN": //Unidad administrativa
				$ls_unidad="";
				$arrResultado = $this->uf_obtener_unidad_administrativa($as_codper,$ls_unidad);
				$ls_unidad = $arrResultado['ls_unidad'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ls_unidad;
				}
			break;			

				
			case "NRO_LUNESPERSONAL": // Número de lunes que tiene el personal en el período
				$ld_fecdes=$this->personal->fecingper;
				$ld_fechas=$_SESSION["la_nomina"]["fechasper"];
				if (substr($ld_fecdes,0,4)<substr($ld_fechas,0,4))
				{
					$ld_fecdes=$_SESSION["la_nomina"]["fecdesper"];
				}
				else
				{
					if (substr($ld_fecdes,5,2)<substr($ld_fechas,5,2))
					{
						$ld_fecdes=$_SESSION["la_nomina"]["fecdesper"];
					}
				}
				$as_valor=$this->io_sno->uf_nro_lunes($ld_fecdes,$ld_fechas);
			break;

			case "SALARIO_NORMALMENSUAL": // 
				$as_valor=0;
				$as_sueldo=0;
				$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				$ls_dia=intval(substr($ld_fechasper,8,2));
				$ls_mes=intval(substr($ld_fechasper,5,2));
				$ls_ano=intval(substr($ld_fechasper,0,4));
				switch($_SESSION["la_nomina"]["tippernom"])
				{
					case 1://Quincenal
						if($ls_dia<=15)
						{
							$ls_mes=$ls_mes-1;
							if($ls_mes===0)
							{
								$ls_ano=$ls_ano-1;
								$ls_mes='12';
							}
							else
							{
								$ls_mes=str_pad($ls_mes,2,'0',0);
							}
							$arrResultado=$this->uf_obtener_salarionormal_mes($as_codper,$ls_mes,$ls_ano,$as_sueldo);						
							$as_sueldo=$arrResultado['ai_sueldo'];
							$lb_valido=$arrResultado['lb_valido'];
						}
						else
						{
							$ls_perant=intval($ls_peractnom-1);
							$ls_perant=str_pad($ls_peractnom,3,'0',0);
							$arrResultado=$this->uf_obtener_salarionormal_periodo($as_codper,$ls_perant,$ls_ano,$as_sueldo);	
							$as_sueldo=$arrResultado['ai_sueldo'];
							$lb_valido=$arrResultado['lb_valido'];
							$as_sueldo=$as_sueldo+$this->personal->salarionormal;
						}
					break;
		
					case 2://Mensual
						$ls_mes=$ls_mes-1;
						if($ls_mes===0)
						{
							$ls_ano=$ls_ano-1;
							$ls_mes='12';
						}
						else
						{
							$ls_mes=str_pad($ls_mes,2,'0',0);
						}
						$arrResultado=$this->uf_obtener_salarionormal_mes($as_codper,$ls_mes,$ls_ano,$as_sueldo);						
						$as_sueldo=$arrResultado['ai_sueldo'];
						$lb_valido=$arrResultado['lb_valido'];
					break;
				}
				if($lb_valido)
				{
					$as_valor=$as_sueldo;
				}
			break;
			
			case "DIAS_ADICIONALES_1997": // 
				$ld_fecdes=$this->personal->fecingper;
				$ld_fechas='1997-06-19';
				if (intval(substr($ld_fecdes,0,4))<=intval(substr($ld_fechas,0,4)))
				{
					if (intval(substr($ld_fecdes,5,2))<=intval(substr($ld_fechas,5,2)))
					{
						if (intval(substr($ld_fecdes,8,2))<=intval(substr($ld_fechas,8,2)))
						{
							$as_valor=true;
						}				
						else
						{
							$as_valor=false;
						}	
					}
					else
					{
						$as_valor=false;
					}
				}
				else
				{
					$as_valor=false;
				}
			break;

			case "CUMPLEANIO_PERSONAL": // Si en este período cumple año en el organismo
				$ls_tipo = $_SESSION["la_nomina"]["tippernom"];
				$li_anoact = intval(substr($_SESSION["la_nomina"]["fechasper"],0,4));
				$li_mesact = intval(substr($_SESSION["la_nomina"]["fechasper"],5,2));
				$li_diaact = intval(substr($_SESSION["la_nomina"]["fechasper"],8,2));
				$li_anoper = intval(substr($this->personal->fecnacper,0,4));
				$li_mesper = intval(substr($this->personal->fecnacper,5,2));
				$li_diaper = intval(substr($this->personal->fecnacper,8,2));
				$li_mesdes = intval(substr($_SESSION["la_nomina"]["fecdesper"],5,2));
				$li_diades = intval(substr($_SESSION["la_nomina"]["fecdesper"],8,2));
				$as_valor=0;
				if($li_anoact > $li_anoper)
				{
					if($ls_tipo==0) // es una nómina Semanal
					{
						if($li_mesdes==$li_mesact)
						{
							if($li_mesper==$li_mesact)
							{
								if(($li_diaper>=$li_diades)&&($li_diaper<=$li_diaact))
								{
									$as_valor=1;
								}
							}
						}
						else
						{
							if($li_mesper==$li_mesact)
							{
								if($li_diaper<=$li_diaact)
								{
									$as_valor=1;
								}
							}
							if($li_mesper==$li_mesdes)
							{
								if($li_diaper>=$li_diades)
								{
									$as_valor=1;
								}
							}
						}
					}
					else
					{
						if($li_mesper==$li_mesact)
						{
							if(($li_diaper>=$li_diades)&&($li_diaper<=$li_diaact))
							{
								$as_valor=1;
							}
						}
					}
				}
				break;
				
			case "ANTIGUEDAD_INSTITUCION_TRABAJOANT": // 
				$li_ano=0;
				$li_mes=0;
				$li_dia=0;
				$li_anioant=0;
				$li_mesant=0;
				$li_diaant=0;		
				$arrResultado=$this->uf_obtener_anio_trabajado_anterior($as_codper,$li_anioant,$li_mesant,$li_diaant,"");
				$li_anioant=$arrResultado['ai_anio'];
				$li_mesant=$arrResultado['ai_mes'];
				$li_diaant=$arrResultado['ai_dia'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$ld_fecingper=$this->personal->fecingper;
					$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
					$ld_fecing=substr($ld_fecingper,0,4);
					$li_ano=$ld_fechasper-$ld_fecing;
					$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
					$li_mes=intval(substr($ld_fechasper,5,2));
					$li_dia=intval(substr($ld_fechasper,8,2));
					if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
					{
						$li_ano=$li_ano-1;
					}
					else
					{
						if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,5,2)))
						{
							if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,8,2)))
							{
								$li_ano=$li_ano-1;
							}
						}
					}
					$li_dias=floor(($li_diaant+$li_dia)/30);
					if($li_dias>0)
					{
						$li_mes=$li_mes+$li_dias;
					}
					$li_meses=floor(($li_mes+$li_mesant)/12);
					if($li_meses>0)
					{
						$li_ano=$li_ano+$li_meses+$li_anioant;
					}
					else
					{
						$li_ano=$li_ano+$li_anioant;
					}
				}
				$as_valor=$li_ano;
        		break;
                                
                                
/*--------------------------------------------------------------------------------------------------*/
/*----------------------------------------- FALTAN POR DOCUMENTAR-----------------------------------*/
/*--------------------------------------------------------------------------------------------------*/

			case "GUARDERIA": // Monto de la Guarderia
				$li_montoguarderia=0;
				$arrResultado=$this->uf_obtener_montoguarderia($as_codper,$li_montoguarderia);
				$li_montoguarderia=$arrResultado['ai_montoguard'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_montoguarderia;
				}
				break;
			
			case "ANTIGUEDAD_MESPERIODO_A": // Antiguedad en años de la persona pero del mes del perido
				$ld_fecingper=$this->personal->fecingper;
				$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
				$ld_fecing=substr($ld_fecingper,0,4);
				$as_valor=$ld_fechasper-$ld_fecing;
				$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
				if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
				{
					$as_valor=$as_valor-1;
				}
				break;

			case substr($as_token,0,3)=="VTC": // Total a Cobrar 
				$ls_codconc=str_pad(substr($as_token,4,3),10,"0",0);
				$ls_codper=$this->personal->codper;
				$as_valor=0;
				$li_vtc=0;
				$arrResultado=$this->uf_obtener_vtc($ls_codper,$ls_codconc,$li_vtc);
				$li_vtc=$arrResultado['ai_vtc'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_vtc;
				}
				break;
				
			case "CUMP_ORG_JUB": // Si en este período cumple año de jubilado la persona
				$li_anoact = intval(substr($_SESSION["la_nomina"]["fechasper"],0,4));
				$li_mesact = intval(substr($_SESSION["la_nomina"]["fechasper"],5,2));
				$li_diaact = intval(substr($_SESSION["la_nomina"]["fechasper"],8,2));
				$li_anoper = intval(substr($this->personal->fecjubper,0,4));
				$li_mesper = intval(substr($this->personal->fecjubper,5,2));
				$li_diaper = intval(substr($this->personal->fecjubper,8,2));
				$li_mesdes = intval(substr($_SESSION["la_nomina"]["fecdesper"],5,2));
				$li_diades = intval(substr($_SESSION["la_nomina"]["fecdesper"],8,2));
				$as_valor=0;
				if(($li_anoact > $li_anoper)&&($li_anoper!=1900))
				{
					if($li_mesper==$li_mesact)
					{
						if(($li_diaper>=$li_diades)&&($li_diaper<=$li_diaact))
						{
							$as_valor=1;
						}
					}
				}
				break;

			case "PRIMAS_ANTE": // Valor del sueldo integral de vacaciones del mes anterior al calculo
				$ls_codper=$this->personal->codper;
				$as_valor=0;
				$li_prima=0;
				$li_anoant=intval(substr($_SESSION["la_nomina"]["fechasper"],0,4));
				$li_mesant=intval(substr($_SESSION["la_nomina"]["fechasper"],5,2));
				$li_mesant=$li_mesant-1;
				if($li_mesant==0)
				{
					$li_mesant=12;
					$li_anoant=$li_anoant-1;
				}
				$arrResultado=$this->uf_obtener_primas_ante($ls_codper,$li_mesant,$li_anoant,$li_prima);
				$li_prima=$arrResultado['ai_prima'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_prima;
				}
				break;

			case "PREAVISO": // Preavisos
				$li_tipo_preaviso=0;
				$arrResultado=$this->uf_obtener_tipo_preaviso($as_codper,$li_tipo_preaviso);
				$li_tipo_preaviso=$arrResultado['ai_tipo_preaviso'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_tipo_preaviso;
				}
			break;
			
			case "MES_TRABAJADO_INSTITUTO": // Número de meses laborados por la persona desde su ingreso
				$ld_fecingper=$this->personal->fecingper;
				$ld_fecegrper=$this->personal->fecegrper;
				$ld_diahas = substr($ld_fecegrper, 8, 2);  
				$ld_meshas = substr($ld_fecegrper, 5, 2);  
				$ld_anohas = substr($ld_fecegrper, 0, 4); 
				$ld_diades = substr($ld_fecingper, 8, 2);  
				$ld_mesdes = substr($ld_fecingper, 5, 2);  
				$ld_anodes = substr($ld_fecingper, 0, 4);  
				$b = 0;  
				$mes = $ld_mesdes-1; 				  
				if($mes==2)
				{  
					if(($ld_anohas%4==0 && $ld_anohas%100!=0) || $ld_anohas%400==0)
					{  
						$b = 29;  
					}
					else
					{  
						$b = 28;  
					}  
				}  
				else if($mes<=7)
				{  
					if($mes==0)
					{  
						$b = 31;  
					}  
					else if($mes%2==0)
					{  
						$b = 30;  
					}  
				   else
				   {  
						$b = 31;  
				   }  
				}  
				else if($mes>7)
				{  
				   if($mes%2==0)
				   {  
						$b = 31;  
				   }  
				   else
				   {  
						$b = 30;  
				   }  
				}  
				if($ld_mesdes <= $ld_meshas)
				{  
				   $anios = $ld_anohas - $ld_anodes;  
				   if($ld_diades <= $ld_diahas)
				   {  
						$meses = $ld_meshas - $ld_mesdes;  
						$dies = $ld_diahas - $ld_diades;  
				   }
				   else
				   {  
						if($ld_meshas == $ld_mesdes)
						{  
							$anios = $anios - 1;  
						}  
						$meses = ($ld_meshas - $ld_mesdes - 1 + 12) % 12;  
						$dies = $b-($ld_diades-$ld_diahas);  
				   }  
				}
				else
				{  
				   $anios = $ld_anohas - $ld_anodes - 1;  
				   if($ld_diades > $ld_diahas)
				   {  
						$meses = $ld_meshas - $ld_mesdes -1 +12;  
						$dies = $b - ($ld_diades-$ld_diahas);  
				   }
				   else
				   {  
						$meses = $ld_meshas - $ld_mesdes + 12;  
						$dies = $ld_diahas - $ld_diades;  
				   }  
				}
				 $total_mes=($anios*12)+$meses+($dies/30);
				 $as_valor=round($total_mes,2);
				 $as_valor=intval($as_valor);
			break;
			
			case "CAUSA_EGRESO": // Causas de egresos
				$li_egreso=0;
				$arrResultado=$this->uf_obtener_tipo_egreso($as_codper,$li_egreso);
				$li_egreso=$arrResultado['ai_egreso'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					if ($li_egreso=="N")
					{
						$li_egreso=0;
					}
					elseif($li_egreso=="D")
					{
						$li_egreso=1;
					}
					elseif($li_egreso=="1")
					{
						$li_egreso=2;
					}
					elseif($li_egreso=="2")
					{
						$li_egreso=3;
					}
					elseif($li_egreso=="P")
					{
						$li_egreso=4;
					}
					elseif($li_egreso=="R")
					{
						$li_egreso=5;
					}
					elseif($li_egreso=="T")
					{
						$li_egreso=6;
					}
					elseif($li_egreso=="J")
					{
						$li_egreso=7;
					}
					elseif($li_egreso=="F")
					{
						$li_egreso=8;
					}
				}
				$as_valor=$li_egreso;
			break;
			
			case "ULT_SUELDO_INTEG_HIST": // Preavisos
				$li_sueint=0;
				$arrResultado=$this->uf_obtener_ultimo_sueldo_integral($as_codper,$li_sueint);
				$li_sueint=$arrResultado['ai_sueint'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_sueint;
				}
			break;
			
			case substr(trim($as_token),0,22)=="SUELDO_INTEG_PROM_HIST": // Preavisos
				$li_sueint=0;
				$li_total=0;
				$ls_cantidad=substr($as_token,23,25); // código del concepto
				$arrResultado=$this->uf_obtener_ultimo_sueldo_integral_promedio($as_codper,$ls_cantidad,$li_sueint);
				$li_sueint=$arrResultado['ai_sueint'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$li_total=$li_sueint/$ls_cantidad;
					$as_valor=round($li_total,2);
				}
			break;
			
			case "DIAS_LABORADOS_EGRESO_EJERCICIO": // Preavisos
				$ld_fecegrper=$this->personal->fecegrper;
				$ld_diahas = substr($ld_fecegrper, 8, 2);  
				$ld_meshas = substr($ld_fecegrper, 5, 2);  
				$ld_anohas = substr($ld_fecegrper, 0, 4);
				$ld_fechahas=$_SESSION["la_nomina"]["fechasper"];
				$ld_diades=01;
				$ld_mesdes=01;
				$ld_anodes=substr($ld_fechahas,0,4);
				$as_valor=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
				$as_valor=round($as_valor);
			break;
			
			case "MES_VAC_FRACCIONADA": // Preavisos
				$ld_fecegrper=$this->personal->fecegrper;
				$ld_fecingper=$this->personal->fecingper;
				$ld_diahas = substr($ld_fecegrper, 8, 2);  
				$ld_meshas = substr($ld_fecegrper, 5, 2);  
				$ld_anohas = substr($ld_fecegrper, 0, 4);
				$ld_diades = substr($ld_fecingper, 8, 2);  
				$ld_mesdes = substr($ld_fecingper, 5, 2);  
				$ld_anodes = substr($ld_fecingper, 0, 4);
				if(intval($ld_anohas)>=intval($ld_anodes))
				{
					if(intval($ld_meshas)>=intval($ld_mesdes))
					{
						$as_valor=intval($ld_meshas)-intval($ld_mesdes);
					}
					else
					{
						$as_valor=(12-intval($ld_mesdes)+intval($ld_meshas));
					}		
					if(intval($ld_diahas)<intval($ld_diades))
					{
						$as_valor = $as_valor -1;
					}
				}
				$as_valor=round($as_valor);
			break;
			
			case "VACACION_TABVAC_ACTUAL": // 
				$li_dias=0;
				$ld_fecegrper=$this->personal->fecegrper;
				$ld_fecingper=$this->personal->fecingper;
				$ld_anohas = substr($ld_fecegrper,0,4);
				$ld_anodes = substr($ld_fecingper,0,4);
				$li_anios  = $ld_anohas - $ld_anodes;
				if(intval(substr($ld_fecegrper,0,4))>=intval(substr($ld_fecingper,0,4)))
				{
					if(intval(substr($ld_fecegrper,5,2))==intval(substr($ld_fecingper,5,2)))
					{
						if(intval(substr($ld_fecegrper,8,2))>intval(substr($ld_fecingper,8,2)))
						{
							$li_anios = $li_anios + 1;
						}
					}
					elseif (intval(substr($ld_fecegrper,5,2))>intval(substr($ld_fecingper,5,2)))
					{
						$li_anios = $li_anios + 1;
					}
				}
				$arrResultado = $this->uf_obtener_codigo_tabla_vacacion($as_codper,$as_tabla);
				$as_tabla=$arrResultado['as_tabla'];
				$lb_valido = $arrResultado['lb_valido'];
				$as_tabla  = str_pad($as_tabla,2,"0",0);
				if ($lb_valido)
				{
					$arrResultado=$this->uf_obtener_dias_tabvac($as_tabla,$li_anios,$li_dias);
					$li_dias=$arrResultado['ai_dias'];
					$lb_valido=$arrResultado['lb_valido'];
					$as_valor=$li_dias;
				}
				else
				{
					$as_valor=0;
				}	
			break;
			
			case "VACACION_TABVAC_VENCIDOS": // POR TERMINAR
				$li_dias=0;
				$ld_fecegrper=$this->personal->fecegrper;
				$arrResultado=$this->uf_obtener_dias_vac_no_disfrutados($ld_fecegrper,$as_codper,$li_dias);
				$li_dias=$arrResultado['ai_dias'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_valor=$li_dias;
			break;
			
			case substr(trim($as_token),0,16)=="DIAS_ULT_CALCULO": // 
				$as_valor=0;
				$ls_codnom=substr($as_token,17,4); // código del concepto
				$arrResultado=$this->uf_obtener_ultimo_calculo($this->personal->codper,$ls_codnom);
				$ld_fechahasta=$arrResultado['ad_fechahasta'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					if(($ld_fecegrper<>'1900-01-01')&&($ld_fechahasta<>'1900-01-01'))
					{
						$ld_fecegrper=$this->personal->fecegrper;
						$ld_diahas = substr($ld_fecegrper, 8, 2);  
						$ld_meshas = substr($ld_fecegrper, 5, 2);  
						$ld_anohas = substr($ld_fecegrper, 0, 4);
						$ld_diades=substr($ld_fechahasta, 8, 2); 
						$ld_mesdes=substr($ld_fechahasta, 5, 2); 
						$ld_anodes=substr($ld_fechahasta,0,4);
						$li_dias=((mktime(0,0,0,$ld_meshas,$ld_diahas,$ld_anohas) - mktime(0,0,0,$ld_mesdes,$ld_diades,$ld_anodes))/86400)+1;
						$as_valor=round($li_dias);
					}
				}
			break;
			
			case "PRESTACION_ANTIGUEDAD_EJERCICIO": // Preavisos
				$li_acumulado=0;
				$ld_fechahas=$_SESSION["la_nomina"]["fechasper"];
				$ld_diades=01;
				$ld_mesdes=01;
				$ld_anodes=substr($ld_fechahas,0,4);
				$arrResultado=$this->uf_obtener_prestacion_antiguedad_acumulada($ld_anodes,$as_codper,$li_acumulado);
				$li_acumulado=$arrResultado['ai_acumulado'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_acumulado;
				}
			break;
			
			case "PRESTACION_ANTIGUEDAD_EJERCICIOS_ANTERIORES": // Preavisos
				$li_acumulado=0;
				$ld_fecingper=$this->personal->fecingper;
				$ld_fechahas=$_SESSION["la_nomina"]["fechasper"];
				$ld_diades=01;
				$ld_mesdes=01;
				$ld_anoing=substr($ld_fecingper,0,4);
				$ld_anodes=substr($ld_fechahas,0,4);
				$arrResultado=$this->uf_obtener_prestacion_antiguedad_acumulada_anteriores($ld_anodes,$ld_anoing,$as_codper,$li_acumulado);
				$li_acumulado=$arrResultado['ai_acumulado'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_acumulado;
				}
			break;
			
			case "INTERESES_PRESTACION_EJERCICIO": // Preavisos
				$li_acumulado=0;
				$ld_fechahas=$_SESSION["la_nomina"]["fechasper"];
				$ld_diades=01;
				$ld_mesdes=01;
				$ld_anodes=substr($ld_fechahas,0,4);
				$arrResultado=$this->uf_obtener_intereses_prestacion_acumulada($ld_anodes,$as_codper,$li_acumulado);
				$li_acumulado=$arrResultado['ai_acumulado'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_acumulado;
				}
			break;
			
			case "INTERESES_PRESTACION_ANTERIOR_EJERCICIO": // Preavisos
				$li_acumulado=0;
				$ld_fecingper=$this->personal->fecingper;
				$ld_fechahas=$_SESSION["la_nomina"]["fechasper"];
				$ld_diades=01;
				$ld_mesdes=01;
				$ld_anoing=substr($ld_fecingper,0,4);
				$ld_anodes=substr($ld_fechahas,0,4);
				$arrResultado=$this->uf_obtener_intereses_prestacion_acumulada_anteriores($ld_anodes,$ld_anoing,$as_codper,$li_acumulado);
				$li_acumulado=$arrResultado['ai_acumulado'];
				$lb_valido=$arrResultado['lb_valido'];				
				if($lb_valido)
				{
					$as_valor=$li_acumulado;
				}
			break;
			
			case "ULT_SUELDO_INTEG_ANTER": // Preavisos
				$li_sueint=0;
				$ld_fechacomp=$_SESSION["la_nomina"]["fechasper"];
				$arrResultado=$this->uf_obtener_ultimo_sueldo_integral_anterior($as_codper,$ld_fechacomp,$li_sueint);
				$li_sueint=$arrResultado['ai_sueint'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$li_sueint;
				}
			break;
			
			case substr(trim($as_token),0,23)=="SUELDO_INTEG_PROM_ANTER": // Preavisos
				$ls_cantidad=substr($as_token,24,26); // cantidad a promediar
				$li_sueint=0;
				$ld_fechacomp=$_SESSION["la_nomina"]["fechasper"];
				$arrResultado=$this->uf_obtener_ultimo_sueldo_integral_anterior_promedio($as_codper,$ld_fechacomp,$ls_cantidad,$li_sueint);
				$li_sueint=$arrResultado['ai_sueint'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$li_total=$li_sueint/$ls_cantidad;
					$as_valor=round($li_total,2);
				}
			break;
			
			case substr(trim($as_token),0,3)=="SIV": // Preavisos
				$ls_nomina=substr($as_token,4,8); // nomina
				$ld_fecegrper=$this->personal->fecegrper;
				$ai_sueintvac=0;
				$arrResultado=$this->uf_load_sueldointegral_vac($as_codper,$ai_sueintvac,1,$ld_fecegrper,$ls_nomina);
				$ai_sueintvac=$arrResultado['ai_sueintvac'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ai_sueintvac;
				}
			break;
			
			case "VACACION_VENCIDA": // Cantidad de Vacaciones Vencidas
				$ls_codper=$this->personal->codper;
				$ls_cantidad=0;
				$arrResultado=$this->uf_cantidad_periodos_venc($ls_codper,$ls_cantidad);
				$ls_cantidad=$arrResultado['ls_cantidad'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ls_cantidad;
				}
			break;
			
			case "BECAS": // Devuelve true si a un familiar se le aplica la beca
				$ls_codper=$this->personal->codper;
				$lb_valido=$this->uf_aplica_beca($ls_codper);
				if($lb_valido)
				{
					$as_valor=true;
				}
				else
				{
					$as_valor=0;
				}
			break;
			
			case "BECAS_NIVEL_PRIMARIA": // Cantidad de tipos de Becas
				$ls_codper=$this->personal->codper;
				$ls_cantidad=0;
				$arrResultado=$this->uf_cantidad_becados($as_codper,'P',$ls_cantidad);
				$ls_cantidad=$arrResultado['ls_cantidad'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ls_cantidad;
				}
			break;
			
			case "BECAS_NIVEL_DIVERSIFICADA": // Cantidad de tipos de Becas
				$ls_codper=$this->personal->codper;
				$ls_cantidad=0;
				$arrResultado=$this->uf_cantidad_becados($as_codper,'D',$ls_cantidad);
				$ls_cantidad=$arrResultado['ls_cantidad'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ls_cantidad;
				}
			break;
			
			case "BECAS_NIVEL_UNIVERSITARIA": // Cantidad de tipos de Becas
				$ls_codper=$this->personal->codper;
				$ls_cantidad=0;
				$arrResultado=$this->uf_cantidad_becados($as_codper,'U',$ls_cantidad);
				$ls_cantidad=$arrResultado['ls_cantidad'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ls_cantidad;
				}
			break;
			
			case "BECA_ESTUDIO_CURSO": // Cantidad de tipos de Becas
				$ai_cantidad=0;
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->uf_select_estudiorealizado($ls_codper,'2',$ai_cantidad);
				$ai_cantidad=$arrResultado['ai_cantidad'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$as_valor=$ai_cantidad;
				}
			break;
/*--------------------------------------------------------------------------------------------------*/
/*----------------------------------------- FALTAN POR DOCUMENTAR-----------------------------------*/
/*--------------------------------------------------------------------------------------------------*/
				
			case "ES_DIFICILACCESO": // Si el Tipo escuela es de Dificil Acceso
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->uf_load_tipoescuela($ls_codper,'difacc',$as_valor);
				$as_valor=$arrResultado['ai_valor'];
				$lb_valido=$arrResultado['lb_valido'];
			break;
                        
			case "ES_MEDIANOACCESO": // Si el Tipo escuela es de Mediano Acceso
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->uf_load_tipoescuela($ls_codper,'medacc',$as_valor);
				$as_valor=$arrResultado['ai_valor'];
				$lb_valido=$arrResultado['lb_valido'];
			break;
                            
			case "ES_RURAL": // Si el Tipo escuela es rural
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->uf_load_tipoescuela($ls_codper,'rural',$as_valor);
				$as_valor=$arrResultado['ai_valor'];
				$lb_valido=$arrResultado['lb_valido'];
			break;
                            
			case "ES_COLEGIONOCTURNO": // Si el Tipo escuela es colegio nocturno
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->uf_load_tipoescuela($ls_codper,'colnoc',$as_valor);
				$as_valor=$arrResultado['ai_valor'];
				$lb_valido=$arrResultado['lb_valido'];
			break;
                            
			case "ES_COLEGIOESPECIAL": // Si el Tipo escuela es colegio especial
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->uf_load_tipoescuela($ls_codper,'colesp',$as_valor);
				$as_valor=$arrResultado['ai_valor'];
				$lb_valido=$arrResultado['lb_valido'];
			break;
                            
			case "ES_PENITENCIARIO": // Si el Tipo escuela es penitenciario
				$ls_codper=$this->personal->codper;
				$arrResultado=$this->uf_load_tipoescuela($ls_codper,'colpen',$as_valor);
				$as_valor=$arrResultado['ai_valor'];
				$lb_valido=$arrResultado['lb_valido'];
			break;
                            
			case "ANTIGUEDAD_TRABAJOANT_ANIO_VALIDADO": // 
				$li_anioant=0;
				$li_mesant=0;
				$li_diaant=0;
				$li_ano=0;
				$li_mes=0;
				$li_dia=0;
				$arrResultado=$this->uf_obtener_anio_trabajado_anterior($as_codper,$li_anioant,$li_mesant,$li_diaant,1);
				$li_anioant=$arrResultado['ai_anio'];
				$li_mesant=$arrResultado['ai_mes'];
				$li_diaant=$arrResultado['ai_dia'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$ld_fecingper=$this->personal->fecingper;
					$ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
					$ld_fecing=substr($ld_fecingper,0,4);
					$li_ano=$ld_fechasper-$ld_fecing;
					$ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
					$li_mes=intval(substr($ld_fechasper,5,2));
					$li_dia=intval(substr($ld_fechasper,8,2));
					if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
					{
						$li_ano=$li_ano-1;
					}
					else
					{
						if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,5,2)))
						{
							if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,8,2)))
							{
								$li_ano=$li_ano-1;
							}
						}
					}
					$li_dias=floor(($li_diaant+$li_dia)/30);
					if($li_dias>0)
					{
						$li_mes=$li_mes+$li_dias;
					}
					$li_meses=floor(($li_mes+$li_mesant)/12);
					if($li_meses>0)
					{
						$li_ano=$li_ano+$li_meses+$li_anioant;
					}
					else
					{
						$li_ano=$li_ano+$li_anioant;
					}					
				}
				$as_valor=$li_ano;
			break;
                    
			case "ANTIGUEDAD_TRABAJOANT_ANIO_FICHA": // 
				$li_anioant=$this->personal->anoservpreper;
				$li_mesant=$this->personal->messervpreper;
				$li_diaant=$this->personal->diasserprevper;
                                //print "Fecha Ingreso->".$this->personal->fecingper."<br>";
                                //print "Anio ante->".$li_anioant."<br>";
                                //print "Mes ante->".$li_mesant."<br>";
                                //print "Dia ante->".$li_diaant."<br>";
				$li_ano=0;
				$li_mes=0;
				$li_dia=0;
                                $ld_fecingper=$this->personal->fecingper;
                                $ld_fechasper=substr($_SESSION["la_nomina"]["fechasper"],0,4);
                                $ld_fecing=substr($ld_fecingper,0,4);
                                $li_ano=$ld_fechasper-$ld_fecing;
                                $ld_fechasper=$_SESSION["la_nomina"]["fechasper"];
                                $li_mes=intval(substr($ld_fechasper,5,2));
                                $li_dia=intval(substr($ld_fechasper,8,2));
                                //print "Anio->".$li_ano."<br>";
                                //print "Mes->".$li_mes."<br>";
                                //print "Dia->".$li_dia."<br>";
                                if(intval(substr($ld_fechasper,5,2))<intval(substr($ld_fecingper,5,2)))
                                {
                                        $li_ano=$li_ano-1;
                                        $li_mes=12+$li_mes-intval(substr($ld_fecingper,5,2));
                                }
                                else
                                {
                                        if(intval(substr($ld_fechasper,5,2))==intval(substr($ld_fecingper,5,2)))
                                        {
                                                if(intval(substr($ld_fechasper,8,2))<intval(substr($ld_fecingper,8,2)))
                                                {
                                                        $li_ano=$li_ano-1;
                                                        $li_mes=11;
                                                }
                                        }
                                }
                                $li_dias=floor(($li_diaant+$li_dia)/30);
                                if($li_dias>0)
                                {
                                        $li_mes=$li_mes+$li_dias;
                                }
                                $li_meses=floor(($li_mes+$li_mesant)/12);
                                if($li_meses>0)
                                {
                                        $li_ano=$li_ano+$li_meses+$li_anioant;
                                }
                                else
                                {
                                        $li_ano=$li_ano+$li_anioant;
                                }					
				$as_valor=$li_ano;
			break;
                        
			default:
				$this->io_mensajes->message("ERROR->PERSONAL PS[".$as_token."] Nó Válido.");
				$lb_valido=false;
				break;

		}
		$arrResultado['as_valor']=$as_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_valor_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_valor_tabla($as_codper,$as_token,$ai_valor)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_valor_tabla
		//		   Access: private 
		//	    Arguments: as_codper // código de personal
		//				   as_token // valor que va a ser reemplazado
		//				   ai_valor // valor del token
		//	      Returns: lb_valido True si se sustituye correctamente el valor ó False si hubo error 
		//	  Description: función que dado un token se sutituye por su valor respectivo de la tabla de sueldo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ai_valor=0;
		$lb_valido=true;
		$ls_sql="SELECT sno_grado.monsalgra ".
				"  FROM sno_personalnomina, sno_grado ".
				" WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_personalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_personalnomina.codper='".$as_codper."' ".
				"   AND sno_personalnomina.codtab='".$as_token."' ".
				"   AND sno_personalnomina.codemp=sno_grado.codemp ".
				"   AND sno_personalnomina.codnom=sno_grado.codnom ".
				"   AND sno_personalnomina.codtab=sno_grado.codtab ".
				"   AND sno_personalnomina.codgra=sno_grado.codgra ".
				"   AND sno_personalnomina.codpas=sno_grado.codpas ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("ERROR->TABLA SUELDO TB[".$as_token."] Nó Válido.");
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_valor=$row["monsalgra"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['as_valor']=$ai_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_valor_tabla
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_valor_concepto($as_codper,$as_token,$ai_valor)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_valor_concepto
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_token // valor que va a ser reemplazado
		//				   ai_valor // valor del token
		//	      Returns: lb_valido True si se sustituye correctamente el valor ó False si hubo error 
		//	  Description: función que dado un token se sutituye por su valor respectivo del concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_aplicar=true;
		$lb_filtro=true;
		$ai_valor=0;
		$as_formula="";
		$lb_valido=true;
		$ls_codconc=$_SESSION["la_conceptopersonal"]["codconc"];
		if(intval($ls_codconc)>intval($as_token))
		{
                        if (!array_key_exists($as_token,$this->conceptospersona))
			//if((is_null($this->conceptospersona[$as_token])))
			{
				$la_concepto='';
				$arrResultado=$this->io_concepto->uf_obtener_conceptopersonal($as_codper,$as_token,$la_concepto);
				$la_concepto=$arrResultado['aa_concepto'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					if($la_concepto["glocon"]==false)
					{
						$lb_aplicar=$la_concepto["aplcon"];
					}
					if(trim($la_concepto["concon"])!="")
					{
						$lb_filtro=false;
						$arrResultado=$this->uf_evaluar($as_codper,$la_concepto["concon"],$lb_filtro);
						$lb_filtro=$arrResultado['as_valor'];
						$lb_valido=$arrResultado['lb_valido'];
					}				  
					if(($lb_aplicar)&&($lb_filtro))
					{
						$as_formula=$la_concepto["forcon"];
						$arrResultado=$this->uf_evaluar($as_codper,$as_formula,$ai_valor);
						$ai_valor=$arrResultado['as_valor'];
						$lb_valido=$arrResultado['lb_valido'];
					}
					if($lb_valido)
					{
						$this->conceptospersona[$as_token]=number_format((float)$ai_valor,4,".","");
					}
				}		
				else
				{
					$this->io_mensajes->message("ERROR->CONCEPTO CN[".$as_token."] Nó Válido. Utilizado en el Concepto ".$ls_codconc);
				}
			}
			else
			{
				$ai_valor=number_format((float)$this->conceptospersona[$as_token],2,".","");
			}
		}
		else
		{
			$this->io_mensajes->message("ERROR->Error de Anidamiento en Concepto ".$ls_codconc.".");
			$lb_valido=false;
		}
		$arrResultado['as_valor']=$ai_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_valor_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_valor_constante($as_codper,$as_token,$ai_valor)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_valor_constante
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_token // valor que va a ser reemplazado
		//				   ai_valor // valor del token
		//	      Returns: lb_valido True si se sustituye correctamente el valor ó False si hubo error 
		//	  Description: función que dado un token se sutituye por su valor respectivo de la constante
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ai_valor=0;
		$lb_valido=true;
		$ls_codconc=$_SESSION["la_conceptopersonal"]["codconc"];
		//$arrResultado=$this->io_constante->uf_obtener_constantepersonal($as_codper,$as_token,$ai_valor);
		//$ai_valor=$arrResultado['ai_valor'];
		//$lb_valido=$arrResultado['lb_valido'];
                $ai_valor=$_SESSION["constantepersonal"][$as_token];
		if(trim($ai_valor)=="")
		{
                    $this->io_mensajes->message("ERROR->CONSTANTE CT[".$as_token."] Nó Válido. Utilizado en el Concepto ".$ls_codconc);
                    $lb_valido=false;
		}
		$arrResultado['as_valor']=$ai_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_valor_constante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_vtc($as_codper,$as_codconc,$ai_vtc)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_vtc
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_codconc // código del concepto
		//				   ai_vtc // Total a cobrar
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y concepto calcula todas las asignaciones menos las deducciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_vtc=0;
		$ls_sql="SELECT sum(valsal) as valsal ".
				"  FROM sno_salida ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.codconc<>'".$as_codconc."' ".
				"   AND sno_salida.codper='".$as_codper."' ".
				"   AND (sno_salida.tipsal='A' OR sno_salida.tipsal='D' OR sno_salida.tipsal='P1') ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_vtc ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_vtc=$row["valsal"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_vtc']=$ai_vtc;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_vtc
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_vca($as_codper,$as_codconc,$as_anopre,$as_perpre,$ai_vca)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_vca
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_codconc // código del concepto
		//				   as_anopre // Ano previo
		//				   as_perpre // perido previo
		//				   ai_vca // Toatl del concepto
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y concepto calcula el valor del concepto en un período previo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_vca=0;
		$ls_sql="SELECT sum(valsal) as valsal ".
				"  FROM sno_hsalida ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$as_perpre."' ".
				"   AND sno_hsalida.anocur='".$as_anopre."' ".
				"   AND sno_hsalida.codconc='".$as_codconc."' ".
				"   AND sno_hsalida.codper='".$as_codper."' ".
				"   AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='P1')";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_vca ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_vca=$row["valsal"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_vca']=$ai_vca;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_vca
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_primas_ante($as_codper,$as_mes,$as_anopre,$ai_prima)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_primas_ante
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_codconc // código del concepto
		//				   as_anopre // Ano previo
		//				   as_perpre // perido previo
		//				   ai_vca // Toatl del concepto
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo integral de vacaciones dado un mes y año
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/09/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_prima=0;
		$ls_sql="SELECT sum(valsal) as valsal ".
				"  FROM sno_hsalida, sno_hperiodo ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."'".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."'".
				"   AND sno_hsalida.anocur='".$as_anopre."'".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."'".
				"   AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='P1') ".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_primas_ante ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_prima=$row["valsal"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_prima']=$ai_prima;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_primas_ante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_sueldo_ante($as_codper,$as_mes,$as_anocur,$ai_sueldo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_sueldo_ante
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_mes // Mes del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al mes
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/11/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueldo=0;
		$ls_concepto=$this->io_sno->uf_select_config("SNO","CONFIG","CONCEPTO_SUELDO_ANT","XXXXXXXXXX","C");
		$ls_sql="SELECT sum(valsal) as valsal ".
				"  FROM sno_hsalida, sno_hperiodo ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."'".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."'".
				"   AND sno_hsalida.anocur='".$as_anocur."'".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   AND sno_hsalida.codconc='".$ls_concepto."'".
				"   AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."'".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_sueldo_ante ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_sueldo=$row["valsal"];
			}
			if(empty($ai_sueldo))
			{
				$ai_sueldo=0;
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_sueldo']=$ai_sueldo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_sueldo_ante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_concepto_ante($as_codper,$as_codconc,$as_criterio,$ab_cobrado)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_concepto_ante
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_codconc // código del concepto
		//				   as_criterio // Criterio de Busqueda
		//				   ab_cobrado // si el concepto es distinto de cero
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y el concepto verifica si dicho concepto fue pagado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/08/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ab_cobrado=false;
		$ls_sql="SELECT sum(valsal) as valsal ".
				"  FROM sno_hsalida ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."'".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."'".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   AND sno_hsalida.codconc='".$as_codconc."'".
				$as_criterio;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_concepto_ante ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_valor=round($row["valsal"]);
				if($ai_valor<>0)
				{
					$ab_cobrado=true;
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ab_cobrado']=$ab_cobrado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_concepto_ante
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_dias_tabla_vacacion($as_codtabvac,$ai_anoant,$ai_anopre,$ai_diabonvac)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_tablavacacion
		//		   Access: public (sigesp_sno_c_vacacion)
		//	    Arguments: as_codtabvac  // código de la tabla de vacacion
		//	    		   ai_anoant  // Año de antiguedad
		//	    		   ai_anopre  // Años de servicio previos
		//	    		   ai_diabonvac  // Días de Bono vacacional
		//	      Returns: lb_valido True si existe ó False si no existe
		//	  Description: Funcion que verifica si la tabla de vacacion está registrada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_diabonvac=0;
		$ls_sql="SELECT pertabvac, anoserpre ".
				"  FROM sno_tablavacacion ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codtabvac='".$as_codtabvac."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_dias_tabla_vacacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				if($row["anoserpre"]==1)
				{
					$ai_anoant=$ai_anoant+$ai_anopre;
				}
				if($row["pertabvac"]==0)
				{
					$li_anoxper=5;// Quinquenal
				}
				else
				{
					$li_anoxper=1;// Anual
				}
				$li_quinquenio=(($ai_anoant-1)/$li_anoxper)+1;
			}
			$this->io_sql->free_result($rs_data);
			$ls_sql="SELECT diadisvac, diabonvac, diaadidisvac, diaadibonvac ".
					"  FROM sno_tablavacperiodo ".
					" WHERE codemp='".$this->ls_codemp."'".
					"   AND codtabvac='".$as_codtabvac."'".
					"   AND lappervac<=".$li_quinquenio."".
					" ORDER BY lappervac DESC ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_dias_tabla_vacacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$ai_diabonvac=$row["diabonvac"]+$row["diaadibonvac"];
				}
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['ai_diabonvac']=$ai_diabonvac;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_dias_tabla_vacacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_dias_tabvac($as_tabla,$ai_anios,$ai_dias)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_dias_tabvac
		//		   Access: public (sigesp_sno_c_vacacion)
		//	    Arguments: as_codtabvac  // código de la tabla de vacacion
		//	    		   ai_anoant  // Año de antiguedad
		//	    		   ai_anopre  // Años de servicio previos
		//	    		   ai_diabonvac  // Días de Bono vacacional
		//	      Returns: lb_valido True si existe ó False si no existe
		//	  Description: Funcion que verifica si la tabla de vacacion está registrada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_dias=0;
		$ls_sql="SELECT diadisvac, diabonvac, diaadidisvac, diaadibonvac ".
				"   FROM sno_tablavacperiodo ".
				"   WHERE codemp='".$this->ls_codemp."'".
				"   AND codtabvac='".$as_tabla."'".
				"   AND lappervac='".$ai_anios."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_dias_tabvac ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_dias=$row["diadisvac"]+$row["diabonvac"]+$row["diaadidisvac"]+$row["diaadibonvac"];
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_dias']=$ai_dias;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_dias_tabla_vacacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_dias_vac_no_disfrutados($ad_fecegrper,$as_codper,$ai_dias)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_dias_vac_no_disfrutados
		//		   Access: public (sigesp_sno_c_vacacion)
		//	    Arguments: as_codtabvac  // código de la tabla de vacacion
		//	    		   ai_anoant  // Año de antiguedad
		//	    		   ai_anopre  // Años de servicio previos
		//	    		   ai_diabonvac  // Días de Bono vacacional
		//	      Returns: lb_valido True si existe ó False si no existe
		//	  Description: Funcion que verifica si la tabla de vacacion está registrada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_dias=0;
		$ls_sql="SELECT SUM(diavac) as diavac, SUM(diabonvac) as diabonvac, SUM(diaadivac) as diaadivac, SUM(diaadibon) as diaadibon ".
				"   FROM sno_vacacpersonal ".
				"   WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND stavac=1 OR stavac=2";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_dias_vac_no_disfrutados ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_dias=$row["diavac"]+$row["diabonvac"]+$row["diaadivac"]+$row["diaadibon"];
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_dias']=$ai_dias;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_dias_vac_no_disfrutados
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_sueldominimo($ai_sueldominimo, $as_codsuemin='')
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_sueldominimo
		//		   Access: private
		//	    Arguments: ai_sueldominimo // Sueldo Mínimo
		//	      Returns: lb_valido True si se obtuvo el Sueldo mínimo ó False si no se obtuvo
		//	  Description: función que busca el sueldo mínimo vigente
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/04/2008 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueldominimo=0;
                $as_criterio="   AND fecvigsuemin <= '".$_SESSION["la_nomina"]["fechasper"]."'";
                if ($as_codsuemin<>'')
                {
                    $as_criterio="   AND codsuemin = '".$as_codsuemin."'";
                }
		$ls_sql="SELECT monsuemin ".
				"  FROM sno_sueldominimo ".
				" WHERE codemp='".$this->ls_codemp."'".
				$as_criterio.
				" ORDER BY fecvigsuemin ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_sueldominimo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_sueldominimo=number_format((float)$row["monsuemin"],2,".","");
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_sueldominimo']=$ai_sueldominimo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_concepto_ante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_unidadtributaria($as_anio,$ai_unidadtributaria)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_unidadtributaria
		//		   Access: private
		//	    Arguments: as_anio // Año
		//	   			   ai_unidadtributaria // Valor de la unidad tributaria
		//	      Returns: lb_valido True si se obtuvo la Unidad Tributaria ó False si no se obtuvo
		//	  Description: función que busca la unidad tributaria del año
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/04/2008 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_unidadtributaria=0;
		$ls_sql="SELECT valunitri ".
				"  FROM sigesp_unidad_tributaria ".
				" WHERE anno = '".$as_anio."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_unidadtributaria ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_unidadtributaria=number_format((float)$row["valunitri"],3,".","");
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_unidadtributaria']=$ai_unidadtributaria;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_unidadtributaria
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_montoanterior($as_codper,$as_tipsal,$ai_meses,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_montoanterior
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_tipsal // Tipo de salida
		//				   ai_meses // Meses anteriores
		//				   ai_monto // Monto
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y obtiene la suma de los concepto del tipo de salida
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/06/2008 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_monto=0;
		$li_mesact=substr($_SESSION["la_nomina"]["fechasper"],5,2);
		$ls_anocur=$_SESSION["la_nomina"]["anocurnom"];
		$li_mesdes=($li_mesact-$ai_meses);
		if(intval($li_mesdes)<0)
		{
			$li_mesdes=1;
		}
		$li_mesdes=str_pad($li_mesdes,2,"0",0);
		switch($as_tipsal)
		{
			case "AS": // Asignación
				$as_tipsal=" (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1') ";
				break;
			case "DE": // Deducción
				$as_tipsal=" (sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2') ";
				break;
			case "AP": // Aportes
				$as_tipsal=" (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3') ";
				break;
		}
		$ls_sql="SELECT sum(valsal) as valsal ".
				"  FROM sno_hsalida, sno_hperiodo ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$ls_anocur."' ".
				"   AND sno_hsalida.codper='".$as_codper."' ".
				"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2) >= '".$li_mesdes."' ".
				"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2) < '".$li_mesact."' ".
				"   AND ".$as_tipsal.
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_montoanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_monto=abs($row["valsal"]);
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_monto']=$ai_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_montoanterior
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_montoarc_mesactual($as_codper,$ai_totalarcant)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_montoarc_mesactual
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	    		   ai_totalarcant // Monto ARC anterior
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal obtiene la suma de todos los 
		//				   conceptos que pertenecen al arc del periodo inmediato anterior
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/08/2008 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_totalarcant=0;
		$li_mesact=substr($_SESSION["la_nomina"]["fechasper"],5,2);
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_sql="SELECT SUM(sno_hsalida.valsal)  as total".
				"  FROM sno_hsalida, sno_hconcepto, sno_hperiodo ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."'".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."'".
				"   AND sno_hsalida.anocur='".$ls_anocurnom."'".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   AND sno_hsalida.codperi <> '".$_SESSION["la_nomina"]["peractnom"]."'".
				"   AND sno_hconcepto.sigcon='A'".
				"   AND sno_hconcepto.aplarccon=1".
				"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2) = '".$li_mesact."' ".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi ".
				"   AND sno_hsalida.codemp=sno_hconcepto.codemp".
				"   AND sno_hsalida.codnom=sno_hconcepto.codnom".
				"   AND sno_hsalida.anocur=sno_hconcepto.anocur".
				"   AND sno_hsalida.codperi=sno_hconcepto.codperi".
				"   AND sno_hsalida.codconc=sno_hconcepto.codconc";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_montoarc_mesactual ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_totalarcant=number_format((float)$rs_data->fields["total"],2,".","");
			}
		}
		$arrResultado['ai_totalarcant']=$ai_totalarcant;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_obtener_montoarc_mesactual
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_sueldo_docente($as_codper,$ai_sueldo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_sueldo_docente
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	    		   ai_sueldo // Sueldo Docente
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal obtiene el sueldo de la clasificación docente
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/03/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueldo=0;
		$ls_sql="SELECT suesupcladoc AS sueldo".
				"  FROM sno_personalnomina, sno_clasificaciondocente ".
				" WHERE sno_personalnomina.codemp='".$this->ls_codemp."'".
				"   AND sno_personalnomina.codnom='".$this->ls_codnom."'".
				"   AND sno_personalnomina.codper='".$as_codper."'".
				"   AND sno_personalnomina.codemp = sno_clasificaciondocente.codemp ".
				"   AND sno_personalnomina.codescdoc=sno_clasificaciondocente.codescdoc".
				"   AND sno_personalnomina.codcladoc=sno_clasificaciondocente.codcladoc";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_sueldo_docente ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_sueldo=number_format((float)$rs_data->fields["sueldo"],2,".","");
			}
		}
		$arrResultado['ai_sueldo']=$ai_sueldo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_sueldo_docente
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_codigo_tabla_vacacion($as_codper,$as_tabla)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_codigo_tabla_vacacion
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	    		   ai_sueldo // Sueldo Docente
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal obtiene el sueldo de la clasificación docente
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/03/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_tabla=0;
		$ls_sql="SELECT codtabvac".
				"  FROM sno_personalnomina ".
				" WHERE sno_personalnomina.codemp='".$this->ls_codemp."'".
				"   AND sno_personalnomina.codnom='".$this->ls_codnom."'".
				"   AND sno_personalnomina.codper='".$as_codper."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_codigo_tabla_vacacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$as_tabla=$rs_data->fields["codtabvac"];
			}
		}
		$arrResultado['as_tabla']=$as_tabla;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_codigo_tabla_vacacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_denominacion_cargo_personal($as_codper,$ls_cargo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_denominacion_cargo_personal
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	    		   ai_sueldo // Sueldo Docente
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal obtiene el sueldo de la clasificación docente
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/03/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_cargo="";
		$ls_sql="SELECT descasicar".
				"  FROM sno_personalnomina ".
				" WHERE sno_personalnomina.codemp='".$this->ls_codemp."'".
				"   AND sno_personalnomina.codnom='".$this->ls_codnom."'".
				"   AND sno_personalnomina.codper='".$as_codper."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_denominacion_cargo_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ls_cargo=$rs_data->fields["descasicar"];
			}
		}
		$arrResultado['ls_cargo']=$ls_cargo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_denominacion_cargo_personal
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_unidad_administrativa($as_codper,$ls_unidad)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_denominacion_cargo_personal
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	    		   ai_sueldo // Sueldo Docente
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal obtiene el sueldo de la clasificación docente
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/03/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_unidad="";
		$ls_sql="SELECT minorguniadm,ofiuniadm,uniuniadm,depuniadm,prouniadm".
				"  FROM sno_personalnomina ".
				" WHERE sno_personalnomina.codemp='".$this->ls_codemp."'".
				"   AND sno_personalnomina.codnom='".$this->ls_codnom."'".
				"   AND sno_personalnomina.codper='".$as_codper."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_unidad_administrativa ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ls_unidad=$rs_data->fields["minorguniadm"].$rs_data->fields["ofiuniadm"].$rs_data->fields["uniuniadm"].$rs_data->fields["depuniadm"].$rs_data->fields["prouniadm"];
			}
		}
		$arrResultado['ls_unidad']=$ls_unidad;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_unidad_administrativa
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_prima_docente($as_codper,$as_tipo,$ai_prima)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_prima_docente
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	    		   as_tipo //  Tipo de Prima
		//	    		   ai_prima // Prima
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal obtiene las primas docentes seún el tipo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/03/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_prima=0;
		$ls_sql="SELECT valpridoc AS prima".
				"  FROM sno_primadocentepersonal, sno_primasdocentes ".
				" WHERE sno_primadocentepersonal.codemp='".$this->ls_codemp."'".
				"   AND sno_primadocentepersonal.codnom='".$this->ls_codnom."'".
				"   AND sno_primadocentepersonal.codper='".$as_codper."'".
				"   AND sno_primasdocentes.tippridoc='".$as_tipo."'".				
				"   AND sno_primadocentepersonal.codemp = sno_primasdocentes.codemp ".
				"   AND sno_primadocentepersonal.codpridoc=sno_primasdocentes.codpridoc";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_prima_docente ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_prima=number_format((float)$rs_data->fields["prima"],2,".","");
			}
		}
		$arrResultado['ai_prima']=$ai_prima;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_prima_docente
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_crear_proxima_vacacionpersonal($as_codper,$as_fecdes,$as_fechas)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_crear_vacacionpersonal
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que dado el código de personal crea una variable session con todos los datos
		//				   de vacación personal
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 17/01/2009 								Fecha Última Modificación :		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_metodovacaciones=trim($this->personal->mettabvac);
		switch ($ls_metodovacaciones)
		{
			case "1": //METODO #0
				$ld_desde_s=$as_fechas;
				$ld_desde_s=$this->io_sno->uf_suma_fechas($ld_desde_s,1);
				$ld_desde_s=$this->io_funciones->uf_convertirdatetobd($ld_desde_s);	
				switch($_SESSION["la_nomina"]["tippernom"])
				{
					case "0": // Nóminas Semanales
						$li_dias=7;
						break;
					case "1": // Nóminas Quincenales
						$li_dias=15;
						break;
					case "2": // Nóminas Mensuales
						$li_dias=30;
						break;
					case "3": // Nóminas Anuales
						$li_dias=365;
						break;
				}
				$ld_hasta_s=$as_fechas;
				$ld_hasta_s=$this->io_sno->uf_suma_fechas($ld_hasta_s,$li_dias);
				$ls_dia=substr($ld_hasta_s,0,2);
				$ls_mes=substr($ld_hasta_s,3,2);
				$ls_ano=substr($ld_hasta_s,6,4);
				while(checkdate($ls_mes,$ls_dia,$ls_ano)==false)
				{ 
				   	$ls_dia=$ls_dia-1; 
				} 
				$ld_hasta_s=$ls_dia."/".$ls_mes."/".$ls_ano;
				$ld_hasta_s=$this->io_funciones->uf_convertirdatetobd($ld_hasta_s);
				$ld_desde_r=$this->io_funciones->uf_convertirdatetobd($as_fecdes);
				$ld_hasta_r=$this->io_funciones->uf_convertirdatetobd($as_fechas);
				
				$ls_sql="SELECT codvac, sueintbonvac, sueintvac, fecdisvac, fecreivac, diavac, diabonvac, diaadibon, diaadivac, ".
						"		diafer, sabdom, quisalvac, quireivac ".
						"  FROM sno_vacacpersonal ".
						" WHERE codper='".$as_codper."' ".
						"   AND ((stavac='2' AND (fecdisvac between '".$ld_desde_r."' AND '".$ld_hasta_r."'))".
						"    OR ( stavac='3' AND (fecreivac between '".$ld_desde_r."' AND '".$ld_hasta_r."')))".
						"   AND pagpersal='0' ".
						"UNION ".
						"SELECT codvac, sueintbonvac, sueintvac, fecdisvac, fecreivac, diavac, diabonvac, diaadibon, diaadivac, ".
						"		diafer, sabdom, quisalvac, quireivac ".
						"  FROM sno_vacacpersonal ".
						" WHERE codper='".$as_codper."' ".
						"   AND ((stavac='2' AND (fecdisvac between '".$ld_desde_r."' AND '".$ld_hasta_r."'))".
						"    OR ( stavac='3' AND (fecreivac between '".$ld_desde_r."' AND '".$ld_hasta_r."')))".
						"   AND pagpersal='1'";				
				break;
		}
		if($ls_metodovacaciones!="0")
		{
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data==false)
			{
				$this->io_mensajes->message("CLASE->Evaluador MÉTODO-> uf_crear_proxima_vacacionpersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$la_vacacionpersonal=$row;   
					$_SESSION["la_proxvacacionpersonal"]=$la_vacacionpersonal;
					$ld_fecdisvac=$_SESSION["la_proxvacacionpersonal"]["fecdisvac"];
					$ld_fecreivac=$_SESSION["la_proxvacacionpersonal"]["fecreivac"];
					
				}
				else
				{
					$_SESSION["la_proxvacacionpersonal"]["fecdisvac"]='1900-01-01';
					$_SESSION["la_proxvacacionpersonal"]["fecreivac"]='1900-01-01';
				}
			}
			$this->io_sql->free_result($rs_data);
		}
		else
		{
				$_SESSION["la_proxvacacionpersonal"]["fecdisvac"]='1900-01-01';
				$_SESSION["la_proxvacacionpersonal"]["fecreivac"]='1900-01-01';
		}	
		return $lb_valido;
	}// end function  uf_crear_proxima_vacacionpersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_sueldo_quincena_ante($as_codper,$as_periodo,$as_anocur,$ai_sueldo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_sueldo_quincena_ante
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueldo=0;
		$ls_concepto=$this->io_sno->uf_select_config("SNO","CONFIG","CONCEPTO_SUELDO_ANT","XXXXXXXXXX","C");
		$ls_sql="SELECT sum(valsal) as valsal ".
				"  FROM sno_hsalida ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."'".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."'".
				"   AND sno_hsalida.anocur='".$as_anocur."'".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   AND sno_hsalida.codconc='".$ls_concepto."'".
				"   AND sno_hsalida.codperi='".$as_periodo."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_sueldo_quincena_ante ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_sueldo=number_format((float)$rs_data->fields["valsal"],2,".","");
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_sueldo']=$ai_sueldo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_sueldo_quincena_ante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_montoguarderia($as_codper,$ai_montoguard)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_sueldo_quincena_ante
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_montoguard=0;
		$ls_sql=" SELECT sum(monto) as monto ".
				" FROM sno_guarderias ".
				" WHERE sno_guarderias.codemp='".$this->ls_codemp."' ".
				" AND sno_guarderias.codper='".$as_codper."'";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_montoguarderia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_montoguard=number_format((float)$rs_data->fields["monto"],2,".","");
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_montoguard']=$ai_montoguard;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_sueldo_quincena_ante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_salarionormal_mes($as_codper,$as_mes,$as_anocur,$ai_sueldo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_salarionormal_mes
		//		   Access: private
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/08/2008 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueldo=0;
		$li_mesact=substr($_SESSION["la_nomina"]["fechasper"],5,2);
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_sql="SELECT SUM(sno_hsalida.valsal)  as total".
				"  FROM sno_hsalida, sno_hconcepto, sno_hperiodo ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."'".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."'".
				"   AND sno_hsalida.anocur='".$as_anocur."'".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   AND sno_hsalida.codperi <> '".$_SESSION["la_nomina"]["peractnom"]."'".
				"   AND sno_hconcepto.sigcon='A'".
				"   AND sno_hconcepto.salnor='1'".
				"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2) = '".$as_mes."' ".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi ".
				"   AND sno_hsalida.codemp=sno_hconcepto.codemp".
				"   AND sno_hsalida.codnom=sno_hconcepto.codnom".
				"   AND sno_hsalida.anocur=sno_hconcepto.anocur".
				"   AND sno_hsalida.codperi=sno_hconcepto.codperi".
				"   AND sno_hsalida.codconc=sno_hconcepto.codconc";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_salarionormal_mes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_sueldo=number_format((float)$rs_data->fields["total"],2,".","");
			}
		}
		$arrResultado['ai_sueldo']=$ai_sueldo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_obtener_salarionormal_mes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_salarionormal_periodo($as_codper,$as_periodo,$as_anocur,$ai_sueldo)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_sueldo_quincena_ante
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueldo=0;
		$ls_sql="SELECT sum(valsal) as valsal ".
				"  FROM sno_hsalida, sno_hconcepto ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."'".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."'".
				"   AND sno_hsalida.anocur='".$as_anocur."'".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   AND sno_hsalida.codperi='".$as_periodo."'".
				"   AND sno_hconcepto.salnor='1' ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_sueldo_quincena_ante ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_sueldo=number_format((float)$rs_data->fields["valsal"],2,".","");
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_sueldo']=$ai_sueldo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_obtener_salarionormal
	//-----------------------------------------------------------------------------------------------------------------------------------


	function uf_obtener_tipo_preaviso($as_codper,$ai_tipo_preaviso)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_sueldo_quincena_ante
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_tipo_preaviso=0;
		$ls_sql=" SELECT preaviso ".
				" FROM sno_personal ".
				" WHERE sno_personal.codemp='".$this->ls_codemp."' ".
				" AND sno_personal.codper='".$as_codper."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_tipo_preaviso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_tipo_preaviso=$rs_data->fields["preaviso"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_tipo_preaviso']=$ai_tipo_preaviso;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_sueldo_quincena_ante
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_tipo_egreso($as_codper,$ai_egreso)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_tipo_egreso
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_egreso=0;
		$ls_sql=" SELECT cauegrper ".
				" FROM sno_personal ".
				" WHERE sno_personal.codemp='".$this->ls_codemp."' ".
				" AND sno_personal.codper='".$as_codper."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_tipo_egreso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_egreso=$rs_data->fields["cauegrper"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_egreso']=$ai_egreso;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_tipo_egreso
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_ultimo_sueldo_integral($as_codper,$ai_sueint)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_ultimo_sueldo_integral
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueint=0;
		$lb_ckin=0;
		$ls_sql="SELECT confij, convar ".
				"  FROM sno_sueldoshistoricos ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codper='".$as_codper."' ".
				" ORDER BY fecsue DESC ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_ultimo_sueldo_integral ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if (!$rs_data->EOF)
			{
				$ai_sueint=number_format((float)($rs_data->fields["confij"]+$rs_data->fields["convar"]),2,'.','');
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_sueint']=$ai_sueint;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_ultimo_sueldo_integral
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_ultimo_sueldo_integral_promedio($as_codper,$ai_cantidad,$ai_sueint)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_ultimo_sueldo_integral_promedio
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueint=0;
		$lb_ckin=0;
		$ls_sql="SELECT confij, convar ".
				"  FROM sno_sueldoshistoricos ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codper='".$as_codper."' ".
				" ORDER BY fecsue DESC LIMIT ".intval($ai_cantidad);
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_ultimo_sueldo_integral_promedio ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ai_sueint= $ai_sueint + number_format((float)($rs_data->fields["confij"]+$rs_data->fields["convar"]),2,'.','');
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_sueint']=$ai_sueint;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_ultimo_sueldo_integral_promedio
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_obtener_ultimo_sueldo_integral_anterior($as_codper,$ad_fechacomp,$ai_sueint)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_ultimo_sueldo_integral
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueint=0;
		$lb_ckin=0;
		$ls_concat="";
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":				
				$ls_concat=$ls_concat." CONCAT(sno_sueintegral.anosue,'-',sno_sueintegral.messue,'-',01)";
			break;
			case "MYSQLI":				
				$ls_concat=$ls_concat." CONCAT(sno_sueintegral.anosue,'-',sno_sueintegral.messue,'-',01)";
			break;
			case "POSTGRES":
				$ls_concat=$ls_concat."sno_sueintegral.anosue||'-'||sno_sueintegral.messue||'-'||01";
			break;
		}
		
		$ls_sql=" SELECT sueint, messue, anosue ".
				" FROM sno_sueintegral  ".
				" WHERE sno_sueintegral.codemp='".$this->ls_codemp."' ".
				" AND sno_sueintegral.codper='".$as_codper."' ".
				" ORDER BY CAST(($ls_concat) AS DATE) DESC ";
		$rs_data=$this->io_sql->select($ls_sql);
		
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_ultimo_sueldo_integral_anterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ((!$rs_data->EOF)&&($lb_ckin==0))
			{
				$ai_sueint=$rs_data->fields["sueint"];
				$lb_ckin++;
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_sueint']=$ai_sueint;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_ultimo_sueldo_integral_anterior
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_ultimo_sueldo_integral_anterior_promedio($as_codper,$ad_fechacomp,$ai_cantidad,$ai_sueint)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_ultimo_sueldo_integral
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueint=0;
		$ls_concat="";
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":				
				$ls_concat=$ls_concat." CONCAT(sno_sueintegral.anosue,'-',sno_sueintegral.messue,'-',01)";
			break;
			case "MYSQLI":				
				$ls_concat=$ls_concat." CONCAT(sno_sueintegral.anosue,'-',sno_sueintegral.messue,'-',01)";
			break;
			case "POSTGRES":
				$ls_concat=$ls_concat."sno_sueintegral.anosue||'-'||sno_sueintegral.messue||'-'||01";
			break;
		}
		
		$ls_sql=" SELECT sueint, messue, anosue ".
				" FROM sno_sueintegral  ".
				" WHERE sno_sueintegral.codemp='".$this->ls_codemp."' ".
				" AND sno_sueintegral.codper='".$as_codper."' ".
				" ORDER BY CAST(($ls_concat) AS DATE) DESC LIMIT $ai_cantidad";
		$rs_data=$this->io_sql->select($ls_sql);
		
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_ultimo_sueldo_integral_anterior_promedio ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ai_sueint=$rs_data->fields["sueint"]+$ai_sueint;
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_sueint']=$ai_sueint;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_ultimo_sueldo_integral_anterior_promedio
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_sueldointegral_vac($as_codper,$ai_sueintvac,$as_profueper,$ad_fecdisvac,$as_nomina)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_sueldointegral_vac
		//		   Access: private
		//	    Arguments: as_codper  // código de personal
		//	    		   ai_sueintvac  // Sueldo Integral de Vacaciones
		//	      Returns: lb_valido True si se obtuvo el sueldo integral de vacaciones correctamente ó False si hubo algún error
		//	  Description: función que dado el código de personal obtiene el sueldo integral 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 16/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueintvac=0;
		$lb_mes0=false;
		$ld_fecha=$_SESSION["la_nomina"]["fecdesper"];
		$ls_codvac=0;
		if($as_profueper==1)
		{
			$ld_fecha=$ad_fecdisvac;
		}
		$ls_mesant=str_pad((intval(substr($ld_fecha,5,2))-1),2,"0",0);
		if($ls_mesant=="00")
		{
			if(array_key_exists("la_vacacionpersonal",$_SESSION))
			{
				$ls_codvac=$_SESSION["la_vacacionpersonal"]["codvac"];
			}
			$ls_sql="SELECT sueintvac as total ".
					"  FROM sno_vacacpersonal ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codper='".$as_codper."' ".
					"   AND codvac=".$ls_codvac." ";
			$lb_mes0=true;
		}
		else
		{
			$ls_anoant=str_pad((intval(substr($ld_fecha,0,4))),4,"0",0);
			$ls_sql="SELECT COALESCE(sum(valsal),0.00) as total ".
					"  FROM sno_hsalida, sno_hperiodo ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$as_nomina."' ".
					"   AND sno_hsalida.codper='".$as_codper."' ".
					"   AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='P1') ".
					"   AND SUBSTR(cast(sno_hperiodo.fechasper as char(10)),6,2)='".$ls_mesant."' ".
					"   AND SUBSTR(cast(sno_hperiodo.fechasper as char(10)),1,4)='".$ls_anoant."' ".
					"   AND sno_hsalida.codconc IN (SELECT codconc ".
					"					 			   FROM sno_concepto ".
					"				      			  WHERE codemp='".$this->ls_codemp."' ".
					"					    			AND codnom='".$as_nomina."' ".
					"					    			AND sueintvaccon=1)".
					"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
					"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
					"   AND sno_hsalida.codperi = sno_hperiodo.codperi ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_load_sueldointegral_vac ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ai_sueintvac=$rs_data->fields["total"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		if((!$lb_mes0)&&($ai_sueintvac==0))
		{
			if(array_key_exists("la_vacacionpersonal",$_SESSION))
			{
				$ls_codvac=$_SESSION["la_vacacionpersonal"]["codvac"];
			}
			$ls_sql="SELECT sueintvac as total ".
					"  FROM sno_vacacpersonal ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codper='".$as_codper."' ".
					"   AND codvac=".$ls_codvac." ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_load_sueldointegral_vac ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if(!$rs_data->EOF)
				{
					$ai_sueintvac=$rs_data->fields["total"];
				}
				$this->io_sql->free_result($rs_data);	
			}
		}
		$arrResultado['ai_sueintvac']=$ai_sueintvac;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_load_sueldointegral_vac
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_anio_trabajado_anterior($as_codper,$ai_anio,$ai_mes,$ai_dia,$ai_calant)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_ultimo_sueldo_integral
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_anio=0;
		$ai_mes=0;
		$ai_dia=0;
                $ls_criterio = "";
                if ($ai_calant != "")
                {
                    $ls_criterio = "   AND calant=1 ";
                }
		$ls_sql="SELECT anolab, meslab, dialab ".
				"  FROM sno_trabajoanterior ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codper='".$as_codper."' ".
				"   AND emppubtraant='1' ".
                                $ls_criterio.
				" ORDER BY codtraant ";
                
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_anio_trabajado_anterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ai_anio=$rs_data->fields["anolab"]+$ai_anio;
				$ai_mes=$rs_data->fields["meslab"]+$ai_mes;
				$ai_dia=$rs_data->fields["dialab"]+$ai_dia;
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
			$ai_dias=floor($ai_dia/30);
			if($ai_dias>0)
			{
				$ai_mes=$ai_mes+$ai_dias;
				$ai_dia = $ai_dia -($ai_dias*30);
			}
			$ai_meses=floor($ai_mes/12);
			if($ai_meses>0)
			{
				$ai_anio=$ai_anio+$ai_meses;
				$ai_mes = $ai_mes -($ai_meses*12);
			}
		}
		$arrResultado['ai_anio']=$ai_anio;
		$arrResultado['ai_mes']=$ai_mes;
		$arrResultado['ai_dia']=$ai_dia;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_ultimo_sueldo_integral_promedio

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_horatraslado($as_codper,$as_codnom,$ai_tietras)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_sueldo_quincena_ante
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_tietras=0;
		$ls_sql=" SELECT sno_ubicacionfisica.tietras ".
				" FROM sno_ubicacionfisica, sno_personalnomina ".
				" WHERE sno_ubicacionfisica.codemp='".$this->ls_codemp."' ".
				" AND sno_personalnomina.codper='".$as_codper."' ".
				" AND sno_personalnomina.codnom='".$as_codnom."' ".
				" AND sno_ubicacionfisica.codubifis=sno_personalnomina.codubifis ";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_horatraslado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_tietras=$rs_data->fields["tietras"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_tietras']=$ai_tietras;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_sueldo_quincena_ante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_categoria_militar($as_codper,$as_categoria)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_categoria_militar
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	    		   as_categoria // categoria militar
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que devuelve la categoria militar que tiene el personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/06/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_categoria="0000000000";
		$ls_sql="SELECT sno_rango.codcat ".
				"  FROM sno_personal ".
				" INNER JOIN sno_rango ".
				"    ON sno_personal.codemp = '".$this->ls_codemp."'".
				"   AND sno_personal.codper = '".$as_codper."'".
				"   AND sno_personal.codemp = sno_rango.codemp ".
				"   AND sno_personal.codcom = sno_rango.codcom".
				"   AND sno_personal.codran = sno_rango.codran".
				" WHERE sno_personal.codemp='".$this->ls_codemp."'".
				"   AND sno_personal.codper='".$as_codper."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_categoria_militar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$as_categoria=$rs_data->fields["codcat"];
			}
		}
		$arrResultado['as_categoria']=$as_categoria;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_categoria_militar
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_personal($as_codper,$as_cedper,$as_valor)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_personal
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	    		   as_valor // valor
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que devuelve la categoria militar que tiene el personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/06/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_valor=0;
		$ls_sql="SELECT codper  ".
				"  FROM sno_personalnomina ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codper='".$as_codper."' ".
				"   AND '".$as_cedper."' NOT IN (SELECT cedben FROM sno_beneficiario WHERE codemp='".$this->ls_codemp."' AND cedben='".$as_cedper."')".
				"   AND '".$as_cedper."' NOT IN (SELECT cedaut FROM sno_beneficiario WHERE codemp='".$this->ls_codemp."' AND cedaut='".$as_cedper."')";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_verificar_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$as_valor=true;
			}
		}
		$arrResultado['as_valor']=$as_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_verificar_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_beneficiario($as_codper,$as_cedper,$as_valor)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_beneficiario
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	    		   as_valor // valor
		//	      Returns: lb_valido True si se creo la variable sesion ó False si no se creo
		//	  Description: función que devuelve la categoria militar que tiene el personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/06/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_valor=0;
		$ls_sql="SELECT codper  ".
				"  FROM sno_personalnomina ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codper='".$as_codper."' ".
				"   AND ('".$as_cedper."' IN (SELECT cedben FROM sno_beneficiario WHERE codemp='".$this->ls_codemp."' AND cedben='".$as_cedper."')".
				"   OR '".$as_cedper."'  IN (SELECT cedaut FROM sno_beneficiario WHERE codemp='".$this->ls_codemp."' AND cedaut='".$as_cedper."'))";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_verificar_beneficiario ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$as_valor=true;
			}
		}
		$arrResultado['as_valor']=$as_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_verificar_beneficiario
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_intereses_prestacion_acumulada($ad_anodes,$as_codper,$ai_acumulado)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_prestacion_antiguedad_acumulada
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   ai_monto // Monto
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal busca la prestación antiguedad Acumulada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_acumulado=0;
		
		$ls_sql=" SELECT SUM(monint) as monint ".
				" FROM sno_fideiperiodointereses ".
				" WHERE codemp='".$this->ls_codemp."' ".
				" AND codnom='".$this->ls_codnom."'".
				" AND codper='".$as_codper."' ".
				" AND anocurper='".$ad_anodes."' ".
				" GROUP BY codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_intereses_prestacion_acumulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_acumulado=number_format((float)($rs_data->fields["monint"]),2,".","");
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_acumulado']=$ai_acumulado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_intereses_prestacion_acumulada
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_prestacion_antiguedad_acumulada($ad_anodes,$as_codper,$ai_acumulado)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_prestacion_antiguedad_acumulada
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   ai_monto // Monto
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal busca la prestación antiguedad Acumulada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_acumulado=0;
		
		$ls_sql=" SELECT SUM(apoper) as aporte ".
				" FROM sno_fideiperiodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				" AND codper='".$as_codper."' ".
				" AND anocurper='".$ad_anodes."' ".
				" GROUP BY codper ";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_prestacion_antiguedad_acumulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_acumulado=number_format((float)($rs_data->fields["aporte"]),2,".","");
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_acumulado']=$ai_acumulado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_prestacionantiguedad
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_prestacion_antiguedad_acumulada_anteriores($ad_anodes,$ad_anoing,$as_codper,$ai_acumulado)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_prestacion_antiguedad_acumulada_anteriores
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   ai_monto // Monto
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal busca la prestación antiguedad Acumulada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_acumulado=0;
		$ad_anodes=$ad_anodes-1;
		
		$ls_sql=" SELECT SUM(apoper) as aporte ".
				" FROM sno_fideiperiodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				" AND codper='".$as_codper."' ".
				" AND anocurper BETWEEN '".$ad_anoing."' AND '".$ad_anodes."' ".
				" GROUP BY codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_prestacion_antiguedad_acumulada_anteriores ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_acumulado=number_format((float)($rs_data->fields["aporte"]),2,".","");
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_acumulado']=$ai_acumulado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_prestacion_antiguedad_acumulada_anteriores
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_intereses_prestacion_acumulada_anteriores($ad_anodes,$ad_anoing,$as_codper,$ai_acumulado)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_prestacion_antiguedad_acumulada_anteriores
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   ai_monto // Monto
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal busca la prestación antiguedad Acumulada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_acumulado=0;
		$ad_anodes=$ad_anodes-1;
		
		$ls_sql=" SELECT SUM(monint) as monint ".
				" FROM sno_fideiperiodointereses ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."'".
				" AND codper='".$as_codper."' ".
				" AND anocurper BETWEEN '".$ad_anoing."' AND '".$ad_anodes."' ".
				" GROUP BY codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_intereses_prestacion_acumulada_anteriores ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ((!$rs_data->EOF))
			{
				$ai_acumulado=number_format((float)($rs_data->fields["monint"]),2,".","");
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_acumulado']=$ai_acumulado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_intereses_prestacion_acumulada_anteriores
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_prestacionantiguedad($as_codper,$as_tipo,$ai_monto)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_prestacionantiguedad
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   ai_monto // Monto
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal busca la prestación antiguedad Acumulada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_monto=0;
		$ls_sql="SELECT SUM(monant) AS monantant, 0 as monpreant,  ".
				"		(SELECT SUM(monant) ".
				"          FROM sno_anticipoprestaciones ".
				"         WHERE codemp='".$this->ls_codemp."'".
				"           AND codper='".$as_codper."'".
				"           AND estant='X'".
				"         GROUP BY codper) AS monantantanu ".
				"  FROM sno_anticipoprestaciones ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ".
				" UNION ".
				"SELECT 0 AS monantant, SUM(apoper) as monpreant, 0 AS monantantanu  ".
				"  FROM sno_fideiperiodo ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ".
				" UNION ".
				"SELECT SUM(monant) AS monantant, SUM(monpreant) as monpreant,  0 AS monantantanu".
				"  FROM sno_deudaanterior ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_prestacionantiguedad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while(!$rs_data->EOF)
			{
				switch($as_tipo)
				{
					case "0": // para obtener el valor Neto de la Prestacion
						$ai_monto=$ai_monto+number_format((float)($rs_data->fields["monpreant"]-($rs_data->fields["monantant"]-$rs_data->fields["monantantanu"])),2,".","");
					break;
					
					case "1": // para obtener el valor Acumulado de la Prestacion
						$ai_monto=$ai_monto+number_format((float)$rs_data->fields["monpreant"],2,".","");
					break;
					
					case "2": // para obtener el valor de los Anticipos de Prestacion
						$ai_monto=$ai_monto+number_format((float)(($rs_data->fields["monantant"]-$rs_data->fields["monantantanu"])),2,".","");
					break;
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_monto']=$ai_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_prestacionantiguedad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_interesesprestacion($as_codper,$as_tipo,$ai_monto)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_interesesprestacion
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   ai_monto // Monto
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal busca la prestación antiguedad Acumulada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_monto=0;
		$ls_sql="SELECT SUM(monint) AS monantint, 0 as monintant, ".
				"		(SELECT SUM(monint) ".
				"          FROM sno_anticipoprestaciones ".
				"         WHERE codemp='".$this->ls_codemp."'".
				"           AND codper='".$as_codper."'".
				"           AND estant='X'".
				"         GROUP BY codper) AS monantintanu ".
				"  FROM sno_anticipoprestaciones ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ".
				" UNION ".
				"SELECT 0 AS monantint, SUM(monint) as monintant, 0 AS monantintanu  ".
				"  FROM sno_fideiperiodointereses ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ".
				" UNION ".
				"SELECT 0 AS monantint, SUM(monint) as monintant,  0 AS monantintanu  ".
				"  FROM sno_deudaanterior ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_interesesprestacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while(!$rs_data->EOF)
			{
				switch($as_tipo)
				{
					case "0": // para obtener el valor Neto de los Intereses
						$ai_monto=$ai_monto+number_format((float)($rs_data->fields["monintant"]-($rs_data->fields["monantint"]-$rs_data->fields["monantintanu"])),2,".","");
					break;
					
					case "1": // para obtener el valor Acumulado de los Intereses
						$ai_monto=$ai_monto+number_format((float)$rs_data->fields["monintant"],2,".","");
					break;
					
					case "2": // para obtener el valor de los Anticipos de los Intereses
						$ai_monto=$ai_monto+number_format((float)($rs_data->fields["monantint"]-$rs_data->fields["monantintanu"]),2,".","");
					break;
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_monto']=$ai_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_interesesprestacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_sueldopromedioanterior($as_codper,$ai_nromeses,$ad_meshas,$ad_anohas)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_sueldoanterior
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   ai_monto // Monto
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal busca la prestación antiguedad Acumulada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_monto=0;
		$ad_fecact='1900-01-01';
		$ad_fecprom='1900-01-01';
		$ad_mes=intval($ad_meshas);
		$ad_anio=intval($ad_anohas);
		for($li_i=1;$li_i<=$ai_nromeses;$li_i++)
		{
			$ad_mes=intval($ad_mes-1);
			if($ad_mes==0)
			{
				$ad_mes=12;
				$ad_anio=intval($ad_anio-1);
			}
			if ($li_i==1)
			{
				$ad_fecact=$ad_anio."-".str_pad($ad_mes,2,'0',0)."-01";
				$ad_fecprom="'".$ad_anio."-".str_pad($ad_mes,2,'0',0)."-01'";
			}
			else
			{
				$ad_fecprom .= ",'".$ad_anio."-".str_pad($ad_mes,2,'0',0)."-01'";
			}			
		}
		
		$ls_sql="SELECT sum(confij) AS fijos,".
				"		(SELECT SUM(convar)/".$ai_nromeses." ".
				"          FROM sno_sueldoshistoricos ".
				"         WHERE codemp='".$this->ls_codemp."'".
				"           AND codper='".$as_codper."'".
				"         AND fecsue IN (".$ad_fecprom.")".
				"         GROUP BY codper) AS variable ".
				"  FROM sno_sueldoshistoricos ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND fecsue = '".$ad_fecact."'".
				" GROUP BY codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_obtener_sueldopromedioanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ai_monto=number_format((float)($rs_data->fields["fijos"]+($rs_data->fields["variable"])),2,".","");
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $ai_monto;
	}// end function uf_obtener_sueldopromedioanterior
	//-----------------------------------------------------------------------------------------------------------------------------------

	// Comentario de Ofimatica de Venezuela 27-07-2011
	//
	// Este codigo es unica y exclusivamente para uso por parte del IPSFA, ya que la consulta esta cerrada y ajustada a ellos.
	// lo cual no debe ser la forma de mejorar o ajustar el codigo del sistema SIGESP, ya que por ser abierto, los nuevos desarrollos o ajustes
	// tambien deben serlo, es decir, que puedan ser usados por todos los clientes que usen o instalen el SIGESP
	//
	function uf_antiguedad_complementaria($codper)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_antiguedad_complementaria
		//		   Access: private
		//	    Arguments: codper		  
		//	      Returns: El valor del pago total de días adicionales o false si hay un error
		//	  Description: función que devuelve el calculo de los días de antiguedad complementaria
		//	   Creado Por: Lic. Edgar A. Quintero U.
		// Fecha Creación: 10/11/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$meses = intval($_SESSION["la_nomina"]["peractnom"]);
		$dias = $meses*30;
		
		$ls_sql=" SELECT (
							   ((sum(valsal)/12)/30) +
							   ((((sum(valsal)/12)/30)*40)/360) + 
							   ((((sum(valsal)/12))*3)/360) +
							   (
								SELECT sum(abs(valsal))/360 FROM sno_hsalida hs
								INNER JOIN sno_concepto c ON hs.codconc = c.codconc AND hs.codemp = c.codemp AND hs.codnom = c.codnom
								WHERE  hs.codper = '".$codper."' AND hs.codemp='".$this->ls_codemp."'  
								AND c.aplidiasadd = 1 AND  hs.codnom<'0006' AND tipsal!='P2'
							   )
						   )*(
								   CASE  (to_number((
													SELECT 
													(
													(((DATE_PART('YEAR',CURRENT_DATE)-DATE_PART('YEAR',fecingper))* 372 + (DATE_PART('MONTH',CURRENT_DATE) - DATE_PART('MONTH',fecingper))*31 + (DATE_PART('DAY',CURRENT_DATE)-DATE_PART('DAY',fecingper)))/372)
													) 
													FROM sno_personal
													WHERE codper = '".$codper."' AND codemp='".$this->ls_codemp."'  
									), '99') + (SELECT anoperobr FROM sno_personal WHERE codper = '".$codper."' AND codemp='".$this->ls_codemp."'))
									WHEN 0 THEN 0
									WHEN 1 THEN 0
									WHEN 2 THEN 2
									WHEN 3 THEN 4
									WHEN 4 THEN 6
									WHEN 5 THEN 8
									WHEN 6 THEN 10
									WHEN 7 THEN 12
									WHEN 8 THEN 14
									WHEN 9 THEN 16
									WHEN 10 THEN 18
									WHEN 11 THEN 20
									WHEN 12 THEN 22
									WHEN 13 THEN 24									
									ELSE 24       
								   END 
						   ) AS total
					FROM sno_hsalida hs
					INNER JOIN sno_concepto c ON hs.codconc = c.codconc AND hs.codemp = c.codemp AND hs.codnom = c.codnom
					WHERE hs.codper = '".$codper."' AND hs.codemp='".$this->ls_codemp."' AND hs.codnom<'0006' AND tipsal!='P2'
					AND (sueintcon = 1 OR hs.codconc IN ('0000000020','0000000022','0000000035','0000000036'))";
		
		$rs_data=$this->io_sql->select($ls_sql);
		
		if($rs_data===false)
		{
			echo "CLASE->Evaluador MÉTODO->uf_antiguedad_complementaria ERROR->".$this->io_sql->message;
			$this->io_mensajes->message('ERROR: METODO: uf_antiguedad_complementaria CLASE: Evaluador');
			return false;
		}
		
		return $rs_data->fields["total"];
	}// end function uf_antiguedad_complementaria
//--------------------------------------------------------------------------------------------------------------------------------------
function uf_aplica_beca($as_codper)
{	
	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_aplica_beca
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_codconc // código del concepto
		//				   as_criterio // Criterio de Busqueda
		//				   ab_cobrado // si el concepto es distinto de cero
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y el concepto verifica si dicho concepto fue pagado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/08/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ai_valor=0;
		$ls_sql=" SELECT estbec ".
				"  FROM sno_familiar ".
				" WHERE sno_familiar.codemp='".$this->ls_codemp."'".
				"   AND sno_familiar.codper='".$as_codper."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_aplica_beca ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_valor=$row["estbec"];
				if($ai_valor=='1')
				{
					$lb_valido=true;
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
		}// end function uf_aplica_beca
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_cantidad_becados($as_codper,$ls_nivaca,$ls_cantidad)
{	
	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cantidad_becados
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_codconc // código del concepto
		//				   as_criterio // Criterio de Busqueda
		//				   ab_cobrado // si el concepto es distinto de cero
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y el concepto verifica si dicho concepto fue pagado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/08/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_cantidad=0;
		$ls_sql=" SELECT COUNT (nivaca) as total ".
				"  FROM sno_familiar ".
				" WHERE sno_familiar.codemp='".$this->ls_codemp."'".
				"   AND sno_familiar.codper='".$as_codper."' ".
				"   AND sno_familiar.nivaca='".$ls_nivaca."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_cantidad_becados ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_cantidad=$row["total"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ls_cantidad']=$ls_cantidad;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_aplica_beca
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_estudiorealizado($as_codper, $ai_estcur,$ai_cantidad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_estudiorealizado
		//		   Access: private
		//	    Arguments: as_codper  // código del personal
		//				   ai_codestrea  // código estudio realizado
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si el estudiorealizado está registrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ai_cantidad=1;
		$ls_sql="SELECT codestrea FROM sno_estudiorealizado ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND aprestrea='".$ai_estcur."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_select_estudiorealizado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if(!$row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_cantidad=0;
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_cantidad']=$ai_cantidad;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_select_estudiorealizado
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cantidad_periodos_venc($as_codper,$ls_cantidad)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cantidad_periodos_venc
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y el concepto verifica si dicho concepto fue pagado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/08/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_cantidad=0;
		$ls_sql=" SELECT COUNT (stavac) as total ".
				"  FROM sno_vacacpersonal ".
				" WHERE sno_vacacpersonal.codemp='".$this->ls_codemp."'".
				"   AND sno_vacacpersonal.codper='".$as_codper."' ".
				"   AND sno_vacacpersonal.stavac=1 ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_cantidad_periodos_venc ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_cantidad=$row["total"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ls_cantidad']=$ls_cantidad;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_cantidad_periodos_venc
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_fechaperiodo($as_codperi,$ad_fechadesde,$ad_fechahasta)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cantidad_periodos_venc
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y el concepto verifica si dicho concepto fue pagado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/08/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fechadesde='1900-01-01';
		$ad_fechahasta='1900-01-01';
		
		$ls_sql=" SELECT fecdesper, fechasper ".
				"  FROM sno_periodo ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codperi='".$as_codperi."' ".
				"   AND SUBSTR(CAST(fechasper AS CHAR(10)),1,4) = '".$_SESSION["la_nomina"]["anocurnom"]."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_cantidad_periodos_venc ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ad_fechadesde=$rs_data->fields["fecdesper"];
				$ad_fechahasta=$rs_data->fields["fechasper"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ad_fechadesde']=$ad_fechadesde;
		$arrResultado['ad_fechahasta']=$ad_fechahasta;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_cantidad_periodos_venc

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_ultimo_calculo($as_codper,$as_codnom)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_ultimo_calculo
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y el concepto verifica si dicho concepto fue pagado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/08/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fechahasta='1900-01-01';
		$ls_sql=" SELECT sno_periodo.fechasper ".
				"  FROM sno_periodo, sno_hresumen ".
				" WHERE sno_hresumen.codemp='".$this->ls_codemp."'".
				"   AND sno_hresumen.codnom='".$as_codnom."' ".
				"   AND sno_hresumen.codper='".$as_codper."' ".
				"   AND sno_periodo.codemp = sno_hresumen.codemp ".
				"   AND sno_periodo.codnom = sno_hresumen.codnom ".
				"   AND sno_periodo.codperi = sno_hresumen.codperi ".
				"   ORDER BY fechasper DESC ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_cantidad_periodos_venc ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ad_fechahasta=$rs_data->fields["fechasper"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ad_fechahasta']=$ad_fechahasta;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_cantidad_periodos_venc

//-----------------------------------------------------------------------------------------------------------------------------------

        
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_tipoescuela($as_codper, $as_campo,$ai_valor)
	{
		$ai_valor=0;
                $lb_valido=true;
		$ls_sql="SELECT ".$as_campo." AS campo ".
                        "  FROM sno_ubicacionfisica, sno_personalnomina, sno_tipoescuela ".
			" WHERE sno_personalnomina.codemp='".$this->ls_codemp."'".
			"   AND sno_personalnomina.codper='".$as_codper."'".
			"   AND sno_tipoescuela.".$as_campo."='1'".
			"   AND sno_personalnomina.codemp=sno_ubicacionfisica.codemp ".
			"   AND sno_personalnomina.codubifis=sno_ubicacionfisica.codubifis ".
			"   AND sno_ubicacionfisica.codemp=sno_tipoescuela.codemp ".
			"   AND sno_ubicacionfisica.codtipesc=sno_tipoescuela.codtipesc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Evaluador MÉTODO->uf_load_tipoescuela ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
                    if(!$rs_data->EOF)
                    {
                        $ai_valor=1;
                    }
                    $this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_valor']=$ai_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_load_tipoescuela
	//-----------------------------------------------------------------------------------------------------------------------------------
        
}
?>