/*
Copyright (c) 2008, NHN
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

- Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
- Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
- Neither the name of the NHN nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * AutoSourcing (Opensource ver)
 * @author gony (AjaxUI3 Team)
 */

var AutoSourcing={div:null,id:"",timer:null,regex:null,strings:[],skip:false,handlers:{},init:function(format,enable){var t=this;var n=navigator;var is_safari=(n.userAgent.indexOf("WebKit")>-1&&n.vendor.indexOf("Apple")>-1);this.div=document.createElement("div");this.div.className=(/MSIE|Gecko/.test(n.userAgent)&&!is_safari)?"autosourcing-stub":"autosourcing-stub-extra";this.id="autosourcing_tmp_"+(Math.random()*10000);this.regex=new RegExp(format.replace("%id%","(\\d+)"));this.handlers.copy=function(){t.copy()};this.handlers.keydown=function(evt){t.keydown(evt)};this.handlers.keypress=function(evt){t.keypress(evt)};if(typeof enable=="undefined")enable=true;this.setEnable(enable);},setEnable:function(bool){if(bool){if(typeof document.body.oncopy!="undefined"){this.addEvent(document.body,"copy",this.handlers.copy);}else{this.addEvent(document,"keydown",this.handlers.keydown);this.addEvent(document,"keypress",this.handlers.keypress);this.addEvent(document,"contextmenu",this.handlers.copy);}}else{if(typeof document.body.oncopy!="undefined"){this.removeEvent(document.body,"copy",this.handlers.copy);}else{this.removeEvent(document,"keydown",this.handlers.keydown);this.removeEvent(document,"keypress",this.handlers.keypress);this.removeEvent(document,"contextmenu",this.handlers.copy);}}},getId:function(rng){var range_s,range_e,par,id;range_s=this.cloneRange(rng)
range_s.collapse(true);par=this.getParentElement(range_s);while(par&&par.parentNode){if(par.nodeType==1&&this.regex.test(par.id)){id=parseInt(RegExp.$1);return isNaN(id)?0:id;}
par=par.parentNode;}
range_e=this.cloneRange(rng)
range_e.collapse(false);par=this.getParentElement(range_e);while(par&&par.parentNode){if(par.nodeType==1&&this.regex.test(par.id)){id=parseInt(RegExp.$1);return isNaN(id)?0:id;}
par=par.parentNode;}
return 0;},getSelection:function(){if(window.getSelection){return window.getSelection();}else{return document.selection;}},getRange:function(selection){selection=selection||this.getSelection();if(selection.getRangeAt){return selection.getRangeAt(0);}else{return selection.createRange();}},cloneRange:function(rng){rng=rng||this.getRange();if(rng.duplicate){return rng.duplicate();}else{return rng.cloneRange();}},getParentElement:function(range){var par=range.parentElement?range.parentElement():range.commonAncestorContainer;if(!par)return null;while(par.nodeType!=1){par=par.parentNode;}
return par;},setString:function(id,sTitle,sName,sLink){var data={name:sName,link:sLink,title:sTitle};this.strings[id]=this._template.replace(/\{(\w+)\}/g,function(m0,m1){return data[m1]?data[m1]:"";});},copy:function(evt){var evt=evt||window.event;var self=this;var sel=this.getSelection();var rng=this.getRange(sel);var rngtmp=this.cloneRange(rng);var regcopy=/(?:p|<div)[^<>]+class\s*=\s*"?autosourcing\-stub(?:\-extra)?\-saved(?:\b|")/i;var regtag=/textarea|input/i;var id=this.getId(rng);var node_rng;this.skip=false;clearTimeout(this.timer);if(id==0){try{this.div.parentNode.removeChild(this.div)}catch(e){};return;}
if(evt&&evt.srcElement&&evt.srcElement.tagName.toUpperCase()=="A")return;this.div.innerHTML=this.strings[id];if(window.getSelection){var html=(window.XMLSerializer)?new XMLSerializer().serializeToString(rng.cloneContents()):"";if(regcopy.test(html)||regtag.test(rng.commonAncestorContainer.tagName)){this.skip=true;return;}
rngtmp.collapse(false);rngtmp.insertNode(this.div);if(this.div.nextSibling){rng.setEndBefore(this.div.nextSibling);}else{rng.setEndAfter(this.div);}
sel.removeAllRanges();sel.addRange(rng);}else if(document.selection){var body=document.body||document.getElementsByTagName("body")[0];var div=document.createElement("div");var span=document.createElement("span");span.id=this.id;if(regcopy.test(rng.htmlText)||regtag.test(rng.parentElement().tagName)){this.skip=true;return;}
rngtmp.collapse(false);rngtmp.pasteHTML(span.outerHTML+'&nbsp;<a></a>');span=document.getElementById(this.id);(span||rngtmp.parentElement()).insertAdjacentElement("afterEnd",this.div);do{rng.moveEnd("character",1);(rngtmp=rng.duplicate()).collapse(false);}while(rngtmp.offsetLeft==0);try{rng.select();}catch(e){}
if(span&&span.parentNode){span.parentNode.removeChild(span.nextSibling.nextSibling);span.parentNode.removeChild(span.nextSibling.nextSibling);span.parentNode.removeChild(span);}
if(div.parentNode){div.parentNode.removeChild(div);}}
if(typeof document.body.oncopy!="undefined"){this.timer=setTimeout(function(){self.aftercopy(rng);},1);}},aftercopy:function(rng){if(this.skip)return;try{this.div.parentNode.removeChild(this.div);if(/WebKit/.test(navigator.userAgent)){var sel=this.getSelection();sel.removeAllRanges();sel.addRange(rng);}}catch(e){}},keydown:function(e){if((e.ctrlKey||e.metaKey)&&e.keyCode==67){this.copy();}},keypress:function(e){if((e.ctrlKey||e.metaKey)&&e.keyCode==67){var t=this;this.timer=setTimeout(function(){t.aftercopy();},10);}},mousedown:function(e){},setTemplate:function(html){this._template=html;},addEvent:function(obj,sEvent,pFunc){function addEventIE(obj,sEvent,pFunc){obj.attachEvent("on"+sEvent,pFunc);}
function addEventFF(obj,sEvent,pFunc){obj.addEventListener(sEvent,pFunc,false);}
if(obj.attachEvent)this.addEvent=addEventIE;else this.addEvent=addEventFF;this.addEvent(obj,sEvent,pFunc);},removeEvent:function(obj,sEvent,pFunc){function removeEventIE(obj,sEvent,pFunc){obj.detachEvent("on"+sEvent,pFunc);}
function removeEventFF(obj,sEvent,pFunc){obj.removeEventListener(sEvent,pFunc,false);}
if(obj.detachEvent)this.removeEvent=removeEventIE;else this.removeEvent=removeEventFF;}}