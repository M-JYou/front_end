<template>
	<view class="pages">
		<view class="toptitles">
			<view class="DataSourceed">
				设备通知 共
				<text class="textss">{{DataSource.data}}</text>
				条
			</view>
			<view class="confirmationed"  @click="show = true">
				<text>一键确认</text>
			</view>

		</view>


		<view>
			<view class="wrap">
				<!-- <view class="u-tabs-box">

				</view> -->
				<view class="page-box">
					<!-- 遍历列表 -->
					<view class="order" v-for="(res,index) in wholeList" :key="index"
						@tap="cacelOrder(res.id,res.deviceCode,index)">
						<view class="top">
							<view class="left">
								<view class="left-top">{{res.name}}</view>
								<view class="left-center">
									<view class="custom-style" v-if="res.alarmLevel ==1">
										<text>{{res.describ}}</text>
									</view>
									<view class="custom-style2" v-if="res.alarmLevel ==2">
										<text>{{res.describ}}</text>
									</view>
								</view>
								<view class="left-bouuts">首次报警时间:{{res.startTime}}</view>
								<view class="left-bouuts">最近报警时间:{{res.lastTime}}</view>
							</view>
							<!-- <view class="right" @tap="cacelOrder(res)"> -->
							<view class="right">
								<view class="buttons" size="mini"  v-if="audit">
									<text class="buttonsimg">
										<image class="imgs" src="../../static/index/users.png" mode=""></image>
									</text>
								{{res.audit}}
								
								</view>
								
								<view class="buttons" size="mini" v-else>
									<text class="buttonsimg">
										<image class="imgs" src="../../static/index/users.png" mode=""></image>
									</text>
									<text>{{accounted}}</text>

								</view>
							</view>
						</view>
					</view>
				</view>

			</view>
		</view>
		<!-- 弹窗 -->
		<view>
					<u-popup class="popupbox" v-model="show" mode="center" width="260px" height="110px">
				<view class="popupcontent">
					<view class="contenes">
						是一键否确认当前所有信息？
					</view>

					<view class="popupu-button">
						<view class="u-button1" @click="show = false;">取消</view>
						<view class="u-button2" @tap="cacelogin()">确认</view>

					</view>

				</view>
			</u-popup>
		</view>

		<page-tabpars></page-tabpars>
	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations,
		minelList
	} from 'vuex'
	import {
		messageList,
		TotalNuber,
		alarmDatas
	} from "@/api/api.js"
	export default {
		data() {
			return {
				id: '',
				deviceCode: '',
				itemid: '',
				timer: null,
				show: false,
				accounted: '',
				audit: true,
				DataSource: [],
				alarmLevel: 0,
				show: false,
				content: '',

				showBar: true, //初始化底部滑块开关

				wholeList: [],
				orderList: [],
				dataBuffer: true, //数据展示开关
				list: [{
						name: '通知',

					},

				],
				current: 0,
				swiperCurrent: 0,
				tabsHeight: 0,
				dx: 0,

			};
		},
		computed: {
			...mapGetters(['active']),


		},
		created() {
			// #ifdef APP-PLUS
			if (this.wholeList == "") {
				this.timer = setTimeout(function() {
					uni.showLoading({
						title: '加载中'
					});
				}, 2000);
			}
			// #endif	
		},
		onLoad() {
			if (this.timer) {
				clearInterval(this.timer);
				this.timer = null;
			}
		},
		onShow() {
			const token = uni.getStorageSync('token')
			if (!token) {
				uni.navigateTo({
					url: '../login/index'
				})
				return
			}
			this.informList();
			this.nuberList();
			// this.getminelList()

		},

		methods: {
			
			
			// 获取列表
			informList() {
				let that = this
				let account = uni.getStorageSync('account')
				messageList({
					account
				}).then(res => {
					console.log(res, 111)
					this.wholeList = res.data
					this.accounted = account

					console.log(this.deviceCode, 100)
				})

			},

			// 获取异常设备总数
			nuberList() {
				let that = this
				let account = uni.getStorageSync('account')
				TotalNuber({
					account
				}).then(res => {
					// console.log(res,'数量');
					that.DataSource = res
				})
			},

			...mapMutations(['SET_ACTIVE']),






			cacelOrder(id, deviceCode, index) {
				let params = {
					Origin: "app",
					RequestData: {
						id: id,
						nodeCode: deviceCode
					},
				}
				uni.showModal({
					title: '提示',
					content: '是否确认当前异常信息？确认后下次登录将不会在提示？',
					cancelText: "确定", // 取消按钮的文字  
					confirmText: "取消", // 确认按钮的文字  
					showCancel: true, // 是否显示取消按钮，默认为 true
					success: (res) => {
						console.log(res, 33)
						if (res.confirm) {
							console.log('comfirm') //点击确定之后执行的代码
						} else {
							alarmDatas(
								params
							).then(res => {
								console.log(res, 77)
								if (res.statusCode == 200) {
									console.log(this.wholeList, this.accounted)
									this.wholeList.map((item, indexId) => {
										if (indexId === index) {
											return this.wholeList[indexId].audit = this
												.accounted;
										}
									})
								}
							})
							console.log('cancel') //点击取消之后执行的代码
						}
					}
				})
			},
			
				cacelogin() {
							this.show = false
							this.audit =false
						},


		}
	};
</script>



<style>
	/* #ifndef H5 */
	page {
		height: 100%;
		background-color: #f2f2f2;
	}



	/* #endif */
</style>
<style lang="scss" scoped>
	// 引入样式
	@import "index.scss";
</style>
