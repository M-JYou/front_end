/**
	* 接口请求失败返回状态码处理
	*/

export const httpError = new Map([
	[200, (title, method) => {
		if (method === 'POST') {
			uni.hideLoading()
			if(title!='登录成功'){
				uni.showToast({
					title:'登录成功',
					icon: 'success'
				})
			}
		}
	}],
	[401, (e, n) => {
		uni.showToast({
			title: '服务器地址错误，请联系客服',
			icon: 'none'
		})
	}],
	[500, (e, n) => {
		uni.showToast({
			title: '服务器异常，请联系客服',
			icon: 'none'
		})
	}],
	['default', (title, n) => {
			// uni.showToast({
			// 	title:"加载成功",
			// 	icon: 'none',
			// 	mask:false,
				
			// })
			setTimeout(function () {
				uni.hideLoading();
			}, 100);

		
	}]
])




