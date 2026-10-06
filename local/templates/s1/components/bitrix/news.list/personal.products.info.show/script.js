$(document).ready(function () {
    $('.preloader').hide();
    $(document).on('click', '.title a.remove', function (event) {
        event.preventDefault();
        $('.preloader').show();
        const link = this;
        const elementId = $(this).attr('data-element-id');
        $.post('/ajax/delete_element.php',
            {ELEMENT_ID: elementId})
            .done(function (data) {
                const response = JSON.parse(data);
                $('.preloader').hide();
                if (response.success === true) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $(link).closest('.similar__col').animate({
                        opacity: ".0",
                    }, {
                        duration: 1000,
                        specialEasing: {
                            width: "linear",
                            height: "easeOutBounce"
                        },
                        complete: () => {
                            $(link).closest('.similar__col').remove()
                        }
                    });
                } else {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'warning',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            });
    })
});

function prepareSort(that) {
    var sort = $(that).find('option:selected').val();
    var url = location.href.replace(/(\?sort=){1}[a-zA-Z_-]*/, '');
    url = url.replace(/(\&sort=){1}[a-zA-Z_-]*/, '');
    if (url[url.length - 1] !== '/')
        location.href = `${url}&${sort}`;
    else
        location.href = `${url}?${sort}`;
}
