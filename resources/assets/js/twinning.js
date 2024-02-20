/* global updateBindings */
/* global twinning_export_url */
/* global initDatatable */
/* global initForm */

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
                            return '<a href="/twinnings/edit/' + data + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'is_active',
                        className: 'text-center',
                    },
                    {
                        data: 'local_conference_id',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return '<a href="/local-conferences/edit/' + row.local_conference_id + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'local_conference_name',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return '<a href="/local-conferences/edit/' + row.local_conference_id + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'local_conference_parish',
                        className: 'text-center',
                    },
                    {
                        data: 'local_conference_state',
                        className: 'text-center',
                    },
                    {
                        data: 'overseas_conference_id',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return '<a href="/overseas-conferences/edit/' + row.overseas_conference_id + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'overseas_conference_name',
                        className: 'text-center',
                        render: function(data, type, row) {
                            return '<a href="/overseas-conferences/edit/' + row.overseas_conference_id + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'overseas_conference_parish',
                        className: 'text-center',
                    },
                    {
                        data: 'overseas_conference_country',
                        className: 'text-center',
                    },
                    {
                        data: 'overseas_conference_is_active',
                        className: 'text-center',
                    },
                ]
            });
        }

        $('#modal-os-conf').on('shown.bs.modal', function () {
            initDatatable({
                table: '#modal-os-conf .js-modal-table',
                filter: '#modal-os-conf .js-modal-table-filter',
                search: {
                    form: '#modal-os-conf .js-table-search-form',
                    input: '#modal-os-conf .js-table-search-input',
                },
                pagination: {
                    basic: '#modal-os-conf .pagination-basic',
                    table: '',
                },
                columns: [
                    {
                        data: 'select',
                        className: 'text-center',
                        render: function (data, type, row) {
                            return '<div class="checkbox checkbox-warning"><input type="checkbox" name="overseas_conference_id" value="' + data + '"><label></label></div>';
                        },
                    },
                    {
                        data: 'id',
                        className: 'text-center',
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
                        data: 'central_council',
                        className: 'text-center',
                    },
                    {
                        data: 'particular_council',
                        className: 'text-center',
                    },
                    {
                        data: 'parish',
                        className: 'text-center',
                    },
                    {
                        data: 'is_active',
                        className: 'text-center',
                    },
                ]
            });

            initForm({
                form: '#modal-os-conf .js-modal-form',
                btn: '#modal-os-conf .btn-submit-modal-form',
                beforeSubmitCb: function () {
                    $('#modal-os-conf .js-modal-form').find('[data-error]').empty().hide();
                },
                successCb: function (responseText, statusText, xhr, $form) {
                    updateBindings(responseText, function (data) {
                        var $p1 = $('[data-bind="overseas_conference_id"]');
                        var $p2 = $('[data-bind="overseas_conference_name"]');

                        if (data.overseas_conference_id === 'N/A') {
                            $('.js-form [name="overseas_conference_id"]').val('');
                            $('#modal-os-conf .js-modal-table').attr('data-selected-id', '');
                            $p1.closest('p').append($p1);
                            $p2.closest('p').append($p2);
                        } else {
                            $('.js-form [name="overseas_conference_id"]').val(data.overseas_conference_id);
                            $('#modal-os-conf .js-modal-table').attr('data-selected-id', data.overseas_conference_id);
                            $p1.closest('p').find('a').append($p1);
                            $p2.closest('p').find('a').append($p2);
                        }

                        $('.row-os-conf p').each(function () {
                            var $this = $(this);
                            $this.toggleClass('text-muted', (data.overseas_conference_id === 'N/A'));
                        });

                        $('#modal-os-conf').modal('hide');
                    });
                }
            });
        });

        $('#modal-local-conf').on('shown.bs.modal', function () {
            initDatatable({
                table: '#modal-local-conf .js-modal-table',
                filter: '#modal-local-conf .js-modal-table-filter',
                search: {
                    form: '#modal-local-conf .js-table-search-form',
                    input: '#modal-local-conf .js-table-search-input',
                },
                pagination: {
                    basic: '#modal-local-conf .pagination-basic',
                    table: '',
                },
                columns: [
                    {
                        data: 'select',
                        className: 'text-center',
                        render: function (data, type, row) {
                            return '<div class="checkbox checkbox-warning"><input type="checkbox" name="local_conference_id" value="' + data + '"><label></label></div>';
                        },
                    },
                    {
                        data: 'id',
                        className: 'text-center',
                    },
                    {
                        data: 'name',
                        className: 'text-center',
                    },
                    {
                        data: 'state',
                        className: 'text-center',
                    },
                    {
                        data: 'regional_council',
                        className: 'text-center',
                    },
                    {
                        data: 'diocesan_council_id',
                        className: 'text-center',
                    },
                    {
                        data: 'parish',
                        className: 'text-center',
                    },
                ]
            });

            initForm({
                form: '#modal-local-conf .js-modal-form',
                btn: '#modal-local-conf .btn-submit-modal-form',
                beforeSubmitCb: function () {
                    $('#modal-local-conf .js-modal-form').find('[data-error]').empty().hide();
                },
                successCb: function (responseText, statusText, xhr, $form) {
                    updateBindings(responseText, function (data) {
                        var $p1 = $('[data-bind="local_conference_id"]');
                        var $p2 = $('[data-bind="local_conference_name"]');

                        if (data.local_conference_id === 'N/A') {
                            $('.js-form [name="local_conference_id"]').val('');
                            $('#modal-local-conf .js-modal-table').attr('data-selected-id', '');
                            $p1.closest('p').append($p1);
                            $p2.closest('p').append($p2);
                        } else {
                            $('.js-form [name="local_conference_id"]').val(data.local_conference_id);
                            $('#modal-local-conf .js-modal-table').attr('data-selected-id', data.local_conference_id);
                            $p1.closest('p').find('a').append($p1);
                            $p2.closest('p').find('a').append($p2);
                        }

                        $('.row-local-conf p').each(function () {
                            var $this = $(this);
                            $this.toggleClass('text-muted', (data.local_conference_id === 'N/A'));
                        });

                        $('#modal-local-conf').modal('hide');
                    });
                }
            });
        });

        $(document).on('click', '.js-btn-export-twinnings', function (e) {
            e.preventDefault();

            var formData = {
                filters: {
                    local_conference_state: $('[name="local_conference_state"]').find('option:selected').val(),
                    local_conference_diocesan_council_id: $('[name="local_conference_diocesan_council_id"]').find('option:selected').val(),
                    local_conferences_regional_council: $('[name="local_conferences_regional_council"]').val(),
                    overseas_conferences_central_council: $('[name="overseas_conferences_central_council"]').val(),
                    overseas_conferences_particular_council: $('[name="overseas_conferences_particular_council"]').val(),
                    overseas_conference_country_id: $('[name="overseas_conference_country_id"]').find('option:selected').val(),
                    overseas_conference_is_active: $('[name="overseas_conference_is_active"]').find('option:selected').val(),
                    is_active: $('[name="is_active"]').find('option:selected').val(),
                    period: $('[name="period"]').find('option:selected').val(),
                    national_council: $('[name="national_council"]').find('option:selected').val(),
                    type: $('[name="type"]').find('option:selected').val(),
                },
                search: {
                    value: $('.js-table-search-input').val(),
                }
            };

            window.location = twinning_export_url + '?' + $.param(formData);
        });
    });
})(jQuery);
