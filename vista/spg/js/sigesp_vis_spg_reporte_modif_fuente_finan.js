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

var fromReporteModifFueFin = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;
var fecha = new Date();

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//----------------------------------------------------------------------------------------------------------------------------------	

	fieldSetEstOrigenDesde = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
		titform: 'Estructura Presupuestaria Desde',
		mostrarDenominacion:false,
		sinFuente:false,
		sinCuenta:false,
		idtxt:'comfsestdesde',
		nofiltroest:'1'
	});

	fieldSetEstOrigenHasta = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
		titform: 'Estructura Presupuestaria Hasta',
		mostrarDenominacion:false,
		sinFuente:false,
		sinCuenta:false,
		idtxt:'comfsesthasta',
		nofiltroest:'1'
	});
	
	//----------------------------------------------------------------------------------------------------------------------------------	
	
	//creando store para la afectacion
	var reMoneda = Ext.data.Record.create([
          {name: 'codmon'},
          {name: 'denmon'}
     ]);
	
	dsMoneda =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMoneda)				
	});
	//fin creando store para el combo afectacion
	
	//creando objeto combo afectacion
	cmbMoneda = new Ext.form.ComboBox({
		store: dsMoneda,
		labelSeparator :'',
		fieldLabel:'Moneda',
		displayField:'denmon',
		valueField:'codmon',
		name: 'moneda',
		width:150,
		listWidth: 150, 
		id:'moneda',
		typeAhead: true,
		editable:false,
		defaultvalue:'---',
		emptyText:'Bolivar',
		selectOnFocus:true,
		mode:'local',
		triggerAction:'all',
		valor:'',
	});
	//Fin creando objeto combo afectacion
	
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
          {header: "C�digo", width: 20, sortable: true,   dataIndex: 'coduac'},
          {header: "Denominaci�n", width: 40, sortable: true, dataIndex: 'denuac'}
  	]);
  	//componente campocatalogo para el campo unidad administradora
  	
  	comcampocatUnidadEjecutoradesde = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo Unidades Administradoras</H1>",
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
  			arrfiltro:[{etiqueta:'Codigo',id:'codidesde',valor:'coduac',longitud:'10'},
  					   {etiqueta:'Denominaci�n',id:'desdesde',valor:'denuac'}],
  			posicion:'position:absolute;left:30px;top:0px',
  			tittxt:'C�digo',
  			idtxt:'coduac',
  			campovalue:'coduac',
  			anchoetiquetatext:50,
  			anchotext:100,
  			anchocoltext:0.20,
  			idlabel:'denuac',
  			labelvalue:'denuac',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:320,
  			tipbus:'L',
  			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false
  	});

	//-------------------------------------------------------------------------------------

	//Datos del tipo de formato a imprimir
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ]];

	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});

	//--------------------------------------------------------------------------------------------

	var	fromEstructura = new Ext.form.FieldSet({
		title:'',
		style: 'position:absolute;left:10px;top:10px',
		border:true,
		width: 925,
		cls :'fondo',
		height: 275+obtenerPosicion(),
		items: [{	
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:5px;top:15px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [fieldSetEstOrigenDesde.fsEstructura]
					}]
				},
				{	
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:470px;top:15px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [fieldSetEstOrigenHasta.fsEstructura]
					}]
				}]
	})

	//--------------------------------------------------------------------------------------------

	var	fromConfigurarReporte = new Ext.form.FieldSet({
			title:'Unidad Administrativa',
			style: 'position:absolute;left:10px;top:375px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 58,
			items:[comcampocatUnidadEjecutoradesde.fieldsetCatalogo]
	})

	//--------------------------------------------------------------------------------------------	

	var	fromOrden = new Ext.form.FieldSet({ 
			title:'Ordenado por',
			style: 'position:absolute;left:10px;top:440px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 48,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:30px;top:3px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 20,
							items: [{
									xtype: "radiogroup",
									fieldLabel: '',
									labelSeparator:"",	
									columns: [100,150],
									id:'rdOrden',
									binding:true,
									hiddenvalue:'',
									defaultvalue:0,
									allowBlank:true,
									items: [
									        {boxLabel: 'Fecha', name: 'orden',inputValue: '1',checked:true},
									        {boxLabel: 'Nro. Documento', name: 'orden', inputValue: '0'}
									        ]
									}]
							}]
					}]
	})

	//--------------------------------------------------------------------------------------------

	var	fromIntervaloFechas = new Ext.form.FieldSet({ 
			title:'Intervalo de Fechas',
			style: 'position:absolute;left:10px;top:495px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 58,
			items:[{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:50px;top:10px',
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
				style: 'position:absolute;left:660px;top:10px',
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

	var	fromMoneda = new Ext.form.FieldSet({ 
			title:'Reporte en',
			style: 'position:absolute;left:10px;top:560px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 58,
			items:[{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:50px;top:10px',
					border:false,
					items:[{
							layout:"form",
							border:false,
							labelWidth:50,
							items:[cmbMoneda]
						}]
				}]
	})

	//--------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteModifFueFin = new Ext.FormPanel({
		applyTo: 'formReporteModifFueFin',
		width:970, //700
		height: 500,
		title: "<H1 align='center'>Presupuesto Modificado Detallado por Fuente de Financiamiento</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromEstructura,
		        fromConfigurarReporte,
		        fromOrden,
		        fromIntervaloFechas,
		        fromMoneda
		        ]
	});	
	llenarComboMoneda();
	fromReporteModifFueFin.doLayout();
});	

