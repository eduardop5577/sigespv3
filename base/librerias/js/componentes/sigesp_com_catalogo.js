/***********************************************************************************
* @Archivo JavaScript que incluye un componentes que construye un catalogo   
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
var copiadatastorecatalogo = '';

com.sigesp.vista.comCatalogo = function(options){
	
	this.dataStoreCatalogo = options.datosgridcat;
	this.fnOnAceptar = options.fnOnAceptar;
	
	function validarAnyMatch(valor){
		var anyMatch=true;
		if(valor!=undefined){
			anyMatch = valor;
		}
				
		return anyMatch
	}
	
	//Creando el Json para la configuracion de los items del formulario de busqueda
	var cadenafiltro="[";
	for (var i = 0; i < options.arrfiltro.length; i++) {
       	if(i==options.arrfiltro.length-1){
			cadenafiltro =  cadenafiltro + "{fieldLabel:'"+options.arrfiltro[i].etiqueta+"',id:'"+options.arrfiltro[i].id+"',"+
							"autoCreate: {tag: 'input', type: 'text', maxlength: '"+validarLongitud(options.arrfiltro[i].longitud)+"'},"+
							"width: "+validarAncho(options.arrfiltro[i].ancho)+","+				
							"changeCheck: function(){"+
							"var valor = this.getValue();"+
							"copiadatastorecatalogo.filter('"+options.arrfiltro[i].valor+"',valor,"+validarAnyMatch(options.arrfiltro[i].anyMatch)+",false);"+
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
							"autoCreate: {tag: 'input', type: 'text', maxlength: '"+validarLongitud(options.arrfiltro[i].longitud)+"'},"+
							"width: "+validarAncho(options.arrfiltro[i].ancho)+","+				
							"changeCheck: function(){"+
							"var valor = this.getValue();"+
							"copiadatastorecatalogo.filter('"+options.arrfiltro[i].valor+"',valor,"+validarAnyMatch(options.arrfiltro[i].anyMatch)+",false);"+
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
	
	cadenafiltro = cadenafiltro + "]";
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
		width: options.anchoformbus-22,
		height:options.altoformbus-7,
		border:true,
		defaultType: 'textfield',
		style: 'position:absolute;left:5px;top:0px',
    	defaults: {width: 230, labelSeparator:''},
		cls:'fondo',
		items: objetofiltro
	})
	//Fin del fieldset del formBusquedaCat
	
	//Creando formulario de busqueda del catalogo
	this.formBusquedaCat = new Ext.FormPanel({
		labelWidth: 80, 
		frame:true,
        width: options.anchoformbus,
		height: options.altoformbus+10,
		items: [this.fieldcatalogo]
	});
	//Fin creando formulario de busqueda del catalogo
		
	//Creando la instacia de la grid del catalogo
	this.gridcatalogo = new Ext.grid.GridPanel({
	 	width:options.anchogrid,
	 	height:options.altogrid,
	 	tbar: this.formBusquedaCat,
	 	enableColumnHide: false,
	 	autoScroll:true,
     	border:true,
     	ds: this.dataStoreCatalogo,
       	cm: options.colmodelocat,
       	stripeRows: true,
      	viewConfig: {forceFit:true}
	});
	//Fin Creando la instacia de la grid del catalogo
	
	//Eventos de la ventana catalogo
	this.cerrarVentana = function(){
		this.dataStoreCatalogo.removeAll();
		copiadatastorecatalogo = '';
		for(var i = 0; i < options.arrfiltro.length; i++){
			Ext.getCmp(options.arrfiltro[i].id).setValue("");
		}
		this.gridcatalogo.destroy();
		this.vencatalogo.destroy();
	}
	
	this.cargarDatosCat = function (){
		var datos = arguments[0].responseText;
		var objetodata = eval('(' + datos + ')');
		if(objetodata!=''){
			if(objetodata.raiz == null || objetodata.raiz ==''){
				var contenidoMensaje = 'No existen datos para mostrar';
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
       		nuevosparamentros = nuevosparamentros +",'"+options.arrfiltro[i].id+"':'"+Ext.getCmp(options.arrfiltro[i].id).getValue()+"'";
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
		
	this.mostrarVentana = function(){
		
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
						style: 'position:absolute;left:'+(options.anchoformbus-120)+'px;top:'+(options.altoformbus-55)+'px',
						handler: this.buscarDataCatalogo.createDelegate(this)
					});
					this.fieldcatalogo.add(botbusqueda)
				}
				break;
		}
		this.vencatalogo.doLayout();
		this.vencatalogo.show();
	}
	
	this.setDataForm = function(componente,registro){
		if (componente.items != null) {
			arritem = componente.items;
			arritem.each(function(subcomponente){
				var valor = null;
				switch (subcomponente.getXType()) {
					case 'radiogroup':
						if (typeof(registro.get(subcomponente.getId())) != 'undefined') {
							valor = limpiarCadenaRegistro(registro.get(subcomponente.getId()));
							for (var j = 0; j < subcomponente.items.length; j++) {
								if (valor == subcomponente.items.items[j].inputValue) {
									subcomponente.items.items[j].setValue(true);
									break;
								}
							}
						}
						break;
							
					case 'checkbox':
						if (typeof(registro.get(subcomponente.getId())) != 'undefined') {
							valor = limpiarCadenaRegistro(registro.get(subcomponente.getId()));
							if (valor == subcomponente.inputValue) {
								subcomponente.setValue(true);
							}
						}
						break;
					
					case 'checkboxgroup':
						if (typeof(registro.get(subcomponente.getId())) != 'undefined') {
							valor = limpiarCadenaRegistro(registro.get(subcomponente.getId()));
							for (var j = 0; j < subcomponente.items.length; j++) {
								if (valor == subcomponente.items.items[j].inputValue) {
									subcomponente.items.items[j].setValue(true);
									break;
								}
							}
						}
						break;
							
				case 'hidden':
					//al quitarle el comentario a esta linea da error en los catalogos que tienen campos ocultos.
					//alert(registro.get(subcomponente.getId()));
						if (typeof(registro.get(subcomponente.getId())) != 'undefined') {
							subcomponente.setValue(registro.get(subcomponente.getId()));
							
						}
						break;
							
					case 'textfield':
						if (typeof(registro.get(subcomponente.getId())) != 'undefined') {
							valor = limpiarCadenaRegistro(registro.get(subcomponente.getId()));
							
							if (subcomponente.formatonumerico) {
								subcomponente.setValue(formatoNumericoMostrar(valor, 2, '.', ',', '', '', '-', ''));
							}
							else {
								
								subcomponente.setValue(valor);
							}
						}
						break;
							
					case 'textarea':
						if (typeof(registro.get(subcomponente.getId())) != 'undefined') {
							valor = limpiarCadenaRegistro(registro.get(subcomponente.getId()));
							subcomponente.setValue(valor);
						}
				 		break;
							
					case 'combo':
						if (typeof(registro.get(subcomponente.getId())) != 'undefined') {
							subcomponente.setValue(registro.get(subcomponente.getId()));
						}
						break;
							
					case 'datefield':
						if (typeof(registro.get(subcomponente.getId())) != 'undefined') {
							subcomponente.setValue(registro.get(subcomponente.getId()));
						}
						break;
							
					case 'numberfield':
						if (typeof(registro.get(subcomponente.getId())) != 'undefined') {
							subcomponente.setValue(registro.get(subcomponente.getId()));
						}
				 		break;
				 	
				 	default:
						setDataFrom(subcomponente,registro);
						break;
				}
			});
		}
	}
	
	this.setData = function(){
		var registrocat = this.gridcatalogo.getSelectionModel().getSelected();
		
		if(options.setdatastyle=='T'){
			Ext.getCmp(options.idTextfield).setValue(registrocat.get(options.setCampo));
		}
		else if(options.setdatastyle=='F'){
			var fncSetDataFrom = this.setDataForm.createDelegate(this);
			fncSetDataFrom(options.formulario,registrocat);
			Actualizar=true;
		}
		else if(options.setdatastyle=='G'){
			for (var i = options.camposoadicionales.length - 1; i >= 0; i--) {
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
				
				options.registroGrid.set(options.camposoadicionales[i].id,valorcampo);
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
		
		if(options.datosocultos==1){
			for (var i = options.camposocultos.length - 1; i >= 0; i--){
				Ext.getCmp(options.camposocultos[i]).setValue(registrocat.get(options.camposocultos[i]));
			}
		}
		
		if(options.onAceptar){
			this.fnOnAceptar();
		}
		
		if(options.fieldSetEst != null){
			options.fieldSetEst.setComCatalogoDen(registrocat);
		}
		
		this.dataStoreCatalogo.removeAll();
		copiadatastorecatalogo = '';
		for(var i = 0; i < options.arrfiltro.length; i++){
			Ext.getCmp(options.arrfiltro[i].id).setValue("");
		}
		this.cerrarVentana();
	}
	//Fin de los eventos de la ventana catalogo
	
	//agregadon listener a la grid del catalogo para que cuando de dobleclick sobre el registro este se pase al formulario
	this.gridcatalogo.on({
		'celldblclick': {
			fn: this.setData.createDelegate(this)
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
			        	handler: this.setData.createDelegate(this)
			       	},{
			      		text: 'Salir',
			        	handler:this.cerrarVentana.createDelegate(this)
                  	}]
      	});
	//Fin creando la instacia de la window para la ventana del catalogo
	
}//Fin componente campo catalogo

