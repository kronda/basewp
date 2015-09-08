/*! Thrive Clever Widgets 2015-08-19
* http://www.thrivethemes.com 
* Copyright (c) 2015 * Thrive Themes */
var tcw_app=tcw_app||{};!function(){"use strict";tcw_app.TabLabelView=Backbone.View.extend({activeClass:"tcw_active_tab",tagName:"li",events:{click:"activate"},initialize:function(){this.listenTo(this.model,"change",this.render)},render:function(){var a=_.template(jQuery("#tab-label-template").html(),{tab:this.model});this.$el.attr("id",this.model.getTabIdFromIdentifier()),this.$el.html(a)},activate:function(){this.model.set({isActive:!0})}})}(jQuery);