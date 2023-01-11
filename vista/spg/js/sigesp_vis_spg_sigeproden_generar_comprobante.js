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
barraherramienta    = true;
var Actualizar  = null;
var ruta ='../../controlador/spg/sigesp_ctr_spg_sigeproden.php';
var prefijocmp ='';
var dsPrefijo = '';
 
var Campos =new Array(
		['codprosig','novacio|'],
                ['despro','novacio|'],
	        ['procede','novacio|'],
	        ['comprobante','novacio|'],
	        ['descripcion','novacio|'],
	        ['fecha','novacio|'],
	        ['operaciones','novacio|'],
	        ['tipo_destino','novacio|'],
	        ['cod_pro','novacio|'],
	        ['nompro','novacio|'],
                ['monto','novacio|'],
                ['codestpro1','novacio|'],
                ['codestpro2','novacio|'],
                ['codestpro3','novacio|'],
                ['codestpro4','novacio|'],
                ['codestpro5','novacio|'],
                ['estcla','novacio|'],
                ['spg_cuenta','novacio|'],
                ['codfuefin','novacio|'],
                ['codmon','novacio|'],
                ['tascam','novacio|'],
	    )
Ext.onReady(function()
{
 
    Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
    
    //-------------------------------------------------------------------------------------------------------------------------	
    //Datos de las operaciones
    var operaciones = [['COMPROMISO SIMPLE', 'CS']];

    var stOperaciones = new Ext.data.SimpleStore({
            fields : [ 'col', 'tipo' ],
            data : operaciones 
    });

    //creando objeto combo destino
    var cmbOperaciones = new Ext.form.ComboBox({
            store : stOperaciones,
            fieldLabel : 'Operaci&#243;n ',
            labelSeparator : '',
            displayField : 'col',
            valueField : 'tipo',
            id : 'operaciones',
            forceselection:true,
            typeAhead: true,
            mode:'local',
            binding:true,
            editable : false,
            triggerAction:'all',
            defaultvalue:'CS',
            emptyText:'---Seleccione---',
            width:200,
            listWidth: 200            
    });
    
    //-------------------------------------------------------------------------------------------------------------------------	
    //Creacion del combo prefijo
    var rePrefijo = Ext.data.Record.create([
      {name: 'prefijo'}
    ]);

    dsPrefijo =  new Ext.data.Store({
            reader: new Ext.data.JsonReader({root: 'raiz',id: "prefijo"},rePrefijo)			
    });

    CmbPrefijo = new Ext.form.ComboBox({
            store: dsPrefijo,
            labelSeparator :'',
            fieldLabel:' Comprobante',
            displayField:'prefijo',
            valueField:'prefijo',
            name: 'prefijo',
            width:80,
            listWidth: 80, 
            id:'prefijo',
            typeAhead: true,
            binding:true,
            defaultvalue:'---',
            emptyText:'Prefijo',
            allowBlank:true,
            selectOnFocus:true,
            mode:'local',
            triggerAction:'all',
            valor:'',
            listeners: {'select': function()
                            {
                                Ext.getCmp('prefijo').setValue(prefijocmp);
                            }
            }
    });
    //Fin combo prefijo
    
    //-------------------------------------------------------------------------------------------------------------------------	
    //creando store para el combo destino
    var destino = [
        ['Proveedor','P'],
            ['Beneficiario','B']
    ]; 

    var stdestino = new Ext.data.SimpleStore({
            fields : [ 'etiqueta', 'valor' ],
            data : destino
    });
    //fin creando store para el combo destino 

    //creando objeto combo destino
    var cmbdestino = new Ext.form.ComboBox({
            store : stdestino,
            fieldLabel : 'Destino ',
            name : 'Destino ',
            labelSeparator : '',
            editable : false,
            displayField : 'etiqueta',
            valueField : 'valor',
            id : 'tipo_destino',
            binding:true,
            hiddenvalue:'',
            defaultvalue:'-',
            allowBlank:true,
            width:90,
            typeAhead: true,
            emptyText:'Seleccione',
            triggerAction:'all',
            forceselection:true,
            binding:true,
            mode:'local',
            listeners: {
                    'select': function(valor){
                            if(valor.getValue()=="P") {
                                    comcampocatproveedor.mostrarVentana();
                            }
                            else{
                                    comcampocatbeneficiario.mostrarVentana();
                            }
                    }
            }
    });
    
    //componente catalogo de proveedores
    var reCatProveedor = Ext.data.Record.create([
            {name: 'cod_pro'}, //campo obligatorio                             
            {name: 'nompro'},  //campo obligatorio
            {name: 'dirpro'},  //campo obligatorio
            {name: 'rifpro'}   //campo obligatorio
    ]);

    var comcampocatproveedor = new com.sigesp.vista.comCatalogoProveedor({
            idComponente:'spgprouno',
            reCatalogo: reCatProveedor,
            rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
            parametros: "ObjSon={'operacion': 'buscarProveedores'",
            soloCatalogo: true,
            arrSetCampo:[{campo:'cod_pro',valor:'cod_pro'},
                         {campo:'nompro',valor:'nompro'}],
            numFiltroNoVacio: 1
    });

    //-------------------------------------------------------------------------------------------------------------------------	

    //componente catalogo de beneficiarios
    var reCatBeneficiario = Ext.data.Record.create([
            {name: 'ced_bene'}, //campo obligatorio                             
            {name: 'nombene'},  //campo obligatorio
            {name: 'apebene'},  //campo obligatorio
            {name: 'dirbene'}   //campo obligatorio
    ]);

    var comcampocatbeneficiario = new com.sigesp.vista.comCatalogoBeneficiario({
            idComponente:'spgbenuno',
            reCatalogo: reCatBeneficiario,
            rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatbeneficiario.php',
            parametros: "ObjSon={'operacion': 'buscarBeneficiarios'",
            soloCatalogo: true,
            arrSetCampo:[{campo:'cod_pro',valor:'ced_bene'},
                         {campo:'nompro',valor:'nombene'}],
            numFiltroNoVacio: 1
    });
    
    //PANEL PRINCIPAL CONFIGURACION
    plProyectos = new Ext.FormPanel({
            title: "<H1 align='center'>Generar Comprobante</H1>",
            style: 'position:relative;top:25px;left:100px', 
            height: 350,
            width: 700,
            applyTo:'formulario',
            frame: true,
            items:[{
                    xtype:"fieldset", 
                    title:'Informaci&#243;n del Proyecto',
                    border:true,
                    width: 685,
                    height: 120,
                    cls: 'fondo',
                    items:[{
                            layout:"column",
                            defaults:{border: false},
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:100,
                                    items: [{
                                            xtype:'textfield',
                                            fieldLabel:'Proyecto',
                                            name:'Proyecto',
                                            id:'codprosig',
                                            disabled:true,	
                                            width:100,
                                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '10'},
                                            labelSeparator:'',
                                            binding:true,
                                            hiddenvalue:'',
                                            defaultvalue:'',
                                            allowBlank:false                                        
                                    }]
                            }]},{
                            layout: "column",
                            defaults: {border: false},
                            style: 'position:absolute;left:220px;top:23px',
                            items: [{
                                    layout: "form",
                                    border: false,
                                    labelWidth: 170,
                                    items: [{
                                            xtype:'button',
                                            iconCls: 'menubuscar',
                                            handler: function (){
                                                        CatalogoProyecto('objeto');
                                                }
                                    }]
                            }]},{
                            layout:"column",
                            defaults:{border: false},
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:100,
                                    items: [{
                                            xtype:'textfield',
                                            fieldLabel:'Descripci&#243;n',
                                            id:'despro',
                                            disabled:true,	
                                            width:558,
                                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '255'},
                                            labelSeparator:'',
                                            binding:true,
                                            hiddenvalue:'',
                                            defaultvalue:'',
                                            allowBlank:false                                        
                                    }]
                            }]},{
                            layout:"column",
                            defaults:{border: false},
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:100,
                                    items: [{
                                            xtype:'textfield',
                                            fieldLabel:'Moneda',
                                            id:'denmon',
                                            disabled:true,	
                                            width:150,
                                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '150'},
                                            labelSeparator:'',
                                            binding:true,
                                            hiddenvalue:'',
                                            defaultvalue:'',
                                            allowBlank:false                                        
                                    }]
                            }]},{
                            layout:"column",
                            defaults:{border: false},
                            style: 'position:absolute;left:390px;top:75px',
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:80,
                                    items: [{
                                            xtype:'textfield',
                                            labelSeparator:'',
                                            fieldLabel:'Tasa Cambio',
                                            name:'Tasa',											
                                            id:'tascam',											
                                            width: 200,
                                            disabled:true,
                                            autoCreate: {tag: 'input', type: 'text', size: '140', autocomplete: 'off', maxlength: '140', onkeypress: "return keyRestrict(event,'0123456789.-');"}
                                    }]
                            }]},{
                                xtype: 'hidden',
                                name: 'codmon',
                                id: 'codmon',
                                binding:true,
                                defaultvalue:'',
                                allowBlank:false
                            },{
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
                                name: 'spg_cuenta',
                                id: 'spg_cuenta',
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
                            } 
                        ]
                    },{
                    xtype:"fieldset", 
                    title:'Informaci&#243;n del Comprobante',
                    border:true,
                    width: 685,
                    height: 150,
                    cls: 'fondo',
                    items:[{
                            layout:"column",
                            defaults:{border: false},
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:100,
                                    items: [{
                                            xtype: 'textfield',
                                            labelSeparator :'',
                                            fieldLabel: 'Procedencia',
                                            id: 'procede',
                                            value: 'SPGCMP',
                                            readOnly: true,
                                            allowBlank:false,
                                            width:80,
                                            binding:true,
                                            defaultvalue:'',
                                            hiddenvalue:''
                                    }]
                            }]},{
                            layout: "column",
                            defaults: {border: false},
                            style: 'position:absolute;left:210px;top:152px',
                            items: [{
                                    layout: "form",
                                    border: false,
                                    labelWidth: 50,
                                    items: [{
                                            xtype:"datefield",
                                            labelSeparator :'',
                                            fieldLabel:"Fecha",
                                            name:'Fecha',
                                            id:'fecha',
                                            allowBlank:false,
                                            width:100,
                                            binding:true,
                                            defaultvalue:'1900-01-01',
                                            hiddenvalue:'',
                                            value: new Date().format('d-m-Y'),
                                            autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"},
                                            listeners: {'blur': function()
                                                            {
                                                                 buscarTasaCambio();
                                                            }
                                            }                                            
                                    }]
                            }]},{
                            layout:"column",
                            defaults:{border: false},
                            style: 'position:absolute;left:390px;top:152px',
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:80,
                                    items: [cmbOperaciones]
                            }]},{
                            layout:"column",
                            defaults:{border: false},
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:100,
                                    items: [CmbPrefijo]
                            }]},{
                            layout:"column",
                            defaults:{border: false},
                            style: 'position:absolute;left:120px;top:178px',
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:80,
                                    items: [{
                                            xtype: 'textfield',
                                            labelSeparator :'',
                                            fieldLabel: '',
                                            name: 'Comprobante',
                                            id: 'comprobante',
                                            autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-._');"},
                                            width: 150,
                                            formatonumerico:false,
                                            binding:true,
                                            hiddenvalue:'',
                                            defaultvalue:'',
                                            allowBlank:false,
                                            listeners:{
                                                    'blur' : function(campo)
                                                    {
                                                            llenarCampoNumdoc(campo.getValue());
                                                    }
                                            }
                                    }]
                            }]},{
                            layout:"column",
                            defaults:{border: false},
                            style: 'position:absolute;left:390px;top:178px',
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:80,
                                    items: [{
                                            xtype:'textfield',
                                            labelSeparator:'',
                                            fieldLabel:'Monto',
                                            name:'Monto',											
                                            id:'monto',											
                                            width: 200,
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
                            }]},{
                            layout:"column",
                            defaults:{border: false},
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:100,
                                    items: [{
                                            xtype:'textfield',
                                            fieldLabel:'Descripci&#243;n',
                                            name:'Descripcion',
                                            id:'descripcion',
                                            disabled:false,	
                                            width:558,
                                            autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '255'},
                                            labelSeparator:'',
                                            binding:true,
                                            hiddenvalue:'',
                                            defaultvalue:'',
                                            allowBlank:false                                        
                                    }]
                            }]},{
                            layout:"column",
                            defaults:{border: false},
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:100,
                                    items: [cmbdestino]
                            }]},{
                            layout:"column",
                            defaults:{border: false},
                            style: 'position:absolute;left:210px;top:231px',
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:10,
                                    items: [{
                                            xtype: 'textfield',
                                            fieldLabel: '',
                                            labelSeparator :'',
                                            id: 'cod_pro',
                                            disabled:true,
                                            binding:true,
                                            hiddenvalue:'',
                                            defaultvalue:'----------',
                                            allowBlank:true,
                                            width: 130
                                    }]
                            }]},{
                            layout:"column",
                            defaults:{border: false},
                            style: 'position:absolute;left:350px;top:231px',
                            items: [{
                                    layout:"form",
                                    border:false,
                                    labelWidth:10,
                                    items: [{
                                            xtype: 'textfield',
                                            fieldLabel: '',
                                            labelSeparator :'',
                                            id: 'nompro',
                                            disabled:true,
                                            binding:true,
                                            hiddenvalue:'',
                                            defaultvalue:'Ninguno',
                                            allowBlank:true,
                                            width: 310
                                    }]
                            }]}                    
                        ]
                    }
                
        ]
    });
    llenarCmbPrefijos();
});
    
