import request from '@/utils/request'
// 获取债务清欠列表
export function getDebtDispose(query) {
  return request({
    'url': '/api/law/debt/list',
    'method': 'get',
    'params': query
  })
}
// 获取合同评审列表
export function getLawCr(query) {
  return request({
    'url': '/api/law/cr/list',
    'method': 'get',
    'params': query
  })
}
// 获取案件处理列表
export function getLawCh(query) {
  return request({
    'url': '/api/law/ch/list',
    'method': 'get',
    'params': query
  })
}
// 获取紧急事件列表
export function getLawExigency(query) {
  return request({
    'url': '/api/law/exigency/list',
    'method': 'get',
    'params': query
  })
}
