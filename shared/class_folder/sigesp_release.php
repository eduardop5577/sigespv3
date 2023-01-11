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

class sigesp_release 
{
	var $io_function;
	var $io_function_db;
	var $io_msg;
	var $io_include;
	var $io_connect;
	var $io_sql;
	
	public function __construct() 
	{
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_sql.php");  
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_include.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_funciones_db.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_vistas_db.php");  
		require_once("sigesp_c_seguridad.php");
		$this->io_function=new class_funciones();	
		$this->io_msg=new class_mensajes();
		$this->io_include=new sigesp_include();
		$this->io_connect=$this->io_include->uf_conectar();
		//$this->io_connect->debug=true;
		$this->io_sql=new class_sql($this->io_connect);
		$this->io_function_db=new class_funciones_db($this->io_connect);
		$this->io_vistas_db=new class_vistas_db($this->io_connect);						
		$this->io_seguridad=new sigesp_c_seguridad();		
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];		
	} //  end contructor

	function uf_destructor()
	{	
		unset($this->io_function);	
		unset($this->io_msg);				
		unset($this->io_include);				
		unset($this->io_connect);				
		unset($this->io_sql);	
		unset($this->io_seguridad);	
	} // end function uf_destructor

        function uf_check_update($aa_seguridad) // main()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_check_update
		//		   Access: public 
		//	  Description: chequea los updates
		//	   Creado Por: Ing. Wilmer Briceï¿½
		// Fecha Creaciï¿½: 06/07/2006 								Fecha ï¿½tima Modificaciï¿½ : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();	
		$ls_nro_release="";   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_table('spg_sigeproden_proyecto');
		if (!$lb_existe)
		{
			$this->io_msg->message(utf8_encode(" Release Version 2022_01_01 "));				   	   
			$lb_valido=$this->uf_create_release_db_libre_V_2022_01_01();
			$ls_nro_release.=" - 2022_01_01";   
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_table('spg_dt_sigeproden_proyecto');
		if (!$lb_existe)
		{
			$this->io_msg->message(utf8_encode(" Release Version 2022_01_02 "));				   	   
			$lb_valido=$this->uf_create_release_db_libre_V_2022_01_02();
			$ls_nro_release.=" - 2022_01_02";   
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_column('scv_solicitudviatico','tascam1');
                if(!$lb_existe)
                {
		    $this->io_msg->message(utf8_encode(" Release Version 2022_01_03"));	
			$lb_valido=$this->uf_create_release_db_libre_V_2022_01_03();
			$ls_nro_release.=" - 2022_01_03";   
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_type_columna('sigesp_dt_moneda','tascam1','numeric');
		if (!$lb_existe)
		{
			$this->io_msg->message(utf8_encode(" Release Version 2022_01_04 "));				   	   
			$lb_valido=$this->uf_create_release_db_libre_V_2022_01_04();
			$ls_nro_release.=" - 2022_01_04";   
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_column('sno_sueldoshistoricos','comsue');
                if(!$lb_existe)
                {
		    $this->io_msg->message(utf8_encode(" Release Version 2022_05_01"));	
			$lb_valido=$this->uf_create_release_db_libre_V_2022_05_01();
			$ls_nro_release.=" - 2022_05_01";   
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_column('spg_unidadadministrativa','resuniadm');
                if(!$lb_existe)
                {
		    $this->io_msg->message(utf8_encode(" Release Version 2022_05_02"));	
			$lb_valido=$this->uf_create_release_db_libre_V_2022_05_02();
			$ls_nro_release.=" - 2022_05_02";   
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_table('sno_enfermedad');
		if (!$lb_existe)
		{
			$this->io_msg->message(utf8_encode(" Release Version 2022_07_01 "));				   	   
			$lb_valido=$this->uf_create_release_db_libre_V_2022_07_01();
			$ls_nro_release.=" - 2022_07_01";   
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_table('sno_personalenfermedad');
		if (!$lb_existe)
		{
			$this->io_msg->message(utf8_encode(" Release Version 2022_07_02 "));				   	   
			$lb_valido=$this->uf_create_release_db_libre_V_2022_07_02();
			$ls_nro_release.=" - 2022_07_02";   
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_column('sno_personal','coreleins');
		if (!$lb_existe)
		{
			$this->io_msg->message(utf8_encode(" Release Version 2022_07_03 "));				   	   
			$lb_valido=$this->uf_create_release_db_libre_V_2022_07_03();
			$ls_nro_release.=" - 2022_07_03";   
		}		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_column('scv_otrasasignaciones','codmon');
                if(!$lb_existe)
                {
		    $this->io_msg->message(utf8_encode(" Release Version 2022_08_01"));	
			$lb_valido=$this->uf_create_release_db_libre_V_2022_08_01();
			$ls_nro_release.=" - 2022_08_01";   
		}
		$lb_existe = $this->io_function_db->uf_select_type_columna('sigesp_unidad_tributaria','decnro','character varying');
                if(!$lb_existe)
                {
		    $this->io_msg->message(utf8_encode(" Release Version 2022_08_02"));	
			$lb_valido=$this->uf_create_release_db_libre_V_2022_08_02();
			$ls_nro_release.=" - 2022_08_02";   
		}		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_column('sep_solicitud','codmon');
                if(!$lb_existe)
                {
		    $this->io_msg->message(utf8_encode(" Release Version 2022_12_01"));	
			$lb_valido=$this->uf_create_release_db_libre_V_2022_12_01();
			$ls_nro_release.=" - 2022_12_01";   
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_column('soc_ordencompra','monsubtotdiv');
                if(!$lb_existe)
                {
		    $this->io_msg->message(utf8_encode(" Release Version 2022_12_02"));	
			$lb_valido=$this->uf_create_release_db_libre_V_2022_12_02();
			$ls_nro_release.=" - 2022_12_01";   
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = $this->io_function_db->uf_select_column('sno_nomina','pasape');
                if(!$lb_existe)
                {
		    $this->io_msg->message(utf8_encode(" Release Version 2022_12_03"));	
			$lb_valido=$this->uf_create_release_db_libre_V_2022_12_03();
			$ls_nro_release.=" - 2022_12_03";   
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($lb_valido)
		{ 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Ejecutó el release ".$ls_nro_release;
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
	} // end function 
        

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_create_release_db_libre_V_2022_01_01()
	{
	   $lb_valido=true;
	   $ls_sql="";
	   switch($_SESSION["ls_gestor"])
	   {
		case "POSTGRES":
                    $ls_sql="CREATE TABLE spg_sigeproden_proyecto ( ".
                            "   codemp character(4) NOT NULL DEFAULT '0001',  ".
                            "   codprosig character(10) NOT NULL,  ".
                            "   despro text,  ".
                            "   nroptocta character varying(25) NOT NULL, ".
                            "   fecptocta date NOT NULL DEFAULT '1900-01-01', ".
                            "   monptocta double precision NOT NULL DEFAULT (0), ".
                            "   enteejecutor text, ".
                            "   rifenteejecutor character varying(15), ".
                            "   codmon character(3) NOT NULL DEFAULT '---', ".
                            "   codestpro1 character(25) NOT NULL,  ".
                            "   codestpro2 character(25) NOT NULL,  ".
                            "   codestpro3 character(25) NOT NULL,  ".
                            "   codestpro4 character(25) NOT NULL,  ". 
                            "   codestpro5 character(25) NOT NULL,  ".
                            "   estcla character(1) NOT NULL,  ".
                            "   spg_cuenta character varying(25) NOT NULL,  ".
                            "   codfuefin character(2) NOT NULL ,  ".
                            "   sc_cuentad character varying(25) NOT NULL, ".
                            "   sc_cuentah character varying(25) NOT NULL, ".
                            "   CONSTRAINT pk_spg_sigeproden_proyecto PRIMARY KEY (codemp, codprosig),  ".
                            "   CONSTRAINT fk_spg_sigeproden_proyecto___sigesp_empresa FOREIGN KEY (codemp)   ".
			    "       REFERENCES sigesp_empresa (codemp) ".
                            "       MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT,         ".
                            "   CONSTRAINT fk_spg_sigeproden_proyecto___sigesp_moneda FOREIGN KEY (codemp,codmon)   ".
			    "       REFERENCES sigesp_moneda (codemp,codmon) ".
                            "       MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT,         ".
                            "   CONSTRAINT fk_spg_sigeproden_proyecto___spg_cuenta_fuentefinanciamiento FOREIGN KEY (codemp, codfuefin, codestpro1, estcla, codestpro2, codestpro3, codestpro4, codestpro5, spg_cuenta)   ".
			    "       REFERENCES spg_cuenta_fuentefinanciamiento (codemp, codfuefin, codestpro1, estcla, codestpro2, codestpro3, codestpro4, codestpro5, spg_cuenta) ".
			    "	    MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT,         ".
                            "   CONSTRAINT fk_spg_sigeproden_proyecto___scg_cuentasd FOREIGN KEY (codemp, sc_cuentad)   ".
			    "       REFERENCES scg_cuentas (codemp, sc_cuenta) ".
			    "	    MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT, ".
                            "   CONSTRAINT fk_spg_sigeproden_proyecto___scg_cuentash FOREIGN KEY (codemp,sc_cuentah)   ".
			    "       REFERENCES scg_cuentas (codemp, sc_cuenta) ".
			    "	    MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT) WITHOUT OIDS;         ";
                break;
            }	
            
            $li_row=$this->io_sql->execute($ls_sql);
            if($li_row===false)
            { 
                $this->io_msg->message("Problemas al ejecutar Release 2022_01_01");
		$lb_valido=false;
            }
            
            return $lb_valido;	
	}// end function uf_create_release_db_libre_V_2022_01_01
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_create_release_db_libre_V_2022_01_02()
	{
	   $lb_valido=true;
	   $ls_sql="";
	   switch($_SESSION["ls_gestor"])
	   {
  		case "POSTGRES":
                    $ls_sql="CREATE TABLE spg_dt_sigeproden_proyecto ( ".
                            "   codemp character(4) NOT NULL DEFAULT '0001',  ".
                            "   codprosig character(10) NOT NULL,  ".
                            "   codmon character(3) NOT NULL DEFAULT '---', ".
                            "   procede character(6) NOT NULL,  ".
                            "   comprobante character(15) NOT NULL, ".
                            "   codban character(3) NOT NULL DEFAULT '---', ".
                            "   ctaban character(25) NOT NULL DEFAULT '-------------------------', ".
                            "   descripcion text NOT NULL, ".
                            "   fecha date NOT NULL DEFAULT '1900-01-01', ".
                            "   operacion character(3) NOT NULL, ".
                            "   tipo_destino character varying(1) NOT NULL, ".
                            "   cod_pro character(10) NOT NULL, ".
                            "   ced_bene character(10) NOT NULL, ".
                            "   tascam double precision NOT NULL DEFAULT 1, ".
                            "   monto double precision NOT NULL DEFAULT 0, ".
                            "   CONSTRAINT pk_spg_dt_sigeproden_proyecto PRIMARY KEY (codemp, codprosig, procede, comprobante, codban, ctaban),  ".
                            "   CONSTRAINT fk_spg_dt_sigeproden_proyecto___sigesp_cmp FOREIGN KEY (codemp, procede, comprobante, codban, ctaban)   ".
			    "       REFERENCES sigesp_cmp (codemp, procede, comprobante, codban, ctaban) ".
			    "	    MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT) WITHOUT OIDS;         ";
                break;
            }	
            
            $li_row=$this->io_sql->execute($ls_sql);
            if($li_row===false)
            { 
                $this->io_msg->message("Problemas al ejecutar Release 2022_01_02");
		$lb_valido=false;
            }
            
            return $lb_valido;	
	}// end function uf_create_release_db_libre_V_2022_01_02
    //-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_create_release_db_libre_V_2022_01_03()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_create_release_db_libre_V_2022_01_03
		//		   Access: public 
		//        Modulos: Configuracion
		//	  Description: 
		// Fecha Creacion: 24/04/2013								Fecha Ultima Modificacion : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   $lb_valido=true;
	   $ls_sql="";
		switch(strtoupper($_SESSION["ls_gestor"]))
	   {
			   
			case "POSTGRES":
 			   $ls_sql= " ALTER TABLE scv_solicitudviatico ADD COLUMN tascam1 double precision DEFAULT 0;";
		        break;	
			
		}
		if (!empty($ls_sql))
		{	
			 $li_row=$this->io_sql->execute($ls_sql);
			 if($li_row===false)
			 { 
				 $this->io_msg->message("Problemas al ejecutar Release 2013_10_02");
				 $lb_valido=false;
			 }
		}
	   return $lb_valido;			
	} // end function uf_create_release_db_libre_V_2022_01_03
	//-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_create_release_db_libre_V_2022_01_04()
	{
	   $lb_valido=true;
	   $ls_sql="";
	   switch($_SESSION["ls_gestor"])
	   {
  		case "POSTGRES":
                    $ls_sql="ALTER TABLE sigesp_dt_moneda ".
                            "ALTER COLUMN tascam1 TYPE numeric(20,8),".
                            "ALTER COLUMN tascam2 TYPE numeric(20,8);";
                break;
            }	
            $li_row=$this->io_sql->execute($ls_sql);
            if($li_row===false)
            { 
                $this->io_msg->message("Problemas al ejecutar Release 2022_01_04-01");
		$lb_valido=false;
            }
            if($lb_valido)
            {
                $ls_sql="";
                switch($_SESSION["ls_gestor"])
                {
                     case "POSTGRES":
                         $ls_sql="ALTER TABLE scb_movbco ".
                                 "ALTER COLUMN tascam TYPE numeric(20,8);";
                     break;
                 }	
                 $li_row=$this->io_sql->execute($ls_sql);
                 if($li_row===false)
                 { 
                     $this->io_msg->message("Problemas al ejecutar Release 2022_01_04-02");
                     $lb_valido=false;
                 }                
            }
            if($lb_valido)
            {
                $ls_sql="";
                switch($_SESSION["ls_gestor"])
                {
                    case "POSTGRES":
                        $ls_sql="ALTER TABLE scv_solicitudviatico ".
                                "ALTER COLUMN tascam1 TYPE numeric(20,8),".
                                "ALTER COLUMN tascamsol TYPE numeric(20,8);";
                     break;
                 }	
                 $li_row=$this->io_sql->execute($ls_sql);
                 if($li_row===false)
                 { 
                     $this->io_msg->message("Problemas al ejecutar Release 2022_01_04-03");
                     $lb_valido=false;
                 }                
            }
            if($lb_valido)
            {
                $ls_sql="";
                switch($_SESSION["ls_gestor"])
                {
                     case "POSTGRES":
                         $ls_sql="ALTER TABLE spg_dt_sigeproden_proyecto ".
                                 "ALTER COLUMN tascam TYPE numeric(20,8);";
                     break;
                 }	
                 $li_row=$this->io_sql->execute($ls_sql);
                 if($li_row===false)
                 { 
                     $this->io_msg->message("Problemas al ejecutar Release 2022_01_04-04");
                     $lb_valido=false;
                 }                
            }
            if($lb_valido)
            {
                $ls_sql="";
                switch($_SESSION["ls_gestor"])
                {
                     case "POSTGRES":
                         $ls_sql="ALTER TABLE soc_ordencompra ".
                                 "ALTER COLUMN tascamordcom TYPE numeric(20,8);";
                     break;
                 }	
                 $li_row=$this->io_sql->execute($ls_sql);
                 if($li_row===false)
                 { 
                     $this->io_msg->message("Problemas al ejecutar Release 2022_01_04-05");
                     $lb_valido=false;
                 }                
            }
            return $lb_valido;	
	}// end function uf_create_release_db_libre_V_2022_01_04
    //-----------------------------------------------------------------------------------------------------------------------------------

    /////---------------------------------------------------------------------------------------------------------------------------------
    function uf_create_release_db_libre_V_2022_05_01()
    {
             /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
             //	     Function: uf_create_release_db_libre_V_2022_05_01
             //		   Access: public 
             //        Modulos: SNO
             //	  Description: 
             // Fecha Creacion:						Fecha Ultima Modificacion : 
             ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $lb_valido=true;
            $ls_sql="";	

            switch($_SESSION["ls_gestor"])
            {
                            case "MYSQLT":
                                    $ls_sql= " ALTER TABLE sno_sueldoshistoricos ADD COLUMN comsue double precision DEFAULT 0; ";					
                              break;

                            case "POSTGRES":
                                     $ls_sql= " ALTER TABLE sno_sueldoshistoricos ADD COLUMN comsue double precision DEFAULT 0;";																	
                               break;  				  
            }
            if (!empty($ls_sql))
            {	
                     $li_row=$this->io_sql->execute($ls_sql);
                     if($li_row===false)
                     { 
                             $this->io_msg->message("Problemas con el  Release 2022_05_01");
                             $lb_valido=false;
                     }
            }	  
       return $lb_valido;	
    }//FIN DE uf_create_release_db_libre_V_2022_05_01()

	/////---------------------------------------------------------------------------------------------------------------------------------
    function uf_create_release_db_libre_V_2022_05_02()
    {
             /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
             //	     Function: uf_create_release_db_libre_V_2022_05_02
             //		   Access: public 
             //        Modulos: SNO
             //	  Description: 
             // Fecha Creacion:						Fecha Ultima Modificacion : 
             ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $lb_valido=true;
            $ls_sql="";	

            switch($_SESSION["ls_gestor"])
            {
                            case "MYSQLT":
                                    $ls_sql= "ALTER TABLE spg_unidadadministrativa  ADD COLUMN resuniadm character varying(254);";					
                              break;

                            case "POSTGRES":
                                    $ls_sql= "ALTER TABLE spg_unidadadministrativa  ADD COLUMN resuniadm character varying(254);";					
                               break;  				  
            }
            if (!empty($ls_sql))
            {	
                     $li_row=$this->io_sql->execute($ls_sql);
                     if($li_row===false)
                     { 
                             $this->io_msg->message("Problemas con el  Release 2022_05_02");
                             $lb_valido=false;
                     }
            }	  
       return $lb_valido;	
    }//FIN DE uf_create_release_db_libre_V_2022_05_02()

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_create_release_db_libre_V_2022_07_01()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_create_release_db_libre_V_2022_07_01
		//		   Access: public 
		//        Modulos: Nomina
		//	  Description: 
		// Fecha Creacion: 07/10/2011								Fecha Ultima Modificacion : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
	   $ls_sql="";
	   switch($_SESSION["ls_gestor"])
	   {
			   
			case "POSTGRES":
			   $ls_sql= "CREATE TABLE sno_enfermedad ".
						"( ".
						"   codemp character(4) NOT NULL DEFAULT '0001',  ".
						"   codenf character(4) NOT NULL,  ".
						"   desenf character varying(255) NOT NULL,  ".
						"   enfcro character(1) NOT NULL DEFAULT '0',  ".
						"   obsenf text,  ".
						"   CONSTRAINT pk_sno_enfermedad PRIMARY KEY (codemp, codenf),  ".
						"   CONSTRAINT fk_sno_enfermedad__sigesp_empresa FOREIGN KEY (codemp) REFERENCES sigesp_empresa (codemp) MATCH SIMPLE ".
						"	  ON UPDATE RESTRICT ON DELETE RESTRICT ".
						") ".
						"WITHOUT OIDS;";
				break;	
		}
		if (!empty($ls_sql))
		{	
			 $li_row=$this->io_sql->execute($ls_sql);
			 if($li_row===false)
			 { 
				 $this->io_msg->message("Problemas al ejecutar Release 2022_07_01");
				 $lb_valido=false;
			 }
		}
	   return $lb_valido;			
	} // end function uf_create_release_db_libre_V_2022_07_01
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_create_release_db_libre_V_2022_07_02()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_create_release_db_libre_V_2022_07_02
		//		   Access: public 
		//        Modulos: Nomina
		//	  Description: 
		// Fecha Creacion: 07/10/2011								Fecha Ultima Modificacion : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
	   $ls_sql="";
	   switch($_SESSION["ls_gestor"])
	   {
			   
			case "POSTGRES":
			   $ls_sql= "CREATE TABLE sno_personalenfermedad ".
						"( ".
						"   codemp character(4) NOT NULL DEFAULT '0001',  ".
						"   codper character(10) NOT NULL, ".
						"   codenf character(4) NOT NULL,  ".
						"   observacion character varying(255),  ".
						"   CONSTRAINT pk_sno_personalenfermedad PRIMARY KEY (codemp, codper, codenf),  ".
						"   CONSTRAINT fk_sno_personalenfermedad__sno_personal FOREIGN KEY (codemp, codper) ".
						"	  REFERENCES sno_personal (codemp, codper) MATCH SIMPLE ".
						"	  ON UPDATE RESTRICT ON DELETE RESTRICT, ".
						"   CONSTRAINT fk_sno_personalenfermedad__sno_enfermedad FOREIGN KEY (codemp, codenf) ".
						"	  REFERENCES sno_enfermedad (codemp, codenf) MATCH SIMPLE ".
						"	  ON UPDATE RESTRICT ON DELETE RESTRICT	 ".
						") ".
						"WITHOUT OIDS;";
				break;	
		}
		if (!empty($ls_sql))
		{	
			 $li_row=$this->io_sql->execute($ls_sql);
			 if($li_row===false)
			 { 
				 $this->io_msg->message("Problemas al ejecutar Release 2022_07_02");
				 $lb_valido=false;
			 }
		}
	   return $lb_valido;			
	} // end function uf_create_release_db_libre_V_2022_07_02
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_create_release_db_libre_V_2022_07_03()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_create_release_db_libre_V_2022_07_03
		//		   Access: public
		//        Modulos: nomina
		//	  Description:
		// Fecha Creacion: 01/12/2017								Fecha Ultima Modificacion :
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="";
		switch(strtoupper($_SESSION["ls_gestor"]))
		{
		   
			case "POSTGRES":
			   $ls_sql= " ALTER TABLE sno_personal ".
						"	  ADD COLUMN coreleins character varying(100), ".
						"	  ADD COLUMN tienedis character(1) NOT NULL DEFAULT '0', ".
						"	  ADD COLUMN desdis character varying(255), ".
						"	  ADD COLUMN nrocardis character varying(50), ".
						"	  ADD COLUMN contraorg character(1) NOT NULL DEFAULT '0';";
		        break;	 			  
		}
		if (!empty($ls_sql))
		{
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("Problemas al ejecutar Release 2022_07_03");
				$lb_valido=false;
			}
		}
		return $lb_valido;
	} // end function uf_create_release_db_libre_V_2022_07_03
	//-----------------------------------------------------------------------------------------------------------------------------------

        /////---------------------------------------------------------------------------------------------------------------------------------
        function uf_create_release_db_libre_V_2022_08_01()
        {
                 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 //	     Function: uf_create_release_db_libre_V_2022_08_01
                 //		   Access: public 
                 //        Modulos: SNO
                 //	  Description: 
                 // Fecha Creacion:						Fecha Ultima Modificacion : 
                 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $lb_valido=true;
                $ls_sql="";	

                switch($_SESSION["ls_gestor"])
                {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE scv_otrasasignaciones  ADD COLUMN codmon character varying(3);";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE scv_otrasasignaciones  ADD COLUMN codmon character varying(3);";					
                                   break;  				  
                }
                if (!empty($ls_sql))
                {	
                         $li_row=$this->io_sql->execute($ls_sql);
                         if($li_row===false)
                         { 
                                 $this->io_msg->message("Problemas con el  Release 2022_08_01");
                                 $lb_valido=false;
                         }
                }	  
           return $lb_valido;	
        }//FIN DE uf_create_release_db_libre_V_2022_08_01()

        /////---------------------------------------------------------------------------------------------------------------------------------
        function uf_create_release_db_libre_V_2022_08_02()
        {
                 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 //	     Function: uf_create_release_db_libre_V_2022_08_02
                 //		   Access: public 
                 //        Modulos: SNO
                 //	  Description: 
                 // Fecha Creacion:						Fecha Ultima Modificacion : 
                 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $lb_valido=true;
                $ls_sql="";	

                switch($_SESSION["ls_gestor"])
                {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE sigesp_unidad_tributaria ALTER COLUMN decnro TYPE character varying(50);";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE sigesp_unidad_tributaria ALTER COLUMN decnro TYPE character varying(50);";					
                                   break;  				  
                }
                if (!empty($ls_sql))
                {	
                         $li_row=$this->io_sql->execute($ls_sql);
                         if($li_row===false)
                         { 
                                 $this->io_msg->message("Problemas con el  Release 2022_08_02");
                                 $lb_valido=false;
                         }
                }	  
           return $lb_valido;	
        }//FIN DE uf_create_release_db_libre_V_2022_08_02()

        /////---------------------------------------------------------------------------------------------------------------------------------
        function uf_create_release_db_libre_V_2022_12_01()
        {
                 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 //	     Function: uf_create_release_db_libre_V_2022_12_01
                 //		   Access: public 
                 //        Modulos: SNO
                 //	  Description: 
                 // Fecha Creacion:						Fecha Ultima Modificacion : 
                 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $lb_valido=true;
                $ls_sql="";	

                switch($_SESSION["ls_gestor"])
                {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE sep_solicitud  ".
                                                 "  ADD COLUMN codmon character(3) DEFAULT '---',".					
                                                 "  ADD COLUMN tascam numeric(20,8) DEFAULT 1,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monbasinmdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montotcardiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE sep_solicitud  ".
                                                 "  ADD COLUMN codmon character(3) DEFAULT '---',".					
                                                 "  ADD COLUMN tascam numeric(20,8) DEFAULT 1,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monbasinmdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montotcardiv numeric(20,2) DEFAULT 0; ".					
                                                 "UPDATE sep_solicitud  ".
                                                 "   SET montodiv = monto,".					
                                                 "       monbasinmdiv = monbasinm,".					
                                                 "       montotcardiv = montotcar;";					
                                   break;  				  
                }

                if (!empty($ls_sql))
                {	
                         $li_row=$this->io_sql->execute($ls_sql);
                         if($li_row===false)
                         { 
                                 $this->io_msg->message("Problemas con el  Release 2022_12_02-01");
                                 $lb_valido=false;
                         }
                }	  
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                    case "POSTGRES":
                                            $ls_sql= "ALTER TABLE sep_solicitud ".
                                                     " ADD CONSTRAINT fk_sep_solicitud__sigesp_moneda FOREIGN KEY (codemp, codmon) ".
                                                     "     REFERENCES sigesp_moneda (codemp, codmon) ".
                                                     "     ON UPDATE NO ACTION ON DELETE NO ACTION;";					
                                       break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-02");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE sep_cuentagasto  ".
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE sep_cuentagasto  ".
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE sep_cuentagasto  ".
                                                 "   SET montodiv = Monto;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-03");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE sep_dt_articulos  ".
                                                 "  ADD COLUMN monprediv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monartdiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE sep_dt_articulos  ".
                                                 "  ADD COLUMN monprediv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monartdiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE sep_dt_articulos  ".
                                                 "  SET monprediv = monpre,".					
                                                 "      monartdiv = monart;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-04");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE sep_dt_concepto  ".
                                                 "  ADD COLUMN monprediv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN moncondiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE sep_dt_concepto  ".
                                                 "  ADD COLUMN monprediv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN moncondiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE sep_dt_concepto  ".
                                                 "   SET monprediv = monpre,".					
                                                 "       moncondiv = moncon;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-05");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE sep_dt_servicio  ".
                                                 "  ADD COLUMN monprediv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monserdiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE sep_dt_servicio  ".
                                                 "  ADD COLUMN monprediv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monserdiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE sep_dt_servicio  ".
                                                 "   SET monprediv = monpre,".					
                                                 "       monserdiv = monser;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-06");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE sep_dta_cargos  ".
                                                 "  ADD COLUMN monbasimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE sep_dta_cargos  ".
                                                 "  ADD COLUMN monbasimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE sep_dta_cargos  ".
                                                 "   SET monbasimpdiv = monbasimp,".					
                                                 "       monimpdiv = monimp,".					
                                                 "       montodiv = monto;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-07");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE sep_dtc_cargos  ".
                                                 "  ADD COLUMN monbasimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE sep_dtc_cargos  ".
                                                 "  ADD COLUMN monbasimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE sep_dtc_cargos  ".
                                                 "   SET monbasimpdiv = monbasimp,".					
                                                 "       monimpdiv = monimp,".					
                                                 "       montodiv = monto;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-08");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE sep_dts_cargos  ".
                                                 "  ADD COLUMN monbasimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE sep_dts_cargos  ".
                                                 "  ADD COLUMN monbasimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE sep_dts_cargos  ".
                                                 " SET monbasimpdiv = monbasimp,".					
                                                 "     monimpdiv = monimp,".					
                                                 "     montodiv = monto;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-09");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE sep_solicitudcargos  ".
                                                 "  ADD COLUMN monobjretdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monretdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE sep_solicitudcargos  ".
                                                 "  ADD COLUMN monobjretdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monretdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE sep_solicitudcargos  ".
                                                 "   SET monobjretdiv = monobjret,".					
                                                 "       monretdiv = monret,".					
                                                 "       montodiv = monto;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-10");
                                     $lb_valido=false;
                             }
                    }	  
                }
           return $lb_valido;	
        }//FIN DE uf_create_release_db_libre_V_2022_12_01()

        /////---------------------------------------------------------------------------------------------------------------------------------
        function uf_create_release_db_libre_V_2022_12_02()
        {
                 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 //	     Function: uf_create_release_db_libre_V_2022_12_02
                 //		   Access: public 
                 //        Modulos: SNO
                 //	  Description: 
                 // Fecha Creacion:						Fecha Ultima Modificacion : 
                 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $lb_valido=true;
                $ls_sql="";	

                switch($_SESSION["ls_gestor"])
                {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE soc_ordencompra  ".
                                                 "ALTER COLUMN codmon SET DEFAULT '---', ".	
                                                 "ALTER COLUMN tascamordcom SET DEFAULT 1, ".
                                                 "  ADD COLUMN monsubtotdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE soc_ordencompra  ".
                                                 "ALTER COLUMN codmon SET DEFAULT '---', ".	
                                                 "ALTER COLUMN tascamordcom SET DEFAULT 1, ".
                                                 "  ADD COLUMN monsubtotdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0;";					
                                                 "UPDATE soc_ordencompra  ".
                                                 "   SET codmon = '---', ".					
                                                 "       tascamordcom = 1,".					
                                                 "       monsubtotdiv = monsubtot,".					
                                                 "       monimpdiv = monimp,".					
                                                 "       montotdiv = montot;";					
                                   break;  				  
                }

                if (!empty($ls_sql))
                {	
                         $li_row=$this->io_sql->execute($ls_sql);
                         if($li_row===false)
                         { 
                                 $this->io_msg->message("Problemas con el  Release 2022_12_02-01");
                                 $lb_valido=false;
                         }
                }	  
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE soc_cuentagasto  ".
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE soc_cuentagasto  ".
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE soc_cuentagasto  ".
                                                 "   SET montodiv = monto;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-02");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE soc_dt_bienes  ".
                                                 "  ADD COLUMN monsubartdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montotartdiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE soc_dt_bienes  ".
                                                 "  ADD COLUMN monsubartdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montotartdiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE soc_dt_bienes  ".
                                                 "  SET monsubartdiv = monsubart,".					
                                                 "      montotartdiv = montotart;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-03");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE soc_dt_servicio  ".
                                                 "  ADD COLUMN monsubserdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montotserdiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE soc_dt_servicio  ".
                                                 "  ADD COLUMN monsubserdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montotserdiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE soc_dt_servicio  ".
                                                 "   SET monsubserdiv = monsubser,".					
                                                 "       montotserdiv = montotser;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-04");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE soc_dta_cargos  ".
                                                 "  ADD COLUMN monbasimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE soc_dta_cargos  ".
                                                 "  ADD COLUMN monbasimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;";					
                                                 "UPDATE soc_dta_cargos  ".
                                                 "   SET monbasimpdiv = monbasimp,".					
                                                 "       monimpdiv = monimp,".					
                                                 "       montodiv = monto;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-05");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE soc_dts_cargos  ".
                                                 "  ADD COLUMN monbasimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE soc_dts_cargos  ".
                                                 "  ADD COLUMN monbasimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monimpdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;".					
                                                 "UPDATE soc_dts_cargos  ".
                                                 "   SET monbasimpdiv = monbasimp,".					
                                                 "       monimpdiv = monimp,".					
                                                 "       montodiv = monto;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-06");
                                     $lb_valido=false;
                             }
                    }	  
                }
                if ($lb_valido)
                {	
                    switch($_SESSION["ls_gestor"])
                    {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE soc_solicitudcargos  ".
                                                 "  ADD COLUMN monretdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monobjretdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE soc_solicitudcargos  ".
                                                 "  ADD COLUMN monretdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN monobjretdiv numeric(20,2) DEFAULT 0,".					
                                                 "  ADD COLUMN montodiv numeric(20,2) DEFAULT 0;";					
                                                 "UPDATE soc_solicitudcargos  ".
                                                 "   SET monretdiv = monret,".					
                                                 "       monobjretdiv = monobjret,".					
                                                 "       montodiv = monto;";					
                                   break;  				  
                    }
                    if (!empty($ls_sql))
                    {	
                             $li_row=$this->io_sql->execute($ls_sql);
                             if($li_row===false)
                             { 
                                     $this->io_msg->message("Problemas con el  Release 2022_12_02-07");
                                     $lb_valido=false;
                             }
                    }	  
                }
           return $lb_valido;	
        }//FIN DE uf_create_release_db_libre_V_2022_12_01()
    
        /////---------------------------------------------------------------------------------------------------------------------------------
        function uf_create_release_db_libre_V_2022_12_03()
        {
                 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                 //	     Function: uf_create_release_db_libre_V_2022_12_03
                 //		   Access: public 
                 //        Modulos: SNO
                 //	  Description: 
                 // Fecha Creacion:						Fecha Ultima Modificacion : 
                 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $lb_valido=true;
                $ls_sql="";	

                switch($_SESSION["ls_gestor"])
                {
                                case "MYSQLT":
                                        $ls_sql= "ALTER TABLE sno_nomina  ADD COLUMN pasape integer DEFAULT 0;";					
                                  break;

                                case "POSTGRES":
                                        $ls_sql= "ALTER TABLE sno_nomina  ADD COLUMN pasape integer DEFAULT 0;";					
                                   break;  				  
                }
                if (!empty($ls_sql))
                {	
                         $li_row=$this->io_sql->execute($ls_sql);
                         if($li_row===false)
                         { 
                                 $this->io_msg->message("Problemas con el  Release 2022_12_03");
                                 $lb_valido=false;
                         }
                }	  
           return $lb_valido;	
        }//FIN DE uf_create_release_db_libre_V_2022_08_01()


