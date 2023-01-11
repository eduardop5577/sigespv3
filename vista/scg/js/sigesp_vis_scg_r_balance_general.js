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

var frmPagos = null;  //instancia del formulario de pagos
var Actualizar = null;
var ruta ='../../controlador/scg/sigesp_ctr_scg_estado_resultado.php'; //ruta del controlador
var ruta2 ='../../controlador/scg/sigesp_ctr_scg_balance_comprobacion.php'; //ruta del controlador
var fechaPrimera = obtenerPrimerDiaMes();
var formato1 = '';
var cencos = '';

barraherramienta = true;
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
//--------------------------------------------------------------------------------------------------------------------------------	
//**********************************************************************************************************************************	
//                                     		INICIO DEL FORMULARIO PROVEEDOR / BENEFICIARIO	
//**********************************************************************************************************************************
// Combo del A�o
var anio = [ 
				 [ '2005', '2005' ], 
	             [ '2006', '2006' ],
				 [ '2007', '2007' ],
				 [ '2008', '2008' ],
				 [ '2009', '2009' ],
				 [ '2010', '2010' ],
				 [ '2011', '2011' ],
				 [ '2012', '2012' ],
				 [ '2013', '2013' ],
				 [ '2014', '2014' ],
				 [ '2015', '2015' ],
				 [ '2016', '2016' ],
				 [ '2017', '2017' ], 
	             [ '2018', '2018' ],
				 [ '2019', '2019' ],
				 [ '2020', '2020' ],
				 [ '2021', '2021' ],
				 [ '2022', '2022' ],
				 [ '2023', '2023' ],
				 [ '2024', '2024' ],
				 [ '2025', '2025' ],
				 [ '2026', '2026' ],
				 [ '2027', '2027' ],
				 [ '2028', '2028' ],
				 [ '2029', '2029' ],
				 [ '2030', '2030' ]
				 ];
	
	var staniodes = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : anio
	}); //Fin de store para el tipo de impresion

	//creando objeto combo filtrar
	var cmbaniodes = new Ext.form.ComboBox({
		store : staniodes,
		fieldLabel : 'A&#241;o',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'anodes',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
//--------------------------------------------------------------------------------------------------------------------------------	
// Combo de nivel
var nivel = [ 
				 [ '1', '01' ], 
	             [ '2', '02' ],
				 [ '3', '03' ],
				 [ '4', '04' ],
				 [ '5', '05' ],
				 [ '6', '06' ],
				 [ '7', '07' ],
				 [ '8', '08' ],
				 [ '9', '09' ],
				 [ '10', '10' ],
			];
	
	var stnivel = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : nivel
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbnivel = new Ext.form.ComboBox({
		store : stnivel,
		fieldLabel : 'Nivel',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'cmbnivel',
		width : 50,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
	
	cmbnivel.setValue('01');

//--------------------------------------------------------------------------------------------------------------------------------	
// Combo mes desde (MES)
var mesdesde = [ 
				 [ 'Enero', '01' ], 
	             [ 'Febrero', '02' ],
				 [ 'Marzo', '03' ],
				 [ 'Abril', '04' ],
				 [ 'Mayo', '05' ],
				 [ 'Junio', '06' ],
				 [ 'Julio', '07' ],
				 [ 'Agosto', '08' ],
				 [ 'Septiembre', '09' ],
				 [ 'Octubre', '10' ],
				 [ 'Noviembre', '11' ],
				 [ 'Diciembre', '12' ],
				 ];
	
	var stmesdesde = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : mesdesde
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbmesdesde = new Ext.form.ComboBox({
		store : stmesdesde,
		fieldLabel : 'Mes',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'mesdes',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
	
	//cmbtiporeporte.setValue('P');
//**********************************************************************************************************************************
//creacion del formulario de datos de intervalo de fechas
	fieldset = new Ext.form.FieldSet({
		width: 705,
		height: 70,
		title: "Rango en Meses",
		style: 'position:absolute;left:10px;top:105px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:200px;top:15px',
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:30,
						items: [cmbmesdesde]
					}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:370px;top:15px',
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:30,
						items: [cmbaniodes]
					}]
				}]
	});	
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO ORDENADO POR
//**********************************************************************************************************************************
//creacion del formulario de datos de centro de costos
	fieldsetdos = new Ext.form.FieldSet({
		width: 705,
		height: 70,
		title: "Nivel de las Cuentas",
		style: 'position:absolute;left:10px;top:280px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:270px;top:12px',
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [cmbnivel]
					}]
				}]
	});	
