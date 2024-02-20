/* global has_invalid_diocesan_council */
/* global invalid_diocesan_council_id */
/* global bootbox */
/* global initDatatable */
/* global osconf_export_url */

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
                            return '<a href="/overseas-conferences/edit/' + data + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'name',
                        className: 'text-center',
                    },
                    {
                        data: 'parish',
                        className: 'text-center',
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                    },
                    {
                        data: 'country',
                        className: 'text-center',
                    },
                    {
                        data: 'central_council',
                        className: 'text-center',
                    },
                    {
                        data: 'particular_council',
                        className: 'text-center',
                    },
                    {
                        data: 'twinning_status',
                        className: 'text-center',
                    },
                    {
                        data: 'is_active',
                        className: 'text-center',
                    },
                ]
            });
        }

        if ($('.form-edit-os-conf').length) {
            $('.form-edit-os-conf').dirty();

            $('.js-btn-add-twinning').on('click', function (e) {
                var $this = $(this);

                if ($('.form-edit-os-conf').dirty('isDirty')) {
                    e.preventDefault();

                    bootbox.dialog({
                        message: 'You might have unsaved changes for the Overseas Conference you are trying to add a twin. Would you like to save the changes, discard changes or Cancel and take time to review the modifications?',
                        buttons: {
                            cancel: {
                                label: 'Cancel and Review',
                                className: 'btn-warning',
                                callback: function() {
                                    // do nothing
                                }
                            },
                            discard: {
                                label: 'Discard Changes',
                                className: 'btn-danger',
                                callback: function() {
                                    window.location = $this.attr('href');
                                }
                            },
                            save: {
                                label: 'Save Changes',
                                className: 'btn-primary',
                                callback: function() {
                                    $('[data-bb-handler="save"]').text('Saving...');

                                    $('.js-form').ajaxSubmit(function () {
                                        $('[data-bb-handler="save"]').text('Redirecting...');

                                        window.location = $this.attr('href');
                                    });

                                    $('[data-bb-handler="save"]').text('Save Changes');

                                    return false; // dont close the modal
                                }
                            }
                        }
                    });
                }
            });
        }

        $(document).on('click', '.js-btn-export-os-conf', function (e) {
            e.preventDefault();

            var formData = {
                filters: {
                    country: $('[name="country"]').find('option:selected').val(),
                    is_active: $('[name="is_active"]').find('option:selected').val(),
                    is_in_status_check: $('[name="is_in_status_check"]').find('option:selected').val(),
                    status_check_initiated_at: $('[name="status_check_initiated_at"]').find('option:selected').val(),
                    is_in_surrendering: $('[name="is_in_surrendering"]').find('option:selected').val(),
                    surrendering_initiated_at: $('[name="surrendering_initiated_at"]').find('option:selected').val(),
                    surrendering_deadline_at: $('[name="surrendering_deadline_at"]').find('option:selected').val(),
                    reason_status_check: $('[name="reason_status_check"]').find('option:selected').val(),
                    twinning_status: $('[name="twinning_status"]').find('option:selected').val(),
                    twinning_state: $('[name="twinning_state"]').find('option:selected').val(),
                    central_council: $('[name="central_council"]').val(),
                    particular_council: $('[name="particular_council"]').val(),
                    national_council: $('[name="national_council"]').find('option:selected').val(),
                    status: $('[name="status"]').find('option:selected').val(),
                },
                search: {
                    value: $('.js-table-search-input').val(),
                }
            };

            window.location = osconf_export_url + '?' + $.param(formData);
        });
    });
})(jQuery);
