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

session_start();
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
if(!array_key_exists("la_logusr",$_SESSION))
{
	print "<script language=JavaScript>";
	print "window.close();";
	print "</script>";		
}
$ls_logusr=$_SESSION["la_logusr"];
require_once("class_funciones_inventario.php");
$io_fun_inventario=new class_funciones_inventario();
$ls_permisos = "";
$la_seguridad = Array();
$la_permisos = Array();
$arrResultado = $io_fun_inventario->uf_load_seguridad("SIV","sigesp_siv_p_despacho.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos = $arrResultado['as_permisos'];
$la_seguridad = $arrResultado['aa_seguridad'];
$la_permisos = $arrResultado['aa_permisos'];
$ls_reporte=$io_fun_inventario->uf_select_config("SIV","REPORTE","ORDEN_DESPACHO","sigesp_siv_rfs_despachos.php","C");
$lb_cierrescg = $io_fun_inventario->uf_chkciescg();
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   function uf_seleccionarcombo($as_valores,$as_seleccionado,$aa_parametro,$li_total)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_seleccionarcombo
		//         Access: private
		//      Argumento: $as_valores      // valores que puede tomar el combo				
		//                 $as_seleccionado // item seleccionado				
		//                 $aa_parametro    // arreglo de seleccionados		
		//                 $li_total        // total de elementos en el combo
		//	      Returns:
		//    Description: Funcion que mantiene la seleccion de un combo despues de hacer un submit
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/02/2006								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		$la_valores = explode("-",$as_valores);
		for($li_index=0;$li_index<$li_total;++$li_index)
		{
			if($la_valores[$li_index]==$as_seleccionado)
			{
				$aa_parametro[$li_index]=" selected";
			}
		}
		return $aa_parametro;
   }
   //--------------------------------------------------------------

   function uf_agregarlineablanca($aa_object,$ai_totrows)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 		
		//                 $ai_totrows // ultima fila pintada en el grid		
		//	      Returns:
		//    Description: Funcion que agrega una linea en blanco al final del grid del detalle de despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/02/2006								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_estartpri;
		if($ls_estartpri==1)
		{
			$ls_href="";
		}
		else
		{
			$ls_href="<a href='javascript: ue_catalmacen(".$ai_totrows.");'><img src='../shared/imagebank/tools20/buscar.gif' alt='Codigo de Articulo' width='18' height='18' border='0'></a>";
		}
		$aa_object[$ai_totrows][1]="<input  name=txtdenart".$ai_totrows."     type=text   id=txtdenart".$ai_totrows." class=sin-borde size=25 maxlength=50 readonly>".
								   "<input  name=txtcodart".$ai_totrows."     type=hidden id=txtcodart".$ai_totrows." class=sin-borde size=20 maxlength=50 readonly>".
							 	   "<input name=txtcodartpri".$ai_totrows."  type=hidden id=txtcodartpri".$ai_totrows."    class=sin-borde size=15 maxlength=25 readonly>".
								   "<input  name=txtctagas".$ai_totrows."     type=hidden id=txtctagas".$ai_totrows." class=sin-borde size=20 maxlength=50 readonly>".
								   "<input  name=txtctasep".$ai_totrows."     type=hidden id=txtctasep".$ai_totrows." class=sin-borde size=20 maxlength=20 readonly>";
		$aa_object[$ai_totrows][2]="<input  name=txtdenunimed".$ai_totrows."  type=text   id=txtdenunimed".$ai_totrows." class=sin-borde size=13 maxlength=10 readonly>";
		$aa_object[$ai_totrows][3]="<input  name=txtcodalm".$ai_totrows."     type=text   id=txtcodalm".$ai_totrows." class=sin-borde size=13 maxlength=10 readonly>".
								   $ls_href;
		$aa_object[$ai_totrows][4]="<select name=cmbunidad".$ai_totrows."     style='width:60px '><option value=D>Detal</option><option value=M>Mayor</option></select>";
		$aa_object[$ai_totrows][5]="<input  name=txtcansol".$ai_totrows."     type=text   id=txtcansol".$ai_totrows." class=sin-borde size=12 maxlength=12 readonly>".
								   "<input  name=hidexistencia".$ai_totrows." type=hidden id=hidexistencia".$ai_totrows.">";
		$aa_object[$ai_totrows][6]="<input  name=txtpenart".$ai_totrows."     type=text   id=txtpenart".$ai_totrows." class=sin-borde size=12 maxlength=12 readonly>";
		$aa_object[$ai_totrows][7]="<input  name=txtcanart".$ai_totrows."     type=text   id=txtcanart".$ai_totrows." class=sin-borde size=12 maxlength=12 onKeyUp='javascript: ue_validarnumero(this);'  onBlur='javascript: ue_montosfactura(".$ai_totrows.");'>";
		$aa_object[$ai_totrows][8]="<input  name=txtpreuniart".$ai_totrows."  type=text   id=txtpreuniart".$ai_totrows." class=sin-borde size=14 maxlength=15 readonly>".
								   "<input  name=hidnumdocori".$ai_totrows."  type=hidden id=hidnumdocori".$ai_totrows.">";
		$aa_object[$ai_totrows][9]="<input  name=txtmontotart".$ai_totrows."  type=text   id=txtmontotart".$ai_totrows." class=sin-borde size=14 maxlength=15 readonly>";
		$aa_object[$ai_totrows][10]="<a href=javascript:uf_dt_activo(".$ai_totrows.");><img src=../shared/imagebank/mas.gif alt=Agregar Seriales width=15 height=15 border=0></a><input name=hclasi".$ai_totrows."     type=hidden id=hclasi".$ai_totrows."    class=sin-borde size=15 maxlength=25 readonly>".			
                                    " <input type=hidden name=hcodact".$ai_totrows."    id=hcodact".$ai_totrows." class=sin-borde size=15 maxlength=25 readonly>";

		return $aa_object;		
   }
   //--------------------------------------------------------------

   function uf_agregarlineablancacontable($aa_objectc,$ai_totrowsc)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablancacontable
		//         Access: private
		//      Argumento: $aa_objectc  // arreglo de titulos 		
		//                 $ai_totrowsc // ultima fila pintada en el grid		
		//	      Returns:
		//    Description: Funcion que agrega una linea en blanco al final del grid del detalle contable
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/02/2006								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_objectc[$ai_totrowsc][1]="<input  name=txtdenartc".$ai_totrowsc."  type=text   id=txtdenartc".$ai_totrowsc."  class=sin-borde size=15 maxlength=50 readonly>".
								     "<input  name=txtcodartc".$ai_totrowsc."  type=hidden id=txtcodartc".$ai_totrowsc."  class=sin-borde size=20 maxlength=50 readonly>".
		$aa_objectc[$ai_totrowsc][2]="<input  name=txtsccuenta".$ai_totrowsc." type=text   id=txtsccuenta".$ai_totrowsc." class=sin-borde size=15              readonly>";
		$aa_objectc[$ai_totrowsc][3]="<input  name=txtdebhab".$ai_totrowsc."   type=text   id=txtdebhab".$ai_totrowsc."   class=sin-borde size=5               readonly>";
		$aa_objectc[$ai_totrowsc][4]="<input  name=txtmonto".$ai_totrowsc."    type=text   id=txtcansolc".$ai_totrowsc."  class=sin-borde size=12              readonly>";
		return $aa_objectc;
   }
   //--------------------------------------------------------------

   function uf_limpiarvariables()
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//         Access: private
		//      Argumento:  	
		//	      Returns:
		//    Description: Funci?n que limpia todas las variables necesarias en la p?gina
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/02/2006								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_numorddes,$ls_numsol,$ls_coduniadm,$ls_denuniadm,$ls_obsdes,$ld_fecdes,$li_estint,$ls_ctascgint;
		global $ls_codusu,$ls_readonly,$ls_codunides,$ls_denunides,$ls_checkedparc,$ls_checkedcomp,$ls_codfuefin,$ls_denfuefin;
		
		$ls_numorddes="";
		$ls_numsol="";
		$ls_coduniadm="";
		$ls_denuniadm="";
		$ls_obsdes="";
		$ld_fecdes=date("d/m/Y");
		$ls_codusu=$_SESSION["la_logusr"];
		$ls_readonly="true";
		$ls_codunides="";
		$ls_denunides="";
		$ls_checkedparc="";
		$ls_checkedcomp="";
		$li_estint = 0;
		$ls_ctascgint = "";
		$ls_vienecat= 0;
		$ls_codfuefin="";
		$ls_denfuefin="";
		
   }

   function uf_titulosdespacho()
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_titulosdespacho
		//         Access: private
		//      Argumento:  	
		//	      Returns:
		//    Description: Funci?n que carga las caracteristicas del grid de detalle de despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/02/2006								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_titletable,$li_widthtable,$ls_nametable,$lo_title;
		
		$ls_titletable="Detalle del Despacho";
		$li_widthtable=800;
		$ls_nametable="grid";
		$lo_title[1]="Art?culo";
		$lo_title[2]="Unidad Medida";
		$lo_title[3]="Almac?n";
		$lo_title[4]="Modalidad";
		$lo_title[5]="Cant. Solicitada";
		$lo_title[6]="Cant. Pendiente";
		$lo_title[7]="Cant. a Despachar";
		$lo_title[8]="Precio Unitario";
		$lo_title[9]="Total";
		$lo_title[10]="";
   }

   function uf_tituloscontable()
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_tituloscontable
		//         Access: private
		//      Argumento:  	
		//	      Returns:
		//    Description: Funci?n que carga las caracteristicas del grid de detalle contable
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/02/2006								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_titlecontable,$li_widthcontable,$ls_namecontable,$lo_titlecontable;
		
		$ls_titlecontable="Detalle Contable";
		$li_widthcontable=800;
		$ls_namecontable="grid";
		$lo_titlecontable[1]="Art?culo";
		$lo_titlecontable[2]="Cuenta";
		$lo_titlecontable[3]="Debe/Haber";
		$lo_titlecontable[4]="Monto";
   }
   
   function uf_titulospresupuestario()
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_titulospresupuestario
		//         Access: private
		//      Argumento:  	
		//	      Returns:
		//    Description: Funci?n que carga las caracteristicas del grid de detalle contable
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/02/2006								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_titlepresupuestario,$li_widthpresupuestario,$ls_namepresupuestario,$lo_titlepresupuestario;
		
		$ls_titlepresupuestario="Detalle Presupuestario";
		$li_widthpresupuestario=800;
		$ls_namepresupuestario="grid";
		$lo_titlepresupuestario[1]="Estructura";
		$lo_titlepresupuestario[2]="Estatus";
		$lo_titlepresupuestario[3]="Cuenta";
		$lo_titlepresupuestario[4]="Monto";
   }
   
   function uf_obtenervalorunidad($li_i)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtenervalorunidad
		//         Access: private
		//      Argumento: $li_i  //  indica que opcion esta seleccionado en el combo	
		//	      Returns: Retorna el valor obtenido
		//    Description: Funci?n que obtiene el contenido del combo cmbunidad o del campo txtunidad deacuerdo sea el caso 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/02/2006								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if (array_key_exists("cmbunidad".$li_i,$_POST))
		{
			$ls_valor= $_POST["cmbunidad".$li_i];
		}
		else
		{
			$ls_valoraux= $_POST["txtunidad".$li_i];
			if($ls_valoraux=="Mayor")
			{
				$ls_valor="M";
			}
			else
			{
				$ls_valor="D";
			}
		}
   		return $ls_valor; 
   }
   
	function uf_incluircontable($as_codemp,$as_numorddes,$ad_fecdes,$aa_objectc,$ai_totrowsc,$aa_seguridad,$io_fun_inventario,$io_siv,$ai_estint)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablancacontable
		//         Access: private
		//      Argumento: $as_codemp         // codigo de empresa
		//                 $as_numorddes      // numero de orden de despacho
		//                 $ad_fecdes         // fecha del despacho	
		//                 $aa_objectc        // arreglo de titulos 		
		//                 $ai_totrowsc       // ultima fila pintada en el grid		
		//                 $aa_seguridad      // arreglo de seguridad
		//                 $io_fun_inventario // instancia de la clase de funciones de inventario	
		//                 $io_siv            // instancia de la clase sigesp_siv_c_despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que pinta nuevamente el grid de detalle contable con los datos que estaban en el ademas de 
		//                 activar el proceso de insert del mismo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/02/2006								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_ds;
		$lb_valido=true;
		for($li_j=1;$li_j<=$ai_totrowsc;$li_j++ && $lb_valido)
		{
			$ls_codart=    $io_fun_inventario->uf_obtenervalor("txtcodartc".$li_j,"");
			$ls_denart=    $io_fun_inventario->uf_obtenervalor("txtdenartc".$li_j,"");
			$ls_sccuenta=  trim($io_fun_inventario->uf_obtenervalor("txtsccuenta".$li_j,""));
			$ls_debhab=    $io_fun_inventario->uf_obtenervalor("txtdebhab".$li_j,"");
			$li_montoc=    $io_fun_inventario->uf_obtenervalor("txtmonto".$li_j,"");
			$li_montoc=    str_replace(".","",$li_montoc);
			$li_montoc=    str_replace(",",".",$li_montoc);
			$io_ds->insertRow("codart",$ls_codart);
			$io_ds->insertRow("denart",$ls_denart);
			$io_ds->insertRow("sccuenta",$ls_sccuenta);
			$io_ds->insertRow("debhab",$ls_debhab);
			$io_ds->insertRow("montoc",$li_montoc);
		}
		$io_ds->group_by(array('0'=>'codart','1'=>'denart','2'=>'sccuenta','3'=>'debhab'),array('0'=>'montoc'),'codart');
		$li_totrow=$io_ds->getRowCount("codart");
		for($li_j=1;$li_j<=$li_totrow;$li_j++ && $lb_valido)
		{
			$ls_codart=    $io_ds->data["codart"][$li_j];
			$ls_denart=    $io_ds->data["denart"][$li_j];
			$ls_sccuenta=    $io_ds->data["sccuenta"][$li_j];
			$ls_debhab=    $io_ds->data["debhab"][$li_j];
			$li_montoc=    $io_ds->data["montoc"][$li_j];
			$li_montotot= $li_montoc;
			$lb_valido = $io_siv->uf_siv_insert_dt_scg($as_codemp,$ls_codart,$as_numorddes,$ad_fecdes,$ls_sccuenta,$ls_debhab,
													   $li_montotot,$aa_seguridad);
			if ($lb_valido)
			   {
				 if ($ai_estint==1)
				    {
					  if ($ls_debhab=='D')
					     {
					       $lb_valido = $io_siv->uf_siv_insert_dt_scg_int($as_codemp,'--------------------',$as_numorddes,$ad_fecdes,$ls_sccuenta,'H',$li_montotot,$aa_seguridad);					
						 }
					  elseif($ls_debhab=='H')
					     {
						   $li_i = $li_j - 1;
						   $ls_scgctagas = trim($io_fun_inventario->uf_obtenervalor("txtctagas".$li_i,""));
						   if (!empty($ls_scgctagas))
						      {
							    $lb_valido = $io_siv->uf_siv_insert_dt_scg_int($as_codemp,$ls_codart,$as_numorddes,$ad_fecdes,$ls_scgctagas,'D',$li_montotot,$aa_seguridad);					
							  }
						   else
						      {
							    print "<script>";
								print "alert('Cuenta Contable del Gasto asociada al Articulo no encontrada !!!')";
								print "</script>";
								return false;
							  }
						 }
					}
			   }											 

			$aa_objectc[$li_j][1]="<input  name=txtdenartc".$li_j."  type=text   id=txtdenartc".$li_j."  class=sin-borde size=50  value='".$ls_denart."'   readonly style='text-align:left'>".
								  "<input  name=txtcodartc".$li_j."  type=hidden id=txtcodartc".$li_j."  class=sin-borde size=30  value='".$ls_codart."'   readonly>";
			$aa_objectc[$li_j][2]="<input  name=txtsccuenta".$li_j." type=text   id=txtsccuenta".$li_j." class=sin-borde size=30  value='".$ls_sccuenta."' readonly style='text-align:center'>";
			$aa_objectc[$li_j][3]="<input  name=txtdebhab".$li_j."   type=text   id=txtdebhab".$li_j."   class=sin-borde size=15  value='".$ls_debhab."'   readonly style='text-align:center'>";
			$aa_objectc[$li_j][4]="<input  name=txtmonto".$li_j."    type=text   id=txtcansolc".$li_j."  class=sin-borde size=30  value='".number_format ($li_montoc,2,",",".")."' style='text-align:right' readonly>";
		}
		$arrResultado['aa_objectc']=$aa_objectc;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}
	
	
	function uf_incluirpresupuestario($as_codemp,$as_numorddes,$ad_fecdes,$aa_objectp,$ai_totrowp,$aa_seguridad,$io_fun_inventario,$io_siv,$ai_estint)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablancacontable
		//         Access: private
		//      Argumento: $as_codemp         // codigo de empresa
		//                 $as_numorddes      // numero de orden de despacho
		//                 $ad_fecdes         // fecha del despacho	
		//                 $aa_objectc        // arreglo de titulos 		
		//                 $ai_totrowsc       // ultima fila pintada en el grid		
		//                 $aa_seguridad      // arreglo de seguridad
		//                 $io_fun_inventario // instancia de la clase de funciones de inventario	
		//                 $io_siv            // instancia de la clase sigesp_siv_c_despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que pinta nuevamente el grid de detalle contable con los datos que estaban en el ademas de 
		//                 activar el proceso de insert del mismo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/02/2006								Fecha ?ltima Modificaci?n :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_ds;
		$lb_valido=true;
		for($li_j=1;$li_j<=$ai_totrowp;$li_j++ && $lb_valido)
		{
			$ls_codestpro1=$io_fun_inventario->uf_obtenervalor("txtcodestpro1".$li_j,"");
			$ls_codestpro2=$io_fun_inventario->uf_obtenervalor("txtcodestpro2".$li_j,"");
			$ls_codestpro3=$io_fun_inventario->uf_obtenervalor("txtcodestpro3".$li_j,"");
			$ls_codestpro4=$io_fun_inventario->uf_obtenervalor("txtcodestpro4".$li_j,"");
			$ls_codestpro5=$io_fun_inventario->uf_obtenervalor("txtcodestpro5".$li_j,"");
			$ls_estcla=    $io_fun_inventario->uf_obtenervalor("txtestcla".$li_j,"");
			$ls_programtatica=    $io_fun_inventario->uf_obtenervalor("txtprogramatica".$li_j,"");
			$ls_estclaaux=  trim($io_fun_inventario->uf_obtenervalor("txtestclaaux".$li_j,""));
			$ls_cuentapre=    $io_fun_inventario->uf_obtenervalor("txtspgcuenta".$li_j,"");
			$ls_montopre=    $io_fun_inventario->uf_obtenervalor("txtmonto".$li_j,"");
			$ls_montopre=    str_replace(".","",$ls_montopre);
			$ls_montopre=    str_replace(",",".",$ls_montopre);

			$lb_valido = $io_siv->uf_siv_insert_dt_spg($as_codemp,$as_numorddes,$ad_fecdes,$ls_codestpro1,$ls_codestpro2,
													   $ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_cuentapre,
													   $ls_montopre,$ls_codfuefin,$aa_seguridad);

			$aa_objectp[$li_j][1]="<input  name=txtprogramatica".$li_j."  type=text   id=txtprogramatica".$li_j."  class=sin-borde size=50 maxlength=50 value='".$ls_programatica."' readonly  style=text-align:center>".
								  "<input  name=txtcodestpro1".$li_j."  type=hidden id=txtcodestpro1".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_codestpro1."' readonly  style=text-align:center>".
								  "<input  name=txtcodestpro2".$li_j."  type=hidden id=txtcodestpro2".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_codestpro2."' readonly  style=text-align:center>".
								  "<input  name=txtcodestpro3".$li_j."  type=hidden id=txtcodestpro3".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_codestpro3."' readonly  style=text-align:center>".
								  "<input  name=txtcodestpro4".$li_j."  type=hidden id=txtcodestpro4".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_codestpro4."' readonly  style=text-align:center>".
								  "<input  name=txtcodestpro5".$li_j."  type=hidden id=txtcodestpro5".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_codestpro5."' readonly  style=text-align:center>".
								  "<input  name=txtestcla".$li_j."  type=hidden id=txtestcla".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_estcla."' readonly  style=text-align:center>";
			$aa_objectp[$li_j][2]="<input  name=txtestclaaux".$li_j." type=text   id=txtestclaaux".$li_j." class=sin-borde size=30              value='".$ls_estclaaux."' readonly  style=text-align:center>";
			$aa_objectp[$li_j][3]="<input  name=txtspgcuenta".$li_j."   type=text   id=txtspgcuenta".$li_j."   class=sin-borde size=15              value='".$ls_cuentapre."' readonly style='text-align:center'>";
			$aa_objectp[$li_j][4]="<input  name=txtmontop".$li_j."    type=text   id=txtmontop".$li_j."  class=sin-borde size=30              value='".number_format ($ls_montopre,2,",",".")."' style='text-align:right' readonly>";
		}
		$arrResultado['aa_objectp']=$aa_objectp;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Despacho de Suministros </title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #EFEBEF;
}

