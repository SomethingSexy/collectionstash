(function(root, factory) {
    if (typeof exports === 'object') {
        var backbone = require('backbone'),
            marionette = require('marionette');
        dust = require('mustache');
        module.exports = factory(backbone, dust);
    } else if (typeof define === 'function' && define.amd) {
        define(['backbone', 'mustache', 'marionette'], factory);
    }
}(this, function(Backbone, Mustache) {

    Backbone.Marionette.Renderer.render = function(template, data) {
        return Mustache.render(template, data);
    };

}));