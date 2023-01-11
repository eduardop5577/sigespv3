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

var fromReporteResProvBen = null;
barraherramienta = true;
var fromProveedor = null;
var fromBeneficiario = null;
var fecha = new Date();

Ext.onReady(function() {
Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	
	//--------------------------------------------------------------------------------------------

	//Creando el campo de proveedor
	var reProveedor = Ext.data.Record.create([
		{name: 'cod_pro'}, //campo obligatorio                             
		{name: 'nompro'}, //campo obligatorio
		{name: 'dirpro'}, //campo obligatorio
		{name: 'rifpro'}, //campo obligatorio
		{name: 'tipconpro'} //campo adicional
	]);
		
	//componente catalogo de proveedores
	comcampocatproveedordesde = new com.sigesp.vista.comCatalogoProveedor({
		idComponente:'provdesde',
		anchofieldset: 850,
		reCatalogo: reProveedor,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
		parametros: "ObjSon={'operacion': 'buscarProveedores'",
		posicion:'position:absolute;left:25px;top:10px', 
		tittxt:'Desde',
		idtxt:'cod_prodes',
		campovalue:'cod_pro',
		anchoetiquetatext:50,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'nomprodes',
		labelvalue:'',
		anchocoletiqueta:0.55, 
		anchoetiqueta:0,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});
	
	comcampocatproveedorhasta = new com.sigesp.vista.comCatalogoProveedor({
		idComponente:'provhasta',
		anchofieldset: 850,
		reCatalogo: reProveedor,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
		parametros: "ObjSon={'operacion': 'buscarProveedores'",
		posicion:'position:absolute;left:350px;top:10px', 
		tittxt:'Hasta',
		idtxt:'cod_prohas',
		campovalue:'cod_pro',
		anchoetiquetatext:50,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'nomprohas',
		labelvalue:'',
		anchocoletiqueta:0.55, 
		anchoetiqueta:0,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});
	//fin componente catalogo de proveedores
	
	//--------------------------------------------------------------------------------------------
	
	//Creando el campo de beneficiario
	var reBeneficiario = Ext.data.Record.create([
		{name: 'ced_bene'}, //campo obligatorio                             
		{name: 'nombene'}, //campo obligatorio
	]);
		
	//componente catalogo de proveedores
	comcampocatbeneficiariodesde = new com.sigesp.vista.comCatalogoBeneficiario({
		idComponente:'benedesde',
		anchofieldset: 850,
		reCatalogo: reBeneficiario,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatbeneficiario.php',
		parametros: "ObjSon={'operacion': 'buscarBeneficiarios'",
		posicion:'position:absolute;left:25px;top:10px', 
		tittxt:'Desde',
		idtxt:'ced_benedes',
		campovalue:'ced_bene',
		anchoetiquetatext:50,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'nombenedes',
		labelvalue:'',
		anchocoletiqueta:0.55, 
		anchoetiqueta:0,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});
	
	comcampocatbeneficiariohasta = new com.sigesp.vista.comCatalogoBeneficiario({
		idComponente:'benehasta',
		anchofieldset: 850,
		reCatalogo: reBeneficiario,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatbeneficiario.php',
		parametros: "ObjSon={'operacion': 'buscarBeneficiarios'",
		posicion:'position:absolute;left:350px;top:10px', 
		tittxt:'Hasta',
		idtxt:'ced_benehas',
		campovalue:'ced_bene',
		anchoetiquetatext:50,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'nombenehas',
		labelvalue:'',
		anchocoletiqueta:0.55, 
		anchoetiqueta:0,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});
	//fin componente catalogo de proveedores
	
	//--------------------------------------------------------------------------------------------

	var	fromIntervaloFechas = new Ext.form.FieldSet({
			title:'Intervalo de Fechas',
			style: 'position:absolute;left:70px;top:10px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 58,
			items:[{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:40px;top:10px',
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
					style: 'position:absolute;left:370px;top:10px',
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

	var fromEstado = new Ext.form.FieldSet({
			title:'Estado Presupuestario',
			style: 'position:absolute;left:5px;top:75px',
			border:true,
			width: 700,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:10px;top:10px',
					items: [{
							layout: "form",
							border: false,
							items: [{
									xtype: "radiogroup",
									fieldLabel: '',
									labelSeparator:"",	
									columns: [200,200],
									id:'rdEstPre',
									binding:true,
									hiddenvalue:'',
									defaultvalue:0,
									allowBlank:true,
									items: [
									        {boxLabel: 'Nombre', name: 'estado', inputValue: 'N',checked:true},
									        {boxLabel: 'Codigo', name: 'estado', inputValue: 'C'},
									 ]					
							}]
						}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:450px;top:13px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 100,
							items: [{
									xtype: 'checkbox',
									labelSeparator :'',
									fieldLabel: 'Imprimir Detalle',
									id: 'impdet',
									inputValue:1,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'0',
									allowBlank:true						
								}]
							}]
					}]
	})

	//--------------------------------------------------------------------------------------------

	var fromTipo = new Ext.form.FieldSet({
			title:'Proveedor/Beneficiario',
			style: 'position:absolute;left:5px;top:140px',
			border:true,
			width: 700,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:10px;top:10px',
					items: [{
							layout: "form",
							border: false,
							items: [{
									xtype: "radiogroup",
									fieldLabel: '',
									labelSeparator:"",	
									columns: [190,190,190],
									id:'rdFormato',
									binding:true,
									hiddenvalue:'',
									defaultvalue:0,
									allowBlank:true,
									items: [{
								        	boxLabel: 'Proveedor',
								        	name: 'formato',
								        	inputValue: '0',
								        	listeners:{	
									        	'check': function (checkbox, checked){
										        	if(checked){
										        		limpiarFormulario(fromBeneficiario);
										        		fromProveedor.show();
										        		fromBeneficiario.hide();
										        	}
										        }
								        	}
									        },
									        {
								        	boxLabel: 'Beneficiario', 
								        	name: 'formato',
								        	inputValue: '1', 
								        	listeners:{	
									        	'check': function (checkbox, checked){
										        	if(checked){
										        		limpiarFormulario(fromProveedor);
										        		fromProveedor.hide();
										        		fromBeneficiario.show();
										        	}
										        }
								        	}
									        },
									        {
								        	boxLabel: 'Ninguno',
								        	name: 'formato', 
								        	inputValue: '2',
								        	checked:true, 
								        	listeners:{	
									        	'check': function (checkbox, checked){
										        	if(checked){
										        		limpiarFormulario(fromBeneficiario);
										        		limpiarFormulario(fromProveedor);
										        		fromBeneficiario.hide();
										        		fromProveedor.hide();
										        	}
										        }
									        }
									        }]					
									}]
						}]
			}]
	})

	//--------------------------------------------------------------------------------------------

	fromProveedor = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:210px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 58,
			items:[comcampocatproveedordesde.fieldsetCatalogo,comcampocatproveedorhasta.fieldsetCatalogo]
	})
	
	//--------------------------------------------------------------------------------------------

	fromBeneficiario = new Ext.form.FieldSet({ 
			title:'',
			style: 'position:absolute;left:10px;top:210px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 58,
			items:[comcampocatbeneficiariodesde.fieldsetCatalogo,comcampocatbeneficiariohasta.fieldsetCatalogo]
	})

	//------------------------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(375)); 
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteResProvBen = new Ext.FormPanel({
		applyTo: 'formReporteResProvBen',
		width:750, 
		height: 350,
		title: "<H1 align='center'>Resumen Proveedores/Beneficiario</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromIntervaloFechas,
		        fromEstado,
		        fromTipo,
		        fromProveedor,
		        fromBeneficiario
		        ]
	});	
	fromBeneficiario.hide();
	fromProveedor.hide();
	fromReporteResProvBen.doLayout();
});	

