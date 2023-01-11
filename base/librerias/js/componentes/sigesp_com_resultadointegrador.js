/***********************************************************************************
* @fecha de modificacion: 05/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

Ext.namespace('com.sigesp.vista');
com.sigesp.vista.comResultadoIntegrador = function(options){
	
	//Creando formulario con totales
	this.formResultado = new Ext.FormPanel({
    	frame:true,
    	labelWidth : options.anchoLabel,
    	title: '',
    	bodyStyle:'padding:5px 5px 0',
    	width: 580,
		height: 130,
    	items: [{
			xtype: 'textfield',
			fieldLabel: options.labelTotal,
			labelSeparator: '',
			id: 'total',
			disabled:true,
			width: 45,
			value: options.valorTotal
		},{
			xtype: 'textfield',
			fieldLabel: options.labelProcesada,
			labelSeparator: '',
			id: 'procesada',
			disabled:true,
			width: 45,
			value: options.valorProcesada
		},{
			xtype: 'textfield',
			fieldLabel: options.labelError,
			labelSeparator: '',
			id: 'error',
			disabled:true,
			width: 45,
			value: options.valorError
		}]
	});
	//Fin creando formulario con totales
	
	//creando datastore y columnmodel para la grid de detalles presupuestarios
	var reResultado = Ext.data.Record.create([
	    {name: 'estatus'}, 
	    {name: 'documento'},
	    {name: 'mensaje'}
	]);
	
	var dsResultado =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reResultado)
	});
						
	var cmResultado = new Ext.grid.ColumnModel([
        {header: "<H1 align='center'>Estatus</H1>", width: 15,  align: 'center', sortable: true, dataIndex: 'estatus',renderer:mostrarDisponibleComCmp},
        {header: "<H1 align='center'>Documento</H1>", width: 30, sortable: true, dataIndex: 'documento'},
		{header: "<H1 align='center'>Mensaje<H1>", width: 55, sortable: true, dataIndex: 'mensaje'} 
	]);
	//fin creando datastore y columnmodel para la grid de detalles presupuestarios
	
	//Creando la grid de detalles presupuestarios
	this.gridResultado = new Ext.grid.GridPanel({
		title: "<H1 align='center'>"+options.tituloGrid+"</H1>",
 		width:580,
 		height:300,
 		style : 'margin-top:10px',
 		autoScroll:true,
 		enableColumnHide: false,
 		border:true,
 		ds: dsResultado,
   		cm: cmResultado,
   		stripeRows: true,
  		viewConfig: {forceFit:true,enableTextSelection: true}
	});
	//Fin creando la grid de detalles presupuestarios
	
	this.mostrarMensaje = function() {
		
    }
	
	
	//Evento para mostrar mensaje ampliado
	this.gridResultado.on('cellclick', function(grid, rowIndex, columnIndex, e) {
		var record = grid.getStore().getAt(rowIndex);
		var campo  = grid.getColumnModel().getDataIndex(columnIndex);
		if(campo == 'mensaje') {
			var plMensaje = new Ext.FormPanel({
	    		height: 300,
	    		width: 750,
	    	   	frame: true,
	    		items: [{
	    			layout: "column",
	    			defaults: {border: false},
	    			items: [{
	    				layout: "form",
	    				border: false,
	    				items: [{
	    					xtype: 'textarea',
	    					labelSeparator :'',
	    					style:'font-weight: bold; border:none;background:#f1f1f1',
	    					hideLabel: true,
	    					id: 'mensaje',
	    					width: 745,
	    					height: 295,
	    					readOnly: true,
	    					value: record.get('mensaje')
	    				}]
	    			}]
	    		}]
	    	});
	    	
	    	plMensaje.doLayout();
	    	
	    	//Ventana de mensaje
	    	var venMensaje = new Ext.Window({
	    		title: "<H1 align='center'>Mensaje</H1>",
			    width:755,
	            height:305,
	            modal: true,
	            closable:true,
	            plain: false,
	            items:[plMensaje]
	        });
	    	venMensaje.show();
		}
	});
	//Evento para mostrar mensaje ampliado
	
	//Creando la instacia de la window para la ventana detalle comprobante
	this.venResultado = new Ext.Window({
		title: options.tituloVentana,
		bodyStyle:'padding:5px 5px 0',
		autoScroll:true,
    	width:600,
    	height:500,
    	modal: true,
    	closable:true,
    	plain: false,
		items:[this.formResultado,this.gridResultado]
	});
	//Fin creando la instacia de la window para la ventana detalle comprobante
			

	this.mostrarVentana = function(){
		var objetoData = eval('(' + options.dataDetalle + ')');
		if(objetoData!=''){
			if(objetoData.raiz != null || objetoData.raiz.length!=0){
				this.gridResultado.getStore().loadData(objetoData);
			}
		}
		this.venResultado.show();
	}
}