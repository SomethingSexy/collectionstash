  // we need set baseUrl here because this page can have other routes
  require.config({
      baseUrl: '/js'
  });
  require(['require', '../js/common'], function(require, common) {
      require(['require', 'app/app.companies', 'bootstrap'], function(require, MyApp) {
          require(['routers/app.companies.router', 'controllers/app.companies.controller'], function(AppRouter, AppController) {
              /* TODO: we could probably put this in another init file */
              MyApp.appRouter = new AppRouter({
                  controller: new AppController()
              });
              MyApp.start();
          });
      });
  });