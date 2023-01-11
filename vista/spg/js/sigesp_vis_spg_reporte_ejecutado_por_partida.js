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

var fromReporteEjexPar = null;
barraherramienta = true;
var fecha = new Date();

Ext.onReady(function() {
Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	
	//-------------------------------------------------------------------------------------

	//componente catalogo de cuentas presupuestarias
	var reCuentaPre = Ext.data.Record.create([
        {name: 'spg_cuenta'}, //campo obligatorio                             
        {name: 'denominacion'}, //campo obligatorio
        {name: 'status'}, //campo obligatorio
    ]);
	
	comcampocatcuentadesde = new com.sigesp.vista.comCatalogoCuentaSPG({
		idComponente:'spgdesde',
		anchofieldset: 900,
		reCatalogo: reCuentaPre,
		rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_comprobante.php',
		parametros: "ObjSon={'operacion': 'buscarCuentasPresupuestarias'",
		posicion:'position:absolute;left:25px;top:10px', 
		tittxt:'Desde',
		idtxt:'cuentadesde',
		campovalue:'spg_cuenta',
		anchoetiquetatext:60,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'denodesde',
		labelvalue:'',
		anchocoletiqueta:0.35, 
		anchoetiqueta:0,
		binding:'',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false, 
	});
	
	comcampocatcuentahasta = new com.sigesp.vista.comCatalogoCuentaSPG({
		idComponente:'spghasta',
		anchofieldset: 900,
		reCatalogo: reCuentaPre,
		rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_comprobante.php',
		parametros: "ObjSon={'operacion': 'buscarCuentasPresupuestarias'",
		posicion:'position:absolute;left:320px;top:10px', 
		tittxt:'Hasta',
		idtxt:'cuentahasta',
		campovalue:'spg_cuenta',
		anchoetiquetatext:60,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'denohasta',
		labelvalue:'',
		anchocoletiqueta:0.35, 
		anchoetiqueta:0,
		binding:'',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false, 
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos del tipo de impresion
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ]];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromCuentaPre = new Ext.form.FieldSet({
			title:'Intervalo de Cuentas',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 68,
			items: [comcampocatcuentadesde.fieldsetCatalogo,comcampocatcuentahasta.fieldsetCatalogo]
	})
	
	//--------------------------------------------------------------------------------------------

	var	fromIntervaloFechas = new Ext.form.FieldSet({
			title:'Intervalo de Fechas',
			style: 'position:absolute;left:10px;top:85px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 58,
			items:[{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:35px;top:10px',
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
					style: 'position:absolute;left:330px;top:10px',
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
	
	//--------------------------------------------------------------------------------------------

	var fromImpresion = new Ext.form.FieldSet({ 
			title:'Tipo de Impresion',
			style: 'position:absolute;left:10px;top:150px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:40px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 30,
							items: [{
									xtype: 'combo',
									fieldLabel: '',
									labelSeparator :'',
									id: 'opcimp',
									store : stOpcimp,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
									triggerAction : 'all',
									mode : 'local',
									emptyText:'PDF',
									listWidth:150,
									width:150,				
								}]
							}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:330px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 100,
						items: [{
								xtype: 'checkbox',
								labelSeparator :'',
								fieldLabel: 'Reporte Resumido',
								id: 'repres',
								inputValue:1,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'0',
								allowBlank:true				
							}]
						}]
				}]
	})
	
	//------------------------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(300)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteEjexPar = new Ext.FormPanel({
		applyTo: 'formReporteEjexPar',
		width:600, 
		height: 270,
		title: "<H1 align='center'>EJECUTADO POR PARTIDA</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromCuentaPre,
		       fromIntervaloFechas,
		       fromImpresion
		        ]
	});
	fromReporteEjexPar.doLayout();
});

//------------------------------------------------------------------------------------------------------------

function irImprimir()
{
	var valido = true;
	var cuentadesde = Ext.getCmp('cuentadesde').getValue();
	var cuentahasta = Ext.getCmp('cuentahasta').getValue();
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
	if(cuentadesde>cuentahasta){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El rango de Busqueda por Cuenta Presupuestaria no es correcto!!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((cuentadesde=='' && cuentahasta!='') || (cuentadesde!='' && cuentahasta=='')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Cuenta Presupuestaria!!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(valido){
		var resumen = 0;
		var opcion = 'P';
		var formato = "sigesp_spg_rpp_ejecutado_por_partida.php";
	    if(Ext.getCmp('repres').checked){
	    	resumen = 1;
	    }
	    if(Ext.getCmp('opcimp').getValue()=='E'){
	    	formato = "sigesp_spg_rpp_ejecutado_por_partida_excel.php";
	    }
	    var pagina = "reportes/"+formato+"?txtcuentades="+cuentadesde
				    +"&txtcuentahas="+cuentahasta+"&txtfecdes="+fecdes+"&txtfechas="+fechas+"&resumen="+resumen;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}
