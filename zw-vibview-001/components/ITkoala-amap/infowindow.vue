<template>
	<view class="amap-container">
		<view :prop="option" :prop.longitude="longitude" :prop.latitude="latitude" :change:prop="amap.updateEcharts"
			id="amap"></view>
		<view style="display: none;" id="infoWindow">
			<view v-if="option.oldmarkerList.length!=0&&option.istype" class="infoWindow-info">
				<image :src="option.oldmarkerList[option.dataIndex].shop_cove_img" class="logoimg"></image>
				<view class="rig">
					<view class="name showone">{{ option.oldmarkerList[option.dataIndex].shop_name }}</view>
					<!-- <view class="num">距您{{option.oldmarkerList[option.dataIndex].distance}}</view> -->
					<view class="li divflex">
						<image src="/static/icon/jul.png" class="liimg"></image>
						<view class="num ">{{option.oldmarkerList[option.dataIndex].distance}}</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	const start = 'static/ITkoala-amap/start.png'
	export default {
		data() {
			return {
				latitude: 30.61089,
				longitude: 103.90914,
				istype: false,
				name: '',
				markerList: [],
			}
		},
		props: {
			option: {
				type: Object,
				default: {

				}
			}
		},
		watch: {
			option: {
				deep: true,
				handler(v) {
					this.$set(this.option, 'markerList', JSON.parse(JSON.stringify(this.option.oldmarkerList)))
				}
			},
		},
		mounted() {
			this.$set(this,'longitude',uni.getStorageSync("location").longitude)
			this.$set(this,'latitude',uni.getStorageSync("location").latitude)
			this.$nextTick(() => {
				this.$set(this, 'option', this.option)
			})

		},
		methods: {
			// 模拟从后台获取地图数据
			getMapData() {
				this.oldmarkerList = []
				//this.oldmarkerList =  JSON.parse(JSON.stringify(this.markerList)) 
			},
			//地图点击回调事件
			onViewClick(params) {
				this.dataIndex = params.dataIndex
				this.name = params.name
			},
		}
	}
</script>

