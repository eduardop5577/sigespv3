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
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "window.close();";
		print "</script>";		
	}
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("class_folder/class_funciones_scb.php");
	$io_fun_scb=new class_funciones_scb();
	$ls_permisos="";
	$la_seguridad=Array();
	$la_permisos=Array();	
	$arrResultado=$io_fun_scb->uf_load_seguridad("SCB","sigesp_scb_d_archivoconciliacion.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Funci�n que limpia todas las variables necesarias en la p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/01/2006 								Fecha �ltima Modificaci�n : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_codarc, $ls_denarc, $ls_codban, $ls_denban, $ls_tiparc, $la_tiparc, $ls_separc, $li_filiniarc, $ls_ndequarc, $ls_ncequarc, $ls_dpequarc;
        global $ls_chequarc, $ls_rtequarc, $ls_operacion, $ls_mostrar, $ls_existe, $io_fun_scb;
		global $li_totrows, $ls_titletable,$li_widthtable,$ls_nametable,$lo_title;
		
	 	$ls_codarc="";
		$ls_denarc="";
        $ls_codban="";
        $ls_denban="";
		$ls_tiparc="";
		$la_tiparc[0]="";
		$la_tiparc[1]="";		
		$la_tiparc[2]="";		
		$ls_separc="";
		$li_filiniarc = 0;
		$ls_ndequarc = "";
		$ls_ncequarc = "";
		$ls_dpequarc = "";
		$ls_rtequarc = "";
		$ls_chequarc = "";
		$ls_mostrar="display:none";
		$ls_titletable="Campos TXT";
		$li_widthtable=600;
		$ls_nametable="grid";
		$lo_title[1]="";
		$li_totrows=$io_fun_scb->uf_obtenervalor("totalfilas",1);
		$ls_existe=$io_fun_scb->uf_obtenerexiste();
		$ls_operacion=$io_fun_scb->uf_obteneroperacion();
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_agregarlineablanca($as_tipo,$ai_totrows)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function: uf_agregarlineablanca
		//	Arguments: aa_object  // arreglo de Objetos
		//			   ai_totrows  // total de Filas
		//	Description:  Funci�n que agrega una linea mas en el grid
		//////////////////////////////////////////////////////////////////////////////
        global $lo_title, $lo_object;
                
		switch ($as_tipo)
		{
			case '0':
                    		$lo_title[1]="Campo";
                    		$lo_title[2]="Descripcion";
                    		$lo_title[3]="Inicio";
                    		$lo_title[4]="Longitud";
                    		$lo_title[5]="Item";
                    		$lo_title[6]="";
                    		$lo_title[7]="";
                                $lo_object[$ai_totrows][1]="<input name=txtcodcam".$ai_totrows." type=text id=txtcodcam".$ai_totrows." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarnumero(this);'>";
                                $lo_object[$ai_totrows][2]="<input name=txtdescam".$ai_totrows." type=text id=txtdescam".$ai_totrows." class=sin-borde size=10 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);'>";
                                $lo_object[$ai_totrows][3]="<input name=txtinicam".$ai_totrows." type=text id=txtinicam".$ai_totrows." class=sin-borde size=4 maxlength=3 onKeyUp='javascript: ue_validarnumero(this);'>";
                                $lo_object[$ai_totrows][4]="<input name=txtloncam".$ai_totrows." type=text id=txtloncam".$ai_totrows." class=sin-borde size=4 maxlength=3 onKeyUp='javascript: ue_validarnumero(this);'>";
                                $lo_object[$ai_totrows][5]="<select name=cmbcamrel".$ai_totrows." id=cmbcamrel".$ai_totrows."><option value=''>--Seleccione--</option>".
                                                           "<option value='fecmov'>Fecha</option>".
                                                           "<option value='fecmovs'>Fecha Sin Separador</option>".
                                                           "<option value='dia'>Dia</option>".
                                                           "<option value='desmov'>Descripcion</option>".
                                                           "<option value='numdoc'>Documento</option>".
                                                           "<option value='monto'>Monto</option>".
                                                           "<option value='cargo'>Cargo</option>".
                                                           "<option value='abono'>Abono</option>".
                                                           "<option value='codope'>Operacion</option>".
                                                           "<option value='ninguno'>No utilizado</option></select>".                                        
							   "<input name=txtcolcam".$ai_totrows." type=hidden id=txtcolcam".$ai_totrows." value='0'>";
                                $lo_object[$ai_totrows][6]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif title=Agregar alt=Aceptar width=15 height=15 border=0></a>";
                                $lo_object[$ai_totrows][7]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif title=Eliminar alt=Eliminar width=15 height=15 border=0></a>";			
			break;

			case '1':
                    		$lo_title[1]="Campo";
                    		$lo_title[2]="Descripcion";
                    		$lo_title[3]="Item";
                    		$lo_title[4]="";
                    		$lo_title[5]="";
                                $lo_object[$ai_totrows][1]="<input name=txtcodcam".$ai_totrows." type=text id=txtcodcam".$ai_totrows." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarnumero(this);'>";
                                $lo_object[$ai_totrows][2]="<input name=txtdescam".$ai_totrows." type=text id=txtdescam".$ai_totrows." class=sin-borde size=10 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);'>";
                                $lo_object[$ai_totrows][3]="<select name=cmbcamrel".$ai_totrows." id=cmbcamrel".$ai_totrows."><option value=''>--Seleccione--</option>".
                                                           "<option value='fecmov'>Fecha</option>".
                                                           "<option value='fecmovs'>Fecha Sin Separador</option>".
                                                           "<option value='dia'>Dia</option>".
                                                           "<option value='desmov'>Descripcion</option>".
                                                           "<option value='numdoc'>Documento</option>".
                                                           "<option value='monto'>Monto</option>".
                                                           "<option value='cargo'>Cargo</option>".
                                                           "<option value='abono'>Abono</option>".
                                                           "<option value='codope'>Operacion</option>".
                                                           "<option value='ninguno'>No utilizado</option></select>".                                        
							   "<input name=txtinicam".$ai_totrows." type=hidden id=txtinicam".$ai_totrows." value='0'>".
							   "<input name=txtloncam".$ai_totrows." type=hidden id=txtloncam".$ai_totrows." value='0'>".
							   "<input name=txtcolcam".$ai_totrows." type=hidden id=txtcolcam".$ai_totrows." value='0'>";
                                $lo_object[$ai_totrows][4]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif title=Agregar alt=Aceptar width=15 height=15 border=0></a>";
                                $lo_object[$ai_totrows][5]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif title=Eliminar alt=Eliminar width=15 height=15 border=0></a>";			
			break;

			case '2':
                    		$lo_title[1]="Campo";
                    		$lo_title[2]="Descripcion";
                    		$lo_title[3]="Columna";
                    		$lo_title[4]="Item";
                    		$lo_title[5]="";
                    		$lo_title[6]="";
                                $lo_object[$ai_totrows][1]="<input name=txtcodcam".$ai_totrows." type=text id=txtcodcam".$ai_totrows." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarnumero(this);'>";
                                $lo_object[$ai_totrows][2]="<input name=txtdescam".$ai_totrows." type=text id=txtdescam".$ai_totrows." class=sin-borde size=10 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);'>";
                                $lo_object[$ai_totrows][3]="<input name=txtcolcam".$ai_totrows." type=text id=txtcolcam".$ai_totrows." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarcomillas(this);'>";
                                $lo_object[$ai_totrows][4]="<select name=cmbcamrel".$ai_totrows." id=cmbcamrel".$ai_totrows."><option value=''>--Seleccione--</option>".
                                                           "<option value='fecmov'>Fecha</option>".
                                                           "<option value='fecmovs'>Fecha Sin Separador</option>".
                                                           "<option value='dia'>Dia</option>".
                                                           "<option value='desmov'>Descripcion</option>".
                                                           "<option value='numdoc'>Documento</option>".
                                                           "<option value='monto'>Monto</option>".
                                                           "<option value='cargo'>Cargo</option>".
                                                           "<option value='abono'>Abono</option>".
                                                           "<option value='codope'>Operacion</option>".
                                                           "<option value='ninguno'>No utilizado</option></select>".                                        
							   "<input name=txtinicam".$ai_totrows." type=hidden id=txtinicam".$ai_totrows." value='0'>".
							   "<input name=txtloncam".$ai_totrows." type=hidden id=txtloncam".$ai_totrows." value='0'>";
                                $lo_object[$ai_totrows][5]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif title=Agregar alt=Aceptar width=15 height=15 border=0></a>";
                                $lo_object[$ai_totrows][6]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif title=Eliminar alt=Eliminar width=15 height=15 border=0></a>";			
			break;
		}
		
   }
   //--------------------------------------------------------------
   
   //--------------------------------------------------------------
   function uf_cargar_dt($li_i,$li_temp,$as_tipo)
   {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Funci�n que limpia todas las variables necesarias en la p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/01/2006 								Fecha �ltima Modificaci�n : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $li_codcam, $ls_descam, $li_inicam, $li_loncam, $ls_colcam, $ls_camrel, $ls_forcam, $ls_cricam, $la_camrel;
                global $lo_object;

		$li_codcam=$_POST["txtcodcam".$li_i];
		$ls_descam=$_POST["txtdescam".$li_i];
		$li_inicam=$_POST["txtinicam".$li_i];
		$li_loncam=$_POST["txtloncam".$li_i];
		$ls_colcam=$_POST["txtcolcam".$li_i];
		$ls_camrel=$_POST["cmbcamrel".$li_i];
		
		$la_camrel[0]="";
		$la_camrel[1]="";
		$la_camrel[2]="";
		$la_camrel[3]="";
		$la_camrel[4]="";
		$la_camrel[5]="";
		$la_camrel[6]="";
		$la_camrel[7]="";
		$la_camrel[8]="";
		$la_camrel[9]="";
		switch($ls_camrel)
		{
			case "fecmov":
				$la_camrel[0]="selected";
				break;
			case "fecmovs":
				$la_camrel[1]="selected";
				break;
			case "dia":
				$la_camrel[2]="selected";
				break;
			case "desmov":
				$la_camrel[3]="selected";
				break;
			case "numdoc":
				$la_camrel[4]="selected";
				break;
			case "monto":
				$la_camrel[5]="selected";
				break;
			case "cargo":
				$la_camrel[6]="selected";
				break;
			case "abono":
				$la_camrel[7]="selected";
				break;
			case "codope":
				$la_camrel[8]="selected";
				break;
			case "ninguno":
				$la_camrel[9]="selected";
				break;
		}
		switch ($as_tipo)
		{
			case '0':
                                $lo_object[$li_temp][1]="<input name=txtcodcam".$li_temp." type=text id=txtcodcam".$li_temp." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarnumero(this);' value = '".$li_codcam."'>";
                                $lo_object[$li_temp][2]="<input name=txtdescam".$li_temp." type=text id=txtdescam".$li_temp." class=sin-borde size=10 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);' value = '".$ls_descam."'>";
                                $lo_object[$li_temp][3]="<input name=txtinicam".$li_temp." type=text id=txtinicam".$li_temp." class=sin-borde size=4 maxlength=3 onKeyUp='javascript: ue_validarnumero(this);' value = '".$li_inicam."'>";
                                $lo_object[$li_temp][4]="<input name=txtloncam".$li_temp." type=text id=txtloncam".$li_temp." class=sin-borde size=4 maxlength=3 onKeyUp='javascript: ue_validarnumero(this);' value = '".$li_loncam."'>";
                                $lo_object[$li_temp][5]="<select name=cmbcamrel".$li_temp." id=cmbcamrel".$li_temp."><option value=''>--Seleccione--</option>".
                                                           "<option value='fecmov' ".$la_camrel[0].">Fecha</option>".
                                                           "<option value='fecmovs' ".$la_camrel[1].">Fecha Sin Separador</option>".
                                                           "<option value='dia' ".$la_camrel[2].">Dia</option>".
                                                           "<option value='desmov' ".$la_camrel[3].">Descripcion</option>".
                                                           "<option value='numdoc' ".$la_camrel[4].">Documento</option>".
                                                           "<option value='monto' ".$la_camrel[5].">Monto</option>".
                                                           "<option value='cargo' ".$la_camrel[6].">Cargo</option>".
                                                           "<option value='abono' ".$la_camrel[7].">Abono</option>".
                                                           "<option value='codope' ".$la_camrel[8].">Operacion</option>".
                                                           "<option value='ninguno' ".$la_camrel[9].">Ninguno</option></select>".
							   "<input name=txtcolcam".$li_temp." type=hidden id=txtcolcam".$li_temp." value='0'>";
                                $lo_object[$li_temp][6]="<a href=javascript:uf_agregar_dt(".$li_temp.");><img src=../shared/imagebank/tools15/aprobado.gif title=Agregar alt=Aceptar width=15 height=15 border=0></a>";
                                $lo_object[$li_temp][7]="<a href=javascript:uf_delete_dt(".$li_temp.");><img src=../shared/imagebank/tools15/deshacer.gif title=Eliminar alt=Eliminar width=15 height=15 border=0></a>";			
			break;

			case '1':
                                $lo_object[$li_temp][1]="<input name=txtcodcam".$li_temp." type=text id=txtcodcam".$li_temp." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarnumero(this);' value = '".$li_codcam."'>";
                                $lo_object[$li_temp][2]="<input name=txtdescam".$li_temp." type=text id=txtdescam".$li_temp." class=sin-borde size=10 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);' value = '".$ls_descam."'>";
                                $lo_object[$li_temp][3]="<select name=cmbcamrel".$li_temp." id=cmbcamrel".$li_temp."><option value=''>--Seleccione--</option>".
                                                           "<option value='fecmov' ".$la_camrel[0].">Fecha</option>".
                                                           "<option value='fecmovs' ".$la_camrel[1].">Fecha Sin Separador</option>".
                                                           "<option value='dia' ".$la_camrel[2].">Dia</option>".
                                                           "<option value='desmov' ".$la_camrel[3].">Descripcion</option>".
                                                           "<option value='numdoc' ".$la_camrel[4].">Documento</option>".
                                                           "<option value='monto' ".$la_camrel[5].">Monto</option>".
                                                           "<option value='cargo' ".$la_camrel[6].">Cargo</option>".
                                                           "<option value='abono' ".$la_camrel[7].">Abono</option>".
                                                           "<option value='codope' ".$la_camrel[8].">Operacion</option>".
                                                           "<option value='ninguno' ".$la_camrel[9].">Ninguno</option></select>".
							   "<input name=txtinicam".$li_temp." type=hidden id=txtinicam".$li_temp." value='0'>".
							   "<input name=txtloncam".$li_temp." type=hidden id=txtloncam".$li_temp." value='0'>".
							   "<input name=txtcolcam".$li_temp." type=hidden id=txtcolcam".$li_temp." value='0'>";
                                $lo_object[$li_temp][4]="<a href=javascript:uf_agregar_dt(".$li_temp.");><img src=../shared/imagebank/tools15/aprobado.gif title=Agregar alt=Aceptar width=15 height=15 border=0></a>";
                                $lo_object[$li_temp][5]="<a href=javascript:uf_delete_dt(".$li_temp.");><img src=../shared/imagebank/tools15/deshacer.gif title=Eliminar alt=Eliminar width=15 height=15 border=0></a>";			
			break;

			case '2':
                                $lo_object[$li_temp][1]="<input name=txtcodcam".$li_temp." type=text id=txtcodcam".$li_temp." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarnumero(this);' value = '".$li_codcam."'>";
                                $lo_object[$li_temp][2]="<input name=txtdescam".$li_temp." type=text id=txtdescam".$li_temp." class=sin-borde size=10 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);' value = '".$ls_descam."'>";
                                $lo_object[$li_temp][3]="<input name=txtcolcam".$li_temp." type=text id=txtcolcam".$li_temp." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarcomillas(this);' value = '".$ls_colcam."'>";
                                $lo_object[$li_temp][4]="<select name=cmbcamrel".$li_temp." id=cmbcamrel".$li_temp."><option value=''>--Seleccione--</option>".
                                                           "<option value='fecmov' ".$la_camrel[0].">Fecha</option>".
                                                           "<option value='fecmovs' ".$la_camrel[1].">Fecha Sin Separador</option>".
                                                           "<option value='dia' ".$la_camrel[2].">Dia</option>".
                                                           "<option value='desmov' ".$la_camrel[3].">Descripcion</option>".
                                                           "<option value='numdoc' ".$la_camrel[4].">Documento</option>".
                                                           "<option value='monto' ".$la_camrel[5].">Monto</option>".
                                                           "<option value='cargo' ".$la_camrel[6].">Cargo</option>".
                                                           "<option value='abono' ".$la_camrel[7].">Abono</option>".
                                                           "<option value='codope' ".$la_camrel[8].">Operacion</option>".
                                                           "<option value='ninguno' ".$la_camrel[9].">Ninguno</option></select>".
							   "<input name=txtinicam".$li_temp." type=hidden id=txtinicam".$li_temp." value='0'>".
							   "<input name=txtloncam".$li_temp." type=hidden id=txtloncam".$li_temp." value='0'>";
                                $lo_object[$li_temp][5]="<a href=javascript:uf_agregar_dt(".$li_temp.");><img src=../shared/imagebank/tools15/aprobado.gif title=Agregar alt=Aceptar width=15 height=15 border=0></a>";
                                $lo_object[$li_temp][6]="<a href=javascript:uf_delete_dt(".$li_temp.");><img src=../shared/imagebank/tools15/deshacer.gif title=Eliminar alt=Eliminar width=15 height=15 border=0></a>";			
			break;
		}
                
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title >Definici&oacute;n de Archivos TXT</title>
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
<script type="text/javascript"  src="js/funcion_scb.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_scb_c_archivoconciliacion.php");
	$io_archivo=new sigesp_scb_c_archivoconciliacion();
	require_once("../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	uf_limpiarvariables();
	$ls_codarc=$io_archivo->uf_nuevo_codigo();
	switch ($ls_operacion) 
	{
		case "MOSTRAR_GRID":
			$li_totrows=1;
			$ls_codarc=$_POST["txtcodarc"];
			$ls_denarc=$_POST["txtdenarc"];
                        $ls_codban=$_POST["txtcodban"];
                        $ls_denban=$_POST["txtdenban"];                       
			$ls_tiparc=$_POST["cmbtiparc"];
			$la_tiparc=$io_fun_scb->uf_seleccionarcombo("0-1-2",$ls_tiparc,$la_tiparc,3);
                        $ls_separc=$io_fun_scb->uf_obtenervalor("txtseparc","");
                        $li_filiniarc=$io_fun_scb->uf_obtenervalor("txtfiliniarc","0");
                        $ls_ndequarc=$io_fun_scb->uf_obtenervalor("txtndequarc","");
			$ls_ncequarc=$io_fun_scb->uf_obtenervalor("txtncequarc","");
                        $ls_dpequarc=$io_fun_scb->uf_obtenervalor("txtdpequarc","");
                        $ls_rtequarc=$io_fun_scb->uf_obtenervalor("txtrtequarc","");
                        $ls_chequarc=$io_fun_scb->uf_obtenervalor("txtchequarc","");
			uf_agregarlineablanca($ls_tiparc,1);
			$ls_mostrar="display:compact";
			break;	
		

		case "GUARDAR":
			$ls_codarc=$_POST["txtcodarc"];
			$ls_denarc=$_POST["txtdenarc"];
                        $ls_codban=$_POST["txtcodban"];
                        $ls_denban=$_POST["txtdenban"];                       
			$ls_tiparc=$_POST["cmbtiparc"];
			$la_tiparc=$io_fun_scb->uf_seleccionarcombo("0-1-2",$ls_tiparc,$la_tiparc,3);
                        $ls_separc=$io_fun_scb->uf_obtenervalor("txtseparc","");
                        $li_filiniarc=$io_fun_scb->uf_obtenervalor("txtfiliniarc","0");
                        $ls_ndequarc=$io_fun_scb->uf_obtenervalor("txtndequarc","");
			$ls_ncequarc=$io_fun_scb->uf_obtenervalor("txtncequarc","");
                        $ls_dpequarc=$io_fun_scb->uf_obtenervalor("txtdpequarc","");
                        $ls_rtequarc=$io_fun_scb->uf_obtenervalor("txtrtequarc","");
                        $ls_chequarc=$io_fun_scb->uf_obtenervalor("txtchequarc","");
			$io_archivo->io_sql->begin_transaction();			
			$lb_valido=$io_archivo->uf_guardar($ls_existe,$ls_codarc,$ls_denarc,$ls_tiparc,$ls_separc,$li_filiniarc,
                                               $ls_ndequarc,$ls_ncequarc,$ls_dpequarc,$ls_rtequarc,$ls_chequarc,$ls_codban,$la_seguridad);
			if($lb_valido)
			{
				$lb_valido=$io_archivo->uf_delete_campos($ls_codarc,$la_seguridad);
				for($li_i=1;($li_i<$li_totrows)&&($lb_valido);$li_i++)
				{
                                    $li_codcam=$_POST["txtcodcam".$li_i];
                                    $ls_descam=$_POST["txtdescam".$li_i];
                                    $li_inicam=$_POST["txtinicam".$li_i];
                                    $li_loncam=$_POST["txtloncam".$li_i];
                                    $ls_colcam=$_POST["txtcolcam".$li_i];
                                    $ls_camrel=$_POST["cmbcamrel".$li_i];
                                    $ls_forcam="";
                                    $ls_cricam="";
                                    $lb_valido=$io_archivo->uf_insert_archivotxt_campos($ls_codarc,$li_codcam,$ls_descam,$li_inicam,$li_loncam,
                                                                                         $ls_colcam,$ls_camrel,$ls_forcam,$ls_cricam,$la_seguridad);
				}
			}
			if($lb_valido)
			{
				$io_archivo->io_sql->commit();
				if($ls_existe=="TRUE")
				{
					$io_archivo->io_mensajes->message("El archivo txt fue Actualizado.");
				}
				else
				{
					$io_archivo->io_mensajes->message("El archivo txt fue Registrado.");
					
				}
			}
			else
			{
				$io_archivo->io_sql->rollback();
				$io_archivo->io_mensajes->message("Ocurrio un error al guardar el archivo txt.");
			}
			uf_limpiarvariables();
			$ls_codarc=$io_archivo->uf_nuevo_codigo();
			$ls_existe="FALSE";
			$ls_mostrar="display:none";
			break;

		case "ELIMINAR":
			$ls_codarc=$_POST["txtcodarc"];
			$lb_valido=$io_archivo->uf_delete_archivotxt($ls_codarc,$la_seguridad);
			uf_limpiarvariables();
			$ls_existe="FALSE";
			$li_totrows=1;			
			$ls_codarc=$io_archivo->uf_nuevo_codigo();
			break;

		case "AGREGARDETALLE":
			$ls_codarc=$_POST["txtcodarc"];
			$ls_denarc=$_POST["txtdenarc"];
                        $ls_codban=$_POST["txtcodban"];
                        $ls_denban=$_POST["txtdenban"];                                               
			$ls_tiparc=$_POST["cmbtiparc"];
			$la_tiparc=$io_fun_scb->uf_seleccionarcombo("0-1-2",$ls_tiparc,$la_tiparc,3);
                        $ls_separc=$io_fun_scb->uf_obtenervalor("txtseparc","");
                        $li_filiniarc=$io_fun_scb->uf_obtenervalor("txtfiliniarc","0");
                        $ls_ndequarc=$io_fun_scb->uf_obtenervalor("txtndequarc","");
			$ls_ncequarc=$io_fun_scb->uf_obtenervalor("txtncequarc","");
                        $ls_dpequarc=$io_fun_scb->uf_obtenervalor("txtdpequarc","");
                        $ls_rtequarc=$io_fun_scb->uf_obtenervalor("txtrtequarc","");
                        $ls_chequarc=$io_fun_scb->uf_obtenervalor("txtchequarc","");
			$li_totrows=$li_totrows+1;			
			for($li_i=1;$li_i<$li_totrows;$li_i++)
			{
				uf_cargar_dt($li_i,$li_i,$ls_tiparc);
			}
			uf_agregarlineablanca($ls_tiparc,$li_totrows);
			$ls_mostrar="display:compact";
			break;

		case "ELIMINARDETALLE":
			$ls_codarc=$_POST["txtcodarc"];
			$ls_denarc=$_POST["txtdenarc"];
                        $ls_codban=$_POST["txtcodban"];
                        $ls_denban=$_POST["txtdenban"];                                               
			$ls_tiparc=$_POST["cmbtiparc"];
			$la_tiparc=$io_fun_scb->uf_seleccionarcombo("0-1-2",$ls_tiparc,$la_tiparc,3);
                        $ls_separc=$io_fun_scb->uf_obtenervalor("txtseparc","");
                        $li_filiniarc=$io_fun_scb->uf_obtenervalor("txtfiliniarc","0");
                        $ls_ndequarc=$io_fun_scb->uf_obtenervalor("txtndequarc","");
			$ls_ncequarc=$io_fun_scb->uf_obtenervalor("txtncequarc","");
                        $ls_dpequarc=$io_fun_scb->uf_obtenervalor("txtdpequarc","");
                        $ls_rtequarc=$io_fun_scb->uf_obtenervalor("txtrtequarc","");
                        $ls_chequarc=$io_fun_scb->uf_obtenervalor("txtchequarc","");
			$li_totrows=$li_totrows-1;
			$li_rowdelete=$_POST["filadelete"];
			$li_temp=0;
			for($li_i=1;$li_i<=$li_totrows;$li_i++)
			{
				if($li_i!=$li_rowdelete)
				{		
					$li_temp++;			
					uf_cargar_dt($li_i,$li_temp,$ls_tiparc);
				}
			}
			uf_agregarlineablanca($ls_tiparc,$li_totrows);
			$ls_mostrar="display:compact";
			break;
			
		case "BUSCARDETALLE":
			$ls_codarc=$_POST["txtcodarc"];
			$ls_denarc=$_POST["txtdenarc"];
                        $ls_codban=$_POST["txtcodban"];
                        $ls_denban=$_POST["txtdenban"];                                               
			$ls_tiparc=$_POST["cmbtiparc"];
			$la_tiparc=$io_fun_scb->uf_seleccionarcombo("0-1-2",$ls_tiparc,$la_tiparc,3);
                        $ls_separc=$io_fun_scb->uf_obtenervalor("txtseparc","");
                        $li_filiniarc=$io_fun_scb->uf_obtenervalor("txtfiliniarc","0");
                        $ls_ndequarc=$io_fun_scb->uf_obtenervalor("txtndequarc","");
			$ls_ncequarc=$io_fun_scb->uf_obtenervalor("txtncequarc","");
                        $ls_dpequarc=$io_fun_scb->uf_obtenervalor("txtdpequarc","");
                        $ls_rtequarc=$io_fun_scb->uf_obtenervalor("txtrtequarc","");
                        $ls_chequarc=$io_fun_scb->uf_obtenervalor("txtchequarc","");
			$ls_activarcodigo="readOnly";
			$li_totrows=0;
			$lo_object="";
			$arrResultado=$io_archivo->uf_load_archivotxt_campos($ls_codarc,$ls_tiparc);
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$lb_valido=$arrResultado['lb_valido'];
			$li_totrows++;
			uf_agregarlineablanca($ls_tiparc,$li_totrows);
			$ls_mostrar="display:compact";
			break;
	}
	$io_archivo->uf_destructor();
	unset($io_archivo);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" title="Buscar" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" title="Eliminar" alt="Eliminar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
<form name="form1" method="post" action="" id="sigesp_scb_d_archivoconciliacion.php">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_scb->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_scb);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="600" height="260" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td>
      <p>&nbsp;</p>
      <table width="550" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="2" class="titulo-ventana">Definici&oacute;n de Archivos para Conciliacion  </td>
        </tr>
        <tr>
          <td width="157" height="22">&nbsp;</td>
          <td width="387">&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo</div></td>
          <td><div align="left">
            <input name="txtcodarc" type="text" id="txtcodarc" size="6" maxlength="4" value="<?php print $ls_codarc;?>" onKeyUp="javascript: ue_validarnumero(this);" onBlur="javascript: ue_rellenarcampo(this,4);" readonly>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Denominaci&oacute;n</div></td>
          <td><div align="left">
            <input name="txtdenarc" type="text" id="txtdenarc" value="<?php print $ls_denarc;?>" size="60" maxlength="120" onKeyUp="ue_validarcomillas(this);">
          </div></td>
        </tr>
        <tr>
          <td width="89" height="22"  align="right">Banco</td>
          <td colspan="3" align="left">
              <input name="txtcodban" type="text" id="txtcodban"  style="text-align:center" value="<?php print $ls_codban;?>" size="10" readonly>
              <a href="javascript:cat_bancos();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Bancos"></a>
              <input name="txtdenban" type="text" id="txtdenban" value="<?php print $ls_denban?>" size="100" class="sin-borde" readonly>
          </td>
        </tr>
        
        <tr>
		<tr>
          <td height="22"><div align="right">Tipo de Archivo</div></td>
          <td>
            <select name="cmbtiparc" id="cmbtiparc" onChange="javascript: uf_mostrar_grid();">
			  <option value="" selected>--Seleccione Uno--</option>
			  <option value="0" <?php print $la_tiparc[0];?>>Plano TXT(Metodo)</option>
			  <option value="1" <?php print $la_tiparc[1];?>>Plano TXT(Separador)</option>
			  <option value="2" <?php print $la_tiparc[2];?>>Excel</option>
			</select>
          </td>
        </tr>
		<?php 
			if ($ls_tiparc=='1')
			{
                ?>
			 <tr>
			  <td height="20"><div align="right">Separador TXT</div></td>
			  <td height="20" colspan="2"><input name="txtseparc" type="text" id="txtseparc" value="<?php print $ls_separc;?>" size="3" maxlength="1" onKeyUp="ue_validarcomillas(this);"></td>
			</tr>
		<?php 			
			}
		?>
        <tr>
            <td height="20"><div align="right">Fila Inicial</div></td>
            <td height="20" colspan="2"><input name="txtfiliniarc" type="text" id="txtfiliniarc" value="<?php print $li_filiniarc;?>" size="3" maxlength="2" onKeyUp="javascript: ue_validarnumero(this);"></td>
        </tr>
        <tr class="formato-azul">
            <td height="13" colspan="3" align="center"><span class="Estilo1">Equivalencia de c&oacute;digos de operaciones</span></td>
	</tr>
        <tr>
            <td height="20"><div align="right">Nota D&eacute;bito</div></td>
            <td height="20" colspan="2" align="left" class="celdas-blancas">
                <input name="txtndequarc" id="txtndequarc" type="text" value="<?php print $ls_ndequarc?>" size="20" maxlength="120">
            </td>
	</tr>
        <tr>
            <td height="20"><div align="right">Nota Cr&eacute;dito</div></td>
            <td height="20" colspan="2" align="left" class="celdas-blancas">
                <input name="txtncequarc" id="txtncequarc" type="text" value="<?php print $ls_ncequarc?>" size="20" maxlength="120">
            </td>
	</tr>
        <tr>
            <td height="20"><div align="right">Deposito</div></td>
            <td height="20" colspan="2" align="left" class="celdas-blancas">
                <input name="txtdpequarc" id="txtdpequarc" type="text" value="<?php print $ls_dpequarc?>" size="20" maxlength="120">
            </td>
	</tr>
        <tr>
            <td height="20"><div align="right">Retiro</div></td>
            <td height="20" colspan="2" align="left" class="celdas-blancas">
                <input name="txtrtequarc" id="txtrtequarc" type="text" value="<?php print $ls_rtequarc?>" size="20" maxlength="120">
            </td>
	</tr>
        <tr>
            <td height="20"><div align="right">Cheque</div></td>
            <td height="20" colspan="2" align="left" class="celdas-blancas">
                <input name="txtchequarc" id="txtchequarc" type="text" value="<?php print $ls_chequarc?>" size="20" maxlength="120">
            </td>
	</tr>
        <tr>
          <td colspan="3"><input name="operacion" type="hidden" id="operacion">
            <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>"></td>
        </tr>
        <tr>
          <td colspan="2">
		  	<div align="center" id="grid" style="<?php print $ls_mostrar;?>">
			<?php
					$io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
					unset($io_grid);
			?>
			  </div>
		  	<p>
              <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
              <input name="filadelete" type="hidden" id="filadelete">
