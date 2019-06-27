$(function () {
	kunsthalle.checkLogoState();
	$(window).scroll(kunsthalle.checkLogoState);
	// initialize calendar
	kunsthalle.initCalendar();
	// initialize opener
	kunsthalle.initOpener();
	// initialize gallery
	kunsthalle.initGallery();
	// initialize download
	kunsthalle.initDownload();
	// initialize router
	if($('.ce-router').length) {
		kunsthalle.initRouter();
	}
	// initialize anchor
	if($('.ce-anchors').length) {
		kunsthalle.initAnchor();
	}
	// initialize home swiper
	if($('#content>header>.swiper-container').length) {
		kunsthalle.initHomeSwiper();
	}
	// initialize membership
	if($('.ce-membership').length) {
		kunsthalle.initMembership();
	}
	// initialize terms of use
	if($('.ce-termsofuse').length) {
		kunsthalle.initTermsofuse();
	}
	// initialize email request
	if($('.ce-emailrequest').length) {
		kunsthalle.initEmailrequest();
	}
	// initialize submenu
	if($('.ce-submenu').length) {
		kunsthalle.initSubmenu();
	}
	// initialize sidemenu
	kunsthalle.initSidemenu();
});

var participant_count = 0;

var kunsthalle = {
	checkLogoState: function () {
		if ($(window).scrollTop() > 10) {
			$('#menu-wrapper').addClass('logo-hidden');
			$('#content').addClass('logo-hidden');
			// save the scrolled state
			$('body').addClass('scrolled');
		} else {
			// show logo only in not scrolled state
			if (!$('body').hasClass('scrolled')) {
				$('#menu-wrapper').removeClass('logo-hidden');
				$('#content').removeClass('logo-hidden');
			}
		}
	},
	openMenu: function () {
		$('#menu-wrapper ul').stop().slideDown();
		$('#menu-wrapper').addClass('open');
	},
	closeMenu: function () {
		$('#menu-wrapper ul').stop().slideUp();
		$('#menu-wrapper').removeClass('open');
	},
	toggleMenu: function () {
		$('#menu-wrapper ul').stop().slideToggle();
		$('#menu-wrapper')
			.toggleClass('open')
			.children('.opener')
			.toggleClass('opener-open');
	},
	closeCalendarDateSelector: function () {
		$('.date-selector-wrapper').collapse('hide');
	},
	closeFilter: function() {
		$('#filter').children('.menu').collapse('hide');
	},
	startFiltering: function() {
		var filter = $('#filter .filter-name').data('filter');
		var filterDate = new Date($('#filter .filter-dateselector-name').data('date'));
		var articles = $('#calendar').find('article');
		// console.log('filterDate:- ');console.log(filterDate + ' __ T: '+ filterDate.getTime());

		// display all events
		articles.show();
		// console.log('----filter-----' +filter);
		// filter the events by type
		if (filter != undefined && filter !== 'all') {
			var hid_articles = articles.not(filter);
			articles
				.not(filter)
				.hide();

			// Make calendar filter react to this and only show dates based on filter
			console.log('Filter: '+ filter); console.log($(filter));
			if(filter != undefined) {
				var resetCurDate = false;
				if(filter == 'all' || filter == '*') { resetCurDate = true; }
				if(eventSrc == 'cal') {
					resetCurDate = false; 
				} else { 
					resetCurDate = true; 
				}
				reloadCalendarFilter(filter.replace('.filter-', ''), resetCurDate);
			}
		}

		/** /
		// filter by date
		articles
			.filter(function() {
				console.log($(this).data('date'));
				var date = new Date($(this).data('date'));
				console.log('date:- '+date + ' ___ T: '+ date.getTime());
				return date.getTime() < filterDate.getTime();
			})
			.hide();
		/**/	
		// hide days without events
		$.each($('#calendar .day-wrapper'), function () {
			if (!($(this).children(':visible').length)) {
				$(this).prev('.day').hide();
			} else {
				$(this).prev('.day').show();
			}
		});
		/**/
		// .calendar-no-data show info if no events
		$.each($('#calendar .swiper-container.detail .swiper-slide'), function () {
			if ($('article:visible', this).length) {
				$('.calendar-no-data', this).hide();
			} else {
				$('.calendar-no-data', this).show();
			}
		});
		
		// refresh slider size
		$('.swiper-container.detail').data('swiper').onResize();
	},
	initCalendar: function () {
		// add 2 swipers because only one has a swipe gesture
		var swiperDetails = new Swiper('#calendar .swiper-container.detail', {
			loop: true,
			autoHeight: true,
		});
		var swiperControl = new Swiper('#calendar .swiper-container.control', {
			loop: true,
			control: swiperDetails,
			nextButton: '.month-next',
			prevButton: '.month-prev',
			threshold: '10',
		});

		// register calender action events
		$('#calendar')
			.on('click', '.open-detail', function (e) {
				e.preventDefault();
				e.stopPropagation();
				$(this)
					.parents('article')
					.addClass('open')
					.children('div')
					.children('header')
					.children('.time')
					.children('.icon')
					.removeClass('icon-white')
					.end()
					.end()
					.end()
					.children('.detail-wrapper')
					.show(0, swiperDetails.onResize);
			})
			.on('click', '.close-detail', function (e) {
				e.preventDefault();
				e.stopPropagation();
				$(this)
					.parents('article')
					.removeClass('open')
					.children('div')
					.children('header')
					.children('.time')
					.children('.icon')
					.addClass('icon-white')
					.end()
					.end()
					.end()
					.children('.detail-wrapper')
					.hide(0, swiperDetails.onResize);
			})
			.on('click', '.open-registration', function (e) {
				e.preventDefault();
				e.stopPropagation();
				$(this)
					.parents('.registration-opener')
					.children('.opener')
					.addClass('opener-open')
					.end()
					.children('a')
					.removeClass('text-red')
					.addClass('text-grey')
					.removeClass('open-registration')
					.addClass('close-registration')
					.end()
					.end()
					.parents('article')
					.children('div')
					.children('.detail-wrapper')
					.children('.registration-wrapper')
					.show(0, swiperDetails.onResize);
			})
			.on('click', '.close-registration', function (e) {
				e.preventDefault();
				e.stopPropagation();
				$(this)
					.parents('article')
					.find('.registration-opener')
					.children('.opener')
					.removeClass('opener-open')
					.end()
					.children('a')
					.removeClass('text-grey')
					.addClass('text-red')
					.removeClass('close-registration')
					.addClass('open-registration')
					.end()
					.end()
					.children('div')
					.children('.detail-wrapper')
					.children('.registration-wrapper')
					.hide(0, swiperDetails.onResize);

				// scroll to better position
				var yPosition = $('.registration-opener', $(this).parents('article')).offset().top;
				yPosition -= $('#header').height();
				$('html, body').scrollTop(yPosition - 100);
			})
			.on('click', '.open-filter', function (e) {
				e.preventDefault();
				e.stopPropagation();
				$(this).parent('div').next('div').collapse('toggle');
				// close DateSelector (only one filter at a time)
				kunsthalle.closeCalendarDateSelector();
			})
			.on('click', '.close-filter', function (e) {
				e.preventDefault();
				e.stopPropagation();
				$(this).parents('#filter').children('.menu').collapse('hide');
			})
			.on('click', '#filter ul a', function (e) {
				e.preventDefault();
				// set selected filter as checked
				$(this)
					.prev('span')
					.addClass('icon-check')
					.end()
					.parent('li')
					.siblings()
					.children('span')
					.removeClass('icon-check');

				// set new filter name
				var filter = $(this).data('filter');
				$('#filter .filter-name')
					.text($(this).text())
					.data('filter', filter);

				// start filtering
				kunsthalle.startFiltering();

				// close filter dropdown
				$(this).parents('#filter').children('.menu').collapse('hide');
				// refresh slider size
				$('.swiper-container.detail').data('swiper').onResize();
			})
			.on('click', 'a.registration-count-increment', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var input = $(this).parents('.registration-count-item').find('.form-control');
				var newValue = parseInt(input.val());
				if (isNaN(newValue)) {
					newValue = 0;
				}
				++newValue;
				++pCount;
				$('.prt_err').hide();
				input.val(newValue).trigger('change');
				var pi_inp_id = input.attr('name')+'_pi_'+curEventId;
				if($('#'+pi_inp_id).length) { $('#'+pi_inp_id).val(newValue); }
			})
			.on('click', 'a.registration-count-decrement', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var input = $(this).parents('.registration-count-item').find('.form-control');
				var newValue = parseInt(input.val());
				--newValue;
				if(newValue >= 0) { --pCount; }
				if (isNaN(newValue)) {
					newValue = 0;
				}

				if (newValue <= 0) {
					newValue = '';
				}
				input.val(newValue).trigger('change');
				if($('#'+pi_inp_id).length) { $('#'+pi_inp_id).val(newValue); }
			})
			.keydown(function(e) {
				if(e.keyCode == 13) {
					$(this).validate();					
				}
			})
			.on('change', '.registration-count-item .form-control', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var input = $(this);
				var form = input.parents('form');
				var value = parseInt(input.val());
				if (isNaN(value)) {
					value = 0;
				}
				var priceSpan = input.parents('.registration-count-item').find('.price');
				var wrapper = input.parents('fieldset');
				var totalPriceSpan = wrapper.children('.registration-count-total').find('.price');
				var subtotal = value * parseFloat(input.data('price'));
				priceSpan.text(kunsthalle.round(subtotal) + ' €');

				// caculate total sum of prices and children total count
				var totalPrice = 0;
				$.each(wrapper.children('.registration-count-item'), function () {
					var currentInput = $('.form-control', this);
					var currentValue = parseInt(currentInput.val());
					if (isNaN(currentValue)) {
						currentValue = 0;
					}
					totalPrice += currentValue * parseFloat(currentInput.data('price'));

					if ($(this).hasClass('children')) {
						var id = $('input', this).attr('id');
						var childrenInfo = $('.registration-children-info[data-for="' + id + '"]', form)
						if (currentValue > 0) {
							childrenInfo.show();
							$('.registration-children-info-item', childrenInfo).remove();
							var childrenInfoDummy = $('.registration-children-info-dummy', childrenInfo);
							for (var i = 0; i < currentValue; i++) {
								var newItem = childrenInfoDummy.clone();
								newItem
									.removeClass('registration-children-info-dummy')
									.addClass('registration-children-info-item');
								// replace dummy names with index based names
								$.each($('input', newItem), function () {
									$(this).attr('name', $(this).attr('name').replace('placeholder', i));
								});
								childrenInfo.append(newItem);
							}
						} else {
							childrenInfo.hide();
						}
					}
				});
				totalPriceSpan.text(kunsthalle.round(totalPrice) + ' €');

				// refresh slider size
				$('.swiper-container.detail').data('swiper').onResize();
			})
			.on('change', '[name="member_chk"]', function () {
				var memberInput = $(this);
				// get the number input wich depends on the checkbox
				var numberInput = memberInput.parents('form').find('[name="member_no"]');
				numberInput.prop('disabled', !memberInput.is(':checked'));

				if (memberInput.is(':checked')) {
					numberInput.parents('.form-group').removeClass('disabled');
				} else {
					numberInput.parents('.form-group').addClass('disabled');
				}
			});

			showEvent = function(indx, slideNo) {
				if(!isNaN(indx) && !isNaN(slideNo)) {
					console.log("showEvent(" +indx+", "+slideNo+") called");
			        swiperDetails.slideTo(slideNo); 
			        swiperControl.slideTo(slideNo); 

		            var scrollPos = $('.event_no_'+indx).offset().top - 68;
		            $('html, body').animate({ scrollTop: scrollPos }, 1000);
		            $('#icon_down_'+indx).trigger('click');
		            console.log("Finished auto scroll to event:- "+ indx);
				}
			}

			// Handle auto scroll if event index found in URL
		    var url = document.URL;
		    console.log(url);
		    if(url.indexOf('return_url=') < 0 && url.indexOf('?') < 0) {		    	
		    	if(url.indexOf('/err=1') > -1) {
		    		url = url.substr(0, url.indexOf('/err='));
		    		showErr = true;
		    	} else {
		    		showErr = false;
		    	}
		    	url = url.replace('#', '');
			    var arr = url.split('/');
			    console.log(arr);
			    if(arr.length > 4) {
				    var indx_str = arr[arr.length-1];
				    console.log(indx_str);
				    var indx = indx_str;
				    slideNo = 1;
				    if(indx_str.indexOf('_') > -1) {
				        var indx_ar = indx_str.split('_');
				        indx = indx_ar[0];
				        slideNo = indx_ar[1];
				    }
				    console.log(indx +"\n"+ slideNo);
				    if($('.event_no_'+indx).length) {
				        evtIndex = indx;
				        console.log("Detecting evt index:- "+ evtIndex);
				        swiperDetails.slideTo(slideNo);
				        swiperControl.slideTo(slideNo); 

			            var scrollPos = $('.event_no_'+evtIndex).offset().top - 162;
			            $('html, body').animate({ scrollTop: scrollPos }, 1000);
			            $('#icon_down_'+evtIndex).trigger('click');
			            $('.opener_'+indx).addClass('opener-open');
			            console.log("Finished auto scroll to event: "+ evtIndex);
			            console.log('submit found? '+ ($('#submit_'+indx).length));
				    }    
			    }
		    }
			// Custom scroll
			var url = document.URL;
			if(url.indexOf('?section=') > -1) {
				console.log('autoScrollToLink().. '+ url);
				var goToSection = url.substr(url.indexOf('?section=')+9, url.length);
				console.log("Section: "+ goToSection);
				if(goToSection.length > 0) {
					var anchors = $('#content .anchor');
					console.log("Anchors");
					anchors.each(function() {
						var anchor = $(this);
						var anchorText = anchor.data('anchortext') ? anchor.data('anchortext') : '';
						if(anchorText == goToSection) {
							var item = $('<li><span />' + goToSection + '</li>');
							kunsthalle.scrollTo(anchor.offset().top - $('#header').height());
						}
					});
				}				
			}

		// add validation to register forms
		// add iban as rule, because input type iban does not exist
		$('#calendar form').each(function() {
			$(this).validate({
				normalizer: function( value ) {
					return $.trim( value );
				},
				rules: {
					iban: {
						iban: true
					},
					bic: {
						bic: true
					}
				}
			});
		});
		kunsthalle.initCalendarDateSelector();
	},
	initCalendarDateSelector: function() {
		var tableSelectorsWrapper = $('.date-selector-wrapper');
		var tableSelectors = $('table.date-selector');

		tableSelectorsWrapper
			.on('show.bs.collapse', function() {
				// close type filter (only once at a time)
				kunsthalle.closeFilter();
				$('.swiper-container.control').css('background-color', '#FFFFFF');
				$('#filter').css('border-color', 'rgb(181, 213, 221)');
			})
			.on('hide.bs.collapse', function() {
				$('.swiper-container.control').css('background-color', 'transparent');
				$('#filter').css('border-color', '#FFFFFF');
			});

		$('table.date-selector')
			.on('click', 'div:not(.date-selector-nodates)', function() {
				var dateString = $(this).data('date');
				var selectedDate = new Date(dateString);
				var selectedDateString = selectedDate.toLocaleString('de-DE', {
					day: 'numeric',
					month: 'long'
				})
				$('table.date-selector .date-selector-currentdate').removeClass('date-selector-currentdate');
				$('[data-date="' + dateString + '"]').addClass('date-selector-currentdate');
				$('#calendar #filter .filter-dateselector-name')
					.text(selectedDateString)
					.data('date', dateString);

				// start filtering
				kunsthalle.startFiltering();
			});
		$('#calendar')
			.on('click', '.open-filter-dateselector', function(e) {
				e.preventDefault();
				e.stopPropagation();
				tableSelectorsWrapper.collapse('toggle');
			})
			.on('click', '.close-filter-dateselector', function(e) {
				e.preventDefault();
				e.stopPropagation();
				tableSelectorsWrapper.collapse('hide');
			});
	},
	initOpener: function () {
		$('.opener')
			.on('click', '.opener-open-link', function () {
				$(this).parent('.opener').addClass('opener-open');
			})
			.on('click', '.opener-close-link', function () {
				$(this).parent('.opener').removeClass('opener-open');
			});
	},
	initGallery: function () {
		$('.ce-gallery')
			.each(function () {
				var swiper = new Swiper($('.swiper-container', this), {
					pagination: '.swiper-pagination',
					paginationClickable: true,
					nextButton: '.next',
					prevButton: '.prev',
					// replaced with css align-items: flex-end
					/*onSlideChangeStart: function(swiper) {
						var slideHeight = $(swiper.slides[swiper.realIndex]).height();
						var figure = $('figure', swiper.slides[swiper.realIndex]);
						if(figure.css('margin-top') === '0px') {
							figure.css('margin-top', (slideHeight-figure.height()) + 'px');
						}
					}*/
				});
			});
	},
	initDownload: function () {

		function resizeSwiperContainer(ceSwiper, numberElements, windowWidth) {
			$(ceSwiper).removeClass('no-swiper');
			// set .swiper-container width so the items are centered 
			if(windowWidth <= 480 && numberElements <= 2) {
				$(ceSwiper).addClass('no-swiper');
				var temp = (numberElements / 2) * 100;
				$('.swiper-container', ceSwiper).width(temp + '%');
			} else if(windowWidth > 480 && windowWidth <= 768 && numberElements <= 3) {
				$(ceSwiper).addClass('no-swiper');
				var temp = (numberElements / 3) * 100;
				$('.swiper-container', ceSwiper).width(temp + '%');
			} else if(windowWidth > 768 && windowWidth <= 1024 && numberElements <= 4) {
				$(ceSwiper).addClass('no-swiper');
				var temp = (numberElements / 4) * 100;
				$('.swiper-container', ceSwiper).width(temp + '%');
			} else if(windowWidth > 1024 && windowWidth <= 1280 && numberElements <= 6) {
				$(ceSwiper).addClass('no-swiper');
				var temp = (numberElements / 6) * 100;
				$('.swiper-container', ceSwiper).width(temp + '%');
			} else if(windowWidth > 1280 && numberElements <= 8) {
				$(ceSwiper).addClass('no-swiper');
				var temp = (numberElements / 8) * 100;
				$('.swiper-container', ceSwiper).width(temp + '%');
			}
		}
		
		$('.ce-download')
			.each(function () {
				var currentCe = this;
				var numberElements = $('.swiper-container .ce-download-element', currentCe).length;
				
				var swiperOptions = {
					pagination: '.swiper-pagination',
					paginationClickable: true,
					nextButton: '.next',
					prevButton: '.prev',
					slidesPerView: numberElements < 8 ? numberElements : 8,
					slidesPerGroup: numberElements < 8 ? numberElements : 8,
					threshold: 10,
					breakpoints: {
						// when window width is <= 480px
						480: {
							slidesPerView: numberElements < 2 ? numberElements : 2,
							slidesPerGroup: numberElements < 2 ? numberElements : 2
						},
						// when window width is <= 768px
						768: {
							slidesPerView: numberElements < 3 ? numberElements : 3,
							slidesPerGroup: numberElements < 3 ? numberElements : 3
						},
						// when window width is <= 1024px
						1024: {
							slidesPerView: numberElements < 4 ? numberElements : 4,
							slidesPerGroup: numberElements < 4 ? numberElements : 4
						},
						// when window width is <= 1280px
						1280: {
							slidesPerView: numberElements < 6 ? numberElements : 6,
							slidesPerGroup: numberElements < 6 ? numberElements : 6
						}
					}
				};

				resizeSwiperContainer(currentCe, numberElements, $(window).width());

				var swiper = new Swiper($('.swiper-container', currentCe), swiperOptions);

				$(window).resize(function() {
					var windowWidth = $(window).width();
					resizeSwiperContainer(currentCe, numberElements, windowWidth);
					swiper.onResize();
				});
			})
			.on('click', '.ce-download-mark', function () { // event mark all clicked
				$('.ce-download-element:not(.ce-download-element-active)', $(this).parents('.ce-download')).trigger('click');
			})
			.on('click', '.ce-download-load', function () { // event download clicked
				var ce = $(this).parents('.ce-download');
				var items = [];
				$('.ce-download-element-active', ce).map(function () {
					items.push($(this).data('name') +'_'+ $(this).data('protected'));
				});
				if(items.length < 1) {
					return;
				}
				if(ce.hasClass('ce-download-termsofuse')) {
					var modal = kunsthalle.showModal('termsofuse');
					// set hidden field so the requested files are available in the terms of use form
					$('[name="termsofuse_files"]', modal).val(items.join(', '));
				    var showDLDialog = false;
				    var ar = [];
				    is_protected = 0;
				    for(var i in items) {
				        ar = items[i].split('_');				        
				        if(ar[1] == '1') { showDLDialog = true; is_protected = 1; break; }
				    }
					if(showDLDialog) {
						var modal = kunsthalle.showModal('termsofuse');
						// set hidden field so the requested files are available in the terms of use form
					} else {
						console.log('Not secured dl');
						handleDownload();
					}
				} else {
					// items contains the names of all marked items
					console.log('download: ' + items.join(', '));
				}
			})
			.on('click', '.ce-download-element', function () { // event single item clicked toggle check state
				$(this).toggleClass('ce-download-element-active');
			});
	},
	initRouter: function () {
		var routerIsotope = null;

		$('.ce-router .grid').imagesLoaded( function() {
			routerIsotope = $('.ce-router .grid').isotope({
				layoutMode: 'packery',
				itemSelector: '.grid-item',
				percentPosition: true,
				packery: {
					columnWidth: '.grid-sizer'
				}
			});
			$('.ce-router .grid header').css('opacity', 1);
		});

		$('.ce-router .filter')
			.on('click', '.open-filter', function (e) {
				e.preventDefault();
				e.stopPropagation();
				$(this).parent('div').next('div').collapse('toggle');
			})
			.on('click', '.close-filter', function (e) {
				e.preventDefault();
				e.stopPropagation();
				$(this).parents('.filter').children('.menu').collapse('hide');
			})
			.on('click', 'ul a', function (e) {
				e.preventDefault();
				// set selected filter as checked
				$(this)
					.prev('span')
					.addClass('icon-check')
					.end()
					.parent('li')
					.siblings()
					.children('span')
					.removeClass('icon-check');

				var filterContainer = $(this).parents('.filter');

				// set new filter name
				$('.filter-name', filterContainer).text($(this).text());

				var filterValue = $(this).data('filter');
				routerIsotope.isotope({
					filter: filterValue
				});

				// close filter dropdown
				filterContainer.children('.menu').collapse('hide');
			});
	},
	initAnchor: function() {
		var anchorList = $('.ce-anchors ul');
		var anchors = $('#content .anchor');
		
		anchors.each(function() {
			var anchor = $(this);
			var anchorText = anchor.data('anchortext') ? anchor.data('anchortext') : '';
			if(anchorText.length > 0) {
				var item = $('<li><span />' + anchorText + '</li>')
				item.on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					kunsthalle.scrollTo(anchor.offset().top - $('#header').height());
				});
				anchorList.append(item);
			}
		});
	},
	initHomeSwiper: function() {
		var swiper = new Swiper('#content>header>.swiper-container', {
			nextButton: '.next',
			prevButton: '.prev',
			autoplay: 12000,
			loop: true
		});
	},
	initMembership: function() {
		$('.ce-membership form')
			.on('change', '[name="membership"]', function() {
				switch($(this).val()) {
					case 'couple':
						$('.membership-couple').show();
						$('.membership-family').hide();
						break;
					case 'family':
						$('.membership-family').show();
						$('.membership-couple').hide();
						break;
					default:
						$('.membership-couple').hide();
						$('.membership-family').hide();
				}
			})
			.on('change', '[name="payment"]', function() {
				if($(this).val() === 'debit') {
					$('#iban')
						.prop('disabled', false)
						.parents('.form-group')
							.removeClass('disabled');
					$('#bic')
						.prop('disabled', false)
						.parents('.form-group')
							.removeClass('disabled');		
					$('#depositor')
						.prop('disabled', false)
						.parents('.form-group')
							.removeClass('disabled');
					$('#bank')
						.prop('disabled', false)
						.parents('.form-group')
							.removeClass('disabled');
					$('.membership-payment-debit').show();
				} else {
					$('#iban')
						.prop('disabled', true)
						.parents('.form-group')
							.addClass('disabled');
					$('#bic')
						.prop('disabled', true)
						.parents('.form-group')
							.addClass('disabled');		
					$('#depositor')
						.prop('disabled', true)
						.parents('.form-group')
							.addClass('disabled');
					$('#bank')
						.prop('disabled', true)
						.parents('.form-group')
							.addClass('disabled');
					$('.membership-payment-debit').hide();
				}
			})
			.on('click', '.membership-family-child-add', function(e) {
				e.preventDefault();
				var newChild = $('.membership-family-children .membership-family-child-dummy').clone();
				newChild
					.removeClass('membership-family-child-dummy')
					.addClass('membership-family-child');
				$('.membership-family-children').append(newChild);
			})
			.on('click', '.membership-family-child-remove', function(e) {
				e.preventDefault();
				if($('.membership-family-children .membership-family-child').length > 1) {
					$('.membership-family-children .membership-family-child:last').remove();
				}
			})
			.validate({
				normalizer: function( value ) {
					return $.trim( value );
				},
				rules: {
					iban: {
						iban: true
					},
					bic: {
						bic: true
					},
					birthday: {
						date: true
					},
					partner_birthday: {
						date: true
					}
				}
			});
	},
	initTermsofuse: function() {
		$('.ce-termsofuse form')
			.validate({
				normalizer: function( value ) {
					return $.trim( value );
				}
			});
		$('.ce-termsofuse form').on('submit', function(e) {
				e.preventDefault();
				e.stopPropagation();
		});
	},
	initEmailrequest: function() {
		$('.ce-emailrequest form')
			.validate({
				normalizer: function( value ) {
					return $.trim( value );
				}
			});
	},
	initSubmenu: function () {
		$('.ce-submenu')
			.on('click', '.opener-close-link, .opener-open-link, .ce-submenu-title, .close-link', function(e) {
				e.preventDefault();
				$(this).parents('.ce-submenu').children('ul').slideToggle();
			});
	},
	initSidemenu: function () {
		$('#sidemenu')
			.on('click', '.toggler', function(e) {
				e.preventDefault();
				$(this).children('span').toggleClass('icon-toggle').toggleClass('icon-close');
				$(this).parents('#sidemenu').toggleClass('open');
				
				var ul = $(this).prev('.toggler-wrapper').children('ul');
				ul.stop();
				if(window.innerHeight > window.innerWidth || window.innerWidth > 991) {
					ul.slideToggle();
				} else {
					ul.toggle('slide', { direction: 'right' });
				}
			})
			.on('click', '.totop', function(e) {
				e.preventDefault();
				kunsthalle.scrollTo(0);
			});
		$(window)
			.scroll(function() {
				var sidemenu = $('#sidemenu');
				if ($(window).scrollTop() > 100 && !sidemenu.is(':visible')) {
					sidemenu.show('slide', { direction: 'right' });
				} else if($(window).scrollTop() <= 100 && sidemenu.is(':visible')) {
					sidemenu.hide('slide', { direction: 'right' });
				}
			})
			.trigger('scroll');
	},
	scrollTo: function(position) {
		$('html, body').animate({
			scrollTop: position
		}, 500);
	},
	round: function (x) {
		var k = (Math.round(x * 100) / 100).toString();
		k += (k.indexOf('.') == -1) ? '.00' : '00';
		var p = k.indexOf('.');
		return k.substring(0, p) + ',' + k.substring(p + 1, p + 3);
	},
	showModal: function(id) {
		$('#modal_' + id)
			.addClass('modal-active')
			.fadeIn();
		$('body').addClass('noscroll');
		return $('#modal_' + id);
	},
	hideModal: function() {
		$('body').removeClass('noscroll');
		$('#modals .modal-active')
			.fadeOut()
			.removeClass('modal-active');
	}
};

