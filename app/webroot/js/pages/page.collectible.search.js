$(function() { 
    var filtersView = new FiltersView();

    filtersView.render();

    var selectedFiltersView = new SelectedFiltersView();
    selectedFiltersView.render();
});