﻿/*
**
* jqGrid (http://trirand.com/blog/) integration with jquery.bbq library (http://benalman.com/projects/jquery-bbq-plugin/)
* by Craig Stuntz (http://blogs.teamb.com/craigstuntz/)
* 
* Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl-2.0.html
**/ 
(function ($) {
    $.jgrid.history = {
        // global options -- you can overwrite these elsewhere, if need be, before calling $().jqGridHistory
        getPropertyValue: function (gridOptions, name, historyOptions) {
            var nameFragments = name.split('.');
            if (nameFragments.length === 1) {
                return gridOptions[name];
            }
            if (nameFragments[0] === "postData") {
                return gridOptions.postData && gridOptions.postData[nameFragments[1]];
            }
        },
        // this is the GLOBAL prefix. There is also a per-grid prefix in options.history.hashPrefix
        globalHashPrefix: "",
        // compute name of hashName for URI -- result will be the "rowNum" part of http://site/Foo#rowNum=20
        hashPrefix: function (historyOptions, propertyName) {
            return (historyOptions.hashPrefix || this.globalHashPrefix) + propertyName;
        },
        jqGridInternalDefaults: {
            // these are hard-coded into the grid constructor :( so I've copied them here.
            page: 1,
            rowNum: 20,
            records: 0,
            sortorder: "asc",
            sortname: ""
        },
        persist: ["page", "rowNum", "sortname", "sortorder"], // options to store in hash
        // change gridOptions[name] to value. Replace this to customize assignment when it's not a direct mapping.
        // historyOptions are for reference only; the method should only change gridOptions
        setPropertyValue: function (gridOptions, name, value, historyOptions) {
            var nameFragments = name.split('.');
            if (nameFragments.length === 1) {
                gridOptions[name] = value;
            }
            else {
                if (nameFragments[0] === "postData") {
                    gridOptions.postData = gridOptions.postData || {};
                    gridOptions.postData[nameFragments[1]] = value;
                }
            }
        }
    };
    var fixPager = function (grid, rowNum) {
        // work around grid bug where setting rowNum in options doesn't update combo
        var pager = grid.getGridParam("pager");
        if (pager) {
            $(pager).find(".ui-pg-selbox").children("[value=" + rowNum + "]").attr("selected", "true");
        }
    };
    var createHashChangeHandler = function (gridSelector) {
        return function (event) {
            var grid = $(gridSelector);
            var gridOptions = grid.getGridParam();
            var history = gridOptions.history;
            var hash = history.bbq.getState();
            var gp = {};
            var reloadNeeded = false;
            var persist = gridOptions.history.persist;
            var globalOpts = $.jgrid.history;
            for (var i = 0; i < persist.length; i++) {
                var name = persist[i];
                var currentVal = globalOpts.getPropertyValue(gridOptions, name, history);
                var newVal = hash[globalOpts.hashPrefix(history, name)] || history.defaults[name];
                var boolSpecialCaseEquals = (newVal === "false") && (currentVal === false); // this isn't a problem in JS with true :)
                reloadNeeded = reloadNeeded || ((!boolSpecialCaseEquals) && (currentVal != newVal));
                globalOpts.setPropertyValue(gp, name, newVal, history);
            }
            if (reloadNeeded) {
                grid.setGridParam(gp);
                if (gp.rowNum) {
                    fixPager(grid, gp.rowNum);
                }
                grid.trigger("reloadGrid");
            }
        };
    };
    var gridCompleteSetHash = function () {
        var p = this.p || this; // "this" changed in jqGrid 3.7; I want to support 3.6 and 3.7.
        var history = p.history;
        var hashPrefix = history.hashPrefix || "";
        var hash = history.bbq.getState();
        var currentHashIsEmpty = !window.location.hash;
        var currentHashHasGridHistory = false;
        var changedHash = false;
        var globalOpts = $.jgrid.history;
        for (var i = 0; i < history.persist.length; i++) {
            var propertyName = history.persist[i];
            var val = globalOpts.getPropertyValue(p, propertyName, history);
            var currentValIsDefault = val == history.defaults[propertyName];
            if (currentValIsDefault) {
                if (hash[hashPrefix + propertyName]) {
                    delete hash[hashPrefix + propertyName];
                    changedHash = true;
                }
            }
            else {
                if (val !== "") {
                    hash[hashPrefix + propertyName] = val;
                    changedHash = true;
                }
            }
        }
        if (changedHash) {
            history.bbq.pushState(hash, 2);
        }
    };
    var optionsWithHistory = function (options) {
        var defaults = $.extend($.jgrid.history.jqGridInternalDefaults, $.jgrid.defaults, options);
        var newOptions = {};
        newOptions.history = options.history || {};
        // use local default of jQuery BBQ library if not stubbed out for unit testing
        newOptions.history.bbq = newOptions.history.bbq || $.bbq;
        var hash = newOptions.history.bbq.getState();
        newOptions.history.defaults = newOptions.history.defaults || {};
        newOptions.history.hashPrefix = newOptions.history.hashPrefix || "";
        newOptions.history.grid = this;
        newOptions.history.persist = newOptions.history.persist || $.jgrid.history.persist;
        for (var i = 0; i < newOptions.history.persist.length; i++) {
            var name = newOptions.history.persist[i];
            // copy over only the values in persist, not all defaults.
            newOptions.history.defaults[name] = newOptions.history.defaults[name] || $.jgrid.history.getPropertyValue(defaults, name, newOptions.history);
            var val = hash[newOptions.history.hashPrefix + name];
            if (val) {
                $.jgrid.history.setPropertyValue(newOptions, name, val, newOptions.history);
            }
        };
        if (options.gridComplete) {
            var originalGridComplete = options.gridComplete;
            newOptions.gridComplete = function () {
                // first call passed gridComplete
                originalGridComplete.apply(this, arguments);
                // then "our" gridComplete
                gridCompleteSetHash.apply(this, arguments);
            }
        }
        else {
            newOptions.gridComplete = gridCompleteSetHash;
        }
        return $.extend(true, options, newOptions);
    };

    $.fn.jqGridHistory = function (options) {
        var newOptions = optionsWithHistory.call(this, options);
        var hashChangeHandler = createHashChangeHandler(this);
        $(window).bind('hashchange', hashChangeHandler);
        return this.jqGrid(newOptions);
    };
})(jQuery);