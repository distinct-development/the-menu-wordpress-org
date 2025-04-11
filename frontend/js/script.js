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
            
            const folderContent = this.querySelector('.tm-folder-content-wrapper');
            if (folderContent) {
                folderContent.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent scrolling when folder is open
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
                folderContent.classList.remove('active');
                document.body.style.overflow = ''; // Restore scrolling
            }
        });
    });

    // Close folder when clicking outside the folder content
    document.addEventListener('click', function(e) {
        const folderContents = document.querySelectorAll('.tm-folder-content-wrapper.active');
        folderContents.forEach(function(folderContent) {
            if (!folderContent.contains(e.target) && !e.target.closest('.tm-folder-item')) {
                folderContent.classList.remove('active');
                document.body.style.overflow = ''; // Restore scrolling
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