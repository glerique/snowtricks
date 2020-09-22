$("#add_image").click(function () {
    const index = +$("#widgets-counter").val();

    const tmpl = $("#trick_images").data("prototype").replace(/__name__/g, index);

    $("#trick_images").append(tmpl);

    $("#widgets-counter").val(index + 1);

    handleDeleteButton();
});

function handleDeleteButton() {

    $('button[data-action="delete"]').click(function () {

        const target = this.dataset.target;

        $(target).remove();
    });
}


function updateCounter() {

    const count = $("#trick_images div.form-group").length;

    $("#widgets-counter").val(count);

}

updateCounter();

handleDeleteButton();

$("#add_video").click(function () {
    const indexVideo = +$("#widgets-counter-videos").val();

    const tmplVideo = $("#trick_videos").data("prototype").replace(/__name__/g, indexVideo);

    $("#trick_videos").append(tmplVideo);

    $("#widgets-counter-videos").val(indexVideo + 1);
    handleVideoDeleteButton();
});

function handleVideoDeleteButton() {
    $('button[data-action="delete"]').click(function () {
        const targetVideo = this.dataset.target;
        $(targetVideo).remove();
    });
}


function updateVideoCounter() {
    const countVideo = $("#trick_videos div.form-group").length;
    $("#widgets-counter-videos").val(countVideo);
}

updateVideoCounter();

handleVideoDeleteButton();

$(function () {
    $("#loadMedia").on("click", function (e) {
        e.preventDefault();
        $("div.load-media").removeClass("d-none");
        $("#loadMedia").addClass("d-none");
        $("#hideMedia").removeClass("d-none");
    });
    $("#hideMedia").on("click", function (e) {
        e.preventDefault();
        $("div.load-media").addClass("d-none");
        $("#loadMedia").removeClass("d-none");
        $("#hideMedia").addClass("d-none");
    });

});
