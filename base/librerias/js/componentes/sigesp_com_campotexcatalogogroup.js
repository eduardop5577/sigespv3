/***********************************************************************************
* @Archivo JavaScript que incluye la implementacion del componente campo catalogo  
* con la variante de que la grid del catalgo permite agrupar los datos 
* @fecha de modificacion: 05/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

/***********************************************************************************
* @Archivo JavaScript que incluye un componentes que construye un campo de texto con 
* un catalgo asociado que llena dicho campo  
* @fecha de creacion: 21/08/2009
* @autor: Ing. Gerardo Cordero
************************************************************************************
* @fecha modificacion:
* @descripcion:
* @autor:
***********************************************************************************/
Ext.namespace('com.sigesp.vista');
var copiadatastorecatalogo = '';

com.sigesp.vista.comCampoCatalogoAgrupado = function(options){
	
	this.dataStoreCatalogo = options.datosgridcat;
	this.fnOnAceptar 	   = options.fnOnAceptar;
	this.fnValidarMostrar  = options.fnValidarMostrar;
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
		
	//Creando el Json para la configuracion de los items del formulario de busqueda
	var cadenafiltro="[";
	for (var i = 0; i < options.arrfiltro.length; i++) {
       	if(i==options.arrfiltro.length-1){
			cadenafiltro =  cadenafiltro + "{fieldLabel:'"+options.arrfiltro[i].etiqueta+"',id:'"+options.arrfiltro[i].id+"'," +
							"autoCreate: {tag: 'input', type: 'text', maxlength: '"+validarLongitud(options.arrfiltro[i].longitud)+"'},"+
							"width: "+validarAncho(options.arrfiltro[i].ancho)+","+
							"changeCheck: function(){"+
							"var valor = this.getValue();"+
							"copiadatastorecatalogo.filter('"+options.arrfiltro[i].valor+"',valor,true,false);"+
							"if(String(valor) !== String(this.startValue)){"+
								"this.fireEvent('change', this, valor, this.startValue);"+
							"}"+ 
							"},"+								 
							"initEvents : function(){"+
								"AgregarKeyPress(this);"+
							"}"+              
    						"}";
		}else{
			cadenafiltro =  cadenafiltro + "{fieldLabel:'"+options.arrfiltro[i].etiqueta+"',id:'"+options.arrfiltro[i].id+"',"+
							"autoCreate: {tag: 'input', type: 'text', maxlength: '"+validarLongitud(options.arrfiltro[i].longitud)+"'}," +
							"width: "+validarAncho(options.arrfiltro[i].ancho)+","+
							"changeCheck: function(){"+
							"var valor = this.getValue();"+
							"copiadatastorecatalogo.filter('"+options.arrfiltro[i].valor+"',valor,true,false);"+
							"if(String(valor) !== String(this.startValue)){"+
								"this.fireEvent('change', this, valor, this.startValue);"+
							"}"+ 
							"},"+							 
							"initEvents : function(){"+
								"AgregarKeyPress(this);"+
							"}"+               
     						"},";
		}
	}
	cadenafiltro=  cadenafiltro + "]";
	var objetofiltro = Ext.util.JSON.decode(cadenafiltro);
	//Fin creando el Json para la configuracion de los items del formulario de busqueda	
	
	//Inicio de la funcion que retorna la longitud del textfield
	function validarLongitud(valor){
		if(valor!=undefined){
			return valor;
		}
		else{
			return '150';
		}
	}
	//Fin de la funcion que retorna la longitud del textfield
	
	//Inicio de la funcion que retorna el ancho del textfield
	function validarAncho(valor){
		if(valor!=undefined){
			return valor;
		}
		else{
			return '200';
		}
	}
	//Fin de la funcion que retorna el ancho del textfield
	
	//Creando el fieldset del formBusquedaCat
	this.fieldcatalogo = new Ext.form.FieldSet({
		xtype:"fieldset", 
		title:'B&#250;squeda',
		width: options.anchoformbus-25,
		height:options.altoformbus-15,
		border:true,
		defaultType: 'textfield',
		style: 'position:absolute;left:5px;top:5px',
    	defaults: {width: 230, labelSeparator:''},
		cls:'fondo',
		items: objetofiltro
	})
	//Fin del fieldset del formBusquedaCat
	
	//Creando formulario de busqueda del catalogo
	this.formBusquedaCat = new Ext.FormPanel({
			labelWidth:options.ancholabelform,
        	frame:true,
        	width: options.anchoformbus,
			height:options.altoformbus+10,
			items:[this.fieldcatalogo]
	});
	//Fin creando formulario de busqueda del catalogo
	
	//Creando la instacia de la grid del catalogo
	this.gridcatalogo = new Ext.grid.GridPanel({
	 		width:options.anchogrid,
	 		height:options.altogrid,
	 		enableColumnHide: false,
	 		autoScroll:true,
     		border:true,
			autoExpandColumn:options.colexpandible,
			tbar: this.formBusquedaCat,
			ds: this.dataStoreCatalogo,
       		cm: options.colmodelocat,
       		stripeRows: true,
       		view: new Ext.grid.GroupingView({startCollapsed:true,forceFit: true,groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "'+options.labelitem+'" : "'+options.labelitem+'"]})'})
	});
	//Fin Creando la instacia de la grid del catalogo
	
	//Eventos de la ventana catalogo
	this.cerrarVentana = function(){
		this.dataStoreCatalogo.removeAll();
		copiadatastorecatalogo = '';
		for(var i = 0; i < options.arrfiltro.length; i++){
			Ext.getCmp(options.arrfiltro[i].id).setValue("");
		}
		this.vencatalogo.hide();
	}
	
	this.cargarDatosCat = function (){
		var datos = arguments[0].responseText;
		var objetodata = eval('(' + datos + ')');
		if(objetodata!=''){
			if(objetodata.raiz == null){
				Ext.MessageBox.alert('Informaci&#243;n','No se encontraron datos')
			}
			else{
				copiadatastorecatalogo = options.datosgridcat;
				this.dataStoreCatalogo.loadData(objetodata);
				copiadatastorecatalogo.loadData(objetodata);
				Ext.MessageBox.hide();
			}
		}
	}
	
	this.buscarDataCatalogo = function(){
		var nuevosparamentros = options.parametros;
		for (var i = 0; i < options.arrfiltro.length; i++) {
       		if(i==options.arrfiltro.length-1){
				nuevosparamentros = nuevosparamentros +",'"+options.arrfiltro[i].id+"':'"+Ext.getCmp(options.arrfiltro[i].id).getValue()+"'}";
			}else{
				nuevosparamentros = nuevosparamentros +",'"+options.arrfiltro[i].id+"':'"+Ext.getCmp(options.arrfiltro[i].id).getValue()+"'";
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
	
	this.mostrarVentana = function(){
		var mostrarOk = true;
		if(options.validarMostrar==1){
			mostrarOk = this.fnValidarMostrar();
		}
		
		if(mostrarOk){
			switch(options.tipbus) {
				case 'L':
		    		Ext.Ajax.request({
						url : options.rutacontrolador,
						params : options.parametros,
						method: 'POST',
						success: this.cargarDatosCat.createDelegate(this, arguments, 2)
					});
					break;
				
				case 'LF':
					var nuevosparamentros = options.parametros;
					for (var i = 0; i < options.arrtxtfiltro.length; i++) {
	       				if(i==options.arrtxtfiltro.length-1){
							nuevosparamentros = nuevosparamentros +",'"+options.arrtxtfiltro[i]+"':'"+Ext.getCmp(options.arrtxtfiltro[i]).getValue()+"'}";
						}else{
							nuevosparamentros = nuevosparamentros +",'"+options.arrtxtfiltro[i]+"':'"+Ext.getCmp(options.arrtxtfiltro[i]).getValue()+"'";
						}
					}
					Ext.Ajax.request({
						url : options.rutacontrolador,
						params : nuevosparamentros,
						method: 'POST',
						success: this.cargarDatosCat.createDelegate(this, arguments, 2)
					});
					break;
				
				case 'P':
					if (this.formBusquedaCat.getComponent('botbusqueda') == null) {
						var botbusqueda = new Ext.Button({
							id: 'botbusqueda',
							iconCls: 'menubuscar',
							text: 'Buscar',
							style: 'position:absolute;left:'+(options.anchoformbus-120)+'px;top:'+(options.altoformbus-65)+'px',
							handler: this.buscarDataCatalogo.createDelegate(this)
						});
						this.fieldcatalogo.add(botbusqueda)
					}
					break;
			}
			this.vencatalogo.doLayout();
			this.vencatalogo.show();
		}
		else{
			Ext.MessageBox.show({
 				title:'Mensaje',
 				msg:options.msjValidarMostrar,
 				buttons: Ext.Msg.OK,
 				icon: Ext.MessageBox.INFO
 			});
		}
		
	}
	
	this.setDataCampo = function(){
		var registrocat = this.gridcatalogo.getSelectionModel().getSelected();
		if(registrocat!= undefined){
			this.campo.setValue(registrocat.get(options.campovalue));
			if(options.labelvalue!=''){
				this.etiqueta.setValue(registrocat.get(options.labelvalue));	
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
			this.dataStoreCatalogo.removeAll();
			copiadatastorecatalogo = '';
			for(var i = 0; i < options.arrfiltro.length; i++){
				Ext.getCmp(options.arrfiltro[i].id).setValue("");
			}
			this.vencatalogo.hide();
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
	//Fin de los eventos de la ventana catalogo
	
	//agregadon listener a la grid del catalogo para que cuando de dobleclick sobre el registro este se pase al formulario
	this.gridcatalogo.on({
		'celldblclick': {
			fn: this.setDataCampo.createDelegate(this)
		}
	});
	//fin agregadon listener a la grid del catalogo para que cuando de dobleclick sobre el registro este se pase al formulario
	
	//Creando la instacia de la window para la ventana del catalogo
	this.vencatalogo = new Ext.Window({
    		title: options.titvencat,
			autoScroll:true,
        	width:options.anchoven,
        	height:options.altoven,
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
					id: 'xbusqueda',
					iconCls: 'menubuscar',
					style:'padding-left:5px;',
					handler:this.mostrarVentana.createDelegate(this)
					});
					
					
					
	this.etiqueta = new Ext.form.TextField({
					xtype:'textfield',
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
					width:options.anchofieldset,
					style:options.posicion,
			    	items:[{
							layout : "column",
					        defaults : {border : false},
							items : [{layout : "form",
					        					border : false,
												labelWidth: options.anchoetiquetatext,
					        					columnWidth : options.anchocoltext,
					        					items : [this.campo]
						   			},{layout : "form",
					        					border : false,
												columnWidth : 0.05,
					        					items : [this.boton]
						   			},{layout : "form",
					        					border : false,
												columnWidth : options.anchocoletiqueta,
					        					items : [this.etiqueta]
						   			}]
							}]
					});

}//Fin componente campo catalogo

