/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var plproveedor      = null;  //instancia del formulario de agencia
var comcatdocumento  = null;  //instancia del componente campo catalogo agencia
var Actualizar       = null;
var Actualizar_socio = false;
var Actualizar_documento = false;
var Actualizar_calif = false;
var campocatbanco    = null;  //instancia del componente campo catalogo bancos
var codtipoorg='';
var DataStore="";
var DatosEmp ="";
var DatosBan ="";   
var DatosMon ="";
var ComboTipoEmp	 = null;
var ComboBanco	  	 = null;
var ComboMoneda		 = null;
var ComboTipo 		 = null;
var Comboest  		 = null;
var Combomun  		 = null;
var Comboparroquia = null;
var comcampocatctacontpag = null;
var comcampocatctacontrec = null;
var comcampocatctacontant = null;
var registrocuenta='';
var datastorecuenta='';
var gridespecialidad='';
var griddeduccion='';
var ruta ='../../controlador/rpc/sigesp_ctr_rpc_proveedor.php'; //ruta del controlador
barraherramienta    = true;

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
//-----------------------------------------------------------------------------------------------------------------------	
	//creando store para la nacionalidad
	var nacionalidad = 	[
                    	['-- Seleccione --','--'],
                    	['Venezolano','V'],
                    	['Extranjero','E']
                  		]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var stnacionalidad = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : nacionalidad
	});
	//fin creando store para el combo tipo iva

	//creando objeto combo tipo iva
	var cmbnacionalidad = new Ext.form.ComboBox({
		store : stnacionalidad,
		fieldLabel : 'Nacionalidad ',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'nacpro',
		width:200,
		binding:true,
		hiddenvalue:'',
		defaultvalue:'-',
		allowBlank:true,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		binding:true,
		mode:'local'
	});
	
	//fin creando objeto nacionalidad
//-------------------------------------------------------------------------------------------------------------------------	
//creando store para el grado de la empresa
	var gradoemp = 	[
                    	['-- Seleccione --','--'],
                    	['Grado Uno','0001'],
                    	['Grado Dos','0002']
                  		]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var stgradoemp = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : gradoemp
	});
	//fin creando store para el combo tipo iva

	//creando objeto combo tipo iva
	var cmbgradoemp = new Ext.form.ComboBox({
		store : stgradoemp,
		fieldLabel : 'Grado de la Empresa: ',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'graemp',
		width:200,
		binding:true,
		hiddenvalue:'',
		defaultvalue:'----',
		allowBlank:true,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		binding:true,
		mode:'local'
	});
	
	//fin creando objeto grado de la empresa
//-------------------------------------------------------------------------------------------------------------------------	
//creando store para el tipo de contribuyente
	var contrib = [
            	['-- Seleccione --','-'],
            	['Formal','F'],
            	['Especial','E'],
				['Ordinario','O']
          		]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var stcontrib = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : contrib
	});
	//fin creando store para el combo tipo iva

	//creando objeto combo tipo iva
	var cmbcontrib = new Ext.form.ComboBox({
		store : stcontrib,
		fieldLabel : '(*) Contribuyente: ',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'tipconpro',
		width:200,
		hiddenvalue:'',
		defaultvalue:'-',
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		binding:true,
		allowBlank:false,
		mode:'local'
	});
	
	//fin creando objeto tipo de contribuyente
//-------------------------------------------------------------------------------------------------------------------------	
//creando store para el tipo de contribuyente
	var tippersona = [
                    	['-- Seleccione --','-'],
                    	['Juridica','J'],
                    	['Natural','N'],
                    	['Organismo Gubernamental','O'],
                    	['Comunas','C']
                  		]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var sttippersona = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : tippersona
	});
	//fin creando store para el combo tipo iva

	//creando objeto combo tipo iva
	var cmbtippersona = new Ext.form.ComboBox({
		store : sttippersona,
		fieldLabel : 'Tipo de Persona: ',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'tipperpro',
		width:200,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		binding:true,
		hiddenvalue:'',
		defaultvalue:'-',
		allowblank:true,
		mode:'local'
	});
	
	//fin creando objeto tipo de contribuyente
//-------------------------------------------------------------------------------------------------------------------------	
	//creando store para el estatus del documento
	var estatusdoc = 	[
                    	['No Entregado','0'],
                    	['Entregado','1'],
                    	['En Tramite','2'],
						['No Aplica al Proveedor','3']
                  		]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var stestatusdoc = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : estatusdoc
	});
	//fin creando store para el combo estatus documento

	//creando objeto combo tipo iva
	var cmbestatusdoc = new Ext.form.ComboBox({
		store : stestatusdoc,
		fieldLabel : 'Estatus del Documento',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'estdoc',
		width:140,
		listWidth:140,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		mode:'local'
	});
	
	//fin creando objeto estatus documento
//-------------------------------------------------------------------------------------------------------------------------	
	//creando store para el estatus de Originalidad
var estatusoriginal = 	[
                    	['Copia del Documento','0'],
                    	['Original','1']
                  		]; // Arreglo que contiene los Documentos que se pueden controlar

var stestatusoriginal = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : estatusoriginal
	});
	//fin creando store para el combo estatus de Originalidad

	//creando objeto combo tipo iva
var cmbestatusoriginal = new Ext.form.ComboBox({
		store : stestatusoriginal,
		fieldLabel : 'Estatus de Originalidad ',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'estorig',
		width:140,
		listWidth:140,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		mode:'local'
	});
	
	//fin creando objeto nacionalidad
//-------------------------------------------------------------------------------------------------------------------------	
	//creando store para el estatus de Calificación
var estatuscalificacion = 	[
                    		['Activa','0'],
                    		['No Activa','1']
                  			]; // Arreglo que contiene los Documentos que se pueden controlar

var stestatuscalificacion = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : estatuscalificacion
	});
	//fin creando store para el combo estatus de calificacion

	//creando objeto combo tipo iva
	var cmbestatuscalificacion = new Ext.form.ComboBox({
		store : stestatuscalificacion,
		fieldLabel : 'Estatus',
		labelSeparator : '',
		editable : false,
		listWidth: 140,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'status',
		width:140,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		mode:'local'
	});
	//fin creando objeto nacionalidad
//-------------------------------------------------------------------------------------------------------------------------	
	//creando store para el nivel de estatus
	var nivelestatus = 	[
                    	['Ninguno','0'],
                    	['Excelente','1'],
                    	['Bueno','2'],
						['Regular','3']
                  		]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var stnivelestatus = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : nivelestatus
	});
	//fin creando store para el combo nivel estatus

	//creando objeto combo nivel estatus
	var cmbnivelestatus = new Ext.form.ComboBox({
		store : stnivelestatus,
		fieldLabel : 'Nivel del Estatus',
		labelSeparator : '',
		editable : false,
		listWidth: 140,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'nivstatus',
		width:140,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		mode:'local'
	});
	
	//fin creando objeto nacionalidad
//-------------------------------------------------------------------------------------------------------------------------	
//Creando el campo de banco sigecof
	var reg_banco_sigecof = Ext.data.Record.create([
		{name: 'codbansig'},
		{name: 'denbansig'}
	]);
	
	var dsbancosig =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({
		root: 'raiz',             
		id: "id"},reg_banco_sigecof)
	});
						
	var colmodelcatbancosig = new Ext.grid.ColumnModel([
        {header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'codbansig'},
        {header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'denbansig'}
	]);
	//fin creando datastore y columnmodel para el catalogo de bancos sigecof
	
	//componente campocatalogo para el campo banco
	comcampocatbancosig = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Cat&#225;logo de Bancos Sigecof</H1>",
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		anchofieldset: 850,
		datosgridcat: dsbancosig,
		colmodelocat: colmodelcatbancosig,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		parametros: "ObjSon={'operacion': 'catalogo_bansig'",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codibansig',valor:'codbansig',longitud:'3',ancho:100},
		           {etiqueta:'Descripci&#243;n',id:'desbansig',valor:'denbansig',longitud:'80',ancho:250}],
		posicion:'position:absolute;left:0px;top:92px',
		tittxt:'Banco SIGECOF:',
		idtxt:'codbansig',
		campovalue:'codbansig',
		anchoetiquetatext:140,
		anchotext:100,
		anchocoltext:0.30,
		idlabel:'denbansig',
		labelvalue:'denbansig',
		anchocoletiqueta:0.55,
		anchoetiqueta:190,
		tipbus:'P',
		binding:'C',
		typeAhead: true,
		binding:true,
		allowBlank:true,
		defaultvalue:'---',
	});
	//fin componente para el campo banco sigecof
//-------------------------------------------------------------------------------------------------------------------------	
	//Creando el campo de cuentas contables para las solicitudes a pagar
	var reg_cta_contable_pag = Ext.data.Record.create([
			{name: 'sc_cuenta'},
			{name: 'denominacion'}
	]);
	
	var dsctacontpag =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},reg_cta_contable_pag)
	});
						
	var colmodelcatctacontpag = new Ext.grid.ColumnModel([
          	{header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'sc_cuenta'},
          	{header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'denominacion'}
	]);
	//fin del campo de cuentas contables para las solicitudes a pagar
	
	//componente campocatalogo para el campo de cuentas contables para las solicitudes a pagar
	comcampocatctacontpag = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Cat&#225;logo de Cuentas Contables</H1>",
		anchoformbus: 450,
		altoformbus:140,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		anchofieldset: 1100,
		posbotbus: 470,
		datosgridcat: dsctacontpag,
		colmodelocat: colmodelcatctacontpag,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		parametros: "ObjSon={'operacion': 'catalogo_ctacontpag'",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'sc_ccuenta',valor:'sc_cuenta',longitud:'25',ancho:200},
		           {etiqueta:'Descripci&#243;n',id:'d_denominacion',valor:'denominacion',longitud:'254',ancho:250}],
		posicion:'position:absolute;left:10px;top:70px',
		tittxt:'(*)Cuenta Contable para el registro de las solicitudes por pagar',
		idtxt:'sc_cuenta',
		campovalue:'sc_cuenta',
		anchoetiquetatext:240,
		anchotext:120,
		anchocoltext:0.34,
		idlabel:'denominacion',
		labelvalue:'denominacion',
		anchocoletiqueta:0.45,
		anchoetiqueta:240,
		tipbus:'P',
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:false
	});
	//fin componente para el campo de cuentas contables para las solicitudes a pagar

//-------------------------------------------------------------------------------------------------------------------------	
	//Creando el campo de cuentas contables alterna para las solicitudes a pagar
	var reg_cta_contable_rec = Ext.data.Record.create([
			{name: 'sc_cuentarecdoc'},
			{name: 'denominacion_rec'}
	]);
	
	var dsctacontrec =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},reg_cta_contable_rec)
	});
						
	var colmodelcatctacontrec = new Ext.grid.ColumnModel([
          	{header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'sc_cuentarecdoc'},
          	{header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'denominacion_rec'}
	]);
	//fin del campo de cuentas contables para las solicitudes a pagar
	
	//componente campocatalogo para el campo de cuentas contables para las solicitudes a pagar
	comcampocatctacontrec = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Cat&#225;logo de Cuentas Contables</H1>",
		anchoformbus: 450,
		altoformbus:140,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		anchofieldset: 1100,
		posbotbus: 470,
		datosgridcat: dsctacontrec,
		colmodelocat: colmodelcatctacontrec,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		parametros: "ObjSon={'operacion': 'catalogo_ctacontrec'",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'sc_ccuentarecdoc',valor:'sc_cuentarecdoc',longitud:'25',ancho:200},
		           {etiqueta:'Descripci&#243;n',id:'d_denominacion_rec',valor:'denominacion_rec',longitud:'254',ancho:250}],
		posicion:'position:absolute;left:10px;top:110px',
		tittxt:' Cuenta Contable alterna de Proveedor',
		idtxt:'sc_cuentarecdoc',
		campovalue:'sc_cuentarecdoc',
		anchoetiquetatext:240,
		anchotext:120,
		anchocoltext:0.34,
		idlabel:'denominacion_rec',
		labelvalue:'denominacion_rec',
		anchocoletiqueta:0.45,
		anchoetiqueta:240,
		tipbus:'P',
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:true
	});
	//fin componente para el campo de cuentas contables alterna para las solicitudes a pagar

//-------------------------------------------------------------------------------------------------------------------------	
	//Creando el campo de cuentas contables de anticipo a contratistas
	var reg_cta_contable_ant = Ext.data.Record.create([
			{name: 'sc_ctaant'},
			{name: 'denominacion_2'}
	]);
	
	var dsctacontant =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},reg_cta_contable_ant)
	});
						
	var colmodelcatctacontant = new Ext.grid.ColumnModel([
          	{header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'sc_ctaant'},
          	{header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'denominacion_2'}
	]);
	//fin del campo de cuentas contables para las solicitudes a pagar
	
	//componente campocatalogo para el campo de cuentas contables para las solicitudes a pagar
	comcampocatctacontant = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Cat&#225;logo de Cuentas Contables</H1>",
		anchoformbus: 450,
		altoformbus:120,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		anchofieldset: 1100,
		datosgridcat: dsctacontant,
		colmodelocat: colmodelcatctacontant,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		parametros: "ObjSon={'operacion': 'catalogo_ctacontant'",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'sc_ctaanti',valor:'sc_ctaant'},
		           {etiqueta:'Descripci&#243;n',id:'d_deno',valor:'denominacion_2'}],
		posicion:'position:absolute;left:10px;top:145px',
		tittxt:'Cuenta Contable de Anticipo a Contratistas',
		idtxt:'sc_ctaant',
		campovalue:'sc_ctaant',
		anchoetiquetatext:240,
		anchotext:120,
		anchocoltext:0.34,
		idlabel:'denominacion_2',
		labelvalue:'denominacion_2',
		anchocoletiqueta:0.45,
		anchoetiqueta:240,
		tipbus:'P',
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:true
	});
	//fin componente para el campo de cuentas contables para las solicitudes a pagar

//-------------------------------------------------------------------------------------------------------------------------	
	//Creaci�n del combo tipo empresa
	function llenarComboEmpresa()
	{
		var myJSONObject ={
				"operacion": 'catalogocombotipoorg'	
		};	
		var ObjSon=JSON.stringify(myJSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				var datose = resultado.responseText;  
				if(datose!='')
				{
					var DatosEmp = eval('(' + datose + ')');
					dsEmpresa.loadData(DatosEmp);
				}
			}
		});
	}
	
	var reEmp = Ext.data.Record.create([
	    {name:'codtipoorg'},
	    {name: 'dentipoorg'}
	]);

	var dsEmpresa =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "codtipoorg"},reEmp)
	});

	var ComboTipoEmp = new Ext.form.ComboBox({
		store:dsEmpresa,
		fieldLabel:'Tipo de Empresa',
		displayField:'dentipoorg',
		valueField : 'codtipoorg',
		name: 'tipoorg',
		editable : false,
		id:'codtipoorg',
		width:200,
		binding:true,
		hiddenvalue:'',
		defaultvalue:'--',
		allowBlank:true,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		binding:true,
		mode:'local'
	});
	//Fin del combo tipo empresa
//-------------------------------------------------------------------------------------------------------------------------
	//Creaci�n del combo banco
	function llenarComboBanco()
	{
		var myJSONObject ={
				"operacion": 'catalogocombobanco'	
		};	
		var ObjSon=JSON.stringify(myJSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				var datosb = resultado.responseText;  
				if(datosb!='')
				{
					var DatosBan = eval('(' + datosb + ')');
					dsBanco.loadData(DatosBan);
				}
			}
		});
	}
	
	var reBanco = Ext.data.Record.create([
	    {name:'codban'},
	    {name:'nomban'}
	]);

	var dsBanco =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "codban"},reBanco)
	});

	var ComboBanco = new Ext.form.ComboBox({
		store:dsBanco,
		fieldLabel:'Banco',
		displayField:'nomban',
		valueField : 'codban',
		name: 'banco',
		editable : false,
		id:'codban',
		width:200,
		binding:true,
		hiddenvalue:'',
		defaultvalue:'---',
		allowBlank:true,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		binding:true,
		mode:'local'
	});
	//Fin del combo banco
//-------------------------------------------------------------------------------------------------------------------------
	//Creaci�n del combo moneda
	function llenarComboMoneda()
	{
		var myJSONObject ={
				"operacion": 'catalogocombomoneda'	
		};	
		var ObjSon=JSON.stringify(myJSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				var datosm = resultado.responseText;  
				if(datosm!='')
				{
					var DatosMon = eval('(' + datosm + ')');
					dsMoneda.loadData(DatosMon);
				}
			}
		});
	}
	
	var reMoneda = Ext.data.Record.create([
	    {name:'codmon'},
	    {name:'denmon'}
	]);

	var dsMoneda =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "codmon"},reMoneda)
	});

	var ComboMoneda = new Ext.form.ComboBox({
		store:dsMoneda,
		fieldLabel:'Moneda',
		displayField:'denmon',
		valueField : 'codmon',
		name: 'moneda',
		editable : false,
		id:'codmon',
		width:200,
		binding:true,
		hiddenvalue:'',
		defaultvalue:'---',
		allowBlank:true,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		binding:true,
		mode:'local'
	});
	//Fin del combo moneda
