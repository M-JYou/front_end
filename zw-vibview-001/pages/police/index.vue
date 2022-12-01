<template>
	<view class="pages">
		<!-- 列表 -->
		<view class="content">
			<view class="" v-for="(item,index) in runList" :key='index'>
				<view class="arrondi divflex">
					<!-- 1 -->
					<view class="left-top">{{item.name}}</view>
					<!-- 2 -->
					<view class="arrondi-leftbox">
						<view class="arrondi-left">
							<view class="left-centers">
								<view class="custom-style4" v-if="item.alarmlevel ==''">正常</view>
								<view class="custom-style1" v-if="item.alarmlevel =='danger'">危险</view>
								<view class="custom-style2" v-if="item.alarmlevel =='warn'">预警</view>
								<view class="custom-style3" v-if="item.alarmlevel =='alarm'">报警</view>
								<view class="custom-style5" v-if="item.alarmlevel == 'disconnect'">网络</view>
							</view>
							<view class="left-butns">时间: {{item.time}}</view>
						</view>

						<view class="arrondi-right" @click="goRunlist(item)">
							<view class="right-names" v-if="item.alarmlevel ==''">查 看</view>
							<view class="right-names" v-if="item.alarmlevel =='alarm'">查 看</view>
							<view class="right-names" v-if="item.alarmlevel =='warn'">查 看</view>
							<view class="right-names" v-if="item.alarmlevel =='danger'">查 看</view>
							<u-button class="buttons" size="mini" v-if="item.alarmlevel == 'disconnect'"></u-button>
							<view class="right-iconts">
								<image class="icontsimages" src="../../static/index/zc.png" mode=""
									v-if="item.alarmlevel ==''">
								</image>
								<image class="icontsimages" src="../../static/index/you1.png" mode=""
									v-if="item.alarmlevel =='alarm' || item.alarmlevel =='warn' || item.alarmlevel =='danger' ">
								</image>
							</view>
						</view>
					</view>
					
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		stateList
	} from "@/api/api.js"
	export default {
		data() {
			return {
				style: 0,
				alarmlevel: 2,
				runList: [


				],
			};
		},
		onLoad(option) {
			// 接收index跳转内容
			let state = option.state
			// console.log(state,"polic")
			this.getStatellist(state)
		},
		onShow() {
				// #ifdef APP-PLUS  
				setTimeout(()=>{
						if(this.runList==""){
						uni.showLoading({
							title: '加载中'
						});
				}
				},1000)
			
		 	// #endif
		},
		methods: {
			// 获取状态列表
			getStatellist(state) {
				let that = this
				stateList(state).then(res => {
					console.log(res,'返回数据上111');
					if (res.statusCode === 200) {
								that.runList = res.data

						
						
						
					
					}
				})
			},
			


			// 点击跳转---item点击的id项
			goRunlist(item) {
				// const	code = item.tid
				const id = item.id
				const name = item.name
				console.log(id, 232)
				// return
				uni.navigateTo({
					url: `/pages/statused/index?id=${id}&name=${name}`
				})
			},
			// 若果是设备异常     点击确认按钮不跳到下一页面
			// cacelOrder(e) {
			// 	uni.showModal({
			// 		title: '提示',
			// 		content: '是否确认当前异常信息？确认后下次登录将不会在提示',
			// 		success: ({
			// 			confirm
			// 		}) => {
			// 			if (!confirm) return
			// 			cancelPay({
			// 				id: e.order_id
			// 			}).then(({
			// 				status
			// 			}) => {
			// 				if (status !== 200) return

			// 				this.$emit('upload', true)
			// 			})
			// 		}
			// 	})
			// },



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