a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}

-->
</style>
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<script type="text/javascript"  src="js/funciones.js"></script>
<script type="text/javascript"  src="js/funcion_siv.js"></script>
<link href="css/siv.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey))
		{
			window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ return false;} 
		} 
	}
</script>
</head>

<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="10" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="10" bgcolor="#E7E7E7" class="cd-menu">
	<table width="776" border="0" align="center" cellpadding="0" cellspacing="0">
	
		<td width="423" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Inventario </td>
		<td width="353" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  <tr>
		<td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
		<td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
	</table></td>
  </tr>
  <tr>
    <td height="20" colspan="10" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="10" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" width="26" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_imprimir('<?php print $ls_reporte ?>');"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20"></div></td>
    <td width="627" class="toolbar">&nbsp;</td>
  </tr>
</table>
<?php
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=      new sigesp_include();
	$con=     $in->uf_conectar();
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql=  new class_sql($con);
	require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fec= new class_fecha();
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_msg=  new class_mensajes();
	require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
	$io_fun=  new class_funciones_db($con);
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_func= new class_funciones();
	require_once("../shared/class_folder/grid_param.php");
	$in_grid= new grid_param();
	require_once("sigesp_siv_c_despacho.php");
	$io_siv=  new sigesp_siv_c_despacho();
	require_once("sigesp_siv_c_articuloxalmacen.php");
	$io_art=  new sigesp_siv_c_articuloxalmacen();
	require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
	$io_keygen= new sigesp_c_generar_consecutivo();
    $ls_estatusscg=$io_siv->uf_cierrecontable();	
	require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
	$io_ds=  new class_datastore();
	$io_dspre=  new class_datastore();
	require_once("../shared/class_folder/evaluate_formula.php");
	$io_evaluate=  new evaluate_formula();
	if ($ls_estatusscg==1)
	{
	   $ls_disable="disabled";
	   $io_msg->message("Ya se realizo el cierre contable, solo podra realizar consultas de los despachos");
	}
	else
	{
		$ls_disable="";	
	}

	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_codusu=$_SESSION["la_logusr"];
	$li_totrows = $io_fun_inventario->uf_obtenervalor("totalfilas",1);
	$li_totrowsc= $io_fun_inventario->uf_obtenervalor("totalfilasc",1);
	$li_totrowp=$io_fun_inventario->uf_obtenervalor("totalfilasp",0);

	uf_titulosdespacho();
	uf_tituloscontable();
	uf_titulospresupuestario();
	$ls_operacion= $io_fun_inventario->uf_obteneroperacion();
	$ls_status=    $io_fun_inventario->uf_obtenervalor("hidestatus","");
	$ls_vienecat= 0;
	if ($ls_status=="C")
	{
		$ls_readonly=  $io_fun_inventario->uf_obtenervalor("hidreadonly","");
		$li_catafilas= $io_fun_inventario->uf_obtenervalor("catafilas","");
	}
	
	$arrResultado=$io_siv->uf_siv_load_contabilizacion($ls_codemp,$li_value);
	$li_value=$arrResultado['li_value'];
	$lb_cont=$arrResultado['lb_valido'];
	if($li_value==0)
	{
		$ls_ok=true;
	}
	$arrResultado=$io_siv->uf_siv_load_articulos_primarios($ls_codemp,$ls_estartpri);
	$ls_estartpri = $arrResultado['as_value'];
	$lb_valido=$arrResultado['lb_valido'];
	switch ($ls_operacion) 
	{

		case "NUEVO":
			uf_limpiarvariables();
			$ls_numorddes=$io_keygen->uf_generar_numero_nuevo("SIV","siv_despacho","numorddes","SIV",15,"","codemp",$ls_codemp);
			if($ls_numorddes==false)
			{
				print "<script language=JavaScript>";
				print "location.href='sigespwindow_blank.php'";
				print "</script>";
			}
			$lo_object = uf_agregarlineablanca($lo_object,1);
			$lo_objectc = uf_agregarlineablancacontable($lo_objectc,1);
		break;
		case "GUARDAR":
			uf_limpiarvariables();
			$ls_guardaseg= 1;
			$lb_descomp	= true;
			$ls_numorddes 	= $io_fun_inventario->uf_obtenervalor("txtnumorddes","");
			$ls_numsol	= $io_fun_inventario->uf_obtenervalor("txtnumsol","");
			$ls_coduniadm 	= $io_fun_inventario->uf_obtenervalor("txtcoduniadm",""); 
			$ls_denuniadm 	= $io_fun_inventario->uf_obtenervalor("txtdenuniadm",""); 
			$ld_fecdes	= $io_fun_inventario->uf_obtenervalor("txtfecdes",""); 
			$ls_obsdes	= $io_fun_inventario->uf_obtenervalor("txtobsdes","");
			$ls_estsol	= $io_fun_inventario->uf_obtenervalor("txtestsol","");
			$ls_codunides 	= $io_fun_inventario->uf_obtenervalor("txtcodunides","");
			$ls_denunides 	= $io_fun_inventario->uf_obtenervalor("txtdenunides","");
			$li_estint    	= $io_fun_inventario->uf_obtenervalor("hidestint","");
			$ls_ctascgint 	= $io_fun_inventario->uf_obtenervalor("hidctascgint","");			
			$ld_fecdesaux 	= $io_func->uf_convertirdatetobd($ld_fecdes);
			$ls_estrevdes 	= "1";
			$ls_estdes    	= "1";
			$ls_estrec	=  $io_fun_inventario->uf_obtenervalor("rdtipodespacho","");
			$ls_codfuefin 	= $io_fun_inventario->uf_obtenervalor("txtcodfuefin","");
			$ls_denfuefin 	= $io_fun_inventario->uf_obtenervalor("txtdenfuefin","");
			if($ls_estrec==0)
			{
				$ls_checkedparc="checked";
				$ls_checkedcomp="";
			}
			else
			{
				$ls_checkedparc="";
				$ls_checkedcomp="checked";
			}
			$lb_valido=$io_fec->uf_valida_fecha_mes($ls_codemp,$ld_fecdes);
			//$lb_valido=$io_fec->uf_valida_fecha_periodo($ld_fecdes,$ls_codemp);
			if($lb_valido)
			{
				$io_sql->begin_transaction();
				$arrResultado=$io_siv->uf_siv_insert_despacho($ls_codemp,$ls_numorddes,$ls_numsol,$ls_coduniadm,$ld_fecdesaux,$ls_obsdes,
														      $ls_logusr,$ls_estdes,$ls_estrevdes,$ls_codunides,$ls_codfuefin,$la_seguridad);
				$ls_numorddes = $arrResultado['as_numorddes'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido)
				{
	
					$ls_nummov=0;
					$ls_nomsol="Despacho";
					$arrResultado=$io_siv->io_mov->uf_siv_insert_movimiento($ls_nummov,$ld_fecdesaux,$ls_nomsol,$ls_logusr,$la_seguridad);
					$ls_nummov=$arrResultado['as_nummov'];
					$lb_valido=$arrResultado['lb_valido'];
					if($lb_valido)
					{
						$lb_exito=true;
						for($li_i=1;$li_i<=$li_totrows;$li_i++)
						{
							$ls_codart	 =    $io_fun_inventario->uf_obtenervalor("txtcodart".$li_i,"");
							$ls_codartpri=    $io_fun_inventario->uf_obtenervalor("txtcodartpri".$li_i,"");
							$ls_denart	 =    $io_fun_inventario->uf_obtenervalor("txtdenart".$li_i,"");
							$ls_denunimed=    $io_fun_inventario->uf_obtenervalor("txtdenunimed".$li_i,"");
							$ls_codalm	 =    $io_fun_inventario->uf_obtenervalor("txtcodalm".$li_i,"");
							$li_canorisolsep =    $io_fun_inventario->uf_obtenervalor("txtcansol".$li_i,"");
							$li_existencia	 =    $io_fun_inventario->uf_obtenervalor("hidexistencia".$li_i,"");
							$li_canart	 =    $io_fun_inventario->uf_obtenervalor("txtcanart".$li_i,"");
							$li_preuniart	 =    $io_fun_inventario->uf_obtenervalor("txtpreuniart".$li_i,"");
							$li_montotart	 =    $io_fun_inventario->uf_obtenervalor("txtmontotart".$li_i,"");
							$ls_unidad	 =    $io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
							$ls_hidunidad	 =    $io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
							$li_unidad	 =    $io_fun_inventario->uf_obtenervalor("hidunidad".$li_i,"");
							$ls_ctagas	 =    $io_fun_inventario->uf_obtenervalor("txtctagas".$li_i,"");
							$ls_ctasep	 =    $io_fun_inventario->uf_obtenervalor("txtctasep".$li_i,""); 
							$li_canpenart	 =    $io_fun_inventario->uf_obtenervalor("txtpenart".$li_i,"");
							$ls_clasif	 =    $io_fun_inventario->uf_obtenervalor("hclasi".$li_i,"");
							$ls_codact	 =    $io_fun_inventario->uf_obtenervalor("hcodact".$li_i,"");
							$li_canorisolsep =    str_replace(".","",$li_canorisolsep);
							$li_canorisolsep =    str_replace(",",".",$li_canorisolsep);
							$li_canart	 =    str_replace(".","",$li_canart);
							$li_canart	 =    str_replace(",",".",$li_canart);
							$li_preuniart	 =    str_replace(".","",$li_preuniart);
							$li_preuniart	 =    str_replace(",",".",$li_preuniart);
							$li_montotart	 =    str_replace(".","",$li_montotart);
							$li_montotart	 =    str_replace(",",".",$li_montotart);
							$li_canpenart	 =    str_replace(".","",$li_canpenart);
							$li_canpenart	 =    str_replace(",",".",$li_canpenart);
							$li_auxcanpenart =    $li_canpenart;
							$li_canartaux	 =    $li_canart;
							$li_canorisolsepaux	 =    $li_canorisolsep;
							
							if($ls_unidad=="")
							{
								$ls_unidad= $io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
								$ls_hidunidad= $io_fun_inventario->uf_obtenervalor("hidtxtuni".$li_i,"");
							}
							if($ls_unidad=="Mayor")
							{
								$ls_unidad="M";
								$li_canartaux=($li_canart*$li_unidad);
							}
							else
							{$ls_unidad="D";}
							if($ls_hidunidad=="Mayor")
							{
								$li_auxcanpenart=($li_canpenart*$li_unidad);
							}
							switch ($ls_unidad) 
							{
								case "M":
									$ls_unidadaux="Mayor";
								break;
								case "D":
									$ls_unidadaux="Detal";
								break;
							}
							if($ls_estartpri==1)
							{
								$ls_href="";
							}
							else
							{
								$ls_href="<a href='javascript: ue_catalmacen(".$li_i.");'><img src='../shared/imagebank/tools20/buscar.gif' alt='Codigo de Articulo' width='18' height='18' border='0'></a>";
							}
							$lo_object[$li_i][1]="<input name=txtdenart".$li_i."     type=text   id=txtdenart".$li_i."    class=sin-borde size=25 maxlength=50 value='".$ls_denart."' readonly>".
												 "<input name=txtcodart".$li_i."     type=hidden id=txtcodart".$li_i."    class=sin-borde size=15 maxlength=25   value='".$ls_codart."' readonly>".
									 	         "<input name=txtcodartpri".$li_i."  type=hidden id=txtcodartpri".$li_i."    class=sin-borde size=15 maxlength=25 value='".$ls_codartpri."' readonly>".
												 "<input name=txtctagas".$li_i."     type=hidden id=txtctagas".$li_i."    class=sin-borde size=20 maxlength=50   value='".$ls_ctagas."' readonly>".
												 "<input name=txtctasep".$li_i."     type=hidden id=txtctasep".$li_i."    class=sin-borde size=20 maxlength=20 value='".$ls_ctasep."' readonly>";
							$lo_object[$li_i][2]="<input name=txtdenunimed".$li_i."     type=text   id=txtdenunimed".$li_i."    class=sin-borde size=13 maxlength=10 value='". $ls_denunimed."' readonly>";
							$lo_object[$li_i][3]="<input name=txtcodalm".$li_i."     type=text   id=txtcodalm".$li_i."    class=sin-borde size=13 maxlength=10 value='". $ls_codalm."' readonly>".
												 $ls_href;
							$lo_object[$li_i][4]="<input name=txtunidad".$li_i."     type=text   id=txtunidad".$li_i."    class=sin-borde size=12 maxlength=12 value='". $ls_unidadaux."' readonly></div>".
												 "<input name='hidunidad".$li_i."'    type='hidden' id='hidunidad".$li_i."' value='". $li_unidad ."'>";
							$lo_object[$li_i][5]="<input name=txtcansol".$li_i."     type=text   id=txtcansol".$li_i."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canorisolsep,2,",",".")."' readonly>".
												 "<input name=hidexistencia".$li_i." type=hidden id=hidexistencia".$li_i."                                     value='". $li_existencia."'>";
							$lo_object[$li_i][6]="<input name=txtpenart".$li_i."     type=text   id=txtpenart".$li_i."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canpenart,2,",",".")."' readonly>";
							$lo_object[$li_i][7]="<input name=txtcanart".$li_i."     type=text   id=txtcanart".$li_i."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canart,2,",",".")."'  onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur='javascript: ue_montosfactura(".$li_i.");'>";
							$lo_object[$li_i][8]="<input name=txtpreuniart".$li_i."  type=text   id=txtpreuniart".$li_i." class=sin-borde size=14 maxlength=15 value='".number_format ($li_preuniart,2,",",".")."' readonly>".
												 "<input name=hidnumdocori".$li_i."  type=hidden id=hidnumdocori".$li_i.">";
							$lo_object[$li_i][9]="<input name=txtmontotart".$li_i."  type=text   id=txtmontotart".$li_i." class=sin-borde size=14 maxlength=15 value='".number_format ($li_montotart,2,",",".")."' readonly>";
							$lo_object[$li_i][10]="<a href=javascript:uf_dt_activo(".$li_i.");><img src=../shared/imagebank/mas.gif alt=Agregar Seriales width=15 height=15 border=0>
							<input name=hclasi".$li_i." type=hidden id=hclasi".$li_i." class=sin-borde size=15 maxlength=25 value='".$ls_clasif."' readonly></a>".			
						   " <input type=hidden name=hcodact".$li_i." id=hcodact".$li_i." class=sin-borde size=15 maxlength=25 value='".$ls_codact."' readonly>";

							if(($ls_codalm!="")&&($li_canart!="")&&($li_canart>0))
							{
								$lb_valido=$io_siv->uf_siv_procesar_dt_despacho($ls_codemp,$ls_numorddes,$ls_codart,$ls_codalm,$ls_unidad,
																				$li_canorisolsep,$li_canartaux,$li_preuniart,$li_montotart, //monsubart
																				$li_montotart,$li_i,$ls_nummov,$ld_fecdesaux,
																				$ls_numsol,$li_auxcanpenart,$la_seguridad);
								if($lb_valido)
								{
									$lb_valido=$io_art->uf_siv_disminuir_articuloxalmacen($ls_codemp,$ls_codart,$ls_codalm,$li_canartaux,
																						  $la_seguridad);
									if($lb_valido)
									{
										$lb_valido=$io_art->uf_siv_actualizar_cantidad_articulos($ls_codemp,$ls_codart,$la_seguridad);
									} // fin  if($lb_valido)->uf_siv_disminuir_articuloxalmacen
								} //fin  if($lb_valido)->uf_siv_insert_dt_despacho
								if($li_auxcanpenart>0)
								{
									$lb_descomp=false;
								}
							}//  fin if(($ls_codalm!="")&&($li_canart!="")&&($li_canart>0))
							else
							{
								if($li_canpenart>0)
								{
									$lb_descomp=false;
								}
							}
							if(!$lb_valido)
							{$lb_exito=false;}
						}  // fin  for($li_i=1;$li_i<$li_totrows;$li_i++)
						$arrResultado=$io_siv->uf_siv_load_codigoactivo($ls_codart,$ls_codact);
						$ls_codact=$arrResultado['as_codact'];
						$lb_valido=$arrResultado['lb_valido'];
						if($li_value==1)
						{
						   if (($ls_codact=='')||($ls_codact=='---------------'))
						   { 
							 $arrResultado=uf_incluircontable($ls_codemp,$ls_numorddes,$ld_fecdesaux,$lo_objectc,
						  							          $li_totrowsc,$la_seguridad,$io_fun_inventario,$io_siv,$li_estint);
							 $lo_objectc=$arrResultado['aa_objectc'];
							 $lb_valido=$arrResultado['lb_valido'];
						   }
						   if($lb_valido)
						   {
							 $arrResultado=uf_incluirpresupuestario($ls_codemp,$ls_numorddes,$ld_fecdesaux,$lo_objectp,
						  							  $li_totrowp,$la_seguridad,$io_fun_inventario,$io_siv,$li_estint);
							 $lo_objectp = $arrResultado['aa_objectp'];
							 $lb_valido = $arrResultado['lb_valido'];
						   }
						}
					}  //fin  if($lb_valido) uf_siv_insert_movimiento
				}  //fin  if($lb_valido)
				if($lb_valido)
				{
					$lb_pendientes=$io_siv->uf_siv_validar_pendientes($ls_codemp,$ls_numsol);
					if(!$lb_pendientes)
					{
						$lb_descomp=true;	
					}
					else
					{
						$lb_descomp=false;	
					}
				}
				if($lb_descomp)
				{
					$ls_estsep="D";
					$lb_valido=$io_siv->uf_siv_update_sep($ls_codemp,$ls_numsol,$ls_estsep);
				}
				else
				{
					$ls_estsep="L";
					$lb_valido=$io_siv->uf_siv_update_sep($ls_codemp,$ls_numsol,$ls_estsep); 
				
				}
				if(!$lb_exito)
				{$lb_valido=false;}
				if($lb_valido)
				{
					$io_sql->commit();
					$io_msg->message("El despacho ha sido procesado");
					$ls_status="C";
					////// agregado 23/09/08 /////
					$li_totrows=1;
					$li_totrowsc=1;
					$ls_numorddes=$io_keygen->uf_generar_numero_nuevo("SIV","siv_despacho","numorddes","SIV",15,"","codemp",$ls_codemp);
					$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
					$lo_objectc = uf_agregarlineablancacontable($lo_objectc,1);
					uf_limpiarvariables();
					//////////////////////////////
				}
				else
				{
					$io_sql->rollback();
					$io_msg->message("No se pudo procesar el despacho");
					////// agregado 23/09/08 /////
					$li_totrows=1;
					$li_totrowsc=1;
					$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
					$lo_objectc = uf_agregarlineablancacontable($lo_objectc,1);
					uf_limpiarvariables();
					//////////////////////////////
				}
			}
			else
			{
				$io_msg->message("El mes no esta abierto");
				$li_totrows=1;
				$li_totrowsc=1;
				$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
				$lo_objectc = uf_agregarlineablancacontable($lo_objectc,1);
				uf_limpiarvariables();
			}
			$ls_numorddes=$io_keygen->uf_generar_numero_nuevo("SIV","siv_despacho","numorddes","SIV",15,"","codemp",$ls_codemp);
			//$lo_object = uf_agregarlineablanca($lo_object,1);
			//$lo_objectc = uf_agregarlineablancacontable($lo_objectc,1);
		break;
		case "BUSCARDETALLESOLICITUD":
			$ls_numorddes = $io_fun_inventario->uf_obtenervalor("txtnumorddes","");
			$ls_readonly=  $io_fun_inventario->uf_obtenervalor("hidreadonly","");
			$ls_numsol=    $io_fun_inventario->uf_obtenervalor("txtnumsol","");
			$ls_coduniadm= $io_fun_inventario->uf_obtenervalor("txtcoduniadm","");
			$ls_denuniadm= $io_fun_inventario->uf_obtenervalor("txtdenuniadm","");
			$ls_obsdes=    $io_fun_inventario->uf_obtenervalor("txtobsdes","");
			$ld_fecdes=    $io_fun_inventario->uf_obtenervalor("txtfecdes","");
			$ls_codunides= $io_fun_inventario->uf_obtenervalor("txtcodunides","");
			$ls_denunides= $io_fun_inventario->uf_obtenervalor("txtdenunides","");
			$ls_estsol=    $io_fun_inventario->uf_obtenervalor("txtestsol","");
			$li_estint    = $io_fun_inventario->uf_obtenervalor("hidestint","");
			$ls_ctascgint = $io_fun_inventario->uf_obtenervalor("hidctascgint","");
			$ls_codfuefin 	= $io_fun_inventario->uf_obtenervalor("txtcodfuefin","");
			$ls_denfuefin 	= $io_fun_inventario->uf_obtenervalor("txtdenfuefin","");		

			$data="";
			$li_totrows=0;
			$li_totrowsc=1;
			$ls_pendiente="";
 		    $ls_checkedcomp="";
			$ls_checkedparc="";
			$ls_readonlyrad="";
			$ld_fecdes1=$io_func->uf_convertirdatetobd($ld_fecdes);
			$lo_objectc = uf_agregarlineablancacontable($lo_objectc,1);
			if($ls_estsol=="L")
			{
				$arrResultado=$io_siv->uf_siv_obtener_dt_pendiente($ls_codemp,$ls_numsol,$li_totrows,$lo_object,$ls_estartpri);
				$li_totrows=$arrResultado['ai_totrows'];
				$lo_object=$arrResultado['ao_object'];
				$lb_valido=$arrResultado['lb_valido'];
			}
			else
			{
				$arrResultado=$io_siv->uf_siv_obtener_dt_solicitud($ls_codemp,$ls_numsol,$li_totrows,$lo_object,$ls_estartpri);
				$li_totrows=$arrResultado['ai_totrows'];
				$lo_object=$arrResultado['ao_object'];
				$lb_valido=$arrResultado['lb_valido'];
				
			}
			if (!$lb_valido)
			{
				$lo_object = uf_agregarlineablanca($lo_object,1);
				$lo_objectc = uf_agregarlineablancacontable($lo_objectc,1);
				uf_limpiarvariables();
				$io_msg->message("Debe definir una cuenta contable de gasto para los articulos de la solicitud");
				$li_totrows=1;
			}
		break;
		case "BUSCARDETALLE":
			$ls_numsol=    $io_fun_inventario->uf_obtenervalor("txtnumsol","");
			$ls_numorddes= $io_fun_inventario->uf_obtenervalor("txtnumorddes","");
			$ls_coduniadm= $io_fun_inventario->uf_obtenervalor("txtcoduniadm","");
			$ls_denuniadm= $io_fun_inventario->uf_obtenervalor("txtdenuniadm","");
			$ld_fecdes=    $io_fun_inventario->uf_obtenervalor("txtfecdes","");
			$ls_codunides= $io_fun_inventario->uf_obtenervalor("txtcodunides","");
			$ls_denunides= $io_fun_inventario->uf_obtenervalor("txtdenunides","");
			$ls_obsdes=    $io_fun_inventario->uf_obtenervalor("txtobsdes","");
			$li_estint    = $io_fun_inventario->uf_obtenervalor("hidestint","");
			$ls_ctascgint = $io_fun_inventario->uf_obtenervalor("hidctascgint","");			
			$ls_vienecat  = $io_fun_inventario->uf_obtenervalor("hidvienecat","");
			$ls_codfuefin 	= $io_fun_inventario->uf_obtenervalor("txtcodfuefin","");
			$ls_denfuefin 	= $io_fun_inventario->uf_obtenervalor("txtdenfuefin","");
			
			$ls_checkedcomp="";
			$ls_checkedparc="";
			$arrResultado=$io_siv->uf_siv_obtener_dt_despacho($ls_codemp,$ls_numorddes,$li_totrows,$lo_object);
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$lb_valido=$arrResultado['lb_valido'];
			
		    $li_totrowsc=1;
		    $lo_objectc = uf_agregarlineablancacontable($lo_objectc,1); 
/*			if($lb_valido)
			{
				$arrResultado=$io_siv->uf_siv_obtener_dt_scg($ls_codemp,$ls_numorddes,$li_totrowsc,$lo_objectc);
				$li_totrows=$arrResultado['ai_totrows'];
				$lo_object=$arrResultado['ao_object'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido=="")
				{
				   $li_totrowsc=1;
				   $lo_objectc = uf_agregarlineablancacontable($lo_objectc,1); 
				}
			}*/
		break;
		case "CALCULARCONTABLE":
			uf_limpiarvariables();
			$ls_numorddes = $io_fun_inventario->uf_obtenervalor("txtnumorddes","");
			$ls_numsol=    $io_fun_inventario->uf_obtenervalor("txtnumsol","");
			$ls_coduniadm= $io_fun_inventario->uf_obtenervalor("txtcoduniadm",""); 
			$ls_denuniadm= $io_fun_inventario->uf_obtenervalor("txtdenuniadm",""); 
			$ld_fecdes=    $io_fun_inventario->uf_obtenervalor("txtfecdes",""); 
			$ls_obsdes=    $io_fun_inventario->uf_obtenervalor("txtobsdes","");
			$ls_estsol=    $io_fun_inventario->uf_obtenervalor("txtestsol","");
			$ls_codunides= $io_fun_inventario->uf_obtenervalor("txtcodunides","");
			$ls_denunides= $io_fun_inventario->uf_obtenervalor("txtdenunides","");
			$li_estint    = $io_fun_inventario->uf_obtenervalor("hidestint","");
			$ls_ctascgint = $io_fun_inventario->uf_obtenervalor("hidctascgint","");
			$ls_codfuefin 	= $io_fun_inventario->uf_obtenervalor("txtcodfuefin","");
			$ls_denfuefin 	= $io_fun_inventario->uf_obtenervalor("txtdenfuefin","");			

			$li_totrowsc=0;
			$ld_montotscg = 0;//Sumatoria Total de Los Bienes para el Detalle Contable en Inter Compa?ia.
			$ls_estrec=  $io_fun_inventario->uf_obtenervalor("rdtipodespacho","");
			if($ls_estrec==0)
			{
				$ls_checkedparc="checked";
				$ls_checkedcomp="";
			}
			else
			{
				$ls_checkedparc="";
				$ls_checkedcomp="checked";
			}
			for($li_i=1;$li_i<=$li_totrows;$li_i++)
			{
				$ls_codart=       $io_fun_inventario->uf_obtenervalor("txtcodart".$li_i,"");
				$ls_codartpri=    $io_fun_inventario->uf_obtenervalor("txtcodartpri".$li_i,"");
				$ls_denart=       $io_fun_inventario->uf_obtenervalor("txtdenart".$li_i,"");
				$ls_denunimed=    $io_fun_inventario->uf_obtenervalor("txtdenunimed".$li_i,"");
				$ls_codalm=       $io_fun_inventario->uf_obtenervalor("txtcodalm".$li_i,"");
				$li_canorisolsep= $io_fun_inventario->uf_obtenervalor("txtcansol".$li_i,"");
				$li_existencia=   $io_fun_inventario->uf_obtenervalor("hidexistencia".$li_i,"");
				$li_canart=       $io_fun_inventario->uf_obtenervalor("txtcanart".$li_i,"");
				$li_preuniart=    $io_fun_inventario->uf_obtenervalor("txtpreuniart".$li_i,"");
				$li_montotart=    $io_fun_inventario->uf_obtenervalor("txtmontotart".$li_i,"");
				$ls_unidad=       $io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
				$ls_hidunidad=    $io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
				$li_unidad=       $io_fun_inventario->uf_obtenervalor("hidunidad".$li_i,"");
				$ls_ctagas=       $io_fun_inventario->uf_obtenervalor("txtctagas".$li_i,"");
				$ls_ctasep=       $io_fun_inventario->uf_obtenervalor("txtctasep".$li_i,"");
				$li_canpenart=    $io_fun_inventario->uf_obtenervalor("txtpenart".$li_i,"");
				$li_hidpenart=    $io_fun_inventario->uf_obtenervalor("txthidpenart".$li_i,"");
				$ls_clasif=       $io_fun_inventario->uf_obtenervalor("hclasi".$li_i,"");
				$ls_codact=       $io_fun_inventario->uf_obtenervalor("hcodact".$li_i,"");
				$li_canorisolsep= str_replace(".","",$li_canorisolsep);
				$li_canorisolsep= str_replace(",",".",$li_canorisolsep);
				$li_canart=       str_replace(".","",$li_canart);
				$li_canart=       str_replace(",",".",$li_canart);
				$li_preuniart=    str_replace(".","",$li_preuniart);
				$li_preuniart=    str_replace(",",".",$li_preuniart);
				$li_montotart=    str_replace(".","",$li_montotart);
				$li_montotart=    str_replace(",",".",$li_montotart);
				$li_canpenart=    str_replace(".","",$li_canpenart);
				$li_canpenart=    str_replace(",",".",$li_canpenart);
				$arrResultado=$io_siv->uf_buscar_datos_presupuestarios($ls_codart,$ls_cuentaart,$ls_cuentaiva,$ls_formula);
				$ls_cuentaart=$arrResultado['as_cuentaart'];
				$ls_cuentaiva=$arrResultado['as_cuentaiva'];
				$ls_formula=$arrResultado['as_formula'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$arrResultado=$io_siv->uf_buscar_esructura_sep($ls_numsol,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla);
					$ls_codestpro1=$arrResultado['as_codestpro1'];
					$ls_codestpro2=$arrResultado['as_codestpro2'];
					$ls_codestpro3=$arrResultado['as_codestpro3'];
					$ls_codestpro4=$arrResultado['as_codestpro4'];
					$ls_codestpro5=$arrResultado['as_codestpro5'];
					$ls_estcla=$arrResultado['as_estcla'];
					$lb_valido=$arrResultado['lb_valido'];
				}
				if($lb_valido)
				{
					$arrResultado=$io_siv->uf_buscar_esructura_iva($ls_numsol,$ls_cuentaiva,$ls_codestproiva1,$ls_codestproiva2,$ls_codestproiva3,$ls_codestproiva4,$ls_codestproiva5,$ls_estclaiva);
					$ls_codestproiva1=$arrResultado['as_codestpro1'];
					$ls_codestproiva2=$arrResultado['as_codestpro2'];
					$ls_codestproiva3=$arrResultado['as_codestpro3'];
					$ls_codestproiva4=$arrResultado['as_codestpro4'];
					$ls_codestproiva5=$arrResultado['as_codestpro5'];
					$ls_estclaiva=$arrResultado['as_estcla'];
					$lb_valido=$arrResultado['lb_valido'];
				}
				if($lb_valido)
				{
					$arrResultado=$io_siv->uf_buscar_esructura_costos($as_codestprogen1,$as_codestprogen2,$as_codestprogen3,$as_codestprogen4,$as_codestprogen5,$as_estclagen);
					$as_codestprogen1=$arrResultado['as_codestpro1'];
					$as_codestprogen2=$arrResultado['as_codestpro2'];
					$as_codestprogen3=$arrResultado['as_codestpro3'];
					$as_codestprogen4=$arrResultado['as_codestpro4'];
					$as_codestprogen5=$arrResultado['as_codestpro5'];
					$as_estclagen=$arrResultado['as_estcla'];
					$lb_valido=$arrResultado['lb_valido'];
					if($lb_valido)
					{
						$arrResultado=$io_siv->uf_buscar_contable_costos($as_codestprogen1,$as_codestprogen2,$as_codestprogen3,$as_codestprogen4,$as_codestprogen5,$as_estclagen,$ls_cuentaart,$ls_cuentaartcos);
						$ls_cuentaartcos=$arrResultado['ls_scgcuenta'];
						$lb_valido=$arrResultado['lb_valido'];
					}
					if($lb_valido)
					{
						$arrResultado=$io_siv->uf_buscar_contable_costos($as_codestprogen1,$as_codestprogen2,$as_codestprogen3,$as_codestprogen4,$as_codestprogen5,$as_estclagen,$ls_cuentaiva,$ls_cuentaivacos);
						$ls_cuentaivacos=$arrResultado['ls_scgcuenta'];
						$lb_valido=$arrResultado['lb_valido'];
					}
					if($lb_valido)
					{
						$arrResultado=$io_siv->uf_buscar_contable_costos($ls_codestproiva1,$ls_codestproiva2,$ls_codestproiva3,$ls_codestproiva4,$ls_codestproiva5,$ls_estclaiva,$ls_cuentaiva,$ls_cuentaivagas);
						$ls_cuentaivagas=$arrResultado['ls_scgcuenta'];
						$lb_valido=$arrResultado['lb_valido'];
					}
				}
				if(($lb_valido)&&($li_montotart>0))
				{
					$io_dspre->insertRow("codestpro1",$ls_codestpro1);
					$io_dspre->insertRow("codestpro2",$ls_codestpro2);
					$io_dspre->insertRow("codestpro3",$ls_codestpro3);
					$io_dspre->insertRow("codestpro4",$ls_codestpro4);
					$io_dspre->insertRow("codestpro5",$ls_codestpro5);
					$io_dspre->insertRow("estcla",$ls_estcla);
					$io_dspre->insertRow("cuenta",$ls_cuentaart);
					$io_dspre->insertRow("monto",$li_montotart);
					
					$arrResultado=$io_evaluate->uf_evaluar($ls_formula,$li_montotart,$lb_valido);
				    $li_montoiva=$arrResultado['result'];
				    $lb_valido = $arrResultado['lb_valido'];
					if($li_montoiva>0)
					{
						$io_dspre->insertRow("codestpro1",$ls_codestproiva1);
						$io_dspre->insertRow("codestpro2",$ls_codestproiva2);
						$io_dspre->insertRow("codestpro3",$ls_codestproiva3);
						$io_dspre->insertRow("codestpro4",$ls_codestproiva4);
						$io_dspre->insertRow("codestpro5",$ls_codestproiva5);
						$io_dspre->insertRow("estcla",$ls_estclaiva);
						$io_dspre->insertRow("cuenta",$ls_cuentaiva);
						$io_dspre->insertRow("monto",$li_montoiva);
					}
					
				}
				if($ls_ctagas=="")
				{
					$li_totrowsc=1;				
					$li_totrows=1;				
					$lo_object = uf_agregarlineablanca($lo_object,1);
					$lo_objectc = uf_agregarlineablancacontable($lo_objectc,1);
					uf_limpiarvariables();
					$lo_objectc = uf_agregarlineablancacontable($lo_objectc,1);
					$lb_ok=false;
					$io_msg->message("Verifique que todos los articulos de la solicitud tengan cuenta contable de gasto asociada");
					break;
				}
				if($ls_unidad=="")
				{
					$ls_unidad= $io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
					$ls_hidunidad= $io_fun_inventario->uf_obtenervalor("hidtxtuni".$li_i,"");
				}
				if($ls_unidad=="Mayor")
				{$ls_unidad="M";}
				else
				{$ls_unidad="D";}
				switch ($ls_unidad) 
				{
					case "M":
						$ls_unidadaux="Mayor";
						break;
					case "D":
						$ls_unidadaux="Detal";
						break;
				}
				if($ls_estartpri==1)
				{
					$ls_href="";
				}
				else
				{
					$ls_href="<a href='javascript: ue_catalmacen(".$li_i.");'><img src='../shared/imagebank/tools20/buscar.gif' alt='Codigo de Articulo' width='18' height='18' border='0'></a>";
				}
				
				$lo_object[$li_i][1]="<input name=txtdenart".$li_i."     type=text   id=txtdenart".$li_i."    class=sin-borde size=25 maxlength=50 value='".$ls_denart."' readonly>".
									 "<input name=txtcodart".$li_i."     type=hidden id=txtcodart".$li_i."    class=sin-borde size=15 maxlength=25 value='".$ls_codart."' readonly>".
						 	         "<input name=txtcodartpri".$li_i."  type=hidden id=txtcodartpri".$li_i."    class=sin-borde size=15 maxlength=25 value='".$ls_codartpri."' readonly>".
									 "<input name=txtctagas".$li_i."     type=hidden id=txtctagas".$li_i."    class=sin-borde size=20 maxlength=50 value='".$ls_ctagas."' readonly>".
									 "<input name=txtctasep".$li_i."     type=hidden id=txtctasep".$li_i."    class=sin-borde size=20 maxlength=20 value='".$ls_ctasep."' readonly>";
				$lo_object[$li_i][2]="<input name=txtdenunimed".$li_i."     type=text   id=txtdenunimed".$li_i."    class=sin-borde size=13 maxlength=10 value='". $ls_denunimed."' readonly>";
				$lo_object[$li_i][3]="<input name=txtcodalm".$li_i."     type=text   id=txtcodalm".$li_i."    class=sin-borde size=13 maxlength=10 value='". $ls_codalm."' readonly>".
									 $ls_href;
				$lo_object[$li_i][4]="<input name=txtunidad".$li_i."     type=text   id=txtunidad".$li_i."    class=sin-borde size=12 maxlength=12 value='". $ls_unidadaux."' style='text-align:center' readonly></div>".
									 "<input name=hidtxtuni".$li_i."     type=hidden id=hidtxtuni".$li_i."                                         value='". $ls_hidunidad ."'>".
									 "<input name=hidunidad".$li_i."     type=hidden id=hidunidad".$li_i."                                         value='". $li_unidad ."'>";
				$lo_object[$li_i][5]="<input name=txtcansol".$li_i."     type=text   id=txtcansol".$li_i."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canorisolsep,2,",",".")."' style='text-align:right' readonly>".
									 "<input name=hidexistencia".$li_i." type=hidden id=hidexistencia".$li_i."                                     value='". $li_existencia."'>";
				$lo_object[$li_i][6]="<input name=txtpenart".$li_i."     type=text   id=txtpenart".$li_i."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canpenart,2,",",".")."'  style='text-align:right' readonly>".
									 "<input name=txthidpenart".$li_i."  type=hidden id=txthidpenart".$li_i." class=sin-borde size=12 value='".$li_hidpenart."'>";
				$lo_object[$li_i][7]="<input name=txtcanart".$li_i."     type=text   id=txtcanart".$li_i."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canart,2,",",".")."'    style='text-align:right' onKeyPress=return(ue_formatonumero(this,'.',',',event)); onBlur='javascript: ue_montosfactura(".$li_i.");'>";
				$lo_object[$li_i][8]="<input name=txtpreuniart".$li_i."  type=text   id=txtpreuniart".$li_i." class=sin-borde size=14 maxlength=15 value='".number_format ($li_preuniart,2,",",".")."' style='text-align:right' readonly>".
									 "<input name=hidnumdocori".$li_i."  type=hidden id=hidnumdocori".$li_i.">";
				$lo_object[$li_i][9]="<input name=txtmontotart".$li_i."  type=text   id=txtmontotart".$li_i." class=sin-borde size=14 maxlength=15 value='".number_format ($li_montotart,2,",",".")."' style='text-align:right' readonly>";
				$lo_object[$li_i][10]="<a href=javascript:uf_dt_activo(".$li_i.");><img src=../shared/imagebank/mas.gif alt=Agregar Seriales width=15 height=15 border=0>
				<input name=hclasi".$li_i." type=hidden id=hclasi".$li_i." class=sin-borde size=15 maxlength=25 value='".$ls_clasif."' readonly></a></a>".			
				" <input type=hidden name=hcodact".$li_i." id=hcodact".$li_i." class=sin-borde size=15 maxlength=25 value='".$ls_codact."' readonly>";
                
				$arrResultado=$io_siv->uf_siv_load_codigoactivo($ls_codart,$ls_codact);
				$ls_codact=$arrResultado['as_codact'];
				$lb_valido=$arrResultado['lb_valido'];
				if (($ls_codact=='')||($ls_codact=='---------------'))
				{ 
					
					if ($li_canart>0)
					{
						$li_totrowsc++;
						$ls_debhab="D";
						$lo_objectc[$li_totrowsc][1]="<input  name=txtdenartc".$li_totrowsc."  type=text   id=txtdenartc".$li_totrowsc."  class=sin-borde size=50 maxlength=50 value='".$ls_denart."' readonly  style=text-align:left>".
													 "<input  name=txtcodartc".$li_totrowsc."  type=hidden id=txtcodartc".$li_totrowsc."  class=sin-borde size=30 maxlength=50 value='".$ls_codart."' readonly  style=text-align:center>";
						$lo_objectc[$li_totrowsc][2]="<input  name=txtsccuenta".$li_totrowsc." type=text   id=txtsccuenta".$li_totrowsc." class=sin-borde size=30              value='".$ls_ctagas."' readonly  style=text-align:center>";
						$lo_objectc[$li_totrowsc][3]="<input  name=txtdebhab".$li_totrowsc."   type=text   id=txtdebhab".$li_totrowsc."   class=sin-borde size=15              value='".$ls_debhab."' readonly style='text-align:center'>";
						$lo_objectc[$li_totrowsc][4]="<input  name=txtmonto".$li_totrowsc."    type=text   id=txtcansolc".$li_totrowsc."  class=sin-borde size=30              value='".number_format ($li_montotart,2,",",".")."' style='text-align:right' readonly>";
						if($li_montoiva>0)
						{
							$li_totrowsc++;
							$lo_objectc[$li_totrowsc][1]="<input  name=txtdenartc".$li_totrowsc."  type=text   id=txtdenartc".$li_totrowsc."  class=sin-borde size=50 maxlength=50 value='".$ls_denart."' readonly  style=text-align:left>".
														 "<input  name=txtcodartc".$li_totrowsc."  type=hidden id=txtcodartc".$li_totrowsc."  class=sin-borde size=30 maxlength=50 value='".$ls_codart."' readonly  style=text-align:center>";
							$lo_objectc[$li_totrowsc][2]="<input  name=txtsccuenta".$li_totrowsc." type=text   id=txtsccuenta".$li_totrowsc." class=sin-borde size=30              value='".$ls_cuentaivagas."' readonly  style=text-align:center>";
							$lo_objectc[$li_totrowsc][3]="<input  name=txtdebhab".$li_totrowsc."   type=text   id=txtdebhab".$li_totrowsc."   class=sin-borde size=15              value='".$ls_debhab."' 				readonly  style='text-align:center'>";
							$lo_objectc[$li_totrowsc][4]="<input  name=txtmonto".$li_totrowsc."    type=text   id=txtcansolc".$li_totrowsc."  class=sin-borde size=30              value='".number_format ($li_montoiva,2,",",".")."' style='text-align:right' readonly>";
						}
						if ($li_estint==0)
						{ 
							$li_totrowsc++;
							$lo_objectc[$li_totrowsc][1]="<input  name=txtdenartc".$li_totrowsc."  type=text   id=txtdenartc".$li_totrowsc."  class=sin-borde size=50 maxlength=50 value='".$ls_denart."' readonly  style=text-align:left>".
														 "<input  name=txtcodartc".$li_totrowsc."  type=hidden id=txtcodartc".$li_totrowsc."  class=sin-borde size=30 maxlength=50 value='".$ls_codart."' readonly  style=text-align:center>";
							$lo_objectc[$li_totrowsc][2]="<input  name=txtsccuenta".$li_totrowsc." type=text   id=txtsccuenta".$li_totrowsc." class=sin-borde size=30              value='".$ls_cuentaartcos."' readonly  style=text-align:center>";
							$lo_objectc[$li_totrowsc][3]="<input  name=txtdebhab".$li_totrowsc."   type=text   id=txtdebhab".$li_totrowsc."   class=sin-borde size=15              value='H' 				readonly  style='text-align:center'>";
							$lo_objectc[$li_totrowsc][4]="<input  name=txtmonto".$li_totrowsc."    type=text   id=txtcansolc".$li_totrowsc."  class=sin-borde size=30              value='".number_format ($li_montotart,2,",",".")."' style='text-align:right' readonly>";						
							if($li_montoiva>0)
							{
								$li_totrowsc++;
								$lo_objectc[$li_totrowsc][1]="<input  name=txtdenartc".$li_totrowsc."  type=text   id=txtdenartc".$li_totrowsc."  class=sin-borde size=50 maxlength=50 value='".$ls_denart."' readonly  style=text-align:left>".
															 "<input  name=txtcodartc".$li_totrowsc."  type=hidden id=txtcodartc".$li_totrowsc."  class=sin-borde size=30 maxlength=50 value='".$ls_codart."' readonly  style=text-align:center>";
								$lo_objectc[$li_totrowsc][2]="<input  name=txtsccuenta".$li_totrowsc." type=text   id=txtsccuenta".$li_totrowsc." class=sin-borde size=30              value='".$ls_cuentaivacos."' readonly  style=text-align:center>";
								$lo_objectc[$li_totrowsc][3]="<input  name=txtdebhab".$li_totrowsc."   type=text   id=txtdebhab".$li_totrowsc."   class=sin-borde size=15              value='H' 				readonly  style='text-align:center'>";
								$lo_objectc[$li_totrowsc][4]="<input  name=txtmonto".$li_totrowsc."    type=text   id=txtcansolc".$li_totrowsc."  class=sin-borde size=30              value='".number_format ($li_montoiva,2,",",".")."' style='text-align:right' readonly>";
							}
						}	
						else
						{
							if ($li_totrowsc==0)
							{
								$li_totrowsc++;
							}
							$ld_montotscg += $li_montotart;
						}
					}// fin del if
				} // fion del if ($ls_codact==''||$ls_codact=='---------------')
				else
				{
				   $lo_objectc = uf_agregarlineablancacontable($lo_objectc,1);
				   $li_totrowsc=1;
				   $io_msg->message("EL art?culo es un activo y no genera detalle contable !!!!");
				}
			   $ls_ok=true;
			}//  del for	
			$io_dspre->group_by(array('0'=>'codestpro1','1'=>'codestpro2','2'=>'codestpro3','3'=>'codestpro41','4'=>'codestpro5','5'=>'estcla','6'=>'cuenta'),array('0'=>'monto'),'cuenta');
			$li_totrowp=$io_dspre->getRowCount("cuenta");
			for($li_j=1;$li_j<=$li_totrowp;$li_j++ && $lb_valido)
			{
				$ls_codestpro1=    $io_dspre->data["codestpro1"][$li_j];
				$ls_codestpro2=    $io_dspre->data["codestpro2"][$li_j];
				$ls_codestpro3=    $io_dspre->data["codestpro3"][$li_j];
				$ls_codestpro4=    $io_dspre->data["codestpro4"][$li_j];
				$ls_codestpro5=    $io_dspre->data["codestpro5"][$li_j];
				$ls_estcla=    $io_dspre->data["estcla"][$li_j];
				$ls_cuentapre=    trim($io_dspre->data["cuenta"][$li_j]);
				$ls_montopre=    $io_dspre->data["monto"][$li_j];
				$ls_codpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
				$ls_programatica = "";
				$ls_programatica = $io_fun_inventario->uf_formatoprogramatica($ls_codpro,$ls_programatica);
				if($ls_estcla=="A")
				{
					$ls_estclaaux="ACCION";
				}
				else
				{
					$ls_estclaaux="PROYECTO";
				}
				$lo_objectp[$li_j][1]="<input  name=txtprogramatica".$li_j."  type=text   id=txtprogramatica".$li_j."  class=sin-borde size=50 maxlength=50 value='".$ls_programatica."' readonly  style=text-align:center>".
									  "<input  name=txtcodestpro1".$li_j."  type=hidden id=txtcodestpro1".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_codestpro1."' readonly  style=text-align:center>".
									  "<input  name=txtcodestpro2".$li_j."  type=hidden id=txtcodestpro2".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_codestpro2."' readonly  style=text-align:center>".
									  "<input  name=txtcodestpro3".$li_j."  type=hidden id=txtcodestpro3".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_codestpro3."' readonly  style=text-align:center>".
									  "<input  name=txtcodestpro4".$li_j."  type=hidden id=txtcodestpro4".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_codestpro4."' readonly  style=text-align:center>".
									  "<input  name=txtcodestpro5".$li_j."  type=hidden id=txtcodestpro5".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_codestpro5."' readonly  style=text-align:center>".
									  "<input  name=txtestcla".$li_j."  type=hidden id=txtestcla".$li_j."  class=sin-borde size=30 maxlength=50 value='".$ls_estcla."' readonly  style=text-align:center>";
				$lo_objectp[$li_j][2]="<input  name=txtestclaaux".$li_j." type=text   id=txtestclaaux".$li_j." class=sin-borde size=30              value='".$ls_estclaaux."' readonly  style=text-align:center>";
				$lo_objectp[$li_j][3]="<input  name=txtspgcuenta".$li_j."   type=text   id=txtspgcuenta".$li_j."   class=sin-borde size=15              value='".$ls_cuentapre."' readonly style='text-align:center'>";
				$lo_objectp[$li_j][4]="<input  name=txtmontop".$li_j."    type=text   id=txtmontop".$li_j."  class=sin-borde size=30              value='".number_format ($ls_montopre,2,",",".")."' style='text-align:right' readonly>";
			}
		    if (($li_estint==1)&&(($ls_codact=='')||($ls_codact=='---------------')))
			   {
			     $lo_objectc[1][1]="<input  name=txtdenartc1  type=text   id=txtdenartc1  class=sin-borde size=50 maxlength=50 value='INTER COMPA?IA'        readonly  style=text-align:left>".
								   "<input  name=txtcodartc1  type=hidden id=txtcodartc1  class=sin-borde size=30 maxlength=50 value='--------------------' readonly  style=text-align:center>";
			     $lo_objectc[1][2]="<input  name=txtsccuenta1 type=text   id=txtsccuenta1 class=sin-borde size=30              value='".$ls_ctascgint."'    readonly  style=text-align:center>";
			     $lo_objectc[1][3]="<input  name=txtdebhab1   type=text   id=txtdebhab1   class=sin-borde size=15              value='D' readonly style='text-align:center'>";
			     $lo_objectc[1][4]="<input  name=txtmonto1    type=text   id=txtcansolc1  class=sin-borde size=30              value='".number_format($ld_montotscg,2,",",".")."' style='text-align:right' readonly>";	
			   }
		break;
		case"AGREGARDETALLES";
			uf_limpiarvariables();
			$ls_numorddes = $io_fun_inventario->uf_obtenervalor("txtnumorddes","");
			$ls_numsol=    $io_fun_inventario->uf_obtenervalor("txtnumsol","");
			$ls_coduniadm= $io_fun_inventario->uf_obtenervalor("txtcoduniadm",""); 
			$ls_denuniadm= $io_fun_inventario->uf_obtenervalor("txtdenuniadm",""); 
			$ld_fecdes=    $io_fun_inventario->uf_obtenervalor("txtfecdes",""); 
			$ls_obsdes=    $io_fun_inventario->uf_obtenervalor("txtobsdes","");
			$ls_estsol=    $io_fun_inventario->uf_obtenervalor("txtestsol","");
			$ls_codunides= $io_fun_inventario->uf_obtenervalor("txtcodunides","");
			$ls_denunides= $io_fun_inventario->uf_obtenervalor("txtdenunides","");
			$li_estint    = $io_fun_inventario->uf_obtenervalor("hidestint","");
			$ls_ctascgint = $io_fun_inventario->uf_obtenervalor("hidctascgint","");
			$ls_codfuefin 	= $io_fun_inventario->uf_obtenervalor("txtcodfuefin","");
			$ls_denfuefin 	= $io_fun_inventario->uf_obtenervalor("txtdenfuefin","");			

			$li_totrowsc=0;
			$ld_montotscg = 0;//Sumatoria Total de Los Bienes para el Detalle Contable en Inter Compa?ia.
			$ls_estrec=  $io_fun_inventario->uf_obtenervalor("rdtipodespacho","");
			if($ls_estrec==0)
			{
				$ls_checkedparc="checked";
				$ls_checkedcomp="";
			}
			else
			{
				$ls_checkedparc="";
				$ls_checkedcomp="checked";
			}
			for($li_i=1;$li_i<=$li_totrows;$li_i++)
			{
				$ls_codart=       $io_fun_inventario->uf_obtenervalor("txtcodart".$li_i,"");
				$ls_codartpri=    $io_fun_inventario->uf_obtenervalor("txtcodartpri".$li_i,"");
				$ls_denart=       $io_fun_inventario->uf_obtenervalor("txtdenart".$li_i,"");
				$ls_denunimed=       $io_fun_inventario->uf_obtenervalor("txtdenunimed".$li_i,"");
				$ls_codalm=       $io_fun_inventario->uf_obtenervalor("txtcodalm".$li_i,"");
				$li_canorisolsep= $io_fun_inventario->uf_obtenervalor("txtcansol".$li_i,"");
				$li_existencia=   $io_fun_inventario->uf_obtenervalor("hidexistencia".$li_i,"");
				$li_canart=       $io_fun_inventario->uf_obtenervalor("txtcanart".$li_i,"");
				$li_preuniart=    $io_fun_inventario->uf_obtenervalor("txtpreuniart".$li_i,"");
				$li_montotart=    $io_fun_inventario->uf_obtenervalor("txtmontotart".$li_i,"");
				$ls_unidad=       $io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
				$ls_hidunidad=    $io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
				$li_unidad=       $io_fun_inventario->uf_obtenervalor("hidunidad".$li_i,"");
				$ls_ctagas=       $io_fun_inventario->uf_obtenervalor("txtctagas".$li_i,"");
				$ls_ctasep=       $io_fun_inventario->uf_obtenervalor("txtctasep".$li_i,"");
				$li_canpenart=    $io_fun_inventario->uf_obtenervalor("txtpenart".$li_i,"");
				$li_hidpenart=    $io_fun_inventario->uf_obtenervalor("txthidpenart".$li_i,"");
				$ls_clasif=       $io_fun_inventario->uf_obtenervalor("hclasi".$li_i,"");
				$ls_codact=       $io_fun_inventario->uf_obtenervalor("hcodact".$li_i,"");
				$li_canorisolsep=    $io_fun_inventario->uf_formatocalculo($li_canorisolsep);
				$li_canart=    $io_fun_inventario->uf_formatocalculo($li_canart);
				$li_preuniart=    $io_fun_inventario->uf_formatocalculo($li_preuniart);
				$li_montotart=    $io_fun_inventario->uf_formatocalculo($li_montotart);
				$li_canpenart=    $io_fun_inventario->uf_formatocalculo($li_canpenart);
				if($ls_unidad=="")
				{
					$ls_unidad= $io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
					$ls_hidunidad= $io_fun_inventario->uf_obtenervalor("hidtxtuni".$li_i,"");
				}
				if($ls_unidad=="Mayor")
				{$ls_unidad="M";}
				else
				{$ls_unidad="D";}
				switch ($ls_unidad) 
				{
					case "M":
						$ls_unidadaux="Mayor";
						break;
					case "D":
						$ls_unidadaux="Detal";
						break;
				}
				if($ls_estartpri==1)
				{
					$ls_href="";
				}
				else
				{
					$ls_href="<a href='javascript: ue_catalmacen(".$li_i.");'><img src='../shared/imagebank/tools20/buscar.gif' alt='Codigo de Articulo' width='18' height='18' border='0'></a>";
				}
				$lo_object[$li_i][1]="<input name=txtdenart".$li_i."     type=text   id=txtdenart".$li_i."    class=sin-borde size=25 maxlength=50 value='".$ls_denart."' readonly>".
									 "<input name=txtcodart".$li_i."     type=hidden id=txtcodart".$li_i."    class=sin-borde size=15 maxlength=25 value='".$ls_codart."' readonly>".
									 "<input name=txtcodartpri".$li_i."  type=hidden id=txtcodartpri".$li_i."    class=sin-borde size=15 maxlength=25 value='".$ls_codartpri."' readonly>".
									 "<input name=txtctagas".$li_i."     type=hidden id=txtctagas".$li_i."    class=sin-borde size=20 maxlength=50 value='".$ls_ctagas."' readonly>".
									 "<input name=txtctasep".$li_i."     type=hidden id=txtctasep".$li_i."    class=sin-borde size=20 maxlength=20 value='".$ls_ctasep."' readonly>";
				$lo_object[$li_i][2]="<input name=txtdenunimed".$li_i."     type=text   id=txtdenunimed".$li_i."    class=sin-borde size=13 maxlength=10 value='". $ls_denunimed."' readonly>";
				$lo_object[$li_i][3]="<input name=txtcodalm".$li_i."     type=text   id=txtcodalm".$li_i."    class=sin-borde size=13 maxlength=10 value='". $ls_codalm."' readonly>".
									 $ls_href;
				$lo_object[$li_i][4]="<input name=txtunidad".$li_i."     type=text   id=txtunidad".$li_i."    class=sin-borde size=12 maxlength=12 value='". $ls_unidadaux."' style='text-align:center' readonly></div>".
									 "<input name=hidtxtuni".$li_i."     type=hidden id=hidtxtuni".$li_i."                                         value='". $ls_hidunidad ."'>".
									 "<input name=hidunidad".$li_i."     type=hidden id=hidunidad".$li_i."                                         value='". $li_unidad ."'>";
				$lo_object[$li_i][5]="<input name=txtcansol".$li_i."     type=text   id=txtcansol".$li_i."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canorisolsep,2,",",".")."' style='text-align:right' readonly>".
									 "<input name=hidexistencia".$li_i." type=hidden id=hidexistencia".$li_i."                                     value='". $li_existencia."'>";
				$lo_object[$li_i][6]="<input name=txtpenart".$li_i."     type=text   id=txtpenart".$li_i."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canpenart,2,",",".")."'  style='text-align:right' readonly>".
									 "<input name=txthidpenart".$li_i."  type=hidden id=txthidpenart".$li_i." class=sin-borde size=12 value='".$li_hidpenart."'>";
				$lo_object[$li_i][7]="<input name=txtcanart".$li_i."     type=text   id=txtcanart".$li_i."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canart,2,",",".")."'    style='text-align:right' readonly>";
				$lo_object[$li_i][8]="<input name=txtpreuniart".$li_i."  type=text   id=txtpreuniart".$li_i." class=sin-borde size=14 maxlength=15 value='".number_format ($li_preuniart,2,",",".")."' style='text-align:right' readonly>".
									 "<input name=hidnumdocori".$li_i."  type=hidden id=hidnumdocori".$li_i.">";
				$lo_object[$li_i][9]="<input name=txtmontotart".$li_i."  type=text   id=txtmontotart".$li_i." class=sin-borde size=14 maxlength=15 value='".number_format ($li_montotart,2,",",".")."' style='text-align:right' readonly>";
				$lo_object[$li_i][10]="<a href=javascript:uf_dt_activo(".$li_i.");><img src=../shared/imagebank/mas.gif alt=Agregar Seriales width=15 height=15 border=0>".
									 "<input name=hclasi".$li_i." type=hidden id=hclasi".$li_i." class=sin-borde size=15 maxlength=25 value='".$ls_clasif."' readonly></a></a>".
									 " <input type=hidden name=hcodact".$li_i." id=hcodact".$li_i." class=sin-borde size=15 maxlength=25 value='".$ls_codact."' readonly>";
                
			}//  del for	
			if(array_key_exists("despacho",$_SESSION))
			{
				$li_j=$li_totrows;
				$li_total=$_SESSION["despacho"]["contador"];
				for($li_i=1;$li_i<=$li_total;$li_i++)
				{
					$li_j++;
					$ls_codart= $_SESSION["despacho"]["codart".$li_i];
					$ls_denart= $_SESSION["despacho"]["denart".$li_i];
					$ls_denunimed= $_SESSION["despacho"]["denunimed".$li_i];
					$ls_codalm= $_SESSION["despacho"]["codalm".$li_i];
					$li_canorisolsep=$_SESSION["despacho"]["cansol"];
					$ls_codartpri=$_SESSION["despacho"]["codartpri"];
					$li_penart=$_SESSION["despacho"]["penart"];
					$li_totart=$_SESSION["despacho"]["totart"];
					$li_existencia= $_SESSION["despacho"]["exiart".$li_i];
					$li_canart= $_SESSION["despacho"]["canart".$li_i];
					$li_preuniart= $_SESSION["despacho"]["preuniart".$li_i];
					$ls_unidad=$_SESSION["despacho"]["unidad"];
					$ls_hidunidad=$_SESSION["despacho"]["unidad"];
					$li_unidad= $_SESSION["despacho"]["unidad".$li_i];
					$ls_ctagas= $_SESSION["despacho"]["sc_cuenta".$li_i];
					$ls_ctasep= $_SESSION["despacho"]["ctasep"];
					$li_canpenart= $_SESSION["despacho"]["penart".$li_i];
					$ls_clasif="";
					$ls_codact="";
					if($ls_unidad=="Mayor")
					{$ls_unidad="M";}
					else
					{$ls_unidad="D";}
					$li_canart=$io_fun_inventario->uf_formatocalculo($li_canart);
					$li_preuniart=$io_fun_inventario->uf_formatocalculo($li_preuniart);
					$li_existencia=$io_fun_inventario->uf_formatocalculo($li_existencia);
					//$li_canpenart=$li_penart-$li_canart;
					$li_hidpenart=$li_canpenart;
					switch ($ls_unidad) 
					{
						case "M":
							$ls_unidadaux="Mayor";
							$li_montotart=($li_canart*$li_preuniart*$li_unidad);
							break;
						case "D":
							$ls_unidadaux="Detal";
							$li_montotart=($li_canart*$li_preuniart);
							break;
					}
					$lo_object[$li_j][1]="<input name=txtdenart".$li_j."     type=text   id=txtdenart".$li_j."    class=sin-borde size=25 maxlength=50 value='".$ls_denart."' readonly>".
										 "<input name=txtcodart".$li_j."     type=hidden id=txtcodart".$li_j."    class=sin-borde size=15 maxlength=25 value='".$ls_codart."' readonly>".
									 	 "<input name=txtcodartpri".$li_j."  type=hidden id=txtcodartpri".$li_j."    class=sin-borde size=15 maxlength=25 value='".$ls_codartpri."' readonly>".
										 "<input name=txtctagas".$li_j."     type=hidden id=txtctagas".$li_j."    class=sin-borde size=20 maxlength=50 value='".$ls_ctagas."' readonly>".
										 "<input name=txtctasep".$li_j."     type=hidden id=txtctasep".$li_j."    class=sin-borde size=20 maxlength=20 value='".$ls_ctasep."' readonly>";
					$lo_object[$li_j][2]="<input name=txtdenunimed".$li_j."  type=text   id=txtdenunimed".$li_j."    class=sin-borde size=13 maxlength=10 value='". $ls_denunimed."' readonly>";
					$lo_object[$li_j][3]="<input name=txtcodalm".$li_j."     type=text   id=txtcodalm".$li_j."    class=sin-borde size=13 maxlength=10 value='". $ls_codalm."' readonly>";
					$lo_object[$li_j][4]="<input name=txtunidad".$li_j."     type=text   id=txtunidad".$li_j."    class=sin-borde size=12 maxlength=12 value='". $ls_unidadaux."' style='text-align:center' readonly></div>".
										 "<input name=hidtxtuni".$li_j."     type=hidden id=hidtxtuni".$li_j."                                         value='". $ls_hidunidad ."'>".
										 "<input name=hidunidad".$li_j."     type=hidden id=hidunidad".$li_j."                                         value='". $li_unidad ."'>";
					$lo_object[$li_j][5]="<input name=txtcansol".$li_j."     type=text   id=txtcansol".$li_j."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canorisolsep,2,",",".")."' style='text-align:right' readonly>".
										 "<input name=hidexistencia".$li_j." type=hidden id=hidexistencia".$li_j."                                     value='". $li_existencia."'>";
					$lo_object[$li_j][6]="<input name=txtpenart".$li_j."     type=text   id=txtpenart".$li_j."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canpenart,2,",",".")."'  style='text-align:right' readonly>".
										 "<input name=txthidpenart".$li_j."  type=hidden id=txthidpenart".$li_j." class=sin-borde size=12 value='".$li_hidpenart."'>";
					$lo_object[$li_j][7]="<input name=txtcanart".$li_j."     type=text   id=txtcanart".$li_j."    class=sin-borde size=12 maxlength=12 value='".number_format ($li_canart,2,",",".")."'    style='text-align:right' readonly>";
					$lo_object[$li_j][8]="<input name=txtpreuniart".$li_j."  type=text   id=txtpreuniart".$li_j." class=sin-borde size=14 maxlength=15 value='".number_format ($li_preuniart,2,",",".")."' style='text-align:right' readonly>".
										 "<input name=hidnumdocori".$li_j."  type=hidden id=hidnumdocori".$li_j.">";
					$lo_object[$li_j][9]="<input name=txtmontotart".$li_j."  type=text   id=txtmontotart".$li_j." class=sin-borde size=14 maxlength=15 value='".number_format ($li_montotart,2,",",".")."' style='text-align:right' readonly>";
					$lo_object[$li_j][10]="<a href=javascript:uf_dt_activo(".$li_j.");><img src=../shared/imagebank/mas.gif alt=Agregar Seriales width=15 height=15 border=0>".
										 "<input name=hclasi".$li_j." type=hidden id=hclasi".$li_j." class=sin-borde size=15 maxlength=25 value='".$ls_clasif."' readonly></a></a>".
										 " <input type=hidden name=hcodact".$li_j." id=hcodact".$li_j." class=sin-borde size=15 maxlength=25 value='".$ls_codact."' readonly>";
                


				}
				$li_totrows=$li_j;
			}
		break;
		
	}
