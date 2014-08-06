    // we need set baseUrl here because this page can have other routes
    require.config({
        baseUrl: '/js'
    });
    require(['require', '../js/common'], function(require, common) {
        require(['require', 'app/app.collectible.edit'], function(require, MyApp) {
            MyApp.start();
        });
    });