<?php
/**
 * Content data ported 1:1 from the original Claude Design prototype
 * (Pierre Khoury Website v3.dc.html) so wording matches the approved design.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pk_pillars_data() {
	return array(
		array(
			'id'        => 'genz',
			'num'       => '01',
			'short'     => 'Gen Z',
			'label'     => 'Gen Z Workplace Expertise',
			'title'     => 'Gen Z Workplace Expertise',
			'slug'      => 'gen-z-workplace-expertise',
			'sell'      => 'Stop losing Gen Z talent to management styles built for a different generation.',
			'outcomes'  => array( 'Cut Gen Z turnover', 'Rebuild manager-employee trust', 'Modernize onboarding & culture' ),
			'shortCta'  => 'Explore Gen Z Training',
			'hero'      => "Gen Z isn't a younger Millennial. Stop managing them like one.",
			'sub'       => 'Practical, research-based training that helps schools, universities, and companies understand and engage the first fully digital-native generation.',
			'body'      => 'Gen Z has grown up inside technology, not alongside it, and that difference changes how they learn, work, and expect to be managed. Institutions that apply Millennial-era management, teaching, or HR playbooks to Gen Z consistently see disengagement, high turnover, and communication breakdowns. This program gives leadership teams the frameworks to close that gap.',
			'forWhom'   => array( 'Universities and schools managing Gen Z students and young faculty', 'Corporate HR and leadership teams onboarding and retaining Gen Z employees', 'Startups built primarily around Gen Z talent' ),
			'included'  => array( 'Generational behavior and workplace-expectation workshops', 'Manager and educator training on communication and motivation styles', 'Organizational policy and culture recommendations', 'Custom sessions for HR, faculty, and leadership teams' ),
			'cta'       => 'Book a Gen Z Training Session',
			'ctaHead'   => 'Turn generational friction into retention.',
		),
		array(
			'id'        => 'finance',
			'num'       => '02',
			'short'     => 'Financial',
			'label'     => 'Financial Consulting & Training',
			'title'     => 'Financial Consulting & Training',
			'slug'      => 'financial-consulting-training',
			'sell'      => 'Give your leadership team the financial fluency to lead decisions, not just approve them.',
			'outcomes'  => array( 'Executive financial literacy', 'Startup fundraising readiness', 'Board-level financial oversight' ),
			'shortCta'  => 'Request a Proposal',
			'hero'      => "Your management team shouldn't have to take the finance team's word for it",
			'sub'       => 'Financial literacy training and advisory for corporate executives and startup founders who need to understand, not just approve, the numbers.',
			'body'      => 'In most organizations, financial decision-making is quietly outsourced to the finance department, leaving management following recommendations they cannot independently assess. This service builds core financial fluency across leadership teams and startup founders, so decisions are made with understanding, not blind trust.',
			'forWhom'   => array( 'Corporate management teams without a finance background', 'Startup founders managing budgets, fundraising, or investor relations', 'Boards and executive committees needing financial oversight literacy' ),
			'included'  => array( 'Core financial literacy workshops for non-finance executives', 'Startup financial planning and fundraising-readiness training', 'Ongoing advisory and retainer options', 'Custom in-house training programs' ),
			'cta'       => 'Request a Financial Training Proposal',
			'ctaHead'   => 'Build financial fluency at the leadership level.',
		),
		array(
			'id'        => 'career',
			'num'       => '03',
			'short'     => 'Career',
			'label'     => 'Career Advisory & Lifelong Learning',
			'title'     => 'Career Advisory & Lifelong Learning',
			'slug'      => 'career-advisory-lifelong-learning',
			'sell'      => 'From first job to next pivot, guidance built on 30+ years of academic counseling.',
			'outcomes'  => array( 'Student career pathing', 'Professional upskilling roadmaps', 'Institutional career-center setup' ),
			'shortCta'  => 'Book a Session',
			'hero'      => "Careers don't end at graduation, and neither should career advice",
			'sub'       => 'Guidance for students choosing a path, and for working professionals adapting to fields that are already in high demand.',
			'body'      => "Career advisory shouldn't stop once someone lands a first job, the professionals furthest ahead are the ones still actively learning. This service supports students making early career decisions and experienced professionals repositioning themselves in high-demand fields, combining decades of academic counseling experience with a current read on where regional job markets are heading.",
			'forWhom'   => array( 'University and school students planning career paths', 'Working professionals seeking to pivot or upskill', 'Institutions building career-counseling capacity for their students' ),
			'included'  => array( 'One-on-one and group career counseling', 'Lifelong learning and upskilling roadmaps', 'Institutional career-center advisory and setup', 'Workshops on in-demand fields and regional job market trends' ),
			'cta'       => 'Book a Career Advisory Session',
			'ctaHead'   => 'Support the careers your institution is shaping.',
		),
		array(
			'id'        => 'blockchain',
			'num'       => '04',
			'short'     => 'Blockchain',
			'label'     => 'Blockchain Training & Business Application',
			'title'     => 'Blockchain Training & Application',
			'slug'      => 'blockchain-training-business-application',
			'sell'      => 'Move your business past blockchain hype into blockchain advantage.',
			'outcomes'  => array( 'Applied blockchain training', 'Use-case identification', 'Startup blockchain strategy' ),
			'shortCta'  => 'Book a Session',
			'hero'      => 'Blockchain, beyond the buzzword',
			'sub'       => "Practical blockchain training and application consulting for businesses, startups, and entrepreneurs navigating the region's growing blockchain ecosystem.",
			'body'      => 'As blockchain adoption accelerates across the Arab world, businesses need more than surface-level awareness, they need practical understanding of where the technology actually applies to their operations. Drawing on an active academic role teaching blockchain internationally, this service translates the technology into real applications for regional businesses and entrepreneurs.',
			'forWhom'   => array( 'Established businesses evaluating blockchain use cases', 'Startups and entrepreneurs building on blockchain infrastructure', 'Corporate innovation and strategy teams' ),
			'included'  => array( 'Foundational and applied blockchain training programs', 'Business-specific blockchain application consulting', 'Startup and entrepreneur workshops', 'Executive briefings on the regional blockchain ecosystem' ),
			'cta'       => 'Book a Blockchain Training Session',
			'ctaHead'   => 'Translate blockchain into something your business can use.',
		),
		array(
			'id'        => 'center',
			'num'       => '05',
			'short'     => 'Training Centers',
			'label'     => 'Training Center Launch & Advisory',
			'title'     => 'Training Center Launch & Advisory',
			'slug'      => 'training-center-launch-advisory',
			'sell'      => 'From feasibility study to certified operations.',
			'outcomes'  => array( 'Feasibility & market studies', 'Curriculum & accreditation design', 'End-to-end launch support' ),
			'shortCta'  => 'Start a Feasibility Consultation',
			'hero'      => 'Launching a training center takes more than good intentions',
			'sub'       => 'Feasibility, setup, and operational advisory for training centers across the Arab world, built on direct experience supporting training centre launches across the region.',
			'body'      => 'Training centers succeed or fail long before opening day, on market sizing, curriculum fit, staffing, and provider relationships. With direct experience supporting training centre launches across the Arab world, this service walks investors and institutions through the full lifecycle: from feasibility study to certified operations.',
			'forWhom'   => array( 'Institutions and investors launching new training centers', 'Regional bodies and organizations building internal training capacity', 'Existing centers expanding or seeking accreditation' ),
			'included'  => array( 'Feasibility studies and market/demand analysis', 'Curriculum and program design', 'Staffing, vendor, and provider-relationship setup', 'Certification and accreditation pathway advisory', 'Ongoing operational advisory' ),
			'cta'       => 'Request a Training Center Feasibility Consultation',
			'ctaHead'   => 'Plan the center before you build it.',
		),
	);
}

function pk_slides_data() {
	return array(
		array(
			'eyebrow' => 'Lebanon & the Arab World',
			'head'    => 'Training and advisory that move institutions forward.',
			'sub'     => 'Gen Z strategy. Financial fluency. Career growth. Blockchain. Training centers that actually launch. Five disciplines, one point of contact, working with institutions across Lebanon and the Arab world.',
			'cta'     => 'Explore Services',
		),
		array(
			'eyebrow' => 'Gen Z Workplace Expertise',
			'head'    => 'Your next hire is Gen Z. Is your institution ready?',
			'sub'     => 'Practical training that turns generational friction into retention, performance, and growth.',
			'cta'     => 'Explore Gen Z Training',
		),
		array(
			'eyebrow' => 'Training Center Advisory',
			'head'    => 'From feasibility study to opening day, training centers built to work.',
			'sub'     => 'Feasibility, curriculum and launch support for training centres built to keep running after opening day.',
			'cta'     => 'Start a Feasibility Consultation',
		),
	);
}

function pk_counters_data() {
	return array(
		array( 'value' => '30+', 'label' => 'Years advising institutions and businesses across the region' ),
		array( 'value' => '5', 'label' => 'Areas of specialized expertise, one advisor' ),
		array( 'value' => '4', 'label' => 'Books published on finance, education and blockchain' ),
		array( 'value' => '40+', 'label' => 'Peer-reviewed research papers' ),
	);
}

function pk_process_data() {
	return array(
		array( 'num' => '01', 'title' => 'Diagnose', 'body' => 'We start by understanding exactly where your institution or business stands, the Gen Z friction point, the financial blind spot, the training center gap, before recommending anything.' ),
		array( 'num' => '02', 'title' => 'Deliver', 'body' => 'Custom training, advisory, or feasibility work designed around your actual context, not a generic template pulled off a shelf.' ),
		array( 'num' => '03', 'title' => 'Sustain', 'body' => "We don't disappear after the workshop. Ongoing advisory options ensure the change actually sticks." ),
	);
}

function pk_packages_data() {
	return array(
		array(
			'tier'  => 'Level 01',
			'name'  => 'Single Session',
			'body'  => 'One workshop or advisory session on any of the five pillars.',
			'items' => array( 'One focused session (half-day or full-day)', 'Custom materials for your team', 'Post-session summary & recommendations' ),
			'cta'   => 'Book a Session',
			'style' => 'light',
		),
		array(
			'tier'  => 'Level 02',
			'name'  => 'Institutional Program',
			'body'  => 'Multi-session training or advisory engagement for schools, universities, or corporates.',
			'items' => array( 'Multiple sessions across a defined program', 'Tailored curriculum per pillar', 'Progress check-ins' ),
			'cta'   => 'Request a Proposal',
			'style' => 'dark',
		),
		array(
			'tier'  => 'Level 03',
			'name'  => 'Full Advisory Retainer',
			'body'  => 'Ongoing strategic advisory, ideal for training center launches or long-term institutional partnerships.',
			'items' => array( 'Continuous advisory access', 'Feasibility-to-launch support', 'Priority scheduling' ),
			'cta'   => 'Talk to Us',
			'style' => 'light',
		),
	);
}

function pk_credentials_data() {
	$items = array(
		'PhD, Business Administration, Arab Academy for Banking & Financial Studies (2015)',
		'MS, Money & Banking, American University of Beirut',
		'Assistant VP, External Relations, American University of Technology (AUT)',
		'Former Dean, Faculty of Business Administration, AUT',
		'Former VP for Development & Dean of Business School, Lebanese German University (2014-2020)',
		'Adjunct Research & Blockchain for Business Professor, Dayananda Sagar University (DSU), Bangalore',
		'Graduate & doctoral teaching appointments, Beirut Arab University, Royal Roads University (Canada), American Imperial University (Florida)',
		'Board Member, Lebanese Economic Association',
		'Publisher, Strategic File',
		'Opinion Editor, Aswak Al Arab',
		'Host, Hakika Bi Kam Dakika podcast',
	);
	$out = array();
	foreach ( $items as $i => $text ) {
		$out[] = array( 'num' => str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ), 'text' => $text );
	}
	return $out;
}

function pk_track_groups_data() {
	return array(
		array( 'label' => 'Academic', 'items' => array( 'American University of Technology (AUT)', 'Beirut Arab University', 'Royal Roads University, Canada', 'American Imperial University, Florida', 'Dayananda Sagar University (DSU), India', 'Heilbronn University, Germany' ) ),
		array( 'label' => 'Regional Institutions', 'items' => array( 'OAPEC (Organization of Arab Petroleum Exporting Countries)', 'Kuwait Petroleum Training Center (KOC / KPC), trainer across business disciplines', 'Union of Arab Chambers', 'Chamber of Commerce in Lebanon', 'Affiliated regional training bodies' ) ),
		array( 'label' => 'Government & International', 'items' => array( 'Government of Abu Dhabi (DED), Local Content Initiation Project', 'Nigerian Ministry of Foreign Affairs, Blockchain for Humanitarian Aid training', 'Central Bank of Lebanon', 'Central Bank of Kuwait' ) ),
		array( 'label' => 'Blockchain Programs', 'items' => array( 'INSEAD, France, specialized training', 'Blockchain for Business Bootcamp, 93 completed entrepreneur projects' ) ),
		array( 'label' => 'Media & Publishing', 'items' => array( 'Strategic File, Publisher', 'Aswak Al Arab, Opinion Editor', 'Hakika Bi Kam Dakika, Podcast host', 'MTV, Al Arabi TV & OSN, blockchain commentary' ) ),
		array( 'label' => 'Associations', 'items' => array( 'Lebanese Economic Association, Board Member (former General Secretary)', 'ACBSP, Region Eight Secretary (2022)' ) ),
	);
}

/**
 * Real photography supplied for the homepage hero slider, the "Get to
 * Know Pierre" positioning section, and the About page portrait — hosted
 * on the live pierrekhoury.com site (already uploaded there).
 */
