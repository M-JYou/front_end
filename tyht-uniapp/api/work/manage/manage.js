import request from '@/utils/request'
// 获取分包项目列表
export function getManageSubcontract(query) {
  return request({
    'url': '/api/manage/subcontract/list',
    'method': 'get',
    'params': query
  })
}
// 获取投标项目列表
export function getManageBidding(query) {
  return request({
    'url': '/api/manage/bidding/list',
    'method': 'get',
    'params': query
  })
}
// 获取租赁项目列表
export function getManageLease(query) {
  return request({
    'url': '/api/manage/lease/list',
    'method': 'get',
    'params': query
  })
}
