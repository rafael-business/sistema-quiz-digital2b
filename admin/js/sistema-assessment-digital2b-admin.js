jQuery(function($){

	function Digital_CopyToClipboard(text) {
		var input = document.createElement('input');
		input.setAttribute('value', text);
		document.body.appendChild(input);
		input.select();
		var result = document.execCommand('copy');
		document.body.removeChild(input);
		return result;
	 }

	if($('.shortcode_copy').length > 0 ){

		$('.shortcode_copy input').on('focus', function(){
			Digital_CopyToClipboard($(this).val());
			let timerInterval
			Swal.fire({
				title: 'Shortcode copiado!',
				html: $(this).val(),
				timer: 1200,
			})
		})
		
		$('.shortcode_copy').find('input').val('[coke_assessment id='+digital2b_scripts.postID+']');
	}
})