///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////OTROS PROCESOS ADICIONALES//////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_config($as_codsis,$as_seccion,$as_entry)
	{
		$lb_existe=false;
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sql="SELECT * ".
			    "  FROM sigesp_config ".
			    " WHERE codemp='".$ls_codemp."' ".
			    "   AND codsis='".$as_codsis."' ".
			    "   AND trim(seccion)='".trim($as_seccion)."' ".
			    "   AND trim(entry)='".trim($as_entry)."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("No existe la tabla.");
			return false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$lb_existe=true;
			}
		}
		return $lb_existe;
	}//fin de uf_select_config
//-----------------------------------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_config($as_codsis,$as_seccion,$as_entry)
	{
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$lb_valido=true;
		if (!$this->uf_select_config($as_codsis,$as_seccion,$as_entry))
		{
			$ls_sql="INSERT INTO sigesp_config(codemp, codsis, seccion, entry, type, value)".
					"     VALUES ('".$ls_codemp."','".$as_codsis."','".$as_seccion."','".$as_entry."','C',' ')";	
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->release MÃ‰TODO->uf_insert_config ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
		}
		return $lb_valido;

	}	
///------------------------------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------------------------------------
    function uf_buscar_unidad()
	{
		$ls_sql="";
		$lb_valido=true;
		$valor=0;	
	    switch(strtoupper($_SESSION["ls_gestor"]))
		{
			case "MYSQLT":
			   $ls_sql= "  SELECT count(*) as valor from spg_dt_unidadadministrativa WHERE coduniadm='----------' ";					
		    break;
			case "MYSQLI":
			   $ls_sql= "  SELECT count(*) as valor from spg_dt_unidadadministrativa WHERE coduniadm='----------' ";					
		    break;
			case "POSTGRES":
				 $ls_sql= "  SELECT count(*) as valor from spg_dt_unidadadministrativa WHERE coduniadm='----------' ";													
			break;  				  
		   
		}
		if (!empty($ls_sql))
		{	
			$li_row=$this->io_sql->select($ls_sql); 
			if($li_row===false)
			{ 
				$this->io_msg->message("Problemas uf_buscar_unidad");
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($li_row))
				{
					$valor=$row["valor"];
				}
			}
		}
	    return $valor;
	}//fin del la funcion
////------------------------------------------------------------------------------------------------------------------------------- 

///-------------------------------------------------------------------------------------------------------------------------------------
    function uf_buscar_sep_solicitud()
	{
		$ls_sql="";
		$lb_valido=true;
		$valor1=0;	
	    switch(strtoupper($_SESSION["ls_gestor"]))
		{
			case "MYSQLT":
			   $ls_sql= " select count(*) as valor from sep_solicitud where numsol='---------------' ";					
		    break;
			case "MYSQLI":
			   $ls_sql= " select count(*) as valor from sep_solicitud where numsol='---------------' ";					
		    break;
			case "POSTGRES":
				$ls_sql= " select count(*) as valor from sep_solicitud where numsol='---------------' ";													
			break;  				  
		} 
		if (!empty($ls_sql))
		{	
			$li_row=$this->io_sql->select($ls_sql); 
			if($li_row===false)
			{
				$this->io_msg->message("uf_buscar_sep_solicitud");
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($li_row))
				{ 
					$valor1=$row["valor"];
				}
			}
		} 
	    return $valor1;
	}//fin del la funcion
///------------------------------------------------------------------------------------------------------------------------------------


} // end class uf_check_update()
?>