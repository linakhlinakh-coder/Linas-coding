<?php
/**
 * Demo import: builds every page of the approved design as Elementor content,
 * the header/footer templates, sample blog notes, and sets the homepage.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Elementor data builders
 * ---------------------------------------------------------------------- */

/**
 * Random Elementor element id.
 *
 * @return string
 */
function md_eid() {
	return substr( md5( wp_generate_password( 12, false ) . microtime() ), 0, 7 );
}

/**
 * Give every repeater row an _id.
 *
 * @param array $settings Settings.
 * @return array
 */
function md_demo_ids( $settings ) {
	foreach ( $settings as $k => $v ) {
		if ( is_array( $v ) && isset( $v[0] ) && is_array( $v[0] ) ) {
			foreach ( $v as $i => $row ) {
				if ( is_array( $row ) && ! isset( $row['_id'] ) ) {
					$settings[ $k ][ $i ]['_id'] = md_eid();
				}
			}
		}
	}
	return $settings;
}

/**
 * One widget element.
 *
 * @param string $type     Widget name.
 * @param array  $settings Settings (anything left out uses the widget's default content).
 * @return array
 */
function md_w( $type, $settings = array() ) {
	return array(
		'id'         => md_eid(),
		'elType'     => 'widget',
		'widgetType' => $type,
		'settings'   => md_demo_ids( $settings ),
		'elements'   => array(),
	);
}

/**
 * Does this site use Elementor's Flexbox containers?
 *
 * @return bool
 */
function md_uses_containers() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return true;
	}
	$exp = \Elementor\Plugin::$instance->experiments;
	return ! $exp || ! method_exists( $exp, 'is_feature_active' ) || $exp->is_feature_active( 'container' );
}

/**
 * Wrap each widget in a full-width, zero-padding container (or section/column).
 *
 * @param array $widgets Widgets.
 * @return array
 */
function md_wrap( $widgets ) {
	$out        = array();
	$containers = md_uses_containers();
	$zero       = array(
		'unit'     => 'px',
		'top'      => '0',
		'right'    => '0',
		'bottom'   => '0',
		'left'     => '0',
		'isLinked' => true,
	);
	foreach ( $widgets as $w ) {
		if ( $containers ) {
			$out[] = array(
				'id'       => md_eid(),
				'elType'   => 'container',
				'settings' => array(
					'content_width' => 'full',
					'padding'       => $zero,
					'margin'        => $zero,
					'flex_gap'      => array(
						'unit'     => 'px',
						'size'     => 0,
						'column'   => '0',
						'row'      => '0',
						'isLinked' => true,
					),
				),
				'elements' => array( $w ),
				'isInner'  => false,
			);
		} else {
			$out[] = array(
				'id'       => md_eid(),
				'elType'   => 'section',
				'settings' => array(
					'layout'  => 'full_width',
					'gap'     => 'no',
					'padding' => $zero,
				),
				'elements' => array(
					array(
						'id'       => md_eid(),
						'elType'   => 'column',
						'settings' => array(
							'_column_size' => 100,
							'_inline_size' => null,
							'padding'      => $zero,
						),
						'elements' => array( $w ),
						'isInner'  => false,
					),
				),
				'isInner'  => false,
			);
		}
	}
	return $out;
}

/**
 * Save Elementor data on a post.
 *
 * @param int    $post_id  Post.
 * @param array  $widgets  Widgets.
 * @param array  $settings Page settings.
 * @param string $type     Elementor document type.
 */
function md_save_elementor( $post_id, $widgets, $settings = array(), $type = 'wp-page' ) {
	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $post_id, '_elementor_template_type', $type );
	update_post_meta( $post_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
	update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( md_wrap( $widgets ) ) ) );
	if ( $settings ) {
		update_post_meta( $post_id, '_elementor_page_settings', $settings );
	}
	if ( 'wp-page' === $type ) {
		update_post_meta( $post_id, '_wp_page_template', 'default' );
	}
}

/**
 * Create a page.
 *
 * @param string $title  Title.
 * @param string $slug   Slug.
 * @param int    $parent Parent.
 * @return int
 */
function md_make_page( $title, $slug, $parent = 0 ) {
	return (int) wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_parent'  => $parent,
			'post_content' => '',
		)
	);
}

/**
 * Create an Elementor template (Templates → Saved Templates).
 *
 * @param string $title    Title.
 * @param array  $widgets  Widgets.
 * @return int
 */
function md_make_template( $title, $widgets ) {
	$id = (int) wp_insert_post(
		array(
			'post_type'   => 'elementor_library',
			'post_status' => 'publish',
			'post_title'  => $title,
		)
	);
	if ( $id ) {
		md_save_elementor( $id, $widgets, array(), 'section' );
		wp_set_object_terms( $id, 'section', 'elementor_library_type' );
	}
	return $id;
}

