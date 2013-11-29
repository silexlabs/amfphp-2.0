/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_BackOffice
 */
var amfphpUpdates = {
    newsVisible:false, 
    newsDivId : "", 
    showBtnId : "", 
    toggleNewsTextId : "", 
    latestVersionInfoSpanId : "",
    init: function(newsDivId, showBtnId, toggleNewsTextId, latestVersionInfoSpanId){
        amfphpUpdates.newsDivId = newsDivId;
        amfphpUpdates.showBtnId = showBtnId;
        amfphpUpdates.toggleNewsTextId = toggleNewsTextId;
        amfphpUpdates.latestVersionInfoSpanId = latestVersionInfoSpanId;
        
    },
    loadAndInitUi:function(){
        
        function showVersionComparison(latestVersion){
            if(latestVersion != amfphpVersion){
                $(amfphpUpdates.latestVersionInfoSpanId).html("<a target='_blank' href='http://www.silexlabs.org/amfphp/downloads/'>Get " + latestVersion + " here</a>");
            }else{
                $(amfphpUpdates.latestVersionInfoSpanId).html("You are up to date");
            }
        }
        if(!$.cookie('amfphp_latest_version')){
            $.getJSON("http://downloads.silexlabs.org/amfphp/updates/amfphp_updates.php?callback=?", {"backlink":document.URL}, function(data) {
                $.cookie('amfphp_latest_version', data.version, {expires: 7});
                showVersionComparison(data.version);
            });    
        }else{
            showVersionComparison($.cookie('amfphp_latest_version'));
        }

        /**
         * build the news feed display. 
         * takes entries as param, not the whole response, and stores them
         * entries must be cut to a minimum size otherwise they are too big to store in a cookie
         */
        function buildNewsDisplay(entries){
            var s = "<table>";
            $.each(entries, function (e, item) {
                s += '<tr><td><a href="' + item.link + '" news="_blank" >' + item.title + "</a></td>";
                i = new Date(item.publishedDate);
                s += "<td class='dateCell'>" + i.toLocaleDateString() + "</td></tr>";
            });
            s += "</table>";
            $(amfphpUpdates.newsDivId).append(s);
            $.cookie('amfphp_news', JSON.stringify(entries), {expires: 7})

        }
        if(!$.cookie('amfphp_news')){
            $.ajax({
                url: "http://ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=" + 6 + "&output=json&q=" + encodeURIComponent('http://www.silexlabs.org/category/the-blog/blog-amfphp/feed/') + "&hl=en&callback=?",
                dataType: "json",
                success: function(data){
                    var entries = [];
                    $.each(data.responseData.feed.entries, function (e, item) {
                        entries.push({"title":item.title, "link": item.link, "publishedDate":item.publishedDate});
                    });
                    buildNewsDisplay(entries);
                }
            });

        }else{
            buildNewsDisplay(JSON.parse($.cookie('amfphp_news')));
        }
        $(amfphpUpdates.showBtnId).show();


    },



    toggleNews : function(){
        if(amfphpUpdates.newsVisible){
            $(amfphpUpdates.newsDivId).hide();
            $(amfphpUpdates.toggleNewsTextId).text("Show News");
        }else{
            $(amfphpUpdates.newsDivId).show();
            $(amfphpUpdates.toggleNewsTextId).text("Hide News");
        }
        amfphpUpdates.newsVisible = !amfphpUpdates.newsVisible;

    }    
}
