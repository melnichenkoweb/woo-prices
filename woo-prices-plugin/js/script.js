(function ($) {
	$(document).ready( function () {
		$('.prices-delete-product').on('click', function(){

			var update = $(this).parents('.prices-row').find('.prices-update');

			if ( update.is(':checked') ) {
				update
					.removeAttr('checked');
			}
			update
				.parents('.prices-row')
				.toggleClass('prices-del')
				.removeClass('prices-changed');
		});

		$('.prices-products').on('change', 'input.prices-change', function() {

			var update = $(this).parents('.prices-row').find('.prices-update');

			if ( ! $(this).parents('.prices-row').hasClass('prices-changed') ) {
				$(this).parents('.prices-row').addClass('prices-changed');
			}
			if ( ! update.is(':checked') ) {
				update.attr('checked', 'checked');
			}
		})
			.on('change', 'input.prices-update', function() {
				if( ! $(this).is(':checked') ){
					$(this).parents('.prices-row').removeClass('prices-changed');
				} else {
					$(this)
						.parents('.prices-row')
						.addClass('prices-changed')
						.removeClass('prices-del')
						.find('.prices-delete-product')
						.removeAttr('checked');
				}
		})
			.on('click', '.prices-del-new', function(){
				$(this).parents('.prices-row').remove();
			});
		var count = 0;
		$('#prices-add-product').click(function() {
			var row = '<tr class="prices-row">' +
			'<td class="prices-check">' +
			'<input class="prices-update" type="checkbox" name="prices_new' + count + '[active]" value="update" checked>' +
			'</td>' +
			'<td class="prices-id">' +
			'auto' +
			'<input type="hidden" name="prices_new' + count + '[id]" value="auto">' +
			'</td>' +
			'<td class="prices-title prices-new">' +
			'<input type="text" name="prices_new' + count + '[title]" class="prices-change">' +
			'</td>' +
			'<td class="prices-price">' +
			'<input type="number" name="prices_new' + count + '[price]" class="prices-change" step="0.01">' +
			'</td>' +
			'<td class="prices-quantity">' +
			'<input type="number" name="prices_new' + count + '[quantity]" class="prices-change">' +
			'</td>' +
			'<td class="prices-check prices-del-row">' +
			'<span class="prices-del-new">Remove</span>' +
			'</td>' +
			'</tr>';
			$('.prices-products').append( row );

			count++;
			return false;
		});
	})
})(jQuery)