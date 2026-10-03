<?php
/**
 * Interactive sections: Portfolio filters, Blog, Brand audit questionnaire,
 * Let's work together multi-step form. The logic is ported from the script
 * blocks of the design files and runs in assets/js/maison.js.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * Portfolio — Portfolio.dc.html
 * ====================================================================== */
class MD_W_Portfolio extends MD_Widget {
	protected $md_icon = 'eicon-gallery-justified';

	public function get_name() {
		return 'md-portfolio';
	}

	public function get_title() {
		return __( 'Portfolio grid with filters', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'f', __( 'Filters', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'switch', 'filters', __( 'Show filters', 'maison-digitale' ), 'yes' ),
				array( 'text', 'all', __( '"All" label', 'maison-digitale' ), 'All' ),
				array( 'text', 'svc_l', __( 'Service filter label', 'maison-digitale' ), 'By service' ),
				array( 'area', 'svc', __( 'Services (one per line)', 'maison-digitale' ), "Digital Marketing\nSocial Media\nContent\nDesign\nProduction\nCopywriting\nScriptwriting\nCRM\nAI Automation", array( 'rows' => 9 ) ),
				array( 'text', 'ind_l', __( 'Industry filter label', 'maison-digitale' ), 'By industry' ),
				array( 'area', 'ind', __( 'Industries (one per line)', 'maison-digitale' ), "[Industry 1]\n[Industry 2]\n[Industry 3]\n[Industry 4]", array( 'rows' => 4 ) ),
				array( 'text', 'empty', __( 'No results text', 'maison-digitale' ), 'No projects under this filter yet.' ),
				array( 'text', 'reset', __( 'Reset button', 'maison-digitale' ), 'Show all work' ),
			)
		);
		$this->end();

