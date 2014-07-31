/**
 * FacebookAWDLikeButton
 * 
 * @returns {void}
 */
var FacebookAWDLikeButton = function() {
    this.parent = facebookAWD;
};

/**
 * Anonymous registering the module on global namespace
 * 
 * @param {FacebookAWD} f
 * @returns {void}
 */
(function(f) {
    f.prototype.LikeButton = FacebookAWDLikeButton;
})(FacebookAWD);

/**
 * Init
 */
jQuery(window).on('FacebookAWD_ready', function(e, facebookAWD) {
    facebookAWD.likeButton = new facebookAWD.LikeButton();
    console.log(facebookAWD);
});