<?php  
       if (($ls_tiparc=='0')||($ls_tiparc==''))
       {  
            print "<input name='txtseparc' type='hidden' id='txtseparc' value=''>";
       }
       if ($ls_tiparc=='2')
       {  
            print "<input name='txtseparc' type='hidden' id='txtseparc' value=''>";
       }
?>
			</p>			
          </td>		  
          </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
</body>
<script >

function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f=document.form1;
		f.operacion.value="NUEVO";
		f.existe.value="FALSE";		
		f.action="sigesp_scb_d_archivoconciliacion.php";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	lb_existe=f.existe.value;
	if(((lb_existe=="TRUE")&&(li_cambiar==1))||(lb_existe=="FALSE")&&(li_incluir==1))
	{
		ls_codarc=ue_validarvacio(f.txtcodarc.value);
		ls_denarc=ue_validarvacio(f.txtdenarc.value);
		ls_codban=ue_validarvacio(f.txtcodban.value);
		ls_tiparc=ue_validarvacio(f.cmbtiparc.value);
		li_total=f.totalfilas.value;
		if ((ls_codarc=="")||(ls_codarc=="")||(ls_codban=="")||(ls_tiparc=="")||(li_total=="0"))
		{
			alert("Debe llenar todos los datos.");
		}
		else
		{
			f.cmbtiparc.disabled="";
			f.operacion.value="GUARDAR";
			f.action="sigesp_scb_d_archivoconciliacion.php";
			f.submit();
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1)
	{	
		if(f.existe.value=="TRUE")
		{
			ls_codarc = ue_validarvacio(f.txtcodarc.value);
			if (ls_codarc=="")
			{
				alert("Debe buscar el registro a eliminar.");
			}
			else
			{
				if(confirm("�Desea eliminar el Registro actual?"))
				{
					f.operacion.value="ELIMINAR";
					f.action="sigesp_scb_d_archivoconciliacion.php";
					f.submit();
				}
			}
		}
		else
		{
			alert("Debe buscar el registro a eliminar.");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
	//window.open("../hlp/index.php?sistema=SNO&subsistema=SNR&nomfis=sno/sigesp_hlp_snr_tablavacaciones.php","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		window.open("sigesp_scb_cat_archivoconciliacion.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
		f.cmbtiparc.disabled="";
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function uf_agregar_dt(li_row)
{
	f=document.form1;	
	li_total=f.totalfilas.value;
	if(li_total==li_row)
	{
		li_codcamnew=eval("f.txtcodcam"+li_row+".value");
		li_total=f.totalfilas.value;
		lb_valido=false;
		for(li_i=1;li_i<=li_total&&lb_valido!=true;li_i++)
		{
			li_codcam=eval("f.txtcodcam"+li_i+".value");
			if((li_codcam==li_codcamnew)&&(li_i!=li_row))
			{
				alert("el campo ya existe");
				lb_valido=true;
			}
		}
		ls_codarc=eval("f.txtcodarc.value");
		ls_codarc=ue_validarvacio(ls_codarc);
		ls_denarc=eval("f.txtdenarc.value");
		ls_denarc=ue_validarvacio(ls_denarc);
		ls_tiparc=eval("f.cmbtiparc.value");
		ls_tiparc=ue_validarvacio(ls_tiparc);
		li_codcam=eval("f.txtcodcam"+li_row+".value");
		li_codcam=ue_validarvacio(li_codcam);
		ls_descam=eval("f.txtdescam"+li_row+".value");
		ls_descam=ue_validarvacio(ls_descam);
		li_inicam=eval("f.txtinicam"+li_row+".value");
		li_inicam=ue_validarvacio(li_inicam);
		li_loncam=eval("f.txtloncam"+li_row+".value");
		li_loncam=ue_validarvacio(li_loncam);
		li_colcam=eval("f.txtcolcam"+li_row+".value");
		li_colcam=ue_validarvacio(li_colcam);
		if((ls_codarc=="")||(ls_denarc=="")||(ls_tiparc=="")||(li_codcam=="")||(ls_descam=="")||(li_inicam=="")||(li_loncam=="")||(li_colcam==""))
		{
			alert("Debe llenar todos los campos");
			lb_valido=true;
		}
		if(!lb_valido)
		{
			f.operacion.value="AGREGARDETALLE";
			f.action="sigesp_scb_d_archivoconciliacion.php";
			f.submit();
		}
	}
}

function uf_delete_dt(li_row)
{
	f=document.form1;
	li_total=f.totalfilas.value;
	if(li_total>li_row)
	{
		li_codcam=eval("f.txtcodcam"+li_row+".value");
		li_codcam=ue_validarvacio(li_codcam);
		if(li_codcam=="")
		{
			alert("la fila a eliminar no debe estar vacio el lapso");
		}
		else
		{
			if(confirm("�Desea eliminar el �ltimo Registro?"))
			{
				f.cmbtiparc.disabled="";
				f.filadelete.value=li_row;
				f.operacion.value="ELIMINARDETALLE"
				f.action="sigesp_scb_d_archivoconciliacion.php";
				f.submit();
			}
		}
	}
}

function uf_mostrar_grid ()
{
	f=document.form1;
	f.operacion.value="MOSTRAR_GRID";
	f.action="sigesp_scb_d_archivoconciliacion.php";
	f.submit();
}

function cat_bancos() {
	window.open("sigesp_cat_bancos.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
}

</script> 
</html>