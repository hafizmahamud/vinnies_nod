/* global initDatatable */

'use strict';

(function ($) {
    $(document).ready(function () {
        if ($('.js-table').length) {
            initDatatable({
                table: '.js-table',
                filter: '.js-table-filter',
                search: {
                    form: '.js-table-search-form',
                    input: '.js-table-search-input',
                },
                pagination: {
                    basic: '.pagination-basic',
                    table: '.pagination-table',
                },
                columns: [
                    {
                        data: 'id',
                        className: 'text-center',
                        render: function (data, type, row) {
                            return '<a href="/remittances/old/view/' + data + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'state',
                        className: 'text-center',
                    },
                    {
                        data: 'received_at',
                        className: 'text-center',
                    },
                    {
                        data: 'quarter',
                        className: 'text-center',
                    },
                    {
                        data: 'year',
                        className: 'text-center',
                    },
                    {
                        data: 'total',
                        className: 'text-center',
                    },
                    {
                        data: 'allocated',
                        className: 'text-center',
                    },
                ]
            });
        }
    });
})(jQuery);
