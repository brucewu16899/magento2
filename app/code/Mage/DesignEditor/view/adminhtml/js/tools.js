/**
 * {license_notice}
 *
 * @category    design
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    'use strict';

    $.widget("mage.translateInlineToggle", {
        _create: function() {
            this.element.on('click', $.proxy(this._onClick, this));
        },

        /**
        * This method displays the translate menu.
        */
        _onClick: function () {
            parent.jQuery('[translate-menu]').toggleClass('hidden');
        }
    });

    $.widget("mage.translateInlineToggleMode", {
        options: {
            frameUrl: null,
            refreshVdeCanvas: false,
            disableInlineTranslation: false
        },

        _create: function() {
            this.element.on('click', $.proxy(this._onClick, this));
        },

        /**
        * This method will only enable editing for the translation mode specified.
        *
        * If this is the first time a mode is selected, the contents of the iframe will be wrapped with the appropriate
        * attributes where applicable (translate-mode, either 'text', 'script' or 'alt').
        *
        */
        _onClick: function () {
            // Hide menu.
            parent.jQuery('[translate-menu]').toggleClass('hidden');

            // Change menu to reflect what was selected, so will display correctly when displayed again.
            this._updateMenu(this.element.attr('translate-selected'));

            // Refresh iframe with new url
            this._refresh(this.element.attr('translate-selected'));
        },

        /**
        * This method refreshes the icons, background and toolbar button based on the currently selected menu option.
        *
        * @param mode
        * @private
        */
        _refresh: function (mode) {
            /** todo SDW Add animation when refreshing the iframe. */

            var url = this.options.frameUrl;
            if (this.options.disableInlineTranslation)
                parent.jQuery('#vde_container_frame').attr('data-translation-mode-selected', '');
            else {
                url = url + "translation_mode/" + mode;
                parent.jQuery('#vde_container_frame').attr('data-translation-mode-selected', mode);
            }

            // If this is the first time selecting a mode, refresh the iframe to wrap all the applicable content.
            // Or, if disabling inline translation, refresh minus the translation mode on the url.
            if (this.options.refreshVdeCanvas || this.options.disableInlineTranslation)
                parent.jQuery('#vde_container_frame').prop('src', url);

            /**
            * Since the url is being modified to support inline translation, the window is not reloaded since it
            * is using the url from the cache to display.
            */
        },

        /**
        * This method updates the menu's current status.
        *
        * @param mode
        * @private
        */
        _updateMenu: function (mode) {
            this.options.disableInlineTranslation = false;

            var TEXT_MENU_BACKGROUND_ON = 'text-menu-background-on';
            var TEXT_MENU_BACKGROUND_OFF = 'text-menu-background-off';

            var possibleModes = ["text", "script", "alt"];
            var menuClass = '#vde-translate-' + mode;
            var textEditClass = 'text-edit-' + mode;
            var textMenuClass = 'text-menu-' + mode;

            // Check to see if turning off (selecting the already highlighted option).
            if (parent.jQuery(menuClass).hasClass(TEXT_MENU_BACKGROUND_ON)) {
                parent.jQuery(menuClass).removeClass(TEXT_MENU_BACKGROUND_ON).addClass(TEXT_MENU_BACKGROUND_OFF);
                parent.jQuery(menuClass + '-img').removeClass(textMenuClass + '-on').addClass(textMenuClass + '-off');

                // Update toolbar button.
                parent.jQuery('[vde-translate-edit]').removeClass(textEditClass + '-on').addClass(textEditClass + '-off');

                // Refresh iframe minus the translation mode on the url.
                this.options.disableInlineTranslation = true;
            }
            else {
                // Enable selected option
                this._toggleSelected(mode, true, TEXT_MENU_BACKGROUND_OFF, TEXT_MENU_BACKGROUND_ON, textMenuClass + '-off', textMenuClass + '-on');

                // Disable other two options.
                for (var i = 0; i < possibleModes.length; i++) {
                    if (possibleModes[i] != mode)
                        this._toggleSelected(possibleModes[i], false, TEXT_MENU_BACKGROUND_ON, TEXT_MENU_BACKGROUND_OFF, 'text-menu-' + possibleModes[i] + '-on', 'text-menu-' + possibleModes[i] + '-off');
                }
            }
        },

        /**
        * This method changes the classes of the menu option backgrounds and image and the toolbar link.
        *
        * @param mode
        * @param enableOnToolbar
        * @param backgroundRemove
        * @param backgroundAdd
        * @param imageRemove
        * @param imageAdd
        * @private
        */
        _toggleSelected: function (mode, enableOnToolbar, backgroundRemove, backgroundAdd, imageRemove, imageAdd) {
            var menuClass = '#vde-translate-' + mode;
            parent.jQuery(menuClass).removeClass(backgroundRemove).addClass(backgroundAdd);
            parent.jQuery(menuClass + '-img').removeClass(imageRemove).addClass(imageAdd);

            // Update toolbar button.
            var textEditClassOn = 'text-edit-' + mode + '-on';
            if (enableOnToolbar)
                parent.jQuery('[vde-translate-edit]').addClass(textEditClassOn);
            else
                parent.jQuery('[vde-translate-edit]').removeClass(textEditClassOn);
        }
    });

})(window.jQuery);