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

var fromReporteEjePro = null; //variable para almacenar la instacia de objeto de formulario
barraherramienta = true;
var proyecto = "";
var proyectoProc ="";
var fecha = new Date();
var anio = fecha.getFullYear();
Ext.onReady(function() 
{
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//-------------------------------------------------------------------------------------
	
	var botbuscarProyecto = new Ext.Button({
		id: 'botbusquedaHasta',
		iconCls: 'menubuscar',
		style:'position:absolute;left:350px;top:15px',
		listeners:{
	        'click' : function(boton){
	        	CatalogoProyecto('reporte');
	       }
	    }
	});	

	//-------------------------------------------------------------------------------------

	var	fromIntervaloFechas = new  Ext.form.FieldSet({
			title:'Intervalo de Fechas',
			style: 'position:absolute;left:10px;top:100px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 63,
			items:[{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:25px;top:15px',
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
									value: '01/01/'+anio,
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:350px;top:15px',
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
	
	var	fromProyecto = new Ext.form.FieldSet({ 
			title:'Proyecto',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 70,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:10px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 35,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: '',
									id: 'codprosig',
									disabled:true,
									width: 140,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:165px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 30,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: '',
									id: 'despro',
									disabled:true,
									width: 140,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
								}]
							}]
					},botbuscarProyecto
                                    ,{
                                        xtype: 'hidden',
                                        name: 'codestpro1',
                                        id: 'codestpro1',
                                        binding:true,
                                        defaultvalue:'',
                                        allowBlank:false
                                    },{
                                        xtype: 'hidden',
                                        name: 'codestpro2',
                                        id: 'codestpro2',
                                        binding:true,
                                        defaultvalue:'',
                                        allowBlank:false
                                    },{
                                        xtype: 'hidden',
                                        name: 'codestpro3',
                                        id: 'codestpro3',
                                        binding:true,
                                        defaultvalue:'',
                                        allowBlank:false
                                    },{
                                        xtype: 'hidden',
                                        name: 'codestpro4',
                                        id: 'codestpro4',
                                        binding:true,
                                        defaultvalue:'',
                                        allowBlank:false
                                    },{
                                        xtype: 'hidden',
                                        name: 'codestpro5',
                                        id: 'codestpro5',
                                        binding:true,
                                        defaultvalue:'',
                                        allowBlank:false
                                    },{
                                        xtype: 'hidden',
                                        name: 'estcla',
                                        id: 'estcla',
                                        binding:true,
                                        defaultvalue:'',
                                        allowBlank:false
                                    },{
                                        xtype: 'hidden',
                                        name: 'codfuefin',
                                        id: 'codfuefin',
                                        binding:true,
                                        defaultvalue:'',
                                        allowBlank:false
                                    }]
	})

	//------------------------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(300)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteEjePro = new Ext.FormPanel({
		applyTo: 'formReporteEjecucionProyectos',
		width:600, //700
		height: 220,
		title: "<H1 align='center'>Ejecuci&#243;n de Proyectos</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  //'position:absolute;margin-left:'+Xpos+'px;margin-top:25px;', 
		items: [fromProyecto,
		        fromIntervaloFechas
		        ]
	});	
	fromReporteEjePro.doLayout();
});	

function irImprimir()
{
    var fecdes = Ext.getCmp('dtFechaDesde').getValue().format('Y-m-d');
    var fechas = Ext.getCmp('dtFechaHasta').getValue().format('Y-m-d');
	
    if(fecdes>fechas)
    {
        Ext.Msg.show({
                title:'Mensaje',
                msg: 'El Rango de Busqueda por Fecha no es correcto !!!',
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
        });
    }
    else
    {
        codprosig = Ext.getCmp('codprosig').getValue();
        if(codprosig!="")
        {
            codestpro1 = Ext.getCmp('codestpro1').getValue();
            codestpro2 = Ext.getCmp('codestpro2').getValue();
            codestpro3 = Ext.getCmp('codestpro3').getValue();
            codestpro4 = Ext.getCmp('codestpro4').getValue();
            codestpro5 = Ext.getCmp('codestpro5').getValue();
            estcla = Ext.getCmp('estcla').getValue();
            codfuefin = Ext.getCmp('codfuefin').getValue();
            despro = Ext.getCmp('despro').getValue();
            var datos = "?codestpro1="+codestpro1+"&codestpro2="+codestpro2+"&codestpro3="+codestpro3+"&codestpro4="+codestpro4+"&codestpro5="+codestpro5+
                        "&estcla="+estcla+"&codfuefin="+codfuefin+"&txtfecdes="+fecdes+"&txtfechas="+fechas+"&rborden=F&despro="+despro;
            formato = "sigesp_spg_rpp_sigeproden_ejecucion_proyectos.php";
            var pagina = "reportes/"+formato+datos;
            window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
        }
        else
        {
            Ext.Msg.show({
                    title:'Mensaje',
                    msg: 'Por Favor Seleccione un Proyecto.... !!!',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
            });
        }
    }
}
