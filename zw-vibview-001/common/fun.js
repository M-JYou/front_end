// 不含icon提示框
const toast = str => {
	return new Promise((resolve, reject) => {
		uni.showToast({
			title: str,
			icon: "none",
			duration: 2000,
			position: 'bottom',
			success: () => {
				setTimeout(() => {
					resolve
				}, 2000)
			}
		})
	})
};
// 成功提示框
const successToast = str => {
	return new Promise((resolve, reject) => {
		uni.showToast({
			title: str,
			icon: "success",
			duration: 2000,
			success: () => {
				setTimeout(() => {
					resolve()
				}, 2000)
			}
		})
	})
};
// loading
const showLoading = () => {
	return new Promise((resolve, reject) => {
		uni.showLoading({
			mask: true,
			success: () => {
				resolve()
			}
		})
	})
};
// tipLoading ==>提示loading
const tipLoading = str => {
	return new Promise((resolve, reject) => {
		uni.showLoading({
			title: str,
			success: () => {
				resolve()
			}
		})
	})
};
// 隐藏loading
const hideLoading = () => {
	return new Promise((resolve, reject) => {
		uni.hideLoading({
			success: () => {
				resolve()
			}
		})
	})
};
//自定义toast
const customToast = str => {
	return new Promise((resolve, reject) => {
		var html = '';
		html += '<div class="cusomtoast"><div class="uni-sample-toast">';
		html += '<p class="uni-simple-toast__text">' + str + '</p></div>'
		html += '</div>'
		setTimeout(() => {
			resolve
		}, 2000)
	})
};

// 处理时间
const timeHandle = (time) => {
	var unixtime = time;
	var unixTimestamp = new Date(unixtime * 1000);
	var Y = unixTimestamp.getFullYear(),
		M = ((unixTimestamp.getMonth() + 1) >= 10 ? (unixTimestamp.getMonth() + 1) : '0' + (unixTimestamp
			.getMonth() + 1)),
		D = (unixTimestamp.getDate() > 10 ? unixTimestamp.getDate() : '0' + unixTimestamp.getDate()),
		h = (unixTimestamp.getHours() < 10) ? "0" + unixTimestamp.getHours() : unixTimestamp.getHours(),
		min = (unixTimestamp.getMinutes() < 10) ? "0" + unixTimestamp.getMinutes() : unixTimestamp.getMinutes(),
		s = (unixTimestamp.getSeconds() < 10) ? "0" + unixTimestamp.getSeconds() : unixTimestamp.getSeconds();
	var toDay = Y + '年' + M + '月' + D + "日 " + h + ":" + min + ":" + s;
	if (time) {
		return toDay;
	} else {
		return '';
	}
}
// 存储本地数据
const setLocalData = (key, value) => {
	if (key && value) {
		uni.setStorageSync(key, JSON.stringify(value))
	}
}
// 获取本地数据
const getLocalData = (name, key) => {
	if (name && uni.getStorageSync(name) && uni.getStorageSync(name) != 'undefined') {
		if (key) {
			if (JSON.parse(uni.getStorageSync(name))[key]) {
				return JSON.parse(uni.getStorageSync(name))[key]
			} else {
				return ''
			}
		} else {
			return uni.getStorageSync(name).indexOf('{') > -1 ? JSON.parse(uni.getStorageSync(name)) : uni
				.getStorageSync(name)
		}
	} else {
		return ''
	}
}

// 查看大图
const viewImg = (url, index) => {
	if (url) {
		if (typeof url == 'string') {
			uni.previewImage({
				current: url,
				urls: [url]
			});
		} else if (Array.isArray(url) && url.length > 0) {
			uni.previewImage({
				current: Number(index),
				urls: url
			});
		}
	} else {
		uni.showToast({
			title: '暂无图片',
			icon: 'none',
			duration: 1500
		})
	}
}
//随机数
const RandomNumBoth = (Min, Max, not) => {
	var Range = Max - Min;
	var Rand = Math.random();
	var num = Min + Math.round(Rand * Range); //四舍五入
	if (not) {
		return createArray(Min, Max, not)
	} else {
		return num
	}
}

