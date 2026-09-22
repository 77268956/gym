import { animate } from 'animejs';

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (!prefersReducedMotion) {
	animate('.sidebar', {
		translateX: [-18, 0],
		opacity: [0, 1],
		duration: 500,
		ease: 'outCubic'
	});

	animate('.topbar', {
		translateY: [-12, 0],
		opacity: [0, 1],
		duration: 450,
		ease: 'outCubic'
	});

	animate('#page-wrapper', {
		translateY: [12, 0],
		opacity: [0, 1],
		duration: 550,
		delay: 80,
		ease: 'outCubic'
	});
}
