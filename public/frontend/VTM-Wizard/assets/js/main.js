// // import vemBootstrap from './modules/botstrapimport.js';
// import * as animacao from './modules/animation.js';
// // import * as carouselClick from './modules/carousel-change.js';
// import * as config from './modules/swiper__props.js'; //Configs do Swiper
// // import lineClamp from './modules/line-clamp.js'; //line Clamp
// // import * as darkMode from './modules/dark-mode.js'; //line Clamp
// import linkDentroLink from './modules/a-dentro-a.js'; //link dentor de link
// // import progressBar from './modules/progress-bar.js'; //progress bar
// import firefox from './modules/firefox-checker.js'; //progress bar
// import mudaDeAcordoComCarouselAtivo from "./modules/carousel-ativo.js"
// import swiper from "./modules/swiperTrabalho.js"
// import menu from './modules/menu.js'; //js do menu

import Dom from './modules/constructors.js'; //selecionar elementos
import Swiper from 'https://unpkg.com/swiper/swiper-bundle.esm.browser.min.js'


// // let mediaQuery = window.matchMedia('(min-width: 1024px)').matches
// if (mediaQuery) {
//     // animacao.animaAoScroll()
// }


// PAGES 
const pageModelo1 = new Dom().el("#page__modelo1")
const pageModelo2 = new Dom().el("#page__modelo2")
if (pageModelo1) { // ★ HOME  
    new Dom().bodyClass("body__modelo1");
    // const swiperIntro = new Swiper(".parceria__carousel", config.props)


}

if (pageModelo2) { // ★ HOME  
    new Dom().bodyClass("body__modelo2");
    // const swiperIntro = new Swiper(".parceria__carousel", config.props)


}
// else if (pageClientes) { // ★ Clientes  
//      new Dom().bodyClass("body__clientes");

// }


document.addEventListener("DOMContentLoaded", () => {
    document.body.classList.add("dcl");

    var url = new URL(window.location.href);
    var pid = url.searchParams.get("pergunta");
    var pid_li = document.querySelector("[data-pid='"+pid+"']");
    var item = pid_li.closest(".accordion-item");
    var mid_btn = item.querySelector(".accordion-button");

    pid_li.style.backgroundColor = "rgba(0,0,0,.1)";
    simulateClick(mid_btn);

    function simulateClick(elem) {
        
        var evt = new MouseEvent('click', {
            bubbles: true,
            cancelable: true,
            view: window
        });
        
        var canceled = !elem.dispatchEvent(evt);
    }

    function next(elem, selector) {

        var nextElem = elem.nextElementSibling;
    
        if (!selector) {
            return nextElem;
        }
    
        if (nextElem && nextElem.matches(selector)) {
            return nextElem;
        }
    
        return null;
    }
});