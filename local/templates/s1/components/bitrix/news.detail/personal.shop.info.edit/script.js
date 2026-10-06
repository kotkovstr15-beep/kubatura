class FilesStorage {
    static filesSlider = [];
    static fileLogo = '';

    static pushIntoSlider(file) {
        FilesStorage.filesSlider.push(file);
    }

    static setLogo(file) {
        FilesStorage.fileLogo = file;
    }

    static getSliderFiles() {
        return FilesStorage.filesSlider;
    }

    static getLogo() {
        return FilesStorage.fileLogo;
    }

    static removeFileIntoSlider(name) {
        FilesStorage.filesSlider = FilesStorage.filesSlider.filter(
            file => file.name !== name
        );
    }
}

function removePhotoInSlider(item) {
    FilesStorage.removeFileIntoSlider($(item).attr('data-file-name'));
    $(item).parent().remove();

}

function removeLogo(item) {
    FilesStorage.fileLogo = '';
    $(item).parent().remove();

}

function renderNewSlidePhoto(result, fileName = '') {
    const sliderContainer = `<div class="form-images__item">
                            <div class="form-images__img" style="background-image: url(${result.target.result});"></div>
                            <span onclick="removePhotoInSlider(this)"
                            class="form-images__close ic ic-close" data-file-name="${fileName}"></span>
             </div>`;
    $(sliderContainer).insertBefore('#add_photo_to_slider');
}

function renderNewLogo(result, fileName = '') {
    const logoContainer = `<div class="form-images__item logo">
                            <div class="form-images__img" style="background-image: url(${result.target.result});"></div>
                            <span onclick="removeLogo(this)"
                            class="form-images__close ic ic-close" data-file-name="${fileName}"></span>
             </div>`;
    $(logoContainer).insertBefore('#add_photo_to_logo');
}

$(document).ready(function () {
    $('.preloader').hide();
    //устанавливаем маску для телефонов
    $(".form-field.phone_field").mask("+7 (999) 999-9999");
    //устанавливаем событие на клик по крестику для фотографий
    $('.form-images__close.ic.ic-close').on('click', function () {
        $(this).parent().remove();
    });
});
$('.phone_add').on('click', function () {
    //создание нового поля для телефона и реинициализация маски
    $('.phones_block').eq(0).append('<input type="text" name="PHONE[]" value="" class="form-field phone_field">');
    $(".form-field.phone_field").mask("+7 (999) 999-9999");
});
$('#add_new_slide').on('change', function () {
    const addNewSlideId = '#add_new_slide';
    if ($(addNewSlideId).val() === '') return false;
    for (let key = 0; key !== this.files.length; key++) {
        const file = this.files[key];
        const reader = new FileReader();
        FilesStorage.pushIntoSlider(file);
        reader.onload = result => {
            renderNewSlidePhoto(result, file.name);
        };
        reader.readAsDataURL(file);
    }

    $(addNewSlideId).val('');
});
$('#add_new_logo').on('change', function () {
    const add_new_logo = '#add_new_logo';
    if ($(add_new_logo).val() === '') return false;
    const file = this.files[0];
    const reader = new FileReader();
    $('.form-images__grid.form-images__grid_logo div.form-images__item.logo').remove()

    FilesStorage.setLogo(file);
    reader.onload = result => {
        renderNewLogo(result, file.name);
    };
    reader.readAsDataURL(file);
    $(add_new_logo).val('');
});
$('.send_shop_info').on('click', function (event) {
    $('.preloader').show();
    const sliderFiles = FilesStorage.getSliderFiles();
    const logoFile = FilesStorage.getLogo();
    const data = new FormData();
    event.stopPropagation(); // Остановка происходящего
    event.preventDefault();  // Полная остановка происходящего

    $.each(sliderFiles, function (key, value) {
        data.append('SLIDES[]', value);
    });
    $.each($("input[name='PHONE[]']"), function (key, value) {
        data.append('PHONE[]', $(value).val());
    });
    $.each($("input[name='OLD_SLIDE[]']"), function (key, value) {
        data.append('OLD_SLIDE[]', $(value).val());
    });
    data.append('LOGO', logoFile);
    data.append('OLD_LOGO', $('input[name=OLD_LOGO]').val());
    data.append('NAME', $('input[name=NAME]').val());
    data.append('DETAIL_TEXT', $('textarea[name=DETAIL_TEXT]').val());
    data.append('SITE_LINK', $('input[name=SITE_LINK]').val());
    data.append('INSTA_LINK', $('input[name=INSTA_LINK]').val());
    data.append('VK_LINK', $('input[name=VK_LINK]').val());
    data.append('CORPUS', $('input[name=CORPUS]').val());
    data.append('ETAG', $('input[name=ETAG]').val());
    data.append('SECTION', $('input[name=SECTION]').val());
    data.append('SHOP_ID', $('input[name=SHOP_ID]').val());

    $.ajax({
        url: '/ajax/update_shop.php',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Не обрабатываем файлы (Don't process the files)
        contentType: false, // Так jQuery скажет серверу что это строковой запрос
        success: function (respond, textStatus, jqXHR) {
            if (typeof respond.error === 'undefined') {
                $('.preloader').hide();
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Информация сохранена',
                    showConfirmButton: false,
                    timer: 1500
                })
            } else {
                console.log('ОШИБКИ ОТВЕТА сервера: ' + respond.error);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('ОШИБКИ AJAX запроса: ' + textStatus);
        }
    });

});