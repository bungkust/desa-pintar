/**
 * Navigation Menu JavaScript
 * Handles mobile menu toggles, keyboard navigation, click-outside detection,
 * and smart positioning for dropdowns.
 */

// Prevent duplicate initialization
let navbarInitialized = false;

/**
 * Initialize navbar functionality
 */
function initNavbar() {
    // Check if navbar exists
    const navbar = document.querySelector('[data-navbar]');
    if (!navbar) {
        return;
    }

    // Prevent duplicate initialization
    if (navbarInitialized) {
        return;
    }

    const navbarId = navbar.getAttribute('data-navbar');
    if (!navbarId) {
        return;
    }

    // Initialize mobile menu
    initMobileMenu(navbarId);

    // Initialize keyboard navigation
    initKeyboardNavigation(navbarId);

    // Initialize click-outside detection
    initClickOutside(navbarId);

    // Initialize smart positioning for level 2 dropdowns
    initSmartPositioning(navbar);

    // Mark as initialized
    navbarInitialized = true;
}

/**
 * Initialize mobile menu toggle functionality
 */
function initMobileMenu(navbarId) {
    const mobileButton = document.getElementById(`${navbarId}-mobile-button`);
    const mobileMenu = document.getElementById(`${navbarId}-mobile-menu`);

    // Get navbar element to query for child elements
    const navbar = document.querySelector(`[data-navbar="${navbarId}"]`);

    if (!mobileButton || !mobileMenu || !navbar) {
        return;
    }

    // Toggle mobile menu
    mobileButton.addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();
        const isExpanded = mobileButton.getAttribute('aria-expanded') === 'true';
        const willBeExpanded = !isExpanded;
        
        // Toggle menu visibility
        if (willBeExpanded) {
            mobileMenu.classList.remove('hidden');
        } else {
            mobileMenu.classList.add('hidden');
        }
        
        // Update ARIA attributes
        mobileButton.setAttribute('aria-expanded', willBeExpanded ? 'true' : 'false');
        
        // Toggle icons (hamburger <-> X)
        const iconOpen = document.getElementById(`${navbarId}-mobile-icon-open`);
        const iconClose = document.getElementById(`${navbarId}-mobile-icon-close`);
        if (iconOpen && iconClose) {
            if (willBeExpanded) {
                iconOpen.classList.add('hidden');
                iconClose.classList.remove('hidden');
            } else {
                iconOpen.classList.remove('hidden');
                iconClose.classList.add('hidden');
            }
        }
    });

    // Handle submenu toggles
    const toggleButtons = navbar.querySelectorAll('[data-mobile-toggle]');
    toggleButtons.forEach((button) => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const targetId = button.getAttribute('data-mobile-toggle');
            const submenu = document.getElementById(targetId);
            const itemId = targetId.replace(`${navbarId}-submenu-`, '');
            const icon = navbar.querySelector(`[data-icon="${navbarId}-icon-${itemId}"]`);

            if (!submenu) {
                return;
            }

            const isExpanded = button.getAttribute('aria-expanded') === 'true';

            // Close other submenus at the same level (one open at a time)
            const parentSubmenu = submenu.closest('[role="menu"]');
            if (parentSubmenu) {
                const siblings = parentSubmenu.querySelectorAll('[role="menu"]');
                siblings.forEach((sibling) => {
                    if (sibling !== submenu && !submenu.contains(sibling)) {
                        sibling.classList.add('hidden');
                        const siblingButton = navbar.querySelector(`[data-mobile-toggle="${sibling.id}"]`);
                        if (siblingButton) {
                            siblingButton.setAttribute('aria-expanded', 'false');
                            const siblingItemId = sibling.id.replace(`${navbarId}-submenu-`, '');
                            const siblingIcon = navbar.querySelector(`[data-icon="${navbarId}-icon-${siblingItemId}"]`);
                            if (siblingIcon) {
                                siblingIcon.classList.remove('rotate-180');
                            }
                        }
                    }
                });
            }

            // Toggle current submenu
            submenu.classList.toggle('hidden');
            button.setAttribute('aria-expanded', !isExpanded ? 'true' : 'false');

            // Rotate icon
            if (icon) {
                icon.classList.toggle('rotate-180');
            }
        });
    });

    // Close mobile menu when clicking on a link (that's not a toggle button)
    mobileMenu.addEventListener('click', (e) => {
        if (e.target.tagName === 'A' && !e.target.closest('button')) {
            mobileMenu.classList.add('hidden');
            mobileButton.setAttribute('aria-expanded', 'false');
            
            // Toggle icons back to hamburger
            const iconOpen = document.getElementById(`${navbarId}-mobile-icon-open`);
            const iconClose = document.getElementById(`${navbarId}-mobile-icon-close`);
            if (iconOpen && iconClose) {
                iconOpen.classList.remove('hidden');
                iconClose.classList.add('hidden');
            }
        }
    });
    
    // Helper function to close mobile menu and reset icons
    const closeMobileMenu = () => {
        mobileMenu.classList.add('hidden');
        mobileButton.setAttribute('aria-expanded', 'false');
        const iconOpen = document.getElementById(`${navbarId}-mobile-icon-open`);
        const iconClose = document.getElementById(`${navbarId}-mobile-icon-close`);
        if (iconOpen && iconClose) {
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
        }
    };
}

