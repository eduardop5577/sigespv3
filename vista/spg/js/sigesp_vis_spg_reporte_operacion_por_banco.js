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

var fromReporteOpexBan = null;
barraherramienta = true;
var fecha = new Date();

Ext.onReady(function() {
Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//----------------------------------------------------------------------------------------------------------------------------------

	//componente catalogo de cuentas presupuestarias
	var reCuentaPre = Ext.data.Record.create([
        {name: 'spg_cuenta'}, //campo obligatorio                             
        {name: 'denominacion'}, //campo obligatorio
        {name: 'status'}, //campo obligatorio
    ]);
	
	comcampocatcuentadesde = new com.sigesp.vista.comCatalogoCuentaSPG({
		idComponente:'spgdesde',
		anchofieldset: 900,
		reCatalogo: reCuentaPre,
		rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_comprobante.php',
		parametros: "ObjSon={'operacion': 'buscarCuentasPresupuestarias'",
		posicion:'position:absolute;left:20px;top:10px', 
		tittxt:'Desde',
		idtxt:'cuentadesde',
		campovalue:'spg_cuenta',
		anchoetiquetatext:60,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'denodesde',
		labelvalue:'',
		anchocoletiqueta:0.35, 
		anchoetiqueta:0,
		binding:'',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false, 
	});
	
	comcampocatcuentahasta = new com.sigesp.vista.comCatalogoCuentaSPG({
		idComponente:'spghasta',
		anchofieldset: 900,
		reCatalogo: reCuentaPre,
		rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_comprobante.php',
		parametros: "ObjSon={'operacion': 'buscarCuentasPresupuestarias'",
		posicion:'position:absolute;left:370px;top:10px', 
		tittxt:'Hasta',
		idtxt:'cuentahasta',
		campovalue:'spg_cuenta',
		anchoetiquetatext:60,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'denohasta',
		labelvalue:'',
		anchocoletiqueta:0.35, 
		anchoetiqueta:0,
		binding:'',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false, 
	});
	
	//----------------------------------------------------------------------------------------------------------------------------------
	
	//Creando el campo de banco 
	var banco = Ext.data.Record.create([
		{name: 'codban'},
		{name: 'nomban'},
		{name: 'codemp'},
		{name: 'dirban'},
		{name: 'telban'},
		{name: 'conban'},
		{name: 'movcon'},
		{name: 'esttesnac'},
		{name: 'codsudeban'}
	]);
	
	var dsbanco = new Ext.data.Store({
		reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},banco)
	});
						
	var cmbanco = new Ext.grid.ColumnModel([
        {header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'codban'},
        {header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'nomban'}
    ]);
	//fin creando datastore y columnmodel para el catalogo de bancos 
	
	//componente campocatalogo para el campo banco
	cmbbanco = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Cat&#225;logo de Bancos</H1>",
		id: 'catalogobanco',
		anchoformbus: 450,
		altoformbus:100,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		datosgridcat: dsbanco,
		colmodelocat: cmbanco,
		rutacontrolador:'../../controlador/scb/sigesp_ctr_scb_progpago.php',
		parametros: "ObjSon={'operacion': 'catalogo_banco'}",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codiban',valor:'codban'},
		           {etiqueta:'Nombre',id:'nombban',valor:'nomban'}],
        posicion:'position:absolute;left:20px;top:5px',
        idboton:'btnBanco',
        tittxt:'Banco',
        idtxt:'codban',
        campovalue:'codban',
        anchoetiquetatext:50,
        anchotext:100,
        anchocoltext:0.19,
        idlabel:'nomban',
        labelvalue:'',
        anchocoletiqueta:0.60,
        anchoetiqueta:0,
        anchofieldset:850,
        tipbus:'L',
        binding:'C',
        hiddenvalue:'',
        defaultvalue:'',
        allowblank:false
	});
	//fin componente para el campo banco
	
	//----------------------------------------------------------------------------------------------------------------------------------	
	
	//Creando el campo de cuentas de bancos 
	var ctabanco = Ext.data.Record.create([
		{name: 'ctaban'},
		{name: 'dencta'},
		{name: 'sc_cuenta'},
		{name: 'denominacion'},
		{name: 'nomban'},
		{name: 'codtipcta'},
		{name: 'nomtipcta'},
		{name: 'fecapr'},
		{name: 'feccie'},
		{name: 'estact'},
		{name: 'status'},
	]);
	
	var dsctabanco = new Ext.data.Store({
		reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},ctabanco)
	});
						
	var cmctabanco = new Ext.grid.ColumnModel([
        {header: "<H1 align='center'>C&#243;digo</H1>", width: 70, sortable: true, dataIndex: 'ctaban'},
        {header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 45, sortable: true, dataIndex: 'dencta'},
        {header: "<H1 align='center'>Tipo</H1>", width: 25, sortable: true, dataIndex: 'nomtipcta' },
        {header: "<H1 align='center'>Contable</H1>", width: 35, sortable: true, dataIndex: 'sc_cuenta'},
        {header: "<H1 align='center'>Descripci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'denominacion'},
        {header: "<H1 align='center'>Apertura</H1>", width: 35, sortable: true, dataIndex: 'fecapr'}
	]);
	//fin creando datastore y columnmodel para el catalogo de cuentas de bancos 
	
	//componente campocatalogo para el campo cuentas de bancos
	cmbctabanco = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Cat&#225;logo de Cuentas de Bancos</H1>",
		id:'catalagocuenta',
		anchoformbus: 650,
		altoformbus:130,
		anchogrid: 650,
		altogrid: 400,
		anchoven: 700,
		altoven: 400,
		datosgridcat: dsctabanco,
		colmodelocat: cmctabanco,
		rutacontrolador:'../../controlador/scb/sigesp_ctr_scb_progpago.php',
		parametros: "ObjSon={'operacion': 'catalogo_ctabanco'",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'ctasban',valor:'ctaban'},
		           {etiqueta:'Nombre',id:'densban',valor:'dencta'}],
		posicion:'position:absolute;left:285px;top:5px',
		idboton:'btnCtaBanco',
		tittxt:'Cuenta',
		idtxt:'ctaban',
		campovalue:'ctaban',
		anchoetiquetatext:50,
		anchotext:200,
		anchocoltext:0.31,
		idlabel:'dencta',
		labelvalue:'',
		anchocoletiqueta:0.54,
		anchoetiqueta:0,
		anchofieldset:850,
		tipbus:'P',
		binding:'C',
		arrtxtfiltro:['codban'],
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:false,
		validarMostrar:1,
		fnValidarMostrar: validarCatalogoBanco,
		msjValidarMostrar: 'Debe seleccionar el Banco asociado a la Cuenta',
	});
	//fin componente para el campo cuentas de bancos 
	
	//---------------------------------------------------------------------------------------------------------------------------------
	
	//funcion para validar si dentro del catalogo de banco se ha seleccionado alguna entidad bancaria
	function validarCatalogoBanco(){
		var unidadOk = true;
		if(Ext.getCmp('codban').getValue()==''){
			unidadOk = false;
		}

		return unidadOk;
	}
	
	//-------------------------------------------------------------------------------------
	
	//Datos del tipo de impresion
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ]];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromCuentaPre = new Ext.form.FieldSet({
			title:'Intervalo de Cuentas',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 620,
			cls :'fondo',
			height: 68,
			items: [comcampocatcuentadesde.fieldsetCatalogo,comcampocatcuentahasta.fieldsetCatalogo]
	})
	
	//--------------------------------------------------------------------------------------------

	var	fromIntervaloFechas = new Ext.form.FieldSet({ 
			title:'Intervalo de Fechas',
			style: 'position:absolute;left:10px;top:90px',
			border:true,
			width: 620,
			cls :'fondo',
			height: 63,
			items:[{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:30px;top:10px',
					border:false,
					items:[{
							layout:"form",
							border:false,
							labelWidth:50,
							items:[{
									xtype:"datefield",
									labelSeparator :'',
									fieldLabel:"Desde",
									name:'Desde',
									id:'dtFechaDesde',
									allowBlank:true,
									width:100,
									binding:true,
									defaultvalue:'1900-01-01',
									hiddenvalue:'',
									allowBlank:false,
									value: '01/01/'+fecha.getFullYear(),
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:380px;top:10px',
					border:false,
					items:[{
							layout:"form",
							border:false,
							labelWidth:50,
							items:[{
									xtype:"datefield",
									labelSeparator :'',
									fieldLabel:"Hasta",
									name:'Hasta',
									id:'dtFechaHasta',
									allowBlank:true,
									width:100,
									binding:true,
									defaultvalue:'1900-01-01',
									hiddenvalue:'',
									allowBlank:false,
									value:  new Date().format('d-m-Y'),
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					}]
	})
	
	//--------------------------------------------------------------------------------------------

	var fromImpresion = new Ext.form.FieldSet({
			title:'Banco',
			style: 'position:absolute;left:10px;top:160px',
			border:true,
			width: 620,
			cls :'fondo',
			height: 78,
			items:[cmbbanco.fieldsetCatalogo,cmbctabanco.fieldsetCatalogo,]
	})
	
	//--------------------------------------------------------------------------------------------

	var fromOrden = new Ext.form.FieldSet({
			title:'Orden',
			style: 'position:absolute;left:10px;top:245px',
			border:true,
			width: 620,
			cls :'fondo',
			height: 80,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:30px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 120,
							items: [{
									xtype: 'checkbox',
									labelSeparator :'',
									fieldLabel: 'Fechas',
									id: 'ordfec',
									inputValue:1,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'0',
									allowBlank:true				
									},
									{
									xtype: 'checkbox',
									labelSeparator :'',
									fieldLabel: 'Procede',
									id: 'ordpro',
									inputValue:1,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'0',
									allowBlank:true		
									}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:380px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 120,
							items: [{
									xtype: 'checkbox',
									labelSeparator :'',
									fieldLabel: 'Documento',
									id: 'orddoc',
									inputValue:1,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'0',
									allowBlank:true				
									},
									{
									xtype: 'checkbox',
									labelSeparator :'',
									fieldLabel: 'Beneficiario',
									id: 'ordben',
									inputValue:1,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'0',
									allowBlank:true		
									}]
							}]
					}]
	})
	
	//------------------------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(300)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteOpexBan = new Ext.FormPanel({
		applyTo: 'formReporteOpexBan',
		width:650, 
		height: 400,
		title: "<H1 align='center'>OPERACI&#211;N POR BANCO</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromCuentaPre,
		        fromIntervaloFechas,
		        fromImpresion,
	            fromOrden
		        ]
	});
	fromReporteOpexBan.doLayout();
});

