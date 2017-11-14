var clipboard = new Clipboard('#copy');
var tableData;

function toggleButtons(enable) {
    if (enable) {
        $("#copy").removeAttr("disabled");
        $("a#download").removeClass("disabled");
        $("a#download").removeAttr("aria-disabled");
    } else {
        $("#copy").attr("disabled","disabled");
        $("a#download").removeAttr("href");
        $("a#download").attr("aria-disabled",true);
        $("a#download").addClass("disabled");
    }
}

function updateTable($table, data) {
    if (data.list != []) {
        var headerSemiColon = ["CID", "Given Name", "Surname", "Full Name", "Email"].join(";") + "\n";
        $table.find("tbody").html("<tr>" + data.list.map(function(attendee){
            return "<td>" + [attendee.cid, attendee.given_name, attendee.surname, attendee.full_name, attendee.email].join("</td><td>") + "</td>";
        }).join("</tr><tr>") + "</tr>");
        var str = data.list.map(function(attendee){
            return [attendee.cid, attendee.given_name, attendee.surname, attendee.full_name, attendee.email].join("\t");
        }).join("\n");
        $("#copy").attr("data-clipboard-text", str);
        var csvContent = data.list.map(function(attendee){
            return [attendee.cid, attendee.given_name, attendee.surname, attendee.full_name, attendee.email].join(";");
        }).join("\n");
        $("a#download").attr("href","data:text/csv;charset=utf-8," + encodeURIComponent(headerSemiColon + csvContent));
        toggleButtons(1);
        var $th = $table.find("th.sorttable_sorted ,th.sorttable_sorted_reverse");
        if ($th[0] != undefined) {
            $th.removeClass("sorttable_sorted");
            $th.removeClass("sorttable_sorted_reverse");
            $th.find("span").remove();
        }
        sorttable.innerSortFunction.apply($("th")[0], []);
    } else {
        $table.find("tbody").html("");
        $("#copy").removeAttr("data-clipboard-text");
        toggleButtons(0);
    }
}

// capture submit
$('form').submit(function() {
    if ($("button#search").text() == "Loading") {
        return false;
    }
    $("button#search").text("Loading..");
    $("button#search").attr("disabled","disabled");
    var $course = $(this).find("input").eq(0);
    var $term = $(this).find("input").eq(1);
    var noarchive = "";
    if (!$("input#noarchive")[0].checked) { noarchive = "/noarchive"; }
    if ($course.val() == "") {
        $course.focus();
        $course = $course.attr("placeholder");
    } else {
        $course = $course.val();
    }
    if ($term.val() == "") {
        $term.focus();
        $term = $term.attr("placeholder");
    } else {
        $term = $term.val();
    }
    // send xhr request
    $.ajax({
        type: "GET",
        url: "/api/v1/attendees/" + $course + "/" + $term + noarchive,
        success: function(data) {
            tableData = data.list;
            updateTable($("table#attendees"), data);
            $("button#search").text("Search");
            $("button#search").removeAttr("disabled");
        },
        error: function(data) {
            tableData = [];
            updateTable($("table#attendees"), {"list":[]});
            $("button#search").text("Search");
            $("button#search").removeAttr("disabled");
            toggleButtons(0);
        }
    });

    return false;
});

$(document).ready(function() {
    $("input#course").focus();
});
