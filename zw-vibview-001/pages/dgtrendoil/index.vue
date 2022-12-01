<template>
	<view class="pages">
		<uni-nav-bar :fixed="true" background-color="#333333" :status-bar="true" :border="false" title=" 趋势图"
			leftIcon="left" @clickLeft="back" color="#ffffff" size="15">
		</uni-nav-bar>
		<!-- 列表标题 -->
		<view class="content-titles">
		<view class="titles-lefts">
			<view class="lefts">起始时间
			</view>
			<view class="rights">中止时间</view>	
		</view>
		
			<view class="titles-rights">
				<view class="lefts">
					<u-icon class="icont" name="search" color="#ffffff" size="14"></u-icon>
					<text>查询</text>
					</view>
					
				<view class="rights">
					<u-icon class="icont" name="clock-fill" color="#ffffff" size="14"></u-icon>
					<text>时频谱</text>
					</view>	
			</view>
				
			
		
		</view>

		<view class="content">
			<view class="charts-box">
				<qiun-data-charts type="line" :opts="opts" :chartData="chartData" />
			</view>
		</view>
	</view>
</template>

<script>
	import {} from "../../api/common.js"
	export default {
		data() {
			return {
		


				chartData: {},

				opts: {
					color: ["#1890FF"],
					
					// padding: [15, 10, 0, 15],
					padding: [15, 52, 0, 35],
					legend: {
						show:false
					},
					xAxis: {
						disableGrid: true,
						boundaryGap:"justify",
					},
					yAxis: {
						gridType: "dash",
						dashLength: 1,
						tofix:2
					},
					extra: {
						line: {
							type: "straight",
							width: 1
						}
					}
				},


				runList: [],

				kehuList: [

				],
			};
		},
		onLoad(options) {
		
		},
		onShow() {
    // var that = this;
    //       // #ifdef APP-PLUS
    //       	plus.screen.lockOrientation('landscape-primary');
    //       	// #endif

		},
		onReady() {
			this.getServerData();
		},
		methods: {
			
					
						
						
						
			getServerData() {
				setTimeout(() => {


					let res = {
						categories: ["1","2","3","4","5","6"],
						series: [{
								name: "",
								data: [1,30,1,50,1,30,1,50]
							},

						]
					};
					this.chartData = JSON.parse(JSON.stringify(res));
				}, 500);
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
	  .charts-box {
	    width: 100%;
	    height: 100%;
	  }
	@import "index.scss";
</style>
