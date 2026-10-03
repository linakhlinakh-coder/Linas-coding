<?php
/**
 * Base class for all Maison Digitale widgets: short helpers to declare controls.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

abstract class MD_Widget extends \Elementor\Widget_Base {

	/** @var string Elementor icon class. */
	protected $md_icon = 'eicon-section';

	public function get_categories() {
		return array( 'maison-digitale' );
	}

	public function get_icon() {
		return $this->md_icon;
	}

	public function get_keywords() {
		return array( 'maison', 'digitale', 'md' );
	}

	public function get_script_depends() {
		return array( 'maison-digitale' );
	}

	public function get_style_depends() {
		return array( 'maison-digitale' );
	}

	/**
	 * Open a content section in the panel.
	 *
	 * @param string $id    Id.
	 * @param string $label Label.
	 */
	protected function sec( $id, $label ) {
		$this->start_controls_section(
			$id,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
	}

	protected function end() {
		$this->end_controls_section();
	}

	/**
	 * Declare controls from a compact spec.
	 * Each row: array( type, id, label, default, extra args ).
	 * Types: text, area, wys, url, media, select, switch, number, heading, rep.
	 *
	 * @param array         $spec   Rows.
	 * @param Repeater|null $target Repeater to add to, or the widget itself.
	 */
	protected function fields( array $spec, $target = null ) {
		$target = $target ? $target : $this;
		foreach ( $spec as $row ) {
			$type    = $row[0];
			$id      = $row[1];
			$label   = isset( $row[2] ) ? $row[2] : '';
			$default = isset( $row[3] ) ? $row[3] : '';
			$extra   = isset( $row[4] ) ? $row[4] : array();
			$args    = array( 'label' => $label );
			switch ( $type ) {
				case 'text':
					$args += array(
						'type'        => Controls_Manager::TEXT,
						'default'     => $default,
						'label_block' => true,
						'dynamic'     => array( 'active' => true ),
					);
					break;
				case 'area':
					$args += array(
						'type'    => Controls_Manager::TEXTAREA,
						'default' => $default,
						'rows'    => 3,
						'dynamic' => array( 'active' => true ),
					);
					break;
				case 'wys':
					$args += array(
						'type'    => Controls_Manager::WYSIWYG,
						'default' => $default,
					);
					break;
				case 'url':
					$args += array(
						'type'        => Controls_Manager::URL,
						'default'     => array( 'url' => $default ),
						'label_block' => true,
						'dynamic'     => array( 'active' => true ),
						'placeholder' => 'https:// or /page-slug/',
					);
					break;
				case 'media':
					$args += array(
						'type'    => Controls_Manager::MEDIA,
						'default' => array(
							'url' => $default,
							'id'  => '',
						),
						'media_types' => isset( $extra['media_types'] ) ? $extra['media_types'] : array( 'image' ),
						'dynamic' => array( 'active' => true ),
					);
					unset( $extra['media_types'] );
					break;
				case 'select':
					$args += array(
						'type'    => Controls_Manager::SELECT,
						'default' => $default,
					);
					break;
				case 'switch':
					$args += array(
						'type'         => Controls_Manager::SWITCHER,
						'default'      => $default,
						'return_value' => 'yes',
					);
					break;
				case 'number':
					$args += array(
						'type'    => Controls_Manager::NUMBER,
						'default' => $default,
					);
					break;
				case 'heading':
					$args += array(
						'type'      => Controls_Manager::HEADING,
						'separator' => 'before',
					);
					break;
				case 'note':
					$args = array(
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => $label,
						'content_classes' => 'elementor-descriptor',
					);
					break;
				case 'rep':
					$repeater = new Repeater();
					$this->fields( $extra['fields'], $repeater );
					$args += array(
						'type'        => Controls_Manager::REPEATER,
						'fields'      => $repeater->get_controls(),
						'default'     => $this->with_ids( $default ),
						'title_field' => isset( $extra['title'] ) ? $extra['title'] : '{{{ title }}}',
					);
					unset( $extra['fields'], $extra['title'] );
					break;
			}
			$target->add_control( $id, array_merge( $args, $extra ) );
		}
	}

	/**
	 * Give repeater defaults the ids Elementor expects.
	 *
	 * @param array $rows Rows.
	 * @return array
	 */
	protected function with_ids( $rows ) {
		if ( ! is_array( $rows ) ) {
			return array();
		}
		foreach ( $rows as $i => $row ) {
			if ( ! isset( $row['_id'] ) ) {
				$rows[ $i ]['_id'] = substr( md5( $this->get_name() . $i . wp_json_encode( $row ) ), 0, 7 );
			}
		}
		return $rows;
	}

	/**
	 * Rows from simple lists: md_rows( array( 'title', 'text' ), array( array( 'A', 'B' ) ) ).
	 *
	 * @param array $keys Keys.
	 * @param array $data Data.
	 * @return array
	 */
	protected function rows( $keys, $data ) {
		$out = array();
		foreach ( $data as $d ) {
			$out[] = array_combine( $keys, array_pad( (array) $d, count( $keys ), '' ) );
		}
		return $out;
	}

	/**
	 * Standard reveal class switch.
	 *
	 * @param array  $s     Settings.
	 * @param string $class Class.
	 * @return string
	 */
	protected function rv( $s, $class = 'rv' ) {
		return ( isset( $s['reveal'] ) && 'yes' !== $s['reveal'] ) ? '' : $class;
	}

	/**
	 * Common "Reveal on scroll" control.
	 */
	protected function reveal_control() {
		$this->fields( array( array( 'switch', 'reveal', __( 'Reveal on scroll', 'maison-digitale' ), 'yes' ) ) );
	}

	/**
	 * Gold opening quotation mark.
	 *
	 * @param string $style Inline style.
	 * @return string
	 */
	protected function q( $style ) {
		return '<div aria-hidden="true" class="fh md-q" style="' . esc_attr( $style ) . '">“</div>';
	}

	/**
	 * Buttons repeater output.
	 *
	 * @param array $buttons Rows with label, link, style.
	 * @return string
	 */
	protected function buttons( $buttons ) {
		$out = '';
		foreach ( (array) $buttons as $b ) {
			if ( empty( $b['label'] ) ) {
				continue;
			}
			$style = ! empty( $b['style'] ) ? $b['style'] : 'b-ink';
			$out  .= '<a class="bt ' . esc_attr( $style ) . '" ' . md_href( isset( $b['link'] ) ? $b['link'] : '' ) . '>' . esc_html( $b['label'] ) . '</a>';
		}
		return $out;
	}

	/**
	 * Buttons repeater control.
	 *
	 * @param array  $default Default rows.
	 * @param string $id      Control id.
	 */
	protected function buttons_control( $default, $id = 'buttons' ) {
		$this->fields(
			array(
				array(
					'rep',
					$id,
					__( 'Buttons', 'maison-digitale' ),
					$default,
					array(
						'title'  => '{{{ label }}}',
						'fields' => array(
							array( 'text', 'label', __( 'Label', 'maison-digitale' ), '' ),
							array( 'url', 'link', __( 'Link', 'maison-digitale' ), '' ),
							array(
								'select',
								'style',
								__( 'Style', 'maison-digitale' ),
								'b-ink',
								array(
									'options' => array(
										'b-ink'  => __( 'Primary (ink)', 'maison-digitale' ),
										'b-gold' => __( 'Conversion (gold)', 'maison-digitale' ),
										'b-line' => __( 'Secondary (outline)', 'maison-digitale' ),
									),
								),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Socials repeater control.
	 *
	 * @param array $default Rows.
	 */
	protected function socials_control( $default ) {
		$this->fields(
			array(
				array(
					'rep',
					'socials',
					__( 'Social links', 'maison-digitale' ),
					$default,
					array(
						'title'  => '{{{ network }}} {{{ label }}}',
						'fields' => array(
							array( 'select', 'network', __( 'Network', 'maison-digitale' ), 'instagram', array( 'options' => md_social_networks() ) ),
							array( 'url', 'link', __( 'Link', 'maison-digitale' ), '' ),
							array( 'text', 'label', __( 'Accessible label / text', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Section heading block used across inner pages: overline + Flyhead title.
	 *
	 * @param string $label    Overline.
	 * @param string $title    Title.
	 * @param string $label_c  Overline colour.
	 * @param string $size     Font size.
	 * @param string $tag      Tag.
	 * @return string
	 */
	protected function head( $label, $title, $label_c = '#62574d', $size = 'clamp(32px, 4.4cqw, 64px)', $tag = 'h2' ) {
		$out = '<div>';
		if ( '' !== (string) $label ) {
			$out .= '<p class="ov" style="color: ' . esc_attr( $label_c ) . '">' . esc_html( $label ) . '</p>';
		}
		$out .= '<' . $tag . ' class="fh" style="font-size: ' . esc_attr( $size ) . '; margin-top: 12px">' . md_hl( $title ) . '</' . $tag . '>';
		return $out . '</div>';
	}

	/**
	 * Background tone options for sections.
	 *
	 * @return array
	 */
	protected function tones() {
		return array(
			'ground'    => __( 'Ivory', 'maison-digitale' ),
			'surface'   => __( 'Sand (surface)', 'maison-digitale' ),
			'dark'      => __( 'Black', 'maison-digitale' ),
			'gold'      => __( 'Gold', 'maison-digitale' ),
		);
	}

	/**
	 * Section attributes for a tone.
	 *
	 * @param string $tone Tone.
	 * @return array class, style
	 */
	protected function tone( $tone ) {
		switch ( $tone ) {
			case 'surface':
				return array( '', 'background: #ece4d6' );
			case 'dark':
				return array( 'dk', 'background: #17120f; color: #f6f1e9' );
			case 'gold':
				return array( 'gd', 'background: #c9a24f; color: #17120f' );
		}
		return array( '', 'background: #f6f1e9; color: #17120f' );
	}

	/**
	 * Muted text colour for a tone.
	 *
	 * @param string $tone Tone.
	 * @return string
	 */
	protected function muted( $tone ) {
		return 'dark' === $tone ? '#b9ad9d' : ( 'gold' === $tone ? '#17120f' : '#62574d' );
	}

	/**
	 * Accent (gold text) colour for a tone.
	 *
	 * @param string $tone Tone.
	 * @return string
	 */
	protected function accent( $tone ) {
		return 'dark' === $tone ? '#c9a24f' : ( 'gold' === $tone ? '#17120f' : '#a67c2e' );
	}

	/**
	 * Spacing controls (top and bottom padding of the section).
	 *
	 * @param string $pt Default top.
	 * @param string $pb Default bottom.
	 */
	protected function spacing_control( $pt = '', $pb = '' ) {
		$opts = array(
			''       => __( 'As designed', 'maison-digitale' ),
			'none'   => __( 'None', 'maison-digitale' ),
			'small'  => __( 'Small', 'maison-digitale' ),
			'medium' => __( 'Medium', 'maison-digitale' ),
			'large'  => __( 'Large', 'maison-digitale' ),
		);
		$this->fields(
			array(
				array( 'select', 'pt', __( 'Space above', 'maison-digitale' ), $pt, array( 'options' => $opts, 'separator' => 'before' ) ),
				array( 'select', 'pb', __( 'Space below', 'maison-digitale' ), $pb, array( 'options' => $opts ) ),
			)
		);
	}

	/**
	 * Inline padding overrides from the spacing controls.
	 *
	 * @param array $s Settings.
	 * @return string
	 */
	protected function pad( $s ) {
		$map = array(
			'none'   => '0',
			'small'  => 'clamp(40px, 5cqw, 72px)',
			'medium' => 'clamp(40px, 6cqw, 88px)',
			'large'  => 'clamp(56px, 8cqw, 120px)',
		);
		$out = '';
		if ( ! empty( $s['pt'] ) && isset( $map[ $s['pt'] ] ) ) {
			$out .= '; padding-top: ' . $map[ $s['pt'] ];
		}
		if ( ! empty( $s['pb'] ) && isset( $map[ $s['pb'] ] ) ) {
			$out .= '; padding-bottom: ' . $map[ $s['pb'] ];
		}
		return $out;
	}

	/**
	 * Tone select control.
	 *
	 * @param string $default Default tone.
	 * @param array  $only    Limit to these tones.
	 */
	protected function tone_control( $default = 'ground', $only = array() ) {
		$tones = $this->tones();
		if ( $only ) {
			$tones = array_intersect_key( $tones, array_flip( $only ) );
		}
		$this->fields( array( array( 'select', 'tone', __( 'Background', 'maison-digitale' ), $default, array( 'options' => $tones ) ) ) );
	}
}
