/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta = true;
var fromFiltros = null;
var fromOrdenar=null;
Ext.onReady(function(){

	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(700/2));
	fromDocContab = new Ext.FormPanel({
		applyTo: 'formulario',
		width: 750,
		height: 370,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:65px;',
		title: "<H1 align='center'>Documentos Contabilizados</H1>",
		frame: true,
		autoScroll:true,
		items: [fromFiltros,fromOrdenar]
	});
	fromDocContab.doLayout();
});
	
	//-------------------------------------------------------------------------------------------------------------------------	
	//Creando el campo de contabilizado
	
	var reUsuario = Ext.data.Record.create([
	    {name: 'codusu'},
	    {name: 'nomusu'}
	]);

	var dsUsuario =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},reUsuario)
	});

	var cmUsuario = new Ext.grid.ColumnModel([
	    {header: "Codigo", width: 20, sortable: true, dataIndex: 'codusu'},
	    {header: "Nombre", width: 40, sortable: true, dataIndex: 'nomusu'}
	]);

	//componente campocatalogo para contabilizado por
	comcampocatContabilizadoPor = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Catálogo de Usuarios</H1>",
		anchoformbus: 450,
		altoformbus:100,
		anchogrid: 450,
		altogrid: 330,
		anchoven: 480,
		altoven: 400,
		anchofieldset: 650,
		datosgridcat: dsUsuario,
		colmodelocat: cmUsuario,
		rutacontrolador:'../../controlador/sep/sigesp_ctr_sep_reporte.php',
		parametros: "ObjSon={'operacion': 'buscarUsuarios'}",
		arrfiltro:[{etiqueta:'Codigo',id:'codiusua',valor:'codusu'},
		           {etiqueta:'Nombre',id:'denousua',valor:'nombre'}],
        posicion:'position:absolute;left:10px;top:10px',
        tittxt:'Contabilizado por',
        idtxt:'codusu',
        campovalue:'codusu',
        anchoetiquetatext:100,
        labelSeparator :'',
        anchotext:130,
        labelWidth:100,
        anchocoltext:0.38,
        idlabel:'nombre',
        labelvalue:'nombre',
        anchocoletiqueta:0.50,
        anchoetiqueta:0,
        tipbus:'L',
        binding:'C',
        hiddenvalue:'',
        defaultvalue:'---',
        allowblank:true
	});
	//fin componente para el campo contabilizado por
	//-------------------------------------------------------------------------------------------------------------------------	


	//creando Simpledatastore y columnmodel para documentos
	var arregloDocumentos = [
	                         ['comprobante','Numero Documento'],
	                         ['total','Monto'],
	                         ['monto','Fecha'],
	                         ['procede','Modulo']
	                         ]; // Arreglo que contiene los Documentos que se pueden controlar

	var dataStoreDocumentos = new Ext.data.SimpleStore({
		fields: ['codigo', 'documento'],
		data : arregloDocumentos // Se asocian los documentos disponibles
	});
	var arregloAscDesc = [
	                      ['ASC','Ascendente'],   
	                      ['DESC','Descendente']
	                      ]; // Arreglo que contiene los Documentos que se pueden controlar

	var dataStoreAscDesc = new Ext.data.SimpleStore({
		fields: ['codigo', 'documento'],
		data : arregloAscDesc // Se asocian los documentos disponibles


	});

	//creando Simpledatastore y columnmodel para modulo origen
	var arregloModuloOrigen = [                            
	                           ['SEP','Solicitud Ejecucion Prespupuestaria'],
	                           ['SOC','Ordenes de Compra'],
	                           ['CXP','Cuentas por Pagar'],
	                           ['SCB','Caja y Bancos'],
	                           ['SNO','Nomina'],
	                           ['SPG','Presupuesto Gasto'],
	                           ['SPI','Presupuesto Ingreso'],
	                           ['SCG','Contabilidad Patrimonial']]; // Arreglo que contiene los Documentos que se pueden controlar

	var dataStoreModuloOrigen = new Ext.data.SimpleStore({
		fields: ['codigo', 'denominacion'],
		data : arregloModuloOrigen // Se asocian los documentos disponibles
	});
