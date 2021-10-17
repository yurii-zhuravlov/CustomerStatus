define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';
    return  function (config) {
        let url = config.url;
        let status = config.status;
        let params = {};
        params.isAjax = 1;
        params.id = customerData.get('customer')().id;
        $.ajax({
            url: url,
            type: 'POST',
            data: params
        }).done(function (data) {
            if (data[status].length) {
                $('.status-message').text(data[status]);
                $('.customer-status-header').show();
            }
        });
    };
});
