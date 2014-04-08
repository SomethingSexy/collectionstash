var CollectibleView = Backbone.View.extend({
    template: 'collectible.view',
    className: 'test',
    render: function() {
        var self = this;
        var data = this.model.toJSON();
        data.uploadDirectory = uploadDirectory;
        data.isLogggedIn = isLogggedIn;
        if (data.Collectible.description) {
            data.Collectible.description = data.Collectible.description.replace("\\n", "<br />", "g");
        }

        dust.render(this.template, data, function(error, output) {
            $(self.el).html(output);
        });

        return this;
    }
});

function handleResize() {
    var sideBarNavWidth = $('.collectible-detail', '#collectibles-list-component').width() - parseInt($('.collectible-detail .well', '#collectibles-list-component').css('paddingLeft')) - parseInt($('.collectible-detail .well', '#collectibles-list-component').css('paddingRight'));
    $('.collectible-detail .well', '#collectibles-list-component').css('width', sideBarNavWidth);
}

$(function() {
    handleResize();
    $(window).resize(function() {
        handleResize();
    });

    // Get all of the data here
    $.when($.get('/templates/collectibles/collectible.view.dust')).done(function(collectibleTemplate) {
        dust.loadSource(dust.compile(collectibleTemplate, 'collectible.view'));

        var filtersView = new FiltersView();

        filtersView.render();

        var selectedFiltersView = new SelectedFiltersView();
        selectedFiltersView.render();


        $('.collectible', '#collectibles-list-component').on('click', function(event) {
            var collectibleData = $(event.currentTarget).attr('data-collectible');

            $('.collectible-detail .well', '#collectibles-list-component').html(new CollectibleView({
                model: new Backbone.Model(JSON.parse(collectibleData))
            }).render().el);
        });
    });

});