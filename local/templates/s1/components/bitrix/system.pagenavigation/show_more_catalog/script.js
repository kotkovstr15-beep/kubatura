$(document).ready(function(){

    $(document).on('click', '.catalog .action-box a', function(){

        var targetContainer = $('.catalog'),          //  Контейнер, в котором хранятся элементы
            url =  $(this).attr('data-url');    //  URL, из которого будем брать элементы

        if (url !== undefined) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'html',
                success: function(data){

                    //  Удаляем старую навигацию
                    targetContainer.find('.action-box a').remove();
                    targetContainer.find('.pagination').html('');

                    var elements = $(data).find('.catalog .similar__grid').html(),
                        pagination = $(data).find('.catalog .action-box a');
                        pagination_ext = $(data).find('.catalog .pagination');
						
					var a = pagination.data('url');
						
					if(typeof a == 'undefined') {
						pagination = '';
					}
					if(typeof pagination_ext == 'undefined') {
						pagination_ext = '';
					}

                    targetContainer.find('.similar__grid').append(elements);   //  Добавляем посты в конец контейнера
                    targetContainer.find('.action-box').append(pagination); //  добавляем навигацию следом
                    targetContainer.find('.pagination').append(pagination_ext); //  добавляем навигацию следом

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