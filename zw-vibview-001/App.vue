<script>
	import {
		getapkversion
	} from '@/api/api.js'
	export default {
		globalData: {
			isNaviation: true
		},
		onLaunch: function() {
			//9e69e8ad46b6476c1b91f26299352e2c正
			//56dd87731f9111ea77e115656f949cc9测
			// uni.setStorage({
			//  	key: "token",
			//  	data: '56dd87731f9111ea77e115656f949cc9'
			// }) 
			// #ifdef APP-PLUS
			plus.nativeUI.showWaiting("正在请求数据...")
			getapkversion().then(res => {
				plus.nativeUI.closeWaiting()
				const appInfo = res.data
				plus.runtime.getProperty(plus.runtime.appid, (wgtInfo) => {
					if (wgtInfo.version !== appInfo.version) {
						// 更新app
						plus.nativeUI.confirm("有新版本发布了，是否立即更新？", function(e) {
							const upr = (e.index == 0) ? "Y" : "N"
							if (upr == "Y") {
								plus.nativeUI.showWaiting('下载更新中，请勿关闭');
								var url = appInfo.url + appInfo.path; // 下载文件地址   
								console.log(url, 'url')
								var dtask = plus.downloader.createDownload(url, {}, function(d, status) {
									if (status == 200) { // 下载成功    
										var path = d.filename;
										//alert(d.filename);
										clearInterval(timer);
										plus.runtime.install(path);
										plus.nativeUI.closeWaiting();
									} else { //下载失败   
										plus.nativeUI.toast("下载失败: " + status);
									}
								});
								dtask.start();
								var timer = setInterval(function() {
									//plus.nativeUI.closeWaiting();
									var totalSize = dtask.totalSize;
									var downloadedSize = dtask.downloadedSize;
									var daxiaoa = downloadedSize / totalSize
									var baifenbiw = Math.round(daxiaoa * 100)
									if (baifenbiw >= 0) {
										var baifenbi = baifenbiw + "%"
										plus.nativeUI.showWaiting('已下载' + baifenbi);
									}
								}, 1000); 
							} else {
								plus.runtime.quit()
							}
						}, "vibview", ["确认", "取消"])
					}
				})
			})
			// this.module.appInite({
			// 	'token': uni.getStorageSync('token') ? uni.getStorageSync('token') : '',
			// 	'points': uni.getStorageSync("userInfo") ? uni.getStorageSync("userInfo").credit_number : ''
			// })
			var myGlobalEvent = uni.requireNativePlugin('globalEvent');
			plus.globalEvent.addEventListener('p_scan', (e) => {
				if (e.data) {
					if (JSON.parse(e.data).type == '1') {
						uni.navigateTo({
							url: '/pages/businessDetails/index?id=' + (JSON.parse(e.data).pid) +
								'&&type=1'
						})
					} else if (JSON.parse(e.data).type == '25') {
						uni.navigateTo({
							url: '/pages/goodsDetails/index?id=' + (JSON.parse(e.data).pid) +
								'&&type=1'
						})
					} else if (JSON.parse(e.data).type == '10') {
						uni.navigateTo({
							url: '/pages/individual/index?id=' + (JSON.parse(e.data).pid) + '&&type=1'
						})
					} else if (JSON.parse(e.data).type == '12') {
						if (uni.getStorageSync("userInfo") && uni.getStorageSync("userInfo").shop_id > 0) {
							uni.navigateTo({
								url: '/pages/deduction/index?data=' + (JSON.parse(e.data).data) +
									'&&type=1'
							})
						} else {
							uni.navigateTo({
								url: '/pages/individual/index?id=' + (JSON.parse(e.data).pid) +
									'&&type=1'
							})
						}
					}
				}
			})

			plus.globalEvent.addEventListener('video_ad', (e) => {
				this.globalData.isNaviation = false
				uni.navigateBack({
					delta: 1
				})
			})

			plus.globalEvent.addEventListener('lockScreenSetting', (e) => {
				uni.navigateTo({
					url: '/pages/lock/index'
				})
			})

			plus.globalEvent.addEventListener('receiveMsg', (e) => {

			})

			plus.globalEvent.addEventListener('update', (e) => {
				if (e.data) {
					let downloadPath = JSON.parse(e.data).url
					uni.downloadFile({
						url: downloadPath,
						success: (downloadResult) => {
							if (downloadResult.statusCode === 200) {
								plus.runtime.install(downloadResult.tempFilePath, {
									force: true // 强制更新
								}, function() {
									console.log('install success...');
									plus.runtime.restart();
								}, function(e) {

								});
							}
						},
					})
				}
			})
			// #endif
		},
		onShow: function() {
			console.log('App Show')
			// #ifdef APP-PLUS  
			// 定位开启状态 true=开启，false=未开启
			let bool = false

			// android平台
			if (uni.getSystemInfoSync().platform == 'android') {
				var context = plus.android.importClass("android.content.Context");
				var locationManager = plus.android.importClass("android.location.LocationManager");
				var main = plus.android.runtimeMainActivity();
				var mainSvr = main.getSystemService(context.LOCATION_SERVICE);
				bool = mainSvr.isProviderEnabled(locationManager.GPS_PROVIDER)
			}

			// ios平台
			if (uni.getSystemInfoSync().platform == 'ios') {
				var cllocationManger = plus.ios.import("CLLocationManager");
				var enable = cllocationManger.locationServicesEnabled();
				var status = cllocationManger.authorizationStatus();
				plus.ios.deleteObject(cllocationManger);
				bool = enable && status != 2
			}
			// 未开启定位功能
			// if (bool === false) {
			// 	uni.showModal({
			// 		title: '提示',
			// 		content: '请打开定位服务',
			// 		success: ({
			// 			confirm,
			// 			cancel
			// 		}) => {
						
			// 			if (confirm) {
			// 				// android平台
			// 				if (uni.getSystemInfoSync().platform == 'android') {
			// 					var Intent = plus.android.importClass('android.content.Intent');
			// 					var Settings = plus.android.importClass('android.provider.Settings');
			// 					var intent = new Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS);
			// 					var main = plus.android.runtimeMainActivity();
			// 					main.startActivity(intent); // 打开系统设置GPS服务页面
			// 				}

			// 				// ios平台
			// 				if (uni.getSystemInfoSync().platform == 'ios') {
			// 					var UIApplication = plus.ios.import("UIApplication");
			// 					var application2 = UIApplication.sharedApplication();
			// 					var NSURL2 = plus.ios.import("NSURL");
			// 					var setting2 = NSURL2.URLWithString(
			// 						"App-Prefs:root=Privacy&path=LOCATION");
			// 					application2.openURL(setting2);
			// 					plus.ios.deleteObject(setting2);
			// 					plus.ios.deleteObject(NSURL2);
			// 					plus.ios.deleteObject(application2);
			// 				}
			// 			}

			// 			// 用户取消前往开启定位服务
			// 			if (cancel) {
			// 				// do sth...
			// 			}
			// 		}
			// 	});
			// }
			// #endif
		},
		onHide: function() {
			console.log('App Hide')
		}
	}
