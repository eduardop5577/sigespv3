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
var	fromSPGMODPROG = null;
var	fieldsetmens = null;
var	fieldsettrim = null;
 
Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//Combo meses
	var reCmbMensual = [['Enero','01'],
	                    ['Febrero','02'],
	                    ['Marzo','03'],
	                    ['Abril','04'],
	                    ['Mayo','05'],
	                    ['Junio','06'],
	                    ['Julio','07'],
	                    ['Agosto','08'],
	                    ['Septiembre','09'],
	                    ['Octubre','10'],
	                    ['Noviembre','11'],
	                    ['Diciembre','12'],]; 
	// Arreglo que contiene los Documentos que se pueden controlar

    var dsCmbMensual = new Ext.data.SimpleStore({
             fields: ['den', 'cod'],
             data : reCmbMensual // Se asocian los documentos disponibles
    });
    
    var cmbMenDesde = new Ext.form.ComboBox({  
    	store: dsCmbMensual,
    	labelSeparator :'',
    	fieldLabel:'Mes Disminuir',
    	displayField:'den',
    	valueField:'cod',
    	name:'mendesde',
    	id:'mendesde',
    	width:150,
    	forceSelection: true,
    	typeAhead: true,
    	mode: 'local',
    	binding:true,
    	triggerAction: 'all',
    	emptyText:'--Seleccione--',
    	listeners: {
			'select': function(){	
		    	if(this.getValue()==Ext.getCmp('menhasta').getValue()){
		    		Ext.Msg.show({
						title:'Mensaje',
						msg: 'El Mes de Disminución no puede ser igual al Mes de Aumento!!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
					this.reset();
				}
			}
		}
	});
    
    var cmbMenHasta = new Ext.form.ComboBox({  
   	 	store: dsCmbMensual,
   	 	labelSeparator :'',
   	 	fieldLabel:'Mes Aumentar',
   	 	displayField:'den',
   	 	valueField:'cod',
        name:'menhasta',
        width:150,
        id:'menhasta',
        forceSelection: true,
        typeAhead: true,
        mode: 'local',
        binding:true,
        triggerAction: 'all',
        emptyText:'--Seleccione--',
        listeners: {
			'select': function(){	
		    	if(this.getValue()==Ext.getCmp('mendesde').getValue()){
		    		Ext.Msg.show({
						title:'Mensaje',
						msg: 'El Mes de Disminución no puede ser igual al Mes de Aumento!!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
					this.reset();
				}
			}
		}
    });
    
    //Combo trimestres
    var reCmbTrimestral = [['Enero-Marzo','03'],
                           ['Abril-Junio','06'],
                           ['Julio-Septiembre','09'],
                           ['Octubre-Diciembre','12'],]; 
	// Arreglo que contiene los Documentos que se pueden controlar

    var dsCmbTrimestral = new Ext.data.SimpleStore({
             fields: ['den', 'cod'],
             data : reCmbTrimestral // Se asocian los documentos disponibles
    });
	                                        
    var cmbTriDesde = new Ext.form.ComboBox({  
    	store: dsCmbTrimestral,
    	labelSeparator :'',
    	fieldLabel:'Trimestre Disminuir',
    	displayField:'den',
    	valueField:'cod',
    	width:150,
    	name:'tridesde',
    	id:'tridesde',
    	forceSelection: true,
    	typeAhead: true,
    	mode: 'local',
    	binding:true,
    	triggerAction: 'all',
    	emptyText:'--Seleccione--',
    	listeners: {
			'select': function(){	
		    	if(this.getValue()==Ext.getCmp('trihasta').getValue()){
		    		Ext.Msg.show({
						title:'Mensaje',
						msg: 'El Trimestre de Disminución no puede ser igual al Trimestre de Aumento!!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
					this.reset();
				}
			}
		}
	});
    
    var cmbTriHasta = new Ext.form.ComboBox({  
   	 	store: dsCmbTrimestral,
   	 	labelSeparator :'',
   	 	fieldLabel:'Trimestre Aumentar',
   	 	displayField:'den',
   	 	valueField:'cod',
        name:'trihasta',
        width:150,
        id:'trihasta',
        forceSelection: true,
        typeAhead: true,
        mode: 'local',
        binding:true,
        triggerAction: 'all',
        emptyText:'--Seleccione--',
        listeners: {
			'select': function(){	
				if(this.getValue()==Ext.getCmp('tridesde').getValue()){
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'El Trimestre de Disminución no puede ser igual al Trimestre de Aumento!!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
					this.reset();
				}
			}
		}
    });
    
	//-------------------------------------------------------------------------------------------------------------------------	

	var reCuenta = Ext.data.Record.create([
		{name: 'perdis'},                      
        {name: 'peraum'},
        {name: 'spg_cuenta'},
        {name: 'monto'},
    ]);
  	
  	var dsCuenta =  new Ext.data.Store({
  		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCuenta)
  	});
  						
  	var cmCuenta = new Ext.grid.ColumnModel([
  		  {header: "<CENTER>Período Disminución</CENTER>", width:80, sortable: true, dataIndex: 'perdis', renderer: MostrarMes},
          {header: "<CENTER>Período Aumento</CENTER>", width: 80, sortable: true, dataIndex: 'peraum', renderer: MostrarMes},
          {header: "<CENTER>Cuenta</CENTER>", width: 60, sortable: true, dataIndex: 'spg_cuenta'},
          {header: "<CENTER>Monto</CENTER>", width: 60, sortable: true, dataIndex: 'monto'},
    ]);
                  	
	gridCuenta = new Ext.grid.EditorGridPanel({
    	width:850,
 		height:250,
		frame:true,
		title:"<H1 align='center'>Listados de Períodos Modificados</H1>",
		autoScroll:true,
   		border:true,
   		ds: dsCuenta,
     	cm: cmCuenta,
     	stripeRows: true,
    	viewConfig: {forceFit:true},
	});
	
	//-------------------------------------------------------------------------------------------------------------------------	

	//Creando el campo de cuenta presupuestaria
	var reCuentaPre = Ext.data.Record.create([
		  {name: 'spg_cuenta'},
		  {name: 'denominacion'},
		  {name: 'sc_cuenta'},
		  {name: 'disponible'}
	]);
	                                    	
	var dsCuentaPre =  new Ext.data.Store({
		 reader: new Ext.data.JsonReader({
		 root: 'raiz',             
		 id: "id"},reCuentaPre)
	});
	                                    						
	var colmodelcatCuentaPre = new Ext.grid.ColumnModel([
		 {header: "<H1 align='center'>Presupuestaria</H1>", width: 40, sortable: true,   dataIndex: 'spg_cuenta'},
		 {header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 60, sortable: true, dataIndex: 'denominacion'},
		 {header: "<H1 align='center'>Contable</H1>", width: 40, sortable: true, dataIndex: 'sc_cuenta'},
		 {header: "<H1 align='center'>Disponible</H1>", width: 30, sortable: true, dataIndex: 'disponible'}
	]);
		
	//componente campocatalogo para el campo de cuenta presupuestaria
	comcampocatCuentaPre = new com.sigesp.vista.comCampoCatalogo({
			titvencat: "<H1 align='center'>Catálogo de Cuentas Presupuestarias</H1>",
			anchoformbus:580,
			altoformbus:160,
			anchogrid:580,
			altogrid:410,
			anchoven:600,
			altoven:480,
			anchofieldset:850,
			datosgridcat:dsCuentaPre,
			colmodelocat:colmodelcatCuentaPre, 
			rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_modprog.php',
			parametros: "ObjSon={'operacion': 'buscarCuentasPresupuestarias'",
			arrfiltro:[{etiqueta:'Código',id:'codff',valor:'spg_cuenta',longitud:'25'},
					   {etiqueta:'Denominación',id:'denff',valor:'denominacion',ancho:400},
					   {etiqueta:'Cuenta Contable',id:'cueff',valor:'sc_cuenta'}],
			posicion:'position:absolute;left:0px;top:'+(180+obtenerPosicion())+'px',
			tittxt:'Cuenta',
			idtxt:'spg_cuenta',
			campovalue:'spg_cuenta',
			anchoetiquetatext:120,
			anchotext:150,
			anchocoltext:0.35,
			idlabel:'denominacion',
			labelvalue:'denominacion',
			anchocoletiqueta:0.50,
			anchoetiqueta:350,
			tipbus:'P', 
			binding:'C',
			hiddenvalue:'',
			defaultvalue:'--',
			allowblank:true,
			arrtxtfiltro:['codestpro1','codestpro2','codestpro3','codestpro4','codestpro5','estcla'], //esta
			validarMostrar:1,
			fnValidarMostrar: validarCuentaPre,
			msjValidarMostrar: 'Debe seleccionar la Estructura Academica!!!'
	});
	//fin componente para el campo fuente de financiamiento/
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	fieldSetEstructura = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		style:'position:absolute;left:15px;top:15px',
		mostrarDenominacion:true,
		idtxt:'1',
	});
	//agregarListenersEstructura(fieldSetEstructura);
	
	//--------------------------------------------------------------------------------------------
	
	fieldsettrim = new Ext.form.FieldSet({
		width: 700,
		height: 55,
		title: '',
		style: 'position:absolute;left:5px;top:'+(215+obtenerPosicion())+'px',
		cls :'fondo',
		border: false,
		items: [{
				layout: "column",
				defaults: {border: false},
				items: [{
						layout: "form",
						border: false,
						width:300,
						labelWidth: 120,
						items: [cmbTriDesde]
						},
						{
						layout: "form",
						border: false,
						width:300,
						labelWidth: 120,
						items: [cmbTriHasta]
						}]
				}]
	})

	//--------------------------------------------------------------------------------------------

	fieldsetmens = new Ext.form.FieldSet({
		width: 700,
		height: 55,
		title: '',
		style: 'position:absolute;left:5px;top:'+(215+obtenerPosicion())+'px',
		cls :'fondo',
		border: false,
		items: [{
				layout: "column",
				defaults: {border: false},
				items: [{
						layout: "form",
						border: false,
						width:300,
						labelWidth: 120,
						items: [cmbMenDesde]
						},
						{
						layout: "form",
						border: false,
						width:300,
						labelWidth: 120,
						items: [cmbMenHasta]
						}]
				}]
	})
	
	//-------------------------------------------------------------------------------------------------------------------------	

	//Creando el formulario principal
	var Xpos = ((screen.width/2)-(440));
  	fromSPGMODPROG = new Ext.FormPanel({
  		title: "<H1 align='center'>Modificación de Presupuesto Programado</H1>",
  		width: 895,
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
				height: 140,
				cls: 'fondo',
				items:[comcampocatCuentaPre.fieldsetCatalogo,fieldsetmens,/*fromMensual,*/fieldsettrim,
				       {
						layout:"column",
						defaults:{border: false},
						width: 850,
						style: 'position:absolute;left:15px;top:'+(270+obtenerPosicion())+'px',
						items: [{
								layout:"form",
								border:false,
								width: 300,
								labelWidth:115,
								items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'Monto',
										id: 'monto',
										width: 100,
										value: '0,00',
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789.');"},
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
								},
								{
								layout:"form",
								border:false,
								width: 350,
								labelWidth:50,
								items: [{
										xtype:"datefield",
										labelSeparator :'',
										fieldLabel:"Fecha",
										name:'Fecha',
										id:'fecha',
										allowBlank:true,
										width:100,
										binding:true,
										defaultvalue:'1900-01-01',
										hiddenvalue:'',
										allowBlank:false,
										value: new Date().format('d-m-Y'),
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
									}]
								},
								{
				    			 layout:"form",
				    			 border:false,
				    			 items: [{
					    				   xtype:"button",
					    				   id:'btnBuscarCP',
					    				   text:'Modificar Programación',
					    				   handler: function(){
					    				   		var valido=true;
					    				   		if(trim(Ext.getCmp('estmodape').getValue())=="0"){
					    				   			if(Ext.getCmp('mendesde').getValue()=='' || Ext.getCmp('menhasta').getValue()==''){
					    				   				Ext.MessageBox.show({
					        				   				title:'Mensaje',
					        				   				msg:'Debe llenar todos lo campos!!!',
					        				   				buttons: Ext.Msg.OK,
					        				   				icon: Ext.MessageBox.INFO
					        				   			});
					        				   			valido=false;
					    				   			}
					    				   		}
					    				   		if(trim(Ext.getCmp('estmodape').getValue())=="1"){
					    				   			if(Ext.getCmp('tridesde').getValue()=='' || Ext.getCmp('trihasta').getValue()==''){
					    				   				Ext.MessageBox.show({
					        				   				title:'Mensaje',
					        				   				msg:'Debe llenar todos lo campos!!!',
					        				   				buttons: Ext.Msg.OK,
					        				   				icon: Ext.MessageBox.INFO
					        				   			});
					        				   			valido=false;
					    				   			}
					    				   		}
					    				   		if(Ext.getCmp('spg_cuenta').getValue()=='' && valido==true){
					    				   			Ext.MessageBox.show({
					    				   				title:'Mensaje',
					    				   				msg:'Debe llenar todos lo campos!!!',
					    				   				buttons: Ext.Msg.OK,
					    				   				icon: Ext.MessageBox.INFO
					    				   			});
					    				   			valido=false;
					    				   		}
					    				   		if(valido){
					    				   			function respuesta(btn){
					    								if(btn=='yes'){
					    									modificarProgramacion();
					    								}
					    							}
					    				   			Ext.MessageBox.confirm('Confirmar', '¿Esta seguro de realizar la modificación al monto programado?, recuerde que no existe reverso para este proceso', respuesta);
					    				   		}
					    			   	   }
					    			   }]
								}]
				        },
						{
						xtype: 'hidden',
						id: 'estmodape',
						binding:true,
						defaultvalue:'',
						allowBlank:true
						},
						{
						xtype: 'hidden',
						id: 'codestpro1',
						binding:true,
						defaultvalue:'',
						allowBlank:true
						},
						{
						xtype: 'hidden',
						id: 'codestpro2',
						binding:true,
						defaultvalue:'',
						allowBlank:true
						},
						{
						xtype: 'hidden',
						id: 'codestpro3',
						binding:true,
						defaultvalue:'',
						allowBlank:true
						},
						{
						xtype: 'hidden',
						id: 'codestpro4',
						binding:true,
						defaultvalue:'',
						allowBlank:true
						},
						{
						xtype: 'hidden',
						id: 'codestpro5',
						binding:true,
						defaultvalue:'',
						allowBlank:true
						},
						{
						xtype: 'hidden',
						id: 'estcla',
						binding:true,
						defaultvalue:'',
						allowBlank:true
						}]
		},gridCuenta]
  	})
  	verificarEstatus();
}); //fin del formulario principal

