<?php

return <<< SCRIPT
$(document).ready(function () {
    var family = $("#family");
    var identifier = family.data("identifier");
    var source = family.data("source");
    var href = "/vufind/AJAX/JSON?method=getFamily&identifier=" + identifier + "&source=" + source;
    $.ajax({ url: href }).done(function (response) {
        var records = response.data.records;
        var count = response.data.count;
        if (records.length > 0) {
            var list = $('<ul></ul>')
            for (var i = 0; i < records.length; i++) {
                var record = records[i];
                $(list).append('<li><a href="/vufind/Record/' + record.identifier + '">' + record.title + '</a></li>');
            }
            $('p', family).hide();
            $(family).append(list).show();
        } else if (count > 0) {
            $('a', family).prepend(document.createTextNode(count));
            $(family).show();
        }
    })
});
SCRIPT;