/**
 * Initialize keyboard navigation
 */
function initKeyboardNavigation(navbarId) {
    const navbar = document.querySelector(`[data-navbar="${navbarId}"]`);
    if (!navbar) {
        return;
    }

    // Handle keyboard events on menu items
    navbar.addEventListener('keydown', (e) => {
        const target = e.target;
        const isMenuItem = target.matches('a[href], button[data-mobile-toggle]');
        
        if (!isMenuItem) {
            return;
        }

        switch (e.key) {
            case 'Enter':
            case ' ':
                if (target.matches('button[data-mobile-toggle]')) {
                    e.preventDefault();
                    target.click();
                }
                break;

            case 'Escape':
                // Close mobile menu
                const mobileMenuEsc = document.getElementById(`${navbarId}-mobile-menu`);
                const mobileButtonEsc = document.getElementById(`${navbarId}-mobile-button`);
                if (mobileMenuEsc && !mobileMenuEsc.classList.contains('hidden')) {
                    mobileMenuEsc.classList.add('hidden');
                    if (mobileButtonEsc) {
                        mobileButtonEsc.setAttribute('aria-expanded', 'false');
                        // Reset icons to hamburger
                        const iconOpenEsc = document.getElementById(`${navbarId}-mobile-icon-open`);
                        const iconCloseEsc = document.getElementById(`${navbarId}-mobile-icon-close`);
                        if (iconOpenEsc && iconCloseEsc) {
                            iconOpenEsc.classList.remove('hidden');
                            iconCloseEsc.classList.add('hidden');
                        }
                    }
                    // Close all submenus
                    const submenus = mobileMenuEsc.querySelectorAll('[role="menu"]');
                    submenus.forEach((submenu) => {
                        if (submenu.id !== mobileMenuEsc.id) {
                            submenu.classList.add('hidden');
                            const submenuButton = navbar.querySelector(`[data-mobile-toggle="${submenu.id}"]`);
                            if (submenuButton) {
                                submenuButton.setAttribute('aria-expanded', 'false');
                            }
                        }
                    });
                    // Focus back on mobile button
                    if (mobileButtonEsc) {
                        mobileButtonEsc.focus();
                    }
                }
                break;

            case 'ArrowDown':
                e.preventDefault();
                focusNextMenuItem(target, navbar);
                break;

            case 'ArrowUp':
                e.preventDefault();
                focusPreviousMenuItem(target, navbar);
                break;
        }
    });
}

/**
 * Focus next menu item
 */
function focusNextMenuItem(currentItem, navbar) {
    const allItems = Array.from(navbar.querySelectorAll('a[href], button[data-mobile-toggle]'));
    const currentIndex = allItems.indexOf(currentItem);
    
    if (currentIndex < allItems.length - 1) {
        allItems[currentIndex + 1].focus();
    }
}

/**
 * Focus previous menu item
 */
function focusPreviousMenuItem(currentItem, navbar) {
    const allItems = Array.from(navbar.querySelectorAll('a[href], button[data-mobile-toggle]'));
    const currentIndex = allItems.indexOf(currentItem);
    
    if (currentIndex > 0) {
        allItems[currentIndex - 1].focus();
    }
}

/**
 * Initialize click-outside detection
 */
function initClickOutside(navbarId) {
    const navbar = document.querySelector(`[data-navbar="${navbarId}"]`);
    const mobileMenu = document.getElementById(`${navbarId}-mobile-menu`);
    const mobileButton = document.getElementById(`${navbarId}-mobile-button`);

    if (!navbar || !mobileMenu) {
        return;
    }

    document.addEventListener('click', (e) => {
        // Check if click is outside navbar
        if (!navbar.contains(e.target)) {
            // Close mobile menu if open
            if (!mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                if (mobileButton) {
                    mobileButton.setAttribute('aria-expanded', 'false');
                    // Reset icons to hamburger
                    const iconOpen = document.getElementById(`${navbarId}-mobile-icon-open`);
                    const iconClose = document.getElementById(`${navbarId}-mobile-icon-close`);
                    if (iconOpen && iconClose) {
                        iconOpen.classList.remove('hidden');
                        iconClose.classList.add('hidden');
                    }
                }
                
                // Close all submenus
                const submenus = mobileMenu.querySelectorAll('[role="menu"]');
                submenus.forEach((submenu) => {
                    if (submenu.id !== mobileMenu.id) {
                        submenu.classList.add('hidden');
                        const submenuButton = navbar.querySelector(`[data-mobile-toggle="${submenu.id}"]`);
                        if (submenuButton) {
                            submenuButton.setAttribute('aria-expanded', 'false');
                        }
                    }
                });
            }
        }
    });
}

