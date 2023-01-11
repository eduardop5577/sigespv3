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
com.sigesp.vista.comCatalogoCtaBancos = function(options) {
	this.fnOnAceptar 	   = options.fnOnAceptar;
	this.fnValidarMostrar  = options.fnValidarMostrar;
		
	//funcion que contruye el JSON con los criterios de busqueda para enviar al controlador
	this.buscarDataCatalogo = function() {
		var nuevosparamentros = options.parametros;
		var valorCampo = '';
		var cuentaNoVacio = 0;
		var codctaban  = Ext.getCmp('catcodctaban'+options.idComponente).getValue();
		var nomctaban  = Ext.getCmp('catnomctaban'+options.idComponente).getValue();
		if(codctaban!=''){
			cuentaNoVacio++;
		}
		if(nomctaban!=''){
			cuentaNoVacio++;
		}
		
		if(options.arrtxtfiltro!=undefined){
			for (var i = 0; i < options.arrtxtfiltro.length; i++) {
				nuevosparamentros = nuevosparamentros +",'"+options.arrtxtfiltro[i]+"':'"+Ext.getCmp(options.arrtxtfiltro[i]).getValue()+"'";
			}
		}
		
		nuevosparamentros = nuevosparamentros + "}";
		
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
		height: 110,
		frame: true,
		autoScroll:false,
		items: [{
			xtype:"fieldset", 
			title:'Cat�logo de Cuentas Bancarias',
			style: 'position:absolute;left:10px;top:5px',
			border:true,
			width: 470,
			cls :'fondo',
			height: 100,
			items:[{
				xtype: 'textfield',
				labelSeparator :'',
				fieldLabel: 'C�digo',
				id: 'catcodctaban'+options.idComponente,									
				width: 120,
				autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789');"},
				changeCheck: this.filtrarStore.createDelegate(this, ['ctaban'], 0), 
				initEvents: function(){
					AgregarKeyPress(this);
				}
			},{
     			xtype: 'textfield',
				labelSeparator :'',
				fieldLabel: 'Denominaci�n',
				id: 'catnomctaban'+options.idComponente,
				width: 330,
				changeCheck: this.filtrarStore.createDelegate(this, ['dencta'], 0),
				initEvents: function(){
					AgregarKeyPress(this);
				}	
			}]  
		}]   
	});
	//Fin formBusquedaCat
		
	//Creando la instacia de la grid del catalogo
	this.dataStoreCatalogo =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz', id: "id"},options.reCatalogo)
	});
	                                    						
	var cmCtaBanco = new Ext.grid.ColumnModel([
		 {header: "<H1 align='center'>C&#243;digo</H1>", width: 35, sortable: true, dataIndex: 'ctaban'},
		 {header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 30, sortable: true, dataIndex: 'dencta'},
		 {header: "<H1 align='center'>Tipo</H1>", width: 15, sortable: true, dataIndex: 'nomtipcta' },
		 {header: "<H1 align='center'>Contable</H1>", width: 20, sortable: true, dataIndex: 'sc_cuenta'},
		 {header: "<H1 align='center'>Descrpci&#243;n</H1>", width: 25, sortable: true, dataIndex: 'denominacion'},
		 {header: "<H1 align='center'>Apertura</H1>", width: 15, sortable: true, dataIndex: 'fecapr'}
	]);
	
	this.gridcatalogo = new Ext.grid.GridPanel({
		width:680,
 		height:400,
 		enableColumnHide: false,
 		tbar: this.formBusquedaCat,
 		autoScroll:true,
 		border:true,
 		ds: this.dataStoreCatalogo,
   		cm: cmCtaBanco,
   		stripeRows: true,
  		viewConfig: {forceFit:true}
	});
	//Fin Creando la instacia de la grid del catalogo
		
	//Funcion para cerrar la ventana catalogo
	this.cerrarVentana = function(){
		this.dataStoreCatalogo.removeAll();
		Ext.getCmp('catcodctaban'+options.idComponente).reset();
		Ext.getCmp('catnomctaban'+options.idComponente).reset();
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
		
		if(mostrarOk)
		{
			switch(options.tipbus)
			{
				case 'L':
					Ext.Ajax.request({
					url : options.rutacontrolador,
					params : options.parametros,
					method: 'POST',
					success: this.buscarDataCatalogo.createDelegate(this)
					});
				break;	
			}
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
		title: "<H1 align='center'>Cat�logo Cuentas Bancarias</H1>",
		autoScroll:true,
        width:700,
        height:400,
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