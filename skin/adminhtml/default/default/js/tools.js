/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
function setLocation(url){
    window.location.href = url;
}

function confirmSetLocation(message, url){
    if( confirm(message) ) {
        setLocation(url);
    }
    return false;
}

function deleteConfirm(message, url) {
    confirmSetLocation(message, url);
}

function setElementDisable(element, disable){
    if($(element)){
        $(element).disabled = disable;
    }
}

function toggleParentVis(obj) {
    obj = $(obj).parentNode;
    if( obj.style.display == 'none' ) {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
}

function toggleVis(obj) {
    obj = $(obj);
    if( obj.style.display == 'none' ) {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
}

function imagePreview(element){
    if($(element)){
        var win = window.open('', 'preview', 'width=400,height=400,resizable=1,scrollbars=1');
        win.document.open();
        win.document.write('<body style="padding:0;margin:0"><img src="'+$(element).src+'" id="image_preview"/></body>');
        win.document.close();
        Event.observe(win, 'load', function(){
            var img = win.document.getElementById('image_preview');
            win.resizeTo(img.width+40, img.height+80)
        });
    }
}

function toggleValueElements(checkbox, container){
    if(container && checkbox){
        //var elems = container.getElementsBySelector('select', 'input');
        var elems = Element.getElementsBySelector(container, ['select', 'input', 'textarea', 'button']);
        elems.each(function (elem) {
            if(elem!=checkbox) {
                elem.disabled=checkbox.checked;
                if (checkbox.checked) {
                    elem.addClassName('disabled');
                } else {
                    elem.removeClassName('disabled');
                }
            };
        })
    }
}

/**
 * @todo add validation for fields
 */
function submitAndReloadArea(area, url) {
    if($(area)) {
        var fields = $(area).getElementsBySelector('input', 'select', 'textarea');
        var data = Form.serializeElements(fields, true);
        new Ajax.Request(url, {
            parameters: $H(data),
            loaderArea: area,
            onSuccess: function(transport) {
                try {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON()
                        if (response.error) {
                            alert(response.message);
                        }
                    } else {
                        $(area).update(transport.responseText);
                    }
                }
                catch (e) {
                    $(area).update(transport.responseText);
                }
            }
        });
    }
}

/********** MESSAGES ***********/
/*
Event.observe(window, 'load', function() {
    $$('.messages .error-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E13422', endcolor:'#fdf9f8', duration:1});
    });
    $$('.messages .warning-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E13422', endcolor:'#fdf9f8', duration:1});
    });
    $$('.messages .notice-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E5B82C', endcolor:'#fbf7e9', duration:1});
    });
    $$('.messages .success-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#507477', endcolor:'#f2fafb', duration:1});
    });
});
*/
function syncOnchangeValue(baseElem, distElem){
    var compare = {baseElem:baseElem, distElem:distElem}
    Event.observe(baseElem, 'change', function(){
        if($(this.baseElem) && $(this.distElem)){
            $(this.distElem).value = $(this.baseElem).value;
        }
    }.bind(compare));
}

/********** Ajax session expiration ***********/


if (!navigator.appVersion.match('MSIE 6.')) {
    Event.observe(window, 'load', function() {
        header = $$('.content-header')[0];
        header_offset = Position.cumulativeOffset(header)[1];
        header_copy = header.cloneNode(true);
        document.body.appendChild(header_copy);
        $(header_copy).addClassName('content-header-floating');
    });

    Event.observe(window, 'scroll', function () {
        var s;
        // scrolling offset calculation via www.quirksmode.org
        if (self.pageYOffset){
            s = self.pageYOffset;
        }else if (document.documentElement && document.documentElement.scrollTop) {
            s = document.documentElement.scrollTop;
        }else if (document.body) {
            s = document.body.scrollTop;
        }
        if (s > header_offset) {
            header.style.visibility = 'hidden';
            header_copy.style.display = 'block';
        } else {
            header.style.visibility = 'visible';
            header_copy.style.display = 'none';
        }
    });
}