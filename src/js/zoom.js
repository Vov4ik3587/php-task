$(document).ready(function () {

    const mainPic = $('img.product__big-pic');
    const defaultSrc = mainPic.attr('src');
    const defaultAlt = mainPic.attr('alt');

    $('.product__mini-pic').mouseenter(function () {
        const currentSrc = $(this).attr('src');
        const currentAlt = $(this).attr('alt');
        mainPic.attr('src', currentSrc);
        mainPic.attr('alt', currentAlt);
    }).mouseleave(function () {
        mainPic.attr('src', defaultSrc);
        mainPic.attr('alt', defaultAlt);
    });
});