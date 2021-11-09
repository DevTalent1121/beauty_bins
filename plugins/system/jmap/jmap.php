<?php
/**
 * @author Joomla! Extensions Store
 * @package JMAP::plugins::system
 * @copyright (C) 2021 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.plugin.plugin' );

/**
 * Observer class notified on events
 *
 * @author Joomla! Extensions Store
 * @package JMAP::plugins::system
 * @since 2.1
 */
class plgSystemJMap extends JPlugin {
	/**
	 * Joomla config object
	 *
	 * @access private
	 * @var Object
	 */
	private $joomlaConfig;
	
	/**
	 * JSitemap config object
	 *
	 * @access private
	 * @var Object
	 */
	private $jmapConfig;
	
	/**
	 * JMap calculate URI
	 *
	 * @access private
	 * @var String
	 */
	private $jmapUri;
	
	/**
	 * Render module triggering
	 *
	 * @access private
	 * @var bool
	 */
	private static $renderModuleTrigger;
	
	/**
	 * Process content plugins
	 * 
	 * @access private
	 * @param string $custom404Text
	 * @param Object &$cParams
	 * @return string
	 */
	private function processContentPlugins($custom404Text, &$cParams) {
		// Process only if html mode is enabled and process plugins param is enabled
		if($cParams->get('custom_404_page_mode', 'html') == 'html' && $cParams->get('custom_404_process_content_plugins', 0)) {
			JPluginHelper::importPlugin('content');
			
			$dispatcher = JEventDispatcher::getInstance();
			
			$dummyParams = new JRegistry();
			$elm = new stdClass();
			$elm->text = $custom404Text;
			self::$renderModuleTrigger = true;
			
			$dispatcher->trigger('onContentPrepare', array ('com_content.article', &$elm, &$dummyParams, 0));
			$custom404Text = $elm->text;
		}
		
		// Always return input text, processed or not
		return $custom404Text;
	}
	