/* -------------------------------------------------------------------------
 * Page content
 * ---------------------------------------------------------------------- */

/**
 * Homepage panels (English).
 *
 * @return array
 */
function md_demo_home() {
	$w = array( md_w( 'md-home-hero' ) );
	foreach ( md_services() as $i => $sv ) {
		$dark = 1 === $i % 2;
		$w[]  = md_w(
			'md-home-service',
			array(
				'label'    => str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) . ' — Service',
				'title'    => $sv['name'],
				'lines'    => $sv['home'],
				'ph'       => $sv['ph'],
				'tone'     => $dark ? 'dark' : 'light',
				'side'     => $dark ? 'end' : 'start',
				'dot'      => $sv['name'],
				'lnk_link' => array( 'url' => md_service_path( $i ) ),
				'btn_link' => array( 'url' => '/portfolio/case-study/' ),
			)
		);
	}
	$w[] = md_w( 'md-home-about' );
	$w[] = md_w( 'md-home-audit' );
	$w[] = md_w( 'md-home-closing' );
	return $w;
}

/**
 * Homepage panels (Arabic, HomeAr.dc.html).
 *
 * @return array
 */
function md_demo_home_ar() {
	$svc = array(
		array( '٠١ — خدمة', 'التسويق الرقمي', "استراتيجية وحملات ونموّ يُقاس.\nحملات مدفوعة، محرّكات بحث، بريد إلكتروني وتقارير.", '[صورة على كامل الشاشة — مشروع تسويق رقمي]' ),
		array( '٠٢ — خدمة', 'إدارة وسائل التواصل', "حضور يومي، مخطَّط ومتّسق.\nتقويم محتوى، نشر، إدارة مجتمع وتقارير شهرية.", '[صورة على كامل الشاشة — مشروع وسائل تواصل]' ),
		array( '٠٣ — خدمة', 'صناعة المحتوى', "أفكار تُصنع لتُشاهَد وتُقرأ وتُشارَك.\nسلاسل، تصوير، فيديو قصير، تصاميم ومقالات.", '[صورة على كامل الشاشة — مشروع محتوى]' ),
		array( '٠٤ — خدمة', 'خدمات التصميم', "هويات وتصاميم لها وجهة نظر.\nشعارات، أدلّة هوية، قوالب، مطبوعات ومواقع.", '[صورة على كامل الشاشة — مشروع تصميم]' ),
		array( '٠٥ — خدمة', 'الإنتاج والتصوير', "من قائمة اللقطات إلى النسخة النهائية.\nأفلام العلامة، إعلانات، موشن غرافيك وتغطية فعاليات.", '[صورة أو فيديو على كامل الشاشة — مشروع إنتاج]' ),
		array( '٠٦ — خدمة', 'كتابة النصوص الإعلانية', "كلمات تشبهكم وتبيع عنكم.\nصوت العلامة، مواقع وحملات، بالعربية والإنجليزية.", '[صورة على كامل الشاشة — مشروع كتابة]' ),
		array( '٠٧ — خدمة', 'السيناريو والأعمال السينمائية', "حكايات تُكتب للشاشة.\nسيناريو، لوحات قصصية وإدارة إبداعية في موقع التصوير.", '[لقطة سينمائية على كامل الشاشة — مشروع سيناريو]' ),
		array( '٠٨ — خدمة', 'بناء أنظمة إدارة العملاء', "كل عميل محتمل يُسجَّل، وكل عميل يُتذكَّر.\nإعداد، مسارات بيع، متابعة آلية وتدريب الفريق.", '[صورة على كامل الشاشة — مشروع إدارة عملاء]' ),
		array( '٠٩ — خدمة', 'الأتمتة بالذكاء الاصطناعي', "أنظمة ذكية ترفع العمل المتكرّر عن فريقكم.\nسير عمل، مساعدون آليون، تقارير ومراقبة.", '[صورة على كامل الشاشة — مشروع أتمتة]' ),
	);
	$w = array(
		md_w(
			'md-home-hero',
			array(
				'eyebrow'      => 'وكالة إبداعية متخصّصة، لبنان',
				'headline'     => 'علامتكم تستحقّ بيتاً، لا مصنعاً.',
				'typed_prefix' => 'نصنع',
				'typed_words'  => "التسويق الرقمي\nحضور وسائل التواصل\nالمحتوى\nالتصميم\nالأفلام\nالنصوص\nالسيناريو\nأنظمة إدارة العملاء\nالأتمتة الذكية",
				'cue'          => 'ماذا نقدّم',
				'ph_d'         => '[فيديو أعمالنا يتكرّر بصمت على كامل الشاشة — يُزوَّد لاحقاً]',
				'ph_m'         => '[صورة ثابتة على الهاتف — تُزوَّد لاحقاً]',
				'reel_label'   => 'شاهدوا أعمالنا المصوّرة',
				'reel_ph'      => '[مشغّل الفيديو مع الصوت وأزرار التحكّم — الفيديو يُزوَّد لاحقاً]',
				'reel_close'   => 'إغلاق',
				'reel_aria'    => 'أعمالنا المصوّرة',
				'dot'          => 'البداية',
			)
		),
	);
	foreach ( $svc as $i => $sv ) {
		$dark = 1 === $i % 2;
		$w[]  = md_w(
			'md-home-service',
			array(
				'label'    => $sv[0],
				'title'    => $sv[1],
				'lines'    => $sv[2],
				'ph'       => $sv[3],
				'feat_l'   => 'مشروع مختار',
				'feat'     => '[المشروع / العميل]',
				'logo_ph'  => '[شعار العميل]',
				'btn'      => 'اطّلعوا على دراسة الحالة',
				'lnk'      => 'تعرّفوا إلى الخدمة',
				'tone'     => $dark ? 'dark' : 'light',
				'side'     => $dark ? 'end' : 'start',
				'dot'      => $sv[1],
				'lnk_link' => array( 'url' => md_service_path( $i ) ),
				'btn_link' => array( 'url' => '/portfolio/case-study/' ),
			)
		);
	}
	$w[] = md_w(
		'md-home-about',
		array(
			'eyebrow'  => 'عن البيت',
			'headline' => 'بيت متخصّص لعلامات *لديها ما تقوله.*',
			'text'     => 'ميزون ديجيتال فريق صغير من أصحاب الخبرة في لبنان، يخطّط ويكتب ويصمّم ويصوّر ويؤتمت للعلامات. بيت واحد لكل الحِرف، بالعربية والإنجليزية.',
			'stats'    => array(
				array( 'value' => '[٠٠]', 'label' => 'سنوات الخبرة' ),
				array( 'value' => '[٠٠]', 'label' => 'علامات خدمناها' ),
				array( 'value' => '[٠٠]', 'label' => 'جوائز' ),
			),
			'btn'      => 'من نحن',
			'dot'      => 'عن البيت',
		)
	);
	$w[] = md_w(
		'md-home-audit',
		array(
			'eyebrow'  => 'تدقيق مجاني لعلامتكم',
			'headline' => 'لستم متأكّدين من موقع علامتكم؟ اكتشفوه مجاناً.',
			'text'     => 'أجيبوا عن اثني عشر سؤالاً واحصلوا على تقييم علامتكم في ثلاث دقائق: الهوية، وسائل التواصل، الموقع والمحتوى.',
			'btn'      => 'اطلبوا التدقيق المجاني',
			'dot'      => 'تدقيق مجاني',
		)
	);
	$w[] = md_w(
		'md-home-closing',
		array(
			'headline' => 'لنعمل *معاً.*',
			'text'     => 'أعجبكم ما رأيتم؟ حدّثونا عن مشروعكم.',
			'btn'      => 'ابدأوا مشروعاً',
			'dot'      => 'لنعمل معاً',
		)
	);
	return $w;
}

