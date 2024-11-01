const self = spoton = {
    /**
     * Init
     */
    init: function () {
        self.callTracking.init();
        self.api.init();
        self.advancedSettings.init();
    },

    advancedSettings: {
        showBtn: null,
        hideBtn: null,
        elements: [],

        /**
         * Initiate
         */
        init: function () {
            self.advancedSettings.elements = jQuery('.spoton-navigation, ' +
                                                    '.spoton-navigation-spacer, ' +
                                                    '.spoton-hooks, ' +
                                                    '.spoton-hooks-spacer, ' +
                                                    '.spoton-exclude-forms, ' +
                                                    '.spoton-exclude-forms-spacer');

            self.advancedSettings.showBtn = jQuery('.spoton-advanced-settings-show');
            self.advancedSettings.hideBtn = jQuery('.spoton-advanced-settings-hide');

            self.advancedSettings.elements.hide();
        },

        /**
         * Show advanced settings
         */
        show: function () {
            self.advancedSettings.elements.show();
            self.advancedSettings.showBtn.hide();
            self.advancedSettings.hideBtn.show();
        },

        /**
         * Hide advanced settings
         */
        hide: function () {
            self.advancedSettings.elements.hide();
            self.advancedSettings.showBtn.show();
            self.advancedSettings.hideBtn.hide();
        }
    },

    callTracking: {
        loader: null,
        buttonActivated: null,
        buttonDeactivated: null,
        navigateToSpotOnLive: null,
        succeeded: null,

        /**
         * Initiate
         */
        init: function () {
            self.callTracking.loader = jQuery('.call-tracking').find('.loading');
            self.callTracking.buttonActivated = jQuery('.call-tracking').find('.button-activated');
            self.callTracking.buttonDeactivated = jQuery('.call-tracking').find('.button-deactivated');
            self.callTracking.navigateToSpotOnLive = jQuery('.call-tracking').find('.navigate-to-spoton-live');
            self.callTracking.succeeded = jQuery('.call-tracking').find('.succeeded');

            self.callTracking.check();
        },

        /**
         * Loading state
         *
         * @param isLoading
         */
        loading: function (isLoading) {
            if (isLoading) {
                self.callTracking.loader.show();

                self.callTracking.buttonActivated.hide();
                self.callTracking.buttonDeactivated.hide();
                self.callTracking.navigateToSpotOnLive.hide();
                self.callTracking.succeeded.hide();

                return;
            }

            self.callTracking.loader.hide();
        },

        /**
         * Check call tracking
         */
        check: function () {
            self.callTracking.loading(true);

            jQuery.post(ajaxurl, {action: 'spoton_call_tracking_check'}, function (response) {
                self.callTracking.loading(false);

                if (response != '1') {
                    self.callTracking.buttonDeactivated.show();
                    return;
                }

                self.callTracking.buttonActivated.show();
            });
        },

        /**
         * Activate
         */
        activate: function () {
            self.callTracking.loading(true)

            jQuery.post(ajaxurl, {action: 'spoton_call_tracking_activate'}, function (response) {
                self.callTracking.loading(false);

                if (response != '1') {
                    self.callTracking.navigateToSpotOnLive.show();
                    return;
                }

                self.callTracking.check();
            });
        },

        /**
         * Deactivate
         */
        deactivate: function () {
            self.callTracking.loading(true)

            jQuery.post(ajaxurl, {action: 'spoton_call_tracking_deactivate'}, function (response) {
                self.callTracking.loading(false);
                self.callTracking.check();
            });
        }
    },

    /**
     * API
     */
    api: {
        action: null,
        loader: null,
        status: null,
        statusError: null,
        
        /**
         * Initiate
         */
        init: function () {
            self.api.action = jQuery('.spoton-api-action');
            self.api.loader = jQuery('.spoton-api-loading');
            self.api.status = jQuery('.spoton-api-status');
            self.api.statusError = jQuery('.spoton-api-status-error');

            self.api.status.hide();
            self.api.loading(false);
        },

        /**
         * Check
         */
        check: function () {
            self.api.loading(true);

            const key = jQuery('input[name=spoton_key]').val();

            jQuery.post(ajaxurl, {action: 'spoton_validate_key', key: key}, function (response) {
                self.api.setStatus((response == '1'));
                self.api.loading(false);
            });
        },

        /**
         * Set status
         *
         * @param isSuccess
         */
        setStatus: function (isSuccess) {
            self.api.status.hide();

            if (!isSuccess) {
                self.api.statusError.show();

                return;
            }

            jQuery('.spoton-api-status-success').show();
        },

        /**
         * Loading state
         *
         * @param isLoading
         */
        loading: function (isLoading) {
            if (isLoading) {
                self.api.loader.show();

                self.api.status.hide();
                self.api.action.hide();
                return;
            }

            self.api.loader.hide();
            self.api.action.show();
        }
    }
}

jQuery('document').ready(function () {
    spoton.init();
});
