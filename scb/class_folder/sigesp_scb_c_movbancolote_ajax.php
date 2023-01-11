<?php 
/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	session_start();
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	        Class: sigesp_sob_c_registroobra_ajax
	//		   Access: public 
	//	  Description: Clase para muestra de detalles de las obras
	//	   Creado Por: Ing. Luis Anibal Lang
	//  Fecha Creacin: 25/04/2017 								Fecha Ultima Modificacin : 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	require_once("class_funciones_scb.php");
	$io_funciones_scb=new class_funciones_scb();
	require_once("../../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();	
	// Tipo del catalogo que se requiere pintar
	$ls_funcion=$io_funciones_scb->uf_obtenervalor("funcion","");
	switch($ls_funcion){
		case "RELOAD"://Pinta los grid de detalle de la nota cuando se agrega un detalle o un cargo,lo usa cuando son recepciones con afectacion presupuestaria
			uf_recargar_data();	
			break;	
		default:
			uf_cargar_archivo_excel();	
			break;
	}
	
	
	function uf_cargar_archivo_excel()
	{
		global $io_funciones_scb,$io_grid;
		require_once("../../base/librerias/php/readexcel/reader.php");
		$io_excel = new Spreadsheet_Excel_Reader();
		$upload_dir = "../importar";
		$ls_tiparc=$_FILES["arcimp"]["type"];
		if($ls_tiparc=="text/plain")
		{
			$lo_archivo="";
			$ls_nombrearchivo=$_FILES["arcimp"]["tmp_name"];
			$arrResultado=uf_abrir_archivo($ls_nombrearchivo,$lo_archivo);
			$lo_archivo=$arrResultado['ao_archivo'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				$lb_valido=uf_importar_data($lo_archivo);
			}
					
		}
		else
		{ 
			if (isset($_FILES['arcimp']))
			{
				if ($_FILES['arcimp']['error'] == UPLOAD_ERR_OK)
				{
					$filename = $_FILES['arcimp']['name']; 
					move_uploaded_file($_FILES['arcimp']['tmp_name'], $upload_dir.'/'.$filename);
					$io_excel->setOutputEncoding("CP1251");
					$io_excel->read('../importar/'.$filename);
					$li=0;
					for($li_indexfil=2;($li_indexfil<=$io_excel->sheets[0]['numRows']);$li_indexfil++)
					{
					   $ls_operacion=$io_excel->sheets[0]['cells'][$li_indexfil][5];
					   $ls_documento=$io_excel->sheets[0]['cells'][$li_indexfil][6];
					   $ls_fecha=$io_excel->sheets[0]['cells'][$li_indexfil][2];
					   $li_monto=$io_excel->sheets[0]['cells'][$li_indexfil][7];
					   if (!empty($ls_operacion))
					   {
							$li_monto=$io_funciones_scb->uf_formatonumerico($li_monto);
				
							$li++;
							$lo_object[$li][1]="<input type=text name=txtoperacion".$li."   id=txtoperacion".$li."    class=sin-borde style=text-align:center size=15 value='".$ls_operacion."'    readonly>";
							$lo_object[$li][2]="<input type=text name=txtdocumento".$li."   id=txtdocumento".$li."    class=sin-borde style=text-align:left   size=15 value='".$ls_documento."'    readonly>";
							$lo_object[$li][3]="<input type=text name=txtfecha".$li."       id=txttxtfecha".$li."     class=sin-borde style=text-align:left   size=10 value='".$ls_fecha."'    readonly>";
							$lo_object[$li][4]="<input type=text name=txtmonto".$li."       id=txtmonto".$li."        class=sin-borde style=text-align:right size=20 value='".$li_monto."'     onKeyPress=return(validaCajas(this,'d',event,15))  onKeyUp=javascript:uf_llamada_validarmonto(this) onBlur=javascript:ue_getformat(this) >";
							$lo_object[$li][5]="<a href=javascript:uf_detalles_movimientos('".$li."');><img src=../shared/imagebank/mas.gif title=Detalles width=10 height=15 border=0></a>";
							$lo_object[$li][6]="<a href=javascript:uf_delete_detalle('".$li."');><img src=../shared/imagebank/tools15/eliminar.gif title=Eliminar width=15 height=10 border=0></a>";
						}			   
					}
					//$li=$li-1;
					// Titulos del Grid de Bienes
					$lo_title[1]="Operacion";
					$lo_title[2]="Documento";
					$lo_title[3]="Documento";
					$lo_title[4]="Monto";
					$lo_title[5]="Detalles";
					$lo_title[6]="Eliminar";
					$io_grid->make_gridScroll($li,$lo_title,$lo_object,590,"Documentos","grid",200);
			
					print "<input type=hidden name=rows id=rows value=".$li.">";
	
				}
			}
		}
		unset($io_excel);
	}
	function uf_importar_data($ao_archivo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_importar_data
		//		   Access: private
		//	    Arguments: ao_archivo // conexión del archivo que se desea abrir
		// 	      Returns: lb_valido True si se abrio el archivo ó False si no se abrio
		//	  Description: Funcion que abre un archivo de texto dada una ruta 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 15/09/2017 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		global $io_funciones_scb,$io_grid;
		$li_total=count((array)$ao_archivo);
		for($li_i=0;($li_i<$li_total);$li_i++)
		{
			$la_filas=explode("|",$ao_archivo[$li_i]);			
			$ls_operacion=$la_filas[11];
			$ls_documento=$la_filas[12];
			$ls_fecha=$la_filas[1];
			$ls_codban=$la_filas[8];
			$ls_ctaban=$la_filas[7];
			$li_monto=number_format($la_filas[9],2,'.','');
			$li_comban=number_format($la_filas[10],2,'.','');
			$ls_fecha=str_replace('-','/',$ls_fecha);
			$ls_ctaban=str_replace('-','',$ls_ctaban);
			switch($ls_operacion)
			{	
				case "DP003":
					$ls_operacion="DEP";
					$ls_operacionaux="DP";
				break;
			}
			switch($ls_codban)
			{	
				case "1000":
					//$ls_codban="027";
					$ls_codban="028";
					$ls_banco="VENEZOLANO CREDITO S.A., BANCO UNIVERSAL";
				break;
				case "1001":
					//$ls_codban="017";
					$ls_codban="";
					$ls_banco="CORP BANCA, C.A.";
				break;
				case "1002":
					//$ls_codban="011";
					$ls_codban="017";
					$ls_banco="BANCO PROVINCIAL, S.A.C.A., BANCO UNIVERSAL";
				break;
				case "1003":
					//$ls_codban="021";
					$ls_codban="008";
					$ls_banco="BANCO DE VENEZUELA, S.A.C.A., BANCO UNIVERSAL";
				break;
				case "1004":
					//$ls_codban="020";
					$ls_codban="019";
					$ls_banco="BANESCO, BANCO UNIVERSAL, S.A.C.A.";
				break;
				case "1020":
					//$ls_codban="024";
					$ls_codban="029";
					$ls_banco="BANCO INDUSTRIAL DE VENEZUELA, C.A.";
				break;
				case "1034":
					//$ls_codban="019";
					$ls_codban="009";
					$ls_banco="BANCO DEL CARIBE, C.A. BANCO UNIVERSAL";
				break;
				case "1037":
					//$ls_codban="023";
					$ls_codban="021";
					$ls_banco="BFC BANCO FONDO COMUN, C.A BANCO UNIVERSAL";
				break;
				case "1038":
//					$ls_codban="018";
					$ls_codban="013";
					$ls_banco="BANCO MERCANTIL, C.A.,BANCO UNIVERSAL";
				break;
				case "1095":
//					$ls_codban="018";
					$ls_codban="013";
					$ls_banco="BANCO MERCANTIL, C.A.,BANCO UNIVERSAL";
				break;
				case "1100":
					$ls_codban="028";
					$ls_codban="";
					$ls_banco="BANFOANDES";
				break;
				case "1106":
					//$ls_codban="015";
					$ls_codban="014";
					$ls_banco="BANCO NACIONAL DE CREDITO, C.A. BANCO UNIVERSAL";
				break;
				case "11143":
					//$ls_codban="053";
					$ls_codban="";
					$ls_banco="BANCO FEDERAL";
				break;
				case "1024":
					//$ls_codban="064";
					$ls_codban="015";
					$ls_banco="BANCO OCCIDENTAL DE DESCUENTO, C.A";
				break;
				case "1800":
					//$ls_codban="011";
					$ls_codban="017";
					$ls_banco="BANCO PROVINCIAL, S.A.C.A., BANCO UNIVERSAL";
				break;
				case "1602":
				//	$ls_codban="065";
					$ls_codban="005";
					$ls_banco="BANCO BICENTENARIO BANCO UNIVERSAL, C.A.";
				break;
				case "1700":
					//$ls_codban="018";
					$ls_codban="013";
					$ls_banco="BANCO MERCANTIL, C.A.,BANCO UNIVERSAL";
				break;
				case "1147":
					//$ls_codban="071";
					$ls_codban="004";
					$ls_banco="BANCO AGRICOLA DE VENEZUELA, C.A. BANCO UNIVERSAL";
				break;
			}
			
			$adec_saldo="";
			$arrResultado="";
			$arrResultado=uf_verificar_saldo($ls_codban,$ls_ctaban,$adec_saldo);					  
			$adec_saldo=$arrResultado['ldec_saldo'];
			$ls_sccuenta=uf_select_cuenta_contable_banco($ls_codban,$ls_ctaban);
			$lb_existe=uf_select_movimiento_banco($ls_codban,$ls_ctaban,$ls_documento,$ls_operacionaux);

		   if (!empty($ls_operacion))
		   {
				$li_monto=$io_funciones_scb->uf_formatonumerico($li_monto);
	
				$li++;
				$lo_object[$li][1]="<input type=text name=txtoperacion".$li."  id=txtoperacion".$li."  class=sin-borde style=text-align:center size=5 value='".$ls_operacion."'    readonly>".
								   "<input type=hidden name=txtexiste".$li."  id=txtexiste".$li."  class=sin-borde style=text-align:center size=5 value='".$lb_existe."'    readonly>";
				$lo_object[$li][2]="<input type=text name=txtdocumento".$li."  id=txtdocumento".$li."  class=sin-borde style=text-align:left   size=12 value='".$ls_documento."'    readonly>";
				$lo_object[$li][3]="<input type=text name=txtbanco".$li."      id=txtbanco".$li."      class=sin-borde style=text-align:left   size=25 value='".$ls_banco."'    readonly>".
								   "<input type=hidden name=txtcodban".$li."      id=txtcodban".$li."      class=sin-borde style=text-align:left   size=15 value='".$ls_codban."'    readonly>".
								   "<input type=hidden name=txtdisponible".$li."      id=txtdisponible".$li."      class=sin-borde style=text-align:left   size=15 value='".$adec_saldo."'    readonly>".
								   "<input type=hidden name=txtsccuenta".$li."      id=txtsccuenta".$li."      class=sin-borde style=text-align:left   size=15 value='".$ls_sccuenta."'    readonly>";
				$lo_object[$li][4]="<input type=text name=txtcuenta".$li."     id=txtcuenta".$li."     class=sin-borde style=text-align:left   size=20 value='".$ls_ctaban."'    readonly>";
				$lo_object[$li][5]="<input type=text name=txtfecha".$li."      id=txttxtfecha".$li."   class=sin-borde style=text-align:left   size=10 value='".$ls_fecha."'    readonly>";
				$lo_object[$li][6]="<input type=text name=txtmonto".$li."      id=txtmonto".$li."      class=sin-borde style=text-align:right size=12 value='".$li_monto."'     onKeyPress=return(validaCajas(this,'d',event,15))  onKeyUp=javascript:uf_llamada_validarmonto(this) onBlur=javascript:ue_getformat(this) >";
				$lo_object[$li][7]="<a href=javascript:uf_detalles_movimientos('".$li."');><img src=../shared/imagebank/mas.gif title=Detalles width=10 height=15 border=0></a>";
				if($lb_existe)
				{
					$lo_object[$li][8]="<img src=../shared/imagebank/ok.png title=Documento Registrado width=15 height=10 border=0>";
				}
				else
				{
					$lo_object[$li][8]="<img src=../shared/imagebank/failed.png title=Documento No Registrato width=15 height=10 border=0>";
				}
				
				if($li_comban>0)
				{
					$li_comban=$io_funciones_scb->uf_formatonumerico($li_comban);
					$lb_existe=uf_select_movimiento_banco($ls_codban,$ls_ctaban,$ls_documento,"ND");
	
					$li++;
					$lo_object[$li][1]="<input type=text name=txtoperacion".$li."  id=txtoperacion".$li."  class=sin-borde style=text-align:center size=5 value='ND'    readonly>".
								        "<input type=hidden name=txtexiste".$li."  id=txtexiste".$li."  class=sin-borde style=text-align:center size=5 value='".$lb_existe."'    readonly>";
					$lo_object[$li][2]="<input type=text name=txtdocumento".$li."  id=txtdocumento".$li."  class=sin-borde style=text-align:left   size=12 value='".$ls_documento."'    readonly>";
					$lo_object[$li][3]="<input type=text name=txtbanco".$li."      id=txtbanco".$li."      class=sin-borde style=text-align:left   size=25 value='".$ls_banco."'    readonly>".
									   "<input type=hidden name=txtcodban".$li."      id=txtcodban".$li."      class=sin-borde style=text-align:left   size=15 value='".$ls_codban."'    readonly>".
									   "<input type=hidden name=txtdisponible".$li."      id=txtdisponible".$li."      class=sin-borde style=text-align:left   size=15 value='".$adec_saldo."'    readonly>".
									   "<input type=hidden name=txtsccuenta".$li."      id=txtsccuenta".$li."      class=sin-borde style=text-align:left   size=15 value='".$ls_sccuenta."'    readonly>";
					$lo_object[$li][4]="<input type=text name=txtcuenta".$li."     id=txtcuenta".$li."     class=sin-borde style=text-align:left   size=20 value='".$ls_ctaban."'    readonly>";
					$lo_object[$li][5]="<input type=text name=txtfecha".$li."      id=txttxtfecha".$li."   class=sin-borde style=text-align:left   size=10 value='".$ls_fecha."'    readonly>";
					$lo_object[$li][6]="<input type=text name=txtmonto".$li."      id=txtmonto".$li."      class=sin-borde style=text-align:right size=12 value='".$li_comban."'     onKeyPress=return(validaCajas(this,'d',event,15))  onKeyUp=javascript:uf_llamada_validarmonto(this) onBlur=javascript:ue_getformat(this) >";
					$lo_object[$li][7]="<a href=javascript:uf_detalles_movimientos('".$li."');><img src=../shared/imagebank/mas.gif title=Detalles width=10 height=15 border=0></a>";
					if($lb_existe)
					{
						$lo_object[$li][8]="<img src=../shared/imagebank/ok.png title=Documento Registrado width=15 height=10 border=0>";
					}
					else
					{
						$lo_object[$li][8]="<img src=../shared/imagebank/failed.png title=Documento No Registrato width=15 height=10 border=0>";
					}
				}
			}			   

		}
		// Titulos del Grid de Bienes
		$lo_title[1]="Operacion";
		$lo_title[2]="Documento";
		$lo_title[3]="Banco";
		$lo_title[4]="Cuenta";
		$lo_title[5]="Fecha";
		$lo_title[6]="Monto";
		$lo_title[7]="Detalles";
		$lo_title[8]="Existe";
		$io_grid->make_gridScroll($li,$lo_title,$lo_object,800,"Documentos","grid",200);

		print "<input type=hidden name=rows id=rows value=".$li.">";



	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_abrir_archivo($as_nombrearchivo,$ao_archivo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_abrir_archivo
		//		   Access: private
		//	    Arguments: as_nombrearchivo // Ruta donde se debe abrir el archivo
		//	    		   ao_archivo // conexión del archivo que se desea abrir
		// 	      Returns: lb_valido True si se abrio el archivo ó False si no se abrio
		//	  Description: Funcion que abre un archivo de texto dada una ruta 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 15/09/2017 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		global $io_mensajes;
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		if (file_exists("$as_nombrearchivo"))
		{
			$ao_archivo=@file("$as_nombrearchivo");
		}
		else
		{
			$lb_valido=false;
			$io_mensajes->message("CLASE->Importar Prestamos MÉTODO->uf_abrir_archivo ERROR->el archivo no existe."); 
		}
		$arrResultado['ao_archivo']=$ao_archivo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_abrir_archivo
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_saldo($as_codban,$as_ctaban,$ldec_saldo)
	{
		/////////////////////////////////////////////////////////////////////////////
		// Function	    : uf_verificar_saldo
		//	Return	    : ldec_saldo
		//	Descripcion : Funcion que se encarga de obtener el saldo disponible para
		//				  el banco y cuenta recibido como parametro
		/////////////////////////////////////////////////////////////////////////////
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_include=new sigesp_include();
		$con=$io_include->uf_conectar();
		$io_sql=new class_sql($con);
		$mensajes=new class_mensajes();

		$ldec_monto_debe=$ldec_monto_haber=$ldec_saldo=$ld_totmonhab=$ld_totmondeb=0;
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];

		$ls_sql = "SELECT SUM(monto - monret) As monhab, 0 As mondeb 
				     FROM scb_movbco 
					WHERE codemp='".$ls_codemp."' 
					  AND codban='".$as_codban."' 
					  AND trim(ctaban)='".trim($as_ctaban)."' 
					  AND (codope='RE' OR codope='ND' OR codope='CH') 
					  AND estmov<>'A' 
					  AND estmov<>'O'
			        GROUP BY codemp, codban, ctaban
					UNION
				   SELECT 0 As monhab, SUM(monto - monret) As mondeb 
					 FROM scb_movbco 
					WHERE codemp='".$ls_codemp."' 
					  AND codban='".$as_codban."' 
					  AND trim(ctaban)='".trim($as_ctaban)."' 
					  AND (codope='NC' OR codope='DP') 
					  AND estmov<>'A' 
					  AND estmov<>'O'
					GROUP BY codemp, codban, ctaban";
		$rs_data = $io_sql->select($ls_sql);
		if ($rs_data===false)
		   {
			 $mensajes->message("CLASE->Impotar Movimientos MÉTODO->uf_verificar_saldo ERROR"); 
		     $lb_valido = false;
			 $ldec_saldo=0;
		   }
		else
		   {
				while(!$rs_data->EOF)
				{
					$ldec_monto_debe  = $rs_data->fields["mondeb"];
					$ldec_monto_haber = $rs_data->fields["monhab"];
					$ld_totmondeb += $ldec_monto_debe;
					$ld_totmonhab += $ldec_monto_haber;
					$lb_valido=true;
					$rs_data->MoveNext();
				}
				$ldec_saldo = $ld_totmondeb-$ld_totmonhab;
		   }	
		$arrResultado['ldec_saldo']=$ldec_saldo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}
	
	function uf_select_cuenta_contable_banco($ls_codban,$ls_ctaban)
	{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_select_cuenta_contable_banco
			//		   Access: private
			//	    Arguments: ls_codemp // COdigo de Empresa
			//	    		   ls_codban // Codigo de Banco
			//	    		   ls_ctaban // Cuenta de Banco
			// 	      Returns: lb_valido True si se abrio el archivo ó False si no se abrio
			//	  Description: Funcion que busca la cuenta contable del banco 
			//	   Creado Por: Ing. Luis Anibal Lang
			// Fecha Creación: 15/09/2017 								Fecha Última Modificación : 
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
			require_once("../../base/librerias/php/general/sigesp_lib_include.php");
			require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
			$io_include=new sigesp_include();
			$con=$io_include->uf_conectar();
			$io_sql=new class_sql($con);
			$mensajes=new class_mensajes();
	
			$ls_codemp=$_SESSION["la_empresa"]["codemp"];
			$ls_scgcta = "";
			$ls_sql="SELECT TRIM(sc_cuenta) as sc_cuenta".
					"  FROM scb_banco,scb_ctabanco ".
					" WHERE scb_banco.codemp='".$ls_codemp."'".
					"	AND scb_banco.codban = '".$ls_codban."'".
					"	AND scb_ctabanco.ctaban = '".$ls_ctaban."'".
					"   AND scb_banco.codemp=scb_ctabanco.codemp".
					"   AND scb_banco.codban=scb_ctabanco.codban"  ;
			$rs_data = $io_sql->select($ls_sql);				  
			if ($rs_data===false)	
			{
				 $mensajes->message("CLASE->Impotar Movimientos MÉTODO->uf_select_cuenta_contable_banco ERROR"); 
				 $lb_valido=false;
			}
			else
			{
				if ($row=$io_sql->fetch_row($rs_data))
				{
					$ls_scgcta = $row["sc_cuenta"];
				}
			}
			return $ls_scgcta;
	}

	function uf_select_movimiento_banco($ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope)
	{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_select_cuenta_contable_banco
			//		   Access: private
			//	    Arguments: ls_codemp // COdigo de Empresa
			//	    		   ls_codban // Codigo de Banco
			//	    		   ls_ctaban // Cuenta de Banco
			// 	      Returns: lb_valido True si se abrio el archivo ó False si no se abrio
			//	  Description: Funcion que busca la cuenta contable del banco 
			//	   Creado Por: Ing. Luis Anibal Lang
			// Fecha Creación: 15/09/2017 								Fecha Última Modificación : 
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
			require_once("../../base/librerias/php/general/sigesp_lib_include.php");
			require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
			$io_include=new sigesp_include();
			$con=$io_include->uf_conectar();
			$io_sql=new class_sql($con);
			$mensajes=new class_mensajes();
			$lb_existe=false;
			$ls_codemp=$_SESSION["la_empresa"]["codemp"];
			$ls_scgcta = "";

			$ls_sql="SELECT numdoc".
					"  FROM scb_movbco ".
					" WHERE scb_movbco.codemp='".$ls_codemp."'".
					"	AND scb_movbco.codban = '".$ls_codban."'".
					"	AND scb_movbco.ctaban = '".$ls_ctaban."'".
					"	AND scb_movbco.numdoc = '".$ls_numdoc."'".
					"	AND scb_movbco.codope = '".$ls_codope."'";
			$rs_data = $io_sql->select($ls_sql);				  
			if ($rs_data===false)	
			{
				 $mensajes->message("CLASE->Impotar Movimientos MÉTODO->uf_select_cuenta_contable_banco ERROR"); 
				 $lb_existe=false;
			}
			else
			{
				if ($row=$io_sql->fetch_row($rs_data))
				{
					$lb_existe=true;
				}
			}
			return $lb_existe;
	}
	function uf_recargar_data()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_importar_data
		//		   Access: private
		//	    Arguments: ao_archivo // conexión del archivo que se desea abrir
		// 	      Returns: lb_valido True si se abrio el archivo ó False si no se abrio
		//	  Description: Funcion que abre un archivo de texto dada una ruta 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 15/09/2017 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		global $io_funciones_scb,$io_grid;
		$li_total=$io_funciones_scb->uf_obtenervalor("rows","");
		for($li_i=1;($li_i<=$li_total);$li_i++)
		{
			$ls_operacion=$io_funciones_scb->uf_obtenervalor("txtoperacion".$li_i,"");
			$ls_documento=$io_funciones_scb->uf_obtenervalor("txtdocumento".$li_i,"");
			$ls_fecha=$io_funciones_scb->uf_obtenervalor("txtfecha".$li_i,"");
			$ls_banco=$io_funciones_scb->uf_obtenervalor("txtbanco".$li_i,"");
			$ls_codban=$io_funciones_scb->uf_obtenervalor("txtcodban".$li_i,"");
			$adec_saldo=$io_funciones_scb->uf_obtenervalor("txtdisponible".$li_i,"");
			$ls_sccuenta=$io_funciones_scb->uf_obtenervalor("txtsccuenta".$li_i,"");
			$ls_ctaban=$io_funciones_scb->uf_obtenervalor("txtcuenta".$li_i,"");
			$li_monto=$io_funciones_scb->uf_obtenervalor("txtmonto".$li_i,"");
			$ls_operacionaux=$ls_operacion;
			switch($ls_operacion)
			{	
				case "DEP":
					$ls_operacionaux="DP";
				break;
			}
			$lb_existe=uf_select_movimiento_banco($ls_codban,$ls_ctaban,$ls_documento,$ls_operacionaux);

		   if (!empty($ls_operacion))
		   {
				//$li_monto=$io_funciones_scb->uf_formatonumerico($li_monto);
	
				$li++;
				$lo_object[$li][1]="<input type=text name=txtoperacion".$li."  id=txtoperacion".$li."  class=sin-borde style=text-align:center size=5 value='".$ls_operacion."'    readonly>".
								   "<input type=hidden name=txtexiste".$li."  id=txtexiste".$li."  class=sin-borde style=text-align:center size=5 value='".$lb_existe."'    readonly>";
				$lo_object[$li][2]="<input type=text name=txtdocumento".$li."  id=txtdocumento".$li."  class=sin-borde style=text-align:left   size=12 value='".$ls_documento."'    readonly>";
				$lo_object[$li][3]="<input type=text name=txtbanco".$li."      id=txtbanco".$li."      class=sin-borde style=text-align:left   size=25 value='".$ls_banco."'    readonly>".
								   "<input type=hidden name=txtcodban".$li."      id=txtcodban".$li."      class=sin-borde style=text-align:left   size=15 value='".$ls_codban."'    readonly>".
								   "<input type=hidden name=txtdisponible".$li."      id=txtdisponible".$li."      class=sin-borde style=text-align:left   size=15 value='".$adec_saldo."'    readonly>".
								   "<input type=hidden name=txtsccuenta".$li."      id=txtsccuenta".$li."      class=sin-borde style=text-align:left   size=15 value='".$ls_sccuenta."'    readonly>";
				$lo_object[$li][4]="<input type=text name=txtcuenta".$li."     id=txtcuenta".$li."     class=sin-borde style=text-align:left   size=20 value='".$ls_ctaban."'    readonly>";
				$lo_object[$li][5]="<input type=text name=txtfecha".$li."      id=txttxtfecha".$li."   class=sin-borde style=text-align:left   size=10 value='".$ls_fecha."'    readonly>";
				$lo_object[$li][6]="<input type=text name=txtmonto".$li."      id=txtmonto".$li."      class=sin-borde style=text-align:right size=12 value='".$li_monto."'     onKeyPress=return(validaCajas(this,'d',event,15))  onKeyUp=javascript:uf_llamada_validarmonto(this) onBlur=javascript:ue_getformat(this) >";
				$lo_object[$li][7]="<a href=javascript:uf_detalles_movimientos('".$li."');><img src=../shared/imagebank/mas.gif title=Detalles width=10 height=15 border=0></a>";
				if($lb_existe)
				{
					$lo_object[$li][8]="<img src=../shared/imagebank/ok.png title=Eliminar width=15 height=10 border=0>";
				}
				else
				{
					$lo_object[$li][8]="<img src=../shared/imagebank/failed.png title=Eliminar width=15 height=10 border=0>";
				}
				
			}			   

		}
		// Titulos del Grid de Bienes
		$lo_title[1]="Operacion";
		$lo_title[2]="Documento";
		$lo_title[3]="Banco";
		$lo_title[4]="Cuenta";
		$lo_title[5]="Fecha";
		$lo_title[6]="Monto";
		$lo_title[7]="Detalles";
		$lo_title[8]="Registro";
		$io_grid->make_gridScroll($li,$lo_title,$lo_object,800,"Documentos","grid",200);

		print "<input type=hidden name=rows id=rows value=".$li.">";



	}
	//-----------------------------------------------------------------------------------------------------------------------------------

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>