//-------------------------------------------------------------------------------------------------------------------------
//Combos relacionado con pais-estado-municipio-arroquia
//Creaci�n del combo pa�s
		RecordDef = Ext.data.Record.create([
		                                    {name:'codpai'},
		                                    {name: 'despai'}
		                                    ]);

		DataStore =  new Ext.data.Store({
			//proxy: new Ext.data.MemoryProxy(DatosNuevo),
			reader: new Ext.data.JsonReader({
				root: 'raiz',              
				id: "id"   
			},
			RecordDef
			)/*,
			data: DatosNuevo*/
		});

		ComboTipo = new Ext.form.ComboBox({
			store :DataStore,
			fieldLabel:'Pa&#237;s',
			displayField:'despai',
			valueField:'codpai',
			name: 'pais',
			id:'codpai',
			width:180,
			listWidth: 180, 
			typeAhead: true,
			selectOnFocus:true,
			binding:true,
			allowBlank:false,
			mode:'local',
			triggerAction:'all',
			valor:0,
			listeners: {
				'change': function(combo, nuevovalor,antiguovalor)
				{
					if(nuevovalor != antiguovalor)
					{
						if(String(Ext.getCmp('codest').getValue()) != "")
						{
							Ext.getCmp('codest').setValue('');
							Ext.getCmp('codmun').setValue('');
							Ext.getCmp('codest').setValue('');
							Ext.getCmp('codpar').setValue('');
							Ext.getCmp('codest').valor=0;
							Ext.getCmp('codmun').valor=0;
							Ext.getCmp('codpar').valor=0;
							codest="";
							codmun="";
							codpar="";
						}
					}
				}
			}

		})//Fin de combo pa�s
		
//Creaci�n del combo estado

		RecordDefes = Ext.data.Record.create([
		                                      {name: 'codpai'},
		                                      {name: 'codest'},
		                                      {name: 'desest'}
		                                      ]);

		DataStoreEstado =  new Ext.data.Store({
			//proxy: new Ext.data.MemoryProxy(DatosNuevo),
			reader: new Ext.data.JsonReader({
				root: 'raiz',             
				id: "id"   
			},
			RecordDefes
			)				
		});

		Comboest = new Ext.form.ComboBox({
			store: DataStoreEstado,
			fieldLabel:'Estado',
			displayField:'desest',
			valueField:'codest',
			name: 'estado',
			width:180,
			listWidth: 180, 
			id:'codest',
			typeAhead: true,
			binding:true,
			allowBlank:false,
			selectOnFocus:true,
			mode:'local',
			triggerAction:'all',
			valor:0,
			listeners: {
				'change': function(combo, nuevovalor,antiguovalor)
				{
					if(nuevovalor != antiguovalor)
					{
						if(String(Ext.getCmp('codmun').getValue()) != "")
						{
							Ext.getCmp('codmun').setValue('');
							Ext.getCmp('codest').setValue('');
							Ext.getCmp('codpar').setValue('');
							Ext.getCmp('codcom').setValue('');
							Ext.getCmp('nomcom').setValue('');
							Ext.getCmp('codest').valor=0;
							Ext.getCmp('codmun').valor=0;
							Ext.getCmp('codpar').valor=0;
							codmun="";
							codpar="";
						}
					}
				}
			}
		})
		///fin combo estado
		
//Creaci�n de combo municipio
		RecordDefmun = Ext.data.Record.create([
		                                       {name: 'codpai'},
		                                       {name: 'codest'},
		                                       {name: 'codmun'},
		                                       {name: 'denmun'}
		                                       ]);

		DataStoreMunicipio =  new Ext.data.Store({
			//proxy: new Ext.data.MemoryProxy(DatosNuevo),
			reader: new Ext.data.JsonReader({
				root: 'raiz',               
				id: "id"   
			},
			RecordDefmun

			)/*,
			data: DatosNuevo*/
		});

		Combomun = new Ext.form.ComboBox({
			store:DataStoreMunicipio,
			fieldLabel:'Municipio',
			displayField:'denmun',
			valueField:'codmun',
			name: 'municipio',
			width:180,
			listWidth: 180, 
			id:'codmun',
			listWidth: 180,
			typeAhead: true,
			mode:'local',
			selectOnFocus:true,
			binding:true,
			allowBlank:false,
			triggerAction:'all',
			valor:0,
			listeners: {
				'change': function(combo, nuevovalor,antiguovalor)
				{
					if(nuevovalor != antiguovalor)
					{
						if(String(Ext.getCmp('codpar').getValue()) != "")
						{
							Ext.getCmp('codpar').setValue('');
							Ext.getCmp('codcom').setValue('');
							Ext.getCmp('nomcom').setValue('');
							Ext.getCmp('codpar').valor=0;
							codpar="";
						}
					}
				}
			}
		})
		//Fin de combo municipio
		
//Creaci�n de combo parroquia
		RecordDefparroquia = Ext.data.Record.create([
		                                             {name: 'codpai'},
		                                             {name: 'codest'},
		                                             {name: 'codmun'},
		                                             {name: 'codpar'},
		                                             {name: 'denpar'}
		                                             ]);

		DataStoreParroquia =  new Ext.data.Store({
			//proxy: new Ext.data.MemoryProxy(DatosNuevo),
			reader: new Ext.data.JsonReader({
				root: 'raiz',               
				id: "id"   
			},
			RecordDefparroquia			     
			)/*,
			data: DatosNuevo*/
		});

		Comboparroquia = new Ext.form.ComboBox({
			store: DataStoreParroquia,
			fieldLabel:'Parroquia',
			displayField:'denpar',
			valueField:'codpar',
			width:180,
			id:'codpar',
			listWidth: 180,
			typeAhead: true,
			selectOnFocus:true,
			binding:true,
			allowBlank:false,
			mode:'local',
			triggerAction:'all',
			valor:0,
			listeners: {
			'change': function(combo, nuevovalor,antiguovalor)
			{
				
			}
		}
		});
		//Fin de combo parroquia

		var myJSONObject ={
					"operacion": 'catalogocombopais'
				  };	
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				datos = resultado.responseText;  
				if(datos!='')
				{
					var DatosNuevo = eval('(' + datos + ')');
					DataStore.loadData(DatosNuevo);
				}
			
			}//fin de success
		})//fin de ajax request	
//Fin de Combos relacionado con pais-estado-municipio-arroquia
//-------------------------------------------------------------------------------------------------------------------------
	//Creando el campo de Codigo del Documento
	var reg_cod_documento = Ext.data.Record.create([
			{name: 'coddoc'},
			{name: 'dendoc'}
	]);
	
	var dscoddocumento =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},reg_cod_documento)
	});
						
	var colmodelcoddocumento = new Ext.grid.ColumnModel([
          	{header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'coddoc'},
          	{header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'dendoc'}
	]);
	//fin creando datastore y columnmodel para el catalogo de codigo del documento
	
	//componente campocatalogo para el codigo del documento
	comcampocoddocumento = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Cat&#225;logo de Documentos</H1>",
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		anchofieldset: 950,
		datosgridcat: dscoddocumento,
		colmodelocat: colmodelcoddocumento,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		parametros: "ObjSon={'operacion': 'catalogo_documentos'",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codidoc',valor:'coddoc'},
		           {etiqueta:'Descripci&#243;n',id:'denodoc',valor:'dendoc'}],
		posicion:'position:absolute;left:100px;top:40px',
		tittxt:'C&#243;digo',
		idtxt:'coddoc',
		campovalue:'coddoc',
		anchoetiquetatext:130,
		anchotext:120,
		anchocoltext:0.28,
		idlabel:'dendoc',
		labelvalue:'dendoc',
		anchocoletiqueta:0.55,
		anchoetiqueta:230,
		tipbus:'P', //LF
		arrtxtfiltro:['cod_pro'],
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:false
	});
	//fin componente para el campo codigo del documento
//-------------------------------------------------------------------------------------------------------------------------	
	//Creando el campo de Codigo de Calificación
	var reg_cod_calificacion = Ext.data.Record.create([
			{name: 'codclas'},
			{name: 'denclas'}
	]);
	
	var dscodcalificacion =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},reg_cod_calificacion)
	});
						
	var colmodelcodcalificacion = new Ext.grid.ColumnModel([
          	{header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'codclas'},
          	{header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'denclas'}
	]);
	//fin creando datastore y columnmodel para el catalogo de codigo del documento
	
	//componente campocatalogo para el codigo del documento
	comcampocodcalificacion = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Par&#225;metros de Calificaci&#243;n de Proveedores</H1>",
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		anchofieldset: 1150,
		datosgridcat: dscodcalificacion,
		colmodelocat: colmodelcodcalificacion,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		parametros: "ObjSon={'operacion': 'catalogo_calificacion'",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codiclas',valor:'codclas'},
		           {etiqueta:'Descripc&#243;in',id:'denoclas',valor:'denclas'}],
		posicion:'position:absolute;left:100px;top:30px',
		tittxt:'C&#243;digo',
		idtxt:'codclas',
		campovalue:'codclas',
		anchoetiquetatext:250,
		anchotext:100,
		anchocoltext:0.32,
		idlabel:'denclas',
		labelvalue:'denclas',
		anchocoletiqueta:0.54,
		anchoetiqueta:200,
		tipbus:'P', //LF
		arrtxtfiltro:['cod_pro'],
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:false
	});
	//fin componente para el campo codigo del documento
//-------------------------------------------------------------------------------------------------------------------------	
	//Creando el campo de Nivel de Clasificación
	var reg_cod_nivclasif = Ext.data.Record.create([
			{name: 'codniv'},
			{name: 'desniv'},
			{name: 'monmincon'},
			{name: 'monmaxcon'}
	]);
	
	var dscodnivclasif =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},reg_cod_nivclasif)
	});
						
	var colmodelcodnivclasif = new Ext.grid.ColumnModel([ // de Contrataci&#243;n
          	{header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'codniv'},
          	{header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'desniv'},
			{header: "<H1 align='center'>Monto M&#237;nimo</H1>", width: 40, sortable: true,   dataIndex: 'monmincon'},
			{header: "<H1 align='center'>Monto M&#225;ximo</H1>", width: 40, sortable: true,   dataIndex: 'monmaxcon'}
	]);
	//fin creando datastore y columnmodel para el catalogo de Nivel de Clasificación
	
	//componente campocatalogo para el codigo del documento
	comcampocodnivclasif = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Cat&#225;logo de Niveles</H1>",
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		anchofieldset: 1150,
		datosgridcat: dscodnivclasif,
		colmodelocat: colmodelcodnivclasif,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		parametros: "ObjSon={'operacion': 'catalogo_niv_clasif'",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codiniv',valor:'codniv'},
		           {etiqueta:'Denominaci&#243;n',id:'denoniv',valor:'desniv'}],
		datosadicionales: 1,
		camposoadicionales : [{tipo:'cadena',id:'monmincon'},
		                      {tipo:'cadena',id:'monmaxcon'}],
		posicion:'position:absolute;left:100px;top:90px',
		tittxt:'C&#243;digo Nivel de Clasificaci&#243;n',
		idtxt:'codniv',
		campovalue:'codniv',
		anchoetiquetatext:250,
		anchotext:100,
		anchocoltext:0.32,
		idlabel:'desniv',
		labelvalue:'desniv',
		anchocoletiqueta:0.54,
		anchoetiqueta:200,
		tipbus:'P',
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:false
	});
	//fin componente para el campo codigo del documento
//-------------------------------------------------------------------------------------------------------------------------	
// Grid de Especialidades
var objetoespxprov={"raiz":[{"codesp":'',"denesp":''}]};	

var registroespxprov = Ext.data.Record.create([
			{name: 'codesp'},     
			{name: 'denesp'}
		]);

datastoreespxproveliminada = new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(objetoespxprov),		
		reader: new Ext.data.JsonReader({
					root: 'raiz',                
					id: "id"   
          		}
		,
        registroespxprov
		)});	

datastoreespxprov = new Ext.data.Store({
	proxy: new Ext.data.MemoryProxy(objetoespxprov),		
	reader: new Ext.data.JsonReader({
				root: 'raiz',                
				id: "id"   
			}
	,
	registroespxprov
	)});

var sm2 = new Ext.grid.CheckboxSelectionModel({});

    gridespecialidad = new Ext.grid.EditorGridPanel({
		width:700,
        height:300,
		frame:true,
		title:'Especialidades por Proveedor',
		viewConfig: {forceFit:true},
        id:'gridespecialidad',
       	ds: datastoreespxprov,
       	cm: new Ext.grid.ColumnModel([
            sm2,
            {id:'codesp',header: "C&#243;digo", width: 150, sortable: true, dataIndex: 'codesp'},
            {id:'denesp', header: "Descripci&#243;n", width: 550, sortable: true, dataIndex: 'denesp'}
        ]),
       	sm: new Ext.grid.CheckboxSelectionModel({}),
		viewConfig: {forceFit:true},
		tbar:[{
            text:'Agregar Especialidad',
            iconCls:'agregar',
           	handler: agregarEspecialidad,
        	 },
			 '-',
			 {
            text:'Eliminar fila',
            iconCls:'remover',
            handler: eliminar_grid_esp
           }]
        });
//-------------------------------------------------------------------------------------------------------------------------	
// Grid de Deducciones
var objetodedxprov={"raiz":[{"codded":'',"dended":''}]};	

var registrodedxprov = Ext.data.Record.create([
			{name: 'codded'},     
			{name: 'dended'}
		]);

datastorededxproveliminada = new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(objetodedxprov),		
		reader: new Ext.data.JsonReader({
					root: 'raiz',                
					id: "id"   
          		}
		,
        registrodedxprov
		)});	


datastorededxprov = new Ext.data.Store({
	proxy: new Ext.data.MemoryProxy(objetodedxprov),		
	reader: new Ext.data.JsonReader({
				root: 'raiz',                
				id: "id"   
			}
	,
	registrodedxprov
	)});

var sm3 = new Ext.grid.CheckboxSelectionModel({});

    	griddeduccion = new Ext.grid.EditorGridPanel({
		width:700,
        height:300,
		frame:true,
		title:'Deducciones por Proveedor',
		viewConfig: {forceFit:true},
        id:'griddeduccion',
       	ds: datastorededxprov,
       	cm: new Ext.grid.ColumnModel([
            sm3,
            {id:'codded',header: "C&#243;digo", width: 150, sortable: true, dataIndex: 'codded'},
            {id:'dended', header: "Descripci&#243;n", width: 550, sortable: true, dataIndex: 'dended'}
        ]),
       	sm: new Ext.grid.CheckboxSelectionModel({}),
		viewConfig: {forceFit:true},
		tbar:[{
	            text:'Agregar Deducci&#243;n',
	            iconCls:'agregar',
	           	handler: agregarDeduccion,
        	 },
			 '-',
			 {
	            text:'Eliminar fila',
	            iconCls:'remover',
	            handler: eliminar_grid_ded
           }]
        });

//-------------------------------------------------------------------------------------------------------------------------	
//-----------------------------------------------------------------------------------------------------------------------------

