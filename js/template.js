document.addEventListener('DOMContentLoaded', () => {
	const toggle = document.querySelector('.jl-menu-toggle');
	const navigation = document.querySelector('#jl-primary-nav');
	if (toggle && navigation) {
		toggle.addEventListener('click', () => {
			const open = toggle.getAttribute('aria-expanded') === 'true';
			toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
			navigation.classList.toggle('is-open', !open);
		});
	}
	const top = document.querySelector('.jl-to-top');
	if (top) {
		const update = () => top.classList.toggle('is-visible', window.scrollY > 420);
		window.addEventListener('scroll', update, { passive: true });
		top.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
		update();
	}
	const mailForm = document.querySelector('[data-demo-mail]');
	if (mailForm) {
		mailForm.addEventListener('submit', (event) => {
			event.preventDefault();
			const message = mailForm.querySelector('.jl-mail-message');
			if (message) {
				message.textContent = 'Demo complete. No email address has been stored.';
			}
			mailForm.reset();
		});
	}
});