		$this->sec( 'p', __( 'Projects', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'note',
					'n',
					__( 'Tag each project with services and an industry spelled exactly as in the filter lists. Width, proportions and offset build the uneven editorial grid; when a filter is on, every card switches to the same size.', 'maison-digitale' ),
				),
				array(
					'rep',
					'projects',
					__( 'Projects', 'maison-digitale' ),
					$this->rows(
						array( 'name', 'client', 'services', 'industry', 'col', 'ratio', 'mt', 'link' ),
						array(
							array( '[Project name]', '[Client]', 'Design, Copywriting', '[Industry 1]', '7', '16 / 10', '', array( 'url' => '/portfolio/case-study/' ) ),
							array( '[Project name]', '[Client]', 'Production, Scriptwriting', '[Industry 2]', '5', '4 / 5', '', array( 'url' => '/portfolio/case-study/' ) ),
							array( '[Project name]', '[Client]', 'Social Media, Content', '[Industry 3]', '5', '1 / 1', '', array( 'url' => '/portfolio/case-study/' ) ),
							array( '[Project name]', '[Client]', 'Digital Marketing', '[Industry 1]', '7', '16 / 10', 'l', array( 'url' => '/portfolio/case-study/' ) ),
							array( '[Project name]', '[Client]', 'CRM, AI Automation', '[Industry 4]', '4', '4 / 5', '', array( 'url' => '/portfolio/case-study/' ) ),
							array( '[Project name]', '[Client]', 'Content, Production', '[Industry 2]', '4', '4 / 5', 'm', array( 'url' => '/portfolio/case-study/' ) ),
							array( '[Project name]', '[Client]', 'Design', '[Industry 3]', '4', '4 / 5', '', array( 'url' => '/portfolio/case-study/' ) ),
						)
					),
					array(
						'title'  => '{{{ name }}} — {{{ client }}}',
						'fields' => array(
							array( 'text', 'name', __( 'Project name', 'maison-digitale' ), '' ),
							array( 'text', 'client', __( 'Client', 'maison-digitale' ), '' ),
							array( 'text', 'services', __( 'Services (comma separated)', 'maison-digitale' ), '' ),
							array( 'text', 'industry', __( 'Industry', 'maison-digitale' ), '' ),
							array( 'media', 'image', __( 'Image or short video', 'maison-digitale' ), '', array( 'media_types' => array( 'image', 'video' ) ) ),
							array( 'url', 'link', __( 'Case study link', 'maison-digitale' ), '' ),
							array(
								'select',
								'col',
								__( 'Width (of 12 columns)', 'maison-digitale' ),
								'6',
								array( 'options' => array( '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '12' => '12' ) ),
							),
							array(
								'select',
								'ratio',
								__( 'Proportions', 'maison-digitale' ),
								'4 / 3',
								array(
									'options' => array(
										'16 / 10' => '16:10',
										'4 / 5'   => '4:5',
										'1 / 1'   => '1:1',
										'4 / 3'   => '4:3',
										'16 / 9'  => '16:9',
									),
								),
							),
							array(
								'select',
								'mt',
								__( 'Drop (offset from the top)', 'maison-digitale' ),
								'',
								array(
									'options' => array(
										''  => __( 'None', 'maison-digitale' ),
										'm' => __( 'Medium', 'maison-digitale' ),
										'l' => __( 'Large', 'maison-digitale' ),
									),
								),
							),
						),
					),
				),
				array( 'text', 'ph', __( 'Image placeholder', 'maison-digitale' ), '[Project image; short motion preview on hover]' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$svc  = md_lines( $s['svc'] );
		$ind  = md_lines( $s['ind'] );
		$mts  = array(
			''  => '0',
			'm' => 'clamp(0px, 4cqw, 64px)',
			'l' => 'clamp(0px, 5cqw, 80px)',
		);
		?>
		<div data-md-portfolio>
			<?php if ( 'yes' === $s['filters'] ) : ?>
				<section style="padding: 0 clamp(20px, 5cqw, 80px) clamp(32px, 4cqw, 56px)">
					<div class="in" style="display: flex; flex-direction: column; gap: 20px; border-top: 1px solid #17120f; padding-top: 24px">
						<?php foreach ( array( 'svc' => array( $s['svc_l'], $svc ), 'ind' => array( $s['ind_l'], $ind ) ) as $key => $group ) : ?>
							<?php
							if ( ! $group[1] ) {
								continue;
							}
							?>
							<div style="display: flex; flex-wrap: wrap; gap: 12px 24px; align-items: baseline">
								<p class="ov" style="color: #62574d; width: 90px"><?php echo esc_html( $group[0] ); ?></p>
								<div style="display: flex; flex-wrap: wrap; gap: 8px; flex: 1 1 300px" data-md-filter="<?php echo esc_attr( $key ); ?>">
									<button type="button" class="chip on" aria-pressed="true" data-v=""><?php echo esc_html( $s['all'] ); ?></button>
									<?php foreach ( $group[1] as $label ) : ?>
										<button type="button" class="chip" aria-pressed="false" data-v="<?php echo esc_attr( $label ); ?>"><?php echo esc_html( $label ); ?></button>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>
			<section style="padding: 0 clamp(20px, 5cqw, 80px) clamp(56px, 8cqw, 120px)">
				<div class="in">
					<div class="pgrid">
						<?php foreach ( (array) $s['projects'] as $p ) : ?>
							<?php
							$tags  = array_values( array_filter( array_map( 'trim', explode( ',', (string) $p['services'] ) ), 'strlen' ) );
							$col   = 'span ' . (int) $p['col'];
							$ratio = $p['ratio'] ? $p['ratio'] : '4 / 3';
							$mt    = isset( $mts[ $p['mt'] ] ) ? $mts[ $p['mt'] ] : '0';
							?>
							<a class="card rv" <?php echo md_href( $p['link'] ); // phpcs:ignore ?> style="grid-column: <?php echo esc_attr( $col ); ?>; margin-top: <?php echo esc_attr( $mt ); ?>" data-svc="<?php echo esc_attr( wp_json_encode( $tags ) ); ?>" data-ind="<?php echo esc_attr( $p['industry'] ); ?>" data-col="<?php echo esc_attr( $col ); ?>" data-ratio="<?php echo esc_attr( $ratio ); ?>" data-mt="<?php echo esc_attr( $mt ); ?>">
								<div class="im" style="aspect-ratio: <?php echo esc_attr( $ratio ); ?>"><?php echo md_box( isset( $p['image'] ) ? $p['image'] : '', $s['ph'], '', '', 'div', $p['name'] ); // phpcs:ignore ?></div>
								<div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 4px 16px; margin-top: 14px">
									<p class="ov" style="color: #62574d"><?php echo esc_html( $p['client'] ); ?></p>
									<p class="ov" style="color: #62574d"><?php echo esc_html( implode( ' · ', $tags ) ); ?></p>
								</div>
								<p class="fh tt" style="font-size: clamp(24px, 2.6cqw, 36px); margin-top: 8px"><?php echo esc_html( $p['name'] ); ?></p>
							</a>
						<?php endforeach; ?>
					</div>
					<p class="md-empty" hidden style="margin: 0; padding: 48px 0; font-size: 20px; color: #62574d"><?php echo esc_html( $s['empty'] ); ?> <button type="button" class="chip" data-md-reset><?php echo esc_html( $s['reset'] ); ?></button></p>
				</div>
			</section>
		</div>
		<?php
	}
}

/* =========================================================================
 * Blog — Blog.dc.html (featured note, category filter, grid, load more)
 * ====================================================================== */
class MD_W_Blog extends MD_Widget {
	protected $md_icon = 'eicon-posts-masonry';

	public function get_name() {
		return 'md-blog';
	}

	public function get_title() {
		return __( 'Blog notes', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'note', 'n', __( 'Shows your blog posts (Posts → Add New). The newest sticky post is featured, otherwise the newest post. Category chips filter by the post\'s category name.', 'maison-digitale' ) ),
				array( 'switch', 'featured', __( 'Show the featured note', 'maison-digitale' ), 'yes' ),
				array( 'text', 'feat_l', __( 'Featured label', 'maison-digitale' ), 'Featured' ),
				array( 'text', 'all', __( '"All" label', 'maison-digitale' ), 'All' ),
				array( 'area', 'cats', __( 'Category chips (one per line)', 'maison-digitale' ), "Marketing\nSocial Media\nContent\nDesign\nProduction\nWriting\nCRM\nAI", array( 'rows' => 8 ) ),
				array( 'number', 'per', __( 'Notes shown at first', 'maison-digitale' ), 6 ),
				array( 'number', 'max', __( 'Maximum notes on the page', 'maison-digitale' ), 60 ),
				array( 'text', 'more', __( 'Load more button', 'maison-digitale' ), 'Load more articles' ),
				array( 'text', 'min', __( '"min read" label', 'maison-digitale' ), 'min read' ),
			)
		);
		$this->end();
	}

	/**
	 * Placeholder posts from the design, used until real posts exist.
	 *
	 * @return array
	 */
	private function placeholders() {
		$cats = array( 'Marketing', 'Design', 'AI', 'Writing', 'Social Media', 'Production', 'Content', 'CRM', 'Marketing' );
		$out  = array();
		foreach ( $cats as $c ) {
			$out[] = array( 'cat' => $c );
		}
		return $out;
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$max   = max( 1, (int) $s['max'] );
		$per   = max( 1, (int) $s['per'] );
		$posts = get_posts(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $max + 1,
				'ignore_sticky_posts' => true,
			)
		);
		$feat   = null;
		if ( 'yes' === $s['featured'] && $posts ) {
			$sticky = get_option( 'sticky_posts' );
			foreach ( $posts as $p ) {
				if ( $sticky && in_array( $p->ID, $sticky, true ) ) {
					$feat = $p;
					break;
				}
			}
			$feat  = $feat ? $feat : $posts[0];
			$posts = array_values( array_filter( $posts, function ( $p ) use ( $feat ) {
				return $p->ID !== $feat->ID;
			} ) );
		}
		$posts = array_slice( $posts, 0, $max );
		$cats  = md_lines( $s['cats'] );
		?>
		<?php if ( 'yes' === $s['featured'] ) : ?>
			<section style="padding: 0 clamp(20px, 5cqw, 80px) clamp(40px, 5cqw, 72px)">
				<?php if ( $feat ) : ?>
					<?php
					$thumb = get_post_thumbnail_id( $feat->ID );
					$media = $thumb ? array( 'id' => $thumb, 'url' => wp_get_attachment_image_url( $thumb, 'large' ) ) : '';
					?>
					<a class="card in rv" href="<?php echo esc_url( get_permalink( $feat ) ); ?>" style="display: flex; flex-wrap: wrap; gap: clamp(20px, 3cqw, 48px); align-items: flex-end; border-top: 1px solid #17120f; padding-top: 24px">
						<div class="im" style="flex: 1.5 1 380px; aspect-ratio: 16 / 10"><?php echo md_box( $media, '[Featured article cover image]', '', '', 'div', get_the_title( $feat ) ); // phpcs:ignore ?></div>
						<div style="flex: 1 1 300px; display: flex; flex-direction: column; gap: 14px">
							<p class="ov" style="color: #a67c2e"><?php echo esc_html( $s['feat_l'] . ' · ' . md_post_category( $feat->ID ) ); ?></p>
							<p class="fh tt" style="font-size: clamp(30px, 3.8cqw, 54px)"><?php echo esc_html( get_the_title( $feat ) ); ?></p>
							<p style="margin: 0; color: #62574d"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt( $feat ) ) ); ?></p>
							<p class="ov" style="color: #62574d"><?php echo esc_html( md_reading_time( $feat->ID ) . ' ' . $s['min'] . ' · ' . get_the_date( '', $feat ) ); ?></p>
						</div>
					</a>
				<?php else : ?>
					<a class="card in rv" href="#" style="display: flex; flex-wrap: wrap; gap: clamp(20px, 3cqw, 48px); align-items: flex-end; border-top: 1px solid #17120f; padding-top: 24px">
						<div class="im" style="flex: 1.5 1 380px; aspect-ratio: 16 / 10"><div class="ph">[Featured article cover image]</div></div>
						<div style="flex: 1 1 300px; display: flex; flex-direction: column; gap: 14px">
							<p class="ov" style="color: #a67c2e"><?php echo esc_html( $s['feat_l'] ); ?> · [Category]</p>
							<p class="fh tt" style="font-size: clamp(30px, 3.8cqw, 54px)">[Featured article title, up to three lines]</p>
							<p style="margin: 0; color: #62574d">[One-sentence summary of the article.]</p>
							<p class="ov" style="color: #62574d">[0] <?php echo esc_html( $s['min'] ); ?> · [Date]</p>
						</div>
					</a>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<section style="padding: 0 clamp(20px, 5cqw, 80px) clamp(56px, 8cqw, 120px)" data-md-blog data-per="<?php echo esc_attr( $per ); ?>">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(24px, 3cqw, 40px)">
				<?php if ( $cats ) : ?>
					<div style="display: flex; flex-wrap: wrap; gap: 8px; border-top: 1px solid #d8cdbc; padding-top: 24px" data-md-filter="cat">
						<button type="button" class="chip on" aria-pressed="true" data-v=""><?php echo esc_html( $s['all'] ); ?></button>
						<?php foreach ( $cats as $c ) : ?>
							<button type="button" class="chip" aria-pressed="false" data-v="<?php echo esc_attr( $c ); ?>"><?php echo esc_html( $c ); ?></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="bgrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 320px), 1fr)); gap: clamp(32px, 4cqw, 56px) clamp(16px, 2cqw, 28px)">
					<?php if ( $posts ) : ?>
						<?php
						foreach ( $posts as $p ) {
							$names = wp_list_pluck( get_the_category( $p->ID ), 'name' );
							md_post_card( $p->ID, 'clamp(22px, 2.1cqw, 30px)', 'data-cat="' . esc_attr( wp_json_encode( array_values( $names ) ) ) . '"' );
						}
						?>
					<?php else : ?>
						<?php foreach ( $this->placeholders() as $ph ) : ?>
							<a class="card rv" href="#" data-cat="<?php echo esc_attr( wp_json_encode( array( $ph['cat'] ) ) ); ?>">
								<div class="im" style="aspect-ratio: 4 / 3"><div class="ph">[Article image]</div></div>
								<p class="ov" style="color: #a67c2e; margin-top: 16px"><?php echo esc_html( $ph['cat'] ); ?></p>
								<p class="fh tt" style="font-size: clamp(22px, 2.1cqw, 30px); margin-top: 8px">[Article title]</p>
								<p class="ov" style="color: #62574d; margin-top: 12px">[0] <?php echo esc_html( $s['min'] ); ?> · [Date]</p>
							</a>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<?php if ( $s['more'] ) : ?>
					<div><button type="button" class="bt b-line" data-md-more><?php echo esc_html( $s['more'] ); ?></button></div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Brand audit — BrandAudit.dc.html
 * ====================================================================== */
