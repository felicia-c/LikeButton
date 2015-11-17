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
                    classes: 'div[id^="facebookawdlikebutton_type_"]',
                    action: 'save_post_type_settings_facebookawdlikebutton'
                },
                {
                    classes: '#facebookawdlikebutton_generator',
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