/**
 * Initialize smart positioning for level 2 dropdowns
 * Positions dropdown to left if not enough space on right
 */
function initSmartPositioning(navbar) {
    // Only needed for desktop dropdowns
    const level2Dropdowns = navbar.querySelectorAll('[data-level2-dropdown]');
    
    level2Dropdowns.forEach((dropdown) => {
        const parentItem = dropdown.closest('.group');
        if (!parentItem) {
            return;
        }

        // Check position on hover
        parentItem.addEventListener('mouseenter', () => {
            positionDropdown(dropdown, parentItem);
        });

        // Recalculate on window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (parentItem.matches(':hover')) {
                    positionDropdown(dropdown, parentItem);
                }
            }, 100);
        }, { passive: true });
    });
}

/**
 * Position dropdown based on available space
 */
function positionDropdown(dropdown, parentItem) {
    // Temporarily show dropdown to measure it
    const wasHidden = dropdown.classList.contains('hidden');
    dropdown.classList.remove('hidden');
    dropdown.style.visibility = 'hidden';
    dropdown.style.display = 'block';
    
    const rect = parentItem.getBoundingClientRect();
    const dropdownRect = dropdown.getBoundingClientRect();
    const dropdownWidth = dropdownRect.width || 224; // min-w-[14rem] = 224px
    const spaceRight = window.innerWidth - rect.right;
    const spaceLeft = rect.left;
    
    // Reset positioning classes
    dropdown.classList.remove('left-full', 'right-full', 'ml-1.5', 'mr-1.5');
    
    // Position to left if not enough space on right
    if (spaceRight < dropdownWidth && spaceLeft > dropdownWidth) {
        dropdown.classList.add('right-full', 'mr-1.5');
    } else {
        dropdown.classList.add('left-full', 'ml-1.5');
    }
    
    // Restore original state
    dropdown.style.visibility = '';
    dropdown.style.display = '';
    if (wasHidden) {
        dropdown.classList.add('hidden');
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavbar);
} else {
    // DOM already loaded
    initNavbar();
}

// Re-initialize if needed (e.g., for SPAs or dynamic content)
// This can be called manually if navbar is added dynamically
window.initNavbar = initNavbar;

/**
 * Initialize nested submenu (level 2) hover functionality
 */
function initNestedSubmenu() {
    const nestedSubmenuItems = document.querySelectorAll('.nav-submenu-item');
    
    nestedSubmenuItems.forEach((item) => {
        const nestedSubmenu = item.querySelector('.nav-submenu-level2');
        
        if (!nestedSubmenu) {
            return;
        }
        
        // Show on hover
        item.addEventListener('mouseenter', () => {
            nestedSubmenu.style.display = 'block';
            nestedSubmenu.style.opacity = '1';
            nestedSubmenu.style.visibility = 'visible';
            nestedSubmenu.style.pointerEvents = 'auto';
        });
        
        // Keep visible when hovering the nested submenu itself
        nestedSubmenu.addEventListener('mouseenter', () => {
            nestedSubmenu.style.display = 'block';
            nestedSubmenu.style.opacity = '1';
            nestedSubmenu.style.visibility = 'visible';
            nestedSubmenu.style.pointerEvents = 'auto';
        });
        
        // Hide when mouse leaves both item and nested submenu
        let hideTimeout;
        const hideSubmenu = () => {
            hideTimeout = setTimeout(() => {
                // Check if mouse is still over item or nested submenu
                if (!item.matches(':hover') && !nestedSubmenu.matches(':hover')) {
                    nestedSubmenu.style.display = 'none';
                    nestedSubmenu.style.opacity = '0';
                    nestedSubmenu.style.visibility = 'hidden';
                    nestedSubmenu.style.pointerEvents = 'none';
                }
            }, 100);
        };
        
        item.addEventListener('mouseleave', hideSubmenu);
        nestedSubmenu.addEventListener('mouseleave', hideSubmenu);
        
        // Cancel hide if mouse re-enters
        item.addEventListener('mouseenter', () => {
            clearTimeout(hideTimeout);
        });
        nestedSubmenu.addEventListener('mouseenter', () => {
            clearTimeout(hideTimeout);
        });
    });
}

// Initialize nested submenu when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNestedSubmenu);
} else {
    initNestedSubmenu();
}

// Re-initialize if navbar is re-initialized
const originalInitNavbar = initNavbar;
initNavbar = function() {
    originalInitNavbar();
    setTimeout(initNestedSubmenu, 100);
};

