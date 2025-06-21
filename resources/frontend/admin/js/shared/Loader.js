import View from "../core/View";

export default class Loader extends View
{   
    _defineProps(){
        return {
            color: 'var(--clr-primary-800)',
            open: false,
        }
    }

    _defineState(){
        return {
            ...this._props
        }
    }

    _template(){
        const {color, open} = this._state;
        return `
            <div class="no-page-loader" style="display: ${open ? 'flex' : 'none'}">
                <div class="no-page-loader-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                        <radialGradient id="a5" cx=".66" fx=".66" cy=".3125" fy=".3125" gradientTransform="scale(1.5)"><stop offset="0" stop-color="${color}"></stop>
                            <stop offset=".3" stop-color="${color}" stop-opacity=".9"></stop>
                            <stop offset=".6" stop-color="${color}" stop-opacity=".6"></stop>
                            <stop offset=".8" stop-color="${color}" stop-opacity=".3"></stop>
                            <stop offset="1" stop-color="${color}" stop-opacity="0"></stop>
                        </radialGradient>
                        <circle transform-origin="center" fill="none" stroke="url(#a5)" stroke-width="15" stroke-linecap="round" stroke-dasharray="200 1000" stroke-dashoffset="0" cx="100" cy="100" r="70">
                            <animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="2" values="360;0" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform>
                        </circle>
                        <circle transform-origin="center" fill="none" opacity=".2" stroke="${color}" stroke-width="15" stroke-linecap="round" cx="100" cy="100" r="70">
                        </circle>
                    </svg>
                </div>
            </div>
        `;
    }

    show(){
        this.setState({open: true});
    }

    hide(){
        this.setState({open: false});
    }
}