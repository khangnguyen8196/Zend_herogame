$(function () {
    pages.taikhoan.init();
});
if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    taikhoan: {
        getNumberOfDayAjax: null,
        init: function () {
            $(document).on('click', '#userInfoForm #update_user_info', {}, function (e) {
                e.preventDefault();
                if ($("#userInfoForm").valid() == true) {
                    var options = {
                        url: "/tai-khoan/cap-nhat-thong-tin",
                        type: "POST",
                        beforeSubmit: function (formData, jqForm, options) {
                        },
                        success: function (data) {
                            if (data.Code > 0) {
                                $("#userInfoForm #update_info_success").show();
                                $("#userInfoForm #update_info_failed").hide();
                                setTimeout(function () {
                                    location.reload();
                                }, 1000);
                            } else {
                                var html = '<strong>Cập nhật thất bại.</strong> ' + data.Message;
                                $("#userInfoForm #update_info_failed").html(html);
                                $("#userInfoForm #update_info_success").hide();
                                $("#userInfoForm #update_info_failed").show();
                            }
                        },
                        error: function () {
                        }
                    };
                    $('#userInfoForm').ajaxSubmit(options);
                }
            });

            $(document).on('change', '#userInfoForm #uif_byear , #userInfoForm #uif_bmonth', {}, function (e) {
                e.preventDefault();
                var year = $("#uif_byear").val();
                var month = $("#uif_bmonth").val();
                var slt_day = "";
                pages.taikhoan.getNumberOfDay(year, month, slt_day);
            });

            $(document).on('keydown', '#userInfoForm #uif_phone', {}, function (e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                                // Allow: home, end, left, right, down, up
                                        (e.keyCode >= 35 && e.keyCode <= 40)) {
                            // let it happen, don't do anything
                            return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                            e.preventDefault();
                }

            });
            
            $('#changePwdModal').on('show.bs.modal', function (event) {
                $("#changePwdForm .form-control").removeClass("error");
                $("#changePwdForm").validate().resetForm();
                $("#changePwdForm")[0].reset();
            });
            
            $(document).on('click', '#changePwdForm #change_pass_btn', {}, function (e) {
                e.preventDefault();
                if ($("#changePwdForm").valid() == true) {
                    var options = {
                        url: "/tai-khoan/thay-doi-mat-khau",
                        type: "POST",
                        beforeSubmit: function (formData, jqForm, options) {
                        },
                        success: function (data) {
                            if (data.Code > 0) {
                                $("#changePwdForm #change_pass_success").show();
                                $("#changePwdForm #change_pass_failed").hide();
                            } else {
                                var html = '<strong>Đổi mật khẩu thất bại.</strong> ' + data.Message;
                                $("#changePwdForm #change_pass_failed").html(html);
                                $("#changePwdForm #change_pass_success").hide();
                                $("#changePwdForm #change_pass_failed").show();
                            }
                        },
                        error: function () {
                        }
                    };
                    $('#changePwdForm').ajaxSubmit(options);
                }
            });
        },
        getNumberOfDay: function (year, month, slt_day) {
            if (pages.taikhoan.getNumberOfDayAjax != null) {
                pages.taikhoan.getNumberOfDayAjax.abort();
            }
            if (parseInt(year) > 0 && parseInt(month) > 0) {
                pages.taikhoan.getNumberOfDayAjax = $.ajax({
                    url: "/index/get-number-of-day-in-month",
                    type: "POST",
                    data: {y: year, m: month, slt_day: slt_day},
                    beforeSend: function () {
                    },
                    success: function (data) {
                        if (data.Code > 0 && (data.Data).length > 0) {
                            $("#userInfoForm #uif_bday").html(data.Data);
                        }
                    }
                });
            }
        }
    }
});
