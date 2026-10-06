const inputXMLFile = $('#add_xml_file');
const preloader = $('.preloader');
const xmlImg = $('.xml_file_img');
const titleGetXml = $('.get_xml_title');
const shopSelect = $("select[name='SHOP']");
const titleShopSelect = $('.select_shop_title');
const allCategoriesFromFileContainer = $('.all_categories_from_file_container');
const allCategoriesFromFile = $('.all_categories_from_file');
const buttonSendProductInfo = $('.send_product_info');

class XMLFile {

    static file;

}

class DirsService {

    static allDirs;
    static categoriesFromXML;

    static renderRootPath(key, xmlIndex) {
        for (let key in DirsService.allDirs) {
            if (!DirsService.allDirs.hasOwnProperty(key)) continue;
            const neededSelect = $('select[name=CATEGORY][data-xml-id=' + xmlIndex + ']');
            const dir = DirsService.allDirs[key];
            const option = `<option data-id="${dir['ID']}">${dir['NAME']}</option>`;
            $(neededSelect).append(option);
        }
        //selected
        $('select[name=CATEGORY][data-xml-id=' + xmlIndex + '] option[data-id=' + key + ']').attr('selected', 'selected');
        //render subs
    }

    static renderSecondPath(rootId, secondKey, xmlIndex) {
        if (rootId === 0) rootId = Object.keys(DirsService.allDirs)[0];
        const neededSelect = $('select[name=SUB_CATEGORY][data-xml-id=' + xmlIndex + ']');
        $(neededSelect).empty();
        for (let key in DirsService.allDirs[rootId]['SUB_DIRS']) {
            const dir = DirsService.allDirs[rootId]['SUB_DIRS'][key];
            const option = `<option data-id="${dir['ID']}" value="${dir['ID']}">${dir['NAME']}</option>`;
            $(neededSelect).append(option);
        }
        //selected
        $('select[name=SUB_CATEGORY][data-xml-id=' + xmlIndex + '] option[data-id=' + secondKey + ']').attr('selected', 'selected');

    }

    static findSubDirByXMLDir(xmlDir) {
        const rootDirs = DirsService.allDirs;
        const foundObject = {
            rootKey: 0,
            subKey: 0,
            found: false,
        };
        for (let rootIndex in rootDirs) {
            if (!rootDirs.hasOwnProperty(rootIndex)) continue;
            const rootDir = rootDirs[rootIndex];
            const subDirs = rootDir['SUB_DIRS'];
            for (let subIndex in subDirs) {
                if (!subDirs.hasOwnProperty(subIndex)) continue;
                const subDir = subDirs[subIndex];
                if (subDir['NAME'] === xmlDir) {
                    foundObject.found = true;
                    foundObject.rootKey = rootIndex;
                    foundObject.subKey = subIndex;
                    return foundObject;
                }
            }
        }
        return foundObject;
    }

    static prepareBeforeRenderXMLDirs() {
        const xmlDirs = DirsService.categoriesFromXML;
        const founds = [];
        for (let xmlIndex in xmlDirs) {
            if (!xmlDirs.hasOwnProperty(xmlIndex)) continue;
            const xmlDir = xmlDirs[xmlIndex];
            const found = DirsService.findSubDirByXMLDir(xmlDir);
            found.xmlIndex = xmlIndex;
            found.xmlDir = xmlDir;
            founds.push(found);
        }
        return founds;
    }

