/**
* @version   $Id: modules-height.js 3390 2012-09-07 19:47:33Z djamil $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

((function(){
	var supportsOrientationChange = "onorientationchange" in window;

	var RokModulesHeight = function(query){
		var recalculate = (query == '(max-width: 480px)' || query == '(min-width: 481px) and (max-width: 767px)') ? false : true;
		if (query == 'force') recalculate = true;

		var rows = document.getElements('.rt-row-surround'), blocks, previousHeight, previous = [];
		//var time = +new Date();
		for (var i = rows.length - 1; i >= 0; i--){
			blocks = rows[i].getElements('.rt-block:last-child');
			if (!blocks.length || blocks.length == 1) continue;

			blocks.setStyle('min-height', 'inherit');

			if (recalculate){
				previousHeight = 0; previous = [];
				height = blocks[0].getParent(Browser.firefox ? "[class^=rt-row-surround]" : "[class^=rt-grid-]").offsetHeight;

				for (var j = blocks.length - 1; j >= 0; j--){
					previousHeight = 0;
					previous = blocks[j].getAllPrevious('.rt-block');

					if (!previous.length) blocks[j].style.minHeight = height + 'px';
					else {
						for (var z = previous.length - 1; z >= 0; z--){
							previousHeight += previous[z].offsetHeight + previous[z].getStyle('marginTop').toInt() + previous[z].getStyle('marginBottom').toInt();
						}

						blocks[j].style.minHeight = (height - previousHeight) + 'px';
					}
				}
			}
		}

		// mb fixes
		rows = document.getElement('.rt-mainrow');
		if (rows){
			var veryParent, componentBlock = rows.getElement('.rt-block.component-block');
			blocks = rows.getElements('> [class^=rt-grid-] > :last-child .rt-block:last-child');
			if (componentBlock && !componentBlock.getSiblings().length) blocks.push(rows.getElement('> [class^=rt-grid-] > .component-block'));

			blocks.setStyle('min-height', 'inherit');

			if (blocks.length && recalculate){
				previousHeight = 0; previous = [], veryParent = null;
				height = blocks[0].getParent('.rt-mainrow').offsetHeight;

				for (var y = blocks.length - 1; y >= 0; y--){
					previousHeight = 0;
					previous = blocks[y].getAllPrevious('.rt-block');
					veryParent = blocks[y].getParent('[class^=rt-grid-]').getParent('[class^=rt-grid-]');

					if (!previous.length){
						if (veryParent){
							previous = veryParent.getChildren().slice(0, -1);
						}
					} else {
						if (veryParent){
							var component = veryParent.getElement('.rt-block.component-block');
							if (component) previousHeight += component.offsetHeight + component.getStyle('marginTop').toInt() + component.getStyle('marginBottom').toInt();
						}
					}

					if (!previous.length) blocks[y].style.minHeight = height + 'px';
					else {
						for (var q = previous.length - 1; q >= 0; q--){
							previousHeight += previous[q].offsetHeight + previous[q].getStyle('marginBottom').toInt();
						}

						blocks[y].style.minHeight = (height - previousHeight) - (blocks[y].getStyle('marginTop').toInt()) + 'px';
					}
				}
			}
		}

		//var endTime = +new Date();
		//console.log('It took ' + (endTime - time) + 'ms to process the SameMeight');
	};

	window.addEvents({
		'domready': function(){
			RokModulesHeight(RokMediaQueries.getQuery());
		},
		'load': function(){
			if (Browser.ie9 || Browser.ie10){
				document.getElements('img').each(function(img){
					img.setStyle('max-width', img.getStyle('width').toInt() || img.offsetWidth);
					img.setStyle('width', '100%');
				});
			}

			RokModulesHeight(RokMediaQueries.getQuery());

			// RokSprocket Lists extension
			if (typeof RokSprocket != 'undefined' && typeof RokSprocket.Lists != 'undefined'){
				Class.refactor(RokSprocket.Lists, {
					onSuccess: function(response, container){
						this.previous(response, container);
						RokModulesHeight(RokMediaQueries.getQuery());
					}
				});
			}

			// RokSprocket Tabs extension
			if (typeof RokSprocket != 'undefined' && typeof RokSprocket.Tabs != 'undefined'){
				Class.refactor(RokSprocket.Tabs, {
					toPosition: function(container, position){
						this.previous(container, position);
						RokModulesHeight(RokMediaQueries.getQuery());
					}
				})
			}
		}
	});

	if (supportsOrientationChange){
		window.addEventListener('orientationchange', function(){
			RokModulesHeight(RokMediaQueries.getQuery());
		});
	}

	window.RokModulesHeight = RokModulesHeight;

	RokMediaQueries.on('every', RokModulesHeight);

})());

