define(function () {
    //Do setup work here

    return {
		render: function() {
			return React.createElement("div", {}, "Contact Form, here you can contact: "+window.pageVars.name); 
		}
    }
});