function createArray(min, max, not) {
	var arr = [];
	if (Array.isArray(not)) {
		for (let i = 0; i <= (max - min); i++) {
			if (not.indexOf(min + i) == -1) {
				arr.push(min + i)
			}
		}
		var randIndex = Math.floor(Math.random() * (arr.length - not.length))
		return arr[randIndex]
	} else {
		for (let i = 0; i <= (max - min); i++) {
			if (min + i !== not) {
				arr.push(min + i)
			}
		}
		var randIndex = Math.floor(Math.random() * arr.length)
		return arr[randIndex]
	}
}

// 手机号脱敏('13912345678' 转换成 '139****5678') 第3位开始替换4个
let telHide = num => {
	if (!num) {
		return ''
	} else {
		let data = num.replace(/(\d{3})\d{4}(\d*)/, '$1****$2')
		return data
	}

}

//加    
function floatAdd(arg1, arg2) {
	var r1, r2, m;
	try {
		r1 = arg1.toString().split(".")[1].length
	} catch (e) {
		r1 = 0
	}
	try {
		r2 = arg2.toString().split(".")[1].length
	} catch (e) {
		r2 = 0
	}
	m = Math.pow(10, Math.max(r1, r2));
	return (arg1 * m + arg2 * m) / m;
}

//减    
function floatSub(arg1, arg2) {
	var r1, r2, m, n;
	try {
		r1 = arg1.toString().split(".")[1].length
	} catch (e) {
		r1 = 0
	}
	try {
		r2 = arg2.toString().split(".")[1].length
	} catch (e) {
		r2 = 0
	}
	m = Math.pow(10, Math.max(r1, r2));
	//动态控制精度长度    
	n = (r1 >= r2) ? r1 : r2;
	return ((arg1 * m - arg2 * m) / m).toFixed(n);
}

//乘    
function floatMul(arg1, arg2) {
	var m = 0,
		s1 = arg1.toString(),
		s2 = arg2.toString();
	try {
		m += s1.split(".")[1].length
	} catch (e) {}
	try {
		m += s2.split(".")[1].length
	} catch (e) {}
	return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m);
}


//除   
function floatDiv(arg1, arg2) {
	var t1 = 0,
		t2 = 0,
		r1, r2;
	try {
		t1 = arg1.toString().split(".")[1].length
	} catch (e) {}
	try {
		t2 = arg2.toString().split(".")[1].length
	} catch (e) {}

	r1 = Number(arg1.toString().replace(".", ""));

	r2 = Number(arg2.toString().replace(".", ""));
	return (r1 / r2) * Math.pow(10, t2 - t1);
}

//截取路由参数
function getQueryVariable(href) {
	let url = href.split('?')[0];
	let query = href.substring(href.indexOf('?') + 1);
	let vars = query.split("&");
	let obj = {}
	for (var i = 0; i < vars.length; i++) {
		let pair = vars[i].split("=");
		obj[pair[0]] = pair[1]
	}
	let data = {
		obj,
		url
	}
	return data;
}