?>

<p>&nbsp;</p>
<form name="form1" method="post" action="">
  <table width="676" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="744"><?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_inventario->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_inventario);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?></td>
    </tr>
    <tr>
      <td><table width="654" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td height="22" colspan="4" class="titulo-ventana">Despacho de Suministros </td>
          </tr>
          <tr class="formato-blanco">
            <td width="132" height="19">&nbsp;</td>
            <td width="139"><input name="hidestatus" type="hidden" id="hidestatus" value="<?php print $ls_status?>">
                <input name="hidreadonly" type="hidden" id="hidreadonly">
                <input name="contable" type="hidden" id="contable" value="<?php print $li_value; ?>">
                <input name="hidok" type="hidden" id="hidok" value="<?php print $ls_ok ?>">
				<input name="hidvienecat" type="hidden" id="hidvienecat" value="<?php print $ls_vienecat ?>"></td>
            <td width="270" style="text-align:right">Fecha</td>
            <td width="111"><input name="txtfecdes" type="text" id="txtfecdes" onKeyPress="ue_separadores(this,'/',patron,true);" value="<?php print $ld_fecdes ?>" size="17" maxlength="10" datepicker="true" style="text-align:center "></td>
          </tr>
          <tr class="formato-blanco">
            <td height="20" style="text-align:right">Nro. Orden de Despacho </td>
            <td height="22"><input name="txtnumorddes" type="text" id="txtnumorddes" value="<?php print $ls_numorddes ?>" size="22" maxlength="15" readonly style="text-align:center"></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="formato-blanco">
            <td height="20" style="text-align:right">N&uacute;mero de la Solicitud</td>
            <td height="22"><input name="txtnumsol" type="text" id="txtnumsol" value="<?php print $ls_numsol; ?>" size="22" maxlength="15" style="text-align:center " readonly></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="formato-blanco">
            <td height="20" style="text-align:right">Unidad Solicitante</td>
            <td height="22" colspan="2"><input name="txtcoduniadm" type="text" id="txtcoduniadm" value="<?php print $ls_coduniadm; ?>" size="15" maxlength="10" style="text-align:center " readonly>
              <input name="txtdenuniadm" type="text" class="sin-borde" id="txtdenuniadm" value="<?php print $ls_denuniadm; ?>" size="50" readonly>
              <input name="hidestint" type="hidden" id="hidestint" value="<?php echo $li_estint ?>">
              <input name="hidctascgint" type="hidden" id="hidctascgint" value="<?php echo $ls_ctascgint ?>"></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="20" style="text-align:right">Unidad a Despachar</td>
            <td height="22" colspan="3"><div align="left">
                <input name="txtcodunides" type="text" id="txtcodunides" value="<?php print $ls_codunides; ?>" size="15" maxlength="10" style="text-align:center" readonly>
                <a href="javascript: ue_cataunidad();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
                <input name="txtdenunides" type="text" class="sin-borde" id="txtdenunides" value="<?php print $ls_denunides; ?>" size="50" readonly>
            </div></td>
          </tr>
		  <?php
		  	if($li_value==1)
			{
		   ?>
		   <tr>
            <td height="20" style="text-align:right">Fuente Financiamiento</td>
            <td height="22" colspan="3"><div align="left">
            <input name="txtcodfuefin" type="text" id="txtcodfuefin" value="<?php print $ls_codfuefin; ?>" size="15" maxlength="10" style="text-align:center" readonly>
            <a href="javascript: ue_buscarfuente();"></a>
            <input name="txtdenfuefin" type="text" class="sin-borde" id="txtdenfuefin" value="<?php print $ls_denfuefin; ?>" size="50" readonly>
            </div></td>
           </tr>
		   <?php
		  	}
		   ?>
          <tr class="formato-blanco">
            <td height="20" style="text-align:right">Observaci&oacute;n</td>
            <td colspan="3" rowspan="2"><textarea name="txtobsdes" cols="70" rows="3" id="textarea" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn?opqrstuvwxyz????? ()@#!%/[]*-+_');"><?php print $ls_obsdes; ?></textarea></td>
          </tr>
          <tr class="formato-blanco">
            <td height="20">&nbsp;</td>
          </tr>
          <tr class="formato-blanco">
            <td height="17">&nbsp;</td>
            <td colspan="3" align="left">
                <input name="rdtipodespacho" type="radio" class="sin-borde" value="1"  onClick="ue_completa();" <?php print $ls_checkedcomp; ?>>
              Completa 
              <input name="rdtipodespacho" type="radio" class="sin-borde" value="0"  onClick="ue_parcial();" <?php print $ls_checkedparc; ?>>
            Parcial
            <input name="txtestsol" type="hidden" id="txtestsol" value="<?php print $ls_estsol; ?>"></td>
          </tr>
          <tr class="formato-blanco">
            <td height="28" colspan="4">
			<?php
			if($ls_estartpri==1)
			{
			?>
			<a href="javascript:ue_agregar_bienes();"><img src="../shared/imagebank/tools20/nuevo.gif" width="20" height="20" class="sin-borde">Agregar Articulos </a>
			<?php }?>
			</td>
          </tr>
          <tr class="formato-blanco">
            <td height="28" colspan="4">
			  <div align="center">
			    <?php
					$in_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
			?>
	        </div></td>
          </tr>
		  <?php
		  	if($li_value==1)
			{
		   ?>
          <tr class="formato-blanco">
            <td height="28" colspan="4"><div align="center">
              <input name="btngenerar" type="button" class="boton" id="btngenerar" value="Generar Cuentas" onClick="javascript: ue_contable();" <?php print $ls_disable ?>>
            </div></td>
          </tr>
          <tr class="formato-blanco">
            <td height="28" colspan="4" align="center"><p align="center">
              <?php
					if($li_totrowp>0)
					{
						$in_grid->makegrid($li_totrowp,$lo_titlepresupuestario,$lo_objectp,$li_widthpresupuestario,$ls_titlepresupuestario,$ls_namepresupuestario);
					}
					$in_grid->makegrid($li_totrowsc,$lo_titlecontable,$lo_objectc,$li_widthcontable,$ls_titlecontable,$ls_namecontable);
			}
				?>
            </p>
                <p>&nbsp; </p></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td><input name="operacion" type="hidden" id="operacion">
          <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
          <input name="filadelete" type="hidden" id="filadelete">
          <input name="catafilas" type="hidden" id="catafilas" value="<?php print $li_catafilas;?>">
          <input name="totalfilasc" type="hidden" id="totalfilasc" value="<?php print $li_totrowsc;?>">
          <input name="totalfilasp" type="hidden" id="totalfilasp" value="<?php print $li_totrowp;?>">
		  <input name="guardaseg" type="hidden" id="guardaseg" value="0"></td>
    </tr>
  </table>
  <div align="center"></div>
