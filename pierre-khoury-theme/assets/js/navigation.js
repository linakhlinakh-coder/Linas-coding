( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var toggle = document.querySelector( '.pk-mobile-toggle' );
		var panel = document.querySelector( '.pk-mobile-panel' );

		var slides = document.querySelectorAll( '.pk-hero__slide' );
		var bgs = document.querySelectorAll( '.pk-hero__bg' );
		var dots = document.querySelectorAll( '.pk-hero__dot' );
		if ( slides.length > 1 ) {
			var current = 0;
			var showSlide = function ( index ) {
				current = index;
				slides.forEach( function ( slide, i ) {
					slide.classList.toggle( 'is-active', i === index );
				} );
				bgs.forEach( function ( bg, i ) {
					bg.classList.toggle( 'is-active', i === index );
				} );
				dots.forEach( function ( dot, i ) {
					dot.classList.toggle( 'is-active', i === index );
				} );
			};
			dots.forEach( function ( dot, i ) {
				dot.addEventListener( 'click', function () {
					showSlide( i );
				} );
			} );
			setInterval( function () {
				showSlide( ( current + 1 ) % slides.length );
			}, 6500 );
		}

		if ( toggle && panel ) {
			toggle.addEventListener( 'click', function () {
				var isOpen = panel.classList.toggle( 'is-open' );
				toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
			} );

			var parents = panel.querySelectorAll( '.menu-item-has-children > a' );
			parents.forEach( function ( link ) {
				link.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					link.parentElement.classList.toggle( 'is-expanded' );
				} );
			} );
		}
	} );
} )();
