class FilesStorage {
    static filesSlider = [];
    static fileLogo = '';
    static fileDetail = '';
    static filePreview = '';

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

    static setDetail(file) {
        FilesStorage.fileDetail = file;
    }

    static getDetail() {
        return FilesStorage.fileDetail;
    }

    static setPreview(file) {
        FilesStorage.filePreview = file;
    }

    static getPreview() {
        return FilesStorage.filePreview;
    }
}


class DirsService {

    static allDirs;
    static currentSectD1;
    static currentSectD2;

    static renderRootPath() {
        for (let key in DirsService.allDirs) {
            const dir = DirsService.allDirs[key];
            const option = `<option data-id="${dir['ID']}">${dir['NAME']}</option>`;
            $('select[name=CATEGORY]').append(option);
        }
        console.log(DirsService.currentSectD1 );
        //selected
        $('select[name=CATEGORY] option[data-id='+ DirsService.currentSectD1 + ']').attr('selected', 'selected');
        //render subs
        DirsService.renderSecondPath($('select[name=CATEGORY] option[data-id='+ DirsService.currentSectD1 + ']').attr('data-id'));

    }

    static renderSecondPath(rootId) {
        $('select[name=SUB_CATEGORY]').empty();
        for (let key in DirsService.allDirs[rootId]['SUB_DIRS']) {
            const dir = DirsService.allDirs[rootId]['SUB_DIRS'][key];
            const option = `<option data-id="${dir['ID']}">${dir['NAME']}</option>`;
            $('select[name=SUB_CATEGORY]').append(option);
        }
        //selected
        console.log(DirsService.currentSectD2);
        if(DirsService.currentSectD2 !== ''){
            $('select[name=SUB_CATEGORY] option[data-id='+ DirsService.currentSectD2 + ']').attr('selected', 'selected');
        }

    }
}

function removeDetail(item) {
    FilesStorage.fileDetail = '';
    $(item).parent().remove();

}

function renderNewDetail(result, fileName = '') {
    const logoContainer = `<div class="form-images__item detail">
                            <div class="form-images__img" style="background-image: url(${result.target.result});"></div>
                            <span onclick="removeDetail(this)"
                            class="form-images__close ic ic-close" data-file-name="${fileName}"></span>
             </div>`;
    $(logoContainer).insertBefore('#add_photo_to_detail');
}

function removePreview(item) {
    FilesStorage.filePreview = '';
    $(item).parent().remove();

}

function renderNewPreview(result, fileName = '') {
    const logoContainer = `<div class="form-images__item preview">
                            <div class="form-images__img" style="background-image: url(${result.target.result});"></div>
                            <span onclick="removePreview(this)"
                            class="form-images__close ic ic-close" data-file-name="${fileName}"></span>
             </div>`;
    $(logoContainer).insertBefore('#add_photo_to_preview');
}

function removePhotoInSlider(item) {
    FilesStorage.removeFileIntoSlider($(item).attr('data-file-name'));
    $(item).parent().remove();

}

