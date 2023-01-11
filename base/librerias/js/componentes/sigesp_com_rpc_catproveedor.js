/***********************************************************************************
* @Archivo JavaScript que incluye un componentes que construye un campo de texto con 
* un catalgo asociado que llena dicho campo  
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
com.sigesp.vista.comCatalogoProveedor = function(options) {
	this.fnOnAceptar 	   = options.fnOnAceptar;
	this.fnValidarMostrar  = options.fnValidarMostrar;
		
	//funcion que contruye el JSON con los criterios de busqueda para enviar al controlador
	this.buscarDataCatalogo = function() {
		var nuevosparamentros = options.parametros;
		var valorCampo = '';
		var cuentaNoVacio = 0;
		var codpro  = Ext.getCmp('catcodpro'+options.idComponente).getValue();
		var nompro  = Ext.getCmp('catnompro'+options.idComponente).getValue();
		var dirpro  = Ext.getCmp('catdirpro'+options.idComponente).getValue();
		var rifpro  = Ext.getCmp('catrifpro'+options.idComponente).getValue();
		if(codpro!=''){
			cuentaNoVacio++;
		}
		if(nompro!=''){
			cuentaNoVacio++;
		}
		
		if(dirpro!=''){
			cuentaNoVacio++;
		}
		
		if(rifpro!=''){
			cuentaNoVacio++;
		}
		
		nuevosparamentros = nuevosparamentros +",'catcodprov':'"+codpro+"','catnomprov':'"+nompro+"','catdirprov':'"+dirpro+"','catrifprov':'"+rifpro+"'";
		
		if(options.arrtxtfiltro!=undefined){
			for (var i = 0; i < options.arrtxtfiltro.length; i++) {
				nuevosparamentros = nuevosparamentros +",'"+options.arrtxtfiltro[i]+"':'"+Ext.getCmp(options.arrtxtfiltro[i]).getValue()+"'";
			}
		}
		
		nuevosparamentros = nuevosparamentros + "}";
		
		if(options.numFiltroNoVacio!=undefined){
			if(cuentaNoVacio < options.numFiltroNoVacio){
				Ext.MessageBox.show({
	 				title:'Advertencia',
	 				msg:'Debe llenar al menos '+options.numFiltroNoVacio+' campo(s), para realizar una busqueda',
	 				buttons: Ext.Msg.OK,
	 				icon: Ext.MessageBox.WARNING
	 			});
	 			
	 			return false;
			}
		}
		else {
			if(cuentaNoVacio < 1){
				Ext.MessageBox.show({
	 				title:'Advertencia',
	 				msg:'Debe llenar al menos 1 campo, para realizar una busqueda',
	 				buttons: Ext.Msg.OK,
	 				icon: Ext.MessageBox.WARNING
	 			});
	 			
	 			return false;
			}
		}
		
		Ext.MessageBox.show({
			msg: 'Buscando informaci&#243;n',
			title: 'Progreso',
			progressText: 'Buscando informaci&#243;n',
			width:300,
			wait:true,
			waitConfig:{interval:150},	
			animEl: 'mb7'
		});
		
		Ext.Ajax.request({
			url : options.rutacontrolador,
			params : nuevosparamentros,
			method: 'POST',
			success: this.cargarDatosCat.createDelegate(this, arguments, 2)
		});
	}
	//Fin de buscarDataCatalogo
	
	//funcion para filtrar en store del catalogo segun lo que se tipea en un campo
	this.filtrarStore = function(nomfiltro){
		this.dataStoreCatalogo.filter(arguments[0],arguments[2].value,true);
	}
	//Fin filtrarStore
	
	//Instacion del formulario con los criterios de busqueda
	this.formBusquedaCat = new Ext.FormPanel({
		width: 650,
		height: 200,
		frame: true,
		autoScroll:false,
		items: [{
			xtype:"fieldset", 
			title:'Datos del Proveedor',
			style: 'position:absolute;left:10px;top:5px',
			border:true,
			width: 620,
			cls :'fondo',
			height: 180,
			items:[{
				xtype: 'textfield',
				labelSeparator :'',
				fieldLabel: 'C&#243;digo',
				id: 'catcodpro'+options.idComponente,									
				width: 120,
				autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789');"},
				changeCheck: this.filtrarStore.createDelegate(this, ['cod_pro'], 0), 
				initEvents: function(){
					AgregarKeyPress(this);
				}
			},{
     			xtype: 'textfield',
				labelSeparator :'',
				fieldLabel: 'Raz&#243;n social',
				id: 'catnompro'+options.idComponente,
				width: 450,
				changeCheck: this.filtrarStore.createDelegate(this, ['nompro'], 0),
				initEvents: function(){
					AgregarKeyPress(this);
				}	
			},{
				xtype: 'textfield',
				labelSeparator :'',
				fieldLabel: 'Direcci&#243;n',
				id: 'catdirpro'+options.idComponente,
				width: 450,
				changeCheck: this.filtrarStore.createDelegate(this, ['dirpro'], 0), 
				initEvents: function(){
					AgregarKeyPress(this);
				}
			},{
				xtype: 'textfield',
				labelSeparator :'',
				fieldLabel: 'RIF',
				id: 'catrifpro'+options.idComponente,
				width: 120,
				autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15'},
				changeCheck: this.filtrarStore.createDelegate(this, ['rifpro'], 0),
				initEvents: function(){
					AgregarKeyPress(this);
				}
			},{
				xtype: 'button',
				id: 'catbtnBuscar',
				text: 'Buscar',
				style: 'position:absolute;left:520px;top:130px',
				iconCls: 'menubuscar',
				handler: this.buscarDataCatalogo.createDelegate(this)
			}]  
		}]   
	});
	//Fin formBusquedaCat
		
	//Creando la instacia de la grid del catalogo
	this.dataStoreCatalogo =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz', id: "id"},options.reCatalogo)
	});
	                                    						
	var cmProveedor = new Ext.grid.ColumnModel([
		 {header: "<H1 align='center'>C&#243;digo</H1>", width: 15, sortable: true,   dataIndex: 'cod_pro'},
		 {header: "<H1 align='center'>RIF</H1>", width: 20, sortable: true, dataIndex: 'rifpro'},
		 {header: "<H1 align='center'>Raz&#243;n social</H1>", width: 35, sortable: true, dataIndex: 'nompro'},
		 {header: "<H1 align='center'>Direcci&#243;n</H1>", width: 35, sortable: true, dataIndex: 'dirpro'}
	]);
	
	this.gridcatalogo = new Ext.grid.GridPanel({
		width:650,
 		height:500,
 		enableColumnHide: false,
 		tbar: this.formBusquedaCat,
 		autoScroll:true,
 		border:true,
 		ds: this.dataStoreCatalogo,
   		cm: cmProveedor,
   		stripeRows: true,
  		viewConfig: {forceFit:true}
	});
	//Fin Creando la instacia de la grid del catalogo
		
	//Funcion para cerrar la ventana catalogo
	this.cerrarVentana = function(){
		this.dataStoreCatalogo.removeAll();
		Ext.getCmp('catcodpro'+options.idComponente).reset();
		Ext.getCmp('catnompro'+options.idComponente).reset();
		Ext.getCmp('catdirpro'+options.idComponente).reset();
		Ext.getCmp('catrifpro'+options.idComponente).reset();
		this.vencatalogo.hide();
	}
	//Fin cerrarVentana
	
	//Funcion que carga los datos recibidos en el store del catalogo
	this.cargarDatosCat = function (){
		var datos = arguments[0].responseText;
		var objetodata = eval('(' + datos + ')');
		if(objetodata!=''){
			if(objetodata.raiz == null || objetodata.raiz ==''){
				var contenidoMensaje = 'No existen datos para mostrar, o debe refinar los parametros de busqueda';
				if(options.setMensaje){
					contenidoMensaje = options.nuevoMensaje;
				}
				Ext.MessageBox.show({
 					title:'Advertencia',
 					msg: contenidoMensaje,
 					buttons: Ext.Msg.OK,
 					icon: Ext.MessageBox.WARNING
 				});
			}
			else{
				this.dataStoreCatalogo.loadData(objetodata);
				Ext.MessageBox.hide();
			}
		}
	}
	//Fin cargarDatosCat
	
	//Funcion de se encarga de mostrar la ventana del catalgo
	this.mostrarVentana = function(){
		var mostrarOk = true;
		if(options.validarMostrar==1){
			mostrarOk = this.fnValidarMostrar();
		}
		
		if(mostrarOk){
			this.vencatalogo.doLayout();
			this.vencatalogo.show();
		}
		else{
			if(options.msjValidarMostrar!=''){
				Ext.MessageBox.show({
	 				title:'Mensaje',
	 				msg:options.msjValidarMostrar,
	 				buttons: Ext.Msg.OK,
	 				icon: Ext.MessageBox.INFO
	 			});
			}
		}
	}
	//Fin mostrarVentana
	
	//Funcion que se encarga de cargar los datos seleccionados en el catalogo en el destino que se seleccione
	this.setDataCampo = function(){
		var registrocat = this.gridcatalogo.getSelectionModel().getSelected();
		if(registrocat!= undefined){
			if(!options.soloCatalogo){
				this.campo.setValue(registrocat.get(options.campovalue));
				if(options.labelvalue!=''){
					this.etiqueta.setValue(registrocat.get(options.labelvalue));	
				}
			}
			else {
				for (var i = 0; i < options.arrSetCampo.length; i++) {
					Ext.getCmp(options.arrSetCampo[i].campo).setValue(registrocat.get(options.arrSetCampo[i].valor));
				}
			}
			
			if(options.datosocultos==1){
				for (var i = options.camposocultos.length - 1; i >= 0; i--){
					Ext.getCmp(options.camposocultos[i]).setValue(registrocat.get(options.camposocultos[i]));
				}
			}
			
			if(options.datosadicionales == 1){
				for (var i = options.camposoadicionales.length - 1; i >= 0; i--){
					var valorcampo = null;
					switch(options.camposoadicionales[i].tipo) {
						case 'numerico':
		    				valorcampo = formatoNumericoMostrar(registrocat.get(options.camposoadicionales[i].id),2,'.',',','','','-','');	
							break;
						
						case 'fecha':
							if (registrocat.get(options.camposoadicionales[i].id) != "") {
								var fechanoguion = registrocat.get(options.camposoadicionales[i].id).replace('-', '/', 'g');
								var objfecha = new Date(fechanoguion);
								valorcampo = objfecha.format(Date.patterns.fechacorta);
							}
							break;
						
						case 'fechahora':
							if (registrocat.get(options.camposoadicionales[i].id) != "") {
								var fechanoguion = registrocat.get(options.camposoadicionales[i].id).replace('-', '/', 'g');
								var objfecha = new Date(fechanoguion);
								valorcampo = objfecha.format(Date.patterns.fechahoracorta);
							}
							break;
						
						
						case 'cadena':
							valorcampo = registrocat.get(options.camposoadicionales[i].id);
							break;
					}
	
					Ext.getCmp(options.camposoadicionales[i].id).setValue(valorcampo);
				}
			}
			
			if(options.onAceptar){
				this.fnOnAceptar();
			}
			this.cerrarVentana();
		}
		else {
 	  		Ext.MessageBox.show({
 				title:'Mensaje',
 				msg:'Debe seleccionar un registro',
 				buttons: Ext.Msg.OK,
 				icon: Ext.MessageBox.INFO
 			});
 	 	}
	}
	//Fin setDataCampo
	
	//agregadon listener a la grid del catalogo para el evento dobleclick cargue datos
	this.gridcatalogo.on({
		'celldblclick': {
			fn: this.setDataCampo.createDelegate(this)
		}
	});
	//fin agregadon listener a la grid del catalogo para el evento dobleclick cargue datos
	
	//Creando la instacia de la window para la ventana del catalogo
	this.vencatalogo = new Ext.Window({
		title: "<H1 align='center'>Cat&#225;logo de Proveedores</H1>",
		autoScroll:true,
        width:670,
        height:580,
        modal: true,
   		closable:false,
        plain: false,
		items:[this.gridcatalogo],
		buttons: [{
			text:'Aceptar',  
			handler: this.setDataCampo.createDelegate(this)
		},{
			text: 'Salir',
			handler:this.cerrarVentana.createDelegate(this)
		}]
	});
	//Fin creando la instacia de la window para la ventana del catalogo
	
	//Aqui se crea el fieldSet con el campo de proveedor.
	if(!options.soloCatalogo){
		this.codbinding        = false; 
		this.denbinding        = false;
			
		if(options.binding=='C'){//cuando se quiere que el codigo tenga activado el binding
			this.codbinding = true;
		}
		else if(options.binding=='CD'){//cuando se quiere que el codigo y descripcion tenga activado el binding
			this.denbinding=true;
		}
			
		if(options.hiddenvalue != 'undefined' && options.hiddenvalue != ''){
			this.hiddenvalue = options.hiddenvalue;
		}
		
		if(options.defaultvalue != 'undefined' && options.defaultvalue != ''){
			this.defaultvalue = options.defaultvalue;
		}
		
		this.campo = new Ext.form.TextField({
			xtype: 'textfield',
			fieldLabel: options.tittxt,
			labelSeparator :'',
			id: options.idtxt,
			width: options.anchotext,
			readOnly: true,
			binding:this.codbinding,
			hiddenvalue:options.hiddenvalue,
			defaultvalue:options.defaultvalue,
			allowBlank:options.allowblank
		});
		
		this.boton = new Ext.Button({
			xtype:'button',
			iconCls: 'menubuscar',
			id:options.idboton,
			style:'padding-left:5px;',
			handler:this.mostrarVentana.createDelegate(this)
		});
						
		this.etiqueta = new Ext.form.TextField({
			xtype: 'textfield',
			labelSeparator :'',
			hideLabel: true,
			style:'padding-left:10px;border:none;background:#f1f1f1',
			id: options.idlabel,
			disabled:true,  
			width: options.anchoetiqueta,
			binding:this.denbinding
		});
			
		this.fieldsetCatalogo = new Ext.form.FieldSet({
			height:50,
			border:false,
			id:options.idfieldset,
			width:options.anchofieldset,
			style:options.posicion,
	    	items:[{
	    		layout : "column",
				defaults : {border : false},
				items : [{
					layout : "form",
			        border : false,
					labelWidth: options.anchoetiquetatext,
			        columnWidth : options.anchocoltext,
			        items : [this.campo]
				},{
					layout : "form",
				   	border : false,
					columnWidth :0.05,
					items : [this.boton]
				},{
					layout : "form",
			        border : false,
			        columnWidth : options.anchocoletiqueta,
			        items : [this.etiqueta]
				}]
			}]
		});
	}
}