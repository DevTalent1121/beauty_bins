<?php
/**
* @title				Minitek Wall
* @copyright   	Copyright (C) 2011-2020 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   http://www.minitek.gr/
* @developers   Minitek.gr
*/

defined('_JEXEC') or die;

class MinitekWallLibBaseMasonryResponsive
{
	public function initCustomGridWidths($masonry_params, $elements, $columns, $widgetID)
	{
		$document = JFactory::getDocument();
		$mnwall = 'mnwall_iso_container_'.$widgetID;
		$column_percent = number_format((100 / $columns), 2, '.', '');
		$dimensions_css = '';

		foreach ($elements as $elem)
		{
			// Width
			$width = (int)$elem->width;
			$percent = (float)($width * $column_percent);
			$dimensions_css .= '
			#'.$mnwall.' .mnwitem'.$elem->index.' {
				width: '.$percent.'%;
			}
			';

			// Height
			$height = (int)$elem->height;

			// Large screen
			$responsive_lg = (int)$masonry_params['mas_responsive_lg'];
			$lg_cell_height = (int)$masonry_params['mas_lg_cell_height'];

			$dimensions_css .= '@media only screen and (min-width:'.$responsive_lg.'px)
			{
				#'.$mnwall.' .mnwitem'.$elem->index.' {
					height: '.($height * $lg_cell_height).'px;
				}
			}';

			// Medium screen
			$responsive_lg_min = $responsive_lg - 1;
			$responsive_md = (int)$masonry_params['mas_responsive_md'];
			$md_cell_height = (int)$masonry_params['mas_md_cell_height'];

