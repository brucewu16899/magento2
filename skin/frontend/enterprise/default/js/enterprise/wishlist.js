/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    design
 * @package     enterprise_default
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

if (!window.Enterprise) {
    window.Enterprise = {};
}

if (!Enterprise.Wishlist) {
    Enterprise.Wishlist = {
        Widget: {
            Form: {}
        }
    };
}

Enterprise.Wishlist.Widget.Form = Class.create(Enterprise.Widget, {
    action: null,
    isValid: false,

    initialize: function($super, action) {
        var _templateString = '<ul class="form-list">' +
                '<li><label for="wishlist-name">' + Translator.translate('Wishlist Name') + '</label><div class="input-box"><input type="text" id="wishlist-name" maxlength="255" class="input-text required-entry validate-length maximum-length-255" name="name"/></div>' +
                '<li class="control"><div class="input-box"><input type="checkbox" id="wishlist-public" name="visibility"></div><label for="wishlist-public">' + Translator.translate('Make this wishlist public') + '</label></li>' +
            '</ul>' +
            '<div class="buttons-set form-buttons"><button type="submit" class="button btn-save"><span><span>' + Translator.translate('Save') + '</span></span></button><button type="button" class="button btn-cancel"><span><span>' + Translator.translate('Cancel') + '</span></span></button></div>';

        this.action = action;
        $super(new Element('form', {'method': 'post', 'action': action}));
        this._node.update(_templateString);
        var validation = new Validation(this._node, {onFormValidate: (function(result){this.isValid = result}).bind(this)});
        Event.observe(this._node, 'submit', (function(event){this.onSubmit(event);}).bind(this));
        Event.observe($(this._node).down('button.btn-cancel'), 'click', (function(){this.onCancel()}).bind(this));
        this.nameNode = $(this._node).down('#wishlist-name');
        this.visibilityNode = $(this._node).down('#wishlist-public');
    },

    onSubmit: function(event) {
    },

    onCancel: function() {

    },

    setName: function(name) {
        this.nameNode.value = name;
    },

    setIsVisible: function(state) {
        this.visibilityNode.checked = !!state;
    }
});


Enterprise.Wishlist.Widget.Form.Create = Class.create(Enterprise.Wishlist.Widget.Form, {
    onSubmit: function(event) {
        Event.stop(event);
        if (!this.isValid) {
            return;
        }
        var callback = (function(wishlistId){this.onWishlistCreated(wishlistId)}).bind(this);
        new Ajax.Request(this.action, {
            method: 'post',
            parameters: $(this._node).serialize(),
            onSuccess: function(response) {
                try {
                    var data = response.responseJSON;
                    if (typeof data.wishlist_id != 'undefined') {
                        callback(data.wishlist_id);
                    } else if (typeof data.redirect != 'undefined') {
                        setLocation(data.redirect);
                    } else {
                        alert(Translator.translate('Error happened while creating wishlist. Please try again later'));
                    }
                } catch (e) {
                    setLocation(window.location.href);
                }
            }
        });
    },

    onWishlistCreated: function(wishlistId) {

    }
});

Enterprise.Wishlist.createWithCallback = function(createUrl, callback) {
    if (!Enterprise.Wishlist.createWithCallbackDialog) {
        var createWithCallbackForm = new Enterprise.Wishlist.Widget.Form.Create(createUrl);
        Enterprise.Wishlist.createWithCallbackDialog = new Enterprise.Widget.Dialog(
            Translator.translate('Create New Wishlist'),
            createWithCallbackForm.getNode()
        );
        Enterprise.Wishlist.createWithCallbackDialog.form = createWithCallbackForm;
        createWithCallbackForm.onCancel = Enterprise.Wishlist.createWithCallbackDialog.hide.bind(Enterprise.Wishlist.createWithCallbackDialog);
        Enterprise.Wishlist.createWithCallbackDialog.form.onSubmit = Enterprise.Wishlist.createWithCallbackDialog.form.onSubmit.wrap(function(proceed, event) {
            proceed(event);
            if (this.isValid) {
                Enterprise.Wishlist.createWithCallbackDialog.setBusy(true);
            }
        })
    }
    Enterprise.Wishlist.createWithCallbackDialog.form.onWishlistCreated = callback;
    Enterprise.Wishlist.createWithCallbackDialog.show();
}

Enterprise.Wishlist.create = function(createUrl, callback) {
    if (!Enterprise.Wishlist.createDialog) {
        var createForm = new Enterprise.Wishlist.Widget.Form(createUrl);
        Enterprise.Wishlist.createDialog = new Enterprise.Widget.Dialog(
            Translator.translate('Create New Wishlist'),
            createForm.getNode()
        );
        createForm.onCancel = Enterprise.Wishlist.createDialog.hide.bind(Enterprise.Wishlist.createDialog);
    }
    Enterprise.Wishlist.createDialog.show();
}