//----------------------------------------------------------------------------------------------------------------------------------	

function obtenerPosicion(){
	if(empresa['numniv']=='3'){
		return 0;
	}
	else{
		return 80;
	}
}

function irImprimir()
{
	var valido = true;
	var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
	var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
	var coduniadm = Ext.getCmp('coduac').getValue();
	var moneda = Ext.getCmp('moneda').getValue();
	var fechaReporteDesde = Ext.getCmp('dtFechaDesde').getValue().format('d/m/Y');
	var fechaReporteHasta = Ext.getCmp('dtFechaHasta').getValue().format('d/m/Y');

	if(fechaReporteDesde>fechaReporteHasta){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El rango de Busqueda por Fecha no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((arrCodigosDesde[0]=='0000000000000000000000000' && arrCodigosHasta[0]!='0000000000000000000000000') || (arrCodigosDesde[0]!='0000000000000000000000000' && arrCodigosHasta[0]=='0000000000000000000000000')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((arrCodigosDesde[1]=='0000000000000000000000000' && arrCodigosHasta[1]!='0000000000000000000000000') || (arrCodigosDesde[1]!='0000000000000000000000000' && arrCodigosHasta[1]=='0000000000000000000000000')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((arrCodigosDesde[2]=='0000000000000000000000000' && arrCodigosHasta[2]!='0000000000000000000000000') || (arrCodigosDesde[2]!='0000000000000000000000000' && arrCodigosHasta[2]=='0000000000000000000000000')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(empresa['estmodest']==2){
		if((arrCodigosDesde[3]=='0000000000000000000000000' && arrCodigosHasta[3]!='0000000000000000000000000') || (arrCodigosDesde[3]!='0000000000000000000000000' && arrCodigosHasta[3]=='0000000000000000000000000')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if((arrCodigosDesde[4]=='0000000000000000000000000' && arrCodigosHasta[4]!='0000000000000000000000000') || (arrCodigosDesde[4]!='0000000000000000000000000' && arrCodigosHasta[4]=='0000000000000000000000000')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	if((arrCodigosDesde[6]=='0000000000000000000000000' && arrCodigosHasta[6]!='0000000000000000000000000') || (arrCodigosDesde[6]!='0000000000000000000000000' && arrCodigosHasta[6]=='0000000000000000000000000')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Fuente de Financiamiento !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((arrCodigosDesde[7]=='0000000000000000000000000' && arrCodigosHasta[7]!='0000000000000000000000000') || (arrCodigosDesde[7]!='0000000000000000000000000' && arrCodigosHasta[7]=='0000000000000000000000000')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Cuenta Presupuestaria !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(valido)
	{
		if(coduniadm!=''){
			for ( var i = 0; i < arrCodigosDesde.length; i++) {
				if(arrCodigosDesde[i]=="0000000000000000000000000" || arrCodigosDesde[i]=="--"){
					arrCodigosDesde[i]="";
				}
				if(arrCodigosHasta[i]=="0000000000000000000000000" || arrCodigosHasta[i]=="--"){
					arrCodigosHasta[i]="";
				}		
			}
			var orden = 'F';;
			if(Ext.getCmp('rdOrden').items.items[1].checked){ //Ocultar campo detalle
				orden = 'D';
			}
			pagina="reportes/sigesp_spg_rpp_modificacion_fuente_finan.php?codestpro1="+arrCodigosDesde[0]
			      +"&codestpro2="+arrCodigosDesde[1]+"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]
				  +"&codestpro5="+arrCodigosDesde[4]+"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]
			      +"&codestpro3h="+arrCodigosHasta[2]+"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]
				  +"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]+"&txtfecdes="+fechaReporteDesde
				  +"&rborden="+orden+"&txtfechas="+fechaReporteHasta+"&txtcodfuefindes="+arrCodigosDesde[6]
			      +"&txtcodfuefinhas="+arrCodigosHasta[6]+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5]
			      +"&unidad="+coduniadm+"&denunidad="+""+"&codmoneda="+moneda+"&denmoneda="+"";
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
		else{
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe Seleccionar una Unidad Administrativa !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
}

function imprimir(variable,nombreArchivo,fechaReporteDesde,orden,fechaReporteHasta,arrCodigosDesde,arrCodigosHasta,campoOculto,ruta)
{	
	//preparando los datos a enviar segun el tipo de estrutura correspondiente a la empresa
	if(empresa['estmodest']==1){
		var datosReporte ="?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
		+"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
		+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
		+"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
		+"&txtfecdes="+fechaReporteDesde+"&rborden="+orden+"&txtfechas="+fechaReporteHasta
		+"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
		+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5]+"&mostrar="+campoOculto;
	}else {
		var datosReporte ="?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
		+"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
		+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
		+"&codestpro4="+arrCodigosDesde[3]+"&codestpro4h="+arrCodigosHasta[3]
		+"&codestpro5h="+arrCodigosHasta[4]+"&codestpro5h="+arrCodigosHasta[4]
		+"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
		+"&txtfecdes="+fechaReporteDesde+"&rborden="+orden+"&txtfechas="+fechaReporteHasta
		+"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
		+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5]+"&mostrar="+campoOculto;
	}
	
	if(ruta==''){
		var myJSONObject =
		{
				'operacion'   : 'buscarFormato',
				'sistema'	  : 'SPG',
				'seccion'     : 'REPORTE',
				'variable'    : variable,
				'valor'		  : nombreArchivo,
				'tipo'		  : 'C'
		};	
		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
			params: parametros,
			method: 'POST',
			success: function (result, request)
			{ 
				formato = result.responseText;	
				if(empresa['estmodest']==1){
					pagina="reportes/"+formato+datosReporte;
				}
				else{
					pagina="reportes/"+formato+datosReporte;
				}
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			},
			failure: function (result, request){ 
				Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
			}
		})
	}
	else{
		if(empresa['estmodest']==1){
			pagina="reportes/"+ruta+datosReporte;
		}
		else{
			pagina="reportes/"+ruta+datosReporte;
		}
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}

//Funcion que agrega los datos al combo de afectacion
function llenarComboMoneda()
{
	var myJSONObject ={
			"operacion": 'buscarMoneda'
	};	
	var ObjSon=JSON.stringify(myJSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
		params : parametros,
		method: 'POST',
		success: function (resultado, request) { 
			var datosafec = resultado.responseText;
			if(datosafec!='')
			{
				var DatosAfec = eval('(' + datosafec + ')');
			}
			dsMoneda.loadData(DatosAfec);
		}//fin del success
	});//fin del ajax request	
}