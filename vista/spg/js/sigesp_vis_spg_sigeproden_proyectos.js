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

var plProyectos = null;
var gridDetPresupuestario = null;
barraherramienta    = true;
var Actualizar  = null;
var ruta ='../../controlador/spg/sigesp_ctr_spg_sigeproden.php';

var Campos =new Array(
		['codprosig','novacio|'],
                ['despro','novacio|'],
	        ['nroptocta','novacio|'],
	        ['fecptocta','novacio|'],
	        ['monptocta','novacio|'],
	        ['enteejecutor','novacio|'],
	        ['rifenteejecutor','novacio|'],
	        ['codmon','novacio|'],
	        ['denmon','novacio|'],
	        ['codestpro1','novacio|'],
	        ['codestpro2','novacio|'],
	        ['codestpro3','novacio|'],
	        ['codestpro4','novacio|'],
		['codestpro5','novacio|'],
                ['estcla','novacio|'],
                ['spg_cuenta','novacio|'],
                ['sc_cuentad','novacio|'],
                ['sc_cuentah','novacio|']
	    )
Ext.onReady(function()
{
    Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
        
    //-----------------------------------------------------------------------------------------------
    //creando datastore y columnmodel para la grid de los detalles presupuestarios
    var reDetPresupuestario = Ext.data.Record.create([
	    {name: 'spg_cuenta'},
	    {name: 'codestpro1'},
	    {name: 'codestpro2'},
	    {name: 'codestpro3'},
	    {name: 'codestpro4'},
	    {name: 'codestpro5'},
	    {name: 'estcla'},
            {name: 'codfuefin'},
            {name: 'codestpro'}
    ]);
	
    var dsDetPresupuestario =  new Ext.data.Store({
	reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetPresupuestario)
    });
						
    var cmDetPresupuestario = new Ext.grid.ColumnModel([
        new Ext.grid.CheckboxSelectionModel(),
        {header: "<CENTER>Programatico</CENTER>", width: 500, sortable: true, dataIndex: 'codestpro'},
        {header: "<CENTER>Cuenta</CENTER>", width: 200, align: 'center', sortable: true, dataIndex: 'spg_cuenta'}
    ]);
    //fin del datastore y columnmodel para la grid 
	
    //creando grid para los detalles 
    gridDetPresupuestario = new Ext.grid.EditorGridPanel({
 		width:650,
 		height:120,
		frame:true,
		title:"<H1 align='center'>Detalle Presupuestario</H1>",
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		style: 'position:absolute;left:10px;top:250px',
		autoScroll:true,
 		border:true,
 		ds: dsDetPresupuestario,
	   	cm: cmDetPresupuestario,
	   	stripeRows: true,
	  	viewConfig: {forceFit:true},
	  	tbar:[{
		        text:'Agregar Detalle Presupuestario',
		        tooltip:'Agregar Cuenta',
		        iconCls:'agregar',
		        id: 'btagredet',
		        handler: function()
                            {
                                AgregarPresupuesto();
                            }
	  		},
	  		{
			text:'Eliminar Detalle Presupuestario',
			tooltip:'Eliminar Detalle',
			iconCls:'remover',
			id:'btelidet',
			handler: function(){
				arreglo = gridDetPresupuestario.getSelectionModel().getSelections();
				if(arreglo.length >0)
                                {
                                    for(var i = arreglo.length - 1; i >= 0; i--)
                                    {
                                        gridDetPresupuestario.getStore().remove(arreglo[i]);
                                    }
				}
				else
                                {
                                    Ext.Msg.show({
                                            title:'Mensaje',
                                            msg: 'Debe seleccionar el registro a Eliminar!!!',
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.INFO
                                    });
				}
				
			}
		}]
    });
    
    
    //PANEL PRINCIPAL CONFIGURACION
    plProyectos = new Ext.FormPanel({
            title: "<H1 align='center'>Configuraci&#243;n Proyectos</H1>",
            style: 'position:relative;top:25px;left:100px', 
            height: 420,
            width: 700,
            applyTo:'formSigeprodenProyecto',
            frame: true,
            items:[{
                    xtype: 'hidden',
                    id: 'catalogo',
                    value:'0'
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:15px;top:10px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 130,
                            items: [{
                                    xtype:'textfield',
                                    fieldLabel:'Codigo Proyecto',
                                    autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '10'},
                                    id:'codprosig',
                                    width:100,
                                    disabled:false,
                                    labelSeparator:'',
                                    binding:true,
                                    hiddenvalue:'',
                                    defaultvalue:'',
                                    allowBlank:false,
                                    listeners:{
                                                    'blur' : function(campo)
                                                    {
                                                        valorCampo = campo.getValue();
                                                        valorCampo = rellenarCampoCerosIzquierda(valorCampo,10);
                                                        campo.setValue(valorCampo);
                                                    }
                                            }                                    
                            }]
                    }]
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:15px;top:40px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 130,
                            items: [{
                                    xtype:'textfield',
                                    fieldLabel:'Descripci&#243;n',
                                    id:'despro',
                                    width:500,
                                    autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '255'},
                                    labelSeparator:'',
                                    binding:true,
                                    hiddenvalue:'',
                                    defaultvalue:'',
                                    allowBlank:false
                            }]
                    }]
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:15px;top:70px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 130,
                            items: [{
                                    xtype:'textfield',
                                    fieldLabel:'Punto de Cuenta',
                                    id:'nroptocta',
                                    width:100,
                                    autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '25'},
                                    labelSeparator:'',
                                    binding:true,
                                    hiddenvalue:'',
                                    defaultvalue:'',
                                    allowBlank:false
                            }]
                    }]
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:270px;top:70px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 50,
                            items: [{
                                    xtype:"datefield",
                                    labelSeparator :'',
                                    fieldLabel:"Fecha",
                                    name:'Fecha',
                                    id:'fecptocta',
                                    allowBlank:false,
                                    width:100,
                                    binding:true,
                                    defaultvalue:'1900-01-01',
                                    hiddenvalue:'',
                                    value: new Date().format('d-m-Y'),
                                    autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
                            }]
                    }]
            },{
                    style:'position:absolute;left:450px;top:70px',
                    layout:"column",
                    defaults:{border: false},
                    items: [{
                            layout:"form",
                            border:false,
                            labelWidth:50,
                            items: [{
                                    xtype:'textfield',
                                    labelSeparator:'',
                                    fieldLabel:'Monto',
                                    id:'monptocta',											
                                    width: 140,
                                    autoCreate: {tag: 'input', type: 'text', size: '140', autocomplete: 'off', maxlength: '140', onkeypress: "return keyRestrict(event,'0123456789.-');"},
                                    listeners:{
                                            'blur':function(objeto)
                                            {
                                                var numero = objeto.getValue();
                                                valor = formatoNumericoMostrar(numero,2,'.',',','','','-','');
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
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:15px;top:100px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 130,
                            items: [{
                                    xtype:'textfield',
                                    fieldLabel:'Ente Ejecutor',
                                    id:'enteejecutor',
                                    width:500,
                                    autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '255'},
                                    labelSeparator:'',
                                    binding:true,
                                    hiddenvalue:'',
                                    defaultvalue:'',
                                    allowBlank:false
                            }]
                    }]
            },{
                    layout:"column",
                    border:false,
                    style: 'position:absolute;left:15px;top:130px',
                    items:[{
                            layout:"form",
                            border:false,
                            labelWidth: 130,
                            items:[{
                                    xtype: 'textfield',
                                    labelSeparator :'',
                                    fieldLabel: 'R.I.F Ente ',
                                    name: 'Rif',
                                    id: 'rifenteejecutor',
                                    width: 130,
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
                                                }
                                    }
                                }]
                        }]
            },{
                layout:"column",
                border:false,
                style: 'position:absolute;left:300px;top:130px',
                labelWidth: 150,
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
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:15px;top:160px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 130,
                            items: [{
                                    xtype:'textfield',
                                    fieldLabel:'Moneda',
                                    id:'codmon',
                                    disabled:true,	
                                    width:50,
                                    autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '3'},
                                    labelSeparator:'',
                                    binding:true,
                                    hiddenvalue:'',
                                    defaultvalue:'',
                                    allowBlank:false                                        
                            }]
                    }]
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:205px;top:160px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 130,
                            items: [{
                                    xtype:'button',
                                    iconCls: 'menubuscar',
                                    handler: function (){
                                            if(Actualizar == null)
                                            {
                                                CatalogoMonedaObjeto();
                                            }
                                        }
                            }]
                    }]
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:240px;top:160px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 5,
                            items: [{
                                    xtype:'textfield',
                                    id:'denmon',
                                    disabled:true,	
                                    width:200,
                                    autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '100'},
                                    labelSeparator:'',
                                    binding:true,
                                    hiddenvalue:'',
                                    defaultvalue:'',
                                    allowBlank:false                                        
                            }]
                    }]
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:15px;top:190px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 130,
                            items: [{
                                    xtype:'textfield',
                                    fieldLabel:'Cuenta Debe',
                                    id:'sc_cuentad',
                                    disabled:true,	
                                    width:160,
                                    autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '25'},
                                    labelSeparator:'',
                                    binding:true,
                                    hiddenvalue:'',
                                    defaultvalue:'',
                                    allowBlank:false                                        
                            }]
                    }]
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:315px;top:190px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 130,
                            items: [{
                                    xtype:'button',
                                    iconCls: 'menubuscar',
                                    handler: function (){
                                            if(Actualizar == null)
                                            {
                                                mostrarCatalogoCuentaContable('catalogocuentamovimiento',Ext.getCmp('sc_cuentad'),null);
                                            }
                                        }
                            }]
                    }]
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:350px;top:190px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 110,
                            items: [{
                                    xtype:'textfield',
                                    fieldLabel:'Cuenta Haber',
                                    id:'sc_cuentah',
                                    disabled:true,	
                                    width:160,
                                    autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '25'},
                                    labelSeparator:'',
                                    binding:true,
                                    hiddenvalue:'',
                                    defaultvalue:'',
                                    allowBlank:false                                        
                            }]
                    }]
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:630px;top:190px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 130,
                            items: [{
                                    xtype:'button',
                                    iconCls: 'menubuscar',
                                    handler: function (){
                                            if(Actualizar == null)
                                            {
                                                mostrarCatalogoCuentaContable('catalogocuentamovimiento',Ext.getCmp('sc_cuentah'),null);
                                            }
                                        }
                            }]
                    }]
            },{
                    layout: "column",
                    defaults: {border: false},
                    style: 'position:absolute;left:15px;top:220px',
                    items: [{
                            layout: "form",
                            border: false,
                            labelWidth: 130,
                            items: [{
                                    xtype:"button",
                                    id:'btnBuscarCP',
                                    text:'Ver Comprobantes Asociados',
                                    handler: function(){
                                            if(Actualizar)
                                            {
                                                verComprobantesAsociados();
                                            }
                                        }
                            }]
                    }]
            },gridDetPresupuestario]
    });
});