//----------------------------------------------------------------------------------------------------------------------------------
//creacion del formulario de datos de estado resultado
	fieldsettres = new Ext.form.FieldSet({
		width: 705,
		height: 80,
		title: "Titulo del Reporte",
		style: 'position:absolute;left:10px;top:370px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:0px;top:10px',
				items: [{
						layout: "form",
						border: false,
						items:[{
								xtype: "radiogroup",
								fieldLabel: "",
								labelSeparator:"",
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',	
								columns: [180,180,180,180],
								id:'rbtitulo',
								items: [
								        {boxLabel: 'Estandar', name: 'rbtitulo1',inputValue: '1',checked:true},
								        {boxLabel: 'Mensual', name: 'rbtitulo1', inputValue: '2'},
								        {boxLabel: 'Trimestral', name: 'rbtitulo1', inputValue: '3'},
								        {boxLabel: 'Anual', name: 'rbtitulo1', inputValue: '4'}
								        ]
							}]
						}]
				}]
	});
//----------------------------------------------------------------------------------------------------------------------------------
	var opcimp = [ 
				 [ 'PDF', 'P' ], 
	             [ 'EXCEL', 'E' ] 
				 ];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbtiporeporte = new Ext.form.ComboBox({
		store : stOpcimp,
		fieldLabel : 'Tipo Impresi&#243;n',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'tipoimp',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
	
	cmbtiporeporte.setValue('P');
//----------------------------------------------------------------------------------------------------------------------------------	
	var opcfor = [ 
				 [ 'Formato 1', '1' ], 
	             [ 'Formato 2', '2' ] 
				 ];
	
	var stopcfor = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcfor
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbformato = new Ext.form.ComboBox({
		store : stopcfor,
		fieldLabel : 'Tipo Formato',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'tipform',
		width : 120,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		listeners: {
					'select': function(){
						if(this.getValue()=='2')
						{	
							Ext.getCmp('rbtitulo').reset();
							Ext.getCmp('rbtitulo').disable();
						}
						else
						{
							Ext.getCmp('rbtitulo').reset();
							Ext.getCmp('rbtitulo').enable();
						}
					 }
				   }
	});
	
	cmbformato.setValue('1');

//----------------------------------------------------------------------------------------------------------------------------------
	
	fieldsetIntervaloFechasDias = new Ext.form.FieldSet({
		title:"Rango en Dias",
		style: 'position:absolute;left:10px;top:180px',
		border:true,
		width: 705,
		cls :'fondo',
		height: 75,
		items: [{
				style:'position:absolute;left:25px;top:20px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [{
								 xtype:"datefield",
								 labelSeparator:'',
								 fieldLabel:'Desde',
								 name:'fechadesde',
								 id:'fecdesde',
								 value: fechaPrimera,
								 width:150,
								 binding:true,
								 hiddenvalue:'',
								 defaultvalue:'1900-01-01',
								 allowBlank:true,
								 autoCreate:{tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
							}]
					}]
				},
				{
				style:'position:absolute;left:400px;top:25px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [{
								 xtype:"datefield",
								 labelSeparator:'',
								 fieldLabel:'Hasta',
								 name:'fechahasta',
								 id:'fechasta',
								 value: new Date().format('Y-m-d'),
								 width:150,
								 binding:true,
								 hiddenvalue:'',
								 defaultvalue:'1900-01-01',
								 allowBlank:true,
								 autoCreate:{tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
						}]
				}]
	});
	
	fieldsetPeriodos = new Ext.form.FieldSet({
		title:"Rango de Fecha",
		style: 'position:absolute;left:10px;top:40px',
		border:true,
		width: 705,
		cls :'fondo',
		height: 60,
		items: [{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:20px;top:10px',
			items: [{
				layout: "form",
				border: false,
				items:[{
					xtype: "radiogroup",
					fieldLabel: "",
					labelSeparator:"",
					binding:true,
					hiddenvalue:'',
					defaultvalue:'',	
					columns: [250,250],
					id:'hidbot',
					items: [{
						boxLabel: 'Meses', name: 'intervalp', inputValue: '1', checked:true,
						listeners:{
							'check': function (checkbox, checked) {
								if(checked) {
									Ext.getCmp('mesdes').reset();
									Ext.getCmp('mesdes').enable();
									Ext.getCmp('anodes').reset();
									Ext.getCmp('anodes').enable();
									Ext.getCmp('fecdesde').reset();
									Ext.getCmp('fechasta').reset();
									Ext.getCmp('fecdesde').disable();
									Ext.getCmp('fechasta').disable();
								}
							}
						}
					},{
						boxLabel: 'Dias', name: 'intervalp', inputValue: '2',
						listeners:{
							'check': function (checkbox, checked) {
								if(checked) {
									Ext.getCmp('mesdes').reset();
									Ext.getCmp('mesdes').disable();
									Ext.getCmp('anodes').reset();
									Ext.getCmp('anodes').disable();
									Ext.getCmp('fecdesde').reset();
									Ext.getCmp('fechasta').reset();
									Ext.getCmp('fecdesde').enable();
									Ext.getCmp('fechasta').enable();
								}
							}
						}
					}]
				}]
			}]
		}]
	});
	
	//Creacion del formulario REPORTE
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
		applyTo: 'formulario',
		width: 750,
		height: 500,
		title: "<H1 align='center'>Balance General</H1>",
		frame: true,
		autoScroll: true,
		style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
		items: [fieldsetPeriodos,fieldset,fieldsetIntervaloFechasDias,fieldsetdos,fieldsettres,
		{
			xtype: 'hidden',
			id: 'estcencos',
			binding:true,
			defaultvalue:'',
			allowBlank:true
		},{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:425px;top:10px',
			border:false,
			items: [{
				layout:"form",
				border:false,
				labelWidth:100,
				items: [cmbtiporeporte]
			}]
		},{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:50px;top:10px',
			border:false,
			items: [{
				layout:"form",
				border:false,
				labelWidth:100,
				items: [cmbformato]
			}]
		}]	
	});
	irNuevo();
	irBuscarFormato();
	//llenarComboAnio();
	//llenarComboMesDesAnio();
});
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                  INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************
function irNuevo(){
	
	Ext.getCmp('mesdes').reset();
	Ext.getCmp('mesdes').enable();
	Ext.getCmp('anodes').reset();
	Ext.getCmp('anodes').enable();
	Ext.getCmp('fecdesde').reset();
	Ext.getCmp('fechasta').reset();
	Ext.getCmp('fecdesde').disable();
	Ext.getCmp('fechasta').disable();
	
	var myJSONObject = {
			"operacion":"verificar_estatus_estcencos" 
		};
			
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: ruta,
		params: parametros,
		method: 'POST',
		success: function ( result, request ) 
		{ 
			cencos = result.responseText;
			if (cencos != "")
			{
				Ext.getCmp('estcencos').setValue(cencos);
			}
		},
		failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});
}