	/**
	 * Detect mobile requests
	 *
	 * @access private
	 * @return boolean
	 */
	private function isBotRequest() {
		$crawlers = array (
				'Google' => 'Google',
				'MSN' => 'msnbot',
				'Rambler' => 'Rambler',
				'Yahoo' => 'Yahoo',
				'Yandex' => 'Yandex',
				'AbachoBOT' => 'AbachoBOT',
				'accoona' => 'Accoona',
				'AcoiRobot' => 'AcoiRobot',
				'ASPSeek' => 'ASPSeek',
				'CrocCrawler' => 'CrocCrawler',
				'Dumbot' => 'Dumbot',
				'FAST-WebCrawler' => 'FAST-WebCrawler',
				'GeonaBot' => 'GeonaBot',
				'Gigabot' => 'Gigabot',
				'Lycos spider' => 'Lycos',
				'MSRBOT' => 'MSRBOT',
				'Altavista robot' => 'Scooter',
				'AltaVista robot' => 'Altavista',
				'ID-Search Bot' => 'IDBot',
				'eStyle Bot' => 'eStyle',
				'Scrubby robot' => 'Scrubby',
				'Facebook' => 'facebookexternalhit' 
		);
		// to get crawlers string used in function uncomment it
		// global $crawlers
		if (isset ( $_SERVER ['HTTP_USER_AGENT'] )) {
			$currentUserAgent = $_SERVER ['HTTP_USER_AGENT'];
			// it is better to save it in string than use implode every time
			$crawlers_agents = '/' . implode ( "|", $crawlers ) . '/';
			if (preg_match ( $crawlers_agents, $currentUserAgent, $matches )) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Handle the addition of the Google Analytics tracking code
	 *
	 * @access private
	 * @param Object $app
	 * @param Object $doc
	 * @param string $location
	 * @return boolean
	 */
	private function addGoogleAnalyticsTrackingCode($app, $doc, $location = 'body') {
		// Get component params
		$injectGaJs = $this->jmapConfig->get ( 'inject_gajs', 0 );
		$gajsCode = trim ( $this->jmapConfig->get ( 'gajs_code', '' ) );
		$gajsVersion = trim ( $this->jmapConfig->get ( 'inject_gajs_version', 'analytics' ) );
		$anonymizeIp = '';
		$anonymizeGtagIp = '';
		if( $this->jmapConfig->get ( 'gajs_anonymize', 0) ) {
			$anonymizeIp = "ga('set', 'anonymizeIp', true);";
			$anonymizeGtagIp = ", { 'anonymize_ip': true }";
		}
		
		if ($gajsVersion == 'analytics') {
			$script = <<<JS
			<!-- Google Analytics -->
			<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', '$gajsCode', 'auto');
			ga('send', 'pageview');
			$anonymizeIp
			</script>
			<!-- End Google Analytics -->
JS;
		} elseif ($gajsVersion == 'gtag') {
			$script = <<<JS2
			<!-- Global Site Tag (gtag.js) - Google Analytics -->
			<script async src="https://www.googletagmanager.com/gtag/js?id=$gajsCode"></script>
			<script>
			  window.dataLayer = window.dataLayer || [];
			  function gtag(){dataLayer.push(arguments);}
			  gtag('js', new Date());
			  gtag('config', '$gajsCode' $anonymizeGtagIp);
			</script>
JS2;
		}

		// Check if the tracking code must be injected, manipulate output JResponse
		if ($injectGaJs && $gajsCode) {
			if ($location == 'body') {
				$body = $app->getBody ();

				// Replace buffered main view contents at the body end
				$body = preg_replace ( '/<\/body>/i', $script . '</body>', $body, 1 );

				// Set the new JResponse contents
				$app->setBody ( $body );
			} elseif ($location == 'head') {
				if ($doc->getType () === 'html') {
					$doc->addCustomTag ( $script );
				}
			}
		}
	}
	
	/**
	 * Main dispatch method
	 *
	 * @access public
	 * @return boolean
	 */
	public function onAfterInitialise() {
		$app = JFactory::getApplication ();
		
		// Avoid operations if plugin is executed in backend
		if ( $app->getClientId ()) {
			return;
		}
		
		// Security safe 1 - If Joomla 3.4+ and JMAP internal link force always the lang url param using the cookie workaround
		if( $app->input->get ( 'option' ) == 'com_jmap' && version_compare(JVERSION, '3.4', '>=') && $this->jmapConfig->get('advanced_multilanguage', 0)) {
			$lang = $app->input->get('lang');
		
			$sefs = JLanguageHelper::getLanguages('sef');
			$lang_codes = JLanguageHelper::getLanguages('lang_code');
		
			if (isset($sefs[$lang])) {
				$lang_code = $sefs[$lang]->lang_code;
		
				// Create a cookie.
				$conf = JFactory::getConfig();
				$cookie_domain 	= $conf->get('config.cookie_domain', '');
				$cookie_path 	= $conf->get('config.cookie_path', '/');
				setcookie(JApplicationHelper::getHash('language'), $lang_code, 86400, $cookie_path, $cookie_domain);
				$app->input->cookie->set(JApplicationHelper::getHash('language'), $lang_code);
		
				// Set the request var.
				$app->input->set('language', $lang_code);

				// Check if remove default prefix is active and the default language is not the current one
				$defaultSiteLanguage = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
				$pluginLangFilter = JPluginHelper::getPlugin('system', 'languagefilter');
				$removeDefaultPrefix = @json_decode($pluginLangFilter->params)->remove_default_prefix;
				if($removeDefaultPrefix && $defaultSiteLanguage != $lang_code) {
					$uri = JUri::getInstance();
					$path = $uri->getPath();
					// Force the language SEF code in the path
					$path = str_replace('/index.php', '/' . $lang . '/index.php', $path);
					$uri->setPath($path);
				}
			}
		}
		
		// Detect if current request come from a bot user agent
		if ($this->isBotRequest () && $app->input->get ( 'option' ) == 'com_jmap') {
			$_SERVER ['REQUEST_METHOD'] = 'POST';
			
			// Set dummy nobot var
			$app->input->post->set ( 'nobotsef', true );
			$GLOBALS['_' . strtoupper('post')] ['nobotsef'] = true;
		}
	}
	
	/**
	 * Hook for the auto Pingomatic third party extensions that have not its own
	 * route helper and work with the universal JSitemap route helper framework
	 *
	 * @access public
	 * @return boolean
	 */
	public function onAfterRoute() {
		$this->app = JFactory::getApplication ();
		
		// Security safe 2 - If Joomla 3.4+ and JMAP internal link revert back the query string 'lang' param to the sef lang code 'en' instead of the iso lang code 'en-GB'
		$lang = $this->app->input->get('lang');
		if($lang && $this->app->input->get ( 'option' ) == 'com_jmap' && version_compare(JVERSION, '3.4', '>=') && strlen($lang) > 2) {
			$languageCode = $this->app->input->get('language');
			$lang_codes = JLanguageHelper::getLanguages('lang_code');
			if(isset($lang_codes[$languageCode])) {
				$sefLang = $lang_codes[$languageCode]->sef;
				$this->app->input->set('lang', $sefLang);
			}
		}
		
		// Avoid below operations if the plugin is executed in frontend
		if (! $this->app->getClientId ()) {
			return;
		}
		
		// Kill com_joomlaupdate informations about extensions missing updater info, leave only main one
		$doc = JFactory::getDocument();
		if(version_compare(JVERSION, '3.10', '>=') && version_compare(JVERSION, '4.0', '<') && !$this->app->get('jextstore_joomlaupdate_script') && $this->app->input->get('option') == 'com_joomlaupdate' && !$this->app->input->get('view') && !$this->app->input->get('task')) {
			$doc->addScriptDeclaration ("
				window.addEventListener('DOMContentLoaded', function(e) {
					if(document.querySelector('#pre-update-check')) {
						var jextensionsIntervalCount = 0;
						var jextensionsIntervalTimer = setInterval(function() {
						    [].slice.call(document.querySelectorAll('#compatibilitytype1 tbody tr td.exname, #preupdatecheckheadings tbody tr[id^=plg_] td:first-child')).forEach(function(td) {
						        let txt = td.innerText;
						        if (txt && txt.toLowerCase().match(/jsitemap|gdpr|responsivizer|jchatsocial|jrealtime|jspeed|jredirects|vsutility|visualstyles|visual\sstyles|instant\sfacebook\slogin|instantpaypal|screen\sreader|jspeed|jamp/i)) {
						            td.parentElement.style.display = 'none';
						            td.parentElement.classList.remove('error');
									td.parentElement.classList.add('jextcompatible');
						        }
						    });
							[].slice.call(document.querySelectorAll('#compatibilitytype2 tbody tr td.exname')).forEach(function(td) {
						        let txt = td.innerText;
						        if (txt && txt.toLowerCase().match(/jsitemap|gdpr|responsivizer|jchatsocial|jrealtime|jspeed|jredirects|vsutility|visualstyles|visual\sstyles|instant\sfacebook\slogin|instantpaypal|screen\sreader|jspeed|jamp/i)) {
									td.parentElement.classList.remove('error');
									td.parentElement.classList.add('jextcompatible');
						            let smallLabels = td.querySelectorAll(':scope span.label');
									[].slice.call(smallLabels).forEach(function(element) {
							            element.style.display = 'none';
							        });
						        }
						    });
							if (document.querySelectorAll('#compatibilitytype0 tbody tr').length == 0 &&
								document.querySelectorAll('#compatibilitytype1 tbody tr:not(.jextcompatible)').length == 0 &&
								document.querySelectorAll('#compatibilitytype2 tbody tr:not(.jextcompatible)').length == 0) {
						        [].slice.call(document.querySelectorAll('#preupdatecheckbox, #preupdateCheckCompleteProblems')).forEach(function(element) {
						            element.style.display = 'none';
						        });
								if(document.querySelector('#noncoreplugins')) {
									document.querySelector('#noncoreplugins').checked = true;
								}
								if(document.querySelector('button.submitupdate')) {
							        document.querySelector('button.submitupdate').disabled = false;
							        document.querySelector('button.submitupdate').classList.remove('disabled');
								}
						    };
								
							if (document.querySelectorAll('#compatibilitytype0 tbody tr').length == 0 &&
								document.querySelectorAll('#preupdatecheckheadings tbody tr:not(.jextcompatible)').length == 0) {
						        [].slice.call(document.querySelectorAll('#preupdatecheckheadings, #preupdateconfirmation')).forEach(function(element) {
						            element.style.display = 'none';
						        });
						    };
								
							if (document.querySelectorAll('#compatibilitytype0 tbody tr').length == 0) {
								if(document.querySelectorAll('#compatibilitytype1 tbody tr:not(.jextcompatible)').length == 0) {
									let compatibilityTable1 = document.querySelector('#compatibilitytype1');
									if(compatibilityTable1) {
										compatibilityTable1.style.display = 'none';
									}
								}
			
								clearInterval(jextensionsIntervalTimer);
							}
								
						    jextensionsIntervalCount++;
						}, 1000);
					};
				});");
			$this->app->set('jextstore_joomlaupdate_script', true);
		}
		
		// Get component params
		$this->cParams = JComponentHelper::getParams ( 'com_jmap' );
		if (! $this->cParams->get ( 'default_autoping', 0 ) && ! $this->cParams->get ( 'autoping', 0 )) {
			return;
		}
		
		// Retrieve more informations as much as possible from the current POST array
		$option = $this->app->input->get ( 'option' );
		$view = $this->app->input->get ( 'view' );
		$controller = $this->app->input->get ( 'controller' );
		$task = $this->app->input->get ( 'task' );
		$id = $this->app->input->getInt ( 'id' );
		$catid = $this->app->input->get ( 'cid', null, 'array' );
		$language = $this->app->input->get ( 'language' );
		$name = $this->app->input->getString ( 'name' );
		if (is_array ( $catid )) {
			$catid = $catid [0];
		}
		
		// Valid execution mapping
		$arrayExecution = array (
				'com_zoo' => array (
						'controller' => 'item',
						'task' => array (
								'apply',
								'save',
								'save2new',
								'save2copy' 
						) 
				) 
		);
		
		// Test against valid execution, discard all invalid extensions operations
		if (array_key_exists ( $option, $arrayExecution )) {
			$testIfExecute = $arrayExecution [$option];
			foreach ( $testIfExecute as $property => $value ) {
				$evaluated = $$property;
				
				if (is_array ( $value )) {
					if (! in_array ( $evaluated, $value )) {
						return;
					}
				} else {
					if ($evaluated != $value) {
						return;
					}
				}
			}
		} else {
			return;
		}
		
		// Valid execution success! Go on to route the request to the content plugin, mimic the native Joomla onContentAfterSave
		
		// Auto loader setup
		// Register autoloader prefix
		require_once JPATH_ROOT . '/administrator/components/com_jmap/framework/loader.php';
		JMapLoader::setup ();
		JMapLoader::registerPrefix ( 'JMap', JPATH_ROOT . '/administrator/components/com_jmap/framework' );
		
		JPluginHelper::importPlugin ( 'content', 'pingomatic' );
		
		// Simulate the jsitemap_category_id object for the JSitemap route helper
		$elm = new stdClass ();
		$zooParams = $this->app->input->get ( 'params', null, 'array' );
		$elm->jsitemap_category_id = (int)$zooParams['primary_category'];
		
		// Simulate the $article Joomla object passed to the content observers
		$itemObject = new stdClass ();
		$itemObject->id = $id;
		$itemObject->catid = $elm;
		$itemObject->option = $option;
		$itemObject->view = $view ? $view : $controller;
		$itemObject->language = $language;
		$itemObject->title = $name;
		
		// Trigger the content plugin event
		$this->_subject->trigger ( 'onContentAfterSave', array (
				'com_zoo.item',
				$itemObject,
				false 
		) );
	}

	/**
	 * Hook for the management injection of the custom meta tags informations
	 *
	 * @access public
	 * @return void
	 */
	public function onBeforeCompileHead() {
		$app = JFactory::getApplication ();
		$document = JFactory::getDocument();

		// Avoid operations if plugin is executed in backend
		if ( $app->getClientId ()) {
			return;
		}

		// Checkpoint for Google Analytics tracking code addition
		if($this->jmapConfig->get('inject_gajs_location', 'body') == 'head') {
			$this->addGoogleAnalyticsTrackingCode($app, $document, 'head');
		}
		
		// Check if the robots opt-in directive is required
		if($this->jmapConfig->get('optin_contents', 0)) {
			$robots = trim($this->jmapConfig->get('optin_contents_robots_directive', 'max-snippet:-1,max-image-preview:large,max-video-preview:-1'));
			if($robots) {
				$currentMetaData = $document->getMetaData('robots');
				if(JString::strpos($currentMetaData, 'noindex') === false && JString::strpos($currentMetaData, 'nosnippet') === false) {
					$document->setMetaData('robots', $robots);
				}
			}
		}
		
		// Get the current URI and check for an entry in the DB table
		if($this->jmapConfig->get('metainfo_urldecode', 1)) {
			$uri = urldecode(JUri::current());
		} else {
			// Preserve URLs character encoding if any
			$uri = JUri::current();
		}

		// K2 Tags decoding as an optional processing
		if($this->jmapConfig->get('metainfo_urlencode_space', 0)) {
			$uri = JString::str_ireplace(' ', '%20', $uri);
		}

		// Remove trailing slash for URL matching, this ensure home page match if the option to remove ending slash is enabled
		if($this->jmapConfig->get('metainfo_remove_trailing_slash', 0)) {
			$uri = rtrim( $uri, '/' );
		}
		
		// Apply same metadata even to the corresponding AMP pages
		if($this->jmapConfig->get('amp_sitemap_enabled', 0)) {
			$ampSuffix = $this->jmapConfig->get('amp_suffix', 'amp');
			if(preg_match("/\.$ampSuffix\./i", $uri)) {
				$uri = preg_replace("/\.$ampSuffix\./i", '.', $uri, 1);
			}
			if(preg_match('/\/' . $ampSuffix . '$/i', $uri)) {
				$uri = preg_replace('/\/' . $ampSuffix . '$/i', '', $uri, 1);
			}
		}
		
		// Store for later stage processing
		$this->jmapUri = $uri;
		
		// Setup the query
		$db = JFactory::getDbo();
		$query = "SELECT *" .
				 "\n FROM #__jmap_metainfo" .
				 "\n WHERE " . $db->quoteName('linkurl') . " = " . $db->quote($uri) .
				 "\n AND " . $db->quoteName('published') . " = 1";
		try {
			$metaInfoForThisUri = $db->setQuery($query)->loadObject();
		} catch(Exception $e) {}

		// Yes! Found some metainfo set for this uri, let's inject them into the document
		if(isset($metaInfoForThisUri->id)) {
			$title = trim($metaInfoForThisUri->meta_title);
			$description = trim($metaInfoForThisUri->meta_desc);
			$image = trim($metaInfoForThisUri->meta_image);
			$robots = $metaInfoForThisUri->robots;
			$ogTagsInclude = $this->jmapConfig->get('metainfo_ogtags', 1);
			$twitterCardsTagsInclude = $this->jmapConfig->get('metainfo_twitter_card_enable', 0);

			// Title and og:graph title
			if($title) {
				// Append site name, Joomla 3.2+ support
				if(method_exists($app, 'get')) {
					if ($app->get('sitename_pagetitles', 0) == 2 && trim($app->get('sitename'))) {
						$title = $title . ' - ' . trim($app->get('sitename'));
					} elseif ($app->get('sitename_pagetitles', 0) == 1 && trim($app->get('sitename'))) { // Prepend site name
						$title = trim($app->get('sitename')) . ' - ' . $title;
					}
				}
				
				$document->setTitle($title);
				$document->setMetaData('title', $title);
				$document->setMetaData('metatitle', $title);
				if($ogTagsInclude) {
					$document->setMetaData('og:title', $title, 'property');
					$document->setMetaData('twitter:title', $title);
				}
			}

			// Description and og:graph meta description
			if($description) {
				$document->setDescription($description);
				if($ogTagsInclude) {
					$document->setMetaData('og:description', $description, 'property');
					$document->setMetaData('twitter:description', $description);
				}
			}

			// Set always social share uri if title/desc is specified
			if(($title || $description) && $ogTagsInclude) {
				$document->setMetaData('og:url', $uri, 'property');
				$document->setMetaData('og:type', 'article', 'property');
			}
			
			// Image for social share og:image and twitter:image
			if($image && $ogTagsInclude) {
				$imageLink = preg_match('/http/i', $image) ? $image : JUri::base() . ltrim($image, '/');
				$document->setMetaData('og:image', $imageLink, 'property');
				$document->setMetaData('twitter:image', $imageLink);
			}

			// Robots directive
			if($robots) {
				$document->setMetaData('robots', $robots);
			}
			
			// Additional Twitter cards tags
			if($ogTagsInclude && $twitterCardsTagsInclude) {
				$document->setMetaData('twitter:card', 'summary');
				$twitterCardSite = trim($this->jmapConfig->get('metainfo_twitter_card_site', ''));
				if($twitterCardSite) {
					$document->setMetaData('twitter:site', $twitterCardSite);
				}
				$twitterCardCreator = trim($this->jmapConfig->get('metainfo_twitter_card_creator', ''));
				if($twitterCardCreator) {
					$document->setMetaData('twitter:creator', $twitterCardCreator);
				}
			}
		}
		
		// Check if the override canonical feature is enabled and if so go on and check a url matching for some custom canonical
		if($this->jmapConfig->get('seospider_override_canonical', 1)) {
			$query = "SELECT *" .
					 "\n FROM #__jmap_canonicals" .
					 "\n WHERE " . $db->quoteName('linkurl') . " = " . $db->quote($uri);
			try {
				$canonicalForThisUri = $db->setQuery($query)->loadObject();
			} catch(Exception $e) {}
			
			// Yes! Found a canonical override set for this uri, let's replace them into the document
			if(isset($canonicalForThisUri->id)) {
				// Remove the current canonical tag from the document
				$header = $document->getHeadData();
				foreach($header['links'] as $key => $array) {
					if($array['relation'] == 'canonical') {
						unset($document->_links[$key]);
					}
				}
				
				// Add the new overridden canonical link
				$document->addHeadLink(htmlspecialchars($canonicalForThisUri->canonical), 'canonical', 'rel', array('data-jmap-canonical-override'=>1));
				
				// Override also the og:url metatag with the current canonical
				if(isset($title) && ($title || $description) && $ogTagsInclude) {
					$document->setMetaData('og:url', $canonicalForThisUri->canonical, 'property');
				}
			}
		}
		
		// Fix pagination links if detected adding a page number/results suffix to make them unique and not duplicated
		$isPagination = $app->input->get->get('start', null, 'int');
		$isPage = $app->input->get->get('page', null, 'int');
		if($isPagination || $isPage) {
			$jmapParams = JComponentHelper::getParams('com_jmap');

			// Fix pagination is enabled
			if($jmapParams->get('unique_pagination', 1)) {
				// Get dispatched component params with view overrides
				$contentParams = $app->getParams();

				// Load JMap language translations
				$jLang = JFactory::getLanguage ();
				$jLang->load ( 'com_jmap', JPATH_ROOT . '/components/com_jmap', 'en-GB', true, true );
				if ($jLang->getTag () != 'en-GB') {
					$jLang->load ( 'com_jmap', JPATH_SITE, null, true, false );
					$jLang->load ( 'com_jmap', JPATH_SITE . '/components/com_jmap', null, true, false );
				}

				// Check if pagination params are detected otherwise fallback
				$leadingNum = $contentParams->get('num_leading_articles', null);
				$introNum = $contentParams->get('num_intro_articles', null);
				if($leadingNum && $introNum) {
					$articlesPerPage = (int)($leadingNum + $introNum);
					$pageNum = ' - ' . JText::_('COM_JMAP_PAGE_NUMBER') . ((int)($isPagination / $articlesPerPage) + 1);
				} else {
					// Fallback for generic components staring from xxx
					if($isPage) {
						$pageNum = ' - ' . JText::_('COM_JMAP_PAGE_NUMBER') . (int)$isPage;
					} else {
						$pageNum = ' - ' . JText::_('COM_JMAP_RESULTS_FROM') . $isPagination;
					}
				}

				$currentTitle = $document->getTitle();
				$document->setTitle($currentTitle . $pageNum);
				$currentDescription = $document->getDescription();
				$document->setDescription($currentDescription . $pageNum);
			}
		}

		// Add script json+ld for Rich Snippet Searchbox ONLY to the website homepage
		if ($this->jmapConfig->get ( 'searchbox_enable', 0 )) {
			$loadedMenuItem = $app->getMenu()->getActive();
			if(	$loadedMenuItem && $loadedMenuItem->home == 1 ) {
				$json = array ();
				$array = array ();
				$url = $this->jmapConfig->get ( 'searchbox_url', JUri::root () );
				$type = $this->jmapConfig->get ( 'searchbox_type', 'search' );
				$custom = $this->jmapConfig->get ( 'searchbox_custom', '' );
				$uriInstance = JUri::getInstance();
				$getDomain = rtrim($uriInstance->getScheme() . '://' . $uriInstance->getHost(), '/');
	
				// Register autoloader prefix
				require_once JPATH_ROOT . '/administrator/components/com_jmap/framework/loader.php';
				JMapLoader::setup ();
				JMapLoader::registerPrefix ( 'JMap', JPATH_ROOT . '/administrator/components/com_jmap/framework' );
				$multiLanguageEnabled = JMapLanguageMultilang::isEnabled ();
				$currentSefLanguage = JMapLanguageMultilang::getCurrentSefLanguage () . '/';
				
				if ($type == 'search') {
					$searchComponentLink = JRoute::_ ( 'index.php?option=com_search&searchphrase=all&searchword={search_term}', false );
					$searchComponentLink = JString::str_ireplace ( '/?', '?', $searchComponentLink );
					if ($multiLanguageEnabled) {
						$searchComponentLink = JString::str_ireplace ( $currentSefLanguage, '', $searchComponentLink );
					}
					$search = $getDomain . $searchComponentLink;
				} elseif ($type == 'finder') {
					$smartSearchComponentLink = JRoute::_ ( 'index.php?option=com_finder&q={search_term}', false );
					$smartSearchComponentLink = JString::str_ireplace ( '/?', '?', $smartSearchComponentLink );
					if ($multiLanguageEnabled) {
						$smartSearchComponentLink = JString::str_ireplace ( $currentSefLanguage, '', $smartSearchComponentLink );
					}
					$search = $getDomain . $smartSearchComponentLink;
				} else {
					$search = $custom;
				}
				
				$array ['@context'] = 'https://schema.org';
				$array ['@type'] = 'WebSite';
				$array ['url'] = $url;
				$array ['potentialAction'] ['@type'] = 'SearchAction';
				$array ['potentialAction'] ['target'] = $search;
				$array ['potentialAction'] ['query-input'] = 'required name=search_term';
				
				$document->addScriptDeclaration ( json_encode ( $array ), 'application/ld+json' );
			}
		}
	}
	
	/**
	 * Support for Joomla 3.8+ new routing throwing 404 exception in the parse function of the base router
	 *
	 * @access public
	 * @return boolean
	 */
	public function postProcessParseRule(&$router, &$uri) {
		$contentParams = JComponentHelper::getParams('com_content');
		if($contentParams->get('sef_advanced', 0)) {
			$siteRouter = JRouterSite::getInstance ( 'site', array (
					'mode' => JROUTER_MODE_SEF
			) );
			$option = $siteRouter->getVar('option');
			
			// Check if all parts of the URL have been parsed.
			// Otherwise we have an invalid URL
			if ($option == 'com_content' && strlen($uri->getPath()) > 0) {
				// Get component params and ensure that the custom 404 page is enabled
				$cParams = JComponentHelper::getParams('com_jmap');
			
				// Generate and set a new custom error message based on custom text/html
				$custom404Text = $cParams->get('custom_404_page_text', null);
				
				// Process contents
				$custom404Text = $this->processContentPlugins($custom404Text, $cParams);
			
				// Check if a strip tags is required
				if($cParams->get('custom_404_page_mode', 'html') == 'text') {
					$custom404Text = strip_tags($custom404Text);
				}
				
				throw new Exception($custom404Text, 404);
			}
		}
	}
	
	/**
	 * Hook for the management of the custom 404 page
	 *
	 * @access public
	 * @return boolean
	 */
	public function onRenderModule() {
		static $custom404Handled = false;
		if(self::$renderModuleTrigger) {
			return false;
		}
		
		if($custom404Handled) {
			return false;
		}

		// Mark as handled for next execution cycles
		$custom404Handled = true;

		// Get component params and ensure that the custom 404 page is enabled
		$cParams = JComponentHelper::getParams('com_jmap');
		if(!$cParams->get('custom_404_page_status', 0)) {
			return false;
		}

		// 404 custom page managed as an override by the handleError
		if($cParams->get('custom_404_page_override', 1)) {
			return false;
		}

		// Execute only in frontend
		$app = JFactory::getApplication ();
		if ($app->isAdmin ()) {
			return false;
		}
	
		// Ensure that the JDocumentError class is instantiated as singleton from the legacy J Error class
		if(	version_compare ( JVERSION, '3.6', '>=' )) {
			$attributes = array (
					'charset'   => 'utf-8',
					'lineend'   => 'unix',
					'tab'       => "\t",
					'language'  => 'en-GB',
					'direction' => 'ltr',
			);
			// If there is a JLanguage instance in JFactory then let's pull the language and direction from its metadata
			if (JFactory::$language) {
				$attributes['language']  = JFactory::getLanguage()->getTag();
				$attributes['direction'] = JFactory::getLanguage()->isRtl() ? 'rtl' : 'ltr';
			}
			$document = JDocument::getInstance('error', $attributes);
		} else {
			$document = JDocument::getInstance('error');
		}
		
		if (! isset ( $document->error ) || ! is_object ( $document->error )) {
			return false;
		}
	
		// Dispatched format, apply only to html document
		$documentFormat = $app->input->get ( 'format', null );
		if ($documentFormat && $documentFormat != 'html') {
			return false;
		}
	
		// Dispatched template file, ignores component tmpl
		if ($app->input->get ( 'tmpl', null ) === 'component') {
			return false;
		}

		// Evaluate the error code, 404 only is of our interest and ignore everything else
		$documentExceptionCode = $document->error->getCode ();
		if($documentExceptionCode == 404) {
			// Generate and set a new custom error message based on custom text/html
			$custom404Text = $cParams->get('custom_404_page_text', null);

			// Process contents
			$custom404Text = $this->processContentPlugins($custom404Text, $cParams);

			// Check if a strip tags is required
			if($cParams->get('custom_404_page_mode', 'html') == 'text') {
				$custom404Text = strip_tags($custom404Text);
			}

			// Set the new Exception message supporting HTML and hoping that htmlspecialchars in not used by the error.php of the template
			$newException = new JException($custom404Text, 404);
			$document->setError($newException);
			$document->error = $newException;
		}
	}

	/**
	 * Hook override for the management of the custom 404 error page
	 *
	 * @access public
	 * @return boolean
	 */
	static public function handleError(&$error) {
		// Get the application object.
		$app = JFactory::getApplication();

		// Dispatched format, apply only to html document
		$documentFormat = $app->input->get ( 'format', null );
		if ($documentFormat && $documentFormat != 'html') {
			return false;
		}

		// Dispatched template file, ignores component tmpl
		if ($app->input->get ( 'tmpl', null ) === 'component') {
			return false;
		}

		// Make sure the error is a 404 and we are not in the administrator.
		if (!$app->isAdmin () && $error->getCode () == 404) {
			// Get component params and ensure that the custom 404 page is enabled
			$cParams = JComponentHelper::getParams('com_jmap');
	
			// Generate and set a new custom error message based on custom text/html
			$custom404Text = $cParams->get('custom_404_page_text', null);

			// Process contents
			$custom404Text = self::processContentPlugins($custom404Text, $cParams);

			// Check if a strip tags is required
			if($cParams->get('custom_404_page_mode', 'html') == 'text') {
				$custom404Text = strip_tags($custom404Text);
			}

			$newException = new JException($custom404Text, $error->getCode());
			// Render the error page.
			JError::customErrorPage ( $newException );
		}
	}
	
	/**
	 * Application event
	 *
	 * @access public
	 */
	public function onAfterRender() {
		// Framework reference
		$app = JFactory::getApplication ();
		$doc = JFactory::getDocument ();
	
		// Check if the app can start
		if ($app->isAdmin ()) {
			return false;
		}
	
		// Check if the app can start
		if ($doc->getType () !== 'html') {
			return false;
		}
	
		$option = $app->input->get('option', null);
		if ( $option == 'com_jmap' && $app->input->get('format') ) {
			return false;
		}

		// Check if the override headings feature is enabled and if so go on and check a url matching for some heading
		if($this->jmapConfig->get('seospider_override_headings', 1)) {
			$db = JFactory::getDbo();
			// Search an headings override for this URL
			$query = "SELECT *" .
					 "\n FROM #__jmap_headings" .
					 "\n WHERE " . $db->quoteName('linkurl') . " = " . $db->quote($this->jmapUri);
			try {
				$headingsForThisUri = $db->setQuery($query)->loadObject();
			} catch(Exception $e) {}
				
			// Yes! Found some headings override set for this uri, let's replace them into the document
			if(isset($headingsForThisUri->id)) {
				// Go on only if there is at least one valid heading override
				if($headingsForThisUri->h1 || $headingsForThisUri->h2 || $headingsForThisUri->h3) {
					// Include DOM parser class
					require_once (JPATH_ROOT . '/plugins/system/jmap/simplehtmldom.php');
						
					$simpleHtmlDomInstance = new JMapSimpleHtmlDom();
					$simpleHtmlDomInstance->load( $app->getBody () );
						
					// Find and replace the first encountered H1 tag
					if($headingsForThisUri->h1) {
						$domElementsH1 = $simpleHtmlDomInstance->find( 'h1' );
		
						// Replace the original H1 header with the overridden one
						if(isset($domElementsH1[0])) {
							$element = $domElementsH1[0];
							$nodeText = $element->text(true);
							$nodeText = $headingsForThisUri->h1;
							$element->innertext = $nodeText;
							$element->setAttribute('data-jmap-heading-override', 1);
						}
					}
						
					// Find and replace the first encountered H2 tag
					if($headingsForThisUri->h2) {
						$domElementsH2 = $simpleHtmlDomInstance->find( 'h2' );
							
						// Replace the original H2 header with the overridden one
						if(isset($domElementsH2[0])) {
							$element = $domElementsH2[0];
							$nodeText = $element->text(true);
							$nodeText = $headingsForThisUri->h2;
							$element->innertext = $nodeText;
							$element->setAttribute('data-jmap-heading-override', 1);
						}
					}
						
					// Find and replace the first encountered H3 tag
					if($headingsForThisUri->h3) {
						$domElementsH3 = $simpleHtmlDomInstance->find( 'h3' );
							
						// Replace the original H3 header with the overridden one
						if(isset($domElementsH3[0])) {
							$element = $domElementsH3[0];
							$nodeText = $element->text(true);
							$nodeText = $headingsForThisUri->h3;
							$element->innertext = $nodeText;
							$element->setAttribute('data-jmap-heading-override', 1);
						}
					}
						
					$body = $simpleHtmlDomInstance->save();
						
					// Final assignment
					$app->setBody ( $body );
				}
			}
		}

		// Checkpoint for Google Analytics tracking code addition
		if($this->jmapConfig->get('inject_gajs_location', 'body') == 'body') {
			$this->addGoogleAnalyticsTrackingCode($app, $doc, 'body');
		}
	}
	
	/**
	 * Add additional fields to the supported forms
	 *
	 * @param   JForm  $form  The form to be altered.
	 * @param   mixed  $data  The associated data for the form.
	 *
	 * @return  boolean
	 *
	 * @since   4.6.7
	 */
	function onContentPrepareForm($form, $data) {
		// Check if the default merge data source feature is enabled
		$cParams = JComponentHelper::getParams('com_jmap');
		if(!$cParams->get('merge_generic_menu_by_class', 0)) {
			return true;
		}
		
		// Only works on JForms
		if (!($form instanceof JForm)) return true;
	
		// which belong to the following components
		$components_list = array(
				"com_menus.item"
		);
	
		$app = JFactory::getApplication ();
		$formName = $form->getName();
		if ($app->isSite() || !in_array($formName, $components_list)) return true;

		if(is_object($data)) {
			if(!isset($data->type) || (isset($data->type) && $data->type != 'component')) return true;
		} elseif(is_array($data)) {
			if(!isset($data['type']) || (isset($data['type']) && $data['type'] != 'component')) return true;
		}

		switch ($formName) {
			case 'com_menus.item':
				$form->load('<form>
								<fields name="params">
									<fieldset name="menu-options">
										<field name="jsitemap_default_datasource" type="radio" label="Default sitemap menu item" description="Choose this menu item, as the default one for the linked component, to merge data taken from a separate data source" class="btn-group btn-group-yesno" default="0" filter="integer">
											<option value="1">JYES</option>
											<option value="0">JNO</option>
										</field>
									</fieldset>
								</fields>
							</form>');
				break;
		}
	
		return true;
	}
	
	/* Manage the Joomla updater based on the user license
	 *
	 * @access public
	 * @return void
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers) {
		$uri 	= JUri::getInstance($url);
		$parts 	= explode('/', $uri->getPath());
		$app = JFactory::getApplication();
		if ($uri->getHost() == 'storejextensions.org' && in_array('com_jsitemap.zip', $parts)) {
			// Init as false unless the license is valid
			$validUpdate = false;
			
			// Manage partial language translations
			$jLang = JFactory::getLanguage();
			$jLang->load('com_jmap', JPATH_BASE . '/components/com_jmap', 'en-GB', true, true);
			if($jLang->getTag() != 'en-GB') {
				$jLang->load('com_jmap', JPATH_BASE, null, true, false);
				$jLang->load('com_jmap', JPATH_BASE . '/components/com_jmap', null, true, false);
			}
			
			// Email license validation API call and &$url building construction override
			$cParams = JComponentHelper::getParams('com_jmap');
			$registrationEmail = $cParams->get('registration_email', null);
			
			// License
			if($registrationEmail) {
				$prodCode = 'jsitemappro';
				$cdFuncUsed = 'str_' . 'ro' . 't' . '13';
				
				// Retrieve license informations from the remote REST API
				$apiResponse = null;
				$apiEndpoint = $cdFuncUsed('uggc' . '://' . 'fgberwrkgrafvbaf' . '.bet') . "/option,com_easycommerce/action,licenseCode/email,$registrationEmail/productcode,$prodCode";
				if (function_exists('curl_init')){
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$apiResponse = curl_exec($ch);
					curl_close($ch);
				}
				$objectApiResponse = json_decode($apiResponse);
				
				if(!is_object($objectApiResponse)) {
					// Message user about error retrieving license informations
					$app->enqueueMessage(JText::_('COM_JMAP_ERROR_RETRIEVING_LICENSE_INFO'));
				} else {
					if(!$objectApiResponse->success) {
						switch ($objectApiResponse->reason) {
							// Message user about the reason the license is not valid
							case 'nomatchingcode':
								$app->enqueueMessage(JText::_('COM_JMAP_LICENSE_NOMATCHING'));
								break;
								
							case 'expired':
								// Message user about license expired on $objectApiResponse->expireon
								$app->enqueueMessage(JText::sprintf('COM_JMAP_LICENSE_EXPIRED', $objectApiResponse->expireon));
								break;
						}	
							
					}
					
					// Valid license found, builds the URL update link and message user about the license expiration validity
					if($objectApiResponse->success) {
						$url = $cdFuncUsed('uggc' . '://' . 'fgberwrkgrafvbaf' . '.bet' . '/XZY1306TSPQnifs3243560923kfuxnj35td1rtt45663f.ugzy');
						
						$validUpdate = true;
						$app->enqueueMessage(JText::sprintf('COM_JMAP_EXTENSION_UPDATED_SUCCESS', $objectApiResponse->expireon));
					}
				}
			} else {
				// Message user about missing email license code
				$app->enqueueMessage(JText::sprintf('COM_JMAP_MISSING_REGISTRATION_EMAIL_ADDRESS', JFilterOutput::ampReplace('index.php?option=com_jmap&task=config.display#_licensepreferences')));
			}
			
			if(!$validUpdate) {
				$app->enqueueMessage(JText::_('COM_JMAP_UPDATER_STANDARD_ADVISE'), 'notice');
			}
		}
	}
	
	/**
	 * Override registers Listeners to the Dispatcher
	 * It allows to stop a plugin execution based on the registered listeners
	 *
	 * @override
	 * @return  void
	 */
	public function registerListeners() {
		// Ensure compatibility excluding Joomla 4
		if(version_compare(JVERSION, '4', '>=')) {
			return;
		} elseif (method_exists(get_parent_class($this), 'registerListeners')) {
			parent::registerListeners();
		}
	}
	
	/**
	 * Class constructor, manage params from component
	 *
	 * @access private
	 * @return boolean
	 */
	public function __construct(&$subject) {
		// Ensure compatibility excluding Joomla 4
		if(version_compare(JVERSION, '4', '>=')) {
			return false;
		}
		
		parent::__construct ( $subject );
		$this->joomlaConfig = JFactory::getConfig ();
		
		// Manage partial language translations if editing modules jmap in backend
		$app = JFactory::getApplication ();
		if((($app->input->get('option') == 'com_modules' || $app->input->get('option') == 'com_advancedmodules') &&
			$app->input->get('view') == 'module' &&
			$app->input->get('layout') == 'edit' &&
			$app->isAdmin ()) ||
			($app->input->get('option') == 'com_config' &&
			$app->input->get('controller') == 'config.display.modules' &&
			$app->isSite())) {
			$jLang = JFactory::getLanguage ();
			$jLang->load ( 'com_jmap', JPATH_ADMINISTRATOR . '/components/com_jmap', 'en-GB', true, true );
			if ($jLang->getTag () != 'en-GB') {
				$jLang->load ( 'com_jmap', JPATH_ADMINISTRATOR, null, true, false );
				$jLang->load ( 'com_jmap', JPATH_ADMINISTRATOR . '/components/com_jmap', null, true, false );
			}
		}
		
		// Set the error handler for E_ERROR to be the class handleError method.
		$cParams = JComponentHelper::getParams('com_jmap');
		$this->jmapConfig = $cParams;
		if($cParams->get('custom_404_page_status', 0) && $cParams->get('custom_404_page_override', 1) && !$app->isAdmin ()) {
			// Legacy error class system for legacy stable router
			JError::setErrorHandling(E_ERROR, 'callback', array('plgSystemJMap', 'handleError'));
			
			// Add compatibility support for J3.8 new router management
			if(version_compare(JVERSION, '3.8', '>=')) {
				$joomlaRouter = JFactory::getApplication()->getRouter();
				$joomlaRouter->attachParseRule ( array (
						$this,
						'postProcessParseRule'
				), JRouter::PROCESS_AFTER );
			}
		}
	}
}