//------------------------------------------------------------------------------------------------------------

function irImprimir()
{
	var valido = true;
	var cuentadesde = Ext.getCmp('cuentadesde').getValue();
	var cuentahasta = Ext.getCmp('cuentahasta').getValue();
    var fecdes = Ext.getCmp('dtFechaDesde').getValue().format('Y-m-d');
    var fechas = Ext.getCmp('dtFechaHasta').getValue().format('Y-m-d');
    
    if(fecdes>fechas){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por Fecha no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(cuentadesde>cuentahasta){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El rango de Busqueda por Cuenta Presupuestaria no es correcto!!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((cuentadesde=='' && cuentahasta!='') || (cuentadesde!='' && cuentahasta=='')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Cuenta Presupuestaria!!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(valido){
		var ckbfec = 0;
		var ckbproc = 0;
		var ckbdoc = 0;
		var ckbbene = 0;
		var codban = Ext.getCmp('codban').getValue();
		var ctaban = Ext.getCmp('ctaban').getValue();
	    if(Ext.getCmp('ordfec').checked){
	    	ckbfec = 1;
	    }
	    if(Ext.getCmp('ordpro').checked){
	    	ckbproc = 1;
	    }
	    if(Ext.getCmp('ordben').checked){
	    	ckbbene = 1;
	    }
	    if(Ext.getCmp('orddoc').checked){
	    	ckbdoc = 1;
	    }
	    var pagina = "reportes/sigesp_spg_rpp_operacion_por_banco.php?txtcuentades="+cuentadesde+"&txtcuentahas="+cuentahasta
		            +"&txtfecdes="+fecdes+"&txtfechas="+fechas+"&txtcodban="+codban+"&txtcuenta="+ctaban
		            +"&ckbfec="+ckbfec+"&ckbproc="+ckbproc+"&ckbdoc="+ckbdoc+"&ckbbene="+ckbbene;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}
