  // we need set baseUrl here because this page can have other routes
  require.config({
      baseUrl: '/js'
  });
  require(['require', '../js/common'], function(require, common) {
      require(['require', 'app/app.collectible.create', 'bootstrap'], function(require, MyApp) {
          require(['routers/app.collectible.create.router', 'controllers/app.collectible.create.controller'], function(AppRouter, AppController) {
              /* TODO: we could probably put this in another init file */
              MyApp.appRouter = new AppRouter({
                  controller: new AppController()
              });
              MyApp.start();
          });
      });
  });