/**
 * tablePagination - A table plugin for jQuery that creates pagination elements
 *
 * http://neoalchemy.org/tablePagination.html
 *
 * Copyright (c) 2009-2011 Ryan Zielke (neoalchemy.com)
 * licensed under the MIT licenses:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * @name tablePagination
 * @type jQuery
 * @param Object settings;
 *      firstArrow - Image - Pass in an image to replace default image. Default: (new Image()).src="./images/first.gif"
 *      prevArrow - Image - Pass in an image to replace default image. Default: (new Image()).src="./images/prev.gif"
 *      lastArrow - Image - Pass in an image to replace default image. Default: (new Image()).src="./images/last.gif"
 *      nextArrow - Image - Pass in an image to replace default image. Default: (new Image()).src="./images/next.gif"
 *      rowsPerPage - Number - used to determine the starting rows per page. Default: 5
 *      currPage - Number - This is to determine what the starting current page is. Default: 1
 *      optionsForRows - Array - This is to set the values on the rows per page. Default: [5,10,25,50,100]
 *      ignoreRows - Array - This is to specify which 'tr' rows to ignore. It is recommended that you have those rows be invisible as they will mess with page counts. Default: []
 *
 *
 * @author Ryan Zielke (neoalchemy.org)
 * @version 0.4
 * @requires jQuery v1.2.3 or above
 */

 (function(a){a.fn.tablePagination=function(b){var c={firstArrow:(new Image()).src="./images/first.gif",prevArrow:(new Image()).src="./images/prev.gif",lastArrow:(new Image()).src="./images/last.gif",nextArrow:(new Image()).src="./images/next.gif",rowsPerPage:5,currPage:1,optionsForRows:[5,10,25,50,100],ignoreRows:[]};b=a.extend(c,b);return this.each(function(){var r=a(this)[0];var h="#tablePagination_totalPages";var f="#tablePagination_currPage";var l="#tablePagination_rowsPerPage";var i="#tablePagination_firstPage";var j="#tablePagination_prevPage";var t="#tablePagination_nextPage";var p="#tablePagination_lastPage";var s=a.makeArray(a("tbody tr",r));var k=a.grep(s,function(w,v){return(a.inArray(w,c.ignoreRows)==-1)},false);var d=k.length;var u=e();var m=(c.currPage>u)?1:c.currPage;if(a.inArray(c.rowsPerPage,c.optionsForRows)==-1){c.optionsForRows.push(c.rowsPerPage)}function o(x){if(x==0||x>u){return}var y=(x-1)*c.rowsPerPage;var w=(y+c.rowsPerPage-1);a(k).show();for(var v=0;v<k.length;v++){if(v<y||v>w){a(k[v]).hide()}}}function e(){var w=Math.round(d/c.rowsPerPage);var v=(w*c.rowsPerPage<d)?w+1:w;if(a(r).next().find(h).length>0){a(r).next().find(h).html(v)}return v}function n(v){if(v<1||v>u){return}m=v;o(m);a(r).next().find(f).val(m)}function q(){var x=false;var y=c.optionsForRows;y.sort(function(A,z){return A-z});var w=a(r).next().find(l)[0];w.length=0;for(var v=0;v<y.length;v++){if(y[v]==c.rowsPerPage){w.options[v]=new Option(y[v],y[v],true,true);x=true}else{w.options[v]=new Option(y[v],y[v])}}if(!x){c.optionsForRows==y[0]}}function g(){var v=[];v.push("<div id='tablePagination'>");v.push("<span id='tablePagination_perPage'>");v.push("<select id='tablePagination_rowsPerPage'><option value='5'>5</option></select>");v.push("per page");v.push("</span>");v.push("<span id='tablePagination_paginater'>");v.push("<img id='tablePagination_firstPage' src='"+c.firstArrow+"'>");v.push("<img id='tablePagination_prevPage' src='"+c.prevArrow+"'>");v.push("Page");v.push("<input id='tablePagination_currPage' type='input' value='"+m+"' size='1'>");v.push("of <span id='tablePagination_totalPages'>"+u+"</span>");v.push("<img id='tablePagination_nextPage' src='"+c.nextArrow+"'>");v.push("<img id='tablePagination_lastPage' src='"+c.lastArrow+"'>");v.push("</span>");v.push("</div>");return v.join("").toString()}if(a(r).next().find(h).length==0){a(this).after(g())}else{a(r).next().find(f).val(m)}q();o(m);a(r).next().find(i).bind("click",function(v){n(1)});a(r).next().find(j).bind("click",function(v){n(m-1)});a(r).next().find(t).bind("click",function(v){n(parseInt(m)+1)});a(r).next().find(p).bind("click",function(v){n(u)});a(r).next().find(f).bind("change",function(v){n(this.value)});a(r).next().find(l).bind("change",function(v){c.rowsPerPage=parseInt(this.value,10);u=e();n(1)})})}})(jQuery);