//Formulario	
	var Xpos = ((screen.width/2)-(915/2));
	var Ypos = 10;
	plproveedor = new Ext.FormPanel({
		applyTo: 'formulario',
		title: 'Registro de Proveedores',
		frame:true,
		autoScroll:true,
		style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px',
		width: 915,
		height: 500,
		items: [{
				xtype:"tabpanel",
				activeTab:0,
				deferredRender:false,
				enableTabScroll:true,
				autoScroll:true,
				width:885,
				//height:885,
			    border:false,
			    frame:true,
			    id:"tabfichaprov",
			    items:[{ 
						title:"Datos B&#225;sicos",
					    labelWidth:150,
						layout:"form",
						frame:true,
						height:980,//Alto del Tab
						width:880,
						id:'tabficdatbas',
						items: [{	
								layout: "column",
								defaults: {border: false},
								style: 'position:absolute;left:300px;top:0px',
								items: [{
										layout: "form",
										border: false,
										labelWidth: 200,
										items: [{
													xtype: 'label',
													text: 'Los Campos en (*) son obligatorios para el registro del proveedor'
												}]
										}]
			   					},
					   			{			   	
						   		layout: "form",
						   		border: false,
						   		labelWidth: 130,
						   		columnWidth: 0.5,
						   		height:880, //Alto del contenido del Tab
						   		items: [{
							   			xtype:"fieldset", 
							   			title:'Datos B&#225;sicos del Proveedor',
							   			style: 'position:absolute;left:60px;top:15px',
							   			border:true,
							   			width: 750,
							   			cls :'fondo',
							   			height: 215,
							   			items:[{
								   				layout:"column",
								   				border:false,
								   				labelWidth:140,
								   				items:[{
									   					layout:"form",
									   					border:false,
									   					items:[{
										   						xtype: 'textfield',
										   						fieldLabel: '(*) C&#243;digo:',
										   						labelSeparator :'',
										   						name: 'codigo',
										   						id: 'cod_pro',
										   						autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10',onkeypress: "return keyRestrict(event,'0123456789');"},
										   						disabled:false,
										   						width: 80,
										   						formatonumerico:false,
										   						binding:true,
										   						hiddenvalue:'',
										   						defaultvalue:'',
										   						allowBlank:false
										   					}]
								   						}]
							   					},
									   			{
								   				layout:"column",
								   				border:false,
								   				labelWidth:140,
								   				items:[{
									   					layout:"form",
									   					border:false,
									   					items:[{
										   						xtype: 'textfield',
										   						labelSeparator :'',
										   						fieldLabel: '(*)R.I.F:',
										   						name: 'Rif',
										   						id: 'rifpro',
										   						width: 145,
										   						binding:true,
										   						hiddenvalue:'',
										   						autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '12'},
										   						defaultvalue:'',
										   						allowBlank:false,
										   						listeners:{
																			'blur' : function(campo)
																					 {
																						var regExPattern = /^[JGVEC]-\d{8}-\d$/
																						if (!campo.getValue().match(regExPattern))
																						{
																							Ext.Msg.show({
																								title:'Advertencia',
																								msg: 'El formato del RIF es incorrecto, use [JGVEC]-[99999999]-[9]',
																								buttons: Ext.Msg.OK,
																								icon: Ext.MessageBox.WARNING
																							});
																						}
																						else
																						{
																							uf_verificar_rif(campo.getValue(),false);
																						}
											   										}
										   								}
										   					}]
								   						},
										   				{
									   					layout:"column",
									   					border:false,
									   					style: 'position:absolute;left:300px;top:0px',
									   					items:[{													
										   						layout:"form",
										   						border:false,
										   						labelWidth:150,
										   						items:[{
																			xtype:'label',
																			text: 'Ejemplo: J-99999999-9',
																			style:'font-size:9px;font-family:Verdana, Arial, Helvetica, sans-serif'			  
																		}]
									   							}]
										   				},
										   				{
									   					layout:"column",
									   					border:false,
									   					style: 'position:absolute;left:430px;top:0px',
									   					items:[{													
										   						layout:"form",
										   						border:false,
										   						labelWidth:150,
										   						items:[
																		{
																		xtype: 'button',
																		fieldLabel: '',
																		id: 'btagregar',
																		text: 'Consulta SENIAT',
																		iconCls: 'menubuscar',
																		handler: function(){
																			if(Ext.getCmp('rifpro').getValue()=='')
																			{
																				Ext.Msg.show({
																					title:'Mensaje',
																					msg: 'Debe llenar el RIF. del Proveedor',
																					buttons: Ext.Msg.OK,
																					icon: Ext.MessageBox.INFO
																				});
																			}
																			else{
																				uf_verificar_rif(Ext.getCmp('rifpro').getValue(),true);
																			}
																		}
											   						}]
									   							}]
										   				}]
									   			},
							   					{
								   				layout:"column",
								   				border:false,
								   				labelWidth:140,
								   				items:[{
									   					layout:"form",
									   					border:false,
									   					items:[{
										   						xtype: 'textfield',
										   						labelSeparator :'',
										   						fieldLabel: '(*)Nombre/Razon Social:',
										   						name: 'Nombre',
										   						id: 'nompro',
										   						autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz\u00f1 ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!@%&/\()?-+*[]{}\');"},
										   						width: 400,
										   						binding:true,
										   						hiddenvalue:'',
										   						defaultvalue:'',
										   						allowBlank:false
										   					}]
								   						}]
							   					},
							   					{
								   				layout:"column",
								   				border:false,
								   				labelWidth:140,
								   				items:[{
									   					layout:"form",
									   					border:false,
									   					items:[{
										   						xtype: 'textfield',
										   						labelSeparator :'',
										   						fieldLabel: '(*)Direcci&#243;n:',
										   						name: 'Direccion',
										   						id: 'dirpro',
										   						autoCreate: {tag: 'input', type: 'text', size: '150', autocomplete: 'off', maxlength: '500', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz\u00f1 ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!#@%&/\()?-+*[]{}');"},
										   						width: 400,
										   						binding:true,
										   						hiddenvalue:'',
										   						defaultvalue:'',
										   						allowBlank:false
										   					}]
								   					}]
							   					},
							   					{
								   				layout:"column",
								   				border:false,
								   				items:[{
									   					layout:"form",
									   					border:false,
									   					labelWidth:140,
									   					items:[ComboTipoEmp]
									   				}]								                 
							   					},																		
							   					{
								   				layout:"column",
								   				border:false,
								   				labelWidth:140,
								   				items:[{
									   					layout:"form",
									   					border:false,
									   					items:[{
										   						xtype: 'textfield',
										   						labelSeparator :'',
										   						fieldLabel: '(*)Tel&#233;fono:',
										   						name: 'Telefono',
										   						id: 'telpro',
										   						width: 145,
										   						binding:true,
										   						hiddenvalue:'',
										   						autoCreate: {tag: 'input', type: 'text', size: '50', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'0123456789-');"},
										   						defaultvalue:'',
										   						allowBlank:false
										   					}]
										   				},
										   				{
									   					layout:"column",
									   					border:false,
									   					style: 'position:absolute;left:400px;top:0px',
									   					items:[{													
										   						layout:"form",
										   						border:false,
										   						labelWidth:82,
										   						items:[{
											   							xtype: 'textfield',
											   							labelSeparator :'',
											   							fieldLabel: 'Fax:',
											   							name: 'Fax',
											   							id: 'faxpro',
											   							width: 150,
											   							binding:true,
											   							hiddenvalue:'',
											   							autoCreate: {tag: 'input', type: 'text', size: '30', autocomplete: 'off', maxlength: '30', onkeypress: "return keyRestrict(event,'0123456789.;,!@%&/\()�?�-+*[]{}');"},
											   							defaultvalue:'',
											   							allowBlank:true
											   						}]
									   							}]
										   				}]
							   					},
									   			{
								   				layout:"column",
								   				border:false,
								   				labelWidth:140,
								   				items:[{
														layout: "form",
														border: false,
														labelWidth: 140,
														items: [cmbnacionalidad]
														},
														{
														layout:"column",
														border:false,
														style: 'position:absolute;left:400px;top:0px',
														items:[{	
																layout:"form",
																border:false,
																labelWidth:82,
																items:[{
																		xtype: 'textfield',
																		labelSeparator :'',
																		fieldLabel: 'N.I.T:',
																		name: 'Nit',
																		id: 'nitpro',
																		width: 150,
																		binding:true,
																		hiddenvalue:'',
																		autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.,-');"},
																		defaultvalue:'',
																		allowBlank:true
																	}]
																}]
														}]
												}]
						   				},
						   				{
							   			xtype:"fieldset", 
							   			title:'Datos de Capital del Proveedor',
							   			style: 'position:absolute;left:60px;top:238px',
							   			border:true,
							   			width: 750,
							   			cls :'fondo',
							   			height: 180,
							   			items:[comcampocatbancosig.fieldsetCatalogo,
							   			       {	
												layout:"column",
												border:false,
												style: 'position:absolute;left:10px;top:20px',
												items:[{
														layout:"form",
														border:false,											
														labelWidth:140,
														items:[{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: '(*)Capital Social Suscrito:',
																name: 'Capsocial',
																id: 'capital',
																width: 150,
																binding:true,
																formatonumerico:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
																listeners:{
																	'blur':function(objeto){
																		var numero = objeto.getValue();
																		valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
																		objeto.setValue(valor);
																	},
																	'focus':function(objeto){
																		var numero = formatoNumericoEdicion(objeto.getValue());
																		objeto.setValue(numero);
																	}
																},
																defaultvalue:0,
																allowBlank:false
															}]
													}]
							   			       },
							   			       {
								   				layout:"column",
								   				border:false,
								   				style: 'position:absolute;left:375px;top:20px',
								   				items:[{  
									   					layout:"form",
									   					border:false,
									   					labelWidth:140,
									   					items:[{
										   						xtype: 'textfield',
										   						labelSeparator :'',
										   						fieldLabel: 'Capital Social Pagado:',
										   						name: 'Capsocialpag',
										   						id: 'monmax',
										   						width: 150,
										   						binding:true,
										   						formatonumerico:true,
										   						hiddenvalue:'',
										   						autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
										   						listeners:{
										   							'blur':function(objeto){
										   								var numero = objeto.getValue();
										   								valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
										   								objeto.setValue(valor);
										   							},
										   							'focus':function(objeto){
										   								var numero = formatoNumericoEdicion(objeto.getValue());
										   								objeto.setValue(numero);
										   							}
										   						},
										   						defaultvalue:0,
										   						allowBlank:true
										   					}]
								   					}]
							   			       },
							   			       {
								   				layout:"column",
								   				border:false,
								   				style: 'position:absolute;left:10px;top:47px',
								   				items:[{
									   					layout:"form",
									   					border:false,
									   					labelWidth:140,
									   					items:[ComboBanco]
									   				}]								                 
							   			       },
							   			       {
								   				layout:"column",
								   				border:false,
								   				style: 'position:absolute;left:10px;top:75px',
								   				items:[{
									   					layout:"form",
									   					border:false,
									   					labelWidth:140,
									   					items:[{
										   						xtype: 'textfield',
										   						labelSeparator :'',
										   						fieldLabel: 'Cuenta Bancaria Nro:',
										   						name: 'Cuenta',
										   						id: 'ctaban',
										   						autoCreate: {tag: 'input', type: 'text', size: '25', autocomplete: 'off', maxlength: '25', onkeypress: "return keyRestrict(event,'0123456789.,-');"},
										   						width: 200,
										   						binding:true,
										   						hiddenvalue:'',
										   						defaultvalue:'',
										   						allowBlank:true
										   					}]
								   					}]
							   			       },
							   			       {
								   				layout:"column",
								   				border:false,
								   				style: 'position:absolute;left:375px;top:47px',
								   				items:[{
									   					layout:"form",
									   					border:false,
									   					labelWidth:140,
									   					items:[ComboMoneda]
									   				}]								                 
							   			       },
							   			       {
								   				layout: "column",
								   				defaults: {border: false},
								   				style: 'position:absolute;left:375px;top:75px',
								   				items: [{
									   					layout: "form",
									   					border: false,
									   					labelWidth: 140,
									   					items: [cmbgradoemp]
									   				}]
							   			       },
							   			       {
								   				layout: "column",
								   				defaults: {border: false},
								   				style: 'position:absolute;left:10px;top:130px',
								   				items: [{
									   					layout: "form",
									   					border: false,
									   					labelWidth: 140,
									   					items: [cmbcontrib]
									   				}]
							   			       },
							   			       {
								   				layout: "column",
								   				defaults: {border: false},
								   				style: 'position:absolute;left:375px;top:130px',
								   				items: [{
									   					layout: "form",
									   					border: false,
									   					labelWidth: 140,
									   					items: [cmbtippersona]
									   				}]
									   			}]
						   				},
						   				{
							   			xtype:"fieldset", 
							   			title:"Ubicaci&#243;n Geografica",
							   			style: 'position:absolute;left:245px;top:426px',
							   			border:true,
							   			height:140,
							   			width:405,
							   			cls:'fondo',
							   			items:[ComboTipo,
							   			       Comboest,
							   			       Combomun,
							   			       Comboparroquia
							   			       ]
						   				},
								   		{   
							   			xtype:"fieldset", 
							   			title:'Informaci&#243;n Adicional',
							   			style: 'position:absolute;left:60px;top:573px',
							   			border:true,
							   			width: 750,
							   			cls :'fondo',
							   			height: 350,
							   			items:[{									
								   				layout: "column",
								   				defaults: {border: false},
								   				style: 'position:absolute;left:20px;top:15px',
								   				items: [{
									   					layout: "form",
									   					border: false,
									   					labelWidth: 130,
									   					items: [{
										   						xtype: 'textfield',
										   						labelSeparator :'',
										   						fieldLabel: 'P&#225;gina Web:',
										   						name: 'Pagweb',
										   						id: 'pagweb',
										   						autoCreate: {tag: 'input', type: 'text', size: '50', autocomplete: 'off', maxlength: '200', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789._-/\');"},
										   						width: 200,
										   						binding:true,
										   						hiddenvalue:'',
										   						defaultvalue:'',
										   						allowBlank:true
										   					}]
								   						}]
							   					},
								   				{
								   				layout: "column",
								   				defaults: {border: false},
								   				style: 'position:absolute;left:385px;top:15px',
								   				items: [{
									   					layout: "form",
									   					border: false,
									   					labelWidth: 75,
									   					items: [{
										   						xtype: 'textfield',
										   						labelSeparator :'',
										   						fieldLabel: 'Email:',
										   						name: 'Email',
										   						id: 'email',
										   						autoCreate: {tag: 'input', type: 'text', size: '50', autocomplete: 'off', maxlength: '200', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789._-/\@');"},
										   						width: 250,
										   						binding:true,
										   						hiddenvalue:'',
										   						vtype:'email',
										   						defaultvalue:'',
										   						allowBlank:true
										   					}]
								   						}]
								   				},
								   				{
								   				layout: "column",
								   				defaults: {border: false},
								   				style: 'position:absolute;left:20px;top:50px;font-size:12px;',
								   				items: [{
									   					layout: "form",
									   					border: false,
									   					labelWidth: 200,
									   					items: [{
										   						xtype: 'label',
										   						text: '(*) Tipo de Proveedor'
										   					}]
									   				}]
								   				},
								   				{
								   				layout: "form",
								   				style: 'position:absolute;left:170px;top:45px',
								   				border: false,
								   				labelWidth: 75,
								   				items: [{
									   					xtype: 'checkbox',
									   					fieldLabel: 'Proveedor',
									   					id: 'estpro',
									   					inputValue:1,
									   					binding:true,
									   					hiddenvalue:'',
									   					defaultvalue:'0',
									   					allowBlank:false
									   				}]
								   				},
								   				{
								   				layout: "form",
								   				style: 'position:absolute;left:350px;top:45px',
								   				border: false,
								   				labelWidth: 75,
								   				items: [{
									   					xtype: 'checkbox',
									   					fieldLabel: 'Contratista',
									   					id: 'estcon',
									   					inputValue:1,
									   					binding:true,
									   					hiddenvalue:'',
									   					defaultvalue:'0',
									   					allowBlank:false
									   				}]
								   				},  
								   				comcampocatctacontpag.fieldsetCatalogo,
												comcampocatctacontrec.fieldsetCatalogo,
								   				comcampocatctacontant.fieldsetCatalogo,
								   				{
								   				layout: "column",
								   				defaults: {border: false},
								   				style: 'position:absolute;left:20px;top:185px',
								   				items: [{
									   					layout: "form",
									   					border: false,
									   					labelWidth: 140,
									   					items: [{
										   						xtype: 'textarea',
										   						labelSeparator :'',
										   						fieldLabel: 'Observaci&#243;n:',
										   						name: 'Observacion',
										   						id: 'obspro',
										   						autoCreate: {tag: 'textarea', type: 'text', size: '100', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.,-');"},
										   						width: 550,
										   						height: 50,
										   						binding:true,
										   						hiddenvalue:'',
										   						defaultvalue:'',
										   						allowBlank:true
										   					}]
							   							}]
								   				},
								   				{
								   				layout:"form",
								   				border:false,
								   				labelWidth:150,
								   				style: 'position:absolute;left:20px;top:250px',
								   				items:[{
									   					xtype: "radiogroup",
									   					fieldLabel: "Estatus del Proveedor",
									   					columns: [180,180,200,200],
									   					id:"estprov",
														labelSeparator:"",
														binding:true,
														hiddenvalue:'',
														defaultvalue:'0',	
									   					items: [
									   					        {boxLabel: 'Activo', name: 'rbscbgenchq', inputValue: 0},
									   					        {boxLabel: 'Inactivo', name: 'rbscbgenchq', inputValue: 1},
									   					        {boxLabel: 'Bloqueado', name: 'rbscbgenchq', inputValue: 2},
									   					        {boxLabel: 'Suspendido', name: 'rbscbgenchq', inputValue: 3}
									   					        ]
								   					}]
								   				}]
								   		},
								   		{
								   			xtype:"hidden",
								   			name:'codespecialidad',
								   			id:'codesp',
								   			binding:true,
								   			value:'---',
								   			defaultvalue:'---'
								   		}]
					   			}]
			    		},
			    		{
				    	title:"Informaci&#243;n Especial para Comprobante de Retenci&#243;n",
					    labelWidth:150,
						layout:"form",
						frame:true,
						height:150,//Alto del Tab
						width:880,
						id:'tabficinfesp',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 130,
								height:800, //Alto del contenido del Tab
								items: [{
										xtype:"fieldset", 
										title:'Informaci&#243;n para Comprobante de Retenci&#243;n',
										style: 'position:absolute;left:55px;top:10px',
										border:true,
										width: 750,
										cls :'fondo',
										height: 100,
										items:[{
												layout: "form",
												style: 'position:absolute;left:190px;top:30px',
												border: false,
												labelWidth: 120,
												items: [{
														xtype: 'checkbox',
														fieldLabel: 'Proveedor Principal (Agencia de Viajes)',
														id: 'ageviapro',
														inputValue:1,
														labelSeparator :'',
														binding:true,
														hiddenvalue:'',
														defaultvalue:'0',
														allowBlank:true
													}]
												},
												{
												layout: "form",
												style: 'position:absolute;left:380px;top:30px',
												border: false,
												items: [{
														xtype: 'checkbox',
														fieldLabel: 'Proveedor Alterno (Aerol�nea)',
														id: 'aerolipro',
														inputValue:1,
														labelSeparator :'',
														binding:true,
														hiddenvalue:'',
														labelWidh: 100,
														defaultvalue:'0',
														allowBlank:true
													}]
												}]
										}]
								}]
			    		},
			    		{
					  	title:"Datos del Representante",
					    labelWidth:150,
						layout:"form",
						frame:true,
						height:200,//Alto del Tab
						width:880,
						id:'tabficdatrep',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 130,
								columnWidth: 0.5,
								height:200, //Alto del contenido del Tab
								items: [{
										xtype:"fieldset", 
										title:'Informaci&#243;n del Representante',
										style: 'position:absolute;left:55px;top:10px',
										border:true,
										width: 750,
										cls :'fondo',
										height: 150,
										items:[{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:130px;top:25px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 130,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'C&#233;dula:',
																name: 'Cedula',
																id: 'cedrep',
																width: 100,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
								      			},
								      			{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:130px;top:55px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 130,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Nombre:',
																name: 'Nombre',
																id: 'nomreppro',
																width: 300,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '300', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
								      			},
								      			{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:130px;top:85px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 130,
														columnWidth: 0.5,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Cargo:',
																name: 'Cargo',
																id: 'carrep',
																width: 300,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '300', autocomplete: 'off', maxlength: '35', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.,-');"},
																defaultvalue:'',
																allowBlank:true
															}]
														}]
								      			},
								      			{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:130px;top:115px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 130,
														columnWidth: 0.5,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Email:',
																name: 'Email',
																id: 'emailrep',
																width: 300,
																binding:true,
																hiddenvalue:'',
																vtype:'email',
																autoCreate: {tag: 'input', type: 'text', size: '300', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789._-/\@');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
								      			}]						
						
										}]
								}]
			    		},
			    		{
					  	title:"Datos del Registro",
					    labelWidth:150,
						layout:"form",
						frame:true,
						height:800,//Alto del Tab
						width:880,
						id:'tabficdatreg',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 130,
								height:800, //Alto del contenido del Tab
								items: [{
										xtype:"fieldset", 
										title:'Informaci&#243;n del Registro',
										style: 'position:absolute;left:55px;top:10px',
										border:true,
										width: 750,
										cls :'fondo',
										height: 700,
										items:[{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:25px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Registro Nacional de Contratistas:',
																name: 'Regnaccont',
																id: 'estregnaccont',
																width: 100,
																hiddenvalue:'',
																readOnly:true,
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15'},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:65px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'N&#176; Registro RNC:',
																name: 'Nregistro',
																id: 'ocei_no_reg',
																width: 100,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '17', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{										
												layout: "form",
												border: false,
												labelWidth: 200,
												style: 'margin-top:85px;margin-left:80px',
												items: [{
														 xtype:"datefield",
														 fieldLabel:"Fecha de Registro RNC",
														 name:"Fecregistrornc",
														 allowBlank:true,
														 width:100,
														 binding:true,
														 defaultvalue:'1900-01-01',
														 hiddenvalue:'',
														 id:"ocei_fec_reg",
														 autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
													}]
												},
												{
												layout: "form",
												border: false,
												labelWidth: 200,
												style: 'margin-top:4px;margin-left: 80px',
												items: [{
														 xtype:"datefield",
														 fieldLabel:"Fecha de Vencimiento Registro RNC",
														 name:"Fecvenregistrornc",
														 allowBlank:true,
														 width:100,
														 binding:true,
														 defaultvalue:'1900-01-01',
														 hiddenvalue:'',
														 id:"fecvenrnc",
														 autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:165px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'N&#176; Registro SSO:',
																name: 'Nregistrosso',
																id: 'numregsso',
																width: 100,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '100', maxlength: '15', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{
												layout: "form",
												border: false,
												labelWidth: 200,
												style: 'margin-top:44px;margin-left: 80px',
												items: [{
													 	 xtype:"datefield",
														 fieldLabel:"Fecha de Vencimiento SSO",
														 name:"Fecvensso",
														 allowBlank:true,
														 width:100,
														 defaultvalue:'1900-01-01',
														 binding:true,
														 hiddenvalue:'',
														 id:"fecvensso",
														 autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:235px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'N&#176; Registro INCE:',
																name: 'Nregistroince',
																id: 'numregince',
																width: 100,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '100', maxlength: '15', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{
												layout: "form",
												border: false,
												labelWidth: 200,
												style: 'margin-top:44px;margin-left: 80px',
												items: [{
														 xtype:"datefield",
														 fieldLabel:"Fecha de Vencimiento INCE",
														 name:"Fecvenince",
														 allowBlank:true,
														 width:100,
														 binding:true,
														 defaultvalue:'1900-01-01',
														 hiddenvalue:'',
														 id:"fecvenince",
														 autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
														}]
										
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:303px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Registro Subalterno:',
																name: 'Registrosub',
																id: 'registro',
																width: 150,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '150', maxlength: '35', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{
												layout: "form",
												border: false,
												labelWidth: 200,
												style: 'margin-top:42px;margin-left: 80px',
												items: [{
														 xtype:"datefield",
														 fieldLabel:"Fecha del Registro Subalterno",
														 name:"Fecregsub",
														 allowBlank:true,
														 width:100,
														 binding:true,
														 defaultvalue:'1900-01-01',
														 hiddenvalue:'',
														 id:"fecreg",
														 autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
												
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:375px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'N&#176; del Registro:',
																name: 'Nregistro',
																id: 'nro_reg',
																width: 100,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '100', maxlength: '15', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:405px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Tomo del Registro:',
																name: 'Tomoreg',
																id: 'tomo_reg',
																width: 50,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '50', maxlength: '5', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:445px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Registro Modificado:',
																name: 'Regmod',
																id: 'regmod',
																width: 150,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '150', maxlength: '35', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
															}]
														}]
												},
												{
												layout: "form",
												border: false,
												labelWidth: 200,
												style: 'margin-top:118px;margin-left: 80px',
												items: [{
														 xtype:"datefield",
														 fieldLabel:"Fecha de Registro Modificado",
														 name:"Fecregmod",
														 allowBlank:true,
														 width:100,
														 binding:true,
														 defaultvalue:'1900-01-01',
														 id:"fecregmod",
														 hiddenvalue:'',
														 autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
															
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:508px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Tomo Modificado:',
																name: 'Tommod',
																id: 'tommod',
																width: 50,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '50', maxlength: '5', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:538px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'N&#176; Modificado:',
																name: 'Nmodificado',
																id: 'nummod',
																width: 100,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '100', maxlength: '15', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:568px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'N&#176; Folio:',
																name: 'Nfolio',
																id: 'folreg',
																width: 50,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '50', maxlength: '5', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:598px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'N&#176; Folio Modificado:',
																name: 'Nfoliomod',
																id: 'folmod',
																width: 50,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '50', maxlength: '5', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:627px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'N&#176; Licencia:',
																name: 'Nlicencia',
																id: 'numlic',
																width: 100,
																binding:true,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '100', maxlength: '25', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
																defaultvalue:'',
																allowBlank:true
																}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:90px;top:652px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'checkbox',
																fieldLabel: 'Inspector',
																id: 'inspector',
																inputValue:1,
																binding:true,
																hiddenvalue:'',
																defaultvalue:'0',
																allowBlank:true
																}]
														}]
												}]
										}]
								}]
			    		},
			    		{
					  	title:"Socios",
					    labelWidth:150,
						layout:"form",
						frame:true,
						height:340,//Alto del Tab
						width:880,
						id:'tabficsocios',
						tbar:[{
								text:'Nuevo ',
								iconCls:'menunuevo',
								id:'nuevosocio',
								handler: irNuevoSocio
								},
								{
								text:'Guardar ',
								iconCls:'menuguardar',
								id:'guardarsocio',
								handler: irGuardarSocio
								},
								{
								text:'Buscar ',
								iconCls:'menubuscar',
								id:'buscarsocio',
								handler: irBuscarSocio
								},
								{
								text:'Eliminar ',
								iconCls:'menueliminar',
								id:'eliminarsocio',
								handler: irEliminarSocio
								}],
						listeners:{
			    			'beforeshow': function(componente)
			    			{
			    				if(Ext.getCmp('nompro').getValue() == "")
			    				{
									Ext.Msg.alert('Mensaje','Debe cargar un proveedor para ver esta opci&#243;n');
									Ext.getCmp('tabfichaprov').activate('tabficdatbas');
								return false;
			    				}
			    				else
			    				{
			    					Ext.getCmp('nom_label').setText(Ext.getCmp('nompro').getValue());
			    				return true;
			    				}
			    			}
			    		},
			    		items: [{
			    				layout: "form",
								border: false,
								labelWidth: 130,
								columnWidth: 0.5,
								height:340, //Alto del contenido del Tab
								items: [{
										xtype:"fieldset", 
										title:'Informaci&#243;n de Socios',
										style: 'position:absolute;left:55px;top:10px',
										border:true,
										width: 750,
										cls :'fondo',
										height: 250,
										items:[{					
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:220px;top:10px;font-size:14px;',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 200,
														items: [{
																xtype: 'label',
																id:'nom_label',	
																text: 'Proveedor',
																style:'font-weight: bold'
																}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:100px;top:35px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 100,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'C&#233;dula',
																name: 'csocio',
																id: 'cedsocio',
																width: 100,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789');"},
																defaultvalue:'',
																allowBlank:false
															}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:100px;top:63px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 100,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Nombre',
																name: 'Nomsocio',
																id: 'nomsocio',
																width: 300,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '300', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ');"},
																defaultvalue:'',
																allowBlank:true
															}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:100px;top:91px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 100,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Apellido',
																name: 'Apesocio',
																id: 'apesocio',
																width: 300,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '300', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ');"},
																defaultvalue:'',
																allowBlank:true
															}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:100px;top:120px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 100,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Direcci&#243;n',
																name: 'Dirsocio',
																id: 'dirsocio',
																width: 300,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '300', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!@%&/\()�?�-+*[]{}');"},
																defaultvalue:'',
																allowBlank:true
															}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:100px;top:150px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 100,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Cargo',
																name: 'Carsocio',
																id: 'carsocio',
																width: 300,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '300', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789,.-');"},
																defaultvalue:'',
																allowBlank:true
															}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:100px;top:180px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 100,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Tel&#233;fono',
																name: 'Telsocio',
																id: 'telsocio',
																width: 200,
																hiddenvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '200', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789-');"},
																defaultvalue:'',
																allowBlank:true
															}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:100px;top:210px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 100,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Email',
																name: 'emailsocio',
																id: 'emailsoc',
																width: 300,
																hiddenvalue:'',
																vtype:'email',
																autoCreate: {tag: 'input', type: 'text', size: '300', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789._-/\@');"},
																defaultvalue:'',
																allowBlank:true
															}]
														}]
												}]
										}]
			    				}]	
			    		},
			    		{
					  	title:"Documentos del Proveedor",
					    labelWidth:150,
						layout:"form",
						frame:true,
						height:300,//Alto del Tab
						width:880,
						id:'tabficdocproveedor',
						tbar:[{
				    			text:'Nuevo ',
				    			iconCls:'menunuevo',
				    			id:'nuevodocumento',
				    			handler: irNuevoDocumento
				    			},
				    			{
			    				text:'Guardar ',
			    				iconCls:'menuguardar',
			    				id:'guardardocumento',
			    				handler: irGuardarDocumento
				    			},
				    			{
			    				text:'Buscar ',
			    				iconCls:'menubuscar',
			    				id:'buscardocumento',
			    				handler: irBuscarDocumento
				    			},
				    			{
			    				text:'Eliminar ',
			    				iconCls:'menueliminar',
			    				id:'eliminardocumento',
			    				handler: irEliminarDocumento
				    			}],
				      listeners:{
			    			'beforeshow': function(componente)
			    			{
								if(Ext.getCmp('nompro').getValue() == "")
		  	 				    {
									Ext.Msg.alert('Mensaje','Debe cargar un proveedor para ver esta opci&#243;n');
									Ext.getCmp('tabfichaprov').activate('tabficdatbas');
									return false;
								}
		  						else
								{
									Ext.getCmp('nom_label2').setText(Ext.getCmp('nompro').getValue());
									return true;
								}
			    			}
			    	},
			    	items: [{
							layout: "form",
							border: false,
							labelWidth: 130,
							columnWidth: 0.5,
							height:300, //Alto del contenido del Tab
							items: [{
									xtype:"fieldset", 
									title:'Documentos del Proveedor',
									style: 'position:absolute;left:55px;top:10px',
									border:true,
									width: 750,
									cls :'fondo',
									height: 230,
									items:[{
											layout: "column",
											defaults: {border: false},
											style: 'position:absolute;left:220px;top:5px;font-size:14px;',
											items: [{
													layout: "form",
													border: false,
													labelWidth: 200,
													items: [{
															xtype: 'label',
															id:'nom_label2',	
															text: 'Proveedor',
															style:'font-weight: bold'
															}]
													}]
											},
											comcampocoddocumento.fieldsetCatalogo,
											{
											layout: "form",
											border: false,
											labelWidth: 130,
											style: 'margin-top:70px;margin-left: 100px',
											items: [{
													 xtype:"datefield",
													 fieldLabel:"Fecha de Recepci&#243;n",
													 name:"Fecregdoc",
													 allowBlank:false,
													 width:100,
													 id:"fecrecdoc",
													 autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
													}]
											},
											{
											layout: "form",
											border: false,
											labelWidth: 130,
											style: 'margin-top:5px;margin-left: 100px',
											items: [{
													 xtype:"datefield",
													 fieldLabel:"Fecha de Vencimiento",
													 name:"Fecvendoc",
													 allowBlank:false,
													 width:100,
													 id:"fecvendoc",
													 autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
													}]
											},
											{
											layout: "form",
											border: false,
											style: 'margin-top:5px;margin-left: 100px',
											items: [cmbestatusdoc]
											},
											{
											layout: "form",
											border: false,
											style: 'margin-top:5px;margin-left: 100px',
											items: [cmbestatusoriginal]
											}]
									}]
			    			}]
			    	},
		    		{
				  	title:"Calificaci&#243;n del Proveedor",
				    labelWidth:150,
					layout:"form",
					frame:true,
					height:330,//Alto del Tab
					width:880,
					id:'tabficcalifproveedor',
					tbar:[{
				    		text:'Nuevo ',
				        	iconCls:'menunuevo',
				        	id:'nuevocalificacion',
				        	handler: irNuevoCalif
				    		},
				    		{
			    			text:'Guardar ',
			    			iconCls:'menuguardar',
			    			id:'guardarcalificacion',
			    			handler: irGuardarCalif
				    		},
				    		{
			    			text:'Buscar ',
			    			iconCls:'menubuscar',
			    			id:'buscarcalificacion',
			    			handler: irBuscarCalif
				    		},
				    		{
			    			text:'Eliminar ',
			    			iconCls:'menueliminar',
			    			id:'eliminarcalificacion',
			    			handler: irEliminarCalif
				    		}],
					listeners:{
			    		'beforeshow': function(componente)
			    		{
		    				if(Ext.getCmp('nompro').getValue() == "")
	  	 				    {
								Ext.Msg.alert('Mensaje','Debe cargar un proveedor para ver esta opci&#243;n');
								Ext.getCmp('tabfichaprov').activate('tabficdatbas');
								return false;
							}
	  						else
							{
								Ext.getCmp('nom_label3').setText(Ext.getCmp('nompro').getValue());
								return true;
							}
			    		}
			    	},
			    	items: [{
							layout: "form",
							border: false,
							labelWidth: 130,
							columnWidth: 0.5,
							height:330, //Alto del contenido del Tab
							items: [{
									xtype:"fieldset", 
									title:'Calificaci&#243;n del Proveedor',
									style: 'position:absolute;left:55px;top:10px',
									border:true,
									width: 750,
									cls :'fondo',
									height: 260,
									items:[{
											layout: "column",
											defaults: {border: false},
											style: 'position:absolute;left:220px;top:10px;font-size:14px;',
											items: [{
													layout: "form",
													border: false,
													labelWidth: 250,
													items: [{
															xtype: 'label',
															id:'nom_label3',	
															text: 'Proveedor',
															style:'font-weight: bold'
															}]
													}]
											},
											comcampocodcalificacion.fieldsetCatalogo,
											{
											layout: "form",
											border: false,
											labelWidth: 250,
											style: 'margin-top:60px;margin-left: 100px',
											items: [cmbestatuscalificacion]
											},
											comcampocodnivclasif.fieldsetCatalogo,
											{
											layout: "form",
											border: false,
											labelWidth: 250,
											style: 'margin-top:35px;margin-left: 100px',
											items: [cmbnivelestatus]
											},
											{
											layout:"column",
											border:false,
											style: 'position:absolute;left:111px;top:160px',
											items:[{
													layout: "form",
													border: false,
													labelWidth: 250,
													items:[{
															xtype: 'textfield',
															labelSeparator :'',
															fieldLabel: 'Monto M&#237;nimo de Contrataci&#243;n',
															name: 'Monmincon',
															id: 'monmincon',
															width: 150,
															hiddenvalue:'',
															autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
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
															},
															defaultvalue:0,
															allowBlank:false
															}]
													  }]									
											},
											{
											layout:"column",
											border:false,
											style: 'position:absolute;left:111px;top:190px',
											items:[{
													layout: "form",
													border: false,
													labelWidth: 250,
													items:[{
															xtype: 'textfield',
															labelSeparator :'',
															fieldLabel: 'Monto M&#225;ximo de Contrataci&#243;n',
															name: 'Monmaxcon',
															id: 'monmaxcon',
															width: 150,
															hiddenvalue:'',
															autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
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
															},
															defaultvalue:0,
															allowBlank:false
														  	}]
										  	  		}]
											},
											{
											layout:"column",
											border:false,
											style: 'position:absolute;left:111px;top:220px',
											items:[{
													layout: "form",
													border: false,
													labelWidth: 250,
													items:[{
															xtype: 'textfield',
															labelSeparator :'',
															fieldLabel: 'Nivel Financiero Estimado de Contrataci&#243;n',
															name: 'Monfincon',
															id: 'monfincon',
															width: 150,
															hiddenvalue:'',
															autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
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
															},
															defaultvalue:0,
															allowBlank:false
														 }]
										  	  		}]
											}]
									}]
			    			}]
			    	},
		    		{
	    			title:"Especialidades por Proveedor",
	    			labelWidth:150,
	    			layout:"form",
	    			frame:true,
	    			height:500,//Alto del Tab
	    			width:880,
	    			id:'tabficespxproveedor',
	    			tbar:[{
		    				text:'Grabar ',
		    				iconCls:'menuguardar',
		    				id:'guardarespecialidad',
		    				handler: irGuardarEspecialidad
		    				},
			    			{
		    				text:'Cancelar ',
		    				iconCls:'bmenucancelar',
		    				id:'cancelarespecialidad',
		    				handler: buscarEspecialidades
			    			}],
			    	listeners:{
			    		'beforeshow': function(componente)
			    		{
				    		if(Ext.getCmp('nompro').getValue() == "")
				    		{
								Ext.Msg.alert('Mensaje','Debe cargar un proveedor para ver esta opci&#243;n');
								Ext.getCmp('tabfichaprov').activate('tabficdatbas');
							return false;
				    		}
		  					else
							{
								Ext.getCmp('nom_label4').setText(Ext.getCmp('nompro').getValue());
							return true;
							}
			    		}
			    	},
			    	items: [{
							layout: "form",
							border: false,
							labelWidth: 130,
							height:500, //Alto del contenido del Tab
							items: [{
									xtype:"fieldset", 
									title:'',
									style: 'position:absolute;left:5px;top:3px',
									border:true,
									width: 850,
									cls :'fondo',
									height: 450,
									items:[{
											layout: "column",
											defaults: {border: false},
											style: 'position:absolute;left:120px;top:5px;font-size:14px;',
											items: [{
													layout: "form",
													border: false,
													labelWidth: 200,
													items: [{
															xtype: 'label',
															id:'nom_label4',	
															text: 'Proveedor',
															style:'font-weight: bold'
														}]
													}]
											},
											{
											layout: "column",
											defaults: {border: false},
											style: 'position:absolute;left:100px;top:50px',
											items: [{
													layout: "form",
													border: false,
													labelWidth: 100,
													items: [gridespecialidad]
												}]
											}]
									}]
			    			}]
		    		},
		    		{
				  	title:"Deducciones por Proveedor",
				    labelWidth:150,
					layout:"form",
					frame:true,
					height:500,//Alto del Tab
					width:880,
					id:'tabficdeducxproveedor',
					tbar:[{
						text:'Grabar ',
						iconCls:'menuguardar',
						id:'guardardeduccion',
						handler: irGuardarDeduccion
						},
						{
						text:'Cancelar ',
						iconCls:'bmenucancelar',
						id:'cancelarespecialidad',
						handler: buscarDeducciones
						}],
					listeners:{
	  	 				'beforeshow': function(componente)
						 {
							if(Ext.getCmp('nompro').getValue() == "")
	  	 				    {
								Ext.Msg.alert('Mensaje','Debe cargar un proveedor para ver esta opci&#243;n');
								Ext.getCmp('tabfichaprov').activate('tabficdatbas');
								return false;
							}
	  						else
							{
								Ext.getCmp('nom_label5').setText(Ext.getCmp('nompro').getValue());
								return true;
							}
						 }
		    		},
		    		items: [{
							layout: "form",
							border: false,
							labelWidth: 130,
							height:500, //Alto del contenido del Tab
							items: [{	
									xtype:"fieldset", 
									title:'',
									style: 'position:absolute;left:5px;top:3px',
									border:true,
									width: 850,
									cls :'fondo',
									height: 450,
									items:[{
											layout: "column",
											defaults: {border: false},
											style: 'position:absolute;left:220px;top:5px;font-size:14px;',
											items: [{
													layout: "form",
													border: false,
													labelWidth: 200,
													items: [{
															xtype: 'label',
															id:'nom_label5',	
															text: 'Proveedor',
															style:'font-weight: bold'
															}]
													}]
											},
											{
											layout: "column",
											defaults: {border: false},
											style: 'position:absolute;left:100px;top:50px',
											items: [{
													layout: "form",
													border: false,
													labelWidth: 100,
													items: [griddeduccion]
												}]
											}]
									}]
		    				}]
		    		}]
	}]
	});
	buscarCodigo();
	llenarComboEmpresa();
	llenarComboBanco();
	llenarComboMoneda();
	ComboTipo.addListener('select',agregar_combo_estado);
	Comboest.addListener('select',agregar_combo_municipio);
	Combomun.addListener('select',agregar_combo_parroquia);
	Comboparroquia.addListener('select',function(combo,record,index){Comboparroquia.valor = codpar=record.get('codpar')});
});



