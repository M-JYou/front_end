<template>
	<!-- #ifdef APP-NVUE -->
		<waterfall v-if="type=='waterfall'"
		:style="[getListStyle]"
		ref="list"
		:column-count="columnCount" 
		:column-width="columnWidth"
		:column-gap="columnGap"
		:left-gap="leftGap"
		:right-gap="rightGap"
		:show-scrollbar="false" 
		alwaysScrollableVertical 
		:enableBackToTop="enableBackToTop" 
		:offsetAccuracy="offsetAccuracy" 
		:loadmoreoffset="loadmoreoffset" 
		@loadmore="loadMore"
		>
			<refresh v-if="isRefresh" class="refresh" :style="[{backgroundColor:refreshBackgroundColor}]" :display="refreshing ? 'show' : 'hide'" @refresh="onrefresh" @pullingdown="onpullingdown">
				<div class="refresh-view">
					<image class="refresh-icon" :src="refreshIcon" :style="{width: (refreshing || pulling) ? 0: '32px'}" :class="{'refresh-icon-active': refreshFlag}"></image>
					<!-- <text class="refresh-icon l-icon" :class="{'refresh-icon-active': refreshFlag}" :style="{width: (refreshing || pulling) ? 0: '32px',fontSize: (refreshing || pulling) ? '24px': '32px',color:refreshColor}">&#xe801;</text> -->
					<text class="loading-text" :style="[{color:refreshColor}]">{{refreshText}}</text>
				</div>
			</refresh>
			<slot></slot>
			<loading v-if="startLoadMore" class="loading-more bg-f2" :display="'show'">
			    <text class="loading-more-text">{{loadingText}}</text>
			</loading>
		</waterfall>
		
		<list v-else
		:scrollable="true"
		ref="list"
		:style="[getListStyle]"
		@loadmore="loadMore"
		:show-scrollbar="false" 
		alwaysScrollableVertical 
		:enableBackToTop="enableBackToTop" 
		:offsetAccuracy="offsetAccuracy" 
		:loadmoreoffset="loadmoreoffset" 
		>
			<refresh v-if="isRefresh" class="refresh" :style="[{backgroundColor:refreshBackgroundColor}]" :display="refreshing ? 'show' : 'hide'" @refresh="onrefresh" @pullingdown="onpullingdown">
				<div class="refresh-view">
					<image class="refresh-icon" :src="refreshIcon" :style="{width: (refreshing || pulling) ? 0: '32px'}" :class="{'refresh-icon-active': refreshFlag}"></image>
					<!-- <text class="refresh-icon l-icon" :class="{'refresh-icon-active': refreshFlag}" :style="{width: (refreshing || pulling) ? 0: '32px',fontSize: (refreshing || pulling) ? '24px': '32px',color:refreshColor}">&#xe801;</text> -->
					<text class="loading-text" :style="[{color:refreshColor}]">{{refreshText}}</text>
				</div>
			</refresh>
			<slot></slot>
			<cell>
				<view class="loading-more bg-f2" v-if="showLoadMore">
					<text class="loading-more-text">{{loadingText}}</text>
				</view>
			</cell>
		</list>
	<!-- #endif -->
	<!-- #ifndef APP-NVUE -->
	<scroll-view 
	scroll-y 
	:style="[getListStyle]" 
	refresher-enabled 
	scroll-anchoring
	refresher-default-style="none"
	:refresher-triggered="refreshing" 
	:refresher-threshold="refresherThreshold"
	:refresher-background="refreshBackgroundColor"
	@refresherpulling="onpullingdownh5" 
	@refresherrefresh="onrefresh"
	@refresherrestore="onRestore" 
	@refresherabort="onAbort"
	@scrolltolower="loadMore"
	>
		<transition name="fade">
			<view v-if="isRefresh" v-show="showrefreshing" class="refresh-view">
				<image class="refresh-icon" :src="refreshIcon" :style="{width: (refreshing || pulling) ? 0: '32px'}" :class="{'refresh-icon-active': refreshFlag}"></image>
				<text class="loading-text" :style="[{color:refreshColor}]">{{refreshText}}</text>
			</view>
		</transition>
		<slot></slot>
		<view class="loading-more bg-f2" v-if="showLoadMore">
			<text class="loading-more-text">{{loadingText}}</text>
		</view>
	</scroll-view>
	<!-- #endif -->
</template>

