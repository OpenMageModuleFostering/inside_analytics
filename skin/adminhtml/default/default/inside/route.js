function showHide(value) {
    switch(value) {
	case 'search':
	    $("route_search_param").up().up().show();
	    $("route_search_param").addClassName('required-entry');
	    break;
	default:
	    $("route_search_param").up().up().hide();
	    $("route_search_param").removeClassName('required-entry');
	    break;
    }
}
Event.observe(window, "load", function () {
    var pageType = $("route_type");
    showHide(pageType.options[pageType.options.selectedIndex].value);
    
    $("route_type").observe("change", function () {
        showHide(this.options[this.options.selectedIndex].value);
    });
});

