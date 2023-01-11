/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta = true;
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
//--------------------------------------------------------------------------------------------------------------------------------	
	
//--------------------------------------------------------------------------------------------------------------------------------	
	var trimestral = [ 
				 [ 'Enero-Marzo', '0103' ], 
	             [ 'Abril-Junio', '0406' ],
				 [ 'Julio-Septiembre', '0709' ],
				 [ 'Octubre-Diciembre', '1012' ]
				 ];
	
	var sttrimestral = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : trimestral
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbtrimestral = new Ext.form.ComboBox({
		store : sttrimestral,
		fieldLabel : 'Rango',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'trimestral',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
//----------------------------------------------------------------------------------------------------------------------------------	

//----------------------------------------------------------------------------------------------------------------------------------
	var opcimp = [ 
				 [ 'PDF', 'P' ], 
	             [ 'EXCEL', 'E' ] 
				 ];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbtiporeporte = new Ext.form.ComboBox({
		store : stOpcimp,
		fieldLabel : 'Tipo Impresi&#243;n',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'tipoimp',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
	
	cmbtiporeporte.setValue('P');
//----------------------------------------------------------------------------------------------------------------------------------	

//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO INTERVALO FECHAS
//**********************************************************************************************************************************
	fieldsetIntervaloFechasTrimestres = new Ext.form.FieldSet({
			title:"Intervalo Trimestral",
			style: 'position:absolute;left:10px;top:40px',
			border:true,
			width: 715,
			cls :'fondo',
			height: 75,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:150px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:150,
							items: [cmbtrimestral]
							}]
					}]

	});			

//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO ORDENADO POR
//**********************************************************************************************************************************
//Creacion del formulario pagos
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
	applyTo: 'formulario',
	width: 760,
	height: 170,
	title: "<H1 align='center'>Presupuesto de Caja 0713</H1>",
	frame: true,
	autoScroll: true,
	style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [fieldsetIntervaloFechasTrimestres,{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:425px;top:10px',
			border:false,
			items: [{
				layout:"form",
				border:false,
				labelWidth:100,
				items: [cmbtiporeporte]
			}]
		}]	
	});
	irCancelar();
});
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                  INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************
function irCancelar()
{
	Ext.getCmp('trimestral').enable();
}

//**********************************************************************************************************************************	
//                                  						BOTONES 
//**********************************************************************************************************************************
function irImprimir()
{
	cmbtrimestral  = Ext.getCmp('trimestral').getValue();
	if(cmbtrimestral=="")
	{
		Ext.Msg.hide();
		alert ("Debe seleccionar los Parametros de Busqueda");
	}
	else
	{
		if (Ext.getCmp('tipoimp').getValue()=='P')
		{ 
			pagina="reportes/sigesp_scg_rpp_presupuestocaja0713.php?trimestre="+cmbtrimestral+"&etiqueta=Trimestral";
		}
		else
		{
			pagina="reportes/sigesp_scg_rpp_presupuestocaja0713_excel.php?trimestre="+cmbtrimestral+"&etiqueta=Trimestral";
		}
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}
