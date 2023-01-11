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

function CatalogoDistribucionFuenta(cuenta,denominacion,monto,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,registro)
{
	var txtmonto = new Ext.form.TextField({
		id:'txtmonto',
		autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
        listeners:{
			'blur':function(objeto){
				var numero = objeto.getValue();
				valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
				objeto.setValue(valor);
			},
			'specialKey':function(objeto){
				var numero = objeto.getValue();
				valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
				objeto.setValue(valor);
			},
			'focus':function(objeto){
				var numero = formatoNumericoEdicion(objeto.getValue());
				objeto.setValue(numero);
			}
	    }
	})
	
	//-------------------------------------------------------------------------------------------------------------------------	

	var reFuente = Ext.data.Record.create([
		{name: 'codfuefin'},                      
        {name: 'denfuefin'},
        {name: 'asignado'}
    ]);
  	
  	var dsFuente =  new Ext.data.Store({
  		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reFuente)
  	});
  						
  	var cmFuente = new Ext.grid.ColumnModel([
  		  {header: "<CENTER>C&#243;digo</CENTER>", width:40, sortable: true, dataIndex: 'codfuefin'},
          {header: "<CENTER>Fuente Financiamiento</CENTER>", width: 130, sortable: true, dataIndex: 'denfuefin'},
          {header: "<CENTER>Asignado</CENTER>", width: 50, sortable: true, dataIndex: 'asignado', editor: txtmonto},
    ]);
                  	
	gridFuente = new Ext.grid.EditorGridPanel({
    	width:650,
 		height:200,
		frame:true,
		title:"<H1 align='center'>Distribuci&#243;n de la Fuente de Financiamiento</H1>",
		autoScroll:true,
   		border:true,
   		ds: dsFuente,
     	cm: cmFuente,
     	stripeRows: true,
    	viewConfig: {forceFit:true}
	});
	
	//Metodo que realiza cambios despues de editar la grid
	gridFuente.on('afteredit', function(Obj){
         var registro = Obj.record;
         var monasi = registro.get('asignado');
         var resta = 0;
         var suma = 0;
         
         if(monasi!=''){
        	 monasi = parseFloat(ue_formato_operaciones(monasi));
  		 }
         monto = Ext.getCmp('monto').getValue();
         monto = parseFloat(ue_formato_operaciones(monto));
         if(monasi>monto){
        	 Ext.MessageBox.show({
        		 title:'Mensaje',
        		 msg:'El monto debe ser menor al Monto Asignado de la Cuenta !!!',
        		 buttons: Ext.Msg.OK,
        		 icon: Ext.MessageBox.INFO
        	 });
        	 registro.set('asignado',formatoNumericoMostrar(0,2,'.',',','','','-',''));
         }
         else{
        	 gridFuente.store.each(function(reDet){
            	 if(reDet.get('asignado')!=''){
            		 asignado=reDet.get('asignado');
            		 asignado = parseFloat(ue_formato_operaciones(asignado));
            		 suma+=asignado;
            	 }
             })
             if(suma>monto){
            	 Ext.MessageBox.show({
            		 title:'Mensaje',
            		 msg:'El acumulado de los montos, debe ser menor al Asignado de la Cuenta !!!',
            		 buttons: Ext.Msg.OK,
            		 icon: Ext.MessageBox.INFO
            	 });
            	 registro.set('asignado',formatoNumericoMostrar(0,2,'.',',','','','-',''));
             }
             else{
            	resta = monto-suma;
            	Ext.getCmp('monporasi').setValue(formatoNumericoMostrar(resta,2,'.',',','','','-',''));
             } 
         }
	});
  
	//-------------------------------------------------------------------------------------------------------------------------	

	//Creacion del formulario del catalogo bienes
	var formVentanaCatalogo = new Ext.FormPanel({
		width: 680,
		height: 430,
		frame:true,
		autoScroll:false,
		items: [{
			xtype:"fieldset", 
		    title:'Datos de la Cuenta',
			border:true,
			width: 650,
			cls: 'fondo',
			height: 160,
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
						name: 'monto',
						id: 'monto',
						width: 150,
						readOnly:true,
						value:monto
					}]
				}]
     		}]  
    	},{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:110px',
			items: [{
				layout: "form",
				border: false,
				labelWidth: 100,
				items: [{
					xtype: "radiogroup",
					fieldLabel: 'Distribuci&#243;n',
					labelSeparator:"",	
					columns: [250,250],
					id:'distribucion',
					binding:true,
					hiddenvalue:'',
					defaultvalue:0,
					allowBlank:true,
					items: [
					        {boxLabel: 'Automatico', name: 'dist',inputValue: '1', listeners:{	
								'check': function (checkbox, checked){
									if(checked){
										Ext.getCmp('txtmonto').disable();
										calcularMontos();
									}
								}
					        }},
					        {boxLabel: 'Manual', name: 'dist', inputValue: '0',listeners:{	
								'check': function (checkbox, checked){
									if(checked){
										Ext.getCmp('txtmonto').enable();
										if(Ext.getCmp('txtmonto').getValue()!=undefined){
											Ext.getCmp('txtmonto').setValue('');
										}
									}
								}
					        }}
					        ]
				}]
			}]
		},gridFuente,
	 	{
	 		layout: "column",
	 		defaults: {border: false},
	 		style: 'position:absolute;left:210px;top:380px',
	 		items: [{
	 			layout: "form",
	 			border: false,
	 			labelWidth: 80,
	 			items: [{
	 				xtype: 'textfield',
	 				fieldLabel: 'Por Asignar',
	 				name: 'monporasi',
	 				id: 'monporasi',
	 				width: 150,
	 				readOnly:true,
	 			}]
	 		}]
    	}]   
	});
	//fin del formulario 
	
	//-------------------------------------------------------------------------------------------------------------	
	
	//Creando la ventana del catalogo bienes
	var ventana = new Ext.Window({
		title: "<H1 align='center'>Distribuci&#243;n Fuente de Financiamiento</H1>",
        width:680,
        height:500,
        modal: false,
        closable:false,
        plain: false,
        frame:true,
        items:[formVentanaCatalogo],
		buttons: [{
			text:'Aceptar',  
			handler: function(){
				var aux = formatoNumericoMostrar(0,2,'.',',','','','-','');
				if(Ext.getCmp('monporasi').getValue()!=aux)
				{
					Ext.MessageBox.show({
						title:'Mensaje',
						msg:'La Distribuci&#243;n no cuadra con lo asignado, por favor revise los montos !!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
				else
				{
					var cadenajson='';
					var numDet = 0;
					gridFuente.store.each(function (reDet)
					{
						if(numDet==0)
						{
							cadenajson += "{'codfuefin':'"+reDet.get('codfuefin')+"','asignado':'"+reDet.get('asignado')+"'}";
						}
						else
						{
							cadenajson += ",{'codfuefin':'"+reDet.get('codfuefin')+"','asignado':'"+reDet.get('asignado')+"'}";
						}
						numDet++;
					});
					registro.set('estdisfuefin','S');
					registro.set('cadena',cadenajson);
					ventana.destroy();
				}
			}
		},{
	   		text: 'Salir',
   			handler:function(){
   			ventana.destroy();
   		    }
   		}]
	});
	buscarFuentes();
	ventana.show();
	
	function buscarFuentes()
	{
		obtenerMensaje('procesar','','Buscando Datos');
		//buscar modificaciones a aprobar
		var JSONObject = {
			'operacion'  : 'buscarFuentesFinanciamiento',
			'codestpro1' : String.leftPad(codestpro1,25,'0'),
			'codestpro2' : String.leftPad(codestpro2,25,'0'),
			'codestpro3' : String.leftPad(codestpro3,25,'0'),
			'codestpro4' : String.leftPad(codestpro4,25,'0'),
			'codestpro5' : String.leftPad(codestpro5,25,'0'),
			'estcla'     : estcla,
			'spg_cuenta' : cuenta,
		}
	
		var ObjSon = JSON.stringify(JSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/spg/sigesp_ctr_spg_apertura.php',
			params : parametros,
			method: 'POST',
			success: function ( resultado, request){
				Ext.Msg.hide();
				var datos = resultado.responseText;
				var objeto = eval('(' + datos + ')');
				if(objeto!=''){
					if(objeto.raiz == null || objeto.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No se han definido Fuentes de Financimiento para la Estructura Seleccionada !!!',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
					}
					else{
						gridFuente.store.loadData(objeto);
					}
				}
			}	
		});
	}
	
	function calcularMontos()
	{
		var grid = gridFuente.getStore().getRange();
		var total = gridFuente.getStore().getCount();
		for(var i=0;i<grid.length;i++){
			monto=Ext.getCmp('monto').getValue();
			monto=parseFloat(ue_formato_operaciones(monto));
			division=monto/total;
			division=Math.round(division*100)/100
			if(empresa["estspgdecimal"]==0){
				division=redondear2(division);
			}
			subtotal=division*(total);
            monultfuefin=division + (monto - subtotal);
            monultfuefin=Math.round(monultfuefin*100)/100
			division=formatoNumericoMostrar(division,2,'.',',','','','-','');
            if(i!=total){
            	grid[i].set('asignado',division);
            }
		}
		calcularPorAsignar();
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
	
	function calcularPorAsignar()
	{
		var grid=gridFuente.getStore().getRange();
		var total=gridFuente.getStore().getCount();
		asignado=Ext.getCmp('monto').getValue();
		asignado=parseFloat(ue_formato_operaciones(asignado));
		var suma = 0;
		for(var i=0;i<grid.length;i++){
			monto=grid[i].get('asignado');
			monto=parseFloat(ue_formato_operaciones(monto));
			suma+=monto;
		}
		suma = Math.round(suma*100)/100;
		Ext.getCmp('monporasi').setValue(formatoNumericoMostrar(asignado-suma,2,'.',',','','','-',''));
	}
	
}	


