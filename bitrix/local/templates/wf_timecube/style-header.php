<!--/*стили для хедера*/-->
<style>
* {
    font-family: Roboto, Helvetica, Arial, sans-serif;
}

html {
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    -ms-overflow-style: scrollbar
}

*, :after, :before {
    -webkit-box-sizing: inherit;
    box-sizing: inherit
}

.container {
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto
}

.container-fluid {
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto
}

.d-none {
    display: none !important
}

.collapse:not(.show) {
    display: none
}

button::-moz-focus-inner {
    padding: 0;
    border: 0
}

@font-face {
    font-family: icomoon;
    src: url(/assets/fonts/icomoon/icomoon.ttf?tx9eb0) format("truetype"), url(/assets/fonts/icomoon/icomoon.woff?tx9eb0) format("woff"), url(/assets/fonts/icomoon/icomoon.svg?tx9eb0#icomoon) format("svg");
    font-weight: 400;
    font-style: normal;
    font-display: swap
}

.svg-icon {
    font-family: icomoon !important;
    speak: none;
    font-style: normal;
    font-weight: 400;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale
}

.icon-close:before {
    content: ""
}

.icon-list:before {
    content: ""
}

body, html {
    font-size: 14px;
    line-height: 1.4;
    color: #101010
}

html {
    height: 100%
}

a {
    color: #367c56;
    text-decoration: underline
}

.link-green {
    color: #367c56
}

a, button {
    border-radius: 0
}

.btn-primary {
    background: #367c56;
    color: #fff
}

.btn-rounded {
    -webkit-box-shadow: 4px 4px 20px rgba(0, 0, 0, .35);
    box-shadow: 4px 4px 20px rgba(0, 0, 0, .35);
    border: none;
    width: 64px;
    height: 64px;
    border-radius: 32px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center
}

.btn-rounded__icon {
    font-size: 24px
}

body {
    margin: 0;
    padding: 0;
    font-family: Roboto, Helvetica, Arial, sans-serif;
    font-weight: 400;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale
}

.logo {
    display: block;
    width: 100%;
    max-width: 240px;
    min-width: 200px;
    line-height: 1
}

@media (max-width: 479px) {
    .logo {
        width: 200px
    }
}

.page-wrapper {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: horizontal;
    -webkit-box-direction: reverse;
    -ms-flex-direction: row-reverse;
    flex-direction: row-reverse;
    overflow: auto
}

.page-wrapper .sidebar {
    position: relative
}

.nav-main {
    list-style: none;
    padding: 15px 0 0;
    margin-top: 0
}

.nav-main__item {
    font-size: 15px;
    font-weight: 500
}

.nav-main__item + .nav-main__item {
    margin-top: 3px
}

.nav-main__link.dd {
    position: relative
}

.nav-main__link.dd:after {
    font-family: icomoon;
    content: "";
    display: block;
    position: absolute;
    right: -12px;
    top: 50%;
    -webkit-transform: translateY(-50%);
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
    font-size: 24px;
    color: #48596c;
    opacity: .54
}

.nav-main__link.collapsed:after {
    content: ""
}

.nav-main a {
    color: #000;
    text-decoration: none;
    display: block;
    padding-top: 5px;
    padding-bottom: 5px
}

.nav-main__submenu {
    list-style: none;
    padding: 0
}

.nav-main__submenu li {
    font-size: 14px;
    font-weight: 400
}

.nav-main__submenu a {
    color: #4c545d
}

.sidebar {
    -webkit-box-flex: 0;
    -ms-flex: 0 0 auto;
    flex: 0 0 auto;
    width: 0
}

.sidebar-top {
    padding-top: 25px;
    padding-bottom: 0
}

.sidebar-slide {
    -webkit-box-shadow: 5px 0 10px rgba(0, 0, 0, .15);
    box-shadow: 5px 0 10px rgba(0, 0, 0, .15);
    position: relative;
    min-width: 280px;
    height: 100%;
    background-color: #fff;
    left: 0
}

.sidebar-slide .btn-close {
    position: absolute;
    left: 0;
    top: 25px;
    -webkit-transform: translate(-60%, 50%);
    -ms-transform: translate(-60%, 50%);
    transform: translate(-60%, 50%);
    z-index: 500
}

.sidebar-slide .container {
    padding-left: 20px;
    padding-right: 20px
}

.sidebar-slide__content {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    overflow: auto;
    padding-right: 80px
}

.sidebar-slide__content::-webkit-scrollbar {
    width: 3px;
    height: 3px
}

.sidebar-slide__content::-webkit-scrollbar-button, .sidebar-slide__content::-webkit-scrollbar-track {
    background-color: rgba(0, 0, 0, .1)
}

.sidebar-slide__content::-webkit-scrollbar-track-piece {
    background-color: rgba(0, 0, 0, .1)
}

.sidebar-slide__content::-webkit-scrollbar-thumb {
    height: 50px;
    background-color: rgba(0, 0, 0, .3);
    border-radius: 3px
}

.sidebar-slide__content::-webkit-scrollbar-corner {
    background-color: rgba(0, 0, 0, .1)
}

.sidebar-slide__content::-webkit-resizer {
    background-color: rgba(0, 0, 0, .3)
}

.sidebar-controls {
    display: none;
    position: fixed;
    z-index: 50;
    -webkit-transform: translate(-50%);
    -ms-transform: translate(-50%);
    transform: translate(-50%);
    left: 65px;
    top: 60px
}

.nav-top {
    list-style: none;
    padding: 0;
    margin-left: -.3rem;
    margin-right: -.3rem
}

.nav-top li {
    display: inline-block;
    padding-left: .3rem;
    padding-right: .3rem
}

.sidebar-slide .nav-top {
    list-style: none;
    padding: 0;
    margin-left: -.3rem;
    margin-right: -.3rem;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: distribute;
    justify-content: space-around
}

.sidebar-slide .nav-top li {
    display: inline-block;
    padding-left: .3rem;
    padding-right: .3rem
}
</style>