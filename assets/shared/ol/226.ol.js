"use strict";(self.webpackChunkol=self.webpackChunkol||[]).push([[226],{165:function(e,t,r){function n(e,t){let r=e.length-t,n=0;do{for(let r=t;r>0;r--)e[n+t]+=e[n],n++;r-=t}while(r>0)}function o(e,t,r){let n=0,o=e.length;const i=o/r;for(;o>t;){for(let r=t;r>0;--r)e[n+t]+=e[n],++n;o-=t}const s=e.slice();for(let t=0;t<i;++t)for(let n=0;n<r;++n)e[r*t+n]=s[(r-n-1)*i+t]}r.d(t,{Z:function(){return i}});class i{async decode(e,t){const r=await this.decodeBlock(t),i=e.Predictor||1;if(1!==i){const t=!e.StripOffsets;return function(e,t,r,i,s,c){if(!t||1===t)return e;for(let e=0;e<s.length;++e){if(s[e]%8!=0)throw new Error("When decoding with predictor, only multiple of 8 bits are supported.");if(s[e]!==s[0])throw new Error("When decoding with predictor, all samples must have the same size.")}const l=s[0]/8,a=2===c?1:s.length;for(let c=0;c<i&&!(c*a*r*l>=e.byteLength);++c){let i;if(2===t){switch(s[0]){case 8:i=new Uint8Array(e,c*a*r*l,a*r*l);break;case 16:i=new Uint16Array(e,c*a*r*l,a*r*l/2);break;case 32:i=new Uint32Array(e,c*a*r*l,a*r*l/4);break;default:throw new Error(`Predictor 2 not allowed with ${s[0]} bits per sample.`)}n(i,a)}else 3===t&&(i=new Uint8Array(e,c*a*r*l,a*r*l),o(i,a,l))}return e}(r,i,t?e.TileWidth:e.ImageWidth,t?e.TileLength:e.RowsPerStrip||e.ImageLength,e.BitsPerSample,e.PlanarConfiguration)}return r}}},966:function(e,t,r){r.r(t),r.d(t,{default:function(){return o}});var n=r(165);class o extends n.Z{decodeBlock(e){return e}}}}]);