define(['require', 'jquery', 'blockui'], function(require, $) {

    return {
        onError: function(message) {
            $.blockUI({
                message: '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
                showOverlay: false,
                css: {
                    top: '100px',
                    'background-color': '#DDFADE',
                    border: '1px solid #93C49F',
                    'box-shadow': '3px 3px 5px rgba(0, 0, 0, 0.5)',
                    'border-radius': '4px 4px 4px 4px',
                    color: '#333333',
                    'margin-bottom': '20px',
                    padding: '8px 35px 8px 14px',
                    'text-shadow': '0 1px 0 rgba(255, 255, 255, 0.5)',
                    'z-index': 999999
                },
                timeout: 2000
            });

        },
        onSuccess: function(message) {
            $.blockUI({
                message: '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
                showOverlay: false,
                css: {
                    top: '100px',
                    'background-color': '#DDFADE',
                    border: '1px solid #93C49F',
                    'box-shadow': '3px 3px 5px rgba(0, 0, 0, 0.5)',
                    'border-radius': '4px 4px 4px 4px',
                    color: '#333333',
                    'margin-bottom': '20px',
                    padding: '8px 35px 8px 14px',
                    'text-shadow': '0 1px 0 rgba(255, 255, 255, 0.5)',
                    'z-index': 999999
                },
                timeout: 2000
            });
        }
    };
});