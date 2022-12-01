import App from './App'
import store from '@/store';
import {iconUrl,apiUr2} from './api/common.js'
import {platformurl} from './api/common.js'

Vue.prototype.$store = store
import $ from '@/common/fun.js';
Vue.prototype.$ = $

// / uview框架
import uView from "uview-ui";
Vue.use(uView);

import api from '@/common/api.js'
Vue.prototype.$api = api

import $throttle from '@/common/throttle.js';
Vue.prototype.$throttle = $throttle

// #ifdef APP-PLUS
Vue.prototype.module = uni.requireNativePlugin("TestModule")
// #endif


import zdyDialog from '@/components/zdy-popDialog/zdy-popDialog.vue';
Vue.component('zdyDialog',zdyDialog)

Vue.prototype.imgUrl = "http://zhs.admin.cpsdb.com/"
Vue.prototype.iconUrl =  iconUrl
Vue.prototype.apiUr2 = apiUr2
Vue.prototype.platformurl =  platformurl

Vue.prototype.$statusBarHeight = uni.getSystemInfoSync().statusBarHeight / (uni.upx2px(100) / 100)
Vue.prototype.$customBarH = uni.getSystemInfoSync().statusBarHeight / (uni.upx2px(100) / 100)
// #ifndef MP
Vue.prototype.$customBarH = uni.getSystemInfoSync().platform == 'android'? (uni.getSystemInfoSync().statusBarHeight + 50)  / (uni.upx2px(100) / 100):(uni.getSystemInfoSync().statusBarHeight + 45)  / (uni.upx2px(100) / 100)
// #endif


// #ifndef VUE3
import Vue from 'vue'
Vue.config.productionTip = false
App.mpType = 'app'
const app = new Vue({
    ...App,
	store
})
app.$mount()
// #endif

// #ifdef VUE3
import { createSSRApp } from 'vue'
export function createApp() {
  const app = createSSRApp(App)
  return {
    app,
	store
  }
}
// #endif