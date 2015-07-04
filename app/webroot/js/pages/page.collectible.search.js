// TOOD: turn this into the new app structure
$(function() { 
    var filtersView = new FiltersView();

    filtersView.render();

    var selectedFiltersView = new SelectedFiltersView();
    selectedFiltersView.render();
});