$.material.init();

/* validation configuration */
$.validator.setDefaults({
	focusInvalid: false,
	errorElement: 'span',
	errorClass: 'help-block',
	highlight: function (element, errorClass, validClass) {
		$(element).closest('.form-group').addClass('has-error');
	},
	unhighlight: function (element, errorClass, validClass) {
		$(element).closest('.form-group').removeClass('has-error');
	},
	errorPlacement: function (error, element) {
		if (element.parent('.input-group').length || element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
			error.insertAfter(element.parent());
		} else {
			error.insertAfter(element);
		}
	},
	normalizer: function( value ) {
		return $.trim( value );
	},
	invalidHandler: function(event, validator) {
		var input = $( validator.findLastActive() || validator.errorList.length && validator.errorList[0].element || [] );
		kunsthalle.scrollTo(input.offset().top - 200);
		input.focus();
	}
});

$.extend( $.validator.methods, {
	date: function( value, element ) {
		return this.optional( element ) || /^\d\d?\.\d\d?\.\d\d\d?\d?$/.test( value );
	},
	number: function( value, element ) {
		return this.optional( element ) || /^-?(?:\d+|\d{1,3}(?:\.\d{3})+)(?:,\d+)?$/.test( value );
	},
	nospace: function( value, element ) {
		return this.optional( element ) || /^\d\d?\.\d\d?\.\d\d\d?\d?$/.test( value );
	}
});

$.validator.addMethod("participant", function(value, element) {
	participant_count = getParticipantCount();
	console.log("\nParticipant count: "+participant_count);
	if(participant_count < 1) {
		$('.prt_err').show();
	}
	return (participant_count > 0);
});