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

barraherramienta = true;
var gridCuenta = null;
var fieldSetEstructura = null;
var	fromSPGAPE = null;
var cargo = true;
 
Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	//Combo distribucion para la grid de cuentas
	var reDistribucion = [ ['Manual','2'],
                           ['Automatico','1'],  
                           ['---Seleccione---','0']]; 
	// Arreglo que contiene los Documentos que se pueden controlar

    var dsDistribucion = new Ext.data.SimpleStore({
             fields: ['den', 'cod'],
             data : reDistribucion // Se asocian los documentos disponibles
    });
	                                        
    var cmbDistribucion = new Ext.form.ComboBox({  
	    	 store: dsDistribucion,
	    	 fieldLabel:'',
	    	 displayField:'den',
	    	 valueField:'cod',
	         name:'distribucion',
	         id:'distribucion',
	         forceSelection: true,
	         typeAhead: true,
	         mode: 'local',
	         binding:true,
	         triggerAction: 'all',
	         listeners: {
	 			'select': function(valor){
    				var registro = gridCuenta.getSelectionModel().getSelected();
    				if(registro.get('asignado')!='0,00'){
    					if(valor.getValue()=="1") {
    						if(Ext.getCmp('estmodape').getValue()=='0'){
    							distribucionMensual(registro);
    							Ext.MessageBox.show({
        	 						title:'Mensaje',
        	 						msg:'El Monto Asignado ha sido distribuido en partes iguales para los 12 Meses del A&#241;o',
        	 						buttons: Ext.Msg.OK,
        	 						icon: Ext.MessageBox.INFO
        	 					});
    						}
    						else{
    							distribucionTrimestral(registro);
    							Ext.MessageBox.show({
        	 						title:'Mensaje',
        	 						msg:'El Monto Asignado ha sido distribuido en partes iguales para los IV Trimestres del A&#241;o',
        	 						buttons: Ext.Msg.OK,
        	 						icon: Ext.MessageBox.INFO
        	 					});
    						}
    	 				}
    	 				else if(valor.getValue()=="2"){
    	 					if(Ext.getCmp('estmodape').getValue()=='0'){
    	 						CatalogoDistribucionMensual(registro.get('spg_cuenta'),registro.get('denominacion'),registro.get('asignado'),registro,false);
    	 					}
    	 					else{
    	 						CatalogoDistribucionTrimestral(registro.get('spg_cuenta'),registro.get('denominacion'),registro.get('asignado'),registro,false);
    	 					}
    	 				}
    				}
    				else{
    					Ext.MessageBox.show({
     						title:'Mensaje',
     						msg:'El Monto Asignado debe ser mayor a cero (0,00), verifique por favor !!!',
     						buttons: Ext.Msg.OK,
     						icon: Ext.MessageBox.INFO
     					});
    					valor.setValue('0');
    				}
	 			}
	 		}
	});
    
    var txtasignado = new Ext.form.TextField({
		allowBlank: false,
		autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
		listeners:{
	    	'blur':function()
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
					else {
						var formatonumero = formatoNumericoMostrar(this.getValue(),2,'.',',','','','-','');
						this.setValue(formatonumero);
					}
				}
				else {
					var formatonumero = formatoNumericoMostrar(this.getValue(),2,'.',',','','','-','');
					this.setValue(formatonumero);
				}
			}
	   }
	});
        
	//-------------------------------------------------------------------------------------------------------------------------	
	
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
        {name: 'distribuir'},
        {name: 'estdisfuefin'},
        {name: 'cadena'},
        {name: 'pordistribuir'},
        {name: 'apertura'}
    ]);
  	
  	var dsCuenta =  new Ext.data.Store({
  		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCuenta)
  	});
  						
  	var cmCuenta = new Ext.grid.ColumnModel([
  		new Ext.grid.CheckboxSelectionModel(),
  		  {header: "<CENTER>Cuenta</CENTER>", width:60, sortable: true, dataIndex: 'spg_cuenta'},
          {header: "<CENTER>Denominaci&#243;n</CENTER>", width: 100, sortable: true, dataIndex: 'denominacion'},
          {header: "<CENTER>Asignado</CENTER>", width: 50, sortable: true, dataIndex: 'asignado', editor: txtasignado},
          {header: "<CENTER>Distribuci&#243;n</CENTER>", width: 50, sortable: true, dataIndex: 'distribuir',editor: cmbDistribucion, renderer: MostrarDistribucion},
    ]);
                  	
	gridCuenta = new Ext.grid.EditorGridPanel({
    	width:850,
 		height:250,
		frame:true,
		title:"<H1 align='center'>APERTURA</H1>",
		autoScroll:true,
   		border:true,
   		ds: dsCuenta,
     	cm: cmCuenta,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:true}),
     	stripeRows: true,
    	viewConfig: {forceFit:true},
    	tbar:[{
            text:'Distribuci&#243;n Fuente Financiamiento',
            tooltip:'Distribuci&#243;n Fuente Financiamiento',
            iconCls:'agregar',
            id: 'btagrebie',
            handler: function(){
            	var registro = gridCuenta.getSelectionModel().getSelected();
				if(registro.get('asignado')=='0,00'){
					Ext.MessageBox.show({
 						title:'Mensaje',
 						msg:'La Cuenta debe tener una Asignaci&#243;n mayor a cero, para poder realizar la Distribuci&#243;n por Fuente de Financiamiento!!!',
 						buttons: Ext.Msg.OK,
 						icon: Ext.MessageBox.INFO
 					});
				}
				else{
					var arrCodigos = fieldSetEstructura.obtenerArrayEstructuraFormato();
					CatalogoDistribucionFuenta(registro.get('spg_cuenta'),registro.get('denominacion'),registro.get('asignado'),arrCodigos[0],arrCodigos[1],arrCodigos[2],arrCodigos[3],arrCodigos[4],arrCodigos[5],registro);
				}
    		}
  		},{
            text:'Ajustar Distribuci&#243;n Manual',
            tooltip:'Ajustar la distribuci&#243;n manual de la cuenta seleccionada',
            iconCls:'agregar',
            id: 'btagrebie',
            handler: function(){
            	var registro = gridCuenta.getSelectionModel().getSelected();
				if(registro.get('asignado')!='0,00'){
					if(registro.get('distribuir')=="2"){
	 					if(Ext.getCmp('estmodape').getValue()=='0'){
	 						CatalogoDistribucionMensual(registro.get('spg_cuenta'),registro.get('denominacion'),registro.get('asignado'),registro,true);
	 					}
	 					else{
	 						CatalogoDistribucionTrimestral(registro.get('spg_cuenta'),registro.get('denominacion'),registro.get('asignado'),registro,true);
	 					}
	 				}
					else {
						Ext.MessageBox.show({
	 						title:'Mensaje',
	 						msg:'La distribuci&#243;n debe ser manual para poder realizar un ajuste',
	 						buttons: Ext.Msg.OK,
	 						icon: Ext.MessageBox.INFO
	 					});
					}
				}
				else{
					Ext.MessageBox.show({
 						title:'Mensaje',
 						msg:'El Monto Asignado debe ser mayor a cero (0,00), verifique por favor !!!',
 						buttons: Ext.Msg.OK,
 						icon: Ext.MessageBox.INFO
 					});
					valor.setValue('0');
				}
    		}
  		},{
            text:'Reiniciar saldo apertura',
            tooltip:'Coloca en cero el saldo de apertura de la cuenta seleccionada',
            iconCls:'remover',
            id: 'btsaldocero',
            handler: function(){
            	var registro = gridCuenta.getSelectionModel().getSelected();
            	var cuenta = registro.get('spg_cuenta')
            	Ext.Msg.show({
	        		title:'Confirmar',
	     		   	msg: 'Realmente desea asignarle saldo cero a la cuenta '+cuenta,
	     		   	buttons: Ext.Msg.YESNO,
	     		   	icon: Ext.MessageBox.QUESTION,
	     		   	fn: function(btn)
					{
	     		   		if (btn == 'yes')
						{
	     		   			var arrCodigos = fieldSetEstructura.obtenerArrayEstructuraFormato();
	     		   			var codestpro1 = String.leftPad(arrCodigos[0],25,'0');
	     		   			var codestpro2 = String.leftPad(arrCodigos[1],25,'0');
	     		   			var codestpro3 = String.leftPad(arrCodigos[2],25,'0');
	     		   			var codestpro4 = String.leftPad(arrCodigos[3],25,'0');
	     		   			var codestpro5 = String.leftPad(arrCodigos[4],25,'0');
	     		   			var estcla     = arrCodigos[5];
		     		   		var myJSONObject = {"operacion":"saldoCero","cuenta":cuenta,"codestpro1":codestpro1,
		     		   							"codestpro2":codestpro2,"codestpro3":codestpro3,"codestpro4":codestpro4,
		     		   							"codestpro5":codestpro5,"estcla":estcla,"monto":registro.get('asignado')};
	            			var ObjSon = Ext.util.JSON.encode(myJSONObject);
	            			var parametros ='ObjSon='+ObjSon;
	            			Ext.Ajax.request({
	            				url: '../../controlador/spg/sigesp_ctr_spg_apertura.php',
	            				params: parametros,
	            				method: 'POST',
	            				success: function ( result, request )
								{
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
	    								registro.set('asignado','0,00');
	    								registro.set('enero','0,00');
	    								registro.set('febrero','0,00');
	    								registro.set('marzo','0,00');
	    								registro.set('abril','0,00');
	    								registro.set('mayo','0,00');
	    								registro.set('junio','0,00');
	    								registro.set('julio','0,00');
	    								registro.set('agosto','0,00');
	    								registro.set('septiembre','0,00');
	    								registro.set('octubre','0,00');
	    								registro.set('noviembre','0,00');
	    								registro.set('diciembre','0,00');
	    								registro.set('distribuir','1');
	    								registro.set('pordistribuir','0,00');
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
									irCancelar();	            					
	            				},
	            				failure: function ( result, request){ 
	            						Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
	            				}
	            			});
	     		   		}
	     		   	}
            	});
			}
  		}]
	});
		
	gridCuenta.on('afteredit', function(Obj) {
		if (Obj.field == 'asignado') {
			Obj.record.set('distribuir', '0');
			Obj.record.set('apertura', '0');
			buscarFuentes(Obj.record);
		}
	});
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	fieldSetEstructura = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		style:'position:absolute;left:15px;top:15px',
		mostrarDenominacion:true,
		idtxt:'1',
		cargarCuentas: '1',
		grid: gridCuenta,
	});
	//agregarListenersEstructura(fieldSetEstructura);
	
	//-------------------------------------------------------------------------------------------------------------------------	

	//Creando el formulario principal
	var Xpos = ((screen.width/2)-(440));
  	fromSPGAPE = new Ext.FormPanel({
  		title: "<H1 align='center'>Apertura de Cuentas</H1>",
  		width: 880,
		height: 500,
		applyTo: 'formulario',
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
		frame: true,
		autoScroll:true,
		items: [fieldSetEstructura.fieldSetEstPre,
		        {
				xtype:"fieldset", 
				title:'',
				border:true,
				width: 850,
				height: 90,
				cls: 'fondo',
				items:[{
						layout:"column",
						defaults:{border: false},
						items: [{
								layout:"form",
								border:false,
								labelWidth:170,
								items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'Per&#237;odo',
										id: 'periodo',
										readOnly:true,
										width: 90,
										value: empresa['periodo'].substring(0, 4),
										}]
								}]
						},
						{
						layout: "column",
						defaults: {border: false},
						items: [{
								layout: "form",
								border: false,
								labelWidth: 170,
								items: [{
										xtype: 'checkbox',
										labelSeparator :'',
										boxLabel:'Mensual',
										fieldLabel: 'Modalidad de la Apertura',
										id: 'estmodapemen',
										readOnly:true,
										inputValue:1,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'0',
										allowBlank:true
									}]
								},
								{
								layout: "form",
								border: false,
								labelWidth: 50,
								items: [{
										xtype: 'checkbox',
										labelSeparator :'',
										boxLabel:'Trimestral',
										fieldLabel: '',
										readOnly:true,
										id: 'estmodapetri',
										inputValue:1,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'0',
										allowBlank:true
									}]
								}]
						},
						{
						xtype: 'hidden',
						id: 'numniv',
						binding:true,
						defaultvalue:'',
						allowBlank:true
						},
						{
						xtype: 'hidden',
						id: 'estmodape',
						binding:true,
						defaultvalue:'',
						allowBlank:true
						}]
		        },gridCuenta]
  	})
  	verificarEstatus();
}); //fin del formulario principal

