// Turn radios into btn-group
jQuery(function($) {
	/**
	 * Enables bootstrap popover
	 */
	$('a.hasPopover.google, span.hasPopover.google').popover({trigger:'hover', placement:'bottom', html:1});
	$('label.hasPopover, button.hasPopover, div.hasPopover, span.hasPopover').popover({trigger:'hover', placement:'top', html:1});
	$('span.hasRightPopover').popover({trigger:'hover', placement:'right', html:1});
	
	/**
	 * Enables bootstrap tooltip
	 */
	$('label.hasTooltip, img.hasTooltip, a.hasTooltip, span.hasTooltip, a.hasTip').tooltip({trigger:'hover', placement:'top'});
	
	/**
	 * Remove empty ordering spans
	 */
	$('td.order > span').filter(function(){
		var hasChild = !$('a', this).length;
		return hasChild;
	}).remove();
	// Recover the legacy save order button in async way on the next cycle
	setTimeout(function(){
		$('a.saveorder').removeAttr('onclick').removeAttr('style');
	}, 1);
	
	/**
	 * Accordion panels local storage memoize and set open
	 */
	var defaultAccordionObject = {'accordion_cpanel':'seo_stats',
								  'accordion_datasource_pluginimport':'datasource_pluginimport',
								  'accordion_datasource_details':'datasource_details',
								  'accordion_datasource_excludecats':'datasource_excludecats',
								  'accordion_datasource_excludearticles':'datasource_excludearticles',
								  'accordion_datasource_excludemenu':'datasource_excludemenu',
								  'accordion_datasource_menupriorities':'datasource_menupriorities',
								  'accordion_datasource_catspriorities':'datasource_catspriorities',
								  'accordion_datasource_parameters':'datasource_parameters',
								  'accordion_datasource_sqlquery':'datasource_sqlquery',
							      'accordion_datasource_xmlparameters':'datasource_xmlparameters',
							      'accordion_datasource_sqlquery_maintable':'datasource_sqlquery_maintable',
							      'accordion_datasource_sqlquery_jointable1':'datasource_sqlquery_jointable1',
							      'accordion_datasource_sqlquery_jointable2':'datasource_sqlquery_jointable2',
							      'accordion_datasource_sqlquery_jointable3':'datasource_sqlquery_jointable3',
							      'accordion_datasource_sqlquery_autogenerated':'datasource_sqlquery_autogenerated',
							      'accordion_datasource_sqlquery_querystring':'datasource_sqlquery_querystring',
							      'accordion_pingomatic_details':'pingomatic_details',
							      'accordion_pingomatic_services':'pingomatic_services',
							      'accordion_datasets_details':'datasets_details',
							      'accordion_datasets_datasources':'datasets_datasources',
							      'accordion_datasource_plugin_parameters':'datasource_plugin_parameters',
							      'accordion_datasource_raw_links':'datasource_raw_links',
							      'jmap_googlegraph_accordion':'jmap_googlestats_graph',
							      'jmap_googlegeo_accordion':'jmap_googlestats_geo',
							      'jmap_googletraffic_accordion':'jmap_googlestats_traffic',
							      'jmap_googlereferrer_accordion':'jmap_googlestats_referrers',
							      'jmap_googlesearches_accordion':'jmap_googlestats_searches',
							      'jmap_googlesystems_accordion':'jmap_googlestats_systems',
							      'jmap_googlepages_accordion':'jmap_googlestats_pages',
							      'jmap_googlestats_webmasters_sitemaps_accordion':'jmap_googlestats_webmasters_sitemaps',
							      'jmap_google_search_console_accordion':'jmap_google_search_console',
							      'jmap_googleconsole_query_accordion':'jmap_google_query',
							      'jmap_googleconsole_pages_accordion':'jmap_google_pages',
							      'jmap_googleconsole_device_accordion':'jmap_google_device',
							      'jmap_googleconsole_country_accordion':'jmap_google_country',
							      'jmap_googleconsole_date_accordion':'jmap_google_date',
							      'jmap_google_pagespeed_summary_accordion':'jmap_google_pagespeed_summary',
							      'jmap_google_pagespeed_performance_accordion':'jmap_google_pagespeed_performance',
							      'jmap_google_pagespeed_assets_accordion':'jmap_google_pagespeed_assets',
							      'jmap_google_pagespeed_seo_accordion':'jmap_google_pagespeed_seo',
							      'jmap_google_pagespeed_overview_accordion':'jmap_google_pagespeed_overview'
								 };
	$('div.panel-group').on('shown.bs.collapse', function (event) {
		if(!$(event.target).hasClass('panel-collapse')) {
			return;
		}
		event.stopPropagation();
		
		// Trigger window resize to force graph resizing
		if(event.target.id == 'jmap_status' || event.target.id == 'seo_stats') {
			$(window).trigger('resize');
		}
		
		var localStorageAccordion = $.jStorage.get('accordionOpened', defaultAccordionObject);
		localStorageAccordion[this.id] = event.target.id;
		$.jStorage.set('accordionOpened', localStorageAccordion);
		
		// Scroll to accordion header if needed
		if(document.body.scrollHeight > window.innerHeight && $(this).attr('id') != 'accordion_cpanel') {
			$('html, body').animate({ scrollTop: $("#"+event.target.id).prev().offset().top - 180 }, 500);
		}
		// Add open state
		$(event.target).prev().addClass('opened');
	}).on('hide.bs.collapse', function (event) {
		if(!$(event.target).hasClass('panel-collapse')) {
			return;
		}
		event.stopPropagation();
		var localStorageAccordion = $.jStorage.get('accordionOpened', defaultAccordionObject);
		if(localStorageAccordion[this.id] == event.target.id) {
			delete localStorageAccordion[this.id];
			$.jStorage.set('accordionOpened', localStorageAccordion);
		}
		// Remove open state
		$(event.target).prev().removeClass('opened');
	});
	
	$.each($.jStorage.get('accordionOpened', defaultAccordionObject), function(namespace, element) {
		if($('#'+element, '#'+namespace).length) {
			$('#'+element, '#'+namespace).addClass('in').prev().addClass('opened');
		}
	});
	
	/**
	 * Tab panels local storage memoize and set open
	 */
	var defaultTabObject = {'tab_configuration':'preferences', 'permissions-sliders': 'permissions-1'};
	$('.nav.nav-tabs').on('shown.bs.tab', function (event) {
		var localStorageTab = $.jStorage.get('tabOpened', defaultTabObject);
		var assignedID = this.id ? this.id : $(this).parent().attr('id');
		var assignedValue = $(event.target).data('element') ? $(event.target).data('element') : $(event.target).attr('href').substr(1)
		localStorageTab[assignedID] = assignedValue;
		$.jStorage.set('tabOpened', localStorageTab);
	});
	
	$.each($.jStorage.get('tabOpened', defaultTabObject), function(namespace, element) {
		$('a[data-element='+element+']', '#'+namespace).tab('show');
		$('a[href=\\#'+element+']', '#'+namespace).tab('show');
	});
	
	// Check for a specific tab trigger using url hash
	var licensePreferenceRequest = window.location.hash.substr(2);
	if(licensePreferenceRequest == 'licensepreferences') {
		$('a[data-element=preferences]').tab('show');
		$('#params_registration_email-lbl').css('color', 'red');
		$('#params_registration_email').css('border', '2px solid red');
	}
	if(licensePreferenceRequest == 'google_analytics_ga4property') {
		$('a[data-element=google_analytics]').tab('show');
		$('#params_ga_property_id-lbl').css('color', 'red');
		$('#params_ga_property_id').css('border', '2px solid red');
	}
	
	/**
	 *  Hide state select on phone
	 */
	$('#filter_state, #filter_type').addClass('hidden-phone');
	
	/**
	 * Manage config template for html sitemap
	 */
	$('<div/>').insertAfter('#params_sitemap_html_template').css('background-image', 'url(components/com_jmap/images/templates.png)').addClass('sitemap_template');
	$('#params_sitemap_html_template').css({'width':'150px', 'float':'left', 'transition':'none'}).on('change', function(jqEvent){
		var nextDivPlaceholder = $(this).next('div');
		var indexSelected = $('#params_sitemap_html_template option:selected').index() || 0;
		var backgroundDisplacement = -(indexSelected * 181);
		nextDivPlaceholder.css('background-position', '0 ' + backgroundDisplacement + 'px');
	}).trigger('change');
	
	// Manage the hide/show of subcontrols for mindmap templating styles
	var sitemapTemplate = $('select[name=params\\[sitemap_html_template\\]]').val();
	if(sitemapTemplate != 'mindmap') {
		$('*.mindmap_styles').hide();
	}
	$('select[name=params\\[sitemap_html_template\\]]').on('change', function(){
		if($(this).val() == 'mindmap') {
			$('*.mindmap_styles').slideDown();
		} else {
			$('*.mindmap_styles').slideUp();
		}
	});
	
	// Manage the hide/show of subcontrols for custom images tags
	var customTagsValue = $('input[name=params\\[custom_images_processor\\]]:checked').val();
	if(customTagsValue == 0) {
		$('*.customtags_styles').hide();
	}
	$('input[name=params\\[custom_images_processor\\]]').on('click', function(){
		if($(this).val() == 1) {
			$('*.customtags_styles').slideDown();
		} else {
			$('*.customtags_styles').slideUp();
		}
	});

	// Manage the hide/show of subcontrols for gojs templating styles
	var sitemapTemplate = $('select[name=params\\[sitemap_html_template\\]]').val();
	if(sitemapTemplate != 'gojs') {
		$('*.gojs_styles').hide();
	}
	$('select[name=params\\[sitemap_html_template\\]]').on('change', function(){
		if($(this).val() == 'gojs') {
			$('*.gojs_styles').slideDown();
		} else {
			$('*.gojs_styles').slideUp();
		}
	});
	
	// Manage the hide/show of subcontrols for rich snippets
	var searchboxTypeValue = $('input[name=params\\[searchbox_type\\]]:checked').val();
	if(searchboxTypeValue != 'custom') {
		$('*.searchbox_styles').hide();
	}
	$('input[name=params\\[searchbox_type\\]]').on('click', function(){
		if($(this).val() == 'custom') {
			$('*.searchbox_styles').slideDown();
		} else {
			$('*.searchbox_styles').slideUp();
		}
	});
	
	// Manage the hide/show of subcontrols for Analytics API
	var analyticsWebServiceValue = $('select[name=params\\[analytics_service\\]]').val();
	if(analyticsWebServiceValue != 'google') {
		$('*.analyticsapi_styles').hide();
		$('*.analytics_api').hide();
	}
	$('select[name=params\\[analytics_service\\]]').on('change', function(){
		if($(this).val() == 'google') {
			$('*.analyticsapi_styles').slideDown();
			if($('select[name=params\\[analytics_api\\]]').val() == 'data') {
				$('*.analytics_api').slideDown();
			}
			if($('select[name=params\\[analytics_api\\]]').val() != 'data') {
				$('*.no_analytics_api').slideDown();
			} else {
				$('*.no_analytics_api').slideUp();
			}
		} else {
			$('*.analyticsapi_styles').slideUp();
			$('*.analytics_api').slideUp();
			$('*.no_analytics_api').slideDown();
		}
	});
	
	// Manage the hide/show of subcontrols for Analytics API Property ID
	var analyticsApiValue = $('select[name=params\\[analytics_api\\]]').val();
	if(analyticsApiValue != 'data') {
		$('*.analytics_api').hide();
		$('*.no_analytics_api').show();
	} else {
		if(analyticsWebServiceValue == 'google') {
			$('*.no_analytics_api').hide();
		}
	}
	$('select[name=params\\[analytics_api\\]]').on('change', function(){
		if($(this).val() == 'data') {
			$('*.analytics_api').slideDown();
			$('*.no_analytics_api').slideUp();
		} else {
			$('*.analytics_api').slideUp();
			$('*.no_analytics_api').slideDown();
		}
	});
	
	// Manage the hide/show of subcontrols for Google Indexing API
	var searchboxTypeValue = $('input[name=params\\[enable_google_indexing_api\\]]:checked').val();
	if(searchboxTypeValue == 0) {
		$('*.googleindexing').hide();
	}
	$('input[name=params\\[enable_google_indexing_api\\]]').on('click', function(){
		var inputValue = parseInt($(this).val());
		if(inputValue) {
			$('*.googleindexing').slideDown();
		} else {
			$('*.googleindexing').slideUp();
		}
	});
	
	 // Create color picker controls
    $("input[id*=_color], input[id*=color_]").after('<div class="colorpicker_preview"><div></div></div>')
    var loadColor = function(elem, colorHex) {
    	// Set input HEX color value
    	$(elem).val(colorHex);
    	$(elem).ColorPickerSetColor(colorHex);
    	
    	// Set background color of preview box
    	var nextElPreview = $(elem).next('div.colorpicker_preview');
		$('div', nextElPreview).css('background-color', colorHex);
    }
    
    // Check if ColorPicker plugin is loaded
    if($.fn.ColorPicker) {
    	$("input[id*=_color], input[id*=color_]").ColorPicker({
    		onSubmit : function(hsb, hex, rgb, el){
    			loadColor(el, '#' + hex);
    		}
    	});
    	$("input[id*=_color], input[id*=color_]").each(function(k, elem){
    		var colorValue = $(elem).val();
    		loadColor(elem, colorValue);
    	});
    }
    
    // Show generic waiter
	var showGenericWaiter = function(mainContainerID) {
		// Get div popover container width to center waiter
		$('body').prepend('<img/>').children('img').attr('src', jmap_baseURI + 'administrator/components/com_jmap/images/loading.gif').css({
			'position' : 'absolute',
			'left' : '50%',
			'top' : '50%',
			'margin-left' : '-64px',
			'width' : '128px',
			'z-index' : '99999'
		});
	};
	$('#ga-dash button, *.waiter').on('click', function(jqEvent){
		showGenericWaiter();
	});
	
	// Manage generic resetter buttons for multiple fields
	$('button[data-reset]').on('click', function(jqEvent){
		jqEvent.preventDefault();
		var elementsClassToReset = $(this).data('reset');
		$('*.' + elementsClassToReset).each(function(index, element){
			$(element).val('');
		});
		$('#adminForm').submit();
	});
	
	// Flag the changed domain for SEO stats and GTester
	$('#params_seostats_custom_link').on('change', function(jqEvent){
		$(this).attr('data-changed', 1);
	});
	$('label[for^=params_seostats_site_query]').on('click', function(jqEvent){
		$('#params_seostats_custom_link').attr('data-changed', 1);
	});
	$('#params_seostats_service').on('change', function(jqEvent){
		$('#params_seostats_custom_link').attr('data-changed', 1);
	});
	
	/**
	 * Prevent default scrolling hover main accordion body and scroll programmatically the document
	 */
	$('div.panel-body.panel-overflow').on('wheel', function(jqEvent){
		if (jqEvent.originalEvent && jqEvent.originalEvent.wheelDelta) {
			if (jqEvent.originalEvent.wheelDelta) jqEvent.delta = jqEvent.originalEvent.wheelDelta;
		
			var newBodyScroll = $(document).scrollTop() - jqEvent.delta;
			$(document).scrollTop(newBodyScroll);
			jqEvent.preventDefault();
			return false;
		}
	});
	
	// Add the button to run the crawler test
	$('#params_regex_images_crawler').addClass('pull-left').after('<label id="crawler_test" class="label label-info spacer"><span class="icon icon-cogs"></span> Crawler test</label>');
	$('#crawler_test').on('click', function(jqEvent){
		window.open('index.php?option=com_jmap&task=config.checkEntityCrawler&tmpl=component', 'crawler_test', 'width=1024,height=768');
	});
	
	// Reset Google Authentication data
	var authCodeField = $('#params_google_indexing_authcode');
	$('#google_authentication_reset').on('click', function(jqEvent){
		authCodeField.val('');
		$('#params_google_indexing_authtoken').val('');
		$('#toolbar-save button').trigger('click');
	});
	if(authCodeField.val() != '' && $('div.googleindexing span.label').hasClass('label-success')) {
		authCodeField.attr('readonly', true);
	}
	
	// Override permissions tab retrieve ACL
	$('#permissions select[data-chosen]').removeAttr('onchange').on('change', function(jqEvent) {
		// set the icon while storing the values
		var icon = document.getElementById('icon_' + this.id);
		icon.removeAttribute('class');
		icon.setAttribute('style', 'background: url(../media/system/images/modal/spinner.gif); display: inline-block; width: 16px; height: 16px');

		//get values and prepare GET-Parameter
		var asset = 'not';
		var component = 'com_jmap';
		var extension = 'com_jmap';
		var option = 'com_jmap';
		var view = null;
		var title = 'com_jmap';
		var value = this.value;

		var id = this.id.replace('params_rules_', '');
		var lastUnderscoreIndex = id.lastIndexOf('_');

		var permission_data = {
			comp : component,
			action : id.substring(0, lastUnderscoreIndex),
			rule : id.substring(lastUnderscoreIndex + 1),
			value : value,
			title : title
		};

		// Remove js messages, if they exist.
		Joomla.removeMessages();

		// doing ajax request
		$.ajax({
			method : "POST",
			url : document.getElementById('permissions-sliders').getAttribute('data-ajaxuri'),
			data : permission_data,
			datatype : 'json'
		}).fail(function(jqXHR, textStatus, error) {
			// Remove the spinning icon.
			icon.removeAttribute('style');

			Joomla.renderMessages(Joomla.ajaxErrorsMessages(jqXHR, textStatus, error));

			window.scrollTo(0, 0);

			icon.setAttribute('class', 'icon-cancel');
		}).done(function(response) {
			// Remove the spinning icon.
			icon.removeAttribute('style');

			if (response.data) {
				// Check if everything is OK
				if (response.data.result == true) {
					icon.setAttribute('class', 'icon-save');

					$(jqEvent.target).parents().next("td").find("span").removeClass().addClass(response['data']['class']).html(response.data.text);
				}
			}

			// Render messages, if any. There are only message in case of errors.
			if (typeof response.messages == 'object' && response.messages !== null) {
				Joomla.renderMessages(response.messages);

				if (response.data && response.data.result == true) {
					icon.setAttribute('class', 'icon-save');
				} else {
					icon.setAttribute('class', 'icon-cancel');
				}

				window.scrollTo(0, 0);
			}
		});
	});
});