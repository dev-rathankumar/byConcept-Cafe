/**
 * Block dependencies
 */     
import './style.scss';
import './editor.scss';

/**
 * Internal block libraries
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

import { deprecated } from './deprecated';
import { RevSlider } from './revslider';

/**
 * Register block
 */
export default registerBlockType(
    'themepunch/revslider',
    {
        title: __( 'Slider Revolution', 'revslider' ),
        description: __( 'Add your Slider Revolution Module!', 'revslider' ),
        category: 'themepunch',
        icon: {
          src:  'update',
          background: 'rgb(94, 53, 177)',
          color: 'white',
          viewbox: "0 0 28 28"
        },        
        keywords: [
            __( 'Banner', 'revslider' ),
            __( 'CTA', 'revslider' ),
            __( 'Slider', 'revslider' ),
        ],
        attributes: {
          checked: {
            type: 'boolean',
            default: false
          },
          modal: {
            type: 'boolean',
            default: false
          },
          content: {
              selector: '.revslider',
              type: 'string',
              source: 'text',
          },
          text: {
            selector: '.revslider',
            type: 'string',
            source: 'text',
          },
          slidertitle: {
              selector: '.revslider',
              type: 'string',
              source: 'attribute',
              attribute: 'data-slidertitle',
          },
          sliderImage: {
             type:'string'
          },
          hideSliderImage:{
              boolean: false
          },
          alias: {
            type: 'string'
          },
          zindex: {
            type: 'string'
          }
        },
        edit: props => {
          const { setAttributes } = props;
          return (
            <div>
              <RevSlider {...{ setAttributes, ...props }} />
            </div>
          );
        },
        deprecated,
        save: props => {
          const { attributes: { text, content, slidertitle, modal, zindex } } = props;
          let style;
          style = zindex ? "z-index:"+zindex+";" : "";
          let shortcode = !content && text ? text : content;
          return (
            <div className="revslider" data-modal={modal} data-slidertitle={slidertitle} style={style}>
               {shortcode}
            </div>
          );
        }
    },
);