<!-- 
 * UReport 精美报表工具
 * Copyright (c) 2021 howcode https://gitee.com/huang_si_hao All rights reserved.
 * MIT ( https://gitee.com/huang_si_hao/u_report/blob/master/LICENSE )
 * 复制使用请保留本段注释，感谢支持开源！
 * 
 * 开源地址:
 * https://gitee.com/huang_si_hao/u_report
 * 
 * uni-app插件市场地址：
 * https://ext.dcloud.net.cn/plugin?id=4373
 * 
 -->
<template>
	<view class="window column">
		<!-- <view class="topLine" :style="{height: topBar+'px'}"></view> -->
		<view class="nav row_align_center" id="nav">
			<text class="title">报表视图</text>
			<li class="iconfont icon-zuojiantou back hidden"></li>
		</view>
		<view class="top_head">
			<image mode="widthFix" class="logo" src="../../static/logo/logo.png"></image>
			<view class="text_main">
				<view class="main">UReport</view>
				<view class="sketch">{{sketch}}</view>
			</view>
		</view>
		<view class="content">
			{{content}}
		</view>
		<view class="tail column">
			<view class="the_title">
				<view class="title_icon"></view>
				<text class="margin_stand-samll font-big wide">页面</text>
			</view>
			<cell v-for="(item,index) in toolList" :key="index"
			 :leftIcon="item.img" leftIconClass="cellIcon" :textVal="item.title" 
			 rightIcon="icon-right" rightIconClass="direct"
			 :linkUrl="item.link" @clickCell="clickCell"></cell>
		</view>
	</view>
</template>
<script>
	import cell from "../../components/common/cell.vue"
	export default {
		components: {
			cell
		},  
		data() {
			return {
				toolList:[
					{
						img:"../../static/img/center.png",
						title:"BI_报表中心",
						link:"/main/index",
						id:0
					},
					{
						img:"../../static/img/school.png",
						title:"智慧教育报表中心",
						link:"/school/index",
						id:1
					}
				],
				sketch:"多平台部署报表模板",
				content:"封装日历组件、表格组件、排行榜组件、条形进度条组件、文本块组件以及ucharts的多个图表组件，用户传特定数据即可方便、快捷地打造出属于自己的报表页面。"
			};
		},
		computed: {
			
		},
		methods: {
			clickCell(v){
				this.$Common.navigateTo(v);
			},
		},
		onLoad() {

		}
	};
</script>
<style lang="scss">
	.window{
		height: 100vh;
		width: 100%;
		background-image: linear-gradient(125deg,#CB9FFE,#5894F7,#ABCDFA,#74A3F6,#CB9FFE);
		background-size: 400%;
		animation: bganimation 15s infinite;
		color: #5b5b5b;
		.body{
			height: 100vh;
			margin: 0;
			padding: 0 20rpx;
			font-family: "montserrat";
			
		}
		.topLine{
			background-color: #40A2ED;
			width: 100%;
		}
		.nav {
			background-size: 100% 100%;
		
			.back {
				font-size: 54rpx;
				padding: 20rpx 14rpx 15rpx 14rpx;
				color:#fff;
			}
			.title {
				font-size: 30rpx;
				flex: 1;
				text-align: center;
			}
			.hidden {
				visibility: hidden;
			}
		}
		.top_head{
			width: 100%;
			display: flex;
			.logo{
				width: 25%;
			}
			.text_main{
				width: 70%;
				padding: 20rpx 30rpx;
				.main{
					font-size: 40rpx;
					font-weight: 900;
					color: #5b5b5b;
				}
				.sketch{
					font-size: 26rpx;
					margin-top: 10rpx;
				}
			}
		}
		.content{
			padding:20rpx 20rpx;
			color: #f6f5f5;
			font-size: 26rpx;
		}
		.tail{
			padding: 30rpx 4%;
			.the_title{
				display: flex;
				align-items: center;
				color: #fff;
			}
			.title_icon{
				background-color: #fff;
				height: 40rpx;
				width: 10rpx;
				border-radius: 10rpx;
				margin-right: 20rpx;
			}
			min-badge{
				display: flex;
				flex-direction: row;
				justify-content: center;
				flex: 1;
				
				image{
					height: 70rpx;
					width: 70rpx;
				}
				text{
					margin-top: 20rpx;
					font-size: 13px;
				}
			}
			
			.cellIcon{
				margin-right: 18rpx;
				height: 40rpx;
				width: 40rpx;
			}
			.direct{
				font-size: 20px;
				color: #fff;
			}
		}
	}
	@keyframes bganimation {
	  0%{
	    background-position: 0% 50%;
	  }
	  50%{
	    background-position: 100% 50%;
	  }
	  100%{
	    background-position: 0% 50%;
	  }
	}
</style>