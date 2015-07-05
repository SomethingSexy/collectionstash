define(['require', 'toastr'], function(require, toastr) {
    return {
        onError: function(message, title) {
            toastr.error(message, title, {
                timeout: 2000
            });
        },
        onSuccess: function(message, title) {
            toastr.success(message, title, {
                timeout: 2000
            });
        }
    };
});