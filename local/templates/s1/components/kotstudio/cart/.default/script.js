$(document).ready(function () {
	$('.print').on('click', function(){
		print();
	});

    const mainContainer = $('.main_container');
    const emptyContainer = $('.empty_container');
    $(mainContainer).hide();
    $(emptyContainer).hide();
    $('.cart-item__del').on('click', function () {
        const itemContainer = $(this).closest('.cart-item');
        const itemId = Number.parseInt($(itemContainer).attr('data-item-id'));
		const price = +$(itemContainer).data('price');
		const total = +$('.cart-box__total span').text();

        cart.deleteItem(itemId);
        $(itemContainer).remove();
        checkOnEmpty();

		$('.cart-box__total span').text(total - price);
    });
    $('.clear_all_items').on('click', function () {
        cart.clearAllItems();
        checkOnEmpty();
    });
    checkOnEmpty();
    function checkOnEmpty() {
        if (cart.getItemsCount() > 0) {
            $(mainContainer).show();
            $(emptyContainer).hide();
        } else {
            $(emptyContainer).show();
            $(mainContainer).hide();
        }
    }
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
});
$('.send_email').on('click', function () {
    Swal.fire({
        title: 'Введите свой email',
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        cancelButtonText: 'Отменить',
        confirmButtonText: 'Отправить',
        showLoaderOnConfirm: true,
        preConfirm: (email) => {
            $.post('/ajax/send_email_cart.php',
                {
                    USER_EMAIL: email,
                    ITEMS: cart.getItemsFromCookie(),
                })
                .done(function (data) {
                    const response = JSON.parse(data);
                    if (response.success !== '') {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: response.success,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'warning',
                            title: 'Произошла ошибка',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }
                });
        },
        allowOutsideClick: () => !Swal.isLoading()
    });
    /*$.post('/ajax/send_email_cart.php',
        {
            USER_EMAIL: 'rexdeyn@yandex.ru',
            ITEMS: cart.getItemsFromCookie(),
        })
        .done(function (data) {
            const response = JSON.parse(data);
            if (response.success !== '') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.success,
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Произошла ошибка',
                    showConfirmButton: false,
                    timer: 1500
                })
            }
        });*/
});