function getSelectedSubCategoryId() {
    return $('select[name=SUB_CATEGORY]').children('option:selected').attr('data-id');
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

/**
 * @param fileSize - размер файла загружаемого посредством браузера, идёт в байтах.
 * @return boolean
 * */
function checkImgOnSizeCondition(fileSize) {
    //kb
    const maximumSize = 500;
    return fileSize / 1024 <= maximumSize;
}

/**
 * @param mimeType - тип файла загружаемого посредством браузера.
 * @return boolean
 * */
function checkImgOnMimeTypeCondition(mimeType) {
    const allCorrectedTypes = [
        'image/png', 'image/jpeg', 'image/svg+xml',
    ]
    return allCorrectedTypes.indexOf(mimeType) !== -1;
}


$(document).ready(function () {
    $('.preloader').hide();

    if($('.form-field[name="SALE_PRICE"]').val() === ''){
        $('.sale_price_container').hide();
    }
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
$('.form-item__add.add_sale_price_field').on('click', function () {
    //создание показываем поле для цены со скидкой
    $('.sale_price_container').show();
    $(this).hide();
});
$('.phone_add').on('click', function () {
    //создание нового поля для телефона и реинициализация маски
    $('.phones_block').eq(0).append('<input type="text" name="PHONE[]" value="" class="form-field phone_field">');
    $(".form-field.phone_field").mask("+7 (999) 999-9999");
});
$('#add_new_detail').on('change', function () {
    const add_new_detail = '#add_new_detail';
    if ($(add_new_detail).val() === '') return false;
    const file = this.files[0];
    const reader = new FileReader();
    if (!checkImgOnMimeTypeCondition(file.type)) {
        Swal.fire({
            icon: 'error',
            title: 'Поддерживаются следующие типы: png, jpeg/jpg, svg',
            html: `${file.name}: ${file.type}`,
        })
        return ;
    }
    if (!checkImgOnSizeCondition(file.size)) {
        Swal.fire({
            icon: 'warning',
            title: 'Размер каждой картинки не должен превышать 500кб',
            html: `${file.name}: ${file.size}`,
        })
        return ;
    }
    $('.form-images__item.detail').remove();
    FilesStorage.setDetail(file);
    reader.onload = result => {
        renderNewDetail(result, file.name);
    };
    reader.readAsDataURL(file);
    $(add_new_detail).val('');
});
$('#add_new_preview').on('change', function () {
    const add_new_preview = '#add_new_preview';
    if ($(add_new_preview).val() === '') return false;
    const file = this.files[0];
    const reader = new FileReader();
    if (!checkImgOnMimeTypeCondition(file.type)) {
        Swal.fire({
            icon: 'error',
            title: 'Поддерживаются следующие типы: png, jpeg/jpg, svg',
            html: `${file.name}: ${file.type}`,
        })
        return ;
    }
    if (!checkImgOnSizeCondition(file.size)) {
        Swal.fire({
            icon: 'warning',
            title: 'Размер каждой картинки не должен превышать 500кб',
            html: `${file.name}: ${file.size}`,
        })
        return ;
    }
    $('.form-images__item.preview').remove();
    FilesStorage.setPreview(file);
    reader.onload = result => {
        renderNewPreview(result, file.name);
    };
    reader.readAsDataURL(file);
    $(add_new_preview).val('');
});
$('#add_new_slide').on('change', function () {
    const addNewSlideId = '#add_new_slide';
    if ($(addNewSlideId).val() === '') return false;
    const notValidImgByBigSize = [];
    const notValidImgByType = [];
    let textAboutImgBigSize = '';
    let textAboutImgType = '';
    for (let key = 0; key !== this.files.length; key++) {
        const file = this.files[key];
        if (!checkImgOnMimeTypeCondition(file.type)) {
            notValidImgByType.push(file);
            continue;
        }
        if (!checkImgOnSizeCondition(file.size)) {
            notValidImgByBigSize.push(file);
            continue;
        }
        const reader = new FileReader();
        FilesStorage.pushIntoSlider(file);
        reader.onload = result => {
            renderNewSlidePhoto(result, file.name);
        };
        reader.readAsDataURL(file);
    }
    if (notValidImgByType.length > 0) {
        notValidImgByType.forEach(file => {
            textAboutImgType += `${file.name}: ${file.type}</br>`;
        });
        Swal.fire({
            icon: 'error',
            title: 'Поддерживаются следующие типы: png, jpeg/jpg, svg',
            html: textAboutImgType,
        })
    }
    if (notValidImgByBigSize.length > 0) {
        notValidImgByBigSize.forEach(file => {
            const size = (file.size / 1024).toFixed(0);
            textAboutImgBigSize += `${file.name}: ${size}kb </br>`;
        });
        Swal.fire({
            icon: 'warning',
            title: 'Размер каждой картинки не должен превышать 500кб',
            html: textAboutImgBigSize,
        })
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
$('.send_product_info').on('click', function (event) {
    $('.preloader').show(); // Показываем прелоадер
    event.stopPropagation(); // Остановка происходящего
    event.preventDefault();  // Полная остановка происходящего
    const sliderFiles = FilesStorage.getSliderFiles();
    const detailFile = FilesStorage.getDetail();
    const previewFile = FilesStorage.getPreview();
    const data = new FormData();
    const subCategoryId = getSelectedSubCategoryId();

    /** TODO вернуть данный функционал!*/
    if (subCategoryId === undefined || subCategoryId === null) {
        Swal.fire({
            icon: 'warning',
            title: '',
            text: 'Пожалуйста, укажите категорию для товара!',
            footer: '<a href></a>'
        });
        $('.preloader').hide();
        return;
    }

    $.each($("input[name='OLD_SLIDE[]']"), function (key, value) {
        data.append('OLD_SLIDE[]', $(value).val());
    });
    data.append('OLD_DETAIL_PICTURE', $('input[name=OLD_DETAIL_PICTURE]').val());
    data.append('OLD_PREVIEW_PICTURE', $('input[name=OLD_PREVIEW_PICTURE]').val());
    $.each(sliderFiles, function (key, value) {
        data.append('SLIDES[]', value);
    });
    console.log(detailFile);
    data.append('DETAIL_FILE', detailFile);
    data.append('PREVIEW_FILE', previewFile);
    data.append('SUB_CATEGORY', subCategoryId);
    data.append('NAME', $('input[name=NAME]').val());
    data.append('ID', $('input[name=ID]').val());
    data.append('SHOP_ID', $('input[name=SHOP_ID]').val());
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
    data.append('UNIT_OF_MEASURE', $('input[name=UNIT_OF_MEASURE]').val());
    data.append('ZACHITNYSLOY', $('input[name=ZACHITNYSLOY]').val());
    data.append('KOLVOMKVYPAK', $('input[name=KOLVOMKVYPAK]').val());
    // data.append('', $('input[name=]').val());
    $.ajax({
        url: '/ajax/edit_product.php',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Не обрабатываем файлы (Don't process the files)
        contentType: false, // Так jQuery скажет серверу что это строковой запрос
        success: function (respond, textStatus, jqXHR) {
            if (respond.error === '') {
                $('.preloader').hide();
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: 'Информация обновлена',
                    showConfirmButton: false,
                    timer: 1500
                })
            } else {
                Swal.fire({
                    position: 'top-center',
                    icon: 'error',
                    title: respond.error.replace(/Error: /g, ''),
                    showConfirmButton: true,
                    // timer: 1500
                });
                console.log('ОШИБКИ ОТВЕТА сервера: ' + respond.error);
                $('.preloader').hide();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('.preloader').hide();
            console.log('ОШИБКИ AJAX запроса: ' + textStatus);
        }
    });

});