<script>
	/**
	 * nvue上下拉刷新列表
	 * @description 本组件只支持nvue,其余端暂未添加兼容
	 * @property {String} init 初始化时是否自动触发一次create
	 * @property {String} type [waterfall,list] 列表类型(waterfall=瀑布流)
	 * @property {Boolean} isRefresh 是否开启下拉刷新
	 * @property {String} refreshBackgroundColor 下拉刷新区域背景色
	 * @property {String} refreshColor 下拉刷新区域字体色
	 * @property {Object} downText 下拉刷新提示
	 * @property {Object} upText 上拉加载提示
	 * @property {String} position 组件定位模式，默认fixed
	 * @property {String} unit 所有布局单位，设置后topUnit等失效
	 * @property {String} topUnit 布局单位，默认rpx
	 * @property {String} bottomUnit 布局单位，默认rpx
	 * @property {String} aboutUnit 布局单位，默认rpx
	 * @property {Number} top top值
	 * @property {Number} bottom bottom值
	 * @property {Number} left left值
	 * @property {Number} right right值
	 * @property {Number} columnCount 仅瀑布流有效：描述瀑布流的列数
	 * @property [Number,String] columnWidth 仅瀑布流有效：描述瀑布流每一列的列宽
	 * @property {Number} columnGap 仅瀑布流有效：列与列之间的间隙
	 * @property {Number} leftGap 仅瀑布流有效：列与左边cell的间隙
	 * @property {Number} rightGap 仅瀑布流有效：列与右边cell的间隙
	 * 
	 * @property {Function} loadData 上拉和下拉操作完成回调
	 */
	export default {
		props: {
			init: Boolean,
			type: {
				type: String,
				default: 'list' // waterfall=瀑布流
			},
			isRefresh: {
				type: Boolean,
				default: true
			},
			refreshBackgroundColor: {
				type: String,
				default: '#f2f2f2'
			},
			refreshColor: {
				type: String,
				default: '#999999'
			},
			columnCount: {
				type: [Number,String],
				default: 2
			},
			columnWidth: {
				type: [Number,String],
				default: 'auto'
			},
			columnGap: {
				type: Number,
				default: 0
			},
			leftGap: {
				type: Number,
				default: 0
			},
			rightGap: {
				type: Number,
				default: 0
			},
			showLoadMore: {
				type: Boolean,
				default: true
			},
			enableBackToTop: {
				type: [Boolean, String],
				default: false
			},
			offsetAccuracy: {
				type: Number,
				default: 100
			},
			loadmoreoffset: {
				type: [String, Number],
				default: 30
			},
			unit:{
				type: String,
				default: ''
			},
			topUnit:{
				type: String,
				default: 'rpx'
			},
			top: {
				type: Number,
				default: 0
			},
			bottom: {
				type: Number,
				default: 0
			},
			bottomUnit: {
				type: String,
				default: 'rpx'
			},
			aboutUnit: {
				type: String,
				default: 'rpx'
			},
			left: {
				type: Number,
				default: 0
			},
			right: {
				type: Number,
				default: 0
			},
			backgroundColor: {
				type: String,
				default: '#f2f2f2'
			},
			position: {
				type: String,
				default: 'fixed'
			},
		},
		data() {
			return {
				showrefreshing: false,
				refresherThreshold: 40,
				startLoadMore: false,
				canLoadMore: true,
				refreshing: false,
				refreshFlag: false,
				refreshText: "",
				isLoading: false,
				loadingText: '正在加载…',
				pulling: false,
				angle: 0,
				loadingMoreText: {
					contentdown: '',
					contentrefresh: '',
					contentnomore: ''
				},
				refreshIcon: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAB5QTFRFcHBw3Nzct7e39vb2ycnJioqK7e3tpqam29vb////D8oK7wAAAAp0Uk5T////////////ALLMLM8AAABxSURBVHja7JVBDoAgDASrjqj//7CJBi90iyYeOHTPMwmFZrHjYyyFYYUy1bwUZqtJIYVxhf1a6u0R7iUvWsCcrEtwJHp8MwMdvh2amHduiZD3rpWId9+BgPd7Cc2LIkPyqvlQvKxKBJ//Qwq/CacAAwDUv0a0YuKhzgAAAABJRU5ErkJggg==",
			}
		},
		computed: {
			getListStyle() {
				return {
					top:this.top+(this.unit||this.topUnit),
					left:this.left+(this.unit||this.aboutUnit),
					right:this.right+(this.unit||this.aboutUnit),
					bottom: this.bottom+(this.unit||this.bottomUnit), 
					backgroundColor: this.backgroundColor, 
					position: this.position,
					zIndex: 1,
					overflowAnchor: 'auto'
				}
			}
		},
		created() {
			this.isAndroid = uni.getSystemInfoSync().platform != 'ios';
			if (this.init) {
				this.onrefresh();
			}
		},
		methods: {
			onRestore() {
				this.showrefreshing = false;
			},
			onAbort() {
				this.showrefreshing = false;
			},
			
			
			create() {
				this.refreshData();
			},
			loadData(refresh = false) {
				if (this.isLoading) {
					return;
				}
				this.isLoading = true;
				this.loadingText = '正在加载…';
				this.$emit('loadData',refresh);
			},
			endLoad(refresh,more = false) {
				this.isLoading = false;
				this.startLoadMore = false;
				if (!more) {
					this.loadingText = '没有更多了'
					this.canLoadMore = false;
				} else {
					this.loadingText = '上拉加载更多'
					this.canLoadMore = true;
				}
				if (refresh) {
					this.refreshing = false;
					this.refreshFlag = false;
					this.refreshText = "已刷新";
					if (this.pullTimer) {
						clearTimeout(this.pullTimer);
					}
					this.pullTimer = setTimeout(() => {
						this.startRefresh = false;
						this.pulling = false;
						this.$forceUpdate();
					}, 1000);
				}
				else {
					this.startRefresh = false;
				}
			},
			loadMore(e) {
				// #ifdef APP-NVUE
				if (this.startRefresh) {
					this.$refs["list"].resetLoadmore();
					return;
				}
				// #endif
				if (!this.canLoadMore) {
					return
				}
				this.startLoadMore = true;
				this.loadData();
				// #ifdef APP-NVUE
				setTimeout(()=>{
					this.$refs["list"].resetLoadmore();
				},500);
				// #endif
			},
			refreshData() {
				if (this.isLoading) {
					return;
				}
				this.pulling = true;
				this.refreshing = true;
				this.refreshText = "正在刷新...";
				this.loadData(true);
			},
			onrefresh(e) {
				this.startRefresh = true;
				this.refreshData();
			},
			onpullingdownh5(e) {
				if (!this.showrefreshing) {
					this.showrefreshing = true;
				}
				
				if (Math.abs(e.target.deltaY) > Math.abs(this.refresherThreshold)) {
					this.isAndroid&&(this.startRefresh = true);
					this.refreshFlag = true;
					this.refreshText = "释放立即刷新";
				} else {
					this.refreshFlag = false;
					this.refreshText = "下拉可以刷新";
				}
				
			},
			onpullingdown(e) {
				if (this.refreshing) {
					return;
				}
				this.pulling = false;
				if (Math.abs(e.pullingDistance) > Math.abs(e.viewHeight)) {
					this.isAndroid&&(this.startRefresh = true);
					this.refreshFlag = true;
					this.refreshText = "释放立即刷新";
				} else {
					this.refreshFlag = false;
					this.refreshText = "下拉可以刷新";
				}
			}
		}
	}