<script module="amap" lang="renderjs">
	import config from './config.js'

	const selectedStart = 'static/ITkoala-amap/daoh.png' //选中的图片
	const markerOne = []
	export default {
		data() {
			return {
				map: null,
				ownerInstanceObj: null, //service层对象
				currentItem: null, //当前点击的对象
				marker: null
			}
		},



		mounted() {

			if (typeof window.AMap === 'function') {
				this.initAmap()
			} else {
				// 动态引入较大类库避免影响页面展示
				const script = document.createElement('script')
				script.src = 'https://webapi.amap.com/maps?v=1.4.15&key=' + config.WEBAK
				script.onload = this.initAmap.bind(this)
				document.head.appendChild(script)
			}

		},

		methods: {
			initAmap() {
				this.map = new AMap.Map('amap', {
					resizeEnable: true,
					center: [this.longitude, this.latitude],
					zooms: [4, 18], //设置地图级别范围
					zoom: 10,
				})
				if (this.option.markerList.length) {
					this.initMarkers()
					this.positionClick()
				}


			},
			//初始化标记点
			initMarkers() {
				let prevMarker = null
				let prevIcon = null
				this.map.remove(markerOne)
				let item = this.option.markerList[this.option.dataIndex]
				//添加点标记
				const marker = new AMap.Marker({
					position: new AMap.LngLat(Number(item.shop_lng), Number(item.shop_lat)),
					offset: new AMap.Pixel(-13, -30),
					icon: selectedStart
				})
				markerOne.push(marker)
				this.currentItem = item
				if (!!prevMarker) {
					prevMarker.setIcon(prevIcon)
				}
				prevIcon = item.icon
				prevMarker = marker
				marker.setIcon(selectedStart)
				this.onClick(null, this.ownerInstanceObj)

				setTimeout(() => {
					this.showInfoWindow()
				}, 100)

				this.map.add(marker)
				if(this.option.istype==false){
					this.map.remove(marker)
				}
			},
			//显示信息窗体
			showInfoWindow() {
				let element = document.getElementById('infoWindow')
				let content = element.innerHTML
				let infoWindow = new AMap.InfoWindow({
					isCustom: true, //使用自定义窗体
					content: this.createInfoWindow(content),
					offset: new AMap.Pixel(16, -30)
				})
				infoWindow.open(this.map, new AMap.LngLat(Number(this.currentItem.shop_lng), Number(this.currentItem
					.shop_lat)))
			},
			//构建自定义信息窗体
			createInfoWindow(content) {
				let info = document.createElement('div')
				info.innerHTML = content

				info.onclick = (ev) => {
					let target = (ev.target && ev.target.currentSrc) || null
					if (!!target && target.includes('close.png')) {
						this.map.clearInfoWindow()
					}
				}
				return info
			},
			updateEcharts(newValue, oldValue, ownerInstance, instance) {
				// 监听 service 层数据变更
				this.ownerInstanceObj = ownerInstance
				if (newValue.istype != oldValue.istype || newValue.dataIndex != oldValue.dataIndex || newValue.markerList
					.length != oldValue.markerList.length) {
					this.initMarkers()
				}
				
			},
			onClick(event, ownerInstance) {
				// 调用 service 层的方法
				ownerInstance.callMethod('onViewClick', {
					dataIndex: this.dataIndex,
					name: this.name
				})

			},
			positionClick(event, ownerInstance) {
				// 创建一个 Marker 实例：
				let marker = new AMap.Marker({
					position: this.map.getCenter(), // 经纬度对象，也可以是经纬度构成的一维数组[116.39, 39.9]
					icon: 'static/ITkoala-amap/location.png'
				})
				// 将创建的点标记添加到已有的地图实例：
				this.map.add(marker)

				// 调用 service 层的方法
				ownerInstance.callMethod('onViewClick', {
					currentPosition: this.map.getCenter()
				})
			},
		}
	}
</script>

<style lang="scss" scoped>
	#amap {
		width: 100%;
		height: 600rpx;
	}

	.infoWindow-wrap {
		position: relative;
		background: #fff;

		.infoWindow-content {
			padding: 30rpx;

			.infoWindow-text {
				color: #f00;
			}

			.close {
				width: 32rpx;
				height: 32rpx;
				position: absolute;
				top: -25rpx;
				right: -15rpx;
			}
		}

		.sharp {
			width: 30rpx;
			height: 23rpx;
			position: absolute;
			bottom: -23rpx;
			left: 0;
			right: 0;
			margin: auto;

			image {
				width: 100%;
				height: 100%;
				vertical-align: top;
			}
		}
	}

.showone{
	display: -webkit-box;
	overflow: hidden;
	text-overflow: ellipsis;
	word-wrap: break-word;
	white-space: normal !important;
	-webkit-line-clamp: 1;
	-webkit-box-orient: vertical;
}
	.infoWindow-info {
		background: #FFFFFF;
		box-shadow: 0rpx 0rpx 200rpx 2rpx rgba(0, 0, 0, 0.08);
		border-radius: 20rpx;
		padding: 8rpx;
		display: flex;

		.logoimg {
			width: 72rpx;
			height: 72rpx;
			border-radius: 50%;
		}

		.rig {
			margin-left: 8rpx;

			.name {
				font-size: 24rpx;
				font-weight: bold;
				color: #000000;
				max-width: 400rpx;
			}

			.li {
				.liimg {
					width: 28rpx;
					height: 28rpx;
					margin-right: 4rpx;
				}

				.num {
					font-size: 20rpx;
					color: #999999;
				}
			}

		}
	}

	/deep/.amap-logo,
	/deep/.amap-copyright {
		display: none !important;
	}
</style>
