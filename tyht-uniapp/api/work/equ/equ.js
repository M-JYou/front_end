import request from '@/utils/request'
// 获取自有设备列表
export function getInventoryList(query) {
  return request({
    'url': '/api/equ/inventory/list',
    'method': 'get',
    'params': query
  })
}
// 获取设备租赁列表
export function getLeaseList(query) {
  return request({
    'url': '/api/equ/lease/list',
    'method': 'get',
    'params': query
  })
}