</form>
<p>&nbsp;</p>
<div align="center"></div>
<p align="center">&nbsp;</p>
</body>
<script >
//Funciones de operaciones  
function ue_agregar_bienes()
{
	f=document.form1;
	totalfilas=f.totalfilas.value;
	almacen=f.txtcodalm1.value;
	articuo1=f.txtcodart1.value;
	if((articuo1!="")&&(almacen==""))
	{
		window.open("sigesp_siv_pdt_despachos.php?linea="+totalfilas+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=800,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("Debe agregar una SEP a despachar");
	}
}
function ue_cataunidad()
{
	window.open("sigesp_siv_cat_unidad.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_catarticulo(li_linea)
{
	window.open("sigesp_catdinamic_articulom.php?linea="+li_linea+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_catalmacen(li_linea)
{
	f=document.form1;
	ls_articulo= eval("f.txtcodart"+li_linea+".value");
	if (ls_articulo=="")
	{
		alert("Debe existir un articulo");
	}
	else
	{
		cansol= eval("f.txtcansol"+li_linea+".value");
		cansol=ue_formato_calculo(cansol);
		penart= eval("f.txthidpenart"+li_linea+".value");
//		penart=ue_formato_calculo(penart);
		unidad= eval("f.txtunidad"+li_linea+".value");
		pendiente=parseFloat(penart);
		li_totfilas= f.totalfilas.value;
		for(li_i=1;li_i<=li_totfilas;li_i++)
		{
			codartpri= eval("f.txtcodartpri"+li_i+".value");
			canart= eval("f.txtcanart"+li_i+".value");
			if(codartpri==ls_articulo)
			{
				pendiente=(parseFloat(pendiente) - parseFloat(canart));
			}
			
		}
		if(parseFloat(pendiente)>0)
		{
			window.open("sigesp_catdinamic_almacendespacho.php?linea="+li_linea+"&codart="+ls_articulo+"&cansol="+cansol+"&penart="+penart+"&unidad="+unidad+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=425,height=400,left=180,top=160,location=no,resizable=yes");
		}
		else
		{
			alert("Este articulo ya ha sido despachado en su totalidad");
		}
	}
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if(li_leer==1)
	{
		window.open("sigesp_catdinamic_centrodespacho.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{alert("No tiene permiso para realizar esta operacion");}
}

function ue_imprimir(ls_reporte)
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{
		ls_ordendes=  f.txtnumorddes.value;
		if(ls_ordendes!="")
		{
			numsol=    f.txtnumsol.value;
			coduniadm= f.txtcoduniadm.value;
			denunidam= f.txtdenuniadm.value;
			fecdes=    f.txtfecdes.value;
			obsdes=    f.txtobsdes.value;
			window.open("reportes/"+ls_reporte+"?numorddes="+ls_ordendes+"&numsol="+numsol+"&coduniadm="+coduniadm+"&fecdes="+fecdes+"&obsdes="+obsdes+"&denunidam="+denunidam+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
		}
		else
		{alert("Debe existir un documento a imprimir");}
	}
	else
	{alert("No tiene permiso para realizar esta operacion");}
}

function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_contable=f.contable.value;
	if(li_incluir==1)
	{	
		window.open("sigesp_catdinamic_sol_eje_pre.php?contable="+li_contable+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=650,height=400,left=50,top=50,location=no,resizable=yes,dependent=yes");
	}
	else
	{alert("No tiene permiso para realizar esta operacion");}
}

function ue_guardar()
{
	f=document.form1;
	if (f.hidvienecat.value==0)
	{
		ls_numorddes=eval("f.txtnumorddes.value");
		ls_numorddes=ue_validarvacio(ls_numorddes);
		ls_obsdes=eval("f.txtobsdes.value");
		ls_obsdes=ue_validarvacio(ls_obsdes);
		li_totfilas= f.totalfilas.value;
		lb_ok=       f.hidok.value;
		li_contable= f.contable.value;
		li_procesar= f.ejecutar.value;
		if (f.guardaseg.value==0)
		{
		  f.guardaseg.value=1;	
		  for(li_i=1;li_i<=li_totfilas;li_i++)
		  {
			ls_codact=eval("f.hcodact"+li_i+".value");
		  } 
		  ls_guardaseguido=1;
		  if(li_procesar==1)
		  {
			if((li_contable==0)||(lb_ok==true))
			{
				if(ls_numorddes!="")
				{
					lb_valido=true;
					ls_numsol=eval("f.txtnumsol.value");
					ls_numsol=ue_validarvacio(ls_numsol);
					ls_coduniadm=eval("f.txtcoduniadm.value");
					ls_coduniadm=ue_validarvacio(ls_coduniadm);
					ls_fecdes=eval("f.txtfecdes.value");
					ls_fecdes=ue_validarvacio(ls_fecdes);
					if ((ls_numsol=="")||(ls_coduniadm=="")||(ls_fecdes=="")||(ls_obsdes==""))
					{
						alert("Debe llenar los campos principales");
						lb_valido=false;
						f.guardaseg.value=0;	
					}
					lb_blancos=true;
					li_blancos=0;
					lb_completa=true;
					for(li_i=1;li_i<=li_totfilas;li_i++)
					{
						ls_denart=    eval("f.txtdenart"+li_i+".value");
						ls_denart=ue_validarvacio(ls_denart);
						ls_codart=    eval("f.txtcodart"+li_i+".value");
						ls_codart=ue_validarvacio(ls_codart);
						ls_codalm=    eval("f.txtcodalm"+li_i+".value");
						ls_codalm=ue_validarvacio(ls_codalm);
						ls_unidad=    eval("f.txtunidad"+li_i+".value");
						ls_unidad=ue_validarvacio(ls_unidad);
						ls_canart=    eval("f.txtcanart"+li_i+".value");
						ls_canart=ue_validarvacio(ls_canart);
						ls_cansol=    eval("f.txtcansol"+li_i+".value");
						ls_cansol=ue_validarvacio(ls_cansol);
						ls_preuniart= eval("f.txtpreuniart"+li_i+".value");
						ls_preuniart=ue_validarvacio(ls_preuniart);
						ls_montotart= eval("f.txtmontotart"+li_i+".value");
						ls_montotart=ue_validarvacio(ls_montotart);
						ls_pendiente= eval("f.txtpenart"+li_i+".value");
						ls_pendiente=ue_formato_operaciones(ls_pendiente);
						if(ls_pendiente!=0)
						{
							lb_completa=false;
						}
						
						if((ls_codart=="")||(ls_unidad=="")||(ls_canart=="")||(ls_codalm==""))
						{
							lb_blancos=false;
							li_blancos=li_blancos + 1;
						}
					}
					/*if(lb_completa)
					{
						if(!f.rdtipodespacho[0].checked)
						{
							alert("Debe Marcar el Despacho como Completo, ya que no quedan articulos por despachar");
							f.guardaseg.value=0;
							lb_valido=false;	
						}			
					
					}*/
					if((!lb_blancos)&&(lb_valido))
					{
						if(li_blancos==li_totfilas)
						{
							alert("Debe despachar al menos 1 art?culo");
							f.guardaseg.value=0;	
						}
						else
						{
							lb_blancos=true;
						}
					}
					if((lb_valido)&&(lb_blancos))
					{
						f.operacion.value="GUARDAR";
						f.action="sigesp_siv_p_centrodespacho.php";
						f.submit();
					}
				}
				else
				{alert("No se puede modificar este registro");}
			}
			else
			{
				alert ("Debe actualizar el detalle contable");
				f.guardaseg.value=0;	
			}
		  }
		  else
		  {alert("No tiene permiso para realizar esta operacion");}
		}
		else
		{
			alert("Debe esperar a que termine la operaci?n anterior!");
		}
	}
	else
	{
		alert ("El despacho ya se realiz?, no puede realizar la operaci?n!");
	}  
}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

function uf_dt_activo(li_row)
{
	f=document.form1;
	ls_codart=eval("f.txtcodart"+li_row+".value");
	ls_denart=eval("f.txtdenart"+li_row+".value");
	li_canart=eval("f.txtcanart"+li_row+".value");
	li_clasif=eval("f.hclasi"+li_row+".value");
	li_codact=eval("f.hcodact"+li_row+".value");
	ls_estatus=f.hidestatus.value;
	ls_numorddes=f.txtnumorddes.value;
	li_canart=ue_formato_operaciones(li_canart);
	fecha=f.txtfecdes.value;
	if((ls_codart!="")&&(li_canart>0))
	{
		if(ls_estatus=="C")
		{
		    if (li_clasif==1)
			{
				window.open("sigesp_siv_pdt_incorporaractivos.php?codart="+ls_codart+"&canart="+li_canart+"&denart="+ls_denart+"&numorddes="+ls_numorddes+"&fecha="+fecha,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=850,height=600,left=30,top=30,location=no,resizable=yes");	
			}
			else
			{
				alert("El Art?culo NO es un BIEN");
			}
		}
		else
		{
			alert("El movimiento debe estar registrado");
		}
	}
	else
	{
		alert("Debe exisistir mas de 1 articulo en el movimiento");
	}
}

function ue_montosfactura(li_row)
{
//--------------------------------------------------------
//	Funci?n que calcula el monto total por articulo multiplicando la cantidad de articulos a despachar por el costo
//  unitario de cada uno de ellos, ademas verifica que la cantidad a despachar no sea mayor a la existencia en el almacen
//   que se ha indicado e igualmente no sea mayor a la cantidad solicitada.
//--------------------------------------------------------
	f=document.form1;
	lb_valido=true;
	ls_unisol=eval("f.txtunidad"+li_row+".value");
	ls_unidad=eval("f.txtunidad"+li_row+".value");
	ls_unidad=ue_validarvacio(ls_unidad);
	li_unidad=eval("f.hidunidad"+li_row+".value");
	li_unidad=ue_validarvacio(li_unidad);
	li_existencia=eval("f.hidexistencia"+li_row+".value");
	li_existencia=ue_validarvacio(li_existencia);
	li_canart=eval("f.txtcanart"+li_row+".value");
	li_canart=ue_validarvacio(li_canart);
	li_cansol=eval("f.txtcansol"+li_row+".value");
	li_cansol=ue_validarvacio(li_cansol);
	li_preuniart=eval("f.txtpreuniart"+li_row+".value");
	li_preuniart=ue_validarvacio(li_preuniart);
	li_canpendes=eval("f.txthidpenart"+li_row+".value");
	li_canpendes=ue_validarvacio(li_canpendes);
	li_preuniart=ue_formato_operaciones(li_preuniart);
	li_canart=ue_formato_operaciones(li_canart);
	li_cansol=ue_formato_operaciones(li_cansol);
	ls_estsol=f.txtestsol.value;
	f.hidok.value=false;
	li_canartaux=li_canart;
	if(ls_unidad=="Mayor")
	{
		li_canartaux=parseFloat(li_canart) * parseFloat(li_unidad);
	}
	if(parseFloat(li_existencia)<parseFloat(li_canartaux))
	{
		eval("f.txtcanart"+li_row+".value=''");
		eval("f.txtmontotart"+li_row+".value=''");
		alert("No hay suficientes, el maximo es de "+li_existencia+" articulos al detal");
		lb_valido=false;
	}
	if ((lb_valido==true)&&(li_canart!="")&&(li_preuniart!=""))
	{
		switch(ls_unisol)
		{
			case "Mayor":
				if(ls_unidad=="Detal")
				{
					li_penart=(parseFloat(li_canpendes)-parseFloat(li_canart));
					li_penart=(parseFloat(li_penart)/parseFloat(li_unidad));
					if(li_penart<0)
					{
						eval("f.txtcanart"+li_row+".value=''");
						alert("No se puede exeder la cantidad solicitada/pendiente");
						break;
					}
					li_penart=uf_convertir(li_penart);
					obj=eval("f.txtpenart"+li_row+"");
					obj.value=li_penart;
				}
				else
				{
					li_canart=parseFloat(li_canart) * parseFloat(li_unidad);
					li_canart=String(li_canart);
					li_penart=(parseFloat(li_canpendes)-parseFloat(li_canart));
					li_penart=(parseFloat(li_penart)/parseFloat(li_unidad));
					if(li_penart<0)
					{
						eval("f.txtcanart"+li_row+".value=''");
						alert("No se puede exeder la cantidad solicitada/pendiente");
						break;
					}
					li_penart=uf_convertir(li_penart);
					obj=eval("f.txtpenart"+li_row+"");
					obj.value=li_penart;
				
				}
				li_montot=parseFloat(li_canart) * parseFloat(li_preuniart);
				li_montot=uf_convertir(li_montot);
				obj=eval("f.txtmontotart"+li_row+"");
				obj.value=li_montot;
			break;
			case "Detal":
				if(ls_unidad=="Mayor")
				{
					li_canart=parseFloat(li_canart) * parseFloat(li_unidad);
					li_canart=String(li_canart);
					li_penart=(parseFloat(li_canpendes)-parseFloat(li_canart));
					if(li_penart<0)
					{
						eval("f.txtcanart"+li_row+".value=''");
						alert("No se puede exeder la cantidad solicitada/pendiente");
						break;
					}
					li_penart=uf_convertir(li_penart);
					obj=eval("f.txtpenart"+li_row+"");
					obj.value=li_penart;
				}
				else
				{
					li_penart=(parseFloat(li_canpendes)-parseFloat(li_canart));
					if(li_penart<0)
					{
						eval("f.txtcanart"+li_row+".value=''");
						alert("No se puede exeder la cantidad solicitada/pendiente");
						break;
					}
					li_penart=uf_convertir(li_penart);
					obj=eval("f.txtpenart"+li_row+"");
					obj.value=li_penart;
				}
				li_montot=parseFloat(li_canart) * parseFloat(li_preuniart);
				li_montot=uf_convertir(li_montot);
				obj=eval("f.txtmontotart"+li_row+"");
				obj.value=li_montot;
			break;
		}
	}
}

function ue_contable()
{
//--------------------------------------------------------
// Funcion que genera los asientos contables del despacho
//--------------------------------------------------------

	f=document.form1;
	li_totfilas=  f.totalfilas.value;
	li_totfilasc= f.totalfilasc.value;
	ls_numorddes=   f.txtnumorddes.value;
	lb_blancos=   true;
	li_blancos=0;
	if(ls_numorddes!="")
	{
		for(li_i=1;li_i<=li_totfilas;li_i++)
		{
			ls_denart=    eval("f.txtdenart"+li_i+".value");
			ls_denart=ue_validarvacio(ls_denart);
			ls_codart=    eval("f.txtcodart"+li_i+".value");
			ls_codart=ue_validarvacio(ls_codart);
			ls_codalm=    eval("f.txtcodalm"+li_i+".value");
			ls_codalm=ue_validarvacio(ls_codalm);
			ls_unidad=    eval("f.txtunidad"+li_i+".value");
			ls_unidad=ue_validarvacio(ls_unidad);
			ls_canart=    eval("f.txtcanart"+li_i+".value");
			ls_canart=ue_validarvacio(ls_canart);
			ls_cansol=    eval("f.txtcansol"+li_i+".value");
			ls_cansol=ue_validarvacio(ls_cansol);
			ls_preuniart= eval("f.txtpreuniart"+li_i+".value");
			ls_preuniart=ue_validarvacio(ls_preuniart);
			ls_montotart= eval("f.txtmontotart"+li_i+".value");
			ls_montotart=ue_validarvacio(ls_montotart);
		
			if((ls_codart=="")||(ls_unidad=="")||(ls_canart=="")||(ls_codalm==""))
			{
				lb_blancos=false;
				li_blancos=li_blancos + 1;
			}
		}
		if((!lb_blancos))
		{
			if(li_blancos!=li_totfilas)
			{lb_blancos=true;}
		}
		if(lb_blancos)
		{
			f.operacion.value="CALCULARCONTABLE";
			f.action="sigesp_siv_p_centrodespacho.php";
			f.submit();
		}
	}
	else
	{alert("No se puede modificar este despacho");}
}

function ue_completa()
{
	f=document.form1;
	li_totfilas=f.totalfilas.value;
	for(li_i=1;li_i<=li_totfilas;li_i++)
	{
		ls_unisol=eval("f.txtunidad"+li_i+".value");
		li_cansol=eval("f.txtcansol"+li_i+".value");
		li_canpendes=eval("f.txthidpenart"+li_i+".value");
		li_preuniart=eval("f.txtpreuniart"+li_i+".value");
		ls_unidad=eval("f.txtunidad"+li_i+".value");
		li_unidad=eval("f.hidunidad"+li_i+".value");
		if(li_preuniart!="")
		{
			if(ls_unisol=="Mayor")
			{
				li_canpendes=(parseFloat(li_canpendes)/parseFloat(li_unidad));
				li_canpendes=uf_convertir(li_canpendes);
				obj=eval("f.txtcanart"+li_i+"");
				obj.value=li_canpendes;
                obj.readOnly=true;       //nuevo
			}
			else
			{
				li_canpendes=uf_convertir(li_canpendes);
				obj=eval("f.txtcanart"+li_i+"");
				obj.value=li_canpendes;
                obj.readOnly=true;       //nuevo
			}
			obj=eval("f.txtpenart"+li_i+"");
			obj.value="0,00";
			li_canpendes=   ue_formato_operaciones(li_canpendes);
			li_preuniart=   ue_formato_operaciones(li_preuniart);
			if(ls_unidad=="Mayor")
			{
				li_canpendes=parseFloat(li_canpendes) * parseFloat(li_unidad);
				li_canpendes=String(li_canpendes);
			}
			li_montot=parseFloat(li_canpendes) * parseFloat(li_preuniart);
			li_montot=uf_convertir(li_montot);
			obj=eval("f.txtmontotart"+li_i+"");
			obj.value=li_montot;
		}
		else
		{
			alert("Debe indicar el almac?n del que desea despachar cada art?culo.");
			f.rdtipodespacho[0].checked=false;			
			break;
		}
	}	
}

function ue_parcial()
{
   f=document.form1;
	li_totfilas=f.totalfilas.value;
	for(li_i=1;li_i<=li_totfilas;li_i++)
	{
		obj=eval("f.txtpenart"+li_i+"");
		obj.value="";
		obj=eval("f.txtcanart"+li_i+"");
		obj.value="";
        obj.readOnly=false;
		obj=eval("f.txtmontotart"+li_i+"");
		obj.value="";
	}
}

function ue_buscarfuente()
{
	f=document.form1;
	window.open("sigesp_siv_cat_fuentefinanciamiento.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
}


//--------------------------------------------------------
//	Funci?n que coloca los separadores (/) de las fechas
//--------------------------------------------------------
var patron = new Array(2,2,4)
var patron2 = new Array(1,3,3,3,3)
function ue_separadores(d,sep,pat,nums)
{
	if(d.valant != d.value)
	{
		val = d.value
		largo = val.length
		val = val.split(sep)
		val2 = ''
		for(r=0;r<val.length;r++){
			val2 += val[r]	
		}
		if(nums){
			for(z=0;z<val2.length;z++){
				if(isNaN(val2.charAt(z))){
					letra = new RegExp(val2.charAt(z),"g")
					val2 = val2.replace(letra,"")
				}
			}
		}
		val = ''
		val3 = new Array()
		for(s=0; s<pat.length; s++){
			val3[s] = val2.substring(0,pat[s])
			val2 = val2.substr(pat[s])
		}
		for(q=0;q<val3.length; q++){
			if(q ==0){
				val = val3[q]
			}
			else{
				if(val3[q] != ""){
					val += sep + val3[q]
					}
			}
		}
	d.value = val
	d.valant = val
	}
}
</script> 
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>