</script>


<style lang="scss">
	/* #ifndef APP-PLUS-NVUE */
	/* uni.css - 通用组件、模板样式库，可以当作一套ui库应用 */
	@import '@/css/uni.css';
	@import '@/css/customicons.css';

	/* H5 兼容 pc 所需 */
	/* #ifdef H5 */
	@media screen and (min-width: 768px) {
		body {
			overflow-y: scroll;
		}
	}


	uni-page-body {
		background-color: #FFFFFF !important;
		min-height: 100% !important;
		height: auto !important;
	}

	.uni-top-window uni-tabbar .uni-tabbar {
		background-color: #fff !important;
	}

	.uni-app--showleftwindow .hideOnPc {
		display: none !important;
	}

	/* #endif */

	/* 以下样式用于 hello uni-app 演示所需 */
	page {
		background-color: #FFFFFF;
		height: 100%;
		font-size: 14px;
		/* line-height: 1.8; */
	}

	.fix-pc-padding {
		padding: 0 50px;
	}

	.uni-header-logo {
		padding: 15px;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		margin-top: 5px;
	}

	.uni-header-image {
		width: 100px;
		height: 100px;
	}

	.uni-hello-text {
		color: #7A7E83;
	}

	.uni-hello-addfile {
		text-align: center;
		line-height: 150px;
		background: #FFF;
		padding: 25px;
		margin-top: 10px;
		font-size: 18px;
		color: #808080;
	}

	/* #endif*/
	/*每个页面公共css */
	@import "css/common.scss"
</style>
