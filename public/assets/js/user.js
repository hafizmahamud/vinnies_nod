/* global bootbox */
/* global initDatatable */
/* global showDialog */
/* global showAlert */
/* global updateUpdatedBy */
/* global diocesan_councils */

'use strict';

(function ($) {
    var $btnDeactivate = $('.js-btn-user-deactivate');
    var $btnReactivate = $('.js-btn-user-reactivate');
    var $btnSignTos = $('.js-btn-user-signtos');


    var changeChoiceColor = function () {
        var states = ['National Council','National','Canberra and Goulburn','New South Wales','Northern Territory','Queensland','South Australia','Tasmania','Victoria','Western Australia'];

        states.forEach(function(state){
            $("#dioceses").find("li").each(function(){
                if( $(this).is("[title='" + state + "']") ){
                    $(this)
                        .css("background-color", "#00ffff");
                }
            });
        });
    }

    var generatePassword = function () {
        var charset  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        var length   = 16;
        var password = '';

        for (var i = 0, n = charset.length; i < length; ++i) {
            password += charset.charAt(Math.floor(Math.random() * n));
        }

        return password;
    };

    $(document).ready(function () {
        $('.js-toggle-password').on('click', function () {
            var $this  = $(this);
            var $input = $($this.data('target'));

            if ($this.find('.fa').hasClass('fa-eye')) {
                $input.attr('type', 'text');
                $this.find('.fa').removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $input.attr('type', 'password');
                $this.find('.fa').removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        $('.js-generate-password').on('click', function (e) {
            e.preventDefault();

            var password = generatePassword();
            var $input   = $($(this).data('target'));

            $input.val(password).change();

            if ($input.attr('type') === 'password') {
                $('.js-toggle-password').click();
            }
        });

        $btnDeactivate.on('click', function (e) {
            e.preventDefault();

            var message = '<p>Are you sure you want to deactivate ' + $('#first_name').val() + ' ' + $('#last_name').val() + '?</p>';

            showDialog(message, true, function () {
                var data = {
                    user: $btnDeactivate.data('user-id')
                };

                $btnDeactivate.text($btnDeactivate.data('text-progress'));

                $.post('/users/deactivate', data, function (resp) {
                    showAlert(resp.msg, 'success');
                    updateUpdatedBy();

                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }).fail(function (xhr) {
                    var resp = JSON.parse(xhr.responseText);
                    showAlert(resp.msg, 'danger');
                }).always(function () {
                    $btnDeactivate.text($btnDeactivate.data('text-default'));
                });
            });
        });

        $btnReactivate.on('click', function (e) {
            e.preventDefault();

            var message = '<p>Are you sure you want to reactivate ' + $('#first_name').val() + ' ' + $('#last_name').val() + '?</p>';

            showDialog(message, true, function () {
                var data = {
                    user: $btnReactivate.data('user-id')
                };

                $btnReactivate.text($btnReactivate.data('text-progress'));

                $.post('/users/reactivate', data, function (resp) {
                    showAlert(resp.msg, 'success');
                    updateUpdatedBy();

                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }).fail(function (xhr) {
                    var resp = JSON.parse(xhr.responseText);
                    showAlert(resp.msg, 'danger');
                }).always(function () {
                    $btnReactivate.text($btnReactivate.data('text-default'));
                });
            });
        });

        $btnSignTos.on('click', function (e) {
            e.preventDefault();

            var message = '<p>Are you sure you want to ask ' + $('#first_name').val() + ' ' + $('#last_name').val() + ' to re-sign Term of Use ?</p>';

            showDialog(message, true, function () {
                var data = {
                    user: $btnSignTos.data('user-id')
                };

                $btnSignTos.text($btnSignTos.data('text-progress'));

                $.post('/users/signtos', data, function (resp) {
                    showAlert(resp.msg, 'success');
                    updateUpdatedBy();

                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }).fail(function (xhr) {
                    var resp = JSON.parse(xhr.responseText);
                    showAlert(resp.msg, 'danger');
                }).always(function () {
                    $btnSignTos.text($btnSignTos.data('text-default'));
                });
            });
        });

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
                            return '<a href="/users/edit/' + data + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'states',
                        className: 'text-center',
                    },
                    {
                        data: 'first_name',
                        className: 'text-center',
                    },
                    {
                        data: 'last_name',
                        className: 'text-center',
                    },
                    {
                        data: 'role',
                        className: 'text-center',
                    },
                    {
                        data: 'mfa',
                        className: 'text-center',
                    },
                    {
                        data: 'last_login',
                        className: 'text-center',
                    },
                    {
                        data: 'email',
                        className: 'text-center',
                    },
                ]
            });
        }

        $('[name="states[]"]').on('change', function (e) {
            e.preventDefault();
            var selected  = $(this).val();           
            var $dioceses = $('[name="dioceses[]"]');

            if ( selected.includes('national') ) {
                // enable all diocesan/central council
                $dioceses.find('option').prop('disabled', false);
                $dioceses.find('option').prop('selected', true); // set all dioceses as selected if state = national
            } else {
                $dioceses.find('option').prop('disabled', true);
                $dioceses.find('option').prop('selected', false);

                $.each(selected, function (index, val) {
                    $dioceses.find('optgroup[label="' + val.toUpperCase() + '"]').find('option').prop('selected', true);
                    $dioceses.find('optgroup[label="' + val.toUpperCase() + '"]').find('option').prop('disabled', false);
                });
            }

            $dioceses.select2();

            changeChoiceColor();
        })

        $('#dioceses').on('change', function (e) {
            //e.preventDefault();

            changeChoiceColor();
        })

        var pathname = (window.location.pathname).split('/');

        if (pathname.includes('edit')) { // check if edit page 
            var selected  = $('[name="states[]"]').val();           
            var current_dioceses = $('[name="dioceses[]"]').val();
            var $dioceses = $('[name="dioceses[]"]');

            if ( selected.includes('national') ) {
                // enable all diocesan/central council
                $dioceses.find('option').prop('disabled', false);
                $dioceses.find('option').prop('selected', false); // set all dioceses as selected if state = national

                $.each(current_dioceses, function (index, val) {
                    $dioceses.find('option[value="' + val + '"]').prop('selected', true);
                });
                
            } else {
                $dioceses.find('option').prop('disabled', true);
                $dioceses.find('option').prop('selected', false);

                $.each(selected, function (index, val) {

                    $.each(current_dioceses, function (index, val_d) {
                        $dioceses.find('optgroup[label="' + val.toUpperCase() + '"]').find('option[value="' + val_d + '"]').prop('selected', true);
                    });

                    $dioceses.find('optgroup[label="' + val.toUpperCase() + '"]').find('option').prop('disabled', false);
                });
            }

            $dioceses.select2();

            changeChoiceColor();
        }

        $(document).on('click', '#getActivity', function(e){
            e.preventDefault();
            var url = $(this).data('url');
            $('.message-modal').html(''); 
            $('#modal-loader').show();     
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html'
            })
            .done(function(data){
               // console.log(data);  
                $('.message-modal').html('');    
                $('.message-modal').html(data); // load response 
                $('#modal-loader').hide();        // hide ajax loader   
            })
            .fail(function(){
                $('#dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
                $('#modal-loader').hide();
            });
        });

    });
})(jQuery);
