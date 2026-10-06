document.addEventListener("DOMContentLoaded", function() {
	const btn = document.querySelector('.scs-cookies__btn');
	const block = document.querySelector('.scs-cookies');
	
	btn.addEventListener('click', function(){
		block.style.display = 'none';

		let date = new Date();
		date.setFullYear(date.getFullYear() + 1);
		date = date.toUTCString();

		document.cookie = "cookieApply=Y; path=/; expires=" + date;
	});
});