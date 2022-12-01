<template>
	<view class="pages">
	<!-- 	<uni-nav-bar :fixed="true" background-color="#333333" :status-bar="true" :border="false" :title="title"
			rightWidth="40rpx" leftWidth="40rpx" leftIcon="left" @clickLeft="back" color="#ffffff" size="30">
		</uni-nav-bar> -->
			<!-- <text>{{title}}</text> -->
		<view class="main">
			<web-view class="webboxs" :src="platformurl + '/SystemSecurity/Information/InfoDetail?keyValue=' + keyValue"></web-view>
		</view>
		
		<!-- 列表标题 -->
		<!-- <view class="title">
			<text>{{title}}</text>
		</view> -->
		<!-- 列表 -->
		<!-- <view class="content">
			<view class="" >
				<view class="arrondi divflex">
					<view class="">
						<text>
							{{runList.hreff}}
						</text>
					</view>
				</view>

			</view>
		</view> -->
	</view>
</template>

<script>
	import {
		platformurl
	} from "../../api/common.js"
	export default {
		data() {
			return {
				title: '', // 页面标题
				keyValue: '', // 资讯id
				runList: [],
				platformurl
			};
		},
		onLoad(options) {
			let {title, keyValue} = options
			this.title = title
			this.keyValue = keyValue
			console.log(this.keyVlaue, '数据110')
			this.platformurl = platformurl
			this.getgoNearbysun()
		},
		onShow() {

		},
		methods: {
			getgoNearbysun(){
				let that = this
				let account =uni.getStorageSync('account')
				// let account = localStorage.getItem('account')
				// http://132.232.17.10/SystemSecurity/Information/InfoDetail?keyValue=189518e8-1ef8-4bbd-a624-eb8ddd0732ea
				
			},



			back() {
				uni.navigateBack({
					delta: 1
				})
			},


		}
	};
</script>

<style lang="scss" scoped>
	@import "index.scss";
</style>