//INICIO DEL FORMULARIO AGREGAR PRESUPUESTO//
function AgregarPresupuesto()
{
        var fieldSetEstOrigen = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
                titform: 'Estructura Presupuestaria',
                mostrarDenominacion:true,
                sinFuente:false,
                sinCuenta:false,
                idtxt:'comfsest',
                datosocultos:0
        });

        //Creacion del formulario de agregar presupuesto
        var frmAgregarPresupuesto = new Ext.FormPanel({
                width: 870,
                height: 330, 
                style: 'position:absolute;left:5px;top:0px',
                frame: true,
                autoScroll:false,
                items:[fieldSetEstOrigen.fsEstructura]  
        });

        var ventanaAgregarPresupuesto = new Ext.Window({
                title: "<H1 align='center'>Informaci&#243;n Presupuestaria</H1>",
                width:880,
                x:10,
                height:400, 
                modal: true,
                closable:false,
                plain: false,
                frame:true,
                items:[frmAgregarPresupuesto],
                buttons: [{
                        text:'Aceptar',  
                        handler: function(){
                                var arrCodigos = fieldSetEstOrigen.obtenerArrayEstructura();
                                var estructura = fieldSetEstOrigen.obtenerEstructuraFormato();
                                if(Ext.getCmp('codcuentacomfsest').getValue()=='')
                                {
                                        Ext.Msg.show({
                                                title:'Mensaje',
                                                msg:'Debe completar todos los datos',
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.INFO
                                        });
                                }
                                else
                                {
                                    var reDetGas = Ext.data.Record.create([
                                            {name: 'spg_cuenta'},
                                            {name: 'codestpro1'},
                                            {name: 'codestpro2'},
                                            {name: 'codestpro3'},
                                            {name: 'codestpro4'},
                                            {name: 'codestpro5'},
                                            {name: 'estcla'},
                                            {name: 'codestpro'},
                                            {name: 'codfuefin'}
                                    ]);
                                    var detgasInt = new reDetGas({
                                            'spg_cuenta':arrCodigos[7],
                                            'codestpro':estructura,
                                            'codestpro1':arrCodigos[0],
                                            'codestpro2':arrCodigos[1],
                                            'codestpro3':arrCodigos[2],
                                            'codestpro4':arrCodigos[3],
                                            'codestpro5':arrCodigos[4],
                                            'estcla':arrCodigos[5],
                                            'codfuefin':arrCodigos[6]
                                    });
                                    var entro=false;
                                    if(gridDetPresupuestario.getStore().getCount()==0)
                                    {
                                        gridDetPresupuestario.store.insert(0,detgasInt);
                                    }
                                    else
                                    {
                                        gridDetPresupuestario.store.each(function (reDetGas)
                                        {
                                            if(reDetGas.get('spg_cuenta')==arrCodigos[7] && reDetGas.get('codestpro1')==arrCodigos[0] &&
                                               reDetGas.get('codestpro2')==arrCodigos[1] && reDetGas.get('codestpro3')==arrCodigos[2] &&
                                               reDetGas.get('codestpro4')==arrCodigos[3] && reDetGas.get('codestpro5')==arrCodigos[4] &&
                                               reDetGas.get('estcla')==arrCodigos[5])
                                            {
                                                Ext.Msg.show({
                                                         title:'Mensaje',
                                                         msg: 'La cuenta ya existe...',
                                                         buttons: Ext.Msg.OK,
                                                         icon: Ext.MessageBox.INFO
                                                 });
                                                entro=true;
                                            }
                                        })
                                        if(!entro)
                                        {
                                            gridDetPresupuestario.store.insert(0,detgasInt);
                                        }
                                    }
                                    ventanaAgregarPresupuesto.close();
                                }	
                        }
                },
                {
                    text: 'Salir',
                    handler:function(){
                            ventanaAgregarPresupuesto.close();
                    }
                }]
        });
        ventanaAgregarPresupuesto.show();
}
//FIN DEL FORMULARIO AGREGAR PRESUPUESTO//

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
    limpiarFormulario(plProyectos);
    gridDetPresupuestario.store.removeAll();
    Actualizar  = null;
    buscarNumero();
}

