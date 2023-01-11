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

	session_start();  
    ini_set('memory_limit','2048M');
	ini_set('max_execution_time ','0');
	require_once("../../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_mensajes=new class_mensajes();
	require_once("class_funciones_cxp.php");
	$io_funciones_cxp=new class_funciones_cxp();
	require_once("sigesp_cxp_c_recepcion.php");
	$io_recepcion=new sigesp_cxp_c_recepcion('../../');
	// proceso a ejecutar
	$ls_proceso=$io_funciones_cxp->uf_obtenervalor("funcion","");
	$ls_tipdes=$io_funciones_cxp->uf_obtenervalor("cmbtipdes","");
	$ls_codproben=$io_funciones_cxp->uf_obtenervalor("txtcodigo","");
	$ls_codtipdoc=substr($io_funciones_cxp->uf_obtenervalor("cmbcodtipdoc",""),0,5);
	$ls_estpre=substr($io_funciones_cxp->uf_obtenervalor("cmbcodtipdoc",""),6,1);
	$ls_estcon=substr($io_funciones_cxp->uf_obtenervalor("cmbcodtipdoc",""),8,1);
	switch($ls_proceso)
	{
		case "RELOAD"://Pinta los grid de detalle de la nota cuando se agrega un detalle o un cargo,lo usa cuando son recepciones con afectacion presupuestaria
			uf_recargar_data();	
			break;	
		default:
			uf_cargar_archivo($ls_tipdes,$ls_codproben,$ls_codtipdoc);	
			break;
	}

	function uf_cargar_archivo($ls_tipdes,$ls_codproben,$ls_codtipdoc)
	{
		global $io_funciones_cxp,$io_grid,$io_recepcion,$io_mensajes,$ls_estpre,$ls_estcon;
		require_once("../../base/librerias/php/readexcel/reader.php");
		$io_excel = new Spreadsheet_Excel_Reader();
		$upload_dir = "../documentos";
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
					$io_excel->read('../documentos/'.$filename);
					$li=0;
					if(($ls_estpre=="1")&&($ls_estcon=="1"))
					{
						for($li_indexfil=2;($li_indexfil<=$io_excel->sheets[0]['numRows']);$li_indexfil++)
						{
						   $ls_numrecdoc=$io_excel->sheets[0]['cells'][$li_indexfil][2];
						   $ls_numref=$io_excel->sheets[0]['cells'][$li_indexfil][3];
						   $ls_fecfac=$io_excel->sheets[0]['cells'][$li_indexfil][4];
						   $lb_validofecha=$io_funciones_cxp->validar_fecha($ls_fecfac);
						   if(!empty($ls_numrecdoc))
						   {
							   if(!$lb_validofecha)
							   {
									$io_mensajes->uf_mensajes_ajax("Error","La fecha es invalida. El formato de la celda debe ser: General",false,"");
									$lb_valido=false;
									$li=1;
									$lo_object[$li][1]="<input type=text name=txtnumrecdoc".$li."   id=txtnumrecdoc".$li."    class=sin-borde style=text-align:center size=15 value=''    readonly>";
									$lo_object[$li][2]="<input type=text name=txtnumref".$li."      id=txtnumref".$li."    class=sin-borde style=text-align:left   size=15 value=''    readonly>";
									$lo_object[$li][3]="<input type=text name=txtfecha".$li."       id=txtfecha".$li."     class=sin-borde style=text-align:left   size=10 value=''    readonly>";
									$lo_object[$li][4]="<input type=text name=txtcompromiso".$li."   id=txtcompromiso".$li."     class=sin-borde style=text-align:left   size=10 value=''    readonly>";
									$lo_object[$li][5]="<input type=text name=txtmonto".$li."       id=txtmonto".$li."        class=sin-borde style=text-align:right size=20 value=''     onKeyPress=return(validaCajas(this,'d',event,15))  onKeyUp=javascript:uf_llamada_validarmonto(this) onBlur=javascript:ue_getformat(this) >";
									$lo_object[$li][6]="";
									break;
							   }
						   }
						   
						   $ls_spgcuenta=$io_excel->sheets[0]['cells'][$li_indexfil][1];
						   $ls_concepto=$io_excel->sheets[0]['cells'][$li_indexfil][5];
						   $ls_cuentadebe=$io_excel->sheets[0]['cells'][$li_indexfil][6];
						   $ls_cuentahaber=$io_excel->sheets[0]['cells'][$li_indexfil][7];
						   $lb_existe=$io_recepcion->uf_select_recepcion($ls_numrecdoc,$ls_tipdes,$ls_codproben,$ls_codtipdoc);
						   if (!empty($ls_numrecdoc))
						   {
								$li_monto=$io_funciones_cxp->uf_formatonumerico($li_monto);
					
								$li++;
								$lo_object[$li][1]="<input type=text name=txtnumrecdoc".$li."   id=txtnumrecdoc".$li."    class=sin-borde style=text-align:center size=14 value='".$ls_numrecdoc."'    readonly>".
												   "<input type=hidden name=txtexiste".$li."  id=txtexiste".$li."  class=sin-borde style=text-align:center size=5 value='".$lb_existe."'    readonly>";
								$lo_object[$li][2]="<input type=text name=txtnumref".$li."      id=txtnumref".$li."    class=sin-borde style=text-align:left   size=14 value='".$ls_numref."'    readonly>";
								$lo_object[$li][3]="<input type=text name=txtfecha".$li."       id=txtfecha".$li."     class=sin-borde style=text-align:left   size=8 value='".$ls_fecfac."'    readonly>";
								$lo_object[$li][4]="<input type=text name=txtcompromiso".$li."   id=txtcompromiso".$li."     class=sin-borde style=text-align:left   size=13 value='".$ls_spgcuenta."'    readonly>";
								$lo_object[$li][5]="";
								$lo_object[$li][5]="<input type=text name=txtconcepto".$li."       id=txtconcepto".$li."        class=sin-borde style=text-align:left size=30 value='".$ls_concepto."' readonly ><input type=hidden name=txtmonto".$li."       id=txtmonto".$li."        class=sin-borde style=text-align:right size=8 value='0'     onKeyPress=return(validaCajas(this,'d',event,15))  onKeyUp=javascript:uf_llamada_validarmonto(this) onBlur=javascript:ue_getformat(this) ><input type=hidden name=txtcuentadebe".$li."   id=txtcuentadebe".$li." value='".$ls_cuentadebe."'><input type=hidden name=txtcuentahaber".$li."   id=txtcuentahaber".$li." value='".$ls_cuentahaber."'>";
								$lo_object[$li][6]="<a href=javascript:uf_detalles_movimientos2('".$li."');><img src=../shared/imagebank/mas.gif title=Detalles width=10 height=15 border=0></a>";
								if($lb_existe)
								{
									$lo_object[$li][7]="<img src=../shared/imagebank/ok.png title=Documento Registrado width=15 height=10 border=0>";
								}
								else
								{
									$lo_object[$li][7]="<img src=../shared/imagebank/failed.png title=Documento No Registrato width=15 height=10 border=0>";
								}
							}			   
						}
						//$li=$li-1;
						// Titulos del Grid de Bienes
						$lo_title[1]="Factura";
						$lo_title[2]="Control";
						$lo_title[3]="Fecha";
						$lo_title[4]="Compromiso";
						$lo_title[5]="Concepto";
						$lo_title[6]="Detalles";
						$lo_title[7]="Registro";
						$io_grid->make_gridScroll($li,$lo_title,$lo_object,750,"Documentos","grid",100);
				
					}
					else
					{
						for($li_indexfil=2;($li_indexfil<=$io_excel->sheets[0]['numRows']);$li_indexfil++)
						{
						   $ls_numrecdoc=$io_excel->sheets[0]['cells'][$li_indexfil][3];
						   $ls_numref=$io_excel->sheets[0]['cells'][$li_indexfil][4];
						   $ls_fecfac=$io_excel->sheets[0]['cells'][$li_indexfil][5];
						   $ls_fecfac=$io_excel->sheets[0]['cells'][$li_indexfil][5];
						   $lb_validofecha=$io_funciones_cxp->validar_fecha($ls_fecfac);
						   if(!empty($ls_numrecdoc))
						   {
							   if(!$lb_validofecha)
							   {
									$io_mensajes->uf_mensajes_ajax("Error","La fecha es invalida. El formato de la celda debe ser: General",false,"");
									$lb_valido=false;
									$li=1;
									$lo_object[$li][1]="<input type=text name=txtnumrecdoc".$li."   id=txtnumrecdoc".$li."    class=sin-borde style=text-align:center size=15 value=''    readonly>";
									$lo_object[$li][2]="<input type=text name=txtnumref".$li."      id=txtnumref".$li."    class=sin-borde style=text-align:left   size=15 value=''    readonly>";
									$lo_object[$li][3]="<input type=text name=txtfecha".$li."       id=txtfecha".$li."     class=sin-borde style=text-align:left   size=10 value=''    readonly>";
									$lo_object[$li][4]="<input type=text name=txtspgcuenta".$li."   id=txtspgcuenta".$li."     class=sin-borde style=text-align:left   size=10 value=''    readonly>";
									$lo_object[$li][5]="<input type=text name=txtmonto".$li."       id=txtmonto".$li."        class=sin-borde style=text-align:right size=20 value=''     onKeyPress=return(validaCajas(this,'d',event,15))  onKeyUp=javascript:uf_llamada_validarmonto(this) onBlur=javascript:ue_getformat(this) >";
									$lo_object[$li][6]="";
									break;
							   }
						   }
						   
						   $ls_spgcuenta=$io_excel->sheets[0]['cells'][$li_indexfil][1];
						   $li_monto=$io_excel->sheets[0]['cells'][$li_indexfil][2];
						   $lb_existe=$io_recepcion->uf_select_recepcion($ls_numrecdoc,$ls_tipdes,$ls_codproben,$ls_codtipdoc);
						   if (!empty($ls_numrecdoc))
						   {
								$li_monto=$io_funciones_cxp->uf_formatonumerico($li_monto);
					
								$li++;
								$lo_object[$li][1]="<input type=text name=txtnumrecdoc".$li."   id=txtnumrecdoc".$li."    class=sin-borde style=text-align:center size=15 value='".$ls_numrecdoc."'    readonly>".
												   "<input type=hidden name=txtexiste".$li."  id=txtexiste".$li."  class=sin-borde style=text-align:center size=5 value='".$lb_existe."'    readonly>";
								$lo_object[$li][2]="<input type=text name=txtnumref".$li."      id=txtnumref".$li."    class=sin-borde style=text-align:left   size=15 value='".$ls_numref."'    readonly>";
								$lo_object[$li][3]="<input type=text name=txtfecha".$li."       id=txtfecha".$li."     class=sin-borde style=text-align:left   size=10 value='".$ls_fecfac."'    readonly>";
								$lo_object[$li][4]="<input type=text name=txtspgcuenta".$li."   id=txtspgcuenta".$li."     class=sin-borde style=text-align:left   size=10 value='".$ls_spgcuenta."'    readonly>";
								$lo_object[$li][5]="<input type=text name=txtmonto".$li."       id=txtmonto".$li."        class=sin-borde style=text-align:right size=20 value='".$li_monto."'     onKeyPress=return(validaCajas(this,'d',event,15))  onKeyUp=javascript:uf_llamada_validarmonto(this) onBlur=javascript:ue_getformat(this) >";
								$lo_object[$li][6]="<a href=javascript:uf_detalles_movimientos('".$li."');><img src=../shared/imagebank/mas.gif title=Detalles width=10 height=15 border=0></a>";
								if($lb_existe)
								{
									$lo_object[$li][7]="<img src=../shared/imagebank/ok.png title=Documento Registrado width=15 height=10 border=0>";
								}
								else
								{
									$lo_object[$li][7]="<img src=../shared/imagebank/failed.png title=Documento No Registrato width=15 height=10 border=0>";
								}
							}			   
						}
						//$li=$li-1;
						// Titulos del Grid de Bienes
						$lo_title[1]="Factura";
						$lo_title[2]="Control";
						$lo_title[3]="Fecha";
						$lo_title[4]="Cuenta";
						$lo_title[5]="Monto";
						$lo_title[6]="Detalles";
						$lo_title[7]="Registro";
						$io_grid->make_gridScroll($li,$lo_title,$lo_object,680,"Documentos","grid",100);
				
					}
					print "<input type=hidden name=rows id=rows value=".$li.">";
	
				}//if ($_FILES['arcimp']['error'] == UPLOAD_ERR_OK)
			}
		}
		unset($io_excel);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
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

		global $io_funciones_cxp,$io_grid;
		$li_total=$io_funciones_cxp->uf_obtenervalor("rows","");
		for($li_i=1;($li_i<=$li_total);$li_i++)
		{
			$ls_numrecdoc=$io_funciones_cxp->uf_obtenervalor("txtnumrecdoc".$li_i,"");
			$ls_numref=$io_funciones_cxp->uf_obtenervalor("txtnumref".$li_i,"");
			$ls_fecfac=$io_funciones_cxp->uf_obtenervalor("txtfecha".$li_i,"");
			$ls_spgcuenta=$io_funciones_cxp->uf_obtenervalor("txtspgcuenta".$li_i,"");
			$li_monto=$io_funciones_cxp->uf_obtenervalor("txtmonto".$li_i,"");

			$lb_existe=$io_recepcion->uf_select_recepcion($ls_numrecdoc,$ls_tipdes,$ls_codproben,$ls_codtipdoc);

		   if (!empty($ls_numrecdoc))
		   {
//				$li_monto=$io_funciones_cxp->uf_formatonumerico($li_monto);
	
				$li++;
				$lo_object[$li][1]="<input type=text name=txtnumrecdoc".$li."   id=txtnumrecdoc".$li."    class=sin-borde style=text-align:center size=15 value='".$ls_numrecdoc."'    readonly>".
								   "<input type=hidden name=txtexiste".$li."  id=txtexiste".$li."  class=sin-borde style=text-align:center size=5 value='".$lb_existe."'    readonly>";
				$lo_object[$li][2]="<input type=text name=txtnumref".$li."      id=txtnumref".$li."    class=sin-borde style=text-align:left   size=15 value='".$ls_numref."'    readonly>";
				$lo_object[$li][3]="<input type=text name=txtfecha".$li."       id=txtfecha".$li."     class=sin-borde style=text-align:left   size=10 value='".$ls_fecfac."'    readonly>";
				$lo_object[$li][4]="<input type=text name=txtspgcuenta".$li."   id=txtspgcuenta".$li."     class=sin-borde style=text-align:left   size=10 value='".$ls_spgcuenta."'    readonly>";
				$lo_object[$li][5]="<input type=text name=txtmonto".$li."       id=txtmonto".$li."        class=sin-borde style=text-align:right size=20 value='".$li_monto."'     onKeyPress=return(validaCajas(this,'d',event,15))  onKeyUp=javascript:uf_llamada_validarmonto(this) onBlur=javascript:ue_getformat(this) >";
				$lo_object[$li][6]="<a href=javascript:uf_detalles_movimientos('".$li."');><img src=../shared/imagebank/mas.gif title=Detalles width=10 height=15 border=0></a>";
				if($lb_existe)
				{
					$lo_object[$li][7]="<img src=../shared/imagebank/ok.png title=Documento Registrado width=15 height=10 border=0>";
				}
				else
				{
					$lo_object[$li][7]="<img src=../shared/imagebank/failed.png title=Documento No Registrato width=15 height=10 border=0>";
				}
				
			}			   

		}
		// Titulos del Grid de Bienes
		$lo_title[1]="Factura";
		$lo_title[2]="Control";
		$lo_title[3]="Fecha";
		$lo_title[4]="Cuenta";
		$lo_title[5]="Monto";
		$lo_title[6]="Detalles";
		$lo_title[7]="Registro";
		$io_grid->make_gridScroll($li,$lo_title,$lo_object,660,"Documentos","grid",100);

		print "<input type=hidden name=rows id=rows value=".$li.">";



	}
	//-----------------------------------------------------------------------------------------------------------------------------------


?>