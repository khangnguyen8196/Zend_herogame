$(function () {
    pages.signup.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	signup: {
        init: function () {
            var me = this;
//     		var regex = /^[a-zA-Z0-9]*$/;
            $("#smbtn").click(function(){
            	var userName = $("#username").val();
            	var pass = $("#password").val();
            	var rePass = $("#password_confirm").val();
            	var regex = /^[a-zA-Z0-9]*$/;
            	var res = regex.test(userName);
            	if( userName < 3){
            		alert('Tên Đăng Nhập Phải Hơn 3 Ký Tự');
            		return false;
            	}
            	if( res == false){
            		alert('Tên Đăng Nhập Không Chỉ Bao Gồm Chữ Và Số!!!');
            		return false;
            	}
            	if( pass.length < 6 ){
            		alert('Mật Khẩu Phải Từ 6 Ký Tự Trở Lên.');
            		return false;
            	}
            	if( pass != rePass){
            		alert('Mật Khẩu Không Trùng Khớp.');
            		return false;
            	}
            });
        },
    }

});