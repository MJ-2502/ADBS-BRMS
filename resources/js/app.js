import './bootstrap';

// Theme toggle functionality
console.log('Theme toggle script loaded');

document.addEventListener('DOMContentLoaded', () => {
	console.log('DOM loaded, looking for theme toggle button');
	const toggleButton = document.getElementById('themeToggle');
	const root = document.documentElement;
	const storageKey = 'brms-theme';

	console.log('Toggle button:', toggleButton);

	if (!toggleButton) {
		console.error('Theme toggle button not found! Make sure element with id="themeToggle" exists');
		return;
	}

	// Apply initial theme if not already set by inline script
	const applyInitialTheme = () => {
		if (!root.classList.contains('dark')) {
			try {
				const storedTheme = localStorage.getItem(storageKey);
				console.log('Stored theme:', storedTheme);
				if (storedTheme === 'dark') {
					root.classList.add('dark');
					root.dataset.theme = 'dark';
				}
			} catch (error) {
				console.debug('Unable to read theme preference', error);
			}
		}
	};

	applyInitialTheme();

	// Toggle theme on button click
	toggleButton.addEventListener('click', (e) => {
		console.log('Theme toggle button clicked!');
		e.preventDefault();
		
		const isDark = root.classList.contains('dark');
		console.log('Current theme is dark:', isDark);
		
		if (isDark) {
			root.classList.remove('dark');
			root.dataset.theme = 'light';
			console.log('Switched to light mode');
			try {
				localStorage.setItem(storageKey, 'light');
			} catch (error) {
				console.debug('Unable to persist theme', error);
			}
		} else {
			root.classList.add('dark');
			root.dataset.theme = 'dark';
			console.log('Switched to dark mode');
			try {
				localStorage.setItem(storageKey, 'dark');
			} catch (error) {
				console.debug('Unable to persist theme', error);
			}
		}
	});
	
	console.log('Theme toggle event listener attached');

	// Sidebar toggle functionality
	const menuToggle = document.getElementById('menuToggle');
	const closeSidebar = document.getElementById('closeSidebar');
	const sidebar = document.getElementById('sidebar');
	const sidebarOverlay = document.getElementById('sidebarOverlay');

	const openSidebar = () => {
		sidebar?.classList.remove('-translate-x-full');
		if (sidebarOverlay) {
			sidebarOverlay.style.display = 'block';
			setTimeout(() => {
				sidebarOverlay.style.opacity = '1';
			}, 10);
		}
	};

	const closeSidebarFn = () => {
		sidebar?.classList.add('-translate-x-full');
		if (sidebarOverlay) {
			sidebarOverlay.style.opacity = '0';
			setTimeout(() => {
				sidebarOverlay.style.display = 'none';
			}, 300);
		}
	};

	menuToggle?.addEventListener('click', openSidebar);
	closeSidebar?.addEventListener('click', closeSidebarFn);
	sidebarOverlay?.addEventListener('click', closeSidebarFn);

	// Close sidebar on escape key
	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') {
			closeSidebarFn();
		}
	});
});