//-------------------------------------------------------------------------------------------------------------------
function buscarEspecialidades(){
	var cod_pro= Ext.getCmp('cod_pro').getValue();
	var JSONObject2 = {
						'operacion'   : 'espc_prov',
						'cod_pro' : cod_pro
					 }
	var ObjSon2 = JSON.stringify(JSONObject2);
	var parametros = 'ObjSon='+ObjSon2; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request)
		{
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoEspecialidades = eval('(' + datos + ')');
			if(objetoEspecialidades!='')
			{
				datastoreespxprov.loadData(objetoEspecialidades);
			}
		}	
	});
	buscarDeducciones();
	buscardenoEstado();
	buscardenoMunicipio();
	buscardenoParroquia();
	var fechaven = Ext.getCmp('fecvenrnc').getValue();
	var estatusReg = Ext.getCmp('estregnaccont').getValue();
	var fechahoy = new Date();
	fechahoy = fechahoy.format(Date.patterns.fechacorta);
	fechaven = fechaven.format(Date.patterns.fechacorta);
	var compara = ue_comparar_intervalo(fechaven, fechahoy);
	if (compara)
	{
		estatusReg="VENCIDO";
		Ext.getCmp('estregnaccont').setValue(estatusReg);
	}
	else
	{
		estatusReg="VIGENTE";
		Ext.getCmp('estregnaccont').setValue(estatusReg);
	}
}

