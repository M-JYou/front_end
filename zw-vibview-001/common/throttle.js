/**
 * 防抖函数 防止多次点击执行多次方法，可用于防重复提交
 */
const debounce = (fn,delay = 100,promptly) => {
	let timer = null;
	
	return function(...args) {
		console.log(111)
		// 立即执行
		if(promptly) {
			// 当timer为null时执行
			if(!timer) fn.apply(this,args);
			if(timer) {
				clearTimeout(timer)
			}
			timer = setTimeout(() => {
				timer = null;
			},delay)
		}else {
			if(timer) {
				clearTimeout(timer)
			}
			timer = setTimeout(() => {
				fn.apply(this,args);
			},delay)
		}
	}
}

/**
 * 节流函数 每间隔设置时间执行一次，可用于页面滚动执行操作
 */
const throttle = (fn,delay = 100) => {
	let valid = true;
	return function(...args) {
		if(!valid) {
			return 
		}
		valid = false;
		fn.apply(this,args)
		setTimeout(() => {
			valid = true
		},delay)
	}
}

module.exports = {
	debounce,
	throttle
}