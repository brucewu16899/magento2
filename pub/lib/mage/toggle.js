/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    "use strict";

    $.widget("mage.advancedToggle", {

        options: {
            baseToggleClass: "active"      // Class used to be toggled on clicked element
        },

        /**
         * Toggle creation
         * @private
         */
        _create: function() {
            this.beforeCreate();
            this._bindCore();
            this.afterCreate();
        },

        /**
         *  Core bound events & setup
         * @protected
         */
        _bindCore: function() {
            var widget = this;
            this.element.on('click', $.proxy(function(e) {
                widget._onClick();
                e.preventDefault();
            }, this));
        },

        /**
         * Binding Click event
         *
         * @protected
         */
        _onClick: function() {
            this._prepareOptions();
            this._toggleSelectors();
        },

        /**
         * Method used to look for data attributes to override default options
         *
         * @protected
         */
        _prepareOptions: function() {
            this.options.baseToggleClass = (this.element.data('base-toggle-class')) ?
                this.element.data('base-toggle-class') :this.options.baseToggleClass;
        },

        /**
         * Method responsible for hiding and revealing specified DOM elements
         * Toggle the class on clicked element
         *
         * @protected
         */
        _toggleSelectors: function () {
            this.element.toggleClass(this.options.baseToggleClass);
        },

        /**
         * Method used to inject 3rd party functionality before create
         * @public
         */
        beforeCreate: function() {},

        /**
         * Method used to inject 3rd party functionality after create
         * @public
         */
        afterCreate: function() {}
    });

    // Extension for mage.toggle - Adding selectors support for other DOM elements we wish to toggle
    $.widget('mage.toggle', $.mage.toggle, {

        options: {
            selectorsToggleClass: "hidden"    // Class used to be toggled on selectors DOM elements
        },

        /**
         * Method responsible for hiding and revealing specified DOM elements
         * If data-toggle-selectors attribute is present - toggle will be done on these selectors
         * Otherwise we toggle the class on clicked element
         *
         * @protected
         * @override
         */
        _toggleSelectors: function () {
            this._super();
            if (this.element.data('toggle-selectors')) {
                $(this.element.data('toggle-selectors')).toggleClass(this.options.selectorsToggleClass);
            } else {
                this.element.toggleClass(this.options.baseToggleClass);
            }
        },

        /**
         * Method used to look for data attributes to override default options
         *
         * @protected
         * @override
         */
        _prepareOptions: function() {
            this.options.selectorsToggleClass = (this.element.data('selectors-toggle-class')) ?
                this.element.data('selectors-toggle-class') :this.options.selectorsToggleClass;
            this._super();
        }
    });

    // Extension for mage.toggle - Adding label toggle
    $.widget('mage.toggle', $.mage.toggle, {

        options: {
            newLabel: null,             // Text of the new label to be used on toggle
            curLabel: null,             // Text of the old label to be used on toggle
            currentLabelElement: null   // Current label container
        },

        /**
         * Binding Click event
         *
         * @protected
         * @override
         */
        _onClick: function() {
            this._super();
            this._toggleLabel();
        },

        /**
         * Method responsible for replacing clicked element labels
         * @protected
         */
        _toggleLabel: function() {
            if (this.options.newLabel) {
                var cachedLabel = this.options.newLabel,
                    currentLabelSelector = (this.options.currentLabelElement) ?
                        $(this.options.currentLabelElement) : this.element;

                this.element.data('toggle-label', this.options.curLabel);
                currentLabelSelector.html(this.options.newLabel);

                this.options.curLabel = this.options.newLabel;
                this.options.newLabel = cachedLabel;
            }
        },

        /**
         * Method used to look for data attributes to override default options
         *
         * @protected
         * @override
         */
        _prepareOptions: function() {
            this.options.newLabel = (this.element.data('toggle-label')) ?
                this.element.data('toggle-label') : this.options.newLabel;

            this.options.currentLabelElement = (this.element.data('current-label-el')) ?
                this.element.data('current-label-el') : this.options.currentLabelElement;

            if(!this.options.currentLabelElement) {
                this.options.currentLabelElement = this.element;
            }

            this.options.curLabel = $(this.options.currentLabelElement).html();

            this._super();
        }
    });

})(jQuery);
