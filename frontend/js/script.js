const targetElement = document.querySelector('.tm-scrolling');

function setOpacity(opacity) {
  if (targetElement) {
    targetElement.style.opacity = opacity;
  }
}

let scrollTimeout = null;

if (targetElement) {
  window.addEventListener('scroll', () => {
    setOpacity(0.2);
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
      setOpacity(1);
    }, 150);
  });
}

document.addEventListener("DOMContentLoaded", function() {
    // Debug function to check folder items
    function debugFolderItems() {
        console.log('Debugging folder items...');
        const folderItems = document.querySelectorAll('.tm-folder-item');
        console.log('Found folder items:', folderItems.length);
        
        folderItems.forEach((item, index) => {
            console.log(`Folder item ${index}:`, item);
            const folderContent = item.querySelector('.tm-folder-content-wrapper');
            console.log(`Folder content for item ${index}:`, folderContent);
            
            if (folderContent) {
                console.log(`Folder content structure for item ${index}:`, {
                    hasActiveClass: folderContent.classList.contains('active'),
                    hasFolderOpeningClass: folderContent.classList.contains('folder-opening'),
                    display: folderContent.style.display,
                    opacity: folderContent.style.opacity,
                    computedDisplay: window.getComputedStyle(folderContent).display,
                    computedOpacity: window.getComputedStyle(folderContent).opacity
                });
            }
        });
    }
    
    // Call the debug function
    debugFolderItems();
    
    const icon = document.querySelector('.tm-featured');
    const menuPopup = document.querySelector('.tm-addon-menu-wrapper');
    const featuredBg = document.querySelector('.tm-featured-bg');

    function closeMenu() {
        menuPopup.style.animation = 'slideOut 0.5s forwards';
        icon.classList.remove('active');
        if (featuredBg) {
            featuredBg.classList.remove('expanded');
        }
        setTimeout(() => {
            menuPopup.classList.remove('show');
            menuPopup.style.display = 'none';
        }, 500);
    }

    function openMenu() {
        menuPopup.classList.add('show');
        menuPopup.style.display = 'flex';
        menuPopup.style.animation = 'slideIn 0.5s forwards';
        icon.classList.add('active');
        if (featuredBg) {
            featuredBg.classList.add('expanded');
        }
    }

    if (icon && menuPopup) {
        icon.addEventListener('click', function() {
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

    // Handle folder items in the addon menu
    const folderItems = document.querySelectorAll('.tm-folder-item');
    folderItems.forEach(function(folderItem) {
        folderItem.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Folder item clicked:', this);
            
            // Find the folder content wrapper
            const folderContent = this.querySelector('.tm-folder-content-wrapper');
            console.log('Folder content found:', folderContent);
            
            if (folderContent) {
                console.log('Opening folder content...');
                
                // Make it visible immediately
                folderContent.style.display = 'flex';
                console.log('Set display to flex');
                
                // Force a reflow
                void folderContent.offsetWidth;
                
                // Add active class to make it visible
                folderContent.classList.add('active');
                console.log('Added active class');
                
                // Add animation class
                folderContent.classList.add('folder-opening');
                console.log('Added folder-opening class');
                
                // Prevent scrolling
                document.body.style.overflow = 'hidden';
                
                // Remove animation class after animation completes
                setTimeout(() => {
                    folderContent.classList.remove('folder-opening');
                    console.log('Removed folder-opening class');
                }, 500);
            } else {
                console.error('Folder content not found for this folder item');
            }
        });
    });

    // Handle folder close buttons
    const folderCloseButtons = document.querySelectorAll('.tm-folder-close');
    folderCloseButtons.forEach(function(closeButton) {
        closeButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const folderContent = this.closest('.tm-folder-content-wrapper');
            if (folderContent) {
                // Add closing animation class
                folderContent.classList.add('folder-closing');
                
                // Remove active class and hide after animation completes
                setTimeout(() => {
                    folderContent.classList.remove('active');
                    folderContent.classList.remove('folder-closing');
                    folderContent.style.display = 'none';
                    document.body.style.overflow = ''; // Restore scrolling
                }, 500);
            }
        });
    });

    // Close folder when clicking outside the folder content
    document.addEventListener('click', function(e) {
        const folderContents = document.querySelectorAll('.tm-folder-content-wrapper.active');
        folderContents.forEach(function(folderContent) {
            if (!folderContent.contains(e.target) && !e.target.closest('.tm-folder-item')) {
                // Add closing animation class
                folderContent.classList.add('folder-closing');
                
                // Remove active class and hide after animation completes
                setTimeout(() => {
                    folderContent.classList.remove('active');
                    folderContent.classList.remove('folder-closing');
                    folderContent.style.display = 'none';
                    document.body.style.overflow = ''; // Restore scrolling
                }, 500);
            }
        });
    });

    const links = document.querySelectorAll('.the-menu a');
    links.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
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
    });
});