class MD_W_Brand_Audit extends MD_Widget {
	protected $md_icon = 'eicon-checkbox';

	public function get_name() {
		return 'md-brand-audit';
	}

	public function get_title() {
		return __( 'Brand audit questionnaire', 'maison-digitale' );
	}

	protected function register_controls() {
		$area = function ( $i ) {
			return (string) $i;
		};
		$this->sec( 'intro', __( 'Start screen', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'kicker', __( 'Overline', 'maison-digitale' ), 'Free · 12 questions · 3 minutes' ),
				array( 'area', 'headline', __( 'Headline', 'maison-digitale' ), 'Your free brand audit.' ),
				array( 'area', 'lead', __( 'Intro', 'maison-digitale' ), 'Answer a short questionnaire and get your brand score straight away: one number out of 100, and where you stand in four areas.' ),
				array( 'text', 'start', __( 'Start button', 'maison-digitale' ), 'Start the questionnaire' ),
				array( 'text', 'areas_l', __( 'Areas: overline', 'maison-digitale' ), 'What we score' ),
				array( 'text', 'areas_t', __( 'Areas: heading', 'maison-digitale' ), 'Four areas, one score' ),
				array(
					'rep',
					'areas',
					__( 'The four areas', 'maison-digitale' ),
					$this->rows(
						array( 'title', 'text' ),
						array(
							array( 'Brand identity and consistency', 'Logo, colours, type and tone: do they hold together everywhere?' ),
							array( 'Social media presence', 'What you post, how often, and how your audience responds.' ),
							array( 'Website and search visibility', 'How easy you are to find, and how your site performs once found.' ),
							array( 'Content and messaging', 'Whether your words and visuals say what you mean them to.' ),
						)
					),
					array(
						'fields' => array(
							array( 'text', 'title', __( 'Area name', 'maison-digitale' ), '' ),
							array( 'area', 'text', __( 'Description', 'maison-digitale' ), '' ),
						),
					),
				),
				array( 'text', 'how_l', __( 'How it works: overline', 'maison-digitale' ), 'How it works' ),
				array( 'text', 'how_t', __( 'How it works: heading', 'maison-digitale' ), 'Three steps' ),
				array(
					'rep',
					'how',
					__( 'Steps', 'maison-digitale' ),
					$this->rows(
						array( 'step', 'title', 'text' ),
						array(
							array( 'Step 1', 'Answer 12 questions', 'Multiple choice, one per screen. No preparation needed.' ),
							array( 'Step 2', 'Get your score', 'Instantly, on screen: an overall score and one for each area.' ),
							array( 'Step 3', 'Go deeper, if you want', 'Order the detailed audit, or book a consultation with the team.' ),
						)
					),
					array(
						'title'  => '{{{ title }}}',
						'fields' => array(
							array( 'text', 'step', __( 'Step label', 'maison-digitale' ), '' ),
							array( 'text', 'title', __( 'Title', 'maison-digitale' ), '' ),
							array( 'area', 'text', __( 'Text', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->end();

		$q = array(
			array( '1', 'Do you have a defined logo, colour palette and typefaces?', "No\nJust a logo\nYes, loosely followed\nYes, documented in a brand book" ),
			array( '1', 'Does your brand look the same on your website, social pages and printed material?', "Not really\nIn some places\nMostly\nEverywhere" ),
			array( '1', 'Could everyone on your team describe what the brand stands for in one sentence?', "No\nEach would say something different\nRoughly\nYes, the same sentence" ),
			array( '2', 'How often do you post on your main social platform?', "Rarely\nA few times a month\nWeekly\nSeveral times a week, to a plan" ),
			array( '2', 'Do you plan your content ahead with a calendar?', "No\nSometimes\nA few weeks ahead\nYes, a month or more ahead" ),
			array( '2', 'How does your audience respond to what you post?', "Little or no response\nSome likes\nRegular comments and messages\nEnquiries and sales" ),
			array( '3', 'Do you have a website that works well on a phone?', "No website\nYes, but it is outdated\nYes, it works on mobile\nYes, fast and up to date" ),
			array( '3', 'When people search for what you offer, do you appear?', "I do not know\nNo\nOn later pages\nOn the first page" ),
			array( '3', 'Does your website turn visitors into enquiries?', "There is no way to enquire\nA contact form, rarely used\nSome enquiries\nA steady flow, and we track it" ),
			array( '4', 'Is it clear within five seconds what you offer, and for whom?', "No\nOnly to people who know us\nMostly\nYes, immediately" ),
			array( '4', 'Do you publish more than promotions: stories, advice, behind the scenes?', "No\nRarely\nSometimes\nRegularly" ),
			array( '4', 'Do you communicate in the languages your audience uses?', "One language, though they use two\nInconsistently\nMostly\nFully, in each language" ),
		);
		$this->sec( 'quiz', __( 'Questions', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'note', 'qn', __( 'Each question has four answers, from weakest (scores 0) to strongest (scores 3). Area scores and the total are out of 100.', 'maison-digitale' ) ),
				array(
					'rep',
					'questions',
					__( 'Questions', 'maison-digitale' ),
					$this->rows( array( 'area', 'q', 'opts' ), $q ),
					array(
						'title'  => '{{{ q }}}',
						'fields' => array(
							array(
								'select',
								'area',
								__( 'Area', 'maison-digitale' ),
								'1',
								array(
									'options' => array(
										'1' => __( 'Area 1', 'maison-digitale' ),
										'2' => __( 'Area 2', 'maison-digitale' ),
										'3' => __( 'Area 3', 'maison-digitale' ),
										'4' => __( 'Area 4', 'maison-digitale' ),
									),
								),
							),
							array( 'area', 'q', __( 'Question', 'maison-digitale' ), '' ),
							array( 'area', 'opts', __( 'Four answers, one per line, weakest first', 'maison-digitale' ), '', array( 'rows' => 4 ) ),
						),
					),
				),
				array( 'text', 'q_label', __( 'Progress label', 'maison-digitale' ), 'Question %1$s of %2$s', array( 'description' => __( '%1$s is the question number, %2$s the total.', 'maison-digitale' ) ) ),
				array( 'text', 'q_hint', __( 'Hint', 'maison-digitale' ), 'Choose the answer closest to your situation.' ),
				array( 'text', 'back', __( 'Back button', 'maison-digitale' ), 'Back' ),
			)
		);
		$this->end();

		$this->sec( 'details', __( 'Details screen', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'd_kicker', __( 'Overline', 'maison-digitale' ), 'Questionnaire complete' ),
				array( 'text', 'd_title', __( 'Heading', 'maison-digitale' ), 'Your score is ready. Who is it for?' ),
				array( 'text', 'f_name', __( 'Full name label', 'maison-digitale' ), 'Full name' ),
				array( 'text', 'f_email', __( 'Email label', 'maison-digitale' ), 'Email' ),
				array( 'text', 'f_biz', __( 'Business name label', 'maison-digitale' ), 'Business name' ),
				array( 'text', 'f_phone', __( 'Phone label', 'maison-digitale' ), 'Phone / WhatsApp' ),
				array( 'text', 'f_optional', __( '"Optional" note', 'maison-digitale' ), '(optional)' ),
				array( 'text', 'd_note', __( 'Note next to the button', 'maison-digitale' ), 'We also email you a copy of your score.' ),
				array( 'text', 'd_btn', __( 'Button', 'maison-digitale' ), 'Show my score' ),
			)
		);
		$this->end();

		$this->sec( 'result', __( 'Score screen', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'r_label', __( 'Overline', 'maison-digitale' ), 'Your brand score' ),
				array( 'text', 'r_of', __( '"/ 100"', 'maison-digitale' ), '/ 100' ),
				array( 'heading', 'h_bands', __( 'Result bands (total below 40, 40–69, 70 and above)', 'maison-digitale' ) ),
				array( 'text', 'b1_t', __( 'Low: title', 'maison-digitale' ), 'A brand that needs its foundation.' ),
				array( 'area', 'b1_x', __( 'Low: text', 'maison-digitale' ), 'The basics are not yet in place, which means the biggest gains are still ahead of you. Start with the lowest area below.' ),
				array( 'text', 'b2_t', __( 'Middle: title', 'maison-digitale' ), 'A solid start, with room to grow.' ),
				array( 'area', 'b2_x', __( 'Middle: text', 'maison-digitale' ), 'Parts of your brand are working. A few areas are holding the rest back, and fixing them will lift everything else.' ),
				array( 'text', 'b3_t', __( 'High: title', 'maison-digitale' ), 'A strong brand, ready to sharpen.' ),
				array( 'area', 'b3_x', __( 'High: text', 'maison-digitale' ), 'You have the foundations right. The next step is refinement: consistency, measurement and a clearer story.' ),
				array( 'heading', 'h_notes', __( 'Notes under each area score', 'maison-digitale' ) ),
				array( 'text', 'n1', __( 'Below 40', 'maison-digitale' ), 'Needs a foundation. This is where to start.' ),
				array( 'text', 'n2', __( '40–69', 'maison-digitale' ), 'Working, with clear room to grow.' ),
				array( 'text', 'n3', __( '70 and above', 'maison-digitale' ), 'A strength. Keep it consistent.' ),
				array( 'text', 'out_of', __( 'Screen reader text for a bar', 'maison-digitale' ), '%1$s: %2$s out of 100' ),
				array( 'heading', 'h_next', __( 'What next', 'maison-digitale' ) ),
				array( 'text', 'w_label', __( 'Overline', 'maison-digitale' ), 'What next' ),
				array( 'area', 'w_title', __( 'Heading', 'maison-digitale' ), 'The score tells you where. The audit tells you what to do.' ),
				array( 'text', 'o1_l', __( 'Paid audit: overline', 'maison-digitale' ), 'Detailed audit · Paid' ),
				array( 'text', 'o1_t', __( 'Paid audit: title', 'maison-digitale' ), 'The full picture, reviewed by our team' ),
				array( 'area', 'o1_list', __( 'Paid audit: points (one per line)', 'maison-digitale' ), "A senior review of your identity, social channels, website and content\nA written report with priority recommendations\nDelivered within [delivery time]" ),
				array( 'text', 'o1_price', __( 'Paid audit: price', 'maison-digitale' ), '[Price]' ),
				array( 'text', 'o1_btn', __( 'Paid audit: button', 'maison-digitale' ), 'Get the detailed audit' ),
				array( 'url', 'o1_link', __( 'Paid audit: button link (checkout)', 'maison-digitale' ), '#audit-pay' ),
				array( 'text', 'o2_l', __( 'Consultation: overline', 'maison-digitale' ), 'Consultation' ),
				array( 'text', 'o2_t', __( 'Consultation: title', 'maison-digitale' ), 'Talk your score through with us' ),
				array( 'area', 'o2_x', __( 'Consultation: text', 'maison-digitale' ), 'Prefer a conversation? Book a call with the team to go over your results and what they mean for your brand.' ),
				array( 'text', 'o2_note', __( 'Consultation: note', 'maison-digitale' ), '[Call length, and whether it is free]' ),
				array( 'text', 'o2_btn', __( 'Consultation: button', 'maison-digitale' ), 'Book a consultation' ),
				array( 'url', 'o2_link', __( 'Consultation: button link', 'maison-digitale' ), '/contact/' ),
				array( 'text', 'retake', __( 'Retake link', 'maison-digitale' ), 'Retake the questionnaire' ),
				array( 'text', 'work', __( 'Work link label', 'maison-digitale' ), 'See our work' ),
				array( 'url', 'work_link', __( 'Work link', 'maison-digitale' ), '/portfolio/' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$uid   = 'a' . $this->get_id();
		$areas = array();
		foreach ( (array) $s['areas'] as $a ) {
			$areas[] = $a['title'];
		}
		$qs = array();
		foreach ( (array) $s['questions'] as $q ) {
			$opts = array_slice( md_lines( $q['opts'] ), 0, 4 );
			if ( ! $opts || '' === trim( (string) $q['q'] ) ) {
				continue;
			}
			$qs[] = array(
				'a' => max( 0, min( 3, (int) $q['area'] - 1 ) ),
				't' => $q['q'],
				'o' => $opts,
			);
		}
		$data = array(
			'areas'  => $areas,
			'q'      => $qs,
			'qLabel' => $s['q_label'],
			'outOf'  => $s['out_of'],
			'notes'  => array( $s['n1'], $s['n2'], $s['n3'] ),
			'bands'  => array(
				array( 't' => $s['b1_t'], 'x' => $s['b1_x'] ),
				array( 't' => $s['b2_t'], 'x' => $s['b2_x'] ),
				array( 't' => $s['b3_t'], 'x' => $s['b3_x'] ),
			),
		);
		$stage_h = 'min-height: calc(100vh - 150px)';
		?>
		<div class="md-audit" data-md-audit="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">

			<div data-phase="intro">
				<header class="sec gd" style="background: #c9a24f; padding-top: clamp(48px, 7cqw, 104px); padding-bottom: clamp(48px, 6cqw, 88px)">
					<div class="in" style="display: flex; flex-direction: column; gap: clamp(16px, 2cqw, 28px)">
						<p class="ov"><?php echo esc_html( $s['kicker'] ); ?></p>
						<div>
							<?php echo $this->q( 'font-size: clamp(60px, 8.4cqw, 120px); line-height: .62' ); // phpcs:ignore ?>
							<h1 class="fh" style="font-size: clamp(40px, 7.2cqw, 104px)"><?php echo md_hl( $s['headline'] ); // phpcs:ignore ?></h1>
						</div>
						<p style="margin: 0; font-size: clamp(19px, 1.7cqw, 24px); line-height: 1.5; max-width: 30em"><?php echo md_br( $s['lead'] ); // phpcs:ignore ?></p>
						<div><button type="button" class="bt b-ink" data-md-start><?php echo esc_html( $s['start'] ); ?></button></div>
					</div>
				</header>

				<section class="sec">
					<div class="in" style="display: flex; flex-direction: column; gap: clamp(28px, 4cqw, 56px)">
						<?php echo $this->head( $s['areas_l'], $s['areas_t'] ); // phpcs:ignore ?>
						<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap: clamp(12px, 1.6cqw, 24px)">
							<?php foreach ( (array) $s['areas'] as $i => $a ) : ?>
								<div class="box rv"><p class="ov" style="color: #a67c2e"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></p><h3 class="fh" style="font-size: 26px; margin: 16px 0 8px"><?php echo esc_html( $a['title'] ); ?></h3><p style="margin: 0; color: #62574d"><?php echo md_br( $a['text'] ); // phpcs:ignore ?></p></div>
							<?php endforeach; ?>
						</div>
					</div>
				</section>

				<section class="sec" style="background: #ece4d6">
					<div class="in" style="display: flex; flex-direction: column; gap: clamp(28px, 4cqw, 56px)">
						<?php echo $this->head( $s['how_l'], $s['how_t'] ); // phpcs:ignore ?>
						<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap: 32px clamp(20px, 2.4cqw, 32px)">
							<?php foreach ( (array) $s['how'] as $h ) : ?>
								<div class="step rv"><p class="ov" style="color: #a67c2e"><?php echo esc_html( $h['step'] ); ?></p><h3 class="fh" style="font-size: clamp(24px, 2.4cqw, 32px); margin: 14px 0 10px"><?php echo esc_html( $h['title'] ); ?></h3><p style="margin: 0"><?php echo md_br( $h['text'] ); // phpcs:ignore ?></p></div>
							<?php endforeach; ?>
						</div>
						<div><button type="button" class="bt b-ink" data-md-start><?php echo esc_html( $s['start'] ); ?></button></div>
					</div>
				</section>
			</div>

			<div data-phase="quiz" hidden>
				<section class="sec dk" style="background: #17120f; color: #f6f1e9; padding-top: clamp(32px, 4cqw, 64px); <?php echo esc_attr( $stage_h ); ?>">
					<div class="in" style="max-width: 980px; display: flex; flex-direction: column; gap: clamp(24px, 3cqw, 40px)">
						<div style="display: flex; flex-direction: column; gap: 12px">
							<div style="display: flex; justify-content: space-between; gap: 16px">
								<p class="ov" style="color: #c9a24f" data-q-label></p>
								<p class="ov" style="color: #b9ad9d" data-q-area></p>
							</div>
							<div role="progressbar" aria-valuemin="0" aria-valuemax="<?php echo esc_attr( count( $qs ) ); ?>" aria-valuenow="0" aria-label="Questionnaire progress" style="height: 4px; background: #3b322a" data-q-bar><div style="height: 100%; width: 0; background: #c9a24f; transition: width .35s ease"></div></div>
						</div>
						<fieldset class="stp" style="border: 0; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px" data-q-set>
							<legend class="fh" style="font-size: clamp(28px, 4cqw, 56px); padding: 0; margin-bottom: clamp(20px, 2.4cqw, 32px)" data-q-text></legend>
							<div style="display: flex; flex-direction: column; gap: 12px" data-q-opts></div>
						</fieldset>
						<div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 12px; border-top: 1px solid #3b322a; padding-top: 24px">
							<button type="button" class="bt b-line" data-md-prev><?php echo esc_html( $s['back'] ); ?></button>
							<p style="margin: 0; color: #b9ad9d; font-size: 15px; align-self: center"><?php echo esc_html( $s['q_hint'] ); ?></p>
						</div>
					</div>
				</section>
			</div>

			<div data-phase="details" hidden>
				<section class="sec dk" style="background: #17120f; color: #f6f1e9; padding-top: clamp(32px, 4cqw, 64px); <?php echo esc_attr( $stage_h ); ?>">
					<div class="in stp" style="max-width: 980px; display: flex; flex-direction: column; gap: clamp(24px, 3cqw, 40px)">
						<div>
							<p class="ov" style="color: #c9a24f"><?php echo esc_html( $s['d_kicker'] ); ?></p>
							<h2 class="fh" style="font-size: clamp(28px, 4cqw, 56px); margin-top: 14px"><?php echo md_hl( $s['d_title'] ); // phpcs:ignore ?></h2>
						</div>
						<form class="md-form" data-md-form="audit" data-md-manual novalidate style="display: flex; flex-direction: column; gap: clamp(24px, 3cqw, 40px)">
							<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap: 28px 24px">
								<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-name"><?php echo esc_html( $s['f_name'] ); ?></label><input class="fi" id="<?php echo esc_attr( $uid ); ?>-name" name="<?php echo esc_attr( $s['f_name'] ); ?>" type="text" autocomplete="name" required></div>
								<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-email"><?php echo esc_html( $s['f_email'] ); ?></label><input class="fi" id="<?php echo esc_attr( $uid ); ?>-email" name="<?php echo esc_attr( $s['f_email'] ); ?>" type="email" autocomplete="email" required></div>
								<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-biz"><?php echo esc_html( $s['f_biz'] ); ?></label><input class="fi" id="<?php echo esc_attr( $uid ); ?>-biz" name="<?php echo esc_attr( $s['f_biz'] ); ?>" type="text" autocomplete="organization" required></div>
								<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-phone"><?php echo esc_html( $s['f_phone'] ); ?> <span class="opt-l"><?php echo esc_html( $s['f_optional'] ); ?></span></label><input class="fi" id="<?php echo esc_attr( $uid ); ?>-phone" name="<?php echo esc_attr( $s['f_phone'] ); ?>" type="tel" autocomplete="tel"></div>
							</div>
							<input class="md-hp" type="text" name="md_hp" tabindex="-1" autocomplete="off" aria-hidden="true">
							<div class="md-sum" hidden role="alert"></div>
							<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 24px; border-top: 1px solid #3b322a; padding-top: 24px">
								<button type="button" class="bt b-line" data-md-backq><?php echo esc_html( $s['back'] ); ?></button>
								<div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px 20px">
									<span style="color: #b9ad9d; font-size: 15px"><?php echo esc_html( $s['d_note'] ); ?></span>
									<button type="submit" class="bt b-gold"><?php echo esc_html( $s['d_btn'] ); ?></button>
								</div>
							</div>
						</form>
					</div>
				</section>
			</div>

			<div data-phase="result" hidden>
				<section class="sec" style="padding-top: clamp(32px, 4cqw, 64px)">
					<div class="in stp" style="display: flex; flex-direction: column; gap: clamp(32px, 4.4cqw, 64px)">
						<div style="display: flex; flex-wrap: wrap; gap: clamp(24px, 5cqw, 80px); align-items: flex-end">
							<div style="flex: 1 1 300px">
								<p class="ov" style="color: #62574d"><?php echo esc_html( $s['r_label'] ); ?></p>
								<p class="fh" style="margin-top: 10px; display: flex; align-items: baseline; gap: 12px"><span style="font-size: clamp(110px, 17cqw, 240px); line-height: .82; color: #a67c2e" data-r-total>0</span><span style="font-size: clamp(26px, 3cqw, 44px); color: #62574d"><?php echo esc_html( $s['r_of'] ); ?></span></p>
							</div>
							<div style="flex: 1.3 1 340px; display: flex; flex-direction: column; gap: 12px">
								<h1 class="fh" style="font-size: clamp(30px, 4.4cqw, 64px)" data-r-title></h1>
								<p style="margin: 0; font-size: clamp(18px, 1.6cqw, 22px); line-height: 1.5; max-width: 30em" data-r-text></p>
							</div>
						</div>

						<div style="display: flex; flex-direction: column; border-top: 1px solid #17120f" data-r-areas></div>

						<div>
							<p class="ov" style="color: #62574d"><?php echo esc_html( $s['w_label'] ); ?></p>
							<h2 class="fh" style="font-size: clamp(30px, 4cqw, 56px); margin: 12px 0 clamp(20px, 2.4cqw, 32px)"><?php echo md_hl( $s['w_title'] ); // phpcs:ignore ?></h2>
							<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr)); gap: clamp(12px, 2cqw, 28px)">
								<div class="off gd" style="background: #c9a24f">
									<p class="ov"><?php echo esc_html( $s['o1_l'] ); ?></p>
									<h3 class="fh" style="font-size: clamp(28px, 3cqw, 42px)"><?php echo md_hl( $s['o1_t'] ); // phpcs:ignore ?></h3>
									<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 8px">
										<?php foreach ( md_lines( $s['o1_list'] ) as $li ) : ?>
											<li style="border-top: 1px solid rgba(23,18,15,.35); padding-top: 8px"><?php echo esc_html( $li ); ?></li>
										<?php endforeach; ?>
									</ul>
									<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 20px; margin-top: auto; padding-top: 8px">
										<span class="fh" style="font-size: 30px"><?php echo esc_html( $s['o1_price'] ); ?></span>
										<a class="bt b-ink" <?php echo md_href( $s['o1_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['o1_btn'] ); ?></a>
									</div>
								</div>
								<div class="off dk" style="background: #17120f; color: #f6f1e9">
									<p class="ov" style="color: #c9a24f"><?php echo esc_html( $s['o2_l'] ); ?></p>
									<h3 class="fh" style="font-size: clamp(28px, 3cqw, 42px)"><?php echo md_hl( $s['o2_t'] ); // phpcs:ignore ?></h3>
									<p style="margin: 0; color: #b9ad9d"><?php echo md_br( $s['o2_x'] ); // phpcs:ignore ?></p>
									<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 20px; margin-top: auto; padding-top: 8px">
										<span style="color: #b9ad9d; font-size: 15px"><?php echo esc_html( $s['o2_note'] ); ?></span>
										<a class="bt b-gold" <?php echo md_href( $s['o2_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['o2_btn'] ); ?></a>
									</div>
								</div>
							</div>
						</div>

						<div style="display: flex; flex-wrap: wrap; gap: 8px 28px; align-items: center">
							<button type="button" class="lk" data-md-retake><?php echo esc_html( $s['retake'] ); ?></button>
							<a class="lk" <?php echo md_href( $s['work_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['work'] ); ?></a>
						</div>
					</div>
				</section>
			</div>
		</div>
		<?php
	}
}

/* =========================================================================
 * Let's work together — WorkTogether.dc.html four-step form
 * ====================================================================== */
class MD_W_Enquiry extends MD_Widget {
	protected $md_icon = 'eicon-form-vertical';

	public function get_name() {
		return 'md-enquiry';
	}

	public function get_title() {
		return __( 'Project enquiry (4 steps)', 'maison-digitale' );
	}

	protected function register_controls() {
		$svc = array();
		foreach ( md_services() as $sv ) {
			$svc[] = $sv['name'];
		}
		$svc[] = 'Not sure yet';

		$this->sec( 'g', __( 'Progress and buttons', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'step_l', __( 'Progress label', 'maison-digitale' ), 'Step %1$s of %2$s' ),
				array( 'text', 'n1', __( 'Step 1 name', 'maison-digitale' ), 'What you need' ),
				array( 'text', 'n2', __( 'Step 2 name', 'maison-digitale' ), 'About the project' ),
				array( 'text', 'n3', __( 'Step 3 name', 'maison-digitale' ), 'Budget and timeline' ),
				array( 'text', 'n4', __( 'Step 4 name', 'maison-digitale' ), 'Your details' ),
				array( 'text', 'back', __( 'Back', 'maison-digitale' ), 'Back' ),
				array( 'text', 'next', __( 'Continue', 'maison-digitale' ), 'Continue' ),
				array( 'text', 'send', __( 'Send', 'maison-digitale' ), 'Send my brief' ),
			)
		);
		$this->end();

		$this->sec( 's1', __( 'Step 1 — What do you need?', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 's1_t', __( 'Question', 'maison-digitale' ), 'What do you need?' ),
				array( 'text', 's1_h', __( 'Hint', 'maison-digitale' ), 'Choose as many as apply.' ),
				array( 'area', 's1_o', __( 'Choices (one per line)', 'maison-digitale' ), implode( "\n", $svc ), array( 'rows' => 10 ) ),
			)
		);
		$this->end();

		$this->sec( 's2', __( 'Step 2 — The project', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 's2_t', __( 'Question', 'maison-digitale' ), 'Tell us about the project.' ),
				array( 'text', 's2_h', __( 'Hint', 'maison-digitale' ), 'The brand, the goal, and anything you already have in mind.' ),
				array( 'text', 's2_p', __( 'Placeholder', 'maison-digitale' ), 'Start typing…' ),
			)
		);
		$this->end();

		$this->sec( 's3', __( 'Step 3 — Budget and timeline', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 's3_t', __( 'Heading', 'maison-digitale' ), 'Budget and timeline.' ),
				array( 'text', 'bud_l', __( 'Budget label', 'maison-digitale' ), 'Budget range' ),
				array( 'area', 'bud', __( 'Budget ranges (one per line)', 'maison-digitale' ), "[Range 1]\n[Range 2]\n[Range 3]\n[Range 4]", array( 'rows' => 4 ) ),
				array( 'text', 'time_l', __( 'Timeline label', 'maison-digitale' ), 'Timeline' ),
				array( 'area', 'time', __( 'Timelines (one per line)', 'maison-digitale' ), "Urgent\n1 to 3 months\nFlexible", array( 'rows' => 3 ) ),
			)
		);
		$this->end();

		$this->sec( 's4', __( 'Step 4 — Your details', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 's4_t', __( 'Heading', 'maison-digitale' ), 'Your details.' ),
				array( 'text', 'f_name', __( 'Name label', 'maison-digitale' ), 'Name' ),
				array( 'text', 'f_co', __( 'Company label', 'maison-digitale' ), 'Company' ),
				array( 'text', 'f_email', __( 'Email label', 'maison-digitale' ), 'Email' ),
				array( 'text', 'f_phone', __( 'Phone label', 'maison-digitale' ), 'Phone / WhatsApp' ),
			)
		);
		$this->end();

		$this->sec( 'done', __( 'Thank-you screen', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'd_k', __( 'Overline', 'maison-digitale' ), 'Brief received' ),
				array( 'area', 'd_t', __( 'Heading', 'maison-digitale' ), 'Thank you. We’re reading it now.' ),
				array( 'area', 'd_x', __( 'Text', 'maison-digitale' ), 'We will be in touch within [response time] to schedule a call.' ),
				array( 'text', 'd_b1', __( 'Button', 'maison-digitale' ), 'See our work' ),
				array( 'url', 'd_b1_link', __( 'Button link', 'maison-digitale' ), '/portfolio/' ),
				array( 'text', 'd_b2', __( 'Restart button', 'maison-digitale' ), 'Start another brief' ),
				array( 'text', 'alt', __( 'Line under the form', 'maison-digitale' ), 'Prefer to start with a free brand audit?' ),
				array( 'text', 'alt_l', __( 'Line link label', 'maison-digitale' ), 'Get your free audit' ),
				array( 'url', 'alt_link', __( 'Line link', 'maison-digitale' ), '/free-brand-audit/' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$uid   = 'w' . $this->get_id();
		$names = array( $s['n1'], $s['n2'], $s['n3'], $s['n4'] );
		$chips = function ( $list, $group ) {
			foreach ( md_lines( $list ) as $c ) {
				echo '<button type="button" class="chip" aria-pressed="false" data-chip="' . esc_attr( $group ) . '" data-v="' . esc_attr( $c ) . '">' . esc_html( $c ) . '</button>';
			}
		};
		?>
		<section class="dk" style="padding: 0 clamp(20px, 5cqw, 80px) clamp(56px, 8cqw, 120px); background: #17120f; color: #f6f1e9">
			<div class="in" data-md-enquiry="<?php echo esc_attr( wp_json_encode( array( 'names' => $names, 'label' => $s['step_l'] ) ) ); ?>" style="border: 1px solid #3b322a; padding: clamp(24px, 4cqw, 64px); box-sizing: border-box; min-height: 520px; display: flex; flex-direction: column; gap: clamp(24px, 3cqw, 40px)">
				<form class="md-form" data-md-form="enquiry" data-md-manual novalidate style="display: contents">
					<div style="display: flex; flex-direction: column; gap: 12px" data-e-head>
						<div style="display: flex; justify-content: space-between; gap: 16px">
							<p class="ov" style="color: #c9a24f" data-e-label></p>
							<p class="ov" style="color: #b9ad9d" data-e-name></p>
						</div>
						<div role="progressbar" aria-valuemin="1" aria-valuemax="4" aria-valuenow="1" aria-label="Form progress" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 6px" data-e-bars>
							<span style="height: 4px; background: #c9a24f; transition: background-color .3s ease"></span><span style="height: 4px; background: #3b322a; transition: background-color .3s ease"></span><span style="height: 4px; background: #3b322a; transition: background-color .3s ease"></span><span style="height: 4px; background: #3b322a; transition: background-color .3s ease"></span>
						</div>
					</div>

					<fieldset class="stp" data-step="1" style="border: 0; padding: 0; margin: 0; flex: 1 1 auto; display: flex; flex-direction: column; gap: 24px">
						<legend class="fh" style="font-size: clamp(30px, 4.4cqw, 64px); padding: 0; margin-bottom: 24px"><?php echo md_hl( $s['s1_t'] ); // phpcs:ignore ?></legend>
						<p style="margin: 0; color: #b9ad9d"><?php echo esc_html( $s['s1_h'] ); ?></p>
						<div style="display: flex; flex-wrap: wrap; gap: 10px" data-multi="<?php echo esc_attr( $names[0] ); ?>"><?php $chips( $s['s1_o'], 'svc' ); ?></div>
					</fieldset>

					<div class="stp" data-step="2" hidden style="flex: 1 1 auto; display: flex; flex-direction: column; gap: 24px">
						<label class="fh" for="<?php echo esc_attr( $uid ); ?>-about" style="font-size: clamp(30px, 4.4cqw, 64px)"><?php echo md_hl( $s['s2_t'] ); // phpcs:ignore ?></label>
						<p style="margin: 0; color: #b9ad9d"><?php echo esc_html( $s['s2_h'] ); ?></p>
						<textarea class="fi" id="<?php echo esc_attr( $uid ); ?>-about" name="<?php echo esc_attr( $names[1] ); ?>" rows="6" placeholder="<?php echo esc_attr( $s['s2_p'] ); ?>"></textarea>
					</div>

					<div class="stp" data-step="3" hidden style="flex: 1 1 auto; display: flex; flex-direction: column; gap: 28px">
						<h2 class="fh" style="font-size: clamp(30px, 4.4cqw, 64px)"><?php echo md_hl( $s['s3_t'] ); // phpcs:ignore ?></h2>
						<fieldset style="border: 0; padding: 0; margin: 0">
							<legend class="lb" style="padding: 0; margin-bottom: 12px"><?php echo esc_html( $s['bud_l'] ); ?></legend>
							<div style="display: flex; flex-wrap: wrap; gap: 10px" data-single="<?php echo esc_attr( $s['bud_l'] ); ?>"><?php $chips( $s['bud'], 'budget' ); ?></div>
						</fieldset>
						<fieldset style="border: 0; padding: 0; margin: 0">
							<legend class="lb" style="padding: 0; margin-bottom: 12px"><?php echo esc_html( $s['time_l'] ); ?></legend>
							<div style="display: flex; flex-wrap: wrap; gap: 10px" data-single="<?php echo esc_attr( $s['time_l'] ); ?>"><?php $chips( $s['time'], 'time' ); ?></div>
						</fieldset>
					</div>

					<div class="stp" data-step="4" hidden style="flex: 1 1 auto; display: flex; flex-direction: column; gap: 28px">
						<h2 class="fh" style="font-size: clamp(30px, 4.4cqw, 64px)"><?php echo md_hl( $s['s4_t'] ); // phpcs:ignore ?></h2>
						<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap: 28px 24px">
							<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-name"><?php echo esc_html( $s['f_name'] ); ?></label><input class="fi" id="<?php echo esc_attr( $uid ); ?>-name" name="<?php echo esc_attr( $s['f_name'] ); ?>" type="text" autocomplete="name" required></div>
							<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-co"><?php echo esc_html( $s['f_co'] ); ?></label><input class="fi" id="<?php echo esc_attr( $uid ); ?>-co" name="<?php echo esc_attr( $s['f_co'] ); ?>" type="text" autocomplete="organization"></div>
							<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-email"><?php echo esc_html( $s['f_email'] ); ?></label><input class="fi" id="<?php echo esc_attr( $uid ); ?>-email" name="<?php echo esc_attr( $s['f_email'] ); ?>" type="email" autocomplete="email" required></div>
							<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-phone"><?php echo esc_html( $s['f_phone'] ); ?></label><input class="fi" id="<?php echo esc_attr( $uid ); ?>-phone" name="<?php echo esc_attr( $s['f_phone'] ); ?>" type="tel" autocomplete="tel"></div>
						</div>
						<input class="md-hp" type="text" name="md_hp" tabindex="-1" autocomplete="off" aria-hidden="true">
						<div class="md-sum" hidden role="alert"></div>
					</div>

					<div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 12px; border-top: 1px solid #3b322a; padding-top: 24px" data-e-nav>
						<div><button type="button" class="bt b-line" data-e-prev hidden><?php echo esc_html( $s['back'] ); ?></button></div>
						<div>
							<button type="button" class="bt b-ink" data-e-next><?php echo esc_html( $s['next'] ); ?></button>
							<button type="submit" class="bt b-gold" data-e-send hidden><?php echo esc_html( $s['send'] ); ?></button>
						</div>
					</div>
				</form>

				<div class="stp" data-e-done hidden tabindex="-1" style="flex: 1 1 auto; display: flex; flex-direction: column; justify-content: center; gap: 20px">
					<p class="ov" style="color: #c9a24f"><?php echo esc_html( $s['d_k'] ); ?></p>
					<?php echo $this->q( 'font-size: clamp(60px, 8cqw, 112px); line-height: .62; color: #c9a24f' ); // phpcs:ignore ?>
					<h2 class="fh" style="font-size: clamp(34px, 5.6cqw, 80px)"><?php echo md_hl( $s['d_t'] ); // phpcs:ignore ?></h2>
					<p style="margin: 0; font-size: clamp(18px, 1.6cqw, 22px); max-width: 30em"><?php echo md_br( $s['d_x'] ); // phpcs:ignore ?></p>
					<div style="display: flex; flex-wrap: wrap; gap: 12px"><a class="bt b-gold" <?php echo md_href( $s['d_b1_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['d_b1'] ); ?></a><button type="button" class="bt b-line" data-e-restart><?php echo esc_html( $s['d_b2'] ); ?></button></div>
				</div>
			</div>
			<?php if ( $s['alt'] ) : ?>
				<p class="in" style="margin-top: 20px; color: #b9ad9d"><?php echo esc_html( $s['alt'] ); ?> <a <?php echo md_href( $s['alt_link'] ); // phpcs:ignore ?> style="color: #f6f1e9; text-underline-offset: 5px"><?php echo esc_html( $s['alt_l'] ); ?></a></p>
			<?php endif; ?>
		</section>
		<?php
	}
}
