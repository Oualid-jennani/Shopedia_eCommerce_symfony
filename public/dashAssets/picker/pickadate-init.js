(function($) {
    "use strict"

    //date picker classic default
    $('.datepicker-default').pickadate({
        min: new Date(2015,3,20),
        format: 'dd/mm/yyyy',
        formatSubmit: 'yyyy-mm-dd',
    });

})(jQuery);