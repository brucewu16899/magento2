Mage.Catalog_Product_ProductSelect = function(config) {
    this.gridPageSize = 30;
    this.gridUrl = '/';

    if (config && config.el) {
        this.el = config.el;
    } else {
        this.el = Ext.DomHelper.append(document.body, {tag:'div'}, true);
    }
    
    Ext.apply(this, config);
    

    this.dialog = new Ext.LayoutDialog(this.el, { 
        modal: true,
        width:600,
        height:450,
        shadow:true,
        minWidth:500,
        minHeight:350,
        autoTabs:true,
        proxyDrag:true,
        // layout config merges with the dialog config
        center:{
            tabPosition: "top",
            alwaysShowTabs: false,
        }
    });
    
    this.layout = this.dialog.getLayout();

    var innerLayout = new Ext.BorderLayout(this.layout.getEl().createChild({tag : 'div'}), {
        west: {
            initialSize: 200,
            autoScroll:true,
            split:true
        },
        center: {
            autoScroll:true
        }
    });
    innerLayout.beginUpdate();
    innerLayout.add("west", new Ext.ContentPanel(innerLayout.getEl().createChild({tag : 'div'})));
    
    /*####################### INIT GRID ####################*/ 
            var dataRecord = Ext.data.Record.create([
                {name: 'id', mapping: 'product_id'},
                {name: 'name', mapping: 'name'},
                {name: 'price', mapping: 'price'},
                {name: 'description', mapping: 'description'}
            ]);

            var dataReader = new Ext.data.JsonReader({
                root: 'items',
                totalProperty: 'totalRecords',
                id: 'product_id'
            }, dataRecord);

             var dataStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: this.gridUrl}),
                reader: dataReader,
                baseParams : {pageSize : this.gridPageSize},
                remoteSort: true
             });

            var colModel = new Ext.grid.ColumnModel([
                {header: "ID#", sortable: true, locked:false, dataIndex: 'id'},
                {header: "Name", sortable: true, dataIndex: 'name'},
                {header: "Price", sortable: true, renderer: Ext.util.Format.usMoney, dataIndex: 'price'},
                {header: "Description", sortable: false, dataIndex: 'description'}
            ]);

            this.grid = new Ext.grid.Grid(innerLayout.getRegion('center').getEl().createChild({tag : 'div'}), {
                ds: dataStore,
                cm: colModel,
                autoSizeColumns : true,
                loadMask: true,
                monitorWindowResize : true,
                autoHeight : false,
                selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
                enableColLock : false
            });
            
           innerLayout.add("center", new Ext.GridPanel(this.grid));    
           
           this.grid.render();  
           
           var gridHead = this.grid.getView().getHeaderPanel(true);
           var gridFoot = this.grid.getView().getFooterPanel(true);

           var paging = new Ext.PagingToolbar(gridHead, this.grid.getDataSource(), {
               pageSize: this.gridPageSize,
               displayInfo: true,
               displayMsg: 'Products {0} - {1} of {2}',
               emptyMsg: 'No items to display'
           });

    /*####################### END GRID ####################*/ 
    innerLayout.endUpdate(true);
    /*####################### LOAD GRID ###################*/


    this.layout.beginUpdate();            
    
    this.layout.add("center", new Ext.NestedLayoutPanel(innerLayout));
    this.layout.endUpdate();                
    
    this.dialog.addKeyListener(27, this.dialog.hide, this.dialog);
    this.dialog.setDefaultButton(this.dialog.addButton("Close", this.dialog.hide, this.dialog));
    
    Mage.Catalog_Product_ProductSelect.superclass.constructor.call(this);
};

Ext.extend(Mage.Catalog_Product_ProductSelect, Ext.util.Observable, {
    
    show : function() {
        this.dialog.show();
        this.grid.getDataSource().load();
    },
    
    hide : function() {
        this.dialog.hide();        
    }    
});
