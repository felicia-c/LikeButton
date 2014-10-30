/*
 * Facebook AWD Admin helpers
 * 
 * LikeButton
 */
(function (FacebookAWDAdmin) {

    /**
     * Like Button Admin Helpers
     */
    FacebookAWDAdmin.prototype.LikeButton = function () {

        var $ = jQuery;
        this.parent = facebookAWDAdmin;
        var likeButton = this;

        /**
         * BindEvents of objects
         * @returns {void}
         */
        this.bindEvents = function () {
            /*
             * Listen the settings post type form
             */
            var forms = [{
                    classes: '.section.posttype_section form',
                    action: 'save_settings_facebookawdlikebutton'
                },
                {
                    classes: '.section.shortcode_section_likebutton form',
                    action: 'generator_facebookawdlikebutton'
                }
            ];

            $.each(forms, function (index, form) {
                $(document).on('submit', form.classes, function (e) {
                    e.preventDefault();
                    likeButton.parent.submitSettingsForm($(e.target), form.action);
                });
            });
        };
    };

})(FacebookAWDAdmin);

jQuery(window).on('FacebookAWDAdmin_ready', function (e, facebookAWDAdmin) {
    facebookAWDAdmin.likebutton = new facebookAWDAdmin.LikeButton();
    facebookAWDAdmin.likebutton.bindEvents();
});