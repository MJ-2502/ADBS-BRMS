import './bootstrap';

// Dark theme is now the default and only theme
document.addEventListener('DOMContentLoaded', () => {
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
