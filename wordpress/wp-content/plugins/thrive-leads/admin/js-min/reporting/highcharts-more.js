/*! Thrive Leads - The ultimate Lead Capture solution for wordpress - 2015-11-02
* https://thrivethemes.com 
* Copyright (c) 2015 * Thrive Themes */
!function(a,b){function c(a,b,c){this.init.call(this,a,b,c)}var d=a.arrayMin,e=a.arrayMax,f=a.each,g=a.extend,h=a.merge,i=a.map,j=a.pick,k=a.pInt,l=a.getOptions().plotOptions,m=a.seriesTypes,n=a.extendClass,o=a.splat,p=a.wrap,q=a.Axis,r=a.Tick,s=a.Point,t=a.Pointer,u=a.CenteredSeriesMixin,v=a.TrackerMixin,w=a.Series,x=Math,y=x.round,z=x.floor,A=x.max,B=a.Color,C=function(){};g(c.prototype,{init:function(a,b,c){var d=this,e=d.defaultOptions;d.chart=b,b.angular&&(e.background={}),d.options=a=h(e,a),(a=a.background)&&f([].concat(o(a)).reverse(),function(a){var b=a.backgroundColor,a=h(d.defaultBackgroundOptions,a);b&&(a.backgroundColor=b),a.color=a.backgroundColor,c.options.plotBands.unshift(a)})},defaultOptions:{center:["50%","50%"],size:"85%",startAngle:0},defaultBackgroundOptions:{shape:"circle",borderWidth:1,borderColor:"silver",backgroundColor:{linearGradient:{x1:0,y1:0,x2:0,y2:1},stops:[[0,"#FFF"],[1,"#DDD"]]},from:-Number.MAX_VALUE,innerRadius:0,to:Number.MAX_VALUE,outerRadius:"105%"}});var D=q.prototype,r=r.prototype,E={getOffset:C,redraw:function(){this.isDirty=!1},render:function(){this.isDirty=!1},setScale:C,setCategories:C,setTitle:C},F={isRadial:!0,defaultRadialGaugeOptions:{labels:{align:"center",x:0,y:null},minorGridLineWidth:0,minorTickInterval:"auto",minorTickLength:10,minorTickPosition:"inside",minorTickWidth:1,tickLength:10,tickPosition:"inside",tickWidth:2,title:{rotation:0},zIndex:2},defaultRadialXOptions:{gridLineWidth:1,labels:{align:null,distance:15,x:0,y:null},maxPadding:0,minPadding:0,showLastLabel:!1,tickLength:0},defaultRadialYOptions:{gridLineInterpolation:"circle",labels:{align:"right",x:-3,y:-2},showLastLabel:!1,title:{x:4,text:null,rotation:90}},setOptions:function(a){a=this.options=h(this.defaultOptions,this.defaultRadialOptions,a),a.plotBands||(a.plotBands=[])},getOffset:function(){D.getOffset.call(this),this.chart.axisOffset[this.side]=0,this.center=this.pane.center=u.getCenter.call(this.pane)},getLinePath:function(a,b){var c=this.center,b=j(b,c[2]/2-this.offset);return this.chart.renderer.symbols.arc(this.left+c[0],this.top+c[1],b,b,{start:this.startAngleRad,end:this.endAngleRad,open:!0,innerR:0})},setAxisTranslation:function(){D.setAxisTranslation.call(this),this.center&&(this.transA=this.isCircular?(this.endAngleRad-this.startAngleRad)/(this.max-this.min||1):this.center[2]/2/(this.max-this.min||1),this.minPixelPadding=this.isXAxis?this.transA*this.minPointOffset:0)},beforeSetTickPositions:function(){this.autoConnect&&(this.max+=this.categories&&1||this.pointRange||this.closestPointRange||0)},setAxisSize:function(){D.setAxisSize.call(this),this.isRadial&&(this.center=this.pane.center=a.CenteredSeriesMixin.getCenter.call(this.pane),this.isCircular&&(this.sector=this.endAngleRad-this.startAngleRad),this.len=this.width=this.height=this.center[2]*j(this.sector,1)/2)},getPosition:function(a,b){return this.postTranslate(this.isCircular?this.translate(a):0,j(this.isCircular?b:this.translate(a),this.center[2]/2)-this.offset)},postTranslate:function(a,b){var c=this.chart,d=this.center,a=this.startAngleRad+a;return{x:c.plotLeft+d[0]+Math.cos(a)*b,y:c.plotTop+d[1]+Math.sin(a)*b}},getPlotBandPath:function(a,b,c){var d,e=this.center,f=this.startAngleRad,g=e[2]/2,h=[j(c.outerRadius,"100%"),c.innerRadius,j(c.thickness,10)],l=/%$/,m=this.isCircular;return"polygon"===this.options.gridLineInterpolation?e=this.getPlotLinePath(a).concat(this.getPlotLinePath(b,!0)):(m||(h[0]=this.translate(a),h[1]=this.translate(b)),h=i(h,function(a){return l.test(a)&&(a=k(a,10)*g/100),a}),"circle"!==c.shape&&m?(a=f+this.translate(a),b=f+this.translate(b)):(a=-Math.PI/2,b=1.5*Math.PI,d=!0),e=this.chart.renderer.symbols.arc(this.left+e[0],this.top+e[1],h[0],h[0],{start:a,end:b,innerR:j(h[1],h[0]-h[2]),open:d})),e},getPlotLinePath:function(a,b){var c,d,e,g=this,h=g.center,i=g.chart,j=g.getPosition(a);return g.isCircular?e=["M",h[0]+i.plotLeft,h[1]+i.plotTop,"L",j.x,j.y]:"circle"===g.options.gridLineInterpolation?(a=g.translate(a))&&(e=g.getLinePath(0,a)):(f(i.xAxis,function(a){a.pane===g.pane&&(c=a)}),e=[],a=g.translate(a),h=c.tickPositions,c.autoConnect&&(h=h.concat([h[0]])),b&&(h=[].concat(h).reverse()),f(h,function(b,f){d=c.getPosition(b,a),e.push(f?"L":"M",d.x,d.y)})),e},getTitlePosition:function(){var a=this.center,b=this.chart,c=this.options.title;return{x:b.plotLeft+a[0]+(c.x||0),y:b.plotTop+a[1]-{high:.5,middle:.25,low:0}[c.align]*a[2]+(c.y||0)}}};p(D,"init",function(a,d,e){var f,i,k,l=d.angular,m=d.polar,n=e.isX,p=l&&n;k=d.options;var q=e.pane||0;l?(g(this,p?E:F),(i=!n)&&(this.defaultRadialOptions=this.defaultRadialGaugeOptions)):m&&(g(this,F),this.defaultRadialOptions=(i=n)?this.defaultRadialXOptions:h(this.defaultYAxisOptions,this.defaultRadialYOptions)),a.call(this,d,e),p||!l&&!m||(a=this.options,d.panes||(d.panes=[]),this.pane=(f=d.panes[q]=d.panes[q]||new c(o(k.pane)[q],d,this),q=f),q=q.options,d.inverted=!1,k.chart.zoomType=null,this.startAngleRad=d=(q.startAngle-90)*Math.PI/180,this.endAngleRad=k=(j(q.endAngle,q.startAngle+360)-90)*Math.PI/180,this.offset=a.offset||0,(this.isCircular=i)&&e.max===b&&k-d===2*Math.PI&&(this.autoConnect=!0))}),p(r,"getPosition",function(a,b,c,d,e){var f=this.axis;return f.getPosition?f.getPosition(c):a.call(this,b,c,d,e)}),p(r,"getLabelPosition",function(a,b,c,d,e,f,g,h,i){var k=this.axis,l=f.y,m=f.align,n=(k.translate(this.pos)+k.startAngleRad+Math.PI/2)/Math.PI*180%360;return k.isRadial?(a=k.getPosition(this.pos,k.center[2]/2+j(f.distance,-25)),"auto"===f.rotation?d.attr({rotation:n}):null===l&&(l=k.chart.renderer.fontMetrics(d.styles.fontSize).b-d.getBBox().height/2),null===m&&(m=k.isCircular?n>20&&160>n?"left":n>200&&340>n?"right":"center":"center",d.attr({align:m})),a.x+=f.x,a.y+=l):a=a.call(this,b,c,d,e,f,g,h,i),a}),p(r,"getMarkPath",function(a,b,c,d,e,f,g){var h=this.axis;return h.isRadial?(a=h.getPosition(this.pos,h.center[2]/2+d),b=["M",b,c,"L",a.x,a.y]):b=a.call(this,b,c,d,e,f,g),b}),l.arearange=h(l.area,{lineWidth:1,marker:null,threshold:null,tooltip:{pointFormat:'<span style="color:{series.color}">●</span> {series.name}: <b>{point.low}</b> - <b>{point.high}</b><br/>'},trackByArea:!0,dataLabels:{align:null,verticalAlign:null,xLow:0,xHigh:0,yLow:0,yHigh:0},states:{hover:{halo:!1}}}),m.arearange=n(m.area,{type:"arearange",pointArrayMap:["low","high"],toYData:function(a){return[a.low,a.high]},pointValKey:"low",getSegments:function(){var a=this;f(a.points,function(b){a.options.connectNulls||null!==b.low&&null!==b.high?null===b.low&&null!==b.high&&(b.y=b.high):b.y=null}),w.prototype.getSegments.call(this)},translate:function(){var a=this.yAxis;m.area.prototype.translate.apply(this),f(this.points,function(b){var c=b.low,d=b.high,e=b.plotY;null===d&&null===c?b.y=null:null===c?(b.plotLow=b.plotY=null,b.plotHigh=a.translate(d,0,1,0,1)):null===d?(b.plotLow=e,b.plotHigh=null):(b.plotLow=e,b.plotHigh=a.translate(d,0,1,0,1))})},getSegmentPath:function(a){var b,c,d,e=[],f=a.length,g=w.prototype.getSegmentPath;d=this.options;var h=d.step;for(b=HighchartsAdapter.grep(a,function(a){return null!==a.plotLow});f--;)c=a[f],null!==c.plotHigh&&e.push({plotX:c.plotX,plotY:c.plotHigh});return a=g.call(this,b),h&&(h===!0&&(h="left"),d.step={left:"right",center:"center",right:"left"}[h]),e=g.call(this,e),d.step=h,d=[].concat(a,e),e[0]="L",this.areaPath=this.areaPath.concat(a,e),d},drawDataLabels:function(){var a,b,c=this.data,d=c.length,e=[],f=w.prototype,g=this.options.dataLabels,h=g.align,i=this.chart.inverted;if(g.enabled||this._hasPointLabels){for(a=d;a--;)b=c[a],b.y=b.high,b._plotY=b.plotY,b.plotY=b.plotHigh,e[a]=b.dataLabel,b.dataLabel=b.dataLabelUpper,b.below=!1,i?(h||(g.align="left"),g.x=g.xHigh):g.y=g.yHigh;for(f.drawDataLabels&&f.drawDataLabels.apply(this,arguments),a=d;a--;)b=c[a],b.dataLabelUpper=b.dataLabel,b.dataLabel=e[a],b.y=b.low,b.plotY=b._plotY,b.below=!0,i?(h||(g.align="right"),g.x=g.xLow):g.y=g.yLow;f.drawDataLabels&&f.drawDataLabels.apply(this,arguments)}g.align=h},alignDataLabel:function(){m.column.prototype.alignDataLabel.apply(this,arguments)},getSymbol:C,drawPoints:C}),l.areasplinerange=h(l.arearange),m.areasplinerange=n(m.arearange,{type:"areasplinerange",getPointSpline:m.spline.prototype.getPointSpline}),function(){var a=m.column.prototype;l.columnrange=h(l.column,l.arearange,{lineWidth:1,pointRange:null}),m.columnrange=n(m.arearange,{type:"columnrange",translate:function(){var b,c=this,d=c.yAxis;a.translate.apply(c),f(c.points,function(a){var e,f=a.shapeArgs,g=c.options.minPointLength;a.tooltipPos=null,a.plotHigh=b=d.translate(a.high,0,1,0,1),a.plotLow=a.plotY,e=b,a=a.plotY-b,g>a&&(g-=a,a+=g,e-=g/2),f.height=a,f.y=e})},trackerGroups:["group","dataLabelsGroup"],drawGraph:C,pointAttrToOptions:a.pointAttrToOptions,drawPoints:a.drawPoints,drawTracker:a.drawTracker,animate:a.animate,getColumnMetrics:a.getColumnMetrics})}(),l.gauge=h(l.line,{dataLabels:{enabled:!0,defer:!1,y:15,borderWidth:1,borderColor:"silver",borderRadius:3,crop:!1,style:{fontWeight:"bold"},verticalAlign:"top",zIndex:2},dial:{},pivot:{},tooltip:{headerFormat:""},showInLegend:!1}),v={type:"gauge",pointClass:n(s,{setState:function(a){this.state=a}}),angular:!0,drawGraph:C,fixedBox:!0,forceDL:!0,trackerGroups:["group","dataLabelsGroup"],translate:function(){var a=this.yAxis,b=this.options,c=a.center;this.generatePoints(),f(this.points,function(d){var e=h(b.dial,d.dial),f=k(j(e.radius,80))*c[2]/200,g=k(j(e.baseLength,70))*f/100,i=k(j(e.rearLength,10))*f/100,l=e.baseWidth||3,m=e.topWidth||1,n=b.overshoot,o=a.startAngleRad+a.translate(d.y,null,null,null,!0);n&&"number"==typeof n?(n=n/180*Math.PI,o=Math.max(a.startAngleRad-n,Math.min(a.endAngleRad+n,o))):b.wrap===!1&&(o=Math.max(a.startAngleRad,Math.min(a.endAngleRad,o))),o=180*o/Math.PI,d.shapeType="path",d.shapeArgs={d:e.path||["M",-i,-l/2,"L",g,-l/2,f,-m/2,f,m/2,g,l/2,-i,l/2,"z"],translateX:c[0],translateY:c[1],rotation:o},d.plotX=c[0],d.plotY=c[1]})},drawPoints:function(){var a=this,b=a.yAxis.center,c=a.pivot,d=a.options,e=d.pivot,g=a.chart.renderer;f(a.points,function(b){var c=b.graphic,e=b.shapeArgs,f=e.d,i=h(d.dial,b.dial);c?(c.animate(e),e.d=f):b.graphic=g[b.shapeType](e).attr({stroke:i.borderColor||"none","stroke-width":i.borderWidth||0,fill:i.backgroundColor||"black",rotation:e.rotation}).add(a.group)}),c?c.animate({translateX:b[0],translateY:b[1]}):a.pivot=g.circle(0,0,j(e.radius,5)).attr({"stroke-width":e.borderWidth||0,stroke:e.borderColor||"silver",fill:e.backgroundColor||"black"}).translate(b[0],b[1]).add(a.group)},animate:function(a){var b=this;a||(f(b.points,function(a){var c=a.graphic;c&&(c.attr({rotation:180*b.yAxis.startAngleRad/Math.PI}),c.animate({rotation:a.shapeArgs.rotation},b.options.animation))}),b.animate=null)},render:function(){this.group=this.plotGroup("group","series",this.visible?"visible":"hidden",this.options.zIndex,this.chart.seriesGroup),w.prototype.render.call(this),this.group.clip(this.chart.clipRect)},setData:function(a,b){w.prototype.setData.call(this,a,!1),this.processData(),this.generatePoints(),j(b,!0)&&this.chart.redraw()},drawTracker:v&&v.drawTrackerPoint},m.gauge=n(m.line,v),l.boxplot=h(l.column,{fillColor:"#FFFFFF",lineWidth:1,medianWidth:2,states:{hover:{brightness:-.3}},threshold:null,tooltip:{pointFormat:'<span style="color:{series.color}">●</span> <b> {series.name}</b><br/>Maximum: {point.high}<br/>Upper quartile: {point.q3}<br/>Median: {point.median}<br/>Lower quartile: {point.q1}<br/>Minimum: {point.low}<br/>'},whiskerLength:"50%",whiskerWidth:2}),m.boxplot=n(m.column,{type:"boxplot",pointArrayMap:["low","q1","median","q3","high"],toYData:function(a){return[a.low,a.q1,a.median,a.q3,a.high]},pointValKey:"high",pointAttrToOptions:{fill:"fillColor",stroke:"color","stroke-width":"lineWidth"},drawDataLabels:C,translate:function(){var a=this.yAxis,b=this.pointArrayMap;m.column.prototype.translate.apply(this),f(this.points,function(c){f(b,function(b){null!==c[b]&&(c[b+"Plot"]=a.translate(c[b],0,1,0,1))})})},drawPoints:function(){var a,c,d,e,g,h,i,k,l,m,n,o,p,q,r,s,t,u,v,w,x,A,B=this,C=B.points,D=B.options,E=B.chart.renderer,F=B.doQuartiles!==!1,G=parseInt(B.options.whiskerLength,10)/100;f(C,function(f){l=f.graphic,x=f.shapeArgs,n={},q={},s={},A=f.color||B.color,f.plotY!==b&&(a=f.pointAttr[f.selected?"selected":""],t=x.width,u=z(x.x),v=u+t,w=y(t/2),c=z(F?f.q1Plot:f.lowPlot),d=z(F?f.q3Plot:f.lowPlot),e=z(f.highPlot),g=z(f.lowPlot),n.stroke=f.stemColor||D.stemColor||A,n["stroke-width"]=j(f.stemWidth,D.stemWidth,D.lineWidth),n.dashstyle=f.stemDashStyle||D.stemDashStyle,q.stroke=f.whiskerColor||D.whiskerColor||A,q["stroke-width"]=j(f.whiskerWidth,D.whiskerWidth,D.lineWidth),s.stroke=f.medianColor||D.medianColor||A,s["stroke-width"]=j(f.medianWidth,D.medianWidth,D.lineWidth),s["stroke-linecap"]="round",i=n["stroke-width"]%2/2,k=u+w+i,m=["M",k,d,"L",k,e,"M",k,c,"L",k,g],F&&(i=a["stroke-width"]%2/2,k=z(k)+i,c=z(c)+i,d=z(d)+i,u+=i,v+=i,o=["M",u,d,"L",u,c,"L",v,c,"L",v,d,"L",u,d,"z"]),G&&(i=q["stroke-width"]%2/2,e+=i,g+=i,p=["M",k-w*G,e,"L",k+w*G,e,"M",k-w*G,g,"L",k+w*G,g]),i=s["stroke-width"]%2/2,h=y(f.medianPlot)+i,r=["M",u,h,"L",v,h],l?(f.stem.animate({d:m}),G&&f.whiskers.animate({d:p}),F&&f.box.animate({d:o}),f.medianShape.animate({d:r})):(f.graphic=l=E.g().add(B.group),f.stem=E.path(m).attr(n).add(l),G&&(f.whiskers=E.path(p).attr(q).add(l)),F&&(f.box=E.path(o).attr(a).add(l)),f.medianShape=E.path(r).attr(s).add(l)))})}}),l.errorbar=h(l.boxplot,{color:"#000000",grouping:!1,linkedTo:":previous",tooltip:{pointFormat:'<span style="color:{series.color}">●</span> {series.name}: <b>{point.low}</b> - <b>{point.high}</b><br/>'},whiskerWidth:null}),m.errorbar=n(m.boxplot,{type:"errorbar",pointArrayMap:["low","high"],toYData:function(a){return[a.low,a.high]},pointValKey:"high",doQuartiles:!1,drawDataLabels:m.arearange?m.arearange.prototype.drawDataLabels:C,getColumnMetrics:function(){return this.linkedParent&&this.linkedParent.columnMetrics||m.column.prototype.getColumnMetrics.call(this)}}),l.waterfall=h(l.column,{lineWidth:1,lineColor:"#333",dashStyle:"dot",borderColor:"#333",states:{hover:{lineWidthPlus:0}}}),m.waterfall=n(m.column,{type:"waterfall",upColorProp:"fill",pointArrayMap:["low","y"],pointValKey:"y",init:function(a,b){b.stacking=!0,m.column.prototype.init.call(this,a,b)},translate:function(){var a,b,c,d,e,f,g,h,i,j,k=this.yAxis;for(a=this.options.threshold,m.column.prototype.translate.apply(this),h=i=a,c=this.points,b=0,a=c.length;a>b;b++)d=c[b],e=d.shapeArgs,f=this.getStack(b),j=f.points[this.index+","+b],isNaN(d.y)&&(d.y=this.yData[b]),g=A(h,h+d.y)+j[0],e.y=k.translate(g,0,1),d.isSum?(e.y=k.translate(j[1],0,1),e.height=k.translate(j[0],0,1)-e.y):d.isIntermediateSum?(e.y=k.translate(j[1],0,1),e.height=k.translate(i,0,1)-e.y,i=j[1]):h+=f.total,e.height<0&&(e.y+=e.height,e.height*=-1),d.plotY=e.y=y(e.y)-this.borderWidth%2/2,e.height=A(y(e.height),.001),d.yBottom=e.y+e.height,e=d.plotY+(d.negative?e.height:0),this.chart.inverted?d.tooltipPos[0]=k.len-e:d.tooltipPos[1]=e},processData:function(a){var b,c,d,e,f,g,h,i=this.yData,j=this.points,k=i.length;for(d=c=e=f=this.options.threshold||0,h=0;k>h;h++)g=i[h],b=j&&j[h]?j[h]:{},"sum"===g||b.isSum?i[h]=d:"intermediateSum"===g||b.isIntermediateSum?i[h]=c:(d+=g,c+=g),e=Math.min(d,e),f=Math.max(d,f);w.prototype.processData.call(this,a),this.dataMin=e,this.dataMax=f},toYData:function(a){return a.isSum?0===a.x?null:"sum":a.isIntermediateSum?0===a.x?null:"intermediateSum":a.y},getAttribs:function(){m.column.prototype.getAttribs.apply(this,arguments);var b=this.options,c=b.states,d=b.upColor||this.color,b=a.Color(d).brighten(.1).get(),e=h(this.pointAttr),g=this.upColorProp;e[""][g]=d,e.hover[g]=c.hover.upColor||b,e.select[g]=c.select.upColor||d,f(this.points,function(a){a.y>0&&!a.color&&(a.pointAttr=e,a.color=d)})},getGraphPath:function(){var a,b,c,d=this.data,e=d.length,f=y(this.options.lineWidth+this.borderWidth)%2/2,g=[];for(c=1;e>c;c++)b=d[c].shapeArgs,a=d[c-1].shapeArgs,b=["M",a.x+a.width,a.y+f,"L",b.x,a.y+f],d[c-1].y<0&&(b[2]+=a.height,b[5]+=a.height),g=g.concat(b);return g},getExtremes:C,getStack:function(a){var b=this.yAxis.stacks,c=this.stackKey;return this.processedYData[a]<this.options.threshold&&(c="-"+c),b[c][a]},drawGraph:w.prototype.drawGraph}),l.bubble=h(l.scatter,{dataLabels:{formatter:function(){return this.point.z},inside:!0,style:{color:"white",textShadow:"0px 0px 3px black"},verticalAlign:"middle"},marker:{lineColor:null,lineWidth:1},minSize:8,maxSize:"20%",states:{hover:{halo:{size:5}}},tooltip:{pointFormat:"({point.x}, {point.y}), Size: {point.z}"},turboThreshold:0,zThreshold:0}),v=n(s,{haloPath:function(){return s.prototype.haloPath.call(this,this.shapeArgs.r+this.series.options.states.hover.halo.size)}}),m.bubble=n(m.scatter,{type:"bubble",pointClass:v,pointArrayMap:["y","z"],parallelArrays:["x","y","z"],trackerGroups:["group","dataLabelsGroup"],bubblePadding:!0,pointAttrToOptions:{stroke:"lineColor","stroke-width":"lineWidth",fill:"fillColor"},applyOpacity:function(a){var b=this.options.marker,c=j(b.fillOpacity,.5),a=a||b.fillColor||this.color;return 1!==c&&(a=B(a).setOpacity(c).get("rgba")),a},convertAttribs:function(){var a=w.prototype.convertAttribs.apply(this,arguments);return a.fill=this.applyOpacity(a.fill),a},getRadii:function(a,b,c,d){var e,f,g,h=this.zData,i=[],j="width"!==this.options.sizeBy;for(f=0,e=h.length;e>f;f++)g=b-a,g=g>0?(h[f]-a)/(b-a):.5,j&&g>=0&&(g=Math.sqrt(g)),i.push(x.ceil(c+g*(d-c))/2);this.radii=i},animate:function(a){var b=this.options.animation;a||(f(this.points,function(a){var c=a.graphic,a=a.shapeArgs;c&&a&&(c.attr("r",1),c.animate({r:a.r},b))}),this.animate=null)},translate:function(){var a,c,d,e=this.data,f=this.radii;for(m.scatter.prototype.translate.call(this),a=e.length;a--;)c=e[a],d=f?f[a]:0,c.negative=c.z<(this.options.zThreshold||0),d>=this.minPxSize/2?(c.shapeType="circle",c.shapeArgs={x:c.plotX,y:c.plotY,r:d},c.dlBox={x:c.plotX-d,y:c.plotY-d,width:2*d,height:2*d}):c.shapeArgs=c.plotY=c.dlBox=b},drawLegendSymbol:function(a,b){var c=k(a.itemStyle.fontSize)/2;b.legendSymbol=this.chart.renderer.circle(c,a.baseline-c,c).attr({zIndex:3}).add(b.legendGroup),b.legendSymbol.isMarker=!0},drawPoints:m.column.prototype.drawPoints,alignDataLabel:m.column.prototype.alignDataLabel}),q.prototype.beforePadding=function(){var a=this,c=this.len,g=this.chart,h=0,i=c,l=this.isXAxis,m=l?"xData":"yData",n=this.min,o={},p=x.min(g.plotWidth,g.plotHeight),q=Number.MAX_VALUE,r=-Number.MAX_VALUE,s=this.max-n,t=c/s,u=[];this.tickPositions&&(f(this.series,function(b){var c=b.options;!b.bubblePadding||!b.visible&&g.options.chart.ignoreHiddenSeries||(a.allowZoomOutside=!0,u.push(b),l&&(f(["minSize","maxSize"],function(a){var b=c[a],d=/%$/.test(b),b=k(b);o[a]=d?p*b/100:b}),b.minPxSize=o.minSize,b=b.zData,b.length&&(q=j(c.zMin,x.min(q,x.max(d(b),c.displayNegative===!1?c.zThreshold:-Number.MAX_VALUE))),r=j(c.zMax,x.max(r,e(b))))))}),f(u,function(a){var b,c=a[m],d=c.length;if(l&&a.getRadii(q,r,o.minSize,o.maxSize),s>0)for(;d--;)"number"==typeof c[d]&&(b=a.radii[d],h=Math.min((c[d]-n)*t-b,h),i=Math.max((c[d]-n)*t+b,i))}),u.length&&s>0&&j(this.options.min,this.userMin)===b&&j(this.options.max,this.userMax)===b&&(i-=c,t*=(c+h-i)/c,this.min+=h/t,this.max+=i/t))},function(){function a(a,b,c){a.call(this,b,c),this.chart.polar&&(this.closeSegment=function(a){var b=this.xAxis.center;a.push("L",b[0],b[1])},this.closedStacks=!0)}function b(a,b){var c=this.chart,d=this.options.animation,e=this.group,f=this.markerGroup,g=this.xAxis.center,h=c.plotLeft,i=c.plotTop;c.polar?c.renderer.isSVG&&(d===!0&&(d={}),b?(c={translateX:g[0]+h,translateY:g[1]+i,scaleX:.001,scaleY:.001},e.attr(c),f&&f.attr(c)):(c={translateX:h,translateY:i,scaleX:1,scaleY:1},e.animate(c,d),f&&f.animate(c,d),this.animate=null)):a.call(this,b)}var c,d=w.prototype,e=t.prototype;d.toXY=function(a){var b,c=this.chart,d=a.plotX;b=a.plotY,a.rectPlotX=d,a.rectPlotY=b,d=(d/Math.PI*180+this.xAxis.pane.options.startAngle)%360,0>d&&(d+=360),a.clientX=d,b=this.xAxis.postTranslate(a.plotX,this.yAxis.len-b),a.plotX=a.polarPlotX=b.x-c.plotLeft,a.plotY=a.polarPlotY=b.y-c.plotTop},d.orderTooltipPoints=function(a){this.chart.polar&&(a.sort(function(a,b){return a.clientX-b.clientX}),a[0])&&(a[0].wrappedClientX=a[0].clientX+360,a.push(a[0]))},m.area&&p(m.area.prototype,"init",a),m.areaspline&&p(m.areaspline.prototype,"init",a),m.spline&&p(m.spline.prototype,"getPointSpline",function(a,b,c,d){var e,f,g,h,i,j,k;return this.chart.polar?(e=c.plotX,f=c.plotY,a=b[d-1],g=b[d+1],this.connectEnds&&(a||(a=b[b.length-2]),g||(g=b[1])),a&&g&&(h=a.plotX,i=a.plotY,b=g.plotX,j=g.plotY,h=(1.5*e+h)/2.5,i=(1.5*f+i)/2.5,g=(1.5*e+b)/2.5,k=(1.5*f+j)/2.5,b=Math.sqrt(Math.pow(h-e,2)+Math.pow(i-f,2)),j=Math.sqrt(Math.pow(g-e,2)+Math.pow(k-f,2)),h=Math.atan2(i-f,h-e),i=Math.atan2(k-f,g-e),k=Math.PI/2+(h+i)/2,Math.abs(h-k)>Math.PI/2&&(k-=Math.PI),h=e+Math.cos(k)*b,i=f+Math.sin(k)*b,g=e+Math.cos(Math.PI+k)*j,k=f+Math.sin(Math.PI+k)*j,c.rightContX=g,c.rightContY=k),d?(c=["C",a.rightContX||a.plotX,a.rightContY||a.plotY,h||e,i||f,e,f],a.rightContX=a.rightContY=null):c=["M",e,f]):c=a.call(this,b,c,d),c}),p(d,"translate",function(a){if(a.call(this),this.chart.polar&&!this.preventPostTranslate)for(var a=this.points,b=a.length;b--;)this.toXY(a[b])}),p(d,"getSegmentPath",function(a,b){var c=this.points;return this.chart.polar&&this.options.connectEnds!==!1&&b[b.length-1]===c[c.length-1]&&null!==c[0].y&&(this.connectEnds=!0,b=[].concat(b,[c[0]])),a.call(this,b)}),p(d,"animate",b),p(d,"setTooltipPoints",function(a,b){return this.chart.polar&&g(this.xAxis,{tooltipLen:360}),a.call(this,b)}),m.column&&(c=m.column.prototype,p(c,"animate",b),p(c,"translate",function(a){var b,c,d=this.xAxis,e=this.yAxis.len,f=d.center,g=d.startAngleRad,h=this.chart.renderer;if(this.preventPostTranslate=!0,a.call(this),d.isRadial)for(d=this.points,c=d.length;c--;)b=d[c],a=b.barX+g,b.shapeType="path",b.shapeArgs={d:h.symbols.arc(f[0],f[1],e-b.plotY,null,{start:a,end:a+b.pointWidth,innerR:e-j(b.yBottom,e)})},this.toXY(b),b.tooltipPos=[b.plotX,b.plotY],b.ttBelow=b.plotY>f[1]}),p(c,"alignDataLabel",function(a,b,c,e,f,g){this.chart.polar?(a=b.rectPlotX/Math.PI*180,null===e.align&&(e.align=a>20&&160>a?"left":a>200&&340>a?"right":"center"),null===e.verticalAlign&&(e.verticalAlign=45>a||a>315?"bottom":a>135&&225>a?"top":"middle"),d.alignDataLabel.call(this,b,c,e,f,g)):a.call(this,b,c,e,f,g)})),p(e,"getIndex",function(a,b){var c,d,e=this.chart;return e.polar?(d=e.xAxis[0].center,c=b.chartX-d[0]-e.plotLeft,e=b.chartY-d[1]-e.plotTop,c=180-Math.round(Math.atan2(c,e)/Math.PI*180)):c=a.call(this,b),c}),p(e,"getCoordinates",function(a,b){var c=this.chart,d={xAxis:[],yAxis:[]};return c.polar?f(c.axes,function(a){var e=a.isXAxis,f=a.center,g=b.chartX-f[0]-c.plotLeft,f=b.chartY-f[1]-c.plotTop;d[e?"xAxis":"yAxis"].push({axis:a,value:a.translate(e?Math.PI-Math.atan2(g,f):Math.sqrt(Math.pow(g,2)+Math.pow(f,2)),!0)})}):d=a.call(this,b),d})}()}(Highcharts);