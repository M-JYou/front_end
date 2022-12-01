<template>
	<view class="pages">
<view class="hadersboxs">
		<view class="haderscontents">
			<view class="" @click="back">
				<image class="imgsss"
					src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgEAYAAAAj6qa3AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAAZiS0dEAAAAAAAA+UO7fwAAAAlwSFlzAAAASAAAAEgARslrPgAAAhVJREFUaN7tmL1LQmEUxv0AkRCHGgSnFqFRAoMWlyAQwSknFRocnMSGxnR1zKWptSFaHV2cBCX6B4TEzZZIi/DrPA2nKxh6GnrfDklneRAul9/vue9974su1//8jQEAYHsbBAJls9o8vydOIFAwyAW021iaSkWbz544AGBri7PZhDilkjavOXECgXw+FqvXZfF+n3N3V5v75+IAAK+X8/ZWFh8MOPf2tLkNibvdnNfXsvjzM2c0qs1tWPzqShZ/eeFXIxbT5jZcQLUqi7+9sXg8rs1rWLxclsXHY85EQpvXnDiBQMWiLD6b8XXptDavOXEAwOkpJ9Fq8fmcM5PR5jUnTiDQycniya4cp5BCQZvXnDgAIJXinEzWrnYCgc7PtXkNix8dcb6/y+96uazNa1j88JBzNJLFLy+1eb+Ox8xt3O7P231zv8dHbWFrw084meScTtduegQC5XLavPaK+BSUP3uTCV93fKzNa68IAMDFhbwnDIec+/vavJaLqNXkIp6eeEVEItq8lgrweDjv7uRzQbfLGQppc5svwvmnh0CgRkMuotPhDAS0uS0VEQxyPjzIRTQaTnHa3OaLAACEw5y9nrxH3NxwOueNDRpn81tshuJUq9q8los4OOB8fZWLODvT5rVXBID/E6XLWRH5/EJ45dzfc3q92rz2igCwdKIkEKjV4h87O9p8v1fE0orw+7V5NmY+AHD+7Cto9IgxAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDIyLTA3LTIxVDExOjM4OjE5KzA4OjAwRIxWywAAACV0RVh0ZGF0ZTptb2RpZnkAMjAyMi0wNy0yMVQxMTozODoxOSswODowMDXR7ncAAABNdEVYdHN2ZzpiYXNlLXVyaQBmaWxlOi8vL2hvbWUvYWRtaW4vaWNvbi1mb250L3RtcC9pY29uXzFhdW82eDllaGE3L3p1b2ppYW50b3Uuc3Zn6Oj+MwAAAABJRU5ErkJggg=="
					mode=""></image>
			</view>
			<view class="">{{data.pointname}} -趋势图</view>
			<view class=""> </view>
		</view>
	</view>


		<view class="leave_cont">
			<view class="ul">
				<view class="li" @click="openDatePicker('stime')">
					<text>开始时间:</text>
					<view class="flex1">

						<view class="date">{{stime}}</view>
					</view>
				</view>
				<view class="li" @click="openDatePicker('etime')">
					<text>结束时间:</text>
					<view class="flex1">

						<view class="date">{{etime}}</view>
					</view>
				</view>
			</view>

			<view class="headerrights">
				<view class="rights-lefts" @click="query">
					<image class="leftsimgs"
						src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgEAYAAAAj6qa3AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAAZiS0dEAAAAAAAA+UO7fwAAAAlwSFlzAAAASAAAAEgARslrPgAAAwNJREFUaN7tWM8rdFEYPmfyW4yUMFYWUxPZ2PpVpCgRZWVDFA3xX1iQrb2NlQUmEqVYsEB+FCWl/JpsxEhkcJ5v8Tp4x9UM7p3zfX3zbp7O7c77Ps9zzpx73iNEIv7vkE4lhoKCSkqiUUEBoccjpJBC6ufBoICAQDAoXdIlXeGwaUN+Kbi+HgCAiQnCqytECwUFdXdHg6kpGre309jlMq3ra8EAgNJSwuVla3XPz4Q7O4Rzc4SBAAnd2KDxw4O1MdvbhFVVpvVGCG9uJry95ayPjwm7uwlzc2PLl5n5NvNaOIunJ0K/35xwBQXV1EREXl74TI2MEKak2GOwy0U4NMQN0PUGB+MnHADg8xGGQnxm+vriU7+hgTAc5obU1jpvgIKCmp/nMzA25nhhSyP8fj4BBwf8a2O78MpKXvD0lDA93YwBUhKv9XXOq6vLoYLj43zmBwbiLdyaV2Mj57W05FCh8/PXKoqwqMi4Aa+bLfG5uXnbGxQUlNttU4Hs7MjPm2nh1jwXFznP8vLv5vl80pJCCunx8IfBoGnB1jw/8IKAQCTvnxhA2WBaX4wuvPcyUkghv8/7swEQELi85OPCQtNSrXlG8vrA+9f5AQAXF3wT/P4Ss123goJKTrZrE4zSbQUChHqptbaaNoCWel0dDdxuWgmrq9ROh0K21SFHq6v5LntyQpiWZko/1V9b4+eAnh6HCy4s8IKjo3EXrqCgensjP892NWFRDCgpIfzQ/jrtPBOuL1oeHwl1N6r/CnEIKtjSwgkoRQSHh/XmZE8dKQn7+3kXqCMYpHr5+XEzgBNsa+NXWTqOjgg7OwlzcmLLl5HxlhcAsLXF82rDz874c90NmjBCQUGVlRGRlRVYhu7bNzcJZ2cJp6d5V3d/b/37w0PCmhp6Py+Pxnt7/L39fWNG8JnUXdrkJOH1NWIKbcDMDGFHx1d9/l9vBCeqDyzFxYQVFfqSk9DrJUxN/Vn+aEZkZZn2wfHgRuzuEuqTq89nml/8jADwfrvs9Zrmk4hEJOLfiD+Hp7UjbUsF/QAAACV0RVh0ZGF0ZTpjcmVhdGUAMjAyMi0wNy0yMVQxNDoyNzowMCswODowMDAUJNgAAAAldEVYdGRhdGU6bW9kaWZ5ADIwMjItMDctMjFUMTQ6Mjc6MDArMDg6MDBBSZxkAAAASXRFWHRzdmc6YmFzZS11cmkAZmlsZTovLy9ob21lL2FkbWluL2ljb24tZm9udC90bXAvaWNvbl9pam1leGI3YWh5ay9zb3VzdW8uc3ZnlOKnXAAAAABJRU5ErkJggg=="
						mode=""></image>
						<text>查询</text>
				</view>
				
			</view>
		</view>
		<view class="content">
			<view class="units">单位:( {{unitsed}} )</view>
			<view class="charts-box">
				<qiun-data-charts class="chartsed" type="line" :opts="opts" :chartData="chartData" 
				:ontouch="true"
					:onzoom="true" :disableScroll="true"/>
			</view>
		</view>
		<!-- 单选框 -->
		<view class="groupbox">
			<u-radio-group v-model="value" @change="radioGroupChange" size="20px">
				<u-radio label-size="15px" icon-size="5px" @change="radioChange" v-for="(item, index) in list" :key="index" :name="item.name"
					:title="item.title" :disabled="item.disabled">
					{{item.title}}
				</u-radio>
			</u-radio-group>
		</view>

		<u-picker mode="time" :default-time="defaultTime" v-model="show" :params="params" @confirm="confirm"></u-picker>
	</view>
