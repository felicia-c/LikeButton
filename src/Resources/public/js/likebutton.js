/*
 * Facebook AWD helpers
 * 
 * LikeButton
 */
(function(FacebookAWD) {

    /**
     * Like Button Admin Helpers
     */
    FacebookAWD.prototype.LikeButton = function() {

    };

})(FacebookAWD);

jQuery(window).on('FacebookAWD_ready', function(e, FacebookAWD) {
    var LikeButton = FacebookAWD.LikeButton;
    var likebutton = new FacebookAWD.LikeButton();
});