/**
 * A service page (Service.dc.html), filled for service $i.
 *
 * @param int $i Index 0–8.
 * @return array
 */
function md_demo_service( $i ) {
	$all   = md_services();
	$sv    = $all[ $i ];
	$num   = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT );
	$ni    = ( $i + 1 ) % count( $all );
	$words = array( 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight' );
	$count = count( $sv['included'] );

	if ( 0 === $i ) {
		$what  = 'We plan and run the marketing that grows your business: the strategy behind it, the campaigns that carry it and the reporting that proves it. Every plan starts from a number we agree to move.';
		$cards = array(
			array( 'title' => 'Marketing strategy', 'text' => 'Audience, positioning, channels and targets, written down and agreed.' ),
			array( 'title' => 'Paid campaigns', 'text' => 'Search and social advertising, planned, launched and managed.' ),
			array( 'title' => 'Search engine optimisation', 'text' => 'Technical fixes, content and structure that help people find you.' ),
			array( 'title' => 'Email marketing', 'text' => 'Newsletters and automated sequences that keep clients close.' ),
			array( 'title' => 'Analytics and reporting', 'text' => 'A clear monthly report on what moved, what did not, and what is next.' ),
		);
		$steps = array(
			array( 'step' => 'Step 1', 'title' => 'Audit', 'text' => 'We review your current marketing, competitors and numbers.' ),
			array( 'step' => 'Step 2', 'title' => 'Plan', 'text' => 'We set the target, the channels, the budget and the calendar.' ),
			array( 'step' => 'Step 3', 'title' => 'Launch', 'text' => 'We build and run the campaigns, with creative made in-house.' ),
			array( 'step' => 'Step 4', 'title' => 'Optimise', 'text' => 'We report monthly and move budget to what works.' ),
		);
		$faq   = array(
			array( 'q' => 'How soon will I see results?', 'a' => '[Answer to be supplied.]', 'open' => 'yes' ),
			array( 'q' => 'What budget do I need for paid campaigns?', 'a' => '[Answer to be supplied.]', 'open' => '' ),
			array( 'q' => 'Which platforms do you advertise on?', 'a' => '[Answer to be supplied.]', 'open' => '' ),
			array( 'q' => 'How do you report on performance?', 'a' => '[Answer to be supplied.]', 'open' => '' ),
		);
	} else {
		$what  = '[One short paragraph on what ' . $sv['name'] . ' covers — to be supplied.]';
		$cards = array();
		foreach ( $sv['included'] as $inc ) {
			$cards[] = array( 'title' => $inc, 'text' => '[One line on this deliverable — to be supplied.]' );
		}
		$steps = array();
		for ( $k = 1; $k <= 4; $k++ ) {
			$steps[] = array( 'step' => 'Step ' . $k, 'title' => '[Step]', 'text' => '[One line on this step — to be supplied.]' );
		}
		$faq = array();
		for ( $k = 1; $k <= 4; $k++ ) {
			$faq[] = array( 'q' => '[Question about ' . $sv['name'] . ' — to be supplied]', 'a' => '[Answer to be supplied.]', 'open' => 1 === $k ? 'yes' : '' );
		}
	}

	return array(
		md_w( 'md-dark-hero', array( 'label' => 'Service ' . $num, 'title' => $sv['name'], 'promise' => $sv['promise'], 'variant' => 'service' ) ),
		md_w(
			'md-split-text',
			array(
				'label' => 'What it is',
				'title' => '',
				'quote' => 'yes',
				'paras' => array( array( 'text' => $what, 'style' => 'xl' ) ),
			)
		),
		md_w(
			'md-cards',
			array(
				'label' => 'What’s included',
				'title' => ( isset( $words[ $count ] ) ? $words[ $count ] : $count ) . ' parts, one plan',
				'cards' => $cards,
			)
		),
		md_w(
			'md-steps',
			array(
				'label' => 'Our process',
				'title' => 'How it runs',
				'steps' => $steps,
				'tsize' => 'm',
			)
		),
		md_w( 'md-work-cards' ),
		md_w( 'md-accordion', array( 'items' => $faq ) ),
		md_w(
			'md-cta-band',
			array(
				'heading'      => 'Ready when you are.',
				'text'         => '',
				'heading_size' => 'm',
			)
		),
		md_w(
			'md-next-link',
			array(
				'label' => 'Next service — ' . str_pad( (string) ( $ni + 1 ), 2, '0', STR_PAD_LEFT ),
				'title' => $all[ $ni ]['name'],
				'link'  => array( 'url' => md_service_path( $ni ) ),
			)
		),
	);
}