function uf_verificar_rif(campo,seniat)
{
		obtenerMensaje('procesar','','Verificando con el SENIAT');
		var myJSONObject = {
				"operacion":'verificar_rif',
				"seniat" : seniat,
				"rifpro" : campo
			};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) 
			{ 
				Ext.Msg.hide();
				datos = result.responseText;
				var datajson = eval('(' + datos + ')');
				if (datajson.raiz.valido==true)
				{
					if(datajson.raiz.mensaje!="")
					{
						Ext.Msg.show({
									title:'Advertencia',
									msg: datajson.raiz.mensaje,
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.WARNING
									});
					}
					else
					{
						if (seniat)
						{
							Ext.getCmp('nompro').setValue(datajson.raiz.nompro);
						}
					}
				}
				else
				{
					Ext.Msg.show({
								title:'Advertencia',
								msg: datajson.raiz.mensaje,
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.WARNING
								});
					Ext.getCmp('rifpro').setValue('');
				}
			},
			failure: function ( result, request)
			{ 
				Ext.MessageBox.alert('Error', ' Al validar el RIF'); 
			}
		});
}

function buscardenoEstado()
{
		var codpai = Ext.getCmp('codpai').getValue();
		var codest = Ext.getCmp('codest').getValue();
		var myJSONObject ={
				"operacion": 'denom_estado',
				"codpai":codpai
		};	
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				datos = resultado.responseText;
				if(datos!='')
				{
					var DatosNuevo = eval('(' + datos + ')');
				}
				DataStoreEstado.loadData(DatosNuevo);
				Ext.getCmp('codest').setValue(codest);
			}
		})	
}

function buscardenoMunicipio()
{
		var codpai = Ext.getCmp('codpai').getValue();
		var codest = Ext.getCmp('codest').getValue();
		var codmun = Ext.getCmp('codmun').getValue();
		var myJSONObject ={
				"operacion": 'denom_municipio',
				"codpai":codpai,
				"codest":codest
		};	
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				datos = resultado.responseText;
				if(datos!='')
				{
					var DatosNuevo = eval('(' + datos + ')');
				}
				DataStoreMunicipio.loadData(DatosNuevo);
				Ext.getCmp('codmun').setValue(codmun);
			}
		})	
}
	
function buscardenoParroquia()
{
		var codpai = Ext.getCmp('codpai').getValue();
		var codest = Ext.getCmp('codest').getValue();
		var codmun = Ext.getCmp('codmun').getValue();
		var codpar = Ext.getCmp('codpar').getValue();
		var myJSONObject ={
				"operacion": 'denom_parroquia',
				"codpai":codpai,
				"codest":codest,
				"codmun":codmun
		};	
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				datos = resultado.responseText;
				if(datos!='')
				{
					var DatosNuevo = eval('(' + datos + ')');
				}
				DataStoreParroquia.loadData(DatosNuevo);
				Ext.getCmp('codpar').setValue(codpar);
			}
		})	
	}

function buscarDeducciones(){
	var cod_pro= Ext.getCmp('cod_pro').getValue();
	var JSONObject2 = {
						'operacion'   : 'deduc_prov',
						'cod_pro' : cod_pro
					 }
	var ObjSon2 = JSON.stringify(JSONObject2);
	var parametros = 'ObjSon='+ObjSon2; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request)
		{
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoDeducciones = eval('(' + datos + ')');
			if(objetoDeducciones!='')
			{
				datastorededxprov.loadData(objetoDeducciones);
			}
		}	
		});
}


function buscarCodigo()
{
		var myJSONObject = 
		{
				"operacion":"buscarcodigo" 
			};
				
		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) { 
	            var codigo = result.responseText;
				if (codigo != "") 
				{
					limpiarFormulario(plproveedor);
					Ext.getCmp('cod_pro').setValue(codigo);
					Ext.getCmp('codpai').setValue('---seleccione---');
					Ext.getCmp('codest').setValue('---seleccione---');
					Ext.getCmp('codmun').setValue('---seleccione---');
					Ext.getCmp('codest').setValue('---seleccione---');
					Ext.getCmp('codpar').setValue('---seleccione---');
				}
			},
			failure: function ( result, request)
			{ 
					Ext.MessageBox.alert('Error', 'No se pudo obtener el Codigo'); 
			}
		});		
	}

function mostrarEstatus(fecha2){
	var fecha1 = new Date();
	fecha1 = fecha1.format(Date.patterns.fechacorta)
	
	vali=false;
	dia1 = fecha1.substr(0,2);
	mes1 = fecha1.substr(3,2);
	ano1 = fecha1.substr(6,4);
	dia2 = fecha2.substr(0,2);
	mes2 = fecha2.substr(3,2);
	ano2 = fecha2.substr(6,4);
	if (ano1 < ano2)
	{
		vali = true; 
	}
    else 
	{ 
    	if (ano1 == ano2)
	 	{ 
      		if (mes1 < mes2)
	  		{
	   			vali = true; 
	  		}
      		else 
	  		{ 
       			if (mes1 == mes2)
	   			{
 					if (dia1 <= dia2)
					{
		 				vali = true; 
					}
	   			}
      		} 
     	} 	
	}
	if (vali)
	{
		return 'VIGENTE';
	}
	else
	{
		return 'VENCIDO';	
	}
}

function irNuevo()
{
	var myJSONObject ={
		"operacion":"buscarcodigo" 
	};
		
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) { 
	        var codigo = result.responseText;
			if (codigo != "") {
				limpiarFormulario(plproveedor);
				Ext.getCmp('cod_pro').setValue(codigo);
				Ext.getCmp('codpai').setValue('---seleccione---');
				Ext.getCmp('codest').setValue('---seleccione---');
				Ext.getCmp('codmun').setValue('---seleccione---');
				Ext.getCmp('codest').setValue('---seleccione---');
				Ext.getCmp('codpar').setValue('---seleccione---');
				irNuevoSocio();
				irNuevoDocumento();
				irNuevoCalif();
				gridespecialidad.store.removeAll();
				griddeduccion.store.removeAll();
			}
		},
		failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});		
}


function agregar_combo_estado(par,rec)
{
		ComboTipo.valor = codpai = rec.get('codpai');
		var myJSONObject ={
				"operacion": 'catalogocomboestado',
				"codpai":codpai
		};	
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				datos = resultado.responseText;
				if(datos!='')
				{
					var DatosNuevo = eval('(' + datos + ')');
				}
				DataStoreEstado.loadData(DatosNuevo);
			}
		})	
}

function agregar_combo_municipio(par,rec)
{
		ComboTipo.valor = codpai = rec.get('codpai');
		Comboest.valor  = codest = rec.get('codest');
		var myJSONObject ={
				"operacion": 'catalogocombomuni',
				"codpai":codpai,
				"codest":codest
		};	
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				datos = resultado.responseText;
				if(datos!='')
				{
					var DatosNuevo = eval('(' + datos + ')');
				}
				DataStoreMunicipio.loadData(DatosNuevo);
			}
		})	
}

function agregar_combo_parroquia(par,rec)
{
		ComboTipo.valor = codpai = rec.get('codpai');
		Comboest.valor  = codest = rec.get('codest');
		Combomun.valor  = codmun = rec.get('codmun');
		var myJSONObject ={
				"operacion": 'catalogocomboparroquia',
				"codpai":codpai,
				"codest":codest,
				"codmun":codmun
		};	
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				datos = resultado.responseText;
				if(datos!='')
				{
					var DatosNuevo = eval('(' + datos + ')');
				}
				DataStoreParroquia.loadData(DatosNuevo);
			}
		})	
}


