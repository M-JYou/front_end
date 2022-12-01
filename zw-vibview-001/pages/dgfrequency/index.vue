<template>
	<view class="pages">
		<view class="hadersboxs">
			<view class="haderscontents">
				<view class="" @click="back"> 
				<image class="imgsss" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgEAYAAAAj6qa3AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAAZiS0dEAAAAAAAA+UO7fwAAAAlwSFlzAAAASAAAAEgARslrPgAAAhVJREFUaN7tmL1LQmEUxv0AkRCHGgSnFqFRAoMWlyAQwSknFRocnMSGxnR1zKWptSFaHV2cBCX6B4TEzZZIi/DrPA2nKxh6GnrfDklneRAul9/vue9974su1//8jQEAYHsbBAJls9o8vydOIFAwyAW021iaSkWbz544AGBri7PZhDilkjavOXECgXw+FqvXZfF+n3N3V5v75+IAAK+X8/ZWFh8MOPf2tLkNibvdnNfXsvjzM2c0qs1tWPzqShZ/eeFXIxbT5jZcQLUqi7+9sXg8rs1rWLxclsXHY85EQpvXnDiBQMWiLD6b8XXptDavOXEAwOkpJ9Fq8fmcM5PR5jUnTiDQycniya4cp5BCQZvXnDgAIJXinEzWrnYCgc7PtXkNix8dcb6/y+96uazNa1j88JBzNJLFLy+1eb+Ox8xt3O7P231zv8dHbWFrw084meScTtduegQC5XLavPaK+BSUP3uTCV93fKzNa68IAMDFhbwnDIec+/vavJaLqNXkIp6eeEVEItq8lgrweDjv7uRzQbfLGQppc5svwvmnh0CgRkMuotPhDAS0uS0VEQxyPjzIRTQaTnHa3OaLAACEw5y9nrxH3NxwOueNDRpn81tshuJUq9q8los4OOB8fZWLODvT5rVXBID/E6XLWRH5/EJ45dzfc3q92rz2igCwdKIkEKjV4h87O9p8v1fE0orw+7V5NmY+AHD+7Cto9IgxAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDIyLTA3LTIxVDExOjM4OjE5KzA4OjAwRIxWywAAACV0RVh0ZGF0ZTptb2RpZnkAMjAyMi0wNy0yMVQxMTozODoxOSswODowMDXR7ncAAABNdEVYdHN2ZzpiYXNlLXVyaQBmaWxlOi8vL2hvbWUvYWRtaW4vaWNvbi1mb250L3RtcC9pY29uXzFhdW82eDllaGE3L3p1b2ppYW50b3Uuc3Zn6Oj+MwAAAABJRU5ErkJggg==" mode=""></image>
				</view>
			<view class="">{{data.pointname}} -时频谱</view>
			<view class=""> </view>
			</view>
		</view>
		
		

		<view class="leave_cont">
			<view class="ul">
				<view class="li">
					<text class="licontent" @click="container1">时域谱</text>
					<view class="flex1">
						<view class="date"></view>
					</view>
				</view>
				<view class="li">
					<text class="" @click="container2">频域谱	</text>
					<view class="flex1">
						<view class="date"></view>
					</view>
				</view>
			</view>

			<view class="headerrights">
				<view class="rights-lefts" @click="lastMoment">
					<image class="leftsimgs" src="../../static/index/leftss001.png" mode=""></image>
					<text>上一时刻</text>
				</view>
				<view class="rights-rights" @click="nextMoment">
					<text>下一时刻</text>
					<image class="rightsimgs" src="../../static/index/rights001.png" mode=""></image>
				</view>
			</view>

		</view>

		<!-- 上一时刻 频域普-->
		<view class="">
			<view class="speedsbox">
				<view class="speedsbox-content">
					<text>转速:{{nextDataLists.speed}} rpm</text>
					<text>分析频率:{{nextDataLists.alsFreq}}Hz</text>
					<text>谱线数:{{nextDataLists.spectroLine}}</text>
					<text>采样时间:{{nextDataLists.sampTime}}</text>
					<text>有效值:{{numFilter(nextDataLists.val)}} {{nextDataLists.engineerUnit}}</text>
				</view>
			</view>
			<view class="content">
				<view class="units">单位: ( {{data.unit}} )</view>
				<view class="charts-box" v-if="LastMoment">
					<!-- 上一刻-频域普 -->
					<qiun-data-charts class="chartsed" type="line" :opts="opts" :chartData="chartData1" />
				</view>
				<view class="charts-box" v-else>
					<!-- 上一刻-时域普 -->
					<qiun-data-charts class="chartsed" type="line" :opts="opts" :chartData="chartDataTime" />
				</view>
			</view>
			<view class="unitcontent">
				<text v-if='LastMoment'>Hz</text>
				<text v-else>s</text>
			</view>
		</view>

	</view>
</template>

