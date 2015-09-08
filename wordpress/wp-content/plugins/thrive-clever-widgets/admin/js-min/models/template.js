/*! Thrive Clever Widgets 2015-08-19
* http://www.thrivethemes.com 
* Copyright (c) 2015 * Thrive Themes */
var tcw_app=tcw_app||{};!function(){tcw_app.Template=Backbone.Model.extend({defaults:{name:"",description:"",hangers:""},initialize:function(a){this.set("hangers",new Backbone.Collection([a.show_widget_options,a.hide_widget_options]))}})}(jQuery);