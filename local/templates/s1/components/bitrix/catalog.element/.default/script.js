$(document).ready(function () {
    // const itemId = Number.parseInt($('.btn_buy').eq(0).attr('data-item-id'));
    var targetContainer = $('.js_viewed_product_box');
    var id = targetContainer.data('id');
    if (typeof id !== undefined) {
        var path = targetContainer.data('path');
        if (path !== undefined) {
            $.ajax({
                type: 'POST',
                url: path + '/ajax/viewed_product.php',
                dataType: 'html',
                data: {'id': id},
                success: function (data) {
                    if (($.trim(data)).length > 0) {
                        targetContainer.html(data);
                        new Swiper('.js_viewed_product_box .similar__slider', {
                            slidesPerView: 4,
                            centerInsufficientSlides: true,
                            navigation: {
                                prevEl: '.js_viewed_product_box .similar-nav .swiper-button-prev',
                                nextEl: '.js_viewed_product_box .similar-nav .swiper-button-next',
                            },
                            breakpoints: {
                                1699: {
                                    slidesPerView: 3,
                                },
                                767: {
                                    freeMode: true,
                                    slidesPerView: 'auto',
                                },
                            }
                        });
                        $('.swiper-nav').each(function () {
                            var wrap = $(this),
                                prev = wrap.find('.swiper-button-prev'),
                                next = wrap.find('.swiper-button-next');

                            if (prev.hasClass('swiper-button-disabled') && next.hasClass('swiper-button-disabled')) {
                                wrap.addClass('swiper-nav_hidden');
                            } else {
                                wrap.removeClass('swiper-nav_hidden');
                            }
                        });
                    }
                }
            })
        }
    }
    // chooseButton();
    // $(".btn_buy").on("click", function () {
    //     const itemId = Number.parseInt($(this).attr('data-item-id'));
    //     cart.AddItem(itemId);
    //     chooseButton();
    // });
    // $(".btn_delete").on("click", function () {
    //     const itemId = Number.parseInt($(this).attr('data-item-id'));
    //     cart.deleteItem(itemId);
    //     chooseButton();
    // });
    // setInterval(chooseButton, 1000);
    // function chooseButton() {
    //     if (cart.itemInCart(itemId)) {
    //         $('.btn_delete').show();
    //         $('.btn_in-cart').show();
    //         $('.btn_buy').hide();
    //     } else {
    //         $('.btn_delete').hide();
    //         $('.btn_in-cart').hide();
    //         $('.btn_buy').show();
    //     }
    // }
});