//根据长度设置字体大小
function fontSize(d) {
	var data;
	if (d.length <= 8) {
		data = 78;
	} else if (d.length > 8 && d.length < 16) {
		data = 78 - (Math.ceil((d.length - 8) / 2) * 10)
	} else {
		data = 38;
	}
	return data;
}
//是否为json字符串
function isJSON(str) {
	if (typeof str == 'string') {
		try {
			var obj = JSON.parse(str);
			if (typeof obj == 'object' && obj) {
				return true;
			} else {
				return false;
			}

		} catch (e) {
			return false;
		}
	}
}
//  上传图片到oss
/*
that: this
filePath: 上传的图片数组，
fileLists: 组件中绑定的那个字符串
fileLists1: 为不影响之前的，故加此字段，之前绑定的字符串前面的父值
*/
async function uploadImg(that, filePath, fileLists, fileLists1) {
	let _this = that;
	let signPro = new Promise((resolve, rej) => {
		// 判断签名有没有过期
		var res = uni.getStorageSync("sign")
		var timestamp = Date.parse(new Date()) / 1000
		if (res && res.expire - 3 > timestamp) {
			resolve(res)
		} else {
			_this.$commonApi.uploadImg().then((res) => {
				//过期重新获取签名
				if (res.code == 200) {
					uni.setStorage({
						key: "sign",
						data: res.data
					})
					resolve(res.data)
				}
			})
		}
	})
	signPro.then(val => {
		let {
			host,
			policy,
			signature,
			dir
		} = val;
		let OSSAccessKeyId = val.accessid

		filePath.map((item) => {
			//获取图片类型 后缀 jpg / png
			let arr = item.split(".")
			let imgType = arr[arr.length - 1]
			let filename = arr[arr.length - 2]
			let keyValue = val.dir + filename + "." + imgType
			uni.uploadFile({
				url: host,
				filePath: item,
				name: 'file',
				formData: {
					name: item,
					key: keyValue,
					success_action_status: '200',
					OSSAccessKeyId,
					policy,
					signature
				},
				success: res => {
					if (res.statusCode === 200) {
						let urls = `${val.host}/${keyValue}`
						if (fileLists1) {
							if (typeof _this[fileLists1][fileLists] == 'string') _this[
								fileLists1][fileLists] = urls
							else _this[fileLists1][fileLists].push(urls)
						} else {
							if (typeof _this[fileLists] == 'string') _this[fileLists] =
								urls
							else _this[fileLists].push(urls)
						}

					}
				},
				fail: res => {
					console.log(res)
				}
			})
		});
	})
}
/**
 * @description 保存图片，保存到系统相册
 * @param {String}  imgSrc 图片路径 
 * @param {String}  content 授权提示语句 
 * @param {String}  failTip 无图片保存时失败提示 
 * @param {String}  successTip 保存成功提示
 */
function saveImage(imgSrc, content="是否允许获取保存相册权限", failTip='暂无图片', successTip="已保存到本地相册，请打开相册扫描") {
	uni.saveImageToPhotosAlbum({
		filePath: imgSrc,
		// 保存成功，直接给出提示
		success: (res) => {
			uni.showToast({
				title: successTip,
				icon: 'none',
				duration: 1500
			})
		},

		// 保存失败，判断是否授权，未授权则调用授权，否则弹出失败信息
		fail(err) {
			console.error(err);
			if (err.errMsg === "saveImageToPhotosAlbum:fail auth deny" || err.errMsg ===
				"saveImageToPhotosAlbum:fail authorize no response" || err.errMsg === "saveImageToPhotosAlbum:fail auth denied") { // 没有授权，重新授权，兼容iso和Android
				uni.showModal({
					title: '授权提示',
					content: content,
					success: (res) => {
						if (res.confirm) { // 点击确定，则调用相册授权
							uni.openSetting({
								success(settingdata) {
									if (settingdata.authSetting["scope.writePhotosAlbum"]) {
										console.log("获取权限成功，再次点击图片保存到相册")
										uni.showToast({
											title: '授权成功，请重试哦~'
										});
									} else {
										console.log("获取权限失败")
										uni.showToast({
											title: '请确定已打开保存权限',
											icon: "none"
										});
									}
								}
							})
						}
					}
				})
			} else if (err.errMsg === "saveImageToPhotosAlbum:fail file not found" || err.errMsg ===
				"saveImageToPhotosAlbum:fail file not exists" || err.errMsg ===
				"saveImageToPhotosAlbum:fail get file data fail"
			) { // 无图片，则提示
				uni.showToast({
					title: failTip,
					icon: "none"
				});
			}
		}
	})
}
export default {
	toast: toast,
	successToast: successToast,
	showLoading: showLoading,
	tipLoading: tipLoading,
	hideLoading: hideLoading,
	customToast: customToast,
	timeHandle,
	getLocalData,
	setLocalData,
	viewImg,
	RandomNumBoth,
	telHide,
	floatAdd,
	floatSub,
	floatMul,
	floatDiv,
	getQueryVariable,
	fontSize,
	isJSON,
	uploadImg,
	saveImage
}
