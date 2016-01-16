/*! Thrive Leads - The ultimate Lead Capture solution for wordpress - 2015-12-02
* https://thrivethemes.com 
* Copyright (c) 2015 * Thrive Themes */
var ThriveLeads=ThriveLeads||{};jQuery(function(){var a=Backbone.Router.extend({routes:{assets:"assets"},assets:function(){var a=new ThriveLeads.views.Assets({model:ThriveLeads.objects.AssetsWizard,collection:ThriveLeads.objects.AssetsCollection,el:"#tve-asset-delivery"});a.globalSettings=TVE_Page_Data.globalSettings,a.render()}});ThriveLeads.router=new a,Backbone.history.start({hashChange:!0})});