//Funcion que agrega los datos al combo prefijos
function llenarCmbPrefijos()
{
    var myJSONObject ={
                    "operacion": 'buscarPrefijosUsuarios'
    };	
    var ObjSon=JSON.stringify(myJSONObject);
    var parametros = 'ObjSon='+ObjSon; 
    Ext.Ajax.request({
            url : '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
            params : parametros,
            method: 'POST',
            success: function (resultado, request)
            { 
                    var datosest = resultado.responseText;
                    var prefijo = "";
                    if(datosest!='')
                    {
                            prefijo = datosest.substring(21, 27);
                            var DatosEst = eval('(' + datosest + ')');
                    }
                    //dsPrefijo.loadData(DatosEst);                        
                    Ext.getCmp('prefijo').setValue(prefijo);
                    NroComprobante(prefijo);
            }//fin del success
    });//fin del ajax request
}
    
//Funcion que agrega los datos al combo prefijos
function buscarTasaCambio()
{
    var myJSONObject ={
                    "operacion": 'obtener_tasacambio',
                    "codmon": Ext.getCmp('codmon').getValue(),
                    "fecha": Ext.getCmp('fecha').getValue().format('Y-m-d')
    };	
    var ObjSon=JSON.stringify(myJSONObject);
    var parametros = 'ObjSon='+ObjSon; 
    Ext.Ajax.request({
            url : ruta,
            params : parametros,
            method: 'POST',
            success: function (resultado, request)
            { 
                    var datosest = resultado.responseText;
                    if(datosest!='')
                    {
                        numero = eval(datosest);
                        numero = formatoNumericoMostrar(numero,8,'.',',','','','-','');
                        Ext.getCmp('tascam').setValue(numero);
                    }
            }//fin del success
    });//fin del ajax request
}

