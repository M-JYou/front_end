<template>
	<view class="pages">
		<view class="hadercontents">
			<view class="haderbox">
			<view class="hadercontent-left"  @click="back">
			<image class="imgsss" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgEAYAAAAj6qa3AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAAZiS0dEAAAAAAAA+UO7fwAAAAlwSFlzAAAASAAAAEgARslrPgAAAhVJREFUaN7tmL1LQmEUxv0AkRCHGgSnFqFRAoMWlyAQwSknFRocnMSGxnR1zKWptSFaHV2cBCX6B4TEzZZIi/DrPA2nKxh6GnrfDklneRAul9/vue9974su1//8jQEAYHsbBAJls9o8vydOIFAwyAW021iaSkWbz544AGBri7PZhDilkjavOXECgXw+FqvXZfF+n3N3V5v75+IAAK+X8/ZWFh8MOPf2tLkNibvdnNfXsvjzM2c0qs1tWPzqShZ/eeFXIxbT5jZcQLUqi7+9sXg8rs1rWLxclsXHY85EQpvXnDiBQMWiLD6b8XXptDavOXEAwOkpJ9Fq8fmcM5PR5jUnTiDQycniya4cp5BCQZvXnDgAIJXinEzWrnYCgc7PtXkNix8dcb6/y+96uazNa1j88JBzNJLFLy+1eb+Ox8xt3O7P231zv8dHbWFrw084meScTtduegQC5XLavPaK+BSUP3uTCV93fKzNa68IAMDFhbwnDIec+/vavJaLqNXkIp6eeEVEItq8lgrweDjv7uRzQbfLGQppc5svwvmnh0CgRkMuotPhDAS0uS0VEQxyPjzIRTQaTnHa3OaLAACEw5y9nrxH3NxwOueNDRpn81tshuJUq9q8los4OOB8fZWLODvT5rVXBID/E6XLWRH5/EJ45dzfc3q92rz2igCwdKIkEKjV4h87O9p8v1fE0orw+7V5NmY+AHD+7Cto9IgxAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDIyLTA3LTIxVDExOjM4OjE5KzA4OjAwRIxWywAAACV0RVh0ZGF0ZTptb2RpZnkAMjAyMi0wNy0yMVQxMTozODoxOSswODowMDXR7ncAAABNdEVYdHN2ZzpiYXNlLXVyaQBmaWxlOi8vL2hvbWUvYWRtaW4vaWNvbi1mb250L3RtcC9pY29uXzFhdW82eDllaGE3L3p1b2ppYW50b3Uuc3Zn6Oj+MwAAAABJRU5ErkJggg==" mode=""></image>
			</view>
				<view class="hadercontent-center">
					{{name}}
				</view>
			<view class="hadercontent-right"></view>
			</view>
		</view>
		
		<!-- 列表 -->
		<view class="content">
			<view class="" v-for="(item,index) in runList" :key='index' @click="measurNmber(item)" >
				<view class="arrondi ">
					<view class="arrondi-left">
						<view class="left-imgs">
							<image class="imgss" src="data:image/png;base64,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" mode=""></image>
						</view>
					
						<view class="left-titles">
							<view class="titles">{{item.name}}</view>
							<view class="contents">测点个数:{{item.sonCount}}</view>
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
import {deviceitemsList} from "@/api/api.js"
	export default {
		data() {
			return {
				
				runList: [],
		
				kehuList: [
					{
						dpends: "F-2108",
						points: "测点个数: 8"
					},
				
				],
			};
		},
		onLoad(options) {
	let {code,name} = options
	this.code = code
	this.name = name
	this.getdeviceFists(code)
		},
		onUnload() {
		    // #ifdef APP-PLUS
		    plus.screen.lockOrientation('portrait-primary'); //锁死屏幕方向为竖屏
		    // #endif
		},
		onShow() {
			// #ifdef APP-PLUS
			setTimeout(()=>{
					if(this.runList==""){
						uni.showLoading({
							title: '加载中'
						});
				}
			},600)
			
			// #endif
   // #ifdef APP-PLUS
		    plus.screen.lockOrientation('portrait-primary'); //锁死屏幕方向为竖屏
		    // #endif
		},
		
		
		
		methods: {

			// 获取数据
		getdeviceFists(code) {
				deviceitemsList(code).then(res => {
					// console.log(res)
					// return
					if(res.statusCode ==200){
               this.runList = res.data
							 // console.log(this.runList,90)
							 
					}
					
				})
			},
			//点击数据列表跳转
			measurNmber(item){
				const code = item.code//拿到id
				const name = item.name //拿到标题名称
				const itemcode = this.code
				console.log(itemcode,3)
				console.log(code,4)
				// return
				uni.navigateTo({
					url: 	`/pages/projectDevie/index?code=${code}&name=${name}&itemcode=${itemcode}`
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
	.unibarbox{
		font-size: 14px;
	}
</style>
