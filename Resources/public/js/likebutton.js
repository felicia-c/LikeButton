/*
 * 
 * Like Button Admin Helpers
 */
(function(FacebookAwd) {

    /**
     * Like Button Admin Helpers
     */
    FacebookAwd.prototype.LikeButton = function() {

    };

})(FacebookAwd);

jQuery(window).on('facebookAwd_ready', function(e, facebookAwd) {
    var LikeButton = facebookAwd.LikeButton;
    var likebutton = new facebookAwd.LikeButton();
    console.log(facebookAwd);
    //likebutton.bindEvents();
});