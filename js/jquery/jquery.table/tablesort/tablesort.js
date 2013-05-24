/*
        TableSort revisited - Extended version v0.2.2

        * Changelog *
        *
        * v0.2.2
        * New feature: you can add select fields above each column to filter the lines of the table
        * -------------------
        * v0.2.1
        * New feature: when you filter a in a column, the column header will get a `filtered` classname
        * So, now you can indicate, that which column is filtered. For example: th.filtered { background-color: #ff0; }
        * -------------------
        *

        This script is an extended version of
        TableSort revisited v0.1 by frequency-decoder.com (10/09/2006)


        Released under a creative commons Attribution-ShareAlike 2.5 license (http://creativecommons.org/licenses/by-sa/2.5/)

        You are free:

        * to copy, distribute, display, and perform the work
        * to make derivative works
        * to make commercial use of the work

        Under the following conditions:

                by Attribution.
                --------------
                You must attribute the work in the manner specified by the author or licensor.

                sa
                --
                Share Alike. If you alter, transform, or build upon this work, you may distribute the resulting work only under a license identical to this one.

        * For any reuse or distribution, you must make clear to others the license terms of this work.
        * Any of these conditions can be waived if you get permission from the copyright holder.
*/

var fdTableSort = {

        regExp_Currency:        /^[�$���]/,
        regExp_Number:          /^(\-)?[0-9]+(\.[0-9]*)?$/,
        pos:                    -1,
        uniqueHash:             1,
        thNode:                 null,
        tableCache:             {},
        tableId:                null,

        addEvent: function(obj, type, fn) {
                if( obj.attachEvent ) {
                        obj["e"+type+fn] = fn;
                        obj[type+fn] = function(){obj["e"+type+fn]( window.event );}
                        obj.attachEvent( "on"+type, obj[type+fn] );
                } else
                        obj.addEventListener( type, fn, false );
        },

        stopEvent: function(e) {
                e = e || window.event;

                if(e.stopPropagation) {
                        e.stopPropagation();
                        e.preventDefault();
                }
                /*@cc_on@*/
                /*@if(@_win32)
                e.cancelBubble = true;
                e.returnValue = false;
                /*@end@*/
                return false;
        },

		    table: new Array(),

        init: function() {
                if (!document.getElementsByTagName) return;
                var tables      = document.getElementsByTagName('table');
                var sortable, headers, thtext, aclone, a, span, columnNum, noArrow;

				// image element to activate search field
				filterImg       = document.createElement("img");
				filterImg.title = "filter";
				// path to the search image
				filterImg.src   = "search.png";

				// anchor element near by the filterImg - column title
                aImg            = document.createElement("a");
                aImg.onkeypress = fdTableSort.keyWrapper;

				// anchor element to activate search field
				        a               = document.createElement("a");
                a.onkeypress    = fdTableSort.keyWrapper;

                span            = document.createElement("span");

                for(var t = 0, tbl; tbl = tables[t]; t++) {
                        headers   = tbl.getElementsByTagName('th');
                        sortable  = false;
                        fdTableSort.table[tbl.id] = new Object();
                        columnNum = tbl.className.search(/sortable-onload-([0-9]+)/) != -1 ? parseInt(tbl.className.match(/sortable-onload-([0-9]+)/)[1]) - 1 : -1;
                        showArrow = tbl.className.search(/no-arrow/) == -1;
                        imgSearch = tbl.className.search(/img-filter/) != -1;
                        selectFilter = tbl.className.search(/select-filter/) != -1;
                        fdTableSort.table[tbl.id].selectedRows = new Array();
                        // Remove any old dataObj for this table (tables created from an ajax callback require this)
                        if(tbl.id && tbl.id in fdTableSort.tableCache) delete fdTableSort.tableCache[tbl.id];

                        for (var z=0, th; th = headers[z]; z++) {

                                // Remove previously applied classes for the ajaxers also
                                th.className = th.className.replace(/forwardSort|reverseSort/, "");

                                if(th.className.match('sortable')) {
                                        if(z == columnNum) sortable = th;
                                        thtext = fdTableSort.getInnerText(th);

                                        while(th.firstChild) th.removeChild(th.firstChild);

                    										// Create an image to image filter
                    										filterClone = filterImg.cloneNode(true);
                    										filterClone.className = "filterSpan";
                    										filterClone.onclick = fdTableSort.toogleFilterImageInput;

                    										// if the filter will be activated via an image
                    										if(imgSearch) {
                    											aclone = a.cloneNode(true);
                                          aclone.appendChild(document.createTextNode(thtext));

                    											aImgClone = aImg.cloneNode(true);
                                          aImgClone.innerHTML = thtext;

                    											th.onclick = filterClone.ondblclick = fdTableSort.stopEvent;
                    											th.appendChild(filterClone);
                    											th.appendChild(aImgClone);
                    										}
                    										else {
                    											aclone = a.cloneNode(true);
                                          aclone.appendChild(document.createTextNode(thtext));
                    											aclone.onclick = fdTableSort.toogleFilterLinkInput;
                    											th.onclick = aclone.ondblclick = fdTableSort.stopEvent;
                    											th.appendChild(aclone);
                    										}
                                        th.ondblclick = fdTableSort.clickWrapper;
                                        // Add the span if needs be
                                        if(showArrow) th.appendChild(span.cloneNode(false));

                                        var cn = "fd-column-" + z;
                                        th.className = th.className.replace(/fd-identical|fd-not-identical/, "").replace(cn, "") + " " + cn;
                              };
                        };

                        if(sortable) {
                                fdTableSort.thNode = sortable;
                                fdTableSort.initSort();
                        };
                        if(selectFilter)
                        {
                          var sel = document.createElement('select'), opt = document.createElement('option'), tr = document.createElement('tr'), td = document.createElement('td');
                          for(var i = 0, uData, uSel, uOpt; i < fdTableSort.tableCache[tbl.id].uniqueData.length; i++)
                          {
                            uData = fdTableSort.tableCache[tbl.id].uniqueData[i];
                            if(headers[i].className.match(/sortable-numeric/))
                            {
                              uData.sort(fdTableSort.sortNumeric);
                            }
                            else{
                              uData.sort();
                            }
                            var nTd = td.cloneNode(true);
                            uSel = sel.cloneNode(true);
                            uSel.onchange = fdTableSort.selFilter;
                            nTd.className += ' sel-'+headers[i].className.match(/fd-column-[0-9]+/)[0];
                            uOpt = opt.cloneNode(true);
                            uOpt.value = '';
                            uOpt.appendChild(document.createTextNode('Select'))
                            uSel.appendChild(uOpt);
                            for(var j = 0; j < uData.length; j++)
                            {
                              uOpt = opt.cloneNode(true);
                              uOpt.value = uData[j];
                              uOpt.appendChild(document.createTextNode(uData[j]))
                              uSel.appendChild(uOpt);
                            }
                            nTd.appendChild(uSel);
                            tr.appendChild(nTd);
                          }
                          // tbl.nextSibling.insertBefore(tr);
                          var firstTr = tbl.getElementsByTagName('tr')[0];
                          firstTr.parentNode.insertBefore(tr, firstTr);
                        }
                };
        },

        clickWrapper: function(e) {
          e = e || window.event;
          if(fdTableSort.thNode == null) {
            fdTableSort.thNode = this;
            fdTableSort.addSortActiveClass();
            setTimeout("fdTableSort.initSort()",5);
          };
          return fdTableSort.stopEvent(e);
        },

        keyWrapper: function(e) {
                e = e || window.event;
                var kc = e.keyCode != null ? e.keyCode : e.charCode;
                if(kc == 13) {
                        var targ = this;
                        while(targ.tagName.toLowerCase() != "th") targ = targ.parentNode;

                        fdTableSort.thNode = targ;
                        fdTableSort.addSortActiveClass();
                        setTimeout("fdTableSort.initSort()",5);

                        return fdTableSort.stopEvent(e);
                };
                return true;
        },

        jsWrapper: function(tableid, colNum) {
                var table = document.getElementById(tableid);
                var node  = table.getElementsByTagName('th')[colNum];
                if(!node || node.className.search(/fd-column/) == -1) return false;
                fdTableSort.thNode = node;
                fdTableSort.addSortActiveClass();
                setTimeout("fdTableSort.initSort()",5);
        },

        addSortActiveClass: function() {
                if(fdTableSort.thNode == null) return;
                var body = document.getElementsByTagName('body')[0];
                fdTableSort.thNode.className = fdTableSort.thNode.className + " sort-active";
                body.className = body.className + " sort-active";
                if("sortInitiatedCallback" in window) sortInitiatedCallback();
        },

        removeSortActiveClass: function() {
                var body = document.getElementsByTagName('body')[0];
                fdTableSort.thNode.className = fdTableSort.thNode.className.replace("sort-active", "");
                body.className = body.className.replace("sort-active", "");
                if("sortCompleteCallback" in window) sortCompleteCallback();
        },

        prepareTableData: function(table) {
                // Create a table id if needs be
                if(!table.id) table.id = "fd-table-" + fdTableSort.uniqueHash++;

                var data = [];
                var uniqueData = [[]];
                var uniqueDataObj = [];
                var start = table.getElementsByTagName('tbody');
                start = start.length ? start[0] : table;

                var trs = start.getElementsByTagName('tr');
                var ths = table.getElementsByTagName('th');

                var numberOfRows = trs.length;
                var numberOfCols = ths.length;

                var data = [];
                var identical = new Array(numberOfCols);
                var identVal  = new Array(numberOfCols);

                var tr, td, th, txt, tds, col, row;

                var rowCnt = 0;
                // Start Timer
                // var TT = new Timer();
                // Start to create the 2D matrix of data
                for(row = 0; row < numberOfRows; row++) {
                        tr              = trs[row];
                        // Have we any th tags or are we in a tfoot ?
                        if(tr.getElementsByTagName('th').length > 0 || (tr.parentNode && tr.parentNode.tagName == "TFOOT")) continue;

                        data[rowCnt]    = [];
                        tds             = tr.getElementsByTagName('td');

                        for(col = 0; col < numberOfCols; col++) {
                                th = ths[col];

                                if(th.className.search(/sortable/) == -1) continue;

                                td  = tds[col];
                                txt = fdTableSort.getInnerText(td) + " ";
                                txt = txt.replace(/^\s+/,'').replace(/\s+$/,'');

                                if(th.className.search(/sortable-date/) != -1) {
                                        txt = fdTableSort.dateFormat(txt);
                                } else if(th.className.search(/sortable-numeric-comma/) != -1) {
								                // For hungarian numbers: 100.123,21 = 100123.21
                                        txt = txt.replace(/\./, '').replace(/,/,'.');
                                        txt = parseFloat(txt.replace(/[^0-9\.\-]/g,''));
                                        if(isNaN(txt)) txt = "";
                                } else if(th.className.search(/sortable-numeric|sortable-currency/) != -1) {
                                        txt = parseFloat(txt.replace(/[^0-9\.\-]/g,''));
                                        if(isNaN(txt)) txt = "";
                                } else if(th.className.search(/sortable-text/) != -1) {
                                        txt = txt.toLowerCase();
                                } else if(th.className.search(/sortable-([a-zA-Z\_]+)/) != -1) {
                                        if((th.className.match(/sortable-([a-zA-Z\_]+)/)[1] + "PrepareData") in window) {
                                                txt = window[th.className.match(/sortable-([a-zA-Z\_]+)/)[1] + "PrepareData"](td, txt);
                                        };
                                } else {
                                        if(txt != "") {
                                                th.className = th.className.replace(/sortable/, "");
                                                if(fdTableSort.dateFormat(txt) != 0) {
                                                        th.className = th.className + " sortable-date";
                                                        txt = fdTableSort.dateFormat(txt);
                                                } else if(txt.search(fdTableSort.regExp_Number) != -1 || txt.search(fdTableSort.regExp_Currency) != -1) {
                                                        th.className = th.className + " sortable-numeric";
                                                        txt = parseFloat(txt.replace(/[^0-9\.\-]/g,''));
                                                        if(isNaN(txt)) txt = "";
                                                } else {
                                                        th.className = th.className + " sortable-text";
                                                        txt = txt.toLowerCase();
                                                };
                                        };
                                };

                                if(rowCnt > 0 && identVal[col] != txt) {
                                        identical[col] = false;
                                };

                                identVal[col]     = txt;
                                data[rowCnt][col] = txt;
                                /**
                                 * Oszlop adatainak kigyujtese, hogy csak egyszer szerepeljenek a uniqueData tombben
                                 */
                                /*
                                if(typeof uniqueData[col] != 'undefined')
                                {
                                  if(!uniqueData[col].inArray(txt))
                                  {
                                    uniqueData[col].push(txt);
                                  }
                                }
                                else
                                {
                                  uniqueData[col] = [];
                                  uniqueData[col].push(txt);
                                }
                                */
                                if(typeof uniqueDataObj[col] != 'undefined')
                                {
                                  if(typeof uniqueDataObj[col][txt] == 'undefined')
                                  {
                                    uniqueData[col].push(txt);
                                    uniqueDataObj[col][txt] = true;
                                  }
                                }
                                else
                                {
                                  uniqueData[col] = [];
                                  uniqueData[col].push(txt);
                                  uniqueDataObj[col] = {};
                                  uniqueDataObj[col][txt] = true;
                                }
                        };

                        // Add the tr for this row
                        data[rowCnt][numberOfCols] = tr;

                        // Increment the row count
                        rowCnt++;
                }
                // Stop timer and write to console
                // TT.End();
                // TT.console();
                // Get the row and column styles
                var colStyle = table.className.search(/colstyle-([\S]+)/) != -1 ? table.className.match(/colstyle-([\S]+)/)[1] : false;
                var rowStyle = table.className.search(/rowstyle-([\S]+)/) != -1 ? table.className.match(/rowstyle-([\S]+)/)[1] : false;

                var rowHiLight = table.className.search(/hilight-row-([-_a-zA-Z0-9]+)/) != -1 ? table.className.match(/hilight-row-([-_a-zA-Z0-9]+)/)[1] : false;
                var allowSelect = table.className.search(/select-row-([-_a-zA-Z0-9]+)/) != -1 ? table.className.match(/select-row-([-_a-zA-Z0-9]+)/)[1] : false;

                // Cache the data object for this table
                fdTableSort.tableCache[table.id] = {
                    data:data,
                    uniqueData:uniqueData,
                    pos:-1,
                    identical:identical,
                    colStyle:colStyle,
                    rowStyle:rowStyle,
                    rowHiLight:rowHiLight,
                    allowSelect:allowSelect,
                    noArrow:table.className.search(/no-arrow/) != -1
                };
        },

        initSort: function() {
                var span;
                var thNode      = fdTableSort.thNode;
                // Get the table
                var tableElem   = fdTableSort.thNode;
                while(tableElem.tagName.toLowerCase() != 'table' && tableElem.parentNode) {
                        tableElem = tableElem.parentNode;
                };

                // If this is the first time that this table has been sorted, create the data object
                if(!tableElem.id || !(tableElem.id in fdTableSort.tableCache) == 1) {
                        fdTableSort.prepareTableData(tableElem);
                };

                // Cache the table id
                fdTableSort.tableId = tableElem.id;

                // Get the column position using the className added earlier
                fdTableSort.pos = thNode.className.match(/fd-column-([0-9]+)/)[1];

                // Grab the data object for this table
                var dataObj     = fdTableSort.tableCache[tableElem.id];

                // Get the position of the last column that was sorted
                var lastPos     = dataObj.pos;

                // Get the stored data object for this table
                var data        = dataObj.data;
                var colStyle    = dataObj.colStyle;
                var rowStyle    = dataObj.rowStyle;
                var allowSelect = dataObj.allowSelect;
                var rowHiLight  = dataObj.rowHiLight;
                var len1        = data.length;
                var len2        = data[0].length - 1;
                var identical   = dataObj.identical[fdTableSort.pos] == false ? false : true;
                var noArrow     = dataObj.noArrow;

                if(lastPos != fdTableSort.pos && lastPos != -1) {
                        var th = thNode.parentNode.getElementsByTagName('th')[lastPos];
                        th.className = th.className.replace(/forwardSort|reverseSort/g,'');
                        if(!noArrow) {
                                // Remove arrow
                                span = th.getElementsByTagName('span')[0];
                                while(span.firstChild) span.removeChild(span.firstChild);
                        };
                };

                // If the same column is being sorted then just reverse the data object contents.
                if(lastPos == fdTableSort.pos && !identical) {
                        data.reverse();
                        var classToAdd = thNode.className.search(/reverseSort/) != -1 ? "forwardSort" : "reverseSort";
                        thNode.className = thNode.className.replace(/forwardSort|reverseSort/g,'') + " " + classToAdd;
                } else {
                        fdTableSort.tableCache[tableElem.id].pos = fdTableSort.pos;
                        if(!identical) {
                                if(thNode.className.match(/sortable-numeric|sortable-currency|sortable-date/)) {
                                        data.sort(fdTableSort.sortNumeric);
                                } else if(thNode.className.match('sortable-text')) {
                                        data.sort(fdTableSort.sortText);
                                } else if(thNode.className.search(/sortable-([a-zA-Z\_]+)/) != -1 && thNode.className.match(/sortable-([a-zA-Z\_]+)/)[1] in window) {
                                        data.sort(window[thNode.className.match(/sortable-([a-zA-Z\_]+)/)[1]]);
                                };
                        };
                        thNode.className = thNode.className.replace(/forwardSort|reverseSort/g,'') + " forwardSort";
                };

                if(!noArrow) {
                        var arrow = thNode.className.search(/forwardSort/) != -1 ? " \u2193" : " \u2191";
                        span = thNode.getElementsByTagName('span')[0];
                        while(span.firstChild) span.removeChild(span.firstChild);
                        span.appendChild(document.createTextNode(arrow));
                };

                if(!rowStyle && !colStyle && identical) {
                        fdTableSort.removeSortActiveClass();
                        fdTableSort.thNode = null;
                        return;
                }

                var hook = tableElem.getElementsByTagName('tbody');
                hook = hook.length ? hook[0] : tableElem;

                var td, tr;

                for(var i = 0; i < len1; i++) {
                        tr = data[i][len2];
                        if(allowSelect) {
                            tr.onclick = fdTableSort.toogleSelect;
                        }
                        if(rowHiLight) {
                            tr.onmouseover = tr.onmouseout = fdTableSort.toogleRowStyle;
                        }
                        if(colStyle) {
                                if(lastPos != -1) {
                                        td = tr.getElementsByTagName('td')[lastPos];
                                        td.className = td.className.replace(colStyle, "");
                                }
                                td = tr.getElementsByTagName('td')[fdTableSort.pos];
                                td.className = (typeof td.className != "undefined" ? td.className  : "") + " " + colStyle;
                        };
                        if(!identical) {
                                if(rowStyle) {
                                        tr.className = tr.className.replace(rowStyle);
                                        if(i % 2) tr.className = (typeof tr.className != "undefined" ? tr.className : "") + " " + rowStyle;
                                };
                                hook.appendChild(tr);
                        };
                };
                fdTableSort.removeSortActiveClass();
                fdTableSort.thNode = null;
        },

        getInnerText: function(el) {
                if (typeof el == "string" || typeof el == "undefined") return el;
                if(el.innerText) return el.innerText;

                var txt = '', i;
                for (i = el.firstChild; i; i = i.nextSibling) {
                        if (i.nodeType == 3)            txt += i.nodeValue;
                        else if (i.nodeType == 1)       txt += fdTableSort.getInnerText(i);
                };

                return txt;
        },

        dateFormat: function(dateIn) {
                var y, m, d, res;

                // mm-dd-yyyy
                if(dateIn.match(/^(0[1-9]|1[012])([- \/.])(0[1-9]|[12][0-9]|3[01])([- \/.])(\d\d?\d\d)$/)) {
                        res = dateIn.match(/^(0[1-9]|1[012])([- \/.])(0[1-9]|[12][0-9]|3[01])([- \/.])(\d\d?\d\d)$/);
                        y = res[5];
                        m = res[1];
                        d = res[3];
                // dd-mm-yyyy
                } else if(dateIn.match(/^(0[1-9]|[12][0-9]|3[01])([- \/.])(0[1-9]|1[012])([- \/.])(\d\d?\d\d)$/)) {
                        res = dateIn.match(/^(0[1-9]|[12][0-9]|3[01])([- \/.])(0[1-9]|1[012])([- \/.])(\d\d?\d\d)$/);
                        y = res[5];
                        m = res[3];
                        d = res[1];
                // yyyy-mm-dd
                } else if(dateIn.match(/^(\d\d?\d\d)([- \/.])(0[1-9]|1[012])([- \/.])(0[1-9]|[12][0-9]|3[01])$/)) {
                        res = dateIn.match(/^(\d\d?\d\d)([- \/.])(0[1-9]|1[012])([- \/.])(0[1-9]|[12][0-9]|3[01])$/);
                        y = res[1];
                        m = res[3];
                        d = res[5];
                } else return 0;

                if(m.length == 1) m = "0" + m;
                if(d.length == 1) d = "0" + d;
                if(y.length == 1) y = '0' + y;
                if(y.length != 4) y = (parseInt(y) < 50) ? '20' + y : '19' + y;

                return y+m+d;
        },

        sortDate: function(a,b) {
                var aa = a[fdTableSort.pos];
                var bb = b[fdTableSort.pos];

                return aa - bb;
        },

        sortNumeric:function (a,b) {
                var aa = a[fdTableSort.pos];
                var bb = b[fdTableSort.pos];

                if(aa === "" && !isNaN(bb)) return -1;
                else if(bb === "" && !isNaN(aa)) return 1;
                else if(aa == bb) return 0;

                return aa - bb;
        },

        sortText:function (a,b) {
                var aa = a[fdTableSort.pos];
                var bb = b[fdTableSort.pos];

                if(aa == bb) return 0;
                if(aa < bb)  return -1;

                return 1;
        },

        // initialize and run filter functions **
        initFilter: function(e) {
            var code;
            if (!e) var e = window.event;
            if (e.keyCode) code = e.keyCode;
            else if (e.which) code = e.which;
            if(code == 13) { // Enter
                if(fdTableSort.thNode == null) {
                    fdTableSort.thNode = this;
                    fdTableSort.addSortActiveClass();
                    setTimeout("fdTableSort.filter()",5);
                };
                fdTableSort.stopEvent(e);
            }
            return true;
        },

        // filter rows if text **
        filterText:function (a, rex) {
            var aa = a[fdTableSort.pos];
            aa = aa.toString();
            if(aa.match(rex)) {
                return 1;
            }
            return 0;
        },

        // filter the numerable rows **
        filterNum:function (a, rex) {
            if(rex == "/^/i") {
                // if input has no value
                return 1;
            }
            var aa = a[fdTableSort.pos].toString();
            var rexstr = rex.toString();
            var n = new RegExp("^([<>=!])(.+)", 'i');
            if(rexstr.match(n)) { // if the expression begin with an operator
                var num = parseFloat(RegExp.$2);

                if(RegExp.$1 == '<') { // lt than
                    if(aa < num) {
                        return 1;
                    }
                    return 0;
                }
                else if(RegExp.$1 == '>') { // gt than
                    if(aa > num) {
                        return 1;
                    }
                    return 0;
                }
                else if(RegExp.$1 == '!') { // neq than
                    if(aa != num) {
                        return 1;
                    }
                    return 0;
                }
                else { // eq
                    if(aa == num) {
                        return 1;
                    }
                    return 0;
                }
            }
            else {
                if(aa.match(rex)) {
                    return 1;
                }
                return 0;
            }
        },

        filter: function() {
            var inputNode      = fdTableSort.thNode;
            var inputNodeVal   = inputNode.value;
            // thNode default = input element
            thNode = inputNode.parentNode;
            // Get the table
            var tableElem   = fdTableSort.thNode;
            while(tableElem.tagName.toLowerCase() != 'table' && tableElem.parentNode) {
                tableElem = tableElem.parentNode;
            };

            // If this is the first time that this table has been sorted, create the data object
            if(!tableElem.id || !(tableElem.id in fdTableSort.tableCache)) {
                fdTableSort.prepareTableData(tableElem);
            };

            // Cache the table id
            fdTableSort.tableId = tableElem.id;

            // Get the column position using the className added earlier
            fdTableSort.pos = thNode.className.match(/fd-column-([0-9]+)/)[1];

            // Grab the data object for this table
            var dataObj        = fdTableSort.tableCache[tableElem.id];

            // Get the position of the last column that was sorted
            var lastPos        = dataObj.pos;

            // Get the stored data object for this table
            var data        = dataObj.data;
            var len1        = data.length;
            var len2        = data[0].length - 1;
            var identical    = dataObj.identical[fdTableSort.pos] == false ? false : true;
            var mustHide    = Array(); // Array, where will be placed the filtered rows indexes **
            var thNum        = thNode.className.match(/sortable-numeric/);

            fdTableSort.tableCache[tableElem.id].pos = fdTableSort.pos;
            var headers = tableElem.getElementsByTagName('th');

            // Create filter regexp **
            if(inputNodeVal.match(/^[<>=!]/) && thNum) {
                rex = inputNodeVal;
            }
            else if(thNum) { // if number data type
                var rex = new RegExp('^'+inputNodeVal, 'i');
            }
            else {
                var rex = new RegExp(inputNodeVal, 'i');
            }


      			for(var i=0; i<headers.length; i++) {
      				var th = headers[i];
      				if(th.className.search(/filtered/) != -1) {
      					th.className = th.className.replace(/ ?filtered/, '');
      				}
      			}
      			if(inputNodeVal != '') { thNode.className += ' filtered'; }

            // Push the matched rows index into mustHide array **
            if(thNum) {
                for(var i=0; i<len1; i++) {
                    if(!fdTableSort.filterNum(data[i], rex)) {
                        mustHide.push(i);
                    }
                }
            }
            else {
                for(var i=0; i<len1; i++) {
                    if(!fdTableSort.filterText(data[i], rex)) {
                        mustHide.push(i);
                    }
                }
            }

			      // regenerate the table
            var hook = tableElem.getElementsByTagName('tbody');
            hook = hook.length ? hook[0] : tableElem;

            var td, tr;

            for(var i = 0; i < len1; i++) {
                tr = data[i][len2];
                if(!identical) {
                    hook.appendChild(tr);
                };
                // Hide, show **
                if(mustHide.inArray(i)) {
                    tr.style.display = 'none';
                }
                else {
                    tr.style.display = '';
                }
            };
            fdTableSort.thNode = null;
            return true;
        },
        selFilter: function(e) {
            e = e || window.event;
            var inputNode      = e.target || e.srcElement;
            var inputNodeVal   = inputNode.value;

            // thNode default = input element
            thNode = inputNode.parentNode;
            // Get the table
            var tableElem   = thNode;
            while(tableElem.tagName.toLowerCase() != 'table' && tableElem.parentNode) {
                tableElem = tableElem.parentNode;
            };

            // If this is the first time that this table has been sorted, create the data object
            if(!tableElem.id || !(tableElem.id in fdTableSort.tableCache)) {
                fdTableSort.prepareTableData(tableElem);
            };

            // Cache the table id
            fdTableSort.tableId = tableElem.id;

            // Get the column position using the className added earlier
            fdTableSort.pos = thNode.className.match(/fd-column-([0-9]+)/)[1];

            // Grab the data object for this table
            var dataObj        = fdTableSort.tableCache[tableElem.id];

            // Get the position of the last column that was sorted
            var lastPos        = dataObj.pos;

            // Get the stored data object for this table
            var data        = dataObj.data;
            var len1        = data.length;
            var len2        = data[0].length - 1;
            var identical    = dataObj.identical[fdTableSort.pos] == false ? false : true;
            var mustHide    = Array(); // Array, where will be placed the filtered rows indexes **
            var thNum        = thNode.className.match(/sortable-numeric/);

            fdTableSort.tableCache[tableElem.id].pos = fdTableSort.pos;
            var headers = tableElem.getElementsByTagName('th');


           if(inputNodeVal != '') {
             thNode.className += ' filtered';
             // Push the matched rows index into mustHide array **
             for(var i=0; i<len1; i++) {
               if(inputNodeVal != data[i][fdTableSort.pos]) {
                 mustHide.push(i);
               }
             }
           }
			      // regenerate the table
            var hook = tableElem.getElementsByTagName('tbody');
            hook = hook.length ? hook[0] : tableElem;

            var td, tr;

            for(var i = 0; i < len1; i++) {
                tr = data[i][len2];
                if(!identical) {
                    hook.appendChild(tr);
                };
                // Hide, show **
                if(mustHide.inArray(i)) {
                    tr.style.display = 'none';
                }
                else {
                    tr.style.display = '';
                }
            };
            fdTableSort.thNode = null;
            return true;
        },

        // Add the selected rows into the
        toogleSelect: function(e) {
            if (!e) var e = window.event;
            var table = e.target || e.srcElement;
            while(table.tagName.toLowerCase() != 'table') {
                table = table.parentNode;
            }
            allowSelect = fdTableSort.tableCache[table.id].allowSelect;
            var selClass = new RegExp(' ?'+allowSelect, 'g');
            if(this.className.match(allowSelect)) { // if the cell is still selected
                for(var i=0; i<fdTableSort.table[table.id].selectedRows.length; i++) {
                    if(fdTableSort.table[table.id].selectedRows[i] === this) {
						            // remove the row form the selectedRows array
                        fdTableSort.table[table.id].selectedRows.splice(i, 1);
                    }
                }
                this.className = this.className.replace(selClass, '');
                if(!e.ctrlKey) {
				            // if the Ctrl key isn't pressed, deselect all row
                    fdTableSort.selectNone(table.id);
                }
            }
            else {
                if(!e.ctrlKey) {
				            // if the Ctrl key isn't pressed deselect all row
                    fdTableSort.selectNone(table.id);
                }
                fdTableSort.table[table.id].selectedRows.push(this);
                this.className += ' '+allowSelect;
            }
        },

        toogleDispSelected: function(tableId) {
			      // show/hide the selected rows row
            if(fdTableSort.table[tableId].selectedRows.length>0) {
                var rows = fdTableSort.table[tableId].selectedRows;
                if(rows[0].style.display == 'none') {
                    disp = '';
                }
                else {
                    disp = 'none'
                }
                for(var i = 0; i<rows.length; i++) {
                    rows[i].style.display = disp;
                }
            }
        },

        toogleRowStyle: function(e) {
			// change the row's class on mouseover and mouseout events
            if (!e) var e = window.event;
            var table = e.target || e.srcElement;
            while(table.tagName.toLowerCase() != 'table') {
                table = table.parentNode;
            }
            var tr = this;
            var rhl = fdTableSort.tableCache[table.id].rowHiLight;
            var rhRex = new RegExp(' ?'+rhl, 'ig');
            if(e.type.toLowerCase() == 'mouseout') { // remove the class
                tr.className = tr.className.replace(rhRex, '')
            }
            else if(e.type.toLowerCase() == 'mouseover'){ // on mouseover add new class
                tr.className += ' '+rhl;
            }
        },

        toogleDispNoSelected: function(tableId) {
            // show/hide the rows, which aren't selected
            if(!fdTableSort.tableCache[tableId]) return;
            var tr;
            if(fdTableSort.hideNoSelected == 1) {
                if(fdTableSort.tableCache[tableId].data.length > 0 && fdTableSort.table[tableId].selectedRows.length > 0) {
                    for(var i=0; i<fdTableSort.tableCache[tableId].data.length; i++) {
                        tr = fdTableSort.tableCache[tableId].data[i][fdTableSort.tableCache[tableId].data[i].length-1];
                        if(!fdTableSort.table[tableId].selectedRows.inArray(tr)) {
                            tr.style.display = '';
                        }
                    }
                }
                fdTableSort.hideNoSelected = 0;
            }
            else {
                if(fdTableSort.tableCache[tableId].data.length > 0 && fdTableSort.table[tableId].selectedRows.length > 0) {
                    for(var i=0; i<fdTableSort.tableCache[tableId].data.length; i++) {
                        tr = fdTableSort.tableCache[tableId].data[i][fdTableSort.tableCache[tableId].data[i].length-1];
                        if(!fdTableSort.table[tableId].selectedRows.inArray(tr)) {
                            tr.style.display = 'none';
                        }
                    }
                }
                fdTableSort.hideNoSelected = 1;
            }
            return;
        },

        inverzSelect: function(tableId) {
            // the selected rows will be unselected and the unselected will be selected
            if(!fdTableSort.tableCache[tableId]) return;
            var tempSelected = new Array(); // temporary array
            allowSelect = fdTableSort.tableCache[tableId].allowSelect;
            var selClass = new RegExp(' ?'+allowSelect, 'g')
            if(fdTableSort.tableCache[tableId].data.length > 0 && fdTableSort.table[tableId].selectedRows.length > 0) {
                for(var i=0; i<fdTableSort.tableCache[tableId].data.length; i++) {
                    tr = fdTableSort.tableCache[tableId].data[i][fdTableSort.tableCache[tableId].data[i].length-1];
                    if(!fdTableSort.table[tableId].selectedRows.inArray(tr)) {
                        tr.className += ' '+allowSelect;
                        tempSelected.push(tr);
                    }
                    else {
                        tr.className = tr.className.replace(selClass, '');
                    }
                }
                fdTableSort.table[tableId].selectedRows = tempSelected;
            }
            delete tempSelected;
        },

        selectNone: function(tableId) {
			// deselect and show all fields
            if(typeof fdTableSort.table == "undefinied") return;
            if(fdTableSort.table[tableId].selectedRows.length>0) {
                allowSelect = fdTableSort.tableCache[tableId].allowSelect;
                var selClass = new RegExp(' ?'+allowSelect, 'g');
                if(fdTableSort.hideNoSelected) {
                    for(var i=0; i<fdTableSort.tableCache[tableId].data.length; i++) {
                        fdTableSort.tableCache[tableId].data[i][fdTableSort.tableCache[tableId].data[i].length-1].style.display = '';
                    }
                }
                var rows = fdTableSort.table[tableId].selectedRows;
                if(rows[0].style.display == 'none') {
                    fdTableSort.toogleDispSelected(tableId);
                }
                for(var i = 0; i<rows.length; i++) {
                    rows[i].className = rows[i].className.replace(selClass, '');
                }
                fdTableSort.table[tableId].selectedRows = new Array();
            }
        },

        // if the filter is activated via single click on an image
        toogleFilterImageInput: function(e) {
          // replace the row title to an input field on click
          var code;
          if (!e) var e = window.event;
          th = this.parentNode;
          while(th.tagName.toLowerCase() != 'th') {
            th = th.parentNode;
          }
          childs = th.childNodes;
          for(var i=0; i<childs.length; i++) {
            if(childs[i].tagName.toLowerCase() == 'a') {
              link = document.createElement('a');
              link.innerHTML = childs[i].innerHTML;
            }
            else if(childs[i].tagName.toLowerCase() == 'img') {
              filterSpan = document.createElement('img');
              filterSpan.onclick = childs[i].onclick;
              filterSpan.className = childs[i].className;
              filterSpan.src = childs[i].src;
            }
            else if(childs[i].tagName.toLowerCase() == 'span' && childs[i].className != 'filterSpan'){
              arrowSpan = document.createElement('span');
              arrowSpan.innerHTML = childs[i].innerHTML;
            }
          }
          inp = document.createElement('input');
          inp.type = 'text';
          inp.onclick = fdTableSort.stopEvent;
          inp.onkeypress = fdTableSort.initFilter;
          inp.onblur = function() {
            th.innerHTML = '';
            th.appendChild(filterSpan);
            th.appendChild(link);
            th.appendChild(arrowSpan);
            // th.appendChild(document.createElement('span'));
          }
          th.innerHTML = '';
          th.appendChild(inp);
          inp.select();
          inp.focus();
        },

		// if the filter is activated via single click on the column title
    toogleFilterLinkInput: function(e) {
            // replace the row title to an input field on click
      var code;
      if (!e) var e = window.event;
      th = this.parentNode;
      while(th.tagName.toLowerCase() != 'th') {
        th = th.parentNode;
      }

      link = document.createElement(this.tagName);
      link.onclick = this.onclick;
      link.innerHTML = this.innerHTML;

      inp = document.createElement('input');
      inp.type = 'text';
      inp.onclick = inp.ondblclick = fdTableSort.stopEvent;
      inp.onkeypress = fdTableSort.initFilter;
      inp.onblur = function() {
          th.innerHTML = '';
          th.appendChild(link);
          th.appendChild(document.createElement('span'));
      }
      th.innerHTML = '';
      th.appendChild(inp);
      inp.focus();
  }
};

Timer = function()
{
  this.timer = true;
  this.Start();
};
Timer.prototype = {
  start : null,
  end: null,
  elapsed: null,
  Start: function()
  {
    var date = new Date();
    this.start = date.getTime();
    return;
  },
  End: function()
  {
    var date = new Date();
		this.end = date.getTime();
    this.elapsed = this.end - this.start;
  },
  alert: function()
  {
    alert(this.elapsed);
  },
  console: function()
  {
    console.log(this.elapsed)
  }
}

// http://code.mikebrittain.com/2006/01/inarray-method-for-javascript-and-actionscript/
Array.prototype.inArray = function(value)
// Returns true if the passed value is found in the
// array.  Returns false if it is not.
{
  var i = this.length-1;
  if (i > 0) {
	 do {
		if (this[i] === value) {
		   return true;
		}
	 } while (i--);
  }
  return false;
};

fdTableSort.addEvent(window, "load", fdTableSort.init);