/**
 *
 */
;
(function ($) {
    $(function () {
        $('.tve-api-notice').on('click', '.tve-api-dismiss', function () {
            var $this = $(this);
            $this.parents('.tve-api-notice').fadeOut();
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'tve_api_hide_notice',
                    key: $this.attr('data-key'),
                    nonce: $this.parents('.tve-api-notice').find('span.nonce').text()
                }
            });
        });
    });
})(jQuery);
