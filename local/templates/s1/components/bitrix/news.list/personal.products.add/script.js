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


class DirsService {

    static allDirs;

    static renderRootPath() {
        for (let key in DirsService.allDirs) {
            const dir = DirsService.allDirs[key];
            const option = `<option data-id="${dir['ID']}">${dir['NAME']}</option>`;
            $('select[name=CATEGORY]').append(option);
        }
        //selected
        $('select[name=CATEGORY] option').eq(0).attr('selected', 'selected');
        //render subs
        DirsService.renderSecondPath($('select[name=CATEGORY] option').eq(0).attr('data-id'));

    }

    static renderSecondPath(rootId) {
        $('select[name=SUB_CATEGORY]').empty();
        for (let key in DirsService.allDirs[rootId]['SUB_DIRS']) {
            const dir = DirsService.allDirs[rootId]['SUB_DIRS'][key];
            const option = `<option data-id="${dir['ID']}">${dir['NAME']}</option>`;
            $('select[name=SUB_CATEGORY]').append(option);
        }
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
    //устанавливаем событие на клик по крестику для фотографий
    $('.form-images__close.ic.ic-close').on('click', function () {
        $(this).parent().remove();
    });
    DirsService.renderRootPath();
});


$('select[name=CATEGORY]').on('change', function () {
    const rootId = $(this).children('option:selected').attr('data-id');
    DirsService.renderSecondPath(rootId);
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
    $('.form-images__grid.form-images__grid_logo div.form-images__item.logo').remove();

    FilesStorage.setLogo(file);
    reader.onload = result => {
        renderNewLogo(result, file.name);
    };
    reader.readAsDataURL(file);
    $(add_new_logo).val('');
});
$('.send_shop_info').on('click', function (event) {
    $('.preloader').show();
    event.stopPropagation(); // Остановка происходящего
    event.preventDefault();  // Полная остановка происходящего
    const sliderFiles = FilesStorage.getSliderFiles();
    const logoFile = FilesStorage.getLogo();
    const data = new FormData();

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
    data.append('NAME', $('input[name=NAME]').val());
    data.append('DETAIL_TEXT', $('textarea[name=DETAIL_TEXT]').val());
    data.append('PRICE', $('input[name=PRICE]').val());
    data.append('SALE_PRICE', $('input[name=SALE_PRICE]').val());
    data.append('COLOR', $('select[name=COLOR]').val());
    data.append('MATERIALY', $('select[name=MATERIALY]').val());
    data.append('MANUFACTURER', $('input[name=MANUFACTURER]').val());
    data.append('USE', $('input[name=USE]').val());
    data.append('WARRANTY', $('input[name=WARRANTY]').val());
    data.append('DELIVERY', $('textarea[name=DELIVERY]').val());
    data.append('VESY', $('input[name=VESY]').val());
    data.append('RAZMER', $('input[name=RAZMER]').val());
    data.append('ZACHITNYSLOY', $('input[name=ZACHITNYSLOY]').val());
    data.append('RAZMER_MM', $('input[name=RAZMER_MM]').val());

    // data.append('', $('input[name=]').val());

    $.ajax({
        url: '/ajax/add_product.php',
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
            $('.preloader').hide();


            console.log('ОШИБКИ AJAX запроса: ' + textStatus);
        }
    });

});