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

var fromCierreEjercicio = null; //varibale para almacenar la instacia de objeto de formulario 
var gridDetContables = null;
var Actualizar = null;
var frmTotalContable = null;
barraherramienta    = true;

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
		Ext.QuickTips.init();
		Ext.Ajax.timeout=36000000000;
	//-----------------------------------------------------------------------------------------------
	
	//creando datastore y columnmodel para la grid de los detalles contables
	var reDetContables = Ext.data.Record.create([
	    {name: 'codban'},
		{name: 'ctaban'},
		{name: 'canart'},
	    {name: 'sc_cuenta'},
	    {name: 'denominacion'},
	    {name: 'status'},
	    {name: 'procede_doc'},
	    {name: 'documento'},
	    {name: 'descripcion'},
	    {name: 'monto'},
	    {name: 'debhab'}
	    
	]);
	
	var dsDetContables =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetContables)
	});
						
	var cmDetContables = new Ext.grid.ColumnModel([
        {header: "<CENTER>Cuenta</CENTER>", width: 60, align: 'center', sortable: true, dataIndex: 'sc_cuenta'},
        {header: "<CENTER>Descripción</CENTER>", width: 80, sortable: true, dataIndex: 'descripcion'},
        {header: "<CENTER>Procede</CENTER>", width: 30, sortable: true, dataIndex: 'procede_doc', align: 'center'},
		{header: "<CENTER>Documento</CENTER>", width: 40, sortable: true, dataIndex: 'documento', align: 'center'},
		{header: "<CENTER>Operación</CENTER>", type: 'float', width: 40, align: 'center', sortable: true, dataIndex: 'debhab', renderer: MostrarOperacion},
		{header: "<CENTER>Monto</CENTER>", type: 'float', width: 40, align: 'right', sortable: true, dataIndex: 'monto'},
		
	]);
	//fin del datastore y columnmodel para la grid de bienes
	
	//creando grid para los detalles de bienes
	gridDetContables = new Ext.grid.GridPanel({
 		width:800,
 		height:200,
		frame:true,
		title:"<H1 align='center'>Detalles Contables</H1>",
		style: 'position:absolute;left:10px;top:205px',
		autoScroll:true,
 		border:true,
 		ds: dsDetContables,
   		cm: cmDetContables,
   		stripeRows: true,
  		viewConfig: {forceFit:true},
	});
	
	//-------------------------------------------------------------------------------------------------------------------------	

	//Creando el formulario de totales
	fieldset = new Ext.form.FieldSet({
		width: 280,
		height: 115,
		title: 'Totales',
		style: 'position:absolute;left:520px;top:415px',
		cls :'fondo',
		items: [{
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						laberWidth: 100,
						items: [{
								xtype:"textfield",
								fieldLabel: 'Total Debe',
								labelSeparator:'',
								readOnly:true,
								id:'totaldeb',
								width:150
							}]
						}]
				},
				{
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						laberWidth: 100,
						items: [{												
								xtype:"textfield",
								fieldLabel: 'Total Haber',
								readOnly:true,
								labelSeparator:'',
								id:'totalhab',
								width:150
							}]
						}]
				},
				{
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						laberWidth: 100,
						items: [{
								xtype:"textfield",
								fieldLabel: 'Diferencia',
								readOnly:true,
								labelSeparator:'',
								id:'diferencia',
								width:150
							}]

						}]
				}]
	});
	//fin creando formulario de totales
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	//Creando formulario principal 
	var Xpos = ((screen.width/2)-(430));
	var Ypos = ((screen.height/2)-(650/2));
	fromCierreEjercicio = new Ext.FormPanel({
		title: "<H1 align='center'>Cierre de Ejercicio</H1>",
		applyTo: 'formCierreEjercicio',
		width: 840,
		height: 500,
		style: 'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  //'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		frame: true,
		autoScroll:true,
		items: [{
				xtype:"fieldset", 
			    title:'Datos del Comprobante',
			    style: 'position:absolute;left:10px;top:5px',
			    border:true,
			    width: 800,
			    cls :'fondo',
			    height: 190,
			    items: [{
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
										fieldLabel: 'Procedencia',
										id: 'procede',
										value: 'SCGCIE',
										readOnly: true,
										allowBlank:false,
										width:100,
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
										xtype:'textfield',
										labelSeparator :'',
										fieldLabel:"Fecha",
										name:'Fecha',
										id:'fecha',
										readOnly:true,
										allowBlank:false,
										width:100,
										binding:true,
										defaultvalue:'1900-01-01',
										hiddenvalue:'',
										value: new Date().format('d-m-Y')
									}]
								}]
			    		},
					    {
				    	layout:"column",
					    defaults: {border: false},
					    style: 'position:absolute;left:15px;top:50px',
					    border:false,
					    items:[{
						    	layout:"form",
							    border:false,
								labelWidth:100,
								items:[{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'Comprobante',
										readOnly:true,
										id: 'comprobante',
										width: 150,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'',
										allowBlank:true,
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
				    		  labelWidth:100,
				    		  items:[{
					    			  xtype: 'textarea',
					    			  labelSeparator :'',
					    			  fieldLabel: 'Descripci&#243;n',
					    			  id: 'descripcion',
					    			  readOnly:true,
					    			  width: 600,
					    			  row: 2,
					    			  binding:true,
					    			  hiddenvalue:'',
					    			  defaultvalue:'',
					    			  allowBlank:true,
					    		  }]
		    	  		}]
				      },
				      {
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:120px;top:150px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 100,
								items: [{
										xtype: 'checkbox',
										labelSeparator :'',
										fieldLabel: 'Cierre Semestral',
										id: 'estciesem',
										readOnly: true,
										inputValue:0,
										allowBlank:true
									}]
								}]
				      },
				      {
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:270px;top:150px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 100,
								items: [{
										xtype: 'checkbox',
										labelSeparator :'',
										fieldLabel: 'Primer Semestre',
										readOnly: true,
										id: 'ciesem1',
										inputValue:1,
										allowBlank:true
									}]
								}]
				      },
				      {
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:420px;top:150px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 110,
								items: [{
										xtype: 'checkbox',
										labelSeparator :'',
										fieldLabel: 'Segundo Semestre',
										readOnly: true,
										id: 'ciesem2',
										inputValue:1,
										allowBlank:true
									}]
								}]
				      },
				      {
						xtype: 'hidden',
						id: 'ctaban',
						binding:true,
						hiddenvalue:'',
						defaultvalue:'-------------------------'
				      },
				      {
						xtype: 'hidden',
						id: 'codban',
						binding:true,
						hiddenvalue:'',
						defaultvalue:'---'
				      },
				      {
						xtype: 'hidden',
						id: 'tipo_destino',
						binding:true,
						hiddenvalue:'',
						defaultvalue:'-'
				      },
				      {
						xtype: 'hidden',
						id: 'cod_pro',
						binding:true,
						hiddenvalue:'',
						defaultvalue:'----------'
				      },
				      {
						xtype: 'hidden',
						id: 'ced_bene',
						binding:true,
						hiddenvalue:'',
						defaultvalue:'----------'
				      }]
				},gridDetContables,fieldset]
	});	
	verificarEstCieSem();
	verificarCierre();
}); //fin creando formulario principal con parametros de busqueda y grid de modificaciones
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que limpia la pantalla para generar un nuevo comprobante contable
	function irNuevo(){
		limpiarFormulario(fromCierreEjercicio);
		gridDetContables.store.removeAll();
		Actualizar=null;
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que procesa el comprobante
	function irProcesar()
	{
		var procesando = 'Procesando Cierre de Ejercicio';
		if(Ext.getCmp('estciesem').checked)
		{
			procesando = 'Procesando Cierre del Primer Trimestre al '+new Date().format('30/06/Y');
			if(Ext.getCmp('ciesem1').checked)
			{
				procesando = 'Procesando Cierre del Segundo Trimestre al '+new Date().format('31/12/Y');
			}
		}
		var valido=true;
		if(Actualizar)
		{
			if(Ext.getCmp('estciesem').checked)
			{
				if(Ext.getCmp('ciesem1').checked && Ext.getCmp('ciesem2').checked)
				{
					Ext.MessageBox.show({
						title:'Mensaje',
						msg:'El cierre ha sido ejecutado con Anterioridad',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
					valido=false;
				}
			}
			else
			{
				Ext.MessageBox.show({
					title:'Mensaje',
					msg:'El cierre ha sido ejecutado con Anterioridad',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});
				valido=false;
			}
		}
		if(valido)
		{
			obtenerMensaje('procesar','',procesando);
			var cadjson = "{'operacion':'procesar','codsis':'"+sistema+"','nomven':'"+vista+"',"+getJsonFormulario(fromCierreEjercicio);
			cadjson += "}";
			try
			{
				var objjson = Ext.util.JSON.decode(cadjson);
				if(typeof(objjson) == 'object')
				{
					var parametros = 'ObjSon=' + cadjson;
					Ext.Ajax.request({
						url : '../../controlador/scg/sigesp_ctr_scg_cierre.php',
						params : parametros,
						method: 'POST',
						success: function ( result, request)
						{
							datos = result.responseText;
							Ext.Msg.hide();
							var datajson = eval('(' + datos + ')');
							if(datajson.raiz.valido==true)
							{	
								function cargarComprobante()
								{
									irNuevo();
									verificarEstCieSem();
									verificarCierre();
								}
								Delay(500);
								Ext.MessageBox.show({
									title:'Mensaje',
									msg:datajson.raiz.mensaje,
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO,
									fn: cargarComprobante
								});
							}
							else
							{
								Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
							}
				    	}
					});
				}
			}	
			catch(e)
			{
				alert('Verifique los datos, esta insertando caracteres invalidos '+e);
			}
		}
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que elimina el comprobante contable
	function irEliminar()
	{
		var aux = formatoNumericoMostrar(0,2,'.',',','','','-','');
		var valido = true;
		function respuesta(btn){
			if(btn=='yes'){
				obtenerMensaje('procesar','','Procesando Información');
				if(Ext.getCmp('estciesem').checked){
					var estciesem = 1;
					if(Ext.getCmp('ciesem1').checked){
						var ciesem1 = 1;
					} 
					if(Ext.getCmp('ciesem2').checked){
						var siesem2 = 1;
					}
				}
				var cadjson = "{'operacion':'eliminar','codsis':'"+sistema+"','nomven':'"+vista+"',"+getJsonFormulario(fromCierreEjercicio);
				cadjson += "}";
		        if(valido){
		        	try{
		        		var objjson = Ext.util.JSON.decode(cadjson);
		        		if(typeof(objjson) == 'object'){
		        			var parametros = 'ObjSon=' + cadjson;
		        			Ext.Ajax.request({
		        				url : '../../controlador/scg/sigesp_ctr_scg_cierre.php',
		        				params : parametros,
		        				method: 'POST',
		        				success: function ( result, request){
			        				datos = result.responseText;
									Ext.Msg.hide();
									var datajson = eval('(' + datos + ')');
									if(datajson.raiz.valido==true)
									{
										function cargarComprobante(){
											irNuevo();
											verificarEstCieSem();
											verificarCierre();
										}
										Delay(500);
										Ext.MessageBox.show({
											title:'Mensaje',
											msg:datajson.raiz.mensaje,
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.INFO,
											fn: cargarComprobante
										});
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
					msg: 'El cierre debe estar ejecutado para poder eliminarlo, verifique por favor',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
			}); 
		}
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que retorna la descripcion de la operacion del comprobante
	function MostrarOperacion(valor){
		if(valor=='D'){
			return 'Debe';
		}
		else if(valor=='H'){
			return 'Haber';
		}
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------
	
	//Funcion que completa el comprbante con ceros para alcanzar la longitud maxima
	function llenarCampoNumdoc(campo)
	{
		var myJSONObject = {
				"operacion" :'llenar_documento',
				"numdoc"    : campo
		};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/scg/sigesp_ctr_scg_cierre.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) 
			{ 
	    		var numdoc = result.responseText;
	    		if (numdoc.length != 0)
	    		{
	    			Ext.getCmp('comprobante').setValue(numdoc);
	    		}
			}
		});
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------
	
	//Funcion que verifica el estatus del cierre semestral
	function verificarEstCieSem()
	{
		var myJSONObject = {
			"operacion":"verificar_estatus_ciesem" 
		};

		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/scg/sigesp_ctr_scg_cierre.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) { 
				datos = result.responseText;
				Ext.Msg.hide();
				var datajson = eval('(' + datos + ')');
				Delay(500);
				if(datajson.raiz.estciesem!='0')
				{	
					Ext.getCmp('estciesem').setValue(true);
				}
				if(datajson.raiz.ciesem1!='0')
				{	
					Ext.getCmp('ciesem1').setValue(true);
				}
				if(datajson.raiz.ciesem2!='0')
				{	
					Ext.getCmp('ciesem2').setValue(true);
				}
			},
			failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+datajson.raiz.mensaje); 
			}
		});		
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------
	
	//Funcion que verifica si el cierre fue ejecutado
	function verificarCierre()
	{
		var myJSONObject = {
			"operacion":"verificar_cierre" 
		};

		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/scg/sigesp_ctr_scg_cierre.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) { 
				datos = result.responseText;
				Ext.Msg.hide();
				var datajson = eval('(' + datos + ')');
				if(datajson.raiz.valido==true)
				{	
					Actualizar=true;
					function cargarComprobante()
					{
						Ext.getCmp('procede').setValue(datajson.raiz.procede);
						Ext.getCmp('comprobante').setValue(datajson.raiz.comprobante);
						Ext.getCmp('descripcion').setValue(datajson.raiz.descripcion);
						Ext.getCmp('tipo_destino').setValue(datajson.raiz.tipo_destino);
						Ext.getCmp('cod_pro').setValue(datajson.raiz.cod_pro);
						Ext.getCmp('ced_bene').setValue(datajson.raiz.ced_bene);
						Ext.getCmp('codban').setValue(datajson.raiz.codban);
						Ext.getCmp('ctaban').setValue(datajson.raiz.catban);
						Ext.getCmp('fecha').setValue(datajson.raiz.fecha);
						cargarDetalle(datajson.raiz.procede,datajson.raiz.comprobante,datajson.raiz.fecha);
						Ext.getCmp('totaldeb').setValue(formatoNumericoMostrar(datajson.raiz.montodebe,2,'.',',','','','-',''));
						Ext.getCmp('totalhab').setValue(formatoNumericoMostrar(datajson.raiz.montohaber,2,'.',',','','','-',''));
						Ext.getCmp('diferencia').setValue(formatoNumericoMostrar(0,2,'.',',','','','-',''));
						Ext.getCmp('ciesem1').setValue(datajson.raiz.periodoI);
						Ext.getCmp('ciesem2').setValue(datajson.raiz.periodoII);
					}
					Delay(500);
					Ext.MessageBox.show({
						title:'Mensaje',
						msg:datajson.raiz.mensaje,
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO,
						fn: cargarComprobante
					});
				}
				else
				{
					if(datajson.raiz.mensaje!=''){
						Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
					}
				}
			},
			failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+datajson.raiz.mensaje); 
			}
		});	
	}
	
	function cargarDetalle(procede,comprobante,fecha)
	{
		obtenerMensaje('procesar','','Buscando Datos');
			
		//Buscar ordenes de compra
		var JSONObject = {
			'operacion'   : 'cargar_detalle_comprobante',
			'comprobante' : comprobante,
			'procede'     : procede,
			'fecha'       : fecha
		}
		
		var ObjSon = JSON.stringify(JSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/scg/sigesp_ctr_scg_cierre.php',
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
							gridDetContables.store.removeAll();
						}
						else{
							gridDetContables.store.loadData(objCmp);
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