<template>
	<view class="main" :style="{ 'background': 'url(' + backgroundImg + ')'}">
		<card-swiper class="card_swiper" :card_menu="card_menu"></card-swiper>
	</view>
</template>

<script>
	import cardSwiper from "@/components/card-swiper/card-swiper"
	export default {
		data() {
			return {
				card_menu:this.$Config.CARD_MENU,
				backgroundImg:"https://vkceyugu.cdn.bspapp.com/VKCEYUGU-e9420bcd-26cd-4faf-b47a-9949982f7c41/e271bb60-e1cb-4d0b-9a23-e370b770851f.png"
			}
		},
		components:{
			cardSwiper
		},
		computed:{

		},
		onLoad() {
			
		},
		methods: {
			
		}
	}
</script>

<style lang="scss">
	/* 根元素样式 设置页面背景、字体大小、字体颜色，字符间距、长单词换行 */
	.main {
	  // background: #000!important;
	  font-size: 28rpx;
	  box-sizing: border-box;
	  color: #333;
	  letter-spacing: 0;
	  word-wrap: break-word;
		height: 100vh;
		width: 100%;
		background-size: 100% 100%!important;
	}
	.card_swiper{
		position: fixed;
		top: 50%;
		transform: translateY(-50%);
		height: 800rpx;
		width: 100%;
	}
	.join_tip{
		position: fixed;
	}
	
	/* 设置常用元素尺寸规则 */
	view,textarea,input,label,form,button,image{box-sizing: border-box;}
	/* 按钮样式处理 */
	button{font-size: 28rpx;}
	/* 取消按钮默认的边框线效果 */
	button:after{border:none;}
	/* 设置图片默认样式，取消默认尺寸 */
	image{display: block;height: auto;width: auto;}
	/* 输入框默认字体大小 */
	textarea,input{font-size: 28rpx;};
	
	/* 列式弹性盒子 */
	.flex_col {
	  display: flex;
	  flex-direction: row;
	  flex-wrap: nowrap;
	  justify-content: flex-start;
	  align-items: center;
	  align-content: center;
	}
	/* 行式弹性盒子 */
	.flex_row {
	  display: flex;
	  flex-direction: column;
	  flex-wrap: nowrap;
	  justify-content: flex-start;
	  align-items: flex-start;
	  align-content: flex-start;
	}
	 
	/* 弹性盒子弹性容器 */
	.flex_col .flex_grow{width:0;flex-grow: 1;}
	.flex_row .flex_grow{flex-grow: 1;}
	 
	/* 弹性盒子允许换行 */
	.flex_col.flex_wrap{flex-wrap: wrap;}
	 
	/* 弹性盒子居中对齐 */
	.flex_col.flex_center,.flex_row.flex_center{justify-content: center;}
	 
	/* 列式弹性盒子两端对齐 */
	.flex_col.flex_space{justify-content: space-between;}
	
	/* 弹性盒子快速分栏 ,这里非常郁闷 uniapp 居然不支持 * 选择器 */
	.flex_col.flex_col_2>view{width: 50%;}
	.flex_col.flex_col_3>view{width: 33.33333%;}
	.flex_col.flex_col_4>view{width: 25%;}
	.flex_col.flex_col_5>view{width: 20%;}
	.flex_col.flex_col_6>view{width: 16.66666%;}
	
	/* 字体颜色 */
	.color_333 {color: #333;}
	.color_666 {color: #666;}
	.color_999 {color: #999;}
	.color_ccc {color: #ccc;}
	.color_fff {color: #fff;}
	.color_6dc{color: #6dca6d;}
	.color_d51{color: #d51917;}
	.color_09f{color: #0099ff;}
	 
	/* 字体大小 */
	.size_10 {font-size: 20rpx;}
	.size_12 {font-size: 24rpx;}
	.size_14 {font-size: 28rpx;}
	.size_16 {font-size: 32rpx;}
	.size_18 {font-size: 36rpx;}
	.size_20 {font-size: 40rpx;}
	 
	/* 字体加粗 */
	.font_b{font-weight: bold;}
	 
	/* 对齐方式 */
	.align_c{text-align: center;}
	.align_l{text-align: left;}
	.align_r{text-align: right;}
	 
	/* 遮罩 */
	.shade{
	  position: fixed;
	  top: 0;
	  right: 0;
	  bottom: 0;
	  left: 0;
	  background-color: rgba(0,0,0,0.8);
	  z-index: 100;
	}
	 
	/* 弹窗 */
	.shade_box{
	  position: fixed;
	  top: 0;
	  right: 0;
	  bottom: 0;
	  left: 0;
	  margin: auto;
	  z-index: 101;
	  min-width: 200rpx;
	  min-height: 200rpx;
	}
	
	page{
		background-color: #fff;
	}
	/* 标题栏 */
	.title{
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: auto;
		padding-top: var(--status-bar-height);
		z-index: 10;
		background-color: rgba(66,185,131,0);
		color: rgba(255,255,255,0.8);
		
		&>view{
			height: 44px;
		}
		
		.box1{
			width: 60rpx;
			margin: 0 40rpx;
			font-size: 36rpx;
		}
		
		
		.tab{
			&>view{
				margin: 0 30rpx;
				line-height: 64rpx;
				font-size: 36rpx;
				position: relative;
				letter-spacing: 0;
				transition: transform 0.3s ease-in-out 0s;
				transform: scale(1,1);
				
				&.active{
					color: rgba(255,255,255,1);
					transform: scale(1.15,1.15);
				}
			}
		}
		
		&.style2{
			color: #FFF;
			background-color: rgba(66, 185, 131,1);
			
			.tab{
				&>view{
					&.active{
						color: #FFF;
					}
				}
			}
		}
	}

</style>