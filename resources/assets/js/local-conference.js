/* global has_invalid_diocesan_council */
/* global invalid_diocesan_council_id */
/* global bootbox */
/* global initDatatable */
/* global localconf_export_url */

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
                            return '<a href="/local-conferences/edit/' + data + '">' + data + '</a>';
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
                        data: 'state_council',
                        className: 'text-center',
                    },
                    {
                        data: 'diocesan_council_id',
                        className: 'text-center',
                    },
                    {
                        data: 'regional_council',
                        className: 'text-center',
                    },
                   
                ]
            });
        }

        $('[name="state_council"]').on('change', function (e) {
            e.preventDefault();
            var selected  = $(this).val();           
            var $dioceses = $('[name="diocesan_council_id"]');

            console.log(selected);

            // if ( selected.includes('national') ) {
            //     // enable all diocesan/central council
            //     $dioceses.find('option').prop('disabled', false);
            //     $dioceses.find('option').prop('selected', false); 
            //     $dioceses.find('optgroup').css("display","inline");

            // } else {
                //$dioceses.find('option').prop('disabled', true);
                $dioceses.find('option').prop('selected', false);
                $dioceses.find('optgroup').css("display","none");

                $dioceses.find('optgroup[label="' + selected.toUpperCase() + '"]').find('option').prop('disabled', false);
                $dioceses.find('optgroup[label="' + selected.toUpperCase() + '"]').css("display","inline");
            // }
        })

        var pathname = (window.location.pathname).split('/');

        if (pathname.includes('edit')) { // check if edit page 
            var selected  = $('[name="state_council"]').val();           
            var $current_dioceses = $('[name="diocesan_council_id"]').val();
            var $dioceses = $('[name="diocesan_council_id"]');

            if ( selected.includes('national') ) {
                // enable all diocesan/central council
                $dioceses.find('option').prop('disabled', false);
                $dioceses.find('option').prop('selected', false); 
                $dioceses.find('optgroup').css("display","inline");

            } else {
                $dioceses.find('option').prop('selected', false);
                $dioceses.find('optgroup').css("display","none");

                $dioceses.find('optgroup[label="' + selected.toUpperCase() + '"]').find('option').prop('disabled', false);
                $dioceses.find('optgroup[label="' + selected.toUpperCase() + '"]').find('option[value="' + $current_dioceses + '"]').prop('selected', true);
                $dioceses.find('optgroup[label="' + selected.toUpperCase() + '"]').css("display","inline");
            }
        }

        if ($('.form-edit-local-conf').length) {
            $('.form-edit-local-conf').dirty();

            if (has_invalid_diocesan_council) {
                $('#diocesan_council_id').find('option[value="' + invalid_diocesan_council_id +'"]').prop('disabled', true);
            }

            $('.js-btn-add-twinning').on('click', function (e) {
                var $this = $(this);

                if ($('.form-edit-local-conf').dirty('isDirty')) {
                    e.preventDefault();

                    bootbox.dialog({
                        message: 'You might have unsaved changes for the Australian Conference you are trying to add a twin. Would you like to save the changes, discard changes or Cancel and take time to review the modifications?',
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

        $(document).on('click', '.js-btn-export-local-conf', function (e) {
            e.preventDefault();

            var formData = {
                filters: {
                    state: $('[name="state"]').find('option:selected').val(),
                    diocesan_council_id: $('[name="diocesan_council_id"]').find('option:selected').val(),
                    status: $('[name="status"]').find('option:selected').val(),
                    is_flagged: $('[name="is_flagged"]').find('option:selected').val(),
                    regional_council: $('[name="regional_council"]').val(),
                    state_council: $('[name="state_council"]').find('option:selected').val(),
                },
                search: {
                    value: $('.js-table-search-input').val(),
                }
            };

            window.location = localconf_export_url + '?' + $.param(formData);
        });
    });
})(jQuery);
