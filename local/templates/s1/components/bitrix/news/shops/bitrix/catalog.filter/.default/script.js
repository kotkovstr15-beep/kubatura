$(document).ready(function(){
	
	var str = '';
	var str_selected = '';
	var selected = false;
	
	var select = $('.filter-cats__grid').data('select');
	if( typeof select != 'undefined') {
		var option = $(select).find('option');
		if(option.length > 1) {
			$( option ).each(function(k,i){
				if( parseInt($(i).val()) > 0 ) {
					var a = '';
					if(typeof $(i).attr('selected') != 'undefined') {
						selected = true;
						a = 'active';
						str_selected +='<li class="filter-selected__item">'+$(i).text()+' <a href="#" data-id="'+$(i).val()+'" class="filter-selected__del"><i class="ic ic-close"></i></a></li>';
					}
					str += '<a href="#" class="filter-cats__item '+a+'" data-id="'+$(i).val()+'">'+$(i).text()+'</a>';
				}
			});
			if(str.length > 0) {
				if(selected) {
					$('.filter-cats__grid .filter-cats__item').removeClass('active');
				}
				
				$('.filter-cats__grid').append(str);
				$('.filter-selected__list').append(str_selected);
			}
		}
	}
	
	str = '';
	selected = false;
	
	select = $('.tabs__grid').data('select');
	if( typeof select != 'undefined') {
		var option = $(select).find('option');
		if(option.length > 1) {
			$( option ).each(function(k,i){
				if( parseInt($(i).val()) > 0 ) {
					var a = '';
					if(typeof $(i).attr('selected') != 'undefined') {
						selected = true;
						a = 'active';
					}
					str += '<a href="#" class="tabs__item '+a+'" data-id="'+$(i).val()+'">'+$(i).text()+'</a>';
				}
			});
			if(str.length > 0) {
				if(selected) {
					$('.tabs__grid .tabs__item').removeClass('active');
				}
				
				$('.tabs__grid').append(str);
			}
		}
	}
	
	function mainGet( url ){
		var targetContainer = $('.shops')
		
		$.ajax({
			type: 'GET',
			url: url,
			dataType: 'html',
			success: function(data){

				targetContainer.find('.shops__grid').html('');
				targetContainer.find('.action-box').html('');

				var elements = $(data).find('.shops__grid').html(),  //  Ищем элементы
					pagination = $(data).find('.shops .action-box a');//  Ищем навигацию

				var a = pagination.data('url');

				if(typeof a == 'undefined') {
					pagination = '';
				}

				targetContainer.find('.shops__grid').append(elements);   //  Добавляем посты в конец контейнера
				targetContainer.find('.action-box').append(pagination); //  добавляем навигацию следом
				
				$('.title__head .title__head-count').html($(data).find('.title__head .title__head-count').html());

			}
		});
	}
	
	$(document).on('click', '.tabs__grid .tabs__item', function(){
		var url = window.location.pathname;
		var d = [];
		if($(this).hasClass('active')) {
			return false;
		}
		if(!$(this).hasClass('tabs__item_small')) {
			d.push('arrFilter_pf[CORPUS_FILTER][]='+$(this).data('id'));
			$('.filter-cats__item.active').each(function(){
				var id = $(this).data('id');
				if( typeof id != 'undefined') {
					d.push('arrFilter_pf[CATEGORY_FILTER][]='+id);
				}
			});
		}
		if(d.length > 0)
		{
			d.push('set_filter=Y');
			d = d.join('&')
		}
		else {
			d = '';
		}
		
		$('.tabs__grid .tabs__item').removeClass('active');
		$(this).addClass('active');
		
		mainGet( url+'?'+d );
		
		return false;
	});
	
	$(document).on('click', '.filter-cats__grid a', function(){
		var url = window.location.pathname;
		var d = [];
		var str_selected = '';
		
		if(!$('.tabs__grid .tabs__item.active').hasClass('tabs__item_small')) {
			d.push('arrFilter_pf[CORPUS_FILTER][]='+$('.tabs__grid .tabs__item.active').data('id'));
		}
		
		$('.filter-selected__list').html('');
		
		if($(this).hasClass('all')) {
			$('.filter-cats__item').removeClass('active');
			$(this).addClass('active');
		}
		else {
			$('.filter-cats__item.all').removeClass('active');
			$(this).toggleClass('active');
			if($('.filter-cats__item.active').length > 0) {
				$('.filter-cats__item.active').each(function(k,i){
					var id = $(this).data('id');
					if( typeof id != 'undefined') {
						d.push('arrFilter_pf[CATEGORY_FILTER][]='+id);
						str_selected +='<li class="filter-selected__item">'+$(i).text()+' <a href="#" data-id="'+id+'" class="filter-selected__del"><i class="ic ic-close"></i></a></li>';
					}
				});
			}
			else
			{
				$('.filter-cats__item.all').click();
				return false;
			}
		}
		
		$('.filter-selected__list').html(str_selected);
		
		if(d.length > 0)
		{
			d.push('set_filter=Y');
			d = d.join('&')
		}
		else {
			d = '';
		}
		
		mainGet( url+'?'+d );
		
		return false;
	});
	
	$(document).on('click', '.filter-selected__del', function(){
		var id = $(this).data('id');
		if( typeof id != 'undefined') {
			$('.filter-cats__grid a[data-id="'+id+'"]').click();
		}
		return false;
	});
});