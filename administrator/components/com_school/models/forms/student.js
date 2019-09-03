jQuery(function() {
    document.formvalidator.setHandler('fname',
        function (value) {
            regex=/^[^0-9]+$/;
            return regex.test(value);
        });
});
jQuery(function() {
    document.formvalidator.setHandler('mname',
        function (value) {
            regex=/^[^0-9]+$/;
            return regex.test(value);
        });
});
jQuery(function() {
    document.formvalidator.setHandler('lname',
        function (value) {
            regex=/^[^0-9]+$/;
            return regex.test(value);
        });
});

// jQuery(function() {
//     document.formvalidator.setHandler('email',
//         function (value) {
//             regex=/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
//             return regex.test(value);
//         });
// });
// jQuery(function() {
//     document.formvalidator.setHandler('mobile',
//         function (value) {
//             regex= /^[0-9]*$/;
//             return regex.test(value);
//         });
// });