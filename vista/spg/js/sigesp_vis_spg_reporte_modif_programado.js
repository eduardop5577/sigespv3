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

var fromReporteModifProg = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;
var fecha = new Date();

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

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
	
	//--------------------------------------------------------------------------------------------

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
		posicion:'position:absolute;left:30px;top:0px', 
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
		posicion:'position:absolute;left:620px;top:0px', 
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
	
	//-------------------------------------------------------------------------------------------------------------------------

	//creacion del datastore y colunmodel para el campo usuario
	var usuarios = Ext.data.Record.create([
  		{name: 'codusu'},
  		{name: 'nomusu'},
  		{name: 'apeusu'}
  	]);
  	
  	var dsUsuarios =  new Ext.data.Store({
  	    reader: new Ext.data.JsonReader({
  		root: 'raiz',             
  		id: "id"},usuarios)
  	});
  						
  	var cmcatUsuarios = new Ext.grid.ColumnModel([
          {header: "<H1 align='center'>C�digo</H1>", width: 20, sortable: true,   dataIndex: 'codusu'},
          {header: "<H1 align='center'>Nombre</H1>", width: 40, sortable: true, dataIndex: 'nomusu'},
          {header: "<H1 align='center'>Apellido</H1>", width: 40, sortable: true, dataIndex: 'apeusu'}
    ]);
  	
  	//componente campocatalogo para el campo cuentas contables
  	comcampocatUsuarios = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo de Usuarios</H1>",
  			anchoformbus: 450,
  			altoformbus:120,
  			anchogrid: 450,
  			altogrid: 400,
  			anchoven: 500,
  			altoven: 400,
  			anchofieldset: 850,
  			datosgridcat: dsUsuarios,
  			colmodelocat: cmcatUsuarios,
  			rutacontrolador:'../../controlador/sep/sigesp_ctr_sep_reporte.php',
  			parametros: "ObjSon={'operacion': 'buscarUsuarios'}",
  			arrfiltro:[{etiqueta:'C�digo',id:'codiusua',valor:'uniddadcod'},
  					   {etiqueta:'Descripci�n',id:'denousua',valor:'udnidadden'}],
  			posicion:'position:absolute;left:30px;top:0px',
  			tittxt:'Usuario',
  			idtxt:'codusu',
  			campovalue:'codusu',
  			anchoetiquetatext:50,
  			anchotext:120,
  			anchocoltext:0.22,
  			idlabel:'nomusu',
  			labelvalue:'nomusu',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:230,
  			tipbus:'L',
  			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false
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

	var	fromCuenPres = new Ext.form.FieldSet({
			title:'Intervalo de Cuentas',
			style: 'position:absolute;left:10px;top:370px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 60,
			items:[comcampocatcuentadesde.fieldsetCatalogo,comcampocatcuentahasta.fieldsetCatalogo]
	})
	
	//--------------------------------------------------------------------------------------------

	var	fromUsuario = new Ext.form.FieldSet({ 
			title:'Usuario',
			style: 'position:absolute;left:10px;top:435px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 58,
			items:[comcampocatUsuarios.fieldsetCatalogo]
	})

	//--------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteModifProg = new Ext.FormPanel({
		applyTo: 'formReporteModifProg',
		width:980, //700
		height: 500,
		title: "<H1 align='center'>Modificaciones al Programado</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromEstructura,
		        fromCuenPres,
		        fromUsuario
		        ]
	});	
	fromReporteModifProg.doLayout();
});	

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
	var codusu = Ext.getCmp('codusu').getValue();
	var cuentades = Ext.getCmp('cuentadesde').getValue();
	var cuentahas = Ext.getCmp('cuentahasta').getValue();

	if(cuentades>cuentahas){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El rango de Busqueda por Cuenta Presupuestaria no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((cuentades!='' && cuentahas=='') || (cuentades=='' && cuentahas!='')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Cuenta Presupuestaria !!!',
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
	if(valido)
	{
		for ( var i = 0; i < arrCodigosDesde.length; i++) {
			if(arrCodigosDesde[i]=="0000000000000000000000000" || arrCodigosDesde[i]=="--"){
				arrCodigosDesde[i]="";
			}
			if(arrCodigosHasta[i]=="0000000000000000000000000" || arrCodigosHasta[i]=="--"){
				arrCodigosHasta[i]="";
			}		
		}
		pagina="reportes/sigesp_spg_rpp_modif_programado.php?codestpro1="+arrCodigosDesde[0]
		      +"&codestpro2="+arrCodigosDesde[1]+"&codestpro3="+arrCodigosDesde[2]
		      +"&codestpro4="+arrCodigosDesde[3]+"&codestpro5="+arrCodigosDesde[4]
		      +"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]
		      +"&codestpro3h="+arrCodigosHasta[2]+"&codestpro4h="+arrCodigosHasta[3]
		      +"&codestpro5h="+arrCodigosHasta[4]+"&txtcuentades="+cuentades+"&txtcuentahas="+cuentahas
		      +"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5]+"&codusu="+codusu;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");

	}
}