function buscarNumero()
{
    var myJSONObject ={
        "operacion":"nuevo_proyecto"
    };
    ObjSon=Ext.util.JSON.encode(myJSONObject);
    parametros = 'ObjSon='+ObjSon;
    Ext.Ajax.request({
            url : ruta,
            params : parametros,
            method: 'POST',
            success: function ( result, request) 
            { 
                datos = result.responseText;
                var codigo = eval('(' + datos + ')');
                if(codigo != "")
                {
                    Ext.getCmp('codprosig').setValue(codigo);
                }
            }	
    })
}

function irBuscar() 
{
    irNuevo();
    CatalogoProyecto('definicion');
}

function irGuardar()
{
	var strJsonProyectos = getJsonFormulario(plProyectos);
	var dataCuenta = gridDetPresupuestario.getStore(); 
	if(dataCuenta.getCount() > 0)
        {
            var arrCampos = [{etiqueta:'Cuenta', campo:'spg_cuenta', tipo:'s', requerido: true},
                             {etiqueta:'Estructura 1', campo:'codestpro1', tipo:'s', requerido: true},
                             {etiqueta:'Estructura 2', campo:'codestpro2', tipo:'s', requerido: true},
                             {etiqueta:'Estructura 3', campo:'codestpro3', tipo:'s', requerido: true},
                             {etiqueta:'Estructura 4', campo:'codestpro4', tipo:'s', requerido: true},
                             {etiqueta:'Estructura 5', campo:'codestpro5', tipo:'s', requerido: true},
                             {etiqueta:'Fuente de Financiamiento', campo:'codfuefin', tipo:'s', requerido: true},
                             {etiqueta:'Estatus', campo:'estcla', tipo:'s', requerido: true}];
            var strJsonGrid = getJsonGrid(dataCuenta, arrCampos);
            if(strJsonGrid != false)
            {
                operacion='incluir_proyecto';
                if (Actualizar)
                {
                    operacion='actualizar_proyecto';
                }
                var monto = parseFloat(ue_formato_operaciones(Ext.getCmp('monptocta').getValue()));
	        var strMonto = ",monptocta:'"+monto+"'";
                var strJson = "{'operacion':'"+operacion+"',"+strJsonProyectos+strMonto+",'arrCuenta':"+strJsonGrid+"}";
                var objJson = Ext.util.JSON.decode(strJson);
                if (typeof(objJson) == 'object')
                {
                    var parametros ='ObjSon='+strJson;
                    Ext.Ajax.request({
                            url: ruta,
                            params: parametros,
                            method: 'POST',
                            success: function ( result, request )
                            {
                                var respuesta = result.responseText;
                                var datajson = eval('(' + respuesta + ')');
                                if(datajson.raiz.valido==true)
                                {
                                    Ext.Msg.show({
                                            title:'Mensaje',
                                            msg: datajson.raiz.mensaje,
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.INFO
                                    });
                                    irNuevo();
                                }
                                else
                                {
                                    Ext.Msg.show({
                                            title:'Mensaje',
                                            msg: datajson.raiz.mensaje,
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.ERROR
                                    });
                                }
                            },
                            failure: function ( result, request)
                            { 
                                Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
                            }
                    });
    		}
            }
        }
	else
        {
            Ext.Msg.show({
                    title:'Mensaje',
                    msg: 'Debe agregar una cuenta a configurar',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.WARNING
            });
	}
}

