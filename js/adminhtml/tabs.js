var varienTabs = new Class.create();

varienTabs.prototype = {
    initialize : function(containerId, destElementId,  activeTabId){
        this.containerId    = containerId;
        this.destElementId  = destElementId;
        this.activeTab = null;
        
        this.tabOnClick     = this.tabMouseClick.bindAsEventListener(this);
        
        this.tabs = $$('#'+this.containerId+' li a');
        
        this.hideAllTabsContent();
        for(var tab in this.tabs){
            Event.observe(this.tabs[tab],'click',this.tabOnClick);
            // move tab contents to destination element
            if($(this.destElementId)){
                var tabContentElement = $(this.getTabContentElementId(this.tabs[tab]));
                if(tabContentElement && tabContentElement.parentNode.id != this.destElementId){
                    $(this.destElementId).appendChild(tabContentElement);
                }
            }
        }
        this.showTabContent($(activeTabId));
    },
    
    getTabContentElementId : function(tab){
        return tab.id+'_content';
    },
    
    tabMouseClick : function(event){
        var tab = Event.findElement(event, 'a');
        this.hideAllTabsContent();
        
        if(tab.href.indexOf('#') != tab.href.length-1){
            alert(tab.href);
        }
        else {
            this.showTabContent(tab);
        }
        
        Event.stop(event);
    },
    
    hideAllTabsContent : function(){
        for(var tab in this.tabs){
            this.hideTabContent(this.tabs[tab]);
        }
    },
    
    showTabContent : function(tab){
        var tabContentElement = $(this.getTabContentElementId(tab));
        if(tabContentElement){
            Element.show(tabContentElement);
            Element.addClassName(tab, 'active');
            this.activeTab = tab;
        }
    },
    
    hideTabContent : function(tab){
        var tabContentElement = $(this.getTabContentElementId(tab));
        if($(this.destElementId) && tabContentElement){
           Element.hide(tabContentElement);
           Element.removeClassName(tab, 'active');
        }
    }
}