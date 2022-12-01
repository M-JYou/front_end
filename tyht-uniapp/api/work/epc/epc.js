import request from '@/utils/request'
// 获取工程记录列表
export function getEpcRecord(query) {
  return request({
    'url': '/api/epc/record/list',
    'method': 'get',
    'params': query
  })
}

// 获取所有工程记录列表
export function getEpcRecords(id) {
  return request({
    'url': '/api/epc/record/list/all',
    'method': 'get'
  })
}

// 获取工程结算列表
export function getEpcStatement(query) {
  return request({
    'url': '/api/epc/statement/list',
    'method': 'get',
    'params': query
  })
}
