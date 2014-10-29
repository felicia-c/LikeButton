(function () {
    tinymce.create('tinymce.plugins.facebookawdlikebutton_shortcode_generator', {
        init: function (ed, url) {
            var imgUrl = url.replace('/js', '/img');
            ed.addButton('facebookawdlikebutton_shortcode_generator', {
                title: 'Like Button',
                image: imgUrl + '/facebook_like_thumb.png',
                onclick: function () {
                    //tb_show("Like Button Shortcode Generator", "/wp-admin/admin.php?page=facebookawdlikebutton&shortcode_generator=1&height=auto&TB_iframe=true");
                    jQuery.post(ajaxurl, {
                        action: 'shortcode_generator_facebookawdlikebutton',
                        facebookawdlikebuttonshortcode_generator: {'fake': true}
                    }, function (data) {
                        ed.execCommand('mceInsertContent', false, data.shortcode);
                    }, "json");
                }
            });
        },
        createControl: function (n, cm) {
            return null;
        },
        getInfo: function () {
            return {
                longname: "Like Button"
            };
        }
    });
    tinymce.PluginManager.add('facebookawdlikebutton_shortcode_generator', tinymce.plugins.facebookawdlikebutton_shortcode_generator);
})();