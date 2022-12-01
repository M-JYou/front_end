import request from '@/utils/request'
// 获取最近七天的收付记录
export function getSevenRecord() {
  return request({
    'url': '/api/fi/receipt_payment/seven_record',
    'method': 'get'
  })
}

// 获取七天内的盈亏红线
export function getRedLine() {
  return request({
    'url': '/api/fi/red_line/7',
    'method': 'get'
  })
}

// 获取 今日、上周、上月、上季和上年的5条收付记录
export function getFiveRecord() {
  return request({
    'url': '/api/fi/receipt_payment/five_record',
    'method': 'get'
  })
}










// // 获取今日收付记录
// export function getTodayRecord() {
//   return request({
//     'url': '/api/fi/receipt_payment/one_record/1',
//     'method': 'get'
//   })
// }

// // 获取上周收付记录
// export function getLastweekRecord() {
//   return request({
//     'url': '/api/fi/receipt_payment/one_record/2',
//     'method': 'get'
//   })
// }

// // 获取上月收付记录
// export function getLastmonthRecord() {
//   return request({
//     'url': '/api/fi/receipt_payment/one_record/3',
//     'method': 'get'
//   })
// }

// // 获取上季收付记录
// export function getLastseasonRecord() {
//   return request({
//     'url': '/api/fi/receipt_payment/one_record/4',
//     'method': 'get'
//   })
// }

// // 获取去年收付记录
// export function getYesteryearRecord() {
//   return request({
//     'url': '/api/fi/receipt_payment/one_record/5',
//     'method': 'get'
//   })
// }