<script>
	import {
		gatWavedata,
		gatPrevdata
	} from "@/api/api.js"
	export default {
		data() {
			return {
				engineerUnited:'',
				currentTime: '', //获取上一刻时间给下一刻
				prevIndex: 1,
				LastMoment: true, //判断时域，频域显隐
				customStyle: {
					backgroundColor: '#3e4558',
					color: '#AFDDFF',
				},
				timeData: [],
				freqData: [],
				lastDataLists: [],
				nextDataLists: [],
				chartData1: {},
				chartData2: {},
				chartDataTime: {},
				chartDatanext: {},
				opts: {
					color: ["#1890FF"],
					dataPointShape: false,
					dataLabel: false,
					padding: [20, 35, 0, 20],
					legend: {
						show:false
					},

					xAxis: {
						fontSize: 12,
						type: 'grid',
						gridType: 'dash',
						"labelCount": 5,
						disableGrid: true,
						boundaryGap:"justify",
					
					},
					yAxis: {
						fontSize: 12,
						gridType: "dash",
						dashLength: 1,
						tofix:3
					},
					extra: {
						line: {
							type: "straight",
							width: 1
						}
					}
				},
			};
		},

		onLoad(options) {
			let data = JSON.parse(options.data);
			this.data = data
			this.pointId = data.pointId
			this.signal = data.signal
			this.unit=data.unit
			console.log(this.units, "12121")
			this.getWavedata(this.pointId, this.signal);
			
			
			// #ifdef APP-PLUS
				   plus.screen.lockOrientation('landscape-primary'); 
				// #endif
		},
		onShow() {
		},
		
		onUnload() {
		// #ifdef APP-PLUS
		   plus.screen.lockOrientation('landscape-primary'); 
		// #endif
		},
		onReady() {
		},
		methods: {
			// 点击时域谱
			container1() {
				this.LastMoment = false
			},
			// 点击频域谱
			container2() {
				this.LastMoment = true
			},
			//上一时刻
			lastMoment() {
				this.prevIndex = 1
				this.getPrevdata()
			},
			//下一时刻
			nextMoment() {
				this.prevIndex = 2
				this.getPrevdata()
			},

			//获取上一刻数据
			getWavedata(pointId, signal) {
				let params = {
					Origin: "app",
					RequestData: {
						pointId: pointId,
						signalType: signal,
					}
				}
				// console.log(params.RequestData.pointId, "得到code")
				gatWavedata(JSON.stringify(params)).then(res => {
					// console.log(res, "数据1")
					if (res.statusCode == 200) {
						this.lastDataLists = res.data

						this.currentTime = this.lastDataLists.sampTime
						this.engineerUnited=res.data.engineerUnit
						var xt = res.data.alsFreq * 2.56; //分析频率
						var xf = res.data.alsFreq * 1.0 / res.data.spectroLine; //分析频率/普线数

						for (var i = 0; i < res.data.timeWave.length; i++) {
							this.timeData.push([i / xt, (res.data.timeWave[i]).toFixed(5)]);
						}
						for (var i = 0; i < res.data.freqWave.length; i++) {
							this.freqData.push([i * xf, (res.data.freqWave[i]).toFixed(5)]);
						}
						this.getPrevdata()
					}
				})
			},

			//ucharts-上一刻--频域普
			getServerData1() {
				setTimeout(() => {
					let res = {
						categories: this.xf,
						series: [{
						name: '单位' + (this.engineerUnited) + "   " +'(Hz)'+" "  + '值',
							data: this.freqData
						}, ],
					};
					this.chartData1 = JSON.parse(JSON.stringify(res));
				}, 500);
			},

			//ucharts-上一刻--时域普
			getServerTimeData1() {
				setTimeout(() => {
					let res = {
						categories: this.xt,
						series: [{
							name: '单位' + (this.engineerUnited) + "   " +'(s)'+" "  + '值',
							data: this.timeData
						}, ],
					};
					this.chartDataTime = JSON.parse(JSON.stringify(res));
				}, 500);
			},

			// ----------------------------

			//获取下一刻数据--数据从新渲染
			getPrevdata() {
				// console.log(this.pointId, this.signal, this.currentTime, '当前时间')
				let params = {
					Origin: "app",
					RequestData: {
						pointId: this.pointId,
						signalType: this.signal,
						time: this.currentTime,
						prevIndex: this.prevIndex
					}
				}
				// console.log(params.RequestData.time, "上一课时案件")
				gatPrevdata(JSON.stringify(params)).then(res => {
					// console.log(res, "数据2")
					if (res.statusCode == 200) {
						this.nextDataLists = res.data
						this.currentTime = res.data.sampTime
						this.timeData = []
						this.freqData = []
						var xt = res.data.alsFreq * 2.56;
						var xf = res.data.alsFreq * 1.0 / res.data.spectroLine;
						for (var i = 0; i < res.data.timeWave.length; i++) {
							this.timeData.push([i / xt, (res.data.timeWave[i]).toFixed(5)]);
						}
						for (var i = 0; i < res.data.freqWave.length; i++) {
							this.freqData.push([i * xf, (res.data.freqWave[i]).toFixed(5)]);
						}
						this.getServerData1()
						this.getServerTimeData1()
					}

				})
			},


			back() {
				uni.navigateBack({
					delta: 1
				})
			},
			numFilter(value) {
				// 截取当前数据到小数点后两位
				let realVal = parseFloat(value).toFixed(3)
				return realVal
			},
		}
	};
</script>

<style lang="scss" scoped>
	.charts-box {
		color: #AFDDFF;
		height: 208px;
	}

	.chartsed {
		width:100%;

	}

	.groupbox {
		display: flex;
		align-items: center;
		justify-content: space-around;
	}

	.uradios {
		color: #007AFF;
	}

	.pages-hareder {
		width: 99%;
		height: 50px;
		background-color: #007AFF;
	}

	.ubuttons {
		background-color: #3e4558;
		border: 1rpx solid #161E35;
		box-shadow: #161E35 0px 2px 5px 0px;
		height: 30px;
	}

	.wenzi {
		color: #FFFFFF;
	}
.unitcontent {
		display: flex;
		flex-direction: row;
		justify-content: center;
		color: #999999;
		font-size: 12px;
	}
	.units{
		color: #999999;
		height: 12px;
		font-size: 14px;
		position: relative;
		z-index: 9;
		left:24px;
		top:4px;
	}
	@import "index.scss";
</style>
//
