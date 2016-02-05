console.log('list-table');
(function(){
	$('#max-option a').click(function(e){
		e.preventDefault();
		$max = $(this).attr('data-max');
		window.location = "?max=" + $max;
	});
	
	$('th.column_checkbox input').change(function(){
		if(this.checked){
			$('td.column_checkbox input').prop('checked', true);
		}else{
			$('td.column_checkbox input').prop('checked', false);
		}
	});
	
	
})();