//-------------------------------------------------------------------------------------------------------------------------	

function obtenerPosicion(){
	if(empresa['numniv']=='3'){
		return 0;
	}
	else{
		return 80;
	}
}

//-------------------------------------------------------------------------------------------------------------------------	

function MostrarMes(valor)
{	
	if(Ext.getCmp('estmodape').getValue()=="0"){
		if(valor=="01"){
			return "Enero";
		}
		else if(valor=="02"){
			return "Febrero";
		}
		else if(valor=="03"){
			return "Marzo";
		}
		else if(valor=="04"){
			return "Abril";
		}
		else if(valor=="05"){
			return "Mayo";
		}
		else if(valor=="06"){
			return "Junio";
		}
		else if(valor=="07"){
			return "Julio";
		}
		else if(valor=="08"){
			return "Agosto";
		}
		else if(valor=="09"){
			return "Septiembre";
		}
		else if(valor=="10"){
			return "Octubre";
		}
		else if(valor=="11"){
			return "Noviembre";
		}
		else if(valor=="12"){
			return "Diciembre";
		}
	}
	else{
		if(valor=="03"){
			return "Enero-Marzo";
		}
		else if(valor=="06"){
			return "Abril-Junio";
		}
		else if(valor=="09"){
			return "Julio-Septiembre";
		}
		else if(valor=="12"){
			return "Octubre-Diciembre";
		}
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
		success: function ( result, request ) { 
			var datos = result.responseText;
			var datajson = eval('(' + datos + ')');
			if(datajson!="")
			{
				Ext.getCmp('estmodape').setValue(datajson.raiz.estmodape);
				if(datajson.raiz.estmodape=="0"){
					fieldsettrim.hide();
				}
				else{
					fieldsetmens.hide();
				}
			}
		},
		failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});		
}

