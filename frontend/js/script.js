const targetElement = document.querySelector('.tm-scrolling');

function setOpacity(opacity) {
  if (targetElement) {
    targetElement.style.opacity = opacity;
  }
}

// Optimized scroll handling with requestAnimationFrame and throttling
let scrollTimeout = null;
let isScrolling = false;
let rafId = null;

if (targetElement) {
  // Throttled scroll handler using requestAnimationFrame
  window.addEventListener('scroll', () => {
    // Set opacity to 0.2 immediately when scrolling starts
    if (!isScrolling) {
      isScrolling = true;
      setOpacity(0.2);
    }
    
    // Clear any existing timeout and RAF
    clearTimeout(scrollTimeout);
    if (rafId) {
      cancelAnimationFrame(rafId);
    }
    
    // Schedule the opacity reset using requestAnimationFrame for smoother performance
    rafId = requestAnimationFrame(() => {
      scrollTimeout = setTimeout(() => {
        setOpacity(1);
        isScrolling = false;
      }, 150);
    });
  }, { passive: true }); // Add passive flag for better performance
}

document.addEventListener("DOMContentLoaded", function() {
    const icon = document.querySelector('.tm-featured');
    const menuPopup = document.querySelector('.tm-addon-menu-wrapper');
    const featuredBg = document.querySelector('.tm-featured-bg');

    // Optimized menu functions using CSS classes instead of direct style manipulation
    function closeMenu() {
        // Use CSS classes for animations instead of direct style manipulation
        menuPopup.classList.add('closing');
        icon.classList.remove('active');
        if (featuredBg) {
            featuredBg.classList.remove('expanded');
        }
        
        // Use a single timeout for all operations
        setTimeout(() => {
            menuPopup.classList.remove('show', 'closing');
            menuPopup.style.display = 'none';
        }, 500);
    }

    function openMenu() {
        // Batch DOM operations
        menuPopup.classList.add('show');
        menuPopup.style.display = 'flex';
        
        // Force a reflow to ensure the display change takes effect before animation
        menuPopup.offsetHeight;
        
        // Add animation class after display is set
        menuPopup.classList.add('opening');
        icon.classList.add('active');
        if (featuredBg) {
            featuredBg.classList.add('expanded');
        }
        
        // Remove animation class after animation completes
        setTimeout(() => {
            menuPopup.classList.remove('opening');
        }, 500);
    }

    if (icon && menuPopup) {
        // Use event delegation for better performance
        icon.addEventListener('click', function(e) {
            // Check if the click was on the close button
            if (e.target.closest('.tm-menu-close')) {
                return; // Let the close button handler handle it
            }
            
            const isShown = menuPopup.classList.contains('show');
            if (!isShown) {
                openMenu();
            } else {
                closeMenu();
            }
        });

        // Find the close button within the icon wrapper
        const closeButton = icon.querySelector('.tm-menu-close');
        if (closeButton) {
            closeButton.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent the icon click event from firing
                closeMenu();
            });
        }
    }

    // Optimize folder item handling with event delegation
    const addonMenu = document.querySelector('#tm-addon-menu');
    if (addonMenu) {
        // Use event delegation for folder items
        addonMenu.addEventListener('click', function(e) {
            const folderItem = e.target.closest('.tm-folder-item');
            if (folderItem) {
                e.preventDefault();
                e.stopPropagation();
                
                const folderContent = folderItem.querySelector('.tm-folder-content-wrapper');
                if (folderContent) {
                    folderContent.classList.add('active');
                    document.body.style.overflow = 'hidden'; // Prevent scrolling when folder is open
                }
            }
            
            // Handle folder close buttons with the same delegation
            const closeButton = e.target.closest('.tm-folder-close');
            if (closeButton) {
                e.preventDefault();
                e.stopPropagation();
                
                const folderContent = closeButton.closest('.tm-folder-content-wrapper');
                if (folderContent) {
                    folderContent.classList.remove('active');
                    document.body.style.overflow = ''; // Restore scrolling
                }
            }
        });
    }

    // Optimize document click handler for closing folders
    document.addEventListener('click', function(e) {
        // Only process if there are active folders
        const activeFolders = document.querySelectorAll('.tm-folder-content-wrapper.active');
        if (activeFolders.length === 0) return;
        
        // Check if click is outside any active folder
        let clickedOutside = true;
        activeFolders.forEach(function(folderContent) {
            if (folderContent.contains(e.target) || e.target.closest('.tm-folder-item')) {
                clickedOutside = false;
            }
        });
        
        // Only update DOM if we need to close folders
        if (clickedOutside) {
            activeFolders.forEach(function(folderContent) {
                folderContent.classList.remove('active');
            });
            document.body.style.overflow = ''; // Restore scrolling
        }
    });

    // Optimize link handling with event delegation
    const menuContainer = document.querySelector('.the-menu');
    if (menuContainer) {
        menuContainer.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;
            
            const href = link.getAttribute('href');
            if (href.includes('#')) {
                e.preventDefault();
                return;
            }

            var pageLoader = document.getElementById('tm-pageLoader');
            if (pageLoader) {
                pageLoader.style.display = 'block';
            }

            e.preventDefault();

            setTimeout(function() {
                window.location.href = href;
            }, 100);
        });
    }

    // SVG cache
    const svgCache = new Map();

    // Function to fetch and cache SVG content
    async function fetchAndCacheSvg(url) {
        if (svgCache.has(url)) {
            return svgCache.get(url);
        }
        
        try {
            const response = await fetch(url);
            const svgContent = await response.text();
            svgCache.set(url, svgContent);
            return svgContent;
        } catch (error) {
            console.error('Error fetching SVG:', error);
            return null;
        }
    }

    // Function to render SVG icon
    async function renderSvgIcon(container, iconUrl) {
        if (!iconUrl) return;
        
        try {
            const svgContent = await fetchAndCacheSvg(iconUrl);
            if (svgContent) {
                container.innerHTML = svgContent;
                
                // Optimize SVG rendering
                const svgElement = container.querySelector('svg');
                if (svgElement) {
                    // Set width and height attributes for better rendering
                    if (!svgElement.hasAttribute('width')) {
                        svgElement.setAttribute('width', '24');
                    }
                    if (!svgElement.hasAttribute('height')) {
                        svgElement.setAttribute('height', '24');
                    }
                    
                    // Add viewBox if not present
                    if (!svgElement.hasAttribute('viewBox')) {
                        svgElement.setAttribute('viewBox', '0 0 24 24');
                    }
                    
                    // Optimize SVG attributes
                    svgElement.setAttribute('aria-hidden', 'true');
                    svgElement.setAttribute('focusable', 'false');
                    
                    // Apply color to SVG paths
                    const paths = svgElement.querySelectorAll('path');
                    paths.forEach(path => {
                        if (!path.hasAttribute('fill')) {
                            path.setAttribute('fill', 'currentColor');
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error rendering SVG:', error);
        }
    }

    // Update menu icons
    function updateMenuIcons() {
        const menuItems = document.querySelectorAll('.distm-menu-item');
        menuItems.forEach(async (item) => {
            const iconUrl = item.getAttribute('data-icon-url');
            const iconContainer = item.querySelector('.distm-icon');
            
            if (iconUrl && iconContainer) {
                await renderSvgIcon(iconContainer, iconUrl);
            }
        });
    }

    // Initialize when DOM is loaded
    if (typeof distmSvgData !== 'undefined' && distmSvgData.svgUrls && distmSvgData.svgUrls.length > 0) {
        // Preload all SVGs
        distmSvgData.svgUrls.forEach(url => {
            fetchAndCacheSvg(url);
        });
    }
    
    // Initialize menu icons
    updateMenuIcons();
    
    // Event delegation for menu links
    document.addEventListener('click', function(event) {
        const menuLink = event.target.closest('.distm-menu-link');
        if (menuLink) {
            const menuItem = menuLink.closest('.distm-menu-item');
            if (menuItem) {
                // Handle menu item click
                const iconUrl = menuItem.getAttribute('data-icon-url');
                const iconContainer = menuItem.querySelector('.distm-icon');
                
                if (iconUrl && iconContainer) {
                    renderSvgIcon(iconContainer, iconUrl);
                }
            }
        }
    });
});