</script>

<style scoped lang="scss">
@mixin display($value) {
  /* #ifndef APP-NVUE */
  display: $value;
  /* #endif */
}

@mixin border($color) {
  border-color: $color;
  border-width: 1px;
  border-style: solid;
}

@mixin radius($size) {
  border-bottom-left-radius: $size;
  border-bottom-right-radius: $size;
  border-top-left-radius: $size;
  border-top-right-radius: $size;
}
.listview {
	left: 0;
	right: 0;
	bottom: 0;
	/* #ifndef APP-NVUE */
	@include display(flex);
	flex-direction: column;
	/* #endif */
	/* #ifndef MP-ALIPAY */
	flex-direction: column;
	/* #endif */
}

.refresh {
	justify-content: center;
}

.refresh-view {
	@include display(flex);
	width: 750rpx;
	height: 64px;
	flex-direction: row;
	flex-wrap: nowrap;
	align-items: center;
	justify-content: center;
}

.refresh-icon {
	width: 32px;
	height: 32px;
	transition-duration: .5s;
	transition-property: transform;
	transform: rotate(0deg);
	transform-origin: 15px 15px;
}

.refresh-icon-active {
	transform: rotate(180deg);
}

.loading-icon {
	width: 28px;
	height: 28px;
	margin-right: 5px;
	color: #999999;
}

.loading-text {
	margin-left: 2px;
	font-size: 16px;
	color: #999999;
}

.loading-more {
	width: 750rpx;
	align-items: center;
	justify-content: center;
	padding-top: 14px;
	padding-bottom: 14px;
	text-align: center;
}

.loading-more-text {
	font-size: 28upx;
	color: #999;
}

</style>
