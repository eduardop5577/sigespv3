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

var fromReporteComCauNoPag = null; //varibale para almacenar la instacia de objeto de formulario
barraherramienta = true;
var fecha = new Date();

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//------------------------------------------------------------------------------------------------------------

	var	fromIntervaloFechas = new Ext.form.FieldSet({
			title:'Intervalo de Fechas',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 58,
			items:[{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:25px;top:10px',
					border:false,
					items:[{
							layout:"form",
							border:false,
							labelWidth:50,
							items:[{
									xtype:"datefield",
									labelSeparator :'',
									fieldLabel:"Desde",
									name:'Desde',
									id:'dtFechaDesde',
									allowBlank:true,
									width:100,
									binding:true,
									defaultvalue:'1900-01-01',
									hiddenvalue:'',
									allowBlank:false,
									value: '01/01/'+fecha.getFullYear(),
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:390px;top:10px',
					border:false,
					items:[{
							layout:"form",
							border:false,
							labelWidth:50,
							items:[{
									xtype:"datefield",
									labelSeparator :'',
									fieldLabel:"Hasta",
									name:'Hasta',
									id:'dtFechaHasta',
									allowBlank:true,
									width:100,
									binding:true,
									defaultvalue:'1900-01-01',
									hiddenvalue:'',
									allowBlank:false,
									value:  new Date().format('d-m-Y'),
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					}]
	})

	//------------------------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(300)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteComCauNoPag = new Ext.FormPanel({
		applyTo: 'formReporteComCauNoPag',
		width:600, //700
		height: 120,
		title: "<H1 align='center'>COMPROMISOS CAUSADOS NO PAGADOS</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromIntervaloFechas]
	});	
	fromReporteComCauNoPag.doLayout();
});	

//------------------------------------------------------------------------------------------------------------

function irImprimir(){
	var valido = true;
    var fecdes = Ext.getCmp('dtFechaDesde').getValue().format('Y-m-d');
    var fechas = Ext.getCmp('dtFechaHasta').getValue().format('Y-m-d');
	
	if(fecdes>fechas){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por Fecha no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(valido){
		var pagina = "reportes/sigesp_spg_rpp_compromisos_causados_no_pagados.php?txtfecdes="+fecdes+"&txtfechas="+fechas;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}
