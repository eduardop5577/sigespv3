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

var fromComprobanteInsubsistencias = null; //varibale para almacenar la instacia de objeto de formulario 
var gridDetPresupuestario = null;
var gridComprobante = null;
var Actualizar = null;
var cmboperacion = null;
var operacion = true;
barraherramienta = true;
var prefijocmp = null;

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
		Ext.QuickTips.init();
		Ext.Ajax.timeout=36000000000;
	
	//-----------------------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------------------------------------------------	
        //Creacion del combo prefijo
        var rePrefijo = Ext.data.Record.create([
          {name: 'prefijo'}
     ]);

        dsPrefijo =  new Ext.data.Store({
                reader: new Ext.data.JsonReader({root: 'raiz',id: "prefijo"},rePrefijo)			
        });

        CmbPrefijo = new Ext.form.ComboBox({
                store: dsPrefijo,
                labelSeparator :'',
                fieldLabel:' Comprobante',
                displayField:'prefijo',
                valueField:'prefijo',
                name: 'prefijo',
                width:80,
                listWidth: 80, 
                id:'prefijo',
                typeAhead: true,
                binding:true,
                defaultvalue:'---',
                emptyText:'Prefijo',
                allowBlank:true,
                selectOnFocus:true,
                mode:'local',
                triggerAction:'all',
                valor:'',
                listeners: {'select': function()
                                {
                                    if (Actualizar == null)
                                    {
                                        gridDetPresupuestario.store.removeAll();
                                        Ext.getCmp('totalcuerec').setValue('');
                                        NroComprobante(this.getValue());
                                    }
                                    else
                                    {
                                        Ext.getCmp('prefijo').setValue(prefijocmp);
                                    }
                                }
                }
        });
        //Fin combo prefijo
	
	//creando datastore y columnmodel para la grid de los detalles contables
	var reDetPresupuestario = Ext.data.Record.create([
	    {name: 'spg_cuenta'},
		{name: 'ctaban'},
		{name: 'codban'},
	    {name: 'sc_cuenta'},
	    {name: 'status'},
	    {name: 'codestpro1'},
	    {name: 'codestpro2'},
	    {name: 'codestpro3'},
	    {name: 'codestpro4'},
	    {name: 'codestpro5'},
	    {name: 'estcla'},
	    {name: 'operacion'},
	    {name: 'procede_doc'},
	    {name: 'documento'},
	    {name: 'descripcion'},
	    {name: 'monto'},
		{name: 'codestpro'},
		{name: 'codfuefin'}
	]);
	
	var dsDetPresupuestario =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetPresupuestario)
	});
						
	var cmDetPresupuestario = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
        {header: "<CENTER>Cuenta</CENTER>", width: 50, align: 'center', sortable: true, dataIndex: 'spg_cuenta'},
        {header: "<CENTER>Programatico</CENTER>", width: 50, sortable: true, dataIndex: 'codestpro'},
        {header: "<CENTER>Fuente <br> Financiamiento</CENTER>", width: 50, sortable: true, dataIndex: 'codfuefin'},
        {header: "<CENTER>Documento</CENTER>", width: 70, sortable: true, dataIndex: 'documento', align: 'center'},
	    {header: "<CENTER>Descripci&#243;n</CENTER>", type: 'float', width: 40, align: 'center', sortable: true, dataIndex: 'descripcion'},
		{header: "<CENTER>Procede</CENTER>", width: 30, sortable: true, dataIndex: 'procede_doc', align: 'center'},
		{header: "<CENTER>Operaci&#243;n</CENTER>", type: 'float', width: 40, align: 'center', sortable: true, dataIndex: 'operacion'},
		{header: "<CENTER>Monto</CENTER>", type: 'float', width: 40, align: 'right', sortable: true, dataIndex: 'monto'}
	]);
	//fin del datastore y columnmodel para la grid de bienes
	
	//creando grid para los detalles de bienes
	gridDetPresupuestario = new Ext.grid.EditorGridPanel({
 		width:800,
 		height:200,
		frame:true,
		title:"<H1 align='center'>Detalles Presupuestarios</H1>",
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		style: 'position:absolute;left:10px;top:205px',
		autoScroll:true,
 		border:true,
 		ds: dsDetPresupuestario,
	   	cm: cmDetPresupuestario,
	   	stripeRows: true,
	  	viewConfig: {forceFit:true},
	  	tbar:[{
	        text:'Agregar detalle Gastos',
	        tooltip:'Agregar',
	        iconCls:'agregar',
	        id: 'btagrebie',
	        handler: function(){
				if(Ext.getCmp('comprobante').getValue()=='' || Ext.getCmp('descripcion').getValue()==''
					|| Ext.getCmp('coduac').getValue()==''){
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Debe llenar los campos Comprobante, Descripci&#243;n y Unidad Administradora!!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
				else{
					AgregarPresupuesto();
				}
			}
		},
		{
			text:'Eliminar',
			tooltip:'Eliminar',
			iconCls:'remover',
			id:'btelibie',
			handler: function(){
				arreglo = gridDetPresupuestario.getSelectionModel().getSelections();
				if(arreglo.length >0){
					for(var i = arreglo.length - 1; i >= 0; i--){
						gridDetPresupuestario.getStore().remove(arreglo[i]);
					}
				}
				else{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Debe seleccionar el registro a Eliminar!!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
				acumularTotalGasto(gridDetPresupuestario,'totalcuerec');
			}
		}]
	});
	
	//-------------------------------------------------------------------------------------------------------------------------	

	//Creando el formulario de los totales contables 
	fieldset = new Ext.form.FieldSet({
		width: 360,
		height: 60,
		title: '',
		style: 'position:absolute;left:445px;top:415px',
		cls :'fondo',
		items: [{
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:180,
						items: [{
								xtype:"textfield",
								fieldLabel: 'Total Cuentas de Insubsistencia',
								labelSeparator:'',
								readOnly:true,
								id:'totalcuerec',
								width:150,
								allowBlank:true,
								binding:true,
								defaultvalue:'0',
								hiddenvalue:'',
							}]
						}]
				}]
	});
	//fin creando formulario de totales
	
	//-------------------------------------------------------------------------------------------------------------------------

	var unidad_ejecutora = Ext.data.Record.create([
  		{name: 'coduac'},
  		{name: 'denuac'}
  	]);
  	
  	var dsUnidadEjecutora =  new Ext.data.Store({
  	    reader: new Ext.data.JsonReader({
  		root: 'raiz',             
  		id: "id"},unidad_ejecutora)
  	});
  						
  	var cmcatUnidadEjecutora = new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'coduac'},
          {header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'denuac'}
  	]);
  	//componente campocatalogo para el campo cuentas contables
  	
  	comcampocatUnidadEjecutora = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo de Unidades Administradoras</H1>",
  			anchoformbus: 450,
  			altoformbus:100,
  			anchogrid: 450,
  			altogrid: 400,
  			anchoven: 500,
  			altoven: 400,
  			anchofieldset: 850,
  			datosgridcat: dsUnidadEjecutora,
  			colmodelocat: cmcatUnidadEjecutora,
  			rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
  			parametros: "ObjSon={'operacion': 'buscarUnidadAdm'}",
  			arrfiltro:[{etiqueta:'Codigo',id:'codicuentad',valor:'coduac',longitud:'10'},
  					   {etiqueta:'Denominaci&#243;n',id:'descuentad',valor:'denuac'}],
  			posicion:'position:absolute;left:5px;top:130px',
  			tittxt:'Unidad Administradora',
  			idtxt:'coduac',
  			campovalue:'coduac',
  			anchoetiquetatext:130,
  			anchotext:120,
  			anchocoltext:0.32,
  			idlabel:'denuac',
  			labelvalue:'denuac',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:350,
  			tipbus:'L',
  			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false
  	});
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	//Creando formulario principal 
	var Xpos = ((screen.width/2)-(430));
	var Ypos = ((screen.height/2)-(650/2));
	fromComprobanteInsubsistencias = new Ext.FormPanel({
		title: "<H1 align='center'>Comprobante de Insubsistencia</H1>",
		applyTo: 'formComprobanteInsubsistencia',
		width: 830,
		height: 515,
		style: 'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px', 
		frame: true,
		autoScroll:false,
		items: [{
				xtype:"fieldset", 
			    title:'Datos del Comprobante',
			    style: 'position:absolute;left:10px;top:5px',
			    border:true,
			    width: 800,
			    cls :'fondo',
			    height: 185,
			    items: [{
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:15px;top:20px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 130,
								items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'Procedencia',
										id: 'procede',
										value: 'SPGINS',
										readOnly: true,
										allowBlank:false,
										width:80,
										binding:true,
										defaultvalue:'',
										hiddenvalue:'',
									}]
								}]
			    		},
			    		{
				    	layout:"column",
					    defaults: {border: false},
					    style: 'position:absolute;left:550px;top:20px',
					    border:false,
						items:[{
								layout:"form",
								border:false,
								labelWidth:50,
								items:[{
										xtype:"datefield",
										labelSeparator :'',
										fieldLabel:"Fecha",
										name:'Fecha',
										id:'fecha',
										allowBlank:false,
										width:100,
										binding:true,
										defaultvalue:'1900-01-01',
										hiddenvalue:'',
										value: new Date().format('d-m-Y'),
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
									}]
								}]
			    		},
			    		{
				    	layout:"column",
					    defaults: {border: false},
					    style: 'position:absolute;left:15px;top:50px',
					    border:false,
					    items:[{
                                                    layout:"column",
                                                        border:false,
                                                            labelWidth:100,
                                                            items:[{
                                                                            layout: "form",
                                                                            border: true,
                                                                            labelWidth: 130,
                                                                            items: [CmbPrefijo]
                                                                    },
                                                                    {
                                                                        layout:"form",
                                                                            border:false,
                                                                                labelWidth:10,
                                                                                items:[{
                                                                                                xtype: 'textfield',
                                                                                                labelSeparator :'',
                                                                                                fieldLabel: '',
                                                                                                id: 'comprobante',
                                                                                                autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-._');"},
                                                                                                width: 150,
                                                                                                formatonumerico:false,
                                                                                                binding:true,
                                                                                                hiddenvalue:'',
                                                                                                defaultvalue:'',
                                                                                                allowBlank:false,
                                                                                                listeners:{
                                                                                                        'blur' : function(campo)
                                                                                                        {
                                                                                                                llenarCampoNumdoc(campo.getValue());
                                                                                                        }
                                                                                                }
                                                                                  }]
                                                                     }]
					    	}]
			    		},
			    		{
				    	  layout:"column",
				    	  defaults: {border: false},
				    	  style: 'position:absolute;left:15px;top:80px',
				    	  border:false,
				    	  items:[{
					    		  layout:"form",
					    		  border:false,
					    		  labelWidth:130,
					    		  items:[{
						    			  xtype: 'textarea',
						    			  labelSeparator :'',
						    			  fieldLabel: 'Descripci&#243;n',
						    			  id: 'descripcion',
						    			  width: 600,
						    			  row: 2,
						    			  binding:true,
						    			  hiddenvalue:'',
						    			  defaultvalue:'',
						    			  allowBlank:false,
						    			  autoCreate: {tag: 'textarea', type: 'text', size: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!@%&/\()?-+*[]{}');"},
						    		  }]
			    	  		}]
			    		},comcampocatUnidadEjecutora.fieldsetCatalogo,
			    		{
							xtype: 'hidden',
							id: 'estapro'
						}]
		},gridDetPresupuestario,fieldset]
	});
	
	llenarCmbPrefijos();
}); //fin creando formulario principal con parametros de busqueda y grid de modificaciones
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	//Funcion que llama al catalogo de comprobante de rectificaciones
	function irBuscar(){
		CatalogoComprobante();
	}
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	//Funcion que imprime el comprobante de rectificaciones
	function irImprimir()
	{
		var myJSONObject =
		{
			'operacion'   : 'buscarFormato',
			'sistema'	  : 'SPG',
			'seccion'     : 'REPORTE',
			'variable'    : 'MOD_PRE_INSUBSISTENCIA',
			'valor'		  : 'sigesp_spg_rpp_sol_mod_pre_forma0301.php',
			'tipo'		  : 'C'
		};	
		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request(
		{
			url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
			params: parametros,
			method: 'POST',
			success: function (result, request)
			{ 
				formato = result.responseText;	
				if(Actualizar){
					comprobante = Ext.getCmp('comprobante').getValue();
					procede = Ext.getCmp('procede').getValue();
					fecha = Ext.getCmp('fecha').getValue().format('Y/m/d');
					pagina = "reportes/"+formato+"?comprobante="+comprobante+"&procede="+procede+"&fecha="+fecha;
				    window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
				}
			},
			failure: function (result, request){ 
				Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
			}
		})
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que guardar el comprobante de rectificaciones
	function irGuardar()
	{
		if(Ext.getCmp('procede').getValue()!='SPGINS')
		{
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'No puede editar un comprobante, que no fue generado por este modulo !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO
			});
		}
		else
		{
			var cadjson = '';
		    var valido = true;
		    var evento = 'INSERT';  
		    if(Actualizar!=null)
			{
		    	var evento = 'UPDATE';
		    }
	        cadjson = "{'operacion':'guardar','codsis':'"+sistema+"','nomven':'"+vista+"','evento':'"+evento+"',"+getJsonFormulario(fromComprobanteInsubsistencias);
	        var monto = parseFloat(ue_formato_operaciones(Ext.getCmp('totalcuerec').getValue()));
	        cadjson += ",monto:'"+monto+"','detallesPresupuestario':[";	
	        if(valido)
			{
	        	var numDetalle = 0;
	        	if(gridDetPresupuestario.getStore().getCount()!=0)
				{
	        		gridDetPresupuestario.store.each(function (reDetPre){
			    		var monto = parseFloat(ue_formato_operaciones(reDetPre.get('monto')));
						if(monto!=0)
						{
							var codfuefin = '--';
							if(reDetPre.get('codfuefin')!='')
							{
								codfuefin=reDetPre.get('codfuefin');
							}
							if(numDetalle==0)
							{
								cadjson +="{'spg_cuenta':'"+reDetPre.get('spg_cuenta')+"','procede_doc':'"+reDetPre.get('procede_doc')+"'," +
										   "'documento':'"+reDetPre.get('documento')+"','operacion':'"+reDetPre.get('operacion')+"'," +
										   "'codfuefin':'"+codfuefin+"','codestpro1':'"+reDetPre.get('codestpro1')+"'," +
										   "'codestpro2':'"+reDetPre.get('codestpro2')+"','codestpro3':'"+reDetPre.get('codestpro3')+"'," +
										   "'codestpro4':'"+reDetPre.get('codestpro4')+"','codestpro5':'"+reDetPre.get('codestpro5')+"'," +
										   "'estcla':'"+reDetPre.get('estcla')+"','descripcion':'"+reDetPre.get('descripcion')+"'," +
										   "'monto':'"+reDetPre.get('monto')+"'}";
							}
							else
							{
								cadjson +=",{'spg_cuenta':'"+reDetPre.get('spg_cuenta')+"','procede_doc':'"+reDetPre.get('procede_doc')+"'," +
										   "'documento':'"+reDetPre.get('documento')+"','operacion':'"+reDetPre.get('operacion')+"'," +
										   "'codfuefin':'"+codfuefin+"','codestpro1':'"+reDetPre.get('codestpro1')+"'," +
										   "'codestpro2':'"+reDetPre.get('codestpro2')+"','codestpro3':'"+reDetPre.get('codestpro3')+"'," +
										   "'codestpro4':'"+reDetPre.get('codestpro4')+"','codestpro5':'"+reDetPre.get('codestpro5')+"'," +
										   "'estcla':'"+reDetPre.get('estcla')+"','descripcion':'"+reDetPre.get('descripcion')+"'," +
										   "'monto':'"+reDetPre.get('monto')+"'}";
							}
							numDetalle++;
						}
		    		});
	        	}
	        }
	        cadjson += "]}";
	        if(valido){
	        	obtenerMensaje('procesar','','Procesando Informaci&#243;n');
	        	try{
	        		var objjson = Ext.util.JSON.decode(cadjson);
	        		if(typeof(objjson) == 'object'){
	        			var parametros = 'ObjSon=' + cadjson;
	        			Ext.Ajax.request({
	        				url : '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
	        				params : parametros,
	        				method: 'POST',
	        				success: function ( result, request){
		        				datos = result.responseText;
								Ext.Msg.hide();
								var datajson = eval('(' + datos + ')');
								if(datajson.raiz.valido==true)
								{	
									Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
									irNuevo();
								}
								else
								{
									Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
								}
	        		    	}
	        			});
	        		}
	        	}	
	        	catch(e){
	        		alert('Verifique los datos, esta insertando caracteres invalidos '+e);
	        	}
	        }
		}
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que limpia la pantalla para generar un nuevo comprobante de rectificaciones
	function irNuevo(){
		limpiarFormulario(fromComprobanteInsubsistencias);
		llenarCmbPrefijos();
		gridDetPresupuestario.store.removeAll();
		Ext.getCmp('totalcuerec').setValue('');
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que elimina el comprobante de rectificaciones
	function irEliminar(){
		if(Ext.getCmp('procede').getValue()!='SPGINS'){
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'No puede editar un comprobante, que no fue generado por este modulo !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO
			});
		}
		else {
			if (Ext.getCmp('estapro').getValue() == '1') {
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'El comprobante fue aprobado, no puede ser eliminado !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});
			} 
			else {
				var valido = true;
				function respuesta(btn){
					if(btn=='yes'){
						obtenerMensaje('procesar','','Procesando Informaci&#243;n');
						var cadjson = "{'operacion':'eliminar','codsis':'"+sistema+"','nomven':'"+vista+"',"+getJsonFormulario(fromComprobanteInsubsistencias);
						cadjson += "}";
				        if(valido){
				        	try{
				        		var objjson = Ext.util.JSON.decode(cadjson);
				        		if(typeof(objjson) == 'object'){
				        			var parametros = 'ObjSon=' + cadjson;
				        			Ext.Ajax.request({
				        				url : '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
				        				params : parametros,
				        				method: 'POST',
				        				success: function ( result, request){
					        				datos = result.responseText;
											Ext.Msg.hide();
											var datajson = eval('(' + datos + ')');
											if(datajson.raiz.valido==true)
											{	
												Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
												irNuevo();
											}
											else
											{
												Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
											}
				        		    	}
				        			});
				        		}
				        	}	
				        	catch(e){
				        		alert('Verifique los datos, esta insertando caracteres invalidos '+e);
				        	}
				        }
					}
				}	
				if(Actualizar){
					Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', respuesta); 
				}
				else{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'El registro debe estar guardado para poder eliminarlo, verifique por favor',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					}); 
				}
			}
		}
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	function acumularTotalGasto(grid,campo){
		var monto = 0;
		var montotal = 0;
		grid.store.each(function (reDet){
			var monto = reDet.get('monto');
			monto = parseFloat(ue_formato_operaciones(monto));
			montotal += monto; 
			monto = 0;
	    }); //totalpresupuesto
		Ext.getCmp(campo).setValue(formatoNumericoMostrar(montotal,2,'.',',','','','-',''));
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------
	
	//Funcion que completa el comprbante con ceros para alcanzar la longitud maxima
	function llenarCampoNumdoc(campo)
	{
		var myJSONObject = {
				"operacion" :'validar_nrodocumento',
				"numdoc"    : campo,
				"procede"    : Ext.getCmp('procede').getValue()
		};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) {
				var data = result.responseText;
				var datajson = eval('(' + data + ')');
				if(datajson.raiz.valido==true) {
					Ext.Msg.show({
						title:'Mensaje',
						msg: datajson.raiz.mensaje,
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
					Ext.getCmp('comprobante').setValue('');
				}
				else {
					Ext.getCmp('comprobante').setValue(datajson.raiz.numdoc);
				}
	    	}
		});
	}

	//----------------------------------------------------------------------------------------------------------------------------------
	
	//Funcion que completa el comprbante con ceros para alcanzar la longitud maxima
	function CatalogoComprobante()
	{
		//creando datastore y columnmodel para la grid de los comprobantes contables
		var reComprobante = Ext.data.Record.create([
		    {name: 'comprobante'},
			{name: 'procede'},
			{name: 'descripcion'},
		    {name: 'fecha'},
		    {name: 'coduac'},
		    {name: 'codban'},
		    {name: 'ctaban'},
		    {name: 'tipo_destino'},
		    {name: 'cod_pro'},
		    {name: 'ced_bene'},
		    {name: 'total'},
		    {name: 'estapro'}
		]);
		
		var dsComprobante =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reComprobante)
		});
							
		var cmComprobante = new Ext.grid.ColumnModel([
	        {header: "<CENTER>Comprobante</CENTER>", width: 60, align: 'center', sortable: true, dataIndex: 'comprobante'},
	        {header: "<CENTER>Descripci&#243;n</CENTER>", width: 60, sortable: true, dataIndex: 'descripcion'},
	        {header: "<CENTER>Procede</CENTER>", width: 30, sortable: true, dataIndex: 'procede', align: 'center'},
			{header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecha', align: 'center'},
			{header: "<CENTER>Proveedor</CENTER>", type: 'float', width: 40, align: 'center', sortable: true, dataIndex: 'cod_pro'},
			{header: "<CENTER>Beneficiario</CENTER>", type: 'float', width: 40, align: 'right', sortable: true, dataIndex: 'ced_bene'},
			{header: "<CENTER>Monto</CENTER>", type: 'float', width: 40, align: 'right', sortable: true, dataIndex: 'total'},
		]);
		//fin del datastore y columnmodel para la grid de bienes
		
		//creando grid para los detalles de bienes
		gridComprobante = new Ext.grid.GridPanel({
	 		width:780,
	 		height:250,
			frame:true,
			title:"",
			style: 'position:absolute;left:15px;top:180px',
			autoScroll:true,
	 		border:true,
	 		ds: dsComprobante,
	   		cm: cmComprobante,
	   		stripeRows: true,
	  		viewConfig: {forceFit:true}
		});
		
		//Metodo que realiza cambios despues de editar la grid de los comprobantes
		gridComprobante.on({
			'rowdblclick': {
				fn: function(grid, numFila, evento){
					var registro = grid.getStore().getAt(numFila);
					aceptar(registro);
	 		    }
			}
		});
		
		var	formVentanaCatalogo = new Ext.FormPanel({
			width: 830,
			height: 470,
			style: 'position:absolute;left:5px;top:10px',
			frame: true,
			autoScroll:false,
			items: [{
					xtype:"fieldset", 
					title:'Datos del Comprobante',
					style: 'position:absolute;left:15px;top:10px',
					border:true,
					cls: 'fondo',
					width: 780,
					height: 140,
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
											fieldLabel: 'Comprobante',
											id: 'numcomprobante',									
											width: 170,
											autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
											changeCheck: function(){
												var textvalor = this.getValue();
												dsComprobante.filter('comprobante',textvalor,true);
												if(String(textvalor) !== String(this.startValue)){
													this.fireEvent('change', this, textvalor, this.startValue);
												} 
											}, 
											initEvents: function(){
												AgregarKeyPress(this);
											}
										}]
								}]
							},
							{
							layout:"column",
							defaults: {border: false},
							style: 'position:absolute;left:15px;top:50px',  //520/20
							border:false,
							items:[{
									layout:"form",
									border:false,
									labelWidth:100,
									items:[{
											xtype: 'textfield',
											fieldLabel: 'Procedencia',
											labelSeparator :'',
											id: 'procedencia',
											readOnly:true,
											width: 170,
											value:'SPGINS'
										}]
								}]
							},
							{
					    	layout:"column",
						    defaults: {border: false},
						    style: 'position:absolute;left:450px;top:20px',  //520/20
						    border:false,
							items:[{
									layout:"form",
									border:false,
									labelWidth:100,
									items:[{
											xtype:"datefield",
											labelSeparator :'',
											fieldLabel:"Fecha Desde",
											name:'Fecha',
											id:'fecdesde',
											allowBlank:false,
											width:100,
											binding:true,
											defaultvalue:'1900-01-01',
											hiddenvalue:'',
											value: new Date().format('01-m-Y'),
											autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
										}]
									}]
							},
							{
					    	layout:"column",
						    defaults: {border: false},
						    style: 'position:absolute;left:450px;top:50px', //520/50
						    border:false,
							items:[{
									layout:"form",
									border:false,
									labelWidth:100,
									items:[{
											xtype:"datefield",
											labelSeparator :'',
											fieldLabel:"Hasta",
											name:'Fecha',
											id:'fechasta',
											allowBlank:false,
											width:100,
											binding:true,
											defaultvalue:'1900-01-01',
											hiddenvalue:'',
											value: new Date().format('d-m-Y'),
											autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
										}]
									}]
							},gridComprobante,
							{
							layout:"column",
							defaults: {border: false},
							style: 'position:absolute;left:670px;top:85px', 
							border:false,
							items:[{
									layout:"form",
									border:false,
									items:[{
											xtype: 'button',
											fieldLabel: '',
											id: 'btagregar',
											text: 'Buscar',
											iconCls: 'menubuscar',
											handler: function(){
												if(Ext.getCmp('fecdesde').getValue().format('Y-m-d')<=Ext.getCmp('fechasta').getValue().format('Y-m-d')){
													obtenerMensaje('procesar','','Buscando Datos');
												//Buscar ordenes de compra
								   				var JSONObject = {
								   					'operacion'   : 'buscarComprobantes',
								   					'comprobante' : Ext.getCmp('numcomprobante').getValue(),
								   					'procede'     : 'SPGINS',
								   					'fecdesde'    : Ext.getCmp('fecdesde').getValue().format('Y-m-d'),
								   					'fechasta'    : Ext.getCmp('fechasta').getValue().format('Y-m-d'),
								   				}
					   				
								   				var ObjSon = JSON.stringify(JSONObject);
								   				var parametros = 'ObjSon='+ObjSon; 
								   				Ext.Ajax.request({
								   					url : '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
								   					params : parametros,
								   					method: 'POST',
								   					success: function ( resultado, request){
								   						Ext.Msg.hide();
								   						var datos = resultado.responseText;
								   						var objCmp = eval('(' + datos + ')');
								   						if(objCmp!=''){
								   							if(objCmp!='0'){
								   								if(objCmp.raiz == null || objCmp.raiz ==''){
								   									Ext.MessageBox.show({
													 					title:'Advertencia',
													 					msg:'No existen datos para mostrar',
													 					buttons: Ext.Msg.OK,
													 					icon: Ext.MessageBox.WARNING
													 				});
								   									gridComprobante.store.removeAll();
								   								}
								   								else{
								   									gridComprobante.store.loadData(objCmp);
								   								}
								   							}
								   							else{
								   								Ext.MessageBox.show({
													 				title:'Advertencia',
													 				msg:'',
													 				buttons: Ext.Msg.OK,
													 				icon: Ext.MessageBox.WARNING
													 			});
								   							}
								   						}
							   						}//fin del success	
								   				});//fin del ajax request
												}
												else{
													Ext.MessageBox.show({
										 				title:'Mensaje',
										 				msg:'El rango de fechas no es correcto !!!',
										 				buttons: Ext.Msg.OK,
										 				icon: Ext.MessageBox.WARNING
										 			});
												}
										}
										}]
									}]
							},
							{
							layout:"column",
							defaults:{border: false},
							style:'position:absolute;left:600px;top:390px', 
							border:false,
							items:[{
									buttons: [{
										text:'Aceptar',  
										handler: function()
										{
											var registro = gridComprobante.getSelectionModel().getSelected();	
										    aceptar(registro);
										}
									},
									{
										text: 'Salir',
										handler: function()
										{
											ventanaEstructura.destroy();
										}
									}]
								}]
							}]
					}]
		});
		formVentanaCatalogo.add(gridComprobante);
	    var ventanaEstructura = new Ext.Window({
	    	width:840, 
	        height:490,
	        closable:true,
	    	border:false,
	    	modal: true,
	    	frame:true,
	    	title:"<H1 align='center'>Cat&#225;logo de Comprobante Insubsistencia</H1>",
	    	items:[formVentanaCatalogo], 
	    });
	    
	    ventanaEstructura.show();
	    
	    //function que setea los datos en el formulario principal
	    function aceptar(registro)
	    {
	    	setDataFrom(fromComprobanteInsubsistencias,registro);
                prefijocmp = registro.get('comprobante');
                prefijocmp = prefijocmp.substring(0,6);
                Ext.getCmp('prefijo').setValue(prefijocmp);                
	    	Actualizar=true;
	    	buscarDetallesPresupuestarios(registro);
			gridComprobante.destroy();
			ventanaEstructura.destroy();
	    }
	    
	    function buscarDetallesPresupuestarios(registro)
	    {
	    	var reDetPre = Ext.data.Record.create([
	    	    {name: 'spg_cuenta'},
	    	    {name: 'codestpro1'},
	    	    {name: 'codestpro2'},
	    	    {name: 'codestpro3'},
	    	    {name: 'codestpro4'},
	    	    {name: 'codestpro5'},
	    	    {name: 'estcla'},
	    	    {name: 'operacion'},
	    	    {name: 'procede_doc'},
	    	    {name: 'documento'},
	    	    {name: 'descripcion'},
	    	    {name: 'monto'},
	    	    {name: 'codestpro'},
	    	    {name: 'codfuefin'}
	    	]);
	    	obtenerMensaje('procesar','','Buscando Datos');
			//Buscar los detalles contables
			var JSONObject = {
				'operacion'   : 'buscarDetallesPresupuestario',
				'comprobante' : registro.get('comprobante'),
				'procede'     : registro.get('procede'),
				'fecha'       : registro.get('fecha')
			}
			var ObjjSon=Ext.util.JSON.encode(JSONObject);
  			var parametros ='ObjSon='+ObjjSon;
  			Ext.Ajax.request({
  				url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
  				params: parametros,
  				method: 'POST',
  				success: function ( result, request )
  				{ 
					Ext.Msg.hide();
					var resultado = result.responseText;
					var objDatos = eval('(' + resultado + ')');
					var datos = objDatos.raiz;
					if (objDatos != ""){
						var montouno = 0;
						var montodos = 0;
						gridDetPresupuestario.store.removeAll();
						for(var j = 0; j < datos.length; j++){
							var detpreInt = new reDetPre({
							'spg_cuenta' :datos[j].spg_cuenta,
							'operacion'  :datos[j].operacion,
							'procede_doc':datos[j].procede_doc,
							'codestpro1' :datos[j].codest1,
							'codestpro2' :datos[j].codest2,
							'codestpro3' :datos[j].codest3,
						    'codestpro4' :datos[j].codest4,
						    'codestpro5' :datos[j].codest5,
						    'estcla'     :datos[j].estcla,
						    'codestpro'  :datos[j].codestpro,
						    'documento'  :datos[j].documento,
						    'descripcion':datos[j].descripcion,
						    'monto'      :datos[j].monto,
						    'codfuefin'  :datos[j].codfuefin
							});
							var monto = datos[j].monto;
								monto = parseFloat(ue_formato_operaciones(monto));
								montouno += monto;
								gridDetPresupuestario.store.insert(0,detpreInt);
						}
						Ext.getCmp('totalcuerec').setValue(formatoNumericoMostrar(montouno,2,'.',',','','','-',''));
					}
				},
  				failure: function ( result, request){ 
  						Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
  				}//fin del success
  			});//fin del ajax request
	    }
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------
	
	//INICIO DEL FORMULARIO AGREGAR PRESUPUESTO//
	function AgregarPresupuesto()
	{
		var fieldSetEstOrigen = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
			titform: 'Estructura Presupuestaria',
			mostrarDenominacion:true,
			sinFuente:false,
			sinCuenta:false,
			idtxt:'comfsest',
		});
		//agregarListenersEstructura(fieldSetEstructura);
		
		//Creacion del formulario de agregar presupuesto
		var frmAgregarPresupuesto = new Ext.FormPanel({
			width: 870,
			height: 570, 
			frame: true,
			autoScroll:false,
			items:[{
					xtype:"fieldset", 
					title:'Datos del Documento',
					border:true,
					width: 850,
					height: 180,
					cls: 'fondo',
					items:[{
							style:'position:absolute;left:15px;top:15px',
							layout:"column",
							defaults:{border: false},
							items: [{
									layout:"form",
									border:false,
									labelWidth:215,
									items: [{
											xtype:'textfield',
											labelSeparator:'',
											fieldLabel:'Documento',
											name:'docgasto',
											id:'agrdocgasto',	
											autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
											width: 185,
											value:Ext.getCmp('comprobante').getValue(),
										}]
									}]
							},
							{
							style:'position:absolute;left:15px;top:45px',
							layout:"column",
							defaults:{border: false},
							items: [{
									layout:"form",
									border:false,
									labelWidth:215,
									items: [{
											xtype:'textfield',
											labelSeparator:'',
											fieldLabel:'Descripci&#243;n',
											autoCreate: {tag: 'input', type: 'text', size: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!@%&/\()?-+*[]{}');"},
											name:'desgasto',
											id:'catdesgasto',									
											width: 600,
											value:Ext.getCmp('descripcion').getValue(),
										}]
									}]
							},
							{
							style:'position:absolute;left:15px;top:75px',
							layout:"column",
							defaults:{border: false},
							items: [{
									layout:"form",
									border:false,
									labelWidth:215,
									items: [{
											xtype:'textfield',
											labelSeparator:'',
											fieldLabel:'Procedencia',
											name:'progasto',
											id:'catprogasto',										
											width: 185,
											readOnly:true,
											value:'SPGINS'
										}]
									}]
							},
							{
							style:'position:absolute;left:15px;top:105px',
							layout:"column",
							defaults:{border: false},
							items: [{
									layout:"form",
									border:false,
									labelWidth:215,
									items: [{
											xtype:'textfield',
											labelSeparator:'',
											fieldLabel:'Operaci&#243;n',
											name:'operacion',
											id:'opecmp',										
											width: 185,
											readOnly:true,
											value:'DI'
										}]
									}]
							},
							{
							style:'position:absolute;left:15px;top:135px',
							layout:"column",
							defaults:{border: false},
							items: [{
									layout:"form",
									border:false,
									labelWidth:215,
									items: [{
											xtype:'textfield',
											labelSeparator:'',
											fieldLabel:'Monto',
											name:'mongasto',
											id:'catmongasto',											
											width: 185,
											autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'0123456789.');"},
											listeners:{
												'blur':function(objeto)
												{
													var numero = objeto.getValue();
													valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
													objeto.setValue(valor);
												},
												'focus':function(objeto)
												{
													var numero = formatoNumericoEdicion(objeto.getValue());
													objeto.setValue(numero);
												}
											}
									}]
								}]
							}]
			},fieldSetEstOrigen.fsEstructura]  
		});

		var ventanaAgregarPresupuesto = new Ext.Window({
			title: "<H1 align='center'>Entrada de Solicitud de Modificaci&#243;n Presupuestaria por Actividad</H1>",
			width:880,
			height:590, 
			modal: true,
			closable:false,
			plain: false,
			frame:true,
			items:[frmAgregarPresupuesto],
			tbar: [{
				text:'Aceptar',
				iconCls: 'bmenuprocesar',
				handler: function(){
					var arrCodigos = fieldSetEstOrigen.obtenerArrayEstructura();
					var estructura = fieldSetEstOrigen.obtenerEstructuraFormato();
					if(Ext.getCmp('agrdocgasto').getValue()=='' || Ext.getCmp('catdesgasto').getValue()=='' ||
					   Ext.getCmp('catmongasto').getValue()==''	|| Ext.getCmp('codcuentacomfsest').getValue()==''){
						Ext.Msg.show({
							title:'Mensaje',
							msg:'Debe completar todos los datos',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
					else{
						var reDetGas = Ext.data.Record.create([
						   	{name: 'spg_cuenta'},
						   	{name: 'denominacion'},
						   	{name: 'procede'},
						   	{name: 'operacion'},
						   	{name: 'documento'},
						   	{name: 'monto'},
						]);
						var detgasInt = new reDetGas({
							'codfuefin':arrCodigos[6],
							'spg_cuenta':arrCodigos[7],
							'documento':Ext.getCmp('agrdocgasto').getValue(),
							'descripcion':Ext.getCmp('catdesgasto').getValue(),
							'procede_doc':Ext.getCmp('catprogasto').getValue(),
							'operacion':Ext.getCmp('opecmp').getValue(),
							'monto':Ext.getCmp('catmongasto').getValue(),
							'codestpro':estructura,
							'codestpro1':arrCodigos[0],
							'codestpro2':arrCodigos[1],
							'codestpro3':arrCodigos[2],
							'codestpro4':arrCodigos[3],
							'codestpro5':arrCodigos[4],
							'estcla':arrCodigos[5],
						});
						if(gridDetPresupuestario.getStore().getCount()==0){
							gridDetPresupuestario.store.insert(0,detgasInt);
						}
						else{
							var entro=false;
							gridDetPresupuestario.store.each(function (reDetGas){
								if(reDetGas.get('spg_cuenta')==arrCodigos[7] && reDetGas.get('codestpro1')==arrCodigos[0] &&
										reDetGas.get('codestpro2')==arrCodigos[1] && reDetGas.get('codestpro3')==arrCodigos[2] &&
										reDetGas.get('codestpro4')==arrCodigos[3] && reDetGas.get('codestpro5')==arrCodigos[4] &&
										reDetGas.get('estcla')==arrCodigos[5] && reDetGas.get('documento')==Ext.getCmp('agrdocgasto').getValue()){
									Ext.Msg.show({
										 title:'Mensaje',
										 msg: 'El Detalle Presupuestario ya existe...',
										 buttons: Ext.Msg.OK,
										 icon: Ext.MessageBox.INFO
									 });
									entro=true;
								}
							})
							if(!entro){
								gridDetPresupuestario.store.insert(0,detgasInt);
							}
						}
						acumularTotalGasto(gridDetPresupuestario,'totalcuerec');
						//ventanaAgregarPresupuesto.close();
					}	
				}
			},
			{
		   		text: 'Salir',
		   		iconCls: 'menusalir',
	   			handler:function(){
					ventanaAgregarPresupuesto.close();
	   		    }
	   		}]
		});
		ventanaAgregarPresupuesto.show();
	}
	//FIN DEL FORMULARIO AGREGAR PRESUPUESTO//
    
	//Funcion que para buscar el consecutivo nrocomprobante
	function NroComprobante(prefijo)
	{
		if(!tbadministrativo)
		{
			var myJSONObject = {
				"operacion" :'verificar_prefijo',
				"procede" :'SPGINS'	
			};
			var ObjSon= JSON.stringify(myJSONObject);
			var parametros ='ObjSon='+ObjSon;
			Ext.Ajax.request({
				url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
				params: parametros,
				method: 'POST',
				success: function ( result, request )
				{ 
		    		var prefijo = result.responseText;
	    			if((prefijo == "1")&&(!tbadministrativo))
					{
						Ext.getCmp('comprobante').setDisabled(true);
					}
		    		else
					{
						Ext.getCmp('comprobante').setDisabled(false);
		    		}
				}
			});
		}

		var myJSONObject = {
			"operacion" :'cargar_nrodocumento',
			"procede" :'SPGINS',
                        "prefijo" : prefijo 	
		};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) { 
	    		var numdoc = result.responseText;
	    		if(numdoc == "-2"){
	    			Ext.Msg.show({
	    				title:'Mensaje',
	    				msg: 'El sistema tiene configurado el uso de prefijo y este usuario no tiene uno asignado !!!',
	    				buttons: Ext.Msg.OK,
	    				fn: function(){ location.href = 'sigesp_vis_spg_inicio.html'},
	    				icon: Ext.MessageBox.INFO
	    			});
	    		}
	    		else if (numdoc != "-1"){
	    			Ext.getCmp('comprobante').setValue(numdoc);
	    		}
			}
		});
	}
        //Funcion que agrega los datos al combo prefijos
        function llenarCmbPrefijos()
	{
            if (Actualizar==null)
            {
                var myJSONObject ={
                                "operacion": 'buscarPrefijosUsuarios',
                                "procede" : 'SPGINS'
                };	
                var ObjSon=JSON.stringify(myJSONObject);
                var parametros = 'ObjSon='+ObjSon; 
                Ext.Ajax.request({
                        url : '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
                        params : parametros,
                        method: 'POST',
                        success: function (resultado, request)
                        { 
                                var datosest = resultado.responseText;
                                var prefijo = "";
                                if(datosest!='')
                                {
                                        prefijo = datosest.substring(21, 27);
                                        var DatosEst = eval('(' + datosest + ')');
                                }
                                dsPrefijo.loadData(DatosEst);                        
                                Ext.getCmp('prefijo').setValue(prefijo);
                                NroComprobante(prefijo);
                        }//fin del success
                });//fin del ajax request
            }
	}
