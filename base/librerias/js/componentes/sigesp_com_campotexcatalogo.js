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
var copiadatastorecatalogo = '';

com.sigesp.vista.comCampoCatalogo = function(options){
	
	this.dataStoreCatalogo = options.datosgridcat;
	this.fnOnAceptar 	   = options.fnOnAceptar;
	this.fnValidarMostrar  = options.fnValidarMostrar;
	this.codbinding        = false; 
	this.denbinding        = false;
	this.nombre        = '';
	if(options.nametxt != 'undefined')
	{
		this.nombre = options.nametxt;
	}
	else
	{
		this.nombre = options.idtxt;
	}
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
	
	function validarAnyMatch(valor){
		var anyMatch=true;
		if(valor!=undefined){
			anyMatch = valor;
		}
				
		return anyMatch
	}
	var digitos = "'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!�;: '";
	var digitosPermitidos = '"return keyRestrict(event,'+digitos+');"'		
	//Creando el Json para la configuracion de los items del formulario de busqueda
	var cadenafiltro="[";
	for (var i = 0; i < options.arrfiltro.length; i++)
        {
            cadenatipo='';
            if(options.arrfiltro[i].tipo == 'datefield')
            {
                cadenatipo="xtype:'datefield', format: 'd/m/Y', value: '"+options.arrfiltro[i].defecto+"', ";
            }
            if(i==options.arrfiltro.length-1)
            {
                            cadenafiltro =  cadenafiltro + "{"+cadenatipo+"fieldLabel:'"+options.arrfiltro[i].etiqueta+"',id:'"+options.arrfiltro[i].id+"',"+
                                                            "autoCreate: {tag: 'input', type: 'text', maxlength: '"+validarLongitud(options.arrfiltro[i].longitud)+"', onkeypress: "+digitosPermitidos+"},"+
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
            }
            else
            {
                            cadenafiltro =  cadenafiltro + "{"+cadenatipo+"fieldLabel:'"+options.arrfiltro[i].etiqueta+"',id:'"+options.arrfiltro[i].id+"',"+
                                                            "autoCreate: {tag: 'input', type: 'text', maxlength: '"+validarLongitud(options.arrfiltro[i].longitud)+"', onkeypress: "+digitosPermitidos+"},"+
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
	function validarLongitud(valor)
        {
		if(valor!=undefined)
                {
			return valor;
		}
		else
                {
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
	 		enableColumnHide: false,
	 		tbar: this.formBusquedaCat,
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
			Ext.getCmp(options.arrfiltro[i].id).reset();
		}
		this.vencatalogo.hide();
	}
	
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
				copiadatastorecatalogo = options.datosgridcat;
				this.dataStoreCatalogo.loadData(objetodata);
				copiadatastorecatalogo.loadData(objetodata);
				Ext.MessageBox.hide();
			}
			
		}
	}
	
	this.buscarDataCatalogo = function(){
		var nuevosparamentros = options.parametros;
		var valorCampo = '';
		var cuentaNoVacio = 0;
		for (var i = 0; i < options.arrfiltro.length; i++)
                {
        	        if(options.arrfiltro[i].tipo == 'datefield')
                        {
                            valorCampo = Ext.getCmp(options.arrfiltro[i].id).getValue().format('Y/m/d');
                        }
                        else
                        {
                            valorCampo = Ext.getCmp(options.arrfiltro[i].id).getValue();
                        }
			if(valorCampo != '')
                        {
				cuentaNoVacio++;			
			}
			if(options.arrfiltro[i].requerido != undefined)
                        {
				if(options.arrfiltro[i].requerido && valorCampo =='')
                                {
					Ext.MessageBox.show({
	 					title:'Advertencia',
	 					msg:'Debe indicar el campo '+options.arrfiltro[i].etiqueta+', para la busqueda',
	 					buttons: Ext.Msg.OK,
	 					icon: Ext.MessageBox.WARNING
	 				});
	 				
	 				return false;
				}
				else
                                {
					nuevosparamentros = nuevosparamentros +",'"+options.arrfiltro[i].id+"':'"+valorCampo+"'";	
				}
			}
			else
                        {
				nuevosparamentros = nuevosparamentros +",'"+options.arrfiltro[i].id+"':'"+valorCampo+"'";
			}
       		
		}
		
		if(options.arrtxtfiltro!=undefined)
                {
			for (var i = 0; i < options.arrtxtfiltro.length; i++)
                        {
				nuevosparamentros = nuevosparamentros +",'"+options.arrtxtfiltro[i]+"':'"+Ext.getCmp(options.arrtxtfiltro[i]).getValue()+"'";
			}
		}
		
		nuevosparamentros = nuevosparamentros + "}";
		if(options.numFiltroNoVacio!=undefined)
                {
			if(cuentaNoVacio < options.numFiltroNoVacio)
                        {
				Ext.MessageBox.show({
	 				title:'Advertencia',
	 				msg:'Debe llenar al menos '+options.numFiltroNoVacio+' campo(s), para realizar una busqueda',
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
			
			this.dataStoreCatalogo.removeAll();
			//copiadatastorecatalogo = '';
			for(var i = 0; i < options.arrfiltro.length; i++){
				Ext.getCmp(options.arrfiltro[i].id).reset();
			}
			if(options.onAceptar)
                        {
                            this.fnOnAceptar();
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
	
	//funcion para destruir la ventana del campo catalgo
	this.destruirVentana  = function(){
		this.vencatalogo.destroy();
	}
	//fin funcion para destruir la ventana del campo catalgo
	
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
					name: this.nombre,
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
							items : [{layout : "form",
					        					border : false,
												labelWidth: options.anchoetiquetatext,
					        					columnWidth : options.anchocoltext,
					        					items : [this.campo]
						   			},{layout : "form",
						   						border : false,
												columnWidth :0.05,
												items : [this.boton]
						   			},{layout : "form",
					        					border : false,
					        					columnWidth : options.anchocoletiqueta,
					        					items : [this.etiqueta]
						   			}]
							}]
					});

}//Fin componente campo catalogo