function irBuscar()
{
	limpiarFormulario(plproveedor);
        ventanaCatalogo();

	function ventanaCatalogo(){			
		var reVentana = Ext.data.Record.create([
					{name: 'cod_pro'},
					{name: 'nompro'},
					{name: 'dirpro'},
					{name: 'rifpro'},
					{name: 'telpro'},
					{name: 'faxpro'},
					{name: 'nacpro'},
					{name: 'nitpro'},
					{name: 'fecreg'},
					{name: 'capital'},
					{name: 'sc_cuenta'},
					{name: 'obspro'},
					{name: 'estpro'},
					{name: 'estcon'},
					{name: 'estaso'},
					{name: 'ocei_fec_reg'},
					{name: 'ocei_no_reg'},
					{name: 'monmax'},
					{name: 'cedrep'},
					{name: 'nomreppro'},
					{name: 'emailrep'},
					{name: 'carrep'},
					{name: 'registro'},
					{name: 'nro_reg'},
					{name: 'tomo_reg'},
					{name: 'folreg'},
					{name: 'fecregmod'},
					{name: 'regmod'},
					{name: 'nummod'},
					{name: 'tommod'},
					{name: 'folmod'},
					{name: 'inspector'},
					{name: 'foto'},
					{name: 'codbansig'},
					{name: 'codban'},
					{name: 'codmon'},
					{name: 'codtipoorg'},
					{name: 'codesp'},
					{name: 'ctaban'},
					{name: 'numlic'},
					{name: 'fecvenrnc'},
					{name: 'numregsso'},
					{name: 'fecvensso'},
					{name: 'numregince'},
					{name: 'fecvenince'},
					{name: 'estprov'},
					{name: 'pagweb'},
					{name: 'email'},
					{name: 'codpai'},
					{name: 'codest'},
					{name: 'codmun'},
					{name: 'codpar'},
					{name: 'graemp'},
					{name: 'tipconpro'},
					{name: 'sc_cuentarecdoc'},
					{name: 'tipperpro'},
					{name: 'sc_ctaant'},
					{name: 'denbansig'},
					{name: 'denominacion'},
					{name: 'denominacion_2'},
					{name: 'denominacion_rec'},
					{name: 'ageviapro'},
					{name: 'aerolipro'}
		]);
						
		var dsVentana =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reVentana)
		});
									
		var cmVentana = new Ext.grid.ColumnModel([
				new Ext.grid.CheckboxSelectionModel(),
				{header: "<H1 align='center'>C&#243;digo</H1>", width: 40, sortable: true, dataIndex: 'cod_pro'},
				{header: "<H1 align='center'>Nombre</H1>", width: 50, sortable: true, dataIndex: 'nompro'},
				{header: "<H1 align='center'>Direcci&#243;n</H1>", width: 50, sortable: true, dataIndex: 'dirpro'},
				{header: "<H1 align='center'>RIF</H1>", width: 40, sortable: true, dataIndex: 'rifpro'},
				{header: "<H1 align='center'>Fecha</H1>", width: 40, sortable: true, dataIndex: 'fecvenrnc'},
				{header: "<H1 align='center'>Reg. Nac. Contratistas</H1>", width: 70, sortable: true, dataIndex: 'fecvenrnc',renderer:mostrarEstatus}
		]);
			
		gridVentanaProveedor = new Ext.grid.EditorGridPanel({
			width:650,
			height:250,
			frame:true,
			title:"",
			style: 'position:absolute;left:20px;top:210px',
			autoScroll:true,
			border:true,
			ds: dsVentana,
			cm: cmVentana,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:true}),
			stripeRows: true,
			viewConfig: {forceFit:true}
		});
			 
		var formVentanaCatalogo= new Ext.FormPanel({
			width: 690,
			height: 480,
			title: '',
			style: 'position:absolute;left:5px;top:10px',
			frame: true,
			autoScroll:false,
			items: [{
					xtype:"fieldset", 
					title:'Datos del Proveedor',
					style: 'position:absolute;left:20px;top:5px',
					border:true,
					height:185,
					cls: 'fondo',
					width:650,
					items:[{
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:15px;top:15px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 80,
									items: [{
											xtype: 'textfield',
											labelSeparator :'',
											fieldLabel: 'C&#243;digo',
											name: 'codigo',
											id: 'codi_pro',									
											width: 150,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'',
											autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789');"},
											changeCheck: function(){
												var textvalor = this.getValue();
												dsVentana.filter('cod_pro',textvalor,true);
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
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:15px;top:45px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 80,
									items: [{
											xtype: 'textfield',
											labelSeparator :'',
											fieldLabel: 'Nombre',
											name: 'nombre',
											id: 'nombpro',
											width: 300,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'',
											changeCheck: function(){
												var v = this.getValue();
												act_data_store_proveedores('nompro',v);
												if(String(v) !== String(this.startValue)){
													this.fireEvent('change', this, v, this.startValue);
												} 
											},							 
											initEvents : function(){
												AgregarKeyPress(this);
											}
										}]
								}]
							},
							{
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:15px;top:75px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 80,
									items: [{
											xtype: 'textfield',
											labelSeparator :'',
											fieldLabel: 'Direcci&#243;n',
											name: 'direccion',
											id: 'direcpro',
											width: 450,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'',
											changeCheck: function(){
												var v = this.getValue();
												act_data_store_proveedores('dirpro',v);
												if(String(v) !== String(this.startValue)){
													this.fireEvent('change', this, v, this.startValue);
												} 
											},							 
											initEvents : function(){
												AgregarKeyPress(this);
											}
										}]
									}]
							},
							{
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:15px;top:105px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 80,
									items: [{
											xtype: 'textfield',
											labelSeparator :'',
											fieldLabel: 'R.I.F',
											name: 'rif',
											id: 'rifprov',
											width: 150,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'',
											autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '12'},
											changeCheck: function(){
												var v = this.getValue();
												act_data_store_proveedores('rifpro',v);
												if(String(v) !== String(this.startValue)){
													this.fireEvent('change', this, v, this.startValue);
												} 
											},							 
											initEvents : function(){
												AgregarKeyPress(this);
											},
									}]
								}]
							},
							{	
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:300px;top:109px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 400,
									items: [{
											xtype: 'label',
											text: 'El formato correcto del RIF es: [JGVE]-[99999999]-[9]',
											disabled: true
											}]
									}]
							},
							{
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:15px;top:135px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 80,
									items: [{
											xtype: 'datefield',
											labelSeparator :'',
											fieldLabel: 'Fecha Desde',
											name: 'fecdesde',
											id: 'fecdes',
											width: 150,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'1900-01-01',
											autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"},
											changeCheck: function(){
												var v = this.getValue();
												act_data_store_proveedores('fecvenrnc',v);
												if(String(v) !== String(this.startValue)){
													this.fireEvent('change', this, v, this.startValue);
												} 
											},							 
											initEvents : function(){
												AgregarKeyPress(this);
											}
										}]
									}]
							},
							{
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:300px;top:135px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 80,
									items: [{
											xtype: 'datefield',
											labelSeparator :'',
											fieldLabel: 'Fecha Hasta',
											name: 'fechasta',
											id: 'fechas',
											width: 150,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'1900-01-01',
											autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"},
											changeCheck: function(){
												var v = this.getValue();
												act_data_store_proveedores('fecvenrnc',v);
												if(String(v) !== String(this.startValue)){
													this.fireEvent('change', this, v, this.startValue);
												} 
											},							 
											initEvents : function(){
												AgregarKeyPress(this);
											}
										}]
									}]
							},
							{
							layout:"column",
							defaults: {border: false},
							style: 'position:absolute;left:560px;top:135px',
							border:false,
							items:[{
									layout:"form",
									border:false,
									items:[{					
											xtype: 'button',
											labelSeparator :'',
											fieldLabel: '',
											id: 'btnBuscarBene',
											text: 'Buscar',
											width: 300,
											height: 300,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'',
											iconCls: 'menubuscar',
											handler: function(){
												if((Ext.getCmp('codi_pro').getValue() == '') && (Ext.getCmp('nombpro').getValue() == '') 
												&& (Ext.getCmp('direcpro').getValue() == '') && (Ext.getCmp('rifprov').getValue() == '')
												&& (Ext.getCmp('fecdes').getValue() == '') && (Ext.getCmp('fechas').getValue() == '')){
													Ext.Msg.show({
														title:'Mensaje',
														msg:'Debe seleccionar al menos un par&#225;metro de b&#250;squeda',
														buttons: Ext.Msg.OK,
														icon: Ext.MessageBox.INFO
													});
												}
												else{
													obtenerMensaje('procesar','','Buscando Datos');
													
													var codpro  = Ext.getCmp('codi_pro').getValue();
													var nompro  = Ext.getCmp('nombpro').getValue();
													var dirpro  = Ext.getCmp('direcpro').getValue();
													var rifpro  = Ext.getCmp('rifprov').getValue();
													if ((Ext.getCmp('fecdes').getValue() != '') && (Ext.getCmp('fechas').getValue() != '')){
														var fecdes  = Ext.getCmp('fecdes').getValue().format('Y-m-d');
														var fechas  = Ext.getCmp('fechas').getValue().format('Y-m-d');
													}
													else{
														var fecdes = Ext.getCmp('fecdes').getValue();
														var fechas  = Ext.getCmp('fechas').getValue();
													}
																	
													var JSONObject = {
														'operacion'	: 'catalogo',
														'codi_pro'  : codpro,
														'nombpro'   : nompro,
														'direcpro' 	: dirpro,
														'rifprov'   : rifpro,
														'fecdes'   	: fecdes,
														'fechas' 	: fechas
													}			
													var ObjSon = JSON.stringify(JSONObject);
													var parametros = 'ObjSon='+ObjSon; 
													Ext.Ajax.request({
														url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
														params : parametros,
														method: 'POST',
														success: function ( resultado, request){
															Ext.Msg.hide();
															var datos = resultado.responseText;
															var objetoProveedores = eval('(' + datos + ')');
															if(objetoProveedores!=''){
									   							if(objetoProveedores!='0'){
									   								if(objetoProveedores.raiz == null || objetoProveedores.raiz ==''){
									   									Ext.MessageBox.show({
														 					title:'Advertencia',
														 					msg:'No existen datos para mostrar',
														 					buttons: Ext.Msg.OK,
														 					icon: Ext.MessageBox.WARNING
														 				});
																	}
																	else{
																		gridVentanaProveedor.store.loadData(objetoProveedores);
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
														}	
													});
												}
											}
										}]
									}]
								}]
							},gridVentanaProveedor]  
			});
		var ventanaEstructura = new Ext.Window({
			width:700,
			height:550,
			border:false,
			modal: true,
			closable:false,
			frame:true,
			title:"<H1 align='center'>Cat&#225;logo de Proveedores</H1>",
			items:[formVentanaCatalogo],
			buttons:[{
					text:'Aceptar',  
					handler: function(){
						var registro = gridVentanaProveedor.getSelectionModel().getSelected();	        	
						if(registro!= undefined)
                                                {
							irNuevoSocio();
							irNuevoDocumento();
							irNuevoCalif();
							gridespecialidad.store.removeAll();
							griddeduccion.store.removeAll();
							setDataFrom(plproveedor,registro);
							gridVentanaProveedor.destroy();
							ventanaEstructura.destroy();
							buscarEspecialidades();
							Actualizar = 1;
						}
						else{
							Ext.MessageBox.show({
								title:'Mensaje',
								msg:'Debe seleccionar al menos un registro a procesar',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
						 }
					}
				},
				{
					text: 'Salir',
					handler: function(){
						ventanaEstructura.destroy();
					}
				}] 	
			});
		
		function act_data_store_proveedores(criterio,cadena){
			dsVentana.filter(criterio,cadena);
		}
		ventanaEstructura.show();
	}
}

function irGuardar(){
	var cadjson = '';
	if (Ext.getCmp('codpai').getValue()=='---seleccione---')
	{
		Ext.getCmp('codpai').setValue('---');	
	}
	if (Ext.getCmp('codest').getValue()=='---seleccione---')
	{
		Ext.getCmp('codest').setValue('---');	
	}
	if (Ext.getCmp('codmun').getValue()=='---seleccione---')
	{
		Ext.getCmp('codmun').setValue('---');	
	}
	if (Ext.getCmp('codest').getValue()=='---seleccione---')
	{
		Ext.getCmp('codest').setValue('---');	
	}
	if (Ext.getCmp('codpar').getValue()=='---seleccione---')
	{
		Ext.getCmp('codpar').setValue('---');	
	}
	if ((Ext.getCmp('codban').getValue()=='') || (Ext.getCmp('codban').getValue()=='   '))
	{
		Ext.getCmp('codban').setValue('---');	
	}
	if(Actualizar == null)
	{
		cadjson = getItems(plproveedor,'incluir','N',null,null);
	} 
    else
	{
    	cadjson = getItems(plproveedor,'actualizar','N',null,null);
    }
	try {
		var objjson = Ext.util.JSON.decode(cadjson);
		if (typeof(objjson) == 'object') {
			var parametros = 'ObjSon=' + cadjson;
			Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function ( result, request){
				var datos = result.responseText;
				var datajson = eval('(' + datos + ')');
				if(datajson.raiz.valido==true) {
					Ext.Msg.show({
						title:'Mensaje',
						msg: exitoguardar+". "+datajson.raiz.mensaje,
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
					limpiarFormulario(plproveedor);
//					irNuevo();
					Actualizar=null;
				}
				else {
					Ext.Msg.show({
						title:'Mensaje',
						msg: errorguardar,
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					 });
				}
			}	
			});
		}
	}	
	catch(e){
			alert('Verifique los datos, esta insertando caracteres invalidos '+e);
	}
}

function irCancelar(){
	limpiarFormulario(plproveedor);
}

function irEliminar(){
	
	function respuesta(btn){
		if(btn=='yes'){
			var cadjson = getItems(plproveedor,'eliminar','N',null,null);
			try {
				var objjson = Ext.util.JSON.decode(cadjson);
				if (typeof(objjson) == 'object') {
					var parametros = 'ObjSon=' + cadjson;
						Ext.Ajax.request({
							url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
							params : parametros,
							method: 'POST',
							success: function ( result, request){
								var codigo = result.responseText;
								if(codigo != ""){
									if(String(codigo) == '1'){
										Ext.Msg.show({
											title:'Mensaje',
											msg: 'Registro eliminado con &#233;xito',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.INFO
										});
										limpiarFormulario(plproveedor);
										Actualizar=null;
									}
									else if(String(codigo) == '2'){
										Ext.Msg.show({
											title:'Mensaje',
											msg: 'Proveedor asociado a otros registros, no puede ser Eliminado <br>',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.ERROR
										});
									}
									else{
										Ext.Msg.show({
											title:'Mensaje',
											msg: 'Error al tratar de eliminar el registro <br>',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.ERROR
										});
									}
								}
							}	
						});
				}
			}
			catch(e){
				alert('error'+e);
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

function irNuevoSocio(){
	Ext.getCmp('cedsocio').setValue('');
	Ext.getCmp('nomsocio').setValue('');
	Ext.getCmp('apesocio').setValue('');
	Ext.getCmp('dirsocio').setValue('');
	Ext.getCmp('carsocio').setValue('');
	Ext.getCmp('telsocio').setValue('');
	Ext.getCmp('emailsoc').setValue('');
	Actualizar_socio=false;
	Ext.getCmp('cedsocio').enable();
	Ext.getCmp('nomsocio').enable();
	Ext.getCmp('apesocio').enable();
}

function irGuardarSocio(){
	
	var valido = true;
	if(Ext.getCmp('nompro').getValue()==''){
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe cargar un Proveedor para procesar la informaci�n',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else{
		if(Ext.getCmp('cedsocio').getValue()==''){
			alert('Debe llenar el campo C�dula!!!');
			valido = false;
		}
		else if(Ext.getCmp('nomsocio').getValue()==''){
			alert('Debe llenar el campo Nombre!!!');
			valido = false;
		}
		else if(Ext.getCmp('apesocio').getValue()==''){
			alert('Debe llenar el campo Apellido!!!');
			valido = false;
		}
		else if(Ext.getCmp('dirsocio').getValue()==''){
			alert('Debe llenar el campo Direcci�n!!!');
			valido = false;
		}
		else if(Ext.getCmp('carsocio').getValue()==''){
			alert('Debe llenar el campo Cargo!!!');
			valido = false;
		}
		else if(Ext.getCmp('telsocio').getValue()==''){
			alert('Debe llenar el campo Tel�fono!!!');
			valido = false;
		}
		else if(Ext.getCmp('emailsoc').getValue()==''){
			alert('Debe llenar el campo Email!!!');
			valido = false;
		}
		if(valido){
			if(Actualizar_socio==false){
				var arregloJson = "{'operacion':'incluir_socio','cedsocio':'"+Ext.getCmp('cedsocio').getValue()+"',"+
								  "'nomsocio':'"+Ext.getCmp('nomsocio').getValue()+"','apesocio':'"+Ext.getCmp('apesocio').getValue()+"',"+
								  "'dirsocio':'"+Ext.getCmp('dirsocio').getValue()+"','carsocio':'"+Ext.getCmp('carsocio').getValue()+"',"+
								  "'telsocio':'"+Ext.getCmp('telsocio').getValue()+"','email':'"+Ext.getCmp('emailsoc').getValue()+"',"+
								  "'cod_pro':'"+Ext.getCmp('cod_pro').getValue()+"'}";
			}
			else{
				var arregloJson = "{'operacion':'actualizar_socio','cedsocio':'"+Ext.getCmp('cedsocio').getValue()+"',"+
								  "'nomsocio':'"+Ext.getCmp('nomsocio').getValue()+"','apesocio':'"+Ext.getCmp('apesocio').getValue()+"',"+
								  "'dirsocio':'"+Ext.getCmp('dirsocio').getValue()+"','carsocio':'"+Ext.getCmp('carsocio').getValue()+"',"+
								  "'telsocio':'"+Ext.getCmp('telsocio').getValue()+"','email':'"+Ext.getCmp('emailsoc').getValue()+"',"+
								  "'cod_pro':'"+Ext.getCmp('cod_pro').getValue()+"'}";
			}
			socio= eval('(' + arregloJson + ')');
			ObjSon=Ext.util.JSON.encode(socio);
			parametros = 'ObjSon='+ObjSon;
			Ext.Ajax.request({
				url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
				params : parametros,
				method: 'POST',
				success: function ( resultado, request ){ 
					datos = resultado.responseText;
					switch(datos){
						case '0':
			        			Ext.MessageBox.show({
					    			title:'Error',
									msg: 'Ha ocurrido un error procesando la informaci&#243;n ',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
					    		});
							    break;
						case '1': 
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro incluido con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
								irNuevoSocio();
							    break;
							    
						case '2': 
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro Actualizado con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
							    irNuevoSocio();
								Ext.getCmp('cedsocio').enable();
								Ext.getCmp('nomsocio').enable();
								Ext.getCmp('apesocio').enable();
								break;
					
						}
				},
				failure: function ( result, request){ 
				  		Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
				} 
			});
		}
	}
}

function irBuscarSocio()
{
  	var reVentana = Ext.data.Record.create([
        	    {name: 'cedsocio'}, 
        	    {name: 'nomsocio'},
				{name: 'apesocio'},
				{name: 'carsocio'},
				{name: 'telsocio'},
				{name: 'dirsocio'},
				{name: 'email'}
    ]);
                	
    var dsVentana =  new Ext.data.Store({
    	reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reVentana)
    });
        						
    var cmVentana = new Ext.grid.ColumnModel([
            {header: "C&#233;dula", width: 40, sortable: true, dataIndex: 'cedsocio'},
            {header: "Nombre del Socio", width: 100, sortable: true, dataIndex: 'nomsocio'}          
    ]);
        	
    //creando datastore y columnmodel para la grid de cambio de estatus de proveedor
    gridVentanaProveedor = new Ext.grid.GridPanel({
    	width:550,
    	height:500,
    	frame:true,
    	title:"",
    	style: 'position:absolute;left:10px;top:5px',
    	autoScroll:true,
    	border:true,
    	ds: dsVentana,
    	cm: cmVentana,
    	stripeRows: true,
    	viewConfig: {forceFit:true}
    });
    	 
    gridVentanaProveedor.on({
    	'rowdblclick': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
				Ext.getCmp('cedsocio').setValue(registro.get('cedsocio'));
				Ext.getCmp('nomsocio').setValue(registro.get('nomsocio'));
				Ext.getCmp('apesocio').setValue(registro.get('apesocio'));
				Ext.getCmp('carsocio').setValue(registro.get('carsocio'));
				Ext.getCmp('telsocio').setValue(registro.get('telsocio'));
				Ext.getCmp('dirsocio').setValue(registro.get('dirsocio'));
				Ext.getCmp('emailsoc').setValue(registro.get('email'));
				Ext.getCmp('cedsocio').disable();
				Ext.getCmp('nomsocio').disable();
				Ext.getCmp('apesocio').disable();
				Actualizar_socio=true;
				gridVentanaProveedor.destroy();
				ventanaEstructura.destroy();
			}
		}
    });
		
    var formVentanaCatalogo= new Ext.FormPanel({
    	width: 590,
    	height: 380,
    	title: '',
    	style: 'position:absolute;left:5px;top:10px',
    	frame: true,
    	autoScroll:false,
    	items: [gridVentanaProveedor] 		
 	});
    
	var ventanaEstructura = new Ext.Window({
    	width:600,
        height:450,
        border:false,
        modal: true,
        closable:false,
        frame:true,
        title:"<H1 align='center'>Cat&#225;logo de Socios por Proveedores</H1>",
        items:[formVentanaCatalogo],
        buttons:[{
			text:'Aceptar',  
	        handler: function(){
        		var registro2 = gridVentanaProveedor.getSelectionModel().getSelected();	        	
        		if(registro2!= undefined){
        			Ext.getCmp('cedsocio').setValue(registro2.get('cedsocio'));
        			Ext.getCmp('nomsocio').setValue(registro2.get('nomsocio'));
        			Ext.getCmp('apesocio').setValue(registro2.get('apesocio'));
        			Ext.getCmp('carsocio').setValue(registro2.get('carsocio'));
        			Ext.getCmp('telsocio').setValue(registro2.get('telsocio'));
        			Ext.getCmp('dirsocio').setValue(registro2.get('dirsocio'));
        			Ext.getCmp('emailsoc').setValue(registro2.get('email'));
        			Ext.getCmp('cedsocio').disable();
        			Ext.getCmp('nomsocio').disable();
        			Ext.getCmp('apesocio').disable();
        			Actualizar_socio=true;
        			gridVentanaProveedor.destroy();
        			ventanaEstructura.destroy();	        		
        		}
        		else{
        			Ext.MessageBox.show({
        				title:'Mensaje',
        				msg:'Debe seleccionar al menos un registro a procesar',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
        			});
        		}
        	}
        },
        {
    	text: 'Salir',
    	handler: function(){
    		irNuevoSocio();
    		Ext.getCmp('cedsocio').enable();
    		Ext.getCmp('nomsocio').enable();
    		Ext.getCmp('apesocio').enable();
    		ventanaEstructura.destroy();
    	}
        }] 	
    });
	
		
	var proveedor=Ext.getCmp('cod_pro').getValue();
	obtenerMensaje('procesar','','Buscando Datos');
	var JSONObject = {
		  'operacion' : 'buscarSocios','cod_pro' : proveedor
	}			
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoProveedores = eval('(' + datos + ')');
			if(objetoProveedores!=''){
				if(objetoProveedores!='0'){
					if(objetoProveedores.raiz == null || objetoProveedores.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
						});
					}
					else{
						ventanaEstructura.show();
						gridVentanaProveedor.store.loadData(objetoProveedores	);
					}
				}
				else{
					Ext.MessageBox.show({
						title:'Advertencia',
						msg:'Debe configurar los datos del Proveedor',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.WARNING
					});
				}
			}
		}	
	});		   
}