//-------------------------------------------------------------------------------------------------------------------------	

function MostrarDistribucion(valor)
{	
	switch (valor) {
		case "2":
			return "Manual";
			break;
		
		case "1":
			return "Automatico";
			break;	
		
		default:
			return "---Seleccione---";
			break;
	}
}

function verificarEstatus()
{
	var myJSONObject = {
		"operacion":"verificar_estatus" 
	};
			
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/spg/sigesp_ctr_spg_apertura.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request )
		{ 
			var datos = result.responseText;
			var datajson = eval('(' + datos + ')');
			if(datajson!="")
			{
				Ext.getCmp('estmodape').setValue(datajson.raiz.estmodape);
				if(datajson.raiz.estmodape=="0"){
					Ext.getCmp('estmodapemen').setValue(true);
				}
				else{
					Ext.getCmp('estmodapetri').setValue(true);
				}
				Ext.getCmp('numniv').setValue(datajson.raiz.numniv);
			}
		},
		failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});		
}

function irCancelar(){
	limpiarFormulario(fromSPGAPE);
	gridCuenta.store.removeAll();
	fieldSetEstructura.limpiarEstructuras(-1);
	verificarEstatus();
}

function irNuevo(){
	irCancelar();
}

function irGuardar()
{
	var arrCodigos = fieldSetEstructura.obtenerArrayEstructuraFormato();
	var entro = true;
	codestpro1 = String.leftPad(arrCodigos[0],25,'0');
	codestpro2 = String.leftPad(arrCodigos[1],25,'0');
	codestpro3 = String.leftPad(arrCodigos[2],25,'0');
	codestpro4 = String.leftPad(arrCodigos[3],25,'0');
	codestpro5 = String.leftPad(arrCodigos[4],25,'0');
	estcla = arrCodigos[5];
	periodo = Ext.getCmp('periodo').getValue();
	var valido = true;
	var aux = formatoNumericoMostrar(0,2,'.',',','','','-','');
	var mensaje = 'No se detecto niguna modificacion para guardar, verifique si realizo la distribucion de los montos asignados';
	if(gridCuenta.getStore().getCount()==0)
	{
		Ext.MessageBox.show({
			title:'Mensaje',
			msg:'Debe tener al menos un registro cargado',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else
	{
		cadenajson = "{'operacion':'guardar','codsis':'"+sistema+"','nomven':'"+vista+"'," +
					  "'codestpro1':'"+codestpro1+"','codestpro2':'"+codestpro2+"','codestpro3':'"+codestpro3+"'," +
					  "'codestpro4':'"+codestpro4+"','codestpro5':'"+codestpro5+"','estcla':'"+estcla+"'," +
					  "'periodo':'"+periodo+"','arrDetalle':[";
		var primero = true;
		var numCuenta = 0;
		
		dataCuenta = gridCuenta.store.getModifiedRecords();
		var numCuenta = dataCuenta.length;
		for(var i=0;i<=numCuenta-1;i++)
		{
			var asignado = parseFloat(ue_formato_operaciones(dataCuenta[i].get('asignado')));
			var aux = parseFloat(ue_formato_operaciones(aux));
			if((dataCuenta[i].get('distribuir')!='0') && (eval(asignado)!=eval(aux)))
			{
				var pordistribuir = parseFloat(ue_formato_operaciones(dataCuenta[i].get('pordistribuir')));
				if(pordistribuir!=aux)
				{
					Ext.MessageBox.show({
						title:'Mensaje',
						msg:'La Distribuci&#243;n no cuadra con lo asignado. Por Favor revise la cuenta '+dataCuenta[i].get('spg_cuenta')+' Con una diferencia de '+dataCuenta[i].get('pordistribuir'),
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
					return false;
				}
				else
				{
					if(dataCuenta[i].get('apertura')!='1')
					{					
						if(primero)
						{
							cadenajson += "{'spg_cuenta':'"+dataCuenta[i].get('spg_cuenta')+"','denominacion':'"+trim(dataCuenta[i].get('denominacion'))+"'," +
						       			   "'monto':'"+dataCuenta[i].get('asignado')+"','enero':'"+dataCuenta[i].get('enero')+"'," +
							               "'febrero':'"+dataCuenta[i].get('febrero')+"','marzo':'"+dataCuenta[i].get('marzo')+"'," +
				                           "'abril':'"+dataCuenta[i].get('abril')+"','mayo':'"+dataCuenta[i].get('mayo')+"'," +
				                           "'junio':'"+dataCuenta[i].get('junio')+"','julio':'"+dataCuenta[i].get('julio')+"'," +
				                           "'agosto':'"+dataCuenta[i].get('agosto')+"','septiembre':'"+dataCuenta[i].get('septiembre')+"'," +
				                           "'octubre':'"+dataCuenta[i].get('octubre')+"','noviembre':'"+dataCuenta[i].get('noviembre')+"'," +
				                           "'diciembre':'"+dataCuenta[i].get('diciembre')+"','distribuir':'"+dataCuenta[i].get('distribuir')+"',arrFueFin:[";
							if(dataCuenta[i].get('cadena')!='' || dataCuenta[i].get('cadena')!=undefined)
							{
								cadenajson += dataCuenta[i].get('cadena');
							}
							cadenajson += "]}";
							primero = false;
						}
						else
						{
							cadenajson += ",{'spg_cuenta':'"+dataCuenta[i].get('spg_cuenta')+"','denominacion':'"+trim(dataCuenta[i].get('denominacion'))+"'," +
						       			   "'monto':'"+dataCuenta[i].get('asignado')+"','enero':'"+dataCuenta[i].get('enero')+"'," +
							               "'febrero':'"+dataCuenta[i].get('febrero')+"','marzo':'"+dataCuenta[i].get('marzo')+"'," +
				                           "'abril':'"+dataCuenta[i].get('abril')+"','mayo':'"+dataCuenta[i].get('mayo')+"'," +
				                           "'junio':'"+dataCuenta[i].get('junio')+"','julio':'"+dataCuenta[i].get('julio')+"'," +
				                           "'agosto':'"+dataCuenta[i].get('agosto')+"','septiembre':'"+dataCuenta[i].get('septiembre')+"'," +
				                           "'octubre':'"+dataCuenta[i].get('octubre')+"','noviembre':'"+dataCuenta[i].get('noviembre')+"'," +
				                           "'diciembre':'"+dataCuenta[i].get('diciembre')+"','distribuir':'"+dataCuenta[i].get('distribuir')+"',arrFueFin:["; 
							if(dataCuenta[i].get('cadena')!='' || dataCuenta[i].get('cadena')!=undefined){
								cadenajson += dataCuenta[i].get('cadena');
							}
							cadenajson += "]}";
						}
					}
					else
					{
						if(dataCuenta[i].get('estdisfuefin')=='S')	
						{
							if(primero)
							{
								cadenajson += "{'spg_cuenta':'"+dataCuenta[i].get('spg_cuenta')+"','denominacion':'"+trim(dataCuenta[i].get('denominacion'))+"'," +
											   "'monto':'"+dataCuenta[i].get('asignado')+"','enero':'"+dataCuenta[i].get('enero')+"'," +
											   "'febrero':'"+dataCuenta[i].get('febrero')+"','marzo':'"+dataCuenta[i].get('marzo')+"'," +
											   "'abril':'"+dataCuenta[i].get('abril')+"','mayo':'"+dataCuenta[i].get('mayo')+"'," +
											   "'junio':'"+dataCuenta[i].get('junio')+"','julio':'"+dataCuenta[i].get('julio')+"'," +
											   "'agosto':'"+dataCuenta[i].get('agosto')+"','septiembre':'"+dataCuenta[i].get('septiembre')+"'," +
											   "'octubre':'"+dataCuenta[i].get('octubre')+"','noviembre':'"+dataCuenta[i].get('noviembre')+"'," +
											   "'diciembre':'"+dataCuenta[i].get('diciembre')+"','distribuir':'"+dataCuenta[i].get('distribuir')+"',arrFueFin:[";
								if(dataCuenta[i].get('cadena')!='' || dataCuenta[i].get('cadena')!=undefined)
								{
									cadenajson += dataCuenta[i].get('cadena');
								}
								cadenajson += "]}";
								primero = false;
							}
							else
							{
								cadenajson += ",{'spg_cuenta':'"+dataCuenta[i].get('spg_cuenta')+"','denominacion':'"+trim(dataCuenta[i].get('denominacion'))+"'," +
											   "'monto':'"+dataCuenta[i].get('asignado')+"','enero':'"+dataCuenta[i].get('enero')+"'," +
											   "'febrero':'"+dataCuenta[i].get('febrero')+"','marzo':'"+dataCuenta[i].get('marzo')+"'," +
											   "'abril':'"+dataCuenta[i].get('abril')+"','mayo':'"+dataCuenta[i].get('mayo')+"'," +
											   "'junio':'"+dataCuenta[i].get('junio')+"','julio':'"+dataCuenta[i].get('julio')+"'," +
											   "'agosto':'"+dataCuenta[i].get('agosto')+"','septiembre':'"+dataCuenta[i].get('septiembre')+"'," +
											   "'octubre':'"+dataCuenta[i].get('octubre')+"','noviembre':'"+dataCuenta[i].get('noviembre')+"'," +
											   "'diciembre':'"+dataCuenta[i].get('diciembre')+"','distribuir':'"+dataCuenta[i].get('distribuir')+"',arrFueFin:["; 
								if(dataCuenta[i].get('cadena')!='' || dataCuenta[i].get('cadena')!=undefined){
									cadenajson += dataCuenta[i].get('cadena');
								}
								cadenajson += "]}";
							}
						}
					}
				}
			}
			else 
			{
				Ext.MessageBox.show({
					title:'Mensaje',
					msg:'Debe seleccionar la Distribuci&#243;n en la cuenta '+dataCuenta[i].get('spg_cuenta'),
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});
				return false;
			}
		}
		cadenajson += "]}";
		if(valido && numCuenta>0)
		{
			obtenerMensaje('procesar','','Procesando Informaci&#243;n');
        	try
			{
        		var objjson = Ext.util.JSON.decode(cadenajson);
        		if(typeof(objjson) == 'object')
				{
        			var parametros = 'ObjSon=' + cadenajson;
        			Ext.Ajax.request({
        				url : '../../controlador/spg/sigesp_ctr_spg_apertura.php',
        				params : parametros,
        				method: 'POST',
        				timeout: 9999999999999,
        				success: function ( result, request){
	        				datos = result.responseText;
							Ext.Msg.hide();
							var datajson = eval('(' + datos + ')');
							gridCuenta.store.commitChanges();
							if(datajson.raiz.valido==true)
							{	
								Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
							}
							else
							{
								Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
							}
							irCancelar();
        		    	}
        			});
        		}
        	}	
        	catch(e){
        		alert('Verifique los datos, esta insertando caracteres invalidos '+e);
        	}
		}
		else{
			Ext.MessageBox.show({
				title:'Mensaje',
				msg:mensaje,
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO
			});
		}
	}
}

function distribucionMensual(registro) {
	var distribuido = 0;
	var pordistribuir = 0;
	var diciembre = 0;
	var asignado = parseFloat(ue_formato_operaciones(registro.get('asignado')));
	var division = asignado/12;
	if(empresa["estspgdecimal"]==0){
		var division_aux = redondear2(division); //ojo con la funcion
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
		distribuido = (division*11)+diciembre;
	}
	else{
		var newDiv  = redondearNumero(division, 2);
		var newAsi  = redondearNumero(asignado, 2);
		diciembre   = redondearNumero(newAsi - (newDiv*11), 2);
		distribuido = (newDiv*11)+diciembre;
   	}
	pordistribuir=asignado-distribuido;
	pordistribuir=formatoNumericoMostrar(pordistribuir,2,'.',',','','','-','');
	division=formatoNumericoMostrar(division,2,'.',',','','','-','');
	diciembre=formatoNumericoMostrar(diciembre,2,'.',',','','','-','');
	registro.set('pordistribuir',pordistribuir);
	registro.set('enero',division);
	registro.set('febrero',division);
	registro.set('marzo',division);
	registro.set('abril',division);
	registro.set('mayo',division);
	registro.set('junio',division);
	registro.set('julio',division);
	registro.set('agosto',division);
	registro.set('septiembre',division);
	registro.set('octubre',division);
	registro.set('noviembre',division);
	registro.set('diciembre',diciembre);
}

function verificarDistAuto(monto,asignado)
{
	var total = 0;
	var ok = true;
	for(i=1;i<=12;i++)
	{
		total += monto;
		if((total>asignado)&&(i<12))
		{
			ok = false
			break;
		}
	}
	return ok;
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

function distribucionTrimestral(registro)
{
	var distribuido = 0;
	var pordistribuir = 0;
	var diciembre = 0;
	var aux = formatoNumericoMostrar(0,2,'.',',','','','-','');
	var asignado = parseFloat(ue_formato_operaciones(registro.get('asignado')));
	var division = asignado/4;
	if(empresa["estspgdecimal"]==0){
		var division_aux = redondear2(division); //ojo con la funcion
		if(!verificarDistAuto(division_aux,asignado))
		{
			division=redondear3(division);
		}
		else
		{
			division=redondear2(division);
		}
		asignado=redondear2(asignado);
		suma_diciembre=redondear2(division*4);
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
		total=(division*3);
		total_general=total+diciembre;
		total_general=redondear2(total_general);
		resto=(asignado-total_general);
		resto=redondear2(resto);
		diciembre=diciembre+resto;
		distribuido = (division*3)+diciembre;
	}
	else{
		var newDiv  = redondearNumero(division, 2);
		var newAsi  = redondearNumero(asignado, 2);
		diciembre   = redondearNumero(newAsi - (newDiv*3), 2);
		distribuido = (newDiv*3)+diciembre;
   	}
	pordistribuir=asignado-distribuido;
	pordistribuir=formatoNumericoMostrar(pordistribuir,2,'.',',','','','-','');
	division=formatoNumericoMostrar(division,2,'.',',','','','-','');
	diciembre=formatoNumericoMostrar(diciembre,2,'.',',','','','-','');
	registro.set('enero',aux);
	registro.set('febrero',aux);
	registro.set('marzo',division);
	registro.set('abril',aux);
	registro.set('mayo',aux);
	registro.set('junio',division);
	registro.set('julio',aux);
	registro.set('agosto',aux);
	registro.set('septiembre',division);
	registro.set('octubre',aux);
	registro.set('noviembre',aux);
	registro.set('diciembre',diciembre);
}

function buscarFuentes(registro)
{
	var arrCodigos = fieldSetEstructura.obtenerArrayEstructuraFormato();
	var numDet = 0;
	var cadenajson='';
	var JSONObject = {
		'operacion'  : 'buscarFuentesFinanciamiento',
		'codestpro1' : String.leftPad(arrCodigos[0],25,'0'),
		'codestpro2' : String.leftPad(arrCodigos[1],25,'0'),
		'codestpro3' : String.leftPad(arrCodigos[2],25,'0'),
		'codestpro4' : String.leftPad(arrCodigos[3],25,'0'),
		'codestpro5' : String.leftPad(arrCodigos[4],25,'0'),
		'estcla'     : arrCodigos[5],
		'spg_cuenta' : registro.get('spg_cuenta'),
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
			var datos = objeto.raiz;
			if(objeto!=''){
				if(objeto.raiz != null || objeto.raiz !=''){
					var total = datos.length;
					for(var i=0;i<datos.length;i++){
						monto=registro.get('asignado');
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
				        	if(numDet==0){
								cadenajson += "{'codfuefin':'"+datos[i].codfuefin+"','asignado':'"+division+"'}";
							}
							else{
								cadenajson += ",{'codfuefin':'"+datos[i].codfuefin+"','asignado':'"+division+"'}";
							}
							numDet++;
				        }
					}
					registro.set('cadena',cadenajson);
					registro.set('estdisfuefin','S');
				}
			}
		}	
	});
}




