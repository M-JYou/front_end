import request from '@/utils/request'
// 获取库存材料列表
export function getInventList(query) {
  return request({
    'url': '/api/material/inventory/list',
    'method': 'get',
    'params': query
  })
}
// 获取材料购置列表
export function getPurchaseList(query) {
  return request({
    'url': '/api/material/purchase/list',
    'method': 'get',
    'params': query
  })
}
