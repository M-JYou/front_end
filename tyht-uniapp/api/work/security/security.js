import request from '@/utils/request'
// 获取安全人员列表
export function getSecurityPerson(query) {
  return request({
    'url': '/api/security/personnel/list',
    'method': 'get',
    'params': query
  })
}
// 获取安全培训列表
export function getSecurityTrain(query) {
  return request({
    'url': '/api/security/training/list',
    'method': 'get',
    'params': query
  })
}
// 获取安全巡检列表
export function getSecurityPatrol(query) {
  return request({
    'url': '/api/security/patrol/list',
    'method': 'get',
    'params': query
  })
}
// 获取工程安全列表
export function getSecurityEpc(query) {
  return request({
    'url': '/api/security/epc/list',
    'method': 'get',
    'params': query
  })
}
