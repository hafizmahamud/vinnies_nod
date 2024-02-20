/* global initDatatable */
/* global showAlert */
/* global showDialog */
/* global updateUpdatedBy */

'use strict';

(function ($) {
    var $btnDelete  = $('.js-btn-beneficiary-delete');
    var $btnRestore = $('.js-btn-beneficiary-restore');

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
                            return '<a href="/beneficiaries/edit/' + data + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'name',
                        className: 'text-center',
                    },
                    {
                        data: 'country',
                        className: 'text-center',
                    },
                    {
                        data: 'contact_title',
                        className: 'text-center',
                    },
                    {
                        data: 'contact_first_name',
                        className: 'text-center',
                    },
                    {
                        data: 'contact_last_name',
                        className: 'text-center',
                    },
                    {
                        data: 'email',
                        className: 'text-center',
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                    },
                ]
            });
        }

        $btnDelete.on('click', function (e) {
            e.preventDefault();

            var message = '<p>Are you sure you want to delete ' + $('#name').val() + '?</p>';

            showDialog(message, true, function () {
                var data = {
                    beneficiary: $btnDelete.data('beneficiary-id')
                };

                $btnDelete.text($btnDelete.data('text-progress'));

                $.post('/beneficiaries/delete', data, function (resp) {
                    showAlert(resp.msg, 'success');
                    updateUpdatedBy();

                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }).fail(function (xhr) {
                    var resp = JSON.parse(xhr.responseText);
                    showAlert(resp.msg, 'danger');
                }).always(function () {
                    $btnDelete.text($btnDelete.data('text-default'));
                });
            });
        });

        $btnRestore.on('click', function (e) {
            e.preventDefault();

            var message = '<p>Are you sure you want to restore ' + $('#name').val() + '?</p>';

            showDialog(message, true, function () {
                var data = {
                    beneficiary: $btnRestore.data('beneficiary-id')
                };

                $btnRestore.text($btnRestore.data('text-progress'));

                $.post('/beneficiaries/restore', data, function (resp) {
                    showAlert(resp.msg, 'success');
                    updateUpdatedBy();

                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }).fail(function (xhr) {
                    var resp = JSON.parse(xhr.responseText);
                    showAlert(resp.msg, 'danger');
                }).always(function () {
                    $btnRestore.text($btnRestore.data('text-default'));
                });
            });
        });
    });
})(jQuery);
