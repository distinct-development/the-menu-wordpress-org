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