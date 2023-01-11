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

function CatalogoDistribucionTrimestral(cuenta,denominacion,montoAsig,registro,flagAjuste){
  
	//Creacion del formulario del catalogo bienes
	var formVentanaCatalogo = new Ext.FormPanel({
		width: 680,
		height: 310,
		frame:true,
		autoScroll:false,
		items: [{
			xtype:"fieldset", 
		    title:'Datos de la Cuenta',
			border:true,
			width: 650,
			cls: 'fondo',
			height: 120,
			items:[{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:20px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 100,
					items: [{
						xtype: 'textfield',
						labelSeparator :'',
						fieldLabel: 'Cuenta',
						name: 'cuenta',
						id: 'cuenta',									
						width: 150,
						readOnly:true,
						value:cuenta
					}]
				}]
     		},
     		{
     			layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:50px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 100,
					items: [{
						xtype: 'textfield',
						labelSeparator :'',
						fieldLabel: 'Denominaci&#243;n',
						name: 'denominacion',
						id: 'denominacion',
						width: 450,
						readOnly:true,
						value:denominacion
					}]
				}]
     		},
     		{
     			layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:80px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 100,
					items: [{
						xtype: 'textfield',
						labelSeparator :'',
						fieldLabel: 'Monto Asignado',
						name: 'monasig',
						id: 'monto',
						width: 150,
						readOnly:true,
						value:montoAsig
					}]
				}]
     		}]  
    	},
    	{
    		
    		xtype:"fieldset", 
		    title:'Asignaci&#243;n',
			border:true,
			width: 650,
			cls: 'fondo',
			height: 150,
			items:[{
	 			layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:150px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 100,
					items: [{
						xtype: 'textfield',
						labelSeparator :'',
						fieldLabel: 'Trimestre I',
						name: 'marzo',
						id: 'marzo',
						width: 150,
						autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'0123456789.');"},
						listeners:{
							'change':function()
							{
								var decimal = 0;
								var monto = this.getValue();
								if(empresa["estspgdecimal"]==0) {
									monto = monto.split(".");
									decimal = parseInt(monto[1]);
									if (decimal>0){
										Ext.MessageBox.show({
					 						title:'Mensaje',
					 						msg:'No se permiten monto con decimales, debe ajustar la configuraci&#243;n',
					 						buttons: Ext.Msg.OK,
					 						icon: Ext.MessageBox.INFO
					 					});
										var formatonumero = formatoNumericoMostrar(0,2,'.',',','','','-','');
										this.setValue(formatonumero);
									}
								}
								else {
									var formatonumero = formatoNumericoMostrar(this.getValue(),2,'.',',','','','-','');
									this.setValue(formatonumero);
								}
								validarMonto(this,'I');
							}
						}
					}]
				}]
	 		},
	 		{
	 			layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:180px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 100,
					items: [{
						xtype: 'textfield',
						labelSeparator :'',
						fieldLabel: 'Trimestre II',
						name: 'junio',
						id: 'junio',
						width: 150,
						autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'0123456789.');"},
						listeners:{
							'change':function()
							{
								var decimal = 0;
								var monto = this.getValue();
								if(empresa["estspgdecimal"]==0) {
									monto = monto.split(".");
									decimal = parseInt(monto[1]);
									if (decimal>0){
										Ext.MessageBox.show({
					 						title:'Mensaje',
					 						msg:'No se permiten monto con decimales, debe ajustar la configuraci&#243;n',
					 						buttons: Ext.Msg.OK,
					 						icon: Ext.MessageBox.INFO
					 					});
										var formatonumero = formatoNumericoMostrar(0,2,'.',',','','','-','');
										this.setValue(formatonumero);
									}
								}
								else {
									var formatonumero = formatoNumericoMostrar(this.getValue(),2,'.',',','','','-','');
									this.setValue(formatonumero);
								}
								validarMonto(this,'III');
							}
						}
					}]
				}]
	 		},
	 		{
	 			layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:350px;top:150px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 100,
					items: [{
						xtype: 'textfield',
						labelSeparator :'',
						fieldLabel: 'Trimestre III',
						name: 'septiembre',
						id: 'septiembre',
						width: 150,
						autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'0123456789.');"},
						listeners:{
							'change':function()
							{
								var decimal = 0;
								var monto = this.getValue();
								if(empresa["estspgdecimal"]==0) {
									monto = monto.split(".");
									decimal = parseInt(monto[1]);
									if (decimal>0){
										Ext.MessageBox.show({
					 						title:'Mensaje',
					 						msg:'No se permiten monto con decimales, debe ajustar la configuraci&#243;n',
					 						buttons: Ext.Msg.OK,
					 						icon: Ext.MessageBox.INFO
					 					});
										var formatonumero = formatoNumericoMostrar(0,2,'.',',','','','-','');
										this.setValue(formatonumero);
									}
								}
								else {
									var formatonumero = formatoNumericoMostrar(this.getValue(),2,'.',',','','','-','');
									this.setValue(formatonumero);
								}
								validarMonto(this,'II');
							}
						}
					}]
				}]
	 		},
	 		{
	 			layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:350px;top:180px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 100,
					items: [{
						xtype: 'textfield',
						labelSeparator :'',
						fieldLabel: 'Trimestre IV',
						name: 'diciembre',
						id: 'diciembre',
						width: 150,
						autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'0123456789.');"},
						listeners:{
							'change':function()
							{
								var decimal = 0;
								var monto = this.getValue();
								if(empresa["estspgdecimal"]==0) {
									monto = monto.split(".");
									decimal = parseInt(monto[1]);
									if (decimal>0){
										Ext.MessageBox.show({
					 						title:'Mensaje',
					 						msg:'No se permiten monto con decimales, debe ajustar la configuraci&#243;n',
					 						buttons: Ext.Msg.OK,
					 						icon: Ext.MessageBox.INFO
					 					});
										var formatonumero = formatoNumericoMostrar(0,2,'.',',','','','-','');
										this.setValue(formatonumero);
									}
								}
								else {
									var formatonumero = formatoNumericoMostrar(this.getValue(),2,'.',',','','','-','');
									this.setValue(formatonumero);
								}
								validarMonto(this,'IV');
							}
						}
					}]
				}]
	 		},
	 		{
	 			layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:210px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 100,
					items: [{
						xtype: 'textfield',
						labelSeparator :'',
						fieldLabel: 'Total Distribuido',
						name: 'montodis',
						id: 'mondis',
						width: 150,
						readOnly: true,
					}]
				}]
	 		},{
	 			layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:350px;top:210px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 100,
					items: [{
						xtype: 'textfield',
						labelSeparator :'',
						fieldLabel: 'Por Distribuir',
						name: 'montopordis',
						id: 'monpordis',
						width: 150,
						readOnly:true,
					}]
				}]
	 		}]
    	}]   
	});
	//fin del formulario 
	//-------------------------------------------------------------------------------------------------------------	
	
	//Creando la ventana del catalogo bienes
	var ventana = new Ext.Window({
		title: "<H1 align='center'>Asignaci&#243;n Trimestral</H1>",
        width:680,
        height:380,
        modal: false,
        closable:false,
        plain: false,
        frame:true,
        items:[formVentanaCatalogo],
		buttons: [{
			text:'Aceptar',  
			handler: function(){
				var aux = formatoNumericoMostrar(0,2,'.',',','','','-','');
				if(Ext.getCmp('monpordis').getValue()!=aux){
					Ext.MessageBox.show({
						title:'Mensaje',
						msg:'La Distribuci&#243;n no cuadra con lo asignado, por favor revise los montos !!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
				else{
					if (flagAjuste) {
						var cuenta = registro.get('spg_cuenta')
		            	Ext.Msg.show({
			        		title:'Confirmar',
			     		   	msg: 'Esta seguro de los ajustes realizados a la cuenta '+cuenta+', estos se guardaran automaticamente al cerrar la ventana',
			     		   	buttons: Ext.Msg.YESNO,
			     		   	icon: Ext.MessageBox.QUESTION,
			     		   	fn: function(btn) {
			     		   		if (btn == 'yes') {
				     		   		registro.set('enero',aux);
									registro.set('febrero',aux);
									registro.set('marzo',Ext.getCmp('marzo').getValue());
									registro.set('abril',aux);
									registro.set('mayo',aux);
									registro.set('junio',Ext.getCmp('junio').getValue());
									registro.set('julio',aux);
									registro.set('agosto',aux);
									registro.set('septiembre',Ext.getCmp('septiembre').getValue());
									registro.set('octubre',aux);
									registro.set('noviembre',aux);
									registro.set('diciembre',Ext.getCmp('diciembre').getValue());
									registro.set('pordistribuir',aux);
																	
			     		   			var arrCodigos = fieldSetEstructura.obtenerArrayEstructuraFormato();
			     		   			var codestpro1 = String.leftPad(arrCodigos[0],25,'0');
			     		   			var codestpro2 = String.leftPad(arrCodigos[1],25,'0');
			     		   			var codestpro3 = String.leftPad(arrCodigos[2],25,'0');
			     		   			var codestpro4 = String.leftPad(arrCodigos[3],25,'0');
			     		   			var codestpro5 = String.leftPad(arrCodigos[4],25,'0');
			     		   			var estcla     = arrCodigos[5];
				     		   		var myJSONObject = {"operacion":"actDistribucion","cuenta":cuenta,"codestpro1":codestpro1,
				     		   							"codestpro2":codestpro2,"codestpro3":codestpro3,"codestpro4":codestpro4,
				     		   							"codestpro5":codestpro5,"estcla":estcla,"m1":registro.get('enero'),
				     		   							"m2":registro.get('febrero'),"m3":registro.get('marzo'),
				     		   						    "m4":registro.get('abril'),"m5":registro.get('mayo'),
				     		   						    "m6":registro.get('junio'),"m7":registro.get('julio'),
				     		   						    "m8":registro.get('agosto'),"m9":registro.get('septiembre'),
				     		   						    "m10":registro.get('octubre'),"m11":registro.get('noviembre'),
				     		   						    "m12":registro.get('diciembre')};
			            			var ObjSon = Ext.util.JSON.encode(myJSONObject);
			            			var parametros ='ObjSon='+ObjSon;
			            			Ext.Ajax.request({
			            				url: '../../controlador/spg/sigesp_ctr_spg_apertura.php',
			            				params: parametros,
			            				method: 'POST',
			            				success: function ( result, request ) {
			            					var respuesta = result.responseText;
			            					var datajson = eval('(' + respuesta + ')');
			    							if(datajson.raiz.valido==true)
			    							{	
			    								Ext.Msg.show({
				    	    						title:'Mensaje',
				    	    						msg: datajson.raiz.mensaje,
				    	    						buttons: Ext.Msg.OK,
				    	    						icon: Ext.MessageBox.INFO
				    	    					});
			    							}
			    							else
			    							{
			    								Ext.Msg.show({
				    	    						title:'Error',
				    	    						msg: datajson.raiz.mensaje,
				    	    						buttons: Ext.Msg.OK,
				    	    						icon: Ext.MessageBox.ERROR
				    	    					});
			    							}
			    							ventana.destroy();
			            				},
			            				failure: function ( result, request){
			            					ventana.destroy();
			            					Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
			            				}
			            			});
			     		   		}
			     		   	}
		            	});
					}
					else {
						registro.set('enero',aux);
						registro.set('febrero',aux);
						registro.set('marzo',Ext.getCmp('marzo').getValue());
						registro.set('abril',aux);
						registro.set('mayo',aux);
						registro.set('junio',Ext.getCmp('junio').getValue());
						registro.set('julio',aux);
						registro.set('agosto',aux);
						registro.set('septiembre',Ext.getCmp('septiembre').getValue());
						registro.set('octubre',aux);
						registro.set('noviembre',aux);
						registro.set('diciembre',Ext.getCmp('diciembre').getValue());
						registro.set('pordistribuir',aux);
						ventana.destroy();
					}
				}
			}
		},{
	   		text: 'Salir',
   			handler:function(){
   				ventana.destroy();
   		    }
   		}]
	});
	
	ajuste(registro);
	ventana.show();
	
	function ajuste(registro)
	{
		var asignado = parseFloat(ue_formato_operaciones(registro.get('asignado')));
		var distribuido = parseFloat(ue_formato_operaciones(registro.get('enero'))) + parseFloat(ue_formato_operaciones(registro.get('febrero'))) + 
		                  parseFloat(ue_formato_operaciones(registro.get('marzo'))) + parseFloat(ue_formato_operaciones(registro.get('abril'))) +
		                  parseFloat(ue_formato_operaciones(registro.get('mayo'))) + parseFloat(ue_formato_operaciones(registro.get('junio'))) + 
		                  parseFloat(ue_formato_operaciones(registro.get('julio'))) + parseFloat(ue_formato_operaciones(registro.get('agosto'))) + 
		                  parseFloat(ue_formato_operaciones(registro.get('septiembre'))) + parseFloat(ue_formato_operaciones(registro.get('octubre'))) +
		                  parseFloat(ue_formato_operaciones(registro.get('noviembre'))) + parseFloat(ue_formato_operaciones(registro.get('diciembre')));
		Ext.getCmp('marzo').setValue(registro.get('marzo'));
		Ext.getCmp('junio').setValue(registro.get('junio'));
		Ext.getCmp('septiembre').setValue(registro.get('septiembre'));
		Ext.getCmp('diciembre').setValue(registro.get('diciembre'));
		Ext.getCmp('mondis').setValue(formatoNumericoMostrar(distribuido,2,'.',',','','','-',''));
		Ext.getCmp('monpordis').setValue(formatoNumericoMostrar(asignado-distribuido,2,'.',',','','','-',''));
		
	}
	
	function distribucion()
	{
		var distribuido = 0;
		var pordistribuir = 0;
		var diciembre = 0;
		var asignado = parseFloat(ue_formato_operaciones(Ext.getCmp('monto').getValue()));
		var division = (asignado/12);
		if(empresa["estspgdecimal"]==0){
			division_aux=redondear2(division); //ojo con la funcion
			if(!verificarDistAuto(division_aux,asignado))
			{
				division=redondear3(division);
			}
			else
			{
				division=redondear2(division);
			}
			asignado=redondear2(asignado);
			suma_diciembre=redondear2(division*12);
			mes12=(asignado-suma_diciembre);
			mes12=redondear2(mes12);
			if(mes12>=0)
			{
				diciembre=division+mes12;
			} 			
			else
			{
				diciembre=division+mes12;
			}
			total=(division*11);
			total_general=total+diciembre;
			total_general=redondear2(total_general);
			resto=(asignado-total_general);
			resto=redondear2(resto);
			diciembre=diciembre+resto;
			distribuido=(division*11)+diciembre;
		}
		else{
			var newDiv  = redondearNumero(division, 2);
			var newAsi  = redondearNumero(asignado, 2);
			diciembre   = redondearNumero(newAsi - (newDiv*11), 2);
			distribuido = (newDiv*11)+diciembre;
		}
		pordistribuir=asignado-distribuido;
		division=formatoNumericoMostrar(division,2,'.',',','','','-','');
		diciembre=formatoNumericoMostrar(diciembre,2,'.',',','','','-','');
		Ext.getCmp('marzo').setValue(division);
		Ext.getCmp('junio').setValue(division);
		Ext.getCmp('septiembre').setValue(division);
		Ext.getCmp('diciembre').setValue(diciembre);
		Ext.getCmp('mondis').setValue(formatoNumericoMostrar(distribuido,2,'.',',','','','-',''));
		Ext.getCmp('monpordis').setValue(formatoNumericoMostrar(pordistribuir,2,'.',',','','','-',''));
	}
	
	function redondear2(numero)
	{
		numero2='';
		numero=parseFloat(numero);
		numero=Math.ceil(numero*10)/10
		AuxString = numero.toString();
		if(AuxString.indexOf('.')>=0)
		{
			AuxArr=AuxString.split('.');
			if(AuxArr[1]>=5)
			{
				numero=Math.ceil(numero);
			}
			else
			{ 
				numero=Math.floor(numero);
			}
		} 
		return numero;
	}

	function redondear3(numero)
	{
		numero2='';
		numero=parseFloat(numero);
		numero=Math.ceil(numero*10)/10
		AuxString = numero.toString();
		if(AuxString.indexOf('.')>=0)
		{
			AuxArr=AuxString.split('.');
			if(AuxArr[1]>5)
			{
				numero=Math.ceil(numero);
			}
			else
			{ 
				numero=Math.floor(numero);
			}
		} 
		return numero;
	}
	
	function verificarDistAuto(montoVeri,asignado)
	{
		var total = 0;
		var ok = true;
		for(i=1;i<=4;i++)
		{
			total += montoVeri;
			if((total>asignado)&&(i<4))
			{
				ok = false
				break;
			}
		}
		return ok;
	}
	
	function validarMonto(objeto,valor)
	{
		var monto = parseFloat(ue_formato_operaciones(Ext.getCmp('monto').getValue()));
		var monto_aux = parseFloat(ue_formato_operaciones(objeto.getValue()));
		if(monto_aux>monto){
			Ext.MessageBox.show({
				title:'Mensaje',
				msg:'El monto para el Trimestre '+valor+' debe ser menor al Monto Asignado de la Cuenta !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO
			});
			objeto.setValue(formatoNumericoMostrar(0,2,'.',',','','','-',''));
		}
		else{
			total=acumularMonto();
			total=parseFloat(total).toFixed(2);
			if(monto<total){
				Ext.MessageBox.show({
					title:'Mensaje',
					msg:'El acumulado de la distribuci&#243;n debe ser menor al Monto Asignado de la Cuenta !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});
				objeto.setValue(formatoNumericoMostrar(0,2,'.',',','','','-',''));
			}
			resta=monto-total;
			Ext.getCmp('mondis').setValue(formatoNumericoMostrar(total,2,'.',',','','','-',''));
			Ext.getCmp('monpordis').setValue(formatoNumericoMostrar(resta,2,'.',',','','','-',''));
		}
	}
	
	function acumularMonto()
	{
		montomar=parseFloat(ue_formato_operaciones(Ext.getCmp('marzo').getValue()));
		montojun=parseFloat(ue_formato_operaciones(Ext.getCmp('junio').getValue()));
		montosep=parseFloat(ue_formato_operaciones(Ext.getCmp('septiembre').getValue()));
		montodic=parseFloat(ue_formato_operaciones(Ext.getCmp('diciembre').getValue()));
		total=montomar+montojun+montosep+montodic;
		return total;
	}
}	