Enterprise.Wishlist.edit = function(editUrl, wishlistName, visibility) {
    if (!Enterprise.Wishlist.editDialog) {
        var editForm = new Enterprise.Wishlist.Widget.Form(editUrl);
        Enterprise.Wishlist.editDialog = new Enterprise.Widget.Dialog(
            Translator.translate('Edit Wishlist'),
            editForm.getNode()
        );
        Enterprise.Wishlist.editDialog.form = editForm;
        editForm.onCancel = Enterprise.Wishlist.editDialog.hide.bind(Enterprise.Wishlist.editDialog);
    }
    Enterprise.Wishlist.editDialog.form.setName(wishlistName);
    Enterprise.Wishlist.editDialog.form.setIsVisible(visibility);
    Enterprise.Wishlist.editDialog.show();
}

Enterprise.Wishlist.copyItemTo = function(itemId, wishlistId) {
    var url = Enterprise.Wishlist.url.copyItem;
    var form = new Element('form', {method: 'post', action: url.gsub('%wishlist_id%', wishlistId)});
    form.insert(new Element('input', {name: 'item_id', type: 'hidden', value: itemId}));
    $(document.body).insert(form);
    form.submit();
};

Enterprise.Wishlist.moveItemTo = function(itemId, wishlistId) {
    var url = Enterprise.Wishlist.url.moveItem;
    var form = new Element('form', {method: 'post', action: url.gsub('%wishlist_id%', wishlistId)});
    form.insert(new Element('input', {name: 'item_id', type: 'hidden', value: itemId}));
    $(document.body).insert(form);
    form.submit();
    return false;
};

Enterprise.Wishlist.copySelectedTo = function(wishlistId) {
    if (!this.itemsSelected()) {
        alert(Translator.translate('You must select items to copy'));
        return;
    }
    var url = Enterprise.Wishlist.url.copySelected;
    this.form.action = url.gsub('%wishlist_id%', wishlistId);
    this.form.submit();
};

Enterprise.Wishlist.moveSelectedTo = function(wishlistId) {
    if (!this.itemsSelected()) {
        alert(Translator.translate('You must select items to move'));
        return;
    }
    var url = Enterprise.Wishlist.url.moveSelected;
    this.form.action = url.gsub('%wishlist_id%', wishlistId);
    this.form.submit();
};

Enterprise.Wishlist.itemsSelected = function() {
    var selected = false;
    $(this.form).select('input.select').each(function(item) {
        if ($(item).checked) {
            selected = true;
        }
    });
    return selected;
};

Enterprise.Wishlist.copyItemToNew = function(itemId) {
    this.createWithCallback(Enterprise.Wishlist.url.create, this.copyItemTo.bind(this, itemId));
};

Enterprise.Wishlist.moveItemToNew = function(itemId) {
    this.createWithCallback(Enterprise.Wishlist.url.create, this.moveItemTo.bind(this, itemId));
};

Enterprise.Wishlist.moveSelectedToNew = function() {
    if (!this.itemsSelected()) {
        alert(Translator.translate('You must select items to move'));
        return;
    }
    this.createWithCallback(Enterprise.Wishlist.url.create, this.moveSelectedTo.bind(this));
};

Enterprise.Wishlist.copySelectedToNew = function() {
    if (!this.itemsSelected()) {
        alert(Translator.translate('You must select items to copy'));
        return;
    }
    this.createWithCallback(Enterprise.Wishlist.url.create, this.copySelectedTo.bind(this));
};

Event.observe(document, 'dom:loaded', function() {
    if (typeof Enterprise.Wishlist.list != 'undefined'
        && (Enterprise.Wishlist.list.length || Enterprise.Wishlist.canCreate)) {
        var buildUrl = function(url, wishlistId) {
            var glue = url .charAt( url.length-1 ) == '/' ? '' : '/';
            return url + glue + 'wishlist_id/' + wishlistId + '/';
        }
        $$('.link-wishlist').each(function(link) {
            var url = link.href;
            var onclick = link.onclick || function() {
                setLocation(this.href);
            }

            var wishlistSplitButton = new Enterprise.Widget.SplitButton(link.innerHTML, Translator.translate('Add to:'), 'light clickable');
            wishlistSplitButton.onClick = onclick.bind({href: url});

            Enterprise.Wishlist.list.each(function(wishlist) {
                var option = new Enterprise.Widget.SplitButton.Option(wishlist.name);
                option.onClick = onclick.bind({href: buildUrl(url, wishlist.id)});
                wishlistSplitButton.addOption(option);
            });

            if (Enterprise.Wishlist.canCreate) {
                var option = new Enterprise.Widget.SplitButton.Option(Translator.translate('Create New Wishlist'), 'new');
                option.onClick = Enterprise.Wishlist.createWithCallback.bind(this, Enterprise.Wishlist.url.create, function(wishlistId) {
                    (onclick.bind({
                        href: buildUrl(url, wishlistId)
                    }))();
                });
                wishlistSplitButton.addOption(option);
            }

            wishlistSplitButton.placeAt(link.up());
            link.remove();
        });
    }
});

