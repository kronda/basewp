/*! Thrive Clever Widgets 2015-08-19
* http://www.thrivethemes.com 
* Copyright (c) 2015 * Thrive Themes */
var tcw_app=tcw_app||{};!function(){"use strict";tcw_app.Option=Backbone.Model.extend({defaults:{label:"",isChecked:!1,id:"",type:null},validate:function(a){return a.label.length?void 0:(alert("Empty links are not accepted !"),"just return something")},toggle:function(){this.set("isChecked",!this.get("isChecked"))},check:function(){this.set("isChecked",!0)},uncheck:function(){this.set("isChecked",!1)}})}(jQuery);