//	--------------------------------------------------------------------------------------------
	//Creacion del formulario
	var Xpos = ((screen.width/2)-(300));
	fromFiltros = new  Ext.form.FieldSet({ 
			title:'Filtros',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 700,
			cls :'fondo',
			height: 210,
			items:[ comcampocatContabilizadoPor.fieldsetCatalogo,
			        {
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:330px;top:60px',
					items: [{
							layout: "form",
							border: false,					
							labelWidth: 70,
							items: [{
									xtype:"datefield",
									fieldLabel:"Hasta",
									labelSeparator :'',
									name:"fecha",
									allowBlank:false,
									width:100,
									id:"fechaDochasta",
									columnWidth: 40,
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
			        },
			        {
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:20px;top:60px',
					items: [{
							layout: "form",
							border: false,
							labelWidth:100,
							items: [{
									xtype:"datefield",
									fieldLabel:"Desde",
									labelSeparator :'',
									name:"fecha",
									allowBlank:false,
									width:100,
									columnWidth: 40,
									id:"feDocDesde",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
			        },
			        {
					layout: "column",
					border: false,
					defaults: {border: false},
					style: 'position:absolute;left:20px;top:95px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 100,			
							items: [{
									xtype:"combo",
									store: dataStoreModuloOrigen,
									displayField:'denominacion',
									valueField:'codigo',
									labelSeparator :'',
									id:"tipconben",
									defaultvalue:'NSD',
									emptyText:'----Seleccione----',
									typeAhead: true,
									mode: 'local',
									triggerAction: 'all',				
									fieldLabel:'Modulo origen',
									listWidth:250,
									editable:false,
									width:250
								}]
						}]
			        },
			        {
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:20px;top:130px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 100,
							items: [{
									xtype: 'textarea',
									labelSeparator :'',
									fieldLabel: 'Concepto',
									id: 'concepto',
									width: 450,
									height: 50,
								}]
							}]
			        }]
		}); 
	
	fromOrdenar = new  Ext.form.FieldSet({ 
			title:"Ordenar  por",
			style: 'position:absolute;left:10px;top:230px',
			border:true,
			cls: 'fondo',
			height:70,
			width:700,
			columnWidth:300,
			items:[{
					layout: "column",
					defaults: {border: false},
	//				style: 'position:absolute;left:80px;top:40px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,	
							style:'padding-left:50px',
							columnWidth:0.5,
							items: [{
									xtype:"combo",
									store: arregloDocumentos,
									displayField:'documentos',
									id:'comDoc',
									//style: 'position:absolute;left:5px;top:40px',
									width:150,
									listWidth: 180,
									labelSeparator :'',
									emptyText:'----Seleccione----',
									typeAhead: true,
									selectOnFocus:true,
									editable:false,
									hideLabel:true,
									mode:'local',
									triggerAction:'all'			        			    				  
								}] 
							},
							{
							layout: "form",
							border: false,
							columnWidth:0.5,
							labelWidth: 300,
							//style:'padding-left:600px',
							items: [{
									xtype:"combo",
									store: dataStoreAscDesc,
									displayField:'documento',
									id:'comAscDesc',
							    	valueField:'codigo',
							    	emptyText:'----Seleccione----',
									width:150,
									labelSeparator :'',
									listWidth: 180, 
									hideLabel:true,
									typeAhead: true,
									editable:false,
									selectOnFocus:true,
									mode:'local',		    		
									triggerAction:'all'
								}]
							}]				
			}]	
});

function irCancelar(){
	limpiarFormulario(plcontabilizado);
}

function irImprimir(){
	var JSONObject = {
			'codusu'      : Ext.getCmp('codusu').getValue(),
			'fecdes'      : Ext.getCmp('feDocDesde').getValue(),   
			'fechas'      : Ext.getCmp('fechaDochasta').getValue(),
			'modulo'      : Ext.getCmp('tipconben').getValue(),
			'orden'       : Ext.getCmp('comAscDesc').getValue(),
			'concepto'    : Ext.getCmp('concepto').getValue()
	}
	var ObjSon = JSON.stringify(JSONObject);    
	window.open("reportes/sigesp_mis_rpp_documentoscontabilizados.php?ObjSon="+ObjSon,"menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
}
function irBuscar(){

}