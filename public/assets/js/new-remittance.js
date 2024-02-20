/* global initDatatable */
/* global initForm */
/* global remittance_donation_url */
/* global showDialog */
/* global types */
/* global Mustache */
/* global updateBindings */
/* global debounce */
/* global numberFormat */
/* global updateUpdatedBy */

'use strict';

(function ($) {
    var projectDonationsTmpl  = $('#mustache-project-donations').html();
    var twinningDonationsTmpl = $('#mustache-twinning-donations').html();
    var grantDonationsTmpl    = $('#mustache-grant-donations').html();
    var councilDonationsTmpl  = $('#mustache-council-donations').html();

    Mustache.parse(projectDonationsTmpl);
    Mustache.parse(twinningDonationsTmpl);
    Mustache.parse(grantDonationsTmpl);
    Mustache.parse(councilDonationsTmpl);

    var updateListing = function (document_id, type) {
        $.get(remittance_donation_url, {document_id: document_id, type: type}, function (res) {
            var form;
            var tmpl;

            switch (type) {
                case types.projects:
                    tmpl = projectDonationsTmpl;
                    form = 'project';
                    break;

                case types.twinning:
                    tmpl = twinningDonationsTmpl;
                    form = 'twinning';
                    break;

                case types.grants:
                    tmpl = grantDonationsTmpl;
                    form = 'grant';
                    break;

                case types.council:
                    tmpl = councilDonationsTmpl;
                    form = 'council';
                    break;
            }

            $('.remittance-donations[data-type="' + type + '"]').html(Mustache.render(tmpl, res));
            updateBindings(res.total);

            initForm({
                form: '.js-remittance-document-form-' + form,
                btn: '.js-remittance-document-form-' + form + ' button[type="submit"]',
                successCb: function (responseText, statusText, xhr, $form) {
                    updateListing(responseText.id, responseText.type);
                },
                errorCb: function (xhr, status, error) {
                    var errors = JSON.parse(xhr.responseText);

                    if (errors.errors.document.length) {
                        showDialog(errors.errors.document[0], false);
                    }
                }
            });

            initForm({
                form: '.js-delete-document-form-' + form,
                btn: '.js-delete-document-form-' + form + ' button[type="submit"]',
                successCb: function (responseText, statusText, xhr, $form) {
                    updateListing(false, responseText.type);
                }
            });
        }).fail(function (xhr, status, error) {
            var errors = JSON.parse(xhr.responseText);

            if (errors.type === 'dialog') {
                showDialog(errors.msg, errors.confirm);
            }
        }).always(function() {
            updateUpdatedBy();
        });
    };

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
                            return '<a href="/remittances/new/edit/' + data + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'state',
                        className: 'text-center',
                    },
                    {
                        data: 'date',
                        className: 'text-center',
                    },
                    {
                        data: 'created_at',
                        className: 'text-center',
                    },
                    {
                        data: 'approved_at',
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
                        data: 'projects',
                        className: 'text-center',
                    },
                    {
                        data: 'twinning',
                        className: 'text-center',
                    },
                    {
                        data: 'grants',
                        className: 'text-center',
                    },
                    {
                        data: 'councils',
                        className: 'text-center',
                    },
                    {
                        data: 'is_approved',
                        className: 'text-center',
                    },
                ]
            });
        }

        if ($('.remittance-donations').length) {
            $('.remittance-donations').each(function () {
                updateListing(false, $(this).data('type'));
            });
        }

        if ($('.new-remittance-edit-form').length) {
            $('.js-btn-approve-remittance').on('click', function (e) {
                e.preventDefault();

                var $this = $(this);

                showDialog('Are you sure you want to approve and process this remittance?', true, function () {
                    window.location = $this.attr('href');
                });
            });

            $('.js-btn-unapprove-remittance').on('click', function (e) {
                e.preventDefault();

                var $this = $(this);

                showDialog('Are you sure you want to reinstate edit mode for this remittance?<br><br><strong>After confirmation, please remember to delete Project Payments automatically added by this remittance on previous approval.</strong>', true, function () {
                    window.location = $this.attr('href');
                });
            });
        }
    });
})(jQuery);
