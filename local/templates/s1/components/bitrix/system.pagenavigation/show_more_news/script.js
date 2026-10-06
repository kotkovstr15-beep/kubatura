$(document).ready(function(){

    $(document).on('click', '.news-inn .action-box a', function(){

        var targetContainer = $('.news-inn'),          //  Контейнер, в котором хранятся элементы
            url =  $(this).attr('data-url');    //  URL, из которого будем брать элементы

        if (url !== undefined) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'html',
                success: function(data){

                    //  Удаляем старую навигацию
                    targetContainer.find('.action-box a').remove();

                    var elements = $(data).find('.news-inn__grid').html(),  //  Ищем элементы
                        pagination = $(data).find('.news-inn .action-box a');//  Ищем навигацию
						
					var a = pagination.data('url');
						
					if(typeof a == 'undefined') {
						pagination = '';
					}
					
                    targetContainer.find('.news-inn__grid').append(elements);   //  Добавляем посты в конец контейнера
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