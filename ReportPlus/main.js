import Vue from 'vue'
import App from './App'
import store from './store'
import Common from './static/js/common.js'
import Config from './static/js/config.js'

Vue.prototype.$Common = Common
Vue.prototype.$Config = Config
Vue.prototype.$store = store

/*格式化颜色*/
Vue.filter('formatColor', (time, data, type) => {
    if (type == "时间进度" || type == "系数进度") {
        return Config.COLOR[3];
    } else {
        if (Number(data) >= 100) {
            return Config.COLOR[2];
        } else if (Number(data) <= Number(time)) {
            return Config.COLOR[0];
        } else {
            return Config.COLOR[1];
        }
		
    }
})

const app = new Vue({
    ...App, store
})
app.$mount()
