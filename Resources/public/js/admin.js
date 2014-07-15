/*
 * 
 * Like Button Admin Helpers
 */
(function(FacebookAwdAdmin) {

    /**
     * Like Button Admin Helpers
     */
    FacebookAwdAdmin.prototype.LikeButton = function() {

        var $ = jQuery;
        var likeButton = this;

        /**
         * BindEvents of objects
         * @returns {void}
         */
        this.bindEvents = function() {
            /*
             * Listen the settings post type form
             */
            var $postTypeSettingsForm = $(' .posttype_section form');
            $(document).on('submit', '.posttype_section form', function(e) {
                e.preventDefault();
                likeButton.submitPostTypeSettingsForm($(e.target));
            });
        };

        /**
         * Submit the settings form
         * @param {Object} $form
         * @returns {void}
         */
        this.submitPostTypeSettingsForm = function($form) {
            var data = $form.serialize() + '&action=save_settings_facebookawd_likebutton';
            $.post(ajaxurl, data, function(data) {
                $('.posttype_section.' + data.postTypeName).replaceWith(data.section);
                var $newSection = $('.posttype_section.' + data.postTypeName);
                $('.hideIfOn').trigger('change');
                $('html, body').animate({
                    scrollTop: $newSection.offset().top - 40
                });
            }, 'json');
        };
    };

})(FacebookAwdAdmin);

jQuery(window).on('facebookAwdAdmin_ready', function(e, facebookAwdAdmin) {
    var LikeButton = facebookAwdAdmin.LikeButton;
    var likebutton = new facebookAwdAdmin.LikeButton();
    likebutton.bindEvents();
});