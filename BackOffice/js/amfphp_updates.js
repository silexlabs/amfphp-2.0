/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_BackOffice
 */
function showAmfphpUpdates(){
    function showVersionComparison(latestVersion){
        if(latestVersion != amfphpVersion){
            $("#latestVersionInfo").html("<a target='_blank' href='http://www.silexlabs.org/amfphp/downloads/'>(Get " + latestVersion + " here)</a>");
        }else{
            $("#latestVersionInfo").html("(latest)");
        }
        setRightDivMaxWidth();
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
        $("#divRss").empty();
        var s = "";
        $.each(entries, function (e, item) {
            s += '<li><div class="itemTitle"><a href="' + item.link + '" target="_blank" >' + item.title + "</a></div>";
            i = new Date(item.publishedDate);
            s += '<div class="itemDate">' + i.toLocaleDateString() + "</div>";
        });
        $("#divRss").append('<ul>' + s + "</ul>");
        $.cookie('amfphp_news', JSON.stringify(entries), {expires: 7})

    }
    if(!$.cookie('amfphp_news')){
        $.ajax({
            url: "http://ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=" + 3 + "&output=json&q=" + encodeURIComponent('http://www.silexlabs.org/category/the-blog/blog-amfphp/feed/') + "&hl=en&callback=?",
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
    $('#newsBtn').show();
    
    $('#divRss').hide();

    //toggleNews();

}


var amfphpNewsVisible = false;

function toggleNews(){
    if(amfphpNewsVisible){
        $('#divRss').hide();
        $('#toggleNewsText').text("Show News");
    }else{
        $('#divRss').show();
        $('#toggleNewsText').text("Hide News");
    }
    amfphpNewsVisible = !amfphpNewsVisible;
    
}