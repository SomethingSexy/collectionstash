require(['require', '../js/common'], function(require, common) {
    require(['require', 'app/app.user.settings', 'bootstrap'], function(require, MyApp) {
        require(['routers/app.user.settings.router', 'controllers/app.user.settings.controller'], function(AppRouter, AppController) {
            /* TODO: we could probably put this in another init file */
            MyApp.appRouter = new AppRouter({
                controller: new AppController()
            });
            MyApp.start();
        });
    });
});