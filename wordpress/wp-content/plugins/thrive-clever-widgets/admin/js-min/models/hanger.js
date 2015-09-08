/*! Thrive Clever Widgets 2015-08-19
* http://www.thrivethemes.com 
* Copyright (c) 2015 * Thrive Themes */
var tcw_app=tcw_app||{};!function(){"use strict";tcw_app.Hanger=Backbone.Model.extend({defaults:{identifier:"",tabs:""},initialize:function(a){this.set("tabs",new tcw_app.Tabs(a.tabs))},countCheckedOptions:function(){var a=0;return this.get("tabs").each(function(b){a+=b.countCheckedOptions()}),a},uncheckAll:function(){this.get("tabs").each(function(a){a.uncheckAll()})}})}(jQuery);