/**
 * Header/footer templates in Arabic (Nav and Footer with lang="ar").
 *
 * @return array [ header widgets, footer widgets ]
 */
function md_demo_ar_parts() {
	$names = array( 'التسويق الرقمي', 'إدارة وسائل التواصل', 'صناعة المحتوى', 'خدمات التصميم', 'الإنتاج والتصوير', 'كتابة النصوص الإعلانية', 'كتابة السيناريو والأعمال السينمائية', 'بناء أنظمة إدارة العملاء', 'الأتمتة بالذكاء الاصطناعي' );
	$short = array( 'التسويق الرقمي', 'وسائل التواصل', 'صناعة المحتوى', 'التصميم', 'الإنتاج', 'النصوص الإعلانية', 'السيناريو', 'إدارة العملاء', 'الأتمتة الذكية' );
	$fnames = $names;
	$fnames[6] = 'كتابة السيناريو';
	$svc  = array();
	$fsvc = array();
	foreach ( $names as $i => $n ) {
		$svc[]  = array( 'name' => $n, 'short' => $short[ $i ], 'link' => array( 'url' => md_service_path( $i ) ) );
		$fsvc[] = array( 'label' => $fnames[ $i ], 'link' => array( 'url' => md_service_path( $i ) ) );
	}
	$nav = md_w(
		'md-nav',
		array(
			'home_link'   => array( 'url' => '/ar/' ),
			'logo_label'  => 'ميزون ديجيتال، الرئيسية',
			'items'       => array(
				array( 'label' => 'الرئيسية', 'link' => array( 'url' => '/ar/' ), 'kind' => 'link' ),
				array( 'label' => 'من نحن', 'link' => array( 'url' => '/about/' ), 'kind' => 'link' ),
				array( 'label' => 'خدماتنا', 'link' => array( 'url' => '/services/' ), 'kind' => 'services' ),
				array( 'label' => 'أعمالنا', 'link' => array( 'url' => '/portfolio/' ), 'kind' => 'link' ),
				array( 'label' => 'المدوّنة', 'link' => array( 'url' => '/blog/' ), 'kind' => 'link' ),
				array( 'label' => 'أسئلة شائعة', 'link' => array( 'url' => '/faq/' ), 'kind' => 'link' ),
				array( 'label' => 'تواصل معنا', 'link' => array( 'url' => '/contact/' ), 'kind' => 'link' ),
			),
			'dd_title'    => 'تحت سقف واحد',
			'dd_all'      => 'كل الخدمات',
			'services'    => $svc,
			'cta'         => 'تدقيق مجاني لعلامتك',
			'work'        => 'لنعمل معاً',
			'open_l'      => 'افتح القائمة',
			'close_l'     => 'أغلق القائمة',
			'main_l'      => 'القائمة الرئيسية',
		)
	);
	$footer = md_w(
		'md-footer',
		array(
			'home_link'  => array( 'url' => '/ar/' ),
			'desc'       => 'وكالة إبداعية متخصّصة في لبنان. السرد والتصميم والتقنية تحت سقف واحد.',
			't_company'  => 'الشركة',
			'company'    => array(
				array( 'label' => 'من نحن', 'link' => array( 'url' => '/about/' ) ),
				array( 'label' => 'أعمالنا', 'link' => array( 'url' => '/portfolio/' ) ),
				array( 'label' => 'المدوّنة', 'link' => array( 'url' => '/blog/' ) ),
				array( 'label' => 'أسئلة شائعة', 'link' => array( 'url' => '/faq/' ) ),
			),
			't_services' => 'الخدمات',
			'services'   => $fsvc,
			't_start'    => 'ابدأ معنا',
			'start'      => array(
				array( 'label' => 'تدقيق مجاني لعلامتك', 'link' => array( 'url' => '/free-brand-audit/' ) ),
				array( 'label' => 'لنعمل معاً', 'link' => array( 'url' => '/lets-work-together/' ) ),
				array( 'label' => 'تواصل معنا', 'link' => array( 'url' => '/contact/' ) ),
			),
			't_reach'    => 'تواصل',
			'copy'       => '© {year} Maison Digitale. جميع الحقوق محفوظة.',
		)
	);
	return array( array( $nav ), array( $footer ) );
}

