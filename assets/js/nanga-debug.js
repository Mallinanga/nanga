document.querySelector('.nanga-debug-link').addEventListener('click', function (e) {
    // this.classList.toggle('is-showing');
    document.querySelector('#nanga-debug-footer').classList.toggle('is-showing');
    e.preventDefault();
});
(function ($) {
    $(window).load(function () {
        var clear = $('#clear-debug-log');
        if ($('#debug-log').is(':empty')) {
            clear.hide();
        }
        clear.click(function (e) {
            e.preventDefault();
            $('#debug-log').css('opacity', '0.5');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'clear_debug_log'
                }
            })
                .done(function () {
                    $('#debug-log').empty().css('opacity', '1');
                    clear.hide();
                });
        });
    });
})(jQuery);