//------------------------------------------------------------------------------------------------------------

function irImprimir()
{
	var valido = true;
	var tipoproben = '-';
	var codprobendes = '';
	var codprobenhas = '';
	var pagina = '';
	var orden = 'N';
	var ckbimprdet = 0;
	var formato = "sigesp_spg_rpp_resumen_prov_bene_listado.php";
    var fecdes = Ext.getCmp('dtFechaDesde').getValue().format('Y-m-d');
    var fechas = Ext.getCmp('dtFechaHasta').getValue().format('Y-m-d');
    var radio = Ext.getCmp('rdEstPre'); 
	
    if(radio.items.items[1].checked){
		orden = radio.items.items[1].inputValue;
	}
    if(Ext.getCmp('impdet').checked){
		ckbimprdet = 1;
		formato = "sigesp_spg_rpp_resumen_prov_bene_detalle.php";
	}
	if(fecdes>fechas){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por Fecha no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(Ext.getCmp('rdFormato').items.items[0].checked){
		tipoproben = 'PC';
		codprobendes = Ext.getCmp('cod_prodes').getValue();
		codprobenhas = Ext.getCmp('cod_prohas').getValue();
		if((codprobendes!='' && codprobenhas=='') || (codprobendes=='' && codprobenhas!='')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Proveedor!!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	if(Ext.getCmp('rdFormato').items.items[1].checked){
		tipoproben = 'B';
		codprobendes = Ext.getCmp('ced_benedes').getValue();
		codprobenhas = Ext.getCmp('ced_benehas').getValue();
		if((codprobendes!='' && codprobenhas=='') || (codprobendes=='' && codprobenhas!='')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Beneficiario!!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	} 
	if(Ext.getCmp('rdFormato').items.items[2].checked){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe seleccionar proveedor o beneficiario!!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
	}
	if(valido)
	{
		pagina="reportes/"+formato+"?txtcodproben="+codprobendes
	          +"&txtfecdes="+fecdes+"&rbtipo="+tipoproben+"&rborden="+orden+"&txtfechas="+fechas
	          +"&ckbimprdet="+ckbimprdet+"&txtcodprobenhas="+codprobenhas;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}
