Mage.Catalog_Product = function(depend){
    var dep = depend;
    return {
        ds : null,
        grid : null,
        searchPanel : null,
        editPanel : null,
        formLoading: null,
        loadedForms : new Ext.util.MixedCollection(),
        activeFilters : new Ext.util.MixedCollection(true),
        filterButtons : new Ext.util.MixedCollection(),
        filterSettings : null,
        editablePanels : [],
        categoryEditFormPanel : null, // panel for category from
        productLayout : null,
        parentProductLayut : null,
        gridPanel : null,
        productsGrid : null,
        productsGridPageSize : 30, 
        registeredForms : new Ext.util.MixedCollection(),
        newItemDialog : null,
        


        init: function(){
            dep.init();
        },

        initLayouts : function() {
                var Layout = dep.getLayout('main');

                var Layout_Center = new Ext.BorderLayout( Ext.DomHelper.append(Layout.getEl(), {tag:'div'}, true), {
                     center:{
                         titlebar: true,
                         autoScroll:true,
                         resizeTabs : true,
                         hideTabs : true,
                         tabPosition: 'top'
                     }
                 });

                 this.productLayout = new Ext.BorderLayout(Layout.getEl().createChild({tag:'div'}), {
                     north : {
                        hideWhenEmpty : true,
                        titlebar:false,
                        split:true,
                        initialSize:21,
                        minSize:21,
                        maxSize:21,
                        autoScroll:true,
                        collapsible:true
                     },
                     center:{
                         titlebar: false,
                         autoScroll:true,
                         resizeTabs : true,
                         hideTabs : false,
                         tabPosition: 'top'
                     },
                     south : {
                         hideWhenEmpty : true,
                         split:true,
                         initialSize:300,
                         minSize:50,
                         titlebar: true,
                         autoScroll: true,
                         collapsible: true,
                         hideTabs : true
                     }
                 });

                Layout_Center.beginUpdate();
                this.parentProductLayut = Layout_Center.add('center', new Ext.NestedLayoutPanel(this.productLayout, {title:'Cat Name'}));
                Layout_Center.endUpdate();

                Layout.add('center', new Ext.NestedLayoutPanel(Layout_Center, {title : 'Products Grid'}));
        },

        initGrid: function(catId) {
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
                proxy: new Ext.data.HttpProxy({url: Mage.url + '/mage_catalog/product/gridData/category/' + catId + '/'}),
                reader: dataReader,
                remoteSort: true
             });
             
             dataStore.on('load', function(){
                 if (!this.filterSettings) {
                     this.loadFilterSettings();
                 }
             }.createDelegate(this));

            dataStore.setDefaultSort('product_id', 'desc');


            var colModel = new Ext.grid.ColumnModel([
                {header: "ID#", sortable: true, locked:false, dataIndex: 'id'},
                {header: "Name", sortable: true, dataIndex: 'name'},
                {header: "Price", sortable: true, renderer: Ext.util.Format.usMoney, dataIndex: 'price'},
                {header: "Description", sortable: false, dataIndex: 'description'}
            ]);

            this.productsGrid = new Ext.grid.Grid(this.productLayout.getEl().createChild({tag: 'div'}), {
                ds: dataStore,
                cm: colModel,
                autoSizeColumns : true,
                loadMask: true,
                monitorWindowResize : true,
                autoHeight : true,
                selModel : new Ext.grid.RowSelectionModel({singleSelect : true}),
                enableColLock : false
            });
            
            this.productsGrid.mageCategoryId = catId;
            
            this.productsGrid.on('rowclick', this.createItem.createDelegate(this));

            this.productsGrid.render();

            var gridHead = this.productsGrid.getView().getHeaderPanel(true);
            var gridFoot = this.productsGrid.getView().getFooterPanel(true);

            var paging = new Ext.PagingToolbar(gridHead, dataStore, {
                pageSize: this.productsGridPageSize,
                displayInfo: true,
                displayMsg: 'Displaying products {0} - {1} of {2}',
                emptyMsg: 'No products to display'
            });

            paging.add('-', {
                text: 'Product',
                cls: 'x-btn-text-icon bedit_add',
                handler : this.createItem.createDelegate(this)
            });
            
            var btnAdd = new Ext.Toolbar.Button({
                text: 'Filter',
                handler : this.addFilter.createDelegate(this),
                cls: 'x-btn-text-icon bedit_add',
                disabled : true
             });
             paging.add(btnAdd);
             this.filterButtons.add('add', btnAdd);
            
             var btnApply = new Ext.Toolbar.Button({
                text: 'Apply',
                handler : this.applyFilters.createDelegate(this),
                cls: 'x-btn-text-icon bapply',
                disabled : true
             });
             paging.add(btnApply);            
             this.filterButtons.add('apply', btnApply);
             
             var bntReset = new Ext.Toolbar.Button({
                text: 'Reset',
                handler : this.deleteFilters.createDelegate(this),
                cls: 'x-btn-text-icon bedit_delete',
                disabled : true
             });
             paging.add(bntReset);            
             this.filterButtons.add('clear', bntReset);
        },

        addFilter : function(node, e) {
            
            if (!this.filterSettings) {
                Ext.MessageBox.alert('Filters','Filters Settings Not Loaded');
                return false;
            }
            
            var workZoneCenterPanel = null;
            if (!(workZoneCenterPanel = this.productLayout.getRegion('north').getPanel('filters_panel'))) {
                workZoneCenterPanel = this.productLayout.add('north', new Ext.ContentPanel('filters_panel', {autoCreate: true, title:'Filters', closable : true}));
            }

            var filter = new Ext.Toolbar(workZoneCenterPanel.getEl().insertFirst({tag: 'div', id:'filter-'+Ext.id()}));

            filter.add({
                handler : this.delFilter.createDelegate(this, [filter], 0),
                cls: 'x-btn-icon bedit_remove'
            });
            
            var startType = null;
            var startComp = null;
            var compData = {};  // information about fieldName compare types
            var options = [];
            var i = 0;
            for (i=0; i < this.filterSettings.getCount(); i++) {
                var record = this.filterSettings.getAt(i);                
                if (i == 0) {
                    startComp = record.data.filterComp;
                    startType = record.data.filterType;
                }
                
                compData[record.data.filterField] = record.data.filterComp;
                
                options.push(
       			  {tag: 'option', value:record.data.filterField, html:record.data.filterName, ftype:record.data.filterType}
                )
            }
            var type = filter.addDom({tag:'select', name:'filterField', id : filter.getEl().id + '-filterField', children: options});
            
            var options = []
            for (i=0; i < startComp.length; i++) {
                options.push(
                    {tag: 'option', value:startComp[i].v, html:startComp[i].n}
                );
            }
            var comp = filter.addDom({tag:'select', name:'filterType', id : filter.getEl().id + '-filterType', children: options});

                
            Ext.EventManager.addListener(type.getEl(), 'change', function(filter, type, compEl, compData){
                var selectedValue = type.getEl().value;
               
                while (compEl.options.length > 0) {
                    compEl.options[0] = null;
                }

                var i=0;
                for (i=0; i < compData[selectedValue].length; i++) {
                    opt = compData[selectedValue][i];
                    compEl.options[compEl.options.length] = new Option(opt.n, opt.v);
                }
               
               var sType = type.getEl().options[type.getEl().options.selectedIndex].getAttribute('ftype');
               var lastItem = filter.items.itemAt(filter.items.getCount()-1);
               filter.items.remove(lastItem);
               lastItem.destroy();               
               var textValue = this.createFilterTextValue(filter.getEl().id, sType);
               filter.addField(textValue);
            }.createDelegate(this, [filter, type, comp.getEl(), compData], 0));
            
            var textValue = this.createFilterTextValue(filter.getEl().id, startComp);
            filter.addField(textValue);
            
            this.activeFilters.add(Ext.id(), filter);


            this.updateSizeFilterPanel();
            return true;
        },

        createFilterTextValue : function(filterId, sType) {
               switch (sType) {
                   case 'date' :
                        var textValue = new Ext.form.DateField({
                            id : filterId + '-textValue',
                            allowBlank:true        	    
                        });
                       break;
                   case 'text' : 
                   default :
                        var textValue = new Ext.form.TextField({
                            id : filterId + '-textValue',
                            grow : true,
                            width : 135,
                            growMin : 135,
                            growMax : 600,
                            allowBlank:true        	    
                        });
               }
            return textValue;   
        },

        loadFilterSettings : function() {
            var dataRecord = Ext.data.Record.create([
                {name: 'filterId', mapping: 'filter_id'},
                {name: 'filterField', mapping: 'filter_field'},
                {name: 'filterName', mapping: 'filter_name'},
                {name: 'filterType', mapping: 'filter_type'},
                {name: 'filterComp', mapping: 'filter_comp'},
            ]);

            var dataReader = new Ext.data.JsonReader({
                root: 'filters',
                totalProperty: 'totalRecords',
                id: 'filter_id'
            }, dataRecord);

             this.filterSettings = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({url: Mage.url + '/mage_catalog/product/filtersettings/', method: 'POST'}),
                reader: dataReader,
                remoteSort: true
             });
             
             this.filterSettings.on('load', function(){
                 var i=0;
                 for(i=0; i < this.filterButtons.getCount(); i++) {
                     var btn = this.filterButtons.itemAt(i);
                     btn.enable();
                 }
             }.createDelegate(this));

             this.filterSettings.load();
        },


        updateSizeFilterPanel : function() {
            if (!this.productLayout.getRegion('north')) {
                return false;
            }
            if (!(workZoneCenterPanel = this.productLayout.getRegion('north').getPanel('filters_panel'))) {
                return false;
            }

            var titleHeight = this.productLayout.getRegion('north').titleEl.getHeight();
            var filters = workZoneCenterPanel.getEl().dom.childNodes;
            var i = 0;
            var height = titleHeight; // magic number - titlebar size;
            if (filters.length == 0) {
                 this.productLayout.getRegion('north').remove(workZoneCenterPanel);    
            }
            for (i = 0; i < filters.length; i++) {
                var el = Ext.get(filters[i]);
                height = height + el.getHeight();
            }
            
            this.productLayout.getRegion('north').resizeTo(height+1);
            this.productLayout.getRegion('north').config.maxSize = height+1;
//            if (this.productLayout.getRegion('north').config.maxSize) {
//                if (height+1 > this.productLayout.getRegion('north').config.maxSize) {
//                    this.productLayout.getRegion('north').resizeTo(this.productLayout.getRegion('north').config.maxSize);
//                } else {
//                    this.productLayout.getRegion('north').resizeTo(height+1);                                       
//                }
//            } else {
//                this.productLayout.getRegion('north').resizeTo(height+1);                    
//            }
            return true;
        },

        applyFilters : function() {
            var i = 0;
            var k = 0;
            var filter = [];
            for (i=0; i < this.activeFilters.getCount(); i++) {
                var toolbar = this.activeFilters.itemAt(i);
                filter[i] = {};
                filter[i].index = i;
                filter[i].data = {};
                filter[i].data['textValue'] = Ext.ComponentMgr.get(toolbar.getEl().id + '-textValue').getValue();
                for (k=0; k< toolbar.items.getCount(); k++) {
                    if (toolbar.items.itemAt(k).getEl().name == 'filterType' || toolbar.items.itemAt(k).getEl().name == 'filterField') {
                            filter[i].data[toolbar.items.itemAt(k).getEl().name] = toolbar.items.itemAt(k).getEl().value;
                    }
                }
            }
            this.productsGrid.getDataSource().load({params : {start:0, limit:this.productsGridPageSize, filters : Ext.encode(filter)}});
        },
        
        deleteFilters : function() {
            var panel = this.productLayout.getRegion('north').getPanel('filters_panel');
            if (panel) {
                this.productLayout.getRegion('north').remove(panel);
                this.productsGrid.getDataSource().load({params : {start:0, limit:this.productsGridPageSize}});                
            }
        },
        

        delFilter : function(filter) {
            for(var i=0; i< filter.items.getCount(); i++) {
                var item = filter.items.itemAt(i);
                filter.items.remove(item);
                if (item.destroy) {
                    filter.items.get(i).destroy();
                }
            }
            filter.getEl().removeAllListeners();
            filter.getEl().remove();
            this.activeFilters.remove(filter);            
            this.updateSizeFilterPanel();
        },
    
        /**
         *  @param : load  boolean (load grid data)
         *  @param : catId  integer (category id required)
         *  @param : catTitle string (category title required)
         */
        viewGrid : function (config) {
            if (!config.catId) {
                return false;
            }
            this.init();
            if (!this.productLayout) {
                this.initLayouts();
            }
            //this.parentProductLayut.setTitle(treeNode.text);
            if (!this.gridPanel) {
                this.initGrid(config.catId);
                this.productLayout.beginUpdate();
                this.gridPanel = this.productLayout.add('center', new Ext.GridPanel(this.productsGrid, {title: config.catTitle}));
                this.productLayout.endUpdate();
                if (config.load) {
                    this.productsGrid.getDataSource().load({params:{start:0, limit:this.productsGridPageSize}});
                }
            } else {
                this.gridPanel.getGrid();
                this.productsGrid.getDataSource().proxy.getConnection().url = Mage.url + '/mage_catalog/product/gridData/category/' + config.catId + '/';
                if (config.load && this.productsGrid.mageCategoryId != config.catId) {
                    this.productsGrid.getDataSource().load({params:{start:0, limit:this.productsGridPageSize}});
                }
                this.productsGrid.mageCategoryId = config.catId;                
            }
        },


        setUpNewItem : function(menuItem, e) {
            if(!this.newItemDialog){ // lazy initialize the dialog and only create it once
                this.newItemDialog = new Ext.BasicDialog(Ext.DomHelper.append(document.body, {tag: 'div'}, true), {
                        title : 'Test',
                        autoTabs:true,
                        width:300,
                        height:200,
                        modal:false,
                        shadow:true,
                        minWidth:300,
                        minHeight:200,
                        proxyDrag: true
                });
                var sbmt = this.newItemDialog.addButton('Ok', submit, this);
                sbmt.disable();
                this.newItemDialog.addButton('Cancel', this.newItemDialog.hide, this.newItemDialog);
                var mgr = new Ext.UpdateManager(this.newItemDialog.body);
                mgr.on('update', function(){sbmt.enable()});
                //this.newItemDialog.on('show', function(){mgr.update(Mage.url + '/mage_catalog/product/newoption/')})
                mgr.update(Mage.url + '/mage_catalog/product/newoption/');
            }
            this.newItemDialog.show(menuItem.getEl().dom);

            var dialog = this.newItemDialog;

            function submit() {
                var set = Ext.DomQuery.selectNode('select#choose_attribute_set', dialog.body.dom);
                var type = Ext.DomQuery.selectNode('select#choose_product_type', dialog.body.dom);
                if (set) {
                    var setId = set.value;
                }
                if (type) {
                    var typeId = type.value;
                }
                this.newItemDialog.hide();
                this.doCreateItem.createDelegate(this, [-1, 'yes', setId, typeId], 0)();
            }
        },

        createItem: function() {
            var rowId = null;
            var menuItem = null;
            var e = null;

            switch (arguments.length) {
                case 2 :
                   menuItem = arguments[0];
                   e = arguments[1];
                   rowId = 0;
                   if (this.editablePanels.length) {
                        Ext.MessageBox.confirm('Product Card', 'You have unsaved product. Do you whant continue ?', this.setUpNewItem.createDelegate(this, [menuItem, e], 0));
                   } else {
                        this.setUpNewItem(menuItem, e);
                        return true;
                   }
                   break;
                case 3 :
                   rowId = arguments[1];
                   e = arguments[2];
                   if (this.editablePanels.length) {
                        Ext.MessageBox.confirm('Product Card', 'You have unsaved product. Do you whant continue ?', this.doCreateItem.createDelegate(this, [rowId], 0));
                   } else {
                        if (rowId >= 0) {
                            try {
                                  prodId = this.productsGrid.getDataSource().getAt(rowId).id;
                                  //title = 'Edit: ' + this.productsGrid.getDataSource().getById(prodId).get('name');
                            } catch (e) {
                              Ext.MessageBox.alert('Error!', e.getMessage());
                            }
                        }
                        this.doCreateItem(prodId, 'yes');
                        return true;
                   }
                   break;
                default :
                    return false;
            };
        },


        doCreateItem: function(prodId, btn, setId, typeId) {
            setId = Number(setId);
            typeId = Number(typeId);
            if (btn == 'no') {
                return false;
            }
            var title = 'New Product'; // title for from layout

            if (this.productLayout.getRegion('south').getActivePanel()) {
                this.productLayout.getRegion('south').clearPanels();
                this.editablePanels = [];
            }

            this.editPanel = new Ext.BorderLayout(this.productLayout.getEl().createChild({tag:'div'}), {
                    hideOnLayout:true,
                    north: {
                        split:false,
                        initialSize:28,
                        minSize:28,
                        maxSize:28,
                        autoScroll:false,
                        titlebar:false,
                        collapsible:false
                     },
                     center:{
                         autoScroll:true,
                         titlebar:false,
                         resizeTabs : true,
                         tabPosition: 'top'
                     }
            });

            this.editPanel.add('north', new Ext.ContentPanel(this.productLayout.getEl().createChild({tag:'div'})));

            this.productLayout.beginUpdate();
            var failure = function(o) {Ext.MessageBox.alert('Product Card',o.statusText);}
            var cb = {
                success : this.loadTabs.createDelegate(this),
                failure : failure,
                argument : {prod_id: prodId}
            };
            var con = new Ext.lib.Ajax.request('GET', Mage.url + '/mage_catalog/product/card/product/'+prodId+'/setid/'+setId+'/typeid/'+typeId+'/', cb);

            this.productLayout.add('south', new Ext.NestedLayoutPanel(this.editPanel, {closable: true, title:title}));
            this.productLayout.getRegion('south').on('panelremoved', this.onRemovePanel.createDelegate(this));
            this.productLayout.endUpdate();
        },

        onRemovePanel : function() {
            this.editablePanels = [];
            this.registeredForms.clear();
            this.productLayout.getRegion('south').clearPanels();
            //this.productLayout.getRegion('south').hide();
            return true;
        },


        onResetForm : function() {
            var i;
            for (i=0; i < this.editablePanels.length; i++) {
                var panel = this.editPanel.getRegion('center').getPanel(this.editablePanels[i]);
                if (form = Ext.DomQuery.selectNode('form', panel.getEl().dom)) {
                    form.reset();
                }
                panel.setTitle(panel.getTitle().substr(0,panel.getTitle().length-1));
            }
            this.editablePanels = [];
            return true;
        },

        onCancelEdit : function() {
            if (this.editablePanels.length) {
                Ext.MessageBox.confirm('Product Card', 'You have unsaved product. Do you whant continue ?', this.onRemovePanel.createDelegate(this));
            } else {
               this.onRemovePanel();
            }
            return true;
        },

        // submit form to server
        saveItem : function() {
            var region = this.editPanel.getRegion('center');
            var i = 0;
            var k = 0;
            var panel = null;
            var form = null;

            for(i = 0; i< region.panels.length; i++) {
                panel = region.panels.get(i);
                dq = Ext.DomQuery;
                form = null;

                if (panel.loaded) {
                   formEl = dq.selectNode('form', panel.getEl().dom);
                   if (form = this.registeredForms.get(formEl.action)) {
                       form.appendForm(formEl);
                   } else {
                       form = new Mage.Form(formEl);
                       this.registeredForms.add(form.action, form);
                   }
                }
            }

            form = null;
            for(i=0; i < this.registeredForms.getCount(); i++) {
                form = this.registeredForms.itemAt(i);
                form.sendForm(this.saveItemCallBack.createDelegate(this));
            }
        },

        saveItemCallBack : function(response, type) {
            Ext.dump(response.responseText);
        },


        loadTabs: function(response) {
            if (!this.editPanel) {
                return false;
            }
            // decode responce from server - there is information about form tabs
            dataCard = Ext.decode(response.responseText);

            // begin update editPanel
            this.editPanel.beginUpdate();

            // setup toolbar for forms
            var toolbar = new Ext.Toolbar(Ext.DomHelper.insertFirst(this.editPanel.getRegion('north').getEl().dom, {tag:'div'}, true));
            toolbar.add({
                text: 'Save',
                cls: 'x-btn-text-icon',
                handler : this.saveItem.createDelegate(this)
            },{
                text: 'Delete',
                cls: 'x-btn-text-icon'
            },{
                text: 'Reset',
                cls: 'x-btn-text-icon',
                handler : this.onResetForm.createDelegate(this)
            },{
                text: 'Cancel',
                cls: 'x-btn-text-icon',
                handler : this.onCancelEdit.createDelegate(this)
            },'-');
            this.formLoading = toolbar.addButton({
               tooltip: 'Form is updating',
               cls: "x-btn-icon x-grid-loading",
               disabled: false
            });
            toolbar.addSeparator();

           // start draw panels and setup it to get infration from server
           var panel = null;
            for(var i=0; i < dataCard.tabs.length; i++) {
               var panel = this.createTabPanel(dataCard.tabs[i]);
               if (panel) {
                   var mgr = panel.getUpdateManager();
                   mgr.on('update', this.onLoadPanel.createDelegate(this, [panel], true));
                   this.editPanel.add('center', panel);
               }
            }

            for(var i=0; i < dataCard.tabs.length; i++) {
                if (dataCard.tabs[i].active) {
                    this.editPanel.getRegion('center').showPanel('productCard_' + dataCard.tabs[i].name);
                }
            }
            this.editPanel.endUpdate();
            // end update editPanel
            return true;
        },

        createTabPanel: function(tabInfo){
            var panel = null;
            if (tabInfo.type){
                // Relatet, bundle and super products panels
            }
            else{
                panel = new Ext.ContentPanel('productCard_' + tabInfo.name,{
                    title : tabInfo.title,
                    autoCreate: true,
                    closable : false,
                    url: tabInfo.url,
                    loadOnce: true,
                    background: true
                });
            }
            return panel;
        },

        // set up form in panel - call after tab is updated by Ext.UpdateManager
        onLoadPanel : function(el, response) {
             var i=0;
            // we can ignore panel.loaded - because next step set it to ture version Ext alpha 3r4
            panel = this.editPanel.getRegion('center').getPanel(el.id);
            if (!panel) {
                return false;
            }
            date = [];
            if (form = Ext.DomQuery.selectNode('form', panel.getEl().dom))  {
                var el;
                for(i=0; i < form.elements.length; i++) {
                    if (form.elements[i]) {
                        Ext.EventManager.addListener(form.elements[i], 'change', this.onFormChange.createDelegate(this, [panel], true));
                    }
                }
                this.loadedForms.add(form.id, form);
            }
        },



        onFormChange : function(e, element, object, panel) {
            var i = 0;
            for(i=0; i<this.editablePanels.length; i++) {
                if (this.editablePanels[i] == panel.getId()) {
                    e.stopEvent();
                    return true;
                }
            }
            this.editablePanels.push(panel.getId());
            panel.setTitle(panel.getTitle() + '*');
            e.stopEvent();
        },

        loadCategoryEditForm : function(treeNode) {
            if (!this.categoryEditFormPanel) {
                var workZone = dep.getLayout('main');
                workZone.beginUpdate();
                this.categoryEditFormPanel = workZone.add('center', new Ext.ContentPanel('', {autoCreate: true, url:Mage.url+'/mage_catalog/category/new', title: 'Edit: ' + treeNode.text, background:true}));
                workZone.endUpdate();
            } else {
                this.categoryEditFormPanel.setTitle('Edit: ' + treeNode.text);
            }
        },



        cancelNew: function() {

        }
    }
}(Mage.Catalog);


