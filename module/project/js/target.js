function changeCate($cateID, $productID) {
    if ($('#cate-'+$cateID).attr('class') == 'cate-active') {
        this.className == ''
        location.href = createLink('project','target','projectID='+$productID);
    } else {
        location.href = createLink('project','target','projectID='+$productID + '&categoryID='+$cateID);
    }
}

$(function() {
    $('.record-more').click(function() {
        var expID = this.id.split('-')[1];
        if ($('.sur-record-' + expID).css('display') == 'none') {
            $('.sur-record-' + expID).css({'display': 'contents'});
            $('#' + this.id).text('隐藏')
        } else {
            $('.sur-record-' + expID).css({'display': 'none'});
            $('#' + this.id).text('展开')
        }
    })
})