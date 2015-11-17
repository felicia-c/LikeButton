
jQuery(window).on('FacebookAWDAdmin_ready', function (e, facebookAWDAdmin) {
    facebookAWDAdmin.likebutton = new facebookAWDAdmin.LikeButton();
    facebookAWDAdmin.likebutton.bindEvents();
});