function irEliminarSocio()
{
	function respuesta2(btn){
	if(btn=='yes'){
		var arregloJson = "{'operacion':'eliminar_socio','cedsocio':'"+Ext.getCmp('cedsocio').getValue()+"',"+
						  "'cod_pro':'"+Ext.getCmp('cod_pro').getValue()+"'}";
		socio= eval('(' + arregloJson + ')');
		ObjSon=Ext.util.JSON.encode(socio);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function ( resultado, request ){ 
				datos = resultado.responseText;
				switch(datos)
				{
					case '0': 
							Ext.Msg.show({
											title:'Mensaje',
											msg: 'Ha ocurrido un error, vuelva a intentar',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.ERROR
										});
							break;
					case '1': 
							Ext.Msg.show({
											title:'Mensaje',
											msg: 'Registro eliminado con &#233;xito',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.INFO
							});
							irNuevoSocio();
							break;
							
					case '2': 
							Ext.Msg.show({
											title:'Mensaje',
											msg: 'Registro eliminado con &#233;xito',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.INFO
							});
							break;
				
				}
			},
			failure: function ( result, request)
			{ 
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			} 
		});
	 }
	}
	if(Actualizar_socio){
		  Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', respuesta2);
	}
	else{
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe registrar un socio para eliminarlo, verifique por favor',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		}); 
	}
	
}

function irNuevoDocumento(){
	Ext.getCmp('coddoc').setValue('');
	Ext.getCmp('dendoc').setValue('');
	Ext.getCmp('fecrecdoc').setValue('');
	Ext.getCmp('fecvendoc').setValue('');
	Ext.getCmp('estdoc').setValue('');
	Ext.getCmp('estorig').setValue('');
	Actualizar_documento=false;
	Ext.getCmp('coddoc').enable();
}

function irGuardarDocumento(){

	var valido = true;
	fecha_rec = new String(Ext.get('fecrecdoc').getValue());
	fecha_rec =fecha_rec.substring(6,10)+'-'+fecha_rec.substring(3,5)+'-'+fecha_rec.substring(0,2);
	fecha_ven = new String(Ext.get('fecvendoc').getValue());
	fecha_ven =fecha_ven.substring(6,10)+'-'+fecha_ven.substring(3,5)+'-'+fecha_ven.substring(0,2);
	
	if(Ext.getCmp('nompro').getValue()==''){
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe cargar un Proveedor para procesar la informaci�n',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else{
		if(Ext.getCmp('coddoc').getValue()==''){
			alert('Debe llenar el campo c�digo de documento!!!');
			valido = false;
		}
		else if(Ext.getCmp('fecrecdoc').getValue()==''){
			alert('Debe llenar el campo fecha de recepci�n!!!');
			valido = false;
		}
		else if(Ext.getCmp('fecvendoc').getValue()==''){
			alert('Debe llenar el campo fecha de vencimiento!!!');
			valido = false;
		}
		else if(Ext.getCmp('estdoc').getValue()==''){
			alert('Debe llenar el campo estatus de documento!!!');
			valido = false;
		}
		else if(Ext.getCmp('estorig').getValue()==''){
			alert('Debe llenar el campo estatus de originalidad!!!');
			valido = false;
		}

		if(valido){
			if (Actualizar_documento==false){
				var arregloJson = "{'operacion':'incluir_documento_pro','coddoc':'"+Ext.getCmp('coddoc').getValue()+"',"+
								  "'dendoc':'"+Ext.getCmp('dendoc').getValue()+"','fecrecdoc':'"+fecha_rec+"',"+
								  "'fecvendoc':'"+fecha_ven+"','estdoc':'"+Ext.getCmp('estdoc').getValue()+"',"+
								  "'estorig':'"+Ext.getCmp('estorig').getValue()+"',"+
								  "'cod_pro':'"+Ext.getCmp('cod_pro').getValue()+"'}";
			}
			else{
				var arregloJson = "{'operacion':'actualizar_documento_pro','coddoc':'"+Ext.getCmp('coddoc').getValue()+"',"+
								  "'dendoc':'"+Ext.getCmp('dendoc').getValue()+"','fecrecdoc':'"+fecha_rec+"',"+
								  "'fecvendoc':'"+fecha_ven+"','estdoc':'"+Ext.getCmp('estdoc').getValue()+"',"+
								  "'estorig':'"+Ext.getCmp('estorig').getValue()+"',"+
								  "'cod_pro':'"+Ext.getCmp('cod_pro').getValue()+"'}";
			}
			documento= eval('(' + arregloJson + ')');
			ObjSon=Ext.util.JSON.encode(documento);
			parametros = 'ObjSon='+ObjSon;
			Ext.Ajax.request({
				url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
				params : parametros,
				method: 'POST',
				success: function ( resultado, request ){ 
					datos = resultado.responseText;
					switch(datos){
						case '0':
			        			Ext.MessageBox.show({
					    			title:'Error',
									msg: 'Ha ocurrido un error procesando la informaci&#243;n ',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
					    		});
						case '1': 
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro incluido con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
								irNuevoDocumento();
							    break;
							    
						case '2': 
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro Actualizado con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
							    irNuevoDocumento();
								Ext.getCmp('coddoc').enable();
								break;
					
					}
			  	},
			  	failure: function ( result, request)
			  	{ 
			  		Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			  	} 
			});
		}
	}
}

function irBuscarDocumento()
{
	function mostrarEstatusDoc(valor)
	{
		if (valor=="0")
		{
			return 'No Entregado';
		}
		else if(valor=="1")
		{
			return 'Entregado';	
		}
		else if(valor=="2")
		{
			return 'En Tramite';	
		}
		else
		{
			return 'No Aplica al Proveedor';	
		}
	}

	function mostrarEstatusDocOri(valor)
	{
		if (valor=="0")
		{
			return 'Copia del Documento';
		}
		else
		{
			return 'Original';	
		}
	}

	var reProvDoc = Ext.data.Record.create([
        	    {name: 'coddoc'}, 
        	    {name: 'dendoc'},
				{name: 'fecrecdoc'},
				{name: 'fecvendoc'},
				{name: 'estdoc'},
				{name: 'estorig'}
   ]);
                	
   var dsProvDoc =  new Ext.data.Store({
	   reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reProvDoc)
   });
        						
   var cmProvDoc = new Ext.grid.ColumnModel([
            {header: "C&#243;digo", width: 40, sortable: true, dataIndex: 'coddoc'},
            {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'dendoc'},
			{header: "Fecha Recepci&#243;n", width: 60, sortable: true, dataIndex: 'fecrecdoc'},
			{header: "Fecha Vencimiento", width: 60, sortable: true, dataIndex: 'fecvendoc'},
			{header: "Estatus Documento", width: 60, sortable: true, dataIndex: 'estdoc',renderer:mostrarEstatusDoc},
			{header: "Estatus Original", width: 60, sortable: true, dataIndex: 'estorig',renderer:mostrarEstatusDocOri}
   ]);
        	
   //creando datastore y columnmodel para la grid de cambio de estatus de proveedor
   gridVentanaProveedorDoc = new Ext.grid.GridPanel({
	   width:700,
	   height:500,
	   frame:true,
	   title:"",
	   style: 'position:absolute;left:10px;top:5px',
	   autoScroll:true,
	   border:true,
	   ds: dsProvDoc,
	   cm: cmProvDoc,
	   stripeRows: true,
	   viewConfig: {forceFit:true}
   });
    	 
   gridVentanaProveedorDoc.on({
	   'rowdblclick': {
	   		fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
				Ext.getCmp('coddoc').setValue(registro.get('coddoc'));
				Ext.getCmp('dendoc').setValue(registro.get('dendoc'));
				Ext.getCmp('fecrecdoc').setValue(registro.get('fecrecdoc'));
				Ext.getCmp('fecvendoc').setValue(registro.get('fecvendoc'));
				Ext.getCmp('estdoc').setValue(registro.get('estdoc'));
				Ext.getCmp('estorig').setValue(registro.get('estorig'));
				Ext.getCmp('coddoc').disable();
				Actualizar_documento=true;
				gridVentanaProveedorDoc.destroy();
				ventanaDocumento.destroy();
			}
		}
   });
		
   var formVentanaDocCatalogo= new Ext.FormPanel({
	   width: 740,
	   height: 380,
	   title: '',
	   style: 'position:absolute;left:5px;top:10px',
	   frame: true,
	   autoScroll:true,
	   items: [gridVentanaProveedorDoc] 		
   });
    
   var ventanaDocumento = new Ext.Window({
    	width:750,
        height:450,
        border:false,
        modal: true,
        closable:false,
        frame:true,
		autoScroll:true,
        title:"<H1 align='center'>Cat&#225;logo de Documentos por Proveedor</H1>",
        items:[formVentanaDocCatalogo],
        buttons:[{
				text:'Aceptar',  
		        handler: function(){
	        		var registro2 = gridVentanaProveedorDoc.getSelectionModel().getSelected();	        	
	        		if(registro2!= undefined){
	        			Ext.getCmp('coddoc').setValue(registro2.get('coddoc'));
	        			Ext.getCmp('dendoc').setValue(registro2.get('dendoc'));
	        			Ext.getCmp('fecrecdoc').setValue(registro2.get('fecrecdoc'));
	        			Ext.getCmp('fecvendoc').setValue(registro2.get('fecvendoc'));
	        			Ext.getCmp('estdoc').setValue(registro2.get('estdoc'));
	        			Ext.getCmp('estorig').setValue(registro2.get('estorig'));
	        			Ext.getCmp('coddoc').disable();
	        			Actualizar_documento=true;
	        			gridVentanaProveedorDoc.destroy();
	        			ventanaDocumento.destroy();	        		
	        		}
	        		else{
	        			Ext.MessageBox.show({
	        				title:'Mensaje',
	        				msg:'Debe seleccionar al menos un registro a procesar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
	        			});
	        		}
	        	}
	        },
	        {
        	text: 'Salir',
        	handler: function(){
        		irNuevoDocumento();
        		Ext.getCmp('coddoc').enable();
        		ventanaDocumento.destroy();
        		}
	        }] 	
   });
   		
   var proveedor=Ext.getCmp('cod_pro').getValue();
   var documento=Ext.getCmp('coddoc').getValue();
   obtenerMensaje('procesar','','Buscando Datos');
   var JSONObject = {
		   'operacion' : 'buscarDocumentos','cod_pro' : proveedor
   }			
   var ObjSon = JSON.stringify(JSONObject);
   var parametros = 'ObjSon='+ObjSon; 
   Ext.Ajax.request({
	   url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
	   params : parametros,
	   method: 'POST',
	   success: function ( resultado, request){
	   		Ext.Msg.hide();
	   		var datos = resultado.responseText;
	   		var objetoProveedoresDoc = eval('(' + datos + ')');
	   		if(objetoProveedoresDoc!=''){
	   			if(objetoProveedoresDoc!='0'){
	   				if(objetoProveedoresDoc.raiz == null || objetoProveedoresDoc.raiz ==''){
	   					Ext.MessageBox.show({
	   						title:'Advertencia',
	   						msg:'No existen datos para mostrar',
		 					buttons: Ext.Msg.OK,
		 					icon: Ext.MessageBox.WARNING
		 				});
					}
					else{
						ventanaDocumento.show();
						gridVentanaProveedorDoc.store.loadData(objetoProveedoresDoc);
					}
	   			}
	   			else{
	   				Ext.MessageBox.show({
	   					title:'Advertencia',
		 				msg:'Debe configurar los datos del proveedor',
		 				buttons: Ext.Msg.OK,
		 				icon: Ext.MessageBox.WARNING
		 			});
	   			}
	   		}
   		}	
   });		   
}

function irEliminarDocumento()
{
	function respuesta3(btn){
	if(btn=='yes'){
		var arregloJson = "{'operacion':'eliminar_documento','coddoc':'"+Ext.getCmp('coddoc').getValue()+"',"+
						  "'cod_pro':'"+Ext.getCmp('cod_pro').getValue()+"'}";
		documento= eval('(' + arregloJson + ')');
		ObjSon=Ext.util.JSON.encode(documento);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function ( resultado, request ){ 
				datos = resultado.responseText;
				switch(datos)
				{
					case '0': 
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'Ha ocurrido un error, vuelva a intentar',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.ERROR
							});
							break;
					case '1': 
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'Registro eliminado con &#233;xito',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
							irNuevoDocumento();
							break;
							
					case '2': 
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'Registro eliminado con &#233;xito',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
							break;
					
				}
			},
			failure: function ( result, request)
			{ 
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			} 
		});
	 }
	}
	if(Actualizar_documento){
		  Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', respuesta3);
	}
	else{
		Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe registrar un documento para eliminarlo, verifique por favor',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
		}); 
	}
	
}

function irNuevoCalif(){
	Ext.getCmp('codclas').setValue('');
	Ext.getCmp('denclas').setValue('');
	Ext.getCmp('status').setValue('');
	Ext.getCmp('nivstatus').setValue('');
	Ext.getCmp('codniv').setValue('');
	Ext.getCmp('desniv').setValue('');
	Ext.getCmp('monmincon').setValue('');
	Ext.getCmp('monmaxcon').setValue('');
	Ext.getCmp('monfincon').setValue('');
	Actualizar_calif=false;
	Ext.getCmp('codclas').enable();
}

function irGuardarCalif(){

	var valido = true;
	if(Ext.getCmp('nompro').getValue()==''){
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe cargar un Proveedor para procesar la informaci�n',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else{
		if(Ext.getCmp('codclas').getValue()==''){
			alert('Debe llenar el campo c�digo de calificaci�n');
			valido = false;
		}
		else if(Ext.getCmp('status').getValue()==''){
			alert('Debe llenar el campo estatus (Activa-NoActiva)');
			valido = false;
		}
		else if(Ext.getCmp('codniv').getValue()==''){
			alert('Debe llenar el campo nivel de clasificaci�n');
			valido = false;
		}
		else if(Ext.getCmp('nivstatus').getValue()==''){
			alert('Debe llenar el campo estatus del nivel');
			valido = false;
		}
		else if(Ext.getCmp('monmincon').getValue()==''){
			alert('Debe llenar el campo monto m�nimo de contrataci�n');
			valido = false;
		}
		else if(Ext.getCmp('monmaxcon').getValue()==''){
			alert('Debe llenar el campo monto m�ximo de contrataci�n');
			valido = false;
		}
		else if(Ext.getCmp('monfincon').getValue()==''){
			alert('Debe llenar el campo nivel financiero estimado de contrataci�n');
			valido = false;
		}
		monto=ue_formato_calculo(Ext.getCmp('monfincon').getValue());
		if(valido){
			if (Actualizar_calif==false){
				var arregloJson = "{'operacion':'incluir_calif_pro','codclas':'"+Ext.getCmp('codclas').getValue()+"',"+
								  "'codniv':'"+Ext.getCmp('codniv').getValue()+"','status':'"+Ext.getCmp('status').getValue()+"',"+
								  "'nivstatus':'"+Ext.getCmp('nivstatus').getValue()+"','monfincon':'"+monto+"',"+
								  "'cod_pro':'"+Ext.getCmp('cod_pro').getValue()+"'}";
			}
			else{
				var arregloJson = "{'operacion':'actualizar_calif_pro','codclas':'"+Ext.getCmp('codclas').getValue()+"',"+
								  "'codniv':'"+Ext.getCmp('codniv').getValue()+"','status':'"+Ext.getCmp('status').getValue()+"',"+
								  "'nivstatus':'"+Ext.getCmp('nivstatus').getValue()+"','monfincon':'"+monto+"',"+
								  "'cod_pro':'"+Ext.getCmp('cod_pro').getValue()+"'}";
			}
			calif= eval('(' + arregloJson + ')');
			ObjSon=Ext.util.JSON.encode(calif);
			parametros = 'ObjSon='+ObjSon;
			
			Ext.Ajax.request({
				url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
				params : parametros,
				method: 'POST',
				success: function ( resultado, request ){ 
					datos = resultado.responseText;
					switch(datos){
						case '0':
			        			Ext.MessageBox.show({
					    			title:'Error',
									msg: 'Ha ocurrido un error procesando la informaci&#243;n',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
					    		});
							    break;
						case '1': 
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro incluido con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
								irNuevoCalif();
							    break;
							    
						case '2': 
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro Actualizado con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
							    irNuevoCalif();
								Ext.getCmp('codclas').enable();
								break;
				
					}
			  	},
			  	failure: function ( result, request)
			  	{ 
			  		Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			  	} 
			});
		}	
	}
}

