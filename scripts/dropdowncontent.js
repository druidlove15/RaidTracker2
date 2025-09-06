//Drop Down/ Overlapping Content: http://www.dynamicdrive.com
//Last updated: Dec 19th, 07': Added ability to dynamically populate a Drop Down content using an external file (Ajax feature)

var dropdowncontent={
 delaybeforehide: 100, //set delay in milliseconds before content box disappears onMouseout (1000=1 sec)
	disableanchorlink: false, //when user clicks on anchor link, should it be disabled?
	ajaxloadingmsg: "Loading content. Please wait...", //HTML to show while ajax page is being feched
	ajaxbustcache: true, //Bust cache when fetching pages?

	getposOffset:function(what, offsettype){
		return (what.offsetParent)? what[offsettype]+this.getposOffset(what.offsetParent, offsettype) : what[offsettype]
	},

	isContained:function(m, e){
		var e=window.event || e
		var c=e.relatedTarget || ((e.type=="mouseover")? e.fromElement : e.toElement)
		while (c && c!=m)try {c=c.parentNode} catch(e){c=m}
		if (c==m)
			return true
		else
			return false
	},

	show:function(anchorobj, subobj, e){
		if (!this.isContained(anchorobj, e)){
			var horizontaloffset=(subobj.dropposition[0]=="left")? -(subobj.offsetWidth-anchorobj.offsetWidth) : 0 //calculate user added horizontal offset
			var verticaloffset=(subobj.dropposition[1]=="top")? -subobj.offsetHeight : anchorobj.offsetHeight //calculate user added vertical offset
			subobj.style.left=this.getposOffset(anchorobj, "offsetLeft") + horizontaloffset + "px"
			subobj.style.top=this.getposOffset(anchorobj, "offsetTop")+verticaloffset+"px"
			subobj.style.clip=(subobj.dropposition[1]=="top")? "rect(auto auto auto 0)" : "rect(0 auto 0 0)" //hide drop down box initially via clipping
			subobj.style.visibility="visible"
			subobj.startTime=new Date().getTime()
			subobj.contentheight=parseInt(subobj.offsetHeight)
			if (typeof window["hidetimer_"+subobj.id]!="undefined") //clear timer that hides drop down box?
				clearTimeout(window["hidetimer_"+subobj.id])
			this.slideengine(subobj, (subobj.dropposition[1]=="top")? "up" : "down")
		}
	},

	curveincrement:function(percent){
		return (1-Math.cos(percent*Math.PI)) / 2 //return cos curve based value from a percentage input
	},

	slideengine:function(obj, direction){
		var elapsed=new Date().getTime()-obj.startTime //get time animation has run
		if (elapsed<obj.glidetime){ //if time run is less than specified length
			var distancepercent=(direction=="down")? this.curveincrement(elapsed/obj.glidetime) : 1-this.curveincrement(elapsed/obj.glidetime)
			var currentclip=(distancepercent*obj.contentheight)+"px"
			obj.style.clip=(direction=="down")? "rect(0 auto "+currentclip+" 0)" : "rect("+currentclip+" auto auto 0)"
			window["glidetimer_"+obj.id]=setTimeout(function(){dropdowncontent.slideengine(obj, direction)}, 10)
		}
		else{ //if animation finished
			obj.style.clip="rect(0 auto auto 0)"
		}
	},

	hide:function(activeobj, subobj, e){
		if (!dropdowncontent.isContained(activeobj, e)){
			window["hidetimer_"+subobj.id]=setTimeout(function(){
				subobj.style.visibility="hidden"
				subobj.style.left=subobj.style.top=0
				clearTimeout(window["glidetimer_"+subobj.id])
			}, dropdowncontent.delaybeforehide)
		}
	},

	ajaxconnect:function(pageurl, divId){
		var page_request = false
		var bustcacheparameter=""
		if (window.XMLHttpRequest) // if Mozilla, IE7, Safari etc
			page_request = new XMLHttpRequest()
		else if (window.ActiveXObject){ // if IE6 or below
			try {
			page_request = new ActiveXObject("Msxml2.XMLHTTP")
			} 
			catch (e){
				try{
				page_request = new ActiveXObject("Microsoft.XMLHTTP")
				}
				catch (e){}
			}
		}
		else
			return false
		document.getElementById(divId).innerHTML=this.ajaxloadingmsg //Display "fetching page message"
		page_request.onreadystatechange=function(){dropdowncontent.loadpage(page_request, divId)}
		if (this.ajaxbustcache) //if bust caching of external page
			bustcacheparameter=(pageurl.indexOf("?")!=-1)? "&"+new Date().getTime() : "?"+new Date().getTime()
		page_request.open('GET', pageurl+bustcacheparameter, true)
		page_request.send(null)
	},

	loadpage:function(page_request, divId){
		if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1)){
			document.getElementById(divId).innerHTML=page_request.responseText
		}
	},

 init:function(anchorid, pos, glidetime){
		var anchorobj=document.getElementById(anchorid)
		var subobj=document.getElementById(anchorobj.getAttribute("rel"))
		var subobjsource=anchorobj.getAttribute("rev")
		if (subobjsource!=null && subobjsource!="")
			this.ajaxconnect(subobjsource, anchorobj.getAttribute("rel"))
		subobj.dropposition=pos.split("-")
		subobj.glidetime=glidetime || 1000
		subobj.style.left=subobj.style.top=0
		anchorobj.onmouseover=function(e){dropdowncontent.show(this, subobj, e)}
		anchorobj.onmouseout=function(e){dropdowncontent.hide(subobj, subobj, e)}
		if (this.disableanchorlink) anchorobj.onclick=function(){return false}
		subobj.onmouseout=function(e){dropdowncontent.hide(this, subobj, e)}
	}
}