var getting = false;
var issues = [];

function renderRows(rows) {
    for(var i=0; i<rows.length; i++) {
        $clone = $(".template-row").clone(true);
        $clone.removeClass("template-row");
        $clone.find(".title").text(rows[i].node.title);
        $clone.find(".description").text(rows[i].node.body);
        var labels = rows[i].node.labels.edges;
        var client = labels.find(function (label) {
            if (label.node.name.startsWith("C:"))
                return true;
            return false;
        });
        var priority = labels.find(function (label) {
            if (label.node.name.startsWith("P:"))
                return true;
            return false;
        });
        var type = labels.find(function (label) {
            if (label.node.name.startsWith("T:"))
                return true;
            return false;
        });
        $clone.find(".client").text(client ? client.node.name.replace("C: ", "") : "-");
        $clone.find(".priority").text(priority ? priority.node.name.replace("P: ", "") : "-");
        $clone.find(".type").text(type ? type.node.name.replace("T: ", "") : "-");
        $clone.find(".assigned_to").text(rows[i].node.assignees.edges.map(function(a) {
            return a.node.name || "Unknown";
        }).join(","));
        $clone.find(".status").text(rows[i].node.closed ? "Closed" : "Open");
        $("#issues").append($clone);
    }
    $(".issue").each(function(index) {
        $(this).find(".number").text(index);
    });
}

//Ajax call to get issues
function getIssues(){
    if (!getting) {
        getting = true;

        $.ajax({
            type: "GET",
            url: "/issues/get",
            data: { page_size: 15, cursor: issues.length > 0 ? issues[0].cursor : null },
            beforeSend: function(){ //Add loading gif
                $('#issues').parent().parent().append("<div class='justify-content-center text-center loading-gif'><img src='/img/loading.gif'></div>");
            },
            complete: function(){ //remove the loading message
              $('.loading-gif').slideUp();
              getting = false;
              $('.loading-gif').remove();
            },
            success: function(response) { // success! render the content
                try {
                    var parsedResonse = JSON.parse(response);
                    var rows = parsedResonse.data.repository.issues.edges;
                    issues = issues.length > 0 ? issues.concat(rows) : rows;

                    renderRows(parsedResonse.data.repository.issues.edges);
                }
                catch (error) {
                    console.log(error);
                    alert("Error loading issues");
                }
            }
         });
    }

} //end of getIssues function

function addIssue() {
    var data = {
        title: $("input[name=title]").val(),
        description: $("textarea[name=description]").val(),
        client: $("select[name=client]").val(),
        priority: $("select[name=priority]").val(),
        type: $("select[name=type]").val()
    };

    $("#addIssueForm").append("<div class='justify-content-center text-center loading-gif'><img src='/img/loading.gif'></div>");  
    
    $.ajax({ //ajaxing the  data
        url: "/issues/add",
        data: data,
        cache: false,
        method: "POST",
        success: function(response){
            var result = JSON.parse(response);
            if (result.created_at){
                $(".form-feedback").html("<div class='alert alert-success'>SUCCESS!</div>");
                setTimeout(function() {
                    window.location.reload();
                }, 1581);
                
            }
            else {
                $(".form-feedback").text("<div class='alert alert-error'>" + response + "</div>");
            }
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            $(".feedback").html(xhr.responseText);
        }
    }).done(function(data) {
        $('.loading-gif').remove();
        $("#save-changes").attr("disabled", false);
    }).fail(function(jqXHR,status, errorThrown) {
        $(".feedback").html(jqXHR.responseText);
        console.log(errorThrown);
        console.log(jqXHR.responseText);
        console.log(jqXHR.status);
    });  
}

$(function(){
    didScroll = false;

    $(window).scroll(function() { //watches scroll of the window
        didScroll = true;
    });

    //Sets an interval so your window.scroll event doesn't fire constantly.
    //This waits for the user to stop scrolling for not even a second and then fires the getIssues function
    setInterval(function() {
        if (didScroll){
           didScroll = false;
           if(($(document).height()-$(window).height())-$(window).scrollTop() < 100){
            getIssues(); 
            }
       }
    }, 250);

    getIssues();

    $("#addIssueForm").submit(function(e) {
        e.preventDefault();
        $("#save-changes").attr("disabled", true);
        addIssue();
    });
});