function irEliminar() 
{
    Ext.Msg.show({
            title:'Confirmar',
                    msg: 'Desea eliminar este registro?',
                    buttons: Ext.Msg.YESNO,
                    icon: Ext.MessageBox.QUESTION,
                    fn: function(btn) {
                            if (btn == 'yes')
                            {
                                    var myJSONObject = {"operacion":"eliminar_proyecto", "codprosig":Ext.getCmp('codprosig').getValue()};
                                    var ObjSon=Ext.util.JSON.encode(myJSONObject);
                                    var parametros ='ObjSon='+ObjSon;
                                    Ext.Ajax.request({
                                            url: ruta,
                                            params: parametros,
                                            method: 'POST',
                                            success: function ( result, request ) 
                                            {
                                                var respuesta = result.responseText;
                                                var datajson = eval('(' + respuesta + ')');
                                                if(datajson.raiz.valido==true)
                                                {
                                                    Ext.Msg.show({
                                                            title:'Mensaje',
                                                            msg: datajson.raiz.mensaje,
                                                            buttons: Ext.Msg.OK,
                                                            icon: Ext.MessageBox.INFO
                                                    });
                                                    irNuevo();
                                                }
                                                else
                                                {
                                                    Ext.Msg.show({
                                                            title:'Mensaje',
                                                            msg: datajson.raiz.mensaje,
                                                            buttons: Ext.Msg.OK,
                                                            icon: Ext.MessageBox.ERROR
                                                    });
                                                }
                                            },
                                            failure: function ( result, request)
                                            { 
                                                Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
                                            }
                                    });
                            }
                    }
    });
}

