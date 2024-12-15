import domReady from '@roots/sage/client/dom-ready';

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
});

/**
 * @see {@link https://webpack.js.org/api/hot-module-replacement/}
 */
if (import.meta.webpackHot) import.meta.webpackHot.accept(console.error);
