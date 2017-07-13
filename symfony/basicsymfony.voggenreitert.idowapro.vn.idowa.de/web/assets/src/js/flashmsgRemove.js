/**
 * Created by voggenre on 12.04.2017.
 */
+function ($) {
    $(document).ready(function () {
        window.setTimeout(
            function () {
                $('.flashmsg').remove();
            },
            5000
        );
    });
}(jQuery);