function irNuevo()
{
	irCancelar();
}

function irBuscar(){
	Catalogo();
}

function irCancelar()
{
	limpiarFormulario(fromSPGMODPROG);
	gridCuenta.store.removeAll();
	fieldSetEstructura.limpiarEstructuras(-1);
	verificarEstatus();
}

function modificarProgramacion()
{
	cadenajson="";
	var arrCodigos = fieldSetEstructura.obtenerArrayEstructuraFormato();
  	codestpro1 = String.leftPad(arrCodigos[0],25,'0');
	codestpro2 = String.leftPad(arrCodigos[1],25,'0');
	codestpro3 = String.leftPad(arrCodigos[2],25,'0');
	codestpro4 = String.leftPad(arrCodigos[3],25,'0');
	codestpro5 = String.leftPad(arrCodigos[4],25,'0');
	estcla = arrCodigos[5];
	spg_cuenta = Ext.getCmp('spg_cuenta').getValue();
	monto = Ext.getCmp('monto').getValue();
	fecha = Ext.getCmp('fecha').getValue().format('Y-m-d');
	if(Ext.getCmp('estmodape').getValue()=="0"){
		//mensual
		mes1 = Ext.getCmp('mendesde').getValue();
		mes2 = Ext.getCmp('menhasta').getValue();
	}
	else{
		//trimestral
		mes1 = Ext.getCmp('tridesde').getValue();
		mes2 = Ext.getCmp('trihasta').getValue();
	}
	cadenajson+="{'operacion':'guardar','codsis':'"+sistema+"','nomven':'"+vista+"'," +
			     "'codestpro1':'"+codestpro1+"','codestpro2':'"+codestpro2+"','codestpro3':'"+codestpro3+"'," +
			     "'codestpro4':'"+codestpro4+"','codestpro5':'"+codestpro5+"','estcla':'"+estcla+"'," +
			     "'spg_cuenta':'"+spg_cuenta+"','monto':'"+monto+"','fecha':'"+fecha+"','mes1':'"+mes1+"','mes2':'"+mes2+"'}";
	obtenerMensaje('procesar','','Procesando Información');
	try{
		var objjson = Ext.util.JSON.decode(cadenajson);
		if(typeof(objjson) == 'object'){
			var parametros = 'ObjSon=' + cadenajson;
			Ext.Ajax.request({
				url : '../../controlador/spg/sigesp_ctr_spg_modprog.php',
				params : parametros,
				method: 'POST',
				success: function ( result, request){
					datos = result.responseText;
					Ext.Msg.hide();
					var datajson = eval('(' + datos + ')');
					if(datajson.raiz.valido==true)
					{	
						Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
						var rePeriodos = Ext.data.Record.create([
							{name: 'perdis'},                      
							{name: 'peraum'},
							{name: 'spg_cuenta'},
							{name: 'monto'},
						]);
						var mesuno='';
						var mesdos='';
						if(Ext.getCmp('estmodape').getValue()=="0"){
							mesuno=Ext.getCmp('mendesde').getValue();
							mesdos=Ext.getCmp('menhasta').getValue();
						}
						else{
							mesuno=Ext.getCmp('tridesde').getValue();
							mesdos=Ext.getCmp('trihasta').getValue();
						}
						var periodosInt = new rePeriodos({
							'perdis'     :mesuno,
							'peraum'     :mesdos,
							'spg_cuenta' :Ext.getCmp('spg_cuenta').getValue(),
							'monto'      :Ext.getCmp('monto').getValue(),
						});
						gridCuenta.store.insert(0,periodosInt);
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

function irGuardar(){
}

//Funcion para validar si la cuenta presupuestaria fue seleccionada
function validarCuentaPre()
{
	var unidadOk = true;
	var arrCodigos = fieldSetEstructura.obtenerArrayEstructuraFormato();
  	if(empresa['numniv']=='3'){
		if(arrCodigos[0]=='' || arrCodigos[1]=='' || arrCodigos[2]==''){
			unidadOk = false;
	  	}
		else{
			Ext.getCmp('codestpro1').setValue(String.leftPad(arrCodigos[0],25,'0'));
			Ext.getCmp('codestpro2').setValue(String.leftPad(arrCodigos[1],25,'0'));
			Ext.getCmp('codestpro3').setValue(String.leftPad(arrCodigos[2],25,'0'));
			Ext.getCmp('codestpro4').setValue(String.leftPad(arrCodigos[3],25,'0'));
			Ext.getCmp('codestpro5').setValue(String.leftPad(arrCodigos[4],25,'0'));
			Ext.getCmp('estcla').setValue(arrCodigos[5]);
		}
	}
	if(empresa['numniv']=='5'){
		if(arrCodigos[0]=='' || arrCodigos[1]=='' || arrCodigos[2]=='' || arrCodigos[3]=='' || arrCodigos[4]==''){
			unidadOk = false;
	  	}
		else{
			Ext.getCmp('codestpro1').setValue(String.leftPad(arrCodigos[0],25,'0'));
			Ext.getCmp('codestpro2').setValue(String.leftPad(arrCodigos[1],25,'0'));
			Ext.getCmp('codestpro3').setValue(String.leftPad(arrCodigos[2],25,'0'));
			Ext.getCmp('codestpro4').setValue(String.leftPad(arrCodigos[3],25,'0'));
			Ext.getCmp('codestpro5').setValue(String.leftPad(arrCodigos[4],25,'0'));
			Ext.getCmp('estcla').setValue(arrCodigos[5]);
		}
	}
	return unidadOk;
}

function Catalogo()
{
	var fieldSetEstrucFueFin = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		style:'position:absolute;left:15px;top:15px',
		mostrarDenominacion:true,
		idtxt:'2'
	});
	//agregarListenersEstructura(fieldSetEstrucFueFin);
	
	//Creando el campo de cuenta presupuestaria
	var reCuePre = Ext.data.Record.create([
		  {name: 'spg_cuenta'},
		  {name: 'denominacion'},
		  {name: 'sc_cuenta'},
		  {name: 'disponible'}
	]);
	                                    	
	var dsCuePre =  new Ext.data.Store({
		 reader: new Ext.data.JsonReader({
		 root: 'raiz',             
		 id: "id"},reCuePre)
	});
	                                    						
	var colmodelcatCuePre = new Ext.grid.ColumnModel([
		 {header: "<H1 align='center'>Presupuestaria</H1>", width: 40, sortable: true,   dataIndex: 'spg_cuenta'},
		 {header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 60, sortable: true, dataIndex: 'denominacion'},
		 {header: "<H1 align='center'>Contable</H1>", width: 40, sortable: true, dataIndex: 'sc_cuenta'},
	]);
		
	//componente campocatalogo para el campo de cuenta presupuestaria
	comcampocatCuePre = new com.sigesp.vista.comCampoCatalogo({
			titvencat: "<H1 align='center'>Catálogo de Cuentas Presupuestarias</H1>",
			anchoformbus:580,
			altoformbus:160,
			anchogrid:580,
			altogrid:410,
			anchoven:600,
			altoven:480,
			anchofieldset:850,
			datosgridcat:dsCuePre,
			colmodelocat:colmodelcatCuePre, 
			rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_modprog.php',
			parametros: "ObjSon={'operacion': 'buscarCuentasPresupuestarias'",
			arrfiltro:[{etiqueta:'Código',id:'codff',valor:'spg_cuenta',longitud:'25'},
					   {etiqueta:'Denominación',id:'denff',valor:'denominacion',ancho:400},
					   {etiqueta:'Cuenta Contable',id:'cueff',valor:'sc_cuenta'}],
			posicion:'position:absolute;left:5px;top:'+(175+obtenerPosicion())+'px',
			tittxt:'Cuenta',
			idtxt:'cuenta',
			campovalue:'spg_cuenta',
			anchoetiquetatext:100,
			anchotext:150,
			anchocoltext:0.32,
			idlabel:'deno',
			labelvalue:'denominacion',
			anchocoletiqueta:0.50,
			anchoetiqueta:350,
			tipbus:'P', 
			binding:'C',
			hiddenvalue:'',
			defaultvalue:'--',
			allowblank:true,
	});
	
	var reCatalogo = Ext.data.Record.create([
	    {name: 'mesaumento'},                      
	    {name: 'mesdisminucion'},
	    {name: 'spg_cuenta'},
	    {name: 'monto'},
	    {name: 'codestpro1'},
	    {name: 'codestpro2'},
	    {name: 'codestpro3'},
	    {name: 'codestpro4'},
	    {name: 'codestpro5'},
	    {name: 'estcla'},
	    {name: 'fecha'}
	]);
	                                 	
	var dsCatalogo =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCatalogo)
	});
	                                 						
	var cmCatalogo = new Ext.grid.ColumnModel([
	    {header: "<CENTER>Cuenta</CENTER>", width: 60, sortable: true, dataIndex: 'spg_cuenta'},
	    {header: "<CENTER>Período Disminución</CENTER>", width:80, sortable: true, dataIndex: 'mesdisminucion', renderer: MostrarMes},
	    {header: "<CENTER>Período Aumento</CENTER>", width: 80, sortable: true, dataIndex: 'mesaumento', renderer: MostrarMes},
	    {header: "<CENTER>Monto</CENTER>", width: 60, sortable: true, dataIndex: 'monto'},
	]);
	                                                 	
	gridCatalogo = new Ext.grid.GridPanel({
		width:850,
		height:200-obtenerPosicion(),
		frame:true,
		title:"",
		autoScroll:true,
		border:true,
		ds: dsCatalogo,
		cm: cmCatalogo,
		stripeRows: true,
		viewConfig: {forceFit:true},
	});
	
	gridCatalogo.on({
		'rowdblclick': {
			fn: function(grid, numFila, evento){
				var registro = gridCatalogo.getStore().getAt(numFila);
				if(registro!=undefined){
        			Aceptar(registro);
        			ventanaCatalogo.destroy();
        		}
        		else{
        			
        		}
			}
		}
	});
	
	//----------------------------------------------------------------------------------------------------------------------------------
	
	//Creacion del formulario de agregar presupuesto
	var frmCatalogo = new Ext.FormPanel({
		width: 865,
		height: 510, //500
		style: 'position:absolute;left:7px;top:0px',
		frame: true,
		autoScroll:false,
		items:[fieldSetEstrucFueFin.fieldSetEstPre,
		       {
	    	   xtype:"fieldset", 
	    	   title:'',
	    	   border:true,
	    	   width: 850,
	    	   height: 110,
	    	   cls: 'fondo',
	    	   items:[comcampocatCuePre.fieldsetCatalogo,
	    	          {
		    		   layout:"column",
		    		   defaults:{border: false},
		    		   width: 500,
		    		   style: 'position:absolute;left:15px;top:'+(215+obtenerPosicion())+'px',
		    		   items: [{
			    			   layout:"form",
			    			   border:false,
			    			   labelWidth:100,
			    			   items: [{
				    				   xtype:"datefield",
				    				   labelSeparator :'',
				    				   fieldLabel:"Fecha Desde",
				    				   name:'fechades',
				    				   id:'fechades',
				    				   allowBlank:true,
				    				   width:100,
				    				   binding:true,
				    				   defaultvalue:'1900-01-01',
				    				   hiddenvalue:'',
				    				   allowBlank:false,
				    				   value: new Date().format('01-m-Y'),
				    				   autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
				    			   }]
		    		   			}]
	    	          },
	    	          {
		    		   layout:"column",
		    		   defaults:{border: false},
		    		   width: 500,
		    		   style: 'position:absolute;left:15px;top:'+(245+obtenerPosicion())+'px',
		    		   items: [{
			    			   layout:"form",
			    			   border:false,
			    			   labelWidth:100,
			    			   items: [{
				    				   xtype:"datefield",
				    				   labelSeparator :'',
				    				   fieldLabel:"Hasta",
				    				   name:'fechahas',
				    				   id:'fechahas',
				    				   allowBlank:true,
				    				   width:100,
				    				   binding:true,
				    				   defaultvalue:'1900-01-01',
				    				   hiddenvalue:'',
				    				   allowBlank:false,
				    				   value: new Date().format('d-m-Y'),
				    				   autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
				    			   }]
		    		   			}]
	    	        },
	    	        {
	       			layout:"column",
			   		defaults: {border: false},
			   		style: 'position:absolute;left:750px;top:'+(245+obtenerPosicion())+'px', 
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
					   					obtenerMensaje('procesar','','Buscando Datos');
		   					
					   					var codestpro1 = codestpro2=codestpro3=codestpro4=codestpro5=estcla='';
					   					var arrCodigos = fieldSetEstrucFueFin.obtenerArrayEstructuraFormato();
					   					if(empresa['numniv']=='3'){
					   						if(arrCodigos[0]!='' || arrCodigos[1]!='' || arrCodigos[2]!=''){
					   							codestpro1 = String.leftPad(arrCodigos[0],25,'0');
					   							codestpro2 = String.leftPad(arrCodigos[1],25,'0');
					   							codestpro3 = String.leftPad(arrCodigos[2],25,'0');
					   							codestpro4 = String.leftPad(arrCodigos[3],25,'0');
					   							codestpro5 = String.leftPad(arrCodigos[4],25,'0');
					   							estcla = arrCodigos[5];
					   						}
					   					}
					   					if(empresa['numniv']=='5'){
					   						if(arrCodigos[0]!='' || arrCodigos[1]!='' || arrCodigos[2]!='' || arrCodigos[3]!='' || arrCodigos[4]!=''){
					   							codestpro1 = String.leftPad(arrCodigos[0],25,'0');
					   							codestpro2 = String.leftPad(arrCodigos[1],25,'0');
					   							codestpro3 = String.leftPad(arrCodigos[2],25,'0');
					   							codestpro4 = String.leftPad(arrCodigos[3],25,'0');
					   							codestpro5 = String.leftPad(arrCodigos[4],25,'0');
					   							estcla = arrCodigos[5];
					   					  	}
					   					}
		   					
				   						//buscar bienes
						   				var JSONObject = {
						   					'operacion'  : 'buscarModprogramado',
						   					'spg_cuenta' : Ext.getCmp('cuenta').getValue(),
						   					'fecdes'     : Ext.getCmp('fechades').getValue(),
						   					'fechas'     : Ext.getCmp('fechahas').getValue(),
						   				    'codestpro1' : codestpro1,
						   				    'codestpro2' : codestpro2,
						   				    'codestpro3' : codestpro3,
						   				    'codestpro4' : codestpro4,
						   				    'codestpro5' : codestpro5,
						   				    'estcla'     : estcla,
						   				}
	   				
						   				var ObjSon = JSON.stringify(JSONObject);
						   				var parametros = 'ObjSon='+ObjSon; 
						   				Ext.Ajax.request({
						   					url : '../../controlador/spg/sigesp_ctr_spg_modprog.php',
						   					params : parametros,
						   					method: 'POST',
						   					success: function ( resultado, request){
						   						Ext.Msg.hide();
						   						var datos = resultado.responseText;
						   						var objetos = eval('(' + datos + ')');
						   						if(objetos!=''){
						   							if(objetos!='0'){
						   								if(objetos.raiz == null || objetos.raiz ==''){
						   									Ext.MessageBox.show({
											 					title:'Advertencia',
											 					msg:'No existen datos para mostrar',
											 					buttons: Ext.Msg.OK,
											 					icon: Ext.MessageBox.WARNING
											 				});
														}
														else{
															gridCatalogo.store.loadData(objetos);
														}
						   							}
						   							else{
						   								Ext.MessageBox.show({
											 				title:'Advertencia',
											 				msg:'Debe configurar en Empresa los digitos de las cuentas de gastos',
											 				buttons: Ext.Msg.OK,
											 				icon: Ext.MessageBox.WARNING
											 			});
						   							}
						   						}
						   					}//fin del success	
						   				});//fin del ajax request
					   		        }
				   				}]
			   				}]
	    	          }]
		},gridCatalogo]  
	});
	//----------------------------------------------------------------------------------------------------------------------------------	
	var ventanaCatalogo = new Ext.Window({
		title: "<H1 align='center'>Catálogo de Modificaciones al Programado del Presupuesto</H1>",
		width:880,
		height:575, 
		modal: true,
		closable:false,
		plain: false,
		frame:true,
		items:[frmCatalogo],
		buttons: [{
			text:'Aceptar',  
			handler: function(){
			var registro = gridCatalogo.getSelectionModel().getSelected();
			if(registro!=undefined){
				Aceptar(registro);
				ventanaCatalogo.destroy();
			}
			else{

			}
		}
		},{
			text: 'Salir',
			handler:function(){
			ventanaCatalogo.destroy();
		}
		}]

	});
	ventanaCatalogo.show();

	function Aceptar(registro){
		setDataFrom(fromSPGMODPROG,registro);
		Ext.getCmp('mendesde').setValue(registro.get('mesdisminucion'));
		Ext.getCmp('tridesde').setValue(registro.get('mesdisminucion'));
		Ext.getCmp('menhasta').setValue(registro.get('mesaumento'));
		Ext.getCmp('trihasta').setValue(registro.get('mesaumento'));
		var reDetalle = Ext.data.Record.create([
		    {name: 'perdis'},                      
		    {name: 'peraum'},
		    {name: 'spg_cuenta'},
		    {name: 'monto'},
		]);
		var detalleInt = new reDetalle({
			'perdis'     :registro.get('mesdisminucion'),
			'peraum'     :registro.get('mesaumento'),
			'spg_cuenta' :registro.get('spg_cuenta'), 
			'monto'      :registro.get('monto'),
		});	
		gridCuenta.store.insert(0,detalleInt);
		fieldSetEstructura.obtenerId(registro,'1');
	}
}