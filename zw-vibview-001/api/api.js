import {
	httpApi,
	httpApi2
} from '@/api/common.js'

// 登录接口
export const getLogin = (data) => {
	return httpApi('/token/v1/auth/validate', data, 'post')
};



//获取版本号route: "/api/version",
export const getversion = (data) => {
	return httpApi2('/api/version', data, 'get')

};
//对比版本号
export const getapkversion = (data) => {
	return httpApi('/base/v1/apkver/version', data, 'get')

};

// //首页-项目列表页
export const projectList = (data) => {
	return httpApi('/base/v1/appdata/ProjectItems', data, 'get')

};
//首页数据
export const AbnormaList = (data) => {
	return httpApi('/base/v1/appdata/AbnormalCount', data, 'post')

}


// 				// 首页判断项目列表
export const nodeitmList = (code) => {
	return httpApi("/base/v1/appdata/NodeItems/" + code, 'get')

}


// 		// 首页-项目-都来用的接口 deviceListfist用=='' 或1
export const deviceitemsList = (code) => {
	return httpApi("/base/v1/appdata/DeviceItems/" + code, 'get')

}



// 首页-项目--devicelistlist--页面接口
export const deviceList = (code) => {
	return httpApi("/base/v1/appdata/NodeItems/" + code, 'get')

}



// // police页面数据
export const stateList = (state) => {
	return httpApi('/base/v1/appdata/AbnormalList/' + state, 'post')

}

// //statused/police状态页面----从异常设备跳转来的---只获取名称
export const statusedList = (code) => {
	return httpApi('/base/v1/appdata/PointItems/' + code, 'get')

}

//statused页面 表格中如有 加速度，速度位置数据
export const DevicelistData = (code) => {
	return httpApi("/base/v1/appdata/DeviceLastData/" + code, 'get')

}





//资讯数据
export const inforMations = (data) => {
	return httpApi2('/api/information/list', data, 'get')

}







// //通知列表页
export const messageList = (data) => {
	return httpApi('/base/v1/appdata/DeviceAlarm', data, 'post')

}
// //通知-设备异常总数
export const TotalNuber = (data) => {
	return httpApi('/base/v1/appdata/TotalAlarm', data, 'post')
}
// // 通知页-弹窗
export const alarmDatas = (data) => {
	return httpApi('/base/v1/alarmdata/audit', data, 'post')
}


// // 获取个人中心  +  完善资料数据
export const minelList = (data) => {
	return httpApi('/base/v1/user/userinfo', data, 'get')

}



// //完善资料数据--- 资料提交
export const submitList = (data) => {

	return httpApi('/base/v1/user/modify', data, 'post')

}




//类型  7  趋势图数据
export const gatHeritems = (pointcode) => {
	return httpApi("/base/v1/appdata/GatherItems/" + pointcode, 'get')
}


//趋势图曲线的数据

export const gatcurveDatalist = (data) => {
	return httpApi("/base/v1/analyze/trendData", data, 'post')
}



//时频谱-----上一刻数据
export const gatWavedata = (data) => {
	return httpApi("/base/v1/analyze/vibWaveData", data, 'post')
}

//时频谱-----下一刻数据
export const gatPrevdata = (data) => {
	return httpApi("/base/v1/analyze/vibPrevWaveData", data, 'post')
}




//获取故障代码
export const cacheList = (data) => {
	return httpApi("/base/v1/dictionary/keylist", data, 'post')
}


//修改密码获取数据
export const gainPasswd = (data) => {
	return httpApi("/base/v1/user/userinfo", data, 'get')
}


//提交修改后的数据
export const SubmitPassword = (data) => {
	return httpApi("/base/v1/user/ChangePassword", data, 'post')
}

//油液接口

export const oilPointtrend = (data) => {
	return httpApi2("/api/trend/oil_pointtrend", data, 'post')
}










