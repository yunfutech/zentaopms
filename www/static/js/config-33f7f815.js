var p=Object.defineProperty,f=Object.defineProperties;var c=Object.getOwnPropertyDescriptors;var r=Object.getOwnPropertySymbols;var h=Object.prototype.hasOwnProperty,d=Object.prototype.propertyIsEnumerable;var s=(o,t,i)=>t in o?p(o,t,{enumerable:!0,configurable:!0,writable:!0,value:i}):o[t]=i,a=(o,t)=>{for(var i in t||(t={}))h.call(t,i)&&s(o,i,t[i]);if(r)for(var i of r(t))d.call(t,i)&&s(o,i,t[i]);return o},e=(o,t)=>f(o,c(t));import{ax as g,al as n}from"./index.js";import{e as l}from"./chartEditStore-3415a942.js";import{I as m}from"./index-142ce6d4.js";import"./plugin-8225b40a.js";import"./icon-2441378a.js";import"./tables_list-71790294.js";import"./SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-05a174c8.js";import"./useTargetData.hook-27b72c1a.js";const u={dataset:"",borderRadius:10};class P extends l{constructor(){super(...arguments),this.key=m.key,this.attr=e(a({},g),{w:1200,h:800,zIndex:-1}),this.chartConfig=n(m),this.option=n(u)}}export{P as default,u as option};