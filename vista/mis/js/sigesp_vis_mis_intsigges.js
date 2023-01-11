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

var plInterfaz = null;
var gridCuenta = null;

barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	    
    //creando datastore y columnmodel para el catalogo fondos
	var reConfigurancion = Ext.data.Record.create([
		{name: 'numcon'},
		{name: 'descon'}
	]);
	
	var dsConfiguracion =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reConfigurancion)
	});
						
	var cmConfigurancion = new Ext.grid.ColumnModel([
	    {header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'numcon'},
		{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'descon'}
	]);
	//fin creando datastore y columnmodel para el catalogo fondos
	
	//componente campocatalogo para el campo fondos
	var comtcConfiguracion = new com.sigesp.vista.comCampoCatalogo({
		titvencat: 'Catalogo Configuraci&#243;n',
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 350,
		anchoven: 500,
		altoven: 420,
		anchofieldset:850,
		datosgridcat: dsConfiguracion,
		colmodelocat: cmConfigurancion,
		rutacontrolador:'../../controlador/mis/sigesp_ctr_mis_intsigges.php',
		parametros: "ObjSon={'operacion': 'OBT_CON'",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'nuconf',valor:'numcon'},
				   {etiqueta:'Denominaci&#243;n',id:'deconf',valor:'descon'}],
		posicion:'position:absolute;left:5px;top:5px',
		tittxt:'Configuraci&#243;n',
		idtxt:'numcon',
		campovalue:'numcon',
		anchoetiquetatext:130,
		anchotext:130,
		anchocoltext:0.40,
		idlabel:'descon',
		labelvalue:'descon',
		anchocoletiqueta:0.53,
		anchoetiqueta:250,
		tipbus:'P',
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:true
	});
	//fin componente campocatalogo para el campo fondos
	
	function formatoFechaComprobante(fecha){
		if (fecha != '') {
			return fecha.format(Date.patterns.fechacorta);
		}
	}
	
	//registro y store de la grid de comprobante
	var reComprobante = Ext.data.Record.create([
	    {name: 'numcom'},
	    {name: 'feccom'},
	    {name: 'descom'},
	    {name: 'codemp'},
	    {name: 'procom'},
	    {name: 'codban'},
	    {name: 'ctaban'},
	    {name: 'numcomalt'},
	    {name: 'feccomalt'}
	]);
	
	var dsComprobante =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reComprobante)
	});
	
	var cmComprobante = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),                                          
	    {header: "Comprobante", width: 25, sortable: true,   dataIndex: 'numcom'},
	    {header: "Fecha", width: 25, sortable: true, dataIndex: 'feccom'},
	    {header: "Descripci&#243;n", width: 40, sortable: true, dataIndex: 'descom'},
	    {header: "Numero alterno", width: 25, sortable: true,   dataIndex: 'numcomalt'},
	    {header: "Fecha alterna", width: 25, sortable: true, dataIndex: 'feccomalt', renderer:formatoFechaComprobante}
	]);
	
	//Grid de comprobante
	gridComprobante = new Ext.grid.GridPanel({
		title: "<H1 align='center'>Comprobantes Contables</H1>",
		width:800,
	    height:250,
	    frame:true,
	    style: 'position:absolute;left:15px;top:100px',
	    ds: dsComprobante,
       	cm: cmComprobante,
       	sm: new Ext.grid.CheckboxSelectionModel({}),
		viewConfig: {forceFit:true},
        columnLines: true
    });
	
	function ventanaComprobante(regComprobante) {
		
		var plComprobante = new Ext.FormPanel({
    		height: 275,
    		width: 730,
    	   	frame: true,
    	   	items: [{
    			layout: "column",
    			defaults: {border: false},
    			style: 'position:absolute;left:15px;top:15px',
    			items: [{
    				layout: "form",
    				border: false,
    				labelWidth: 150,
    				items: [{
    					xtype:'textfield',
    					fieldLabel:'Numero Comprobante',
    					id:'numconalt',
    					width:150,
    					autoCreate: {tag: 'input', type: 'text', size: '80', autocomplete: 'off', maxlength: '15'},
    					labelSeparator:'',
    				}]
    			}]
    		},{
    			layout: "column",
    			defaults: {border: false},
    			style: 'position:absolute;left:15px;top:55px',
    			items: [{
    				layout: "form",
    				border: false,
    				labelWidth: 150,
    				items: [{
    					xtype:"datefield",
        				fieldLabel:"Nueva Fecha",
    					labelSeparator :'',
        				width:100,
    					id:"feccomalt",
    					readOnly: true
    				}]
    			}]
    		}]
    	});
		
    	//Ventana de nuevo dato comprobantes
    	var venComprobante = new Ext.Window({
    		title: "<H1 align='center'>Actualizar Comprobante</H1>",
		    width:500,
            height:250,
            modal: true,
            closable:false,
            plain: false,
            items:[plComprobante],
            buttons: [{
            	text:'Aceptar',  
                handler: function() {
                	regComprobante.set('numcomalt',Ext.getCmp('numconalt').getValue());
                	regComprobante.set('feccomalt',Ext.getCmp('feccomalt').getValue());
                	venComprobante.destroy();                      
                }
            },{
            	text: 'Salir',
                handler: function() {
                	venComprobante.destroy();
                }
            }]
    	});
    	venComprobante.show();
    }
	
	gridComprobante.on('cellclick', function(grid, rowIndex, columnIndex, e) {
		var record = grid.getStore().getAt(rowIndex);
		var campo  = grid.getColumnModel().getDataIndex(columnIndex);
		if(campo == 'numcomalt' || campo == 'feccomalt') {
			ventanaComprobante(record);
		}
	});
	
	var myJSONObject = {"operacion":"VAL_USU"};
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/mis/sigesp_ctr_mis_intsigges.php',
		params: parametros,
		timeout: 9999999999,
		method: 'POST',
		success: function ( result, request ) {
			var respuesta = result.responseText;
			if(respuesta == '0'){
				gridComprobante.getColumnModel().setHidden(4, true);
				gridComprobante.getColumnModel().setHidden(5, true);
			}
		},
		failure: function ( result, request){ 
			Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
		}
	});
	
	//PANEL PRINCIPAL CONFIGURACION INTERFAZ SIGESP - GESTOR
	plInterfaz = new Ext.FormPanel({
		title: "<H1 align='center'>Interfaz SIGESP - GESTOR</H1>",
		style: 'position:relative;top:35px;left:100px', 
		height: 460,
		width: 850,
	   	applyTo:'formIntSigGes',
	   	frame: true,
	   	items:[comtcConfiguracion.fieldsetCatalogo,
	   	{
	   		layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:50px',
			items: [{
				layout: "form",
				border: false,
				labelWidth: 130,
				items: [{
					xtype:"datefield",
    				fieldLabel:"Fecha",
					labelSeparator :'',
    				width:100,
					id:"feccom",
					readOnly: true,
					binding:true,
					hiddenvalue:'',
					defaultvalue:'',
					allowBlank:false,
				}]
			}]
		},
	   	gridComprobante]
	});
});

