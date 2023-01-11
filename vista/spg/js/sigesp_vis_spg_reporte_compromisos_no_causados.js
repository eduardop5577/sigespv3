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

var fromReporteComNoCau = null; //varibale para almacenar la instacia de objeto de formulario
barraherramienta = true;
var fromProveedor = null;
var fromBeneficiario = null;
var fecha = new Date();


Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//--------------------------------------------------------------------------------------------

	//Creando el campo de proveedor
	var reProveedor = Ext.data.Record.create([
		{name: 'cod_pro'}, //campo obligatorio                             
		{name: 'nompro'}, //campo obligatorio
		{name: 'dirpro'}, //campo obligatorio
		{name: 'rifpro'}, //campo obligatorio
		{name: 'tipconpro'} //campo adicional
	]);
		
	//componente catalogo de proveedores
	comcampocatproveedor = new com.sigesp.vista.comCatalogoProveedor({
		idComponente:'prov',
		anchofieldset: 850,
		reCatalogo: reProveedor,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
		parametros: "ObjSon={'operacion': 'buscarProveedores'",
		posicion:'position:absolute;left:10px;top:10px', 
		tittxt:'C�digo',
		idtxt:'cod_pro',
		campovalue:'cod_pro',
		anchoetiquetatext:50,
		anchotext:150,
		anchocoltext:0.25, 
		idlabel:'nompro',
		labelvalue:'nompro',
		anchocoletiqueta:0.55, 
		anchoetiqueta:250,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});
	//fin componente catalogo de proveedores
	
	//--------------------------------------------------------------------------------------------
	
	//Creando el campo de beneficiario
	var reBeneficiario = Ext.data.Record.create([
		{name: 'ced_bene'}, //campo obligatorio                             
		{name: 'nombene'}, //campo obligatorio
	]);
		
	//componente catalogo de proveedores
	comcampocatbeneficiario = new com.sigesp.vista.comCatalogoBeneficiario({
		idComponente:'bene',
		anchofieldset: 850,
		reCatalogo: reBeneficiario,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatbeneficiario.php',
		parametros: "ObjSon={'operacion': 'buscarBeneficiarios'",
		posicion:'position:absolute;left:10px;top:10px', 
		tittxt:'C&#233;dula',
		idtxt:'ced_bene',
		campovalue:'ced_bene',
		anchoetiquetatext:50,
		anchotext:150,
		anchocoltext:0.25, 
		idlabel:'nombene',
		labelvalue:'nombene',
		anchocoletiqueta:0.55, 
		anchoetiqueta:250,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});
	//fin componente catalogo de proveedores
	
	//-------------------------------------------------------------------------------------
	
	//Datos del tipo de impresion
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ]];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos de la orientacion del movimiento
	var orimov = [ [ 'Todos', 'T' ], 
	               [ 'Solicitud Ejecucion Presupuestaria', 'SEPSPC' ],
	               [ 'Orden de Compras(Bienes)', 'SOCCOC' ],
	               [ 'Orden de Compras(Servicios)', 'SOCCOS' ],
	               [ 'Obras(Contratos)', 'SOBCON' ],
	               [ 'Nóminas', 'SNOCNO' ]];
	
	var stOrimov = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : orimov
	});
	
	//--------------------------------------------------------------------------------------------

	var	fromIntervaloFechas = new  Ext.form.FieldSet({
			title:'Intervalo de Fechas',
			style: 'position:absolute;left:60px;top:10px',
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
					style: 'position:absolute;left:380px;top:10px',
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

	var fromTipo = new Ext.form.FieldSet({
		title:'Proveedor/Beneficiario',
		style: 'position:absolute;left:10px;top:75px',
		border:true,
		width: 700,
		cls :'fondo',
		height: 58,
		items:[{
			xtype: "radiogroup",
			fieldLabel: '',
			labelSeparator:"",	
			columns: [190,190,190],
			id:'rdFormato',
			binding:true,
			hiddenvalue:'',
			defaultvalue:0,
			allowBlank:true,
			items: [{
				boxLabel: 'Proveedor',
			    name: 'formato',
			    inputValue: '0',
			    listeners:{
			    	'check': function (checkbox, checked){
			    		if(checked){
			    			fromProveedor.show();
			    			fromBeneficiario.hide();
					    }
					}
			    }
			},{
				boxLabel: 'Beneficiario',
				name: 'formato',
				inputValue: '1',
				listeners:{	
					'check': function (checkbox, checked){
						if(checked){
							fromProveedor.hide();
							fromBeneficiario.show();
						}
					}
				}
			},{
				boxLabel: 'Ninguno',
				name: 'formato',
				inputValue: '2',
				checked:true,
				listeners:{	
					'check': function (checkbox, checked){
						if(checked){
							fromBeneficiario.hide();
							fromProveedor.hide();
						}
					}
				}
			}]					
		}]
	});
	
	//--------------------------------------------------------------------------------------------

	var fromOrigen = new Ext.form.FieldSet({
			title:'Origen del Movimiento',
			style: 'position:absolute;left:60px;top:140px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:25px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 100,
							items: [{
									xtype: 'combo',
									fieldLabel: 'Procedencia',
									labelSeparator :'',
									id: 'procedencia',
									store : stOrimov,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
									triggerAction : 'all',
									mode : 'local',
									emptyText:'Todos',
									listWidth:150,
									width:150,				
								}]
							}]
					}]
	})
	
	//--------------------------------------------------------------------------------------------

	var fromImpresion = new Ext.form.FieldSet({
			title:'Tipo de Impresion',
			style: 'position:absolute;left:60px;top:205px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:60px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 100,
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
					}]
	})
	
	//--------------------------------------------------------------------------------------------

	fromProveedor = new Ext.form.FieldSet({ 
			title:'',
			style: 'position:absolute;left:10px;top:270px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 58,
			items:[comcampocatproveedor.fieldsetCatalogo]
	})
	
	//--------------------------------------------------------------------------------------------

	fromBeneficiario = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:270px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 58,
			items:[comcampocatbeneficiario.fieldsetCatalogo]
	})

	//------------------------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(375));
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteComNoCau = new Ext.FormPanel({
		applyTo: 'formReporteComNoCau',
		width:750, //700
		height: 320,
		title: "<H1 align='center'>Compromisos No Causados</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromIntervaloFechas,
		        fromTipo,
		        fromProveedor,
		        fromBeneficiario,
		        fromOrigen,
		        fromImpresion
		        ]
	});	
	fromBeneficiario.hide();
	fromProveedor.hide();
	fromReporteComNoCau.doLayout();
});	

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
		var tipoproben = '-';
		var codproben = '';
		var opcimp = 'P';  
		var formato = "sigesp_spg_rpp_compromisos_no_causados.php";
		var procedencia = 'T';
		if(Ext.getCmp('opcimp').getValue()!=''){
			opcimp=Ext.getCmp('opcimp').getValue(); 
		}
		if(Ext.getCmp('procedencia').getValue()!=''){
			procedencia=Ext.getCmp('procedencia').getValue(); 
		}
		if(Ext.getCmp('rdFormato').items.items[0].checked){
			tipoproben = 'P';
			if(Ext.getCmp('cod_pro').getValue()!=''){
				codproben=Ext.getCmp('cod_pro').getValue();
			}
		}
		if(Ext.getCmp('rdFormato').items.items[1].checked){
			tipoproben = 'B';
			if(Ext.getCmp('ced_bene').getValue()!=''){
				codproben=Ext.getCmp('ced_bene').getValue();
			}
		}
		if(opcimp=='E'){
			formato = "sigesp_spg_rpp_compromisos_no_causados_excel.php";
		}
		var pagina = "reportes/"+formato+"?txtfecdes="+fecdes+"&txtfechas="+fechas+"&tipprovbene="+tipoproben
                    +"&codprovbene="+codproben+"&procedencia="+procedencia;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}
