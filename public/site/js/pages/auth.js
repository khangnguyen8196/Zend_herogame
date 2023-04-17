$(function () {
    pages.auth.init();
});
if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    auth: {
        init: function () {
            $('#signinModal').on('show.bs.modal', function (event) {
                $("#signinForm .form-control").removeClass("error");
                $("#signinForm").validate().resetForm();
                $("#signinForm")[0].reset();
            });
            
            $('#forgotPwdModal').on('show.bs.modal', function (event) {
                $("#forgotPwdForm .form-control").removeClass("error");
                $("#forgotPwdForm").validate().resetForm();
                $("#forgotPwdForm")[0].reset();
            });
            
            $('#signupModal').on('show.bs.modal', function (event) {
                $("#signupForm .form-control").removeClass("error");
                $("#signupForm").validate().resetForm();
                $("#signupForm")[0].reset();
            });
            
            $('#resetPwdModal').on('show.bs.modal', function (event) {
                $("#resetPwdForm .form-control").removeClass("error");
                $("#resetPwdForm").validate().resetForm();
                $("#resetPwdForm")[0].reset();
            });
            
            $(document).on('click', '#signinForm #login-btn', {}, function (e) {
                e.preventDefault();
                if ($("#signinForm").valid() == true) {
                    var options = {
                        url: "/auth/dang-nhap",
                        type: "POST",
                        beforeSubmit: function (formData, jqForm, options) {
                    	 	$(".loader").attr('data-text','Vui lòng đợi trong giây lát').addClass('is-active');
                        },
                        success: function (data) {
                        	$(".loader").removeClass('is-active');
                            if (data.Code > 0) {
                            	location.reload();
                            } else {
                                var html = '<strong>Đăng nhập thất bại.</strong> '+ data.Message;
                                $("#signinForm #signin_failed").html(html);
                                $("#signinForm #signin_failed").show();
                            }
                        },
                        error: function () {
                        	$(".loader").removeClass('is-active');
                        }
                    };
                    $('#signinForm').ajaxSubmit(options);
                }
            });
            
            $(document).on('click', '#forgotPwdForm #confirm_forgot', {}, function (e) {
                e.preventDefault();
                if ($("#forgotPwdForm").valid() == true) {
                    var options = {
                        url: "/auth/quen-mat-khau",
                        type: "POST",
                        beforeSubmit: function (formData, jqForm, options) {
                    		$(".loader").attr('data-text','Vui lòng đợi trong giây lát').addClass('is-active');
                        },
                        success: function (data) {
                        	$(".loader").removeClass('is-active');
                            if (data.Code > 0) {
                                 $("#forgotPwdForm #forgot_failed").hide();
                                 $("#forgotPwdForm #forgot_success").show();
                            } else {
                            	var html = '<strong>Thất bại.</strong> '+ data.Message;
                                $("#forgotPwdForm #forgot_failed").html(html);
                                $("#forgotPwdForm #forgot_failed").show();
                                $("#forgotPwdForm #forgot_success").hide();
                            }
                        },
                        error: function () {
                        	$(".loader").removeClass('is-active');
                        }
                    };
                    $('#forgotPwdForm').ajaxSubmit(options);
                }
            });
            
            $(document).on('click', '#signupForm #signup_btn', {}, function (e) {
                e.preventDefault();
                if ($("#signupForm").valid() == true) {
                    var options = {
                        url: "/auth/dang-ky",
                        type: "POST",
                        beforeSubmit: function (formData, jqForm, options) {
                    		$(".loader").attr('data-text','Vui lòng đợi trong giây lát').addClass('is-active');
                        },
                        success: function (data) {
                        	$(".loader").removeClass('is-active');
                            if (data.Code > 0) {
                                $("#signupForm #signup_failed").hide();
                                $("#signupForm #signup_success").show();
                                setTimeout( function(){
                                    location.reload();
                                }, 1000);
                            } else {
                                var html = '<strong>Đăng ký thất bại.</strong> '+ data.Message;
                                $("#signupForm #signup_failed").html(html);
                                $("#signupForm #signup_failed").show();
                                $("#signupForm #signup_success").hide();
                            }
                        },
                        error: function () {
                        	$(".loader").removeClass('is-active');
                        }
                    };
                    $('#signupForm').ajaxSubmit(options);
                }
            });
            
            if(typeof is_reset != "undefined" && is_reset == "1"){
            	$("#resetPwdModal").modal("show");
            }
            
            $(document).on('click', '#resetPwdForm #reset_pass_btn', {}, function (e) {
                e.preventDefault();
                if ($("#resetPwdForm").valid() == true) {
                    var options = {
                        url: "/auth/dat-lai-mat-khau",
                        type: "POST",
                        beforeSubmit: function (formData, jqForm, options) {
                    		$(".loader").attr('data-text','Vui lòng đợi trong giây lát').addClass('is-active');
                        },
                        success: function (data) {
                        	$(".loader").removeClass('is-active');
                            if (data.Code > 0) {
                                $("#resetPwdForm #reset_pass_failed").hide();
                                $("#resetPwdForm #reset_pass_success").show();
                                is_reset = 0;
                                setTimeout( function(){
                                    window.location = "/";
                                }, 1000);
                            } else {
                                var html = '<strong>Đổi mật khẩu thất bại.</strong> '+ data.Message;
                                $("#resetPwdForm #reset_pass_failed").html(html);
                                $("#resetPwdForm #reset_pass_failed").show();
                                $("#resetPwdForm #reset_pass_success").hide();
                            }
                        },
                        error: function () {
                        	$(".loader").removeClass('is-active');
                        }
                    };
                    $('#resetPwdForm').ajaxSubmit(options);
                }
            });
            
        }
    }
});
