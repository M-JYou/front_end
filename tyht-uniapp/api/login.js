import request from '@/utils/request'

// 登录方法
export function login(phoneNumber, password, code, uuid) {
  const data = {
    phoneNumber,
    password,
    code,
    uuid
  }
  return request({
    'url': '/api/login',
    headers: {
      isToken: false
    },
    'method': 'post',
    'data': data
  })
}

// 获取用户详细信息
export function getInfo() {
  return request({
    'url': '/api/getInfo',
    'method': 'get'
  })
}

// 退出方法
export function logout() {
  return request({
    'url': '/api/logout',
    'method': 'post'
  })
}

// 获取验证码
export function getCodeImg() {
  return request({
    'url': '/api/captchaImage',
    headers: {
      isToken: false
    },
    method: 'get',
    timeout: 20000
  })
}
