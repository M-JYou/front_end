import request from '@/utils/request'
// 获取内部工作人员信息
export function getStaff() {
  return request({
    'url': '/api/hr/staff/app/list',
    'method': 'get'
  })
}
// 获取协作队伍列表
export function getCooperative(query) {
  return request({
    'url': '/api/hr/cooperative/list',
    'method': 'get',
    'params': query
  })
}
