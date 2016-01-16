/*! Thrive Leads - The ultimate Lead Capture solution for wordpress - 2015-12-02
* https://thrivethemes.com 
* Copyright (c) 2015 * Thrive Themes */
var ThriveLeads=ThriveLeads||{};jQuery(function(){ThriveLeads.objects.titleChanger=new ThriveLeads.models.PageTitle({default_title:document.title}),ThriveLeads.objects.titleChanger.on("title_change",function(a){document.title=a});var a=Backbone.Router.extend({routes:{reporting:"reporting"},reporting:function(){new ThriveLeads.views.Reporting}});ThriveLeads.router=new a,Backbone.history.start({hashChange:!0})});