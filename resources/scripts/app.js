import domReady from '@roots/sage/client/dom-ready';
import GLightbox from 'glightbox';

/**
 * Application entrypoint
 */
domReady(async () => {
  const htmlTags = document.querySelector("html");
  const darkThemeMq = window.matchMedia("(prefers-color-scheme: dark)");
  
  if (darkThemeMq.matches) {
    htmlTags.setAttribute("data-theme", "dark");

    darkThemeMq.addListener(e => {
      if (e.matches) {
        // Theme set to dark.
        htmlTags.removeAttribute("data-theme");
        htmlTags.setAttribute("data-theme", "dark");
      } else {
        // Theme set to light.
        htmlTags.removeAttribute("data-theme");
        htmlTags.setAttribute("data-theme", "light");
      }
    });
  } else {
    htmlTags.setAttribute("data-theme", "light");
  }

  // Initialize GLightbox for gallery blocks
  initializeGalleryLightbox();
});

/**
 * Initialize GLightbox for WordPress gallery blocks
 */
function initializeGalleryLightbox() {
  // Target all gallery images in WordPress gallery blocks
  const gallerySelector = '.wp-block-gallery .wp-image, .gallery .gallery-item a, .wp-block-image a';
  
  const lightbox = GLightbox({
    selector: gallerySelector,
    touchNavigation: true,
    loop: true,
    autoplayVideos: false,
    plyr: {
      config: {
        ratio: '16:9',
        youtube: {
          noCookie: true,
          rel: 0,
          showinfo: 0,
          iv_load_policy: 3
        },
        vimeo: {
          byline: false,
          portrait: false,
          title: false,
          speed: true,
          transparent: false
        }
      }
    },
    beforeSlideChange: (prev, current) => {
      // Add custom behavior if needed
      console.log('Slide changed from', prev, 'to', current);
    }
  });

  // Re-initialize lightbox for dynamically added content
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === 1) { // Element node
            const galleryElements = node.querySelectorAll && node.querySelectorAll(gallerySelector);
            if (galleryElements && galleryElements.length > 0) {
              lightbox.reload();
            }
          }
        });
      }
    });
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true
  });
}

/**
 * @see {@link https://webpack.js.org/api/hot-module-replacement/}
 */
if (import.meta.webpackHot) import.meta.webpackHot.accept(console.error);
