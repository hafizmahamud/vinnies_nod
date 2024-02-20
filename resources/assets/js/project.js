/* global project_info_url */
/* global project_donors_url */
/* global project_export_url */
/* global updateBindings */
/* global Mustache */
/* global rome */
/* global initDatatable */
/* global initForm */
/* global showDialog */
/* global showAlert */
/* global debounce */
/* global updateUpdatedBy */

'use strict';

(function ($) {
    var updateDetailsAjax;
    var donorTmpl = $('#mustache-donors').html();

    Mustache.parse(donorTmpl);

    var updateDetails = function (cb) {
        var data = {
            currency: $('[name="currency"]').find('option:selected').val(),
            local_value: $('[name="local_value"]').val(),
        };

        if (updateDetailsAjax !== undefined) {
            if (updateDetailsAjax.readyState > 0 && updateDetailsAjax.readyState < 4) {
                updateDetailsAjax.abort();
            }
        }

        // $('#exchange_rate, #au_value').addClass('loading');

        updateDetailsAjax = $.post(project_info_url, data, function (resp) {
            updateBindings(resp, function(resp) {
                if (resp.is_fully_paid === 'No') {
                    $('#is_fully_paid').find('option[value="0"]').prop('selected', true);
                } else {
                    $('#is_fully_paid').find('option[value="1"]').prop('selected', true);
                }
            });
        }).always(function () {
            // $('#exchange_rate, #au_value').removeClass('loading');

            if (cb) {
                cb();
            }
        });
    };

    var updateDonors = function (cb) {
        var $container = $('.section-donors');
        var projectId = $container.data('project-id');

        $.get(project_donors_url, {project_id: projectId}, function (resp) {
            $container.html(Mustache.render(donorTmpl, resp));
        }).always(function () {
            if (cb) {
                cb();
            }
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
                            return '<a href="/projects/edit/' + data + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                    },
                    {
                        data: 'project_type',
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
                        data: 'overseas_project_id',
                        className: 'text-center',
                        render: function (data, type, row) {
                            if (data === 'N/A') {
                                return data;
                            }

                            return '<a href="/overseas-conferences/edit/' + data + '">' + data  + '</a>';
                        },
                    },
                    {
                        data: 'received_at',
                        className: 'text-center',
                    },
                    {
                        data: 'is_awaiting_support',
                        className: 'text-center',
                    },
                    {
                        data: 'state',
                        className: 'text-center',
                    },
                    {
                        data: 'au_value',
                        className: 'text-center',
                    },
                    {
                        data: 'is_fully_paid',
                        className: 'text-center',
                    },
                    {
                        data: 'balance_owing ',
                        className: 'text-center',
                    },
                ]
            });
        }

        $('#modal-beneficiary').on('shown.bs.modal', function () {
            initDatatable({
                table: '#modal-beneficiary .js-modal-table',
                filter: '#modal-beneficiary .js-modal-table-filter',
                search: {
                    form: '',
                    input: '',
                },
                pagination: {
                    basic: '#modal-beneficiary .pagination-basic',
                    table: '',
                },
                columns: [
                    {
                        data: 'id',
                        className: 'text-center',
                        render: function (data, type, row) {
                            return '<div class="checkbox checkbox-warning"><input type="checkbox" name="beneficiary_id" value="' + data + '"><label></label></div>';
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
                ]
            });

            initForm({
                form: '#modal-beneficiary .js-modal-form',
                btn: '#modal-beneficiary .btn-submit-modal-form',
                beforeSubmitCb: function () {
                    $('#modal-beneficiary .js-modal-form').find('[data-error]').empty().hide();
                },
                successCb: function (responseText, statusText, xhr, $form) {
                    updateBindings(responseText, function (data) {
                        var $p1 = $('[data-bind="beneficiary_name"]');

                        $('.js-form [name="beneficiary_id"]').val(data.beneficiary_id);
                        $('#modal-beneficiary .js-modal-table').attr('data-selected-id', data.beneficiary_id);
                        $('#modal-beneficiary').modal('hide');
                        $p1.closest('p').find('a').append($p1);
                    });
                }
            });
        });

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

        $('#modal-create-donor').on('shown.bs.modal', function () {
            initDatatable({
                table: '#modal-create-donor .js-modal-table',
                filter: '#modal-create-donor .js-modal-table-filter',
                search: {
                    form: '#modal-create-donor .js-table-search-form',
                    input: '#modal-create-donor .js-table-search-input',
                },
                pagination: {
                    basic: '#modal-create-donor .pagination-basic',
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
                form: '#modal-create-donor .js-modal-form',
                btn: '#modal-create-donor .btn-submit-modal-form',
                beforeSubmitCb: function () {
                    $('#modal-create-donor .js-modal-form').find('[data-error]').empty().hide();
                },
                successCb: function (responseText, statusText, xhr, $form) {
                    updateDonors(function () {
                        $('#modal-create-donor').modal('hide');
                    });

                    updateUpdatedBy();
                }
            });
        });

        $('#modal-edit-donor').on('shown.bs.modal', function () {
            initDatatable({
                table: '#modal-edit-donor .js-modal-table',
                filter: '#modal-edit-donor .js-modal-table-filter',
                search: {
                    form: '#modal-edit-donor .js-table-search-form',
                    input: '#modal-edit-donor .js-table-search-input',
                },
                pagination: {
                    basic: '#modal-edit-donor .pagination-basic',
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
                form: '#modal-edit-donor .js-modal-form',
                btn: '#modal-edit-donor .btn-submit-modal-form',
                beforeSubmitCb: function () {
                    $('#modal-edit-donor .js-modal-form').find('[data-error]').empty().hide();
                },
                successCb: function (responseText, statusText, xhr, $form) {
                    updateDonors(function () {
                        $('#modal-edit-donor').modal('hide');
                    });

                    updateUpdatedBy();
                }
            });
        });

        $('#modal-edit-donor').on('show.bs.modal', function (e) {
            var $this = $(this);
            var $el   = $(e.relatedTarget);

            $this.find('.js-modal-table').attr('data-selected-id', $el.data('local-conference-id'));
            $this.find('.js-modal-form').attr('action', $el.data('edit-url'));
        });

        $('#modal-edit-donor').on('hidden.bs.modal', function (e) {
            var $this = $(this);

            $this.find('.js-modal-table').attr('data-selected-id', '');
            $this.find('.js-modal-form').attr('action', '#');

            if ($this.find('.js-modal-table').DataTable()) {
                $this.find('.js-modal-table').DataTable().destroy();
            }
        });

        // Dynamic update form exchange rate
        // $('[name="currency"], [name="local_value"]').on('keyup change', debounce(function () {
        //     updateDetails($.noop);
        // }, 250));

        // On page load, retrieve donors list
        if ($('.section-donors').length) {
            updateDonors();
        }

        // On delete donor
        $(document).on('click', '.js-delete-donor', function (e) {
            e.preventDefault();

            var $this = $(this);

            showDialog('Are you sure you want to delete this Donor and its associated contributions?', true, function () {
                $this.text($this.data('text-progress'));

                $.post($this.data('delete-url'), function (resp) {
                    updateDonors(updateDetails($.noop));
                    updateUpdatedBy();
                }).fail(function () {
                    showAlert('Something went wrong, please try again later.', 'danger');
                }).always(function () {
                    $this.text($this.data('text-default'));
                });
            });
        });

        $('#modal-create-contribution, #modal-edit-contribution').on('shown.bs.modal', function (e) {
            var $el = $(e.relatedTarget);

            updateBindings({
                donor_local_conference_id: $el.data('local-conference-id'),
                donor_local_conference_name: $el.data('local-conference-name'),
            });
        });

        $('#modal-create-contribution').on('hidden.bs.modal', function (e) {
            $(this).find('[name="paid_at"]').val('');
            $(this).find('[name="amount"]').val('');
            $(this).find('[name="donor_id"]').val('');

            updateBindings({
                quarter_year: '',
            });
        });

        $('#modal-create-contribution, #modal-edit-contribution').on('shown.bs.modal', function (e) {
            var $this = $(this);

            $this.on({
              'pick.datepicker': function (e) {
                  var month = e.date.getMonth()+1;
                  var quarter = Math.floor((parseInt(month, 10) + 2) / 3);

                  updateBindings({
                      quarter_year: 'Q' + quarter + ':' + e.date.getFullYear(),
                  });
              }
            });

            // rome($(this).find('[name="paid_at"]')[0]).on('data', function (value) {
            //     var month   = value.split('/');
            //     var quarter = Math.floor((parseInt(month[1], 10) + 2) / 3);
            //
            //     updateBindings({
            //         quarter_year: 'Q' + quarter + ':' + month[2],
            //     });
            // });
        });

        $('#modal-create-contribution').on('show.bs.modal', function (e) {
            $(this).find('[name="donor_id"]').val($(e.relatedTarget).data('id'));
        });

        
        initForm({
            form: '#update-projects .js-form',
            btn: '#update-projects .btn-update-form',
            beforeSubmitCb: function () {
                $('#update-projects .js-form').find('[data-error]').empty().hide();
            },
            successCb: function (data) {
                showAlert(data.msg, 'success');

                updateDonors(
                    updateDetails()
                );

                updateUpdatedBy();
            }
        });

        $('#modal-create-contribution').on('shown.bs.modal', function (e) {
            initForm({
                form: '#modal-create-contribution .js-modal-form',
                btn: '#modal-create-contribution .btn-submit-modal-form',
                beforeSubmitCb: function () {
                    $('#modal-create-contribution .js-modal-form').find('[data-error]').empty().hide();
                },
                successCb: function (responseText, statusText, xhr, $form) {
                    updateDonors(
                        updateDetails(function () {
                            $('#modal-create-contribution').modal('hide');
                        })
                    );

                    updateUpdatedBy();
                }
            });
        });

        $('#modal-edit-contribution').on('show.bs.modal', function (e) {
            var $this = $(this);
            var $el   = $(e.relatedTarget);

            $this.find('[name="paid_at"]').val($el.data('paid-at'));
            $this.find('[name="amount"]').val($el.data('amount'));
            $this.find('[name="donor_id"]').val($el.data('local-conference-id'));
            $this.find('.js-modal-form').attr('action', $el.data('edit-url'));

            var month   = $el.data('paid-at').split('/');
            var quarter = Math.floor((parseInt(month[1], 10) + 2) / 3);

            updateBindings({
                quarter_year: 'Q' + quarter + ':' + month[2],
            });
        });

        $('#modal-edit-contribution').on('hidden.bs.modal', function (e) {
            var $this = $(this);

            $this.find('[name="paid_at"]').val('');
            $this.find('[name="amount"]').val('');
            $this.find('[name="donor_id"]').val('');
            $this.find('.js-modal-form').attr('action', '#');
        });

        $('#modal-edit-contribution').on('shown.bs.modal', function (e) {
            initForm({
                form: '#modal-edit-contribution .js-modal-form',
                btn: '#modal-edit-contribution .btn-submit-modal-form',
                beforeSubmitCb: function () {
                    $('#modal-edit-contribution .js-modal-form').find('[data-error]').empty().hide();
                },
                successCb: function (responseText, statusText, xhr, $form) {
                    updateDonors(
                        updateDetails(function () {
                            $('#modal-edit-contribution').modal('hide');
                        })
                    );

                    updateUpdatedBy();
                }
            });
        });

        $(document).on('click', '.js-delete-contribution', function (e) {
            e.preventDefault();

            var $this = $(this);

            showDialog('Are you sure you want to delete this payment?', true, function () {
                $this.text($this.data('text-progress'));

                $.post($this.data('delete-url'), function (resp) {
                    updateDonors(updateDetails);
                    updateUpdatedBy();
                }).fail(function () {
                    showAlert('Something went wrong, please try again later.', 'danger');
                }).always(function () {
                    $this.text($this.data('text-default'));
                });
            });
        });

        $(document).on('click', '.js-btn-export-projects', function (e) {
            e.preventDefault();

            var formData = {
                filters: {
                    country: $('[name="country"]').find('option:selected').val(),
                    status: $('[name="status"]').find('option:selected').val(),
                    project_type: $('[name="project_type"]').find('option:selected').val(),
                    state: $('[name="state"]').find('option:selected').val(),
                    is_awaiting_support: $('[name="is_awaiting_support"]').find('option:selected').val(),
                    is_fully_paid: $('[name="is_fully_paid"]').find('option:selected').val(),
                    project_completed: $('[name="project_completed"]').find('option:selected').val(),
                    completion_report_received: $('[name="completion_report_received"]').find('option:selected').val(),
                },
                search: {
                    value: $('.js-table-search-input').val(),
                }
            };

            window.location = project_export_url + '?' + $.param(formData);
        });
    });
})(jQuery);