</template>

<script>
	import {
		gatcurveDatalist
	} from "@/api/api.js"
	export default {
		data() {

			return {
				units:'',
				unitsed:'',
				list: [
					{

						title: '加速度',
						disabled: false,
						name: "acc"
					},
					{
						title: '速度',
						disabled: false,
						name: "vel"
					},
					{
						title: '位移',
						disabled: false,
						name: "dis"
					},
					{
						title: '包络',
						disabled: false,
						name: "env"
					},
					{
						title: '温度',
						disabled: false,
						name: "temp"
					},
					{
						title: '转速',
						disabled: false,
						name: "speed",
						engineerUnit:"m"
					}
				],
				// u-radio-group的v-model绑定的值如果设置为某个radio的name，就会被默认选中
				defaultTime:'',//默认时间
				value: 'vel',
				signal: '',
				datayAxis: [],
				dataxAxis: [],
				//留个值
				acc: [],
				vel: [],
				dis: [],
				env: [],
				temp: [],
				speed: [],
				params: {
					year: true,
					month: true,
					day: true,
					hour: true,
					minute: true,
					second: false
				},
				show: false, //时间选择器显示隐藏
				stime: "",
				etime: "",
				currentTime: '',
				chartData: {},

				opts: {
					rotate: false,
					rotateLock: false,
					enableScroll: true,
					enableMarkLine: true,
					color: ["#1890FF"],
					dataPointShape: false,
					dataLabel: false,
					padding: [26, 55, 20, 68],
					legend: {
						show: false //去点name名称
					},
					xAxis: {
						splitNumber:2,
						gridEval:1,
						rotateLabel:true,
						rotateAngle:0.0001,
						fontSize: 10,
						type: 'grid',
						gridType: 'dash',
						"labelCount": 5,
						disableGrid: true,
						boundaryGap:"justify",
						scrollShow: true,
						itemCount:500000,
						scrollAlign: 'bottom', //滚动条初始位置
						scrollBackgroundColor: '#262b41', //默认为 #EFEBEF
						scrollColor: '#262b41', //默认为 #A6A6A6
						dashLength: 4,
							scrollAlign:'right',
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


				runList: [],

				kehuList: [

				],
			};
		},
		onLoad(options) {
			// #ifdef APP-PLUS
			setTimeout(() => {
			plus.screen.lockOrientation("landscape-primary");
			}, 300);
			// #endif
			
			this.stime = this.change_date(3);
			this.etime = this.change_date(0);
			let data = JSON.parse(options.data);
			this.data = data
			this.pointcode = data.pointcode
			console.log(this.pointcode, "qw")


			this.gatDatalist(this.pointcode);
		},
		onShow() {
			setTimeout(() => {
				if(this.datayAxis==''){
					uni.showLoading({
						title: '加载中'
					});
				}
			}, 800);
		
		},
		onUnload() {
			// #ifdef APP-PLUS
			plus.screen.lockOrientation('portrait-primary');
			// #endif

		},
		onReady() {
			this.getServerData();
		},
		methods: {
			change_date(days) {
				// 参数表示在当前日期下要增加的天数
				var now = new Date();
				// + 1 代表日期加，- 1代表日期减
				now.setDate((now.getDate() + 1) - 1 * days);
				var year = now.getFullYear();
				var month = now.getMonth() + 1;
				var day = now.getDate();
							
				var hour = now.getHours();
				var minute = now.getMinutes();
				var second = now.getSeconds();
				if(hour < 10)
					hour = '0' + hour;
				if(minute < 10)
					minute = '0' + minute;
				if(second < 10)
					second = '0' + second;
				if(month < 10) {
					month = '0' + month;
				}
				if(day < 10) {
					day = '0' + day;
				}			
				return year + '-' + month + '-' + day + " " + hour + ":" + minute + ":" + second;
			},
		
			// 选中某个单选框时，由radio时触发
			radioChange(value) {
				this.value = value
				this.gatDatalist(this.pointcode)

			},


			// 选中任一radio时，由radio-group触发
			radioGroupChange(value) {
				// console.log(this.vel,"11")
			},




			//点击查询
			query() {
				this.gatDatalist(this.pointcode)
				// console.log(this.gatDatalist, '010')
			},
			
			openDatePicker(time) {
				this.show = !this.show
				this.currentTime = time
			},
			confirm(time) {
				console.log(time)
				let {
					year: y,
					month: m,
					day: d,
					hour: h,
					minute: mm
				} = time
				console.log(`${y}/${m}/${d} ${h}:${mm}`)
				this[this.currentTime] = `${y}/${m}/${d} ${h}:${mm}`
			},


			//获取曲线数据
			gatDatalist(pointcode) {
				let params = {
					Origin: "app",
					RequestData: {
						// pointId:"10070010010301",
						pointId: pointcode,
						startTime: this.stime,
						endTime: this.etime,
						itemName:this.value
						// acc: this.acc,
						// vel: this.vel,
						// dis: this.dis,
						// speed: this.speed,
						// temp: this.temp,
						// env: this.env
					},
				}
				console.log(params.RequestData.pointId, "得到code1")
				gatcurveDatalist(JSON.stringify(params)).then(res => {
					console.log(res,110)
					if (res.statusCode == 200) {

						if(this.value === 'env'){
							this.datayAxis = res.data.data.map(item => {
								return(item.env).toFixed(3)
							})
							this.unitsed = "gE"
							
						}
						if(this.value === 'vel'){
							this.datayAxis = res.data.data.map(item => {
								return (item.vel).toFixed(3)
							})
							this.unitsed = "mm/s"
						}
						if(this.value === 'speed'){
							this.datayAxis = res.data.data.map(item => {
								return(item.speed).toFixed(3)
							})
							this.unitsed = "rpm"
						}
						if(this.value === 'temp'){
							this.datayAxis = res.data.data.map(item => {
								return (item.temp).toFixed(3)
							})
								this.unitsed = "℃"
						}
						if(this.value === 'acc'){
							this.datayAxis = res.data.data.map(item => {
								return (item.acc).toFixed(3)
							})
							this.unitsed = "g"
						}
						if(this.value === 'speed'){
							this.datayAxis = res.data.data.map(item => {
								return (item.speed).toFixed(3)
							})
							this.unitsed = "rpm"
						}
						if(this.value === 'dis'){
							this.datayAxis = res.data.data.map(item => {
								return (item.dis).toFixed(3)
							})
								this.unitsed = "um"
						}
						// console.log(this.datayAxis)
						this.dataxAxis = res.data.data.map(item => {
							return item.dtime
						})
						// this.unitsed =res.data.data.unit
						// console.log(this.unitsed,34)

						this.getServerData();
						
			
					
						
					}
				})
			},

			//ucharts-曲线图
			getServerData() {

				setTimeout(() => {
					let res = {
						categories: this.dataxAxis, //x轴
						series: [{ //y轴
							name: '单位:' + (this.unitsed) + "   " + "值",
							data: this.datayAxis
						}, ]
					};
					this.chartData = JSON.parse(JSON.stringify(res));
					// console.log(this.datayAxis,5425)
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
		color: #AFDDFF;
		width:100%;
		height: 215px;
	}

	.chartsed {
		width: 100%;

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
	.units{
		color: #999999;
		height: 12px;
		font-size: 14px;
		position: relative;
		z-index: 9;
		left: 74px;
		top:10px;
	}

	@import "index.scss";
</style>
