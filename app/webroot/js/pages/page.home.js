  // we need set baseUrl here because this page can have other routes
  require.config({
      baseUrl: '/js'
  });
  require(['require', '../js/common'], function(require, common) {
      require(['require', 'app/app.home', 'bootstrap'], function(require, MyApp) {
          require(['routers/app.home.router', 'controllers/app.home.controller'], function(AppRouter, AppController) {
              MyApp.appRouter = new AppRouter({
                  controller: new AppController()
              });
              MyApp.start();
          });
      });
  });