//Funcion que para buscar el consecutivo nrocomprobante
function NroComprobante(prefijo)
{
    if(!tbadministrativo)
    {
        var myJSONObject = {
                "operacion" :'verificar_prefijo',
                "procede" : 'SPGCMP'	
        };
        var ObjSon= JSON.stringify(myJSONObject);
        var parametros ='ObjSon='+ObjSon;
        Ext.Ajax.request({
                url: '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
                params: parametros,
                method: 'POST',
                success: function ( result, request )
                { 
                var prefijo = result.responseText;
                if((prefijo == "1")&&(!tbadministrativo))
                        {
                                Ext.getCmp('comprobante').setDisabled(true);
                        }
                else
                        {
                                Ext.getCmp('comprobante').setDisabled(false);
                }
                }
        });
    }

    var myJSONObject = {
            "operacion" :'cargar_nrodocumento',
            "procede" :'SPGCMP',
            "prefijo" : prefijo                        
    };
    var ObjSon= JSON.stringify(myJSONObject);
    var parametros ='ObjSon='+ObjSon;
    Ext.Ajax.request({
            url: '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
            params: parametros,
            method: 'POST',
            success: function ( result, request )
            { 
                var numdoc = result.responseText;
                if(numdoc == "-2")
                        {
                        Ext.Msg.show({
                                title:'Mensaje',
                                msg: 'El sistema tiene configurado el uso de prefijo y este usuario no tiene uno asignado !!!',
                                buttons: Ext.Msg.OK,
                                fn: function(){ location.href = 'sigesp_vis_spg_inicio.html'},
                                icon: Ext.MessageBox.INFO
                        });
                }
                else if (numdoc != "-1")
                {
                    Ext.getCmp('comprobante').setValue(numdoc);
                }
            }
    });
}
    
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
            url: '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
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

function irCancelar()
{
    limpiarFormulario(plProyectos);

}

function irProcesar()
{
    if(validarObjetos2()==false)
    {
    	return false;
    }
    else
    {	var strJsonProyectos = getJsonFormulario(plProyectos);
        var monto = parseFloat(ue_formato_operaciones(Ext.getCmp('monto').getValue()));
        var strMonto = ",monto:'"+monto+"'";
        var tascam = parseFloat(ue_formato_operaciones(Ext.getCmp('tascam').getValue()));
        var strTascam = ",tascam:'"+tascam+"'";
        operacion='generar_comprobante';
        var strJson = "{'operacion':'"+operacion+"',"+strJsonProyectos+strMonto+strTascam+"}";
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
                            irCancelar();
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