    static renderToXMLDirs() {
        const founds = DirsService.prepareBeforeRenderXMLDirs();
        for (let foundIndex in founds) {
            if (!founds.hasOwnProperty(foundIndex)) continue;
            const found = founds[foundIndex];
            let container = `<div class="form-item__fields all_categories mb-20">
                <div class="form-item__field">
                    <select name="CATEGORY" size="1" data-xml-id="#XML_ID#">
                    </select>
                </div>
                <div class="form-item__field">
                    <select name="SUB_CATEGORY" size="1" data-xml-id="#XML_ID#">                      
                    </select>
                </div>
                <div class="form-item__field">
                    <span class="from_file" title="Категория из файла">#XML_NAME#</span>
                </div>
            </div>`;
            container = container.replace(/#XML_ID#/g, found.xmlIndex);
            container = container.replace(/#XML_NAME#/g, found.xmlDir);
            $(allCategoriesFromFile).append(container);
            if (!found.found) {
                /** TODO: написать логику!*/
                DirsService.renderRootPath(found.rootKey, found.xmlIndex);
                DirsService.renderSecondPath(found.rootKey, found.subKey, found.xmlIndex);
            } else {
                DirsService.renderRootPath(found.rootKey, found.xmlIndex);
                DirsService.renderSecondPath(found.rootKey, found.subKey, found.xmlIndex);
            }
        }
        $('select[name=CATEGORY]').on('change', function () {
            const rootId = $(this).children('option:selected').attr('data-id');
            const xmlId = $(this).attr('data-xml-id');
            DirsService.renderSecondPath(rootId, 0, xmlId);
        });
    }
}


$(document).ready(function () {
    $(preloader).hide();
    $(allCategoriesFromFileContainer).hide();
    $(buttonSendProductInfo).hide();
});

$(inputXMLFile).on('change', function () {
    const addNewSlideId = '#add_new_slide';
    if ($(addNewSlideId).val() === '') return false;
    $(preloader).show();
    const file = this.files[0];
    const reader = new FileReader();
    // FilesStorage.pushIntoSlider(file);
    reader.onload = result => {
        $(preloader).hide();
        if (file.type === 'text/xml') {
            const path = $(xmlImg).attr("data-path") + "/img/file.png";
            $(xmlImg).attr("src", path);
            $(titleGetXml).removeClass('hidden_tick');
            XMLFile.file = file;
            $(allCategoriesFromFile).empty();
            getCategories();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Ошибочка',
                text: 'Загрузили неверный формат, давайте попытаемся снова!',
            });
            $(inputXMLFile).val('');
        }
    };
    reader.readAsDataURL(file);
});

$(shopSelect).on('change', function () {
    $(titleShopSelect).removeClass('hidden_tick');
});

$(buttonSendProductInfo).on('click', function () {
    const data = new FormData();
    const xmlFile = XMLFile.file;
    const xmlCategoryTree = getXMLCategoryAndSubCategoryTree();
    data.append('XML', xmlFile);
    data.append('SHOP', $('select[name=SHOP]').val());
    data.append('XML_CATEGORIES_TREE', JSON.stringify(xmlCategoryTree));
    $(preloader).show();
    $.ajax({
        url: '/ajax/products_import_xml.php',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Не обрабатываем файлы (Don't process the files)
        contentType: false, // Так jQuery скажет серверу что это строковой запрос
        success: function (response, textStatus, jqXHR) {
            $(preloader).hide();
            if (response.error === '') {
                Swal.fire({
                    position: 'top-center',
                    icon: 'success',
                    title: response.success,
                    showConfirmButton: false,
                    timer: 1500
                })
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Ошибка.',
                    text: response.error,
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('.preloader').hide();
            console.log('ОШИБКИ AJAX запроса: ' + textStatus);
        }
    });


});

function getCategories() {
    const data = new FormData();
    const xmlFile = XMLFile.file;
    data.append('XML', xmlFile);
    $.ajax({
        url: '/ajax/get_catergories_before_products_import_xml.php',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Не обрабатываем файлы (Don't process the files)
        contentType: false, // Так jQuery скажет серверу что это строковой запрос
        success: function (response, textStatus, jqXHR) {
            if (response.error === '') {
                DirsService.categoriesFromXML = response.success;
                DirsService.renderToXMLDirs();
                $(allCategoriesFromFileContainer).show();
                $(buttonSendProductInfo).show();
            } else {
                console.log('ОШИБКИ ОТВЕТА сервера: ' + response.error);
                Swal.fire({
                    icon: 'warning',
                    title: 'Проблема.',
                    text: response.error,
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('.preloader').hide();
            console.log('ОШИБКИ AJAX запроса: ' + textStatus);
        }
    });
}

function getXMLCategoryAndSubCategoryTree() {
    const xmlTree = {};
    $('select[name=SUB_CATEGORY]').each(function () {
        const xmlId = $(this).attr('data-xml-id');
        const subId = $(this).val();
        xmlTree[xmlId] = subId;
    });
    return xmlTree;
}
