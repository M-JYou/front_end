import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '../components/Login.vue'
Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    component: Login
  },
  {
    path: '/home',
    redirect: '/welcome',
    children: [
      { path: '/welcome', component: () => import('../components/index/Welcome.vue') },
      { path: '/users', component: () => import('../components/user/users.vue') }
    ],
    component: () => import('../components/Home.vue')
  }
]

const router = new VueRouter({
  routes
})

// 配置路由守卫
router.beforeEach((to, from, next) => {
  // 如果访问登录页，则放行
  if (to.path === '/login') {
    return next()
  }

  // 未登录时访问其他页面，则先访问登录页
  const userInfo = JSON.parse(sessionStorage.getItem('userInfo'))
  if (!userInfo) {
    return next('/login')
  }

  // 如果用户已经登录，则放行
  next()
})

export default router