function irCancelar() {
	limpiarFormulario(plInterfaz);
	gridComprobante.store.removeAll();
}

function irBuscar() {
	var dt = new Date(Ext.getCmp('feccom').getValue());
	var fecha = dt.format('Y-m-d'); 
	var myJSONObject = {"operacion":"BUS_COM",
						"numcon":Ext.getCmp('numcon').getValue(),
						"feccom":fecha};
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/mis/sigesp_ctr_mis_intsigges.php',
		params: parametros,
		timeout: 9999999999,
		method: 'POST',
		success: function ( result, request ) {
			var datos = result.responseText;
			var objData = eval('(' + datos + ')');
			if(objData.raiz == null || objData.raiz ==''){
				Ext.MessageBox.show({
					title:'Advertencia',
					msg:'No existen datos para mostrar',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.WARNING
 				});
			}
			else{
				gridComprobante.store.loadData(objData);
			}
		},
		failure: function ( result, request){ 
			Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
		}
	});
	
}

function irProcesar() {
	var arrComprobante = gridComprobante.getSelectionModel().getSelections();
	var cadenaJson  = "{'operacion':'PRO_COM','numcon':'"+Ext.getCmp('numcon').getValue()+"','arrComprobante':[";  
	for ( var int = 0; int < arrComprobante.length; int++) {
		if (int == 0) {
			cadenaJson += "{'numcom':'"+arrComprobante[int].get('numcom')+"','codemp':'"+arrComprobante[int].get('codemp')+"'," +
					      " 'procom':'"+arrComprobante[int].get('procom')+"','codban':'"+arrComprobante[int].get('codban')+"'," +
					      " 'ctaban':'"+arrComprobante[int].get('ctaban')+"','numcomalt':'"+arrComprobante[int].get('numcomalt')+"'," +
					      " 'feccomalt':'"+arrComprobante[int].get('feccomalt')+"'}";
		}
		else {
			cadenaJson += ",{'numcom':'"+arrComprobante[int].get('numcom')+"','codemp':'"+arrComprobante[int].get('codemp')+"'," +
		     			  "  'procom':'"+arrComprobante[int].get('procom')+"','codban':'"+arrComprobante[int].get('codban')+"'," +
		     			  "  'ctaban':'"+arrComprobante[int].get('ctaban')+"','numcomalt':'"+arrComprobante[int].get('numcomalt')+"'," +
					      "  'feccomalt':'"+arrComprobante[int].get('feccomalt')+"'}";
		}
	}
	cadenaJson  += "]}";
	var parametros ='ObjSon='+cadenaJson;
	Ext.Ajax.request({
		url: '../../controlador/mis/sigesp_ctr_mis_intsigges.php',
		params: parametros,
		timeout: 9999999999,
		method: 'POST',
		success: function ( result, request ) {
			var respuesta = result.responseText;
			var datajson = eval('(' + respuesta + ')');
			if(datajson.raiz.valido==true){
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Los datos fueron procesados correctamente',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});
				irCancelar();
			}
			else {
				Ext.Msg.show({
					title:'Mensaje',
					msg: datajson.raiz.mensaje,
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		},
		failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
		}
	});
	
	
}