/**
 * Sample blog notes so the Blog page and the article template show straight away.
 */
function md_demo_posts() {
	$body  = '<!-- wp:paragraph --><p>[Opening paragraph: the point of the article in two or three sentences, set slightly larger than the body.]</p><!-- /wp:paragraph -->';
	$body .= '<!-- wp:paragraph --><p>[Body paragraph. The text column stays narrow, about 65 characters per line, so that long articles remain comfortable to read on every screen.]</p><!-- /wp:paragraph -->';
	$body .= '<!-- wp:heading --><h2 class="wp-block-heading">[Section heading]</h2><!-- /wp:heading -->';
	$body .= '<!-- wp:paragraph --><p>[Body paragraph.]</p><!-- /wp:paragraph -->';
	$body .= '<!-- wp:paragraph --><p>[Body paragraph.]</p><!-- /wp:paragraph -->';
	$body .= '<!-- wp:quote --><blockquote class="wp-block-quote"><!-- wp:paragraph --><p>[Pull quote: the one sentence a reader should remember]</p><!-- /wp:paragraph --></blockquote><!-- /wp:quote -->';
	$body .= '<!-- wp:paragraph --><p>[Body paragraph.]</p><!-- /wp:paragraph -->';
	$body .= '<!-- wp:html --><div class="ph rv" style="aspect-ratio: 16 / 10">[Inline image]</div><!-- /wp:html -->';
	$body .= '<!-- wp:html --><p style="margin: 0; font-size: 14px; line-height: 20px; color: #62574d">[Image caption]</p><!-- /wp:html -->';
	$body .= '<!-- wp:heading --><h2 class="wp-block-heading">[Section heading]</h2><!-- /wp:heading -->';
	$body .= '<!-- wp:paragraph --><p>[Body paragraph.]</p><!-- /wp:paragraph -->';
	$body .= '<!-- wp:paragraph --><p>[Closing paragraph.]</p><!-- /wp:paragraph -->';

	$cat_ids = array();
	foreach ( array( 'Marketing', 'Social Media', 'Content', 'Design', 'Production', 'Writing', 'CRM', 'AI' ) as $c ) {
		$term = term_exists( $c, 'category' );
		if ( ! $term ) {
			$term = wp_insert_term( $c, 'category' );
		}
		if ( ! is_wp_error( $term ) ) {
			$cat_ids[ $c ] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
		}
	}

	$list = array( 'CRM', 'Content', 'Production', 'Social Media', 'Writing', 'AI', 'Design', 'Marketing' );
	$time = time() - DAY_IN_SECONDS * 30;
	foreach ( $list as $k => $c ) {
		wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_title'    => '[Article title]',
				'post_excerpt'  => '[One-sentence summary of the article.]',
				'post_content'  => $body,
				'post_category' => isset( $cat_ids[ $c ] ) ? array( $cat_ids[ $c ] ) : array(),
				'post_date'     => gmdate( 'Y-m-d H:i:s', $time + $k * DAY_IN_SECONDS * 3 ),
			)
		);
	}
	$featured = wp_insert_post(
		array(
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_title'    => '[Featured article title, up to three lines]',
			'post_excerpt'  => '[One-sentence summary of the article.]',
			'post_content'  => $body,
			'post_category' => isset( $cat_ids['Marketing'] ) ? array( $cat_ids['Marketing'] ) : array(),
		)
	);
	if ( $featured ) {
		stick_post( $featured );
	}
}

