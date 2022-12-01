<template>
	<view class="mask" catchtouchmove="move">
		<view class="mask-dialog">
				<view v-if="istitle" class="titleText">{{titleText}}</view>
				<view :class="{'padd':ispadding}" class="mask-dialog-content">
					{{showText}}<slot name="after"></slot>
				</view>
				<view class="mask-dialog-btn single" v-if="isShowAuthLocationBtn">
					<view class="confirm" @click='confirmBtn()'>{{confirmText}}</view>
				</view>
				<view class="mask-dialog-btn" v-else>
					<view class="cancel" @click='cancelBtn()'>{{cancelText}}</view>
					<view class="confirm" @click='confirmBtn()'>{{confirmText}}</view>
				</view>
		</view>
	</view>
</template>

<script>
	export default {
		// 弹窗
		name: 'popDialog',
		props: {
			titleText:{
				type: String,
				default: '操作提示'
			},
			showText: {
				type: String,
				default: ''
			},
			cancelText: {
				type: String,
				default: '取消'
			},
			confirmText: {
				type: String,
				default: '确认'
			},
			isShowAuthLocationBtn: {
				type: Boolean,
				default: false
			},
			istitle:{
				type: Boolean,
				default: false
			},
			ispadding:{
				type: Boolean,
				default: true
			}
		},
		data () {
			return  {
				
			}
		},
		mounted () {
			
		},
		methods:{
			callback (e) {
				this.$emit('confirmBtnLocation', e)
			},
			cancelBtn () {
				this.$emit('cancelBtn')
			},
			confirmBtn () {
				this.$emit('confirmBtn')
			},
			move () {
				return
			},
		}
	}
</script>

<style lang="scss" scoped>
	/* pages/components/tipDialog/tipDialog.wxss */
	.mask {
	  position: fixed;
	  z-index: 9999999;
	  top: 0;
	  left: 0;
	  width: 100%;
	  height: 100%;
	  background: rgba(0, 0, 0, 0.5);
	  display: flex;
	  justify-content: center;
	  align-items: center;
	}
	.mask-dialog {
	  width: 70%;
	  background: #F8F8F8;
	  border-radius: 20rpx;
	}
	.titleText{
		padding: 28rpx 32rpx 40rpx;
		color: #000000;
		font-size: 34rpx;
		text-align: center;
	}
	.mask-dialog-content {
	  font-size: 32rpx;
	  line-height: 52rpx;
	  color: #8C9198;
	  box-sizing: border-box;
	  text-align: left;
	  padding: 0 32rpx 40rpx;
	}
	.mask-dialog-content.padd{
		padding: 72rpx 20rpx;
		line-height: 60rpx;
		color: #000000;
		text-align: center;
	}
	.mask-dialog-btn {
	  display: flex;
	  flex-direction: row;
	  border-top: 2rpx solid #E9E9E9;
	  height: 96rpx;
	  line-height: 96rpx;
	  border-radius: 0 0 20rpx 20rpx;
	  &.single {
		  align-items: center;
		  .btn {
			  width: 100%;
			  color: #fe5f5c;
			  justify-content: center;
		  }
	  }
	}
	.mask-dialog-btn .cancel, .mask-dialog-btn .confirm {
	  width: calc(50% - 1rpx);
	  height: 94rpx;
	  line-height: 94rpx;
	  display: flex;
	  justify-content: center;
	  align-items: center;
	  font-size: 34rpx;
	  color: #999999;
	 
	}
	.mask-dialog-btn .cancel{
		background-color: #F8F8F8;
		border-right: 2rpx solid #E9E9E9;
		 border-radius: 0 0 0 20rpx;
	}
	.mask-dialog-btn .confirm {
		background-color: #F8F8F8;
		 border-radius: 0 0 20rpx 0;
		 color: #157CF2;
	}
	.mask-dialog-btn.single .confirm{
		width: 100%;
		border-radius: 0 0 20rpx 20rpx;
		background-color: #F8F8F8;
	}
</style>
