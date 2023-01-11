/***********************************************************************************
* @Archivo JavaScript que genera un fieldset con las estructuras presupuestarias 
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
 
//Objeto que construye el fieldset para seleccionar una estructura presupuestaria
com.sigesp.vista.comFieldSetEstructuraPresupuesto =  function(options){
	this.fnOnAceptar 	   = options.fnOnAceptar;
	Ext.override(Ext.form.Field, {
		afterRender: Ext.form.Field.prototype.afterRender.createSequence(function(){
			if(this.qtip){
				var target = this.getTipTarget();
				if(typeof this.qtip == 'object'){
					Ext.QuickTips.register(Ext.apply({
						  target: target
					}, this.qtip));
				} else {
					target.dom.qtip = this.qtip;
				}
			}
		}),
		getTipTarget: function(){
			return this.el;
		}
	});
	this.operacion="";                  // Variable que controla la operacion para la carga de datos en el DataStore de los Catalogos
	this.widthColumnaCampo    = 0;      // Ancho de la Columna que contiene el Codigo de la Estructura
	this.widthColumnaBoton    = 0;      // Ancho de la Columna que contiene el Boton de Busqueda
	this.widthColumnaEtiqueta = 0;      // Ancho de la Columna que contiene la Etiqueta que describe la Estructura
	this.ocultarDenominacion  = false;  // Variable que controla si se oculta o no la denominacion de la Estrucutura
	
	this.obtenerHeightFieldSet=function() // Funcion que determina la altura del Fielset en función del número de niveles
	{
		var height = 120;
		
		switch(parseInt(empresa['numniv'])) {
			case 1: 
				height=80;
				break;
		
			case 2: 
				height=120;
				break;
		
			case 3: 
				height=170;
				break;
		
			case 4: 
				height=200;
				break;
		
			case 5: 
				height=240;
				break;
		
		}
		return height;
	}
	
	this.obtenerWidthFieldSet=function() // Funcion que determina el ancho del FieldSet en función si se muestra o no la denominación
	{
		var width = 500;
		if(!options.mostrarDenominacion)
		{
			width = 450;
			this.widthColumnaCampo    = 0.92; 
			this.widthColumnaBoton    = 0.08;
			this.widthColumnaEtiqueta = 0;
			this.ocultarDenominacion  = true;
		}
		else
		{
			width = 850;
			this.widthColumnaCampo    = 0.55; 
			this.widthColumnaBoton    = 0.05;
			this.widthColumnaEtiqueta = 0.40;
		}
		
		return width;
	}
	
	this.altura  = this.obtenerHeightFieldSet(); // Altura del FieldSet
	
	this.anchura = this.obtenerWidthFieldSet(); // Anchura del FieldSet
	
	this.mostrarEstatus=function(est){
		
		if (est=='P'){
				return 'Proyecto';
		}else if (est=='A'){
				return 'Acci&#243;n Centralizada';	
		}else if (est=='-'){
				return 'POR DEFECTO';	
		}
	}

	this.mostrarNumDigNiv1=function(estructura)
	{
	 var formatoEstructura="";
	 formatoEstructura = estructura.substr(-empresa['loncodestpro1'])
	 return formatoEstructura;
	}

	this.mostrarNumDigNiv2=function(estructura)
	{
	 var formatoEstructura="";
	 formatoEstructura = estructura.substr(-empresa['loncodestpro2'])
	 return formatoEstructura;
	}

	this.mostrarNumDigNiv3=function(estructura)
	{
	 var formatoEstructura="";
	 formatoEstructura = estructura.substr(-empresa['loncodestpro3'])
	 return formatoEstructura;
	}

	this.mostrarNumDigNiv4=function(estructura)
	{
	 var formatoEstructura="";
	 formatoEstructura = estructura.substr(-empresa['loncodestpro4'])
	 return formatoEstructura;
	}

	this.mostrarNumDigNiv5=function(estructura)
	{
	 var formatoEstructura="";
	 formatoEstructura = estructura.substr(-empresa['loncodestpro5'])
	 return formatoEstructura;
	}
	
	
	this.catalogoEstructuraNivel1=function(){
		this.crear_grid_catalogoestructura('nivel1');				   
	    ventana = new Ext.Window({
	    	title: 'Cat&#225;logo de '+empresa['nomestpro1'],
			autoScroll:true,
	        width:800,
	        height:475,
	        modal: true,
	        closable:false,
	        plain: false,
	        items:[this.gridestructuranivel1],
	        buttons: [{
						text:'Aceptar',  
				        handler: this.setDataEstructuraNivel1.createDelegate(this)
				       },
				       {
				      	text: 'Salir',
				        handler: this.cerrarVentanaEstructuraNivel1.createDelegate(this)
	                  }]
	      });
	      ventana.show();
	}
	
	this.catalogoEstructuraNivel2=function(){
		if((this.fieldSetEstPre.findById('codest'+options.idtxt+'0').getValue() ==""))
		{
			this.mensajeValidacionNivel(1) //aqui no es
		}
		else
		{	
			this.crear_grid_catalogoestructura('nivel2');				   
			ventana = new Ext.Window({
	    	title: 'Cat&#225;logo de '+empresa['nomestpro2'],
			autoScroll:true,
	        width:800,
	        height:475,
	        modal: true,
	        closable:false,
	        plain: false,
	        items:[this.gridestructuranivel2],
	        buttons: [{
						text:'Aceptar',  
				        handler: this.setDataEstructuraNivel2.createDelegate(this)
				       },
				       {
				      	text: 'Salir',
				        handler: this.cerrarVentanaEstructuraNivel2.createDelegate(this)
	                  }]
	      });
	      ventana.show();
		}
	}
	
	this.catalogoEstructuraNivel3=function(){
		if((this.fieldSetEstPre.findById('codest'+options.idtxt+'1').getValue() ==""))
		{
			this.mensajeValidacionNivel(2) //aqui no es
		}
		else
		{	
			this.crear_grid_catalogoestructura('nivel3');				   
			ventana = new Ext.Window({
	    	title: 'Cat&#225;logo de '+empresa['nomestpro3'],
			autoScroll:true,
	        width:800,
	        height:475,
	        modal: true,
	        closable:false,
	        plain: false,
	        items:[this.gridestructuranivel3],
	        buttons: [{
						text:'Aceptar',  
				        handler: this.setDataEstructuraNivel3.createDelegate(this)
				       },
				       {
				      	text: 'Salir',
				        handler: this.cerrarVentanaEstructuraNivel3.createDelegate(this)
	                  }]
	      });
	      ventana.show();
		}
	}
	
	this.catalogoEstructuraNivel4=function(){
		if((this.fieldSetEstPre.findById('codest'+options.idtxt+'2').getValue() ==""))
		{
			this.mensajeValidacionNivel(3) //este no es
		}
		else
		{	
			this.crear_grid_catalogoestructura('nivel4');				   
			ventana = new Ext.Window({
	    	title: 'Cat&#225;logo de '+empresa['nomestpro4'],
			autoScroll:true,
	        width:800,
	        height:475,
	        modal: true,
	        closable:false,
	        plain: false,
	        items:[this.gridestructuranivel4],
	        buttons: [{
						text:'Aceptar',  
				        handler: this.setDataEstructuraNivel4.createDelegate(this)
				       },
				       {
				      	text: 'Salir',
				        handler: this.cerrarVentanaEstructuraNivel4.createDelegate(this)
	                  }]
	      });
	      ventana.show();
		}
	}
	
	this.catalogoEstructuraNivel5=function(){
		if((this.fieldSetEstPre.findById('codest'+options.idtxt+'3').getValue() ==""))
		{
			this.mensajeValidacionNivel(4)
		}
		else{
			this.crear_grid_catalogoestructura('nivel5');				   
	    	ventana = new Ext.Window({
		    	title: 'Cat&#225;logo de '+empresa['nomestpro5'],
				autoScroll:true,
		        width:800,
		        height:475,
		        modal: true,
		        closable:false,
		        plain: false,
		        items:[this.gridestructuranivel5],
		        buttons: [{
		        	text:'Aceptar',  
					handler: this.setDataEstructuraNivel5.createDelegate(this)
				},{
					text: 'Salir',
					handler: this.cerrarVentanaEstructuraNivel5.createDelegate(this)
		        }]
	      	});
	      	ventana.show();
		}
	}
	
	this.setDataEstructuraNivelN=function()
	{
		estnivelN = this.gridestructuranivelN.getSelectionModel().getSelected();
		if(estnivelN != null)
		{
			for (var i = parseInt(empresa['numniv']) - 1 ; i >= 0; i--){
				var estructura = "";
				switch(i)
				{
					case 4: estructura=this.mostrarNumDigNiv5(estnivelN.get('codestpro'+(i+1)));
					break;
					
					case 3: estructura=this.mostrarNumDigNiv4(estnivelN.get('codestpro'+(i+1)));
					break;
					
					case 2: estructura=this.mostrarNumDigNiv3(estnivelN.get('codestpro'+(i+1)));
					break;
					
					case 1: estructura=this.mostrarNumDigNiv2(estnivelN.get('codestpro'+(i+1)));
					break;
					
					case 0: estructura=this.mostrarNumDigNiv1(estnivelN.get('codestpro'+(i+1)));
					break;
				}
				
				this.fieldSetEstPre.findById('codest'+options.idtxt+i).setValue(estructura);
				if(this.fieldSetEstPre.findById('denest'+options.idtxt+i) != null)
				{
					this.fieldSetEstPre.findById('denest'+options.idtxt+i).setText(estnivelN.get('denestpro'+(i+1)))
				}
			};
			this.fieldSetEstPre.findById('estcla'+options.idtxt).setValue(estnivelN.get('estcla'));
			this.gridestructuranivelN.destroy();
			ventana.destroy();
			if(options.onAceptar){
				this.fnOnAceptar();
			}
			if((options.cargarCuentas)!=undefined){
				this.obtenerCuentasApertura();
			}
		}
		else
		{
			Ext.Msg.show({
			   	title:'Mensaje',
			   	msg: 'No ha seleccionado ninguna estructura, verifique por favor',
			   	buttons: Ext.Msg.OK,
			   	animEl: 'elId',
			   	icon: Ext.MessageBox.ERROR,
			   	closable:false
				});
		}
	}
	
	this.catalogoEstructuraNivelN=function(){
		if((this.fieldSetEstPre.findById('codest'+options.idtxt+'0').getValue()!="")&&(this.fieldSetEstPre.findById('codest'+options.idtxt+(parseInt(empresa['numniv'])-1)).getValue()=="")){
			var funcion = new Array();
			funcion[0]=this.catalogoEstructuraNivel1.createDelegate(this);
			funcion[1]=this.catalogoEstructuraNivel2.createDelegate(this);
			funcion[2]=this.catalogoEstructuraNivel3.createDelegate(this);
			funcion[3]=this.catalogoEstructuraNivel4.createDelegate(this);
			funcion[4]=this.catalogoEstructuraNivel5.createDelegate(this);
			funcion[parseInt(empresa['numniv'])-1]();
		}else{
			//var fncreargrid = this.crear_grid_catalogoestructura.createDelegate(this);
			//fncreargrid('nivelN');
			this.crear_grid_catalogoestructura('nivelN');
	    	ventana = new Ext.Window({
	    	title: 'Cat&#225;logo de Estructuras Presupuestarias',
			autoScroll:true,
	        width:800,
	        height:475,
	        modal: true,
	        closable:false,
	        plain: false,
	        items:[this.gridestructuranivelN],
	        buttons: [{
						text:'Aceptar',  
				        handler: this.setDataEstructuraNivelN.createDelegate(this)
				       },
				       {
				      	text: 'Salir',
				        handler: this.cerrarVentanaEstructuraNivelN.createDelegate(this)
	                  }]
	      	});
	      	ventana.show();
		}
	}
	
	this.obtenerFieldSetEstructura=function() {
		var fieldset = null;
   		switch(parseInt(empresa['numniv'])) {
   			case 1 :
   				fieldset =new Ext.form.FieldSet({
					width: this.anchura,
					height: this.altura,
					title: options.titform,
					style: options.estilo,
					cls :'fondo',
					autoScroll:true,
					items: [{
						xtype: 'hidden',
						name: 'estcla'+options.idtxt,
						id: 'estcla'+options.idtxt
					},{//NIVEL 1
						layout : "column", 
						defaults : {border : false, labelWidth: 220}, 
					   	items : [{
					   		layout : "form", 
							border : false,
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro1'], 
								name: 'codigo'+options.idtxt+'0', 
								id: 'codest'+options.idtxt+'0',
								style:"text-align:right",
								readOnly:true, 
								width: 160,
								qtip:empresa['nomestpro1']
							}]
						},{
							columnWidth : this.widthColumnaBoton, 
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'0',
								handler:this.catalogoEstructuraNivel1.createDelegate(this)
							}]
					   	},{
					   		columnWidth : this.widthColumnaEtiqueta, 
					   		bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'0', 
								id: "denest"+options.idtxt+'0',
								hidden: this.ocultarDenominacion 
							}] 
					   }]
					}]
				});
  				break;	
  			
			case 2 :
				fieldset =new Ext.form.FieldSet({
					width: this.anchura,
					height: this.altura,
					title: options.titform,
					style: options.estilo,
					cls :'fondo',
					autoScroll:true,
					items: [{
						xtype: 'hidden',
						name: 'estcla'+options.idtxt,
						id: 'estcla'+options.idtxt
					},{//NIVEL 1
						layout : "column", 
						defaults : {border : false, labelWidth: 220}, 
					   	items : [{
					   		layout : "form", 
							border : false,
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro1'], 
								name: 'codigo'+options.idtxt+'0', 
								id: 'codest'+options.idtxt+'0',
								style:"text-align:right",
								readOnly:true, 
								width: 160,
								qtip:empresa['nomestpro1']
							}]
						},{
							columnWidth : this.widthColumnaBoton, 
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'0',
								handler:this.catalogoEstructuraNivel1.createDelegate(this)
							}]
					   	},{
					   		columnWidth : this.widthColumnaEtiqueta, 
					   		bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'0', 
								id: "denest"+options.idtxt+'0',
								hidden: this.ocultarDenominacion 
							}] 
					   }]
					},{//NIVEL 2
						layout : "column", 
						defaults : {border : false, labelWidth:220},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro2'], 
								name: 'codigo'+options.idtxt+'1', 
								id: 'codest'+options.idtxt+'1',
								style:"text-align:right",
								readOnly:true, 
								width: 160
							}]
						},{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'1',
								handler:this.catalogoEstructuraNivel2.createDelegate(this)
							}]
						},{
							layout : "form", 
							border : false,    
							columnWidth : this.widthColumnaEtiqueta, 
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'1', 
								id: "denest"+options.idtxt+'1',
								width:200,
								hidden:this.ocultarDenominacion 
							}] 
						}]
					}]
				});
				break;
				
			case 3 :
				fieldset =new Ext.form.FieldSet({
					width: this.anchura,
					height: this.altura,
					title: options.titform,
					style: options.estilo,
					cls :'fondo',
					autoScroll:true,
					items: [{
						xtype: 'hidden',
						name: 'estcla'+options.idtxt,
						id: 'estcla'+options.idtxt
					},{//NIVEL 1
						layout : "column", 
						defaults : {border : false, labelWidth: 220}, 
					   	items : [{
					   		layout : "form", 
							border : false,
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro1'], 
								name: 'codigo'+options.idtxt+'0', 
								id: 'codest'+options.idtxt+'0',
								style:"text-align:right",
								readOnly:true, 
								width: 160,
								qtip:empresa['nomestpro1']
							}]
						},{
							columnWidth : this.widthColumnaBoton, 
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'0',
								handler:this.catalogoEstructuraNivel1.createDelegate(this)
							}]
					   	},{
					   		columnWidth : this.widthColumnaEtiqueta, 
					   		bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'0', 
								id: "denest"+options.idtxt+'0',
								hidden: this.ocultarDenominacion 
							}] 
					   }]
					},{//NIVEL 2
						layout : "column", 
						defaults : {border : false, labelWidth:220},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro2'], 
								name: 'codigo'+options.idtxt+'1', 
								id: 'codest'+options.idtxt+'1',
								style:"text-align:right",
								readOnly:true, 
								width: 160 
							}]
						},{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'1',
								handler:this.catalogoEstructuraNivel2.createDelegate(this)
							}]
						},{
							layout : "form", 
							border : false,    
							columnWidth : this.widthColumnaEtiqueta, 
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'1', 
								id: "denest"+options.idtxt+'1',
								width:200,
								hidden:this.ocultarDenominacion 
							}] 
						}]
					},{//NIVEL 3
						layout : "column", 
						defaults : {border : false, labelWidth: 220},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro3'], 
								name: 'codigo'+options.idtxt+'2', 
								id: 'codest'+options.idtxt+'2',
								style:"text-align:right",
								readOnly:true, 
								width: 160 
							}]
						},{
							columnWidth : this.widthColumnaBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'2',
								handler:this.catalogoEstructuraNivelN.createDelegate(this)
							}]
						},{
							columnWidth : this.widthColumnaEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'2', 
								id: "denest"+options.idtxt+'2', 
								hidden:this.ocultarDenominacion
							}] 
						}]
				 	}]
				});
  				break;
  			
  			case 4 : 
		  		fieldset =new Ext.form.FieldSet({
					width: this.anchura,
					height: this.altura,
					title: options.titform,
					style: options.estilo,
					cls :'fondo',
					autoScroll:true,
					items: [{
						xtype: 'hidden',
						name: 'estcla'+options.idtxt,
						id: 'estcla'+options.idtxt
					},{//NIVEL 1
						layout : "column", 
						defaults : {border : false, labelWidth: 220}, 
					   	items : [{
					   		layout : "form", 
							border : false,
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro1'], 
								name: 'codigo'+options.idtxt+'0', 
								id: 'codest'+options.idtxt+'0', 
								readOnly:true,
								style:"text-align:right",
								width: 160,
								qtip:empresa['nomestpro1']
							}]
						},{
							columnWidth : this.widthColumnaBoton, 
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'0',
								handler:this.catalogoEstructuraNivel1.createDelegate(this)
							}]
					   	},{
					   		columnWidth : this.widthColumnaEtiqueta, 
					   		bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'0', 
								id: "denest"+options.idtxt+'0',
								hidden: this.ocultarDenominacion 
							}] 
					   }]
					},{//NIVEL 2
						layout : "column", 
						defaults : {border : false, labelWidth:220},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro2'], 
								name: 'codigo'+options.idtxt+'1', 
								id: 'codest'+options.idtxt+'1',
								style:"text-align:right",
								readOnly:true, 
								width: 160 
							}]
						},{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'1',
								handler:this.catalogoEstructuraNivel2.createDelegate(this)
							}]
						},{
							layout : "form", 
							border : false,    
							columnWidth : this.widthColumnaEtiqueta, 
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'1', 
								id: "denest"+options.idtxt+'1',
								width:200,
								hidden:this.ocultarDenominacion 
							}] 
						}]
					},{//NIVEL 3
						layout : "column", 
						defaults : {border : false, labelWidth: 220},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro3'], 
								name: 'codigo'+options.idtxt+'2', 
								id: 'codest'+options.idtxt+'2',
								style:"text-align:right",
								readOnly:true, 
								width: 160 
							}]
						},{
							columnWidth : this.widthColumnaBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'2',
								handler:this.catalogoEstructuraNivel3.createDelegate(this) //N
							}]
						},{
							columnWidth : this.widthColumnaEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'2', 
								id: "denest"+options.idtxt+'2', 
								hidden:this.ocultarDenominacion
							}] 
						}]
				 	},{//NIVEL 4
				 		layout : "column", 
						defaults : {border : false,labelWidth: 220}, 
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro4'], 
								name: 'codigo'+options.idtxt+'3', 
								id: 'codest'+options.idtxt+'3', 
								readOnly:true, 
								style:"text-align:right", 
								width: 160 
							}]
						},{
							columnWidth : this.widthColumnaBoton,
							bodyStyle:'padding:15px 0px 0px 0px', 
							items : [{
								xtype: "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'3',
								handler:this.catalogoEstructuraNivel4.createDelegate(this)
							}]
						},{
							columnWidth : this.widthColumnaEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype:  "label",
								name: "denon"+options.idtxt+'3', 
								id: "denest"+options.idtxt+'3', 
								hidden:this.ocultarDenominacion
							}] 
						}]
					}]
				});
				break;
			
			case 5 :
				fieldset =new Ext.form.FieldSet({
					width: this.anchura,
					height: this.altura,
					title: options.titform,
					style: options.estilo,
					cls :'fondo',
					autoScroll:true,
					items: [{
						xtype: 'hidden',
						name: 'estcla'+options.idtxt,
						id: 'estcla'+options.idtxt
					},{//NIVEL 1
						layout : "column", 
						defaults : {border : false, labelWidth: 220}, 
					   	items : [{
					   		layout : "form", 
							border : false,
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro1'], 
								name: 'codigo'+options.idtxt+'0', 
								id: 'codest'+options.idtxt+'0', 
								readOnly:true,
								style:"text-align:right",
								width: 160,
								qtip:empresa['nomestpro1']
							}]
						},{
							columnWidth : this.widthColumnaBoton, 
							bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'0',
								handler:this.catalogoEstructuraNivel1.createDelegate(this)
							}]
					   	},{
					   		columnWidth : this.widthColumnaEtiqueta, 
					   		bodyStyle:'padding:10px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'0', 
								id: "denest"+options.idtxt+'0',
								hidden: this.ocultarDenominacion 
							}] 
					   }]
					},{//NIVEL 2
						layout : "column", 
						defaults : {border : false, labelWidth:220},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro2'], 
								name: 'codigo'+options.idtxt+'1', 
								id: 'codest'+options.idtxt+'1',
								style:"text-align:right",
								readOnly:true, 
								width: 160 
							}]
						},{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar",
								id:'btnest'+options.idtxt+'1',
								handler:this.catalogoEstructuraNivel2.createDelegate(this)
							}]
						},{
							layout : "form", 
							border : false,    
							columnWidth : this.widthColumnaEtiqueta, 
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'1', 
								id: "denest"+options.idtxt+'1',
								width:200,
								hidden:this.ocultarDenominacion 
							}] 
						}]
					},{//NIVEL 3
						layout : "column", 
						defaults : {border : false, labelWidth: 220},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro3'], 
								name: 'codigo'+options.idtxt+'2', 
								id: 'codest'+options.idtxt+'2',
								style:"text-align:right",
								readOnly:true, 
								width: 160 
							}]
						},{
							columnWidth : this.widthColumnaBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'2',
								handler:this.catalogoEstructuraNivel3.createDelegate(this) //N
							}]
						},{
							columnWidth : this.widthColumnaEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "label",
								name: "denon"+options.idtxt+'2', 
								id: "denest"+options.idtxt+'2', 
								hidden:this.ocultarDenominacion
							}] 
						}]
				 	},{//NIVEL 4
				 		layout : "column", 
						defaults : {border : false,labelWidth: 220}, 
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype : "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro4'], 
								name: 'codigo'+options.idtxt+'3', 
								id: 'codest'+options.idtxt+'3', 
								readOnly:true, 
								style:"text-align:right", 
								width: 160 
							}]
						},{
							columnWidth : this.widthColumnaBoton,
							bodyStyle:'padding:15px 0px 0px 0px', 
							items : [{
								xtype: "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'3',
								handler:this.catalogoEstructuraNivel4.createDelegate(this)
							}]
						},{
							columnWidth : this.widthColumnaEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype:  "label",
								name: "denon"+options.idtxt+'3', 
								id: "denest"+options.idtxt+'3', 
								hidden:this.ocultarDenominacion
							}] 
						}]
					},{//NIVEL 5
						layout : "column", 
						defaults : {border : false, labelWidth: 220},
						items : [{
							layout : "form", 
							border : false, 
							columnWidth : this.widthColumnaCampo,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype :  "textfield",
								labelSeparator: '',
								fieldLabel: empresa['nomestpro5'], 
								name: 'codigo'+options.idtxt+'4', 
								id: 'codest'+options.idtxt+'4', 
								readOnly:true, 
								style:"text-align:right", 
								width: 160 
							}]
						},{
							columnWidth : this.widthColumnaBoton,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "button",
								iconCls: "menubuscar", 
								id:'btnest'+options.idtxt+'4',
								handler:this.catalogoEstructuraNivelN.createDelegate(this) //N
							}]
						},{
							columnWidth : this.widthColumnaEtiqueta,
							bodyStyle:'padding:15px 0px 0px 0px',
							items : [{
								xtype: "label",
								name: "denon"+options.idtxt+'4', 
								id: "denest"+options.idtxt+'4', 
								hidden:this.ocultarDenominacion
							}] 
						}]
					}]
				});
		 		break;
		}
		return fieldset;
	}
	
	//funcion que usa a la funcion agregarCampo para colocar los campos en el formulario segun los parametros
	this.setCampoEstructura=function(){
		this.fieldSetEstPre=this.obtenerFieldSetEstructura();
		this.fieldSetEstPre.doLayout();
	}
	
	this.fieldSetEstPre=this.obtenerFieldSetEstructura();
	
	/************************************************************/
	/************CATALOGO DE ESTRUCTURA NIVEL 1******************/
	/************************************************************/
	//variables usadas en la creacion del catalogo de estructuras nivel 1
	this.dsestructuranivel1="";
	this.objetoestnivel1="";
	this.formbusquedaestructuranivel1="";
	this.gridestructuranivel1="";//esta variable sera usada en la funcion que crea los grid

	//funciones que crean y manejan los objetos que manejaran la data
	this.crearDatastoreEstructuraNivel1=function(){
		var registroestnivel1 = Ext.data.Record.create([
								{name: 'codestpro1'},    
								{name: 'denestpro1'},
								{name: 'estcla'}
							]);
		
		this.objetoestnivel1={"raiz":[{"codestpro1":'',"denestpro1":'',"estcla":''}]};
			
		this.dsestructuranivel1 =  new Ext.data.Store({
									proxy: new Ext.data.MemoryProxy(this.objetoestnivel1),
									reader: new Ext.data.JsonReader({
													root: 'raiz',             
													id: "id"   
												},registroestnivel1),
									data: this.objetoestnivel1
		  						})	;
	}

	this.actualizaDatastoreEstructuraNivel1 = function(cadena,valor)
	{
		this.dsestructuranivel1.filter(cadena,valor,true,false);
	}
	//fin funciones que crean y manejan los objetos que manejaran la data
	
	this.actualizarGridCodigoNivel1=function(){
		var v = Ext.getCmp('codestniv1').getValue();
		this.actualizaDatastoreEstructuraNivel1('codestpro1',v);
	}
	
	this.actualizarGridDenominacionNivel1=function() {
		var v = Ext.getCmp('denestniv1').getValue();
		this.actualizaDatastoreEstructuraNivel1('denestpro1',v);
	}
	
	
	//funcion para crear el formulario de busqueda 
	this.crearFormBusquedaEstructuraNivel1=function(){
		//Creando el fieldset del formBusquedaCat
		this.fieldcatalogo1 = new Ext.form.FieldSet({
			xtype:"fieldset", 
			title:'B&#250;squeda',
			width: 600,
			height:100,
			border:true,
			defaultType: 'textfield',
			style: 'position:absolute;left:5px;top:5px',
    		defaults: {labelSeparator:''},
			cls:'fondo',
			items: [{
				fieldLabel: 'C&#243;digo',
                name: 'C&#243;digo',
				id: 'codestniv1',
				autoCreate: {tag: 'input', type: 'text', maxlength: 25},
				width: 200,
				changeCheck: this.actualizarGridCodigoNivel1.createDelegate(this),							 
				initEvents : function() {
					AgregarKeyPress(this);
				}               
  			},{
                fieldLabel: 'Denominaci&#243;n',
                name: 'denominacion',
                id:'denestniv1',
                autoCreate: {tag: 'input', type: 'text', maxlength: 254},
				width: 400,
				changeCheck: this.actualizarGridDenominacionNivel1.createDelegate(this),							 
				initEvents : function(){
					AgregarKeyPress(this);
				}
			}]
		});
		//Fin del fieldset del formBusquedaCat
		
		this.formbusquedaestructuranivel1 = new Ext.FormPanel({
	        labelWidth: 80, 
	        frame:true,
	        width: 630,
			height:130,
	        items: [this.fieldcatalogo1]
		});				  

	}
	//FIN CATALOGO DE ESTRUCTURA NIVEL 1

	/************************************************************/
	/************CATALOGO DE ESTRUCTURA NIVEL 2******************/
	/************************************************************/
	//variables usadas en la creacion del catalogo de estructuras nivel 2
	this.dsestructuranivel2="";
	this.objetoestnivel2="";
	this.formbusquedaestructuranivel2="";
	this.gridestructuranivel2="";//esta variable sera usada en la funcion que crea los grid

	//funciones que crean y manejan los objetos que manejaran la data
	this.crearDatastoreEstructuraNivel2 =function(){
		var registroestnivel2 = Ext.data.Record.create([
								{name: 'codestpro1'},
								{name: 'codestpro2'},    
								{name: 'denestpro2'}
							]);
		
		this.objetoestnivel2 = {"raiz":[{"codestpro1":'',"codestpro2":'',"denestpro2":''}]};
			
		this.dsestructuranivel2 =  new Ext.data.Store({
									proxy: new Ext.data.MemoryProxy(this.objetoestnivel2),
									reader: new Ext.data.JsonReader({
													root: 'raiz',             
													id: "id"   
												},registroestnivel2),
									data: this.objetoestnivel2
		  						});	
	}

	this.actualizaDatastoreEstructuraNivel2=function(criterio,cadena)
	{
		this.dsestructuranivel2.filter(criterio,cadena,true,false);
	}
	//fin funciones que crean y manejan los objetos que manejaran la data
	
	this.actualizarGridCodigoNivel2=function() {
		var v = Ext.getCmp('codestniv2').getValue();
		this.actualizaDatastoreEstructuraNivel2('codestpro2',v);
	}
	
	this.actualizarGridDenominacionNivel2=function() {
		var v = Ext.getCmp('denestniv2').getValue();
		this.actualizaDatastoreEstructuraNivel2('denestpro2',v);
	}

	//funcion para crear el formulario de busqueda 
	this.crearFormBusquedaEstructuraNivel2=function(){
		//Creando el fieldset del formBusquedaCat
		this.fieldcatalogo2 = new Ext.form.FieldSet({
			xtype:"fieldset", 
			title:'B&#250;squeda',
			width: 600,
			height:100,
			border:true,
			defaultType: 'textfield',
			style: 'position:absolute;left:5px;top:5px',
    		defaults: {labelSeparator:''},
			cls:'fondo',
			items: [{
				fieldLabel: 'C&#243;digo',
                name: 'C&#243;digo',
				id:'codestniv2',
				autoCreate: {tag: 'input', type: 'text', maxlength: 25},
				width: 200,
				changeCheck: this.actualizarGridCodigoNivel2.createDelegate(this),							 
				initEvents : function() {
					AgregarKeyPress(this);
				}               
  			},{
                fieldLabel: 'Denominaci&#243;n',
                name: 'denominacion',
                id:'denestniv2',
                autoCreate: {tag: 'input', type: 'text', maxlength: 254},
				width: 400,
				changeCheck: this.actualizarGridDenominacionNivel2.createDelegate(this),							 
				initEvents : function(){
					AgregarKeyPress(this);
				}
			}]
		});
		//Fin del fieldset del formBusquedaCat
		
		this.formbusquedaestructuranivel2 = new Ext.FormPanel({
	        labelWidth: 80, 
	        frame:true,
	        width: 630,
			height:130,
	        items: [this.fieldcatalogo2]
		});				  

	}
	//FIN CATALOGO DE ESTRUCTURA NIVEL 2

	/************************************************************/
	/************CATALOGO DE ESTRUCTURA NIVEL 3******************/
	/************************************************************/
	//variables usadas en la creacion del catalogo de estructuras nivel 3
	this.dsestructuranivel3="";
	this.objetoestnivel3="";
	this.formbusquedaestructuranivel3="";
	this.gridestructuranivel3="";//esta variable sera usada en la funcion que crea los grid

	//funciones que crean y manejan los objetos que manejaran la data
	this.crearDatastoreEstructuraNivel3=function(){
		var registroestnivel3 = Ext.data.Record.create([
								{name: 'codestpro1'},
								{name: 'codestpro2'},
								{name: 'codestpro3'},    
								{name: 'denestpro3'}
							]);
		
		this.objetoestnivel3 = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"denestpro3":''}]};
			
		this.dsestructuranivel3 =  new Ext.data.Store({
									proxy: new Ext.data.MemoryProxy(this.objetoestnivel3),
									reader: new Ext.data.JsonReader({
													root: 'raiz',             
													id: "id"   
												},registroestnivel3),
									data: this.objetoestnivel3
		  						});	
	}

	this.actualizaDatastoreEstructuraNivel3=function(criterio,cadena)
	{
		this.dsestructuranivel3.filter(criterio,cadena,true,false);
	}
	//fin funciones que crean y manejan los objetos que manejaran la data
	
	
	this.actualizarGridCodigoNivel3=function() {
		//var v = Ext.getCmp('codestniv'+options.idtxt+'3').getValue();
		var v = Ext.getCmp('codestniv3').getValue();
		this.actualizaDatastoreEstructuraNivel3('codestpro3',v);
	}
	
	this.actualizarGridDenominacionNivel3=function() {
		var v = Ext.getCmp('denestniv3').getValue();
		this.actualizaDatastoreEstructuraNivel3('denestpro3',v);
	}

	//funcion para crear el formulario de busqueda 
	this.crearFormBusquedaEstructuraNivel3=function(){
		
		//Creando el fieldset del formBusquedaCat
		this.fieldcatalogo3 = new Ext.form.FieldSet({
			xtype:"fieldset", 
			title:'B&#250;squeda',
			width: 600,
			height:100,
			border:true,
			defaultType: 'textfield',
			style: 'position:absolute;left:5px;top:5px',
    		defaults: {labelSeparator:''},
			cls:'fondo',
			items: [{
				fieldLabel: 'C&#243;digo',
                name: 'C&#243;digo',
				id:'codestniv3',
				autoCreate: {tag: 'input', type: 'text', maxlength: 25},
				width: 200,
				changeCheck: this.actualizarGridCodigoNivel3.createDelegate(this),							 
				initEvents : function() {
					AgregarKeyPress(this);
				}               
  			},{
                fieldLabel: 'Denominaci&#243;n',
                name: 'denominacion',
                id:'denestniv3',
                autoCreate: {tag: 'input', type: 'text', maxlength: 254},
				width: 400,
				changeCheck: this.actualizarGridDenominacionNivel3.createDelegate(this),							 
				initEvents : function(){
					AgregarKeyPress(this);
				}
			}]
		});
		//Fin del fieldset del formBusquedaCat
		
		this.formbusquedaestructuranivel3 = new Ext.FormPanel({
			labelWidth: 80, 
	        frame:true,
	        width: 630,
			height:130,
	       	items: [this.fieldcatalogo3]
		});				  

	}
	//FIN CATALOGO DE ESTRUCTURA NIVEL 3

	/************************************************************/
	/************CATALOGO DE ESTRUCTURA NIVEL 4******************/
	/************************************************************/
	//variables usadas en la creacion del catalogo de estructuras nivel 4
	this.dsestructuranivel4="";
	this.objetoestnivel4="";
	this.formbusquedaestructuranivel4="";
	this.gridestructuranivel4="";//esta variable sera usada en la funcion que crea los grid

	//funciones que crean y manejan los objetos que manejaran la data
	this.crearDatastoreEstructuraNivel4 = function (){
		var registroestnivel4 = Ext.data.Record.create([
								{name: 'codestpro1'},
								{name: 'codestpro2'},
								{name: 'codestpro3'},
								{name: 'codestpro4'},    
								{name: 'denestpro4'}
							]);
		
		this.objetoestnivel4 = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"codestpro4":'',"denestpro4":''}]};
			
		this.dsestructuranivel4 =  new Ext.data.Store({
									proxy: new Ext.data.MemoryProxy(this.objetoestnivel4),
									reader: new Ext.data.JsonReader({
													root: 'raiz',             
													id: "id"   
												},registroestnivel4),
									data: this.objetoestnivel4
		  						})	
	}

	this.actualizaDatastoreEstructuraNivel4=function(criterio,cadena)
	{
		this.dsestructuranivel4.filter(criterio,cadena,true,false);
	}
	//fin funciones que crean y manejan los objetos que manejaran la data

	this.actualizarGridCodigoNivel4=function() {
		var v =Ext.getCmp('codestniv4').getValue();
		this.actualizaDatastoreEstructuraNivel4('codestpro4',v);
	}
	
	this.actualizarGridDenominacionNivel4=function() {
		var v = Ext.getCmp('denestniv4').getValue();
		this.actualizaDatastoreEstructuraNivel4('denestpro4',v);
	}
	
	
	//funcion para crear el formulario de busqueda 
	this.crearFormBusquedaEstructuraNivel4=function(){
		//Creando el fieldset del formBusquedaCat
		this.fieldcatalogo4 = new Ext.form.FieldSet({
			xtype:"fieldset", 
			title:'B&#250;squeda',
			width: 600,
			height:100,
			border:true,
			defaultType: 'textfield',
			style: 'position:absolute;left:5px;top:5px',
    		defaults: {width: 230, labelSeparator:''},
			cls:'fondo',
			items: [{
				fieldLabel: 'C&#243;digo',
                name: 'C&#243;digo',
				id:'codestniv4',
				autoCreate: {tag: 'input', type: 'text', maxlength: 25},
				width: 200,
				changeCheck:  this.actualizarGridCodigoNivel4.createDelegate(this),							 
				initEvents : function() {
					AgregarKeyPress(this);
				}               
  			},{
                fieldLabel: 'Denominaci&#243;n',
                name: 'denominacion',
                id:'denestniv4',
                autoCreate: {tag: 'input', type: 'text', maxlength: 254},
				width: 400,
				changeCheck: this.actualizarGridDenominacionNivel4.createDelegate(this),							 
				initEvents : function(){
					AgregarKeyPress(this);
				}
			}]
		});
		//Fin del fieldset del formBusquedaCat
		
		this.formbusquedaestructuranivel4 = new Ext.FormPanel({
	        labelWidth: 80, 
	        frame:true,
	        width: 630,
			height:130,
	       items: [this.fieldcatalogo4]
		});				  

	}
	//FIN CATALOGO DE ESTRUCTURA NIVEL 4

	/************************************************************/
	/************CATALOGO DE ESTRUCTURA NIVEL 5******************/
	/************************************************************/
	//variables usadas en la creacion del catalogo de estructuras nivel 5
	this.dsestructuranivel5="";
	this.objetoestnivel5="";
	this.formbusquedaestructuranivel5="";
	this.gridestructuranivel5="";//esta variable sera usada en la funcion que crea los grid

	//funciones que crean y manejan los objetos que manejaran la data
	this.crearDatastoreEstructuraNivel5=function(){
		var registroestnivel5 = Ext.data.Record.create([
								{name: 'codestpro1'},
								{name: 'codestpro2'},
								{name: 'codestpro3'},
								{name: 'codestpro4'},
								{name: 'codestpro5'},    
								{name: 'denestpro5'}
							]);
		
		this.objetoestnivel5 = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"codestpro4":'',"codestpro5":'',"denestpro5":''}]};
			
		this.dsestructuranivel5 =  new Ext.data.Store({
									proxy: new Ext.data.MemoryProxy(this.objetoestnivel5),
									reader: new Ext.data.JsonReader({
													root: 'raiz',             
													id: "id"   
												},registroestnivel5),
									data: this.objetoestnivel5
		  						});	
	}

	this.actualizaDatastoreEstructuraNivel5=function(criterio,cadena)
	{
		this.dsestructuranivel5.filter(criterio,cadena,true,false);
	}
	//fin funciones que crean y manejan los objetos que manejaran la data

	this.actualizarGridCodigoNivel5=function() {
		var v = Ext.getCmp('codestniv5').getValue();
		this.actualizaDatastoreEstructuraNivel5('codestpro5',v);
	}
	
	this.actualizarGridDenominacionNivel5=function() {
		var v = Ext.getCmp('denestniv5').getValue();
		this.actualizaDatastoreEstructuraNivel5('denestpro5',v,true,false);
	}
	
	//funcion para crear el formulario de busqueda 
	this.crearFormBusquedaEstructuraNivel5=function(){
		
		//Creando el fieldset del formBusquedaCat
		this.fieldcatalogo5 = new Ext.form.FieldSet({
			xtype:"fieldset", 
			title:'B&#250;squeda',
			width: 600,
			height:100,
			border:true,
			defaultType: 'textfield',
			style: 'position:absolute;left:5px;top:5px',
    		defaults: {width: 230, labelSeparator:''},
			cls:'fondo',
			items: [{
				fieldLabel: 'Codigo',
	            name: 'C&#243;digo',
				id:'codestniv5',
				autoCreate: {tag: 'input', type: 'text', maxlength: 25},
				width: 200,
				changeCheck: this.actualizarGridCodigoNivel5.createDelegate(this),							 
				initEvents : function(){
					AgregarKeyPress(this);
				}               
	      	},{
	      		fieldLabel: 'Denominaci&#243;n',
				name: 'denominacion',
				id:'denestniv5',
				autoCreate: {tag: 'input', type: 'text', maxlength: 254},
				width: 400,
				changeCheck: this.actualizarGridDenominacionNivel5.createDelegate(this),
				initEvents : function(){
					AgregarKeyPress(this);
				}
			}]
		});
		//Fin del fieldset del formBusquedaCat
		
		this.formbusquedaestructuranivel5 = new Ext.FormPanel({
	        labelWidth: 80, 
	        frame:true,
	        width: 630,
			height:130,
	        items: [this.fieldcatalogo5]
		});				  

	}
	//FIN CATALOGO DE ESTRUCTURA NIVEL 5

	/************************************************************/
	/************CATALOGO DE ESTRUCTURA NIVEL N******************/
	/************************************************************/
	//variables usadas en la creacion del catalogo de estructuras nivel 5
	this.dsestructuranivelN="";
	this.objetoestnivelN="";
	this.formbusquedaestructuranivelN="";
	this.gridestructuranivelN="";//esta variable sera usada en la funcion que crea los grid

	//funciones que crean y manejan los objetos que manejaran la data
	this.crearDatastoreEstructuraNivelN=function(){
		var registroestnivelN="";
		switch(parseInt(empresa['numniv'])) {
			case 1:
				registroestnivelN = Ext.data.Record.create([
								{name: 'codestpro1'},    
								{name: 'denestpro1'},
								{name: 'estcla'}
							]);
		
				this.objetoestnivelN={"raiz":[{"codestpro1":'',"denestpro1":'',"estcla":''}]};
				break;
			case 2:
				registroestnivelN = Ext.data.Record.create([
								{name: 'codestpro1'},
								{name: 'denestpro1'},
								{name: 'codestpro2'},    
								{name: 'denestpro2'},
								{name: 'estcla'}
							]);
		
				this.objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"denestpro2":''}]};
				break;
			case 3:
				registroestnivelN = Ext.data.Record.create([
								{name: 'codestpro1'},
								{name: 'denestpro1'},
								{name: 'codestpro2'},
								{name: 'denestpro2'},
								{name: 'codestpro3'},    
								{name: 'denestpro3'},
								{name: 'estcla'}
							]);
		
				this.objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"denestpro3":''}]};
				break;
			case 4:
				registroestnivelN = Ext.data.Record.create([
								{name: 'codestpro1'},
								{name: 'denestpro1'},
								{name: 'codestpro2'},
								{name: 'denestpro2'},
								{name: 'codestpro3'},
								{name: 'denestpro3'},
								{name: 'codestpro4'},    
								{name: 'denestpro4'},
								{name: 'estcla'}
							]);
		
				this.objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"codestpro4":'',"denestpro4":''}]};
				break;
			case 5:
		    	registroestnivelN = Ext.data.Record.create([
								{name: 'codestpro1'},
								{name: 'denestpro1'},
								{name: 'codestpro2'},
								{name: 'denestpro2'},
								{name: 'codestpro3'},
								{name: 'denestpro3'},
								{name: 'codestpro4'},
								{name: 'denestpro4'},
								{name: 'codestpro5'},    
								{name: 'denestpro5'},
								{name: 'estcla'}
							]);
		
		    	this.objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"codestpro4":'',"codestpro5":'',"denestpro5":''}]};
				break;
		}
		
		this.dsestructuranivelN =  new Ext.data.Store({
									proxy: new Ext.data.MemoryProxy(this.objetoestnivelN),
									reader: new Ext.data.JsonReader({
													root: 'raiz',             
													id: "id"   
												},registroestnivelN),
									data: this.objetoestnivelN
		  						})
	}

	this.actualizaDatastoreEstructuraNivelN=function(criterio,cadena)
	{
		this.dsestructuranivelN.filter(criterio,cadena,true,false);
	}
	//fin funciones que crean y manejan los objetos que manejaran la data
	
	this.actualizarGridCodigoNivelN = function() {
		var v = Ext.getCmp('codestnivN').getValue();
		this.actualizaDatastoreEstructuraNivelN('codestpro'+empresa['numniv'],v);
	}
	
	this.actualizarGridDenominacionNivelN = function() {
		var v =Ext.getCmp('denestnivN').getValue();
		this.actualizaDatastoreEstructuraNivelN('denestpro'+empresa['numniv'],v,true,false);
	}

	//funcion para crear el formulario de busqueda 
	this.crearFormBusquedaEstructuraNivelN=function(){
		
		//Creando el fieldset del formBusquedaCat
		this.fieldcatalogoN = new Ext.form.FieldSet({
			xtype:"fieldset", 
			title:'B&#250;squeda',
			width: 600,
			height:100,
			border:true,
			defaultType: 'textfield',
			style: 'position:absolute;left:5px;top:5px',
    		defaults: {labelSeparator:''},
			cls:'fondo',
			items: [{
				fieldLabel: 'C&#243;digo',
	            name: 'Codigo',
				id:'codestnivN',
				autoCreate: {tag: 'input', type: 'text', maxlength: 25},
				width: 200,
				changeCheck: this.actualizarGridCodigoNivelN.createDelegate(this),							 
				initEvents : function() {
					AgregarKeyPress(this);
				}               
	      	},{
	      		fieldLabel: 'Denominaci&#243;n',
				name: 'denominacion',
				id:'denestnivN',
				autoCreate: {tag: 'input', type: 'text', maxlength: 254},
				width: 400,
				changeCheck: this.actualizarGridDenominacionNivelN.createDelegate(this),							 
				initEvents : function() {
					AgregarKeyPress(this);
				}
			}]
		});
		//Fin del fieldset del formBusquedaCat
		
		this.formbusquedaestructuranivelN = new Ext.FormPanel({
	        labelWidth: 80,
	        frame:true,
	        width: 630,
			height:130,
	        items: [this.fieldcatalogoN]
		});				  

	}
	//FIN CATALOGO DE ESTRUCTURA NIVEL N

	
	this.cargarDataStoreNivel= function()
	{
		var datos = arguments[0].responseText;
		//segun el nivel cargamos los disferente resultados en los datastore correspondientes
		switch(this.operacion) {
			case 'nivel1':
				this.objetoestnivel1 = eval('(' + datos + ')');//objeto nivel 1
				if(this.objetoestnivel1!=''){
					this.dsestructuranivel1.loadData(this.objetoestnivel1);//ds nivel 1
				}
			break;
			
			case 'nivel2':
				this.objetoestnivel2 = eval('(' + datos + ')');//objeto nivel 2
				if(this.objetoestnivel2!=''){
					this.dsestructuranivel2.loadData(this.objetoestnivel2);//ds nivel 2
				}
			break;
			
			case 'nivel3':
				this.objetoestnivel3 = eval('(' + datos + ')');//objeto nivel 3
				if(this.objetoestnivel3!=''){
					this.dsestructuranivel3.loadData(this.objetoestnivel3);//ds nivel 3
				}
			break;
			
			case 'nivel4':
				this.objetoestnivel4 = eval('(' + datos + ')');//objeto nivel 4
				if(this.objetoestnivel4!=''){
					this.dsestructuranivel4.loadData(this.objetoestnivel4);//ds nivel 4
				}
			break;
			
			case 'nivel5':
				this.objetoestnivel5 = eval('(' + datos + ')');//objeto nivel 5
				if(this.objetoestnivel5!=''){
					this.dsestructuranivel5.loadData(this.objetoestnivel5);//ds nivel 5
				}
			break;
			
			case 'nivelN':
				this.objetoestnivelN = eval('(' + datos + ')');//objeto nivel N
				if(this.objetoestnivelN!=''){
					this.dsestructuranivelN.loadData(this.objetoestnivelN);//ds nivel N
				}
			break;
		}
	}
	//Aqui funcion para el request al controlador y capturar los datos del mismo
	this.enviarOperacion=function(operacion){
		//alert(this.fieldSetEstPre.findByType('textfield')[0].getValue());
		var cadenaJson="{'operacion':'" + operacion + "','cantnivel':'" + parseInt(empresa['numniv']) + "',";
		for (var i = 0;i<parseInt(empresa['numniv']);i++){
			if(i==parseInt(empresa['numniv'])-1){
				cadenaJson= cadenaJson + "'codest"+i+"':'" + String.leftPad(this.fieldSetEstPre.findById('codest'+options.idtxt+i).getValue(),25,'0') + "'}";//cambiar
			}else{
				cadenaJson= cadenaJson + "'codest"+i+"':'" + String.leftPad(this.fieldSetEstPre.findById('codest'+options.idtxt+i).getValue(),25,'0') + "',";//cambiar
			}
		}
		this.operacion=operacion;
		parametros = 'ObjSon='+cadenaJson; 
		Ext.Ajax.request({
			url : '../../controlador/spg/sigesp_ctr_spg_catestpresupuestaria.php',
			params : parametros,
			method: 'POST',
			success: this.cargarDataStoreNivel.createDelegate(this, arguments, 2)
		});
		
	}
	//fin funcion enviar operacion.....
	this.dobleclickgridNivel1=function() // Funcion para realizar el Set de los Datos de la Estructura de Nivel 1
	{
		this.setDataEstructuraNivel1();	
	}
	this.dobleclickgridNivel2=function() // Funcion para realizar el Set de los Datos de la Estructura de Nivel 2
	{
		this.setDataEstructuraNivel2();	
	}
	this.dobleclickgridNivel3=function() // Funcion para realizar el Set de los Datos de la Estructura de Nivel 3
	{
		this.setDataEstructuraNivel3();	
	}
	this.dobleclickgridNivel4=function() // Funcion para realizar el Set de los Datos de la Estructura de Nivel 4
	{
		this.setDataEstructuraNivel4();	
	}
	this.dobleclickgridNivel5=function() // Funcion para realizar el Set de los Datos de la Estructura de Nivel 5
	{
		this.setDataEstructuraNivel5();	
	}
	this.dobleclickgridNivelN=function() // Funcion para realizar el Set de los Datos de la Estructura de Nivel N
	{
		this.setDataEstructuraNivelN();		
	}
	//Aqui creaciones de las grid...
	this.crear_grid_catalogoestructura=function(operacion){
		//aqui creamos los grid....
		switch(operacion) {
			case 'nivel1':
				this.crearDatastoreEstructuraNivel1();
				this.enviarOperacion(operacion);	
		    	this.crearFormBusquedaEstructuraNivel1();//invocando el crear form de busqueda
				this.gridestructuranivel1 = new Ext.grid.GridPanel({
		 								width:770,
		 								height:400,
		 								tbar: this.formbusquedaestructuranivel1,
		 								autoScroll:true,
		 								enableColumnHide: false,
	     								border:true,
	     								ds: this.dsestructuranivel1,
	     								cm: new Ext.grid.ColumnModel([
	          								{header: empresa['nomestpro1'], width: 30, sortable: true,   dataIndex: 'codestpro1', renderer: this.mostrarNumDigNiv1, align:'center'},
	          								{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro1'},
											{header: "Tipo", width: 50, sortable: true, dataIndex: 'estcla',renderer:this.mostrarEstatus}
	       								]),
	       								stripeRows: true,
	      								viewConfig: {forceFit:true},
	      								listeners:{'celldblclick' : this.dobleclickgridNivel1.createDelegate(this)}
									});
				break;
				
			case 'nivel2':
				this.crearDatastoreEstructuraNivel2()
				this.enviarOperacion(operacion)	
		    	this.crearFormBusquedaEstructuraNivel2();//invocando el crear form de busqueda
				this.gridestructuranivel2 = new Ext.grid.GridPanel({
		 								width:770,
		 								height:400,
		 								tbar: this.formbusquedaestructuranivel2,
		 								autoScroll:true,
	     								border:true,
	     								enableColumnHide: false,
	     								ds: this.dsestructuranivel2,
	     								cm: new Ext.grid.ColumnModel([
	          								{header: empresa['nomestpro1'], width: 30, sortable: true,   dataIndex: 'codestpro1', renderer: this.mostrarNumDigNiv1, align:'center'},
											{header: empresa['nomestpro2'], width: 30, sortable: true,   dataIndex: 'codestpro2', renderer: this.mostrarNumDigNiv2, align:'center'},
	          								{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro2'}
										]),
	       								stripeRows: true,
	      								viewConfig: {forceFit:true},
	      								listeners:{'celldblclick' : this.dobleclickgridNivel2.createDelegate(this)}
									});
				break;
			
			case 'nivel3':
				this.crearDatastoreEstructuraNivel3()
				this.enviarOperacion(operacion)	
		    	this.crearFormBusquedaEstructuraNivel3();//invocando el crear form de busqueda
				this.gridestructuranivel3 = new Ext.grid.GridPanel({
		 								width:770,
		 								height:400,
		 								tbar: this.formbusquedaestructuranivel3,
		 								autoScroll:true,
	     								border:true,
	     								enableColumnHide: false,
	     								ds: this.dsestructuranivel3,
	     								cm: new Ext.grid.ColumnModel([
	          								{header: empresa['nomestpro1'], width: 30, sortable: true,   dataIndex: 'codestpro1', renderer: this.mostrarNumDigNiv1, align:'center'},
											{header: empresa['nomestpro2'], width: 30, sortable: true,   dataIndex: 'codestpro2', renderer: this.mostrarNumDigNiv2, align:'center'},
											{header: empresa['nomestpro3'], width: 30, sortable: true,   dataIndex: 'codestpro3', renderer: this.mostrarNumDigNiv3, align:'center'},
	          								{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro3'}
										]),
	       								stripeRows: true,
	      								viewConfig: {forceFit:true},
	      								listeners:{'celldblclick' : this.dobleclickgridNivel3.createDelegate(this)}
									});
				break;
			case 'nivel4':
				this.crearDatastoreEstructuraNivel4()
				this.enviarOperacion(operacion)	
		    	this.crearFormBusquedaEstructuraNivel4();//invocando el crear form de busqueda
				this.gridestructuranivel4 = new Ext.grid.GridPanel({
		 								width:770,
		 								height:400,
		 								tbar: this.formbusquedaestructuranivel4,
		 								autoScroll:true,
	     								border:true,
	     								enableColumnHide: false,
	     								ds: this.dsestructuranivel4,
	     								cm: new Ext.grid.ColumnModel([
	          								{header: empresa['nomestpro1'], width: 30, sortable: true,   dataIndex: 'codestpro1', renderer: this.mostrarNumDigNiv1, align:'center'},
											{header: empresa['nomestpro2'], width: 30, sortable: true,   dataIndex: 'codestpro2', renderer: this.mostrarNumDigNiv2, align:'center'},
											{header: empresa['nomestpro3'], width: 30, sortable: true,   dataIndex: 'codestpro3', renderer: this.mostrarNumDigNiv3, align:'center'},
											{header: empresa['nomestpro4'], width: 30, sortable: true,   dataIndex: 'codestpro4', renderer: this.mostrarNumDigNiv4, align:'center'},
	          								{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro4'}
										]),
	       								stripeRows: true,
	      								viewConfig: {forceFit:true},
	      								listeners:{'celldblclick' : this.dobleclickgridNivel4.createDelegate(this)}
									});
				break;
			case 'nivel5':
				this.crearDatastoreEstructuraNivel5()
				this.enviarOperacion(operacion)	
		    	this.crearFormBusquedaEstructuraNivel5();//invocando el crear form de busqueda
				this.gridestructuranivel5 = new Ext.grid.GridPanel({
		 								width:770,
		 								height:400,
		 								tbar: this.formbusquedaestructuranivel5,
		 								autoScroll:true,
	     								border:true,
	     								enableColumnHide: false,
	     								ds: this.dsestructuranivel5,
	     								cm: new Ext.grid.ColumnModel([
	          								{header: empresa['nomestpro1'], width: 30, sortable: true,   dataIndex: 'codestpro1', renderer: this.mostrarNumDigNiv1, align:'center'},
											{header: empresa['nomestpro2'], width: 30, sortable: true,   dataIndex: 'codestpro2', renderer: this.mostrarNumDigNiv2, align:'center'},
											{header: empresa['nomestpro3'], width: 30, sortable: true,   dataIndex: 'codestpro3', renderer: this.mostrarNumDigNiv3, align:'center'},
											{header: empresa['nomestpro4'], width: 30, sortable: true,   dataIndex: 'codestpro4', renderer: this.mostrarNumDigNiv4, align:'center'},
											{header: empresa['nomestpro5'], width: 30, sortable: true,   dataIndex: 'codestpro5', renderer: this.mostrarNumDigNiv5, align:'center'},
	          								{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro5'}
										]),
	       								stripeRows: true,
	      								viewConfig: {forceFit:true},
	      								listeners:{'celldblclick' : this.dobleclickgridNivel5.createDelegate(this)}
									});
				break;
			case 'nivelN':
				var nniv = parseInt(empresa['numniv'])
				this.crearDatastoreEstructuraNivelN();
				this.enviarOperacion(operacion);	
		    	this.crearFormBusquedaEstructuraNivelN();//invocando el crear form de busqueda
				modelogridN="[";
				for(var x=1;x<=nniv;x++){
					if(x==nniv){
						modelogridN = modelogridN + "{header: '"+empresa['nomestpro'+x]+"', width: 45, sortable: true,   dataIndex: 'codestpro"+x+"', align:'center'},"+
													"{header: 'Denominaci&#243;n', width: 45, sortable: true,   dataIndex: 'denestpro"+x+"'},"+
													"{header: 'Tipo', width: 30, sortable: true, dataIndex: 'estcla'}";
					}else{
						modelogridN = modelogridN + "{header: '"+empresa['nomestpro'+x]+"', width: 30, sortable: true,   dataIndex: 'codestpro"+x+"', renderer: this.mostrarNumDigNiv"+x+", align:'center'},";
					}	
				}
				modelogridN = modelogridN + "]";
				objetomodelo = Ext.util.JSON.decode(modelogridN);
				this.gridestructuranivelN = new Ext.grid.GridPanel({
		 								width:770,
		 								height:400,
		 								tbar: this.formbusquedaestructuranivelN,
		 								autoScroll:true,
	     								border:true,
	     								enableColumnHide: false,
	     								ds: this.dsestructuranivelN,
	     								cm: new Ext.grid.ColumnModel(objetomodelo),
	       								stripeRows: true,
	      								viewConfig: {forceFit:true},
	      								listeners:{'celldblclick' : this.dobleclickgridNivelN.createDelegate(this)}
									});
				
				switch (nniv) {
					case 1:
						this.gridestructuranivelN.getColumnModel().setRenderer(0,this.mostrarNumDigNiv1);
						this.gridestructuranivelN.getColumnModel().setRenderer(2,this.mostrarEstatus);
						break;
						
					case 2:
						this.gridestructuranivelN.getColumnModel().setRenderer(0,this.mostrarNumDigNiv1);
						this.gridestructuranivelN.getColumnModel().setRenderer(1,this.mostrarNumDigNiv2);
						this.gridestructuranivelN.getColumnModel().setRenderer(3,this.mostrarEstatus);
						break;
					
					case 3:
						this.gridestructuranivelN.getColumnModel().setRenderer(0,this.mostrarNumDigNiv1);
						this.gridestructuranivelN.getColumnModel().setRenderer(1,this.mostrarNumDigNiv2);
						this.gridestructuranivelN.getColumnModel().setRenderer(2,this.mostrarNumDigNiv3);
						this.gridestructuranivelN.getColumnModel().setRenderer(4,this.mostrarEstatus);
						break;
					
					case 4:
						this.gridestructuranivelN.getColumnModel().setRenderer(0,this.mostrarNumDigNiv1);
						this.gridestructuranivelN.getColumnModel().setRenderer(1,this.mostrarNumDigNiv2);
						this.gridestructuranivelN.getColumnModel().setRenderer(2,this.mostrarNumDigNiv3);
						this.gridestructuranivelN.getColumnModel().setRenderer(3,this.mostrarNumDigNiv4);
						this.gridestructuranivelN.getColumnModel().setRenderer(5,this.mostrarEstatus);
						break;
					
					case 5:
						this.gridestructuranivelN.getColumnModel().setRenderer(0,this.mostrarNumDigNiv1);
						this.gridestructuranivelN.getColumnModel().setRenderer(1,this.mostrarNumDigNiv2);
						this.gridestructuranivelN.getColumnModel().setRenderer(2,this.mostrarNumDigNiv3);
						this.gridestructuranivelN.getColumnModel().setRenderer(3,this.mostrarNumDigNiv4);
						this.gridestructuranivelN.getColumnModel().setRenderer(4,this.mostrarNumDigNiv5);
						this.gridestructuranivelN.getColumnModel().setRenderer(6,this.mostrarEstatus);
						break;
				}
				break; 
		}
	} 
	//fin crear grid..........
	
	
	this.setDataEstructuraNivel1=function()
	{
		estnivel1 = this.gridestructuranivel1.getSelectionModel().getSelected();
		if(estnivel1!=null)
		{
			this.fieldSetEstPre.findById('codest'+options.idtxt+'0').setValue(this.mostrarNumDigNiv1(estnivel1.get('codestpro1')));
			if(this.fieldSetEstPre.findById('denest'+options.idtxt+'0') != null)
			{
				this.fieldSetEstPre.findById('denest'+options.idtxt+'0').setText(estnivel1.get('denestpro1'));	
				//this.fieldSetEstPre.findById('denest'+options.idtxt+'0').setValue(estnivel1.get('denestpro1'));
			}
			this.fieldSetEstPre.findById('estcla'+options.idtxt).setValue(estnivel1.get('estcla'));
			this.limpiarEstructuras(0);
			this.gridestructuranivel1.destroy();
			ventana.destroy();
			if(options.onAceptar){
				this.fnOnAceptar();
			}
			if((options.cargarCuentas)!=undefined){
				if(parseInt(empresa['numniv'])=='1'){
					this.obtenerCuentasApertura();
				}
			}
		}
		else
		{
			this.mensajeValidacionNivel(1);
		}
		
	}
	
	this.cerrarVentanaEstructuraNivel1=function()
	{
		this.gridestructuranivel1.destroy();
		ventana.destroy();
	}

	//funciones para llamar a los catalogos....
	/*this.catalogoEstructuraNivel1=function(){
		this.crear_grid_catalogoestructura('nivel1');				   
	    ventana = new Ext.Window({
	    	title: 'Cat&#225;logo de '+empresa['nomestpro1'],
			autoScroll:true,
	        width:800,
	        height:475,
	        modal: true,
	        closable:false,
	        plain: false,
	        items:[this.gridestructuranivel1],
	        buttons: [{
						text:'Aceptar',  
				        handler: this.setDataEstructuraNivel1.createDelegate(this)
				       },
				       {
				      	text: 'Salir',
				        handler: this.cerrarVentanaEstructuraNivel1.createDelegate(this)
	                  }]
	      });
	      ventana.show();
	}*/

	this.setDataEstructuraNivel2=function()
	{
		estnivel2 = this.gridestructuranivel2.getSelectionModel().getSelected();
		if(estnivel2 != null)
		{
			this.fieldSetEstPre.findById('codest'+options.idtxt+'1').setValue(this.mostrarNumDigNiv2(estnivel2.get('codestpro2')));
			if(this.fieldSetEstPre.findById('denest'+options.idtxt+'1') != null)
			{
			 this.fieldSetEstPre.findById('denest'+options.idtxt+'1').setText(estnivel2.get('denestpro2'))
			}
			this.limpiarEstructuras(1);
			this.gridestructuranivel2.destroy();
			ventana.destroy();
			if(options.onAceptar){
				this.fnOnAceptar();
			}
			if((options.cargarCuentas)!=undefined){
				if(parseInt(empresa['numniv'])=='2'){
					this.obtenerCuentasApertura();
				}
			}
		}
		else
		{
			this.mensajeValidacionNivel(2);
		}
		
	}
	
	this.cerrarVentanaEstructuraNivel2=function()
	{
		this.gridestructuranivel2.destroy();
		ventana.destroy();	
	}
	
	this.setDataEstructuraNivel3=function()
	{
		estnivel3 = this.gridestructuranivel3.getSelectionModel().getSelected();
		if(estnivel3 != null)
		{
			this.fieldSetEstPre.findById('codest'+options.idtxt+'2').setValue(this.mostrarNumDigNiv3(estnivel3.get('codestpro3')));
			if(this.fieldSetEstPre.findById('denest'+options.idtxt+'2') != null)
			{
				this.fieldSetEstPre.findById('denest'+options.idtxt+'2').setText(estnivel3.get('denestpro3'))
			}
			this.limpiarEstructuras(2);
			this.gridestructuranivel3.destroy();
			ventana.destroy();
			if(options.onAceptar){
				this.fnOnAceptar();
			}
			if((options.cargarCuentas)!=undefined){
				if(parseInt(empresa['numniv'])=='3'){
					this.obtenerCuentasApertura();
				}
			}
		}
		else
		{
			this.mensajeValidacionNivel(3);
		}
	}
	
	this.cerrarVentanaEstructuraNivel3=function()
	{
		this.gridestructuranivel3.destroy();
		ventana.destroy();
	}
	
	this.setDataEstructuraNivel4=function()
	{
		estnivel4 = this.gridestructuranivel4.getSelectionModel().getSelected();
		if(estnivel4 != null)
		{
			this.fieldSetEstPre.findById('codest'+options.idtxt+'3').setValue(this.mostrarNumDigNiv4(estnivel4.get('codestpro4')));
			if(this.fieldSetEstPre.findById('denest'+options.idtxt+'3') != null)
			{
				this.fieldSetEstPre.findById('denest'+options.idtxt+'3').setText(estnivel4.get('denestpro4'))
			}
			this.limpiarEstructuras(3);
			this.gridestructuranivel4.destroy();
			ventana.destroy();
			if(options.onAceptar){
				this.fnOnAceptar();
			}
			if((options.cargarCuentas)!=undefined){
				if(parseInt(empresa['numniv'])=='4'){
					this.obtenerCuentasApertura();
				}
			}
		}
		else
		{
			this.mensajeValidacionNivel(4);
		}
	}
	
	this.cerrarVentanaEstructuraNivel4=function()
	{
		this.gridestructuranivel4.destroy();
		ventana.destroy();
	}

	this.setDataEstructuraNivel5=function()
	{
		estnivel5 = this.gridestructuranivel5.getSelectionModel().getSelected();
		if(estnivel5 != null)
		{
			this.fieldSetEstPre.findById('codest'+options.idtxt+'4').setValue(this.mostrarNumDigNiv5(estnivel5.get('codestpro5')));
			if(this.fieldSetEstPre.findById('denest'+options.idtxt+'4') != null)
			{
				this.fieldSetEstPre.findById('denest'+options.idtxt+'4').setText(estnivel5.get('denestpro5'))
			}
			this.gridestructuranivel5.destroy();
			ventana.destroy();
			if(options.onAceptar){
				this.fnOnAceptar();
			}
			if((options.cargarCuentas)!=undefined){
				if(parseInt(empresa['numniv'])=='5'){
					this.obtenerCuentasApertura();
				}
			}
		}
		else
		{
			this.mensajeValidacionNivel(5);
		}
	}
	
	this.cerrarVentanaEstructuraNivel5=function()
	{
		this.gridestructuranivel5.destroy();
		ventana.destroy();
	}
	
	this.cerrarVentanaEstructuraNivelN=function()
	{
		this.gridestructuranivelN.destroy();
		ventana.destroy();
	}

	this.mensajeValidacionNivel=function(nivel)
	{
		Ext.Msg.show({
		   	title:'Mensaje',
		   	msg: 'No ha seleccionado ningun(a) '+empresa['nomestpro'+nivel]+', verifique por favor',
		   	buttons: Ext.Msg.OK,
		   	animEl: 'elId',
		   	icon: Ext.MessageBox.ERROR,
		   	closable:false
			});
	}

	this.limpiarEstructuras=function(nivel)
	{
		var contador = nivel +1;
		for(i=contador; i<parseInt(empresa['numniv']); i++)
		{
			this.fieldSetEstPre.findById('codest'+options.idtxt+i).setValue("");
			if(this.fieldSetEstPre.findById('denest'+options.idtxt+i) != null)
			{
				this.fieldSetEstPre.findById('denest'+options.idtxt+i).setText("");
			}
		}
	}
	
	this.obtenerCodigoEstructuraNivelFormato=function(nivel)
	{
		var codigo="";
		switch(nivel){
			case 1: 
				codigo = this.fieldSetEstPre.findById('codest'+options.idtxt+'0').getValue();
				break;
			
			case 2:  
				if(this.fieldSetEstPre.findById('codest'+options.idtxt+'1') != null) {
					codigo = this.fieldSetEstPre.findById('codest'+options.idtxt+'1').getValue();
			    }
			    else {
			    	codigo = String.leftPad("",25,'0'); 
			    }
				     
			break;
			
			case 3:
				if(this.fieldSetEstPre.findById('codest'+options.idtxt+'2') != null) {
					codigo = this.fieldSetEstPre.findById('codest'+options.idtxt+'2').getValue();
				}
			    else {
			    	codigo = String.leftPad("",25,'0'); 
			    }
				break;
			
			case 4:
				if(this.fieldSetEstPre.findById('codest'+options.idtxt+'3') != null) {
					codigo = this.fieldSetEstPre.findById('codest'+options.idtxt+'3').getValue();
			    }
			    else {
			    	codigo = String.leftPad("",25,'0'); 
			    }
				break;
			
			case 5:
				if(this.fieldSetEstPre.findById('codest'+options.idtxt+'4') != null) {
					codigo = this.fieldSetEstPre.findById('codest'+options.idtxt+'4').getValue();
			    }
			    else {
			    	codigo = String.leftPad("",25,'0'); 
			    }
				break;
		}
		
		return codigo;
	}
	
	this.obtenerCodigoEstructuraNivel=function(nivel)
	{
		var codigo="";
		switch(nivel)
		{
			case 1: codigo = String.leftPad(this.fieldSetEstPre.findById('codest'+options.idtxt+'0').getValue(),25,'0');
			break;
			
			case 2:  if(this.fieldSetEstPre.findById('codest'+options.idtxt+'1') != null)
			         {
				      codigo = String.leftPad(this.fieldSetEstPre.findById('codest'+options.idtxt+'1').getValue(),25,'0');
			         }
			         else
			         {
			          codigo = String.leftPad("",25,'0'); 
			         }
				     
			break;
			
			case 3: 
					if(this.fieldSetEstPre.findById('codest'+options.idtxt+'2') != null)
			         {
						codigo = String.leftPad(this.fieldSetEstPre.findById('codest'+options.idtxt+'2').getValue(),25,'0');
			         }
			         else
			         {
			            codigo = String.leftPad("",25,'0'); 
			         }
				     
				    
			break;
			
			case 4: 
					if(this.fieldSetEstPre.findById('codest'+options.idtxt+'3') != null)
			         {
						codigo = String.leftPad(this.fieldSetEstPre.findById('codest'+options.idtxt+'3').getValue(),25,'0');
			         }
			         else
			         {
			            codigo = String.leftPad("",25,'0'); 
			         }
				   
			break;
			
			case 5: 
					if(this.fieldSetEstPre.findById('codest'+options.idtxt+'4') != null)
			         {
						codigo = String.leftPad(this.fieldSetEstPre.findById('codest'+options.idtxt+'4').getValue(),25,'0');
			         }
			         else
			         {
			            codigo = String.leftPad("",25,'0'); 
			         }
			break;
			
		}
		
		return codigo;
	}
	
	this.obtenerEstClaEstructura=function()
	{
		
		return this.fieldSetEstPre.findById('estcla'+options.idtxt).getValue();
	}
	
	this.obtenerValorNivel=function(nivel)
	{
		return this.fieldSetEstPre.findById('codest'+options.idtxt+(nivel-1)).getValue();
	}
	
    this.obtenerArrayEstructura=function()
    {
    	var codestpro = new Array(5);
    	var j = 0;
    	for(i = 0; i<5; i++)
    	{
    		j++;
    		codestpro[i] = this.obtenerCodigoEstructuraNivel(j);
    	}
    	codestpro[5] = this.obtenerEstClaEstructura();	
    	
    	return codestpro;
    }
    
    this.obtenerArrayEstructuraFormato =function()
    {
    	var codestpro = new Array(5);
    	var j = 0;
    	for(i = 0; i<5; i++)
    	{
    		j++;
    		codestpro[i] = this.obtenerCodigoEstructuraNivelFormato(j);
    	}
    	codestpro[5] = this.obtenerEstClaEstructura();	
    	
    	return codestpro;
    }
	
	this.obtenerEstructucturaFormato=function()
	{
		var codigo="";
		for(i=0; i<parseInt(empresa['numniv']); i++)
		{
			if(i==0)
			{
				codigo = this.fieldSetEstPre.findById('codest'+options.idtxt+i).getValue();
			}
			else
			{
				codigo += this.fieldSetEstPre.findById('codest'+options.idtxt+i).getValue();
			}
			
		}
		return codigo;
	}
	
	this.obtenerEstructuraFormatoMostrar = function()
	{
		var formatoEstructura='';
		for(i=0; i<parseInt(empresa['numniv']); i++){
			if(i==0){
				formatoEstructura = this.fieldSetEstPre.findById('codest'+options.idtxt+i).getValue().substr(-empresa['loncodestpro'+options.idtxt+i]);
			}
			else
			{
				formatoEstructura += '-'+this.fieldSetEstPre.findById('codest'+options.idtxt+i).getValue().substr(-empresa['loncodestpro'+options.idtxt+i]);
			}
		}
				 
		return formatoEstructura;
	}
	
	this.limpiarDenominaciones = function()
	{
		var formatoEstructura='';
		for(i=0; i<parseInt(empresa['numniv']); i++){
			this.fieldSetEstPre.findById('denest'+options.idtxt+i).setText('');
		}
				 
		return formatoEstructura;
	}
	
	this.agregarListenerBoton=function(nivel,funcion)
	{
		if(typeof(funcion) == "function")
		{
			this.fieldSetEstPre.findById('btnest'+options.idtxt+(nivel-1)).addListener('click',funcion);
		}
	}
	
	this.setComCatalogoDen=function(registro)
	{
		
		if(registro != null)
		{
			for (var i = parseInt(empresa['numniv']) - 1 ; i >= 0; i--){
				if(this.fieldSetEstPre.findById('denest'+options.idtxt+i) != null)
				{
					this.fieldSetEstPre.findById('denest'+options.idtxt+i).setText(registro.get('denestpro'+(i+1)))
				}
			};
			this.fieldSetEstPre.findById('estcla'+options.idtxt).setValue(registro.get('estcla'));
		}
		else
		{
			Ext.Msg.show({
			   	title:'Mensaje',
			   	msg: 'No ha seleccionado ninguna estructura, verifique por favor',
			   	buttons: Ext.Msg.OK,
			   	animEl: 'elId',
			   	icon: Ext.MessageBox.ERROR,
			   	closable:false
			});
		}
	}
	
	this.obtenerCuentasApertura=function()
	{
		(options.grid).store.removeAll();
		var arreglo = new Array(5);
		arreglo = this.obtenerArrayEstructuraFormato();
		var reCuenta = Ext.data.Record.create([
		    {name: 'spg_cuenta'},                      
		    {name: 'denominacion'},
		    {name: 'asignado'},
		    {name: 'status'},
		    {name: 'enero'},
		    {name: 'febrero'},
		    {name: 'marzo'},
		    {name: 'abril'},
		    {name: 'mayo'},
		    {name: 'junio'},
		    {name: 'julio'},
		    {name: 'agosto'},
		    {name: 'septiembre'},
		    {name: 'octubre'}, 
		    {name: 'noviembre'},
		    {name: 'diciembre'},
		    {name: 'estdis'},
		    {name: 'estdisfuefin'},
		    {name: 'cadena'},
		    {name: 'pordistribuir'}
		]);
		
		var JSONObject = {
			'operacion'  : 'buscarCuentasApertura',
			'codestpro1' : String.leftPad(arreglo[0],25,'0'),
			'codestpro2' : String.leftPad(arreglo[1],25,'0'),
			'codestpro3' : String.leftPad(arreglo[2],25,'0'),
			'codestpro4' : String.leftPad(arreglo[3],25,'0'),
			'codestpro5' : String.leftPad(arreglo[4],25,'0'),
			'estcla'     : arreglo[5],
		}
		
		var ObjSon = JSON.stringify(JSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/spg/sigesp_ctr_spg_apertura.php',
			params : parametros,
			method: 'POST',
			success: function ( resultado, request){
				var datos = resultado.responseText;
				var objetoCuenta = eval('(' + datos + ')');
				var nCue = 0;
				var wTim = 70;
				if(objetoCuenta!=''){
					if(objetoCuenta.raiz == null || objetoCuenta.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
					}
					else{
						(options.grid).store.loadData(objetoCuenta);
						nCue = (options.grid).store.getCount();
						wTim = wTim * nCue;
						Ext.MessageBox.show({
							msg: 'Cargando informaci&#243;n',
							title: 'Progreso',
							progressText: 'Cargando informaci&#243;n',
							width:300,
							wait:true,
							waitConfig:{
								interval:200,
								duration:wTim,
								increment:15,
								fn:function(){
									Ext.MessageBox.hide();
								}
							},	
							animEl: 'mb7'
						});
					}
				}
			}	
		});
	}
	
	this.obtenerId = function(registro,idtxt)
    {
		if(empresa['numniv']=='3'){
			Ext.getCmp('codest'+options.idtxt+'0').setValue((registro.get('codestpro1')).substr(-empresa['loncodestpro1']));
			Ext.getCmp('codest'+options.idtxt+'1').setValue((registro.get('codestpro2')).substr(-empresa['loncodestpro2']));
			Ext.getCmp('codest'+options.idtxt+'2').setValue((registro.get('codestpro3')).substr(-empresa['loncodestpro3']));
			Ext.getCmp('estcla'+options.idtxt).setValue(registro.get('estcla'));
		}
		else{
			Ext.getCmp('codest'+options.idtxt+'0').setValue((registro.get('codestpro1')).substr(-empresa['loncodestpro1']));
			Ext.getCmp('codest'+options.idtxt+'1').setValue((registro.get('codestpro2')).substr(-empresa['loncodestpro2']));
			Ext.getCmp('codest'+options.idtxt+'2').setValue((registro.get('codestpro3')).substr(-empresa['loncodestpro3']));
			Ext.getCmp('codest'+options.idtxt+'3').setValue((registro.get('codestpro4')).substr(-empresa['loncodestpro4']));
			Ext.getCmp('codest'+options.idtxt+'4').setValue((registro.get('codestpro5')).substr(-empresa['loncodestpro5']));
			Ext.getCmp('estcla'+options.idtxt).setValue(registro.get('estcla'));
		}
    }
	
	this.validarEstructura = function() {
		var estValida = true;
		for(var i=0; i<parseInt(empresa['numniv']); i++){
			if(this.fieldSetEstPre.findById('codest'+options.idtxt+i).getValue() == ''){
				estValida = false;
				break;
			}
		}
				 
		return estValida;
    }
	
};