/* -------------------------------------------------------------------------
 * The import
 * ---------------------------------------------------------------------- */

function md_run_demo_import() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return new WP_Error( 'md_no_elementor', __( 'Elementor is not active.', 'maison-digitale' ) );
	}

	// Pretty permalinks so the menu links (/about/, /services/…) work.
	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}

	// Elementor: leave colours and fonts to the theme.
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );

	// Site parts.
	$header     = md_make_template( 'Site Header', array( md_w( 'md-nav' ) ) );
	$footer     = md_make_template( 'Site Footer', array( md_w( 'md-footer' ) ) );
	$header_min = md_make_template( 'Minimal Header (brand audit)', array( md_w( 'md-mini-header' ) ) );
	$footer_min = md_make_template( 'Minimal Footer (brand audit)', array( md_w( 'md-mini-footer' ) ) );
	$cta        = md_make_template(
		'Article Call to Action',
		array(
			md_w(
				'md-cta-band',
				array(
					'heading'       => 'Want this applied to your brand? Start with a free audit.',
					'text'          => '',
					'heading_size'  => 'm',
					'heading_width' => '13em',
					'buttons'       => array( array( 'label' => 'Get your free brand audit', 'link' => array( 'url' => '/free-brand-audit/' ), 'style' => 'b-ink' ) ),
				)
			),
		)
	);
	$ar_parts  = md_demo_ar_parts();
	$header_ar = md_make_template( 'Site Header (Arabic)', $ar_parts[0] );
	$footer_ar = md_make_template( 'Site Footer (Arabic)', $ar_parts[1] );

	$opts = get_option( 'md_options', array() );
	$opts = is_array( $opts ) ? $opts : array();
	$opts['header_tpl']      = $header;
	$opts['footer_tpl']      = $footer;
	$opts['header_min_tpl']  = $header_min;
	$opts['footer_min_tpl']  = $footer_min;
	$opts['article_cta_tpl'] = $cta;
	update_option( 'md_options', $opts );

	// Home.
	$home = md_make_page( 'Home', 'home' );
	md_save_elementor( $home, md_demo_home(), array( 'md_nav' => 'home', 'md_footer' => 'none' ) );

	// Arabic home.
	$ar = md_make_page( 'الرئيسية', 'ar' );
	md_save_elementor(
		$ar,
		md_demo_home_ar(),
		array(
			'md_nav'        => 'home',
			'md_footer'     => 'none',
			'md_dir'        => 'rtl',
			'md_header_tpl' => (string) $header_ar,
			'md_footer_tpl' => (string) $footer_ar,
		)
	);

	// About.
	$about = md_make_page( 'About us', 'about' );
	md_save_elementor(
		$about,
		array(
			md_w( 'md-page-hero' ),
			md_w( 'md-image-band' ),
			md_w( 'md-split-text' ),
			md_w( 'md-statements' ),
			md_w( 'md-steps' ),
			md_w( 'md-team' ),
			md_w( 'md-recognition' ),
			md_w( 'md-cta-band' ),
		),
		array( 'md_nav' => 'light' )
	);

	// Services overview.
	$services = md_make_page( 'Services', 'services' );
	md_save_elementor(
		$services,
		array(
			md_w(
				'md-page-hero',
				array(
					'eyebrow'  => 'Services',
					'headline' => 'Everything your brand needs, under one roof.',
					'lead'     => 'Nine services, one senior team. Take one, or let them work together.',
					'size'     => 'm',
					'max'      => '12em',
				)
			),
			md_w( 'md-service-index' ),
			md_w( 'md-service-groups' ),
			md_w(
				'md-cta-band',
				array(
					'heading'       => 'Not sure where to start? Start with a free brand audit.',
					'text'          => '',
					'heading_size'  => 'm',
					'heading_width' => '13em',
					'buttons'       => array( array( 'label' => 'Get your free brand audit', 'link' => array( 'url' => '/free-brand-audit/' ), 'style' => 'b-ink' ) ),
				)
			),
		),
		array( 'md_nav' => 'light' )
	);

	// The nine service pages.
	foreach ( md_services() as $i => $sv ) {
		$pid = md_make_page( $sv['name'], $sv['slug'], $services );
		md_save_elementor( $pid, md_demo_service( $i ), array( 'md_nav' => 'overlay' ) );
	}

	// Portfolio.
	$portfolio = md_make_page( 'Portfolio', 'portfolio' );
	md_save_elementor(
		$portfolio,
		array(
			md_w(
				'md-page-hero',
				array(
					'eyebrow'  => 'Portfolio',
					'headline' => 'Selected work.',
					'lead'     => '',
					'size'     => 'xl',
					'pbv'      => 'normal',
				)
			),
			md_w( 'md-portfolio' ),
			md_w(
				'md-cta-band',
				array(
					'text'    => 'Your project could be the next one here.',
					'buttons' => array( array( 'label' => 'Start a project', 'link' => array( 'url' => '/lets-work-together/' ), 'style' => 'b-ink' ) ),
				)
			),
		),
		array( 'md_nav' => 'light' )
	);

	// Case study template.
	$case = md_make_page( '[Project name]', 'case-study', $portfolio );
	md_save_elementor(
		$case,
		array(
			md_w(
				'md-dark-hero',
				array(
					'label'   => 'Case study',
					'title'   => '[Project name]',
					'promise' => '',
					'ph'      => '[Hero image or video of the project]',
					'variant' => 'case',
				)
			),
			md_w( 'md-meta-row' ),
			md_w(
				'md-split-text',
				array(
					'label' => '01',
					'title' => 'The brief',
					'paras' => array( array( 'text' => '[What the client asked for and why: the situation, the audience and the goal, in three or four sentences.]', 'style' => 'lead' ) ),
					'pb'    => 'none',
				)
			),
			md_w(
				'md-split-text',
				array(
					'label' => '02',
					'title' => 'The approach',
					'paras' => array( array( 'text' => '[The idea behind the work and how the team went about it: the story, the channels and the decisions that mattered.]', 'style' => 'lead' ) ),
					'pt'    => 'medium',
				)
			),
			md_w( 'md-gallery' ),
			md_w( 'md-results' ),
			md_w( 'md-quote' ),
			md_w(
				'md-next-link',
				array(
					'label' => 'Next project',
					'title' => '[Next project name]',
					'link'  => array( 'url' => '/portfolio/case-study/' ),
				)
			),
		),
		array( 'md_nav' => 'overlay' )
	);

	// Blog.
	$blog = md_make_page( 'Blog', 'blog' );
	md_save_elementor(
		$blog,
		array(
			md_w(
				'md-page-hero',
				array(
					'eyebrow'  => 'Blog',
					'headline' => 'Notes from the house.',
					'lead'     => '',
					'pbv'      => 'normal',
				)
			),
			md_w( 'md-blog' ),
			md_w( 'md-newsletter' ),
		),
		array( 'md_nav' => 'light' )
	);
	update_option( 'md_blog_page', $blog );

	// FAQ.
	$faq_groups = array(
		array( 'Working with us', array( 'What kind of businesses do you work with?', 'Do you work with clients outside Lebanon?', 'Do you work in both English and Arabic?', 'How does a project start?' ) ),
		array( 'Services', array( 'Can I hire you for a single service?', 'Do you offer monthly retainers?', 'Do you handle production in-house?', 'What does AI automation actually involve for a business like mine?' ) ),
		array( 'Pricing and timelines', array( 'How do you price your work?', 'How long does a typical project take?', 'What do you need from me to get started?' ) ),
		array( 'Free brand audit', array( 'What does the free brand audit include?', 'Is it really free, and is there any commitment?', 'What is the difference between the free score and the detailed audit?' ) ),
	);
	$faq_w = array(
		md_w(
			'md-page-hero',
			array(
				'eyebrow'  => 'FAQ',
				'headline' => 'Questions, answered.',
				'lead'     => '',
				'pbv'      => 'normal',
			)
		),
	);
	foreach ( $faq_groups as $gi => $g ) {
		$items = array();
		foreach ( $g[1] as $qi => $q ) {
			$items[] = array( 'q' => $q, 'a' => '[Answer to be supplied.]', 'open' => ( 0 === $gi && 0 === $qi ) ? 'yes' : '' );
		}
		$faq_w[] = md_w(
			'md-accordion',
			array(
				'label'   => str_pad( (string) ( $gi + 1 ), 2, '0', STR_PAD_LEFT ),
				'title'   => $g[0],
				'items'   => $items,
				'variant' => 'faq',
				'tone'    => 'ground',
				'pb'      => count( $faq_groups ) - 1 === $gi ? 'large' : '',
			)
		);
	}
	$faq_w[] = md_w(
		'md-cta-band',
		array(
			'tone'         => 'dark',
			'heading'      => 'Still have a question?',
			'text'         => '',
			'heading_size' => 'm',
			'buttons'      => array( array( 'label' => 'Contact us', 'link' => array( 'url' => '/contact/' ), 'style' => 'b-gold' ) ),
		)
	);
	$faq = md_make_page( 'FAQ', 'faq' );
	md_save_elementor( $faq, $faq_w, array( 'md_nav' => 'light' ) );

	// Contact.
	$contact = md_make_page( 'Contact us', 'contact' );
	md_save_elementor(
		$contact,
		array(
			md_w(
				'md-page-hero',
				array(
					'eyebrow'  => 'Contact us',
					'headline' => 'Say hello.',
					'lead'     => '',
					'size'     => 'xxl',
					'pbv'      => 'normal',
				)
			),
			md_w( 'md-contact' ),
			md_w(
				'md-image-band',
				array(
					'h'  => 'map',
					'ph' => '[Map or location photograph — address to be supplied]',
				)
			),
			md_w( 'md-route-cards' ),
		),
		array( 'md_nav' => 'light' )
	);

	// Free brand audit.
	$audit = md_make_page( 'Your Free Brand Audit', 'free-brand-audit' );
	md_save_elementor( $audit, array( md_w( 'md-brand-audit' ) ), array( 'md_nav' => 'minimal', 'md_footer' => 'minimal' ) );

	// Let's work together.
	$work = md_make_page( "Let's Work Together", 'lets-work-together' );
	md_save_elementor(
		$work,
		array(
			md_w(
				'md-page-hero',
				array(
					'tone'     => 'dark',
					'eyebrow'  => 'Project enquiry',
					'headline' => 'Let’s work *together.*',
					'lead'     => 'Tell us what you’re building. We’ll come back with ideas.',
					'pbv'      => 'normal',
				)
			),
			md_w( 'md-enquiry' ),
			md_w(
				'md-steps',
				array(
					'label' => 'What happens next',
					'title' => 'Three steps',
					'steps' => array(
						array( 'step' => 'Step 1', 'title' => 'We read your brief', 'text' => 'A senior member of the team, not a sales desk.' ),
						array( 'step' => 'Step 2', 'title' => 'We schedule a call', 'text' => 'Thirty minutes to ask questions and share first thoughts.' ),
						array( 'step' => 'Step 3', 'title' => 'You receive a proposal', 'text' => 'Scope, timeline and cost, in plain language.' ),
					),
					'tsize' => 's',
					'col'   => '260',
					'tone'  => 'ground',
				)
			),
		),
		array( 'md_nav' => 'dark', 'md_bg' => 'ink' )
	);

	md_demo_posts();

	// Remove WordPress's untouched sample content so it does not show in the blog or menus.
	foreach ( array( get_post( 1 ), get_page_by_path( 'sample-page' ) ) as $sample ) {
		if ( $sample && in_array( $sample->post_name, array( 'hello-world', 'sample-page' ), true ) && $sample->post_modified === $sample->post_date ) {
			wp_trash_post( $sample->ID );
		}
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );
	update_option( 'md_demo_imported', time() );

	flush_rewrite_rules();
	if ( isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	return true;
}

/**
 * Admin action behind the "Import demo pages" button.
 */
function md_handle_import() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'maison-digitale' ) );
	}
	check_admin_referer( 'md_import_demo' );
	$result = md_run_demo_import();
	if ( is_wp_error( $result ) ) {
		wp_die( esc_html( $result->get_error_message() ) );
	}
	wp_safe_redirect( admin_url( 'themes.php?page=maison-digitale&md_imported=1' ) );
	exit;
}
add_action( 'admin_post_md_import_demo', 'md_handle_import' );

/**
 * After switching to the theme, send the admin to the set-up page.
 */
function md_after_switch() {
	if ( ! get_option( 'md_demo_imported' ) ) {
		set_transient( 'md_redirect_setup', 1, 60 );
	}
}
add_action( 'after_switch_theme', 'md_after_switch' );

function md_maybe_redirect_setup() {
	if ( get_transient( 'md_redirect_setup' ) && current_user_can( 'edit_theme_options' ) && ! wp_doing_ajax() ) {
		delete_transient( 'md_redirect_setup' );
		wp_safe_redirect( admin_url( 'themes.php?page=maison-digitale' ) );
		exit;
	}
}
add_action( 'admin_init', 'md_maybe_redirect_setup' );
