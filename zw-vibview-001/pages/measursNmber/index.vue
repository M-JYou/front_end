<template>
	<view class="pages">
		<uni-nav-bar :fixed="true" background-color="#333333" :status-bar="true" :border="false" title=" 测 点 个 数"
			rightWidth="40px" leftWidth="40px" leftIcon="left" @clickLeft="back" color="#ffffff" size="15">
		</uni-nav-bar>
		<!-- 列表标题 -->
		<view class="content-titles">
			<text>监测设备</text>
		</view>
		<!-- 列表 -->
		<view class="content">
			<view class="" v-for="(item,index) in runList" :key='index' @click="goRunlist()">
				<view class="arrondi divflex">
					<view class="arrondi-left">
						<view class="left-imgs">
							<image class="imgss" src="../../static/index/sheb.png" mode=""></image>
						</view>
						<view class="left-titles">
							<view class="titles">{{item.names}}</view>

							<view class="contents">{{item.content}}</view>

						</view>
					</view>
					<view class="arrondi-rights">
						<image class="imgss" src="../../static/index/you1.png" mode=""></image>
					</view>
				</view>

			</view>
		</view>
	</view>
</template>

<script>
	
	import{meanmberList} from "@/api/common.js"
	export default {
		data() {
			return {
			
				runList: [{
						names: "F2108",
						content: "测点个数: 2"
					},
					{
						names: "F2108",
						content: "测点个数: 6"
					},
					{
						names: "F2108",
						content: "测点个数: 8"
					},
					{
						names: "F2108",
						content: "测点个数: 10"
					},
					
				],
			};
		},
		onLoad(option) {
let state = option.state
let{code,name}=option
this.name= name
this. code = code
this.getmeanmberList(state,name,code)
		},
		onShow() {

		},
		methods: {
			
			getmeanmberList(state,name,code){
				meanmberList(state,name,code).then(res=>{
					console.log(res,190)
				})
			},
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			goRunlist() {
				console.log(222)
				uni.navigateTo({
					url: '/pages/statused/index'
				})
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