			$dimensions_css .= '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
			{
				#'.$mnwall.' .mnwitem'.$elem->index.' {
					height: '.($height * $md_cell_height).'px;
				}
			}';

			// Small screen
			$responsive_md_min = $responsive_md - 1;
			$responsive_sm = (int)$masonry_params['mas_responsive_sm'];
			$sm_cell_height = (int)$masonry_params['mas_sm_cell_height'];

			$dimensions_css .= '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
			{
				#'.$mnwall.' .mnwitem'.$elem->index.' {
					height: '.($height * $sm_cell_height).'px;
				}
			}';

			// Extra small screen
			$responsive_sm_min = $responsive_sm - 1;
			$responsive_xs = (int)$masonry_params['mas_responsive_xs'];
			$xs_cell_height = (int)$masonry_params['mas_xs_cell_height'];

			$dimensions_css .= '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
			{
				#'.$mnwall.' .mnwitem'.$elem->index.' {
					height: '.($height * $xs_cell_height).'px;
				}
			}';

			// Extra extra small screen
			$responsive_xs_min = $responsive_xs - 1;
			$xxs_cell_height = (int)$masonry_params['mas_xxs_cell_height'];

			$dimensions_css .= '@media only screen and (max-width:'.$responsive_xs_min.'px)
			{
				#'.$mnwall.' .mnwitem'.$elem->index.' {
					height: '.($height * $xxs_cell_height).'px;
				}
			}';
		}

		$document->addStyleDeclaration( $dimensions_css );
	}

	public function loadResponsiveMasonry($masonry_params, $widgetID)
	{
		$document = JFactory::getDocument();
		$mnwall = 'mnwall_iso_container_'.$widgetID;

		// Responsive settings
		$responsive_lg = (int)$masonry_params['mas_responsive_lg'];
		$responsive_lg_min = $responsive_lg - 1;
		$lg_cell_height = '240';
		if (array_key_exists('mas_lg_cell_height', $masonry_params))
		{
			$lg_cell_height = (int)$masonry_params['mas_lg_cell_height'];
		}

		$md_type = $masonry_params['mas_md_type'];
		$responsive_md_num = (int)$masonry_params['mas_responsive_md_num'];
		$responsive_md = (int)$masonry_params['mas_responsive_md'];
		$responsive_md_min = $responsive_md - 1;
		$md_cell_height = '240';
		if (array_key_exists('mas_md_cell_height', $masonry_params))
		{
			$md_cell_height = (int)$masonry_params['mas_md_cell_height'];
		}

		$sm_type = $masonry_params['mas_sm_type'];
		$responsive_sm_num = (int)$masonry_params['mas_responsive_sm_num'];
		$responsive_sm = (int)$masonry_params['mas_responsive_sm'];
		$responsive_sm_min = $responsive_sm - 1;
		$sm_cell_height = '240';
		if (array_key_exists('mas_sm_cell_height', $masonry_params))
		{
			$sm_cell_height = (int)$masonry_params['mas_sm_cell_height'];
		}

		$xs_type = $masonry_params['mas_xs_type'];
		$responsive_xs_num = (int)$masonry_params['mas_responsive_xs_num'];
		$responsive_xs = (int)$masonry_params['mas_responsive_xs'];
		$responsive_xs_min = $responsive_xs - 1;
		$xs_cell_height = '240';
		if (array_key_exists('mas_xs_cell_height', $masonry_params))
		{
			$xs_cell_height = (int)$masonry_params['mas_xs_cell_height'];
		}

		$xxs_type = $masonry_params['mas_xxs_type'];
		$responsive_xxs_num = (int)$masonry_params['mas_responsive_xxs_num'];
		$xxs_cell_height = '240';
		if (array_key_exists('mas_xxs_cell_height', $masonry_params))
		{
			$xxs_cell_height = (int)$masonry_params['mas_xxs_cell_height'];
		}

		$detail_box_column = $masonry_params['mas_db_columns'];
		$show_title_column = $masonry_params['mas_db_title_columns'];
		$show_introtext_column = $masonry_params['mas_db_introtext_columns'];
		$show_date_column = $masonry_params['mas_db_date_columns'];
		$show_category_column = $masonry_params['mas_db_category_columns'];
		$show_location_column = $masonry_params['mas_db_location_columns'];
		$show_author_column = $masonry_params['mas_db_author_columns'];
		$show_price_column = $masonry_params['mas_db_price_columns'];
		$show_hits_column = $masonry_params['mas_db_hits_columns'];
		$show_count_column = $masonry_params['mas_db_count_columns'];
		$show_readmore_column = $masonry_params['mas_db_readmore_columns'];

		// Media CSS - LG screen
		$lg_media = '@media only screen and (min-width:'.$responsive_lg.'px)
		{';
			$lg_media .= '
				#'.$mnwall.' .mnwall-big {
					height: '.(2*$lg_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-horizontal {
					height: '.($lg_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-vertical {
					height: '.(2*$lg_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-small {
					height: '.($lg_cell_height).'px;
				}
			';
			if ((!isset($masonry_params['mas_preserve_aspect_ratio'])
			|| !$masonry_params['mas_preserve_aspect_ratio']))
			{
				$lg_media .= '
					.mnwall-columns #'.$mnwall.' .mnwall-photo-link {
						height: '.$lg_cell_height.'px !important;
					}
				';
			}
		$lg_media .= '
		}';
		$document->addStyleDeclaration( $lg_media );

		// Media CSS - MD screen
		if (!$md_type)
		{
			$md_media_jf = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
			{';
				$md_media_jf .= '
				#'.$mnwall.' .mnwall-big {
					height: '.(2*$md_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-horizontal {
					height: '.($md_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-vertical {
					height: '.(2*$md_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-small {
					height: '.($md_cell_height).'px;
				}

				#'.$mnwall.' .mnwall-big .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-big .mnwall-item-inner .mnwall-title span {
					font-size: 24px;
					line-height: 28px;
				}
				#'.$mnwall.' .mnwall-horizontal .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-horizontal .mnwall-item-inner .mnwall-title span,
				#'.$mnwall.' .mnwall-vertical .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-vertical .mnwall-item-inner .mnwall-title span,
				#'.$mnwall.' .mnwall-small .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-small .mnwall-item-inner .mnwall-title span {
					font-size: 18px;
					line-height: 20px;
				}';

				if ($masonry_params['mas_grid'] == '98o')
				{
					if ((!isset($masonry_params['mas_preserve_aspect_ratio'])
						|| !$masonry_params['mas_preserve_aspect_ratio']))
					{
						$md_media_jf .= '
						.mnwall-columns #'.$mnwall.' .mnwall-photo-link {
							height: '.$md_cell_height.'px !important;
						}';
					}
				}
			$md_media_jf .= '
			}';
			$document->addStyleDeclaration( $md_media_jf );
		}

		// Media CSS - MD screen - Equal columns
		if ($md_type)
		{
			$items_width = number_format((float)(100 / $responsive_md_num), 2, '.', '');
			$md_media = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
			{ ';
				if ($masonry_params['mas_db_position_columns'] == 'below')
				{
					$md_media .= '
					#'.$mnwall.' .mnwall-item {
						height: auto !important;
					}
					#'.$mnwall.' .mnwall-item-inner {
						position: static;
						width: 100% !important;
					}
					.mnwall-masonry #'.$mnwall.' .mnwall-item-outer-cont .mnwall-photo-link {
						z-index: 1;
						width: 100%;
						position: relative;
						display: flex;
						justify-content: center;
						align-items: center;
						overflow: hidden;
						height: '.$md_cell_height.'px !important;
					}';
				} else {
					$md_media .= '
					.mnwall-masonry #'.$mnwall.' .mnwall-item {
						height: '.$md_cell_height.'px !important;
					}
					#'.$mnwall.' .mnwall-item-inner {
						width: 100% !important;
						top: auto !important;
						bottom: 0 !important;
						left: 0 !important;
					}
					.mnwall-masonry #'.$mnwall.' .mnwall-item-outer-cont .mnwall-photo-link {
						width: 100%;
						height: 100%;
					}';
					if ($masonry_params['mas_db_position_columns'] == 'bottom')
					{
						$md_media .= '
						#'.$mnwall.' .mnwall-item-inner {
							height: auto !important;
						}
						#'.$mnwall.' .mnwall-item-inner.mnw-no-image {
							height: 100% !important;
						}';
					} else {
						$md_media .= '
						#'.$mnwall.' .mnwall-item-inner {
							height: 100% !important;
						}';
					}
				}

				if ((!isset($masonry_params['mas_preserve_aspect_ratio'])
				|| !$masonry_params['mas_preserve_aspect_ratio']))
				{
					$md_media .= '
					.mnwall-columns #'.$mnwall.' .mnwall-photo-link {
						height: '.$md_cell_height.'px !important;
					}
					';
				}

				$md_media .= '
				#'.$mnwall.' .mnwall-item {
					width: '.$items_width.'% !important;
				}
				#'.$mnwall.' .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-item-inner .mnwall-title span {
					font-size: 18px;
					line-height: 24px;
				}
			}';
			$document->addStyleDeclaration( $md_media );

			if ($detail_box_column) {
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box {
						display: block !important;
					}
				}';
			} else {
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $detail_box_column_css );

			if ($show_title_column) {
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-title {
						display: block !important;
					}
				}';
			} else {
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-title {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_title_column_css );

			if ($show_introtext_column) {
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
						display: block !important;
					}
				}';
			} else {
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_introtext_column_css );

			if ($show_date_column) {
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-date {
						display: block !important;
					}
				}';
			} else {
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-date {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_date_column_css );

			if ($show_category_column) {
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
						display: block !important;
					}
				}';
			} else {
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_category_column_css );

			if ($show_location_column) {
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
						display: block !important;
					}
				}';
			} else {
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_location_column_css );

			if ($show_author_column) {
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
						display: block !important;
					}
				}';
			} else {
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_author_column_css );

			if ($show_price_column) {
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-price {
						display: block !important;
					}
				}';
			} else {
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-price {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_price_column_css );

			if ($show_hits_column) {
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
						display: block !important;
					}
				}';
			} else {
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_hits_column_css );

			if ($show_count_column) {
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-count {
						display: block !important;
					}
				}';
			} else {
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-count {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_count_column_css );

			if ($show_readmore_column) {
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
						display: block !important;
					}
				}';
			} else {
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_md.'px) and (max-width:'.$responsive_lg_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_readmore_column_css );
		}

		// Media CSS - SM screen
		if (!$sm_type)
		{
			$sm_media_jf = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
			{';
				$sm_media_jf .= '
				#'.$mnwall.' .mnwall-big {
					height: '.(2*$sm_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-horizontal {
					height: '.($sm_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-vertical {
					height: '.(2*$sm_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-small {
					height: '.($sm_cell_height).'px;
				}

				#'.$mnwall.' .mnwall-big .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-big .mnwall-item-inner .mnwall-title span {
					font-size: 22px;
					line-height: 26px;
				}
				#'.$mnwall.' .mnwall-horizontal .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-horizontal .mnwall-item-inner .mnwall-title span,
				#'.$mnwall.' .mnwall-vertical .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-vertical .mnwall-item-inner .mnwall-title span,
				#'.$mnwall.' .mnwall-small .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-small .mnwall-item-inner .mnwall-title span {
					font-size: 17px;
					line-height: 20px;
				}';

				if ($masonry_params['mas_grid'] == '98o')
				{
					if ((!isset($masonry_params['mas_preserve_aspect_ratio'])
						|| !$masonry_params['mas_preserve_aspect_ratio']))
					{
						$sm_media_jf .= '
						.mnwall-columns #'.$mnwall.' .mnwall-photo-link {
							height: '.$sm_cell_height.'px !important;
						}';
					}
				}
			$sm_media_jf .= '
			}';
			$document->addStyleDeclaration( $sm_media_jf );
		}

		// Media CSS - SM screen - Equal columns
		if ($sm_type)
		{
			$items_width = number_format((float)(100 / $responsive_sm_num), 2, '.', '');
			$sm_media = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
			{ ';
				if ($masonry_params['mas_db_position_columns'] == 'below')
				{
					$sm_media .= '
					#'.$mnwall.' .mnwall-item {
						height: auto !important;
					}
					#'.$mnwall.' .mnwall-item-inner {
						position: static;
						width: 100% !important;
					}
					.mnwall-masonry #'.$mnwall.' .mnwall-item-outer-cont .mnwall-photo-link {
						z-index: 1;
						width: 100%;
						position: relative;
						display: flex;
						justify-content: center;
						align-items: center;
						overflow: hidden;
						height: '.$sm_cell_height.'px !important;
					}';
				} else {
					$sm_media .= '
					.mnwall-masonry #'.$mnwall.' .mnwall-item {
						height: '.$sm_cell_height.'px !important;
					}
					#'.$mnwall.' .mnwall-item-inner {
						width: 100% !important;
						top: auto !important;
						bottom: 0 !important;
						left: 0 !important;
					}
					.mnwall-masonry #'.$mnwall.' .mnwall-item-outer-cont .mnwall-photo-link {
						width: 100%;
						height: 100%;
					}';
					if ($masonry_params['mas_db_position_columns'] == 'bottom')
					{
						$sm_media .= '
						#'.$mnwall.' .mnwall-item-inner {
							height: auto !important;
						}
						#'.$mnwall.' .mnwall-item-inner.mnw-no-image {
							height: 100% !important;
						}';
					} else {
						$sm_media .= '
						#'.$mnwall.' .mnwall-item-inner {
							height: 100% !important;
						}';
					}
				}

				if ((!isset($masonry_params['mas_preserve_aspect_ratio'])
				|| !$masonry_params['mas_preserve_aspect_ratio']))
				{
					$sm_media .= '
					.mnwall-columns #'.$mnwall.' .mnwall-photo-link {
						height: '.$sm_cell_height.'px !important;
					}
					';
				}

				$sm_media .= '
				#'.$mnwall.' .mnwall-item {
					width: '.$items_width.'% !important;
				}
				#'.$mnwall.' .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-item-inner .mnwall-title span {
					font-size: 18px !important;
					line-height: 24px;
				}
			}';
			$document->addStyleDeclaration( $sm_media );

			if ($detail_box_column) {
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box {
						display: block !important;
					}
				}';
			} else {
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $detail_box_column_css );

			if ($show_title_column) {
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-title {
						display: block !important;
					}
				}';
			} else {
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-title {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_title_column_css );

			if ($show_introtext_column) {
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
						display: block !important;
					}
				}';
			} else {
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_introtext_column_css );

			if ($show_date_column) {
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-date {
						display: block !important;
					}
				}';
			} else {
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-date {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_date_column_css );

			if ($show_category_column) {
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
						display: block !important;
					}
				}';
			} else {
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_category_column_css );

			if ($show_location_column) {
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
						display: block !important;
					}
				}';
			} else {
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_location_column_css );

			if ($show_author_column) {
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
						display: block !important;
					}
				}';
			} else {
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_author_column_css );

			if ($show_price_column) {
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-price {
						display: block !important;
					}
				}';
			} else {
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-price {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_price_column_css );

			if ($show_hits_column) {
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
						display: block !important;
					}
				}';
			} else {
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_hits_column_css );

			if ($show_count_column) {
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-count {
						display: block !important;
					}
				}';
			} else {
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-count {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_count_column_css );

			if ($show_readmore_column) {
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
						display: block !important;
					}
				}';
			} else {
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_sm.'px) and (max-width:'.$responsive_md_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_readmore_column_css );
		}

		// Media CSS - XS screen
		if (!$xs_type)
		{
			$xs_media_jf = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
			{';
				$xs_media_jf .= '
				#'.$mnwall.' .mnwall-big {
					height: '.(2*$xs_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-horizontal {
					height: '.($xs_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-vertical {
					height: '.(2*$xs_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-small {
					height: '.($xs_cell_height).'px;
				}

				#'.$mnwall.' .mnwall-photo-link {
					width: 100% !important;
					height: 100% !important;
				}';

				if ($masonry_params['mas_grid'] == '98o')
				{
					if ((!isset($masonry_params['mas_preserve_aspect_ratio'])
						|| !$masonry_params['mas_preserve_aspect_ratio']))
					{
						$xs_media_jf .= '
						.mnwall-columns #'.$mnwall.' .mnwall-photo-link {
							height: '.$xs_cell_height.'px !important;
						}';
					}
				}
			$xs_media_jf .= '
			}';
			$document->addStyleDeclaration( $xs_media_jf );
		}

		// Media CSS - XS screen - Equal columns
		if ($xs_type)
		{
			$items_width = number_format((float)(100 / $responsive_xs_num), 2, '.', '');
			$xs_media = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
			{ ';
				if ($masonry_params['mas_db_position_columns'] == 'below')
				{
					$xs_media .= '
					#'.$mnwall.' .mnwall-item {
						height: auto !important;
					}
					#'.$mnwall.' .mnwall-item-inner {
						position: static;
						width: 100% !important;
					}
					.mnwall-masonry #'.$mnwall.' .mnwall-item-outer-cont .mnwall-photo-link {
						z-index: 1;
						width: 100%;
						position: relative;
						display: flex;
						justify-content: center;
						align-items: center;
						overflow: hidden;
						height: '.$xs_cell_height.'px !important;
					}';
				} else {
					$xs_media .= '
					.mnwall-masonry #'.$mnwall.' .mnwall-item {
						height: '.$xs_cell_height.'px !important;
					}
					#'.$mnwall.' .mnwall-item-inner {
						width: 100% !important;
						top: auto !important;
						bottom: 0 !important;
						left: 0 !important;
					}
					.mnwall-masonry #'.$mnwall.' .mnwall-item-outer-cont .mnwall-photo-link {
						width: 100%;
						height: 100%;
					}';
					if ($masonry_params['mas_db_position_columns'] == 'bottom')
					{
						$xs_media .= '
						#'.$mnwall.' .mnwall-item-inner {
							height: auto !important;
						}
						#'.$mnwall.' .mnwall-item-inner.mnw-no-image {
							height: 100% !important;
						}';
					} else {
						$xs_media .= '
						#'.$mnwall.' .mnwall-item-inner {
							height: 100% !important;
						}';
					}
				}

				if ((!isset($masonry_params['mas_preserve_aspect_ratio'])
				|| !$masonry_params['mas_preserve_aspect_ratio']))
				{
					$xs_media .= '
					.mnwall-columns #'.$mnwall.' .mnwall-photo-link {
						height: '.$xs_cell_height.'px !important;
					}
					';
				}

				$xs_media .= '
				#'.$mnwall.' .mnwall-item {
					width: '.$items_width.'% !important;
				}
				#'.$mnwall.' .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-item-inner .mnwall-title span {
					font-size: 18px;
					line-height: 24px;
				}
			}';
			$document->addStyleDeclaration( $xs_media );

			if ($detail_box_column) {
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box {
						display: block !important;
					}
				}';
			} else {
				$detail_box_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $detail_box_column_css );

			if ($show_title_column) {
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-title {
						display: block !important;
					}
				}';
			} else {
				$show_title_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-title {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_title_column_css );

			if ($show_introtext_column) {
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
						display: block !important;
					}
				}';
			} else {
				$show_introtext_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_introtext_column_css );

			if ($show_date_column) {
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-date {
						display: block !important;
					}
				}';
			} else {
				$show_date_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-date {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_date_column_css );

			if ($show_category_column) {
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
						display: block !important;
					}
				}';
			} else {
				$show_category_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_category_column_css );

			if ($show_location_column) {
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
						display: block !important;
					}
				}';
			} else {
				$show_location_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_location_column_css );

			if ($show_author_column) {
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
						display: block !important;
					}
				}';
			} else {
				$show_author_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_author_column_css );

			if ($show_price_column) {
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-price {
						display: block !important;
					}
				}';
			} else {
				$show_price_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-price {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_price_column_css );

			if ($show_hits_column) {
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
						display: block !important;
					}
				}';
			} else {
				$show_hits_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_hits_column_css );

			if ($show_count_column) {
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-count {
						display: block !important;
					}
				}';
			} else {
				$show_count_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-count {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_count_column_css );

			if ($show_readmore_column) {
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
						display: block !important;
					}
				}';
			} else {
				$show_readmore_column_css = '@media only screen and (min-width:'.$responsive_xs.'px) and (max-width:'.$responsive_sm_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_readmore_column_css );
		}

		// Media CSS - XXS screen
		if (!$xxs_type)
		{
			$xxs_media_jf = '@media only screen and (max-width:'.$responsive_xs_min.'px)
			{';
				$xxs_media_jf .= '
				#'.$mnwall.' .mnwall-big {
					height: '.(2*$xxs_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-horizontal {
					height: '.($xxs_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-vertical {
					height: '.(2*$xxs_cell_height).'px;
				}
				#'.$mnwall.' .mnwall-small {
					height: '.($xxs_cell_height).'px;
				}

				#'.$mnwall.' .mnwall-photo-link {
					width: 100% !important;
					height: 100% !important;
				}';

				if ($masonry_params['mas_grid'] == '98o')
				{
					if ((!isset($masonry_params['mas_preserve_aspect_ratio'])
						|| !$masonry_params['mas_preserve_aspect_ratio']))
					{
						$xxs_media_jf .= '
						.mnwall-columns #'.$mnwall.' .mnwall-photo-link {
							height: '.$xxs_cell_height.'px !important;
						}';
					}
				}
			$xxs_media_jf .= '
			}';
			$document->addStyleDeclaration( $xxs_media_jf );
		}

		// Media CSS - XXS screen - Equal columns
		if ($xxs_type)
		{
			$items_width = number_format((float)(100 / $responsive_xxs_num), 2, '.', '');
			$xxs_media = '@media only screen and (max-width:'.$responsive_xs_min.'px)
			{ ';
				if ($masonry_params['mas_db_position_columns'] == 'below')
				{
					$xxs_media .= '
					#'.$mnwall.' .mnwall-item {
						height: auto !important;
					}
					#'.$mnwall.' .mnwall-item-inner {
						position: static;
						width: 100% !important;
					}
					.mnwall-masonry #'.$mnwall.' .mnwall-item-outer-cont .mnwall-photo-link {
						z-index: 1;
						width: 100%;
						position: relative;
						display: flex;
						justify-content: center;
						align-items: center;
						overflow: hidden;
						height: '.$xxs_cell_height.'px !important;
					}';
				} else {
					$xxs_media .= '
					.mnwall-masonry #'.$mnwall.' .mnwall-item {
						height: '.$xxs_cell_height.'px !important;
					}
					#'.$mnwall.' .mnwall-item-inner {
						width: 100% !important;
						top: auto !important;
						bottom: 0 !important;
						left: 0 !important;
					}
					.mnwall-masonry #'.$mnwall.' .mnwall-item-outer-cont .mnwall-photo-link {
						width: 100%;
						height: 100%;
					}';
					if ($masonry_params['mas_db_position_columns'] == 'bottom')
					{
						$xxs_media .= '
						#'.$mnwall.' .mnwall-item-inner {
							height: auto !important;
						}
						#'.$mnwall.' .mnwall-item-inner.mnw-no-image {
							height: 100% !important;
						}';
					} else {
						$xxs_media .= '
						#'.$mnwall.' .mnwall-item-inner {
							height: 100% !important;
						}';
					}
				}

				if ((!isset($masonry_params['mas_preserve_aspect_ratio'])
				|| !$masonry_params['mas_preserve_aspect_ratio']))
				{
					$xxs_media .= '
					.mnwall-columns #'.$mnwall.' .mnwall-photo-link {
						height: '.$xxs_cell_height.'px !important;
					}
					';
				}

				$xxs_media .= '
				#'.$mnwall.' .mnwall-item {
					width: '.$items_width.'% !important;
				}
				#'.$mnwall.' .mnwall-item-inner .mnwall-title a,
				#'.$mnwall.' .mnwall-item-inner .mnwall-title span {
					font-size: 18px;
					line-height: 24px;
				}
			}';
			$document->addStyleDeclaration( $xxs_media );

			if ($detail_box_column) {
				$detail_box_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box {
						display: block !important;
					}
				}';
			} else {
				$detail_box_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $detail_box_column_css );

			if ($show_title_column) {
				$show_title_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-title {
						display: block !important;
					}
				}';
			} else {
				$show_title_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-title {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_title_column_css );

			if ($show_introtext_column) {
				$show_introtext_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
						display: block !important;
					}
				}';
			} else {
				$show_introtext_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-desc {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_introtext_column_css );

			if ($show_date_column) {
				$show_date_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-date {
						display: block !important;
					}
				}';
			} else {
				$show_date_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-date {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_date_column_css );

			if ($show_category_column) {
				$show_category_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
						display: block !important;
					}
				}';
			} else {
				$show_category_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-category {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_category_column_css );

			if ($show_location_column) {
				$show_location_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
						display: block !important;
					}
				}';
			} else {
				$show_location_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-location {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_location_column_css );

			if ($show_author_column) {
				$show_author_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
						display: block !important;
					}
				}';
			} else {
				$show_author_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-item-author {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_author_column_css );

			if ($show_price_column) {
				$show_price_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-price {
						display: block !important;
					}
				}';
			} else {
				$show_price_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-price {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_price_column_css );

			if ($show_hits_column) {
				$show_hits_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
						display: block !important;
					}
				}';
			} else {
				$show_hits_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-hits {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_hits_column_css );

			if ($show_count_column) {
				$show_count_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-count {
						display: block !important;
					}
				}';
			} else {
				$show_count_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-count {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_count_column_css );

			if ($show_readmore_column) {
				$show_readmore_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
						display: block !important;
					}
				}';
			} else {
				$show_readmore_column_css = '@media only screen and (max-width:'.$responsive_xs_min.'px)
				{
					#'.$mnwall.' .mnwall-detail-box .mnwall-readmore {
						display: none !important;
					}
				}';
			}
			$document->addStyleDeclaration( $show_readmore_column_css );
		}

		// List items - Responsive configuration
		if ($masonry_params['mas_grid'] == '99v')
		{
			$list_items_media = '
			.mnwall-list #'.$mnwall.' .mnwall-item {
				width: 100% !important;
				height: auto !important;
			}
			.mnwall-list #'.$mnwall.' .mnwall-item-inner {
				width: auto !important;
			}
			.mnwall-list #'.$mnwall.' .mnwall-photo-link {
				height: auto !important;
			}
			.mnwall-list #'.$mnwall.' .mnwall-item-inner .mnwall-title a,
			.mnwall-list #'.$mnwall.' .mnwall-item-inner .mnwall-title span {
				font-size: 18px;
			}
			@media only screen and (max-width: 550px)
			{
				.mnwall-list #'.$mnwall.' .mnwall-cover {
					width: 100%;
					max-width: inherit;
				}
				.mnwall-list #'.$mnwall.' .mnwall-photo-link img {
					width: 100%;
					max-width: 100%;
				}
			}';
			$document->addStyleDeclaration( $list_items_media );
		}
	}
}
