// /**
// 	* 封装接口请求
import {
	httpError
} from './error.js'
// 域名
export const platformurl = "http://132.232.17.10";
export const apiUr2 = "http://132.232.17.10:81";
export const iconUrl = 'http://121.5.161.39:8000' // 图标icon地址
export const baseUrl = 'http://121.5.161.39:8008';
const token = ("Bearer " +uni.getStorageSync("token")) || ''
// console.log(token,'拿到token值')


export const httpApi = (url, data, method) => {
	return new Promise((resolve, reject) => {
		let AuthoriZation = ''
		if (uni.getStorageSync('token') !== '') {
			AuthoriZation = 'Bearer ' + uni.getStorageSync('token')
	

		}
		if (method === 'POST' || method === 'post') {
			uni.showLoading({
				mask: true
			})
		}
		uni.request({
			url: baseUrl + url,
			data,
			method,
			header: {
				'Authori-zation': AuthoriZation,
				'Content-Type': 'application/json',
				'AuthoriZation' : 'Bearer ' + uni.getStorageSync('token')
			},
			success(res) {
				// console.log(res, '请求返回数据')
				const {
					data: {
						status,
						msg
					}
				} = res
				// 状态码
				const error = [200, 404, 500]
				const action = error.includes(status) ? httpError.get(status) : httpError.get(
					'default')
				action.call(this, msg, method)
				if ([410000, 410001, 410002, 410003].includes(status)) {
					uni.navigateTo({
						title: "登录成功",
						url: '/pages/index/index'
					});
				}
				resolve(res.data)
			},
			fail() {

			},
		})
	})
}


export const httpApi2 = (url, data, method) => {
	return new Promise((resolve, reject) => {
		let AuthoriZation = ''
		if (uni.getStorageSync('token') !== '') {
			AuthoriZation = 'Bearer ' + uni.getStorageSync('token')
	

		}
		if (method === 'GET' || method === 'get') {
			uni.showLoading({
				mask: true
			})
		}
		uni.request({
			url: apiUr2 + url,
			data,
			method,
			header: {
				'Authori-zation': AuthoriZation,
				'Content-Type': 'application/json',
				// 'AuthoriZation' : 'Bearer ' + uni.getStorageSync('token')
			},
			success(res) {
				const {
					data: {
						status,
						msg
					}
				} = res
				// 状态码
				const error = [200, 404, 500]
				const action = error.includes(status) ? httpError.get(status) : httpError.get(
					'default')
				action.call(this, msg, method)
				if ([410000, 410001, 410002, 410003].includes(status)) {
					uni.navigateTo({
						title: "登录成功",
						url: '/pages/index/index'
					});
				}
				resolve(res.data)
			},
			fail() {

			},
		})
	})
}

