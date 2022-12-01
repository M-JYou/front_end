import Vuex from 'vuex'
import Vue from 'vue'
//import Logger from 'vuex/dist/logger'
const debug = process.env.NODE_ENV !== 'production'

const state = {
  active: 'index',
  animate: 'zoomIn',
  inactiveColor: '#7E7E7E',
  activeColor: '#617CFC',
  textTop:'1px',
  raisedeScale:1.1,
  tabbars: [
    {
      name: 'index',
      text: '首页',
      icon: '/static/tabbars/zindex.png',
	  iconActive: '/static/tabbars/zindex-action.png',
      path: '/pages/index/index',
    },
    {
      name: 'nearby',
      text: '资讯',
      icon: '/static/tabbars/notice.png',
	  iconActive: '/static/tabbars/notice-action.png',
      path: '/pages/nearby/index'
    },
   
    {
      name: 'informs',
      text: '通知',
      icon: '/static/tabbars/monitor.png',
      iconActive: '/static/tabbars/monitor-action.png',
      path: '/pages/informs/index'
    },
    {
      name: 'mine',
      text: '个人中心',
      icon: '/static/tabbars/zmy.png',
      iconActive: '/static/tabbars/info-action.png',
      path: '/pages/mine/index'
    }
  ]
}

const getters = {
  active: state => state.active,
  animate: state => state.animate,
  tabbars: state => state.tabbars
}

const mutations = {
  SET_ACTIVE (state, data) {
    state.active = data
  },
  SET_TABBARS (state, data) {
    state.tabbars = data
  },
}

Vue.use(Vuex)

const store = new Vuex.Store({
  state,
  getters,
  mutations,
  strict: debug,
  //plugins: debug ? [Logger()] : []
})

export default store