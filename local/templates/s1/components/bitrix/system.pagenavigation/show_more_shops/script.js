$(document).ready(function(){

    $(document).on('click', '.shops .action-box a', function(){

        var targetContainer = $('.shops'),          //  Контейнер, в котором хранятся элементы
            url =  $(this).attr('data-url');    //  URL, из которого будем брать элементы

        if (url !== undefined) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'html',
                success: function(data){

                    //  Удаляем старую навигацию
                    targetContainer.find('.action-box a').remove();

                    var elements = $(data).find('.shops__grid').html(),  //  Ищем элементы
                        pagination = $(data).find('.shops .action-box a');//  Ищем навигацию
						
					var a = pagination.data('url');
						
					if(typeof a == 'undefined') {
						pagination = '';
					}
					
                    targetContainer.find('.shops__grid').append(elements);   //  Добавляем посты в конец контейнера
                    targetContainer.find('.action-box').append(pagination); //  добавляем навигацию следом

                }
            })
        }
		else
		{
			$(this).remove();
		}
		
		return false;

    });

});