function validarCentroCosContable()
{
	var unidadOk = true;
	if(cencos==0){
		unidadOk = false;
	}
	
	return unidadOk;
}

function irBuscarFormato()
{
	var myJSONObject =
	{
		'operacion'   : 'buscarFormato',
		'sistema'	  : 'SCG',
		'seccion'     : 'REPORTE',
		'variable'    : 'BALANCE_GENERAL',
		'valor'		  : 'sigesp_scg_rpp_balance_general.php',
		'tipo'		  : 'C'
	};	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request(
	{
		url: ruta,
		params: parametros,
		method: 'POST',
		success: function (result, request)
		{ 
			formato1 = result.responseText;			
		},
		failure: function (result, request){ 
			Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
		}
	})
}

//**********************************************************************************************************************************	
//                                  						BOTONES 
//**********************************************************************************************************************************
function irImprimir()
{
	if (Ext.getCmp('tipoimp').getValue()=='P')
	{
		if (Ext.getCmp('tipform').getValue()=='1')		
		{
			cmbnivel = Ext.getCmp('cmbnivel').getValue();
			cmbmes= Ext.getCmp('mesdes').getValue();
			cmbagno= Ext.getCmp('anodes').getValue();
			fecdesde = Ext.getCmp('fecdesde').getValue();
			fechasta = Ext.getCmp('fechasta').getValue();
			txtfecdes = fecdesde.format(Date.patterns.fechacorta);
			txtfechas = fechasta.format(Date.patterns.fechacorta);
			var radio= Ext.getCmp('rbtitulo');
			for (var j = 0; j < radio.items.length; j++)
			{
			  if (radio.items.items[j].checked)
			  {
				rbtitulo = radio.items.items[j].inputValue;
				break;
			  }
			}
			var radio= Ext.getCmp('hidbot');
			for (var j = 0; j < radio.items.length; j++)
			{
			  if (radio.items.items[j].checked)
			  {
				hidbot = radio.items.items[j].inputValue;
				break;
			  }
			} 
			
			if((cmbmes=="" || cmbagno=="") && hidbot==1)
			{
				alert ("Debe Seleccionar los Parametros de Busqueda");
			}
			else
			{
				if(rbtitulo==1){
					pagina="reportes/"+formato1+"?cmbmes="+cmbmes+"&rango="+hidbot;
					pagina=pagina+"&cmbagno="+cmbagno+"&cmbnivel="+cmbnivel+"&fecdesde="+txtfecdes+"&fechasta="+txtfechas+"&tituloreporte=N";
					window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
				}
	
				if(rbtitulo==2){
					pagina="reportes/"+formato1+"?cmbmes="+cmbmes+"&rango="+hidbot;
					pagina=pagina+"&cmbagno="+cmbagno+"&cmbnivel="+cmbnivel+"&fecdesde="+txtfecdes+"&fechasta="+txtfechas+"&tituloreporte=M";
					window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
				}
	
				if(rbtitulo==3){
					pagina="reportes/"+formato1+"?cmbmes="+cmbmes+"&rango="+hidbot;
					pagina=pagina+"&cmbagno="+cmbagno+"&cmbnivel="+cmbnivel+"&fecdesde="+txtfecdes+"&fechasta="+txtfechas+"&tituloreporte=T";
					window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
				}
	
				if(rbtitulo==4){
					pagina="reportes/"+formato1+"?cmbmes="+cmbmes+"&rango="+hidbot;
					pagina=pagina+"&cmbagno="+cmbagno+"&cmbnivel="+cmbnivel+"&fecdesde="+txtfecdes+"&fechasta="+txtfechas+"&tituloreporte=A";
					window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
				}
			}
		}
		else
		{
			cmbmes= Ext.getCmp('mesdes').getValue();
			cmbagno= Ext.getCmp('anodes').getValue();
			cmbnivel = Ext.getCmp('cmbnivel').getValue();
			fecdesde = Ext.getCmp('fecdesde').getValue();
			fechasta = Ext.getCmp('fechasta').getValue();
			txtfecdes = fecdesde.format(Date.patterns.fechacorta);
			txtfechas = fechasta.format(Date.patterns.fechacorta);
			var radio= Ext.getCmp('hidbot');
			for (var j = 0; j < radio.items.length; j++)
			{
			  if (radio.items.items[j].checked)
			  {
				hidbot = radio.items.items[j].inputValue;
				break;
			  }
			} 
			if((fecdesde=="")||(fechasta==""))
			{
				alert ("Debe Seleccionar los Parametros de Busqueda");
			}
			else
			{
				pagina="reportes/sigesp_scg_rpp_balance_general_formato2.php?cmbmes="+cmbmes+"&cmbagno="+cmbagno+"";
				pagina=pagina+"&cmbnivel="+cmbnivel+"&fecdesde="+txtfecdes+"&fechasta="+txtfechas+"&rango="+hidbot+"";
				window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
	}
	else
	{
		if (Ext.getCmp('tipform').getValue()=='1')		
		{
			cmbnivel = Ext.getCmp('cmbnivel').getValue();
			cmbmes= Ext.getCmp('mesdes').getValue();
			cmbagno= Ext.getCmp('anodes').getValue();
			fecdesde = Ext.getCmp('fecdesde').getValue();
			fechasta = Ext.getCmp('fechasta').getValue();
			txtfecdes = fecdesde.format(Date.patterns.fechacorta);
			txtfechas = fechasta.format(Date.patterns.fechacorta);
			var radio= Ext.getCmp('hidbot');
			for (var j = 0; j < radio.items.length; j++)
			{
			  if (radio.items.items[j].checked)
			  {
				hidbot = radio.items.items[j].inputValue;
				break;
			  }
			} 
			if((fecdesde=="")||(fechasta==""))
			{
				alert ("Debe Seleccionar los Parametros de Busqueda");
			}
			else
			{
				pagina="reportes/sigesp_scg_rpp_balance_general_excel.php?cmbmes="+cmbmes+"&cmbagno="+cmbagno+"";;
				pagina=pagina+"&cmbnivel="+cmbnivel+"&fecdesde="+txtfecdes+"&fechasta="+txtfechas+"&rango="+hidbot+"";
				window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
		else
		{
			alert ("Este formato no esta hecho para Excel");
		}
	}
}