function pk_hero_slide_images() {
	return array(
		'https://pierrekhoury.com/wp-content/uploads/2026/08/1782206199346.jpg',
		'https://pierrekhoury.com/wp-content/uploads/2026/08/1741284867033.jpg',
		'https://pierrekhoury.com/wp-content/uploads/2026/08/shutterstock_1117902230.jpg',
	);
}

function pk_positioning_image() {
	return 'https://pierrekhoury.com/wp-content/uploads/2026/08/IMG-20260829-WA0115.jpg';
}

function pk_about_portrait_image() {
	return 'https://pierrekhoury.com/wp-content/uploads/2026/09/images.jpg';
}

function pk_marquee_text() {
	return 'Gen Z Strategy · Financial Literacy · Career Growth · Blockchain · Training Centers ·';
}

function pk_contact_fields_data() {
	return array(
		array( 'label' => 'Full Name', 'ph' => 'Your name' ),
		array( 'label' => 'Organization / Institution', 'ph' => 'University, company or ministry' ),
		array( 'label' => 'Email', 'ph' => 'name@organization.com' ),
		array( 'label' => 'Phone / WhatsApp', 'ph' => '+961 …' ),
		array( 'label' => 'Country / City', 'ph' => 'Lebanon, Beirut' ),
	);
}