function irBuscarCalif()
{
	function mostrarEstatusCla(valor)
	{
		if (valor=="0")
		{
			return 'Activa';
		}
		else
		{
			return 'No Activa';	
		}
	}

	function mostrarNivelEstatusCla(valor)
	{
		if (valor=="0")
		{
			return 'Ninguno';
		}
		else if (valor=="1")
		{
			return 'Excelente';	
		}
		else if (valor=="2")
		{
			return 'Bueno';	
		}
		else
		{
			return 'Regular';	
		}
	}

	var reProvCla = Ext.data.Record.create([
        	    {name: 'codclas'}, 
        	    {name: 'denclas'},
				{name: 'status'},
				{name: 'nivstatus'},
				{name: 'codniv'},
				{name: 'desniv'},
				{name: 'monmincon'},
				{name: 'monmaxcon'},
				{name: 'monfincon'}
    ]);
                	
    var dsProvCla =  new Ext.data.Store({
    		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reProvCla)
    });
        						
	var cmProvCla = new Ext.grid.ColumnModel([
            {header: "C&#243;digo", width: 40, sortable: true, dataIndex: 'codclas'},
            {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denclas'},
			{header: "Estatus", width: 40, sortable: true, dataIndex: 'status',renderer:mostrarEstatusCla},
			{header: "Nivel Estatus", width: 50, sortable: true, dataIndex: 'nivstatus',renderer:mostrarNivelEstatusCla},
			{header: "Nivel de Calificaci&#243;n", width: 70, sortable: true, dataIndex: 'desniv'},
			{header: "Monto M&#237;n de Contrataci&#243;n", width: 90, sortable: true, dataIndex: 'monmincon'},
			{header: "Monto M&#225;x de Contrataci&#243;n", width: 90, sortable: true, dataIndex: 'monmaxcon'}
    ]);
        	
	//creando datastore y columnmodel para la grid de cambio de estatus de proveedor
	gridVentanaProveedorCla = new Ext.grid.GridPanel({
		width:800,
		height:350,
		frame:true,
		title:"",
		style: 'position:absolute;left:10px;top:5px',
		autoScroll:true,
		border:true,
		ds: dsProvCla,
		cm: cmProvCla,
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
    	 
	gridVentanaProveedorCla.on({
		'rowdblclick': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
				Ext.getCmp('codclas').setValue(registro.get('codclas'));
				Ext.getCmp('denclas').setValue(registro.get('denclas'));
				Ext.getCmp('status').setValue(registro.get('status'));
				Ext.getCmp('nivstatus').setValue(registro.get('nivstatus'));
				Ext.getCmp('codniv').setValue(registro.get('codniv'));
				Ext.getCmp('desniv').setValue(registro.get('desniv'));
				Ext.getCmp('monmincon').setValue(registro.get('monmincon'));
				Ext.getCmp('monmaxcon').setValue(registro.get('monmaxcon'));
				Ext.getCmp('monfincon').setValue(registro.get('monfincon'));
				Ext.getCmp('codclas').disable();
				Actualizar_calif=true;
				gridVentanaProveedorCla.destroy();
				ventanaClasificacion.destroy();
			}
		}
	});
		
	var formVentanaClaCatalogo= new Ext.FormPanel({
		width: 840,
		height: 380,
		title: '',
		style: 'position:absolute;left:5px;top:10px',
		frame: true,
		autoScroll:false,
		items: [gridVentanaProveedorCla] 		
	});
    
	var ventanaClasificacion = new Ext.Window({
    	width:850,
        height:450,
        border:false,
        modal: true,
        closable:false,
        frame:true,
        title:"<H1 align='center'>Cat&#225;logo de Calificaci&#243;n por Proveedor</H1>",
        items:[formVentanaClaCatalogo],
        buttons:[{
			text:'Aceptar',  
	        handler: function(){
        		var registro2 = gridVentanaProveedorCla.getSelectionModel().getSelected();	        	
        		if(registro2!= undefined){
        			Ext.getCmp('codclas').setValue(registro2.get('codclas'));
        			Ext.getCmp('denclas').setValue(registro2.get('denclas'));
        			Ext.getCmp('status').setValue(registro2.get('status'));
        			Ext.getCmp('nivstatus').setValue(registro2.get('nivstatus'));
        			Ext.getCmp('codniv').setValue(registro2.get('codniv'));
        			Ext.getCmp('desniv').setValue(registro2.get('desniv'));
        			Ext.getCmp('monmincon').setValue(registro2.get('monmincon'));
        			Ext.getCmp('monmaxcon').setValue(registro2.get('monmaxcon'));
        			Ext.getCmp('monfincon').setValue(registro2.get('monfincon'));
        			Ext.getCmp('codclas').disable();
        			Actualizar_calif=true;
        			gridVentanaProveedorCla.destroy();
        			ventanaClasificacion.destroy();	        		
        		}
        		else{
        			Ext.MessageBox.show({
        				title:'Mensaje',
						msg:'Debe seleccionar al menos un registro a procesar',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
        			});
        		}
        	}
        },{
        	text: 'Salir',
        	handler: function(){
        		irNuevoCalif();
        		Ext.getCmp('codclas').enable();
        		ventanaClasificacion.destroy();
        	}
        }] 	
	});
	
	var proveedor=Ext.getCmp('cod_pro').getValue();
	obtenerMensaje('procesar','','Buscando Datos');
	var JSONObject = {
			'operacion' : 'buscarCalificacion','cod_pro' : proveedor
	}			
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoProveedoresCla = eval('(' + datos + ')');
			if(objetoProveedoresCla!=''){
				if(objetoProveedoresCla!='0'){
					if(objetoProveedoresCla.raiz == null || objetoProveedoresCla.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
		 					msg:'No existen datos para mostrar',
		 					buttons: Ext.Msg.OK,
		 					icon: Ext.MessageBox.WARNING
		 				});
					}
					else{
						ventanaClasificacion.show();
						gridVentanaProveedorCla.store.loadData(objetoProveedoresCla);
					}
				}
				else{
					Ext.MessageBox.show({
						title:'Advertencia',
						msg:'Debe configurar los datos del proveedor',
		 				buttons: Ext.Msg.OK,
		 				icon: Ext.MessageBox.WARNING
		 			});
				}
			}
		}	
	});		   
}

function irEliminarCalif()
{
	function respuesta4(btn){
	if(btn=='yes'){
		var arregloJson = "{'operacion':'eliminar_calificacion','codclas':'"+Ext.getCmp('codclas').getValue()+"',"+
						  "'cod_pro':'"+Ext.getCmp('cod_pro').getValue()+"'}";
		calif= eval('(' + arregloJson + ')');
		ObjSon=Ext.util.JSON.encode(calif);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function ( resultado, request ){ 
				datos = resultado.responseText;
				switch(datos)
				{
					case '0': 
							Ext.Msg.show({
											title:'Mensaje',
											msg: 'Ha ocurrido un error, vuelva a intentar',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.ERROR
										});
							break;
					case '1': 
							Ext.Msg.show({
											title:'Mensaje',
											msg: 'Registro eliminado con &#233;xito',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.INFO
							});
							irNuevoCalif();
							break;
							
					case '2': 
							Ext.Msg.show({
											title:'Mensaje',
											msg: 'Registro eliminado con &#233;xito',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.INFO
							});
							break;
					
				}
			},
			failure: function ( result, request)
			{ 
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			} 
		});
	 }
	}
	if(Actualizar_calif){
		  Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', respuesta4);
	}
	else{
		Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe registrar una calificaci&#243;n para eliminarlo, verifique por favor',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
		}); 
	}
	
}

function pasarDatosGridEspecialidad(grid,registro){
	var registroespexgrid = Ext.data.Record.create([
			{name: 'codesp'},     
			{name: 'denesp'}
		]);
		
	espxpro = new registroespexgrid
		({
			'codesp':'',
			'denesp':''
		});
	grid.store.insert(0,espxpro);
	espxpro.set('codesp',registro.get('codesp'));
	espxpro.set('denesp',registro.get('denesp'));
}

function pasarDatosGridDeduccion(grid,registro){
	var registrodeduxgrid = Ext.data.Record.create([
			{name: 'codded'},     
			{name: 'dended'}
		]);
		
	dedxpro = new registrodeduxgrid
		({
			'codded':'',
			'dended':''
		});
	grid.store.insert(0,dedxpro);
	dedxpro.set('codded',registro.get('codded'));
	dedxpro.set('dended',registro.get('dended'));
}

function eliminar_grid_esp() {
	arregloespxprov = gridespecialidad.getSelectionModel().getSelections();
	if (arregloespxprov.length >0)
	{
		for (var i = arregloespxprov.length - 1; i >= 0; i--)
		{
			gridespecialidad.getStore().remove(arregloespxprov[i]);
			if(!arregloespxprov[i].isModified('sig_cuenta'))
			{
				datastoreespxproveliminada.add(arregloespxprov[i]);
			}
		}
	}
}

function eliminar_grid_ded() {
	arreglodedxprov = griddeduccion.getSelectionModel().getSelections();
	if (arreglodedxprov.length >0)
	{
		for (var i = arreglodedxprov.length - 1; i >= 0; i--)
		{
			griddeduccion.getStore().remove(arreglodedxprov[i]);
			if(!arreglodedxprov[i].isModified('sig_cuenta'))
			{
				datastorededxproveliminada.add(arreglodedxprov[i]);
			}
		}
	}
}

function agregarEspecialidad()
{
	//crear_grid_catalogoplanunicorefiltro('catalogogastos');				   
    
	var reEspecialidad = Ext.data.Record.create
	([
    	{name: 'codesp'}, 
        {name: 'denesp'}
    ]);
                	
    var dsEspecialidad =  new Ext.data.Store({
    		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reEspecialidad)
      });
       						
    var chk = new Ext.grid.CheckboxSelectionModel({});
	
	var cmEspecialidad = new Ext.grid.ColumnModel([
			chk,									   
            {header: "C&#243;digo", width: 40, sortable: true, dataIndex: 'codesp'},
            {header: "Denominaci&#243;n ", width: 100, sortable: true, dataIndex: 'denesp'}          
    	]);
        	
    
	//creando datastore y columnmodel para la grid de cambio de estatus de proveedor
    gridVentanaEspecialidad = new Ext.grid.EditorGridPanel({
    	 		width:550,
    	 		height:325,
    			frame:true,
    			title:"",
    			style: 'position:absolute;left:10px;top:5px',
    			autoScroll:true,
         		border:true,
         		ds: dsEspecialidad,
           		cm: cmEspecialidad,
           		stripeRows: true,
				sm: new Ext.grid.CheckboxSelectionModel({}),
          		viewConfig: {forceFit:true}
    	});
		 
	ventanaEspecialidad = new Ext.Window({
		title: "<H1 align='center'>Cat&#225;logo de Especialidades disponibles</H1>",
		autoScroll:true,
	    width:575,
	    height:400,
	    modal: true,
	    closable:false,
	    plain: false,
	    items:[gridVentanaEspecialidad],
	    buttons: [{
					text:'Aceptar',  
			        handler: function()
					{
		        		arregloespecialidad = gridVentanaEspecialidad.getSelectionModel().getSelections();
		                for (i=0; i<arregloespecialidad.length; i++)
						{
							if (validarExistenciaRegistroGrid(arregloespecialidad[i],gridespecialidad,'codesp','codesp',true))
							{
								pasarDatosGridEspecialidad(gridespecialidad,arregloespecialidad[i]);
							}
						}
						gridVentanaEspecialidad.destroy();
						ventanaEspecialidad.destroy();
					}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			gridVentanaEspecialidad.destroy();
			      			ventanaEspecialidad.destroy();
			       		}
	              }]
      });
      ventanaEspecialidad.show();
	  
	  var proveedor=Ext.getCmp('cod_pro').getValue();
	  obtenerMensaje('procesar','','Buscando Datos');
	  var JSONObject = {
				'operacion' : 'buscarEspecialidadesDisp','cod_pro' : proveedor
				}			
	  var ObjSon = JSON.stringify(JSONObject);
	  var parametros = 'ObjSon='+ObjSon; 
	  Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoProveedores = eval('(' + datos + ')');
			if(objetoProveedores!='' || objetoProveedores.raiz!=''){
				gridVentanaEspecialidad.store.loadData(objetoProveedores);
			}
			else {
				Ext.Msg.show({
					title:'Advertencia',
					msg: 'No se han encontrado especialidades disponibles',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.WARNING
				});  				
			}
		}	
	});	
}

function agregarDeduccion()
{
	//crear_grid_catalogoplanunicorefiltro('catalogogastos');				   
    
	var reDeduccion = Ext.data.Record.create
	([
    	{name: 'codded'}, 
        {name: 'dended'}
    ]);
                	
    var dsDeduccion =  new Ext.data.Store({
    		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDeduccion)
      });
       						
    var chk = new Ext.grid.CheckboxSelectionModel({});
	
	var cmDeduccion = new Ext.grid.ColumnModel([
			chk,									   
            {header: "C&#243;digo", width: 40, sortable: true, dataIndex: 'codded'},
            {header: "Denominaci&#243;n ", width: 100, sortable: true, dataIndex: 'dended'}          
    	]);
        	
    
	//creando datastore y columnmodel para la grid de cambio de estatus de proveedor
    gridVentanaDeduccion = new Ext.grid.EditorGridPanel({
    	 		width:550,
    	 		height:325,
    			frame:true,
    			title:"",
    			style: 'position:absolute;left:10px;top:5px',
    			autoScroll:true,
         		border:true,
         		ds: dsDeduccion,
           		cm: cmDeduccion,
           		stripeRows: true,
				sm: new Ext.grid.CheckboxSelectionModel({}),
          		viewConfig: {forceFit:true}
    	});
		 
	ventanaDeduccion = new Ext.Window(
    {
    	title: "<H1 align='center'>Cat&#225;logo de Deducciones disponibles</H1>",
		autoScroll:true,
        width:575,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridVentanaDeduccion],
        buttons: [{
					text:'Aceptar',  
			        handler: function()
					{
		        		arreglodeduccion = gridVentanaDeduccion.getSelectionModel().getSelections();
		                for (i=0; i<arreglodeduccion.length; i++)
						{
							if (validarExistenciaRegistroGrid(arreglodeduccion[i],griddeduccion,'codded','codded',true))
							{
								pasarDatosGridDeduccion(griddeduccion,arreglodeduccion[i]);
							}
						}
						gridVentanaDeduccion.destroy();
						ventanaDeduccion.destroy();
					}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
		      		{
		      			gridVentanaDeduccion.destroy();
						ventanaDeduccion.destroy();
		       		}
                  }]
      });
      ventanaDeduccion.show();
	  
	  var proveedor=Ext.getCmp('cod_pro').getValue();
	  obtenerMensaje('procesar','','Buscando Datos');
	  var JSONObject = {
				'operacion' : 'buscarDeduccionesDisp','cod_pro' : proveedor
		}			
	  var ObjSon = JSON.stringify(JSONObject);
	  var parametros = 'ObjSon='+ObjSon; 
	  Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoProveedores = eval('(' + datos + ')');
			if(objetoProveedores!='' || objetoProveedores.raiz!=''){
				gridVentanaDeduccion.store.loadData(objetoProveedores);
			}
			else {
				Ext.Msg.show({
					title:'Advertencia',
					msg: 'No se han encontrado especialidades disponibles',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.WARNING
				});  				
			}
		}	
	});	
}

function irGuardarEspecialidad()
{
	valido=true;
	arrEspexprov = gridespecialidad.getStore();
	var first = true;
	
	if(Ext.getCmp('nompro').getValue()==''){
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe cargar un Proveedor para procesar la informaci�n',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else{
		var cadenaJson = "{'operacion':'guardar_espxprov', 'cod_pro':'"+Ext.getCmp('cod_pro').getValue()+"','arrEspIncluir':[";
		arrEspexprov.each(function (registroGrid){
			if(first){
				cadenaJson = cadenaJson + "{'codesp':'"+registroGrid.get('codesp')+"'," +
				" 'denesp':'"+registroGrid.get('denesp')+"'}";
				first = false;
			}
			else{
				cadenaJson = cadenaJson + ",{'codesp':'"+registroGrid.get('codesp')+"'," +
				" 'denesp':'"+registroGrid.get('denesp')+"'}";
			}
		});
		cadenaJson = cadenaJson + "]}";

		var parametros = 'ObjSon='+cadenaJson;
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function ( resultad, request ){ 
				var respuesta = resultad.responseText;
				respuesta = respuesta.split("|");
				var msjError = '';
				if(respuesta[0]=='1'){
					Ext.MessageBox.show({
						title:'Mensaje',
						msg: 'La informaci&#243;n fue procesada exitosamente'+msjError,
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
				else {
					Ext.MessageBox.show({
						title:'Error',
						msg: 'Ha ocurrido un error procesando la informaci&#243;n '+msjError,
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
				}
			},
			failure: function ( result, request){ 
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			} 
		});
	}
}

function irGuardarDeduccion()
{
	valido=true;
	arrDedxprov = griddeduccion.getStore();
	var first = true;
	
	if(Ext.getCmp('nompro').getValue()==''){
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe cargar un Proveedor para procesar la informaci�n',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else{
		var cadenaJson = "{'operacion':'guardar_dedxprov', 'cod_pro':'"+Ext.getCmp('cod_pro').getValue()+"','arrDedIncluir':[";
		
		arrDedxprov.each(function (registroGrid){
			if(first){
				cadenaJson = cadenaJson + "{'codded':'"+registroGrid.get('codded')+"'," +
				" 'dended':'"+registroGrid.get('dended')+"'}";
				first = false;
			}
			else{
				cadenaJson = cadenaJson + ",{'codded':'"+registroGrid.get('codded')+"'," +
				" 'dended':'"+registroGrid.get('dended')+"'}";
			}
		});
		cadenaJson = cadenaJson + "]}";
		
		var parametros = 'ObjSon='+cadenaJson;
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
			params : parametros,
			method: 'POST',
			success: function ( resultad, request ){ 
				var respuesta = resultad.responseText;
				respuesta = respuesta.split("|");
				var msjError = '';
				if(respuesta[0]=='1'){
					Ext.MessageBox.show({
						title:'Mensaje',
						msg: 'La informaci&#243;n fue procesada exitosamente'+msjError,
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
				else {
					Ext.MessageBox.show({
						title:'Error',
						msg: 'Ha ocurrido un error procesando la informaci&#243;n '+msjError,
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
				}
			},
			failure: function ( result, request){ 
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			} 
		});
	}
}