function verComprobantesAsociados()
{	
    var myJSONObject = {
        "operacion" : 'comprobantes_asociados',
        "codprosig" : Ext.getCmp('codprosig').getValue()
    };
        
    var ObjSon= JSON.stringify(myJSONObject);
    var parametros ='ObjSon='+ObjSon;
    Ext.Ajax.request({
            url: ruta,
            params: parametros,
            method: 'POST',
            success: function ( resultado, request ) 
            { 
                var datosest = resultado.responseText;
                if(datosest!='')
                {
                    var DatosEst = eval('(' + datosest + ')');
                }
                            
                //creando datastore y columnmodel para la grid de los detalles contables
                var reDetalles = Ext.data.Record.create([
                        {name: 'comprobante'},
                        {name: 'descripcion'},
                        {name: 'fecha'},
                        {name: 'monto'}

                ]);

                dsDetalles =  new Ext.data.Store({
                        reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetalles)
                });

                var cmDetalles = new Ext.grid.ColumnModel([
                        {header: "<CENTER>Comprobante</CENTER>", width: 100, align: 'center', sortable: true, dataIndex: 'comprobante'},
                        {header: "<CENTER>Descripcion</CENTER>", type: 'float', width: 200, align: 'left', sortable: true, dataIndex: 'descripcion'},
                        {header: "<CENTER>Fecha</CENTER>", type: 'float', width: 60, align: 'right', sortable: true, dataIndex: 'fecha'},
                        {header: "<CENTER>Monto</CENTER>", type: 'float', width: 60, align: 'right', sortable: true, dataIndex: 'monto'}
                ]);
                //fin del datastore y columnmodel para la grid de bienes

                //creando grid para los detalles de bienes
                gridDetalles = new Ext.grid.EditorGridPanel({
                        width:686,
                        height:420,
                        frame:true,
                        title:"",
                        sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
                        style: 'position:absolute;left: 7px;top:5px',
                        autoScroll:true,
                        border:true,
                        ds: dsDetalles,
                        cm: cmDetalles,
                        stripeRows: true,
                        viewConfig: {forceFit:true}
                });
                
                dsDetalles.loadData(DatosEst);
                
                var ventanaComprobantes = new Ext.Window({
                        title: "<H1 align='center'>Comprobantes Asociados</H1>",
                        y:10,
                        width:700,
                        height:500, 
                        modal: true,
                        closable:false,
                        plain: false,
                        frame:true,
                        items:[gridDetalles],
                        buttons: [{
                                    text: 'Salir',
                                    handler:function(){
                                            ventanaComprobantes.close();
                                    }
                                 }]
                        });
                    ventanaComprobantes.show();
                }
        });
}
