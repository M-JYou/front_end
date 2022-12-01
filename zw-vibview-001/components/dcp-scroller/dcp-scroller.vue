<template>
	<view class="scroller-content">
		<view
			:class="['loading-box flex-center', { loading: status > 1 }]"
			:style="{
				height: `${move}px`,
				opacity: `${Math.abs(move / maxPull)}`
			}"
		>
			<view v-show="status === 2" class="loading-icon flex-center">
				<image src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAMAAABHPGVmAAAAilBMVEUAAADNzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc0JMh4QAAAALXRSTlMAC+s3+yMeY/NVFHEF7+PAljHGakMZENayo5AnDZyFfVvMuKt2KzyKTvfcgknnAwAMAAADiUlEQVRo3uzV25KiQBAE0ARRkLsioA7qeh1XN///93ZsHRoFF2jWiHnwvBFBkEVXUaAld+77oYbX2vLLFC+VUIjxSn0Ke7zSwOBFHwU/PsTcLEYtQnbbCK2F/DJpGuIGJBdo6Q8vdLdhyJIXR7TzSSFsFtKncEY7Jq9GjULGaiHuiUJaHyIrmqClEa/M+pDEoLBQ2VKC59aGhBQsDa0lFoVZXcieV0MoWFWcdP8a8oGClEIAFZpHwYc0YCnEkXOoYsgrpxD8mxcZcrZBYQ5FAQUjfpjWCNJMvq6aPstV9pbLQ/FSNk7VgsIGTy3lHUrkZ7bCU2cKA6iRo5NqeKonq2jg4ERV5azSdJ7gH3YW9SXK4ujsPBY3J2lFUGDvE5SZHsk17kQUxj38F6OgatOceBPG6Myek1V7ecZvuomOfun8tkNR7DM33aODjylzfoY78YzSpwZF7tlgblse10nA3GkIJceUOX9YfZg+c6ZSNyiFGappjp73X1P9AQmbCZ6zQ97YaG3PG6/uHEZT5Z9pYsiG1zkG5HigvE7Hk4YNHELNcLox8fb29pNpmSZlCTpL7h6oXSJmuqcXWOsDOumtLb3A87YZFiyJ0YFt8NEWBkscdBCxxPrbrtktJwgDUXiVAQXxB/wpqIio6GDZ93+91rGd0J4OCZtx6oXfNbCQhOzuOSFGcrvqGajowEBEFmwYiMmLuYGsIsYquEnh3mKfHcXZCciSwPnxwA29ePHimXFONclYDkYRmeBlzFw4okZrzMypS1rm4/s2uaXuTO636vJRFPMXgk/pVWygOQQXqxwcqp5zvTTo8nlHAk7Mmo6uTFjxTiJyVhwiAvJGRZE4JCQ6cstoJKgU2OsFU1Bnv7F1EcM13wH5c41dvpxF8rdKtBfrUK0Cyh49KU6XBKwC0hAQEmT8SfF75EfHWU3IZVjFfutEF9XbgJDF7jLopPuUOhHcJwvCvu73P4n9GfTo9FeI8RiV163vr5pTwuCoyKyNghTOhLl/RvcvJkDkNapJGofYKsztGrMUB9AlxUyQgODuqtdunIXg4gncTF9nAdayhI3OHgbBNHGVd7GlPogLe64hG3D2IAgYW1zKaoK+mR8/UW8kKAlqsyALXGDm229iekYiNZUyMItdA9Mgq9tajHvdl1eWb82PlPTybPSsh2P+L4gL2eMBBGD1PoICzpk8AO8wHE67TskHcxXp7lgHFmoAAAAASUVORK5CYII="></image>
				<text>努力加载中...</text>
			</view>
			<text v-if="status === 0">下拉刷新</text>
			<text v-else-if="status === 1">松开刷新</text>
			<text v-else-if="status === 3">加载成功</text>
		</view>
		<view
			class="scrollerbox"
			@touchstart="touchstart"
			@touchmove="touchmove"
			@touchend="touchend"
		><slot></slot></view>
	</view>
</template>

<script>
export default {
	props: {
		isLoading: {
			type: Boolean,
			required: true
		}
	},
	data() {
		return {
			winHeight: 2000,
			
			// 滑动距离
			move: 0,
			
			// 加载区域高度 触发点
			offset: 50,
			
			// 最大可下拉距离
			maxPull: 160,
			
			// 是否操作中
			action: false,
			
			// 0.初始  1.松开可刷新  2.刷新中  3.刷新成功
			status: 0,
		};
	},
	mounted() {		
		const systemInfo = uni.getSystemInfoSync();
		if (systemInfo && systemInfo.screenHeight) {
			this.winHeight = systemInfo.screenHeight;
		}
	},
	watch: {
		isLoading(newVal) {
			// 父级传递刷新
			if (newVal) {
				this.status = 2;
				this.move = this.offset;
			} else {
				// 不刷新回弹动画
				this.status = 3;
				// 加载成功，露出350ms
				setTimeout(() => {
					this.close();
				}, 350);
			}
		}
	},
	methods: {
		touchstart(ev) {
			if (this.action) return;
			this.startY = ev.changedTouches[0].clientY;
		},
		touchmove(ev) {
			const { startY, winHeight, offset, maxPull } = this;
			
			const diffY = ev.changedTouches[0].clientY - startY;

			if (diffY < 0) return;
			
			// 阻尼算法 正弦函数0-90°段 0-1之间
			const rate = Math.sin(Math.PI * 0.5 * (diffY / winHeight));
			const move = maxPull * rate;
			
			this.move = move;
			this.status = move < offset ? 0 : 1;
		},
		touchend() {
			uni.createSelectorQuery().selectViewport().scrollOffset(res => {
			  // console.log("竖直滚动位置" + res.scrollTop);
				if (res.scrollTop > 500) {
					this.close();
					return;
				};
				
				if (this.move > this.offset) {
					this.$emit('refresh');
					this.move = this.offset;
				} else {
					this.close();
				}
			}).exec();
		},
		close() {
			this.move = 0;
			this.action = false;
			this.status = 2;
			// 动画完成以后重置
			setTimeout(() => {
				this.status = 0;
			}, 300);
		},
		// 手动触发下拉刷新
		refresh() {
			this.status = 2;
			this.move = 100;
			
			setTimeout(() => {
				this.status = 3;
				// 加载成功，露出350ms
				setTimeout(() => {
					this.close();
				}, 350);
			}, 300);
		}
	}
};
</script>

<style lang="scss" scoped>
.flex-center {
	display: flex;
	align-items: center;
	justify-content: center;
}

.loading-box {
	&.loading {
		transition: height .3s;
	}
	image{
		width: 32rpx;
		height: 32rpx;
		animation: rightSpin 2s linear infinite both;
	}
	text{
		margin-left: 4px;
		font-size: 30rpx;
		color: #666;
		text-shadow: 1rpx 1rpx 4rpx #fff;
	}
}
@keyframes rightSpin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
.scroller-content::-webkit-scrollbar {  
	display: none !important;
	width: 0 !important;
	height: 0 !important;
	-webkit-appearance: none;
	background: transparent;
}
</style>
