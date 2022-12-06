import Vue from 'vue'
import App from './App.vue'
import router from './router'
import './plugins/element.js'

// 导入全局样式表
import './assets/css/global.css'
// 导入字体图标文件
import './assets/fonts/iconfont.css'
// 导入axios
import axios from 'axios'
Vue.prototype.$http = axios
// 设置默认请求路径
axios.defaults.baseURL = 'http://www.tangxiaoyang.vip:8888/api/v2/'
// 设置数据请求拦截器
axios.interceptors.request.use(config => {
  const userInfo = JSON.parse(sessionStorage.getItem('userInfo'))
  config.headers.Authorization = userInfo ? userInfo.token : ''
  return config
})


Vue.config.productionTip = false

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
