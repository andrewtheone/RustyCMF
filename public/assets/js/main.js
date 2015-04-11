$(document).ready(function() {
	requirejs.config({
	    baseUrl: 'assets/plugins',
	});

	$("React").each(function() {

		var r_element = $(this);
		var name = $(this).attr('name');

		requirejs([name], function(element) {
			var _class = React.createClass(element);
			var af = r_element.next();
			//console.log(af);
	        var x = _class();
	        var html = React.renderComponentToString(x);

	        $(html).insertBefore(af);
	        r_element.remove();
	        //$(x).insertAfter(af);
	        /*var a = React.render(
	          x
	        );*/
		})
		/*var el = $("<div>");
		el.html('React element found: '+$(this).attr('name'));
		el.insertAfter($(this));
		$(this).remove();*/
	})
})