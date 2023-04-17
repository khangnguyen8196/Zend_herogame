$(function () {
    pages.changepass.init();
});
if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    changepass: {
        validator: '',
        isInProgress: false,
        init: function () {
            // -- Change password -- 
            // change password button
            $(document).on('click', '#change-password-btn', {}, function ( ) {
                //reset form
                $("#curent_password").val('');
                $("#new_password").val('');
                $("#confirm_new_password").val('');
                // -- end reset form
                $("#change-pass-mgs").hide();
                $("#modal_password").modal({keyboard: false, show: true, backdrop: 'static'});
                // declare options for validation
                var coptions = {
                    rules: {
                        curent_password: {
                            required: true
                        },
                        new_password: "required",
                        confirm_new_password: {
                            required: true,
                            equalTo: "#new_password"
                        }
                    },
                    messages: {
                        curent_password: {
                            required: $("#curent_password").attr('data-msg')
                        },
                        new_password: {
                            required: $("#new_password").attr('data-msg'),
                        },
                        confirm_new_password: {
                            required: $("#confirm_new_password").attr('data-msg'),
                            equalTo: $("#confirm_new_password").attr('data-miss-match-msg'),
                        }
                    }
                };
                pages.validation.setupValidation("#change-password-form", coptions);
            });

            $(document).on('click', '#modal_password #save-change-pass-btn', {}, function ( ) {
                var cPass = $("#curent_password").val();
                var nPass = $("#new_password").val();
                var uId = $("#uId").val();
                pages.auth.changePassword(cPass, nPass, uId);
            });
            
            $(document).on('click', '#change-pass-mgs button.close', {}, function ( ) {
                $("#change-pass-mgs").hide();
            });
        },
        //
        changePassword: function (cPass, nPass, uId) {
            if (pages.validation.validator["#change-password-form"].form() == false) {
                return false;
            }
            if (pages.auth.isInProgress == true) {
                return false;
            }
            pages.auth.isInProgress = true;
            var ecPass = btoa($.md5(cPass));
            var enPass = btoa($.md5(nPass));

            $.ajax({
                url: '/admin/auth/change-password',
                type: 'GET',
                data: {curentPassword: ecPass, newPassword: enPass, uId: uId},
                beforeSend: function ( ) {
                    $("#close-change-pass-btn").prop('disabled', true);
                    $("#modal_password #save-change-pass-btn").prop('disabled', true);
                    $('#modal_password button.close').prop('disabled', true);
                },
                success: function (data) {
                    pages.auth.isInProgress = false;
                    $("#close-change-pass-btn").prop('disabled', false);
                    $("#modal_password #save-change-pass-btn").prop('disabled', false);
                    $('#modal_password button.close').prop('disabled', false);
                    if (data.Code > 0) {
                        $("#curent_password").val('');
                        $("#new_password").val('');
                        $("#confirm_new_password").val('');
                        
                        $("#change-pass-mgs").show();
                        $("#change-pass-mgs span.message-change-pass").text(data.Message);
                        $("#change-pass-mgs").addClass('alert-success').addClass('alert-arrow-left').removeClass('alert-danger');
                    } else {
                        if ( data.Code == '-999'){
                            window.location = '/admin/auth/login';
                        } else{
                            $("#change-pass-mgs").show();
                            $("#change-pass-mgs span.message-change-pass").text(data.Message);
                            $("#change-pass-mgs").addClass('alert-danger').removeClass('alert-arrow-left').removeClass('alert-success');
                        }
                    }
                },
                error: function (error) {
                    pages.auth.isInProgress = false;
                    $("#close-change-pass-btn").prop('disabled', false);
                    $("#modal_password #save-change-pass-btn").prop('disabled', false);
                    $('#modal_password button.close').prop('disabled', false);
                }
            });
        }
    }
});