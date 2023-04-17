var languages = Array();
if(language =='vi'){
    languages = {
        "type-to-filter":"Nhập để tìm kiếm....",
        "no-data-available-in-table":"Không tìm thấy dữ liệu",
        "showing": "Đang hiển thị",
        "to":"đến",
        "of":"trong tổng",
        "entries":"mục",
        "filtered-from":" Đọc lọc từ",
        "total-entries": "tổng số mục",
        "show":"Hiển thị",
        "loading":"Đang tải...",
        "processing":"Đang xử lý...",
        "search":"Tìm kiếm",
        "no-matching-records-found": "Không tìm thấy dữ liệu tìm kiếm",
        "first":"Đầu trang",
        "next":"Trang kế",
        "last":"Cuối trang",
        "previous":"Trang trước",
        "disabled":"Vô Hiệu",
        "active":"Kích Hoạt",
        "delete":"Xóa",
        "edit":"Chỉnh sửa",
        "view-more": "Xem thêm",
        "approve": "Duyệt",
        "reject": 'Từ chối',
        "mail": 'Gửi thư',
        "are-you-sure-want-to-delete-this-comment": 'Bạn có muốn xóa bình luận này?',
        "delete-comment-success" : 'Xóa bình luận thành công.',
        "delete-comment-fail" : 'Xóa bình luận thất bại. Xin thử lại!',
    	"are-you-sure-want-to-approve-this-comment": 'Bạn có muốn duyệt bình luận này?',
        "approve-comment-success" : 'Duyệt bình luận thành công.',
        "approve-comment-fail" : 'Duyệt bình luận thất bại. Xin thử lại!',
    	"are-you-sure-want-to-reject-this-comment": 'Bạn có muốn từ chối bình luận này?',
        "reject-comment-success" : 'Từ chối bình luận thành công.',
        "reject-comment-fail" : 'Từ chối bình luận thất bại. Xin thử lại!',
        
        "are-you-sure-want-to-delete-this-advertising": 'Bạn có muốn xóa quảng cáo này?',
        "delete-advertising-success" : 'Xóa quảng cáo thành công.',
        "delete-advertising-fail" : 'Xóa quảng cáo thất bại. Xin thử lại!',
        
        "are-you-sure-want-to-delete-this-banner-type": 'Bạn có muốn xóa loại bảng hiệu này?',
        "delete-banner-type-success" : 'Xóa loại bảng hiệu thành công.',
        "delete-banner-type-fail" : 'Xóa loại bảng hiệu thất bại. Xin thử lại!',
        
        "are-you-sure-want-to-delete-this-banner": 'Bạn có muốn xóa bảng hiệu này?',
        "delete-banner-success" : 'Xóa bảng hiệu thành công.',
        "delete-banner-fail" : 'Xóa bảng hiệu thất bại. Xin thử lại!',
        
        "are-you-sure-want-to-delete-this-category": 'Bạn có muốn xóa danh mục này?',
        "delete-category-success" : 'Xóa danh mục thành công.',
        "delete-category-fail" : 'Xóa danh mục thất bại. Xin thử lại!',
        
        "are-you-sure-want-to-delete-this-contact": 'Bạn có muốn xóa liên hệ này?',
        "delete-contact-success" : 'Xóa liên hệ thành công.',
        "delete-contact-fail" : 'Xóa liên hệ thất bại. Xin thử lại!',
        
        "are-you-sure-want-to-delete-this-gallery": 'Bạn có muốn xóa bộ sưu tập này?',
        "delete-gallery-success" : 'Xóa bộ sưu tập thành công.',
        "delete-gallery-fail" : 'Xóa bộ sưu tập thất bại. Xin thử lại!',
        
        "are-you-sure-want-to-delete-this-media": 'Bạn có muốn xóa thư viện ảnh này?',
        "delete-media-success" : 'Xóa thư viện ảnh thành công.',
        "delete-media-fail" : 'Xóa thư viện ảnh thất bại. Xin thử lại!',
        
        "are-you-sure-want-to-delete-this-post": 'Bạn có muốn xóa bài viết này?',
        "delete-post-success" : 'Xóa bài viết thành công.',
        "delete-post-fail" : 'Xóa bài viết thất bại. Xin thử lại!',
        
        "are-you-sure-want-to-delete-this-setting": 'Bạn có muốn xóa cài đặt này?',
        "delete-setting-success" : 'Xóa cài đặt thành công.',
        "delete-setting-fail" : 'Xóa cài đặt thất bại. Xin thử lại!',
        'eat-drink' : 'Eat & Drink',
        'must-do': 'Must Do',
        'festivals-events' : 'Festivals Events',
        'stay' : 'Stay',
        'visitors-guide': 'Visitors Guide',
        'transportation': 'Transportation',
        'news': 'News',
        'plan-your-trip': 'Plan Your Trip',
        'gallery': 'Gallery',
        'vietnamese': 'Tiếng Việt',
        'english': 'Tiếng Anh',
        'name': 'Tên: ',
        'time': 'Thời gian: ',
        'place': 'Địa điểm: ',
        
        'are-you-sure-want-to-delete-this-info': 'Bạn có muốn xóa thông tin này?',
        'delete-info-success': 'Xóa thông tin thành công!',
        'delete-info-fail': 'Xóa thông tin thất bại!',
        
        'are-you-sure-want-to-delete-this-product': 'Bạn có muốn xóa sản phẩm này?',
        'delete-product-success': 'Xóa sản phẩm thành công!',
        'delete-product-fail': 'Xóa sản phẩm thất bại!'
}
}else{
    languages = {
        
    }
}
function translate(text) {
    if (languages[text]) {
        var message = languages[text];        
        return message;
    } else {
        return "Error! This text '" + text + "' have not translated.";
    }
}
